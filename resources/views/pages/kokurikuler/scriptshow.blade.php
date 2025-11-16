<script>
  $(document).ready(function(){
    $('#myTableShow').dataTable({
      processing:true,
      serveside: true,
      ordering: false,
      searching: false,
      paginate: false,
      ajax: {
        url: '{{ route('kokurikuler.show', $kelas->id) }}',
      },
      columns: [
        {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: true, searchable: false},
        {data: 'name', name: 'name'},
        {data: 'nis', name: 'nis'},
        {data: 'nilai_tinggi', name: 'nilai_tinggi', orderable: false, searchable: false},
        {data: 'nilai_rendah', name: 'nilai_rendah', orderable: false, searchable: false},
      ]
    });

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
  });

  function collectDimensions(studentId, level) {
    return $(`.nilai-checkbox[data-student-id="${studentId}"][data-level="${level}"]:checked`)
      .map(function() { return $(this).data('dimension-id'); })
      .get();
  }

  function saveKokurikuler(studentId, showLoaderIndicator = true) {
    const columnElement = $(`.nilai-column[data-student-id="${studentId}"]`).first();
    if (!columnElement.length) return;

    const classId = columnElement.data('class-id');
    const tapelId = columnElement.data('tapel-id');

    const tinggiDimensions = collectDimensions(studentId, 'tinggi');
    const rendahDimensions = collectDimensions(studentId, 'rendah');
    const finalDescription = $(`.final-description[data-student-id="${studentId}"]`).val() || '';

    if (tinggiDimensions.length === 0 && rendahDimensions.length === 0) {
      warningToast('Pilih minimal satu dimensi dengan nilai tinggi atau rendah');
      return;
    }

    if (showLoaderIndicator) {
      showLoader();
    }

    $.ajax({
      url: '{{ route('kokurikuler.store') }}',
      method: 'POST',
      data: {
        student_id: studentId,
        class_id: classId,
        tapel_id: tapelId,
        tinggi_dimensions: tinggiDimensions,
        rendah_dimensions: rendahDimensions,
        final_description: finalDescription,
      },
      success: function(response) {
        if (showLoaderIndicator) {
          hideLoader();
        }
        if (response.success) {
          successToast(response.success);
          if (response.final_description) {
            $(`.final-description[data-student-id="${studentId}"]`).val(response.final_description);
          }
        } else if (response.failed) {
          failedToast(response.failed);
        }
      },
      error: function(xhr) {
        if (showLoaderIndicator) {
          hideLoader();
        }
        const errorMsg = xhr.responseJSON?.failed || 'Terjadi kesalahan saat menyimpan data';
        failedToast(errorMsg);
      }
    });
  }

  $(document).on('change', '.nilai-checkbox', function() {
    const studentId = $(this).data('student-id');
    const dimensionId = $(this).data('dimension-id');
    const level = $(this).data('level');

    if ($(this).is(':checked')) {
      $(`.nilai-checkbox[data-student-id="${studentId}"][data-dimension-id="${dimensionId}"]`)
        .not(this)
        .prop('checked', false);
    }

    saveKokurikuler(studentId, false);
  });

  $(document).on('click', '.save-inline-button', function() {
    const studentId = $(this).data('student-id');
    saveKokurikuler(studentId, true);
  });

  $(document).on('click', '.btn-save-dimension', function() {
    const $btn = $(this);
    const container = $btn.closest('.dimension-item');
    const dimensionId = container.data('dimension-id');
    const name = container.find('.dimension-name').val().trim();
    const description = container.find('.dimension-description').val().trim();

    if (!name || !description) {
      warningToast('Nama dan deskripsi dimensi wajib diisi');
      return;
    }

    $btn.prop('disabled', true);
    $.ajax({
      url: `{{ url('kokurikuler/dimensions') }}/${dimensionId}`,
      method: 'PUT',
      data: {
        name,
        description,
        _token: $('meta[name="csrf-token"]').attr('content')
      },
      success: function(response) {
        successToast(response.success ?? 'Dimensi berhasil disimpan');
      },
      error: function(xhr) {
        const errorMsg = xhr.responseJSON?.message || xhr.responseJSON?.failed || 'Gagal menyimpan dimensi';
        failedToast(errorMsg);
      },
      complete: function() {
        $btn.prop('disabled', false);
      }
    });
  });

  $('#save-template-button').on('click', function() {
    const tinggi = $('#template-tinggi').val().trim();
    const rendah = $('#template-rendah').val().trim();

    if (!tinggi || !rendah) {
      warningToast('Kedua template harus diisi');
      return;
    }

    const $btn = $(this).prop('disabled', true);
    $.ajax({
      url: '{{ route('kokurikuler.templates.update') }}',
      method: 'PUT',
      data: {
        template_tinggi: tinggi,
        template_rendah: rendah,
        _token: $('meta[name="csrf-token"]').attr('content')
      },
      success: function(response) {
        successToast(response.success ?? 'Template berhasil disimpan');
      },
      error: function(xhr) {
        const errorMsg = xhr.responseJSON?.message || xhr.responseJSON?.failed || 'Gagal menyimpan template';
        failedToast(errorMsg);
      },
      complete: function() {
        $btn.prop('disabled', false);
      }
    });
  });
</script>

