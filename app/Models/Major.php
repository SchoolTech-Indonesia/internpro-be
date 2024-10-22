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

    // Relasi ke model `Kelas`
    public function classes()
    {
        return $this->hasMany(Kelas::class, 'major', 'uuid');
    }

    // Relasi ke model `User`
    public function users()
    {
        return $this->hasMany(User::class, 'major_id', 'uuid');
    }

    // Event Eloquent untuk generate kode major secara otomatis
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->major_code)) {
                $lastMajor = Major::withTrashed()->orderBy('major_code', 'desc')->first();
                if ($lastMajor) {
                    $lastCode = $lastMajor->major_code;
                    $number = (int) substr($lastCode, 3);
                    $newNumber = $number + 1;
                    $model->major_code = 'MJ-' . str_pad($newNumber, 3, '0', STR_PAD_LEFT); // Generate kode baru
                } else {
                    $model->major_code = 'MJ-001';
                }
            }
        });
    }
}
