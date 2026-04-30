@extends('layouts.store')

@section('title', 'Gestión del negocio')

@push('styles')
<style>
    .admin-hero { margin-bottom: 1.25rem; }
    .grid-kpi { display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 0.85rem; }
    .grid-wide { display: grid; gap: 1rem; }
    @media (min-width: 960px) {
        .grid-wide { grid-template-columns: 1fr 1fr; align-items: start; }
    }
    .kpi .muted { font-size: 0.78rem; }
    .kpi strong { font-size: 1.35rem; }
    .pill { display: inline-block; padding: 0.2rem 0.55rem; border-radius: 999px; font-size: 0.75rem; font-weight: 600; }
    .pill-warn { background: rgba(245, 158, 11, 0.15); color: #fcd34d; border: 1px solid rgba(245, 158, 11, 0.35); }
    .pill-muted { background: var(--surface2); color: var(--muted); border: 1px solid var(--border); }
    .quick-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 0.65rem; margin-top: 0.75rem; }
    .quick-grid a {
        display: block; padding: 0.65rem 0.85rem; border-radius: 8px;
        background: var(--surface2); border: 1px solid var(--border); color: var(--text);
        font-size: 0.88rem; font-weight: 500;
    }
    .quick-grid a:hover { border-color: var(--accent); text-decoration: none; }
    h2.section { font-size: 1rem; color: var(--text); margin: 0 0 0.65rem; font-weight: 700; }
</style>
@endpush

