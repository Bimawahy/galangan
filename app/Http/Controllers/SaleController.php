<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SaleController extends Controller
{
    public function index(Request $request): View
    {
        $sales = Sale::with('user')->withCount('items')
            ->when($request->filled('from'), fn ($q) => $q->whereDate('created_at', '>=', $request->from))
            ->when($request->filled('to'), fn ($q) => $q->whereDate('created_at', '<=', $request->to))
            ->when($request->filled('q'), fn ($q) => $q->where(fn ($w) => $w
                ->where('invoice_no', 'like', '%'.$request->q.'%')
                ->orWhere('customer_name', 'like', '%'.$request->q.'%')
                ->orWhere('vehicle_plate', 'like', '%'.$request->q.'%')))
            ->latest()->paginate(15)->withQueryString();

        return view('sales.index', compact('sales'));
    }

    public function show(Sale $sale): View
    {
        $sale->load('items', 'user');

        return view('sales.show', compact('sale'));
    }

    public function settle(Sale $sale): RedirectResponse
    {
        $sale->update(['paid' => $sale->total]);

        return back()->with('ok', 'Piutang dilunasi.');
    }
}
