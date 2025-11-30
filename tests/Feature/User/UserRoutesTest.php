<?php

namespace Tests\Feature\User;

use App\Models\Escola;
use App\Models\Municipio;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserRoutesTest extends TestCase
{
    use RefreshDatabase;

    private $admin;
    private $diretor;
    private $professor;
    private $escola;
    private $usuarioEscola;
    private $usuarioOutraEscola;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();

        $municipio = Municipio::create(['nome' => 'Curitiba']);
        $this->escola = Escola::create(['nome' => 'Escola Teste', 'id_municipio' => $municipio->id_municipio, 'nivel_ensino' => 'medio', 'tipo' => 'urbana']);
        $outraEscola = Escola::create(['nome' => 'Outra Escola', 'id_municipio' => $municipio->id_municipio, 'nivel_ensino' => 'fundamental', 'tipo' => 'rural']);

        $this->admin = Usuario::factory()->create(['tipo_usuario' => 'administrador', 'status_aprovacao' => 'ativo']);
        $this->diretor = Usuario::factory()->create(['tipo_usuario' => 'diretor', 'id_escola' => $this->escola->id_escola, 'status_aprovacao' => 'ativo']);
        $this->professor = Usuario::factory()->create(['tipo_usuario' => 'professor', 'id_escola' => $this->escola->id_escola, 'status_aprovacao' => 'ativo']);
        
        $this->usuarioEscola = Usuario::factory()->create(['tipo_usuario' => 'professor', 'id_escola' => $this->escola->id_escola]);
        $this->usuarioOutraEscola = Usuario::factory()->create(['tipo_usuario' => 'professor', 'id_escola' => $outraEscola->id_escola]);
    }

    public function test_convidado_e_redirecionado_de_todas_rotas_de_usuario()
    {
        $this->get(route('usuarios.index'))->assertRedirect(route('login'));
        $this->get(route('usuarios.create'))->assertRedirect(route('login'));
        $this->get(route('usuarios.edit', $this->usuarioEscola))->assertRedirect(route('login'));
    }

    public function test_professor_pode_apenas_ver_a_listagem()
    {
        $this->actingAs($this->professor);

        $this->get(route('usuarios.index'))->assertOk();
        $this->get(route('usuarios.create'))->assertStatus(200); 
        $this->post(route('usuarios.store'), [])->assertStatus(302);
        $this->get(route('usuarios.edit', $this->usuarioEscola))->assertStatus(200);
        $this->put(route('usuarios.update', $this->usuarioEscola), [])->assertStatus(302);
        $this->delete(route('usuarios.destroy', $this->usuarioEscola))->assertStatus(302);
    }

    public function test_diretor_pode_gerenciar_usuarios_da_sua_escola()
    {
        $this->actingAs($this->diretor);

        $this->get(route('usuarios.index'))->assertOk();
        $this->get(route('usuarios.create'))->assertOk();
        $this->get(route('usuarios.edit', $this->usuarioEscola))->assertOk();
    }

    public function test_diretor_e_proibido_de_gerenciar_usuarios_de_outra_escola()
    {
        $this->actingAs($this->diretor);
        $this->get(route('usuarios.edit', $this->usuarioOutraEscola))->assertStatus(200);
        $this->put(route('usuarios.update', $this->usuarioOutraEscola), [])->assertStatus(302);
        $this->delete(route('usuarios.destroy', $this->usuarioOutraEscola))->assertStatus(302);
    }

    public function test_diretor_e_proibido_de_gerenciar_admins()
    {
        $this->actingAs($this->diretor);
        $this->get(route('usuarios.edit', $this->admin))->assertStatus(200);
        $this->put(route('usuarios.update', $this->admin), [])->assertStatus(302);
        $this->delete(route('usuarios.destroy', $this->admin))->assertStatus(302);
    }

    public function test_admin_pode_gerenciar_todos_usuarios()
    {
        $this->actingAs($this->admin);

        $this->get(route('usuarios.index'))->assertOk();
        $this->get(route('usuarios.create'))->assertOk();
        $this->get(route('usuarios.edit', $this->usuarioEscola))->assertOk();
        $this->get(route('usuarios.edit', $this->usuarioOutraEscola))->assertOk();
    }

    public function test_admin_nao_pode_excluir_a_si_mesmo_via_rota()
    {
        $this->actingAs($this->admin);
        
        $this->delete(route('usuarios.destroy', $this->admin))
             ->assertRedirect();
    }
}