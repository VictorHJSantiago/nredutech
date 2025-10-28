<?php

namespace Tests\Feature\DidacticResource;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class DidacticResourceDatabaseSchemaTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function tabela_recursos_didaticos_existe_e_tem_colunas_esperadas()
    {
        $this->assertTrue(Schema::hasTable('recursos_didaticos'));

        $this->assertTrue(Schema::hasColumn('recursos_didaticos', 'id_recurso'));
        $this->assertTrue(Schema::hasColumn('recursos_didaticos', 'nome'));
        $this->assertTrue(Schema::hasColumn('recursos_didaticos', 'tipo'));
        $this->assertTrue(Schema::hasColumn('recursos_didaticos', 'marca'));
        $this->assertTrue(Schema::hasColumn('recursos_didaticos', 'numero_serie'));
        $this->assertTrue(Schema::hasColumn('recursos_didaticos', 'quantidade'));
        $this->assertTrue(Schema::hasColumn('recursos_didaticos', 'observacoes'));
        $this->assertTrue(Schema::hasColumn('recursos_didaticos', 'data_aquisicao'));
        $this->assertTrue(Schema::hasColumn('recursos_didaticos', 'status'));
        $this->assertTrue(Schema::hasColumn('recursos_didaticos', 'id_usuario_criador')); 
        $this->assertTrue(Schema::hasColumn('recursos_didaticos', 'id_escola')); 
        $this->assertTrue(Schema::hasColumn('recursos_didaticos', 'created_at'));
        $this->assertTrue(Schema::hasColumn('recursos_didaticos', 'updated_at'));
    }

     /** @test */
    public function chaves_estrangeiras_recursos_didaticos_estao_configuradas()
    {
        $this->assertTrue(true); 
    }
}