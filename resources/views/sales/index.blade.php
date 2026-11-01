@extends('layouts.admin')
@section('title', 'Penjualan')
@section('content')
<h1>Riwayat Penjualan</h1>
<div class="card">
    <form method="GET" class="row" style="margin-bottom:14px">
        <div><label>Cari (invoice / pelanggan / plat)</label><input name="q" value="{{ request('q') }}"></div>
        <div><label>Dari</label><input type="date" name="from" value="{{ request('from') }}"></div>
        <div><label>Sampai</label><input type="date" name="to" value="{{ request('to') }}"></div>
        <button class="btn ghost">Filter</button>
    </form>
    <table>
        <tr><th>Invoice</th><th>Waktu</th><th>Pelanggan</th><th>Bayar</th><th class="r">Total</th><th>Status</th></tr>
        @forelse ($sales as $s)
            <tr>
                <td><a href="{{ route('sales.show', $s) }}"><b>{{ $s->invoice_no }}</b></a></td>
                <td>{{ $s->created_at->format('d/m/Y H:i') }}</td>
                <td>{{ $s->customer_name ?: 'Umum' }}@if($s->vehicle_plate) <small class="muted">· {{ $s->vehicle_plate }}</small>@endif</td>
                <td>{{ ucfirst($s->payment_method) }}</td>
                <td class="r">Rp {{ number_format($s->total, 0, ',', '.') }}</td>
                <td>@if($s->paid >= $s->total)<span class="badge ok">Lunas</span>@else<span class="badge low">Kurang Rp {{ number_format($s->total - $s->paid, 0, ',', '.') }}</span>@endif</td>
            </tr>
        @empty
            <tr><td colspan="6" class="muted">Belum ada transaksi.</td></tr>
        @endforelse
    </table>
    <div class="pager">{{ $sales->links() }}</div>
</div>
@endsection
