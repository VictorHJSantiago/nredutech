<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreComponenteCurricularRequest;
use App\Http\Requests\UpdateComponenteCurricularRequest;
use App\Http\Resources\ComponenteCurricularResource;
use App\Models\ComponenteCurricular;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Http\Request; 

class ComponenteCurricularController extends Controller
{
    public function index(Request $request) 
    {        
        $query = ComponenteCurricular::query();
        $query->when($request->query('search_text'), function ($q, $searchText) {
            return $q->where(function ($subQ) use ($searchText) {
                $subQ->where('nome', 'LIKE', "%{$searchText}%")
                     ->orWhere('descricao', 'LIKE', "%{$searchText}%");
            });
        });

        $query->when($request->query('search_carga'), function ($q, $searchCarga) {
            return $q->where('carga_horaria', 'LIKE', "%{$searchCarga}%");
        });

        $query->when($request->query('status'), function ($q, $status) {
            return $q->where('status', $status);
        });

        $sortBy = $request->query('sort_by', 'nome'); 
        $order = $request->query('order', 'asc'); 

        $allowedSorts = ['id_componente', 'nome', 'descricao', 'carga_horaria', 'status'];

        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $order);
        } else {
            $query->orderBy('nome', 'asc'); 
        }
        
        $componentes = $query->paginate(5)->withQueryString();

        return view('disciplines.index', [
            'componentes' => $componentes,
            'sortBy' => $sortBy,
            'order' => $order
        ]);
    }

    public function create()
    {
        return view('disciplines.create');
    }

    public function edit(ComponenteCurricular $componente) 
    {
        return view('disciplines.edit', ['componenteCurricular' => $componente]);
    }

    public function store(StoreComponenteCurricularRequest $request)
    {
        $validatedData = $request->validated();
        if (Auth::user()->tipo_usuario === 'professor') {
            $validatedData['status'] = 'pendente';
        }

        ComponenteCurricular::create($validatedData);

        return redirect()->route('componentes.index')->with('success', 'Disciplina cadastrada com sucesso! Aguardando aprovação se necessário.');
    }

    public function show(ComponenteCurricular $componenteCurricular): ComponenteCurricularResource
    {
        return new ComponenteCurricularResource($componenteCurricular);
    }

    public function update(UpdateComponenteCurricularRequest $request, ComponenteCurricular $componente)
    {
        $componente->update($request->validated());

        return redirect()->route('componentes.index')->with('success', 'Disciplina atualizada com sucesso!');
    }

    public function destroy(ComponenteCurricular $componente): JsonResponse
    {
        $componente->delete();

        return response()->json(null, 204);
    }
}