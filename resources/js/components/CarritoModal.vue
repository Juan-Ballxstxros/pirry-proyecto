<template>
    <div class="modal" :style="{ display: abierto ? 'flex' : 'none' }" @click.self="cerrar()">
        <div class="modal-content">
            <h3>🛒 Tu Pedido</h3>

            <div id="contenidoCarrito">
                <p v-if="items.length === 0">Tu carrito está vacío.</p>
                <table v-else>
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Cant.</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="item in items" :key="item.id">
                            <td>{{ item.nombre }}</td>
                            <td>
                                <div class="qty-controls">
                                    <button type="button" @click="cambiarCantidad(item, -1)">−</button>
                                    <span>{{ item.cantidad }}</span>
                                    <button type="button" @click="cambiarCantidad(item, 1)">+</button>
                                </div>
                            </td>
                            <td>
                                <button type="button" class="btn-del-small" @click="eliminar(item)">🗑️</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- RN-017: toda venta debe incluir un método de pago. -->
            <div class="form-pago" v-if="items.length > 0">
                <label for="formaPago">Método de pago</label>
                <select id="formaPago" v-model="formaPago">
                    <option value="efectivo">Efectivo</option>
                    <option value="tarjeta">Tarjeta</option>
                    <option value="transferencia">Transferencia</option>
                    <option value="nequi">Nequi</option>
                    <option value="daviplata">Daviplata</option>
                </select>
            </div>

            <!-- Botón de envío del pedido por WhatsApp. Solo LEE el carrito
                 existente: no modifica productos, cantidades ni contadores. -->
            <button type="button" class="btn-whatsapp" @click="enviarPedidoWhatsApp">
                <svg viewBox="0 0 32 32" aria-hidden="true" focusable="false">
                    <path fill="currentColor" d="M16.004 3.2c-7.06 0-12.8 5.74-12.8 12.8 0 2.26.6 4.46 1.72 6.4L3.2 28.8l6.56-1.68a12.75 12.75 0 0 0 6.24 1.6h.004c7.06 0 12.8-5.74 12.8-12.8s-5.74-12.8-12.8-12.8zm0 23.392h-.004c-1.92 0-3.8-.52-5.44-1.5l-.39-.23-4.04 1.04 1.08-3.94-.26-.4a10.6 10.6 0 0 1-1.63-5.66c0-5.87 4.78-10.64 10.65-10.64 2.84 0 5.51 1.11 7.52 3.12a10.57 10.57 0 0 1 3.12 7.53c0 5.87-4.78 10.64-10.65 10.64zm5.84-7.97c-.32-.16-1.89-.93-2.18-1.04-.29-.11-.5-.16-.72.16-.21.32-.83 1.04-1.02 1.25-.19.21-.37.24-.69.08-.32-.16-1.35-.5-2.57-1.59-.95-.85-1.59-1.9-1.78-2.22-.19-.32-.02-.49.14-.65.14-.14.32-.37.48-.56.16-.19.21-.32.32-.53.11-.21.05-.4-.03-.56-.08-.16-.72-1.73-.98-2.37-.26-.62-.52-.54-.72-.55h-.61c-.21 0-.56.08-.85.4-.29.32-1.11 1.09-1.11 2.65s1.14 3.07 1.3 3.29c.16.21 2.24 3.42 5.42 4.79.76.33 1.35.52 1.81.67.76.24 1.45.21 2 .13.61-.09 1.89-.77 2.15-1.52.27-.74.27-1.38.19-1.52-.08-.13-.29-.21-.61-.37z"/>
                </svg>
                Enviar pedido por WhatsApp
            </button>

            <div class="modal-buttons">
                <button type="button" class="btn-cancel" @click="cerrar()">Seguir comprando</button>
                <button type="button" class="btn-save" :disabled="procesando" @click="confirmarPedido">✅ Confirmar Pedido</button>
            </div>
        </div>
    </div>
</template>

<script>
/**
 * Carrito de compras (modal).
 * Portado 1:1 desde public/js/index.js (carrito + WhatsApp).
 * - La sesión la valida SIEMPRE el servidor: confirmar reacciona al 401
 *   del middleware auth.session y WhatsApp lee 'logueado' de GET /carrito
 *   (Auth::check del sistema existente). No hay validación de sesión en JS.
 * - El total del pedido sigue calculándolo Laravel al confirmar.
 * - El mensaje de WhatsApp usa solo nombre × cantidad (sin precios),
 *   igual que antes.
 */
