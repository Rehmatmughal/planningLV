<div class="sidebar bg-dark text-white">

    {{-- =========================================================
        SIDEBAR HEADER
    ========================================================= --}}
    <div class="sidebar-header p-3 border-bottom">
        <h5 class="mb-0 text-nowrap">
            <i class="fa fa-city me-2"></i>
            Town Planning MIS
        </h5>
    </div>


    {{-- =========================================================
        SIDEBAR MENU
    ========================================================= --}}
    <ul class="nav flex-column sidebar-menu">


        {{-- =====================================================
            DASHBOARD
        ====================================================== --}}
        <li class="nav-item">

            <a href="{{ route('dashboard') }}"
               class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">

                <i class="fa fa-home fa-fw me-2"></i>

                <span>Dashboard</span>

            </a>

        </li>


        {{-- =====================================================
            MASTERS
        ====================================================== --}}
        <li class="nav-item">

            <a class="nav-link"
               data-bs-toggle="collapse"
               href="#masters"
               role="button"
               aria-expanded="{{ request()->routeIs(
                    'admin.*',
                    'sizes.*',
                    'categories.*',
                    'property-type-assignments.*',
                    'plot-size-assignments.*'
               ) ? 'true' : 'false' }}"
               aria-controls="masters">

                <i class="fa fa-database fa-fw me-2"></i>

                <span>Masters</span>

                <i class="fa fa-chevron-down ms-auto sidebar-arrow"></i>

            </a>


            <div class="collapse
                {{ request()->routeIs(
                    'admin.*',
                    'sizes.*',
                    'categories.*',
                    'property-type-assignments.*',
                    'plot-size-assignments.*'
                ) ? 'show' : '' }}"
                id="masters">


                {{-- Admin --}}
                <a href="{{ route('admin.admin.index') }}"
                   class="nav-link sidebar-submenu
                   {{ request()->routeIs('admin.*') ? 'active' : '' }}">

                    <i class="fa fa-user-shield fa-fw me-2"></i>

                    <span>Admin</span>

                </a>
                


                {{-- Plot Sizes --}}
                <a href="{{ route('sizes.index') }}"
                   class="nav-link sidebar-submenu
                   {{ request()->routeIs('sizes.*') ? 'active' : '' }}">

                    <i class="fa fa-ruler-combined fa-fw me-2"></i>

                    <span>Plot Sizes</span>

                </a>


                {{-- Plot Categories --}}
                <a href="{{ route('categories.index') }}"
                   class="nav-link sidebar-submenu
                   {{ request()->routeIs('categories.*') ? 'active' : '' }}">

                    <i class="fa fa-layer-group fa-fw me-2"></i>

                    <span>Plot Categories</span>

                </a>


                {{-- Property Type Assignments --}}
                <a href="{{ route('property-type-assignments.index') }}"
                   class="nav-link sidebar-submenu
                   {{ request()->routeIs('property-type-assignments.*') ? 'active' : '' }}">

                    <i class="fa fa-building fa-fw me-2"></i>

                    <span>Property Types</span>

                </a>


                {{-- Plot Size Assignments --}}
                <a href="{{ route('plot-size-assignments.index') }}"
                   class="nav-link sidebar-submenu
                   {{ request()->routeIs('plot-size-assignments.*') ? 'active' : '' }}">

                    <i class="fa fa-list-check fa-fw me-2"></i>

                    <span>Plot Size Assignments</span>

                </a>


            </div>

        </li>

        {{-- 3 items --}}
        {{-- =====================================================
            PROJECT
        ====================================================== --}}
        <li class="nav-item">

            <a href="{{ route('projects.index') }}"
            class="nav-link {{ request()->routeIs('projects.*') ? 'active' : '' }}">

                <i class="fa fa-building fa-fw me-2"></i>

                <span>Projects</span>

            </a>

        </li>


        {{-- =====================================================
            BLOCK
        ====================================================== --}}
        <li class="nav-item">

            <a href="{{ route('blocks.index') }}"
            class="nav-link {{ request()->routeIs('blocks.*') ? 'active' : '' }}">

                <i class="fa fa-cubes fa-fw me-2"></i>

                <span>Blocks</span>

            </a>

        </li>


        {{-- =====================================================
            STREETS
        ====================================================== --}}
        <li class="nav-item">

            <a href="{{ route('streets.index') }}"
            class="nav-link {{ request()->routeIs('streets.*') ? 'active' : '' }}">

                <i class="fa fa-road fa-fw me-2"></i>

                <span>Streets</span>

            </a>

        </li>



        {{-- =====================================================
            PLOT MANAGEMENT
        ====================================================== --}}
        <li class="nav-item">

            <a href="{{ route('plots.index') }}"
               class="nav-link {{ request()->routeIs('plots.*') ? 'active' : '' }}">

                <i class="fa fa-map fa-fw me-2"></i>

                <span>Plot Management</span>

            </a>

        </li>


        {{-- =====================================================
            DEVELOPMENT
        ====================================================== --}}
        <li class="nav-item">

            <a href="{{ route('development.index') }}"
               class="nav-link {{ request()->routeIs('development.*') ? 'active' : '' }}">

                <i class="fa fa-hard-hat fa-fw me-2"></i>

                <span>Development Status</span>

            </a>

        </li>


        {{-- =====================================================
            LOP & MORTGAGE
        ====================================================== --}}
        <li class="nav-item">

            <a href="{{ route('lop-mortgage.index') }}"
               class="nav-link {{ request()->routeIs('lop-mortgage.*') ? 'active' : '' }}">

                <i class="fa fa-file-contract fa-fw me-2"></i>

                <span>LOP &amp; Mortgage</span>

            </a>

        </li>


        {{-- =====================================================
            AREA VARIATION
        ====================================================== --}}
        <li class="nav-item">

            <a href="{{ route('area_variations.index') }}"
               class="nav-link {{ request()->routeIs('area_variations.*') ? 'active' : '' }}">

                <i class="fa fa-ruler-combined fa-fw me-2"></i>

                <span>Area Variation</span>

            </a>

        </li>


        {{-- =====================================================
            POSSESSION
        ====================================================== --}}
        <li class="nav-item">

            <a class="nav-link"
               data-bs-toggle="collapse"
               href="#possessionMenu"
               role="button"
               aria-expanded="{{ request()->routeIs(
                    'possession-cases.*',
                    'owners.*'
               ) ? 'true' : 'false' }}"
               aria-controls="possessionMenu">

                <i class="fa fa-key fa-fw me-2"></i>

                <span>Possession</span>

                <i class="fa fa-chevron-down ms-auto sidebar-arrow"></i>

            </a>


            <div class="collapse
                {{ request()->routeIs(
                    'possession-cases.*',
                    'owners.*'
                ) ? 'show' : '' }}"
                id="possessionMenu">


                {{-- Possession Cases --}}
                <a href="{{ route('possession-cases.index') }}"
                   class="nav-link sidebar-submenu
                   {{ request()->routeIs('possession-cases.*') ? 'active' : '' }}">

                    <i class="fa fa-file-signature fa-fw me-2"></i>

                    <span>Possession Cases</span>

                </a>


                {{-- Owners --}}
                <a href="{{ route('owners.index') }}"
                   class="nav-link sidebar-submenu
                   {{ request()->routeIs('owners.*') ? 'active' : '' }}">

                    <i class="fa fa-users fa-fw me-2"></i>

                    <span>Owners</span>

                </a>


            </div>

        </li>


    </ul>

