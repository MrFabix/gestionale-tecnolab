<?php

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AttrezzaturaController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\CommessaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OffertaController;
use App\Http\Controllers\PersonaleController;
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
    Route::get('/eventi/feed', [\App\Http\Controllers\EventController::class, 'feed'])->name('eventi.feed');
    Route::resource('eventi', \App\Http\Controllers\EventController::class);

    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::post('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.updatePassword');
    Route::get('/', [DashboardController::class, 'index'])->name('home');



    Route::resource('attrezzature', AttrezzaturaController::class)
        ->parameters(['attrezzature' => 'attrezzatura']);

// Cancellazione singolo file media
    Route::delete('attrezzature/{attrezzatura}/media/{mediaId}', [AttrezzaturaController::class, 'destroyMedia'])
        ->name('attrezzature.media.destroy');

// Tarature
    Route::post('attrezzature/{attrezzatura}/tarature', [AttrezzaturaController::class, 'storeTaratura'])
        ->name('attrezzature.tarature.store');
    Route::put('attrezzature/{attrezzatura}/tarature/{taratura}', [AttrezzaturaController::class, 'updateTaratura'])
        ->name('attrezzature.tarature.update');
    Route::delete('attrezzature/{attrezzatura}/tarature/{taratura}', [AttrezzaturaController::class, 'destroyTaratura'])
        ->name('attrezzature.tarature.destroy');

// Manutenzioni
    Route::post('attrezzature/{attrezzatura}/manutenzioni', [AttrezzaturaController::class, 'storeManutenzione'])
        ->name('attrezzature.manutenzioni.store');
    Route::put('attrezzature/{attrezzatura}/manutenzioni/{manutenzione}', [AttrezzaturaController::class, 'updateManutenzione'])
        ->name('attrezzature.manutenzioni.update');
    Route::delete('attrezzature/{attrezzatura}/manutenzioni/{manutenzione}', [AttrezzaturaController::class, 'destroyManutenzione'])
        ->name('attrezzature.manutenzioni.destroy');

    Route::resource('personale', PersonaleController::class);

// Cancellazione singolo documento
    Route::delete('personale/{personale}/media/{mediaId}', [PersonaleController::class, 'destroyMedia'])
        ->name('personale.media.destroy');

// Formazione
    Route::post('personale/{personale}/formazioni', [PersonaleController::class, 'storeFormazione'])
        ->name('personale.formazioni.store');
    Route::put('personale/{personale}/formazioni/{formazione}', [PersonaleController::class, 'updateFormazione'])
        ->name('personale.formazioni.update');
    Route::delete('personale/{personale}/formazioni/{formazione}', [PersonaleController::class, 'destroyFormazione'])
        ->name('personale.formazioni.destroy');

    Route::resource('offerte', OffertaController::class)->parameters(['offerte' => 'offerta']);

// Azioni extra
    Route::post('offerte/{offerta}/accetta',      [OffertaController::class, 'accetta'])->name('offerte.accetta');
    Route::get('offerte/{offerta}/download-word', [OffertaController::class, 'downloadWord'])->name('offerte.downloadWord');
    Route::delete('offerte/{offerta}/media/{mediaId}', [OffertaController::class, 'destroyMedia'])->name('offerte.media.destroy');


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
    Route::resource('users', UserController::class)->parameters(['users' => 'user']);
    Route::post('users/{user}/send-credentials', [UserController::class, 'sendCredentials'])->name('users.sendCredentials');
    Route::get('users/logs', [UserController::class, 'logs'])->name('users.logs');
});






Auth::routes();
