<?php

namespace App\Livewire;

use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\CourseItemSubmission;
use App\Models\CourseProgress;
use App\Models\CourseSessionItem;
use App\Models\CourseViewLog;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithFileUploads;


class StudentCourseShow extends Component
{
    use WithFileUploads;

    public Course $course;

    #[Url(as: 'week', history: true)]
    public ?int $weekId = null;

    #[Url(as: 'session', history: true)]
    public ?int $sessionId = null;

    #[Url(as: 'item', history: true)]
    public ?int $itemId = null;

    public string $answerText = '';

    public $submissionFile = null;

    // TID of the most recent access-log row for the item currently on screen.
    // Rendered into the watermark so leaked footage can be traced back to a view.
    public ?string $currentViewTid = null;

    public $selectedReadAloudSelectors = [];

    public function mount(Course $course): void
    {
        $user = Auth::user();
        abort_unless($user?->role === User::ROLE_STUDENT, 403);

        $this->course = $course;

        $enrollment = CourseEnrollment::query()
            ->where('course_id', $course->id)
            ->where('student_id', $user->id)
            ->first();

        abort_unless($enrollment, 403, 'You can open only enrolled courses.');

        // Seed progress rows for any items that don't have one yet.
        $itemIds = CourseSessionItem::whereHas(
            'session.week',
            fn ($q) => $q->where('course_id', $course->id)
        )->pluck('id');

        foreach ($itemIds as $itemId) {
            CourseProgress::firstOrCreate(
                [
                    'course_enrollment_id' => $enrollment->id,
                    'course_session_item_id' => $itemId,
                ],
                ['completed_at' => null]
            );
        }

        // Resolve an initial week/session/item if the URL didn't specify one.
        if (! $this->weekId) {
            $next = $this->resolveNextPending($enrollment->id, $course);

            $firstWeek = $course->weeks->first();
            $this->weekId = $next['weekId'] ?? $firstWeek?->id;

            $week = $course->weeks->firstWhere('id', $this->weekId);
            $firstSession = $week?->sessions->first();
            $this->sessionId = $next['sessionId'] ?? $firstSession?->id;

            $session = $week?->sessions->firstWhere('id', $this->sessionId);
            $this->itemId = $next['itemId'] ?? $session?->items->first()?->id;
        }

        $this->refreshReadAloudSelectors();
        $this->logAccess('viewed', ['source' => 'mount']);
    }

    /* -----------------------------------------------------------------
     |  Computed state — mirrors the logic that used to live in the
     |  controller / blade @php block, but stays reactive.
     |----------------------------------------------------------------- */

    #[Computed]
    public function enrollment(): CourseEnrollment
    {
        return CourseEnrollment::with(['course.weeks.sessions.items', 'trainer', 'progressItems'])
            ->where('course_id', $this->course->id)
            ->where('student_id', Auth::id())
            ->firstOrFail();
    }

    #[Computed]
    public function allSessions(): Collection
    {
        return $this->course->weeks->flatMap->sessions;
    }

    #[Computed]
    public function completedItemIds(): array
    {
        return $this->enrollment->progressItems
            ->whereNotNull('completed_at')
            ->pluck('course_session_item_id')
            ->map(fn ($id) => (int) $id)
            ->values()
            ->all();
    }

    #[Computed]
    public function completedMap(): array
    {
        return array_flip($this->completedItemIds);
    }

    #[Computed]
    public function totalItems(): int
    {
        return $this->allSessions->flatMap->items->count();
    }

    #[Computed]
    public function completedItems(): int
    {
        return count($this->completedItemIds);
    }

    #[Computed]
    public function progressPercent(): int
    {
        return $this->totalItems > 0
            ? (int) round(($this->completedItems / $this->totalItems) * 100)
            : 0;
    }

    #[Computed]
    public function pendingItems(): int
    {
        return max(0, $this->totalItems - $this->completedItems);
    }

    #[Computed]
    public function nextPendingItemId(): ?int
    {
        return $this->allSessions
            ->flatMap->items
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->first(fn (int $id) => ! in_array($id, $this->completedItemIds, true));
    }

