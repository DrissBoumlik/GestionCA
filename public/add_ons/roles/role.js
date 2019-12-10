$(function () {
    $('.delete-role').on('click', function (e) {
        e.preventDefault();
        var confirmed = confirm('Are you sure ?');
        if (confirmed) {
            role_id = $(this).attr('data-role');
            element = this;
            $.ajax({
                method: 'DELETE',
                url: window.location.origin + '/roles/' + role_id,
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                success: function (response) {
                    feedBack(response.message, 'success');
                    setTimeout(() => {
                        window.location = '/roles';
                    }, 500);
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    if (jqXHR.status === 401 || jqXHR.status === 422) {
                        feedBack(jqXHR.responseJSON.message, 'error');
                    }
                }
            });
        }
    });

    $('#permissions-data').on('change', '.data-status', function () {
        var confirmed = confirm('Are you sure ?');
        if (confirmed) {
            permission_id = $(this).attr('data-status');
            role_id = $(this).attr('data-role');
            element = this;
            status = $(this).prop('checked');
            $.ajax({
                method: 'POST',
                url: window.location.origin + '/assignPermissionRole',
                data: {status, role_id, permission_id, method: 'patch'},
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                success: function (response) {
                    feedBack(response.message, 'success');
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    if (jqXHR.status === 401 || jqXHR.status === 422) {
                        feedBack(jqXHR.responseJSON.message, 'error');
                    }
                    $(this).prop('checked', !$(element).prop('checked'));
                }
            });
        }
    });


    function feedBack(message, status) {
        swal(
            status.replace(/^\w/, c => c.toUpperCase()) + '!',
            message,
            status
        )
    }
});
