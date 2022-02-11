<?php
header("Content-type: text/xml");
date_default_timezone_set('America/Mexico_City');
require_once('CommonXMLFunctions.php');
$gdb=null;
if(isset($_POST['accion']))
{
    require_once('../clases/CVentas.php');
    require_once("../db/AccessDB.php");    
    $gdb = $db;
    $venta = new CVentas($db);
    $venta->setIdEquipo($_POST['id']);

    if($_POST['accion']=="Registrar"){
        $venta->setHoraInicio(date('H:i:s'));
        $venta->registrarVenta();
        if(!registrarEnXML($_POST['id']))
            registrarEnXML($_POST['id']);
        echo $venta->getNoVenta();
    }
    else if($_POST['accion']=='Finalizar'){
        $venta->setHoraTermino(date('H:i:s'));
        $venta->setTotal($_POST['total']);
        $venta->finalizarVenta();
         $equipos = simplexml_load_file('equipos.xml');
          $resultado = $equipos->xpath('pc[@id="'.$_POST['id'].'"]');
          if($resultado)
          {
           $resultado[0]['status']='finished';
           $resultado[0]->horaTermino=date('H:i:s');
           $equipos->asXml("equipos.xml"); //guardamos los cambios
           $equipos->asXml("equiposRespaldo.xml");
          }
          else
            echo "ID no encontrado";
        echo "ID:".$_POST['id'];
    }
    else if($_POST['accion']=='VerificarCierre'){
        $equipos = simplexml_load_file('equipos.xml');
        $resultado = $equipos->xpath('pc[@id="'.$_POST['id'].'"]');
        if($resultado[0]['status']!='finished')
        {
            if($resultado[0]->horaTermino!='00:00:00'){
                $faltante = calcular_tiempo_trasnc($resultado[0]->horaTermino,date('H:i:s'));
                if($faltante<=0)
                    echo "CierreExitoso";
                else
                    echo "Faltan:".$faltante." mins";
            }
            else{
                $transcurrido = calcular_tiempo_trasnc(date('H:i:s'), $resultado[0]->horaInicio);
                echo "Tiempo:".$transcurrido." mins";
            }
        }
        else
        {            
            echo "CierreExitoso";
        }
    }
    else if($_POST['accion']=='SetHoraInicio'){
        /*guardamos el cambio en la base de datos*/
        $venta->setHoraInicio($_POST['hora_inicio']);
        $venta->registrarHoraInicio();
        /*guardamoe el cambios en el archivo xml*/
        $equipos = simplexml_load_file('equipos.xml');
        $resultado = $equipos->xpath('pc[@id="'.$_POST['id'].'"]');
        if($resultado){
            $resultado[0]->horaInicio=$_POST['hora_inicio'];
            $equipos->asXml('equipos.xml');
        }
        echo "Hora de inicio establecida: ".$_POST['hora_inicio'];
    }
    else if($_POST['accion']=='SetHoraTermino'){
        /*guardamos el cambio en la base de datos*/
        $venta->setHoraTermino($_POST['hora_termino']);
        $venta->registrarHoraTermino();
        /*guardamoe el cambios en el archivo xml*/
        $equipos = simplexml_load_file('equipos.xml');
        $resultado = $equipos->xpath('pc[@id="'.$_POST['id'].'"]');
        if($resultado){
            $resultado[0]->horaTermino=$_POST['hora_termino'];
            $equipos->asXml('equipos.xml');
        }
        echo "PC".$_POST['id']." finaliza a la(s) ".$_POST['hora_termino'];
    }
    else if($_POST['accion']=='AgregarDetalleDeVenta'){
        $obj = array("id_producto"=>$_POST['id_producto'],"cantidad"=>$_POST['cantidad'],"precio_unitario"=>$_POST['precio'],"total"=>$_POST['total']);        
        $venta->setNoVenta($_POST['no_venta']);
        $no_reg = $venta->registrarDetalleDeVenta($obj);
        updateXML($_POST['id'],$no_reg, $obj); //actualizar producto en el xml
        echo $no_reg;
    }
    else if($_POST['accion']=='ActualizarDetalleDeVenta'){
        $obj = array("cnsdv"=>$_POST['cnsdv'],"id_producto"=>$_POST['id_producto'],"cantidad"=>$_POST['cantidad'],"precio_unitario"=>$_POST['precio'],"total"=>$_POST['total']);        
        updateXML($_POST['id'],$_POST['cnsdv'], $obj); //actualizar producto en el xml
        if($venta->actualizarDetalleDeVenta($obj))
            echo 1;
        else
            echo 0;
    }
    else if($_POST['accion']=='SetPagado'){
        $venta->setNoVenta($_POST['no_venta']);
        $venta->setPagado($_POST['pagado']);
    }
    else if($_POST['accion']=='AgregarNota'){
        $venta->setNoVenta($_POST['no_venta']);
        $venta->setNota($_POST['nota']);
    }
}
else{
    /*Consultar el progreso y estado de la venta*/
    if (file_exists('equipos.xml'))
    {
        $equipos = simplexml_load_file('equipos.xml');
        echo "<?xml version='1.0'?>";
        $resultado = $equipos->xpath('pc[@id="'.$_POST['id'].'"]');

        if($resultado){
            $hi = $resultado[0]->horaInicio;
            $minutos = calcular_tiempo_trasnc(date("H:i:s"),$hi);
            if($resultado[0]->horaTermino!='00:00:00')
            {
                $resultado[0]->tiempoRestante=calcular_tiempo_trasnc($resultado[0]->horaTermino,date('H:i:s')).' mins';
            }
            $prod = $resultado[0]->prodservs[0]->producto;
            $prod->cantidad = $minutos." mins";
            $prod->total = calcularTotal($minutos);
            /*guardamos los cambios de tiempo y total en la base de datos*/
            include_once('../clases/CVentas.php');
            include_once("../db/AccessDB.php");
            global $db;
            $venta = new CVentas($db);
            $venta->setIdEquipo($_POST['id']);            
            $obj = array("cnsdv"=>$prod['noreg'],"id_producto"=>1,"cantidad"=>$minutos,"precio_unitario"=>10,"total"=>$prod->total);
            $venta->actualizarDetalleDeVenta($obj);
            /*enviamos el estado actual del servicio en el equipo con id x*/
            echo $resultado[0]->asXML();
            $equipos->asXml("equipos.xml"); //guardamos los cambios
            /*cerramos conexion de la BD*/
            $db->close_conn();
        }
    }else{
        echo 'Error abriendo equipos.xml';
    }
}

