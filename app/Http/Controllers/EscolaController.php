<?php

namespace App\Http\Controllers;

use App\Models\Escola;
use App\Models\Municipio;
use App\Http\Requests\StoreEscolaRequest;
use App\Http\Requests\UpdateEscolaRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Models\Usuario;
use Illuminate\Support\Facades\DB;

class EscolaController extends Controller
{
    public function index(Request $request): View
    {
        $allowedSorts = ['id_escola', 'nome', 'nivel_ensino', 'tipo', 'municipio_nome', 'diretor_nome'];
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

        $directorSubQuery = Usuario::select('id_escola', DB::raw("GROUP_CONCAT(nome_completo ORDER BY nome_completo SEPARATOR ', ') as diretor_nome"))
            ->where('tipo_usuario', 'diretor')
            ->where('status_aprovacao', 'ativo')
            ->groupBy('id_escola');

        $query->leftJoin('municipios', 'escolas.id_municipio', '=', 'municipios.id_municipio')
              ->leftJoinSub($directorSubQuery, 'diretores_joined', function ($join) {
                  $join->on('escolas.id_escola', '=', 'diretores_joined.id_escola');
              });

        $query->when($request->query('search_nome'), function ($q, $search_nome) {
            return $q->where(function ($subQ) use ($search_nome) {
                $subQ->where('escolas.nome', 'LIKE', "%{$search_nome}%")
                     ->orWhere('diretores_joined.diretor_nome', 'LIKE', "%{$search_nome}%");
            });
        });

        $query->when($request->query('id_municipio'), fn($q, $id) => $q->where('escolas.id_municipio', $id));
        $query->when($request->query('nivel_ensino'), fn($q, $n) => $q->where('escolas.nivel_ensino', $n));
        $query->when($request->query('tipo'), fn($q, $t) => $q->where('escolas.tipo', $t));

        $sortColumn = match($sortBy) {
            'municipio_nome' => 'municipios.nome',
            'diretor_nome' => 'diretores_joined.diretor_nome',
            'nome' => 'escolas.nome',
            default => 'escolas.' . $sortBy,
        };
        
        $finalOrder = $order;
        if ($sortBy === 'tipo') {
            $finalOrder = ($order === 'asc') ? 'desc' : 'asc';
        }
        
        $query->orderBy($sortColumn, $finalOrder);

        $escolas = $query->select('escolas.*', 'diretores_joined.diretor_nome')->paginate(5)->withQueryString();
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