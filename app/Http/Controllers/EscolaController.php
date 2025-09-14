<?php

namespace App\Http\Controllers;

use App\Models\Escola;
use App\Models\Municipio;
use App\Http\Requests\StoreEscolaRequest;
use App\Http\Requests\UpdateEscolaRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Http\Request; 

class EscolaController extends Controller
{
    public function index(Request $request): View
    {
        $allowedSorts = ['id_escola', 'nome', 'nivel_ensino', 'tipo'];
        $sortBy = $request->query('sort_by', 'nome');
        $order = $request->query('order', 'asc');

        if (!in_array($sortBy, $allowedSorts)) {
            $sortBy = 'nome';
        }
        if (!in_array(strtolower($order), ['asc', 'desc'])) {
            $order = 'asc';
        }
        
       $query = Escola::query()->with([
            'municipio', 
            'usuarios' => fn($q) => $q->where('tipo_usuario', 'diretor')->where('status_aprovacao', 'ativo')
        ]);

        $query->when($request->query('search_nome'), function ($q, $search_nome) {
            return $q->where('nome', 'LIKE', "%{$search_nome}%");
        });

        $query->when($request->query('id_municipio'), function ($q, $municipioId) {
            return $q->where('id_municipio', $municipioId);
        });

        $query->when($request->query('nivel_ensino'), function ($q, $nivel) {
            return $q->where('nivel_ensino', $nivel);
        });

        $query->when($request->query('tipo'), function ($q, $tipo) {
            return $q->where('tipo', $tipo);
        });

        if ($sortBy === 'municipio.nome') {
             $query->join('municipios', 'escolas.id_municipio', '=', 'municipios.id_municipio')
                   ->orderBy('municipios.nome', $order)
                   ->select('escolas.*'); 
        } else {
            $query->orderBy($sortBy, $order);
        }

        $escolas = $query->paginate(perPage: 5)->withQueryString(); 
        $municipios = Municipio::orderBy('nome')->get();
        return view('schools.index', compact('escolas', 'municipios', 'sortBy', 'order'));
    }

    public function store(StoreEscolaRequest $request): RedirectResponse
    {
        Escola::create($request->validated());
        return redirect()->route('escolas.index')->with('success', 'Escola adicionada com sucesso!');
    }

    public function edit(Escola $escola): View
    {
        $municipios = Municipio::orderBy('nome')->get();
        return view('schools.edit', compact('escola', 'municipios'));
    }

    public function update(UpdateEscolaRequest $request, Escola $escola): RedirectResponse
    {
        $escola->update($request->validated());
        return redirect()->route('escolas.index')->with('success', 'Escola atualizada com sucesso!');
    }

    public function destroy(Escola $escola): RedirectResponse
    {
        if ($escola->turmas()->exists()) {
            return redirect()->route('escolas.index')->with('error', 'Não é possível excluir esta escola, pois ela já possui turmas cadastradas.');
        }

         if ($escola->usuarios()->exists()) {
            return redirect()->route('escolas.index')->with('error', 'Não é possível excluir esta escola, pois ela possui usuários (diretores ou professores) associados.');
        }

        $escola->delete();
        return redirect()->route('escolas.index')->with('success', 'Escola excluída com sucesso!');
    }
}