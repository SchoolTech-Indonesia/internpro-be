<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * @model School
 *
 * @property string $uuid
 * @property string $school_name
 * @property string $school_address
 * @property string $phone_number
 * @property \Illuminate\Support\Carbon $start_member
 * @property \Illuminate\Support\Carbon $end_member
 */
class School extends Model
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;
    protected $table = 'school';
    protected $fillable = [
        'uuid',
        'school_name',
        'school_address',
        'phone_number',
        'start_member',
        'end_member',
    ];

    protected $dates = ['deleted_at'];

    protected static function booted()
    {
        static::addGlobalScope('notDeleted', function (Builder $builder) {
            $builder->whereNull('deleted_at');
        });
    }
}
