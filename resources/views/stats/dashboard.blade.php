@extends('layouts.backend')

@section('page-title')
    Dashboard
@endsection

@section('css_after')
    <!-- DataTables -->
    <link rel="stylesheet" href={{ asset("/add_ons/datatables-bs4/css/dataTables.bootstrap4.css") }}>
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/7.2.0/sweetalert2.min.css">
    <!-- Select2 -->
    <link href="{{ asset("/add_ons/select2/css/select2.min.css") }}" rel="stylesheet"/>

    {{--        <link rel="stylesheet" href="https://cdn.metroui.org.ua/v4.3.2/css/metro-all.min.css">--}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.min.css">
@endsection

@section('js_after')
    <!-- DataTables -->
    <script src={{ asset("/add_ons/datatables/jquery.dataTables.js") }}></script>
    <script src={{ asset("/add_ons/datatables-bs4/js/dataTables.bootstrap4.js") }}></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/7.2.0/sweetalert2.all.min.js"></script>
    <!-- Select2 -->
    <script src="{{ asset("/add_ons/select2/js/select2.min.js") }}"></script>

    <!-- TREE VIEW -->
    {{--        <script src="https://cdn.metroui.org.ua/v4.3.2/js/metro.min.js"></script>--}}
    <script src="{{ asset("/add_ons/tree-view/tree.js") }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.min.js"></script>
    <script src="{{ asset("/add_ons/stats/stats.js") }}"></script>
    <script>
        $(function () {
            @if($errors->any())
            swal(
                'Error!',
                '{{ $errors->first() }}',
                'error'
            );
            @endif
            @if(session()->has('message'))
            swal(
                'Success!',
                '{{ session()->get('message') }}',
                'success'
            );
            @endif
        })
    </script>
@endsection

@section('content-header')
    <!-- Hero -->
    <div class="bg-image overflow-hidden"
         style="background-image: url('{{ asset('/media/backgrounds/photo3@2x.jpg') }}');">
        <div class="bg-primary-dark-op">
            <div class="content content-narrow content-full">
                <div
                    class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center mt-5 mb-2 text-center text-sm-left">
                    <div class="flex-sm-fill">
                        <h1 class="font-w600 text-white mb-0 invisible" data-toggle="appear">Dashboard</h1>
                        <h2 class="h4 font-w400 text-white-75 mb-0 invisible" data-toggle="appear" data-timeout="250">
                            Welcome Administrator</h2>
                    </div>
                    <div class="flex-sm-00-auto mt-3 mt-sm-0 ml-sm-3">
                        <span class="d-inline-block invisible" data-toggle="appear" data-timeout="350">
                            <a class="btn btn-primary px-4 py-2" data-toggle="click-ripple"
                               href="stats">
                                <i class="fa fa-plus mr-1"></i> New Import
                            </a>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END Hero -->
@endsection

@section('content')
    <!-- Page Content -->
    <div class="content content-narrow">
        <!-- Stats -->
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title d-inline-block">Résultats Appels (Clients Joints)</h3>
                            <hr>
                            <div class="refresh-form">
                                <div id="tree-view-0" class="tree-view d-inline-block"></div>
                                <button type="button" id="refreshRegions" class="btn btn-primary float-right">
                                    <span class="btn-field font-weight-normal position-relative">Refresh</span>
                                </button>
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body table-responsive">
                            <table id="statsRegions"
                                   class="table table-bordered table-striped table-valign-middle capitalize">
                                <thead>
                                <tr>
                                    <th>Résultats Appels Préalables "Client Joignables"</th>
                                    @for($i = 1; $i < count($calls_results); $i++)
                                        <th>{{ $calls_results[$i]->name }}</th>
                                    @endfor
                                </tr>
                                </thead>
                            </table>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
                <!-- /.col -->
                <div class="col-12">
                    <canvas id="statsRegionsChart" class="d-none"></canvas>
                </div>
            </div>
            <!-- /.row -->
            <hr>
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title d-inline-block">Résultats Appels Préalables par agence</h3>
                            <hr>
                            <div class="refresh-form">
                                <div id="tree-view-5" class="tree-view d-inline-block"></div>
                                <button type="button" id="refreshCallStatesAgencies" class="btn btn-primary float-right">
                                    <span class="btn-field font-weight-normal position-relative">Refresh</span>
                                </button>
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body table-responsive">
                            <table id="calls_states_agencies"
                                   class="table table-bordered table-striped table-valign-middle capitalize">
                                <thead>
                                <tr>
                                    <th>Résultats Appels Préalables par agence</th>
                                    @for($i = 1; $i < count($calls_states_regions); $i++)
                                        <th>{{ $calls_states_regions[$i]->name }}</th>
                                    @endfor
                                </tr>
                                </thead>
                            </table>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
                <!-- /.col -->
                <div class="col-12">
                    <canvas id="statsRegionsChart" class="d-none"></canvas>
                </div>
            </div>
            <!-- /.row -->
            <hr>
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title d-inline-block">Résultats Appels Préalables par semaine</h3>
                            <hr>
                            <div class="refresh-form">
                                <div id="tree-view-6" class="tree-view d-inline-block"></div>
                                <button type="button" id="refreshCallStatesWeeks" class="btn btn-primary float-right">
                                    <span class="btn-field font-weight-normal position-relative">Refresh</span>
                                </button>
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body table-responsive">
                            <table id="calls_states_weeks"
                                   class="table table-bordered table-striped table-valign-middle capitalize">
                                <thead>
                                <tr>
                                    <th>Résultats Appels Préalables par agence</th>
                                    @for($i = 1; $i < count($calls_states_weeks); $i++)
                                        <th>{{ $calls_states_weeks[$i]->name }}</th>
                                    @endfor
                                </tr>
                                </thead>
                            </table>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
                <!-- /.col -->
                <div class="col-12">
                    <canvas id="statsRegionsChart" class="d-none"></canvas>
                </div>
            </div>
            <!-- /.row -->
            <hr>
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title d-inline-block">Code Interventions liés aux RDV Confirmés (Clients
                                Joignables)</h3>
                            <hr>
                            <div class="refresh-form">
                                <div id="tree-view-1" class="tree-view d-inline-block"></div>
                                <button type="button" id="refreshCallResultPos" class="btn btn-primary float-right">
                                    <span class="btn-field font-weight-normal position-relative">Refresh</span>
                                </button>
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body table-responsive">
                            <table id="statsCallsPos"
                                   class="table table-bordered table-striped table-valign-middle capitalize">
                                <thead>
                                <tr>
                                    <th>Résultats Appels Préalables</th>
                                    @for($i = 1; $i < count($calls_pos); $i++)
                                        <th>{{ $calls_pos[$i]->name }}</th>
                                    @endfor
                                </tr>
                                </thead>
                            </table>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->

            <hr>
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title d-inline-block">Code Interventions liés aux RDV Non Confirmés (Clients
                                Injoignables)</h3>
                            <hr>
                            <div class="refresh-form">
                                <div id="tree-view-2" class="tree-view d-inline-block"></div>
                                <button type="button" id="refreshCallResultNeg" class="btn btn-primary float-right">
                                    <span class="btn-field font-weight-normal position-relative">Refresh</span>
                                </button>
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body table-responsive">
                            <table id="statsCallsNeg"
                                   class="table table-bordered table-striped table-valign-middle capitalize">
                                <thead>
                                <tr>
                                    <th>Résultats Appels Préalables</th>
                                    @for($i = 1; $i < count($calls_neg); $i++)
                                        <th>{{ $calls_neg[$i]->name }}</th>
                                    @endfor
                                </tr>
                                </thead>
                            </table>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->

            <hr>
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title d-inline-block">Répartition des dossiers non validés par Code Type
                                intervention</h3>
                            <hr>
                            <div class="refresh-form">
                                <div id="tree-view-3" class="tree-view d-inline-block"></div>
                                <button type="button" id="refreshFoldersByType" class="btn btn-primary float-right">
                                    <span class="btn-field font-weight-normal position-relative">Refresh</span>
                                </button>
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body table-responsive">
                            <table id="statsTypes"
                                   class="table table-bordered table-striped table-valign-middle capitalize">
                                <thead>
                                <tr>
                                    <th>Type Intervention</th>
                                    @for($i = 1; $i < count($regions_names_type); $i++)
                                        <th>{{ $regions_names_type[$i]->name }}</th>
                                    @endfor
                                </tr>
                                </thead>
                            </table>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
            <hr>
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title d-inline-block">Répartition des dossiers non validés par code
                                intervention</h3>
                            <hr>
                            <div class="refresh-form">
                                <div id="tree-view-4" class="tree-view d-inline-block"></div>
                                <button type="button" id="refreshFoldersByCode" class="btn btn-primary float-right">
                                    <span class="btn-field font-weight-normal position-relative">Refresh</span>
                                </button>
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body table-responsive">
                            <table id="statsCodes"
                                   class="table table-bordered table-striped table-valign-middle capitalize">
                                <thead>
                                <tr>
                                    <th>Code Intervention</th>
                                    @for($i = 1; $i < count($regions_names_code); $i++)
                                        <th>{{ $regions_names_code[$i]->name }}</th>
                                    @endfor
                                </tr>
                                </thead>
                            </table>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </div>
        <!-- END Stats -->
    </div>
@endsection
