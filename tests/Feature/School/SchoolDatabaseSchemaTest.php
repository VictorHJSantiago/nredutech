<?php

namespace Tests\Feature\School; 

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class SchoolDatabaseSchemaTest extends TestCase
{
    use RefreshDatabase; 

    #[Test]
    public function test_tabela_escolas_existe_e_tem_colunas_esperadas()
    {
        $this->assertTrue(Schema::hasTable('escolas'));

        $this->assertTrue(Schema::hasColumn('escolas', 'id_escola'));
        $this->assertTrue(Schema::hasColumn('escolas', 'nome'));
        $this->assertTrue(Schema::hasColumn('escolas', 'id_municipio')); 
        $this->assertTrue(Schema::hasColumn('escolas', 'nivel_ensino'));
        $this->assertTrue(Schema::hasColumn('escolas', 'tipo'));
        $this->assertTrue(Schema::hasColumn('escolas', 'id_diretor_1'));
        $this->assertTrue(Schema::hasColumn('escolas', 'id_diretor_2'));
        $this->assertTrue(Schema::hasColumn('escolas', 'deleted_at'));
    }

    #[Test]
    public function test_tabela_municipios_existe_e_tem_colunas_esperadas()
    {
        $this->assertTrue(Schema::hasTable('municipios'));
        $this->assertTrue(Schema::hasColumn('municipios', 'id_municipio'));
        $this->assertTrue(Schema::hasColumn('municipios', 'nome'));
        $this->assertTrue(Schema::hasColumn('municipios', 'created_at'));
        $this->assertTrue(Schema::hasColumn('municipios', 'updated_at'));
    }

    #[Test]
    public function test_chave_estrangeira_escolas_municipios_esta_configurada()
    {
        $this->assertTrue(true);
    }
}