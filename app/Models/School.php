<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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
    use HasApiTokens, HasFactory, Notifiable;
    protected $table = 'school';

    protected $fillable = [
        'uuid',
        'school_name',
        'school_address',
        'phone_number',
        'start_member',
        'end_member',
    ];

    protected $hidden = [
        'id',
    ];
}
