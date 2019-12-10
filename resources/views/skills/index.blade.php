@extends('layouts.backend')

@section('page-title')
    Skills
@endsection

@section('css_after')
    <!-- DataTables -->
    <link rel="stylesheet"
          href={{ asset("/add_ons/datatables-bs4/css/dataTables.bootstrap4.css") }}>
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/7.2.0/sweetalert2.min.css">
    <link href="{{ asset("/add_ons/select2/css/select2.min.css") }}" rel="stylesheet"/>
@endsection

@section('content-header')
    <!-- Hero -->
    <div class="bg-body-light">
        <div class="content content-full">
            <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
                <div class="flex-sm-fill">
                    <h1 class="h3 my-2 d-inline-block">Skills</h1>
                    <a href="/users/create" class="link btn btn-primary mgl-10 round d-none" title="Add User"><i
                            class="fas fa-plus"></i></a>
                </div>
                <nav class="flex-sm-00-auto ml-sm-3" aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-alt">
                        <li class="breadcrumb-item">
                            <a class="link-fx" href="/">Home</a>
                        </li>
                        <li class="breadcrumb-item" aria-current="page">Skills</li>
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
                    <div class="card-header">
                        <h3 class="card-title float-left">Add Skills</h3>
                        <a href="#" class="link btn btn-primary mgl-10 round add-skill" data-target='#skillForm' data-toggle='modal' title="Add User"><i
                                class="fas fa-plus"></i></a>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body table-responsive">
                        <table id="skills-data"
                               class="table table-bordered table-striped table-valign-middle capitalize">
                            <thead>
                            <tr>
                                <th>Name</th>
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
    <!-- Skill Form Modal -->
    <!-- Modal -->
    <div class="modal fade" id="skillForm" tabindex="-1" role="dialog"
         aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="header-form">Add / Edit Skill</h5>
                    <button type="button" class="close" data-dismiss="modal"
                            aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-4">
                                <label for="skill">Skill</label>
                            </div>
                            <div class="col-8">
                                <input type="text" class="form-control form-field"
                                       name="skill"
                                       data-id=""
                                       id="skill" placeholder="Skill">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="col-md-6">
                        <button type="button" class="btn btn-secondary full-w"
                                data-dismiss="modal">
                            <span class="mr-3">Close</span>
                            <i class="far fa-times-circle"></i>
                        </button>
                    </div>
                    <div class="col-md-6">
                        <button type="submit" class="btn btn-primary full-w update-skill">
                            <span class="mr-3" id="header-btn-send">Update</span>
                            <i class="far fa-edit"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js_after')
    <!-- DataTables -->
    <script src={{ asset("/add_ons/datatables/jquery.dataTables.js") }}></script>
    <script src={{ asset("/add_ons/datatables-bs4/js/dataTables.bootstrap4.js") }}></script>
    <script src="{{ asset("/add_ons/skills/datatable.js") }}"></script>
    <!-- Alert -->
    <script src="//cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/7.2.0/sweetalert2.all.min.js"></script>
    <!-- Select -->
    <script src="{{ asset("/add_ons/select2/js/select2.min.js") }}"></script>
    <script>
        $(function () {
            function feedBack(message, status) {
                swal(
                    status.replace(/^\w/, c => c.toUpperCase()) + '!',
                    message,
                    status
                )
            }

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
        });
    </script>
@endsection
