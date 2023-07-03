<?php

namespace App\Http\Resources;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CumulReportingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // dd($this);
        return [
            // 'seconds' => $this->prodSeconds,
            // 'totalProdHour' => sprintf('%02d:%02d:%02d', ($this->prodSeconds / 3600), ($this->prodSeconds / 60 % 60), $this->prodSeconds % 60),
            'totalProdHour' => $this->prodSeconds,
            'totalBreakHour' => $this->breakSeconds,
            'transportAmount' => $this->nightWork * 500,
            'totalProd' => $this->totalProd,
            'totalOff' => $this->totalOff,
            'totalLate' => $this->totalLate,
            'totalAr' => $this->totalAr,
            'totalAbsent' => $this->totalAbsent,
            'totalPresent' => $this->totalPresent,
            'employee' => Employee::find($this->employee_id),
        ];
    }
}
