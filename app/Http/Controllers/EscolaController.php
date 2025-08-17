<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEscolaRequest;
use App\Http\Requests\UpdateEscolaRequest;
use App\Http\Resources\EscolaResource;
use App\Models\Escola;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class EscolaController extends Controller
{

    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Escola::query()->with(['municipio', 'diretor']);

        $query->when($request->query('tipo'), function ($q, $tipo) {
            return $q->where('tipo', $tipo);
        });

        $query->when($request->query('municipio_id'), function ($q, $municipioId) {
            return $q->where('id_municipio', $municipioId);
        });

        $escolas = $query->paginate(15);

        return EscolaResource::collection($escolas);
    }

    public function store(StoreEscolaRequest $request): EscolaResource
    {
        $escola = Escola::create($request->validated());

        return new EscolaResource($escola->load(['municipio', 'diretor']));
    }

    public function show(Escola $escola): EscolaResource
    {
        $escola->load(['municipio', 'diretor', 'turmas', 'usuarios']);
        
        return new EscolaResource($escola);
    }

    public function update(UpdateEscolaRequest $request, Escola $escola): EscolaResource
    {
        $escola->update($request->validated());

        return new EscolaResource($escola->fresh()->load(['municipio', 'diretor']));
    }

    public function destroy(Escola $escola): JsonResponse
    {
        $escola->delete();

        return response()->json(null, 204);
    }
}