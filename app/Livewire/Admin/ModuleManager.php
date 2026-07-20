<?php

namespace App\Livewire\Admin;

use App\Models\Module;
use App\Models\ModuleCategory;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Validate;
use Livewire\Component;

class ModuleManager extends Component
{
    public bool $showModal = false;
    public ?int $editingId = null;

    #[Validate('required|exists:module_categories,id')]
    public ?int $category_id = null;

    #[Validate('nullable|exists:modules,id')]
    public ?int $parent_id = null;

    #[Validate('required|string|max:60|regex:/^[a-z0-9._-]+$/')]
    public string $module_key = '';

    #[Validate('required|string|max:100')]
    public string $label = '';

    #[Validate('nullable|string|max:60')]
    public string $icon = '';

    #[Validate('nullable|string|max:150')]
    public string $route = '';

    #[Validate('nullable|integer|min:0')]
    public int $sort_order = 0;

    public bool $status = true;

    protected array $messages = [
        'module_key.regex' => 'Module key can only contain lowercase letters, numbers, dots, dashes and underscores (e.g. courses.builder).',
    ];

    public function render()
    {
        $tree = ModuleCategory::where('status', true)
            ->orderBy('sort_order')
            ->with(['modules.children'])
            ->get();

        return view('livewire.admin.module-manager', [
            'tree'       => $tree,
            'categories' => ModuleCategory::orderBy('sort_order')->get(),
            // parent options = top-level modules only, so we never allow 4th-level nesting
            'parentOptions' => $this->category_id
                ? Module::where('category_id', $this->category_id)->whereNull('parent_id')->orderBy('sort_order')->get()
                : collect(),
        ]);
    }

    public function updatedCategoryId(): void
    {
        // switching category invalidates a previously selected parent from another category
        $this->parent_id = null;
    }

    public function openCreate(?int $categoryId = null, ?int $parentId = null): void
    {
        $this->resetForm();
        $this->category_id = $categoryId;
        $this->parent_id = $parentId;
        $this->showModal = true;
    }

    public function edit(int $id): void
    {
        $module = Module::findOrFail($id);

        $this->editingId   = $module->id;
        $this->category_id = $module->category_id;
        $this->parent_id   = $module->parent_id;
        $this->module_key  = $module->module_key;
        $this->label       = $module->label;
        $this->icon        = $module->icon ?? '';
        $this->route       = $module->route ?? '';
        $this->sort_order  = $module->sort_order;
        $this->status      = $module->status;

        $this->showModal = true;
    }

    public function save(): void
    {
        $rules = [
            'category_id' => 'required|exists:module_categories,id',
            'parent_id'   => 'nullable|exists:modules,id',
            'module_key'  => [
                'required', 'string', 'max:60', 'regex:/^[a-z0-9._-]+$/',
                Rule::unique('modules', 'module_key')->ignore($this->editingId),
            ],
            'label'      => 'required|string|max:100',
            'icon'       => 'nullable|string|max:60',
            'route'      => 'nullable|string|max:150',
            'sort_order' => 'nullable|integer|min:0',
        ];

        $validated = $this->validate($rules);

        // A child module's parent must live in the same category — keep the tree consistent.
        if ($this->parent_id) {
            $parent = Module::find($this->parent_id);
            if (! $parent || $parent->category_id !== (int) $this->category_id) {
                $this->addError('parent_id', 'Parent module must belong to the selected category.');
                return;
            }
            if ($parent->parent_id !== null) {
                $this->addError('parent_id', 'A child module cannot itself be nested under another child module.');
                return;
            }
        }

        Module::updateOrCreate(
            ['id' => $this->editingId],
            [
                'category_id' => $validated['category_id'],
                'parent_id'   => $this->parent_id,
                'module_key'  => $validated['module_key'],
                'label'       => $validated['label'],
                'icon'        => $validated['icon'] ?: null,
                'route'       => $validated['route'] ?: null,
                'sort_order'  => $validated['sort_order'] ?? 0,
                'status'      => $this->status,
            ]
        );

        session()->flash('success', $this->editingId ? 'Module updated.' : 'Module created.');

        $this->resetForm();
        $this->showModal = false;
    }

    public function toggleStatus(int $id): void
    {
        $module = Module::findOrFail($id);
        $module->update(['status' => ! $module->status]);
    }

    public function delete(int $id): void
    {
        $module = Module::withCount('children')->findOrFail($id);

        if ($module->children_count > 0) {
            session()->flash('error', 'Delete or move the child modules first.');
            return;
        }

        $module->delete();
        session()->flash('success', 'Module deleted.');
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetForm();
    }

    protected function resetForm(): void
    {
        $this->reset(['editingId', 'category_id', 'parent_id', 'module_key', 'label', 'icon', 'route', 'sort_order']);
        $this->status = true;
        $this->resetErrorBag();
    }
}