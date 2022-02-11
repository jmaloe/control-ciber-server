<?php
/*Autor: Jesus Malo, support: dic.malo@gmail.com 04/09/2015*/
session_start();
 if(!isset($_SESSION['USER'])){
 	header("Location:../");
 }
 if(isset($_SESSION['rol_usuario'])){
 	if($_SESSION['rol_usuario']>1)
 		header("Location:../");
 }

 require_once("../db/AccessDB.php");
 require_once("../clases/CModelo.php");
 require_once("Utilidades.php");
 $obj = new CModelo($db);
?>

<!DOCTYPE html>
<html lang="es">
<head>
	<title>Administrador</title>
	<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1">
	<meta charset="utf-8" />
	<meta name="description" content="Administración de Catalogos">
	<meta name="author" content="Jesús Malo Escobar">
	<?php
		getStyles();
	?>	
</head>
<body class="todo-contenido ancho">	
  <form id="AdminMyControl" action="Admin.php" method="POST" enctype="multipart/form-data">  	
	<div class="panel panel-primary contenido">
    	<div class="panel-heading"><h4>Administración de Catalogos</h4></div>
	 	<div class="panel-body">
	 		<p class="instruccion">
	 			Alta, eliminación y actualización de datos a nivel base de datos de las principales tablas del Sistema.
			</p>
			<hr />
			<div class="content">
		  		<div class="form-group">
					<label class="doce">Listado de tablas</label>
					<div class="siete">
						<ul>
							<?php
								echo $obj->showTablesFromDB(0);

								$id_tabla="";
								
								if(isset($_GET['id_t'])){
									$id_tabla = $_GET['id_t'];
								}
								if(isset($_POST['id_tabla'])){
									$id_tabla = $_POST['id_tabla'];
								}								
							?>
						</ul>
					</div>
				</div>
				<div class="form-group tabla">
					<table class="table-responsive actividades caja-elementos">
						<thead>
							<tr>
								<?php
									if($id_tabla!="")
									{	
										$obj->setTabla($id_tabla);									
										echo $obj->getTableHeader($id_tabla);
										$obj->loadCamposEdicion($id_tabla); //cargamos los campos con edición
									}
								?>
							</tr>
						</thead>
						<tbody>
							<!-- Aqui los datos de las tabla seleccionada -->
							<?php
									echo '<input type="hidden" name="id_tabla" id="id_tabla" value="'.$id_tabla.'">';

									$row_id = -1;
									$edit_mode=false;
											
									if(isset($_POST['accion'])){
										//hacemos explode para obtener la accion y el id
										$data = explode(":?:", $_POST['accion']);
										$row_id = $data[1];										

										if($data[0]=='c'){											
											$row_id=-1;
										}

										if($data[0]=='m'){
											//habilita opcion de modificar											
											echo $obj->getEditionRows($row_id);
											$edit_mode=true;
										}
										else if($data[0]=='a'){
											//actualiza la información
											$campos="";
											$index=0;
											foreach ($_POST['campo_edicion'] as $key => $value){
												//quitamos comilla simple del $key
												$key= str_replace("'", "", $key);
												//comprobamos si el $value es una fecha para adecuarla
												if (DateTime::createFromFormat('d/m/Y', $value) !== FALSE){
													$value = $obj->getFechaToMysql($value);
												}
												//si ya se insertó el primer campo agregamos coma para el siguiente elemento
												if($index>0)
												{
													 if(is_numeric($value))
														$campos.=",".$key."=".$value;
													 else
													 	$campos.=",".$key."='".$value."'";
												}else{
													if(is_numeric($value))
														$campos.=$key."=".$value;
													else
														$campos.=$key."='".$value."'";
												}
												$index++;
											}
											$obj->setCampos($campos);
											$obj->setClausulas('where '.$obj->getPrimaryKey().'='.$row_id);
											$obj->actualizar();
											//una vez actualizado los datos es necesario volver a cargar los campos de edicion para mostrar correctamente											
											$obj->getTableHeader($id_tabla);
										}
										else if($data[0]=='e'){
											//elimina elemento por ID en la base de datos
											$obj->setClausulas('where '.$obj->getPrimaryKey().'='.$row_id);
											$obj->eliminar();
										}
									}
									
									if(!$edit_mode)
										echo $obj->getEditionRows($row_id);
								
							?>
						</tbody>
					</table>							
				</div>
				<p style="text-align:right">Fecha: <?php echo date("d-M-Y"); ?></p>
				<!-- <button type="submit" name="btn-accion" class="btn btn-success" value="Enviar">Enviar</button> -->
			</div> <!-- div content -->
	 	</div> <!-- div panel-body -->
	</div> <!-- div panel-primary -->
  </form>
  <?php 
	echo getHomeButton();
	$db->close_conn();
	getScripts();
  ?>
</body>
</html>