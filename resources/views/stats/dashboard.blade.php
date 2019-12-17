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

            let data = [
                    {
                        "id": "2019",
                        "text": "2019",
                        "name": "truc",
                        "children": [
                            {
                                "id": "2019-02",
                                "text": "2019-02",
                                "children": [
                                    {
                                        "id": "2019-02-02",
                                        "text": "2019-02-02"
                                    },
                                    {
                                        "id": "2019-02-05",
                                        "text": "2019-02-05"
                                    },
                                    {
                                        "id": "2019-02-15",
                                        "text": "2019-02-15"
                                    }
                                ]
                            },
                            {
                                "id": "2019-05",
                                "text": "2019-05",
                                "children": [
                                    {
                                        "id": "2019-05-10",
                                        "text": "2019-05-10"
                                    }
                                    ,
                                    {
                                        "id": "2019-05-13",
                                        "text": "2019-05-13"
                                    },
                                    {
                                        "id": "2019-05-22",
                                        "text": "2019-05-22"
                                    }
                                ]
                            },
                            {
                                "id": "2019-09",
                                "text": "2019-09",
                                "children": [
                                    {
                                        "id": "2019-09-06",
                                        "text": "2019-09-06"
                                    },
                                    {
                                        "id": "2019-09-16",
                                        "text": "2019-09-16"
                                    },
                                    {
                                        "id": "2019-09-18",
                                        "text": "2019-09-18"
                                    }
                                ]
                            }
                        ]
                    },
                    {
                        "id": "2018",
                        "text": "2018",
                        "children": [
                            {
                                "id": "2018-04",
                                "text": "2018-04",
                                "children": [
                                    {
                                        "id": "2018-04-05",
                                        "text": "2018-04-05"
                                    },
                                    {
                                        "id": "2018-04-10",
                                        "text": "2018-04-10"
                                    },
                                    {
                                        "id": "2018-04-22",
                                        "text": "2018-04-22"
                                    }
                                ]
                            },
                            {
                                "id": "2018-07",
                                "text": "2018-07",
                                "children": [
                                    {
                                        "id": "2018-07-04",
                                        "text": "2018-07-04"
                                    },
                                    {
                                        "id": "2018-07-06",
                                        "text": "2018-07-06"
                                    },
                                    {
                                        "id": "2018-07-19",
                                        "text": "2018-07-19"
                                    }
                                ]
                            },
                        ]
                    },
                    {
                        "id": "2017",
                        "text": "2017",
                        "children": [
                            {
                                "id": "2017-06",
                                "text": "2017-06",
                                "children": [
                                    {
                                        "id": "2017-06-05",
                                        "text": "2017-06-05"
                                    },
                                    {

                                        "id": "2017-06-17",
                                        "text": "2017-06-17"
                                    },
                                    {
                                        "id": "2017-06-25",
                                        "text": "2017-06-25"
                                    }
                                ]
                            }
                        ]
                    }
                ];

            let tree = new Tree('#tree-view', {
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
            $('#tree-view .treejs-placeholder').each(function (index) {
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
                            <form action="/getRegionsByDate" method="POST">
                                @csrf
                                <div id="tree-view"></div>
                                <hr>
                                <div class="row">
                                    <div class="col-4">
                                        <div class="select2-wrapper d-inline">
                                            <select name="_dates[]" id="dates" class="w-100"></select>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <button type="submit" class="btn btn-primary">
                                            <span class="btn-field font-weight-normal position-relative">Refresh</span>
                                        </button>
                                    </div>
                                </div>
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
                                        <td>{{ $call['Resultat_Appel'] }}</td>
                                        @foreach($call['regions'] as $region)
                                            <td>{{ $region }} %</td>
                                        @endforeach
                                        @for ($i = 0; $i < count($regions['regions_names']) - count($call['regions']); $i++)
                                            <td>0.00 %</td>
                                        @endfor
                                        <td>{{ $call['total'] }} %</td>
                                    </tr>
                                @endforeach
                                @if (!count($regions['calls']))
                                    <tr><td colspan="100">Pas de résultats</td></tr>
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
                            <h3 class="card-title float-left">Code Interventions liés aux RDV Confirmés (Clients
                                Joignables)</h3>
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
                                        <td>{{ $region['Nom_Region'] }}</td>
                                        @foreach($region['codes'] as $code)
                                            <td>{{ $code }} %</td>
                                        @endforeach
                                        @for ($i = 0; $i < count($joignable['codes_names']) - count($region['codes']); $i++)
                                            <td>0.00 %</td>
                                        @endfor
                                        <td>{{ $region['total'] }} %</td>
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
                            <h3 class="card-title float-left">Code Interventions liés aux RDV Non Confirmés (Clients
                                Injoignables)</h3>
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
                                        <td>{{ $region['Nom_Region'] }}</td>
                                        @foreach($region['codes'] as $code)
                                            <td>{{ $code }} %</td>
                                        @endforeach
                                        @for ($i = 0; $i < count($inJoignable['codes_names']) - count($region['codes']); $i++)
                                            <td>0.00 %</td>
                                        @endfor
                                        <td>{{ $region['total'] }} %</td>
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
        </div>
        <!-- END Stats -->
    </div>
@endsection
