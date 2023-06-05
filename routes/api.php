<?php

use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\TeamController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth.basic')->group(function () {
    Route::apiResource('team', TeamController::class);
    Route::apiResource('employee', EmployeeController::class);
    Route::apiResource('schedule', ScheduleController::class)->except(['bage', 'planning']);
});

Route::post('/attendance/bage', [ScheduleController::class, 'bage']);
Route::get('/planning/{employee:mtle}', [ScheduleController::class, 'planning']);
