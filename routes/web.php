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

    Route::get('/reports', [PageController::class, 'reports'])->name('reports');
    Route::get('/settings', [PageController::class, 'settings'])->name('settings');

    Route::resource('componentes', controller: ComponenteCurricularController::class);
    Route::resource('municipios', MunicipioController::class);
    Route::resource('escolas', EscolaController::class);
    Route::resource('turmas', TurmaController::class);
    Route::resource('users', UsuarioController::class)->names(['index' => 'user-list']);
    Route::resource('disciplines', ComponenteCurricularController::class)->names(['index' => 'discipline-list']);
    Route::resource('oferta-componentes', OfertaComponenteController::class);
    Route::resource('recursos-didaticos', RecursoDidaticoController::class)
        ->parameter('recursos-didaticos', 'recurso_didatico') 
        ->names('resources');
    Route::resource('notificacoes', NotificacaoController::class);
    Route::resource('usuarios', UsuarioController::class);
    
    Route::get('/professors', [UsuarioController::class, 'index'])->name('professor-list');
    Route::get('/laboratories', fn() => redirect()->route('laboratory-list'))->name('laboratory-list');
    Route::patch('notificacoes/{notificacao}/marcar-como-lida', [NotificacaoController::class, 'marcarComoLida'])->name('notificacoes.marcar-como-lida'); 
    
    Route::get('users/{usuario}/preferences', [UsuarioPreferenciaController::class, 'show'])->name('users.preferences.show');
    Route::put('users/{usuario}/preferences', [UsuarioPreferenciaController::class, 'update'])->name('users.preferences.update');
    Route::delete('users/{usuario}/preferences', [UsuarioPreferenciaController::class, 'destroy'])->name('users.preferences.destroy');


    // --- LOGOUT ---
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});