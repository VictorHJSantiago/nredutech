<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\CurricularComponentController;
use App\Http\Controllers\SchoolController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\CourseOfferingController;
use App\Http\Controllers\DidacticResourceController;
use App\Http\Controllers\SchoolClassController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\NotificationController;

Route::middleware(['auth'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('index');


    Route::get('/dashboard', fn() => redirect()->route('index'))->name('dashboard');
    Route::get('/notificacoes', [NotificationController::class, 'index'])->name('notifications.index');
    Route::delete('/notificacoes/{notificacao}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
    Route::post('/notificacoes/clear-all', [NotificationController::class, 'clearAll'])->name('notifications.clearAll');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/configuracoes', [SettingsController::class, 'index'])->name('settings');
    Route::patch('/configuracoes/preferences', [SettingsController::class, 'updatePreferences'])->name('settings.preferences.update');
    Route::middleware(['can:administrador'])->group(function() {
        Route::patch('/configuracoes/backup/schedule', [SettingsController::class, 'updateBackupSchedule'])->name('settings.backup.schedule.update');
        Route::get('/configuracoes/backup/download-file/{filename}', [SettingsController::class, 'downloadFile'])->name('settings.backup.download-file');
        Route::get('/configuracoes/backup/download/latest', [SettingsController::class, 'downloadLatestBackup'])->name('settings.backup.download.latest');

        // Rotas que exigem confirmação de senha
        Route::middleware(['password.confirm'])->group(function () {
            Route::get('/configuracoes/backup/initiate', [SettingsController::class, 'initiateBackup'])->name('settings.backup.initiate');
            Route::get('/configuracoes/backup/download/{filename}', [SettingsController::class, 'downloadBackup'])->name('settings.backup.download');
            Route::get('/configuracoes/backup/restore', [SettingsController::class, 'showRestorePage'])->name('settings.backup.restore');
            Route::post('/configuracoes/backup/restore-upload', [SettingsController::class, 'uploadAndRestore'])->name('settings.backup.restore-upload');
        });
    });


    // Rotas de Recursos CRUD (Acesso Geral, controlado nos controllers)
    Route::get('/agendamentos/events', [AppointmentController::class, 'getCalendarEvents'])->name('appointments.events');
    Route::post('/agendamentos/availability', [AppointmentController::class, 'getAvailabilityForDate'])->name('appointments.availability');
    Route::resource('agendamentos', AppointmentController::class);
    Route::resource('componentes', CurricularComponentController::class);
    Route::resource('turmas', SchoolClassController::class);
    Route::resource('ofertas', CourseOfferingController::class)->parameters(['ofertas' => 'ofertaComponente']);
    Route::resource('recursos-didaticos', DidacticResourceController::class)
        ->parameters(['recursos-didaticos' => 'recursoDidatico'])
        ->names('resources');
    Route::resource('usuarios', UserController::class); 

    Route::get('/relatorios', [ReportController::class, 'index'])->name('reports.index'); 
    Route::middleware(['can:administrador'])->group(function () {
        Route::resource('escolas', SchoolController::class)->parameters(['escolas' => 'escola']);
        Route::resource('municipios', CityController::class);
    });
});

require __DIR__.'/auth.php';