<?php
header("Content-type: text/html");
date_default_timezone_set('America/Mexico_City');
include_once('../clases/CVentas.php');
include_once("../db/AccessDB.php");
$venta = new CVentas($db);
 if(isset($_POST['fecha']))
 {    
    $venta->setFecha($_POST['fecha']);
    $venta->getHistorialVentas();
    $db->close_conn();
 }
 else if($_POST['no_venta']){
 	$venta->setNoVenta($_POST['no_venta']);
 	$venta->getDetalleVenta();
 }
?>