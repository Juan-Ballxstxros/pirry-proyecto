
<?php 
include("seguridad.php");
include("../conexion.php");

$conn = $conexion; 

// Validar conexión
if (!$conn) {
    die("Error de conexión");
}

// FUNCION PARA CONTAR
function contar($conn, $tabla, $condicion = "") {
    $sql = "SELECT COUNT(*) as total FROM $tabla $condicion";
    $res = mysqli_query($conn, $sql);
    if ($res && $fila = mysqli_fetch_assoc($res)) {
        return $fila['total'];
    }
    return 0;
}

// CONTADORES
$totalUsuarios = contar($conn, "usuarios");
$totalRoles = contar($conn, "roles");
$totalPermisos = contar($conn, "permisos");
$totalActivos = contar($conn, "usuarios", "WHERE estado='activo'");

$porcentaje = ($totalUsuarios > 0) ? ($totalActivos / $totalUsuarios) * 100 : 0;

// ÚLTIMOS USUARIOS
$ultimos = mysqli_query($conn, "SELECT id, nombre, estado FROM usuarios ORDER BY id DESC LIMIT 5");
?>

<!DOCTYPE html>
<html>
<head>
<title>Panel Administrador</title>
<link rel="stylesheet" href="../css/interfaz_ad.css">
</head>

<body>

<div class="dashboard">

    <aside class="sidebar">
        <h2 class="logo">Admin</h2>
        <ul>
            <li class="active"><a href="#">Inicio</a></li>
            <li><a href="../usuarios/consultar_usuarios.php">Usuarios</a></li>
            <li><a href="../roles/roles.php">Roles</a></li>
            <li><a href="../permisos/permisos.php">Permisos</a></li>
            <li><a href="/pirry/login_configuracion/logout.php" onclick="return confirmarLogout()">Cerrar sesión</a></li>
        </ul>
    </aside>

    <main class="main">

        <div class="topbar">
            <div class="user">
                Bienvenido <?php echo isset($_SESSION['usuario']) ? $_SESSION['usuario'] : 'Admin'; ?>
            </div>
        </div>

        
        <div class="cards">
            <div class="card">Usuarios<br><strong><?php echo $totalUsuarios; ?></strong></div>
            <div class="card">Roles<br><strong><?php echo $totalRoles; ?></strong></div>
            <div class="card">Permisos<br><strong><?php echo $totalPermisos; ?></strong></div>
            <div class="card">Activos<br><strong><?php echo round($porcentaje); ?>%</strong></div>
        </div>

       
        <div class="tabla">
            <h3>Últimas acciones</h3>
            <table>
                <tr>
                    <th>Usuario</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>

                <?php 
                if ($ultimos && mysqli_num_rows($ultimos) > 0) {
                    while($fila = mysqli_fetch_assoc($ultimos)) { 
                ?>
                <tr>
                    <td><?php echo $fila['nombre']; ?></td>

                    <td class="<?php echo ($fila['estado']=='activo') ? 'activo' : 'pendiente'; ?>">
                        <?php echo ucfirst($fila['estado']); ?>
                    </td>

                    <td>
                        <a class="btn-recuperar" href="../login_configuracion/generar_recuperacion.php?id=<?php echo $fila['id']; ?>"> Recuperar contraseña </a>
                    </td>

                </tr>
                <?php 
                    }
                } else {
                    echo "<tr><td colspan='3'>No hay datos</td></tr>";
                }
                ?>

            </table>
        </div>

        

    </main>

</div>

<script>
function confirmarLogout() {
    return confirm("¿Seguro que deseas cerrar sesión?");
}
</script>

</body>
</html>
