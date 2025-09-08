<?php

namespace App\Http\Controllers;

use App\Models\Escola;
use App\Models\Municipio;
use App\Http\Requests\StoreEscolaRequest;
use App\Http\Requests\UpdateEscolaRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class EscolaController extends Controller
{
    public function index(): View
    {
        $escolas = Escola::with([
                            'municipio', 
                            'usuarios' => fn($query) => $query->where('tipo_usuario', 'diretor')
                        ])
                        ->orderBy('nome')
                        ->get();
                        
        $municipios = Municipio::orderBy('nome')->get();
        
        return view('schools.index', compact('escolas', 'municipios'));
    }

    public function store(StoreEscolaRequest $request): RedirectResponse
    {
        Escola::create($request->validated());
        return redirect()->route('escolas.index')->with('success', 'Escola adicionada com sucesso!');
    }

    public function edit(Escola $escola): View
    {
        $municipios = Municipio::orderBy('nome')->get();
        return view('schools.edit', compact('escola', 'municipios'));
    }

    public function update(UpdateEscolaRequest $request, Escola $escola): RedirectResponse
    {
        $escola->update($request->validated());
        return redirect()->route('escolas.index')->with('success', 'Escola atualizada com sucesso!');
    }

    public function destroy(Escola $escola): RedirectResponse
    {
        if ($escola->turmas()->exists()) {
            return redirect()->route('escolas.index')->with('error', 'Não é possível excluir esta escola, pois ela já possui turmas cadastradas.');
        }

         if ($escola->usuarios()->exists()) {
            return redirect()->route('escolas.index')->with('error', 'Não é possível excluir esta escola, pois ela possui usuários (diretores ou professores) associados.');
        }

        $escola->delete();
        return redirect()->route('escolas.index')->with('success', 'Escola excluída com sucesso!');
    }
}