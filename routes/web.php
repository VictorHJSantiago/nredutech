<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AgendamentoController;
use App\Http\Controllers\ComponenteCurricularController;
use App\Http\Controllers\EscolaController;
use App\Http\Controllers\MunicipioController;
use App\Http\Controllers\OfertaComponenteController;
use App\Http\Controllers\RecursoDidaticoController;
use App\Http\Controllers\TurmaController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\ConfiguracoesController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\NotificacaoController;

Route::middleware(['auth'])->group(function () {

    Route::get('/', [DashboardController::class, 'index'])->name('index');
    Route::get('/dashboard', fn() => redirect()->route('index'))->name('dashboard');
    Route::get('/notifications', [NotificacaoController::class, 'index'])->name('notifications.index');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/settings', [ConfiguracoesController::class, 'index'])->name('settings');
    Route::patch('/settings/preferences', [ConfiguracoesController::class, 'updatePreferences'])->name('settings.preferences.update');

    Route::get('/settings/backup/initiate', [ConfiguracoesController::class, 'initiateBackup'])->name('settings.backup.initiate')->middleware('password.confirm');
    Route::get('/settings/backup/download/latest', [ConfiguracoesController::class, 'downloadLatestBackup'])->name('settings.backup.download.latest');
    Route::get('/settings/backup/download-file/{filename}', [ConfiguracoesController::class, 'downloadFile'])->name('settings.backup.download-file');

    Route::middleware(['password.confirm'])->group(function () {
        Route::get('/settings/backup/download/{filename}', [ConfiguracoesController::class, 'downloadBackup'])->name('settings.backup.download');
        Route::post('/settings/backup/restore', [ConfiguracoesController::class, 'uploadAndRestore'])->name('settings.backup.restore');
    });


    Route::resource('agendamentos', AgendamentoController::class);
    Route::resource('componentes', ComponenteCurricularController::class);
    Route::resource('escolas', EscolaController::class)->parameters(['escolas' => 'escola']);
    Route::resource('municipios', MunicipioController::class);
    Route::resource('turmas', TurmaController::class);
    Route::resource('ofertas', OfertaComponenteController::class)->parameters(['ofertas' => 'ofertaComponente']);
    Route::resource('recursos-didaticos', RecursoDidaticoController::class)
        ->parameters(['recursos-didaticos' => 'recursoDidatico'])
        ->names('resources');

    Route::resource('usuarios', UsuarioController::class);
});

require __DIR__.'/auth.php';