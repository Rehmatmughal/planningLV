<div class="sidebar bg-dark text-white">

    <div class="p-3 border-bottom">
        <h5>Town Planning MIS</h5>
    </div>

    <ul class="nav flex-column">

        <li class="nav-item">
            <a href="{{ route('dashboard') }}" class="nav-link">
                <i class="fa fa-home"></i>
                Dashboard
            </a>
        </li>

        <li class="nav-item">

            {{-- <a class="nav-link"
               data-bs-toggle="collapse"
               href="#masters"> --}}
            <a class="nav-link"
                data-bs-toggle="collapse"
                href="#masters"
                role="button"
                aria-expanded="false"
                aria-controls="masters">

                <i class="fa fa-database"></i>
                Masters

            </a>

            <div class="collapse" id="masters">

                {{-- <a href="{{route('projects.index')}}" class="nav-link ps-5">Projects</a> --}}
                <a href="{{ route('admin.admin.index') }}"
                class="nav-link ps-5 {{ request()->routeIs('admin.*') ? 'active' : '' }}">
                    Admin
                </a>
                <a href="{{ route('projects.index') }}"
                class="nav-link ps-5 {{ request()->routeIs('projects.*') ? 'active' : '' }}">
                    Projects
                </a>
                <a href="{{ route('blocks.index') }}"
                class="nav-link ps-5 {{ request()->routeIs('blocks.*') ? 'active' : '' }}">
                    Blocks
                </a>
                <a href="{{ route('streets.index') }}"
                class="nav-link ps-5 {{ request()->routeIs('streets.*') ? 'active' : '' }}">
                    Streets
                </a>
                <a href="{{ route('plots.index') }}"
                class="nav-link ps-5 {{ request()->routeIs('plots.*') ? 'active' : '' }}">
                    Plots
                </a>
                <a href="{{ route('sizes.index') }}"
                class="nav-link ps-5 {{ request()->routeIs('sizes.*') ? 'active' : '' }}">
                    Plot Sizes
                </a>
                <a href="{{ route('categories.index') }}"
                class="nav-link ps-5 {{ request()->routeIs('categories.*') ? 'active' : '' }}">
                    Plot Categories
                </a>
                {{-- <a href="#" class="nav-link ps-5">Blocks</a>
                <a href="#" class="nav-link ps-5">Streets</a>
                <a href="#" class="nav-link ps-5">Plot Sizes</a>
                <a href="#" class="nav-link ps-5">Plot Categories</a>
                <a href="#" class="nav-link ps-5">Development Status</a>
                <a href="#" class="nav-link ps-5">LOP Status</a>
                <a href="#" class="nav-link ps-5">Possession Status</a> --}}

            </div>

        </li>

        <li class="nav-item">
            <a href="#" class="nav-link">
                <i class="fa fa-map"></i>
                Plot Management
                {{-- Plot Management ({{ $stats['total_plots'] }}) --}}
                {{-- <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">LOP Clear Plots</div>
                <div class="h6 mb-0 font-weight-bold text-gray-800">{{ $stats['lop_clear'] }}</div> --}}

            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('area_variations.index')}}" class="nav-link">
                <i class="fa fa-map"></i>
                Area Variation
                {{-- Plot Management ({{ $stats['total_plots'] }}) --}}
                {{-- <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">LOP Clear Plots</div>
                <div class="h6 mb-0 font-weight-bold text-gray-800">{{ $stats['lop_clear'] }}</div> --}}

            </a>
        </li>

        {{-- <li class="nav-item">
            <a href="{{ route('development.index')}}" class="nav-link">
                <i class="fa fa-road"></i>
                Development
            </a>
        </li> --}}

        {{-- <li class="nav-item">
            <a href="#" class="nav-link">
                <i class="fa fa-file"></i>
                LOP Management
            </a>
        </li> --}}

        {{-- <li class="nav-item">
            <a href="#" class="nav-link">
                <i class="fa fa-key"></i>
                Possession
            </a>
        </li> --}}

        {{-- <li class="nav-item">
            <a href="#" class="nav-link">
                <i class="fa fa-chart-bar"></i>
                Reports
            </a>
        </li> --}}

        {{-- <li class="nav-item">
            <a href="#" class="nav-link">
                <i class="fa fa-cog"></i>
                Settings
            </a>
        </li> --}}
    </ul>
</div>

{{-- old side bar --}}
{{-- <div class="sidebar bg-dark text-white p-3" style="width:250px; height:100vh;">

    <h4 class="mb-4">Navigation</h4>

    <ul class="list-unstyled">

        <li>
            <a href="#" class="toggle-menu text-white">Projects</a>
            <ul class="submenu list-unstyled ms-3 d-none">
                @foreach($projects as $project)
                    <li>
                        <a href="#" class="toggle-menu text-white"> {{ $project->project_name }} </a>
                        <ul class="submenu list-unstyled ms-3 d-none">
                            @foreach($project->blocks as $block)
                                <li>
                                    <a href="#" class="toggle-menu text-white"> {{ $block->block_name }} </a>

                                    <ul class="submenu list-unstyled ms-3 d-none">
                                        @foreach($block->plots as $plot)
                                            <li class="text-secondary">
                                                Plot # {{ $plot->plot_no }}
                                            </li>
                                        @endforeach
                                    </ul>

                                </li>
                            @endforeach
                        </ul>
                    </li>
                @endforeach
            </ul>
        </li>

    </ul>

</div>

<script>
document.querySelectorAll('.toggle-menu').forEach(item => {
    item.addEventListener('click', () => {
        let nextUl = item.nextElementSibling;
        if (nextUl) nextUl.classList.toggle('d-none');
    });
});
</script> --}}
