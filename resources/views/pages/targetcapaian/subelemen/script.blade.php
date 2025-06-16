<script>
  $(document).ready(function(){
    $('#myTable').dataTable({
      processing:true,
      serveside: true,
      ordering: false,
      ajax: {
        url: '/subelemen/{{ $elemen->id }}',
      },
      columns: [
        {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: true, searchable: false},
        {data: 'name', name: 'name'},
        {data: 'capaianakhir.count', name: 'capaianakhir.count'},
        {data: 'aksi', name: 'aksi'},
      ]
    });

    // SETUP CSRF
    $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // CREATE
    $('body').on('click', '.create-button', function(e) {
        e.preventDefault();
        $('#modal-create').modal('show');

        $('#store-button').off('click').click(function(s) {
          s.preventDefault();
          showLoaderAtShow();
          store();
        });
    });

    // STORE
    function store(){
      $.ajax({
        url: '{{ route('subelemen.store') }}',
        type: 'POST',
        data: {
          name: $('#create-name').val(),
          elemen_id: $('#elemen_id').val(),
        },

        success: function(response){
          hideLoaderAtShow();
          if (response.errors) {
            $('.create-field').removeClass('is-invalid');
            $('.create-field').addClass('is-valid');
            $('.invalid-feedback').html('');

            $.each(response.errors, function(field, errors) {
                $('#create-' + field).removeClass('is-valid');
                $('#create-' + field).addClass('is-invalid');
                $('#error-create-' + field).html(errors[0]);
            });

          } else if(response.failed){
            $('#modal-create').modal('hide');
            failedToast(response.failed);

          } else {
            $('#modal-create').modal('hide');
            successToast(response.success);
            $('#myTable').DataTable().ajax.reload();
            clearCreateForm();
          }
        }
      });
    }

    // SHOW
    $('body').on('click', '.show-button', function(){
      loadAMoment();
      window.location.href = '/capaianakhir/' + $(this).data('id');
    });

    // EDIT
    $('body').on('click', '.edit-button', function(e) {
        showLoader();
        var id = $(this).data('id');
        $.ajax({
          url: '/subelemen/' + id + '/edit',
          type: 'GET',
          success: function(response){
            hideLoader();
            $('#modal-edit').modal('show');

            $.each(response.dataEdit, function(field, value) {
              $('#edit-' + field).val(value);
            });

            $('#update-button').off('click').click(function(u) {
                u.preventDefault();
                showLoaderAtShow();
                update(id);
            });
          }
        })
    });

    // UPDATE
    function update(id){
      $.ajax({
        url: '/subelemen/' + id,
        type: 'PUT',
        data: {
          name: $('#edit-name').val(),
          elemen_id: $('#elemen_id').val(),
        },

        success: function(response){
          hideLoaderAtShow();
          if (response.errors) {
            $('.edit-field').removeClass('is-invalid');
            $('.edit-field').addClass('is-valid');
            $('.invalid-feedback').html('');

            $.each(response.errors, function(field, errors) {
                $('#edit-' + field).removeClass('is-valid');
                $('#edit-' + field).addClass('is-invalid');
                $('#error-edit-' + field).html(errors[0]);
            });

          } else if(response.failed){
            $('#modal-edit').modal('hide');
            failedToast(response.failed);

          } else {
            $('#modal-edit').modal('hide');
            successToast(response.success);
            $('#myTable').DataTable().ajax.reload();
            clearEditForm();
          }
        }
      });
    }

    // DELETE
    $('body').on('click', '.delete-button', function(e) {
      var id = $(this).data('id');
      var name = $(this).data('name');

      $('#delete-subelemen-name').html(name);
      $('#modal-delete').modal('show');

      $('#confirm-delete-button').off('click').click(function() {
        $('#modal-delete').modal('hide');
        showLoaderAtShow();
          $.ajax({
              url: '/subelemen/' + id,
              type: 'DELETE',
              success: function(response) {
                if(response.failed){
                  $('#modal-edit').modal('hide');
                  failedToast(response.failed);
                } else {
                  hideLoaderAtShow();
                  successToast(response.success);
                  $('#myTable').DataTable().ajax.reload();
                }
              }
          });
      });
    });


  function clearCreateForm() {
    $('#create-name').val('');
    $('.is-invalid').removeClass('is-invalid');
    $('.is-valid').removeClass('is-valid');
  }

  function clearEditForm() {
    $('#edit-name').val('');
    $('.is-invalid').removeClass('is-invalid');
    $('.is-valid').removeClass('is-valid');
  }

  });
</script>
