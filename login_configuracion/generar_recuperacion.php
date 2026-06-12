<?php

session_start();
include("../conexion.php");

// VALIDAR SESIÓN
if(!isset($_SESSION['usuario'])){
    die("Debe iniciar sesión");
}

// VALIDAR ID
if(!isset($_GET['id'])){
    die("Usuario inválido");
}

$id = $_GET['id'];

// GENERAR TOKEN
$token = bin2hex(random_bytes(32));

// EXPIRACIÓN 15 MIN
$expira = date("Y-m-d H:i:s", strtotime("+15 minutes"));

// GUARDAR TOKEN
$sql = "UPDATE usuarios
SET reset_token='$token',
token_expira='$expira'
WHERE id=$id";

mysqli_query($conexion, $sql);

// CREAR LINK
$link = "http://localhost/pirry/usuarios/reset_password.php?token=$token";

?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Recuperación</title>

<link rel="stylesheet" href="../css/recuperacion.css?v=<?php echo time(); ?>">

</head>
<body>
    <div class="spam-box">

    <div class="spam-header">
        <div class="icon">✓</div>
        <h2>Recuperación generada</h2>
    </div>

    <p>
        El enlace de recuperación fue generado correctamente.
        Comparta este enlace con el usuario para que pueda
        restablecer su contraseña.
    </p>

    <div class="link-box">
        <a href="<?php echo $link; ?>">
            <?php echo $link; ?>
        </a>
    </div>

    <p class="info">
        Este enlace expirará automáticamente en 15 minutos.
    </p>

    <a href="admin_inicio.php" class="btn">
        Volver al panel
    </a>

</div>
</body>
</html>
