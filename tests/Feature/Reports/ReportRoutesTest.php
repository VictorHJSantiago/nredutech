<?php

namespace Tests\Feature\Reports;

use Tests\TestCase;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ReportRoutesTest extends TestCase
{
    use RefreshDatabase;

    private Usuario $admin;
    private Usuario $diretor;
    private Usuario $professor;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = Usuario::factory()->administrador()->create();
        $this->diretor = Usuario::factory()->diretor()->create();
        $this->professor = Usuario::factory()->professor()->create();
    }

    public function test_guest_is_redirected_from_all_report_routes()
    {
        $this->get(route('reports.index'))->assertRedirect(route('login'));
        $this->post(route('reports.preview'))->assertRedirect(route('login'));
        $this->post(route('reports.export'))->assertRedirect(route('login'));
        $this->post(route('reports.exportAll'))->assertRedirect(route('login'));
    }

    public function test_professor_is_forbidden_from_all_report_routes()
    {
        $this->actingAs($this->professor);

        $this->get(route('reports.index'))->assertForbidden();
        $this->post(route('reports.preview'))->assertForbidden();
        $this->post(route('reports.export'))->assertForbidden();
        $this->post(route('reports.exportAll'))->assertForbidden();
    }

    public function test_diretor_can_access_all_report_routes()
    {
        $this->actingAs($this->diretor);

        $this->get(route('reports.index'))->assertOk();
        
        $postData = [
            'report_type' => 'usage_by_resource',
            'start_date' => now()->subMonth()->format('Y-m-d'),
            'end_date' => now()->format('Y-m-d'),
        ];
        
        $this->post(route('reports.preview'), $postData)->assertOk();
        $this->post(route('reports.export'), $postData)->assertOk();
        $this->post(route('reports.exportAll'), $postData)->assertOk();
    }

    public function test_admin_can_access_all_report_routes()
    {
        $this->actingAs($this->admin);

        $this->get(route('reports.index'))->assertOk();

        $postData = [
            'report_type' => 'usage_by_resource',
            'start_date' => now()->subMonth()->format('Y-m-d'),
            'end_date' => now()->format('Y-m-d'),
            'escola_id' => 'all',
        ];

        $this->post(route('reports.preview'), $postData)->assertOk();
        $this->post(route('reports.export'), $postData)->assertOk();
        $this->post(route('reports.exportAll'), $postData)->assertOk();
    }
}