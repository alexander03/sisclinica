<?php
	use Illuminate\Support\Facades\Session;
	use Illuminate\Support\Facades\Auth;

	$sucursal_id = Session::get('sucursal_id');
?>

<!-- Content Header (Page header) -->
<section class="content-header">
	<h1>
		{{ $title }}
		{{-- <small>Descripción</small> --}}
	</h1>
	{{--
	<ol class="breadcrumb">
		<li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
		<li><a href="#">Tables</a></li>
		<li class="active">Data tables</li>
	</ol>
	--}}
</section>

<!-- Main content -->
<section class="content">
	<div class="row">
		<div class="col-xs-12">
			<div class="box">
				<div class="box-header">
					<div class="row">
						<div class="col-xs-12">
							{!! Form::open(['route' => $ruta["search"], 'method' => 'POST' ,'onsubmit' => 'return false;', 'class' => 'form-inline', 'role' => 'form', 'autocomplete' => 'off', 'id' => 'formBusqueda'.$entidad]) !!}
							{!! Form::hidden('page', 1, array('id' => 'page')) !!}
							{!! Form::hidden('accion', 'listar', array('id' => 'accion')) !!}
							<div class="form-group">
								{!! Form::label('fechainicial', 'Fecha Inicial:') !!}
								{!! Form::date('fechainicial', date('Y-m-d'), array('class' => 'form-control input-xs', 'id' => 'fechainicial')) !!}
							</div>
                            <div class="form-group">
								{!! Form::label('fechafinal', 'Fecha Final:') !!}
								{!! Form::date('fechafinal', date('Y-m-d'), array('class' => 'form-control input-xs', 'id' => 'fechafinal')) !!}
							</div>
							<div class="form-group" @if(Auth::user()->usertype_id != 1 && Auth::user()->usertype_id != 2) style="display: none;" @endif>
								<select name="caja_id" id="caja_id" class='form-control input-xs' onchange="buscar('{{$entidad}}')">
									@if($sucursal_id==1)
									<option value="3">CAJA FARMACIA BMOJOS</option>
									<option value="1">CAJA BMOJOS</option>
									@elseif($sucursal_id==2)
									<option value="4">CAJA FARMACIA ESPECIALIDADES</option>
									<option value="2">CAJA ESPECIALIDADES</option>
									@endif
								</select>
							</div>			
							<div class="form-group">
								{!! Form::label('tipodocumento', 'Tipo Doc.:') !!}
								{!! Form::select('tipodocumento', $cboTipoDoc,'', array('class' => 'form-control input-xs', 'id' => 'tipodocumento', 'onchange' => 'buscar(\''.$entidad.'\')')) !!}
							</div>
							<div class="form-group">
								{!! Form::label('numero', 'Nro.:') !!}
								{!! Form::text('numero', '', array('class' => 'form-control input-xs', 'id' => 'numero', 'size' => 10)) !!}
							</div>
							<div class="form-group">
								{!! Form::label('paciente', 'Paciente:') !!}
								{!! Form::text('paciente', '', array('class' => 'form-control input-xs', 'id' => 'paciente', 'size' => 20)) !!}
							</div>
							<div class="form-group">
								{!! Form::label('filas', 'Filas a mostrar:')!!}
								{!! Form::selectRange('filas', 1, 30, 20, array('class' => 'form-control input-xs', 'onchange' => 'buscar(\''.$entidad.'\')')) !!}
							</div>
							<div class="form-group">
								{!! Form::label('situacion', 'Situacion:') !!}
								{!! Form::select('situacion', $cboSituacion,'', array('class' => 'form-control input-xs', 'id' => 'situacion', 'onchange' => 'buscar(\''.$entidad.'\')')) !!}
							</div>
							{!! Form::button('<i class="glyphicon glyphicon-search"></i> Buscar', array('class' => 'btn btn-info btn-xs', 'id' => 'btnBuscar', 'onclick' => 'buscar(\''.$entidad.'\')')) !!}
							@if(Auth::user()->usertype_id==8 || Auth::user()->usertype_id==1)
							{!! Form::button('<i class="glyphicon glyphicon-cog"></i> Procesar', array('class' => 'btn btn-danger btn-xs', 'id' => 'btnProcesar', 'onclick' => 'procesar(\''.$entidad.'\')', 'style' => 'display:none')) !!}
							{!! Form::button('<i class="glyphicon glyphicon-cog"></i> Resumen', array('class' => 'btn btn-warning btn-xs', 'id' => 'btnResumen','onclick' => 'resumen();')) !!}
							<?php /*{!! Form::button('<i class="glyphicon glyphicon-file"></i> No click', array('class' => 'btn btn-danger btn-xs', 'id' => 'btnResumen','onclick' => 'resumen1();')) !!} */ ?>
							{!! Form::button('<i class="glyphicon glyphicon-file"></i> Concar', array('class' => 'btn btn-success btn-xs', 'id' => 'btnExcel','onclick' => 'excelConcar();')) !!}
							{!! Form::button('<i class="glyphicon glyphicon-file"></i> Venta', array('class' => 'btn btn-success btn-xs', 'id' => 'btnExcel1','onclick' => 'excelVenta();')) !!}
							{!! Form::button('<i class="glyphicon glyphicon-file"></i> Sunat', array('class' => 'btn btn-success btn-xs', 'id' => 'btnExcel2','onclick' => 'excelSunat();')) !!}
							{!! Form::button('<i class="glyphicon glyphicon-file"></i> Venta Convenio', array('class' => 'btn btn-success btn-xs', 'id' => 'btnExcel3','onclick' => 'excelVenta2();')) !!}
							{!! Form::button('<i class="glyphicon glyphicon-file"></i> Venta Bizlink', array('class' => 'btn btn-success btn-xs', 'id' => 'btnExcel4','onclick' => 'excelVentaBizlink();')) !!}
							@endif
							{!! Form::close() !!}
						</div>
					</div>
				</div>
				<!-- /.box-header -->
				<div class="box-body" id="listado{{ $entidad }}">
				</div>
				<!-- /.box-body -->
			</div>
			<!-- /.box -->
		</div>
		<!-- /.col -->
	</div>
	<!-- /.row -->
