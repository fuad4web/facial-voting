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

    // Add after your existing routes
Route::get('/facial-login', [App\Http\Controllers\Auth\FacialLoginController::class, 'showLoginForm'])
    ->middleware('guest')
    ->name('facial.login');
    
Route::post('/facial-login/verify', [App\Http\Controllers\Auth\FacialLoginController::class, 'login'])
    ->middleware('guest')
    ->name('facial.login.verify');

    // fortesting my facial recognition login
    Route::get('/test-face', function () {
        return view('test-face');
    })->middleware('auth');

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
