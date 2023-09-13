<?php
/*Autor: Jesus Malo, support: dic.malo@gmail.com*/
 require_once("../db/ConexionDB.php");

 class CVentas extends ConexionDB{
 	var $no_venta,
 		$id_equipo,
 		$fecha_inicial,
		$fecha_final,
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

 	function setFechaInicial($fc){
 		$this->fecha_inicial = $this->getFechaToMysql($fc);
 	}

	 function setFechaFinal($fc){
		$this->fecha_final = $this->getFechaToMysql($fc);
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

 	function getFechaInicial(){
 		return $this->fecha_inicial;
 	}

 	function getFechaFinal(){
 		return $this->fecha_final;
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
 		$sql = "SELECT 
					no_venta,
					v.id_equipo,
					e.tag,
					DATE(fecha) as fecha,
					hora_inicio,
					hora_termino,
					nota,
					total 
				FROM 
					Venta v, 
					Equipo e 
				WHERE 
					e.id_equipo = v.id_equipo 
					AND DATE(fecha) BETWEEN '".$this->fecha_inicial."' AND '".$this->fecha_final."' 
				ORDER BY 
					no_venta DESC";
		
 		$resultado = $this->query($sql);
 		$ventatotal = 0;
		$ventaxdia = 0;
 		$flag = 0;
		$flagFecha = "";
 	 	if($resultado)
 	 	{
 	 		echo '<table width="100%">';
 	 		echo "<tr>
					<th>#Venta</th>
					<th>#PC</th>
					<th>Descripcion</th>
					<th>Hr. inicio</th>
					<th>Hr. Termino</th>
					<th>Obs.</th>
					<th>Total</th>
				  </tr>";
 	 		while($dato = mysqli_fetch_assoc($resultado))
 	 		{
				if($flagFecha!=$dato['fecha'] && $ventaxdia > 0){
					//agregamos el total de la venta por dia
					echo '<tr>';
					echo '<td colspan="6" style="text-align:right !important">Total</td>';
					echo '<td>$'.number_format($ventaxdia).'</td>';
					echo '</tr>';
					$ventaxdia = 0;
				}

				//identificamos las fechas y las agrupamos
				if($flagFecha!=$dato['fecha']){
					$flagFecha = $dato['fecha'];
					echo "<tr style='background-color:lightblue'><td colspan='8'>$flagFecha</td></tr>";
				}

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
 	 			echo '<td colspan="1">'.$dato['nota'].'</td>';
 	 			echo '<td>'.$dato['total'].'</td>';
 	 			echo '<td><button type="submit" class="btn btn-danger" name="eliminar" value="'.$dato['no_venta'].'"><i class="fa fa-trash" aria-hidden="true"></i></td></button>';
 	 			echo '</tr>';
				$ventaxdia += $dato['total'];
 	 			$ventatotal += $dato['total']; 	 			
 	 		}
				//total venta por dia
				echo '<tr>';
				echo '<td colspan="6" style="text-align:right !important">Total</td>';
				echo '<td>$'.number_format($ventaxdia).'</td>';
				echo '</tr>';
 	 			echo '<tr>';
				//gran total
 	 			echo '<td colspan="6" style="text-align:right !important"><b>Venta total:</b></td>';
 	 			echo '<td><b>$'.number_format($ventatotal, 2).'</b></td>'; 	 			
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