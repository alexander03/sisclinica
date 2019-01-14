<?php
$entidad='Producto';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{ config('app.name', 'SIGHO') }}</title>
    <link rel="icon" href="{{ asset('dist/img/user2-160x160.jpg') }}" sizes="16x16 32x32 48x48 64x64" type="image/vnd.microsoft.icon">
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Bootstrap 3.3.6 -->
    {!! Html::style('bootstrap/css/bootstrap.min.css') !!}
    <!-- Font Awesome -->
    {!! Html::style('dist/css/font-awesome.min.css') !!}
    <!-- Ionicons -->
    {!! Html::style('dist/css/ionicons.min.css') !!}
    <!-- DataTables -->
    {!! Html::style('plugins/datatables/dataTables.bootstrap.css') !!}
    <!-- Theme style -->
    {!! Html::style('dist/css/AdminLTE.min.css') !!}
    {!! Html::style('css/custom.css') !!}
    <!-- AdminLTE Skins. Choose a skin from the css/skins
    folder instead of downloading all of them to reduce the load. -->
    {!! Html::style('dist/css/skins/_all-skins.min.css') !!}
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]-->
    {!! Html::script('dist/js/html5shiv.min.js') !!}
    {!! Html::script('dist/js/respond.min.js') !!}
    <!--[endif]-->
    {{-- bootstrap-datetimepicker: para calendarios --}}
    {!! HTML::style('dist/css/bootstrap-datetimepicker.min.css', array('media' => 'screen')) !!}

    {{-- typeahead.js-bootstrap: para autocompletar --}}
    {!! HTML::style('dist/css/typeahead.js-bootstrap.css', array('media' => 'screen')) !!}

