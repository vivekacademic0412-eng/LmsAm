<?php

namespace App\Livewire;

use App\Models\Payment;
use Livewire\Component;
use Livewire\WithPagination;

class PaymentTransactions extends Component
{
    use WithPagination;

    public $search = '';
    public $status = '';
    public $gateway = '';
    public $dateFrom = '';
    public $dateTo = '';
    public $perPage = 10;

    protected $queryString = [
        'search'   => ['except' => ''],
        'status'   => ['except' => ''],
        'gateway'  => ['except' => ''],
        'dateFrom' => ['except' => ''],
        'dateTo'   => ['except' => ''],
    ];

    public function updatingSearch()   { $this->resetPage(); }
    public function updatingStatus()   { $this->resetPage(); }
    public function updatingGateway()  { $this->resetPage(); }
    public function updatingDateFrom() { $this->resetPage(); }
    public function updatingDateTo()   { $this->resetPage(); }

    public function resetFilters()
    {
        $this->reset(['search', 'status', 'gateway', 'dateFrom', 'dateTo']);
        $this->resetPage();
    }

    /**
     * Adjust this to match your actual role system
     * (e.g. Spatie: auth()->user()->hasRole('admin'))
     */
    public function isAdmin(): bool
    {
        return auth()->user()?->role === 'admin';
    }

    public function getPaymentsProperty()
    {
        $query = Payment::query()->latest('paid_at');

        // Role-based visibility
        if (! $this->isAdmin()) {
            $query->where('user_id', auth()->id());
        }

        if ($this->search !== '') {
            $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('email', 'like', "%{$this->search}%")
                  ->orWhere('invoice_no', 'like', "%{$this->search}%")
                  ->orWhere('transaction_id', 'like', "%{$this->search}%")
                  ->orWhere('razorpay_payment_id', 'like', "%{$this->search}%");
            });
        }

        if ($this->status !== '') {
            $query->where('status', $this->status);
        }

        if ($this->gateway !== '') {
            $query->where('gateway', $this->gateway);
        }

        if ($this->dateFrom !== '') {
            $query->whereDate('paid_at', '>=', $this->dateFrom);
        }

        if ($this->dateTo !== '') {
            $query->whereDate('paid_at', '<=', $this->dateTo);
        }

        return $query->paginate($this->perPage);
    }

    public function getTotalsProperty()
    {
        $base = Payment::query();

        if (! $this->isAdmin()) {
            $base->where('user_id', auth()->id());
        }

        return [
            'total_paid'   => (clone $base)->where('status', 'success')->sum('paid_amount'),
            'success_count' => (clone $base)->where('status', 'success')->count(),
            'failed_count'  => (clone $base)->where('status', 'failed')->count(),
            'pending_count' => (clone $base)->where('status', 'pending')->count(),
        ];
    }

    public function render()
    {
        return view('livewire.payment-transactions', [
            'payments' => $this->payments,
            'totals'   => $this->totals,
        ]);
    }
}