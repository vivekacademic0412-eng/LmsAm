{{-- FILE: resources/views/livewire/user-onboarding-report.blade.php --}}
<div class="uor-wrap">

<style>
/* Scoped to this component — reuses your existing CSS variables (--bg-card, --text-main, etc.) */
.uor-wrap{ display:flex; flex-direction:column; gap:20px; }

.uor-header{ display:flex; align-items:flex-start; justify-content:space-between; gap:16px; flex-wrap:wrap; }
.uor-header h1{ font-size:22px; font-weight:700; color:var(--text-main); letter-spacing:-.01em; }
.uor-header p{ font-size:13px; color:var(--text-muted); margin-top:4px; }

.uor-stats{ display:grid; grid-template-columns:repeat(auto-fit,minmax(160px,1fr)); gap:14px; }
.uor-stat{ background:var(--bg-card); border:1px solid var(--border); border-radius:var(--radius-sm); padding:14px 16px; box-shadow:var(--shadow-sm); }
.uor-stat .num{ font-size:22px; font-weight:700; color:var(--text-main); }
.uor-stat .lbl{ font-size:11.5px; color:var(--text-muted); text-transform:uppercase; letter-spacing:.04em; margin-top:2px; }
.uor-stat.accent .num{ color:var(--primary); }
.uor-stat.ok .num{ color:var(--success); }
.uor-stat.warn .num{ color:var(--warning); }

.uor-filter-bar{ display:flex; flex-wrap:wrap; align-items:end; gap:12px; background:var(--bg-card); border:1px solid var(--border); border-radius:var(--radius-sm); padding:14px 16px; box-shadow:var(--shadow-sm); }
.uor-fgroup{ display:flex; flex-direction:column; gap:5px; min-width:150px; }
.uor-fgroup label{ font-size:11px; font-weight:600; color:var(--text-muted); text-transform:uppercase; letter-spacing:.04em; }
.uor-fgroup input, .uor-fgroup select{
    background:var(--input-bg); border:1px solid var(--input-border); border-radius:var(--radius-xs, 8px);
    padding:8px 10px; font-size:13px; color:var(--text-main); outline:none; transition:border-color .15s;
}
.uor-fgroup input:focus, .uor-fgroup select:focus{ border-color:var(--input-focus); }
.uor-search{ min-width:220px; position:relative; }
.uor-search svg{ position:absolute; left:10px; top:31px; color:var(--text-muted); pointer-events:none; }
.uor-search input{ padding-left:30px; width:100%; }
.uor-clear-btn{ display:flex; align-items:center; gap:6px; background:transparent; border:1px solid var(--border); color:var(--text-muted); font-size:12.5px; font-weight:600; padding:8px 12px; border-radius:var(--radius-xs,8px); }
.uor-clear-btn:hover{ color:var(--danger); border-color:var(--danger); }
.uor-count{ margin-left:auto; font-size:12px; color:var(--text-muted); align-self:center; }

.uor-table-card{ background:var(--bg-card); border:1px solid var(--border); border-radius:var(--radius); overflow:hidden; box-shadow:var(--shadow-card); }
.uor-table-head{ padding:16px 18px; border-bottom:1px solid var(--border); }
.uor-table-head h2{ font-size:15px; font-weight:700; color:var(--text-main); }

.uor-table-wrap{ overflow-x:auto; }
table.uor-table{ width:100%; border-collapse:collapse; min-width:900px; }
table.uor-table thead th{
    text-align:left; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.05em;
    color:var(--text-muted); padding:11px 16px; border-bottom:1px solid var(--border); background:var(--bg-card2, var(--bg2));
}
table.uor-table tbody td{ padding:12px 16px; border-bottom:1px solid var(--line); font-size:13.5px; color:var(--text-main); vertical-align:middle; }
table.uor-table tbody tr:last-child td{ border-bottom:none; }
table.uor-table tbody tr:hover{ background:var(--bg2); }

