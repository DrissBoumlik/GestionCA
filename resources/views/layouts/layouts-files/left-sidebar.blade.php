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
            @if(isAdmin())
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

                <li class="nav-main-item{{ request()->is('import/*') ? ' open' : '' }}">
                    <a class="nav-main-link nav-main-link-submenu" data-toggle="submenu" aria-haspopup="true" aria-expanded="true" href="#">
                        <i class="nav-main-link-icon fas fa-building"></i>
                        <span class="nav-main-link-name">Importation</span>
                    </a>
                    <ul class="nav-main-submenu">
                        <li class="nav-main-item">
                            <a class="nav-main-link {{ request()->is('import/stats') ? ' active' : '' }}" href="{{ route('stats.importView') }}">
                                <span class="nav-main-link-name">Importation des données</span>
                            </a>
                        </li>
                        <li class="nav-main-item">
                            <a class="nav-main-link {{ request()->is('import/agents') ? ' active' : '' }}" href="{{ route('agents.importView') }}">
                                <span class="nav-main-link-name">Importation des agents</span>
                            </a>
                        </li>
                    </ul>
{{--                    <a class="nav-main-link{{ request()->is('stats') ? ' active' : '' }}" href="{{ route('stats.import') }}">--}}
{{--                        <i class="nav-main-link-icon si si-cursor"></i>--}}
{{--                        <span class="nav-main-link-name">Importation</span>--}}
{{--                    </a>--}}
                </li>
            @endif
        </ul>
    </div>
    <!-- END Side Navigation -->
</nav>
