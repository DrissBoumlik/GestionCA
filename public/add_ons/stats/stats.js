$(function () {
    //
    // $.ajax({
    //     url: '/getRegions',
    //     method: 'GET',
    //     headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
    //     success: function (response) {
    //         console.log(response);
    //         $('#statsDemo').DataTable({
    //             columns: response.regions_names,
    //             data: response.call
    //         });
    //     },
    //     error: function (jqXHR, textStatus, errorThrown) {
    //         console.log('error');
    //     }
    // });
    //

    function feedBack(message, status) {
        swal(
            status.replace(/^\w/, c => c.toUpperCase()) + '!',
            message,
            status
        )
    }
});
