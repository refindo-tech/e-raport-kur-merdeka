@extends('layouts.main')

@section('content')
<div class="content-header">
  <div class="container-fluid">
    <h4 class="m-0 fw-bold">Tapel baru aktif</h4>
  </div>
</div>
<section class="content">
  <div class="container-fluid">
    <div class="alert alert-success">
      {{ $tapel->tahun_pelajaran }} / {{ $tapel->semester() }} sekarang aktif.
      {{ $disalin }} siswa ditempatkan{{ $tinggal ? ', termasuk ' . $tinggal . ' yang tinggal' : '' }}.
      Nilai, absensi, dan catatan tapel baru masih kosong.
    </div>
    <div class="card">
      <div class="card-header">Siswa yang tidak masuk tapel baru</div>
      <div class="card-body">
        <p>
          Status mereka nonaktif. Rapor tapel lama tetap bisa dicetak dari menu cetak rapor pada kelas lama.
          Hapus dari data siswa hanya jika rapor itu tidak diperlukan lagi. Menghapus siswa menghapus akun dan seluruh nilainya.
        </p>
        @if (count($lulus) === 0)
          <p class="mb-0">Semua siswa aktif masuk tapel baru.</p>
        @else
          <table class="table table-sm table-bordered">
            <thead>
              <tr>
                <th>Nama</th>
                <th>NIS</th>
                <th>Kelas terakhir</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($lulus as $siswa)
                <tr>
                  <td>{{ $siswa->name }}</td>
                  <td>{{ $siswa->nis }}</td>
                  <td>{{ $siswa->kelas->name ?? '-' }}</td>
                </tr>
              @endforeach
            </tbody>
          </table>
        @endif
        <a href="{{ route('tapel.index') }}" class="btn btn-secondary">Ke tahun pelajaran</a>
        <a href="{{ route('siswa.index') }}" class="btn btn-outline-danger">Ke data siswa untuk menghapus yang lulus</a>
      </div>
    </div>
  </div>
</section>
@endsection
