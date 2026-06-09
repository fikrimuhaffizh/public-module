<header id="header" class="header d-flex align-items-center fixed-top">
    <div class="container-fluid container-xl position-relative d-flex align-items-center justify-content-between">

      <a href="{{ route('public.index') }}" class="logo d-flex align-items-center me-auto me-xl-0">
        <h1 class="sitename">
            <a href="{{ route('public.index') }}" class="navbar-brand navbar-brand-autodark">
                <img src="{{ asset('images/logo-apps.png') }}" width="110" height="32" alt="{{ config('app.name') }}" class="navbar-brand-image">
            </a>
        </h1>
      </a>

      <nav id="navmenu" class="navmenu">
        <ul>
          <li><a href="{{ route('public.index') }}" class="{{ request()->routeIs('public.index') ? 'active' : '' }}">Home</a></li>
          <li><a href="{{ route('public.announcements.index') }}" class="{{ request()->routeIs('public.announcements.index') ? 'active' : '' }}">Announcements</a></li>
        </ul>
        <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
      </nav>

      <a class="btn-getstarted" href="{{ route('login') }}">Login</a>

    </div>
  </header>
