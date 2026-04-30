@extends('layouts.store')

@section('title', 'Inicio')

@section('content')
    <h1>Panel del negocio</h1>
    <p class="muted">Resumen del día y acceso rápido a la caja. Iniciá sesión desde el mismo usuario que usás en el administrador.</p>
    <p class="muted" style="font-size:0.85rem;">La caja guarda la venta y el cobro en efectivo; el stock por lote no se descuenta automáticamente todavía — ajustalo desde administración si hace falta.</p>

    @if($ctx['branch_id'])
        <p class="muted">Sucursal activa: <strong style="color:var(--text)">{{ $stats['branch'] ?? 'Sucursal #'.$ctx['branch_id'] }}</strong></p>
    @else
        <div class="card" style="border-color:var(--warn);">
            <strong style="color:var(--warn);">Falta configurar una sucursal</strong>
            <p class="muted" style="margin:0.5rem 0 0;">Creá una sucursal en el administrador y asignala al usuario (campo «Sucursal por defecto»).</p>
        </div>
    @endif

    <div class="grid2" style="margin-top:1rem;">
        <div class="stat">
            <span class="muted">Ventas completadas hoy</span>
            <strong>{{ $stats['ventas_hoy'] }}</strong>
        </div>
        <div class="stat">
            <span class="muted">Total facturado hoy</span>
            <strong>$ {{ number_format((float) $stats['total_hoy'], 2, ',', '.') }}</strong>
        </div>
    </div>

    <div style="margin-top:1.5rem; display:flex; gap:0.75rem; flex-wrap:wrap;">
        <a href="{{ route('panel.pos') }}" class="btn btn-primary" style="text-decoration:none;">Abrir caja rápida</a>
        <a href="{{ backpack_url('products') }}" class="btn btn-ghost" style="text-decoration:none;">Productos (admin)</a>
        <a href="{{ backpack_url('sales') }}" class="btn btn-ghost" style="text-decoration:none;">Ventas (admin)</a>
    </div>

    <div class="card" style="margin-top:1.5rem;">
        <h2>Últimas ventas</h2>
        @if($stats['ultimas']->isEmpty())
            <p class="muted">Todavía no hay ventas registradas como completadas.</p>
        @else
            <table>
                <thead>
                    <tr>
                        <th>Número</th>
                        <th>Total</th>
                        <th>Fecha</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($stats['ultimas'] as $v)
                        <tr>
                            <td class="mono">{{ $v->sale_number }}</td>
                            <td>$ {{ number_format((float) $v->grand_total, 2, ',', '.') }}</td>
                            <td class="muted">{{ $v->completed_at?->timezone(config('app.timezone'))->format('d/m H:i') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
@endsection
