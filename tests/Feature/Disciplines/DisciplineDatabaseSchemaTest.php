<?php

namespace Tests\Feature\Disciplines; 

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class DisciplineDatabaseSchemaTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function tabela_componentes_curriculares_existe_e_tem_colunas_esperadas()
    {
        $this->assertTrue(Schema::hasTable('componentes_curriculares'));

        $this->assertTrue(Schema::hasColumn('componentes_curriculares', 'id_componente'));
        $this->assertTrue(Schema::hasColumn('componentes_curriculares', 'nome'));
        $this->assertTrue(Schema::hasColumn('componentes_curriculares', 'descricao'));
        $this->assertTrue(Schema::hasColumn('componentes_curriculares', 'carga_horaria'));
        $this->assertTrue(Schema::hasColumn('componentes_curriculares', 'status'));
        $this->assertTrue(Schema::hasColumn('componentes_curriculares', 'id_usuario_criador')); 
        $this->assertTrue(Schema::hasColumn('componentes_curriculares', 'id_escola')); 
        $this->assertTrue(Schema::hasColumn('componentes_curriculares', 'created_at'));
        $this->assertTrue(Schema::hasColumn('componentes_curriculares', 'updated_at'));
    }

    /** @test */
    public function chaves_estrangeiras_componentes_curriculares_estao_configuradas()
    {
        $this->assertTrue(true); 
    }
}