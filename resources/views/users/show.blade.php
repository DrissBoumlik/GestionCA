@extends('layouts.backend')

@section('page-title')
    User - {{ fullName($user, ' ') }}
@endsection

@section('css_after')
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/7.2.0/sweetalert2.min.css">
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/croppie/2.6.4/croppie.min.css">
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
@section('content-header')
    <!-- Hero -->
    <div class="bg-body-light">
        <div class="content content-full">
            <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
                <div class="flex-sm-fill">
                    <h1 class="h3 my-2 d-inline-block">Profile</h1>
                </div>
                <nav class="flex-sm-00-auto ml-sm-3" aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-alt">
                        <li class="breadcrumb-item">
                            <a class="link-fx" href="/">Home</a>
                        </li>
                        <li class="breadcrumb-item" aria-current="page">Profile</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <!-- END Hero -->
@endsection
@section('content')
    <div class="user-profile profile">
        <form method="POST" action="/users/{{ $user->id }}" enctype="multipart/form-data">
            @method('PUT')
            @csrf
            <div class="container">
                <div class="row">
                    <div class="col-md-3">
                        <div class="user-profile-box">
                            <div class="profile-wrapper align-center">
                                <div class="profile-header clearfix">
                                    <div class="user-picture">
                                        <img
                                            src="{{ $user->picture ?? '//images2.imgbox.com/75/b0/NyZk9Glf_o.png' }}"
                                            id="user-picture" alt=""
                                            class="round auth-w">
                                        <label for="picture" class="pointer">
                                            <input type="file" class="custom-file-input d-none" id="picture"
                                                   accept="image/png, image/jpeg">
                                            <input type="hidden" class="custom-file-input d-none" id="picture-data"
                                                   name="picture">
                                        </label>
                                    </div>
                                    <div class="user-name mgt-10 text-capitalize">
                                        <h4>{{ $user->firstname . ' ' . $user->lastname}}</h4>
                                    </div>
                                    <div class="user-role mgt-10 text-capitalize">
                                        <h4>({{ $user->role->name }})</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8 offset-md-1 mt-sm-5 mt-5 mt-lg-0 mt-xl-0">
                        <div class="update-profile">
                            <div class="header">
                                <h2 class="capitalize">Update Profile</h2>
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
                                                   aria-describedby="emailHelp" placeholder="First name"
                                                   value="{{ $user->firstname }}">
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
                                                   aria-describedby="emailHelp" placeholder="Last name"
                                                   value="{{ $user->lastname }}">
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
                                                   aria-describedby="emailHelp" placeholder="Enter email"
                                                   value="{{ $user->email }}">
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
                                                    class="form-control capitalize form-field @error('role') is-invalid @enderror">
                                                @foreach($roles as $role)
                                                    <option class="capitalize"
                                                            value="{{ $role->id }}" {{ $user->role->name == $role->name ? 'selected' : '' }}>
                                                        {{ $role->name }}
                                                    </option>
                                                @endforeach
                                            </select>
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
                                                        value="male" {{ $user->gender == 'male' ? 'selected' : '' }}>
                                                    Male
                                                </option>
                                                <option class="capitalize"
                                                        value="female" {{ $user->gender == 'female' ? 'selected' : '' }}>
                                                    Female
                                                </option>
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
                                            <label for="status-{{ $user->id }}">
                                                <input class='data-status d-none' id="status-{{ $user->id }}"
                                                       type='checkbox'
                                                       {{ ($user->status ? 'checked' : '') }}
                                                       name='status'>
                                                <span class='status pointer'></span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row update-btn mt-lg-5 mt-sm-0">
                                    <div class="col-md-4 mt-3">
                                        <button type="submit" class="btn btn-primary full-w">
                                            <span class="btn-field font-weight-normal fa-edit pr-4 position-relative">Update</span>
                                        </button>
                                    </div>
                                    <div class="col-md-4 mt-3">
                                        <button class="btn btn-danger full-w delete-user"
                                                data-user="{{ $user->id }}">
                                            <span
                                                class="btn-field font-weight-bold fa-trash-alt pr-3 position-relative">Delete</span>
                                        </button>
                                    </div>
                                    <div class="col-md-4 mt-3">
                                        <button class="btn btn-warning full-w" data-toggle="modal" type="button"
                                                data-target="#resetPassModal">
                                            <span
                                                class="btn-field font-weight-bold fa-exclamation-triangle pr-3 position-relative">Reset Password</span>
                                        </button>
                                        <!-- RESET PASSWORD Modal -->
                                        <!-- Modal -->
                                        <div class="modal fade" id="resetPassModal" tabindex="-1" role="dialog"
                                             aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="exampleModalLongTitle">Reset
                                                            Password</h5>
                                                        <button type="button" class="close" data-dismiss="modal"
                                                                aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="form-group">
                                                            <div class="row">
                                                                <div class="col-4">
                                                                    <label for="password">Password</label>
                                                                </div>
                                                                <div class="col-8">
                                                                    <input type="text" class="form-control form-field"
                                                                           name="password"
                                                                           id="password" placeholder="Password">
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
                                                            <button type="submit" class="btn btn-primary full-w">
                                                                <span class="mr-3">Update</span>
                                                                <i class="far fa-edit"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <!-- CROP Modal -->
    <div class="modal" id="cropModal" tabindex="-1" role="dialog" aria-labelledby="cropModal"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cropModal">Crop Image</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="crop-box"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary crop_image">Save changes</button>
                </div>
            </div>
        </div>
    </div>
@endsection
