<?php

use App\Http\Controllers\BreakTimeController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\StatsController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ScheduleController;
use App\Models\BreakTime;
use App\Models\Schedule;
use Illuminate\Routing\Router;

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
    Route::get('/breaktime/live', [BreakTimeController::class, 'live'])->name('breaktime.live');
    Route::apiResource('breaktime', BreakTimeController::class)->except(['bage']);
    Route::get('/team/{team}/schedules', [StatsController::class, 'getSchedulesByTeamId'])->name('stats.team.date');
});

Route::post('/attendance/bage', [ScheduleController::class, 'bage']);
Route::post('/breaktime/bage', [BreakTimeController::class, 'bage']);
Route::get('/planning/{employee:mtle}', [ScheduleController::class, 'planning']);

Route::get('/dev/meta', function () {
    $schedule = Schedule::where('metadata', '=', request()->ip())
        ->where('day', date('Y-m-d'))->first();
    if (!$schedule) {
        return response()->json([
            'status' => 'Pass',
            'message' => 'This Machine has not yet been badged today',
        ]);
    }

    $start = new DateTime($schedule->day . ' ' . $schedule->started_time);
    $end = new DateTime(now());

    // Calculate total diff minutes
    $diff = $start->diff($end);
    $totalMinutes = $diff->h * 60 + $diff->i;

    //Unlock Badge if 30 minutes exceeded
    if ($totalMinutes >= 60) {
        return response()->json([
            'status' => 'Pass',
            'message' => 'This Machine is now able to badge'
        ]);
    } else {
        return response()->json([
            'status' => 'Fail',
            'message' => 'This Machine has just badged',
            'exceeded' => $totalMinutes
        ]);
    }
});
