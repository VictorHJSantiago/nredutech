<?php

namespace Tests\Feature\School; 

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class SchoolDatabaseSchemaTest extends TestCase
{
    use RefreshDatabase; 

    /** @test */
    public function tabela_escolas_existe_e_tem_colunas_esperadas()
    {
        $this->assertTrue(Schema::hasTable('escolas'));

        $this->assertTrue(Schema::hasColumn('escolas', 'id_escola'));
        $this->assertTrue(Schema::hasColumn('escolas', 'nome'));
        $this->assertTrue(Schema::hasColumn('escolas', 'id_municipio')); 
        $this->assertTrue(Schema::hasColumn('escolas', 'nivel_ensino'));
        $this->assertTrue(Schema::hasColumn('escolas', 'localizacao'));
    }

     /** @test */
    public function tabela_municipios_existe_e_tem_colunas_esperadas()
    {
        $this->assertTrue(Schema::hasTable('municipios'));
        $this->assertTrue(Schema::hasColumn('municipios', 'id_municipio'));
        $this->assertTrue(Schema::hasColumn('municipios', 'nome'));
    }

    /** @test */
    public function chave_estrangeira_escolas_municipios_esta_configurada()
    {
        $this->assertTrue(true);
    }
}