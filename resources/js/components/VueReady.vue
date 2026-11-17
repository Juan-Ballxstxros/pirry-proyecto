<template>
    <div class="vue-ready-chip" aria-live="polite">
        <span class="vue-ready-dot" aria-hidden="true"></span>
        <strong>Vue 3 activo</strong>
        <span class="vue-ready-sep">·</span>
        <span v-if="cargando">conectando con Laravel…</span>
        <span v-else>Carrito: {{ count }} {{ count === 1 ? 'producto' : 'productos' }}</span>
    </div>
</template>

<script>
/**
 * PASO 2 — Componente de prueba (mínimo y temporal).
 * Demuestra la integración real Vue + Vite + API de Laravel:
 * lee el estado del carrito desde GET /carrito (endpoint existente).
 * Se reemplazará por los componentes reales en el PASO 3.
 */
export default {
    name: 'VueReady',
    data() {
        return {
            count: 0,
            cargando: true,
        };
    },
    mounted() {
        this.actualizar();
        window.addEventListener('pirry:cart', this.alCambiarCarrito);
    },
    beforeUnmount() {
        window.removeEventListener('pirry:cart', this.alCambiarCarrito);
    },
    methods: {
        alCambiarCarrito(evento) {
            if (evento.detail && typeof evento.detail.count === 'number') {
                this.count = evento.detail.count;
                this.cargando = false;
            }
        },
        async actualizar() {
            try {
                const res = await fetch(window.routes.carritoShow, {
                    headers: { Accept: 'application/json' },
                });
                const data = await res.json();
                this.count = data.count || 0;
            } catch (err) {
                /* Silencioso: es solo una prueba visual */
            } finally {
                this.cargando = false;
            }
        },
    },
};
</script>

<style scoped>
.vue-ready-chip {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    margin-top: 10px;
    padding: 6px 14px;
    background: #fff7ed;
    border: 1px dashed #f59e0b;
    border-radius: 999px;
    font-size: 13px;
    color: #92400e;
}
.vue-ready-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #10b981;
    flex-shrink: 0;
}
.vue-ready-sep {
    opacity: 0.5;
}
</style>
