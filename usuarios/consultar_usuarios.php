<?php


$conexion = new mysqli("localhost", "root", "", "usuarios");

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Cambiar estado
if (isset($_GET['cambiar'])) {
    $id = $_GET['cambiar'];

    $consulta = $conexion->query("SELECT estado FROM usuarios WHERE id=$id");
    $fila = $consulta->fetch_assoc();

    $nuevoEstado = ($fila['estado'] == "Activo") ? "Inactivo" : "Activo";

    $conexion->query("UPDATE usuarios SET estado='$nuevoEstado' WHERE id=$id");
}

$resultado = $conexion->query("SELECT * FROM usuarios");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Usuarios Registrados</title>

    <!-- CSS con anti-cache -->
    <link rel="stylesheet" href="../css/consulta_usuario.css?v=<?php echo time(); ?>">
</head>

<body>

<div class="dashboard">

    <!-- SIDEBAR -->
    <aside class="sidebar">
        <h2 class="logo">Admin</h2>
        <ul>
            <li><a href="../login_configuracion/admin_inicio.php">Inicio</a></li>
            <li class="active"><a href="#">Usuarios</a></li>
            <li><a href="../roles/roles.php">Roles</a></li>
            <li><a href="../permisos/permisos.php">Permisos</a></li>
            <li class="logout"><a href="../login.php">Cerrar sesión</a></li>
        </ul>
    </aside>

    <!-- CONTENIDO -->
    <main class="main">

        <!-- TOP -->
        <div class="topbar">
            <h1>Usuarios Registrados</h1>
            <a class="btn-crear" href="../usuarios/registrar_usuario.php">+ Registrar</a>
        </div>

        <!-- TABLA -->
        <div class="card">

            <table>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Usuario</th>
                    <th>Rol</th>
                    <th>Estado</th>
                    <th>Acción</th>
                </tr>

                <?php while($fila = $resultado->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $fila['id']; ?></td>
                    <td><?php echo $fila['nombre']; ?></td>
                    <td><?php echo $fila['usuario']; ?></td>
                    <td><?php echo $fila['id_rol']; ?></td>

                    <td class="<?php echo strtolower($fila['estado']); ?>">
                        <?php echo $fila['estado']; ?>
                    </td>

                    <td>
                        <?php if($fila['estado'] == "Activo") { ?>
                            <a class="btn desactivar" href="?cambiar=<?php echo $fila['id']; ?>">Inhabilitar</a>
                        <?php } else { ?>
                            <a class="btn activar" href="?cambiar=<?php echo $fila['id']; ?>">Habilitar</a>
                        <?php } ?>

                        <a class="btn-recuperar" href="../login_configuracion/generar_recuperacion.php?id=<?php echo $fila['id']; ?>"> Recuperar contraseña </a>
                
                    </td>
                </tr>
                <?php } ?>

            </table>

        </div>

    </main>

</div>

</body>
</html>

<?php
$conexion->close();
?>