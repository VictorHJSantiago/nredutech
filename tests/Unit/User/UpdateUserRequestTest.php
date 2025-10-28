<?php

namespace Tests\Unit\User;
use Tests\TestCase;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Usuario;
use App\Models\Escola;
use App\Models\Municipio;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UpdateUserRequestTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $escola;
    protected $usuarioParaEditar;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = Usuario::factory()->create(['tipo_usuario' => 'administrador', 'id_escola' => null]);
        $municipio = Municipio::factory()->create();
        $this->escola = Escola::factory()->create(['id_municipio' => $municipio->id_municipio]);
        $this->usuarioParaEditar = Usuario::factory()->create(['tipo_usuario' => 'professor', 'id_escola' => $this->escola->id_escola]);
    }

    /**
     * @test
     */
    public function professor_ou_diretor_exige_id_escola()
    {
        $request = new UpdateUserRequest();
        $request->setRouteResolver(fn () => \Illuminate\Support\Facades\Route::get('usuarios/{usuario}', fn (Usuario $usuario) => $usuario)
            ->bind('usuario', $this->usuarioParaEditar));

        $dados = [
            'tipo_usuario' => 'professor',
            'id_escola' => null 
        ];

        $validator = Validator::make($dados, $request->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('id_escola', $validator->errors()->toArray());
        $this->assertStringContainsString('obrigatório para diretores e professores', $validator->errors()->first('id_escola'));
    }

    /**
     * @test
     */
    public function administrador_nao_pode_ter_id_escola()
    {
        $request = new UpdateUserRequest();
        $request->setRouteResolver(fn () => \Illuminate\Support\Facades\Route::get('usuarios/{usuario}', fn (Usuario $usuario) => $usuario)
            ->bind('usuario', $this->usuarioParaEditar)); 
            
        $dados = [
            'tipo_usuario' => 'administrador', 
            'id_escola' => $this->escola->id_escola
        ];

        $validator = Validator::make($dados, $request->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('id_escola', $validator->errors()->toArray());
        $this->assertStringContainsString('Administradores não podem ser associados', $validator->errors()->first('id_escola'));
    }

    /**
     * @test
     */
    public function nao_pode_atualizar_para_terceiro_diretor_ativo_na_escola()
    {
        Usuario::factory(2)->create(['tipo_usuario' => 'diretor', 'status_aprovacao' => 'ativo', 'id_escola' => $this->escola->id_escola]);
        $request = new UpdateUserRequest();
        $request->setRouteResolver(fn () => \Illuminate\Support\Facades\Route::get('usuarios/{usuario}', fn (Usuario $usuario) => $usuario)
            ->bind('usuario', $this->usuarioParaEditar));

        $dados = [
            'tipo_usuario' => 'diretor',
            'status_aprovacao' => 'ativo',
            'id_escola' => $this->escola->id_escola
        ];

        $validator = Validator::make($dados, $request->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('id_escola', $validator->errors()->toArray());
        $this->assertStringContainsString('limite de 2 (dois) diretores ativos', $validator->errors()->first('id_escola'));
    }

    /**
     * @test
     */
    public function validacao_passa_com_apenas_um_campo_sendo_atualizado()
    {
        $request = new UpdateUserRequest();
        $request->setRouteResolver(fn () => \Illuminate\Support\Facades\Route::get('usuarios/{usuario}', fn (Usuario $usuario) => $usuario)
            ->bind('usuario', $this->usuarioParaEditar));

        $dados = [
            'telefone' => '(99) 99999-9999' 
        ];

        Validator::extend('celular_com_ddd', fn() => true); 

        $validator = Validator::make($dados, $request->rules());
        
        // 'nome_completo' => 'sometimes|required'
        // 'telefone' => 'sometimes|required|celular_com_ddd'        
        $dadosSemTelefone = [
            'nome_completo' => 'Novo Nome Teste'
        ];
        $validatorSemTelefone = Validator::make($dadosSemTelefone, $request->rules());
        $this->assertFalse($validatorSemTelefone->fails()); 

        $dadosComTelefone = [
            'telefone' => '(99) 99999-9999'
        ];
        Validator::extend('celular_com_ddd', fn() => true); 
        $validatorComTelefone = Validator::make($dadosComTelefone, $request->rules());
        $this->assertFalse($validatorComTelefone->fails()); 
    }
}