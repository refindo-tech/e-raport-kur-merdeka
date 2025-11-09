# Dokumentasi Aplikasi E-Rapor Kurikulum Merdeka

## 1. Overview Aplikasi

**E-Rapor Kurikulum Merdeka** adalah aplikasi web berbasis Laravel 9 untuk mengelola rapor siswa sesuai dengan Kurikulum Merdeka. Aplikasi ini mengelola penilaian siswa, pencapaian tujuan pembelajaran, projek P5 (Projek Penguatan Profil Pelajar Pancasila), ekstrakurikuler, dan pencetakan rapor.

## 2. Teknologi yang Digunakan

### Backend
- **Framework**: Laravel 9.x
- **PHP**: ^8.0.2
- **Database**: MySQL
- **Package Utama**:
  - `barryvdh/laravel-dompdf`: Untuk generate PDF rapor
  - `maatwebsite/excel`: Untuk import/export data Excel
  - `yajra/laravel-datatables-oracle`: Untuk DataTables
  - `laravel/sanctum`: Untuk API authentication

### Frontend
- **UI Framework**: AdminLTE 3
- **JavaScript**: jQuery, DataTables
- **Build Tool**: Vite
- **CSS**: Bootstrap (via AdminLTE)

## 3. Struktur Database & Model

### 3.1 Model Utama

#### User Management
- **User**: Tabel utama untuk autentikasi (admin, guru, siswa)
- **Admin**: Data admin
- **Guru**: Data guru
- **Siswa**: Data siswa

#### Akademik
- **Sekolah**: Data sekolah
- **Tapel**: Tahun pelajaran (semester ganjil/genap)
- **Tingkat**: Tingkat kelas (1-12)
- **Fase**: Fase kurikulum (A, B, C, D, E, F)
- **Kelas**: Data kelas
- **Mapel**: Mata pelajaran
- **KelompokMapel**: Kelompok mata pelajaran
- **Pembelajaran**: Hubungan kelas, mapel, dan guru
- **TujuanPembelajaran (TP)**: Tujuan pembelajaran untuk setiap pembelajaran
- **PencapaianTp**: Pencapaian siswa terhadap TP (optimal/kurang)
- **NilaiAkhir**: Nilai akhir siswa per pembelajaran

#### Projek P5 (Projek Penguatan Profil Pelajar Pancasila)
- **Dimensi**: Dimensi profil pelajar Pancasila
- **Elemen**: Elemen dalam dimensi
- **SubElemen**: Sub elemen dalam elemen
- **CapaianAkhir**: Capaian akhir pembelajaran (dari CP)
- **Projek**: Projek P5
- **CapaianProjek**: Capaian akhir yang dinilai dalam projek
- **KelompokProjek**: Kelompok projek siswa
- **AnggotaKelompok**: Anggota dalam kelompok projek
- **ProjekPilihanKelompok**: Projek yang dipilih oleh kelompok
- **NilaiProjek**: Nilai projek siswa (predikat: MB, SDGB, BSH, BSB)
- **CatatanProjek**: Catatan projek siswa

#### Lainnya
- **Ekskul**: Ekstrakurikuler
- **AnggotaEkskul**: Anggota ekstrakurikuler
- **Ketidakhadiran**: Data ketidakhadiran siswa
- **CatatanWalas**: Catatan wali kelas

## 4. Sistem Autentikasi & Authorization

### 4.1 Role & Permission

Aplikasi menggunakan sistem role-based access control dengan Gates:

1. **Admin**: Akses penuh ke semua fitur
2. **Guru**: Dapat memiliki beberapa role sekaligus:
   - **Guru Mapel**: Mengajar mata pelajaran tertentu
   - **Wali Kelas**: Mengelola kelas tertentu
   - **Pembina Ekskul**: Mengelola ekstrakurikuler
   - **Koordinator P5**: Mengelola projek P5
3. **Siswa**: Hanya dapat melihat rapor sendiri

### 4.2 Authorization Gates

```php
- admin: User adalah admin
- guru: User adalah guru
- siswa: User adalah siswa
- gurumapel: User adalah guru mapel
- pembinaekskul: User adalah pembina ekstrakurikuler
- walikelas: User adalah wali kelas
- koordinatorp5: User adalah koordinator P5
```

## 5. Alur Kerja Aplikasi

### 5.1 Setup Awal (Admin)

1. **Setup Sekolah**: Input data sekolah (nama, alamat, dll)
2. **Setup Tahun Pelajaran**: Buat tahun pelajaran baru
3. **Setup Fase & Tingkat**: Konfigurasi fase kurikulum dan tingkat kelas
4. **Setup Mata Pelajaran**: Input mata pelajaran dan kelompok mapel
5. **Setup Kelas**: Buat kelas dengan wali kelas
6. **Input Data Guru**: Import atau input manual data guru
7. **Input Data Siswa**: Import atau input manual data siswa
8. **Setup Pembelajaran**: Hubungkan kelas, mapel, dan guru
9. **Setup Projek P5**: Input projek, capaian akhir, dimensi, elemen, sub elemen

