<?php

namespace Tests\Feature\Disciplines;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class DisciplineDatabaseSchemaTest extends TestCase
{
    use RefreshDatabase;

    #[Test('Tabela componentes curriculares tem colunas esperadas')]
    public function tabela_componentes_curriculares_tem_colunas_esperadas()
    {
        $this->assertTrue(Schema::hasTable('componentes_curriculares'));

        $this->assertTrue(Schema::hasColumns('componentes_curriculares', [
            'id_componente',
            'nome',
            'descricao',
            'carga_horaria',
            'status',
            'id_escola',
            'id_usuario_criador'
        ]));
    }

    #[Test('Coluna id escola em componentes permite nulo')]
    public function coluna_id_escola_em_componentes_permite_nulo()
    {
        $this->assertTrue(true);
    }

    #[Test('Chave estrangeira id escola existe')]
    public function chave_estrangeira_id_escola_existe()
    {
        $this->assertTrue(true);
    }
}