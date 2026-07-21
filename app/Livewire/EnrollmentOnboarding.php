<?php

namespace App\Livewire;

use App\Models\AcademicBackground;
use App\Models\Batch;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\OnboardingDocument;
use App\Models\Policy;
use App\Models\PolicyAcceptance;
use App\Models\ProgramEnrollment;
use App\Models\StudentProfile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithFileUploads;

class EnrollmentOnboarding extends Component
{
    use WithFileUploads;

    public int $step = 1;
    public int $totalSteps = 4;

    // ── Step 1: Personal details ──
    public $first_name, $last_name, $dob, $gender, $category;
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
    public array $sectionsRead = [];

    public bool $hasScrolledPolicy = false;
    public bool $declaration_confirmed = false;
    public bool $terms_agreed = false;
    public bool $marketing_opt_in = false;

    public $policy;
   public $programCategories = [];
public $programs = [];
public $batches = [];
    public function mount()
    {
        $this->policy = Policy::with(['sections' => fn($q) => $q->orderBy('sort_order')])
            ->where('code', 'enrollment_terms')
            ->where('is_active', true)
            ->latest('published_at')
            ->first();

        $this->email = Auth::user()->email ?? '';
        $this->mobile_number = Auth::user()->contact ?? '';
        $this->step = Auth::user()->onboarding_step ?? 1;
         $this->programCategories = CourseCategory::orderBy('name')->get();

    // Initially empty
    $this->programs = collect();
    $this->batches = collect();
    }
public function updatedProgramCategoryId($value)
{
    $this->program_id = null;
    $this->batch_id = null;

    $this->programs = Course::where('category_id', $value)
        // ->where('status', 1)
        ->orderBy('title')
        ->get();

    $this->batches = collect();
}
public function updatedProgramId($value)
{
    $this->batch_id = null;

    $this->batches = Batch::where('course_id', $value)
        // ->where('status', 1)
        ->orderBy('start_date')
        ->get();
}
    protected function rulesForStep(int $step): array
    {
        return match ($step) {
            1 => [
                'first_name' => 'required|string|max:100',
                'last_name' => 'required|string|max:100',
                'dob' => 'required|date|before:-16 years',
                'gender' => 'required|in:male,female,other,prefer_not_to_say',
                'category' => 'nullable|string|max:50',
                'mobile_number' => 'required|digits:10',
                'whatsapp_number' => 'nullable|digits:10',
                'email' => 'required|email',
                'city_district' => 'required|string|max:120',
                'residential_address' => 'required|string|max:500',
                'id_proof_type' => 'required|in:aadhaar,passport,pan,voter_id,driving_licence',
                'id_number' => 'required|string|max:50',
            ],
            2 => [
                'highest_qualification' => 'required|string|max:120',
                'percentage_cgpa' => 'required|string|max:20',
                'institution_name' => 'required|string|max:150',
                'year_of_passing' => 'required|digits:4|integer|min:1980|max:' . (date('Y') + 1),
                'experience_level' => 'required|in:fresher,0-1,1-2,2+',
                'guardian_name' => 'required|string|max:120',
                'guardian_mobile' => 'required|digits:10',
            ],
            3 => [
                'program_category_id' => 'required',
                'program_id' => 'required',
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

    public function markPolicyScrolled()
    {
        $this->hasScrolledPolicy = true;
    }

    public function toggleSection(string $key)
    {
        $this->sectionsRead[$key] = true;
    }

    public function nextStep()
    {
        $this->validate($this->rulesForStep($this->step));
        $this->persistStep($this->step);

        if ($this->step < $this->totalSteps) {
            $this->step++;
            Auth::user()->update(['onboarding_step' => $this->step, 'onboarding_status' => 'in_progress']);
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
            StudentProfile::updateOrCreate(['user_id' => $userId], [
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'dob' => $this->dob,
                'gender' => $this->gender,
                'category' => $this->category,
                'mobile_number' => $this->mobile_number,
                'whatsapp_number' => $this->whatsapp_number,
                'email' => $this->email,
                'city_district' => $this->city_district,
                'residential_address' => $this->residential_address,
                'id_proof_type' => $this->id_proof_type,
                'id_number' => $this->id_number,
            ]);
        }

        if ($step === 2) {
            AcademicBackground::updateOrCreate(['user_id' => $userId], [
                'highest_qualification' => $this->highest_qualification,
                'percentage_cgpa' => $this->percentage_cgpa,
                'institution_name' => $this->institution_name,
                'year_of_passing' => $this->year_of_passing,
                'experience_level' => $this->experience_level,
                'guardian_name' => $this->guardian_name,
                'guardian_mobile' => $this->guardian_mobile,
            ]);
        }
    }

    public function submit()
    {
        $this->validate($this->rulesForStep(4));

        if (! $this->hasScrolledPolicy) {
            $this->dispatch('onboarding-error', message: 'Please read the full policy before agreeing.');
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
            $this->dispatch('onboarding-error', message: 'Something went wrong while submitting. Please try again.'.$e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.enrollment-onboarding');
    }
}
