<?php
	use Illuminate\Support\Facades\Auth;
	$usertype_id = Auth::user()->usertype_id;

	date_default_timezone_set('America/Lima');
?>
<!-- Content Header (Page header) -->
<section class="content-header">
	<h1>
		{{ $title }} <small>Facturación</small>
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
						@if($usertype_id==28||$usertype_id==1||$usertype_id==2)
						<div class="col-xs-{{ ($usertype_id==1||$usertype_id==2)?'6':'12' }}">
							{!! Form::open(['route' => $ruta["search"], 'method' => 'POST' ,'onsubmit' => 'return false;', 'role' => 'form', 'class' => 'form-inline', 'autocomplete' => 'off', 'id' => 'formBusqueda'.$entidad]) !!}
							{!! Form::hidden('page', 1, array('id' => 'page')) !!}
							{!! Form::hidden('accion', 'listar', array('id' => 'accion')) !!}
							<div class="form-group">
								{!! Form::label('nombre', 'Nombre:') !!}
								{!! Form::text('nombre', '', array('class' => 'form-control input-xs', 'id' => 'nombre')) !!}
							</div>
							<div class="form-group">
								{!! Form::label('fecha', 'Fecha:') !!}
								{!! Form::date('fecha', date('Y-m-d'), array('class' => 'form-control input-xs', 'id' => 'fecha')) !!}
							</div>
							<div class="form-group">
								{!! Form::label('filas', 'Filas a mostrar:')!!}
								{!! Form::selectRange('filas', 1, 30, 10, array('class' => 'form-control input-xs', 'onchange' => 'buscar(\''.$entidad.'\')')) !!}
							</div>
							@if($usertype_id==1||$usertype_id==2)
							<div class="form-group">
								{!! Form::label('filtro', 'Estado:')!!}
								<select name="filtro" id="filtro" class='form-control input-xs' onchange="buscar('{{ $entidad }}')">
									<option value="DESCARGADO">DESCARGADO</option>
									<option value="SUBIDO">SUBIDO</option>
									<option value="IMPORTADO">IMPORTADO</option>
								</select>
							</div>
							@endif
							{!! Form::button('<i class="glyphicon glyphicon-search"></i> Buscar', array('class' => 'btn btn-success btn-xs', 'id' => 'btnBuscar_', 'onclick' => 'buscar(\''.$entidad.'\')')) !!}
							{!! Form::button('<i class="glyphicon glyphicon-file"></i> Generar Archivo', array('class' => 'btn btn-primary btn-xs', 'id' => 'btnBuscar__', 'onclick' => 'generarArchivo()')) !!}
							{!! Form::close() !!}
						</div>
						@endif
						@if($usertype_id==29||$usertype_id==1||$usertype_id==2)
						<div class="col-xs-{{ ($usertype_id==1||$usertype_id==2)?'6':'12' }}">
							{!! Form::open(['enctype'=>'multipart/form-data', 'route' => $ruta["search"], 'method' => 'POST' ,'onsubmit' => 'return false;', 'role' => 'form', 'class' => 'form-inline', 'autocomplete' => 'off', 'id' => 'formBusqueda'.$entidad]) !!}
							{!! Form::hidden('page', 1, array('id' => 'page')) !!}
							{!! Form::hidden('accion', 'listar', array('id' => 'accion')) !!}
							<div class="form-group">
							    <label for="nombrearchivo">Adjuntar archivo</label>							    
							    <input type="file" id="nombrearchivo" name="nombrearchivo" class="form-control input-xs">
							</div>
							<div class="form-group">
								{!! Form::label('filas', 'Filas a mostrar:')!!}
								{!! Form::selectRange('filas', 1, 30, 10, array('class' => 'form-control input-xs', 'onchange' => 'buscar(\''.$entidad.'\')')) !!}
							</div>
							{!! Form::button('<i class="glyphicon glyphicon-file"></i> Importar Archivo', array('class' => 'btn btn-warning btn-xs', 'id' => 'btnBuscar___')) !!}
							{!! Form::button('<i class="glyphicon glyphicon-search"></i> Buscar', array('class' => 'btn btn-success btn-xs', 'id' => 'btnBuscar', 'onclick' => 'buscar(\''.$entidad.'\')')) !!}		
							<p id="mensajeFileExcel"></p>					
							{!! Form::close() !!}
						</div>
						@endif
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
	<!-- Modal -->
    <div id="modalMensajeArchivo" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">MENSAJE DE ARCHIVO</h4>
                </div>
                <div class="modal-body">
                    <div class="bootbox-body">
                        <p id="mensajeArchivo" style="font-size: 12px;"></p>
                    </div>     
                    <p class="text-right"><button type="button" class="btn btn-warning" onclick="$('#modalMensajeArchivo').modal('hide');$('#mensajeArchivo').html('');">Cancelar</button></p>
                </div>                
            </div>
      </div>
    </div>
	<!-- /.row -->
