<nav id="sidebar" aria-label="Main Navigation">
    <!-- Side Header -->
    <div class="content-header bg-white-5">
        <!-- Logo -->
        <a class="font-w600 text-dual" href="/">
            <img src="{{ asset('media/circetwhite.png') }}" alt="" class="logo">
        </a>
        <!-- END Logo -->

        <!-- Options -->
        <div>
            <!-- Color Variations -->
            <div class="dropdown d-inline-block ml-3">
                <a class="text-dual font-size-sm" id="sidebar-themes-dropdown" data-toggle="dropdown"
                   aria-haspopup="true" aria-expanded="false" href="#">
                    <i class="si si-drop"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-right font-size-sm smini-hide border-0"
                     aria-labelledby="sidebar-themes-dropdown">
                    <!-- Color Themes -->
                    <!-- Layout API, functionality initialized in Template._uiHandleTheme() -->
                    <a class="dropdown-item d-flex align-items-center justify-content-between" data-toggle="theme"
                       data-theme="default" href="#">
                        <span>Default</span>
                        <i class="fa fa-circle text-default"></i>
                    </a>
                    <a class="dropdown-item d-flex align-items-center justify-content-between" data-toggle="theme"
                       data-theme="{{ mix('css/themes/amethyst.css') }}" href="#">
                        <span>Amethyst</span>
                        <i class="fa fa-circle text-amethyst"></i>
                    </a>
                    <a class="dropdown-item d-flex align-items-center justify-content-between" data-toggle="theme"
                       data-theme="{{ mix('css/themes/city.css') }}" href="#">
                        <span>City</span>
                        <i class="fa fa-circle text-city"></i>
                    </a>
                    <a class="dropdown-item d-flex align-items-center justify-content-between" data-toggle="theme"
                       data-theme="{{ mix('css/themes/flat.css') }}" href="#">
                        <span>Flat</span>
                        <i class="fa fa-circle text-flat"></i>
                    </a>
                    <a class="dropdown-item d-flex align-items-center justify-content-between" data-toggle="theme"
                       data-theme="{{ mix('css/themes/modern.css') }}" href="#">
                        <span>Modern</span>
                        <i class="fa fa-circle text-modern"></i>
                    </a>
                    <a class="dropdown-item d-flex align-items-center justify-content-between" data-toggle="theme"
                       data-theme="{{ mix('css/themes/smooth.css') }}" href="#">
                        <span>Smooth</span>
                        <i class="fa fa-circle text-smooth"></i>
                    </a>
                    <!-- END Color Themes -->

                    <div class="dropdown-divider"></div>

                    <!-- Sidebar Styles -->
                    <!-- Layout API, functionality initialized in Template._uiApiLayout() -->
                    <a class="dropdown-item" data-toggle="layout" data-action="sidebar_style_light" href="#">
                        <span>Sidebar Light</span>
                    </a>
                    <a class="dropdown-item" data-toggle="layout" data-action="sidebar_style_dark" href="#">
                        <span>Sidebar Dark</span>
                    </a>
                    <!-- Sidebar Styles -->

                    <div class="dropdown-divider"></div>

                    <!-- Header Styles -->
                    <!-- Layout API, functionality initialized in Template._uiApiLayout() -->
                    <a class="dropdown-item" data-toggle="layout" data-action="header_style_light" href="#">
                        <span>Header Light</span>
                    </a>
                    <a class="dropdown-item" data-toggle="layout" data-action="header_style_dark" href="#">
                        <span>Header Dark</span>
                    </a>
                    <!-- Header Styles -->
                </div>
            </div>
            <!-- END Themes -->

            <!-- Close Sidebar, Visible only on mobile screens -->
            <!-- Layout API, functionality initialized in Template._uiApiLayout() -->
            <a class="d-lg-none text-dual ml-3" data-toggle="layout" data-action="sidebar_close"
               href="javascript:void(0)">
                <i class="fa fa-times"></i>
            </a>
            <!-- END Close Sidebar -->
        </div>
        <!-- END Options -->
    </div>
    <!-- END Side Header -->

    <!-- Side Navigation -->
    <div class="content-side content-side-full">
        <ul class="nav-main">
            <li class="nav-main-item">
                <a class="nav-main-link{{ request()->is('dashboard') ? ' active' : '' }}" href="dashboard">
                    <i class="nav-main-link-icon si si-cursor"></i>
                    <span class="nav-main-link-name">Dashboard</span>
                </a>
            </li>
            @can('view', Auth::user())
                <li class="nav-main-item">
                    <a class="nav-main-link{{ (request()->is('users') || request()->is('users/*')) ? ' active' : '' }}"
                       href="users">
                        <i class="nav-main-link-icon si si-users"></i>
                        <span class="nav-main-link-name">Users</span>
                    </a>
                </li>
                <li class="nav-main-item">
                    <a class="nav-main-link{{ (request()->is('roles') || request()->is('roles/*')) ? ' active' : '' }}"
                       href="roles">
                        <i class="nav-main-link-icon si si-shield"></i>
                        <span class="nav-main-link-name">Roles</span>
                    </a>
                </li>
                <li class="nav-main-item">
                    <a class="nav-main-link{{ (request()->is('permissions') || request()->is('permissions/*')) ? ' active' : '' }}"
                       href="permissions">
                        <i class="nav-main-link-icon si si-key"></i>
                        <span class="nav-main-link-name">Permissions</span>
                    </a>
                </li>
                {{--                <li class="nav-main-item">--}}
                {{--                    <a class="nav-main-link{{ (request()->is('skills') || request()->is('skills/*')) ? ' active' : '' }}"--}}
                {{--                       href="skills">--}}
                {{--                        <i class="nav-main-link-icon si si-game-controller"></i>--}}
                {{--                        <span class="nav-main-link-name">Skills</span>--}}
                {{--                    </a>--}}
                {{--                </li>--}}
                {{--                <li class="nav-main-item">--}}
                {{--                    <a class="nav-main-link{{ (request()->is('projects') || request()->is('projects/*')) ? ' active' : '' }}"--}}
                {{--                       href="projects">--}}
                {{--                        <i class="nav-main-link-icon si si-briefcase"></i>--}}
                {{--                        <span class="nav-main-link-name">Projects</span>--}}
                {{--                    </a>--}}
                {{--                </li>--}}
            @endcan

            <li class="nav-main-item{{ request()->is('agences') ? ' open' : '' }}">
                <a class="nav-main-link nav-main-link-submenu" data-toggle="submenu" aria-haspopup="true" aria-expanded="true" href="#">
                    <i class="nav-main-link-icon fas fa-building"></i>
                    <span class="nav-main-link-name">Agences</span>
                </a>
                <ul class="nav-main-submenu">
                    @foreach(agencesList() as $agence)
                    <li class="nav-main-item">
                        <a class="nav-main-link{{ request()->is('agences') ? ' active' : '' }}" href="{{ route('agence.index', ['agence_code' => $agence['code']]) }}">
                            <span class="nav-main-link-name"> <i class="fas fa-list"></i> {{ $agence['name'] }}</span>
                        </a>
                    </li>
                    @endforeach
                </ul>
            </li>

            <li class="nav-main-item">
                <a class="nav-main-link{{ request()->is('stats') ? ' active' : '' }}" href="stats">
                    <i class="nav-main-link-icon si si-cursor"></i>
                    <span class="nav-main-link-name">Import</span>
                </a>
            </li>
            <ul class="d-none">
                <li class="nav-main-heading">Various</li>
                <li class="nav-main-item{{ request()->is('examples/*') ? ' open' : '' }}">
                    <a class="nav-main-link nav-main-link-submenu" data-toggle="submenu" aria-haspopup="true"
                       aria-expanded="true" href="#">
                        <i class="nav-main-link-icon si si-bulb"></i>
                        <span class="nav-main-link-name">Examples</span>
                    </a>
                    <ul class="nav-main-submenu">
                        <li class="nav-main-item">
                            <a class="nav-main-link{{ request()->is('examples/plugin-helper') ? ' active' : '' }}"
                               href="examples/plugin-helper">
                                <span class="nav-main-link-name">Plugin with JS Helper</span>
                            </a>
                        </li>
                        <li class="nav-main-item">
                            <a class="nav-main-link{{ request()->is('examples/plugin-init') ? ' active' : '' }}"
                               href="examples/plugin-init">
                                <span class="nav-main-link-name">Plugin with JS Init</span>
                            </a>
                        </li>
                        <li class="nav-main-item">
                            <a class="nav-main-link{{ request()->is('examples/blank') ? ' active' : '' }}"
                               href="examples/blank">
                                <span class="nav-main-link-name">Blank</span>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-main-heading">More</li>
                <li class="nav-main-item open">
                    <a class="nav-main-link" href="/">
                        <i class="nav-main-link-icon si si-globe"></i>
                        <span class="nav-main-link-name">Landing</span>
                    </a>
                </li>
            </ul>
        </ul>
    </div>
    <!-- END Side Navigation -->
</nav>
