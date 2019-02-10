<div class="row">
	<div class="col-xs-12">
		<div class="row">
			<div class="col-xs-12">
				<table class="table table-striped table-bordered col-lg-12 col-md-12 col-sm-12 " style="padding: 0px 0px !important;">
					<thead id="cabecera">
						<tr>
							<th width='50%' style="font-size: 13px !important;">Descripción</th>
							<th align="center" width='10%' style="font-size: 13px !important;">Fecha</th>
							<th align="center" width='15%' style="font-size: 13px !important;">Realizado</th>
							<th width='25%' style="font-size: 13px !important;">Lugar</th>
						</tr>
					</thead>
					<tbody id="detalle">
					@foreach($examenes as $examen)
						<tr id="{{ $examen->idhc }}">
							<td class="servicio" id="{{ $examen->servicio->id }}">{{ $examen->servicio->nombre }}</td>
							<td align="center">{{ date("d/m/Y",strtotime($examen->historiaclinica->fecha_atencion)) }}</td>
							<td align="center">
								<label>
									<input type="radio" class="situacion{{ $examen->idhc }}" name="situacion{{ $examen->idhc }}" value="S"> SI
								</label>
								<label>
									<input checked type="radio" class="situacion{{ $examen->idhc }}" name="situacion{{ $examen->idhc }}" value="N"> NO
								</label>
							</td>
							<td align="center">
								<label>
									<input checked type="radio" class="lugar{{ $examen->idhc }}" name="lugar{{ $examen->idhc }}" value="C"> CLÍNICA
								</label>
								<label>
									<input type="radio" class="lugar{{ $examen->idhc }}" name="lugar{{ $examen->idhc }}" value="E"> EXTERIOR
								</label>
							</td>
						</tr>
					@endforeach
					</tbody>
				</table>
				<div style="text-align: right;">
				{!! Form::button('<i class="fa fa-check fa-lg"></i> Guardar', array('class' => 'btn btn-success btn-sm', 'onclick' => 'guardarExamenes();')) !!}
				{!! Form::button('<i class="fa fa-exclamation fa-lg"></i> Cerrar', array('class' => 'btn btn-warning btn-sm', 'onclick' => 'cerrarModal();')) !!}
				</div>
			</div>
		</div>
		<!-- /.box -->
	</div>
	<!-- /.col -->
</div>
<script type="text/javascript">
$(document).ready(function() {
	configurarAnchoModal('900');
}); 

function guardarExamenes(){

	var data = [];

	var examenesdetalle ="";
	
	$("#detalle tr").each(function(){
		var element = $(this); // <-- en la variable element tienes tu elemento
		var id = element.attr('id');
		var situacion ="N";
		if( $('input:radio[name=situacion'+id+']:checked').val() == "S") {  
			situacion = "S";
		}else{  
			situacion = "N";
		}  
		var lugar ="E";
		if( $('input:radio[name=lugar'+id+']:checked').val() == "E") {  
			lugar = "E";
		}else{  
			lugar = "C";
		}  
		data.push(
			{ 
				"id": id ,
				"situacion": situacion,
				"lugar": lugar,
			}
		);

		if( situacion == "S" && lugar == "C" ){

			var examen_clinica_id = $(this).children('.servicio').attr('id');

			seleccionarServicio(examen_clinica_id);
		}
		
	});

	var detalle = {"data": data};
	var json = JSON.stringify(detalle);

	 $.ajax({
		"method": "POST",
		"url": "{{ url('/ticket/guardarExamenes') }}",
		"data": {
			"examenes" : json, 
			"_token": "{{ csrf_token() }}",
			}
	}).done(function(info){
		if(info == "OK"){
			alert("Datos guardados correctamente");
			cerrarModal();

		}
	});
	
}
</script>