</section>
<!-- /.content -->	
<script>
$(document).ready(function () {
	buscar('{{ $entidad }}');
	init(IDFORMBUSQUEDA+'{{ $entidad }}', 'B', '{{ $entidad }}');
	$(IDFORMBUSQUEDA + '{{ $entidad }} :input[id="numero"]').keyup(function (e) {
		var key = window.event ? e.keyCode : e.which;
		if (key == '13') {
			buscar('{{ $entidad }}');
		}
	});
	$(IDFORMBUSQUEDA + '{{ $entidad }} :input[id="paciente"]').keyup(function (e) {
		var key = window.event ? e.keyCode : e.which;
		if (key == '13') {
			buscar('{{ $entidad }}');
		}
	});

});
function procesar(entidad){
	var btn = $(IDFORMBUSQUEDA + '{!! $entidad !!} :input[id="btnProcesar"]');
	btn.button('loading');
    $.ajax({
        type: "POST",
        url: "ventaadmision/procesar",
        data: "fechainicial="+$(IDFORMBUSQUEDA + '{!! $entidad !!} :input[name="fechainicial"]').val()+"&fechafinal="+$(IDFORMBUSQUEDA + '{!! $entidad !!} :input[name="fechafinal"]').val()+"&tipodocumento="+$(IDFORMBUSQUEDA + '{!! $entidad !!} :input[name="tipodocumento"]').val()+"&numero="+$(IDFORMBUSQUEDA + '{!! $entidad !!} :input[name="numero"]').val()+"&_token="+$(IDFORMBUSQUEDA + '{!! $entidad !!} :input[name="_token"]').val(),
        success: function(a) {
        	btn.button('reset');
        	alert("Procesado correctamente");
            buscar(entidad);
        }
    });
}

function resumen(entidad){
	var btn = $(IDFORMBUSQUEDA + '{!! $entidad !!} :input[id="btnResumen"]');
	btn.button('loading');
    $.ajax({
        type: "POST",
        url: "ventaadmision/resumen",
        data: "fechainicial="+$(IDFORMBUSQUEDA + '{!! $entidad !!} :input[name="fechainicial"]').val()+"&fechafinal="+$(IDFORMBUSQUEDA + '{!! $entidad !!} :input[name="fechafinal"]').val()+"&_token="+$(IDFORMBUSQUEDA + '{!! $entidad !!} :input[name="_token"]').val(),
        success: function(a) {
        	btn.button('reset');
        	alert("Enviado correctamente");
            buscar(entidad);
        }
    });
}

function resumen1(entidad){
	var btn = $(IDFORMBUSQUEDA + '{!! $entidad !!} :input[id="btnResumen1"]');
	btn.button('loading');
    $.ajax({
        type: "POST",
        url: "ventaadmision/resumen1",
        data: "fechainicial="+$(IDFORMBUSQUEDA + '{!! $entidad !!} :input[name="fechainicial"]').val()+"&fechafinal="+$(IDFORMBUSQUEDA + '{!! $entidad !!} :input[name="fechafinal"]').val()+"&_token="+$(IDFORMBUSQUEDA + '{!! $entidad !!} :input[name="_token"]').val(),
        success: function(a) {
        	btn.button('reset');
        	alert("Enviado correctamente");
            buscar(entidad);
        }
    });
}


