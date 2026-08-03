<?php

namespace App\Livewire;

use App\Mail\BroadcastMail;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\User;
use App\Notifications\BroadcastAlert;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithFileUploads;

class BroadcastNotifications extends Component
{
    use WithFileUploads;

    public string $audience = 'all_active_users';

    public ?int $courseId = null;

    public string $title = '';

    public string $message = '';

    /** @var array<int, string> 'notification' and/or 'email' */
    public array $deliveryChannels = ['notification'];

    public string $manualEmailInput = '';

    /** @var array<int, string> */
    public array $manualEmails = [];

    /** @var array<int, \Livewire\Features\SupportFileUploads\TemporaryUploadedFile> */
    public array $attachments = [];

    public bool $sending = false;

    public function mount(): void
    {
        abort_unless(
            in_array(auth()->user()?->role, [User::ROLE_SUPERADMIN, User::ROLE_ADMIN], true),
            403
        );
    }

    #[Computed]
    public function audienceOptions(): array
    {
        return [
            'all_active_users' => 'All Active Users',
            'students' => 'Students',
            'trainers' => 'Trainers',
            'manager_hr' => 'Manager / HR',
            'it' => 'IT',
            'admins' => 'Admins',
            'demo_users' => 'Demo Users',
            'course_students' => 'Students In One Course',
        ];
    }

    #[Computed]
    public function courses()
    {
        return Course::orderBy('title')->get(['id', 'title']);
    }

    #[Computed]
    public function notificationsReady(): bool
    {
        return Schema::hasTable('notifications');
    }

    public function updatedAudience(): void
    {
        if ($this->audience !== 'course_students') {
            $this->courseId = null;
        }
    }

    public function toggleChannel(string $channel): void
    {
        if (in_array($channel, $this->deliveryChannels, true)) {
            $this->deliveryChannels = array_values(array_diff($this->deliveryChannels, [$channel]));
        } else {
            $this->deliveryChannels[] = $channel;
        }

        // Attachments are only meaningful for email delivery.
        if (! in_array('email', $this->deliveryChannels, true)) {
            $this->attachments = [];
        }
    }

    /**
     * Adds one or several manually typed addresses (comma / space / newline separated) as chips.
     */
    public function addManualEmail(): void
    {
        $raw = trim($this->manualEmailInput);

        if ($raw === '') {
            return;
        }

        $candidates = array_filter(array_map('trim', preg_split('/[,\s]+/', $raw)));

        foreach ($candidates as $candidate) {
            if (filter_var($candidate, FILTER_VALIDATE_EMAIL) && ! in_array($candidate, $this->manualEmails, true)) {
                $this->manualEmails[] = $candidate;
            }
        }

        $this->manualEmailInput = '';
    }

    public function removeManualEmail(int $index): void
    {
        unset($this->manualEmails[$index]);
        $this->manualEmails = array_values($this->manualEmails);
    }

    public function removeAttachment(int $index): void
    {
        unset($this->attachments[$index]);
        $this->attachments = array_values($this->attachments);
    }

    protected function rules(): array
    {
        return [
            'audience' => 'required|in:' . implode(',', array_keys($this->audienceOptions)),
            'courseId' => $this->audience === 'course_students'
                ? 'required|integer|exists:courses,id'
                : 'nullable|integer|exists:courses,id',
            'title' => 'required|string|max:120',
            'message' => 'required|string|max:1200',
            'deliveryChannels' => 'required|array|min:1',
            'deliveryChannels.*' => 'in:notification,email',
            'manualEmails' => 'array',
            'manualEmails.*' => 'email',
            // Any file type is accepted (PDF, PPT, DOCX, images, zips, ...); adjust max sizes/count as needed.
            'attachments' => 'array|max:5',
            'attachments.*' => 'file|max:10240',
        ];
    }

    protected $messages = [
        'deliveryChannels.min' => 'Choose at least one delivery method (notification and/or email).',
        'attachments.max' => 'You can attach up to 5 files per broadcast.',
        'attachments.*.max' => 'Each attachment must be 10MB or smaller.',
    ];

