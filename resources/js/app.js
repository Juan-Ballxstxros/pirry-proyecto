/**
 * El Pirry — punto de entrada Vite + Vue.
 *
 * Arquitectura (PASO 3):
 *  - Laravel sigue siendo el backend: auth, sesiones, roles, validaciones,
 *    precios, productos y pedidos. Vue SOLO consume las APIs existentes
 *    usando el CSRF que Blade imprime en el layout. La autorización la
 *    decide SIEMPRE el servidor (401 → modal de login existente).
 *  - MenuProductos.vue → menú por categorías + CRUD admin (monta en #vue-menu)
 *  - CarritoModal.vue  → modal del carrito + WhatsApp (monta en #vue-carrito)
 *  - Badge del carrito → Vanilla JS mínimo que conserva el HTML de Blade
 *  - toast.js (clásico) se conserva: toasts, pirryConfirm y confirmLogout.
 *
 * El JS clásico index.js/menu.js ya no se carga en esta página; los
 * archivos se conservan en public/js como referencia.
 */
import { createApp } from 'vue';
import MenuProductos from './components/MenuProductos.vue';
import CarritoModal from './components/CarritoModal.vue';

/* ---------- Contador del carrito (event bus + badge) ---------- */
function actualizarBadge(count) {
    ['navCartCount', 'countCarrito'].forEach((id) => {
        const el = document.getElementById(id);
        if (el) el.textContent = count || 0;
    });
}

function notificarCarrito(count) {
    window.dispatchEvent(new CustomEvent('pirry:cart', { detail: { count: count || 0 } }));
}

window.addEventListener('pirry:cart', (e) => {
    if (e.detail && typeof e.detail.count === 'number') actualizarBadge(e.detail.count);
});

(async () => {
    try {
        const res = await fetch(window.routes.carritoShow, { headers: { Accept: 'application/json' } });
        const data = await res.json();
        actualizarBadge(data.count);
    } catch (err) { /* silencioso: el badge se actualiza en la siguiente acción */ }
})();

/* ---------- Menú + CRUD admin ---------- */
const menuEl = document.getElementById('vue-menu');
if (menuEl) {
    createApp(MenuProductos, {
        isAdmin: !!window.isAdmin,
        routes: window.routes,
        csrf: window.csrfToken,
    }).mount(menuEl);
}

/* ---------- Modal del carrito ---------- */
const cartEl = document.getElementById('vue-carrito');
if (cartEl) {
    const carrito = createApp(CarritoModal, {
        routes: window.routes,
        csrf: window.csrfToken,
        notificarCarrito,
    }).mount(cartEl);

    // El layout llama verCarrito() desde la nav y el badge flotante
    window.verCarrito = () => carrito.abrir();
}

/* ---------- Modal de autenticación (HTML en Blade) ---------- */
// Cierre por clic en el fondo (mismo comportamiento que tenía index.js)
const authModal = document.getElementById('modalAuth');
if (authModal) {
    authModal.addEventListener('click', (e) => {
        if (e.target === authModal) authModal.style.display = 'none';
    });
}
