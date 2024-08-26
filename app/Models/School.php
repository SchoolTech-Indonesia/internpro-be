<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * Class School
 *
 * @package App\Models
 *
 * @property string $uuid
 * @property string $school_name
 * @property string $school_address
 * @property string $phone_number
 * @property \DateTime $start_member
 * @property \DateTime $end_member
 */

class School extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * @var string $table The name of the table that this model interacts with.
     */
    protected $table = 'schools';

    /**
     * @var array $fillable The attributes that are mass assignable.
     */
    protected $fillable = [
        'uuid',
        'school_name',
        'school_address',
        'phone_number',
        'start_member',
        'end_member',
    ];
}
