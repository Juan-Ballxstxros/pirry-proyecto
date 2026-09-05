<template>
    <div>
        <div class="menu-busqueda">
            <input type="text" id="busquedaMenu" v-model="busqueda"
                   placeholder="🔍 Buscar producto por nombre...">
        </div>

        <div class="menu-filtros">
            <button type="button" v-for="s in secciones" :key="s.clave"
                    class="menu-pill" :class="{ activa: filtroActivo === s.clave }"
                    @click="filtroActivo = s.clave">
                <span class="menu-pill-emoji" aria-hidden="true">{{ s.emoji }}</span>
                {{ s.nombre }}
                <span class="menu-pill-count">{{ s.cantidad }}</span>
            </button>
        </div>

        <div class="menu-grid">
            <p v-if="cargando" class="loading-msg">Cargando productos...</p>

            <p v-else-if="error" class="menu-sin-resultados">No se pudo cargar el menú.
                <button type="button" class="btn-item-reintentar" @click="cargar()">Reintentar</button>
            </p>

            <p v-else-if="listaFiltrada.length === 0" class="menu-sin-resultados">
                No hay productos en esta categoría todavía. ¡Vuelve pronto! 🍽️
            </p>

            <template v-else>
                <article v-for="p in listaFiltrada" :key="p.id"
                         class="menu-card" :class="{ 'menu-card-agotado': !estaDisponible(p) }">
                    <img v-if="p.imagen" class="menu-card-img" :src="p.imagen" :alt="p.nombre" loading="lazy">
                    <div v-else class="menu-card-icono" aria-hidden="true">{{ emojiDe(p.categoria) }}</div>
                    <h3 class="menu-card-nombre">{{ p.nombre }}</h3>
                    <p v-if="p.descripcion" class="menu-card-desc">{{ p.descripcion }}</p>
                    <button class="btn-card-add" :disabled="!estaDisponible(p)" @click="agregarAlCarrito(p)">
                        {{ estaDisponible(p) ? '🛒 Agregar al carrito' : 'No disponible' }}
                    </button>
                    <div v-if="isAdmin" class="admin-controls">
                        <button class="btn-edit-small" @click="abrirEditar(p)">✏️ Editar</button>
                        <button class="btn-del-small" @click="eliminarProducto(p)">🗑️ Eliminar</button>
                    </div>
                </article>
            </template>
        </div>

        <div v-if="isAdmin" class="menu-admin-bar">
            <button class="btn-edit" @click="abrirCrear()">➕ Nuevo Producto</button>
        </div>

                <!-- Modal de producto (solo administrador). Mismo HTML que tenía
             store/index.blade.php; la lógica viene de index.js. -->
        <div id="modalProducto" class="modal" :style="{ display: modalAbierto ? 'flex' : 'none' }" @click.self="cerrarModal()">
            <div class="modal-content">
                <h3>{{ modalTitulo }}</h3>

                <label>Nombre *</label>
                <input type="text" id="prodNombre" v-model="form.nombre" required>

                <label>Descripción</label>
                <textarea id="prodDescripcion" v-model="form.descripcion"></textarea>

                <label>Imagen del producto</label>
                <input type="file" id="prodImagen" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" @change="alCambiarImagen">
                <div class="imagen-preview">
                    <p class="imagen-vacia" v-show="!previewUrl">Sin imagen</p>
                    <img id="imagenPreviewImg" v-if="previewUrl" :src="previewUrl" alt="Vista previa de la imagen del producto">
                </div>
                <button type="button" class="btn-eliminar-imagen" v-if="imagenActual && !archivoImagen" @click="eliminarImagenProducto">🗑️ Eliminar imagen</button>

                <label>Precio ($) — opcional, no se muestra al cliente</label>
                <input type="number" id="prodPrecio" v-model="form.precio" step="0.01" min="0">

                <label>Categoría *</label>
                <select id="prodCategoria" v-model="form.id_categoria" required>
                    <option v-for="c in opcionesCategoria" :key="c.id" :value="c.id">{{ c.nombre }}</option>
                </select>

                <label class="checkbox-label">
                    <input type="checkbox" id="prodDisponible" v-model="form.disponible">
                    Disponible para venta
                </label>

                <p v-if="form.id" class="id-info"><strong>ID del producto:</strong> {{ form.id }} (no modificable)</p>

                <div class="modal-buttons">
                    <button type="button" class="btn-cancel" @click="cerrarModal()">Cancelar</button>
                    <button type="button" class="btn-save" :disabled="guardando" @click="guardarProducto">💾 Guardar Cambios</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
