<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRecursoDidaticoRequest;
use App\Http\Requests\UpdateRecursoDidaticoRequest;
use App\Http\Resources\RecursoDidaticoResource;
use App\Models\RecursoDidatico;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RecursoDidaticoController extends Controller
{
    public function index(Request $request)
    {
        $query = RecursoDidatico::query()->latest();

        $query->when($request->query('status'), function ($q, $status) {
            return $q->where('status', $status);
        });

        $recursos = $query->paginate(15);

        if ($request->wantsJson()) {
            return RecursoDidaticoResource::collection($recursos);
        }

        return view('resources.index', ['recursos' => $recursos]);
    }

    public function create()
    {
        return view('resources.create');
    }

    public function store(StoreRecursoDidaticoRequest $request)
    {
        $recurso = RecursoDidatico::create($request->validated());

        if ($request->wantsJson()) {
            return (new RecursoDidaticoResource($recurso))
                ->response()
                ->setStatusCode(201); 
        }

        return redirect()->route('resources.index')
                         ->with('success', 'Recurso didático cadastrado com sucesso!');
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