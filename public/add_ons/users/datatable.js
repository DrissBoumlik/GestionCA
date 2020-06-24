$(function () {
    // var usersTable = $("#users-data").DataTable();
// $('#users-data').DataTable({
//     "paging": true,
//     "lengthChange": true, //false,
//     "searching": true, //false,
//     "ordering": true,
//     "info": true,
//     "autoWidth": true, //false,
// });
    var usersTable = $("#users-data").DataTable({
        language: frLang,
        responsive: true,
        info: false,
        processing: true,
        serverSide: true,
        searching: false,
        ajax: 'getUsers',
        columns: [
            // {
            //     data: 'id', name: 'id', orderable: false,
            //     render: function (data, type, full, meta) {
            //         return "<a href='" + APP_URL + "/users/" + data + "' class='align-center blue d-block'><i class='far fa-edit big-icon-fz'></i></a>";
            //     }
            // },
            {
                data: 'picture', name: 'picture', title: 'Image', orderable: false,
                render: function (data, type, full, meta) {
                    return "<div class='align-center'><img src='" + data + "' height=50 class='round'/></div>";
                },
            },
            {data: 'firstname', name: 'firstname', title: 'Prénom', class: 'capitalize'},
            {data: 'lastname', name: 'lastname', title: 'Nom', class: 'capitalize'},
            // {data: 'gender', name: 'gender', title: 'Genre'},
            {data: 'email', name: 'email', title: 'Email', class: 'capitalize'},
            {data: 'role', name: 'role', title: 'Rôle', class: 'capitalize'},
            {
                data: 'status', name: 'status', title: 'Etat',
                render: function (data, type, full, meta) {
                    return "<label for='status-" + full.id + "' class='m-0'>" +
                        "<input class='data-status d-none change-status' data-status='" + full.id + "' " +
                        "id='status-" + full.id + "' type='checkbox'" +
                        (data ? 'checked' : '') +
                        " name='status'>" +
                        "<span class='status pointer'></span>" +
                        "</label>";
                }
            },
            {
                data: 'id', name: 'id', title: 'Options', orderable: false,
                render: function (data, type, full, meta) {
                    return "<div class='options'>" +
                        "<a href='" + APP_URL + "/users/" + data + "' class='align-center blue d-inline-block'>" +
                        "<i class='far fa-edit big-icon-fz'></i></a>" +
                        "<a data-user='" + data + "' class='delete-user blue pointer align-center d-inline-block'>" +
                        "<i class='fas fa-trash-alt big-icon-fz'></i></a>" +
                        "</div>";
                }
            },
        ],
        // order: [[0, "desc"]]
    });
    let element;
    $('#users-data').on('click', '.delete-user', function () {
        var confirmed = confirm('Are you sure ?');
        if (confirmed) {
            user_id = $(this).attr('data-user');
            element = this;
            sendRequest($(this), 'DELETE', APP_URL + '/users/' + user_id);
        }
    });

    $('#users-data').on('change', '.data-status', function () {
        var _this = $(this);
        var status = $(_this).prop('checked');
        var confirmed = confirm('Are you sure ?');
        if (confirmed) {
            user_id = $(_this).attr('data-status');
            sendRequest($(this), 'PUT', `${APP_URL}/changeStatus/${user_id}`, {status, method: 'patch'}, true);
        } else {
            $(_this).prop('checked', !status);
        }
    });

    function sendRequest(_this, method, route, data = null, toggleCheck = false, reload = false) {
        $.ajax({
            method: method,
            url: route,
            data: data,
            success: function (response) {
                feedBack(response.message, 'success');
                if (method === 'DELETE') {
                    removeElement(element);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                if (jqXHR.status === 401 || jqXHR.status === 422) {
                    feedBack(jqXHR.responseJSON.message, 'error');
                }
                if (method === 'PUT') {
                    $(_this).prop('checked', !$(element).prop('checked'));
                }
            }
        });
    }

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