/**
 * Menú de productos + CRUD de administrador.
 * Portado 1:1 desde public/js/menu.js + public/js/index.js (modales).
 * - Mismas clases CSS (menu.css/index.css) y mismos endpoints de Laravel.
 * - Los precios y permisos los valida Laravel; Vue solo muestra la interfaz.
 */
export default {
    name: 'MenuProductos',
    props: {
        isAdmin: { type: Boolean, default: false },
        routes: { type: Object, required: true },
        csrf: { type: String, required: true },
    },
    data() {
        return {
            productos: [],
            categoriasDB: [],
            filtroActivo: 'todos',
            busqueda: '',
            cargando: true,
            error: false,
            MENU_CATEGORIAS_CONFIG: [
                { nombre: 'Salchipapas', emoji: '🍟' },
                { nombre: 'Perros', emoji: '🌭' },
                { nombre: 'Perras', emoji: '🌭' },
                { nombre: 'Bebidas', emoji: '🥤' },
                { nombre: 'Picadas', emoji: '🥩' },
                { nombre: 'Hamburguesas', emoji: '🍔' },
                { nombre: 'Tociqueso', emoji: '🧀' },
                { nombre: 'Pirrypollo', emoji: '🍗' },
                { nombre: 'Adiciones', emoji: '🥓' },
            ],
            modalAbierto: false,
            modalTitulo: 'Nuevo Producto',
            form: { id: null, nombre: '', descripcion: '', precio: '', id_categoria: '', disponible: true },
            imagenActual: null,
            archivoImagen: null,
            previewUrl: null,
            previewEsBlob: false,
            guardando: false,
        };
    },
    computed: {
        secciones() {
            const grupos = {};
            (this.productos || []).forEach((p) => {
                const clave = this.normalizarTexto(p.categoria);
                (grupos[clave] = grupos[clave] || []).push(p);
            });
            const secciones = this.MENU_CATEGORIAS_CONFIG.map((cfg) => {
                const clave = this.normalizarTexto(cfg.nombre);
                return { ...cfg, clave, cantidad: (grupos[clave] || []).length };
            });
            const usadas = secciones.map((s) => s.clave);
            Object.keys(grupos).forEach((clave) => {
                if (!usadas.includes(clave)) {
                    secciones.push({
                        nombre: grupos[clave][0].categoria || clave,
                        emoji: '🍽️',
                        clave,
                        cantidad: grupos[clave].length,
                    });
                }
            });
            return secciones;
        },
        listaFiltrada() {
            let lista = this.productos || [];
            if (this.filtroActivo !== 'todos') {
                lista = lista.filter((p) => this.normalizarTexto(p.categoria) === this.filtroActivo);
            }
            const q = this.normalizarTexto(this.busqueda);
            if (q) {
                lista = lista.filter((p) => this.normalizarTexto((p.nombre || '') + ' ' + (p.descripcion || '')).includes(q));
            }
            return lista;
        },
        opcionesCategoria() {
            if (this.categoriasDB.length) return this.categoriasDB;
            const vistos = {};
            const lista = [];
            (this.productos || []).forEach((p) => {
                if (p.id_categoria && !vistos[p.id_categoria]) {
                    vistos[p.id_categoria] = true;
                    lista.push({ id: p.id_categoria, nombre: p.categoria });
                }
            });
            return lista;
        },
    },
    async mounted() {
        await this.cargar();
        if (this.isAdmin) this.cargarCategorias();
    },
    methods: {
        normalizarTexto(texto) {
            return String(texto || '').toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '').trim();
        },
        emojiDe(nombre) {
            const clave = this.normalizarTexto(nombre);
            const fija = this.MENU_CATEGORIAS_CONFIG.find((c) => this.normalizarTexto(c.nombre) === clave);
            return fija ? fija.emoji : '🍽️';
        },
        estaDisponible(p) {
            return p.disponible !== false;
        },
        async cargar() {
            this.cargando = true;
            this.error = false;
            try {
                const res = await fetch(this.routes.productos, { headers: { Accept: 'application/json' } });
                if (!res.ok) throw new Error('http ' + res.status);
                this.productos = await res.json();
            } catch (err) {
                this.error = true;
            } finally {
                this.cargando = false;
            }
        },
        async cargarCategorias() {
            try {
                const res = await fetch(this.routes.productos + '/categorias', { headers: { Accept: 'application/json' } });
                this.categoriasDB = await res.json();
            } catch (err) { /* el modal usa las categorías derivadas de productos */ }
        },
        async agregarAlCarrito(p) {
            try {
                const res = await fetch(this.routes.carritoAdd, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': this.csrf, Accept: 'application/json' },
                    body: JSON.stringify({ id: p.id }),
                });
                const data = await res.json();
                if (data.exito) {
                    window.dispatchEvent(new CustomEvent('pirry:cart', { detail: { count: data.count } }));
                    this.mostrarNotificacion('Producto agregado al carrito');
                } else {
                    this.toast(data.error || 'Error al agregar producto');
                }
            } catch (err) {
                this.toast('Error de conexión');
            }
        },
        mostrarNotificacion(msg) {
            const notif = document.createElement('div');
            notif.className = 'notif-toast';
            notif.textContent = msg;
            document.body.appendChild(notif);
            setTimeout(() => notif.remove(), 2500);
        },
        toast(mensaje, tipo, titulo) {
            if (typeof window.showToast === 'function') {
                window.showToast(mensaje, tipo || 'error', 4200, titulo || null);
            }
        },
        abrirCrear() {
            this.modalTitulo = 'Nuevo Producto';
            this.form = { id: null, nombre: '', descripcion: '', precio: '', id_categoria: this.opcionesCategoria.length ? this.opcionesCategoria[0].id : '', disponible: true };
            this.resetearImagen(null);
            this.modalAbierto = true;
        },
        abrirEditar(p) {
            this.modalTitulo = 'Editar Producto';
            this.form = { id: p.id, nombre: p.nombre, descripcion: p.descripcion || '', precio: p.precio, id_categoria: p.id_categoria, disponible: p.disponible !== false };
            this.resetearImagen(p.imagen || null);
            this.modalAbierto = true;
        },
        cerrarModal() {
            this.modalAbierto = false;
        },
        resetearImagen(imagenActual) {
            if (this.previewEsBlob && this.previewUrl) URL.revokeObjectURL(this.previewUrl);
            this.archivoImagen = null;
            this.previewEsBlob = false;
            this.imagenActual = imagenActual;
            this.previewUrl = imagenActual;
        },
        alCambiarImagen(evento) {
            const archivo = evento.target.files && evento.target.files[0];
            if (!archivo) return;
            const tiposPermitidos = ['image/jpeg', 'image/png', 'image/webp'];
            if (!tiposPermitidos.includes(archivo.type)) {
                this.toast('Formato no permitido. Usa JPG, JPEG, PNG o WEBP.');
                evento.target.value = '';
                return;
            }
            if (archivo.size > 2 * 1024 * 1024) {
                this.toast('La imagen no debe pesar más de 2 MB.');
                evento.target.value = '';
                return;
            }
            if (this.previewEsBlob && this.previewUrl) URL.revokeObjectURL(this.previewUrl);
            this.archivoImagen = archivo;
            this.previewUrl = URL.createObjectURL(archivo);
            this.previewEsBlob = true;
        },
        async eliminarImagenProducto() {
            if (!this.form.id) return;
            const confirmado = await window.pirryConfirm({
                icon: '🖼️',
                title: '¿Eliminar la imagen?',
                text: 'El producto quedará sin imagen. Esta acción no se puede deshacer.',
                okText: 'Eliminar imagen',
                cancelText: 'Cancelar',
                danger: true,
            });
            if (!confirmado) return;
            try {
                const res = await fetch(this.routes.productos + '/' + this.form.id + '/imagen', {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': this.csrf, Accept: 'application/json' },
                });
                const data = await res.json();
                if (data.exito) {
                    await this.cargar();
                    this.resetearImagen(null);
                    this.toast('La imagen se eliminó correctamente.', 'success', '¡Imagen eliminada!');
                } else {
                    this.toast(data.error || 'Error al eliminar la imagen.');
                }
            } catch (err) {
                this.toast('Error de conexión al eliminar la imagen.');
            }
        },
        async guardarProducto() {
            const precio = this.form.precio === '' ? 0 : parseFloat(this.form.precio);
            if (!this.form.nombre || isNaN(precio) || precio < 0 || !this.form.id_categoria) {
                this.toast('Complete los campos obligatorios.');
                return;
            }
            this.guardando = true;
            try {
                const formData = new FormData();
                formData.append('nombre', this.form.nombre);
                formData.append('descripcion', this.form.descripcion);
                formData.append('precio', String(precio));
                formData.append('id_categoria', this.form.id_categoria);
                formData.append('disponible', this.form.disponible ? '1' : '0');
                // Si no se eligió archivo, "imagen" no viaja: la imagen se conserva
                if (this.archivoImagen) formData.append('imagen', this.archivoImagen);
                // Method spoofing de Laravel: PHP NO llena $_POST/$_FILES en una
                // petición PUT real con multipart/form-data, por lo que el
                // update() recibía los campos vacíos y la validación "required"
                // fallaba (422) aunque el formulario estuviera completo. Se envía
                // POST con _method=PUT y el router de Laravel lo trata como PUT.
                if (this.form.id) formData.append('_method', 'PUT');
                const url = this.form.id ? this.routes.productos + '/' + this.form.id : this.routes.productos;
                const res = await fetch(url, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': this.csrf, Accept: 'application/json' },
                    body: formData,
                });
                const data = await res.json();
                if (res.status === 422) {
                    const errores = data.errors || {};
                    const primer = (Object.values(errores)[0] && Object.values(errores)[0][0]) || data.message || 'Datos inválidos.';
                    this.toast(primer);
                    return;
                }
                if (data.exito) {
                    this.cerrarModal();
                    await this.cargar();
                    if (this.isAdmin) this.cargarCategorias();
                    this.toast('Los cambios se guardaron correctamente.', 'success', '¡' + data.exito + '!');
                } else {
                    this.toast(data.error || 'Error al guardar el producto.');
                }
            } catch (err) {
                this.toast('Error de conexión al guardar el producto.');
            } finally {
                this.guardando = false;
            }
        },
        async eliminarProducto(p) {
            const confirmado = await window.pirryConfirm({
                icon: '🗑️',
                title: '¿Estás seguro?',
                text: '¿Quieres eliminar este producto? Esta acción no se puede deshacer.',
                okText: 'Eliminar',
                cancelText: 'Cancelar',
                danger: true,
            });
            if (!confirmado) return;
            try {
                const res = await fetch(this.routes.productos + '/' + p.id, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': this.csrf, Accept: 'application/json' },
                });
                const data = await res.json();
                if (data.exito) {
                    await this.cargar();
                    this.toast('El producto se eliminó correctamente.', 'success', '¡Producto eliminado!');
                } else {
                    this.toast(data.error || 'Error al eliminar el producto.');
                }
            } catch (err) {
                this.toast('Error de conexión al eliminar el producto.');
            }
        },
    },
};
</script>
