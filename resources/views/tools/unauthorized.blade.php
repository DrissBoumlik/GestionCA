@extends('layouts.backend')

@section('page-title')
    Unauthorized
@endsection

@section('js_after')
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
                    <h1 class="h3 my-2 d-inline-block">Accueil</h1>
                </div>
                <nav class="flex-sm-00-auto ml-sm-3" aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-alt">
                        <li class="breadcrumb-item">
                            <a class="link-fx" href="/">Accueil</a>
                        </li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <!-- END Hero -->
@endsection

@section('content')
    <div class="box-wrapper">
        <div class="box-container">
            <div class="container">
                <div class="row">
                    <div class="col-6 offset-3">
                        <div class="box-content">
                            <div class="box-content-wrapper align-center">
                                <div class="box-header clearfix">
                                    <div class="box-picture d-inline-block position-relative">
                                        <img src="//images2.imgbox.com/19/67/UJ4adhZl_o.png" id="user-picture" alt=""
                                             class="auto-w">
                                    </div>
                                    <div class="mgt-10">
                                        <h4>Vous n'êtes pas autorisé à afficher cette page !</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
