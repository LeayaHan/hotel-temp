<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign In — TASKINN</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;1,9..40,300&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --cream:#faf7f2;--warm:#f0e9de;--stone:#c8bfb0;--muted:#8a7d6e;
            --ink:#2c2416;--gold:#b8913f;--gold-lt:#d4a84b;--rust:#a84b2f;
            --border:#e5ddd0;--shadow-lg:0 8px 40px 0 rgba(44,36,22,.18);--radius:10px;
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'DM Sans', sans-serif;
            color: var(--ink);
            min-height: 100vh;
            display: flex;
            overflow: hidden;
        }

        /* ── LEFT PANEL ── */
        .left-panel {
            flex: 1;
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 56px 52px;
            overflow: hidden;
            min-height: 100vh;
        }

        /* Deep ombre background */
        .left-panel::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(
                160deg,
                #5c3d1e 0%,
                #3b2510 25%,
                #2c1a08 50%,
                #1a0f04 75%,
                #0d0702 100%
            );
            z-index: 0;
        }

        /* Warm golden glow overlay */
        .left-panel::after {
            content: '';
            position: absolute;
            inset: 0;
            background:
                radial-gradient(ellipse 70% 60% at 30% 20%, rgba(184,145,63,.22) 0%, transparent 65%),
                radial-gradient(ellipse 50% 40% at 80% 80%, rgba(92,61,30,.4) 0%, transparent 60%),
                radial-gradient(ellipse 80% 50% at 50% 50%, rgba(44,26,8,.3) 0%, transparent 70%);
            z-index: 1;
        }

        /* Subtle grain texture */
        .grain {
            position: absolute;
            inset: 0;
            z-index: 2;
            opacity: .045;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noise'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noise)' opacity='1'/%3E%3C/svg%3E");
            background-size: 180px;
        }

        /* Decorative horizontal lines */
        .left-lines {
            position: absolute;
            inset: 0;
            z-index: 2;
            overflow: hidden;
        }
        .left-lines span {
            position: absolute;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(184,145,63,.18), transparent);
        }
        .left-lines span:nth-child(1) { top: 18%; }
        .left-lines span:nth-child(2) { top: 42%; }
        .left-lines span:nth-child(3) { top: 68%; }
        .left-lines span:nth-child(4) { top: 88%; }

        /* Floating orb accent */
        .orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            z-index: 1;
        }
        .orb-1 {
            width: 320px; height: 320px;
            background: rgba(184,145,63,.12);
            top: -60px; left: -80px;
        }
        .orb-2 {
            width: 240px; height: 240px;
            background: rgba(92,61,30,.25);
            bottom: 80px; right: -40px;
        }
        .orb-3 {
            width: 180px; height: 180px;
            background: rgba(184,145,63,.08);
            top: 45%; left: 55%;
        }

        .left-content {
            position: relative;
            z-index: 3;
            width: 100%;
            max-width: 420px;
        }

        .brand-mark {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 40px;
        }
        .brand-mark-icon {
            width: 40px; height: 40px;
            border: 1.5px solid rgba(184,145,63,.5);
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
        }
        .brand-mark-icon svg { opacity: .8; }
        .brand-mark-name {
            font-family: 'DM Serif Display', serif;
            font-size: 1.4rem;
            color: var(--gold);
            letter-spacing: .08em;
        }

        .left-headline {
            font-family: 'DM Serif Display', serif;
            font-size: clamp(2rem, 3.5vw, 2.8rem);
            line-height: 1.15;
            color: var(--cream);
            margin-bottom: 18px;
        }
        .left-headline em {
            font-style: italic;
            color: var(--gold-lt);
        }

        .left-tagline {
            font-size: .92rem;
            color: rgba(250,247,242,.45);
            line-height: 1.7;
            max-width: 340px;
            margin-bottom: 44px;
        }

        .left-stats {
            display: flex;
            gap: 32px;
        }
        .stat {
            display: flex;
            flex-direction: column;
            gap: 3px;
        }
        .stat-num {
            font-family: 'DM Serif Display', serif;
            font-size: 1.5rem;
            color: var(--gold-lt);
        }
        .stat-label {
            font-size: .75rem;
            color: rgba(250,247,242,.35);
            letter-spacing: .06em;
            text-transform: uppercase;
        }

        .left-divider {
            width: 48px;
            height: 1px;
            background: rgba(184,145,63,.35);
            margin-bottom: 44px;
        }

        /* ── RIGHT PANEL ── */
        .right-panel {
            width: 460px;
            flex-shrink: 0;
            background: var(--cream);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 48px 52px;
            position: relative;
            min-height: 100vh;
        }

        /* Subtle top border accent */
        .right-panel::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--gold), var(--gold-lt), var(--gold));
        }

        .form-wrap {
            width: 100%;
            max-width: 340px;
        }

        .form-logo {
            font-family: 'DM Serif Display', serif;
            font-size: 1.6rem;
            color: var(--gold);
            margin-bottom: 4px;
        }

        .form-sub {
            font-size: .88rem;
            color: var(--muted);
            margin-bottom: 36px;
        }

        .form-group { margin-bottom: 18px; }

        .form-label {
            display: block;
            font-size: .75rem;
            font-weight: 600;
            color: var(--muted);
            letter-spacing: .07em;
            text-transform: uppercase;
            margin-bottom: 7px;
        }

        .form-control {
            width: 100%;
            padding: 10px 14px;
            border: 1.5px solid var(--border);
            border-radius: 8px;
            background: #fff;
            color: var(--ink);
            font-family: 'DM Sans', sans-serif;
            font-size: .92rem;
            transition: border-color .15s, box-shadow .15s;
            outline: none;
        }
        .form-control:focus {
            border-color: var(--gold);
            box-shadow: 0 0 0 3px rgba(184,145,63,.15);
        }
        .form-error { color: var(--rust); font-size: .82rem; margin-top: 5px; }

        .remember-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 22px;
        }
        .checkbox-label {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: .87rem;
            color: var(--muted);
            cursor: pointer;
        }
        input[type=checkbox] { accent-color: var(--gold); width: 15px; height: 15px; }

        .forgot-link {
            font-size: .85rem;
            color: var(--gold);
            text-decoration: none;
            font-weight: 500;
        }
        .forgot-link:hover { text-decoration: underline; }

        .btn-submit {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            padding: 12px 20px;
            border-radius: 8px;
            background: var(--gold);
            color: var(--ink);
            font-weight: 700;
            font-size: .93rem;
            border: none;
            cursor: pointer;
            transition: background .15s, transform .1s;
            letter-spacing: .02em;
            font-family: 'DM Sans', sans-serif;
        }
        .btn-submit:hover { background: var(--gold-lt); transform: translateY(-1px); }
        .btn-submit:active { transform: translateY(0); }

        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
            padding: 10px 16px;
            border-radius: 8px;
            font-size: .88rem;
            margin-bottom: 18px;
        }
        .alert-success {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #a7f3d0;
            padding: 10px 16px;
            border-radius: 8px;
            font-size: .88rem;
            margin-bottom: 18px;
        }

        /* Responsive: stack on small screens */
        @media (max-width: 768px) {
            body { flex-direction: column; overflow: auto; }
            .left-panel { min-height: 260px; padding: 36px 28px; justify-content: center; }
            .left-stats { display: none; }
            .left-headline { font-size: 1.6rem; margin-bottom: 10px; }
            .left-tagline { display: none; }
            .right-panel { width: 100%; min-height: unset; padding: 40px 28px; }
        }
    </style>
