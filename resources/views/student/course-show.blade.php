@extends('layouts.app')

@section('content')
    @php
        $course = $enrollment->course;
        $allSessions = $course->weeks->flatMap->sessions;
        $progressPercent = $totalItems > 0 ? (int) round(($completedItems / $totalItems) * 100) : 0;
        $pendingItems = max(0, $totalItems - $completedItems);
        $completedMap = array_flip($completedItemIds ?? []);
        $completedWeeksCount = $course->weeks->filter(function ($week) use ($completedMap) {
            $weekItems = $week->sessions->flatMap->items;

            return $weekItems->isNotEmpty()
                && $weekItems->every(fn ($item) => isset($completedMap[(int) $item->id]));
        })->count();
        $completedSessionsCount = $allSessions->filter(function ($session) use ($completedMap) {
            $sessionItems = $session->items;

            return $sessionItems->isNotEmpty()
                && $sessionItems->every(fn ($item) => isset($completedMap[(int) $item->id]));
        })->count();
        $heroThumb = $course->thumbnail_url ?: null;
        $courseSummary = $course->short_description;

        if (! $courseSummary) {
            $courseSummary = trim(preg_replace('/\s+/', ' ', strip_tags((string) $course->description)));
            $courseSummary = $courseSummary !== '' ? \Illuminate\Support\Str::limit($courseSummary, 220) : null;
        }

        $nextWeekId = null;
        $nextSessionId = null;

        foreach ($course->weeks as $week) {
            foreach ($week->sessions as $session) {
                foreach ($session->items as $item) {
                    if ((int) $item->id === (int) $nextPendingItemId) {
                        $nextWeekId = (int) $week->id;
                        $nextSessionId = (int) $session->id;
                        break 3;
                    }
                }
            }
        }

        $selectedWeekId = max(0, (int) request('week', 0));
        $selectedSessionId = max(0, (int) request('session', 0));
        $selectedItemId = max(0, (int) request('item', 0));

        $selectedWeek = $selectedWeekId > 0 ? $course->weeks->firstWhere('id', $selectedWeekId) : null;
        if (! $selectedWeek) {
            $selectedSessionId = 0;
            $selectedItemId = 0;
        }

        $selectedSession = $selectedWeek && $selectedSessionId > 0
            ? $selectedWeek->sessions->firstWhere('id', $selectedSessionId)
            : null;
        if (! $selectedSession) {
            $selectedItemId = 0;
        }

        $selectedItem = $selectedSession && $selectedItemId > 0
            ? $selectedSession->items->firstWhere('id', $selectedItemId)
            : null;

        if (! $selectedWeek) {
            $selectedWeek = $nextWeekId
                ? $course->weeks->firstWhere('id', $nextWeekId)
                : $course->weeks->first();
            $selectedWeekId = (int) ($selectedWeek?->id ?? 0);
        }

        if (! $selectedSession && $selectedWeek) {
            $selectedSession = $nextSessionId && (int) $selectedWeek->id === (int) $nextWeekId
                ? ($selectedWeek->sessions->firstWhere('id', $nextSessionId) ?: $selectedWeek->sessions->first())
                : $selectedWeek->sessions->first();
            $selectedSessionId = (int) ($selectedSession?->id ?? 0);
        }

        if (! $selectedItem && $selectedSession) {
            $selectedItem = $nextPendingItemId && (int) $selectedSession->id === (int) $nextSessionId
                ? ($selectedSession->items->firstWhere('id', $nextPendingItemId) ?: $selectedSession->items->first())
                : $selectedSession->items->first();
            $selectedItemId = (int) ($selectedItem?->id ?? 0);
        }

        $selectedSubmission = $selectedItem ? $latestSubmissions->get($selectedItem->id) : null;
        $selectedIsQuiz = $selectedItem?->item_type === \App\Models\CourseSessionItem::TYPE_QUIZ;
        $selectedIsTask = $selectedItem?->item_type === \App\Models\CourseSessionItem::TYPE_TASK;
        $selectedFormat = \Illuminate\Support\Str::lower((string) ($selectedItem?->cloudinary_format ?? ''));
        $selectedHasPrivateAsset = $selectedItem?->hasPrivateCloudinaryAsset() ?? false;
        $selectedCanPreviewVideo = $selectedHasPrivateAsset && $selectedItem?->cloudinary_resource_type === 'video';
        $selectedCanPreviewPdf = $selectedHasPrivateAsset && $selectedFormat === 'pdf';
        $selectedCanPreviewDocx = $selectedHasPrivateAsset && $selectedFormat === 'docx';
        $selectedCanPreviewPptx = $selectedHasPrivateAsset && $selectedFormat === 'pptx';
        $selectedCanPreviewOffice = $selectedHasPrivateAsset && in_array($selectedFormat, ['doc', 'ppt', 'xls', 'xlsx'], true);
        $selectedViewerUrl = $selectedItem ? route('course-session-items.media.view', $selectedItem) : null;
        $selectedEmbeddedViewerUrl = $selectedItem ? route('course-session-items.media.view', ['item' => $selectedItem, 'embed' => 1]) : null;
        $selectedStreamUrl = $selectedItem ? route('course-session-items.media.stream', $selectedItem) : null;
        $selectedDownloadUrl = $selectedItem && ($selectedIsTask || $selectedIsQuiz)
            ? route('course-session-items.media.download', $selectedItem)
            : null;
        $selectedSessionItems = $selectedSession ? $selectedSession->items : collect();
        $selectedSessionCompleted = $selectedSession
            ? $selectedSessionItems->filter(fn ($item) => isset($completedMap[(int) $item->id]))->count()
            : 0;
        $selectedSessionRemaining = max(0, $selectedSessionItems->count() - $selectedSessionCompleted);
        $selectedSessionProgress = $selectedSessionItems->count() > 0
            ? (int) round(($selectedSessionCompleted / $selectedSessionItems->count()) * 100)
            : 0;
        $continueUrl = $nextPendingItemId && $nextWeekId && $nextSessionId
            ? route('student.courses.show', [
                'course' => $course,
                'week' => $nextWeekId,
                'session' => $nextSessionId,
                'item' => $nextPendingItemId,
            ]).'#learning-workspace'
            : null;
        $selectedIsCompleted = $selectedItem ? isset($completedMap[(int) $selectedItem->id]) : false;
        $selectedIsNext = $selectedItem && $nextPendingItemId !== null && (int) $selectedItem->id === (int) $nextPendingItemId;
        $selectedItemIndex = $selectedItem
            ? $selectedSessionItems->search(fn ($sessionItem) => (int) $sessionItem->id === (int) $selectedItem->id)
            : false;
        $selectedItemOffset = $selectedItemIndex === false ? null : (int) $selectedItemIndex;
        $selectedItemPosition = $selectedItemOffset !== null ? $selectedItemOffset + 1 : null;
        $selectedPreviousItem = $selectedItemOffset !== null && $selectedItemOffset > 0
            ? $selectedSessionItems->get($selectedItemOffset - 1)
            : null;
        $selectedNextItem = $selectedItemOffset !== null && $selectedItemOffset < ($selectedSessionItems->count() - 1)
            ? $selectedSessionItems->get($selectedItemOffset + 1)
            : null;
        $selectedPreviousUrl = $selectedPreviousItem
            ? route('student.courses.show', [
                'course' => $course,
                'week' => $selectedWeek?->id,
                'session' => $selectedSession?->id,
                'item' => $selectedPreviousItem->id,
            ]).'#learning-workspace'
            : null;
        $selectedNextUrl = $selectedNextItem
            ? route('student.courses.show', [
                'course' => $course,
                'week' => $selectedWeek?->id,
                'session' => $selectedSession?->id,
                'item' => $selectedNextItem->id,
            ]).'#learning-workspace'
            : null;
        $selectedNextItemPosition = $selectedNextItem && $selectedItemPosition ? $selectedItemPosition + 1 : null;
        $selectedTypeLabel = $selectedItem ? ucwords(str_replace('_', ' ', $selectedItem->item_type)) : null;
        $selectedResourceLabel = $selectedFormat !== ''
            ? strtoupper($selectedFormat)
            : ($selectedItem?->cloudinary_resource_type
                ? strtoupper((string) $selectedItem->cloudinary_resource_type)
                : ($selectedItem?->resource_url ? 'LINK' : 'TEXT'));
        $selectedPreviewLabel = 'Lesson Notes';

        if ($selectedCanPreviewVideo) {
            $selectedPreviewLabel = 'Secure Video';
        } elseif ($selectedCanPreviewPdf) {
            $selectedPreviewLabel = 'Inline PDF';
        } elseif ($selectedCanPreviewDocx) {
            $selectedPreviewLabel = 'Inline DOCX';
        } elseif ($selectedCanPreviewPptx) {
            $selectedPreviewLabel = 'Inline PPTX';
        } elseif ($selectedCanPreviewOffice) {
            $selectedPreviewLabel = 'Secure Office Viewer';
        } elseif ($selectedItem?->resource_url) {
            $selectedPreviewLabel = 'External Resource';
        } elseif ($selectedIsTask) {
            $selectedPreviewLabel = 'Task Workspace';
        } elseif ($selectedIsQuiz) {
            $selectedPreviewLabel = $selectedItem?->is_live ? 'Live Quiz' : 'Quiz Waiting Room';
        }

        $selectedAccessLabel = 'Read and continue';

        if ($selectedHasPrivateAsset) {
            $selectedAccessLabel = 'View-only in project';
        } elseif ($selectedItem?->resource_url) {
            $selectedAccessLabel = 'Opens in new tab';
        } elseif ($selectedIsTask) {
            $selectedAccessLabel = 'Upload required';
        } elseif ($selectedIsQuiz) {
            $selectedAccessLabel = $selectedItem?->is_live ? 'Text answer required' : 'Waiting for trainer';
        }

        $selectedReadAloudSelectors = array_values(array_filter([
            $selectedCanPreviewDocx ? '#lesson-read-aloud-docx .docx-renderer__body' : null,
            $selectedCanPreviewDocx ? '#lesson-read-aloud-docx section.docx-viewer' : null,
            $selectedCanPreviewPptx ? '#lesson-read-aloud-pptx .pptx-renderer__slide-stage' : null,
            !empty($selectedItem?->content) ? '#lesson-read-aloud-notes' : null,
        ]));
        $selectedCanUseReadAloud = count($selectedReadAloudSelectors) > 0;
    @endphp

    <style>
        .page { max-width: 1480px; padding: 8px 20px 0; }
        .student-course-shell { display: grid; gap: 22px; --course-smooth: cubic-bezier(0.22, 1, 0.36, 1); }
        .student-course-alert { border: 1px solid #f2c4c4; border-radius: 18px; background: #fff7f7; color: #932626; padding: 14px 16px; box-shadow: var(--shadow); }
        .student-course-alert strong { display: block; margin-bottom: 6px; }
        .student-course-hero { display: grid; grid-template-columns: minmax(0, 1.3fr) minmax(280px, 0.7fr); gap: 24px; padding: 28px; border-radius: 18px; border: 1px solid rgba(15, 89, 199, 0.1); background: radial-gradient(circle at top right, rgba(77, 160, 255, 0.22), transparent 36%), linear-gradient(135deg, #ffffff 0%, #f3f8ff 52%, #edf4ff 100%); box-shadow: 0 22px 50px rgba(15, 64, 140, 0.1); }
        .hero-copy, .hero-side, .roadmap-card, .week-stage-body, .session-stage-body, .viewer-actions, .aside-card, .aside-list { display: grid; gap: 14px; }
        .hero-back, .stage-link, .download-link { width: fit-content; text-decoration: none; color: #0f59c7; font-size: 12px; font-weight: 800; }
        .hero-back:hover, .stage-link:hover, .download-link:hover, .jump-link:hover { text-decoration: underline; }
        .hero-chip-row, .hero-meta-row, .hero-actions, .stage-actions, .viewer-actions-row { display: flex; flex-wrap: wrap; gap: 8px; }
        .hero-chip, .hero-meta, .item-pill, .stage-count, .viewer-status, .item-status { display: inline-flex; align-items: center; justify-content: center; padding: 6px 10px; border-radius: 999px; font-size: 11px; font-weight: 800; }
        .hero-chip { background: #0f59c7; color: #fff; }
        .hero-chip.hero-chip--soft, .hero-meta { background: #eef4ff; color: #19438f; border: 1px solid #d0e0fb; }
        .stage-count, .item-status--pending { background: #f4f7fb; color: #5b6d87; }
        .item-status--ready, .viewer-status--ready { background: #eef4ff; color: #275bb7; }
        .item-status--done, .viewer-status--done { background: #edf9f2; color: #1f7a4d; }
        .item-status--live, .viewer-status--live { background: #fff2df; color: #b16509; }
        .item-status--next, .viewer-status--next { background: #edf4ff; color: #0f59c7; }
        .student-course-hero h1, .roadmap-head h2, .week-stage h3, .session-stage h4, .viewer-head h3, .aside-card h3 { margin: 0; color: #102849; }
        .student-course-hero h1 { font-size: clamp(28px, 3vw, 38px); line-height: 1.06; }
        .hero-summary { margin: 0; color: #5a6b84; font-size: 14px; line-height: 1.7; }
        .hero-note, .roadmap-note, .stage-note, .viewer-note, .submission-meta, .aside-note { margin: 0; color: #5a6b84; font-size: 13px; line-height: 1.7; }
        .hero-progress-card, .hero-preview, .viewer-panel, .submission-box, .hint-panel, .empty-panel { border: 1px solid #dbe6f6; border-radius: 18px; background: rgba(255, 255, 255, 0.88); }
        .hero-progress-card, .viewer-panel, .submission-box, .hint-panel, .empty-panel { padding: 16px; }
        .hero-progress-head, .week-stage-top, .session-stage-top, .viewer-head, .aside-row { display: flex; justify-content: space-between; gap: 14px; align-items: start; }
        .progress-track, .stage-progress { height: 9px; border-radius: 999px; background: #e8eef8; overflow: hidden; }
        .progress-fill, .stage-progress-fill { height: 100%; border-radius: inherit; background: linear-gradient(90deg, #0f59c7 0%, #54a7ff 60%, #6ee7b7 100%); }
        .course-action { display: inline-flex; align-items: center; justify-content: center; gap: 6px; min-height: 40px; padding: 9px 14px; border-radius: 8px; text-decoration: none; font-size: 12px; font-weight: 800; border: 1px solid #0f59c7; background: #0f59c7; color: #fff; }
        .course-action:hover { filter: brightness(0.97); }
        .course-action--soft { border-color: #c7dafc; background: #edf4ff; color: #19438f; }
        .course-action:disabled { opacity: 0.55; cursor: not-allowed; filter: none; }
        .hero-preview { min-height: 220px; overflow: hidden; border-radius: 12px; }
        .hero-preview img { width: 100%; height: 100%; object-fit: cover; display: block; border-radius: 16px; }
        .hero-preview--empty { display: grid; place-items: center; background: radial-gradient(circle at top left, rgba(63, 151, 255, 0.24), transparent 34%), linear-gradient(135deg, #dcecff 0%, #f4f8ff 100%); color: #4f6483; font-weight: 800; }
        .hero-stats { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 10px; }
        .hero-stat { border-radius: 12px; padding: 14px; border: 1px solid #d8e5f7; background: rgba(255, 255, 255, 0.9); }
        .hero-stat span { display: block; font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.04em; color: #607089; }
        .hero-stat strong { display: block; margin-top: 6px; font-size: 22px; color: #102849; }
        .session-focus-shell { display: grid; gap: 18px; padding: 24px; border-radius: 16px; border: 1px solid rgba(15, 89, 199, 0.12); background: radial-gradient(circle at top right, rgba(97, 180, 255, 0.18), transparent 34%), linear-gradient(135deg, #ffffff 0%, #f5f9ff 55%, #eef5ff 100%); box-shadow: 0 20px 42px rgba(15, 64, 140, 0.08); }
        .session-focus-top { display: flex; justify-content: space-between; align-items: start; gap: 16px; }
        .session-focus-copy { display: grid; gap: 12px; }
        .session-focus-copy h2 { margin: 0; color: #102849; font-size: clamp(26px, 2.6vw, 34px); line-height: 1.08; }
        .session-focus-actions { display: flex; flex-wrap: wrap; gap: 8px; justify-content: flex-end; }
        .session-switch-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 10px; }
        .session-switch-link { display: grid; gap: 4px; padding: 14px 16px; border-radius: 10px; border: 1px solid #dbe6f6; text-decoration: none; background: rgba(255, 255, 255, 0.92); color: #18304f; transition: transform 0.18s ease, box-shadow 0.18s ease, border-color 0.18s ease; }
        .session-switch-link:hover { transform: translateY(-1px); border-color: #bfd4f7; box-shadow: 0 16px 28px rgba(16, 55, 116, 0.08); }
        .session-switch-link--active { border-color: #0f59c7; background: #edf4ff; box-shadow: 0 18px 34px rgba(15, 89, 199, 0.12); }
        .session-switch-link strong { font-size: 15px; color: #102849; }
        .session-switch-link span { font-size: 12px; color: #5b6d87; }
        .session-focus-layout { display: grid; grid-template-columns: 340px minmax(0, 1fr); gap: 22px; align-items: start; }
        .session-focus-panel { border: 1px solid var(--line); border-radius: 12px; background: var(--card); box-shadow: var(--shadow); padding: 18px; display: grid; gap: 14px; }
        .session-focus-panel .viewer-head h3 { font-size: 22px; }
        .session-stage-summary { margin-top: 12px; }
        .student-course-layout { display: grid; grid-template-columns: minmax(0, 1fr) 320px; gap: 18px; align-items: start; }
        .roadmap-card, .week-stage, .session-stage, .aside-card { border: 1px solid var(--line); border-radius: 12px; background: var(--card); box-shadow: var(--shadow); }
        .roadmap-card, .aside-card, .week-stage, .session-stage { padding: 18px; }
        .roadmap-kicker, .stage-kicker, .aside-kicker, .item-order { display: inline-flex; align-items: center; gap: 6px; font-size: 11px; font-weight: 800; letter-spacing: 0.05em; text-transform: uppercase; color: #1f4fa3; }
        .week-stage, .session-stage { transition: transform 0.32s var(--course-smooth), box-shadow 0.32s var(--course-smooth), border-color 0.32s var(--course-smooth), background 0.32s var(--course-smooth); }
        .week-stage:hover, .session-stage:hover { transform: translateY(-1px); border-color: #c6d8ff; box-shadow: 0 20px 36px rgba(16, 55, 116, 0.08); }
        .week-stage--active, .session-stage--active { border-color: #bfd4f7; box-shadow: 0 18px 36px rgba(15, 89, 199, 0.12); background: linear-gradient(180deg, rgba(240, 247, 255, 0.95), #fff); }
        .week-stage-body, .session-stage-body { overflow: hidden; max-height: 0; opacity: 0; transform: translateY(-10px); margin-top: 0; padding-top: 0; transition: max-height 0.45s var(--course-smooth), opacity 0.24s ease, transform 0.28s ease, margin-top 0.28s ease, padding-top 0.28s ease; }
        .week-stage-body.is-open, .session-stage-body.is-open { opacity: 1; transform: translateY(0); margin-top: 16px; padding-top: 14px; border-top: 1px solid #e7ebf0; }
        .item-nav-grid { display: grid; gap: 10px; grid-template-columns: 1fr; }
        .item-nav { display: grid; grid-template-columns: minmax(0, 1fr) auto; align-items: center; gap: 12px; padding: 14px 16px; text-decoration: none; border-radius: 8px; border: 1px solid #dde6f3; background: linear-gradient(180deg, #ffffff 0%, #fbfdff 100%); color: inherit; transition: transform 0.18s ease, box-shadow 0.18s ease, border-color 0.18s ease, background 0.18s ease; }
        .item-nav:hover { transform: translateY(-1px); border-color: #bfd4f7; box-shadow: 0 16px 28px rgba(16, 55, 116, 0.08); }
        .item-nav--active { border-color: #0f59c7; box-shadow: 0 18px 34px rgba(15, 89, 199, 0.14); background: #edf4ff; }
        .item-nav--done { background: linear-gradient(180deg, rgba(56, 161, 105, 0.05), #fff); }
        .item-nav-main, .item-nav-copy, .item-nav-side { display: grid; gap: 4px; }
        .item-nav-main { min-width: 0; }
        .item-nav-copy strong { font-size: 15px; color: #112849; }
        .item-nav-type { font-size: 12px; color: #5c6d86; }
        .item-nav-side { justify-items: end; }
        .item-nav-arrow { font-size: 11px; font-weight: 800; color: #0f59c7; letter-spacing: 0.04em; text-transform: uppercase; }
        .session-viewer-host { display: grid; gap: 14px; }
        .viewer-panel, .hint-panel { animation: coursePanelIn 0.28s var(--course-smooth); }
        .viewer-panel { display: grid; gap: 18px; border-radius: 18px; border-color: #dbe6f6; background: rgba(255, 255, 255, 0.94); padding: 24px; box-shadow: 0 22px 40px rgba(15, 64, 140, 0.08); }
        .viewer-head h3 { margin-top: 4px; font-size: 24px; }
        .lesson-stage-card {
            display: grid;
            gap: 14px;
            padding: 18px;
            border-radius: 18px;
            border: 1px solid #d7e4f7;
            background: radial-gradient(circle at top right, rgba(84, 167, 255, 0.18), transparent 34%), linear-gradient(180deg, #f7fbff 0%, #ffffff 100%);
        }
        .lesson-stage-top {
            display: flex;
            justify-content: space-between;
            align-items: start;
            gap: 12px;
        }
        .lesson-stage-copy {
            display: grid;
            gap: 6px;
        }
        .lesson-stage-copy h4 {
            margin: 0;
            color: #102849;
            font-size: clamp(22px, 2vw, 28px);
            line-height: 1.12;
        }
        .lesson-stage-stats {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 10px;
        }
        .lesson-stage-stat {
            display: grid;
            gap: 5px;
            padding: 12px 14px;
            border-radius: 14px;
            border: 1px solid #deebf8;
            background: rgba(255, 255, 255, 0.9);
        }
        .lesson-stage-stat span {
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            color: #668;
        }
        .lesson-stage-stat strong {
            color: #102849;
            font-size: 16px;
            line-height: 1.2;
        }
        .viewer-frame { overflow: hidden; border: 1px solid #d6e4f5; border-radius: 18px; background: #091120; box-shadow: 0 24px 40px rgba(8, 18, 38, 0.18); }
        .viewer-frame iframe, .viewer-frame video { width: 100%; display: block; border: 0; }
        .viewer-frame iframe { min-height: 720px; background: #fff; }
        .viewer-frame video { min-height: 0; aspect-ratio: 16 / 9; background: #000; }
        .docx-renderer {
            width: 100%;
            min-height: 720px;
            border: 1px solid #d6e4f5;
            border-radius: 18px;
            background: #eef4fb;
            color: #21324b;
            overflow: auto;
            box-shadow: 0 24px 40px rgba(8, 18, 38, 0.12);
            padding: 0;
        }
        .docx-renderer__status {
            min-height: 640px;
            display: grid;
            place-items: center;
            text-align: center;
            color: #5a6b84;
            font-size: 14px;
            padding: 24px;
        }
        .docx-renderer__body {
            max-width: 960px;
            margin: 0 auto;
            line-height: 1.7;
            padding: 22px;
        }
        .docx-renderer__body img {
            max-width: 100%;
            height: auto;
        }
        .docx-renderer__body table {
            width: 100%;
            border-collapse: collapse;
        }
        .docx-renderer__body td,
        .docx-renderer__body th {
            border: 1px solid #d7e3f3;
            padding: 8px;
        }
        .docx-renderer .docx-viewer-wrapper {
            background: #eef4fb;
            padding: 24px 18px 6px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 18px;
        }
        .docx-renderer section.docx-viewer {
            margin: 0 0 18px;
            box-shadow: 0 18px 36px rgba(15, 64, 140, 0.14);
        }
        .docx-renderer section.docx-viewer:last-child {
            margin-bottom: 0;
        }
        .pptx-renderer {
            width: 100%;
            min-height: 720px;
            border: 1px solid #d6e4f5;
            border-radius: 18px;
            background: #eef4fb;
            overflow-x: hidden;
            overflow-y: auto;
            box-shadow: 0 24px 40px rgba(8, 18, 38, 0.12);
            padding: 24px 18px;
            display: grid;
            gap: 18px;
            align-content: start;
        }
        .pptx-renderer__status {
            min-height: 640px;
            display: grid;
            place-items: center;
            text-align: center;
            color: #5a6b84;
            font-size: 14px;
            padding: 24px;
        }
        .pptx-renderer__slide-card {
            display: grid;
            gap: 10px;
            justify-items: center;
        }
        .pptx-renderer__slide-meta {
            font-size: 12px;
            font-weight: 800;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            color: #4f6483;
        }
        .pptx-renderer__slide-stage {
            width: 100%;
            display: grid;
            justify-items: center;
            overflow: hidden;
        }
        .pptx-renderer__slide-stage > * {
            max-width: 100%;
            box-shadow: 0 18px 36px rgba(15, 64, 140, 0.14);
        }
        .viewer-text, .submission-answer { padding: 16px 18px; border-radius: 16px; border: 1px solid #e0e9f5; background: #fff; color: #21324b; font-size: 14px; line-height: 1.75; white-space: pre-wrap; }
        .submission-box form { display: grid; gap: 10px; }
        .submission-box input[type="file"], .submission-box textarea { width: 100%; border: 1px solid #d4deec; border-radius: 8px; padding: 10px 12px; background: #fff; color: #142744; font: inherit; }
        .submission-box textarea { min-height: 120px; resize: vertical; }
        .aside-progress { display: grid; place-items: center; width: 116px; height: 116px; margin: 0 auto; border-radius: 50%; border: 10px solid #dce8fb; box-shadow: inset 0 0 0 8px rgba(255, 255, 255, 0.94); background: radial-gradient(circle, #fff 58%, transparent 59%); color: #0f4ea9; font-size: 24px; font-weight: 800; }
        .aside-row { font-size: 13px; color: #5d6e87; }
        .aside-row strong { color: #132c52; }
        .jump-links { display: grid; gap: 8px; }
        .jump-link { padding: 10px 12px; border-radius: 8px; border: 1px solid #e0e7f1; background: #f9fbff; color: #20314d; text-decoration: none; font-size: 12px; font-weight: 700; }
        .jump-link:hover { border-color: #c6d8ff; background: #eef4ff; }
        .workspace-card, .support-card {
            border: 1px solid var(--line);
            border-radius: 14px;
            background: var(--card);
            box-shadow: var(--shadow);
        }
        .support-card { padding: 16px; display: grid; gap: 8px; }
        .support-card span {
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            color: #607089;
        }
        .support-card strong {
            font-size: 24px;
            color: #102849;
        }
        .learning-workspace {
            display: grid;
            gap: 16px;
            padding: 22px;
            border-radius: 22px;
            border: 1px solid rgba(15, 89, 199, 0.12);
            background: radial-gradient(circle at top right, rgba(84, 167, 255, 0.14), transparent 30%), linear-gradient(180deg, rgba(240, 247, 255, 0.9), #fff);
            box-shadow: 0 22px 44px rgba(15, 64, 140, 0.09);
        }
        .learning-workspace-head {
            display: flex;
            justify-content: space-between;
            align-items: start;
            gap: 16px;
        }
        .learning-workspace-head h2 {
            margin: 2px 0 0;
            color: #102849;
            font-size: clamp(26px, 2.4vw, 33px);
            line-height: 1.08;
        }
        .workspace-head-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 12px;
        }
        .workspace-flow {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 10px;
        }
        .workspace-flow-step {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 7px 11px;
            border-radius: 999px;
            border: 1px solid #d7e4f8;
            background: #f7faff;
            color: #5f7190;
            font-size: 11px;
            font-weight: 800;
        }
        .workspace-flow-step--active {
            border-color: #0f59c7;
            background: #edf4ff;
            color: #0f59c7;
        }
        .learning-workspace-layout {
            display: grid;
            grid-template-columns: 260px minmax(0, 1fr);
            gap: 18px;
            align-items: start;
        }
        .curriculum-stack {
            display: grid;
            gap: 12px;
            position: sticky;
            top: 18px;
        }
        .workspace-card { padding: 15px; display: grid; gap: 10px; border-radius: 18px; }
        .workspace-card-head { display: grid; gap: 4px; }
        .workspace-card-head h3 { margin: 0; color: #102849; font-size: 16px; }
        .workspace-card-note { margin: 0; color: #5a6b84; font-size: 12px; line-height: 1.55; }
        .learning-glance-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 12px;
        }
        .glance-card {
            display: grid;
            gap: 6px;
            padding: 16px 18px;
            border-radius: 18px;
            border: 1px solid #dce7f7;
            background: rgba(255, 255, 255, 0.92);
            box-shadow: 0 16px 30px rgba(15, 64, 140, 0.06);
        }
        .glance-card--accent {
            border-color: #bed4f7;
            background: linear-gradient(180deg, rgba(237, 244, 255, 0.96), rgba(255, 255, 255, 0.98));
        }
        .glance-card span {
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            color: #5d7393;
        }
        .glance-card strong {
            color: #102849;
            font-size: 20px;
            line-height: 1.15;
        }
        .glance-card p {
            margin: 0;
            color: #5a6b84;
            font-size: 12px;
            line-height: 1.65;
        }
        .week-link-grid, .session-link-list, .item-link-list, .support-grid {
            display: grid;
            gap: 8px;
        }
        .week-link, .session-link, .item-link {
            display: grid;
            gap: 4px;
            padding: 11px 12px;
            border-radius: 14px;
            border: 1px solid #dde6f3;
            background: linear-gradient(180deg, #ffffff 0%, #fbfdff 100%);
            text-decoration: none;
            color: #173153;
            transition: transform 0.18s ease, box-shadow 0.18s ease, border-color 0.18s ease, background 0.18s ease;
        }
        .link-head, .item-link-meta {
            display: flex;
            align-items: start;
            justify-content: space-between;
            gap: 8px;
            flex-wrap: wrap;
        }
        .link-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 5px 9px;
            border-radius: 999px;
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            border: 1px solid #d7e4f7;
            background: #f8fbff;
            color: #5f7190;
        }
        .link-badge--active {
            border-color: #0f59c7;
            background: #edf4ff;
            color: #0f59c7;
        }
        .link-badge--next {
            border-color: #cfe0ff;
            background: #f4f8ff;
            color: #2557af;
        }
        .week-link:hover, .session-link:hover, .item-link:hover {
            transform: translateY(-1px);
            border-color: #bfd4f7;
            box-shadow: 0 12px 24px rgba(16, 55, 116, 0.08);
        }
        .week-link--active, .session-link--active, .item-link--active {
            border-color: #0f59c7;
            background: #edf4ff;
            box-shadow: 0 14px 28px rgba(15, 89, 199, 0.12);
        }
        .item-link--done { background: linear-gradient(180deg, rgba(56, 161, 105, 0.05), #fff); }
        .week-link strong, .session-link strong, .item-link strong {
            font-size: 13px;
            color: #102849;
        }
        .item-pill {
            width: fit-content;
            background: #f7faff;
            color: #31588f;
            border: 1px solid #d8e4f7;
        }
        .week-link span, .session-link span, .item-link span {
            font-size: 11px;
            color: #5d6e87;
        }
        .week-link small, .session-link small, .item-link small {
            font-size: 11px;
            color: #789;
        }
        .lesson-panel { display: grid; gap: 18px; }
        .lesson-path {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            padding: 10px 12px;
            border: 1px solid #dce7f7;
            border-radius: 14px;
            background: #f9fbff;
        }
        .lesson-path .hero-meta { background: #fff; }
        .lesson-panel .viewer-panel { min-height: 100%; }
        .viewer-head--stack {
            display: grid;
            gap: 14px;
        }
        .viewer-head-copy {
            display: grid;
            gap: 6px;
        }
        .lesson-nav-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }
        .viewer-summary-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 10px;
        }
        .viewer-summary-card {
            display: grid;
            gap: 6px;
            padding: 14px 16px;
            border-radius: 16px;
            border: 1px solid #dce8f8;
            background: linear-gradient(180deg, #f8fbff 0%, #ffffff 100%);
        }
        .viewer-summary-card span {
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            color: #60718b;
        }
        .viewer-summary-card strong {
            color: #102849;
            font-size: 17px;
            line-height: 1.2;
        }
        .viewer-summary-card p {
            margin: 0;
            color: #5a6b84;
            font-size: 12px;
            line-height: 1.65;
        }
        .viewer-notes-panel {
            display: grid;
            gap: 12px;
            padding: 18px;
            border-radius: 18px;
            border: 1px solid #deebf8;
            background: #f9fbff;
        }
        .viewer-notes-head {
            display: flex;
            align-items: start;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
        }
        .viewer-notes-head h4 {
            margin: 4px 0 0;
            color: #102849;
            font-size: 18px;
            line-height: 1.2;
        }
        .viewer-actions {
            padding-top: 2px;
        }
        .lesson-tools {
            display: grid;
            gap: 12px;
            padding: 16px 18px;
            border-radius: 18px;
            border: 1px solid #deebf8;
            background: linear-gradient(180deg, #f8fbff 0%, #ffffff 100%);
        }
        .lesson-tools-head {
            display: grid;
            gap: 4px;
        }
        .lesson-tools-head strong {
            color: #102849;
            font-size: 16px;
        }
        .lesson-tools-head span {
            color: #5a6b84;
            font-size: 13px;
            line-height: 1.6;
        }
        .lesson-tools-grid {
            display: grid;
            gap: 10px;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        }
        .lesson-tools-field {
            display: grid;
            gap: 6px;
        }
        .lesson-tools-field span {
            color: #5a6b84;
            font-size: 12px;
            font-weight: 700;
        }
        .lesson-tools-field select {
            width: 100%;
            min-height: 40px;
            border: 1px solid #d4deec;
            border-radius: 8px;
            padding: 10px 12px;
            background: #fff;
            color: #142744;
            font: inherit;
        }
        .support-grid { grid-template-columns: repeat(4, minmax(0, 1fr)); }
        .support-card { min-height: 100%; border-radius: 18px; }
        .support-card--accent {
            border-color: #bfd4f7;
            background: linear-gradient(180deg, rgba(237, 244, 255, 0.94), rgba(255, 255, 255, 0.98));
        }
        @keyframes coursePanelIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        @media (max-width: 1080px) { .student-course-hero, .session-focus-layout, .learning-workspace-layout, .lesson-stage-stats { grid-template-columns: 1fr; } .learning-glance-grid, .support-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); } .viewer-summary-grid { grid-template-columns: 1fr; } .hero-side { order: -1; } .session-focus-actions, .curriculum-stack { position: static; } }
        @media (max-width: 760px) { .page { padding-inline: 14px; } .item-nav-grid, .hero-stats, .learning-glance-grid, .support-grid, .viewer-summary-grid { grid-template-columns: 1fr; } .student-course-hero, .roadmap-card, .aside-card, .week-stage, .session-stage, .session-focus-shell, .session-focus-panel, .learning-workspace, .workspace-card { padding: 16px; } .hero-progress-head, .week-stage-top, .session-stage-top, .viewer-head, .aside-row, .item-nav, .session-focus-top, .learning-workspace-head, .viewer-notes-head, .link-head, .item-link-meta { display: grid; } .item-nav-side, .session-focus-actions { justify-items: start; } .lesson-nav-actions { width: 100%; } .lesson-nav-actions .course-action { width: 100%; } }
    </style>

    <div class="student-course-shell">
        @if ($errors->any())
            <section class="student-course-alert" role="alert">
                <strong>We could not submit that item yet.</strong>
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </section>
        @endif

        <section class="student-course-hero">
            <div class="hero-copy">
                <a href="{{ route('student.courses') }}" class="hero-back">&larr; Back to My Courses</a>

                <div class="hero-chip-row">
                    <span class="hero-chip">Student Learning Flow</span>
                    <span class="hero-chip hero-chip--soft">
                        {{ $progressPercent >= 100 ? 'Course Completed' : ($continueUrl ? 'Continue Your Next Item' : 'Choose a Week to Start') }}
                    </span>
                </div>

                <div>
                    <h1>{{ $course->title }}</h1>
                    @if ($courseSummary)
                        <p class="hero-summary">{{ $courseSummary }}</p>
                    @endif
                </div>

                <div class="hero-meta-row">
                    <span class="hero-meta">Category: {{ $course->category?->name ?? 'N/A' }}</span>
                    @if ($course->subcategory)
                        <span class="hero-meta">Subcategory: {{ $course->subcategory->name }}</span>
                    @endif
                    <span class="hero-meta">Trainer: {{ $enrollment->trainer?->name ?? 'Not Assigned' }}</span>
                    <span class="hero-meta">Language: {{ $course->language ?: 'N/A' }}</span>
                </div>

                <div class="hero-progress-card">
                    <div class="hero-progress-head">
                        <div>
                            <strong>{{ $progressPercent }}% complete</strong>
                            <p class="hero-note">{{ $completedItems }} of {{ $totalItems }} learning items finished</p>
                        </div>
                        <div class="hero-actions">
                            @if ($continueUrl)
                                <a href="{{ $continueUrl }}" class="course-action">Continue Next Item</a>
                            @endif
                            <a href="#learning-workspace" class="course-action course-action--soft">Open Learning Workspace</a>
                        </div>
                    </div>
                    <div class="progress-track">
                        <div class="progress-fill" style="width: {{ $progressPercent }}%;"></div>
                    </div>
                </div>
            </div>

            <div class="hero-side">
                <div class="hero-preview {{ $heroThumb ? '' : 'hero-preview--empty' }}">
                    @if ($heroThumb)
                        <img src="{{ $heroThumb }}" alt="{{ $course->title }}">
                    @else
                        Course Preview
                    @endif
                </div>

                <div class="hero-stats">
                    <div class="hero-stat">
                        <span>Weeks</span>
                        <strong>{{ $course->weeks->count() }}</strong>
                    </div>
                    <div class="hero-stat">
                        <span>Sessions</span>
                        <strong>{{ $allSessions->count() }}</strong>
                    </div>
                    <div class="hero-stat">
                        <span>Items</span>
                        <strong>{{ $totalItems }}</strong>
                    </div>
                    <div class="hero-stat">
                        <span>Pending</span>
                        <strong>{{ $pendingItems }}</strong>
                    </div>
                </div>
            </div>
        </section>

        <section class="learning-workspace" id="learning-workspace">
            <div class="learning-workspace-head">
                <div>
                    <span class="roadmap-kicker">Connected Learning Workspace</span>
                    <h2>
                        @if ($selectedWeek && $selectedSession)
                            Week {{ $selectedWeek->week_number }} / Session {{ $selectedSession->session_number }}
                        @elseif ($selectedWeek)
                            Week {{ $selectedWeek->week_number }}
                        @else
                            Start Your Course Flow
                        @endif
                    </h2>
                    <p class="workspace-card-note">Choose a week, then a session, then a lesson item. The selected content opens on the right in one clean screen.</p>
                    <div class="workspace-head-meta">
                        <span class="hero-meta">Progress {{ $progressPercent }}%</span>
                        <span class="hero-meta">{{ $selectedWeek ? 'Week '.$selectedWeek->week_number : 'Choose week' }}</span>
                        <span class="hero-meta">{{ $selectedSession ? 'Session '.$selectedSession->session_number : 'Choose session' }}</span>
                        <span class="hero-meta">{{ $pendingItems }} pending</span>
                    </div>
                    <div class="workspace-flow">
                        <span class="workspace-flow-step {{ $selectedWeek ? 'workspace-flow-step--active' : '' }}">1. Week</span>
                        <span class="workspace-flow-step {{ $selectedSession ? 'workspace-flow-step--active' : '' }}">2. Session</span>
                        <span class="workspace-flow-step {{ $selectedItem ? 'workspace-flow-step--active' : '' }}">3. Lesson Item</span>
                    </div>
                </div>
                <div class="hero-actions">
                    @if ($continueUrl)
                        <a href="{{ $continueUrl }}" class="course-action">Continue Next Item</a>
                    @endif
                    <a href="{{ route('student.courses') }}" class="course-action course-action--soft">Back to My Courses</a>
                </div>
            </div>

            <div class="learning-glance-grid">
                <section class="glance-card">
                    <span>Course Progress</span>
                    <strong>{{ $progressPercent }}%</strong>
                    <p>{{ $completedItems }} of {{ $totalItems }} items completed across {{ $completedWeeksCount }}/{{ $course->weeks->count() }} weeks.</p>
                </section>
                <section class="glance-card">
                    <span>Current Week</span>
                    <strong>{{ $selectedWeek ? 'Week '.$selectedWeek->week_number : 'Not chosen yet' }}</strong>
                    <p>{{ $selectedWeek ? $selectedWeek->title : 'Choose a week from the left to load its sessions.' }}</p>
                </section>
                <section class="glance-card">
                    <span>Current Session</span>
                    <strong>{{ $selectedSession ? 'Session '.$selectedSession->session_number : 'Not chosen yet' }}</strong>
                    <p>
                        @if ($selectedSession)
                            {{ $selectedSessionCompleted }} of {{ $selectedSessionItems->count() }} items completed in this session.
                        @else
                            Pick a session to load its lessons and submissions.
                        @endif
                    </p>
                </section>
                <section class="glance-card {{ $selectedNextItem || $continueUrl ? 'glance-card--accent' : '' }}">
                    <span>Next Step</span>
                    <strong>{{ $selectedNextItem?->title ?? ($continueUrl ? 'Continue next pending item' : 'Choose your next lesson') }}</strong>
                    <p>
                        @if ($selectedNextItem && $selectedNextItemPosition)
                            Item {{ $selectedNextItemPosition }} is ready right after this lesson in the same session.
                        @elseif ($continueUrl)
                            Resume the next pending lesson from your course flow anytime.
                        @else
                            Start from the left curriculum stack and the page will guide you forward.
                        @endif
                    </p>
                </section>
            </div>

            <div class="learning-workspace-layout">
                <aside class="curriculum-stack">
                    <section class="workspace-card">
                        <div class="workspace-card-head">
                            <span class="roadmap-kicker">Step 1</span>
                            <h3>Choose a Week</h3>
                            <p class="workspace-card-note">Open the week you want to study. Your next pending lesson is prioritized when available.</p>
                        </div>
                        <div class="week-link-grid">
                            @forelse ($course->weeks as $week)
                                @php
                                    $weekStartSession = ((int) $week->id === (int) $nextWeekId)
                                        ? ($week->sessions->firstWhere('id', $nextSessionId) ?: $week->sessions->first())
                                        : $week->sessions->first();
                                    $weekStartItem = $weekStartSession
                                        ? (((int) $week->id === (int) $nextWeekId && $nextPendingItemId)
                                            ? ($weekStartSession->items->firstWhere('id', $nextPendingItemId) ?: $weekStartSession->items->first())
                                            : $weekStartSession->items->first())
                                        : null;
                                    $weekRouteParams = ['course' => $course, 'week' => $week->id];
                                    if ($weekStartSession) {
                                        $weekRouteParams['session'] = $weekStartSession->id;
                                    }
                                    if ($weekStartItem) {
                                        $weekRouteParams['item'] = $weekStartItem->id;
                                    }
                                    $weekLinkUrl = route('student.courses.show', $weekRouteParams).'#learning-workspace';
                                    $isCurrentWeek = $selectedWeek && (int) $selectedWeek->id === (int) $week->id;
                                    $weekItems = $week->sessions->flatMap->items;
                                    $weekDone = $weekItems->filter(fn ($item) => isset($completedMap[(int) $item->id]))->count();
                                @endphp
                                <a href="{{ $weekLinkUrl }}" class="week-link {{ $isCurrentWeek ? 'week-link--active' : '' }}">
                                    <div class="link-head">
                                        <strong>Week {{ $week->week_number }}: {{ $week->title }}</strong>
                                        @if ($isCurrentWeek)
                                            <span class="link-badge link-badge--active">Open</span>
                                        @elseif ((int) $week->id === (int) $nextWeekId)
                                            <span class="link-badge link-badge--next">Recommended</span>
                                        @endif
                                    </div>
                                    <span>{{ $week->sessions->count() }} sessions available</span>
                                    <small>{{ $weekDone }}/{{ $weekItems->count() }} items completed</small>
                                </a>
                            @empty
                                <div class="empty-panel">No weeks are available in this course yet.</div>
                            @endforelse
                        </div>
                    </section>

                    <section class="workspace-card">
                        <div class="workspace-card-head">
                            <span class="roadmap-kicker">Step 2</span>
                            <h3>{{ $selectedWeek ? 'Choose a Session' : 'Sessions' }}</h3>
                            <p class="workspace-card-note">
                                {{ $selectedWeek ? 'Sessions from the selected week appear here.' : 'Choose a week first to load its sessions.' }}
                            </p>
                        </div>
                        <div class="session-link-list">
                            @if ($selectedWeek && $selectedWeek->sessions->isNotEmpty())
                                @foreach ($selectedWeek->sessions as $session)
                                    @php
                                        $sessionItems = $session->items;
                                        $sessionDone = $sessionItems->filter(fn ($item) => isset($completedMap[(int) $item->id]))->count();
                                        $sessionStartItem = ((int) $session->id === (int) $nextSessionId && $nextPendingItemId)
                                            ? ($sessionItems->firstWhere('id', $nextPendingItemId) ?: $sessionItems->first())
                                            : ($sessionItems->firstWhere('id', $selectedItem?->id) ?: $sessionItems->first());
                                        $sessionRouteParams = [
                                            'course' => $course,
                                            'week' => $selectedWeek->id,
                                            'session' => $session->id,
                                        ];
                                        if ($sessionStartItem) {
                                            $sessionRouteParams['item'] = $sessionStartItem->id;
                                        }
                                        $sessionLinkUrl = route('student.courses.show', $sessionRouteParams).'#learning-workspace';
                                        $isCurrentSession = $selectedSession && (int) $selectedSession->id === (int) $session->id;
                                    @endphp
                                    <a href="{{ $sessionLinkUrl }}" class="session-link {{ $isCurrentSession ? 'session-link--active' : '' }}">
                                        <div class="link-head">
                                            <strong>Session {{ $session->session_number }}: {{ $session->title }}</strong>
                                            @if ($isCurrentSession)
                                                <span class="link-badge link-badge--active">Open</span>
                                            @elseif ((int) $session->id === (int) $nextSessionId)
                                                <span class="link-badge link-badge--next">Recommended</span>
                                            @endif
                                        </div>
                                        <span>{{ $sessionItems->count() }} learning item{{ $sessionItems->count() === 1 ? '' : 's' }}</span>
                                        <small>{{ $sessionDone }}/{{ $sessionItems->count() }} completed</small>
                                    </a>
                                @endforeach
                            @elseif ($selectedWeek)
                                <div class="empty-panel">This week does not have any sessions yet.</div>
                            @else
                                <div class="hint-panel">
                                    <strong>Select a week first</strong>
                                    <p class="viewer-note">Your session list will appear here after you choose a week.</p>
                                </div>
                            @endif
                        </div>
                    </section>

                    <section class="workspace-card">
                        <div class="workspace-card-head">
                            <span class="roadmap-kicker">Step 3</span>
                            <h3>{{ $selectedSession ? 'Open a Lesson Item' : 'Lesson Items' }}</h3>
                            <p class="workspace-card-note">
                                {{ $selectedSession ? 'Pick one intro, main video, task, or quiz from this session.' : 'Choose a session first to load its items.' }}
                            </p>
                        </div>
                        <div class="item-link-list">
                            @if ($selectedSession && $selectedSessionItems->isNotEmpty())
                                @foreach ($selectedSessionItems as $item)
                                    @php
                                        $isCompleted = isset($completedMap[(int) $item->id]);
                                        $isNextItem = $nextPendingItemId !== null && (int) $item->id === (int) $nextPendingItemId;
                                        $isSelectedItem = $selectedItem && (int) $selectedItem->id === (int) $item->id;
                                        $isQuiz = $item->item_type === \App\Models\CourseSessionItem::TYPE_QUIZ;
                                        $isTask = $item->item_type === \App\Models\CourseSessionItem::TYPE_TASK;
                                        $itemStatus = $isCompleted
                                            ? ($isTask ? 'Task submitted' : ($isQuiz ? 'Quiz answered' : 'Completed'))
                                            : ($isNextItem ? 'Up next' : ($isQuiz && $item->is_live ? 'Live quiz' : 'Ready to open'));
                                        $itemTone = $isCompleted ? 'done' : ($isNextItem ? 'next' : ($isQuiz && $item->is_live ? 'live' : 'ready'));
                                        $itemNote = $isTask
                                            ? 'Upload your completed work from this page.'
                                            : ($isQuiz
                                                ? ($item->is_live ? 'Answer it directly inside this workspace.' : 'Waiting for the trainer to make it live.')
                                                : ($item->resource_url ? 'Opens with a linked lesson resource.' : 'Preview the lesson content inside the page.'));
                                        $itemUrl = route('student.courses.show', [
                                            'course' => $course,
                                            'week' => $selectedWeek->id,
                                            'session' => $selectedSession->id,
                                            'item' => $item->id,
                                        ]).'#learning-workspace';
                                    @endphp
                                    <a href="{{ $itemUrl }}" class="item-link {{ $isSelectedItem ? 'item-link--active' : '' }} {{ $isCompleted ? 'item-link--done' : '' }}">
                                        <div class="link-head">
                                            <strong>Item {{ $loop->iteration }}: {{ $item->title }}</strong>
                                            @if ($isSelectedItem)
                                                <span class="link-badge link-badge--active">Now Viewing</span>
                                            @elseif ($isNextItem)
                                                <span class="link-badge link-badge--next">Up Next</span>
                                            @endif
                                        </div>
                                        <div class="item-link-meta">
                                            <span class="item-pill">{{ ucwords(str_replace('_', ' ', $item->item_type)) }}</span>
                                            <span class="item-status item-status--{{ $itemTone }}">{{ $itemStatus }}</span>
                                        </div>
                                        <small>{{ $itemNote }}</small>
                                    </a>
                                @endforeach
                            @elseif ($selectedSession)
                                <div class="empty-panel">This session does not have any items yet.</div>
                            @else
                                <div class="hint-panel">
                                    <strong>Select a session first</strong>
                                    <p class="viewer-note">The lesson items from your selected session will appear here.</p>
                                </div>
                            @endif
                        </div>
                    </section>
                </aside>

                <div class="lesson-panel">
                    <div class="lesson-path">
                        @if ($selectedWeek)
                            <span class="hero-meta">Week {{ $selectedWeek->week_number }}: {{ $selectedWeek->title }}</span>
                        @endif
                        @if ($selectedSession)
                            <span class="hero-meta">Session {{ $selectedSession->session_number }}: {{ $selectedSession->title }}</span>
                        @endif
                        @if ($selectedItem)
                            <span class="hero-meta">{{ ucwords(str_replace('_', ' ', $selectedItem->item_type)) }}</span>
                        @endif
                    </div>

                    @if ($selectedItem && $selectedSession && $selectedWeek)
                        @php
                            $selectedTone = 'ready';
                            $selectedLabel = 'Ready to Open';

                            if ($selectedIsCompleted) {
                                $selectedTone = 'done';
                                $selectedLabel = $selectedIsTask ? 'Task Submitted' : ($selectedIsQuiz ? 'Quiz Answered' : 'Completed');
                            } elseif ($selectedIsNext) {
                                $selectedTone = 'next';
                                $selectedLabel = 'Up Next';
                            } elseif ($selectedIsQuiz && $selectedItem->is_live) {
                                $selectedTone = 'live';
                                $selectedLabel = 'Live Quiz';
                            }
                        @endphp

                        <section class="viewer-panel">
                            <div class="viewer-head viewer-head--stack">
                                <div class="viewer-head-copy">
                                    <span class="stage-kicker">{{ $selectedTypeLabel }}</span>
                                    <h3>{{ $selectedItem->title }}</h3>
                                    <p class="viewer-note">Everything for this lesson stays here, including protected previews, notes, and submissions.</p>
                                </div>
                                <div class="lesson-nav-actions">
                                    @if ($selectedPreviousUrl)
                                        <a href="{{ $selectedPreviousUrl }}" class="course-action course-action--soft">&larr; Previous Lesson</a>
                                    @endif
                                    @if ($selectedNextUrl)
                                        <a href="{{ $selectedNextUrl }}" class="course-action">Next Lesson &rarr;</a>
                                    @elseif ($continueUrl && ! $selectedIsCompleted)
                                        <a href="{{ $continueUrl }}" class="course-action">Return to Next Pending</a>
                                    @endif
                                </div>
                            </div>

                            <div class="viewer-summary-grid">
                                <div class="viewer-summary-card">
                                    <span>Preview Mode</span>
                                    <strong>{{ $selectedPreviewLabel }}</strong>
                                    <p>{{ $selectedResourceLabel }} source attached to this lesson.</p>
                                </div>
                                <div class="viewer-summary-card">
                                    <span>Access</span>
                                    <strong>{{ $selectedAccessLabel }}</strong>
                                    <p>
                                        @if ($selectedHasPrivateAsset)
                                            Protected files stay inside the secure course flow.
                                        @elseif ($selectedItem->resource_url)
                                            This lesson continues only when you open the linked resource.
                                        @else
                                            No extra access step is needed for this lesson.
                                        @endif
                                    </p>
                                </div>
                                <div class="viewer-summary-card">
                                    <span>Next Action</span>
                                    <strong>
                                        @if ($selectedIsTask)
                                            Submit your task
                                        @elseif ($selectedIsQuiz)
                                            {{ $selectedItem->is_live ? 'Submit your answer' : 'Wait for quiz to go live' }}
                                        @elseif ($selectedNextItem)
                                            Continue to the next lesson
                                        @else
                                            Finish this session strong
                                        @endif
                                    </strong>
                                    <p>
                                        @if ($selectedNextItem)
                                            {{ $selectedNextItem->title }} is ready after this lesson.
                                        @elseif ($selectedSessionRemaining > 0)
                                            {{ $selectedSessionRemaining }} item{{ $selectedSessionRemaining === 1 ? '' : 's' }} remain in this session.
                                        @else
                                            You are at the end of this session.
                                        @endif
                                    </p>
                                </div>
                            </div>

                            <div class="lesson-stage-card">
                                <div class="lesson-stage-top">
                                    <div class="lesson-stage-copy">
                                        <span class="stage-kicker">Session Focus</span>
                                        <h4>{{ $selectedSession->title }}</h4>
                                        <p class="viewer-note">Stay in this view to watch the lesson, read the details, and submit your work without jumping around the page.</p>
                                    </div>
                                    <span class="viewer-status viewer-status--{{ $selectedTone }}">{{ $selectedLabel }}</span>
                                </div>
                                <div class="lesson-stage-stats">
                                    <div class="lesson-stage-stat">
                                        <span>Current Lesson</span>
                                        <strong>{{ $selectedItemPosition ? $selectedItemPosition.' / '.$selectedSessionItems->count() : $selectedSessionItems->count() }}</strong>
                                    </div>
                                    <div class="lesson-stage-stat">
                                        <span>Session Progress</span>
                                        <strong>{{ $selectedSessionProgress }}%</strong>
                                    </div>
                                    <div class="lesson-stage-stat">
                                        <span>Trainer</span>
                                        <strong>{{ $enrollment->trainer?->name ?? 'Not Assigned' }}</strong>
                                    </div>
                                </div>
                            </div>

                            @if ($selectedCanUseReadAloud)
                                <div
                                    class="lesson-tools"
                                    data-read-aloud
                                    data-read-aloud-selectors="{{ implode(', ', $selectedReadAloudSelectors) }}"
                                    data-read-aloud-ready="Use your browser voice to listen to the visible lesson text."
                                    data-read-aloud-empty="Slides are still loading or this lesson does not have readable text yet."
                                >
                                    <div class="lesson-tools-head">
                                        <strong>Read Aloud</strong>
                                        <span data-read-aloud-status aria-live="polite">Use your browser voice to listen to the visible lesson text.</span>
                                    </div>
                                    <div class="lesson-tools-grid">
                                        <label class="lesson-tools-field">
                                            <span>Voice</span>
                                            <select data-read-aloud-voice></select>
                                        </label>
                                        <label class="lesson-tools-field">
                                            <span>Speed</span>
                                            <select data-read-aloud-rate>
                                                <option value="0.85">Slow</option>
                                                <option value="1" selected>Normal</option>
                                                <option value="1.15">Fast</option>
                                                <option value="1.3">Faster</option>
                                            </select>
                                        </label>
                                    </div>
                                    <div class="viewer-actions-row">
                                        <button type="button" class="course-action course-action--soft" data-read-aloud-play>Speak</button>
                                        <button type="button" class="course-action course-action--soft" data-read-aloud-pause disabled>Pause</button>
                                        <button type="button" class="course-action course-action--soft" data-read-aloud-resume disabled>Resume</button>
                                        <button type="button" class="course-action course-action--soft" data-read-aloud-stop disabled>Stop</button>
                                    </div>
                                </div>
                            @endif

                            @if ($selectedCanPreviewVideo)
                                <div class="viewer-frame">
                                    <video controls controlsList="nodownload noplaybackrate" preload="metadata">
                                        <source src="{{ $selectedStreamUrl }}">
                                        Your browser does not support secure video playback.
                                    </video>
                                </div>
                            @elseif ($selectedCanPreviewPdf)
                                <div class="viewer-frame">
                                    <iframe src="{{ $selectedStreamUrl }}#toolbar=0&navpanes=0&scrollbar=1" title="{{ $selectedItem->title }}"></iframe>
                                </div>
                            @elseif ($selectedCanPreviewDocx)
                                <div class="docx-renderer" id="lesson-read-aloud-docx" data-docx-stream="{{ $selectedStreamUrl }}">
                                    <div class="docx-renderer__status" data-docx-status>Loading DOCX preview...</div>
                                </div>
                            @elseif ($selectedCanPreviewPptx)
                                <div class="pptx-renderer" id="lesson-read-aloud-pptx" data-pptx-stream="{{ $selectedStreamUrl }}">
                                    <div class="pptx-renderer__status" data-pptx-status>Loading PPTX slides...</div>
                                </div>
                            @elseif ($selectedCanPreviewOffice)
                                <div class="viewer-frame">
                                    <iframe src="{{ $selectedEmbeddedViewerUrl }}" title="{{ $selectedItem->title }}" loading="lazy"></iframe>
                                </div>
                            @elseif ($selectedItem->resource_url)
                                <div class="hint-panel">
                                    <strong>External resource</strong>
                                    <p class="viewer-note">This lesson opens from an outside link. Use the button below to continue.</p>
                                </div>
                            @elseif ($selectedHasPrivateAsset)
                                <div class="hint-panel">
                                    <strong>Secure file attached</strong>
                                    <p class="viewer-note">This file is protected and cannot preview directly here, but you can open it safely below.</p>
                                </div>
                            @elseif (! $selectedIsTask && ! $selectedIsQuiz)
                                <div class="hint-panel">
                                    <strong>No preview source attached</strong>
                                    <p class="viewer-note">This learning item does not have a video or document attached yet.</p>
                                </div>
                            @endif

                            @if ($selectedItem->content)
                                <div class="viewer-notes-panel">
                                    <div class="viewer-notes-head">
                                        <div>
                                            <span class="stage-kicker">{{ $selectedIsTask ? 'Task Brief' : ($selectedIsQuiz ? 'Quiz Notes' : 'Lesson Notes') }}</span>
                                            <h4>{{ $selectedIsTask ? 'Instructions and expectations' : ($selectedIsQuiz ? 'What to use before you answer' : 'What to remember from this lesson') }}</h4>
                                        </div>
                                        <span class="hero-meta">{{ $selectedResourceLabel }}</span>
                                    </div>
                                    <div class="viewer-text" id="lesson-read-aloud-notes">{{ $selectedItem->content }}</div>
                                </div>
                            @endif

                            <div class="viewer-actions">
                                <p class="viewer-note">
                                    @if ($selectedHasPrivateAsset)
                                        Protected media is view-only inside this course. Use the secure viewer when you want the dedicated protected view.
                                    @elseif ($selectedDownloadUrl)
                                        This lesson includes a downloadable brief for your submission.
                                    @else
                                        Stay on this page to finish the lesson and move forward smoothly.
                                    @endif
                                </p>
                                <div class="viewer-actions-row">
                                    @if ($selectedHasPrivateAsset)
                                        <a href="{{ $selectedViewerUrl }}" class="course-action">Open Secure Viewer</a>
                                    @endif
                                    @if ($selectedItem->resource_url)
                                        <a href="{{ $selectedItem->resource_url }}" target="_blank" rel="noopener" class="course-action course-action--soft">Open External Link</a>
                                    @endif
                                    @if ($selectedDownloadUrl)
                                        <a href="{{ $selectedDownloadUrl }}" class="course-action course-action--soft">Download Brief</a>
                                    @endif
                                </div>
                            </div>

                            @if ($selectedIsTask)
                                <div class="submission-box">
                                    <strong>Submit your task file</strong>
                                    <p class="viewer-note">Upload the completed task below. This keeps the same week, session, and item open after you submit.</p>
                                    <form method="POST" action="{{ route('course-session-items.submit', $selectedItem) }}" enctype="multipart/form-data">
                                        @csrf
                                        <input type="file" name="submission_file" accept="*/*" required>
                                        <button class="course-action course-action--soft" type="submit">Submit Task</button>
                                    </form>
                                    @if ($selectedSubmission)
                                        <div class="submission-meta">
                                            <span>Last submitted {{ optional($selectedSubmission->submitted_at)->diffForHumans() }}</span>
                                            <span>Review status: {{ $selectedSubmission->reviewStatusLabel() }}</span>
                                            @if ($selectedSubmission->file_name)
                                                <span>Latest file: {{ $selectedSubmission->file_name }}</span>
                                            @endif
                                            @if ($selectedSubmission->file_path)
                                                <a href="{{ route('course-item-submissions.download', $selectedSubmission) }}" class="download-link">Download Your Submission</a>
                                            @endif
                                            @if ($selectedSubmission->review_notes)
                                                <div class="submission-answer">{{ \Illuminate\Support\Str::limit($selectedSubmission->review_notes, 220) }}</div>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            @endif

                            @if ($selectedIsQuiz)
                                <div class="submission-box">
                                    <strong>Submit your quiz answer</strong>
                                    @if (! $selectedItem->is_live)
                                        <p class="viewer-note">This quiz is not live yet. Your trainer must open it first.</p>
                                    @else
                                        <p class="viewer-note">Write your answer below and submit it from this same connected workspace.</p>
                                        <form method="POST" action="{{ route('course-session-items.submit', $selectedItem) }}">
                                            @csrf
                                            <textarea name="answer_text" rows="4" placeholder="Type your answer here..." required>{{ old('answer_text') }}</textarea>
                                            <button class="course-action course-action--soft" type="submit">Submit Quiz</button>
                                        </form>
                                    @endif

                                    @if ($selectedSubmission)
                                        <div class="submission-meta">
                                            <span>Last answer submitted {{ optional($selectedSubmission->submitted_at)->diffForHumans() }}</span>
                                            <span>Review status: {{ $selectedSubmission->reviewStatusLabel() }}</span>
                                            @if ($selectedSubmission->answer_text)
                                                <div class="submission-answer">{{ \Illuminate\Support\Str::limit($selectedSubmission->answer_text, 220) }}</div>
                                            @endif
                                            @if ($selectedSubmission->review_notes)
                                                <div class="submission-answer">{{ \Illuminate\Support\Str::limit($selectedSubmission->review_notes, 220) }}</div>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </section>
                    @else
                        <div class="hint-panel">
                            <strong>Start from the left curriculum flow</strong>
                            <p class="viewer-note">Choose a week, then a session, then one lesson item. The matching content will open here on the right.</p>
                        </div>
                    @endif

                    <div class="support-grid">
                        <section class="support-card">
                            <span>Current Focus</span>
                            <strong>{{ $selectedItem?->title ?? ($selectedSession?->title ?? ($selectedWeek?->title ?? 'Choose your next step')) }}</strong>
                            <p class="aside-note">
                                @if ($selectedItem)
                                    You are working inside Session {{ $selectedSession->session_number }} of Week {{ $selectedWeek->week_number }}.
                                @elseif ($selectedSession)
                                    Pick one item from this session to start learning.
                                @else
                                    Use the left side to choose your week, session, and lesson item.
                                @endif
                            </p>
                        </section>
                        <section class="support-card">
                            <span>Session Progress</span>
                            <strong>{{ $selectedSessionProgress }}%</strong>
                            <p class="aside-note">{{ $selectedSessionCompleted }}/{{ $selectedSessionItems->count() }} items completed in this session. {{ $selectedSessionRemaining }} remaining.</p>
                        </section>
                        <section class="support-card support-card--accent">
                            <span>Next Step</span>
                            <strong>{{ $selectedNextItem?->title ?? ($continueUrl ? 'Continue your course flow' : 'Keep building momentum') }}</strong>
                            <p class="aside-note">
                                @if ($selectedNextItem)
                                    Move straight to the next lesson in this session when you are ready.
                                @elseif ($continueUrl)
                                    Re-open your next pending lesson from the overall course journey.
                                @else
                                    Use the left curriculum stack to choose the next step that fits you best.
                                @endif
                            </p>
                            @if ($selectedNextUrl)
                                <a href="{{ $selectedNextUrl }}" class="stage-link">Open Next Lesson</a>
                            @elseif ($continueUrl)
                                <a href="{{ $continueUrl }}" class="stage-link">Continue Course Flow</a>
                            @endif
                        </section>
                        <section class="support-card">
                            <span>Trainer Support</span>
                            <strong>{{ $enrollment->trainer?->name ?? 'Not Assigned' }}</strong>
                            <p class="aside-note">{{ $enrollment->trainer ? 'Follow the connected flow and submit each task or quiz from this same workspace.' : 'A trainer will appear here once one is assigned to your course.' }}</p>
                            <div class="hero-meta">Sessions done {{ $completedSessionsCount }}/{{ $allSessions->count() }}</div>
                        </section>
                    </div>
                </div>
            </div>
        </section>
    </div>
    @if ($selectedCanPreviewPptx)
        <script src="{{ asset('js/jszip.min.js') }}"></script>
        <script type="module" src="{{ asset('js/secure-pptx-viewer.js') }}"></script>
    @endif
    @if ($selectedCanPreviewDocx)
        <script src="{{ asset('js/jszip.min.js') }}" defer></script>
        <script src="{{ asset('js/docx-preview.min.js') }}" defer></script>
        <script src="{{ asset('js/mammoth.browser.min.js') }}" defer></script>
        <script src="{{ asset('js/secure-docx-viewer.js') }}" defer></script>
    @endif
    @if ($selectedCanUseReadAloud)
        <script src="{{ asset('js/secure-read-aloud.js') }}" defer></script>
    @endif
@endsection
