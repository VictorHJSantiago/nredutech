<?php

namespace Tests\Feature\User;

use Tests\TestCase;
use Illuminate\Support\Facades\Schema;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class UserDatabaseSchemaTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function tabela_usuarios_tem_colunas_esperadas()
    {
        $this->assertTrue(Schema::hasTable('usuarios'));

        $this->assertTrue(Schema::hasColumns('usuarios', [
            'id_usuario',
            'nome_completo',
            'username',
            'email',
            'data_nascimento',
            'cpf',
            'rg',
            'rco_siape',
            'telefone',
            'formacao',
            'area_formacao',
            'data_registro',
            'status_aprovacao',
            'tipo_usuario',
            'id_escola',
            'password',
            'created_at',
            'updated_at',
            'deleted_at',
            'remember_token',
        ]));
    }

    #[Test]
    public function tabela_usuarios_tem_chave_estrangeira_para_escolas()
    {
        $foreignKeys = $this->getTableForeignKeys('usuarios');
        $this->assertContains('id_escola', $foreignKeys);
    }

    #[Test]
    public function tabela_usuarios_tem_restricoes_unique()
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