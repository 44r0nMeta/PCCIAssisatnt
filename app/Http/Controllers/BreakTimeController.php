<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\BreakTime;
use App\Http\Resources\BreakTimeResource;
use App\Http\Requests\EmployeeBageRequest;
use App\Http\Requests\StoreBreakTimeRequest;
use App\Http\Requests\UpdateBreakTimeRequest;
use App\Models\User;

class BreakTimeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return BreakTimeResource::collection(
            BreakTime::orderBy('day', 'desc')->orderBy('created_at', 'desc')->get()
        );
    }

    /**
     * Display a listing of the resource.
     */
    public function live()
    {
        return BreakTimeResource::collection(
            BreakTime::whereDate('day', date('Y-m-d'))
                ->orderBy('day', 'desc')->orderBy('created_at', 'desc')->get()
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBreakTimeRequest $request)
    {
        $breakTime = BreakTime::create($request->validated());

        return new BreakTimeResource($breakTime);
    }

    /**
     * Display the specified resource.
     */
    public function show(BreakTime $breaktime)
    {
        return new BreakTimeResource($breaktime);
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

        $currentBreakTime = $employee->breaktimes->where('day', date('Y-m-d'))->whereNull('ended_time')->first();
        if ($request->type === 'start') {
            if ($currentBreakTime != null) {
                return response()->json([
                    "errors" => [
                        "pause" => [
                            0 => "Vous avez une mise en pause non cloturé"
                        ]
                    ],
                ], status: 422);
            }

            $breakTime = BreakTime::create([
                'type' => 'break',
                'employee_id' => $employee->id,
                'day' => date('Y-m-d'),
                'expected_start_time' => date('H:i:s'),
                'expected_end_time' => date('H:i:s'),
                'started_time' => date('H:i:s'),
                'metadata' => request()->ip(),
            ]);

            return new BreakTimeResource($breakTime);
        } else { //stop
            if ($currentBreakTime == null) {
                return response()->json([
                    "errors" => [
                        "pause" => [
                            0 => "Vous n'avez entammé aucune pause"
                        ]
                    ],
                ], status: 422);
            }

            $currentBreakTime->update([
                'expected_end_time' => date('H:i:s'),
                'ended_time' => date('H:i:s'),
                'status' => 'P'
            ]);

            return new BreakTimeResource($currentBreakTime);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBreakTimeRequest $request, BreakTime $breaktime)
    {
        $breaktime->update($request->validated());

        return new BreakTimeResource($breaktime);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BreakTime $breaktime)
    {
        $breaktime->delete();

        return response(status: 204);
    }
}
