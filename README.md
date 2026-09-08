# El Pirry - Comidas Rápidas (Laravel)

Proyecto migrado a **Laravel 11** con tienda pública, carrito de compras, autenticación/registro y panel administrador.

## Requisitos

- PHP 8.2+
- Composer
- MySQL / MariaDB (XAMPP recomendado)
- Extensiones PHP: pdo_mysql, mbstring, openssl, tokenizer, xml, ctype, json, bcmath

## Instalación

### 1. Clonar / abrir el proyecto

```bash
cd pirry-proyecto-master
```

### 2. Instalar dependencias

```bash
composer install
```

### 3. Configurar entorno

```bash
copy .env.example .env
php artisan key:generate
```

Edita `.env` si tu base de datos usa credenciales distintas:

```
DB_DATABASE=usuarios
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Base de datos

**Opción A – Base de datos existente (conserva tus datos):**

Importa tu dump actual en phpMyAdmin y luego ejecuta solo las migraciones nuevas:

```bash
php artisan migrate
```

Las migraciones detectan tablas existentes y solo agregan columnas faltantes (ej. `imagen`, `reset_token`).

**Opción B – Instalación desde cero:**

```bash
php artisan migrate --seed
```

Esto crea todas las tablas y datos iniciales (usuarios, roles, productos de ejemplo).

> **Nota:** El seeder de productos **solo inserta datos si la tabla `productos` está vacía**, para no sobrescribir precios ni productos existentes.

### 5. Ejecutar el proyecto

```bash
php artisan serve
```

Abre: **http://localhost:8000**

### 6. XAMPP (Apache)

Configura el **DocumentRoot** de Apache apuntando a la carpeta `public/` del proyecto, o usa el `.htaccess` en la raíz que redirige automáticamente.

## Credenciales de prueba

| Usuario | Correo | Contraseña | Rol |
|---------|--------|------------|-----|
| catalina | catarojasvaz@gmail.com | 123 | Administrador |
| juan | ramirezballesterosjuanpablo@gmail.com | (hash bcrypt) | Administrador |

## Rutas principales

| Ruta | Descripción |
|------|-------------|
| `/` | Tienda / menú de productos |
| `/login` | Iniciar sesión |
| `/registro` | Crear cuenta nueva |
| `/carrito/*` | API del carrito (sesión) |
| `/productos` | API JSON de productos |
| `/admin` | Panel administrador |

## Estructura Laravel

```
app/
├── Http/Controllers/     # Lógica de negocio
├── Http/Middleware/      # Auth y Admin
└── Models/               # Eloquent (Usuario, Product, etc.)
resources/views/          # Vistas Blade
routes/web.php            # Rutas
database/migrations/      # Esquema BD
database/seeders/         # Datos iniciales
public/css/               # Estilos
public/js/index.js        # Carrito y productos (AJAX)
legacy/                   # Archivos PHP originales (referencia)
```

## Funcionalidades

- ✅ Menú de productos con filtros y búsqueda
- ✅ Carrito con cantidades, subtotal y total
- ✅ Registro e inicio de sesión
- ✅ Confirmación de pedidos (requiere login)
- ✅ Panel admin (usuarios, roles, permisos)
- ✅ CRUD de productos para administradores
- ✅ Diseño responsive (móvil, tablet, desktop)

## Archivos legacy

Los archivos PHP originales se conservan en sus carpetas (`login_configuracion/`, `usuarios/`, etc.) como referencia. La aplicación activa corre completamente en Laravel.
