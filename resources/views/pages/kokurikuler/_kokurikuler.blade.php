@php
    $existingLevels = $siswa->kokurikulerLevels->where('tapel_id', $tapel->id)->keyBy('dimension_id');
    $existingDescription = $siswa->kokurikulerDescriptions->where('tapel_id', $tapel->id)->first();
@endphp

<div class="nilai-column" 
     data-student-id="{{ $siswa->id }}" 
     data-class-id="{{ $siswa->kelas_id }}" 
     data-tapel-id="{{ $tapel->id }}" 
     data-level-type="{{ $levelType }}">
  <div class="dimension-list overflow-auto" style="max-height: 360px;">
    @foreach ($dimensions as $dimension)
      @php
        $currentLevel = $existingLevels->get($dimension->id);
        $isChecked = $currentLevel && $currentLevel->level === $levelType;
      @endphp
      <div class="custom-control custom-checkbox mb-1">
        <input type="checkbox"
               class="custom-control-input nilai-checkbox"
               id="{{ $levelType }}-{{ $siswa->id }}-{{ $dimension->id }}"
               data-student-id="{{ $siswa->id }}"
               data-dimension-id="{{ $dimension->id }}"
               data-level="{{ $levelType }}"
               {{ $isChecked ? 'checked' : '' }}>
        <label class="custom-control-label small" for="{{ $levelType }}-{{ $siswa->id }}-{{ $dimension->id }}">
          <strong>{{ $dimension->name }}</strong>
          @if ($dimension->description)
            <br><span class="text-muted">{{ $dimension->description }}</span>
          @endif
        </label>
      </div>
    @endforeach
  </div>
</div>
