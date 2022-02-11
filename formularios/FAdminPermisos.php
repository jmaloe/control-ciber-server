<?php
/*Autor: Jesus Malo, support: dic.malo@gmail.com*/
 header('Content-type: text/html; charset=utf-8');
 session_start();

 if(!isset($_SESSION['USER']))
 	header("Location:../");
 require_once("../db/AccessDB.php");
 require_once("../acceso/CPermisos.php");
 require_once("../acceso/CUsuario.php");
 require_once("Utilidades.php");

 $usuario = new CUsuario($db);
 $msj = "";
 $encontrado=false;

 if(isset($_POST['accion']))
 {
  if($_POST['accion']!="CANCELAR")
  {
 	if(isset($_POST['id_usuario']))
 	{ 		
 		$usuario->setIdUsuario($_POST['id_usuario']);
 		$encontrado=$usuario->buscarById();
 	}

 	if($_POST['accion']=="BUSCAR")
 	{
 		if($_POST['usuario']!="")
 		{
 			$usuario->setUsuario($_POST['usuario']);
 			$encontrado = $usuario->buscarByUser(); 			
 		}
 		else if($_POST['nombre']!="")
 		{
 			$usuario->setNombre($_POST['nombre']);
 			$encontrado = $usuario->buscarByNombre(); 			
 		}
 		else if($_POST['correo']!="")
 		{ 			
 			$usuario->setEmail($_POST['correo']);
 			$encontrado = $usuario->buscarByEmail(); 			
 		}
 		if($encontrado)
 			$permiso->setIdUsuario($usuario->getIdUsuario());
 	}
 	else if($_POST['accion']=="Asignar")
 	{
 		$permiso->setIdRol($_POST['roles_disponibles']);
 		$permiso->getRolById();
 		$permiso->setIdUsuario($_POST['id_usuario']); 		
 		$permiso->setRolToUser();
 		$msj = "Rol asignado: ".$permiso->getNombreRol();
 	}
 	else if($_POST['accion']=="Mostrar")
 	{
 		$permiso->setIdUsuario($_POST['id_usuario']); 		
 	}
 	else if($_POST['accion']=="Eliminar")
 	{
 		$permiso->setIdRol($_POST['roles_asignados']);
 		$permiso->getRolById();
 		$permiso->setIdUsuario($_POST['id_usuario']);
 		if($permiso->deleteRolFromUser()){
 			$msj = 'Se eliminó correctamente el rol "'.$permiso->getNombreRol().'"';
 		}
 		else
 			$msj='No se pudo eliminar el rol "'.$permiso->getNombreRol().'" - '.$permiso->getError();;
 	}
  }
 }
?>
<!DOCTYPE html>
<html lang="es">
	<head>
		<title>Administración de permisos</title>
		<meta charset="utf-8" />
		<meta name="description" content="Login">
		<meta name="author" content="Jesús Malo Escobar">
		<?php getStyles(); ?>		
	</head>
	<body class="formularioPermisos" style="width:30%; margin:0 auto;">
		<section id="Admin">
			<form action="FAdminPermisos.php" method="POST" accept-charset="UTF-8"
