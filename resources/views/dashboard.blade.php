@extends('layouts.admin')
@section('title', 'Dashboard')
@section('content')
@php $rp = fn ($n) => 'Rp '.number_format($n, 0, ',', '.'); $max = max(1, $chart->max('total'));
   $role = auth()->user()->activeRole(); $own = in_array($role, ['owner', 'master']); $ops = in_array($role, ['admin', 'master']); @endphp
<h1>Halo, {{ auth()->user()->name }} 👋</h1>
{{-- owner: laporan keuangan; admin: operasional harian; master: keduanya --}}

<div class="grid g4" style="margin-bottom:20px">
    <div class="card stat" style="margin:0"><small>Omzet hari ini</small><b>{{ $rp($stats['omzet_hari']) }}</b></div>
    <div class="card stat" style="margin:0"><small>Transaksi hari ini</small><b>{{ $stats['trx_hari'] }}</b></div>
    @if ($own)<div class="card stat" style="margin:0"><small>Omzet bulan ini</small><b>{{ $rp($stats['omzet_bulan']) }}</b></div>@endif
    <div class="card stat" style="margin:0"><small>Piutang (tempo)</small><b style="color:var(--danger)">{{ $rp($stats['piutang']) }}</b></div>
</div>

<div class="grid g2">
    @if ($own)
    <div class="card">
        <h2>Omzet 7 hari terakhir</h2>
        <div class="bars">
            @foreach ($chart as $c)
                <div title="{{ $rp($c['total']) }}">
                    <span style="height: {{ round($c['total'] / $max * 100) }}%"></span>{{ $c['label'] }}
                </div>
            @endforeach
        </div>
    </div>

    <div class="card">
        <h2>Terlaris bulan ini</h2>
        <table>
            @forelse ($top as $t)
                <tr><td>{{ $t->product_name }}</td><td class="r muted">{{ rtrim(rtrim(number_format($t->qty, 2, ',', '.'), '0'), ',') }} {{ $t->unit }}</td><td class="r"><b>{{ $rp($t->omzet) }}</b></td></tr>
            @empty
                <tr><td class="muted">Belum ada penjualan bulan ini.</td></tr>
            @endforelse
        </table>
    </div>
    @endif

    @if ($ops)
    <div class="card">
        <h2>⚠️ Stok menipis</h2>
        <table>
            @forelse ($lowStock as $p)
                <tr><td>{{ $p->name }}</td><td class="r"><span class="badge low">{{ rtrim(rtrim(number_format($p->stock, 2, ',', '.'), '0'), ',') }} {{ $p->unit }}</span></td></tr>
            @empty
                <tr><td class="muted">Semua stok aman.</td></tr>
            @endforelse
        </table>
    </div>

    <div class="card">
        <h2>Transaksi terakhir</h2>
        <table>
            @forelse ($recent as $s)
                <tr>
                    <td><a href="{{ route('sales.show', $s) }}"><b>{{ $s->invoice_no }}</b></a><br><small class="muted">{{ $s->customer_name ?: 'Umum' }} · {{ $s->created_at->format('d/m H:i') }}</small></td>
                    <td class="r">{{ $rp($s->total) }}</td>
                </tr>
            @empty
                <tr><td class="muted">Belum ada transaksi. <a href="{{ route('pos') }}"><u>Buka kasir</u></a></td></tr>
            @endforelse
        </table>
    </div>
    @endif
</div>
@endsection
