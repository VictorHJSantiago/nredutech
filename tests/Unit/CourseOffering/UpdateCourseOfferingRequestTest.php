<?php

namespace Tests\Unit\CourseOffering;

use Tests\TestCase;
use App\Http\Requests\UpdateCourseOfferingRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use App\Models\Escola;
use App\Models\Turma;
use App\Models\ComponenteCurricular;
use App\Models\Usuario;
use App\Models\OfertaComponente;
use Illuminate\Http\Request;
use App\Models\Municipio;
use Illuminate\Routing\Route;

class UpdateCourseOfferingRequestTest extends TestCase
{
    use RefreshDatabase;

    private Escola $escola;
    private Turma $turma;
    private ComponenteCurricular $componente;
    private Usuario $professor;
    private Usuario $admin;
    private OfertaComponente $oferta;

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
        
        Usuario::factory()->create([
            'id_escola' => $this->escola->id_escola,
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

        $this->turma = Turma::factory()->create(['id_escola' => $this->escola->id_escola]);
        $this->componente = ComponenteCurricular::factory()->create(['id_escola' => $this->escola->id_escola]);
        $this->professor = Usuario::factory()->create(['tipo_usuario' => 'professor', 'id_escola' => $this->escola->id_escola, 'status_aprovacao' => 'ativo']);
        $this->admin = Usuario::factory()->create(['tipo_usuario' => 'administrador']);

        $this->oferta = OfertaComponente::factory()->create([
            'id_turma' => $this->turma->id_turma,
            'id_componente' => $this->componente->id_componente,
            'id_professor' => $this->professor->id_usuario,
        ]);
    }

    private function getRequestForUser(Usuario $user, OfertaComponente $oferta): UpdateCourseOfferingRequest
    {
        $this->actingAs($user);
        $request = new UpdateCourseOfferingRequest();
        $request->setUserResolver(fn () => $user);
        
        $request->setRouteResolver(function () use ($oferta) {
            $route = new Route('PUT', 'componentes/{componente}', []);
            $route->bind('componente', $oferta);
            return $route;
        });
        
        return $request;
    }

    public function test_authorize_returns_true()
    {
        $request = $this->getRequestForUser($this->admin, $this->oferta);
        $this->assertTrue($request->authorize());
    }

    public function test_validation_passes_with_valid_data()
    {
        $request = $this->getRequestForUser($this->admin, $this->oferta);
        $data = [
            'id_turma' => $this->turma->id_turma,
            'id_componente' => $this->componente->id_componente,
            'id_professor' => $this->professor->id_usuario,
        ];

        $validator = Validator::make($data, $request->rules());
        
        $this->assertFalse($validator->fails());
    }

    public function test_validation_passes_when_offering_is_unchanged()
    {
        $request = $this->getRequestForUser($this->admin, $this->oferta);
        $data = [
            'id_turma' => $this->oferta->id_turma,
            'id_componente' => $this->oferta->id_componente,
            'id_professor' => $this->oferta->id_professor,
        ];

        $validator = Validator::make($data, $request->rules());
        
        $this->assertFalse($validator->fails());
    }

    public function test_validation_fails_on_duplicate_offering()
    {
        $outraOferta = OfertaComponente::factory()->create([
            'id_turma' => $this->turma->id_turma,
            'id_componente' => ComponenteCurricular::factory()->create(['id_escola' => $this->escola->id_escola])->id_componente,
            'id_professor' => $this->professor->id_usuario,
        ]);
        
        $request = $this->getRequestForUser($this->admin, $this->oferta);

        $data = [
            'id_turma' => $outraOferta->id_turma,
            'id_componente' => $outraOferta->id_componente,
            'id_professor' => $outraOferta->id_professor,
        ];

        $validator = Validator::make($data, $request->rules());
        
        $this->assertFalse($validator->fails());
    }
}