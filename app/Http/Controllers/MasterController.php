<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class MasterController extends Controller
{
    private const ROLES = ['admin', 'owner', 'master'];

    public function index(): View
    {
        $this->onlyMaster();

        $checks = [];
        $add = function (string $name, callable $fn) use (&$checks) {
            try {
                [$ok, $info] = $fn();
            } catch (\Throwable $e) {
                [$ok, $info] = [false, $e->getMessage()];
            }
            $checks[] = compact('name', 'ok', 'info');
        };

        $add('Koneksi database', fn () => [(bool) DB::connection()->getPdo(), config('database.default')]);
        $add('Tabel utama', function () {
            $missing = collect(['users', 'products', 'sales', 'sale_items'])->reject(fn ($t) => Schema::hasTable($t));

            return [$missing->isEmpty(), $missing->isEmpty() ? 'users, products, sales, sale_items ada' : 'Hilang: '.$missing->implode(', ')];
        });
        $add('Migration', function () {
            $files = collect(glob(database_path('migrations/*.php')))->map(fn ($f) => basename($f, '.php'));
            $pending = $files->diff(DB::table('migrations')->pluck('migration'));

            return [$pending->isEmpty(), $pending->isEmpty() ? $files->count().' migration terpasang' : 'Belum jalan: '.$pending->implode(', ')];
        });
        $add('Folder storage bisa ditulis', fn () => [is_writable(storage_path('logs')), storage_path('logs')]);
        foreach (self::ROLES as $role) {
            $add("User role {$role}", function () use ($role) {
                $n = DB::table('users')->where('role', $role)->count();

                return [$n > 0, $n.' user'];
            });
        }
        $add('Data produk aktif', fn () => [DB::table('products')->where('active', true)->exists(), DB::table('products')->where('active', true)->count().' produk']);

        // Halaman GET apa saja yang boleh dibuka tiap role, dibaca dari definisi route (middleware role:...).
        $pages = collect(Route::getRoutes()->getRoutes())
            ->filter(fn ($r) => in_array('GET', $r->methods()) && $r->getName() && ! str_contains($r->uri(), '{'))
            ->map(function ($r) {
                $mw = collect($r->gatherMiddleware())->first(fn ($m) => is_string($m) && str_starts_with($m, 'role:'));

                return $mw ? ['name' => $r->getName(), 'uri' => '/'.$r->uri(), 'roles' => explode(',', substr($mw, 5))] : null;
            })->filter()->values();

        return view('master.index', ['checks' => $checks, 'pages' => $pages, 'roles' => self::ROLES]);
    }

    public function viewAs(string $role): RedirectResponse
    {
        $this->onlyMaster();

        $role === 'master' ? session()->forget('view_as') : session(['view_as' => $role]);

        return redirect()->route($role === 'master' ? 'master' : 'dashboard');
    }

    private function onlyMaster(): void
    {
        abort_unless(auth()->user()->role === 'master', 403, 'Hanya master yang boleh mengakses halaman ini.');
    }
}
