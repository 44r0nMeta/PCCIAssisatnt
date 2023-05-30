<?php

namespace App\Http\Controllers;

use App\Http\Requests\EmployeeBageRequest;
use App\Models\Schedule;
use App\Http\Requests\StoreScheduleRequest;
use App\Http\Requests\UpdateScheduleRequest;
use App\Http\Resources\EmployeeResource;
use App\Http\Resources\ScheduleResource;
use App\Models\Employee;
use Illuminate\Support\Facades\Request;

class ScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $paginate = Request::input('paginate') ?? 10;

        return ScheduleResource::collection(Schedule::orderBy('created_at', 'desc')->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreScheduleRequest $request)
    {
        $schedule = Schedule::create($request->validated());

        return new ScheduleResource($schedule);
    }

    /**
     * Display the specified resource.
     */
    public function show(Schedule $schedule)
    {
        return new ScheduleResource($schedule);
    }

    /**
     * Display the specified resource.
     */
    public function bage(EmployeeBageRequest $request)
    {
        $employee = Employee::where('mtle', $request->mtle)->first();

        // return date('Y-m-d');
        $schedule =  $employee->schedules->where('day', date('Y-m-d'))
            ->where('type', "prod")
            ->first();

        if ($schedule === null) {
            return response()->json([
                "errors" => [
                    "schedule" => [
                        0 => "Aucune production trouvé aujourd'hui pour vous"
                    ]
                ],
            ], status: 422);
        }

        $status = $schedule->exepected_start_time > date("H:m") ? 'R' : 'P';
        if ($request->type === 'in') {
            if ($schedule->started_time !== null) {
                return response()->json([
                    "errors" => [
                        "schedule" => [
                            0 => "Vous avez dejà pointé l'entrée d'aujourd'hui"
                        ]
                    ],
                ], status: 422);
            }

            $schedule->update([
                "started_time" => date("H:i"),
                "status" => $status
            ]);
        } else {

            if ($schedule->ended_time !== null) {
                return response()->json([
                    "errors" => [
                        "schedule" => [
                            0 => "Vous avez dejà pointé la sortie d'aujourd'hui"
                        ]
                    ],
                ], status: 422);
            }

            $schedule->update([
                "ended_time" => date("H:i")
            ]);
        }

        return new ScheduleResource($schedule);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateScheduleRequest $request, Schedule $schedule)
    {
        $schedule->update($request->validated());

        return new ScheduleResource($schedule);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Schedule $schedule)
    {
        $schedule->delete();

        return response(status: 204);
    }
}