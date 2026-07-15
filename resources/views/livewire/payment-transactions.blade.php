<div class="txn-page">
    {{-- ══ HEADER ══ --}}
    <div class="txn-header">
        <div>
            <h1>{{ $this->isAdmin() ? 'All Student Transactions' : 'My Transactions' }}</h1>
            <p>{{ $this->isAdmin() ? 'View and manage every enrollment payment on the platform.' : 'View and download your payment history and invoices.' }}</p>
        </div>
    </div>

    {{-- ══ STAT CARDS ══ --}}
    <div class="txn-stats">
        <div class="txn-stat-card">
            <div class="txn-stat-icon success"><i class="fas fa-wallet"></i></div>
            <div>
                <span>Total Paid</span>
                <strong>₹{{ number_format($totals['total_paid']) }}</strong>
            </div>
        </div>
        <div class="txn-stat-card">
            <div class="txn-stat-icon success"><i class="fas fa-check-circle"></i></div>
            <div>
                <span>Successful</span>
                <strong>{{ $totals['success_count'] }}</strong>
            </div>
        </div>
        <div class="txn-stat-card">
            <div class="txn-stat-icon warning"><i class="fas fa-clock"></i></div>
            <div>
                <span>Pending</span>
                <strong>{{ $totals['pending_count'] }}</strong>
            </div>
        </div>
        <div class="txn-stat-card">
            <div class="txn-stat-icon danger"><i class="fas fa-times-circle"></i></div>
            <div>
                <span>Failed</span>
                <strong>{{ $totals['failed_count'] }}</strong>
            </div>
        </div>
    </div>

    {{-- ══ FILTER BAR ══ --}}
    <div class="txn-filters">
        <div class="txn-filter-group txn-search">
            <i class="fas fa-search"></i>
            <input type="text" wire:model.live.debounce.400ms="search"
                   placeholder="Search name, email, invoice, txn id...">
        </div>

        <div class="txn-filter-group">
            <select wire:model.live="status">
                <option value="">All Status</option>
                <option value="success">Success</option>
                <option value="pending">Pending</option>
                <option value="failed">Failed</option>
            </select>
        </div>

        <div class="txn-filter-group">
            <select wire:model.live="gateway">
                <option value="">All Gateways</option>
                <option value="Razorpay">Razorpay</option>
                <option value="Direct">Direct</option>
            </select>
        </div>

        <div class="txn-filter-group">
            <input type="date" wire:model.live="dateFrom">
        </div>
        <div class="txn-filter-group">
            <input type="date" wire:model.live="dateTo">
        </div>

        <button class="txn-clear-btn" wire:click="resetFilters">
            <i class="fas fa-rotate-left"></i> Reset
        </button>
    </div>

    {{-- ══ TABLE ══ --}}
    <div class="txn-table-wrap">
        <table class="txn-table">
            <thead>
                <tr>
                    <th>Invoice</th>
                    @if ($this->isAdmin())
                        <th>Student</th>
                    @endif
                    <th>Amount</th>
                    <th>Gateway</th>
                    <th>Transaction ID</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th class="txn-actions-col">Invoice</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($payments as $payment)
                    <tr wire:key="payment-{{ $payment->id }}">
                        <td><span class="txn-invoice-no">{{ $payment->invoice_no ?? '—' }}</span></td>

                        @if ($this->isAdmin())
                            <td>
                                <div class="txn-student">
                                    <strong>{{ $payment->name }}</strong>
                                    <span>{{ $payment->email }}</span>
                                </div>
                            </td>
                        @endif

                        <td class="txn-amount">₹{{ number_format($payment->paid_amount ?? $payment->amount) }}</td>

                        <td>
                            <span class="txn-gateway-pill">{{ $payment->gateway }}</span>
                        </td>

                        <td>
                            <span class="txn-mono">{{ $payment->transaction_id ?? $payment->razorpay_payment_id ?? '—' }}</span>
                        </td>

                        <td>
                            @php
                                $statusClass = match($payment->status) {
                                    'success' => 'badge-success',
                                    'failed'  => 'badge-danger',
                                    'pending' => 'badge-warning',
                                    default   => 'badge-muted',
                                };
                            @endphp
                            <span class="txn-badge {{ $statusClass }}">
                                {{ ucfirst($payment->status) }}
                            </span>
                        </td>

                        <td class="txn-date">
                            {{ optional($payment->paid_at ?? $payment->created_at)->format('d M Y, h:i A') }}
                        </td>

                        <td class="txn-actions-col">
                            @if ($payment->status === 'success')
                                <a href="{{ route('invoice.download', $payment->id) }}"
                                   target="_blank"
                                   class="txn-download-btn"
                                   title="Download Invoice">
                                    <i class="fas fa-download"></i>
                                </a>
                            @else
                                <span class="txn-download-disabled" title="Invoice unavailable">
                                    <i class="fas fa-ban"></i>
                                </span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ $this->isAdmin() ? 7 : 6 }}" class="txn-empty">
                            <i class="fas fa-receipt"></i>
                            <p>No transactions found matching your filters.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- ══ PAGINATION ══ --}}
    <div class="txn-pagination">
        {{ $payments->links() }}
    </div>
</div>

