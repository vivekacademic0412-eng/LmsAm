@extends('layouts.app')

@section('content')
    <style>
        .media-viewer-card {
            border: 1px solid var(--line-soft);
            border-radius: 14px;
            background: linear-gradient(180deg, rgba(20, 95, 209, 0.03), rgba(20, 95, 209, 0));
            padding: 14px;
        }
        .media-frame {
            width: 100%;
            min-height: 72vh;
            border: 1px solid var(--line-soft);
            border-radius: 12px;
            background: #000;
        }
        .doc-frame {
            background: #fff;
        }
    </style>

    <div class="stack">
        <section class="card">
            <div class="page-head">
                <div>
                    <h1>Secure Media Viewer</h1>
                    <p>{{ $item->title }}</p>
                </div>
            </div>
        </section>

        <section class="media-viewer-card">
            @if ($isVideo)
                <video class="media-frame" controls controlsList="nodownload noplaybackrate" preload="metadata">
                    <source src="{{ $directUrl }}">
                    Your browser does not support secure video playback.
                </video>
            @elseif ($isPdf)
                <iframe class="media-frame doc-frame" src="{{ $directUrl }}" title="Secure PDF Viewer"></iframe>
            @else
                <iframe class="media-frame doc-frame" src="{{ $directUrl }}" title="Secure Document Viewer"></iframe>
                <p class="muted mt-4">
                    If this file cannot be previewed by your browser, use
                    <a class="secure-link" href="{{ $directUrl }}">secure download</a>.
                </p>
            @endif
        </section>
    </div>
@endsection
