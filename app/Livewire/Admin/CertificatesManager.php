<?php

namespace App\Livewire\Admin;

use App\Models\Certificate;
use App\Models\Course;
use App\Models\User;
use App\Services\CertificateEligibilityService;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class CertificatesManager extends Component
{
    use WithPagination;

    public string $search = '';
    public string $typeFilter = '';     // '', course, week, level, demo
    public string $statusFilter = '';   // '', locked, pending_admin_approval, unlocked

    // Manual-issue modal state
    public bool $showIssueModal = false;
    public ?int $issueUserId = null;
    public ?int $issueCourseId = null;
    public string $issueType = Certificate::TYPE_COURSE;

    protected $queryString = ['search', 'typeFilter', 'statusFilter'];

    public function updatingSearch()      { $this->resetPage(); }
    public function updatingTypeFilter()  { $this->resetPage(); }
    public function updatingStatusFilter(){ $this->resetPage(); }

   
    public function certificates()
    {
        return Certificate::query()
            ->with(['user', 'course', 'week', 'category'])
            ->when($this->search, fn ($q) => $q->whereHas(
                'user',
                fn ($u) => $u->where('name', 'like', "%{$this->search}%")
                              ->orWhere('email', 'like', "%{$this->search}%")
            ))
            ->when($this->typeFilter, fn ($q) => $q->where('type', $this->typeFilter))
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter))
            ->latest('updated_at')
            ->paginate(12);
    }

    public function stats(): array
    {
        return [
            'total'    => Certificate::count(),
            'pending'  => Certificate::where('status', Certificate::STATUS_PENDING)->count(),
            'unlocked' => Certificate::where('status', Certificate::STATUS_UNLOCKED)->count(),
            'locked'   => Certificate::where('status', Certificate::STATUS_LOCKED)->count(),
        ];
    }

   
    public function courses()
    {
        return Course::orderBy('title')->get(['id', 'title']);
    }

    public function approve(int $certificateId, CertificateEligibilityService $service): void
    {
        $certificate = Certificate::findOrFail($certificateId);
        $service->approveCertificate($certificate, auth()->user());
        $this->dispatch('certificate-approved');
    }

    public function reject(int $certificateId, CertificateEligibilityService $service): void
    {
        $certificate = Certificate::findOrFail($certificateId);
        $service->rejectCertificate($certificate);
        $this->dispatch('certificate-rejected');
    }

    public function revoke(int $certificateId): void
    {
        Certificate::findOrFail($certificateId)->update([
            'status'      => Certificate::STATUS_LOCKED,
            'approved_by' => null,
            'approved_at' => null,
            'issued_at'   => null,
        ]);
        $this->dispatch('certificate-revoked');
    }

    public function openIssueModal(): void
    {
        $this->reset(['issueUserId', 'issueCourseId']);
        $this->issueType = Certificate::TYPE_COURSE;
        $this->showIssueModal = true;
    }

    public function issueManually(CertificateEligibilityService $service): void
    {
        $this->validate([
            'issueUserId'   => 'required|exists:users,id',
            'issueCourseId' => 'required|exists:courses,id',
            'issueType'     => 'required|in:course,week,level,demo',
        ]);

        $user   = User::findOrFail($this->issueUserId);
        $course = Course::findOrFail($this->issueCourseId);

        $service->issueManualCertificate($user, $course, auth()->user(), $this->issueType);

        $this->showIssueModal = false;
        $this->dispatch('certificate-issued');
    }

    public function render()
    {
        return view('livewire.admin.certificates-manager');
    }
}