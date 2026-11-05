
<template>
    <div class="mp-page">
        <header class="mp-header">
            <div class="mp-brand">
                <span class="mp-brand-mark">P</span>
                <div>
                    <strong>PIRRY</strong>
                    <small>MI CUENTA</small>
                </div>
            </div>
            <span class="mp-header-label">MIS PEDIDOS</span>
        </header>

        <main class="mp-container">
            <section class="mp-intro">
                <div>
                    <span class="mp-eyebrow">TU EXPERIENCIA PIRRY</span>
                    <h1>Mis pedidos<span>.</span></h1>
                    <p>Consulta tus pedidos y sigue cada etapa de preparación.</p>
                </div>

                <button class="mp-refresh" type="button"
                    @click="cargar" :disabled="cargando">
                    <span :class="{ 'mp-spinning': cargando }">↻</span>
                    Actualizar
                </button>
            </section>

            <div v-if="ultimaActualizacion" class="mp-update-time">
                Última actualización: {{ ultimaActualizacion }}
            </div>

            <section v-if="cargando && pedidos.length === 0" class="mp-state">
                <div class="mp-loader"></div>
                <h3>Cargando tus pedidos</h3>
                <p>Estamos consultando tu información.</p>
            </section>

            <section v-else-if="error" class="mp-state mp-state-error">
                <div class="mp-state-icon">!</div>
                <h3>No pudimos cargar tus pedidos</h3>
                <p>{{ error }}</p>
                <a v-if="routes.login" :href="routes.login" class="mp-primary">
                    Iniciar sesión
                </a>
                <button v-else class="mp-primary" @click="cargar">
                    Intentar de nuevo
                </button>
            </section>

            <section v-else-if="pedidos.length === 0" class="mp-state">
                <div class="mp-empty-icon">▤</div>
                <h3>Aún no tienes pedidos</h3>
                <p>Cuando realices tu primer pedido, podrás seguirlo desde aquí.</p>
                <a :href="routes.home" class="mp-primary">Ver el menú</a>
            </section>

            <section v-else class="mp-orders">
                <article v-for="pedido in pedidos" :key="pedido.id"
                    class="mp-card">

                    <div class="mp-card-top">
                        <div>
                            <span class="mp-order-label">NÚMERO DE PEDIDO</span>
                            <h2>{{ pedido.codigo || ('#' + pedido.id) }}</h2>
                            <span class="mp-date">{{ pedido.fecha }}</span>
                        </div>
                        <span class="mp-badge"
                            :class="'mp-badge-' + pedido.estado">
                            {{ etiqueta(pedido.estado) }}
                        </span>
                    </div>

                    <div v-if="pedido.estado === 'cancelado'"
                        class="mp-cancelled">
                        <strong>Pedido cancelado</strong>
                        <p>Este pedido ya no se encuentra en preparación.</p>
                    </div>

                    <div v-else class="mp-progress-section">
                        <div class="mp-current-info">
                            <div>
                                <span class="mp-section-label">ESTADO ACTUAL</span>
                                <h3>{{ etiqueta(pedido.estado) }}</h3>
                                <p>{{ descripcionEstado(pedido.estado) }}</p>
                            </div>
                            <div class="mp-progress-number">
                                {{ progreso(pedido.estado) }}%
                            </div>
                        </div>

                        <div class="mp-progress-track">
                            <div class="mp-progress-fill"
                                :style="{ width: progreso(pedido.estado) + '%' }">
                            </div>
                        </div>

                        <div class="mp-timeline">
                            <div v-for="(paso, i) in flujo" :key="paso"
                                class="mp-timeline-item"
                                :class="clasePaso(pedido, i)">
                                <div class="mp-timeline-marker">
                                    <span v-if="indiceEstado(pedido.estado) > i">✓</span>
                                    <span v-else>{{ i + 1 }}</span>
                                </div>
                                <div class="mp-timeline-content">
                                    <strong>{{ etiqueta(paso) }}</strong>
                                    <small>{{ descripcionPaso(i) }}</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mp-divider"></div>

                    <section class="mp-products">
                        <div class="mp-section-heading">
                            <h3>Tu pedido</h3>
                            <span>{{ (pedido.productos || []).length }}
                                producto(s)</span>
                        </div>

                        <div v-if="!pedido.productos || !pedido.productos.length"
                            class="mp-no-products">
                            No hay productos para mostrar.
                        </div>

                        <div v-for="(item, i) in pedido.productos || []"
                            :key="i" class="mp-product">
                            <div class="mp-product-quantity">
                                {{ item.cantidad }}x
                            </div>
                            <div class="mp-product-info">
                                <strong>{{ item.nombre }}</strong>
                                <small>{{ dinero(item.precio_unitario) }} c/u</small>
                            </div>
                            <strong class="mp-product-total">
                                {{ dinero(item.subtotal) }}
                            </strong>
                        </div>
                    </section>

                    <div class="mp-payment">
                        <div>
                            <span>Forma de pago</span>
                            <strong>{{ capitalizar(pedido.forma_pago) }}</strong>
                        </div>
                        <div class="mp-total">
                            <span>Total del pedido</span>
                            <strong>{{ dinero(pedido.total) }}</strong>
                        </div>
                    </div>

                    <details class="mp-history">
                        <summary>
                            <span>Historial del pedido</span>
                            <span class="mp-history-count">
                                {{ (pedido.historial || []).length }}
                            </span>
                            <span class="mp-history-arrow">⌄</span>
                        </summary>

                        <div v-if="!(pedido.historial || []).length"
                            class="mp-history-empty">
                            Todavía no hay cambios registrados.
                        </div>

                        <div v-for="(h, i) in pedido.historial || []"
                            :key="i" class="mp-history-item">
                            <div class="mp-history-dot"></div>
                            <div>
                                <strong>
                                    {{ etiqueta(h.estado_nuevo) }}
                                </strong>
                                <p v-if="h.estado_anterior &&
                                    h.estado_anterior !== h.estado_nuevo">
                                    {{ etiqueta(h.estado_anterior) }}
                                    → {{ etiqueta(h.estado_nuevo) }}
                                </p>
                                <small>{{ h.fecha }}</small>
                                <small v-if="h.usuario">Registrado por {{ h.usuario }}</small>
                            </div>
                        </div>
                    </details>
                </article>
            </section>
        </main>

        <footer class="mp-footer">
            <strong>PIRRY</strong>
            <span>Gracias por elegirnos.</span>
        </footer>
    </div>
