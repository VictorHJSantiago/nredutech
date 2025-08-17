<?php

namespace App\Http\Controllers;

use App\Models\Turma;
use Illuminate\Http\Request;

class TurmaController extends Controller
{
    public function index()
    {
        return response()->json(Turma::with('escola')->get());
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'serie' => 'required|string|max:50',
            'turno' => 'required|in:manha,tarde,noite',
            'ano_letivo' => 'required|integer|digits:4',
            'nivel_escolaridade' => 'required|in:fundamental_1,fundamental_2,medio',
            'id_escola' => 'required|exists:escolas,id_escola',
        ]);

        $turma = Turma::create($validatedData);
        return response()->json($turma, 201);
    }

    public function show(Turma $turma)
    {
        return response()->json($turma->load('escola', 'ofertasComponentes'));
    }

    public function update(Request $request, Turma $turma)
    {
        $validatedData = $request->validate([
             'serie' => 'sometimes|required|string|max:50',
             'turno' => 'sometimes|required|in:manha,tarde,noite',
        ]);
        
        $turma->update($validatedData);
        return response()->json($turma);
    }

    public function destroy(Turma $turma)
    {
        $turma->delete();
        return response()->json(null, 204);
    }
}