    #[Computed]
    public function completedWeeksCount(): int
    {
        return $this->course->weeks->filter(function ($week) {
            $items = $week->sessions->flatMap->items;

            return $items->isNotEmpty()
                && $items->every(fn ($item) => isset($this->completedMap[(int) $item->id]));
        })->count();
    }

    #[Computed]
    public function completedSessionsCount(): int
    {
        return $this->allSessions->filter(function ($session) {
            $items = $session->items;

            return $items->isNotEmpty()
                && $items->every(fn ($item) => isset($this->completedMap[(int) $item->id]));
        })->count();
    }

    #[Computed]
    public function selectedWeek()
    {
        return $this->course->weeks->firstWhere('id', $this->weekId)
            ?? $this->course->weeks->first();
    }

    #[Computed]
    public function selectedSession()
    {
        return $this->selectedWeek?->sessions->firstWhere('id', $this->sessionId)
            ?? $this->selectedWeek?->sessions->first();
    }

    #[Computed]
    public function selectedSessionItems(): Collection
    {
        return $this->selectedSession?->items ?? collect();
    }

    #[Computed]
    public function selectedItem()
    {
        return $this->selectedSessionItems->firstWhere('id', $this->itemId)
            ?? $this->selectedSessionItems->first();
    }

    #[Computed]
    public function selectedIsQuiz(): bool
    {
        return $this->selectedItem?->item_type === CourseSessionItem::TYPE_QUIZ;
    }

    #[Computed]
    public function selectedIsTask(): bool
    {
        return $this->selectedItem?->item_type === CourseSessionItem::TYPE_TASK;
    }

    #[Computed]
    public function selectedIsCompleted(): bool
    {
        return $this->selectedItem ? isset($this->completedMap[(int) $this->selectedItem->id]) : false;
    }

    #[Computed]
    public function selectedIsNext(): bool
    {
        return $this->selectedItem
            && $this->nextPendingItemId !== null
            && (int) $this->selectedItem->id === (int) $this->nextPendingItemId;
    }

    #[Computed]
    public function selectedFormat(): string
    {
        return Str::lower((string) ($this->selectedItem?->cloudinary_format ?? ''));
    }

    #[Computed]
    public function selectedHasPrivateAsset(): bool
    {
        return $this->selectedItem?->hasPrivateCloudinaryAsset() ?? false;
    }

    #[Computed]
    public function selectedCanPreviewVideo(): bool
    {
        return $this->selectedHasPrivateAsset && $this->selectedItem?->cloudinary_resource_type === 'video';
    }

    #[Computed]
    public function selectedCanPreviewPdf(): bool
    {
        return $this->selectedHasPrivateAsset && $this->selectedFormat === 'pdf';
    }

    /**
     * Word documents render through the in-browser docx-preview/mammoth pipeline
     * (see the docx script block at the bottom of the view) so the reader can be
     * fed into the read-aloud widget.
     */
    #[Computed]
    public function selectedCanPreviewDocx(): bool
    {
        return $this->selectedHasPrivateAsset && in_array($this->selectedFormat, ['doc', 'docx'], true);
    }

    /**
     * PowerPoint files render through the JSZip-based slide viewer so slide text
     * can also be fed into the read-aloud widget.
     */
    #[Computed]
    public function selectedCanPreviewPptx(): bool
    {
        return $this->selectedHasPrivateAsset && in_array($this->selectedFormat, ['ppt', 'pptx'], true);
    }

    /**
     * Excel formats fall back to the secure server-rendered iframe viewer —
     * doc/docx/ppt/pptx are handled by the richer viewers above.
     */
    #[Computed]
    public function selectedCanPreviewOffice(): bool
    {
        return $this->selectedHasPrivateAsset && in_array($this->selectedFormat, ['xls', 'xlsx'], true);
    }

    /**
     * Text fed straight to the inline Alpine read-aloud widget — no DOM scraping,
     * so it's reliable regardless of what's rendered in the media area.
     */
    #[Computed]
    public function selectedReadAloudText(): string
    {
        $parts = array_filter([
            $this->selectedWeek ? 'Week '.$this->selectedWeek->week_number.': '.$this->selectedWeek->title : null,
            $this->selectedSession ? 'Session '.$this->selectedSession->session_number.': '.$this->selectedSession->title : null,
            $this->selectedItem?->title,
            $this->selectedItem?->content,
        ]);

        return trim(implode('. ', $parts));
    }

