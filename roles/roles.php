<?php





$conexion = new mysqli("localhost", "root", "", "usuarios");

if ($conexion->connect_error) {
    die("Error de conexión");
}

/* CREAR */
if(isset($_POST['crear'])){
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];

    $conexion->query("INSERT INTO roles (nombre, descripcion, estado) VALUES ('$nombre','$descripcion','Activo')");
}

/* ACTUALIZAR */
if(isset($_POST['actualizar'])){
    $id_roles = $_POST['id_roles'];
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];

    $conexion->query("UPDATE roles SET nombre='$nombre', descripcion='$descripcion' WHERE id_roles=$id_roles");
}

/* CAMBIAR ESTADO */
if(isset($_GET['estado'])){
    $id_roles = $_GET['estado'];

    $resultado = $conexion->query("SELECT estado FROM roles WHERE id_roles=$id_roles");
    $fila = $resultado->fetch_assoc();

    $nuevoEstado = ($fila['estado']=='Activo') ? 'Inactivo' : 'Activo';

    $conexion->query("UPDATE roles SET estado='$nuevoEstado' WHERE id_roles=$id_roles");
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Gestión de Roles</title>
<link rel="stylesheet" href="../css/roles.css">
</head>

<body>

<div class="dashboard">

    <!-- SIDEBAR -->
    <aside class="sidebar">
        <h2 class="logo">Admin</h2>
        <ul>
            <li><a href="../login_configuracion/admin_inicio.php">Inicio</a></li>
            <li><a href="../usuarios/consultar_usuarios.php">Usuarios</a></li>
            <li class="active"><a href="#">Roles</a></li>
            <li><a href="../permisos/permisos.php">Permisos</a></li>
            <li class="logout"><a href="../login.php">Cerrar sesión</a></li>
        </ul>
    </aside>

    <!-- CONTENIDO -->
    <main class="main">

        <h1>Gestión de Roles</h1>

        <!-- FORMULARIO -->
        <div class="card">
            <h3>Crear Nuevo Rol</h3>
            <form method="POST" class="formulario">
                <input type="text" name="nombre" placeholder="Nombre del rol" required>
                <input type="text" name="descripcion" placeholder="Descripción" required>
                <button type="submit" name="crear">Guardar</button>
            </form>
        </div>

        <!-- TABLA -->
        <div class="card">
            <h3>Roles Registrados</h3>

            <table>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Estado</th>
                    <th>Acción</th>
                </tr>

                <?php
                $resultado = $conexion->query("SELECT * FROM roles");

                while($fila = $resultado->fetch_assoc()){
                ?>

                <tr>
                    <td><?php echo $fila['id_roles']; ?></td>
                    <td><?php echo $fila['nombre']; ?></td>
                    <td><?php echo $fila['descripcion']; ?></td>
                    <td class="<?php echo strtolower($fila['estado']); ?>">
                        <?php echo $fila['estado']; ?>
                    </td>
                    <td>
                        <a class="btn" href="?estado=<?php echo $fila['id_roles']; ?>">
                            Cambiar
                        </a>
                    </td>
                </tr>

                <?php } ?>

            </table>
        </div>

    </main>

</div>

</body>
</html>