</head>
<body class="hold-transition skin-blue sidebar-mini">
	<form action="#" id="formHistoriaClinica">
		{!! Form::hidden('historia_id', '', array('id' => 'historia_id')) !!}
		{!! Form::hidden('ticket_id', '', array('id' => 'ticket_id')) !!}
	    <div class="wrapper">
			<!-- Content Header (Page header) -->
			<section class="content-header">
				<h1>
					VISTA MEDICO
					{{-- <small>Descripción</small> --}}
				</h1>
			</section>
			<ul class="nav nav-tabs">
			  <li><a data-toggle="tab" href="#Farmacia">Farmacia</a></li>
			  <li><a data-toggle="tab" href="#cie">CIE 10</a></li>
			  <li class="active"><a data-toggle="tab" href="#cola" id="pestanaPacienteCola">Pacientes en cola</a></li>
			  <li style="" id="pestanaAtencion"><a data-toggle="tab" href="#atencion">Atención de Paciente</a></li>
			</ul>
			<div class="tab-content">
	  			<div id="Farmacia" class="tab-pane fade">
					<!-- Main content -->
					<section class="content">
						<div class="row">
							<div class="col-xs-12">
								<div class="box">
									<div class="box-header">
										<div class="row">
											<div class="col-xs-12">
												{!! Form::open(['method' => 'POST' ,'onsubmit' => 'return false;', 'class' => 'form-inline', 'role' => 'form', 'autocomplete' => 'off', 'id' => 'formBusqueda'.$entidad]) !!}
												{!! Form::hidden('page', 1, array('id' => 'page')) !!}
												{!! Form::hidden('accion', 'listar', array('id' => 'accion')) !!}
												<div class="form-group">
													{!! Form::label('nombre', 'Nombre:') !!}
													{!! Form::text('nombre', '', array('class' => 'form-control input-xs', 'id' => 'nombre')) !!}
												</div>
												{!! Form::button('<i class="glyphicon glyphicon-search"></i> Buscar', array('class' => 'btn btn-success btn-xs', 'id' => 'btnBuscar', 'onclick' => 'buscar2(\''.$entidad.'\')')) !!}
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
				</div>
				<div id="cie" class="tab-pane fade">
					<!-- Main content -->
					<section class="content">
						<div class="row">
							<div class="col-xs-12">
								<div class="box">
									<div class="box-header">
										<div class="row">
											<div class="col-xs-12">
												{!! Form::open(['method' => 'POST' ,'onsubmit' => 'return false;', 'class' => 'form-inline', 'role' => 'form', 'autocomplete' => 'off', 'id' => 'formBusqueda'.$entidad]) !!}
												<div class="form-group">
													{!! Form::label('cie10', 'Cie10:') !!}
													{!! Form::text('cie10', '', array('class' => 'form-control input-xs', 'id' => 'cie10')) !!}
												</div>
												{!! Form::button('<i class="glyphicon glyphicon-search"></i> Buscar', array('class' => 'btn btn-success btn-xs', 'id' => 'btnBuscar2', 'onclick' => 'buscar3(\''.$entidad.'\')')) !!}
												{!! Form::close() !!}
											</div>
										</div>
									</div>
									<!-- /.box-header -->
									<div class="box-body" id="listado2{{ $entidad }}">
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
				</div>

				<div id="cola" class="tab-pane fade in active">
					<!-- Main content -->
					<section class="content">
						<div class="row">
							<div class="col-xs-12">
								<div class="box">
									<div class="box-header">
										<div class="row">
											<div class="col-xs-8">
												<div class="box-body" id="listado">
												</div>
											</div>
											<div class="col-xs-4">
												<strong>SIGUIENTE PACIENTE: </strong>
												<div class="box-body" id="atender">
												</div>
											</div>
										</div>
									</div>
									<!-- /.box-header -->
									<div class="box-body" id="listado2{{ $entidad }}">
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
				</div>

				<div id="atencion" class="tab-pane fade">
					<!-- Main content -->
					<section class="content">
						<div class="row">
							<div class="col-xs-12">
								<div class="box">
									<div class="box-header">
										<div class="row">
											<div class="col-xs-12">
												{!! Form::open(['method' => 'POST' ,'onsubmit' => 'return false;', 'class' => 'form-horizontal', 'role' => 'form', 'autocomplete' => 'off', 'id' => 'formBusqueda'.$entidad]) !!}
												<div class="col-sm-4">
													<?php
													$hoy = date("Y-m-d");
													?>
													<div class="form-group">
														{!! Form::label('fecha', 'Fecha:', array('class' => 'col-sm-2 control-label')) !!}
														<div class="col-sm-5">
															{!! Form::date('fecha', $hoy, array('class' => 'form-control input-xs col-sm-3', 'id' => 'fecha')) !!}
														</div>
													</div>
													<div class="form-group">
														{!! Form::label('paciente', 'Paciente:', array('class' => 'col-sm-2 control-label')) !!}
														<div class="col-sm-10">
															{!! Form::text('paciente', '', array('class' => 'form-control input-xs', 'id' => 'paciente')) !!}
														</div>
													</div>
													<div class="form-group">
														{!! Form::label('historia', 'Historia:', array('class' => 'col-sm-2 control-label')) !!}
														<div class="col-sm-5">
															{!! Form::text('historia', '', array('class' => 'form-control input-xs', 'id' => 'historia')) !!}
														</div>
													</div>
													<div class="form-group">
														{!! Form::label('cie102', 'Cie10:', array('class' => 'col-sm-2 control-label')) !!}
														<div class="col-sm-5">
															{!! Form::text('cie102', '', array('class' => 'form-control input-xs', 'id' => 'cie102')) !!}
														</div>
													</div>
													{!! Form::button('<i class="glyphicon glyphicon-check"></i> Guardar', array('class' => 'btn btn-success btn-sm', 'id' => 'btnBuscar2', 'onclick' => 'buscar3(\''.$entidad.'\')')) !!}
												</div>
												<div class="col-sm-4">
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
												<div class="col-sm-4">
													<!-- Lista de historias clinicas anteriores -->
													<strong>LISTA DE CITAS ANTERIORES:</strong>
													<!-- Fin historias clinicas anteriores -->	
												</div>
												
												{!! Form::close() !!}
											</div>
										</div>
									</div>
									<!-- /.box-header -->
									<div class="box-body" id="listado2{{ $entidad }}">
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
				</div>
				
	        </div>
	        <!-- /.content-wrapper -->
	        <footer class="navbar-default navbar-fixed-bottom" style="padding-left: 20px !important; padding-bottom: 20px; padding-top: 20px; padding-right: 20px;">
	            <div class="container-fluid">
	    			<div class="pull-right hidden-xs">
	    				<b>Version</b> 2.3.8
	    			</div>
	    			<strong>Copyright © 2018 <a href="#">GARZATEC</a>.</strong> All rights
	    			reserved.
	            </div>
			</footer>
	    </div>
	</form>
    <!-- ./wrapper -->
    <!-- jQuery 2.2.3 -->
    {!! Html::script('plugins/jQuery/jquery-2.2.3.min.js') !!}
    <!-- Bootstrap 3.3.6 -->
    {!! Html::script('bootstrap/js/bootstrap.min.js') !!}
    {!! Html::script('plugins/datatables/jquery.dataTables.min.js') !!}
    {!! Html::script('plugins/datatables/dataTables.bootstrap.min.js') !!}
    <!-- Slimscroll -->
    {!! Html::script('plugins/slimScroll/jquery.slimscroll.min.js') !!}
    <!-- FastClick -->
    {!! Html::script('plugins/fastclick/fastclick.js') !!}
    <!-- AdminLTE App -->
    {!! Html::script('dist/js/app.min.js') !!}
    <!-- AdminLTE for demo purposes -->
    {!! Html::script('dist/js/demo.js') !!}
    <!-- bootbox code -->
    {!! Html::script('dist/js/bootbox.min.js') !!}
    {{-- Funciones propias --}}
    {!! Html::script('dist/js/funciones.js') !!}
    {{-- jquery.inputmask: para mascaras en cajas de texto --}}
    {!! Html::script('plugins/input-mask/jquery.inputmask.js') !!}
    {!! Html::script('plugins/input-mask/jquery.inputmask.extensions.js') !!}
    {!! Html::script('plugins/input-mask/jquery.inputmask.date.extensions.js') !!}
    {!! Html::script('plugins/input-mask/jquery.inputmask.numeric.extensions.js') !!}
    {!! Html::script('plugins/input-mask/jquery.inputmask.phone.extensions.js') !!}
    {!! Html::script('plugins/input-mask/jquery.inputmask.regex.extensions.js') !!}
    {{-- bootstrap-datetimepicker: para calendarios --}}
    {!! HTML::script('dist/js/moment-with-locales.min.js') !!}
    {!! HTML::script('dist/js/bootstrap-datetimepicker.min.js') !!}
    {{-- typeahead.js-bootstrap: para autocompletar --}}
    {!! HTML::script('dist/js/typeahead.bundle.min.js') !!}
    {!! HTML::script('dist/js/bloodhound.min.js') !!}
    
