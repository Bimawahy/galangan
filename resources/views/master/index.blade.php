@extends('layouts.admin')
@section('title', 'Master')
@section('content')
<h1>Master · Cek Sistem</h1>

<div class="card">
    <h2>Lihat sebagai role</h2>
    <p class="muted" style="margin-top:0">Buka aplikasi dengan tampilan dan hak akses role lain untuk memastikan semuanya berjalan normal.</p>
    <div class="row" style="justify-content:flex-start">
        @foreach (['admin', 'owner'] as $r)
            <form method="POST" action="{{ route('master.view', $r) }}" style="flex:0 0 auto">@csrf
                <button class="btn">Lihat sebagai {{ ucfirst($r) }}</button>
            </form>
        @endforeach
    </div>
</div>

<div class="card">
    <h2>Status sistem</h2>
    <table>
        @foreach ($checks as $c)
            <tr>
                <td>{{ $c['name'] }}</td>
                <td class="muted">{{ $c['info'] }}</td>
                <td class="r"><span class="badge {{ $c['ok'] ? 'ok' : 'low' }}">{{ $c['ok'] ? 'OK' : 'Bermasalah' }}</span></td>
            </tr>
        @endforeach
    </table>
</div>

<div class="card">
    <h2>Akses halaman per role</h2>
    <table>
        <tr><th>Halaman</th>@foreach ($roles as $r)<th class="r">{{ ucfirst($r) }}</th>@endforeach</tr>
        @foreach ($pages as $p)
            <tr>
                <td><a href="{{ $p['uri'] }}"><u>{{ $p['uri'] }}</u></a></td>
                @foreach ($roles as $r)
                    <td class="r">{{ in_array($r, $p['roles']) ? '✔' : '—' }}</td>
                @endforeach
            </tr>
        @endforeach
    </table>
</div>
@endsection
