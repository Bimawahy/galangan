<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') · Galian Pasir POS</title>
    <style>
        :root {
            --bg: #f3e6c8; --panel: #fbf3df; --border: #d9c394;
            --accent: #a8702c; --accent-2: #d9a45c;
            --text: #4a3b26; --muted: #8a7655; --danger: #c0392b; --ok: #4f7a3a;
        }
        * { box-sizing: border-box; }
        body { margin: 0; font-family: 'Segoe UI', system-ui, sans-serif; background: var(--bg); color: var(--text); display: flex; min-height: 100vh; }
        a { color: inherit; text-decoration: none; }
        aside { width: 230px; background: var(--panel); border-right: 1px solid var(--border); padding: 22px 14px; display: flex; flex-direction: column; position: sticky; top: 0; height: 100vh; }
        .brand { display: flex; align-items: center; gap: 10px; font-weight: 700; font-size: 17px; margin: 0 8px 26px; }
        .brand i { width: 38px; height: 38px; border-radius: 10px; background: linear-gradient(135deg, var(--accent), var(--accent-2)); display: grid; place-items: center; font-style: normal; font-size: 20px; }
        nav a { display: block; padding: 11px 14px; border-radius: 10px; margin-bottom: 4px; font-weight: 500; }
        nav a:hover { background: rgba(168,112,44,.12); }
        nav a.on { background: linear-gradient(135deg, var(--accent), var(--accent-2)); color: #fff; }
        .who { margin-top: auto; font-size: 13px; color: var(--muted); padding: 0 8px; }
        .who form { margin-top: 8px; }
        main { flex: 1; padding: 28px 32px; min-width: 0; }
        h1 { margin: 0 0 20px; font-size: 24px; }
        .card { background: var(--panel); border: 1px solid var(--border); border-radius: 14px; padding: 20px; margin-bottom: 20px; }
        .card h2 { margin: 0 0 14px; font-size: 16px; }
        .grid { display: grid; gap: 16px; }
        .g4 { grid-template-columns: repeat(auto-fit, minmax(190px, 1fr)); }
        .g2 { grid-template-columns: repeat(auto-fit, minmax(340px, 1fr)); }
        .stat small { color: var(--muted); display: block; margin-bottom: 6px; }
        .stat b { font-size: 24px; }
        table { width: 100%; border-collapse: collapse; font-size: 14px; }
        th, td { text-align: left; padding: 10px 8px; border-bottom: 1px solid var(--border); }
        th { color: var(--muted); font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: .04em; }
        td.r, th.r { text-align: right; }
        .btn, button.btn { display: inline-block; padding: 9px 16px; border: 0; border-radius: 9px; cursor: pointer; font: inherit; font-weight: 600; background: linear-gradient(135deg, var(--accent), var(--accent-2)); color: #fff; }
        .btn.ghost { background: transparent; border: 1px solid var(--border); color: var(--text); }
        .btn.danger { background: var(--danger); }
        .btn.sm { padding: 5px 11px; font-size: 13px; }
        input, select, textarea { font: inherit; color: var(--text); background: var(--bg); border: 1px solid var(--border); border-radius: 9px; padding: 9px 12px; width: 100%; }
        input:focus, select:focus, textarea:focus { outline: 0; border-color: var(--accent); box-shadow: 0 0 0 3px rgba(168,112,44,.2); }
        label { display: block; font-size: 13px; color: var(--muted); margin-bottom: 5px; }
        .field { margin-bottom: 14px; }
        .row { display: flex; gap: 10px; align-items: end; flex-wrap: wrap; }
        .row > * { flex: 1; min-width: 120px; }
        .row > .btn { flex: 0 0 auto; }
        .flash { background: #e3edcf; border: 1px solid #b7cc92; color: #3d5a28; padding: 10px 14px; border-radius: 10px; margin-bottom: 16px; }
        .err { color: var(--danger); font-size: 13px; margin-top: 4px; }
        .badge { display: inline-block; padding: 2px 9px; border-radius: 99px; font-size: 12px; font-weight: 600; background: rgba(168,112,44,.15); }
        .badge.low { background: rgba(192,57,43,.15); color: var(--danger); }
        .badge.ok { background: rgba(79,122,58,.18); color: var(--ok); }
        .bars { display: flex; align-items: end; gap: 10px; height: 170px; }
        .bars div { flex: 1; text-align: center; font-size: 11px; color: var(--muted); display: flex; flex-direction: column; justify-content: end; height: 100%; }
        .bars span { display: block; background: linear-gradient(180deg, var(--accent-2), var(--accent)); border-radius: 6px 6px 0 0; min-height: 3px; margin-bottom: 4px; }
        .muted { color: var(--muted); }
        .pager nav { display: flex; gap: 6px; margin-top: 14px; }
        .pager svg { width: 16px; }
        @media (max-width: 800px) { body { flex-direction: column; } aside { position: static; width: 100%; height: auto; } main { padding: 18px; } }
        @media print { aside, .noprint { display: none !important; } body { background: #fff; } main { padding: 0; } .card { border: 0; } }
    </style>
    @stack('head')
</head>
<body>
@php $role = auth()->user()->activeRole(); @endphp
<aside>
    <div class="brand"><i>⛰️</i> Galian Pasir</div>
    <nav>
        <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'on' : '' }}">📊 Dashboard</a>
        @if (in_array($role, ['admin', 'master']))
            <a href="{{ route('pos') }}" class="{{ request()->routeIs('pos') ? 'on' : '' }}">🧾 Kasir (POS)</a>
            <a href="{{ route('products.index') }}" class="{{ request()->routeIs('products.*') ? 'on' : '' }}">🪨 Produk &amp; Stok</a>
        @endif
        <a href="{{ route('sales.index') }}" class="{{ request()->routeIs('sales.*') ? 'on' : '' }}">📒 Penjualan</a>
        @if (auth()->user()->role === 'master')
            <a href="{{ route('master') }}" class="{{ request()->routeIs('master') ? 'on' : '' }}">🛠️ Master</a>
        @endif
    </nav>
    <div class="who">
        {{ auth()->user()->name }}<br><span>{{ ucfirst(auth()->user()->role) }}</span>
        @if ($role !== auth()->user()->role)<br><span class="badge">tampilan: {{ $role }}</span>@endif
        <form method="POST" action="{{ route('logout') }}">@csrf
            <button class="btn ghost sm" type="submit">Keluar</button>
        </form>
    </div>
</aside>
<main>
    @if ($role !== auth()->user()->role)
        <div class="flash noprint">Master sedang melihat sebagai <b>{{ $role }}</b>.
            <form method="POST" action="{{ route('master.view', 'master') }}" style="display:inline">@csrf <button class="btn sm">Kembali ke Master</button></form>
        </div>
    @endif
    @if (session('ok'))<div class="flash">{{ session('ok') }}</div>@endif
    @yield('content')
</main>
</body>
</html>
