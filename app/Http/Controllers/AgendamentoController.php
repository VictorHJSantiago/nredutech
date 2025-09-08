<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAgendamentoRequest;
use App\Http\Requests\UpdateAgendamentoRequest;
use App\Http\Resources\AgendamentoResource;
use App\Models\Agendamento;
use App\Models\OfertaComponente;
use App\Models\RecursoDidatico;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth; 
use App\Models\Turma; 

class AgendamentoController extends Controller
{
    /**
     * @param Request $request
     * @return View|AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        if ($request->wantsJson()) {
            $request->validate([
                'start' => 'required|date',
                'end' => 'required|date|after_or_equal:start',
            ]);

            $query = Agendamento::query()->with(['recurso', 'oferta.turma', 'oferta.professor', 'oferta.componenteCurricular']);

            $query->where('data_hora_inicio', '<', $request->end)
                  ->where('data_hora_fim', '>', $request->start);

            $agendamentos = $query->get();

            return AgendamentoResource::collection($agendamentos);
        }
        $recursos = RecursoDidatico::where('status', 'funcionando')->get();        
        $usuarioLogado = Auth::user();
        $ofertasQuery = OfertaComponente::query()->with([
            'componenteCurricular', 
            'turma', 
            'professor'
        ]);

        if ($usuarioLogado->tipo_usuario === 'professor') {
            $ofertasQuery->where('id_professor', $usuarioLogado->id_usuario);

        } elseif ($usuarioLogado->tipo_usuario === 'diretor' && $usuarioLogado->id_escola) {
            $turmaIdsDaEscola = Turma::where('id_escola', $usuarioLogado->id_escola)->pluck('id_turma');
            $ofertasQuery->whereIn('id_turma', $turmaIdsDaEscola);
        }

        $ofertas = $ofertasQuery
                        ->whereHas('turma') 
                        ->whereHas('componenteCurricular')
                        ->whereHas('professor')
                        ->get();
        return view('appointments.index', [
            'recursos' => $recursos,
            'ofertas' => $ofertas,
            'now' => now()->toIso8601String(),
        ]);
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
        if ($agendamento->data_hora_inicio < now()->addHours(3)) {
            return response()->json(['message' => 'Não é possível excluir agendamentos passados ou muito próximos.'], 403);
        }
        $agendamento->delete();
        return response()->json(null, 204);
    }
}