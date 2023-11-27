@extends('layouts.simple')

@section('page-title')
    Se connecter
@endsection

@section('content')

    <!-- Page Content -->
    <div class="bg-image">
        <div class="hero-static bg-white-95">
            <div class="content">
                <div class="row justify-content-center">
                    <div class="col-md-8 col-lg-6 col-xl-4">
                        <!-- Sign In Block -->
                        <div class="block block-themed block-fx-shadow mb-0" style="background-color: #d7d7d7">
                            <div class="block-header">
                                <h3 class="block-title">Se connecter</h3>
                                <div class="block-options">
                                    <a class="btn-block-option font-size-sm" href="{{ route('password.request') }}">Mot de passe oublié?</a>
                                </div>
                            </div>
                            <div class="block-content">
                                <div class="p-sm-3 px-lg-4 py-lg-5">
                                    <img src="{{ asset('media/logo-placeholder.png') }}" alt="" style="width: 100%">
                                    <p>Bienvenue, veuillez vous connecter.</p>
                                    <form class="js-validation-signin" action="{{ route('login') }}" method="POST">
                                        @csrf
                                        <div class="py-3">
                                            <div class="form-group">
                                                <input type="text"
                                                       class="form-control form-control-alt form-control-lg @error('email') is-invalid @enderror"
                                                       id="email" name="email" value="{{ old('email') }}"
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
                                                       id="password" name="password" value=""
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
                                                    <label class="custom-control-label font-w400" for="remember">Se souvenir de moi</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-md-12 col-xl-12">
                                                <button type="submit" class="btn btn-block btn-primary">
                                                    <i class="fa fa-fw fa-sign-in-alt mr-1"></i> Se connecter
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
                <strong>COMPANY</strong> &copy; <span data-toggle="year-copy"></span>
            </div>
        </div>
    </div>
    <!-- END Page Content -->
@endsection
