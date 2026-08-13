<?php

function esAdmin(): bool
{
    if (! isset($_SESSION['usuario_id']) && isset($_SESSION['id'])) {
        $_SESSION['usuario_id'] = $_SESSION['id'];
    }

    if (! isset($_SESSION['usuario_id'])) {
        return false;
    }

    require_once __DIR__.'/../conexion.php';

    $stmt = $conexion->prepare("
        SELECT r.nombre FROM usuarios u
        JOIN roles r ON u.id_rol = r.id_roles
        WHERE u.id = ? AND LOWER(r.nombre) = 'administrador'
    ");
    $stmt->bind_param('i', $_SESSION['usuario_id']);
    $stmt->execute();

    return $stmt->get_result()->num_rows > 0;
}
