<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\JournalController;

// Auth email check on login/sign-up
Route::post('/auth', [AuthController::class, 'auth']);
Route::delete('/auth', [AuthController::class, 'delete']);

// journals crud
Route::middleware('auth:sanctum')->group(function () {

    Route::get('/journals', [JournalController::class, 'index']);       // all
    Route::get('/journal/{id}', [JournalController::class, 'show']);    // one
    Route::post('/journal', [JournalController::class, 'store']);       // create
    Route::post('/journal/{id}', [JournalController::class, 'update']); // update
    Route::delete('/journal/{id}', [JournalController::class, 'destroy']); // delete

});
