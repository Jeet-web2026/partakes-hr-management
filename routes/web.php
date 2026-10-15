<?php

use App\Http\Controllers\AddAgreementController;
use App\Http\Controllers\AgreementNameController;
use App\Http\Controllers\DashboardManagementController;
use App\Http\Controllers\DraftAgreementManagementController;
use App\Http\Controllers\EditAgreementController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Optimization\OptimizationController;
use App\Http\Controllers\RenewalAgreementController;
use App\Http\Controllers\ViewAgreementController;
use Illuminate\Support\Facades\Route;

Route::controller(HomeController::class)->group(function () {
    Route::get('/', 'index')->name('home');
    Route::post('/select-organisation', 'selectOrganisation')->name('select-organisation');
});

Route::middleware('partaker.session')->group(function () {
    Route::controller(HomeController::class)->group(function () {
        Route::get('agreements', 'allAgreements')->name('all-agreements');
        Route::get('agreements/data', 'agreementsData')->name('agreements.data');
        Route::get('drafts/data', 'draftAgreementsData')->name('drafts.data');
        Route::get('agreements-name/data', 'agreementNamesData')->name('agreements-name.data');
    });

    Route::controller(AddAgreementController::class)->group(function () {
        Route::get('add-agreement', 'index')->name('add-agreement');
        Route::get('agreement-types/{category}', 'getAgreementTypes')
            ->name('agreement.types');
        Route::post('agreements', 'store')
            ->name('agreements.store');
    });

    Route::controller(DraftAgreementManagementController::class)->group(function () {
        Route::get('draft-agreement', 'index')->name('draft-agreement');
        Route::post('draft-agreements', 'store')
            ->name('draft-agreements.store');
        Route::delete('draft/{agreement}', 'destroy')->name('draft.destroy');
    });

    Route::controller(EditAgreementController::class)->group(function () {
        Route::get('edit-agreement/{agreementId}', 'editAgreement')->name('edit-agreement');
        Route::put('agreements/{agreement}', 'updateAgreement')
            ->name('agreements.update');
    });

    Route::controller(RenewalAgreementController::class)->group(function () {
        Route::get('renewal-agreement/{agreementId}', 'renewalAgreement')->name('renewal-agreement');
        Route::post('agreements/{agreementId}/renew', 'renewAgreement')
            ->name('agreements.renew');
    });

    Route::controller(ViewAgreementController::class)->group(function () {
        Route::get('view-agreement/{agreementId}', 'index')->name('view-agreement');
        Route::get('agreements/{id}/history', 'history')->name('agreements.history');
        Route::delete('agreements/{agreement}', 'destroy')->name('agreements.destroy');
        Route::post('/agreements/{agreement}/generate-view-link', 'generateViewLink')->name('agreements.generate-view-link');
        Route::post('/drafts/{draft}/generate-view-link', 'generateDraftViewLink')->name('drafts.generate-view-link');

        Route::get('/files/{agreement}/download', 'download')
            ->name('agreements.download');

        Route::get('/files/{draft}/{columnName}/download', 'draftdownload')
            ->name('drafts.download');
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

Route::get('/agreements/public/{token}/{timeStamp}', [ViewAgreementController::class, 'publicView'])
    ->name('agreements.public-view');
