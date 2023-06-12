<?php

namespace App\Models;

use App\Traits\UserTraceTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

class Team extends Model
{
    use HasFactory, UserTraceTrait, SoftDeletes;

    protected $fillable = [
        'label',
        'description',
    ];

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }

    public function schedules(): HasManyThrough
    {
        return $this->hasManyThrough(Schedule::class, Employee::class);
    }
}
