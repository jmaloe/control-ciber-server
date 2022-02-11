<?php
 session_start();
 unset($_SESSION["ID_USER"]);
 unset($_SESSION['USER']);
 unset($_SESSION['USERNAME']);
 unset($_SESSION['rol_usuario']);
 header("Location:login.php");
?>