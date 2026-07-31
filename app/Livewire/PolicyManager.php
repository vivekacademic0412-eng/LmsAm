<?php

namespace App\Livewire;

use App\Models\Policy;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class PolicyManager extends Component
{
    use WithPagination;

    /* ── Filters (bookmarkable via query string) ─────────────── */
    #[Url(history: true)]
    public string $search = '';

    #[Url(history: true)]
    public string $statusFilter = ''; // '', 'active', 'inactive'

    #[Url(history: true)]
    public int $perPage = 10;

    /* ── Modal / form state ───────────────────────────────────── */
    public bool $showModal = false;
    public bool $isEditing = false;
    public ?int $policyId = null;

    public string $code = '';
    public string $version = '';
    public string $title = '';
    public bool $is_active = true;
    public ?string $published_at = null;

    protected function rules(): array
    {
        return [
            'code'         => ['required', 'string', 'max:50', 'unique:policies,code,' . ($this->policyId ?? 'NULL') . ',id'],
            'version'      => ['required', 'string', 'max:20'],
            'title'        => ['required', 'string', 'max:255'],
            'is_active'    => ['boolean'],
            'published_at' => ['nullable', 'date'],
        ];
    }

    protected function messages(): array
    {
        return [
            'code.required'    => 'Policy code is required.',
            'code.unique'      => 'This policy code is already in use.',
            'version.required' => 'Please enter a version.',
            'title.required'   => 'Please enter a title.',
        ];
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatingPerPage(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->reset('search', 'statusFilter');
        $this->resetPage();
    }

    /* ── Real-time field validation as the user types/blurs ──── */
    public function updated($property): void
    {
        if (in_array($property, ['code', 'version', 'title', 'published_at'])) {
            $this->validateOnly($property);
        }
    }

    /* ── Modal open/close ─────────────────────────────────────── */
    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->isEditing = false;
        $this->showModal = true;
    }

    public function openEditModal(int $id): void
    {
        $policy = Policy::findOrFail($id);

        $this->policyId     = $policy->id;
        $this->code         = $policy->code;
        $this->version      = $policy->version;
        $this->title        = $policy->title;
        $this->is_active    = (bool) $policy->is_active;
        $this->published_at = optional($policy->published_at)->format('Y-m-d\TH:i');

        $this->isEditing = true;
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->reset(['policyId', 'code', 'version', 'title', 'published_at']);
        $this->is_active = true;
        $this->resetErrorBag();
    }

    /* ── Create / Update ──────────────────────────────────────── */
    public function save(): void
    {
        $validated = $this->validate();

        if ($this->isEditing && $this->policyId) {
            $policy = Policy::findOrFail($this->policyId);
            $policy->update($validated);
            $message = "Policy \"{$policy->title}\" updated successfully.";
        } else {
            $policy = Policy::create($validated);
            $message = "Policy \"{$policy->title}\" created successfully.";
        }

        $this->showModal = false;
        $this->resetForm();
        $this->resetPage();

        $this->dispatch('toast', type: 'success', message: $message);
    }

    /* ── Delete (confirmed via SweetAlert on the frontend) ───── */
    public function confirmDelete(int $id): void
    {
        $policy = Policy::findOrFail($id);

        $this->dispatch(
            'confirm-policy-delete',
            id: $policy->id,
            title: $policy->title,
        );
    }

    #[On('deletePolicyConfirmed')]
    public function delete(int $id): void
    {
        $policy = Policy::find($id);

        if (! $policy) {
            $this->dispatch('toast', type: 'error', message: 'Policy not found or already removed.');
            return;
        }

        $title = $policy->title;
        $policy->delete();

        $this->resetPage();
        $this->dispatch('toast', type: 'success', message: "Policy \"{$title}\" removed.");
    }

    public function toggleActive(int $id): void
    {
        $policy = Policy::findOrFail($id);
        $policy->update(['is_active' => ! $policy->is_active]);

        $status = $policy->is_active ? 'activated' : 'deactivated';
        $this->dispatch('toast', type: 'success', message: "Policy \"{$policy->title}\" {$status}.");
    }

    public function render()
    {
        $policies = Policy::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('title', 'like', "%{$this->search}%")
                      ->orWhere('code', 'like', "%{$this->search}%");
                });
            })
            ->when($this->statusFilter === 'active', fn ($query) => $query->where('is_active', true))
            ->when($this->statusFilter === 'inactive', fn ($query) => $query->where('is_active', false))
            ->latest('id')
            ->paginate($this->perPage);

        return view('livewire.policy-manager', [
            'policies' => $policies,
        ]);
    }
}