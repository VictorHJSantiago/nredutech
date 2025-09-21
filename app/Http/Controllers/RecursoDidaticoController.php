<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRecursoDidaticoRequest;
use App\Http\Requests\UpdateRecursoDidaticoRequest;
use App\Http\Resources\RecursoDidaticoResource;
use App\Models\RecursoDidatico;
use App\Models\Notificacao;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Arr;

class RecursoDidaticoController extends Controller
{
    public function index(Request $request)
    {
        $allowedSorts = ['id_recurso', 'nome', 'marca', 'numero_serie', 'quantidade', 'tipo', 'status', 'data_aquisicao'];
        $sortBy = $request->query('sort_by', 'id_recurso');
        $order = $request->query('order', 'asc');

        if (!in_array($sortBy, $allowedSorts)) {
            $sortBy = 'id_recurso';
        }
        if (!in_array(strtolower($order), ['asc', 'desc'])) {
            $order = 'asc';
        }

        $query = RecursoDidatico::query();

        $query->when($request->query('status'), function ($q, $status) {
            return $q->where('status', $status);
        });
        $query->when($request->query('search_nome'), function ($q, $search_nome) {
            return $q->where('nome', 'LIKE', "%{$search_nome}%");
        });
        $query->when($request->query('search_marca'), function ($q, $search_marca) {
            return $q->where('marca', 'LIKE', "%{$search_marca}%");
        });

        $query->orderBy($sortBy, $order);

        $recursos = $query->paginate(5)->withQueryString();

        if ($request->wantsJson()) {
            return RecursoDidaticoResource::collection($recursos);
        }

        return view('resources.index', [
            'recursos' => $recursos,
            'sortBy' => $sortBy,
            'order' => $order
        ]);
    }

    public function create()
    {
        return view('resources.create');
    }

    public function store(StoreRecursoDidaticoRequest $request)
    {
        $validatedData = $request->validated();
        $totalQuantidade = (int) $validatedData['quantidade'];
        $maxSplitLimit = 50; 

        $usersToNotify = Usuario::whereIn('tipo_usuario', ['administrador', 'diretor'])->get();

        if ($request->input('split_quantity') === 'true' && $totalQuantidade > 1 && $totalQuantidade <= $maxSplitLimit) {
            
            Arr::pull($validatedData, 'quantidade');
            $validatedData['quantidade'] = 1;

            $baseNumeroSerie = $validatedData['numero_serie'] ?? null;
            $createdResources = [];

            for ($i = 0; $i < $totalQuantidade; $i++) {
                if ($baseNumeroSerie) {
                    $validatedData['numero_serie'] = $baseNumeroSerie . '-' . ($i + 1);
                }
                $createdResources[] = RecursoDidatico::create($validatedData);
            }

            foreach ($usersToNotify as $user) {
                Notificacao::create([
                    'titulo' => 'Novos Recursos Cadastrados',
                    'mensagem' => "{$totalQuantidade} unidades do recurso '{$validatedData['nome']}' foram cadastradas.",
                    'data_envio' => now(),
                    'status_mensagem' => 'enviada',
                    'id_usuario' => $user->id_usuario,
                ]);
            }

            $successMessage = $totalQuantidade . ' recursos individuais cadastrados com sucesso!';

            if ($request->wantsJson()) {
                return (RecursoDidaticoResource::collection(collect($createdResources)))
                    ->response()
                    ->setStatusCode(201);
            }

        } else {
            
            $recurso = RecursoDidatico::create($validatedData);

            foreach ($usersToNotify as $user) {
                Notificacao::create([
                    'titulo' => 'Novo Lote de Recursos Cadastrado',
                    'mensagem' => "Um lote de {$totalQuantidade} unidade(s) do recurso '{$recurso->nome}' foi cadastrado.",
                    'data_envio' => now(),
                    'status_mensagem' => 'enviada',
                    'id_usuario' => $user->id_usuario,
                ]);
            }
            $successMessage = 'Lote de ' . $totalQuantidade . ' recurso(s) cadastrado com sucesso!';

            if ($request->wantsJson()) {
                return (new RecursoDidaticoResource($recurso))
                    ->response()
                    ->setStatusCode(201);
            }
        }

        return redirect()->route('resources.index')
                         ->with('success', $successMessage);
    }

    public function show(Request $request, RecursoDidatico $recursoDidatico)
    {
        if ($request->wantsJson()) {
            $recursoDidatico->load(['agendamentos' => function ($query) {
                $query->where('data_hora_inicio', '>=', now())->orderBy('data_hora_inicio');
            }]);
            return new RecursoDidaticoResource($recursoDidatico);
        }
        
        return redirect()->route('resources.edit', $recursoDidatico->id_recurso);
    }
    
    public function edit(RecursoDidatico $recursoDidatico): View 
    {
        return view('resources.edit', ['recursoDidatico' => $recursoDidatico]);
    }

    public function update(UpdateRecursoDidaticoRequest $request, RecursoDidatico $recursoDidatico)
    {
        $recursoDidatico->update($request->validated());

        $usersToNotify = Usuario::whereIn('tipo_usuario', ['administrador', 'diretor'])->get();
        foreach ($usersToNotify as $user) {
            Notificacao::create([
                'titulo' => 'Recurso Didático Atualizado',
                'mensagem' => "O recurso '{$recursoDidatico->nome}' foi atualizado.",
                'data_envio' => now(),
                'status_mensagem' => 'enviada',
                'id_usuario' => $user->id_usuario,
            ]);
        }

        if ($request->wantsJson()) {
            return new RecursoDidaticoResource($recursoDidatico->fresh());
        }

        return redirect()->route('resources.index')
                         ->with('success', 'Recurso didático atualizado com sucesso!');
    }

    public function destroy(Request $request, RecursoDidatico $recursoDidatico)
    {
        $usersToNotify = Usuario::whereIn('tipo_usuario', ['administrador', 'diretor'])->get();
        foreach ($usersToNotify as $user) {
            Notificacao::create([
                'titulo' => 'Recurso Didático Excluído',
                'mensagem' => "O recurso '{$recursoDidatico->nome}' foi excluído.",
                'data_envio' => now(),
                'status_mensagem' => 'enviada',
                'id_usuario' => $user->id_usuario,
            ]);
        }

        $recursoDidatico->delete();

        if ($request->wantsJson()) {
            return response()->json(null, 204); 
        }

        return redirect()->route('resources.index')
                         ->with('success', 'Recurso didático excluído com sucesso!');
    }
}