@extends('layouts.backend')

@section('page-title')
    Create Role
@endsection

@section('css_after')
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/croppie/2.6.4/croppie.min.css">
@endsection
@section('content-header')
    <!-- Hero -->
    <div class="bg-body-light">
        <div class="content content-full">
            <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
                <div class="flex-sm-fill">
                    <h1 class="h3 my-2 d-inline-block">Rôles</h1>
                </div>
                <nav class="flex-sm-00-auto ml-sm-3" aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-alt">
                        <li class="breadcrumb-item">
                            <a class="link-fx" href="{{ route('dashboard') }}">Tableau de bord</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a class="link-fx" href="{{ route('roles.index') }}">Rôles</a>
                        </li>
                        <li class="breadcrumb-item" aria-current="page">Créer</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <!-- END Hero -->
@endsection
@section('content')
    <div class="profile">
        <form method="POST" action="{{ route('roles.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="container">
                <div class="row">
                    <div class="col-md-8 offset-md-2">
                        <div class="update-profile">
                            <div class="header">
                                <h2 class="capitalize">Créer Rôle</h2>
                            </div>
                            <hr>
                            <div class="profile-data">
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-4">
                                            <label for="name">Nom</label>
                                        </div>
                                        <div class="col-8">
                                            <input type="text" class="form-control form-field" id="name"
                                                   name="name"
                                                   aria-describedby="emailHelp" placeholder="Nom">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-4">
                                            <label for="description">Description</label>
                                        </div>
                                        <div class="col-8">
                                            <input type="text" class="form-control form-field" id="description"
                                                   name="description"
                                                   aria-describedby="emailHelp" placeholder="Description">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 offset-md-3">
                                        <button type="submit" class="btn btn-primary mt-5 full-w">
                                            <span class="mr-3">Créer</span>
                                            <i class="fas fa-shield-alt"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
@section('js_after')
    <script src="{{ asset("/js/plugins/crop.js") }}"></script>
    <script src="{{ asset("/js/plugins/users/profile.js") }}"></script>
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
