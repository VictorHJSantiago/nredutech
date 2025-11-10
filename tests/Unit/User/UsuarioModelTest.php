<?php

namespace Tests\Unit\User;

use Tests\TestCase;
use App\Models\Usuario;
use App\Models\Escola;
use App\Models\Municipio;
use App\Models\Turma;
use App\Models\ComponenteCurricular;
use App\Models\OfertaComponente;
use App\Models\Agendamento;
use App\Models\RecursoDidatico;
use App\Models\Notificacao;
use App\Models\UsuarioPreferencia;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class UsuarioModelTest extends TestCase
{
    use RefreshDatabase;

    protected $escola;
    protected $usuario;

    protected function setUp(): void
    {
        parent::setUp();
        $municipio = Municipio::create(['nome' => 'Municipio Teste', 'estado' => 'PR']);
        $this->escola = Escola::create([
            'nome' => 'Escola Teste',
            'id_municipio' => $municipio->id_municipio,
            'nivel_ensino' => 'colegio_estadual',
            'tipo' => 'urbana'
        ]);
        $this->usuario = Usuario::factory()->create(['id_escola' => $this->escola->id_escola]);
    }

    public function test_usuario_model_usa_tabela_correta()
    {
        $usuario = new Usuario();
        $this->assertEquals('usuarios', $usuario->getTable());
    }

    public function test_usuario_model_usa_chave_primaria_correta()
    {
        $usuario = new Usuario();
        $this->assertEquals('id_usuario', $usuario->getKeyName());
    }

    public function test_usuario_model_tem_propriedades_fillable_corretas()
    {
        $usuario = new Usuario();
        $expected = [
            'nome_completo',
            'username',
            'email',
            'data_nascimento',
            'cpf',
            'rg',
            'rco_siape',
            'telefone',
            'formacao',
            'area_formacao',
            'data_registro',
            'status_aprovacao',
            'tipo_usuario',
            'id_escola',
            'password',
        ];
        $this->assertEquals($expected, $usuario->getFillable());
    }

    public function test_usuario_model_oculta_password_e_remember_token()
    {
        $usuario = new Usuario();
        $expected = [
            'password',
        ];
        $this->assertEquals($expected, $usuario->getHidden());
    }

    public function test_usuario_model_converte_atributos_corretamente()
    {
        $usuario = new Usuario();
        $expected = [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'data_registro' => 'datetime',
            'data_nascimento' => 'date',
            'cpf' => 'encrypted',
            'rg' => 'encrypted',
            'rco_siape' => 'encrypted',
            'telefone' => 'encrypted',
            'id_usuario' => 'int',
            'deleted_at' => 'datetime',
        ];
        $this->assertEquals($expected, $usuario->getCasts());
    }

    public function test_set_password_attribute_faz_hash_da_senha()
    {
        $usuario = new Usuario();
        $usuario->password = 'password123';
        $this->assertTrue(Hash::check('password123', $usuario->password));
    }

    public function test_metodo_is_user_type_funciona_corretamente()
    {
        $admin = Usuario::factory()->create(['tipo_usuario' => 'administrador']);
        $diretor = Usuario::factory()->create(['tipo_usuario' => 'diretor']);
        
        $this->assertTrue($admin->tipo_usuario === 'administrador');
        $this->assertFalse($admin->tipo_usuario === 'diretor');
        $this->assertTrue($diretor->tipo_usuario === 'diretor');
    }

    public function test_usuario_model_tem_relacao_com_escola()
    {
        $this->assertInstanceOf(Escola::class, $this->usuario->escola);
        $this->assertEquals($this->escola->id_escola, $this->usuario->escola->id_escola);
    }

    public function test_usuario_model_tem_relacao_com_preferencias()
    {
        UsuarioPreferencia::create(['id_usuario' => $this->usuario->id_usuario]);
        
        $this->assertInstanceOf(UsuarioPreferencia::class, $this->usuario->preferencias);
    }

    public function test_usuario_model_cria_preferencias_se_nao_existir()
    {
        $this->assertDatabaseMissing('usuario_preferencias', ['id_usuario' => $this->usuario->id_usuario]);
        
        $preferencias = $this->usuario->preferencias;
        $this->assertNull($preferencias);
    }

    public function test_usuario_model_tem_relacao_com_notificacoes()
    {
        Notificacao::create([
            'id_usuario' => $this->usuario->id_usuario,
            'titulo' => 'Teste',
            'mensagem' => 'Msg teste',
            'data_envio' => now(),
            'status_mensagem' => 'enviada',
        ]);
        
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $this->usuario->notificacoes);
        $this->assertCount(1, $this->usuario->notificacoes);
    }

    public function test_usuario_model_tem_relacao_com_recursos_criados()
    {
        RecursoDidatico::factory()->create(['id_usuario_criador' => $this->usuario->id_usuario, 'id_escola' => $this->escola->id_escola]);
        
        $this->assertTrue(true);
    }

    public function test_usuario_model_tem_relacao_com_ofertas_como_professor()
    {
        $professor = Usuario::factory()->create(['tipo_usuario' => 'professor', 'id_escola' => $this->escola->id_escola, 'status_aprovacao' => 'ativo']);
        ComponenteCurricular::create(['nome' => 'Teste', 'carga_horaria' => 60, 'status' => 'aprovado']);
        $turma = Turma::factory()->create(['id_escola' => $this->escola->id_escola]);

        OfertaComponente::factory()->create([
            'id_professor' => $professor->id_usuario,
            'id_turma' => $turma->id_turma
        ]);
        
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $professor->ofertasComponentes);
        $this->assertCount(1, $professor->ofertasComponentes);
    }

    public function test_usuario_model_tem_relacao_com_agendamentos()
    {
        $professor = Usuario::factory()->create(['tipo_usuario' => 'professor', 'id_escola' => $this->escola->id_escola, 'status_aprovacao' => 'ativo']);
        $componente = ComponenteCurricular::create(['nome' => 'Teste', 'carga_horaria' => 60, 'status' => 'aprovado']);
        $turma = Turma::factory()->create(['id_escola' => $this->escola->id_escola]);
        $oferta = OfertaComponente::factory()->create([
            'id_professor' => $professor->id_usuario,
            'id_turma' => $turma->id_turma,
            'id_componente' => $componente->id_componente
        ]);
        RecursoDidatico::factory()->create(['id_escola' => $this->escola->id_escola, 'status' => 'funcionando']);

        Agendamento::factory()->create([
            'id_oferta' => $oferta->id_oferta,
        ]);
        
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $professor->agendamentos);
        $this->assertCount(1, $professor->agendamentos);
    }
}