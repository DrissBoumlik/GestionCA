$(function () {
    $('.delete-user').on('click', function (e) {
        e.preventDefault();
        var confirmed = confirm('Are you sure ?');
        if (confirmed) {
            var user_id = $(this).attr('data-user');
            $.ajax({
                method: 'DELETE',
                url: window.location.origin + '/users/' + user_id,
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                success: function (response) {
                    feedBack(response.message, 'success');
                    setTimeout(() => {
                        window.location = '/users';
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

    function feedBack(message, status) {
        swal(
            status.replace(/^\w/, c => c.toUpperCase()) + '!',
            message,
            status
        )
    }
});
