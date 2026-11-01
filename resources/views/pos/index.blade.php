@extends('layouts.admin')
@section('title', 'Kasir')
@push('head')
<style>
    .pos { display: grid; grid-template-columns: 1fr 380px; gap: 20px; align-items: start; }
    @media (max-width: 1100px) { .pos { grid-template-columns: 1fr; } }
    .tiles { display: grid; grid-template-columns: repeat(auto-fill, minmax(165px, 1fr)); gap: 12px; }
    .tile { background: var(--bg); border: 1px solid var(--border); border-radius: 12px; padding: 14px; cursor: pointer; text-align: left; font: inherit; color: var(--text); }
    .tile:hover { border-color: var(--accent); transform: translateY(-1px); }
    .tile[disabled] { opacity: .45; cursor: not-allowed; }
    .tile b { display: block; margin-bottom: 4px; }
    .tile small { color: var(--muted); display: block; }
    .chips { display: flex; gap: 8px; margin-bottom: 14px; flex-wrap: wrap; }
    .chip { padding: 6px 14px; border-radius: 99px; border: 1px solid var(--border); background: transparent; font: inherit; color: var(--text); cursor: pointer; }
    .chip.on { background: var(--accent); color: #fff; border-color: var(--accent); }
    .line { display: grid; grid-template-columns: 1fr 90px 28px; gap: 8px; align-items: center; padding: 8px 0; border-bottom: 1px solid var(--border); }
    .line input { padding: 6px 8px; }
    .x { background: none; border: 0; color: var(--danger); cursor: pointer; font-size: 18px; }
    .total { display: flex; justify-content: space-between; font-size: 22px; font-weight: 700; margin: 14px 0; }
</style>
@endpush
@section('content')
<h1>Kasir (POS)</h1>
<div class="pos" x-data>
    <div class="card">
        <div class="row" style="margin-bottom:12px"><div><input id="search" placeholder="Cari produk…"></div></div>
        <div class="chips" id="chips"></div>
        <div class="tiles" id="tiles"></div>
    </div>

    <div class="card" style="position:sticky;top:20px">
        <h2>Keranjang</h2>
        <div id="cart"></div>
        <div class="total"><span>Total</span><span id="total">Rp 0</span></div>

        <div class="field"><label>Nama pelanggan</label><input id="customer" placeholder="Umum"></div>
        <div class="row">
            <div class="field"><label>Plat kendaraan</label><input id="plate" placeholder="L 1234 AB"></div>
            <div class="field"><label>Pembayaran</label>
                <select id="method"><option value="tunai">Tunai</option><option value="transfer">Transfer</option><option value="tempo">Tempo (hutang)</option></select>
            </div>
        </div>
        <div class="field" id="paidBox"><label id="paidLabel">Uang diterima (Rp)</label><input id="paid" type="number" min="0"><div class="muted" id="change" style="margin-top:4px"></div></div>
        <div class="field"><label>Catatan</label><input id="note" placeholder="Alamat kirim, dll."></div>
        <div class="err" id="error"></div>
        <button class="btn" id="pay" style="width:100%;padding:13px">Proses Transaksi</button>
    </div>
</div>

<script>
const PRODUCTS = @json($products);
const rp = n => 'Rp ' + Math.round(n).toLocaleString('id-ID');
const cart = {}; // id -> qty
let cat = '';

const $ = id => document.getElementById(id);

function renderTiles() {
    const q = $('search').value.toLowerCase();
    const cats = [...new Set(PRODUCTS.map(p => p.category))];
    $('chips').innerHTML = ['', ...cats].map(c =>
        `<button class="chip ${c === cat ? 'on' : ''}" data-cat="${c}">${c || 'Semua'}</button>`).join('');
    $('tiles').innerHTML = PRODUCTS
        .filter(p => (!cat || p.category === cat) && p.name.toLowerCase().includes(q))
        .map(p => `<button class="tile" data-id="${p.id}" ${p.stock <= 0 ? 'disabled' : ''}>
            <b></b><small>${rp(p.price)} / ${p.unit}</small><small>Stok: ${p.stock} ${p.unit}</small></button>`).join('');
    // nama diisi lewat textContent agar aman dari HTML injection
    document.querySelectorAll('#tiles .tile').forEach(t => {
        t.querySelector('b').textContent = PRODUCTS.find(p => p.id == t.dataset.id).name;
    });
}

function total() {
    return Object.entries(cart).reduce((s, [id, q]) => s + Math.round(q * PRODUCTS.find(p => p.id == id).price), 0);
}

function renderCart() {
    const ids = Object.keys(cart);
    $('cart').innerHTML = ids.length ? ids.map(id => `<div class="line" data-id="${id}">
        <div><b></b><br><small class="muted"></small></div>
        <input type="number" min="0.01" step="0.01" value="${cart[id]}">
        <button class="x" title="Hapus">×</button></div>`).join('') : '<p class="muted">Pilih produk di sebelah kiri.</p>';
    document.querySelectorAll('#cart .line').forEach(l => {
        const p = PRODUCTS.find(p => p.id == l.dataset.id);
        l.querySelector('b').textContent = p.name;
        l.querySelector('small').textContent = `${rp(p.price)} / ${p.unit}`;
    });
    $('total').textContent = rp(total());
    updatePaid();
}

function updatePaid() {
    const m = $('method').value, t = total(), paid = parseInt($('paid').value || 0);
    $('paidBox').style.display = m === 'transfer' ? 'none' : '';
    $('paidLabel').textContent = m === 'tempo' ? 'Uang muka (Rp)' : 'Uang diterima (Rp)';
    $('change').textContent = m === 'tunai' && paid ? (paid >= t ? 'Kembalian: ' + rp(paid - t) : 'Kurang: ' + rp(t - paid)) :
        m === 'tempo' ? 'Sisa piutang: ' + rp(Math.max(0, t - paid)) : '';
}

$('chips').onclick = e => { if (e.target.dataset.cat !== undefined) { cat = e.target.dataset.cat; renderTiles(); } };
$('search').oninput = renderTiles;
$('tiles').onclick = e => {
    const t = e.target.closest('.tile'); if (!t || t.disabled) return;
    const p = PRODUCTS.find(p => p.id == t.dataset.id);
    cart[p.id] = Math.min(p.stock, +((cart[p.id] || 0) + 1).toFixed(2));
    renderCart();
};
$('cart').oninput = e => {
    const id = e.target.closest('.line').dataset.id, p = PRODUCTS.find(p => p.id == id);
    cart[id] = Math.min(p.stock, parseFloat(e.target.value) || 0);
    $('total').textContent = rp(total()); updatePaid();
};
$('cart').onclick = e => { if (e.target.classList.contains('x')) { delete cart[e.target.closest('.line').dataset.id]; renderCart(); } };
$('method').onchange = updatePaid;
$('paid').oninput = updatePaid;

$('pay').onclick = async () => {
    $('error').textContent = '';
    const items = Object.entries(cart).filter(([, q]) => q > 0).map(([id, qty]) => ({ id: +id, qty }));
    const m = $('method').value;
    const body = {
        customer_name: $('customer').value, vehicle_plate: $('plate').value, payment_method: m,
        note: $('note').value, items,
    };
    if (m !== 'transfer' && $('paid').value !== '') body.paid = parseInt($('paid').value);
    $('pay').disabled = true;
    try {
        const res = await fetch('{{ route('pos.store') }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json',
                       'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content },
            body: JSON.stringify(body),
        });
        const data = await res.json();
        if (res.ok) { location.href = data.redirect; return; }
        $('error').textContent = Object.values(data.errors || {}).flat()[0] || data.message || 'Gagal memproses.';
    } catch { $('error').textContent = 'Koneksi bermasalah, coba lagi.'; }
    $('pay').disabled = false;
};

renderTiles(); renderCart();
</script>
@endsection
