<div class="row">
	<div class="col-xs-12">
		<div class="row">
			<div class="col-xs-12">
				{!! Form::open(['method' => 'GET' ,'onsubmit' => 'return false;', 'class' => 'form-inline', 'role' => 'form', 'autocomplete' => 'off']) !!}
					<div class="form-group">
						{!! Form::label('fecha', 'Fecha:') !!}
						{!! Form::date('fecha', date('Y-m-d'), array('class' => 'form-control input-xs', 'id' => 'fechareprogramar', 'onchange' => 'listaticketsatendidos();')) !!}
					</div>
					<div class="form-group">
						{!! Form::label('doctor_ticket', 'Doctor:') !!}
						{!! Form::select('doctor_ticket', $cboDoctores, '', array('class' => 'form-control input-xs', 'id' => 'doctor_ticket', 'onchange' => 'listaticketsatendidos();')) !!}
					</div>
					<div class="form-group">
						{!! Form::label('persona_ticket', 'Paciente:') !!}
						{!! Form::text('persona_ticket', '', array('class' => 'form-control input-xs', 'id' => 'persona_ticket', 'onkeyup' => 'listaticketsatendidos();')) !!}
					</div>
					<br>
					{!! Form::button('<i class="glyphicon glyphicon-search"></i> Buscar', array('class' => 'btn btn-success btn-xs', 'id' => 'btnBuscar', 'onclick' => 'listaticketsatendidos();')) !!}
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
	listaticketsatendidos();
	$('#persona_ticket').focus();
}); 
function listaticketsatendidos() {	
	var paciente = $('#persona_ticket').val();
	var doctor = $('#doctor_ticket').val();
	if(doctor == '') {
		doctor = '0';
	}
	if(paciente == '') {
		paciente = '0';
	}
	cargarRuta('{{ url('/ticket/listaticketsatendidos') }}' + '/' + doctor +  '/' + $('#fechareprogramar').val() + '/' + paciente , "listado{{ $entidad }}");	
}
</script>