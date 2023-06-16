<?php

namespace App\Traits;

use App\Models\User;

trait UserTraceTrait
{
    public static function bootUserTraceTrait()
    {
        if (auth()->check()) {
            static::creating(function ($model) {
                $model->created_by = auth()->id();
                $model->updated_by = auth()->id();
            });

            static::updating(function ($model) {
                $model->updated_by = auth()->id();
            });

            static::deleting(function ($model) {
                $model->deleted_by = auth()->id();
            });
        } else {

            static::creating(function ($model) {
                $guest = User::where('name', 'guest')->first();
                $model->created_by = $guest->id;
                $model->updated_by = $guest->id;
            });

            static::updating(function ($model) {
                $guest = User::where('name', 'guest')->first();
                $model->updated_by = $guest->id;
            });
        }
    }
}
