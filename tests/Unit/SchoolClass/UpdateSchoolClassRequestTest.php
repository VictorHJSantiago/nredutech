<?php

namespace Tests\Unit\SchoolClass; 

use Tests\TestCase;
use App\Http\Requests\UpdateSchoolClassRequest;
use Illuminate\Support\Facades\Validator;
use App\Models\Escola;
use App\Models\Municipio;
use App\Models\Turma;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\DataProvider;

class UpdateSchoolClassRequestTest extends TestCase
{
    use RefreshDatabase;

    protected $escola;
    protected $turmaParaEditar;

    protected function setUp(): void
    {
        parent::setUp();
        
        $municipio = Municipio::create(['nome' => 'Municipio Teste']);
        
        $this->escola = Escola::create([
            'nome' => 'Escola Teste',
            'id_municipio' => $municipio->id_municipio,
            'nivel_ensino' => 'colegio_estadual',
            'tipo' => 'urbana'
        ]);

        $this->turmaParaEditar = Turma::factory()->create(['id_escola' => $this->escola->id_escola]);
    }

    #[Test]
    public function validacao_passa_atualizando_apenas_serie()
    {
        $request = new UpdateSchoolClassRequest();
        $request->setRouteResolver(fn () => \Illuminate\Support\Facades\Route::put('turmas/{turma}', fn (Turma $turma) => $turma)
            ->bind('turma', $this->turmaParaEditar));

        $dados = ['serie' => 'Nova Série 99'];
        $validator = Validator::make($dados, $request->rules());
        $this->assertFalse($validator->fails());
    }

    #[Test]
    public function validacao_passa_atualizando_apenas_ano_letivo()
    {
        $request = new UpdateSchoolClassRequest();
        $request->setRouteResolver(fn () => \Illuminate\Support\Facades\Route::put('turmas/{turma}', fn (Turma $turma) => $turma)
            ->bind('turma', $this->turmaParaEditar));

        $dados = ['ano_letivo' => date('Y') + 1];

        $validator = Validator::make($dados, $request->rules());

        $this->assertFalse($validator->fails());
    }

    #[Test]
    #[DataProvider('invalidUpdateDataProvider')]
    public function validacao_falha_se_campo_enviado_for_invalido(array $dadosInvalidos)
    {
        $request = new UpdateSchoolClassRequest();
        $request->setRouteResolver(fn () => \Illuminate\Support\Facades\Route::put('turmas/{turma}', fn (Turma $turma) => $turma)
            ->bind('turma', $this->turmaParaEditar));

        $validator = Validator::make($dadosInvalidos, $request->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey(key($dadosInvalidos), $validator->errors()->toArray());
    }

    #[Test]
    public function validacao_passa_com_todos_campos_validos()
    {
        $request = new UpdateSchoolClassRequest();
        $request->setRouteResolver(fn () => \Illuminate\Support\Facades\Route::put('turmas/{turma}', fn (Turma $turma) => $turma)
            ->bind('turma', $this->turmaParaEditar));
        
        $outraEscola = Escola::create([
            'nome' => 'Outra Escola',
            'id_municipio' => $this->escola->id_municipio,
            'nivel_ensino' => 'escola_municipal',
            'tipo' => 'rural'
        ]);

        $dados = [
            'serie' => 'Turma Completa Editada',
            'turno' => 'noite',
            'ano_letivo' => 2026,
            'nivel_escolaridade' => 'fundamental_1',
            'id_escola' => $outraEscola->id_escola,
        ];
        $validator = Validator::make($dados, $request->rules());
        $this->assertFalse($validator->fails());
    }

    public static function invalidUpdateDataProvider(): array
    {
        return [
            'serie vazia' => [['serie' => '']], 
            'turno invalido' => [['turno' => 'madrugada']],
            'ano letivo invalido' => [['ano_letivo' => 'ano_passado']],
            'nivel invalido' => [['nivel_escolaridade' => 'jardim_infancia']],
            'escola inexistente' => [['id_escola' => 99999]],
            'serie muito longa' => [['serie' => str_repeat('S', 256)]],
        ];
    }
}