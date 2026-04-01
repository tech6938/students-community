<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\JournalController;
use App\Http\Controllers\Api\HiveBoardController;
use App\Http\Controllers\Api\CommunityController;
use App\Http\Controllers\Api\FirendController;
use App\Http\Controllers\api\LocalController;

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


// Protected routes
    Route::apiResource('hive-boards', HiveBoardController::class);
    Route::apiResource('communities', CommunityController::class);
    // Get locals
    Route::get('locals', [LocalController::class, 'getLocal']);
    // add friends
    Route::post('addFriend', [FirendController::class, 'addFriend']);
    //    check requests
    Route::get('incoming-requests', [FirendController::class, 'incomingRequests']);
    // friendList
    Route::get('friend-list', [FirendController::class, 'friendList']);
    // acceptFriend
    Route::post('accept-friend', [FirendController::class, 'acceptFriend']);
});
