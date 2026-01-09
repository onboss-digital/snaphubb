<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\Voting\Http\Controllers\VotingController;

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

Route::middleware(['auth:sanctum'])->prefix('v1')->name('api.')->group(function () {
    Route::get('voting/top-3', [VotingController::class, 'getTop3']);
    Route::post('voting/vote', [VotingController::class, 'storeVote']);
    Route::get('voting/user-vote/{week_id}', [VotingController::class, 'getUserVote']);
    Route::get('voting/all-candidates', [VotingController::class, 'getAllCandidates']);
    Route::post('voting/suggest', [VotingController::class, 'storeSuggestion']);
});

