<?php

namespace Tests\Unit\CourseOffering;

use Tests\TestCase;
use App\Models\OfertaComponente;
use App\Models\Turma;
use App\Models\ComponenteCurricular;
use App\Models\Usuario;
use App\Http\Resources\CourseOfferingResource;
use App\Http\Resources\SchoolClassResource;
use App\Http\Resources\CurricularComponentResource;
use App\Http\Resources\UserResource;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use App\Models\Escola;
use App\Models\Municipio;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CourseOfferingResourceTest extends TestCase
{
    use RefreshDatabase;

    protected $escola;
    protected $componente;
    protected $professor;

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

        $this->professor = Usuario::factory()->create([
            'id_escola' => $this->escola->id_escola,
            'tipo_usuario' => 'professor',
            'status_aprovacao' => 'ativo'
        ]);

        $this->componente = ComponenteCurricular::create([
            'nome' => 'Matemática',
            'status' => 'aprovado',
            'carga_horaria' => 60,
            'id_escola' => $this->escola->id_escola,
            'id_usuario_criador' => $criador->id_usuario,
        ]);
        
        Turma::factory()->create(['id_escola' => $this->escola->id_escola]);
    }

    public function test_course_offering_resource_transforms_correctly()
    {
        $turma = Turma::factory()->create();
        
        $oferta = OfertaComponente::factory()->create([
            'id_turma' => $turma->id_turma,
            'id_componente' => $this->componente->id_componente,
            'id_professor' => $this->professor->id_usuario,
        ]);
        
        $oferta->load('turma', 'componenteCurricular', 'professor', 'agendamentos');

        $resource = new CourseOfferingResource($oferta);
        $request = new Request();
        $resourceArray = $resource->toArray($request);

        $expectedArray = [
            'id' => $oferta->id_oferta,
            'turma' => new SchoolClassResource($oferta->turma),
            'componente' => new CurricularComponentResource($oferta->componenteCurricular),
            'professor' => new UserResource($oferta->professor),
            'agendamentos' => new AnonymousResourceCollection($oferta->agendamentos, \App\Http\Resources\AppointmentResource::class),
        ];
        
        $this->assertEquals(json_decode(json_encode($expectedArray), true), json_decode(json_encode($resourceArray), true));
    }
}