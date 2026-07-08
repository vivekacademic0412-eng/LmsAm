<?php

namespace App\Livewire\Admin;

use App\Models\Brochure;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;

class BrochureAdmin extends Component
{
    use WithFileUploads;

    public $brochures;

    // ── Form state ──────────────────────────────────────────────
    public ?int $editingId = null;
    public string $title   = '';
    public $file           = null; // new PDF upload
    public bool $is_active = true;

    public function mount(): void
    {
        $this->loadBrochures();
    }

    protected function loadBrochures(): void
    {
        $this->brochures = Brochure::orderBy('sort_order')->orderByDesc('created_at')->get();
    }

    protected function rules(): array
    {
        return [
            'title'     => 'required|string|max:150',
            'file'      => [
                Rule::requiredIf($this->editingId === null),
                'nullable',
                'file',
                'mimes:pdf',
                'max:10240', // 10 MB
            ],
            'is_active' => 'boolean',
        ];
    }

    protected array $messages = [
        'title.required' => 'Please give the brochure a title.',
        'file.required'  => 'Please upload a PDF file.',
        'file.mimes'     => 'The brochure must be a PDF file.',
        'file.max'       => 'The PDF must be smaller than 10 MB.',
    ];

    // ── Form actions ─────────────────────────────────────────────
    public function edit(int $id): void
    {
        $brochure = Brochure::findOrFail($id);

        $this->editingId = $brochure->id;
        $this->title     = $brochure->title;
        $this->is_active = $brochure->is_active;
        $this->file      = null;

        $this->resetErrorBag();
    }

    public function cancelEdit(): void
    {
        $this->resetForm();
    }

    protected function resetForm(): void
    {
        $this->reset(['editingId', 'title', 'file', 'is_active']);
        $this->is_active = true;
        $this->resetErrorBag();
    }

    public function save(): void
    {
        $validated = $this->validate();

        $brochure = $this->editingId
            ? Brochure::findOrFail($this->editingId)
            : new Brochure(['sort_order' => Brochure::max('sort_order') + 1]);

        if ($this->file) {
            // Replace the old PDF on disk so storage doesn't accumulate orphans.
            if ($brochure->file_path) {
                Storage::disk('public')->delete($brochure->file_path);
            }

            $brochure->file_path      = $this->file->store('brochures', 'public');
            $brochure->original_name  = $this->file->getClientOriginalName();
            $brochure->file_size      = $this->file->getSize();
        }

        $brochure->title     = $validated['title'];
        $brochure->is_active = $this->is_active;
        $brochure->save();

        $this->loadBrochures();
        $this->resetForm();

        $this->dispatch('brochure-saved', title: $brochure->title);
    }

    public function toggleActive(int $id): void
    {
        $brochure = Brochure::findOrFail($id);
        $brochure->update(['is_active' => ! $brochure->is_active]);

        $this->loadBrochures();
    }

    public function delete(int $id): void
    {
        $brochure = Brochure::findOrFail($id);

        if ($brochure->file_path) {
            Storage::disk('public')->delete($brochure->file_path);
        }

        $brochure->delete();

        if ($this->editingId === $id) {
            $this->resetForm();
        }

        $this->loadBrochures();

        $this->dispatch('brochure-deleted');
    }

    public function render()
    {
        return view('livewire.admin.brochure-admin');
    }
}