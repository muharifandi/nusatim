<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PartnerDocumentController;
use App\Http\Controllers\Api\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Partner Mobile API
|--------------------------------------------------------------------------
|
| Token-based (Sanctum) API for the Partner Portal mobile app - a separate
| surface from the session-based `partner` guard used by the Filament
| Partner Portal panel. Every module here wraps the same model/business
| logic the Filament panel already uses (Lead, PartnerProject, Commission,
| Withdrawal, etc.) rather than reimplementing it.
|
*/

Route::prefix('v1')->name('api.v1.')->group(function () {

    // Public - no token required.
    Route::prefix('auth')->name('auth.')->group(function () {
        Route::post('register', [AuthController::class, 'register'])->name('register');
        Route::post('login', [AuthController::class, 'login'])->name('login');
    });

    // Authenticated, but NOT gated behind partner-approval - a partner
    // pending/rejected/suspended can still manage their own account and
    // see why they're gated (mirrors EnsurePartnerApproved's own exemption
    // for the status page + logout in the Filament panel).
    Route::middleware('auth:api')->group(function () {
        Route::prefix('auth')->name('auth.')->group(function () {
            Route::post('logout', [AuthController::class, 'logout'])->name('logout');
            Route::get('me', [AuthController::class, 'me'])->name('me');
        });

        Route::prefix('profile')->name('profile.')->group(function () {
            Route::get('/', [ProfileController::class, 'show'])->name('show');
            Route::put('/', [ProfileController::class, 'update'])->name('update');
            Route::post('photo', [ProfileController::class, 'updatePhoto'])->name('photo');
            Route::post('ktp', [ProfileController::class, 'updateKtp'])->name('ktp');
            Route::post('npwp', [ProfileController::class, 'updateNpwp'])->name('npwp');
            Route::put('password', [ProfileController::class, 'changePassword'])->name('password');
            Route::get('documents/{type}', [PartnerDocumentController::class, 'show'])->name('documents');
        });
    });

    // Authenticated AND approved - all business-data modules.
    Route::middleware(['auth:api', 'partner.approved.api'])->group(function () {
        // dashboard, leads, pipeline, customers, projects, commissions,
        // withdrawals, marketing-materials, support-tickets, notifications
        // routes added in Fase 3-11
    });
});
