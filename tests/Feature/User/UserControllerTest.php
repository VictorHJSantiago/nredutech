<?php

namespace Tests\Feature\User;

use Tests\TestCase;
use App\Models\Usuario;
use App\Models\Escola;
use App\Models\Municipio;
use App\Models\RecursoDidatico;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Foundation\Testing\WithFaker;

class UserControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private Usuario $admin;
    private Usuario $diretorA;
    private Usuario $professorA;
    private Usuario $diretorB;
    private Escola $escolaA;
    private Escola $escolaB;

    protected function setUp(): void
    {
        parent::setUp();
        
        $municipio = Municipio::create(['nome' => 'Municipio Teste', 'estado' => 'PR']);
        $this->escolaA = Escola::create(['nome' => 'Escola Teste A', 'id_municipio' => $municipio->id_municipio, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana']);
        $this->escolaB = Escola::create(['nome' => 'Escola Teste B', 'id_municipio' => $municipio->id_municipio, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana']);
        
        $this->admin = Usuario::factory()->create($this->getValidUserData(['tipo_usuario' => 'administrador', 'id_escola' => null]));
        $this->diretorA = Usuario::factory()->create($this->getValidUserData(['tipo_usuario' => 'diretor', 'id_escola' => $this->escolaA->id_escola]));
        $this->professorA = Usuario::factory()->create($this->getValidUserData(['tipo_usuario' => 'professor', 'id_escola' => $this->escolaA->id_escola, 'status_aprovacao' => 'ativo']));
        $this->diretorB = Usuario::factory()->create($this->getValidUserData(['tipo_usuario' => 'diretor', 'id_escola' => $this->escolaB->id_escola, 'status_aprovacao' => 'pendente']));
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
    public function admin_pode_ver_todos_usuarios_na_listagem()
    {
        $response = $this->actingAs($this->admin)->get(route('usuarios.index'));
        $response->assertOk();
        $response->assertViewHas('usuarios', fn ($users) => $users->count() === 4);
    }

    #[Test]
    public function diretor_pode_ver_apenas_usuarios_da_propria_escola_na_listagem()
    {
        $response = $this->actingAs($this->diretorA)->get(route('usuarios.index'));
        $response->assertOk();
        $response->assertViewHas('usuarios', fn ($users) => $users->count() === 2);
        $response->assertSee($this->diretorA->nome_completo);
        $response->assertSee($this->professorA->nome_completo);
        $response->assertDontSee($this->admin->nome_completo);
        $response->assertDontSee($this->diretorB->nome_completo);
    }

    #[Test]
    public function professor_pode_ver_apenas_usuarios_da_propria_escola_na_listagem()
    {
        $response = $this->actingAs($this->professorA)->get(route('usuarios.index'));
        $response->assertOk();
        $response->assertViewHas('usuarios', fn ($users) => $users->count() === 4);
        $response->assertSee($this->diretorA->nome_completo);
        $response->assertSee($this->admin->nome_completo);
    }

    #[Test]
    public function filtros_da_listagem_de_usuarios_funcionam_corretamente()
    {
        $response = $this->actingAs($this->admin)->get(route('usuarios.index', ['search' => $this->professorA->nome_completo]));
        $response->assertViewHas('usuarios', fn ($users) => $users->count() === 1 && $users->first()->nome_completo === $this->professorA->nome_completo);

        $response = $this->actingAs($this->admin)->get(route('usuarios.index', ['status' => 'pendente']));
        $response->assertViewHas('usuarios', fn ($users) => $users->count() === 1 && $users->first()->nome_completo === $this->diretorB->nome_completo);

        $response = $this->actingAs($this->admin)->get(route('usuarios.index', ['tipo_usuario' => 'professor']));
        $response->assertViewHas('usuarios', fn ($users) => $users->count() === 1);
    }

    #[Test]
    public function admin_pode_cadastrar_qualquer_usuario()
    {
        $data = Usuario::factory()->make($this->getValidUserData(['tipo_usuario' => 'diretor', 'id_escola' => $this->escolaB->id_escola]))->toArray();
        $data['password'] = 'ValidPassword@123456';
        $data['password_confirmation'] = 'ValidPassword@123456';

        $response = $this->actingAs($this->admin)->post(route('usuarios.store'), $data);
        
        $response->assertRedirect(route('usuarios.index'));
        $response->assertSessionHas('success', 'Usuário cadastrado com sucesso!');
        $this->assertDatabaseHas('usuarios', ['username' => $data['username'], 'id_escola' => $this->escolaB->id_escola]);
    }

    #[Test]
    public function diretor_pode_cadastrar_usuario_da_propria_escola()
    {
        $data = Usuario::factory()->make($this->getValidUserData(['tipo_usuario' => 'professor', 'id_escola' => $this->escolaA->id_escola]))->toArray();
        $data['password'] = 'ValidPassword@123456';
        $data['password_confirmation'] = 'ValidPassword@123456';

        $response = $this->actingAs($this->diretorA)->post(route('usuarios.store'), $data);
        
        $response->assertRedirect(route('usuarios.index'));
        $this->assertDatabaseHas('usuarios', ['username' => $data['username'], 'id_escola' => $this->escolaA->id_escola]);
    }

    #[Test]
    public function diretor_nao_pode_cadastrar_usuario_admin()
    {
        $data = Usuario::factory()->make($this->getValidUserData(['tipo_usuario' => 'administrador', 'id_escola' => $this->escolaA->id_escola]))->toArray();
        $data['password'] = 'ValidPassword@123456';
        $data['password_confirmation'] = 'ValidPassword@123456';

        $response = $this->actingAs($this->diretorA)->post(route('usuarios.store'), $data);
        $response->assertSessionHasErrors(['tipo_usuario', 'id_escola']);
    }

    #[Test]
    public function diretor_nao_pode_cadastrar_usuario_para_outra_escola()
    {
        $data = Usuario::factory()->make($this->getValidUserData(['tipo_usuario' => 'professor', 'id_escola' => $this->escolaB->id_escola]))->toArray();
        $data['password'] = 'ValidPassword@123456';
        $data['password_confirmation'] = 'ValidPassword@123456';

        $response = $this->actingAs($this->diretorA)->post(route('usuarios.store'), $data);
        $response->assertSessionHasErrors('id_escola');
    }

    #[Test]
    public function admin_pode_atualizar_qualquer_usuario()
    {
        $data = $this->diretorB->toArray();
        $data['nome_completo'] = 'Nome Atualizado Admin';
        $data['id_escola'] = $this->escolaA->id_escola;

        $response = $this->actingAs($this->admin)->put(route('usuarios.update', $this->diretorB), $data);
        
        $response->assertRedirect(route('usuarios.index'));
        $this->assertDatabaseHas('usuarios', ['id_usuario' => $this->diretorB->id_usuario, 'nome_completo' => 'Nome Atualizado Admin', 'id_escola' => $this->escolaA->id_escola]);
    }

    #[Test]
    public function atualizacao_de_usuario_lida_com_senha_corretamente()
    {
        $oldHash = $this->professorA->password;
        $data = $this->professorA->toArray();
        $data['nome_completo'] = 'Nome Atualizado';
        
        $response = $this->actingAs($this->diretorA)->put(route('usuarios.update', $this->professorA), $data);
        $response->assertRedirect(route('usuarios.index'));
        $this->professorA->refresh();
        $this->assertEquals('Nome Atualizado', $this->professorA->nome_completo);
        $this->assertEquals($oldHash, $this->professorA->password);

        $data['password'] = 'ValidPassword@654321';
        $data['password_confirmation'] = 'ValidPassword@654321';

        $this->actingAs($this->diretorA)->put(route('usuarios.update', $this->professorA), $data);
        $this->professorA->refresh();
        $this->assertTrue(Hash::check('ValidPassword@654321', $this->professorA->password));
        $this->assertNotEquals($oldHash, $this->professorA->password);
    }

    #[Test]
    public function diretor_nao_pode_atualizar_usuario_de_outra_escola()
    {
        $data = $this->diretorB->toArray();
        $data['nome_completo'] = 'Update Proibido';

        $response = $this->actingAs($this->diretorA)->put(route('usuarios.update', $this->diretorB), $data);
        $response->assertForbidden();
    }

    #[Test]
    public function admin_pode_destruir_usuario()
    {
        $response = $this->actingAs($this->admin)->delete(route('usuarios.destroy', $this->diretorB));
        
        $response->assertRedirect(route('usuarios.index'));
        $response->assertSessionHas('success', 'Usuário excluído com sucesso!');
        $this->assertSoftDeleted('usuarios', ['id_usuario' => $this->diretorB->id_usuario]);
    }

    #[Test]
    public function admin_nao_pode_destruir_a_si_mesmo()
    {
        $response = $this->actingAs($this->admin)->delete(route('usuarios.destroy', $this->admin));
        
        $response->assertRedirect();
        $this->assertDatabaseHas('usuarios', ['id_usuario' => $this->admin->id_usuario]);
    }

    #[Test]
    public function diretor_pode_destruir_usuario_da_propria_escola()
    {
        $response = $this->actingAs($this->diretorA)->delete(route('usuarios.destroy', $this->professorA));
        
        $response->assertRedirect(route('usuarios.index'));
        $this->assertSoftDeleted('usuarios', ['id_usuario' => $this->professorA->id_usuario]);
    }

    #[Test]
    public function diretor_nao_pode_destruir_usuario_de_outra_escola()
    {
        $response = $this->actingAs($this->diretorA)->delete(route('usuarios.destroy', $this->diretorB));
        $response->assertForbidden();
    }

    #[Test]
    public function nao_pode_destruir_usuario_com_dependencias()
    {
        RecursoDidatico::factory()->create(['id_usuario_criador' => $this->professorA->id_usuario, 'id_escola' => $this->professorA->id_escola]);
        
        $response = $this->actingAs($this->admin)->delete(route('usuarios.destroy', $this->professorA));
        
        $response->assertRedirect(route('usuarios.index'));
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('usuarios', ['id_usuario' => $this->professorA->id_usuario]);
    }
}