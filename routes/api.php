<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\JournalController;
use App\Http\Controllers\StoryController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicPhotoController;
use App\Http\Controllers\BuzzController;

// Auth email check on login/sign-up

Route::controller(AuthController::class)->group(function () {

Route::post('/auth', 'auth');
Route::post('/logout', 'logout')->middleware('auth:sanctum');

});

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


Route::middleware('auth:sanctum')->prefix('profile')->controller(ProfileController::class)->group(function () {

        Route::get('/show', 'show');      // get my profile
        Route::post('/create', 'store');  // create profile
        Route::post('/update', 'update'); // update my profile
        Route::delete('/delete', 'destroy'); // delete my profile
    });


    // Buzz crud
Route::middleware('auth:sanctum')->prefix('buzz')->controller(BuzzController::class)->group(function () {

    Route::get('/all', 'index');              // GET  /api/buzz/all
    Route::get('/show/{id}', 'show');         // GET  /api/buzz/show/1
    Route::post('/create', 'store');          // POST /api/buzz/create
    Route::post('/update/{id}', 'update');    // POST /api/buzz/update/1
    Route::delete('/delete/{id}', 'destroy'); // DELETE /api/buzz/delete/1

});

// Route::middleware('auth:sanctum')->prefix('profile')->controller(ProfileController::class)->group(function () {

//         // Route::get('/all', 'index');
//         Route::get('/show', 'show');
//         Route::post('/create', 'store');
//         Route::post('/update/{id}', 'update');
//         Route::delete('/delete/{id}', 'destroy');
//     });


// Route::middleware('auth:sanctum')->prefix('photos')->controller(PublicPhotoController::class)->group(function () {

//         Route::get('/all/{profileId}', 'index');
//         Route::post('/upload', 'store');
//         Route::post('/update/{id}', 'update');
//         Route::delete('/delete/{id}', 'destroy');
//     });



