
@extends($layout)

@section('title', 'Historial de Ventas')
@section('page_title', 'Historial de ventas')

@section('content')

<style>
    .historial-page {
        --pirry-orange: #F97316;
        --pirry-dark: #292524;
        --pirry-gray: #78716C;
        --pirry-border: #FED7AA;
        width: 100%;
        color: var(--pirry-dark);
        background: #FFFFFF;
    }

    .historial-page * {
        box-sizing: border-box;
    }

    .historial-page .historial-card {
        background: #FFFFFF;
        border: 1px solid var(--pirry-border);
        border-radius: 14px;
        padding: 24px;
        margin-bottom: 24px;
        box-shadow: 0 3px 12px rgba(41, 37, 36, .04);
    }

    .historial-page .section-title {
        margin: 0 0 7px;
        font-size: 19px;
        font-weight: 700;
        color: var(--pirry-dark);
    }

    .historial-page .section-subtitle {
        margin: 0 0 20px;
        color: var(--pirry-gray);
        font-size: 13px;
    }

    .historial-page .filter-form {
        display: flex;
        align-items: end;
        flex-wrap: wrap;
        gap: 18px;
    }

    .historial-page .filter-group {
        display: flex;
        flex-direction: column;
        gap: 8px;
        flex: 1;
        min-width: 180px;
    }

    .historial-page .filter-group label {
        color: var(--pirry-dark);
        font-size: 13px;
        font-weight: 600;
    }

    .historial-page .filter-group input {
        width: 100%;
        height: 43px;
        padding: 10px 12px;
        border: 1px solid var(--pirry-border);
        border-radius: 8px;
        background: #FFFFFF;
        color: var(--pirry-dark);
        font-size: 14px;
        outline: none;
        transition: .2s;
    }

    .historial-page .filter-group input:focus {
        border-color: var(--pirry-orange);
        box-shadow: 0 0 0 3px rgba(249, 115, 22, .10);
    }

    .historial-page .btn-filter {
        height: 43px;
        padding: 0 24px;
        border: 1px solid var(--pirry-orange);
        border-radius: 8px;
        background: var(--pirry-orange);
        color: #FFFFFF;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: .2s;
    }

    .historial-page .btn-filter:hover {
        background: #EA580C;
        transform: translateY(-1px);
    }

    .historial-page .table-heading {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 18px;
    }

    .historial-page .table-heading h3 {
        margin: 0;
        color: var(--pirry-dark);
        font-size: 19px;
        font-weight: 700;
    }

    .historial-page .sales-count {
        padding: 6px 12px;
        border: 1px solid var(--pirry-border);
        border-radius: 20px;
        background: #FFF7ED;
        color: var(--pirry-orange);
        font-size: 12px;
        font-weight: 700;
        white-space: nowrap;
    }

    .historial-page .table-container {
        width: 100%;
        overflow-x: auto;
        border: 1px solid var(--pirry-border);
        border-radius: 10px;
    }

    .historial-page table {
        width: 100%;
        min-width: 850px;
        border-collapse: collapse;
        background: #FFFFFF;
    }

    .historial-page table th {
        padding: 15px 14px;
        background: #FFF7ED;
        color: var(--pirry-dark);
        text-align: left;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .3px;
        border-bottom: 1px solid var(--pirry-border);
        white-space: nowrap;
    }

    .historial-page table td {
        padding: 16px 14px;
        border-bottom: 1px solid var(--pirry-border);
        color: var(--pirry-dark);
        font-size: 13px;
        vertical-align: middle;
    }

    .historial-page table tr:last-child td {
        border-bottom: none;
    }

    .historial-page table tbody tr {
        transition: background .2s;
    }

    .historial-page table tbody tr:hover {
        background: #FFF7ED;
    }

    .historial-page .sale-code {
        color: var(--pirry-orange);
        font-weight: 700;
        white-space: nowrap;
    }

    .historial-page .customer-name {
        font-weight: 600;
    }

    .historial-page .products-cell {
        color: var(--pirry-gray);
        min-width: 180px;
        line-height: 1.6;
    }

    .historial-page .sale-total {
        color: var(--pirry-dark);
        font-weight: 700;
        white-space: nowrap;
    }

    .historial-page .payment-type {
        color: var(--pirry-gray);
        white-space: nowrap;
    }

    .historial-page .sale-date {
        color: var(--pirry-gray);
        white-space: nowrap;
    }

    .historial-page .empty-state {
        padding: 35px 15px;
        text-align: center;
        color: var(--pirry-gray);
        font-size: 14px;
    }

    .historial-page .pagination-container {
        margin-top: 20px;
        overflow-x: auto;
    }

    .historial-page .pagination-container nav {
        display: flex;
        justify-content: flex-end;
    }

    @media (max-width: 600px) {
        .historial-page .historial-card {
            padding: 17px;
        }

        .historial-page .filter-form {
            flex-direction: column;
            align-items: stretch;
        }

        .historial-page .filter-group {
            width: 100%;
        }

        .historial-page .btn-filter {
            width: 100%;
        }

        .historial-page .table-heading {
            align-items: flex-start;
        }
    }
