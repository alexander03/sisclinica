@if($trama == 'TAA0')
{{ $fechatrama }}|{{ $datos->codigo1 }}|{{ $datos->codigo2 }}|{{ $datos->consultoriosfi }}|{{ $datos->consultoriosfu }}|{{ $datos->camas }}|{{ $datos->medicost }}|{{ $datos->medicoss }}|{{ $datos->medicosr }}|{{ $datos->enfermeras }}|{{ $datos->odontologos }}|{{ $datos->psicologos }}|{{ $datos->nutricionistas }}|{{ $datos->tecnologos }}|{{ $datos->obstetrices }}|{{ $datos->farmaceuticos }}|{{ $datos->auxiliares }}|{{ $datos->otros }}|{{ $datos->ambulancias }}
@endif

@if($trama == 'TAB1')
<?php $totalatenciones = 0; $i = 1; $texto = ''; ?>
@for($a = $i; $a < count($elementos); $a++)
<?php if($elementos[$a]->edad < 1) { $edad = 1; $totalatenciones += $elementos[$a]->totalatenciones; $i++;}
else { if($totalatenciones != 0) { $texto .= trim($fechatrama.'|'.$datos->codigo1.'|'.$datos->codigo2.'|1|1|'.$totalatenciones.'|0|'.$totalatenciones)."\n"; }$totalatenciones = 0; break; } ?>	
@endfor
@for($a = $i; $a < count($elementos); $a++)
<?php if($elementos[$a]->edad >= 1 && $elementos[$a]->edad <= 4) { $edad = 2; $totalatenciones += $elementos[$a]->totalatenciones; $i++;}
else { if($totalatenciones != 0) { $texto .= trim($fechatrama.'|'.$datos->codigo1.'|'.$datos->codigo2.'|1|2|'.$totalatenciones.'|0|'.$totalatenciones)."\n"; } $totalatenciones = 0; break; } ?>
@endfor
@for($a = $i; $a < count($elementos); $a++)
<?php if($elementos[$a]->edad >= 5 && $elementos[$a]->edad <= 9) { $edad = 3; $totalatenciones += $elementos[$a]->totalatenciones; $i++;}
else { if($totalatenciones != 0) { $texto .= trim($fechatrama.'|'.$datos->codigo1.'|'.$datos->codigo2.'|1|3|'.$totalatenciones.'|0|'.$totalatenciones)."\n"; } $totalatenciones = 0; break; } ?>
@endfor
@for($a = $i; $a < count($elementos); $a++)
<?php if($elementos[$a]->edad >= 10 && $elementos[$a]->edad <= 14) { $edad = 4; $totalatenciones += $elementos[$a]->totalatenciones; $i++;}
else { if($totalatenciones != 0) { $texto .= trim($fechatrama.'|'.$datos->codigo1.'|'.$datos->codigo2.'|1|4|'.$totalatenciones.'|0|'.$totalatenciones)."\n";  }$totalatenciones = 0; break; } ?>	
@endfor
@for($a = $i; $a < count($elementos); $a++)
<?php if($elementos[$a]->edad >= 15 && $elementos[$a]->edad <= 19) { $edad = 5; $totalatenciones += $elementos[$a]->totalatenciones; $i++;}
else { if($totalatenciones != 0) { $texto .= trim($fechatrama.'|'.$datos->codigo1.'|'.$datos->codigo2.'|1|5|'.$totalatenciones.'|0|'.$totalatenciones)."\n";  }$totalatenciones = 0; break; } ?>	
@endfor
@for($a = $i; $a < count($elementos); $a++)
<?php if($elementos[$a]->edad >= 20 && $elementos[$a]->edad <= 24) { $edad = 6; $totalatenciones += $elementos[$a]->totalatenciones; $i++;}
else { if($totalatenciones != 0) { $texto .= trim($fechatrama.'|'.$datos->codigo1.'|'.$datos->codigo2.'|1|6|'.$totalatenciones.'|0|'.$totalatenciones)."\n";  }$totalatenciones = 0; break; } ?>	
@endfor
@for($a = $i; $a < count($elementos); $a++)
<?php if($elementos[$a]->edad >= 25 && $elementos[$a]->edad <= 29) { $edad = 7; $totalatenciones += $elementos[$a]->totalatenciones; $i++;}
else { if($totalatenciones != 0) { $texto .= trim($fechatrama.'|'.$datos->codigo1.'|'.$datos->codigo2.'|1|7|'.$totalatenciones.'|0|'.$totalatenciones)."\n";  }$totalatenciones = 0; break; } ?>	
@endfor
@for($a = $i; $a < count($elementos); $a++)
<?php if($elementos[$a]->edad >= 30 && $elementos[$a]->edad <= 34) { $edad = 8; $totalatenciones += $elementos[$a]->totalatenciones; $i++;}
else { if($totalatenciones != 0) { $texto .= trim($fechatrama.'|'.$datos->codigo1.'|'.$datos->codigo2.'|1|8|'.$totalatenciones.'|0|'.$totalatenciones)."\n";  }$totalatenciones = 0; break; } ?>	
@endfor
@for($a = $i; $a < count($elementos); $a++)
<?php if($elementos[$a]->edad >= 35 && $elementos[$a]->edad <= 39) { $edad = 9; $totalatenciones += $elementos[$a]->totalatenciones; $i++;}
else { if($totalatenciones != 0) { $texto .= trim($fechatrama.'|'.$datos->codigo1.'|'.$datos->codigo2.'|1|9|'.$totalatenciones.'|0|'.$totalatenciones)."\n"; } $totalatenciones = 0; break; } ?>
@endfor
@for($a = $i; $a < count($elementos); $a++)
<?php if($elementos[$a]->edad >= 40 && $elementos[$a]->edad <= 44) { $edad = 10; $totalatenciones += $elementos[$a]->totalatenciones; $i++;}
else { if($totalatenciones != 0) { $texto .= trim($fechatrama.'|'.$datos->codigo1.'|'.$datos->codigo2.'|1|10|'.$totalatenciones.'|0|'.$totalatenciones)."\n";  }$totalatenciones = 0; break; } ?>	
@endfor
@for($a = $i; $a < count($elementos); $a++)
<?php if($elementos[$a]->edad >= 45 && $elementos[$a]->edad <= 49) { $edad = 11; $totalatenciones += $elementos[$a]->totalatenciones; $i++;}
else { if($totalatenciones != 0) { $texto .= trim($fechatrama.'|'.$datos->codigo1.'|'.$datos->codigo2.'|1|11|'.$totalatenciones.'|0|'.$totalatenciones)."\n";  }$totalatenciones = 0; break; } ?>	
@endfor
@for($a = $i; $a < count($elementos); $a++)
<?php if($elementos[$a]->edad >= 50 && $elementos[$a]->edad <= 54) { $edad = 12; $totalatenciones += $elementos[$a]->totalatenciones; $i++;}
else { if($totalatenciones != 0) { $texto .= trim($fechatrama.'|'.$datos->codigo1.'|'.$datos->codigo2.'|1|12|'.$totalatenciones.'|0|'.$totalatenciones)."\n";  }$totalatenciones = 0; break; } ?>	
@endfor
@for($a = $i; $a < count($elementos); $a++)
<?php if($elementos[$a]->edad >= 55 && $elementos[$a]->edad <= 59) { $edad = 13; $totalatenciones += $elementos[$a]->totalatenciones; $i++;}
else { if($totalatenciones != 0) { $texto .= trim($fechatrama.'|'.$datos->codigo1.'|'.$datos->codigo2.'|1|13|'.$totalatenciones.'|0|'.$totalatenciones)."\n";  }$totalatenciones = 0; break; } ?>	
@endfor
@for($a = $i; $a < count($elementos); $a++)
<?php if($elementos[$a]->edad >= 60 && $elementos[$a]->edad <= 64) { $edad = 14; $totalatenciones += $elementos[$a]->totalatenciones; $i++;}
else { if($totalatenciones != 0) { $texto .= trim($fechatrama.'|'.$datos->codigo1.'|'.$datos->codigo2.'|1|14|'.$totalatenciones.'|0|'.$totalatenciones)."\n"; } $totalatenciones = 0; break; } ?>
@endfor
@for($a = $i; $a < count($elementos); $a++)
<?php if($elementos[$a]->edad > 64) { $edad = 15; $totalatenciones += $elementos[$a]->totalatenciones; $i++;}
else { if($totalatenciones != 0) { $texto .= trim($fechatrama.'|'.$datos->codigo1.'|'.$datos->codigo2.'|1|15|'.$totalatenciones.'|0|'.$totalatenciones)."\n";  }$totalatenciones = 0; break; } ?>	
@endfor
@for($a = $i; $a < count($elementos); $a++)
<?php if($elementos[$a]->edad < 1) { $edad = 1; $totalatenciones += $elementos[$a]->totalatenciones; $i++;}
else { if($totalatenciones != 0) { $texto .= trim($fechatrama.'|'.$datos->codigo1.'|'.$datos->codigo2.'|2|1|'.$totalatenciones.'|0|'.$totalatenciones)."\n";  }$totalatenciones = 0; break; } ?>	
@endfor
@for($a = $i; $a < count($elementos); $a++)
<?php if($elementos[$a]->edad >= 1 && $elementos[$a]->edad <= 4) { $edad = 2; $totalatenciones += $elementos[$a]->totalatenciones; $i++;}
else { if($totalatenciones != 0) { $texto .= trim($fechatrama.'|'.$datos->codigo1.'|'.$datos->codigo2.'|2|2|'.$totalatenciones.'|0|'.$totalatenciones)."\n"; } $totalatenciones = 0; break; } ?>
@endfor
@for($a = $i; $a < count($elementos); $a++)
<?php if($elementos[$a]->edad >= 5 && $elementos[$a]->edad <= 9) { $edad = 3; $totalatenciones += $elementos[$a]->totalatenciones; $i++;}
else { if($totalatenciones != 0) { $texto .= trim($fechatrama.'|'.$datos->codigo1.'|'.$datos->codigo2.'|2|3|'.$totalatenciones.'|0|'.$totalatenciones)."\n"; } $totalatenciones = 0; break; } ?>
@endfor
@for($a = $i; $a < count($elementos); $a++)
<?php if($elementos[$a]->edad >= 10 && $elementos[$a]->edad <= 14) { $edad = 4; $totalatenciones += $elementos[$a]->totalatenciones; $i++;}
else { if($totalatenciones != 0) { $texto .= trim($fechatrama.'|'.$datos->codigo1.'|'.$datos->codigo2.'|2|4|'.$totalatenciones.'|0|'.$totalatenciones)."\n";  }$totalatenciones = 0; break; } ?>	
@endfor
@for($a = $i; $a < count($elementos); $a++)
<?php if($elementos[$a]->edad >= 15 && $elementos[$a]->edad <= 19) { $edad = 5; $totalatenciones += $elementos[$a]->totalatenciones; $i++;}
else { if($totalatenciones != 0) { $texto .= trim($fechatrama.'|'.$datos->codigo1.'|'.$datos->codigo2.'|2|5|'.$totalatenciones.'|0|'.$totalatenciones)."\n";  }$totalatenciones = 0; break; } ?>	
@endfor
@for($a = $i; $a < count($elementos); $a++)
<?php if($elementos[$a]->edad >= 20 && $elementos[$a]->edad <= 24) { $edad = 6; $totalatenciones += $elementos[$a]->totalatenciones; $i++;}
else { if($totalatenciones != 0) { $texto .= trim($fechatrama.'|'.$datos->codigo1.'|'.$datos->codigo2.'|2|6|'.$totalatenciones.'|0|'.$totalatenciones)."\n";  }$totalatenciones = 0; break; } ?>	
@endfor
@for($a = $i; $a < count($elementos); $a++)
<?php if($elementos[$a]->edad >= 25 && $elementos[$a]->edad <= 29) { $edad = 7; $totalatenciones += $elementos[$a]->totalatenciones; $i++;}
else { if($totalatenciones != 0) { $texto .= trim($fechatrama.'|'.$datos->codigo1.'|'.$datos->codigo2.'|2|7|'.$totalatenciones.'|0|'.$totalatenciones)."\n";  }$totalatenciones = 0; break; } ?>	
@endfor
@for($a = $i; $a < count($elementos); $a++)
<?php if($elementos[$a]->edad >= 30 && $elementos[$a]->edad <= 34) { $edad = 8; $totalatenciones += $elementos[$a]->totalatenciones; $i++;}
else { if($totalatenciones != 0) { $texto .= trim($fechatrama.'|'.$datos->codigo1.'|'.$datos->codigo2.'|2|8|'.$totalatenciones.'|0|'.$totalatenciones)."\n";  }$totalatenciones = 0; break; } ?>	
@endfor
@for($a = $i; $a < count($elementos); $a++)
<?php if($elementos[$a]->edad >= 35 && $elementos[$a]->edad <= 39) { $edad = 9; $totalatenciones += $elementos[$a]->totalatenciones; $i++;}
else { if($totalatenciones != 0) { $texto .= trim($fechatrama.'|'.$datos->codigo1.'|'.$datos->codigo2.'|2|9|'.$totalatenciones.'|0|'.$totalatenciones)."\n"; } $totalatenciones = 0; break; } ?>
@endfor
@for($a = $i; $a < count($elementos); $a++)
<?php if($elementos[$a]->edad >= 40 && $elementos[$a]->edad <= 44) { $edad = 10; $totalatenciones += $elementos[$a]->totalatenciones; $i++;}
else { if($totalatenciones != 0) { $texto .= trim($fechatrama.'|'.$datos->codigo1.'|'.$datos->codigo2.'|2|10|'.$totalatenciones.'|0|'.$totalatenciones)."\n";  }$totalatenciones = 0; break; } ?>	
@endfor
@for($a = $i; $a < count($elementos); $a++)
<?php if($elementos[$a]->edad >= 45 && $elementos[$a]->edad <= 49) { $edad = 11; $totalatenciones += $elementos[$a]->totalatenciones; $i++;}
else { if($totalatenciones != 0) { $texto .= trim($fechatrama.'|'.$datos->codigo1.'|'.$datos->codigo2.'|2|11|'.$totalatenciones.'|0|'.$totalatenciones)."\n";  }$totalatenciones = 0; break; } ?>	
@endfor
@for($a = $i; $a < count($elementos); $a++)
<?php if($elementos[$a]->edad >= 50 && $elementos[$a]->edad <= 54) { $edad = 12; $totalatenciones += $elementos[$a]->totalatenciones; $i++;}
else { if($totalatenciones != 0) { $texto .= trim($fechatrama.'|'.$datos->codigo1.'|'.$datos->codigo2.'|2|12|'.$totalatenciones.'|0|'.$totalatenciones)."\n";  }$totalatenciones = 0; break; } ?>	
@endfor
@for($a = $i; $a < count($elementos); $a++)
<?php if($elementos[$a]->edad >= 55 && $elementos[$a]->edad <= 59) { $edad = 13; $totalatenciones += $elementos[$a]->totalatenciones; $i++;}
else { if($totalatenciones != 0) { $texto .= trim($fechatrama.'|'.$datos->codigo1.'|'.$datos->codigo2.'|2|13|'.$totalatenciones.'|0|'.$totalatenciones)."\n";  }$totalatenciones = 0; break; } ?>	
@endfor
@for($a = $i; $a < count($elementos); $a++)
<?php if($elementos[$a]->edad >= 60 && $elementos[$a]->edad <= 64) { $edad = 14; $totalatenciones += $elementos[$a]->totalatenciones; $i++;}
else { if($totalatenciones != 0) { $texto .= trim($fechatrama.'|'.$datos->codigo1.'|'.$datos->codigo2.'|2|14|'.$totalatenciones.'|0|'.$totalatenciones)."\n"; } $totalatenciones = 0; break; } ?>
@endfor
@for($a = $i; $a < count($elementos); $a++)
<?php if($elementos[$a]->edad > 64) { $edad = 15; $totalatenciones += $elementos[$a]->totalatenciones; $i++;}
else { if($totalatenciones != 0) { $texto .= trim($fechatrama.'|'.$datos->codigo1.'|'.$datos->codigo2.'|2|15|'.$totalatenciones.'|0|'.$totalatenciones)."\n";  }$totalatenciones = 0; break; } ?>	
@endfor
<?php echo $texto; ?>
@endif

