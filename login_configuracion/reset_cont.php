<?php
include("../conexion.php");

$token = $_GET['token'];

$sql = "SELECT * FROM usuarios
WHERE reset_token='$token'
AND token_expira > NOW()";

$resultado = mysqli_query($conexion, $sql);

if(mysqli_num_rows($resultado) == 0){
    die("El enlace expiró o no es válido");
}
?>

<form action="guardar_cont.php" method="POST">

    <input type="hidden" name="token" value="<?php echo $token; ?>">

    <label>Nueva contraseña</label><br>
    <input type="password" name="password" required><br><br>

    <button type="submit">
        Cambiar contraseña
    </button>

</form>