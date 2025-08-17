<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMunicipioRequest;
use App\Http\Requests\UpdateMunicipioRequest;
use App\Http\Resources\MunicipioResource;
use App\Models\Municipio;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class MunicipioController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $municipios = Municipio::with('escolas')->paginate(15);

        return MunicipioResource::collection($municipios);
    }

    public function store(StoreMunicipioRequest $request): MunicipioResource
    {
        $municipio = Municipio::create($request->validated());

        return new MunicipioResource($municipio);
    }

    public function show(Municipio $municipio): MunicipioResource
    {
        $municipio->load('escolas');

        return new MunicipioResource($municipio);
    }

    public function update(UpdateMunicipioRequest $request, Municipio $municipio): MunicipioResource
    {
        $municipio->update($request->validated());

        return new MunicipioResource($municipio->fresh());
    }

    public function destroy(Municipio $municipio): JsonResponse
    {
        $municipio->delete();

        return response()->json(null, 204);
    }
}