@if($trama == 'TAB2')
<?php $totalatenciones = 0; $i = 1; $texto = ''; ?>
@for($a = $i; $a < count($elementos); $a++)
<?php if($elementos[$a]->edad < 1) { $edad = 1; $totalatenciones += $elementos[$a]->totalatenciones; $i++;}
else { if($totalatenciones != 0) { $texto .= trim($fechatrama.'|'.$datos->codigo1.'|'.$datos->codigo2.'|1|1|'.$totalatenciones.'|0|'.$totalatenciones)."\n"; }$totalatenciones = 0; break; } ?>	
@endfor
@for($a = $i; $a < count($elementos); $a++)
<?php if($elementos[$a]->edad >= 1 && $elementos[$a]->edad <= 4) { $edad = 2; $totalatenciones += $elementos[$a]->totalatenciones; $i++;}
else { if($totalatenciones != 0) { $texto .= trim($fechatrama.'|'.$datos->codigo1.'|'.$datos->codigo2.'|1|2|'.$totalatenciones.'|0|'.$totalatenciones)."\n"; } $totalatenciones = 0; break; } ?>
@endfor
@for($a = $i; $a < count($elementos); $a++)
<?php if($elementos[$a]->edad >= 5 && $elementos[$a]->edad <= 9) { $edad = 3; $totalatenciones += $elementos[$a]->totalatenciones; $i++;}
else { if($totalatenciones != 0) { $texto .= trim($fechatrama.'|'.$datos->codigo1.'|'.$datos->codigo2.'|1|3|'.$totalatenciones.'|0|'.$totalatenciones)."\n"; } $totalatenciones = 0; break; } ?>
@endfor
@for($a = $i; $a < count($elementos); $a++)
<?php if($elementos[$a]->edad >= 10 && $elementos[$a]->edad <= 14) { $edad = 4; $totalatenciones += $elementos[$a]->totalatenciones; $i++;}
else { if($totalatenciones != 0) { $texto .= trim($fechatrama.'|'.$datos->codigo1.'|'.$datos->codigo2.'|1|4|'.$totalatenciones.'|0|'.$totalatenciones)."\n";  }$totalatenciones = 0; break; } ?>	
@endfor
@for($a = $i; $a < count($elementos); $a++)
<?php if($elementos[$a]->edad >= 15 && $elementos[$a]->edad <= 19) { $edad = 5; $totalatenciones += $elementos[$a]->totalatenciones; $i++;}
else { if($totalatenciones != 0) { $texto .= trim($fechatrama.'|'.$datos->codigo1.'|'.$datos->codigo2.'|1|5|'.$totalatenciones.'|0|'.$totalatenciones)."\n";  }$totalatenciones = 0; break; } ?>	
@endfor
@for($a = $i; $a < count($elementos); $a++)
<?php if($elementos[$a]->edad >= 20 && $elementos[$a]->edad <= 24) { $edad = 6; $totalatenciones += $elementos[$a]->totalatenciones; $i++;}
else { if($totalatenciones != 0) { $texto .= trim($fechatrama.'|'.$datos->codigo1.'|'.$datos->codigo2.'|1|6|'.$totalatenciones.'|0|'.$totalatenciones)."\n";  }$totalatenciones = 0; break; } ?>	
@endfor
@for($a = $i; $a < count($elementos); $a++)
<?php if($elementos[$a]->edad >= 25 && $elementos[$a]->edad <= 29) { $edad = 7; $totalatenciones += $elementos[$a]->totalatenciones; $i++;}
else { if($totalatenciones != 0) { $texto .= trim($fechatrama.'|'.$datos->codigo1.'|'.$datos->codigo2.'|1|7|'.$totalatenciones.'|0|'.$totalatenciones)."\n";  }$totalatenciones = 0; break; } ?>	
@endfor
@for($a = $i; $a < count($elementos); $a++)
<?php if($elementos[$a]->edad >= 30 && $elementos[$a]->edad <= 34) { $edad = 8; $totalatenciones += $elementos[$a]->totalatenciones; $i++;}
else { if($totalatenciones != 0) { $texto .= trim($fechatrama.'|'.$datos->codigo1.'|'.$datos->codigo2.'|1|8|'.$totalatenciones.'|0|'.$totalatenciones)."\n";  }$totalatenciones = 0; break; } ?>	
@endfor
@for($a = $i; $a < count($elementos); $a++)
<?php if($elementos[$a]->edad >= 35 && $elementos[$a]->edad <= 39) { $edad = 9; $totalatenciones += $elementos[$a]->totalatenciones; $i++;}
else { if($totalatenciones != 0) { $texto .= trim($fechatrama.'|'.$datos->codigo1.'|'.$datos->codigo2.'|1|9|'.$totalatenciones.'|0|'.$totalatenciones)."\n"; } $totalatenciones = 0; break; } ?>
@endfor
@for($a = $i; $a < count($elementos); $a++)
<?php if($elementos[$a]->edad >= 40 && $elementos[$a]->edad <= 44) { $edad = 10; $totalatenciones += $elementos[$a]->totalatenciones; $i++;}
else { if($totalatenciones != 0) { $texto .= trim($fechatrama.'|'.$datos->codigo1.'|'.$datos->codigo2.'|1|10|'.$totalatenciones.'|0|'.$totalatenciones)."\n";  }$totalatenciones = 0; break; } ?>	
@endfor
@for($a = $i; $a < count($elementos); $a++)
<?php if($elementos[$a]->edad >= 45 && $elementos[$a]->edad <= 49) { $edad = 11; $totalatenciones += $elementos[$a]->totalatenciones; $i++;}
else { if($totalatenciones != 0) { $texto .= trim($fechatrama.'|'.$datos->codigo1.'|'.$datos->codigo2.'|1|11|'.$totalatenciones.'|0|'.$totalatenciones)."\n";  }$totalatenciones = 0; break; } ?>	
@endfor
@for($a = $i; $a < count($elementos); $a++)
<?php if($elementos[$a]->edad >= 50 && $elementos[$a]->edad <= 54) { $edad = 12; $totalatenciones += $elementos[$a]->totalatenciones; $i++;}
else { if($totalatenciones != 0) { $texto .= trim($fechatrama.'|'.$datos->codigo1.'|'.$datos->codigo2.'|1|12|'.$totalatenciones.'|0|'.$totalatenciones)."\n";  }$totalatenciones = 0; break; } ?>	
@endfor
@for($a = $i; $a < count($elementos); $a++)
<?php if($elementos[$a]->edad >= 55 && $elementos[$a]->edad <= 59) { $edad = 13; $totalatenciones += $elementos[$a]->totalatenciones; $i++;}
else { if($totalatenciones != 0) { $texto .= trim($fechatrama.'|'.$datos->codigo1.'|'.$datos->codigo2.'|1|13|'.$totalatenciones.'|0|'.$totalatenciones)."\n";  }$totalatenciones = 0; break; } ?>	
@endfor
@for($a = $i; $a < count($elementos); $a++)
<?php if($elementos[$a]->edad >= 60 && $elementos[$a]->edad <= 64) { $edad = 14; $totalatenciones += $elementos[$a]->totalatenciones; $i++;}
else { if($totalatenciones != 0) { $texto .= trim($fechatrama.'|'.$datos->codigo1.'|'.$datos->codigo2.'|1|14|'.$totalatenciones.'|0|'.$totalatenciones)."\n"; } $totalatenciones = 0; break; } ?>
@endfor
@for($a = $i; $a < count($elementos); $a++)
<?php if($elementos[$a]->edad > 64) { $edad = 15; $totalatenciones += $elementos[$a]->totalatenciones; $i++;}
else { if($totalatenciones != 0) { $texto .= trim($fechatrama.'|'.$datos->codigo1.'|'.$datos->codigo2.'|1|15|'.$totalatenciones.'|0|'.$totalatenciones)."\n";  }$totalatenciones = 0; break; } ?>	
@endfor
@for($a = $i; $a < count($elementos); $a++)
<?php if($elementos[$a]->edad < 1) { $edad = 1; $totalatenciones += $elementos[$a]->totalatenciones; $i++;}
else { if($totalatenciones != 0) { $texto .= trim($fechatrama.'|'.$datos->codigo1.'|'.$datos->codigo2.'|2|1|'.$totalatenciones.'|0|'.$totalatenciones)."\n";  }$totalatenciones = 0; break; } ?>	
@endfor
@for($a = $i; $a < count($elementos); $a++)
<?php if($elementos[$a]->edad >= 1 && $elementos[$a]->edad <= 4) { $edad = 2; $totalatenciones += $elementos[$a]->totalatenciones; $i++;}
else { if($totalatenciones != 0) { $texto .= trim($fechatrama.'|'.$datos->codigo1.'|'.$datos->codigo2.'|2|2|'.$totalatenciones.'|0|'.$totalatenciones)."\n"; } $totalatenciones = 0; break; } ?>
@endfor
@for($a = $i; $a < count($elementos); $a++)
<?php if($elementos[$a]->edad >= 5 && $elementos[$a]->edad <= 9) { $edad = 3; $totalatenciones += $elementos[$a]->totalatenciones; $i++;}
else { if($totalatenciones != 0) { $texto .= trim($fechatrama.'|'.$datos->codigo1.'|'.$datos->codigo2.'|2|3|'.$totalatenciones.'|0|'.$totalatenciones)."\n"; } $totalatenciones = 0; break; } ?>
@endfor
@for($a = $i; $a < count($elementos); $a++)
<?php if($elementos[$a]->edad >= 10 && $elementos[$a]->edad <= 14) { $edad = 4; $totalatenciones += $elementos[$a]->totalatenciones; $i++;}
else { if($totalatenciones != 0) { $texto .= trim($fechatrama.'|'.$datos->codigo1.'|'.$datos->codigo2.'|2|4|'.$totalatenciones.'|0|'.$totalatenciones)."\n";  }$totalatenciones = 0; break; } ?>	
@endfor
@for($a = $i; $a < count($elementos); $a++)
<?php if($elementos[$a]->edad >= 15 && $elementos[$a]->edad <= 19) { $edad = 5; $totalatenciones += $elementos[$a]->totalatenciones; $i++;}
else { if($totalatenciones != 0) { $texto .= trim($fechatrama.'|'.$datos->codigo1.'|'.$datos->codigo2.'|2|5|'.$totalatenciones.'|0|'.$totalatenciones)."\n";  }$totalatenciones = 0; break; } ?>	
@endfor
@for($a = $i; $a < count($elementos); $a++)
<?php if($elementos[$a]->edad >= 20 && $elementos[$a]->edad <= 24) { $edad = 6; $totalatenciones += $elementos[$a]->totalatenciones; $i++;}
else { if($totalatenciones != 0) { $texto .= trim($fechatrama.'|'.$datos->codigo1.'|'.$datos->codigo2.'|2|6|'.$totalatenciones.'|0|'.$totalatenciones)."\n";  }$totalatenciones = 0; break; } ?>	
@endfor
@for($a = $i; $a < count($elementos); $a++)
<?php if($elementos[$a]->edad >= 25 && $elementos[$a]->edad <= 29) { $edad = 7; $totalatenciones += $elementos[$a]->totalatenciones; $i++;}
else { if($totalatenciones != 0) { $texto .= trim($fechatrama.'|'.$datos->codigo1.'|'.$datos->codigo2.'|2|7|'.$totalatenciones.'|0|'.$totalatenciones)."\n";  }$totalatenciones = 0; break; } ?>	
@endfor
@for($a = $i; $a < count($elementos); $a++)
<?php if($elementos[$a]->edad >= 30 && $elementos[$a]->edad <= 34) { $edad = 8; $totalatenciones += $elementos[$a]->totalatenciones; $i++;}
else { if($totalatenciones != 0) { $texto .= trim($fechatrama.'|'.$datos->codigo1.'|'.$datos->codigo2.'|2|8|'.$totalatenciones.'|0|'.$totalatenciones)."\n";  }$totalatenciones = 0; break; } ?>	
@endfor
@for($a = $i; $a < count($elementos); $a++)
<?php if($elementos[$a]->edad >= 35 && $elementos[$a]->edad <= 39) { $edad = 9; $totalatenciones += $elementos[$a]->totalatenciones; $i++;}
else { if($totalatenciones != 0) { $texto .= trim($fechatrama.'|'.$datos->codigo1.'|'.$datos->codigo2.'|2|9|'.$totalatenciones.'|0|'.$totalatenciones)."\n"; } $totalatenciones = 0; break; } ?>
@endfor
@for($a = $i; $a < count($elementos); $a++)
<?php if($elementos[$a]->edad >= 40 && $elementos[$a]->edad <= 44) { $edad = 10; $totalatenciones += $elementos[$a]->totalatenciones; $i++;}
else { if($totalatenciones != 0) { $texto .= trim($fechatrama.'|'.$datos->codigo1.'|'.$datos->codigo2.'|2|10|'.$totalatenciones.'|0|'.$totalatenciones)."\n";  }$totalatenciones = 0; break; } ?>	
@endfor
@for($a = $i; $a < count($elementos); $a++)
<?php if($elementos[$a]->edad >= 45 && $elementos[$a]->edad <= 49) { $edad = 11; $totalatenciones += $elementos[$a]->totalatenciones; $i++;}
else { if($totalatenciones != 0) { $texto .= trim($fechatrama.'|'.$datos->codigo1.'|'.$datos->codigo2.'|2|11|'.$totalatenciones.'|0|'.$totalatenciones)."\n";  }$totalatenciones = 0; break; } ?>	
@endfor
@for($a = $i; $a < count($elementos); $a++)
<?php if($elementos[$a]->edad >= 50 && $elementos[$a]->edad <= 54) { $edad = 12; $totalatenciones += $elementos[$a]->totalatenciones; $i++;}
else { if($totalatenciones != 0) { $texto .= trim($fechatrama.'|'.$datos->codigo1.'|'.$datos->codigo2.'|2|12|'.$totalatenciones.'|0|'.$totalatenciones)."\n";  }$totalatenciones = 0; break; } ?>	
@endfor
@for($a = $i; $a < count($elementos); $a++)
<?php if($elementos[$a]->edad >= 55 && $elementos[$a]->edad <= 59) { $edad = 13; $totalatenciones += $elementos[$a]->totalatenciones; $i++;}
else { if($totalatenciones != 0) { $texto .= trim($fechatrama.'|'.$datos->codigo1.'|'.$datos->codigo2.'|2|13|'.$totalatenciones.'|0|'.$totalatenciones)."\n";  }$totalatenciones = 0; break; } ?>	
@endfor
@for($a = $i; $a < count($elementos); $a++)
<?php if($elementos[$a]->edad >= 60 && $elementos[$a]->edad <= 64) { $edad = 14; $totalatenciones += $elementos[$a]->totalatenciones; $i++;}
else { if($totalatenciones != 0) { $texto .= trim($fechatrama.'|'.$datos->codigo1.'|'.$datos->codigo2.'|2|14|'.$totalatenciones.'|0|'.$totalatenciones)."\n"; } $totalatenciones = 0; break; } ?>
@endfor
@for($a = $i; $a < count($elementos); $a++)
<?php if($elementos[$a]->edad > 64) { $edad = 15; $totalatenciones += $elementos[$a]->totalatenciones; $i++;}
else { if($totalatenciones != 0) { $texto .= trim($fechatrama.'|'.$datos->codigo1.'|'.$datos->codigo2.'|2|15|'.$totalatenciones.'|0|'.$totalatenciones)."\n";  }$totalatenciones = 0; break; } ?>	
@endfor
<?php echo $texto; ?>
@endif

