<?php

namespace Tests\Unit\Settings;

use Tests\TestCase;
use App\Models\Escola;
use App\Models\Municipio;
use App\Models\Usuario;
use App\Models\UsuarioPreferencia;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UsuarioPreferenciaModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_modelo_de_preferencia_de_usuario_usa_tabela_correta()
    {
        $preferencia = new UsuarioPreferencia();
        $this->assertEquals('usuario_preferencias', $preferencia->getTable());
    }

    public function test_modelo_de_preferencia_de_usuario_usa_chave_primaria_correta()
    {
        $preferencia = new UsuarioPreferencia();
        $this->assertEquals('id_usuario', $preferencia->getKeyName());
    }

    public function test_modelo_de_preferencia_de_usuario_tem_propriedades_fillable_corretas()
    {
        $preferencia = new UsuarioPreferencia();
        $expected = [
            'id_usuario',
            'notif_email',
            'notif_popup',
            'tema',
            'tamanho_fonte',
            'backup_frequency',
        ];
        $this->assertEquals($expected, $preferencia->getFillable());
    }

    public function test_modelo_de_preferencia_de_usuario_tem_timestamps()
    {
        $preferencia = new UsuarioPreferencia();
        $this->assertTrue($preferencia->timestamps);
    }

    public function test_modelo_de_preferencia_de_usuario_converte_booleanos_corretamente()
    {
        $preferencia = new UsuarioPreferencia();
        $casts = $preferencia->getCasts();
        
        $this->assertArrayNotHasKey('notif_email', $casts);
        $this->assertArrayNotHasKey('notif_popup', $casts);
    }

    public function test_modelo_de_preferencia_de_usuario_tem_relacao_com_usuario()
    {
        $municipio = Municipio::create(['nome' => 'Municipio Teste', 'estado' => 'PR']);
        $escola = Escola::create([
            'nome' => 'Escola Teste',
            'id_municipio' => $municipio->id_municipio,
            'nivel_ensino' => 'colegio_estadual',
            'tipo' => 'urbana'
        ]);
        $user = Usuario::factory()->create(['id_escola' => $escola->id_escola]);
        $preferencia = UsuarioPreferencia::create([
            'id_usuario' => $user->id_usuario,
            'tema' => 'claro',
            'tamanho_fonte' => 'padrao',
        ]);

        $this->assertInstanceOf(Usuario::class, $preferencia->usuario);
        $this->assertEquals($user->id_usuario, $preferencia->usuario->id_usuario);
    }
}