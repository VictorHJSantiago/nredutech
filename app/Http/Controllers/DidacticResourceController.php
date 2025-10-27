<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDidacticResourceRequest;
use App\Http\Requests\UpdateDidacticResourceRequest;
use App\Http\Resources\DidacticResourceResource;
use App\Models\RecursoDidatico;
use App\Models\Notificacao;
use App\Models\Usuario;
use App\Models\Escola;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;

class DidacticResourceController extends Controller
{
    public function index(Request $request)
    {
        $allowedSorts = ['id_recurso', 'nome', 'marca', 'numero_serie', 'quantidade', 'tipo', 'status', 'data_aquisicao', 'escola_nome', 'criador_nome'];
        $sortBy = $request->query('sort_by', 'id_recurso');
        $order = $request->query('order', 'asc');
        $user = Auth::user();

        if (!in_array($sortBy, $allowedSorts)) {
            $sortBy = 'id_recurso';
        }
        if (!in_array(strtolower($order), ['asc', 'desc'])) {
            $order = 'asc';
        }

        $query = RecursoDidatico::query()
                ->with(['escola', 'criador']) 
                ->leftJoin('escolas', 'recursos_didaticos.id_escola', '=', 'escolas.id_escola') 
                ->leftJoin('usuarios', 'recursos_didaticos.id_usuario_criador', '=', 'usuarios.id_usuario') 
                ->select('recursos_didaticos.*', 'escolas.nome as escola_nome', 'usuarios.nome_completo as criador_nome'); 

        if ($user->tipo_usuario !== 'administrador' && $user->id_escola) {
            $userSchoolId = $user->id_escola;
            $query->where(function ($q) use ($userSchoolId) {
                $q->where('recursos_didaticos.id_escola', $userSchoolId)
                  ->orWhereNull('recursos_didaticos.id_escola'); 
            });
        } elseif ($user->tipo_usuario !== 'administrador') {
             $query->whereNull('recursos_didaticos.id_escola'); 
        }

         $query->when($request->query('status'), function ($q, $status) {
            return $q->where('recursos_didaticos.status', $status); 
        });
        $query->when($request->query('search_nome'), function ($q, $search_nome) {
            return $q->where('recursos_didaticos.nome', 'LIKE', "%{$search_nome}%"); 
        });
        $query->when($request->query('search_marca'), function ($q, $search_marca) {
            return $q->where('recursos_didaticos.marca', 'LIKE', "%{$search_marca}%"); 
        });

         $sortColumn = match($sortBy) {
            'escola_nome' => 'escolas.nome',
            'criador_nome' => 'usuarios.nome_completo',
            default => 'recursos_didaticos.' . $sortBy,
        };
        $query->orderBy($sortColumn, $order);
        $query->orderBy('recursos_didaticos.id_recurso', 'asc');
        $recursos = $query->paginate(5)->withQueryString();

        if ($request->wantsJson()) {
            return DidacticResourceResource::collection($recursos);
        }

        return view('resources.index', [
            'recursos' => $recursos,
            'sortBy' => $sortBy,
            'order' => $order
        ]);
    }

    public function create()
    {
        $escolas = collect();
        if (Auth::user()->tipo_usuario === 'administrador') {
            $escolas = Escola::orderBy('nome')->get();
        }
        return view('resources.create', compact('escolas'));
    }

    public function store(StoreDidacticResourceRequest $request)
    {
        $validatedData = $request->validated();
        $actor = Auth::user(); 
        $validatedData['id_usuario_criador'] = $actor->id_usuario;
        if ($actor->tipo_usuario !== 'administrador') {
            $validatedData['id_escola'] = $actor->id_escola;
        } else {
            $validatedData['id_escola'] = $request->input('id_escola') ?: null;
        }

        $totalQuantidade = (int) $validatedData['quantidade'];
        $maxSplitLimit = 50;
        $escolaId = $validatedData['id_escola']; 
        $administradores = Usuario::where('tipo_usuario', 'administrador')->where('status_aprovacao', 'ativo')->get();
        $diretoresDaEscola = collect();
        if ($escolaId) {
            $diretoresDaEscola = Usuario::where('id_escola', $escolaId)
                                        ->where('tipo_usuario', 'diretor')
                                        ->where('status_aprovacao', 'ativo')
                                        ->get();
        }
        $recipients = collect([$actor])->merge($diretoresDaEscola)->merge($administradores)->unique('id_usuario');
        $createdResources = []; 
        $successMessage = ''; 
        $isSplit = $request->input('split_quantity') === 'true' && $totalQuantidade > 1 && $totalQuantidade <= $maxSplitLimit;

        if ($isSplit) {
            Arr::pull($validatedData, 'quantidade');
            $validatedData['quantidade'] = 1;
            $baseNumeroSerie = $validatedData['numero_serie'] ?? null;

            for ($i = 0; $i < $totalQuantidade; $i++) {
                if ($baseNumeroSerie) {
                    $validatedData['numero_serie'] = $baseNumeroSerie . '-' . ($i + 1);
                }
                $createdResources[] = RecursoDidatico::create($validatedData);
            }
            $successMessage = $totalQuantidade . ' recursos individuais cadastrados com sucesso!';
        } else {
            $createdResources[] = RecursoDidatico::create($validatedData);
            $successMessage = 'Lote de ' . $totalQuantidade . ' recurso(s) cadastrado com sucesso!';
        }

        if (!empty($createdResources)) {
            $firstResource = $createdResources[0]->loadMissing('escola'); 
            $escolaNome = $firstResource->escola ? $firstResource->escola->nome : 'Global'; 
            $titulo = $isSplit ? 'Novos Recursos Individuais Cadastrados' : 'Novo Lote de Recursos Cadastrado';
            $quantidadeMsg = $isSplit ? $totalQuantidade . ' unidades' : 'Um lote de ' . $totalQuantidade . ' unidade(s)';
            $localMsg = $escolaId ? " na escola '{$escolaNome}'" : " (Global)";
            $mensagem = "{$quantidadeMsg} do recurso '{$firstResource->nome}' foram cadastradas por {$actor->nome_completo}{$localMsg}.";

            foreach ($recipients as $recipient) {
                Notificacao::create([
                    'titulo' => $titulo,
                    'mensagem' => $mensagem,
                    'data_envio' => now(),
                    'status_mensagem' => 'enviada',
                    'id_usuario' => $recipient->id_usuario,
                ]);
            }
        } else {
             \Log::error('Nenhum recurso didático foi criado no método store.');
             $successMessage = 'Ocorreu um erro ao cadastrar os recursos.'; 
        }

        if ($request->wantsJson()) {
             if (!empty($createdResources)) {
                 return (new DidacticResourceResource($createdResources[0]))
                     ->response()
                     ->setStatusCode(201);
             } else {
                 return response()->json(['message' => 'Erro ao criar recurso'], 500);
             }
        }
        return redirect()->route('resources.index')
                         ->with(!empty($createdResources) ? 'success' : 'error', $successMessage);
    }


