<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CommissionController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\LeadActivityController;
use App\Http\Controllers\Api\LeadController;
use App\Http\Controllers\Api\LeadDocumentController;
use App\Http\Controllers\Api\LeadReminderController;
use App\Http\Controllers\Api\MarketingMaterialController;
use App\Http\Controllers\Api\PartnerDocumentController;
use App\Http\Controllers\Api\PipelineController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\SupportTicketController;
use App\Http\Controllers\Api\WithdrawalController;
use App\Http\Controllers\Api\WithdrawalDocumentController;
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
        Route::get('dashboard', [DashboardController::class, 'summary'])->name('dashboard');

        Route::get('pipeline', [PipelineController::class, 'board'])->name('pipeline.board');

        Route::prefix('leads')->name('leads.')->group(function () {
            Route::get('/', [LeadController::class, 'index'])->name('index');
            Route::post('/', [LeadController::class, 'store'])->name('store');
            Route::get('{lead}', [LeadController::class, 'show'])->name('show');
            Route::put('{lead}', [LeadController::class, 'update'])->name('update');
            Route::patch('{lead}/status', [LeadController::class, 'updateStatus'])->name('status');
            Route::delete('{lead}', [LeadController::class, 'destroy'])->name('destroy');

            Route::get('{lead}/reminders', [LeadReminderController::class, 'index'])->name('reminders.index');
            Route::post('{lead}/reminders', [LeadReminderController::class, 'store'])->name('reminders.store');
            Route::put('{lead}/reminders/{reminder}', [LeadReminderController::class, 'update'])->name('reminders.update');
            Route::patch('{lead}/reminders/{reminder}/complete', [LeadReminderController::class, 'complete'])->name('reminders.complete');
            Route::delete('{lead}/reminders/{reminder}', [LeadReminderController::class, 'destroy'])->name('reminders.destroy');

            Route::get('{lead}/documents', [LeadDocumentController::class, 'index'])->name('documents.index');
            Route::post('{lead}/documents', [LeadDocumentController::class, 'store'])->name('documents.store');
            Route::get('{lead}/documents/{document}/download', [LeadDocumentController::class, 'download'])->name('documents.download');
            Route::delete('{lead}/documents/{document}', [LeadDocumentController::class, 'destroy'])->name('documents.destroy');

            Route::get('{lead}/activities', [LeadActivityController::class, 'index'])->name('activities.index');
            Route::post('{lead}/activities', [LeadActivityController::class, 'store'])->name('activities.store');
        });

        Route::prefix('customers')->name('customers.')->group(function () {
            Route::get('/', [CustomerController::class, 'index'])->name('index');
            Route::get('{customer}', [CustomerController::class, 'show'])->name('show');
            Route::put('{customer}', [CustomerController::class, 'update'])->name('update');
            Route::patch('{customer}/progress', [CustomerController::class, 'updateProgress'])->name('progress');
        });

        Route::prefix('projects')->name('projects.')->group(function () {
            Route::get('/', [ProjectController::class, 'index'])->name('index');
            Route::get('{project}', [ProjectController::class, 'show'])->name('show');
            Route::post('{project}/claim', [ProjectController::class, 'claim'])->name('claim');
            Route::post('{project}/cancel-claim', [ProjectController::class, 'cancelClaim'])->name('cancel-claim');
        });

        Route::prefix('commissions')->name('commissions.')->group(function () {
            Route::get('/', [CommissionController::class, 'index'])->name('index');
            Route::get('{commission}', [CommissionController::class, 'show'])->name('show');
        });

        Route::prefix('withdrawals')->name('withdrawals.')->group(function () {
            Route::get('balance', [WithdrawalController::class, 'balance'])->name('balance');
            Route::get('/', [WithdrawalController::class, 'index'])->name('index');
            Route::post('/', [WithdrawalController::class, 'store'])->name('store');
            Route::get('{withdrawal}', [WithdrawalController::class, 'show'])->name('show');
            Route::get('{withdrawal}/documents/{type}', [WithdrawalDocumentController::class, 'show'])->name('documents');
        });

        Route::prefix('marketing-materials')->name('marketing-materials.')->group(function () {
            Route::get('/', [MarketingMaterialController::class, 'index'])->name('index');
            Route::get('{marketingMaterial}', [MarketingMaterialController::class, 'show'])->name('show');
        });

        Route::prefix('support-tickets')->name('support-tickets.')->group(function () {
            Route::get('/', [SupportTicketController::class, 'index'])->name('index');
            Route::post('/', [SupportTicketController::class, 'store'])->name('store');
            Route::get('{supportTicket}', [SupportTicketController::class, 'show'])->name('show');
        });

        // notifications routes added in Fase 11
    });
});
