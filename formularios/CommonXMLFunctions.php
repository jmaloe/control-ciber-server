<?php
function calcular_tiempo_trasnc($hi,$hf){ 
    $separar[1]=explode(':',$hi); 
    $separar[2]=explode(':',$hf); 

    $total_minutos_trasncurridos[1] = ($separar[1][0]*60)+$separar[1][1]; 
    $total_minutos_trasncurridos[2] = ($separar[2][0]*60)+$separar[2][1]; 
    $total_minutos_trasncurridos = $total_minutos_trasncurridos[1]-$total_minutos_trasncurridos[2]; 
    return $total_minutos_trasncurridos;
}

/*calcular costo por tiempo de uso de internet*/
function calcularTotal($minutos){
    if($minutos>=0 & $minutos<=4)
        return 3;
    else if($minutos>4 & $minutos<=32)
        return 5;
    else if($minutos>32 & $minutos<=60)
        return 10;
    else{
        $horas = floor($minutos/60);
        $mins = $minutos%60;
        if($mins>2)
            return ($horas*10)+calcularTotal($mins);
        return ($horas*10);
    }
}
?>