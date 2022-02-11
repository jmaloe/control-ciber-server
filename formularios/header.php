<section id="acciones">
	<form id="form_principal" method="POST" action="FActividad.php">	
		<div class="logout">
			<span><?php echo $_SESSION['USER']; ?></span>
			<b>rol (</b>					
			<select name="roles" onChange='submit()' class="form-control dos" id="roles">
				<?php
					require_once('../acceso/CRoles.php');
				  	$roles = new CRoles($db);
  					$roles->setUsuario($_SESSION['USER']);
  					$roles->buscarByUser();

					if(isset($_POST['roles']))
					{
						$_SESSION['rol_usuario'] = $_POST['roles'];
						echo $roles->getRolesByUser($_POST['roles']);
					}
					else
					{
						if(isset($_SESSION['rol_usuario'])){
							echo $roles->getRolesByUser($_SESSION['rol_usuario']);
						}
						else
						{
							echo $roles->getRolesByUser(-1); /*-1 indica el rol por default, al iniciar sesion*/
							$_SESSION['rol_usuario'] = $roles->getDefaultRol();
						}
					}
					
					/*Una vez definido el rol solicitamos CPermisos.php*/
					require_once("../acceso/CPermisos.php");
				?>
			</select>
			<b>)</b>					
			<input type="hidden" name="usuario" id="usuario" <?php echo 'value="'.$_SESSION['USER'].'"'; ?>>
			<button type="button" class="btn btn-info" name="accion" id="ajustar" value="Ajustar"><i class="fa fa-cog"></i> Perfil</button>
			<a href="../acceso/logout.php" class="btn btn-danger"><i class="fa fa-sign-out"></i> Salir</a>
		</div>
	</form>	
</section>