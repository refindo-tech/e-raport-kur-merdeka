<?php

namespace App\Http\Controllers;

use App\Imports\CapaianAkhirImport;
use App\Imports\SiswaImport;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\Tingkat;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;
use Yajra\DataTables\Facades\DataTables;

class SiswaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        if ($user->isAdmin()) {
          $data = Siswa::query()->orderBy('name', 'asc');
        } elseif($user->isWaliKelas()){
          $data = Siswa::where('kelas_id', $user->guru->kelas->id)->orderBy('name', 'asc');
        } else {
          abort(403);
        }

        if ($request->ajax()) {

          if ($request->jk) $data->where('jk', $request->jk);
          if ($request->kelas_id) $data->where('kelas_id', $request->kelas_id);
          if ($request->is_aktif !== null) {
            $isAktif = $request->is_aktif === '1' ? true : false;
            $data->whereHas('user', fn($q) => $q->where('is_aktif', $isAktif));
          }

          return DataTables::of($data->with('user:id,is_aktif', 'kelas'))->addIndexColumn()
                                      ->editColumn('kelas.name', function($data){
                                        return ($data->kelas) ? $data->kelas->name : '-';
                                      })
                                      ->editColumn('user.is_aktif', function($data){
                                        return $data->user->is_aktif == true ? 'AKTIF' : 'NON-AKTIF';
                                      })
                                      ->editColumn('nis-nisn', function($data){
                                        return $data->nis . '/' . $data->nisn;
                                      })
                                      ->addColumn('aksi', function($data){
                                        return view('pages.siswa._aksi')->with('data', $data);
                                      })
                                      ->make(true);
          }

        return view('pages.siswa.index', [
          'siswa' => $data,
          'kelas' => Kelas::select('id', 'name')->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!Auth::user()->isAdmin() && !Auth::user()->isWaliKelas()) abort(403);
        return view('pages.siswa.create',[
          'kelas' => Kelas::select('id','name')->get(),
          'tingkat' => Tingkat::select('angka', 'romawi')->get()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
          'kelas_id' => 'nullable',
          'name' => 'required',
          'nis' => 'nullable|numeric|unique:siswas,nis',
          'nisn' => 'nullable|numeric|unique:siswas,nisn',
          'tempatlahir' => 'required',
          'tanggallahir' => 'required|date',
          'jk' => 'required',
          'agama' => 'required',
          'statusdalamkeluarga' => 'nullable',
          'anak_ke' => 'nullable|numeric',
          'alamatsiswa' => 'nullable',
          'teleponsiswa' => 'nullable',
          'sekolahasal' => 'nullable',
          'diterimadikelas' => 'nullable',
          'diterimaditanggal' => 'nullable|date',
          'namaayah' => 'nullable',
          'pekerjaanayah' => 'nullable',
          'namaibu' => 'nullable',
          'pekerjaanibu' => 'nullable',
          'alamatortu' => 'nullable',
          'teleponortu' => 'nullable',
          'namawali' => 'nullable',
          'pekerjaanwali' => 'nullable',
          'alamatwali' => 'nullable',
          'teleponwali' => 'nullable',

          'email' => 'nullable',
          'username' => 'required|unique:users',
          'password' => 'required',

        ]);

        $user = User::create([
          'username' => $request->username,
          'password' => $request->password,
          'email' => $request->email,
          'role' => 'siswa',
        ]);

        $request['user_id'] = $user->id;
        Siswa::create($request->except('username', 'password', 'email'));
        return redirect(route('siswa.index'))->withSuccess('Data berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Siswa $siswa)
    {
        $siswa->load('user:id,is_aktif,foto');
        $siswa->has('kelas') ? $siswa->load('kelas:id,name') : '';
        return response()->json(['result' => $siswa]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Siswa $siswa)
    {
      if (!Auth::user()->isAdmin() && !Auth::user()->isWaliKelas()) abort(403);
        return view('pages.siswa.edit',[
          'siswa' => $siswa,
          'kelas' => Kelas::select('id','name')->get(),
          'tingkat' => Tingkat::select('angka', 'romawi')->get()
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Siswa $siswa)
    {
      try {
        \Log::info('Siswa Update Request', [
          'siswa_id' => $siswa->id,
          'request_data' => $request->all()
        ]);

        $request->validate([
          'kelas_id' => 'nullable',
          'name' => 'required',
          'nis' => 'nullable|numeric|unique:siswas,nis,' . $siswa->id,
          'nisn' => 'nullable|numeric|unique:siswas,nisn,' . $siswa->id,
          'tempatlahir' => 'required',
          'tanggallahir' => 'required|date',
          'jk' => 'required',
          'agama' => 'required',
          'statusdalamkeluarga' => 'nullable',
          'anak_ke' => 'nullable|numeric',
          'alamatsiswa' => 'nullable',
          'teleponsiswa' => 'nullable',
          'sekolahasal' => 'nullable',
          'diterimadikelas' => 'nullable',
          'diterimaditanggal' => 'nullable|date',
          'namaayah' => 'nullable',
          'pekerjaanayah' => 'nullable',
          'namaibu' => 'nullable',
          'pekerjaanibu' => 'nullable',
          'alamatortu' => 'nullable',
          'teleponortu' => 'nullable',
          'namawali' => 'nullable',
          'pekerjaanwali' => 'nullable',
          'alamatwali' => 'nullable',
          'teleponwali' => 'nullable',
          'email' => 'nullable|unique:users,email,' . $siswa->user_id,
          'username' => 'required|unique:users,username,' . $siswa->user_id,
          'is_aktif' => 'required',
          'password' => 'nullable',
        ]);

        \Log::info('Validation passed for siswa update', ['siswa_id' => $siswa->id]);

        DB::beginTransaction();

        if (filled($request->password)) {
          $userUpdate = $siswa->user->update([
            'username' => $request->username,
            'email' => $request->email,
            'is_aktif' => $request->is_aktif,
            'password' => $request->password,
          ]);
          \Log::info('User updated with password', ['user_id' => $siswa->user_id, 'success' => $userUpdate]);
        } else {
          $userUpdate = $siswa->user->update([
            'username' => $request->username,
            'email' => $request->email,
            'is_aktif' => $request->is_aktif,
          ]);
          \Log::info('User updated without password', ['user_id' => $siswa->user_id, 'success' => $userUpdate]);
        }

        $siswaData = $request->except('username', 'password', 'email', 'is_aktif');
        $siswaUpdate = $siswa->update($siswaData);
        \Log::info('Siswa updated', ['siswa_id' => $siswa->id, 'success' => $siswaUpdate, 'data' => $siswaData]);

        DB::commit();
        \Log::info('Siswa update transaction committed', ['siswa_id' => $siswa->id]);

        return redirect(route('siswa.index'))->withSuccess('Data berhasil diperbarui!');
      } catch (\Illuminate\Validation\ValidationException $e) {
        DB::rollBack();
        \Log::error('Validation error in siswa update', [
          'siswa_id' => $siswa->id,
          'errors' => $e->errors()
        ]);
        
        // Format error messages untuk popup
        $errorMessages = [];
        $fieldNames = [
          'name' => 'Nama',
          'kelas_id' => 'Kelas',
          'nis' => 'NIS',
          'nisn' => 'NISN',
          'tempatlahir' => 'Tempat Lahir',
          'tanggallahir' => 'Tanggal Lahir',
          'jk' => 'Jenis Kelamin',
          'agama' => 'Agama',
          'statusdalamkeluarga' => 'Status Dalam Keluarga',
          'anak_ke' => 'Anak Ke',
          'alamatsiswa' => 'Alamat Siswa',
          'teleponsiswa' => 'Telepon Siswa',
          'sekolahasal' => 'Sekolah Asal',
          'diterimadikelas' => 'Diterima Di Kelas',
          'diterimaditanggal' => 'Diterima Di Tanggal',
          'namaayah' => 'Nama Ayah',
          'pekerjaanayah' => 'Pekerjaan Ayah',
          'namaibu' => 'Nama Ibu',
          'pekerjaanibu' => 'Pekerjaan Ibu',
          'alamatortu' => 'Alamat Orang Tua',
          'teleponortu' => 'Telepon Orang Tua',
          'namawali' => 'Nama Wali',
          'pekerjaanwali' => 'Pekerjaan Wali',
          'alamatwali' => 'Alamat Wali',
          'teleponwali' => 'Telepon Wali',
          'email' => 'Email',
          'username' => 'Username',
          'is_aktif' => 'Status Aktif',
          'password' => 'Password',
        ];
        
        foreach ($e->errors() as $field => $messages) {
          $fieldLabel = $fieldNames[$field] ?? ucfirst(str_replace('_', ' ', $field));
          foreach ($messages as $message) {
            $errorMessages[] = '<strong>' . $fieldLabel . '</strong>: ' . $message;
          }
        }
        
        $errorMessage = '<strong>Gagal menyimpan data siswa!</strong><br><br>Silakan perbaiki field berikut:<br>' . implode('<br>', $errorMessages);
        
        return back()->withInput()->withErrors($e->errors())->withFailed($errorMessage);
      } catch (\Exception $e) {
        DB::rollBack();
        \Log::error('Error updating siswa', [
          'siswa_id' => $siswa->id,
          'error' => $e->getMessage(),
          'trace' => $e->getTraceAsString()
        ]);
        return back()->withInput()->withFailed('Terjadi kesalahan saat memperbarui data: ' . $e->getMessage());
      }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Siswa $siswa)
    {
      $success = $siswa->name . ' berhasil dihapus!';
      // Hapus user, siswa akan otomatis terhapus karena cascade delete
      $siswa->user->delete();
      return response()->json(['success' => $success]);
    }

    public function getDestroyMany(Request $request){
      $user = Auth::user();
      if ($user->isAdmin()) {
        $data = Siswa::query()->orderBy('name', 'asc');
      } elseif($user->isWaliKelas()){
        $data = Siswa::where('kelas_id', $user->guru->kelas->id)->orderBy('name', 'asc');
      }

      if ($request->kelas_id) $data->where('kelas_id', $request->kelas_id);

      if ($request->ajax()) {
        return DataTables::of($data->with('kelas'))
            ->addColumn('hapus', function($data){
                return view('pages.siswa._tandai')->with('id', $data->id);
            })
            ->editColumn('kelas.name', function($data){
              return ($data->kelas) ? $data->kelas->name : '-';
            })
            ->editColumn('nis-nisn', function($data){
              return $data->nis . '/' . $data->nisn;
            })
            ->rawColumns(['hapus'])
            ->make(true);
      }
    }

    public function postDestroyMany(Request $request){
      $validasi = Validator::make($request->all(),[
        'siswa_id' => 'required|exists:siswas,id'
      ]);

      if ($validasi->fails()) {
        return response()->json(['failed' => 'Gagal!']);
      }

      try {
        DB::beginTransaction();
          // Ambil user_id dari siswa yang akan dihapus
          $siswaIds = $request->siswa_id;
          $userIds = Siswa::whereIn('id', $siswaIds)->pluck('user_id')->toArray();
          
          // Hapus user, siswa akan otomatis terhapus karena cascade delete
          User::whereIn('id', $userIds)->delete();
        DB::commit();
        return response()->json(['success' => 'Berhasil dihapus!']);
      } catch (\Throwable $th) {
        DB::rollBack();
        \Log::error('Delete Many Siswa Error: ' . $th->getMessage());
        return response()->json(['failed' => 'Gagal, coba lagi!']);
      }
    }

    public function import(Request $request)
    {
      $request->validate([
        'file' => ['required', 'file', 'distinct']
      ]);

      $file = $request->file('file');
      if ($file->getClientOriginalExtension() != 'xlsx') {
          return back()->withFailed('Import Gagal! File yang anda masukkan tidak sesuai ketentuan!');
      }

      try {
        $import = new SiswaImport();
        Excel::import($import, request()->file('file'));
        
        $failures = $import->getFailures();
        if (!empty($failures)) {
          $failureMessages = [];
          foreach ($failures as $failure) {
            $failureMessages[] = "Baris {$failure['row']}: " . implode(', ', $failure['errors']);
          }
          return redirect()->back()
            ->with('success', 'Data siswa berhasil diimport!')
            ->with('warning', 'Beberapa data memiliki masalah: ' . implode(' | ', $failureMessages));
        }
        
        return redirect()->back()->with('success', 'Data siswa berhasil diimport!');
      } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
        $failures = $e->failures();
        $failureMessages = [];
        foreach ($failures as $failure) {
          $failureMessages[] = "Baris {$failure->row()}: " . implode(', ', $failure->errors());
        }
        return back()->withFailed('Import Gagal! Validasi error: ' . implode(' | ', $failureMessages));
      } catch (\Throwable $th) {
        \Log::error('Import Siswa Error: ' . $th->getMessage());
        \Log::error('Stack Trace: ' . $th->getTraceAsString());
        return back()->withFailed('Import Gagal! ' . $th->getMessage());
      }

    }
}
