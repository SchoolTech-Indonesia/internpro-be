<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MentorPartner extends Model
{
    use HasFactory, HasUuids;

    protected $primaryKey = 'uuid';

    protected $fillable = ['user_id', 'partner_id'];

    public function partners()
    {
        return $this->belongsTo(Partner::class, 'partner_id', 'uuid');
    }

    public function users()
    {
        return $this->belongsTo(User::class, 'user_id', 'uuid');
    }
}
