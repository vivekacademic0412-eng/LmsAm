<?php

namespace App\Livewire\Admin\Courses;

use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\CourseSession;
use App\Models\CourseSessionItem;
use App\Models\CourseSessionSetting;
use App\Models\CourseWeek;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

class CourseSessionManager extends Component
{
    use WithFileUploads;

    public $categories = [];
    public $category_id = null;
    public $course_id = null;
    public $week_id = null;

    public $weeks = [];
    public $sessions = [];

    // ---- session modal ----
    public $showSessionModal = false;
    public $editing_session_id = null;
    public $session_number;
    public $title;
    public $is_required_for_certificate = false;
    public $meet_link;
    public $meet_datetime;
    public $is_visible = true;

    // ---- session items ----
    public $active_session_id = null; // which session's item list is expanded
    public $items = [];

    // ---- item modal ----
    public $showItemModal = false;
    public $editing_item_id = null;
    public $item_type;
    public $item_title;
    public $resource_type; // video | document | link
    public $content;
    public $resource_url;
    public $is_live = false;
    public $live_at;
    public $linked_from_item_id;

    /** @var \Livewire\Features\SupportFileUploads\TemporaryUploadedFile|null */
    public $video_file = null;
    /** @var \Livewire\Features\SupportFileUploads\TemporaryUploadedFile|null */
    public $doc_file = null;
    public $uploading = false;

    // items whose type always needs a video/document/link resource attached
    const RESOURCE_REQUIRED_TYPES = ['intro', 'main_video'];

    public function mount(): void
    {
        $this->categories = CourseCategory::orderBy('name')->get();
    }

    /**
     * Crash Course is never shown here — it's fully auto-synced from its
     * parent Basic / Professional-Beginner course (see CrashCourseSyncService).
     * Admins should never manually edit its weeks/sessions.
     */
    public function getCoursesProperty()
    {
        if (!$this->category_id) return collect();

        return Course::where('category_id', $this->category_id)
            ->whereHas('courseType', fn ($q) => $q->where('name', '!=', 'Crash Course'))
            ->orderBy('title')
            ->get();
    }

    public function updatedCategoryId(): void
    {
        $this->course_id = null;
        $this->week_id = null;
        $this->weeks = [];
        $this->sessions = [];
        $this->closeItemsPanel();
    }

    public function updatedCourseId(): void
    {
        $this->week_id = null;
        $this->sessions = [];
        $this->weeks = $this->course_id
            ? CourseWeek::where('course_id', $this->course_id)->orderBy('week_number')->get()
            : [];
        $this->closeItemsPanel();
    }

    public function updatedWeekId(): void
    {
        $this->closeItemsPanel();
        $this->loadSessions();
    }

    public function loadSessions(): void
    {
        if (!$this->week_id) {
            $this->sessions = [];
            return;
        }

        $this->sessions = CourseSession::with(['settings', 'items'])
            ->where('course_week_id', $this->week_id)
            ->orderBy('session_number')
            ->get();
    }

    // ---------------------------------------------------------------
    // Session modal
    // ---------------------------------------------------------------

    public function openSessionModal(): void
    {
        $this->resetSessionForm();
        $this->showSessionModal = true;
    }

    public function editSession($sessionId): void
    {
        $session = CourseSession::with('settings')->findOrFail($sessionId);
        $this->editing_session_id = $session->id;
        $this->session_number = $session->session_number;
        $this->title = $session->title;

        $s = $session->settings;
        $this->is_required_for_certificate = (bool) optional($s)->is_required_for_certificate;
        $this->meet_link = optional($s)->meet_link;
        $this->meet_datetime = optional($s)->meet_datetime?->format('Y-m-d\TH:i');
        $this->is_visible = optional($s)->is_visible ?? true;
        $this->showSessionModal = true;
    }

    public function closeSessionModal(): void
    {
        $this->showSessionModal = false;
        $this->resetSessionForm();
    }

    public function resetSessionForm(): void
    {
        $this->editing_session_id = null;
        $this->session_number = null;
        $this->title = null;
        $this->is_required_for_certificate = false;
        $this->meet_link = null;
        $this->meet_datetime = null;
        $this->is_visible = true;
    }

    public function saveSession(): void
    {
        $this->validate([
            'week_id'        => 'required|exists:course_weeks,id',
            'session_number' => 'required|integer|min:1',
            'title'          => 'required|string|max:255',
            'meet_link'      => 'nullable|url',
            'meet_datetime'  => 'nullable|date',
        ], [], [
            'title' => 'session title',
        ]);

        $session = CourseSession::updateOrCreate(
            [
                'id'             => $this->editing_session_id,
                'course_week_id' => $this->week_id,
            ],
            [
                'course_week_id' => $this->week_id,
                'session_number' => $this->session_number,
                'title'          => $this->title,
            ]
        );

        CourseSessionSetting::updateOrCreate(
            ['course_session_id' => $session->id],
            [
                'is_required_for_certificate' => $this->is_required_for_certificate,
                'meet_link'                    => $this->meet_link,
                'meet_datetime'                => $this->meet_datetime,
                'is_visible'                    => $this->is_visible,
            ]
        );

        $wasEditing = (bool) $this->editing_session_id;
        $this->loadSessions();
        $this->closeSessionModal();

        // Modal closes here deliberately (session is a one-per-slot form) but we
        // fire a success event either way for the SweetAlert toast.
        $this->dispatch('session-saved', wasEditing: $wasEditing);
    }

    public function deleteSession($sessionId): void
    {
        CourseSession::where('id', $sessionId)->delete();

        if ($this->active_session_id === (int) $sessionId) {
            $this->closeItemsPanel();
        }

        $this->loadSessions();
        $this->dispatch('session-deleted');
    }

