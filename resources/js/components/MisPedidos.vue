<template>
    <div class="mp-wrap">
        <div class="mp-head">
            <div>
                <h2 class="mp-title">📦 Mis pedidos</h2>
                <p class="mp-subtitle">Consulta el estado y el seguimiento de tus pedidos en tiempo real.</p>
            </div>
            <div class="mp-refresh">
                <span v-if="ultimaActualizacion" class="mp-updated">Actualizado: {{ ultimaActualizacion }}</span>
                <button type="button" class="mp-btn-refresh" title="Actualizar ahora" @click="cargar()">🔄</button>
            </div>
        </div>

        <p v-if="cargando" class="mp-vacio">Cargando tus pedidos…</p>

        <p v-else-if="error" class="mp-vacio mp-error">{{ error }} <a :href="routes.login">Iniciar sesión</a></p>

        <div v-else-if="pedidos.length === 0" class="mp-vacio">
            <p>Aún no has realizado pedidos. 🍔</p>
            <a class="mp-btn-primario" :href="routes.home">Ver el menú</a>
        </div>

        <div v-else class="mp-grid">
            <article v-for="pedido in pedidos" :key="pedido.id" class="mp-card">
                <header class="mp-card-head" :class="'mp-head-' + pedido.estado">
                    <div>
                        <span class="mp-codigo">{{ pedido.codigo }}</span>
                        <span class="mp-fecha">{{ pedido.fecha }}</span>
                    </div>
                    <span class="mp-badge" :class="'mp-badge-' + pedido.estado">{{ etiqueta(pedido.estado) }}</span>
                </header>

                <!-- Aviso especial para pedidos cancelados -->
                <div v-if="pedido.estado === 'cancelado'" class="mp-cancelado-aviso">
                    Este pedido fue cancelado.
                </div>

                <div class="mp-detalles">
                    <table class="mp-tabla">
                        <thead>
                            <tr><th>Producto</th><th>Cant.</th><th>Precio</th><th>Subtotal</th></tr>
                        </thead>
                        <tbody>
                            <tr v-for="(item, i) in pedido.productos" :key="i">
                                <td>{{ item.nombre }}</td>
                                <td>{{ item.cantidad }}</td>
                                <td>{{ dinero(item.precio_unitario) }}</td>
                                <td>{{ dinero(item.subtotal) }}</td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="mp-totales">
                        <span class="mp-pago">Pago: {{ capitalizar(pedido.forma_pago) }}</span>
                        <span class="mp-total">Total: <strong>{{ dinero(pedido.total) }}</strong></span>
                    </div>
                </div>

                <!-- Línea de tiempo del avance (solo si no está cancelado) -->
                <ol v-if="pedido.estado !== 'cancelado'" class="mp-timeline">
                    <li v-for="(paso, i) in flujo" :key="paso"
                        :class="clasePaso(pedido, i)">
                        <span class="mp-paso-dot"></span>
                        <span class="mp-paso-label">{{ etiqueta(paso) }}</span>
                    </li>
                </ol>

                <!-- Historial de cambios de estado -->
                <details class="mp-historial">
                    <summary>Historial de estados ({{ pedido.historial.length }})</summary>
                    <ul>
                        <li v-for="(h, i) in pedido.historial" :key="i">
                            <span class="mp-hist-fecha">{{ h.fecha }}</span>
                            <span v-if="h.estado_anterior && h.estado_anterior !== h.estado_nuevo" class="mp-hist-cambio">
                                {{ etiqueta(h.estado_anterior) }} → <strong>{{ etiqueta(h.estado_nuevo) }}</strong>
                            </span>
                            <span v-else class="mp-hist-cambio"><strong>{{ etiqueta(h.estado_nuevo) }}</strong></span>
                            <span class="mp-hist-por" v-if="h.usuario">por {{ h.usuario }}</span>
                        </li>
                    </ul>
                </details>
            </article>
        </div>
    </div>
</template>

<script>
/**
 * MisPedidos.vue — seguimiento de pedidos del cliente.
 * - Consume SOLO la API /api/mis-pedidos (el servidor filtra por la sesión:
 *   un cliente nunca puede ver pedidos de otro usuario).
 * - Polling cada 15 segundos: el estado se actualiza solo cuando el
 *   administrador lo cambia, sin cerrar sesión ni recargar la página.
 */
export default {
    name: 'MisPedidos',
    props: {
        routes: { type: Object, required: true },
    },
    data() {
        return {
            pedidos: [],
            cargando: true,
            error: null,
            ultimaActualizacion: null,
            temporizador: null,
            flujo: ['pendiente', 'confirmado', 'en_preparacion', 'listo', 'en_camino', 'entregado'],
            etiquetas: {
                pendiente: 'Pendiente',
                confirmado: 'Confirmado',
                en_preparacion: 'En preparación',
                listo: 'Listo',
                en_camino: 'En camino',
                entregado: 'Entregado',
                cancelado: 'Cancelado',
            },
        };
    },
    mounted() {
        this.cargar();
        // Polling: actualización periódica sin recargar.
        this.temporizador = setInterval(() => this.cargar(true), 15000);
    },
    beforeUnmount() {
        if (this.temporizador) clearInterval(this.temporizador);
    },
    methods: {
        async cargar(silencioso = false) {
            if (!silencioso) this.cargando = true;
            try {
                const res = await fetch(this.routes.misPedidosApi, { headers: { Accept: 'application/json' } });
                if (res.status === 401) {
                    this.error = 'Tu sesión expiró.';
                    return;
                }
                const data = await res.json();
                this.pedidos = data.pedidos || [];
                this.ultimaActualizacion = data.actualizado_en;
                this.error = null;
            } catch (err) {
                if (!silencioso) this.error = 'No se pudieron cargar tus pedidos.';
            } finally {
                this.cargando = false;
            }
        },
        etiqueta(estado) {
            return this.etiquetas[estado] || estado;
        },
        clasePaso(pedido, indice) {
            const actual = this.flujo.indexOf(pedido.estado);
            return {
                'mp-paso-hecho': actual > indice,
                'mp-paso-actual': actual === indice,
                'mp-paso-futuro': actual === -1 || actual < indice,
            };
        },
        dinero(valor) {
            return '$' + Number(valor || 0).toLocaleString('es-CO', { maximumFractionDigits: 0 });
        },
        capitalizar(texto) {
            if (!texto) return '';
            return texto.charAt(0).toUpperCase() + texto.slice(1);
        },
    },
};
</script>
