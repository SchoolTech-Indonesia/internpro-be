<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Traits\CreatedBy;

class Internship extends Model
{
    use HasFactory, SoftDeletes, CreatedBy;

    public $incrementing = false;

    protected $primaryKey = 'uuid';
    
    protected $keyType = 'string';

    protected $fillable = [
        'uuid',
        'code',
        'name',
        'description',
        'start_date',
        'end_date',
        'school_id',
        'major_ids',
        'class_ids',
        'coordinator_ids',
    ];

    protected $dates = [
        'start_date',
        'end_date',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function($internship) {
            $internship->uuid = (string) Str::uuid();
            $internship->code = strtoupper(Str::random(6));
        });
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function majors()
    {
        return $this->belongsToMany(Major::class, 'internship_has_major', 'internship_id', 'major_id');
    }

    public function classes()
    {
        return $this->belongsToMany(Kelas::class, 'internship_has_class', 'internship_id', 'class_id');
    }

    public function coordinators()
    {
        return $this->belongsToMany(User::class, 'internship_has_coordinator', 'internship_id', 'coordinator_id');
    }
}
