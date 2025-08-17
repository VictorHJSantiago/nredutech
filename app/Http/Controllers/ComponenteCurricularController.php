<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreComponenteCurricularRequest;
use App\Http\Requests\UpdateComponenteCurricularRequest;
use App\Http\Resources\ComponenteCurricularResource;
use App\Models\ComponenteCurricular;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ComponenteCurricularController extends Controller
{
    /**
     * Exibe uma lista paginada de componentes curriculares.
     */
    public function index(): AnonymousResourceCollection
    {
        // Usa paginate() para otimizar a performance em vez de all()
        $componentes = ComponenteCurricular::paginate(15);

        return ComponenteCurricularResource::collection($componentes);
    }

    public function store(StoreComponenteCurricularRequest $request): ComponenteCurricularResource
    {
        $componente = ComponenteCurricular::create($request->validated());

        return new ComponenteCurricularResource($componente);
    }

    public function show(ComponenteCurricular $componenteCurricular): ComponenteCurricularResource
    {
        return new ComponenteCurricularResource($componenteCurricular);
    }

    public function update(UpdateComponenteCurricularRequest $request, ComponenteCurricular $componenteCurricular): ComponenteCurricularResource
    {
        $componenteCurricular->update($request->validated());

        return new ComponenteCurricularResource($componenteCurricular->fresh());
    }

    public function destroy(ComponenteCurricular $componenteCurricular): JsonResponse
    {
        $componenteCurricular->delete();

        return response()->json(null, 204);
    }
}