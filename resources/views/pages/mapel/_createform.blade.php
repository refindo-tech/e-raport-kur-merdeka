<div class="modal" id="modal-create">
  <div class="modal-dialog modal-dialog-scrollable">
      <div class="modal-content">
          <div class="modal-header">
              <h5 class="modal-title fw-bold ">Tambah Data Mapel</h5>
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
                <label for="name" class="col-sm-3 col-form-label">Nama Mapel @include('partials._wajib')</label>
                <div class="col-sm-9">
                  <input type="text" name="name" value="" class="form-control create-field" id="create-name" placeholder="Ketik Nama Mapel">
                  <small class="text-danger invalid-feedback" id="error-create-name"></small>
                </div>
              </div>
              <div class="form-group row">
                <label for="singkatan" class="col-sm-3 col-form-label">Singkatan @include('partials._wajib')</label>
                <div class="col-sm-9">
                  <input type="text" name="singkatan" value="" class="form-control create-field" id="create-singkatan" placeholder="Ketik Nama Mapel">
                  <small class="text-danger invalid-feedback" id="error-create-singkatan"></small>
                </div>
              </div>
              <div class="form-group row">
                <label for="kelompok_mapel_id" class="col-sm-3 col-form-label">Kelompok Mapel @include('partials._wajib')</label>
                <div class="col-sm-9">
                  <select name="kelompok_mapel_id" id="create-kelompok_mapel_id" class="form-control create-field" data-width="100%" required>
                    <option selected disabled hidden>-- Pilih --</option>
                    @foreach ($kelompokMapel as $item)
                      <option value="{{ $item->id }}" {{ old('kelompok_mapel_id') == $item->id ? 'selected' : '' }}>{{ $item->name }}</option>
                    @endforeach
                  </select>
                  <small class="text-danger invalid-feedback" id="error-create-kelompok_mapel_id"></small>
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
