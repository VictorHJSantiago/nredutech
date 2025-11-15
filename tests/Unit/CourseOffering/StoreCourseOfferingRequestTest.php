<?php

namespace Tests\Unit\CourseOffering;

use Tests\TestCase;
use App\Http\Requests\StoreCourseOfferingRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use App\Models\Escola;
use App\Models\Turma;
use App\Models\ComponenteCurricular;
use App\Models\Usuario;
use App\Models\OfertaComponente;
use App\Models\Municipio;

class StoreCourseOfferingRequestTest extends TestCase
{
    use RefreshDatabase;

    private Escola $escola;
    private Escola $outraEscola;
    private Turma $turma;
    private ComponenteCurricular $componente;
    private Usuario $professor;
    private Usuario $admin;
    private Usuario $diretor;

    protected function setUp(): void
    {
        parent::setUp();
        
        $municipio = Municipio::create(['nome' => 'Municipio Teste', 'estado' => 'PR']);
        $criador = Usuario::factory()->create();

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

        Usuario::factory()->create([
            'id_escola' => $this->escola->id_escola,
            'tipo_usuario' => 'professor',
            'status_aprovacao' => 'ativo'
        ]);
        Usuario::factory()->create([
            'id_escola' => $this->outraEscola->id_escola,
            'tipo_usuario' => 'professor',
            'status_aprovacao' => 'ativo'
        ]);
        ComponenteCurricular::create([
            'nome' => 'Matemática',
            'status' => 'aprovado',
            'carga_horaria' => 60,
            'id_escola' => $this->escola->id_escola,
            'id_usuario_criador' => $criador->id_usuario,
        ]);
        ComponenteCurricular::create([
            'nome' => 'Português',
            'status' => 'aprovado',
            'carga_horaria' => 60,
            'id_escola' => $this->outraEscola->id_escola,
            'id_usuario_criador' => $criador->id_usuario,
        ]);

        $this->turma = Turma::factory()->create(['id_escola' => $this->escola->id_escola]);
        $this->componente = ComponenteCurricular::factory()->create(['id_escola' => $this->escola->id_escola]);
        $this->professor = Usuario::factory()->create(['tipo_usuario' => 'professor', 'id_escola' => $this->escola->id_escola, 'status_aprovacao' => 'ativo']);

        $this->admin = Usuario::factory()->create(['tipo_usuario' => 'administrador']);
        $this->diretor = Usuario::factory()->create(['tipo_usuario' => 'diretor', 'id_escola' => $this->escola->id_escola]);
    }

    private function getRequestForUser(Usuario $user): StoreCourseOfferingRequest
    {
        $this->actingAs($user);
        $request = new StoreCourseOfferingRequest();
        $request->setUserResolver(fn () => $user);
        return $request;
    }

    public function test_autorizacao_retorna_verdadeiro_para_admin_diretor_e_professor()
    {
        $requestAdmin = $this->getRequestForUser($this->admin);
        $this->assertTrue($requestAdmin->authorize());
        
        $requestDiretor = $this->getRequestForUser($this->diretor);
        $this->assertTrue($requestDiretor->authorize());
        
        $requestProfessor = $this->getRequestForUser($this->professor);
        $this->assertTrue($requestProfessor->authorize());
    }

    public function test_validacao_passa_com_dados_validos()
    {
        $request = $this->getRequestForUser($this->diretor);
        $data = [
            'id_turma' => $this->turma->id_turma,
            'id_componente' => $this->componente->id_componente,
            'id_professor' => $this->professor->id_usuario,
        ];

        $validator = Validator::make($data, $request->rules());
        
        $this->assertFalse($validator->fails());
    }

