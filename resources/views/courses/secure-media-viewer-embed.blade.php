<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $item->title }}</title>
    @php
        $wmText = (string) (auth()->user()?->email ?? 'user');
        $wmSvg = "<svg xmlns='http://www.w3.org/2000/svg' width='520' height='320' viewBox='0 0 520 320'>"
            ."<text x='20' y='90' fill='rgba(0,0,0,0.38)' font-size='16' font-family='Arial, sans-serif' transform='rotate(-20 20 90)'>"
            .htmlspecialchars($wmText, ENT_QUOTES, 'UTF-8')
            ."</text></svg>";
        $wmData = 'data:image/svg+xml;utf8,'.rawurlencode($wmSvg);
        $readAloudSelectors = array_values(array_filter([
            !empty($isDocx) && $isDocx ? '.docx-renderer__body' : null,
            !empty($isDocx) && $isDocx ? '.docx-renderer section.docx-viewer' : null,
            !empty($isPptx) && $isPptx ? '.pptx-renderer__slide-stage' : null,
            !empty($isOfficeDoc) && $isOfficeDoc && empty($isDocx) && empty($isPptx) ? '.office-fallback__box' : null,
        ]));
        $hasReadAloud = count($readAloudSelectors) > 0;
    @endphp
    <style>
        html, body {
            margin: 0;
            min-height: 100%;
            background: #f7fbff;
            font-family: Arial, sans-serif;
        }
        body {
            padding: 12px;
            box-sizing: border-box;
        }
        .media-viewer-card {
            border: 1px solid #dbe6f6;
            border-radius: 14px;
            background: linear-gradient(180deg, rgba(20, 95, 209, 0.03), rgba(20, 95, 209, 0));
            padding: 14px;
            position: relative;
        }
        .media-stack {
            position: relative;
            border-radius: 12px;
            overflow: hidden;
        }
        .media-frame {
            width: 100%;
            min-height: 72vh;
            border: 1px solid #dbe6f6;
            border-radius: 12px;
            background: #000;
            position: relative;
            z-index: 1;
            box-sizing: border-box;
        }
        .doc-frame {
            background: #fff;
        }
        .docx-renderer {
            width: 100%;
            min-height: 72vh;
            border: 1px solid #dbe6f6;
            border-radius: 12px;
            background: #eef4fb;
            color: #21324b;
            overflow: auto;
            position: relative;
            z-index: 1;
            padding: 0;
            box-sizing: border-box;
        }
        .docx-renderer__status {
            min-height: 72vh;
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
            min-height: 72vh;
            border: 1px solid #dbe6f6;
            border-radius: 12px;
            background: #eef4fb;
            overflow-x: hidden;
            overflow-y: auto;
            position: relative;
            z-index: 1;
            padding: 24px 18px;
            display: grid;
            gap: 18px;
            box-sizing: border-box;
            align-content: start;
        }
        .pptx-renderer__status {
            min-height: calc(72vh - 48px);
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
            font-weight: 700;
            color: #4f6483;
            text-transform: uppercase;
            letter-spacing: 0.05em;
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
        .office-fallback {
            width: 100%;
            min-height: 72vh;
            border: 1px solid #dbe6f6;
            border-radius: 12px;
            background: linear-gradient(180deg, #f8fbff 0%, #ffffff 100%);
            color: #21324b;
            position: relative;
            z-index: 1;
            display: grid;
            place-items: center;
            padding: 26px;
            text-align: center;
            box-sizing: border-box;
        }
        .office-fallback__box {
            max-width: 620px;
            display: grid;
            gap: 12px;
        }
        .office-fallback__box h3 {
            margin: 0;
            font-size: 22px;
            color: #102849;
        }
        .office-fallback__box p {
            margin: 0;
            line-height: 1.7;
            color: #5a6b84;
        }
        .wm-layer {
            position: absolute;
            inset: 0;
            pointer-events: none;
            background-image: url("{{ $wmData }}");
            background-size: 520px 320px;
            background-repeat: repeat;
            mix-blend-mode: normal;
            opacity: 0.42;
            z-index: 2;
        }
        .viewer-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 12px;
        }
        .viewer-note-inline {
            margin-top: 12px;
            color: #5a6b84;
            font-size: 13px;
            line-height: 1.7;
        }
        .read-aloud-panel {
            display: grid;
            gap: 12px;
            margin-bottom: 14px;
            padding: 14px 16px;
            border: 1px solid #dbe6f6;
            border-radius: 12px;
            background: #fff;
        }
        .read-aloud-copy {
            display: grid;
            gap: 4px;
        }
        .read-aloud-copy strong {
            color: #102849;
            font-size: 15px;
        }
        .read-aloud-copy span {
            color: #5a6b84;
            font-size: 13px;
            line-height: 1.6;
        }
        .read-aloud-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }
        .read-aloud-toolbar {
            display: grid;
            gap: 10px;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        }
        .read-aloud-field {
            display: grid;
            gap: 6px;
        }
        .read-aloud-field span {
            color: #5a6b84;
            font-size: 12px;
            font-weight: 700;
        }
        .read-aloud-field select {
            width: 100%;
            min-height: 40px;
            border: 1px solid #d7deea;
            border-radius: 8px;
            background: #f8fbff;
            color: #17325a;
            font: inherit;
            padding: 8px 10px;
        }
        .audio-wrap {
            background: #0f172a;
            color: #fff;
            display: grid;
            place-items: center;
            padding: 18px;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 40px;
            padding: 10px 14px;
            border-radius: 8px;
            border: 1px solid #c7dafc;
            background: #edf4ff;
            color: #19438f;
            text-decoration: none;
            font-size: 12px;
            font-weight: 700;
            box-sizing: border-box;
        }
        .btn[type="button"]:disabled,
        .btn[disabled] {
            opacity: 0.55;
            cursor: not-allowed;
        }
    </style>
