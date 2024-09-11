<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\QueryException;
use Spatie\Permission\Models\Role as SpatieRole;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends SpatieRole
{
    use HasFactory;
    use HasUuids;
    use SoftDeletes;
    protected $primaryKey = 'id';

    protected $table = 'roles';
    protected $fillable = [
        'name',
        'description',
        'guard_name'
    ];
    protected $dates = ['deleted_at'];
}