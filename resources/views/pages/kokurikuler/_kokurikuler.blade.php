<div class="kokurikuler-container" data-student-id="{{ $siswa->id }}">
  <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#modal-kokurikuler-{{ $siswa->id }}">
    <i class="fas fa-edit"></i> Kelola Kokurikuler
  </button>
  
  <div class="mt-2">
    @php
      $kokurikulerData = $siswa->studentKokurikuler ? $siswa->studentKokurikuler->where('tapel_id', $tapel->id) : collect();
    @endphp
    @if ($kokurikulerData->count() > 0)
      <div class="small">
        <strong>Total: {{ $kokurikulerData->count() }} kokurikuler</strong>
        <ul class="list-unstyled mb-0 mt-1">
          @foreach ($kokurikulerData->take(3) as $kok)
            <li>
              {{ $kok->subdimension->dimension->name ?? '-' }} - 
              {{ $kok->subdimension->name ?? '-' }} 
              ({{ ucfirst($kok->level) }})
            </li>
          @endforeach
          @if ($kokurikulerData->count() > 3)
            <li class="text-muted">... dan {{ $kokurikulerData->count() - 3 }} lainnya</li>
          @endif
        </ul>
      </div>
    @else
      <span class="text-muted small">Belum ada data kokurikuler</span>
    @endif
  </div>
</div>

<div class="modal fade" id="modal-kokurikuler-{{ $siswa->id }}" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Kokurikuler - {{ $siswa->name }}</h5>
        <button type="button" class="close" data-dismiss="modal">
          <span>&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="form-kokurikuler-{{ $siswa->id }}">
          <input type="hidden" name="student_id" value="{{ $siswa->id }}">
          <input type="hidden" name="class_id" value="{{ $siswa->kelas_id }}">
          <input type="hidden" name="tapel_id" value="{{ $tapel->id }}">
          
          <div id="kokurikuler-list-{{ $siswa->id }}" style="max-height: 500px; overflow-y: auto;">
            @php
              $existingKokurikuler = collect();
              if ($siswa->studentKokurikuler) {
                $existingKokurikuler = $siswa->studentKokurikuler->where('tapel_id', $tapel->id)->keyBy('subdimension_id');
              }
            @endphp
            
            @foreach ($dimensions as $dimension)
              <div class="card mb-2">
                <div class="card-header py-2">
                  <h6 class="mb-0"><strong>{{ $dimension->name }}</strong></h6>
                  @if ($dimension->description)
                    <small class="text-muted">{{ $dimension->description }}</small>
                  @endif
                </div>
                <div class="card-body p-2">
                  @foreach ($dimension->subdimensions as $subdimension)
                    <div class="form-group mb-2">
                      <label class="small mb-1">{{ $subdimension->name }}</label>
                      <select name="kokurikuler[{{ $subdimension->id }}]" class="form-control form-control-sm kokurikuler-select" 
                              data-subdimension-id="{{ $subdimension->id }}"
                              data-student-id="{{ $siswa->id }}">
                        <option value="">-- Pilih Level --</option>
                        <option value="berkembang" {{ (isset($existingKokurikuler[$subdimension->id]) && $existingKokurikuler[$subdimension->id]->level == 'berkembang') ? 'selected' : '' }}>Berkembang</option>
                        <option value="cakap" {{ (isset($existingKokurikuler[$subdimension->id]) && $existingKokurikuler[$subdimension->id]->level == 'cakap') ? 'selected' : '' }}>Cakap</option>
                        <option value="mahir" {{ (isset($existingKokurikuler[$subdimension->id]) && $existingKokurikuler[$subdimension->id]->level == 'mahir') ? 'selected' : '' }}>Mahir</option>
                      </select>
                    </div>
                  @endforeach
                </div>
              </div>
            @endforeach
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
        <button type="button" class="btn btn-primary" onclick="saveKokurikuler({{ $siswa->id }})">Simpan</button>
      </div>
    </div>
  </div>
</div>

