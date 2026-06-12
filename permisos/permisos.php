<?php
session_start();


include("../conexion.php");

// Obtener permisos
$sql = "SELECT * FROM permisos";
$resultado_permisos = mysqli_query($conexion, $sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Permisos</title>

    <!-- CSS con anti-cache -->
    <link rel="stylesheet" href="../css/permisos.css?v=<?php echo time(); ?>">
</head>

<body>

<div class="dashboard">

    <!-- SIDEBAR -->
    <aside class="sidebar">
        <h2 class="logo">Admin</h2>
        <ul>
            <li><a href="../login_configuracion/admin_inicio.php">Inicio</a></li>
            <li><a href="../usuarios/consultar_usuarios.php">Usuarios</a></li>
            <li><a href="../roles/roles.php">Roles</a></li>
            <li class="active"><a href="#">Permisos</a></li>
            <li class="logout"><a href="../login.php">Cerrar sesión</a></li>
        </ul>
    </aside>

    <!-- CONTENIDO -->
    <main class="main">

        <!-- TOP -->
        <div class="topbar">
            <h1>Gestión de Permisos</h1>

            <div class="acciones">
                <a href="../permisos/agregar_permisos.php" class="btn-crear">+ Permiso</a>
                <a href="../permisos/asignar_permisos.php" class="btn-crear">Asignar</a>
            </div>
        </div>

        <!-- TABLA -->
        <div class="card">

            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Acción</th>
                    </tr>
                </thead>

                <tbody>
                <?php while($permiso = mysqli_fetch_assoc($resultado_permisos)) { ?>
                <tr>
                    <td><?php echo $permiso['id_permisos']; ?></td>
                    <td><?php echo $permiso['nombre']; ?></td>
                    <td><?php echo $permiso['descripcion']; ?></td>

                    <td class="acciones-tabla">

                        <!-- EDITAR -->
                        <form action="../permisos/editar_permisos.php" method="get">
                            <input type="hidden" name="id" value="<?php echo $permiso['id_permisos']; ?>">
                            <button type="submit" class="btn editar">Editar</button>
                        </form>

                        <!-- ELIMINAR -->
                        <form action="eliminar_permiso.php" method="post">
                            <input type="hidden" name="id" value="<?php echo $permiso['id_permisos']; ?>">
                            <button type="submit" class="btn eliminar" onclick="return confirm('¿Eliminar permiso?');">
                                Eliminar
                            </button>
                        </form>

                    </td>
                </tr>
                <?php } ?>
                </tbody>

            </table>

        </div>

    </main>

</div>

</body>
</html>

<?php
mysqli_close($conexion);
?>