<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DealerController;
use App\Http\Controllers\UserController;
use App\Models\User;
use Illuminate\Support\Facades\Route;

// Email uniqueness check (accessible by all)
Route::get('/check-email', [AuthController::class, 'checkEmail'])->name('check-email');

/*
|--------------------------------------------------------------------------
| Guest Routes (unauthenticated)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/', function () {
        return redirect()->route('login');
    });

    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'dealer.first_login'])->group(function () {
    // Route::get('/dashboard', function () {
    //     return view('dashboard');
    // })->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('users', UserController::class);

    // Dealer routes
    Route::get('/dealers', [DealerController::class, 'index'])->name('dealers.index');
    Route::get('/dealers/{dealer}/edit-location', [DealerController::class, 'editLocation'])->name('dealers.edit-location');
    Route::put('/dealers/{dealer}/update-location', [DealerController::class, 'updateLocation'])->name('dealers.update-location');
});

// Dealer profile completion (auth required, but exempt from dealer middleware)
Route::middleware('auth')->group(function () {
    Route::get('/dealer/complete-profile', [AuthController::class, 'showCompleteProfile'])->name('dealer.complete-profile');
    Route::post('/dealer/complete-profile', [AuthController::class, 'completeProfile'])->name('dealer.complete-profile.store');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
