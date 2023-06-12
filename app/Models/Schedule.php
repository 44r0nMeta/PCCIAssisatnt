<?php

namespace App\Models;

use App\Traits\UserTraceTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Schedule extends Model
{
    use HasFactory, SoftDeletes, UserTraceTrait;

    protected $fillable = [
        'type',
        'day',
        'employee_id',
        'expected_start_time',
        'expected_end_time',
        'started_time',
        'ended_time',
        'breakdown_total_min',
        'memo',
        'status'
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
