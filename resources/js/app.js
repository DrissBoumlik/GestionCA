require('./bootstrap');

let agence_code = '';
let agent_name = '';
const params = window.location.href.split('?')[1];

if (params) {
    const paramsList = params.split('&');
    for (let param of paramsList) {
        const p = param.split('=');
        if (p[0] === 'agence_code') {
            agence_code = p[1];
        }
        if (p[0] === 'agent_name') {
            agent_name = p[1];
        }
    }
}

(($) => {
    // Default Ajax Configuration
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    const select = $(document).find('#agent-code');

    select.select2({
        placeholder: 'Selectione un Agent',
        ajax: {
            url: `agents/list`,
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
                        text: d.name.toUpperCase(),
                        id: d.code
                    };
                });
                return {
                    results: data
                };
            }
        }
    });
    const newOption = new Option(agent_name.toUpperCase(), agent_name, true, true);
    console.log(newOption);
    select.append(newOption).trigger('change');
    //Events
    $(document).on('change', '#agent-code', (e) => {
        agence_code = $(e.currentTarget).val();
        window.location.href = `agents?agent_name=${agence_code}`;
    });
})(jQuery);
