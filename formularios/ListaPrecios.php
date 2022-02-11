<?php
header("Content-type: text/html");
date_default_timezone_set('America/Mexico_City');
 if(isset($_POST['accion']))
 {
    include_once('../clases/CProductos.php');
    include_once("../db/AccessDB.php");
    $lista = new CProductos($db);
    $lista->getListaPrecios();    
    $db->close_conn();
 }
?>