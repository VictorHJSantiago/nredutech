<?php

namespace App\Http\Controllers;

use App\Models\Municipio;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use App\Http\Requests\StoreEscolaRequest;
use App\Http\Requests\UpdateEscolaRequest;
use App\Http\Resources\EscolaResource;
use App\Models\Escola;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class EscolaController extends Controller
{

    public function index(): View
    {
        $escolas = Escola::with('municipio')->orderBy('nome')->get();
        $municipios = Municipio::orderBy('nome')->get();
        return view('schools.index', compact('escolas', 'municipios'));
    }

    public function store(StoreEscolaRequest $request): RedirectResponse
    {
        Escola::create($request->validated());
        return redirect()->route('escolas.index')->with('success', 'Escola adicionada com sucesso!');
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