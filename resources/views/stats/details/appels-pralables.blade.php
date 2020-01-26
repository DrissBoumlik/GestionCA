@extends('layouts.backend')

@section('page-title')
    Appels Préalables
@endsection

@section('css_after')
    <!-- DataTables -->
    <link rel="stylesheet" href={{ asset("/add_ons/datatables-bs4/css/dataTables.bootstrap4.css") }}>
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/7.2.0/sweetalert2.min.css">
    <link rel="stylesheet" href="{{ asset('js/plugins/sweetalert2/sweetalert2.min.css') }}">


    {{--        <link rel="stylesheet" href="https://cdn.metroui.org.ua/v4.3.2/css/metro-all.min.css">--}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.min.css">
    <link rel="stylesheet" href="{{ asset("js/plugins/chart.js/Chart.min.css") }}">
@endsection

@section('js_after')
    <!-- DataTables -->
    <script src={{ asset("/add_ons/datatables/jquery.dataTables.js") }}></script>
    <script src={{ asset("/add_ons/datatables-bs4/js/dataTables.bootstrap4.js") }}></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/7.2.0/sweetalert2.all.min.js"></script>
    <script src="{{ asset("js/plugins/sweetalert2/sweetalert2.all.min.js") }}"></script>

    <!-- TREE VIEW -->
    {{--        <script src="https://cdn.metroui.org.ua/v4.3.2/js/metro.min.js"></script>--}}
    <script src="{{ asset("/add_ons/tree-view/tree.js") }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.min.js"></script>
    <script src="{{ asset("js/plugins/chart.js/Chart.min.js") }}"></script>

    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@0.7.0"></script>

    <script src="{{ asset("/add_ons/stats/details/appels_prealables/stats.js") }}"></script>
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
    @if (request()->has('agence_code'))
        <input type="hidden" name="agence_name" id="agence_name" value="{{$agence}}">
    @endif
    @if (request()->has('agent_name'))
        <input type="hidden" name="agent_name" id="agent_name" value="{{$agent}}">
    @endif
    <div class="bg-image overflow-hidden"
         style="background-image: url('{{ asset('/media/backgrounds/photo3@2x.jpg') }}');">
        <div class="bg-primary-dark-op">
            <div class="content content-narrow content-full">
                <div
                    class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center mt-5 mb-2 text-center text-sm-left">
                    <div class="flex-sm-fill">
                        @if (request()->has('agence_code'))
                            <h1 class="font-w600 text-white mb-0 invisible" data-toggle="appear">Tableau de bord Agence {{$agence}}</h1>
                        @elseif(request()->has('agent_name'))
                            <h1 class="font-w600 text-white mb-0 invisible" data-toggle="appear">Tableau de bord Agent {{strtoupper($agent)}}</h1>
                        @else
                            <h1 class="font-w600 text-white mb-0 invisible" data-toggle="appear">Tableau de bord</h1>
                        @endif
                        <h2 class="h4 font-w400 text-white-75 mb-0 invisible" data-toggle="appear" data-timeout="250">
                            Bonjour {{ Auth::user()->firstname }} {{ Auth::user()->lastname }}</h2>
                    </div>
                    @if (Auth::user()->role->id === 1)
                        <div class="flex-sm-00-auto mt-3 mt-sm-0 ml-sm-3">
                        <span class="d-inline-block invisible" data-toggle="appear" data-timeout="350">
                            <a class="btn btn-primary px-4 py-2" data-toggle="click-ripple"
                               href="{{ route('stats.import') }}">
                                <i class="fa fa-plus mr-1"></i> Nouvelle Importation
                            </a>
                        </span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <!-- END Hero -->
@endsection

@section('content')
    <!-- Page Content -->
    <div class="content content-narrow">
        <!-- Filter -->
        <div class="container-fluid">
            @include('stats.layouts.filter_menu')
            <hr>
            @include('stats.layouts.global_date_filter')
        </div>
        <hr>
        <!-- Stats -->
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title d-inline-block">Résultats Appels Préalables</h3>
                            <hr>
                            <div class="refresh-form">
                                <div id="tree-view-0" class="tree-view d-inline-flex"></div>
                                <div id="stats-groupement-filter" class="tree-groupement-view d-inline-flex"></div>
                                <button type="button" id="refreshCallsPrealable" class="btn btn-primary float-right d-none">
                                    <span class="btn-field font-weight-normal position-relative">Rafraîchir</span>
                                </button>
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body table-responsive">
                            <table id="statsCallsPrealable"
                                   class="table table-bordered table-striped table-valign-middle capitalize">
                            </table>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
                <!-- /.col -->
                <div class="col-12">
                    <canvas id="statsCallsPrealableChart" class=""></canvas>
                </div>
                <!-- /.col -->
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
                                <div id="tree-view-2" class="tree-view d-inline-flex"></div>
                                <div id="stats-call-regions-filter" class="tree-call-region-view d-inline-flex"></div>
                                <button type="button" id="refreshCallStatesAgencies"
                                        class="btn btn-primary float-right d-none">
                                    <span class="btn-field font-weight-normal position-relative">Rafraîchir</span>
                                </button>
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body table-responsive">
                            <table id="callsStatesAgencies"
                                   class="table table-bordered table-striped table-valign-middle capitalize">
                            </table>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
                <!-- /.col -->
                <div class="col-12">
                    <canvas id="callsStatesAgenciesChart" class=""></canvas>
                </div>
                <!-- /.col -->
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
                                <div id="tree-view-3" class="tree-view d-inline-flex"></div>
                                <div id="stats-weeks-regions-filter" class="tree-weeks-region-view d-inline-flex"></div>
                                <button type="button" id="refreshCallStatesWeeks" class="btn btn-primary float-right d-none">
                                    <span class="btn-field font-weight-normal position-relative">Rafraîchir</span>
                                </button>
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body table-responsive">
                            <table id="callsStatesWeeks"
                                   class="table table-bordered table-striped table-valign-middle capitalize">
                            </table>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
                <!-- /.col -->
                <div class="col-12">
                    <canvas id="callsStatesWeeksChart" class=""></canvas>
                </div>
                <!-- /.col -->
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
                                <div id="tree-view-4" class="tree-view d-inline-flex"></div>
                                <div id="code-rdv-intervention-confirm-filter"
                                     class="tree-code-rdv-intervention-confirm-view d-inline-flex"></div>
                                <button type="button" id="refreshCallResultPos" class="btn btn-primary float-right d-none">
                                    <span class="btn-field font-weight-normal position-relative">Rafraîchir</span>
                                </button>
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body table-responsive">
                            <table id="statsCallsPos"
                                   class="table table-bordered table-striped table-valign-middle capitalize">
                            </table>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
                <!-- /.col -->
                <div class="col-12">
                    <canvas id="statsCallsPosChart" class=""></canvas>
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
                                <div id="tree-view-5" class="tree-view d-inline-flex"></div>
                                <div id="code-rdv-intervention-filter"
                                     class="tree-code-rdv-intervention-view d-inline-flex"></div>
                                <button type="button" id="refreshCallResultNeg" class="btn btn-primary float-right d-none">
                                    <span class="btn-field font-weight-normal position-relative">Rafraîchir</span>
                                </button>
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body table-responsive">
                            <table id="statsCallsNeg"
                                   class="table table-bordered table-striped table-valign-middle capitalize">
                            </table>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
                <!-- /.col -->
                <div class="col-12">
                    <canvas id="statscallsNegChart" class=""></canvas>
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </div>
        <!-- END Stats -->
    </div>
@endsection
