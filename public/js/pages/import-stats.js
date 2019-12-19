$(document).ready(function() {
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
        event.preventDefault();
        $.ajax({
            method: 'post',
            url: 'stats/import-stats',
            data: new FormData($('#form-import')[0]),
            dateType: 'json',
            processData: false,
            contentType: false,
            success: function (data) {
                $('#modal-import').modal('hide');
                type = data.success ? 'success' : 'error';
                Swal.fire({
                    // position: 'top-end',
                    type: type,
                    title: data.message,
                    showConfirmButton: false,
                    timer: 1500
                });
                // tableTasks.draw(false);
            }
        });
    });
});
