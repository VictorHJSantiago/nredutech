<?php

namespace App\Http\Controllers;

use App\Models\Escola;
use Illuminate\Http\Request;

class EscolaController extends Controller
{
    public function index()
    {
        return response()->json(Escola::with(['municipio', 'diretor'])->get());
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nome' => 'required|string|max:255',
            'endereco' => 'nullable|string|max:255',
            'id_municipio' => 'required|exists:municipios,id_municipio',
            'id_diretor_responsavel' => 'nullable|exists:usuarios,id_usuario',
            'tipo' => 'required|in:colegio_estadual,escola_tecnica,escola_municipal',
        ]);

        $escola = Escola::create($validatedData);
        return response()->json($escola, 201);
    }

    public function show(Escola $escola)
    {
        return response()->json($escola->load(['municipio', 'diretor', 'turmas', 'usuarios']));
    }

    public function update(Request $request, Escola $escola)
    {
        $validatedData = $request->validate([
            'nome' => 'sometimes|required|string|max:255',
            'id_municipio' => 'sometimes|required|exists:municipios,id_municipio',
            'id_diretor_responsavel' => 'nullable|exists:usuarios,id_usuario',
            'tipo' => 'sometimes|required|in:colegio_estadual,escola_tecnica,escola_municipal',
        ]);

        $escola->update($validatedData);
        return response()->json($escola);
    }

    public function destroy(Escola $escola)
    {
        $escola->delete();
        return response()->json(null, 204);
    }
}