<?php

namespace Tests\Feature\School;

use Tests\TestCase;
use App\Models\Usuario;
use App\Models\Municipio;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CityControllerTest extends TestCase
{
    use RefreshDatabase;

    private Usuario $admin;
    private Usuario $diretor;
    private Usuario $professor;
    private Municipio $municipio;

    protected function setUp(): void
    {
        parent::setUp();

        $this->municipio = Municipio::factory()->create();

        $this->admin = Usuario::factory()->create(['tipo_usuario' => 'administrador']);
        $this->diretor = Usuario::factory()->create(['tipo_usuario' => 'diretor']);
        $this->professor = Usuario::factory()->create(['tipo_usuario' => 'professor']);
    }

    public function test_guest_is_redirected_from_city_routes()
    {
        $this->post(route('municipios.store'))->assertRedirect(route('login'));
        $this->get(route('municipios.edit', $this->municipio))->assertRedirect(route('login'));
        $this->put(route('municipios.update', $this->municipio))->assertRedirect(route('login'));
        $this->delete(route('municipios.destroy', $this->municipio))->assertRedirect(route('login'));
    }

    public function test_non_admin_users_are_forbidden_from_city_routes()
    {
        $users = [$this->diretor, $this->professor];

        foreach ($users as $user) {
            $this->actingAs($user);

            $this->post(route('municipios.store'), [
                'nome' => 'Cidade Proibida',
                'estado' => 'ER'
            ])->assertForbidden();
            
            $this->get(route('municipios.edit', $this->municipio))->assertForbidden();
            
            $this->put(route('municipios.update', $this->municipio), [
                'nome' => 'Update Proibido',
                'estado' => 'ER'
            ])->assertForbidden();
            
            $this->delete(route('municipios.destroy', $this->municipio))->assertForbidden();
        }
    }

    public function test_admin_can_store_municipio()
    {
        $this->actingAs($this->admin);
        
        $data = [
            'nome' => 'Nova Cidade',
            'estado' => 'SP',
        ];

        $response = $this->post(route('municipios.store'), $data);

        $response->assertRedirect(route('settings'));
        $response->assertSessionHas('success', 'Município criado com sucesso!');
        $this->assertDatabaseHas('municipios', $data);
    }

    public function test_admin_can_edit_municipio()
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('municipios.edit', $this->municipio));

        $response->assertOk();
        $response->assertViewIs('cities.edit');
        $response->assertViewHas('municipio', $this->municipio);
    }

    public function test_admin_can_update_municipio()
    {
        $this->actingAs($this->admin);
        
        $data = [
            'nome' => 'Cidade Atualizada',
            'estado' => 'RJ',
        ];

        $response = $this->put(route('municipios.update', $this->municipio), $data);

        $response->assertRedirect(route('settings'));
        $response->assertSessionHas('success', 'Município atualizado com sucesso!');
        $this->assertDatabaseHas('municipios', [
            'id_municipio' => $this->municipio->id_municipio,
            'nome' => 'Cidade Atualizada',
            'estado' => 'RJ',
        ]);
    }

    public function test_admin_can_destroy_municipio()
    {
        $this->actingAs($this->admin);

        $response = $this->delete(route('municipios.destroy', $this->municipio));

        $response->assertRedirect(route('settings'));
        $response->assertSessionHas('success', 'Município excluído com sucesso!');
        $this->assertDatabaseMissing('municipios', [
            'id_municipio' => $this->municipio->id_municipio,
        ]);
    }
}