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
				$movimiento_id = $value->movimiento->id;
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
					<td>{{ $doctor_nombre }}</td>
					<td>{{ $doctor->especialidad->nombre }}</td>
					@if($tipo == "P")
						<td align="center">{{ number_format($sumapago,2,'.','') }}</td>
					@else
						<td align="center">
							<input type="text" class="input-xs form-control" id="monto{{ $value->movimiento->id }}" disabled>
						</td>
					@endif
					<td style="color:black;font-weight: bold;">
						<center>
							@if($tipo == "P")
								<input id="{{ $value->movimiento->id }}" doctor="{{ $doctor->id }}" pago="{{ number_format($sumapago,2,'.','') }}" class="pagar" checked="checked" name="pagar" type="checkbox"><a href="#" id="{{ $value->movimiento->id }}" onclick="checkear({{ $value->movimiento->id }});"> Pagar</a>
							@else
							<a href="#" id="{{ $value->movimiento->id }}" onclick="checkear({{ $value->movimiento->id }});">
								<input id="{{ $value->movimiento->id }}" doctor="{{ $doctor->id }}" pago="{{ number_format($sumapago,2,'.','') }}" class="pagar" name="pagar" type="checkbox" onclick="checkear({{ $value->movimiento->id }});">
						 		Pagar
							</a>
							@endif
						</center>
					</td>
				</tr>
			@endif
		@endforeach
	</tbody>
</table>
<div style="text-align:right;">
{!! Form::button('<i class="fa fa-check fa-lg"></i> Guardar pagos', array('class' => 'btn btn-success btn-sm', 'id' => 'btnGuardar', 'onclick' => 'guardarPagoDoctores();', 'style' => 'margin-top:20px;')) !!}
</div>
<script type="text/javascript">
$(document).ready(function() {
	configurarAnchoModal('1000');
}); 

function checkear(id){
	if( $("input[id=" + id + "]").prop('checked') == true ) {
		$("input[id=" + id + "]").prop('checked',false);
		$("input[id=monto" + id + "]").prop('disabled',true);
	}else{
		$("input[id=" + id + "]").prop('checked',true);
		$("input[id=monto" + id + "]").prop('disabled',false);
	}
}


function guardarPagoDoctores(){
	var data = [];
	var tipopaciente = $('#tipopaciente').val();
	if(tipopaciente == "P"){
		$('.pagar:checked').each(
			function() {
				data.push(
					{ 
						"id": $(this).attr('id') ,
						"doctor": $(this).attr('doctor') ,
						"pago": $(this).attr('pago') ,
					}
				);
			}
		);
	}else if(tipopaciente == "C"){
		$('.pagar:checked').each(
			function() {
				data.push(
					{ 
						"id": $(this).attr('id') ,
						"doctor": $(this).attr('doctor') ,
						"pago": $("input[id=monto" + $(this).attr('id') + "]").val() ,
					}
				);
			}
		);
	}
	var detalle = {"data": data};
	var json = JSON.stringify(detalle);
	console.log(json);

	$.ajax({
		type: "POST",
		url: "caja/guardarPagoDoctores",
		data: {
			"pagados" : json, 
			"_token": "{{ csrf_token() }}",
			},
		success: function(a) {
			alert('GUARDADO CORRECTAMENTE...');
			buscar('Caja');
			listapagosdoctores();
		},
	error: function() {
		alert('OCURRIÓ UN ERROR, VUELVA A INTENTAR...');
	}
	});
}

</script>
@endif