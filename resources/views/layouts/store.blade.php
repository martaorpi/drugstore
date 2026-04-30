<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Panel') — {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700|jetbrains-mono:400,500&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #0f1419;
            --surface: #1a222d;
            --surface2: #243040;
            --border: #2d3a4d;
            --text: #e8edf4;
            --muted: #8b9cb3;
            --accent: #22c55e;
            --accent-dim: #15803d;
            --warn: #f59e0b;
            --danger: #ef4444;
            --radius: 12px;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            font-family: "Instrument Sans", ui-sans-serif, system-ui, sans-serif;
            background: linear-gradient(165deg, #0a0e14 0%, var(--bg) 40%, #121a24 100%);
            color: var(--text);
            line-height: 1.5;
        }
        a { color: var(--accent); text-decoration: none; }
        a:hover { text-decoration: underline; }
        .topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.75rem 1.25rem;
            background: rgba(26, 34, 45, 0.92);
            border-bottom: 1px solid var(--border);
            position: sticky;
            top: 0;
            z-index: 50;
            backdrop-filter: blur(8px);
        }
        .brand { font-weight: 700; font-size: 1.05rem; letter-spacing: -0.02em; }
        .brand span { color: var(--accent); }
        .nav-links { display: flex; gap: 1rem; align-items: center; flex-wrap: wrap; }
        .nav-links a.secondary { color: var(--muted); font-size: 0.9rem; }
        main { max-width: 1100px; margin: 0 auto; padding: 1.25rem; }
        .card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 1.25rem;
            margin-bottom: 1rem;
        }
        h1 { font-size: 1.5rem; margin: 0 0 0.5rem; font-weight: 700; }
        h2 { font-size: 1.1rem; margin: 0 0 1rem; color: var(--muted); font-weight: 600; }
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.4rem;
            padding: 0.55rem 1rem;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.9rem;
            border: none;
            cursor: pointer;
            font-family: inherit;
        }
        .btn-primary { background: var(--accent); color: #052e16; }
        .btn-primary:hover { background: #4ade80; }
        .btn-primary:disabled { opacity: 0.5; cursor: not-allowed; }
        .btn-ghost { background: transparent; color: var(--muted); border: 1px solid var(--border); }
        .btn-ghost:hover { color: var(--text); border-color: var(--muted); }
        .btn-danger { background: rgba(239, 68, 68, 0.15); color: #fca5a5; border: 1px solid rgba(239, 68, 68, 0.35); }
        input, select {
            width: 100%;
            padding: 0.65rem 0.85rem;
            border-radius: 8px;
            border: 1px solid var(--border);
            background: var(--surface2);
            color: var(--text);
            font-size: 1rem;
            font-family: inherit;
        }
        input:focus { outline: 2px solid var(--accent-dim); outline-offset: 1px; }
        .mono { font-family: "JetBrains Mono", monospace; font-size: 0.85rem; }
        .muted { color: var(--muted); font-size: 0.875rem; }
        .grid2 { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 1rem; }
        .stat {
            background: var(--surface2);
            border-radius: var(--radius);
            padding: 1rem;
            border: 1px solid var(--border);
        }
        .stat strong { display: block; font-size: 1.65rem; font-weight: 700; color: var(--accent); }
        .flash { padding: 0.75rem 1rem; border-radius: 8px; margin-bottom: 1rem; display: none; }
        .flash.ok { display: block; background: rgba(34, 197, 94, 0.12); border: 1px solid rgba(34, 197, 94, 0.35); color: #86efac; }
        .flash.err { display: block; background: rgba(239, 68, 68, 0.12); border: 1px solid rgba(239, 68, 68, 0.35); color: #fecaca; }
        table { width: 100%; border-collapse: collapse; font-size: 0.9rem; }
        th, td { text-align: left; padding: 0.5rem 0.35rem; border-bottom: 1px solid var(--border); }
        th { color: var(--muted); font-weight: 600; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.04em; }
    </style>
    @stack('styles')
</head>
<body>
    <header class="topbar">
        <div class="brand"><span>●</span> {{ config('app.name') }}</div>
        <nav class="nav-links">
            <a href="{{ route('panel.index') }}">Inicio</a>
            <a href="{{ route('panel.pos') }}">Caja rápida</a>
            <a href="{{ route('gestion.index') }}">Gestión negocio</a>
            <a href="{{ backpack_url('dashboard') }}" class="secondary">Administración CRUD</a>
        </nav>
    </header>
    <main>
        @yield('content')
    </main>
    @stack('scripts')
</body>
</html>
