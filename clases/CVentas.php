<?php
/*Autor: Jesus Malo, support: dic.malo@gmail.com*/
 require_once("../db/ConexionDB.php");

 class CVentas extends ConexionDB{
 	var $no_venta,
 		$id_equipo,
 		$fecha,
 		$horaInicio,
 		$horaTermino,
 		$total=0,
 		$idUser=1;

 	function __construct($db)
	{
    	parent::__construct($db); /*invocar el constructor de la clase padre*/
 	}

 	//setters
 	function setNoVenta($nv){
 		$this->no_venta = $nv;
 	}

 	function setIdEquipo($ide){
 		$this->id_equipo = $ide;
 	}

 	function setFecha($fc){
 		$this->fecha = $this->getFechaToMysql($fc);
 	}
	
 	function setHoraInicio($hi){
 		$this->horaInicio = $hi;
 	}

 	function setHoraTermino($ht){
 		$this->horaTermino = $ht;
 	}

 	function setTotal($tot){
 		$this->total = $tot;
 	}

 	function setIdUser($idu){
 		$this->idUser = $idu;
 	}

 	//getters
 	function getNoVenta(){
 		return $this->no_venta;
 	}

 	function getIdEquipo(){
 		return $this->id_equipo;
 	}

 	function getFecha(){
 		return $this->fecha;
 	}

 	function getHoraInicio(){
 		return $this->horaInicio; 		
 	}

 	function getHoraTermino(){
 		return $this->horaTermino;
 	}

 	function getTotal(){
 		return $this->total;
 	}

 	function getIdUser(){
 		return $this->idUser;
 	}

 	function checkIfRunning(){
 		$sql = "SELECT no_venta FROM Venta WHERE id_equipo=".$this->id_equipo." AND openorclosed=0";
 		$resultado = $this->query($sql);
 	 	if($resultado)
 	 	{
 	 		if($dato = mysqli_fetch_assoc($resultado))
 	 		{
 				return 0;
 	 		}
 	 		else
 	 			return 1;
 	 	}
 	 	else
 	 		echo $this->getError();	
 	}

 	function registrarVenta(){
 	 	$sql = "INSERT INTO Venta(id_equipo,hora_inicio,idUser) VALUES(".$this->id_equipo.",'".$this->horaInicio."',".$this->idUser.");";
 	 	if($this->query($sql)){
 	 		$this->no_venta = $this->getInsertId();
 	 	}
 	 	else
 	 		echo $this->getError();
 	}

 	function finalizarVenta(){
 		$sql = "UPDATE Venta SET hora_termino='".$this->horaTermino."',total=".$this->total.",openorclosed=1 WHERE id_equipo=".$this->id_equipo." AND openorclosed=0;";
 	 	if($this->query($sql))
 	 		return true;
 	 	else
 	 		echo $this->getError();
 	}

 	function registrarDetalleDeVenta($ddv){
 	 	$sql = "INSERT INTO DetalleDeVenta(no_venta,id_producto,cantidad,precio_unitario,total) VALUES(".$this->no_venta.",".$ddv['id_producto'].",".$ddv['cantidad'].",".$ddv['precio_unitario'].",".$ddv['total'].");";
 	 	if(!$this->query($sql)){
 	 		echo $this->getError();
 	 	}
 	 	return $this->getInsertId();
 	}

 	function actualizarDetalleDeVenta($ddv){
 		$sql = "UPDATE DetalleDeVenta SET id_producto=".$ddv['id_producto'].",cantidad=".$ddv['cantidad'].",precio_unitario=".$ddv['precio_unitario'].",total=".$ddv['total']." WHERE cnsdv=".$ddv['cnsdv'];
 	 	if($this->query($sql))
 	 		return true;
 	 	else
 	 		echo $this->getError();
 	}

 	function registrarHoraInicio(){
 		$sql = "UPDATE Venta SET hora_inicio='".$this->horaInicio."' WHERE id_equipo=".$this->id_equipo." AND openorclosed=0;";
 	 	if($this->query($sql))
 	 		return true;
 	 	else
 	 		echo $this->getError();	
 	}

 	function registrarHoraTermino(){
 		$sql = "UPDATE Venta SET hora_termino='".$this->horaTermino."' WHERE id_equipo=".$this->id_equipo." AND openorclosed=0;";
 	 	if($this->query($sql))
 	 		return true;
 	 	else
 	 		echo $this->getError();	
 	}

 	function getHistorialVentas(){
 		$sql = "SELECT no_venta,v.id_equipo,e.tag,hora_inicio,hora_termino,nota,total FROM Venta v, Equipo e WHERE e.id_equipo=v.id_equipo AND DATE(fecha)='".$this->fecha."' ORDER BY no_venta DESC";
 		$resultado = $this->query($sql);
 		$ventatotal=0; 		
 		$flag=0;
 	 	if($resultado)
 	 	{
 	 		echo '<table width="100%">';
 	 		echo '<tr><th>#Venta</th><th>#PC</th><th>Descripcion</th><th>Hora inicio</th><th>Hora Termino</th><th>Observaciones</th><th>Total</th></tr>';
 	 		while($dato = mysqli_fetch_assoc($resultado))
 	 		{ 	 			
 				if($flag==0)
 				{
 					echo '<tr style="background-color:#F2F2F2" value="'.$dato['no_venta'].'">';
 					$flag=1;
 				}
 				else{
 					echo '<tr value="'.$dato['no_venta'].'">';
 					$flag=0;
 				} 	 			
 	 			echo '<td>'.$dato['no_venta'].'</td>';
 	 			echo '<td>'.$dato['id_equipo'].'</td>';
 	 			echo '<td>'.$dato['tag'].'</td>'; 	 			
 	 			echo '<td>'.$dato['hora_inicio'].'</td>';
 	 			echo '<td>'.$dato['hora_termino'].'</td>';
 	 			echo '<td>'.$dato['nota'].'</td>';
 	 			echo '<td>'.$dato['total'].'</td>';
 	 			echo '<td><button class="btn btn-danger" name="eliminar" value="'.$dato['no_venta'].'"><i class="fa fa-trash" aria-hidden="true"></i></td></button>';
 	 			echo '</tr>';
 	 			$ventatotal+=$dato['total']; 	 			
 	 		}
 	 			echo '<tr>';
 	 			echo '<td colspan="6">Total</td>';
 	 			echo '<td>'.$ventatotal.'</td>'; 	 			
 	 			echo '</tr>';
 	 		echo '</table>';
 	 	}
 	 	else
 	 		echo $this->getError();	
 	}

 	function getDetalleVenta(){
 		$sql = "select cnsdv,dv.id_producto,p.nombre,p.descripcion,dv.cantidad,dv.precio_unitario,dv.total from detalledeventa dv, producto p where p.id_producto=dv.id_producto and dv.no_venta=".$this->no_venta;
 		$resultado = $this->query($sql);
 		$ventatotal=0; 		
 		$flag=0;
 	 	if($resultado)
 	 	{
 	 		echo '<table width="100%">';
 	 		echo '<tr><th>CNSDV</th><th>IDProducto</th><th>Nombre</th><th>Descripción</th><th>Cantidad</th><th>Precio</th><th>Total</th></tr>';
 	 		while($dato = mysqli_fetch_assoc($resultado))
 	 		{ 	 			
 				if($flag==0)
 				{
 					echo '<tr style="background-color:#F2F2F2">';
 					$flag=1;
 				}
 				else{
 					echo '<tr>';
 					$flag=0;
 				} 	 			
 	 			echo '<td>'.$dato['cnsdv'].'</td>';
 	 			echo '<td>'.$dato['id_producto'].'</td>';
 	 			echo '<td>'.$dato['nombre'].'</td>';
 	 			echo '<td>'.$dato['descripcion'].'</td>';
 	 			echo '<td>'.$dato['cantidad'].'</td>';
 	 			echo '<td>'.$dato['precio_unitario'].'</td>';
 	 			echo '<td>'.$dato['total'].'</td>';
 	 			echo '</tr>';
 	 			$ventatotal+=$dato['total'];
 	 		}
 	 			echo '<tr>';
 	 			echo '<td colspan="6"></td>';
 	 			echo '<td><b>'.$ventatotal.'</b></td>';
 	 			echo '</tr>';
 	 		echo '</table>';
 	 	}
 	 	else
 	 		echo $this->getError();	
 	}

 	function eliminarVenta(){
 		$sql = "DELETE FROM Venta WHERE no_venta=".$this->no_venta.";";
 	 	if($this->query($sql))
 	 		return true;
 	 	else
 	 		echo $this->getError();	
 	}

 	function setPagado($dato){
 		$sql = "UPDATE Venta SET pago_anticipado=".$dato." WHERE no_venta=".$this->no_venta.";";
 	 	if($this->query($sql))
 	 		return true;
 	 	else
 	 		echo $this->getError();	
 	}

 	function setNota($dato){
 		$sql = "UPDATE Venta SET nota='".$dato."' WHERE no_venta=".$this->no_venta.";";
 	 	if($this->query($sql))
 	 		return true;
 	 	else
 	 		echo $this->getError();		
 	}
 }
?>