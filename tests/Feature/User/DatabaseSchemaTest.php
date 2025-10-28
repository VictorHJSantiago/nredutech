<?php

namespace Tests\Feature\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class DatabaseSchemaTest extends TestCase
{
    use RefreshDatabase; 

    /** @test */
    public function tabela_usuarios_existe_e_tem_colunas_esperadas()
    {
        $this->assertTrue(Schema::hasTable('usuarios'));
        $this->assertTrue(Schema::hasColumn('usuarios', 'id_usuario'));
        $this->assertTrue(Schema::hasColumn('usuarios', 'nome_completo'));
        $this->assertTrue(Schema::hasColumn('usuarios', 'email'));
        $this->assertTrue(Schema::hasColumn('usuarios', 'password'));
        $this->assertTrue(Schema::hasColumn('usuarios', 'tipo_usuario'));
        $this->assertTrue(Schema::hasColumn('usuarios', 'status_aprovacao'));
        $this->assertTrue(Schema::hasColumn('usuarios', 'id_escola')); 
    }
}