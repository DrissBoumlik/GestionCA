@extends('layouts.backend')

@section('page-title')
    Create User
@endsection

@section('css_after')
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/7.2.0/sweetalert2.min.css">
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/croppie/2.6.4/croppie.min.css">
@endsection

@section('content-header')
    <!-- Hero -->
    <div class="bg-body-light">
        <div class="content content-full">
            <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
                <div class="flex-sm-fill">
                    <h1 class="h3 my-2 d-inline-block">Users</h1>
                </div>
                <nav class="flex-sm-00-auto ml-sm-3" aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-alt">
                        <li class="breadcrumb-item">
                            <a class="link-fx" href="{{ route('dashboard') }}">Home</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a class="link-fx" href="{{ route('users.create') }}">Users</a>
                        </li>
                        <li class="breadcrumb-item" aria-current="page">Create</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <!-- END Hero -->
@endsection
@section('content')
    <div class="profile">
        <form method="POST" action="{{ route('users.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="container">
                <div class="row">
                    <div class="col-md-8 offset-md-2">
                        <div class="update-profile">
                            <div class="header">
                                <h2 class="capitalize">Create Profile</h2>
                            </div>
                            <hr>
                            <div class="profile-data">
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-4">
                                            <label for="firstname">First name</label>
                                        </div>
                                        <div class="col-8">
                                            <input type="text" class="form-control form-field" id="firstname"
                                                   name="firstname"
                                                   aria-describedby="emailHelp" placeholder="First name">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-4">
                                            <label for="lastname">Last name</label>
                                        </div>
                                        <div class="col-8">
                                            <input type="text" class="form-control form-field" id="lastname"
                                                   name="lastname"
                                                   aria-describedby="emailHelp" placeholder="Last name">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-4">
                                            <label for="gender">Gender</label>
                                        </div>
                                        <div class="col-8">
                                            <select name="gender" id="gender"
                                                    class="form-control capitalize form-field @error('gender') is-invalid @enderror">
                                                <option class="capitalize"
                                                        value="male">
                                                    Male
                                                </option>
                                                <option class="capitalize"
                                                        value="female">
                                                    Female
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-4">
                                            <label for="role">Role</label>
                                        </div>
                                        <div class="col-8">
                                            <select name="role" id="role"
                                                    class="form-control form-field capitalize @error('role') is-invalid @enderror">
                                                @foreach($roles as $role)
                                                    <option class="capitalize"
                                                            value="{{ $role->id }}">
                                                        {{ $role->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group" id="agence_name" style="display: none">
                                    <div class="row">
                                        <div class="col-4">
                                            <label for="role">Agence</label>
                                        </div>
                                        <div class="col-8">
                                            <select name="agence_name" id="agence_name"
                                                    class="form-control capitalize form-field @error('agence_name') is-invalid @enderror">
                                                <option value=""></option>
                                                @foreach(agencesList() as $agence)
                                                    <option class="capitalize"
                                                            value="{{ $agence['name'] }}">
                                                        {{ $agence['name'] }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-4">
                                            <label for="status">Status</label>
                                        </div>
                                        <div class="col-8">
                                            <label for="status" class="m-0">
                                                <input class='data-status d-none' id="status" type='checkbox'
                                                       name='status'>
                                                <span class='status'></span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-4">
                                            <label for="email">Email</label>
                                        </div>
                                        <div class="col-8">
                                            <input type="email" class="form-control form-field" id="email"
                                                   name="email"
                                                   aria-describedby="emailHelp" placeholder="Enter email">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-4">
                                            <label for="password">Password</label>
                                        </div>
                                        <div class="col-8">
                                            <input type="password" class="form-control form-field" name="password"
                                                   id="password" placeholder="Password">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-4">
                                            <label for="password_confirmation">Password Confirmation</label>
                                        </div>
                                        <div class="col-8">
                                            <input type="password" class="form-control form-field"
                                                   name="password_confirmation"
                                                   id="password_confirmation" placeholder="Password Confirmation">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 offset-md-3">
                                        <button type="submit" class="btn btn-primary mt-5 full-w">
                                            <span class="mr-3">Create</span>
                                            <i class="fas fa-user-plus"></i>
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
    <script src="//cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/7.2.0/sweetalert2.all.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/croppie/2.6.4/croppie.min.js"></script>
    <script src="{{ asset("/add_ons/crop.js") }}"></script>
    <script src="{{ asset("/add_ons/users/profile.js") }}"></script>
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
