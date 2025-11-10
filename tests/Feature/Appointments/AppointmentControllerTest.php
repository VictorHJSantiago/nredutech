<?php

namespace Tests\Feature\Appointments;

use Tests\TestCase;
use App\Models\Usuario;
use App\Models\Escola;
use App\Models\Turma;
use App\Models\ComponenteCurricular;
use App\Models\OfertaComponente;
use App\Models\RecursoDidatico;
use App\Models\Agendamento;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use App\Notifications\ModelActionNotification;
use Carbon\Carbon;

class AppointmentControllerTest extends TestCase
{
    use RefreshDatabase;

    private Usuario $admin;
    private Usuario $diretorA;
    private Usuario $professorA;
    private Usuario $diretorB;
    private Escola $escolaA;
    private Escola $escolaB;
    private OfertaComponente $ofertaA;
    private OfertaComponente $ofertaB;
    private RecursoDidatico $recursoA;
    private RecursoDidatico $recursoB;
    private Agendamento $agendamentoA;
    private Agendamento $agendamentoB;

    protected function setUp(): void
    {
        parent::setUp();
        Notification::fake();

        $this->escolaA = Escola::factory()->create();
        $this->escolaB = Escola::factory()->create();

        $this->admin = Usuario::factory()->administrador()->create();
        $this->diretorA = Usuario::factory()->diretor()->create(['id_escola' => $this->escolaA->id_escola]);
        $this->professorA = Usuario::factory()->professor()->create(['id_escola' => $this->escolaA->id_escola]);
        $this->diretorB = Usuario::factory()->diretor()->create(['id_escola' => $this->escolaB->id_escola]);

        $this->ofertaA = OfertaComponente::factory()->create([
            'id_turma' => Turma::factory()->create(['id_escola' => $this->escolaA->id_escola])->id_turma,
            'id_componente_curricular' => ComponenteCurricular::factory()->create(['id_escola' => $this->escolaA->id_escola])->id_componente_curricular,
            'id_professor' => $this->professorA->id_usuario,
        ]);
        
        $this->ofertaB = OfertaComponente::factory()->create([
            'id_turma' => Turma::factory()->create(['id_escola' => $this->escolaB->id_escola])->id_turma,
            'id_componente_curricular' => ComponenteCurricular::factory()->create(['id_escola' => $this->escolaB->id_escola])->id_componente_curricular,
            'id_professor' => Usuario::factory()->professor()->create(['id_escola' => $this->escolaB->id_escola])->id_usuario,
        ]);
        
        $this->recursoA = RecursoDidatico::factory()->create(['id_escola' => $this->escolaA->id_escola, 'status' => 'funcionando']);
        $this->recursoB = RecursoDidatico::factory()->create(['id_escola' => $this->escolaB->id_escola, 'status' => 'funcionando']);

        $this->agendamentoA = Agendamento::factory()->create(['id_recurso' => $this->recursoA->id_recurso, 'id_oferta' => $this->ofertaA->id_oferta, 'id_usuario' => $this->professorA->id_usuario, 'data_hora_inicio' => now()->addDay()]);
        $this->agendamentoB = Agendamento::factory()->create(['id_recurso' => $this->recursoB->id_recurso, 'id_oferta' => $this->ofertaB->id_oferta, 'id_usuario' => $this->diretorB->id_usuario, 'data_hora_inicio' => now()->addDay()]);
    }

    public function test_admin_can_view_all_agendamentos_on_index()
    {
        $response = $this->actingAs($this->admin)->get(route('agendamentos.index'));
        $response->assertOk();
        $response->assertViewHas('meusAgendamentos', fn ($agendamentos) => $agendamentos->count() === 2);
    }

    public function test_diretor_can_view_own_school_agendamentos_on_index()
    {
        $response = $this->actingAs($this->diretorA)->get(route('agendamentos.index'));
        $response->assertOk();
        $response->assertViewHas('meusAgendamentos', fn ($agendamentos) => $agendamentos->count() === 1 && $agendamentos->first()->id_agendamento === $this->agendamentoA->id_agendamento);
    }

    public function test_professor_can_view_own_agendamentos_on_index()
    {
        $response = $this->actingAs($this->professorA)->get(route('agendamentos.index'));
        $response->assertOk();
        $response->assertViewHas('meusAgendamentos', fn ($agendamentos) => $agendamentos->count() === 1 && $agendamentos->first()->id_agendamento === $this->agendamentoA->id_agendamento);
    }

    public function test_admin_can_get_all_calendar_events()
    {
        $response = $this->actingAs($this->admin)->get(route('agendamentos.events', ['start' => now()->subMonth()->toIso8601String(), 'end' => now()->addMonth()->toIso8601String()]));
        $response->assertOk();
        $response->assertJsonCount(2, 'data');
    }

    public function test_diretor_can_get_own_school_calendar_events()
    {
        $response = $this->actingAs($this->diretorA)->get(route('agendamentos.events', ['start' => now()->subMonth()->toIso8601String(), 'end' => now()->addMonth()->toIso8601String()]));
        $response->assertOk();
        $response->assertJsonCount(1, 'data');
        $response->assertJsonFragment(['id_agendamento' => $this->agendamentoA->id_agendamento]);
    }

