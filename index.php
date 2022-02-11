<?php
session_start();
 if(!isset($_SESSION['USER']))
 	header("Location:acceso/login.php");
 else
 	header("Location:formularios/FActividad.php");
?>