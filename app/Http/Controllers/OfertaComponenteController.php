<?php

namespace App\Http\Controllers;

use App\Models\OfertaComponente;
use Illuminate\Http\Request;

class OfertaComponenteController extends Controller
{
    public function index()
    {
        return response()->json(OfertaComponente::with(['turma', 'professor', 'componente'])->get());
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'id_turma' => 'required|exists:turmas,id_turma',
            'id_professor' => 'required|exists:usuarios,id_usuario',
            'id_componente' => 'required|exists:componentes_curriculares,id_componente',
        ]);

        $oferta = OfertaComponente::create($validatedData);
        return response()->json($oferta, 201);
    }

    public function show(OfertaComponente $oferta_componente)
    {
        return response()->json($oferta_componente->load(['turma', 'professor', 'componente']));
    }

    public function update(Request $request, OfertaComponente $oferta_componente)
    {
        $validatedData = $request->validate([
            'id_professor' => 'sometimes|required|exists:usuarios,id_usuario',
        ]);

        $oferta_componente->update($validatedData);
        return response()->json($oferta_componente);
    }

    public function destroy(OfertaComponente $oferta_componente)
    {
        $oferta_componente->delete();
        return response()->json(null, 204);
    }
}