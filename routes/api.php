<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\HiveBoardController;
use App\Http\Controllers\Api\CommunityController;
use App\Http\Controllers\Api\FirendController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\api\LocalController;

// Public routes
Route::post('/auth', [AuthController::class, 'auth']);
Route::delete('/auth', [AuthController::class, 'delete']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
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
