<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $products = Product::query()
            ->when($request->filled('q'), fn ($q) => $q->where('name', 'like', '%'.$request->q.'%'))
            ->when($request->filled('category'), fn ($q) => $q->where('category', $request->category))
            ->orderBy('category')->orderBy('name')
            ->get();

        return view('products.index', compact('products'));
    }

    public function create(): View
    {
        return view('products.form', ['product' => new Product(['unit' => 'm3', 'min_stock' => 10, 'active' => true])]);
    }

    public function store(Request $request): RedirectResponse
    {
        Product::create($this->validated($request));

        return redirect()->route('products.index')->with('ok', 'Produk ditambahkan.');
    }

    public function edit(Product $product): View
    {
        return view('products.form', compact('product'));
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $product->update($this->validated($request));

        return redirect()->route('products.index')->with('ok', 'Produk diperbarui.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        // Riwayat penjualan tetap aman: nama & harga disalin ke sale_items.
        $product->delete();

        return redirect()->route('products.index')->with('ok', 'Produk dihapus.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'category' => ['required', Rule::in(Product::CATEGORIES)],
            'unit' => ['required', Rule::in(Product::UNITS)],
            'price' => ['required', 'integer', 'min:0'],
            'stock' => ['required', 'numeric', 'min:0'],
            'min_stock' => ['required', 'numeric', 'min:0'],
        ]);
        $data['active'] = $request->boolean('active');

        return $data;
    }
}
