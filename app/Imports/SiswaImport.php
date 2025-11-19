<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Siswa;
use App\Models\Kelas;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Validators\Failure;

class SiswaImport implements ToModel, WithStartRow, WithValidation, SkipsOnFailure
{
    protected $failures = [];

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
      // Validasi kelas_id jika ada
      $kelasId = !empty($row[0]) ? $row[0] : null;
      if ($kelasId !== null) {
        // Cek apakah kelas_id valid
        $kelasExists = Kelas::where('id', $kelasId)->exists();
        if (!$kelasExists) {
          // Jika kelas tidak ada, set ke null (karena kelas_id nullable)
          \Log::warning("Kelas ID {$kelasId} tidak ditemukan untuk siswa {$row[1]}. Mengatur kelas_id menjadi null.");
          $kelasId = null;
        }
      }

      $user = User::create([
        'email' => $row[25] ?? null,
        'username' => $row[26],
        'role' => 'siswa',
        'password' => $row[27] ?? 'password', // Default password jika kosong
      ]);

      // Menyimpan data ke dalam tabel siswas
      $siswa = new Siswa([
          'user_id' => $user->id,
          'kelas_id' => $kelasId,
          'name' => $row[1],
          'nis' => !empty($row[2]) ? $row[2] : null,
          'nisn' => !empty($row[3]) ? $row[3] : null,
          'tempatlahir' => $row[4],
          'tanggallahir' => $row[5],
          'jk' => $row[6],
          'agama' => $row[7],
          'statusdalamkeluarga' => !empty($row[8]) ? $row[8] : null,
          'anak_ke' => !empty($row[9]) ? $row[9] : null,
          'alamatsiswa' => !empty($row[10]) ? $row[10] : null,
          'teleponsiswa' => !empty($row[11]) ? $row[11] : null,
          'sekolahasal' => !empty($row[12]) ? $row[12] : null,
          'diterimadikelas' => !empty($row[13]) ? $row[13] : null,
          'diterimaditanggal' => !empty($row[14]) ? $row[14] : null,
          'namaayah' => !empty($row[15]) ? $row[15] : null,
          'pekerjaanayah' => !empty($row[16]) ? $row[16] : null,
          'namaibu' => !empty($row[17]) ? $row[17] : null,
          'pekerjaanibu' => !empty($row[18]) ? $row[18] : null,
          'alamatortu' => !empty($row[19]) ? $row[19] : null,
          'teleponortu' => !empty($row[20]) ? $row[20] : null,
          'namawali' => !empty($row[21]) ? $row[21] : null,
          'pekerjaanwali' => !empty($row[22]) ? $row[22] : null,
          'alamatwali' => !empty($row[23]) ? $row[23] : null,
          'teleponwali' => !empty($row[24]) ? $row[24] : null,
      ]);

      $siswa->save();
      return $siswa;
    }

    /**
     * Validasi data sebelum import
     */
    public function rules(): array
    {
      return [
        '0' => 'nullable', // kelas_id - akan divalidasi di method model()
        '1' => 'required', // name
        '2' => 'nullable|unique:siswas,nis', // nis
        '3' => 'nullable|unique:siswas,nisn', // nisn
        '4' => 'required', // tempatlahir
        '5' => 'required|date', // tanggallahir
        '6' => 'required|in:L,P', // jk
        '7' => 'required', // agama
        '26' => 'required|unique:users,username', // username
        '27' => 'nullable', // password
      ];
    }

    /**
     * Custom validation messages
     */
    public function customValidationMessages(): array
    {
      return [
        '1.required' => 'Nama siswa wajib diisi.',
        '2.unique' => 'NIS :input sudah terdaftar.',
        '3.unique' => 'NISN :input sudah terdaftar.',
        '4.required' => 'Tempat lahir wajib diisi.',
        '5.required' => 'Tanggal lahir wajib diisi.',
        '5.date' => 'Format tanggal lahir tidak valid.',
        '6.required' => 'Jenis kelamin wajib diisi.',
        '6.in' => 'Jenis kelamin harus L atau P.',
        '7.required' => 'Agama wajib diisi.',
        '26.required' => 'Username wajib diisi.',
        '26.unique' => 'Username :input sudah terdaftar.',
      ];
    }

    /**
     * Handle validation failures
     */
    public function onFailure(Failure ...$failures)
    {
      foreach ($failures as $failure) {
        $this->failures[] = [
          'row' => $failure->row(),
          'attribute' => $failure->attribute(),
          'errors' => $failure->errors(),
          'values' => $failure->values(),
        ];
      }
    }

    /**
     * Get failures
     */
    public function getFailures(): array
    {
      return $this->failures;
    }

    // Tentukan baris pertama data yang akan diimpor (misalnya, baris judul tidak diimpor)
    public function startRow(): int
    {
        return 6;
    }
}
