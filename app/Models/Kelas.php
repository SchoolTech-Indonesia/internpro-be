<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Kelas extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'classes';
    protected $primaryKey = 'uuid';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $guarded = [];

    public function major()
    {
        return $this->belongsTo(Major::class, 'major', 'uuid');
    }

    // Relasi ke model `User`
    public function users()
    {
        return $this->hasMany(User::class, 'class_id', 'uuid');
    }
}
