<?php

namespace App\Traits;

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
        }
    }
}