    #[Computed]
    public function selectedCanUseReadAloud(): bool
    {
        return $this->selectedReadAloudText !== '';
    }

    #[Computed]
    public function selectedViewerUrl(): ?string
    {
        return $this->selectedItem ? route('course-session-items.media.view', $this->selectedItem) : null;
    }

    #[Computed]
    public function selectedEmbeddedViewerUrl(): ?string
    {
        return $this->selectedItem
            ? route('course-session-items.media.view', ['item' => $this->selectedItem, 'embed' => 1])
            : null;
    }

    #[Computed]
    public function selectedStreamUrl(): ?string
    {
        return $this->selectedItem ? route('course-session-items.media.stream', $this->selectedItem) : null;
    }

    #[Computed]
    public function selectedDownloadUrl(): ?string
    {
        return $this->selectedItem && ($this->selectedIsTask || $this->selectedIsQuiz)
            ? route('course-session-items.media.download', $this->selectedItem)
            : null;
    }

    #[Computed]
    public function selectedSubmission()
    {
        if (! $this->selectedItem) {
            return null;
        }

        return CourseItemSubmission::query()
            ->where('course_enrollment_id', $this->enrollment->id)
            ->where('course_session_item_id', $this->selectedItem->id)
            ->latest('submitted_at')
            ->first();
    }

    #[Computed]
    public function selectedSessionCompleted(): int
    {
        return $this->selectedSessionItems
            ->filter(fn ($item) => isset($this->completedMap[(int) $item->id]))
            ->count();
    }

    #[Computed]
    public function selectedSessionRemaining(): int
    {
        return max(0, $this->selectedSessionItems->count() - $this->selectedSessionCompleted);
    }

    #[Computed]
    public function selectedSessionProgress(): int
    {
        return $this->selectedSessionItems->count() > 0
            ? (int) round(($this->selectedSessionCompleted / $this->selectedSessionItems->count()) * 100)
            : 0;
    }

    #[Computed]
    public function selectedItemPosition(): ?int
    {
        if (! $this->selectedItem) {
            return null;
        }

        $offset = $this->selectedSessionItems->search(
            fn ($item) => (int) $item->id === (int) $this->selectedItem->id
        );

        return $offset === false ? null : $offset + 1;
    }

    #[Computed]
    public function selectedPreviousItem()
    {
        $pos = $this->selectedItemPosition;

        return $pos && $pos > 1 ? $this->selectedSessionItems->get($pos - 2) : null;
    }

    #[Computed]
    public function selectedNextItem()
    {
        $pos = $this->selectedItemPosition;

        return $pos && $pos < $this->selectedSessionItems->count()
            ? $this->selectedSessionItems->get($pos)
            : null;
    }

    #[Computed]
    public function selectedTypeLabel(): ?string
    {
        return $this->selectedItem ? ucwords(str_replace('_', ' ', $this->selectedItem->item_type)) : null;
    }

    #[Computed]
    public function selectedResourceLabel(): string
    {
        if ($this->selectedFormat !== '') {
            return strtoupper($this->selectedFormat);
        }

        if ($this->selectedItem?->cloudinary_resource_type) {
            return strtoupper((string) $this->selectedItem->cloudinary_resource_type);
        }

        return $this->selectedItem?->resource_url ? 'LINK' : 'TEXT';
    }

    #[Computed]
    public function selectedPreviewLabel(): string
    {
        return match (true) {
            $this->selectedCanPreviewVideo => 'Secure Video',
            $this->selectedCanPreviewPdf => 'Inline PDF',
            $this->selectedCanPreviewDocx => 'Secure Document Viewer',
            $this->selectedCanPreviewPptx => 'Secure Slide Viewer',
            $this->selectedCanPreviewOffice => 'Secure Office Viewer',
            (bool) $this->selectedItem?->resource_url => 'External Resource',
            $this->selectedIsTask => 'Task Workspace',
            $this->selectedIsQuiz => $this->selectedItem?->is_live ? 'Live Quiz' : 'Quiz Waiting Room',
            default => 'Lesson Notes',
        };
    }

