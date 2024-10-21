<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;


class Activity extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'activity'; // Nama tabel
    protected $primaryKey = 'uuid'; // Primary key
    public $incrementing = false;
    protected $keyType = 'string';  // Tipe primary key UUID

    protected $fillable = [
        'uuid',
        'code',
        'program_id',
        'name',
        'school_id',
        'partner_id',
        'teacher_id',
        'description',
        'start_date',
        'end_date',
        'created_by',
        'updated_by',
    ];

    // Jika Anda ingin menambahkan relasi dengan model lain:
    public function program()
    {
        return DB::table('internships')->where('uuid', $this->program_id)->first();
    }

    public function school()
    {
        return $this->belongsTo(School::class, 'school_id', 'uuid');
    }

    public function partner()
    {
        return $this->belongsTo(Partner::class, 'partner_id', 'uuid');
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id', 'uuid');
    }

    public static function generateUniqueCode($length = 6)
    {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $code = '';

        do {
            // Generate a random code
            for ($i = 0; $i < $length; $i++) {
                $code .= $characters[rand(0, strlen($characters) - 1)];
            }

            // Check if the generated code already exists in the database
            $codeExists = Activity::where('code', $code)->exists();

        } while ($codeExists);

        return $code;
    }
}
