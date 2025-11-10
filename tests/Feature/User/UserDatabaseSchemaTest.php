<?php

namespace Tests\Feature\User;

use Tests\TestCase;
use Illuminate\Support\Facades\Schema;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserDatabaseSchemaTest extends TestCase
{
    use RefreshDatabase;

    public function test_usuarios_table_has_expected_columns()
    {
        $this->assertTrue(Schema::hasTable('usuarios'));

        $this->assertTrue(Schema::hasColumns('usuarios', [
            'id_usuario',
            'nome_completo',
            'username',
            'email',
            'email_verified_at',
            'password',
            'remember_token',
            'tipo_usuario',
            'status_aprovacao',
            'data_registro',
            'data_nascimento',
            'cpf',
            'rg',
            'telefone',
            'rco_siape',
            'formacao',
            'area_formacao',
            'id_escola',
        ]));
    }

    public function test_usuarios_table_has_foreign_key_to_escolas()
    {
        $foreignKeys = $this->getTableForeignKeys('usuarios');
        $this->assertContains('id_escola', $foreignKeys);
    }

    public function test_usuarios_table_has_unique_constraints()
    {
        $indices = $this->getTableIndices('usuarios');
        
        $this->assertArrayHasKey('usuarios_username_unique', $indices);
        $this->assertTrue($indices['usuarios_username_unique']['unique']);
        
        $this->assertArrayHasKey('usuarios_email_unique', $indices);
        $this->assertTrue($indices['usuarios_email_unique']['unique']);
        
        $this->assertArrayHasKey('usuarios_cpf_unique', $indices);
        $this->assertTrue($indices['usuarios_cpf_unique']['unique']);
        
        $this->assertArrayHasKey('usuarios_rg_unique', $indices);
        $this->assertTrue($indices['usuarios_rg_unique']['unique']);
        
        $this->assertArrayHasKey('usuarios_rco_siape_unique', $indices);
        $this->assertTrue($indices['usuarios_rco_siape_unique']['unique']);
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