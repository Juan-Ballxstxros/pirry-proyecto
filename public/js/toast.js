/* ============================================================
   SISTEMA DE ALERTAS EL PIRRY — Toasts + Modal de confirmación
   ============================================================ */
(function () {
    'use strict';

    const ICONS = { success: '✔', error: '✕', info: 'ℹ' };
    const TITLES = { success: '¡Éxito!', error: '¡Ups!', info: 'Aviso' };

    /* ---------- Toasts ---------- */
    function ensureContainer() {
        let container = document.querySelector('.pirry-toasts');
        if (!container) {
            container = document.createElement('div');
            container.className = 'pirry-toasts';
            document.body.appendChild(container);
        }
        return container;
    }

    window.showToast = function (message, type = 'success', duration = 4200) {
        const container = ensureContainer();

        const toast = document.createElement('div');
        toast.className = 'pirry-toast pirry-toast--' + type;
        toast.setAttribute('role', 'status');
        toast.innerHTML =
            '<div class="pirry-toast__icon">' + (ICONS[type] || ICONS.info) + '</div>' +
            '<div class="pirry-toast__body">' +
                '<div class="pirry-toast__title">' + (TITLES[type] || TITLES.info) + '</div>' +
                '<div class="pirry-toast__msg"></div>' +
            '</div>' +
            '<button type="button" class="pirry-toast__close" aria-label="Cerrar">✕</button>' +
            '<div class="pirry-toast__progress"></div>';

        toast.querySelector('.pirry-toast__msg').textContent = message;

        const progress = toast.querySelector('.pirry-toast__progress');
        progress.style.animationDuration = duration + 'ms';

        container.appendChild(toast);

        // Pequeño doble frame para que la transición de entrada se aplique.
        requestAnimationFrame(() => {
            requestAnimationFrame(() => toast.classList.add('show'));
        });

        const timer = setTimeout(dismiss, duration);

        function dismiss() {
            clearTimeout(timer);
            toast.classList.remove('show');
            toast.classList.add('hide');
            setTimeout(() => toast.remove(), 400);
        }

        toast.querySelector('.pirry-toast__close').addEventListener('click', dismiss);

        return toast;
    };

    /* ---------- Modal de confirmación para cerrar sesión ---------- */
    let overlay = null;
    let pendingForm = null;

    function buildModal() {
        overlay = document.createElement('div');
        overlay.className = 'pirry-confirm-overlay';
        overlay.innerHTML =
            '<div class="pirry-confirm" role="dialog" aria-modal="true" aria-labelledby="pirryConfirmTitle">' +
                '<div class="pirry-confirm__icon">👋</div>' +
                '<h3 class="pirry-confirm__title" id="pirryConfirmTitle">¿Cerrar sesión?</h3>' +
                '<p class="pirry-confirm__text">¿Estás seguro de que deseas salir de tu cuenta?</p>' +
                '<div class="pirry-confirm__actions">' +
                    '<button type="button" class="pirry-confirm__btn pirry-confirm__btn--cancel">Cancelar</button>' +
                    '<button type="button" class="pirry-confirm__btn pirry-confirm__btn--ok">Sí, cerrar sesión</button>' +
                '</div>' +
            '</div>';

        overlay.querySelector('.pirry-confirm__btn--cancel').addEventListener('click', closeConfirm);
        overlay.querySelector('.pirry-confirm__btn--ok').addEventListener('click', () => {
            const form = pendingForm;
            closeConfirm();
            if (form) {
                form.submit(); // envía el formulario POST de logout (con su @csrf)
            }
        });

        overlay.addEventListener('click', (e) => {
            if (e.target === overlay) closeConfirm();
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && overlay.classList.contains('show')) closeConfirm();
        });

        document.body.appendChild(overlay);
    }

    // Se usa como onsubmit del formulario de logout: devuelve false siempre,
    // así el envío nativo se cancela y se muestra primero la confirmación.
    window.confirmLogout = function (form) {
        pendingForm = form || null;
        if (!overlay) buildModal();
        requestAnimationFrame(() => overlay.classList.add('show'));
        return false;
    };

    function closeConfirm() {
        if (!overlay) return;
        overlay.classList.remove('show');
        pendingForm = null;
    }

    /* ---------- Mensajes flash enviados desde Laravel ---------- */
    document.addEventListener('DOMContentLoaded', () => {
        const flashes = window.flashMessages || [];
        flashes.forEach((f) => showToast(f.message, f.type || 'success', f.duration || 4200));
        window.flashMessages = [];
    });
})();
