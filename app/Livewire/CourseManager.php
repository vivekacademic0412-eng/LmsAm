<?php

namespace App\Livewire;

use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\CourseLevel;
use App\Models\CourseSession;
use App\Models\CourseSessionItem;
use App\Models\CourseSessionSetting;
use App\Models\CourseSettings;
use App\Models\CourseType;
use App\Models\CourseWeek;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;

class CourseManager extends Component
{
    use WithFileUploads;

    public Course $course;

    /* ── UI state ─────────────────────────────────────────────── */
    public ?string $activeModal = null; // course-info | course-settings | week | session | session-settings | session-item | video-preview
    public array $expandedWeeks = [];
    public array $expandedSessions = [];
    public string $previewUrl = '';
    public string $previewTitle = '';

    /* ── Context ids for nested edits ────────────────────────── */
    public ?int $editWeekId = null;
    public ?int $editSessionId = null;
    public ?int $editSessionSettingsId = null;
    public ?int $editItemId = null;
    public ?int $activeWeekIdForSession = null;
    public ?int $activeSessionIdForItem = null;

    /* ── Course info form ─────────────────────────────────────── */
    public $category_id = '';
    public $course_type_id = '';
    public $course_level_id = '';
    public $title = '';
    public $short_description = '';
    public $description = '';
    public $original_price = '';
    public $price = '';
    public $gst = '18';
    public $language = 'English';
    public $duration_hours = 0;
    public $thumbnail = '';

    /* ── Course settings form ─────────────────────────────────── */
    public $min_completion_percent = 80;
    public $weekly_unlock_mode = 'week1_gate_only';
    public $certificate_mode = 'per_level';
    public $show_seats_as_full = false;
    public $zero_day_countdown_enabled = false;
    public $countdown_days = 0;

    /* ── Week form ─────────────────────────────────────────────── */
    public $week_number = '';
    public $week_title = '';

    /* ── Session form ─────────────────────────────────────────── */
    public $session_number = '';
    public $session_title = '';

    /* ── Session settings form ───────────────────────────────── */
    public $is_required_for_certificate = false;
    public $meet_link = '';
    public $meet_datetime = '';
    public $is_visible = true;

    /* ── Session item form ───────────────────────────────────── */
    public $item_type = '';
    public $item_title = '';
    public $resource_type = '';
    public $item_content = '';
    public $resource_url = '';
    public $itemFile = null;          // temporary uploaded file (video or ppt)
    public string $uploadMode = 'url'; // 'url' | 'upload'
    public ?string $existingFilePath = null; // stored path of a previously uploaded file, for cleanup on replace
public $courseAssignments = [
    [
        'course_id' => '',
        'trainer_id' => '',
    ]
];
    public function mount(int $courseId): void
    {
        $this->loadCourse($courseId);
    }

    private function loadCourse(?int $courseId = null): void
    {
        $id = $courseId ?? $this->course->id;

        $this->course = Course::with([
            'category', 'courseType', 'courseLevel', 'settings',
            'weeks' => fn ($q) => $q->orderBy('week_number'),
            'weeks.sessions' => fn ($q) => $q->orderBy('session_number'),
            'weeks.sessions.settings',
            'weeks.sessions.items',
        ])->findOrFail($id);
    }

    public function itemTypeOptions(): array
    {
        return [
            CourseSessionItem::TYPE_INTRO      => 'Introduction',
            CourseSessionItem::TYPE_MAIN_VIDEO => 'Main Video',
            CourseSessionItem::TYPE_TASK       => 'Task',
            CourseSessionItem::TYPE_QUIZ       => 'Quiz',
        ];
    }

