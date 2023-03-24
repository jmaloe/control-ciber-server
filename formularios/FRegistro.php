<?php
/*Autor: Jesus Malo, support: dic.malo@gmail.com*/
//session_start();
 /*if(!isset($_SESSION['USER']))
 	header("Location:../");
*/
 require_once("Utilidades.php");

 require_once("../db/AccessDB.php");
 require_once("../clases/CEquipo.php");
 require_once("../clases/CProductos.php");

 $equipos = new CEquipo($db);
 $productos = new CProductos($db);

 if(isset($_GET['eliminar'])){
 	require_once("../clases/CVentas.php");
 	$obj = new CVentas($db);
 	$obj->setNoVenta($_GET['eliminar']);
 	$obj->eliminarVenta();
 }

 
?>
<!DOCTYPE html>
<html lang="es">
	<head>
		<title>Servicios JM: Registro de venta</title>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="description" content="Registro Entradas">
		<link href="../imagenes/favicon.ico" rel="icon" type="image/x-icon">
		<?php 
			getStyles();
		?>
	</head>
	<body class="todo-contenido">
		<audio id="audio_notificacion">  		
  			<source src="../audio/NotificationTone.mp3" type="audio/mpeg">
  			Tu navegador no soporta el elemento de audio
		</audio>
		<div id="container">		
			<form name="registroventas" id="RegistroVentas">
				<div class="panel panel-primary">
				    <div class="panel-heading">
				    	Formulario de Registro de Ventas
				    </div>
			    
					<div class="panel-body">
						
						<ul class="tabs-menu">
							<li id="tab1" class="current">
								<a href="#tab-1" class="goTab" value="1"><i class="fa fa-paper-plane-o" aria-hidden="true"></i> Venta</a>
							</li>			
							<li id="tab2">
								<a href="#tab-2" class="goTab" value="2"><i class="fa fa-history" aria-hidden="true"></i> Historial</a>
							</li>
							<li id="tab3">
								<a href="#tab-3" class="goTab" value="3"><i class="fa fa-tags" aria-hidden="true"></i> Precios</a>
							</li>
						</ul>						
						
						<div id="tab-1" class="tab-content">
							<div class="form-group" id="equipos">
								<?php
									echo '<label>Equipos disponibles <i class="fa fa-hand-o-down" aria-hidden="true"></i></label>';
									$equipos->getEquiposInactivos();
									echo '<label style="text-align:left;margin-top:5px">Servicios activos <i class="fa fa-thumbs-o-up" aria-hidden="true"></i></label>';
									echo '<div class="encabezado">								
											<div class="tres">
												<label>#PC</label>
											</div>
											<div class="nueve">
												<div class="seis">
													<label id="lbleq1">Articulo/Servicio</label>
												</div>
												<div class="dos">
													<label id="lbleq2">Cant.</label>
												</div>
												<div class="dos">
													<label id="lbleq3">P.V.</label>
												</div>
												<div class="dos">
													<label id="lbleq4">Total</label>
												</div>				
											</div>
										</div>';
									$equipos->getEquiposActivos();									
								?>									
							</div>
							<datalist id="articulos">
								<?php
									echo $productos->getProductos();
								?>
							</datalist>
						</div>
						<div id="tab-2" class="tab-content">
							<div id="date">
								<input type="date" id="fecha_consulta_i">
								⇒ <input type="date" id="fecha_consulta_f">
								<button type="button" class="btn btn-success btn_consultar_historial" value="2">Consultar</button>
							</div>
							<div id="HistorialVentas">

							</div>
						</div>
						<div id="tab-3" class="tab-content">							
							<div id="ListaPrecios">

							</div>
						</div>
					</div>
				</div>	  
			</form>
			<div id="dialog" title="Detalle">
  				<p>Esperando resultado...</p>
			</div>
		<?php echo getHomeButton(); ?>
		<footer>Servicios JM 2023 - By: dic.malo@gmail.com</footer>
		</div>
	</body>
</html>
<?php
 getScripts(); /*scripts y estilos comunes utilizados en los demas modulos del sistema*/ 
 $db->close_conn();
