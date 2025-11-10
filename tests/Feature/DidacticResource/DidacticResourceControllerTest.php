<?php

namespace Tests\Feature\DidacticResource;

use Tests\TestCase;
use App\Models\Usuario;
use App\Models\Escola;
use App\Models\RecursoDidatico;
use App\Models\Agendamento;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DidacticResourceControllerTest extends TestCase
{
    use RefreshDatabase;

    private Usuario $admin;
    private Usuario $diretorA;
    private Usuario $professorA;
    private Usuario $outroProfessorA;
    private Usuario $diretorB;
    private Escola $escolaA;
    private Escola $escolaB;
    private RecursoDidatico $recursoGlobal;
    private RecursoDidatico $recursoDiretorA;
    private RecursoDidatico $recursoProfessorA;
    private RecursoDidatico $recursoDiretorB;

    protected function setUp(): void
    {
        parent::setUp();

        $this->escolaA = Escola::factory()->create();
        $this->escolaB = Escola::factory()->create();
        
        $this->admin = Usuario::factory()->administrador()->create();
        $this->diretorA = Usuario::factory()->diretor()->create(['id_escola' => $this->escolaA->id_escola]);
        $this->professorA = Usuario::factory()->professor()->create(['id_escola' => $this->escolaA->id_escola]);
        $this->outroProfessorA = Usuario::factory()->professor()->create(['id_escola' => $this->escolaA->id_escola]);
        $this->diretorB = Usuario::factory()->diretor()->create(['id_escola' => $this->escolaB->id_escola]);

        $this->recursoGlobal = RecursoDidatico::factory()->create(['nome' => 'Global', 'id_escola' => null, 'id_usuario_criador' => $this->admin->id_usuario]);
        $this->recursoDiretorA = RecursoDidatico::factory()->create(['nome' => 'Recurso Diretor A', 'id_escola' => $this->escolaA->id_escola, 'id_usuario_criador' => $this->diretorA->id_usuario]);
        $this->recursoProfessorA = RecursoDidatico::factory()->create(['nome' => 'Recurso Professor A', 'id_escola' => $this->escolaA->id_escola, 'id_usuario_criador' => $this->professorA->id_usuario]);
        $this->recursoDiretorB = RecursoDidatico::factory()->create(['nome' => 'Recurso Diretor B', 'id_escola' => $this->escolaB->id_escola, 'id_usuario_criador' => $this->diretorB->id_usuario]);
    }

    public function test_admin_can_view_all_resources_on_index()
    {
        $response = $this->actingAs($this->admin)->get(route('resources.index'));
        $response->assertOk();
        $response->assertViewHas('recursos', fn ($recursos) => $recursos->count() === 4);
    }

    public function test_diretor_can_view_own_school_and_global_resources_on_index()
    {
        $response = $this->actingAs($this->diretorA)->get(route('resources.index'));
        $response->assertOk();
        $response->assertViewHas('recursos', fn ($recursos) => $recursos->count() === 3);
        $response->assertSee('Global');
        $response->assertSee('Recurso Diretor A');
        $response->assertSee('Recurso Professor A');
        $response->assertDontSee('Recurso Diretor B');
    }

    public function test_professor_can_view_own_school_and_global_resources_on_index()
    {
        $response = $this->actingAs($this->professorA)->get(route('resources.index'));
        $response->assertOk();
        $response->assertViewHas('recursos', fn ($recursos) => $recursos->count() === 3);
        $response->assertSee('Global');
        $response->assertDontSee('Recurso Diretor B');
    }

    public function test_resource_index_filters_work_correctly()
    {
        $response = $this->actingAs($this->admin)->get(route('resources.index', ['search' => 'Global']));
        $response->assertViewHas('recursos', fn ($recursos) => $recursos->count() === 1 && $recursos->first()->nome === 'Global');

        $response = $this->actingAs($this->admin)->get(route('resources.index', ['escola_id' => $this->escolaA->id_escola]));
        $response->assertViewHas('recursos', fn ($recursos) => $recursos->count() === 2);

        $response = $this->actingAs($this->admin)->get(route('resources.index', ['escola_id' => 'global']));
        $response->assertViewHas('recursos', fn ($recursos) => $recursos->count() === 1 && $recursos->first()->nome === 'Global');
    }

    public function test_admin_can_store_global_resource()
    {
        $data = RecursoDidatico::factory()->make(['nome' => 'Novo Global', 'id_escola' => null, 'id_usuario_criador' => $this->admin->id_usuario])->toArray();
        $response = $this->actingAs($this->admin)->post(route('resources.store'), $data);

        $response->assertRedirect(route('resources.index'));
        $response->assertSessionHas('success', 'Recurso cadastrado com sucesso!');
        $this->assertDatabaseHas('recursos_didaticos', ['nome' => 'Novo Global', 'id_escola' => null]);
    }

    public function test_diretor_can_store_own_school_resource()
    {
        $data = RecursoDidatico::factory()->make(['nome' => 'Novo Recurso Diretor', 'id_escola' => $this->diretorA->id_escola, 'id_usuario_criador' => $this->diretorA->id_usuario])->toArray();
        $response = $this->actingAs($this->diretorA)->post(route('resources.store'), $data);

        $response->assertRedirect(route('resources.index'));
        $this->assertDatabaseHas('recursos_didaticos', ['nome' => 'Novo Recurso Diretor', 'id_escola' => $this->escolaA->id_escola]);
    }

    public function test_professor_can_store_own_school_resource()
    {
        $data = RecursoDidatico::factory()->make(['nome' => 'Novo Recurso Professor', 'id_escola' => $this->professorA->id_escola, 'id_usuario_criador' => $this->professorA->id_usuario])->toArray();
        $response = $this->actingAs($this->professorA)->post(route('resources.store'), $data);

        $response->assertRedirect(route('resources.index'));
        $this->assertDatabaseHas('recursos_didaticos', ['nome' => 'Novo Recurso Professor', 'id_escola' => $this->escolaA->id_escola]);
    }

    public function test_diretor_cannot_store_global_resource()
    {
        $data = RecursoDidatico::factory()->make(['nome' => 'Global Proibido', 'id_escola' => null, 'id_usuario_criador' => $this->diretorA->id_usuario])->toArray();
        $response = $this->actingAs($this->diretorA)->post(route('resources.store'), $data);
        $response->assertSessionHasErrors('id_escola');
    }

    public function test_diretor_cannot_store_other_school_resource()
    {
        $data = RecursoDidatico::factory()->make(['nome' => 'Recurso Proibido', 'id_escola' => $this->escolaB->id_escola, 'id_usuario_criador' => $this->diretorA->id_usuario])->toArray();
        $response = $this->actingAs($this->diretorA)->post(route('resources.store'), $data);
        $response->assertSessionHasErrors('id_escola');
    }

    public function test_admin_can_update_any_resource()
    {
        $data = $this->recursoDiretorB->toArray();
        $data['nome'] = 'Atualizado pelo Admin';
        
        $response = $this->actingAs($this->admin)->put(route('resources.update', $this->recursoDiretorB), $data);
        $response->assertRedirect(route('resources.index'));
        $this->assertDatabaseHas('recursos_didaticos', ['id_recurso' => $this->recursoDiretorB->id_recurso, 'nome' => 'Atualizado pelo Admin']);
    }

    public function test_diretor_can_update_own_school_resource_created_by_professor()
    {
        $data = $this->recursoProfessorA->toArray();
        $data['nome'] = 'Atualizado pelo Diretor';
        
        $response = $this->actingAs($this->diretorA)->put(route('resources.update', $this->recursoProfessorA), $data);
        $response->assertRedirect(route('resources.index'));
        $this->assertDatabaseHas('recursos_didaticos', ['id_recurso' => $this->recursoProfessorA->id_recurso, 'nome' => 'Atualizado pelo Diretor']);
    }

    public function test_diretor_cannot_update_other_school_resource()
    {
        $data = $this->recursoDiretorB->toArray();
        $data['nome'] = 'Update Proibido';
        
        $response = $this->actingAs($this->diretorA)->put(route('resources.update', $this->recursoDiretorB), $data);
        $response->assertForbidden();
    }

    public function test_diretor_cannot_update_global_resource()
    {
        $data = $this->recursoGlobal->toArray();
        $data['nome'] = 'Update Proibido';
        
        $response = $this->actingAs($this->diretorA)->put(route('resources.update', $this->recursoGlobal), $data);
        $response->assertForbidden();
    }

    public function test_professor_can_update_own_resource()
    {
        $data = $this->recursoProfessorA->toArray();
        $data['nome'] = 'Atualizado pelo Criador';
        
        $response = $this->actingAs($this->professorA)->put(route('resources.update', $this->recursoProfessorA), $data);
        $response->assertRedirect(route('resources.index'));
        $this->assertDatabaseHas('recursos_didaticos', ['id_recurso' => $this->recursoProfessorA->id_recurso, 'nome' => 'Atualizado pelo Criador']);
    }

    public function test_professor_cannot_update_other_professor_resource()
    {
        $recursoOutroProf = RecursoDidatico::factory()->create(['id_escola' => $this->escolaA->id_escola, 'id_usuario_criador' => $this->outroProfessorA->id_usuario]);
        $data = $recursoOutroProf->toArray();
        $data['nome'] = 'Update Proibido';
        
        $response = $this->actingAs($this->professorA)->put(route('resources.update', $recursoOutroProf), $data);
        $response->assertForbidden();
    }

    public function test_admin_can_destroy_any_resource()
    {
        $response = $this->actingAs($this->admin)->delete(route('resources.destroy', $this->recursoDiretorB));
        $response->assertRedirect(route('resources.index'));
        $response->assertSessionHas('success', 'Recurso excluído com sucesso!');
        $this->assertDatabaseMissing('recursos_didaticos', ['id_recurso' => $this->recursoDiretorB->id_recurso]);
    }

    public function test_diretor_can_destroy_own_school_resource()
    {
        $response = $this->actingAs($this->diretorA)->delete(route('resources.destroy', $this->recursoProfessorA));
        $response->assertRedirect(route('resources.index'));
        $this->assertDatabaseMissing('recursos_didaticos', ['id_recurso' => $this->recursoProfessorA->id_recurso]);
    }

    public function test_diretor_cannot_destroy_other_school_resource()
    {
        $response = $this->actingAs($this->diretorA)->delete(route('resources.destroy', $this->recursoDiretorB));
        $response->assertForbidden();
    }

    public function test_professor_can_destroy_own_resource()
    {
        $response = $this->actingAs($this->professorA)->delete(route('resources.destroy', $this->recursoProfessorA));
        $response->assertRedirect(route('resources.index'));
        $this->assertDatabaseMissing('recursos_didaticos', ['id_recurso' => $this->recursoProfessorA->id_recurso]);
    }

    public function test_professor_cannot_destroy_other_professor_resource()
    {
        $response = $this->actingAs($this->professorA)->delete(route('resources.destroy', $this->recursoDiretorA));
        $response->assertForbidden();
    }

    public function test_cannot_destroy_resource_with_dependencies()
    {
        Agendamento::factory()->create(['id_recurso' => $this->recursoGlobal->id_recurso]);
        
        $response = $this->actingAs($this->admin)->delete(route('resources.destroy', $this->recursoGlobal));
        $response->assertRedirect(route('resources.index'));
        $response->assertSessionHas('error', 'Não é possível excluir o recurso pois ele possui 1 agendamento(s) vinculado(s).');
        $this->assertDatabaseHas('recursos_didaticos', ['id_recurso' => $this->recursoGlobal->id_recurso]);
    }
}