function excelConcar(entidad){
    window.open("ventaadmision/excelConcar?fechainicial="+$(IDFORMBUSQUEDA + '{!! $entidad !!} :input[name="fechainicial"]').val()+"&fechafinal="+$(IDFORMBUSQUEDA + '{!! $entidad !!} :input[name="fechafinal"]').val()+"&_token="+$(IDFORMBUSQUEDA + '{!! $entidad !!} :input[name="_token"]').val(),"_blank");
}
function excelVenta(entidad){
    window.open("ventaadmision/excelVenta?fechainicial="+$(IDFORMBUSQUEDA + '{!! $entidad !!} :input[name="fechainicial"]').val()+"&fechafinal="+$(IDFORMBUSQUEDA + '{!! $entidad !!} :input[name="fechafinal"]').val()+"&_token="+$(IDFORMBUSQUEDA + '{!! $entidad !!} :input[name="_token"]').val(),"_blank");
}
function excelVentaBizlink(entidad){
    window.open("ventaadmision/excelVentaBizlink?fechainicial="+$(IDFORMBUSQUEDA + '{!! $entidad !!} :input[name="fechainicial"]').val()+"&fechafinal="+$(IDFORMBUSQUEDA + '{!! $entidad !!} :input[name="fechafinal"]').val()+"&_token="+$(IDFORMBUSQUEDA + '{!! $entidad !!} :input[name="_token"]').val(),"_blank");
}
function excelSunat(entidad){
    window.open("ventaadmision/excelSunat?fechainicial="+$(IDFORMBUSQUEDA + '{!! $entidad !!} :input[name="fechainicial"]').val()+"&fechafinal="+$(IDFORMBUSQUEDA + '{!! $entidad !!} :input[name="fechafinal"]').val()+"&_token="+$(IDFORMBUSQUEDA + '{!! $entidad !!} :input[name="_token"]').val(),"_blank");
}
function excelVenta2(entidad){
    window.open("ventaadmision/excelVentaConvenio?fechainicial="+$(IDFORMBUSQUEDA + '{!! $entidad !!} :input[name="fechainicial"]').val()+"&fechafinal="+$(IDFORMBUSQUEDA + '{!! $entidad !!} :input[name="fechafinal"]').val()+"&_token="+$(IDFORMBUSQUEDA + '{!! $entidad !!} :input[name="_token"]').val(),"_blank");
}

/*function imprimirVenta(id){
    $.ajax({
        type: "POST",
        url: "venta/imprimirVenta",
        data: "id="+id+"&_token="+$(IDFORMBUSQUEDA + '{!! $entidad !!} :input[name="_token"]').val(),
        success: function(a) {
            console.log(a);
	    }
    });
}*/

function imprimirTicket(id){
	$.ajax({
        type: "POST",
        url: "http://localhost{{ ($sucursal_id==1?'':':81') }}/clifacturacion/controlador/contImprimir.php?funcion=ImprimirTicket",
        data: "id="+id+"&_token="+$(IDFORMBUSQUEDA + '{!! $entidad !!} :input[name="_token"]').val(),
        success: function(a) {
            console.log(a);
	    }
    });
}

function imprimirVenta(numero){
	$.ajax({
        type: "POST",
        url: "http://localhost{{ ($sucursal_id==1?'':':81') }}/clifacturacion/controlador/contImprimir.php?funcion=ImprimirVenta",
        data: "numero="+numero+"&_token="+$(IDFORMBUSQUEDA + '{!! $entidad !!} :input[name="_token"]').val(),
        success: function(a) {
            console.log(a);
	    }
    });
}

function verPDF(numero){
    window.open("../clifacturacion/controlador/contComprobante.php?funcion=generarPDF&numero="+numero+"&empresa={{ ($sucursal_id==1?'2':'1') }}&_token="+$(IDFORMBUSQUEDA + '{!! $entidad !!} :input[name="_token"]').val(),"_blank");
}

function declarar2(idventa,idtipodocumento){
	if(idtipodocumento==5){
		var funcion="enviarBoleta";
	}else{
		var funcion="enviarFactura";
	}
	$.ajax({
        type: "GET",
        url: "../clifacturacion/controlador/contComprobante.php?funcion="+funcion,
        data: "idventa="+idventa+"&empresa={{ ($sucursal_id==1?'2':'1') }}&_token="+$(IDFORMBUSQUEDA + '{!! $entidad !!} :input[name="_token"]').val(),
        success: function(a) {
            console.log(a);
	    }
    });	
}
</script>