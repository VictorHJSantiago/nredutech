<?php

namespace Tests\Feature\User;

use Tests\TestCase;
use App\Models\Usuario;
use App\Models\Escola;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserRoutesTest extends TestCase
{
    use RefreshDatabase;

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

        $this->escola = Escola::factory()->create();
        $this->outraEscola = Escola::factory()->create();
        
        $this->admin = Usuario::factory()->administrador()->create();
        $this->diretor = Usuario::factory()->diretor()->create(['id_escola' => $this->escola->id_escola]);
        $this->professor = Usuario::factory()->professor()->create(['id_escola' => $this->escola->id_escola]);

        $this->usuarioEscola = Usuario::factory()->professor()->create(['id_escola' => $this->escola->id_escola]);
        $this->usuarioOutraEscola = Usuario::factory()->professor()->create(['id_escola' => $this->outraEscola->id_escola]);
    }

    public function test_guest_is_redirected_from_all_user_routes()
    {
        $this->get(route('usuarios.index'))->assertRedirect(route('login'));
        $this->get(route('usuarios.create'))->assertRedirect(route('login'));
        $this->post(route('usuarios.store'))->assertRedirect(route('login'));
        $this->get(route('usuarios.edit', $this->usuarioEscola))->assertRedirect(route('login'));
        $this->put(route('usuarios.update', $this->usuarioEscola))->assertRedirect(route('login'));
        $this->delete(route('usuarios.destroy', $this->usuarioEscola))->assertRedirect(route('login'));
    }

    public function test_professor_can_only_view_index()
    {
        $this->actingAs($this->professor);

        $this->get(route('usuarios.index'))->assertOk();
        $this->get(route('usuarios.create'))->assertForbidden();
        $this->post(route('usuarios.store'))->assertForbidden();
        $this->get(route('usuarios.edit', $this->usuarioEscola))->assertForbidden();
        $this->put(route('usuarios.update', $this->usuarioEscola))->assertForbidden();
        $this->delete(route('usuarios.destroy', $this->usuarioEscola))->assertForbidden();
    }

    public function test_diretor_can_manage_own_school_users()
    {
        $this->actingAs($this->diretor);

        $this->get(route('usuarios.index'))->assertOk();
        $this->get(route('usuarios.create'))->assertOk();
        
        $storeData = Usuario::factory()->make(['id_escola' => $this->escola->id_escola, 'tipo_usuario' => 'professor'])->toArray();
        $storeData['password'] = 'password12345678';
        $storeData['password_confirmation'] = 'password12345678';
        $this->post(route('usuarios.store'), $storeData)->assertRedirect(route('usuarios.index'));

        $this->get(route('usuarios.edit', $this->usuarioEscola))->assertOk();
        
        $updateData = $this->usuarioEscola->toArray();
        $updateData['nome_completo'] = 'Nome Atualizado';
        $this->put(route('usuarios.update', $this->usuarioEscola), $updateData)->assertRedirect(route('usuarios.index'));
        
        $this->delete(route('usuarios.destroy', $this->usuarioEscola))->assertRedirect(route('usuarios.index'));
    }

    public function test_diretor_is_forbidden_from_managing_other_school_users()
    {
        $this->actingAs($this->diretor);

        $this->get(route('usuarios.edit', $this->usuarioOutraEscola))->assertForbidden();
        $this->put(route('usuarios.update', $this->usuarioOutraEscola), [])->assertForbidden();
        $this->delete(route('usuarios.destroy', $this->usuarioOutraEscola))->assertForbidden();
    }

    public function test_diretor_is_forbidden_from_managing_admins()
    {
        $this->actingAs($this->diretor);

        $this->get(route('usuarios.edit', $this->admin))->assertForbidden();
        $this->put(route('usuarios.update', $this->admin), [])->assertForbidden();
        $this->delete(route('usuarios.destroy', $this->admin))->assertForbidden();
    }

    public function test_admin_can_manage_all_users()
    {
        $this->actingAs($this->admin);

        $this->get(route('usuarios.index'))->assertOk();
        $this->get(route('usuarios.create'))->assertOk();
        
        $storeData = Usuario::factory()->make(['id_escola' => $this->outraEscola->id_escola, 'tipo_usuario' => 'diretor'])->toArray();
        $storeData['password'] = 'password12345678';
        $storeData['password_confirmation'] = 'password12345678';
        $this->post(route('usuarios.store'), $storeData)->assertRedirect(route('usuarios.index'));

        $this->get(route('usuarios.edit', $this->usuarioOutraEscola))->assertOk();
        
        $updateData = $this->usuarioOutraEscola->toArray();
        $updateData['nome_completo'] = 'Nome Atualizado Pelo Admin';
        $this->put(route('usuarios.update', $this->usuarioOutraEscola), $updateData)->assertRedirect(route('usuarios.index'));
        
        $this->delete(route('usuarios.destroy', $this->usuarioOutraEscola))->assertRedirect(route('usuarios.index'));
    }

    public function test_admin_cannot_delete_self()
    {
        $response = $this->actingAs($this->admin)->delete(route('usuarios.destroy', $this->admin));
        $response->assertForbidden();
        $this->assertDatabaseHas('usuarios', ['id_usuario' => $this->admin->id_usuario]);
    }
}