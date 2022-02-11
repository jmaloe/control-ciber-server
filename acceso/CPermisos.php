<?php
/*Autor: Jesus Malo, support: dic.malo@gmail.com*/
require_once('CRoles.php');
require_once('CRecursos.php');

class CPermisos extends CRoles{
 var $idPermiso, $idRecurso;
 var $recursos;
 var $permisos = array();

 function __construct($db)
 {
    parent::__construct($db); /*invocar el constructor de la clase padre*/
    $this->recursos = new CRecursos($db);    
 }

 function setIdPermiso($id){
 	$this->idPermiso = $id;
 } 

 function setIdRecurso($id){
 	$this->idRecurso = $id;
 }

 function getIdPermiso(){
 	return $this->idPermiso;
 }

 function getIdRecurso(){
 	return $this->idRecurso;
 }

 function findUsuario($user){
 	$this->setUsuario($user); //en CUsuario.php
 	$this->buscarByUser(); //cargamos los datos del usuario
 }

 function guardarPermiso($data){
 	$sql = "INSERT INTO permisos_globales(idRecurso,idUser,lectura,escritura,actualizacion,eliminacion) VALUES(".$this->idRecurso.",".$this->getIdUsuario().",".$data['lectura'].",".$data['escritura'].",".$data['actualizacion'].",".$data['eliminacion'].");";
 	if($this->update($sql)){
 		return true;
 	}
 	return false;
 }

 function setDisabled($tof){
 	$this->recursos->setDisabled($tof);
 }

 function getPermisosByUser($idRol){
 	$sql = "SELECT re.idRecurso, re.nombreRecurso, pr.lectura, pr.escritura, pr.actualizacion, pr.eliminacion FROM recursos re, permisos_rol pr, roles r, rol_del_usuario ru, usuarios u WHERE re.idRecurso=pr.idRecurso AND pr.idRol=r.idRol AND ru.idRol=r.idRol AND ru.idUser=u.idUser AND r.idRol=".$idRol." AND u.idUser=".$this->getIdUsuario();
 	$resultado = $this->query($sql);
 	if($resultado)
 	{ 	  
 	  return $this->recursos->showFila($resultado);
 	}
  return false;
 }

 function loadResources($idRol){ 	
 	if($this->getIdUsuario()==-1)
 	{
 		echo "No se ha especificado un usuario";
 	return false;
 	} 	
 	$sql = "SELECT re.idRecurso, re.nombreRecurso, pr.lectura, pr.escritura, pr.actualizacion, pr.eliminacion FROM recursos re, permisos_rol pr, roles r, rol_del_usuario ru, usuarios u WHERE re.idRecurso=pr.idRecurso AND pr.idRol=r.idRol AND ru.idRol=r.idRol AND ru.idUser=u.idUser AND r.idRol=".$idRol." AND u.idUser=".$this->getIdUsuario(); 	
 	$res = $this->query($sql);
 	while($fila=mysqli_fetch_row($res)){
 		array_push($this->permisos, array('recurso'=>$fila[1], 'r'=>$fila[2], 'w'=>$fila[3], 'u'=>$fila[4], 'd'=>$fila[5]));
 		//echo $fila[1]." ".$fila[2]." ".$fila[3]." ".$fila[4]." ".$fila[5]."<br>";
 	}
 }

 function getPermisos($recurso){
 	foreach ($this->permisos as $fila){
 		if($fila['recurso']==$recurso){
 			return array('r'=>$fila['r'],'w'=>$fila['w'],'u'=>$fila['u'],'d'=>$fila['d']);
 		}
 	}
 	return false;
 }

}

global $db;
/*creamos el objeto de la clase CPermiso*/
$permiso = new CPermisos($db);
/*indicamos el usuario que solicita los permisos*/
//$permiso->setUsuario($_SESSION['USER']);
/*cargamos los datos del usuario, interesa el idUsuario*/
$permiso->setIdUsuario($_SESSION['ID_USER']);
/*es necesario definir $_SESSION['rol_usuario'] antes de solicitar require_once("CPermisos.php")*/
$permiso->loadResources($_SESSION['rol_usuario']); /*cargamos los permisos del usuario en el rol seleccionado*/
?>