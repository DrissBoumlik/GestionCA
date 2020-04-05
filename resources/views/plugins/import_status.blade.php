<div class="import_status-wrapper">
    <div class="import_status wrapper animated pulse infinite">
        <div class="info" data-toggle="modal" data-target="#modal-import-status">
            <i class="fas fa-info"></i>
        </div>
    </div>


    <!-- Modal ALERT -->
    <!-- Pop In Block Modal -->
    <div class="modal fade" id="modal-import-status" tabindex="-1" role="dialog" aria-labelledby="modal-import-status"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-popin" role="document">
            <div class="modal-content">
                <div class="block block-themed block-transparent mb-0">
                    <div class="block-header bg-primary-dark">
                        <h3 class="block-title">Importation en cours</h3>
                        <div class="block-options">
                            <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                                <i class="fa fa-fw fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <div class="block-content font-size-sm">
                        <p><i class="fas fa-spinner animated rotateIn infinite mr-2"></i><span class="imported-data">0 lignes inserées</span></p>
                    </div>
                    <div class="block-content block-content-full text-right border-top">
                        <button type="button" class="btn btn-sm btn-light" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END Pop In Block Modal -->
</div>
