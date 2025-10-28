<?php

namespace Tests\Unit\User;
use Tests\TestCase;
use App\Models\Usuario;
use App\Models\Escola;
use App\Models\Municipio;
use App\Models\Notificacao;
use App\Models\UsuarioPreferencia;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Attributes\Set; 

class UsuarioModelTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function scope_ativos_retorna_apenas_usuarios_ativos()
    {
        Usuario::factory()->create(['status_aprovacao' => 'ativo']);
        Usuario::factory()->create(['status_aprovacao' => 'ativo']);
        Usuario::factory()->create(['status_aprovacao' => 'pendente']);
        Usuario::factory()->create(['status_aprovacao' => 'bloqueado']);

        $usuariosAtivos = Usuario::ativos()->get();

        $this->assertCount(2, $usuariosAtivos);
        foreach ($usuariosAtivos as $usuario) {
            $this->assertEquals('ativo', $usuario->status_aprovacao);
        }
    }

    /** @test */
    public function scope_pendentes_retorna_apenas_usuarios_pendentes()
    {
        Usuario::factory()->create(['status_aprovacao' => 'ativo']);
        Usuario::factory()->create(['status_aprovacao' => 'pendente']);
        Usuario::factory()->create(['status_aprovacao' => 'pendente']);

        $usuariosPendentes = Usuario::pendentes()->get();

        $this->assertCount(2, $usuariosPendentes);
        foreach ($usuariosPendentes as $usuario) {
            $this->assertEquals('pendente', $usuario->status_aprovacao);
        }
    }

     /** @test */
    public function scope_daEscola_retorna_usuarios_da_escola_especifica()
    {
        $municipio = Municipio::factory()->create();
        $escola1 = Escola::factory()->create(['id_municipio' => $municipio->id_municipio]);
        $escola2 = Escola::factory()->create(['id_municipio' => $municipio->id_municipio]);
        Usuario::factory()->create(['id_escola' => $escola1->id_escola]);
        Usuario::factory()->create(['id_escola' => $escola1->id_escola]);
        Usuario::factory()->create(['id_escola' => $escola2->id_escola]);
        Usuario::factory()->create(['id_escola' => null, 'tipo_usuario' => 'administrador']); 

        $usuariosEscola1 = Usuario::daEscola($escola1->id_escola)->get();
        $usuariosEscola2 = Usuario::daEscola($escola2->id_escola)->get();

        $this->assertCount(2, $usuariosEscola1);
        $this->assertCount(1, $usuariosEscola2);
    }

    /** @test */
    public function relacionamento_escola_funciona()
    {
        $municipio = Municipio::factory()->create();
        $escola = Escola::factory()->create(['id_municipio' => $municipio->id_municipio]);
        $usuario = Usuario::factory()->create(['id_escola' => $escola->id_escola]);

        $this->assertInstanceOf(Escola::class, $usuario->escola);
        $this->assertEquals($escola->id_escola, $usuario->escola->id_escola);
    }

    /** @test */
    public function relacionamento_preferencias_funciona()
    {
        $usuario = Usuario::factory()->create();
        UsuarioPreferencia::factory()->create(['id_usuario' => $usuario->id_usuario]);

        $this->assertInstanceOf(UsuarioPreferencia::class, $usuario->preferencias);
    }

     /** @test */
    public function relacionamento_notificacoes_funciona()
    {
        $usuario = Usuario::factory()->create();
        Notificacao::factory(3)->create(['id_usuario' => $usuario->id_usuario]);

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $usuario->notificacoes);
        $this->assertCount(3, $usuario->notificacoes);
    }

     /** @test */
    public function mutator_de_senha_usa_hash()
    {
        // O Laravel já faz isso automaticamente via `HasAttributes::password()`
        $senhaPlana = 'minhaSenha123';
        $usuario = Usuario::factory()->create(['password' => $senhaPlana]);
        $this->assertDatabaseMissing('usuarios', [
            'id_usuario' => $usuario->id_usuario,
            'password' => $senhaPlana
        ]);
        $this->assertTrue(Hash::check($senhaPlana, $usuario->fresh()->password));
    }

     /** @test */
    public function mutator_formata_cpf_ao_salvar()
    {
         $cpfFormatado = '123.456.789-00';
         $cpfEsperado = '12345678900';
         $usuario = Usuario::factory()->create(['cpf' => $cpfFormatado]);

         $this->assertDatabaseHas('usuarios', ['id_usuario' => $usuario->id_usuario, 'cpf' => $cpfEsperado]);
         $this->assertEquals($cpfEsperado, $usuario->fresh()->cpf); 
    }

    /** @test */
    public function mutator_formata_telefone_ao_salvar()
    {
        $telefoneFormatado = '(42) 99999-8888';
        $telefoneEsperado = '42999998888';
        $usuario = Usuario::factory()->create(['telefone' => $telefoneFormatado]);

        $this->assertDatabaseHas('usuarios', ['id_usuario' => $usuario->id_usuario, 'telefone' => $telefoneEsperado]);
         $this->assertEquals($telefoneEsperado, $usuario->fresh()->telefone);
    }
}