@extends('layouts.backend')

@section('page-title')
    Profile
@endsection

@section('css_after')
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/7.2.0/sweetalert2.min.css">
    <!-- Croppie -->
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/croppie/2.6.4/croppie.min.css">
    <!-- Select2 -->
    <link href="{{ asset("/add_ons/select2/css/select2.min.css") }}" rel="stylesheet"/>
@endsection
@section('js_after')
    <!-- SweetAlert2 -->
    <script src="//cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/7.2.0/sweetalert2.all.min.js"></script>
    <!-- Croppie -->
    <script src="//cdnjs.cloudflare.com/ajax/libs/croppie/2.6.4/croppie.min.js"></script>
    <!-- Select2 -->
    <script src="{{ asset("/add_ons/select2/js/select2.min.js") }}"></script>

    <script src="{{ asset("/add_ons/crop.js") }}"></script>
    <script src="{{ asset("/add_ons/users/profile.js") }}"></script>
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
        })
    </script>
@endsection

@section('content-header')
    <!-- Hero -->
    <div class="bg-body-light">
        <div class="content content-full">
            <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
                <div class="flex-sm-fill">
                    <h1 class="h3 my-2 d-inline-block">Profil</h1>
                </div>
                <nav class="flex-sm-00-auto ml-sm-3" aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-alt">
                        <li class="breadcrumb-item">
                            <a class="link-fx" href="{{ route('dashboard') }}">Tableau de board</a>
                        </li>
                        <li class="breadcrumb-item" aria-current="page">Profil</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <!-- END Hero -->
@endsection

@section('content')
    <div class="user-profile profile">
        <div class="profile-container">
            <form method="POST" action="/updatePicture" enctype="multipart/form-data">
                @method('PUT')
                @csrf
                <div class="container">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="user-profile-box">
                                <div class="profile-wrapper align-center">
                                    <div class="profile-header clearfix">
                                        <div class="user-picture">
                                            <img src="{{ getPicture() }}" id="user-picture" alt=""
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
                                        <div class="update-btn">
                                            <button type="submit" class="btn btn-primary full-w">
                                                <span
                                                    class="btn-field font-weight-normal fa-image pr-4 position-relative">Modifié l'image</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8 offset-md-1 mt-sm-5 mt-5 mt-lg-0 mt-xl-0">
                            <div class="update-profile">
                                <div class="header">
                                    <h2 class="capitalize">Voir Profil</h2>
                                </div>
                                <hr>
                                <div class="profile-data">
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-4">
                                                <label for="firstname">First name</label>
                                            </div>
                                            <div class="col-8">
                                                <label class="form-control form-field" id="firstname"
                                                       name="firstname" disabled
                                                       aria-describedby="emailHelp" placeholder="First name">
                                                    {{ $user->firstname }}
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-4">
                                                <label for="lastname">Last name</label>
                                            </div>
                                            <div class="col-8">
                                                <label class="form-control form-field" id="lastname"
                                                       name="lastname" disabled
                                                       aria-describedby="emailHelp" placeholder="Last name">
                                                    {{ $user->lastname }}
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
                                                <label class="form-control form-field capitalize" id="email"
                                                       name="email" disabled
                                                       aria-describedby="emailHelp" placeholder="Enter email">
                                                    {{ $user->email }}
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-4">
                                                <label for="gender">Gender</label>
                                            </div>
                                            <div class="col-8">
                                                <label class="form-control form-field capitalize"
                                                       id="gender" name="gender" disabled
                                                       aria-describedby="emailHelp" placeholder="Enter email">
                                                    {{ $user->gender }}
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-4">
                                                <label for="role">Role</label>
                                            </div>
                                            <div class="col-8">
                                                <label class="form-control form-field capitalize"
                                                       id="role" name="role" disabled
                                                       aria-describedby="emailHelp" placeholder="Enter role">
                                                    {{ $user->role->name }}
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-4">
                                                <label for="status">Status</label>
                                            </div>
                                            <div class="col-8">
                                                <label
                                                    class="alert alert-{{ ($user->status ? 'success' : 'danger') }}">{{ $user->status ? 'Active' : 'Inactive' }}</label>
                                                {{--                                                <label for="status-{{ $user->id }}">--}}
                                                {{--                                                    <input class='data-status d-none' id="status-{{ $user->id }}"--}}
                                                {{--                                                           type='checkbox'--}}
                                                {{--                                                           {{ ($user->status ? 'checked' : '') }}--}}
                                                {{--                                                           name='status' disabled>--}}
                                                {{--                                                    <span class='status'></span>--}}
                                                {{--                                                </label>--}}
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
    </div>
    <hr class="w-75">
    <!-- CROP Modal -->
    <div class="modal" id="cropModal" tabindex="-1" role="dialog" aria-labelledby="cropModal"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cropModal">Modifié Image</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="crop-box"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                    <button type="button" class="btn btn-primary crop_image">Sauvgarder les modification</button>
                </div>
            </div>
        </div>
    </div>
@endsection
