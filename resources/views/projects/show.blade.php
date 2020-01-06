@extends('layouts.backend')

@section('page-title')
    Project - {{ $project->name }}
@endsection

@section('css_after')
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/7.2.0/sweetalert2.min.css">
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/croppie/2.6.4/croppie.min.css">
    <link href="{{ asset("/add_ons/select2/css/select2.min.css") }}" rel="stylesheet"/>
@endsection
@section('js_after')
    <script src="//cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/7.2.0/sweetalert2.all.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/croppie/2.6.4/croppie.min.js"></script>
    <script src="{{ asset("/add_ons/crop.js") }}"></script>
    <script src="{{ asset("/add_ons/select2/js/select2.min.js") }}"></script>
    <script src="{{ asset("/add_ons/projects/project.js") }}"></script>
    <script>
        $(function () {
            @if($errors->any())
                feedBack('{{ $errors->first() }}', 'error');
            @endif
            @if(session()->has('message'))
                feedBack('{{ session()->get('message') }}', 'success');
            @endif


            var _techs = $('#techs');

            InitLoad();

            $('.update-project').on('click', function (e) {
                e.preventDefault();
                var _techsOpts = $('#techs option:selected');
                var techs = [];
                Object.values(_techsOpts).forEach(function (skill, index) {
                    if (index < _techsOpts.length) {
                        techs.push($(skill).val())
                    }
                });
                var baseUrl = APP_URL;
                $.ajax({
                    method: 'PUT',
                    url: baseUrl + '/projects/{{ $project->id }}',
                    data: {techs, name: $('#name').val()},
                    success: function (response) {
                        feedBack(response.message, 'success');

                        // _skills.select2('data', {id: null, text: null});
                        _techs.select2("destroy");
                        InitLoad();
                        // _skills.val(null).trigger('change');
                        _techs.select2(InitSelect2());

                        // _skills.select2();
                        // _skills.select2("refresh");
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        if (jqXHR.status === 401 || jqXHR.status === 422) {
                            feedBack(jqXHR.responseJSON.message, 'error');
                        }
                        // $(_this).prop('checked', !$(element).prop('checked'));
                    }
                });
            });

            $('.delete-project').on('click', function (e) {
                var confirmed = confirm('Are you sure ?');
                if (confirmed) {
                    project_id = $(this).attr('data-project');
                    element = this;
                    var baseUrl = APP_URL;
                    $.ajax({
                        method: 'DELETE',
                        url: baseUrl + '/projects/' + project_id,
                        success: function (response) {
                            feedBack(response.message, 'success');
                            setTimeout(() => {
                                window.location = '/projects';
                            }, 500);
                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            if (jqXHR.status === 401 || jqXHR.status === 422) {
                                feedBack(jqXHR.responseJSON.message, 'error');
                            }
                        }
                    });
                }

            });

            function InitLoad() {
                _techs.find('option').remove().end();
                $.ajax({
                    type: 'GET',
                    url: '/getTechs/{{ $project->id }}'
                }).then(function (data) {
                    // create the option and append to Select2
                    data.techs.forEach((tech, index) => {
                        var option = new Option(tech.name, tech.id, false, tech.assigned);
                        _techs.append(option).trigger('change');
                    });

                    // manually trigger the `select2:select` event
                    // _skills.trigger({
                    //     type: 'select2:select',
                    //     params: {
                    //         data: data.skills
                    //     }
                    // });
                });
            }


            function feedBack(message, status) {
                swal(
                    status.replace(/^\w/, c => c.toUpperCase()) + '!',
                    message,
                    status
                )
            }

            function InitSelect2(maxSelectionLength = null) {
                return {
                    multiple: true,
                    maximumSelectionLength: maxSelectionLength,
                    allowClear: true,
                    width: 'resolve',
                    // theme: 'classic',
                    placeholder: 'Select a Tech',
                    // placeholder: {
                    //     id: '0', // the value of the option
                    //     text: 'Select an option'
                    // },
                    ajax: {
                        url: '/getTechs/{{ $project->id }}',
                        type: 'GET',
                        dataType: 'json',
                        delay: 50,
                        data: function (params) {
                            params.term = params.term === '*' ? '' : params.term;
                            return {
                                _token: $('meta[name="csrf-token"]').attr('content'),
                                searchTerm: $.trim(params.term)
                            };
                        },
                        processResults: function (response) {
                            results = response.techs.map(function (tech) {
                                tech.text = tech.name;
                                return tech;
                            });
                            return {
                                results: results
                            };
                        },
                        cache: true
                    },
                    templateResult: function (data, x) {
                        if (data.loading) {
                            return data.text;
                        }
                        return data.name;
                    }
                };
            }

            _techs.select2(InitSelect2());
        });
    </script>
@endsection
@section('content-header')
    <!-- Hero -->
    <div class="bg-body-light">
        <div class="content content-full">
            <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
                <div class="flex-sm-fill">
                    <h1 class="h3 my-2 d-inline-block">Project</h1>
                </div>
                <nav class="flex-sm-00-auto ml-sm-3" aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-alt">
                        <li class="breadcrumb-item">
                            <a class="link-fx" href="/">Home</a>
                        </li>
                        <li class="breadcrumb-item" aria-current="page">Project</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <!-- END Hero -->
@endsection
@section('content')
    <div class="user-profile profile">
        <form method="POST" action="/projects/{{ $project->id }}" enctype="multipart/form-data">
            @method('PUT')
            @csrf
            <div class="container">
                <div class="row">
                    <div class="col-md-8 offset-md-1 mt-sm-5 mt-5 mt-lg-0 mt-xl-0">
                        <div class="update-profile">
                            <div class="header">
                                <h2 class="capitalize">Update Project</h2>
                            </div>
                            <hr>
                            <div class="profile-data">
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-4">
                                            <label for="name">Project</label>
                                        </div>
                                        <div class="col-8">
                                            <input type="text" class="form-control form-field" id="name"
                                                   name="name"
                                                   aria-describedby="emailHelp" placeholder="Project name"
                                                   value="{{ $project->name }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-4">
                                            <label for="techs">Update Project Environment</label>
                                        </div>
                                        <div class="col-8">
                                            <div class="select2-wrapper">
                                                <select name="techs[]" id="techs" class="w-100"></select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row update-btn mt-lg-5 mt-sm-0">
                                    <div class="col-md-6 mt-3">
                                        <button type="button" class="btn btn-primary full-w">
                                            <span class="btn-field font-weight-normal fa-edit pr-4 position-relative update-project">Update</span>
                                        </button>
                                    </div>
                                    <div class="col-md-6 mt-3">
                                        <button type="button" class="btn btn-danger full-w delete-project"
                                                data-project="{{ $project->id }}">
                                            <span
                                                class="btn-field font-weight-bold fa-trash-alt pr-3 position-relative">Delete</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
