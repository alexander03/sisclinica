<div id="divMensajeError{!! $entidad !!}"></div>
{!! Form::model($requerimiento, $formData) !!}	
	{!! Form::hidden('listar', $listar, array('id' => 'listar')) !!}
	<div class="col-lg-6 col-md-6 col-sm-6">
		<div class="form-group">
			{!! Form::label('numerodocumento', 'Nro Doc:', array('class' => 'col-lg-5 col-md-5 col-sm-5 control-label')) !!}
			<div class="col-lg-7 col-md-7 col-sm-7">
				{!! Form::text('numerodocumento',str_pad($requerimiento->numero,8,'0',STR_PAD_LEFT), array('class' => 'form-control input-xs', 'id' => 'numerodocumento', 'readonly' => 'true')) !!}
			</div>
		</div>
		<div class="form-group">
			{!! Form::label('comentario', 'Comentario:', array('class' => 'col-lg-5 col-md-5 col-sm-5 control-label')) !!}
			<div class="col-lg-7 col-md-7 col-sm-7">
				{!! Form::textarea('comentario', null, array('style' => 'resize: none;', 'rows' => '3','class' => 'form-control input-xs', 'id' => 'comentario', 'placeholder' => 'Ingrese comentario')) !!}
			</div>
		</div>


	</div>
	<div class="col-lg-6 col-md-6 col-sm-6">
		<div class="form-group">
			{!! Form::label('fecha', 'Fecha:', array('class' => 'col-lg-5 col-md-5 col-sm-5 control-label')) !!}
			<div class="col-lg-7 col-md-7 col-sm-7">
				<div class='input-group input-group-xs' id='divfecha'>
					{!! Form::text('fecha', date('d/m/Y'), array('class' => 'form-control input-xs', 'id' => 'fecha', 'readonly' => 'true')) !!}
				</div>
			</div>
		</div>
		
		
	</div>
	<div class="row">
		<div class="col-lg-12 col-md-12 col-sm-12">
			<div id="divDetail" class="table-responsive" style="overflow:auto; height:180px; padding-right:10px; border:1px outset">
		        <table style="width: 100%;" class="table-condensed table-striped">
		            <thead>
		                <tr>
		                    <th bgcolor="#E0ECF8" class='text-center'>Producto</th>
		                    <th bgcolor="#E0ECF8" class='text-center'>Cantidad</th>
		                    <th bgcolor="#E0ECF8" class="text-center">Presentacion</th>
		                    <th bgcolor="#E0ECF8" class="text-center">Despacho</th>
		                </tr>
		            </thead>
		            <tbody>
		            @foreach($detalles as $key => $value)
					<tr>
						<td class="text-center">{!! $value->producto->nombre !!}</td>
						<td class="text-center">{!! $value->cantidad !!}</td>
						<td class="text-center">{!! $value->producto->presentacion->nombre !!}</td>
					</tr>
					@endforeach
		            </tbody>
		           
		        </table>
		    </div>
		</div>
	 </div>
    <br>
	
	<div class="form-group">
		<div class="col-lg-12 col-md-12 col-sm-12 text-right">
			{!! Form::button('<i class="fa fa-check fa-lg"></i> '.$boton, array('class' => 'btn btn-success btn-sm', 'id' => 'btnGuardar', 'onclick' => 'guardar(\''.$entidad.'\', this)')) !!}
			{!! Form::button('<i class="fa fa-exclamation fa-lg"></i> Cancelar', array('class' => 'btn btn-warning btn-sm', 'id' => 'btnCancelar'.$entidad, 'onclick' => 'cerrarModal();')) !!}
		</div>
	</div>
{!! Form::close() !!}
<script type="text/javascript">
$(document).ready(function() {
	configurarAnchoModal('880');
	init(IDFORMMANTENIMIENTO+'{!! $entidad !!}', 'M', '{!! $entidad !!}');

}); 

</script>