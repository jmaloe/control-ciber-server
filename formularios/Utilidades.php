<?php
/*Autor: Jesus Malo, support: dic.malo@gmail.com*/

 function getStyles(){ 	
 	echo "<link rel='stylesheet' href='../css/bootstrap.min.css'>
		<link rel='stylesheet' href='../css/bootstrap-theme.min.css'>
		<link rel='stylesheet' href='../js/jquery-ui-1.11.3.custom/jquery-ui.css'>
		<link rel='stylesheet' href='../css/openModal.css'>
		<link rel='stylesheet' href='../css/estilo.css'>
		<link rel='stylesheet' href='../css/tabs.css'>
		<link rel='stylesheet' href='../css/jquery-ui-timepicker.css'>
		<link rel='stylesheet' href='../css/font-awesome.min.css'>";
 }
 
 function getScripts(){
	echo '<script src="../js/jquery-1.11.2.min.js"></script>
		<script src="../js/jquery-ui-1.11.3.custom/jquery-ui.js"></script>
		<script src="../js/jquery.ui.timepicker.js"></script>	
		<script src="../js/jquery-validation-1.13.1/jquery.validate.js"></script>
		<script src="../js/bootstrap.min.js"></script>
		<script src="../js/datepickerconfig.js"></script>
		<script src="../js/utilidades.js"></script>
		<script src="../js/loading.js"></script>';
 }

 function getExcelExportScripts(){
 	echo '<script src="../js/xlsx/js/jszip.js"></script>
		<script src="../js/xlsx/js/jszip-load.js"></script>
		<script src="../js/xlsx/js/jszip-deflate.js"></script>
		<script src="../js/xlsx/js/jszip-inflate.js"></script>
		<script src="../js/xlsx/js/xlsx.js"></script>';
 }

 function getHomeButton(){
 	return '<a href="../" class="btn btn-default"><i class="fa fa-home"></i> Página principal</a>';
 }

 function getExportarExcelButton(){
 	return '<br><button id="btn_exportar_xls" class="btn btn-success"><i class="fa fa-file-excel-o"></i> Exportar a excel</button>';
 }

 function getAcceptButton(){
 	return '<button type="submit" name="accion" id="btn_aceptar" class="aceptar btn btn-primary separador" value="ACEPTAR"><i class="fa fa-check"></i> Aceptar</button>';
 }

 function getSaveButton(){
 	return '<button type="submit" name="accion" id="btn_guardar" class="btn btn-success separador" value="GUARDAR"><i class="fa fa-floppy-o"></i> Guardar</button>'; 	
 }

 function getUpdateButton(){
 	return '<button type="submit" name="accion" id="btn_actualizar" class="aceptar btn btn-success separador" value="ACTUALIZAR"><i class="fa fa-floppy-o"></i> Actualizar</button>';
 } 

 function getSearchButton(){
 	return '<button type="submit" name="accion" id="btn_buscar" class="btn_buscar btn btn-info separador" value="BUSCAR"/><i class="fa fa-search"></i> Buscar</button>';
 }

 function getCancelButton(){
 	return '<button type="submit" name="accion" id="btn_cancelar" class="btn_cancelar btn btn-default separador" value="CANCELAR"><i class="fa fa-ban"></i> Cancelar</button>'; 	
 }

 function getDeleteButton(){
 	return '<button type="submit" name="accion" id="btn_eliminar" class="btn btn-danger" value="ELIMINAR"><i class="fa fa-trash"></i> Eliminar</button>';
 }

 function getPlusButton($id){
 	return '<button type="button" id="'.$id.'" class="btn btn-default"/><i class="fa fa-plus-circle"></i></button>';
 }

 function getAcciones($isAdd, $isUpdate, $permisos){
 	if($isAdd)
 	{
 		if($permisos['r'])
 			echo getAcceptButton();	
	}
	if($isUpdate)
	{
	 	if($permisos['u'])
		  echo getUpdateButton();
	}
	else
	{
	 	if($permisos['w'])
			echo getSaveButton();
	}
	if($permisos['r'])
		echo getSearchButton();
	echo getCancelButton();
	if($isUpdate)
	  if($permisos['d'])
		echo getDeleteButton();
 }
?>