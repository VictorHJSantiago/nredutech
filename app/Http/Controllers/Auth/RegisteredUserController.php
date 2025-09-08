<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterUserRequest; 
use App\Models\User;
use App\Models\Usuario;
use App\Models\Escola;
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

        Usuario::create([
            'nome_completo' => $data['name'],
            'email' => $data['email'],
            'username' => $data['username'],
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

        event(new Registered($user));

        return redirect()->route('index');
    }
}
