@extends('layouts.simple')

@section('content')

    <!-- Page Content -->
    <div class="bg-image" style="background-image: url('assets/media/photos/photo6@2x.jpg');">
        <div class="hero-static bg-white-95">
            <div class="content">
                <div class="row justify-content-center">
                    <div class="col-md-8 col-lg-6 col-xl-4">
                        <!-- Sign In Block -->
                        <div class="block block-themed block-fx-shadow mb-0" style="background-color: #d7d7d7">
                            <div class="block-header">
                                <h3 class="block-title">Sign In</h3>
                                <div class="block-options">
                                    <a class="btn-block-option font-size-sm" href="op_auth_reminder.html">Forgot
                                        Password?</a>
                                    <a class="btn-block-option" href="op_auth_signup.html" data-toggle="tooltip"
                                       data-placement="left" title="New Account">
                                        <i class="fa fa-user-plus"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="block-content">
                                <div class="p-sm-3 px-lg-4 py-lg-5">
{{--                                    <h1 class="mb-2">OneUI</h1>--}}
                                    <img src="{{ asset('media/circetwhite.png') }}" alt="" style="width: 100%">
                                    <p>Welcome, please login.</p>

                                    <!-- Sign In Form -->
                                    <!-- jQuery Validation (.js-validation-signin class is initialized in js/pages/op_auth_signin.min.js which was auto compiled from _es6/pages/op_auth_signin.js) -->
                                    <!-- For more info and examples you can check out https://github.com/jzaefferer/jquery-validation -->
                                    <form class="js-validation-signin" action="{{ route('login') }}" method="POST">
                                        @csrf
                                        <div class="py-3">
                                            <div class="form-group">
                                                <input type="text" class="form-control form-control-alt form-control-lg @error('email') is-invalid @enderror"
                                                       id="email" name="email" value="{{ old('email') ?? 'a@a.a' }}"
                                                       required autocomplete="email" autofocus placeholder="Email">
                                                @error('email')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                            <div class="form-group">
                                                <input type="password"
                                                       class="form-control form-control-alt form-control-lg @error('password') is-invalid @enderror"
                                                       id="password" name="password" value="password"
                                                       required autocomplete="current-password" placeholder="Password">
                                                @error('password')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                            <div class="form-group">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" name="remember"
                                                           id="remember" {{ old('remember') ? 'checked' : '' }}>
                                                    <label class="custom-control-label font-w400" for="remember">Remember Me</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-md-6 col-xl-5">
                                                <button type="submit" class="btn btn-block btn-primary">
                                                    <i class="fa fa-fw fa-sign-in-alt mr-1"></i> Sign In
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                    <!-- END Sign In Form -->
                                </div>
                            </div>
                        </div>
                        <!-- END Sign In Block -->
                    </div>
                </div>
            </div>
            <div class="content content-full font-size-sm text-muted text-center">
                <strong>OneUI 4.3</strong> &copy; <span data-toggle="year-copy"></span>
            </div>
        </div>
    </div>
    <!-- END Page Content -->
@endsection
