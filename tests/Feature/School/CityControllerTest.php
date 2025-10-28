<?php

namespace Tests\Feature\School; 

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Usuario;
use App\Models\Municipio;
use App\Models\Escola;

class CityControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = Usuario::factory()->create(['tipo_usuario' => 'administrador']);
    }

    /** @test */
    public function administrador_pode_criar_municipio_com_sucesso() 
    {
        $nomeMunicipio = 'Nova Cidade';
        $response = $this->actingAs($this->admin)->post(route('municipios.store'), ['nome' => $nomeMunicipio]);

        $response->assertRedirect(route('escolas.index'));
        $response->assertSessionHasNoErrors();
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('municipios', ['nome' => $nomeMunicipio]);
    }

    /** @test */
    public function store_municipio_falha_sem_nome() 
    {
        $response = $this->actingAs($this->admin)->post(route('municipios.store'), ['nome' => '']);
        $response->assertSessionHasErrors('nome');
        $this->assertDatabaseCount('municipios', 0);
    }

     /** @test */
    public function store_municipio_falha_com_nome_muito_longo()
    {
        $response = $this->actingAs($this->admin)->post(route('municipios.store'), ['nome' => str_repeat('A', 256)]);
        $response->assertSessionHasErrors('nome');
    }

    /** @test */
    public function administrador_pode_editar_municipio_com_sucesso() 
    {
        $municipio = Municipio::factory()->create();
        $novoNome = 'Cidade Super Atualizada';
        $response = $this->actingAs($this->admin)->put(route('municipios.update', $municipio), ['nome' => $novoNome]);

        $response->assertRedirect(route('escolas.index'));
         $response->assertSessionHasNoErrors();
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('municipios', ['id_municipio' => $municipio->id_municipio, 'nome' => $novoNome]);
    }

    /** @test */
    public function update_municipio_falha_sem_nome()
    {
        $municipio = Municipio::factory()->create(['nome' => 'Nome Antigo']);
        $response = $this->actingAs($this->admin)->put(route('municipios.update', $municipio), ['nome' => '']); 
        $response->assertSessionHasErrors('nome');
        $this->assertDatabaseHas('municipios', ['id_municipio' => $municipio->id_municipio, 'nome' => 'Nome Antigo']); 
    }

     /** @test */
    public function update_municipio_falha_com_nome_muito_longo()
    {
        $municipio = Municipio::factory()->create(['nome' => 'Nome Antigo']);
        $response = $this->actingAs($this->admin)->put(route('municipios.update', $municipio), ['nome' => str_repeat('B', 256)]);
        $response->assertSessionHasErrors('nome');
        $this->assertDatabaseHas('municipios', ['id_municipio' => $municipio->id_municipio, 'nome' => 'Nome Antigo']);
    }

     /** @test */
    public function administrador_pode_excluir_municipio_sem_escolas() 
    {
        $municipio = Municipio::factory()->create();
        $response = $this->actingAs($this->admin)->delete(route('municipios.destroy', $municipio));
        $response->assertRedirect(route('escolas.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('municipios', ['id_municipio' => $municipio->id_municipio]);
    }

    /** @test */
    public function administrador_nao_pode_excluir_municipio_com_escolas() 
    {
        $municipio = Municipio::factory()->create();
        Escola::factory()->create(['id_municipio' => $municipio->id_municipio]);
        $response = $this->actingAs($this->admin)->delete(route('municipios.destroy', $municipio));
        $response->assertRedirect(route('escolas.index'));
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('municipios', ['id_municipio' => $municipio->id_municipio]);
    }
}