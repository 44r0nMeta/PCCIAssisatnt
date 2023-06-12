<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Enums\EmployeeStatus;
use App\Http\Resources\EmployeeResource;
use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return EmployeeResource::collection(Employee::orderBy('created_at', 'desc')->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEmployeeRequest $request)
    {
        $data = $request->validated();
        $data['team_id'] = $data['team'];

        $employee = Employee::create($data);

        return $employee;

        // return new EmployeeResource($employee);
    }

    /**
     * Display the specified resource.
     */
    public function show(Employee $employee)
    {
        return new EmployeeResource($employee);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEmployeeRequest $request, Employee $employee)
    {
        $data = $request->validated();
        $data['team_id'] = $data['team'];

        $employee->update($data);

        return new EmployeeResource($employee);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Employee $employee)
    {
        $employee->update([
            'status' => EmployeeStatus::INACTIF
        ]);
        $employee->schedules()->delete();
        $employee->delete();

        return response(status: 204);
    }
}
