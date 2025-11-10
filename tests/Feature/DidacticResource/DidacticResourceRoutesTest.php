<?php

namespace Tests\Feature\DidacticResource;

use Tests\TestCase;
use App\Models\Usuario;
use App\Models\RecursoDidatico;
use Illuminate\Foundation\Testing\RefreshDatabase;

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

        $this->admin = Usuario::factory()->administrador()->create();
        $this->diretor = Usuario::factory()->diretor()->create();
        $this->professor = Usuario::factory()->professor()->create();
        $this->recurso = RecursoDidatico::factory()->create(['id_usuario_criador' => $this->admin->id_usuario]);
    }

    public function test_guest_is_redirected_from_all_resource_routes()
    {
        $this->get(route('resources.index'))->assertRedirect(route('login'));
        $this->get(route('resources.create'))->assertRedirect(route('login'));
        $this->post(route('resources.store'))->assertRedirect(route('login'));
        $this->get(route('resources.edit', $this->recurso))->assertRedirect(route('login'));
        $this->put(route('resources.update', $this->recurso))->assertRedirect(route('login'));
        $this->delete(route('resources.destroy', $this->recurso))->assertRedirect(route('login'));
    }

    public function test_admin_can_access_all_resource_routes()
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

    public function test_diretor_can_access_all_resource_routes()
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

    public function test_professor_can_access_all_resource_routes()
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