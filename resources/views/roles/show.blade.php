@extends('layouts.backend')

@section('page-title')
    Role - {{ ucfirst($role->name) }}
@endsection

@section('css_after')
    <!-- DataTables -->
    <link rel="stylesheet"
          href={{ asset("/add_ons/datatables-bs4/css/dataTables.bootstrap4.css") }}>
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/7.2.0/sweetalert2.min.css">
@endsection
@section('js_after')
    <!-- DataTables -->
    <script src={{ asset("/add_ons/datatables/jquery.dataTables.js") }}></script>
    <script src={{ asset("/add_ons/datatables-bs4/js/dataTables.bootstrap4.js") }}></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/7.2.0/sweetalert2.all.min.js"></script>
    <script src="{{ asset("/add_ons/roles/role.js") }}"></script>
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

            {{--$("#permissions-data").DataTable({--}}
            {{--    responsive: true,--}}
            {{--    info: false,--}}
            {{--    processing: true,--}}
            {{--    serverSide: true,--}}
            {{--    searching: false,--}}
            {{--    ajax: 'getRolePermissions/{{ $role->id }}',--}}
            {{--    columns: [--}}
            {{--        {--}}
            {{--            data: 'id', name: 'id',--}}
            {{--            render: function (data, type, full, meta) {--}}
            {{--                return "<a href='/permissions/" + data + "' class='align-center blue d-block'><i class='far fa-eye big-icon-fz'></i></a>";--}}
            {{--            }--}}
            {{--        },--}}
            {{--        {data: 'name', name: 'name', class: 'capitalize'},--}}
            {{--        {data: 'method', name: 'method', class: 'capitalize'},--}}
            {{--        {data: 'controller', name: 'controller', class: 'capitalize'},--}}
            {{--        {--}}
            {{--            data: 'assigned', name: 'assigned',--}}
            {{--            render: function (data, type, full, meta) {--}}
            {{--                return "<label for='status-" + full.id + "' class='m-0'>" +--}}
            {{--                    "<input class='data-status d-none change-status' data-role='" + {{ $role->id }} +"' data-status='" + full.id + "'" +--}}
            {{--                    " id='status-" + full.id + "' type='checkbox'" +--}}
            {{--                    (data ? 'checked' : '') +--}}
            {{--                    " name='status'>" +--}}
            {{--                    "<span class='status pointer'></span>" +--}}
            {{--                    "</label>";--}}
            {{--            }--}}
            {{--        }--}}
            {{--    ]--}}
            {{--});--}}
        });
    </script>
@endsection
@section('content-header')
    <!-- Hero -->
    <div class="bg-body-light">
        <div class="content content-full">
            <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
                <div class="flex-sm-fill">
                    <h1 class="h3 my-2 d-inline-block">Role</h1>
                </div>
                <nav class="flex-sm-00-auto ml-sm-3" aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-alt">
                        <li class="breadcrumb-item">
                            <a class="link-fx" href="{{ route('dashboard') }}">Home</a>
                        </li>
                        <li class="breadcrumb-item" aria-current="page">Role</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <!-- END Hero -->
@endsection
@section('content')
    <div class="profile">
        <form method="POST" action="{{ route('roles.update', $role->id) }}" enctype="multipart/form-data">
            @method('PUT')
            @csrf
            <div class="container">
                <div class="row">
                    <div class="col-md-3">
                        <div class="user-profile-box">
                            <div class="profile-wrapper align-center">
                                <div class="profile-header clearfix">
                                    <div class="user-picture">
                                        <img src="//images2.imgbox.com/d2/3c/VpjmQa5y_o.png" id="user-picture"
                                             alt=""
                                             class="round auth-w">
                                    </div>
                                    <div class="user-name mgt-10 text-capitalize">
                                        <h4>{{ $role->name }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8 offset-md-1 mt-sm-5 mt-5 mt-lg-0 mt-xl-0">
                        <div class="update-profile">
                            <div class="header">
                                <h2 class="capitalize">Update Role</h2>
                            </div>
                            <hr>
                            <div class="profile-data">
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-4">
                                            <label for="firstname">Name</label>
                                        </div>
                                        <div class="col-8">
                                            <input type="text" class="form-control form-field capitalize" id="name"
                                                   name="name"
                                                   aria-describedby="emailHelp" placeholder="Role name"
                                                   value="{{ $role->name }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-4">
                                            <label for="lastname">Description</label>
                                        </div>
                                        <div class="col-8">
                                            <input type="text" class="form-control form-field capitalize"
                                                   id="description"
                                                   name="description"
                                                   aria-describedby="emailHelp" placeholder="Role description"
                                                   value="{{ $role->description }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="row update-btn mt-lg-5 mt-sm-0">
                                    <div class="col-md-6 mt-3">
                                        <button type="submit" class="btn btn-primary full-w">
                                            <span class="btn-field font-weight-normal fa-edit pr-4 position-relative">Update</span>
                                        </button>
                                    </div>
                                    <div class="col-md-6 mt-3">
                                        <button class="btn btn-danger full-w delete-role"
                                                data-role="{{ $role->id }}">
                                            <span
                                                class="btn-field font-weight-bold fa-trash-alt pr-3 position-relative">Delete</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
{{--                <div class="row mt-5">--}}
{{--                    <div class="col-12">--}}
{{--                        <div class="card">--}}
{{--                            <div class="card-header">--}}
{{--                                <h3 class="card-title float-left">Assign Permissions</h3>--}}
{{--                                <button type="button" class="btn btn-primary mgl-10 round" title="Add Permission">--}}
{{--                                    <a href="/permissions/create" class="link btn btn-primary mgl-10 round d-none" title="Add Permission"><i class="fas fa-plus"></i></a>--}}
{{--                                </button>--}}
{{--                            </div>--}}
{{--                            <!-- /.card-header -->--}}
{{--                            <div class="card-body table-responsive">--}}
{{--                                <table id="permissions-data"--}}
{{--                                       class="table table-bordered table-striped table-valign-middle capitalize">--}}
{{--                                    <thead>--}}
{{--                                    <tr>--}}
{{--                                        <th></th>--}}
{{--                                        <th>Name</th>--}}
{{--                                        <th>Method</th>--}}
{{--                                        <th>Controller</th>--}}
{{--                                        <th>Assined</th>--}}
{{--                                    </tr>--}}
{{--                                    </thead>--}}
{{--                                </table>--}}
{{--                            </div>--}}
{{--                            <!-- /.card-body -->--}}
{{--                        </div>--}}
{{--                        <!-- /.card -->--}}
{{--                    </div>--}}
{{--                    <!-- /.col -->--}}
{{--                </div>--}}
                <!-- /.row -->
            </div>
        </form>
    </div>
@endsection
