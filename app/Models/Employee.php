<?php

namespace App\Models;

use App\Traits\UserTraceTrait;
use DateTime;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Employee extends Model
{
    use HasFactory, UserTraceTrait, SoftDeletes;

    protected $fillable = [
        'mtle',
        'firstname',
        'lastname',
        'bithday',
        'gender',
        'phone',
        'email',
        'address',
        'contract_type',
        'team_id'
    ];

    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class);
    }
}
