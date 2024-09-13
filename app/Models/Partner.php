<?php

namespace App\Models;

use App\Traits\CreatedBy;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Partner extends Model
{
    use HasFactory, HasUuids, SoftDeletes, CreatedBy;

    protected $primaryKey = 'uuid';

    protected $guarded = [];
    protected $fillable = [
        'name',
        'address',
        'logo',
        'file_sk',
        'number_sk',
        'end_date_sk',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'end_date_sk' => 'datetime',
    ];
}
