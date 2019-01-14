<?php
use App\Productoprincipio;
?>
<div id="divMensajeError{!! $entidad !!}"></div>
{!! Form::model($cita, $formData) !!}	
	<div class="col-sm-6">
		<?php
		$hoy = date("Y-m-d");
		?>
		<div class="form-group">
			{!! Form::label('fecha', 'Fecha:', array('class' => 'col-sm-2 control-label')) !!}
			<div class="col-sm-5">
				{!! Form::date('fecha', date('d-m-Y',strtotime($cita->fecha_atencion)) , array('class' => 'form-control input-xs col-sm-3', 'id' => 'fecha', 'readonly' => 'readonly')) !!}
			</div>
		</div>
		<div class="form-group">
			{!! Form::label('paciente', 'Paciente:', array('class' => 'col-sm-2 control-label')) !!}
			<div class="col-sm-10">
				{!! Form::text('paciente', $historia->persona->apellidopaterno . ' ' . $historia->persona->apellidomaterno . ' ' . $historia->persona->nombres , array('class' => 'form-control input-xs', 'id' => 'paciente', 'readonly' => 'readonly')) !!}
			</div>
		</div>
		<div class="form-group">
			{!! Form::label('historia', 'Historia:', array('class' => 'col-sm-2 control-label')) !!}
			<div class="col-sm-5">
				{!! Form::text('historia', '', array('class' => 'form-control input-xs', 'id' => 'historia', 'readonly' => 'readonly')) !!}
			</div>
		</div>
		<div class="form-group">
			{!! Form::label('numero', 'Tratam.:', array('class' => 'col-sm-2 control-label')) !!}
			<div class="col-sm-5">
				{!! Form::text('numero', , array('class' => 'form-control input-xs', 'id' => 'numero', 'readonly' => 'readonly')) !!}
			</div>
		</div>
		<div class="form-group">
			{!! Form::label('cie102', 'Cie10:', array('class' => 'col-sm-2 control-label')) !!}
			<div class="col-sm-5">
				{!! Form::text('cie102', '', array('class' => 'form-control input-xs', 'id' => 'cie102')) !!}
			</div>
		</div>
		{!! Form::button('<i class="glyphicon glyphicon-check"></i> Guardar', array('class' => 'btn btn-success btn-sm', 'id' => 'btnBuscar2', 'onclick' => 'registrarHistoriaClinica();')) !!}
		<h5 style="color: red; font-weight: bold;" id="mensajeHistoriaClinica"></h5>
		</div>
	<div class="col-sm-6">
		<div class="form-group">
			{!! Form::label('sintomas', 'Sintomas:') !!}
			<textarea class="form-control input-xs" id="sintomas" cols="10" rows="5" name="sintomas"></textarea>
		</div>
		<div class="form-group">
			{!! Form::label('diagnostico', 'Diagnostico:') !!}
			<textarea class="form-control input-xs" id="diagnostico" cols="10" rows="5" name="diagnostico"></textarea>
		</div>
		<div class="form-group">
			{!! Form::label('tratamiento', 'Tratamiento:') !!}
			<textarea class="form-control input-xs" id="tratamiento" cols="10" rows="5" name="tratamiento"></textarea>
		</div>												
	</div>	
{!! Form::close() !!}
