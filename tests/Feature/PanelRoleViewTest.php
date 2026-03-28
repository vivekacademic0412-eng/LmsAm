<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\CourseEnrollment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class PanelRoleViewTest extends TestCase
{
    use RefreshDatabase;

    public function test_manager_hr_panel_renders_for_manager_hr_user(): void
    {
        $user = User::factory()->create([
            'role' => User::ROLE_MANAGER_HR,
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->get(route('panel.manager_hr'));

        $response->assertOk();
        $response->assertSee('Training Pipeline');
        $response->assertSee('Learners Needing Follow-up');
    }

    public function test_it_panel_renders_for_it_user(): void
    {
        $user = User::factory()->create([
            'role' => User::ROLE_IT,
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->get(route('panel.it'));

        $response->assertOk();
        $response->assertSee('Service &amp; Integration Status', false);
        $response->assertSee('Recent Security Events');
    }

    public function test_manager_hr_excel_report_downloads_for_manager_hr_user(): void
    {
        $user = User::factory()->create([
            'role' => User::ROLE_MANAGER_HR,
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->get(route('panel.manager_hr.export', [
            'report' => 'learner-progress',
            'format' => 'xls',
        ]));

        $response->assertOk();
        $this->assertStringContainsString(
            'application/vnd.ms-excel',
            (string) $response->headers->get('content-type')
        );
        $this->assertStringContainsString(
            '.xls',
            (string) $response->headers->get('content-disposition')
        );
    }

    public function test_manager_hr_pdf_report_downloads_for_manager_hr_user(): void
    {
        $user = User::factory()->create([
            'role' => User::ROLE_MANAGER_HR,
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->get(route('panel.manager_hr.export', [
            'report' => 'certificate-ready',
            'format' => 'pdf',
        ]));

        $response->assertOk();
        $this->assertStringContainsString(
            'application/pdf',
            (string) $response->headers->get('content-type')
        );
        $this->assertStringContainsString(
            '.pdf',
            (string) $response->headers->get('content-disposition')
        );
    }

    public function test_manager_hr_panel_renders_follow_up_rows_without_error(): void
    {
        $managerHr = User::factory()->create([
            'role' => User::ROLE_MANAGER_HR,
            'is_active' => true,
        ]);
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
            'is_active' => true,
        ]);
        $student = User::factory()->create([
            'role' => User::ROLE_STUDENT,
            'is_active' => false,
            'email' => 'inactive-student@example.com',
        ]);

        $category = CourseCategory::create([
            'name' => 'Compliance',
            'slug' => 'compliance',
        ]);

        $course = Course::create([
            'category_id' => $category->id,
            'title' => 'Workplace Safety',
            'slug' => Str::slug('Workplace Safety'),
            'description' => 'Safety course',
            'duration_hours' => 4,
            'created_by' => $admin->id,
        ]);

        CourseEnrollment::create([
            'course_id' => $course->id,
            'student_id' => $student->id,
            'trainer_id' => null,
            'assigned_by' => $admin->id,
        ]);

        $response = $this->actingAs($managerHr)->get(route('panel.manager_hr'));

        $response->assertOk();
        $response->assertSee('Learners Needing Follow-up');
        $response->assertSee('inactive-student@example.com');
        $response->assertSee('Learner account is inactive and may be blocked from training access.');
    }
}
