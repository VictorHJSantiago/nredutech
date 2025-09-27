<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCurricularComponentRequest;
use App\Http\Requests\UpdateCurricularComponentRequest;
use App\Models\ComponenteCurricular;
use App\Models\Notificacao; 
use App\Models\Usuario;     
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CurricularComponentController extends Controller
{

    public function index(Request $request) 
    {        
        $query = ComponenteCurricular::query()
            ->join('usuarios', 'componentes_curriculares.id_usuario_criador', '=', 'usuarios.id_usuario')
            ->select('componentes_curriculares.*', 'usuarios.nome_completo as criador_nome');

        $query->when($request->query('search_text'), function ($q, $searchText) {
            return $q->where(function ($subQ) use ($searchText) {
                $subQ->where('componentes_curriculares.nome', 'LIKE', "%{$searchText}%")
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

        $allowedSorts = ['id_componente', 'nome', 'descricao', 'carga_horaria', 'status', 'criador_nome'];

        if (in_array($sortBy, $allowedSorts)) {
            $sortColumn = $sortBy === 'criador_nome' ? 'usuarios.nome_completo' : 'componentes_curriculares.' . $sortBy;
            $query->orderBy($sortColumn, $order);
        } else {
            $query->orderBy('componentes_curriculares.nome', 'asc'); 
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

    public function store(StoreCurricularComponentRequest $request)
    {
        $validatedData = $request->validated();
        $user = Auth::user();

        $validatedData['id_usuario_criador'] = $user->id_usuario;
        
        if ($user->tipo_usuario !== 'administrador') {
            $validatedData['status'] = 'pendente';
        }

        $componente = ComponenteCurricular::create($validatedData);

        if ($componente->status === 'pendente') {
            $usersToNotify = Usuario::whereIn('tipo_usuario', ['administrador', 'diretor'])->get();
            foreach ($usersToNotify as $userToNotify) {
                Notificacao::create([
                    'titulo' => 'Nova Disciplina para Aprovação',
                    'mensagem' => "A disciplina '{$componente->nome}' foi cadastrada por {$user->nome_completo} e aguarda aprovação.",
                    'data_envio' => now(),
                    'status_mensagem' => 'enviada',
                    'id_usuario' => $userToNotify->id_usuario,
                ]);
            }
        }
        $successMessage = 'Disciplina cadastrada com sucesso!';
        if ($user->tipo_usuario !== 'administrador') {
            $successMessage .= ' Aguardando aprovação.';
        }

        return redirect()->route('componentes.index')->with('success', $successMessage);
    }
    
    public function edit(ComponenteCurricular $componente) 
    {
        return view('disciplines.edit', ['componenteCurricular' => $componente]);
    }
    
    public function update(UpdateCurricularComponentRequest $request, ComponenteCurricular $componente)
    {
        $validatedData = $request->validated();
        $componente->update($validatedData);

        if (isset($validatedData['status']) && $componente->id_usuario_criador) {
            $status = $validatedData['status'];
            $criador = $componente->criador;
            if ($criador && $criador->id_usuario != Auth::id()) {
                Notificacao::create([
                    'titulo' => 'Atualização de Status da Disciplina',
                    'mensagem' => "O status da disciplina '{$componente->nome}' que você criou foi atualizado para '{$status}'.",
                    'data_envio' => now(),
                    'status_mensagem' => 'enviada',
                    'id_usuario' => $criador->id_usuario,
                ]);
            }
        }

        return redirect()->route('componentes.index')->with('success', 'Disciplina atualizada com sucesso!');
    }

    public function destroy(ComponenteCurricular $componente)
    {
        if ($componente->ofertas()->exists()) {
            return redirect()->route('componentes.index')->with('error', 'Não é possível excluir uma disciplina que já está associada a uma turma.');
        }
        
        $nomeComponente = $componente->nome;
        $criador = $componente->criador;
        $componente->delete();
        $usersToNotify = Usuario::where('tipo_usuario', 'administrador')->get();
                if ($criador && $criador->id_usuario != Auth::id()) {
            $usersToNotify->push($criador);
        }

        foreach ($usersToNotify->unique('id_usuario') as $user) {
            Notificacao::create([
                'titulo' => 'Disciplina Excluída',
                'mensagem' => "A disciplina '{$nomeComponente}' foi excluída do sistema.",
                'data_envio' => now(),
                'status_mensagem' => 'enviada',
                'id_usuario' => $user->id_usuario,
            ]);
        }
        
        return redirect()->route('componentes.index')->with('success', 'Disciplina excluída com sucesso!');
    }
}