<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterUserRequest;
use App\Models\Escola;
use App\Models\Notificacao;
use App\Models\Usuario;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        $escolas = Escola::orderBy('nome', 'asc')->get();
        return view('auth.register', compact('escolas'));
    }

    public function store(RegisterUserRequest $request): RedirectResponse
    {
        $data = $request->validated();

        return DB::transaction(function () use ($data) {
            
            $usuario = Usuario::create([
                'nome_completo' => $data['name'],
                'username' => $data['username'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'data_nascimento' => $data['data_nascimento'] ?? null,
                'cpf' => $data['cpf'] ?? null,
                'rg' => $data['rg'] ?? null,
                'rco_siape' => $data['rco_siape'] ?? null,
                'telefone' => $data['telefone'] ?? null,
                'formacao' => $data['formacao'] ?? null,
                'area_formacao' => $data['area_formacao'] ?? null,
                'tipo_usuario' => $data['tipo_usuario'],
                'id_escola' => $data['id_escola'],
                'status_aprovacao' => 'pendente',
                'data_registro' => now(),
            ]);

            $usersToNotify = Usuario::whereIn('tipo_usuario', ['administrador', 'diretor'])->get();
            if ($usersToNotify->isNotEmpty()) {
                foreach ($usersToNotify as $userToNotify) {
                    Notificacao::create([
                        'titulo' => 'Novo Usuário Aguardando Aprovação',
                        'mensagem' => "O usuário '{$usuario->nome_completo}' se cadastrou e aguarda aprovação.",
                        'data_envio' => now(),
                        'status_mensagem' => 'enviada',
                        'id_usuario' => $userToNotify->id_usuario,
                    ]);
                }
            }

            
            return redirect()->route('login')->with('success', 'Cadastro realizado com sucesso! Aguarde a aprovação do administrador ou diretor.');
        });
    }
}