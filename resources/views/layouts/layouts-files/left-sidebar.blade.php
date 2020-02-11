<nav id="sidebar" aria-label="Main Navigation">
    <!-- Side Header -->
    <div class="content-header bg-white-5">
        <!-- Logo -->
        <a class="font-w600 text-dual" href="{{ URL::to('/') }}">
            <img src="{{ asset('media/circetwhite.png') }}" alt="" class="logo">
        </a>
        <!-- END Logo -->

        <!-- Options -->
        <div>
            <!-- Color Variations -->
            <div class="dropdown d-inline-block ml-3">
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
                <a class="nav-main-link{{ request()->is('dashboard') ? ' active' : '' }}" href="{{ route('dashboard') }}">
                    <i class="nav-main-link-icon si si-cursor"></i>
                    <span class="nav-main-link-name">Tableau de bord</span>
                </a>
            </li>
            @if(Auth::user()->role->id === 1)
                <li class="nav-main-item">
                    <a class="nav-main-link{{ (request()->is('all-stats') || request()->is('all-stats/*')) ? ' active' : '' }}" href="{{ route('stats.index') }}">
                        <i class="nav-main-link-icon far fa-chart-bar"></i>
                        <span class="nav-main-link-name">Statistiques</span>
                    </a>
                </li>
                <li class="nav-main-item">
                    <a class="nav-main-link{{ (request()->is('users') || request()->is('users/*')) ? ' active' : '' }}"
                       href="{{ route('users.index') }}">
                        <i class="nav-main-link-icon si si-users"></i>
                        <span class="nav-main-link-name">Utilisateurs</span>
                    </a>
                </li>
                <li class="nav-main-item">
                    <a class="nav-main-link{{ (request()->is('roles') || request()->is('roles/*')) ? ' active' : '' }}"
                       href="{{ route('roles.index') }}">
                        <i class="nav-main-link-icon si si-shield"></i>
                        <span class="nav-main-link-name">Rôles</span>
                    </a>
                </li>
            <li class="nav-main-item{{ request()->is('agences') ? ' open' : '' }}">
                <a class="nav-main-link nav-main-link-submenu" data-toggle="submenu" aria-haspopup="true" aria-expanded="true" href="#">
                    <i class="nav-main-link-icon fas fa-building"></i>
                    <span class="nav-main-link-name">Agences</span>
                </a>
                <ul class="nav-main-submenu">
                    @foreach(agencesList() as $agence)
                    <li class="nav-main-item">
                        <a class="nav-main-link {{ request()->has('agence_code') && request()->input('agence_code') === $agence['code'] ? ' active' : '' }}" href="{{ route('agence.index', ['agence_code' => $agence['code']]) }}">
                            <span class="nav-main-link-name"> <i class="fas fa-list"></i> {{ $agence['name'] }}</span>
                        </a>
                    </li>
                    @endforeach
                </ul>
            </li>
            <li class="nav-main-item{{ request()->is('agents') ? ' open' : '' }}">
                <a class="nav-main-link nav-main-link-submenu" data-toggle="submenu" aria-haspopup="true" aria-expanded="true" href="#">
                    <i class="nav-main-link-icon fas fa-users"></i>
                    <span class="nav-main-link-name">Agents</span>
                </a>
                <ul class="nav-main-submenu">
                    <li class="nav-main-item">
                        <a class="nav-main-link{{ request()->is('users') ? ' active' : '' }}" href="javascript:void(0)">
                            <div class="form-group" style="width: 150px">
                                <select class="form-control" id="agent-code" name="agent_code" style="width: 100%;">
                                    <option></option>
                                    <!-- Required for data-placeholder attribute to work with Select2 plugin -->
                                </select>
                            </div>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="nav-main-item">
                <a class="nav-main-link{{ request()->is('stats') ? ' active' : '' }}" href="{{ route('stats.import') }}">
                    <i class="nav-main-link-icon si si-cursor"></i>
                    <span class="nav-main-link-name">Importation</span>
                </a>
            </li>
            @endif
        </ul>
    </div>
    <!-- END Side Navigation -->
</nav>
