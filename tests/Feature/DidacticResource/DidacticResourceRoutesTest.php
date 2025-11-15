<?php

namespace Tests\Feature\DidacticResource;

use Tests\TestCase;
use App\Models\Usuario;
use App\Models\Escola;
use App\Models\Municipio;
use App\Models\RecursoDidatico;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class DidacticResourceRoutesTest extends TestCase
{
    use RefreshDatabase;

    private Usuario $admin;
    private Usuario $diretor;
    private Usuario $professor;
    private RecursoDidatico $recurso;

    protected function setUp(): void
    {
        parent::setUp();

        $municipio = Municipio::create(['nome' => 'Municipio Teste', 'estado' => 'PR']);
        $escola = Escola::create(['nome' => 'Escola Teste', 'id_municipio' => $municipio->id_municipio, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana']);

        $this->admin = Usuario::factory()->create(['tipo_usuario' => 'administrador', 'id_escola' => null]);
        $this->diretor = Usuario::factory()->create(['tipo_usuario' => 'diretor', 'id_escola' => $escola->id_escola]);
        $this->professor = Usuario::factory()->create(['tipo_usuario' => 'professor', 'id_escola' => $escola->id_escola]);
        
        $this->recurso = RecursoDidatico::factory()->create(['id_usuario_criador' => $this->admin->id_usuario, 'id_escola' => null]);
    }

    #[Test]
    public function convidado_e_redirecionado_de_todas_rotas_de_recursos()
    {
        $this->get(route('resources.index'))->assertRedirect(route('login'));
        $this->get(route('resources.create'))->assertRedirect(route('login'));
        $this->post(route('resources.store'))->assertRedirect(route('login'));
        $this->get(route('resources.edit', $this->recurso))->assertRedirect(route('login'));
        $this->put(route('resources.update', $this->recurso))->assertRedirect(route('login'));
        $this->delete(route('resources.destroy', $this->recurso))->assertRedirect(route('login'));
    }

    #[Test]
    public function admin_pode_acessar_todas_rotas_de_recursos()
    {
        $this->actingAs($this->admin);

        $this->get(route('resources.index'))->assertOk();
        $this->get(route('resources.create'))->assertOk();
        
        $storeData = RecursoDidatico::factory()->make(['id_escola' => null, 'id_usuario_criador' => $this->admin->id_usuario])->toArray();
        $this->post(route('resources.store'), $storeData)->assertRedirect(route('resources.index'));

        $this->get(route('resources.edit', $this->recurso))->assertOk();
        
        $updateData = RecursoDidatico::factory()->make(['id_escola' => null, 'id_usuario_criador' => $this->admin->id_usuario])->toArray();
        $this->put(route('resources.update', $this->recurso), $updateData)->assertRedirect(route('resources.index'));
        
        $this->delete(route('resources.destroy', $this->recurso))->assertRedirect(route('resources.index'));
    }

    #[Test]
    public function diretor_pode_acessar_todas_rotas_de_recursos()
    {
        $this->actingAs($this->diretor);

        $this->get(route('resources.index'))->assertOk();
        $this->get(route('resources.create'))->assertOk();
        
        $storeData = RecursoDidatico::factory()->make(['id_escola' => $this->diretor->id_escola, 'id_usuario_criador' => $this->diretor->id_usuario])->toArray();
        $this->post(route('resources.store'), $storeData)->assertRedirect(route('resources.index'));

        $recursoDiretor = RecursoDidatico::factory()->create(['id_usuario_criador' => $this->diretor->id_usuario, 'id_escola' => $this->diretor->id_escola]);
        $this->get(route('resources.edit', $recursoDiretor))->assertOk();
        
        $updateData = RecursoDidatico::factory()->make(['id_escola' => $this->diretor->id_escola, 'id_usuario_criador' => $this->diretor->id_usuario])->toArray();
        $this->put(route('resources.update', $recursoDiretor), $updateData)->assertRedirect(route('resources.index'));
        
        $this->delete(route('resources.destroy', $recursoDiretor))->assertRedirect(route('resources.index'));
    }

    #[Test]
    public function professor_pode_acessar_todas_rotas_de_recursos()
    {
        $this->actingAs($this->professor);

        $this->get(route('resources.index'))->assertOk();
        $this->get(route('resources.create'))->assertOk();
        
        $storeData = RecursoDidatico::factory()->make(['id_escola' => $this->professor->id_escola, 'id_usuario_criador' => $this->professor->id_usuario])->toArray();
        $this->post(route('resources.store'), $storeData)->assertRedirect(route('resources.index'));

        $recursoProfessor = RecursoDidatico::factory()->create(['id_usuario_criador' => $this->professor->id_usuario, 'id_escola' => $this->professor->id_escola]);
        $this->get(route('resources.edit', $recursoProfessor))->assertOk();
        
        $updateData = RecursoDidatico::factory()->make(['id_escola' => $this->professor->id_escola, 'id_usuario_criador' => $this->professor->id_usuario])->toArray();
        $this->put(route('resources.update', $recursoProfessor), $updateData)->assertRedirect(route('resources.index'));
        
        $this->delete(route('resources.destroy', $recursoProfessor))->assertRedirect(route('resources.index'));
    }
}