<?php

namespace App\Http\Controllers;

use DateTime;
use App\Models\Employee;
use App\Models\Schedule;
use Illuminate\Support\Facades\Request;
use App\Http\Resources\EmployeeResource;
use App\Http\Resources\ScheduleResource;
use App\Http\Requests\EmployeeBageRequest;
use App\Http\Requests\StoreScheduleRequest;
use App\Http\Requests\UpdateScheduleRequest;

class ScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $paginate = Request::input('paginate') ?? 10;

        return ScheduleResource::collection(Schedule::orderBy('day', 'desc')->orderBy('created_at', 'desc')->get());
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

        if ($employee === null) {
            return response()->json([
                "errors" => [
                    "mtle" => [
                        0 => "Le champ ID Agent sélectionné est invalide."
                    ]
                ],
            ], status: 422);
        }
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

        $status = (new DateTime($schedule->expected_start_time))->format('H:i') < date("H:i") ? 'R' : 'P';
        // return response()->json(['status' => $status]);
        if ($request->type === 'in') {
            if (!$this->hostRulePass()) {
                return response()->json([
                    "errors" => [
                        "host" => [
                            0 => "Impossible de pointer une entrée actuellement."
                        ]
                    ],
                ], status: 422);
            }

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
                "metadata" => request()->ip(),
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
     * Display the specified resource.
     */

    public function planning(Employee $employee)
    {
        return ScheduleResource::collection($employee->schedules->sortByDesc('day')->take(7));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateScheduleRequest $request, Schedule $schedule)
    {
        $schedule->update($request->validated());

        return new ScheduleResource($schedule);
    }

    public function hostRulePass()
    {
        $schedule = Schedule::where('metadata', '=', request()->ip())
            ->where('day', date('Y-m-d'))->first();
        if (!$schedule) {
            return true;
        }

        $start = new DateTime($schedule->day . ' ' . $schedule->started_time);
        $end = new DateTime(now());

        // Calculate total diff minutes
        $diff = $start->diff($end);
        $totalMinutes = $diff->h * 60 + $diff->i;

        //Unlock Badge if 30 minutes exceeded
        if ($totalMinutes >= 60) {
            return true;
        } else {
            return false;
        }
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
