@extends('layouts.simple')

@section('page-title')
    {{ __('Réinitialiser le mot de passe') }}
@endsection

@section('content')


    <div class="bg-image">
        <div class="hero-static bg-white-95">
            <div class="content">
                <div class="row justify-content-center">
                    <div class="col-md-8 col-lg-6 col-xl-4">
                        <!-- Sign In Block -->
                        <div class="block block-themed block-fx-shadow mb-0" style="background-color: #d7d7d7">
                            <div class="block-header">
                                <h3 class="block-title">{{ __('Réinitialiser') }}</h3>
                                <div class="block-options">
                                    <a class="btn-block-option font-size-sm" href="{{ route('login') }}">Se connecter</a>
                                </div>
                            </div>
                            <div class="block-content">
                                <div class="p-sm-3 px-lg-4 py-lg-5">
                                    <img src="{{ asset('media/logo-placeholder.png') }}" alt="" style="width: 100%">
                                    <form class="js-validation-signin" action="{{ route('password.update') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="token" value="{{ $token }}">
                                        <div class="form-group row">
                                            <label for="email"
                                                   class="col-md-12 col-form-label">{{ __('E-Mail Adresse') }}</label>

                                            <div class="col-md-12">
                                                <input id="email" type="email"
                                                       class="form-control @error('email') is-invalid @enderror"
                                                       name="email" value="{{ $email ?? old('email') }}" required
                                                       autocomplete="email" autofocus>

                                                @error('email')
                                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="password"
                                                   class="col-md-12 col-form-label">{{ __('Mot de passe') }}</label>

                                            <div class="col-md-12">
                                                <input id="password" type="password"
                                                       class="form-control @error('password') is-invalid @enderror"
                                                       name="password" required autocomplete="new-password">

                                                @error('password')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="password-confirm"
                                                   class="col-md-12 col-form-label">{{ __('Confirmation du mot de pase') }}</label>

                                            <div class="col-md-12">
                                                <input id="password-confirm" type="password" class="form-control"
                                                       name="password_confirmation" required
                                                       autocomplete="new-password">
                                            </div>
                                        </div>

                                        <div class="form-group row mb-0">
                                            <div class="col-md-12">
                                                <button type="submit" class="btn btn-primary col-md-12">
                                                    {{ __('Réinitialiser le mot de passe') }}
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
@endsection
