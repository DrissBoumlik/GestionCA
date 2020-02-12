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

frLang = {
    sEmptyTable: "Aucune donnée disponible dans le tableau",
    sInfo: "Affichage de l'élément _START_ à _END_ sur _TOTAL_ éléments",
    sInfoEmpty: "Affichage de l'élément 0 à 0 sur 0 élément",
    sInfoFiltered: "(filtré à partir de _MAX_ éléments au total)",
    sInfoPostFix: "",
    sInfoThousands: ",",
    sLengthMenu: "Afficher _MENU_ éléments",
    sLoadingRecords: "Chargement...",
    sProcessing: "Traitement...",
    sSearch: "Rechercher :",
    sZeroRecords: "Aucun élément correspondant trouvé",
    oPaginate: {
        sFirst: "Premier",
        sLast: "Dernier",
        sNext: "Suivant",
        sPrevious: "Précédent"
    },
    oAria: {
        sSortAscending: ": activer pour trier la colonne par ordre croissant",
        sSortDescending: ": activer pour trier la colonne par ordre décroissant"
    },
    select: {
        rows: {
            0: "Aucune ligne sélectionnée",
            1: "1 ligne sélectionnée",
            _: "%d lignes sélectionnées"
        }
    }
};

(($) => {
    $('#page-container').addClass('sidebar-mini');

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
            url: APP_URL + `/agents/list`,
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
    select.append(newOption).trigger('change');
    //Events
    $(document).on('change', '#agent-code', (e) => {
        agence_code = $(e.currentTarget).val();
        window.location.href = APP_URL + `/agents?agent_name=${agence_code}`;
    });
})(jQuery);
