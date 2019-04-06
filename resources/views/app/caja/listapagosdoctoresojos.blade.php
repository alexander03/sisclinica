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
		@foreach ($doctores as $doctor)

			<?php
				$cantconsultaplan = 0;
				$montomedicoconsulta = 0;
				$montomedicoconsultaplan = 0;
				$montomedicoexamenes = 0;
				$pagomedicoconsulta = 0;
				$pagomedicoexamenes = 0;
			?>

			@foreach ($lista as $key => $value)

				@if($doctor->id == $value->medico_id)
					@if($value->plan_id == 6)
						@if($value->tiposervicio_id == 1)
						<?php
							$montomedicoconsulta += $value->pagohospital*$value->cantidad;
						?>
						@elseif($value->tiposervicio_id == 21)
						<?php
							$montomedicoexamenes += $value->pagohospital*$value->cantidad;
						?>
						@endif
					@else
						@if($value->tiposervicio_id == 1)
							@if($value->precio != 0)
								<?php
									$cantconsultaplan ++;
									$montomedicoconsultaplan += $value->pagohospital*$value->cantidad;
								?>
							@endif
						@endif
					@endif
				@endif
				
			@endforeach

			@if($montomedicoconsulta != 0 || $montomedicoexamenes != 0)
			<?php
			/*	if($doctor->id == 3){
					$pagomedicoexamenes = $montomedicoexamenes / 1.18 * 0.25; 
					$pagomedicoconsulta = $montomedicoconsulta * 0.58 + $cantconsultaplan * 30;
				}else{
					$pagomedicoexamenes = $montomedicoexamenes / 1.18 * 0.25; 
					$pagomedicoconsulta = $montomedicoconsulta / 1.18 * 0.25 + $cantconsultaplan * 30;
				}*/

				$consultas = 0;
				$examenes = 0;
				$montoconvenio = 0;

				if($doctor->consultas != null){
					$consultas = $doctor->consultas;
				}
				if($doctor->examenes != null){
					$examenes = $doctor->examenes;
				}
				if($doctor->montoconvenio != null){
					$montoconvenio = $doctor->montoconvenio;
				}

				if($doctor->consultasigv == 1){
					$particular = $montomedicoconsulta / 1.18 * ( $doctor->consultas / 100 );
				}else{
					$particular = $montomedicoconsulta * $doctor->consultas / 100 ;
				}
				$convenio = $cantconsultaplan * $montoconvenio;
				$pagomedicoconsulta = $particular + $convenio;

				if($doctor->examenesigv == 1){
					$pagomedicoexamenes = $montomedicoexamenes / 1.18 * $doctor->examenes / 100; 
				}else{
					$pagomedicoexamenes = $montomedicoexamenes * $doctor->examenes / 100;
				}
			?>
			@if($pagomedicoexamenes + $pagomedicoconsulta != 0)
			<tr>
				<td>{{ $doctor->apellidopaterno . " " . $doctor->apellidomaterno . " " . $doctor->nombres}}</td>
				<td width="6%" align ="right">{{ number_format( round($montomedicoconsulta + $montomedicoconsultaplan,1) ,2,'.','') }}</td>
				<td width="6%" align ="right">{{ number_format( round($montomedicoexamenes,1) ,2,'.','') }}</td>
				<td width="6%" align ="right">{{ number_format( round($montomedicoexamenes + $montomedicoconsulta + $montomedicoconsultaplan,1) ,2,'.','') }}</td>
				<td width="6%" align ="right">{{ number_format( round($particular,1) ,2,'.','') }}</td>
				<td width="6%"align ="right">{{ number_format( round($convenio,1) ,2,'.','') }}</td>
				<td width="6%" align ="right">{{ number_format( round($pagomedicoexamenes,1) ,2,'.','') }}</td>
				<td width="6%" align ="right">{{ number_format( round($pagomedicoexamenes + $pagomedicoconsulta,1) ,2,'.','') }}</td>
				<td>
					<button class="btn btn-danger btn-xs" id="btnDetalle" data-doctor_id="{{$doctor->id}}" data-fechainicial="{{$fechainicial}}" data-fechafinal="{{$fechafinal}}" onclick="mostrarDetallePago(this);" type="button">
						<i class="fa fa-list fa-lg"></i> Detalle
					</button>
					<button class="btn btn-success btn-xs" id="btnPagar" data-doctor_id="{{$doctor->id}}" data-pagoconsultasp="{{ $particular }}" data-pagoconsultasc="{{ $convenio }}" data-pagoexamenes="{{ $pagomedicoexamenes }}" data-fechainicial="{{$fechainicial}}" data-fechafinal="{{$fechafinal}}" onclick="guardarPagoDoctoresOjos(this);" type="button">
						<i class="fa fa-check fa-lg"></i> Pagar
					</button>
				</td>
			</tr>	
			@endif
			@endif
		@endforeach

	</tbody>
</table>
<div style="text-align: right; display: none;">
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


function guardarPagoDoctoresOjos(elemento){

	$(elemento).prop('disabled',true);

	var doctor_id = $(elemento).data('doctor_id');
	var fechainicial = $(elemento).data('fechainicial');
	var fechafinal = $(elemento).data('fechafinal');
	var pagoconsultasp = $(elemento).data('pagoconsultasp');
	var pagoconsultasc = $(elemento).data('pagoconsultasc');
	var pagoexamenes = $(elemento).data('pagoexamenes');

	$.ajax({
		type: "POST",
		url: "caja/guardarPagoDoctoresOjos",
		data: {
			"doctor_id" : doctor_id, 
			"fechainicial" : fechainicial, 
			"fechafinal" : fechafinal, 
			"pagoconsultasp" : pagoconsultasp,
			"pagoconsultasc" : pagoconsultasc,
			"pagoexamenes" : pagoexamenes,
			"_token": "{{ csrf_token() }}",
			},
		success: function(a) {
			alert('GUARDADO CORRECTAMENTE...');
			buscar('Caja');
			listapagosdoctoresojos();
		},
	error: function() {
		alert('OCURRIÓ UN ERROR, VUELVA A INTENTAR...');
	}
	});
}

function mostrarDetallePago(elemento){
	
	var doctor_id = $(elemento).data('doctor_id');
	var fechainicial = $(elemento).data('fechainicial');
	var fechafinal = $(elemento).data('fechafinal');

	window.open("caja/mostrarDetallePago?fechainicial=" + fechainicial+ "&fechafinal="+ fechafinal + "&doctor_id="+ doctor_id ,"_blank");

}

</script>
@endif