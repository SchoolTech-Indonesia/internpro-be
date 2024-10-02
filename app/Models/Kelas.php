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

    // Event Eloquent untuk generate class_code secara otomatis
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($kelas) {
            $major = Major::find($kelas->major); 
            if ($major) {
                $majorCode = $major->major_code;
                $kelas->class_code = "{$majorCode}-" . str_pad(($major->classes()->count() + 1), 2, '0', STR_PAD_LEFT);
            }
        });
    }
}