@if($trama == 'TAC1')
<?php $totalatenciones = 0; $i = 1; $texto = ''; ?>
@for($a = $i; $a < count($elementos); $a++)
<?php if($elementos[$a]->edad < 1) { $edad = 1; $totalatenciones += $elementos[$a]->totalatenciones; $i++;}
else { if($totalatenciones != 0) { $texto .= trim($fechatrama.'|'.$datos->codigo1.'|'.$datos->codigo2.'|1|1|'.$totalatenciones.'|'.$totalatenciones)."\n";  }$totalatenciones = 0; break; } ?>	
@endfor
@for($a = $i; $a < count($elementos); $a++)
<?php if($elementos[$a]->edad >= 1 && $elementos[$a]->edad <= 4) { $edad = 2; $totalatenciones += $elementos[$a]->totalatenciones; $i++;}
else { if($totalatenciones != 0) { $texto .= trim($fechatrama.'|'.$datos->codigo1.'|'.$datos->codigo2.'|1|2|'.$totalatenciones.'|'.$totalatenciones)."\n"; } $totalatenciones = 0; break; } ?>
@endfor
@for($a = $i; $a < count($elementos); $a++)
<?php if($elementos[$a]->edad >= 5 && $elementos[$a]->edad <= 9) { $edad = 3; $totalatenciones += $elementos[$a]->totalatenciones; $i++;}
else { if($totalatenciones != 0) { $texto .= trim($fechatrama.'|'.$datos->codigo1.'|'.$datos->codigo2.'|1|3|'.$totalatenciones.'|'.$totalatenciones)."\n"; } $totalatenciones = 0; break; } ?>
@endfor
@for($a = $i; $a < count($elementos); $a++)
<?php if($elementos[$a]->edad >= 10 && $elementos[$a]->edad <= 14) { $edad = 4; $totalatenciones += $elementos[$a]->totalatenciones; $i++;}
else { if($totalatenciones != 0) { $texto .= trim($fechatrama.'|'.$datos->codigo1.'|'.$datos->codigo2.'|1|4|'.$totalatenciones.'|'.$totalatenciones)."\n";  }$totalatenciones = 0; break; } ?>	
@endfor
@for($a = $i; $a < count($elementos); $a++)
<?php if($elementos[$a]->edad >= 15 && $elementos[$a]->edad <= 19) { $edad = 5; $totalatenciones += $elementos[$a]->totalatenciones; $i++;}
else { if($totalatenciones != 0) { $texto .= trim($fechatrama.'|'.$datos->codigo1.'|'.$datos->codigo2.'|1|5|'.$totalatenciones.'|'.$totalatenciones)."\n";  }$totalatenciones = 0; break; } ?>	
@endfor
@for($a = $i; $a < count($elementos); $a++)
<?php if($elementos[$a]->edad >= 20 && $elementos[$a]->edad <= 24) { $edad = 6; $totalatenciones += $elementos[$a]->totalatenciones; $i++;}
else { if($totalatenciones != 0) { $texto .= trim($fechatrama.'|'.$datos->codigo1.'|'.$datos->codigo2.'|1|6|'.$totalatenciones.'|'.$totalatenciones)."\n";  }$totalatenciones = 0; break; } ?>	
@endfor
@for($a = $i; $a < count($elementos); $a++)
<?php if($elementos[$a]->edad >= 25 && $elementos[$a]->edad <= 29) { $edad = 7; $totalatenciones += $elementos[$a]->totalatenciones; $i++;}
else { if($totalatenciones != 0) { $texto .= trim($fechatrama.'|'.$datos->codigo1.'|'.$datos->codigo2.'|1|7|'.$totalatenciones.'|'.$totalatenciones)."\n";  }$totalatenciones = 0; break; } ?>	
@endfor
@for($a = $i; $a < count($elementos); $a++)
<?php if($elementos[$a]->edad >= 30 && $elementos[$a]->edad <= 34) { $edad = 8; $totalatenciones += $elementos[$a]->totalatenciones; $i++;}
else { if($totalatenciones != 0) { $texto .= trim($fechatrama.'|'.$datos->codigo1.'|'.$datos->codigo2.'|1|8|'.$totalatenciones.'|'.$totalatenciones)."\n";  }$totalatenciones = 0; break; } ?>	
@endfor
@for($a = $i; $a < count($elementos); $a++)
<?php if($elementos[$a]->edad >= 35 && $elementos[$a]->edad <= 39) { $edad = 9; $totalatenciones += $elementos[$a]->totalatenciones; $i++;}
else { if($totalatenciones != 0) { $texto .= trim($fechatrama.'|'.$datos->codigo1.'|'.$datos->codigo2.'|1|9|'.$totalatenciones.'|'.$totalatenciones)."\n"; } $totalatenciones = 0; break; } ?>
@endfor
@for($a = $i; $a < count($elementos); $a++)
<?php if($elementos[$a]->edad >= 40 && $elementos[$a]->edad <= 44) { $edad = 10; $totalatenciones += $elementos[$a]->totalatenciones; $i++;}
else { if($totalatenciones != 0) { $texto .= trim($fechatrama.'|'.$datos->codigo1.'|'.$datos->codigo2.'|1|10|'.$totalatenciones.'|'.$totalatenciones)."\n";  }$totalatenciones = 0; break; } ?>	
@endfor
@for($a = $i; $a < count($elementos); $a++)
<?php if($elementos[$a]->edad >= 45 && $elementos[$a]->edad <= 49) { $edad = 11; $totalatenciones += $elementos[$a]->totalatenciones; $i++;}
else { if($totalatenciones != 0) { $texto .= trim($fechatrama.'|'.$datos->codigo1.'|'.$datos->codigo2.'|1|11|'.$totalatenciones.'|'.$totalatenciones)."\n";  }$totalatenciones = 0; break; } ?>	
@endfor
@for($a = $i; $a < count($elementos); $a++)
<?php if($elementos[$a]->edad >= 50 && $elementos[$a]->edad <= 54) { $edad = 12; $totalatenciones += $elementos[$a]->totalatenciones; $i++;}
else { if($totalatenciones != 0) { $texto .= trim($fechatrama.'|'.$datos->codigo1.'|'.$datos->codigo2.'|1|12|'.$totalatenciones.'|'.$totalatenciones)."\n";  }$totalatenciones = 0; break; } ?>	
@endfor
@for($a = $i; $a < count($elementos); $a++)
<?php if($elementos[$a]->edad >= 55 && $elementos[$a]->edad <= 59) { $edad = 13; $totalatenciones += $elementos[$a]->totalatenciones; $i++;}
else { if($totalatenciones != 0) { $texto .= trim($fechatrama.'|'.$datos->codigo1.'|'.$datos->codigo2.'|1|13|'.$totalatenciones.'|'.$totalatenciones)."\n";  }$totalatenciones = 0; break; } ?>	
@endfor
@for($a = $i; $a < count($elementos); $a++)
<?php if($elementos[$a]->edad >= 60 && $elementos[$a]->edad <= 64) { $edad = 14; $totalatenciones += $elementos[$a]->totalatenciones; $i++;}
else { if($totalatenciones != 0) { $texto .= trim($fechatrama.'|'.$datos->codigo1.'|'.$datos->codigo2.'|1|14|'.$totalatenciones.'|'.$totalatenciones)."\n"; } $totalatenciones = 0; break; } ?>
@endfor
@for($a = $i; $a < count($elementos); $a++)
<?php if($elementos[$a]->edad > 64) { $edad = 15; $totalatenciones += $elementos[$a]->totalatenciones; $i++;}
else { if($totalatenciones != 0) { $texto .= trim($fechatrama.'|'.$datos->codigo1.'|'.$datos->codigo2.'|1|15|'.$totalatenciones.'|'.$totalatenciones)."\n";  }$totalatenciones = 0; break; } ?>	
@endfor
@for($a = $i; $a < count($elementos); $a++)
<?php if($elementos[$a]->edad < 1) { $edad = 1; $totalatenciones += $elementos[$a]->totalatenciones; $i++;}
else { if($totalatenciones != 0) { $texto .= trim($fechatrama.'|'.$datos->codigo1.'|'.$datos->codigo2.'|2|1|'.$totalatenciones.'|'.$totalatenciones)."\n";  }$totalatenciones = 0; break; } ?>	
@endfor
@for($a = $i; $a < count($elementos); $a++)
<?php if($elementos[$a]->edad >= 1 && $elementos[$a]->edad <= 4) { $edad = 2; $totalatenciones += $elementos[$a]->totalatenciones; $i++;}
else { if($totalatenciones != 0) { $texto .= trim($fechatrama.'|'.$datos->codigo1.'|'.$datos->codigo2.'|2|2|'.$totalatenciones.'|'.$totalatenciones)."\n"; } $totalatenciones = 0; break; } ?>
@endfor
@for($a = $i; $a < count($elementos); $a++)
<?php if($elementos[$a]->edad >= 5 && $elementos[$a]->edad <= 9) { $edad = 3; $totalatenciones += $elementos[$a]->totalatenciones; $i++;}
else { if($totalatenciones != 0) { $texto .= trim($fechatrama.'|'.$datos->codigo1.'|'.$datos->codigo2.'|2|3|'.$totalatenciones.'|'.$totalatenciones)."\n"; } $totalatenciones = 0; break; } ?>
@endfor
@for($a = $i; $a < count($elementos); $a++)
<?php if($elementos[$a]->edad >= 10 && $elementos[$a]->edad <= 14) { $edad = 4; $totalatenciones += $elementos[$a]->totalatenciones; $i++;}
else { if($totalatenciones != 0) { $texto .= trim($fechatrama.'|'.$datos->codigo1.'|'.$datos->codigo2.'|2|4|'.$totalatenciones.'|'.$totalatenciones)."\n";  }$totalatenciones = 0; break; } ?>	
@endfor
@for($a = $i; $a < count($elementos); $a++)
<?php if($elementos[$a]->edad >= 15 && $elementos[$a]->edad <= 19) { $edad = 5; $totalatenciones += $elementos[$a]->totalatenciones; $i++;}
else { if($totalatenciones != 0) { $texto .= trim($fechatrama.'|'.$datos->codigo1.'|'.$datos->codigo2.'|2|5|'.$totalatenciones.'|'.$totalatenciones)."\n";  }$totalatenciones = 0; break; } ?>	
@endfor
@for($a = $i; $a < count($elementos); $a++)
<?php if($elementos[$a]->edad >= 20 && $elementos[$a]->edad <= 24) { $edad = 6; $totalatenciones += $elementos[$a]->totalatenciones; $i++;}
else { if($totalatenciones != 0) { $texto .= trim($fechatrama.'|'.$datos->codigo1.'|'.$datos->codigo2.'|2|6|'.$totalatenciones.'|'.$totalatenciones)."\n";  }$totalatenciones = 0; break; } ?>	
@endfor
@for($a = $i; $a < count($elementos); $a++)
<?php if($elementos[$a]->edad >= 25 && $elementos[$a]->edad <= 29) { $edad = 7; $totalatenciones += $elementos[$a]->totalatenciones; $i++;}
else { if($totalatenciones != 0) { $texto .= trim($fechatrama.'|'.$datos->codigo1.'|'.$datos->codigo2.'|2|7|'.$totalatenciones.'|'.$totalatenciones)."\n";  }$totalatenciones = 0; break; } ?>	
@endfor
@for($a = $i; $a < count($elementos); $a++)
<?php if($elementos[$a]->edad >= 30 && $elementos[$a]->edad <= 34) { $edad = 8; $totalatenciones += $elementos[$a]->totalatenciones; $i++;}
else { if($totalatenciones != 0) { $texto .= trim($fechatrama.'|'.$datos->codigo1.'|'.$datos->codigo2.'|2|8|'.$totalatenciones.'|'.$totalatenciones)."\n";  }$totalatenciones = 0; break; } ?>	
@endfor
@for($a = $i; $a < count($elementos); $a++)
<?php if($elementos[$a]->edad >= 35 && $elementos[$a]->edad <= 39) { $edad = 9; $totalatenciones += $elementos[$a]->totalatenciones; $i++;}
else { if($totalatenciones != 0) { $texto .= trim($fechatrama.'|'.$datos->codigo1.'|'.$datos->codigo2.'|2|9|'.$totalatenciones.'|'.$totalatenciones)."\n"; } $totalatenciones = 0; break; } ?>
@endfor
@for($a = $i; $a < count($elementos); $a++)
<?php if($elementos[$a]->edad >= 40 && $elementos[$a]->edad <= 44) { $edad = 10; $totalatenciones += $elementos[$a]->totalatenciones; $i++;}
else { if($totalatenciones != 0) { $texto .= trim($fechatrama.'|'.$datos->codigo1.'|'.$datos->codigo2.'|2|10|'.$totalatenciones.'|'.$totalatenciones)."\n";  }$totalatenciones = 0; break; } ?>	
@endfor
@for($a = $i; $a < count($elementos); $a++)
<?php if($elementos[$a]->edad >= 45 && $elementos[$a]->edad <= 49) { $edad = 11; $totalatenciones += $elementos[$a]->totalatenciones; $i++;}
else { if($totalatenciones != 0) { $texto .= trim($fechatrama.'|'.$datos->codigo1.'|'.$datos->codigo2.'|2|11|'.$totalatenciones.'|'.$totalatenciones)."\n";  }$totalatenciones = 0; break; } ?>	
@endfor
@for($a = $i; $a < count($elementos); $a++)
<?php if($elementos[$a]->edad >= 50 && $elementos[$a]->edad <= 54) { $edad = 12; $totalatenciones += $elementos[$a]->totalatenciones; $i++;}
else { if($totalatenciones != 0) { $texto .= trim($fechatrama.'|'.$datos->codigo1.'|'.$datos->codigo2.'|2|12|'.$totalatenciones.'|'.$totalatenciones)."\n";  }$totalatenciones = 0; break; } ?>	
@endfor
@for($a = $i; $a < count($elementos); $a++)
<?php if($elementos[$a]->edad >= 55 && $elementos[$a]->edad <= 59) { $edad = 13; $totalatenciones += $elementos[$a]->totalatenciones; $i++;}
else { if($totalatenciones != 0) { $texto .= trim($fechatrama.'|'.$datos->codigo1.'|'.$datos->codigo2.'|2|13|'.$totalatenciones.'|'.$totalatenciones)."\n";  }$totalatenciones = 0; break; } ?>	
@endfor
@for($a = $i; $a < count($elementos); $a++)
<?php if($elementos[$a]->edad >= 60 && $elementos[$a]->edad <= 64) { $edad = 14; $totalatenciones += $elementos[$a]->totalatenciones; $i++;}
else { if($totalatenciones != 0) { $texto .= trim($fechatrama.'|'.$datos->codigo1.'|'.$datos->codigo2.'|2|14|'.$totalatenciones.'|'.$totalatenciones)."\n"; } $totalatenciones = 0; break; } ?>
@endfor
@for($a = $i; $a < count($elementos); $a++)
<?php if($elementos[$a]->edad > 64) { $edad = 15; $totalatenciones += $elementos[$a]->totalatenciones; $i++;}
else { if($totalatenciones != 0) { $texto .= trim($fechatrama.'|'.$datos->codigo1.'|'.$datos->codigo2.'|2|15|'.$totalatenciones.'|'.$totalatenciones)."\n";  }$totalatenciones = 0; break; } ?>	
@endfor
<?php echo $texto; ?>
@endif

