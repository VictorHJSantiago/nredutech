<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOfertaComponenteRequest;
use App\Http\Requests\UpdateOfertaComponenteRequest;
use App\Http\Resources\OfertaComponenteResource;
use App\Models\OfertaComponente;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class OfertaComponenteController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = OfertaComponente::query()->with(['turma', 'professor', 'componente']);

        $query->when($request->query('turma_id'), function ($q, $turmaId) {
            return $q->where('id_turma', $turmaId);
        });

        $query->when($request->query('professor_id'), function ($q, $professorId) {
            return $q->where('id_professor', $professorId);
        });

        $ofertas = $query->paginate(15);

        return OfertaComponenteResource::collection($ofertas);
    }

    public function store(StoreOfertaComponenteRequest $request): OfertaComponenteResource
    {
        $oferta = OfertaComponente::create($request->validated());

        return new OfertaComponenteResource($oferta->load(['turma', 'professor', 'componente']));
    }

    public function show(OfertaComponente $ofertaComponente): OfertaComponenteResource
    {
        $ofertaComponente->load(['turma', 'professor', 'componente', 'agendamentos']);
        
        return new OfertaComponenteResource($ofertaComponente);
    }

    public function update(UpdateOfertaComponenteRequest $request, OfertaComponente $ofertaComponente): OfertaComponenteResource
    {
        $ofertaComponente->update($request->validated());

        return new OfertaComponenteResource($ofertaComponente->fresh()->load(['turma', 'professor', 'componente']));
    }

    public function destroy(OfertaComponente $ofertaComponente): JsonResponse
    {
        $ofertaComponente->delete();

        return response()->json(null, 204);
    }
}