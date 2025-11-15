<?php

namespace Tests\Feature\User;

use Tests\TestCase;
use App\Models\Usuario;
use App\Models\Escola;
use App\Models\Municipio;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Foundation\Testing\WithFaker;

class UserRoutesTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private Usuario $admin;
    private Usuario $diretor;
    private Usuario $professor;
    private Escola $escola;
    private Escola $outraEscola;
    private Usuario $usuarioEscola;
    private Usuario $usuarioOutraEscola;

    protected function setUp(): void
    {
        parent::setUp();

        $municipio = Municipio::create(['nome' => 'Municipio Teste', 'estado' => 'PR']);
        $this->escola = Escola::create(['nome' => 'Escola Teste', 'id_municipio' => $municipio->id_municipio, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana']);
        $this->outraEscola = Escola::create(['nome' => 'Outra Escola', 'id_municipio' => $municipio->id_municipio, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana']);
        
        $this->admin = Usuario::factory()->create($this->getValidUserData(['tipo_usuario' => 'administrador', 'id_escola' => null]));
        $this->diretor = Usuario::factory()->create($this->getValidUserData(['tipo_usuario' => 'diretor', 'id_escola' => $this->escola->id_escola]));
        $this->professor = Usuario::factory()->create($this->getValidUserData(['tipo_usuario' => 'professor', 'id_escola' => $this->escola->id_escola]));

        $this->usuarioEscola = Usuario::factory()->create($this->getValidUserData(['tipo_usuario' => 'professor', 'id_escola' => $this->escola->id_escola]));
        $this->usuarioOutraEscola = Usuario::factory()->create($this->getValidUserData(['tipo_usuario' => 'professor', 'id_escola' => $this->outraEscola->id_escola]));
    }

    private function getValidUserData(array $overrides = []): array
    {
        return array_merge([
            'cpf' => $this->faker->unique()->cpf(false),
            'password' => 'ValidPassword@123456',
            'data_nascimento' => now()->subYears(20)->format('Y-m-d'),
        ], $overrides);
    }

    #[Test]
    public function convidado_e_redirecionado_de_todas_rotas_de_usuario()
    {
        $this->get(route('usuarios.index'))->assertRedirect(route('login'));
        $this->get(route('usuarios.create'))->assertRedirect(route('login'));
        $this->post(route('usuarios.store'), [])->assertRedirect(route('login'));
        $this->get(route('usuarios.edit', $this->usuarioEscola))->assertRedirect(route('login'));
        $this->put(route('usuarios.update', $this->usuarioEscola), [])->assertRedirect(route('login'));
        $this->delete(route('usuarios.destroy', $this->usuarioEscola))->assertRedirect(route('login'));
    }

    #[Test]
    public function professor_pode_apenas_ver_a_listagem()
    {
        $this->actingAs($this->professor);

        $this->get(route('usuarios.index'))->assertOk();
        $this->get(route('usuarios.create'))->assertOk();
        $this->post(route('usuarios.store'), [])->assertForbidden();
        $this->get(route('usuarios.edit', $this->usuarioEscola))->assertOk();
        $this->put(route('usuarios.update', $this->usuarioEscola), [])->assertForbidden();
        $this->delete(route('usuarios.destroy', $this->usuarioEscola))->assertForbidden();
    }

    #[Test]
    public function diretor_pode_gerenciar_usuarios_da_propria_escola()
    {
        $this->actingAs($this->diretor);

        $this->get(route('usuarios.index'))->assertOk();
        $this->get(route('usuarios.create'))->assertOk();
        
        $storeData = Usuario::factory()->make($this->getValidUserData(['id_escola' => $this->escola->id_escola, 'tipo_usuario' => 'professor']))->toArray();
        $storeData['password'] = 'ValidPassword@123456';
        $storeData['password_confirmation'] = 'ValidPassword@123456';
        $this->post(route('usuarios.store'), $storeData)->assertRedirect(route('usuarios.index'));

        $this->get(route('usuarios.edit', $this->usuarioEscola))->assertOk();
        
        $updateData = $this->usuarioEscola->toArray();
        $updateData['nome_completo'] = 'Nome Atualizado';
        $this->put(route('usuarios.update', $this->usuarioEscola), $updateData)->assertRedirect(route('usuarios.index'));
        
        $this->delete(route('usuarios.destroy', $this->usuarioEscola))->assertRedirect(route('usuarios.index'));
    }

    #[Test]
    public function diretor_e_proibido_de_gerenciar_usuarios_de_outra_escola()
    {
        $this->actingAs($this->diretor);

        $this->get(route('usuarios.edit', $this->usuarioOutraEscola))->assertForbidden();
        $this->put(route('usuarios.update', $this->usuarioOutraEscola), [])->assertForbidden();
        $this->delete(route('usuarios.destroy', $this->usuarioOutraEscola))->assertForbidden();
    }

    #[Test]
    public function diretor_e_proibido_de_gerenciar_admins()
    {
        $this->actingAs($this->diretor);

        $this->get(route('usuarios.edit', $this->admin))->assertForbidden();
        $this->put(route('usuarios.update', $this->admin), [])->assertForbidden();
        $this->delete(route('usuarios.destroy', $this->admin))->assertForbidden();
    }

    #[Test]
    public function admin_pode_gerenciar_todos_usuarios()
    {
        $this->actingAs($this->admin);

        $this->get(route('usuarios.index'))->assertOk();
        $this->get(route('usuarios.create'))->assertOk();
        
        $storeData = Usuario::factory()->make($this->getValidUserData(['id_escola' => $this->outraEscola->id_escola, 'tipo_usuario' => 'diretor']))->toArray();
        $storeData['password'] = 'ValidPassword@123456';
        $storeData['password_confirmation'] = 'ValidPassword@123456';
        $this->post(route('usuarios.store'), $storeData)->assertRedirect(route('usuarios.index'));

        $this->get(route('usuarios.edit', $this->usuarioOutraEscola))->assertOk();
        
        $updateData = $this->usuarioOutraEscola->toArray();
        $updateData['nome_completo'] = 'Nome Atualizado Pelo Admin';
        $this->put(route('usuarios.update', $this->usuarioOutraEscola), $updateData)->assertRedirect(route('usuarios.index'));
        
        $this->delete(route('usuarios.destroy', $this->usuarioOutraEscola))->assertRedirect(route('usuarios.index'));
    }

    #[Test]
    public function admin_nao_pode_excluir_a_si_mesmo()
    {
        $response = $this->actingAs($this->admin)->delete(route('usuarios.destroy', $this->admin));
        $response->assertRedirect();
        $this->assertDatabaseHas('usuarios', ['id_usuario' => $this->admin->id_usuario]);
    }
}