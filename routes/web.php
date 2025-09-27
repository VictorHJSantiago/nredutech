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

    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::delete('/notifications/{notificacao}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
    Route::post('/notifications/clear-all', [NotificationController::class, 'clearAll'])->name('notifications.clearAll');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
    Route::patch('/settings/preferences', [SettingsController::class, 'updatePreferences'])->name('settings.preferences.update');

    Route::get('/settings/backup/initiate', [SettingsController::class, 'initiateBackup'])->name('settings.backup.initiate')->middleware('password.confirm');
    Route::get('/settings/backup/download/latest', [SettingsController::class, 'downloadLatestBackup'])->name('settings.backup.download.latest');
    Route::get('/settings/backup/download-file/{filename}', [SettingsController::class, 'downloadFile'])->name('settings.backup.download-file');

    Route::middleware(['password.confirm'])->group(function () {
        Route::get('/settings/backup/download/{filename}', [SettingsController::class, 'downloadBackup'])->name('settings.backup.download');
        Route::post('/settings/backup/restore', [SettingsController::class, 'uploadAndRestore'])->name('settings.backup.restore');
    });


    Route::resource('agendamentos', AppointmentController::class);
    Route::resource('componentes', CurricularComponentController::class);
    Route::resource('escolas', SchoolController::class)->parameters(['escolas' => 'escola']);
    Route::resource('municipios', CityController::class);
    Route::resource('turmas', SchoolClassController::class);
    Route::resource('ofertas', CourseOfferingController::class)->parameters(['ofertas' => 'ofertaComponente']);
    Route::resource('recursos-didaticos', DidacticResourceController::class)
        ->parameters(['recursos-didaticos' => 'recursoDidatico'])
        ->names('resources');

    Route::resource('usuarios', UserController::class);

    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
});

require __DIR__.'/auth.php';