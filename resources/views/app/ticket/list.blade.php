<?php

use App\Detallemovcaja;

?>
@if(count($lista) == 0)
<h3 class="text-warning">No se encontraron resultados.</h3>
@else
{!! $paginacion or '' !!}
<div class="table-responsive">
	<table id="example1" class="table table-bordered table-striped table-condensed table-hover">

		<thead>
			<tr>
				@foreach($cabecera as $key => $value)
					<th class="text-center" @if((int)$value['numero'] > 1) colspan="{{ $value['numero'] }}" @endif>{!! $value['valor'] !!}</th>
				@endforeach
			</tr>
		</thead>
		<tbody>
			<?php
			$contador = $inicio + 1;
			?>
			@foreach ($lista as $key => $value)
			<tr class="{{ $value->id }}">
				<td>{{ $contador }}</td>
	            <td>{{ date('d/m/Y',strtotime($value->fecha)) }}</td>
	            <td>{{ $value->numero }}</td>
	            <td>{{ $value->paciente }}</td>
	            @if($sucursal_id == 1)
					@if($value->clasificacionconsulta=='C')
		            <td>CONSULTA</td>
		            @elseif($value->clasificacionconsulta=='E')
		            <td>EMERGENCIA</td>
					@elseif($value->clasificacionconsulta=='L')
		            <td>LECTURA</td>
		            @elseif($value->clasificacionconsulta=='P')
		            <td>PROCEDIMIENTO</td>
		            @elseif($value->clasificacionconsulta=='X')
		            <td>EXAMENES</td>
		            @endif
		            <td>{{ $value->turno=='M'?'MAÑANA':'TARDE' }}</td>
	            @endif

	            <td align="center">{{ number_format($value->total,2,'.','') }}</td>
	            
	            
            	<?php
				$estado ="";
				if($value->situacion=='P' || $value->situacion=='B'){
					$estado ="PENDIENTE";
				}else if($value->situacion=='C'){
					$estado ="COBRADO";
				}else if($value->situacion=='D'){
					$estado ="DEBE";
				}else if($value->situacion=='U'){
					$estado ="ANULADO";
				}else if($value->situacion=='R'){
					$estado ="REPROGRAMADO";
				}
				if($value->situacion2 == 'A' || $value->situacion2 == 'B' || $value->situacion2 == 'F'){
					$estado .= " - ATENDIENDO";
				}else if($value->situacion2 == 'C' || $value->situacion2 == 'N' || $value->situacion2 == ''){
					$estado .= " - EN ESPERA";
				}else if($value->situacion2 == 'L'){
					$estado .= " - ATENDIDO";
				}	
				$responsable = explode(" ", $value->responsable);	
				$Detalle = Detallemovcaja::where('movimiento_id', $value->id)->orderBy('id', 'ASC')->first();		
				?>

				<td style="font-size:12px">{{ $estado }}</td>


	  			<td>{{ $responsable[0] }}</td>
	  			<td>{{ $Detalle->persona->apellidopaterno . ' ' . explode(" ", $Detalle->persona->nombres)[0] }}</td>
	            @if($value->situacion=='C')
	            	@if($value->total>0)
	                	<td align="center" style="display:none;">{!! Form::button('<div class="glyphicon glyphicon-print"></div>', array('onclick' => 'window.open(\'ticket/pdfComprobante?ticket_id='.$value->id.'\',\'_blank\')', 'class' => 'btn btn-xs btn-info', 'title'=>'Comprobante A4')) !!}</td>
	                	<td align="center" style="display:none;">{!! Form::button('<div class="glyphicon glyphicon-print"></div>', array('onclick' => 'window.open(\'ticket/pdfComprobante3?ticket_id='.$value->id.'\',\'_blank\')', 'class' => 'btn btn-xs btn-info', 'title'=>'Comprobante Ticketera')) !!}</td>
	                @else
	                	<td align="center">{!! Form::button('<div class="glyphicon glyphicon-print"></div>', array('onclick' => 'window.open(\'ticket/pdfPrefactura?ticket_id='.$value->id.'\',\'_blank\')', 'class' => 'btn btn-xs btn-info', 'title' => 'Prefactura')) !!}</td>
	                	<td align="center"> - </td>
	                @endif
	            @else
	                <td align="center"> - </td>
	                <td align="center"> - </td>
	            @endif
	           
				@if(($user->usertype_id==1 || $user->usertype_id==5) && $value->situacion=='P' && $value->total!==0)
					<td align="center">{!! Form::button('<div class="glyphicon glyphicon-pencil"></div>', array('onclick' => 'modal (\''.URL::route($ruta["edit"], array($value->id, 'listar'=>'SI')).'\', \''.$titulo_modificar.'\', this);', 'class' => 'btn btn-xs btn-warning', 'title' => 'Editar')) !!}</td>
				@else
					<td align="center"> - </td>
				@endif
				@if(($user->usertype_id==1 || $user->usertype_id==7 || $user->usertype_id==5 || $user->usertype_id==2) && $value->total!==0 && $value->situacion=='P')
					<td align="center">{!! Form::button('<div class="glyphicon glyphicon-minus"></div>', array('onclick' => 'modal (\''.URL::route($ruta["anular"], array($value->id, 'listar'=>'SI')).'\', \'Anular\', this);', 'class' => 'btn btn-xs btn-danger', 'title' => 'Anular')) !!}</td>
				@else
					<td align="center"> - </td>
				@endif
			</tr>
			<?php
			$contador = $contador + 1;
			?>
			@endforeach
		</tbody>
	</table>
</div>
@endif