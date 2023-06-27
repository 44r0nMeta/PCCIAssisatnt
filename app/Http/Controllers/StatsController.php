<?php

namespace App\Http\Controllers;

use DateTime;
use App\Models\Team;
use App\Models\Employee;
use App\Models\Schedule;
use App\Models\BreakTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\ScheduleResource;

class StatsController extends Controller
{
    public function dashboardStats()
    {
        $day = date('Y-m-d');
        $totalSchedule = Schedule::whereDate('day', $day)->get();
        $totalBreakTime = BreakTime::whereDate('day', $day)->get();
        $totalEmployee = Employee::all();
        // dd($totalSchedule);
        $awaitToday = $totalSchedule->whereNotIn('status', ['OFF'])->count();
        $presentToday = $totalSchedule->where('status', 'P')->count();
        $lateToday = $totalSchedule->where('status', 'R')->count();
        $waitingNow = $totalSchedule->whereNull('status')->count();
        $offToday = $totalSchedule->where('status', 'OFF')->count();
        $currentlyInBreaktime = $totalBreakTime->whereNull('status')->count();
        $onHoliday = $totalEmployee->where('status', 'C')->count();
        // dd($offToday->count());
        return response()->json([
            'awaitToday' => $awaitToday,
            'presentToday' => $presentToday,
            'lateToday' => $lateToday,
            'waitingNow' => $waitingNow,
            'offToday' => $offToday,
            'totalBreakTime' => $totalBreakTime->count(),
            'currentlyInBreaktime' => $currentlyInBreaktime,
            'totalEmployee' => $totalEmployee->count(),
            'onHolyday' => $onHoliday
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

    public function productionReporting()
    {
        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=production_export_" . date('Y-m-d') . ".csv",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0",
        );

        $schedules = Schedule::query()->when(request('start_date'), function ($query, $start_date) {
            return $query->whereBetween('day', [$start_date, request('end_date') ?? $start_date]);
        })->orderBy('day', 'DESC')->orderBy('created_at', 'DESC')->get();


        $schedules = ScheduleResource::collection($schedules);
        $callback = function () use ($schedules) {
            $columns = array(
                'Date',
                'ID Agent',
                'Noms',
                'Prenoms',
                'Heure debut prevu',
                'Heure fin prevu',
                'Heure commence',
                'Heure termine',
                'Decision',
                'Transport',
                'Memo'
            );
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns, separator: ";");

            foreach ($schedules as $schedule) {
                $row['Date']  = $schedule->day;
                $row['ID Agent'] = $schedule->employee->mtle;
                $row['Noms'] = $schedule->employee->lastname;
                $row['Prenoms'] = $schedule->employee->firstname;
                $row['Heure debut prevu'] = $schedule->expected_start_time;
                $row['Heure fin prevu'] = $schedule->expected_end_time;
                $row['Heure commence'] = $schedule->started_time;
                $row['Heure termine'] = $schedule->ended_time;
                $row['Decision'] = $schedule->status;
                $row['Transport'] = $schedule->hasTransport() ? 'Oui' : 'Non';
                $row['Memo'] = $schedule->memo;
                // $row['Description']    = $task->description;
                // $row['Start Date']  = $task->start_at;
                // $row['Due Date']  = $task->end_at;

                fputcsv($file, array(
                    $row['Date'],
                    $row['ID Agent'],
                    $row['Noms'],
                    $row['Prenoms'],
                    $row['Heure debut prevu'],
                    $row['Heure fin prevu'],
                    $row['Heure commence'],
                    $row['Heure termine'],
                    $row['Decision'],
                    $row['Transport'],
                    $row['Memo'],
                ), separator: ";");
            }

            fclose($file);
        };
        if (request('export') && filter_var(request('export'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE)  === true)
            return response()->stream($callback, headers: $headers);
        else
            return $schedules;
    }

    public function productionReportingCumul()
    {
        $start_date = request('start_date');
        $end_date = request('end_date');

        $schedules =  DB::table("schedules")
            ->select(
                'employee_id',
                DB::raw(
                    "SUM(TIMESTAMPDIFF(MINUTE,started_time, ended_time)) AS nbMinutes, 
                    COALESCE(sum(ended_time >= '20:00:00'), 0) AS transportAmount"
                )
            )
            ->whereBetween('day', [$start_date, $end_date ?? $start_date])
            ->where('type', 'prod')
            ->groupBy(DB::raw("employee_id"))
            ->get();

        return response()->json([
            'meta' => request()->all(),
            'message' => 'Cumul des heures travailés à une periode',
            'data' => $schedules
        ]);
    }
}