<style>
    .txn-page {
        padding: 24px;
        color: var(--text);
    }

    .txn-header h1 {
        font-size: 24px;
        font-weight: 700;
        color: var(--text-main);
    }

    .txn-header p {
        color: var(--text-muted);
        margin-top: 4px;
        font-size: 14px;
    }

    /* ── Stat cards ── */
    .txn-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
        margin: 24px 0;
    }

    .txn-stat-card {
        background: var(--bg-card);
        border: 1px solid var(--line);
        border-radius: var(--radius-sm);
        box-shadow: var(--shadow-sm);
        padding: 18px;
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .txn-stat-card span {
        display: block;
        font-size: 12px;
        color: var(--text-muted);
        margin-bottom: 2px;
    }

    .txn-stat-card strong {
        font-size: 20px;
        color: var(--text-main);
    }

    .txn-stat-icon {
        width: 44px;
        height: 44px;
        border-radius: var(--radius-xs);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        flex-shrink: 0;
    }

    .txn-stat-icon.success { background: rgba(22, 163, 74, .12); color: var(--success); }
    .txn-stat-icon.warning { background: rgba(217, 119, 6, .12); color: var(--warning); }
    .txn-stat-icon.danger  { background: rgba(220, 38, 38, .12); color: var(--danger); }

    /* ── Filters ── */
    .txn-filters {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        background: var(--bg-card);
        border: 1px solid var(--line);
        border-radius: var(--radius-sm);
        padding: 14px;
        margin-bottom: 20px;
        align-items: center;
    }

    .txn-filter-group {
        display: flex;
        align-items: center;
        gap: 8px;
        background: var(--input-bg);
        border: 1px solid var(--input-border);
        border-radius: var(--radius-xs);
        padding: 8px 12px;
    }

    .txn-filter-group:focus-within {
        border-color: var(--input-focus);
    }

    .txn-filter-group i { color: var(--text-muted); font-size: 13px; }

    .txn-search { flex: 1; min-width: 220px; }

    .txn-filter-group input,
    .txn-filter-group select {
        border: none;
        background: transparent;
        color: var(--text);
        font-size: 13px;
        outline: none;
        width: 100%;
    }

    .txn-clear-btn {
        display: flex;
        align-items: center;
        gap: 6px;
        border: 1px solid var(--line);
        background: var(--bg-card2);
        color: var(--text-muted);
        border-radius: var(--radius-xs);
        padding: 9px 14px;
        font-size: 13px;
        font-weight: 500;
        transition: all .2s;
    }

    .txn-clear-btn:hover {
        color: var(--brand-primary);
        border-color: var(--brand-primary);
    }

    /* ── Table ── */
    .txn-table-wrap {
        background: var(--bg-card);
        border: 1px solid var(--line);
        border-radius: var(--radius-sm);
        box-shadow: var(--shadow-card);
        overflow-x: auto;
    }

    .txn-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 720px;
    }

    .txn-table thead th {
        text-align: left;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: .03em;
        color: var(--text-muted);
        background: var(--bg-card2);
        padding: 12px 16px;
        border-bottom: 1px solid var(--line);
    }

    .txn-table tbody td {
        padding: 14px 16px;
        border-bottom: 1px solid var(--line);
        font-size: 13.5px;
        vertical-align: middle;
    }

    .txn-table tbody tr:hover { background: var(--bg-card2); }
    .txn-table tbody tr:last-child td { border-bottom: none; }

    .txn-invoice-no { font-weight: 600; color: var(--brand-primary); }

    .txn-student strong { display: block; color: var(--text-main); }
    .txn-student span { font-size: 12px; color: var(--text-muted); }

    .txn-amount { font-weight: 700; color: var(--text-main); }

    .txn-mono {
        font-family: 'Courier New', monospace;
        font-size: 12px;
        color: var(--text-muted);
    }

    .txn-gateway-pill {
        background: var(--primary-glow);
        color: var(--brand-primary);
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 600;
    }

    .txn-date { color: var(--text-muted); font-size: 13px; }

    .txn-badge {
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 600;
        display: inline-block;
    }

    .badge-success { background: rgba(22, 163, 74, .12); color: var(--success); }
    .badge-danger  { background: rgba(220, 38, 38, .12); color: var(--danger); }
    .badge-warning { background: rgba(217, 119, 6, .12); color: var(--warning); }
    .badge-muted   { background: rgba(90, 113, 138, .12); color: var(--muted); }

    .txn-actions-col { text-align: center; width: 70px; }

    .txn-download-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 34px;
        height: 34px;
        border-radius: var(--radius-xs);
        background: var(--brand-primary);
        color: #fff;
        transition: transform .15s, box-shadow .15s;
    }

    .txn-download-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px var(--primary-glow);
    }

    .txn-download-disabled {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 34px;
        height: 34px;
        border-radius: var(--radius-xs);
        background: var(--bg-card2);
        color: var(--text-muted);
        opacity: .6;
    }

    .txn-empty {
        text-align: center;
        padding: 50px 20px;
        color: var(--text-muted);
    }

    .txn-empty i { font-size: 30px; margin-bottom: 10px; display: block; }

    .txn-pagination {
        margin-top: 18px;
    }

    @media (max-width: 640px) {
        .txn-filters { flex-direction: column; align-items: stretch; }
        .txn-filter-group { width: 100%; }
    }
</style>