
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registrar Usuario</title>
    <link rel="stylesheet" href="../css/registro.css?v=1">
</head>

<body>

<div class="dashboard">

    <aside class="sidebar">
        <h2 class="logo">Admin</h2>
        <ul>
            <li><a href="../login_configuracion/admin_inicio.php">Inicio</a></li>
            <li class="active"><a href="../usuarios/consultar_usuarios.php">Usuarios</a></li>
            <li><a href="../roles/roles.php">Roles</a></li>
            <li><a href="../permisos/permisos.php">Permisos</a></li>
            <li class="logout"><a href="../login.php">Cerrar sesión</a></li>
        </ul>
    </aside>

    <main class="main">

        <div class="header">
            <h1>Registrar Usuario</h1>
            <a href="../usuarios/consultar_usuarios.php" class="btn-volver">← Volver</a>
        </div>

        <div class="contenedor-form">
            <div class="card">

                <form action="guardar_usuario.php" method="POST" class="formulario">

                    <div class="grid">

                        <div class="campo">
                            <label>Nombres</label>
                            <input type="text" name="nombre" required>
                        </div>

                        <div class="campo">
                            <label>Documento</label>
                            <input type="text" name="documento" required>
                        </div>

                        <div class="campo">
                            <label>Correo</label>
                            <input type="email" name="correo" required>
                        </div>

                        <div class="campo">
                            <label>Usuario</label>
                            <input type="text" name="usuario" required>
                        </div>

                        <div class="campo">
                            <label>Contraseña</label>
                            <input type="password" name="contrasena" required>
                        </div>

                        <!-- 🔥 ROL CORREGIDO -->
                        <div class="campo">
                            <label>Rol</label>
                            <select name="id_rol" required>
                                <option value="1">Administrador</option>
                                <option value="2">Empleado</option>
                            </select>
                        </div>

                        <!-- 🔥 ESTADO CORREGIDO -->
                        <div class="campo">
                            <label>Estado</label>
                            <select name="estado" required>
                                <option value="activo">Activo</option>
                                <option value="inactivo">Inactivo</option>
                            </select>
                        </div>

                    </div>

                    <div class="acciones">
                        <button type="submit" class="btn guardar">Registrar</button>
                        <a href="../usuarios/consultar_usuarios.php" class="btn cancelar">Cancelar</a>
                    </div>

                </form>

            </div>
        </div>

    </main>

</div>

</body>
</html>


