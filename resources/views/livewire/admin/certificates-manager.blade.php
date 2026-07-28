<div class="cc-page">

    {{-- Flash toast --}}
    <div
        x-data="{ show: false, msg: '', tone: 'success' }"
        x-on:certificate-approved.window="show = true; msg = 'Certificate approved and unlocked'; tone = 'success'; setTimeout(() => show = false, 2500)"
        x-on:certificate-rejected.window="show = true; msg = 'Certificate rejected'; tone = 'danger'; setTimeout(() => show = false, 2500)"
        x-on:certificate-revoked.window="show = true; msg = 'Certificate revoked'; tone = 'danger'; setTimeout(() => show = false, 2500)"
        x-on:certificate-issued.window="show = true; msg = 'Certificate issued'; tone = 'success'; setTimeout(() => show = false, 2500)"
        x-show="show" x-cloak
        x-transition:enter="cc-toast-enter"
        x-transition:leave="cc-toast-leave"
        class="cc-toast"
        :class="tone === 'danger' ? 'cc-toast-danger' : 'cc-toast-success'"
    >
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" x-show="tone === 'success'">
            <path d="M20 6L9 17l-5-5" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" x-show="tone === 'danger'">
            <path d="M18 6L6 18M6 6l12 12" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        <span x-text="msg"></span>
    </div>

    {{-- Page head --}}
    <div class="cc-page-head">
        <div>
            <h1 class="cc-title">Certificates</h1>
            <p class="cc-subtitle">Review auto-unlocked certificates and approve manual submissions.</p>
        </div>
        <button class="btn btn-primary cc-issue-btn" wire:click="openIssueModal">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"/></svg>
            Issue manual certificate
        </button>
    </div>

    {{-- Stats --}}
    <div class="cc-stat-row">
        <div class="cc-stat-card">
            <div class="cc-stat-icon cc-stat-icon--total">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M9 12l2 2 4-4M7.8 4.2h8.4a2 2 0 012 2v13.4l-6.2-3.2-6.2 3.2V6.2a2 2 0 012-2z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </div>
            <div>
                <div class="cc-stat-val">{{ $this->stats['total'] }}</div>
                <div class="cc-stat-lbl">Total certificates</div>
            </div>
        </div>
        <div class="cc-stat-card">
            <div class="cc-stat-icon cc-stat-icon--pending">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.8"/><path d="M12 7v5l3.5 2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
            </div>
            <div>
                <div class="cc-stat-val">{{ $this->stats['pending'] }}</div>
                <div class="cc-stat-lbl">Pending admin approval</div>
            </div>
        </div>
        <div class="cc-stat-card">
            <div class="cc-stat-icon cc-stat-icon--unlocked">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><rect x="5" y="11" width="14" height="9" rx="2" stroke="currentColor" stroke-width="1.8"/><path d="M8 11V7a4 4 0 018 0" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
            </div>
            <div>
                <div class="cc-stat-val">{{ $this->stats['unlocked'] }}</div>
                <div class="cc-stat-lbl">Unlocked</div>
            </div>
        </div>
        <div class="cc-stat-card">
            <div class="cc-stat-icon cc-stat-icon--locked">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><rect x="5" y="11" width="14" height="9" rx="2" stroke="currentColor" stroke-width="1.8"/><path d="M8 11V8a4 4 0 118 0v3" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
            </div>
            <div>
                <div class="cc-stat-val">{{ $this->stats['locked'] }}</div>
                <div class="cc-stat-lbl">Locked (in progress)</div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="cc-filter-bar">
        <div class="cc-search-wrap">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" class="cc-search-icon"><circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="1.8"/><path d="M21 21l-4.3-4.3" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
            <input type="text" wire:model.live.debounce.400ms="search" placeholder="Search by student name or email…">
        </div>
        <select wire:model.live="typeFilter" class="cc-select">
            <option value="">All types</option>
            <option value="course">Course</option>
            <option value="week">Week</option>
            <option value="level">Level</option>
            <option value="demo">Demo</option>
        </select>
        <select wire:model.live="statusFilter" class="cc-select">
            <option value="">All statuses</option>
            <option value="pending_admin_approval">Pending approval</option>
            <option value="unlocked">Unlocked</option>
            <option value="locked">Locked</option>
        </select>
        <button class="btn btn-ghost btn-sm" wire:click="$set('search', '')">Reset</button>
    </div>

    {{-- Table --}}
    <div class="cc-table-card">
        <table class="cc-table">
            <thead>
                <tr>
                    <th>Student</th>
                    <th>Subject</th>
                    <th>Type</th>
                    <th>Completion</th>
                    <th>Status</th>
                    <th>Issued</th>
                    <th class="cc-th-actions">Actions</th>
                </tr>
            </thead>
            <tbody wire:loading.class="cc-loading">
                @forelse ($this->certificates as $cert)
                    <tr wire:key="cert-{{ $cert->id }}">
                        <td>
                            <div class="cc-cell-user">
                                <div class="cc-avatar">{{ strtoupper(substr($cert->user->name ?? '?', 0, 2)) }}</div>
                                <div>
                                    <div class="cc-name">{{ $cert->user->name }}</div>
                                    <div class="cc-email">{{ $cert->user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="cc-subject">{{ $cert->subjectTitle() }}</td>
                        <td><span class="cc-badge cc-badge-type-{{ $cert->type }}">{{ ucfirst($cert->type) }}</span></td>
                        <td>
                            <div class="cc-progress">
                                <div class="cc-progress-track"><span style="width: {{ $cert->completion_percent ?? 0 }}%"></span></div>
                                <div class="cc-progress-pct">{{ $cert->completion_percent ?? 0 }}%</div>
                            </div>
                        </td>
                        <td>
                            @if ($cert->status === 'unlocked')
                                <span class="cc-badge cc-badge-status-unlocked"><i></i>Unlocked</span>
                            @elseif ($cert->status === 'pending_admin_approval')
                                <span class="cc-badge cc-badge-status-pending"><i></i>Pending approval</span>
                            @else
                                <span class="cc-badge cc-badge-status-locked"><i></i>Locked</span>
                            @endif
                        </td>
                        <td class="cc-issued">{{ $cert->issued_at?->format('M d, Y') ?? '—' }}</td>
                        <td>
                            <div class="cc-row-actions">
                                @if ($cert->status === 'pending_admin_approval')
                                    <button class="btn btn-success btn-sm" wire:click="approve({{ $cert->id }})" wire:confirm="Approve and unlock this certificate?">Approve</button>
                                    <button class="btn btn-danger-outline btn-sm" wire:click="reject({{ $cert->id }})">Reject</button>
                                @elseif ($cert->status === 'unlocked')
                                    <button class="btn btn-danger-outline btn-sm" wire:click="revoke({{ $cert->id }})" wire:confirm="Revoke this certificate? The student will lose access.">Revoke</button>
                                @else
                                    <span class="cc-waiting">Waiting on progress</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">
                            <div class="cc-empty">
                                <svg width="34" height="34" viewBox="0 0 24 24" fill="none"><path d="M9 12l2 2 4-4M7.8 4.2h8.4a2 2 0 012 2v13.4l-6.2-3.2-6.2 3.2V6.2a2 2 0 012-2z" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                <p>No certificates match these filters.</p>
                                <button class="btn btn-ghost btn-sm" wire:click="$set('search', '')">Clear filters</button>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="cc-pagination">
        {{ $this->certificates->links() }}
    </div>

    {{-- Manual issue modal --}}
    @if ($showIssueModal)
        <div class="cc-modal-backdrop" wire:click.self="$set('showIssueModal', false)">
            <div class="cc-modal" role="dialog" aria-modal="true" aria-labelledby="cc-modal-title">
                <div class="cc-modal-head">
                    <h3 id="cc-modal-title">Issue manual certificate</h3>
                    <button class="cc-modal-close" wire:click="$set('showIssueModal', false)" aria-label="Close">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M18 6L6 18M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                    </button>
                </div>

                <div class="cc-field">
                    <label for="issueUserId">Student</label>
                    <select id="issueUserId" wire:model="issueUserId">
                        <option value="">Select student…</option>
                        @foreach (\App\Models\User::orderBy('name')->limit(100)->get() as $user)
                            <option value="{{ $user->id }}">{{ $user->name }} — {{ $user->email }}</option>
                        @endforeach
                    </select>
                    @error('issueUserId') <div class="cc-error">{{ $message }}</div> @enderror
                </div>

                <div class="cc-field">
                    <label for="issueCourseId">Course</label>
                    <select id="issueCourseId" wire:model="issueCourseId">
                        <option value="">Select course…</option>
                        @foreach ($this->courses as $course)
                            <option value="{{ $course->id }}">{{ $course->title }}</option>
                        @endforeach
                    </select>
                    @error('issueCourseId') <div class="cc-error">{{ $message }}</div> @enderror
                </div>

                <div class="cc-field">
                    <label for="issueType">Certificate type</label>
                    <select id="issueType" wire:model="issueType">
                        <option value="course">Course</option>
                        <option value="level">Level</option>
                        <option value="demo">Demo</option>
                    </select>
                </div>

                <div class="cc-modal-actions">
                    <button class="btn btn-ghost" wire:click="$set('showIssueModal', false)">Cancel</button>
                    <button class="btn btn-primary" wire:click="issueManually">Issue &amp; unlock</button>
                </div>
            </div>
        </div>
    @endif
</div>
<style>
    /* ═══════════════════════════════════════════════
   CERTIFICATES PAGE — built on existing :root tokens
═══════════════════════════════════════════════ */

/* .cc-page { max-width: 1280px; } */

/* Toast */
.cc-toast {
    position: fixed; top: 20px; right: 20px; z-index: 50;
    display: flex; align-items: center; gap: 10px;
    padding: 12px 18px; border-radius: var(--radius-xs);
    color: #fff; font-size: 14px; font-weight: 600;
    box-shadow: var(--shadow);
}
.cc-toast-success { background: var(--success); }
.cc-toast-danger { background: var(--danger); }
.cc-toast-enter { transition: opacity .2s ease, transform .2s ease; }
.cc-toast-enter { transform: translateY(0); opacity: 1; }
.cc-toast-leave { transition: opacity .2s ease, transform .2s ease; opacity: 0; transform: translateY(-6px); }

/* Page head */
.cc-page-head {
    display: flex; align-items: flex-start; justify-content: space-between;
    gap: 20px; margin-bottom: 28px;
}
.cc-title { font-size: 26px; font-weight: 700; letter-spacing: -0.02em; color: var(--text); }
.cc-subtitle { margin-top: 4px; font-size: 14.5px; color: var(--text-muted); }
.cc-issue-btn { display: inline-flex; align-items: center; gap: 8px; white-space: nowrap; }

/* Stats */
.cc-stat-row {
    display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px;
    margin-bottom: 24px;
}
.cc-stat-card {
    display: flex; align-items: center; gap: 14px;
    background: var(--bg-card); border: 1px solid var(--line);
    border-radius: var(--radius-sm); padding: 18px 20px;
    box-shadow: var(--shadow-sm);
    transition: box-shadow .2s ease, transform .2s ease;
}
.cc-stat-card:hover { box-shadow: var(--shadow-card); transform: translateY(-1px); }
.cc-stat-icon {
    flex-shrink: 0; width: 40px; height: 40px; border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
}
.cc-stat-icon--total    { background: rgba(9, 71, 168, .1);  color: var(--brand-primary); }
.cc-stat-icon--pending  { background: rgba(217, 119, 6, .12); color: var(--warning); }
.cc-stat-icon--unlocked { background: rgba(22, 163, 74, .12); color: var(--success); }
.cc-stat-icon--locked   { background: rgba(90, 113, 138, .12); color: var(--text-muted); }
.cc-stat-val { font-size: 22px; font-weight: 700; color: var(--text); line-height: 1.1; }
.cc-stat-lbl { font-size: 12.5px; color: var(--text-muted); margin-top: 3px; }

/* Filter bar */
.cc-filter-bar {
    display: flex; align-items: center; gap: 10px; margin-bottom: 18px;
    flex-wrap: wrap;
}
.cc-search-wrap { position: relative; flex: 1 1 280px; min-width: 220px; }
.cc-search-icon { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--text-muted); pointer-events: none; }
.cc-search-wrap input {
    width: 100%; padding: 10px 14px 10px 36px;
    border: 1px solid var(--input-border); border-radius: var(--radius-xs);
    background: var(--input-bg); color: var(--text); font-size: 14px;
    transition: border-color .15s ease, box-shadow .15s ease;
}
.cc-search-wrap input:focus {
    outline: none; border-color: var(--input-focus);
    box-shadow: 0 0 0 3px var(--primary-glow);
}
.cc-select {
    padding: 10px 14px; border: 1px solid var(--input-border);
    border-radius: var(--radius-xs); background: var(--input-bg);
    color: var(--text); font-size: 14px; min-width: 170px;
}
.cc-select:focus { outline: none; border-color: var(--input-focus); box-shadow: 0 0 0 3px var(--primary-glow); }

/* Table */
.cc-table-card {
    background: var(--bg-card); border: 1px solid var(--line);
    border-radius: var(--radius-sm); overflow: hidden;
    box-shadow: var(--shadow-sm);
}
.cc-table { width: 100%; border-collapse: collapse; font-size: 14px; }
.cc-table thead th {
    text-align: left; font-size: 12px; font-weight: 600; text-transform: uppercase;
    letter-spacing: .04em; color: var(--text-muted);
    background: var(--bg-card2); padding: 13px 18px; border-bottom: 1px solid var(--line);
}
.cc-th-actions { text-align: right; }
.cc-table tbody tr { border-bottom: 1px solid var(--line); transition: background .12s ease; }
.cc-table tbody tr:last-child { border-bottom: none; }
.cc-table tbody tr:hover { background: var(--bg-card2); }
.cc-table td { padding: 14px 18px; vertical-align: middle; color: var(--text); }
.cc-table tbody.cc-loading { opacity: .5; }

.cc-cell-user { display: flex; align-items: center; gap: 12px; }
.cc-avatar {
    width: 36px; height: 36px; border-radius: 50%; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center;
    font-size: 12.5px; font-weight: 700; color: #fff;
    background: linear-gradient(135deg, var(--brand-primary), var(--brand-secondary));
}
.cc-name { font-weight: 600; color: var(--text); }
.cc-email { font-size: 12.5px; color: var(--text-muted); margin-top: 1px; }
.cc-subject { color: var(--text); }
.cc-issued { color: var(--text-muted); }

/* Badges */
.cc-badge {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 4px 10px; border-radius: 100px;
    font-size: 12px; font-weight: 600;
}
.cc-badge i { width: 6px; height: 6px; border-radius: 50%; background: currentColor; display: inline-block; }

.cc-badge-type-course { background: rgba(9, 71, 168, .1); color: var(--brand-primary); }
.cc-badge-type-week   { background: rgba(122, 92, 255, .12); color: var(--brand-secondary); }
.cc-badge-type-level  { background: rgba(240, 179, 90, .18); color: #97650f; }
.cc-badge-type-demo   { background: rgba(90, 113, 138, .12); color: var(--text-muted); }

.cc-badge-status-unlocked { background: rgba(22, 163, 74, .12); color: var(--success); }
.cc-badge-status-pending  { background: rgba(217, 119, 6, .12); color: var(--warning); }
.cc-badge-status-locked   { background: rgba(90, 113, 138, .12); color: var(--text-muted); }

/* Progress */
.cc-progress { display: flex; align-items: center; gap: 10px; min-width: 140px; }
.cc-progress-track {
    flex: 1; height: 6px; border-radius: 100px; background: var(--line); overflow: hidden;
}
.cc-progress-track span {
    display: block; height: 100%; border-radius: 100px;
    background: linear-gradient(90deg, var(--brand-primary), var(--brand-secondary));
}
.cc-progress-pct { font-size: 12.5px; color: var(--text-muted); width: 34px; text-align: right; }

/* Row actions */
.cc-row-actions { display: flex; justify-content: flex-end; gap: 8px; }
.cc-waiting { font-size: 13px; color: var(--text-muted); }

/* Empty state */
.cc-empty { display: flex; flex-direction: column; align-items: center; gap: 10px; padding: 48px 20px; color: var(--text-muted); }
.cc-empty svg { color: var(--line); }
.cc-empty p { font-size: 14px; }

/* Pagination */
.cc-pagination { margin-top: 18px; }

/* Modal */
.cc-modal-backdrop {
    position: fixed; inset: 0; background: rgba(8, 17, 31, .45);
    backdrop-filter: blur(2px);
    display: flex; align-items: center; justify-content: center; z-index: 40;
    padding: 20px;
}
.cc-modal {
    background: var(--bg-card); border-radius: var(--radius); padding: 24px;
    width: 100%; max-width: 440px; box-shadow: var(--shadow);
    border: 1px solid var(--line);
}
.cc-modal-head { display: flex; align-items: center; justify-content: space-between; margin-bottom: 18px; }
.cc-modal-head h3 { font-size: 17px; font-weight: 700; color: var(--text); }
.cc-modal-close {
    background: transparent; border: none; color: var(--text-muted);
    width: 28px; height: 28px; display: flex; align-items: center; justify-content: center;
    border-radius: 6px; transition: background .12s ease;
}
.cc-modal-close:hover { background: var(--bg-card2); color: var(--text); }

.cc-field { margin-bottom: 16px; }
.cc-field label { display: block; font-size: 12.5px; font-weight: 600; color: var(--text-muted); margin-bottom: 6px; }
.cc-field select {
    width: 100%; padding: 10px 12px; border: 1px solid var(--input-border);
    border-radius: var(--radius-xs); background: var(--input-bg); color: var(--text);
    font-size: 14px;
}
.cc-field select:focus { outline: none; border-color: var(--input-focus); box-shadow: 0 0 0 3px var(--primary-glow); }
.cc-error { color: var(--danger); font-size: 12px; margin-top: 5px; }

.cc-modal-actions { display: flex; gap: 10px; justify-content: flex-end; margin-top: 6px; }

/* Responsive */
@media (max-width: 900px) {
    .cc-stat-row { grid-template-columns: repeat(2, 1fr); }
    .cc-table-card { overflow-x: auto; }
    .cc-table { min-width: 780px; }
}
@media (max-width: 560px) {
    .cc-page-head { flex-direction: column; align-items: stretch; }
    .cc-stat-row { grid-template-columns: 1fr; }
    .cc-filter-bar { flex-direction: column; align-items: stretch; }
    .cc-select { min-width: 0; }
}
</style>
