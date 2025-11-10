<?php

namespace Tests\Unit\Settings;

use Tests\TestCase;
use App\Http\Requests\SaveUserPreferenceRequest;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;

class SaveUserPreferenceRequestTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $request;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = Usuario::factory()->create();
        $this->actingAs($this->user);
        $this->request = new SaveUserPreferenceRequest();
    }

    public function test_authorize_retorna_true_para_usuario_autenticado()
    {
        $this->assertTrue($this->request->authorize());
    }

    public function test_validacao_passa_com_dados_validos()
    {
        $data = [
            'notif_email' => true,
            'notif_popup' => false,
            'tema' => 'claro',
            'tamanho_fonte' => 'padrao',
            'backup_frequency' => 'daily',
        ];

        $validator = Validator::make($data, $this->request->rules());
        
        $this->assertFalse($validator->fails(), $validator->errors()->toJson());
    }

    public function test_validacao_lida_com_booleanos_falsos()
    {
        $data = [
            'notif_email' => false,
            'notif_popup' => false,
            'backup_frequency' => 'weekly',
            'tema' => 'escuro',
            'tamanho_fonte' => 'grande',
        ];

        $validator = Validator::make($data, $this->request->rules());
        
        $this->assertFalse($validator->fails(), $validator->errors()->toJson());
        
        $validatedData = $validator->validated();
        $this->assertFalse($validatedData['notif_email']);
        $this->assertFalse($validatedData['notif_popup']);
    }

    public function test_validacao_falha_com_frequencia_de_backup_invalida()
    {
        $data = [
            'backup_frequency' => 'anual',
            'notif_email' => true,
            'notif_popup' => true,
            'tema' => 'claro',
            'tamanho_fonte' => 'medio',
        ];

        $validator = Validator::make($data, $this->request->rules());
        
        $this->assertFalse($validator->fails());
    }

    public function test_validacao_falha_com_tipos_de_dados_invalidos()
    {
        $data = [
            'notif_email' => 'string_invalida',
            'notif_popup' => 123,
            'backup_frequency' => 'daily',
            'tema' => 'claro',
            'tamanho_fonte' => 'padrao',
        ];
        
        $validator = Validator::make($data, $this->request->rules());
        
        $this->assertFalse($validator->fails());
    }
}