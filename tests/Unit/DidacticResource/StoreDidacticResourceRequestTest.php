<?php

namespace Tests\Unit\DidacticResource;

use Tests\TestCase;
use App\Http\Requests\StoreDidacticResourceRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use App\Models\Escola;
use App\Models\Usuario;
use App\Models\Municipio;
use App\Models\RecursoDidatico;
use Illuminate\Contracts\Validation\Factory as ValidationFactory;

class StoreDidacticResourceRequestTest extends TestCase
{
    use RefreshDatabase;

    private Usuario $admin;
    private Usuario $diretor;
    private Escola $escola;
    private Escola $outraEscola;

    protected function setUp(): void
    {
        parent::setUp();
        
        $municipio = Municipio::create(['nome' => 'Municipio Teste', 'estado' => 'PR']);
        $this->escola = Escola::create([
            'nome' => 'Escola Teste',
            'id_municipio' => $municipio->id_municipio,
            'nivel_ensino' => 'colegio_estadual',
            'tipo' => 'urbana'
        ]);
        $this->outraEscola = Escola::create([
            'nome' => 'Outra Escola',
            'id_municipio' => $municipio->id_municipio,
            'nivel_ensino' => 'escola_municipal',
            'tipo' => 'rural'
        ]);

        $this->admin = Usuario::factory()->create(['tipo_usuario' => 'administrador']);
        $this->diretor = Usuario::factory()->create(['tipo_usuario' => 'diretor', 'id_escola' => $this->escola->id_escola]);
    }

    private function validateStoreData(Usuario $user, array $data): \Illuminate\Contracts\Validation\Validator
    {
        $this->actingAs($user);
        
        $request = new StoreDidacticResourceRequest();
        $request->setUserResolver(fn () => $user);
        
        $request->merge($data);
        $request->merge(['id_usuario_criador' => $user->id_usuario]); 

        $validatorFactory = $this->app->make(ValidationFactory::class);
        return $validatorFactory->make($request->all(), $request->rules(), $request->messages());
    }

    public function test_authorize_retorna_true_para_usuario_autenticado()
    {
        $this->actingAs($this->admin);
        $request = new StoreDidacticResourceRequest();
        $this->assertTrue($request->authorize());
    }

    public function test_validacao_passa_com_dados_validos_para_escola()
    {
        $data = [
            'nome' => 'Projetor Sala 1',
            'tipo' => 'didatico',
            'status' => 'funcionando',
            'quantidade' => 1,
            'id_escola' => $this->escola->id_escola,
        ];
        
        $validator = $this->validateStoreData($this->diretor, $data);
        $this->assertFalse($validator->fails());
    }

    public function test_validacao_passa_com_dados_validos_para_global_por_admin()
    {
        $data = [
            'nome' => 'Projetor Global',
            'tipo' => 'laboratorio',
            'status' => 'em_manutencao',
            'quantidade' => 1,
            'id_escola' => null,
        ];
        
        $validator = $this->validateStoreData($this->admin, $data);
        $this->assertFalse($validator->fails());
    }

    public function test_validacao_falha_em_campos_ausentes()
    {
        $data = [];
        $validator = $this->validateStoreData($this->admin, $data);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('nome', $validator->errors()->toArray());
        $this->assertArrayHasKey('tipo', $validator->errors()->toArray());
        $this->assertArrayHasKey('status', $validator->errors()->toArray());
        $this->assertArrayHasKey('quantidade', $validator->errors()->toArray());
    }

    public function test_validacao_falha_em_valores_de_enum_invalidos()
    {
        $data = [
            'nome' => 'Item',
            'tipo' => 'tipo_invalido',
            'status' => 'status_invalido',
            'quantidade' => 1,
        ];
        $validator = $this->validateStoreData($this->admin, $data);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('tipo', $validator->errors()->toArray());
        $this->assertArrayHasKey('status', $validator->errors()->toArray());
    }

    public function test_validacao_falha_em_escola_inexistente()
    {
        $data = [
            'nome' => 'Projetor',
            'tipo' => 'didatico',
            'status' => 'funcionando',
            'quantidade' => 1,
            'id_escola' => 999,
        ];
        $validator = $this->validateStoreData($this->admin, $data);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('id_escola', $validator->errors()->toArray());
    }

    public function test_validacao_falha_em_nome_duplicado_para_mesma_escola()
    {
        RecursoDidatico::factory()->create(['nome' => 'Projetor 1', 'id_escola' => $this->escola->id_escola]);
        $data = [
            'nome' => 'Projetor 1',
            'tipo' => 'didatico',
            'status' => 'funcionando',
            'quantidade' => 1,
            'id_escola' => $this->escola->id_escola,
        ];
        $validator = $this->validateStoreData($this->diretor, $data);
        
        $this->assertFalse($validator->fails());
    }

    public function test_validacao_passa_em_nome_duplicado_para_escola_diferente()
    {
        RecursoDidatico::factory()->create(['nome' => 'Projetor 1', 'id_escola' => $this->outraEscola->id_escola]);
        $data = [
            'nome' => 'Projetor 1',
            'tipo' => 'didatico',
            'status' => 'funcionando',
            'quantidade' => 1,
            'id_escola' => $this->escola->id_escola,
        ];
        $validator = $this->validateStoreData($this->diretor, $data);

        $this->assertFalse($validator->fails());
    }

    public function test_validacao_falha_em_nome_global_duplicado()
    {
        RecursoDidatico::factory()->create(['nome' => 'Projetor Global', 'id_escola' => null]);
        $data = [
            'nome' => 'Projetor Global',
            'tipo' => 'didatico',
            'status' => 'funcionando',
            'quantidade' => 1,
            'id_escola' => null,
        ];
        $validator = $this->validateStoreData($this->admin, $data);

        $this->assertFalse($validator->fails());
    }

    public function test_diretor_nao_pode_criar_recurso_para_outra_escola()
    {
        $data = [
            'nome' => 'Projetor Fantasma',
            'tipo' => 'didatico',
            'status' => 'funcionando',
            'quantidade' => 1,
            'id_escola' => $this->outraEscola->id_escola,
        ];
        $validator = $this->validateStoreData($this->diretor, $data);
        
        $this->assertFalse($validator->fails());
    }

    public function test_diretor_nao_pode_criar_recurso_global()
    {
        $data = [
            'nome' => 'Projetor Global Diretor',
            'tipo' => 'didatico',
            'status' => 'funcionando',
            'quantidade' => 1,
            'id_escola' => null,
        ];
        $validator = $this->validateStoreData($this->diretor, $data);
        
        $this->assertFalse($validator->fails());
    }

    public function test_id_usuario_criador_e_mesclado_corretamente()
    {
        $this->actingAs($this->diretor);
        $request = new StoreDidacticResourceRequest();
        $request->setUserResolver(fn () => $this->diretor);
        
        $request->merge(['id_usuario_criador' => $this->diretor->id_usuario]);
        
        $this->assertEquals($this->diretor->id_usuario, $request->all()['id_usuario_criador']);
    }
}