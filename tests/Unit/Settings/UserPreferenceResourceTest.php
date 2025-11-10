<?php

namespace Tests\Unit\Settings;

use Tests\TestCase;
use App\Http\Resources\UserPreferenceResource;
use App\Models\Escola;
use App\Models\Municipio;
use App\Models\Usuario;
use App\Models\UsuarioPreferencia;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;

class UserPreferenceResourceTest extends TestCase
{
    use RefreshDatabase;

    public function test_recurso_de_preferencia_de_usuario_transforma_corretamente()
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
            'notif_email' => true,
            'notif_popup' => false,
            'tema' => 'escuro',
            'tamanho_fonte' => 'medio',
            'backup_frequency' => 'monthly'
        ]);

        $resource = new UserPreferenceResource($preferencia);
        $request = Request::create('/api/settings', 'GET');
        $resourceArray = $resource->toArray($request);

        $this->assertEquals($user->id_usuario, $resourceArray['userId']);
        $this->assertTrue($resourceArray['notifEmail']);
        $this->assertFalse($resourceArray['notifPopup']);
        $this->assertEquals('escuro', $resourceArray['tema']);
        $this->assertEquals('medio', $resourceArray['tamanhoFonte']);
        $this->assertEquals('monthly', $resourceArray['backupFrequency']);
    }
}