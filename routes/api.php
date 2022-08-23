<?php

use App\Http\Controllers\API\VoteController;
use App\Http\Controllers\API\NextepController;
use App\Http\Controllers\API\ProfileController;

use App\Models\User;
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
Route::post('/mytoken', [NextepController::class,'mytoken']);
Route::post("/nxp_register", [NextepController::class, "store"]);
Route::middleware('auth:api')->get("/2fa", [NextepController::class, "tfa"]);
Route::post("/2fa", [NextepController::class, "tfa_check"]);


Route::middleware('auth:api')->get('/role', [ProfileController::class, 'role']);
Route::middleware('auth:api')->get('/2faEnabled', [NextepController::class,'is2fa']);


Route::middleware('auth:api')->get('/profile', [ProfileController::class, 'profile']);
Route::middleware('auth:api')->patch('/profile',[ProfileController::class, 'update']);
Route::middleware('auth:api')->post('/profile/photo',[ProfileController::class,'uploadPhoto']);
Route::middleware('auth:api')->delete('/profile/wallet/{id}',[ProfileController::class,'deleteWallet']);
Route::middleware('auth:api')->patch('/profile/password',[ProfileController::class,'changePassword']);

Route::middleware('auth:api')->get('/voting_topics', [VoteController::class, 'votingTopics']);
Route::middleware('auth:api')->post('/vote/{id}', [VoteController::class, 'vote']);
Route::middleware('auth:api')->post('/vote', [VoteController::class, 'store']);
Route::middleware('auth:api')->post('/topic/{id}', [VoteController::class, 'disableTopic']);