    protected function rules(): array
    {
        return match ($this->activeModal) {
            'course-info' => [
                'category_id'        => ['required', 'exists:course_categories,id'],
                'course_type_id'     => ['required', 'exists:course_types,id'],
                'course_level_id'    => ['required', 'exists:course_levels,id'],
                'title'               => ['required', 'string', 'max:255'],
                'short_description'   => ['required', 'string', 'max:500'],
                'description'         => ['required', 'string'],
                'original_price'      => ['required', 'numeric', 'min:0'],
                'price'               => ['required', 'numeric', 'min:0', 'lte:original_price'],
                'gst'                 => ['nullable', 'string', 'max:10'],
                'language'            => ['required', 'string', 'max:100'],
                'duration_hours'      => ['nullable', 'integer', 'min:0'],
                'thumbnail'           => ['nullable', 'string', 'max:255'],
            ],
            'course-settings' => [
                'min_completion_percent'     => ['required', 'integer', 'min:0', 'max:100'],
                'weekly_unlock_mode'         => ['required', 'in:sequential_all_weeks,week1_gate_only,free_no_lock'],
                'certificate_mode'           => ['required', 'in:both,per_level,end_of_course'],
                'show_seats_as_full'         => ['boolean'],
                'zero_day_countdown_enabled' => ['boolean'],
                'countdown_days'             => ['nullable', 'integer', 'min:0'],
            ],
            'week' => [
                'week_number' => [
                    'required', 'integer', 'min:1',
                    'unique:course_weeks,week_number,' . ($this->editWeekId ?? 'NULL') . ',id,course_id,' . $this->course->id,
                ],
                'week_title' => ['required', 'string', 'max:255'],
            ],
            'session' => [
                'session_number' => [
                    'required', 'integer', 'min:1',
                    'unique:course_sessions,session_number,' . ($this->editSessionId ?? 'NULL') . ',id,course_week_id,' . $this->activeWeekIdForSession,
                ],
                'session_title' => ['required', 'string', 'max:255'],
            ],
            'session-settings' => [
                'is_required_for_certificate' => ['boolean'],
                'meet_link'                    => ['nullable', 'url', 'max:500'],
                'meet_datetime'                 => ['nullable', 'date'],
                'is_visible'                    => ['boolean'],
            ],
            'session-item' => [
                'item_type'     => ['required', 'string'],
                'item_title'    => ['required', 'string', 'max:255'],
                'resource_type' => ['nullable', 'in:video,ppt,video_or_ppt'],
                'item_content'  => ['nullable', 'string'],
                'resource_url'  => ['nullable', 'url', 'max:1000'],
                'itemFile'      => $this->itemFileRules(),
            ],
            default => [],
        };
    }

    /**
     * File-upload rule set — mime types and max size depend on the
     * selected resource_type. Upload is always optional: an admin can
     * either upload a file or paste an external URL instead.
     */
    protected function itemFileRules(): array
    {
        return match ($this->resource_type) {
            'video'        => ['nullable', 'file', 'mimes:mp4,mov,avi,wmv,webm,mkv', 'max:102400'],   // 100MB
            'ppt'          => ['nullable', 'file', 'mimes:ppt,pptx,pdf', 'max:20480'],                // 20MB
            'video_or_ppt' => ['nullable', 'file', 'mimes:mp4,mov,avi,wmv,webm,mkv,ppt,pptx,pdf', 'max:102400'],
            default        => ['nullable', 'file', 'max:102400'],
        };
    }

    protected function messages(): array
    {
        return [
            'price.lte'          => 'Selling price cannot be greater than the original price.',
            'week_number.unique' => 'This week number already exists for this course.',
            'session_number.unique' => 'This session number already exists for this week.',
            'itemFile.mimes' => 'Please upload a valid file for the selected resource type.',
            'itemFile.max'   => 'File is too large. Videos: max 100MB, PPT/PDF: max 20MB.',
        ];
    }

    public function updated($property): void
    {
        if ($this->activeModal) {
            $this->validateOnly($property);
        }
    }

    /* ── Toggles ──────────────────────────────────────────────── */
    public function toggleWeek(int $weekId): void
    {
        if (in_array($weekId, $this->expandedWeeks)) {
            $this->expandedWeeks = array_diff($this->expandedWeeks, [$weekId]);
        } else {
            $this->expandedWeeks[] = $weekId;
        }
    }

    public function toggleSession(int $sessionId): void
    {
        if (in_array($sessionId, $this->expandedSessions)) {
            $this->expandedSessions = array_diff($this->expandedSessions, [$sessionId]);
        } else {
            $this->expandedSessions[] = $sessionId;
        }
    }

    /* ── Open modals ──────────────────────────────────────────── */
    public function openCourseInfoModal(): void
    {
        $this->category_id      = $this->course->category_id;
        $this->course_type_id   = $this->course->course_type_id;
        $this->course_level_id  = $this->course->course_level_id;
        $this->title             = $this->course->title;
        $this->short_description = $this->course->short_description;
        $this->description       = $this->course->description;
        $this->original_price    = $this->course->original_price;
        $this->price             = $this->course->price;
        $this->gst                = $this->course->gst;
        $this->language           = $this->course->language;
        $this->duration_hours     = $this->course->duration_hours;
        $this->thumbnail          = $this->course->thumbnail;

        $this->resetErrorBag();
        $this->activeModal = 'course-info';
    }

