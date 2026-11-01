@extends('layouts.admin')
@section('title', 'Surat Jalan '.$sale->invoice_no)
@push('head')
<style>
    /* Printer dot matrix (LX-310): font monospace, hitam putih, tanpa warna/latar. Pilih printer & kertas 9.5 x 11 inch di dialog cetak. */
    @page { size: 9.5in 11in; margin: 8mm 10mm; }
    .sj { font-family: 'Courier New', Courier, monospace; color: #000; font-size: 13px; line-height: 1.35; max-width: 760px; }
    .sj h2 { font-size: 20px; letter-spacing: .15em; text-align: center; margin: 0 0 2px; }
    .sj .sub { text-align: center; margin-bottom: 10px; }
    .sj .info { width: 100%; margin-bottom: 8px; }
    .sj .info td { border: 0; padding: 1px 4px; vertical-align: top; }
    .sj .items { width: 100%; border-collapse: collapse; }
    .sj .items th, .sj .items td { border: 1px solid #000; padding: 3px 6px; color: #000; font-size: 13px; text-transform: none; letter-spacing: 0; }
    .sj .sign { width: 100%; margin-top: 22px; text-align: center; }
    .sj .sign td { border: 0; width: 33%; padding-top: 0; }
    .sj .sign .gap { height: 58px; }
    @media print { .sj { max-width: none; } }
</style>
@endpush
@section('content')
@php
    $alamat = request('alamat'); $sopir = request('sopir');
    $qty = fn ($n) => rtrim(rtrim(number_format($n, 2, ',', '.'), '0'), ',');
@endphp

<div class="noprint">
    <h1>Surat Jalan</h1>
    <form method="GET" class="card row">
        <div><label>Alamat tujuan</label><input name="alamat" value="{{ $alamat }}"></div>
        <div><label>Nama sopir</label><input name="sopir" value="{{ $sopir }}"></div>
        <button class="btn ghost">Terapkan</button>
        <button class="btn" type="button" onclick="window.print()">🖨️ Cetak</button>
        <a class="btn ghost" href="{{ route('delivery.index') }}">← Daftar</a>
    </form>
    <p class="muted">Untuk LX-310: pilih printer Epson LX-310 dan ukuran kertas 9.5 x 11 inci (continuous form) di dialog cetak.</p>
</div>

<div class="card sj">
    <h2>SURAT JALAN</h2>
    <div class="sub">Galian Pasir</div>
    <table class="info">
        <tr><td>No. Surat Jalan</td><td>: SJ-{{ $sale->invoice_no }}</td><td>Tanggal</td><td>: {{ $sale->created_at->format('d/m/Y') }}</td></tr>
        <tr><td>Kepada</td><td>: {{ $sale->customer_name ?: 'Umum' }}</td><td>No. Kendaraan</td><td>: {{ $sale->vehicle_plate ?: '-' }}</td></tr>
        <tr><td>Alamat tujuan</td><td>: {{ $alamat ?: '-' }}</td><td>Sopir</td><td>: {{ $sopir ?: '-' }}</td></tr>
    </table>
    <table class="items">
        <tr><th style="width:40px">No</th><th>Nama Barang</th><th style="width:90px;text-align:right">Qty</th><th style="width:80px">Satuan</th></tr>
        @foreach ($sale->items as $i)
            <tr><td>{{ $loop->iteration }}</td><td>{{ $i->product_name }}</td><td style="text-align:right">{{ $qty($i->qty) }}</td><td>{{ $i->unit }}</td></tr>
        @endforeach
    </table>
    @if ($sale->note)<p>Catatan: {{ $sale->note }}</p>@endif
    <table class="sign">
        <tr><td>Pengirim</td><td>Sopir</td><td>Penerima</td></tr>
        <tr><td class="gap"></td><td></td><td></td></tr>
        <tr><td>(____________)</td><td>(____________)</td><td>(____________)</td></tr>
    </table>
</div>
@endsection
