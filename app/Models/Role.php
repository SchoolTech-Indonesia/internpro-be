<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Role extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'roles';
    protected $fillable = [
        'name',
        'description',
        'created_at',
        'updated_at',
    ];

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'roles_permissions', 'id_role', 'id_permission');
    }
}