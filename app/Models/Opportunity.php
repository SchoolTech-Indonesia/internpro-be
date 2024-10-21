<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Opportunity extends Model
{
    use HasFactory, SoftDeletes;
    protected $primaryKey = 'opportunity_id';
    protected $keyType = 'string';
    protected $fillable = ['opportunity_id', 'code', 'program_id', 'activity_id', 'name', 'description', 'quota', 'school_id', 'mentor_id', 'created_by', 'updated_by', 'deleted_by'];
}
