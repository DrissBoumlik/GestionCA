@extends('layouts.backend')

@section('page-title')
    Dashboard
@endsection

@section('css_after')
    <!-- DataTables -->
    {{--    <link rel="stylesheet" href={{ asset("/add_ons/datatables-bs4/css/dataTables.bootstrap4.css") }}>--}}
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/7.2.0/sweetalert2.min.css">


@endsection

@section('js_after')
    <!-- DataTables -->
    {{--    <script src={{ asset("/add_ons/datatables/jquery.dataTables.js") }}></script>--}}
    {{--    <script src={{ asset("/add_ons/datatables-bs4/js/dataTables.bootstrap4.js") }}></script>--}}
    <script src="//cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/7.2.0/sweetalert2.all.min.js"></script>

    <!-- TREE VIEW -->
    {{--        <script src="https://cdn.metroui.org.ua/v4.3.2/js/metro.min.js"></script>--}}
    <script src="{{ asset("/add_ons/tree-view/tree.js") }}"></script>
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
                               href="javascript:void(0)">
                                <i class="fa fa-plus mr-1"></i> New Project
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
{{--            <div class="row">--}}
{{--                <div class="col-12">--}}
{{--                    <div class="card">--}}
{{--                        <div class="card-header">--}}
{{--                            <h3 class="card-title d-inline-block">Résultats Appels (Clients Joints)</h3>--}}
{{--                            <hr>--}}
{{--                            <form action="/" method="POST">--}}
{{--                                @csrf--}}
{{--                                <div id="tree-view-0" class="tree-view d-inline-block"></div>--}}
{{--                                <button type="submit" class="btn btn-primary float-right">--}}
{{--                                    <span class="btn-field font-weight-normal position-relative">Refresh</span>--}}
{{--                                </button>--}}
{{--                            </form>--}}
{{--                        </div>--}}
{{--                        <!-- /.card-header -->--}}
{{--                        <div class="card-body table-responsive">--}}
{{--                            <table id="stats" class="table table-bordered table-striped table-valign-middle capitalize">--}}
{{--                                <thead>--}}
{{--                                <tr>--}}
{{--                                    <th>Résultats Appels Préalables "Client Joignables"</th>--}}
{{--                                    @foreach($regions['regions_names'] as $key => $region_name)--}}
{{--                                        <th>{{ $region_name }}</th>--}}
{{--                                    @endforeach--}}
{{--                                    <th>Total</th>--}}
{{--                                </tr>--}}
{{--                                </thead>--}}
{{--                                <tbody>--}}
{{--                                @foreach($regions['calls'] as $key => $call)--}}
{{--                                    <tr>--}}
{{--                                        <td>{{ $call->Resultat_Appel }}</td>--}}
{{--                                        @foreach($call->regions as $region)--}}
{{--                                            <td>{{ $region }} %</td>--}}
{{--                                        @endforeach--}}
{{--                                        @for ($i = 0; $i < count($regions['regions_names']) - count($call->regions); $i++)--}}
{{--                                            <td>0.00 %</td>--}}
{{--                                        @endfor--}}
{{--                                        <td>{{ $call->total }} %</td>--}}
{{--                                    </tr>--}}
{{--                                @endforeach--}}
{{--                                @if (!count($regions['calls']))--}}
{{--                                    <tr>--}}
{{--                                        <td colspan="100">Pas de résultats</td>--}}
{{--                                    </tr>--}}
{{--                                @endif--}}
{{--                                </tbody>--}}
{{--                            </table>--}}
{{--                        </div>--}}
{{--                        <!-- /.card-body -->--}}
{{--                    </div>--}}
{{--                    <!-- /.card -->--}}
{{--                </div>--}}
{{--                <!-- /.col -->--}}
{{--            </div>--}}
<!-- /.row -->
        </div>
        <!-- END Stats -->
    </div>
@endsection
