<?php

use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\JournalController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StoryController;
use Illuminate\Support\Facades\Route;

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

        Route::get('/show', 'show');      // get my profile
        Route::post('/create', 'store');  // create profile
        Route::post('/update', 'update'); // update my profile
        Route::delete('/delete', 'destroy'); // delete my profile
    });
});
