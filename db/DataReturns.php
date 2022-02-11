<?php
/*Autor: Jesus Malo, support: dic.malo@gmail.com*/
 class DataReturns{

  var $elementos, $campos_edicion;
 	/*Devuelve valores para el datalist, especifique en su consulta SQL el id y el valor para el option*/
 	public function getDataListItems($result){
 		$datos="";
 		$elementos=0;
		while($fila = mysqli_fetch_row($result))
		{
		   $elementos++;
		   $datos.="<option id='".$fila[0]."' value='".$fila[1]."'/>";
		}
		return $datos;
 	}

 	public function getCustomDataListItems($result){
 		$datos="";
 		while($fila = mysqli_fetch_assoc($result)){
 			$datos.='<option ';
 			foreach ($fila as $key => $value) {
 				$datos.=$key.'="'.$value.'"';
 			}
 			$datos.='/>';
 		}
 		return $datos;
 	}

 	public function getSelectItems($result, $defaultselect){
 		$datos="";
 		$elementos = 0; 		
		while($fila = mysqli_fetch_row($result))
		{
		  $elementos++;
		   if($fila[0]==$defaultselect)
		   	$datos.="<option value='".$fila[0]."' selected>".$fila[1]."</option>";
		   else
		    $datos.="<option value='".$fila[0]."'>".$fila[1]."</option>";
		}
		return $datos;
 	}

 	public function getLiItems($result, $defaultselect){
 		$datos="";
 		$elementos = 0; 		
		while($fila = mysqli_fetch_row($result))
		{
		  $elementos++;
		   if($fila[0]==$defaultselect)
		   	$datos.='<li class="selected"><a href="Admin.php?id_t='.$fila[0].'">'.$elementos." - ".$fila[0].'</a></li>';
		   else
		    $datos.='<li><a href="Admin.php?id_t='.$fila[0].'">'.$elementos." - ".$fila[0].'</a></li>';
		}
		return $datos;
 	}

 	/*para formar el select con optgroup pasar 4 columnas: idTablaSuperior,descripcionTablaSuperior,idTablaInferior,descripcionTablaInferior*/
 	public function getOptionGroupsItems($result){
 		$id=0;
		$datos="";
		while($fila = mysqli_fetch_row($result)) {
		 if($fila[0]!=$id)
		 {
		   if($id>1)
		    $datos.="</optgroup>";	
		   $datos.="<optgroup label='".$fila[1]."'>";
		   $id=$fila[0];
		 }
		 $datos.="<option value='".$fila[2]."::".$fila[3]."'>".$fila[3]."</option>";
		}
		return $datos;
 	}

 	public function getElementosDeTabla($result){
		$datos=""; 		
		while($fila = mysqli_fetch_row($result)){
			$datos.="<tr>";
			foreach ($fila as $key => $value)
			{				
				$datos.="<td>".$value."</td>";
			}
			$datos.="</tr>";
		}
		return $datos;
 	}

 	public function getRowsTableItems($result){
 		$datos="";
 		while($fila = mysqli_fetch_row($result))
 		{
 			$datos.="<tr>";
 			foreach ($fila as $key => $value)
 			{
 				$datos.='<td align="center">'.$value.'</td>';
 			} 		
 			$datos.="</tr>"; 			
 		}
 		return $datos;
 	}

 	public function getFechaToMysql($fecha){
 		return date('Y-m-d', strtotime(str_replace('/', '-', $fecha)));
 	}

 	public function getFechaFromMysql($fecha){
 		return date('d-m-Y',strtotime($fecha));
 	}

 	public function getTimeToMysql($hora){
 		return date('H:i:s', strtotime($hora));
 	}

 	public function getTimeFromMysql($hora){
 		return date('H:i A', strtotime($hora));
 	}

 	public function getReqItems($result){
 		$id=0;
		$datos="";
		while($fila = mysqli_fetch_assoc($result)) {
		 if($fila['id_cat_req']!=$id)
		 {
		   $datos.='<p class="titulo">'.$fila['descripcion'].'</p>';
		   $id=$fila['id_cat_req'];
		 }
		 //$datos.="<option value='".$fila[2]."::".$fila[3]."'>".$fila[3]."</option>";
		 $datos.='<div class="form-group">				
				<label class="'.$fila['l_class'].'">'.$fila['label'].'</label>
				<div class="'.$fila['id_input_class'].'">';
				if($fila['input']=="text")
				 	$datos.='<input type="text" name="'.$fila['id_input'].'" id="'.$fila['id_input'].'" class="form-control requisito" placeholder="'.$fila['id_input_placeholder'].'" />';
				else if($fila['input']=="textarea")
					 $datos.='<textarea name="'.$fila['id_input'].'" id="'.$fila['id_input'].'" class="form-control requisito" placeholder="'.$fila['id_input_placeholder'].'"></textarea>';
		 $datos.='</div>
			</div>';
		}
		return $datos;
 	}

 	public function setCamposEdicion($arreglo){
 		$this->campos_edicion = $arreglo; 		
 	}

 	public function getCamposEdicion(){
 		return $this->campos_edicion;
 	}

 	function hayCampoEdicion($campo){
 		foreach ($this->campos_edicion as $key => $value){
 			$step1 = explode("::", $value); //separamos el length del campo p.e. [0]12::[1]fecha_inicio
 			if($step1[1]==$campo)
 				return $step1[0];
 		}
 		return -1;
 	}

 	public function getElementosDeTablaConEdicion($result, $id_item, $listados){
		$datos="";
		$cont=0;
		$id_row=-1;

		/*verificamos si hay id de edicion proporcionado en el arreglo en la posicion 0*/
		$edition_row = $id_item;
		$edition_row_formed=false;		
		while($fila = mysqli_fetch_assoc($result)){
			$datos.="<tr>";
			if($listados){
				$cont++;
				$datos.="<td>".$cont."</td>";
			}

			$index=0;

			foreach ($fila as $nombre_columna => $value)
			{
				/*tomamos el id de la fila para comparación en la edición*/
				if($index==0)
				{
					$id_row=$value;				
					$index=1;
				}
				/*si hay fila por editar*/
				$edit_on=false;
				if($id_row==$edition_row){
					$edit_on=true;
					$maxlength = $this->hayCampoEdicion($nombre_columna);					
					if($maxlength>0){
						/*si es fecha le asignamos datepicker*/
						if (DateTime::createFromFormat('Y-m-d', $value) !== FALSE){
							$datos.='<td><input type="text" class="form-control datepicker" name="campo_edicion[\''.$nombre_columna.'\']"  value="'.$value.'" maxlength="'.$maxlength.'"/></td>';	
						}
						else
						{
							$datos.='<td><input type="text" class="form-control" name="campo_edicion[\''.$nombre_columna.'\']"  value="'.$value.'" maxlength="'.$maxlength.'"/></td>';
						}						
					}
					else{
						$datos.='<td>'.$value.'</td>';
					}
					$edition_row_formed=true;
				}
				if(!$edit_on)
				{
					$datos.="<td>".$value."</td>"; //si no hay edicion solo mostramos el valor
				}
			}
			if($edition_row_formed)
				$edition_row=-1;
			
			if($edition_row_formed){
				$datos.='<td style="display:inline-flex;">
							<button type="submit" name="accion" value="a:?:'.$id_row.'" class="btn btn-success actualizar novalidar" title="Actualizar" style="margin-right:2px;"><i class="fa fa-floppy-o"></i></button>
							<button type="submit" name="accion" value="c:?:'.$id_row.'" class="btn btn-default cancelar novalidar" title="Cancelar"><i class="fa fa-ban"></i></button>
						</td>';
						$edition_row_formed=false;
			}
			else{
				$datos.='<td style="display:inline-flex;">
							<button type="submit" name="accion" value="m:?:'.$id_row.'" class="btn btn-info editar novalidar" style="margin-right:2px;" title="Editar"><i class="fa fa-pencil"></i></button>
							<button type="submit" name="accion" value="e:?:'.$id_row.'" class="btn btn-danger eliminar novalidar" title="Eliminar"><i class="fa fa-trash"></i></button>
						</td>';	
			}
			
			$datos.="</tr>";
		}
		return $datos;
 	}

 }
?>