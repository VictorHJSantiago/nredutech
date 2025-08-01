<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Aqui é onde você registra as rotas web para sua aplicação.
|
*/

// Rota para exibir o formulário de login
Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');

// Rota para processar o envio do formulário de login
Route::post('/login', [AuthenticatedSessionController::class, 'store']);

// Rota para a página de cadastro
Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');

// Você pode adicionar outras rotas aqui...
Route::get('/', function () {
    return view('welcome');
});