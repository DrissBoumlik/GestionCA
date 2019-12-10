@extends('layouts.backend')

@section('page-title')
    Permissions
@endsection

@section('css_after')
    <!-- DataTables -->
    <link rel="stylesheet"
          href={{ asset("/add_ons/datatables-bs4/css/dataTables.bootstrap4.css") }}>
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/7.2.0/sweetalert2.min.css">
@endsection

@section('content-header')
    <!-- Hero -->
    <div class="bg-body-light">
        <div class="content content-full">
            <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
                <div class="flex-sm-fill">
                    <h1 class="h3 my-2 d-inline-block">Permissions</h1>
                        <a href="/permissions/create" class="link btn btn-primary mgl-10 round d-none" title="Add Permission"><i class="fas fa-plus"></i></a>
                </div>
                <nav class="flex-sm-00-auto ml-sm-3" aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-alt">
                        <li class="breadcrumb-item">
                            <a class="link-fx" href="/">Home</a>
                        </li>
                        <li class="breadcrumb-item" aria-current="page">Permissions</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <!-- END Hero -->
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-none">
                        <h3 class="card-title float-left">Users</h3>
{{--                        <button class="btn btn-primary mgl-10 round" title="Add User">--}}
                            <a href="/users/create" class="link btn btn-primary mgl-10 round" title="Add User"><i class="fas fa-plus"></i></a>
{{--                        </button>--}}
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body table-responsive">
                        <table id="permissions-data" class="table table-bordered table-striped table-valign-middle capitalize">
                            <thead>
                            <tr>
                                <th></th>
                                <th>Name</th>
                                <th>Controller</th>
                                <th>Method</th>
                                <th>Options</th>
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
@endsection

@section('js_after')
    <!-- DataTables -->
    <script src={{ asset("/add_ons/datatables/jquery.dataTables.js") }}></script>
    <script src={{ asset("/add_ons/datatables-bs4/js/dataTables.bootstrap4.js") }}></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/7.2.0/sweetalert2.all.min.js"></script>
    <script src="{{ asset("/add_ons/permissions/datatable.js") }}"></script>
@endsection
