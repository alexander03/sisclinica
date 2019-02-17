<?php
use Illuminate\Support\Facades\Auth;
$entidad='Producto';
use App\Tiposervicio;
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
		{!! Form::hidden('pantalla', '', array('id' => 'pantalla')) !!}
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
			  <li><a data-toggle="tab" href="#Tarifario">Tarifario</a></li>
			  <li><a data-toggle="tab" href="#Historias">Historias</a></li>
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


				<div id="Tarifario" class="tab-pane fade">
					<!-- Main content -->
					<section class="content">
						<div class="row">
							<div class="col-xs-12">
								<div class="box">
									<div class="box-header">
										<div class="row">
											<div class="col-xs-12">
												<div class="form-inline">
												<?php
													$cboTipoPago     = array("Particular" => "Particular", "Convenio" => "Convenio", "ParticularDescuento" => "Particular con Descuento");													
													$cboTipoServicio = array();
													$cboTipoServicio = $cboTipoServicio + array(0 => '--Todos--');
													$tiposervicio = Tiposervicio::where(DB::raw('1'),'=','1')->orderBy('nombre','ASC')->get();
													foreach ($tiposervicio as $key => $value) {
														$cboTipoServicio = $cboTipoServicio + array($value->id => $value->nombre);
													}
												?>
												{!! Form::hidden('page', 1, array('id' => 'page')) !!}
												{!! Form::hidden('accion', 'listar', array('id' => 'accion')) !!}
												<div class="form-group">
													{!! Form::label('tipopago', 'Tipo Pago:') !!}
													{!! Form::select('tipopago', $cboTipoPago, null, array('class' => 'form-control input-xs', 'id' => 'tipopago' , 'onchange' => 'buscarServicio();' )) !!}
												</div>
												<div class="form-group">
													{!! Form::label('nombre_servicio', 'Nombre:') !!}
													{!! Form::text('nombre_servicio', '', array('class' => 'form-control input-xs', 'id' => 'nombre_servicio')) !!}
												</div>
												<div class="form-group">
													{!! Form::label('tiposervicio', 'Tipo:') !!}
													{!! Form::select('tiposervicio', $cboTipoServicio, null, array('class' => 'form-control input-xs', 'id' => 'tiposervicio' , 'onchange' => 'buscarServicio();' )) !!}
												</div>
												<div class="form-group">
													{!! Form::label('filas', 'Filas a mostrar:')!!}
													{!! Form::selectRange('filas', 1, 30, 20, array('class' => 'form-control input-xs', 'onchange' => 'buscar(\''.$entidad.'\')')) !!}
												</div>
												{!! Form::button('<i class="glyphicon glyphicon-search"></i> Buscar', array('class' => 'btn btn-success btn-xs', 'id' => 'btnBuscar', 'onclick' => 'buscarServicio();')) !!}

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




				<div id="Historias" class="tab-pane fade">
					<!-- Main content -->
					<section class="content">
						<div class="row">
							<div class="col-xs-12">
								<div class="box">
									<div class="box-header">
										<div class="row">
											<div class="col-xs-12">
												<div class="form-inline">
													<?php

													$cboTipoPaciente  = array("" => "Todos","Particular" => "Particular", "Convenio" => "Convenio", "Hospital" => "Hospital");

													?>

													{!! Form::hidden('pageh', 1, array('id' => 'pageh')) !!}
													{!! Form::hidden('accionh', 'listar', array('id' => 'accionh')) !!}
													<div class="form-group">
														{!! Form::label('nombreh', 'Apellidos y Nombres:') !!}
														{!! Form::text('nombreh', '', array('class' => 'form-control input-xs', 'id' => 'nombreh')) !!}
													</div>
													<div class="form-group">
														{!! Form::label('dni', 'DNI:') !!}
														{!! Form::text('dni', '', array('class' => 'form-control input-xs', 'id' => 'dni')) !!}
													</div>
													<div class="form-group">
														{!! Form::label('numeroh', 'Historia:') !!}
														{!! Form::text('numeroh', '', array('class' => 'form-control input-xs', 'id' => 'numeroh')) !!}
													</div>
													<div class="form-group">
														{!! Form::label('tipopaciente', 'Tipo Paciente:') !!}
														{!! Form::select('tipopaciente', $cboTipoPaciente, null, array('class' => 'form-control input-xs', 'id' => 'tipopaciente','onchange' =>'buscarHistoria();')) !!}
													</div>
													<div class="form-group">
														{!! Form::label('filash', 'Filas a mostrar:')!!}
														{!! Form::selectRange('filash', 1, 30, 20, array('class' => 'form-control input-xs', 'onchange' => 'buscarHistoria();')) !!}
													</div>

													{!! Form::button('<i class="glyphicon glyphicon-search"></i> Buscar', array('class' => 'btn btn-success btn-xs', 'id' => 'btnBuscar', 'onclick' => 'buscarHistoria();')) !!}



												</div>
											</div>
										</div>
									</div>
									<!-- /.box-header -->
									<div class="box-body" id="listadoh{{ $entidad }}">
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
									<div class="box-body" id="listado3{{ $entidad }}">
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
												<strong>BUSCAR PACIENTE: </strong>
												<div class="col-sm-12" style="margin-top:15px;">
													<div class=" col-sm-9">
														{!! Form::text('buscarPaciente', '', array('class' => 'form-control input-xs', 'id' => 'buscarPaciente', 'style' => 'font-size: 16px;', 'placeholder' => 'Ingrese paciente',  'onkeyup' => 'if(event.keyCode == 13) llamarPacienteNombre();')) !!}
													</div>
													{!! Form::button('<i class="glyphicon glyphicon-search"></i> Buscar', array('class' => 'btn-xs btn btn-success btn-sm col-sm-3', 'id' => 'btnBuscarPaciente', 'onclick' => 'llamarPacienteNombre();')) !!}
												</div>
												<div class="box-body" id="resultadoBusquedaPaciente" style="margin-top:40px;">
												<h5 style="color: red; font-weight: bold;" id="mensajeBusquedaPaciente"></h5>
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


											<div class="form-group col-sm-4">
												{!! Form::label('nombre_atendido', 'Nombre:') !!}
												{!! Form::text('nombre_atendido', '', array('class' => 'form-control input-xs', 'id' => 'nombre_atendido')) !!}
											</div>

											{!! Form::button('<i class="glyphicon glyphicon-search"></i> Buscar', array('class' => 'btn btn-success btn-xs', 'id' => 'btnBuscarAtendido', 'onclick' => 'buscarAtendido();', 'style' => 'margin-top: 25px;')) !!}

											{!! Form::button('<i class="glyphicon glyphicon-search"></i> Todos', array('class' => 'btn btn-primary btn-xs', 'id' => 'btnAtendidos', 'onclick' => 'tablaAtendidos();', 'style' => 'margin-top: 25px;')) !!}


											
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
														<input type="hidden" id="cita_id" name="cita_id">
														<div class="form-group">
															{!! Form::label('fecha', 'Fecha:', array('class' => 'col-sm-2 control-label')) !!}
															<div class="col-sm-5" style="margin-top:7px;">
																{!! Form::date('fecha', $hoy, array('class' => 'form-control input-xs col-sm-3', 'id' => 'fecha', 'readonly' => 'readonly', 'style' => 'font-size: 16px;')) !!}
															</div>
														</div>
														<div class="form-group">
															{!! Form::label('paciente', 'Paciente:', array('class' => 'col-sm-2 control-label')) !!}
															<div class="col-sm-8" style="margin-top:7px;">
																{!! Form::text('paciente', '', array('class' => 'form-control input-xs', 'id' => 'paciente', 'readonly' => 'readonly', 'style' => 'font-size: 13px;')) !!}
															</div>
															<div class="col-sm-2" style="margin-top:7px;">
																<button title="Información del Paciente" onclick="" id="btnInfoPaciente" data-toggle='modal' data-target='#exampleModal3' class="btn btn-xs btn-primary" type="button"><div class="glyphicon glyphicon-eye-open"></div></button>
															</div>
														</div>
														<div class="form-group">
															<div class="col-sm-6" style="padding-left: 0px;">
																{!! Form::label('historia', 'Historia:', array('class' => 'col-sm-4 control-label')) !!}
																<div class="col-sm-8" style="margin-top:7px;">
																	{!! Form::text('historia', '', array('class' => 'form-control input-xs', 'id' => 'historia', 'readonly' => 'readonly', 'style' => 'font-size: 16px;')) !!}
																</div>
															</div>
															<div class="col-sm-6">
																{!! Form::label('numero', 'Tratam.:', array('class' => 'col-sm-4 control-label')) !!}
																<div class="col-sm-8" style="margin-top:7px;">
																	{!! Form::text('numero', '', array('class' => 'form-control input-xs', 'id' => 'numero', 'readonly' => 'readonly', 'style' => 'font-size: 16px;')) !!}
																</div>
															</div>
														</div>
														<div class="form-group">
															{!! Form::label('doctor', 'Doctor:', array('class' => 'col-sm-2 control-label')) !!}
															<div class="col-sm-10" style="margin-top:7px;">
																{!! Form::text('doctor', '', array('class' => 'form-control input-xs', 'id' => 'doctor', 'readonly' => 'readonly', 'style' => 'font-size: 13px;')) !!}
															</div>
														</div>
														<div class="form-group">
															{!! Form::label('cie102', 'Cie10:', array('class' => 'col-sm-2 control-label')) !!}
															<div class="col-sm-10" style="margin-top:7px;">
																{!! Form::text('cie102', '', array('class' => 'form-control input-xs', 'id' => 'cie102', 'style' => 'font-size: 16px;')) !!}
																{!! Form::hidden('cantcie', 0, array('id' => 'cantcie')) !!}
															</div>
															<div style=" margin: 40px 15px 0px 15px;">
																<table class="table table-striped table-bordered col-lg-12 col-md-12 col-sm-12 " style="font-size: 70%;margin-bottom:-10px;">
																	<thead id="cabeceracie">
																		<tr>
																			<th width='80%' style="font-size: 13px !important;">Descripción</th>
																			<th width='20%' style="font-size: 13px !important;">Eliminar</th>
																		</tr>
																	</thead>
																	<tbody id="detallecie"></tbody>
																</table>
															</div>
														</div>
														<div class="form-group">
															{!! Form::label('antecedentes', 'Antecedentes:', array('class' => 'col-sm-2 control-label')) !!}
															<div class="col-sm-12">
																<textarea class="form-control input-xs" id="antecedentes" cols="10" rows="2" style="font-size: 16px; overflow-y:auto;"></textarea>
															</div>
														</div>	
														<div class="form-group">
															<div class="col-sm-6" style="padding-left: 0px;">
																{!! Form::label('fondo', 'Fondo de ojos:', array('class' => 'col-sm-9 control-label', 'style' => 'text-align:left;' )) !!}
																<input style="margin-top: 11px; margin-left: -15px;" type="checkbox" id="fondo" value="1"><br>
															</div>
															<div class="col-sm-6" style="padding-left: 0px;">
																{!! Form::label('citas', 'Cant de Citas:', array('class' => 'col-sm-8 control-label', 'style' => 'text-align:left;' )) !!}
																<div class="col-sm-4" style="margin-top:7px;">
																	{!! Form::text('citas', '', array('class' => 'form-control input-xs', 'id' => 'citas', 'readOnly', 'style' => 'font-size: 16px;')) !!}
																</div>
															</div>
														</div>
														<div class="form-group"  style="padding-left: 0px;">
															{!! Form::label('citaproxima', 'Próxima cita:', array('class' => 'col-sm-4 control-label', 'style' => 'text-align:left;' )) !!}
															<div class="col-sm-5" style="margin-top:7px;">
																{!! Form::date('citaproxima', '', array('class' => 'form-control input-xs', 'id' => 'citaproxima' , 'name' => 'citaproxima', 'style' => 'margin-left: -25px;' , 'onchange' => 'cantidadCitasFecha();' )) !!}
															</div>
															{!! Form::button('<i class="glyphicon glyphicon-check"></i> Guardar', array('class' => 'btn btn-success btn-sm', 'id' => 'btnGuardar', 'onclick' => 'registrarHistoriaClinica();')) !!}
														</div>	
														<h5 style="color: red; font-weight: bold;" id="mensajeHistoriaClinica"></h5>
													</div>
													<div class="col-sm-4" style="font-size: 15px;">
														<div class="form-group" style='display:none;'>
															{!! Form::label('sintomas', 'Sintomas:') !!}
															<textarea class="form-control input-xs" id="sintomas" cols="10" rows="3" style="font-size: 16px; overflow-y:auto;"></textarea>
														</div>
														<div class="form-group">
															{!! Form::label('motivo', 'Motivo:') !!}
															<textarea class="form-control input-xs" id="motivo" cols="10" rows="2" style="font-size: 16px; overflow-y:auto;"></textarea>
														</div>
														<div class="form-group">
															{!! Form::label('exploracion_fisica', 'Exploración Física:') !!}
															<textarea class="form-control input-xs" id="exploracion_fisica" cols="10" rows="6" style="font-size: 16px; overflow-y:auto;"></textarea>
														</div>
														<div class="form-group">
															{!! Form::label('diagnostico', 'Diagnostico:') !!}
															<textarea class="form-control input-xs" id="diagnostico" cols="10" rows="2" style="font-size: 16px; overflow-y:auto;"></textarea>
														</div>
														<div class="form-group">
															{!! Form::label('tratamiento', 'Tratamiento:') !!}
															<textarea class="form-control input-xs" id="tratamiento" cols="10" rows="2" style="font-size: 16px; overflow-y:auto;"></textarea>
														
														</div>
													</div>	
														
													<div class="col-sm-4">
														<!-- Lista de historias clinicas anteriores -->
														<strong>LISTA DE CITAS ANTERIORES:</strong>
														<div id="tablaCita">
														</div>
														<!-- Fin historias clinicas anteriores -->	

														<div class="form-group" style="padding:15px;">
															{!! Form::label('examenes', 'Exámenes:', array('class' => 'col-sm-3 control-label', 'style' => 'margin-left: -15px;')) !!}
															<div class="col-sm-9" style="margin-top:7px;">
																{!! Form::text('examenes', '', array('class' => 'form-control input-xs', 'id' => 'examenes', 'style' => 'font-size: 16px;')) !!}
															</div>
															<strong align="center" class="col-lg-12 col-md-12 col-sm-12 m-t-40" style="margin-top: 10px;">LISTA DE EXÁMENES</strong>
															<table class="table table-striped table-bordered col-lg-12 col-md-12 col-sm-12 " style="font-size: 70%; padding: 0px 0px !important; margin-top: 10px;">
																<thead id="cabecera">
																	<tr>
																		<th width='80%' style="font-size: 13px !important;">Descripción</th>
																		<th width='20%' style="font-size: 13px !important;">Eliminar</th>
																	</tr>
																</thead>
																<tbody id="detalle"></tbody>
															</table>
														</div>
														
													</div>

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
				<div class="modal-dialog" role="document" style="width:1100px;">
				    <div class="modal-content">
						<div class="modal-header">
							<h3 class="modal-title" id="tituloeditar">Editar Atención</h3>
						</div>
						<div class="form-horizontal">
							<input type="hidden" id="atencion_id" name="atencion_id">
							<div class="col-sm-12" style="font-size: 15px;margin-top:20px;">
								<div class="form-group col-sm-4">
									<label for="fechaeditar" class="col-sm-4 control-label">Fecha:</label>
									<div class="col-sm-8" style="margin-top:7px;">
										<input class="form-control input-xs" id="fechaeditar" readonly style="font-size: 16px;"  name="fechaeditar" type="text">
									</div>
								</div>
								<div class="form-group col-sm-4">
									<label for="historiaeditar" class="col-sm-4 control-label">Historia:</label>
									<div class="col-sm-8" style="margin-top:7px;">
										<input class="form-control input-xs" id="historiaeditar" readonly style="font-size: 16px;" name="historiaeditar" type="text">
									</div>
								</div>
								<div class="form-group col-sm-4">
									<label for="numeroeditar" class="col-sm-4 control-label">Tratam.:</label>
									<div class="col-sm-8" style="margin-top:7px;">
										<input class="form-control input-xs" id="numeroeditar" readonly style="font-size: 16px;" name="numeroeditar" type="text">
									</div>
								</div>
							</div>
							<div class="col-sm-12" style="font-size: 15px;">
								<div class="form-group col-sm-6">
									<label for="pacienteeditar" class="col-sm-3 control-label">Paciente:</label>
									<div class="col-sm-9" style="margin-top:7px;">
										<input class="form-control input-xs" id="pacienteeditar" readonly style="font-size: 14px;" name="pacienteeditar" type="text">
									</div>
								</div>
								<div class="form-group col-sm-6">
									<label for="doctoreditar" class="col-sm-3 control-label">Doctor:</label>
									<div class="col-sm-9" style="margin-top:7px;">
										<input class="form-control input-xs" id="doctoreditar" readonly style="font-size: 14px;" name="doctoreditar" type="text">
									</div>
								</div>
							</div>
							<div class="col-sm-12" style="font-size: 15px;">
								<div class="form-group col-sm-4">
									<label for="fondoeditar" class="col-sm-9 control-label">Fondo de ojos:</label>
									<div class="col-sm-3">
										<input style="margin-top: 11px;" type="checkbox" id="fondoeditar"><br>
									</div>
								</div>		
								<div class="form-group col-sm-5">
									{!! Form::label('citaproximaeditar', 'Cita prox.:', array('class' => 'col-sm-4 control-label', 'style' => 'text-align:left;' )) !!}
									<div class="col-sm-6" style="margin-top:7px;">
										{!! Form::date('citaproximaeditar', '', array('class' => 'form-control input-xs', 'id' => 'citaproximaeditar' , 'style' => 'margin-left: -25px;' , 'onchange' => 'cantidadCitasFechaEditar();' )) !!}
									</div>
									<div class="col-sm-2" style="margin-top:7px;">
										{!! Form::text('citaseditar', '', array('class' => 'form-control input-xs', 'id' => 'citaseditar', 'readOnly', 'style' => 'font-size: 16px; margin-left: -35px;width: 50px;')) !!}
									</div>
								</div>	
							</div>
							<div class="col-sm-4" style="font-size: 15px;">
								<div class="form-group" style="margin: 5px;">
									{!! Form::label('motivoeditar', 'Motivo:') !!}
									<textarea class="form-control input-xs" id="motivoeditar" cols="10" rows="3" style="font-size: 16px; overflow-y:auto;"></textarea>
								</div>	
								<div class="form-group" style="margin: 5px;">
									{!! Form::label('antecedenteseditar', 'Antecedentes:') !!}
									<textarea class="form-control input-xs" id="antecedenteseditar" cols="10" rows="3" style="font-size: 16px; overflow-y:auto;"></textarea>
								</div>
								<div class="form-group" style="margin: 5px;">
									{!! Form::label('diagnosticoeditar', 'Diagnostico:') !!}
									<textarea class="form-control input-xs" id="diagnosticoeditar" cols="10" rows="3" style="font-size: 16px; overflow-y:auto;"></textarea>
								</div>
							</div>
							<div class="col-sm-4" style="font-size: 15px;">
								<div class="form-group" style="margin: 5px;">
									{!! Form::label('tratamientoeditar', 'Tratamiento:') !!}
									<textarea class="form-control input-xs" id="tratamientoeditar" cols="10" rows="4" style="font-size: 16px; overflow-y:auto;"></textarea>
								</div>
								<div class="form-group" style="margin: 5px;">
									{!! Form::label('exploracion_fisicaeditar', 'Exploración Física:') !!}
									<textarea class="form-control input-xs" id="exploracion_fisicaeditar" cols="10" rows="6" style="font-size: 16px; overflow-y:auto;"></textarea>
								</div>
																		
							</div>


							<div class="col-sm-4" style="font-size: 15px;">


								<div class="form-group">
									{!! Form::label('cie102editar', 'Cie10:', array('class' => 'col-sm-2 control-label', 'style' => 'margin-left: -15px;')) !!}
									<div class="col-sm-10" style="margin-top:7px;">
										{!! Form::text('cie102editar', '', array('class' => 'form-control input-xs', 'id' => 'cie102editar', 'style' => 'font-size: 16px;')) !!}
										{!! Form::hidden('cantcieeditar', 0, array('id' => 'cantcieeditar')) !!}
									</div>
									<div style=" margin: 40px 15px 0px 0px;">
									<table class="table table-striped table-bordered col-lg-12 col-md-12 col-sm-12 " style="font-size: 70%; padding: 0px 0px !important;">
										<thead id="cabeceracieeditar">
											<tr>
												<th width='80%' style="font-size: 13px !important;">Descripción</th>
												<th width='20%' style="font-size: 13px !important;">Eliminar</th>
											</tr>
										</thead>
										<tbody id="detallecieeditar"></tbody>
									</table>
									</div>
								</div>

								<div class="form-group">
									{!! Form::label('exameneseditar', 'Exámenes:', array('class' => 'col-sm-3 control-label', 'style' => 'margin-left: -15px;')) !!}
									<div class="col-sm-9" style="margin-top:7px;">
										{!! Form::text('exameneseditar', '', array('class' => 'form-control input-xs', 'id' => 'exameneseditar', 'style' => 'font-size: 16px;')) !!}
									</div>
									<div style=" margin: 40px 15px 0px 0px;">
										<table class="table table-striped table-bordered col-lg-12 col-md-12 col-sm-12 " style="font-size: 70%; padding: 0px 0px !important;">
											<thead id="cabecera">
												<tr>
													<th width='80%' style="font-size: 13px !important;">Descripción</th>
													<th width='20%' style="font-size: 13px !important;">Eliminar</th>
												</tr>
											</thead>
											<tbody id="detalleeditar"></tbody>
										</table>
									</div>										
								</div>	

							</div>
						</div>
				        <div class="modal-footer">
							<button type="button" id="btnGuardarEditar" class="btn btn-success"><i class="glyphicon glyphicon-check"></i> Guardar</button>
				            <button type="button" id="btnCerrarModalEditar" class="btn btn-danger"><i class="glyphicon glyphicon-remove"></i> Cerrar</button>
				        </div>
				    </div>
				</div>
			</div>
			<!-- Modal -->
			<div class="modal fade" id="exampleModal3" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
				<div class="modal-dialog" role="document">
				    <div class="modal-content">
					    <div class="modal-body" id="infoPaciente"></div>
				        <div class="modal-footer">
				            <button type="button" class="btn btn-success" data-dismiss="modal">Cerrar</button>
				        </div>
				    </div>
				</div>
			</div>
	        <!-- /.content-wrapper -->
	        <footer class="navbar-default navbar-fixed-bottom" style="display: none; padding-left: 20px !important; padding-bottom: 20px; padding-top: 20px; padding-right: 20px;">
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
		//$("#sintomas").prop('disabled', true);
		$("#antecedentes").prop('disabled', true);
		$("#diagnostico").prop('disabled', true);
		$("#tratamiento").prop('disabled', true);
		$("#exploracion_fisica").prop('disabled', true);
		$("#examenes").prop('disabled', true);
		$("#motivo").prop('disabled', true);
		$("#citaproxima").prop('disabled', true);
		$("#btnGuardar").prop('disabled', true);
		$("#fondo").prop('disabled', true);
		$('#fondo').prop('checked', false);
	});
	
		
	var cie10s = new Bloodhound({
			datumTokenizer: function (d) {
				return Bloodhound.tokenizers.whitespace(d.value);
			},
			limit: 5,
			queryTokenizer: Bloodhound.tokenizers.whitespace,
			remote: {
				url: 'historiaclinica/cie10autocompletar/%QUERY',
				filter: function (cie10s) {
					return $.map(cie10s, function (cie10) {
						return {
							value: cie10.value,
							id: cie10.id,
						};
					});
				}
			}
		});
		cie10s.initialize();
		$("#cie102").typeahead(null,{
			displayKey: 'value',
			source: cie10s.ttAdapter()
		}).on('typeahead:selected', function (object, datum) {
			$("#cie102").val("");
			var cantcie = $("#cantcie").val();
			var cie_id = datum.id;
			var existe = false;
			$("#detallecie tr").each(function(){
				if(cie_id == this.id){
					existe = true;
				}
			});
			if(!existe){
				fila =  '<tr align="center" id="'+ datum.id +'" ><td style="vertical-align: middle; text-align: left;">'+ datum.value +'</td><td style="vertical-align: middle;"><a onclick="eliminarDetalleCie(this)" class="btn btn-xs btn-danger btnEliminar" type="button"><div class="glyphicon glyphicon-remove"></div> Eliminar</a></td></tr>';
				$("#detallecie").append(fila);
				cantcie++;
				$("#cantcie").val(cantcie);
			}

		});   

	var cie10seditar = new Bloodhound({
			datumTokenizer: function (d) {
				return Bloodhound.tokenizers.whitespace(d.value);
			},
			limit: 5,
			queryTokenizer: Bloodhound.tokenizers.whitespace,
			remote: {
				url: 'historiaclinica/cie10autocompletar/%QUERY',
				filter: function (cie10seditar) {
					return $.map(cie10seditar, function (cie10) {
						return {
							value: cie10.value,
							id: cie10.id,
						};
					});
				}
			}
		});
		cie10seditar.initialize();
		$("#cie102editar").typeahead(null,{
			displayKey: 'value',
			source: cie10seditar.ttAdapter()
		}).on('typeahead:selected', function (object, datum) {
			$("#cie102editar").val("");
			var cantcie = $("#cantcieeditar").val();
			var cie_id = datum.id;
			var existe = false;
			$("#detallecieeditar tr").each(function(){
				if(cie_id == this.id){
					existe = true;
				}
			});
			if(!existe){
				fila =  '<tr align="center" id="'+ datum.id +'" ><td style="vertical-align: middle; text-align: left;">'+ datum.value +'</td><td style="vertical-align: middle;"><a onclick="eliminarDetalleCie(this)" class="btn btn-xs btn-danger btnEliminar" type="button"><div class="glyphicon glyphicon-remove"></div> Eliminar</a></td></tr>';
				$("#detallecieeditar").append(fila);
				cantcie++;
				$("#cantcieeditar").val(cantcie);
			}
		});   



	var examenes = new Bloodhound({
			datumTokenizer: function (d) {
				return Bloodhound.tokenizers.whitespace(d.value);
			},
			limit: 5,
			queryTokenizer: Bloodhound.tokenizers.whitespace,
			remote: {
				url: 'historiaclinica/examenesAutocompletar/%QUERY',
				filter: function (examenes) {
					return $.map(examenes, function (examen) {
						return {
							value: examen.value,
							id: examen.id,
						};
					});
				}
			}
		});
		examenes.initialize();
		$("#examenes").typeahead(null,{
			displayKey: 'value',
			source: examenes.ttAdapter()
		}).on('typeahead:selected', function (object, datum) {
			$("#examenes").val("");
			var examen_id = datum.id;
			var existe = false;

			$("#detalle tr").each(function(){
				if(examen_id == this.id){
					existe = true;
				}
			});

			if(!existe){
				fila =  '<tr align="center" id="'+ datum.id +'" ><td style="vertical-align: middle; text-align: left;">'+ datum.value +'</td><td style="vertical-align: middle;"><a onclick="eliminarDetalle(this)" class="btn btn-xs btn-danger btnEliminar" type="button"><div class="glyphicon glyphicon-remove"></div> Eliminar</a></td></tr>';
				$("#detalle").append(fila);
			}
		});  

	
	var exameneseditar = new Bloodhound({
			datumTokenizer: function (d) {
				return Bloodhound.tokenizers.whitespace(d.value);
			},
			limit: 5,
			queryTokenizer: Bloodhound.tokenizers.whitespace,
			remote: {
				url: 'historiaclinica/examenesAutocompletar/%QUERY',
				filter: function (exameneseditar) {
					return $.map(exameneseditar, function (exameneditar) {
						return {
							value: exameneditar.value,
							id: exameneditar.id,
						};
					});
				}
			}
		});
		exameneseditar.initialize();
		$("#exameneseditar").typeahead(null,{
			displayKey: 'value',
			source: exameneseditar.ttAdapter()
		}).on('typeahead:selected', function (object, datum) {
			$("#exameneseditar").val("");
			var examen_id = datum.id;
			var existe = false;

			$("#detalleeditar tr").each(function(){
				if(examen_id == this.id){
					existe = true;
				}
			});

			if(!existe){
				fila =  '<tr align="center" id="'+ datum.id +'" ><td style="vertical-align: middle; text-align: left;">'+ datum.value +'</td><td style="vertical-align: middle;"><a onclick="eliminarDetalle(this)" class="btn btn-xs btn-danger btnEliminar" type="button"><div class="glyphicon glyphicon-remove"></div> Eliminar</a></td></tr>';
				$("#detalleeditar").append(fila);
			}
		}); 

	function eliminarDetalle(comp){
		(($(comp).parent()).parent()).remove();
	}

	function eliminarDetalleCie(comp){
		(($(comp).parent()).parent()).remove();
		var cantcie = $("#cantcie").val();
		cantcie--;
		$("#cantcie").val(cantcie);
	}

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


	$(document).ready(function(){     
	      $("#nombrep").keypress(function(e) {
	        if(e.which == 13) {
	            buscar2();
	        }	
	    });		
	});

	function buscar3(){
		$.ajax({
	        type: "POST",
	        url: "producto/cie10",
	        data: "cie="+$("#cie10").val()+"&_token=<?php echo csrf_token(); ?>",
	        success: function(a) {
	        	$("#listado3{{ $entidad }}").html(a);
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
		//$('.llamando').fadeTo(500, .1).fadeTo(500, 1) ;
		$.ajax({
	        type: "POST",
	        url: "ventaadmision/llamarAtender",
	        data: "_token=<?php echo csrf_token(); ?>",
	        success: function(a) {
	        	$("#atender").html(a);
	        }
	    });
	    setInterval( 
			function(){
				 $('.llamando').fadeTo(500, .1).fadeTo(500, 1) 
			}
		, 1000) ;
	}	
    setInterval(buscar4, 4000);
	
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

	$(document).ready(function(){     
	      $("#nombre_servicio").keypress(function(e) {
	        if(e.which == 13) {
	            buscarServicio();
	        }	
	    });		
	});


	function buscarServicio(){
		$.ajax({
	        type: "POST",
	        url: "servicio/buscar",
	        data: "nombre=" + $("#nombre_servicio").val() + "&tipopago=" + $("#tipopago").val() + "&page=" + $("#page").val() + "&filas=" + $("#filas").val() + "&vistamedico=" + "SI" + "&tiposervicio=" + $("#tiposervicio").val() + "&_token=<?php echo csrf_token(); ?>",
	        success: function(a) {
	        	$("#listado2{{ $entidad }}").html(a);
	        }
	    });
	}

	function buscarHistoria(){
		$.ajax({
	        type: "POST",
	        url: "historia/buscar",
	        data: "nombre=" + $("#nombreh").val() + "&dni=" + $("#dni").val() + "&numero=" + $("#numeroh").val() + "&page=" + $("#pageh").val() + "&filas=" + $("#filash").val() + "&vistamedico=" + "SI" + "&tipopaciente=" + $("#tipopaciente").val() + "&_token=<?php echo csrf_token(); ?>",
	        success: function(a) {
	        	$("#listadoh{{ $entidad }}").html(a);
	        }
	    });
	}

	$(document).ready(function(){     
      	$("#nombreh").keypress(function(e) {
	        if(e.which == 13) {
	            buscarHistoria();
	        }	
	    });		
	    $("#dni").keypress(function(e) {
	        if(e.which == 13) {
	            buscarHistoria();
	        }	
	    });	
	    $("#numeroh").keypress(function(e) {
	        if(e.which == 13) {
	            buscarHistoria();
	        }	
	    });	
	});

	function buscarAtendido(){
		$.ajax({
	        type: "POST",
	        url: "historiaclinica/tablaAtendidos",
	        data: "nombre=" + $("#nombre_atendido").val() + "&_token=<?php echo csrf_token(); ?>",
	        success: function(a) {
	        	$('#tablaAtendidos').html(a);
	        }
	    });
	}

	$(document).ready(function(){     
	      $("#nombre_atendido").keypress(function(e) {
	        if(e.which == 13) {
	            buscarAtendido();
	        }	
	    });		
	});


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

	function llamarPacienteNombre(){

		$('#mensajeBusquedaPaciente').html('');

		var paciente = $("#buscarPaciente").val();

		if(paciente != ""){

			$.ajax({
				"method": "POST",
				"url": "{{ url('/ventaadmision/llamarPacienteNombre') }}",
				"data": {
					"paciente" : paciente, 
					"_token": "{{ csrf_token() }}",
					}
			}).done(function(info){
				$('#resultadoBusquedaPaciente').html(info);
			});

		}else{
			$('#mensajeBusquedaPaciente').html('Ingrese paciente');
			$('#resultadoBusquedaPaciente').html('');
		}
	}

    $(document).on('click', '.btnLlamarPaciente', function(event) {
    	event.preventDefault();
    	var paciente_id = $(this).data('paciente_id');
    	var ticket_id = $(this).data('ticket_id');
		var pantalla = $(this).data('pantalla');

		$('#pantalla').val(pantalla);

		if(pantalla == "SI"){
			$.ajax({
				"method": "POST",
				"url": "{{ url('/ventaadmision/colamedico') }}",
				"data": {
					"ticket_id" : ticket_id, 
					"_token": "{{ csrf_token() }}",
					}
			});
		}

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
				$('#cita_id').val(a.cita_id);
				$('#citaproxima').val(a.citaproxima);
				if(a.fondo == "SI"){
					$('#fondo').prop('checked', false);
					$('#fondo_si').val(a.fondo);
					//$("#cie102").prop('readOnly', true);
					//$("#citaproxima").prop('readOnly', true);
					//$("#motivo").prop('readOnly', true);
				}else{
					$('#fondo').prop('checked', false);
					$('#fondo_si').val(a.fondo);
					//$("#cie102").prop('readOnly', false);
					//$("#motivo").prop('readOnly', false);
				}
				$('#cie102').val(a.cie10);
				//$('#cie102_id').val(a.cie10id);
				$('#citas').val(a.cantcitas);
  				$('#cie102').focus();
				$('#motivo').val(a.motivo.replace(/<BR>/g,"\n"));
				//ANTECEDENTES
				$('#antecedentes').val(a.antecedentes.replace(/<BR>/g,"\n"));
				//FIN ANTECEDENTES
				$('#tratamiento').val(a.tratamiento.replace(/<BR>/g,"\n"));
				$('#diagnostico').val(a.diagnostico.replace(/<BR>/g,"\n"));
				$('#exploracion_fisica').val(a.exploracion_fisica.replace(/<BR>/g,"\n"));
				//$('#examenes').val(a.examenes);
				console.log(a.examenes);
				var arr = a.examenes;
				$.each(arr, function (index, value) {
					var fila =  '<tr align="center" id="'+ value.servicio_id +'" ><td style="vertical-align: middle; text-align: left;">'+ value.nombre +'</td><td style="vertical-align: middle;"><a onclick="eliminarDetalle(this)" class="btn btn-xs btn-danger btnEliminar" type="button"><div class="glyphicon glyphicon-remove"></div> Eliminar</a></td></tr>';
					$("#detalle").append(fila);
				});

				console.log(a.cies);
				var arrcies = a.cies;
				$.each(arrcies, function (index, value) {
					var fila =  '<tr align="center" id="'+ value.cie_id +'" ><td style="vertical-align: middle; text-align: left;">'+ value.descripcion +'</td><td style="vertical-align: middle;"><a onclick="eliminarDetalleCie(this)" class="btn btn-xs btn-danger btnEliminar" type="button"><div class="glyphicon glyphicon-remove"></div> Eliminar</a></td></tr>';
					$("#detallecie").append(fila);
				});
				$('#cantcie').val(a.cantcies);
	        },
		error: function() {
	        alert('OCURRIÓ UN ERROR, VUELVA A INTENTAR...');
	    }
	    });
    });

    function registrarHistoriaClinica(){
    	
    	if($('#cantcie').val() == 0) {
    		$('#cie102').focus();
    		$('#mensajeHistoriaClinica').html('Debes ingresar mínimo un CIE 10.');
    		return 0;
    	}
/*
    	if($('#cie102_id').val() == '' || $('#cie102_id').val() == '0') {
    		$('#cie102_id').focus();
    		$('#mensajeHistoriaClinica').html('Debes ingresar un CIE 10 válido.');
    		return 0;
    	}
    	/* ANTECEDENTES
		if($('#antecedentes').val() == '') {
    		$('#antecedentes').focus();
    		$('#mensajeHistoriaClinica').html('Debes ingresar antecedentes.');
    		return 0;
    	}*/
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
		/*if($('#examenes').val() == '') {
    		$('#examenes').focus();
    		$('#mensajeHistoriaClinica').html('Debes ingresar exámenes.');
    		return 0;
    	}*/
    	var tratamiento = $('#tratamiento').val().replace(/\r?\n/g, "<br>");
    	//ANTECEDENTES
		var antecedentes = $('#antecedentes').val().replace(/\r?\n/g, "<br>");
    	var diagnostico = $('#diagnostico').val().replace(/\r?\n/g, "<br>");
		//var examenes = $('#examenes').val().replace(/\r?\n/g, "<br>");
    	var motivo = $('#motivo').val().replace(/\r?\n/g, "<br>");
    	var exploracion_fisica = $('#exploracion_fisica').val().replace(/\r?\n/g, "<br>");
		var fondo = "NO";
		if( $('#fondo').prop('checked') ){
			fondo = "SI";
		}
		var ticket_id = $(this).data('ticket_id');
		var doctor_id = $('#doctor_id').val();
		var citaproxima = $('#citaproxima').val();
		
		//detalle
		var data = [];
		$("#detalle tr").each(function(){
			var element = $(this); // <-- en la variable element tienes tu elemento
			var id = element.attr('id');
			data.push(
				{ "id": id }
			);
		});
		var detalle = {"data": data};
		var json = JSON.stringify(detalle);

		var cita_id = $('#cita_id').val();

		//fin detalle


		//detalle
		var datacie = [];
		$("#detallecie tr").each(function(){
			var element = $(this); // <-- en la variable element tienes tu elemento
			var id = element.attr('id');
			datacie.push(
				{ "id": id }
			);
		});
		var detallecie = {"data": datacie};
		var jsoncie = JSON.stringify(detallecie);
		//fin detalle


		var dataform = $('#formHistoriaClinica').serializeArray(); 

		dataform.push(
			{
				name: "_token", value: "{{ csrf_token() }}",
			}
		);
		dataform.push(
			{
				name: "tratamiento", value: tratamiento,
			}
		);
		dataform.push(
			{
				name: "citaproxima", value: citaproxima,
			}
		);
		dataform.push(
			{
				name: "antecedentes", value: antecedentes,
			}
		);
		dataform.push(
			{
				name: "diagnostico", value: diagnostico,
			}
		);
		dataform.push(
			{
				name: "motivo", value: motivo,
			}
		);
		dataform.push(
			{
				name: "exploracion_fisica", value: exploracion_fisica,
			}
		);
		dataform.push(
			{
				name: "fondo", value: fondo,
			}
		);
		dataform.push(
			{
				name: "doctor_id", value: doctor_id,
			}
		);
		dataform.push(
			{
				name: "examenes", value: json,
			}
		);
		dataform.push(
			{
				name: "cies", value: jsoncie,
			}
		);
		dataform.push(
			{
				name: "cita_id", value: cita_id,
			}
		);

		console.log(dataform);

		$.ajax({
	        type: "POST",
	        url: "historiaclinica/registrarHistoriaClinica",
	        data: $.param(dataform),
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
	  				$('#antecedentes').val('');
	  				$('#diagnostico').val('');
					$('#examenes').val('');
					$('#motivo').val('');
					$('#citaproxima').val('');
					$('#doctor').val('');
					$('#doctor_id').val('');
					$('#citas').val('');
					$('#exploracion_fisica').val('');
					$("#divpresente").css('display','');
					$('#detalle').html('');
					$('#detallecie').html('');
					$("#cie102").prop('disabled', true);
					$("#antecedentes").prop('disabled', true);
					$("#diagnostico").prop('disabled', true);
					$("#tratamiento").prop('disabled', true);
					$("#exploracion_fisica").prop('disabled', true);
					$("#citaproxima").prop('disabled', true);
					$("#examenes").prop('disabled', true);
					$("#motivo").prop('disabled', true);
					$("#btnGuardar").prop('disabled', true);
					$("#fondo").prop('disabled', true);
					$('#fondo').prop('checked', false);
					tablaAtendidos();
	        	}else{
	        		alert('OCURRIÓ UN ERROR AL GUARDAR, VUELVA A INTENTAR...');
	        		//console.log(a);
	        	}
	        },
		error: function() {
	        alert('OCURRIÓ UN ERROR, VUELVA A INTENTAR...');
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
				//$('#cie10editar').val(a.cie10);
				$('#motivoeditar').val(a.motivo.replace(/<BR>/g,"\n"));
				console.log(a.citaproxima);
				$('#citaproximaeditar').val(a.citaproxima);
				//ANTECEDENTES
				$('#antecedenteseditar').val(a.antecedentes.replace(/<BR>/g,"\n"));
				$('#tratamientoeditar').val(a.tratamiento.replace(/<BR>/g,"\n"));
				$('#diagnosticoeditar').val(a.diagnostico.replace(/<BR>/g,"\n"));
				$('#exploracion_fisicaeditar').val(a.exploracion_fisica.replace(/<BR>/g,"\n"));
				//$('#exameneseditar').val(a.examenes);
				console.log(a.examenes);
				var arr = a.examenes;
				$.each(arr, function (index, value) {
					var fila =  '<tr align="center" id="'+ value.servicio_id +'" ><td style="vertical-align: middle; text-align: left;">'+ value.nombre +'</td><td style="vertical-align: middle;"><a onclick="eliminarDetalle(this)" class="btn btn-xs btn-danger btnEliminar" type="button"><div class="glyphicon glyphicon-remove"></div> Eliminar</a></td></tr>';
					$("#detalleeditar").append(fila);
				});

				console.log(a.cies);
				var arrcies = a.cies;
				$.each(arrcies, function (index, value) {
					var fila =  '<tr align="center" id="'+ value.cie_id +'" ><td style="vertical-align: middle; text-align: left;">'+ value.descripcion +'</td><td style="vertical-align: middle;"><a onclick="eliminarDetalleCie(this)" class="btn btn-xs btn-danger btnEliminar" type="button"><div class="glyphicon glyphicon-remove"></div> Eliminar</a></td></tr>';
					$("#detallecieeditar").append(fila);
				});
				$('#cantcie').val(a.cantcies);
				if(a.fondo == "SI"){
					$('#fondoeditar').prop('checked', true);
				}else{
					$('#fondoeditar').prop('checked', false);
				}
				cantidadCitasFechaEditar();
	        }
	    });
	}	
	
	$(document).on('click', '#btnCerrarModalEditar', function(event) {
		$('#exameneseditar').val('');
		$('#cie102editar').val('');
		$('#detalleeditar').html('');
		$('#detallecieeditar').html('');
		$('#exampleModal2').modal('hide');
	});


	$(document).on('click', '#btnGuardarEditar', function(event) {	

		var cita_id = $("#atencion_id").val();
		var tratamiento = $('#tratamientoeditar').val().replace(/\r?\n/g, "<BR>");
    	var antecedentes = $('#antecedenteseditar').val().replace(/\r?\n/g, "<BR>");
    	var diagnostico = $('#diagnosticoeditar').val().replace(/\r?\n/g, "<BR>");
		//var examenes = $('#exameneseditar').val().replace(/\r?\n/g, "<BR>");
    	var motivo = $('#motivoeditar').val().replace(/\r?\n/g, "<BR>");
    	var exploracion_fisica = $('#exploracion_fisicaeditar').val().replace(/\r?\n/g, "<BR>");
		var citaproxima = $('#citaproximaeditar').val();
		
		//detalle
		var data = [];
		$("#detalleeditar tr").each(function(){
			var element = $(this); // <-- en la variable element tienes tu elemento
			var id = element.attr('id');
			data.push(
				{ "id": id }
			);
		});
		var detalle = {"data": data};
		var json = JSON.stringify(detalle);

		//var cita_id = $('#cita_id').val();

		//fin detalle


		//detalle
		var datacie = [];
		$("#detallecieeditar tr").each(function(){
			var element = $(this); // <-- en la variable element tienes tu elemento
			var id = element.attr('id');
			datacie.push(
				{ "id": id }
			);
		});
		var detallecie = {"data": datacie};
		var jsoncie = JSON.stringify(detallecie);
		//fin detalle


		var fondo = "NO";
		if( $('#fondoeditar').prop('checked') ){
			fondo = "SI";
		}


		$.ajax({
			"method": "POST",
			"url": "{{ url('/historiaclinica/guardarEditado') }}",
			"data": {
				"cita_id" : cita_id, 
				"tratamiento" : tratamiento,
				"antecedentes" : antecedentes,
				"diagnostico" : diagnostico,
				"examenes" : json,
				"cies" : jsoncie,
				"citaproxima" : citaproxima,
				"motivo" : motivo,
				"fondo" : fondo,
				"exploracion_fisica" : exploracion_fisica,
				"_token": "{{ csrf_token() }}",
				}
		}).done(function(info){
			if(info == 'OK') {
				alert('TRATAMIENTO REGISTRADO CORRECTAMENTE...');
				$('#exampleModal2').modal('hide');
				$('#citaseditar').val("");
				$('#detalleeditar').html('');
				$('#detallecieeditar').html('');
				tablaAtendidos();
			}else{
        		alert('OCURRIÓ UN ERROR, VUELVA A INTENTAR...');
        	}
		});

	});

	function presente(estado){
		if(estado == "SI"){
			$("#cie102").prop('disabled', false);
			$("#antecedentes").prop('disabled', false);
			$("#diagnostico").prop('disabled', false);
			$("#tratamiento").prop('disabled', false);
			$("#btnGuardar").prop('disabled', false);
			$("#exploracion_fisica").prop('disabled', false);
			$("#examenes").prop('disabled', false);
			$("#motivo").prop('disabled', false);
			$("#citaproxima").prop('disabled', false);
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
			$('#antecedentes').val('');
			$('#citaproxima').val('');
			$('#diagnostico').val('');
			$('#examenes').val('');
			$('#motivo').val('');
			$('#citas').val('');
			$('#detalle').html('');
			$('#detallecie').html('');
			$('#exploracion_fisica').val('');
			$("#cie102").prop('disabled', true);
			$("#antecedentes").prop('disabled', true);
			$("#diagnostico").prop('disabled', true);
			$("#tratamiento").prop('disabled', true);
			$("#exploracion_fisica").prop('disabled', true);
			$("#examenes").prop('disabled', true);
			$("#citaproxima").prop('disabled', true);
			$("#motivo").prop('disabled', true);
			$("#btnGuardar").prop('disabled', true);
			$("#fondo").prop('disabled', true);
			$('#fondo').prop('checked', false);
		}
		var ticket_id = $('#ticket_id').val();
		var pantalla = $('#pantalla').val();

		if(pantalla == "SI"){
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
	}

	function cantidadCitasFecha(){
		
		var fecha = $('#citaproxima').val();

		$.ajax({
			"method": "POST",
			"url": "{{ url('/historiaclinica/cantidadCitasFecha') }}",
			"data": {
				"fecha" : fecha, 
				"_token": "{{ csrf_token() }}",
				}
		}).done(function(info){
			$('#citas').val(info);
		});

	}

	function cantidadCitasFechaEditar(){
		
		var fecha = $('#citaproximaeditar').val();

		$.ajax({
			"method": "POST",
			"url": "{{ url('/historiaclinica/cantidadCitasFecha') }}",
			"data": {
				"fecha" : fecha, 
				"_token": "{{ csrf_token() }}",
				}
		}).done(function(info){
			$('#citaseditar').val(info);
		});

	}

	$(document).on('click', '#btnInfoPaciente', function(event) {
		var historia = $('#historia').val();
		$.ajax({
			"method": "POST",
			"url": "{{ url('/historiaclinica/infoPaciente') }}",
			"data": {
				"historia" : historia, 
				"_token": "{{ csrf_token() }}",
				}
		}).done(function(info){
			$('#infoPaciente').html(info);
		});


	});
</script>
@endif
@endif