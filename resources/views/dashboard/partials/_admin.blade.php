{{-- dashboard/partials/_admin.blade.php --}}
<section class="d-admin-hero">
    <h3>Admin Panel</h3>
    <p>{{ $panelDescription }}</p>
</section>

<div class="d-stats-grid">
    @foreach ($overviewCards as $i => $card)
        @php $cls = ['g', 't', 'o', ''][$i % 4] ?? ''; @endphp
        <div class="d-stat">
            <div class="d-stat-icon {{ $cls }}">{{ $card['code'] }}</div>
            <div>
                <b>{{ $card['value'] }}{{ $card['suffix'] ?? '' }}</b>
                <span>{{ $card['label'] }}</span>
            </div>
        </div>
    @endforeach
</div>