</template>

<script>
export default {
    name: 'MisPedidos',

    props: {
        routes: { type: Object, required: true }
    },

    data() {
        return {
            pedidos: [],
            cargando: false,
            error: '',
            ultimaActualizacion: '',
            temporizador: null,

            flujo: [
                'pendiente',
                'confirmado',
                'en_preparacion',
                'listo',
                'en_camino',
                'entregado'
            ],

            etiquetas: {
                pendiente: 'Pendiente',
                confirmado: 'Confirmado',
                en_preparacion: 'En preparación',
                listo: 'Listo',
                en_camino: 'En camino',
                entregado: 'Entregado',
                cancelado: 'Cancelado'
            }
        };
    },

    mounted() {
        this.cargar();
        this.temporizador = setInterval(() => {
            this.cargar(true);
        }, 15000);
    },

    beforeUnmount() {
        if (this.temporizador) {
            clearInterval(this.temporizador);
        }
    },

    methods: {
        async cargar(silencioso = false) {
            if (this.cargando) return;

            if (!silencioso) {
                this.cargando = true;
                this.error = '';
            }

            try {
                const respuesta = await fetch(this.routes.misPedidosApi, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin'
                });

                if (respuesta.status === 401 || respuesta.status === 403) {
                    this.error = 'Tu sesión ha expirado. Inicia sesión nuevamente.';
                    this.pedidos = [];
                    return;
                }

                if (!respuesta.ok) {
                    throw new Error('No fue posible consultar tus pedidos.');
                }

                const data = await respuesta.json();

                this.pedidos = Array.isArray(data.pedidos)
                    ? data.pedidos
                    : [];

                this.ultimaActualizacion = new Date().toLocaleTimeString(
                    'es-CO',
                    { hour: '2-digit', minute: '2-digit', second: '2-digit' }
                );

                this.error = '';
            } catch (e) {
                if (!silencioso) {
                    this.error = e.message || 'Ocurrió un error al cargar los pedidos.';
                }
            } finally {
                this.cargando = false;
            }
        },

        etiqueta(estado) {
            return this.etiquetas[estado] || this.capitalizar(estado);
        },

        indiceEstado(estado) {
            return this.flujo.indexOf(estado);
        },

        progreso(estado) {
            const indice = this.indiceEstado(estado);

            if (indice < 0) return 0;

            return Math.round((indice / (this.flujo.length - 1)) * 100);
        },

        clasePaso(pedido, indice) {
            const actual = this.indiceEstado(pedido.estado);

            return {
                'mp-step-done': actual > indice,
                'mp-step-active': actual === indice,
                'mp-step-future': actual < indice
            };
        },

        descripcionEstado(estado) {
            const textos = {
                pendiente: 'Estamos esperando la confirmación de tu pedido.',
                confirmado: 'Tu pedido fue confirmado.',
                en_preparacion: 'Estamos preparando tu comida.',
                listo: 'Tu pedido está listo.',
                en_camino: 'Tu pedido está en camino.',
                entregado: 'Tu pedido fue entregado. ¡Buen provecho!'
            };

            return textos[estado] || 'Consulta aquí el avance de tu pedido.';
        },

        descripcionPaso(indice) {
            const textos = [
                'Pedido recibido',
                'Pedido confirmado',
                'Preparando tu comida',
                'Pedido listo',
                'En camino',
                'Pedido entregado'
            ];

            return textos[indice] || '';
        },

        dinero(valor) {
            return '$' + Number(valor || 0).toLocaleString('es-CO');
        },

        capitalizar(valor) {
            if (!valor) return '';
            return String(valor)
                .replace(/_/g, ' ')
                .replace(/\b\w/g, letra => letra.toUpperCase());
        }
    }
};
</script>

