<?php

use App\Domain\Localization\Enums\Locale;
use App\Http\Controllers\LocaleSwitchController;
use App\Http\Controllers\RootRedirectController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

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
    });

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
});

Route::post('/admin/ckeditor/upload', function (Request $request) {
    $request->validate([
        'upload' => ['required', 'file', 'mimes:jpg,jpeg,png,gif,webp,svg', 'max:5120'], // Maks 5MB
    ]);

    $path = $request->file('upload')->store('ckeditor-uploads', 'public');

    return response()->json([
        'url' => Storage::disk('public')->url($path),
    ]);
})->middleware(['web', 'auth'])->name('ckeditor.upload');

require __DIR__.'/settings.php';
