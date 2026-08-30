let productos = [];
let categorias = [];
let categoriasMap = {};

document.addEventListener('DOMContentLoaded', () => {
    // El menú por categorías (menu.js) maneja la carga inicial cuando existe
    if (!document.getElementById('menuCategorias')) {
        cargarProductos();
    }
    actualizarBadgeCarrito();

    const busqueda = document.getElementById('busqueda');
    const filtro = document.getElementById('filtroCategoria');

    if (busqueda) {
        busqueda.addEventListener('input', debounce(cargarProductos, 300));
    }
    if (filtro) {
        filtro.addEventListener('change', cargarProductos);
    }

    const navToggle = document.getElementById('navToggle');
    const navMenu = document.getElementById('navMenu');
    if (navToggle && navMenu) {
        navToggle.addEventListener('click', () => navMenu.classList.toggle('open'));
    }

    document.querySelectorAll('.modal').forEach(modal => {
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.style.display = 'none';
            }
        });
    });
});

function debounce(fn, delay) {
    let timer;
    return (...args) => {
        clearTimeout(timer);
        timer = setTimeout(() => fn(...args), delay);
    };
}

async function cargarProductos() {
    const lista = document.getElementById('listaProductos');
    const selectModal = document.getElementById('prodCategoria');
    // Si no hay lista de productos ni modal de admin, no hay nada que cargar.
    if (!lista && !selectModal) return;

    const busqueda = document.getElementById('busqueda')?.value || '';
    const categoria = document.getElementById('filtroCategoria')?.value || '';

    const params = new URLSearchParams();
    if (busqueda) params.append('busqueda', busqueda);
    if (categoria) params.append('categoria', categoria);

    try {
        const res = await fetch(`${routes.productos}?${params}`);
        productos = await res.json();
        extraerCategorias();
        renderProductos();
        llenarFiltroCategorias();
        return true;
    } catch (err) {
        if (lista) lista.innerHTML = '<p class="loading-msg">Error al cargar el menú. Intenta de nuevo.</p>';
        return false;
    }
}

function extraerCategorias() {
    categorias = [...new Set(productos.map(p => p.categoria).filter(Boolean))].sort();
    categoriasMap = {};
    productos.forEach(p => {
        if (p.categoria && p.id_categoria) {
            categoriasMap[p.categoria] = p.id_categoria;
        }
    });
}

function llenarFiltroCategorias() {
    const select = document.getElementById('filtroCategoria');
    const selectModal = document.getElementById('prodCategoria');
    const valorActual = select?.value || '';

    if (select) {
        select.innerHTML = '<option value="">Todas las categorías</option>';
        categorias.forEach(cat => {
            select.innerHTML += `<option value="${cat}" ${cat === valorActual ? 'selected' : ''}>${cat}</option>`;
        });
    }

    if (selectModal) {
        selectModal.innerHTML = '';
        categorias.forEach(cat => {
            selectModal.innerHTML += `<option value="${categoriasMap[cat]}">${cat}</option>`;
        });
    }
}

function renderProductos() {
    const lista = document.getElementById('listaProductos');
    if (!lista) return;

    if (productos.length === 0) {
        lista.innerHTML = '<p class="loading-msg">No se encontraron productos.</p>';
        return;
    }

    lista.innerHTML = productos.map(p => {
        const disponible = p.disponible !== false;
        const adminBtns = isAdmin ? `
            <div class="admin-controls">
                <button class="btn-edit-small" onclick="abrirModalEditar(${p.id})">✏️ Editar</button>
                <button class="btn-del-small" onclick="eliminarProducto(${p.id})">🗑️ Eliminar</button>
            </div>` : '';

        return `
        <div class="card ${disponible ? '' : 'product-out'}">
            <span class="cat-badge">${p.categoria || ''}</span>
            <h3>${p.nombre}</h3>
            <p class="desc">${p.descripcion || ''}</p>
            <button class="btn-add" onclick="agregarAlCarrito(${p.id})" ${disponible ? '' : 'disabled'}>
                ${disponible ? '🛒 Agregar al carrito' : 'No disponible'}
            </button>
            ${adminBtns}
        </div>`;
    }).join('');
}

function formatPrecio(precio) {
    return Number(precio).toLocaleString('es-CO', { minimumFractionDigits: 0, maximumFractionDigits: 2 });
}

async function agregarAlCarrito(id) {
    try {
        const res = await fetch(routes.carritoAdd, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ id }),
        });
        const data = await res.json();
        if (data.exito) {
            actualizarBadge(data.count);
            mostrarNotificacion('Producto agregado al carrito');
        } else {
            alert(data.error || 'Error al agregar producto');
        }
    } catch (err) {
        alert('Error de conexión');
    }
}

async function actualizarBadgeCarrito() {
    try {
        const res = await fetch(routes.carritoShow);
        const data = await res.json();
        actualizarBadge(data.count);
    } catch (err) { /* silencioso */ }
}

function actualizarBadge(count) {
    const badge = document.getElementById('countCarrito');
    const navCount = document.getElementById('navCartCount');
    if (badge) badge.textContent = count || 0;
    if (navCount) navCount.textContent = count || 0;
}

async function verCarrito() {
    const modal = document.getElementById('modalCarrito');
    if (!modal) return;

    try {
        const res = await fetch(routes.carritoShow);
        const data = await res.json();
        renderCarrito(data);
        modal.style.display = 'flex';
    } catch (err) {
        alert('Error al cargar el carrito');
    }
}

