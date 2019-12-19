$(function () {
    var stats = $("#stats").DataTable({
        responsive: true,
        info: false,
        processing: true,
        serverSide: true,
        searching: false,
        ajax: 'getStats',
        columns: [
            // {data: 'Resultat_Appel', name: 'Resultat_Appel'},
            // {data: 'Nom_Region', name: 'Nom_Region'},
            // {data: 'percent', name: 'percent'},
        ]
    });
    let element;
    // $('#users-data').on('click', '.delete-user', function () {
    //     var confirmed = confirm('Are you sure ?');
    //     if (confirmed) {
    //         user_id = $(this).attr('data-user');
    //         element = this;
    //         sendRequest($(this), 'DELETE', '/users/' + user_id);
    //     }
    // });
    //
    // $('#users-data').on('change', '.data-status', function () {
    //     var _this = $(this);
    //     var status = $(_this).prop('checked');
    //     var confirmed = confirm('Are you sure ?');
    //     if (confirmed) {
    //         user_id = $(_this).attr('data-status');
    //         sendRequest($(this), 'PUT', '/changeStatus/' + user_id, {status, method: 'patch'}, true);
    //     } else {
    //         $(_this).prop('checked', !status);
    //     }
    // });
    //
    // function sendRequest(_this, method, route, data = null, toggleCheck = false, reload = false) {
    //     var baseUrl = window.location.origin;
    //     $.ajax({
    //         method: method,
    //         url: baseUrl + route,
    //         data: data,
    //         headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
    //         success: function (response) {
    //             feedBack(response.message, 'success');
    //             if (method === 'DELETE') {
    //                 removeElement(element);
    //             }
    //         },
    //         error: function (jqXHR, textStatus, errorThrown) {
    //             if (jqXHR.status === 401 || jqXHR.status === 422) {
    //                 feedBack(jqXHR.responseJSON.message, 'error');
    //             }
    //             if (method === 'PUT') {
    //                 $(_this).prop('checked', !$(element).prop('checked'));
    //             }
    //         }
    //     });
    // }

    function removeElement(element) {
        $('#users-data #user-' + user_id).addClass('danger');
        setTimeout(() => {
            var index = element.parentNode.parentNode.rowIndex;
            // document.getElementById("users-data").deleteRow(index);
            usersTable.row($(this).parents('tr'))
                .remove()
                .draw();
        }, 200);
    }

    function feedBack(message, status) {
        swal(
            status.replace(/^\w/, c => c.toUpperCase()) + '!',
            message,
            status
        )
    }
});
