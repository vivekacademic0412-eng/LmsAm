<?php

namespace App\Livewire;

use App\Models\AcademicBackground;
use App\Models\Batch;
use App\Models\City;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\OnboardingDocument;
use App\Models\Policy;
use App\Models\PolicyAcceptance;
use App\Models\ProgramEnrollment;
use App\Models\StudentProfile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Livewire\WithFileUploads;

class EnrollmentOnboarding extends Component
{
    use WithFileUploads;

    public int $step = 1;
    public int $totalSteps = 4;

    // ── Step 1: Personal details ──
    public $first_name, $last_name, $dob, $gender, $category;
    public $city_id;
    public $mobile_number, $whatsapp_number, $email;
    public $city_district, $residential_address;
    public $id_proof_type, $id_number;

    // ── Step 2: Academic background ──
    public $highest_qualification, $percentage_cgpa, $institution_name, $year_of_passing;
    public $experience_level, $guardian_name, $guardian_mobile;

    // ── Step 3: Program selection + documents ──
    public $program_category_id, $program_id, $batch_id;
    public $mode_of_learning, $preferred_start_date, $referral_source, $career_goal;
    public $photo, $id_proof_file, $marksheet_certificate, $experience_letter;

    // ── Step 4: Declaration ──
    // Which policy sections the user has actually expanded and read (key => true).
    public array $sectionsRead = [];
    // The single section currently expanded — accordion behaviour, only one open at a time.
    public ?string $openSection = null;

    public bool $declaration_confirmed = false;
    public bool $terms_agreed = false;
    public bool $marketing_opt_in = false;

    public $policy;
    public $cities = [];
    public $programCategories = [];
    public $programs = [];
    public $batches = [];

