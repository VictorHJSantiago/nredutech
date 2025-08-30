<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User; 
use Illuminate\Support\Str;

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PageController;


use App\Http\Controllers\AgendamentoController;
use App\Http\Controllers\ComponenteCurricularController;
use App\Http\Controllers\EscolaController;
use App\Http\Controllers\MunicipioController;
use App\Http\Controllers\NotificacaoController;
use App\Http\Controllers\OfertaComponenteController;
use App\Http\Controllers\RecursoDidaticoController;
use App\Http\Controllers\TurmaController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\UsuarioPreferenciaController;
use App\Http\Controllers\ConfiguracoesController; 
use App\Http\Controllers\ProfileController;

// --- ROTAS PÚBLICAS (PARA VISITANTES) ---
Route::middleware('guest')->group(function () {
    Route::get('/login', fn() => view('auth.login'))->name('login');

    Route::post('/login', function (Request $request) {
        $request->validate(['email' => ['required', 'email']]);
        $user = User::firstOrCreate(
            ['email' => $request->email],
            ['name' => 'Usuário ' . Str::before($request->email, '@'), 'password' => Hash::make(Str::random(16))]
        );
        Auth::login($user, true);
        $request->session()->regenerate();
        return redirect()->intended('/');
    });

    Route::get('/register', fn() => view('auth.register'))->name('register');

    Route::post('/register', function (Request $request) {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
        $user = User::create($request->only('name', 'email', 'password'));
        Auth::login($user);
        return redirect()->route('index');
    });
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

