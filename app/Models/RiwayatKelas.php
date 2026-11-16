<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiwayatKelas extends Model
{
    use HasFactory;

    protected $table = 'riwayat_kelas';
    protected $guarded = ['id'];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    public function tapel()
    {
        return $this->belongsTo(Tapel::class);
    }

    public static function catat(Siswa $siswa): void
    {
        if (!$siswa->kelas_id) {
            return;
        }

        $tapelId = Kelas::where('id', $siswa->kelas_id)->value('tapel_id');
        if (!$tapelId) {
            return;
        }

        static::updateOrCreate(
            ['siswa_id' => $siswa->id, 'tapel_id' => $tapelId],
            ['kelas_id' => $siswa->kelas_id]
        );
    }
}
