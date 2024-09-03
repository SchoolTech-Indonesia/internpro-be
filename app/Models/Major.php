<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Major extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $primaryKey = 'uuid';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $guarded = [];

    public function classes()
    {
        return $this->hasMany(Kelas::class, 'major', 'uuid');
    }

    // Relasi ke model `User`
    public function users()
    {
        return $this->hasMany(User::class, 'major_id', 'uuid');
    }
}
