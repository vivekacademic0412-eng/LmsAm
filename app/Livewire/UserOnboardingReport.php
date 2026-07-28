<?php
// FILE: app/Livewire/UserOnboardingReport.php

namespace App\Livewire;

use App\Models\User;
use App\Models\TrafficSource;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Payment;
use Illuminate\Support\Str;
class UserOnboardingReport extends Component
{
    use WithPagination;

    // ── Filters (synced to URL) ────────────────────────────────
    #[Url(as: 'q')]
    public string $search = '';

    #[Url(as: 'role')]
    public string $filterRole = '';

    #[Url(as: 'onboarding')]
    public string $filterOnboarding = '';

    #[Url(as: 'policy')]
    public string $filterPolicy = ''; // agreed | pending

    #[Url(as: 'source')]
    public string $filterSource = '';

    // ── Detail drawer ───────────────────────────────────────────
    public ?int $viewingId  = null;
    public bool $showDetail = false;
  
    public $openActionFor = null; // user_id whose action dropdown is open
    public function mount(): void
    {
        abort_unless(
            in_array(auth()->user()?->role, [User::ROLE_SUPERADMIN, User::ROLE_ADMIN,User::ROLE_MANAGER_HR], true),
            403
        );
    }

    // ── COMPUTED: paginated, filtered users (never Super Admin / Admin) ──
    #[Computed]
    public function users()
    {
        return User::query()
            ->whereNotIn('role', [User::ROLE_SUPERADMIN, User::ROLE_ADMIN])
            ->with([
                'studentProfile',
                'academicBackground',
                'programEnrollments',
                'onboardingDocuments',
                'policyAcceptances' => fn ($q) => $q->latest('accepted_at'),
                'trafficSources'    => fn ($q) => $q->latest('created_at'),
            ])
            ->when($this->filterRole, fn ($q) => $q->where('role', $this->filterRole))
            ->when($this->filterOnboarding, fn ($q) => $q->where('onboarding_status', $this->filterOnboarding))
            ->when($this->filterPolicy === 'agreed', fn ($q) =>
                $q->whereHas('policyAcceptances', fn ($p) => $p->where('terms_agreed', true))
            )
            ->when($this->filterPolicy === 'pending', fn ($q) =>
                $q->whereDoesntHave('policyAcceptances', fn ($p) => $p->where('terms_agreed', true))
            )
            ->when($this->filterSource, fn ($q) =>
                $q->whereHas('trafficSources', fn ($t) =>
                    $t->where('utm_source', $this->filterSource)->orWhere('source', $this->filterSource)
                )
            )
            ->when($this->search, fn ($q) =>
                $q->where(fn ($s) =>
                    $s->where('name', 'like', "%{$this->search}%")
                      ->orWhere('email', 'like', "%{$this->search}%")
                )
            )
            ->orderByDesc('id')
            ->paginate(10);
    }

    #[Computed]
    public function roleOptions(): array
    {
        return collect(User::roleOptions())
            ->except([User::ROLE_SUPERADMIN, User::ROLE_ADMIN])
            ->all();
    }

    #[Computed]
    public function sourceOptions()
    {
        return TrafficSource::query()
            ->whereNotNull('utm_source')
            ->distinct()
            ->orderBy('utm_source')
            ->pluck('utm_source');
    }

    // ── Reset pagination on filter change ────────────────────────
    public function updatedSearch(): void { $this->resetPage(); }
    public function updatedFilterRole(): void { $this->resetPage(); }
    public function updatedFilterOnboarding(): void { $this->resetPage(); }
    public function updatedFilterPolicy(): void { $this->resetPage(); }
    public function updatedFilterSource(): void { $this->resetPage(); }

    public function clearFilters(): void
    {
        $this->reset(['search', 'filterRole', 'filterOnboarding', 'filterPolicy', 'filterSource']);
        $this->resetPage();
    }

    // ── Detail drawer ────────────────────────────────────────────
    public function viewUser(int $id): void
    {
        $this->viewingId  = $id;
        $this->showDetail = true;
    }

    public function closeDetail(): void
    {
        $this->showDetail = false;
        $this->viewingId  = null;
    }

    #[Computed]
    public function viewingUser(): ?User
    {
        if (!$this->viewingId) {
            return null;
        }

        return User::with([
            'studentProfile',
            'academicBackground',
            'programEnrollments',
            'onboardingDocuments',
            'policyAcceptances' => fn ($q) => $q->latest('accepted_at'),
            'policyAcceptances.policy',
            'trafficSources'    => fn ($q) => $q->latest('created_at'),
             'payments' => fn ($q) => $q->latest()->limit(1), 'payments.category', 'payments.course',
        ])->find($this->viewingId);
    }
   
 
    public function toggleAction($userId): void
    {
        // dd($userId);
        $this->openActionFor = $this->openActionFor === $userId ? null : $userId;
    }
 
    public function copyLinkFeedback(): void
    {
        session()->flash('success', 'Payment link copied — share it with the student.');
    }
 
    public function regenerateLink($paymentId): void
    {
        $payment = Payment::findOrFail($paymentId);
 
        if ($payment->status === Payment::STATUS_SUCCESS) {
            session()->flash('error', 'This link is already paid — cannot regenerate.');
            return;
        }
 
        $payment->update(['token' => Str::random(40)]);
        session()->flash('success', 'New payment link generated.');
    }
 

    public function render()
    {
        return view('livewire.user-onboarding-report');
    }
}