    #[Computed]
    public function selectedAccessLabel(): string
    {
        return match (true) {
            $this->selectedHasPrivateAsset => 'View-only in project',
            (bool) $this->selectedItem?->resource_url => 'Opens in new tab',
            $this->selectedIsTask => 'Upload required',
            $this->selectedIsQuiz => $this->selectedItem?->is_live ? 'Text answer required' : 'Waiting for trainer',
            default => 'Read and continue',
        };
    }

    #[Computed]
    public function watermarkLabel(): string
    {
        $user = Auth::user();

        return trim(($user?->name ?? 'Student').' · ID '.($user?->id ?? '—'));
    }

    #[Computed]
    public function continueUrl(): ?string
    {
        return $this->nextPendingItemId ? '#learning-workspace' : null;
    }

    /* -----------------------------------------------------------------
     |  Navigation actions
     |----------------------------------------------------------------- */

    public function selectWeek(int $weekId): void
    {
        $week = $this->course->weeks->firstWhere('id', $weekId);

        $this->weekId = $weekId;
        $this->sessionId = $week?->sessions->first()?->id;
        $this->itemId = $week?->sessions->first()?->items->first()?->id;
        $this->resetErrorBag();
        unset($this->selectedSubmission);

        $this->refreshReadAloudSelectors();
        $this->logAccess('viewed');
    }

    public function selectSession(int $sessionId): void
    {
        $session = $this->selectedWeek?->sessions->firstWhere('id', $sessionId);

        $this->sessionId = $sessionId;
        $this->itemId = $session?->items->first()?->id;
        $this->resetErrorBag();
        unset($this->selectedSubmission);

        $this->refreshReadAloudSelectors();
        $this->logAccess('viewed');
    }

    public function selectItem(int $itemId): void
    {
        $this->itemId = $itemId;
        $this->answerText = '';
        $this->submissionFile = null;
        $this->resetErrorBag();
        unset($this->selectedSubmission);

        $this->refreshReadAloudSelectors();
        $this->logAccess('viewed');
    }

    public function goToNextItem(): void
    {
        if ($next = $this->selectedNextItem) {
            $this->selectItem($next->id);
        }
    }

    public function goToPreviousItem(): void
    {
        if ($prev = $this->selectedPreviousItem) {
            $this->selectItem($prev->id);
        }
    }

    public function continueNextPending(): void
    {
        $next = $this->resolveNextPending($this->enrollment->id, $this->course);

        if ($next) {
            $this->weekId = $next['weekId'];
            $this->sessionId = $next['sessionId'];
            $this->itemId = $next['itemId'];

            $this->refreshReadAloudSelectors();
            $this->logAccess('viewed');
        }
    }

    /* -----------------------------------------------------------------
     |  Submissions & progress
     |----------------------------------------------------------------- */

    public function submitTask(): void
    {
        try {
            $this->validate([
                'submissionFile' => ['required', 'file'],
            ]);
        } catch (ValidationException $e) {
            $this->dispatch('validation-failed', message: $e->validator->errors()->first());
            throw $e;
        }

        CourseItemSubmission::create([
            'course_enrollment_id' => $this->enrollment->id,
            'course_session_item_id' => $this->selectedItem->id,
            'file_name' => $this->submissionFile->getClientOriginalName(),
            'file_path' => $this->submissionFile->store('submissions'),
            'submitted_at' => now(),
        ]);

        $this->markItemComplete($this->selectedItem->id);
        $this->submissionFile = null;
        unset($this->selectedSubmission, $this->completedItemIds, $this->completedMap);

        $this->dispatch('submission-saved', message: 'Your task file was submitted successfully.');
    }

