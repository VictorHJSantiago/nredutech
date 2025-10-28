<?php

namespace Tests\Unit\Appointments;

use Tests\TestCase;
use App\Http\Resources\AppointmentResource;
use App\Models\Agendamento;
use App\Models\Usuario;
use App\Models\Escola;
use App\Models\Turma;
use App\Models\Municipio;
use App\Models\ComponenteCurricular;
use App\Models\RecursoDidatico;
use App\Models\OfertaComponente;
use Illuminate\Http\Request;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

class AppointmentResourceTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function formata_corretamente_os_dados_do_agendamento()
    {
        $municipio = Municipio::factory()->create();
        $escola = Escola::factory()->create(['id_municipio' => $municipio->id_municipio, 'nome' => 'Escola XYZ']);
        $professor = Usuario::factory()->create(['id_escola' => $escola->id_escola, 'tipo_usuario' => 'professor', 'nome_completo' => 'Prof Teste']);
        $turma = Turma::factory()->create(['id_escola' => $escola->id_escola, 'serie' => '8B', 'turno' => 'tarde']);
        $componente = ComponenteCurricular::factory()->create(['nome' => 'Ciências']);
        $recurso = RecursoDidatico::factory()->create(['nome' => 'Microscópio']);
        $oferta = OfertaComponente::factory()->create([
            'id_turma' => $turma->id_turma,
            'id_professor' => $professor->id_usuario,
            'id_componente' => $componente->id_componente
        ]);
        $inicio = Carbon::parse('2025-10-28 14:00:00');
        $fim = Carbon::parse('2025-10-28 15:00:00');
        $agendamento = Agendamento::factory()->create([
            'id_agendamento' => 55,
            'id_recurso' => $recurso->id_recurso,
            'id_oferta' => $oferta->id_oferta,
            'data_hora_inicio' => $inicio,
            'data_hora_fim' => $fim,
            'status' => 'agendado'
        ]);

        $agendamento->load(['recurso', 'oferta.turma.escola', 'oferta.professor', 'oferta.componenteCurricular']);
        $resource = new AppointmentResource($agendamento);
        $request = Request::create('/api/agendamentos/55', 'GET');
        $resourceArray = $resource->toArray($request);
        $this->assertEquals(55, $resourceArray['id']);
        $this->assertEquals("Rec: Microscópio", $resourceArray['title']); 
        $this->assertEquals($inicio->toISOString(), $resourceArray['start']); 
        $this->assertEquals($fim->toISOString(), $resourceArray['end']);
        $this->assertEquals('agendado', $resourceArray['status']);
        $this->assertEquals($recurso->id_recurso, $resourceArray['recurso_id']);
        $this->assertEquals($oferta->id_oferta, $resourceArray['oferta_id']);
        $this->assertEquals($turma->id_turma, $resourceArray['turma_id']);
        $this->assertStringContainsString("Disc: Ciências", $resourceArray['description']);
        $this->assertStringContainsString("Turma: 8B (tarde)", $resourceArray['description']);
        $this->assertStringContainsString("Prof: Prof Teste", $resourceArray['description']);
        $this->assertStringContainsString("Escola: Escola XYZ", $resourceArray['description']);
        $this->assertEquals('#0275d8', $resourceArray['color']); 
    }
}