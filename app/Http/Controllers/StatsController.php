<?php

namespace App\Http\Controllers;

use DateTime;
use App\Models\Team;
use App\Models\Schedule;
use Illuminate\Http\Request;
use App\Http\Resources\ScheduleResource;

class StatsController extends Controller
{
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
