<div class="row">
	<div class="col-xs-12">
		<div class="row">
			<div class="col-xs-12">
				{!! Form::open(['method' => 'GET' ,'onsubmit' => 'return false;', 'class' => 'form-inline', 'role' => 'form', 'autocomplete' => 'off']) !!}
					<div class="form-group">
						{!! Form::label('fechainicial', 'Fecha inicial:') !!}
						{!! Form::date('fechainicial',date('Y-m-d',strtotime("now",strtotime("-1 month"))), array('class' => 'form-control input-xs', 'id' => 'fechainicial', 'onchange' => 'listapagosdoctoresojos();')) !!}
					</div>
					<div class="form-group">
						{!! Form::label('fechafinal', 'Fecha final:') !!}
						{!! Form::date('fechafinal', date('Y-m-d'), array('class' => 'form-control input-xs', 'id' => 'fechafinal', 'onchange' => 'listapagosdoctoresojos();')) !!}
					</div>
					<div class="form-group">
						{!! Form::label('doctor_ticket', 'Doctor:') !!}
						{!! Form::select('doctor_ticket', $cboDoctores, '', array('class' => 'form-control input-xs', 'id' => 'doctor_ticket', 'onchange' => 'listapagosdoctoresojos();')) !!}
					</div>
					<br>
					{!! Form::button('<i class="glyphicon glyphicon-search"></i> Buscar', array('class' => 'btn btn-primary btn-xs', 'id' => 'btnBuscar', 'onclick' => 'listapagosdoctoresojos();')) !!}
					{!! Form::button('<i class="glyphicon glyphicon-print"></i> Exportar PDF', array('class' => 'btn btn-warning btn-xs', 'id' => 'btnReporte', 'onclick' => 'reportePagosOjos();')) !!}
					{!! Form::button('<i class="glyphicon glyphicon-print"></i> Exportar EXCEL', array('class' => 'btn btn-success btn-xs', 'id' => 'btnReporte', 'onclick' => 'excelReportePagosOjos();')) !!}
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
	listapagosdoctoresojos();
	$('#persona_ticket').focus();
}); 
function listapagosdoctoresojos() {	
	var doctor = $('#doctor_ticket').val();
	if(doctor == '') {
		doctor = '0';
	}
	cargarRuta('{{ url('/caja/listapagosdoctoresojos') }}' + '/' + doctor + '/' + $('#fechainicial').val() + '/' + $('#fechafinal').val() , "listado{{ $entidad }}");	
}

function reportePagosOjos(){
	var fechainicial = $("#fechainicial").val();
	var fechafinal = $("#fechafinal").val();
	window.open("caja/pdfReportePagoOjos?fechainicial=" + fechainicial + "&fechafinal=" + fechafinal,"_blank");
}

function excelReportePagosOjos(){
	var fechainicial = $("#fechainicial").val();
	var fechafinal = $("#fechafinal").val();
	window.open("caja/excelReportePagosOjos?fechainicial=" + fechainicial + "&fechafinal=" + fechafinal,"_blank");
}	
</script>