    public function openCourseSettingsModal(): void
    {
        $settings = $this->course->settings;

        $this->min_completion_percent     = $settings->min_completion_percent ?? 80;
        $this->weekly_unlock_mode         = $settings->weekly_unlock_mode ?? 'week1_gate_only';
        $this->certificate_mode           = $settings->certificate_mode ?? 'per_level';
        $this->show_seats_as_full         = (bool) ($settings->show_seats_as_full ?? false);
        $this->zero_day_countdown_enabled = (bool) ($settings->zero_day_countdown_enabled ?? false);
        $this->countdown_days             = $settings->countdown_days ?? 0;

        $this->resetErrorBag();
        $this->activeModal = 'course-settings';
    }

    public function openWeekModal(?int $weekId = null): void
    {
        $this->editWeekId = $weekId;

        if ($weekId) {
            $week = CourseWeek::findOrFail($weekId);
            $this->week_number = $week->week_number;
            $this->week_title  = $week->title;
        } else {
            $this->week_number = ($this->course->weeks->max('week_number') ?? 0) + 1;
            $this->week_title  = '';
        }

        $this->resetErrorBag();
        $this->activeModal = 'week';
    }

    public function openSessionModal(int $weekId, ?int $sessionId = null): void
    {
        $this->activeWeekIdForSession = $weekId;
        $this->editSessionId = $sessionId;

        if ($sessionId) {
            $session = CourseSession::findOrFail($sessionId);
            $this->session_number = $session->session_number;
            $this->session_title  = $session->title;
        } else {
            $week = $this->course->weeks->firstWhere('id', $weekId);
            $this->session_number = ($week?->sessions->max('session_number') ?? 0) + 1;
            $this->session_title  = '';
        }

        $this->resetErrorBag();
        $this->activeModal = 'session';
    }

    public function openSessionSettingsModal(int $sessionId): void
    {
        $session  = CourseSession::with('settings')->findOrFail($sessionId);
        $settings = $session->settings;

        $this->editSessionSettingsId          = $sessionId;
        $this->is_required_for_certificate = (bool) ($settings->is_required_for_certificate ?? false);
        $this->meet_link                    = $settings->meet_link ?? '';
        $this->meet_datetime                = optional($settings?->meet_datetime)->format('Y-m-d\TH:i');
        $this->is_visible                   = (bool) ($settings->is_visible ?? true);

        $this->resetErrorBag();
        $this->activeModal = 'session-settings';
    }

    public function openItemModal(int $sessionId, ?int $itemId = null): void
    {
        $this->activeSessionIdForItem = $sessionId;
        $this->editItemId = $itemId;

        $this->itemFile         = null;
        $this->existingFilePath = null;
        $this->uploadMode       = 'url';

        if ($itemId) {
            $item = CourseSessionItem::findOrFail($itemId);
            $this->item_type     = $item->item_type;
            $this->item_title    = $item->title;
            $this->resource_type = $item->resource_type ?? '';
            $this->item_content  = $item->content;
            $this->resource_url  = $item->resource_url;

            // If the existing resource was previously uploaded through this
            // component (stored on the public disk), default back to the
            // "Upload" tab and remember the path so it can be replaced/cleaned up.
            if ($item->resource_url && str_contains($item->resource_url, '/storage/session-items/')) {
                $this->uploadMode       = 'upload';
                $this->existingFilePath = Str::after($item->resource_url, '/storage/');
            }
        } else {
            $this->item_type     = '';
            $this->item_title    = '';
            $this->resource_type = '';
            $this->item_content  = '';
            $this->resource_url  = '';
        }

        $this->resetErrorBag();
        $this->activeModal = 'session-item';
    }

    public function updatedResourceType(): void
    {
        $this->itemFile = null;
        $this->resetErrorBag(['itemFile', 'resource_url']);
    }

    /** Switch between "paste a URL" and "upload a file" for the session item resource. */
    public function setUploadMode(string $mode): void
    {
        $this->uploadMode = $mode;
        $this->resetErrorBag(['itemFile', 'resource_url']);
    }

    public function removeSelectedFile(): void
    {
        $this->itemFile = null;
    }

    public function openVideoPreview(string $url, string $title = 'Preview'): void
    {
        $this->previewUrl   = $url;
        $this->previewTitle = $title;
        $this->activeModal  = 'video-preview';
    }

    public function closeModal(): void
    {
        $this->activeModal = null;
        $this->resetErrorBag();
    }

    /* ── Save handlers ────────────────────────────────────────── */
    public function saveCourseInfo(): void
    {
        $validated = $this->validate();
        $validated['slug'] = Str::slug($validated['title']);

        $this->course->update($validated);
        $this->loadCourse();

        $this->activeModal = null;
        $this->dispatch('toast', type: 'success', message: 'Course details updated.');
    }

