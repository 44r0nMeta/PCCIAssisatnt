<?php

namespace App\Http\Resources;

use App\Models\User;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TeamResource extends JsonResource
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
            'label' => $this->label,
            'description' => $this->description,
            'created_by' => User::find($this->created_by)->name,
            'created_at' => (new DateTime($this->created_at))->format('Y-m-d H:m'),
        ];
    }
}
