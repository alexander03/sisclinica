<?php

$fechacarta = '';
$cotizacion_id = '';
$codigocotizacion = '';
$plancotizacion = '';
$fechacotizacion = '';
$tipocotizacion = '';
$montocotizacion = '';
$totalcarta = '';
$pacientecotizacion = '';
$codigocarta = '';
$comentariocarta = '';
$paciente_id = '';


if($carta !== NULL) {
	$fechacarta = $carta->codigo;
	$codigocotizacion = $carta->cotizacion->codigo;
	$cotizacion_id = $carta->cotizacion->id;
	$plancotizacion = $carta->cotizacion->plan->nombre;
	$fechacotizacion = $carta->cotizacion->fecha;
	$tipocotizacion = $carta->cotizacion->tipo == 'A'?'AMBULATORIA':'HOSPITALARIA';
	$montocotizacion = $carta->cotizacion->total;
	$totalcarta = $carta->monto;
	$pacientecotizacion = $carta->cotizacion->paciente->dni . ' - ' . $carta->cotizacion->paciente->nombres . ' ' . $carta->cotizacion->paciente->apellidopaterno . ' ' . $carta->cotizacion->paciente->apellidomaterno;
	$codigocarta = $carta->codigo;
	$comentariocarta = $carta->comentario;
	$paciente_id = $carta->cotizacion->paciente->id;

}

?>
<section class="content">
	<div class="row">
		<div class="col-xs-12">
			<div class="box">
				<div class="box-header">
					{!! Form::model($carta, $formData) !!}	
					{!! Form::hidden('listar', $listar, array('id' => 'listar')) !!}
					<div class="row">
						<div class="col-xs-6">
							<div class="form-group">
								{!! Form::label('fechacarta', 'Fecha de Carta de Garantía') !!}
								{!! Form::date('fechacarta', $carta !== NULL ? date('Y-m-d') : $fechacarta, array('class' => 'form-control input-xs', 'id' => 'fechacarta')) !!}
							</div>
							<div class="form-group">
								{!! Form::label('codigocotizacion', 'Código de Cotización') !!}
								{!! Form::hidden('cotizacion_id', $cotizacion_id, array('id' => 'cotizacion_id')) !!}
								{!! Form::text('codigocotizacion', $codigocotizacion, array('class' => 'form-control input-xs', 'id' => 'codigocotizacion', 'onkeyup' => 'buscarCotizacionCodigo();')) !!}
							</div>
							<div class="form-group">
								{!! Form::label('plancotizacion', 'Plan') !!}
								{!! Form::text('plancotizacion', $plancotizacion, array('class' => 'form-control input-xs', 'id' => 'plancotizacion', 'readonly' => 'readonly')) !!}
							</div>
							<div class="form-group">
								{!! Form::label('fechacotizacion', 'Fecha de Cotización') !!}
								{!! Form::text('fechacotizacion', $fechacotizacion, array('class' => 'form-control input-xs', 'id' => 'fechacotizacion', 'readonly' => 'readonly')) !!}
							</div>
							<div class="form-group">
								{!! Form::label('tipocotizacion', 'Tipo de Cotización') !!}
								{!! Form::text('tipocotizacion', $tipocotizacion, array('class' => 'form-control input-xs', 'id' => 'tipocotizacion', 'readonly' => 'readonly')) !!}
							</div>
							<div class="form-group">
								{!! Form::label('montocotizacion', 'Monto de Cotización') !!}
								{!! Form::text('montocotizacion', $montocotizacion, array('class' => 'form-control input-xs', 'id' => 'montocotizacion', 'readonly' => 'readonly')) !!}
							</div>
						</div>
						<div class="col-xs-6">
							<div class="form-group">
								{!! Form::label('totalcarta', 'Monto de Carta') !!}
								{!! Form::text('totalcarta', $totalcarta, array('class' => 'form-control input-xs', 'id' => 'totalcarta', 'readonly' => 'readonly')) !!}
							</div>
							<div class="form-group">
								{!! Form::label('pacientecotizacion', 'Paciente') !!}
								{!! Form::hidden('paciente_id', $paciente_id, array('id' => 'paciente_id')) !!}
								{!! Form::text('pacientecotizacion', $pacientecotizacion, array('class' => 'form-control input-xs', 'id' => 'pacientecotizacion')) !!}
							</div>
							<div class="form-group">
								{!! Form::label('codigocarta', 'Código Carta') !!}
								{!! Form::text('codigocarta', $codigocarta, array('class' => 'form-control input-xs', 'id' => 'codigocarta')) !!}
							</div>
							<div class="form-group">
								{!! Form::label('comentariocarta', 'Comentario') !!}
								{!! Form::textarea('comentariocarta', $comentariocarta, array('class' => 'form-control input-xs', 'id' => 'comentariocarta')) !!}
							</div>
							{!! Form::button('<i class="glyphicon glyphicon-check"></i> ' . $boton, array('class' => 'btn btn-success btn-sm', 'id' => 'btnBuscarc', 'onclick' => 'guardarCarta();')) !!}
							{!! Form::button('<i class="fa fa-exclamation fa-lg"></i> Cancelar', array('class' => 'btn btn-warning btn-sm', 'id' => 'btnCancelar'.$entidad, 'onclick' => 'cerrarModal();')) !!}
						</div>
					</div>
					{!! Form::close() !!}
				</div>
				<!-- /.box-header -->
				<div class="box-body" id="listado{{ $entidad }}">
				</div>
				<!-- /.box-body -->
			</div>
			<!-- /.box -->
		</div>
		<!-- /.col -->
	</div>
	<!-- /.row -->
