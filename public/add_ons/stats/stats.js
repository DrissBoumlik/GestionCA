$(function () {



    $.ajax({
        url: '/getRegionsColumnJson',
        method: 'GET',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        success: function (response) {
            console.log(response);
            console.log(Array.isArray(response.regions_names));
            // $('#stats').DataTable({
            //     columns: response.regions_names,
            //     data: response.call
            // });
            var stats = $("#stats").DataTable({
                responsive: true,
                info: false,
                processing: true,
                serverSide: true,
                searching: false,
                ajax: 'getRegionsJson',
                columns: response.regions_names
                // [
                //     {
                //         data: 'id', name: 'id',
                //         render: function (data, type, full, meta) {
                //             return "<a href='/roles/" + data + "' class='align-center blue d-block'><i class='far fa-eye big-icon-fz'></i></a>";
                //         }
                //     },
                //     {data: 'name', name: 'name', class: 'capitalize'},
                //     {data: 'description', name: 'description', class: 'capitalize'},
                //     {data: 'users_count', name: 'users_count', class: 'capitalize'},
                //     {data: 'permissions_count', name: 'permissions_count', class: 'capitalize'},
                //     {
                //         data: 'id', name: 'id',
                //         render: function (data, type, full, meta) {
                //             return "<a data-role='" + data + "' class='delete-role blue pointer'>" +
                //                 "<i class='fas fa-trash-alt'></i></a>";
                //         }
                //     },
                // ]
            });

        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.log('error');
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
