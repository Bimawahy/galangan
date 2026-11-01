<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $today = Carbon::today();
        $month = Carbon::now()->startOfMonth();

        $stats = [
            'omzet_hari' => Sale::where('created_at', '>=', $today)->sum('total'),
            'trx_hari' => Sale::where('created_at', '>=', $today)->count(),
            'omzet_bulan' => Sale::where('created_at', '>=', $month)->sum('total'),
            'piutang' => Sale::whereColumn('paid', '<', 'total')->get()->sum(fn ($s) => $s->total - $s->paid),
        ];

        $chart = collect(range(6, 0))->map(function ($i) {
            $d = Carbon::today()->subDays($i);
            return [
                'label' => $d->format('d/m'),
                'total' => (int) Sale::whereBetween('created_at', [$d, $d->copy()->endOfDay()])->sum('total'),
            ];
        });

        $top = SaleItem::selectRaw('product_name, unit, SUM(qty) as qty, SUM(subtotal) as omzet')
            ->where('created_at', '>=', $month)
            ->groupBy('product_name', 'unit')
            ->orderByDesc('omzet')
            ->limit(5)
            ->get();

        return view('dashboard', [
            'stats' => $stats,
            'chart' => $chart,
            'top' => $top,
            'lowStock' => Product::where('active', true)->whereColumn('stock', '<=', 'min_stock')->orderBy('stock')->get(),
            'recent' => Sale::with('user')->latest()->limit(6)->get(),
        ]);
    }
}
