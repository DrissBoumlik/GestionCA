@extends('layouts.backend')

@section('page-title')
    Update Skills
@endsection

@section('css_after')
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/7.2.0/sweetalert2.min.css">
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
        <form method="POST" action="/updateSkills" class="w-100">
            @method('PUT')
            @csrf
            <div class="row">
                <div class="col-10 offset-1 mb-5 align-center">
                    <h3>Update your skills :</h3>
                    <div class="select2-wrapper d-inline">
                        <select name="skills[]" id="skills" class="w-50"></select>
                    </div>
                </div>
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-10 offset-1 mb-5 align-center">
                    <h3>Update your Top skills :</h3>
                    <div class="select2-wrapper d-inline">
                        <select name="topSkills[]" id="topSkills" class="w-50"></select>
                    </div>
                </div>
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-10 offset-1 mb-5 align-center">
                    <button type="button" class="btn btn-primary w-25">
                        <span class="btn-field font-weight-normal fa-edit pr-4 position-relative update-skills">Update</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
@endsection

@section('js_after')
    <!-- Alert -->
    <script src="//cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/7.2.0/sweetalert2.all.min.js"></script>
    <!-- Select -->
    <script src="{{ asset("/add_ons/select2/js/select2.min.js") }}"></script>
    <script src="{{ asset("/add_ons/skills/skill.js") }}"></script>
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
        });
    </script>
@endsection
