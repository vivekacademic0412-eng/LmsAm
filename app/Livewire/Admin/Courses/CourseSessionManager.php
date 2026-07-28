<?php

namespace App\Livewire\Admin\Courses;

use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\CourseSession;
use App\Models\CourseSessionItem;
use App\Models\CourseSessionSetting;
use App\Models\CourseWeek;
use App\Models\VideoAccessLog;
use Cloudinary\Cloudinary;
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

    // add/edit session form
    public $editing_session_id = null;
    public $session_number;
    public $title;
    public $is_required_for_certificate = false;
    public $meet_link;
    public $meet_datetime;
    public $is_visible = true;

    // ---- session items ----
    public $active_session_id = null; // which session's item panel is open
    public $items = [];

    // add/edit item form
    public $editing_item_id = null;
    public $item_type;
    public $item_title;
    public $resource_type;
    public $content;
    public $resource_url;
    public $is_live = false;
    public $live_at;
    public $linked_from_item_id;

    /** @var \Livewire\Features\SupportFileUploads\TemporaryUploadedFile|null */
    public $video_file = null;
    public $video_uploading = false;

    // logs shown for the item currently being viewed/edited
    public $viewing_logs_for_item_id = null;
    public $item_logs = [];

    public function mount(): void
    {
        $this->categories = CourseCategory::orderBy('name')->get();
    }

    public function getCoursesProperty()
    {
        if (!$this->category_id) return collect();
        return Course::where('category_id', $this->category_id)->orderBy('title')->get();
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

        $this->sessions = CourseSession::with('settings')
            ->where('course_week_id', $this->week_id)
            ->orderBy('session_number')
            ->get();
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
    }

    public function resetForm(): void
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

        session()->flash('success', 'Session saved.');
        $this->resetForm();
        $this->loadSessions();
    }

    public function deleteSession($sessionId): void
    {
        CourseSession::where('id', $sessionId)->delete();

        if ($this->active_session_id === (int) $sessionId) {
            $this->closeItemsPanel();
        }

        session()->flash('success', 'Session deleted.');
        $this->loadSessions();
    }

    // ---------------------------------------------------------------
    // Session items (intro / main_video / task / quiz / notes)
    // ---------------------------------------------------------------

    /**
     * Toggle the items panel open/closed for a given session.
     */
    public function manageItems($sessionId): void
    {
        if ($this->active_session_id === (int) $sessionId) {
            $this->closeItemsPanel();
            return;
        }

        $this->active_session_id = (int) $sessionId;
        $this->resetItemForm();
        $this->loadItems();
    }

    public function closeItemsPanel(): void
    {
        $this->active_session_id = null;
        $this->items = [];
        $this->resetItemForm();
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

    public function editItem($itemId): void
    {
        $item = CourseSessionItem::findOrFail($itemId);

        // make sure the panel is open for the item's own session
        $this->active_session_id = $item->course_session_id;

        $this->editing_item_id = $item->id;
        $this->item_type = $item->item_type;
        $this->item_title = $item->title;
        $this->resource_type = $item->resource_type;
        $this->content = $item->content;
        $this->resource_url = $item->resource_url;
        $this->is_live = (bool) $item->is_live;
        $this->live_at = $item->live_at?->format('Y-m-d\TH:i');
        $this->linked_from_item_id = $item->linked_from_item_id;
        $this->video_file = null; // never pre-fill a file input; existing video stays unless replaced
        $this->viewing_logs_for_item_id = null;
        $this->item_logs = [];

        $this->loadItems();
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
        $this->video_uploading = false;
    }

    /**
     * Show recent access/suspicious-activity logs for an item, right in the admin table.
     */
    public function viewLogs($itemId): void
    {
        if ($this->viewing_logs_for_item_id === (int) $itemId) {
            $this->viewing_logs_for_item_id = null;
            $this->item_logs = [];
            return;
        }

        $this->viewing_logs_for_item_id = (int) $itemId;
        $this->item_logs = VideoAccessLog::with('user')
            ->where('course_session_item_id', $itemId)
            ->latest('created_at')
            ->limit(100)
            ->get();
    }

    public function saveItem(): void
    {
        $isVideo = $this->resource_type === 'video';

        $this->validate([
            'active_session_id' => 'required|exists:course_sessions,id',
            'item_type'         => 'required|in:' . implode(',', CourseSessionItem::TYPES),
            'item_title'        => 'nullable|string|max:255',
            'resource_type'     => 'nullable|string|max:100',
            'content'           => 'nullable|string',
            // URL only applies to non-video resources now
            'resource_url'      => $isVideo ? 'nullable' : 'nullable|url',
            'is_live'           => 'boolean',
            'live_at'           => 'nullable|date',
            // required on create for a video item; optional on edit (keep existing video if not replacing)
            'video_file'        => ($isVideo && !$this->editing_item_id)
                ? 'required|file|mimetypes:video/mp4,video/quicktime,video/webm,video/x-msvideo|max:512000' // 500MB
                : 'nullable|file|mimetypes:video/mp4,video/quicktime,video/webm,video/x-msvideo|max:512000',
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

        if ($isVideo) {
            // never store a raw shareable URL for video items
            $data['resource_url'] = null;

            if ($this->video_file) {
                $this->video_uploading = true;

                $cloudinary = new Cloudinary(config('services.cloudinary.url', env('CLOUDINARY_URL')));

                $result = $cloudinary->uploadApi()->upload(
                    $this->video_file->getRealPath(),
                    [
                        'resource_type' => 'video',
                        'type'          => 'authenticated', // private: no public URL works without a signature
                        'folder'        => 'course-videos',
                    ]
                );

                $data['cloudinary_public_id']       = $result['public_id'];
                $data['cloudinary_resource_type']   = $result['resource_type'];   // 'video'
                $data['cloudinary_format']          = $result['format'];
                $data['cloudinary_delivery_type']   = $result['type'];            // 'authenticated'

                $this->video_uploading = false;
            }
        } else {
            $data['resource_url'] = $this->resource_url;
        }

        CourseSessionItem::updateOrCreate(
            [
                'id'                 => $this->editing_item_id,
                'course_session_id'  => $this->active_session_id,
            ],
            $data
        );

        session()->flash('success', 'Item saved.');
        $this->resetItemForm();
        $this->loadItems();
    }

    public function deleteItem($itemId): void
    {
        CourseSessionItem::where('id', $itemId)->delete();
        session()->flash('success', 'Item deleted.');
        $this->loadItems();
    }

    public function render()
    {
        return view('livewire.admin.courses.course-session-manager');
    }
}