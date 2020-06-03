$(document).ready(function () {

    let dates = undefined;
    $.ajax({
        url: APP_URL + '/dates',
        method: 'GET',
        success: function (response) {
            let treeData = response.dates;

            $('.tree-view').each(function (index, item) {
                let treeId = '#' + $(this).attr('id');
                new Tree(treeId, {
                    data: [{id: '-1', text: 'Dates', children: treeData}],
                    closeDepth: 1,
                    loaded: function () {
                    },
                    onChange: function () {
                        dates = this.values;
                    }
                });
            });
            // if (datesFilterListExist && datesFilterValuesExist) {
            //     assignFilter(datesFilterList, datesFilterValues);
            // }
            $('.treejs-node .treejs-nodes .treejs-switcher').click();
            $('.refresh-form button').removeClass('d-none');
        },
        error: function (jqXHR, textStatus, errorThrown) {
        }
    });

    let data = $('#data-request').data('request');

    let stats = {
        element: 'stats',
        elementDT: undefined
    };
    stats.elementDT = InitDataTable(stats, data);
    let refreshBtn = $('#refreshAllStats');
    refreshBtn.on('click', function () {
        if (dates) {
            data.dates = dates.join(',');
        }
        stats.elementDT = InitDataTable(stats, data);
    });

    function InitDataTable(object, data) {
        toggleLoader($('#refreshAllStats').parents('.col-12'));
        let table = $('#' + object.element);
        // if ($.fn.DataTable.isDataTable(table)) {
        //     debugger
        //     table.DataTable().destroy();
        //     let tableID = table.attr('id');
        //     let tableParent = table.parents('.card-body');
        //     table.remove();
        //     $('#' + tableID + '_wrapper').remove();
        //     let newTable = '<table id="' + tableID + '" class="table table-bordered table-striped table-valign-middle capitalize">' +
        //         '<thead></thead><tbody></tbody></table>';
        //     tableParent.append(newTable);
        //     table = $('#' + object.element);
        // }
        table.DataTable().destroy();
        return table.DataTable({

            destroy: true,
            responsive: true,
            info: false,
            searching: true,

            language: frLang,
            pageLength: 10,
            processing: true,
            serverSide: true,
            type: 'POST',
            ajax: {
                type: 'POST',
                url: APP_URL + `/stats/get-stats`,
                data: data
            },
            columns: [
                {
                    data: 'Type_Note',
                    name: 'Type_Note',
                    title: 'Type_Note'
                },
                {
                    data: 'Utilisateur',
                    name: 'Utilisateur',
                    title: 'Utilisateur'
                },
                {
                    data: 'Resultat_Appel',
                    name: 'Resultat_Appel',
                    title: 'Resultat_Appel'
                },
                {
                    data: 'Date_Nveau_RDV',
                    name: 'Date_Nveau_RDV',
                    title: 'Date_Nveau_RDV'
                },
                {
                    data: 'Heure_Nveau_RDV',
                    name: 'Heure_Nveau_RDV',
                    title: 'Heure_Nveau_RDV'
                },
                {
                    data: 'Marge_Nveau_RDV',
                    name: 'Marge_Nveau_RDV',
                    title: 'Marge_Nveau_RDV'
                },
                {
                    data: 'Id_Externe',
                    name: 'Id_Externe',
                    title: 'Id_Externe'
                },
                {
                    data: 'Date_Creation',
                    name: 'Date_Creation',
                    title: 'Date_Creation'
                },
                {
                    data: 'Code_Postal_Site',
                    name: 'Code_Postal_Site',
                    title: 'Code_Postal_Site'
                },
                {
                    data: 'Drapeaux',
                    name: 'Drapeaux',
                    title: 'Drapeaux'
                },
                {
                    data: 'Code_Type_Intervention',
                    name: 'Code_Type_Intervention',
                    title: 'Code_Type_Intervention'
                },
                {
                    data: 'Date_Rdv',
                    name: 'Date_Rdv',
                    title: 'Date_Rdv'
                },
                {
                    data: 'Nom_Societe',
                    name: 'Nom_Societe',
                    title: 'Nom_Societe'
                },
                {
                    data: 'Nom_Region',
                    name: 'Nom_Region',
                    title: 'Nom_Region'
                },
                {
                    data: 'Nom_Domaine',
                    name: 'Nom_Domaine',
                    title: 'Nom_Domaine'
                },
                {
                    data: 'Nom_Agence',
                    name: 'Nom_Agence',
                    title: 'Nom_Agence'
                },
                {
                    data: 'Nom_Activite',
                    name: 'Nom_Activite',
                    title: 'Nom_Activite'
                },
                {
                    data: 'Date_Heure_Note',
                    name: 'Date_Heure_Note',
                    title: 'Date_Heure_Note'
                },
                {
                    data: 'Date_Heure_Note_Annee',
                    name: 'Date_Heure_Note_Annee',
                    title: 'Date_Heure_Note_Annee'
                },
                {
                    data: 'Date_Heure_Note_Mois',
                    name: 'Date_Heure_Note_Mois',
                    title: 'Date_Heure_Note_Mois'
                },
                {
                    data: 'Date_Heure_Note_Semaine',
                    name: 'Date_Heure_Note_Semaine',
                    title: 'Date_Heure_Note_Semaine'
                },
                {
                    data: 'Date_Note',
                    name: 'Date_Note',
                    title: 'Date_Note'
                },
                {
                    data: 'Groupement',
                    name: 'Groupement',
                    title: 'Groupement'
                },
                {
                    data: 'key_Groupement',
                    name: 'key_Groupement',
                    title: 'key_Groupement'
                },
                {
                    data: 'Gpmt_Appel_Pre',
                    name: 'Gpmt_Appel_Pre',
                    title: 'Gpmt_Appel_Pre'
                },
                {
                    data: 'Code_Intervention',
                    name: 'Code_Intervention',
                    title: 'Code_Intervention'
                },
                {
                    data: 'EXPORT_ALL_Nom_SITE',
                    name: 'EXPORT_ALL_Nom_SITE',
                    title: 'EXPORT_ALL_Nom_SITE'
                },
                {
                    data: 'EXPORT_ALL_Nom_TECHNICIEN',
                    name: 'EXPORT_ALL_Nom_TECHNICIEN',
                    title: 'EXPORT_ALL_Nom_TECHNICIEN'
                },
                {
                    data: 'EXPORT_ALL_PRENom_TECHNICIEN',
                    name: 'EXPORT_ALL_PRENom_TECHNICIEN',
                    title: 'EXPORT_ALL_PRENom_TECHNICIEN'
                },
                {
                    data: 'EXPORT_ALL_Nom_EQUIPEMENT',
                    name: 'EXPORT_ALL_Nom_EQUIPEMENT',
                    title: 'EXPORT_ALL_Nom_EQUIPEMENT'
                },
                {
                    data: 'EXPORT_ALL_EXTRACT_CUI',
                    name: 'EXPORT_ALL_EXTRACT_CUI',
                    title: 'EXPORT_ALL_EXTRACT_CUI'
                },
                {
                    data: 'EXPORT_ALL_Date_CHARGEMENT_PDA',
                    name: 'EXPORT_ALL_Date_CHARGEMENT_PDA',
                    title: 'EXPORT_ALL_Date_CHARGEMENT_PDA'
                },
                {
                    data: 'EXPORT_ALL_Date_SOLDE',
                    name: 'EXPORT_ALL_Date_SOLDE',
                    title: 'EXPORT_ALL_Date_SOLDE'
                },
                {
                    data: 'EXPORT_ALL_Date_VALIDATION',
                    name: 'EXPORT_ALL_Date_VALIDATION',
                    title: 'EXPORT_ALL_Date_VALIDATION'
                }
            ],
            initComplete: function (settings, response) {
                toggleLoader(refreshBtn.parents('.col-12'), true);
            }

        });
    }

});