enctype="application/x-www-form-urlencoded" autocomplete="off" id="form_users">
			<div class="panel panel-primary">
			    <div class="panel-heading">Asignación de Roles</div>
				 <div class="panel-body">
				 	<?php
				 	 if($msj!="")
				 	 {
				 	 	echo '<div class="form-group"><label style="color:gray">'.$msj.'</label></div>';				 	
				 	 }
				 	?>

				 	<div class="form-group">
				 		<fieldset>
				 		<legend>Roles y permisos</legend>
				 		<label class="doce">Ingrese al menos un criterio de búsqueda:</label>
				 		<?php
				 			if($encontrado)
				 				echo '<input type="hidden" name="id_usuario" value="'.$usuario->getIdUsuario().'">';
				 		?>
				 		<div class="doce form-group">				 			
					 		<label class="tres">Usuario:</label>
					 		<div class="nueve">
					 			<input type="text" name="usuario" id="usuario" class="form-control" placeholder="usuario" 
					 			<?php if($encontrado) echo 'value="'.$usuario->getUsuario().'" readonly'; ?>
					 			>
					 		</div>
					 	</div>					 	
					 	<div class="doce form-group">
					 		<label class="tres">Nombre:</label>
					 		<div class="nueve">
					 			<input type="text" name="nombre" id="nombre" class="form-control" placeholder="nombre completo" <?php if($encontrado) echo 'value="'.$usuario->getNombre().'" readonly'; ?>>
					 		</div>
					 	</div>
					 	<div class="doce form-group">
					 		<label class="tres">Email:</label>
					 		<div class="nueve">
					 			<input type="email" name="correo" id="correo" class="form-control" placeholder="correo@unach.mx" <?php if($encontrado) echo 'value="'.$usuario->getEmail().'" readonly'; ?>>
					 		</div>
					 	</div>
					 	<div class="form-group">
					 		<?php
					 			echo getSearchButton();
					 			echo getCancelButton();
					 		/*echo '<input type="submit" class="btn btn-primary separador" id="btn_buscar" name="accion" value="BUSCAR">';
					 		echo '<input type="submit" class="btn btn-info separador" id="btn_cancelar" name="accion" value="CANCELAR">';					 		*/
					 		?>
					 	</div>
				 		</fieldset>
				 	</div>
					<?php 
					if($encontrado)
					{
					?>
				 		<div class="form-group">
				 		<?php 
				 		$acceso = $permiso->getPermisos("FAdminPermisos");
				 		if($permiso->hayRolesSinAsignarByUser())
				 		{
				 			
				 			if($acceso['w'])
				 			{
				 		?>				 			
					 		<fieldset>
					 			<legend>Roles sin asignar</legend>
					 			<div class="doce form-group">
					 				<label class="tres">Rol:</label>
					 				<div class="cuatro separador">
					 					<select name="roles_disponibles" class="form-control">
					 						<?php
					 						$permiso->setIdUsuario($usuario->getIdUsuario());
					 							echo $permiso->getRolesSinAsignarByUser();
					 						?>
					 					</select>
					 				</div>
					 				<div class="cinco">
					 					<button type="submit" name="accion" value="Asignar" class="btn btn-primary separador"><i class="fa fa-check-circle"></i> Asignar</button>
					 				</div>
					 			</div>
					 		</fieldset>
				 		<?php
				 			}
				 		}
				 		if($permiso->hayRolesByUser())
				 		{
				 			//$acceso fue previamente inicializada lineas arriba
				 			if($acceso['r'])
				 			{
				 		?>
				 		<fieldset>
				 			<legend>Roles asignados</legend>
				 			<div class="doce form-group">
				 				<label class="tres">Rol:</label>
				 				<div class="cuatro separador">
				 					<select name="roles_asignados" class="form-control">
				 						<?php
				 						$permiso->setIdUsuario($usuario->getIdUsuario());
				 							echo $permiso->getRolesAsignadosByUser(isset($_POST['roles_asignados'])?$_POST['roles_asignados']:0);
				 						?>
				 					</select>
				 				</div>
				 				<div class="cinco">
				 					<button type="submit" name="accion" value="Mostrar" class="btn btn-primary separador"><i class="fa fa-eye"></i> Mostrar</button>
				 					<?php
				 						if($acceso['d'])
				 							echo '<button type="submit" name="accion" value="Eliminar" class="btn btn-danger"><i class="fa fa-trash"></i> Eliminar</button>';
				 					?>				 					
				 				</div>
				 			</div>
				 			<?php
							if(isset($_POST['roles_asignados']))							
				 			{
				 			echo '<div class="doce form-group">
				 				<label>Recursos:</label>
				 			</div>
				 			<div class="form-group">
				 				<div class="doce">
				 				<table class="actividades">
				 					<thead>
				 						<tr>
				 							<th>Id</th>
				 							<th>Nombre</th>
				 							<th style="width:10%">Leer</th>
				 							<th style="width:10%">Escribir</th>
				 							<th style="width:10%">Actualizar</th>
				 							<th style="width:10%">Eliminar</th>
				 						</tr>
				 					</thead>
				 					<tbody>';
				 					$permiso->setIdUsuario($usuario->getIdUsuario());
				 					$permiso->setDisabled(true);
				 					echo $permiso->getPermisosByUser($_POST['roles_asignados']);
				 					//echo $permiso->getRecursosNoAsignadosByRol($_POST['roles']);				 							
				 						
				 					echo '</tbody>
				 				</table>
				 				</div>				 				
				 			</div>';
				 			}
							?>
				 		</fieldset>
				 		<?php
				 		 	}
				 		}
				 		?>
				 	</div>
				 	<?php } ?>
				 </div>
			</div>
			</form>
		</section>
		<?php 
			echo getHomeButton(); 
			getScripts();
		?>
		<script src="../js/admin_users_validaciones.js"></script>
	</body>
</html>
<?php
	$db->close_conn();
?>