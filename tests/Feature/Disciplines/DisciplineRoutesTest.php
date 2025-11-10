<?php

namespace Tests\Feature\Disciplines;

use Tests\TestCase;
use App\Models\Usuario;
use App\Models\Escola;
use App\Models\ComponenteCurricular;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DisciplineRoutesTest extends TestCase
{
    use RefreshDatabase;

    private Usuario $admin;
    private Usuario $diretor;
    private Usuario $professor;
    private Escola $escola;
    private Escola $outraEscola;
    private ComponenteCurricular $componente;
    private ComponenteCurricular $outroComponente;

    protected function setUp(): void
    {
        parent::setUp();

        $this->escola = Escola::factory()->create();
        $this->outraEscola = Escola::factory()->create();
        
        $this->componente = ComponenteCurricular::factory()->create(['id_escola' => $this->escola->id_escola]);
        $this->outroComponente = ComponenteCurricular::factory()->create(['id_escola' => $this->outraEscola->id_escola]);

        $this->admin = Usuario::factory()->administrador()->create();
        $this->diretor = Usuario::factory()->diretor()->create(['id_escola' => $this->escola->id_escola]);
        $this->professor = Usuario::factory()->professor()->create(['id_escola' => $this->escola->id_escola]);
    }

    public function test_guest_is_redirected_from_all_discipline_routes()
    {
        $this->get(route('disciplinas.index'))->assertRedirect(route('login'));
        $this->get(route('disciplinas.create'))->assertRedirect(route('login'));
        $this->post(route('disciplinas.store'))->assertRedirect(route('login'));
        $this->get(route('disciplinas.edit', $this->componente))->assertRedirect(route('login'));
        $this->put(route('disciplinas.update', $this->componente))->assertRedirect(route('login'));
        $this->delete(route('disciplinas.destroy', $this->componente))->assertRedirect(route('login'));
    }

    public function test_admin_can_access_all_discipline_routes()
    {
        $this->actingAs($this->admin);

        $this->get(route('disciplinas.index'))->assertOk();
        $this->get(route('disciplinas.create'))->assertOk();
        
        $storeData = ComponenteCurricular::factory()->make(['id_escola' => $this->escola->id_escola])->toArray();
        $this->post(route('disciplinas.store'), $storeData)->assertRedirect(route('disciplinas.index'));

        $this->get(route('disciplinas.edit', $this->componente))->assertOk();
        
        $updateData = ComponenteCurricular::factory()->make(['id_escola' => $this->escola->id_escola])->toArray();
        $this->put(route('disciplinas.update', $this->componente), $updateData)->assertRedirect(route('disciplinas.index'));
        
        $this->delete(route('disciplinas.destroy', $this->componente))->assertRedirect(route('disciplinas.index'));
    }

    public function test_diretor_can_access_discipline_routes_for_own_school()
    {
        $this->actingAs($this->diretor);

        $this->get(route('disciplinas.index'))->assertOk();
        $this->get(route('disciplinas.create'))->assertOk();
        
        $storeData = ComponenteCurricular::factory()->make(['id_escola' => $this->escola->id_escola])->toArray();
        $this->post(route('disciplinas.store'), $storeData)->assertRedirect(route('disciplinas.index'));

        $this->get(route('disciplinas.edit', $this->componente))->assertOk();
        
        $updateData = ComponenteCurricular::factory()->make(['id_escola' => $this->escola->id_escola])->toArray();
        $this->put(route('disciplinas.update', $this->componente), $updateData)->assertRedirect(route('disciplinas.index'));
        
        $this->delete(route('disciplinas.destroy', $this->componente))->assertRedirect(route('disciplinas.index'));
    }

    public function test_diretor_is_forbidden_from_discipline_routes_for_other_school()
    {
        $this->actingAs($this->diretor);

        $this->get(route('disciplinas.edit', $this->outroComponente))->assertForbidden();
        
        $updateData = ComponenteCurricular::factory()->make(['id_escola' => $this->outraEscola->id_escola])->toArray();
        $this->put(route('disciplinas.update', $this->outroComponente), $updateData)->assertForbidden();
        
        $this->delete(route('disciplinas.destroy', $this->outroComponente))->assertForbidden();
    }

    public function test_professor_can_only_access_index_route()
    {
        $this->actingAs($this->professor);
        
        $this->get(route('disciplinas.index'))->assertOk();
    }

    public function test_professor_is_forbidden_from_modifying_discipline_routes()
    {
        $this->actingAs($this->professor);

        $this->get(route('disciplinas.create'))->assertForbidden();
        
        $storeData = ComponenteCurricular::factory()->make(['id_escola' => $this->escola->id_escola])->toArray();
        $this->post(route('disciplinas.store'), $storeData)->assertForbidden();

        $this->get(route('disciplinas.edit', $this->componente))->assertForbidden();
        
        $updateData = ComponenteCurricular::factory()->make(['id_escola' => $this->escola->id_escola])->toArray();
        $this->put(route('disciplinas.update', $this->componente), $updateData)->assertForbidden();
        
        $this->delete(route('disciplinas.destroy', $this->componente))->assertForbidden();
    }
}