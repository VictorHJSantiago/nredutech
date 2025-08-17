<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRecursoDidaticoRequest;
use App\Http\Requests\UpdateRecursoDidaticoRequest;
use App\Http\Resources\RecursoDidaticoResource;
use App\Models\RecursoDidatico;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class RecursoDidaticoController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = RecursoDidatico::query();

        $query->when($request->query('status'), function ($q, $status) {
            return $q->where('status', $status);
        });

        $recursos = $query->paginate(15);

        return RecursoDidaticoResource::collection($recursos);
    }

    public function store(StoreRecursoDidaticoRequest $request): RecursoDidaticoResource
    {
        $recurso = RecursoDidatico::create($request->validated());

        return new RecursoDidaticoResource($recurso);
    }

    public function show(RecursoDidatico $recursoDidatico): RecursoDidaticoResource
    {
        $recursoDidatico->load(['agendamentos' => function ($query) {
            $query->where('data_hora_inicio', '>=', now())->orderBy('data_hora_inicio');
        }]);
        
        return new RecursoDidaticoResource($recursoDidatico);
    }

    public function update(UpdateRecursoDidaticoRequest $request, RecursoDidatico $recursoDidatico): RecursoDidaticoResource
    {
        $recursoDidatico->update($request->validated());

        return new RecursoDidaticoResource($recursoDidatico->fresh());
    }

    public function destroy(RecursoDidatico $recursoDidatico): JsonResponse
    {
        $recursoDidatico->delete();

        return response()->json(null, 204);
    }
}