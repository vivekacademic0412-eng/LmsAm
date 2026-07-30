<?php

namespace App\Livewire\Admin\Students;

use App\Mail\StudentPaymentLinkMail;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Livewire\Attributes\On;
use Livewire\Component;

class StudentCreateModal extends Component
{
    public bool $showModal = false;

    // form fields
    public $name;
    public $email;
    public $contact_no;
    public $enrollment_type = 'demo'; // demo | course
    public $category_id = null;
    public $course_id = null;
    public $price = null;
    public $original_price = null;

    const DEMO_PRICE_PAID = 999;

    // shown once after creation (also sent to the SweetAlert success popup)
    public $generated_username = null;
    public $generated_password = null;
    public $generated_link = null;

    public function getCategoriesProperty()
    {
        return CourseCategory::orderBy('name')->get();
    }

    public function getCoursesProperty()
    {
        if (!$this->category_id || $this->enrollment_type !== 'course') {
            return collect();
        }
        return Course::where('category_id', $this->category_id)->orderBy('title')->get();
    }

    public function open(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function close(): void
    {
        $this->showModal = false;
    }

    public function updatedEnrollmentType(): void
    {
        $this->category_id = null;
        $this->course_id = null;
        $this->price = null;
        $this->original_price = null;
    }

    public function updatedCategoryId(): void
    {
        $this->course_id = null;

        if ($this->enrollment_type === 'demo') {
            $this->price = self::DEMO_PRICE_PAID;
            $this->original_price = self::DEMO_PRICE_PAID;
        }
    }

    public function updatedCourseId($value): void
    {
        if (!$value) {
            $this->price = null;
            $this->original_price = null;
            return;
        }

        $course = Course::findOrFail($value);
        $this->price = $course->price;              // auto-fetched — admin can still edit below
        $this->original_price = $course->price;
    }

    protected function generateUniqueUsername(string $name): string
    {
        $base = Str::slug($name, '');
        $base = strtolower(substr($base, 0, 10)) ?: 'student';

        do {
            $candidate = $base . rand(1000, 9999);
        } while (User::where('username', $candidate)->exists());

        return $candidate;
    }

    public function save(): void
    {
        $this->validate([
            'name'            => 'required|string|max:255',
            'email'           => 'required|email|unique:users,email',
            'contact_no'      => 'required|string|max:20',
            'enrollment_type' => 'required|in:demo,course',
            'category_id'     => 'required|exists:course_categories,id',
            'course_id'       => 'required_if:enrollment_type,course|nullable|exists:courses,id',
            'price'           => 'required|numeric|min:0',
        ]);

        // FIX: this was undefined before ($username was never assigned because the
        // generator call was commented out) — restored below.
        $username = $this->generateUniqueUsername($this->name);
        $plainPassword = Str::password(10, symbols: false);

        $student = User::create([
            'name'       => $this->name,
            'email'      => $this->email,
            'contact'      => $this->contact_no,
            // 'username'   => $username,
            'password'   => Hash::make($plainPassword),
            'role'       => User::ROLE_STUDENT,
            'created_by' => Auth::id(),
        ]);

        // ── Create the pending payment record (your existing `payments` table) ──
        $payment = Payment::create([
            'user_id'     => $student->id,
            'name'        => $student->name,
            'email'       => $student->email,
            'phone'       => $student->contact,
            'amount'      => $this->price,
            'gateway'     => 'razorpay',
            'status'      => Payment::STATUS_PENDING,
            'source'      => 'admin_created',
            'type'        => $this->enrollment_type,
            'category_id' => $this->category_id,
            'course_id'   => $this->enrollment_type === 'course' ? $this->course_id : null,
            'created_by'  => Auth::id(),
        ]);

        $this->generated_username = $username;
        $this->generated_password = $plainPassword;
        $this->generated_link = $payment->publicUrl();


        Mail::to($student->email)->send(
            new StudentPaymentLinkMail(
                $student,
                $payment,
                $plainPassword
            )
        );
        $this->showModal = false;
        // Tell the browser to fire the SweetAlert success popup with these details.
        $this->dispatch('student-created', [
            'username' => $this->generated_username,
            'password' => $this->generated_password,
            'link'     => $this->generated_link,
            'name'     => $this->name,
        ]);
    }

    #[On('close-student-modal')]
    public function handleCloseAfterAlert(): void
    {
        $this->resetForm();
        $this->showModal = false; // synced back to Alpine's `open` via @entangle
    }

    public function resetForm(): void
    {
        $this->reset([
            'name',
            'email',
            'contact_no',
            'category_id',
            'course_id',
            'price',
            'original_price',
            'generated_username',
            'generated_password',
            'generated_link',
        ]);
        $this->enrollment_type = 'demo';
    }

    public function render()
    {
        return view('livewire.admin.students.student-create-modal');
    }
}
