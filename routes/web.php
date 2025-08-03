<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;

// Rota para exibir o formulário de login (acessível em /login)
Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');

// Rota para processar o envio do formulário
Route::post('/login', [AuthenticatedSessionController::class, 'store']);

// Rota para a página de cadastro
Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');

// Rota raiz (/) agora redireciona para a rota de login. Esta é a melhor prática.
Route::get('/', function () {
    return redirect()->route('login');
});