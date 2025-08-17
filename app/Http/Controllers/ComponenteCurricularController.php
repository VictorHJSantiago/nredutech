<?php

namespace App\Http\Controllers;

use App\Models\ComponenteCurricular;
use Illuminate\Http\Request;

class ComponenteCurricularController extends Controller
{
    public function index()
    {
        return response()->json(ComponenteCurricular::all());
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'carga_horaria' => 'required|string',
            'status' => 'required|in:pendente,aprovado,reprovado',
        ]);

        $componente = ComponenteCurricular::create($validatedData);
        return response()->json($componente, 201);
    }

    public function show(ComponenteCurricular $componentes_curriculare)
    {
        return response()->json($componentes_curriculare);
    }

    public function update(Request $request, ComponenteCurricular $componentes_curriculare)
    {
        $validatedData = $request->validate([
            'nome' => 'sometimes|required|string|max:255',
            'descricao' => 'nullable|string',
            'carga_horaria' => 'sometimes|required|string',
            'status' => 'sometimes|required|in:pendente,aprovado,reprovado',
        ]);

        $componentes_curriculare->update($validatedData);
        return response()->json($componentes_curriculare);
    }

    public function destroy(ComponenteCurricular $componentes_curriculare)
    {
        $componentes_curriculare->delete();
        return response()->json(null, 204);
    }
}