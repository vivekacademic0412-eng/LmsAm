<div>
    {{-- Flash toasts --}}
    <div x-data="{ show: false, msg: '' }"
         x-on:certificate-approved.window="show = true; msg = 'Certificate approved and unlocked'; setTimeout(() => show = false, 2500)"
         x-on:certificate-rejected.window="show = true; msg = 'Certificate rejected'; setTimeout(() => show = false, 2500)"
         x-on:certificate-revoked.window="show = true; msg = 'Certificate revoked'; setTimeout(() => show = false, 2500)"
         x-on:certificate-issued.window="show = true; msg = 'Certificate issued'; setTimeout(() => show = false, 2500)"
         x-show="show" x-cloak
         style="position:fixed; top:20px; right:20px; background:var(--success); color:#fff; padding:10px 18px; border-radius:var(--radius-xs); box-shadow:var(--shadow); z-index:50;">
        <span x-text="msg"></span>
    </div>

    <div class="page-head">
        <div>
            <h1>Certificates</h1>
            <p>Review auto-unlocked certificates and approve manual submissions.</p>
        </div>
        <button class="btn btn-primary" wire:click="openIssueModal">+ Issue manual certificate</button>
    </div>

    {{-- Stats --}}
    <div class="stat-row">
        <div class="stat-card">
            <div class="val">{{ $this->stats['total'] }}</div>
            <div class="lbl">Total certificates</div>
        </div>
        <div class="stat-card">
            <div class="val">{{ $this->stats['pending'] }}</div>
            <div class="lbl">Pending admin approval</div>
        </div>
        <div class="stat-card">
            <div class="val">{{ $this->stats['unlocked'] }}</div>
            <div class="lbl">Unlocked</div>
        </div>
        <div class="stat-card">
            <div class="val">{{ $this->stats['locked'] }}</div>
            <div class="lbl">Locked (in progress)</div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="filter-bar">
        <div class="search-wrap">
            <input type="text" wire:model.live.debounce.400ms="search" placeholder="Search by student name or email...">
        </div>
        <select wire:model.live="typeFilter">
            <option value="">All types</option>
            <option value="course">Course</option>
            <option value="week">Week</option>
            <option value="level">Level</option>
            <option value="demo">Demo</option>
        </select>
        <select wire:model.live="statusFilter">
            <option value="">All statuses</option>
            <option value="pending_admin_approval">Pending approval</option>
            <option value="unlocked">Unlocked</option>
            <option value="locked">Locked</option>
        </select>
        <button class="btn btn-ghost btn-sm" wire:click="$set('search', '')">Reset</button>
    </div>

    {{-- Table --}}
    <div class="table-card">
        <table>
            <thead>
                <tr>
                    <th>Student</th><th>Subject</th><th>Type</th><th>Completion</th><th>Status</th><th>Issued</th><th>Actions</th>
                </tr>
            </thead>
            <tbody wire:loading.class="opacity-50">
                @forelse ($this->certificates as $cert)
                    <tr wire:key="cert-{{ $cert->id }}">
                        <td>
                            <div class="cell-user">
                                <div class="mini-avatar">{{ strtoupper(substr($cert->user->name ?? '?', 0, 2)) }}</div>
                                <div>
                                    <div class="name">{{ $cert->user->name }}</div>
                                    <div class="sub">{{ $cert->user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td>{{ $cert->subjectTitle() }}</td>
                        <td><span class="badge badge-type-{{ $cert->type }}">{{ ucfirst($cert->type) }}</span></td>
                        <td>
                            <div class="progress-mini">
                                <div class="bar"><span style="width: {{ $cert->completion_percent ?? 0 }}%"></span></div>
                                <div class="pct">{{ $cert->completion_percent ?? 0 }}%</div>
                            </div>
                        </td>
                        <td>
                            @if ($cert->status === 'unlocked')
                                <span class="badge badge-status-unlocked">Unlocked</span>
                            @elseif ($cert->status === 'pending_admin_approval')
                                <span class="badge badge-status-pending">Pending approval</span>
                            @else
                                <span class="badge badge-status-locked">Locked</span>
                            @endif
                        </td>
                        <td style="color:var(--text-muted)">{{ $cert->issued_at?->format('M d, Y') ?? '—' }}</td>
                        <td>
                            <div class="row-actions">
                                @if ($cert->status === 'pending_admin_approval')
                                    <button class="btn btn-success btn-sm" wire:click="approve({{ $cert->id }})" wire:confirm="Approve and unlock this certificate?">Approve</button>
                                    <button class="btn btn-danger-outline btn-sm" wire:click="reject({{ $cert->id }})">Reject</button>
                                @elseif ($cert->status === 'unlocked')
                                    <button class="btn btn-danger-outline btn-sm" wire:click="revoke({{ $cert->id }})" wire:confirm="Revoke this certificate? The student will lose access.">Revoke</button>
                                @else
                                    <span class="cc-note">Waiting on progress</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" style="text-align:center; color:var(--text-muted); padding:30px;">No certificates match these filters.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top:16px;">
        {{ $this->certificates->links() }}
    </div>

    {{-- Manual issue modal --}}
    @if ($showIssueModal)
        <div style="position:fixed; inset:0; background:rgba(0,0,0,.4); display:flex; align-items:center; justify-content:center; z-index:40;" wire:click.self="$set('showIssueModal', false)">
            <div style="background:var(--bg-card); border-radius:var(--radius); padding:24px; width:420px; box-shadow:var(--shadow);">
                <h3 style="margin-bottom:16px;">Issue manual certificate</h3>

                <label style="font-size:12.5px; color:var(--text-muted);">Student</label>
                <select wire:model="issueUserId" style="width:100%; padding:8px; margin:6px 0 14px; border:1px solid var(--input-border); border-radius:var(--radius-xs); background:var(--input-bg);">
                    <option value="">Select student...</option>
                    @foreach (\App\Models\User::orderBy('name')->limit(100)->get() as $user)
                        <option value="{{ $user->id }}">{{ $user->name }} — {{ $user->email }}</option>
                    @endforeach
                </select>
                @error('issueUserId') <div style="color:var(--danger); font-size:12px;">{{ $message }}</div> @enderror

                <label style="font-size:12.5px; color:var(--text-muted);">Course</label>
                <select wire:model="issueCourseId" style="width:100%; padding:8px; margin:6px 0 14px; border:1px solid var(--input-border); border-radius:var(--radius-xs); background:var(--input-bg);">
                    <option value="">Select course...</option>
                    @foreach ($this->courses as $course)
                        <option value="{{ $course->id }}">{{ $course->title }}</option>
                    @endforeach
                </select>
                @error('issueCourseId') <div style="color:var(--danger); font-size:12px;">{{ $message }}</div> @enderror

                <label style="font-size:12.5px; color:var(--text-muted);">Certificate type</label>
                <select wire:model="issueType" style="width:100%; padding:8px; margin:6px 0 20px; border:1px solid var(--input-border); border-radius:var(--radius-xs); background:var(--input-bg);">
                    <option value="course">Course</option>
                    <option value="level">Level</option>
                    <option value="demo">Demo</option>
                </select>

                <div style="display:flex; gap:10px; justify-content:flex-end;">
                    <button class="btn btn-ghost" wire:click="$set('showIssueModal', false)">Cancel</button>
                    <button class="btn btn-primary" wire:click="issueManually">Issue &amp; unlock</button>
                </div>
            </div>
        </div>
    @endif
</div>