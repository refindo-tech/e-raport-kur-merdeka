@extends('layouts.main')

@section('content')
<div class="content-header">
  <div class="container-fluid">
    <h4 class="m-0 fw-bold">Pratinjau naik kelas</h4>
  </div>
</div>
<section class="content">
  <div class="container-fluid">
    <div class="card">
      <div class="card-body">
        <p>
          Dari <strong>{{ $sumber->tahun_pelajaran }} / {{ $sumber->semester() }}</strong>
          ke <strong>{{ $target['tahun_pelajaran'] }} / {{ $target['semester'] == '1' ? 'Ganjil' : 'Genap' }}</strong>.
          Keputusan awal mengikuti catatan wali pada semester genap. Siswa lulus menjadi nonaktif dan tidak masuk tapel baru. Mereka tetap bisa dicetak dari kelas lama, dan bisa dihapus dari data siswa.
        </p>
        <form method="POST" action="{{ route('tapel.naik.store') }}">
          @csrf
          <input type="hidden" name="jenis" value="{{ $jenis }}">
          <div class="table-responsive">
            <table class="table table-sm table-bordered">
              <thead>
                <tr>
                  <th>Nama</th>
                  <th>NIS</th>
                  <th>Kelas sekarang</th>
                  <th>Penempatan</th>
                  <th>Kelas tujuan</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($baris as $barisSiswa)
                  @php $siswa = $barisSiswa['siswa']; @endphp
                  <tr>
                    <td>{{ $siswa->name }}</td>
                    <td>{{ $siswa->nis }}</td>
                    <td>{{ $barisSiswa['kelas_asal'] }}</td>
                    <td>
                      @if ($jenis === 'semester')
                        <input type="hidden" name="aksi[{{ $siswa->id }}]" value="tetap">
                        Tingkat tetap
                      @elseif (!$siswa->kelas_id)
                        <input type="hidden" name="aksi[{{ $siswa->id }}]" value="lulus">
                        Tidak dibawa
                      @elseif (in_array($barisSiswa['tingkat'], [6, 9, 12], true))
                        <select name="aksi[{{ $siswa->id }}]" class="form-control form-control-sm aksi-siswa" data-id="{{ $siswa->id }}">
                          <option value="lulus" {{ $barisSiswa['aksi'] === 'lulus' ? 'selected' : '' }}>Lulus, nonaktif</option>
                          <option value="tinggal" {{ $barisSiswa['aksi'] === 'tinggal' ? 'selected' : '' }}>Tinggal / tidak lulus</option>
                        </select>
                      @else
                        <select name="aksi[{{ $siswa->id }}]" class="form-control form-control-sm aksi-siswa" data-id="{{ $siswa->id }}">
                          <option value="naik" {{ $barisSiswa['aksi'] === 'naik' ? 'selected' : '' }}>Naik tingkat</option>
                          <option value="tinggal" {{ $barisSiswa['aksi'] === 'tinggal' ? 'selected' : '' }}>Tinggal di tingkat ini</option>
                        </select>
                      @endif
                    </td>
                    <td id="tujuan-{{ $siswa->id }}"
                        data-naik="{{ $siswa->kelas ? $siswa->kelas->name . ' (tingkat ' . ($barisSiswa['tingkat'] + 1) . ')' : '' }}"
                        data-tinggal="{{ $barisSiswa['kelas_asal'] }}"
                        data-tetap="{{ $barisSiswa['kelas_asal'] }}"
                        data-lulus="Tidak masuk tapel baru">
                      {{ $barisSiswa['kelas_tujuan'] }}
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
          <button type="submit" class="btn btn-primary" onclick="return confirm('Siapkan tapel baru dan aktifkan? Nilai lama tetap di tapel ini.')">Siapkan dan aktifkan</button>
          <a href="{{ route('tapel.naik') }}" class="btn btn-secondary">Kembali</a>
        </form>
      </div>
    </div>
  </div>
</section>
@endsection

@section('js')
<script>
  $('.aksi-siswa').on('change', function () {
    var tujuan = $('#tujuan-' + $(this).data('id'));
    tujuan.text(tujuan.data($(this).val()));
  });
</script>
@endsection
