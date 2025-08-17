<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTurmaRequest;
use App\Http\Requests\UpdateTurmaRequest;
use App\Http\Resources\TurmaResource;
use App\Models\Turma;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TurmaController extends Controller
{
    /** /api/turmas?escola_id=1&turno=manha&ano_letivo=2025
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Turma::query()->with('escola');

        $query->when($request->query('escola_id'), function ($q, $escolaId) {
            return $q->where('id_escola', $escolaId);
        });

        $query->when($request->query('turno'), function ($q, $turno) {
            return $q->where('turno', $turno);
        });
        
        $query->when($request->query('ano_letivo'), function ($q, $ano) {
            return $q->where('ano_letivo', $ano);
        });

        $turmas = $query->paginate(15);

        return TurmaResource::collection($turmas);
    }

    public function store(StoreTurmaRequest $request): TurmaResource
    {
        $turma = Turma::create($request->validated());

        return new TurmaResource($turma->load('escola'));
    }

    public function show(Turma $turma): TurmaResource
    {
        $turma->load(['escola', 'ofertasComponentes.professor', 'ofertasComponentes.componente']);
        
        return new TurmaResource($turma);
    }

    public function update(UpdateTurmaRequest $request, Turma $turma): TurmaResource
    {
        $turma->update($request->validated());

        return new TurmaResource($turma->fresh()->load('escola'));
    }

    public function destroy(Turma $turma): JsonResponse
    {
        $turma->delete();

        return response()->json(null, 204);
    }
}