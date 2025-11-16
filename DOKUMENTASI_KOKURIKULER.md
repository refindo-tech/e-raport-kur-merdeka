# Dokumentasi Modul Kokurikuler
## E-Rapor Kurikulum Merdeka

---
By: Abdul Majid Refindo

## Daftar Isi
1. [Pengenalan](#pengenalan)
2. [Akses Modul](#akses-modul)
3. [Mengisi Data Kokurikuler](#mengisi-data-kokurikuler)
4. [Melihat Data di Rapor](#melihat-data-di-rapor)
5. [Penjelasan Dimensi dan Level](#penjelasan-dimensi-dan-level)
6. [Tips dan Best Practices](#tips-dan-best-practices)
7. [FAQ](#faq)

---

## Pengenalan

Modul Kokurikuler adalah fitur yang memungkinkan Wali Kelas menilai perkembangan peserta didik pada 8 dimensi Profil Pelajar Pancasila. Sistem disederhanakan menjadi 2 level penilaian per dimensi: **Nilai Tinggi** dan **Nilai Rendah**, dengan deskripsi otomatis berbasis template yang tetap bisa diedit manual per siswa.

### Tujuan Modul Kokurikuler
- Menilai perkembangan kompetensi siswa di luar pembelajaran intrakurikuler
- Memberikan deskripsi perkembangan siswa berdasarkan dimensi kompetensi
- Mengintegrasikan penilaian kokurikuler ke dalam rapor semester

### Dimensi Kompetensi
Modul ini mencakup 8 dimensi kompetensi:
1. **Keimanan** - Kemampuan memahami, menghayati, dan mengamalkan ajaran agama
2. **Kewargaan** - Kemampuan memahami hak dan kewajiban sebagai warga negara
3. **Penalaran Kritis** - Kemampuan berpikir kritis dan logis dalam menyelesaikan masalah
4. **Kreativitas** - Kemampuan menciptakan ide-ide baru dan inovatif
5. **Kolaborasi** - Kemampuan bekerja sama dalam tim
6. **Kemandirian** - Kemampuan bekerja mandiri dan bertanggung jawab
7. **Kesehatan** - Kemampuan menjaga kesehatan fisik dan mental
8. **Komunikasi** - Kemampuan berkomunikasi secara efektif

---

## Akses Modul

### Persyaratan
- User harus memiliki role **Wali Kelas**
- User harus sudah memiliki kelas yang diampu
- Kelas harus memiliki siswa aktif

### Langkah Mengakses
1. Login ke sistem E-Rapor Kurikulum Merdeka
2. Pastikan Anda sudah login sebagai **Wali Kelas**
3. Di sidebar menu, klik menu **DATA-DATA**
4. Pilih **Kokurikuler**
5. Anda akan melihat daftar kelas yang diampu

### Tampilan Halaman Index
- **No.** - Nomor urut
- **Nama Kelas** - Nama kelas yang diampu
- **Wali Kelas** - Nama wali kelas
- **Tingkat** - Tingkat kelas (1-12)
- **Jumlah Siswa** - Jumlah siswa aktif di kelas
- **Aksi** - Tombol "Kelola" untuk masuk ke halaman pengisian per kelas

### Filter Data
1. Klik tombol **Filter Data** di pojok kanan atas
2. Pilih **Tingkat Kelas** yang ingin ditampilkan
3. Klik **Terapkan** untuk memfilter data

---

## Mengisi Data Kokurikuler

### Langkah-langkah Mengisi Data

#### 1. Membuka Halaman Kelola Kokurikuler
1. Di halaman index kokurikuler, klik tombol **Kelola** pada kelas yang ingin dikelola.
2. Di bagian atas halaman, tersedia:
   - Tombol **Kelola Dimensi Profil Lulusan** untuk mengubah nama/uraian dimensi.
   - Tombol **Edit Deskripsi Nilai** untuk mengubah template deskripsi Nilai Tinggi/Rendah.

#### 2. Mengisi Kokurikuler untuk Setiap Siswa
1. Pada tabel siswa tersedia dua kolom:
   - **Nilai Tinggi**: daftar checkbox 8 dimensi.
   - **Nilai Rendah & Deskripsi**: daftar checkbox 8 dimensi dan textarea deskripsi akhir.
2. Centang dimensi pada kolom yang sesuai. Untuk satu dimensi, hanya salah satu yang dapat dipilih (mutually exclusive).
3. Sunting teks pada textarea deskripsi bila diperlukan.

#### 3. Level Penilaian
Hanya ada dua level per dimensi:
- **Nilai Tinggi**: dimensi menonjol/kuat pada siswa.
- **Nilai Rendah**: dimensi masih membutuhkan penguatan.

#### 4. Menyimpan Data
- Perubahan checkbox disimpan otomatis.
- Perubahan deskripsi disimpan dengan menekan tombol **Simpan Deskripsi**.

### Format Deskripsi Otomatis
Deskripsi akhir dibangkitkan dari dua template yang dapat diedit pada tombol **Edit Deskripsi Nilai**:
- Template **Nilai Tinggi**
- Template **Nilai Rendah**

Template mendukung placeholder:
- `{{ student_name }}` → nama siswa
- `{{ list_of_descriptions }}` → daftar dimensi yang dipilih (dipisah koma, dengan “dan” di akhir)

Contoh default:
- Nilai Tinggi: “Ananda {{ student_name }} sudah baik dalam {{ list_of_descriptions }}.”
- Nilai Rendah: “Ananda {{ student_name }} perlu bantuan dalam {{ list_of_descriptions }}.”

Jika keduanya diisi, sistem akan menggabungkan menjadi satu paragraf pada kolom deskripsi dan dapat Anda sunting secara manual.

### Contoh Pengisian
**Contoh 1: Nilai Tinggi pada Kreativitas dan Komunikasi**
- Centang “Kreativitas” dan “Komunikasi” di kolom Nilai Tinggi.
- Deskripsi otomatis: “Ananda Budi sudah baik dalam kreativitas dan komunikasi.”

**Contoh 2: Nilai Rendah pada Kolaborasi**
- Centang “Kolaborasi” di kolom Nilai Rendah.
- Deskripsi otomatis: “Ananda Siti perlu bantuan dalam kolaborasi.”

### Mengedit Data Kokurikuler
- Ubah centang pada kolom **Nilai Tinggi/Rendah** (tersimpan otomatis).
- Sunting teks deskripsi lalu klik **Simpan Deskripsi**.

### Menghapus Data Kokurikuler
- Hilangkan semua centang pada kedua kolom.
- Kosongkan teks deskripsi lalu klik **Simpan Deskripsi** (opsional).

---

## Melihat Data di Rapor

### Lokasi Data Kokurikuler di Rapor
Data kokurikuler akan muncul di **Rapor Semester** pada halaman kedua, setelah section **Ekstrakurikuler** dan sebelum section **Catatan Wali Kelas**.

### Format Tampilan di Rapor
Data kokurikuler ditampilkan sebagai satu paragraf deskripsi gabungan:
- **Judul**: "KOKURIKULER"
- **Isi**: Deskripsi akhir dari halaman pengisian (kolom “Nilai Rendah & Deskripsi”).

### Contoh Tampilan di Rapor
```
KOKURIKULER
Ananda Budi sudah baik dalam kreativitas dan komunikasi. Ananda Budi perlu bantuan dalam kolaborasi.
```

### Mencetak Rapor dengan Kokurikuler
1. Akses menu **Cetak Rapor**
2. Pilih kelas yang ingin dicetak
3. Pilih siswa yang ingin dicetak rapor
4. Klik tombol **Semester** (ikon printer)
5. Rapor akan dicetak dengan section kokurikuler yang sudah diisi

### Catatan Penting
- Data kokurikuler hanya akan muncul di rapor jika sudah diisi.
- Deskripsi di rapor mengikuti teks pada field “Deskripsi” di halaman pengisian.
- Template dapat disesuaikan melalui tombol **Edit Deskripsi Nilai**.

---

## Penjelasan Dimensi dan Level

### Daftar Dimensi
Modul menggunakan 8 dimensi Profil Pelajar Pancasila:
1) Keimanan  2) Kewargaan  3) Penalaran Kritis  4) Kreativitas  5) Kolaborasi  6) Kemandirian  7) Kesehatan  8) Komunikasi.

