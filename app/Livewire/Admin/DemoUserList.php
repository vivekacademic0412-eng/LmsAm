<?php

namespace App\Livewire\Admin;

use App\Mail\DemoLoginCredentialsMail;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Course;
use App\Models\DemoAccessToken;
use App\Models\DemoTypeSelection;
use App\Models\DemoUser;
use App\Models\SubmittedDemos;
use App\Models\EducationLevel;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class DemoUserList extends Component
{
    use WithPagination;

    public $search = '';
    public $educationLevel = '';
    public $courseFilter = '';
    public $progressFilter = '';
    public $perPage = 10;

    public $sortField = 'created_at';
    public $sortDirection = 'desc';

    // Cooldown (in minutes) before "Send Mail" can be pressed again for the same user.
    protected int $resendCooldownMinutes = 2;

    protected $queryString = [
        'search',
        'educationLevel',
        'courseFilter',
        'progressFilter',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingEducationLevel()
    {
        $this->resetPage();
    }

    public function updatingCourseFilter()
    {
        $this->resetPage();
    }

    public function updatingProgressFilter()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    /**
     * Activate a demo user — but ONLY if their payment is actually confirmed
     * (or the demo type is free). This is the gate that used to be missing:
     * previously any row could be force-activated regardless of payment status.
     */
    public function activateUser($userId)
    {
        $user = User::with(['demo.submittedDemos', 'paymentType.payment'])->findOrFail($userId);

        $selection = $user->paymentType;

        if (!$selection) {
            $this->dispatch('error', message: 'No demo/payment selection found for this user.');
            return;
        }

        if ($this->demoIsCompleted($user)) {
            $this->dispatch('error', message: 'This demo is already completed. It cannot be reactivated.');
            return;
        }

        if ($selection->is_confirm == 2) {
            $this->dispatch('error', message: 'This user is already activated.');
            return;
        }

        $isFree = $selection->demo_type === 'free';
        $isPaymentConfirmed = $selection->status === 'completed';

        if (!$isFree && !$isPaymentConfirmed) {
            $this->dispatch(
                'error',
                message: 'Cannot activate: payment is not confirmed yet for this user.'
            );
            return;
        }

        $selection->update(['is_confirm' => 2]);

        $this->dispatch('success', message: 'User activated successfully. You can now send the demo link.');
    }

    /**
     * Send a one-time secure demo login link.
     * Rules enforced here to stop spam / re-use:
     *  - User must be activated (payment confirmed) first.
     *  - Demo must not already be completed.
     *  - Any previous unused tokens for this user are invalidated first,
     *    so only ONE valid link can exist for a user at any time.
     *  - A short cooldown prevents accidental double-sends from double clicks.
     */
    public function sendLoginMail($userId)
    {
        $user = User::with(['demo.submittedDemos', 'paymentType'])->findOrFail($userId);
        $selection = $user->paymentType;

        if (!$selection || $selection->is_confirm != 2) {
            $this->dispatch('error', message: 'Activate this user (confirm payment) before sending the demo link.');
            return;
        }

        if ($this->demoIsCompleted($user)) {
            $this->dispatch('error', message: 'This demo is already completed. No further link can be sent.');
            return;
        }

        if (
            $selection->mail_sent_at
            && now()->diffInMinutes($selection->mail_sent_at) < $this->resendCooldownMinutes
        ) {
            $this->dispatch(
                'error',
                message: "Please wait a moment before resending — a link was just sent."
            );
            return;
        }

        // Invalidate any previously issued, still-unused tokens for this user.
        // This guarantees a single valid link exists per user at a time.
        DemoAccessToken::where('user_id', $user->id)
            ->where('used', false)
            ->update(['used' => true, 'used_at' => now()]);

        $token = Str::uuid();

        DemoAccessToken::create([
            'user_id'    => $user->id,
            'token'      => $token,
            'expires_at' => now()->addDays(3),
            'used'       => false,
        ]);

        $url = route('demo.secure.login', $token);

        Mail::to($user->email)->send(new DemoLoginCredentialsMail($user, $url));

        $selection->update([
            'mail_sent_at'     => now(),
            'mail_sent_count'  => ($selection->mail_sent_count ?? 0) + 1,
        ]);

        $this->dispatch('success', message: 'Secure demo link sent successfully.');
    }

    /**
     * Consider a demo "completed" if the linked DemoUser record shows 100%
     * progress OR has at least one submitted demo. Adjust to match your
     * actual completion signal if different.
     */
    protected function demoIsCompleted(User $user): bool
    {
        $demo = $user->demo;

        if (!$demo) {
            return false;
        }

        if (($demo->progress_demo ?? 0) >= 100) {
            return true;
        }

        return $demo->relationLoaded('submittedDemos')
            ? $demo->submittedDemos->isNotEmpty()
            : $demo->submittedDemos()->exists();
    }

    public function render()
    {
        $query = User::where('role', 'student')->with([
            'demo.educationLevel',
            'demo.course',
            'demo.submittedDemos',
            'paymentType.payment',
            'paymentType.trafficSource',
        ]);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('full_name', 'like', '%' . $this->search . '%')
                    ->orWhere('email_phone', 'like', '%' . $this->search . '%')
                    ->orWhere('ip_address', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->educationLevel) {
            $query->where('education_level_id', $this->educationLevel);
        }

        if ($this->courseFilter) {
            $query->where('preferred_course_id', $this->courseFilter);
        }

        if ($this->progressFilter) {
            $query->whereHas('demo', function ($q) {
                $upper = (int) $this->progressFilter;
                $lower = $upper === 25 ? 0 : $upper - 25;

                if ($upper === 100) {
                    $q->where('progress_demo', 100);
                } else {
                    $q->whereBetween('progress_demo', [$lower, $upper]);
                }
            });
        }

        $query->orderBy($this->sortField, $this->sortDirection);

        return view('livewire.admin.demo-user-list', [
            'demoUsers' => $query->paginate($this->perPage),
            'educationLevels' => EducationLevel::orderBy('sort_order')->get(),
            'courses' => Course::orderBy('title')->get(),
            'totalUsers' => DemoUser::count(),
            'completedUsers' => DemoUser::where('progress_demo', 100)->count(),
            'totalDemos' => SubmittedDemos::count(),
            'totalCourses' => Course::count(),
        ]);
    }
}