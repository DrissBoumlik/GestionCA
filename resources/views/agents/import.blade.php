@extends('layouts.backend')

@section('page-title')
    Importation des Agents
@endsection

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

    <script src="{{ asset("/add_ons/tree-view/tree.js") }}"></script>
    <script src="{{ asset('js/plugins/jquery-validation/jquery.validate.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>
    <!-- Page JS Code -->
    <script src="{{ asset('add_ons/agents/import-agents.js') }}"></script>
@endsection

@section('content')
    <!-- Hero -->
    <div class="bg-body-light">
        <div class="content content-full">
            <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
                <h1 class="flex-sm-fill h3 my-2">
                    Importation des Agents - Choisissez un fichier excel
                </h1>
                <nav class="flex-sm-00-auto ml-sm-3" aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-alt">
                        <li class="breadcrumb-item">
                            <a class="link-fx" href="{{ route('dashboard') }}">Tableau de board</a>
                        </li>
                        <li class="breadcrumb-item" aria-current="page">Importation</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <!-- END Hero -->

    <!-- Page Content -->
    <div class="content">
        <div class="row mb-4">
            <div class="col-12" id="months">
                <div id="tree-view-months" class="tree-view d-inline-block"></div>
            </div>
        </div>
        <div class="row mb-4">
            <div class="col-12">
                <div class="btns">
                    <div class="btn-import-wrapper">
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal-import"
                                id="showModalImport">
                            <i class="fa fa-upload mr-1"></i> Importer
                        </button>
                    </div>
                    <div class="btn-view-data-wrapper">
                        <a href="{{ route('agents.viewData') }}" target="_blank" class="btn btn-primary btn-view-data">
                            <i class="fa fa-eye mr-1"></i> Visualiser les données importées
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END Page Content -->

    <!-- Model Loader -->
    <div class="modal" id="modal-loader" tabindex="-1" role="dialog" aria-labelledby="modal-loader" aria-hidden="true">
        <div class="spinner-wrapper align-center">
            <div class="spinner-border text-primary" role="status">
                <span class="sr-only">Loading...</span>
            </div>
            <div class="imported-data mt-4 font-size-h3" id="imported-data">
                0 lignes inserées
            </div>
        </div>
    </div>

    <!-- Modal Import -->
    <div class="modal" id="modal-import" tabindex="-1" role="dialog" aria-labelledby="modal-import" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="block block-themed block-transparent mb-0">
                    <div class="block-header bg-primary-dark">
                        <h3 class="block-title">Importation des données</h3>
                        <div class="block-options">
                            <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                                <i class="fa fa-fw fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <div class="block-content font-size-sm">
                        <form id="form-import">
                            @csrf
                            <div class="form-group">
                                <label class="d-none">Bootstrap’s Custom File Input</label>
                                <div class="custom-file">
                                    <!-- Populating custom file input label with the selected filename (data-toggle="custom-file-input" is initialized in Helpers.coreBootstrapCustomFileInput()) -->
                                    {{--                                    accept=".xlsx, .xls, .xlsm"--}}
                                    <input type="file" class="custom-file-input" data-toggle="custom-file-input"
                                           id="file" name="file">
                                    <label class="custom-file-label capitalize-first-letter" for="file">choisir le
                                        fichier</label>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="block-content block-content-full text-right border-top">
                        <button type="button" class="btn btn-sm btn-light" data-dismiss="modal">Fermer</button>
                        <button type="button" class="btn btn-sm btn-primary" id="btn-import"><i
                                class="fa fa-check mr-1"></i>Importer
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal ALERT -->
    <!-- Pop In Block Modal -->
    <div class="modal fade" id="modal-block-popin" tabindex="-1" role="dialog" aria-labelledby="modal-block-popin"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-popin" role="document">
            <div class="modal-content">
                <div class="block block-themed block-transparent mb-0">
                    <div class="block-header bg-primary-dark">
                        <h3 class="block-title">Attention</h3>
                        <div class="block-options">
                            <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                                <i class="fa fa-fw fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <div class="block-content font-size-sm">
                        <p>Vous dever choisir au moin une date.</p>
                    </div>
                    <div class="block-content block-content-full text-right border-top">
                        <button type="button" class="btn btn-sm btn-light" data-dismiss="modal">Close</button>
                        {{--                        <button type="button" class="btn btn-sm btn-primary" data-dismiss="modal"><i class="fa fa-check mr-1"></i>Ok</button>--}}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END Pop In Block Modal -->

@endsection
