@extends('layouts.admin')
@section('title', $sale->invoice_no)
@section('content')
@php $rp = fn ($n) => 'Rp '.number_format($n, 0, ',', '.'); @endphp
<div class="noprint row" style="margin-bottom:16px;justify-content:flex-start">
    <a class="btn ghost" href="{{ route('sales.index') }}" style="flex:0 0 auto">← Riwayat</a>
    <button class="btn" onclick="window.print()" style="flex:0 0 auto">🖨️ Cetak Struk</button>
    <a class="btn ghost" href="{{ route('pos') }}" style="flex:0 0 auto">Transaksi baru</a>
</div>

<div class="card" style="max-width:640px">
    <h2 style="font-size:20px">Galian Pasir — {{ $sale->invoice_no }}</h2>
    <p class="muted" style="margin-top:0">
        {{ $sale->created_at->format('d/m/Y H:i') }} · Kasir: {{ $sale->user->name }}<br>
        Pelanggan: {{ $sale->customer_name ?: 'Umum' }}@if($sale->vehicle_plate) · Plat: {{ $sale->vehicle_plate }}@endif
        @if($sale->note)<br>Catatan: {{ $sale->note }}@endif
    </p>
    <table>
        <tr><th>Barang</th><th class="r">Qty</th><th class="r">Harga</th><th class="r">Subtotal</th></tr>
        @foreach ($sale->items as $i)
            <tr>
                <td>{{ $i->product_name }}</td>
                <td class="r">{{ rtrim(rtrim(number_format($i->qty, 2, ',', '.'), '0'), ',') }} {{ $i->unit }}</td>
                <td class="r">{{ $rp($i->price) }}</td>
                <td class="r">{{ $rp($i->subtotal) }}</td>
            </tr>
        @endforeach
        <tr><td colspan="3" class="r"><b>Total</b></td><td class="r"><b>{{ $rp($sale->total) }}</b></td></tr>
        <tr><td colspan="3" class="r muted">Dibayar ({{ ucfirst($sale->payment_method) }})</td><td class="r">{{ $rp($sale->paid) }}</td></tr>
        @if ($sale->paid < $sale->total)
            <tr><td colspan="3" class="r" style="color:var(--danger)"><b>Sisa piutang</b></td><td class="r" style="color:var(--danger)"><b>{{ $rp($sale->total - $sale->paid) }}</b></td></tr>
        @endif
    </table>

    @if ($sale->paid < $sale->total)
        <form class="noprint" method="POST" action="{{ route('sales.settle', $sale) }}" style="margin-top:16px" onsubmit="return confirm('Tandai transaksi ini lunas?')">
            @csrf <button class="btn">Tandai Lunas</button>
        </form>
    @endif
</div>
@endsection