    public function test_admin_can_get_availability_for_all_schools()
    {
        $response = $this->actingAs($this->admin)->post(route('agendamentos.availability'), ['date' => now()->addDay()->format('Y-m-d')]);
        $response->assertOk();
        $response->assertJsonPath('agendados.total', 2);
        $response->assertJsonPath('disponiveis.total', 2);
    }

    public function test_diretor_can_get_availability_for_own_school()
    {
        RecursoDidatico::factory()->create(['id_escola' => null, 'status' => 'funcionando']);
        $response = $this->actingAs($this->diretorA)->post(route('agendamentos.availability'), ['date' => now()->addDay()->format('Y-m-d')]);
        $response->assertOk();
        $response->assertJsonPath('agendados.total', 1);
        $response->assertJsonPath('disponiveis.total', 2);
    }

    public function test_store_creates_agendamento_and_sends_notifications()
    {
        $data = [
            'id_recurso' => $this->recursoA->id_recurso,
            'id_oferta' => $this->ofertaA->id_oferta,
            'id_usuario' => $this->professorA->id_usuario,
            'data_hora_inicio' => now()->addHours(2)->toDateTimeString(),
            'data_hora_fim' => now()->addHours(3)->toDateTimeString(),
        ];

        $response = $this->actingAs($this->professorA)->post(route('agendamentos.store'), $data);
        
        $response->assertStatus(201);
        $this->assertDatabaseHas('agendamentos', ['id_recurso' => $this->recursoA->id_recurso, 'id_oferta' => $this->ofertaA->id_oferta]);
        Notification::assertSentTo($this->admin, ModelActionNotification::class);
        Notification::assertSentTo($this->diretorA, ModelActionNotification::class);
        Notification::assertSentTo($this->professorA, ModelActionNotification::class);
    }

    public function test_store_fails_for_time_conflict()
    {
        $data = [
            'id_recurso' => $this->agendamentoA->id_recurso,
            'id_oferta' => $this->agendamentoA->id_oferta,
            'id_usuario' => $this->agendamentoA->id_usuario,
            'data_hora_inicio' => $this->agendamentoA->data_hora_inicio,
            'data_hora_fim' => $this->agendamentoA->data_hora_fim,
        ];

        $response = $this->actingAs($this->professorA)->post(route('agendamentos.store'), $data);
        
        $response->assertStatus(422);
        $response->assertJsonValidationErrorFor('data_hora_inicio');
    }

    public function test_store_fails_for_disallowed_time_slot()
    {
        $data = [
            'id_recurso' => $this->recursoA->id_recurso,
            'id_oferta' => $this->ofertaA->id_oferta,
            'id_usuario' => $this->professorA->id_usuario,
            'data_hora_inicio' => now()->addDay()->setTime(23, 30, 0)->toDateTimeString(),
            'data_hora_fim' => now()->addDay()->setTime(23, 59, 0)->toDateTimeString(),
        ];

        $response = $this->actingAs($this->professorA)->post(route('agendamentos.store'), $data);
        
        $response->assertStatus(422);
        $response->assertJson(['message' => 'Não é permitido criar agendamentos entre 23:00 e 06:00.']);
    }

    public function test_store_fails_for_professor_policy()
    {
        $data = [
            'id_recurso' => $this->recursoA->id_recurso,
            'id_oferta' => $this->ofertaA->id_oferta,
            'id_usuario' => $this->professorA->id_usuario,
            'data_hora_inicio' => now()->addHours(2)->toDateTimeString(),
            'data_hora_fim' => now()->addHours(3)->toDateTimeString(),
        ];
        
        $outroProfessor = Usuario::factory()->professor()->create(['id_escola' => $this->escolaA->id_escola]);

        $response = $this->actingAs($outroProfessor)->post(route('agendamentos.store'), $data);
        $response->assertStatus(422);
        $response->assertJsonValidationErrorFor('id_oferta');
    }

    public function test_destroy_deletes_agendamento_and_sends_notifications()
    {
        $response = $this->actingAs($this->admin)->delete(route('agendamentos.destroy', $this->agendamentoA));
        
        $response->assertOk();
        $response->assertJson(['message' => 'Agendamento cancelado com sucesso.']);
        $this->assertDatabaseMissing('agendamentos', ['id_agendamento' => $this->agendamentoA->id_agendamento]);
        
        Notification::assertSentTo($this->admin, ModelActionNotification::class);
        Notification::assertSentTo($this->diretorA, ModelActionNotification::class);
        Notification::assertSentTo($this->professorA, ModelActionNotification::class);
    }

    public function test_destroy_fails_for_10_minute_rule()
    {
        $agendamento = Agendamento::factory()->create([
            'id_oferta' => $this->ofertaA->id_oferta,
            'data_hora_inicio' => now()->addMinutes(5),
        ]);
        
        $response = $this->actingAs($this->professorA)->delete(route('agendamentos.destroy', $agendamento));
        
        $response->assertStatus(422);
        $response->assertJson(['message' => 'Agendamentos não podem ser cancelados com menos de 10 minutos de antecedência do seu início.']);
    }

    public function test_destroy_fails_for_policy()
    {
        $outroProfessor = Usuario::factory()->professor()->create(['id_escola' => $this->escolaA->id_escola]);
        $response = $this->actingAs($outroProfessor)->delete(route('agendamentos.destroy', $this->agendamentoA));
        
        $response->assertForbidden();
    }
}