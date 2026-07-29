<?php

namespace App\Livewire\Admin;

use App\Models\Lead;
use App\Models\TrafficSource;
use Livewire\Component;
use Livewire\WithPagination;

class LeadsIndex extends Component
{
    use WithPagination;

    /** Text search — matches name or email */
    public string $search = '';

    /** 'all' | 'verified' | 'unverified' */
    public string $verifiedFilter = 'all';

    /** Selected traffic source key, or 'all' */
    public string $sourceFilter = 'all';

    /** Selected lead_type, or 'all' */
    public string $leadTypeFilter = 'all';

    public string $sortBy = 'created_at';
    public string $sortDir = 'desc';

    public int $perPage = 20;

    protected $paginationTheme = 'tailwind';

    // Reset to page 1 whenever a filter changes so results stay in sync
    protected $queryString = [
        'search'         => ['except' => ''],
        'verifiedFilter' => ['except' => 'all'],
        'sourceFilter'   => ['except' => 'all'],
        'leadTypeFilter' => ['except' => 'all'],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingVerifiedFilter()
    {
        $this->resetPage();
    }

    public function updatingSourceFilter()
    {
        $this->resetPage();
    }

    public function updatingLeadTypeFilter()
    {
        $this->resetPage();
    }

    public function sortByColumn(string $column)
    {
        if ($this->sortBy === $column) {
            $this->sortDir = $this->sortDir === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDir = 'asc';
        }
    }

    public function resetFilters()
    {
        $this->reset(['search', 'verifiedFilter', 'sourceFilter', 'leadTypeFilter']);
        $this->resetPage();
    }

    /**
     * Distinct lead_type values present in the table, for the filter dropdown.
     */
    public function getLeadTypesProperty()
    {
        return Lead::query()
            ->whereNotNull('lead_type')
            ->distinct()
            ->orderBy('lead_type')
            ->pluck('lead_type');
    }

    /**
     * Traffic sources for the filter dropdown — known sources (with labels/colors)
     * merged with any distinct raw source values actually present on leads
     * via their traffic_source relation.
     */
    public function getSourceOptionsProperty()
    {
        $known = collect(TrafficSource::KNOWN_SOURCES)->map(function ($meta, $key) {
            return ['value' => $key, 'label' => $meta['label']];
        })->values();

        $present = TrafficSource::query()
            ->whereIn('id', Lead::query()->whereNotNull('traffic_source_id')->pluck('traffic_source_id'))
            ->pluck('source')
            ->filter()
            ->map(fn ($s) => strtolower($s))
            ->unique();

        $extra = $present->diff($known->pluck('value'))->map(function ($key) {
            return ['value' => $key, 'label' => str(str_replace(['-', '_'], ' ', $key))->title()];
        })->values();

        return $known->merge($extra)->sortBy('label')->values();
    }

    public function render()
    {
        $query = Lead::query()->with('trafficSource');

        // Search: name or email
        if ($this->search !== '') {
            $term = '%' . $this->search . '%';
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', $term)
                  ->orWhere('email', 'like', $term);
            });
        }

        // Email verified filter
        if ($this->verifiedFilter === 'verified') {
            $query->whereNotNull('email_verified_at');
        } elseif ($this->verifiedFilter === 'unverified') {
            $query->whereNull('email_verified_at');
        }

        // Lead type filter
        if ($this->leadTypeFilter !== 'all') {
            $query->where('lead_type', $this->leadTypeFilter);
        }

        // Source filter — via the related traffic_source.source column
        if ($this->sourceFilter !== 'all') {
            $query->whereHas('trafficSource', function ($q) {
                $q->whereRaw('LOWER(source) = ?', [strtolower($this->sourceFilter)]);
            });
        }

        $query->orderBy($this->sortBy, $this->sortDir);

        $leads = $query->paginate($this->perPage);

        return view('livewire.admin.leads-index', [
            'leads' => $leads,
        ]);
    }
}