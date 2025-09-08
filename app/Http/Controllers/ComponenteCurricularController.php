<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreComponenteCurricularRequest;
use App\Http\Requests\UpdateComponenteCurricularRequest;
use App\Http\Resources\ComponenteCurricularResource;
use App\Models\ComponenteCurricular;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth; 

class ComponenteCurricularController extends Controller
{
    public function index() 
    {        
        $componentes = ComponenteCurricular::paginate(5);
        return view('disciplines.index', ['componentes' => $componentes]);
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