$(document).ready(function() {

    $(document).on('click', '#btn-import', function (event) {
        event.preventDefault();
        $.ajax({
            method: 'post',
            url: 'stats/import-stats',
            data: new FormData($('#form-import')[0]),
            dateType: 'json',
            processData: false,
            contentType: false,
            success: function (data) {
                $('#modal-import').modal('hide');
                type = data.success ? 'success' : 'error';
                Swal.fire({
                    // position: 'top-end',
                    type: type,
                    title: data.message,
                    showConfirmButton: false,
                    timer: 1500
                });
                tableTasks.draw(false);
            }
        });
    });
});
