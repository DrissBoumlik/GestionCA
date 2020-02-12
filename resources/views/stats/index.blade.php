@extends('layouts.backend')

@section('page-title')
    Stats
@endsection

@section('css_after')
    <!-- DataTables -->
    <link rel="stylesheet" href={{ asset("/add_ons/datatables-bs4/css/dataTables.bootstrap4.css") }}>
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/7.2.0/sweetalert2.min.css">
    <link rel="stylesheet" href="{{ asset('js/plugins/sweetalert2/sweetalert2.min.css') }}">

@endsection

@section('js_after')
    <!-- DataTables -->
    <script src={{ asset("/add_ons/datatables/jquery.dataTables.js") }}></script>
    <script src={{ asset("/add_ons/datatables-bs4/js/dataTables.bootstrap4.js") }}></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/7.2.0/sweetalert2.all.min.js"></script>
    <script src="{{ asset("js/plugins/sweetalert2/sweetalert2.all.min.js") }}"></script>

    <script src="{{ asset("/add_ons/tree-view/tree.js") }}"></script>

    <script src="{{ asset("/add_ons/stats/index.js") }}"></script>
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
    <div class="bg-image overflow-hidden"
         style="background-image: url('{{ asset('/media/backgrounds/photo3@2x.jpg') }}');">
        <div class="bg-primary-dark-op">
            <div class="content content-narrow content-full">
                <div
                    class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center mt-5 mb-2 text-center text-sm-left">
                    <div class="flex-sm-fill">
                        @if (request()->has('agence_code'))
                            <h1 class="font-w600 text-white mb-0 invisible" data-toggle="appear">Dashboard
                                Agence {{$agence}}</h1>
                        @elseif(request()->has('agent_name'))
                            <h1 class="font-w600 text-white mb-0 invisible" data-toggle="appear">Dashboard
                                Agent {{strtoupper($agent)}}</h1>
                        @else
                            <h1 class="font-w600 text-white mb-0 invisible" data-toggle="appear">Dashboard</h1>
                        @endif
                        <h2 class="h4 font-w400 text-white-75 mb-0 invisible" data-toggle="appear" data-timeout="250">
                            Welcome {{ Auth::user()->firstname }} {{ Auth::user()->lastname }}</h2>
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
            <a href="{{ URL::route('ExportXls', array('row' => request('row'), 'rowValue' => request('rowValue'),'col' => request('col') , 'colValue' => request('colValue'),'agent' => request('agent') ,
                                    'agence' => request('agence') , 'dates' => request('dates'), 'Resultat_Appel' => request('Resultat_Appel') , 'queryJoin' => request('queryJoin'),
                                    'subGroupBy'=> request('subGroupBy') , 'queryGroupBy' => request('queryGroupBy'),'appCltquery' => request('appCltquery')  )) }}"
               class="btn btn-primary mb-3 capitalize-first-letter bg-green">
                exporter des données au format Excel </a>
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title d-inline-block">Stats</h3>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="stats"
                                       class="table table-bordered table-striped table-valign-middle capitalize">
                                    <thead>
                                        <tr>
                                            <th>Type Note</th>
                                            <th>Utilisateur</th>
                                            <th width="200px">Resultat Appel</th>
                                            <th>Date Nveau RDV</th>
                                            <th>Heure Nveau RDV</th>
                                            <th>Marge Nveau RDV</th>
                                            <th>Id Externe</th>
                                            <th>Date Creation</th>
                                            <th>Code Postal Site</th>
                                            <th>Drapeaux</th>
                                            <th>Code Type Intervention</th>
                                            <th>Date Rdv</th>
                                            <th>Nom Societe</th>
                                            <th>Nom Region</th>
                                            <th>Nom Domaine</th>
                                            <th>Nom Agence</th>
                                            <th>Nom Activite</th>
                                            <th>Date Heure Note</th>
                                            <th>Date Heure Note Annee</th>
                                            <th>Date Heure Note Mois</th>
                                            <th>Date Heure Note Semaine</th>
                                            <th>Date Note</th>
                                            <th>Groupement</th>
                                            <th>key Groupement</th>
                                            <th>Gpmt Appel Pre</th>
                                            <th>Code Intervention</th>
                                            <th>EXPORT ALL Nom SITE</th>
                                            <th>EXPORT ALL Nom TECHNICIEN</th>
                                            <th>EXPORT ALL PRENom TECHNICIEN</th>
                                            <th>EXPORT ALL Nom EQUIPEMENT</th>
                                            <th>EXPORT ALLEXTRACT CUI</th>
                                            <th>EXPORT ALL Date CHARGEMENT PDA</th>
                                            <th>EXPORT ALL Date SOLDE</th>
                                            <th>EXPORT ALL Date VALIDATION</th>
                                        </tr>
                                    </thead>
                                </table>
                                <div id="data-request" data-request="{{ json_encode($data) }}" class="d-none"></div>
                            </div>
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
