<?php

namespace Tests\Feature\SchoolClass; 

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Usuario;
use App\Models\Municipio;
use App\Models\Escola;
use App\Models\Turma;
use App\Models\ComponenteCurricular;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;

#[RunTestsInSeparateProcesses]
class SchoolClassRoutesTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $diretor;
    protected $professor;
    protected $turmaEscolaDiretor;
    protected $turmaOutraEscola;

    protected function setUp(): void
    {
        parent::setUp();
        
        $municipio = Municipio::create(['nome' => 'Municipio Teste']);
        
        $escolaDiretor = Escola::create([
            'nome' => 'Escola Diretor',
            'id_municipio' => $municipio->id_municipio,
            'nivel_ensino' => 'colegio_estadual',
            'tipo' => 'urbana'
        ]);
        
        $outraEscola = Escola::create([
            'nome' => 'Outra Escola',
            'id_municipio' => $municipio->id_municipio,
            'nivel_ensino' => 'escola_municipal',
            'tipo' => 'rural'
        ]);

        $this->admin = Usuario::factory()->create(['tipo_usuario' => 'administrador']);
        $this->diretor = Usuario::factory()->create(['tipo_usuario' => 'diretor', 'id_escola' => $escolaDiretor->id_escola]);
        $this->professor = Usuario::factory()->create(['tipo_usuario' => 'professor', 'id_escola' => $escolaDiretor->id_escola]);

        // Adicionando dependências para a TurmaFactory (que depende da OfertaFactory)
        ComponenteCurricular::create([
            'nome' => 'Matemática',
            'status' => 'aprovado',
            'carga_horaria' => 60
        ]);
        Usuario::factory()->create([
            'id_escola' => $escolaDiretor->id_escola,
            'tipo_usuario' => 'professor',
            'status_aprovacao' => 'ativo'
        ]);
        Usuario::factory()->create([
            'id_escola' => $outraEscola->id_escola,
            'tipo_usuario' => 'professor',
            'status_aprovacao' => 'ativo'
        ]);


        $this->turmaEscolaDiretor = Turma::factory()->create(['id_escola' => $escolaDiretor->id_escola]);
        $this->turmaOutraEscola = Turma::factory()->create(['id_escola' => $outraEscola->id_escola]);
    }

    #[Test]
    #[DataProvider('usuariosAutorizadosProvider')]
    public function usuarios_autorizados_podem_acessar_index_turmas($tipoUsuario)
    {
        $user = $this->getUserByType($tipoUsuario);
        $response = $this->actingAs($user)->get(route('turmas.index'));
        $response->assertStatus(200);
    }

    #[Test]
    public function guest_e_redirecionado_de_index_turmas()
    {
        $response = $this->get(route('turmas.index'));
        $response->assertRedirect(route('login'));
    }

    #[Test]
    #[DataProvider('usuariosAutorizadosProvider')]
    public function usuarios_autorizados_podem_enviar_store_turma($tipoUsuario)
    {
        $user = $this->getUserByType($tipoUsuario);
        $escolaId = ($tipoUsuario === 'administrador') ? $this->turmaOutraEscola->id_escola : $user->id_escola;
        $dados = Turma::factory()->make(['id_escola' => $escolaId])->toArray();

        $response = $this->actingAs($user)->post(route('turmas.store'), $dados);
        $response->assertRedirect(route('turmas.index'));
    }

    #[Test]
    public function admin_pode_ver_show_qualquer_turma()
    {
        $response = $this->actingAs($this->admin)->get(route('turmas.show', $this->turmaEscolaDiretor));
        $response->assertStatus(200);
        $response = $this->actingAs($this->admin)->get(route('turmas.show', $this->turmaOutraEscola));
        $response->assertStatus(200);
    }

    #[Test]
    public function diretor_pode_ver_show_turma_sua_escola()
    {
        $response = $this->actingAs($this->diretor)->get(route('turmas.show', $this->turmaEscolaDiretor));
        $response->assertStatus(200);
    }

    #[Test]
    public function diretor_nao_pode_ver_show_turma_outra_escola()
    {
        $response = $this->actingAs($this->diretor)->get(route('turmas.show', $this->turmaOutraEscola));
        $response->assertStatus(403);
    }

    #[Test]
    public function professor_pode_ver_show_turma_sua_escola() 
    {
        $response = $this->actingAs($this->professor)->get(route('turmas.show', $this->turmaEscolaDiretor));
        $response->assertStatus(200);
    }

    #[Test]
    public function professor_nao_pode_ver_show_turma_outra_escola()
    {
        $response = $this->actingAs($this->professor)->get(route('turmas.show', $this->turmaOutraEscola));
        $response->assertStatus(403);
    }

    #[Test]
    public function diretor_nao_pode_acessar_edit_turma_outra_escola()
    {
        $response = $this->actingAs($this->diretor)->get(route('turmas.edit', $this->turmaOutraEscola));
        $response->assertStatus(403);
    }

    #[Test]
    public function diretor_nao_pode_enviar_update_turma_outra_escola()
    {
        $response = $this->actingAs($this->diretor)->put(route('turmas.update', $this->turmaOutraEscola), ['serie' => 'Tentativa']);
        $response->assertStatus(403);
    }

    #[Test]
    public function diretor_nao_pode_enviar_destroy_turma_outra_escola()
    {
        $response = $this->actingAs($this->diretor)->delete(route('turmas.destroy', $this->turmaOutraEscola));
        $response->assertStatus(403);
    }

    private function getUserByType(string $type): Usuario
    {
        return match ($type) {
            'administrador' => $this->admin,
            'diretor' => $this->diretor,
            'professor' => $this->professor,
            default => throw new \Exception('Tipo de usuário inválido para teste'),
        };
    }

    public static function usuariosAutorizadosProvider(): array
    {
        return [
            ['administrador'],
            ['diretor'],
            ['professor'],
        ];
    }
}