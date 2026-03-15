<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\FacialRegistrationController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Custom facial registration routes
Route::get('/register', [FacialRegistrationController::class, 'create'])
    ->middleware('guest')
    ->name('facial.register');
Route::post('/facial-register', [FacialRegistrationController::class, 'store'])
    ->middleware('guest');

// Default Breeze routes
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
