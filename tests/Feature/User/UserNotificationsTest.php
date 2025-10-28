<?php

namespace Tests\Feature\User;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification; 
use Illuminate\Support\Facades\Mail; 
use Tests\TestCase;
use App\Models\Usuario;
use App\Models\Escola;
use App\Models\Municipio;
use App\Models\Notificacao; 

class UserNotificationsTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $diretor;
    protected $escola;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = Usuario::factory()->create(['tipo_usuario' => 'administrador']);
        $municipio = Municipio::factory()->create();
        $this->escola = Escola::factory()->create(['id_municipio' => $municipio->id_municipio]);
        $this->diretor = Usuario::factory()->create(['tipo_usuario' => 'diretor', 'id_escola' => $this->escola->id_escola]);
        Mail::fake(); 
    }

    /** @test */
    public function admin_e_notificado_quando_novo_usuario_se_registra_pendente()
    {
        $dadosRegistro = [
            'nome_completo' => 'Pendente Teste',
            'username' => 'pendente.teste',
            'email' => 'pendente@example.com',
            'password' => 'Password@123',
            'password_confirmation' => 'Password@123',
            'data_nascimento' => '1998-03-15',
            'cpf' => '11122233344',
            'rg' => '12345678',
            'telefone' => '(31) 95555-4444',
            'formacao' => 'Biologia',
            'tipo_usuario' => 'professor',
            'id_escola' => $this->escola->id_escola,
        ];

        \Illuminate\Support\Facades\Validator::extend('celular_com_ddd', fn() => true);
        \Illuminate\Support\Facades\Validator::extend('cpf', fn() => true);
        \Illuminate\Support\Facades\Validator::extend('rg_valido', fn() => true);

        $response = $this->post(route('register'), $dadosRegistro);
        $response->assertRedirect(); 
        $this->assertDatabaseHas('usuarios', ['email' => 'pendente@example.com', 'status_aprovacao' => 'pendente']);

        $this->assertDatabaseHas('notificacoes', [
            'id_usuario' => $this->admin->id_usuario,
            'titulo' => 'Novo Usuário Aguardando Aprovação',
            // 'mensagem' => 'O usuário Pendente Teste (pendente.teste) se cadastrou e aguarda aprovação.' 
        ]);
        $notificacao = Notificacao::where('id_usuario', $this->admin->id_usuario)->latest('id_notificacao')->first();
        $this->assertNotNull($notificacao);
        $this->assertStringContainsString('Pendente Teste', $notificacao->mensagem);
        $this->assertStringContainsString('aguarda aprovação', $notificacao->mensagem);
    }

    /** @test */
    public function usuario_e_notificado_quando_seu_cadastro_e_aprovado()
    {
        $usuarioPendente = Usuario::factory()->create(['tipo_usuario' => 'professor', 'status_aprovacao' => 'pendente', 'id_escola' => $this->escola->id_escola]);

        $response = $this->actingAs($this->admin)->put(route('usuarios.update', $usuarioPendente), [
             // $usuarioPendente->status_aprovacao = 'ativo';
             // $usuarioPendente->save();
             'status_aprovacao' => 'ativo',
             'nome_completo' => $usuarioPendente->nome_completo,
             'username' => $usuarioPendente->username,
             'email' => $usuarioPendente->email,
             'data_nascimento' => $usuarioPendente->data_nascimento,
             'cpf' => $usuarioPendente->cpf,
             'rg' => $usuarioPendente->rg,
             'telefone' => $usuarioPendente->telefone,
             'formacao' => $usuarioPendente->formacao,
             'tipo_usuario' => $usuarioPendente->tipo_usuario,
             'id_escola' => $usuarioPendente->id_escola,
        ]);

         $response->assertRedirect(route('usuarios.index')); 
         $this->assertDatabaseHas('usuarios', ['id_usuario' => $usuarioPendente->id_usuario, 'status_aprovacao' => 'ativo']);
        $this->assertDatabaseHas('notificacoes', [
            'id_usuario' => $usuarioPendente->id_usuario,
            'titulo' => 'Status do Cadastro Atualizado',
            // 'mensagem' => 'Seu cadastro foi aprovado. Você já pode acessar o sistema.'
        ]);
        $notificacao = Notificacao::where('id_usuario', $usuarioPendente->id_usuario)->latest('id_notificacao')->first();
        $this->assertNotNull($notificacao);
        $this->assertStringContainsString('cadastro foi aprovado', $notificacao->mensagem);
    }
}