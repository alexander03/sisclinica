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
				$montoconsultasp = 0;
				$montoconsultasc = 0;
				$montoexamenes = 0; 
				$pagoconsultasp = 0;
				$pagoconsultasc = 0;
				$pagoexamenes = 0; 

				//formulas de pago
				if($doctor->consultas != null){
					$consultas = $doctor->consultas;
				}else{
					$consultas = 0;
				}
				if($doctor->examenes != null){
					$examenes = $doctor->examenes;
				}else{
					$examenes = 0;
				}
				if($doctor->montoconvenio != null){
					$montoconvenio = $doctor->montoconvenio;
				}else{
					$montoconvenio = 0;
				}
				//fin formulas de pago

				foreach ($lista as $value){

					if($doctor->id == $value->medico_id){
			
						$montoservicio = $value->precio * $value->cantidad;

						if($value->plan_id == 6){

							if($value->tiposervicio_id == 1){

								$montoconsultasp += $montoservicio;

								if($doctor->consultasigv == 1){
									$pagodoctor = $montoservicio / 1.18 * ( $consultas / 100 );
								}else{
									$pagodoctor = $montoservicio * $consultas / 100 ;
								}

								$pagoconsultasp += $pagodoctor;

							}else if($value->tiposervicio_id == 21){
								
								$montoexamenes += $montoservicio;

								if($doctor->examenesigv == 1){
									$pagodoctor = $montoservicio / 1.18 * $examenes / 100; 
								}else{
									$pagodoctor = $montoservicio * $examenes / 100;
								}

								$pagoexamenes += $pagodoctor;

							}
						}else{

							if($value->tiposervicio_id == 1){

								//if($value->precio != 0){
									$montoconsultasc += $montoservicio;
			
									$pagodoctor = $montoconvenio;
			
									$pagoconsultasc += $pagodoctor;
			
								//}
							}
						}

					}
					
				}

			?>

			@if($pagoconsultasc != 0 || $pagoconsultasp != 0|| $pagoexamenes != 0)
			<tr>
				<td>{{ $doctor->apellidopaterno . " " . $doctor->apellidomaterno . " " . $doctor->nombres}}</td>
				<td width="6%" align ="right">{{ number_format( round($montoconsultasc + $montoconsultasp,1) ,2,'.','') }}</td>
				<td width="6%" align ="right">{{ number_format( round($montoexamenes,1) ,2,'.','') }}</td>
				<td width="6%" align ="right">{{ number_format( round($montoconsultasc + $montoconsultasp + $montoexamenes,1) ,2,'.','') }}</td>
				<td width="6%" align ="right">{{ number_format( round($pagoconsultasp,1) ,2,'.','') }}</td>
				<td width="6%"align ="right">{{ number_format( round($pagoconsultasc,1) ,2,'.','') }}</td>
				<td width="6%" align ="right">{{ number_format( round($pagoexamenes,1) ,2,'.','') }}</td>
				<td width="6%" align ="right">{{ number_format( round($pagoconsultasc + $pagoconsultasp + $pagoexamenes,1) ,2,'.','') }}</td>
				<td>
					<button class="btn btn-danger btn-xs" id="btnDetalle" data-doctor_id="{{$doctor->id}}" data-fechainicial="{{$fechainicial}}" data-fechafinal="{{$fechafinal}}" onclick="mostrarDetallePago(this);" type="button">
						<i class="fa fa-list fa-lg"></i> Detalle a pagar
					</button>
					<button class="btn btn-success btn-xs" id="btnPagar" data-doctor_id="{{$doctor->id}}" data-pagoconsultasp="{{ $pagoconsultasp }}" data-pagoconsultasc="{{ $pagoconsultasc }}" data-pagoexamenes="{{ $pagoexamenes }}" data-fechainicial="{{$fechainicial}}" data-fechafinal="{{$fechafinal}}" onclick="guardarPagoDoctoresOjos(this);" type="button">
						<i class="fa fa-check fa-lg"></i> Pagar
					</button>
				</td>
			</tr>	
			@endif
		@endforeach

	</tbody>
</table>
<div style="text-align: right; display: none;">
{!! Form::button('<i class="fa fa-check fa-lg"></i> Guardar pagos', array('class' => 'btn btn-success btn-sm', 'id' => 'btnGuardar', 'onclick' => 'guardarPagoDoctores();', 'style' => 'margin-top:20px;')) !!}
</div>
<script type="text/javascript">
$(document).ready(function() {
	configurarAnchoModal('1100');
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