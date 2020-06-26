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
            <div class="pages">
                <div class="container">
                    <div class="row">
                        @php $faker = Faker\Factory::create(); @endphp
                        @foreach ($wikiData as $page)
                            <div class="page mb-4 mt-3">
                                <div class="col-12">
                                    <div class="header">
                                        <h3 class="capitalize mb-2"><i class="fas fa-file-alt pr-2"></i> Page
                                            : {{ $page['pageTitle'] }}</h3>
                                    </div>
                                    <div class="body">
                                        @foreach ($page['items'] as $key => $item)
                                            <div class="card pt-3 ml-4">
                                                <div class="card-header pb-2">
                                                    <div class="table-title">
                                                        <h4 class="font-weight-bold mb-2 capitalize d-inline-block">
{{--                                                            <i class="fas fa-table medium-icon"></i>--}}
                                                            <i class="fas fa-angle-right pr-2"></i>
                                                            {{ $item['title'] }}</h4>
                                                        <span class="separator px-2">|</span>
{{--                                                        <i class="fas fa-long-arrow-alt-right px-2"></i>--}}
                                                        <a href="{{ url('/') . $item['link'] }}" target="_blank"
                                                           class="underline table-link">Lien
                                                            <i class="fas fa-link icon-link"></i>
                                                            {{--                                                            <i class="fas fa-external-link-alt icon-link"></i>--}}
                                                        </a>
                                                    </div>
                                                </div>
                                                <!-- /.card-header -->
                                                <div class="card-body ml-4">
                                                    <span class="rules">
                                                        <h5 class="mb-2 capitalize">spécifications</h5>
                                                    </span>
                                                    @foreach ($item['specifications'] as $key => $rule)
                                                        @if (!is_array($rule) && $key == 'content')
                                                            <p>{{ $faker->text(200) }}</p>
                                                        @elseif ($key == 'content')
                                                            <ul class="rules-list pl-4">
                                                                @for ($i = 0; $i < 2; $i++)
                                                                    <li class="">{{ $faker->text(100) }}</li>
                                                                @endfor
                                                            </ul>
                                                        @endif
                                                    @endforeach
                                                </div>
                                                <!-- /.card-body -->
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <hr class="w-100">
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js_after')

@endsection
