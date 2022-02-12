$(document).ready(function() {	
    //guardamos en una variable el alto del que tiene tu browser que no es lo mismo que del DOM
    //var alto=$(document).height();
    //agregamos en el body un div que sera que ocupe toda la pantalla y se muestra encima de todo
    $("body").append("<div id='pre-load-web'><div id='imagen-load'><img src='../imagenes/cargando.gif?"+ Math.random() + "' /><br />Cargando</div></div>");    
    //le damos el alto
    $("#pre-load-web").css({height:$(document).height()+"px"});
    //esta sera la capa que esta dento de la capa que muestra un gif
    $("#imagen-load").css({"margin-top":($(window).height()/2)-30+"px"});
});

$(window).load(function(){
   $("#pre-load-web").fadeOut("slow",function()
   {
       //eliminamos la capa de precarga
       $(this).remove();
   });        
});