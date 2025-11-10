<?php

namespace Tests\Unit\School;

use Tests\TestCase;
use App\Models\Municipio;
use App\Models\Escola;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class MunicipioModelTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function municipio_pode_ter_muitas_escolas()
    {
        $municipio = Municipio::create(['nome' => 'Municipio A', 'estado' => 'PR']);
        $escolaData = [
            'nome' => 'Escola Teste',
            'id_municipio' => $municipio->id_municipio,
            'nivel_ensino' => 'colegio_estadual',
            'tipo' => 'urbana'
        ];
        
        Escola::create($escolaData);
        Escola::create(array_merge($escolaData, ['nome' => 'Escola 2']));
        Escola::create(array_merge($escolaData, ['nome' => 'Escola 3']));

        $outroMunicipio = Municipio::create(['nome' => 'Municipio B', 'estado' => 'SC']);
        Escola::create(array_merge($escolaData, ['nome' => 'Escola 4', 'id_municipio' => $outroMunicipio->id_municipio]));

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $municipio->escolas);
        $this->assertCount(3, $municipio->escolas);
    }

    #[Test]
    public function atributos_fillable_estao_corretos()
    {
        $municipio = new Municipio();
        $expected = ['nome']; 
        $this->assertEquals($expected, $municipio->getFillable());
    }

    #[Test]
    public function chave_primaria_e_id_municipio()
    {
        $municipio = new Municipio();
        $this->assertEquals('id_municipio', $municipio->getKeyName());
    }

    #[Test]
    public function timestamps_sao_usados() 
    {
        $municipio = new Municipio();
        $this->assertTrue($municipio->usesTimestamps());
    }
}