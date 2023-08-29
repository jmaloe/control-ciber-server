<?php
/*Autor: Jesus Malo, support: dic.malo@gmail.com*/
 require_once("../db/ConexionDB.php");

 class CEquipo extends ConexionDB{
 	var $id_equipo,
 		$id_categoria,
 		$serial,
		$tag,
		$fechaRegistro,
		$fechaDeBaja,
		$truefalse=true,
		$total=0;

 	function __construct($db)
	{
    	parent::__construct($db); /*invocar el constructor de la clase padre*/
 	}

 	//setters
 	function setIdEquipo($id){
 		$this->id_equipo = $id;
 	}

 	function setIdCategoria($idc){
 		$this->id_categoria = $idc;
 	}

 	function setSerial($s){
 		$this->serial = $this->scapeString($s);
 	}

 	function setTag($t){
 		$this->tag = $this->scapeString($t);
 	}

 	function setFechaRegistro($fr){
 		$this->fechaRegistro = $this->scapeString($this->getFechaToMysql($fr));
 	}

 	function setFechaBaja($fb){
 		$this->fechaBaja = $this->scapeString($this->getFechaToMysql($fb));
 	}

 	//getters
 	function getIdEquipo(){
 		return $this->id_equipo;
 	}

 	function getIdCategoria(){
 		return $this->id_categoria;
 	}

 	function getSerial(){
 		return $this->serial;
 	}

 	function getTag(){
 		return $this->tag;
 	}

 	function getFechaRegistro(){
 		return $this->fechaRegistro;
 	}

 	function getFechaBaja(){
 		return $this->fechaDeBaja;
 	}

 	function agregarEquipo(){
 	 	$sql = "INSERT INTO Equipo(serial,tag) VALUES('".$this->serial."','".$this->tag."';";
 	 	$this->query($sql);
 	 	$this->id_equipo = $this->getInsertId();
 	}

 	function findById(){
 		$sql = "SELECT id_equipo, id_cat, serial,tag,fechaRegistro,fechaDeBaja FROM Equipo WHERE id_equipo=".$this->id_equipo;
 	 	$resultado = $this->query($sql); 	 	
 	 	if($resultado){
 	 		$dato = mysqli_fetch_assoc($resultado);
 	 		$this->setIdEquipo($dato['id_equipo']);
 	 		$this->setIdCategoria($dato['id_cat']);
 	 		$this->setSerial($dato['serial']);
			$this->setTag($dato['tag']);
			$this->setFechaRegistro($dato['fechaRegistro']);
			$this->setFechaBaja($dato['fechaDeBaja']);
 	 	}
 	}

 	function getEquiposActivos(){
 	 	$sql = "SELECT e.id_equipo, tag, icon, v.no_venta, nota, ce.clase, ce.id_cat, v.hora_inicio, v.hora_termino, v.pago_anticipado FROM Equipo e, CategoriaEquipo ce, Venta v where v.id_equipo=e.id_equipo AND ce.id_cat=e.id_cat AND v.openorclosed=0 AND fechaDeBaja IS NULL order by e.id_cat DESC, tag ASC";
 	 	$resultado = $this->query($sql); 	 	
 	 	
 	 	if($resultado)
 	 	while($dato = mysqli_fetch_assoc($resultado))
 	 	{
		  	$this->doBlock($dato,$dato['clase']);
			$this->truefalse?$this->truefalse=false:$this->truefalse=true;
 	 	}
 	 	else
 	 	{
 	 		echo $this->getError();
 	 	}
 	}

 	function getEquiposInactivos(){
 		$sql = "SELECT e.id_equipo, tag, icon, ce.id_cat,ce.clase, NULL as 'hora_inicio', NULL as 'hora_termino' FROM Equipo e, CategoriaEquipo ce where ce.id_cat=e.id_cat AND e.id_equipo NOT IN(select id_equipo from Venta where openorclosed=0) AND fechaDeBaja IS NULL order by e.id_cat DESC, tag ASC";
 	 	$resultado = $this->query($sql); 	 	
 	 	
 	 	if($resultado)
 	 	{ 	 		
 	 		while($dato = mysqli_fetch_assoc($resultado))
 	 		{
 	 			$this->getDeviceHTMLTag($dato,$dato['clase']);		  		
				$this->truefalse?$this->truefalse=false:$this->truefalse=true;
 	 		} 	 		
 	 	}
 	 	else
 	 	{
 	 		echo $this->getError();
 	 	}
 	}

 	function getDeviceHTMLTag($dato,$btn){
 		$no_venta="";
 		$cnsdv=0; 	
 		$this->total=0;	
 		if(isset($dato['no_venta'])){
 			$no_venta='no_venta="'.$dato['no_venta'].'"';
 			$cnsdv=$dato['no_venta'];
 		}
 		
 		$htmlData='<div class="cuatro free-devices" id="'.$dato['id_equipo'].'"'.($this->truefalse?' class="fondo-equipos"':'').' categoria="'.$dato['id_cat'].'" '.$no_venta.'>
					<div class="doce" style="display:inline-block;">
						<div class="doce">
							<button type="button" class="btn '.$btn.' comenzar" value="'.$dato['id_equipo'].'" style="width:100%" title="Click para iniciar">'.$dato['tag'].' <img src="../imagenes/'.$dato['icon'].'" width="18px"/></button>
						</div>						
					</div>					
				  </div>';
		echo $htmlData;
 	}

 	function doBlock($dato,$btn){
 		$no_venta="";
 		$cnsdv=0; 	
 		$this->total=0;	
 		if(isset($dato['no_venta'])){
 			$no_venta='no_venta="'.$dato['no_venta'].'"';
 			$cnsdv=$dato['no_venta'];
 		}
 		
 		$htmlData='<div name="'.$dato['tag'].'" id="'.$dato['id_equipo'].'"'.($this->truefalse?' class="fondo-equipos device"':'class="device"').' categoria="'.$dato['id_cat'].'" '.$no_venta.'>
					<div class="tres" style="display:inline-block;">
						<div class="doce">
							<button type="button" class="comenzar finalizar btn '.$btn.'" value="'.$dato['id_equipo'].'" style="width:100%" title="Click para finalizar">'.$dato['tag'].' <img src="../imagenes/'.$dato['icon'].'" width="18px"/></button>
						</div>
						<div class="doce"><input type="time" class="hi" style="width:100%" value="'.$dato['hora_inicio'].'"/></div>
						<div class="doce"><input type="time" class="hf" style="width:100%" value="'.$dato['hora_termino'].'"/></div>
					</div>
					<div class="nueve" style="display: inline-block;" id="prodsxeq_'.$dato['id_equipo'].'">
						<button type="button" class="btn btn-info agregar-articulo" value="prodsxeq_'.$dato['id_equipo'].'" title="Agregar producto/servicio"><i class="fa fa-plus" aria-hidden="true"></i></button>						
						<button type="button" class="btn btn-light '.($dato['pago_anticipado']==1?"moneda-on":"moneda-off").' checkpagado" value="'.$dato['id_equipo'].'" title="Marcar como pagado"> </button>
						<input type="text" name="nota" class="nota form-control" placeholder="Comentario" value="'.$dato['nota'].'">
						<button type="button" class="btn btn-success pagado" style="display:none; margin-left:5px;">Pagado</button>
						<!-- Aqui se agregan dinamicamente los articulos -->
						'.$this->getDetalleDeVenta($cnsdv).'
					</div>					
					<div class="doce">
						<label class="totalxeq" style="text-align:right; padding-right:5px">
							Total: $<b>'.$this->total.'</b>
						</label>						
					</div>
					<hr/>
				</div>';
		echo $htmlData;
 	}

 	function getDetalleDeVenta($no_venta){
 		if($no_venta==0)
 			return;
 		$sql = "SELECT p.id_producto,dv.cnsdv,concat(p.nombre,' $',p.precio_venta) as nombre, dv.cantidad, dv.precio_unitario, dv.total FROM detalledeventa dv, producto p WHERE dv.id_producto=p.id_producto AND dv.no_venta=".$no_venta;
 	 	$resultado = $this->query($sql); 	 	
 	 	$detalle = "";
 	 	$this->total=0;
 	 	$idProductoIsOne=""; 	 	
 	 	if($resultado)
 	 	{
	 	 	while($dato = mysqli_fetch_assoc($resultado))
	 	 	{
	 	 		if($dato['id_producto']==1)
	 	 			$idProductoIsOne=' style="background-color:#D8D8D8"';
	 	 		else
	 	 			$idProductoIsOne="";
			  	$detalle.='<div class="doce" no_registro="'.$dato['cnsdv'].'">
						<div class="seis">
							<input list="articulos" class="seleccion-articulo form-control" autocomplete="off" value="'.$dato['nombre'].'" '.$idProductoIsOne.'>
						</div>
						<div class="dos">
							<input type="number" class="cantidad" value="'.$dato['cantidad'].'" '.$idProductoIsOne.' title="Enter para agregar mas articulos">
						</div>
						<div class="dos">
							<input type="number" class="precio" value="'.$dato['precio_unitario'].'" '.$idProductoIsOne.'/>
						</div>
						<div class="dos">
							<input type="number" class="total" value="'.$dato['total'].'" disabled />
						</div>
					</div>';
					$this->total+=$dato['total'];
	 	 	}
	 	}
 	 	else
 	 	{
 	 		echo $this->getError();
 	 	}
 	 	return $detalle;
 	}
 }
?>