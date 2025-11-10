<?php

namespace Tests\Feature\Disciplines;

use Tests\TestCase;
use App\Models\Usuario;
use App\Models\Escola;
use App\Models\ComponenteCurricular;
use App\Models\OfertaComponente;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CurricularComponentControllerTest extends TestCase
{
    use RefreshDatabase;

    private Usuario $admin;
    private Usuario $diretor;
    private Usuario $professor;
    private Escola $escola;
    private Escola $outraEscola;
    private ComponenteCurricular $componenteGlobal;
    private ComponenteCurricular $componenteEscola;
    private ComponenteCurricular $outroComponente;

    protected function setUp(): void
    {
        parent::setUp();

        $this->escola = Escola::factory()->create();
        $this->outraEscola = Escola::factory()->create();
        
        $this->componenteGlobal = ComponenteCurricular::factory()->create(['nome' => 'Global', 'id_escola' => null]);
        $this->componenteEscola = ComponenteCurricular::factory()->create(['nome' => 'Matemática', 'id_escola' => $this->escola->id_escola]);
        $this->outroComponente = ComponenteCurricular::factory()->create(['nome' => 'Física', 'id_escola' => $this->outraEscola->id_escola]);

        $this->admin = Usuario::factory()->administrador()->create();
        $this->diretor = Usuario::factory()->diretor()->create(['id_escola' => $this->escola->id_escola]);
        $this->professor = Usuario::factory()->professor()->create(['id_escola' => $this->escola->id_escola]);
    }

    public function test_admin_can_view_all_components_on_index()
    {
        $response = $this->actingAs($this->admin)->get(route('disciplinas.index'));

        $response->assertOk();
        $response->assertViewHas('componentes', fn ($componentes) => $componentes->count() === 3);
        $response->assertSee('Global');
        $response->assertSee('Matemática');
        $response->assertSee('Física');
    }

    public function test_diretor_can_view_own_school_and_global_components_on_index()
    {
        $response = $this->actingAs($this->diretor)->get(route('disciplinas.index'));

        $response->assertOk();
        $response->assertViewHas('componentes', fn ($componentes) => $componentes->count() === 2);
        $response->assertSee('Global');
        $response->assertSee('Matemática');
        $response->assertDontSee('Física');
    }

    public function test_professor_can_view_own_school_and_global_components_on_index()
    {
        $response = $this->actingAs($this->professor)->get(route('disciplinas.index'));

        $response->assertOk();
        $response->assertViewHas('componentes', fn ($componentes) => $componentes->count() === 2);
        $response->assertSee('Global');
        $response->assertSee('Matemática');
        $response->assertDontSee('Física');
    }

    public function test_admin_can_filter_components_by_school()
    {
        $response = $this->actingAs($this->admin)->get(route('disciplinas.index', ['escola_id' => $this->outraEscola->id_escola]));

        $response->assertOk();
        $response->assertViewHas('componentes', fn ($componentes) => $componentes->count() === 1);
        $response->assertSee('Física');
        $response->assertDontSee('Global');
    }

    public function test_admin_can_filter_components_by_global()
    {
        $response = $this->actingAs($this->admin)->get(route('disciplinas.index', ['escola_id' => 'global']));

        $response->assertOk();
        $response->assertViewHas('componentes', fn ($componentes) => $componentes->count() === 1);
        $response->assertSee('Global');
        $response->assertDontSee('Física');
    }

    public function test_admin_can_store_global_component()
    {
        $data = [
            'nome' => 'Nova Global',
            'id_escola' => null,
        ];

        $response = $this->actingAs($this->admin)->post(route('disciplinas.store'), $data);

        $response->assertRedirect(route('disciplinas.index'));
        $response->assertSessionHas('success', 'Disciplina cadastrada com sucesso!');
        $this->assertDatabaseHas('componentes_curriculares', $data);
    }

    public function test_admin_can_store_school_component()
    {
        $data = [
            'nome' => 'Nova da Escola',
            'id_escola' => $this->outraEscola->id_escola,
        ];

        $response = $this->actingAs($this->admin)->post(route('disciplinas.store'), $data);

        $response->assertRedirect(route('disciplinas.index'));
        $this->assertDatabaseHas('componentes_curriculares', $data);
    }

    public function test_diretor_can_store_own_school_component()
    {
        $data = [
            'nome' => 'Nova da Escola do Diretor',
            'id_escola' => $this->escola->id_escola,
        ];

        $response = $this->actingAs($this->diretor)->post(route('disciplinas.store'), $data);

        $response->assertRedirect(route('disciplinas.index'));
        $this->assertDatabaseHas('componentes_curriculares', $data);
    }

    public function test_diretor_cannot_store_global_component()
    {
        $data = [
            'nome' => 'Nova Global Proibida',
            'id_escola' => null,
        ];

        $response = $this->actingAs($this->diretor)->post(route('disciplinas.store'), $data);
        $response->assertSessionHasErrors('id_escola');
    }

    public function test_diretor_cannot_store_other_school_component()
    {
        $data = [
            'nome' => 'Nova Proibida',
            'id_escola' => $this->outraEscola->id_escola,
        ];

        $response = $this->actingAs($this->diretor)->post(route('disciplinas.store'), $data);
        $response->assertSessionHasErrors('id_escola');
    }

    public function test_admin_can_update_any_component()
    {
        $data = ['nome' => 'Física Atualizada'] + $this->outroComponente->toArray();
        
        $response = $this->actingAs($this->admin)->put(route('disciplinas.update', $this->outroComponente), $data);

        $response->assertRedirect(route('disciplinas.index'));
        $this->assertDatabaseHas('componentes_curriculares', ['id_componente_curricular' => $this->outroComponente->id_componente_curricular, 'nome' => 'Física Atualizada']);
    }

    public function test_diretor_can_update_own_school_component()
    {
        $data = ['nome' => 'Matemática Atualizada'] + $this->componenteEscola->toArray();
        
        $response = $this->actingAs($this->diretor)->put(route('disciplinas.update', $this->componenteEscola), $data);

        $response->assertRedirect(route('disciplinas.index'));
        $this->assertDatabaseHas('componentes_curriculares', ['id_componente_curricular' => $this->componenteEscola->id_componente_curricular, 'nome' => 'Matemática Atualizada']);
    }

    public function test_diretor_cannot_update_other_school_component()
    {
        $data = ['nome' => 'Física Proibida'] + $this->outroComponente->toArray();
        
        $response = $this->actingAs($this->diretor)->put(route('disciplinas.update', $this->outroComponente), $data);
        $response->assertForbidden();
    }

    public function test_diretor_cannot_update_global_component()
    {
        $data = ['nome' => 'Global Proibida'] + $this->componenteGlobal->toArray();
        
        $response = $this->actingAs($this->diretor)->put(route('disciplinas.update', $this->componenteGlobal), $data);
        $response->assertForbidden();
    }

    public function test_admin_can_destroy_component()
    {
        $response = $this->actingAs($this->admin)->delete(route('disciplinas.destroy', $this->componenteEscola));

        $response->assertRedirect(route('disciplinas.index'));
        $response->assertSessionHas('success', 'Disciplina excluída com sucesso!');
        $this->assertDatabaseMissing('componentes_curriculares', ['id_componente_curricular' => $this->componenteEscola->id_componente_curricular]);
    }
    
    public function test_diretor_can_destroy_own_school_component()
    {
        $response = $this->actingAs($this->diretor)->delete(route('disciplinas.destroy', $this->componenteEscola));

        $response->assertRedirect(route('disciplinas.index'));
        $this->assertDatabaseMissing('componentes_curriculares', ['id_componente_curricular' => $this->componenteEscola->id_componente_curricular]);
    }

    public function test_diretor_cannot_destroy_other_school_component()
    {
        $response = $this->actingAs($this->diretor)->delete(route('disciplinas.destroy', $this->outroComponente));
        $response->assertForbidden();
    }

    public function test_diretor_cannot_destroy_global_component()
    {
        $response = $this->actingAs($this->diretor)->delete(route('disciplinas.destroy', $this->componenteGlobal));
        $response->assertForbidden();
    }

    public function test_cannot_destroy_component_with_dependencies()
    {
        OfertaComponente::factory()->create(['id_componente_curricular' => $this->componenteEscola->id_componente_curricular]);
        
        $response = $this->actingAs($this->admin)->delete(route('disciplinas.destroy', $this->componenteEscola));

        $response->assertRedirect(route('disciplinas.index'));
        $response->assertSessionHas('error', 'Não é possível excluir a disciplina pois ela possui 1 oferta(s) vinculada(s).');
        $this->assertDatabaseHas('componentes_curriculares', ['id_componente_curricular' => $this->componenteEscola->id_componente_curricular]);
    }
}