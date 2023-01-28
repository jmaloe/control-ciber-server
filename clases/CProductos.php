<?php
/*Autor: Jesus Malo, support: dic.malo@gmail.com*/
 require_once("../db/ConexionDB.php");

 class CProductos extends ConexionDB{
 	var $id_producto,
 		$nombre,
		$descripcion,
		$precioCompra,
		$precioVenta;

 	function __construct($db)
	{
    	parent::__construct($db); /*invocar el constructor de la clase padre*/
 	}

 	//setters
 	function setIdProducto($id){
 		$this->id_producto = $id;
 	}

 	function setNombre($n){
 		$this->nombre = $this->scapeString($n);
 	}

 	function setDescripcion($d){
 		$this->descripcion = $this->scapeString($d);
 	}

 	function setPrecioCompra($pc){
 		$this->precioCompra = $pc;
 	}

 	function setPrecioVenta($pv){
 		$this->precioVenta = $pv;
 	}

 	//getters
 	function getIdProducto(){
 		return $this->id_producto;
 	}

 	function getNombre(){
 		return $this->nombre;
 	}

 	function getDescripcion(){
 		return $this->descripcion;
 	}

 	function getPrecioCompra(){
 		return $this->precioCompra;
 	}

 	function getPrecioVenta(){
 		return $this->precioVenta;
 	}

 	function agregarProducto(){
 	 	$sql = "INSERT INTO Producto(nombre,descripcion,precio_compra,precio_venta) VALUES('".$this->nombre."','".$this->descripcion."',".$this->precioCompra.",".$this->precioVenta.";";
 	 	$this->query($sql);
 	 	$this->id_producto = $this->getInsertId();
 	}

 	function getProductos(){
 	 	$sql = "SELECT id_producto as id, concat(nombre,' $',precio_venta) as value, precio_venta FROM Producto order by nombre";
 	 	$resultado = $this->query($sql);
 	 	if($resultado)
 	 		return $this->getCustomDataListItems($resultado);	
 	 	else
 	 	{
 	 		//echo $this->getError();
 	 		return;
 	 	}
 	}

 	function getListaPrecios(){
 		$sql = "SELECT id_producto as id, nombre, descripcion, precio_venta FROM Producto order by nombre";
 	 	$resultado = $this->query($sql);
 	 	$cont=1;
 	 	if($resultado){
 	 		echo '<table width="100%">';
 	 		echo '<tr>';
 	 			echo '<th>#</th>';
 	 			echo '<th>ID</th>';
 	 			echo '<th>Nombre</th>';
 	 			echo '<th>Descripcion</th>';
 	 			echo '<th>Precio</th>';
 	 		echo '</tr>';
 	 	}
 	 	$flag=true;
 	 	while($data = mysqli_fetch_row($resultado)){
 	 		if($flag){
 	 			$flag=false;
 	 			echo '<tr style="background-color:#F2F2F2">';
 	 		}
 	 		else{
 	 		  echo '<tr>';
 	 		  $flag=true;
 	 		}
 	 			echo '<td>'.$cont.'</td>'; //cns
 	 			echo '<td style="color:lightgray">'.$data[0].'</td>'; //ID
 	 			echo '<td>'.$data[1].'</td>'; //Nombre
 	 			echo '<td>'.$data[2].'</td>'; //Descripcion
 	 			echo '<td style="text-align:right !important;">'.$data[3].'</td>'; //precio
 	 		echo '</tr>';
 	 		$cont++;
 	 	}
 	 	if($resultado)
 	 		echo '</table>';
 	}

 	function findById(){
 		$sql = "SELECT id_producto, nombre, descripcion, precio_compra, precio_venta FROM Producto WHERE id_producto=".$this->id_producto;
 	 	$resultado = $this->query($sql);
 	 	if($resultado){
 	 		$dato = mysqli_fetch_assoc($resultado);
 	 		$this->setIdProducto($dato['id_producto']);
 	 		$this->setNombre($dato['nombre']);
 	 		$this->setDescripcion($dato['descripcion']);
 	 		$this->setPrecioCompra($dato['precio_compra']);
 	 		$this->setPrecioVenta($dato['precio_venta']);
 	 		return true;
 	 	}
 	 	else
 	 	{ 	 		
 	 		return false;
 	 	}
 	}
 }
?>