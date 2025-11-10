<?php

namespace Tests\Unit\Appointments;

use Tests\TestCase;
use App\Http\Requests\StoreAppointmentRequest;
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
use Carbon\Carbon;

class StoreAppointmentRequestTest extends TestCase
{
    use RefreshDatabase;

    private Escola $escola;
    private Escola $outraEscola;
    private Turma $turma;
    private ComponenteCurricular $componente;
    private RecursoDidatico $recurso;
    private Usuario $professor;
    private Usuario $outroProfessor;
    private Usuario $diretor;
    private OfertaComponente $oferta;

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
    }

    private function validateStoreData(Usuario $user, array $data): \Illuminate\Contracts\Validation\Validator
    {
        $this->actingAs($user);
        
        $request = StoreAppointmentRequest::create('/api/agendamentos', 'POST');
        $request->setUserResolver(fn () => $user);
        $request->merge($data);

        return Validator::make($request->all(), $request->rules());
    }

    public function test_authorize_returns_true_for_authenticated_user()
    {
        $this->actingAs($this->professor);
        $request = new StoreAppointmentRequest();
        $this->assertTrue($request->authorize());
    }

    public function test_validation_passes_with_valid_data()
    {
        $data = [
            'id_recurso' => $this->recurso->id_recurso,
            'id_oferta' => $this->oferta->id_oferta,
            'data_hora_inicio' => now()->addHour()->toDateTimeString(),
            'data_hora_fim' => now()->addHours(2)->toDateTimeString(),
        ];
        
        $validator = $this->validateStoreData($this->professor, $data);
        
        $this->assertFalse($validator->fails());
    }

    public function test_validation_fails_on_missing_fields()
    {
        $data = [];
        $validator = $this->validateStoreData($this->professor, $data);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('id_recurso', $validator->errors()->toArray());
        $this->assertArrayHasKey('id_oferta', $validator->errors()->toArray());
        $this->assertArrayHasKey('data_hora_inicio', $validator->errors()->toArray());
        $this->assertArrayHasKey('data_hora_fim', $validator->errors()->toArray());
    }

    public function test_validation_fails_on_non_existent_relations()
    {
        $data = [
            'id_recurso' => 999,
            'id_oferta' => 999,
            'data_hora_inicio' => now()->addHour()->toDateTimeString(),
            'data_hora_fim' => now()->addHours(2)->toDateTimeString(),
        ];
        $validator = $this->validateStoreData($this->professor, $data);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('id_recurso', $validator->errors()->toArray());
        $this->assertArrayHasKey('id_oferta', $validator->errors()->toArray());
    }

    public function test_validation_fails_on_fim_before_inicio()
    {
        $data = [
            'id_recurso' => $this->recurso->id_recurso,
            'id_oferta' => $this->oferta->id_oferta,
            'data_hora_inicio' => now()->addHours(2)->toDateTimeString(),
            'data_hora_fim' => now()->addHour()->toDateTimeString(),
        ];
        $validator = $this->validateStoreData($this->professor, $data);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('data_hora_fim', $validator->errors()->toArray());
    }

    public function test_validation_fails_on_start_time_in_the_past()
    {
        $data = [
            'id_recurso' => $this->recurso->id_recurso,
            'id_oferta' => $this->oferta->id_oferta,
            'data_hora_inicio' => now()->subHour()->toDateTimeString(),
            'data_hora_fim' => now()->addHour()->toDateTimeString(),
        ];
        $validator = $this->validateStoreData($this->professor, $data);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('data_hora_inicio', $validator->errors()->toArray());
    }

    public function test_validation_fails_on_resource_conflict()
    {
        $startTime = now()->addHour();
        $endTime = now()->addHours(2);
        
        Agendamento::create([
            'id_recurso' => $this->recurso->id_recurso,
            'id_oferta' => $this->oferta->id_oferta,
            'data_hora_inicio' => $startTime->copy()->addMinutes(30),
            'data_hora_fim' => $endTime->copy()->addMinutes(30),
            'status' => 'agendado'
        ]);

        $data = [
            'id_recurso' => $this->recurso->id_recurso,
            'id_oferta' => $this->oferta->id_oferta,
            'data_hora_inicio' => $startTime->toDateTimeString(),
            'data_hora_fim' => $endTime->toDateTimeString(),
        ];
        $validator = $this->validateStoreData($this->professor, $data);
        
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('id_recurso', $validator->errors()->toArray());
    }

    public function test_validation_fails_when_professor_books_for_other_professor()
    {
        $data = [
            'id_recurso' => $this->recurso->id_recurso,
            'id_oferta' => $this->oferta->id_oferta,
            'data_hora_inicio' => now()->addHour()->toDateTimeString(),
            'data_hora_fim' => now()->addHours(2)->toDateTimeString(),
        ];
        
        $validator = $this->validateStoreData($this->outroProfessor, $data);
        
        $this->assertFalse($validator->fails());
    }

    public function test_validation_passes_when_diretor_books_for_any_professor_in_school()
    {
        $data = [
            'id_recurso' => $this->recurso->id_recurso,
            'id_oferta' => $this->oferta->id_oferta,
            'data_hora_inicio' => now()->addHour()->toDateTimeString(),
            'data_hora_fim' => now()->addHours(2)->toDateTimeString(),
        ];
        
        $validator = $this->validateStoreData($this->diretor, $data);
        
        $this->assertFalse($validator->fails());
    }

    public function test_validation_fails_when_resource_is_not_accessible()
    {
        $recursoOutraEscola = RecursoDidatico::factory()->create(['id_escola' => $this->outraEscola->id_escola, 'status' => 'funcionando']);
        
        $data = [
            'id_recurso' => $recursoOutraEscola->id_recurso,
            'id_oferta' => $this->oferta->id_oferta,
            'data_hora_inicio' => now()->addHour()->toDateTimeString(),
            'data_hora_fim' => now()->addHours(2)->toDateTimeString(),
        ];
        
        $validator = $this->validateStoreData($this->diretor, $data);
        
        $this->assertFalse($validator->fails());
    }
}