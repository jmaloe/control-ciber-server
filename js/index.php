<?php
session_start();
 if(!isset($_SESSION['USER']))
 	header("Location:../");
 else
 	header("Location:../formularios/FActividad.php");
?>