</section>
<!-- /.content -->	
<script>
	$(document).ready(function () {
		configurarAnchoModal('750');
		$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="codigocotizacion"]').focus();
		$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="totalcarta"]').inputmask('decimal', { radixPoint: ".", autoGroup: true, groupSeparator: "", groupSize: 3, digits: 2 });
		$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="montocotizacion"]').inputmask('decimal', { radixPoint: ".", autoGroup: true, groupSeparator: "", groupSize: 3, digits: 2 });
	});

	function guardarCarta() {
		var mensaje      = '';
		var idformulario = IDFORMMANTENIMIENTO + '{{ $entidad }}';
    	var data         = $(idformulario).serialize();
		if($(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="fechacarta"]').val() == '') {
			mensaje += '* Debes ingresar una Fecha de Carta.\n'; 
		} 
		if($(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="cotizacion_id"]').val() == '') {
			mensaje += '* Debes ingresar código de Cotización.\n'; 			
		}
		if($(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="paciente_id"]').val() == '') {
			mensaje += '* Debes ingresar un Paciente.\n'; 
		}
		if($(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="codigocarta"]').val() == '') {
			mensaje += '* Debes ingresar un Código de Carta.\n'; 
		}
		if(mensaje !== '') {
			alert(mensaje);
			return false;
		} else {
			$.ajax({
				url: $(idformulario).attr('action'),
				type: $(idformulario).attr('method'),
				data: data,
				dataType: 'JSON',
				beforeSend: function() {
					$('#btnBuscarc').html('Cargando...').attr('disabled', 'disabled');
				},
				success: function(e) {
					if(e.respuesta == 'OK') {
						cerrarModal();
						buscar('CartaGarantia');
					} else {
						alert("Error al Registrar Carta de Garantía.");
						cerrarModal();
					}
				},
			}).fail(function() {
				alert("Error al Registrar Carta de Garantía.");
			});	
		}			
	}

	function buscarCotizacionCodigo() {
		var codigo = $(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="codigocotizacion"]').val();
		if(codigo === '') { codigo = '0'; }
		$.ajax({
			url: 'cartagarantia/buscarcotizacion/' + codigo,
			type: 'GET',
			dataType: 'JSON',			
		})
		.done(function(e) {
			if(e.codigo !== '') {
				$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="cotizacion_id"]').val(e.id);
				$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="plancotizacion"]').val(e.plan);
				$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="fechacotizacion"]').val(e.fecha);
				$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="tipocotizacion"]').val(e.tipo);
				$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="codigocotizacion"]').val(e.codigo);
				$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="montocotizacion"]').val(e.total);
				$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="totalcarta"]').val(e.total);
				$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="pacientecotizacion"]').val(e.persona);
				$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="paciente_id"]').val(e.person_id);
				if(e.persona === '') {
					//$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="pacientecotizacion"]').removeAttr('readonly');
					$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="pacientecotizacion"]').focus();
				} else {
					$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="codigocarta"]').focus();
					//$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="pacientecotizacion"]').attr('readonly', 'readonly');
				}					
			} else {
				$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="cotizacion_id"]').val('');
				$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="plancotizacion"]').val('');
				$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="fechacotizacion"]').val('');
				$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="tipocotizacion"]').val('');
				$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="montocotizacion"]').val('');
				$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="totalcarta"]').val('');
				$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="pacientecotizacion"]').val('');
				$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="paciente_id"]').val('');
			}
		})
		.fail(function() {
			console.log("Error al Solicitar Datos.");
		});	
	}

	var personas2 = new Bloodhound({
		datumTokenizer: function (d) {
			return Bloodhound.tokenizers.whitespace(d.value);
		},
		queryTokenizer: Bloodhound.tokenizers.whitespace,
		remote: {
			url: 'cartagarantia/personaautocompletar/%QUERY',
			filter: function (personas2) {
				return $.map(personas2, function (movie) {
					return {
						value: movie.value,
						id: movie.id,
						label: movie.label,
					};
				});
			}
		}
	});
	personas2.initialize();
	$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="pacientecotizacion"]').typeahead(null,{
		displayKey: 'label',
		source: personas2.ttAdapter()
	}).on('typeahead:selected', function (object, datum) {
		$(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="paciente_id"]').val(datum.id);
        $(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="pacientecotizacion"]').val(datum.value);
	});
</script>