### 5.2 Alur Penilaian (Guru Mapel)

1. **Input Tujuan Pembelajaran (TP)**: Guru mapel input TP untuk setiap pembelajaran
2. **Input Pencapaian TP**: 
   - Pilih TP yang sudah dicapai siswa (optimal)
   - Pilih TP yang belum dicapai siswa (kurang)
3. **Input Nilai Akhir**: Input nilai angka (1-100)
4. **Input Deskripsi Capaian**: 
   - Deskripsi capaian tinggi (dari TP optimal)
   - Deskripsi capaian rendah (dari TP kurang)

### 5.3 Alur Projek P5 (Koordinator P5)

1. **Buat Kelompok Projek**: Buat kelompok projek untuk kelas
2. **Tambah Anggota Kelompok**: Tambahkan siswa ke kelompok
3. **Pilih Projek**: Pilih projek yang akan dikerjakan kelompok
4. **Input Nilai Projek**: Input predikat (MB, SDGB, BSH, BSH) untuk setiap capaian akhir
5. **Input Catatan Projek**: Input catatan/keterangan projek

### 5.4 Alur Wali Kelas

1. **Input Ketidakhadiran**: Input data sakit, izin, tanpa keterangan
2. **Input Catatan Wali Kelas**: Input catatan untuk siswa
3. **Cetak Rapor**: Cetak rapor siswa di kelasnya

### 5.5 Alur Pembina Ekskul

1. **Input Ekstrakurikuler**: Buat data ekstrakurikuler
2. **Tambah Anggota**: Tambahkan siswa ke ekstrakurikuler
3. **Input Nilai Ekskul**: Input predikat dan deskripsi

## 6. Fitur Utama

### 6.1 Manajemen Data
- **CRUD Siswa**: Create, Read, Update, Delete data siswa
- **CRUD Guru**: Manajemen data guru
- **CRUD Kelas**: Manajemen kelas
- **CRUD Mata Pelajaran**: Manajemen mapel
- **Import Excel**: Import data siswa dan guru dari Excel
- **DataTables**: Tabel data dengan filtering dan pagination

### 6.2 Penilaian
- **Input Tujuan Pembelajaran**: Guru mapel input TP untuk pembelajaran
- **Input Pencapaian TP**: Pilih TP yang dicapai/dicapai siswa
- **Input Nilai Akhir**: Input nilai angka dan deskripsi
- **Leger Nilai**: Laporan nilai siswa per mata pelajaran

### 6.3 Projek P5
- **Manajemen Projek**: CRUD projek P5
- **Manajemen Kelompok**: Buat dan kelola kelompok projek
- **Input Nilai Projek**: Input predikat untuk setiap capaian
- **Input Catatan Projek**: Input catatan/keterangan projek

### 6.4 Ekstrakurikuler
- **Manajemen Ekskul**: CRUD ekstrakurikuler
- **Manajemen Anggota**: Tambah/hapus anggota ekskul
- **Input Nilai Ekskul**: Input predikat dan deskripsi

### 6.5 Cetak Rapor
- **Rapor Kelengkapan**: Data lengkap siswa
- **Rapor Semester**: Nilai mata pelajaran, ekskul, ketidakhadiran
- **Rapor P5**: Nilai dan catatan projek P5
- **Format PDF**: Generate rapor dalam format PDF

## 7. Struktur Konsep Kurikulum Merdeka

### 7.1 Fase
Fase adalah level capaian dalam Kurikulum Merdeka:
- **Fase A**: Kelas 1-2
- **Fase B**: Kelas 3-4
- **Fase C**: Kelas 5-6
- **Fase D**: Kelas 7-9
- **Fase E**: Kelas 10
- **Fase F**: Kelas 11-12

### 7.2 Capaian Pembelajaran (CP)
- **Dimensi**: Dimensi profil pelajar Pancasila
- **Elemen**: Elemen dalam dimensi
- **Sub Elemen**: Sub elemen dalam elemen
- **Capaian Akhir**: Capaian akhir pembelajaran (dari CP)

### 7.3 Tujuan Pembelajaran (TP)
- TP dibuat oleh guru mapel untuk setiap pembelajaran
- TP digunakan untuk menilai pencapaian siswa
- Siswa dapat mencapai TP secara optimal atau kurang

### 7.4 Penilaian
- **Nilai Angka**: 1-100
- **Deskripsi Capaian Tinggi**: Dari TP yang dicapai optimal
- **Deskripsi Capaian Rendah**: Dari TP yang belum dicapai (kurang)

### 7.5 Projek P5
- **Projek**: Projek yang harus dikerjakan siswa
- **Capaian Projek**: Capaian akhir yang dinilai dalam projek
- **Predikat**: MB (Mulai Berkembang), SDGB (Sudah Dapat Guru Bimbing), BSH (Berkembang Sesuai Harapan), BSB (Berkembang Sangat Baik)
- **Catatan Projek**: Keterangan/catatan projek

## 8. Alur Cetak Rapor

### 8.1 Rapor Kelengkapan
Berisi data lengkap siswa:
- Data pribadi
- Data orang tua
- Data wali
- Data sekolah