    // ---------------------------------------------------------------
    // Session items (intro / main_video / task / quiz / notes)
    // ---------------------------------------------------------------

    public function manageItems($sessionId): void
    {
        $this->active_session_id = (int) $sessionId;
        $this->loadItems();
    }

    public function closeItemsPanel(): void
    {
        $this->active_session_id = null;
        $this->items = [];
    }

    public function loadItems(): void
    {
        if (!$this->active_session_id) {
            $this->items = [];
            return;
        }

        $this->items = CourseSessionItem::where('course_session_id', $this->active_session_id)
            ->orderByRaw("case item_type when 'intro' then 1 when 'main_video' then 2 when 'task' then 3 when 'quiz' then 4 else 5 end")
            ->orderBy('id')
            ->get();
    }

    public function openItemModal(): void
    {
        $this->resetItemForm();
        $this->showItemModal = true;
    }

    public function editItem($itemId): void
    {
        $item = CourseSessionItem::findOrFail($itemId);

        $this->active_session_id = $item->course_session_id;
        $this->editing_item_id = $item->id;
        $this->item_type = $item->item_type;
        $this->item_title = $item->title;
        $this->resource_type = $item->resource_type;
        $this->content = $item->content;
        $this->resource_url = $item->resource_type === 'link' ? $item->resource_url : null;
        $this->is_live = (bool) $item->is_live;
        $this->live_at = $item->live_at?->format('Y-m-d\TH:i');
        $this->linked_from_item_id = $item->linked_from_item_id;
        $this->video_file = null;
        $this->doc_file = null;

        $this->loadItems();
        $this->showItemModal = true;
    }

    public function closeItemModal(): void
    {
        $this->showItemModal = false;
        $this->resetItemForm();
    }

    public function resetItemForm(): void
    {
        $this->editing_item_id = null;
        $this->item_type = null;
        $this->item_title = null;
        $this->resource_type = null;
        $this->content = null;
        $this->resource_url = null;
        $this->is_live = false;
        $this->live_at = null;
        $this->linked_from_item_id = null;
        $this->video_file = null;
        $this->doc_file = null;
        $this->uploading = false;
    }

    public function saveItem(): void
    {
        $isVideo = $this->resource_type === 'video';
        $isDocument = $this->resource_type === 'document';
        $isLink = $this->resource_type === 'link';
        $resourceRequired = in_array($this->item_type, self::RESOURCE_REQUIRED_TYPES, true);

        $existingItem = $this->editing_item_id ? CourseSessionItem::find($this->editing_item_id) : null;
        $hasExistingFile = $existingItem && $existingItem->resource_url && in_array($existingItem->resource_type, ['video', 'document'], true);

        $this->validate([
            'active_session_id' => 'required|exists:course_sessions,id',
            'item_type'         => 'required|in:' . implode(',', CourseSessionItem::TYPES),
            'item_title'        => 'required|string|max:255',
            'resource_type'     => $resourceRequired ? 'required|in:video,document,link' : 'nullable|in:video,document,link',
            'content'           => 'nullable|string',
            'video_file'        => ($isVideo && !$hasExistingFile)
                ? 'required|file|mimetypes:video/mp4,video/quicktime,video/webm,video/x-msvideo|max:512000'
                : 'nullable|file|mimetypes:video/mp4,video/quicktime,video/webm,video/x-msvideo|max:512000',
            'doc_file'          => ($isDocument && !$hasExistingFile)
                ? 'required|file|mimes:pdf,ppt,pptx|max:51200' // 50MB
                : 'nullable|file|mimes:pdf,ppt,pptx|max:51200',
            'resource_url'      => $isLink ? 'required|url' : 'nullable|url',
        ], [
            'video_file.required' => 'Please upload a video file — every intro / main video item needs one.',
            'doc_file.required'   => 'Please upload a PPT or PDF file for this item.',
            'resource_url.required' => 'Please enter the external link URL.',
        ]);

        $data = [
            'course_session_id'   => $this->active_session_id,
            'item_type'           => $this->item_type,
            'title'               => $this->item_title,
            'resource_type'       => $this->resource_type,
            'content'             => $this->content,
            'is_live'             => $this->is_live,
            'live_at'             => $this->live_at,
            'linked_from_item_id' => $this->linked_from_item_id,
        ];

        $this->uploading = true;

        if ($isVideo) {
            if ($this->video_file) {
                $path = $this->video_file->store('course-videos', 'public');
                $data['resource_url'] = Storage::url($path);
                $data['video_path'] = $path;
            }
        } elseif ($isDocument) {
            if ($this->doc_file) {
                $path = $this->doc_file->store('course-documents', 'public');
                $data['resource_url'] = Storage::url($path);
                $data['video_path'] = null;
            }
        } elseif ($isLink) {
            $data['resource_url'] = $this->resource_url;
        } else {
            $data['resource_url'] = null;
        }

        $this->uploading = false;

        CourseSessionItem::updateOrCreate(
            [
                'id'                => $this->editing_item_id,
                'course_session_id' => $this->active_session_id,
            ],
            $data
        );

        $wasEditing = (bool) $this->editing_item_id;
        $this->loadItems();

        // IMPORTANT: modal stays open on purpose so the admin can keep adding
        // items to this session without re-opening it each time.
        $this->resetItemForm();
        $this->dispatch('item-saved', wasEditing: $wasEditing);
    }

    public function deleteItem($itemId): void
    {
        $item = CourseSessionItem::find($itemId);
        if ($item?->video_path) {
            Storage::disk('public')->delete($item->video_path);
        }
        CourseSessionItem::where('id', $itemId)->delete();

        $this->loadItems();
        $this->dispatch('item-deleted');
    }

    public function render()
    {
        return view('livewire.admin.courses.course-session-manager');
    }
}