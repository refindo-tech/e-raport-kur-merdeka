<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentKokurikulerLevel extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'student_id');
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'class_id');
    }

    public function tapel()
    {
        return $this->belongsTo(Tapel::class, 'tapel_id');
    }

    public function dimension()
    {
        return $this->belongsTo(KokurikulerDimension::class, 'dimension_id');
    }
}

