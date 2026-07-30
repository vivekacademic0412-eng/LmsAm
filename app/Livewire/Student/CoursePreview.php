<?php

namespace App\Livewire\Student;

use App\Models\Course;
use Livewire\Component;

class CoursePreview extends Component
{
    public $id;
    public bool $isEnrolled = false;

    /** The student's own enrollment row for this course, if any (loaded via the same
     *  relation your catalog already uses, so it matches whichever enrollment model
     *  User::enrollmentsAsStudent() actually points to). */
    public $enrollment = null;
    public $course;
    /** Batch the visitor has picked on this page, before adding to cart. */
    public ?int $selectedBatchId = null;

    public function mount($id)
    {

        $this->course = Course::where('id', $id)->with([
            'category',
            'subcategory',
            'courseType',
            'weeks.sessions.items',
            'batches' => fn($b) => $b->orderBy('start_date'),
        ])->first();


        $this->enrollment = auth()->user()
            ->enrollmentsAsStudent()
            ->where('course_id', $id)
            ->with('batch')
            ->first();

        $this->isEnrolled = (bool) $this->enrollment;

        $cartBatches = session('course_cart_batches', []);
        $this->selectedBatchId = $cartBatches[$id] ?? null;
    }

    /** Seats remaining on a batch. Public so the Blade view can show live counts. */
    public function seatsLeftFor($batch): int
    {
        $taken = $batch->students()->where('status', '!=', 'cancelled')->count();

        return max(0, ($batch->max_seats ?? 0) - $taken);
    }

    public function pickBatch($batchId)
    {
        $batch = $this->course->batches->firstWhere('id', (int) $batchId);

        if (! $batch || $this->seatsLeftFor($batch) <= 0) {
            $this->addError('batch', 'That batch is full — pick another one.');
            return;
        }

        $this->selectedBatchId = (int) $batchId;
    }

    public function addToCart()
    {
        // If the course runs in batches, make sure something's actually selected
        // (falls back to the first batch with open seats so this still works from
        // a page where the visitor never touched the picker).
        if ($this->course->batches->isNotEmpty() && ! $this->selectedBatchId) {
            $firstOpen = $this->course->batches->first(fn($b) => $this->seatsLeftFor($b) > 0);
            if (! $firstOpen) {
                $this->addError('batch', 'No open batches right now for this course.');
                return;
            }
            $this->selectedBatchId = $firstOpen->id;
        }

        $cart = session('course_cart', []);
        if (! in_array($this->course->id, $cart)) {
            $cart[] = $this->course->id;
            session(['course_cart' => $cart]);
        }

        if ($this->selectedBatchId) {
            $cartBatches = session('course_cart_batches', []);
            $cartBatches[$this->course->id] = $this->selectedBatchId;
            session(['course_cart_batches' => $cartBatches]);
        }

        $this->dispatch('cart-updated');
        session()->flash('cart_message', 'Added to cart.');
    }

    public function getRelatedCoursesProperty()
    {
        return Course::query()
            ->where('id', '!=', $this->course->id)
            ->where(function ($q) {
                $q->where('category_id', $this->course->category_id)
                    ->orWhere('subcategory_id', $this->course->subcategory_id);
            })
            ->orderBy('title')
            ->limit(6)
            ->get();
    }

    public function render()
    {
        return view('livewire.student.course-preview', [
            'relatedCourses' => $this->relatedCourses,
        ]);
    }
}
