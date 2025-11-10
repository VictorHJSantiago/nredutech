<?php

namespace Tests\Feature\DidacticResource;

use Tests\TestCase;
use Illuminate\Support\Facades\Schema;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DidacticResourceDatabaseSchemaTest extends TestCase
{
    use RefreshDatabase;

    public function test_recursos_didaticos_table_has_expected_columns()
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

    public function test_recursos_didaticos_table_has_foreign_keys()
    {
        $foreignKeys = $this->getTableForeignKeys('recursos_didaticos');
        
        $this->assertContains('id_escola', $foreignKeys);
        $this->assertContains('id_usuario_criador', $foreignKeys);
    }

    public function test_recursos_didaticos_table_has_unique_constraint()
    {
        $indices = $this->getTableIndices('recursos_didaticos');
        
        $this->assertArrayHasKey('recursos_didaticos_nome_id_escola_unique', $indices);
        
        $uniqueIndex = $indices['recursos_didaticos_nome_id_escola_unique'];
        
        $this->assertTrue($uniqueIndex['unique']);
        $this->assertCount(2, $uniqueIndex['columns']);
        $this->assertContains('nome', $uniqueIndex['columns']);
        $this->assertContains('id_escola', $uniqueIndex['columns']);
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

    protected function getTableIndices(string $table): array
    {
        $schemaManager = Schema::getConnection()->getDoctrineSchemaManager();
        $indices = $schemaManager->listTableIndexes($table);

        $formattedIndices = [];
        foreach ($indices as $index) {
            $formattedIndices[$index->getName()] = [
                'unique' => $index->isUnique(),
                'primary' => $index->isPrimary(),
                'columns' => $index->getColumns(),
            ];
        }
        return $formattedIndices;
    }
}