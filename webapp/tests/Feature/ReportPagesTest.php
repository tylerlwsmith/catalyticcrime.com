<?php

namespace Tests\Feature;

use App\Models\Report;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ReportPagesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function report_index_page_returns_200()
    {
        $response = $this->get(route('reports.index'));
        $response->assertStatus(200);
    }

    /** @test */
    public function report_index_page_only_shows_approved_posts()
    {
        Report::factory()->count(2)->create(['admin_approved' => true]);
        Report::factory()->count(5)->create(['admin_approved' => false]);

        $response = $this->get(route('reports.index'));
        $this->assertCount(2, $response['reports']);
    }

    /** @test */
    public function report_create_page_returns_200_if_logged_in()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('reports.create'));

        $response->assertStatus(200);
    }

    /** @test */
    public function report_create_page_returns_redirect_if_guest()
    {
        $response = $this->get('/reports/create');
        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function report_edit_page_returns_redirect_if_guest()
    {
        $response = $this->get('/reports/1/edit');
        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function report_edit_page_returns_200_the_report_belongs_to_the_user()
    {
        $user = User::factory()->create();
        $report = Report::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this
            ->actingAs($user)
            ->get(route('reports.edit', ['report' => $report]));

        $response->assertStatus(200);
    }

    /** @test */
    public function user_reports_page_returns_200_if_logged_in()
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get(route('user-reports.index'));

        $response->assertStatus(200);
    }

    /** @test */
    public function user_reports_page_returns_redirect_if_guest()
    {
        $response = $this->get(route('user-reports.index'));
        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function user_reports_page_only_shows_reports_from_that_user()
    {
        Report::factory()->count(5)->create();

        $user = User::factory()->create();
        Report::factory()->count(1)->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get(route('user-reports.index'));

        $this->assertCount(1, $response['reports']);
    }

    /** @test */
    public function an_unathenticated_user_can_view_admin_approved_reports()
    {
        $report = Report::factory(['admin_approved' => true])->create();

        $response = $this->get(route('reports.show', ['report' => $report]));

        $response->assertStatus(200);
    }
}
