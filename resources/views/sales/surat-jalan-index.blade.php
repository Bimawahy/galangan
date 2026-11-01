@extends('layouts.admin')
@section('title', 'Surat Jalan')
@section('content')
<h1>Surat Jalan</h1>
<div class="card">
    <form method="GET" class="row" style="margin-bottom:14px">
        <div><label>Cari (invoice / pelanggan / plat)</label><input name="q" value="{{ request('q') }}"></div>
        <button class="btn ghost">Cari</button>
    </form>
    <table>
        <tr><th>Invoice</th><th>Waktu</th><th>Pelanggan</th><th>Plat</th><th class="r"></th></tr>
        @forelse ($sales as $s)
            <tr>
                <td><b>{{ $s->invoice_no }}</b></td>
                <td>{{ $s->created_at->format('d/m/Y H:i') }}</td>
                <td>{{ $s->customer_name ?: 'Umum' }}</td>
                <td>{{ $s->vehicle_plate ?: '-' }}</td>
                <td class="r"><a class="btn sm" href="{{ route('delivery.show', $s) }}">Buat / Cetak</a></td>
            </tr>
        @empty
            <tr><td colspan="5" class="muted">Belum ada transaksi.</td></tr>
        @endforelse
    </table>
    <div class="pager">{{ $sales->links() }}</div>
</div>
@endsection
