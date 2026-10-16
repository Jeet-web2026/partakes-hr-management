<?php

use App\Http\Controllers\AgreementNameController;
use App\Http\Controllers\DashboardManagementController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Optimization\OptimizationController;
use Illuminate\Support\Facades\Route;

Route::controller(HomeController::class)->group(function () {
    Route::get('/', 'index')->name('home');
    Route::post('/select-organisation', 'selectOrganisation')->name('select-organisation');
});

Route::middleware('partaker.session')->group(function () {
    Route::controller(HomeController::class)->group(function () {
        Route::get('agreements-name/data', 'agreementNamesData')->name('agreements-name.data');
        Route::get('/generate-link', 'generateLink')
            ->name('tiny-url.store');
    });

    Route::controller(AgreementNameController::class)->group(function () {
        Route::get('add-agreement-name', 'index')->name('add-agreement-name');
        Route::post('agreement-names', 'store')
            ->name('agreement-names.store');
        Route::delete('/agreement-names/{id}', 'destroy')
            ->name('agreement-names.destroy');
        Route::get('fetchNameByCategoryandType', 'fetchAgreementName')->name('fetchagreements.name');
    });

    Route::controller(FeedbackController::class)->group(function () {
        Route::post('feedback-store', 'store')->name('feedback-store');
    });

    Route::controller(DashboardManagementController::class)->group(function () {
        Route::post('dashboard/data', 'index')->name('dashboard-data');
    });

    Route::prefix('optimize')->controller(OptimizationController::class)->group(function () {
        Route::get('cache', 'cache')->name('optimize.cache');
        Route::get('clear', 'clear')->name('optimize.clear');
    });

    Route::fallback(function () {
        return redirect()->route('home');
    });
});

Route::get('/t/{token}', [HomeController::class, 'redirect'])
    ->where('token', '[a-z0-9]{6,12}')
    ->name('tiny-url.redirect');
