<?php
/*Autor: Jesus Malo, support: dic.malo@gmail.com*/
require_once('../db/ConexionDB.php');

class CUsuario extends ConexionDB{
 var $idUsuario=-1,
 	 $usuario, 
 	 $password,
 	 $nombre,
 	 $email,
 	 $esVigente=-1,
 	 $fecha_alta;

 function __construct($db)
 {
    parent::__construct($db); /*invocar el constructor de la clase padre*/
 }

 function setIdUsuario($idU){
 	$this->idUsuario = $idU;
 }

 function setUsuario($usr){
 	$this->usuario = $this->scapeString($usr);
 }

 function setPassword($passwd){
 	if($passwd!="******")
 		$this->password = md5($this->scapeString($passwd));
 	else
 		$this->password =  false;
 }

 function setNombre($nom){
 	$this->nombre = $this->scapeString($nom);
 }

 function setEmail($correo){
 	$this->email = $this->scapeString($correo);
 }

 function setActivo($tf){
 	$this->esVigente = $tf;
 }

 function setFechaAlta($fa){
 	$this->fecha_alta = $fa;
 }

 function getIdUsuario(){
 	return $this->idUsuario;
 }

 function getUsuario(){
 	return $this->usuario;
 }

 function getPassword(){
 	return $this->password;
 }

 function getNombre(){
 	return $this->nombre;
 }

 function getEmail(){
 	return $this->email;
 }

 function isActivo(){
 	if($this->esVigente==1)
 		return true;
 	else
 		return false; 	
 }

 function getFechaAlta(){
 	return $this->fecha_alta;
 }

 function autenticar(){
 	$sql = "SELECT idUser,nombre, email, vigente, fechaCaptura FROM usuarios WHERE user='".$this->usuario."' AND password='".$this->getPassword()."';";
 	$resultado = $this->query($sql);
 	 if($dato = mysqli_fetch_assoc($resultado)){
 	 	$this->setIdUsuario($dato['idUser']);
 	 	$this->setNombre($dato['nombre']);
 	 	$this->setEmail($dato['email']);
 	 	$this->setActivo($dato['vigente']);
 	 	$this->setFechaAlta($dato['fechaCaptura']); 	 	
 	 	return true;
 	 }
 	 return false; 	 
 }

 function guardar(){
 	if($this->buscarByUser())
 	{
 		$this->setError("EL USUARIO YA EXISTE");
 		return false;
 	}
 	
 	$sql = "INSERT INTO usuarios(user,password,nombre,email,creado_por) VALUES('".$this->usuario."','".$this->password."','".$this->nombre."','".$this->email."',".$_SESSION["ID_USER"].");";
 	if($this->update($sql)){
 		$this->setIdUsuario( $this->getInsertId());
 		return true;
 	}
 	return false;
 }

 function actualizar(){
 	if($this->password!=false)
 	{
 		if($this->esVigente!=-1)
 			$sql = "UPDATE usuarios SET  password='".$this->password."', nombre='".$this->nombre."', email='".$this->email."', vigente=".$this->esVigente." WHERE user='".$this->usuario."';";
 		else
 			$sql = "UPDATE usuarios SET  password='".$this->password."', nombre='".$this->nombre."', email='".$this->email."' WHERE user='".$this->usuario."';";
 	}
 	else
 	{
 		if($this->esVigente!=-1)
 			$sql = "UPDATE usuarios SET  nombre='".$this->nombre."', email='".$this->email."', vigente=".$this->esVigente." WHERE user='".$this->usuario."';";
 		else
 			$sql = "UPDATE usuarios SET  nombre='".$this->nombre."', email='".$this->email."' WHERE user='".$this->usuario."';";
 	} 	
 	if($this->update($sql)){
 		return true;
 	}
 	return false;
 }

 function eliminar(){
 	$sql = "DELETE FROM usuarios WHERE user='".$this->usuario."';";
 	if($this->update($sql)){
 		return true;
 	}
 	return false;
 }

 function buscarById(){
 	$sql = "SELECT idUser,user,password,nombre,email,vigente,fechaCaptura FROM usuarios WHERE idUser=".$this->getIdUsuario().";";
 	$resultado = $this->query($sql); 	
 	if($this->loadUser($resultado))
 	{
 		return true;
 	}
 	return false;
 }

 function buscarByUser(){
 	$sql = "SELECT idUser,user,password,nombre,email,vigente,fechaCaptura FROM usuarios WHERE user='".$this->usuario."';"; 	
 	$resultado = $this->query($sql); 	
 	if($this->loadUser($resultado))
 	{
 		return true;
 	}
 	return false;
 }

 function buscarByNombre(){
 	$sql = "SELECT idUser,user,password,nombre,email,vigente,fechaCaptura FROM usuarios WHERE nombre like '%".$this->nombre."%';";
 	$resultado = $this->query($sql);
 	if($this->loadUser($resultado))
 	{
 		return true;
 	}
 	return false;
 }

 function buscarByEmail(){
 	$sql = "SELECT idUser,user,password,nombre,email,vigente,fechaCaptura FROM usuarios WHERE email='".$this->email."';";
 	$resultado = $this->query($sql);
 	if($this->loadUser($resultado))
 	{
 		return true;
 	}
 	return false;
 }

 function loadUser($result){
 	if($data = mysqli_fetch_assoc($result))
 	{ 		
 		$this->setIdUsuario($data['idUser']);
 		$this->setUsuario($data['user']);
 		$this->setPassword($data['password']);
 		$this->setNombre($data['nombre']);
 		$this->setEmail($data['email']);
 		$this->setActivo($data['vigente']);
 		$this->setFechaAlta($data['fechaCaptura']);
 		return true;
 	}
 	return false;
 }

 function getListaUsuariosConRol(){
 	$sql = "SELECT u.idUser,u.user,u.nombre,u.email,IF(u.vigente=1,'SI','NO'),group_concat(r.nombreRol),u.fechaCaptura FROM usuarios u, rol_del_usuario ru, roles r where ru.idUser=u.idUser ANd ru.idRol=r.idRol group by u.idUser;";
 	$resultado = $this->query($sql);
 	if($resultado)
 		return $this->getElementosDeTabla($resultado);
 	return null;
 }

 function getListaUsuariosSinRol(){
 	$sql = "SELECT u.idUser,u.user,u.nombre,u.email,IF(u.vigente=1,'SI','NO'),'<span style=\'color:red\'>SIN ROL</span>',u.fechaCaptura FROM usuarios u WHERE u.idUser NOT IN (select idUser from rol_del_usuario) ORDER BY u.idUser;";
 	$resultado = $this->query($sql);
 	if($resultado)
 		return $this->getElementosDeTabla($resultado);
 	return null;
 }

}
?>