</head>
<body>
    <section class="media-viewer-card">
        @if ($hasReadAloud)
            <div
                class="read-aloud-panel"
                data-read-aloud
                data-read-aloud-selectors="{{ implode(', ', $readAloudSelectors) }}"
                data-read-aloud-ready="Use your browser voice to listen to this lesson."
                data-read-aloud-empty="Preview text is still loading or this file does not expose readable text here."
            >
                <div class="read-aloud-copy">
                    <strong>Read Aloud</strong>
                    <span data-read-aloud-status aria-live="polite">Use your browser voice to listen to this lesson.</span>
                </div>
                <div class="read-aloud-toolbar">
                    <label class="read-aloud-field">
                        <span>Voice</span>
                        <select data-read-aloud-voice></select>
                    </label>
                    <label class="read-aloud-field">
                        <span>Speed</span>
                        <select data-read-aloud-rate>
                            <option value="0.85">Slow</option>
                            <option value="1" selected>Normal</option>
                            <option value="1.15">Fast</option>
                            <option value="1.3">Faster</option>
                        </select>
                    </label>
                </div>
                <div class="read-aloud-buttons">
                    <button type="button" class="btn" data-read-aloud-play>Speak</button>
                    <button type="button" class="btn" data-read-aloud-pause disabled>Pause</button>
                    <button type="button" class="btn" data-read-aloud-resume disabled>Resume</button>
                    <button type="button" class="btn" data-read-aloud-stop disabled>Stop</button>
                </div>
            </div>
        @endif
        <div class="media-stack">
            @if ($isVideo)
                <video class="media-frame" controls controlsList="nodownload noplaybackrate" preload="metadata">
                    <source src="{{ $videoStream ?? $directUrl }}">
                    Your browser does not support secure video playback.
                </video>
            @elseif ($isPdf)
                <iframe class="media-frame doc-frame" src="{{ $streamUrl }}#toolbar=0&navpanes=0&scrollbar=1" title="Secure PDF Viewer"></iframe>
            @elseif (!empty($isDocx) && $isDocx)
                <div class="docx-renderer" data-docx-stream="{{ $streamUrl }}">
                    <div class="docx-renderer__status" data-docx-status>Loading DOCX preview...</div>
                </div>
            @elseif (!empty($isPptx) && $isPptx)
                <div class="pptx-renderer" data-pptx-stream="{{ $streamUrl }}">
                    <div class="pptx-renderer__status" data-pptx-status>Loading PPTX slides...</div>
                </div>
            @elseif ($isOfficeDoc)
                <div class="office-fallback">
                    <div class="office-fallback__box">
                        <h3>Inline preview is not available for this Office file</h3>
                        <p>
                            @if (!empty($isPpt) && $isPpt)
                                Old `.ppt` files cannot be rendered directly in this viewer. Use the buttons below or upload the presentation as `.pptx` or PDF for in-project preview.
                            @else
                                Google Docs is refusing the embedded connection in this viewer. Use the buttons below to open the file in Google Docs or open the original file directly.
                            @endif
                        </p>
                    </div>
                </div>
            @elseif (!empty($isImage) && $isImage)
                <img class="media-frame doc-frame" src="{{ $directUrl }}" alt="{{ $item->title }}">
            @elseif (!empty($isAudio) && $isAudio)
                <div class="media-frame audio-wrap">
                    <audio controls src="{{ $directUrl }}" style="width: 100%;"></audio>
                </div>
            @else
                <div class="media-frame doc-frame" style="display:flex;align-items:center;justify-content:center;color:#3b4a66;background:#f8fafc;">
                    Preview not supported for this file type.
                </div>
            @endif
            <div class="wm-layer" aria-hidden="true"></div>
        </div>
        @if (!empty($downloadUrl) && !empty($allowDownload))
            <div class="viewer-actions">
                <a class="btn" href="{{ $downloadUrl }}" target="_blank" rel="noopener">Download Original</a>
            </div>
        @elseif (!empty($isOfficeDoc) && $isOfficeDoc)
            <div class="viewer-note-inline">This file is view-only inside the project.</div>
        @endif
    </section>
    @if (!empty($isDocx) && $isDocx)
        <script src="{{ asset('js/jszip.min.js') }}" defer></script>
        <script src="{{ asset('js/docx-preview.min.js') }}" defer></script>
        <script src="{{ asset('js/mammoth.browser.min.js') }}" defer></script>
        <script src="{{ asset('js/secure-docx-viewer.js') }}" defer></script>
    @endif
    @if (!empty($isPptx) && $isPptx)
        <script src="{{ asset('js/jszip.min.js') }}"></script>
        <script type="module" src="{{ asset('js/secure-pptx-viewer.js') }}"></script>
    @endif
    @if ($hasReadAloud)
        <script src="{{ asset('js/secure-read-aloud.js') }}" defer></script>
    @endif
</body>
</html>
