<?php

namespace App\Http\Controllers;

use App\Models\Agendamento;
use Illuminate\Http\Request;

class AgendamentoController extends Controller
{
    public function index()
    {
        return response()->json(Agendamento::with(['recurso', 'oferta'])->get());
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'data_hora_inicio' => 'required|date',
            'data_hora_fim' => 'required|date|after:data_hora_inicio',
            'status' => 'required|in:agendado,livre',
            'id_recurso' => 'required|exists:recursos_didaticos,id_recurso',
            'id_oferta' => 'required|exists:oferta_componentes,id_oferta',
        ]);
        
        $agendamento = Agendamento::create($validatedData);
        return response()->json($agendamento, 201);
    }

    public function show(Agendamento $agendamento)
    {
        return response()->json($agendamento->load(['recurso', 'oferta']));
    }

    public function update(Request $request, Agendamento $agendamento)
    {
        $validatedData = $request->validate([
            'data_hora_inicio' => 'sometimes|required|date',
            'data_hora_fim' => 'sometimes|required|date|after:data_hora_inicio',
            'status' => 'sometimes|required|in:agendado,livre',
        ]);

        $agendamento->update($validatedData);
        return response()->json($agendamento);
    }

    public function destroy(Agendamento $agendamento)
    {
        $agendamento->delete();
        return response()->json(null, 204);
    }
}