.uor-user-cell{ display:flex; align-items:center; gap:10px; }
.uor-avatar{ width:34px; height:34px; border-radius:50%; background:var(--primary); color:#fff; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:13px; flex-shrink:0; overflow:hidden; }
.uor-avatar img{ width:100%; height:100%; object-fit:cover; }
.uor-user-cell strong{ display:block; font-size:13.5px; color:var(--text-main); }
.uor-user-cell span{ display:block; font-size:12px; color:var(--text-muted); }

.uor-tag{ display:inline-flex; align-items:center; padding:3px 10px; border-radius:999px; font-size:11px; font-weight:700; text-transform:capitalize; }
.role-trainer{ background:rgba(9,71,168,.1); color:var(--brand-primary,var(--primary)); }
.role-student{ background:rgba(22,163,74,.12); color:var(--success); }
.role-hr{ background:rgba(122,92,255,.12); color:var(--brand-secondary,var(--accent2)); }
.role-it{ background:rgba(2,132,199,.12); color:var(--info); }
.role-demo{ background:rgba(217,119,6,.12); color:var(--warning); }
.role-default{ background:var(--bg2); color:var(--text-muted); }

.uor-badge{ display:inline-flex; align-items:center; gap:5px; padding:3px 10px; border-radius:999px; font-size:11px; font-weight:700; }
.ob-completed{ background:rgba(22,163,74,.12); color:var(--success); }
.ob-in_progress{ background:rgba(217,119,6,.12); color:var(--warning); }
.ob-pending{ background:rgba(220,38,38,.1); color:var(--danger); }

.uor-policy-badge{ display:inline-flex; align-items:center; gap:5px; padding:3px 10px; border-radius:999px; font-size:11px; font-weight:700; }
.pol-agreed{ background:rgba(22,163,74,.12); color:var(--success); }
.pol-pending{ background:rgba(220,38,38,.1); color:var(--danger); }

.uor-src-cell{ display:flex; flex-direction:column; gap:2px; }
.uor-src-main{ font-size:12.5px; font-weight:600; color:var(--text-main); }
.uor-src-sub{ font-size:11px; color:var(--text-muted); }
.uor-src-empty{ font-size:12px; color:var(--text-muted); font-style:italic; }

.uor-view-btn{ display:inline-flex; align-items:center; gap:6px; background:var(--primary); color:#fff; border:none; padding:7px 13px; border-radius:var(--radius-xs,8px); font-size:12.5px; font-weight:600; }
.uor-view-btn:hover{ filter:brightness(1.08); }

.uor-empty{ display:flex; flex-direction:column; align-items:center; gap:10px; padding:50px 0; color:var(--text-muted); }

.uor-pagination{ display:flex; align-items:center; justify-content:center; gap:6px; padding:16px; border-top:1px solid var(--border); }
.uor-page-btn{ min-width:32px; height:32px; display:flex; align-items:center; justify-content:center; border-radius:var(--radius-xs,8px); border:1px solid var(--border); background:transparent; color:var(--text-main); font-size:12.5px; font-weight:600; }
.uor-page-btn.active{ background:var(--primary); border-color:var(--primary); color:#fff; }
.uor-page-btn.disabled{ opacity:.4; pointer-events:none; }

/* Detail modal */
.uor-modal-bg{ position:fixed; inset:0; background:rgba(8,15,25,.55); backdrop-filter:blur(2px); display:flex; align-items:center; justify-content:center; z-index:60; padding:20px; }
.uor-modal{ background:var(--bg-card); border:1px solid var(--border); border-radius:var(--radius); width:100%; max-width:760px; max-height:88vh; display:flex; flex-direction:column; box-shadow:var(--shadow); }
.uor-modal-head{ display:flex; align-items:center; justify-content:space-between; padding:18px 22px; border-bottom:1px solid var(--border); }
.uor-modal-head h3{ font-size:16px; font-weight:700; color:var(--text-main); display:flex; align-items:center; gap:10px; }
.uor-modal-close{ background:transparent; border:none; color:var(--text-muted); padding:4px; border-radius:6px; }
.uor-modal-close:hover{ color:var(--danger); background:var(--bg2); }
.uor-modal-body{ padding:20px 22px; overflow-y:auto; display:flex; flex-direction:column; gap:22px; }

.uor-section-title{ font-size:11.5px; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:var(--primary); margin-bottom:10px; padding-bottom:6px; border-bottom:1px solid var(--line); display:flex; align-items:center; gap:6px; }
.uor-kv-grid{ display:grid; grid-template-columns:repeat(auto-fit,minmax(190px,1fr)); gap:12px 20px; }
.uor-kv{ display:flex; flex-direction:column; gap:2px; }
.uor-kv .k{ font-size:11px; color:var(--text-muted); text-transform:uppercase; letter-spacing:.03em; }
.uor-kv .v{ font-size:13.5px; color:var(--text-main); font-weight:500; word-break:break-word; }

.uor-doc-list{ display:flex; flex-direction:column; gap:8px; }
.uor-doc-item{ display:flex; align-items:center; justify-content:space-between; gap:10px; background:var(--bg2); border:1px solid var(--line); border-radius:var(--radius-xs,8px); padding:9px 12px; }
.uor-doc-item .name{ font-size:12.5px; color:var(--text-main); font-weight:600; }
.uor-doc-item .meta{ font-size:11px; color:var(--text-muted); }
.uor-doc-link{ font-size:12px; font-weight:700; color:var(--primary); }

.uor-traffic-entry{ border:1px solid var(--line); border-radius:var(--radius-sm); padding:12px 14px; margin-bottom:10px; background:var(--bg2); }
.uor-traffic-entry:last-child{ margin-bottom:0; }
.uor-traffic-head{ display:flex; align-items:center; justify-content:space-between; margin-bottom:8px; }
.uor-traffic-head .src{ font-size:13px; font-weight:700; color:var(--text-main); }
.uor-traffic-head .date{ font-size:11px; color:var(--text-muted); }

.uor-empty-note{ font-size:12.5px; color:var(--text-muted); font-style:italic; }
</style>


{{-- ═══════════════════════════════════════
     HEADER
════════════════════════════════════════ --}}
<div class="uor-header">
    <div>
        <h1>Onboarding & Traffic Report</h1>
        <p>All users (excluding Super Admin &amp; Admin) with policy acceptance and acquisition-source details.</p>
    </div>
</div>

{{-- ═══════════════════════════════════════
     QUICK STATS
════════════════════════════════════════ --}}
<div class="uor-stats">
    <div class="uor-stat">
        <div class="num">{{ $this->users->total() }}</div>
        <div class="lbl">Total Users</div>
    </div>
    <div class="uor-stat ok">
        <div class="num">{{ \App\Models\User::whereNotIn('role',[\App\Models\User::ROLE_SUPERADMIN,\App\Models\User::ROLE_ADMIN])->where('onboarding_status','completed')->count() }}</div>
        <div class="lbl">Onboarding Completed</div>
    </div>
    <div class="uor-stat warn">
        <div class="num">{{ \App\Models\User::whereNotIn('role',[\App\Models\User::ROLE_SUPERADMIN,\App\Models\User::ROLE_ADMIN])->where('onboarding_status','pending')->count() }}</div>
        <div class="lbl">Onboarding Pending</div>
    </div>
    <div class="uor-stat accent">
        <div class="num">{{ \App\Models\User::whereNotIn('role',[\App\Models\User::ROLE_SUPERADMIN,\App\Models\User::ROLE_ADMIN])->whereHas('policyAcceptances', fn($q) => $q->where('terms_agreed', true))->count() }}</div>
        <div class="lbl">Policy Agreed</div>
    </div>
</div>

{{-- ═══════════════════════════════════════
     FILTER BAR
════════════════════════════════════════ --}}
<div class="uor-filter-bar">
    <div class="uor-fgroup uor-search">
        <label>Search</label>
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input type="text" wire:model.live.debounce.350ms="search" placeholder="Name or email…">
    </div>

    <div class="uor-fgroup">
        <label>Role</label>
        <select wire:model.live="filterRole">
            <option value="">All Roles</option>
            @foreach($this->roleOptions as $val => $lbl)
                <option value="{{ $val }}">{{ $lbl }}</option>
            @endforeach
        </select>
    </div>

    <div class="uor-fgroup">
        <label>Onboarding</label>
        <select wire:model.live="filterOnboarding">
            <option value="">All Statuses</option>
            <option value="pending">Pending</option>
            <option value="in_progress">In Progress</option>
            <option value="completed">Completed</option>
        </select>
    </div>

    <div class="uor-fgroup">
        <label>Policy</label>
        <select wire:model.live="filterPolicy">
            <option value="">Any</option>
            <option value="agreed">Agreed</option>
            <option value="pending">Not Agreed</option>
        </select>
    </div>

    <div class="uor-fgroup">
        <label>Traffic Source</label>
        <select wire:model.live="filterSource">
            <option value="">All Sources</option>
            @foreach($this->sourceOptions as $src)
                <option value="{{ $src }}">{{ $src }}</option>
            @endforeach
        </select>
    </div>

    @if($filterRole || $filterOnboarding || $filterPolicy || $filterSource || $search)
    <button type="button" wire:click="clearFilters" class="uor-clear-btn">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" aria-hidden="true"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        Clear
    </button>
    @endif

    <span class="uor-count">{{ $this->users->total() }} {{ Str::plural('user', $this->users->total()) }} found</span>
</div>

{{-- ═══════════════════════════════════════
     TABLE
════════════════════════════════════════ --}}
<div class="uor-table-card">
    <div class="uor-table-head">
        <h2>All Users</h2>
    </div>

    <div class="uor-table-wrap" wire:loading.class="opacity-60">
        <table class="uor-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>User</th>
                    <th>Role</th>
                    <th>Onboarding</th>
                    <th>Policy</th>
                    <th>Acquisition Source</th>
                    <th>Registered</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($this->users as $u)
                @php
                    $roleClass = match($u->role) {
                        \App\Models\User::ROLE_TRAINER    => 'role-trainer',
                        \App\Models\User::ROLE_STUDENT    => 'role-student',
                        \App\Models\User::ROLE_MANAGER_HR => 'role-hr',
                        \App\Models\User::ROLE_IT         => 'role-it',
                        \App\Models\User::ROLE_DEMO       => 'role-demo',
                        default                           => 'role-default',
                    };
                    $latestPolicy = $u->policyAcceptances->first();
                    $agreed = $latestPolicy && $latestPolicy->terms_agreed;
                    $latestTraffic = $u->trafficSources->first();
                @endphp
                <tr wire:key="uor-user-{{ $u->id }}">
                    <td style="color:var(--text-muted); font-size:12px">{{ $u->id }}</td>

                    <td>
                        <div class="uor-user-cell">
                            <div class="uor-avatar">
                                @if($u->avatar_url ?? false)
                                    <img src="{{ $u->avatar_url }}" alt="{{ $u->name }}">
                                @else
                                    {{ strtoupper(substr($u->name, 0, 1)) }}
                                @endif
                            </div>
                            <div>
                                <strong>{{ $u->name }}</strong>
                                <span>{{ $u->email }}</span>
                            </div>
                        </div>
                    </td>

                    <td>
                        <span class="uor-tag {{ $roleClass }}">
                            {{ \App\Models\User::roleOptions()[$u->role] ?? $u->role }}
                        </span>
                    </td>

                    <td>
                        <span class="uor-badge ob-{{ $u->onboarding_status }}">
                            {{ ucwords(str_replace('_',' ', $u->onboarding_status)) }} · Step {{ $u->onboarding_step }}
                        </span>
                    </td>

                    <td>
                        @if($latestPolicy)
                            <span class="uor-policy-badge {{ $agreed ? 'pol-agreed' : 'pol-pending' }}">
                                {{ $agreed ? '✔ Agreed' : '✘ Declined' }} · v{{ $latestPolicy->policy_version }}
                            </span>
                        @else
                            <span class="uor-policy-badge pol-pending">Not Submitted</span>
                        @endif
                    </td>

                    <td>
                        @if($latestTraffic)
                            <div class="uor-src-cell">
                                <span class="uor-src-main">{{ $latestTraffic->acquisition_label }}</span>
                                <span class="uor-src-sub">{{ $latestTraffic->utm_medium ?: $latestTraffic->device ?: '—' }}</span>
                            </div>
                        @else
                            <span class="uor-src-empty">No tracking data</span>
                        @endif
                    </td>

                    <td style="color:var(--text-muted); font-size:12.5px">{{ $u->created_at?->format('d M Y') }}</td>

                    <td>
                        <button type="button" wire:click="viewUser({{ $u->id }})" class="uor-view-btn">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            View
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8">
                        <div class="uor-empty">
                            <svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" aria-hidden="true"><circle cx="12" cy="8" r="4"/><path d="M6 20v-2a6 6 0 0 1 12 0v2"/></svg>
                            <p>@if($search) No users match "{{ $search }}" @else No users found. @endif</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($this->users->hasPages())
    <div class="uor-pagination">
        <button wire:click="previousPage" class="uor-page-btn {{ $this->users->onFirstPage() ? 'disabled' : '' }}" aria-label="Previous page">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" aria-hidden="true"><polyline points="15 18 9 12 15 6"/></svg>
        </button>
        @foreach($this->users->getUrlRange(1, $this->users->lastPage()) as $page => $url)
            <button wire:click="gotoPage({{ $page }})" class="uor-page-btn {{ $page === $this->users->currentPage() ? 'active' : '' }}">
                {{ $page }}
            </button>
        @endforeach
        <button wire:click="nextPage" class="uor-page-btn {{ $this->users->hasMorePages() ? '' : 'disabled' }}" aria-label="Next page">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" aria-hidden="true"><polyline points="9 18 15 12 9 6"/></svg>
        </button>
    </div>
    @endif
</div>


{{-- ═══════════════════════════════════════
     DETAIL MODAL
════════════════════════════════════════ --}}
@if($showDetail && $this->viewingUser)
@php $vu = $this->viewingUser; @endphp
<div class="uor-modal-bg" wire:click.self="closeDetail">
    <div class="uor-modal" role="dialog" aria-modal="true">
        <div class="uor-modal-head">
            <h3>
                <div class="uor-avatar" style="width:30px;height:30px;font-size:12px;">
                    @if($vu->avatar_url ?? false)
                        <img src="{{ $vu->avatar_url }}" alt="{{ $vu->name }}">
                    @else
                        {{ strtoupper(substr($vu->name, 0, 1)) }}
                    @endif
                </div>
                {{ $vu->name }}
            </h3>
            <button type="button" wire:click="closeDetail" class="uor-modal-close" aria-label="Close">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" aria-hidden="true"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>

        <div class="uor-modal-body">

            {{-- Account --}}
            <div>
                <div class="uor-section-title">Account</div>
                <div class="uor-kv-grid">
                    <div class="uor-kv"><span class="k">Email</span><span class="v">{{ $vu->email }}</span></div>
                    <div class="uor-kv"><span class="k">Role</span><span class="v">{{ \App\Models\User::roleOptions()[$vu->role] ?? $vu->role }}</span></div>
                    <div class="uor-kv"><span class="k">Status</span><span class="v">{{ $vu->is_active ? 'Active' : 'Inactive' }}</span></div>
                     <div class="uor-kv"><span class="k">Contact</span><span class="v">{{ $vu->contact ?? ''}}</span></div>
                    <div class="uor-kv"><span class="k">Onboarding</span><span class="v">{{ ucwords(str_replace('_',' ',$vu->onboarding_status)) }} (Step {{ $vu->onboarding_step }})</span></div>
                    <div class="uor-kv"><span class="k">Registered</span><span class="v">{{ $vu->created_at?->format('d M Y, h:i A') }}</span></div>
                </div>
            </div>

            {{-- Personal --}}
            @if($vu->studentProfile)
            <div>
                <div class="uor-section-title">Personal Details</div>
                <div class="uor-kv-grid">
                    <div class="uor-kv"><span class="k">Full Name</span><span class="v">{{ $vu->studentProfile->first_name }} {{ $vu->studentProfile->last_name }}</span></div>
                    <div class="uor-kv"><span class="k">DOB</span><span class="v">{{ $vu->studentProfile->dob?->format('d M Y') }}</span></div>
                    <div class="uor-kv"><span class="k">Gender</span><span class="v">{{ ucfirst($vu->studentProfile->gender) }}</span></div>
                    <div class="uor-kv"><span class="k">Mobile</span><span class="v">{{ $vu->studentProfile->mobile_number }}</span></div>
                    <div class="uor-kv"><span class="k">WhatsApp</span><span class="v">{{ $vu->studentProfile->whatsapp_number ?: '—' }}</span></div>
                    <div class="uor-kv"><span class="k">City / District</span><span class="v">{{ $vu->studentProfile->city_district }}</span></div>
                    <div class="uor-kv"><span class="k">ID Proof</span><span class="v">{{ strtoupper($vu->studentProfile->id_proof_type) }} · {{ $vu->studentProfile->id_number }}</span></div>
                    <div class="uor-kv" style="grid-column:1/-1"><span class="k">Address</span><span class="v">{{ $vu->studentProfile->residential_address }}</span></div>
                </div>
            </div>
            @endif

            {{-- Academic --}}
            @if($vu->academicBackground)
            <div>
                <div class="uor-section-title">Academic Background</div>
                <div class="uor-kv-grid">
                    <div class="uor-kv"><span class="k">Qualification</span><span class="v">{{ $vu->academicBackground->highest_qualification }}</span></div>
                    <div class="uor-kv"><span class="k">%/CGPA</span><span class="v">{{ $vu->academicBackground->percentage_cgpa }}</span></div>
                    <div class="uor-kv"><span class="k">Institution</span><span class="v">{{ $vu->academicBackground->institution_name }}</span></div>
                    <div class="uor-kv"><span class="k">Year of Passing</span><span class="v">{{ $vu->academicBackground->year_of_passing }}</span></div>
                    <div class="uor-kv"><span class="k">Experience</span><span class="v">{{ $vu->academicBackground->experience_level }}</span></div>
                    <div class="uor-kv"><span class="k">Guardian</span><span class="v">{{ $vu->academicBackground->guardian_name }} ({{ $vu->academicBackground->guardian_mobile }})</span></div>
                </div>
            </div>
            @endif

            {{-- Program enrollment --}}
            @if($vu->programEnrollments->isNotEmpty())
            <div>
                <div class="uor-section-title">Program Enrollment</div>
                @foreach($vu->programEnrollments as $pe)
                <div class="uor-kv-grid" style="margin-bottom:10px;">
                    <div class="uor-kv"><span class="k">Mode</span><span class="v">{{ ucfirst($pe->mode_of_learning) }}</span></div>
                    <div class="uor-kv"><span class="k">Preferred Start</span><span class="v">{{ $pe->preferred_start_date?->format('d M Y') }}</span></div>
                    <div class="uor-kv"><span class="k">Referral Source</span><span class="v">{{ $pe->referral_source ?: '—' }}</span></div>
                    <div class="uor-kv"><span class="k">Status</span><span class="v">{{ ucfirst($pe->status) }}</span></div>
                    <div class="uor-kv" style="grid-column:1/-1"><span class="k">Career Goal</span><span class="v">{{ $pe->career_goal ?: '—' }}</span></div>
                </div>
                @endforeach
            </div>
            @endif

            {{-- Policy acceptance --}}
            <div>
                <div class="uor-section-title">Policy Agreement</div>
                @forelse($vu->policyAcceptances as $pa)
                <div class="uor-traffic-entry">
                    <div class="uor-traffic-head">
                        <span class="src">{{ $pa->policy->title ?? 'Policy' }} · v{{ $pa->policy_version }}</span>
                        <span class="date">{{ $pa->accepted_at?->format('d M Y, h:i A') }}</span>
                    </div>
                    <div class="uor-kv-grid">
                        <div class="uor-kv"><span class="k">Terms Agreed</span><span class="v">{{ $pa->terms_agreed ? '✔ Yes' : '✘ No' }}</span></div>
                        <div class="uor-kv"><span class="k">Declaration Confirmed</span><span class="v">{{ $pa->declaration_confirmed ? '✔ Yes' : '✘ No' }}</span></div>
                        <div class="uor-kv"><span class="k">Marketing Opt-in</span><span class="v">{{ $pa->marketing_opt_in ? 'Yes' : 'No' }}</span></div>
                        <div class="uor-kv"><span class="k">IP Address</span><span class="v">{{ $pa->ip_address }}</span></div>
                    </div>
                </div>
                @empty
                <p class="uor-empty-note">No policy acceptance record submitted yet.</p>
                @endforelse
            </div>

            {{-- Traffic / acquisition --}}
            <div>
                <div class="uor-section-title">Traffic &amp; Acquisition Source</div>
                @forelse($vu->trafficSources as $t)
                <div class="uor-traffic-entry">
                    <div class="uor-traffic-head">
                        <span class="src">{{ $t->acquisition_label }}</span>
                        <span class="date">{{ $t->created_at?->format('d M Y, h:i A') }}</span>
                    </div>
                    <div class="uor-kv-grid">
                        <div class="uor-kv"><span class="k">Source</span><span class="v">{{ $t->source ?: '—' }}</span></div>
                        <div class="uor-kv"><span class="k">UTM Source</span><span class="v">{{ $t->utm_source ?: '—' }}</span></div>
                        <div class="uor-kv"><span class="k">UTM Medium</span><span class="v">{{ $t->utm_medium ?: '—' }}</span></div>
                        <div class="uor-kv"><span class="k">UTM Campaign</span><span class="v">{{ $t->utm_campaign ?: '—' }}</span></div>
                        <div class="uor-kv"><span class="k">UTM Term</span><span class="v">{{ $t->utm_term ?: '—' }}</span></div>
                        <div class="uor-kv"><span class="k">UTM Content</span><span class="v">{{ $t->utm_content ?: '—' }}</span></div>
                        <div class="uor-kv"><span class="k">Device</span><span class="v">{{ $t->device ?: '—' }}</span></div>
                        <div class="uor-kv"><span class="k">Browser</span><span class="v">{{ $t->browser ?: '—' }}</span></div>
                        <div class="uor-kv"><span class="k">Platform</span><span class="v">{{ $t->platform ?: '—' }}</span></div>
                        <div class="uor-kv"><span class="k">IP Address</span><span class="v">{{ $t->user_ip ?: '—' }}</span></div>
                        <div class="uor-kv" style="grid-column:1/-1"><span class="k">Landing Page</span><span class="v">{{ $t->landing_page ?: '—' }}</span></div>
                        <div class="uor-kv" style="grid-column:1/-1"><span class="k">Referrer URL</span><span class="v">{{ $t->referrer_url ?: '—' }}</span></div>
                    </div>
                </div>
                @empty
                <p class="uor-empty-note">No traffic / acquisition data recorded for this user.</p>
                @endforelse
            </div>

            {{-- Documents --}}
            @if($vu->onboardingDocuments->isNotEmpty())
            <div>
                <div class="uor-section-title">Uploaded Documents</div>
                <div class="uor-doc-list">
                    @foreach($vu->onboardingDocuments as $doc)
                    <div class="uor-doc-item">
                        <div>
                            <div class="name">{{ ucwords(str_replace('_',' ',$doc->doc_type)) }} — {{ $doc->original_name }}</div>
                            <div class="meta">{{ $doc->file_size_human }} · uploaded {{ $doc->uploaded_at?->format('d M Y') }}</div>
                        </div>
                        <a href="{{ Storage::disk('public')->url($doc->file_path) }}" target="_blank" class="uor-doc-link">Download</a>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

        </div>
    </div>
</div>
@endif

</div>