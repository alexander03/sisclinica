<div class="row">
	<div class="col-xs-12">
		<div class="row">
			<div class="col-xs-12">
				{!! Form::open(['method' => 'GET' ,'onsubmit' => 'return false;', 'class' => 'form-inline', 'role' => 'form', 'autocomplete' => 'off']) !!}
					<div class="form-group">
						{!! Form::label('fechacirupro', 'Fecha:') !!}
						{!! Form::date('fechacirupro', date('Y-m-d'), array('class' => 'form-control input-xs', 'id' => 'fechacirupro')) !!}
					</div>
					{!! Form::button('<i class="glyphicon glyphicon-search"></i> Reporte sin detalles', array('class' => 'btn btn-success btn-xs', 'onclick' => 'window.open("caja/reporteCiruProSinDetalle?fecha=" + $("#fechacirupro").val())')) !!}
					{!! Form::button('<i class="glyphicon glyphicon-search"></i> Reporte Detallado', array('class' => 'btn btn-danger btn-xs', 'onclick' => 'window.open("caja/reporteCiruProConDetalle?fecha=" + $("#fechacirupro").val())')) !!}
					{!! Form::button('<i class="fa fa-exclamation fa-lg"></i> Cerrar', array('class' => 'btn btn-warning btn-xs', 'onclick' => 'cerrarModal();')) !!}
				{!! Form::close() !!}
			</div>
		</div>
		<hr>
		<!-- /.box -->
	</div>
	<!-- /.col -->
</div>
<script type="text/javascript">
$(document).ready(function() {
	configurarAnchoModal('600');
}); 
</script>