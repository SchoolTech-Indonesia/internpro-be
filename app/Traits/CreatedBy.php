<?php
/**
 *  Laravel-CreatedByBlueprint (http://github.com/malhal/Laravel-CreatedBy)
 *
 *  Created by Malcolm Hall on 27/8/2016.
 *  Copyright © 2016 Malcolm Hall. All rights reserved.
 */

namespace App\Traits;

use function Laravel\Prompts\error;

trait CreatedBy
{
    public static function bootCreatedBy(): void
    {
        // updating created_by and updated_by when model is created
        static::creating(function ($model) {
            if (!$model->isDirty('created_by')) {
                $model->created_by = auth()->user()->uuid;
            }
            if (!$model->isDirty('updated_by')) {
                $model->updated_by = auth()->user()->uuid;
            }
        });

        // updating updated_by when model is updated
        static::updating(function ($model) {
            if (!$model->isDirty('updated_by')) {
                $model->updated_by = auth()->user()->uuid;
            }
        });

        // updating deleted_by when model is deleted
        static::deleting(function ($model) {
            if (
                in_array(\Illuminate\Database\Eloquent\SoftDeletes::class, class_uses_recursive(static::class), true)
                && !$model->isDirty('deleted_by')
            ) {
                $model->deleted_by = auth()->user()?->uuid;
            }
        });
    }
}
