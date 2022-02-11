<?php
/*Autor: Jesus Malo, support: dic.malo@gmail.com*/
header('Content-type: text/html; charset=utf-8');
session_start();
if(isset($_SESSION['USERNAME']))
	header("Location:../formularios/FActividad.php");
$msj = "";
if(isset($_POST['accion'])){
	require_once("../db/AccessDB.php");
	require_once("CUsuario.php");
	global $db;	
	$usuario = new CUsuario($db); 	
	$usuario->setUsuario($_POST['usuario']);
	$usuario->setPassword($_POST['password']);
	if($usuario->autenticar()){
		if($usuario->isActivo())
		{
			$_SESSION["ID_USER"] = $usuario->getIdUsuario();
			$_SESSION["USER"] = $usuario->getUsuario();
			$_SESSION['USERNAME'] = $usuario->getNombre();
			header("Location:../");
		}
		else{
			$msj = "Este usuario no está activo";
		}
	}
	else{
		$msj = "Usuario y/o contraseña incorrectos";
	}
	$db->close_conn();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
	<title>Malo's Ciber</title>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1">
	<meta name="description" content="Login">
	<meta name="author" content="Jesús Malo Escobar">
	<link rel="stylesheet" href="../css/bootstrap.min.css">
	<link rel="stylesheet" href="../css/bootstrap-theme.min.css">
	<link rel="stylesheet" href="../css/estilo.css">
	
</head>
<body class="todo-contenido ">
	<center class="centrado" style="margin-top:15%">
		<div style="display: flex; vertical-align: middle" >
			<div class="container">
				<section id="login">
					<form action="login.php" method="POST" accept-charset="UTF-8"
					enctype="application/x-www-form-urlencoded" autocomplete="off">
					<div class="notificacion">
						Bienvenido al <i>"Sistema de Cobro Automatizado"</i>, para entrar debes iniciar sesión.
					</div>
					<div class="login panel panel-primary">
						<div class="panel-heading border">Inicio de sesión</div>
						<div>
							<div class="form-group">
								<fieldset>
									<legend></legend>
									<div  class="doce form-group">
										<label style="padding-left:5%" class="tres">Usuario:</label>
										<div class="nueve" style="padding-right:5%">
											<input type="text" name="usuario" id="usuario" class="form-control" placeholder="Usuario" style="background-color:#01488a; color:white">
										</div>
									</div>
									<div class="doce form-group">	
										<label style="padding-left:5%" class="tres">Contraseña:</label>
										<div class="nueve" style="padding-right:5%">
											<input type="password" name="password" id="password" class="form-control" placeholder="Contraseña" style="background-color:#01488a; color:white">
										</div>
									</div>
									<div class="form-group">
										<label class="tres"></label>
										<center>
											<label for="accion" class="btn btn-default">
												<input type="submit" name="accion" id="accion" value="Entrar" class="btnsinfondo" style="color:#01488a" />
											</label>
										</center>
									</div>
								</fieldset>
							</div>
						</div>
						<?php
						if($msj != "")
						{
							echo '<div class="form-group panel-heading border"><label>'.$msj.'</label></div>';				 	
						}
						?>	
					</div>		
				</form>
			</section>
		</div>
	</div>
</center>
<script src="../js/jquery-1.11.2.min.js"></script>
<script src="../js/jquery-validation-1.13.1/jquery.validate.js"></script>
<script src="../js/bootstrap.min.js"></script>
<script src="../js/loading.js"></script>
</body>
</html>