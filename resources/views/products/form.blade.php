@extends('layouts.admin')
@section('title', $product->exists ? 'Edit Produk' : 'Tambah Produk')
@section('content')
<h1>{{ $product->exists ? 'Edit Produk' : 'Tambah Produk' }}</h1>
<div class="card" style="max-width:560px">
    <form method="POST" action="{{ $product->exists ? route('products.update', $product) : route('products.store') }}">
        @csrf
        @if ($product->exists) @method('PUT') @endif

        <div class="field"><label>Nama</label><input name="name" value="{{ old('name', $product->name) }}" required>@error('name')<div class="err">{{ $message }}</div>@enderror</div>
        <div class="row">
            <div class="field"><label>Kategori</label>
                <select name="category">@foreach (\App\Models\Product::CATEGORIES as $c)<option @selected(old('category', $product->category) === $c)>{{ $c }}</option>@endforeach</select>
            </div>
            <div class="field"><label>Satuan</label>
                <select name="unit">@foreach (\App\Models\Product::UNITS as $u)<option @selected(old('unit', $product->unit) === $u)>{{ $u }}</option>@endforeach</select>
            </div>
        </div>
        <div class="field"><label>Harga per satuan (Rp)</label><input type="number" name="price" min="0" value="{{ old('price', $product->price) }}" required>@error('price')<div class="err">{{ $message }}</div>@enderror</div>
        <div class="row">
            <div class="field"><label>Stok</label><input type="number" step="0.01" min="0" name="stock" value="{{ old('stock', $product->stock ?? 0) }}" required>@error('stock')<div class="err">{{ $message }}</div>@enderror</div>
            <div class="field"><label>Batas stok menipis</label><input type="number" step="0.01" min="0" name="min_stock" value="{{ old('min_stock', $product->min_stock) }}" required></div>
        </div>
        <div class="field"><label style="display:flex;gap:8px;align-items:center;color:var(--text)"><input type="checkbox" name="active" value="1" style="width:auto" @checked(old('active', $product->active))> Aktif (tampil di kasir)</label></div>
        <button class="btn">Simpan</button>
        <a class="btn ghost" href="{{ route('products.index') }}">Batal</a>
    </form>
</div>
@endsection
