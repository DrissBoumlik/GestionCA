$(document).ready(function () {
    let _months = [
        {
            id: 1,
            text: 'Janvier',
        },
        {
            id: 2,
            text: 'Février',
        },
        {
            id: 3,
            text: 'Mars',
        },
        {
            id: 4,
            text: 'Avril',
        },
        {
            id: 5,
            text: 'Mai',
        },
        {
            id: 6,
            text: 'juin',
        },
        {
            id: 7,
            text: 'Juillet',
        },
        {
            id: 8,
            text: 'Août',
        },
        {
            id: 9,
            text: 'Septembre',
        },
        {
            id: 10,
            text: 'Octobre',
        },
        {
            id: 11,
            text: 'Novembre',
        },
        {
            id: 12,
            text: 'Décembre',
        },
    ];
    // let monthElt = $('#months');
    // months.forEach((item, index) => {
    //     console.log(item);
    //     let element = ' <div class="custom-control custom-switch mb-1" style="display: inline-block;">' +
    //         '<input type="checkbox" class="custom-control-input d-none" id="month-' + index + '" name="months[]">' +
    //         '<label class="custom-control-label" for="month-' + index + '">' + item + '</label>' +
    //         '</div>'
    //     monthElt.append(element);
    // });

    let months = null;
    new Tree('#tree-view-months', {
        data: [{id: '-1', text: 'Choisisser un/des Mois', children: _months}],
        closeDepth: 2,
        loaded: function () {
            // this.values = ['0-0-0', '0-1-1', '0-0-2'];
            // console.log(this.selectedNodes);
            // console.log(this.values);
            // this.disables = ['0-0-0', '0-0-1', '0-0-2']
        },
        onChange: function () {
            months = this.values;
            console.log(months);
        }
    });
    $('.treejs-switcher').click();

    // var tableTasks = $('#table-tasks').DataTable({
    //     processing: true,
    //     serverSide: true,
    //     ajax: 'tasks/get-tasks',
    //     columns: [
    //         { data: 'date_reception_demande', name: 'date_reception_demande' },
    //         { data: 'operateur', name: 'operateur' },
    //         { data: 'code_projet_operateur', name: 'code_projet_operateur' },
    //         { data: 'cdp_operateur', name: 'cdp_operateur' },
    //         { data: 'agence', name: 'agence' },
    //         { data: 'cdp_circet', name: 'cdp_circet' },
    //         { data: 'otc_uo', name: 'otc_uo' },
    //         { data: 'code_site', name: 'code_site' },
    //         { data: 'patrimoine', name: 'patrimoine' },
    //         { data: 'site_b', name: 'site_b' },
    //         { data: 'cle', name: 'cle' },
    //         { data: 'type_op', name: 'type_op' },
    //         { data: 'type_support', name: 'type_support' },
    //         { data: 'conf', name: 'conf' },
    //         { data: 'type_eb_tiers', name: 'type_eb_tiers' },
    //         { data: 'acteur', name: 'acteur' },
    //         { data: 'date_envoi_eb', name: 'date_envoi_eb' },
    //         { data: 'date_validation_eb_par_tiers', name: 'date_validation_eb_par_tiers' },
    //         { data: 'etape_process_accueil_chez_tiers', name: 'etape_process_accueil_chez_tiers' },
    //         { data: 'commentaire', name: 'commentaire' },
    //         { data: 'statut', name: 'statut', className: "text-center", render: function (data) {
    //             switch (data) {
    //                 case 'affecter':
    //                     text = `<span class="badge badge-primary">A affecter</span>`;
    //                     break;
    //                 case 'encours':
    //                     text = `<span class="badge badge-info">En cours</span>`;
    //                     break;
    //                 case 'envoyee':
    //                     text = `<span class="badge badge-warning">Envoyée</span>`;
    //                     break;
    //                 case 'swapiso':
    //                     text = `<span class="badge badge-success">SWAP ISO</span>`;
    //                     break;
    //             }
    //             return text;
    //         }}
    //     ]
    // });

    $(document).on('click', '#btn-import', function (event) {
        $('#modal-import').modal('hide');
        $('#modal-loader').modal('show');
        let formData = new FormData($('#form-import')[0]);
        if (months !== null && months !== undefined) {
            formData.append('months', months);
        }
        event.preventDefault();
        $.ajax({
            method: 'post',
            url: 'stats/import-stats',
            data: formData,
            dateType: 'json',
            processData: false,
            contentType: false,
            success: function (data) {
                $('#modal-loader').modal('hide');
                type = data.success ? 'success' : 'error';
                swal(
                    data.message,
                    '',
                    type
                );
                // Swal.fire({
                //     // position: 'top-end',
                //     type: type,
                //     title: data.message,
                //     showConfirmButton: false,
                //     timer: 1500
                // });
                // tableTasks.draw(false);
            }
        });
    });
});
