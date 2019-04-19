<?php $texto = ''; ?>
@if($trama == 'TAA0')
<?php $texto .= $fechatrama ."|". $datos->codigo1 ."|". $datos->codigo2 ."|". $datos->consultoriosfi ."|". $datos->consultoriosfu ."|". $datos->camas ."|". $datos->medicost ."|". $datos->medicoss ."|". $datos->medicosr ."|". $datos->enfermeras ."|". $datos->odontologos ."|". $datos->psicologos ."|". $datos->nutricionistas ."|". $datos->tecnologos ."|". $datos->obstetrices ."|". $datos->farmaceuticos ."|". $datos->auxiliares ."|". $datos->otros ."|". $datos->ambulancias; ?>
@endif

@if($trama == 'TAB1')
@foreach($elementos as $elemento)
<?php $texto .= trim($fechatrama.'|'.$datos->codigo1.'|'.$datos->codigo2.'|'.$elemento->sexo.'|'.$elemento->edad.'|'.$elemento->totalatenciones.'|0|'.$elemento->totalatenciones)."\r\n"; ?>	
@endforeach
@endif

@if($trama == 'TAB2')
@foreach($elementos as $elemento)
<?php $texto .= trim($fechatrama.'|'.$datos->codigo1.'|'.$datos->codigo2.'|'.$elemento->sexo.'|'.$elemento->edad.'|'.$elemento->codigo.'|'.$elemento->totalatenciones)."\r\n"; ?>	
@endforeach
@endif

@if($trama == 'TAC1')
<?php $texto = ''; ?>
@foreach($elementos as $elemento)
<?php $texto .= trim($fechatrama.'|'.$datos->codigo1.'|'.$datos->codigo2.'|'.$elemento->sexo.'|'.$elemento->edad.'|'.$elemento->totalatenciones.'|'.$elemento->totalatenciones)."\r\n"; ?>	
@endforeach
@endif

@if($trama == 'TAC2')
<?php $texto = ''; ?>
@foreach($elementos as $elemento)
<?php $texto .= trim($fechatrama.'|'.$datos->codigo1.'|'.$datos->codigo2.'|'.$elemento->sexo.'|'.$elemento->edad.'|'.$elemento->codigo.'|'.$elemento->totalatenciones)."\r\n"; ?>	
@endforeach
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
<?php echo utf8_decode($texto); ?>