<?php



include("../conexion.php");

// Validar ID
if (!isset($_GET['id'])) {
    header("Location: permisos.php");
    exit();
}

$id = $_GET['id'];

// Obtener permiso
$sql = "SELECT * FROM permisos WHERE id_permisos='$id'";
$resultado = mysqli_query($conexion, $sql);
$permiso = mysqli_fetch_assoc($resultado);

if (!$permiso) {
    header("Location: permisos.php");
    exit();
}

// Actualizar
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = mysqli_real_escape_string($conexion, $_POST['nombre']);
    $descripcion = mysqli_real_escape_string($conexion, $_POST['descripcion']);

    $sql_update = "UPDATE permisos 
                   SET nombre='$nombre', descripcion='$descripcion' 
                   WHERE id_permisos='$id'";

    if (mysqli_query($conexion, $sql_update)) {
        header("Location: permisos.php");
        exit();
    } else {
        $error = "Error: " . mysqli_error($conexion);
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Permiso</title>

    <!-- CSS -->
    <link rel="stylesheet" href="../css/ed_permisos.css?v=<?php echo time(); ?>">
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
            <li class="active"><a href="permisos.php">Permisos</a></li>
            <li class="logout">
            <li><a href="/pirry/login_configuracion/logout.php" onclick="return confirmarLogout()">Cerrar sesión</a></li>
        </ul>
    </aside>

    <!-- CONTENIDO -->
    <main class="main">

        <!-- HEADER -->
        <div class="header">
            <h1>Editar Permiso</h1>
            <a href="permisos.php" class="btn-volver">← Volver</a>
        </div>

        <!-- CONTENEDOR CENTRADO -->
        <div class="contenedor-form">

            <div class="card">

                <?php if(!empty($error)) { ?>
                    <p class="error"><?php echo $error; ?></p>
                <?php } ?>

                <form method="post" class="formulario">

                    <div class="campo">
                        <label>Nombre del Permiso</label>
                        <input type="text" name="nombre"
                        value="<?php echo $permiso['nombre']; ?>" required>
                    </div>

                    <div class="campo">
                        <label>Descripción</label>
                        <textarea name="descripcion" rows="4" required><?php echo $permiso['descripcion']; ?></textarea>
                    </div>

                    <div class="acciones">
                        <button type="submit" class="btn guardar">Guardar Cambios</button>
                        <a href="permisos.php" class="btn cancelar">Cancelar</a>
                    </div>

                </form>

            </div>

        </div>

    </main>

</div>

</body>
</html>

<?php
mysqli_close($conexion);
?>