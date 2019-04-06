<div id="divMensajeError{!! $entidad !!}"></div>
{!! Form::model($ingreso, $formData) !!}	
	{!! Form::hidden('listar', $listar, array('id' => 'listar')) !!}
	{!! Form::hidden('id22', $id, array('id' => 'id22')) !!}
	<div class="form-group">
		{!! Form::label('efectivo22', 'EFECTIVO:', array('class' => 'col-lg-3 col-md-3 col-sm-3 control-label')) !!}
		<div class="col-lg-9 col-md-9 col-sm-9">
			{!! Form::text('efectivo22', $ingreso->totalpagado, array('class' => 'form-control input-xs', 'id' => 'efectivo22', 'placeholder' => 'Ingrese efectivo')) !!}
		</div>		
	</div>
	<div class="form-group">
		{!! Form::label('visa22', 'VISA:', array('class' => 'col-lg-3 col-md-3 col-sm-3 control-label')) !!}
		<div class="col-lg-9 col-md-9 col-sm-9">
			{!! Form::text('visa22', $ingreso->totalpagadovisa, array('class' => 'form-control input-xs', 'id' => 'visa22', 'placeholder' => 'Ingrese visa22')) !!}
		</div>
	</div>
	<div class="form-group">
		{!! Form::label('master22', 'MASTER:', array('class' => 'col-lg-3 col-md-3 col-sm-3 control-label')) !!}
		<div class="col-lg-9 col-md-9 col-sm-9">
			{!! Form::text('master22', $ingreso->totalpagadomaster, array('class' => 'form-control input-xs', 'id' => 'master22', 'placeholder' => 'Ingrese master22')) !!}
		</div>
	</div>
	<div class="form-group">
		<div class="col-lg-12 col-md-12 col-sm-12 text-right">
			{!! Form::button('<i class="fa fa-check fa-lg"></i> '.$boton, array('class' => 'btn btn-success btn-sm', 'id' => 'btnGuardar', 'onclick' => 'guardar(\''.$entidad.'\', this)')) !!}
			{!! Form::button('<i class="fa fa-exclamation fa-lg"></i> Cancelar', array('class' => 'btn btn-warning btn-sm', 'id' => 'btnCancelar'.$entidad, 'onclick' => 'cerrarModal();')) !!}
		</div>
	</div>
{!! Form::close() !!}
<script type="text/javascript">
$(document).ready(function() {
	configurarAnchoModal('350');
	init(IDFORMMANTENIMIENTO+'{!! $entidad !!}', 'M', '{!! $entidad !!}');
	$(document).on('click', '#btnGuardar', function(e) {
		var efectivo = parseFloat($('#efectivo22').val());
		var visa = parseFloat($('#visa22').val());
		var master = parseFloat($('#master22').val()); 

		if((efectivo+visa+master) !== {{ ($ingreso->totalpagado+$ingreso->totalpagadovisa+$ingreso->totalpagadomaster) }}) {
			alert('Los montos deben sumar {{ ($ingreso->totalpagado+$ingreso->totalpagadovisa+$ingreso->totalpagadomaster) }}');
			return false;
		} else {
			buscar('Caja');
			cerrarModal();
		}			
	});
}); 
</script>