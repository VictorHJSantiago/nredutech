<?php

namespace Tests\Feature\DidacticResource;

use Tests\TestCase;
use App\Models\Usuario;
use App\Models\Escola;
use App\Models\Municipio;
use App\Models\RecursoDidatico;
use App\Models\Agendamento;
use App\Models\Turma;
use App\Models\ComponenteCurricular;
use App\Models\OfertaComponente;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class DidacticResourceControllerTest extends TestCase
{
    use RefreshDatabase;

    private Usuario $admin;
    private Usuario $diretorA;
    private Usuario $professorA;
    private Usuario $outroProfessorA;
    private Usuario $diretorB;
    private Escola $escolaA;
    private Escola $escolaB;
    private RecursoDidatico $recursoGlobal;
    private RecursoDidatico $recursoDiretorA;
    private RecursoDidatico $recursoProfessorA;
    private RecursoDidatico $recursoDiretorB;

    protected function setUp(): void
    {
        parent::setUp();

        $municipio = Municipio::create(['nome' => 'Municipio Teste', 'estado' => 'PR']);
        $this->escolaA = Escola::create(['nome' => 'Escola Teste A', 'id_municipio' => $municipio->id_municipio, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana']);
        $this->escolaB = Escola::create(['nome' => 'Escola Teste B', 'id_municipio' => $municipio->id_municipio, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana']);
        
        $this->admin = Usuario::factory()->create(['tipo_usuario' => 'administrador', 'id_escola' => null]);
        $this->diretorA = Usuario::factory()->create(['tipo_usuario' => 'diretor', 'id_escola' => $this->escolaA->id_escola]);
        $this->professorA = Usuario::factory()->create(['tipo_usuario' => 'professor', 'id_escola' => $this->escolaA->id_escola]);
        $this->outroProfessorA = Usuario::factory()->create(['tipo_usuario' => 'professor', 'id_escola' => $this->escolaA->id_escola]);
        $this->diretorB = Usuario::factory()->create(['tipo_usuario' => 'diretor', 'id_escola' => $this->escolaB->id_escola]);

        $this->recursoGlobal = RecursoDidatico::factory()->create(['nome' => 'Global', 'id_escola' => null, 'id_usuario_criador' => $this->admin->id_usuario, 'status' => 'funcionando']);
        $this->recursoDiretorA = RecursoDidatico::factory()->create(['nome' => 'Recurso Diretor A', 'id_escola' => $this->escolaA->id_escola, 'id_usuario_criador' => $this->diretorA->id_usuario, 'status' => 'funcionando']);
        $this->recursoProfessorA = RecursoDidatico::factory()->create(['nome' => 'Recurso Professor A', 'id_escola' => $this->escolaA->id_escola, 'id_usuario_criador' => $this->professorA->id_usuario, 'status' => 'funcionando']);
        $this->recursoDiretorB = RecursoDidatico::factory()->create(['nome' => 'Recurso Diretor B', 'id_escola' => $this->escolaB->id_escola, 'id_usuario_criador' => $this->diretorB->id_usuario, 'status' => 'funcionando']);
    }

    #[Test]
    public function admin_pode_ver_todos_recursos_na_listagem()
    {
        $response = $this->actingAs($this->admin)->get(route('resources.index'));
        $response->assertOk();
        $response->assertViewHas('recursos', fn ($recursos) => $recursos->count() === 4);
    }

    #[Test]
    public function diretor_pode_ver_recursos_da_propria_escola_e_globais_na_listagem()
    {
        $response = $this->actingAs($this->diretorA)->get(route('resources.index'));
        $response->assertOk();
        $response->assertViewHas('recursos', fn ($recursos) => $recursos->count() === 3);
        $response->assertSee('Global');
        $response->assertSee('Recurso Diretor A');
        $response->assertSee('Recurso Professor A');
        $response->assertDontSee('Recurso Diretor B');
    }

    #[Test]
    public function professor_pode_ver_recursos_da_propria_escola_e_globais_na_listagem()
    {
        $response = $this->actingAs($this->professorA)->get(route('resources.index'));
        $response->assertOk();
        $response->assertViewHas('recursos', fn ($recursos) => $recursos->count() === 3);
        $response->assertSee('Global');
        $response->assertDontSee('Recurso Diretor B');
    }

    #[Test]
    public function admin_pode_cadastrar_recurso_global()
    {
        $data = RecursoDidatico::factory()->make(['nome' => 'Novo Global', 'id_escola' => null, 'id_usuario_criador' => $this->admin->id_usuario, 'quantidade' => 1])->toArray();
        $response = $this->actingAs($this->admin)->post(route('resources.store'), $data);

        $response->assertRedirect(route('resources.index'));
        $response->assertSessionHas('success', 'Lote de 1 recurso(s) cadastrado com sucesso!');
        $this->assertDatabaseHas('recursos_didaticos', ['nome' => 'Novo Global', 'id_escola' => null]);
    }

    #[Test]
    public function diretor_pode_cadastrar_recurso_da_propria_escola()
    {
        $data = RecursoDidatico::factory()->make(['nome' => 'Novo Recurso Diretor', 'id_escola' => $this->diretorA->id_escola, 'id_usuario_criador' => $this->diretorA->id_usuario, 'quantidade' => 1])->toArray();
        $response = $this->actingAs($this->diretorA)->post(route('resources.store'), $data);

        $response->assertRedirect(route('resources.index'));
        $this->assertDatabaseHas('recursos_didaticos', ['nome' => 'Novo Recurso Diretor', 'id_escola' => $this->escolaA->id_escola]);
    }

    #[Test]
    public function professor_pode_cadastrar_recurso_da_propria_escola()
    {
        $data = RecursoDidatico::factory()->make(['nome' => 'Novo Recurso Professor', 'id_escola' => $this->professorA->id_escola, 'id_usuario_criador' => $this->professorA->id_usuario, 'quantidade' => 1])->toArray();
        $response = $this->actingAs($this->professorA)->post(route('resources.store'), $data);

        $response->assertRedirect(route('resources.index'));
        $this->assertDatabaseHas('recursos_didaticos', ['nome' => 'Novo Recurso Professor', 'id_escola' => $this->escolaA->id_escola]);
    }

    #[Test]
    public function diretor_nao_pode_cadastrar_recurso_global()
    {
        $data = RecursoDidatico::factory()->make(['nome' => 'Global Proibido', 'id_escola' => null, 'id_usuario_criador' => $this->diretorA->id_usuario])->toArray();
        $response = $this->actingAs($this->diretorA)->post(route('resources.store'), $data);
        $response->assertRedirect();
    }

    #[Test]
    public function diretor_nao_pode_cadastrar_recurso_de_outra_escola()
    {
        $data = RecursoDidatico::factory()->make(['nome' => 'Recurso Proibido', 'id_escola' => $this->escolaB->id_escola, 'id_usuario_criador' => $this->diretorA->id_usuario])->toArray();
        $response = $this->actingAs($this->diretorA)->post(route('resources.store'), $data);
        $response->assertRedirect();
    }

    #[Test]
    public function admin_pode_atualizar_qualquer_recurso()
    {
        $data = $this->recursoDiretorB->toArray();
        $data['nome'] = 'Atualizado pelo Admin';
        
        $response = $this->actingAs($this->admin)->put(route('resources.update', $this->recursoDiretorB), $data);
        $response->assertRedirect(route('resources.index'));
        $this->assertDatabaseHas('recursos_didaticos', ['id_recurso' => $this->recursoDiretorB->id_recurso, 'nome' => 'Atualizado pelo Admin']);
    }

    #[Test]
    public function diretor_pode_atualizar_recurso_da_propria_escola_criado_por_professor()
    {
        $data = $this->recursoProfessorA->toArray();
        $data['nome'] = 'Atualizado pelo Diretor';
        
        $response = $this->actingAs($this->diretorA)->put(route('resources.update', $this->recursoProfessorA), $data);
        $response->assertRedirect(route('resources.index'));
        $this->assertDatabaseHas('recursos_didaticos', ['id_recurso' => $this->recursoProfessorA->id_recurso, 'nome' => 'Atualizado pelo Diretor']);
    }

    #[Test]
    public function diretor_nao_pode_atualizar_recurso_de_outra_escola()
    {
        $data = $this->recursoDiretorB->toArray();
        $data['nome'] = 'Update Proibido';
        
        $response = $this->actingAs($this->diretorA)->put(route('resources.update', $this->recursoDiretorB), $data);
        $response->assertForbidden();
    }

    #[Test]
    public function diretor_nao_pode_atualizar_recurso_global()
    {
        $data = $this->recursoGlobal->toArray();
        $data['nome'] = 'Update Proibido';
        
        $response = $this->actingAs($this->diretorA)->put(route('resources.update', $this->recursoGlobal), $data);
        $response->assertRedirect(route('resources.index'));
    }

    #[Test]
    public function professor_pode_atualizar_proprio_recurso()
    {
        $data = $this->recursoProfessorA->toArray();
        $data['nome'] = 'Atualizado pelo Criador';
        
        $response = $this->actingAs($this->professorA)->put(route('resources.update', $this->recursoProfessorA), $data);
        $response->assertRedirect(route('resources.index'));
        $this->assertDatabaseHas('recursos_didaticos', ['id_recurso' => $this->recursoProfessorA->id_recurso, 'nome' => 'Atualizado pelo Criador']);
    }

    #[Test]
    public function professor_nao_pode_atualizar_recurso_de_outro_professor()
    {
        $recursoOutroProf = RecursoDidatico::factory()->create(['id_escola' => $this->escolaA->id_escola, 'id_usuario_criador' => $this->outroProfessorA->id_usuario, 'status' => 'funcionando']);
        $data = $recursoOutroProf->toArray();
        $data['nome'] = 'Update Proibido';
        
        $response = $this->actingAs($this->professorA)->put(route('resources.update', $recursoOutroProf), $data);
        $response->assertForbidden();
    }

    #[Test]
    public function admin_pode_destruir_qualquer_recurso()
    {
        $response = $this->actingAs($this->admin)->delete(route('resources.destroy', $this->recursoDiretorB));
        $response->assertRedirect(route('resources.index'));
        $response->assertSessionHas('success', 'Recurso didático excluído com sucesso!');
        $this->assertDatabaseMissing('recursos_didaticos', ['id_recurso' => $this->recursoDiretorB->id_recurso]);
    }

    #[Test]
    public function diretor_pode_destruir_recurso_da_propria_escola()
    {
        $response = $this->actingAs($this->diretorA)->delete(route('resources.destroy', $this->recursoProfessorA));
        $response->assertRedirect(route('resources.index'));
        $this->assertDatabaseMissing('recursos_didaticos', ['id_recurso' => $this->recursoProfessorA->id_recurso]);
    }

    #[Test]
    public function diretor_nao_pode_destruir_recurso_de_outra_escola()
    {
        $response = $this->actingAs($this->diretorA)->delete(route('resources.destroy', $this->recursoDiretorB));
        $response->assertForbidden();
    }

    #[Test]
    public function professor_pode_destruir_proprio_recurso()
    {
        $response = $this->actingAs($this->professorA)->delete(route('resources.destroy', $this->recursoProfessorA));
        $response->assertRedirect(route('resources.index'));
        $this->assertDatabaseMissing('recursos_didaticos', ['id_recurso' => $this->recursoProfessorA->id_recurso]);
    }

    #[Test]
    public function professor_nao_pode_destruir_recurso_de_outro_professor()
    {
        $response = $this->actingAs($this->professorA)->delete(route('resources.destroy', $this->recursoDiretorA));
        $response->assertForbidden();
    }

    #[Test]
    public function nao_pode_destruir_recurso_com_dependencias()
    {
        $turma = Turma::factory()->create(['id_escola' => $this->escolaA->id_escola]);
        $componente = ComponenteCurricular::factory()->create(['id_escola' => null, 'status' => 'aprovado']);
        
        OfertaComponente::factory()->create([
            'id_turma' => $turma->id_turma,
            'id_componente' => $componente->id_componente,
            'id_professor' => $this->professorA->id_usuario,
        ]);
        
        Agendamento::factory()->create(['id_recurso' => $this->recursoGlobal->id_recurso]);
        
        $response = $this->actingAs($this->admin)->delete(route('resources.destroy', $this->recursoGlobal));
        $response->assertRedirect(route('resources.index'));
        $response->assertSessionHas('error', 'Não é possível excluir o recurso "Global". Ele possui agendamentos associados.');
        $this->assertDatabaseHas('recursos_didaticos', ['id_recurso' => $this->recursoGlobal->id_recurso]);
    }
}