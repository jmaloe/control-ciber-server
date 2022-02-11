<?php
/*Autor: Jesus Malo, support: dic.malo@gmail.com*/
require_once('CUsuario.php');

class CRoles extends CUsuario{
 var $idRol, 
 	 $nombreRol,
 	 $rol_default=-1;

 function __construct($db)
 {
    parent::__construct($db); /*invocar el constructor de la clase padre*/
 }

 function setIdRol($id){
 	$this->idRol = $id;
 }

 function setNombreRol($nombre){
 	$this->nombreRol = $this->scapeString($nombre);
 }

 function getIdRol(){
 	return $this->idRol;
 }

 function getNombreRol(){
 	return $this->nombreRol;
 }

 function getDefaultRol(){
 	return $this->rol_default;
 }

 function guardar(){
 	$sql = "INSERT INTO roles(nombreRol) VALUES('".$this->nombreRol."');";
 	if($this->update($sql)){
 		return true;
 	}
 	return false;
 }

 function actualizar(){
 	
 }

 function eliminar(){
 	$sql = "DELETE FROM roles WHERE idRol=".$this->idRol;
 	if($this->update($sql))
 		return true;
 	return false;
 } 

 function getRoles($default_selected){ 	
 	//ocultamos rol de superusuario
 	if($_SESSION['rol_usuario']==1)
 		$sql = "SELECT * FROM roles;";
 	else
 		$sql = "SELECT * FROM roles where idRol>2;"; //obtenemos los roles omitiendo superuser y admin
 	$resultado = $this->query($sql);
 	if($resultado)
 		return $this->getSelectItems($resultado,$default_selected);
 	return false;
 }

 function getRolById(){
 	$sql = "SELECT nombreRol FROM roles WHERE idRol=".$this->getIdRol();
 	$result = $this->query($sql);
 	if($data = mysqli_fetch_row($result)){
 		$this->setNombreRol($data[0]);
 		return true;
 	}
 	return false;
 }

 function hayRolesSinAsignarByUser(){
 	if($_SESSION['rol_usuario']==1)
		$sql = 'SELECT r.idRol FROM roles r WHERE r.idRol NOT IN(select rdu.idRol from rol_del_usuario rdu, usuarios us where rdu.idUser=us.idUser and us.idUser='.$this->getIdUsuario().')';
	else
		$sql = 'SELECT r.idRol FROM roles r WHERE r.idRol NOT IN(select rdu.idRol from rol_del_usuario rdu, usuarios us where rdu.idUser=us.idUser and us.idUser='.$this->getIdUsuario().') and r.idRol>2';
 	$data = $this->query($sql); 	
 	if($fila=mysqli_fetch_row($data))
 		return true;
 	return false;
 }

 function hayRolesByUser(){
 	$sql = "SELECT r.idRol FROM roles r, rol_del_usuario ru, usuarios u Where r.idRol=ru.idRol And ru.idUser=u.idUser And u.idUser=".$this->getIdUsuario();
 	$data = $this->query($sql);
 	if($fila=mysqli_fetch_row($data))
 		return true;
 	return false;
 }

 function getRolesSinAsignarByUser(){
 	if($_SESSION['rol_usuario']==1)
 		$sql = 'SELECT r.idRol, r.nombreRol FROM roles r WHERE r.idRol NOT IN(select rdu.idRol from rol_del_usuario rdu, usuarios us where rdu.idUser=us.idUser and us.idUser='.$this->getIdUsuario().')';
 	else 		
		$sql = 'SELECT r.idRol, r.nombreRol FROM roles r WHERE r.idRol NOT IN(select rdu.idRol from rol_del_usuario rdu, usuarios us where rdu.idUser=us.idUser and us.idUser='.$this->getIdUsuario().') and r.idRol>2'; //2 es administrador
 	$data = $this->query($sql);
 	if($data)
 		return $this->getSelectItems($data,0);
 	return false;
 }

 function getRolesAsignadosByUser($default_select){
 	$sql = "SELECT r.idRol, r.nombreRol from roles r, rol_del_usuario ru, usuarios u Where r.idRol=ru.idRol And ru.idUser=u.idUser And r.idRol>=".$_SESSION['rol_usuario']." And u.idUser=1".$this->getIdUsuario()." ORDER BY r.idRol;";
 	$resultado = $this->query($sql); 	
 	if($resultado)
 	{
 		$data = mysqli_fetch_row($resultado); 		
 		$this->rol_default = $data[0];
 		mysqli_data_seek($resultado,0); //retrocedemos el cursor al primer elemento de la consulta
 		return $this->getSelectItems($resultado,$default_select==-1?$this->rol_default:$default_select);
 	}
 	return false;
 }

/*pasar -1 como parámetro para asignar selected a rol_default*/
 function getRolesByUser($default_select){
 	$sql = "SELECT r.idRol, r.nombreRol from roles r, rol_del_usuario ru, usuarios u Where r.idRol=ru.idRol And ru.idUser=u.idUser And u.idUser=".$this->getIdUsuario()." ORDER BY r.idRol;";
 	$resultado = $this->query($sql); 	
 	if($resultado)
 	{
 		$data = mysqli_fetch_row($resultado); 		
 		$this->rol_default = $data[0];
 		mysqli_data_seek($resultado,0); //retrocedemos el cursor al primer elemento de la consulta
 		return $this->getSelectItems($resultado,$default_select==-1?$this->rol_default:$default_select);
 	}
 	return false;
 }

 function setRolToUser(){
 	$sql = 'INSERT INTO rol_del_usuario(idRol,idUser) values('.$this->getIdRol().','.$this->getIdUsuario().')';
 	if($this->update($sql))
 		return true;
 	return false;
 }

 function deleteRolFromUser(){
 	$sql = 'DELETE FROM rol_del_usuario WHERE idUser='.$this->getIdUsuario().' AND idRol='.$this->getIdRol();
 	if($this->update($sql))
 		return true;
 	return false;
 }

 function eliminarPermisos(){
 	$sql = "DELETE FROM permisos_rol WHERE idRol=".$this->idRol;
 	if($this->update($sql))
 		return true;
 	return false;
 }

 function asignarPermisos($obj){
 	$sql = "INSERT INTO permisos_rol(idRol,idRecurso,lectura,escritura,actualizacion,eliminacion) VALUES(".$this->idRol.",".$obj['idRecurso'].",".$obj['lect'].",".$obj['escr'].",".$obj['act'].",".$obj['elim'].");";
 	if($this->update($sql))
 		return true;
 	return false;
 }

}
?>