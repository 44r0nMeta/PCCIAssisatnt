<?php

namespace App\Models;

use App\Traits\UserTraceTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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
}
