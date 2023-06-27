<?php

namespace App\Models;

use App\Traits\UserTraceTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Schedule extends Model
{
    use HasFactory, SoftDeletes, UserTraceTrait;

    protected static function booted(): void
    {

        // Global scope to apply filter directly on Eloquent Query Call
        static::addGlobalScope('schedule', function (Builder $builder) {
            $builder->whereNot('type', 'break');
        });
    }

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

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function hasTransport(): bool
    {
        if ($this->status == "AP" && $this->expected_end_time >= "12:00:00") {
            return true;
        } elseif ($this->ended_time >= "21:00:00") {
            return true;
        }

        return false;
    }
}
