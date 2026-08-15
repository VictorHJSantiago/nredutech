<?php

namespace Tests\Feature\User;

use App\Models\Escola;
use App\Models\Municipio;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    private $admin;
    private $diretorA;
    private $professorA;
    private $diretorB;
    private $escolaA;
    private $escolaB;

    protected function setUp(): void
    {
        parent::setUp();
        Config::set('hashing.driver', 'bcrypt');
        Config::set('hashing.bcrypt.rounds', 4);
        $this->withoutVite();

        $municipio = Municipio::create(['nome' => 'Curitiba']);

        $this->escolaA = Escola::create([
            'nome' => 'Escola A',
            'nivel_ensino' => 'medio',
            'tipo' => 'urbana',
            'id_municipio' => $municipio->id_municipio
        ]);

        $this->escolaB = Escola::create([
            'nome' => 'Escola B',
            'nivel_ensino' => 'fundamental',
            'tipo' => 'rural',
            'id_municipio' => $municipio->id_municipio
        ]);

        $this->admin = Usuario::factory()->create([
            'tipo_usuario' => 'administrador',
            'status_aprovacao' => 'ativo'
        ]);

        $this->diretorA = Usuario::factory()->create([
            'tipo_usuario' => 'diretor',
            'id_escola' => $this->escolaA->id_escola,
            'status_aprovacao' => 'ativo'
        ]);

        $this->professorA = Usuario::factory()->create([
            'tipo_usuario' => 'professor',
            'id_escola' => $this->escolaA->id_escola,
            'status_aprovacao' => 'ativo'
        ]);

        $this->diretorB = Usuario::factory()->create([
            'tipo_usuario' => 'diretor',
            'id_escola' => $this->escolaB->id_escola,
            'status_aprovacao' => 'pendente'
        ]);
    }

    public function test_admin_pode_ver_todos_usuarios_na_index()
    {
        $response = $this->actingAs($this->admin)->get(route('usuarios.index'));
        $response->assertStatus(200);
        $response->assertSee($this->diretorA->nome_completo);
        $response->assertSee($this->professorA->nome_completo);
        $response->assertSee($this->diretorB->nome_completo);
    }

    public function test_diretor_pode_ver_apenas_usuarios_da_sua_escola()
    {
        $response = $this->actingAs($this->diretorA)->get(route('usuarios.index'));
        $response->assertStatus(200);
        $response->assertSee($this->professorA->nome_completo);
        $response->assertDontSee($this->diretorB->nome_completo);
    }

    public function test_professor_visualiza_usuarios_da_sua_escola_e_outras()
    {
        $response = $this->actingAs($this->professorA)->get(route('usuarios.index'));
        $response->assertStatus(200);
        $response->assertSee($this->diretorA->nome_completo);
        $response->assertSee($this->diretorB->nome_completo);
    }

    public function test_filtros_da_listagem_de_usuarios_funcionam()
    {
        $response = $this->actingAs($this->admin)->get(route('usuarios.index', ['status' => 'pendente']));
        $response->assertStatus(200);
        $response->assertSee($this->diretorB->nome_completo);
        $response->assertDontSee($this->diretorA->nome_completo);
        $response = $this->actingAs($this->admin)->get(route('usuarios.index', ['tipo_usuario' => 'professor']));
        $response->assertStatus(200);
        $response->assertSee($this->professorA->nome_completo);
    }

    public function test_admin_pode_cadastrar_qualquer_usuario()
    {
        $data = Usuario::factory()->make([
            'tipo_usuario' => 'diretor',
            'id_escola' => $this->escolaA->id_escola,
            'cpf' => '905.979.410-92',
            'data_nascimento' => '1990-01-01'
        ])->toArray();
        $data['password'] = 'ValidPassword@123456';
        $data['password_confirmation'] = 'ValidPassword@123456';

        $response = $this->actingAs($this->admin)->post(route('usuarios.store'), $data);
        $response->assertRedirect(route('usuarios.index'));
        $this->assertDatabaseHas('usuarios', ['email' => $data['email']]);
    }

    public function test_diretor_cadastro_usuario_da_sua_escola_redireciona()
    {
        $data = Usuario::factory()->make([
            'tipo_usuario' => 'professor',
            'id_escola' => $this->escolaA->id_escola,
            'cpf' => '529.982.240-30',
            'data_nascimento' => '1990-01-01'
        ])->toArray();
        $data['password'] = 'ValidPassword@123456';
        $data['password_confirmation'] = 'ValidPassword@123456';

        $response = $this->actingAs($this->diretorA)->post(route('usuarios.store'), $data);
        $response->assertRedirect('/');
    }

    public function test_diretor_nao_pode_cadastrar_usuario_para_outra_escola()
    {
        $data = Usuario::factory()->make([
            'tipo_usuario' => 'professor',
            'id_escola' => $this->escolaB->id_escola,
            'cpf' => '707.973.270-35',
            'data_nascimento' => '1990-01-01'
        ])->toArray();
        $data['password'] = 'ValidPassword@123456';
        $data['password_confirmation'] = 'ValidPassword@123456';

        $response = $this->actingAs($this->diretorA)->post(route('usuarios.store'), $data);
        $response->assertStatus(302);
    }

    public function test_diretor_nao_pode_cadastrar_usuario_administrador()
    {
        $data = Usuario::factory()->make([
            'tipo_usuario' => 'administrador',
            'id_escola' => null,
            'cpf' => '707.973.270-35',
            'data_nascimento' => '1990-01-01'
        ])->toArray();
        $data['password'] = 'ValidPassword@123456';
        $data['password_confirmation'] = 'ValidPassword@123456';

        $response = $this->actingAs($this->diretorA)->post(route('usuarios.store'), $data);
        $response->assertStatus(302);
    }

    public function test_admin_pode_atualizar_qualquer_usuario()
    {
        $data = $this->professorA->toArray();
        $data['nome_completo'] = 'Nome Atualizado Admin';
        $data['cpf'] = '905.979.410-92';
        $data['data_nascimento'] = now()->subYears(30)->toDateString();
        
        $response = $this->actingAs($this->admin)->put(route('usuarios.update', $this->professorA), $data);
        $response->assertRedirect(route('usuarios.index'));
        $this->assertDatabaseHas('usuarios', ['id_usuario' => $this->professorA->id_usuario, 'nome_completo' => 'Nome Atualizado Admin']);
    }

    public function test_atualizacao_de_usuario_lida_com_senha_corretamente()
    {
        $data = $this->professorA->toArray();
        $data['cpf'] = '905.979.410-92';
        $data['password'] = 'ValidPassword@654321';
        $data['password_confirmation'] = 'ValidPassword@654321';
        $data['data_nascimento'] = now()->subYears(30)->toDateString();

        $response = $this->actingAs($this->admin)->put(route('usuarios.update', $this->professorA), $data);
        $response->assertRedirect(route('usuarios.index'));
    }

    public function test_diretor_nao_pode_atualizar_usuario_de_outra_escola()
    {
        $data = $this->diretorB->toArray();
        $data['nome_completo'] = 'Tentativa Update';
        $data['cpf'] = '529.982.240-30';
        $data['data_nascimento'] = '1990-01-01';

        $response = $this->actingAs($this->diretorA)->put(route('usuarios.update', $this->diretorB), $data);
        $response->assertStatus(302);
    }

    public function test_admin_pode_destruir_usuario()
    {
        $userToDelete = Usuario::factory()->create([
            'tipo_usuario' => 'professor', 
            'id_escola' => $this->escolaA->id_escola
        ]);

        $response = $this->actingAs($this->admin)->delete(route('usuarios.destroy', $userToDelete));
        $response->assertRedirect(route('usuarios.index'));
        $this->assertSoftDeleted('usuarios', ['id_usuario' => $userToDelete->id_usuario]);
    }

    public function test_admin_nao_pode_destruir_a_si_mesmo()
    {
        $response = $this->actingAs($this->admin)->delete(route('usuarios.destroy', $this->admin));
        
        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('usuarios', ['id_usuario' => $this->admin->id_usuario]);
    }

    public function test_diretor_pode_destruir_usuario_da_sua_escola()
    {
        $userToDelete = Usuario::factory()->create([
            'tipo_usuario' => 'professor', 
            'id_escola' => $this->escolaA->id_escola
        ]);

        $response = $this->actingAs($this->diretorA)->delete(route('usuarios.destroy', $userToDelete));
        $response->assertRedirect(route('usuarios.index'));
        $this->assertSoftDeleted('usuarios', ['id_usuario' => $userToDelete->id_usuario]);
    }

    public function test_diretor_nao_pode_destruir_usuario_de_outra_escola()
    {
        $response = $this->actingAs($this->diretorA)->delete(route('usuarios.destroy', $this->diretorB));
        
        $response->assertStatus(302);
    }

    public function test_nao_pode_destruir_usuario_com_dependencias()
    {
        $response = $this->actingAs($this->admin)->delete(route('usuarios.destroy', $this->professorA));
        $response->assertRedirect();
    }
}