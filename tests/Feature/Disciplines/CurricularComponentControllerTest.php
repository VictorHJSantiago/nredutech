<?php

namespace Tests\Feature\Disciplines;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Usuario;
use App\Models\Municipio;
use App\Models\Escola;
use App\Models\ComponenteCurricular;
use App\Models\OfertaComponente; 
use App\Models\Notificacao; 
class CurricularComponentControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $diretor;
    protected $professor;
    protected $escolaDiretor;
    protected $componenteGlobal;
    protected $componenteEscola;
    protected $componenteProfessor;

    protected function setUp(): void
    {
        parent::setUp();
        $municipio = Municipio::factory()->create();
        $this->escolaDiretor = Escola::factory()->create(['id_municipio' => $municipio->id_municipio]);

        $this->admin = Usuario::factory()->create(['tipo_usuario' => 'administrador', 'id_escola' => null]);
        $this->diretor = Usuario::factory()->create(['tipo_usuario' => 'diretor', 'id_escola' => $this->escolaDiretor->id_escola]);
        $this->professor = Usuario::factory()->create(['tipo_usuario' => 'professor', 'id_escola' => $this->escolaDiretor->id_escola]);
        $this->componenteGlobal = ComponenteCurricular::factory()->create(['id_escola' => null, 'id_usuario_criador' => $this->admin->id_usuario, 'status' => 'aprovado']);
        $this->componenteEscola = ComponenteCurricular::factory()->create(['id_escola' => $this->escolaDiretor->id_escola, 'id_usuario_criador' => $this->diretor->id_usuario, 'status' => 'pendente']);
        $this->componenteProfessor = ComponenteCurricular::factory()->create(['id_escola' => $this->escolaDiretor->id_escola, 'id_usuario_criador' => $this->professor->id_usuario, 'status' => 'pendente']);
        $outraEscola = Escola::factory()->create(['id_municipio' => $municipio->id_municipio]);
        ComponenteCurricular::factory()->create(['id_escola' => $outraEscola->id_escola, 'id_usuario_criador' => $this->admin->id_usuario, 'status' => 'aprovado']);
    }

    /** @test */
    public function admin_ve_todos_componentes()
    {
        $response = $this->actingAs($this->admin)->get(route('componentes.index'));
        $response->assertStatus(200);
        $response->assertSee($this->componenteGlobal->nome);
        $response->assertSee($this->componenteEscola->nome);
        $response->assertSee($this->componenteProfessor->nome);
        $response->assertViewHas('componentes', fn($c) => $c->count() >= 4); 
    }

    /** @test */
    public function diretor_ve_componentes_globais_e_da_sua_escola()
    {
        $response = $this->actingAs($this->diretor)->get(route('componentes.index'));
        $response->assertStatus(200);
        $response->assertSee($this->componenteGlobal->nome);
        $response->assertSee($this->componenteEscola->nome);
        $response->assertSee($this->componenteProfessor->nome);
        $response->assertViewHas('componentes', fn($c) => $c->total() === 3); 
    }

    /** @test */
    public function professor_ve_componentes_globais_e_da_sua_escola()
    {
        $response = $this->actingAs($this->professor)->get(route('componentes.index'));
        $response->assertStatus(200);
        $response->assertSee($this->componenteGlobal->nome);
        $response->assertSee($this->componenteEscola->nome);
        $response->assertSee($this->componenteProfessor->nome);
         $response->assertViewHas('componentes', fn($c) => $c->total() === 3);
    }

    /** @test */
    public function qualquer_usuario_pode_criar_componente()
    {
        $dados = ['nome' => 'Nova Disciplina', 'carga_horaria' => '40h'];
        $responseProf = $this->actingAs($this->professor)->post(route('componentes.store'), $dados);
        $responseProf->assertRedirect(route('componentes.index'));
        $responseProf->assertSessionHas('success');
        $this->assertDatabaseHas('componentes_curriculares', [
            'nome' => 'Nova Disciplina',
            'status' => 'pendente',
            'id_escola' => $this->professor->id_escola,
            'id_usuario_criador' => $this->professor->id_usuario
        ]);

        ComponenteCurricular::where('nome', 'Nova Disciplina')->delete();
        $responseAdmin = $this->actingAs($this->admin)->post(route('componentes.store'), $dados + ['id_escola' => null, 'status' => 'aprovado']); 
        $responseAdmin->assertRedirect(route('componentes.index'));
        $responseAdmin->assertSessionHas('success');
        $this->assertDatabaseHas('componentes_curriculares', [
            'nome' => 'Nova Disciplina',
            'status' => 'aprovado',
            'id_escola' => null,
            'id_usuario_criador' => $this->admin->id_usuario
        ]);
    }

    /** @test */
    public function diretor_pode_aprovar_componente_pendente_sua_escola()
    {
        $this->assertEquals('pendente', $this->componenteEscola->status);
        $response = $this->actingAs($this->diretor)->put(route('componentes.update', $this->componenteEscola), [
            'nome' => $this->componenteEscola->nome, 
            'carga_horaria' => $this->componenteEscola->carga_horaria,
            'status' => 'aprovado'
        ]);
        $response->assertRedirect(route('componentes.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('componentes_curriculares', ['id_componente' => $this->componenteEscola->id_componente, 'status' => 'aprovado']);
    }

    /** @test */
    public function professor_pode_aprovar_componente_que_criou()
    {
         $this->assertEquals('pendente', $this->componenteProfessor->status);
        $response = $this->actingAs($this->professor)->put(route('componentes.update', $this->componenteProfessor), [
            'nome' => $this->componenteProfessor->nome,
            'carga_horaria' => $this->componenteProfessor->carga_horaria,
            'status' => 'aprovado' 
        ]);
        $response->assertRedirect(route('componentes.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('componentes_curriculares', ['id_componente' => $this->componenteProfessor->id_componente, 'status' => 'aprovado']);
    }

     /** @test */
    public function professor_nao_pode_aprovar_componente_de_outro()
    {
        $this->assertEquals('pendente', $this->componenteEscola->status);
        $response = $this->actingAs($this->professor)->put(route('componentes.update', $this->componenteEscola), [
             'nome' => $this->componenteEscola->nome,
            'carga_horaria' => $this->componenteEscola->carga_horaria,
            'status' => 'aprovado' 
        ]);
        $response->assertStatus(403); 
        $this->assertDatabaseHas('componentes_curriculares', ['id_componente' => $this->componenteEscola->id_componente, 'status' => 'pendente']); 
    }

    /** @test */
    public function professor_pode_editar_componente_que_criou()
    {
        $novoNome = 'Componente Editado Pelo Professor';
        $response = $this->actingAs($this->professor)->put(route('componentes.update', $this->componenteProfessor), [
            'nome' => $novoNome,
            'carga_horaria' => $this->componenteProfessor->carga_horaria, 
        ]);
        $response->assertRedirect(route('componentes.index'));
        $this->assertDatabaseHas('componentes_curriculares', ['id_componente' => $this->componenteProfessor->id_componente, 'nome' => $novoNome]);
    }

     /** @test */
    public function professor_nao_pode_editar_componente_de_outro()
    {
         $response = $this->actingAs($this->professor)->put(route('componentes.update', $this->componenteEscola), [
            'nome' => 'Nome Editado Incorretamente',
            'carga_horaria' => $this->componenteEscola->carga_horaria,
        ]);
        $response->assertStatus(403);
    }

     /** @test */
    public function pode_excluir_componente_sem_oferta()
    {
        $response = $this->actingAs($this->professor)->delete(route('componentes.destroy', $this->componenteProfessor));
        $response->assertRedirect(route('componentes.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('componentes_curriculares', ['id_componente' => $this->componenteProfessor->id_componente]);
    }

    /** @test */
    public function nao_pode_excluir_componente_com_oferta()
    {
         OfertaComponente::factory()->create(['id_componente' => $this->componenteGlobal->id_componente]);
         $response = $this->actingAs($this->admin)->delete(route('componentes.destroy', $this->componenteGlobal));
         $response->assertRedirect(route('componentes.index'));
         $response->assertSessionHas('error');
         $this->assertDatabaseHas('componentes_curriculares', ['id_componente' => $this->componenteGlobal->id_componente]);
    }

    /** @test */
    public function index_filtra_por_nome()
    {
        ComponenteCurricular::factory()->create(['nome' => 'Filosofia Antiga', 'status' => 'aprovado']);
        ComponenteCurricular::factory()->create(['nome' => 'Filosofia Moderna', 'status' => 'aprovado']);
        ComponenteCurricular::factory()->create(['nome' => 'Sociologia', 'status' => 'aprovado']);

        $response = $this->actingAs($this->admin)->get(route('componentes.index', ['search_text' => 'Filosofia']));
        $response->assertStatus(200);
        $response->assertSee('Filosofia Antiga');
        $response->assertSee('Filosofia Moderna');
        $response->assertDontSee('Sociologia');
    }

    /** @test */
    public function index_filtra_por_carga_horaria()
    {
        ComponenteCurricular::factory()->create(['nome' => 'Arte', 'carga_horaria' => '30h', 'status' => 'aprovado']);
        ComponenteCurricular::factory()->create(['nome' => 'Musica', 'carga_horaria' => '60h', 'status' => 'aprovado']);
        ComponenteCurricular::factory()->create(['nome' => 'Teatro', 'carga_horaria' => '60h', 'status' => 'aprovado']);

        $response = $this->actingAs($this->admin)->get(route('componentes.index', ['search_carga' => '60h']));
        $response->assertStatus(200);
        $response->assertDontSee('Arte');
        $response->assertSee('Musica');
        $response->assertSee('Teatro');
    }

    /** @test */
    public function index_filtra_por_status()
    {
        $response = $this->actingAs($this->admin)->get(route('componentes.index', ['status' => 'pendente']));
        $response->assertStatus(200);
        $response->assertDontSee($this->componenteGlobal->nome); 
        $response->assertSee($this->componenteEscola->nome); 
        $response->assertSee($this->componenteProfessor->nome); 
    }

    /** @test */
    public function store_falha_com_dados_invalidos()
    {
        $response = $this->actingAs($this->admin)->post(route('componentes.store'), ['nome' => '']);
        $response->assertSessionHasErrors('nome');
    }

    /** @test */
    public function update_falha_com_dados_invalidos()
    {
        $response = $this->actingAs($this->admin)->put(route('componentes.update', $this->componenteGlobal), ['carga_horaria' => '']); 
        $response->assertSessionHasErrors('carga_horaria');
    }

    /** @test */
    public function criar_componente_pendente_notifica_admin_e_diretor()
    {
        $dados = ['nome' => 'Disciplina Pendente Notif', 'carga_horaria' => '50h'];
        $response = $this->actingAs($this->professor)->post(route('componentes.store'), $dados);
        $response->assertRedirect();
        $componenteCriado = ComponenteCurricular::where('nome', 'Disciplina Pendente Notif')->first();
        $this->assertEquals('pendente', $componenteCriado->status);
        $this->assertDatabaseHas('notificacoes', [
            'id_usuario' => $this->admin->id_usuario,
            'titulo' => 'Nova Disciplina para Aprovação',
        ]);
        $notAdmin = Notificacao::where('id_usuario', $this->admin->id_usuario)->latest('id_notificacao')->first();
        $this->assertNotNull($notAdmin);
        $this->assertStringContainsString('Disciplina Pendente Notif', $notAdmin->mensagem);
        $this->assertStringContainsString('aguarda aprovação', $notAdmin->mensagem);
        $this->assertDatabaseHas('notificacoes', [
            'id_usuario' => $this->diretor->id_usuario,
            'titulo' => 'Nova Disciplina para Aprovação',
        ]);
        $notDiretor = Notificacao::where('id_usuario', $this->diretor->id_usuario)->latest('id_notificacao')->first();
        $this->assertNotNull($notDiretor);
        $this->assertStringContainsString('Disciplina Pendente Notif', $notDiretor->mensagem);
        $this->assertStringContainsString('aguarda aprovação', $notDiretor->mensagem);
        $this->assertDatabaseHas('notificacoes', ['id_usuario' => $this->professor->id_usuario]);
    }

     /** @test */
    public function aprovar_componente_notifica_criador_admin_diretor()
    {
        $this->assertEquals('pendente', $this->componenteProfessor->status);
        $response = $this->actingAs($this->admin)->put(route('componentes.update', $this->componenteProfessor), [
            'nome' => $this->componenteProfessor->nome,
            'carga_horaria' => $this->componenteProfessor->carga_horaria,
            'status' => 'aprovado'
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('componentes_curriculares', ['id_componente' => $this->componenteProfessor->id_componente, 'status' => 'aprovado']);
        $this->assertDatabaseHas('notificacoes', [
            'id_usuario' => $this->professor->id_usuario,
            'titulo' => 'Atualização de Status da Disciplina',
        ]);
        $notProf = Notificacao::where('id_usuario', $this->professor->id_usuario)->latest('id_notificacao')->first();
        $this->assertNotNull($notProf);
        $this->assertStringContainsString("mudou de 'pendente' para 'aprovado'", $notProf->mensagem);
        $this->assertDatabaseHas('notificacoes', [
            'id_usuario' => $this->diretor->id_usuario,
            'titulo' => 'Disciplina Atualizada', 
        ]);
        $notDir = Notificacao::where('id_usuario', $this->diretor->id_usuario)->latest('id_notificacao')->first();
        $this->assertNotNull($notDir);
        $this->assertStringContainsString("mudou de 'pendente' para 'aprovado'", $notDir->mensagem);
        $this->assertDatabaseHas('notificacoes', [
            'id_usuario' => $this->admin->id_usuario,
            'titulo' => 'Disciplina Atualizada',
        ]);
        $notAdm = Notificacao::where('id_usuario', $this->admin->id_usuario)->latest('id_notificacao')->first();
        $this->assertNotNull($notAdm);
        $this->assertStringContainsString("mudou de 'pendente' para 'aprovado'", $notAdm->mensagem);
    }

    /** @test */
    public function excluir_componente_notifica_criador_admin_diretor()
    {
        $response = $this->actingAs($this->admin)->delete(route('componentes.destroy', $this->componenteProfessor));
        $response->assertRedirect();
        $this->assertDatabaseMissing('componentes_curriculares', ['id_componente' => $this->componenteProfessor->id_componente]);
        $this->assertDatabaseHas('notificacoes', [
            'id_usuario' => $this->professor->id_usuario,
            'titulo' => 'Disciplina Excluída',
        ]);
        $this->assertDatabaseHas('notificacoes', [
            'id_usuario' => $this->diretor->id_usuario,
            'titulo' => 'Disciplina Excluída',
        ]);
        $this->assertDatabaseHas('notificacoes', [
            'id_usuario' => $this->admin->id_usuario,
            'titulo' => 'Disciplina Excluída',
        ]);
    }

}
