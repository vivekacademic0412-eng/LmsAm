@php
    $studentStartY = 405 - ((max(count($certificate['student_name_lines']), 1) - 1) * 34);
    $courseStartY = 652 - ((max(count($certificate['course_title_lines']), 1) - 1) * 27);
    $brandWordmarkWidth = !empty($certificate['brand_logo_data_uri']) ? 138 : 0;
@endphp

<svg xmlns="http://www.w3.org/2000/svg" width="1600" height="1100" viewBox="0 0 1600 1100" role="img" aria-labelledby="certTitle certDesc">
    <title id="certTitle">Certificate of Completion</title>
    <desc id="certDesc">Certificate awarded to {{ $certificate['student_name'] }} for completing {{ $certificate['course_title'] }}.</desc>
    <defs>
        <linearGradient id="certBg" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" stop-color="#eaf4ff" />
            <stop offset="50%" stop-color="#fffefb" />
            <stop offset="100%" stop-color="#f6efe2" />
        </linearGradient>
        <linearGradient id="certPanel" x1="0%" y1="0%" x2="0%" y2="100%">
            <stop offset="0%" stop-color="#fffefb" />
            <stop offset="100%" stop-color="#fffaf0" />
        </linearGradient>
        <linearGradient id="certEdge" x1="0%" y1="0%" x2="100%" y2="0%">
            <stop offset="0%" stop-color="#0f4c9e" />
            <stop offset="42%" stop-color="#4c9fff" />
            <stop offset="100%" stop-color="#d5a441" />
        </linearGradient>
        <linearGradient id="certTopBand" x1="0%" y1="0%" x2="100%" y2="0%">
            <stop offset="0%" stop-color="#0b376f" />
            <stop offset="55%" stop-color="#0f4d9c" />
            <stop offset="100%" stop-color="#123a6d" />
        </linearGradient>
        <linearGradient id="certTopHighlight" x1="0%" y1="0%" x2="1%" y2="100%">
            <stop offset="0%" stop-color="rgba(255,255,255,0.3)" />
            <stop offset="100%" stop-color="rgba(255,255,255,0)" />
        </linearGradient>
        <linearGradient id="certSoftStroke" x1="0%" y1="0%" x2="0%" y2="100%">
            <stop offset="0%" stop-color="#d9e6f7" />
            <stop offset="100%" stop-color="#c5d4e8" />
        </linearGradient>
        <radialGradient id="certSeal" cx="50%" cy="50%" r="50%">
            <stop offset="0%" stop-color="#ffeab8" />
            <stop offset="100%" stop-color="#cc9228" />
        </radialGradient>
        <radialGradient id="certGlow" cx="50%" cy="50%" r="50%">
            <stop offset="0%" stop-color="rgba(84, 167, 255, 0.2)" />
            <stop offset="100%" stop-color="rgba(84, 167, 255, 0)" />
        </radialGradient>
        <radialGradient id="certGoldGlow" cx="50%" cy="50%" r="50%">
            <stop offset="0%" stop-color="rgba(240, 191, 98, 0.26)" />
            <stop offset="100%" stop-color="rgba(240, 191, 98, 0)" />
        </radialGradient>
        <filter id="panelShadow" x="-10%" y="-10%" width="120%" height="120%">
            <feDropShadow dx="0" dy="18" stdDeviation="20" flood-color="#173561" flood-opacity="0.1" />
        </filter>
        <filter id="softShadow" x="-20%" y="-20%" width="140%" height="140%">
            <feDropShadow dx="0" dy="8" stdDeviation="10" flood-color="#102849" flood-opacity="0.12" />
        </filter>
        <filter id="sealShadow" x="-20%" y="-20%" width="140%" height="140%">
            <feDropShadow dx="0" dy="10" stdDeviation="10" flood-color="#7b560e" flood-opacity="0.25" />
        </filter>
    </defs>

    <rect width="1600" height="1100" fill="url(#certBg)" />
    <rect x="0" y="0" width="1600" height="212" fill="url(#certTopBand)" />
    <path d="M0 140 C200 175 320 102 520 132 C690 158 810 228 1000 180 C1200 130 1380 96 1600 136 L1600 0 L0 0 Z" fill="url(#certTopHighlight)" />
    <circle cx="214" cy="96" r="210" fill="rgba(255,255,255,0.06)" />
    <circle cx="1398" cy="82" r="172" fill="rgba(255,255,255,0.045)" />
    <rect x="28" y="28" width="1544" height="1044" rx="34" fill="none" stroke="url(#certEdge)" stroke-width="10" />
    <rect x="54" y="54" width="1492" height="992" rx="28" fill="none" stroke="#d9e6f6" stroke-width="2" />
    <rect x="110" y="120" width="1380" height="860" rx="36" fill="url(#certPanel)" stroke="#d8e2f2" stroke-width="2" filter="url(#panelShadow)" />
    <rect x="136" y="146" width="1328" height="808" rx="28" fill="none" stroke="url(#certSoftStroke)" stroke-width="1.6" />
    <circle cx="800" cy="520" r="310" fill="url(#certGlow)" />
    <circle cx="800" cy="520" r="250" fill="url(#certGoldGlow)" opacity="0.45" />

    <g opacity="0.95">
        <path d="M192 206 h134" fill="none" stroke="#d0a54b" stroke-width="3" stroke-linecap="round" />
        <path d="M1274 206 h134" fill="none" stroke="#d0a54b" stroke-width="3" stroke-linecap="round" />
        <circle cx="348" cy="206" r="6" fill="#d0a54b" />
        <circle cx="1252" cy="206" r="6" fill="#d0a54b" />
    </g>

    <g opacity="0.24">
        <path d="M156 184 q28 0 28 28 v34 q0 28 28 28 h38" fill="none" stroke="#d8b36e" stroke-width="3" />
        <path d="M1444 184 q-28 0 -28 28 v34 q0 28 -28 28 h-38" fill="none" stroke="#d8b36e" stroke-width="3" />
        <path d="M156 918 q28 0 28 -28 v-34 q0 -28 28 -28 h38" fill="none" stroke="#d8b36e" stroke-width="3" />
        <path d="M1444 918 q-28 0 -28 -28 v-34 q0 -28 -28 -28 h-38" fill="none" stroke="#d8b36e" stroke-width="3" />
    </g>

    @if (!empty($certificate['brand_logo_data_uri']))
        <g opacity="0.06">
            <image href="{{ $certificate['brand_logo_data_uri'] }}" x="470" y="248" width="660" height="660" preserveAspectRatio="xMidYMid meet" />
        </g>
        <g transform="translate(800 146)" filter="url(#softShadow)">
            <circle cx="0" cy="0" r="90" fill="#fffdfa" stroke="#d0a54b" stroke-width="5" />
            <circle cx="0" cy="0" r="72" fill="rgba(245, 249, 255, 0.98)" stroke="#dce7f4" stroke-width="2" />
            <image href="{{ $certificate['brand_logo_data_uri'] }}" x="-{{ $brandWordmarkWidth / 2 }}" y="-31" width="{{ $brandWordmarkWidth }}" height="62" preserveAspectRatio="xMidYMid meet" />
        </g>
    @else
        <g transform="translate(800 146)" filter="url(#softShadow)">
            <circle cx="0" cy="0" r="90" fill="#fffdfa" stroke="#d0a54b" stroke-width="5" />
            <circle cx="0" cy="0" r="72" fill="rgba(245, 249, 255, 0.98)" stroke="#dce7f4" stroke-width="2" />
            <text x="0" y="8" text-anchor="middle" font-size="26" font-weight="700" font-family="Georgia, 'Times New Roman', serif" fill="#102849">AMS</text>
        </g>
    @endif

    <g transform="translate(800 242)">
        <rect x="-260" y="-16" width="520" height="34" rx="17" fill="#103f7f" />
        <text x="0" y="7" text-anchor="middle" font-size="15" font-weight="700" letter-spacing="3.5" font-family="Arial, sans-serif" fill="#f8fbff">{{ strtoupper($certificate['brand_name']) }}</text>
    </g>

    <text x="800" y="308" text-anchor="middle" font-size="22" font-weight="700" letter-spacing="8" font-family="Arial, sans-serif" fill="#7d8faa">CERTIFICATE OF ACHIEVEMENT</text>
    <text x="800" y="360" text-anchor="middle" font-size="52" font-family="Georgia, 'Times New Roman', serif" fill="#102849">Presented with pride to</text>

    <g transform="translate(800 {{ $studentStartY }})">
        @foreach ($certificate['student_name_lines'] as $index => $line)
            <text y="{{ $index * 68 }}" text-anchor="middle" font-size="64" font-weight="700" font-family="Georgia, 'Times New Roman', serif" fill="#0e3f86">{{ $line }}</text>
        @endforeach
    </g>

    <path d="M520 520 q140 34 280 34 q140 0 280 -34" fill="none" stroke="#d5dfee" stroke-width="2" />
    <circle cx="800" cy="520" r="7" fill="#d0a54b" />
    <text x="800" y="586" text-anchor="middle" font-size="30" font-family="Georgia, 'Times New Roman', serif" fill="#50657f">for successfully completing the course</text>

    <g transform="translate(800 {{ $courseStartY }})">
        @foreach ($certificate['course_title_lines'] as $index => $line)
            <text y="{{ $index * 54 }}" text-anchor="middle" font-size="44" font-weight="700" font-family="Arial, sans-serif" fill="#102849">{{ $line }}</text>
        @endforeach
    </g>

    <g transform="translate(208 742)">
        <rect x="0" y="0" width="346" height="102" rx="22" fill="#f5f9ff" stroke="#dbe5f4" stroke-width="2" />
        <text x="26" y="34" font-size="14" font-weight="700" letter-spacing="2.2" font-family="Arial, sans-serif" fill="#7b8ca6">SPECIALIZATION</text>
        <text x="26" y="72" font-size="28" font-weight="700" font-family="Georgia, 'Times New Roman', serif" fill="#102849">{{ $certificate['category'] }}</text>
    </g>

    <g transform="translate(628 742)">
        <rect x="0" y="0" width="346" height="102" rx="22" fill="#f5f9ff" stroke="#dbe5f4" stroke-width="2" />
        <text x="26" y="34" font-size="14" font-weight="700" letter-spacing="2.2" font-family="Arial, sans-serif" fill="#7b8ca6">GUIDED LEARNING</text>
        <text x="26" y="72" font-size="28" font-weight="700" font-family="Georgia, 'Times New Roman', serif" fill="#102849">{{ $certificate['hours_total'] }} Hours</text>
    </g>

    <g transform="translate(1048 742)">
        <rect x="0" y="0" width="346" height="102" rx="22" fill="#f5f9ff" stroke="#dbe5f4" stroke-width="2" />
        <text x="26" y="34" font-size="14" font-weight="700" letter-spacing="2.2" font-family="Arial, sans-serif" fill="#7b8ca6">PROGRAM MENTOR</text>
        <text x="26" y="72" font-size="28" font-weight="700" font-family="Georgia, 'Times New Roman', serif" fill="#102849">{{ $certificate['trainer_name'] }}</text>
    </g>

    <g transform="translate(188 900)">
        <text x="0" y="0" font-size="18" font-family="Arial, sans-serif" fill="#6b7d95">Issued On</text>
        <text x="0" y="38" font-size="30" font-weight="700" font-family="Georgia, 'Times New Roman', serif" fill="#102849">{{ $certificate['issued_at_full'] }}</text>
        <line x1="0" y1="94" x2="250" y2="94" stroke="#c8d7ea" stroke-width="2" />
        <text x="0" y="126" font-size="16" font-family="Arial, sans-serif" fill="#6b7d95">Completion date captured from final course progress.</text>
    </g>

    <g transform="translate(1160 900)">
        <text x="240" y="0" text-anchor="end" font-size="18" font-family="Arial, sans-serif" fill="#6b7d95">Certificate Code</text>
        <text x="240" y="38" text-anchor="end" font-size="28" font-weight="700" font-family="Arial, sans-serif" fill="#102849">{{ $certificate['certificate_code'] }}</text>
        <line x1="0" y1="94" x2="240" y2="94" stroke="#c8d7ea" stroke-width="2" />
        <text x="240" y="126" text-anchor="end" font-size="16" font-family="Arial, sans-serif" fill="#6b7d95">Verified certificate generated by {{ $certificate['brand_name'] }}.</text>
    </g>

    <g transform="translate(800 924)" filter="url(#sealShadow)">
        <polygon points="0,-84 18,-60 48,-70 52,-38 82,-24 66,4 84,30 54,40 52,72 20,62 0,88 -20,62 -52,72 -54,40 -84,30 -66,4 -82,-24 -52,-38 -48,-70 -18,-60" fill="#f5d07a" opacity="0.9" />
        <circle cx="0" cy="0" r="62" fill="url(#certSeal)" stroke="#b98220" stroke-width="4" />
        <circle cx="0" cy="0" r="44" fill="rgba(255,255,255,0.2)" stroke="rgba(255,255,255,0.55)" stroke-width="2" />
        <text x="0" y="-8" text-anchor="middle" font-size="16" font-weight="700" font-family="Arial, sans-serif" fill="#6b4200">CERTIFIED</text>
        <text x="0" y="18" text-anchor="middle" font-size="14" font-weight="700" font-family="Arial, sans-serif" fill="#6b4200">{{ $certificate['issued_at']->format('Y') }}</text>
        <text x="0" y="38" text-anchor="middle" font-size="11" font-weight="700" letter-spacing="2" font-family="Arial, sans-serif" fill="#6b4200">OFFICIAL</text>
    </g>

    <g transform="translate(470 1010)">
        <path d="M0 0 q118 -34 236 0" fill="none" stroke="#7a8da8" stroke-width="2.4" />
        <text x="118" y="34" text-anchor="middle" font-size="18" font-family="Arial, sans-serif" fill="#5c6f86">Program Mentor</text>
    </g>

    <g transform="translate(1130 1010)">
        <path d="M0 0 q118 -34 236 0" fill="none" stroke="#7a8da8" stroke-width="2.4" />
        <text x="118" y="34" text-anchor="middle" font-size="18" font-family="Arial, sans-serif" fill="#5c6f86">{{ $certificate['brand_name'] }} Authorized Signature</text>
    </g>

    <text x="800" y="1060" text-anchor="middle" font-size="14" font-family="Arial, sans-serif" fill="#7b8ca5">
        This certificate recognizes the successful completion of the listed course requirements under {{ $certificate['brand_name'] }}.
    </text>
</svg>