@if($trama == 'TAC2')
@endif

@if($trama == 'TAD1')
{{ $fechatrama }}|{{ $datos->codigo1 }}|{{ $datos->codigo2 }}|NE_0001|NE_0001|NE_0001|NE_0001|NE_0001|NE_0001|NE_0001|NE_0001
@endif

@if($trama == 'TAD2')
{{ $fechatrama }}|{{ $datos->codigo1 }}|{{ $datos->codigo2 }}|NE_0001|NE_0001|NE_0001|NE_0001
@endif

@if($trama == 'TAE0')
{{ $fechatrama }}|{{ $datos->codigo1 }}|{{ $datos->codigo2 }}|NE_0001|NE_0001|NE_0001|NE_0001|NE_0001|NE_0001
@endif

@if($trama == 'TAF0')
{{ $fechatrama }}|{{ $datos->codigo1 }}|{{ $datos->codigo2 }}|NE_0001|NE_0001|NE_0001|NE_0001
@endif

@if($trama == 'TAG0')
{{ $fechatrama }}|{{ $datos->codigo1 }}|{{ $datos->codigo2 }}|NE_0001|NE_0001|NE_0001
@endif

@if($trama == 'TAH0')
@endif

@if($trama == 'TAI0')
{{ $fechatrama }}|{{ $datos->codigo1 }}|{{ $datos->codigo2 }}|NE_0001|NE_0001|NE_0001|NE_0001|NE_0001|NE_0001|NE_0001|NE_0001|NE_0001|NE_0001|NE_0001|NE_0001|NE_0001|NE_0001|NE_0001|NE_0001
@endif

