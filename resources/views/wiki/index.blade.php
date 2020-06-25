@extends('layouts.backend')

@section('page-title')
    Paramètres
@endsection

@section('css_after')
@endsection

@section('content-header')
    <!-- Hero -->
    <div class="bg-body-light">
        <div class="content content-full">
            <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
                <div class="flex-sm-fill">
                    <h1 class="h3 my-2 d-inline-block">Paramètres</h1>
                </div>
                <nav class="flex-sm-00-auto ml-sm-3" aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-alt">
                        <li class="breadcrumb-item">
                            <a class="link-fx" href="{{ route('dashboard') }}">Tableau de board</a>
                        </li>
                        <li class="breadcrumb-item" aria-current="page">Paramètres</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <!-- END Hero -->
@endsection

@section('content')
    <div class="container-fluid">
        <div class="wiki py-5 px-3">
            <div class="container">
                <div class="row">
                    @foreach ($wikiData as $page)
                        <div class="pages">
                            <div class="col-12">
                                <div class="header">
                                    <h3 class="capitalize mb-2"><i class="fas fa-file-alt"></i> Page : {{ $page['pageTitle'] }}</h3>
                                </div>
                                <div class="body">
                                    @foreach ($page['items'] as $item)
                                        <div class="card p-3 pb-4">
                                            <div class="card-header pb-2">
                                                <div class="table-title">
                                                    <span class="font-weight-bold">
                                                        <h4 class="mb-2 capitalize d-inline-block"><i class="fas fa-tasks"></i> {{ $item['title'] }}</h4>
                                                    </span> -
                                                    <a href="{{ url('/') . $item['link'] }}" target="_blank" class="underline table-link">Lien</a>
                                                </div>
                                            </div>
                                            <!-- /.card-header -->
                                            <div class="card-body">
                                                <span class="rules">
                                                    <h5 class="mb-2 capitalize">spécifications</h5>
                                                </span>
                                                @foreach ($item['specifications'] as $key => $rule)
                                                    @if (!is_array($rule) && $key == 'content')
{{--                                                        !is_array($rule))--}}
                                                        <p>{{ file_get_contents('https://loripsum.net/api/plaintext/short/1') }}</p>
                                                    @elseif($key == 'content')
                                                        <ul>
                                                        @for ($i = 0; $i < 2; $i++)
                                                            <li>{{ file_get_contents('https://loripsum.net/api/plaintext/short/1') }}</li>
                                                        @endfor
                                                        </ul>
{{--                                                        <hr>--}}
{{--                                                        <h5 class="mb-2 capitalize">spécifications supplementaires</h5>--}}
{{--                                                        @if (is_array($rule))--}}
{{--                                                            <p>{{ $key }}</p>--}}
{{--                                                            @foreach ($rule as $user)--}}

{{--                                                            @endforeach--}}
{{--                                                        @endif--}}
                                                    @endif
                                                @endforeach
                                            </div>
                                            <!-- /.card-body -->
                                        </div>
{{--                                        @break(true)--}}
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js_after')

@endsection
