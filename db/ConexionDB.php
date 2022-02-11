<?php
/*Autor: Jesus Malo, support: dic.malo@gmail.com*/
require_once("DataReturns.php");

class ConexionDB extends DataReturns {
 
 protected $conexion;
 private $results;
 private $error="";

 public function __construct($db)
 {
	$this->conexion = $db->getConnection(); /*recibe un objeto del tipo AccessDB*/
 }

 public function query($sql)
 {
	if(!($this->results = mysqli_query($this->conexion, $sql)))
	{
	  $this->error = mysqli_error($this->conexion);
	  return false;
	}
	else
	  return $this->results;
 }

 public function update($sql){
 	if(mysqli_query($this->conexion, $sql))
    	return true;
	else
    	$this->error = mysqli_error($this->conexion);
    return false;
 }

 public function free()
 {
 	mysqli_free_result($this->results);
 }

 public function getInsertId(){
 	return mysqli_insert_id($this->conexion);
 }

 public function getAffectedRows(){
 	return mysqli_affected_rows($this->conexion);
 	//return $this->conexion->affected_rows;
 }

 public function getNumRows(){
 	//return $this->results->num_rows;
 	if($this->results)
 		return mysqli_num_rows($this->results);
 	else
 		return 0;
 }

 public function scapeString($string){
 	return mysqli_real_escape_string($this->conexion,$string);
 }

 public function setError($err){
 	$this->error = $err;
 }

 public function getError(){
 	return $this->error;
 }

}

?>