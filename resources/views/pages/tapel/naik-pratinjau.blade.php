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
          Keputusan awal mengikuti catatan wali pada semester genap. Untuk tahun pelajaran baru, kelas tujuan dipilih dari kelas yang sudah ada di tahun ini. Siswa lulus menjadi nonaktif dan tidak masuk tapel baru. Mereka tetap bisa dicetak dari kelas lama, dan bisa dihapus dari data siswa.
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
                    <td id="tujuan-{{ $siswa->id }}">
                      @if ($jenis === 'tahun_baru' && $siswa->kelas_id)
                        <select name="kelas_tujuan[{{ $siswa->id }}]"
                                class="form-control form-control-sm kelas-tujuan {{ $barisSiswa['aksi'] === 'lulus' ? 'd-none' : '' }}"
                                data-naik="{{ $barisSiswa['default_naik'] }}"
                                data-tinggal="{{ $barisSiswa['default_tinggal'] }}"
                                {{ $barisSiswa['aksi'] === 'lulus' ? 'disabled' : 'required' }}>
                          <option value="">Pilih kelas</option>
                          @foreach ($opsi_kelas as $opsi)
                            <option value="{{ $opsi->id }}" {{ (int) $barisSiswa['kelas_tujuan_id'] === (int) $opsi->id ? 'selected' : '' }}>
                              {{ $opsi->name }} (tingkat {{ optional($opsi->tingkat)->angka }})
                            </option>
                          @endforeach
                        </select>
                        <span class="tujuan-teks {{ $barisSiswa['aksi'] === 'lulus' ? '' : 'd-none' }}">Tidak masuk tapel baru</span>
                      @else
                        {{ $barisSiswa['kelas_tujuan'] }}
                      @endif
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
    var pilih = tujuan.find('select');
    var teks = tujuan.find('.tujuan-teks');
    if (!pilih.length) {
      return;
    }
    if ($(this).val() === 'lulus') {
      pilih.addClass('d-none').prop('disabled', true).prop('required', false);
      teks.removeClass('d-none');
      return;
    }
    pilih.removeClass('d-none').prop('disabled', false).prop('required', true);
    teks.addClass('d-none');
    pilih.val(pilih.data($(this).val()) || '');
  });
</script>
@endsection
