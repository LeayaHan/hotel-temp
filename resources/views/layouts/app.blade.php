<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'TASKINN') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;1,9..40,300&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --cream:#faf7f2; --warm:#f0e9de; --stone:#c8bfb0; --muted:#8a7d6e;
            --ink:#2c2416; --gold:#b8913f; --gold-lt:#d4a84b; --rust:#a84b2f;
            --success:#3a7d5a; --card-bg:#ffffff; --border:#e5ddd0;
            --shadow:0 2px 16px 0 rgba(44,36,22,.08);
            --shadow-lg:0 8px 40px 0 rgba(44,36,22,.13);
            --radius:10px; --nav-h:64px;
            --admin-accent:#7c3aed;
            --staff-accent:#0369a1;
        }
        *,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
        html{scroll-behavior:smooth}
        body{font-family:'DM Sans',sans-serif;background:var(--cream);color:var(--ink);min-height:100vh;font-size:15px;line-height:1.6}

        /* ── NAVBAR ── */
        .navbar{position:sticky;top:0;z-index:100;height:var(--nav-h);background:var(--ink);display:flex;align-items:center;padding:0 32px;box-shadow:0 2px 24px rgba(44,36,22,.22);}
        .navbar-brand{font-family:'DM Serif Display',serif;font-size:1.35rem;color:var(--gold-lt);text-decoration:none;letter-spacing:.01em;margin-right:40px;white-space:nowrap;display:flex;align-items:center;gap:10px;}
        .role-badge{font-size:.65rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;padding:2px 8px;border-radius:99px;margin-left:4px;}
        .role-badge.admin{background:var(--admin-accent);color:#fff;}
        .role-badge.staff{background:var(--staff-accent);color:#fff;}
        .role-badge.guest{background:var(--gold);color:var(--ink);}
        .navbar-links{display:flex;align-items:center;gap:4px;flex:1;}
        .nav-link{display:inline-flex;align-items:center;gap:6px;padding:7px 14px;border-radius:7px;color:rgba(255,255,255,.65);text-decoration:none;font-size:.88rem;font-weight:500;letter-spacing:.01em;transition:background .15s,color .15s;}
        .nav-link:hover{background:rgba(255,255,255,.08);color:#fff;}
        .nav-link.active{background:var(--gold);color:var(--ink);}
        .nav-link svg{width:15px;height:15px;flex-shrink:0;}
        .navbar-right{display:flex;align-items:center;gap:12px;margin-left:auto;}
        .nav-user{display:flex;align-items:center;gap:8px;color:rgba(255,255,255,.8);font-size:.85rem;}
        .nav-avatar{width:32px;height:32px;border-radius:50%;background:var(--gold);display:flex;align-items:center;justify-content:center;font-family:'DM Serif Display',serif;font-size:.95rem;color:var(--ink);font-weight:700;}
        .nav-avatar.admin{background:var(--admin-accent);color:#fff;}
        .nav-avatar.staff{background:var(--staff-accent);color:#fff;}
        .btn-logout{padding:6px 14px;border-radius:7px;background:rgba(255,255,255,.1);color:rgba(255,255,255,.75);border:1px solid rgba(255,255,255,.12);font-size:.83rem;font-weight:500;cursor:pointer;text-decoration:none;transition:background .15s,color .15s;}
        .btn-logout:hover{background:rgba(255,255,255,.18);color:#fff;}

        /* ── PAGE ── */
        .page-wrap{max-width:1100px;margin:0 auto;padding:36px 24px 60px;}
        .page-header{margin-bottom:28px;}
        .page-header h1{font-family:'DM Serif Display',serif;font-size:1.9rem;color:var(--ink);line-height:1.2;}
        .page-header p{color:var(--muted);margin-top:4px;font-size:.92rem;}

        /* ── STATS ── */
        .stats-strip{display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:28px;}
        @media(max-width:700px){.stats-strip{grid-template-columns:repeat(2,1fr);}}
        .stat-card{background:var(--card-bg);border:1px solid var(--border);border-radius:var(--radius);padding:20px 22px;box-shadow:var(--shadow);}
        .stat-card.highlight{border-color:var(--gold);border-left:4px solid var(--gold);}
        .stat-label{font-size:.78rem;font-weight:600;letter-spacing:.07em;text-transform:uppercase;color:var(--muted);margin-bottom:6px;}
        .stat-value{font-family:'DM Serif Display',serif;font-size:2rem;color:var(--ink);line-height:1;}

        /* ── CARD ── */
        .card{background:var(--card-bg);border:1px solid var(--border);border-radius:var(--radius);padding:28px 32px;box-shadow:var(--shadow);margin-bottom:24px;}
        .card h2{font-family:'DM Serif Display',serif;font-size:1.25rem;margin-bottom:18px;color:var(--ink);}
        .card-header{display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:20px;}
        .card-header h2{margin-bottom:0;}

        /* ── BUTTONS ── */
        .btn{display:inline-flex;align-items:center;gap:7px;padding:9px 20px;border-radius:8px;background:var(--gold);color:var(--ink);font-weight:600;font-size:.88rem;text-decoration:none;border:none;cursor:pointer;transition:background .15s,transform .1s;letter-spacing:.01em;}
        .btn:hover{background:var(--gold-lt);transform:translateY(-1px);}
        .btn:active{transform:translateY(0);}
        .btn-secondary{background:var(--warm);color:var(--ink);border:1px solid var(--border);}
        .btn-secondary:hover{background:var(--stone);}
        .btn-danger{background:var(--rust);color:#fff;}
        .btn-danger:hover{background:#c0572e;}
        .btn-sm{padding:5px 12px;font-size:.82rem;border-radius:6px;}

        /* ── TABLE ── */
        .table-wrap{overflow-x:auto;border-radius:var(--radius);border:1px solid var(--border);}
        table{width:100%;border-collapse:collapse;background:var(--card-bg);}
        thead{background:var(--warm);}
        th{padding:11px 16px;text-align:left;font-size:.78rem;font-weight:600;letter-spacing:.06em;text-transform:uppercase;color:var(--muted);border-bottom:1px solid var(--border);white-space:nowrap;}
        td{padding:13px 16px;font-size:.9rem;color:var(--ink);border-bottom:1px solid var(--border);vertical-align:middle;}
        tr:last-child td{border-bottom:none;}
        tbody tr:hover{background:#fdfaf6;}

        /* ── BADGES ── */
        .badge{display:inline-block;padding:3px 10px;border-radius:99px;font-size:.75rem;font-weight:600;letter-spacing:.04em;}
        .badge-open{background:#fef3c7;color:#92400e;}
        .badge-progress{background:#dbeafe;color:#1e40af;}
        .badge-done{background:#d1fae5;color:#065f46;}
        .badge-cancelled{background:#fee2e2;color:#991b1b;}
        .badge-normal{background:var(--warm);color:var(--muted);}
        .badge-high{background:#fef3c7;color:#92400e;}
        .badge-urgent{background:#fee2e2;color:#991b1b;}
        .badge-admin{background:#ede9fe;color:#5b21b6;}
        .badge-staff{background:#e0f2fe;color:#075985;}
        .badge-guest{background:#fef3c7;color:#92400e;}

        /* ── FORM ── */
        .form-group{margin-bottom:20px;}
        .form-label{display:block;font-size:.82rem;font-weight:600;color:var(--muted);letter-spacing:.05em;text-transform:uppercase;margin-bottom:7px;}
        .form-control{width:100%;padding:10px 14px;border:1.5px solid var(--border);border-radius:8px;background:var(--cream);color:var(--ink);font-family:'DM Sans',sans-serif;font-size:.92rem;transition:border-color .15s,box-shadow .15s;outline:none;}
        .form-control:focus{border-color:var(--gold);box-shadow:0 0 0 3px rgba(184,145,63,.15);background:#fff;}
        select.form-control{cursor:pointer;}
        textarea.form-control{resize:vertical;min-height:90px;}
        .form-error{color:var(--rust);font-size:.82rem;margin-top:5px;}
        .form-grid-2{display:grid;grid-template-columns:1fr 1fr;gap:0 20px;}
        @media(max-width:600px){.form-grid-2{grid-template-columns:1fr;}}
        .form-actions{display:flex;align-items:center;gap:12px;padding-top:8px;}

        /* ── ALERTS ── */
        .alert{padding:12px 18px;border-radius:8px;margin-bottom:20px;font-size:.9rem;display:flex;align-items:center;gap:10px;}
        .alert-success{background:#d1fae5;color:#065f46;border:1px solid #a7f3d0;}
        .alert-error{background:#fee2e2;color:#991b1b;border:1px solid #fca5a5;}

        /* ── EMPTY STATE ── */
        .empty-state{text-align:center;padding:56px 24px;color:var(--muted);}
        .empty-state svg{margin:0 auto 16px;opacity:.35;}
        .empty-state h3{font-family:'DM Serif Display',serif;font-size:1.2rem;color:var(--ink);margin-bottom:6px;}

        /* ── PAGINATION ── */
        .pagination{margin-top:20px;}
        .pagination .page-link{display:inline-flex;align-items:center;justify-content:center;min-width:34px;height:34px;padding:0 10px;border-radius:7px;border:1px solid var(--border);background:var(--card-bg);color:var(--ink);font-size:.85rem;text-decoration:none;transition:background .12s;margin:0 2px;}
        .pagination .page-link:hover{background:var(--warm);}
        .pagination .page-link.active{background:var(--gold);border-color:var(--gold);color:var(--ink);font-weight:700;}

        /* ── AUTH ── */
        .auth-box{background:var(--cream);border-radius:14px;padding:44px 44px 36px;width:100%;max-width:420px;box-shadow:var(--shadow-lg);}
        .auth-logo{font-family:'DM Serif Display',serif;font-size:1.6rem;color:var(--gold);text-align:center;margin-bottom:6px;}
        .auth-sub{text-align:center;color:var(--muted);font-size:.88rem;margin-bottom:30px;}
        .auth-footer{text-align:center;margin-top:22px;font-size:.85rem;color:var(--muted);}
        .auth-footer a{color:var(--gold);text-decoration:none;font-weight:600;}
    </style>
</head>
<body>

@auth
@php $role = Auth::user()->role ?? 'guest'; @endphp
<nav class="navbar">
    <a href="{{ route('dashboard') }}" class="navbar-brand">
        TASKINN
        <span class="role-badge {{ $role }}">{{ ucfirst($role) }}</span>
    </a>

    <div class="navbar-links">
        {{-- Dashboard (all roles) --}}
        <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
            Dashboard
        </a>

        @if($role === 'guest')
            <a href="{{ route('my-services.create') }}" class="nav-link {{ request()->routeIs('my-services.create') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                New Request
            </a>
            <a href="{{ route('my-services.index') }}" class="nav-link {{ request()->routeIs('my-services.*') && !request()->routeIs('my-services.create') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14,2 14,8 20,8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                My Requests
            </a>
        @endif

        @if($role === 'staff')
            <a href="{{ route('service-requests.index') }}" class="nav-link {{ request()->routeIs('service-requests.*') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14,2 14,8 20,8"/></svg>
                Service Requests
            </a>
            <a href="{{ route('guests.index') }}" class="nav-link {{ request()->routeIs('guests.*') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                Guests
            </a>
        @endif

        @if($role === 'admin')
            <a href="{{ route('service-requests.index') }}" class="nav-link {{ request()->routeIs('service-requests.*') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14,2 14,8 20,8"/></svg>
                Requests
            </a>
            <a href="{{ route('guests.index') }}" class="nav-link {{ request()->routeIs('guests.*') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                Guests
            </a>
            <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
                Users
            </a>
        @endif
    </div>

    <div class="navbar-right">
        <div class="nav-user">
            <div class="nav-avatar {{ $role }}">{{ strtoupper(substr(Auth::user()->name,0,1)) }}</div>
            <span style="max-width:120px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ Auth::user()->name }}</span>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn-logout">Sign out</button>
        </form>
    </div>
</nav>
@endauth

<div class="{{ auth()->check() ? 'page-wrap' : '' }}">
    @if(session('success'))
        <div class="alert alert-success">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20,6 9,17 4,12"/></svg>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-error">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
            {{ session('error') }}
        </div>
    @endif

    @yield('content')
</div>

</body>
</html>