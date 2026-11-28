<?php

use App\Domain\Localization\Enums\Locale;
use App\Http\Controllers\LocaleSwitchController;
use App\Http\Controllers\RootRedirectController;
use Illuminate\Support\Facades\Route;

Route::get('/', RootRedirectController::class)->name('root');

Route::get('/locale/{locale}', LocaleSwitchController::class)
    ->whereIn('locale', Locale::values())
    ->name('locale.switch');

Route::prefix('{locale}')
    ->whereIn('locale', Locale::values())
    ->middleware('locale')
    ->name('public.')
    ->group(function (): void {
        Route::view('/', 'welcome')->name('home');
        Route::view('/privacy', 'privacy')->name('privacy');
    });

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
});

require __DIR__.'/settings.php';

// Route unmatched GET/HEAD requests through the web middleware group so EncryptCookies decrypts `locale` on 404 pages.
Route::fallback(static fn (): never => abort(404));
