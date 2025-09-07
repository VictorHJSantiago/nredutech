<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Usuario; 
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\Rule; 
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'nome_completo' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:80', 'unique:'.Usuario::class],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.Usuario::class],
            'data_nascimento' => ['nullable', 'date'],
            'cpf' => ['nullable', 'string', 'max:14', 'unique:'.Usuario::class],
            'rg' => ['nullable', 'string', 'max:20', 'unique:'.Usuario::class],
            'rco_siape' => ['nullable', 'string', 'max:50', 'unique:'.Usuario::class],
            'telefone' => ['nullable', 'string', 'max:20'],
            'formacao' => ['nullable', 'string', 'max:255'],
            'area_formacao' => ['nullable', 'string', 'max:255'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = Usuario::create([
            'nome_completo' => $request->nome_completo,
            'username' => $request->username,
            'email' => $request->email,
            'data_nascimento' => $request->data_nascimento,
            'cpf' => $request->cpf,
            'rg' => $request->rg,
            'telefone' => $request->telefone,
            'rco_siape' => $request->rco_siape,
            'formacao' => $request->formacao,
            'area_formacao' => $request->area_formacao,
            'password' => Hash::make($request->password),
            'data_registro' => now(),
            'status_aprovacao' => 'pendente', 
            'tipo_usuario' => 'professor', 
        ]);

        event(new Registered($user));

        return redirect()->route('login')->with('success', 'SUCESSO! Cadastro realizado e aguardando aprovação de um administrador.');
    }
}

