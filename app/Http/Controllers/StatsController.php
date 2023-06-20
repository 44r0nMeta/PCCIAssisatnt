<?php

namespace App\Http\Controllers;

use DateTime;
use App\Models\Team;
use App\Models\Schedule;
use Illuminate\Http\Request;
use App\Http\Resources\ScheduleResource;
use App\Models\BreakTime;

class StatsController extends Controller
{
    public function dashboardStats()
    {
        $day = date('Y-m-d');
        $totalSchedule = Schedule::whereDate('day', $day)->get();
        $totalBreakTime = BreakTime::whereDate('day', $day)->get();
        // dd($totalSchedule);
        $awaitToday = $totalSchedule->whereNotIn('status', ['OFF'])->count();
        $presentToday = $totalSchedule->where('status', 'P')->count();
        $lateToday = $totalSchedule->where('status', 'R')->count();
        $waitingNow = $totalSchedule->whereNull('status')->count();
        $offToday = $totalSchedule->where('status', 'OFF')->count();
        $currentlyInBreaktime = $totalBreakTime->whereNull('status')->count();

        // dd($offToday->count());
        return response()->json([
            'awaitToday' => $awaitToday,
            'presentToday' => $presentToday,
            'lateToday' => $lateToday,
            'waitingNow' => $waitingNow,
            'offToday' => $offToday,
            'totalBreakTime' => $totalBreakTime->count(),
            'currentlyInBreaktime' => $currentlyInBreaktime
        ]);
    }

    public function getSchedulesByTeamId(Team $team, Request $request)
    {
        // return Schedule::whereDate('day', '=', $request->input('day'))->get();

        $schedules = $team->schedules;
        if ($request->day)
            $schedules = $schedules->where('day', '=', (new DateTime($request->day))->format('Y-m-d'));

        if ($request->starth)
            $schedules = $schedules->where('expected_start_time', '>=', (new DateTime($request->starth))->format('H:i:s'));

        if ($request->endh)
            $schedules = $schedules->where('expected_start_time', '<=', (new DateTime($request->endh))->format('H:i:s'));
        // return $schedules->count();
        return ScheduleResource::collection($schedules);
    }
}
