<?php




include("../conexion.php");

// Obtener roles
$roles_query = "SELECT * FROM roles";
$roles_result = mysqli_query($conexion, $roles_query);

// Obtener permisos
$permisos_query = "SELECT * FROM permisos";
$permisos_result = mysqli_query($conexion, $permisos_query);

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_rol = $_POST['id_rol'];
    $permisos_seleccionados = isset($_POST['permisos']) ? $_POST['permisos'] : [];

    // Eliminar permisos anteriores
    mysqli_query($conexion, "DELETE FROM rol_permiso WHERE id_roles='$id_rol'");

    // Insertar nuevos
    foreach($permisos_seleccionados as $permiso_id){
        mysqli_query($conexion, "INSERT INTO rol_permiso (id_roles, id_permisos) VALUES ('$id_rol','$permiso_id')");
    }

    header("Location: asignar_permisos.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Asignar Permisos</title>

<link rel="stylesheet" href="../css/as_permisos.css?v=<?php echo time(); ?>">
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
            <h1>Asignar Permisos</h1>
            <a href="permisos.php" class="btn-volver">← Volver</a>
        </div>

        <!-- FORM CENTRADO -->
        <div class="contenedor-form">

            <div class="card">

                <form method="post" class="formulario">

                    <!-- SELECT -->
                    <div class="campo">
                        <label>Seleccionar Rol</label>
                        <select name="id_rol" required>
                            <option value="">--Seleccione--</option>
                            <?php while($rol = mysqli_fetch_assoc($roles_result)) { ?>
                                <option value="<?php echo $rol['id_roles']; ?>">
                                    <?php echo $rol['nombre']; ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <!-- PERMISOS GRID -->
                    <div class="campo">
                        <label>Permisos</label>

                        <div class="permisos-grid">
                            <?php while($permiso = mysqli_fetch_assoc($permisos_result)) { ?>
                                <label class="permiso-item">
                                    <input type="checkbox" name="permisos[]" value="<?php echo $permiso['id_permisos']; ?>">
                                    <span><?php echo $permiso['nombre']; ?></span>
                                </label>
                            <?php } ?>
                        </div>
                    </div>

                    <!-- BOTONES -->
                    <div class="acciones">
                        <button type="submit" class="btn guardar">Guardar</button>
                        <a href="permisos.php" class="btn cancelar">Cancelar</a>
                    </div>

                </form>

            </div>

        </div>

    </main>

</div>

</body>
</html>

<?php mysqli_close($conexion); ?>