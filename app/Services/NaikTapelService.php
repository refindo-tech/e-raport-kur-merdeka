<?php

namespace App\Services;

use App\Models\CatatanWalas;
use App\Models\Ekskul;
use App\Models\Kelas;
use App\Models\Ketidakhadiran;
use App\Models\Pembelajaran;
use App\Models\RiwayatKelas;
use App\Models\Siswa;
use App\Models\Tapel;
use App\Models\Tingkat;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class NaikTapelService
{
    public function sumberAktif(): Tapel
    {
        $tapel = Tapel::where('is_aktif', true)->first() ?: Tapel::latest('id')->first();
        if (!$tapel) {
            throw new InvalidArgumentException('Tidak ada tahun pelajaran.');
        }

        return $tapel;
    }

    public function targetDari(Tapel $sumber, string $jenis): array
    {
        $bagian = explode('/', $sumber->tahun_pelajaran);
        if (count($bagian) !== 2) {
            throw new InvalidArgumentException('Format tahun pelajaran tidak dikenali.');
        }

        if ($jenis === 'semester') {
            if ((string) $sumber->semester !== '1') {
                throw new InvalidArgumentException('Semester genap hanya bisa dilanjutkan ke tahun pelajaran baru.');
            }

            return [
                'tahun_pelajaran' => $sumber->tahun_pelajaran,
                'semester' => '2',
                'naik_tingkat' => false,
            ];
        }

        if ($jenis !== 'tahun_baru') {
            throw new InvalidArgumentException('Jenis pergantian tidak dikenali.');
        }

        $tahunBerikut = (int) $bagian[1];

        return [
            'tahun_pelajaran' => $tahunBerikut . '/' . ($tahunBerikut + 1),
            'semester' => '1',
            'naik_tingkat' => true,
        ];
    }

    public function pratinjau(Tapel $sumber, string $jenis): array
    {
        $target = $this->targetDari($sumber, $jenis);
        $siswa = $this->siswaSumber($sumber);
        $opsi = $this->opsiKelas($sumber);

        $baris = $siswa->map(function (Siswa $item) use ($target, $sumber, $opsi) {
            $tingkat = (int) optional(optional($item->kelas)->tingkat)->angka;
            $keputusan = $this->keputusanWali($item, $target['naik_tingkat'], $tingkat, $sumber->id);
            $defaultNaik = $this->kelasDefault($opsi, $item->kelas, 'naik');
            $defaultTinggal = $this->kelasDefault($opsi, $item->kelas, 'tinggal');

            return [
                'siswa' => $item,
                'tingkat' => $tingkat,
                'aksi' => $keputusan,
                'kelas_asal' => $item->kelas ? $item->kelas->name . ' (tingkat ' . $tingkat . ')' : 'Belum punya kelas',
                'kelas_tujuan' => (!$item->kelas || $keputusan === 'lulus')
                    ? 'Tidak masuk tapel baru'
                    : $item->kelas->name . ' (tingkat ' . $tingkat . ')',
                'kelas_tujuan_id' => $keputusan === 'naik' ? $defaultNaik : ($keputusan === 'tinggal' ? $defaultTinggal : null),
                'default_naik' => $defaultNaik,
                'default_tinggal' => $defaultTinggal,
            ];
        });

        return compact('sumber', 'target', 'jenis') + [
            'baris' => $baris,
            'opsi_kelas' => $opsi,
        ];
    }

    public function jalankan(Tapel $sumber, string $jenis, array $aksiPerSiswa, array $kelasTujuanPerSiswa = []): array
    {
        $targetInfo = $this->targetDari($sumber, $jenis);

        return DB::transaction(function () use ($sumber, $targetInfo, $aksiPerSiswa, $kelasTujuanPerSiswa) {
            $target = Tapel::where('tahun_pelajaran', $targetInfo['tahun_pelajaran'])
                ->where('semester', $targetInfo['semester'])
                ->first();

            if ($target && Kelas::where('tapel_id', $target->id)->exists()) {
                throw new InvalidArgumentException('Tahun pelajaran tujuan sudah berisi kelas. Proses tidak dijalankan ulang.');
            }

            if (!$target) {
                $target = Tapel::create([
                    'tahun_pelajaran' => $targetInfo['tahun_pelajaran'],
                    'semester' => $targetInfo['semester'],
                    'tempat' => $sumber->tempat,
                    'is_aktif' => false,
                ]);
            }

            $siswaSumber = $this->siswaSumber($sumber);
            $keputusan = [];
            foreach ($siswaSumber as $siswa) {
                $tingkat = (int) optional(optional($siswa->kelas)->tingkat)->angka;
                $aksi = $aksiPerSiswa[$siswa->id] ?? $this->keputusanWali($siswa, $targetInfo['naik_tingkat'], $tingkat, $sumber->id);
                $this->pastikanAksiSah($aksi, $tingkat, $targetInfo['naik_tingkat'], (bool) $siswa->kelas_id);
                $keputusan[$siswa->id] = $aksi;
            }

            $kelasSumber = $this->opsiKelas($sumber)->keyBy('id');
            $pilihan = [];
            $kelasBaru = [];
            foreach ($siswaSumber as $siswa) {
                $aksi = $keputusan[$siswa->id];
                if ($aksi === 'lulus' || !$siswa->kelas) {
                    continue;
                }

                $dipilih = $targetInfo['naik_tingkat']
                    ? $this->kelasDipilih($kelasSumber, $kelasTujuanPerSiswa, $siswa)
                    : $siswa->kelas;
                $pilihan[$siswa->id] = $dipilih->id;
                if (!isset($kelasBaru[$dipilih->id])) {
                    $kelasBaru[$dipilih->id] = $this->salinKelas($dipilih, $dipilih->tingkat, $target);
                }
            }

            Ekskul::where('tapel_id', $sumber->id)->get()->each(function (Ekskul $ekskul) use ($target) {
                Ekskul::create([
                    'name' => $ekskul->name,
                    'tapel_id' => $target->id,
                    'guru_id' => $ekskul->guru_id,
                ]);
            });

            $disalin = 0;
            $tinggal = 0;
            $lulus = [];

            foreach ($siswaSumber as $siswa) {
                $aksi = $keputusan[$siswa->id];
                if ($aksi === 'lulus' || !$siswa->kelas) {
                    if ($aksi === 'lulus' && $siswa->kelas && $siswa->user) {
                        $siswa->user->update(['is_aktif' => false]);
                    }
                    $lulus[] = $siswa;
                    continue;
                }

                $kelas = $kelasBaru[$pilihan[$siswa->id]];
                $siswa->update(['kelas_id' => $kelas->id]);
                RiwayatKelas::catat($siswa->fresh());
                $this->barisKosong($siswa, $target);
                $disalin++;
                if ($aksi === 'tinggal') {
                    $tinggal++;
                }
            }

            Tapel::where('id', '!=', $target->id)->update(['is_aktif' => false]);
            $target->update(['is_aktif' => true]);

            return [
                'tapel' => $target->fresh(),
                'disalin' => $disalin,
                'tinggal' => $tinggal,
                'lulus' => $lulus,
            ];
        });
    }

    private function siswaSumber(Tapel $sumber)
    {
        $ids = RiwayatKelas::where('tapel_id', $sumber->id)->pluck('siswa_id');
        $query = Siswa::with('kelas.tingkat', 'catatanWalas', 'user');
        if ($ids->isNotEmpty()) {
            $query->whereIn('id', $ids);
        } else {
            $kelasIds = Kelas::where('tapel_id', $sumber->id)->pluck('id');
            $query->whereIn('kelas_id', $kelasIds);
        }

        return $query->whereHas('user', fn ($q) => $q->where('is_aktif', true))->orderBy('name')->get();
    }

    private function keputusanWali(Siswa $siswa, bool $naikTingkat, int $tingkat, int $tapelId): string
    {
        if (!$siswa->kelas_id || $tingkat < 1) {
            return 'lulus';
        }
        if (!$naikTingkat) {
            return 'tetap';
        }

        $naik = CatatanWalas::where('siswa_id', $siswa->id)->where('tapel_id', $tapelId)->value('naik_tingkat');
        $akhir = in_array($tingkat, [6, 9, 12], true);
        if ($akhir) {
            return ($naik === null || (int) $naik === 1) ? 'lulus' : 'tinggal';
        }

        return (int) $naik === 0 && $naik !== null ? 'tinggal' : 'naik';
    }

    private function pastikanAksiSah(string $aksi, int $tingkat, bool $naikTingkat, bool $punyaKelas): void
    {
        if (!$punyaKelas) {
            if ($aksi !== 'lulus') {
                throw new InvalidArgumentException('Siswa tanpa kelas tidak dapat dibawa.');
            }

            return;
        }

        if (!$naikTingkat && $aksi !== 'tetap') {
            throw new InvalidArgumentException('Pergantian semester menempatkan siswa di tingkat yang sama.');
        }

        $akhir = in_array($tingkat, [6, 9, 12], true);
        if ($naikTingkat && $akhir && !in_array($aksi, ['lulus', 'tinggal'], true)) {
            throw new InvalidArgumentException('Siswa tingkat akhir hanya dapat lulus atau tinggal.');
        }
        if ($naikTingkat && !$akhir && !in_array($aksi, ['naik', 'tinggal'], true)) {
            throw new InvalidArgumentException('Siswa hanya dapat naik atau tinggal.');
        }
    }

    private function opsiKelas(Tapel $sumber)
    {
        return Kelas::with('tingkat')
            ->where('tapel_id', $sumber->id)
            ->orderBy('tingkat_id')
            ->orderBy('name')
            ->get();
    }

    private function kelasDefault($opsi, ?Kelas $asal, string $aksi): ?int
    {
        if (!$asal || !$asal->tingkat || !in_array($aksi, ['naik', 'tinggal'], true)) {
            return null;
        }

        $angka = $aksi === 'naik' ? ((int) $asal->tingkat->angka + 1) : (int) $asal->tingkat->angka;
        $cocok = $opsi->first(function (Kelas $kelas) use ($asal, $angka) {
            return $kelas->name === $asal->name && (int) optional($kelas->tingkat)->angka === $angka;
        });

        return $cocok ? $cocok->id : null;
    }

    private function kelasDipilih($kelasSumber, array $kelasTujuanPerSiswa, Siswa $siswa): Kelas
    {
        $id = (int) ($kelasTujuanPerSiswa[$siswa->id] ?? 0);
        $dipilih = $kelasSumber->get($id);
        if (!$dipilih || !$dipilih->tingkat) {
            throw new InvalidArgumentException('Siswa ' . $siswa->name . ' belum memilih kelas tujuan yang sudah ada.');
        }

        return $dipilih;
    }

    private function salinKelas(Kelas $asal, Tingkat $tingkat, Tapel $target): Kelas
    {
        $ada = Kelas::where('tapel_id', $target->id)
            ->where('tingkat_id', $tingkat->id)
            ->where('name', $asal->name)
            ->first();
        if ($ada) {
            return $ada;
        }

        $baru = Kelas::create([
            'name' => $asal->name,
            'tingkat_id' => $tingkat->id,
            'tapel_id' => $target->id,
            'guru_id' => $asal->guru_id,
        ]);

        Pembelajaran::where('kelas_id', $asal->id)->get()->each(function (Pembelajaran $item) use ($baru) {
            Pembelajaran::create([
                'kelas_id' => $baru->id,
                'mapel_id' => $item->mapel_id,
                'guru_id' => $item->guru_id,
            ]);
        });

        return $baru;
    }

    private function barisKosong(Siswa $siswa, Tapel $tapel): void
    {
        Ketidakhadiran::firstOrCreate(
            ['siswa_id' => $siswa->id, 'tapel_id' => $tapel->id],
            ['sakit' => null, 'izin' => null, 'tk' => null]
        );
        CatatanWalas::firstOrCreate(
            ['siswa_id' => $siswa->id, 'tapel_id' => $tapel->id],
            ['catatan' => null, 'naik_tingkat' => null]
        );
    }
}
