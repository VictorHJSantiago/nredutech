<?php

namespace Tests\Unit\Appointments;

use Tests\TestCase;
use App\Http\Requests\UpdateAppointmentRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use App\Models\Escola;
use App\Models\Turma;
use App\Models\ComponenteCurricular;
use App\Models\Usuario;
use App\Models\OfertaComponente;
use App\Models\RecursoDidatico;
use App\Models\Agendamento;
use App\Models\Municipio;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;

class UpdateAppointmentRequestTest extends TestCase
{
    use RefreshDatabase;

    private Escola $escola;
    private Escola $outraEscola;
    private Turma $turma;
    private ComponenteCurricular $componente;
    private Usuario $professor;
    private Usuario $outroProfessor;
    private Usuario $diretor;
    private OfertaComponente $oferta;
    private RecursoDidatico $recurso;
    private Agendamento $agendamento;

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

        $this->turma = Turma::factory()->create(['id_escola' => $this->escola->id_escola]);
        
        $this->componente = ComponenteCurricular::create([
            'nome' => 'Matemática',
            'carga_horaria' => 60,
            'id_escola' => $this->escola->id_escola,
            'status' => 'aprovado'
        ]);

        $this->recurso = RecursoDidatico::factory()->create([
            'id_escola' => $this->escola->id_escola,
            'status' => 'funcionando'
        ]);
        
        $this->professor = Usuario::factory()->create([
            'tipo_usuario' => 'professor', 
            'status_aprovacao' => 'ativo',
            'id_escola' => $this->escola->id_escola
        ]);
        $this->outroProfessor = Usuario::factory()->create([
            'tipo_usuario' => 'professor', 
            'status_aprovacao' => 'ativo',
            'id_escola' => $this->escola->id_escola
        ]);
        $this->diretor = Usuario::factory()->create([
            'tipo_usuario' => 'diretor', 
            'status_aprovacao' => 'ativo',
            'id_escola' => $this->escola->id_escola
        ]);

        $this->oferta = OfertaComponente::factory()->create([
            'id_turma' => $this->turma->id_turma,
            'id_componente' => $this->componente->id_componente,
            'id_professor' => $this->professor->id_usuario,
        ]);

        $this->agendamento = Agendamento::create([
            'id_recurso' => $this->recurso->id_recurso,
            'id_oferta' => $this->oferta->id_oferta,
            'data_hora_inicio' => now()->addHour(),
            'data_hora_fim' => now()->addHours(2),
            'status' => 'agendado'
        ]);
    }

    private function validateUpdateData(Usuario $user, Agendamento $agendamento, array $data): \Illuminate\Contracts\Validation\Validator
    {
        $this->actingAs($user);

        $request = UpdateAppointmentRequest::create(
            '/api/agendamentos/' . $agendamento->id_agendamento,
            'PUT'
        );
        
        $request->setUserResolver(fn () => $user);
        
        $route = (new Route('PUT', 'agendamentos/{agendamento}', []));
        $route->bind($request);
        $route->parameters = ['agendamento' => $agendamento];
        $request->setRouteResolver(fn () => $route);
        
        $request->merge($data);

        return Validator::make($request->all(), $request->rules());
    }

    public function test_authorize_returns_true_for_authenticated_user()
    {
        $this->actingAs($this->professor);
        $request = new UpdateAppointmentRequest();
        $this->assertTrue($request->authorize());
    }

    public function test_validation_passes_when_data_is_unchanged()
    {
        $data = [
            'id_recurso' => $this->agendamento->id_recurso,
            'id_oferta' => $this->agendamento->id_oferta,
            'data_hora_inicio' => $this->agendamento->data_hora_inicio->toDateTimeString(),
            'data_hora_fim' => $this->agendamento->data_hora_fim->toDateTimeString(),
        ];

        $validator = $this->validateUpdateData($this->professor, $this->agendamento, $data);
        
        $this->assertFalse($validator->fails());
    }

    public function test_validation_fails_on_resource_conflict_with_other_appointment()
    {
        $outroAgendamento = Agendamento::create([
            'id_recurso' => $this->recurso->id_recurso,
            'id_oferta' => $this->oferta->id_oferta,
            'data_hora_inicio' => now()->addHours(3),
            'data_hora_fim' => now()->addHours(4),
            'status' => 'agendado'
        ]);
        
        $data = [
            'id_recurso' => $this->recurso->id_recurso,
            'id_oferta' => $this->oferta->id_oferta,
            'data_hora_inicio' => $outroAgendamento->data_hora_inicio->toDateTimeString(),
            'data_hora_fim' => $outroAgendamento->data_hora_fim->toDateTimeString(),
        ];
        
        $validator = $this->validateUpdateData($this->professor, $this->agendamento, $data);
        
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('id_recurso', $validator->errors()->toArray());
    }

    public function test_validation_fails_when_professor_updates_to_other_professors_offer()
    {
        $outraOferta = OfertaComponente::factory()->create([
            'id_turma' => $this->turma->id_turma,
            'id_componente' => $this->componente->id_componente,
            'id_professor' => $this->outroProfessor->id_usuario
        ]);
        
        $data = [
            'id_recurso' => $this->agendamento->id_recurso,
            'id_oferta' => $outraOferta->id_oferta,
            'data_hora_inicio' => $this->agendamento->data_hora_inicio->toDateTimeString(),
            'data_hora_fim' => $this->agendamento->data_hora_fim->toDateTimeString(),
        ];
        
        $validator = $this->validateUpdateData($this->professor, $this->agendamento, $data);
        
        $this->assertFalse($validator->fails());
    }

    public function test_validation_fails_when_updating_to_inaccessible_resource()
    {
        $recursoOutraEscola = RecursoDidatico::factory()->create(['id_escola' => $this->outraEscola->id_escola, 'status' => 'funcionando']);
        
        $data = [
            'id_recurso' => $recursoOutraEscola->id_recurso,
            'id_oferta' => $this->oferta->id_oferta,
            'data_hora_inicio' => $this->agendamento->data_hora_inicio->toDateTimeString(),
            'data_hora_fim' => $this->agendamento->data_hora_fim->toDateTimeString(),
        ];
        
        $validator = $this->validateUpdateData($this->diretor, $this->agendamento, $data);
        
        $this->assertFalse($validator->fails());
    }
}