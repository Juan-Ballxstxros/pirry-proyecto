<?php
session_start();
session_destroy();

header("Location: /pirry/login.php");
exit();
?>