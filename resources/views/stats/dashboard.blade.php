@extends('layouts.backend')

@section('page-title')
    Dashboard
@endsection

@section('css_after')
    <!-- DataTables -->
    {{--    <link rel="stylesheet" href={{ asset("/add_ons/datatables-bs4/css/dataTables.bootstrap4.css") }}>--}}
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/7.2.0/sweetalert2.min.css">
    <!-- Select2 -->
    <link href="{{ asset("/add_ons/select2/css/select2.min.css") }}" rel="stylesheet"/>

    {{--        <link rel="stylesheet" href="https://cdn.metroui.org.ua/v4.3.2/css/metro-all.min.css">--}}

@endsection

@section('js_after')
    <!-- DataTables -->
    {{--    <script src={{ asset("/add_ons/datatables/jquery.dataTables.js") }}></script>--}}
    {{--    <script src={{ asset("/add_ons/datatables-bs4/js/dataTables.bootstrap4.js") }}></script>--}}
    <script src="//cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/7.2.0/sweetalert2.all.min.js"></script>
    <!-- Select2 -->
    <script src="{{ asset("/add_ons/select2/js/select2.min.js") }}"></script>

    <!-- TREE VIEW -->
    {{--        <script src="https://cdn.metroui.org.ua/v4.3.2/js/metro.min.js"></script>--}}
    <script src="{{ asset("/add_ons/tree-view/tree.js") }}"></script>

    <script src="{{ asset("/add_ons/stats/stats.js") }}"></script>
    <script>
        $(function () {
            $.ajax({
                url: '/getDates',
                method: 'GET',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                success: function (response) {
                    console.log(response);
                    let data = response.dates;
                    let tree = new Tree('.tree-view', {
                        data: [{id: '-1', text: 'Dates', children: data}],
                        closeDepth: 2,
                        loaded: function () {
                            // this.values = ['0-0-0', '0-1-1', '0-0-2'];
                            // console.log(this.selectedNodes);
                            // console.log(this.values);
                            // this.disables = ['0-0-0', '0-0-1', '0-0-2']
                        },
                        onChange: function () {
                            console.log(this.values);
                        }
                    });
                    // $('#tree-view .treejs-label').attr('name', 'dates[]');
                    // $('#tree-view .treejs-checkbox').attr('name', 'dates[]');
                    $('.tree-view .treejs-placeholder').each(function (index) {
                        $(this).append('<input type="hidden" name="dates[]" class="treejs-input" id="' + index + '" />');
                    });
                    var called = false;
                    $('.treejs-node').on('click', function (e) {
                        // console.log(e.target.classList);
                        if (e.target.className !== 'treejs-switcher') {
                            if (!called) {
                                // e.stopPropagation();
                                // let input = $(this).find('.treejs-input');
                                let label = $(this).find('.treejs-placeholder .treejs-label');
                                console.log(label);
                                label.each(function (index) {
                                    // debugger
                                    // console.log($(this));
                                    let input = $(this).next('.treejs-input');
                                    $(input).val(!$(this).parent().hasClass('treejs-node__checked') ? $(this).text() : '');
                                });
                                // console.log($(chbx).prop('checked'));
                                // $(chbx).prop('checked', !$(chbx).prop('checked'));
                                // $(chbx).attr('value','koko');
                                // $(chbx).prop('checked', !$(this).hasClass('treejs-node__checked') && !$(this).hasClass('treejs-node__halfchecked'));
                                // $(input).val(!$(this).hasClass('treejs-node__checked') ? label.text() : '');
                                setTimeout(function () {
                                    called = false;
                                }, 150);
                            }
                            called = true;
                        }
                    });
                    $('.treejs-node.treejs-placeholder').on('click', function (e) {
                        // test parent with class treejs-placeholder
                        let input = $(this).find('.treejs-input');
                        let label = $(this).find('.treejs-label');
                        // $(chbx).prop('checked', !$(this).hasClass('treejs-node__checked'));
                        $(input).val(!$(this).hasClass('treejs-node__checked') ? label.text() : '');
                    });
                    $('.treejs-switcher').first().parent().first().addClass('treejs-node__close')

                },
                error: function (jqXHR, textStatus, errorThrown) {

                }
            });


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
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title d-inline-block">Résultats Appels (Clients Joints)</h3>
                            <hr>
                            <form action="/" method="POST">
                                @csrf
                                <div class="tree-view d-inline-block"></div>
                                <button type="submit" class="btn btn-primary float-right">
                                    <span class="btn-field font-weight-normal position-relative">Refresh</span>
                                </button>
                            </form>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body table-responsive">
                            <table id="stats" class="table table-bordered table-striped table-valign-middle capitalize">
                                <thead>
                                <tr>
                                    <th>Résultats Appels Préalables "Client Joignables"</th>
                                    @foreach($regions['regions_names'] as $key => $region_name)
                                        <th>{{ $region_name }}</th>
                                    @endforeach
                                    <th>Total</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($regions['calls'] as $key => $call)
                                    <tr>
                                        <td>{{ $call->Resultat_Appel }}</td>
                                        @foreach($call->regions as $region)
                                            <td>{{ $region }} %</td>
                                        @endforeach
                                        @for ($i = 0; $i < count($regions['regions_names']) - count($call->regions); $i++)
                                            <td>0.00 %</td>
                                        @endfor
                                        <td>{{ $call->total }} %</td>
                                    </tr>
                                @endforeach
                                @if (!count($regions['calls']))
                                    <tr>
                                        <td colspan="100">Pas de résultats</td>
                                    </tr>
                                @endif
                                </tbody>
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
                            <h3 class="card-title d-inline-block">Code Interventions liés aux RDV Confirmés (Clients
                                Joignables)</h3>
                            <hr>
                            <form action="/" method="POST">
                                @csrf
                                <div class="tree-view d-inline-block"></div>
                                <button type="submit" class="btn btn-primary float-right">
                                    <span class="btn-field font-weight-normal position-relative">Refresh</span>
                                </button>
                            </form>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body table-responsive">
                            <table id="stats" class="table table-bordered table-striped table-valign-middle capitalize">
                                <thead>
                                <tr>
                                    <th>Résultats Appels Préalables "Client Joignables"</th>
                                    @foreach($joignable['codes_names'] as $key => $code)
                                        <th>{{ $code }}</th>
                                    @endforeach
                                    <th>Total</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($joignable['regions'] as $key => $region)
                                    <tr>
                                        <td>{{ $region->Nom_Region }}</td>
                                        @foreach($region->codes as $code)
                                            <td>{{ $code }} %</td>
                                        @endforeach
                                        @for ($i = 0; $i < count($joignable['codes_names']) - count($region->codes); $i++)
                                            <td>0.00 %</td>
                                        @endfor
                                        <td>{{ $region->total }} %</td>
                                    </tr>
                                @endforeach
                                </tbody>
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
                            <form action="/" method="POST">
                                @csrf
                                <div class="tree-view d-inline-block"></div>
                                <button type="submit" class="btn btn-primary float-right">
                                    <span class="btn-field font-weight-normal position-relative">Refresh</span>
                                </button>
                            </form>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body table-responsive">
                            <table id="stats" class="table table-bordered table-striped table-valign-middle capitalize">
                                <thead>
                                <tr>
                                    <th>Résultats Appels Préalables "Client Injoignables"</th>
                                    @foreach($inJoignable['codes_names'] as $key => $code)
                                        <th>{{ $code }}</th>
                                    @endforeach
                                    <th>Total</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($inJoignable['regions'] as $key => $region)
                                    <tr>
                                        <td>{{ $region->Nom_Region }}</td>
                                        @foreach($region->codes as $code)
                                            <td>{{ $code }} %</td>
                                        @endforeach
                                        @for ($i = 0; $i < count($inJoignable['codes_names']) - count($region->codes); $i++)
                                            <td>0.00 %</td>
                                        @endfor
                                        <td>{{ $region->total }} %</td>
                                    </tr>
                                @endforeach
                                </tbody>
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
                            <h3 class="card-title d-inline-block">Répartition des dossiers non validés pas code
                                intervention</h3>
                            <hr>
                            <form action="/" method="POST">
                                @csrf
                                <div class="tree-view d-inline-block"></div>
                                <button type="submit" class="btn btn-primary float-right">
                                    <span class="btn-field font-weight-normal position-relative">Refresh</span>
                                </button>
                            </form>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body table-responsive">
                            <table id="stats" class="table table-bordered table-striped table-valign-middle capitalize">
                                <thead>
                                <tr>
                                    <th>Type Traitement</th>
                                    @foreach($folders['regions_names'] as $key => $region_name)
                                        <th>{{ $region_name }}</th>
                                    @endforeach
                                    <th>Total</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($folders['codes'] as $key => $code)
                                    <tr>
                                        <td>{{ $code->Code_Type_Intervention }}</td>
                                        @foreach($code->regions as $region)
                                            <td>{{ $region }} %</td>
                                        @endforeach
                                        @for ($i = 0; $i < count($regions['regions_names']) - count($code->regions); $i++)
                                            <td>0.00 %</td>
                                        @endfor
                                        <td>{{ $code->total }} %</td>
                                    </tr>
                                @endforeach
                                @if (!count($folders['codes']))
                                    <tr>
                                        <td colspan="100">Pas de résultats</td>
                                    </tr>
                                @endif
                                </tbody>
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
