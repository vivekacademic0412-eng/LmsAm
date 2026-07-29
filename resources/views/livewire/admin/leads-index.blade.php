<div class="leads-index">

    {{-- Filter bar --}}
    <div class="leads-filters" style="
        display:flex; flex-wrap:wrap; gap:12px; align-items:center;
        background:var(--bg-card); border:1px solid var(--line);
        border-radius:var(--radius-sm); padding:16px; margin-bottom:16px;
        box-shadow:var(--shadow-sm);
    ">
        <input
            type="text"
            wire:model.live.debounce.400ms="search"
            placeholder="Search name or email…"
            style="
                flex:1; min-width:200px; padding:10px 14px;
                background:var(--input-bg); border:1px solid var(--input-border);
                border-radius:var(--radius-xs); color:var(--text); font-size:14px;
            "
        >

        <select wire:model.live="verifiedFilter" style="
            padding:10px 14px; background:var(--input-bg);
            border:1px solid var(--input-border); border-radius:var(--radius-xs);
            color:var(--text); font-size:14px;
        ">
            <option value="all">All emails</option>
            <option value="verified">Verified</option>
            <option value="unverified">Unverified</option>
        </select>

        <select wire:model.live="sourceFilter" style="
            padding:10px 14px; background:var(--input-bg);
            border:1px solid var(--input-border); border-radius:var(--radius-xs);
            color:var(--text); font-size:14px;
        ">
            <option value="all">All sources</option>
            @foreach ($this->sourceOptions as $opt)
                <option value="{{ $opt['value'] }}">{{ $opt['label'] }}</option>
            @endforeach
        </select>

        <select wire:model.live="leadTypeFilter" style="
            padding:10px 14px; background:var(--input-bg);
            border:1px solid var(--input-border); border-radius:var(--radius-xs);
            color:var(--text); font-size:14px;
        ">
            <option value="all">All lead types</option>
            @foreach ($this->leadTypes as $type)
                <option value="{{ $type }}">{{ ucfirst($type) }}</option>
            @endforeach
        </select>

        <button wire:click="resetFilters" style="
            padding:10px 16px; background:transparent; border:1px solid var(--line);
            border-radius:var(--radius-xs); color:var(--text-muted); font-size:14px;
        ">
            Reset
        </button>

        <span style="margin-left:auto; color:var(--text-muted); font-size:13px;">
            {{ $leads->total() }} lead{{ $leads->total() === 1 ? '' : 's' }}
        </span>
    </div>

    {{-- Table --}}
    <div style="
        background:var(--bg-card); border:1px solid var(--line);
        border-radius:var(--radius-sm); overflow:hidden; box-shadow:var(--shadow-sm);
    ">
        <table style="width:100%; border-collapse:collapse; font-size:14px;">
            <thead>
                <tr style="background:var(--bg-card2); border-bottom:1px solid var(--line);">
                    @php
                        $cols = [
                            'name'     => 'Name',
                            'email'    => 'Email',
                            'lead_type'=> 'Type',
                            'status'   => 'Status',
                        ];
                    @endphp
                    @foreach ($cols as $col => $label)
                        <th wire:click="sortByColumn('{{ $col }}')" style="
                            text-align:left; padding:12px 16px; cursor:pointer;
                            color:var(--text-muted); font-weight:600; user-select:none;
                        ">
                            {{ $label }}
                            @if ($sortBy === $col)
                                <span>{{ $sortDir === 'asc' ? '↑' : '↓' }}</span>
                            @endif
                        </th>
                    @endforeach
                    <th style="text-align:left; padding:12px 16px; color:var(--text-muted); font-weight:600;">Email Verified</th>
                    <th style="text-align:left; padding:12px 16px; color:var(--text-muted); font-weight:600;">Source</th>
                    <th wire:click="sortByColumn('created_at')" style="
                        text-align:left; padding:12px 16px; cursor:pointer;
                        color:var(--text-muted); font-weight:600;
                    ">
                        Received
                        @if ($sortBy === 'created_at')
                            <span>{{ $sortDir === 'asc' ? '↑' : '↓' }}</span>
                        @endif
                    </th>
                </tr>
            </thead>
            <tbody>
                @forelse ($leads as $lead)
                    <tr style="border-bottom:1px solid var(--line);">
                        <td style="padding:12px 16px; color:var(--text);">{{ $lead->name }}</td>
                        <td style="padding:12px 16px; color:var(--text);">{{ $lead->email }}</td>
                        <td style="padding:12px 16px; color:var(--text);">{{ $lead->lead_type ?? '—' }}</td>
                        <td style="padding:12px 16px; color:var(--text);">{{ $lead->status ?? '—' }}</td>
                        <td style="padding:12px 16px;">
                            @if ($lead->email_verified_at)
                                <span style="
                                    display:inline-block; padding:3px 10px; border-radius:999px;
                                    background:rgba(22,163,74,.12); color:var(--success);
                                    font-size:12px; font-weight:600;
                                ">Verified</span>
                            @else
                                <span style="
                                    display:inline-block; padding:3px 10px; border-radius:999px;
                                    background:rgba(220,38,38,.10); color:var(--danger);
                                    font-size:12px; font-weight:600;
                                ">Unverified</span>
                            @endif
                        </td>
                        <td style="padding:12px 16px;">
                            @if ($lead->trafficSource)
                                <span style="
                                    display:inline-flex; align-items:center; gap:6px;
                                    color:var(--text);
                                ">
                                    <span style="
                                        width:8px; height:8px; border-radius:50%;
                                        background:{{ $lead->trafficSource->source_color }};
                                        display:inline-block;
                                    "></span>
                                    {{ $lead->trafficSource->source_label }}
                                </span>
                            @else
                                <span style="color:var(--text-muted);">—</span>
                            @endif
                        </td>
                        <td style="padding:12px 16px; color:var(--text-muted);">
                            {{ $lead->created_at?->format('M j, Y g:i A') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="padding:32px; text-align:center; color:var(--text-muted);">
                            No leads match these filters.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div style="margin-top:16px;">
        {{ $leads->links('pagination.custom') }}
    </div>
</div>