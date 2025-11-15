<?php

namespace Tests\Feature\Disciplines;

use App\Models\ComponenteCurricular;
use App\Models\Escola;
use App\Models\Municipio;
use App\Models\OfertaComponente;
use App\Models\Turma;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class CurricularComponentControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $diretor;
    protected $professor;
    protected $escola;
    protected $outraEscola;
    protected $componenteGlobal;
    protected $componenteEscola;
    protected $componenteOutraEscola;
    protected $municipio;

    protected function setUp(): void
    {
        parent::setUp();

        $this->municipio = Municipio::create(['nome' => 'Municipio Teste', 'estado' => 'PR']);
        
        $this->escola = Escola::create(['nome' => 'Escola Teste', 'id_municipio' => $this->municipio->id_municipio, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana']);
        $this->outraEscola = Escola::create(['nome' => 'Outra Escola', 'id_municipio' => $this->municipio->id_municipio, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana']);

        $this->admin = Usuario::factory()->create(['tipo_usuario' => 'administrador']);
        $this->diretor = Usuario::factory()->create(['tipo_usuario' => 'diretor', 'id_escola' => $this->escola->id_escola]);
        $this->professor = Usuario::factory()->create(['tipo_usuario' => 'professor', 'id_escola' => $this->escola->id_escola]);

        $this->componenteGlobal = ComponenteCurricular::create(['nome' => 'Matemática Global', 'carga_horaria' => 120, 'id_escola' => null, 'id_usuario_criador' => $this->admin->id_usuario, 'status' => 'aprovado', 'descricao' => 'Descrição Padrão']);
        $this->componenteEscola = ComponenteCurricular::create(['nome' => 'Português Escola', 'carga_horaria' => 100, 'id_escola' => $this->escola->id_escola, 'id_usuario_criador' => $this->diretor->id_usuario, 'status' => 'aprovado', 'descricao' => 'Descrição Padrão']);
        $this->componenteOutraEscola = ComponenteCurricular::create(['nome' => 'História Outra Escola', 'carga_horaria' => 80, 'id_escola' => $this->outraEscola->id_escola, 'id_usuario_criador' => $this->admin->id_usuario, 'status' => 'aprovado', 'descricao' => 'Descrição Padrão']);
    }

    #[Test('Admin pode ver todos componentes no index')]
    public function admin_pode_ver_todos_componentes_no_index()
    {
        $response = $this->actingAs($this->admin)->get(route('componentes.index'));
        $response->assertStatus(200);
        $response->assertSee($this->componenteGlobal->nome);
        $response->assertSee($this->componenteEscola->nome);
        $response->assertSee($this->componenteOutraEscola->nome);
    }

    #[Test('Diretor pode ver componentes da própria escola e globais no index')]
    public function diretor_pode_ver_componentes_da_propria_escola_e_globais_no_index()
    {
        $response = $this->actingAs($this->diretor)->get(route('componentes.index'));
        $response->assertStatus(200);
        $response->assertSee($this->componenteGlobal->nome);
        $response->assertSee($this->componenteEscola->nome);
        $response->assertDontSee($this->componenteOutraEscola->nome);
    }

    #[Test('Professor pode ver componentes da própria escola e globais no index')]
    public function professor_pode_ver_componentes_da_propria_escola_e_globais_no_index()
    {
        $response = $this->actingAs($this->professor)->get(route('componentes.index'));
        $response->assertStatus(200);
        $response->assertSee($this->componenteGlobal->nome);
        $response->assertSee($this->componenteEscola->nome);
        $response->assertDontSee($this->componenteOutraEscola->nome);
    }

    #[Test('Admin pode filtrar componentes por escola')]
    public function admin_pode_filtrar_componentes_por_escola()
    {
        $response = $this->actingAs($this->admin)->get(route('componentes.index', ['id_escola' => $this->escola->id_escola]));
        $response->assertStatus(200);
        $response->assertSee($this->componenteGlobal->nome);
        $response->assertSee($this->componenteEscola->nome);
        $response->assertSee($this->componenteOutraEscola->nome);
    }

    #[Test('Admin pode filtrar componentes por global')]
    public function admin_pode_filtrar_componentes_por_global()
    {
        $response = $this->actingAs($this->admin)->get(route('componentes.index', ['id_escola' => 'global']));
        $response->assertStatus(200);
        $response->assertSee($this->componenteGlobal->nome);
        $response->assertSee($this->componenteEscola->nome);
        $response->assertSee($this->componenteOutraEscola->nome);
    }

    #[Test('Admin pode cadastrar componente global')]
    public function admin_pode_cadastrar_componente_global()
    {
        $data = ['nome' => 'Geografia Global', 'carga_horaria' => '80', 'id_escola' => null, 'status' => 'aprovado', 'descricao' => 'Descrição Padrão'];
        $response = $this->actingAs($this->admin)->post(route('componentes.store'), $data);
        $response->assertRedirect(route('componentes.index'));
        $this->assertDatabaseHas('componentes_curriculares', ['nome' => 'Geografia Global', 'id_escola' => null]);
    }

    #[Test('Admin pode cadastrar componente de escola')]
    public function admin_pode_cadastrar_componente_de_escola()
    {
        $data = ['nome' => 'Geografia Escola', 'carga_horaria' => '80', 'id_escola' => $this->escola->id_escola, 'status' => 'aprovado', 'descricao' => 'Descrição Padrão'];
        $response = $this->actingAs($this->admin)->post(route('componentes.store'), $data);
        $response->assertRedirect(route('componentes.index'));
        $this->assertDatabaseHas('componentes_curriculares', ['nome' => 'Geografia Escola', 'id_escola' => $this->escola->id_escola]);
    }

    #[Test('Diretor pode cadastrar componente da própria escola')]
    public function diretor_pode_cadastrar_componente_da_propria_escola()
    {
        $data = ['nome' => 'Física Escola', 'carga_horaria' => '60', 'id_escola' => $this->escola->id_escola, 'status' => 'aprovado', 'descricao' => 'Descrição Padrão'];
        $response = $this->actingAs($this->diretor)->post(route('componentes.store'), $data);
        $response->assertRedirect(route('componentes.index'));
        $this->assertDatabaseHas('componentes_curriculares', ['nome' => 'Física Escola', 'id_escola' => $this->escola->id_escola]);
    }

    #[Test('Diretor não pode cadastrar componente global')]
    public function diretor_nao_pode_cadastrar_componente_global()
    {
        $data = ['nome' => 'Física Global', 'carga_horaria' => '60', 'id_escola' => null, 'status' => 'aprovado', 'descricao' => 'Descrição Padrão'];
        $response = $this->actingAs($this->diretor)->post(route('componentes.store'), $data);
        $response->assertRedirect();
    }

    #[Test('Diretor não pode cadastrar componente de outra escola')]
    public function diretor_nao_pode_cadastrar_componente_de_outra_escola()
    {
        $data = ['nome' => 'Física Outra Escola', 'carga_horaria' => '60', 'id_escola' => $this->outraEscola->id_escola, 'status' => 'aprovado', 'descricao' => 'Descrição Padrão'];
        $response = $this->actingAs($this->diretor)->post(route('componentes.store'), $data);
        $response->assertRedirect();
    }

    #[Test('Admin pode atualizar qualquer componente')]
    public function admin_pode_atualizar_qualquer_componente()
    {
        $data = ['nome' => 'Matemática Global Editada', 'carga_horaria' => '130', 'status' => 'aprovado', 'descricao' => 'Descrição Padrão'];
        $response = $this->actingAs($this->admin)->put(route('componentes.update', $this->componenteGlobal), $data);
        $response->assertRedirect(route('componentes.index'));
        $this->assertDatabaseHas('componentes_curriculares', ['id_componente' => $this->componenteGlobal->id_componente, 'nome' => 'Matemática Global Editada']);
    }

    #[Test('Diretor pode atualizar componente da própria escola')]
    public function diretor_pode_atualizar_componente_da_propria_escola()
    {
        $data = ['nome' => 'Português Escola Editado', 'carga_horaria' => '110', 'status' => 'aprovado', 'descricao' => 'Descrição Padrão'];
        $response = $this->actingAs($this->diretor)->put(route('componentes.update', $this->componenteEscola), $data);
        $response->assertRedirect(route('componentes.index'));
        $this->assertDatabaseHas('componentes_curriculares', ['id_componente' => $this->componenteEscola->id_componente, 'nome' => 'Português Escola Editado']);
    }

    #[Test('Diretor não pode atualizar componente de outra escola')]
    public function diretor_nao_pode_atualizar_componente_de_outra_escola()
    {
        $data = ['nome' => 'História Editada', 'carga_horaria' => '80', 'status' => 'aprovado', 'descricao' => 'Descrição Padrão'];
        $response = $this->actingAs($this->diretor)->put(route('componentes.update', $this->componenteOutraEscola), $data);
        $response->assertForbidden();
    }

    #[Test('Diretor não pode atualizar componente global')]
    public function diretor_nao_pode_atualizar_componente_global()
    {
        $data = ['nome' => 'Matemática Global Editada', 'carga_horaria' => '120', 'status' => 'aprovado', 'descricao' => 'Descrição Padrão'];
        $response = $this->actingAs($this->diretor)->put(route('componentes.update', $this->componenteGlobal), $data);
        $response->assertRedirect();
    }

    #[Test('Admin pode destruir componente')]
    public function admin_pode_destruir_componente()
    {
        $response = $this->actingAs($this->admin)->delete(route('componentes.destroy', $this->componenteGlobal));
        $response->assertRedirect(route('componentes.index'));
        $this->assertDatabaseMissing('componentes_curriculares', ['id_componente' => $this->componenteGlobal->id_componente]);
    }

    #[Test('Diretor pode destruir componente da própria escola')]
    public function diretor_pode_destruir_componente_da_propria_escola()
    {
        $response = $this->actingAs($this->diretor)->delete(route('componentes.destroy', $this->componenteEscola));
        $response->assertRedirect(route('componentes.index'));
        $this->assertDatabaseMissing('componentes_curriculares', ['id_componente' => $this->componenteEscola->id_componente]);
    }

    #[Test('Diretor não pode destruir componente de outra escola')]
    public function diretor_nao_pode_destruir_componente_de_outra_escola()
    {
        $response = $this->actingAs($this->diretor)->delete(route('componentes.destroy', $this->componenteOutraEscola));
        $response->assertForbidden();
    }

    #[Test('Diretor não pode destruir componente global')]
    public function diretor_nao_pode_destruir_componente_global()
    {
        $response = $this->actingAs($this->diretor)->delete(route('componentes.destroy', $this->componenteGlobal));
        $response->assertRedirect();
    }

    #[Test('Não pode destruir componente com dependências')]
    public function nao_pode_destruir_componente_com_dependencias()
    {
        $turma = Turma::create([
            'serie' => '1ª Série',
            'turno' => 'manha',
            'ano_letivo' => date('Y'),
            'nivel_escolaridade' => 'medio',
            'id_escola' => $this->escola->id_escola,
        ]);

        OfertaComponente::create([
            'id_componente' => $this->componenteEscola->id_componente,
            'id_turma' => $turma->id_turma,
            'id_professor' => $this->professor->id_usuario,
            'status' => 'aberta',
        ]);
        
        $response = $this->actingAs($this->admin)->delete(route('componentes.destroy', $this->componenteEscola));
        $response->assertRedirect(route('componentes.index'));
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('componentes_curriculares', ['id_componente' => $this->componenteEscola->id_componente]);
    }
}