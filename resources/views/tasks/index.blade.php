@extends('layouts.backend')

@section('css_before')
    <!-- Page JS Plugins CSS -->
    <link rel="stylesheet" href="{{ asset('js/plugins/datatables/dataTables.bootstrap4.css') }}">
    <link rel="stylesheet" href="{{ asset('js/plugins/datatables/buttons-bs4/buttons.bootstrap4.min.css') }}">
@endsection

@section('js_after')
    <!-- Page JS Plugins -->
    <script src="{{ asset('js/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('js/plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('js/plugins/datatables/buttons/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('js/plugins/datatables/buttons/buttons.print.min.js') }}"></script>
    <script src="{{ asset('js/plugins/datatables/buttons/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('js/plugins/datatables/buttons/buttons.flash.min.js') }}"></script>
    <script src="{{ asset('js/plugins/datatables/buttons/buttons.colVis.min.js') }}"></script>

    <!-- Page JS Code -->
    <script src="{{ asset('js/pages/task.js') }}"></script>
@endsection

@section('content')
    <!-- Hero -->
    <div class="bg-body-light">
        <div class="content content-full">
            <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
                <h1 class="flex-sm-fill h3 my-2">
                    Les Tâches
                </h1>
                <nav class="flex-sm-00-auto ml-sm-3" aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-alt">
                        <li class="breadcrumb-item">Circet</li>
                        <li class="breadcrumb-item" aria-current="page">
                            <a class="link-fx" href="{{ route('tasks.index') }}">Les Tâches</a>
                        </li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <!-- END Hero -->

    <!-- Page Content -->
    <div class="content">
        <div class="row mb-4">
            <div class="col-12">
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal-import">
                    <i class="fa fa-fw fa-upload mr-1"></i> Importer des tâches
                </button>
            </div>
        </div>
        <!-- Your Block -->
        <div class="block">
            <div class="block-header">
                <h3 class="block-title">Dynamic Table <small>Export Buttons</small></h3>
            </div>
            <div class="block-content block-content-full">
                <div class="table-responsive">
                    <table id="table-tasks" class="table table-bordered table-striped table-vcenter">
                        <thead>
                        <tr>
                            <th>Date de réception</th>
                            <th>Operateur</th>
                            <th>Code projet operateur</th>
                            <th>CDP Opérateur</th>
                            <th>Agence</th>
                            <th>CDP CIRCET</th>
                            <th>OTC - UO</th>
                            <th>Code Site</th>
                            <th>Patrimoine</th>
                            <th>Site B</th>
                            <th>Clé</th>
                            <th>Type d'OP</th>
                            <th>Type support</th>
                            <th>Conf</th>
                            <th>Type EB Tiers</th>
                            <th>Acteur</th>
                            <th>Date Envoi EB</th>
                            <th>Date de validation EB par le tiers</th>
                            <th>Etape dans le process d'accueil chez le Tiers</th>
                            <th>Commentaire</th>
                            <th class="text-center">Statut</th>
                        </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- END Your Block -->
    </div>
    <!-- END Page Content -->

    <div class="modal" id="modal-import" tabindex="-1" role="dialog" aria-labelledby="modal-import" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="block block-themed block-transparent mb-0">
                    <div class="block-header bg-primary-dark">
                        <h3 class="block-title">Importation des tâches</h3>
                        <div class="block-options">
                            <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                                <i class="fa fa-fw fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <div class="block-content font-size-sm">
                        <form id="form-import">
                            <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
                            <div class="form-group">
                                <label>Bootstrap’s Custom File Input</label>
                                <div class="custom-file">
                                    <!-- Populating custom file input label with the selected filename (data-toggle="custom-file-input" is initialized in Helpers.coreBootstrapCustomFileInput()) -->
                                    <input type="file" class="custom-file-input" data-toggle="custom-file-input" id="file" name="file">
                                    <label class="custom-file-label" for="file">choisir le fichier</label>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="block-content block-content-full text-right border-top">
                        <button type="button" class="btn btn-sm btn-light" data-dismiss="modal">Fermer</button>
                        <button type="button" class="btn btn-sm btn-primary" id="btn-import"><i class="fa fa-check mr-1"></i>Importer</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
