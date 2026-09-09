@extends($layout)

@section('title', 'Registrar Venta')
@section('page_title', 'Registrar venta')
@section('page_subtitle', 'Ventas de mostrador o por teléfono')

@push('styles')
<style>
.metodos-pago { display:flex; flex-wrap:wrap; gap:10px; margin-bottom:20px; }
.metodo-pago-btn {
    flex:1; min-width:110px; border:2px solid var(--border,#e7e9f0); background:#fff;
    border-radius:12px; padding:14px 10px; text-align:center; cursor:pointer; font-weight:600; font-size:13px;
    transition:.15s;
}
.metodo-pago-btn .emoji { display:block; font-size:22px; margin-bottom:6px; }
.metodo-pago-btn.activo { border-color:#ff8c42; background:#fff4ea; color:#e6702a; }
.aviso-recoger {
    background:#fff4ea; border:1px solid #ffd8b8; color:#8a4a15; border-radius:10px;
    padding:12px 16px; font-size:13.5px; margin-bottom:20px; display:flex; gap:10px; align-items:flex-start;
}
</style>
@endpush

@section('content')
<div class="contenedor-form">
    <div class="card">
        <div class="aviso-recoger">
            🏍️ <div>Este negocio <strong>no realiza domicilios</strong>: el pedido debe recogerse en el local.</div>
        </div>

        <form action="{{ route(request()->routeIs('admin.*') ? 'admin.ventas.store' : 'empleado.ventas.store') }}" method="POST" id="formVenta">
            @csrf
            <div class="grid">
                <div class="campo">
                    <label>A nombre de quién es el pedido</label>
                    <input type="text" name="nombre_cliente" placeholder="Ej: Juan Pérez" required>
                </div>
                <div class="campo">
                    <label>Cliente registrado (opcional)</label>
                    <select name="id_usuario">
                        <option value="">Cliente sin cuenta / mostrador</option>
                        @foreach($clientes as $cliente)
                            <option value="{{ $cliente->id }}">{{ $cliente->nombre }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <label style="display:block; font-size:13px; font-weight:600; color:var(--text-muted); margin-bottom:8px;">Método de pago</label>
            <div class="metodos-pago" id="metodosPago">
                <div class="metodo-pago-btn activo" data-valor="efectivo"><span class="emoji">💵</span>Efectivo</div>
                <div class="metodo-pago-btn" data-valor="tarjeta"><span class="emoji">💳</span>Tarjeta</div>
                <div class="metodo-pago-btn" data-valor="transferencia"><span class="emoji">🏦</span>Transferencia</div>
                <div class="metodo-pago-btn" data-valor="nequi"><span class="emoji">📱</span>Nequi</div>
                <div class="metodo-pago-btn" data-valor="daviplata"><span class="emoji">📱</span>Daviplata</div>
            </div>
            <input type="hidden" name="forma_pago" id="formaPagoInput" value="efectivo">

            <h3 style="margin-bottom:12px;">Productos</h3>
            <div id="lineasProductos"></div>
            <button type="button" class="btn btn-outline btn-sm" id="btnAgregarLinea" style="margin-bottom:20px;">+ Agregar producto</button>

            <div class="acciones">
                <button type="submit" class="btn guardar">Registrar venta</button>
                <a href="{{ route(request()->routeIs('admin.*') ? 'admin.dashboard' : 'empleado.dashboard') }}" class="btn cancelar">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('metodosPago').addEventListener('click', function (e) {
    const btn = e.target.closest('.metodo-pago-btn');
    if (!btn) return;
    this.querySelectorAll('.metodo-pago-btn').forEach(b => b.classList.remove('activo'));
    btn.classList.add('activo');
    document.getElementById('formaPagoInput').value = btn.dataset.valor;
});

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