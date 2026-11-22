<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Route::view('/', 'welcome')->name('home');

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
