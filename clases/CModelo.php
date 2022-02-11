<?php
require_once('../db/ConexionDB.php');

class CModelo extends ConexionDB{

	var $tabla="",
		$campos="",
		$valores="",
		$clausulas="",
		$primary_key;

	var $resultado = array();

	function __construct($db)
	{
		parent::__construct($db); /*invocar el constructor de la clase padre*/
	}

 	/*Setters*/
 	function setPrimaryKey($pk){
 		$this->primary_key = $pk;
 	} 	

 	function setTabla($t){
 		$this->tabla = $t;
 	}

 	function setCampos($camposx){
 		$this->campos = $camposx;
 	}

 	function setValores($params){
 		$this->valores = $params;
 	}

 	function setClausulas($c){
 		$this->clausulas = $c;
 	}

 	function setResultado($res){
 		while($fila = mysqli_fetch_assoc($res)){
 			$aux = null;
 			$aux = array();
 			foreach ($fila as $key => $value) {
 				$aux["'".$key."'"] = $value;
 				//echo "'".$key."'-".$value;
 			}
 			//echo "<br>";
 			array_push($this->resultado,$aux);
 		}
 		//echo count($this->resultado);
 	}

 	/*Getters*/
 	function getPrimaryKey(){
 		return $this->primary_key;
 	}

 	function getTabla(){
 		return $this->tabla;
 	}

 	function getCampos(){
 		return $this->campos;
 	}

 	function getValores(){
 		return $this->valores;
 	}

 	function getClausulas(){
 		return $this->clausulas;
 	}

	function getValor($criterio){
		foreach ($this->resultado as $fila) {
			foreach ($fila as $key => $value){
				if($key==$criterio){
					return $value;
				}
			}
		}
	}

	/*método de busqueda, require campos, nombre de tabla y restricciones*/
	function buscar(){
		$sql = "SELECT ".$this->getCampos()." FROM ".$this->getTabla()." ".$this->getClausulas();		
		$resultado = $this->query($sql);
		if($this->setResultado($resultado))
			return true;
		else
			return false;
	}

	function showTablesFromDB($selected){
		$sql = "show tables";
		if($resultado = $this->query($sql))
			return $this->getLiItems($resultado, $selected);
        return false;
	}

	function loadCamposEdicion($table){
		$sql = "describe ".$table;
		$campos_edicion = Array();
		if($resultado = $this->query($sql)){
			while($fila = mysqli_fetch_assoc($resultado)){
				if($fila['Key']==""){
					array_push($campos_edicion,$this->getLength($fila['Type'])."::".$fila['Field']);
				}
				else if($fila['Key']=="PRI"){
					$this->setPrimaryKey($fila['Field']);
				}
			}
			$this->setCamposEdicion($campos_edicion);			
			//print_r($campos_edicion);
			return true;
		}
	  return false;
	}

	function getTableHeader($table){
		$sql = "describe ".$table;
		$header="<th>Núm.</th>";
		$camposx = "";
		$first=true;
		if($resultado = $this->query($sql)){
			while($fila = mysqli_fetch_assoc($resultado)){
				$header.="<th>".$fila['Field']."</th>";
				if(!$first)
				{
					$camposx.=",".$fila['Field'];
				}
				else
				{
					$camposx.=$fila['Field'];
					$first=false;
				}					
			}
			$this->setCampos($camposx);
			return $header."<th>Acción</th>";
		}
	  return false;
	}

	function getEditionRows($id_item){
		$sql = "SELECT ".$this->getCampos()." FROM ".$this->getTabla()." ".$this->getClausulas()." ORDER BY ".$this->primary_key." DESC LIMIT 200";
		if($resultado = $this->query($sql))
			return $this->getElementosDeTablaConEdicion($resultado, $id_item, true); //$id_item=elemento a modificar, true=mostrar elementos listados en la tabla
	  return false;
	}

	function getLength($type){		
		if($type=="tinytext")
			return 254;
		if($type=="date")
			return 10;
		if($type=="timestamp" | $type=="time")
			return 0;
		if(strpos($type, ')') !== false) {
    		$step1 = explode("(", $type);
			$step2 = explode(")", $step1[1]);
			if($step2[0]>0)
				return $step2[0];	
		}
	  return 0;
	}

	/*retorna los items para un select, require item por default*/
	function getColeccion($params){
	   $sql = "SELECT ".$this->getCampos()." FROM ".$this->getTabla()." ".$this->getClausulas();
         $resultado = $this->query($sql);
         switch ($params['tiporetorno'])
         {
         	case 'checkbox':
         		return $this->getCheckboxItems($resultado,$params['nombre'],$params["columnas"]);
         	break;

         	case 'select':
         		return $this->getSelectItems($resultado,$params['defaultselect']);
         	break;
         }
	}

	/*método para registro*/
	function insertar(){
		$sql = "INSERT INTO ".$this->tabla."(".$this->campos.") VALUES(".$this->valores.");";
		if($this->query($sql))
			return $this->getInsertId();
	 return false;	
	}	

	/*método para actualización*/
	function actualizar(){
		$sql = "UPDATE ".$this->tabla." SET ".$this->campos." ".$this->clausulas;		
		if($this->update($sql))
			return true;
		else			
	  		return $this->getError();
	}

	/*método para eliminación*/
	function eliminar(){
		/*DELETE FROM nombre_tabla WHERE id=1*/
		$sql = "DELETE FROM ".$this->tabla." ".$this->clausulas;
		if($this->update($sql))
			return true;
	  return false;
	}	
}
?>