let productos = [];
let categorias = [];
let categoriasMap = {};

document.addEventListener('DOMContentLoaded', () => {
    // El menú por categorías (menu.js) maneja la carga inicial cuando existe
    if (!document.getElementById('menuFiltros')) {
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
    // La sesión la valida el SERVIDOR (Auth::check en el middleware):
    // si responde 401, más abajo se muestra el modal de inicio de sesión.
    // Así, una página abierta antes del login funciona igual en cuanto
    // el usuario inicia sesión, sin depender de una variable "fija".
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
            // La sesión expiró a mitad del flujo: modal integrado en la
            // página (sin confirm() nativo del navegador).
            mostrarModalAuth();
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

/* ============================================================
   ENVIAR PEDIDO POR WHATSAPP
   Toma la información que YA existe en el carrito (GET /carrito)
   y la convierte en un mensaje de texto para WhatsApp.
   • Usa solo nombre y cantidad: SIN precios ni totales monetarios.
   • NO modifica el carrito (agregar/eliminar/cantidades quedan igual).
   • El número del negocio se configura en .env → WHATSAPP_NUMERO_NEGOCIO
     y llega aquí vía la variable whatsappConfig del layout. Mientras
     esté vacío, el botón solo muestra un aviso (sin errores).
   ============================================================ */
function notificarAviso(mensaje, tipo, titulo) {
    // Usa el sistema de toasts del proyecto (toast.js) si está disponible.
    if (typeof showToast === 'function') {
        showToast(mensaje, tipo, 5200, titulo);
    }
}

async function enviarPedidoWhatsApp() {
    // 1) Lee el carrito existente (no lo modifica).
    let data;
    try {
        const res = await fetch(routes.carritoShow, {
            headers: { 'Accept': 'application/json' },
        });
        data = await res.json();
    } catch (err) {
        notificarAviso('No se pudo leer tu pedido. Intenta de nuevo.', 'error', '¡Ups!');
        return;
    }

    // 2) Debe haber productos en el carrito.
    if (!data.items || data.items.length === 0) {
        notificarAviso('Tu carrito está vacío. Agrega productos para enviar tu pedido.', 'info', 'Carrito vacío');
        return;
    }

    // 3) Validación de sesión con el ESTADO REAL del servidor:
    //    campo 'logueado' que GET /carrito calcula con Auth::check()
    //    (el mismo sistema de autenticación existente).
    if (!data.logueado) {
        mostrarModalAuth();
        return;
    }

    // 4) Número del negocio (formato internacional, ej. 57XXXXXXXXXX).
    const numero = ((window.whatsappConfig && window.whatsappConfig.numero) || '').trim();

    if (!numero) {
        notificarAviso(
            'El administrador debe configurar el número de WhatsApp del negocio.',
            'info',
            'WhatsApp aún no está configurado'
        );
        return;
    }

    // 5) Mensaje con SOLO la información del pedido: nombre y cantidad.
    //    Sin precios, sin $0 y sin totales monetarios.
    let mensaje = 'Hola, quiero realizar el siguiente pedido:\n';
    data.items.forEach(item => {
        mensaje += '\n' + item.nombre + ' × ' + item.cantidad;
    });

    // 6) Abre WhatsApp con el mensaje ya preparado.
    //    En celular abre la app de WhatsApp; en PC/laptop, WhatsApp Web.
    try {
        const url = 'https://wa.me/' + numero + '?text=' + encodeURIComponent(mensaje);
        window.open(url, '_blank', 'noopener');
    } catch (err) {
        notificarAviso('No se pudo abrir WhatsApp. Intenta de nuevo.', 'error', '¡Ups!');
    }
}

/* ============================================================
   VALIDACIÓN DE SESIÓN PARA CONFIRMAR / ENVIAR EL PEDIDO
   La decide SIEMPRE el servidor con el sistema de autenticación
   existente (Auth::check): confirmarPedido reacciona al 401 de la
   API y enviarPedidoWhatsApp lee el campo 'logueado' de GET /carrito.
   Así no se usa una "foto" del estado hecha al renderizar la página
   (que quedaba obsoleta si el login ocurría después). Los botones
   llevan a las páginas de login/registro que ya existen; el carrito
   vive en la sesión y se conserva intacto tras iniciar sesión.
   ============================================================ */
function mostrarModalAuth() {
    const modal = document.getElementById('modalAuth');
    if (!modal) return;

    // Cierra el modal del carrito para que no quede detrás del de sesión.
    cerrarCarrito();
    modal.style.display = 'flex';
}

function cerrarModalAuth() {
    const modal = document.getElementById('modalAuth');
    if (modal) modal.style.display = 'none';
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
    resetearImagenModal(null);
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
    resetearImagenModal(p.imagen || null);
    document.getElementById('modalProducto').style.display = 'flex';
}

function cerrarModal() {
    document.getElementById('modalProducto').style.display = 'none';
}

/* ---------- Imagen del producto (solo administrador) ---------- */

/* Ajusta la vista previa del modal según el estado actual de la imagen */
function resetearImagenModal(imagenActual) {
    const input = document.getElementById('prodImagen');
    const previewImg = document.getElementById('imagenPreviewImg');
    const vaciaTxt = document.getElementById('imagenVaciaTxt');
    const btnEliminar = document.getElementById('btnEliminarImagen');
    if (!input) return;

    input.value = '';
    if (previewImg.src && previewImg.src.startsWith('blob:')) {
        URL.revokeObjectURL(previewImg.src);
    }

    if (imagenActual) {
        previewImg.src = imagenActual;
        previewImg.style.display = 'block';
        vaciaTxt.style.display = 'none';
        btnEliminar.style.display = 'inline-block';
    } else {
        previewImg.src = '';
        previewImg.style.display = 'none';
        vaciaTxt.style.display = 'block';
        btnEliminar.style.display = 'none';
    }
}

/* Vista previa instantánea al elegir archivo (validación rápida en cliente;
   la validación definitiva la hace Laravel en el servidor) */
document.addEventListener('DOMContentLoaded', () => {
    const inputImagen = document.getElementById('prodImagen');
    if (!inputImagen) return;

    inputImagen.addEventListener('change', () => {
        const archivo = inputImagen.files && inputImagen.files[0];
        if (!archivo) return;

        const tiposPermitidos = ['image/jpeg', 'image/png', 'image/webp'];
        if (!tiposPermitidos.includes(archivo.type)) {
            showToast('Formato no permitido. Usa JPG, JPEG, PNG o WEBP.', 'error');
            inputImagen.value = '';
            return;
        }
        if (archivo.size > 2 * 1024 * 1024) {
            showToast('La imagen no debe pesar más de 2 MB.', 'error');
            inputImagen.value = '';
            return;
        }

        const previewImg = document.getElementById('imagenPreviewImg');
        const vaciaTxt = document.getElementById('imagenVaciaTxt');
        const btnEliminar = document.getElementById('btnEliminarImagen');
        if (previewImg.src && previewImg.src.startsWith('blob:')) {
            URL.revokeObjectURL(previewImg.src);
        }
        previewImg.src = URL.createObjectURL(archivo);
        previewImg.style.display = 'block';
        vaciaTxt.style.display = 'none';
        btnEliminar.style.display = 'none'; // hay un archivo nuevo pendiente de guardar
    });
});

/* Elimina SOLO la imagen del producto (el producto se conserva) */
async function eliminarImagenProducto() {
    const id = document.getElementById('prodId').value;
    if (!id) return;

    const confirmado = await pirryConfirm({
        icon: '🖼️',
        title: '¿Eliminar la imagen?',
        text: 'El producto quedará sin imagen. Esta acción no se puede deshacer.',
        okText: 'Eliminar imagen',
        cancelText: 'Cancelar',
        danger: true,
    });
    if (!confirmado) return;

    try {
        const res = await fetch(`${routes.productos}/${id}/imagen`, {
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
            resetearImagenModal(null);
            showToast('La imagen se eliminó correctamente.', 'success', 4200, '¡Imagen eliminada!');
        } else {
            showToast(data.error || 'Error al eliminar la imagen.', 'error');
        }
    } catch (err) {
        showToast('Error de conexión al eliminar la imagen.', 'error');
    }
}

async function guardarProducto() {
    const id = document.getElementById('prodId').value;
    const precioRaw = document.getElementById('prodPrecio').value;
    const precio = precioRaw === '' ? 0 : parseFloat(precioRaw);

    if (!document.getElementById('prodNombre').value || isNaN(precio) || precio < 0 || !document.getElementById('prodCategoria').value) {
        showToast('Complete los campos obligatorios.', 'error');
        return;
    }

    // FormData (multipart) para poder incluir la imagen del producto
    const formData = new FormData();
    formData.append('nombre', document.getElementById('prodNombre').value);
    formData.append('descripcion', document.getElementById('prodDescripcion').value);
    formData.append('precio', String(precio));
    formData.append('id_categoria', document.getElementById('prodCategoria').value);
    formData.append('disponible', document.getElementById('prodDisponible').checked ? '1' : '0');

    // Si no se eligió archivo, "imagen" no viaja: la imagen actual se conserva
    const inputImagen = document.getElementById('prodImagen');
    const archivoImagen = inputImagen && inputImagen.files && inputImagen.files[0];
    if (archivoImagen) {
        formData.append('imagen', archivoImagen);
    }

    // Method spoofing de Laravel (mismo fix que MenuProductos.vue): PHP no
    // llena $_POST/$_FILES en un PUT real con multipart, por eso fallaba la
    // validación "required". POST + _method=PUT llega igual al update().
    if (id) {
        formData.append('_method', 'PUT');
    }

    const url = id ? `${routes.productos}/${id}` : routes.productos;
    const method = 'POST';

    try {
        const res = await fetch(url, {
            method,
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
            body: formData,
        });
        const data = await res.json();

        // Errores de validación de Laravel (formato/tamaño de imagen, etc.)
        if (res.status === 422) {
            const errores = data.errors || {};
            const primerMensaje = (Object.values(errores)[0] && Object.values(errores)[0][0]) || data.message || 'Datos inválidos.';
            showToast(primerMensaje, 'error');
            return;
        }

        if (data.exito) {
            cerrarModal();
            await cargarProductos();
            if (window.refrescarMenuTrasAdmin) window.refrescarMenuTrasAdmin();
            // Notificación integrada en la página (sin alert() nativo):
            // título = "¡Producto actualizado!" / "¡Producto creado!" según la acción.
            showToast('Los cambios se guardaron correctamente.', 'success', 4200, '¡' + data.exito + '!');
        } else {
            showToast(data.error || 'Error al guardar el producto.', 'error');
        }
    } catch (err) {
        showToast('Error de conexión al guardar el producto.', 'error');
    }
}

async function eliminarProducto(id) {
    // Confirmación con el modal del proyecto (sin confirm() nativo)
    const confirmado = await pirryConfirm({
        icon: '🗑️',
        title: '¿Estás seguro?',
        text: '¿Quieres eliminar este producto? Esta acción no se puede deshacer.',
        okText: 'Eliminar',
        cancelText: 'Cancelar',
        danger: true,
    });
    if (!confirmado) return;

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
            // Notificación integrada en la página (sin alert() nativo)
            showToast('El producto se eliminó correctamente.', 'success', 4200, '¡Producto eliminado!');
        } else {
            showToast(data.error || 'Error al eliminar el producto.', 'error');
        }
    } catch (err) {
        showToast('Error de conexión al eliminar el producto.', 'error');
    }
}
