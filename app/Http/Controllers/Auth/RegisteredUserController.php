<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterUserRequest;
use App\Models\User;
use App\Models\Usuario;
use App\Models\Escola;
use App\Models\Notificacao;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        $escolas = Escola::orderBy('nome')->get();
        return view('auth.register', ['escolas' => $escolas]);
    }

    /**
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(RegisterUserRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        $usuario = Usuario::create([
            'nome_completo' => $data['name'],
            'email' => $data['email'],
            'username' => $data['username'],
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
        foreach ($usersToNotify as $userToNotify) {
            Notificacao::create([
                'titulo' => 'Novo Usuário Aguardando Aprovação',
                'mensagem' => "O usuário '{$usuario->nome_completo}' se cadastrou e aguarda aprovação.",
                'data_envio' => now(),
                'status_mensagem' => 'enviada',
                'id_usuario' => $userToNotify->id_usuario,
            ]);
        }

        event(new Registered($user));

        return redirect()->route('login')->with('success', 'SUCESSO! Cadastro realizado, aguarde a aprovação por um administrador ou diretor.');
    }
}