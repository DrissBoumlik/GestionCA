let data = $('#data-request').data('request');
$('#agents').DataTable({
    language: frLang,
    pageLength: 10,
    processing: true,
    serverSide: true,
    ajax: {
        type: 'GET',
        url: APP_URL + `/agents/get-data`,
        // data: data
    },
    columns: [
        {data: 'pseudo', name: 'pseudo', title: 'Pseudo'},
        {data: 'fullName', name: 'fullName', title: 'Nom Complet'},
        {data: 'hours', name: 'hours', title: 'Heures'},
        {data: 'imported_at', name: 'imported_at', title: 'Date'},
    ]
});
