<?php
/*Autor: Jesus Malo, support: dic.malo@gmail.com*/
 require_once("../db/ConexionDB.php");

 class CRegistro extends ConexionDB{
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
 	 	$sql = "SELECT id_producto as id, nombre as value, precio_venta FROM Producto";
 	 	$resultado = $this->query($sql);
 	 	if($resultado)
 	 		return $this->getCustomDataListItems($resultado);	
 	 	else
 	 	{
 	 		//echo $this->getError();
 	 		return;
 	 	}
 	}
 }
?>