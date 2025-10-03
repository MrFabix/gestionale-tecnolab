<?php

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\CommessaController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Auth::routes();
Auth::routes(['register' => false]);



//route da autentificato
Route::middleware(['auth'])->group(function () {
    Route::resource('commesse', CommessaController::class)->parameters(['commesse' => 'commessa']);
    Route::resource('reports', ReportController::class); // resource completa
    Route::resource('clienti', ClienteController::class)->parameters(['clienti' => 'cliente']);
    Route::resource('eventi', \App\Http\Controllers\EventController::class);
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::post('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.updatePassword');
    Route::get('/', function () {return view('home');})->name('home');



    // Wizard
    Route::prefix('reports/wizard')->name('reports.wizard.')->group(function () {
        Route::get('step1', [ReportController::class, 'createStep1'])->name('step1');
        Route::post('step1', [ReportController::class, 'postStep1'])->name('step1.post');
        Route::get('step2', [ReportController::class, 'createStep2'])->name('step2');
        Route::post('step2', [ReportController::class, 'postStep2'])->name('step2.post');
        Route::get('step3', [ReportController::class, 'createStep3'])->name('step3');
        Route::post('step3', [ReportController::class, 'postStep3'])->name('step3.post');
    });

    // Wizard Modifica Report
    Route::prefix('reports/{report}/edit-wizard')->name('reports.editwizard.')->group(function () {
        Route::get('step1', [ReportController::class, 'editStep1'])->name('step1');
        Route::post('step1', [ReportController::class, 'updateStep1'])->name('step1.post');
        Route::get('step2', [ReportController::class, 'editStep2'])->name('step2');
        Route::post('step2', [ReportController::class, 'updateStep2'])->name('step2.post');
        Route::get('step3', [ReportController::class, 'editStep3'])->name('step3');
        Route::post('step3', [ReportController::class, 'updateStep3'])->name('step3.post');
    });

    Route::get('/reports/{report}/download-pdf', [\App\Http\Controllers\ReportController::class, 'downloadPdf'])->name('reports.downloadPdf');
});

Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('users', UserController::class);
    Route::get('users/logs', [UserController::class, 'logs'])->name('users.logs');
});

Auth::routes();
