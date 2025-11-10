<?php

namespace Tests\Feature\User;

use Tests\TestCase;
use App\Models\Usuario;
use App\Models\Escola;
use App\Models\RecursoDidatico;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    private Usuario $admin;
    private Usuario $diretorA;
    private Usuario $professorA;
    private Usuario $diretorB;
    private Escola $escolaA;
    private Escola $escolaB;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->escolaA = Escola::factory()->create();
        $this->escolaB = Escola::factory()->create();
        
        $this->admin = Usuario::factory()->administrador()->create();
        $this->diretorA = Usuario::factory()->diretor()->create(['id_escola' => $this->escolaA->id_escola]);
        $this->professorA = Usuario::factory()->professor()->create(['id_escola' => $this->escolaA->id_escola, 'status_aprovacao' => 'ativo']);
        $this->diretorB = Usuario::factory()->diretor()->create(['id_escola' => $this->escolaB->id_escola, 'status_aprovacao' => 'pendente']);
    }

    public function test_admin_can_view_all_users_on_index()
    {
        $response = $this->actingAs($this->admin)->get(route('usuarios.index'));
        $response->assertOk();
        $response->assertViewHas('usuarios', fn ($users) => $users->count() === 4);
    }

    public function test_diretor_can_view_only_own_school_users_on_index()
    {
        $response = $this->actingAs($this->diretorA)->get(route('usuarios.index'));
        $response->assertOk();
        $response->assertViewHas('usuarios', fn ($users) => $users->count() === 2);
        $response->assertSee($this->diretorA->nome_completo);
        $response->assertSee($this->professorA->nome_completo);
        $response->assertDontSee($this->admin->nome_completo);
        $response->assertDontSee($this->diretorB->nome_completo);
    }

    public function test_professor_can_view_only_own_school_users_on_index()
    {
        $response = $this->actingAs($this->professorA)->get(route('usuarios.index'));
        $response->assertOk();
        $response->assertViewHas('usuarios', fn ($users) => $users->count() === 2);
        $response->assertSee($this->diretorA->nome_completo);
        $response->assertDontSee($this->admin->nome_completo);
    }

    public function test_user_index_filters_work_correctly()
    {
        $response = $this->actingAs($this->admin)->get(route('usuarios.index', ['search' => $this->professorA->nome_completo]));
        $response->assertViewHas('usuarios', fn ($users) => $users->count() === 1 && $users->first()->nome_completo === $this->professorA->nome_completo);

        $response = $this->actingAs($this->admin)->get(route('usuarios.index', ['status' => 'pendente']));
        $response->assertViewHas('usuarios', fn ($users) => $users->count() === 1 && $users->first()->nome_completo === $this->diretorB->nome_completo);

        $response = $this->actingAs($this->admin)->get(route('usuarios.index', ['tipo_usuario' => 'administrador']));
        $response->assertViewHas('usuarios', fn ($users) => $users->count() === 1 && $users->first()->nome_completo === $this->admin->nome_completo);
    }

    public function test_admin_can_store_any_user()
    {
        $data = Usuario::factory()->make(['tipo_usuario' => 'diretor', 'id_escola' => $this->escolaB->id_escola])->toArray();
        $data['password'] = 'password12345678';
        $data['password_confirmation'] = 'password12345678';

        $response = $this->actingAs($this->admin)->post(route('usuarios.store'), $data);
        
        $response->assertRedirect(route('usuarios.index'));
        $response->assertSessionHas('success', 'Usuário cadastrado com sucesso!');
        $this->assertDatabaseHas('usuarios', ['username' => $data['username'], 'id_escola' => $this->escolaB->id_escola]);
    }

    public function test_diretor_can_store_own_school_user()
    {
        $data = Usuario::factory()->make(['tipo_usuario' => 'professor', 'id_escola' => $this->escolaA->id_escola])->toArray();
        $data['password'] = 'password12345678';
        $data['password_confirmation'] = 'password12345678';

        $response = $this->actingAs($this->diretorA)->post(route('usuarios.store'), $data);
        
        $response->assertRedirect(route('usuarios.index'));
        $this->assertDatabaseHas('usuarios', ['username' => $data['username'], 'id_escola' => $this->escolaA->id_escola]);
    }

    public function test_diretor_cannot_store_admin_user()
    {
        $data = Usuario::factory()->make(['tipo_usuario' => 'administrador', 'id_escola' => $this->escolaA->id_escola])->toArray();
        $data['password'] = 'password12345678';
        $data['password_confirmation'] = 'password12345678';

        $response = $this->actingAs($this->diretorA)->post(route('usuarios.store'), $data);
        $response->assertSessionHasErrors('tipo_usuario');
    }

    public function test_diretor_cannot_store_user_for_other_school()
    {
        $data = Usuario::factory()->make(['tipo_usuario' => 'professor', 'id_escola' => $this->escolaB->id_escola])->toArray();
        $data['password'] = 'password12345678';
        $data['password_confirmation'] = 'password12345678';

        $response = $this->actingAs($this->diretorA)->post(route('usuarios.store'), $data);
        $response->assertSessionHasErrors('id_escola');
    }

    public function test_admin_can_update_any_user()
    {
        $data = $this->diretorB->toArray();
        $data['nome_completo'] = 'Nome Atualizado Admin';
        $data['id_escola'] = $this->escolaA->id_escola;

        $response = $this->actingAs($this->admin)->put(route('usuarios.update', $this->diretorB), $data);
        
        $response->assertRedirect(route('usuarios.index'));
        $this->assertDatabaseHas('usuarios', ['id_usuario' => $this->diretorB->id_usuario, 'nome_completo' => 'Nome Atualizado Admin', 'id_escola' => $this->escolaA->id_escola]);
    }

    public function test_user_update_handles_password_correctly()
    {
        $oldHash = $this->professorA->password;
        $data = $this->professorA->toArray();
        $data['nome_completo'] = 'Nome Atualizado';
        
        $response = $this->actingAs($this->diretorA)->put(route('usuarios.update', $this->professorA), $data);
        $response->assertRedirect(route('usuarios.index'));
        $this->professorA->refresh();
        $this->assertEquals('Nome Atualizado', $this->professorA->nome_completo);
        $this->assertEquals($oldHash, $this->professorA->password);

        $data['password'] = 'novaSenha12345678';
        $data['password_confirmation'] = 'novaSenha12345678';

        $this->actingAs($this->diretorA)->put(route('usuarios.update', $this->professorA), $data);
        $this->professorA->refresh();
        $this->assertTrue(Hash::check('novaSenha12345678', $this->professorA->password));
        $this->assertNotEquals($oldHash, $this->professorA->password);
    }

    public function test_diretor_cannot_update_other_school_user()
    {
        $data = $this->diretorB->toArray();
        $data['nome_completo'] = 'Update Proibido';

        $response = $this->actingAs($this->diretorA)->put(route('usuarios.update', $this->diretorB), $data);
        $response->assertForbidden();
    }

    public function test_admin_can_destroy_user()
    {
        $response = $this->actingAs($this->admin)->delete(route('usuarios.destroy', $this->diretorB));
        
        $response->assertRedirect(route('usuarios.index'));
        $response->assertSessionHas('success', 'Usuário excluído com sucesso!');
        $this->assertDatabaseMissing('usuarios', ['id_usuario' => $this->diretorB->id_usuario]);
    }

    public function test_admin_cannot_destroy_self()
    {
        $response = $this->actingAs($this->admin)->delete(route('usuarios.destroy', $this->admin));
        
        $response->assertForbidden();
        $this->assertDatabaseHas('usuarios', ['id_usuario' => $this->admin->id_usuario]);
    }

    public function test_diretor_can_destroy_own_school_user()
    {
        $response = $this->actingAs($this->diretorA)->delete(route('usuarios.destroy', $this->professorA));
        
        $response->assertRedirect(route('usuarios.index'));
        $this->assertDatabaseMissing('usuarios', ['id_usuario' => $this->professorA->id_usuario]);
    }

    public function test_diretor_cannot_destroy_other_school_user()
    {
        $response = $this->actingAs($this->diretorA)->delete(route('usuarios.destroy', $this->diretorB));
        $response->assertForbidden();
    }

    public function test_cannot_destroy_user_with_dependencies()
    {
        RecursoDidatico::factory()->create(['id_usuario_criador' => $this->professorA->id_usuario]);
        
        $response = $this->actingAs($this->admin)->delete(route('usuarios.destroy', $this->professorA));
        
        $response->assertRedirect(route('usuarios.index'));
        $response->assertSessionHas('error', 'Não é possível excluir o usuário pois ele possui 1 recurso(s) criado(s) ou 0 oferta(s) de componente(s) vinculada(s).');
        $this->assertDatabaseHas('usuarios', ['id_usuario' => $this->professorA->id_usuario]);
    }
}