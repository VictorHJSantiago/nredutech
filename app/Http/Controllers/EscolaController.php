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
use Illuminate\Support\Facades\Auth; 
class EscolaController extends Controller
{

    public function index(): View
    {
        $usuario = Auth::user();
        $escolasQuery = Escola::query()->with('municipio');
        if ($usuario->tipo_usuario !== 'administrador' && $usuario->id_escola) {
            $escolasQuery->where('id_escola', $usuario->id_escola);
        }
        
        $escolas = $escolasQuery->orderBy('nome')->get();
        
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
        $this->authorize('view', $escola); 
        
        $escola->load(['municipio', 'diretor', 'turmas', 'usuarios']);
        
        return new EscolaResource($escola);
    }

    public function edit(Escola $escola): View
    {
         // Aplicar Policy
        // $this->authorize('update', $escola); 
        
        $municipios = Municipio::orderBy('nome')->get();
        return view('schools.edit', compact('escola', 'municipios'));
    }

    public function update(UpdateEscolaRequest $request, Escola $escola): RedirectResponse
    {
         // Aplicar Policy
        // $this->authorize('update', $escola); 

        $escola->update($request->validated());

        return redirect()->route('escolas.index')->with('success', 'Escola atualizada com sucesso!');
    }

    public function destroy(Escola $escola): RedirectResponse
    {
         // Aplicar Policy (só admin)
        // $this->authorize('delete', $escola); 
        
        $escola->delete();

        return redirect()->route('escolas.index')->with('success', 'Escola excluída com sucesso!');
    }
}