<?php
/*Autor: Jesus Malo, support: dic.malo@gmail.com*/
session_start();
 if(!isset($_SESSION['USER']))
 	header("Location:../");

 date_default_timezone_set('America/Mexico_City');
 require_once("Utilidades.php");
 require_once('../clases/CActividad.php');
 require_once("../db/AccessDB.php");

 $actividades = new CActividad($db);
?>
<!DOCTYPE html>
<html lang="es">
	<head>		
		<title>Administración de actividades y eventos</title>
		<meta charset="utf-8" />
		<meta name="description" content="Administración">
		<meta name="author" content="Jesús Malo Escobar">
		<link href="../imagenes/favicon.ico" rel="icon" type="image/x-icon">
		<?php getStyles(); ?>
		<link rel="stylesheet" href="../css/iconos.css">		
	</head>
	<body class="todo-contenido">
	<div class="container">
		<?php require_once("header.php"); ?>		
		<form method="POST" action="FAdministracion.php" id="AdministracionEventos">
		<?php echo '<input type="hidden" name="accionFA" value="'.$_POST['accionFA'].'"/>'; ?>
			<div class="panel panel-primary">
		    <div class="panel-heading">Administración de actividades, eventos y asistentes</div>
				<div class="panel-body">
					<div id="acordeon">
					 <ul class="busqueda">
						<li>
							<h3><div class="icono buscar"></div>Búsqueda</h3>
							<ul>
								<li>
									<div class="doce">
										<p>Realizar la busqueda mediante:</p>
									</div>
									<div class="form-group">
										<label class="tres">Clave:</label>
										<div class="nueve">
											<input type="text" name="clave_evento" id="clave_evento" class="form-control tres" />
										</div><br><br>
										<label class="tres">No. registro:</label>
										<div class="nueve">
											<input type="text" name="no_registro" id="no_registro" class="form-control tres" />
										</div>
									</div>
									<hr/>
									<div class="form-group">
										<label class="tres">Nombre ó indicio:</label>
										<div class="nueve">
											<input type="text" name="nombre" id="nombre" class="form-control diez" />
										</div>
									</div>
									<hr/>
									<div class="form-group" id="tipo_actividad">
										<label class="tres">Tipo:</label>
										<div id="actividad_academica" class="cinco">							  
										  <select id="actividad" name="actividad" class="form-control">
										  	<?php 
										  	 echo $actividades->getActividades();							  	 
										  	?>
										  </select>
										</div>
									</div>
									<div class="form-group">
										<label class="tres">Todos los tipos:</label>
										<div class="nueve">
											<input type="checkbox" name="todas_acts_evts" id="todas_acts_evts" value="all"/>
										</div>
									</div>
									<div class="form-group">
										<label for="f_inicial" class="tres">Del:</label>
										<div class="nueve">				
											<input type="text" class="datepicker form-control tres" name="f_inicial" id="f_inicial" placeholder="dd/mm/aaaa" pattern="(0[1-9]|[12][0-9]|3[01])[/-](0[1-9]|1[012])[/-](19|20)\d\d">
										</div>
									</div>
									<div class="form-group">
										<label for="f_final" class="tres">Hasta:</label>
										<div class="nueve">
											<input type="text" class="datepicker form-control tres" name="f_final" id="f_final" placeholder="dd/mm/aaaa" pattern="(0[1-9]|[12][0-9]|3[01])[/-](0[1-9]|1[012])[/-](19|20)\d\d">
										</div>
									</div>
									<hr/>
									<?php echo getSearchButton(); ?>
									<br><br>
								</li>
							</ul>
						</li>					
					</ul>
					</div>
					<div class="form-group" style="max-height:750px; overflow:auto">						
					 <?php
					 /*de acuerdo al criterio ingresado por el usuario, asignamos los valores al obj criterios de búsqueda*/
					 require_once('../clases/CCriteriosBusqueda.php');
					 $criterios_busqueda = new CCriteriosBusqueda();
					 $hayCriterio=true;
					 if(isset($_POST['clave_evento'])){
					 	if($_POST['clave_evento']!="")
					  	{
						 	echo '<p class="titulo">Buscando evento con clave: '.$_POST['clave_evento'].'</p>';
						 	$criterios_busqueda->setClave($_POST['clave_evento']);
						 	
					  	}
					  	else if($_POST['no_registro']!="")
					  	{
						 	echo '<p class="titulo">Buscando evento con No. de registro: '.$_POST['no_registro'].'</p>';
						 	$criterios_busqueda->setNoRegistro($_POST['no_registro']);						 	
					  	}
					  	else if($_POST['nombre']!="")
					  	{
						 	echo '<p class="titulo">Buscando evento mendiante el nombre: '.$_POST['nombre'].'</p>';
						 	$criterios_busqueda->setNombre($_POST['nombre']);
						 	
					  	}
						else if($_POST['actividad']!="")
						{
							$actividad = explode("::",$_POST["actividad"],2);
							if(isset($_POST['todas_acts_evts']))
							{
								if($_POST['todas_acts_evts']!=""){
									$actividad[0] = "*";
									$actividad[1] = "TODOS";
								}
							}
							echo '<p class="titulo">Buscando actividad ó evento del tipo '.$actividad[1].'<br>'.
							$criterios_busqueda->setTipo($actividad[0]);
						}
						else
						{
							echo '<p class="titulo">No se especificó criterio de búsqueda</p>';
							$hayCriterio=false;
						}
						if($_POST['f_inicial']!="")
						{								
								echo 'Fecha inicial: '.$_POST['f_inicial'].'<br>';					 		
								if($_POST['f_final']!="")
								{
									echo 'Fecha final: '.$_POST['f_final'].'</p>';
									$criterios_busqueda->setFechaFinal($_POST['f_final']);
								}
								else
								{
								    echo 'Fecha final: '.date("d/m/Y").'</p>';
								    $criterios_busqueda->setFechaFinal(date("Y/m/d"));
								}
								$criterios_busqueda->setFechaInicial($_POST['f_inicial']);								
						}
					 }


					 if($_POST['accionFA']=="actividades_eventos" & isset($_POST['clave_evento']))
					 {
					 	if($hayCriterio)
					 	{
						 		require_once('../clases/CBusquedaActividades.php');
						 		$objBA = new CBusquedaActividades($db);
						 		echo '<div id="tabla_exportar" class="table-responsive">';
						  echo '<table cellpadding="2" class="actividades">
								<thead>
									<tr>
										<th>No. registro</th>
										<th>Clave</th>
										<th>Tipo</th>
										<th style="min-width:150px">Descripcion</th>
										<th>Modalidad</th>
										<th>Duración</th>
										<th>Fecha de inicio</th>
										<th>Fecha de término</th>
										<th>Cupo min.</th>
										<th>Fecha de captura</th>
										<th>Estatus</th>
									</tr>
								</thead>
								<tbody>';
								/*pasamos el obj de criterios de búsqueda al objeto que realizará las consultas*/
								$objBA->setCriteriosBusqueda($criterios_busqueda);
								echo $objBA->generarConsulta();			
						  echo '</tbody></table></div>';
						  echo '<p class="titulo">'.$objBA->getTotalFilas().' coincidencia(s) encontrada(s)</p>';
						  if($objBA->getTotalFilas()>0){
						  	echo getExportarExcelButton(); //Utilidades.php
						  }
						}
					 }
					 else if($_POST['accionFA']=="consulta_asistentes" & isset($_POST['clave_evento'])){
					 	require_once('../clases/CListaAsistencia.php');
					 	$lista_asistencia = new CListaAsistencia($db);
					 	echo '<div id="tabla_exportar" class="table-responsive">';
						  echo '<table cellpadding="2" class="actividades">
								<thead>
									<tr>										
										<th>No. Evento</th>
										<th>Clave del evento</th>										
										<th>Folio</th>
										<th>Nombre del participante</th>
										<th>Observaciones</th>
										<th>Estatus</th>										
									</tr>
								</thead>
								<tbody>';
					 	$lista_asistencia->setCriteriosBusqueda($criterios_busqueda);
					 	echo $lista_asistencia->generarConsulta();
					 	  echo '</tbody></table></div>';
						  echo '<p class="titulo">'.$lista_asistencia->getTotalFilas().' coincidencia(s) encontrada(s)</p>';
						  if($lista_asistencia->getTotalFilas()>0){
						  	echo getExportarExcelButton(); //Utilidades.php
						  }
					 }					 
					 ?>
					</div>
				</div>
			</div>
		</form>
		<?php
			echo getHomeButton();
		?>
		<div class="acciones" id="accion">
			<?php			
			$acceso = $permiso->getPermisos("FAdministracion");
			if($acceso['r'])
				echo '<button type="submit" id="ver" class="btn btn-warning separador" title="Ver detalle"><i class="fa fa-info-circle"></i></button>';
			if($acceso['d'])
				echo '<button type="submit" id="eliminar" class="btn btn-danger separador" title="Eliminar"><i class="fa fa-trash"></i></button>';
			if($acceso['u'])
				echo '<button type="submit" id="modificar" class="btn btn-success separador" title="Modificar"><i class="fa fa-pencil"></i></button>';
			if($acceso['r'])
				echo '<button type="submit" id="ficha_actividad" class="btn btn-primary separador" title="Ver ficha"><i class="fa fa-file-text"></i></button>';
			if($acceso['w'])
				echo '<button type="submit" id="agregar_asistentes" class="btn btn-info separador" title="Agregar asistentes"><i class="fa fa-users"></i></button>';			
			$db->close_conn();/*terminamos la conexion a la base de datos*/
			?>
		</div>
		<footer>Universidad Virtual UNACH 2015 - By: jesus.malo@unach.mx</footer>
	</div>
	</body>
</html>
<?php
	getScripts(); /*para darle estilos al formulario*/ 
	getExcelExportScripts();
?>
<script src="../js/utilidades_actividades.js"></script>
<script>
/*Evento que controla efecto de acordeon al hacer click en h3 del div id=acordeon*/
$(document).ready(function(){
	<?php 
	   if(isset($_POST['clave_evento']))
	   	 echo '$("#acordeon ul ul").slideUp();'.chr(13);
	   else
	   	 echo '$("#acordeon ul ul").slideDown();'.chr(13);
	?>
});
</script>