    public function submitQuiz(): void
    {
        try {
            $this->validate([
                'answerText' => ['required', 'string', 'min:1'],
            ]);
        } catch (ValidationException $e) {
            $this->dispatch('validation-failed', message: $e->validator->errors()->first());
            throw $e;
        }

        if (! $this->selectedItem?->is_live) {
            $this->addError('answerText', 'This quiz is not live yet.');
            $this->dispatch('validation-failed', message: 'This quiz is not live yet.');

            return;
        }

        CourseItemSubmission::create([
            'course_enrollment_id' => $this->enrollment->id,
            'course_session_item_id' => $this->selectedItem->id,
            'answer_text' => $this->answerText,
            'submitted_at' => now(),
        ]);

        $this->markItemComplete($this->selectedItem->id);
        $this->answerText = '';
        unset($this->selectedSubmission, $this->completedItemIds, $this->completedMap);

        $this->dispatch('submission-saved', message: 'Your quiz answer was submitted successfully.');
    }

    /**
     * Called from the video's `ended` event (bound inline via Alpine's $wire,
     * no external script). Marks progress the same way a task/quiz submission does.
     */
    public function markVideoWatched(): void
    {
        if (! $this->selectedItem || ! $this->selectedCanPreviewVideo || $this->selectedIsCompleted) {
            return;
        }

        $this->markItemComplete($this->selectedItem->id);
        $this->logAccess('video_completed');

        unset($this->selectedSubmission, $this->completedItemIds, $this->completedMap);

        $this->dispatch('submission-saved', message: 'Video marked as watched. Progress saved.');
    }

    /**
     * Fired from the client-side video-protection script when it notices a
     * likely capture attempt (save/print shortcut, tab hidden mid-playback,
     * devtools opened, PrintScreen, etc). We don't — and can't — actually
     * block screen/video recording from the browser; this only logs the
     * signal against the current view so a leak can be traced back to it.
     */
    public function logSuspiciousActivity(string $type): void
    {
        if (! $this->selectedItem) {
            return;
        }

        $this->logAccess('suspicious_'.Str::limit(preg_replace('/[^a-z0-9_]+/i', '_', $type), 40, ''), [
            'source' => 'client_protection_script',
        ]);
    }

    protected function markItemComplete(int $itemId): void
    {
        CourseProgress::where('course_enrollment_id', $this->enrollment->id)
            ->where('course_session_item_id', $itemId)
            ->update(['completed_at' => now()]);

        unset($this->enrollment);
    }

    /* -----------------------------------------------------------------
     |  Access logging (anti-leak trail)
     |----------------------------------------------------------------- */

    protected function logAccess(string $event, array $meta = []): void
    {
        if (! $this->selectedItem) {
            $this->currentViewTid = null;

            return;
        }

        $log = CourseViewLog::create([
            'user_id' => Auth::id(),
            'course_id' => $this->course->id,
            'course_session_item_id' => $this->selectedItem->id,
            'event' => $event,
            'ip_address' => request()->ip(),
            'user_agent' => substr((string) request()->userAgent(), 0, 255),
            'meta' => $meta ?: null,
        ]);

        $this->currentViewTid = $log->tid;
    }

    /* -----------------------------------------------------------------
     |  Helpers
     |----------------------------------------------------------------- */

    /**
     * Tells the Alpine read-aloud widget which on-screen elements to pull
     * text from for the item currently selected. Kept in sync every time
     * the selected item changes so it never reads stale content.
     */
    protected function refreshReadAloudSelectors(): void
    {
        $this->selectedReadAloudSelectors = array_values(array_filter([
            '#lesson-read-aloud-notes',
            $this->selectedCanPreviewDocx ? '#lesson-read-aloud-docx' : null,
            $this->selectedCanPreviewPptx ? '#lesson-read-aloud-pptx' : null,
        ]));
    }

    protected function resolveNextPending(int $enrollmentId, Course $course): ?array
    {
        $completedIds = CourseProgress::where('course_enrollment_id', $enrollmentId)
            ->whereNotNull('completed_at')
            ->pluck('course_session_item_id')
            ->map(fn ($id) => (int) $id)
            ->all();

        foreach ($course->weeks as $week) {
            foreach ($week->sessions as $session) {
                foreach ($session->items as $item) {
                    if (! in_array((int) $item->id, $completedIds, true)) {
                        return [
                            'weekId' => (int) $week->id,
                            'sessionId' => (int) $session->id,
                            'itemId' => (int) $item->id,
                        ];
                    }
                }
            }
        }

        return null;
    }

    public function render()
    {
        return view('livewire.student-course-show');
    }
}