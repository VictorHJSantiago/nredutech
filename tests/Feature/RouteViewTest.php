<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Usuario;

class RouteViewTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = Usuario::factory()->create(['tipo_usuario' => 'administrador']);
    }

    /**
     * @test
     * @dataProvider 
     */
    public function rotas_retornam_a_view_correta($rota, $view)
    {
        $response = $this->actingAs($this->admin)->get(route($rota));
        $response->assertStatus(200);
        $response->assertViewIs($view);
    }

    public static function rotaViewProvider(): array
    {
        return [
            'dashboard' => ['index', 'index'], 
            'configurações' => ['settings', 'settings'],
            'perfil' => ['profile.edit', 'profile.edit'],
            'escolas' => ['escolas.index', 'schools.index'],
            'turmas' => ['turmas.index', 'classes.index'],
            'disciplinas' => ['componentes.index', 'disciplines.index'],
            'recursos' => ['resources.index', 'resources.index'],
            'agendamentos' => ['agendamentos.index', 'appointments.index'],
            'notificacoes' => ['notifications.index', 'notifications.index'],
            'relatorios' => ['reports.index', 'reports.index'],
            'usuarios' => ['usuarios.index', 'users.index'],
        ];
    }
}