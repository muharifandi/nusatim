<?php

use App\Http\Controllers\ComingSoonController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LeadDocumentController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PartnerDocumentController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\PricingController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ReportExportController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\WithdrawalDocumentController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [PageController::class, 'about'])->name('about');

Route::get('/services', [ServiceController::class, 'index'])->name('services.index');
Route::get('/services/{service:slug}', [ServiceController::class, 'show'])->name('services.show');

Route::get('/portfolio', [ProjectController::class, 'index'])->name('portfolio');
Route::get('/portfolio/{project:slug}', [ProjectController::class, 'show'])->name('portfolio.show');

Route::get('/pricing', [PricingController::class, 'index'])->name('pricing');

Route::get('/team', [TeamController::class, 'index'])->name('team');

Route::get('/blog', [PostController::class, 'index'])->name('blog.index');
Route::get('/blog/{post:slug}', [PostController::class, 'show'])->name('blog.show');

Route::get('/faq', [FaqController::class, 'index'])->name('faq');

Route::get('/contact', [ContactController::class, 'show'])->name('contact');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');

Route::get('/coming-soon', [ComingSoonController::class, 'index'])->name('coming-soon');
Route::post('/newsletter/subscribe', [NewsletterController::class, 'store'])->name('newsletter.subscribe');

Route::get('/partner-documents/{partner}/{type}', [PartnerDocumentController::class, 'show'])
    ->middleware(['auth:web,partner'])
    ->name('partner.documents.show');

Route::get('/lead-documents/{leadDocument}', [LeadDocumentController::class, 'show'])
    ->middleware(['auth:web,partner'])
    ->name('lead.documents.show');

Route::get('/withdrawal-documents/{withdrawal}/{type}', [WithdrawalDocumentController::class, 'show'])
    ->middleware(['auth:web,partner'])
    ->name('withdrawal.documents.show');

Route::get('/admin/reports/export/{report}', [ReportExportController::class, 'export'])
    ->middleware(['auth:web'])
    ->name('admin.reports.export');

Route::get('/partner/stop-impersonating', function () {
    // Guard 'web' is never touched by impersonation (see PartnerResource's
    // 'impersonate' action) - it either was, and still is, a valid admin
    // session, or it never was, in which case a spoofed session flag alone
    // grants nothing (this route never performs a login, only a logout).
    if (session()->has('impersonating_admin_id') && \Illuminate\Support\Facades\Auth::guard('web')->check()) {
        \Illuminate\Support\Facades\Auth::guard('partner')->logout();
        session()->forget('impersonating_admin_id');
    }

    return redirect('/admin/partners');
})->middleware(['auth:partner'])->name('partner.stop-impersonating');