function renderCarrito(data) {
    const contenido = document.getElementById('contenidoCarrito');

    if (!data.items || data.items.length === 0) {
        contenido.innerHTML = '<p>Tu carrito está vacío.</p>';
        return;
    }

    // Carrito SIN precios ni totales monetarios: solo producto, cantidad y eliminar
    let html = '<table><thead><tr><th>Producto</th><th>Cant.</th><th></th></tr></thead><tbody>';
    data.items.forEach(item => {
        html += `<tr>
            <td>${item.nombre}</td>
            <td>
                <div class="qty-controls">
                    <button onclick="cambiarCantidad(${item.id}, ${item.cantidad - 1})">−</button>
                    <span>${item.cantidad}</span>
                    <button onclick="cambiarCantidad(${item.id}, ${item.cantidad + 1})">+</button>
                </div>
            </td>
            <td><button class="btn-del-small" onclick="eliminarDelCarrito(${item.id})">🗑️</button></td>
        </tr>`;
    });
    html += '</tbody></table>';
    contenido.innerHTML = html;
}

function cerrarCarrito() {
    const modal = document.getElementById('modalCarrito');
    if (modal) modal.style.display = 'none';
}

async function cambiarCantidad(id, cantidad) {
    if (cantidad < 1) {
        return eliminarDelCarrito(id);
    }
    try {
        const res = await fetch(routes.carritoUpdate, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ id, cantidad }),
        });
        const data = await res.json();
        renderCarrito(data);
        actualizarBadge(data.count);
    } catch (err) {
        alert('Error al actualizar cantidad');
    }
}

async function eliminarDelCarrito(id) {
    try {
        const res = await fetch(routes.carritoRemove, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ id }),
        });
        const data = await res.json();
        renderCarrito(data);
        actualizarBadge(data.count);
    } catch (err) {
        alert('Error al eliminar producto');
    }
}

async function confirmarPedido() {
    try {
        const res = await fetch(routes.carritoConfirm, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
        });
        const data = await res.json();

        if (res.status === 401) {
            if (confirm('Debes iniciar sesión para confirmar tu pedido. ¿Ir al login?')) {
                window.location.href = routes.login;
            }
            return;
        }

        if (data.exito) {
            alert(`¡Pedido #${data.pedido_id} confirmado! 🎉`);
            cerrarCarrito();
            actualizarBadge(0);
        } else {
            alert(data.error || 'Error al confirmar pedido');
        }
    } catch (err) {
        alert('Error de conexión');
    }
}

function mostrarNotificacion(msg) {
    const notif = document.createElement('div');
    notif.className = 'notif-toast';
    notif.textContent = msg;
    document.body.appendChild(notif);
    setTimeout(() => notif.remove(), 2500);
}

// Admin: modales de producto
function abrirModalCrear() {
    document.getElementById('modalTitulo').textContent = 'Nuevo Producto';
    document.getElementById('prodId').value = '';
    document.getElementById('prodIdDisplay').textContent = '(nuevo)';
    document.getElementById('prodNombre').value = '';
    document.getElementById('prodDescripcion').value = '';
    document.getElementById('prodPrecio').value = '';
    document.getElementById('prodDisponible').checked = true;
    document.getElementById('modalProducto').style.display = 'flex';
}

function abrirModalEditar(id) {
    const p = productos.find(x => x.id === id);
    if (!p) return;

    document.getElementById('modalTitulo').textContent = 'Editar Producto';
    document.getElementById('prodId').value = p.id;
    document.getElementById('prodIdDisplay').textContent = p.id;
    document.getElementById('prodNombre').value = p.nombre;
    document.getElementById('prodDescripcion').value = p.descripcion || '';
    document.getElementById('prodPrecio').value = p.precio;
    document.getElementById('prodCategoria').value = p.id_categoria;
    document.getElementById('prodDisponible').checked = p.disponible !== false;
    document.getElementById('modalProducto').style.display = 'flex';
}

function cerrarModal() {
    document.getElementById('modalProducto').style.display = 'none';
}

async function guardarProducto() {
    const id = document.getElementById('prodId').value;
    const precioRaw = document.getElementById('prodPrecio').value;

    const body = {
        nombre: document.getElementById('prodNombre').value,
        descripcion: document.getElementById('prodDescripcion').value,
        precio: precioRaw === '' ? 0 : parseFloat(precioRaw),
        id_categoria: parseInt(document.getElementById('prodCategoria').value),
        disponible: document.getElementById('prodDisponible').checked,
    };

    if (!body.nombre || isNaN(body.precio) || body.precio < 0 || !body.id_categoria) {
        alert('Complete los campos obligatorios');
        return;
    }

    const url = id ? `${routes.productos}/${id}` : routes.productos;
    const method = id ? 'PUT' : 'POST';

    try {
        const res = await fetch(url, {
            method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
            body: JSON.stringify(body),
        });
        const data = await res.json();
        if (data.exito) {
            cerrarModal();
            await cargarProductos();
            if (window.refrescarMenuTrasAdmin) window.refrescarMenuTrasAdmin();
            alert(data.exito);
        } else {
            alert(data.error || 'Error al guardar');
        }
    } catch (err) {
        alert('Error de conexión');
    }
}

async function eliminarProducto(id) {
    if (!confirm('¿Desactivar este producto?')) return;

    try {
        const res = await fetch(`${routes.productos}/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
        });
        const data = await res.json();
        if (data.exito) {
            await cargarProductos();
            if (window.refrescarMenuTrasAdmin) window.refrescarMenuTrasAdmin();
            alert(data.exito);
        } else {
            alert(data.error || 'Error al eliminar');
        }
    } catch (err) {
        alert('Error de conexión');
    }
}
