<?php
/*Autor: Jesus Malo, support: dic.malo@gmail.com*/
require_once('../db/ConexionDB.php');

class CRecursos extends ConexionDB{
 var $idRecurso, 
 	 $nombreRecurso,
 	 $disabled=" disabled";

 function __construct($db)
 {
    parent::__construct($db); /*invocar el constructor de la clase padre*/
 }

 function setIdRecurso($id){
 	$this->idRecurso = $id;
 }

 function setNombreRecurso($nombre){
 	$this->nombreRecurso = $this->scapeString($nombre);
 }

 function setDisabled($tof){
 	if($tof)
 		$this->disabled=" disabled";
 	else
 		$this->disabled="";
 }

 function getIdRecurso(){
 	return $this->idRecurso;
 }

 function getNombreRecurso(){
 	return $this->nombreRecurso;
 }

 function getDisabled(){
 	return $this->disabled;
 }

 function guardar(){
 	$sql = "INSERT INTO recursos(nombreRecurso) VALUES('".$this->nombreRecurso."');";
 	if($this->update($sql)){
 		return true;
 	}
 	return false;
 }

 function actualizar(){
 	
 }

 function eliminar(){
 	
 }

  function getRecursos(){
 	$sql = "SELECT * FROM recursos ORDER BY idRecurso;";
 	$resultado = $this->query($sql);
 	if($resultado)
 	  return $this->getElementosDeTabla($resultado);
  return false;
 }

 function getRecursosByRol($idRol){
 	$sql = "SELECT re.idRecurso, re.nombreRecurso, pr.lectura, pr.escritura, pr.actualizacion, pr.eliminacion FROM recursos re, permisos_rol pr, roles r WHERE re.idRecurso=pr.idRecurso AND pr.idRol=r.idRol AND r.idRol=".$idRol." ORDER BY re.idRecurso";
 	$resultado = $this->query($sql);
 	if($resultado)
 	  return $this->showFila($resultado);
  return false;
 }

 function getRecursosNoAsignadosByRol($idRol){
 	$sql = "SELECT idRecurso,nombreRecurso,'0','0','0','0' FROM recursos re WHERE re.idRecurso NOT IN(SELECT pr.idRecurso FROM permisos_rol pr, roles r WHERE pr.idRol=r.idRol AND r.idRol=".$idRol.") ORDER BY re.idRecurso;";
 	$resultado = $this->query($sql);
 	return $this->showFila($resultado);
 }

 function showFila($resultado){
 	$datos="";
 	$id_r=0;
 	$columna=0;
 	$fil=0;
 	$index_permiso = array(2=>'r',3=>'w',4=>'u',5=>'d');
 	require_once("CPermisos.php");
 	global $permiso;
		while($fila = mysqli_fetch_row($resultado)){
			$datos.="<tr>";
			$columna=0;			
			foreach ($fila as $key => $value)
			{
				if($key=="idRecurso")
				{
					$id_r = $value;				
				}
				if($value=='0' & $key!="idRecurso")
				{
					if($_SESSION['rol_usuario']>1)
					{
						$elpermiso = $permiso->getPermisos($fila[1]);
						if($elpermiso[ $index_permiso[$key] ] == 1)
							$datos.='<td><input type="checkbox" name="permiso['.$id_r.']['.$columna.']" value="'.$id_r.'"></input></td>';
						else
							$datos.='<td><input type="checkbox" name="permiso['.$id_r.']['.$columna.']" value="'.$id_r.'"'.$this->getDisabled().'></input></td>';
					}
					else
						$datos.='<td><input type="checkbox" name="permiso['.$id_r.']['.$columna.']" value="'.$id_r.'"></input></td>';
				}
				else if($value=='1' & $key!="idRecurso")
				{
					$datos.='<td><input type="checkbox" checked name="permiso['.$id_r.']['.$columna.']" value="'.$id_r.'"></input></td>';
				}
				else
				{
					$datos.="<td>".$value."</td>";
				}
				if($key!="idRecurso" && $key!="nombreRecurso")
					$columna++;
			}
			$fil++;
			$datos.="</tr>";
		}
		return $datos;
 }

}
?>