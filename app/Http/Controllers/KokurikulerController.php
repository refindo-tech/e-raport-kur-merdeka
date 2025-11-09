<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\KokurikulerDimension;
use App\Models\KokurikulerSubdimension;
use App\Models\Siswa;
use App\Models\StudentKokurikuler;
use App\Models\Tapel;
use App\Models\Tingkat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class KokurikulerController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user->isWaliKelas()) {
            abort(403);
        }

        $data = Kelas::where('guru_id', $user->guru->id);

        if ($request->ajax()) {
            if ($request->tingkat_id) {
                $data->where('tingkat_id', $request->tingkat_id);
            }

            return DataTables::of($data->with('tingkat:id,angka', 'siswa:id,kelas_id'))
                ->addIndexColumn()
                ->editColumn('tingkat.angka', function ($data) {
                    return $data->tingkat->angka;
                })
                ->editColumn('guru.name', function ($data) {
                    return $data->wali_kelas();
                })
                ->editColumn('siswa.count', function ($data) {
                    return $data->siswaAktifKelasCount($data->id);
                })
                ->addColumn('aksi', function ($data) {
                    return view('pages.kokurikuler._aksi')->with('data', $data);
                })
                ->make(true);
        }

        return view('pages.kokurikuler.index', [
            'kelas' => $data,
            'tingkat' => Tingkat::get(),
        ]);
    }

    public function show(Request $request, $id)
    {
        $user = Auth::user();
        if (!($user->isWaliKelas() && ($user->guru->id == Kelas::find($id)->guru_id))) {
            abort(403);
        }

        $kelas = Kelas::findOrFail($id);
        $tapel = Tapel::where('id', $kelas->tapel_id)->first();
        $siswa = Siswa::whereHas('user', fn ($q) => $q->where('is_aktif', true))
            ->where('kelas_id', $id)
            ->orderBy('name', 'ASC');

        $dimensions = KokurikulerDimension::with('subdimensions')->get();

        if ($request->ajax()) {
            return DataTables::of($siswa->with(['studentKokurikuler' => function($q) use ($tapel) {
                    $q->where('tapel_id', $tapel->id)->with('subdimension.dimension');
                }]))
                ->addIndexColumn()
                ->editColumn('kokurikuler', function ($data) use ($tapel, $dimensions) {
                    return view('pages.kokurikuler._kokurikuler')
                        ->with(['siswa' => $data, 'tapel' => $tapel, 'dimensions' => $dimensions]);
                })
                ->make(true);
        }

        return view('pages.kokurikuler.show', [
            'siswa' => $siswa,
            'kelas' => $kelas,
            'tapel' => $tapel,
            'dimensions' => $dimensions,
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user->isWaliKelas()) {
            return response()->json(['failed' => 'Unauthorized']);
        }

        $validated = $request->validate([
            'student_id' => 'required|exists:siswas,id',
            'class_id' => 'required|exists:kelas,id',
            'tapel_id' => 'required|exists:tapels,id',
            'kokurikuler' => 'nullable|array',
            'kokurikuler.*' => 'nullable|in:berkembang,cakap,mahir',
        ]);

        try {
            DB::beginTransaction();

            $siswa = Siswa::findOrFail($validated['student_id']);
            
            StudentKokurikuler::where('student_id', $validated['student_id'])
                ->where('tapel_id', $validated['tapel_id'])
                ->delete();

            if (isset($validated['kokurikuler']) && is_array($validated['kokurikuler'])) {
                foreach ($validated['kokurikuler'] as $subdimensionId => $level) {
                    if ($level) {
                        $subdimension = KokurikulerSubdimension::findOrFail($subdimensionId);
                        $descriptionText = $subdimension->getDescriptionByLevel($level);
                        $description = $this->generateDescription($siswa->name, $level, $descriptionText);

                        StudentKokurikuler::create([
                            'student_id' => $validated['student_id'],
                            'class_id' => $validated['class_id'],
                            'tapel_id' => $validated['tapel_id'],
                            'subdimension_id' => $subdimensionId,
                            'level' => $level,
                            'description' => $description,
                        ]);
                    }
                }
            }

            DB::commit();
            return response()->json(['success' => 'Data kokurikuler berhasil disimpan!']);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['failed' => 'Terjadi kesalahan: ' . $th->getMessage()]);
        }
    }

    public function update(Request $request, StudentKokurikuler $kokurikuler)
    {
        $user = Auth::user();
        if (!$user->isWaliKelas()) {
            return response()->json(['failed' => 'Unauthorized']);
        }

        $validated = $request->validate([
            'level' => 'required|in:berkembang,cakap,mahir',
        ]);

        try {
            DB::beginTransaction();

            $siswa = $kokurikuler->siswa;
            $subdimension = $kokurikuler->subdimension;
            $descriptionText = $subdimension->getDescriptionByLevel($validated['level']);
            $description = $this->generateDescription($siswa->name, $validated['level'], $descriptionText);

            $kokurikuler->update([
                'level' => $validated['level'],
                'description' => $description,
            ]);

            DB::commit();
            return response()->json(['success' => 'Data kokurikuler berhasil diperbarui!']);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['failed' => 'Terjadi kesalahan: ' . $th->getMessage()]);
        }
    }

    public function destroy(StudentKokurikuler $kokurikuler)
    {
        $user = Auth::user();
        if (!$user->isWaliKelas()) {
            return response()->json(['failed' => 'Unauthorized']);
        }

        try {
            $kokurikuler->delete();
            return response()->json(['success' => 'Data kokurikuler berhasil dihapus!']);
        } catch (\Throwable $th) {
            return response()->json(['failed' => 'Terjadi kesalahan: ' . $th->getMessage()]);
        }
    }

    private function generateDescription($studentName, $level, $descriptionText)
    {
        if ($level === 'berkembang') {
            $prefix = 'Ananda ' . $studentName . ' masih perlu berlatih dalam ';
        } elseif ($level === 'cakap') {
            $prefix = 'Ananda ' . $studentName . ' sudah baik dalam ';
        } elseif ($level === 'mahir') {
            $prefix = 'Ananda ' . $studentName . ' sudah sangat baik dalam ';
        } else {
            $prefix = 'Ananda ' . $studentName . ' ';
        }

        return $prefix . $descriptionText;
    }
}

