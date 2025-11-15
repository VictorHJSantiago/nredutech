<?php

namespace Tests\Feature\School;

use Tests\TestCase;
use App\Models\Usuario;
use App\Models\Municipio;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

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

        $this->municipio = Municipio::create(['nome' => 'Municipio Teste', 'estado' => 'PR']);

        $this->admin = Usuario::factory()->create(['tipo_usuario' => 'administrador']);
        $this->diretor = Usuario::factory()->create(['tipo_usuario' => 'diretor']);
        $this->professor = Usuario::factory()->create(['tipo_usuario' => 'professor']);
    }

    #[Test]
    public function test_convidado_e_redirecionado_das_rotas_de_municipio()
    {
        $this->post(route('municipios.store'))->assertRedirect(route('login'));
        $this->get(route('municipios.edit', $this->municipio))->assertRedirect(route('login'));
        $this->put(route('municipios.update', $this->municipio))->assertRedirect(route('login'));
        $this->delete(route('municipios.destroy', $this->municipio))->assertRedirect(route('login'));
    }

    #[Test]
    public function test_usuarios_nao_admin_sao_proibidos_nas_rotas_de_municipio()
    {
        $users = [$this->diretor, $this->professor];

        foreach ($users as $user) {
            $this->actingAs($user);

            $this->post(route('municipios.store'), [
                'nome' => 'Cidade Proibida',
            ])->assertForbidden();
            
            $this->get(route('municipios.edit', $this->municipio))->assertForbidden();
            
            $this->put(route('municipios.update', $this->municipio), [
                'nome' => 'Update Proibido',
            ])->assertForbidden();
            
            $this->delete(route('municipios.destroy', $this->municipio))->assertForbidden();
        }
    }

    #[Test]
    public function test_admin_pode_cadastrar_municipio()
    {
        $this->actingAs($this->admin);
        
        $data = [
            'nome' => 'Nova Cidade',
        ];

        $response = $this->post(route('municipios.store'), $data);

        $response->assertRedirect(route('escolas.index'));
        $response->assertSessionHas('success', 'Município adicionado com sucesso!');
        $this->assertDatabaseHas('municipios', $data);
    }

    #[Test]
    public function test_admin_pode_editar_municipio()
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('municipios.edit', $this->municipio));

        $response->assertOk();
        $response->assertViewIs('cities.edit');
        $response->assertViewHas('municipio', $this->municipio);
    }

    #[Test]
    public function test_admin_pode_atualizar_municipio()
    {
        $this->actingAs($this->admin);
        
        $data = [
            'nome' => 'Cidade Atualizada',
        ];

        $response = $this->put(route('municipios.update', $this->municipio), $data);

        $response->assertRedirect(route('escolas.index'));
        $response->assertSessionHas('success', 'Município atualizado com sucesso!');
        $this->assertDatabaseHas('municipios', [
            'id_municipio' => $this->municipio->id_municipio,
            'nome' => 'Cidade Atualizada',
        ]);
    }

    #[Test]
    public function test_admin_pode_excluir_municipio()
    {
        $this->actingAs($this->admin);

        $response = $this->delete(route('municipios.destroy', $this->municipio));

        $response->assertRedirect(route('escolas.index'));
        $response->assertSessionHas('success', 'Município excluído com sucesso!');
        $this->assertDatabaseMissing('municipios', [
            'id_municipio' => $this->municipio->id_municipio,
        ]);
    }
}