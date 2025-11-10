<?php

namespace Tests\Feature\Disciplines;

use Tests\TestCase;
use Illuminate\Support\Facades\Schema;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DisciplineDatabaseSchemaTest extends TestCase
{
    use RefreshDatabase;

    public function test_componentes_curriculares_table_has_expected_columns()
    {
        $this->assertTrue(Schema::hasTable('componentes_curriculares'));

        $this->assertTrue(Schema::hasColumns('componentes_curriculares', [
            'id_componente_curricular',
            'nome',
            'id_escola',
        ]));
    }

    public function test_componentes_curriculares_table_has_foreign_key_to_escolas()
    {
        $foreignKeys = $this->getTableForeignKeys('componentes_curriculares');
        
        $this->assertContains('id_escola', $foreignKeys);
    }
    
    public function test_componentes_curriculares_table_has_unique_constraint()
    {
        $indices = $this->getTableIndices('componentes_curriculares');
        
        $this->assertArrayHasKey('componentes_curriculares_nome_id_escola_unique', $indices);
        
        $uniqueIndex = $indices['componentes_curriculares_nome_id_escola_unique'];
        
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