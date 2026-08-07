<div class="cc-wrap" >

    <div class="cc-header">
        <div class="cc-header-left">
            <div class="cc-icon">📊</div>
            <div>
                <div class="cc-title">Admin Dashboard</div>
                <div class="cc-subtitle">Live overview — demo funnel, payments, enrollments, submissions, attendance</div>
            </div>
        </div>
        <select wire:model.live="range" class="cc-select" style="max-width:160px;">
            <option value="7">Last 7 days</option>
            <option value="30">Last 30 days</option>
            <option value="90">Last 90 days</option>
        </select>
    </div>

    {{-- ═══════════ TOP KPI STRIP ═══════════ --}}
    <div class="cc-grid-3" style="grid-template-columns: repeat(4, minmax(0,1fr));">
        @php $k = $this->kpis; @endphp
        <div class="cc-section" style="text-align:center; margin-bottom:0;">
            <div style="font-size:24px; font-weight:800; color:var(--brand-primary);">{{ $k['users'] }}</div>
            <div class="cc-hint">Total Users</div>
        </div>
        <div class="cc-section" style="text-align:center; margin-bottom:0;">
            <div style="font-size:24px; font-weight:800; color:var(--success);">{{ $k['students'] }}</div>
            <div class="cc-hint">Students</div>
        </div>
        <div class="cc-section" style="text-align:center; margin-bottom:0;">
            <div style="font-size:24px; font-weight:800; color:var(--brand-accent);">{{ $k['trainers'] }}</div>
            <div class="cc-hint">Trainers</div>
        </div>
        <div class="cc-section" style="text-align:center; margin-bottom:0;">
            <div style="font-size:24px; font-weight:800; color:var(--brand-secondary);">{{ $k['demo_users'] }}</div>
            <div class="cc-hint">Demo Users</div>
        </div>
        <div class="cc-section" style="text-align:center; margin-bottom:0;">
            <div style="font-size:24px; font-weight:800; color:var(--text);">{{ $k['courses'] }}</div>
            <div class="cc-hint">Courses</div>
        </div>
        <div class="cc-section" style="text-align:center; margin-bottom:0;">
            <div style="font-size:24px; font-weight:800; color:var(--text);">{{ $k['categories'] }}</div>
            <div class="cc-hint">Subjects</div>
        </div>
        <div class="cc-section" style="text-align:center; margin-bottom:0;">
            <div style="font-size:24px; font-weight:800; color:var(--text);">{{ $k['enrollments'] }}</div>
            <div class="cc-hint">Enrollments</div>
        </div>
        <div class="cc-section" style="text-align:center; margin-bottom:0;">
            <div style="font-size:24px; font-weight:800; color:var(--success);">{{ $k['active_users'] }}</div>
            <div class="cc-hint">Active Users</div>
        </div>
    </div>

    {{-- ═══════════ PAYMENTS ═══════════ --}}
    @php $p = $this->paymentStats; @endphp
    <div class="cc-section" style="margin-top:20px;">
        <div class="cc-section-title">💳 Payments Overview</div>
        <div class="cc-grid-2" style="grid-template-columns: repeat(4, minmax(0,1fr)); margin-bottom:20px;">
            <div>
                <div style="font-size:20px; font-weight:800; color:var(--brand-primary);">₹{{ number_format($p['total_revenue']) }}</div>
                <div class="cc-hint">Total Revenue (all time)</div>
            </div>
            <div>
                <div style="font-size:20px; font-weight:800; color:var(--success);">₹{{ number_format($p['revenue_period']) }}</div>
                <div class="cc-hint">Revenue — last {{ $range }} days</div>
            </div>
            <div>
                <div style="font-size:20px; font-weight:800; color:var(--warning);">{{ $p['pending_count'] }}</div>
                <div class="cc-hint">Pending (₹{{ number_format($p['pending_amount']) }})</div>
            </div>
            <div>
                <div style="font-size:20px; font-weight:800; color:var(--danger);">{{ $p['failed_count'] }}</div>
                <div class="cc-hint">Failed Payments</div>
            </div>
        </div>

        <div class="cc-table-wrap">
            <table class="cc-table">
                <thead><tr><th>Student</th><th>For</th><th>Amount</th><th>Status</th><th>Date</th></tr></thead>
                <tbody>
                    @forelse ($this->recentPayments as $pay)
                        <tr>
                            <td data-label="Student">{{ $pay->name ?? $pay->user?->name }}</td>
                            <td data-label="For">
                                {{ ucfirst($pay->type ?? '-') }}
                                @if ($pay->category) — {{ $pay->category->name }} @endif
                            </td>
                            <td data-label="Amount">₹{{ number_format($pay->amount) }}</td>
                            <td data-label="Status">
                                @if ($pay->status === 'success') <span class="cc-badge cc-badge-success">Paid</span>
                                @elseif ($pay->status === 'failed') <span class="cc-badge cc-badge-danger">Failed</span>
                                @else <span class="cc-badge cc-badge-muted">Pending</span> @endif
                            </td>
                            <td data-label="Date">{{ $pay->created_at?->format('d M, h:i A') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5"><div class="cc-empty">No payments yet.</div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Revenue trend bar chart --}}
        @php
            $trend = $this->paymentTrend;
            $maxAmount = max(1, collect($trend)->max('amount'));
        @endphp
        <div style="margin-top:20px;">
            <div style="font-size:12.5px; font-weight:700; color:var(--text-muted); margin-bottom:10px;">Revenue Trend</div>
            <div style="display:flex; align-items:end; gap:6px; height:120px; overflow-x:auto; padding-bottom:4px;">
                @foreach ($trend as $point)
                    <div style="flex:1; min-width:28px; display:flex; flex-direction:column; align-items:center; gap:6px; height:100%; justify-content:flex-end;" title="₹{{ number_format($point['amount']) }}">
                        <span style="font-size:9.5px; color:var(--text-muted); white-space:nowrap;">{{ $point['amount'] > 0 ? '₹'.number_format($point['amount']/1000, 1).'k' : '' }}</span>
                        <div style="width:100%; max-width:26px; height:{{ max(3, (int) round($point['amount'] / $maxAmount * 100)) }}%; border-radius:4px 4px 0 0; background:linear-gradient(180deg, var(--brand-primary), var(--brand-secondary));"></div>
                        <span style="font-size:9.5px; color:var(--text-muted); white-space:nowrap;">{{ $point['label'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ═══════════ DEMO FUNNEL + LATEST DEMO SUBMISSIONS ═══════════ --}}
    <div class="cc-selector-row" style="grid-template-columns: 1fr 1fr; margin-top:20px;">
        @php $d = $this->demoFunnel; @endphp
        <div class="cc-section" style="margin-bottom:0;">
            <div class="cc-section-title">🎯 Demo Funnel</div>

            @php
                $funnelMax = max(1, $d['demo_users']);
                $funnelSteps = [
                    ['label' => 'Demo Users', 'value' => $d['demo_users'], 'color' => 'var(--brand-secondary)'],
                    ['label' => 'Tasks Assigned', 'value' => $d['tasks_assigned'], 'color' => 'var(--brand-primary)'],
                    ['label' => 'Submitted', 'value' => $d['tasks_submitted'], 'color' => 'var(--success)'],
                    ['label' => 'Pending Review', 'value' => $d['pending_review'], 'color' => 'var(--warning)'],
                ];
            @endphp
            <div style="display:flex; flex-direction:column; gap:10px;">
                @foreach ($funnelSteps as $step)
                    <div>
                        <div style="display:flex; justify-content:space-between; font-size:12px; margin-bottom:4px;">
                            <span style="color:var(--text-muted); font-weight:600;">{{ $step['label'] }}</span>
                            <span style="font-weight:700; color:var(--text);">{{ $step['value'] }}</span>
                        </div>
                        <div style="height:10px; background:var(--line); border-radius:999px; overflow:hidden;">
                            <div style="height:100%; width:{{ max(3, (int) round($step['value'] / $funnelMax * 100)) }}%; background:{{ $step['color'] }}; border-radius:999px;"></div>
                        </div>
                    </div>
                @endforeach
            </div>
            <a href="{{ route('demo-tasks.assign-page') }}" class="cc-btn cc-btn-outline" style="margin-top:14px;">Manage Demo Tasks</a>
        </div>

        <div class="cc-section" style="margin-bottom:0;">
            <div class="cc-section-title">Latest Demo Submissions</div>
            <div style="display:flex; flex-direction:column; gap:8px;">
                @forelse ($this->latestDemoSubmissions as $sub)
                    <div style="display:flex; justify-content:space-between; align-items:center; background:var(--bg-card2); border:1px solid var(--line); border-radius:var(--radius-xs); padding:10px 12px;">
                        <div>
                            <div style="font-weight:600; font-size:13px; color:var(--text);">{{ $sub->assignment?->user?->name ?? '—' }}</div>
                            <div class="cc-hint">{{ $sub->assignment?->demoTask?->title ?? 'Demo Task' }}</div>
                        </div>
                        <span class="cc-badge cc-badge-accent">{{ $sub->submitted_at?->diffForHumans() }}</span>
                    </div>
                @empty
                    <div class="cc-empty">No demo submissions yet.</div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- ═══════════ PER-COURSE STUDENT PROGRESS ═══════════ --}}
    <div class="cc-section" style="margin-top:20px;">
        <div class="cc-section-title">📈 Student Progress per Course</div>
        <div style="display:flex; flex-direction:column; gap:14px;">
            @forelse ($this->courseProgressStats as $row)
                <div>
                    <div style="display:flex; justify-content:space-between; align-items:baseline; margin-bottom:5px; gap:10px;">
                        <span style="font-size:13px; font-weight:600; color:var(--text);">{{ $row['title'] }}</span>
                        <span style="font-size:11.5px; color:var(--text-muted); white-space:nowrap;">{{ $row['enrollments'] }} enrolled</span>
                    </div>
                    <div style="display:flex; align-items:center; gap:10px;">
                        <div style="flex:1; height:12px; background:var(--line); border-radius:999px; overflow:hidden;">
                            <div style="height:100%; width:{{ $row['avg_progress'] }}%; border-radius:999px; background:{{ $row['avg_progress'] >= 75 ? 'var(--success)' : ($row['avg_progress'] >= 40 ? 'var(--brand-primary)' : 'var(--warning)') }};"></div>
                        </div>
                        <span style="font-size:12.5px; font-weight:700; color:var(--text); min-width:36px; text-align:right;">{{ $row['avg_progress'] }}%</span>
                    </div>
                </div>
            @empty
                <div class="cc-empty">No enrolled courses with progress data yet.</div>
            @endforelse
        </div>
    </div>

    {{-- ═══════════ ENROLLMENTS + STUDENT TASK SUBMISSIONS ═══════════ --}}
    <div class="cc-selector-row" style="grid-template-columns: 1fr 1fr; margin-top:20px;">
        <div class="cc-section" style="margin-bottom:0;">
            <div class="cc-section-title">🎓 Recent Enrollments</div>
            <div style="display:flex; flex-direction:column; gap:8px;">
                @forelse ($this->recentEnrollments as $e)
                    <div style="display:flex; justify-content:space-between; align-items:center; background:var(--bg-card2); border:1px solid var(--line); border-radius:var(--radius-xs); padding:10px 12px;">
                        <div>
                            <div style="font-weight:600; font-size:13px; color:var(--text);">{{ $e->student?->name }}</div>
                            <div class="cc-hint">{{ $e->course?->title }}</div>
                        </div>
                        <span class="cc-badge cc-badge-muted">{{ $e->trainer?->name ?? 'Unassigned' }}</span>
                    </div>
                @empty
                    <div class="cc-empty">No enrollments yet.</div>
                @endforelse
            </div>
        </div>

        <div class="cc-section" style="margin-bottom:0;">
            <div class="cc-section-title">📝 Latest Task/Quiz Submissions</div>
            <div style="display:flex; flex-direction:column; gap:8px;">
                @forelse ($this->latestStudentSubmissions as $s)
                    <div style="display:flex; justify-content:space-between; align-items:center; background:var(--bg-card2); border:1px solid var(--line); border-radius:var(--radius-xs); padding:10px 12px;">
                        <div>
                            <div style="font-weight:600; font-size:13px; color:var(--text);">{{ $s->enrollment?->student?->name }}</div>
                            <div class="cc-hint">{{ $s->enrollment?->course?->title }} — {{ $s->sessionItem?->title }}</div>
                        </div>
                        <span class="cc-badge cc-badge-accent">{{ $s->submitted_at?->diffForHumans() }}</span>
                    </div>
                @empty
                    <div class="cc-empty">No submissions yet.</div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- ═══════════ ATTENDANCE ═══════════ --}}
    @php $a = $this->attendanceToday; @endphp
    <div class="cc-section" style="margin-top:20px;">
        <div class="cc-section-title">🗓️ Attendance — Today</div>
        <div class="cc-grid-2" style="grid-template-columns: repeat(5, minmax(0,1fr)); margin-bottom:20px;">
            <div><div style="font-size:20px; font-weight:800; color:var(--success);">{{ $a['present'] }}</div><div class="cc-hint">Present</div></div>
            <div><div style="font-size:20px; font-weight:800; color:var(--danger);">{{ $a['absent'] }}</div><div class="cc-hint">Absent</div></div>
            <div><div style="font-size:20px; font-weight:800; color:var(--warning);">{{ $a['leave'] }}</div><div class="cc-hint">Leave</div></div>
            <div><div style="font-size:20px; font-weight:800;">{{ $a['half_day'] }}</div><div class="cc-hint">Half Day</div></div>
            <div><div style="font-size:20px; font-weight:800; color:var(--brand-accent);">{{ $a['pending_approval'] }}</div><div class="cc-hint">Pending Approval</div></div>
        </div>

        <div class="cc-table-wrap">
            <table class="cc-table">
                <thead><tr><th>User</th><th>Date</th><th>Status</th><th>Approval</th><th>Marked By</th></tr></thead>
                <tbody>
                    @forelse ($this->recentAttendance as $att)
                        <tr>
                            <td data-label="User">{{ $att->user?->name ?? '—' }}</td>
                            <td data-label="Date">{{ $att->date?->format('d M, Y') }}</td>
                            <td data-label="Status">
                                @php
                                    $statusColor = match($att->status) {
                                        'present' => 'cc-badge-success', 'absent' => 'cc-badge-danger',
                                        'leave' => 'cc-badge-accent', default => 'cc-badge-muted',
                                    };
                                @endphp
                                <span class="cc-badge {{ $statusColor }}">{{ ucfirst(str_replace('_',' ',$att->status)) }}</span>
                            </td>
                            <td data-label="Approval">
                                @if ($att->approval_status === 'approved') <span class="cc-badge cc-badge-success">Approved</span>
                                @elseif ($att->approval_status === 'rejected') <span class="cc-badge cc-badge-danger">Rejected</span>
                                @else <span class="cc-badge cc-badge-muted">Pending</span> @endif
                            </td>
                            <td data-label="Marked By">{{ ucfirst($att->marked_by ?? '-') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5"><div class="cc-empty">No attendance records yet.</div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Attendance trend — stacked present/absent/leave, last 7 days --}}
        @php
            $attTrend = $this->attendanceTrend;
            $attMax = max(1, collect($attTrend)->map(fn($p) => $p['present'] + $p['absent'] + $p['leave'])->max());
        @endphp
        <div style="margin-top:20px;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
                <span style="font-size:12.5px; font-weight:700; color:var(--text-muted);">7-Day Attendance Trend</span>
                <div style="display:flex; gap:12px; font-size:11px; color:var(--text-muted);">
                    <span><span style="display:inline-block; width:8px; height:8px; border-radius:2px; background:var(--success); margin-right:4px;"></span>Present</span>
                    <span><span style="display:inline-block; width:8px; height:8px; border-radius:2px; background:var(--danger); margin-right:4px;"></span>Absent</span>
                    <span><span style="display:inline-block; width:8px; height:8px; border-radius:2px; background:var(--warning); margin-right:4px;"></span>Leave</span>
                </div>
            </div>
            <div style="display:flex; align-items:end; gap:10px; height:130px;">
                @foreach ($attTrend as $point)
                    @php $total = $point['present'] + $point['absent'] + $point['leave']; @endphp
                    <div style="flex:1; display:flex; flex-direction:column; align-items:center; gap:6px; height:100%; justify-content:flex-end;">
                        <div style="width:100%; max-width:32px; height:{{ max(3, (int) round($total / $attMax * 100)) }}%; border-radius:4px 4px 0 0; overflow:hidden; display:flex; flex-direction:column-reverse;">
                            @if ($point['present'] > 0)
                                <div style="background:var(--success); height:{{ $total > 0 ? round($point['present']/$total*100) : 0 }}%;"></div>
                            @endif
                            @if ($point['leave'] > 0)
                                <div style="background:var(--warning); height:{{ $total > 0 ? round($point['leave']/$total*100) : 0 }}%;"></div>
                            @endif
                            @if ($point['absent'] > 0)
                                <div style="background:var(--danger); height:{{ $total > 0 ? round($point['absent']/$total*100) : 0 }}%;"></div>
                            @endif
                        </div>
                        <span style="font-size:10px; color:var(--text-muted);">{{ $point['label'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>