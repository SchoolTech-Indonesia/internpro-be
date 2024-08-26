<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

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
}
