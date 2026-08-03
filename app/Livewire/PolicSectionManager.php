<?php

namespace App\Livewire;

use App\Models\Policy;
use App\Models\PolicySection;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class PolicSectionManager extends Component
{
    use WithPagination;

    /* ---------------- Filters / list state ---------------- */

    public string $search = '';
    public string $statusFilter = '';     // '', 'active', 'inactive'
    public string $publishedFilter = '';  // '', 'published', 'draft'

    // Quick "jump to policy" dropdown — populated live from the policies table.
    public ?int $jumpToPolicyId = null;

    /* ---------------- Form state (Policy) ---------------- */

    public bool $showForm = false;
    public ?int $editingId = null;

    public string $code = '';
    public string $version = '';
    public string $title = '';
    public bool $isActive = true;
    public ?string $publishedAt = null; // datetime-local string, e.g. 2026-08-03T14:30

    // Code auto-follows the title until the admin explicitly unlocks it.
    public bool $codeLocked = false;

    /* ---------------- Form state (nested PolicySection rows) ---------------- */

    /** @var array<int, array{id: ?int, section_key: string, title: string, body: string}> */
    public array $sections = [];

    public function mount(): void
    {
        abort_unless(
            in_array(Auth::user()?->role, [User::ROLE_SUPERADMIN, User::ROLE_ADMIN], true),
            403
        );
    }

    /* ---------------- Computed data ---------------- */

    #[Computed]
    public function policies()
    {
        return Policy::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('title', 'like', "%{$this->search}%")
                        ->orWhere('code', 'like', "%{$this->search}%");
                });
            })
            ->when($this->statusFilter !== '', function ($query) {
                $query->where('is_active', $this->statusFilter === 'active');
            })
            ->when($this->publishedFilter === 'published', fn ($query) => $query->whereNotNull('published_at'))
            ->when($this->publishedFilter === 'draft', fn ($query) => $query->whereNull('published_at'))
            ->withCount('sections')
            ->orderByDesc('id')
            ->paginate(10);
    }

    #[Computed]
    public function policyOptions()
    {
        return Policy::orderBy('title')->get(['id', 'title', 'is_active']);
    }

    /* ---------------- Reactivity ---------------- */

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatingPublishedFilter(): void
    {
        $this->resetPage();
    }

    public function updatedTitle(string $value): void
    {
        if (! $this->codeLocked) {
            $this->code = Str::of($value)->slug('_')->upper()->toString();
        }
    }

    public function unlockCode(): void
    {
        $this->codeLocked = true;
    }

    public function updatedJumpToPolicyId($value): void
    {
        if ($value) {
            $this->edit((int) $value);
        }
    }

    /* ---------------- Section repeater actions ---------------- */

    public function addSection(): void
    {
        $this->sections[] = [
            'id' => null,
            'section_key' => '',
            'title' => '',
            'body' => '',
        ];
    }

    public function removeSection(int $index): void
    {
        unset($this->sections[$index]);
        $this->sections = array_values($this->sections);
    }

    public function moveSectionUp(int $index): void
    {
        if ($index <= 0 || ! isset($this->sections[$index - 1])) {
            return;
        }

        [$this->sections[$index - 1], $this->sections[$index]] =
            [$this->sections[$index], $this->sections[$index - 1]];
    }

    public function moveSectionDown(int $index): void
    {
        if (! isset($this->sections[$index + 1])) {
            return;
        }

        [$this->sections[$index + 1], $this->sections[$index]] =
            [$this->sections[$index], $this->sections[$index + 1]];
    }

    /* ---------------- Validation ---------------- */

    protected function rules(): array
    {
        return [
            'code' => [
                'required', 'string', 'max:100',
                Rule::unique('policies', 'code')->ignore($this->editingId),
            ],
            'version' => ['nullable', 'string', 'max:20'],
            'title' => ['required', 'string', 'max:255'],
            'isActive' => ['boolean'],
            'publishedAt' => ['nullable', 'date'],
            'sections' => ['array'],
            'sections.*.section_key' => ['required', 'string', 'max:100'],
            'sections.*.title' => ['required', 'string', 'max:255'],
            'sections.*.body' => ['nullable', 'string'],
        ];
    }

    protected function messages(): array
    {
        return [
            'code.unique' => 'That code is already used by another policy.',
            'sections.*.section_key.required' => 'Every section needs a section key.',
            'sections.*.title.required' => 'Every section needs a title.',
        ];
    }

    /* ---------------- CRUD actions ---------------- */

    public function create(): void
    {
        $this->resetForm();
        $this->sections = [
            ['id' => null, 'section_key' => '', 'title' => '', 'body' => ''],
        ];
        $this->showForm = true;
    }

    public function edit(int $id): void
    {
        $policy = Policy::with('sections')->findOrFail($id);

        $this->editingId = $policy->id;
        $this->code = $policy->code;
        $this->version = (string) $policy->version;
        $this->title = $policy->title;
        $this->isActive = (bool) $policy->is_active;
        $this->publishedAt = $policy->published_at?->format('Y-m-d\TH:i');

        // Editing an existing record: don't let further title edits silently rewrite its code.
        $this->codeLocked = true;

        $this->sections = $policy->sections
            ->map(fn ($section) => [
                'id' => $section->id,
                'section_key' => $section->section_key,
                'title' => $section->title,
                'body' => $section->body,
            ])
            ->values()
            ->all();

        if (empty($this->sections)) {
            $this->sections = [['id' => null, 'section_key' => '', 'title' => '', 'body' => '']];
        }

        $this->showForm = true;
        $this->resetErrorBag();
    }

    public function save(): void
    {
        // Guard against duplicate section keys before hitting the DB.
        $keys = array_map(fn ($s) => $s['section_key'], $this->sections);
        if (count($keys) !== count(array_unique($keys))) {
            $this->addError('sections', 'Section keys must be unique within this policy.');

            return;
        }

        $this->validate();

        $message = '';

        DB::transaction(function () use (&$message) {
            $payload = [
                'code' => $this->code,
                'version' => $this->version ?: null,
                'title' => $this->title,
                'is_active' => (bool) $this->isActive,
                'published_at' => $this->publishedAt ?: null,
            ];

            if ($this->editingId) {
                $policy = Policy::findOrFail($this->editingId);
                $policy->update($payload);
                $message = "\"{$policy->title}\" was updated.";
            } else {
                $policy = Policy::create($payload);
                $message = "\"{$policy->title}\" was created.";
            }

            $keptIds = [];

            foreach ($this->sections as $index => $section) {
                $sectionPayload = [
                    'section_key' => $section['section_key'],
                    'title' => $section['title'],
                    'body' => $section['body'] ?: null,
                    'sort_order' => $index,
                ];

                $model = ! empty($section['id'])
                    ? PolicySection::where('policy_id', $policy->id)->find($section['id'])
                    : null;

                if ($model) {
                    $model->update($sectionPayload);
                } else {
                    $model = $policy->sections()->create($sectionPayload);
                }

                $keptIds[] = $model->id;
            }

            // Remove sections that were deleted client-side before saving.
            $policy->sections()->whereNotIn('id', $keptIds ?: [0])->delete();
        });

        $this->resetForm();
        $this->showForm = false;
        $this->jumpToPolicyId = null;

        unset($this->policies, $this->policyOptions);

        $this->dispatch('policy-saved', message: $message);
    }

    public function delete(int $id): void
    {
        $policy = Policy::find($id);

        if (! $policy) {
            return;
        }

        $title = $policy->title;
        $policy->sections()->delete();
        $policy->delete();

        if ($this->editingId === $id) {
            $this->cancel();
        }

        unset($this->policies, $this->policyOptions);

        $this->dispatch('policy-deleted', message: "\"{$title}\" was deleted.");
    }

    public function cancel(): void
    {
        $this->resetForm();
        $this->showForm = false;
    }

    private function resetForm(): void
    {
        $this->reset([
            'editingId', 'code', 'version', 'title', 'publishedAt', 'sections', 'codeLocked',
        ]);

        $this->isActive = true;
        $this->sections = [];
        $this->resetErrorBag();
    }

    public function render()
    {
        return view('livewire.polic-section-manager');
    }
}