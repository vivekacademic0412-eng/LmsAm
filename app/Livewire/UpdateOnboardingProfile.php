<?php
// FILE: app/Livewire/UpdateOnboardingProfile.php

namespace App\Livewire;

use App\Models\AcademicBackground;
use App\Models\Batch;
use App\Models\City;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\OnboardingSectionSetting;
use App\Models\ProgramEnrollment;
use App\Models\StudentProfile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Component;

class UpdateOnboardingProfile extends Component
{
    // ── Personal ──
    public $first_name, $last_name, $dob, $gender, $category, $city_id;
    public $mobile_number, $whatsapp_number, $residential_address, $id_proof_type, $id_number;

    // ── Academic ──
    public $highest_qualification, $percentage_cgpa, $institution_name, $year_of_passing;
    public $experience_level, $guardian_name, $guardian_mobile;

    // ── Program ──
    public $program_category_id, $program_id, $batch_id, $mode_of_learning, $preferred_start_date, $career_goal;
    public ?int $enrollmentId = null;

    public $cities = [];
    public $programCategories = [];
    public $programs = [];
    public $batches = [];

    // section_key => bool, whether the logged-in user's role may edit it
    public array $editable = [
        'personal' => true,
        'academic' => true,
        'program'  => true,
    ];

    public function mount()
    {
        $user = Auth::user();

        abort_unless(
            $user && $user->onboarding_status === 'completed',
            403,
            'Please complete onboarding before editing your details.'
        );

        $profile    = $user->studentProfile;
        $academic   = $user->academicBackground;
        $enrollment = $user->programEnrollments()->latest()->first();

        if ($profile) {
            $this->first_name = $profile->first_name;
            $this->last_name = $profile->last_name;
            $this->dob = optional($profile->dob)->format('Y-m-d');
            $this->gender = $profile->gender;
            $this->category = $profile->category;
            $this->city_id = $profile->city_id;
            $this->mobile_number = $profile->mobile_number;
            $this->whatsapp_number = $profile->whatsapp_number;
            $this->residential_address = $profile->residential_address;
            $this->id_proof_type = $profile->id_proof_type;
            $this->id_number = $profile->id_number;
        }

        if ($academic) {
            $this->highest_qualification = $academic->highest_qualification;
            $this->percentage_cgpa = $academic->percentage_cgpa;
            $this->institution_name = $academic->institution_name;
            $this->year_of_passing = $academic->year_of_passing;
            $this->experience_level = $academic->experience_level;
            $this->guardian_name = $academic->guardian_name;
            $this->guardian_mobile = $academic->guardian_mobile;
        }

        if ($enrollment) {
            $this->enrollmentId = $enrollment->id;
            $this->program_category_id = $enrollment->program_category_id;
            $this->program_id = $enrollment->program_id;
            $this->batch_id = $enrollment->batch_id;
            $this->mode_of_learning = $enrollment->mode_of_learning;
            $this->preferred_start_date = optional($enrollment->preferred_start_date)->format('Y-m-d');
            $this->career_goal = $enrollment->career_goal;
        }

        $this->cities = City::orderBy('name')->get();
        $this->programCategories = CourseCategory::orderBy('name')->get();
        $this->programs = $this->program_category_id
            ? Course::where('category_id', $this->program_category_id)->orderBy('title')->get()
            : collect();
        $this->batches = $this->program_id
            ? Batch::where('course_id', $this->program_id)->orderBy('start_date')->get()
            : collect();

        $this->editable = [
            'personal' => OnboardingSectionSetting::isEditable($user->role, 'personal'),
            'academic' => OnboardingSectionSetting::isEditable($user->role, 'academic'),
            'program'  => OnboardingSectionSetting::isEditable($user->role, 'program'),
        ];
    }

    public function updatedProgramCategoryId($value)
    {
        $this->program_id = null;
        $this->batch_id = null;
        $this->programs = Course::where('category_id', $value)->orderBy('title')->get();
        $this->batches = collect();
    }

