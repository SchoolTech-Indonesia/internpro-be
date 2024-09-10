<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Models\Permission as SpatiePermission;

class Permission extends SpatiePermission
{
    use HasFactory;
    use HasUuids;
    use SoftDeletes;
    protected $primaryKey = 'id';

    protected $table = 'permissions';
    protected $fillable = [
        'name',
        'guard_name'
    ];
}
