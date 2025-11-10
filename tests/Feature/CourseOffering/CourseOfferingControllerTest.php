<?php

namespace Tests\Feature\CourseOffering;

use Tests\TestCase;
use App\Models\Usuario;
use App\Models\Escola;
use App\Models\Turma;
use App\Models\ComponenteCurricular;
use App\Models\OfertaComponente;
use App\Models\Agendamento;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CourseOfferingControllerTest extends TestCase
{
    use RefreshDatabase;

    private Usuario $admin;
    private Usuario $diretor;
    private Usuario $professor;
    private Usuario $outroProfessor;
    private Escola $escola;
    private Escola $outraEscola;
    private OfertaComponente $oferta;
    private OfertaComponente $outraOferta;
    private Turma $turma;
    private ComponenteCurricular $componente;

    protected function setUp(): void
    {
        parent::setUp();

        $this->escola = Escola::factory()->create();
        $this->outraEscola = Escola::factory()->create();
        
        $this->turma = Turma::factory()->create(['id_escola' => $this->escola->id_escola]);
        $this->componente = ComponenteCurricular::factory()->create(['id_escola' => $this->escola->id_escola]);

        $this->admin = Usuario::factory()->administrador()->create();
        $this->diretor = Usuario::factory()->diretor()->create(['id_escola' => $this->escola->id_escola]);
        $this->professor = Usuario::factory()->professor()->create(['id_escola' => $this->escola->id_escola]);
        $this->outroProfessor = Usuario::factory()->professor()->create(['id_escola' => $this->escola->id_escola]);

        $this->oferta = OfertaComponente::factory()->create([
            'id_turma' => $this->turma->id_turma,
            'id_componente_curricular' => $this->componente->id_componente_curricular,
            'id_professor' => $this->professor->id_usuario,
        ]);
        
        $this->outraOferta = OfertaComponente::factory()->create([
            'id_turma' => Turma::factory()->create(['id_escola' => $this->outraEscola->id_escola])->id_turma,
            'id_componente_curricular' => ComponenteCurricular::factory()->create(['id_escola' => $this->outraEscola->id_escola])->id_componente_curricular,
            'id_professor' => Usuario::factory()->professor()->create(['id_escola' => $this->outraEscola->id_escola])->id_usuario,
        ]);
    }

    public function test_admin_can_view_all_offerings_on_index()
    {
        $response = $this->actingAs($this->admin)->get(route('componentes.index'));

        $response->assertOk();
        $response->assertViewHas('ofertas', fn ($ofertas) => $ofertas->count() === 2);
    }

    public function test_diretor_can_view_only_own_school_offerings_on_index()
    {
        $response = $this->actingAs($this->diretor)->get(route('componentes.index'));

        $response->assertOk();
        $response->assertViewHas('ofertas', fn ($ofertas) => $ofertas->count() === 1);
        $response->assertSee($this->oferta->professor->nome_completo);
        $response->assertDontSee($this->outraOferta->professor->nome_completo);
    }

    public function test_professor_can_view_only_own_school_offerings_on_index()
    {
        $response = $this->actingAs($this->professor)->get(route('componentes.index'));

        $response->assertOk();
        $response->assertViewHas('ofertas', fn ($ofertas) => $ofertas->count() === 1);
        $response->assertSee($this->oferta->professor->nome_completo);
        $response->assertDontSee($this->outraOferta->professor->nome_completo);
    }

    public function test_admin_can_store_offering_for_any_school()
    {
        $data = [
            'id_turma' => $this->outraOferta->id_turma,
            'id_componente_curricular' => $this->outraOferta->id_componente_curricular,
            'id_professor' => $this->outraOferta->id_professor,
        ];
        
        OfertaComponente::query()->delete();

        $response = $this->actingAs($this->admin)->post(route('componentes.store'), $data);

        $response->assertRedirect(route('componentes.index'));
        $response->assertSessionHas('success', 'Oferta de componente cadastrada com sucesso!');
        $this->assertDatabaseHas('oferta_componentes', $data);
    }

    public function test_diretor_can_store_offering_for_own_school()
    {
        $data = [
            'id_turma' => $this->turma->id_turma,
            'id_componente_curricular' => $this->componente->id_componente_curricular,
            'id_professor' => $this->outroProfessor->id_usuario,
        ];

        $response = $this->actingAs($this->diretor)->post(route('componentes.store'), $data);

        $response->assertRedirect(route('componentes.index'));
        $response->assertSessionHas('success', 'Oferta de componente cadastrada com sucesso!');
        $this->assertDatabaseHas('oferta_componentes', $data);
    }

    public function test_diretor_cannot_store_offering_for_other_school()
    {
        $data = [
            'id_turma' => $this->outraOferta->id_turma,
            'id_componente_curricular' => $this->outraOferta->id_componente_curricular,
            'id_professor' => $this->outraOferta->id_professor,
        ];

        $response = $this->actingAs($this->diretor)->post(route('componentes.store'), $data);

        $response->assertSessionHasErrors(['id_turma', 'id_componente_curricular', 'id_professor']);
    }

    public function test_professor_can_store_offering_for_self()
    {
        $data = [
            'id_turma' => $this->turma->id_turma,
            'id_componente_curricular' => ComponenteCurricular::factory()->create(['id_escola' => $this->escola->id_escola])->id_componente_curricular,
            'id_professor' => $this->professor->id_usuario,
        ];

        $response = $this->actingAs($this->professor)->post(route('componentes.store'), $data);

        $response->assertRedirect(route('componentes.index'));
        $response->assertSessionHas('success', 'Oferta de componente cadastrada com sucesso!');
        $this->assertDatabaseHas('oferta_componentes', $data);
    }

    public function test_professor_cannot_store_offering_for_other_professor()
    {
        $data = [
            'id_turma' => $this->turma->id_turma,
            'id_componente_curricular' => $this->componente->id_componente_curricular,
            'id_professor' => $this->outroProfessor->id_usuario,
        ];

        $response = $this->actingAs($this->professor)->post(route('componentes.store'), $data);
        $response->assertSessionHasErrors('id_professor');
    }

    public function test_admin_can_update_any_offering()
    {
        $novoProfessor = Usuario::factory()->professor()->create(['id_escola' => $this->outraEscola->id_escola]);
        $data = $this->outraOferta->toArray();
        $data['id_professor'] = $novoProfessor->id_usuario;

        $response = $this->actingAs($this->admin)->put(route('componentes.update', $this->outraOferta), $data);

        $response->assertRedirect(route('componentes.index'));
        $this->assertDatabaseHas('oferta_componentes', ['id_oferta' => $this->outraOferta->id_oferta, 'id_professor' => $novoProfessor->id_usuario]);
    }

    public function test_diretor_can_update_own_school_offering()
    {
        $data = $this->oferta->toArray();
        $data['id_professor'] = $this->outroProfessor->id_usuario;
        
        $response = $this->actingAs($this->diretor)->put(route('componentes.update', $this->oferta), $data);
        
        $response->assertRedirect(route('componentes.index'));
        $this->assertDatabaseHas('oferta_componentes', ['id_oferta' => $this->oferta->id_oferta, 'id_professor' => $this->outroProfessor->id_usuario]);
    }

    public function test_diretor_cannot_update_other_school_offering()
    {
        $data = $this->outraOferta->toArray();
        $data['id_professor'] = $this->admin->id_usuario;

        $response = $this->actingAs($this->diretor)->put(route('componentes.update', $this->outraOferta), $data);
        
        $response->assertForbidden();
    }

    public function test_professor_can_update_own_offering()
    {
        $novaTurma = Turma::factory()->create(['id_escola' => $this->escola->id_escola]);
        $data = $this->oferta->toArray();
        $data['id_turma'] = $novaTurma->id_turma;

        $response = $this->actingAs($this->professor)->put(route('componentes.update', $this->oferta), $data);
        
        $response->assertRedirect(route('componentes.index'));
        $this->assertDatabaseHas('oferta_componentes', ['id_oferta' => $this->oferta->id_oferta, 'id_turma' => $novaTurma->id_turma]);
    }

    public function test_professor_cannot_update_other_professors_offering()
    {
        $outraOfertaMesmaEscola = OfertaComponente::factory()->create([
            'id_professor' => $this->outroProfessor->id_usuario,
            'id_turma' => $this->turma->id_turma,
        ]);
        $data = $outraOfertaMesmaEscola->toArray();
        $data['id_professor'] = $this->professor->id_usuario;

        $response = $this->actingAs($this->professor)->put(route('componentes.update', $outraOfertaMesmaEscola), $data);
        
        $response->assertForbidden();
    }

    public function test_admin_can_destroy_offering()
    {
        $response = $this->actingAs($this->admin)->delete(route('componentes.destroy', $this->oferta));

        $response->assertRedirect(route('componentes.index'));
        $response->assertSessionHas('success', 'Oferta de componente excluída com sucesso!');
        $this->assertDatabaseMissing('oferta_componentes', ['id_oferta' => $this->oferta->id_oferta]);
    }
    
    public function test_diretor_can_destroy_own_school_offering()
    {
        $response = $this->actingAs($this->diretor)->delete(route('componentes.destroy', $this->oferta));

        $response->assertRedirect(route('componentes.index'));
        $response->assertSessionHas('success', 'Oferta de componente excluída com sucesso!');
        $this->assertDatabaseMissing('oferta_componentes', ['id_oferta' => $this->oferta->id_oferta]);
    }

    public function test_professor_can_destroy_own_offering()
    {
        $response = $this->actingAs($this->professor)->delete(route('componentes.destroy', $this->oferta));

        $response->assertRedirect(route('componentes.index'));
        $response->assertSessionHas('success', 'Oferta de componente excluída com sucesso!');
        $this->assertDatabaseMissing('oferta_componentes', ['id_oferta' => $this->oferta->id_oferta]);
    }

    public function test_professor_cannot_destroy_other_professors_offering()
    {
        $outraOfertaMesmaEscola = OfertaComponente::factory()->create([
            'id_professor' => $this->outroProfessor->id_usuario,
            'id_turma' => $this->turma->id_turma,
        ]);
        
        $response = $this->actingAs($this->professor)->delete(route('componentes.destroy', $outraOfertaMesmaEscola));
        
        $response->assertForbidden();
    }

    public function test_cannot_destroy_offering_with_dependencies()
    {
        Agendamento::factory()->create(['id_oferta' => $this->oferta->id_oferta]);
        
        $response = $this->actingAs($this->admin)->delete(route('componentes.destroy', $this->oferta));

        $response->assertRedirect(route('componentes.index'));
        $response->assertSessionHas('error', 'Não é possível excluir a oferta pois ela possui 1 agendamento(s) vinculado(s).');
        $this->assertDatabaseHas('oferta_componentes', ['id_oferta' => $this->oferta->id_oferta]);
    }
}