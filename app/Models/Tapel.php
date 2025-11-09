<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tapel extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function kelas(){
      return $this->hasMany(Kelas::class);
    }

    public function ekskul(){
      return $this->hasMany(Ekskul::class);
    }

    public function studentKokurikuler(){
      return $this->hasMany(StudentKokurikuler::class, 'tapel_id');
    }

    // HELPER
    public function tanggal(){
      return ($this->tanggal) ? Carbon::parse($this->tanggal)->locale('id_ID')->isoFormat('dddd, D MMMM Y') : '-';
    }

    public function tahun_pelajaran(){
      return $this->tahun_pelajaran .  ' - ' . ($this->semester == '1' ? 'Ganjil' : 'Genap');
    }

    public function semester(){
      return $this->semester == '1' ? 'Ganjil' : 'Genap';
    }
  }
