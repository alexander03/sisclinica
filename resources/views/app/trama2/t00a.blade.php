<?php
	use App\Trama2;
	$t00a = Trama2::find(1);
?>
<div id="mensajeError" style="display: none;">
	<div class="alert alert-danger">
		<button type="button" class="close">×</button>
		<strong>Por favor corrige los siguentes errores:</strong>
		<ul id="ulMensajeError"></ul>
	</div>
</div>
<form action="#" accept-charset="UTF-8" class="form-horizontal" id="formMantenimientoT00a">
    <div class="form-group">
		{!! Form::label('codigo1', 'Código IPRESS', array('class' => 'col-lg-3 col-md-3 col-sm-3 control-label')) !!}
		<div class="col-lg-3 col-md-3 col-sm-3">
			{!! Form::text('codigo1', $t00a == null ? '' : $t00a->codigo1, array('class' => 'form-control input-xs', 'id' => 'codigo1')) !!}
		</div>
		{!! Form::label('codigo2', 'Código UGIPRESS', array('class' => 'col-lg-3 col-md-3 col-sm-3 control-label')) !!}
		<div class="col-lg-3 col-md-3 col-sm-3">
			{!! Form::text('codigo2', $t00a == null ? '' : $t00a->codigo2, array('class' => 'form-control input-xs', 'id' => 'codigo2')) !!}
		</div>
	</div>
	<div class="form-group">
		{!! Form::label('consultoriosfi', 'Consultorios Físicos', array('class' => 'col-lg-3 col-md-3 col-sm-3 control-label')) !!}
		<div class="col-lg-3 col-md-3 col-sm-3">
			{!! Form::text('consultoriosfi', $t00a == null ? '' : $t00a->consultoriosfi, array('class' => 'form-control input-xs', 'id' => 'consultoriosfi', 'onkeypress' => 'return filterFloat(event,this);')) !!}
		</div>
		{!! Form::label('consultoriosfu', 'Consultorios Funcionales', array('class' => 'col-lg-3 col-md-3 col-sm-3 control-label')) !!}
		<div class="col-lg-3 col-md-3 col-sm-3">
			{!! Form::text('consultoriosfu', $t00a == null ? '' : $t00a->consultoriosfu, array('class' => 'form-control input-xs', 'id' => 'consultoriosfu', 'onkeypress' => 'return filterFloat(event,this);')) !!}
		</div>
	</div>
	<div class="form-group">
		{!! Form::label('camas', 'Camas Hospitalarias', array('class' => 'col-lg-3 col-md-3 col-sm-3 control-label')) !!}
		<div class="col-lg-3 col-md-3 col-sm-3">
			{!! Form::text('camas', $t00a == null ? '' : $t00a->camas, array('class' => 'form-control input-xs', 'id' => 'camas', 'onkeypress' => 'return filterFloat(event,this);')) !!}
		</div>
		{!! Form::label('medicost', 'Total de Médicos', array('class' => 'col-lg-3 col-md-3 col-sm-3 control-label')) !!}
		<div class="col-lg-3 col-md-3 col-sm-3">
			{!! Form::text('medicost', $t00a == null ? '' : $t00a->medicost, array('class' => 'form-control input-xs', 'id' => 'medicost', 'onkeypress' => 'return filterFloat(event,this);')) !!}
		</div>
	</div>
	<div class="form-group">
		{!! Form::label('medicoss', 'Médicos Serums', array('class' => 'col-lg-3 col-md-3 col-sm-3 control-label')) !!}
		<div class="col-lg-3 col-md-3 col-sm-3">
			{!! Form::text('medicoss', $t00a == null ? '' : $t00a->medicoss, array('class' => 'form-control input-xs', 'id' => 'medicoss', 'onkeypress' => 'return filterFloat(event,this);')) !!}
		</div>
		{!! Form::label('medicosr', 'Médicos Residentes', array('class' => 'col-lg-3 col-md-3 col-sm-3 control-label')) !!}
		<div class="col-lg-3 col-md-3 col-sm-3">
			{!! Form::text('medicosr', $t00a == null ? '' : $t00a->medicosr, array('class' => 'form-control input-xs', 'id' => 'medicosr', 'onkeypress' => 'return filterFloat(event,this);')) !!}
		</div>
	</div>
	<div class="form-group">
		{!! Form::label('enfermeras', 'Enfermeras', array('class' => 'col-lg-3 col-md-3 col-sm-3 control-label')) !!}
		<div class="col-lg-3 col-md-3 col-sm-3">
			{!! Form::text('enfermeras', $t00a == null ? '' : $t00a->enfermeras, array('class' => 'form-control input-xs', 'id' => 'enfermeras', 'onkeypress' => 'return filterFloat(event,this);')) !!}
		</div>
		{!! Form::label('odontologos', 'Odontólogos', array('class' => 'col-lg-3 col-md-3 col-sm-3 control-label')) !!}
		<div class="col-lg-3 col-md-3 col-sm-3">
			{!! Form::text('odontologos', $t00a == null ? '' : $t00a->odontologos, array('class' => 'form-control input-xs', 'id' => 'odontologos', 'onkeypress' => 'return filterFloat(event,this);')) !!}
		</div>
	</div>
	<div class="form-group">
		{!! Form::label('psicologos', 'Psicólogos', array('class' => 'col-lg-3 col-md-3 col-sm-3 control-label')) !!}
		<div class="col-lg-3 col-md-3 col-sm-3">
			{!! Form::text('psicologos', $t00a == null ? '' : $t00a->psicologos, array('class' => 'form-control input-xs', 'id' => 'psicologos', 'onkeypress' => 'return filterFloat(event,this);')) !!}
		</div>
		{!! Form::label('nutricionistas', 'Nutricionistas', array('class' => 'col-lg-3 col-md-3 col-sm-3 control-label')) !!}
		<div class="col-lg-3 col-md-3 col-sm-3">
			{!! Form::text('nutricionistas', $t00a == null ? '' : $t00a->nutricionistas, array('class' => 'form-control input-xs', 'id' => 'nutricionistas', 'onkeypress' => 'return filterFloat(event,this);')) !!}
		</div>
	</div>
	<div class="form-group">
		{!! Form::label('tecnologos', 'Tecnólogos Médicos', array('class' => 'col-lg-3 col-md-3 col-sm-3 control-label')) !!}
		<div class="col-lg-3 col-md-3 col-sm-3">
			{!! Form::text('tecnologos', $t00a == null ? '' : $t00a->tecnologos, array('class' => 'form-control input-xs', 'id' => 'tecnologos', 'onkeypress' => 'return filterFloat(event,this);')) !!}
		</div>
		{!! Form::label('obstetrices', 'Obtetrices', array('class' => 'col-lg-3 col-md-3 col-sm-3 control-label')) !!}
		<div class="col-lg-3 col-md-3 col-sm-3">
			{!! Form::text('obstetrices', $t00a == null ? '' : $t00a->obstetrices, array('class' => 'form-control input-xs', 'id' => 'obstetrices', 'onkeypress' => 'return filterFloat(event,this);')) !!}
		</div>
	</div>
	<div class="form-group">
		{!! Form::label('farmaceuticos', 'Farmacéuticos', array('class' => 'col-lg-3 col-md-3 col-sm-3 control-label')) !!}
		<div class="col-lg-3 col-md-3 col-sm-3">
			{!! Form::text('farmaceuticos', $t00a == null ? '' : $t00a->farmaceuticos, array('class' => 'form-control input-xs', 'id' => 'farmaceuticos', 'onkeypress' => 'return filterFloat(event,this);')) !!}
		</div>
		{!! Form::label('auxiliares', 'Auxiliares', array('class' => 'col-lg-3 col-md-3 col-sm-3 control-label')) !!}
		<div class="col-lg-3 col-md-3 col-sm-3">
			{!! Form::text('auxiliares', $t00a == null ? '' : $t00a->auxiliares, array('class' => 'form-control input-xs', 'id' => 'auxiliares', 'onkeypress' => 'return filterFloat(event,this);')) !!}
		</div>
	</div>
	<div class="form-group">
		{!! Form::label('otros', 'Otros Profesionales', array('class' => 'col-lg-3 col-md-3 col-sm-3 control-label')) !!}
		<div class="col-lg-3 col-md-3 col-sm-3">
			{!! Form::text('otros', $t00a == null ? '' : $t00a->otros, array('class' => 'form-control input-xs', 'id' => 'otros', 'onkeypress' => 'return filterFloat(event,this);')) !!}
		</div>
		{!! Form::label('ambulancias', 'Ambulancias', array('class' => 'col-lg-3 col-md-3 col-sm-3 control-label')) !!}
		<div class="col-lg-3 col-md-3 col-sm-3">
			{!! Form::text('ambulancias', $t00a == null ? '' : $t00a->ambulancias, array('class' => 'form-control input-xs', 'id' => 'ambulancias', 'onkeypress' => 'return filterFloat(event,this);')) !!}
		</div>
	</div>
	<div class="text-right">
		<button class="btn btn-success btn-sm" data-a="true" id="btnG" onclick="enviar2();" type="button"><i class="fa fa-check fa-lg"></i> Registrar</button>
		<button class="btn btn-warning btn-sm" onclick="cerrarModal();" type="button"><i class="fa fa-exclamation fa-lg"></i> Cancelar</button>
	</div>
