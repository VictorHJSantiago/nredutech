<?php

namespace Tests\Feature\Appointments;

use Tests\TestCase;
use App\Models\Usuario;
use App\Models\Escola;
use App\Models\OfertaComponente;
use App\Models\Agendamento;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AppointmentRoutesTest extends TestCase
{
    use RefreshDatabase;

    private Usuario $admin;
    private Usuario $diretor;
    private Usuario $professor;
    private Usuario $outroProfessor;
    private Agendamento $agendamentoProfessor;
    private Agendamento $agendamentoOutroProfessor;

    protected function setUp(): void
    {
        parent::setUp();

        $escola = Escola::factory()->create();
        $this->admin = Usuario::factory()->administrador()->create();
        $this->diretor = Usuario::factory()->diretor()->create(['id_escola' => $escola->id_escola]);
        $this->professor = Usuario::factory()->professor()->create(['id_escola' => $escola->id_escola]);
        $this->outroProfessor = Usuario::factory()->professor()->create(['id_escola' => $escola->id_escola]);

        $ofertaProfessor = OfertaComponente::factory()->create(['id_professor' => $this->professor->id_usuario]);
        $ofertaOutroProfessor = OfertaComponente::factory()->create(['id_professor' => $this->outroProfessor->id_usuario]);

        $this->agendamentoProfessor = Agendamento::factory()->create(['id_oferta' => $ofertaProfessor->id_oferta, 'id_usuario' => $this->professor->id_usuario]);
        $this->agendamentoOutroProfessor = Agendamento::factory()->create(['id_oferta' => $ofertaOutroProfessor->id_oferta, 'id_usuario' => $this->outroProfessor->id_usuario]);
    }

    public function test_guest_is_redirected_from_all_appointment_routes()
    {
        $this->get(route('agendamentos.index'))->assertRedirect(route('login'));
        $this->get(route('agendamentos.events'))->assertRedirect(route('login'));
        $this->post(route('agendamentos.availability'))->assertRedirect(route('login'));
        $this->post(route('agendamentos.store'))->assertRedirect(route('login'));
        $this->delete(route('agendamentos.destroy', $this->agendamentoProfessor))->assertRedirect(route('login'));
    }

    public function test_admin_can_access_all_appointment_routes()
    {
        $this->actingAs($this->admin);

        $this->get(route('agendamentos.index'))->assertOk();
        $this->get(route('agendamentos.events', ['start' => '2025-01-01', 'end' => '2025-01-31']))->assertOk();
        $this->post(route('agendamentos.availability'), ['date' => '2025-01-01'])->assertOk();
        
        $storeData = Agendamento::factory()->make()->toArray();
        $this->post(route('agendamentos.store'), $storeData)->assertStatus(201);
        
        $this->delete(route('agendamentos.destroy', $this->agendamentoProfessor))->assertOk();
    }

    public function test_diretor_can_access_all_appointment_routes()
    {
        $this->actingAs($this->diretor);

        $this->get(route('agendamentos.index'))->assertOk();
        $this->get(route('agendamentos.events', ['start' => '2025-01-01', 'end' => '2025-01-31']))->assertOk();
        $this->post(route('agendamentos.availability'), ['date' => '2025-01-01'])->assertOk();
        
        $storeData = Agendamento::factory()->make([
            'id_oferta' => $this->agendamentoProfessor->id_oferta,
            'id_recurso' => $this->agendamentoProfessor->id_recurso,
            'id_usuario' => $this->diretor->id_usuario,
        ])->toArray();
        $this->post(route('agendamentos.store'), $storeData)->assertStatus(201);
        
        $this->delete(route('agendamentos.destroy', $this->agendamentoProfessor))->assertOk();
    }

    public function test_professor_can_access_all_appointment_routes()
    {
        $this->actingAs($this->professor);

        $this->get(route('agendamentos.index'))->assertOk();
        $this->get(route('agendamentos.events', ['start' => '2025-01-01', 'end' => '2025-01-31']))->assertOk();
        $this->post(route('agendamentos.availability'), ['date' => '2025-01-01'])->assertOk();
        
        $storeData = Agendamento::factory()->make([
            'id_oferta' => $this->agendamentoProfessor->id_oferta,
            'id_recurso' => $this->agendamentoProfessor->id_recurso,
            'id_usuario' => $this->professor->id_usuario,
        ])->toArray();
        $this->post(route('agendamentos.store'), $storeData)->assertStatus(201);
    }

    public function test_professor_can_destroy_own_appointment()
    {
        $this->actingAs($this->professor)
             ->delete(route('agendamentos.destroy', $this->agendamentoProfessor))
             ->assertOk();
    }

    public function test_professor_is_forbidden_from_destroying_other_appointment()
    {
        $this->actingAs($this->professor)
             ->delete(route('agendamentos.destroy', $this->agendamentoOutroProfessor))
             ->assertForbidden();
    }
}