    public function saveCourseSettings(): void
    {
        $validated = $this->validate();

        CourseSettings::updateOrCreate(['course_id' => $this->course->id], $validated);
        $this->loadCourse();

        $this->activeModal = null;
        $this->dispatch('toast', type: 'success', message: 'Course settings updated.');
    }

    public function saveWeek(): void
    {
        $validated = $this->validate();

        CourseWeek::updateOrCreate(
            ['id' => $this->editWeekId, 'course_id' => $this->course->id],
            ['week_number' => $validated['week_number'], 'title' => $validated['week_title'], 'course_id' => $this->course->id]
        );

        $this->loadCourse();
        $this->activeModal = null;
        $this->dispatch('toast', type: 'success', message: 'Week saved.');
    }

    public function saveSession(): void
    {
        $validated = $this->validate();

        CourseSession::updateOrCreate(
            ['id' => $this->editSessionId, 'course_week_id' => $this->activeWeekIdForSession],
            [
                'session_number'  => $validated['session_number'],
                'title'           => $validated['session_title'],
                'course_week_id'  => $this->activeWeekIdForSession,
            ]
        );

        $this->loadCourse();
        $this->activeModal = null;
        $this->dispatch('toast', type: 'success', message: 'Session saved.');
    }

    public function saveSessionSettings(): void
    {
        $validated = $this->validate();

        CourseSessionSetting::updateOrCreate(
            ['course_session_id' => $this->editSessionSettingsId],
            $validated
        );

        $this->loadCourse();
        $this->activeModal = null;
        $this->dispatch('toast', type: 'success', message: 'Session settings updated.');
    }

    public function saveItem(): void
    {
        $validated = $this->validate();

        $resourceUrl = $validated['resource_url'] ?: null;

        if ($this->uploadMode === 'upload' && $this->itemFile) {
            // A new file was chosen — store it and drop the previous file (if any).
            $folder = 'session-items/' . $this->course->id;
            $path   = $this->itemFile->store($folder, 'public');

            if ($this->existingFilePath && Storage::disk('public')->exists($this->existingFilePath)) {
                Storage::disk('public')->delete($this->existingFilePath);
            }

            $resourceUrl = Storage::disk('public')->url($path);
        } elseif ($this->uploadMode === 'upload' && ! $this->itemFile) {
            // Upload tab active but no new file chosen — keep whatever was already stored.
            $resourceUrl = $this->existingFilePath
                ? Storage::disk('public')->url($this->existingFilePath)
                : null;
        }

        CourseSessionItem::updateOrCreate(
            ['id' => $this->editItemId, 'course_session_id' => $this->activeSessionIdForItem],
            [
                'course_session_id' => $this->activeSessionIdForItem,
                'item_type'         => $validated['item_type'],
                'title'             => $validated['item_title'],
                'resource_type'     => $validated['resource_type'] ?: null,
                'content'           => $validated['item_content'],
                'resource_url'      => $resourceUrl,
            ]
        );

        $this->itemFile = null;
        $this->loadCourse();
        $this->activeModal = null;
        $this->dispatch('toast', type: 'success', message: 'Session item saved.');
    }

    /* ── Deletes (confirmed via SweetAlert) ──────────────────── */
    public function confirmDeleteWeek(int $id): void
    {
        $week = CourseWeek::findOrFail($id);
        $this->dispatch('confirm-delete', kind: 'week', id: $id, label: $week->title);
    }

    public function confirmDeleteSession(int $id): void
    {
        $session = CourseSession::findOrFail($id);
        $this->dispatch('confirm-delete', kind: 'session', id: $id, label: $session->title);
    }

    public function confirmDeleteItem(int $id): void
    {
        $item = CourseSessionItem::findOrFail($id);
        $this->dispatch('confirm-delete', kind: 'item', id: $id, label: $item->title);
    }

    #[On('deleteConfirmed')]
    public function handleDeleteConfirmed(string $kind, int $id): void
    {
        match ($kind) {
            'week'    => CourseWeek::where('id', $id)->delete(),
            'session' => CourseSession::where('id', $id)->delete(),
            'item'    => CourseSessionItem::where('id', $id)->delete(),
            default   => null,
        };

        $this->loadCourse();
        $this->dispatch('toast', type: 'success', message: ucfirst($kind) . ' removed.');
    }

    public function render()
    {
        return view('livewire.course-manager', [
            'categories'   => CourseCategory::orderBy('name')->get(),
            'courseTypes'  => CourseType::orderBy('name')->get(),
            'courseLevels' => CourseLevel::orderBy('name')->get(),
            'itemTypes'    => $this->itemTypeOptions(),
        ]);
    }
}