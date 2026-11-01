@extends('layouts.admin')
@section('title', 'Produk & Stok')
@section('content')
<h1>Produk &amp; Stok</h1>
<div class="card">
    <form method="GET" class="row" style="margin-bottom:14px">
        <div><label>Cari</label><input name="q" value="{{ request('q') }}" placeholder="Nama produk"></div>
        <div><label>Kategori</label>
            <select name="category">
                <option value="">Semua</option>
                @foreach (\App\Models\Product::CATEGORIES as $c)
                    <option @selected(request('category') === $c)>{{ $c }}</option>
                @endforeach
            </select>
        </div>
        <button class="btn ghost">Filter</button>
        <a class="btn" href="{{ route('products.create') }}">+ Produk</a>
    </form>
    <table>
        <tr><th>Nama</th><th>Kategori</th><th class="r">Harga</th><th class="r">Stok</th><th>Status</th><th></th></tr>
        @forelse ($products as $p)
            <tr>
                <td><b>{{ $p->name }}</b></td>
                <td>{{ $p->category }}</td>
                <td class="r">Rp {{ number_format($p->price, 0, ',', '.') }} / {{ $p->unit }}</td>
                <td class="r"><span class="badge {{ $p->isLow() ? 'low' : 'ok' }}">{{ rtrim(rtrim(number_format($p->stock, 2, ',', '.'), '0'), ',') }} {{ $p->unit }}</span></td>
                <td>{{ $p->active ? 'Aktif' : 'Nonaktif' }}</td>
                <td class="r" style="white-space:nowrap">
                    <a class="btn ghost sm" href="{{ route('products.edit', $p) }}">Edit</a>
                    <form method="POST" action="{{ route('products.destroy', $p) }}" style="display:inline" onsubmit="return confirm('Hapus {{ e($p->name) }}?')">
                        @csrf @method('DELETE')
                        <button class="btn danger sm">Hapus</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="6" class="muted">Tidak ada produk.</td></tr>
        @endforelse
    </table>
</div>
@endsection