</head>
<body>

{{-- ── LEFT PANEL ── --}}
<div class="left-panel">
    <div class="grain"></div>
    <div class="left-lines">
        <span></span><span></span><span></span><span></span>
    </div>
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>

    <div class="left-content">
        <div class="brand-mark">
            <div class="brand-mark-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#b8913f" stroke-width="1.8">
                    <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                    <polyline points="9,22 9,12 15,12 15,22"/>
                </svg>
            </div>
            <span class="brand-mark-name">TASKINN</span>
        </div>

        <h1 class="left-headline">
            Hospitality,<br>
            <em>effortlessly</em><br>
            managed.
        </h1>

        <p class="left-tagline">
            From housekeeping to guest services — every request tracked, every task delivered with care.
        </p>

        <div class="left-divider"></div>

        <div class="left-stats">
            <div class="stat">
                <span class="stat-num">100%</span>
                <span class="stat-label">Request Tracking</span>
            </div>
            <div class="stat">
                <span class="stat-num">24/7</span>
                <span class="stat-label">Guest Support</span>
            </div>
            <div class="stat">
                <span class="stat-num">Real-time</span>
                <span class="stat-label">Status Updates</span>
            </div>
        </div>
    </div>
</div>

{{-- ── RIGHT PANEL ── --}}
<div class="right-panel">
    <div class="form-wrap">

        <div class="form-logo">Welcome back</div>
        <div class="form-sub">Sign in to your TASKINN account</div>

        @if(session('status'))
            <div class="alert-success">{{ session('status') }}</div>
        @endif

        <x-auth-session-status :status="session('status')" />

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="form-group">
                <label class="form-label" for="email">Email Address</label>
                <input id="email" class="form-control" type="email" name="email"
                       value="{{ old('email') }}" required autofocus autocomplete="username"
                       placeholder="you@example.com">
                @error('email')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="password">Password</label>
                <input id="password" class="form-control" type="password" name="password"
                       required autocomplete="current-password" placeholder="••••••••">
                @error('password')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            <div class="remember-row">
                <label class="checkbox-label">
                    <input type="checkbox" name="remember"> Remember me
                </label>
                @if (Route::has('password.request'))
                    <a class="forgot-link" href="{{ route('password.request') }}">Forgot password?</a>
                @endif
            </div>

            <button type="submit" class="btn-submit">Sign In</button>
        </form>

    </div>
</div>

</body>
</html>