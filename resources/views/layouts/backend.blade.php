<!doctype html>
<html lang="{{ config('app.locale') }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">

        <title>@yield('page-title')</title>

        <meta name="description" content="OneUI - Bootstrap 4 Admin Template &amp; UI Framework created by pixelcave and published on Themeforest">
        <meta name="author" content="pixelcave">
        <meta name="robots" content="noindex, nofollow">

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <!-- Icons -->
        <link rel="shortcut icon" href="{{ asset('media/favicons/favicon.png') }}">
        <link rel="icon" sizes="192x192" type="image/png" href="{{ asset('media/favicons/favicon-192x192.png') }}">
        <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('media/favicons/apple-touch-icon-180x180.png') }}">

        <!-- Fonts and Styles -->
        @yield('css_before')
        <link rel="stylesheet" href="//fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400italic,600,700%7COpen+Sans:300,400,400italic,600,700">
        <link rel="stylesheet" id="css-main" href="{{ asset('css/oneui.css') }}">

        <!-- You can include a specific file from public/css/themes/ folder to alter the default color theme of the template. eg: -->
        <!-- <link rel="stylesheet" id="css-theme" href="{{ asset('css/themes/amethyst.css') }}"> -->
        <!-- Select2 -->
        <link href="{{ asset("/add_ons/select2/css/select2.min.css") }}" rel="stylesheet"/>
        @yield('css_after')

        <!-- Scripts -->
{{--        <script>window.Laravel = {!! json_encode(['csrfToken' => csrf_token(),]) !!};</script>--}}
    </head>
    <body>

        <div id="page-container" class="sidebar-o enable-page-overlay sidebar-dark side-scroll page-header-fixed">
            <!-- Side Overlay-->
{{--            @include('layouts.layouts-files.right-sidebar')--}}
            <!-- END Side Overlay -->

            @include('layouts.layouts-files.left-sidebar')
            <!-- END Sidebar -->

            <!-- Header -->
            @include('layouts.layouts-files.header')
            <!-- END Header -->

            <!-- Main Container -->
            <main id="main-container">
                @yield('content-header')
                <div class="section">
                    @yield('content')
                </div>
            </main>
            <!-- END Main Container -->

            <!-- Footer -->
            @include('layouts.layouts-files.footer')
            <!-- END Footer -->
        </div>
        <!-- END Page Container -->

        <script !src="">
            APP_URL = '{{ URL::to('/') }}';

            function getDatesFilter(globalElements) {
                $.ajax({
                    url: APP_URL + '/dates',
                    method: 'GET',
                    success: function (response) {
                        let treeData = response.dates;

                        $('.tree-view').each(function (index, item) {
                            let treeId = '#' + $(this).attr('id');
                            let object = globalElements.filter(function (element) {
                                return element.filterElement.dates === treeId;
                            });
                            new Tree(treeId, {
                                data: [{id: '-1', text: 'Dates', children: treeData}],
                                closeDepth: 1,
                                loaded: function () {
                                    // this.values = ['2019-12-02', '2019-12-03'];
                                    // console.log(this.selectedNodes);
                                    // console.log(this.values);
                                    // this.disables = ['0-0-0', '0-0-1', '0-0-2']

                                    if (object.length) {
                                        object = object[0];
                                        object.filterTree.datesTreeObject = this;
                                        if (object.filterTree.dates) {
                                            object.filterTree.datesTreeObject.values = object.filterTree.dates;
                                        }
                                    }
                                },
                                onChange: function () {
                                    // dates = this.values;
                                    if (object.filterTree) {
                                        object.filterTree.dates = this.values;
                                    }
                                }
                            });
                        });
                        // if (datesFilterListExist && datesFilterValuesExist) {
                        //     assignFilter(datesFilterList, datesFilterValues);
                        // }
                        $('.treejs-node .treejs-nodes .treejs-switcher').click();
                        $('.refresh-form button').removeClass('d-none');
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                    }
                });
            }

            function userFilter(userObject, isPost = false) {
                $.ajax({
                    url: APP_URL + '/user/filter',
                    method: isPost ? 'POST' : 'GET',
                    data: {filter: userObject.filterTree.dates},
                    success: function (response) {
                        if (response.userFilter) {
                            userObject.filterTree.dates = response.userFilter.date_filter;
                            if (userObject.filterTree.datesTreeObject && userObject.filterTree.dates) {
                                userObject.filterTree.datesTreeObject.values = userObject.filterTree.dates;
                                if (userObject.objDetail) {
                                    userObject.objDetail.filterTree.dates = userObject.filterTree.dates;
                                }
                            }
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                    }
                });
            }

            function toggleLoader(parent, remove = false) {
                if (remove) {
                    parent.find('.loader_wrapper').remove();
                    parent.find('.loader_container').remove();
                } else {
                    parent.append('<div class="loader_wrapper"><div class="loader"></div></div>');
                    parent.append('<div class="loader_container"></div>');
                }
            }

            function elementExists(object) {
                if (object !== null && object !== undefined) {
                    let element = $('#' + object.element);
                    if (element !== null && element !== undefined) {
                        return element.length;
                    } else {
                        return object.length;
                    }
                }
                return false;
            }

            function dynamicColors(uniqueColors) {
                let color = {
                    r: Math.floor(Math.random() * 255),
                    g: Math.floor(Math.random() * 255),
                    b: Math.floor(Math.random() * 255)
                };
                let exists = false;
                do {
                    exists = uniqueColors.some(function (uniqueColor) {
                        return uniqueColor.r === color.r &&
                            uniqueColor.g === color.g &&
                            uniqueColor.b === color.b;
                    });
                } while (exists);
                uniqueColors.push(color);
                return "rgb(" + color.r + "," + color.g + "," + color.b + ")";
            }

            function feedBack(message, status) {
                swal(
                    status.replace(/^\w/, c => c.toUpperCase()) + '!',
                    message,
                    status
                )
            }

        </script>
        <!-- OneUI Core JS -->
        <script src="{{ asset('js/oneui.app.js') }}"></script>
        <!-- Select2 -->
        <script src="{{ asset("/add_ons/select2/js/select2.min.js") }}"></script>
        <script src={{ asset("/add_ons/select2/js/i18n/fr.js") }}></script>
        <!-- Laravel Scaffolding JS -->
        <script src="{{ asset('js/laravel.app.js') }}"></script>

        @yield('js_after')
    </body>
</html>
