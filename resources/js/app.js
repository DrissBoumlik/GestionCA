require('./bootstrap');

(($) => {
    // Default Ajax Configuration
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $(document).find('#agence-code').select2({
        placeholder: 'Selectione une Agence',
        ajax: {
            url: `agences/list`,
            dataType: 'json',
            data: function (params) {
                // Query parameters will be ?search=[term]&type=public
                return {
                    name: params.term,
                };
            },
            processResults: function (data) {
                // Transforms the top-level key of the response object from 'items' to 'results'
                data = data.map(d => {
                    return {
                        text: d.name,
                        id: d.code
                    };
                });
                return {
                    results: data
                };
            }
        }
    });

    //Events
    $(document).on('select2:close', '#agence-code', (e) => {
        agence_code = $(e.currentTarget).val();
        window.location.href = `agences?agence_code=${agence_code}`;
    });

})(jQuery);