<style scoped>
.mp-page {
    min-height: 100vh;
    background: #f5f5f5;
    color: #202020;
    font-family: Arial, Helvetica, sans-serif;
}

.mp-header {
    min-height: 76px;
    background: #151515;
    color: #fff;
    padding: 0 7%;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.mp-brand {
    display: flex;
    align-items: center;
    gap: 12px;
}

.mp-brand-mark {
    width: 42px;
    height: 42px;
    border-radius: 12px;
    display: grid;
    place-items: center;
    background: #f4b323;
    color: #151515;
    font-size: 25px;
    font-weight: 900;
}

.mp-brand strong,
.mp-brand small {
    display: block;
}

.mp-brand strong {
    font-size: 20px;
    letter-spacing: 3px;
}

.mp-brand small {
    color: #aaa;
    font-size: 9px;
    letter-spacing: 2px;
    margin-top: 3px;
}

.mp-header-label {
    color: #ddd;
    font-size: 12px;
    letter-spacing: 2px;
    font-weight: bold;
}

.mp-container {
    width: min(100% - 36px, 900px);
    margin: 0 auto;
    padding: 48px 0 70px;
}

.mp-intro {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 20px;
    margin-bottom: 8px;
}

.mp-eyebrow,
.mp-section-label,
.mp-order-label {
    color: #a16c00;
    font-size: 10px;
    letter-spacing: 2px;
    font-weight: 800;
}

.mp-intro h1 {
    margin: 9px 0;
    font-size: clamp(32px, 5vw, 43px);
    font-weight: 900;
    letter-spacing: -1.5px;
}

.mp-intro h1 span {
    color: #e7a51c;
}

.mp-intro p {
    margin: 0;
    color: #777;
    font-size: 14px;
    line-height: 1.6;
}

.mp-refresh {
    border: 1px solid #ddd;
    background: #fff;
    color: #222;
    border-radius: 9px;
    padding: 12px 17px;
    font-size: 13px;
    font-weight: bold;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: .2s;
}

.mp-refresh:hover {
    border-color: #e5a51b;
    background: #fffaf0;
}

.mp-refresh:disabled {
    opacity: .6;
    cursor: wait;
}

.mp-refresh span {
    font-size: 19px;
}

.mp-spinning {
    display: inline-block;
    animation: mp-spin 1s linear infinite;
}

@keyframes mp-spin {
    to { transform: rotate(360deg); }
}

.mp-update-time {
    color: #999;
    font-size: 11px;
    text-align: right;
    margin: 12px 2px 20px;
}

.mp-orders {
    display: grid;
    gap: 24px;
    margin-top: 25px;
}

.mp-card {
    background: #fff;
    border: 1px solid #e9e9e9;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 8px 28px rgba(0, 0, 0, .045);
}

.mp-card-top {
    padding: 25px 28px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 15px;
    background: #fff;
}

.mp-order-label {
    color: #999;
    font-size: 9px;
}

.mp-card-top h2 {
    margin: 7px 0 5px;
    font-size: 23px;
    font-weight: 900;
}

.mp-date {
    color: #999;
    font-size: 12px;
}

.mp-badge {
    padding: 9px 13px;
    border-radius: 30px;
    font-size: 11px;
    font-weight: 800;
    background: #fff4d8;
    color: #986400;
    white-space: nowrap;
}

.mp-badge-entregado {
    background: #e5f6eb;
    color: #247541;
}

.mp-badge-cancelado {
    background: #ffe9e9;
    color: #b12b2b;
}

.mp-badge-en_camino,
.mp-badge-listo {
    background: #e9f1ff;
    color: #285a9c;
}

.mp-progress-section {
    margin: 0 28px;
    padding: 23px 0 20px;
    border-top: 1px solid #eee;
}

.mp-current-info {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 15px;
}

.mp-current-info h3 {
    margin: 8px 0 5px;
    font-size: 20px;
    font-weight: 900;
}

.mp-current-info p {
    margin: 0;
    color: #888;
    font-size: 12px;
    line-height: 1.5;
}

.mp-progress-number {
    font-size: 25px;
    font-weight: 900;
    color: #d4940d;
}

.mp-progress-track {
    height: 8px;
    border-radius: 20px;
    background: #eee;
    overflow: hidden;
    margin: 22px 0 27px;
}

.mp-progress-fill {
    height: 100%;
    background: linear-gradient(90deg, #e5a51b, #ffd36a);
    border-radius: 20px;
    transition: width .5s ease;
}

.mp-timeline {
    position: relative;
    display: grid;
    gap: 0;
}

.mp-timeline::before {
    content: "";
    position: absolute;
    top: 17px;
    bottom: 17px;
    left: 15px;
    width: 2px;
    background: #e8e8e8;
}

.mp-timeline-item {
    position: relative;
    display: flex;
    align-items: flex-start;
    gap: 14px;
    min-height: 64px;
}

.mp-timeline-marker {
    width: 32px;
    height: 32px;
    flex: 0 0 32px;
    border-radius: 50%;
    border: 2px solid #ddd;
    background: #fff;
    display: grid;
    place-items: center;
    color: #999;
    font-size: 12px;
    font-weight: 800;
    z-index: 1;
}

.mp-timeline-content {
    padding-top: 2px;
}

.mp-timeline-content strong,
.mp-timeline-content small {
    display: block;
}

.mp-timeline-content strong {
    font-size: 13px;
    color: #999;
}

.mp-timeline-content small {
    margin-top: 5px;
    font-size: 11px;
    color: #aaa;
}

.mp-step-done .mp-timeline-marker {
    background: #e9f6ed;
    border-color: #4a9d61;
    color: #34814b;
}

.mp-step-done .mp-timeline-content strong {
    color: #444;
}

.mp-step-active .mp-timeline-marker {
    background: #e5a51b;
    border-color: #e5a51b;
    color: #171717;
    box-shadow: 0 0 0 5px #fff3d8;
}

.mp-step-active .mp-timeline-content strong {
    color: #a16c00;
    font-weight: 900;
}

.mp-divider {
    height: 1px;
    background: #eee;
    margin: 0 28px;
}

.mp-products {
    padding: 24px 28px 15px;
}

.mp-section-heading {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
}

.mp-section-heading h3 {
    margin: 0;
    font-size: 16px;
    font-weight: 900;
}

.mp-section-heading span {
    font-size: 11px;
    color: #999;
}

.mp-product {
    display: flex;
    align-items: center;
    gap: 13px;
    padding: 13px 0;
    border-bottom: 1px solid #f1f1f1;
}

.mp-product-quantity {
    width: 35px;
    height: 35px;
    flex: 0 0 35px;
    border-radius: 9px;
    display: grid;
    place-items: center;
    background: #fff5dc;
    color: #a16c00;
    font-size: 12px;
    font-weight: 900;
}

.mp-product-info {
    flex: 1;
    min-width: 0;
}

.mp-product-info strong,
.mp-product-info small {
    display: block;
}

.mp-product-info strong {
    font-size: 13px;
}

.mp-product-info small {
    color: #999;
    margin-top: 5px;
    font-size: 11px;
}

.mp-product-total {
    font-size: 13px;
    white-space: nowrap;
}

.mp-no-products {
    color: #999;
    font-size: 12px;
}

.mp-payment {
    margin: 0 28px 25px;
    padding: 18px;
    border-radius: 11px;
    background: #f8f8f8;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 15px;
}

.mp-payment span,
.mp-payment strong {
    display: block;
}

.mp-payment span {
    color: #888;
    font-size: 11px;
    margin-bottom: 6px;
}

.mp-payment strong {
    font-size: 13px;
}

.mp-payment .mp-total {
    text-align: right;
}

.mp-payment .mp-total strong {
    font-size: 21px;
    font-weight: 900;
    color: #a16c00;
}

.mp-history {
    border-top: 1px solid #eee;
    padding: 0 28px;
}

.mp-history summary {
    list-style: none;
    cursor: pointer;
    min-height: 58px;
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 12px;
    font-weight: 800;
}

.mp-history summary::-webkit-details-marker {
    display: none;
}

.mp-history-count {
    background: #f2f2f2;
    color: #777;
    border-radius: 20px;
    padding: 3px 8px;
    font-size: 10px;
}

.mp-history-arrow {
    margin-left: auto;
    font-size: 20px;
    color: #999;
    transition: transform .2s;
}

.mp-history[open] .mp-history-arrow {
    transform: rotate(180deg);
}

.mp-history-item {
    display: flex;
    gap: 12px;
    padding: 0 0 17px;
}

.mp-history-dot {
    width: 9px;
    height: 9px;
    margin-top: 4px;
    border-radius: 50%;
    background: #e5a51b;
    flex: 0 0 9px;
}

.mp-history-item strong {
    font-size: 12px;
}

.mp-history-item p {
    margin: 5px 0;
    color: #777;
    font-size: 11px;
}

.mp-history-item small {
    display: block;
    margin-top: 5px;
    color: #999;
    font-size: 10px;
}

.mp-history-empty {
    padding: 0 0 20px;
    color: #999;
    font-size: 12px;
}

.mp-cancelled {
    margin: 0 28px 24px;
    padding: 17px;
    border-radius: 10px;
    background: #fff0f0;
    border: 1px solid #ffdada;
    color: #a62b2b;
}

.mp-cancelled strong {
    font-size: 14px;
}

.mp-cancelled p {
    margin: 6px 0 0;
    font-size: 12px;
}

.mp-state {
    background: #fff;
    border: 1px solid #eee;
    border-radius: 15px;
    padding: 55px 20px;
    margin-top: 30px;
    text-align: center;
}

.mp-state h3 {
    margin: 15px 0 8px;
    font-size: 19px;
}

.mp-state p {
    color: #888;
    font-size: 13px;
    line-height: 1.6;
}

.mp-loader {
    width: 35px;
    height: 35px;
    border: 3px solid #eee;
    border-top-color: #e5a51b;
    border-radius: 50%;
    margin: 0 auto;
    animation: mp-spin .8s linear infinite;
}

.mp-empty-icon,
.mp-state-icon {
    width: 48px;
    height: 48px;
    margin: 0 auto;
    border-radius: 50%;
    display: grid;
    place-items: center;
    background: #fff3d8;
    color: #a16c00;
    font-size: 24px;
    font-weight: 900;
}

.mp-state-error .mp-state-icon {
    background: #ffe9e9;
    color: #b12b2b;
}

.mp-primary {
    display: inline-block;
    border: 0;
    border-radius: 8px;
    background: #171717;
    color: #fff;
    padding: 13px 20px;
    margin-top: 13px;
    font-size: 12px;
    font-weight: 800;
    text-decoration: none;
    cursor: pointer;
}

.mp-primary:hover {
    background: #d4940d;
    color: #171717;
}

.mp-footer {
    min-height: 75px;
    background: #151515;
    color: #aaa;
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 13px;
    font-size: 11px;
}

.mp-footer strong {
    color: #f4b323;
    letter-spacing: 3px;
    font-size: 15px;
}

@media (max-width: 600px) {
    .mp-header {
        min-height: 65px;
        padding: 0 18px;
    }

    .mp-header-label {
        font-size: 9px;
    }

    .mp-container {
        width: calc(100% - 24px);
        padding: 30px 0 45px;
    }

    .mp-intro {
        align-items: flex-start;
        flex-direction: column;
    }

    .mp-refresh {
        align-self: flex-start;
    }

    .mp-card-top {
        padding: 20px 17px;
    }

    .mp-card-top h2 {
        font-size: 19px;
    }

    .mp-badge {
        font-size: 9px;
        padding: 8px 10px;
    }

    .mp-progress-section {
        margin: 0 17px;
    }

    .mp-divider {
        margin: 0 17px;
    }

    .mp-products {
        padding: 20px 17px 12px;
    }

    .mp-payment {
        margin: 0 17px 20px;
        padding: 14px;
    }

    .mp-history {
        padding: 0 17px;
    }

    .mp-cancelled {
        margin: 0 17px 20px;
    }

    .mp-payment .mp-total strong {
        font-size: 17px;
    }

    .mp-product-total {
        font-size: 12px;
    }
}

@media (max-width: 380px) {
    .mp-payment {
        align-items: flex-start;
        flex-direction: column;
    }

    .mp-payment .mp-total {
        text-align: left;
    }
}
</style>
