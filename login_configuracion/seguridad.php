<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: /pirry/login.php");
    exit();
}

header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");
?>