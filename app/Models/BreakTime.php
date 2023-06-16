<?php

namespace App\Models;

use App\Traits\UserTraceTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BreakTime extends Model
{
    use HasFactory, UserTraceTrait, SoftDeletes;

    protected $table = 'schedules';

    protected $primaryKey = 'id';

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
        'status',
        'metadata'
    ];

    protected static function booted(): void
    {

        // Global scope to apply filter directly on Eloquent Query Call
        static::addGlobalScope('breaktime', function (Builder $builder) {
            $builder->where('type', 'break');
        });
    }
}
