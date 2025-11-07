<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSchoolClassRequest;
use App\Http\Requests\UpdateSchoolClassRequest;
use App\Models\Turma;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use App\Models\Usuario;
use App\Models\ComponenteCurricular;
use App\Models\Escola;
use App\Models\Notificacao;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Gate;

class SchoolClassController extends Controller
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

    public function store(StoreSchoolClassRequest $request): RedirectResponse
    {
        $turma = Turma::create($request->validated());
        
        $this->sendClassChangeNotification(
            $turma,
            Auth::user(),
            'criada'
        );

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
        $user = Auth::user();
        if ($user->tipo_usuario !== 'administrador' && $turma->id_escola) {
            $turmaEscolaId = $turma->id_escola; 
            $queryComponentes->where(function ($q) use ($turmaEscolaId) {
                $q->where('id_escola', $turmaEscolaId)
                ->orWhereNull('id_escola'); 
            });
            $queryProfessores->where('id_escola', $turma->id_escola);
        } elseif ($user->tipo_usuario !== 'administrador') {
            $queryComponentes->whereNull('id_escola');
        }
        $professores = $queryProfessores->orderBy('nome_completo')->get();
        $componentes = $queryComponentes->orderBy('nome')->get(); 
        return view('classes.show', compact('turma', 'professores', 'componentes'));
    }

    public function edit(Turma $turma): View
    {
        $this->authorizeTurmaAccess($turma);
        $usuarioLogado = Auth::user();
        $queryEscolas = Escola::query();

        if ($usuarioLogado->tipo_usuario !== 'administrador' && $usuarioLogado->id_escola) {
            $queryEscolas->where('id_escola', $usuarioLogado->id_escola);
        }
        $escolas = $queryEscolas->orderBy('nome')->get();

        return view('classes.edit', compact('turma', 'escolas'));
    }

    public function update(UpdateSchoolClassRequest $request, Turma $turma): RedirectResponse 
    {
        $this->authorizeTurmaAccess($turma);
        $turma->update($request->validated());
        
        $this->sendClassChangeNotification(
            $turma,
            Auth::user(),
            'atualizada'
        );
        
        return Redirect::route('turmas.index')->with('success', 'Turma atualizada com sucesso!');
    }

    public function destroy(Turma $turma): RedirectResponse 
    {
        $this->authorizeTurmaAccess($turma);

        if ($turma->ofertasComponentes()->exists()) {
             return Redirect::back()->with('error', 'Não é possível excluir. Esta turma já possui professores/disciplinas vinculados.');
        }

        $this->sendClassChangeNotification(
            $turma,
            Auth::user(),
            'excluída'
        );

        $turma->delete();
        
        return Redirect::route('turmas.index')->with('success', 'Turma excluída com sucesso!');
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

    private function sendClassChangeNotification(Turma $turma, Usuario $actionUser, string $action)
    {
        $turma->loadMissing('escola'); 
        
        $turmaNome = "{$turma->serie} - {$turma->turno}";
        $message = "A turma '{$turmaNome}' (Escola: {$turma->escola->nome}) foi {$action} pelo usuário {$actionUser->nome_completo}.";
        $url = route('turmas.index');

        $adminIds = Usuario::where('tipo_usuario', 'administrador')
                           ->where('id_usuario', '!=', $actionUser->id_usuario)
                           ->pluck('id_usuario');

        $directorIds = Usuario::where('tipo_usuario', 'diretor')
                              ->where('id_escola', $turma->id_escola)
                              ->where('id_usuario', '!=', $actionUser->id_usuario)
                              ->pluck('id_usuario');

        $recipientIds = $adminIds->merge($directorIds)
                                ->push($actionUser->id_usuario)
                                ->unique()
                                ->all();

        if (!empty($recipientIds)) {
            $notificationData = [
                'titulo' => "Turma " . ucfirst($action),
                'mensagem' => $message,
                'data_envio' => now(),
                'status_mensagem' => 'enviada',
                'url' => $url,
            ];

            foreach ($recipientIds as $userId) {
                $notificationData['id_usuario'] = $userId;
                Notificacao::create($notificationData);
            }
        }
    }
}