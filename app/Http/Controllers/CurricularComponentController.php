<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCurricularComponentRequest;
use App\Http\Requests\UpdateCurricularComponentRequest;
use App\Models\ComponenteCurricular;
use App\Models\Notificacao;
use App\Models\Usuario;
use App\Models\Escola;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Database\QueryException; 

class CurricularComponentController extends Controller
{

    public function index(Request $request)
    {
        $user = Auth::user();
        $query = ComponenteCurricular::query()
            ->with(['criador', 'escola']) 
            ->leftJoin('usuarios', 'componentes_curriculares.id_usuario_criador', '=', 'usuarios.id_usuario') 
            ->leftJoin('escolas', 'componentes_curriculares.id_escola', '=', 'escolas.id_escola') 
            ->select('componentes_curriculares.*', 'usuarios.nome_completo as criador_nome', 'escolas.nome as escola_nome'); 

        if ($user->tipo_usuario !== 'administrador' && $user->id_escola) {
            $userSchoolId = $user->id_escola;
            $query->where(function ($q) use ($userSchoolId) {
                $q->where('componentes_curriculares.id_escola', $userSchoolId)
                  ->orWhereNull('componentes_curriculares.id_escola'); 
            });
        } elseif ($user->tipo_usuario !== 'administrador') {
             $query->whereNull('componentes_curriculares.id_escola');
        }
        $query->when($request->query('search_text'), function ($q, $searchText) {
            return $q->where(function ($subQ) use ($searchText) {
                $subQ->where('componentes_curriculares.nome', 'LIKE', "%{$searchText}%")
                     ->orWhere('descricao', 'LIKE', "%{$searchText}%");
            });
        });
        $query->when($request->query('search_carga'), function ($q, $searchCarga) {
            return $q->where('carga_horaria', 'LIKE', "%{$searchCarga}%");
        });
        $query->when($request->query('status'), function ($q, $status) {
            return $q->where('componentes_curriculares.status', $status); 
        });

        $sortBy = $request->query('sort_by', 'nome');
        $order = $request->query('order', 'asc');
        $allowedSorts = ['id_componente', 'nome', 'descricao', 'carga_horaria', 'status', 'criador_nome', 'escola_nome'];

        if (in_array($sortBy, $allowedSorts)) {
            $sortColumn = match($sortBy) {
                'criador_nome' => 'usuarios.nome_completo',
                'escola_nome' => 'escolas.nome', 
                default => 'componentes_curriculares.' . $sortBy,
            };
            $query->orderBy($sortColumn, $order);
        } else {
            $query->orderBy('componentes_curriculares.nome', 'asc');
        }
        $query->orderBy('componentes_curriculares.id_componente', 'asc');
        $componentes = $query->paginate(5)->withQueryString();

        return view('disciplines.index', [
            'componentes' => $componentes,
            'sortBy' => $sortBy,
            'order' => $order
        ]);
    }


    public function create(): View
    {
        $escolas = collect();
        if (Auth::user()->tipo_usuario === 'administrador') {
            $escolas = Escola::orderBy('nome')->get();
        }
        return view('disciplines.create', compact('escolas'));
    }

    public function store(StoreCurricularComponentRequest $request): RedirectResponse
    {
        $validatedData = $request->validated();
        $actor = Auth::user(); 

        $validatedData['id_usuario_criador'] = $actor->id_usuario;

        if ($actor->tipo_usuario === 'administrador') {
            $validatedData['id_escola'] = $request->input('id_escola') ?: null;
        } else {
            $validatedData['status'] = 'pendente';
            $validatedData['id_escola'] = $actor->id_escola; 
        }

        $componente = ComponenteCurricular::create($validatedData);
        $componente->loadMissing('escola');
        $escolaId = $componente->id_escola;
        $escolaNome = $componente->escola ? $componente->escola->nome : 'Global'; 

        $administradores = Usuario::where('tipo_usuario', 'administrador')->where('status_aprovacao', 'ativo')->get();
        $diretoresDaEscola = collect();
        if ($escolaId) {
            $diretoresDaEscola = Usuario::where('id_escola', $escolaId)
                                        ->where('tipo_usuario', 'diretor')
                                        ->where('status_aprovacao', 'ativo')
                                        ->get();
        }
        $recipients = collect([$actor])->merge($diretoresDaEscola)->merge($administradores)->unique('id_usuario');

        $tituloNotificacao = $componente->status === 'pendente' ? 'Nova Disciplina para Aprovação' : 'Nova Disciplina Cadastrada';
        $localMsg = $escolaId ? " para a escola '{$escolaNome}'" : " (Global)";
        $mensagemNotificacao = $componente->status === 'pendente'
            ? "A disciplina '{$componente->nome}' foi cadastrada por {$actor->nome_completo} e aguarda aprovação{$localMsg}."
            : "A disciplina '{$componente->nome}' foi cadastrada por {$actor->nome_completo} com status '{$componente->status}'{$localMsg}.";

        foreach ($recipients as $recipient) {
            Notificacao::create([
                'titulo' => $tituloNotificacao,
                'mensagem' => $mensagemNotificacao,
                'data_envio' => now(),
                'status_mensagem' => 'enviada',
                'id_usuario' => $recipient->id_usuario,
            ]);
        }

        $successMessage = 'Disciplina cadastrada com sucesso!';
        if ($actor->tipo_usuario !== 'administrador') {
            $successMessage .= ' Aguardando aprovação.';
        }

        return redirect()->route('componentes.index')->with('success', $successMessage);
    }

    public function edit(ComponenteCurricular $componente): View
    {
        $this->authorizeComponentAccess($componente);
        $escolas = collect();
        if (Auth::user()->tipo_usuario === 'administrador') {
            $escolas = Escola::orderBy('nome')->get();
        }
        $componenteCurricular = $componente;
        return view('disciplines.edit', compact('componenteCurricular', 'escolas'));
    }

