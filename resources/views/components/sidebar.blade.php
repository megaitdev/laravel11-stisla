@auth
    <div class="main-sidebar sidebar-style-2">
        <aside id="sidebar-wrapper">
            <div class="sidebar-brand">
                <a href="">MEGADEV</a>
            </div>
            <div class="sidebar-brand sidebar-brand-sm">
                <a href="">MD</a>
            </div>
            <ul class="sidebar-menu">
                <li class="menu-header">Menu</li>
                <li class="{{ $slug == 'dashboard' ? 'active' : '' }}">
                    <a class="nav-link" href="{{ url('dashboard') }}"><i class="fas fa-fire"></i><span>Dashboard</span></a>
                </li>
                <li class="{{ $slug == 'profile' ? 'active' : '' }}">
                    <a class="nav-link" href="{{ url('profile') }}"><i class="far fa-user"></i>
                        <span>Profile</span></a>
                </li>
            </ul>
        </aside>
    </div>
@endauth
