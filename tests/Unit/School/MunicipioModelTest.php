<?php

namespace Tests\Unit\School;

use Tests\TestCase;
use App\Models\Municipio;
use App\Models\Escola;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MunicipioModelTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function municipio_pode_ter_muitas_escolas()
    {
        $municipio = Municipio::factory()->create();
        Escola::factory(3)->create(['id_municipio' => $municipio->id_municipio]);
        $outroMunicipio = Municipio::factory()->create();
        Escola::factory()->create(['id_municipio' => $outroMunicipio->id_municipio]);

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $municipio->escolas);
        $this->assertCount(3, $municipio->escolas);
    }

    /** @test */
    public function atributos_fillable_estao_corretos()
    {
        $municipio = new Municipio();
        $expected = ['nome'];
        $this->assertEquals($expected, $municipio->getFillable());
    }

     /** @test */
    public function chave_primaria_e_id_municipio()
    {
        $municipio = new Municipio();
        $this->assertEquals('id_municipio', $municipio->getKeyName());
    }

     /** @test */
    public function timestamps_nao_sao_usados() 
    {
        $municipio = new Municipio();
        $this->assertFalse($municipio->usesTimestamps());
    }
}