<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMunicipioRequest;
use App\Http\Requests\UpdateMunicipioRequest;
use App\Http\Resources\MunicipioResource;
use App\Models\Municipio;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class MunicipioController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $municipios = Municipio::with('escolas')->paginate(15);

        return MunicipioResource::collection($municipios);
    }

    public function store(StoreMunicipioRequest $request): RedirectResponse
    {
        Municipio::create($request->validated());
        return redirect()->route('escolas.index')->with('success', 'Município adicionado com sucesso!');
    }

    public function show(Municipio $municipio): MunicipioResource
    {
        $municipio->load('escolas');

        return new MunicipioResource($municipio);
    }

    public function edit(Municipio $municipio): View
    {
        return view('cities.edit', compact('municipio'));
    }

    public function update(UpdateMunicipioRequest $request, Municipio $municipio): RedirectResponse
    {
        $municipio->update($request->validated());

        return redirect()->route('escolas.index')->with('success', 'Município atualizado com sucesso!');
    }

    public function destroy(Municipio $municipio): RedirectResponse
    {
        if ($municipio->escolas()->exists()) {
            return redirect()->route('escolas.index')->with('error', 'Não é possível excluir um município que possui escolas associadas.');
        }

        $municipio->delete();

        return redirect()->route('escolas.index')->with('success', 'Município excluído com sucesso!');
    }
}