</form>
<script type="text/javascript">
	$(document).ready(function() {
		configurarAnchoModal('850');
		$('#codigo1').focus();
	}); 

	$(document).on('click', '.close', function(e){
		$('#mensajeError').css('display', 'none');
	})

	function filterFloat(evt,input){
	    var key = window.Event ? evt.which : evt.keyCode;    
	    var chark = String.fromCharCode(key);
	    var tempValue = input.value+chark;
	    if(key >= 48 && key <= 57){
	        if(filter(tempValue)=== false){
	            return false;
	        }else{       
	            return true;
	        }
	    }else{
	          if(key == 8 || key == 13 || key == 0) {     
	              return true;              
	          }else if(key == 46){
	                if(filter(tempValue)=== false){
	                    return false;
	                }else{       
	                    return true;
	                }
	          }else{
	              return false;
	          }
	    }
	}

	function filter(__val__){
	    var preg = /^([0-9]+\.?[0-9]{0,0})$/; 
	    if(preg.test(__val__) === true){
	        return true;
	    }else{
	       return false;
	    }	    
	}

	function enviar2() {
		var datos = $('#formMantenimientoT00a').serialize();
		var mensaje = '';
		if($('#codigo1').val() == '') {
			mensaje += '<li>Código IPRESS es requerido.</li>';
		}
		if($('#codigo2').val() == '') {
			mensaje += '<li>Código UGIPRESS es requerido.</li>';
		}
		if($('#consultoriosfi').val() == '') {
			mensaje += '<li>Total de Consultorios Físicos es requerido.</li>';
		}
		if($('#consultoriosfu').val() == '') {
			mensaje += '<li>Total de Consultorios Funcionales es requerido.</li>';
		}
		if($('#camas').val() == '') {
			mensaje += '<li>Total de Camas es requerido.</li>';
		}
		if($('#medicost').val() == '') {
			mensaje += '<li>Total de Médicos es requerido.</li>';
		}
		if($('#medicoss').val() == '') {
			mensaje += '<li>Total de Médicos Serums es requerido.</li>';
		}
		if($('#medicosr').val() == '') {
			mensaje += '<li>Total de Médicos Residentes es requerido.</li>';
		}
		if($('#enfermeras').val() == '') {
			mensaje += '<li>Total de Enfermeras es requerido.</li>';
		}
		if($('#odontologos').val() == '') {
			mensaje += '<li>Total de Odontólogos es requerido.</li>';
		}
		if($('#psicologos').val() == '') {
			mensaje += '<li>Total de Psicólogos es requerido.</li>';
		}
		if($('#nutricionistas').val() == '') {
			mensaje += '<li>Total de Nutricionistas es requerido.</li>';
		}
		if($('#tecnologos').val() == '') {
			mensaje += '<li>Total de Tecnólogos es requerido.</li>';
		}
		if($('#obstetrices').val() == '') {
			mensaje += '<li>Total de Obtetrices es requerido.</li>';
		}
		if($('#farmaceuticos').val() == '') {
			mensaje += '<li>Total de Farmacéuticos es requerido.</li>';
		}
		if($('#auxiliares').val() == '') {
			mensaje += '<li>Total de auxiliares es requerido.</li>';
		}
		if($('#otros').val() == '') {
			mensaje += '<li>Total de Otros Profesionales es requerido.</li>';
		}
		if($('#ambulancias').val() == '') {
			mensaje += '<li>Total de Ambulancias es requerido.</li>';
		}
		if(mensaje === '') {
			$.ajax({
				url: 'trama2/registrardatos',
				type: 'GET',
				data: datos,
				beforeSend: function() {
					$('#btnG').attr('disabled', 'disabled');
					$('#btnG').html('Cargando...');
				}
			})
			.done(function(e) {
				alert('Datos Establecidos Correctamente.');
				cerrarModal();
			})
			.fail(function() {
				alert('No se ha podido editar. Vuelve a Intentar.');
			});
			
		} else {
			$('#mensajeError').css('display', '');
			$('#ulMensajeError').html(mensaje);
		}
	}
</script>