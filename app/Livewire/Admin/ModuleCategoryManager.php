<?php

namespace App\Livewire\Admin;

use App\Models\ModuleCategory;
use Illuminate\Support\Str;
use Livewire\Attributes\Validate;
use Livewire\Component;

class ModuleCategoryManager extends Component
{
    public bool $showModal = false;
    public ?int $editingId = null;

    #[Validate('required|string|min:2|max:100')]
    public string $name = '';

    #[Validate('nullable|string|max:60')]
    public string $icon = '';

    #[Validate('nullable|integer|min:0')]
    public int $sort_order = 0;

    public bool $status = true;

    public function render()
    {
        return view('livewire.admin.module-category-manager', [
            'categories' => ModuleCategory::withCount('modules')->orderBy('sort_order')->get(),
        ]);
    }

    public function openCreate(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function edit(int $id): void
    {
        $category = ModuleCategory::findOrFail($id);

        $this->editingId  = $category->id;
        $this->name       = $category->name;
        $this->icon       = $category->icon ?? '';
        $this->sort_order = $category->sort_order;
        $this->status     = $category->status;

        $this->showModal = true;
    }

    public function save(): void
    {
        $validated = $this->validate();

        ModuleCategory::updateOrCreate(
            ['id' => $this->editingId],
            [
                'name'       => $validated['name'],
                'slug'       => $this->editingId
                    ? ModuleCategory::find($this->editingId)->slug
                    : Str::slug($validated['name']) . '-' . Str::random(4),
                'icon'       => $validated['icon'] ?: null,
                'sort_order' => $validated['sort_order'] ?? 0,
                'status'     => $this->status,
            ]
        );

        session()->flash('success', $this->editingId ? 'Category updated.' : 'Category created.');

        $this->resetForm();
        $this->showModal = false;
    }

    public function toggleStatus(int $id): void
    {
        $category = ModuleCategory::findOrFail($id);
        $category->update(['status' => ! $category->status]);
    }

    public function delete(int $id): void
    {
        $category = ModuleCategory::withCount('modules')->findOrFail($id);

        if ($category->modules_count > 0) {
            session()->flash('error', 'Move or delete the modules in this category first.');

            return;
        }

        $category->delete();
        session()->flash('success', 'Category deleted.');
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetForm();
    }

    protected function resetForm(): void
    {
        $this->reset(['editingId', 'name', 'icon', 'sort_order']);
        $this->status = true;
        $this->resetErrorBag();
    }
}