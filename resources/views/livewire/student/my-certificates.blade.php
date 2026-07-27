<div>
    <div class="page-head">
        <div>
            <h1>My Certificates</h1>
            <p>Track your progress and download certificates as you unlock them.</p>
        </div>
    </div>
{{-- 
    <div class="stat-row" style="grid-template-columns:repeat(3,1fr)">
        <div class="stat-card">
            <div class="val">{{ $this->stats['unlocked'] }}</div>
            <div class="lbl">Unlocked</div>
        </div>
        <div class="stat-card">
            <div class="val">{{ $this->stats['pending'] }}</div>
            <div class="lbl">Pending review</div>
        </div>
        <div class="stat-card">
            <div class="val">{{ $this->stats['in_progress'] }}</div>
            <div class="lbl">In progress</div>
        </div>
    </div> --}}

    <div class="cert-tabs">
        @foreach (['all' => 'All', 'course' => 'Course', 'week' => 'Week', 'level' => 'Level', 'demo' => 'Demo'] as $key => $label)
            <div class="cert-tab {{ $activeTab === $key ? 'active' : '' }}" wire:click="setTab('{{ $key }}')">{{ $label }}</div>
        @endforeach
    </div>

    <div class="cert-grid" wire:loading.class="opacity-50">
        @forelse ($this->certificates as $cert)
            @php $pct = $this->livePercent($cert); @endphp
            <div class="cert-card type-{{ $cert->type }}" wire:key="my-cert-{{ $cert->id }}">
                <div class="cc-head">
                    <div>
                        <h3>{{ $cert->subjectTitle() }}</h3>
                        <div class="cc-num">{{ $cert->certificate_number }}</div>
                    </div>
                </div>

                <div class="cc-progress">
                    <div class="bar"><span style="width: {{ $pct }}%"></span></div>
                    <div class="meta">
                        <span>{{ $pct }}% complete</span>
                        <span class="badge badge-type-{{ $cert->type }}">{{ ucfirst($cert->type) }}</span>
                    </div>
                </div>

                <div class="cc-foot">
                    @if ($cert->isUnlocked())
                        <button class="btn btn-primary btn-sm" wire:click="download({{ $cert->id }})">⬇ Download</button>
                        <span class="cc-note">Unlocked {{ $cert->issued_at?->format('M d, Y') }}</span>
                    @elseif ($cert->status === 'pending_admin_approval')
                        <span class="badge badge-status-pending">Pending approval</span>
                        <span class="cc-note">Awaiting admin review</span>
                    @else
                        <span class="badge badge-status-locked">Locked</span>
                        <span class="cc-note">Keep going to unlock</span>
                    @endif
                </div>
            </div>
        @empty
            <p style="color:var(--text-muted); padding:20px;">No certificates in this category yet.</p>
        @endforelse
    </div>
</div>