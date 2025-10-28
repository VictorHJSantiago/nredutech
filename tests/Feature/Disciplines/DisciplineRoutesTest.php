<?php

namespace Tests\Feature\Disciplines; 

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Usuario;
use App\Models\Municipio;
use App\Models\Escola;
use App\Models\ComponenteCurricular;

class DisciplineRoutesTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $diretor;
    protected $professor;
    protected $componenteGlobal;
    protected $componenteEscolaDiretor; 
    protected $componenteEscolaProfessor; 
    protected $componenteOutraEscola;

    protected function setUp(): void
    {
        parent::setUp();
        $municipio = Municipio::factory()->create();
        $escolaDiretor = Escola::factory()->create(['id_municipio' => $municipio->id_municipio]);
        $outraEscola = Escola::factory()->create(['id_municipio' => $municipio->id_municipio]);

        $this->admin = Usuario::factory()->create(['tipo_usuario' => 'administrador']);
        $this->diretor = Usuario::factory()->create(['tipo_usuario' => 'diretor', 'id_escola' => $escolaDiretor->id_escola]);
        $this->professor = Usuario::factory()->create(['tipo_usuario' => 'professor', 'id_escola' => $escolaDiretor->id_escola]);
        $outroProfessor = Usuario::factory()->create(['tipo_usuario' => 'professor', 'id_escola' => $outraEscola->id_escola]);


        $this->componenteGlobal = ComponenteCurricular::factory()->create(['id_escola' => null, 'id_usuario_criador' => $this->admin->id_usuario]);
        $this->componenteEscolaDiretor = ComponenteCurricular::factory()->create(['id_escola' => $escolaDiretor->id_escola, 'id_usuario_criador' => $this->diretor->id_usuario]);
        $this->componenteEscolaProfessor = ComponenteCurricular::factory()->create(['id_escola' => $escolaDiretor->id_escola, 'id_usuario_criador' => $this->professor->id_usuario]);
        $this->componenteOutraEscola = ComponenteCurricular::factory()->create(['id_escola' => $outraEscola->id_escola, 'id_usuario_criador' => $outroProfessor->id_usuario]);
    }

    /**
     * @test
     * @dataProvider 
     */
    public function usuarios_autorizados_podem_acessar_index_componentes($tipoUsuario)
    {
        $user = $this->getUserByType($tipoUsuario);
        $response = $this->actingAs($user)->get(route('componentes.index'));
        $response->assertStatus(200);
    }

    /** @test */
    public function guest_e_redirecionado_de_index_componentes()
    {
        $response = $this->get(route('componentes.index'));
        $response->assertRedirect(route('login'));
    }

    /**
     * @test
     * @dataProvider 
     */
    public function usuarios_autorizados_podem_acessar_create_componentes($tipoUsuario)
    {
         $user = $this->getUserByType($tipoUsuario);
        $response = $this->actingAs($user)->get(route('componentes.create'));
        $response->assertStatus(200);
    }

     /**
     * @test
     * @dataProvider 
     */
    public function usuarios_autorizados_podem_enviar_store_componentes($tipoUsuario)
    {
        $user = $this->getUserByType($tipoUsuario);
        $dados = ComponenteCurricular::factory()->make()->toArray();
        if ($tipoUsuario !== 'administrador') {
            $dados['id_escola'] = $user->id_escola;
            unset($dados['status']); 
        } else {
             $dados['id_escola'] = null; 
             $dados['status'] = 'aprovado';
        }

        $response = $this->actingAs($user)->post(route('componentes.store'), $dados);
        $response->assertRedirect(route('componentes.index'));
    }

    /** @test */
    public function admin_pode_acessar_edit_qualquer_componente()
    {
        $this->actingAs($this->admin)->get(route('componentes.edit', $this->componenteGlobal))->assertStatus(200);
        $this->actingAs($this->admin)->get(route('componentes.edit', $this->componenteEscolaDiretor))->assertStatus(200);
        $this->actingAs($this->admin)->get(route('componentes.edit', $this->componenteEscolaProfessor))->assertStatus(200);
        $this->actingAs($this->admin)->get(route('componentes.edit', $this->componenteOutraEscola))->assertStatus(200);
    }

    /** @test */
    public function diretor_pode_acessar_edit_componente_sua_escola_ou_global()
    {
        $this->actingAs($this->diretor)->get(route('componentes.edit', $this->componenteGlobal))->assertStatus(200);
        $this->actingAs($this->diretor)->get(route('componentes.edit', $this->componenteEscolaDiretor))->assertStatus(200); 
        $this->actingAs($this->diretor)->get(route('componentes.edit', $this->componenteEscolaProfessor))->assertStatus(200); 
    }

    /** @test */
    public function diretor_nao_pode_acessar_edit_componente_outra_escola()
    {
        $this->actingAs($this->diretor)->get(route('componentes.edit', $this->componenteOutraEscola))->assertStatus(403);
    }

     /** @test */
    public function professor_pode_acessar_edit_componente_que_criou()
    {
        $this->actingAs($this->professor)->get(route('componentes.edit', $this->componenteEscolaProfessor))->assertStatus(200);
    }

     /** @test */
    public function professor_nao_pode_acessar_edit_componente_de_outro()
    {
        $this->actingAs($this->professor)->get(route('componentes.edit', $this->componenteGlobal))->assertStatus(403);
        $this->actingAs($this->professor)->get(route('componentes.edit', $this->componenteEscolaDiretor))->assertStatus(403);
        $this->actingAs($this->professor)->get(route('componentes.edit', $this->componenteOutraEscola))->assertStatus(403);
    }
    private function getUserByType(string $type): Usuario
    {
        return match ($type) {
            'administrador' => $this->admin,
            'diretor' => $this->diretor,
            'professor' => $this->professor,
        };
    }
    public static function usuariosAutorizadosProvider(): array
    {
        return [['administrador'], ['diretor'], ['professor']];
    }
}