    public function send(): void
    {
        $this->validate();

        if (! $this->notificationsReady) {
            $this->addError('notifications', 'The notifications table is missing. Run the pending notifications migration before sending broadcasts.');

            return;
        }

        $systemRecipients = $this->resolveRecipients($this->audience, $this->courseId);

        if ($systemRecipients->isEmpty() && empty($this->manualEmails)) {
            $this->addError('audience', 'No matching recipients were found for the selected audience or manual email list.');

            return;
        }

        $this->sending = true;

        $sender = auth()->user();
        $sentNotifications = 0;
        $sentEmails = 0;

        if (in_array('notification', $this->deliveryChannels, true) && $systemRecipients->isNotEmpty()) {
            Notification::send($systemRecipients, new BroadcastAlert(
                title: $this->title,
                message: $this->message,
                senderName: $sender?->name,
                audience: $this->audience,
                courseId: $this->courseId,
            ));

            $sentNotifications = $systemRecipients->count();
        }

        if (in_array('email', $this->deliveryChannels, true)) {
            $preparedAttachments = $this->prepareAttachments();

            $emailAddresses = $systemRecipients->pluck('email')
                ->merge($this->manualEmails)
                ->filter()
                ->unique()
                ->values();

            foreach ($emailAddresses as $email) {
                Mail::to($email)->send(new BroadcastMail(
                    title: $this->title,
                    body: $this->message,
                    senderName: $sender?->name,
                    mailAttachments: $preparedAttachments,
                ));
            }

            $sentEmails = $emailAddresses->count();

            $this->cleanupAttachments($preparedAttachments);
        }

        $this->sending = false;

        $this->reset(['title', 'message', 'manualEmails', 'manualEmailInput', 'attachments', 'courseId']);
        $this->deliveryChannels = ['notification'];

        session()->flash(
            'broadcast-success',
            "Broadcast sent — {$sentNotifications} dashboard notification(s), {$sentEmails} email(s)."
        );
    }

    /**
     * Persists uploaded files temporarily so they survive until the mail is actually sent,
     * then returns the metadata BroadcastMail needs to attach them.
     *
     * @return array<int, array{path:string, name:string, mime:?string, stored:string}>
     */
    private function prepareAttachments(): array
    {
        return collect($this->attachments)->map(function ($file) {
            $storedPath = $file->store('broadcast-attachments', 'local');

            return [
                'path' => Storage::disk('local')->path($storedPath),
                'name' => $file->getClientOriginalName(),
                'mime' => $file->getMimeType(),
                'stored' => $storedPath,
            ];
        })->all();
    }

    private function cleanupAttachments(array $preparedAttachments): void
    {
        foreach ($preparedAttachments as $file) {
            if (! empty($file['stored'])) {
                Storage::disk('local')->delete($file['stored']);
            }
        }
    }

    private function resolveRecipients(string $audience, ?int $courseId): Collection
    {
        return match ($audience) {
            'students' => User::where('role', User::ROLE_STUDENT)->where('is_active', true)->orderBy('name')->get(),
            'trainers' => User::where('role', User::ROLE_TRAINER)->where('is_active', true)->orderBy('name')->get(),
            'manager_hr' => User::where('role', User::ROLE_MANAGER_HR)->where('is_active', true)->orderBy('name')->get(),
            'it' => User::where('role', User::ROLE_IT)->where('is_active', true)->orderBy('name')->get(),
            'admins' => User::whereIn('role', [User::ROLE_SUPERADMIN, User::ROLE_ADMIN])->where('is_active', true)->orderBy('name')->get(),
            'demo_users' => User::where('role', User::ROLE_DEMO)->where('is_active', true)->orderBy('name')->get(),
            'course_students' => CourseEnrollment::with('student')
                ->where('course_id', $courseId)
                ->get()
                ->pluck('student')
                ->filter(fn ($student) => $student instanceof User && $student->is_active)
                ->unique('id')
                ->values(),
            default => User::where('is_active', true)->orderBy('name')->get(),
        };
    }

    public function render()
    {
        return view('livewire.broadcast-notifications');
    }
}