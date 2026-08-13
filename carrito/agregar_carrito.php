<?php

session_start();
include("../conexion.php");

$id = $_GET['id'];

// BUSCAR PRODUCTO
$sql = "SELECT * FROM productos WHERE id=$id";
$resultado = mysqli_query($conexion, $sql);

$producto = mysqli_fetch_assoc($resultado);

// CREAR CARRITO SI NO EXISTE
if(!isset($_SESSION['carrito'])){
    $_SESSION['carrito'] = [];
}

// AGREGAR PRODUCTO
$_SESSION['carrito'][] = [
    "id" => $producto['id'],
    "nombre" => $producto['nombre'],
    "precio" => $producto['precio']
];

// VOLVER
header("Location: ../index.php");

?>