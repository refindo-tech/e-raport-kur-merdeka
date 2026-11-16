<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    /**
     * Standarisasi kapitalisasi agama saat mengambil dari database
     * Mengubah format apapun (islam, ISLAM, Islam) menjadi format standar (Islam)
     */
    public function getAgamaAttribute($value)
    {
        if ($value === null || $value === '') {
            return null;
        }
        // Standarisasi ke title case (huruf pertama kapital, sisanya lowercase)
        return ucfirst(strtolower(trim($value)));
    }

    /**
     * Standarisasi kapitalisasi agama saat menyimpan ke database
     * Mengubah format apapun (islam, ISLAM, Islam) menjadi format standar (Islam)
     */
    public function setAgamaAttribute($value)
    {
        if ($value !== null && $value !== '') {
            // Standarisasi ke title case (huruf pertama kapital, sisanya lowercase)
            $this->attributes['agama'] = ucfirst(strtolower(trim($value)));
        } else {
            $this->attributes['agama'] = null;
        }
    }

    protected static function booted()
    {
        static::saved(function (Siswa $siswa) {
            if ($siswa->wasRecentlyCreated || $siswa->wasChanged('kelas_id')) {
                RiwayatKelas::catat($siswa);
            }
        });
    }

    public function user(){
      return $this->belongsTo(User::class);
    }

    public function kelas(){
      return $this->belongsTo(Kelas::class);
    }

    public function anggotaEkskul(){
      return $this->hasMany(AnggotaEkskul::class);
    }

    public function ketidakhadiran(){
      return $this->hasOne(Ketidakhadiran::class);
    }

    public function catatanWalas(){
      return $this->hasOne(CatatanWalas::class);
    }

    public function pencapaianTp(){
      return $this->hasMany(PencapaianTp::class);
    }

    public function nilaiAkhir(){
      return $this->hasMany(NilaiAkhir::class);
    }

    public function anggotaKelompok(){
      return $this->hasOne(AnggotaKelompok::class);
    }

    public function nilaiProjek(){
      return $this->hasMany(NilaiProjek::class);
    }

    public function catatanProjek(){
      return $this->hasMany(CatatanProjek::class);
    }

    public function kokurikulerLevels(){
      return $this->hasMany(StudentKokurikulerLevel::class, 'student_id');
    }

    public function kokurikulerDescriptions(){
      return $this->hasMany(StudentKokurikulerDescription::class, 'student_id');
    }

    public function aktif(){
      return $this->whereHas('user', fn($q) => $q->where('is_aktif', 1));
    }
}
