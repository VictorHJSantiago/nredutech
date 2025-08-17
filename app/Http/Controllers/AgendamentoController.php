<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAgendamentoRequest;
use App\Http\Requests\UpdateAgendamentoRequest;
use App\Http\Resources\AgendamentoResource;
use App\Models\Agendamento;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AgendamentoController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $request->validate([
            'data_inicio' => 'nullable|date',
            'data_fim' => 'nullable|date|after_or_equal:data_inicio',
            'recurso_id' => 'nullable|integer|exists:recursos_didaticos,id_recurso',
        ]);

        $query = Agendamento::query()->with(['recurso', 'oferta.turma']);

        $query->when($request->data_inicio, function ($q, $data) {
            $q->where('data_hora_inicio', '>=', $data);
        });
        
        $query->when($request->data_fim, function ($q, $data) {
            $q->where('data_hora_fim', '<=', $data);
        });

        $query->when($request->recurso_id, function ($q, $recursoId) {
            $q->where('id_recurso', $recursoId);
        });

        $agendamentos = $query->latest('data_hora_inicio')->paginate(20);

        return AgendamentoResource::collection($agendamentos);
    }

    public function store(StoreAgendamentoRequest $request): AgendamentoResource
    {
        $agendamento = Agendamento::create($request->validated());

        return new AgendamentoResource($agendamento->load(['recurso', 'oferta']));
    }

    public function show(Agendamento $agendamento): AgendamentoResource
    {
        $agendamento->load(['recurso', 'oferta.turma', 'oferta.professor']);
        
        return new AgendamentoResource($agendamento);
    }

    public function update(UpdateAgendamentoRequest $request, Agendamento $agendamento): AgendamentoResource
    {
        $agendamento->update($request->validated());

        return new AgendamentoResource($agendamento->fresh()->load(['recurso', 'oferta']));
    }

    public function destroy(Agendamento $agendamento): JsonResponse
    {
        $agendamento->delete();

        return response()->json(null, 204);
    }
}