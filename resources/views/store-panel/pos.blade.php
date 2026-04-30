@extends('layouts.store')

@section('title', 'Caja rápida')

@push('styles')
<style>
    .pos-layout { display: grid; gap: 1rem; }
    @media (min-width: 900px) {
        .pos-layout { grid-template-columns: 1fr 340px; align-items: start; }
    }
    .search-wrap { position: relative; }
    .search-wrap input { font-size: 1.15rem; padding: 0.85rem 1rem; }
    .results {
        position: absolute; left: 0; right: 0; top: 100%; margin-top: 4px;
        background: var(--surface); border: 1px solid var(--border); border-radius: 8px;
        max-height: 280px; overflow-y: auto; z-index: 20; display: none; box-shadow: 0 12px 40px rgba(0,0,0,0.45);
    }
    .results.open { display: block; }
    .result-row {
        padding: 0.65rem 1rem; cursor: pointer; border-bottom: 1px solid var(--border);
        display: flex; justify-content: space-between; gap: 0.5rem; align-items: center;
    }
    .result-row:hover { background: var(--surface2); }
    .result-row:last-child { border-bottom: none; }
    .cart-lines { max-height: 360px; overflow-y: auto; }
    .line {
        display: grid; grid-template-columns: 1fr auto auto; gap: 0.5rem; align-items: center;
        padding: 0.5rem 0; border-bottom: 1px solid var(--border); font-size: 0.9rem;
    }
    .line input.qty { width: 4rem; padding: 0.35rem; text-align: center; }
    .line input.price { width: 5.5rem; padding: 0.35rem; }
    .totals { font-size: 1.25rem; font-weight: 700; margin-top: 0.75rem; color: var(--accent); }
    .hint { font-size: 0.8rem; color: var(--muted); margin-top: 0.75rem; line-height: 1.4; }
</style>
@endpush

@section('content')
    <h1>Caja rápida</h1>
    <p class="muted">Escribí código de barras, código interno o parte del nombre y presioná Enter. El lector USB suele pegar el código y enviar Enter solo.</p>

    @if(!$ctx['branch_id'])
        <div class="card" style="border-color:var(--warn);">
            <strong style="color:var(--warn);">Sin sucursal</strong>
            <p class="muted" style="margin:0.5rem 0 0;">Configurá una sucursal por defecto en tu usuario o creá sucursales en el administrador.</p>
        </div>
    @else
        <div id="pos-root"
             data-search-url="{{ route('panel.api.products') }}"
             data-sale-url="{{ route('panel.api.sales') }}"
             data-has-branch="1"
        >
            <div class="pos-layout">
                <div class="card search-wrap">
                    <label for="pos-q" class="muted" style="display:block;margin-bottom:0.35rem;">Buscar producto</label>
                    <input type="text" id="pos-q" autocomplete="off" placeholder="EAN, código o nombre…" autofocus>
                    <div id="pos-results" class="results" role="listbox"></div>
                    <p class="hint">Los precios salen de la lista por defecto (si existe). Podés editar el precio unitario en cada línea antes de cobrar.</p>
                </div>
                <div class="card">
                    <h2 style="margin-top:0;">Ticket actual</h2>
                    <div id="pos-cart" class="cart-lines"></div>
                    <div class="totals" id="pos-total">$ 0,00</div>
                    <div style="margin-top:1rem;display:flex;gap:0.5rem;flex-wrap:wrap;">
                        <button type="button" class="btn btn-ghost" id="pos-clear">Vaciar</button>
                        <button type="button" class="btn btn-primary" id="pos-pay" disabled>Cobrar (efectivo)</button>
                    </div>
                    <div id="pos-flash" class="flash" role="status"></div>
                </div>
            </div>
        </div>
    @endif
@endsection

