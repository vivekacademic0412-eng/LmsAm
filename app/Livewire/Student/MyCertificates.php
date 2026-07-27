<?php

namespace App\Livewire\Student;

use App\Models\Certificate;
use App\Services\CertificateEligibilityService;
use Livewire\Attributes\Computed;
use Livewire\Component;

class MyCertificates extends Component
{
    public string $activeTab = 'all'; // all, course, week, level, demo

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
    }


    public function certificates()
    {
        $query = Certificate::query()
            ->where('user_id', auth()->id())
            ->with(['course', 'week', 'category']);

        if ($this->activeTab !== 'all') {
            $query->where('type', $this->activeTab);
        }

        return $query->latest('updated_at')->get();
    }


    public function stats(): array
    {
        $all = Certificate::where('user_id', auth()->id())->get();

        return [
            'unlocked'    => $all->where('status', Certificate::STATUS_UNLOCKED)->count(),
            'pending'     => $all->where('status', Certificate::STATUS_PENDING)->count(),
            'in_progress' => $all->where('status', Certificate::STATUS_LOCKED)->count(),
        ];
    }

    /**
     * Live completion % for locked course/week certs, so the bar reflects
     * progress even if the stored completion_percent hasn't been
     * recalculated since the student's last cached evaluation.
     */
    public function livePercent(Certificate $certificate, CertificateEligibilityService $service): float
    {
        if ($certificate->type === Certificate::TYPE_COURSE && $certificate->course) {
            return $service->courseCompletionPercent(auth()->user(), $certificate->course);
        }
        if ($certificate->type === Certificate::TYPE_WEEK && $certificate->week) {
            return $service->weekCompletionPercent(auth()->user(), $certificate->week);
        }
        return (float) ($certificate->completion_percent ?? 0);
    }

    public function download(int $certificateId)
    {
        $certificate = Certificate::where('user_id', auth()->id())->findOrFail($certificateId);

        abort_unless($certificate->isUnlocked(), 403, 'Certificate not yet unlocked.');
        abort_unless($certificate->file_path, 404, 'Certificate file not generated yet.');

        return response()->download(storage_path('app/' . $certificate->file_path));
    }

    public function render()
    {
        return view('livewire.student.my-certificates');
    }
}