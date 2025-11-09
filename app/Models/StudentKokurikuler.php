<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentKokurikuler extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $table = 'student_kokurikuler';

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

    public function subdimension()
    {
        return $this->belongsTo(KokurikulerSubdimension::class, 'subdimension_id');
    }
}