    public function mount()
    {
        $this->policy = Policy::with(['sections' => fn ($q) => $q->orderBy('sort_order')])
            ->where('code', 'enrollment_terms')
            ->where('is_active', true)
            ->latest('published_at')
            ->first();

        $this->email = Auth::user()->email ?? '';
        $this->mobile_number = Auth::user()->contact ?? '';
        $this->first_name = Auth::user()->name ?? '';
        $this->last_name = Auth::user()->last_name ?? '';
        $this->step = Auth::user()->onboarding_step ?? 1;

        $this->cities = City::orderBy('name')->get();
        $this->programCategories = CourseCategory::orderBy('name')->get();

        $this->programs = collect();
        $this->batches = collect();
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

    /**
     * Percentage / total of policy sections the user has actually opened & read.
     * Used to gate the declaration checkboxes — far more honest than a scroll listener,
     * which can be satisfied by dragging the scrollbar without reading anything.
     */
    public function getAllSectionsReadProperty(): bool
    {
        $total = $this->policy?->sections->count() ?? 0;
        if ($total === 0) {
            return true;
        }
        return collect($this->sectionsRead)->filter()->count() >= $total;
    }

    public function getReadCountProperty(): int
    {
        return collect($this->sectionsRead)->filter()->count();
    }

    protected function rulesForStep(int $step): array
    {
        $userId = Auth::id();

        // Rejects "aaaa", "asdfasdf"-style junk: 4+ identical or sequential chars in a row.
        $notFakePattern = 'regex:/^(?!.*(.)\1{3,}).+$/';

        return match ($step) {
            1 => [
                'first_name' => ['required', 'string', 'min:2', 'max:100', 'regex:/^[A-Za-z\s\.\']+$/', $notFakePattern],
                'last_name'  => ['required', 'string', 'min:1', 'max:100', 'regex:/^[A-Za-z\s\.\']+$/'],
                'dob' => 'required|date|before:-16 years|after:-100 years',
                'gender' => 'required|in:male,female,other,prefer_not_to_say',
                'category' => 'nullable|string|max:50',
                'city_id' => 'required|exists:cities,id',
                'mobile_number' => [
                    'required',
                    'regex:/^(?!(\d)\1{9})[6-9]\d{9}$/', // valid Indian mobile, not all-same-digit
                    Rule::unique('student_profiles', 'mobile_number')->ignore($userId, 'user_id'),
                ],
                'whatsapp_number' => ['nullable', 'regex:/^(?!(\d)\1{9})[6-9]\d{9}$/'],
                'email' => 'required|email',
                'residential_address' => ['required', 'string', 'min:15', 'max:500', $notFakePattern],
                'id_proof_type' => 'required|in:aadhaar,passport,pan,voter_id,driving_licence',
                'id_number' => ['required', 'string', 'max:50', function ($attr, $value, $fail) {
                    $type = $this->id_proof_type;
                    $ok = match ($type) {
                        'aadhaar' => (bool) preg_match('/^\d{12}$/', $value),
                        'pan'     => (bool) preg_match('/^[A-Z]{5}\d{4}[A-Z]$/', strtoupper($value)),
                        'voter_id' => (bool) preg_match('/^[A-Z]{3}\d{7}$/', strtoupper($value)),
                        default   => strlen($value) >= 5, // passport / driving licence — loose check
                    };
                    if (! $ok) {
                        $fail("Please enter a valid {$type} number.");
                    }
                }],
            ],
            2 => [
                'highest_qualification' => 'required|string|max:120',
                'percentage_cgpa' => 'required|string|max:20|regex:/^\d{1,3}(\.\d{1,2})?$/',
                'institution_name' => ['required', 'string', 'max:150', $notFakePattern],
                'year_of_passing' => 'required|digits:4|integer|min:1980|max:' . (date('Y') + 1),
                'experience_level' => 'required|in:fresher,0-1,1-2,2+',
                'guardian_name' => ['required', 'string', 'min:2', 'max:120', 'regex:/^[A-Za-z\s\.\']+$/'],
                'guardian_mobile' => ['required', 'regex:/^(?!(\d)\1{9})[6-9]\d{9}$/', 'different:mobile_number'],
            ],
            3 => [
                'program_category_id' => 'required|exists:course_categories,id',
                'program_id' => 'required|exists:courses,id',
                'mode_of_learning' => 'required|in:online,offline,hybrid',
                'preferred_start_date' => 'required|date|after_or_equal:today',
                'referral_source' => 'nullable|string|max:80',
                'career_goal' => 'nullable|string|max:1000',
                'photo' => 'required|image|max:2048',
                'id_proof_file' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
                'marksheet_certificate' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
                'experience_letter' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            ],
            4 => [
                'declaration_confirmed' => 'accepted',
                'terms_agreed' => 'accepted',
            ],
            default => [],
        };
    }

    protected function attributeNames(): array
    {
        return [
            'city_id' => 'city',
            'id_proof_file' => 'ID proof document',
        ];
    }

    /**
     * Accordion: opening a section closes whatever else was open, and
     * permanently marks that section as read (it stays "read" even if closed again).
     */
    public function toggleSection(string $key)
    {
        $this->openSection = $this->openSection === $key ? null : $key;
        $this->sectionsRead[$key] = true;
    }

    public function nextStep()
    {
        try {
            $this->validate($this->rulesForStep($this->step), [], $this->attributeNames());
        } catch (ValidationException $e) {
            $this->dispatch('swal', type: 'error', title: 'Please check the form', message: 'Some fields need your attention before you can continue.');
            throw $e;
        }

        $this->persistStep($this->step);

        if ($this->step < $this->totalSteps) {
            $this->step++;
            Auth::user()->update(['onboarding_step' => $this->step, 'onboarding_status' => 'in_progress']);
            $this->dispatch('swal', type: 'success', title: 'Saved', message: 'Your details have been saved. Continue to the next step.');
        }
    }

    public function prevStep()
    {
        if ($this->step > 1) {
            $this->step--;
        }
    }

    protected function persistStep(int $step): void
    {
        $userId = Auth::id();

        if ($step === 1) {
            $city = City::find($this->city_id);

            StudentProfile::updateOrCreate(['user_id' => $userId], [
                'first_name' => trim($this->first_name),
                'last_name' => trim($this->last_name),
                'dob' => $this->dob,
                'gender' => $this->gender,
                'category' => $this->category,
                'city_id' => $this->city_id,
                'city_district' => $city?->name,
                'mobile_number' => $this->mobile_number,
                'whatsapp_number' => $this->whatsapp_number,
                'email' => $this->email,
                'residential_address' => trim($this->residential_address),
                'id_proof_type' => $this->id_proof_type,
                'id_number' => strtoupper($this->id_number),
            ]);
        }

        if ($step === 2) {
            AcademicBackground::updateOrCreate(['user_id' => $userId], [
                'highest_qualification' => $this->highest_qualification,
                'percentage_cgpa' => $this->percentage_cgpa,
                'institution_name' => $this->institution_name,
                'year_of_passing' => $this->year_of_passing,
                'experience_level' => $this->experience_level,
                'guardian_name' => trim($this->guardian_name),
                'guardian_mobile' => $this->guardian_mobile,
            ]);
        }
    }

    public function submit()
    {
        try {
            $this->validate($this->rulesForStep(4));
        } catch (ValidationException $e) {
            $this->dispatch('swal', type: 'error', title: 'Please check the form', message: 'Please confirm both declarations before submitting.');
            throw $e;
        }

        if (! $this->allSectionsRead) {
            $this->dispatch('swal', type: 'error', title: 'Policy not fully read', message: 'Please open and read every policy section before agreeing — ' . $this->readCount . ' of ' . ($this->policy?->sections->count() ?? 0) . ' read so far.');
            return;
        }

        try {
            DB::transaction(function () {
                $userId = Auth::id();

                $enrollment = ProgramEnrollment::create([
                    'user_id' => $userId,
                    'program_category_id' => $this->program_category_id,
                    'program_id' => $this->program_id,
                    'batch_id' => $this->batch_id,
                    'mode_of_learning' => $this->mode_of_learning,
                    'preferred_start_date' => $this->preferred_start_date,
                    'referral_source' => $this->referral_source,
                    'career_goal' => $this->career_goal,
                    'status' => 'submitted',
                ]);

                foreach (
                    [
                        'photo' => $this->photo,
                        'id_proof' => $this->id_proof_file,
                        'marksheet_certificate' => $this->marksheet_certificate,
                        'experience_letter' => $this->experience_letter,
                    ] as $type => $file
                ) {
                    if (! $file) continue;
                    $path = $file->store('onboarding-documents/' . $userId, 'private');
                    OnboardingDocument::create([
                        'user_id' => $userId,
                        'doc_type' => $type,
                        'file_path' => $path,
                        'original_name' => $file->getClientOriginalName(),
                        'file_size' => $file->getSize(),
                        'uploaded_at' => now(),
                    ]);
                }

                if ($this->policy) {
                    PolicyAcceptance::create([
                        'user_id' => $userId,
                        'policy_id' => $this->policy->id,
                        'policy_version' => $this->policy->version,
                        'ip_address' => request()->ip(),
                        'user_agent' => request()->userAgent(),
                        'declaration_confirmed' => $this->declaration_confirmed,
                        'terms_agreed' => $this->terms_agreed,
                        'marketing_opt_in' => $this->marketing_opt_in,
                        'accepted_at' => now(),
                    ]);
                }

                Auth::user()->update([
                    'onboarding_status' => 'completed',
                    'onboarding_step' => $this->totalSteps,
                ]);
            });

            $this->dispatch('onboarding-submitted');
        } catch (\Throwable $e) {
            report($e);
            $this->dispatch('onboarding-error', message: 'Something went wrong while submitting. Please try again.');
        }
    }

    public function render()
    {
        return view('livewire.enrollment-onboarding');
    }
}