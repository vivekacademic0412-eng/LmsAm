<?php

namespace App\Services;

use App\Models\CourseEnrollment;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class StudentCertificateService
{
    private ?string $cachedBrandLogoDataUri = null;

    /**
     * @return Collection<int, array<string, mixed>>
     */
    public function completedCertificatesForStudent(User $student, ?int $limit = null): Collection
    {
        $enrollments = CourseEnrollment::query()
            ->where('student_id', $student->id)
            ->with([
                'course.category',
                'course.subcategory',
                'course.weeks.sessions.items',
                'student:id,name,email',
                'trainer:id,name',
                'progressItems',
            ])
            ->latest('id')
            ->get();

        $certificates = $enrollments
            ->map(fn (CourseEnrollment $enrollment) => $this->buildCompletedCertificate($enrollment))
            ->filter()
            ->sortByDesc('issued_at_timestamp')
            ->values();

        if ($limit !== null) {
            return $certificates->take($limit)->values();
        }

        return $certificates;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function buildCompletedCertificate(CourseEnrollment $enrollment): ?array
    {
        $enrollment->loadMissing([
            'course.category',
            'course.subcategory',
            'course.weeks.sessions.items',
            'student:id,name,email',
            'trainer:id,name',
            'progressItems',
        ]);

        $course = $enrollment->course;
        $student = $enrollment->student;

        if (! $course || ! $student) {
            return null;
        }

        $totalItems = $this->resolveTotalItems($enrollment);
        if ($totalItems < 1) {
            return null;
        }

        $completedItems = $this->resolveCompletedItems($enrollment);
        if ($completedItems < $totalItems) {
            return null;
        }

        $issuedAt = $this->resolveIssuedAt($enrollment)
            ?? $this->toCarbon($enrollment->updated_at)
            ?? $this->toCarbon($enrollment->created_at)
            ?? now();

        $certificateCode = $this->certificateCode($enrollment, $issuedAt);
        $courseTitle = trim((string) ($course->title ?: 'Untitled Course'));
        $categoryLabel = $course->subcategory?->name ?? $course->category?->name ?? 'General';
        $brandName = $this->brandName();

        return [
            'enrollment_id' => (int) $enrollment->id,
            'course_id' => (int) $course->id,
            'student_name' => trim((string) ($student->name ?: 'Student')),
            'course_title' => $courseTitle,
            'category' => $categoryLabel,
            'trainer_name' => trim((string) ($enrollment->trainer?->name ?? $brandName)),
            'hours_total' => max(1, (int) ($course->duration_hours ?? 1)),
            'issued_at' => $issuedAt,
            'issued_at_human' => $issuedAt->format('M d, Y'),
            'issued_at_full' => $issuedAt->format('F d, Y'),
            'issued_at_timestamp' => (int) $issuedAt->timestamp,
            'certificate_code' => $certificateCode,
            'download_route' => route('student.certificates.download', $enrollment),
            'download_svg_route' => route('student.certificates.download', $enrollment),
            'download_pdf_route' => route('student.certificates.download.pdf', $enrollment),
            'course_route' => route('student.courses.show', $course),
            'download_filename' => $this->downloadFilename($courseTitle),
            'download_svg_filename' => $this->downloadFilename($courseTitle),
            'download_pdf_filename' => $this->downloadPdfFilename($courseTitle),
            'brand_name' => $brandName,
            'brand_logo_data_uri' => $this->brandLogoDataUri(),
            'student_name_lines' => $this->wrapText($student->name ?: 'Student', 22, 2),
            'course_title_lines' => $this->wrapText($courseTitle, 32, 3),
        ];
    }

    public function downloadFilename(string $courseTitle): string
    {
        return $this->certificateFileBase($courseTitle).'-certificate.svg';
    }

    public function downloadPdfFilename(string $courseTitle): string
    {
        return $this->certificateFileBase($courseTitle).'-certificate.pdf';
    }

    private function certificateFileBase(string $courseTitle): string
    {
        $base = Str::slug($courseTitle);
        
        return $base !== '' ? $base : 'course';
    }

    public function brandName(): string
    {
        $brandName = trim((string) config('app.name', ''));

        if ($brandName === '' || $brandName === 'Laravel') {
            return 'Academic Mantra Services';
        }

        return $brandName;
    }

    public function brandLogoDataUri(): string
    {
        if ($this->cachedBrandLogoDataUri !== null) {
            return $this->cachedBrandLogoDataUri;
        }

        foreach ($this->brandLogoCandidates() as $candidate) {
            $logoPath = public_path($candidate['path']);

            if (! is_file($logoPath)) {
                continue;
            }

            $logoContents = file_get_contents($logoPath);

            if ($logoContents === false) {
                continue;
            }

            return $this->cachedBrandLogoDataUri = 'data:'.$candidate['mime'].';base64,'.base64_encode($logoContents);
        }

        return $this->cachedBrandLogoDataUri = '';
    }

    public function pdfBrandLogoDataUri(): string
    {
        foreach ($this->pdfBrandLogoCandidates() as $candidate) {
            $logoPath = public_path($candidate['path']);

            if (! is_file($logoPath)) {
                continue;
            }

            $logoContents = file_get_contents($logoPath);

            if ($logoContents === false) {
                continue;
            }

            return 'data:'.$candidate['mime'].';base64,'.base64_encode($logoContents);
        }

        return '';
    }

    /**
     * @return array<int, array{path: string, mime: string}>
     */
    private function brandLogoCandidates(): array
    {
        return [
            ['path' => 'images/logo.webp', 'mime' => 'image/webp'],
            ['path' => 'images/logo.png', 'mime' => 'image/png'],
            ['path' => 'images/logo.jpg', 'mime' => 'image/jpeg'],
            ['path' => 'images/logo.jpeg', 'mime' => 'image/jpeg'],
            ['path' => 'images/logo.svg', 'mime' => 'image/svg+xml'],
        ];
    }

    /**
     * @return array<int, array{path: string, mime: string}>
     */
    private function pdfBrandLogoCandidates(): array
    {
        return [
            ['path' => 'images/logo.png', 'mime' => 'image/png'],
            ['path' => 'images/logo.jpg', 'mime' => 'image/jpeg'],
            ['path' => 'images/logo.jpeg', 'mime' => 'image/jpeg'],
            ['path' => 'images/logo.svg', 'mime' => 'image/svg+xml'],
        ];
    }

    private function resolveTotalItems(CourseEnrollment $enrollment): int
    {
        $course = $enrollment->course;

        if ($course && $course->relationLoaded('weeks')) {
            return (int) $course->weeks
                ->flatMap->sessions
                ->flatMap->items
                ->count();
        }

        return (int) DB::table('course_session_items')
            ->join('course_sessions', 'course_sessions.id', '=', 'course_session_items.course_session_id')
            ->join('course_weeks', 'course_weeks.id', '=', 'course_sessions.course_week_id')
            ->where('course_weeks.course_id', $enrollment->course_id)
            ->count('course_session_items.id');
    }

    private function resolveCompletedItems(CourseEnrollment $enrollment): int
    {
        if ($enrollment->relationLoaded('progressItems')) {
            return (int) $enrollment->progressItems
                ->whereNotNull('completed_at')
                ->count();
        }

        return (int) $enrollment->progressItems()
            ->whereNotNull('completed_at')
            ->count();
    }

    private function resolveIssuedAt(CourseEnrollment $enrollment): ?Carbon
    {
        if ($enrollment->relationLoaded('progressItems')) {
            $latest = $enrollment->progressItems
                ->whereNotNull('completed_at')
                ->max('completed_at');

            return $this->toCarbon($latest);
        }

        $latest = $enrollment->progressItems()
            ->whereNotNull('completed_at')
            ->max('completed_at');

        return $this->toCarbon($latest);
    }

    private function toCarbon(mixed $value): ?Carbon
    {
        if ($value instanceof Carbon) {
            return $value->copy();
        }

        if ($value instanceof \DateTimeInterface) {
            return Carbon::instance($value);
        }

        if (is_string($value) && trim($value) !== '') {
            return Carbon::parse($value);
        }

        return null;
    }

    private function certificateCode(CourseEnrollment $enrollment, Carbon $issuedAt): string
    {
        return 'CERT-'
            .str_pad((string) $enrollment->id, 6, '0', STR_PAD_LEFT)
            .'-'
            .$issuedAt->format('Ymd');
    }

    /**
     * @return array<int, string>
     */
    private function wrapText(string $value, int $maxChars, int $maxLines): array
    {
        $text = trim(preg_replace('/\s+/', ' ', $value) ?? '');
        if ($text === '') {
            return ['-'];
        }

        $words = explode(' ', $text);
        $lines = [];
        $current = '';

        foreach ($words as $word) {
            $candidate = $current === '' ? $word : $current.' '.$word;

            if (Str::length($candidate) > $maxChars && $current !== '') {
                $lines[] = $current;
                $current = $word;

                if (count($lines) === $maxLines - 1) {
                    break;
                }

                continue;
            }

            $current = $candidate;
        }

        if ($current !== '') {
            $lines[] = $current;
        }

        $remainingWords = array_slice($words, array_sum(array_map(fn (string $line): int => count(explode(' ', $line)), $lines)));
        if (! empty($remainingWords) && ! empty($lines)) {
            $lastIndex = count($lines) - 1;
            $tail = trim($lines[$lastIndex].' '.implode(' ', $remainingWords));
            $lines[$lastIndex] = Str::limit($tail, $maxChars + 6, '...');
        }

        return array_slice($lines, 0, $maxLines);
    }
}
