<div>
    <style>
        /* =========================================================
   Demo Users Admin Dashboard — v2
   Built on the app's existing design tokens
   (--primary-dark, --bg-card, --line, --radius, --shadow-card, …)
========================================================= */

/* =========================================================
   STAT CARDS
========================================================= */

.d-stats-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 16px;
}

.d-stat {
    background: var(--bg-card);
    border: 1px solid var(--line);
    border-radius: var(--radius-sm);
    padding: 20px 22px;
    display: flex;
    align-items: center;
    gap: 16px;
    box-shadow: var(--shadow-sm);
    transition: box-shadow .2s ease, transform .2s ease, border-color .2s ease;
    position: relative;
    overflow: hidden;
}

.d-stat::before {
    content: "";
    position: absolute;
    inset: 0 auto 0 0;
    width: 3px;
    background: var(--primary-dark);
}

.d-stat:nth-child(2)::before { background: var(--success); }
.d-stat:nth-child(3)::before { background: var(--info); }
.d-stat:nth-child(4)::before { background: var(--accent); }

.d-stat:hover {
    box-shadow: var(--shadow-card);
    transform: translateY(-2px);
    border-color: transparent;
}

.d-stat-icon {
    flex: 0 0 auto;
    width: 44px;
    height: 44px;
    border-radius: var(--radius-xs);
    display: flex;
    align-items: center;
    justify-content: center;
    background: color-mix(in srgb, var(--primary-dark) 12%, transparent);
    color: var(--primary-dark);
    font-size: 18px;
}

.d-stat:nth-child(2) .d-stat-icon { background: color-mix(in srgb, var(--success) 14%, transparent); color: var(--success); }
.d-stat:nth-child(3) .d-stat-icon { background: color-mix(in srgb, var(--info) 14%, transparent); color: var(--info); }
.d-stat:nth-child(4) .d-stat-icon { background: color-mix(in srgb, var(--accent) 20%, transparent); color: var(--accent); }

.d-stat .stat-value { font-size: 26px; font-weight: 700; line-height: 1.1; color: var(--text); letter-spacing: -0.02em; }
.d-stat .stat-label { font-size: 12.5px; font-weight: 500; color: var(--text-muted); text-transform: uppercase; letter-spacing: .04em; margin-top: 2px; }