</style>

<div class="historial-page">

    {{-- FILTROS --}}
    <div class="historial-card">

        <h3 class="section-title">Filtrar ventas</h3>

        <p class="section-subtitle">
            Consulta las ventas realizadas según el rango de fechas.
        </p>

        <form method="GET" class="filter-form">

            <div class="filter-group">
                <label for="desde">Desde</label>
                <input
                    type="date"
                    id="desde"
                    name="desde"
                    value="{{ request('desde') }}"
                >
            </div>

            <div class="filter-group">
                <label for="hasta">Hasta</label>
                <input
                    type="date"
                    id="hasta"
                    name="hasta"
                    value="{{ request('hasta') }}"
                >
            </div>

            <button type="submit" class="btn-filter">
                Filtrar ventas
            </button>

        </form>

    </div>

    {{-- HISTORIAL DE VENTAS --}}
    <div class="historial-card">

        <div class="table-heading">

            <h3>Ventas registradas</h3>

            <span class="sales-count">
                {{ $pedidos->total() }} ventas
            </span>

        </div>

        <div class="table-container">

            <table>

                <thead>
                    <tr>
                        <th>#</th>
                        <th>A nombre de</th>
                        <th>Productos</th>
                        <th>Total</th>
                        <th>Pago</th>
                        <th>Estado</th>
                        <th>Fecha</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse($pedidos as $pedido)

                    <tr>

                        <td class="sale-code">
                            {{ $pedido->codigo }}
                        </td>

                        <td class="customer-name">
                            {{ $pedido->nombre_cliente ?? $pedido->usuario?->nombre ?? 'Invitado' }}
                        </td>

                        <td class="products-cell">
                            @foreach($pedido->items as $item)
                                {{ $item->cantidad }}×
                                {{ $item->producto?->nombre ?? 'Producto eliminado' }}@if(!$loop->last), @endif
                            @endforeach
                        </td>

                        <td class="sale-total">
                            ${{ number_format($pedido->total, 0, ',', '.') }}
                        </td>

                        <td class="payment-type">
                            {{ ucfirst($pedido->forma_pago ?? '—') }}
                        </td>

                        <td>
                            <span class="pill pill-estado pill-estado-{{ $pedido->estado }}">
                                {{ ucfirst(str_replace('_', ' ', $pedido->estado)) }}
                            </span>
                        </td>

                        <td class="sale-date">
                            {{ $pedido->fecha?->format('d/m/Y H:i') }}
                        </td>

                    </tr>

                    @empty

                    <tr>
                        <td colspan="7" class="empty-state">

                            @if(request('desde') || request('hasta'))
                                No hay ventas registradas en ese rango de fechas.
                            @else
                                Aún no hay ventas registradas.
                            @endif

                        </td>
                    </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

        <div class="pagination-container">
            {{ $pedidos->links() }}
        </div>

    </div>

</div>

@endsection
