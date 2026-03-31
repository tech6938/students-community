<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\JournalController;
use App\Http\Controllers\StoryController;

// Auth email check on login/sign-up
Route::post('/auth', [AuthController::class, 'auth']);
Route::delete('/auth', [AuthController::class, 'delete']);

// journals crud
Route::middleware('auth:sanctum')->prefix('journal')->controller(JournalController::class)->group(function () {

        Route::get('/all', 'index');              // GET /api/journal/all
        Route::get('/show/{id}', 'show');             // GET /api/journal/1
        Route::post('/create', 'store');         // POST /api/journal/create
        Route::post('/update/{id}', 'update');   // POST /api/journal/update/1
        Route::delete('/delete/{id}', 'destroy'); // DELETE /api/journal/delete/1

    });

Route::middleware('auth:sanctum')->prefix('story')->controller(StoryController::class)->group(function () {

    Route::get('/all', 'index');
    Route::get('/show/{id}', 'show');
    Route::post('/create', 'store');
    Route::post('/update/{id}', 'update');
    Route::delete('/delete/{id}', 'destroy');

});
