<aside class="main-sidebar sidebar-dark-primary elevation-4 vh-100">

    <a href="#" class="brand-link">
        <span class="brand-text font-weight-bold ml-2">Form Template Remedy</span>
    </a>

    <div class="sidebar">

        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="{{ asset('img/logoweb247.png') }}" class="" alt="User Image" style="width: 150px">
            </div>
        </div>

        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-home"></i>
                        <p>
                            Dashboard
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('home') }}" class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-align-justify"></i>
                        <p>
                            Form Template
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('ticketlist.index') }}" class="nav-link {{ request()->routeIs('ticketlist.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-clipboard-list"></i>
                        <p>
                            Ticket Lists
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('sop.index') }}" class="nav-link {{ request()->routeIs('sop.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-file-pdf"></i>
                        <p>
                            SOP Lists
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-sticky-note"></i>
                        <p>
                            Note Lists(Coming Soon)
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('application.index') }}" class="nav-link {{ request()->routeIs('application.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-rocket"></i>
                        <p>
                            Application
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('kedb.index') }}" class="nav-link {{ request()->routeIs('kedb.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-hashtag"></i>
                        <p>
                            KEDB
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('pic.index') }}" class="nav-link {{ request()->routeIs('pic.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-users"></i>
                        <p>
                            PIC
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('tools.index') }}" class="nav-link {{ request()->routeIs('tools.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-tools"></i>
                        <p>
                            Tools
                        </p>
                    </a>
                </li>
            </ul>
        </nav>
    </div>

</aside>
