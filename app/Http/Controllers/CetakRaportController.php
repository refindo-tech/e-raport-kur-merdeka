<?php

namespace App\Http\Controllers;

use App\Models\AnggotaEkskul;
use App\Models\AnggotaKelompok;
use App\Models\RiwayatKelas;
use App\Models\CapaianAkhir;
use App\Models\CapaianProjek;
use App\Models\CatatanWalas;
use App\Models\Dimensi;
use App\Models\Kelas;
use App\Models\KelompokMapel;
use App\Models\Ketidakhadiran;
use App\Models\NilaiAkhir;
use App\Models\Pembelajaran;
use App\Models\Projek;
use App\Models\Sekolah;
use App\Models\Siswa;
use App\Models\StudentKokurikulerDescription;
use App\Models\Tingkat;
use PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class CetakRaportController extends Controller
{
    public function index(Request $request)
    {
      $user = Auth::user();
      if ($user->isAdmin()) {
        $data = Kelas::latest();
      } elseif($user->isWaliKelas()){
        $data = Kelas::where('guru_id', $user->guru->id);
      } elseif($user->isSiswa()){
        $data = Kelas::where('id', $user->siswa->kelas_id);
      } else {
        abort(403);
      }

      if ($request->ajax()) {

        if ($request->tingkat_id) $data->where('tingkat_id', $request->tingkat_id);

        return DataTables::of($data->with('tingkat:id,angka')->withCount(['siswa', 'riwayatKelas']))->addIndexColumn()
                                    ->editColumn('tingkat.angka', function($data){
                                      return $data->tingkat->angka;})
                                    ->editColumn('guru.name', function($data){
                                      return $data->wali_kelas();})
                                    ->editColumn('siswa.count', function($data){
                                      return $data->riwayat_kelas_count ?: $data->siswa_count;})
                                    ->addColumn('aksi', function($data){
                                        return view('pages.cetakrapor._aksi')->with('kelasId', $data->id);})
                                    ->make(true);
      }

      return view('pages.cetakrapor.index',[
        'kelas' => $data,
        'tingkat' => Tingkat::get(),
      ]);
    }

    public function show(Request $request, Kelas $cetakrapor){
      $kelas = $cetakrapor;

      if (Auth::user()->isAdmin()) {
        $data = $this->siswaDiKelas($kelas);
      } elseif (Auth::user()->isWaliKelas() && Auth::user()->guru && ($kelas->guru_id == Auth::user()->guru->id)){
        $data = $this->siswaDiKelas($kelas);
      } elseif (Auth::user()->isSiswa() && RiwayatKelas::where('siswa_id', Auth::user()->siswa->id)->where('kelas_id', $kelas->id)->exists()) {
        $data = Siswa::where('id', Auth::user()->siswa->id);
      } else {
        abort(403);
      }

      if ($request->ajax()) {
        return DataTables::of($data->with('kelas'))->addIndexColumn()
                                    ->editColumn('nis-nisn', function($data){
                                      return $data->nis . '/' . $data->nisn;
                                    })
                                    ->addColumn('aksi', function($data){
                                      return view('pages.cetakrapor._aksishow')->with('siswaId', $data->id);
                                    })
                                    ->make(true);
        }

      return view('pages.cetakrapor.show', [
        'siswa' => $data,
        'kelas' => $kelas,
      ]);
    }

    public function kelengkapan(Siswa $siswa, $paper) {

      if
      (
        !Auth::user()->isAdmin() &&
        !(Auth::user()->isWaliKelas() && ($siswa->kelas_id == Auth::user()->guru->kelas->id)) &&
        !(Auth::user()->isSiswa() && ($siswa->id == Auth::user()->siswa->id))
      )
      {
        abort(403);
      }

      // Sanitize filename - remove newlines and invalid characters for headers
      $fileName = 'KELENGKAPAN RAPOR - ' . $siswa->name . ' ' . $siswa->kelas->name . ' ' . $siswa->nis . '.pdf';
      $fileName = preg_replace('/[\r\n\t]+/', ' ', $fileName); // Remove newlines, carriage returns, tabs
      $fileName = preg_replace('/\s+/', ' ', $fileName); // Replace multiple spaces with single space
      $fileName = trim($fileName); // Trim whitespace

      try {
        return PDF::loadview('pages.cetakrapor.kelengkapan.print',[
          'siswa' => $siswa,
          'sekolah' => Sekolah::first(),
        ])->setPaper($paper, 'Potrait')->stream($fileName);
      } catch (\Exception $e) {
        \Log::error('PDF Generation Error (Kelengkapan): ' . $e->getMessage(), [
          'fileName' => $fileName,
          'siswa_name' => $siswa->name,
          'kelas_name' => $siswa->kelas->name,
          'nis' => $siswa->nis,
          'trace' => $e->getTraceAsString()
        ]);
        throw $e;
      }
    }

    public function p5(Request $request, Siswa $siswa, $paper) {
      $kelas = $this->kelasUntukCetak($request, $siswa);
      if (!$this->bolehMencetak($siswa, $kelas)) {
        abort(403);
      }
      $siswa->setRelation('kelas', $kelas);

      $anggota = AnggotaKelompok::where('siswa_id', $siswa->id)
        ->whereHas('kelompokProjek', fn ($q) => $q->where('kelas_id', $kelas->id))
        ->first();
      if (!$anggota) {
        return back()->withWarning($siswa->name . ' belum memiliki Projek pada kelas ini!');
      }
      $siswa->setRelation('anggotaKelompok', $anggota);

      $projekId = $siswa->anggotaKelompok->kelompokProjek->projekPilihanKelompok->pluck('projek_id');
      $projek = Projek::whereIn('id', $projekId)->get();
      $capaianProjek =  CapaianProjek::whereIn('projek_id', $projekId)->get();

      $dimensi = Dimensi::select('dimensis.*')
      ->join('elemens', 'dimensis.id', '=', 'elemens.dimensi_id')
      ->join('sub_elemens', 'elemens.id', '=', 'sub_elemens.elemen_id')
      ->join('capaian_akhirs', 'sub_elemens.id', '=', 'capaian_akhirs.sub_elemen_id')
      ->join('capaian_projeks', 'capaian_akhirs.id', '=', 'capaian_projeks.capaian_akhir_id')
      ->whereIn('capaian_projeks.projek_id', $projekId)
      ->distinct('dimensis.id')
      ->get();

      // Sanitize filename - remove newlines and invalid characters for headers
      $fileName = 'RAPOR PROJEK - ' . $siswa->name . ' ' . $siswa->kelas->name . ' ' . $siswa->nis . '.pdf';
      $fileName = preg_replace('/[\r\n\t]+/', ' ', $fileName); // Remove newlines, carriage returns, tabs
      $fileName = preg_replace('/\s+/', ' ', $fileName); // Replace multiple spaces with single space
      $fileName = trim($fileName); // Trim whitespace

      try {
        return PDF::loadview('pages.cetakrapor.p5.print',[
          'siswa' => $siswa,
          'sekolah' => Sekolah::first(),
          'projek' => $projek,
          'dimensi' => $dimensi,
          'capaianprojek' => $capaianProjek,
          'centang' => '√',
        ])->setPaper($paper, 'Potrait')->stream($fileName);
      } catch (\Exception $e) {
        \Log::error('PDF Generation Error (P5): ' . $e->getMessage(), [
          'fileName' => $fileName,
          'siswa_name' => $siswa->name,
          'kelas_name' => $siswa->kelas->name,
          'nis' => $siswa->nis,
          'trace' => $e->getTraceAsString()
        ]);
        throw $e;
      }
    }

    public function semester(Request $request, Siswa $siswa, $paper) {
      $kelas = $this->kelasUntukCetak($request, $siswa);
      if (!$this->bolehMencetak($siswa, $kelas)) {
        abort(403);
      }
      $siswa->setRelation('kelas', $kelas);
      $kelMapelId = $kelas->pembelajaran->pluck('mapel.kelompok_mapel_id')->unique();
      
      // Sanitize filename - remove newlines and invalid characters for headers
      $fileName = 'RAPOR HASIL BELAJAR - ' . $siswa->name . ' ' . $siswa->kelas->name . ' ' . $siswa->nis . '.pdf';
      $fileName = preg_replace('/[\r\n\t]+/', ' ', $fileName); // Remove newlines, carriage returns, tabs
      $fileName = preg_replace('/\s+/', ' ', $fileName); // Replace multiple spaces with single space
      $fileName = trim($fileName); // Trim whitespace
      
      try {
        return PDF::loadview('pages.cetakrapor.semester.print2',[
          'siswa' => $siswa,
          'sekolah' => Sekolah::first(),
          'kelompokmapel' => KelompokMapel::whereIn('id', $kelMapelId)->get(),
          'pembelajaran' => Pembelajaran::where('kelas_id', $kelas->id)->get(),
          'nilaiakhir' => NilaiAkhir::where('siswa_id', $siswa->id)->whereIn('pembelajaran_id', $kelas->pembelajaran->pluck('id')),
          'anggotaEkskul' => AnggotaEkskul::where('siswa_id', $siswa->id)->whereHas('ekskul', fn ($q) => $q->where('tapel_id', $kelas->tapel_id))->get(),
          'ketidakhadiran' => Ketidakhadiran::where('siswa_id', $siswa->id)->where('tapel_id', $kelas->tapel_id)->get(),
          'catatanwalas' => CatatanWalas::where('siswa_id', $siswa->id)->where('tapel_id', $kelas->tapel_id)->get(),
          'studentKokurikuler' => StudentKokurikulerDescription::where('student_id', $siswa->id)
            ->where('tapel_id', $kelas->tapel_id)
            ->first(),
        ])->setPaper($paper, 'Potrait')->stream($fileName);
      } catch (\Exception $e) {
        \Log::error('PDF Generation Error: ' . $e->getMessage(), [
          'fileName' => $fileName,
          'siswa_name' => $siswa->name,
          'kelas_name' => $siswa->kelas->name,
          'nis' => $siswa->nis,
          'trace' => $e->getTraceAsString()
        ]);
        throw $e;
      }
    }

    private function kelasUntukCetak(Request $request, Siswa $siswa): Kelas
    {
        if ($request->filled('kelas')) {
            $kelas = Kelas::findOrFail($request->kelas);
            $punyaRiwayat = RiwayatKelas::where('siswa_id', $siswa->id)->where('kelas_id', $kelas->id)->exists();
            if (!$punyaRiwayat && $siswa->kelas_id != $kelas->id) {
                abort(404);
            }

            return $kelas;
        }

        return $siswa->kelas;
    }

    private function bolehMencetak(Siswa $siswa, Kelas $kelas): bool
    {
        $user = Auth::user();
        if ($user->isAdmin()) {
            return true;
        }
        if ($user->isWaliKelas() && $user->guru && $kelas->guru_id == $user->guru->id) {
            return true;
        }

        return $user->isSiswa() && $user->siswa && $user->siswa->id == $siswa->id;
    }

    private function siswaDiKelas(Kelas $kelas)
    {
        $ids = RiwayatKelas::where('kelas_id', $kelas->id)->pluck('siswa_id');
        $data = Siswa::orderBy('name', 'asc');
        $data = $ids->isNotEmpty() ? $data->whereIn('id', $ids) : $data->where('kelas_id', $kelas->id);
        if ($kelas->tapel && $kelas->tapel->is_aktif) {
            $data->whereHas('user', fn ($q) => $q->where('is_aktif', true));
        }

        return $data;
    }
}
