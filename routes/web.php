<?php

use App\Models\User; 
use App\Models\Usuario; 
use Illuminate\Support\Str;

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController; 
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PageController;

use App\Http\Controllers\ConfiguracoesController;
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
use App\Http\Controllers\ProfileController;


use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

// --- ROTAS PÚBLICAS (PARA VISITANTES) ---
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');

    Route::post('/login', [AuthenticatedSessionController::class, 'store']);

    Route::get('/register', fn() => view('auth.register'))->name('register');

    Route::post('/register', function (Request $request) {
        $request->validate([
            'nome_completo' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:usuarios'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'username' => ['required', 'string', 'max:80', 'unique:usuarios'],
            'data_nascimento' => ['nullable', 'date'],
            'cpf' => ['nullable', 'string', 'max:14', 'unique:usuarios'],
            'rg' => ['nullable', 'string', 'max:20', 'unique:usuarios'],
            'rco_siape' => ['required', 'string', 'max:50', 'unique:usuarios'],
            'telefone' => ['nullable', 'string', 'max:20'],
            'formacao' => ['nullable', 'string', 'max:255'],
            'area_formacao' => ['nullable', 'string', 'max:255'],
        ]);

        $user = Usuario::create([
            'nome_completo' => $request->nome_completo,
            'email' => $request->email,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'data_nascimento' => $request->data_nascimento,
            'cpf' => $request->cpf,
            'rg' => $request->rg,
            'rco_siape' => $request->rco_siape,
            'telefone' => $request->telefone,
            'formacao' => $request->formacao,
            'area_formacao' => $request->area_formacao,
            'data_registro' => now(),
            'status_aprovacao' => 'pendente', 
            'tipo_usuario' => 'professor',   
        ]);
        
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
    
    Route::middleware(['password.confirm'])->group(function () {
        Route::post('/settings/backup', [ConfiguracoesController::class, 'runBackup'])->name('settings.backup.run');
        Route::get('/settings/backup/download/{filename}', [ConfiguracoesController::class, 'downloadBackup'])->name('settings.backup.download');
        Route::post('/settings/backup/restore', [ConfiguracoesController::class, 'uploadAndRestore'])->name('settings.backup.restore');
    });

    Route::resource('agendamentos', AgendamentoController::class);
    Route::resource('componentes', ComponenteCurricularController::class);
    Route::resource('escolas', EscolaController::class)->parameters(['escolas' => 'escola']);
    Route::resource('municipios', MunicipioController::class);
    Route::resource('turmas', TurmaController::class);
    Route::resource('recursos-didaticos', RecursoDidaticoController::class)
        ->parameters(['recursos-didaticos' => 'recurso_didatico']) 
        ->names('resources');
    Route::resource('usuarios', UsuarioController::class);
    
    // --- ROTA DE LOGOUT ---
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});

require __DIR__.'/auth.php';
