<?php

namespace Tests\Unit\Appointments;

use Tests\TestCase;
use App\Models\Agendamento;
use App\Models\RecursoDidatico;
use App\Models\OfertaComponente;
use App\Models\Usuario;
use App\Models\Turma;
use App\Models\ComponenteCurricular;
use App\Models\Escola;
use App\Models\Municipio;
use App\Http\Resources\AppointmentResource;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AppointmentResourceTest extends TestCase
{
    use RefreshDatabase;

    private Usuario $admin;
    private Usuario $diretor;
    private Usuario $professor;
    private Agendamento $agendamento;
    private Escola $escola;

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

        $this->admin = Usuario::factory()->create([
            'tipo_usuario' => 'administrador', 
            'status_aprovacao' => 'ativo',
            'id_escola' => null
        ]);
        $this->diretor = Usuario::factory()->create([
            'tipo_usuario' => 'diretor', 
            'status_aprovacao' => 'ativo',
            'id_escola' => $this->escola->id_escola
        ]);
        $this->professor = Usuario::factory()->create([
            'tipo_usuario' => 'professor', 
            'status_aprovacao' => 'ativo',
            'id_escola' => $this->escola->id_escola
        ]);

        $turma = Turma::factory()->create(['id_escola' => $this->escola->id_escola]);
        
        $componente = ComponenteCurricular::create([
            'nome' => 'Matemática',
            'carga_horaria' => 60,
            'id_escola' => $this->escola->id_escola,
            'status' => 'aprovado'
        ]);

        $oferta = OfertaComponente::factory()->create([
            'id_turma' => $turma->id_turma,
            'id_componente' => $componente->id_componente,
            'id_professor' => $this->professor->id_usuario,
        ]);

        $recurso = RecursoDidatico::factory()->create([
            'id_escola' => $this->escola->id_escola,
            'status' => 'funcionando'
        ]);

        $this->agendamento = Agendamento::create([
            'id_recurso' => $recurso->id_recurso,
            'id_oferta' => $oferta->id_oferta,
            'data_hora_inicio' => now()->addHour(),
            'data_hora_fim' => now()->addHours(2),
            'status' => 'agendado'
        ]);
        
        $this->agendamento->load('oferta.professor', 'recurso');
    }

    public function testa_recurso_de_agendamento_transforma_corretamente()
    {
        Auth::login($this->admin);
        $resource = new AppointmentResource($this->agendamento);
        $resourceArray = $resource->toArray(new Request());

        $this->assertEquals($this->agendamento->id_agendamento, $resourceArray['id']);
        $this->assertEquals($this->agendamento->data_hora_inicio, $resourceArray['dataHoraInicio']);
        $this->assertEquals($this->agendamento->data_hora_fim, $resourceArray['dataHoraFim']);
        $this->assertEquals($this->agendamento->status, $resourceArray['status']);
        
        $this->assertArrayHasKey('recurso', $resourceArray);
        $this->assertArrayHasKey('oferta', $resourceArray);
        
        $this->assertArrayNotHasKey('can_cancel', $resourceArray);
        $this->assertArrayNotHasKey('usuario', $resourceArray);
    }
}