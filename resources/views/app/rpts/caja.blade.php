<?php 
	use Illuminate\Support\Facades\Session;
	$sucursal_id = Session::get('sucursal_id');
?>

<!-- Content Header (Page header) -->
<section class="content-header">
	<h1>
		{{ $title }}
		{{-- <small>Descripci�n</small> --}}
	</h1>
</section>

<!-- Main content -->
<section class="content">
	<div class="row">
		<div class="col-xs-12">
			<div class="box">
				<div class="box-header">
					{!! Form::open(['method' => 'POST' ,'onsubmit' => 'return false;', 'class' => 'form-inline', 'role' => 'form', 'autocomplete' => 'off', 'id' => 'formBusqueda'.$entidad]) !!}
					<div class="row">						
						<div class="col-xs-12">
							{!! Form::hidden('page', 1, array('id' => 'page')) !!}
							{!! Form::hidden('accion', 'listar', array('id' => 'accion')) !!}
						    
							<div class="form-group">
								{!! Form::label('fechainicial', 'Fecha Inicial:') !!}
								{!! Form::date('fechainicial', date('Y-m-d',strtotime("now",strtotime("-1 week"))), array('class' => 'form-control input-xs', 'id' => 'fechainicial')) !!}
							</div>
                            <div class="form-group">
								{!! Form::label('fechafinal', 'Fecha Final:') !!}
								{!! Form::date('fechafinal', date('Y-m-d'), array('class' => 'form-control input-xs', 'id' => 'fechafinal')) !!}
							</div>

							<div class="form-group" @if(Auth::user()->usertype_id != 1 && $user->usertype_id != 24 && $user->usertype_id != 2) style="display: none;" @endif id="cajas">
								
							</div>

							{!! Form::button('<i class="glyphicon glyphicon-file"></i> Consolidado PDF', array('class' => 'btn btn-danger btn-xs', 'onclick' => 'imprimirDetalleF(\'\')')) !!}

							{!! Form::button('<i class="glyphicon glyphicon-file"></i> Por cajas PDF', array('class' => 'btn btn-warning btn-xs', 'onclick' => 'imprimirDetalleF(\'2\')')) !!}

							@if($user->usertype_id==1 || $user->usertype_id==14 || $user->usertype_id==8 || $user->usertype_id==2)
								{{--{!! Form::button('<i class="glyphicon glyphicon-print"></i> Movilidad', array('class' => 'btn btn-warning btn-xs', 'onclick' => 'imprimirMovilidadF()')) !!}
								{! Form::button('<i class="glyphicon glyphicon-file"></i> Excel', array('class' => 'btn btn-success btn-xs', 'onclick' => 'imprimirExcelF()')) !!}--}}
								{!! Form::button('<i class="glyphicon glyphicon-file"></i> Egresos Excel', array('class' => 'btn btn-danger btn-xs', 'onclick' => 'egresosExcel()')) !!}
							@endif
							{!! Form::button('<i class="glyphicon glyphicon-file"></i> Consolidado Excel', array('class' => 'btn btn-success btn-xs','onclick' => 'pdfDetalleCierreExcelF(\'\')')) !!}
							{!! Form::button('<i class="glyphicon glyphicon-file"></i> Por cajas Excel', array('class' => 'btn btn-success btn-xs','onclick' => 'pdfDetalleCierreExcelF(\'2\')')) !!}
							@if($user->usertype_id==1 || $user->usertype_id==23|| $user->usertype_id==2)
								{!! Form::button('<i class="glyphicon glyphicon-file"></i> Detalle de Egresos', array('class' => 'btn btn-info btn-xs','onclick' => 'pdfDetalleEgresos()')) !!}
							@endif
							@if($user->usertype_id==1 || $user->usertype_id==11 || $user->usertype_id == 2)
								{!! Form::button('<i class="glyphicon glyphicon-print"></i> Ventas Por Producto Individual PDF', array('class' => 'btn btn-danger btn-xs', 'id' => 'btnBuscar', 'onclick' => 'detallePorProducto();')) !!}
								{!! Form::button('<i class="glyphicon glyphicon-print"></i> Ventas Por Producto Individual Excel', array('class' => 'btn btn-success btn-xs', 'id' => 'btnBuscar', 'onclick' => 'detallePorProductoF();')) !!}
								{!! Form::button('<i class="glyphicon glyphicon-print"></i> Ventas Por Producto Agrupado, Convenio y Particular', array('class' => 'btn btn-info btn-xs', 'id' => 'btnBuscar', 'onclick' => 'detallePorProductoAgrupado();')) !!}				
							@endif
							@if($user->usertype_id==1 || $user->usertype_id == 2)
								{!! Form::button('<i class="glyphicon glyphicon-list"></i> Resumen de Atenciones Excel', array('class' => 'btn btn-success btn-xs', 'id' => 'btnBuscar', 'onclick' => 'detalleResumenAtenciones();')) !!}				
							@endif
							<!--
							{! Form::button('<i class="glyphicon glyphicon-file"></i> Exportar Excel', array('class' => 'btn btn-success btn-xs','onclick' => 'pdfDetalleCierreExcelF()')) !!}
							-->							
						</div>
					</div>
					@if($user->usertype_id==1 || $user->usertype_id==2 || $user->usertype_id==11 || $user->usertype_id==24)
					<hr>
					<div class="col-xs-12">		
						<div class="form-group">
							{!! Form::label('prodcto', 'Producto:') !!}
						</div>	
						<div class="form-group">
							<select name="prodcto" id="prodcto" class='form-control input-xs'>
								<option value="">------------Todos------------</option>
								@if(count($productos)>0)
									@foreach($productos as $key => $producto)
										<option value="{{ $producto->id }}">{{ $producto->nombre }}</option>
									@endforeach
								@endif
							</select>
						</div>
						<div class="form-group" @if(Auth::user()->usertype_id != 1 && $user->usertype_id != 24 && $user->usertype_id != 2) style="display: none;" @endif>
							<select name="almacen__id" id="almacen__id" class='form-control input-sm'>
								@if($sucursal_id==1)
								<option value="1">FARMACIA BMOJOS</option>
								<option value="2">LOGÍSTICA BMOJOS</option>
								@elseif($sucursal_id==2)
								<option value="3">FARMACIA ESPECIALIDADES</option>
								<option value="4">LOGÍSTICA ESPECIALIDADES</option>
								@endif
							</select>
						</div>					
						{!! Form::button('<i class="glyphicon glyphicon-print"></i> Por Lote, Stock, F.Vencimiento', array('class' => 'btn btn-warning btn-xs', 'id' => 'btnBuscar', 'onclick' => 'pdfDetallePorLoteStockFV();')) !!}
					</div>
					@endif
					{!! Form::close() !!}
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
	$(document).ready(function($) {
		$('#prodcto').chosen({
			width: '100%'
		});
	});
	function cajas(){
		$.ajax({
			type:'GET',
			url:"rpts/cajas",
			data:'',
			success: function(a) {
				$('#cajas').html(a);
			}
		});
	}

	cajas();

	function imprimirDetalleF(tipo){
		var fi = $('#fechainicial').val();
		var ff = $('#fechafinal').val();
        if ($('#Medico').val() != 6 && $('#Medico').val() != 7) {
        	window.open('caja/pdfDetalleCierreF' + tipo + '?caja_id='+$('#Medico').val()+'&fi='+fi+'&ff='+ff,"_blank");
        } else {
        	@if($user->usertype_id==1 || $user->usertype_id==14 || $user->usertype_id==8)
        		window.open('cajatesoreria/pdfDetalleCierreF' + tipo + '?caja_id='+$('#Medico').val()+'&fi='+fi+'&ff='+ff,"_blank");
        	@endif
        }
    }

    function detalleResumenAtenciones() {
    	var fi = $('#fechainicial').val();
		var ff = $('#fechafinal').val();
		window.open('historiaclinica/pdfDetalleResumenAtenciones?fi='+fi+'&ff='+ff,"_blank");
    }

	@if($user->usertype_id==1 || $user->usertype_id==11 || $user->usertype_id==24 || $user->usertype_id==2)
		
	function detallePorProducto(){
		var fi = $('#fechainicial').val();
		var ff = $('#fechafinal').val();
		window.open('caja/pdfDetallePorProducto?caja_id='+$('#Medico').val()+'&fi='+fi+'&ff='+ff,"_blank");
	}

	function detallePorProductoF(){
		var fi = $('#fechainicial').val();
		var ff = $('#fechafinal').val();
		window.open('caja/pdfDetallePorProductoF?caja_id='+$('#Medico').val()+'&fi='+fi+'&ff='+ff,"_blank");
	}

	function detallePorProductoAgrupado(){
		var fi = $('#fechainicial').val();
		var ff = $('#fechafinal').val();
		window.open('caja/pdfDetallePorProductoAgrupado?caja_id='+$('#Medico').val()+'&fi='+fi+'&ff='+ff,"_blank");
	}
	
	function pdfDetallePorLoteStockFV(){
		var fi = $('#fechainicial').val();
		var ff = $('#fechafinal').val();
		var almacen_id = $('#almacen__id').val();
		var producto_id = $('#prodcto').val();
		window.open('caja/pdfDetallePorLoteStockFV?caja_id='+$('#Medico').val()+'&fi='+fi+'&ff='+ff+'&producto_id='+producto_id+'&almacen_id='+almacen_id,"_blank");
	}

	@endif

    function imprimirMovilidadF(){
		var fi = $('#fechainicial').val();
		var ff = $('#fechafinal').val();
        if ($('#Medico').val() != 6) {
        	//window.open('caja/pdfDetalleCierreF?caja_id='+$('#Medico').val()+'&fi='+fi+'&ff='+ff,"_blank");
        } else {
        	window.open('cajatesoreria/pdfMovilidadF?caja_id='+$('#Medico').val()+'&fi='+fi+'&ff='+ff,"_blank");
        }
    }

	function pdfDetalleCierreExcelF(tipo){
		var fi = $('#fechainicial').val();
		var ff = $('#fechafinal').val();
        //if ($('#Medico').val() != 6) {
        	//window.open('caja/pdfDetalleCierreF?caja_id='+$('#Medico').val()+'&fi='+fi+'&ff='+ff,"_blank");
        //} else {
        	window.open('caja/pdfDetalleCierreExcelF' + tipo + '?caja_id='+$('#Medico').val()+'&fi='+fi+'&ff='+ff,"_blank");
        //}
    }

    function pdfDetalleEgresos(){
		var fi = $('#fechainicial').val();
		var ff = $('#fechafinal').val();
        window.open('caja/pdfDetalleEgresos?caja_id='+$('#Medico').val()+'&fi='+fi+'&ff='+ff,"_blank");
    }

    function egresosExcel(){
		var fi = $('#fechainicial').val();
		var ff = $('#fechafinal').val();
      	window.open('cajatesoreria/egresosExcel?caja_id='+$('#Medico').val()+'&fi='+fi+'&ff='+ff,"_blank");
    }

	function Genera(){
		var fi = $('#fechainicial').val();
		var ff = $('#fechafinal').val();
		if (ff != "") {
			var med = '';
			if ($('#Medico').val() != null) {
				med = '&med='+$('#Medico').val();
			}
			var link = 'reporte.php?rep=6&fi='+fi+'&ff='+ff+''+med;
			var link2 = 'reporte.php?rep=61&fi='+fi+'&ff='+ff+'';
			if($('#Medico').val() != 4){
				window.open(link,'_blank');
			} else {
				window.open(link2,'_blank');
			}
		}
	}
</script>