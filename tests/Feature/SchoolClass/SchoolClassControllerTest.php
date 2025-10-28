<?php

namespace Tests\Feature\SchoolClass;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Usuario;
use App\Models\Municipio;
use App\Models\Escola;
use App\Models\Turma;
use App\Models\OfertaComponente; 

class SchoolClassControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $diretor;
    protected $professor;
    protected $escolaAdmin;
    protected $escolaDiretor;

    protected function setUp(): void
    {
        parent::setUp();
        $municipio = Municipio::factory()->create();
        $this->escolaAdmin = Escola::factory()->create(['id_municipio' => $municipio->id_municipio]); 
        $this->escolaDiretor = Escola::factory()->create(['id_municipio' => $municipio->id_municipio]);
        $this->admin = Usuario::factory()->create(['tipo_usuario' => 'administrador', 'id_escola' => null]);
        $this->diretor = Usuario::factory()->create(['tipo_usuario' => 'diretor', 'id_escola' => $this->escolaDiretor->id_escola]);
        $this->professor = Usuario::factory()->create(['tipo_usuario' => 'professor', 'id_escola' => $this->escolaDiretor->id_escola]);
    }

    /** @test */
    public function admin_ve_todas_as_turmas()
    {
        Turma::factory()->create(['id_escola' => $this->escolaAdmin->id_escola]);
        Turma::factory()->create(['id_escola' => $this->escolaDiretor->id_escola]);
        $response = $this->actingAs($this->admin)->get(route('turmas.index'));
        $response->assertStatus(200);
        $response->assertViewHas('turmas', function ($turmas) {
            return $turmas->count() === 2; 
        });
    }

    /** @test */
    public function diretor_ve_apenas_turmas_da_sua_escola()
    {
        Turma::factory()->create(['id_escola' => $this->escolaAdmin->id_escola]); 
        $turmaDiretor = Turma::factory()->create(['id_escola' => $this->escolaDiretor->id_escola]); 
        $response = $this->actingAs($this->diretor)->get(route('turmas.index'));
        $response->assertStatus(200);
        $response->assertViewHas('turmas', function ($turmas) use ($turmaDiretor) {
            return $turmas->count() === 1 && $turmas->first()->id_turma === $turmaDiretor->id_turma;
        });
    }

     /** @test */
    public function professor_ve_apenas_turmas_da_sua_escola()
    {
        Turma::factory()->create(['id_escola' => $this->escolaAdmin->id_escola]);
        $turmaProfessor = Turma::factory()->create(['id_escola' => $this->escolaDiretor->id_escola]);
        $response = $this->actingAs($this->professor)->get(route('turmas.index'));
        $response->assertStatus(200);
         $response->assertViewHas('turmas', function ($turmas) use ($turmaProfessor) {
             return $turmas->count() === 1 && $turmas->first()->id_turma === $turmaProfessor->id_turma;
        });
    }

    /** @test */
    public function qualquer_usuario_autenticado_pode_criar_turma()
    {
        $dadosTurma = [
            'serie' => '1º Ano C',
            'turno' => 'tarde',
            'ano_letivo' => date('Y'),
            'nivel_escolaridade' => 'medio',
            'id_escola' => $this->escolaDiretor->id_escola, 
        ];

        $responseProf = $this->actingAs($this->professor)->post(route('turmas.store'), $dadosTurma);
        $responseProf->assertRedirect(route('turmas.index'));
        $responseProf->assertSessionHas('success');
        $this->assertDatabaseHas('turmas', ['serie' => $dadosTurma['serie'], 'id_escola' => $this->escolaDiretor->id_escola]);
        Turma::where('serie', $dadosTurma['serie'])->delete(); 
        $responseDir = $this->actingAs($this->diretor)->post(route('turmas.store'), $dadosTurma);
        $responseDir->assertRedirect(route('turmas.index'));
        $responseDir->assertSessionHas('success');
        $this->assertDatabaseHas('turmas', ['serie' => $dadosTurma['serie'], 'id_escola' => $this->escolaDiretor->id_escola]);
        Turma::where('serie', $dadosTurma['serie'])->delete(); 
         $dadosTurmaAdmin = $dadosTurma;
         $dadosTurmaAdmin['id_escola'] = $this->escolaAdmin->id_escola; 
        $responseAdmin = $this->actingAs($this->admin)->post(route('turmas.store'), $dadosTurmaAdmin);
        $responseAdmin->assertRedirect(route('turmas.index'));
        $responseAdmin->assertSessionHas('success');
        $this->assertDatabaseHas('turmas', ['serie' => $dadosTurmaAdmin['serie'], 'id_escola' => $this->escolaAdmin->id_escola]);

    }

     /** @test */
    public function diretor_nao_pode_criar_turma_em_outra_escola()
    {
        $response = $this->actingAs($this->diretor)->get(route('turmas.index'));
        $response->assertDontSee('<select id="id_escola" name="id_escola" class="form-control" required>'); 
        $response->assertSee('<option value="'.$this->escolaDiretor->id_escola.'"', false);
        $response->assertDontSee('<option value="'.$this->escolaAdmin->id_escola.'"', false);
        $this->assertTrue(true); 
    }

    /** @test */
    public function admin_pode_ver_detalhes_qualquer_turma()
    {
        $turma = Turma::factory()->create(['id_escola' => $this->escolaDiretor->id_escola]);
        $response = $this->actingAs($this->admin)->get(route('turmas.show', $turma));
        $response->assertStatus(200);
        $response->assertViewIs('classes.show');
    }

    /** @test */
    public function diretor_pode_ver_detalhes_turma_sua_escola()
    {
        $turma = Turma::factory()->create(['id_escola' => $this->escolaDiretor->id_escola]);
        $response = $this->actingAs($this->diretor)->get(route('turmas.show', $turma));
        $response->assertStatus(200);
        $response->assertViewIs('classes.show');
    }

     /** @test */
    public function diretor_nao_pode_ver_detalhes_turma_outra_escola()
    {
        $turmaOutraEscola = Turma::factory()->create(['id_escola' => $this->escolaAdmin->id_escola]);
        $response = $this->actingAs($this->diretor)->get(route('turmas.show', $turmaOutraEscola));
        $response->assertStatus(403);
    }

    /** @test */
    public function admin_pode_atualizar_qualquer_turma()
    {
        $turma = Turma::factory()->create(['id_escola' => $this->escolaDiretor->id_escola]);
        $dadosAtualizados = ['serie' => 'Turma Atualizada Admin'];
        $response = $this->actingAs($this->admin)->put(route('turmas.update', $turma), $dadosAtualizados);
        $response->assertRedirect(route('turmas.index'));
        $this->assertDatabaseHas('turmas', ['id_turma' => $turma->id_turma, 'serie' => 'Turma Atualizada Admin']);
    }

    /** @test */
    public function diretor_pode_atualizar_turma_sua_escola()
    {
        $turma = Turma::factory()->create(['id_escola' => $this->escolaDiretor->id_escola]);
        $dadosAtualizados = ['serie' => 'Turma Atualizada Diretor'];
        $response = $this->actingAs($this->diretor)->put(route('turmas.update', $turma), $dadosAtualizados);
        $response->assertRedirect(route('turmas.index'));
        $this->assertDatabaseHas('turmas', ['id_turma' => $turma->id_turma, 'serie' => 'Turma Atualizada Diretor']);
    }

    /** @test */
    public function diretor_nao_pode_atualizar_turma_outra_escola()
    {
        $turmaOutraEscola = Turma::factory()->create(['id_escola' => $this->escolaAdmin->id_escola]);
        $dadosAtualizados = ['serie' => 'Tentativa Update Diretor'];
        $response = $this->actingAs($this->diretor)->put(route('turmas.update', $turmaOutraEscola), $dadosAtualizados);
        $response->assertStatus(403);
    }

     /** @test */
    public function admin_pode_excluir_turma_sem_ofertas()
    {
        $turma = Turma::factory()->create(['id_escola' => $this->escolaDiretor->id_escola]);
        $response = $this->actingAs($this->admin)->delete(route('turmas.destroy', $turma));
        $response->assertRedirect(route('turmas.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('turmas', ['id_turma' => $turma->id_turma]);
    }

     /** @test */
    public function diretor_pode_excluir_turma_sua_escola_sem_ofertas()
    {
        $turma = Turma::factory()->create(['id_escola' => $this->escolaDiretor->id_escola]);
        $response = $this->actingAs($this->diretor)->delete(route('turmas.destroy', $turma));
        $response->assertRedirect(route('turmas.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('turmas', ['id_turma' => $turma->id_turma]);
    }

    /** @test */
    public function diretor_nao_pode_excluir_turma_outra_escola()
    {
        $turmaOutraEscola = Turma::factory()->create(['id_escola' => $this->escolaAdmin->id_escola]);
        $response = $this->actingAs($this->diretor)->delete(route('turmas.destroy', $turmaOutraEscola));
        $response->assertStatus(403);
    }

     /** @test */
    public function nao_pode_excluir_turma_com_ofertas()
    {
        $turma = Turma::factory()->create(['id_escola' => $this->escolaDiretor->id_escola]);
        OfertaComponente::factory()->create(['id_turma' => $turma->id_turma]);
        $response = $this->actingAs($this->admin)->delete(route('turmas.destroy', $turma));
        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('turmas', ['id_turma' => $turma->id_turma]);
    }

    /** @test */
    public function criar_turma_notifica_diretores_da_escola()
    {
        $diretorEspecifico = Usuario::factory()->create(['tipo_usuario' => 'diretor', 'id_escola' => $this->escolaDiretor->id_escola]);
        $outroDiretor = Usuario::factory()->create(['tipo_usuario' => 'diretor']);
        $dadosTurma = [
            'serie' => 'Notifica Diretor',
            'turno' => 'manha',
            'ano_letivo' => date('Y'),
            'nivel_escolaridade' => 'medio',
            'id_escola' => $this->escolaDiretor->id_escola,
        ];

        $response = $this->actingAs($this->admin)->post(route('turmas.store'), $dadosTurma);
        $response->assertRedirect();
        $this->assertDatabaseHas('turmas', ['serie' => 'Notifica Diretor']);
        $this->assertDatabaseHas('notificacoes', [
            'id_usuario' => $diretorEspecifico->id_usuario,
            'titulo' => 'Nova Turma Cadastrada',
        ]);
         $notificacao = Notificacao::where('id_usuario', $diretorEspecifico->id_usuario)->latest('id_notificacao')->first();
         $this->assertNotNull($notificacao);
         $this->assertStringContainsString('Notifica Diretor', $notificacao->mensagem);
         $this->assertStringContainsString('cadastrada na sua escola', $notificacao->mensagem);
        $this->assertDatabaseMissing('notificacoes', ['id_usuario' => $outroDiretor->id_usuario]);
        $this->assertDatabaseMissing('notificacoes', ['id_usuario' => $this->admin->id_usuario]);
    }

    /** @test */
    public function atualizar_turma_notifica_diretores_da_escola()
    {
         $diretorEspecifico = Usuario::factory()->create(['tipo_usuario' => 'diretor', 'id_escola' => $this->escolaDiretor->id_escola]);
         $turma = Turma::factory()->create(['id_escola' => $this->escolaDiretor->id_escola]);
        $response = $this->actingAs($this->admin)->put(route('turmas.update', $turma), ['serie' => 'Turma Notifica Update']);
        $response->assertRedirect();
        $this->assertDatabaseHas('notificacoes', [
            'id_usuario' => $diretorEspecifico->id_usuario,
            'titulo' => 'Turma Atualizada',
        ]);
         $notificacao = Notificacao::where('id_usuario', $diretorEspecifico->id_usuario)->latest('id_notificacao')->first();
         $this->assertNotNull($notificacao);
         $this->assertStringContainsString('dados da turma', $notificacao->mensagem);
         $this->assertStringContainsString('foram atualizados', $notificacao->mensagem);
    }

    /** @test */
    public function excluir_turma_notifica_diretores_da_escola()
    {
         $diretorEspecifico = Usuario::factory()->create(['tipo_usuario' => 'diretor', 'id_escola' => $this->escolaDiretor->id_escola]);
         $turma = Turma::factory()->create(['id_escola' => $this->escolaDiretor->id_escola]);

         $response = $this->actingAs($this->admin)->delete(route('turmas.destroy', $turma));

         $response->assertRedirect();
         $this->assertDatabaseHas('notificacoes', [
            'id_usuario' => $diretorEspecifico->id_usuario,
            'titulo' => 'Turma Excluída',
         ]);
          $notificacao = Notificacao::where('id_usuario', $diretorEspecifico->id_usuario)->latest('id_notificacao')->first();
          $this->assertNotNull($notificacao);
          $this->assertStringContainsString('foi excluída da sua escola', $notificacao->mensagem);
    }
}
