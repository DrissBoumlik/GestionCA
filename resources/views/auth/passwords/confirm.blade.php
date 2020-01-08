@extends('layouts.simple')

@section('page-title')
    {{ __('Confirmation de mot de passe') }}
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
                                <h3 class="block-title">{{ __('Confirmation de mot de passe') }}</h3>
                            </div>
                            <div class="block-content">
                                <div class="p-sm-3 px-lg-4 py-lg-5">
                                    <img src="{{ asset('media/circetwhite.png') }}" alt="" style="width: 100%">

                                    <div class="card-body">
                                        {{ __('Veuillez confirmer votre mot de passe avant de continuer.') }}

                                        <form method="POST" action="{{ route('password.confirm') }}">
                                            @csrf

                                            <div class="form-group row">
                                                <label for="password" class="col-md-12 col-form-label">{{ __('Mot de passe') }}</label>

                                                <div class="col-md-12">
                                                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">

                                                    @error('password')
                                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="form-group row mb-0">
                                                <div class="col-md-12">
                                                    <button type="submit" class="btn btn-primary col-md-12">
                                                        {{ __('Confirmation de mot de passe') }}
                                                    </button>

                                                    @if (Route::has('password.request'))
                                                        <a class="btn btn-link" href="{{ route('password.request') }}">
                                                            {{ __('Mot de passe oublié?') }}
                                                        </a>
                                                    @endif
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                    <!-- END Sign In Form -->
                                </div>
                            </div>
                        </div>
                        <!-- END Sign In Block -->
                    </div>
                </div>
            </div>
            <div class="content content-full font-size-sm text-muted text-center">
                <strong>CIRCET</strong> &copy; <span data-toggle="year-copy"></span>
            </div>
        </div>
    </div>
@endsection
