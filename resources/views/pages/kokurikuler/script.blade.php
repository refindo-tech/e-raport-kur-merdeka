<script>
  $(document).ready(function(){
    $('#myTable').dataTable({
      processing:true,
      serveside: true,
      ordering: false,
      searching: false,
      paginate: false,
      ajax: {
        url: '{{ route('kokurikuler.index') }}',
        data: function(d) {
          d.tingkat_id = $('#tingkat_id_select').val();
        },
      },
      columns: [
        {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: true, searchable: false},
        {data: 'name', name: 'name'},
        {data: 'guru.name', name: 'guru.name'},
        {data: 'tingkat.angka', name: 'tingkat.angka'},
        {data: 'siswa.count', name: 'siswa.count'},
        {data: 'aksi', name: 'aksi'},
      ]
    });

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('body').on('click', '#filter-button', function(){
      $('#myTable').DataTable().ajax.reload();
      $('#modal-filter').modal('hide');
      toast('Filter berhasil diterapkan');
    });

    $('body').on('click', '.show-button', function() {
      loadAMoment();
      window.location.href = "kokurikuler/" + $(this).data('id');
    });
  });
</script>

