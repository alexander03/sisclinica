<div id="divMensajeError{!! $entidad !!}"></div>
{!! Form::model($cotizacion, $formData) !!}	
	<div class="col-lg-5 col-md-5 col-sm-5">
		<div class="form-group">
			{!! Form::label('fecha', 'Fecha:', array('class' => 'col-lg-5 col-md-5 col-sm-5 control-label')) !!}
			<div class="col-lg-7 col-md-7 col-sm-7">
				{!! Form::text('fecha', $cotizacion->fecha, array('class' => 'form-control input-xs', 'id' => 'fecha', 'readonly' => 'readonly')) !!}
			</div>
		</div>
		<div class="form-group">
			{!! Form::label('numerodocumento', 'Nro:', array('class' => 'col-lg-5 col-md-5 col-sm-5 control-label')) !!}
			<div class="col-lg-7 col-md-7 col-sm-7">
				{!! Form::text('numerodocumento', $cotizacion->numero, array('class' => 'form-control input-xs', 'id' => 'numerodocumento', 'placeholder' => 'Ingrese numerodocumento', 'readonly' => 'readonly')) !!}
			</div>
		</div>
		<div class="form-group">
			{!! Form::label('codigo', 'Código:', array('class' => 'col-lg-5 col-md-5 col-sm-5 control-label')) !!}
			<div class="col-lg-7 col-md-7 col-sm-7">
				{!! Form::text('codigo', $cotizacion->codigo, array('class' => 'form-control input-xs', 'id' => 'codigo', 'readonly' => 'readonly')) !!}
			</div>
		</div>
	</div>
	<div class="col-lg-7 col-md-7 col-sm-7">
		<div class="form-group">
			{!! Form::label('tipo', 'Tipo:', array('class' => 'col-lg-5 col-md-5 col-sm-5 control-label')) !!}
			<div class="col-lg-7 col-md-7 col-sm-7">
				{!! Form::text('tipo', $tipo, array('class' => 'form-control input-xs', 'id' => 'tipo', 'readonly' => 'readonly')) !!}
			</div>
		</div>
		<div class="form-group">
			{!! Form::label('total', 'Monto:', array('class' => 'col-lg-5 col-md-5 col-sm-5 control-label')) !!}
			<div class="col-lg-7 col-md-7 col-sm-7">
				{!! Form::text('total', number_format($cotizacion->total, 2), array('class' => 'form-control input-xs', 'id' => 'total', 'readonly' => 'readonly')) !!}
			</div>
		</div>
		<div class="form-group">
			{!! Form::label('paciente', 'Paciente:', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
			<div class="col-lg-8 col-md-8 col-sm-8">
				{!! Form::text('paciente', $cotizacion->paciente == null ? '-' : ($cotizacion->paciente->apellidopaterno . ' ' . $cotizacion->paciente->apellidomaterno . ' ' . $cotizacion->paciente->nombres), array('class' => 'form-control input-xs', 'id' => 'paciente', 'readonly' => 'readonly')) !!}
			</div>
		</div>
		<div class="form-group">
			{!! Form::label('situacion', 'Situación:', array('class' => 'col-lg-5 col-md-5 col-sm-5 control-label')) !!}
			<div class="col-lg-7 col-md-7 col-sm-7">
				{!! Form::text('situacion', $situacion, array('class' => 'form-control input-xs', 'id' => 'situacion', 'readonly' => 'readonly')) !!}
			</div>
		</div>		
	</div>
	<div class="row">
		<div class="col-lg-12 col-md-12 col-sm-12">
			<div id="divDetail" class="table-responsive" style="overflow:auto; height:180px; padding-right:10px; border:1px outset">
		        <table style="width: 100%;" class="table-condensed table-striped">
		            <thead>
		                <tr>
		                    <th bgcolor="#E0ECF8" class='text-center'>#</th>
		                    <th bgcolor="#E0ECF8" class='text-center'>Descripción</th>
		                </tr>
		            </thead>
		            <tbody>
		            <?php $i= 1; ?>
		            @foreach($cotizacion->detalles as $key => $value)
					<tr>
						<td class="text-center">{!! $i !!}</td>
						<td class="text-left">{!! $value->descripcion !!}</td>
					</tr>
					<?php $i++; ?>
					@endforeach
		            </tbody>
		           
		        </table>
		    </div>
		</div>
	 </div>
    <br>
	
	<div class="form-group">
		<div class="col-lg-12 col-md-12 col-sm-12 text-right">
			
			{!! Form::button('<i class="fa fa-exclamation fa-lg"></i> Cancelar', array('class' => 'btn btn-warning btn-sm', 'id' => 'btnCancelar'.$entidad, 'onclick' => 'cerrarModal();')) !!}
		</div>
	</div>
{!! Form::close() !!}
<script>
$(document).ready(function() {
    configurarAnchoModal('800');
    init(IDFORMMANTENIMIENTO+'{!! $entidad !!}', 'B', '{!! $entidad !!}');
});
</script>