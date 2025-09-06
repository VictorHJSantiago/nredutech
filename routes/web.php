<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AgendamentoController;
use App\Http\Controllers\ComponenteCurricularController;
use App\Http\Controllers\EscolaController;
use App\Http\Controllers\MunicipioController;
use App\Http\Controllers\RecursoDidaticoController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\ConfiguracoesController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TurmaController;

/*
|--------------------------------------------------------------------------
| Rotas da Aplicação Web
|--------------------------------------------------------------------------
*/

// --- ROTAS PÚBLICAS (PARA VISITANTES) ---
// Estas rotas mostram as páginas de login e registro.
// As submissões de formulário (POST) são tratadas pelo routes/auth.php.
Route::middleware('guest')->group(function () {
    Route::get('register', function () {
        return view('auth.register');
    })->name('register');

    Route::get('login', function () {
        return view('auth.login');
    })->name('login');
});


// --- ROTAS PROTEGIDAS (Acessíveis apenas por usuários logados) ---
Route::middleware(['auth'])->group(function () {

    Route::get('/', [DashboardController::class, 'index'])->name('index');
    Route::get('/dashboard', fn() => redirect()->route('index'))->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    Route::get('/settings', [ConfiguracoesController::class, 'index'])->name('settings');
    Route::patch('/settings/preferences', [ConfiguracoesController::class, 'updatePreferences'])->name('settings.preferences.update');
    Route::post('/settings/backup', [ConfiguracoesController::class, 'runBackup'])->name('settings.backup.run');

    Route::resource('agendamentos', AgendamentoController::class);
    Route::resource('componentes', ComponenteCurricularController::class);
    Route::resource('municipios', MunicipioController::class);
    Route::resource('escolas', EscolaController::class);
    Route::resource('turmas', TurmaController::class);
    Route::resource('recursos-didaticos', RecursoDidaticoController::class)->names('resources');
    Route::resource('usuarios', UsuarioController::class);
    
    // --- LOGOUT ---
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});

// Importa as rotas de autenticação (POST de login/registro, etc.)
require __DIR__.'/auth.php';

