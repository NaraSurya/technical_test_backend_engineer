<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CandidateController;
use App\Http\Controllers\VacancyController;
use App\Http\Controllers\CandidateApplyController;

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

Route::get('/greeting', function () {

    return response()->json(collect(["test"=>[1,2,3]]), 200);
});
Route::get('/candidate', [CandidateController::class, 'index']);
Route::get('/vacancy', [VacancyController::class, 'index']);
Route::get('/candidate-apply', [CandidateApplyController::class, 'index']);
Route::post('/candidate-apply/apply', [CandidateApplyController::class, 'store']);
Route::post('/candidate/create', [CandidateController::class, 'store']);
Route::post('/vacancy/create', [VacancyController::class, 'store']);
Route::put('/candidate/update', [CandidateController::class, 'update']);
Route::put('/vacancy/update', [VacancyController::class, 'update']);
Route::delete('/candidate/delete', [CandidateController::class, 'destroy']);
Route::delete('/vacancy/delete', [VacancyController::class, 'destroy']);
Route::delete('/candidate-apply/delete', [CandidateApplyController::class, 'destroy']);
Route::get('/candidate/filter', [CandidateController::class, 'filter']);
Route::get('/vacancy/filter', [vacancyController::class, 'filter']);