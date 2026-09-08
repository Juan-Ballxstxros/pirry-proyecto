@extends('layouts.empleado')

@section('title', 'Registrar Venta')
@section('page_title', 'Registrar venta')
@section('page_subtitle', 'Ventas de mostrador o por teléfono')

@section('content')
<div class="contenedor-form">
    <div class="card">
        <form action="{{ route('empleado.ventas.store') }}" method="POST" id="formVenta">
            @csrf
            <div class="grid">
                <div class="campo">
                    <label>Cliente (opcional)</label>
                    <select name="id_usuario">
                        <option value="">Cliente genérico / mostrador</option>
                        @foreach($clientes as $cliente)
                            <option value="{{ $cliente->id }}">{{ $cliente->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="campo">
                    <label>Método de pago</label>
                    <select name="forma_pago" required>
                        <option value="efectivo">Efectivo</option>
                        <option value="tarjeta">Tarjeta</option>
                        <option value="transferencia">Transferencia</option>
                        <option value="nequi">Nequi</option>
                        <option value="daviplata">Daviplata</option>
                    </select>
                </div>
            </div>

            <h3 style="margin-bottom:12px;">Productos</h3>
            <div id="lineasProductos"></div>
            <button type="button" class="btn btn-outline btn-sm" id="btnAgregarLinea" style="margin-bottom:20px;">+ Agregar producto</button>

            <div class="acciones">
                <button type="submit" class="btn guardar">Registrar venta</button>
                <a href="{{ route('empleado.dashboard') }}" class="btn cancelar">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<script>
const productos = @json($productos->map(fn($p) => ['id' => $p->id, 'nombre' => $p->nombre, 'precio' => $p->precio]));
const contenedor = document.getElementById('lineasProductos');
let contador = 0;

function agregarLinea() {
    const idx = contador++;
    const div = document.createElement('div');
    div.className = 'grid';
    div.style.gridTemplateColumns = '2fr 1fr auto';
    div.innerHTML = `
        <div class="campo">
            <label>Producto</label>
            <select name="productos[${idx}][id]" required>
                ${productos.map(p => `<option value="${p.id}">${p.nombre} - $${Number(p.precio).toLocaleString('es-CO')}</option>`).join('')}
            </select>
        </div>
        <div class="campo">
            <label>Cantidad</label>
            <input type="number" name="productos[${idx}][cantidad]" min="1" value="1" required>
        </div>
        <div class="campo" style="display:flex; align-items:flex-end;">
            <button type="button" class="btn btn-danger btn-sm" onclick="this.closest('.grid').remove()">Quitar</button>
        </div>
    `;
    contenedor.appendChild(div);
}

document.getElementById('btnAgregarLinea').addEventListener('click', agregarLinea);
document.getElementById('formVenta').addEventListener('submit', function (e) {
    if (contenedor.children.length === 0) {
        e.preventDefault();
        alert('Agrega al menos un producto a la venta.');
    }
});

agregarLinea();
</script>
@endsection