### Level Penilaian
Hanya ada dua level:
- **Nilai Tinggi**: dimensi menonjol/kuat pada siswa.
- **Nilai Rendah**: dimensi membutuhkan penguatan/pembinaan.

Catatan: Detail indikator dapat dituangkan pada teks deskripsi atau disesuaikan melalui template.

---

## Tips dan Best Practices

### 1. Penilaian yang Objektif
- Gunakan data observasi yang nyata saat menilai siswa
- Hindari penilaian subjektif atau bias
- Konsultasikan dengan guru mata pelajaran jika diperlukan

### 2. Konsistensi Penilaian
- Gunakan kriteria yang sama untuk semua siswa
- Evaluasi secara berkala untuk melihat perkembangan
- Update data kokurikuler setiap semester

### 3. Komunikasi dengan Siswa
- Jelaskan kepada siswa tentang penilaian kokurikuler
- Berikan feedback yang konstruktif
- Dorong siswa untuk meningkatkan kemampuan mereka

### 4. Dokumentasi
- Simpan catatan observasi untuk referensi
- Dokumentasikan perkembangan siswa dari waktu ke waktu
- Gunakan data kokurikuler untuk perencanaan pembelajaran

### 5. Kolaborasi dengan Guru Lain
- Diskusikan penilaian dengan guru mata pelajaran
- Minta masukan dari guru lain tentang perkembangan siswa
- Gunakan data dari berbagai sumber untuk penilaian yang lebih akurat

