<?php

use App\Http\Controllers\admin\AuthController;
use App\Http\Controllers\admin\VolunteerController;
use App\Http\Middleware\AuthMiddleware;
use Illuminate\Support\Facades\Route;

// dash
Route::middleware([AuthMiddleware::class])->group(function () {
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');
    // volunteers routes
    Route::resource('volunteer', VolunteerController::class);
});

Route::controller(AuthController::class)->group(function () {

    // Login
    Route::get('/', 'login')->name('login');
    Route::post('/match-login', 'match_login')->name('match-login');
    // Signup
    Route::get('/signup', 'signup')->name('signup');
    Route::post('/insert-signup', 'insert_signup')->name('insert-signup');
    // Logout
    Route::post('/logout', 'logout')->name('logout');
});
