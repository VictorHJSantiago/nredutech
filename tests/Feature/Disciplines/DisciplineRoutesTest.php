<?php

namespace Tests\Feature\Disciplines;

use App\Models\ComponenteCurricular;
use App\Models\Escola;
use App\Models\Municipio;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class DisciplineRoutesTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $diretor;
    protected $professor;
    protected $escola;
    protected $outraEscola;
    protected $componenteEscola;
    protected $componenteOutraEscola;

    protected function setUp(): void
    {
        parent::setUp();

        $municipio = Municipio::create(['nome' => 'Municipio Teste', 'estado' => 'PR']);
        $this->escola = Escola::create(['nome' => 'Escola Teste', 'id_municipio' => $municipio->id_municipio, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana']);
        $this->outraEscola = Escola::create(['nome' => 'Outra Escola', 'id_municipio' => $municipio->id_municipio, 'nivel_ensino' => 'colegio_estadual', 'tipo' => 'urbana']);

        $this->admin = Usuario::factory()->create(['tipo_usuario' => 'administrador']);
        $this->diretor = Usuario::factory()->create(['tipo_usuario' => 'diretor', 'id_escola' => $this->escola->id_escola]);
        $this->professor = Usuario::factory()->create(['tipo_usuario' => 'professor', 'id_escola' => $this->escola->id_escola]);
        
        $this->componenteEscola = ComponenteCurricular::create(['nome' => 'Componente 1', 'carga_horaria' => 80, 'id_escola' => $this->escola->id_escola, 'id_usuario_criador' => $this->diretor->id_usuario, 'status' => 'aprovado', 'descricao' => 'Descrição Padrão']);
        $this->componenteOutraEscola = ComponenteCurricular::create(['nome' => 'Componente 2', 'carga_horaria' => 80, 'id_escola' => $this->outraEscola->id_escola, 'id_usuario_criador' => $this->admin->id_usuario, 'status' => 'aprovado', 'descricao' => 'Descrição Padrão']);
    }

    #[Test('Convidado é redirecionado de todas rotas de disciplina')]
    public function convidado_e_redirecionado_de_todas_rotas_de_disciplina()
    {
        $this->get(route('componentes.index'))->assertRedirect(route('login'));
        $this->post(route('componentes.store'))->assertRedirect(route('login'));
        $this->get(route('componentes.edit', $this->componenteEscola))->assertRedirect(route('login'));
        $this->put(route('componentes.update', $this->componenteEscola))->assertRedirect(route('login'));
        $this->delete(route('componentes.destroy', $this->componenteEscola))->assertRedirect(route('login'));
    }

    #[Test('Admin pode acessar todas rotas de disciplina')]
    public function admin_pode_acessar_todas_rotas_de_disciplina()
    {
        $this->actingAs($this->admin)->get(route('componentes.index'))->assertOk();
        $this->actingAs($this->admin)->post(route('componentes.store', ['nome' => 'Teste', 'carga_horaria' => '80', 'id_escola' => $this->escola->id_escola, 'status' => 'aprovado', 'descricao' => 'Descrição Padrão']))->assertRedirect();
        $this->actingAs($this->admin)->get(route('componentes.edit', $this->componenteEscola))->assertOk();
        $this->actingAs($this->admin)->put(route('componentes.update', $this->componenteEscola), ['nome' => 'Teste Update', 'carga_horaria' => '90', 'status' => 'aprovado', 'descricao' => 'Descrição Padrão'])->assertRedirect();
        $this->actingAs($this->admin)->delete(route('componentes.destroy', $this->componenteEscola))->assertRedirect();
    }

    #[Test('Diretor pode acessar rotas de disciplina da própria escola')]
    public function diretor_pode_acessar_rotas_de_disciplina_da_propria_escola()
    {
        $this->actingAs($this->diretor)->get(route('componentes.index'))->assertOk();
        $this->actingAs($this->diretor)->post(route('componentes.store', ['nome' => 'Teste', 'carga_horaria' => '80', 'id_escola' => $this->escola->id_escola, 'status' => 'aprovado', 'descricao' => 'Descrição Padrão']))->assertRedirect();
        $this->actingAs($this->diretor)->get(route('componentes.edit', $this->componenteEscola))->assertOk();
        $this->actingAs($this->diretor)->put(route('componentes.update', $this->componenteEscola), ['nome' => 'Teste Update', 'carga_horaria' => '90', 'status' => 'aprovado', 'descricao' => 'Descrição Padrão'])->assertRedirect();
        $this->actingAs($this->diretor)->delete(route('componentes.destroy', $this->componenteEscola))->assertRedirect();
    }

    #[Test('Diretor é proibido nas rotas de disciplina de outra escola')]
    public function diretor_e_proibido_nas_rotas_de_disciplina_de_outra_escola()
    {
        $this->actingAs($this->diretor)->get(route('componentes.edit', $this->componenteOutraEscola))->assertForbidden();
        $this->actingAs($this->diretor)->put(route('componentes.update', $this->componenteOutraEscola), ['nome' => 'Teste Update', 'carga_horaria' => '90', 'status' => 'aprovado', 'descricao' => 'Descrição Padrão'])->assertForbidden();
        $this->actingAs($this->diretor)->delete(route('componentes.destroy', $this->componenteOutraEscola))->assertForbidden();
    }

    #[Test('Professor pode acessar apenas index')]
    public function professor_pode_acessar_apenas_index()
    {
        $this->actingAs($this->professor)->get(route('componentes.index'))->assertOk();
    }

    #[Test('Professor é proibido nas rotas de modificação de disciplina')]
    public function professor_e_proibido_nas_rotas_de_modificacao_de_disciplina()
    {
        $this->actingAs($this->professor)->post(route('componentes.store'), ['nome' => 'Teste', 'carga_horaria' => '80', 'id_escola' => $this->escola->id_escola, 'status' => 'aprovado', 'descricao' => 'Descrição Padrão'])->assertRedirect();
        $this->actingAs($this->professor)->get(route('componentes.edit', $this->componenteEscola))->assertForbidden();
        $this->actingAs($this->professor)->put(route('componentes.update', $this->componenteEscola), ['nome' => 'Teste Update', 'carga_horaria' => '90', 'status' => 'aprovado', 'descricao' => 'Descrição Padrão'])->assertForbidden();
        $this->actingAs($this->professor)->delete(route('componentes.destroy', $this->componenteEscola))->assertForbidden();
    }
}