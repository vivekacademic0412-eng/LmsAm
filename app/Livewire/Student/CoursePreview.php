<?php

namespace App\Livewire\Student;

use App\Models\Course;
use Livewire\Component;

class CoursePreview extends Component
{
    public Course $course;
    public bool $isEnrolled = false;

    public function mount(Course $course)
    {
        $this->course = $course->load([
            'category',
            'subcategory',
            'weeks.sessions.items',
            'demoFeatureVideos',
        ]);

        $this->isEnrolled = auth()->user()
            ->enrollmentsAsStudent()
            ->where('course_id', $course->id)
            ->exists();
    }

    public function addToCart()
    {
        $cart = session('course_cart', []);
        if (! in_array($this->course->id, $cart)) {
            $cart[] = $this->course->id;
            session(['course_cart' => $cart]);
        }
        $this->dispatch('cart-updated');
        session()->flash('cart_message', 'Added to cart.');
    }

    public function render()
    {
        return view('livewire.student.course-preview');
    }
}