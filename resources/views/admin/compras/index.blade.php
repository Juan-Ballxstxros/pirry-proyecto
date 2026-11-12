
@extends('layouts.admin')

@section('title', 'Compras de Insumos')
@section('page_title', 'Compras de insumos')
@section('page_subtitle', 'Historial de abastecimiento')

@section('content')

<style>
    .compras-page { width: 100%; }
    .compras-top {
        display: flex;
        justify-content: flex-end;
        margin-bottom: 22px;
    }
    .compras-btn {
        display: inline-flex;
        justify-content: center;
        align-items: center;
        padding: 10px 16px;
        border-radius: 9px;
        color: white;
        background: #f97316;
        border: 1px solid #f97316;
        font-size: 13px;
        font-weight: 600;
        text-decoration: none;
        transition: .2s ease;
    }
    .compras-btn:hover {
        color: white;
        background: #ea580c;
    }
    .compras-card {
        background: white;
        border: 1px solid #fed7aa;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(41,37,36,.04);
    }
    .compras-head {
        padding: 20px 22px;
        border-bottom: 1px solid #ffedd5;
    }
    .compras-head h3 {
        margin: 0;
        color: #292524;
        font-size: 17px;
        font-weight: 700;
    }
    .compras-head p {
        margin: 5px 0 0;
        color: #78716c;
        font-size: 13px;
    }
    .compras-scroll { overflow-x: auto; }
    .compras-table {
        width: 100%;
        min-width: 750px;
        border-collapse: collapse;
    }
    .compras-table th {
        padding: 15px 17px;
        background: #fff7ed;
        color: #57534e;
        text-align: left;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: .4px;
    }
    .compras-table td {
        padding: 16px 17px;
        border-top: 1px solid #f5f5f4;
        color: #44403c;
        font-size: 13px;
    }
    .compras-table tbody tr:hover { background: #fffbf7; }
    .compras-id {
        color: #c2410c;
        font-weight: 700;
    }
    .compras-main {
        color: #292524;
        font-weight: 700;
    }
    .compras-total {
        color: #c2410c;
        font-weight: 700;
    }
    .compras-empty {
        text-align: center;
        padding: 40px 20px !important;
        color: #78716c !important;
    }
    @media(max-width:600px) {
        .compras-top { justify-content: stretch; }
        .compras-top .compras-btn { width: 100%; }
    }
</style>

<div class="compras-page">
    <div class="compras-top">
        <a class="compras-btn"
           href="{{ route('admin.compras.create') }}">
            + Registrar compra
        </a>
    </div>

    <div class="compras-card">
        <div class="compras-head">
            <h3>Historial de compras</h3>
            <p>Consulta las compras de ingredientes y el abastecimiento de inventario.</p>
        </div>

        <div class="compras-scroll">
            <table class="compras-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Ingrediente</th>
                        <th>Proveedor</th>
                        <th>Cantidad</th>
                        <th>Precio unitario</th>
                        <th>Total</th>
                        <th>Fecha</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($compras as $c)
                        <tr>
                            <td><span class="compras-id">#{{ $c->id }}</span></td>
                            <td><span class="compras-main">{{ $c->ingrediente?->nombre }}</span></td>
                            <td>{{ $c->proveedor?->nombre }}</td>
                            <td>{{ $c->cantidad }} {{ $c->ingrediente?->unidad_medida }}</td>
                            <td>${{ number_format($c->precio_unitario, 0, ',', '.') }}</td>
                            <td>
                                <span class="compras-total">
                                    ${{ number_format($c->total, 0, ',', '.') }}
                                </span>
                            </td>
                            <td>{{ $c->fecha?->format('d/m/Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="compras-empty">
                                Aún no hay compras registradas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