@push('scripts')
@if($ctx['branch_id'])
<script>
(function () {
    const root = document.getElementById('pos-root');
    const q = document.getElementById('pos-q');
    const results = document.getElementById('pos-results');
    const cartEl = document.getElementById('pos-cart');
    const totalEl = document.getElementById('pos-total');
    const btnPay = document.getElementById('pos-pay');
    const btnClear = document.getElementById('pos-clear');
    const flash = document.getElementById('pos-flash');
    const searchUrl = root.dataset.searchUrl;
    const saleUrl = root.dataset.saleUrl;
    const csrf = document.querySelector('meta[name="csrf-token"]').content;

    /** @type {{id:number,name:string,unit_price:number,tax_rate:number,quantity:number}[]} */
    let cart = [];

    function money(n) {
        return new Intl.NumberFormat('es-AR', { style: 'currency', currency: 'ARS', minimumFractionDigits: 2 }).format(n);
    }

    function cartTotal() {
        return cart.reduce((s, l) => s + (l.quantity * l.unit_price), 0);
    }

    function renderCart() {
        cartEl.innerHTML = '';
        cart.forEach((line, idx) => {
            const row = document.createElement('div');
            row.className = 'line';
            row.innerHTML = `
                <span>${escapeHtml(line.name)}</span>
                <input class="qty" type="number" min="0.01" step="0.01" value="${line.quantity}" data-i="${idx}">
                <input class="price mono" type="number" min="0" step="0.01" value="${line.unit_price}" data-p="${idx}">
            `;
            cartEl.appendChild(row);
        });
        totalEl.textContent = money(cartTotal());
        btnPay.disabled = cart.length === 0;
    }

    cartEl.addEventListener('change', (e) => {
        const t = e.target;
        if (t.matches('input.qty')) {
            const i = +t.dataset.i;
            if (cart[i]) {
                cart[i].quantity = Math.max(0.01, parseFloat(t.value) || 0.01);
                renderCart();
            }
        }
        if (t.matches('input.price')) {
            const i = +t.dataset.p;
            if (cart[i]) {
                cart[i].unit_price = Math.max(0, parseFloat(t.value) || 0);
                renderCart();
            }
        }
    });

    function escapeHtml(s) {
        const d = document.createElement('div');
        d.textContent = s;
        return d.innerHTML;
    }

    function showFlash(msg, ok) {
        flash.className = 'flash ' + (ok ? 'ok' : 'err');
        flash.textContent = msg;
        if (ok) setTimeout(() => { flash.className = 'flash'; flash.textContent = ''; }, 5000);
    }

    function addToCart(p) {
        const ex = cart.find(l => l.id === p.id);
        if (ex) {
            ex.quantity = Math.round((ex.quantity + 1) * 100) / 100;
        } else {
            cart.push({
                id: p.id,
                name: p.name,
                unit_price: p.unit_price > 0 ? p.unit_price : 0,
                tax_rate: p.tax_rate,
                quantity: 1,
            });
        }
        renderCart();
    }

    let searchTimer = null;
    async function runSearch() {
        const term = q.value.trim();
        if (term.length < 1) {
            results.classList.remove('open');
            results.innerHTML = '';
            return;
        }
        const r = await fetch(searchUrl + '?q=' + encodeURIComponent(term), { headers: { 'Accept': 'application/json' } });
        const j = await r.json();
        results.innerHTML = '';
        if (!j.data || j.data.length === 0) {
            results.innerHTML = '<div class="result-row muted">Sin resultados</div>';
        } else {
            j.data.forEach(p => {
                const div = document.createElement('div');
                div.className = 'result-row';
                div.innerHTML = `<span>${escapeHtml(p.name)}</span><span class="mono muted">${money(p.unit_price)}</span>`;
                div.addEventListener('click', () => {
                    addToCart(p);
                    q.value = '';
                    results.classList.remove('open');
                    results.innerHTML = '';
                    q.focus();
                });
                results.appendChild(div);
            });
        }
        results.classList.add('open');
    }

    q.addEventListener('input', () => {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(runSearch, 200);
    });

    q.addEventListener('keydown', (e) => {
        if (e.key === 'Enter') {
            e.preventDefault();
            runSearch().then(() => {
                const first = results.querySelector('.result-row');
                if (first && !first.classList.contains('muted')) first.click();
            });
        }
    });

    document.addEventListener('click', (e) => {
        if (!results.contains(e.target) && e.target !== q) results.classList.remove('open');
    });

    btnClear.addEventListener('click', () => {
        cart = [];
        renderCart();
        flash.className = 'flash';
    });

    btnPay.addEventListener('click', async () => {
        btnPay.disabled = true;
        flash.className = 'flash';
        const lines = cart.map(l => ({
            product_id: l.id,
            quantity: l.quantity,
            unit_price: l.unit_price,
        }));
        try {
            const r = await fetch(saleUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                },
                body: JSON.stringify({ lines }),
            });
            const j = await r.json().catch(() => ({}));
            if (!r.ok) throw new Error(j.message || 'Error al guardar');
            showFlash('Venta ' + j.sale.sale_number + ' — ' + money(j.sale.grand_total), true);
            cart = [];
            renderCart();
        } catch (err) {
            showFlash(err.message || 'Error', false);
        }
        btnPay.disabled = cart.length === 0;
    });

    renderCart();
})();
</script>
@endif
@endpush