</body>
</html>
<script>
	$(document).ready(function () {
		//buscar('{{ $entidad }}');
		$(IDFORMBUSQUEDA + '{{ $entidad }} :input[id="nombre"]').keyup(function (e) {
			var key = window.event ? e.keyCode : e.which;
			if (key == '13') {
				buscar2('{{ $entidad }}');
			}
		});
		$("#cie10").keyup(function (e) {
			var key = window.event ? e.keyCode : e.which;
			if (key == '13') {
				buscar3('{{ $entidad }}');
			}
		});
		buscar4();
		$('#pestanaAtencion').css('display', 'none');
	});
	function buscar2(){
		$.ajax({
	        type: "POST",
	        url: "producto/vistamedico",
	        data: "producto="+$(IDFORMBUSQUEDA + '{{ $entidad }} :input[id="nombre"]').val()+"&_token=<?php echo csrf_token(); ?>",
	        success: function(a) {
	        	$("#listado{{ $entidad }}").html(a);
	        }
	    });
	}
	function buscar3(){
		$.ajax({
	        type: "POST",
	        url: "producto/cie10",
	        data: "cie="+$("#cie10").val()+"&_token=<?php echo csrf_token(); ?>",
	        success: function(a) {
	        	$("#listado2{{ $entidad }}").html(a);
	        }
	    });
	}

	function buscar4(){
		$.ajax({
	        type: "POST",
	        url: "ventaadmision/cola",
	        data: "_token=<?php echo csrf_token(); ?>",
	        success: function(a) {
	        	$("#listado").html(a);
	        }
	    });
		$.ajax({
	        type: "POST",
	        url: "ventaadmision/llamarAtender",
	        data: "_token=<?php echo csrf_token(); ?>",
	        success: function(a) {
	        	$("#atender").html(a);
	        }
	    });
	}

	function registrarHistoriaClinica(){
		$.ajax({
	        type: "POST",
	        url: "historiaclinica/registrarHistoriaClinica",
	        data: $('#formHistoriaClinica').serialize() + "_token=<?php echo csrf_token(); ?>",
	        success: function(a) {
	        	// inhabilito pestaña $("#listado").html(a);
	        }
	    });
	}
    setInterval(buscar4, 1000);

    $(document).on('click', '.btnLlamarPaciente', function(event) {
    	event.preventDefault();
    	var paciente_id = $(this).data('paciente_id');
    	var ticket_id = $(this).data('ticket_id');
    	$.ajax({
	        type: "POST",
	        url: "historiaclinica/nuevaHistoriaClinica/" + paciente_id + "/" + ticket_id,
	        data: "_token=<?php echo csrf_token(); ?>",
	        dataType: "json",
	        success: function(a) {
	        	$("li").removeClass('in active');
	        	$('#Farmacia').removeClass('in active');
				$('#cie').removeClass('in active');
				$('#cola').removeClass('in active');
				$('#atencion').addClass('in active');
  				$("#pestanaAtencion").css('display', '').addClass('active');
  				$("#pestanaPacienteCola").removeClass('active');	
  				$('#historia_id').val(a.historia_id);
  				$('#ticket_id').val(a.ticket_id);
  				$('#historia').val(a.numhistoria);
  				$('#paciente').val(a.paciente);
	        }
	    });
    });
</script>