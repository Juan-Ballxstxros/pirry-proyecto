/* ==========================================================================
   EL PIRRY — MENÚ (filtros por categoría + cuadrícula de productos)
   --------------------------------------------------------------------------
   • Categorías como botones/filtros: cada una muestra solo sus productos.
   • Conectado a la base de datos mediante la API /productos.
   • Diseñado SIN imágenes y SIN precios: nombre + descripción + botón.
   • Todo se agrega al carrito existente (index.js), incluidas las adiciones.
   ========================================================================== */

/* Categorías principales del menú (orden y emoji del filtro).
   "Adiciones" es una categoría más y sus productos se agregan al
   carrito igual que todos los demás. */
const MENU_CATEGORIAS_CONFIG = [
  { nombre: 'Salchipapas', emoji: '🍟' },
  { nombre: 'Perros', emoji: '🌭' },
  { nombre: 'Perras', emoji: '🌭' },
  { nombre: 'Bebidas', emoji: '🥤' },
  { nombre: 'Picadas', emoji: '🥩' },
  { nombre: 'Hamburguesas', emoji: '🍔' },
  { nombre: 'Tociqueso', emoji: '🧀' },
  { nombre: 'Pirrypollo', emoji: '🍗' },
  { nombre: 'Adiciones', emoji: '🥓' },
];

let menuFiltroActivo = 'todos';
let menuCategoriasDB = [];

/* Quita mayúsculas, tildes y espacios extra para comparar categorías */
function normalizarTexto(texto) {
  return String(texto || '')
    .toLowerCase()
    .normalize('NFD')
    .replace(/[\u0300-\u036f]/g, '')
    .trim();
}

function emojiDeCategoria(nombre) {
  const clave = normalizarTexto(nombre);
  const fija = MENU_CATEGORIAS_CONFIG.find((c) => normalizarTexto(c.nombre) === clave);
  return fija ? fija.emoji : '🍽️';
}

/* ---------- Carga inicial ---------- */

async function iniciarMenuCategorias() {
  const grid = document.getElementById('menuGrid');
  if (!grid) return;

  const ok = await cargarProductos();
  if (!ok) {
    grid.innerHTML = `
      <p class="menu-sin-resultados">No se pudo cargar el menú.
        <button type="button" class="btn-item-reintentar" onclick="iniciarMenuCategorias()">Reintentar</button>
      </p>`;
    return;
  }

  await cargarCategoriasParaAdmin();
  renderMenuFiltros();
  renderMenuProductos();
}

/* Todas las categorías de la BD para el selector del modal de admin
   (permite crear productos en categorías que aún no tienen productos) */
async function cargarCategoriasParaAdmin() {
  if (!isAdmin) return;
  const selectModal = document.getElementById('prodCategoria');
  if (!selectModal) return;

  try {
    const res = await fetch(`${routes.productos}/categorias`, {
      headers: { Accept: 'application/json' },
    });
    menuCategoriasDB = await res.json();
    selectModal.innerHTML = menuCategoriasDB
      .map((c) => `<option value="${c.id}">${c.nombre}</option>`)
      .join('');
  } catch (err) {
    /* Si falla, el modal usa las categorías de llenarFiltroCategorias() */
  }
}

/* ---------- Filtros (pills) de categoría ---------- */

function seccionesDelMenu() {
  const grupos = {};
  (productos || []).forEach((p) => {
    const clave = normalizarTexto(p.categoria);
    (grupos[clave] = grupos[clave] || []).push(p);
  });

  const secciones = MENU_CATEGORIAS_CONFIG.map((cfg) => {
    const clave = normalizarTexto(cfg.nombre);
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
}

function renderMenuFiltros() {
  const contenedor = document.getElementById('menuFiltros');
  if (!contenedor) return;

  const total = (productos || []).length;
  let html = `
    <button type="button" class="menu-pill ${menuFiltroActivo === 'todos' ? 'activa' : ''}" data-cat="todos">
      <span class="menu-pill-emoji" aria-hidden="true">✨</span>
      Todos
      <span class="menu-pill-count">${total}</span>
    </button>`;

  seccionesDelMenu().forEach((s) => {
    html += `
    <button type="button" class="menu-pill ${menuFiltroActivo === s.clave ? 'activa' : ''}" data-cat="${s.clave}">
      <span class="menu-pill-emoji" aria-hidden="true">${s.emoji}</span>
      ${s.nombre}
      <span class="menu-pill-count">${s.cantidad}</span>
    </button>`;
  });

  contenedor.innerHTML = html;

  contenedor.querySelectorAll('.menu-pill').forEach((btn) => {
    btn.addEventListener('click', () => {
      menuFiltroActivo = btn.dataset.cat;
      contenedor.querySelectorAll('.menu-pill').forEach((b) => {
        b.classList.toggle('activa', b.dataset.cat === menuFiltroActivo);
      });
      renderMenuProductos();
    });
  });
}

/* ---------- Cuadrícula de productos ---------- */

function productosFiltrados() {
  let lista = productos || [];
  if (menuFiltroActivo !== 'todos') {
    lista = lista.filter((p) => normalizarTexto(p.categoria) === menuFiltroActivo);
  }
  const q = normalizarTexto(document.getElementById('busquedaMenu')?.value || '');
  if (q) {
    lista = lista.filter((p) => normalizarTexto(`${p.nombre} ${p.descripcion || ''}`).includes(q));
  }
  return lista;
}

function renderCardProducto(p) {
  const disponible = p.disponible !== false;
  const adminBtns = isAdmin ? `
      <div class="admin-controls">
        <button class="btn-edit-small" onclick="abrirModalEditar(${p.id})">✏️ Editar</button>
        <button class="btn-del-small" onclick="eliminarProducto(${p.id})">🗑️ Eliminar</button>
      </div>` : '';

  return `
    <article class="menu-card ${disponible ? '' : 'menu-card-agotado'}">
      <div class="menu-card-icono" aria-hidden="true">${emojiDeCategoria(p.categoria)}</div>
      <h3 class="menu-card-nombre">${p.nombre}</h3>
      ${p.descripcion ? `<p class="menu-card-desc">${p.descripcion}</p>` : ''}
      <button class="btn-card-add" onclick="agregarAlCarrito(${p.id})" ${disponible ? '' : 'disabled'}>
        ${disponible ? '🛒 Agregar al carrito' : 'No disponible'}
      </button>
      ${adminBtns}
    </article>`;
}

function renderMenuProductos() {
  const grid = document.getElementById('menuGrid');
  if (!grid) return;

  const lista = productosFiltrados();
  if (lista.length === 0) {
    grid.innerHTML = '<p class="menu-sin-resultados">No hay productos en esta categoría todavía. ¡Vuelve pronto! 🍽️</p>';
    return;
  }

  grid.innerHTML = lista.map(renderCardProducto).join('');
}

/* ---------- Eventos ---------- */

document.addEventListener('DOMContentLoaded', () => {
  const busquedaMenu = document.getElementById('busquedaMenu');
  if (busquedaMenu) {
    busquedaMenu.addEventListener('input', debounce(renderMenuProductos, 250));
  }
});

/* El script se carga al final del <body>, el DOM ya está listo */
iniciarMenuCategorias();

/* Hook para que index.js refresque el menú tras el CRUD de admin */
window.refrescarMenuTrasAdmin = async function () {
  await cargarCategoriasParaAdmin();
  renderMenuFiltros();
  renderMenuProductos();
};

