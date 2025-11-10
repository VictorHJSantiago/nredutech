<?php

namespace Tests\Feature\CourseOffering;

use Tests\TestCase;
use App\Models\Usuario;
use App\Models\Escola;
use App\Models\Turma;
use App\Models\ComponenteCurricular;
use App\Models\OfertaComponente;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CourseOfferingRoutesTest extends TestCase
{
    use RefreshDatabase;

    private Usuario $admin;
    private Usuario $diretor;
    private Usuario $professor;
    private Usuario $outroProfessor;
    private Escola $escola;
    private OfertaComponente $oferta;
    private OfertaComponente $outraOferta;

    protected function setUp(): void
    {
        parent::setUp();

        $this->escola = Escola::factory()->create();
        $this->admin = Usuario::factory()->administrador()->create();
        $this->diretor = Usuario::factory()->diretor()->create(['id_escola' => $this->escola->id_escola]);
        $this->professor = Usuario::factory()->professor()->create(['id_escola' => $this->escola->id_escola]);
        $this->outroProfessor = Usuario::factory()->professor()->create(['id_escola' => $this->escola->id_escola]);

        $this->oferta = OfertaComponente::factory()->create([
            'id_professor' => $this->professor->id_usuario,
            'id_turma' => Turma::factory()->create(['id_escola' => $this->escola->id_escola])->id_turma,
        ]);
        
        $this->outraOferta = OfertaComponente::factory()->create([
            'id_professor' => $this->outroProfessor->id_usuario,
            'id_turma' => Turma::factory()->create(['id_escola' => $this->escola->id_escola])->id_turma,
        ]);
    }

    public function test_guest_is_redirected_from_all_course_offering_routes()
    {
        $this->get(route('componentes.index'))->assertRedirect(route('login'));
        $this->post(route('componentes.store'))->assertRedirect(route('login'));
        $this->put(route('componentes.update', $this->oferta))->assertRedirect(route('login'));
        $this->delete(route('componentes.destroy', $this->oferta))->assertRedirect(route('login'));
    }

    public function test_admin_can_access_all_course_offering_routes()
    {
        $this->actingAs($this->admin);

        $this->get(route('componentes.index'))->assertOk();
        
        $storeData = OfertaComponente::factory()->make()->toArray();
        $this->post(route('componentes.store'), $storeData)->assertRedirect(route('componentes.index'));

        $updateData = OfertaComponente::factory()->make()->toArray();
        $this->put(route('componentes.update', $this->oferta), $updateData)->assertRedirect(route('componentes.index'));
        
        $this->delete(route('componentes.destroy', $this->oferta))->assertRedirect(route('componentes.index'));
    }

    public function test_diretor_can_access_all_course_offering_routes()
    {
        $this->actingAs($this->diretor);

        $this->get(route('componentes.index'))->assertOk();
        
        $storeData = OfertaComponente::factory()->make([
            'id_turma' => $this->oferta->id_turma,
            'id_professor' => $this->professor->id_usuario,
            'id_componente_curricular' => ComponenteCurricular::factory()->create(['id_escola' => $this->escola->id_escola]),
        ])->toArray();
        $this->post(route('componentes.store'), $storeData)->assertRedirect(route('componentes.index'));

        $updateData = OfertaComponente::factory()->make([
            'id_turma' => $this->oferta->id_turma,
            'id_professor' => $this->professor->id_usuario,
        ])->toArray();
        $this->put(route('componentes.update', $this->oferta), $updateData)->assertRedirect(route('componentes.index'));
        
        $this->delete(route('componentes.destroy', $this->oferta))->assertRedirect(route('componentes.index'));
    }

    public function test_professor_can_access_index_and_own_routes()
    {
        $this->actingAs($this->professor);

        $this->get(route('componentes.index'))->assertOk();
        
        $storeData = OfertaComponente::factory()->make([
            'id_turma' => $this->oferta->id_turma,
            'id_professor' => $this->professor->id_usuario,
            'id_componente_curricular' => ComponenteCurricular::factory()->create(['id_escola' => $this->escola->id_escola]),
        ])->toArray();
        $this->post(route('componentes.store'), $storeData)->assertRedirect(route('componentes.index'));

        $updateData = OfertaComponente::factory()->make([
            'id_turma' => $this->oferta->id_turma,
            'id_professor' => $this->professor->id_usuario,
        ])->toArray();
        $this->put(route('componentes.update', $this->oferta), $updateData)->assertRedirect(route('componentes.index'));
        
        $this->delete(route('componentes.destroy', $this->oferta))->assertRedirect(route('componentes.index'));
    }

    public function test_professor_is_forbidden_from_other_professors_routes()
    {
        $this->actingAs($this->professor);

        $updateData = OfertaComponente::factory()->make([
            'id_turma' => $this->outraOferta->id_turma,
            'id_professor' => $this->outroProfessor->id_usuario,
        ])->toArray();
        $this->put(route('componentes.update', $this->outraOferta), $updateData)->assertForbidden();
        
        $this->delete(route('componentes.destroy', $this->outraOferta))->assertForbidden();
    }
}