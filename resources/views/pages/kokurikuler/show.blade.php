@extends('layouts.main')

@section('css')
@endsection

@section('content')

{{-- Header --}}
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-1">
      <div class="col-sm-6">
        <h4 class="m-0 fw-bold">
          <a href="#" class="float-xs-start" onclick="history.back()">
            <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor" class="bi bi-arrow-left text-primary" viewBox="0 0 16 16" style="margin-right: 8px">
              <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z" />
            </svg>
            </a>
          Data Kokurikuler</h4>
      </div>
    </div>
  </div>
</div>

{{-- Content --}}
<section class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col">
        <div class="card">
          <div class="card-header">
            <div class="callout callout-warning my-1">
              <div class="col-md-6">
                <table>
                  <tr>
                    <td class="fw-bold">Kelas</td>
                    <td style="width: 1px;" class="px-2">:</td>
                    <td>{{ $kelas->name }}</td>
                  </tr>
                  <tr>
                    <td class="fw-bold">Wali Kelas</td>
                    <td style="width: 1px;" class="px-2">:</td>
                    <td>{{ $kelas->wali_kelas() }}</td>
                  </tr>
                  <tr>
                    <td class="fw-bold">Tahun Pelajaran</td>
                    <td style="width: 1px;" class="px-2">:</td>
                    <td>{{ $kelas->tapel->tahun_pelajaran() }}</td>
                  </tr>
                </table>
              </div>
            </div>
          </div>
          <div class="card-body">
            <div class="mb-3 d-flex flex-wrap gap-2">
              <button type="button" class="btn btn-outline-primary btn-sm mr-2" data-toggle="modal" data-target="#modal-dimensi">
                <i class="fas fa-layer-group mr-1"></i> Kelola Dimensi Profil Lulusan
              </button>
              <button type="button" class="btn btn-outline-success btn-sm" data-toggle="modal" data-target="#modal-template">
                <i class="fas fa-pen mr-1"></i> Edit Deskripsi Nilai
              </button>
            </div>
            @if ($siswa->count() < 1)
              Data masih kosong!
            @else
              <div class="table-responsive">
                <table id="myTableShow" class="table table-sm table-hover mb-0 table-striped">
                  <thead>
                  <tr class="bg-dark text-white header-table {{ Auth::user()->dark_mode == '1' ? 'bg-light' : '' }}">
                    <th scope="col">No.</th>
                    <th scope="col">Nama</th>
                    <th scope="col">NIS</th>
                    <th scope="col" style="min-width: 480px;">Nilai Tinggi</th>
                    <th scope="col" style="min-width: 480px;">Nilai Rendah</th>
                  </tr>
                  </thead>
                </table>
              </div>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

{{-- Modal Kelola Dimensi --}}
<div class="modal fade" id="modal-dimensi" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Kelola Dimensi Profil Lulusan</h5>
        <button type="button" class="close" data-dismiss="modal">
          <span>&times;</span>
        </button>
      </div>
      <div class="modal-body">
        @foreach ($dimensions as $dimension)
          <div class="card mb-3 dimension-item" data-dimension-id="{{ $dimension->id }}">
            <div class="card-header py-2">
              <strong>Dimensi #{{ $loop->iteration }}</strong>
            </div>
            <div class="card-body">
              <div class="form-group">
                <label class="small mb-1">Nama Dimensi</label>
                <input type="text" class="form-control dimension-name" value="{{ $dimension->name }}">
              </div>
              <div class="form-group mb-2">
                <label class="small mb-1">Deskripsi</label>
                <textarea class="form-control dimension-description" rows="2">{{ $dimension->description }}</textarea>
              </div>
              <button type="button" class="btn btn-primary btn-sm btn-save-dimension">
                <i class="fas fa-save mr-1"></i> Simpan Dimensi
              </button>
            </div>
          </div>
        @endforeach
      </div>
    </div>
  </div>
</div>

{{-- Modal Edit Template --}}
<div class="modal fade" id="modal-template" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Edit Deskripsi Nilai</h5>
        <button type="button" class="close" data-dismiss="modal">
          <span>&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="form-group">
          <label class="small mb-1">Template Nilai Tinggi</label>
          <textarea id="template-tinggi" class="form-control" rows="3">{{ $templates['tinggi'] ?? '' }}</textarea>
@verbatim
          <small class="text-muted">Gunakan placeholder <code>{{ student_name }}</code> dan <code>{{ list_of_descriptions }}</code>.</small>
@endverbatim
        </div>
        <div class="form-group">
          <label class="small mb-1">Template Nilai Rendah</label>
          <textarea id="template-rendah" class="form-control" rows="3">{{ $templates['rendah'] ?? '' }}</textarea>
@verbatim
          <small class="text-muted">Gunakan placeholder <code>{{ student_name }}</code> dan <code>{{ list_of_descriptions }}</code>.</small>
@endverbatim
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
        <button type="button" class="btn btn-success" id="save-template-button">
          <i class="fas fa-save mr-1"></i> Simpan Deskripsi
        </button>
      </div>
    </div>
  </div>
</div>

@endsection

@section('js')
@include('partials.toast2')
@include('pages.kokurikuler.scriptshow')
@endsection