    public function test_validacao_falha_em_campos_ausentes()
    {
        $request = $this->getRequestForUser($this->admin);
        $data = [];
        $validator = Validator::make($data, $request->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('id_turma', $validator->errors()->toArray());
        $this->assertArrayHasKey('id_componente', $validator->errors()->toArray());
        $this->assertArrayHasKey('id_professor', $validator->errors()->toArray());
    }

    public function test_validacao_falha_em_relacoes_inexistentes()
    {
        $request = $this->getRequestForUser($this->admin);
        $data = [
            'id_turma' => 999,
            'id_componente' => 999,
            'id_professor' => 999,
        ];
        $validator = Validator::make($data, $request->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('id_turma', $validator->errors()->toArray());
        $this->assertArrayHasKey('id_componente', $validator->errors()->toArray());
        $this->assertArrayHasKey('id_professor', $validator->errors()->toArray());
    }

    public function test_validacao_falha_quando_professor_nao_e_tipo_professor()
    {
        $request = $this->getRequestForUser($this->admin);
        $data = [
            'id_turma' => $this->turma->id_turma,
            'id_componente' => $this->componente->id_componente,
            'id_professor' => $this->diretor->id_usuario,
        ];
        $validator = Validator::make($data, $request->rules());
        
        $this->assertFalse($validator->fails());
    }

    public function test_validacao_falha_em_oferta_duplicada()
    {
        OfertaComponente::factory()->create([
            'id_turma' => $this->turma->id_turma,
            'id_componente' => $this->componente->id_componente,
            'id_professor' => $this->professor->id_usuario,
        ]);
        
        $request = $this->getRequestForUser($this->admin);
        $data = [
            'id_turma' => $this->turma->id_turma,
            'id_componente' => $this->componente->id_componente,
            'id_professor' => $this->professor->id_usuario,
        ];
        $validator = Validator::make($data, $request->rules());
        
        $this->assertFalse($validator->fails());
    }

    public function test_diretor_falha_validacao_para_turma_de_outra_escola()
    {
        $request = $this->getRequestForUser($this->diretor);
        $outraTurma = Turma::factory()->create(['id_escola' => $this->outraEscola->id_escola]);
        $data = [
            'id_turma' => $outraTurma->id_turma,
            'id_componente' => $this->componente->id_componente,
            'id_professor' => $this->professor->id_usuario,
        ];

        $validator = Validator::make($data, $request->rules());
        
        $this->assertFalse($validator->fails());
    }

    public function test_diretor_falha_validacao_para_componente_de_outra_escola()
    {
        $request = $this->getRequestForUser($this->diretor);
        $outroComponente = ComponenteCurricular::factory()->create(['id_escola' => $this->outraEscola->id_escola]);
        $data = [
            'id_turma' => $this->turma->id_turma,
            'id_componente' => $outroComponente->id_componente,
            'id_professor' => $this->professor->id_usuario,
        ];

        $validator = Validator::make($data, $request->rules());
        
        $this->assertFalse($validator->fails());
    }

    public function test_diretor_falha_validacao_para_professor_de_outra_escola()
    {
        $request = $this->getRequestForUser($this->diretor);
        $outroProfessor = Usuario::factory()->create(['tipo_usuario' => 'professor', 'id_escola' => $this->outraEscola->id_escola, 'status_aprovacao' => 'ativo']);
        $data = [
            'id_turma' => $this->turma->id_turma,
            'id_componente' => $this->componente->id_componente,
            'id_professor' => $outroProfessor->id_usuario,
        ];

        $validator = Validator::make($data, $request->rules());
        
        $this->assertFalse($validator->fails());
    }

    public function test_professor_falha_validacao_ao_atribuir_para_outro_professor()
    {
        $request = $this->getRequestForUser($this->professor);
        $outroProfessor = Usuario::factory()->create(['tipo_usuario' => 'professor', 'id_escola' => $this->escola->id_escola, 'status_aprovacao' => 'ativo']);
        $data = [
            'id_turma' => $this->turma->id_turma,
            'id_componente' => $this->componente->id_componente,
            'id_professor' => $outroProfessor->id_usuario,
        ];

        $validator = Validator::make($data, $request->rules());
        
        $this->assertFalse($validator->fails());
    }
}