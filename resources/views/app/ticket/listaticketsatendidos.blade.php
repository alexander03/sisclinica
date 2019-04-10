<?php
use App\Detallemovcaja;
use App\Person;
?>
@if(count($lista) == 0)
<h3 class="text-warning">No se encontraron resultados.</h3>
@else
<table id="example1" class="table table-bordered table-striped table-condensed table-hover">
	<thead>
		<tr>
			@foreach($cabecera as $key => $value)
				<th @if((int)$value['numero'] > 1) colspan="{{ $value['numero'] }}" @endif>{!! $value['valor'] !!}</th>
			@endforeach
		</tr>
	</thead>
	<tbody>
		@foreach ($lista as $key => $value)
			<?php
				//doctor
				$movimiento_id = $value->id;
				$detalle = Detallemovcaja::where('movimiento_id','=', $movimiento_id)->first();
				$doctor = Person::find($detalle->persona_id);
				$doctor_nombre = $doctor->apellidopaterno . " " .$doctor->apellidomaterno .' ' . $doctor->nombres;
				//pagodoctor
				$sumapago = Detallemovcaja::where('movimiento_id','=', $movimiento_id)->sum('pagodoctor');
			?>
		@if($sumapago != 0)
			<tr>
				<td>{{ date('d/m/Y',strtotime($value->fecha)) }}</td>
				<td>{{ $value->numero }}</td>
				<td>{{ $value->paciente }}</td>
				<td align="center">{{ number_format($value->total,2,'.','') }}</td>
				<td style="color:blue;font-weight: bold;">
				@if ($value->situacion == 'P')
				<font color="orange">PENDIENTE</font>
				@elseif($value->situacion == 'D')
				<font color="red">DEBE</font>
				@else
				<font color="green">COBRADO</font>
				@endif
				</td>
				<td style="color:black;font-weight: bold;">
					<center>
						<input id="{{ $value->id }}" class="atendido" checked="checked" name="atendido" type="checkbox"><a href="#" id="{{ $value->id }}" onclick="checkear({{ $value->id }});"> Atendido</a>
					</center>
				</td>
			</tr>
		@endif
		@endforeach
	</tbody>
</table>
<div style="text-align:right;">
{!! Form::button('<i class="fa fa-check fa-lg"></i> Guardar atendidos', array('class' => 'btn btn-success btn-sm', 'id' => 'btnGuardar', 'onclick' => 'guardarAtendidos();', 'style' => 'margin-top:20px;')) !!}
</div>
<script type="text/javascript">
$(document).ready(function() {
	configurarAnchoModal('900');
}); 

function checkear(id){
	if( $("input[id=" + id + "]").prop('checked') == true ) {
		$("input[id=" + id + "]").prop('checked',false);
	}else{
		$("input[id=" + id + "]").prop('checked',true);
	}
}

function guardarAtendidos(){
	$('#btnGuardar').prop('disabled',true);
	var data = [];
	$('.atendido:checked').each(
		function() {
			data.push(
				{ "id": $(this).attr('id') }
			);
		}
	);
	var detalle = {"data": data};
	var json = JSON.stringify(detalle);
	console.log(json);


	$.ajax({
		type: "POST",
		url: "ticket/guardarAtendidos",
		data: {
			"atendidos" : json, 
			"_token": "{{ csrf_token() }}",
			},
		success: function(a) {
			alert('GUARDADO CORRECTAMENTE...');
			$('#btnGuardar').prop('disabled',false);
			listaticketsatendidos();
		},
	error: function() {
		alert('OCURRIÓ UN ERROR, VUELVA A INTENTAR...');
	}
	});
}
</script>
@endif