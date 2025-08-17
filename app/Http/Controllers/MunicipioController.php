<?php

namespace App\Http\Controllers;

use App\Models\Municipio;
use Illuminate\Http\Request;

class MunicipioController extends Controller
{
    public function index()
    {
        return response()->json(Municipio::with('escolas')->get());
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nome' => 'required|string|max:255',
            'tipo' => 'required|in:urbano,rural',
        ]);

        $municipio = Municipio::create($validatedData);

        return response()->json($municipio, 201);
    }

    public function show(Municipio $municipio)
    {
        return response()->json($municipio->load('escolas'));
    }

    public function update(Request $request, Municipio $municipio)
    {
        $validatedData = $request->validate([
            'nome' => 'sometimes|required|string|max:255',
            'tipo' => 'sometimes|required|in:urbano,rural',
        ]);

        $municipio->update($validatedData);

        return response()->json($municipio);
    }

    public function destroy(Municipio $municipio)
    {
        $municipio->delete();
        return response()->json(null, 204);
    }
}