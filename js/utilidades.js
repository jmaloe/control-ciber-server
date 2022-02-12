$(document).ready(function() {

	/*exportar a excel al hacer click en el elemento que tenga id btn_exportar_xls*/	
	
	$("#btn_exportar_xls").click(function(e){      
	  e.preventDefault();
	  var file = {
	    worksheets: [[]], // worksheets has one empty worksheet (array)
	    creator: 'jesus.malo@unach.mx', created: new Date(),
	    lastModifiedBy: 'jesus.malo@unach.mx', modified: new Date(),
	    activeWorksheet: 0
	    }, w = file.worksheets[0]; // cache current worksheet
	    w.name = "RESULTADOS_REPORTE";

	  $('#tabla_exportar tr').each(function(){
	    var r = w.push([]) - 1; // index of current row
	    $(this).find('th').each(function(){
	      w[r].push($(this).text());
	    });
	    $(this).find('td').each(function() {
	    	if($(this).text()!="")
	    	{
	      		w[r].push($(this).text());
	      	}
	      	else
	      	{
	      		if($(this).children().length>0)
	      		{
	      			w[r].push($(this).children().attr("title"));
	      		}
	      		else{
	      			w[r].push("");
	      		}

	      	}
	    });
	  });

	  window.location.href = xlsx(file).href();
	});	


	/*Deshabilitamos enter para evitar submit form*/
	$("input").keypress(function( e ){
	  if(!$(this).is('textarea'))
	  {
	    if(e.which == 13)
	    {
	     e.preventDefault();
	    }
	  }  
	});


	$(".btn_buscar").on('click', function()
	{
		$('input').each(function() {
			$(this).rules('remove', 'required');
		});	 	
	});

	$(".btn_cancelar").on('click', function(evt)
	{
		$(this).closest('form').data("validator").cancelSubmit = true;        
		$(this).closest('form').trigger("reset");
		$(this).closest('form').submit();
		return false;
 	});

 	try{
		$( "#f_inicial" ).datepicker();	
		$( "#f_final" ).datepicker();
 	}catch(err){ }

 	$("#ajustar").click(function(){
		var form=$("<form/>").attr({
	    	method: "post",
	    	action: "FAdminUsuarios.php"
		});
		form.append($("<input/>").attr({name:"accion",value:'Ajustar'}));
		form.append($("<input/>").attr({name:"usuario",value:$("#usuario").val()}));		
		form.submit();
	});

}); /*FIN $(document).ready*/

	/*Devuelve el id del elemento seleccionado del datalist mediante el input list*/
	function getDataListIdValue(input_list_id, datalist_id){
		var valor = $(input_list_id).val();
		var obj = $(datalist_id);
		var val = $(obj).find('option[value="'+valor+'"]');
		var endval = val.attr('id');
		if(endval!=undefined)
		 return endval;	
		else
		 return 0;
	}

	/*Devuelve el valor del elemento seleccionado buscado por el id en el datalist*/
	function getDataListValueById(datalist_id, id){
		var valor = id;
		var obj = $(datalist_id);
		var val = $(obj).find('option[id="'+id+'"]');
		var endval = val.attr('value');
		if(endval!=undefined)
		 return endval;	
		else
		 return 0;
	}

	/*Devuelve el valor del elemento seleccionado mediante el atributo indicado del datalist*/
	function getDataListValue(input_list_id, datalist_id, atributo){
		var x = $(input_list_id).val();
		var z = $(datalist_id);
		var val = $(z).find('option[value="'+x+'"]');
		var endval = val.attr(atributo);
		if(endval!=undefined)
		 return endval;	
		else
		 return 0;
	}

	/*Ajusta el ALTO de los openModal que contienen los iframes*/
	function ajustarAltoIframe(iframe, valor){
		parent.document.getElementById(iframe).height = valor+"px";
	}

	function getTagDocumento(numDoc){
		if(numDoc)
			return '<span class="view_doc" doc='+numDoc+'></span>';
		else
			return "";
	}

	function getRemoveButton(){
		return '<button type="button" class="btn btn-danger quitarElemento" title="Quitar"><i class="fa fa-minus-circle"></i></button>';
	}

	function sumaFecha(d, fecha)
	{
		var Fecha = new Date();
		var sFecha = fecha || (Fecha.getDate() + "/" + (Fecha.getMonth() +1) + "/" + Fecha.getFullYear());
		var sep = sFecha.indexOf('/') != -1 ? '/' : '-'; 
		var aFecha = sFecha.split(sep);
		var fecha = aFecha[2]+'/'+aFecha[1]+'/'+aFecha[0];
		 fecha= new Date(fecha);
		 fecha.setDate(fecha.getDate()+parseInt(d));
		var anno=fecha.getFullYear();
		var mes= fecha.getMonth()+1;
		var dia= fecha.getDate();
		 mes = (mes < 10) ? ("0" + mes) : mes;
		 dia = (dia < 10) ? ("0" + dia) : dia;
		var fechaFinal = dia+sep+mes+sep+anno;
	 return fechaFinal;
	}