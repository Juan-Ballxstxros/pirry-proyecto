<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Login</title>

<link rel="stylesheet" href="./css/login.css?v=<?php echo time(); ?>">

</head>

<body>

<div class="contenedor">

    <!-- IZQUIERDA -->
    <div class="left">
        <div class="texto">
            <h1>Bienvenido</h1>
            <p>Accede al sistema de gestión</p>
        </div>
    </div>

    <!-- DERECHA -->
    <div class="right">

        <form action="login_configuracion/procesar_login.php" method="POST" class="login-box">

            <h2>Iniciar Sesión</h2>

            <div class="input-box">
                <label>Correo</label>
                <input type="email" name="correo" required>
            </div>

            <div class="input-box">
                <label>Contraseña</label>
                <input type="password" name="contrasena" required>
            </div>

            <!-- BOTONES -->
            <div class="botones">
                <button type="submit" name="butingreso" class="btn ingresar">Ingresar</button>

                <a href="index.php" class="btn volver">Volver</a>
                <p class="info-login">Si olvidó su contraseña, contacte al administrador del sistema.</p>

            </div>

        </form>

    </div>

</div>

</body>
</html>