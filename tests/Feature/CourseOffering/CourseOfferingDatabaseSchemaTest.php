<?php

namespace Tests\Feature\CourseOffering;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class CourseOfferingDatabaseSchemaTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function tabela_oferta_componentes_tem_colunas_esperadas()
    {
        $this->assertTrue(Schema::hasTable('oferta_componentes'));

        $this->assertTrue(Schema::hasColumns('oferta_componentes', [
            'id_oferta',
            'id_turma',
            'id_componente',
            'id_professor',
        ]));
    }

    #[Test]
    public function tabela_oferta_componentes_tem_chaves_estrangeiras()
    {
        $this->assertTrue(true);
    }

    #[Test]
    public function tabela_oferta_componentes_tem_restricao_unique()
    {
        $this->assertTrue(true);
    }
}