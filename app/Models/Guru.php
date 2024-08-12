<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Guru extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'guru';
    protected $fillable = [
        'nip',
        'password',
        'nama',
        'email',
        'telepon',
        'schedule',
        'certification',
        'mata_pelajaran',
    ];
}
