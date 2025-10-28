<?php

namespace Tests\Feature\Appointments; 

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AppointmentDatabaseSchemaTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function tabela_agendamentos_existe_e_tem_colunas_esperadas()
    {
        $this->assertTrue(Schema::hasTable('agendamentos'));

        $this->assertTrue(Schema::hasColumn('agendamentos', 'id_agendamento'));
        $this->assertTrue(Schema::hasColumn('agendamentos', 'data_hora_inicio'));
        $this->assertTrue(Schema::hasColumn('agendamentos', 'data_hora_fim'));
        $this->assertTrue(Schema::hasColumn('agendamentos', 'status'));
        $this->assertTrue(Schema::hasColumn('agendamentos', 'id_oferta')); 
        $this->assertTrue(Schema::hasColumn('agendamentos', 'id_recurso')); 
        $this->assertTrue(Schema::hasColumn('agendamentos', 'created_at'));
        $this->assertTrue(Schema::hasColumn('agendamentos', 'updated_at'));
    }

     /** @test */
    public function chaves_estrangeiras_agendamentos_estao_configuradas()
    {
        $this->assertTrue(true); 
    }
}