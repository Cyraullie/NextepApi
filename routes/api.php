<?php

use App\Http\Controllers\API\FiameController;
use App\Http\Controllers\API\NextepController;
use App\Http\Controllers\API\RunForCauseController;
use App\Models\User;
use App\Http\Controllers\API\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Nextep endpoints
Route::post('/mytoken',[NextepController::class,'mytoken']);

Route::middleware('auth:api')->get('/profile', [NextepController::class, 'profile']);
Route::middleware('auth:api')->patch('/profile',[NextepController::class, 'update']);

Route::middleware('auth:api')->post('/profile/photo',[NextepController::class,'uploadPhoto']);
Route::middleware('auth:api')->delete('/profile/wallet/{id}',[NextepController::class,'deleteWallet']);
Route::middleware('auth:api')->patch('/profile/password',[NextepController::class,'changePassword']);

Route::middleware('auth:api')->get('/voting_topics', [NextepController::class, 'votingTopics']);
Route::middleware('auth:api')->post('/vote/{id}', [NextepController::class, 'vote']);

