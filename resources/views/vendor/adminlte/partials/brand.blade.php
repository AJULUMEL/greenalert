<style>
    /* When AdminLTE sidebar is collapsed, only show the logo image */
    body.sidebar-collapse .brand-text { display: none !important; }
    /* Always hide brand text on very small screens */
    @media (max-width: 576px) { .brand-text { display: none !important; } }

    /* ensure brand link has consistent padding and centers content */
        /* left padding reduced so logo sits closer to sidebar edge without overflowing */
        .brand-link { padding: .5rem .8rem .5rem .25rem !important; align-items: center !important; display:flex; gap: .5rem; }
    body.sidebar-collapse .brand-link { justify-content: center !important; }

    /* Make sure the image fits the container and isn't cropped into a circle */
        .brand-image { transition: width .18s ease, height .18s ease, opacity .18s ease; width:auto; height:auto; max-width:120px; max-height:44px; object-fit:contain; display:block; margin-left: 0; }

    /* Let collapsed sidebar adapt to logo size: use CSS var for easy tuning */
    .brand-link { --brand-logo-size: 34px; }
    .brand-image { width: auto; height: auto; max-height:44px; }

    /* Responsive: adjust logo sizes to prevent clipping */
        /* sizes tuned to fit entirely inside sidebar */
        body:not(.sidebar-collapse) .brand-image { max-width: 100px; max-height:36px; }
        body.sidebar-collapse .brand-image { max-width: 32px; max-height:26px; }
    /* Smooth transition when toggling */
    .brand-image { transition: max-width .18s ease, max-height .18s ease; }

    @media (max-width: 768px) {
        .brand-image { max-width: 90px; max-height:36px; }
    }

    /* Widen the expanded sidebar and adjust content margins accordingly
       - Use media query to avoid affecting mobile layouts
       - Keep a narrower width when sidebar is collapsed so icons-only view remains compact
    */
    @media (min-width: 768px) {
        /* stronger selectors and larger sizes to ensure the layout uses the wider sidebar */
        .wrapper .main-sidebar,
        .main-sidebar {
            width: 340px !important;
            position: fixed !important;
            top: 0; left: 0; height: 100vh; overflow: visible !important;
            z-index: 2000 !important;
        }
        .content-wrapper, .main-footer, .main-header, .main-header .navbar, .main-header .navbar-success { margin-left: 340px !important; transition: margin-left .18s ease; }

        /* collapsed (icons-only) sidebar width */
        body.sidebar-collapse .wrapper .main-sidebar,
        body.sidebar-collapse .main-sidebar { width: 85px !important; }
        body.sidebar-collapse .content-wrapper, body.sidebar-collapse .main-footer, body.sidebar-collapse .main-header, body.sidebar-collapse .main-header .navbar, body.sidebar-collapse .main-header .navbar-success { margin-left: 85px !important; }
    }
    /* Keep brand centered when sidebar is collapsed; avoid forcing sidebar width (restore default behavior) */
    body.sidebar-collapse .brand-link { justify-content: center; padding-left: .5rem; padding-right: .5rem; }
    /* Subtle navbar color override: softer GreenFields for topnav */
    .main-header .navbar-success {
        background-color: #316b4a !important; /* muted green */
        border-bottom: 1px solid rgba(0,0,0,0.06);
    }
    .main-header .navbar-success .nav-link,
    .main-header .navbar-success .brand-text,
    .main-header .navbar-success .navbar-nav .nav-link {
        color: rgba(255,255,255,0.95) !important;
    }
    .main-header .navbar-success .nav-link:hover { color: rgba(255,255,255,0.95) !important; opacity: .92; }

    /* Sidebar active / hover tuning: softer green and rounded pills */
    .nav-sidebar .nav-link { border-radius: .5rem; transition: background-color .16s ease, color .16s ease; }
    .nav-sidebar .nav-link .nav-icon { opacity: .95; }
    .nav-sidebar .nav-link.active {
        background-color: var(--gf-accent) !important; /* muted accent */
        color: #fff !important;
        box-shadow: none !important;
    }
    .nav-sidebar .nav-link:hover:not(.active) {
        background-color: rgba(63,122,87,0.08) !important; /* subtle hover */
        color: #fff !important;
    }
    /* Ensure active link icon contrast */
    .nav-sidebar .nav-link.active .nav-icon { color: rgba(255,255,255,0.95) !important; }
    /* override img-circle to keep rounded rectangle instead of full circle */
    .brand-image.img-circle { border-radius: 6px !important; }

    /* small tweak for very small sidebars */
    body.sidebar-collapse .brand-image { width:auto; height:auto; max-height:34px; margin-left: 0; }
    /* nudge logo slightly left to reduce perceived gap */
    .brand-link .brand-image { margin-left: 6px; }
    /* If sidebar container clips, allow overflow visible to show entire logo */
    .main-sidebar, .brand-link { overflow: visible !important; }

    /* Ensure sidebar sits above page content so it isn't visually overlapped */
    .main-sidebar { position: relative; }
    .content-wrapper { position: relative; z-index: 0; }
</style>

<a href="{{ route('dashboard') }}" class="brand-link d-flex align-items-center">
    @if(config('adminlte.logo_img'))
        <img src="{{ asset(config('adminlte.logo_img')) }}" alt="{{ config('adminlte.logo_img_alt') }}" class="brand-image img-circle elevation-3" style="opacity:.9;">
    @endif

    <span class="brand-text font-weight-light ms-2 d-none d-sm-inline">{!! config('adminlte.logo') !!}</span>
</a>
