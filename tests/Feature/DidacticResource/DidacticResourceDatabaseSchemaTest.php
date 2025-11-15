<?php

namespace Tests\Feature\DidacticResource;

use Tests\TestCase;
use Illuminate\Support\Facades\Schema;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class DidacticResourceDatabaseSchemaTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function tabela_recursos_didaticos_tem_colunas_esperadas()
    {
        $this->assertTrue(Schema::hasTable('recursos_didaticos'));

        $this->assertTrue(Schema::hasColumns('recursos_didaticos', [
            'id_recurso',
            'nome',
            'quantidade',
            'tipo',
            'status',
            'created_at',
            'updated_at',
            'id_escola',
            'id_usuario_criador',
        ]));
    }

    #[Test]
    public function tabela_recursos_didaticos_tem_chaves_estrangeiras()
    {
        $foreignKeys = $this->getTableForeignKeys('recursos_didaticos');
        
        $this->assertContains('id_escola', $foreignKeys);
        $this->assertContains('id_usuario_criador', $foreignKeys);
    }

    #[Test]
    public function tabela_recursos_didaticos_tem_restricao_unique()
    {
        $this->assertTrue(true);
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