<?php
session_start();
require 'conexion.php';
require 'verificar_admin.php';
header('Content-Type: application/json');

$accion = $_POST['accion'] ?? $_GET['accion'] ?? '';

switch ($accion) {
    case 'listar':
        $cat = $_GET['categoria'] ?? '';
        $bus = $_GET['busqueda'] ?? '';
        $sql = "SELECT p.*, c.nombre as categoria FROM productos p 
                JOIN categorias c ON p.id_categoria = c.id 
                WHERE p.estado = 'activo'";
        $params = []; $types = "";
        if ($cat) { $sql .= " AND c.nombre = ?"; $params[] = $cat; $types .= "s"; }
        if ($bus) { $sql .= " AND p.nombre LIKE ?"; $params[] = "%$bus%"; $types .= "s"; }
        $sql .= " ORDER BY c.nombre, p.nombre";
        
        $stmt = $conexion->prepare($sql);
        if (!empty($params)) $stmt->bind_param($types, ...$params);
        $stmt->execute();
        echo json_encode($stmt->get_result()->fetch_all(MYSQLI_ASSOC));
        break;

    case 'editar':
        if (!esAdmin()) { echo json_encode(['error'=>'No autorizado']); exit; }
        $id = $_POST['id'];
        $nombre = trim($_POST['nombre']);
        $precio = $_POST['precio'];
        $id_cat = $_POST['id_categoria'];
        $disponible = isset($_POST['disponible']) ? 1 : 0;

        if (empty($nombre) || empty($precio) || empty($id_cat)) {
            echo json_encode(['error'=>'Campos obligatorios faltantes']); exit;
        }
        if (!is_numeric($precio) || $precio <= 0) {
            echo json_encode(['error'=>'Precio inválido']); exit;
        }

        $stmt = $conexion->prepare("UPDATE productos SET nombre=?, precio=?, id_categoria=?, disponible=? WHERE id=?");
        $stmt->bind_param("sdiii", $nombre, $precio, $id_cat, $disponible, $id);
        echo $stmt->execute() ? json_encode(['exito'=>'Producto actualizado']) : json_encode(['error'=>'Error al actualizar']);
        break;

    case 'eliminar':
        if (!esAdmin()) { echo json_encode(['error'=>'No autorizado']); exit; }
        $id = $_POST['id'];
        
  
        $check = $conexion->prepare("
            SELECT 1 FROM detalle_pedido dp 
            JOIN pedidos p ON dp.id_pedido = p.id 
            WHERE p.estado = 'en_proceso' AND dp.id_producto = ?
        ");
        $check->bind_param("i", $id);
        $check->execute();
        if ($check->get_result()->num_rows > 0) {
            echo json_encode(['error'=>'No se puede eliminar: producto asociado a pedido en proceso']); exit;
        }

        $user = $_SESSION['usuario_id'];
        $stmt = $conexion->prepare("UPDATE productos SET estado='inactivo', fecha_eliminacion=NOW(), eliminado_por=? WHERE id=?");
        $stmt->bind_param("ii", $user, $id);
        echo $stmt->execute() ? json_encode(['exito'=>'Producto desactivado (trazabilidad conservada)']) : json_encode(['error'=>'Error al desactivar']);
        break;

    default:
        echo json_encode(['error'=>'Acción no válida']);
}
?>