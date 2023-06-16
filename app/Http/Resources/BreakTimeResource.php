<?php

namespace App\Http\Resources;

use DateTime;
use App\Models\User;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BreakTimeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'day' => $this->day,
            'employee' => Employee::find($this->employee_id),
            'expected_start_time' => $this->expected_start_time,
            'expected_end_time' => $this->expected_end_time,
            'started_time' => $this->started_time,
            'ended_time' => $this->ended_time,
            'breakdown_total_min' => $this->breakdown_total_min,
            'memo' => $this->memo,
            'status' => $this->status,
            'created_at' => (new DateTime($this->created_at))->format('Y-m-d H:m'),
            'created_by' => User::find($this->created_by)->name,
            'updated_by' => User::find($this->updated_by)->name,
        ];
    }
}
