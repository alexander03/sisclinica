<!-- Page-Title -->
<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
$user = Auth::user();
?>

<!-- Content Header (Page header) -->
<section class="content-header">
	<h1>
		{{ $title }}
		{{-- <small>Descripci�n</small> --}}
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

							{!! Form::open(['route' => $ruta["guardarSucursal"], 'method' => 'POST' ,'onsubmit' => 'return false;', 'class' => 'form-inline', 'role' => 'form', 'autocomplete' => 'off', 'id' => 'formBusqueda'.$entidad]) !!}
							<div class="form-group">
								{!! Form::label('sucursal_id', 'Sucursal:') !!}
								{!! Form::select('sucursal_id', $cboSucursal, null, array('class' => 'form-control input-sm', 'id' => 'sucursal_id' , 'onchange' => 'buscar(\''.$entidad.'\')')) !!}
							</div>
							{!! Form::button('<i class="glyphicon glyphicon-floppy-disk"></i> Guardar', array('class' => 'btn btn-success waves-effect waves-light m-l-10 btn-sm', 'id' => 'btnGuardarSucursal', 'onclick' => 'guardarSucursal();')) !!}
							{!! Form::close() !!}

						</div>
        			</div>
    			</div>
			</div>
    	</div>
	</div>
</section>
<script>
	function guardarSucursal(){
		var sucursal_id = $('#sucursal_id').val();
		var respuesta="";
		var ajax = $.ajax({
			"method": "POST",
			"url": "{{ url('/usuario/guardarSucursal') }}",
			"data": {
				"sucursal_id" : sucursal_id, 
				"_token": "{{ csrf_token() }}",
				}
		}).done(function(info){
			respuesta = info;
		}).always(function(){
			$("#container").html("");
			$("#sucursalsession").html("SUCURSAL: " + respuesta);
		});
	}
</script>