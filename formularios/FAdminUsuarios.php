<?php
/*Autor: Jesus Malo, support: dic.malo@gmail.com*/
 header('Content-type: text/html; charset=utf-8');
 session_start();

 if(!isset($_SESSION['USER']))
 	header("Location:../");
 
 require_once("../db/AccessDB.php");
 require_once("../acceso/CPermisos.php");
 require_once("Utilidades.php");

 $acceso = $permiso->getPermisos("FAdminUsuarios");
 $msj = "";
 $encontrado=false;

 if(isset($_POST['accion']))
 {

 	require_once("../acceso/CUsuario.php");
 	
 	$usuario = new CUsuario($db);
 	if(isset($_POST['usuario']))
 	 $usuario->setUsuario($_POST['usuario']);
 	if(isset($_POST['password']))
 	  $usuario->setPassword($_POST['password']);
 	if(isset($_POST['nombre']))
 	  $usuario->setNombre($_POST['nombre']);
 	if(isset($_POST['correo']))
 	  $usuario->setEmail($_POST['correo']); 	

 	if($_POST['accion']=="GUARDAR"){ 		
 		require_once("../acceso/CRoles.php");
		$roles = new CRoles($db);		
 		if($usuario->guardar()){
 			$msj = "Usuario registrado correctamente";
 			$roles->setIdUsuario($usuario->getIdUsuario());
 			$roles->setIdRol($_POST['rol_usuario']);
 			$roles->setRolToUser();
 		}
 		else{
 			$msj = "Error: ".$usuario->getError();
 		}
 	}
 	else if($_POST['accion']=="BUSCAR" | $_POST['accion']=="Ajustar"){
 		if($_POST['usuario']!="")
 		{
 			$encontrado = $usuario->buscarByUser(); 			
 		}
 		else if($_POST['nombre']!="")
 		{
 			$encontrado = $usuario->buscarByNombre(); 			
 		}
 		else if($_POST['correo']!="")
 		{
 			$encontrado = $usuario->buscarByEmail(); 			
 		}
 	}
 	else if($_POST['accion']=="ELIMINAR"){
 		if($usuario->eliminar()){
 			$msj = "Se eliminó correctamente el usuario ".$usuario->getUsuario();
 		}
 	}
 	else if($_POST['accion']=="ACTUALIZAR"){ 		
 		if($acceso['w'])
 		{
 			if(isset($_POST['activo']))
 				$usuario->setActivo(1);
 			else
 				$usuario->setActivo(0);
 		}
 		if(!$usuario->actualizar())
 			$msj = $usuario->getError();
 		else
 			$msj = "Usuario actualizado: ".$usuario->getUsuario();
 	}
 }
?>
<!DOCTYPE html>
<html lang="es">
	<head>
		<title>Administración de usuarios</title>
		<meta charset="utf-8" />
		<meta name="description" content="Login">
		<meta name="author" content="Jesús Malo Escobar">
		<?php getStyles(); ?>		
	</head>
	<body class="formularioLogin" style="width:30%; margin:0 auto;">
		<section id="Admin">
			<form id="form_users" action="FAdminUsuarios.php" method="POST" accept-charset="UTF-8"
