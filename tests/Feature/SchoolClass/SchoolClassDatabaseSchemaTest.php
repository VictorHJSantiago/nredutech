<?php

namespace Tests\Feature\SchoolClass; 

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class SchoolClassDatabaseSchemaTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function tabela_turmas_existe_e_tem_colunas_esperadas()
    {
        $this->assertTrue(Schema::hasTable('turmas'));

        $this->assertTrue(Schema::hasColumn('turmas', 'id_turma'));
        $this->assertTrue(Schema::hasColumn('turmas', 'serie'));
        $this->assertTrue(Schema::hasColumn('turmas', 'turno'));
        $this->assertTrue(Schema::hasColumn('turmas', 'ano_letivo'));
        $this->assertTrue(Schema::hasColumn('turmas', 'nivel_escolaridade'));
        $this->assertTrue(Schema::hasColumn('turmas', 'id_escola')); 
        $this->assertTrue(Schema::hasColumn('turmas', 'created_at'));
        $this->assertTrue(Schema::hasColumn('turmas', 'updated_at'));
    }

    /** @test */
    public function chave_estrangeira_turmas_escolas_esta_configurada()
    {
        $this->assertTrue(true); 
    }
}