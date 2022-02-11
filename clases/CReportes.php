<?php
/*Autor: Jesus Malo, support: dic.malo@gmail.com 13-08-2015*/
date_default_timezone_set('America/Mexico_City');
require_once('../db/ConexionDB.php');

class CReportes extends ConexionDB{
	
 var $f_inicial, $f_final, $labels, $values, $total_efectivo;
 
 function __construct($db)
 {
    parent::__construct($db); /*invocar el constructor de la clase padre*/
 }
 
 function setFechaInicial($fi){
	 $this->f_inicial = $fi;
 }
 
 function setFechaFinal($ff){
	 $this->f_final = $ff;
 }
 
 function getFechaInicial(){
	 return $this->f_inicial;
 }
 
 function getFechaFinal(){
	 return $this->f_final;
 }

 function getGraficas($graf){
 	if($this->getReporte($graf))
 	{
 		echo $this->getLineChart();
 		echo $this->getBarChart();
 		echo $this->getRadarChart();
 		echo $this->getPolarAreaChart("dataG4");
 		echo $this->getPieChart("dataG5");
 		return true;
 	}
 	return false;
 }

 function getReporte($graf){
    $sql=null;
    $granTotal=0;    
    $this->total_efectivo=0;
    switch ($graf) {
        case 'PorProducto':
            $sql = 'SELECT IF(dv.id_producto=1,sum(dv.cantidad)/60,sum(dv.cantidad)) as cantidad, sum(dv.total) FROM detalledeventa dv, venta v, producto p WHERE dv.no_venta=v.no_venta AND p.id_producto=dv.id_producto AND v.fecha BETWEEN \''.$this->scapeString($this->getFechaToMysql($this->f_inicial)).'\' AND date(\''.$this->scapeString($this->getFechaToMysql($this->f_final)).'\')+1 GROUP BY dv.id_producto ORDER BY dv.id_producto;';
            $granTotal = $this->getGranTotal($sql);
            $sql = 'SELECT p.nombre, IF(dv.id_producto=1,sum(dv.cantidad)/60,sum(dv.cantidad)) as total FROM detalledeventa dv, venta v, producto p WHERE dv.no_venta=v.no_venta AND p.id_producto=dv.id_producto AND v.fecha BETWEEN \''.$this->scapeString($this->getFechaToMysql($this->f_inicial)).'\' AND date(\''.$this->scapeString($this->getFechaToMysql($this->f_final)).'\')+1 GROUP BY dv.id_producto ORDER BY dv.id_producto;';
            if($this->armarGraficas($sql,$granTotal))
                return true;
            break;
        case 'VentaDiaria':
            $sql = 'select sum(total) as total1, sum(total) as total from venta where fecha between \''.$this->scapeString($this->getFechaToMysql($this->f_inicial)).'\' AND date(\''.$this->scapeString($this->getFechaToMysql($this->f_final)).'\')+1 group by date(fecha);';
            $granTotal = $this->getGranTotal($sql);
            $sql = 'select date(fecha) as fecha,sum(total) as total from venta where fecha between \''.$this->scapeString($this->getFechaToMysql($this->f_inicial)).'\' AND date(\''.$this->scapeString($this->getFechaToMysql($this->f_final)).'\')+1 group by date(fecha);';
            if($this->armarGraficas($sql,$granTotal))
                return true;
            break;
        case 'VentaMensual':
            $sql = 'select sum(total) as tot, sum(total) as total from venta where fecha between \''.$this->scapeString($this->getFechaToMysql($this->f_inicial)).'\' AND date(\''.$this->scapeString($this->getFechaToMysql($this->f_final)).'\')+1 group by month(fecha);';
            $granTotal = $this->getGranTotal($sql);
            $sql = 'select MONTHNAME(STR_TO_DATE(month(fecha), \'%m\')), sum(total) as total from venta where fecha between \''.$this->scapeString($this->getFechaToMysql($this->f_inicial)).'\' AND date(\''.$this->scapeString($this->getFechaToMysql($this->f_final)).'\')+1 group by month(fecha);';
            if($this->armarGraficas($sql,$granTotal))
                return true;
            break;
        default:
            # code...
            break;
    }    
 	return false;
 }

 function getGranTotal($sql){
    $resultado = $this->query($sql);    
    $granTotal=0;   
    while($fila=mysqli_fetch_row($resultado)){
        $granTotal+=$fila[0];
        $this->total_efectivo+=$fila[1];
    }
    return $granTotal;
 }

 function armarGraficas($sql, $granTotal){
    $cont=0;
    $resultado = $this->query($sql);
    while($fila=mysqli_fetch_row($resultado)){
        if($cont==0)
        {
            $cont=1;
        }
        else
        {
            $this->labels.=",";
            $this->values.=",";
        }
        $this->labels.='"'.$fila[0].': '.number_format((float)($fila[1]*100/($granTotal)), 1, '.', '').'%"';
        $this->values.=$fila[1];
    }
    if($cont==1)
        return true;
    return false;
 }

 function getTotalEfectivo(){
    return $this->total_efectivo;
 }

 function getLineChart(){
 	return chr(13).'var dataG1 = {
    labels: ['.$this->labels.'],
    datasets: [
        {
            label: "Grafia de lineas",
            fillColor: "rgba(220,220,220,0.2)",
            strokeColor: "rgba(220,220,220,1)",
            pointColor: "rgba(220,220,220,1)",
            pointStrokeColor: "#fff",
            pointHighlightFill: "#fff",
            pointHighlightStroke: "rgba(220,220,220,1)",
            data: ['.$this->values.']
        }
    ]
	};';
 }

 function getBarChart(){
 	return chr(13).'var dataG2 = {
    labels: ['.$this->labels.'],
    datasets: [
        {
            label: "Grafica de barras",
            fillColor: "rgba(220,220,220,0.5)",
            strokeColor: "rgba(220,220,220,0.8)",
            highlightFill: "rgba(220,220,220,0.75)",
            highlightStroke: "rgba(220,220,220,1)",
            data: ['.$this->values.']
        }
    ]
	};';
 }

 function getRadarChart(){
 	return chr(13).'var dataG3 = {
    labels: ['.$this->labels.'],
    datasets: [
        {
            label: "Grafica tipo radar",
            fillColor: "rgba(220,220,220,0.2)",
            strokeColor: "rgba(220,220,220,1)",
            pointColor: "rgba(220,220,220,1)",
            pointStrokeColor: "#fff",
            pointHighlightFill: "#fff",
            pointHighlightStroke: "rgba(220,220,220,1)",
            data: ['.$this->values.']
        }
    ]
	};';
 }

 function getPolarAreaChart($varName){
 	$etiquetas = explode(',',$this->labels);
 	$valores = explode(',',$this->values);
 	$data = chr(13).'var '.$varName.'=[';
 	for($i=0; $i<count($etiquetas); $i++){
 		if($i>0)
 			$data.=','.chr(13);
 		$data.='{';
 		$data.='label:'.$etiquetas[$i].',';
 		$data.='value:'.$valores[$i].',';
 		$data.='color:"#'.str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT).'",';
 		$data.='highlight:"#1c76c5"';
 		$data.='}';
 	}
 	$data.="];".chr(13);
 	return $data;
 }

 function getPieChart($varName){
 	return $this->getPolarAreaChart($varName);
 }
}
?>