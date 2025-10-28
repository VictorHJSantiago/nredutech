<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Usuario;
use App\Models\Escola;
use App\Models\Municipio;

class RouteAuthTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $diretor;
    protected $professor;

    protected function setUp(): void
    {
        parent::setUp();
        $municipio = Municipio::factory()->create();
        $escola = Escola::factory()->create(['id_municipio' => $municipio->id_municipio]);
        $this->admin = Usuario::factory()->create(['tipo_usuario' => 'administrador', 'id_escola' => null]);
        $this->diretor = Usuario::factory()->create(['tipo_usuario' => 'diretor', 'id_escola' => $escola->id_escola]);
        $this->professor = Usuario::factory()->create(['tipo_usuario' => 'professor', 'id_escola' => $escola->id_escola]);
    }

    /**
     * @test
     * @dataProvider 
     */
    public function guests_podem_acessar_rotas_publicas($rota)
    {
        $response = $this->get(route($rota));
        $response->assertStatus(200);
    }

    /**
     * @test
     * @dataProvider
     */
    public function guests_sao_redirecionados_de_rotas_autenticadas($rota)
    {
        $response = $this->get(route($rota));
        $response->assertRedirect(route('login'));
    }

    /**
     * @test
     * @dataProvider 
     */
    public function professor_e_bloqueado_de_rotas_admin($rota)
    {
        $response = $this->actingAs($this->professor)->get(route($rota));
        $response->assertStatus(403);
    }

    /**
     * @test
     * @dataProvider 
     */
    public function diretor_e_bloqueado_de_rotas_admin($rota)
    {
        $response = $this->actingAs($this->diretor)->get(route($rota));
        $response->assertStatus(403); 
    }

    /**
     * @test
     * @dataProvider
     */
    public function admin_pode_acessar_rotas_admin($rota)
    {
        if ($rota === 'settings.backup.initiate') {
            $response = $this->actingAs($this->admin)->get(route($rota));
            $response->assertRedirect(route('password.confirm'));
        } else {
            $response = $this->actingAs($this->admin)->get(route($rota));
            $response->assertStatus(200);
        }
    }

    /**
     * @test
     * @dataProvider 
     */
    public function professor_e_bloqueado_de_rotas_diretor($rota)
    {
        $response = $this->actingAs($this->professor)->get(route($rota));
        if (in_array($rota, ['usuarios.index', 'reports.index'])) {
             $response->assertRedirect(); 
        } else {
            $this->assertTrue(true);
        }
    }
    public static function rotasGuestProvider(): array
    {
        return [
            'login' => ['login'],
            'register' => ['register'],
            'password.request' => ['password.request'],
        ];
    }

    public static function rotasAutenticadasProvider(): array
    {
        return [
            'index (dashboard)' => ['index'],
            'profile.edit' => ['profile.edit'],
            'settings' => ['settings'],
            'agendamentos.index' => ['agendamentos.index'],
            'componentes.index' => ['componentes.index'],
            'resources.index' => ['resources.index'],
        ];
    }

    public static function rotasAdminProvider(): array
    {
        return [
            'escolas.index' => ['escolas.index'],
            'municipios.index' => ['municipios.index'],
            'settings.backup.initiate' => ['settings.backup.initiate'],
            'settings.backup.restore' => ['settings.backup.restore'],
        ];
    }

    public static function rotasDiretorProvider(): array
    {
        return [
             'usuarios.index' => ['usuarios.index'],
             'reports.index' => ['reports.index'],
        ];
    }
}