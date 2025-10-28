<?php

namespace Tests\Feature\DidacticResource;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Usuario;
use App\Models\Municipio;
use App\Models\Escola;
use App\Models\RecursoDidatico;
use App\Models\Agendamento; 

class DidacticResourceControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $diretor;
    protected $professor;
    protected $escolaDiretor;
    protected $recursoGlobal;
    protected $recursoEscola;
    protected $recursoProfessor;

    protected function setUp(): void
    {
        parent::setUp();
        $municipio = Municipio::factory()->create();
        $this->escolaDiretor = Escola::factory()->create(['id_municipio' => $municipio->id_municipio]);
        $this->admin = Usuario::factory()->create(['tipo_usuario' => 'administrador', 'id_escola' => null]);
        $this->diretor = Usuario::factory()->create(['tipo_usuario' => 'diretor', 'id_escola' => $this->escolaDiretor->id_escola]);
        $this->professor = Usuario::factory()->create(['tipo_usuario' => 'professor', 'id_escola' => $this->escolaDiretor->id_escola]);
        $this->recursoGlobal = RecursoDidatico::factory()->create(['id_escola' => null, 'id_usuario_criador' => $this->admin->id_usuario, 'status' => 'funcionando']);
        $this->recursoEscola = RecursoDidatico::factory()->create(['id_escola' => $this->escolaDiretor->id_escola, 'id_usuario_criador' => $this->diretor->id_usuario, 'status' => 'funcionando']);
        $this->recursoProfessor = RecursoDidatico::factory()->create(['id_escola' => $this->escolaDiretor->id_escola, 'id_usuario_criador' => $this->professor->id_usuario, 'status' => 'em_manutencao']);
        $outraEscola = Escola::factory()->create(['id_municipio' => $municipio->id_municipio]);
        RecursoDidatico::factory()->create(['id_escola' => $outraEscola->id_escola, 'id_usuario_criador' => $this->admin->id_usuario, 'status' => 'funcionando']);
    }

     /** @test */
    public function admin_ve_todos_recursos()
    {
        $response = $this->actingAs($this->admin)->get(route('resources.index'));
        $response->assertStatus(200);
        $response->assertSee($this->recursoGlobal->nome);
        $response->assertSee($this->recursoEscola->nome);
        $response->assertSee($this->recursoProfessor->nome);
        $response->assertViewHas('recursos', fn($r) => $r->total() >= 4);
    }

    /** @test */
    public function diretor_ve_recursos_globais_e_da_sua_escola()
    {
        $response = $this->actingAs($this->diretor)->get(route('resources.index'));
        $response->assertStatus(200);
        $response->assertSee($this->recursoGlobal->nome);
        $response->assertSee($this->recursoEscola->nome);
        $response->assertSee($this->recursoProfessor->nome);
        $response->assertViewHas('recursos', fn($r) => $r->total() === 3);
    }

     /** @test */
    public function professor_ve_recursos_globais_e_da_sua_escola()
    {
        $response = $this->actingAs($this->professor)->get(route('resources.index'));
        $response->assertStatus(200);
        $response->assertSee($this->recursoGlobal->nome);
        $response->assertSee($this->recursoEscola->nome);
        $response->assertSee($this->recursoProfessor->nome);
        $response->assertViewHas('recursos', fn($r) => $r->total() === 3);
    }

    /** @test */
    public function qualquer_usuario_pode_criar_recurso()
    {
        $dados = [
            'nome' => 'Novo Projetor',
            'tipo' => 'didatico',
            'quantidade' => 1,
            'status' => 'funcionando',
            'split_quantity' => 'false'
        ];

        $responseProf = $this->actingAs($this->professor)->post(route('resources.store'), $dados);
        $responseProf->assertRedirect(route('resources.index'));
        $responseProf->assertSessionHas('success');
        $this->assertDatabaseHas('recursos_didaticos', [
            'nome' => 'Novo Projetor',
            'id_escola' => $this->professor->id_escola,
            'id_usuario_criador' => $this->professor->id_usuario
        ]);

        RecursoDidatico::where('nome', 'Novo Projetor')->delete(); 
        $responseAdmin = $this->actingAs($this->admin)->post(route('resources.store'), $dados + ['id_escola' => null]);
        $responseAdmin->assertRedirect(route('resources.index'));
        $responseAdmin->assertSessionHas('success');
        $this->assertDatabaseHas('recursos_didaticos', [
            'nome' => 'Novo Projetor',
            'id_escola' => null,
            'id_usuario_criador' => $this->admin->id_usuario
        ]);
    }

    /** @test */
    public function criar_recurso_com_split_gera_multiplos_registros()
    {
        $dados = [
            'nome' => 'Tablet Educacional',
            'tipo' => 'didatico',
            'quantidade' => 3,
            'status' => 'funcionando',
            'numero_serie' => 'SN-TAB',
            'split_quantity' => 'true' 
        ];

        $response = $this->actingAs($this->admin)->post(route('resources.store'), $dados);
        $response->assertRedirect(route('resources.index'));
        $response->assertSessionHas('success', '3 recursos individuais cadastrados com sucesso!');
        $this->assertDatabaseCount('recursos_didaticos', 3 + 4); 
        $this->assertDatabaseHas('recursos_didaticos', ['nome' => 'Tablet Educacional', 'quantidade' => 1, 'numero_serie' => 'SN-TAB-1']);
        $this->assertDatabaseHas('recursos_didaticos', ['nome' => 'Tablet Educacional', 'quantidade' => 1, 'numero_serie' => 'SN-TAB-2']);
        $this->assertDatabaseHas('recursos_didaticos', ['nome' => 'Tablet Educacional', 'quantidade' => 1, 'numero_serie' => 'SN-TAB-3']);
    }

    /** @test */
    public function professor_pode_editar_recurso_que_criou()
    {
         $novoNome = 'Recurso Editado Pelo Professor';
        $response = $this->actingAs($this->professor)->put(route('resources.update', $this->recursoProfessor), [
            'nome' => $novoNome,
            'tipo' => $this->recursoProfessor->tipo, 
            'quantidade' => $this->recursoProfessor->quantidade,
            'status' => 'funcionando', 
        ]);
        $response->assertRedirect(route('resources.index'));
        $this->assertDatabaseHas('recursos_didaticos', ['id_recurso' => $this->recursoProfessor->id_recurso, 'nome' => $novoNome, 'status' => 'funcionando']);
    }

    /** @test */
    public function professor_nao_pode_editar_recurso_de_outro()
    {
         $response = $this->actingAs($this->professor)->put(route('resources.update', $this->recursoEscola), [
            'nome' => 'Nome Editado Incorretamente',
             'tipo' => $this->recursoEscola->tipo,
            'quantidade' => $this->recursoEscola->quantidade,
            'status' => $this->recursoEscola->status,
        ]);
        $response->assertStatus(403);
    }

    /** @test */
    public function pode_excluir_recurso_sem_agendamento()
    {
        $response = $this->actingAs($this->professor)->delete(route('resources.destroy', $this->recursoProfessor));
        $response->assertRedirect(route('resources.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('recursos_didaticos', ['id_recurso' => $this->recursoProfessor->id_recurso]);
    }

    /** @test */
    public function nao_pode_excluir_recurso_com_agendamento()
    {
         Agendamento::factory()->create(['id_recurso' => $this->recursoGlobal->id_recurso]); // Cria agendamento
         $response = $this->actingAs($this->admin)->delete(route('resources.destroy', $this->recursoGlobal));
         $response->assertRedirect(route('resources.index'));
         $response->assertSessionHas('error');
         $this->assertDatabaseHas('recursos_didaticos', ['id_recurso' => $this->recursoGlobal->id_recurso]);
    }

    /** @test */
    public function index_filtra_por_nome()
    {
        RecursoDidatico::factory()->create(['nome' => 'Microscópio Binocular', 'status' => 'funcionando']);
        RecursoDidatico::factory()->create(['nome' => 'Microscópio Eletrônico', 'status' => 'funcionando']);
        RecursoDidatico::factory()->create(['nome' => 'Telescópio', 'status' => 'funcionando']);

        $response = $this->actingAs($this->admin)->get(route('resources.index', ['search_nome' => 'Microscópio']));
        $response->assertStatus(200);
        $response->assertSee('Microscópio Binocular');
        $response->assertSee('Microscópio Eletrônico');
        $response->assertDontSee('Telescópio');
    }

     /** @test */
    public function index_filtra_por_marca()
    {
        RecursoDidatico::factory()->create(['nome' => 'Projetor A', 'marca' => 'Epson', 'status' => 'funcionando']);
        RecursoDidatico::factory()->create(['nome' => 'Projetor B', 'marca' => 'BenQ', 'status' => 'funcionando']);
        RecursoDidatico::factory()->create(['nome' => 'Projetor C', 'marca' => 'Epson', 'status' => 'funcionando']);

        $response = $this->actingAs($this->admin)->get(route('resources.index', ['search_marca' => 'Epson']));
        $response->assertStatus(200);
        $response->assertSee('Projetor A');
        $response->assertDontSee('Projetor B');
        $response->assertSee('Projetor C');
    }

    /** @test */
    public function index_filtra_por_status()
    {
        $response = $this->actingAs($this->admin)->get(route('resources.index', ['status' => 'em_manutencao']));
        $response->assertStatus(200);
        $response->assertDontSee($this->recursoGlobal->nome); 
        $response->assertDontSee($this->recursoEscola->nome); 
        $response->assertSee($this->recursoProfessor->nome); 
    }

    /** @test */
    public function store_falha_com_dados_invalidos()
    {
        $response = $this->actingAs($this->admin)->post(route('resources.store'), ['nome' => '']); 
        $response->assertSessionHasErrors('nome');
    }

    /** @test */
    public function update_falha_com_dados_invalidos()
    {
        $response = $this->actingAs($this->admin)->put(route('resources.update', $this->recursoGlobal), ['quantidade' => -1]); 
        $response->assertSessionHasErrors('quantidade');
    }

    /** @test */
    public function criar_recurso_notifica_admin_e_diretor()
    {
        $dados = [
            'nome' => 'Recurso Notificacao',
            'tipo' => 'didatico',
            'quantidade' => 1,
            'status' => 'funcionando',
            'split_quantity' => 'false'
        ];

        $response = $this->actingAs($this->professor)->post(route('resources.store'), $dados);
        $response->assertRedirect();
        $recursoCriado = RecursoDidatico::where('nome', 'Recurso Notificacao')->first();
        $this->assertDatabaseHas('notificacoes', [
            'id_usuario' => $this->admin->id_usuario,
            'titulo' => 'Novo Lote de Recursos Cadastrado',
        ]);
        $notAdmin = Notificacao::where('id_usuario', $this->admin->id_usuario)->latest('id_notificacao')->first();
        $this->assertNotNull($notAdmin);
        $this->assertStringContainsString('Recurso Notificacao', $notAdmin->mensagem);
        $this->assertStringContainsString($this->professor->nome_completo, $notAdmin->mensagem);

        $this->assertDatabaseHas('notificacoes', [
            'id_usuario' => $this->diretor->id_usuario, 
            'titulo' => 'Novo Lote de Recursos Cadastrado',
        ]);
         $notDiretor = Notificacao::where('id_usuario', $this->diretor->id_usuario)->latest('id_notificacao')->first();
         $this->assertNotNull($notDiretor);
         $this->assertStringContainsString('Recurso Notificacao', $notDiretor->mensagem);
         $this->assertDatabaseHas('notificacoes', ['id_usuario' => $this->professor->id_usuario]);
    }

    /** @test */
    public function atualizar_recurso_notifica_admin_diretores()
    {
        $response = $this->actingAs($this->admin)->put(route('resources.update', $this->recursoProfessor), [
            'nome' => 'Recurso Atualizado Notif',
            'tipo' => $this->recursoProfessor->tipo,
            'quantidade' => $this->recursoProfessor->quantidade,
            'status' => 'quebrado'
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('notificacoes', [ 'id_usuario' => $this->admin->id_usuario, 'titulo' => 'Recurso Didático Atualizado']);
        $this->assertDatabaseHas('notificacoes', [ 'id_usuario' => $this->diretor->id_usuario, 'titulo' => 'Recurso Didático Atualizado']);
         // $this->assertDatabaseHas('notificacoes', [ 'id_usuario' => $this->professor->id_usuario, 'titulo' => 'Recurso Didático Atualizado']);
    }

    /** @test */
    public function excluir_recurso_notifica_criador_admin_diretor()
    {
        $response = $this->actingAs($this->admin)->delete(route('resources.destroy', $this->recursoProfessor));
        $response->assertRedirect();
        $this->assertDatabaseMissing('recursos_didaticos', ['id_recurso' => $this->recursoProfessor->id_recurso]);
        $this->assertDatabaseHas('notificacoes', ['id_usuario' => $this->professor->id_usuario, 'titulo' => 'Recurso Didático Excluído']);
        $this->assertDatabaseHas('notificacoes', ['id_usuario' => $this->diretor->id_usuario, 'titulo' => 'Recurso Didático Excluído']);
        $this->assertDatabaseHas('notificacoes', ['id_usuario' => $this->admin->id_usuario, 'titulo' => 'Recurso Didático Excluído']);
    }
}
