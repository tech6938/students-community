<?php

namespace App\Http\Controllers;

use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\api\CommunityController;
use App\Http\Controllers\api\FirendController;
use App\Http\Controllers\api\HiveBoardController;
use App\Http\Controllers\api\LocalController;
use App\Http\Controllers\BuzzController;
use App\Http\Controllers\JournalController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\StoryController;
use Illuminate\Support\Facades\Route;

// Auth email check on login/sign-up

Route::controller(AuthController::class)->group(function () {
    Route::post('/send-otp', 'sendOtp');
    Route::post('/verify-otp', 'verifyOtp');
    Route::post('/logout', 'logout')->middleware('auth:sanctum');

    // Route::post('/auth', 'auth');
});

// Protected routes
Route::middleware('auth:sanctum')->group(function () {

    Route::apiResource('hive-boards', HiveBoardController::class);
    Route::apiResource('communities', CommunityController::class);
    Route::post('communities/{id}',[ CommunityController::class, 'update']);

    /*
    |--------------------------------------------------------------------------
    | Friends
    |--------------------------------------------------------------------------
    */
    Route::get('locals', [LocalController::class, 'getLocal']);
    Route::controller(FirendController::class)->group(function () {
        Route::post('addFriend', 'addFriend');
        Route::get('incoming-requests', 'incomingRequests');
        Route::get('friend-list', 'friendList');
        Route::post('accept-friend', 'acceptFriend');
    });

    /*
    |--------------------------------------------------------------------------
    | Journals
    |--------------------------------------------------------------------------
    */
    Route::prefix('journal')->controller(JournalController::class)->group(function () {
        Route::get('/all', 'index');
        Route::get('/show/{id}', 'show');
        Route::get('/show/cat/{id}', 'getByCate');
        Route::post('/create', 'store');
        Route::post('/update/{id}', 'updateJournal');   // keeping POST as you had
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
        Route::get('/{id}',  'getProfileByVisibility');
    });


    // Buzz crud
    Route::prefix('buzz')->controller(BuzzController::class)->group(function () {

        Route::get('/all', 'index');              // GET  /api/buzz/all
        Route::get('/show/{id}', 'show');         // GET  /api/buzz/show/1
        Route::post('/create', 'store');          // POST /api/buzz/create
        Route::post('/update/{id}', 'update');    // POST /api/buzz/update/1
        Route::delete('/delete/{id}', 'destroy'); // DELETE /api/buzz/delete/1


        // BUZZ RATING

        Route::get('/ratings',  'ratingsIndex');
        Route::get('/ratings/{id}',  'ratingsShow');
        Route::post('/ratings/create',  'ratingsStore');
        Route::post('/ratings/update/{id}',  'ratingsUpdate');
        Route::delete('/ratings/delete/{id}', 'ratingsDestroy');
    });

    Route::prefix('community-to-journal')->controller(JournalCommunitieController::class)->group(function () {
        Route::get('/', 'index');
        Route::post('/create', 'store');
        Route::delete('/destroy/{type}/{id}', 'destroy');
    });

    Route::prefix('report')->controller(ReportController::class)->group(function () {
        Route::post('/', 'store');
    });
});
