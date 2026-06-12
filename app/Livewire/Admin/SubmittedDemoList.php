<?php

namespace App\Livewire\Admin;

use App\Models\Course;
use App\Models\SubmittedDemos;
use Livewire\Component;
use Illuminate\Support\Facades\Mail;
use App\Mail\DemoApprovedMail;
use App\Mail\DemoRejectedMail;
class SubmittedDemoList extends Component
{
    public $search = '';
    public $courseFilter = '';
    public $statusFilter = '';
    public $perPage = 10;

    public $sortField = 'created_at';
    public $sortDirection = 'desc';
public function approve($id)
{
    $demo = SubmittedDemos::with('user')->findOrFail($id);

    $demo->update([
        'status' => 'approved'
    ]);

    // if ($demo->user?->email) {
    //     Mail::to($demo->user->email)
    //         ->send(new DemoApprovedMail($demo));
    // }

    $this->dispatch(
        'swal',
        icon: 'success',
        title: 'Demo Approved Successfully'
    );
}
   public function reject($id)
{
    $demo = SubmittedDemos::with('user')->findOrFail($id);

    $demo->update([
        'status' => 'rejected'
    ]);

    // if ($demo->user?->email) {
    //     Mail::to($demo->user->email)
    //         ->send(new DemoRejectedMail($demo));
    // }

    $this->dispatch(
        'swal',
        icon: 'success',
        title: 'Demo Rejected Successfully'
    );
}
    public function render()
    {
        $query = SubmittedDemos::with([
            'demoUser',
            'course',
            'user'
        ]);

        if ($this->search) {

            $query->where(function ($q) {

                $q->where('demo_topic', 'like', "%{$this->search}%")

                    ->orWhereHas('demoUser', function ($sub) {

                        $sub->where(
                            'full_name',
                            'like',
                            "%{$this->search}%"
                        );
                    });
            });
        }

        if ($this->courseFilter) {
            $query->where('course_id', $this->courseFilter);
        }

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        $query->orderBy(
            $this->sortField,
            $this->sortDirection
        );

        return view('livewire.admin.submitted-demo-list',
            ['demos' => $query->paginate($this->perPage),
                'courses' => Course::orderBy('title')->get(),
                'totalDemos' =>SubmittedDemos::count(),
                'approvedDemos' =>SubmittedDemos::where('status', 'approved')->count(),
                'pendingDemos' =>SubmittedDemos::where('status','pending')->count(),
                'totalCourses' =>SubmittedDemos::distinct('course_id')->count('course_id'),
            ]
        );
    }
}
