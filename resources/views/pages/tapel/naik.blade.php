@extends('layouts.main')

@section('content')
<div class="content-header">
  <div class="container-fluid">
    <h4 class="m-0 fw-bold">Siapkan semester berikutnya</h4>
  </div>
</div>
<section class="content">
  <div class="container-fluid">
    @if (session('failed'))
      <div class="alert alert-danger">{{ session('failed') }}</div>
    @endif
    <div class="card">
      <div class="card-body">
        <p>
          Tahun pelajaran berjalan:
          <strong>{{ $sumber->tahun_pelajaran }} / {{ $sumber->semester() }}</strong>.
          Biodata siswa tetap. Kelas dan pembelajaran disalin sebagai wadah kosong. Absensi dan catatan semester baru kosong. Rapor tapel ini tetap bisa dicetak dari kelas lamanya.
        </p>
        <form method="POST" action="{{ route('tapel.naik.preview') }}">
          @csrf
          <div class="form-group">
            <label for="jenis">Jenis pergantian</label>
            <select name="jenis" id="jenis" class="form-control" required>
              @if ($bolehSemester)
                <option value="semester">Semester genap, tingkat kelas tetap</option>
              @endif
              <option value="tahun_baru">Tahun pelajaran baru, siswa naik tingkat</option>
            </select>
          </div>
          <button type="submit" class="btn btn-primary">Lanjut ke pratinjau</button>
          <a href="{{ route('tapel.index') }}" class="btn btn-secondary">Batal</a>
        </form>
      </div>
    </div>
  </div>
</section>
@endsection