</div>


{{-- =============================================================
    SIDEBAR CSS
============================================================= --}}
<style>

    /* Fixed sidebar width */
    .sidebar {
        width: 260px;
        min-width: 260px;
        max-width: 260px;

        height: 100vh;

        overflow-y: auto;
        overflow-x: hidden;

        flex-shrink: 0;
    }


    /* Header */
    .sidebar-header {
        width: 100%;
        white-space: nowrap;
        overflow: hidden;
    }


    .sidebar-header h5 {
        font-size: 16px;
        font-weight: 600;
    }


    /* Main menu */
    .sidebar-menu {
        padding: 8px 0;
    }


    /* All links */
    .sidebar .nav-link {
        color: #ced4da;

        display: flex;
        align-items: center;

        min-height: 44px;

        padding: 10px 15px;

        font-size: 14px;

        white-space: nowrap;

        transition:
            background-color 0.2s ease,
            color 0.2s ease;
    }


    /* Prevent text from going to second line */
    .sidebar .nav-link span {
        white-space: nowrap;
    }


    /* Icons */
    .sidebar .nav-link i {
        flex-shrink: 0;
    }


    /* Hover */
    .sidebar .nav-link:hover {
        background-color: rgba(255, 255, 255, 0.08);
        color: #ffffff;
    }


    /* Active */
    .sidebar .nav-link.active {
        background-color: rgba(13, 110, 253, 0.25);
        color: #ffffff;

        border-left: 3px solid #0d6efd;
    }


    /* Sub menu */
    .sidebar-submenu {
        padding-left: 45px !important;

        font-size: 13.5px !important;

        min-height: 40px !important;
    }


    /* Submenu active */
    .sidebar-submenu.active {
        background-color: rgba(13, 110, 253, 0.20);
    }


    /* Collapse arrow */
    .sidebar-arrow {
        font-size: 10px;

        transition: transform 0.2s ease;
    }


    /* Rotate arrow when menu is open */
    .sidebar .nav-link[aria-expanded="true"] .sidebar-arrow {
        transform: rotate(180deg);
    }


    /* Scrollbar */
    .sidebar::-webkit-scrollbar {
        width: 6px;
    }


    .sidebar::-webkit-scrollbar-track {
        background: #212529;
    }


    .sidebar::-webkit-scrollbar-thumb {
        background: #495057;
        border-radius: 10px;
    }


    .sidebar::-webkit-scrollbar-thumb:hover {
        background: #6c757d;
    }

</style>