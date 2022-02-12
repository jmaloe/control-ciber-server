$(function(){

 $("#btn_aceptar").click(function()
 {
 	if($("#password").val()!=$("#password2").val() & $("#password").val()!="")
 	{
 		$(this).closest('form').data("validator").cancelSubmit = true;        		
		alert("Las contraseñas no coinciden");				
		return false;
	}
});

 $("#form_users").validate({
 	lang: 'es',
	rules: {
		usuario: {required: true, maxlength:25},
		nombre: {required: true, maxlength:255},
		correo:{required:true, maxlength:255},
		password:{required:true, maxlength:255,minlength: 6},
		password2:{required:true, maxlength:255,minlength: 6}
	},
	messages: {
		usuario: {required: "Usuario requerido", maxlength:"25 caracteres máximo"},
		nombre: {required: "Nombre requerido", maxlength:"255 caracteres máximo"},
		correo:{required:"Especifíque un correo", maxlength:"255 caracteres máximo"},
		password:{required:"Contraseña requerida", maxlength:"255 caracteres máximo",minlength:"Mínimo 6 caracteres"},
		password2:{required:"Confirme contraseña", maxlength:"255 caracteres máximo",minlength:"Mínimo 6 caracteres"}
	},
	tooltip_options: {
		id_evento: {trigger:'focus'}
	},
	focusInvalid: false,
    invalidHandler: function(form, validator) {

        if (!validator.numberOfInvalids())
            return;

        $('html, body').animate({
            scrollTop: $(validator.errorList[0].element).offset().top
        }, 500);

    }
 });

$("#btn_eliminar").click(function(){

	if(confirm("¿Está seguro de eliminar el registro?")){
		$(this).closest('form').submit();
	}
	else
	{
		return false;
	}
});

$("#btn_listausuarios").click(function(){
	$('input').each(function() {
	   	$(this).rules('remove', 'required');
	});
	$(this).closest("form").attr("novalidate");
});

 $("#form_users").submit(function(event){
	if($("#password").val()!=$("#password2").val() & $("#password").val()!=""){
		event.preventDefault();
		alert("Las contraseñas no coinciden");				
	}
});

 /*prepara el valor del hidden id_adscripcion con el valor seleccionado del datalist*/
 $("#adscripcion").bind('input',function(){ 	
	$("#id_adscripcion").val( getDataListIdValue("#adscripcion","#adscripciones") );
 });

});