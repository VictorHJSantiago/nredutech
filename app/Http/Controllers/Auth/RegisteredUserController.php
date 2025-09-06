<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Usuario; 
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Illuminate\Validation\Rule; 

class RegisteredUserController extends Controller
{
    public function create()
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
            'username' => ['required', 'string', 'max:255', 'unique:'.Usuario::class],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.Usuario::class],
            'data_nascimento' => ['required', 'date'],
            'cpf' => ['required', 'string', 'max:14', 'unique:'.Usuario::class], 
            'rg' => ['required', 'string', 'max:20', 'unique:'.Usuario::class],  
            'telefone' => ['required', 'string', 'max:20'],
            'rco_siape' => ['required', 'string', 'max:255'],
            'formacao' => ['required', 'string', 'max:255'],
            'area_formacao' => ['required', 'string', 'max:255'],
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
            'tipo_usuario' => 'padrao',
            'status_aprovacao' => 'pendente', 
        ]);

        event(new Registered($user));

        return redirect()->route('login')->with('success', 'CADASTRO REALIZADO COM SUCESSO! Só aguardar a aprovação pelo administrador.');
    }
}