?>

<!--script src="../js/utilidades_registro.js"></script-->
<script>
$(document).ready(function() {	
	$("#HistorialVentas").on("click","tr",function(){		
		if($(this).attr("value")!=undefined){
			$.ajax({
		  	type: "POST", //id_producto,cantidad,precio,total, cnsdv+
		  	url: "HistorialVentas.php",
			  data: {"no_venta":$(this).attr("value")},
			  success: function(result){
			  	$("#dialog").html(result);			  		  	
		  	},
		  	dataType: "html"
			});			
			$( "#dialog" ).dialog();
		}			
	});

	$(".goTab").click(function(event){
		event.preventDefault();
    	$('.tabs-menu li.current').removeClass('current');
    	$("#tab"+$(this).attr("value")).addClass("current");    
    	var tab = $(this).attr("href");
    	$(".tab-content").not(tab).css("display", "none");
    	$(tab).fadeIn();    	
    	if($(this).attr("value")==2)
			consultaHistorial();
		else if($(this).attr("value")==3)
			consultaListaPrecios();
	});

	$(".btn_consultar_historial").click(function(){
		consultaHistorial();
	});

	$(".btn_consultar_precios").click(function(){
		consultaListaPrecios();
	});

	function consultaHistorial(){
		$.ajax({
		  type: "POST", //id_producto,cantidad,precio,total, cnsdv+
		  url: "HistorialVentas.php",
		  data: {
			"fecha_inicial":$("#fecha_consulta_i").val(),
			"fecha_final":$("#fecha_consulta_f").val()
		  },
		  success: function(result){
		  	$("#HistorialVentas").html(result);		  	
		  },
		  dataType: "html"
		});
	}

	function consultaListaPrecios(){
		$.ajax({
		  type: "POST", //id_producto,cantidad,precio,total, cnsdv+
		  url: "ListaPrecios.php",
		  data: {"accion":"consultar"},
		  success: function(result){
		  	$("#ListaPrecios").html(result);		  	
		  },
		  dataType: "html"
		});	
	}

	/*Ajustar el container al 100% si la pantalla es grande*/	
	if( $(window).width()>800 ){
		$("#container").addClass("container");
	}

	$("#equipos").on('click','.pagado',function()
	{
		location.reload();
	});

	$(".checkpagado").click(function()
	{		
		var estapagado=1;
		if($(this).hasClass('moneda-on')){
			if(!confirm("¿Desea desmarcar el pago?")){
				return;
			}
			estapagado=0;
			$(this).removeClass("moneda-on");
			$(this).addClass("moneda-off");
		}
		else{
			$(this).removeClass("moneda-off");
			$(this).addClass("moneda-on");
		}
		var parent = $(this).parent().parent();		
		$.ajax({
		  type: "POST", //id_producto,cantidad,precio,total, cnsdv+
		  url: "RegistrarEnDB.php",
		  data: {"id":$(parent).attr("id"), "accion":"SetPagado","no_venta":$(parent).attr("no_venta"),"pagado":estapagado},
		  success: function(response){
		  		console.log(response);
		  },
		  dataType: "html"
		});
	});
	
	
	var locked=false;
	$("#equipos").on('click','.comenzar',function()
	{
		locked=true;
		if($(this).hasClass("finalizar")){
			if(!confirm("¿Finalizar el servicio?")){
				return;
			}
			else{
				$("#"+$(this).val()+" .hf").val(getCurrentTime());
				$(this).toggleClass("btn-primary");
				$(this).toggleClass("finalizar");
				/*Registramos en la BD y XML*/
				var nodo = $(this).parent().parent().parent();
				$.ajax({
				  type: "POST",
				  url: "RegistrarEnDB.php",
				  data: {"id":$(nodo).attr('id'), "accion":"Finalizar","total":calcularTotalARegistrar($(this))},
				  success: function(result){
				  		//console.log(result);
				  		location.reload();
				  },
				  dataType: "html"
				});
			}
		}
		else
		{
			$("#"+$(this).val()+" .hi").val(getCurrentTime());
			$("#"+$(this).val()+" .hf").val("");
			$(this).toggleClass("btn-primary");
			$(this).toggleClass("finalizar");
			$("#prodsxeq_"+$(this).val()).html("");
			//getEstructuraArt($("#prodsxeq_"+$(this).val()),1,null)
			//$("#prodsxeq_"+$(this).val()).append(getEstructuraArt());
			var id_equipo = $(this).val();
			var nodo = $(this).parent().parent().parent();
			
			/*Registramos en la BD y XML*/
			$.ajax({
			  type: "POST",
			  url: "RegistrarEnDB.php",
			  data: {"id":$(nodo).attr('id'), "accion":"Registrar"},
			  success: function(result){
			  	
			  		console.log(result);
			  		//Registramos en DB el nuevo detalle
			  		var data = null;			  		
			  		//Agregamos atributo no_venta con valor x 
			  		$(nodo).attr('no_venta',$.trim(result)); 
			  		//Ponemos por default un producto de acuerdo al tipo de equipo	  		
			  		if($(nodo).attr("categoria")==4){						
						var precio_venta = getValorEnDataListByID(28,"precio_venta");
						data = {"id_equipo":id_equipo,"no_venta":$.trim(result),"id_producto":"28","cantidad":"1","precio":precio_venta,"total":precio_venta,"artDefault":getValorEnDataListByID(28,"value")};
					}
					/*else if($(nodo).attr("categoria")==3){
						var precio_venta = getValorEnDataListByID(2,"precio_venta");
						data = {"id_equipo":id_equipo,"no_venta":$.trim(result),"id_producto":"28","cantidad":"1","precio":precio_venta,"total":precio_venta,"artDefault":getValorEnDataListByID(2,"value")};
					}*/
					else{
						var precio_venta = getValorEnDataListByID(1,"precio_venta");
						data = {"id_equipo":id_equipo,"no_venta":$.trim(result),"id_producto":"1","cantidad":"1","precio":precio_venta,"total":precio_venta,"artDefault":getValorEnDataListByID(1,"value")};
					}
					agregarDetalleVentaEnDB( $("#prodsxeq_"+id_equipo), data);					
			  },
			  dataType: "html"
			});
			setTimeout(function(){location.reload();},800);			
		}
		locked=false;
	});

	$("#equipos").on('click','.agregar-articulo',function(){		
		var parent = $(this).parent().parent();	
		/*validamos que no se agreguen multiples inputs de articulos si ya hay uno donde no se ha seleccionado articulo*/
		var input_disponible=false;
		$("#"+$(this).val()+" input:text").each(function(){
			
			var valor_input = $(this).val();
			
			if(!$(this).val() | valor_input.indexOf("No especificado")>=0){
				if(!$(this).hasClass("nota"))
				{
					$(this).focus();
					input_disponible=true;
				}
			}
		});
		//si hay input no seleccionado entonces retornamos
		if(input_disponible)
			return;
		if($(parent).attr("no_venta")!=undefined)
			agregarDetalleVentaEnDB( 
				$("#"+$(this).val()) , 
				{
					"id_equipo":$(parent).attr("id"), 
					"no_venta":$(parent).attr("no_venta"),
					"id_producto":"28",
					"cantidad":"1",
					"precio":"2",
					"total":"2",
					"artDefault":getValorEnDataListByID(28,"value")
				}
			);
	});

	$("#equipos").on("click",".hf",function(event){
		event.preventDefault();
		userInput = prompt("Tiempo a utilizar el servicio (en minutos):","30");
		if(!userInput)
			return;
		var tiempo=Math.floor( userInput );
		tiempo = Math.abs(tiempo);
		if(tiempo!=null || tiempo!=""){
			if(!isNaN(tiempo)){
				objtime = $(this).parent().parent();
				horai = $(objtime).find(".hi").val();
				horaf = horai.split(":");
				horaf[0] = parseInt(horaf[0])+Math.floor(tiempo/60); //horas
				horaf[1] = parseInt(horaf[1])+(tiempo%60); //minutos
				if(horaf[1]>=60){
					horaf[1] = parseInt(horaf[1])-60;									
					horaf[0] = parseInt(horaf[0]) + 1;
				}
				if(horaf[0]>24)
					horaf[0] = parseInt(horaf[0]) - 24;				
				$(objtime).find(".hf").val((horaf[0]<10?'0'+horaf[0]:horaf[0])+":"+(horaf[1]<10?'0'+horaf[1]:horaf[1])+":"+horaf[2]);
				$("#equipos .hf").trigger("change");
			}
		}
	});

	function getEstructuraArt(nodo,no_registro,data){
		$(nodo).append('<div class="doce" no_registro="'+$.trim(no_registro)+'">'+
					'<div class="seis">'+
						'<input list="articulos" class="seleccion-articulo form-control" autocomplete="off" value="'+data.artDefault+'">'+
					'</div>'+
					'<div class="dos">'+
						'<input type="number" class="cantidad" value="'+data.cantidad+'" min="0" max="999">'+
					'</div>'+
					'<div class="dos">'+
						'<input type="number" class="precio" value="'+data.precio+'" min="0" max="999"/>'+
					'</div>'+
					'<div class="dos">'+
						'<input type="number" class="total" value="'+data.total+'" disabled />'+
					'</div>'+
				'</div>');
		$(nodo).last().find(".seleccion-articulo").focus();
	}

	function agregarDetalleVentaEnDB(nodo, data){
		//console.log(JSON.stringify(data));
		$.ajax({
		  type: "POST", //id_producto,cantidad,precio,total, cnsdv+
		  url: "RegistrarEnDB.php",
		  data: {"id":data.id_equipo, "accion":"AgregarDetalleDeVenta","no_venta":data.no_venta,"id_producto":data.id_producto,"cantidad":data.cantidad,"precio":data.precio,"total":data.total},
		  success: function(no_reg){
		  		getEstructuraArt(nodo,no_reg,data);
		  },
		  dataType: "html"
		});
	}

	function actualizarDetalleVentaEnDB(data){
		//console.log(JSON.stringify(data))
		$.ajax({
		  type: "POST", //id_producto,cantidad,precio,total, cnsdv+
		  url: "RegistrarEnDB.php",
		  data: {"cnsdv":data.cnsdv,
		  		 "id":data.id_equipo, 
		  		 "accion":"ActualizarDetalleDeVenta",
		  		 "id_producto":data.id_producto,
		  		 "cantidad":data.cantidad,
		  		 "precio":data.precio,
		  		 "total":data.total},
		  success: function(result){
		  	console.log(result);
		  },
		  dataType: "html"
		});
	}

	$("#equipos").on('change','.seleccion-articulo',function(){
		var id_producto = getValorEnDataList($(this).val(),"id");
		var parent = $(this).parent().parent();		
		var precio = getValorEnDataList($(this).val(), "precio_venta");
		var cantidad = $(parent).find(".cantidad").val();
		console.log($(parent).find(".cantidad"))
		$(parent).find(".cantidad").focus();
		$(parent).find(".precio").val(precio);
		$(parent).find(".total").val(cantidad*precio);
		calcularTotalxEquipo( $(this) );
		actualizarDetalleVentaEnDB({"cnsdv":$(parent).attr("no_registro"),"id_equipo":$(parent).parent().parent().attr("id"),"id_producto":id_producto,"cantidad":cantidad,"precio":precio,"total":cantidad*precio});
	});

	$("#equipos").on('change','.cantidad',function(){
		var parent = $(this).parent().parent();
		var precio = $(parent).find(".precio").val();
		var id_producto = getValorEnDataList($(parent).find(".seleccion-articulo").val(),"id");
		$(parent).find(".total").val( $(this).val()*precio );
		calcularTotalxEquipo( $(this) );
		actualizarDetalleVentaEnDB({"cnsdv":$(parent).attr("no_registro"),"id_equipo":$(parent).parent().parent().attr("id"),"id_producto":id_producto,"cantidad":$(this).val(),"precio":precio,"total":$(this).val()*precio});
	});

	$("#equipos").on('change','.precio',function(){
		var parent = $(this).parent().parent();
		var cantidad = $(parent).find(".cantidad").val();
		var id_producto = getValorEnDataList($(parent).find(".seleccion-articulo").val(),"id");
		$(parent).find(".total").val( $(this).val()*cantidad );
		calcularTotalxEquipo( $(this) );
		actualizarDetalleVentaEnDB({"cnsdv":$(parent).attr("no_registro"),"id_equipo":$(parent).parent().parent().attr("id"),"id_producto":id_producto,"cantidad":cantidad,"precio":$(this).val(),"total":$(this).val()*cantidad});
	});

	$("#equipos").on('change','.hi',function(){
		var parent = $(this).parent().parent().parent();
		var id_equipo=parent.attr("id");		
		var horainicial = $(this).val();
		$.ajax({
		  type: "POST",
		  url: "RegistrarEnDB.php",
		  data: {"id":id_equipo, "accion":"SetHoraInicio","hora_inicio":horainicial},
		  success: function(result){
		  		console.log(result);
		  },
		  dataType: "html"
		});
	});

	$("#equipos").on('change','.hf',function(){
		var parent = $(this).parent().parent().parent();
		var id_equipo=parent.attr("id");		
		var horafinal = $(this).val();
		$.ajax({
		  type: "POST",
		  url: "RegistrarEnDB.php",
		  data: {"id":id_equipo, "accion":"SetHoraTermino","hora_termino":horafinal},
		  success: function(result){
		  		console.log(result);
		  		//location.reload();		  				
		  },
		  dataType: "html"
		});
	});

	$(".nota").on("change",function(){
		var parent = $(this).parent().parent();		
		var id_equipo=parent.attr("id");
		$.ajax({
		  type: "POST",
		  url: "RegistrarEnDB.php",
		  data: {"id":id_equipo, "accion":"AgregarNota","no_venta":$(parent).attr("no_venta"),"nota":$(this).val()},
		  success: function(result){
		  		console.log(result);		  		
		  },
		  dataType: "html"
		});

	});

	function getValorEnDataList(dato_a_buscar, atributoADevolver){
		var x = dato_a_buscar;
		var z = $("#articulos");
		var val = $(z).find('option[value="'+x+'"]');
		var endval = val.attr(atributoADevolver);
		if(endval!=undefined)
		 return endval;	
		else
		 return 0;
	}

	function getValorEnDataListByID(id, atributoADevolver){
		var x = id;
		var z = $("#articulos");
		var val = $(z).find('option[id="'+x+'"]');
		var endval = val.attr(atributoADevolver);
		if(endval!=undefined)
		 return endval;	
		else
		 return 0;
	}

	function getCurrentTime(){
		var currentdate = new Date();
		return currentdate.getHours()+":"+(currentdate.getMinutes()<10?'0':'') + currentdate.getMinutes();
	}

	Date.prototype.yyyymmdd = function(){
  	var mm = this.getMonth() + 1; // getMonth() is zero-based
  	var dd = this.getDate();
  	return [this.getFullYear(),
          (mm>9 ? '' : '0') + mm,
          (dd>9 ? '' : '0') + dd
         ].join('-');
	};

	function getCurrentDate(){
		var date = new Date();
		return date.yyyymmdd();
	}

	function calcularTotalxEquipo(nodo){
		var total=0;
		var grandparent = $(nodo).parent().parent().parent();

		$('.total',$(grandparent)).each(function () {
				total = total + Number( $(this).val() );
		});
		//alert($(grandparent).html());

		$(grandparent).parent().find('.totalxeq').html("Total: $<b>"+total+"</b>");
		return total;
	}

	function calcularTotalARegistrar(nodo){
		var total=0;
		var grandparent = $(nodo).parent().parent().parent();

		$('.total',$(grandparent)).each(function () {
				total = total + Number( $(this).val() );
		});		
		return total;
	}

	function calcularTiempoyTotales(){
		$.ajax({
		  type: "POST",
		  url: "EquiposXML.php",
		  success: function(xml){
		  //console.log(xml);
		  	$(xml).find('pc').each(function(index){
		  		if($(this).attr("status")=="running")
		  		{		  			
		  			/*Buscamos el PC con el id proporcionado, si no esta en la lista recargamos la pagina para que se muestre*/
		  			if(!$("#"+$(this).attr("id")).hasClass("device")){		  				
		  				location.reload();
		  			}
			  		$(this).find('prodservs').each(function(index){
			  			$(this).find("producto").each(function(index){
			  				if($(this).attr("id")==1){ /*id 1 es para Internet*/
			  					if( $("[no_registro="+$(this).attr("noreg")+"]").find(".seleccion-articulo").val()!="" ){
			  						var cantidad = $(this).find("cantidad").text();
			  						cantidad = cantidad.split(" mins");
			  						$("[no_registro="+$(this).attr("noreg")+"]").find(".cantidad").val(cantidad[0]);
			  						$("[no_registro="+$(this).attr("noreg")+"]").find(".total").val($(this).find("total").text());
			  						/*Recalculamos el total de los equipos activos*/
			  						calcularTotalxEquipo($("[no_registro="+$(this).attr("noreg")+"]").find(".cantidad"));
			  					}
			  				}
			  			});
			  		});
		  		}else{
		  			/*verificamos si el equipo con id x estaba activo para mostrar el boton de pagar*/
		  			if($("#"+$(this).attr("id")).find(".comenzar").hasClass("finalizar")){
		  				$("#"+$(this).attr("id")).find(".pagado").fadeIn(2000);
		  				//si el equipo ya no esta en uso, recargamos las paginas que tengan el formulario abierto para mostrar solo los activos
		  				//location.reload();
		  			}
		  		}
        	});
		  },
		  error : function (xhr, ajaxOptions, thrownError){
            console.log(xhr.status);
            console.log(thrownError);
    	  }, 
		  contentType: "text/xml",
    	  dataType: "text"
		});
	}
	
	/*calcular tiempo y totales cada 15 segundos*/
	setInterval(function(){
		if(!locked)
		{
			calcularTiempoyTotales();
			consultarHoraFinalizacion();
		}
	},15000);

	$("#fecha_consulta_i").val(getCurrentDate());
	$("#fecha_consulta_f").val(getCurrentDate());

	calcularTiempoyTotales(); //al cargar la pagina web de inmediato calculamos los totales

	/*seleccionamos todo el contenido del input para borrarlo mas rapidamente*/
	$('#equipos').on('focus','input', function() {
	    $(this).select();
	});

	function consultarHoraFinalizacion()
	{
		$('#equipos .device').each(function(index){
			if($(this).attr('id')!==undefined){
				//console.log($(this).attr("id"))
				if($(this).find(".hf")!==""){
					var horaactual = getCurrentTime()+":00";
					var horadefinalizacion = $(this).find(".hf").val();
					horadefinalizacion = horadefinalizacion.split(":");
					horadefinalizacion = horadefinalizacion[0]+":"+horadefinalizacion[1]+":00";
					if(horaactual==horadefinalizacion){
						console.log(horaactual+" --- "+horadefinalizacion);
						notifyMe($(this).attr('name'));
					}
				}			
			}
		});
	}

	// request permission on page load
	document.addEventListener('DOMContentLoaded', function () {
	  if (!Notification) {
	    alert('Las notificaciones de escritorio no estan disponible en tu navegador. Intenta usar Chromium.'); 
	    return;
	  }

	  if (Notification.permission !== "granted")
	    Notification.requestPermission();
	});

	function notifyMe(nombre_equipo) {
	  if (Notification.permission !== "granted")
	    Notification.requestPermission();
	  else {	  	
        var audio = document.getElementById("audio_notificacion");
        audio.play();        
	    var notification = new Notification("Servicios JM", {
	      icon: '../imagenes/favicon.ico',
	      body: "Tiempo terminado para "+nombre_equipo,
	    });

	    notification.onclick = function () {
	      window.open("https://localhost/MyControl/formularios/FRegistro.php");
	      //$(this).hide();
	    };

	  }

	}

});
</script>