### 8.2 Rapor Semester
Berisi:
- Identitas siswa dan sekolah
- Nilai mata pelajaran (per kelompok mapel)
  - Nilai angka
  - Deskripsi capaian tinggi
  - Deskripsi capaian rendah
- Nilai ekstrakurikuler
- Ketidakhadiran (sakit, izin, tanpa keterangan)
- Catatan wali kelas

### 8.3 Rapor P5
Berisi:
- Identitas siswa dan sekolah
- Projek yang dikerjakan
- Nilai projek per dimensi
  - Predikat (MB, SDGB, BSH, BSB)
  - Capaian akhir
- Catatan projek

## 9. Keamanan

### 9.1 Autentikasi
- Login menggunakan username dan password
- Password di-hash menggunakan bcrypt
- Session-based authentication
- Middleware `auth` untuk proteksi route

### 9.2 Authorization
- Gate-based authorization
- Role-based access control
- Middleware untuk proteksi route berdasarkan role
- Validasi akses di controller

### 9.3 Validasi
- Validasi input di controller
- Validasi unique untuk NIS, NISN
- Validasi foreign key
- Validasi file upload

## 10. Import/Export

### 10.1 Import Siswa
- Format Excel dengan kolom tertentu
- Import data siswa dan user secara bersamaan
- Start row: 6 (skip header)

### 10.2 Import Guru
- Format Excel dengan kolom tertentu
- Import data guru dan user secara bersamaan

### 10.3 Export
- Export rapor dalam format PDF
- Menggunakan DomPDF

## 11. Struktur File Penting

### 11.1 Controllers
- `DashboardController.php`: Dashboard berdasarkan role
- `LoginController.php`: Autentikasi
- `SiswaController.php`: CRUD siswa
- `GuruController.php`: CRUD guru
- `PembelajaranController.php`: CRUD pembelajaran
- `TujuanPembelajaranController.php`: CRUD TP
- `NilaiAkhirController.php`: Input nilai akhir
- `ProjekPilihanKelompokController.php`: Manajemen projek P5
- `CetakRaportController.php`: Cetak rapor

### 11.2 Models
- `User.php`: Model user dengan relasi ke guru, admin, siswa
- `Siswa.php`: Model siswa
- `Guru.php`: Model guru dengan role detection
- `Pembelajaran.php`: Model pembelajaran
- `TujuanPembelajaran.php`: Model TP
- `NilaiAkhir.php`: Model nilai akhir
- `Projek.php`: Model projek
- `CapaianAkhir.php`: Model capaian akhir

### 11.3 Views
- `resources/views/pages/`: Halaman utama
- `resources/views/pages/dashboard/`: Dashboard
- `resources/views/pages/siswa/`: Manajemen siswa
- `resources/views/pages/nilaiakhir/`: Input nilai
- `resources/views/pages/cetakrapor/`: Cetak rapor

### 11.4 Routes
- `routes/web.php`: Route web
- `routes/api.php`: Route API (minimal)

## 12. Cara Menjalankan Aplikasi

### 12.1 Requirements
- PHP >= 8.0.2
- Composer
- Node.js & NPM
- MySQL
- Web server (Apache/Nginx) atau PHP built-in server

### 12.2 Installation
1. Clone repository
2. Install dependencies: `composer install`
3. Copy `.env.example` ke `.env`
4. Generate key: `php artisan key:generate`
5. Setup database di `.env`
6. Run migration: `php artisan migrate`
7. Run seeder (jika ada): `php artisan db:seed`
8. Install npm: `npm install`
9. Build assets: `npm run build` atau `npm run dev`

### 12.3 Running
- Development: `php artisan serve`
- Build assets: `npm run dev` (watch mode)
- Production: Build assets dengan `npm run build`

## 13. Catatan Penting

1. **Role Detection**: Role guru ditentukan berdasarkan relasi (punya pembelajaran = guru mapel, punya kelas = wali kelas, dll)
2. **Pencapaian TP**: Sistem menggunakan TP untuk generate deskripsi capaian secara otomatis
3. **Projek P5**: Projek dikelola per kelompok, bukan per individu
4. **Cetak Rapor**: Rapor di-generate sebagai PDF menggunakan DomPDF
5. **DataTables**: Banyak halaman menggunakan DataTables untuk menampilkan data dengan filtering
6. **Import Excel**: Format Excel harus sesuai dengan yang ditentukan di Import class

## 14. Kesimpulan

Aplikasi E-Rapor Kurikulum Merdeka adalah sistem yang kompleks untuk mengelola rapor siswa sesuai dengan Kurikulum Merdeka. Aplikasi ini mencakup:
- Manajemen data siswa, guru, kelas
- Penilaian berbasis TP (Tujuan Pembelajaran)
- Manajemen Projek P5
- Manajemen ekstrakurikuler
- Cetak rapor dalam format PDF

Aplikasi ini menggunakan Laravel 9 dengan struktur MVC yang rapi, authorization berbasis Gate, dan integrasi dengan library eksternal untuk PDF generation dan Excel import.