function registrarEnXML($idpc){
    //echo "Quitando PC";  
    $doc = new DOMDocument('1.0');
    if(empty(simplexml_load_file('equipos.xml'))){
        $doc->load('equiposRespaldo.xml');
    }
    else
        $doc->load('equipos.xml');
    $doc->preserveWhiteSpace = false;
    $doc->formatOutput = true;

    $equipos = $doc->getElementsByTagName('pc');

    foreach ($equipos as $pc) { 
        if($pc->getAttribute('id')==$idpc){
            $pc->parentNode->removeChild($pc);
            $doc->saveXML();
        }
    }
    require_once('../clases/CEquipo.php');
    global $gdb;
    $objequipo = new CEquipo($gdb);
    $objequipo->setIdEquipo($_POST['id']);
    $objequipo->findById();
    $tag = $objequipo->getTag();
    $horainicial = date("H:i:s");
    $xml = <<< XML
<?xml version="1.0" encoding="utf-8"?>
<pc id="$idpc" status="running">
    <Tag>$tag</Tag>
    <horaInicio>$horainicial</horaInicio>
    <horaTermino>00:00:00</horaTermino>
    <tiempoRestante>∞</tiempoRestante>
    <prodservs>
    </prodservs>
</pc>
XML;

 $dom = new DOMDocument;
 $dom->loadXML($xml);
 $dom->saveXML();

 $nodo =  $doc->importNode($dom->getElementsByTagName("pc")->item(0), true);
 $doc->documentElement->appendChild($nodo);

 $doc->saveXML(); //IMPORTANTE PARA QUE SE GUARDE EL DOCUMENTO CON FORMATO
    
 $doc->save("equipos.xml");
 return true;
}

function updateXML($idpc, $no_reg, $objProducto){
    //echo "Quitando PC";
    $doc = simplexml_load_file("equipos.xml");    

    $prodslist = null;
    foreach($doc->pc as $pc)
    {
        if($pc['id'] == $idpc) {
            //echo "equipo encontrado";
            $prodslist = $pc->prodservs;
            foreach($pc->prodservs->producto as $articulo){                            
                if($articulo['noreg']==$no_reg){
                    $dom=dom_import_simplexml($articulo);                    
                    $dom->parentNode->removeChild($dom);
                    break;
                }
            }
        }
    }

    require_once('../clases/CProductos.php');
    global $gdb;
    $producto = new CProductos($gdb);
    $producto->setIdProducto($_POST['id_producto']);
    if($objProducto['cantidad']>0 && $producto->findById()){
        @$arti=$prodslist->addChild('producto');
        @$arti->addAttribute("id",$_POST['id_producto']);
        @$arti->addAttribute("noreg",$no_reg);
        @$arti->addChild('descripcion',$producto->getNombre());
        @$arti->addChild('cantidad',$objProducto['cantidad']);
        @$arti->addChild('total',$objProducto['total']);
    }
    
    $doc->asXml("equipos.xml");
    $doc->asXml("equiposRespaldo.xml");
}

if($gdb!=NULL)
    $gdb->close_conn();
?>