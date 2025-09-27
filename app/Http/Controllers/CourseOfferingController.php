<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCourseOfferingRequest;
use App\Http\Requests\UpdateCourseOfferingRequest;
use App\Http\Resources\CourseOfferingResource;
use App\Models\OfertaComponente;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\RedirectResponse; 
use App\Models\Turma;
use Illuminate\Support\Facades\Redirect; 

class CourseOfferingController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = OfertaComponente::query()->with(['turma', 'professor', 'componente']);
        $query->when($request->query('turma_id'), function ($q, $turmaId) {
            return $q->where('id_turma', $turmaId);
        });
        $query->when($request->query('professor_id'), function ($q, $professorId) {
            return $q->where('id_professor', $professorId);
        });

        $ofertas = $query->paginate(15);
        return CourseOfferingResource::collection($ofertas);
    }

    public function store(StoreCourseOfferingRequest $request): RedirectResponse
    {
        $exists = OfertaComponente::where('id_turma', $request->input('id_turma'))
            ->where('id_componente', $request->input('id_componente'))
            ->where('id_professor', $request->input('id_professor'))
            ->exists();
            
        if ($exists) {
            return Redirect::back()->with('error', 'Esta combinação de professor/disciplina já existe para esta turma.');
        }
        OfertaComponente::create($request->validated());
        return Redirect::route('turmas.show', $request->input('id_turma'))
                         ->with('success', 'Oferta (Professor/Disciplina) adicionada à turma com sucesso!');
    }

    public function show(OfertaComponente $ofertaComponente): CourseOfferingResource
    {
        $ofertaComponente->load(['turma', 'professor', 'componente', 'agendamentos']);
        return new CourseOfferingResource($ofertaComponente);
    }

    public function update(UpdateCourseOfferingRequest $request, OfertaComponente $ofertaComponente): CourseOfferingResource
    {
        $ofertaComponente->update($request->validated());
        return new CourseOfferingResource($ofertaComponente->fresh()->load(['turma', 'professor', 'componente']));
    }

    public function destroy(OfertaComponente $ofertaComponente): RedirectResponse
    {
        $turmaId = $ofertaComponente->id_turma;
        if ($ofertaComponente->agendamentos()->exists()) {
             return Redirect::route('turmas.show', $turmaId)
                         ->with('error', 'Não é possível remover. Esta oferta (professor/disciplina) já possui agendamentos ativos.');
        }

        $ofertaComponente->delete();
        return Redirect::route('turmas.show', $turmaId)
                         ->with('success', 'Oferta (Professor/Disciplina) removida da turma com sucesso!');
    }
}