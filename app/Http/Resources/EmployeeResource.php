<?php

namespace App\Http\Resources;

use DateTime;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeResource extends JsonResource
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
            'mtle' => $this->mtle,
            'firstname' => $this->firstname,
            'lastname' => $this->lastname,
            'bithday' => $this->bithday,
            'gender' => $this->gender,
            'phone' => $this->phone,
            'email' => $this->email,
            'address' => $this->address,
            'contract_type' => $this->contract_type,
            'team' => $this->team_id,
            'team_name' => Team::find($this->team_id)->label,
            'created_by' => User::find($this->created_by)->name,
            'created_at' => (new DateTime($this->created_at))->format('Y-m-d H:m'),
        ];
    }
}
