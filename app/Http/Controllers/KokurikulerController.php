<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\KokurikulerDimension;
use App\Models\KokurikulerTemplate;
use App\Models\Siswa;
use App\Models\StudentKokurikulerDescription;
use App\Models\StudentKokurikulerLevel;
use App\Models\Tapel;
use App\Models\Tingkat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class KokurikulerController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user->isAdmin() && !$user->isWaliKelas()) {
            abort(403);
        }

        // Admin dapat melihat semua kelas, Wali Kelas hanya kelas yang diampu
        $data = $user->isAdmin() 
            ? Kelas::query() 
            : Kelas::where('guru_id', $user->guru->id);

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
        $kelas = Kelas::findOrFail($id);
        
        // Admin dapat mengakses semua kelas, Wali Kelas hanya kelas yang diampu
        if (!$user->isAdmin() && !($user->isWaliKelas() && ($user->guru->id == $kelas->guru_id))) {
            abort(403);
        }

        $tapel = Tapel::where('id', $kelas->tapel_id)->first();
        $siswa = Siswa::whereHas('user', fn ($q) => $q->where('is_aktif', true))
            ->where('kelas_id', $id)
            ->orderBy('name', 'ASC');

        $dimensions = KokurikulerDimension::orderBy('id')->get();
        $templates = $this->templateDictionary();

        if ($request->ajax()) {
            return DataTables::of(
                $siswa->with([
                    'kokurikulerLevels' => function ($q) use ($tapel) {
                        $q->where('tapel_id', $tapel->id)->with('dimension');
                    },
                    'kokurikulerDescriptions' => function ($q) use ($tapel) {
                        $q->where('tapel_id', $tapel->id);
                    },
                ])
            )
                ->addIndexColumn()
                ->addColumn('nilai_tinggi', function ($data) use ($tapel, $dimensions) {
                    return view('pages.kokurikuler._kokurikuler', [
                        'siswa' => $data,
                        'tapel' => $tapel,
                        'dimensions' => $dimensions,
                        'levelType' => 'tinggi',
                    ]);
                })
                ->addColumn('nilai_rendah', function ($data) use ($tapel, $dimensions, $templates) {
                    return view('pages.kokurikuler._kokurikuler', [
                        'siswa' => $data,
                        'tapel' => $tapel,
                        'dimensions' => $dimensions,
                        'levelType' => 'rendah',
                        'templates' => $templates,
                    ]);
                })
                ->rawColumns(['nilai_tinggi', 'nilai_rendah'])
                ->make(true);
        }

        return view('pages.kokurikuler.show', [
            'siswa' => $siswa,
            'kelas' => $kelas,
            'tapel' => $tapel,
            'dimensions' => $dimensions,
            'templates' => $templates,
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user->isAdmin() && !$user->isWaliKelas()) {
            return response()->json(['failed' => 'Unauthorized']);
        }

        $validated = $request->validate([
            'student_id' => 'required|exists:siswas,id',
            'class_id' => 'required|exists:kelas,id',
            'tapel_id' => 'required|exists:tapels,id',
            'tinggi_dimensions' => 'nullable|array',
            'tinggi_dimensions.*' => 'exists:kokurikuler_dimensions,id',
            'rendah_dimensions' => 'nullable|array',
            'rendah_dimensions.*' => 'exists:kokurikuler_dimensions,id',
            'final_description' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $siswa = Siswa::findOrFail($validated['student_id']);

            StudentKokurikulerLevel::where('student_id', $validated['student_id'])
                ->where('tapel_id', $validated['tapel_id'])
                ->delete();

            $tinggi = collect($validated['tinggi_dimensions'] ?? [])->unique()->values();
            $rendah = collect($validated['rendah_dimensions'] ?? [])->unique()->values();
            $rendah = $rendah->diff($tinggi);

            $this->storeLevels($validated, $tinggi, 'tinggi');
            $this->storeLevels($validated, $rendah, 'rendah');

            $sentences = $this->generateSentences($siswa->name, $tinggi, $rendah);
            $finalDescription = trim($validated['final_description'] ?? implode(' ', array_filter($sentences)));

            StudentKokurikulerDescription::updateOrCreate(
                [
                    'student_id' => $validated['student_id'],
                    'tapel_id' => $validated['tapel_id'],
                ],
                [
                    'class_id' => $validated['class_id'],
                    'tinggi_description' => $sentences['tinggi'],
                    'rendah_description' => $sentences['rendah'],
                    'final_description' => $finalDescription,
                ]
            );

            DB::commit();
            return response()->json([
                'success' => 'Data kokurikuler berhasil disimpan!',
                'final_description' => $finalDescription,
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['failed' => 'Terjadi kesalahan: ' . $th->getMessage()]);
        }
    }

    public function update(Request $request, StudentKokurikuler $kokurikuler)
    {
        abort(404);
    }

    public function destroy(StudentKokurikuler $kokurikuler)
    {
        abort(404);
    }

    private function storeLevels(array $payload, $dimensionIds, string $level): void
    {
        foreach ($dimensionIds as $dimensionId) {
            StudentKokurikulerLevel::create([
                'student_id' => $payload['student_id'],
                'class_id' => $payload['class_id'],
                'tapel_id' => $payload['tapel_id'],
                'dimension_id' => $dimensionId,
                'level' => $level,
            ]);
        }
    }

    private function generateSentences(string $studentName, $tinggiIds, $rendahIds): array
    {
        $templates = $this->templateDictionary();
        $sentences = [
            'tinggi' => null,
            'rendah' => null,
        ];

        if ($tinggiIds->isNotEmpty()) {
            $list = $this->dimensionList($tinggiIds);
            if ($list !== '') {
                $sentences['tinggi'] = $this->applyTemplate($templates['tinggi'], $studentName, $list);
            }
        }

        if ($rendahIds->isNotEmpty()) {
            $list = $this->dimensionList($rendahIds);
            if ($list !== '') {
                $sentences['rendah'] = $this->applyTemplate($templates['rendah'], $studentName, $list);
            }
        }

        return $sentences;
    }

    private function dimensionList($dimensionIds): string
    {
        return KokurikulerDimension::whereIn('id', $dimensionIds)
            ->orderBy('id')
            ->get()
            ->pluck('description')
            ->map(function ($desc) {
                return $desc ? Str::lower($desc) : null;
            })
            ->filter()
            ->implode(', ');
    }

    private function applyTemplate(string $template, string $studentName, string $list): string
    {
        return str_replace(
            ['{{ student_name }}', '{{ list_of_descriptions }}'],
            [$studentName, $list],
            $template
        );
    }

    private function templateDictionary(): array
    {
        $defaults = [
            'tinggi' => 'Ananda {{ student_name }} sudah baik dalam {{ list_of_descriptions }}.',
            'rendah' => 'Ananda {{ student_name }} perlu bantuan dalam {{ list_of_descriptions }}.',
        ];

        $templates = KokurikulerTemplate::all()->pluck('template_text', 'level')->toArray();

        return array_merge($defaults, $templates);
    }
}

