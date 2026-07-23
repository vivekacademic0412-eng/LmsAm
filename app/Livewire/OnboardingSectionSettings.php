<?php
// FILE: app/Livewire/OnboardingSectionSettings.php

namespace App\Livewire;

use App\Models\OnboardingSectionSetting;
use App\Models\User;
use Livewire\Component;

class OnboardingSectionSettings extends Component
{
    public array $roles = [];
    public array $sections = [
        'personal' => 'Personal Details',
        'academic' => 'Academic Background',
        'program'  => 'Program Selection',
    ];

    // matrix[role][section_key] => bool
    public array $matrix = [];

    public function mount(): void
    {
        abort_unless(
            in_array(auth()->user()?->role, [User::ROLE_SUPERADMIN, User::ROLE_ADMIN], true),
            403
        );

        $this->roles = collect(User::roleOptions())
            ->except([User::ROLE_SUPERADMIN, User::ROLE_ADMIN])
            ->all();

        $existing = OnboardingSectionSetting::all()->groupBy('role');

        foreach ($this->roles as $roleKey => $label) {
            foreach ($this->sections as $secKey => $secLabel) {
                $row = optional($existing->get($roleKey))->firstWhere('section_key', $secKey);
                $this->matrix[$roleKey][$secKey] = $row?->editable ?? true;
            }
        }
    }

    public function toggle(string $role, string $section): void
    {
        $this->matrix[$role][$section] = ! ($this->matrix[$role][$section] ?? true);
    }

    public function save(): void
    {
        foreach ($this->matrix as $role => $sections) {
            foreach ($sections as $section => $editable) {
                OnboardingSectionSetting::updateOrCreate(
                    ['role' => $role, 'section_key' => $section],
                    ['editable' => (bool) $editable]
                );
            }
        }

        $this->dispatch('swal', type: 'success', title: 'Saved', message: 'Onboarding edit permissions have been updated.');
    }

    public function render()
    {
        return view('livewire.onboarding-section-settings');
    }
}