<?php
/*Autor: Jesus Malo, support: dic.malo@gmail.com*/

 class CCriteriosBusqueda{

  function __construct()
  {
 	  
  }

  var $no_registro,
      $clave,
  	  $nombre, 
  	  $tipo, 
  	  $fecha_inicial=null, 
  	  $fecha_final=null;

  //setters
  function setNoRegistro($nr){
  	$this->no_registro = $nr;
  }

  function setClave($c){
    $this->clave=$c;
  }

  function setNombre($nom){
  	$this->nombre = $nom;
  }

  function setTipo($t){
  	$this->tipo = $t;
  }

  function setFechaInicial($fi){
  	$this->fecha_inicial = date('Y-m-d', strtotime(str_replace('/', '-', $fi)));
  }

  function setFechaFinal($ff){
  	$this->fecha_final = date('Y-m-d', strtotime(str_replace('/', '-', $ff)));
  }
  //getters
  function getNoRegistro(){
  	return $this->no_registro;
  }

  function getClave(){
    return $this->clave;
  }

  function getNombre(){
  	return $this->nombre;
  }

  function getTipo(){
  	return $this->tipo;
  }

  function getFechaInicial(){
  	return $this->fecha_inicial;
  }

  function getFechaFinal(){
  	return $this->fecha_final;
  }

  function generarReporte($obj){
    if($this->getNoRegistro()){
      return $obj->getByNoRegistro();
    }
    else if($this->getClave()){
      return $obj->getByClave();
    }
    else if($this->getNombre()){
      return $obj->getByName();
    }
    else if($this->getTipo()){
      return $obj->getByType();
    }
  }

 }
?>