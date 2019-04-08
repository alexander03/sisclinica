<div class="row">
	<div class="col-xs-12">
		<div class="row">
			<div class="col-xs-12">
				{!! Form::open(['method' => 'GET' ,'onsubmit' => 'return false;', 'class' => 'form-inline', 'role' => 'form', 'autocomplete' => 'off']) !!}
					<div class="form-group">
						{!! Form::label('fecha', 'Fecha:') !!}
						{!! Form::date('fecha', date('Y-m-d'), array('class' => 'form-control input-xs', 'id' => 'fecha', 'onchange' => 'listapagosdoctores();')) !!}
					</div>
					<div class="form-group">
						{!! Form::label('doctor_ticket', 'Doctor:') !!}
						{!! Form::select('doctor_ticket', $cboDoctores, '', array('class' => 'form-control input-xs', 'id' => 'doctor_ticket', 'onchange' => 'listapagosdoctores();')) !!}
					</div>
					<div class="form-group">
						{!! Form::label('persona_ticket', 'Paciente:') !!}
						{!! Form::text('persona_ticket', '', array('class' => 'form-control input-xs', 'id' => 'persona_ticket', 'onkeyup' => 'listapagosdoctores();')) !!}
					</div>
					<div class="form-group">
						{!! Form::label('tipopaciente', 'Tipo Paciente:') !!}
						{!! Form::select('tipopaciente', $cboTipoPaciente, '', array('class' => 'form-control input-xs', 'id' => 'tipopaciente', 'onchange' => 'listapagosdoctores();')) !!}
					</div>
					<br>
					{!! Form::button('<i class="glyphicon glyphicon-search"></i> Buscar', array('class' => 'btn btn-primary btn-xs', 'id' => 'btnBuscar', 'onclick' => 'listapagosdoctores();')) !!}
					{!! Form::button('<i class="glyphicon glyphicon-print"></i> Exportar PDF', array('class' => 'btn btn-warning btn-xs', 'id' => 'btnReporte', 'onclick' => 'reportePagos();')) !!}
					{!! Form::button('<i class="glyphicon glyphicon-print"></i> Exportar EXCEL', array('class' => 'btn btn-success btn-xs', 'id' => 'btnReporte', 'onclick' => 'excelReportePagos();')) !!}
					{!! Form::button('<i class="fa fa-exclamation fa-lg"></i> Cancelar', array('class' => 'btn btn-danger btn-xs', 'id' => 'btnCerrarTicketsPendientes', 'onclick' => 'cerrarModal();')) !!}
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
	listapagosdoctores();
	$('#persona_ticket').focus();
}); 
function listapagosdoctores() {	
	var paciente = $('#persona_ticket').val();
	var doctor = $('#doctor_ticket').val();
	var tipopaciente = $('#tipopaciente').val();
	if(doctor == '') {
		doctor = '0';
	}
	if(paciente == '') {
		paciente = '0';
	}
	cargarRuta('{{ url('/caja/listapagosdoctores') }}' + '/' + doctor + '/' + $('#fecha').val() + '/' + paciente + '/' + tipopaciente, "listado{{ $entidad }}");	
}

function reportePagos(){
	var fecha = $("#fecha").val();
	window.open("caja/pdfReportePago?fecha=" + fecha,"_blank");
}	
function excelReportePagos(){
	var fecha = $("#fecha").val();
	window.open("caja/excelReportePagos?fecha=" + fecha,"_blank");
	//window.open("caja/pdfReportePago?fecha=" + fecha,"_blank");
}	
</script>