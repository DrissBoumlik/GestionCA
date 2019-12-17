$(function () {
    var _dates = $('#dates');

    // InitLoad();

    function InitLoad() {
        _dates.find('option').remove().end();
        $.ajax({
            type: 'GET',
            url: '/getDates'
        }).then(function (data) {
            // create the option and append to Select2
            data.dates.forEach((date, index) => {
                console.log(date);
                var option = new Option(date.date, date.date, false, false);
                _dates.append(option).trigger('change');
            });

            // manually trigger the `select2:select` event
            // _dates.trigger({
            //     type: 'select2:select',
            //     params: {
            //         data: data.skills
            //     }
            // });
        });
    }

    function feedBack(message, status) {
        swal(
            status.replace(/^\w/, c => c.toUpperCase()) + '!',
            message,
            status
        )
    }

    function InitSelect2(maxSelectionLength = null) {
        return {
            multiple: true,
            maximumSelectionLength: maxSelectionLength,
            allowClear: true,
            width: 'resolve',
            // theme: 'classic',
            placeholder: 'Selectionner une date',
            // placeholder: {
            //     id: '0', // the value of the option
            //     text: 'Select an option'
            // },
            ajax: {
                url: '/getDates',
                type: 'GET',
                dataType: 'json',
                delay: 50,
                data: function (params) {
                    params.term = params.term === '*' ? '' : params.term;
                    return {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        searchTerm: $.trim(params.term)
                    };
                },
                processResults: function (response) {
                    let results = response.dates.map(function (date) {
                        date.text = date.date;
                        date.id = date.date;
                        return date;
                    });
                    return {
                        results: results
                    };
                },
                cache: true
            },
            templateResult: function (data, x) {
                if (data.loading) {
                    return data.text;
                }
                return data.date;
            }
        };
    }

    _dates.select2(InitSelect2());
});
