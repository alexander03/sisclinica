{!! Form::model($movimiento, $formData) !!}	
	<div id="divMensajeError{!! $entidad !!}"></div>
	<div class="form-group">
	    {!! Form::label('numero', 'Nro. Doc.:', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
	    {!! Form::label('numero', $movimiento->serie.'-'.$movimiento->numero, array('class' => 'col-lg-8 col-md-8 col-sm-8 control-label', 'style' => 'font-weight:normal;text-align:left;text-align:left')) !!}
	</div>
	<div class="form-group">
		{!! Form::label('siniestro', 'Siniestro:', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
		{!! Form::label('siniestro', $movimiento->comentario, array('class' => 'col-lg-8 col-md-8 col-sm-8 control-label', 'style' => 'font-weight:normal;text-align:left')) !!}
	</div>
	<div class="form-group">
		{!! Form::label('fecha', 'Fecha:', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
		{!! Form::label('siniestro', $movimiento->fecha, array('class' => 'col-lg-8 col-md-8 col-sm-8 control-label', 'style' => 'font-weight:normal;text-align:left')) !!}
	</div>
	<div class="form-group">
		{!! Form::label('paciente', 'Paciente:', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
		{!! Form::label('siniestro', $movimiento->persona->nombres . ' ' . $movimiento->persona->apellidopaterno . ' ' . $movimiento->persona->apellidomaterno, array('class' => 'col-lg-8 col-md-8 col-sm-8 control-label', 'style' => 'font-weight:normal;text-align:left')) !!}
	</div>
	<div class="form-group">
		{!! Form::label('responsable', 'Responsable:', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
		{!! Form::label('siniestro', $movimiento->responsable->nombres . ' ' . $movimiento->responsable->apellidopaterno . ' ' . $movimiento->responsable->apellidomaterno, array('class' => 'col-lg-8 col-md-8 col-sm-8 control-label', 'style' => 'font-weight:normal;text-align:left')) !!}
	</div>
	<div class="form-group">
		{!! Form::label('tipomovimiento', 'Tipo de movimiento:', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
		{!! Form::label('siniestro', $movimiento->tipomovimiento->nombre, array('class' => 'col-lg-8 col-md-8 col-sm-8 control-label', 'style' => 'font-weight:normal;text-align:left')) !!}
	</div>
	<div class="form-group">
		{!! Form::label('tipodocumento', 'Tipo de documento:', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
		{!! Form::label('siniestro', $movimiento->tipodocumento->nombre, array('class' => 'col-lg-8 col-md-8 col-sm-8 control-label', 'style' => 'font-weight:normal;text-align:left')) !!}
	</div>
	<div class="form-group">
		{!! Form::label('total', 'Total:', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
		{!! Form::label('siniestro', $movimiento->total, array('class' => 'col-lg-8 col-md-8 col-sm-8 control-label', 'style' => 'font-weight:normal;text-align:left')) !!}
	</div>
	<div class="form-group">
		{!! Form::label('doctor', 'Doctor:', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
		{!! Form::label('siniestro', $movimiento->doctor->nombres . ' ' . $movimiento->doctor->apellidopaterno . ' ' . $movimiento->doctor->apellidomaterno, array('class' => 'col-lg-8 col-md-8 col-sm-8 control-label', 'style' => 'font-weight:normal;text-align:left')) !!}
	</div>
	<div class="form-group">
		{!! Form::label('plan', 'Plan:', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
		{!! Form::label('siniestro', $movimiento->plan->nombre, array('class' => 'col-lg-8 col-md-8 col-sm-8 control-label', 'style' => 'font-weight:normal;text-align:left')) !!}
	</div>

	<div class="form-group">
		{!! Form::label('conceptopago_id', 'Concepto:', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
		<div class="col-lg-8 col-md-8 col-sm-8">
			<select name="conceptopago_id" id="conceptopago_id" class="form-control input-xs">
				<option value="">Seleccione Concepto</option>
				@foreach($cboConcepto as $concepto)
					<option value="{{ $concepto->id }}">{{ $concepto->nombre }}</option>
				@endforeach
			</select>	
		</div>
	</div>
	<div class="form-group">
		{!! Form::label('caja_id', 'Caja:', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
		<div class="col-lg-8 col-md-8 col-sm-8">
			<select name="caja_id" id="caja_id" class="form-control input-xs">
				<option value="">Seleccione Caja</option>
				@foreach($cboCaja as $caja)
					<option value="{{ $caja->id }}">{{ $caja->nombre }}</option>
				@endforeach
			</select>	
		</div>				
	</div>
	<div class="form-group">
		{!! Form::label('voucher', 'Voucher:', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
		<div class="col-lg-8 col-md-8 col-sm-8">
			{!! Form::text('voucher', null, array('class' => 'form-control input-xs', 'id' => 'dni', 'placeholder' => 'Ingrese voucher','maxlength' => '10')) !!}
		</div>		
	</div>
	<div class="form-group">
		{!! Form::label('totalpagado', 'Total Pagado:', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
		<div class="col-lg-8 col-md-8 col-sm-8">
			{!! Form::text('totalpagado', $movimiento->total, array('class' => 'form-control input-xs input-number', 'id' => 'dni', 'placeholder' => 'Ingrese total pagado')) !!}
		</div>			
	</div>	
	{!! Form::hidden('id', $movimiento->id) !!}
	<div class="form-group">
		<div class="col-lg-12 col-md-12 col-sm-12 text-right">
			{!! Form::button('<i class="fa fa-check fa-lg"></i> '.$boton, array('class' => 'btn btn-success btn-sm', 'id' => 'btnGuardar', 'onclick' => 'guardar(\''.$entidad.'\', this)')) !!}
			{!! Form::button('<i class="fa fa-exclamation fa-lg"></i> Cancelar', array('class' => 'btn btn-warning btn-sm', 'id' => 'btnCancelar'.$entidad, 'onclick' => 'cerrarModal();')) !!}
		</div>
	</div>
{!! Form::close() !!}
<script type="text/javascript">
$(document).ready(function() {
	configurarAnchoModal('600');
	init(IDFORMMANTENIMIENTO+'{!! $entidad !!}', 'B', '{!! $entidad !!}');
}); 

</script>