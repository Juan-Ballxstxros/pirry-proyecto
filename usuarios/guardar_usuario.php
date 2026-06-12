
<?php

$conexion = new mysqli("localhost", "root", "", "usuarios");

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// DATOS
$nombre = $_POST['nombre'];
$documento = $_POST['documento'];
$correo = $_POST['correo'];
$usuario = $_POST['usuario'];
$contrasena = password_hash($_POST['contrasena'], PASSWORD_DEFAULT);


$id_rol = $_POST['id_rol'];

$estado = $_POST['estado'];

// VALIDAR EXISTENCIA
$verificar = $conexion->query("SELECT * FROM usuarios WHERE correo='$correo' OR usuario='$usuario'");

if ($verificar->num_rows > 0) {
    echo "El usuario o correo ya existen.";
} else {

    // 🔥 INSERT CORREGIDO
    $sql = "INSERT INTO usuarios (nombre, documento, correo, usuario, contrasena, id_rol, estado)
            VALUES ('$nombre', '$documento', '$correo', '$usuario', '$contrasena', '$id_rol', '$estado')";

    if ($conexion->query($sql)) {
        echo "Usuario registrado correctamente.";
    } else {
        echo "Error al registrar: " . $conexion->error;
    }
}

$conexion->close();

?>


