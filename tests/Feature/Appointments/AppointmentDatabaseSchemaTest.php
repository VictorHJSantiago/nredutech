<?php

namespace Tests\Feature\Appointments;

use Tests\TestCase;
use Illuminate\Support\Facades\Schema;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AppointmentDatabaseSchemaTest extends TestCase
{
    use RefreshDatabase;

    public function test_agendamentos_table_has_expected_columns()
    {
        $this->assertTrue(Schema::hasTable('agendamentos'));

        $this->assertTrue(Schema::hasColumns('agendamentos', [
            'id_agendamento',
            'data_hora_inicio',
            'data_hora_fim',
            'status',
            'created_at',
            'updated_at',
            'id_recurso',
            'id_oferta',
            'id_usuario',
        ]));
    }

    public function test_agendamentos_table_has_foreign_keys()
    {
        $foreignKeys = $this->getTableForeignKeys('agendamentos');
        
        $this->assertContains('id_recurso', $foreignKeys);
        $this->assertContains('id_oferta', $foreignKeys);
        $this->assertContains('id_usuario', $foreignKeys);
    }

    protected function getTableForeignKeys(string $table): array
    {
        $dbDriver = Schema::getConnection()->getDriverName();
        
        if ($dbDriver === 'sqlite') {
            $foreignKeysData = Schema::getConnection()->select("PRAGMA foreign_key_list($table)");
            return array_column($foreignKeysData, 'from');
        }

        if ($dbDriver === 'mysql') {
            $foreignKeysData = Schema::getConnection()->select(
                "SELECT COLUMN_NAME 
                FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
                WHERE TABLE_SCHEMA = DATABASE() 
                AND TABLE_NAME = '$table' 
                AND REFERENCED_TABLE_NAME IS NOT NULL"
            );
            return array_column($foreignKeysData, 'COLUMN_NAME');
        }

        return [];
    }
}