enctype="application/x-www-form-urlencoded" autocomplete="off" id="form_users">
			<div class="panel panel-primary">
			    <div class="panel-heading">Administrador</div>
				 <div class="panel-body">
				 	<?php
				 	 if($msj!="")
				 	 {
				 	 	echo '<div class="form-group"><label style="color:gray">'.$msj.'</label></div>';
				 	 }
				 	?>

				 	<div class="form-group">
				 		<fieldset>
				 		<legend>Administración de usuarios</legend>
				 		<div class="doce form-group">
					 		<label class="tres">Usuario:</label>
					 		<div class="nueve">
					 			<input type="text" name="usuario" id="usuario" class="form-control" maxlength="255" placeholder="usuario" 
					 			 <?php if($encontrado) echo 'value="'.$usuario->getUsuario().'" readonly'; ?>
					 			>
					 		</div>
					 	</div>
					 	<div class="doce form-group">
					 		<label class="tres">Contraseña:</label>
					 		<div class="nueve">
					 			<input type="password" name="password" id="password" class="form-control" maxlength="255" placeholder="password" <?php if($encontrado) echo 'value="******"'; ?>>
					 		</div>
					 	</div>
					 	<div class="doce form-group">
					 		<label class="tres">Confirmar contraseña:</label>
					 		<div class="nueve">
					 			<input type="password" name="password2" id="password2" class="form-control" maxlength="255" placeholder="confirma password" <?php if($encontrado) echo 'value="******"'; ?>>
					 		</div>
					 	</div>
					 	<div class="doce form-group">
					 		<label class="tres">Nombre:</label>
					 		<div class="nueve">
					 			<input type="text" name="nombre" id="nombre" class="form-control" maxlength="255" placeholder="nombre completo" <?php if($encontrado) echo 'value="'.$usuario->getNombre().'"'; ?>>
					 		</div>
					 	</div>
					 	<div class="doce form-group">
					 		<label class="tres">Email:</label>
					 		<div class="nueve">
					 			<input type="email" name="correo" id="correo" class="form-control" maxlength="255" placeholder="correo@gmail.com" <?php if($encontrado) echo 'value="'.$usuario->getEmail().'"'; ?>>
					 		</div>
					 	</div>					 	
					 	<?php 
					 		if(!$encontrado)
					 		{
					 	?>
							 	<div class="doce" id="rol" style="margin-bottom:10px;">
									<label class="tres">Rol:</label>
									<div id="lista_roles" class="nueve">
									  <select id="rol_usuario" name="rol_usuario" class="form-control"  <?php if($_SESSION['rol_usuario']>1) echo "readonly"; ?>>
									  	<?php
									  	require_once("../acceso/CRoles.php");
									  	$roles = new CRoles($db);
									  	echo $roles->getRoles(3);							  	
									  	?>
									  </select>
									</div>
								</div>
					 	<?php
					 		}
					 	if($encontrado)
					 	{
					 		if($acceso['w'])
					 		{					 		
					 		$checked = "";
					 		if($usuario->isActivo())
					 			$checked = "checked";
					 		echo '<div class="doce form-group">
					 		<label class="tres">Activo:</label>
					 			<div class="nueve">
					 			<input type="checkbox" name="activo" id="activo" class="uno" '.$checked.'>
					 			</div>
					 		</div>';
					 	 	}
					 	}
					 	?>

					 	<div class="form-group">
					 		<?php	
					 		 getAcciones(false, $encontrado, $acceso);					 		
					 		?>
					 		<button type="submit" id="btn_listausuarios" name="mostrar_lista" value="VERLISTAUSUARIOS" class="btn btn-warning">Ver usuarios</button>
					 	</div>
				 		</fieldset>
				 	</div>
				 </div>
			</div>
			</form>
			<?php			
			if(isset($_POST['mostrar_lista']))
			{				
			require_once("../acceso/CUsuario.php");
 			$usuario = new CUsuario($db); 			
			echo '<div id="tabla_exportar" class="table-responsive lista_usuarios">
				<table class="actividades">
					<thead>
						<th>ID</th>						
						<th>Usuario</th>						
						<th>Nombre</th>
						<th>Email</th>
						<th>Vigente</th>
						<th>Rol</th>
						<th>Fecha de captura</th>						
					</thead>
					<tbody>';
					echo $usuario->getListaUsuariosConRol();
					echo $usuario->getListaUsuariosSinRol();
			echo	'</tbody>
				</table>
				</div>';
				echo getExportarExcelButton(); //Utilidades.php
			}
			?>	
			</div>
		</section>
		<?php 
			echo getHomeButton();
			$db->close_conn();
			getScripts();
			getExcelExportScripts();
		?>
		<script src="../js/admin_users_validaciones.js"></script>				
	</body>
</html>