@section('content')
    <div class="admin-hero">
        <h1>Gestión del negocio</h1>
        <p class="muted">Vista global para administradores: ventas, alertas de stock y accesos al administrador (Backpack).</p>
    </div>

    <h2 class="section">Ventas (completadas)</h2>
    <div class="grid-kpi" style="margin-bottom:1.25rem;">
        <div class="stat kpi">
            <span class="muted">Hoy — tickets</span>
            <strong>{{ $stats['ventas_hoy'] }}</strong>
        </div>
        <div class="stat kpi">
            <span class="muted">Hoy — total</span>
            <strong>$ {{ number_format((float) $stats['total_hoy'], 2, ',', '.') }}</strong>
        </div>
        <div class="stat kpi">
            <span class="muted">Semana — tickets</span>
            <strong>{{ $stats['ventas_semana'] }}</strong>
        </div>
        <div class="stat kpi">
            <span class="muted">Semana — total</span>
            <strong>$ {{ number_format((float) $stats['total_semana'], 2, ',', '.') }}</strong>
        </div>
        <div class="stat kpi">
            <span class="muted">Mes — tickets</span>
            <strong>{{ $stats['ventas_mes'] }}</strong>
        </div>
        <div class="stat kpi">
            <span class="muted">Mes — total</span>
            <strong>$ {{ number_format((float) $stats['total_mes'], 2, ',', '.') }}</strong>
        </div>
    </div>

    <h2 class="section">Catálogo y operación</h2>
    <div class="grid-kpi" style="margin-bottom:1.25rem;">
        <div class="stat kpi">
            <span class="muted">Sucursales activas</span>
            <strong>{{ $stats['branches'] }}</strong>
        </div>
        <div class="stat kpi">
            <span class="muted">Productos activos</span>
            <strong>{{ $stats['products_active'] }}</strong>
        </div>
        <div class="stat kpi">
            <span class="muted">Usuarios</span>
            <strong>{{ $stats['users'] }}</strong>
        </div>
        <div class="stat kpi">
            <span class="muted">Compras abiertas</span>
            <strong>{{ $stats['open_purchases'] }}</strong>
            <span class="muted" style="display:block;font-size:0.72rem;margin-top:0.25rem;">No recibidas / no canceladas</span>
        </div>
        <div class="stat kpi">
            <span class="muted">Ventas no completadas</span>
            <strong>{{ $stats['draft_sales'] }}</strong>
            <span class="muted" style="display:block;font-size:0.72rem;margin-top:0.25rem;">Borrador u otros estados</span>
        </div>
    </div>

    <div class="grid-wide" style="margin-bottom:1.25rem;">
        <div class="card">
            <h2 class="section">Facturación por sucursal (30 días)</h2>
            @if($byBranch->isEmpty())
                <p class="muted">Todavía no hay ventas completadas en este período.</p>
            @else
                <table>
                    <thead>
                        <tr>
                            <th>Sucursal</th>
                            <th>Ventas</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($byBranch as $row)
                            <tr>
                                <td>{{ $branchNames[$row->branch_id] ?? ('#'.$row->branch_id) }}</td>
                                <td class="mono">{{ $row->sale_count }}</td>
                                <td class="mono">$ {{ number_format((float) $row->revenue, 2, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
        <div class="card">
            <h2 class="section">Productos más vendidos (30 días)</h2>
            @if($topProducts->isEmpty())
                <p class="muted">Sin líneas de venta en el período.</p>
            @else
                <table>
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Cant.</th>
                            <th>Importe</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($topProducts as $row)
                            <tr>
                                <td>{{ $productNames[$row->product_id] ?? 'ID '.$row->product_id }}</td>
                                <td class="mono">{{ number_format((float) $row->qty_sold, 2, ',', '.') }}</td>
                                <td class="mono">$ {{ number_format((float) $row->revenue, 2, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>

    <div class="grid-wide" style="margin-bottom:1.25rem;">
        <div class="card">
            <h2 class="section">Stock bajo mínimo</h2>
            @if($lowStock->isEmpty())
                <p class="muted">Ningún producto activo con <code>min_stock</code> configurado queda por debajo del stock en lotes.</p>
            @else
                <table>
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Código</th>
                            <th>Stock</th>
                            <th>Mín.</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($lowStock as $p)
                            <tr>
                                <td>{{ $p->name }}</td>
                                <td class="mono">{{ $p->internal_code ?? '—' }}</td>
                                <td class="mono"><span class="pill pill-warn">{{ number_format((float) $p->stock_real, 2, ',', '.') }}</span></td>
                                <td class="mono">{{ number_format((float) $p->min_stock, 2, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
        <div class="card">
            <h2 class="section">Lotes que vencen (30 días)</h2>
            @if($expiringSoon->isEmpty())
                <p class="muted">No hay lotes con cantidad &gt; 0 venciendo en los próximos 30 días.</p>
            @else
                <table>
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Lote</th>
                            <th>Vence</th>
                            <th>Stock</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($expiringSoon as $b)
                            <tr>
                                <td>{{ $b->product?->name ?? '—' }}</td>
                                <td class="mono">{{ $b->lot_number ?? '—' }}</td>
                                <td class="mono">{{ $b->expires_on?->format('d/m/Y') }}</td>
                                <td class="mono">{{ number_format((float) $b->quantity_on_hand, 2, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>

    <div class="card" style="margin-bottom:1.25rem;">
        <h2 class="section">Últimas ventas completadas</h2>
        @if($recentSales->isEmpty())
            <p class="muted">No hay ventas completadas registradas.</p>
        @else
            <table>
                <thead>
                    <tr>
                        <th>Número</th>
                        <th>Sucursal</th>
                        <th>Usuario</th>
                        <th>Fecha</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentSales as $s)
                        <tr>
                            <td class="mono">{{ $s->sale_number }}</td>
                            <td>{{ $s->branch?->name ?? '—' }}</td>
                            <td>{{ $s->user?->name ?? '—' }}</td>
                            <td class="mono">{{ $s->completed_at?->format('d/m/Y H:i') }}</td>
                            <td class="mono">$ {{ number_format((float) $s->grand_total, 2, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    <div class="card">
        <h2 class="section">Accesos rápidos al administrador</h2>
        <p class="muted" style="margin-top:0;">Listados y formularios de Backpack.</p>
        <div class="quick-grid">
            <a href="{{ backpack_url('sales') }}">Ventas</a>
            <a href="{{ backpack_url('purchases') }}">Compras</a>
            <a href="{{ backpack_url('product-batches') }}">Lotes / stock</a>
            <a href="{{ backpack_url('products') }}">Productos</a>
            <a href="{{ backpack_url('stock-movements') }}">Movimientos</a>
            <a href="{{ backpack_url('branches') }}">Sucursales</a>
            <a href="{{ backpack_url('users') }}">Usuarios</a>
            <a href="{{ backpack_url('cash-sessions') }}">Sesiones de caja</a>
        </div>
        <p class="muted" style="margin-top:1rem;font-size:0.85rem;">
            <a href="{{ route('panel.index') }}">Panel operativo (caja del día)</a>
            ·
            <a href="{{ backpack_url('dashboard') }}">Inicio Backpack</a>
        </p>
    </div>
@endsection