    public function updatedProgramId($value)
    {
        $this->batch_id = null;
        $this->batches = Batch::where('course_id', $value)->orderBy('start_date')->get();
    }

    public function updatePersonal()
    {
        if (! $this->editable['personal']) {
            $this->dispatch('swal', type: 'error', title: 'Locked', message: 'Personal details cannot be edited for your role. Contact admin.');
            return;
        }

        $userId = Auth::id();

        $data = $this->validate([
            'first_name' => ['required', 'string', 'min:2', 'max:100', 'regex:/^[A-Za-z\s\.\']+$/'],
            'last_name' => ['required', 'string', 'max:100', 'regex:/^[A-Za-z\s\.\']+$/'],
            'dob' => 'required|date|before:-16 years|after:-100 years',
            'gender' => 'required|in:male,female,other,prefer_not_to_say',
            'category' => 'nullable|string|max:50',
            'city_id' => 'required|exists:cities,id',
            'mobile_number' => ['required', 'regex:/^(?!(\d)\1{9})[6-9]\d{9}$/', Rule::unique('student_profiles', 'mobile_number')->ignore($userId, 'user_id')],
            'whatsapp_number' => ['nullable', 'regex:/^(?!(\d)\1{9})[6-9]\d{9}$/'],
            'residential_address' => 'required|string|min:15|max:500',
            'id_proof_type' => 'required|in:aadhaar,passport,pan,voter_id,driving_licence',
            'id_number' => 'required|string|max:50',
        ], [], ['city_id' => 'city']);

        $city = City::find($data['city_id']);

        StudentProfile::where('user_id', $userId)->update(array_merge($data, [
            'city_district' => $city?->name,
            'id_number' => strtoupper($data['id_number']),
        ]));

        $this->dispatch('swal', type: 'success', title: 'Updated', message: 'Your personal details have been saved.');
    }

    public function updateAcademic()
    {
        if (! $this->editable['academic']) {
            $this->dispatch('swal', type: 'error', title: 'Locked', message: 'Academic details cannot be edited for your role. Contact admin.');
            return;
        }

        $userId = Auth::id();

        $data = $this->validate([
            'highest_qualification' => 'required|string|max:120',
            'percentage_cgpa' => 'required|string|max:20|regex:/^\d{1,3}(\.\d{1,2})?$/',
            'institution_name' => 'required|string|max:150',
            'year_of_passing' => 'required|digits:4|integer|min:1980|max:' . (date('Y') + 1),
            'experience_level' => 'required|in:fresher,0-1,1-2,2+',
            'guardian_name' => ['required', 'string', 'max:120', 'regex:/^[A-Za-z\s\.\']+$/'],
            'guardian_mobile' => ['required', 'regex:/^(?!(\d)\1{9})[6-9]\d{9}$/', 'different:mobile_number'],
        ]);

        AcademicBackground::where('user_id', $userId)->update($data);

        $this->dispatch('swal', type: 'success', title: 'Updated', message: 'Your academic background has been saved.');
    }

    public function updateProgram()
    {
        if (! $this->editable['program']) {
            $this->dispatch('swal', type: 'error', title: 'Locked', message: 'Program details cannot be edited for your role. Contact admin.');
            return;
        }

        if (! $this->enrollmentId) {
            $this->dispatch('swal', type: 'error', title: 'Not found', message: 'No enrolment record found to update.');
            return;
        }

        $data = $this->validate([
            'program_category_id' => 'required|exists:course_categories,id',
            'program_id' => 'required|exists:courses,id',
            'batch_id' => 'nullable|exists:batches,id',
            'mode_of_learning' => 'required|in:online,offline,hybrid',
            'preferred_start_date' => 'required|date',
            'career_goal' => 'nullable|string|max:1000',
        ]);

        ProgramEnrollment::where('id', $this->enrollmentId)->update($data);

        $this->dispatch('swal', type: 'success', title: 'Updated', message: 'Your program selection has been saved.');
    }

    public function render()
    {
        return view('livewire.update-onboarding-profile');
    }
}