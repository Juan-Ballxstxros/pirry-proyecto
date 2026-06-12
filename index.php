<?php
session_start();
require 'conexion.php';

// Verificar si es administrador
$esAdmin = false;
if (isset($_SESSION['usuario_id'])) {
    $stmt = $conexion->prepare("
        SELECT r.nombre FROM usuarios u 
        JOIN roles r ON u.id_rol = r.id_roles 
        WHERE u.id = ? AND LOWER(r.nombre) = 'administrador'
    ");
    $stmt->bind_param("i", $_SESSION['usuario_id']);
    $stmt->execute();
    $esAdmin = $stmt->get_result()->num_rows > 0;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>El Pirry - Comidas Rápidas</title>
<link rel="stylesheet" href="css/index.css">
</head>
<body>

<nav>
  <div class="logo">El Pirry</div>
  <ul>
    <li><a href="#">Inicio</a></li>
    <li><a href="#Productos">Menú</a></li>
    <li><a href="#Nosotros">Nosotros</a></li>
    <li>
      <?php if(isset($_SESSION['usuario_id'])): ?>
        <span class="user-greet">Hola, <?php echo htmlspecialchars($_SESSION['usuario_nombre'] ?? 'Usuario'); ?></span>
        <a href="logout.php" class="btn-primary">Cerrar sesión</a>
      <?php else: ?>
        <a href="login.php" class="btn-primary">Iniciar sesión</a>
      <?php endif; ?>
    </li>
  </ul>
</nav>

<section class="hero">
  <div class="hero-text">
    <h1>¡Sabor y calidad en cada bocados!</h1>
    <p>En El Pirry preparamos las mejores salchipapas y hamburguesas de todo Guayabal.</p>
    <div class="hero-buttons">
      <a href="#Productos" class="btn-orange">Ver Menú Completo</a>
      <a href="#" onclick="verCarrito(); return false;" class="btn-outline">🛒 Mi Pedido</a>
    </div>
  </div>
  <img src="https://images.unsplash.com/photo-1550547660-d9450f859349" alt="Comida">
</section>

<!-- PRODUCTOS -->
<section id="Productos" class="section">
  <h2>Nuestro Menú</h2>
  <p>Descubre nuestras especialidades</p>

  <!-- Filtros y búsqueda -->
  <div class="products-filters">
    <input type="text" id="busqueda" placeholder="🔍 Buscar producto por nombre...">
    <select id="filtroCategoria">
      <option value="">Todas las categorías</option>
    </select>
    <?php if($esAdmin): ?>
      <button class="btn-edit" onclick="abrirModalCrear()">➕ Nuevo Producto</button>
    <?php endif; ?>
  </div>

  <div class="products" id="listaProductos">
    <p class="loading-msg">Cargando menú...</p>
  </div>
</section>

<!-- Modal para editar/crear producto (solo admin) -->
<div id="modalProducto" class="modal">
  <div class="modal-content">
    <h3 id="modalTitulo">Editar Producto</h3>
    <input type="hidden" id="prodId">
    
    <label>Nombre *</label>
    <input type="text" id="prodNombre" required>
    
    <label>Descripción</label>
    <textarea id="prodDescripcion"></textarea>
    
    <label>Precio * ($)</label>
    <input type="number" id="prodPrecio" step="0.01" min="0" required>
    
    <label>Categoría *</label>
    <select id="prodCategoria" required></select>
    
    <label class="checkbox-label">
      <input type="checkbox" id="prodDisponible" checked> 
      Disponible para venta
    </label>
    
    <?php if($esAdmin): ?>
      <p class="id-info"><strong>ID del producto:</strong> <span id="prodIdDisplay"></span> (no modificable)</p>
    <?php endif; ?>
    
    <div class="modal-buttons">
      <button class="btn-cancel" onclick="cerrarModal()">Cancelar</button>
      <button class="btn-save" onclick="guardarProducto()">💾 Guardar Cambios</button>
    </div>
  </div>
</div>

<!-- Modal del carrito -->
<div id="modalCarrito" class="modal">
  <div class="modal-content">
    <h3>🛒 Tu Pedido</h3>
    <div id="contenidoCarrito">
      <p>Tu carrito está vacío.</p>
    </div>
    <div class="carrito-total">
      <strong>Total: $<span id="totalCarrito">0.00</span></strong>
    </div>
    <div class="modal-buttons">
      <button class="btn-cancel" onclick="cerrarCarrito()">Seguir comprando</button>
      <button class="btn-save" onclick="confirmarPedido()">✅ Confirmar Pedido</button>
    </div>
  </div>
</div>

<section id="Nosotros" class="section">
  <h2>¿Por qué elegir El Pirry?</h2>
  <div class="stats">
    <div class="stat"><h3>8+</h3><p>Años de experiencia</p></div>
    <div class="stat"><h3>15k+</h3><p>Clientes felices</p></div>
    <div class="stat"><h3>20+</h3><p>Productos en menú</p></div>
    <div class="stat"><h3>4.9</h3><p>Rating promedio</p></div>
  </div>
</section>

<footer>
  <div class="footer-content">
    <div class="footer-box">
      <h3>El Pirry</h3>
      <p>Las mejores salchipapas y hamburguesas de Guayabal 🍔🔥</p>
    </div>
    <div class="footer-box">
      <h3>Enlaces</h3>
      <ul>
        <li><a href="#inicio">Inicio</a></li>
        <li><a href="#Productos">Menú</a></li>
        <li><a href="#Nosotros">Nosotros</a></li>
      </ul>
    </div>
    <div class="footer-box">
      <h3>Contacto</h3>
      <ul>
        <li>📍 Guayabal</li>
        <li>📞 300 123 4567</li>
        <li>📧 elpirry@gmail.com</li>
      </ul>
    </div>
    <div class="footer-box">
      <h3>Síguenos</h3>
      <div class="social">
        <a href="#">FB</a>
        <a href="#">IG</a>
        <a href="#">WA</a>
      </div>
    </div>
  </div>
  <p class="footer-copy">© 2026 El Pirry - Todos los derechos reservados</p>
</footer>

<!-- Badge flotante del carrito -->
<div id="carrito-badge" onclick="verCarrito()">
  🛒 <span id="countCarrito">0</span>
</div>

<script src="js/index.js"></script>
<script>
// Variables globales desde PHP
const isAdmin = <?php echo $esAdmin ? 'true' : 'false'; ?>;
</script>
</body>
</html>