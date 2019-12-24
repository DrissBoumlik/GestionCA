$(function () {
    var permissionsTable = $("#permissions-data").DataTable({
        responsive: true,
        info: false,
        processing: true,
        serverSide: true,
        searching: false,
        ajax: 'getPermissions',
        columns: [
            {
                data: 'id', name: 'id',
                render: function (data, type, full, meta) {
                    return "<a href='/permissions/" + data + "' class='align-center blue d-block'><i class='far fa-eye big-icon-fz'></i></a>";
                }
            },
            {data: 'name', name: 'name', class: 'capitalize'},
            {data: 'controller', name: 'controller', class: 'capitalize'},
            {data: 'method', name: 'method', class: 'capitalize'},
            {
                data: 'id', name: 'id',
                render: function (data, type, full, meta) {
                    return "<a data-permission='" + data + "' class='delete-permission blue pointer'>" +
                        "<i class='fas fa-trash-alt'></i></a>";
                }
            },
        ]
    });


    $('#permissions-data').on('click', '.delete-permission', function () {
        var confirmed = confirm('Are you sure ?');
        if (confirmed) {
            permission_id = $(this).attr('data-permission');
            element = this;
            $.ajax({
                method: 'DELETE',
                url: window.location.origin + '/permissions/' + permission_id,
                success: function (response) {
                    feedBack(response.message, 'success');
                    removeElement(element);
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    if (jqXHR.status === 401 || jqXHR.status === 422) {
                        feedBack(jqXHR.responseJSON.message, 'error');
                    }
                }
            });
        }
    });

    function removeElement(element) {
        $('#permissions-data #permission-' + permission_id).addClass('danger');
        setTimeout(() => {
            var index = element.parentNode.parentNode.rowIndex;
            // document.getElementById("users-data").deleteRow(index);
            permissionsTable.row($(this).parents('tr'))
                .remove()
                .draw();
        }, 200);
    }

    function updateStatus(element) {
        $(element).prop('checked');
    }

    function feedBack(message, status) {
        swal(
            status.replace(/^\w/, c => c.toUpperCase()) + '!',
            message,
            status
        )
    }
});
