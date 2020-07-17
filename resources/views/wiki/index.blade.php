@extends('layouts.backend')

@section('page-title')
  Paramètres
@endsection

@section('js_after')
  <script src="{{ asset('/add_ons/wiki/index.js') }}"></script>
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
      <div class="wiki-wrapper">
        <div class="wiki-summary-wrapper">
          <button type="button" class="btn btn-sm btn-dual toggle-summary">
            {{--                        <i class="fa fa-fw fa-bars"></i>--}}
            Index Des Pages
          </button>
          <!-- From Right Block Modal -->
          <div class="wiki-summary" id="wiki-summary">
            <div class="">
              <div class="summary-content">
                <div class="block block-themed block-transparent mb-0">
                  <div class="block-header bg-primary-dark">
                    <h3 class="block-title">Index Des Pages</h3>
                    <div class="block-options">
                      <button type="button" class="btn-block-option close-summary">
                        <i class="fa fa-fw fa-times"></i>
                      </button>
                    </div>
                  </div>
                  <ul class="pl-5 pr-1 py-4 m-0">
                    <li class="capitalize py-2"><a
                        href="#global">Global</a></li>
                    @foreach ($pages as $page)
                      <li class="capitalize py-2"><a
                          href="#{{ str_replace('/', '-', $page['pageLink']) }}">{{ $page['pageTitle'] }}</a></li>
                    @endforeach
                    @foreach ($pages_types as $pages_type)
                      <li class="capitalize py-2"><a
                          href="#{{ clean(replaceAccentedCharacter($pages_type['title'])) }}">{{ $pages_type['title'] }}</a></li>
                    @endforeach
                  </ul>
                </div>
              </div>
            </div>
          </div>
          <!-- END From Right Block Modal -->
        </div>
        <div class="pages">
          <div class="container">
            <div class="row">
              <div id="global" class="col-12 page mb-4 mt-3">
                <div class="header">
                  <h3 class="capitalize mb-2 d-inline-block"><i class="fas fa-file-alt pr-2"></i> {{ $global['title'] }}
                  </h3>
                </div>
                <div class="body">
                  <div class="card">
                  {{--                  <div class="card-header pb-2">--}}
                  {{--                    <div class="table-title">--}}
                  {{--                      <h4 class="font-weight-bold mb-2 capitalize d-inline-block">--}}
                  {{--                        <i class="fas fa-angle-right pr-2"></i>--}}
                  {{--                        {{ $item['title'] }}</h4>--}}
                  {{--                      <span class="separator px-2">|</span>--}}
                  {{--                      <a href="{{ url('/') . $item['link'] }}" target="_blank"--}}
                  {{--                         class="underline table-link">Lien--}}
                  {{--                        <i class="fas fa-link icon-link"></i>--}}
                  {{--                      </a>--}}
                  {{--                    </div>--}}
                  {{--                  </div>--}}
                  <!-- /.card-header -->
                    <div class="card-body">
                    <span class="rules">
                        <h5 class="mb-2 capitalize">spécifications</h5>
                    </span>
                      @foreach ($global['specifications'] as $key => $content)
                        @if (is_array($content) && $key == 'content')
                          <ul class="rules-list pl-4">
                            @if (!count($content))
                              <li class="">Pas de spécifications !!</li>
                            @endif
                            @foreach ($content as $content_item)
                              @if (is_array($content_item))
                                <li class="">{{ $content_item['title'] }}
                                  <ul class="rules-list pl-4">
                                    @foreach ($content_item['values'] as $content_item_value)
                                      <li class="">{{ $content_item_value }}</li>
                                    @endforeach
                                  </ul>
                                </li>
                              @else
                                <li class="">{{ $content_item }}</li>
                              @endif
                            @endforeach
                          </ul>
                        @endif
                      @endforeach
                    </div>
                    <!-- /.card-body -->
                  </div>
                </div>
              </div>
            </div>
            <hr class="w-100">
            <div class="row">
              @foreach ($pages as $page)
                <div id="{{ str_replace('/', '-', $page['pageLink']) }}" class="col-12 page mb-4 mt-3">
                  <div class="header">
                    <h3 class="capitalize mb-2 d-inline-block"><i
                        class="fas fa-file-alt pr-2"></i> Page
                      : {{ $page['pageTitle'] }}</h3>
                    <span class="separator px-2">|</span>
                    <a href="{{ url('/') . $page['pageLink'] }}" target="_blank"
                       class="underline table-link">Lien
                      <i class="fas fa-link icon-link"></i>
                    </a>
                  </div>
                  <div class="body">
                    @foreach ($page['items'] as $key => $item)
                      <div class="card pt-3 ml-4">
                        <div class="card-header pb-2">
                          <div class="table-title">
                            <h4 class="font-weight-bold mb-2 capitalize d-inline-block">
                              <i class="fas fa-angle-right pr-2"></i>
                              {{ $item['title'] }}</h4>
                            <span class="separator px-2">|</span>
                            <a href="{{ url('/') . $item['link'] }}" target="_blank"
                               class="underline table-link">Lien
                              <i class="fas fa-link icon-link"></i>
                            </a>
                          </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body ml-4">
                          <span class="rules">
                              <h5 class="mb-2 capitalize">spécifications</h5>
                          </span>
                          @foreach ($item['specifications'] as $key => $content)
                            @if (is_array($content) && $key == 'content')
                              <ul class="rules-list pl-4">
                                @if (!count($content))
                                  <li class="">Pas de spécifications !!</li>
                                @endif
                                @foreach ($content as $content_item)
                                  @if (is_array($content_item))
                                    <li class="">{{ $content_item['title'] }}
                                      <ul class="rules-list pl-4">
                                        @foreach ($content_item['values'] as $content_item_value)
                                          <li class="">{{ $content_item_value }}</li>
                                        @endforeach
                                      </ul>
                                    </li>
                                  @else
                                    <li class="">{{ $content_item }}</li>
                                  @endif
                                @endforeach
                              </ul>
                            @endif
                          @endforeach
                        </div>
                        <!-- /.card-body -->
                      </div>
                    @endforeach
                  </div>
                </div>
                <hr class="w-100">
              @endforeach
            </div>
            <div class="row">
              @foreach ($pages_types as $pages_type)
                <div id="{{ clean(replaceAccentedCharacter($pages_type['title'])) }}" class="col-12 page mb-4 mt-3">
                  <div class="header">
                    <h3 class="capitalize mb-2 d-inline-block"><i
                        class="fas fa-file-alt pr-2"></i> {{ $pages_type['title'] }}
                    </h3>
                  </div>
                  <div class="body">
                    <div class="card">
                      <div class="card-body">
                    <span class="rules">
                        <h5 class="mb-2 capitalize">spécifications</h5>
                    </span>
                        @foreach ($pages_type['specifications'] as $key => $content)
                          @if (is_array($content) && $key == 'content')
                            <ul class="rules-list pl-4">
                              @if (!count($content))
                                <li class="">Pas de spécifications !!</li>
                              @endif
                              @foreach ($content as $content_item)
                                @if (is_array($content_item))
                                  <li class="">{{ $content_item['title'] }}
                                    <ul class="rules-list pl-4">
                                      @foreach ($content_item['values'] as $content_item_value)
                                        <li class="">{{ $content_item_value }}</li>
                                      @endforeach
                                    </ul>
                                  </li>
                                @else
                                  <li class="">{{ $content_item }}</li>
                                @endif
                              @endforeach
                            </ul>
                          @endif
                        @endforeach
                      </div>
                      <!-- /.card-body -->
                    </div>
                  </div>
                </div>
              @endforeach
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection

@section('js_after')

@endsection
