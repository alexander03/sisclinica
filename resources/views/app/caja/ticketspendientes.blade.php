<div class="row">
	<div class="col-xs-12">
		<div class="row">
			<div class="col-xs-12">
				{!! Form::open(['method' => 'GET' ,'onsubmit' => 'return false;', 'class' => 'form-inline', 'role' => 'form', 'autocomplete' => 'off']) !!}
					<div class="form-group">
						{!! Form::label('fecha', 'Fecha:') !!}
						{!! Form::date('fecha', date('Y-m-d'), array('class' => 'form-control input-xs', 'id' => 'fecha', 'onchange' => 'listatickestpendientes();')) !!}
					</div>
					<div class="form-group">
						{!! Form::label('numero', 'Nro:') !!}
						{!! Form::text('numero', '', array('class' => 'form-control input-xs', 'id' => 'numero', 'onkeyup' => 'listatickestpendientes();')) !!}
					</div>
					{!! Form::button('<i class="glyphicon glyphicon-search"></i> Buscar', array('class' => 'btn btn-success btn-xs', 'id' => 'btnBuscar', 'onclick' => 'listatickestpendientes();')) !!}
					{!! Form::button('<i class="fa fa-exclamation fa-lg"></i> Cancelar', array('class' => 'btn btn-warning btn-xs', 'id' => 'btnCerrarTicketsPendientes', 'onclick' => 'cerrarModal();')) !!}
				{!! Form::close() !!}
			</div>
		</div>
		<hr>
		<div class="box-body" id="listado{{ $entidad }}"></div>
		<!-- /.box -->
	</div>
	<!-- /.col -->
</div>
<script type="text/javascript">
$(document).ready(function() {
	configurarAnchoModal('900');
	listatickestpendientes();
}); 
function listatickestpendientes() {	
	var numero = $('#numero').val();
	if(numero == '') {
		numero = '0';
	}
	cargarRuta('{{ url('/caja/listaticketspendientes') }}' + '/' + numero + '/' + $('#fecha').val(), "listado{{ $entidad }}");	
}
</script>