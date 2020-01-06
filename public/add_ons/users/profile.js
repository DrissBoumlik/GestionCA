$(function () {
    $('.delete-user').on('click', function (e) {
        e.preventDefault();
        var confirmed = confirm('Are you sure ?');
        if (confirmed) {
            var user_id = $(this).attr('data-user');
            $.ajax({
                method: 'DELETE',
                url: APP_URL + '/users/' + user_id,
                success: function (response) {
                    feedBack(response.message, 'success');
                    setTimeout(() => {
                        window.location = APP_URL + '/users';
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


    $(document).on('change', '#role', (e) => {
        role = $(e.currentTarget).val();
        if (role == 2) {
            $('#agence_name').show();
        } else {
            $('#agence_name').hide();
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
