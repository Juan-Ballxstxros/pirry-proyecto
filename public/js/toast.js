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

    window.showToast = function (message, type = 'success', duration = 4200, title = null) {
        const container = ensureContainer();

        const toast = document.createElement('div');
        toast.className = 'pirry-toast pirry-toast--' + type;
        toast.setAttribute('role', 'status');
        toast.innerHTML =
            '<div class="pirry-toast__icon">' + (ICONS[type] || ICONS.info) + '</div>' +
            '<div class="pirry-toast__body">' +
                '<div class="pirry-toast__title">' + (title || TITLES[type] || TITLES.info) + '</div>' +
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

    /* ---------- Modal de confirmación reutilizable ---------- */
    let overlay = null;
    let confirmState = null; // { resolve, form }

    function buildModal() {
        overlay = document.createElement('div');
        overlay.className = 'pirry-confirm-overlay';
        overlay.innerHTML =
            '<div class="pirry-confirm" role="dialog" aria-modal="true" aria-labelledby="pirryConfirmTitle">' +
                '<div class="pirry-confirm__icon">👋</div>' +
                '<h3 class="pirry-confirm__title" id="pirryConfirmTitle"></h3>' +
                '<p class="pirry-confirm__text"></p>' +
                '<div class="pirry-confirm__actions">' +
                    '<button type="button" class="pirry-confirm__btn pirry-confirm__btn--cancel">Cancelar</button>' +
                    '<button type="button" class="pirry-confirm__btn pirry-confirm__btn--ok">Aceptar</button>' +
                '</div>' +
            '</div>';

        overlay.querySelector('.pirry-confirm__btn--cancel').addEventListener('click', () => closeConfirm(false));
        overlay.querySelector('.pirry-confirm__btn--ok').addEventListener('click', () => closeConfirm(true));

        overlay.addEventListener('click', (e) => {
            if (e.target === overlay) closeConfirm(false);
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && overlay.classList.contains('show')) closeConfirm(false);
        });

        document.body.appendChild(overlay);
    }

    function closeConfirm(resultado) {
        if (!overlay) return;
        overlay.classList.remove('show');

        const state = confirmState;
        confirmState = null;
        if (!state) return;

        if (resultado && state.form) {
            state.form.submit(); // envía el formulario POST de logout (con su @csrf)
        }
        if (state.resolve) {
            state.resolve(!!resultado);
        }
    }

    /**
     * Modal de confirmación reutilizable (mismo estilo en todo el proyecto).
     * Opciones: { title, text, icon, okText, cancelText, danger, form }
     * Devuelve una Promesa: true = confirmó, false = canceló.
     */
    window.pirryConfirm = function (options = {}) {
        return new Promise((resolve) => {
            if (!overlay) buildModal();

            // Si había una confirmación abierta, se cierra como cancelada.
            if (confirmState && confirmState.resolve) {
                confirmState.resolve(false);
            }

            overlay.querySelector('.pirry-confirm__icon').textContent = options.icon || '⚠️';
            overlay.querySelector('.pirry-confirm__title').textContent = options.title || '¿Estás seguro?';
            overlay.querySelector('.pirry-confirm__text').textContent = options.text || '';
            overlay.querySelector('.pirry-confirm__btn--ok').textContent = options.okText || 'Aceptar';
            overlay.querySelector('.pirry-confirm__btn--cancel').textContent = options.cancelText || 'Cancelar';
            overlay.querySelector('.pirry-confirm__btn--ok').classList.toggle('pirry-confirm__btn--danger', !!options.danger);

            confirmState = { resolve, form: options.form || null };
            requestAnimationFrame(() => overlay.classList.add('show'));
        });
    };

    // Se usa como onsubmit del formulario de logout: devuelve false siempre,
    // así el envío nativo se cancela y se muestra primero la confirmación.
    // (Mismo comportamiento de siempre, ahora sobre el modal generalizado.)
    window.confirmLogout = function (form) {
        window.pirryConfirm({
            icon: '👋',
            title: '¿Cerrar sesión?',
            text: '¿Estás seguro de que deseas salir de tu cuenta?',
            okText: 'Sí, cerrar sesión',
            form,
        });
        return false;
    };

    /* ---------- Mensajes flash enviados desde Laravel ---------- */
    document.addEventListener('DOMContentLoaded', () => {
        const flashes = window.flashMessages || [];
        flashes.forEach((f) => showToast(f.message, f.type || 'success', f.duration || 4200, f.title || null));
        window.flashMessages = [];
    });
})();
