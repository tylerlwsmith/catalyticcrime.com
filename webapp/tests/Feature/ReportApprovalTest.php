<?php

namespace Tests\Feature;

use App\Models\Report;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ReportApprovalTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_view_their_own_unapproved_report()
    {
        $user = User::factory()->create();
        $report = Report::factory()->create([
            'user_id' => $user->id,
            'admin_approved' => false
        ]);

        $response = $this
            ->actingAs($user)
            ->get(route('reports.show', ['report' => $report]));

        $response->assertStatus(200);
    }

    /** @test */
    public function an_admin_can_view_a_users_unapproved_report()
    {
        $user = User::factory()->create();
        $report = Report::factory()->create([
            'user_id' => $user->id,
            'admin_approved' => false
        ]);

        $admin = User::factory(['is_admin' => true])->create();

        $response = $this
            ->actingAs($admin)
            ->get(route('reports.show', ['report' => $report]));

        $response->assertStatus(200);
    }

    /** @test */
    public function a_user_cant_view_another_users_own_unapproved_report()
    {
        $user_1 = User::factory()->create();
        $report = Report::factory()->create([
            'user_id' => $user_1->id,
            'admin_approved' => false
        ]);

        $user_2 = User::factory()->create();

        $response = $this
            ->actingAs($user_2)
            ->get(route('reports.show', ['report' => $report]));

        $response->assertStatus(403);
    }

    /** @test */
    public function an_unathenticated_user_cant_view_an_unapproved_report()
    {
        $user = User::factory()->create();
        $report = Report::factory()->create([
            'user_id' => $user->id,
            'admin_approved' => false
        ]);

        $response = $this->get(route('reports.show', ['report' => $report]));

        $response->assertStatus(403);
    }

    /** @test */
    public function an_admin_can_approve_a_report()
    {
        $user = User::factory()->create();
        $report = Report::factory()->create([
            'user_id' => $user->id,
            'admin_approved' => false
        ]);

        $admin = User::factory(['is_admin' => true])->create();

        $response = $this
            ->actingAs($admin)
            ->post(route('report-approvals.store', ['report' => $report]), ['admin_approved' => 1]);

        $response->assertStatus(302);
        $this->assertTrue($report->refresh()->admin_approved);
    }

    /** @test */
    public function an_authenticated_user_cant_approve_a_report()
    {
        $user = User::factory()->create();
        $report = Report::factory()->create([
            'user_id' => $user->id,
            'admin_approved' => false
        ]);

        $response = $this
            ->actingAs($user)
            ->post(route('report-approvals.store', ['report' => $report]), ['admin_approved' => 1]);

        $response->assertStatus(403);
        $this->assertFalse($report->refresh()->admin_approved);
    }

    /** @test */
    public function an_unauthenticated_user_cant_approve_a_report()
    {
        $user = User::factory()->create();
        $report = Report::factory()->create([
            'user_id' => $user->id,
            'admin_approved' => false
        ]);

        $response = $this
            ->post(route('report-approvals.store', ['report' => $report]), ['admin_approved' => 1]);

        $response->assertRedirect(route('login'));
        $this->assertFalse($report->refresh()->admin_approved);
    }
}
