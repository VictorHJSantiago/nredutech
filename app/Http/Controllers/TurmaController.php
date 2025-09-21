<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTurmaRequest;
use App\Http\Requests\UpdateTurmaRequest;
use App\Http\Resources\TurmaResource;
use App\Models\Turma;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use App\Models\Usuario;
use App\Models\ComponenteCurricular;
use App\Models\Escola; 
use App\Models\Notificacao; 
use Illuminate\Http\RedirectResponse; 

class TurmaController extends Controller
{
    public function index(Request $request): View
    {
        $usuarioLogado = Auth::user();
        $queryTurmas = Turma::query()->with('escola');
        $queryEscolas = Escola::query();

        if ($usuarioLogado->tipo_usuario !== 'administrador' && $usuarioLogado->id_escola) {
            $queryTurmas->where('id_escola', $usuarioLogado->id_escola);
            $queryEscolas->where('id_escola', $usuarioLogado->id_escola);
        }
        
        $queryTurmas->when($request->query('ano_letivo'), function ($q, $ano) {
            return $q->where('ano_letivo', $ano);
        });

        $allowedSorts = ['serie', 'turno', 'ano_letivo', 'escola_nome', 'nivel_escolaridade'];
        $sortBy = $request->query('sort_by', 'ano_letivo');
        $order = $request->query('order', 'desc');

        if (!in_array($sortBy, $allowedSorts)) {
            $sortBy = 'ano_letivo';
        }

        $sortColumn = $sortBy;
        if ($sortBy === 'escola_nome') {
            $queryTurmas->join('escolas', 'turmas.id_escola', '=', 'escolas.id_escola')
                        ->select('turmas.*', 'escolas.nome as escola_nome');
            $sortColumn = 'escolas.nome';
        }
        
        $queryTurmas->orderBy($sortColumn, $order);


        $turmas = $queryTurmas->paginate(5)->withQueryString();
        $escolas = $queryEscolas->orderBy('nome')->get();

        return view('classes.index', compact('turmas', 'escolas', 'sortBy', 'order'));
    }


    public function store(StoreTurmaRequest $request): RedirectResponse
    {
        $turma = Turma::create($request->validated());
        $diretores = Usuario::where('id_escola', $turma->id_escola)
                            ->where('tipo_usuario', 'diretor')
                            ->get();

        foreach ($diretores as $diretor) {
            Notificacao::create([
                'titulo' => 'Nova Turma Cadastrada',
                'mensagem' => "A turma '{$turma->serie} - {$turma->turno}' foi cadastrada na sua escola.",
                'data_envio' => now(),
                'status_mensagem' => 'enviada',
                'id_usuario' => $diretor->id_usuario,
            ]);
        }

        return redirect()->route('turmas.index')->with('success', 'Turma cadastrada com sucesso!');
    }

    public function show(Turma $turma): View
    {
        $this->authorizeTurmaAccess($turma);
        $turma->load([
            'escola', 
            'ofertasComponentes.professor', 
            'ofertasComponentes.componenteCurricular'
        ]);

        $queryProfessores = Usuario::whereIn('tipo_usuario', ['professor', 'diretor']);
        $queryComponentes = ComponenteCurricular::where('status', 'aprovado');

        if (Auth::user()->tipo_usuario !== 'administrador') {
             $queryProfessores->where('id_escola', $turma->id_escola);
        }

        $professores = $queryProfessores->orderBy('nome_completo')->get();
        $componentes = $queryComponentes->orderBy('nome')->get();
        
        return view('classes.show', compact('turma', 'professores', 'componentes'));
    }

    private function authorizeTurmaAccess(Turma $turma)
    {
        $usuarioLogado = Auth::user();
        if ($usuarioLogado->tipo_usuario === 'administrador') {
            return;
        }
        if ($usuarioLogado->id_escola !== $turma->id_escola) {
            abort(403, 'Acesso não autorizado a esta turma.');
        }
    }

    public function update(UpdateTurmaRequest $request, Turma $turma): TurmaResource
    {
        $this->authorizeTurmaAccess($turma);
        $turma->update($request->validated());
        return new TurmaResource($turma->fresh()->load('escola'));
    }

    public function destroy(Turma $turma): JsonResponse
    {
        $this->authorizeTurmaAccess($turma);
        
        if ($turma->ofertasComponentes()->exists()) {
             return response()->json(['message' => 'Não é possível excluir. Esta turma já possui professores/disciplinas vinculados.'], 422);
        }
        
        $nomeTurma = "{$turma->serie} ({$turma->turno})";
        $escolaId = $turma->id_escola;
        $turma->delete();
        $diretores = Usuario::where('id_escola', $escolaId)
                            ->where('tipo_usuario', 'diretor')
                            ->get();

        foreach ($diretores as $diretor) {
            Notificacao::create([
                'titulo' => 'Turma Excluída',
                'mensagem' => "A turma '{$nomeTurma}' foi excluída da sua escola.",
                'data_envio' => now(),
                'status_mensagem' => 'enviada',
                'id_usuario' => $diretor->id_usuario,
            ]);
        }
        
        return response()->json(null, 204);
    }
}