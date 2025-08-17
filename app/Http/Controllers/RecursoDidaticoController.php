<?php

namespace App\Http\Controllers;

use App\Models\RecursoDidatico;
use Illuminate\Http\Request;

class RecursoDidaticoController extends Controller
{
    public function index()
    {
        return response()->json(RecursoDidatico::all());
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nome' => 'required|string|max:255',
            'marca' => 'nullable|string|max:100',
            'numero_serie' => 'nullable|string|max:100|unique:recursos_didaticos,numero_serie',
            'quantidade' => 'required|integer|min:0',
            'status' => 'required|in:funcionando,em_manutencao,quebrado,descartado',
        ]);

        $recurso = RecursoDidatico::create($validatedData);
        return response()->json($recurso, 201);
    }

    public function show(RecursoDidatico $recursos_didatico)
    {
        return response()->json($recursos_didatico);
    }

    public function update(Request $request, RecursoDidatico $recursos_didatico)
    {
         $validatedData = $request->validate([
            'nome' => 'sometimes|required|string|max:255',
            'quantidade' => 'sometimes|required|integer|min:0',
            'status' => 'sometimes|required|in:funcionando,em_manutencao,quebrado,descartado',
        ]);

        $recursos_didatico->update($validatedData);
        return response()->json($recursos_didatico);
    }

    public function destroy(RecursoDidatico $recursos_didatico)
    {
        $recursos_didatico->delete();
        return response()->json(null, 204);
    }
}