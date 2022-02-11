<?php
/*Autor: Jesus Malo, support: dic.malo@gmail.com*/
date_default_timezone_set('America/Mexico_City');
require_once('CCiudad.php');
require_once('CCarpetaDocumentos.php');

class CPersona extends CCiudad{
 

 var $id_ciudad, $idpais, $idestado;
 var $id_persona=0, $nombrePersona, $apepat, $apemat;
 var $direccion, $telefono, $celular, $email, $fechanac,$fechaCaptura;
 var $carpeta;

 	function __construct($db)
 	{
      $this->carpeta = new CCarpetaDocumentos($db);
    	parent::__construct($db); /*invocar el constructor de la clase padre*/
 	}
  /*setters*/

	function setIdPersona($id){
		$this->id_persona = $id;
	}

  function setIdCarpeta($id){
    $this->carpeta->setIdCarpeta($id);
  }

	function setNombre($nomPersona){
		$this->nombrePersona = $nomPersona;
 	}

 	function setApellidoPaterno($apellidop){
 		$this->apepat = $apellidop;
 	}

 	function setApellidoMaterno($apellidom){
 		$this->apemat = $apellidom;
 	}

 	function setDireccion($address){
 		$this->direccion = $address;
 	}

 	function setTelefono($tel){
 		$this->telefono = $tel;
 	}

 	function setCelular($cel){
 		$this->celular = $cel;
 	}

 	function setEmail($e_mail){
 		$this->email = $e_mail;
 	}

 	function setFechaNacimiento($fnac){
    $this->fechanac = $fnac;
 	}

  /*getters*/

  function getIdPersona(){
    return $this->id_persona;
  }

  function getNombre(){
    return $this->nombrePersona;
  }

  function getApellidoPaterno(){
    return $this->apepat;
  }

  function getApellidoMaterno(){
    return $this->apemat;
  }

  function getDireccion(){
    return $this->direccion;
  }

  function getTelefono(){
    return $this->telefono;
  }

  function getCelular(){
    return $this->celular;
  }

  function getEmail(){
    return $this->email;
  }

  function getFechaNacimiento(){    
    return $this->fechanac;
  }

  function getFechaCaptura(){
    return $this->fechaCaptura;
  }

  function getIdCarpeta(){
    return $this->carpeta->getIdCarpeta();
  }

 	function getPersonaById(){
 		$sql="SELECT id_persona,nombre,apellido_paterno,apellido_materno from persona WHERE id_persona=".$this->id_persona.";";
    $resultado = $this->query($sql);
    while($fila = mysqli_fetch_assoc($resultado)){
      $this->setIdPersona($fila['id_persona']);
      $this->setNombre($fila['nombre']);
      $this->setApellidoPaterno($fila['apellido_paterno']);
      $this->setApellidoMaterno($fila['apellido_materno']);      
    }
 	}

  function getPersona($nombre,$apepat, $apemat){
     $sql="SELECT * from persona WHERE nombre like '%".$nombre."%' AND apellido_paterno like '%".$apepat."%' AND apellido_materno like '%".$apemat."%';";
     $resultado = $this->query($sql);
     if($dato = $resultado->fetch_assoc())
     {
      $this->setIdPersona($dato['id_persona']);
      $this->setIdCiudad($dato['id_ciudad']);
        
        $this->setIdCiudad($dato['id_ciudad']);
        $this->searchEstadoAndPais();
      $this->setIdEstado($this->getIdEstado());
      $this->setIdPais($this->getIdPais());
      $this->setNombre($dato['nombre']);
      $this->setApellidoPaterno($dato['apellido_paterno']);
      $this->setApellidoMaterno($dato['apellido_materno']);
      $this->setFechaNacimiento($this->getFechaFromMysql($dato['fechaNacimiento']));      
      $this->setDireccion($dato['direccion']);
      $this->setTelefono($dato['telefono']);
      $this->setCelular($dato['telCelular']);
      $this->setEmail($dato['email']);
     }
     
  }

	function getPersonaByName(){
	   $sql="SELECT * from persona WHERE nombre like '%".$this->nombrePersona."%';";
     $resultado = $this->query($sql);
     return $this->getDataListItems($resultado);       
	}

	function getPersonaByApepat(){
		$sql="SELECT * from persona WHERE apellido_paterno like '%".$this->apepat."%';";
    $resultado = $this->query($sql);
    return $this->getDataListItems($resultado);  
	}

	function getPersonaByApemat(){
		$sql="SELECT * from persona WHERE apellido_materno like '%".$this->apemat."%';";
    $resultado = $this->query($sql);
    return $this->getDataListItems($resultado);
	}

  function checkPeople(){
    $this->scapeData();
    $sql="SELECT * from persona WHERE nombre='".$this->nombrePersona."' AND apellido_paterno='".$this->apepat."' AND apellido_materno='".$this->apemat."';";
    $resultado = $this->query($sql);
    if($dato = mysqli_fetch_assoc($resultado))
      return true;
    return false;
  }

	function registrarPersona(){
    if($this->checkPeople()){
      $this->setError("ESTA PERSONA YA EXISTE");
      return false;
    }
    $this->scapeData();
		$sql="INSERT INTO persona(id_ciudad,id_carpeta,nombre,apellido_paterno,apellido_materno,fechaNacimiento,direccion,telefono,telCelular,email) VALUES(".$this->id_ciudad.",".$this->carpeta->getIdCarpeta().",'".$this->nombrePersona."','".$this->apepat."','".$this->apemat."','".$this->getFechaToMysql($this->fechanac)."','".$this->direccion."','".$this->telefono."',".$this->celular.",'".$this->email."');";
    $this->query($sql);
    $this->setIdPersona($this->getInsertId());    
    if($this->id_persona>0)
      return true;
    return false;
	}

  function crearCarpeta($tag){
    $this->carpeta->setTag($tag);
    $this->carpeta->nuevaCarpeta(); /*creamos nueva carpeta para la persona*/
  }

  function actualizarCarpeta($tag){
    $this->carpeta->setTag($tag);    
    $this->carpeta->updateCarpeta();
  }

  function actualizarPersona(){
    $this->scapeData();    
    $sql="UPDATE persona SET id_ciudad=".$this->id_ciudad.",nombre='".$this->nombrePersona."',apellido_paterno='".$this->apepat."',apellido_materno='".$this->apemat."',fechaNacimiento='".$this->getFechaToMysql($this->fechanac)."',direccion='".$this->direccion."',telefono='".$this->telefono."',telCelular=".$this->celular.",email='".$this->email."' WHERE id_persona=".$this->id_persona.";";
      $this->update($sql);    
      return $this->getAffectedRows(); /*id*/   
  }

  function scapeData(){
    $this->nombrePersona = $this->scapeString($this->nombrePersona);
    $this->apepat = $this->scapeString($this->apepat);
    $this->apemat = $this->scapeString($this->apemat);
    $this->fechanac = $this->scapeString($this->fechanac);
    $this->direccion = $this->scapeString($this->direccion);
    $this->telefono = $this->scapeString($this->telefono);
    $this->celular = $this->scapeString($this->celular);
    $this->email = $this->scapeString($this->email);
  }

}
?>