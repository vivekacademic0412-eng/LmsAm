<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notification as BaseNotification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class BroadcastNotificationController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorizeManager($request);

        return view('broadcast-premium', [
            'audienceOptions' => $this->audienceOptions(),
            'courses' => Course::orderBy('title')->get(['id', 'title']),
            'notificationsReady' => Schema::hasTable('notifications'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorizeManager($request);

        if (! Schema::hasTable('notifications')) {
            return back()->withErrors([
                'notifications' => 'The notifications table is missing. Run the pending notifications migration before sending broadcasts.',
            ])->withInput();
        }

        $audienceOptions = $this->audienceOptions();

        $data = $request->validate([
            'audience' => ['required', Rule::in(array_keys($audienceOptions))],
            'course_id' => [
                Rule::requiredIf(fn () => $request->input('audience') === 'course_students'),
                'nullable',
                'integer',
                'exists:courses,id',
            ],
            'title' => ['required', 'string', 'max:120'],
            'message' => ['required', 'string', 'max:1200'],
        ]);

        $recipients = $this->resolveRecipients(
            audience: (string) $data['audience'],
            courseId: isset($data['course_id']) ? (int) $data['course_id'] : null
        );

        if ($recipients->isEmpty()) {
            return back()->withErrors([
                'audience' => 'No matching recipients were found for the selected audience.',
            ])->withInput();
        }

        $sender = $request->user();

        Notification::send($recipients, new class(
            (string) $data['title'],
            (string) $data['message'],
            $sender?->name,
            (string) $data['audience'],
            isset($data['course_id']) ? (int) $data['course_id'] : null
        ) extends BaseNotification {
            use Queueable;

            public function __construct(
                private string $title,
                private string $message,
                private ?string $senderName,
                private string $audience,
                private ?int $courseId
            ) {
            }

            public function via($notifiable): array
            {
                return ['database'];
            }

            public function toArray($notifiable): array
            {
                return [
                    'title' => $this->title,
                    'message' => $this->message,
                    'sender_name' => $this->senderName,
                    'audience' => $this->audience,
                    'course_id' => $this->courseId,
                    'broadcasted_at' => now()->toDateTimeString(),
                ];
            }
        });

        return back()->with('success', 'Broadcast notification sent to '.$recipients->count().' user(s).');
    }

    /**
     * @return array<string, string>
     */
    private function audienceOptions(): array
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

    /**
     * @return Collection<int, User>
     */
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

    private function authorizeManager(Request $request): void
    {
        abort_unless(
            in_array($request->user()?->role, [User::ROLE_SUPERADMIN, User::ROLE_ADMIN], true),
            403
        );
    }
}
