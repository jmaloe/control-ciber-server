<?php
/*Autor: Jesus Malo, support: dic.malo@gmail.com*/
session_start();
 if(!isset($_SESSION['USER']))
 	header("Location:../");
 	
 require_once("../clases/CPersona.php");
 require_once("../db/AccessDB.php");
 require_once("Utilidades.php"); 
 global $db;

 $mensaje = "";
 $encontrado = false;
 $esnuevo=false;
 $persona = new CPersona($db);

if(isset($_POST['accion']))
{
 if($_POST['accion']=="BUSCAR")
 {	
 	$persona->getPersona($_POST['nombrePersona'],$_POST['apepat'],$_POST['apemat']);
 	if($persona->getIdPersona()>0){
 		$encontrado=true;
 		$mensaje = "Persona encontrada ID: ".$persona->getIdPersona();
 	}
 	else
 		$mensaje = 'No se encontraron coincidencias para "'.$_POST['nombrePersona']." ".$_POST['apepat']." ".$_POST['apemat'].'"';
 	 	
 }
 else
 {  
  $persona->setIdCiudad(isset($_POST['ciudad'])?$_POST['ciudad']:100); /*id_ciudad*/
  $persona->setNombre($_POST['nombrePersona']); 	
  $persona->setApellidoPaterno($_POST['apepat']);
  $persona->setApellidoMaterno($_POST['apemat']);
  $persona->setDireccion(isset($_POST['direccion'])?$_POST['direccion']:"NE");
  $persona->setTelefono(isset($_POST['telefono'])?$_POST['telefono']:0);
  $persona->setCelular(isset($_POST['celular'])?$_POST['celular']:0);
  $persona->setEmail(isset($_POST['email'])?$_POST['email']:"NE");
  $persona->setFechaNacimiento(isset($_POST['fnac'])?$_POST['fnac']:"0000-00-00");
   if($_POST['accion']=="GUARDAR")
   {
	$persona->crearCarpeta("P_NEW");
 	if($persona->registrarPersona()){
 	  $mensaje = "Nueva persona agregada ID: ".$persona->getIdPersona();
	  $persona->actualizarCarpeta("PER:".$persona->getIdPersona());
 	  $esnuevo = false;
 	  $encontrado=true; /*habilita opcion de boton ACEPTAR*/
 	}
 	else
 	  $mensaje = "Error: ".$persona->getError();	
   }
   else if($_POST['accion']=="ACTUALIZAR"){
   	$persona->setIdPersona($_POST['id_persona']);
   	$rowsAffected = $persona->actualizarPersona();
   	if($rowsAffected==-1){
   	  $mensaje = "Error: ".$persona->getError();
   	}else{
   	  $mensaje = $rowsAffected." registro actualizado";   	  
   	  $esnuevo=true;
   	}
   }
 }
}
 getStyles(); 
?>

<form action="FPersona.php" method="POST" id="FormPersona">
	<input type="hidden" name="tipoPersona" id="tipoPersona" <?php if(isset($_GET['tipo'])) echo 'value="'.$_GET['tipo'].'"'; else echo 'value="'.$_POST['tipoPersona'].'"'; ?>>
<?php 
  /*resguardamos el id de la persona en un hidden*/
   echo '<input type="hidden" name="id_persona" id="id_persona" value="'.$persona->getIdPersona().'">';
 ?>	
<div id="formulario_persona" class="panel panel-primary">
	<div class="panel-heading">Personas</div>
	<?php
	if($mensaje!=""){
  		echo '<div id="mensajes">'.$mensaje.'</div>';
	}
	?>
	<div class="panel-body">

	<div class="form-group"> 
		<label for="nombrePersona" class="tres">Nombre(s):</label>		
		<div class="nueve">
		  <input type="text" id="nombrePersona" class="form-control" name="nombrePersona" maxlength="30" <?php if($encontrado & !$esnuevo) echo 'value="'.$persona->getNombre().'"'; ?> required>
	    </div>
	</div>
	<div class="form-group">
		<label for="apepat" class="tres">Apellido paterno:</label>
		<div class="nueve">
		<input type="text" id="apepat" class="form-control" name="apepat" maxlength="30" <?php if($encontrado & !$esnuevo) echo 'value="'.$persona->getApellidoPaterno().'"'; ?> required>
		</div>
	</div>
	<div class="form-group">
		<label for="apemat" class="tres">Apellido materno:</label>
		<div class="nueve">
		 <input type="text" id="apemat" class="form-control" name="apemat" maxlength="30" <?php if($encontrado & !$esnuevo) echo 'value="'.$persona->getApellidoMaterno().'"'; ?>>		
		</div>
	</div>
	
	<div class="form-group">
		<label for="telefono" class="tres">Telefono:</label>
		<div class="nueve">
		 <input type="tel" id="telefono" class="form-control" name="telefono" maxlength="30" <?php if($encontrado & !$esnuevo) echo 'value="'.$persona->getTelefono().'"'; ?>>
		</div>
	</div>
	<div class="form-group">
		<label for="celular" class="tres">Celular:</label>
		<div class="nueve">
		 <input maxlength="10" type="tel" id="celular" class="form-control" name="celular" maxlength="10" <?php if($encontrado & !$esnuevo) echo ' value="'.$persona->getCelular().'"'; ?>>
		</div>
	</div>
	<div class="form-group">
		<label for="email" class="tres">E-mail:</label>
		<div class="nueve">
		 <input type="email" id="email" class="form-control" name="email" placeholder="correo@ejemplo.com" maxlength="40" <?php if($encontrado & !$esnuevo) echo ' value="'.$persona->getEmail().'"'; ?>>
		</div>
	</div>
	
	<div class="form-group">

	<?php
	require_once("../acceso/CPermisos.php");
	$acceso = $permiso->getPermisos("FPersona");
	 getAcciones($encontrado, $encontrado, $acceso); /*devuelve los botones necesarios para las acciones*/
	?>
	</div>
</div>
</div>
</form>
<?php
 getScripts(); /* obtenemos los js de Utilidades.php basicos para el formulario*/
 if(isset($_POST['tipoPersona']))
   getScript($_POST['tipoPersona'], $persona);
 else
   getScript($_GET['tipo'],$persona);
 $db->close_conn();
?>
<script src="../js/validacion_form_persona.js"></script>