</section>
<!-- /.content -->	
<script>
	$(document).ready(function () {
		buscar('{{ $entidad }}');
		init(IDFORMBUSQUEDA+'{{ $entidad }}', 'B', '{{ $entidad }}');
		$(IDFORMBUSQUEDA + '{{ $entidad }} :input[id="nombre"]').keyup(function (e) {
			var key = window.event ? e.keyCode : e.which;
			if (key == '13') {
				buscar('{{ $entidad }}');
			}
		});
	});

	function generarArchivo() {
		fecha = $('#fecha').val();
		if(fecha.length!==10) {
			alert('Tienes que elegir una fecha específica.');
			return false;
		}
		$.ajax({
			url: 'backup.php?fecha='+fecha,
			type: 'GET',
			beforeSend: function() {
				$('#btnBuscar__').html('Cargando...').attr('disabled', 'disabled');
			}
		})
		.done(function(a) {
			$('#modalMensajeArchivo').modal('show');
			$('#mensajeArchivo').html(a);
			buscar('Respaldo');
			$('#btnBuscar__').html('<i class="glyphicon glyphicon-file"></i> Generar Archivo').removeAttr('disabled');
		})
		.fail(function() {
			alert("Ocurrió un error.");
		});
		
	}

	function importarArchivo() {
		var nombrearchivo = new FormData($('#formBusqueda{{$entidad}}')[0]);
		$.ajax({
	        url: 'respaldo/importarArchivo?_token='+$('input[name=_token]').val(),
	        type: 'POST',
	        data: nombrearchivo,
	        enctype: 'multipart/form-data',
	        processData: false,
	        contentType: false,
	        cache: false,
	        timeout: 600000,
	        beforeSend: function() {
	            $("#btnBuscar___").html('Cargando...').attr('disabled', 'disabled');
	        },
	        success: function(result) {
	            $('#nombrearchivo').val('');
	            $("#mensajeFileExcel").html(result);
	            buscar('Respaldo');
	            $('#btnBuscar___').html('<i class="glyphicon glyphicon-file"></i> Importar Archivo').removeAttr('disabled');
	        }
	    });
	}

	$(document).on('click', '#btnBuscar___', function(e) {
	    e.preventDefault();
	    e.stopImmediatePropagation();
	    var fileExcel = booleanFileExcel();
	    if (fileExcel!==false) {
	        importarArchivo();
	    }
	});

	$(document).on('change', '#nombrearchivo', function() {
	    booleanFileExcel();
	});

	function booleanFileExcel() {
	    var file = $('#nombrearchivo').val();
	    var mensaje = $('#mensajeFileExcel');
	    if (file==='') {
	        mensaje.css('color', 'red').html('* Debes seleccionar un archivo.');
	        return false;
	    }
	    var ext = file.substring(file.lastIndexOf("."));
	    if (ext != ".gz") {
	        mensaje.css('color', 'red').html('* Debes seleccionar una extensión correcta.');
	        return false;
	    } else {
	        mensaje.css('color', 'green').html('Elegiste una extensión correcta.');
	        return true;
	    }
	};
</script>