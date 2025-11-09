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
        {data: 'jk', name: 'jk'},
        {data: 'kokurikuler', name: 'kokurikuler'},
      ]
    });

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
  });

  function saveKokurikuler(studentId) {
    const form = $(`#form-kokurikuler-${studentId}`);
    const studentIdVal = form.find('input[name="student_id"]').val();
    const classId = form.find('input[name="class_id"]').val();
    const tapelId = form.find('input[name="tapel_id"]').val();
    const kokurikulerData = form.find('select.kokurikuler-select');

    const kokurikuler = {};
    let hasData = false;

    kokurikulerData.each(function() {
      const subdimensionId = $(this).data('subdimension-id');
      const level = $(this).val();
      if (level) {
        hasData = true;
        kokurikuler[subdimensionId] = level;
      }
    });


    showLoader();
    const data = {
      student_id: studentIdVal,
      class_id: classId,
      tapel_id: tapelId,
      kokurikuler: kokurikuler,
      _token: $('meta[name="csrf-token"]').attr('content')
    };

    $.ajax({
      url: '{{ route('kokurikuler.store') }}',
      method: 'POST',
      data: data,
      success: function(response) {
        hideLoader();
        if (response.success) {
          successToast(response.success);
          $(`#modal-kokurikuler-${studentId}`).modal('hide');
          $('#myTableShow').DataTable().ajax.reload();
        } else if (response.failed) {
          failedToast(response.failed);
        }
      },
      error: function(xhr) {
        hideLoader();
        const errorMsg = xhr.responseJSON?.failed || 'Terjadi kesalahan saat menyimpan data';
        failedToast(errorMsg);
      }
    });
  }
</script>

