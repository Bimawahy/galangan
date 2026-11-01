<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PosController extends Controller
{
    public function index(): View
    {
        $products = Product::where('active', true)->orderBy('category')->orderBy('name')
            ->get(['id', 'name', 'category', 'unit', 'price', 'stock']);

        return view('pos.index', compact('products'));
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'customer_name' => ['nullable', 'string', 'max:120'],
            'vehicle_plate' => ['nullable', 'string', 'max:20'],
            'payment_method' => ['required', Rule::in(['tunai', 'transfer', 'tempo'])],
            'paid' => ['nullable', 'integer', 'min:0'],
            'note' => ['nullable', 'string', 'max:500'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.id' => ['required', 'integer', 'distinct'],
            'items.*.qty' => ['required', 'numeric', 'gt:0', 'max:100000'],
        ], [
            'items.required' => 'Keranjang masih kosong.',
            'items.min' => 'Keranjang masih kosong.',
        ]);

        $sale = DB::transaction(function () use ($data, $request) {
            $total = 0;
            $lines = [];

            foreach ($data['items'] as $row) {
                // Harga selalu diambil dari database, bukan dari klien.
                $product = Product::where('active', true)->lockForUpdate()->find($row['id']);
                if (! $product) {
                    throw ValidationException::withMessages(['items' => 'Ada produk yang tidak tersedia.']);
                }
                $qty = round((float) $row['qty'], 2);
                if ($qty > $product->stock) {
                    throw ValidationException::withMessages([
                        'items' => "Stok {$product->name} tidak cukup (sisa {$product->stock} {$product->unit}).",
                    ]);
                }
                $subtotal = (int) round($qty * $product->price);
                $total += $subtotal;
                $lines[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'unit' => $product->unit,
                    'qty' => $qty,
                    'price' => $product->price,
                    'subtotal' => $subtotal,
                ];
                $product->decrement('stock', $qty);
            }

            $method = $data['payment_method'];
            $paid = $method === 'tempo' ? (int) ($data['paid'] ?? 0) : $total;
            if ($method === 'tempo' && $paid > $total) {
                throw ValidationException::withMessages(['paid' => 'Uang muka melebihi total.']);
            }
            if ($method === 'tunai' && isset($data['paid']) && $data['paid'] < $total) {
                throw ValidationException::withMessages(['paid' => 'Uang tunai kurang dari total.']);
            }

            $sale = Sale::create([
                'invoice_no' => 'TMP-'.uniqid(),
                'user_id' => $request->user()->id,
                'customer_name' => $data['customer_name'] ?? null,
                'vehicle_plate' => isset($data['vehicle_plate']) ? strtoupper($data['vehicle_plate']) : null,
                'payment_method' => $method,
                'total' => $total,
                'paid' => $paid,
                'note' => $data['note'] ?? null,
            ]);
            $sale->update(['invoice_no' => 'INV-'.now()->format('Ymd').'-'.str_pad($sale->id, 4, '0', STR_PAD_LEFT)]);
            $sale->items()->createMany($lines);

            return $sale;
        });

        return response()->json(['redirect' => route('sales.show', $sale)]);
    }
}