export default {
    name: 'CarritoModal',
    props: {
        routes: { type: Object, required: true },
        csrf: { type: String, required: true },
        notificarCarrito: { type: Function, required: true },
    },
    data() {
        return {
            abierto: false,
            items: [],
            total: 0,
            count: 0,
            logueado: false,
            procesando: false,
            formaPago: 'efectivo',
        };
    },
    methods: {
        async abrir() {
            await this.cargarCarrito();
            this.abierto = true;
        },
        cerrar() {
            this.abierto = false;
        },
        async cargarCarrito() {
            try {
                const res = await fetch(this.routes.carritoShow, { headers: { Accept: 'application/json' } });
                this.aplicarEstado(await res.json());
            } catch (err) {
                this.toast('Error al cargar el carrito');
            }
        },
        aplicarEstado(data) {
            this.items = data.items || [];
            this.total = data.total || 0;
            this.count = data.count || 0;
            this.logueado = !!data.logueado;
            this.notificarCarrito(this.count);
        },
        async cambiarCantidad(item, delta) {
            const nueva = item.cantidad + delta;
            if (nueva < 1) return this.eliminar(item);
            try {
                const res = await fetch(this.routes.carritoUpdate, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': this.csrf, Accept: 'application/json' },
                    body: JSON.stringify({ id: item.id, cantidad: nueva }),
                });
                this.aplicarEstado(await res.json());
            } catch (err) {
                this.toast('Error al actualizar cantidad');
            }
        },
        async eliminar(item) {
            try {
                const res = await fetch(this.routes.carritoRemove, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': this.csrf, Accept: 'application/json' },
                    body: JSON.stringify({ id: item.id }),
                });
                this.aplicarEstado(await res.json());
            } catch (err) {
                this.toast('Error al eliminar producto');
            }
        },
        async confirmarPedido() {
            if (this.procesando) return;
            this.procesando = true;
            try {
                const res = await fetch(this.routes.carritoConfirm, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': this.csrf, Accept: 'application/json' },
                    body: JSON.stringify({ forma_pago: this.formaPago }),
                });
                if (res.status === 401) {
                    // La sesión la valida el SERVIDOR: si responde 401 se
                    // muestra el modal de inicio de sesión existente.
                    this.mostrarModalAuth();
                    return;
                }
                const data = await res.json();
                if (data.exito) {
                    this.toast('¡Pedido #' + data.pedido_id + ' confirmado! 🎉', 'success');
                    this.abierto = false;
                    this.aplicarEstado({ items: [], total: 0, count: 0, logueado: this.logueado });
                } else {
                    this.toast(data.error || 'Error al confirmar pedido');
                }
            } catch (err) {
                this.toast('Error de conexión');
            } finally {
                this.procesando = false;
            }
        },
        async enviarPedidoWhatsApp() {
            let data;
            try {
                const res = await fetch(this.routes.carritoShow, { headers: { Accept: 'application/json' } });
                data = await res.json();
            } catch (err) {
                this.aviso('No se pudo leer tu pedido. Intenta de nuevo.', 'error', '¡Ups!');
                return;
            }
            if (!data.items || data.items.length === 0) {
                this.aviso('Tu carrito está vacío. Agrega productos para enviar tu pedido.', 'info', 'Carrito vacío');
                return;
            }
            if (!data.logueado) {
                this.mostrarModalAuth();
                return;
            }
            const numero = ((window.whatsappConfig && window.whatsappConfig.numero) || '').trim();
            if (!numero) {
                this.aviso('El administrador debe configurar el número de WhatsApp del negocio.', 'info', 'WhatsApp aún no está configurado');
                return;
            }
            let mensaje = 'Hola, quiero realizar el siguiente pedido:\\n';
            data.items.forEach((item) => {
                mensaje += '\\n' + item.nombre + ' × ' + item.cantidad;
            });
            try {
                const url = 'https://wa.me/' + numero + '?text=' + encodeURIComponent(mensaje);
                window.open(url, '_blank', 'noopener');
            } catch (err) {
                this.aviso('No se pudo abrir WhatsApp. Intenta de nuevo.', 'error', '¡Ups!');
            }
        },
        mostrarModalAuth() {
            // Cierra el carrito para que no quede detrás del modal de sesión
            this.abierto = false;
            const modal = document.getElementById('modalAuth');
            if (modal) modal.style.display = 'flex';
        },
        aviso(mensaje, tipo, titulo) {
            if (typeof window.showToast === 'function') {
                window.showToast(mensaje, tipo || 'info', 5200, titulo || null);
            }
        },
        toast(mensaje, tipo) {
            if (typeof window.showToast === 'function') {
                window.showToast(mensaje, tipo || 'error');
            }
        },
    },
};
</script>