@if($trama == 'TAJ0')
{{ $fechatrama }}|{{ $datos->codigo1 }}|{{ $datos->codigo2 }}|00|9|0|0|75|0|0|75|0|0|0
{{ $fechatrama }}|{{ $datos->codigo1 }}|{{ $datos->codigo2 }}|01|1|20|10|40|0|0|0|0|0|0
{{ $fechatrama }}|{{ $datos->codigo1 }}|{{ $datos->codigo2 }}|02|1|0|0|0|12|0|0|0|0|0
{{ $fechatrama }}|{{ $datos->codigo1 }}|{{ $datos->codigo2 }}|03|0|0|0|0|0|0|0|0|0|0
{{ $fechatrama }}|{{ $datos->codigo1 }}|{{ $datos->codigo2 }}|04|1|0|0|0|0|0|0|0|150|0
{{ $fechatrama }}|{{ $datos->codigo1 }}|{{ $datos->codigo2 }}|05|0|0|0|0|0|0|0|0|0|0
{{ $fechatrama }}|{{ $datos->codigo1 }}|{{ $datos->codigo2 }}|06|8|0|0|75|0|0|75|0|0|0
{{ $fechatrama }}|{{ $datos->codigo1 }}|{{ $datos->codigo2 }}|07|0|0|0|0|0|0|0|0|0|0
{{ $fechatrama }}|{{ $datos->codigo1 }}|{{ $datos->codigo2 }}|08|0|0|0|0|0|0|0|0|0|0
{{ $fechatrama }}|{{ $datos->codigo1 }}|{{ $datos->codigo2 }}|09|0|0|0|0|0|0|0|0|0|0
{{ $fechatrama }}|{{ $datos->codigo1 }}|{{ $datos->codigo2 }}|10|0|0|0|0|0|0|0|0|0|0
@endif