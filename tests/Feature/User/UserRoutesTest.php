<?php

namespace Tests\Feature\User;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Usuario;
use App\Models\Escola;
use App\Models\Municipio;
use Illuminate\Support\Facades\Hash;

class UserRoutesTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $professor;
    protected $escola;

    protected function setUp(): void
    {
        parent::setUp();
        $municipio = Municipio::factory()->create();
        $this->escola = Escola::factory()->create(['id_municipio' => $municipio->id_municipio]);
        $this->admin = Usuario::factory()->create(['tipo_usuario' => 'administrador']);
        $this->professor = Usuario::factory()->create(['tipo_usuario' => 'professor', 'id_escola' => $this->escola->id_escola, 'password' => Hash::make('password')]);
    }

    /** @test */
    public function admin_pode_listar_usuarios()
    {
        $response = $this->actingAs($this->admin)->get(route('usuarios.index'));
        $response->assertStatus(200);
        $response->assertViewIs('users.index');
    }

    /** @test */
    public function admin_pode_criar_usuario()
    {
        \Illuminate\Support\Facades\Validator::extend('celular_com_ddd', fn() => true);
        \Illuminate\Support\Facades\Validator::extend('cpf', fn() => true);
        \Illuminate\Support\Facades\Validator::extend('rg_valido', fn() => true);

        $dadosUsuario = Usuario::factory()->make(['tipo_usuario' => 'professor', 'id_escola' => $this->escola->id_escola])->toArray();
        $dadosUsuario['password'] = 'Password@123';
        $dadosUsuario['password_confirmation'] = 'Password@123';
        $response = $this->actingAs($this->admin)->post(route('usuarios.store'), $dadosUsuario);
        $response->assertRedirect(route('usuarios.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('usuarios', ['email' => $dadosUsuario['email']]);
    }

    /** @test */
    public function usuario_logado_pode_ver_seu_perfil()
    {
        $response = $this->actingAs($this->professor)->get(route('profile.edit'));
        $response->assertStatus(200);
        $response->assertViewIs('profile.edit');
        $response->assertSee($this->professor->email); 
    }

    /** @test */
    public function usuario_logado_pode_atualizar_seu_perfil()
    {
        $novoNome = 'Professor Nome Atualizado';
        $novoTelefone = '(55) 55555-5555';
        \Illuminate\Support\Facades\Validator::extend('celular_com_ddd', fn() => true); 

        $response = $this->actingAs($this->professor)->patch(route('profile.update'), [
            'nome_completo' => $novoNome,
            'email' => $this->professor->email, 
            'telefone' => $novoTelefone,
        ]);

        $response->assertRedirect(route('profile.edit'));
        $response->assertSessionHas('status', 'profile-updated'); 
        $this->assertDatabaseHas('usuarios', [
            'id_usuario' => $this->professor->id_usuario,
            'nome_completo' => $novoNome,
            'telefone' => '55555555555' 
        ]);
    }

    /** @test */
    public function pode_fazer_login_com_credenciais_validas()
    {
        $response = $this->post(route('login'), [
            'login' => $this->professor->email,
            'password' => 'password',
        ]);

        $response->assertRedirect(route('index')); 
        $this->assertAuthenticatedAs($this->professor);
    }

     /** @test */
    public function nao_pode_fazer_login_com_senha_invalida()
    {
        $response = $this->post(route('login'), [
            'login' => $this->professor->email,
            'password' => 'senha_errada',
        ]);

        $response->assertSessionHasErrors('login'); 
        $this->assertGuest();
    }

    /** @test */
    public function pode_fazer_logout()
    {
        $this->actingAs($this->professor);
        $this->assertAuthenticated();

        $response = $this->post(route('logout'));

        $response->assertRedirect('/'); 
        $this->assertGuest();
    }

    // recuperação de senha (PasswordResetLinkController, NewPasswordController), etc.

}