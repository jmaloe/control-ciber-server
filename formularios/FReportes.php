<?php
/*Autor: Jesus Malo, support: dic.malo@gmail.com*/
session_start();
 if(!isset($_SESSION['USER']))
 	header("Location:../");

 header('Content-type: text/html; charset=utf-8');
 require_once('../clases/CReportes.php');
 require_once("../db/AccessDB.php");
 require_once("Utilidades.php"); 
 if(!$db->connect())
 	echo "Error de conexión a la base de datos";
  $reporte = new CReportes($db);
?>
<!DOCTYPE html>
<html lang="es">
	<head>
		<title>Malo's Ciber: Reportes</title>
		<meta charset="utf-8" />
		<meta name="description" content="Formulario de reportes">
		<meta name="author" content="Jesús Malo Escobar">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<?php getStyles();?>        
        <link rel="stylesheet" href="../js/sss/sss.css" type="text/css" media="all">        
	</head>
	<body class="todo-contenido">
		<div class="container">
			<!--?php require_once("header.php"); ?-->
		    <form method="POST" action="FReportes.php" id="informes">
		    	<div class="panel panel-primary">
				    <div class="panel-heading">Reporte de ventas general</div>			
						<div class="panel-body">
			                <div class="doce">
								<p>Indicar rango de fechas para la búsqueda</p>
							</div>
					    	<div class="form-group">
					          <label for="f_inicial" class="tres">Fecha inicial:</label>
					            <div class="nueve">				
					                <input type="text" class="datepicker form-control seis" name="f_inicial" id="f_inicial" placeholder="dd/mm/aaaa" pattern="(0[1-9]|[12][0-9]|3[01])[/-](0[1-9]|1[012])[/-](19|20)\d\d" required>
					            </div>
					        </div>
					        <div class="form-group">
					            <label for="f_final" class="tres">Fecha final:</label>
					            <div class="nueve">
					                <input type="text" class="datepicker form-control seis" name="f_final" id="f_final" placeholder="dd/mm/aaaa" pattern="(0[1-9]|[12][0-9]|3[01])[/-](0[1-9]|1[012])[/-](19|20)\d\d" required>
					            </div>
					        </div>
					        <hr/>
							<button type="submit" name="accion" id="buscar" value="PorProducto" class="btn btn-success"><i class="fa fa-paper-plane-o"></i> Por producto</button>
							<button type="submit" name="accion" id="buscar" value="VentaDiaria" class="btn btn-warning"><i class="fa fa-calendar-check-o"></i> Venta diaria</button>
							<button type="submit" name="accion" id="buscar" value="VentaMensual" class="btn btn-info"><i class="fa fa-calendar-check-o"></i> Mensual</button>
				            <?php
				            	if(isset($_POST['accion']))
				            	{
						            if(isset($_POST['f_inicial']))
						            {
									 if($_POST['f_inicial']!=""){
										if($_POST['f_final']!="")
										{
											$reporte->setFechaFinal($_POST['f_final']);
										}
										else
										{
											$reporte->setFechaFinal(date("Y/m/d"));
										}
										$reporte->setFechaInicial($_POST['f_inicial']);				
										
										echo '<div class="slider">
						                		<canvas id="grafica"></canvas>                		
						            		  </div>';
									 }
									 echo "Reporte generado del ".$reporte->getFechaInicial()." hasta el ".$reporte->getFechaFinal();
									 echo '<div id="totalefectivo">Total</div>';
									}
								}
							?>
			        </div>        
		        </div>
		        <?php echo getHomeButton(); ?>
		    </form>	        
			<footer>Malo's Ciber 2017 - By: dic.malo@gmail.com</footer>
		</div>
		<?php getScripts(); ?>
		<script src="../js/ChartJS/Chart.js"></script>
		<script src="../js/sss/sss.min.js"></script>
		<script>
	        <?php
	        if(isset($_POST['accion']))
	        { 
	        	if($reporte->getGraficas($_POST['accion'])){
	        		echo 'ctx = document.getElementById("grafica").getContext("2d");'.chr(13);
					echo 'grafica = new Chart(ctx).Line(dataG1,{responsive:true, tooltipTemplate: "<%= value %>"});'.chr(13);					
	        	}
	        }
	        ?>
		</script>
		<script>
			$(document).ready(function(){

				$("#totalefectivo").html(<?php echo '"Total: $'.$reporte->getTotalEfectivo().'"'; ?>);

				$('.slider').sss({
					slideShow : false, // Set to false to prevent SSS from automatically animating.
					startOn : 0, // Slide to display first. Uses array notation (0 = first slide).
					transition : 400, // Length (in milliseconds) of the fade transition.
					speed : 3500, // Slideshow speed in milliseconds.
					showNav : true // Set to false to hide navigation arrows.
				});	

				var contSliders=1;
				var ctx, grafica;

				$(".sssnext").click(function(){				
					contSliders++;
					cambiarGrafica();
				});
				$(".sssprev").click(function(){
					contSliders--;
					cambiarGrafica();
				});			

				function cambiarGrafica(){
					$("#grafica").remove();
					$(".sss").append('<canvas id="grafica"></canvas>');								
					ctx = document.getElementById("grafica").getContext("2d");				
					if(contSliders==6)
						contSliders=1;
					else if(contSliders==0)
						contSliders=5;				
					switch(contSliders)
					{
						case 1:						
							grafica = new Chart(ctx).Line(dataG1,{responsive:true, tooltipTemplate: "<%= value %>"});
						break;
						case 2:						
							grafica = new Chart(ctx).Bar(dataG2,{responsive:true, tooltipTemplate: "<%= value %>"});						
						break;
						case 3:						
							grafica = new Chart(ctx).Radar(dataG3,{responsive:true, tooltipTemplate: "<%= value %>"});
						break;
						case 4:						
							grafica = new Chart(ctx).PolarArea(dataG4,{responsive:true, tooltipTemplate: "<%= label %> - <%= value %>"});
						break;
						case 5:						
							grafica = new Chart(ctx).Pie(dataG5,{responsive:true, tooltipTemplate: "<%= label %> - <%= value %>"});						
						break;
					}
				}
			});
		</script>
    </body>
</html>