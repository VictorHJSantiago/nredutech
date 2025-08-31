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

    public function show(Request $request, RecursoDidatico $recursos_didatico)
    {
        if ($request->wantsJson()) {
            $recursos_didatico->load(['agendamentos' => function ($query) {
                $query->where('data_hora_inicio', '>=', now())->orderBy('data_hora_inicio');
            }]);
            return new RecursoDidaticoResource($recursos_didatico);
        }
        
        return redirect()->route('resources.edit', $recursos_didatico->id_recurso);
    }
    
    public function edit(RecursoDidatico $recursos_didatico): View 
    {
        return view('resources.edit', ['recursoDidatico' => $recursos_didatico]);
    }

    public function update(UpdateRecursoDidaticoRequest $request, RecursoDidatico $recursos_didatico)
    {
        $recursos_didatico->update($request->validated());

        if ($request->wantsJson()) {
            return new RecursoDidaticoResource($recursos_didatico->fresh());
        }

        return redirect()->route('resources.index')
                         ->with('success', 'Recurso didático atualizado com sucesso!');
    }

    public function destroy(Request $request, RecursoDidatico $recursos_didatico)
    {
        $recursos_didatico->delete();

        if ($request->wantsJson()) {
            return response()->json(null, 204); 
        }

        return redirect()->route('resources.index')
                         ->with('success', 'Recurso didático excluído com sucesso!');
    }
}