@media (max-width: 992px) { .d-stats-grid { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 560px) { .d-stats-grid { grid-template-columns: 1fr; } }

/* =========================================================
   FILTER BAR
========================================================= */

.filter-card { background: var(--bg-card); border: 1px solid var(--line); border-radius: var(--radius-sm); padding: 16px; box-shadow: var(--shadow-sm); }
.filter-grid { display: grid; grid-template-columns: 2fr 1fr 1fr 1fr; gap: 12px; }

@media (max-width: 900px) { .filter-grid { grid-template-columns: 1fr 1fr; } }
@media (max-width: 560px) { .filter-grid { grid-template-columns: 1fr; } }

/* =========================================================
   FORM CONTROLS
========================================================= */

.theme-form-control {
    width: 100%;
    border: 1.5px solid var(--input-border);
    border-radius: var(--radius-xs);
    padding: 10px 13px;
    font-size: 14px;
    font-family: inherit;
    color: var(--text);
    background: var(--input-bg);
    transition: border-color .15s ease, box-shadow .15s ease, background .15s ease;
    appearance: none;
}

select.theme-form-control {
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6' viewBox='0 0 10 6'%3E%3Cpath fill='%235a718a' d='M0 0l5 6 5-6z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 14px center;
    padding-right: 32px;
}

.theme-form-control::placeholder { color: var(--text-muted); }
.theme-form-control:hover { border-color: var(--input-focus); }
.theme-form-control:focus {
    outline: none;
    border-color: var(--input-focus);
    background: var(--bg-card);
    box-shadow: 0 0 0 4px color-mix(in srgb, var(--input-focus) 18%, transparent);
}

/* =========================================================
   CARD / TABLE
========================================================= */

.card { background: var(--bg-card); border: 1px solid var(--line); border-radius: var(--radius-sm); box-shadow: var(--shadow-card); overflow: hidden; }
.table-responsive { overflow-x: auto; }
.table { width: 100%; border-collapse: collapse; font-size: 13.5px; color: var(--text); }

.table thead th {
    background: var(--bg-card2);
    border-bottom: 1px solid var(--line);
    color: var(--text-muted);
    font-weight: 600;
    font-size: 11.5px;
    text-transform: uppercase;
    letter-spacing: .05em;
    text-align: left;
    padding: 12px 14px;
    white-space: nowrap;
}

.table tbody td { padding: 14px; border-bottom: 1px solid var(--line); vertical-align: middle; }
.table tbody tr:last-child td { border-bottom: none; }
.table tbody tr { transition: background .12s ease; }
.table tbody tr:hover { background: var(--bg-card2); }
.table tbody td strong { color: var(--text); font-weight: 600; }
.table small.text-muted, .table .text-muted { color: var(--text-muted) !important; font-size: 12px; }

/* =========================================================
   USER CELL — avatar chip + name/contact stack
========================================================= */

.user-cell { display: flex; align-items: center; gap: 10px; }

.user-avatar {
    flex: 0 0 auto;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: color-mix(in srgb, var(--primary-dark) 14%, transparent);
    color: var(--primary-dark);
    font-size: 12.5px;
    font-weight: 700;
    letter-spacing: .02em;
    text-transform: uppercase;
}

.user-meta { min-width: 0; }
.user-meta strong { display: block; }
.user-meta small { display: block; }

/* =========================================================
   BADGES
========================================================= */

.badge { display: inline-flex; align-items: center; gap: 5px; font-size: 11.5px; font-weight: 600; letter-spacing: .01em; padding: 4px 9px; border-radius: 999px; line-height: 1.4; white-space: nowrap; }

.badge.bg-primary   { background: color-mix(in srgb, var(--primary-dark) 14%, transparent); color: var(--primary-dark); }
.badge.bg-success   { background: color-mix(in srgb, var(--success) 14%, transparent); color: var(--success); }
.badge.bg-info      { background: color-mix(in srgb, var(--info) 14%, transparent); color: var(--info); }
.badge.bg-warning   { background: color-mix(in srgb, var(--warning) 16%, transparent); color: var(--warning); }
.badge.bg-secondary { background: color-mix(in srgb, var(--text-muted) 16%, transparent); color: var(--text-muted); }
.badge.bg-dark      { background: var(--text); color: var(--bg-card); }
.badge.text-dark     { color: var(--warning); }

.badge.bg-success-subtle   { background: color-mix(in srgb, var(--success) 12%, transparent); }
.badge.text-success        { color: var(--success) !important; }
.badge.bg-warning-subtle   { background: color-mix(in srgb, var(--warning) 14%, transparent); }
.badge.text-warning        { color: var(--warning) !important; }
.badge.bg-secondary-subtle { background: color-mix(in srgb, var(--text-muted) 12%, transparent); }
.badge.text-secondary      { color: var(--text-muted) !important; }

/* =========================================================
   DEMO LINK CHIP — the new bit
========================================================= */

.link-cell { min-width: 200px; }

.link-chip {
    display: flex;
    align-items: center;
    gap: 8px;
    background: var(--bg-card2);
    border: 1px solid var(--line);
    border-radius: var(--radius-xs);
    padding: 6px 8px 6px 10px;
    max-width: 220px;
}

.link-chip code {
    flex: 1 1 auto;
    min-width: 0;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    font-size: 11.5px;
    color: var(--text-muted);
    font-family: ui-monospace, SFMono-Regular, Menlo, monospace;
}

.link-copy-btn {
    flex: 0 0 auto;
    width: 26px;
    height: 26px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 6px;
    border: none;
    background: transparent;
    color: var(--primary-dark);
    cursor: pointer;
    font-size: 12px;
    transition: background .15s ease;
}

.link-copy-btn:hover { background: color-mix(in srgb, var(--primary-dark) 14%, transparent); }
.link-copy-btn.copied { color: var(--success); }

.link-meta-row { display: flex; align-items: center; gap: 6px; margin-top: 6px; flex-wrap: wrap; }
.link-empty { font-size: 12px; color: var(--text-muted); font-style: italic; }

/* =========================================================
   BUTTONS
========================================================= */

.btn { display: inline-flex; align-items: center; gap: 7px; border: 1px solid transparent; border-radius: var(--radius-xs); font-size: 13px; font-weight: 600; padding: 8px 15px; cursor: pointer; transition: background .15s ease, box-shadow .15s ease, transform .05s ease, filter .15s ease; }
.btn:active { transform: translateY(1px); }
.btn-sm { padding: 7px 13px; font-size: 12.5px; }

.btn-success { background: var(--success); color: #fff; box-shadow: 0 1px 2px color-mix(in srgb, var(--success) 40%, transparent); }
.btn-success:hover { filter: brightness(0.92); }

.btn-primary {
    background: linear-gradient(135deg, var(--primary-dark), color-mix(in srgb, var(--primary-dark) 70%, var(--accent2)));
    color: #fff;
    box-shadow: 0 2px 10px color-mix(in srgb, var(--primary-dark) 35%, transparent);
}
.btn-primary:hover { box-shadow: 0 4px 16px color-mix(in srgb, var(--primary-dark) 45%, transparent); filter: brightness(1.05); }
.btn-primary i { font-size: 12px; }

.btn-secondary { background: color-mix(in srgb, var(--text-muted) 14%, transparent); color: var(--text-muted); }

.btn:disabled, .btn-secondary:disabled { cursor: not-allowed; opacity: .65; filter: none; box-shadow: none; }

/* Icon-only action button (Regenerate) */
.btn-icon {
    width: 32px;
    height: 32px;
    padding: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: var(--radius-xs);
    background: color-mix(in srgb, var(--info) 12%, transparent);
    color: var(--info);
    border: 1px solid transparent;
    cursor: pointer;
    font-size: 13px;
    transition: background .15s ease, transform .5s ease;
}
.btn-icon:hover { background: color-mix(in srgb, var(--info) 20%, transparent); }
.btn-icon:active i { transform: rotate(180deg); }
.btn-icon:disabled { opacity: .5; cursor: not-allowed; }

.action-group { display: flex; gap: 6px; flex-wrap: wrap; align-items: center; }

/* =========================================================
   EMPTY STATE
========================================================= */

.empty-state { display: flex; flex-direction: column; align-items: center; gap: 4px; color: var(--text-muted); }
.empty-state .empty-icon { font-size: 40px; margin-bottom: 6px; filter: grayscale(.3); }
.empty-state h5 { color: var(--text); font-weight: 600; font-size: 15px; margin: 0; }
.empty-state p { font-size: 13px; margin: 0; }

/* =========================================================
   UTILITIES
========================================================= */

.d-flex { display: flex; }
.flex-column { flex-direction: column; }
.flex-wrap { flex-wrap: wrap; }
.align-self-start { align-self: flex-start; }
.gap-1 { gap: 4px; }
.gap-2 { gap: 8px; }
.mb-4 { margin-bottom: 20px; }
.mt-4 { margin-top: 20px; }
.py-5 { padding-top: 48px; padding-bottom: 48px; }
.text-center { text-align: center; }

.btn:focus-visible, .theme-form-control:focus-visible, .btn-icon:focus-visible, .link-copy-btn:focus-visible {
    outline: 2px solid var(--input-focus);
    outline-offset: 2px;
}

@media (prefers-reduced-motion: reduce) { .d-stat, .btn, .table tbody tr, .btn-icon { transition: none; } }
    </style>


    {{-- =======================
    TOP STATS
======================== --}}
    <div class="d-stats-grid mb-4">
        <div class="d-stat">
            <div class="d-stat-icon"><i class="fa-solid fa-users"></i></div>
            <div>
                <div class="stat-value">{{ $totalUsers }}</div>
                <div class="stat-label">Total Users</div>
            </div>
        </div>
        <div class="d-stat">
            <div class="d-stat-icon"><i class="fa-solid fa-circle-check"></i></div>
            <div>
                <div class="stat-value">{{ $completedUsers }}</div>
                <div class="stat-label">Completed Users</div>
            </div>
        </div>
        <div class="d-stat">
            <div class="d-stat-icon"><i class="fa-solid fa-video"></i></div>
            <div>
                <div class="stat-value">{{ $totalDemos }}</div>
                <div class="stat-label">Demo Uploads</div>
            </div>
        </div>
        <div class="d-stat">
            <div class="d-stat-icon"><i class="fa-solid fa-book-open"></i></div>
            <div>
                <div class="stat-value">{{ $totalCourses }}</div>
                <div class="stat-label">Courses</div>
            </div>
        </div>
    </div>

    {{-- =======================
    FILTERS
======================== --}}
    <div class="filter-card mb-4">
        <div class="filter-grid">
            <div>
                <input type="text" wire:model.live.debounce.500ms="search" class="theme-form-control form-control"
                    placeholder="🔍 Search Name, Email, Phone">
            </div>
            <div>
                <select wire:model.live="educationLevel" class="theme-form-control form-control">
                    <option value="">All Education Levels</option>
                    @foreach ($educationLevels as $level)
                        <option value="{{ $level->id }}">{{ $level->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <select wire:model.live="courseFilter" class="theme-form-control form-control">
                    <option value="">All Courses</option>
                    @foreach ($courses as $course)
                        <option value="{{ $course->id }}">{{ $course->title }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <select wire:model.live="progressFilter" class="theme-form-control form-control">
                    <option value="">All Progress</option>
                    <option value="25">0 - 25%</option>
                    <option value="50">25 - 50%</option>
                    <option value="75">50 - 75%</option>
                    <option value="100">Completed</option>
                </select>
            </div>
        </div>
    </div>

    {{-- =======================
    TABLE
======================== --}}
    <section class="card">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>User</th>
                        <th>Course / Education</th>
                        <th style="min-width:230px;">Payment Details</th>
                        <th>Demo Status</th>
                        <th class="link-cell">Demo Link</th>
                        <th>Source</th>
                        <th>Created</th>
                        <th width="200">Action</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($demoUsers as $index => $demo)
                        @php
                            $selection     = $demo->paymentType;
                            $payment       = $selection?->payment;
                            $paymentType   = $selection?->demo_type;
                            $paymentStatus = $selection?->status;
                            $isConfirm     = $selection?->status=='completed' ? 2 : ($selection?->status=='pending' ? 1 : 0);

                            $source      = $selection?->trafficSource?->source;
                            $landingPage = $selection?->trafficSource?->landing_page;

                            $demoRecord = $demo->demo; // App\Models\DemoUser
                            $isCompleted = $demoRecord
                                && (
                                    ($demoRecord->progress_demo ?? 0) >= 100
                                    || $demoRecord->submittedDemos->isNotEmpty()
                                );

                            $isFree = $paymentType === 'free';
                            $isPaymentConfirmed = $paymentStatus === 'completed';
                            $canActivate = !$isCompleted && $isConfirm != 2 && ($isFree || $isPaymentConfirmed);
                            $canSendMail = !$isCompleted;
                            $isActivated = $isConfirm == 2;

                            $latestToken = $latestTokens->get($demo->id);
                            $tokenUrl = $latestToken ? route('demo.secure.login', $latestToken->token) : null;
                            $tokenExpired = $latestToken?->expires_at && $latestToken->expires_at->isPast();

                            $initials = collect(preg_split('/\s+/', trim($demo->full_name ?? $demo->name ?? '')))
                                ->map(fn($w) => mb_strtoupper(mb_substr($w, 0, 1)))
                                ->take(2)
                                ->implode('');
                        @endphp

                        <tr>
                            <td>{{ $index + 1 }}</td>

                            {{-- USER --}}
                            <td>
                                <div class="user-cell">
                                    <div class="user-avatar">{{ $initials ?: '?' }}</div>
                                    <div class="user-meta">
                                        <strong>{{ $demo->full_name ?? $demo->name }}</strong>
                                        <small class="text-muted">{{ $demo->email_phone ?? $demo->email }}</small>
                                        <small class="text-muted">IP: {{ $selection?->trafficSource?->user_ip ?? '-' }}</small>
                                    </div>
                                </div>
                            </td>

                            {{-- COURSE / EDUCATION --}}
                            <td>
                                <div>{{ $demoRecord?->course?->title ?? '-' }}</div>
                                <small class="text-muted">{{ $demoRecord?->educationLevel?->name ?? '-' }}</small>
                            </td>

                            {{-- PAYMENT DETAILS BLOCK --}}
                            <td>
                                <div class="d-flex flex-column gap-1">
                                    @if ($paymentType == 'free')
                                        <span class="badge bg-success align-self-start">Free Demo</span>
                                    @elseif($paymentType == 'paid_qr')
                                        <span class="badge bg-warning text-dark align-self-start">QR Payment</span>
                                    @elseif($paymentType == 'online')
                                        <span class="badge bg-primary align-self-start">Online Payment</span>
                                    @elseif($paymentType == 'invoice')
                                        <span class="badge bg-info align-self-start">Invoice</span>
                                    @else
                                        <span class="badge bg-secondary align-self-start">N/A</span>
                                    @endif

                                    @if ($isFree)
                                        <small class="text-muted">No payment required</small>
                                    @elseif ($isPaymentConfirmed)
                                        <span class="badge bg-success-subtle text-success align-self-start">
                                            <i class="fa-solid fa-check"></i> Payment Confirmed
                                        </span>
                                    @elseif($paymentStatus == 'pending')
                                        <span class="badge bg-warning-subtle text-warning align-self-start">
                                            <i class="fa-solid fa-hourglass-half"></i> Payment Pending
                                        </span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary align-self-start">
                                            No Payment Found
                                        </span>
                                    @endif

                                    @if ($payment)
                                        <small class="text-muted">
                                            ₹{{ number_format($payment->paid_amount ?? $payment->amount, 2) }}
                                            via {{ ucfirst($payment->gateway ?? '-') }}
                                        </small>
                                        <small class="text-muted">
                                            Txn: {{ $payment->transaction_id ?? $payment->razorpay_payment_id ?? '-' }}
                                        </small>
                                        @if ($payment->paid_at)
                                            <small class="text-muted">
                                                Paid: {{ $payment->paid_at->format('d M Y, h:i A') }}
                                            </small>
                                        @endif
                                    @endif
                                </div>
                            </td>

                            {{-- DEMO LIFECYCLE STATUS --}}
                            <td>
                                @if ($isCompleted)
                                    <span class="badge bg-dark">
                                        <i class="fa-solid fa-flag-checkered"></i> Completed
                                    </span>
                               @elseif ($isActivated && $selection?->mail_sent_at)
    <span class="badge bg-info text-dark">
        <i class="fa-solid fa-paper-plane"></i> Link Sent
    </span>
    <br>
    <small class="text-muted">
        {{ \Carbon\Carbon::parse($selection->mail_sent_at)->diffForHumans() }}
    </small>

                                @elseif ($isActivated)
                                    <span class="badge bg-primary">
                                        <i class="fa-solid fa-unlock"></i> Activated
                                    </span>
                                @elseif ($isFree || $isPaymentConfirmed)
                                    <span class="badge bg-warning text-dark">Ready to Activate</span>
                                @else
                                    <span class="badge bg-secondary">Awaiting Payment</span>
                                @endif
                            </td>

                            {{-- DEMO LINK --}}
                            <td class="link-cell">
                                @if ($tokenUrl)
                                    <div class="link-chip">
                                        <code title="{{ $tokenUrl }}">{{ $tokenUrl }}</code>
                                        <button type="button" class="link-copy-btn" title="Copy link"
                                            onclick="copyDemoLink(this, '{{ $tokenUrl }}')">
                                            <i class="fa-regular fa-copy"></i>
                                        </button>
                                    </div>
                                    <div class="link-meta-row">
                                        @if ($latestToken->used)
                                            <span class="badge bg-secondary-subtle text-secondary">Used</span>
                                        @elseif ($tokenExpired)
                                            <span class="badge bg-warning-subtle text-warning">Expired</span>
                                        @else
                                            <span class="badge bg-success-subtle text-success">Active</span>
                                        @endif
                                    </div>
                                @else
                                    <span class="link-empty">No link generated yet</span>
                                @endif
                            </td>

                            {{-- SOURCE --}}
                            <td>
                                <span class="badge bg-secondary">{{ ucfirst($source ?? 'Direct') }}</span>
                                <br>
                                <small class="text-muted">{{ Str::limit($landingPage, 30) }}</small>
                            </td>

                            {{-- CREATED --}}
                            <td>
                                {{ $demo->created_at->format('d M Y') }}
                                <br>
                                <small>{{ $demo->created_at->format('h:i A') }}</small>
                            </td>

                            {{-- ACTION --}}
                            <td>
                                <div class="action-group">
                                    @if ($isCompleted)
                                        <span class="text-muted small">No action — demo completed</span>
                                    @else
                                        @if (!$isFree)
                                            @if ($canActivate)
                                                <button wire:click="activateUser({{ $demo->id }})"
                                                    wire:confirm="Payment looks confirmed for this user. Activate demo access?"
                                                    class="btn btn-success btn-sm" title="Activate">
                                                    Activate
                                                </button>
                                            @elseif (!$isActivated)
                                                <button class="btn btn-secondary btn-sm" disabled
                                                    title="Payment not confirmed yet">
                                                    Activate
                                                </button>
                                            @endif
                                        @endif
                                        @if($isFree || $isPaymentConfirmed)
                                             <button wire:click="sendLoginMail({{ $demo->id }})"
                                                wire:confirm="Send a fresh one-time demo link? Any earlier unused link for this user will stop working."
                                                class="btn btn-primary btn-sm" title="Email the demo link"
                                               >
                                                {{ $selection?->mail_sent_at ? 'Resend' : 'Send Mail' }}
                                            </button>

                                            <button wire:click="regenerateLink({{$demo->id }})"
                                                wire:confirm="Generate a new link for this user? Any earlier unused link will stop working."
                                                class="btn-icon" title="Regenerate link (copies to clipboard, no email sent)"
                                               >
                                                <i class="fa-solid fa-rotate"></i>
                                            </button>
                                        @else
                                        @if ($canSendMail)
                                            <button wire:click="sendLoginMail({{ $demo->id }})"
                                                wire:confirm="Send a fresh one-time demo link? Any earlier unused link for this user will stop working."
                                                class="btn btn-primary btn-sm" title="Email the demo link"
                                                @if(!$isActivated) disabled @endif>
                                                {{ $selection?->mail_sent_at ? 'Resend' : 'Send Mail' }}
                                            </button>

                                            <button wire:click="regenerateLink({{$demo->id }})"
                                                wire:confirm="Generate a new link for this user? Any earlier unused link will stop working."
                                                class="btn-icon" title="Regenerate link (copies to clipboard, no email sent)"
                                                @if(!$isActivated) disabled @endif>
                                                <i class="fa-solid fa-rotate"></i>
                                            </button>
                                        @endif
                                            @endif
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5">
                                <div class="empty-state">
                                    <div class="empty-icon">📭</div>
                                    <h5>No Demo Users Found</h5>
                                    <p>No records match your filters.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    {{-- =======================
    PAGINATION
======================== --}}
    <div class="mt-4">
        {{ $demoUsers->links('pagination.custom') }}
    </div>
</div>

<script>
document.addEventListener('livewire:init', () => {

    Livewire.on('success', (event) => {
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: event.message,
            confirmButtonColor: '#0947a8',
            timer: 3000,
            timerProgressBar: true,
            showConfirmButton: false
        });
    });

    Livewire.on('error', (event) => {
        Swal.fire({
            icon: 'error',
            title: 'Cannot proceed',
            text: event.message,
            confirmButtonColor: '#0947a8'
        });
    });

    // Fired by regenerateLink() on the server — auto-copy the fresh link.
    Livewire.on('link-generated', (event) => {
        copyToClipboard(event.url);
    });

});

/**
 * Copy a demo link from the table's copy icon button. Gives that
 * specific button a brief "copied" state so the admin gets feedback
 * right where they clicked, without a popup for something this small.
 */
function copyDemoLink(button, url) {
    copyToClipboard(url).then(() => {
        const icon = button.querySelector('i');
        button.classList.add('copied');
        icon.classList.remove('fa-copy');
        icon.classList.add('fa-check');

        setTimeout(() => {
            button.classList.remove('copied');
            icon.classList.remove('fa-check');
            icon.classList.add('fa-copy');
        }, 1500);
    });
}

function copyToClipboard(text) {
    if (navigator.clipboard && window.isSecureContext) {
        return navigator.clipboard.writeText(text);
    }
    // Fallback for non-HTTPS/local environments
    return new Promise((resolve) => {
        const el = document.createElement('textarea');
        el.value = text;
        el.style.position = 'fixed';
        el.style.opacity = '0';
        document.body.appendChild(el);
        el.select();
        document.execCommand('copy');
        document.body.removeChild(el);
        resolve();
    });
}
</script>