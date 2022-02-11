<?php
header("Content-type: text/xml");
date_default_timezone_set('America/Mexico_City');

include_once('CommonXMLFunctions.php');
/*Consultar el progreso y estado de la venta*/
if (file_exists('equipos.xml'))
{   
            
    /*guardamos los cambios de tiempo y total en la base de datos*/
    include_once('../clases/CVentas.php');
    include_once("../db/AccessDB.php");
    $venta = new CVentas($db);
    $equipos=null;
    if(@empty(simplexml_load_file('equipos.xml'))){
        $equipos = simplexml_load_file('equiposRespaldo.xml');
    }
    else
    {
        $equipos = simplexml_load_file('equipos.xml');
    }
    /*echo "<?xml version='1.0'?>";*/
    foreach($equipos->pc as $pc){
        $venta->setIdEquipo($pc['id']);
        if($pc['status']=='running')
        {
            if($venta->checkIfRunning()==1)
                $pc['status']="finished";
            else
                foreach($pc->prodservs->producto as $producto){
                    if($producto['id']==1){ /*1 para Internet*/
                        $hi = $pc->horaInicio;
                        $minutos = calcular_tiempo_trasnc(date("H:i:s"),$hi);
                        if($pc->horaTermino!='00:00:00')
                        {
                            $pc->tiempoRestante = calcular_tiempo_trasnc($pc->horaTermino,date('H:i:s')).' mins';
                        }                    
                        $producto->cantidad = $minutos." mins";
                        $producto->total = calcularTotal($minutos);
                                            
                        $obj = array("cnsdv"=>$producto['noreg'],"id_producto"=>1,"cantidad"=>$minutos,"precio_unitario"=>10,"total"=>$producto->total);
                        $venta->actualizarDetalleDeVenta($obj);
                        /*enviamos el estado actual del servicio en el equipo con id x*/                    
                    }
                }
        }
    }

    echo $equipos->asXML();
    $equipos->asXml("equipos.xml"); //guardamos los cambios
    /*cerramos conexion de la BD*/
    $db->close_conn();
    
}else{
    exit('Error abriendo equipos.xml');
}

?>