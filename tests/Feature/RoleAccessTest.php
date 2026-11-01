<?php

namespace Tests\Feature;

use App\Models\Sale;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    private function as(?string $role): static
    {
        return $this->actingAs(User::factory()->create(['username' => uniqid($role), 'role' => $role]));
    }

    public function test_akses_per_role(): void
    {
        // [role => [url => status yang diharapkan]]
        $matrix = [
            'admin' => ['/dashboard' => 200, '/kasir' => 200, '/produk' => 200, '/penjualan' => 200, '/surat-jalan' => 200, '/master' => 403],
            'owner' => ['/dashboard' => 200, '/kasir' => 403, '/produk' => 403, '/penjualan' => 200, '/surat-jalan' => 403, '/master' => 403],
            'master' => ['/dashboard' => 200, '/kasir' => 200, '/produk' => 200, '/penjualan' => 200, '/surat-jalan' => 200, '/master' => 200],
            'tanpa-role' => ['/dashboard' => 403, '/kasir' => 403, '/produk' => 403, '/penjualan' => 403, '/surat-jalan' => 403, '/master' => 403],
        ];

        foreach ($matrix as $role => $pages) {
            foreach ($pages as $url => $status) {
                $this->as($role === 'tanpa-role' ? null : $role)->get($url)->assertStatus($status, "$role -> $url");
            }
        }
    }

    public function test_tampilan_dashboard_beda_per_role(): void
    {
        $this->as('owner')->get('/dashboard')->assertSee('Omzet 7 hari terakhir')->assertDontSee('Stok menipis');
        $this->as('admin')->get('/dashboard')->assertSee('Stok menipis')->assertDontSee('Omzet 7 hari terakhir');
        $this->as('master')->get('/dashboard')->assertSee('Omzet 7 hari terakhir')->assertSee('Stok menipis');
    }

    public function test_surat_jalan_menampilkan_barang_tanpa_harga_dan_owner_ditolak(): void
    {
        $sale = Sale::create(['invoice_no' => 'T-9', 'user_id' => User::factory()->create()->id, 'customer_name' => 'Pak Budi', 'vehicle_plate' => 'L 1234 AB', 'total' => 500000, 'paid' => 0]);
        $sale->items()->create(['product_name' => 'Pasir Urug', 'unit' => 'm3', 'qty' => 6, 'price' => 250000, 'subtotal' => 1500000]);

        $this->as('admin')->get("/surat-jalan/{$sale->id}?alamat=Jl.+Mawar&sopir=Joko")
            ->assertOk()->assertSee('SJ-T-9')->assertSee('Pak Budi')->assertSee('L 1234 AB')
            ->assertSee('Pasir Urug')->assertSee('Jl. Mawar')->assertSee('Joko')->assertDontSee('250.000');
        $this->as('owner')->get("/surat-jalan/{$sale->id}")->assertForbidden();
    }

    public function test_owner_tidak_bisa_melunasi_dan_master_bisa_lihat_sebagai(): void
    {
        $sale = Sale::create(['invoice_no' => 'T-1', 'user_id' => User::factory()->create()->id, 'total' => 100, 'paid' => 0]);
        $this->as('owner')->post("/penjualan/{$sale->id}/lunas")->assertForbidden();
        $this->assertSame(0, $sale->fresh()->paid);

        $this->as('master')->post('/master/lihat-sebagai/owner')->assertRedirect('/dashboard');
        $this->get('/kasir')->assertForbidden();       // sedang tampil sebagai owner
        $this->get('/master')->assertOk();             // tetap bisa kembali ke master
        $this->post('/master/lihat-sebagai/master');
        $this->get('/kasir')->assertOk();
    }
}
