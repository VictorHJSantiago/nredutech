<?php

namespace Tests\Feature\DidacticResource; 

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Usuario;
use App\Models\Municipio;
use App\Models\Escola;
use App\Models\RecursoDidatico;

class DidacticResourceRoutesTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $diretor;
    protected $professor;
    protected $recursoGlobal;
    protected $recursoEscolaDiretor; 
    protected $recursoEscolaProfessor; 
    protected $recursoOutraEscola;

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

        $this->recursoGlobal = RecursoDidatico::factory()->create(['id_escola' => null, 'id_usuario_criador' => $this->admin->id_usuario]);
        $this->recursoEscolaDiretor = RecursoDidatico::factory()->create(['id_escola' => $escolaDiretor->id_escola, 'id_usuario_criador' => $this->diretor->id_usuario]);
        $this->recursoEscolaProfessor = RecursoDidatico::factory()->create(['id_escola' => $escolaDiretor->id_escola, 'id_usuario_criador' => $this->professor->id_usuario]);
        $this->recursoOutraEscola = RecursoDidatico::factory()->create(['id_escola' => $outraEscola->id_escola, 'id_usuario_criador' => $outroProfessor->id_usuario]);
    }

    /**
     * @test
     * @dataProvider 
     */
    public function usuarios_autorizados_podem_acessar_index_recursos($tipoUsuario)
    {
        $user = $this->getUserByType($tipoUsuario);
        $response = $this->actingAs($user)->get(route('resources.index'));
        $response->assertStatus(200);
    }

    /** @test */
    public function guest_e_redirecionado_de_index_recursos()
    {
        $response = $this->get(route('resources.index'));
        $response->assertRedirect(route('login'));
    }

    /**
     * @test
     * @dataProvider 
     */
    public function usuarios_autorizados_podem_acessar_create_recursos($tipoUsuario)
    {
         $user = $this->getUserByType($tipoUsuario);
        $response = $this->actingAs($user)->get(route('resources.create'));
        $response->assertStatus(200);
    }

     /**
     * @test
     * @dataProvider 
     */
    public function usuarios_autorizados_podem_enviar_store_recursos($tipoUsuario)
    {
        $user = $this->getUserByType($tipoUsuario);
        $dados = RecursoDidatico::factory()->make()->toArray();
        if ($tipoUsuario !== 'administrador') {
            $dados['id_escola'] = $user->id_escola;
        } else {
             $dados['id_escola'] = null; 
        }
        $dados['split_quantity'] = 'false';

        $response = $this->actingAs($user)->post(route('resources.store'), $dados);
        $response->assertRedirect(route('resources.index'));
    }

    /** @test */
    public function admin_pode_acessar_edit_qualquer_recurso()
    {
        $this->actingAs($this->admin)->get(route('resources.edit', $this->recursoGlobal))->assertStatus(200);
        $this->actingAs($this->admin)->get(route('resources.edit', $this->recursoEscolaDiretor))->assertStatus(200);
        $this->actingAs($this->admin)->get(route('resources.edit', $this->recursoEscolaProfessor))->assertStatus(200);
        $this->actingAs($this->admin)->get(route('resources.edit', $this->recursoOutraEscola))->assertStatus(200);
    }

    /** @test */
    public function diretor_pode_acessar_edit_recurso_sua_escola_ou_global()
    {
        $this->actingAs($this->diretor)->get(route('resources.edit', $this->recursoGlobal))->assertStatus(200);
        $this->actingAs($this->diretor)->get(route('resources.edit', $this->recursoEscolaDiretor))->assertStatus(200); 
        $this->actingAs($this->diretor)->get(route('resources.edit', $this->recursoEscolaProfessor))->assertStatus(200); 
    }

    /** @test */
    public function diretor_nao_pode_acessar_edit_recurso_outra_escola()
    {
        $this->actingAs($this->diretor)->get(route('resources.edit', $this->recursoOutraEscola))->assertStatus(403);
    }

     /** @test */
    public function professor_pode_acessar_edit_recurso_que_criou()
    {
        $this->actingAs($this->professor)->get(route('resources.edit', $this->recursoEscolaProfessor))->assertStatus(200);
    }

     /** @test */
    public function professor_nao_pode_acessar_edit_recurso_de_outro()
    {
        $this->actingAs($this->professor)->get(route('resources.edit', $this->recursoGlobal))->assertStatus(403);
        $this->actingAs($this->professor)->get(route('resources.edit', $this->recursoEscolaDiretor))->assertStatus(403);
        $this->actingAs($this->professor)->get(route('resources.edit', $this->recursoOutraEscola))->assertStatus(403);
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