### 6. Waktu Pengisian
- Isi data kokurikuler secara berkala, tidak sekaligus di akhir semester
- Update data setelah kegiatan kokurikuler tertentu
- Review dan update data sebelum pencetakan rapor

---

## FAQ

### Q: Apakah semua siswa harus memiliki data kokurikuler?
**A**: Tidak. Data kokurikuler bersifat opsional. Anda dapat mengisi data kokurikuler hanya untuk siswa yang sudah dinilai. Data kokurikuler hanya akan muncul di rapor jika sudah diisi.

### Q: Bisakah saya mengubah data kokurikuler setelah disimpan?
**A**: Ya. Ubah centang pada tabel (tersimpan otomatis) atau sunting deskripsi lalu klik **Simpan Deskripsi**.

### Q: Apakah data kokurikuler berbeda untuk setiap semester?
**A**: Ya. Data kokurikuler terikat pada tahun pelajaran (tapel) dan semester. Setiap semester, Anda perlu mengisi data kokurikuler baru untuk siswa.

### Q: Bagaimana jika saya tidak memilih level untuk sebuah dimensi?
**A**: Dimensi yang tidak dipilih tidak akan muncul pada deskripsi rapor. Anda bisa memilihnya kapan saja.

### Q: Bisakah saya menghapus data kokurikuler?
**A**: Ya. Hilangkan semua centang pada kedua kolom dan kosongkan deskripsi lalu klik **Simpan Deskripsi**.

### Q: Apakah deskripsi kokurikuler dapat diubah secara manual?
**A**: Ya. Deskripsi akhir dapat diedit per siswa. Sistem tetap membangkitkan deskripsi awal dari template.

### Q: Bagaimana jika saya ingin mengubah/menambah nama dimensi?
**A**: Gunakan tombol **Kelola Dimensi Profil Lulusan**. Perubahan mempengaruhi daftar checkbox pada tabel.

### Q: Apakah data kokurikuler dapat diekspor?
**A**: Saat ini, data kokurikuler hanya dapat dilihat melalui sistem dan dicetak melalui rapor. Fitur ekspor data dapat ditambahkan di versi selanjutnya.

### Q: Bagaimana cara melihat riwayat perkembangan kokurikuler siswa?
**A**: Saat ini, sistem menampilkan data kokurikuler per semester. Untuk melihat perkembangan, bandingkan data kokurikuler dari semester ke semester.

### Q: Apakah ada batasan jumlah subdimensi yang dapat dipilih per siswa?
**A**: Tidak ada batasan. Anda dapat memilih level untuk semua subdimensi yang tersedia atau hanya beberapa subdimensi yang relevan.
