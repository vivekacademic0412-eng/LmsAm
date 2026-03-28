<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActivityLogPaginationTest extends TestCase
{
    use RefreshDatabase;

    public function test_activity_logs_default_to_eight_items_per_page(): void
    {
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
            'is_active' => true,
        ]);

        foreach (range(1, 9) as $index) {
            ActivityLog::create([
                'user_id' => $admin->id,
                'module' => 'Testing',
                'action' => 'update',
                'description' => 'Visible activity '.str_pad((string) $index, 2, '0', STR_PAD_LEFT),
                'subject_label' => 'Activity row '.str_pad((string) $index, 2, '0', STR_PAD_LEFT),
                'method' => 'PUT',
                'url' => 'https://example.test/activity/'.$index,
            ]);
        }

        $response = $this->actingAs($admin)->get(route('activity-logs.index'));

        $response->assertOk();
        $response->assertSee('Showing 1 to 8 of 9 activity logs.');
        $response->assertSee('Visible activity 09');
        $response->assertDontSee('Visible activity 01');
        $response->assertSee('value="8"', false);
    }
}
