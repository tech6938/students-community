<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\JournalController;
use App\Http\Controllers\StoryController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicPhotoController;

// Auth
Route::post('/auth', [AuthController::class, 'auth']);
Route::delete('/auth', [AuthController::class, 'delete']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Journals
    |--------------------------------------------------------------------------
    */
    Route::prefix('journal')->controller(JournalController::class)->group(function () {
        Route::get('/all', 'index');
        Route::get('/show/{id}', 'show');
        Route::post('/create', 'store');
        Route::post('/update/{id}', 'update');   // keeping POST as you had
        Route::delete('/delete/{id}', 'destroy');
    });

    /*
    |--------------------------------------------------------------------------
    | Stories
    |--------------------------------------------------------------------------
    */
    Route::prefix('story')->controller(StoryController::class)->group(function () {
        Route::get('/all', 'index');
        Route::get('/show/{id}', 'show');
        Route::post('/create', 'store');
        Route::post('/update/{id}', 'update');
        Route::delete('/delete/{id}', 'destroy');
    });

    /*
    |--------------------------------------------------------------------------
    | Profiles
    |--------------------------------------------------------------------------
    */
    Route::prefix('profile')->controller(ProfileController::class)->group(function () {
        Route::get('/all', 'index');
        Route::get('/show/{id}', 'show');
        Route::post('/create', 'store');
        Route::post('/update/{id}', 'update');
        Route::delete('/delete/{id}', 'destroy');
    });

    /*
    |--------------------------------------------------------------------------
    | Photos
    |--------------------------------------------------------------------------
    */
    Route::prefix('photos')->controller(PublicPhotoController::class)->group(function () {
        Route::get('/all/{profileId}', 'index');
        Route::post('/upload', 'store');
        Route::post('/update/{id}', 'update');
        Route::delete('/delete/{id}', 'destroy');
    });

});
