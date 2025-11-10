<?php

namespace Tests\Feature\CourseOffering;

use Tests\TestCase;
use Illuminate\Support\Facades\Schema;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CourseOfferingDatabaseSchemaTest extends TestCase
{
    use RefreshDatabase;

    public function test_oferta_componentes_table_has_expected_columns()
    {
        $this->assertTrue(Schema::hasTable('oferta_componentes'));

        $this->assertTrue(Schema::hasColumns('oferta_componentes', [
            'id_oferta',
            'id_turma',
            'id_componente_curricular',
            'id_professor',
        ]));
    }

    public function test_oferta_componentes_table_has_foreign_keys()
    {
        $foreignKeys = $this->getTableForeignKeys('oferta_componentes');
        
        $this->assertContains('id_turma', $foreignKeys);
        $this->assertContains('id_componente_curricular', $foreignKeys);
        $this->assertContains('id_professor', $foreignKeys);
    }

    public function test_oferta_componentes_table_has_unique_constraint()
    {
        $indices = $this->getTableIndices('oferta_componentes');
        
        $this->assertArrayHasKey('oferta_componentes_id_turma_id_componente_curricular_id_professor_unique', $indices);
        
        $uniqueIndex = $indices['oferta_componentes_id_turma_id_componente_curricular_id_professor_unique'];
        
        $this->assertTrue($uniqueIndex['unique']);
        $this->assertCount(3, $uniqueIndex['columns']);
        $this->assertContains('id_turma', $uniqueIndex['columns']);
        $this->assertContains('id_componente_curricular', $uniqueIndex['columns']);
        $this->assertContains('id_professor', $uniqueIndex['columns']);
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
        $dbDriver = Schema::getConnection()->getDriverName();
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