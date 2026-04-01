<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HiveBoardController;
use App\Http\Controllers\Api\CommunityController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/



Route::apiResource('hive-boards', HiveBoardController::class);
Route::apiResource('communities', CommunityController::class);