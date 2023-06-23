<aside class="menu-sidebar dark_sidebar d-none d-lg-block">
    <div class="logo">
        <a href="{{ route('dashboard') }}">
            <img src="{{ asset('public/images/logo/logo.png') }}" alt="logo" />
        </a>
    </div>
    <div class="menu-sidebar__content js-scrollbar1">
        <nav class="navbar-sidebar">
            <ul class="list-unstyled navbar__list">
                <li class="{{ ( request()->is('/') ) ? 'active' : '' }}" ><a href="{{ route('dashboard') }}"><i class="fas fa-tachometer-alt"></i>Home</a></li>
                <li class="{{ ( request()->is('deal') ) ? 'active' : '' }}" ><a href="{{ route('deal') }}"><i class="fa fa-thumbs-up"></i>Deal</a></li>
                <li><a href="/"><i class="fa fa-cogs" aria-hidden="true"></i>Ad Operation</a></li>
                <li class="{{ ( request()->is('campaign') ) ? 'active' : '' }}"><a href="{{ route('campaign') }}"><i class="fa fa-bullhorn" aria-hidden="true"></i>Campaign</a></li>
                <li><a href="/"><i class="fa fa-user"></i>Advertiser Profile</a></li>
                <li><a href="/"><i class="fa fa-file"></i>Reports</a></li>
                <li><a href="/"><i class="fa fa-users" aria-hidden="true"></i>Administration</a></li>
            </ul>
        </nav>
    </div>
</aside>