<?php

use App\Http\Controllers\{ ProfileController, VotingController, };
use App\Http\Controllers\Admin\{ AdminDashboardController, CandidateController, CategoryController, VoteController, };
use App\Http\Controllers\Auth\{ AuthenticatedSessionController, };
use Illuminate\Support\Facades\Route;

    // Route::get('/', function () {
    //     return view('welcome');
    // });
    
    Route::get('/', [AuthenticatedSessionController::class, 'create']);

// Default Breeze routes
// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::controller(VotingController::class)->group(function () {
        Route::get('/voting', 'index')->name('voting.index');
        Route::get('/voting/{category}', 'show')->name('voting.show');
        Route::post('/voting/{category}/vote', 'store')->name('voting.store')->middleware('throttle:' . config('voting.rate_limit_attempts') . ',' . config('voting.rate_limit_decay'));
        Route::get('/voting/{category}/results', 'results')->name('voting.results');
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    
    Route::resource('categories', CategoryController::class);
    Route::resource('candidates', CandidateController::class);
    
    Route::get('/votes', [VoteController::class, 'index'])->name('votes.index');
});

    // route for real time voting results in JSON formatt
    Route::get('/voting/{category}/results/json', [App\Http\Controllers\VotingController::class, 'realTimeVoteUpdate'])->name('voting.results.json');

require __DIR__.'/auth.php';