    public function edit(RecursoDidatico $recursoDidatico): View
    {
        $this->authorizeResourceAccess($recursoDidatico);

        $escolas = collect();
        if (Auth::user()->tipo_usuario === 'administrador') {
            $escolas = Escola::orderBy('nome')->get();
        }
        return view('resources.edit', compact('recursoDidatico', 'escolas'));
    }

    public function update(UpdateDidacticResourceRequest $request, RecursoDidatico $recursoDidatico)
    {
        $this->authorizeResourceAccess($recursoDidatico);
        $actor = Auth::user(); 
        $validatedData = $request->validated();
        $oldEscolaId = $recursoDidatico->id_escola; 
        if ($actor->tipo_usuario === 'administrador') {
            if ($request->has('id_escola')) {
                 $validatedData['id_escola'] = $request->input('id_escola') ?: null;
            }
        } else {
             unset($validatedData['id_escola']);
        }

        $recursoDidatico->update($validatedData);
        $recursoDidatico->refresh()->loadMissing(['escola', 'criador']); 
        $newEscolaId = $recursoDidatico->id_escola;
        $escolaNome = $recursoDidatico->escola ? $recursoDidatico->escola->nome : 'Global';
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
                        ->merge($diretoresAntigaEscola)
                        ->unique('id_usuario');

        $mensagem = "O recurso '{$recursoDidatico->nome}' (Escola: {$escolaNome}) foi atualizado por {$actor->nome_completo}.";

        foreach ($recipients as $recipient) {
            Notificacao::create([
                'titulo' => 'Recurso Didático Atualizado',
                'mensagem' => $mensagem,
                'data_envio' => now(),
                'status_mensagem' => 'enviada',
                'id_usuario' => $recipient->id_usuario,
            ]);
        }

        if ($request->wantsJson()) {
            return new DidacticResourceResource($recursoDidatico);
        }

        return redirect()->route('resources.index')
                         ->with('success', 'Recurso didático atualizado com sucesso!');
    }

     public function destroy(Request $request, RecursoDidatico $recursoDidatico)
    {
        $this->authorizeResourceAccess($recursoDidatico);
        $actor = Auth::user(); 
        $nomeRecurso = $recursoDidatico->nome;
        $escolaId = $recursoDidatico->id_escola;
        $criador = $recursoDidatico->criador; 
        $escola = $recursoDidatico->escola; 
        $escolaNome = $escola ? $escola->nome : 'Global';

        try {
             if ($recursoDidatico->agendamentos()->exists()) {
                 return redirect()->route('resources.index')
                                 ->with('error', 'Não é possível excluir o recurso "' . $nomeRecurso . '". Ele possui agendamentos associados.');
            }

            $recursoDidatico->delete();
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


            $mensagem = "O recurso '{$nomeRecurso}' (Escola: {$escolaNome}) foi excluído por {$actor->nome_completo}.";

            foreach ($recipients as $recipient) {
                Notificacao::create([
                    'titulo' => 'Recurso Didático Excluído',
                    'mensagem' => $mensagem,
                    'data_envio' => now(),
                    'status_mensagem' => 'enviada',
                    'id_usuario' => $recipient->id_usuario,
                ]);
            }


            if ($request->wantsJson()) {
                return response()->json(null, 204);
            }

            return redirect()->route('resources.index')
                             ->with('success', 'Recurso didático excluído com sucesso!');

        } catch (QueryException $e) { 
            \Log::error("Erro ao excluir recurso: " . $e->getMessage()); 
            return redirect()->route('resources.index')
                             ->with('error', 'Não foi possível excluir o recurso "' . $nomeRecurso . '". Verifique se ele não está em uso ou se há um problema no banco de dados.');
        }


    }

    private function authorizeResourceAccess(RecursoDidatico $recurso)
    {
        $user = Auth::user();
        if ($user->tipo_usuario === 'administrador') {
            return; 
        }
        if ($user->tipo_usuario === 'professor') {
            if ($recurso->id_usuario_criador !== $user->id_usuario) {
                abort(403, 'Acesso não autorizado. Professores só podem modificar os recursos que cadastraram.');
            }
        }
        $recurso->loadMissing('escola');
        if ($user->id_escola && ($recurso->id_escola === null || $recurso->id_escola === $user->id_escola)) {
            return; 
        }
        abort(403, 'Acesso não autorizado a este recurso.');
    }
}