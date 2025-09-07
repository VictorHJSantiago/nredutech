<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRecursoDidaticoRequest;
use App\Http\Requests\UpdateRecursoDidaticoRequest;
use App\Http\Resources\RecursoDidaticoResource;
use App\Models\RecursoDidatico;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Arr;

class RecursoDidaticoController extends Controller
{
    public function index(Request $request)
    {
        $sortableColumns = ['id_recurso', 'nome', 'marca', 'numero_serie', 'quantidade', 'tipo', 'status', 'data_aquisicao'];
        $sortBy = $request->query('sort_by', 'id_recurso');
        $direction = $request->query('direction', 'asc');

        if (!in_array(strtolower($direction), ['asc', 'desc'])) {
            $direction = 'asc';
        }
        if (!in_array($sortBy, $sortableColumns)) {
            $sortBy = 'id_recurso';
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

        $query->orderBy($sortBy, $direction);

        $recursos = $query->paginate(15)->withQueryString();

        if ($request->wantsJson()) {
            return RecursoDidaticoResource::collection($recursos);
        }

        return view('resources.index', [
            'recursos' => $recursos,
            'currentSortBy' => $sortBy,
            'currentDirection' => $direction
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

            $successMessage = $totalQuantidade . ' recursos individuais cadastrados com sucesso!';

            if ($request->wantsJson()) {
                return (RecursoDidaticoResource::collection(collect($createdResources)))
                    ->response()
                    ->setStatusCode(201);
            }

        } else {
            
            $recurso = RecursoDidatico::create($validatedData);
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

        if ($request->wantsJson()) {
            return new RecursoDidaticoResource($recursoDidatico->fresh());
        }

        return redirect()->route('resources.index')
                         ->with('success', 'Recurso didático atualizado com sucesso!');
    }

    public function destroy(Request $request, RecursoDidatico $recursoDidatico)
    {
        $recursoDidatico->delete();

        if ($request->wantsJson()) {
            return response()->json(null, 204); 
        }

        return redirect()->route('resources.index')
                         ->with('success', 'Recurso didático excluído com sucesso!');
    }
}
