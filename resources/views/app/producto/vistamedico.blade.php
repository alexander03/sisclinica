<?php
use Illuminate\Support\Facades\Auth;
$entidad='Producto';
date_default_timezone_set('America/Lima');
$fechahoy = date('j-m-Y');
$user = Auth::user();
?>
@if($user != null)
@if($user->usertype_id == 18 || $user->usertype_id == 1)
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
		{!! Form::hidden('doctor_id', '', array('id' => 'doctor_id')) !!}
		{!! Form::hidden('ticket_id', '', array('id' => 'ticket_id')) !!}
		{!! Form::hidden('fondo_si', '', array('id' => 'fondo_si')) !!}
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
			  <li class="active" id="pestanaPacienteCola"><a data-toggle="tab" href="#cola">Pacientes en cola</a></li>
			  <li><a data-toggle="tab" href="#atendidos">Atenciones del día</a></li>
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
												<div class="form-inline">
													{!! Form::hidden('page', 1, array('id' => 'page')) !!}
													{!! Form::hidden('accion', 'listar', array('id' => 'accion')) !!}
													<div class="form-group">
														{!! Form::label('nombrep', 'Nombre:') !!}
														{!! Form::text('nombrep', '', array('class' => 'form-control input-xs', 'id' => 'nombrep')) !!}
													</div>
													{!! Form::button('<i class="glyphicon glyphicon-search"></i> Buscar', array('class' => 'btn btn-success btn-xs', 'id' => 'btnBuscar', 'onclick' => 'buscar2(\''.$entidad.'\')')) !!}
												</div>
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
												<div class="form-inline">
													<div class="form-group">
														{!! Form::label('cie10', 'Cie10:') !!}
														{!! Form::text('cie10', '', array('class' => 'form-control input-xs', 'id' => 'cie10')) !!}
													</div>
													{!! Form::button('<i class="glyphicon glyphicon-search"></i> Buscar', array('class' => 'btn btn-success btn-xs', 'id' => 'btnBuscar2', 'onclick' => 'buscar3(\''.$entidad.'\')')) !!}
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

				<div id="cola" class="tab-pane fade in active">
					<!-- Main content -->
					<section class="content">
						<div class="row">
							<div class="col-xs-12">
								<div class="box">
									<div class="box-header">
										<div class="row">
											<div class="col-xs-8">
												<div class="line">
												
													<div class="col-sm-6">
														<h3 class='text-center' style='font-weight:bold;color:blue'>CONSULTAS</h3>
														<div style="margin:10px 0px; height: 250px; overflow-y: scroll;" id="listadoConsultas"></div>
													</div>
													<div class="col-sm-6">
														<h3 class='text-center' style='font-weight:bold;color:red'>EMERGENCIAS</h3>
														<div style="margin:10px 0px; height: 250px; overflow-y: scroll;" id="listadoEmergencias"></div>
													</div>
													<div class="col-sm-6">
														<h3 class='text-center' style='font-weight:bold;color:#3498DB'>FONDO DE OJOS</h3>
														<div style="margin:10px 0px; height: 250px; overflow-y: scroll;" id="listadoOjos"></div>
													</div>
													<div class="col-sm-6">
														<h3 class='text-center' style='font-weight:bold;color:green'>LECTURA DE RESULTADOS</h3>
														<div style="margin:10px 0px; height: 250px; overflow-y: scroll;" id="listadoLectura"></div>
													</div>
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
								</div>
								<!-- /.box -->
							</div>
							<!-- /.col -->
						</div>
						<!-- /.row -->
					</section>
					<!-- /.content -->	
				</div>

				<div id="atendidos" class="tab-pane fade">
					<!-- Main content -->
					<section class="content">
						<div class="row">
							<div class="col-xs-12">
								<div class="box">
									<div class="box-header">
										<div class="row">
											
											<div class="col-xs-12">
												<h3 class='text-center' style='font-weight:bold;color:blue'>ATENCIONES DEL DÍA</h3>
												<div id="tablaAtendidos">
												</div>
											</div>

										</div>
									</div>
									<!-- /.box-header -->
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
												<div class="form-horizontal">
													<div class="col-sm-4" style="font-size: 15px;">
														<div id="divpresente" style="margin:10px; padding:15px ; text-align:center;border-style:dotted;">
															<strong>¿El paciente está presente?</strong>
															{!! Form::button('<i class="glyphicon glyphicon-ok"></i> SI', array('class' => 'btn btn-success btn-sm', 'id' => 'btnSi', 'onclick' => 'presente("SI");')) !!}
															{!! Form::button('<i class="glyphicon glyphicon-remove"></i> NO', array('class' => 'btn btn-warning btn-sm', 'id' => 'btnNo', 'onclick' => 'presente("NO");')) !!}
														</div>
														<?php
														$hoy = date("Y-m-d");
														?>
														<div class="form-group">
															{!! Form::label('fecha', 'Fecha:', array('class' => 'col-sm-2 control-label')) !!}
															<div class="col-sm-5">
																{!! Form::date('fecha', $hoy, array('class' => 'form-control input-xs col-sm-3', 'id' => 'fecha', 'readonly' => 'readonly', 'style' => 'font-size: 16px;')) !!}
															</div>
														</div>
														<div class="form-group">
															{!! Form::label('paciente', 'Paciente:', array('class' => 'col-sm-2 control-label')) !!}
															<div class="col-sm-10">
																{!! Form::text('paciente', '', array('class' => 'form-control input-xs', 'id' => 'paciente', 'readonly' => 'readonly', 'style' => 'font-size: 14px;')) !!}
															</div>
														</div>
														<div class="form-group">
															{!! Form::label('doctor', 'Doctor:', array('class' => 'col-sm-2 control-label')) !!}
															<div class="col-sm-10">
																{!! Form::text('doctor', '', array('class' => 'form-control input-xs', 'id' => 'doctor', 'readonly' => 'readonly', 'style' => 'font-size: 14px;')) !!}
															</div>
														</div>
														<div class="form-group">
															{!! Form::label('historia', 'Historia:', array('class' => 'col-sm-2 control-label')) !!}
															<div class="col-sm-5">
																{!! Form::text('historia', '', array('class' => 'form-control input-xs', 'id' => 'historia', 'readonly' => 'readonly', 'style' => 'font-size: 16px;')) !!}
															</div>
														</div>
														<div class="form-group">
															{!! Form::label('numero', 'Tratam.:', array('class' => 'col-sm-2 control-label')) !!}
															<div class="col-sm-5">
																{!! Form::text('numero', '', array('class' => 'form-control input-xs', 'id' => 'numero', 'readonly' => 'readonly', 'style' => 'font-size: 16px;')) !!}
															</div>
														</div>
														<div class="form-group">
															{!! Form::label('cie102', 'Cie10:', array('class' => 'col-sm-2 control-label')) !!}
															<div class="col-sm-5">
																{!! Form::text('cie102', '', array('class' => 'form-control input-xs', 'id' => 'cie102', 'style' => 'font-size: 16px;')) !!}
															</div>
														</div>
														<div class="form-group">
															{!! Form::label('motivo', 'Motivo:', array('class' => 'col-sm-2 control-label')) !!}
															<div class="col-sm-10">
																<textarea class="form-control input-xs" id="motivo" cols="10" rows="2" style="font-size: 16px;"></textarea>
															</div>
														</div>	
														<div class="form-group">
															{!! Form::label('motivo', 'Fondo de ojos:', array('class' => 'col-sm-4 control-label')) !!}
															<input style="margin-top: 11px;" type="checkbox" id="fondo" value="1"><br>
														</div>		

														{!! Form::button('<i class="glyphicon glyphicon-check"></i> Guardar', array('class' => 'btn btn-success btn-sm', 'id' => 'btnGuardar', 'onclick' => 'registrarHistoriaClinica();')) !!}
														<h5 style="color: red; font-weight: bold;" id="mensajeHistoriaClinica"></h5>
													</div>
													<div class="col-sm-4" style="font-size: 15px;">
														<div class="form-group">
															{!! Form::label('sintomas', 'Sintomas:') !!}
															<textarea class="form-control input-xs" id="sintomas" cols="10" rows="3" style="font-size: 16px;"></textarea>
														</div>
														<div class="form-group">
															{!! Form::label('diagnostico', 'Diagnostico:') !!}
															<textarea class="form-control input-xs" id="diagnostico" cols="10" rows="3" style="font-size: 16px;"></textarea>
														</div>
														<div class="form-group">
															{!! Form::label('tratamiento', 'Tratamiento:') !!}
															<textarea class="form-control input-xs" id="tratamiento" cols="10" rows="3" style="font-size: 16px;"></textarea>
														</div>
														<div class="form-group">
															{!! Form::label('exploracion_fisica', 'Exploración Física:') !!}
															<textarea class="form-control input-xs" id="exploracion_fisica" cols="10" rows="3" style="font-size: 16px;"></textarea>
														</div>
														<div class="form-group">
															{!! Form::label('examenes', 'Exámenes:') !!}
															<textarea class="form-control input-xs" id="examenes" cols="10" rows="3" style="font-size: 16px;"></textarea>
														</div>												
													</div>
													<div class="col-sm-4">
														<!-- Lista de historias clinicas anteriores -->
														<strong>LISTA DE CITAS ANTERIORES:</strong>
														<div id="tablaCita">
														</div>
														<!-- Fin historias clinicas anteriores -->	
													</div>
												</div>
											</div>
										</div>
									</div>
									<!-- /.box-header -->
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

			<!-- Modal -->
			<div class="modal fade" id="exampleModal1" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
				<div class="modal-dialog" role="document">
				    <div class="modal-content">
					    <div class="modal-body" id="verCita"></div>
				        <div class="modal-footer">
				            <button type="button" class="btn btn-success" data-dismiss="modal">Cerrar</button>
				        </div>
				    </div>
				</div>
			</div>
			<!-- Modal -->
			<div class="modal fade" id="exampleModal2" tabindex="-1" role="dialog" aria-labelledby="tituloeditar" aria-hidden="true">
				<div class="modal-dialog modal-lg" role="document">
				    <div class="modal-content">
						<div class="modal-header">
							<h3 class="modal-title" id="tituloeditar">Editar Atención</h3>
						</div>
					    <div>
							<div class="form-horizontal">
								<input type="hidden" id="atencion_id" name="atencion_id">
								<div class="col-sm-12" style="font-size: 15px;margin-top:20px;">
									<div class="form-group col-sm-4">
										<label for="fechaeditar" class="col-sm-4 control-label">Fecha:</label>
										<div class="col-sm-8">
											<input class="form-control input-xs" id="fechaeditar" readonly style="font-size: 16px;"  name="fechaeditar" type="text">
										</div>
									</div>
									<div class="form-group col-sm-4">
										<label for="historiaeditar" class="col-sm-4 control-label">Historia:</label>
										<div class="col-sm-8">
											<input class="form-control input-xs" id="historiaeditar" readonly style="font-size: 16px;" name="historiaeditar" type="text">
										</div>
									</div>
									<div class="form-group col-sm-4">
										<label for="numeroeditar" class="col-sm-4 control-label">Tratam.:</label>
										<div class="col-sm-8">
											<input class="form-control input-xs" id="numeroeditar" readonly style="font-size: 16px;" name="numeroeditar" type="text">
										</div>
									</div>
								</div>
								<div class="col-sm-12" style="font-size: 15px;">
									<div class="form-group col-sm-6">
										<label for="pacienteeditar" class="col-sm-3 control-label">Paciente:</label>
										<div class="col-sm-9">
											<input class="form-control input-xs" id="pacienteeditar" readonly style="font-size: 14px;" name="pacienteeditar" type="text">
										</div>
									</div>
									<div class="form-group col-sm-6">
										<label for="doctoreditar" class="col-sm-3 control-label">Doctor:</label>
										<div class="col-sm-9">
											<input class="form-control input-xs" id="doctoreditar" readonly style="font-size: 14px;" name="doctoreditar" type="text">
										</div>
									</div>
								</div>
								<div class="col-sm-12" style="font-size: 15px;">
									<div class="form-group col-sm-4">
										<label for="cie10editar" class="col-sm-3 control-label">Cie10:</label>
										<div class="col-sm-5">
											<input class="form-control input-xs" id="cie10editar" readonly style="font-size: 16px;" name="cie10editar" type="text">
										</div>
									</div>
									<div class="form-group col-sm-4">
										<label for="fondoeditar" class="col-sm-9 control-label">Fondo de ojos:</label>
										<div class="col-sm-3">
											<input style="margin-top: 11px;" disabled type="checkbox" id="fondoeditar"><br>
										</div>
									</div>		
								</div>
								<div class="col-sm-6" style="font-size: 15px;">
									<div class="form-group" style="margin: 5px;">
										{!! Form::label('motivoeditar', 'Motivo:') !!}
										<textarea class="form-control input-xs" id="motivoeditar" cols="10" rows="2" style="font-size: 16px;"></textarea>
									</div>	
									<div class="form-group" style="margin: 5px;">
										{!! Form::label('sintomaseditar', 'Sintomas:') !!}
										<textarea class="form-control input-xs" id="sintomaseditar" cols="10" rows="3" style="font-size: 16px;"></textarea>
									</div>
									<div class="form-group" style="margin: 5px;">
										{!! Form::label('diagnosticoeditar', 'Diagnostico:') !!}
										<textarea class="form-control input-xs" id="diagnosticoeditar" cols="10" rows="3" style="font-size: 16px;"></textarea>
									</div>
								</div>
								<div class="col-sm-6" style="font-size: 15px;">
									<div class="form-group" style="margin: 5px;">
										{!! Form::label('tratamientoeditar', 'Tratamiento:') !!}
										<textarea class="form-control input-xs" id="tratamientoeditar" cols="10" rows="3" style="font-size: 16px;"></textarea>
									</div>
									<div class="form-group" style="margin: 5px;">
										{!! Form::label('exploracion_fisicaeditar', 'Exploración Física:') !!}
										<textarea class="form-control input-xs" id="exploracion_fisicaeditar" cols="10" rows="3" style="font-size: 16px;"></textarea>
									</div>
									<div class="form-group" style="margin: 5px;">
										{!! Form::label('exameneseditar', 'Exámenes:') !!}
										<textarea class="form-control input-xs" id="exameneseditar" cols="10" rows="3" style="font-size: 16px;"></textarea>
									</div>												
								</div>
							</div>

						</div>
				        <div class="modal-footer">
							<button type="button" id="btnGuardarEditar" class="btn btn-success" data-dismiss="modal"><i class="glyphicon glyphicon-check"></i> Guardar</button>
				            <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="glyphicon glyphicon-remove"></i> Cerrar</button>
				        </div>
				    </div>
				</div>
			</div>
	        <!-- /.content-wrapper -->
	        <footer class="navbar-default navbar-fixed-bottom" style="padding-left: 20px !important; padding-bottom: 20px; padding-top: 20px; padding-right: 20px;">
	            <div class="container-fluid">
	    			<div class="pull-right hidden-xs">
	    				<b>Version</b> 2.3.8
	    			</div>
	    			<strong>Copyright © 2018 <a href="#">GARZASOFT</a>.</strong> All rights
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
		$("#nombre").keyup(function (e) {
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
		tablaAtendidos();
		$('#pestanaAtencion').css('display', 'none');
		$("#cie102").prop('disabled', true);
		$("#sintomas").prop('disabled', true);
		$("#diagnostico").prop('disabled', true);
		$("#tratamiento").prop('disabled', true);
		$("#exploracion_fisica").prop('disabled', true);
		$("#examenes").prop('disabled', true);
		$("#motivo").prop('disabled', true);
		$("#btnGuardar").prop('disabled', true);
		$("#fondo").prop('disabled', true);
		$('#fondo').prop('checked', false);
	});
	function buscar2(){
		$.ajax({
	        type: "POST",
	        url: "producto/vistamedico",
	        data: "producto="+$("#nombrep").val()+"&_token=<?php echo csrf_token(); ?>",
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
                url: "ventaadmision/colamedico",
                data: "_token=<?php echo csrf_token(); ?>",
                dataType: 'json',
                success: function(a) {
                    $("#listadoConsultas").html(a.consultas);
                    $("#listadoEmergencias").html(a.emergencias);
                    $("#listadoOjos").html(a.ojos);
                    $("#listadoLectura").html(a.lectura);
                }
            });
		$('.llamando').fadeTo(500, .1).fadeTo(500, 1) ;
		$.ajax({
	        type: "POST",
	        url: "ventaadmision/llamarAtender",
	        data: "_token=<?php echo csrf_token(); ?>",
	        success: function(a) {
	        	$("#atender").html(a);
	        }
	    });
	}	
    setInterval(buscar4, 1000);
	
	function tablaAtendidos(){
		$.ajax({
			"method": "POST",
			"url": "{{ url('/historiaclinica/tablaAtendidos') }}",
			"data": {
				"_token": "{{ csrf_token() }}",
				}
		}).done(function(info){
			$('#tablaAtendidos').html(info);
		});
	}	

	function tablaCita(historia_id){
		$.ajax({
			"method": "POST",
			"url": "{{ url('/historiaclinica/tablaCita') }}",
			"data": {
				"historia_id" : historia_id, 
				"_token": "{{ csrf_token() }}",
				}
		}).done(function(info){
			$('#tablaCita').html(info);
		});
	}	

    $(document).on('click', '.btnLlamarPaciente', function(event) {
    	event.preventDefault();
    	var paciente_id = $(this).data('paciente_id');
    	var ticket_id = $(this).data('ticket_id');
		
		$.ajax({
			"method": "POST",
			"url": "{{ url('/ventaadmision/colamedico') }}",
			"data": {
				"ticket_id" : ticket_id, 
				"_token": "{{ csrf_token() }}",
				}
		});

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
				tablaCita(a.historia_id);
  				$('#ticket_id').val(a.ticket_id);
				$('#doctor_id').val(a.doctor_id);
				$('#doctor').val(a.doctor);
  				$('#historia').val(a.numhistoria);
  				$('#paciente').val(a.paciente);
  				$('#numero').val(a.numero);
				if(a.fondo == "SI"){
					$('#fondo').prop('checked', false);
					$('#fondo_si').val(a.fondo);
					$("#cie102").prop('readOnly', true);
					$("#motivo").prop('readOnly', true);
				}else{
					$('#fondo').prop('checked', false);
					$('#fondo_si').val(a.fondo);
					$("#cie102").prop('readOnly', false);
					$("#motivo").prop('readOnly', false);
				}
				$('#cie102').val(a.cie10);
  				$('#cie102').focus();
				$('#motivo').val(a.motivo);
				$('#sintomas').val(a.sintomas);
				$('#tratamiento').val(a.tratamiento);
				$('#diagnostico').val(a.diagnostico);
				$('#exploracion_fisica').val(a.exploracion_fisica);
				$('#examenes').val(a.examenes);
	        }
	    });
    });

    function registrarHistoriaClinica(){
    	if($('#cie102').val() == '') {
    		$('#cie102').focus();
    		$('#mensajeHistoriaClinica').html('Debes ingresar un CIE 10.');
    		return 0;
    	}
    	if($('#sintomas').val() == '') {
    		$('#sintomas').focus();
    		$('#mensajeHistoriaClinica').html('Debes ingresar síntomas.');
    		return 0;
    	}
    	if($('#diagnostico').val() == '') {
    		$('#diagnostico').focus();
    		$('#mensajeHistoriaClinica').html('Debes ingresar un diagnostico.');
    		return 0;
    	}
    	if($('#tratamiento').val() == '') {
    		$('#tratamiento').focus();
    		$('#mensajeHistoriaClinica').html('Debes ingresar un tratamiento.');
    		return 0;
    	}
		if($('#examenes').val() == '') {
    		$('#examenes').focus();
    		$('#mensajeHistoriaClinica').html('Debes ingresar exámenes.');
    		return 0;
    	}
    	if($('#motivo').val() == '') {
    		$('#motivo').focus();
    		$('#mensajeHistoriaClinica').html('Debes ingresar un motivo.');
    		return 0;
    	}
    	if($('#exploracion_fisica').val() == '') {
    		$('#exploracion_fisica').focus();
    		$('#mensajeHistoriaClinica').html('Debes ingresar exploración física.');
    		return 0;
    	}
    	var tratamiento = $('#tratamiento').val().replace(/\r?\n/g, "<br>");
    	var sintomas = $('#sintomas').val().replace(/\r?\n/g, "<br>");
    	var diagnostico = $('#diagnostico').val().replace(/\r?\n/g, "<br>");
		var examenes = $('#examenes').val().replace(/\r?\n/g, "<br>");
    	var motivo = $('#motivo').val().replace(/\r?\n/g, "<br>");
    	var exploracion_fisica = $('#exploracion_fisica').val().replace(/\r?\n/g, "<br>");
		var fondo = "NO";
		if( $('#fondo').prop('checked') ){
			fondo = "SI";
		}
		var ticket_id = $(this).data('ticket_id');
		var doctor_id = $('#doctor_id').val();
		$.ajax({
	        type: "POST",
	        url: "historiaclinica/registrarHistoriaClinica",
	        data: $('#formHistoriaClinica').serialize() + "&_token=<?php echo csrf_token(); ?>&tratamiento=" + tratamiento + "&sintomas=" + sintomas + "&diagnostico=" + diagnostico + "&examenes=" + examenes + "&motivo=" + motivo + "&exploracion_fisica=" + exploracion_fisica + "&fondo=" + fondo + "&doctor_id=" + doctor_id,
	        success: function(a) {
	        	if(a == 'El Código CIE no existe') {
	        		$('#mensajeHistoriaClinica').html(a);
	        		$('#cie102').focus();
	        	}
	        	if(a == 'OK') {
	        		alert('TRATAMIENTO REGISTRADO CORRECTAMENTE...');
	        		$("li").removeClass('in active');
		        	$('#Farmacia').removeClass('in active');
					$('#cie').removeClass('in active');
					$('#cola').addClass('in active');
					$('#atencion').removeClass('in active');
	  				$("#pestanaAtencion").css('display', 'none').removeClass('active');
	  				$("#pestanaPacienteCola").addClass('active');	
	  				$('#cie102').val('');
	  				$('#tratamiento').val('');
	  				$('#sintomas').val('');
	  				$('#diagnostico').val('');
					$('#examenes').val('');
					$('#motivo').val('');
					$('#doctor').val('');
					$('#doctor_id').val('');
					$('#exploracion_fisica').val('');
					$("#divpresente").css('display','');
					$("#cie102").prop('disabled', true);
					$("#sintomas").prop('disabled', true);
					$("#diagnostico").prop('disabled', true);
					$("#tratamiento").prop('disabled', true);
					$("#exploracion_fisica").prop('disabled', true);
					$("#examenes").prop('disabled', true);
					$("#motivo").prop('disabled', true);
					$("#btnGuardar").prop('disabled', true);
					$("#fondo").prop('disabled', true);
					$('#fondo').prop('checked', false);
					tablaAtendidos();
	        	}
	        }
	    });
	}

	function ver(cita_id){
		$.ajax({
			"method": "POST",
			"url": "{{ url('/historiaclinica/ver') }}",
			"data": {
				"cita_id" : cita_id, 
				"_token": "{{ csrf_token() }}",
				}
		}).done(function(info){
			$('#verCita').html(info);
		});
	}

	function editar(cita_id){
		$.ajax({
	        type: "POST",
	        url: "historiaclinica/editarCita",
	        data: "_token=<?php echo csrf_token(); ?>" + "&cita_id=" + cita_id,
	        dataType: "json",
	        success: function(a) {
				$('#atencion_id').val(a.atencion_id);
	        	$('#fechaeditar').val(a.fecha);
				$('#doctoreditar').val(a.doctor);
				$('#historiaeditar').val(a.numhistoria);
				$('#pacienteeditar').val(a.paciente);
				$('#numeroeditar').val(a.numero);
				$('#cie10editar').val(a.cie10);
				$('#motivoeditar').val(a.motivo);
				$('#sintomaseditar').val(a.sintomas);
				$('#tratamientoeditar').val(a.tratamiento);
				$('#diagnosticoeditar').val(a.diagnostico);
				$('#exploracion_fisicaeditar').val(a.exploracion_fisica);
				$('#exameneseditar').val(a.examenes);
				if(a.fondo == "SI"){
					$('#fondoeditar').prop('checked', true);
				}else{
					$('#fondoeditar').prop('checked', false);
				}
	        }
	    });
	}	

	$(document).on('click', '#btnGuardarEditar', function(event) {	

		var cita_id = $("#atencion_id").val();
		var tratamiento = $('#tratamientoeditar').val().replace(/\r?\n/g, "<br>");
    	var sintomas = $('#sintomaseditar').val().replace(/\r?\n/g, "<br>");
    	var diagnostico = $('#diagnosticoeditar').val().replace(/\r?\n/g, "<br>");
		var examenes = $('#exameneseditar').val().replace(/\r?\n/g, "<br>");
    	var motivo = $('#motivoeditar').val().replace(/\r?\n/g, "<br>");
    	var exploracion_fisica = $('#exploracion_fisicaeditar').val().replace(/\r?\n/g, "<br>");

		$.ajax({
			"method": "POST",
			"url": "{{ url('/historiaclinica/guardarEditado') }}",
			"data": {
				"cita_id" : cita_id, 
				"tratamiento" : tratamiento,
				"sintomas" : sintomas,
				"diagnostico" : diagnostico,
				"examenes" : examenes,
				"motivo" : motivo,
				"exploracion_fisica" : exploracion_fisica,
				"_token": "{{ csrf_token() }}",
				}
		}).done(function(info){
			if(info == 'OK') {
				alert('TRATAMIENTO REGISTRADO CORRECTAMENTE...');
			}
		});

	});

	function presente(estado){
		if(estado == "SI"){
			$("#cie102").prop('disabled', false);
			$("#sintomas").prop('disabled', false);
			$("#diagnostico").prop('disabled', false);
			$("#tratamiento").prop('disabled', false);
			$("#btnGuardar").prop('disabled', false);
			$("#exploracion_fisica").prop('disabled', false);
			$("#examenes").prop('disabled', false);
			$("#motivo").prop('disabled', false);
			//$("#divpresente").css('display','none');
			if( $('#fondo_si').val() == "SI" ){
				$("#fondo").prop('disabled', true);
				$('#fondo').prop('checked', false);
			}else{
				$("#fondo").prop('disabled', false);
				$('#fondo').prop('checked', false);
			}
		}else{
			$("#divpresente").css('display','');
			$("li").removeClass('in active');
			$('#Farmacia').removeClass('in active');
			$('#cie').removeClass('in active');
			$('#cola').addClass('in active');
			$('#atencion').removeClass('in active');
			$("#pestanaAtencion").css('display', 'none').removeClass('active');
			$("#pestanaPacienteCola").addClass('active');	
			$('#cie102').val('');
			$('#tratamiento').val('');
			$('#sintomas').val('');
			$('#diagnostico').val('');
			$('#examenes').val('');
			$('#motivo').val('');
			$('#exploracion_fisica').val('');
			$("#cie102").prop('disabled', true);
			$("#sintomas").prop('disabled', true);
			$("#diagnostico").prop('disabled', true);
			$("#tratamiento").prop('disabled', true);
			$("#exploracion_fisica").prop('disabled', true);
			$("#examenes").prop('disabled', true);
			$("#motivo").prop('disabled', true);
			$("#btnGuardar").prop('disabled', true);
			$("#fondo").prop('disabled', true);
			$('#fondo').prop('checked', false);
		}
		var ticket_id = $('#ticket_id').val();
		$.ajax({
			"method": "POST",
			"url": "{{ url('/ventaadmision/pacienteEstado') }}",
			"data": {
				"estado" : estado, 
				"ticket_id" : ticket_id,
				"_token": "{{ csrf_token() }}",
				}
		});
	}
</script>
@endif
@endif