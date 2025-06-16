<div class="modal" id="modal-create">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
      <div class="modal-content">
          <div class="modal-header">
              <h5 class="modal-title fw-bold ">Tambah Data Kelas</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
              </button>
          </div>
            <div class="modal-body">

              <div class="alert alert-info alert-dismissible fade show" role="alert" id="alert-info">
                * adalah kolom yang wajib diisi!
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>

              <div class="alert alert-warning alert-dismissible fade d-none" role="alert" id="create-confirm-alert">
                Harap centang kotak konfirmasi sebelum melanjutkan!
              </div>

              <div class="form-group row">
                <label for="name" class="col-sm-3 col-form-label">Nama Kelas @include('partials._wajib')</label>
                <div class="col-sm-9">
                  <input type="text" name="name" value="" class="form-control create-field" id="create-name" placeholder="Ketik Nama Kelas">
                  <small class="text-danger invalid-feedback" id="error-create-name"></small>
                </div>
              </div>
              <div class="form-group row">
                <label for="guru_id" class="col-sm-3 col-form-label">Wali Kelas</label>
                <div class="col-sm-9">
                  <select name="guru_id" id="create-guru_id" class="form-control create-field" data-width="100%">
                    <option selected value="">-- Pilih --</option>
                    @foreach ($guru as $item)
                      <option value="{{ $item->id }}" {{ old('guru_id') == $item->id ? 'selected' : '' }}>{{ $item->name }}</option>
                    @endforeach
                  </select>
                  @error('guru_id') <span class="invalid-feedback mt-1">{{ $message }}</span> @enderror
                  <small class="text-danger invalid-feedback" id="error-create-guru_id"></small>
                </div>
              </div>
              <div class="form-group row">
                <label for="tapel_id" class="col-sm-3 col-form-label">Tahun Pelajaran @include('partials._wajib')</label>
                <div class="col-sm-9">
                  <select name="tapel_id" id="create-tapel_id" class="form-control create-field" data-width="100%">
                    <option selected disabled hidden>-- Pilih --</option>
                    @foreach ($tapel as $item)
                      <option value="{{ $item->id }}" {{ old('tapel_id') == $item->id ? 'selected' : '' }}>{{ $item->tahun_pelajaran }} - {{ $item->semester == '1' ? 'Ganjil' : 'Genap' }}</option>
                    @endforeach
                  </select>
                  <small class="text-danger invalid-feedback" id="error-create-tapel_id"></small>
                </div>
              </div>
              <div class="form-group row">
                <label for="tingkat_id" class="col-sm-3 col-form-label">Tingkat @include('partials._wajib')</label>
                <div class="col-sm-9">
                  <select name="tingkat_id" id="create-tingkat_id" class="form-control create-field" data-width="100%" required>
                    <option selected disabled hidden>-- Pilih --</option>
                    @foreach ($tingkat as $item)
                      <option value="{{ $item->id }}" {{ old('tingkat_id') == $item->id ? 'selected' : '' }}>{{ $item->angka }}</option>
                    @endforeach
                  </select>
                  <small class="text-danger invalid-feedback" id="error-create-tingkat_id"></small>
                </div>
              </div>
            </div>
            <div class="modal-footer justify-content-between">
              <div class="form-check">
                <input type="checkbox" class="form-check-input" id="store-confirm" required>
                <label class="form-check-label" for="store-confirm">Saya yakin sudah mengisi dengan benar</label>
              </div>
              <button type="button" class="btn btn-primary" id="store-button">Simpan</button>
          </div>
      </div>
  </div>
</div>