    public function update(UpdateCurricularComponentRequest $request, ComponenteCurricular $componente): RedirectResponse
    {
        $this->authorizeComponentAccess($componente);
        $actor = Auth::user(); 
        $validatedData = $request->validated();

        $oldStatus = $componente->status;
        $oldEscolaId = $componente->id_escola;
        $criador = $componente->criador; 

        if ($actor->tipo_usuario === 'administrador') {
            if ($request->has('id_escola')) {
                 $validatedData['id_escola'] = $request->input('id_escola') ?: null;
            }
        } else {
             unset($validatedData['id_escola']);
             unset($validatedData['status']); 
        }

        $componente->update($validatedData);
        $componente->refresh()->loadMissing(['escola', 'criador']);

        $newStatus = $componente->status;
        $newEscolaId = $componente->id_escola;
        $escolaNome = $componente->escola ? $componente->escola->nome : 'Global';

        $administradores = Usuario::where('tipo_usuario', 'administrador')->where('status_aprovacao', 'ativo')->get();
        $diretoresNovaEscola = collect();
         if ($newEscolaId) {
            $diretoresNovaEscola = Usuario::where('id_escola', $newEscolaId)->where('tipo_usuario', 'diretor')->where('status_aprovacao', 'ativo')->get();
        }
        $diretoresAntigaEscola = collect();
        if ($oldEscolaId && $oldEscolaId !== $newEscolaId) {
             $diretoresAntigaEscola = Usuario::where('id_escola', $oldEscolaId)->where('tipo_usuario', 'diretor')->where('status_aprovacao', 'ativo')->get();
        }

        $recipients = collect([$actor])
                        ->merge($administradores)
                        ->merge($diretoresNovaEscola)
                        ->merge($diretoresAntigaEscola);

        if ($criador && $criador->id_usuario !== $actor->id_usuario && $oldStatus !== $newStatus) {
             $recipients = $recipients->push($criador);
        }
        $recipients = $recipients->unique('id_usuario');

        $mensagem = "A disciplina '{$componente->nome}' (Escola: {$escolaNome}) foi atualizada por {$actor->nome_completo}.";
        if ($oldStatus !== $newStatus) {
            $mensagem .= " Seu status mudou de '{$oldStatus}' para '{$newStatus}'.";
        }

        foreach ($recipients as $recipient) {
             $titulo = ($recipient->id_usuario === $criador?->id_usuario && $oldStatus !== $newStatus)
                        ? 'Atualização de Status da Disciplina'
                        : 'Disciplina Atualizada';

            Notificacao::create([
                'titulo' => $titulo,
                'mensagem' => $mensagem,
                'data_envio' => now(),
                'status_mensagem' => 'enviada',
                'id_usuario' => $recipient->id_usuario,
            ]);
        }

        return redirect()->route('componentes.index')->with('success', 'Disciplina atualizada com sucesso!');
    }

    public function destroy(ComponenteCurricular $componente): RedirectResponse
    {
        $this->authorizeComponentAccess($componente);
        $actor = Auth::user(); 

        if ($componente->ofertas()->exists()) {
            return redirect()->route('componentes.index')->with('error', 'Não é possível excluir uma disciplina que já está associada a uma turma.');
        }

        $nomeComponente = $componente->nome;
        $criador = $componente->criador;
        $escolaId = $componente->id_escola;
        $escola = $componente->escola;
        $escolaNome = $escola ? $escola->nome : 'Global';

        try {
             $componente->delete();

            $administradores = Usuario::where('tipo_usuario', 'administrador')->where('status_aprovacao', 'ativo')->get();
            $diretoresDaEscola = collect();
            if ($escolaId) {
                $diretoresDaEscola = Usuario::where('id_escola', $escolaId)->where('tipo_usuario', 'diretor')->where('status_aprovacao', 'ativo')->get();
            }

            $recipients = collect([$actor])->merge($diretoresDaEscola)->merge($administradores);
            if ($criador && $criador->id_usuario !== $actor->id_usuario) {
                 $recipients = $recipients->push($criador);
            }
            $recipients = $recipients->unique('id_usuario');

            $mensagem = "A disciplina '{$nomeComponente}' (Escola: {$escolaNome}) foi excluída por {$actor->nome_completo}.";

            foreach ($recipients as $recipient) {
                Notificacao::create([
                    'titulo' => 'Disciplina Excluída',
                    'mensagem' => $mensagem,
                    'data_envio' => now(),
                    'status_mensagem' => 'enviada',
                    'id_usuario' => $recipient->id_usuario,
                ]);
            }

            return redirect()->route('componentes.index')->with('success', 'Disciplina excluída com sucesso!');

        } catch (QueryException $e) {
             \Log::error("Erro ao excluir componente curricular: " . $e->getMessage());
             return redirect()->route('componentes.index')->with('error', 'Não foi possível excluir a disciplina. Verifique dependências ou contate o suporte.');
        }
    }

    private function authorizeComponentAccess(ComponenteCurricular $componente)
    {
        $user = Auth::user();
        if ($user->tipo_usuario === 'administrador') {
            return; 
        }

        if ($user->tipo_usuario === 'professor') {
            if ($componente->id_usuario_criador !== $user->id_usuario) {
                abort(403, 'Acesso não autorizado. Professores só podem modificar as disciplinas que cadastraram.');
            }
        }
        $componente->loadMissing('escola'); 
        if ($user->id_escola && ($componente->id_escola === null || $componente->id_escola === $user->id_escola)) {
            return; 
        }
        abort(403, 'Acesso não autorizado a esta disciplina.');
    }
}