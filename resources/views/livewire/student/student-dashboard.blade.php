


<div class="cc-wrap" >

    <div class="cc-header">
        <div class="cc-header-left">
            <div class="cc-icon">👋</div>
            <div>
                <div class="cc-title">Welcome back, {{ Auth::user()->name }}</div>
                <div class="cc-subtitle">
                    {{ $this->hasCourseEnrollment ? 'Your course progress, certificates & activity' : 'Your demo journey so far' }}
                </div>
            </div>
        </div>
    </div>

    @if (! $this->hasCourseEnrollment)
        {{-- ═══════════════════════════════════════
             DEMO-ONLY VIEW
        ═══════════════════════════════════════ --}}
        <div class="cc-section" style="background:var(--primary-glow); border-color:var(--brand-primary);">
            <div style="display:flex; gap:12px; align-items:flex-start;">
                <div style="font-size:20px;">🎬</div>
                <div>
                    <div style="font-weight:700; color:var(--text); margin-bottom:4px;">You're in Demo mode</div>
                    <div class="cc-hint">Complete your assigned demo tasks below to earn your subject demo certificate. Enroll in a full course anytime to unlock weekly progress tracking, live classes, and course certificates.</div>
                </div>
            </div>
        </div>

        {{-- Demo certificate status --}}
        @php $demoCert = $this->demoCertificate; @endphp
        <div class="cc-section">
            <div class="cc-section-title">Demo Certificate</div>
            <div style="display:flex; align-items:center; gap:16px; flex-wrap:wrap;">
                <div style="width:70px; height:70px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:28px;
                            background:{{ $demoCert?->isUnlocked() ? 'rgba(22,163,74,.12)' : 'var(--bg2)' }};">
                    {{ $demoCert?->isUnlocked() ? '🏆' : '🔒' }}
                </div>
                <div>
                    <div style="font-weight:700; color:var(--text); font-size:15px;">
                        {{ $demoCert?->isUnlocked() ? 'Unlocked!' : 'Locked' }}
                    </div>
                    <div class="cc-hint">
                        {{ $demoCert?->isUnlocked() ? 'Your demo certificate is ready to download.' : 'Submit your demo task and wait for admin approval to unlock this.' }}
                    </div>
                    @if ($demoCert?->isUnlocked())
                        <a href="{{ route('certificates.download', $demoCert) }}" class="cc-btn cc-btn-primary" style="margin-top:10px; padding:8px 16px; font-size:12.5px;">Download Certificate</a>
                    @endif
                </div>
            </div>
        </div>

        {{-- Assigned demo tasks --}}
        <div class="cc-section">
            <div class="cc-section-title">Your Demo Tasks</div>
            <div style="display:flex; flex-direction:column; gap:10px;">
                @forelse ($this->demoAssignments as $a)
                    @php $sub = $this->demoSubmissions->get($a->id); @endphp
                    <div style="display:flex; justify-content:space-between; align-items:center; background:var(--bg-card2); border:1px solid var(--line); border-radius:var(--radius-xs); padding:12px 14px; flex-wrap:wrap; gap:8px;">
                        <div>
                            <div style="font-weight:600; font-size:13.5px; color:var(--text);">{{ $a->demoTask?->title ?? 'Demo Task' }}</div>
                            <div class="cc-hint">Assigned {{ $a->assigned_at?->diffForHumans() }}</div>
                        </div>
                        @if ($sub)
                            <span class="cc-badge cc-badge-success">Submitted {{ $sub->submitted_at?->diffForHumans() }}</span>
                        @else
                            <span class="cc-badge cc-badge-muted">Not submitted yet</span>
                        @endif
                    </div>
                @empty
                    <div class="cc-empty">No demo tasks assigned yet — check back soon.</div>
                @endforelse
            </div>
        </div>

    @else
        {{-- ═══════════════════════════════════════
             COURSE-ENROLLED VIEW
        ═══════════════════════════════════════ --}}

        {{-- Course cards with pie chart --}}
        <div class="cc-course-grid" style="margin-bottom:20px;">
            @foreach ($this->courseProgressCards as $card)
                <div class="cc-course-tile" style="cursor:pointer; padding:16px;" wire:click="selectCourse({{ $card['course_id'] }})">
                    <div style="display:flex; align-items:center; gap:14px;">
                        {{-- Pie chart via conic-gradient --}}
                        <div style="width:64px; height:64px; border-radius:50%; flex-shrink:0; position:relative;
                                    background: conic-gradient(var(--brand-primary) {{ $card['percent'] }}%, var(--line) 0);">
                            <div style="position:absolute; inset:6px; border-radius:50%; background:var(--bg-card); display:flex; align-items:center; justify-content:center; font-size:13px; font-weight:800; color:var(--text);">
                                {{ $card['percent'] }}%
                            </div>
                        </div>
                        <div style="min-width:0;">
                            <div style="font-weight:700; font-size:13.5px; color:var(--text); overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">{{ $card['title'] }}</div>
                            <div class="cc-hint">{{ $card['category'] }}</div>
                            <div class="cc-hint">{{ $card['completed'] }}/{{ $card['total_items'] }} items done</div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Weekly bar chart for selected course --}}
        @if ($this->activeCourseWeeklyChart->isNotEmpty())
            <div class="cc-section">
                <div class="cc-section-title">Weekly Progress — {{ $this->courseProgressCards->firstWhere('course_id', $activeCourseId)['title'] ?? '' }}</div>
                <div style="display:flex; align-items:end; gap:8px; height:130px;">
                    @foreach ($this->activeCourseWeeklyChart as $w)
                        <div style="flex:1; display:flex; flex-direction:column; align-items:center; gap:6px; height:100%; justify-content:flex-end;">
                            <span style="font-size:10.5px; font-weight:700; color:var(--text-muted);">{{ $w['percent'] }}%</span>
                            <div style="width:100%; max-width:30px; height:{{ max(3, $w['percent']) }}%; border-radius:4px 4px 0 0; background:{{ $w['percent'] >= 100 ? 'var(--success)' : 'var(--brand-primary)' }};"></div>
                            <span style="font-size:10px; color:var(--text-muted);">W{{ $w['week_number'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Certificates --}}
        <div class="cc-section">
            <div class="cc-section-title">Your Certificates</div>
            <div class="cc-grid-3">
                @forelse ($this->certificates as $cert)
                    <div style="background:var(--bg-card2); border:1px solid var(--line); border-radius:var(--radius-xs); padding:14px; text-align:center;">
                        <div style="font-size:24px;">{{ $cert->isUnlocked() ? '🏆' : '🔒' }}</div>
                        <div style="font-weight:600; font-size:12.5px; color:var(--text); margin-top:6px;">{{ $cert->subjectTitle() }}</div>
                        <div class="cc-hint">{{ ucfirst($cert->type) }}</div>
                        @if ($cert->isUnlocked())
                            <a href="{{ route('certificates.download', $cert) }}" class="cc-btn cc-btn-outline" style="margin-top:8px; padding:5px 12px; font-size:11.5px;">Download</a>
                        @else
                            <span class="cc-badge cc-badge-muted" style="margin-top:8px;">Locked</span>
                        @endif
                    </div>
                @empty
                    <div class="cc-empty">No certificates yet — keep learning!</div>
                @endforelse
            </div>
        </div>
    @endif

    {{-- ═══════════════════════════════════════
         TRANSACTION HISTORY — shown for everyone
    ═══════════════════════════════════════ --}}
    @php $ps = $this->paymentSummary; @endphp
    <div class="cc-section">
        <div class="cc-section-title">💳 Transaction History</div>
        <div class="cc-grid-2" style="margin-bottom:16px;">
            <div>
                <div style="font-size:20px; font-weight:800; color:var(--brand-primary);">₹{{ number_format($ps['total_paid']) }}</div>
                <div class="cc-hint">Total Paid</div>
            </div>
            <div>
                <div style="font-size:20px; font-weight:800; color:var(--warning);">{{ $ps['pending'] }}</div>
                <div class="cc-hint">Pending Payments</div>
            </div>
        </div>
        <div class="cc-table-wrap">
            <table class="cc-table">
                <thead><tr><th>For</th><th>Amount</th><th>Status</th><th>Date</th></tr></thead>
                <tbody>
                    @forelse ($this->paymentHistory as $pay)
                        <tr>
                            <td data-label="For">{{ ucfirst($pay->type ?? '-') }} @if ($pay->course) — {{ $pay->course->title }} @endif</td>
                            <td data-label="Amount">₹{{ number_format($pay->amount) }}</td>
                            <td data-label="Status">
                                @if ($pay->status === 'success') <span class="cc-badge cc-badge-success">Paid</span>
                                @elseif ($pay->status === 'failed') <span class="cc-badge cc-badge-danger">Failed</span>
                                @else <span class="cc-badge cc-badge-muted">Pending</span> @endif
                            </td>
                            <td data-label="Date">{{ $pay->created_at?->format('d M, Y') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4"><div class="cc-empty">No transactions yet.</div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ═══════════════════════════════════════
         ATTENDANCE — pie + recent list
    ═══════════════════════════════════════ --}}
    @php
        $att = $this->attendanceSummary;
        $attTotal = max(1, $att['total']);
        $presentDeg = round($att['present'] / $attTotal * 360);
        $absentDeg = round($att['absent'] / $attTotal * 360);
    @endphp
    <div class="cc-section">
        <div class="cc-section-title">🗓️ My Attendance</div>
        <div style="display:flex; gap:24px; align-items:center; flex-wrap:wrap; margin-bottom:18px;">
            <div style="width:110px; height:110px; border-radius:50%; flex-shrink:0;
                        background: conic-gradient(
                            var(--success) 0deg {{ $presentDeg }}deg,
                            var(--danger) {{ $presentDeg }}deg {{ $presentDeg + $absentDeg }}deg,
                            var(--warning) {{ $presentDeg + $absentDeg }}deg 360deg
                        );">
            </div>
            <div style="display:flex; flex-direction:column; gap:6px; font-size:12.5px;">
                <span><span style="display:inline-block; width:9px; height:9px; border-radius:2px; background:var(--success); margin-right:6px;"></span>Present — {{ $att['present'] }}</span>
                <span><span style="display:inline-block; width:9px; height:9px; border-radius:2px; background:var(--danger); margin-right:6px;"></span>Absent — {{ $att['absent'] }}</span>
                <span><span style="display:inline-block; width:9px; height:9px; border-radius:2px; background:var(--warning); margin-right:6px;"></span>Leave/Half-day — {{ $att['leave'] + $att['half_day'] }}</span>
            </div>
        </div>

        <div class="cc-table-wrap">
            <table class="cc-table">
                <thead><tr><th>Date</th><th>Status</th><th>Check In</th><th>Check Out</th></tr></thead>
                <tbody>
                    @forelse ($this->attendanceRows as $row)
                        <tr>
                            <td data-label="Date">{{ $row->date?->format('d M, Y') }}</td>
                            <td data-label="Status">
                                @php
                                    $c = match($row->status) { 'present' => 'cc-badge-success', 'absent' => 'cc-badge-danger', default => 'cc-badge-muted' };
                                @endphp
                                <span class="cc-badge {{ $c }}">{{ ucfirst(str_replace('_',' ',$row->status)) }}</span>
                            </td>
                            <td data-label="Check In">{{ $row->check_in_at?->format('h:i A') ?? '—' }}</td>
                            <td data-label="Check Out">{{ $row->check_out_at?->format('h:i A') ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4"><div class="cc-empty">No attendance recorded yet.</div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>