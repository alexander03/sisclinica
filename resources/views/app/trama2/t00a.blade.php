<form action="#" accept-charset="UTF-8" class="form-horizontal" id="formMantenimientoT00a">
    <div class="form-group">
		{!! Form::label('codigo1', 'Código IPRESS', array('class' => 'col-lg-3 col-md-3 col-sm-3 control-label')) !!}
		<div class="col-lg-3 col-md-3 col-sm-3">
			{!! Form::text('codigo1', '', array('class' => 'form-control input-xs', 'id' => 'codigo1')) !!}
		</div>
		{!! Form::label('codigo2', 'Código UGIPRESS', array('class' => 'col-lg-3 col-md-3 col-sm-3 control-label')) !!}
		<div class="col-lg-3 col-md-3 col-sm-3">
			{!! Form::text('codigo2', '', array('class' => 'form-control input-xs', 'id' => 'codigo2')) !!}
		</div>
	</div>
	<div class="form-group">
		{!! Form::label('consultoriosfi', 'Consultorios Físicos', array('class' => 'col-lg-3 col-md-3 col-sm-3 control-label')) !!}
		<div class="col-lg-3 col-md-3 col-sm-3">
			{!! Form::text('consultoriosfi', '', array('class' => 'form-control input-xs', 'id' => 'consultoriosfi')) !!}
		</div>
		{!! Form::label('consultoriosfu', 'Consultorios Funcionales', array('class' => 'col-lg-3 col-md-3 col-sm-3 control-label')) !!}
		<div class="col-lg-3 col-md-3 col-sm-3">
			{!! Form::text('consultoriosfu', '', array('class' => 'form-control input-xs', 'id' => 'consultoriosfu')) !!}
		</div>
	</div>
	<div class="form-group">
		{!! Form::label('camas', 'Camas Hospitalarias', array('class' => 'col-lg-3 col-md-3 col-sm-3 control-label')) !!}
		<div class="col-lg-3 col-md-3 col-sm-3">
			{!! Form::text('camas', '', array('class' => 'form-control input-xs', 'id' => 'camas')) !!}
		</div>
		{!! Form::label('medicost', 'Total de Médicos', array('class' => 'col-lg-3 col-md-3 col-sm-3 control-label')) !!}
		<div class="col-lg-3 col-md-3 col-sm-3">
			{!! Form::text('medicost', '', array('class' => 'form-control input-xs', 'id' => 'medicost')) !!}
		</div>
	</div>
	<div class="form-group">
		{!! Form::label('medicoss', 'Médicos Serums', array('class' => 'col-lg-3 col-md-3 col-sm-3 control-label')) !!}
		<div class="col-lg-3 col-md-3 col-sm-3">
			{!! Form::text('medicoss', '', array('class' => 'form-control input-xs', 'id' => 'medicoss')) !!}
		</div>
		{!! Form::label('medicosr', 'Médicos Residentes', array('class' => 'col-lg-3 col-md-3 col-sm-3 control-label')) !!}
		<div class="col-lg-3 col-md-3 col-sm-3">
			{!! Form::text('medicosr', '', array('class' => 'form-control input-xs', 'id' => 'medicosr')) !!}
		</div>
	</div>
	<div class="form-group">
		{!! Form::label('enfermeras', 'Enfermeras', array('class' => 'col-lg-3 col-md-3 col-sm-3 control-label')) !!}
		<div class="col-lg-3 col-md-3 col-sm-3">
			{!! Form::text('enfermeras', '', array('class' => 'form-control input-xs', 'id' => 'enfermeras')) !!}
		</div>
		{!! Form::label('enfermeras', 'Odontólogos', array('class' => 'col-lg-3 col-md-3 col-sm-3 control-label')) !!}
		<div class="col-lg-3 col-md-3 col-sm-3">
			{!! Form::text('enfermeras', '', array('class' => 'form-control input-xs', 'id' => 'enfermeras')) !!}
		</div>
	</div>
	<div class="form-group">
		{!! Form::label('psicologos', 'Psicólogos', array('class' => 'col-lg-3 col-md-3 col-sm-3 control-label')) !!}
		<div class="col-lg-3 col-md-3 col-sm-3">
			{!! Form::text('psicologos', '', array('class' => 'form-control input-xs', 'id' => 'psicologos')) !!}
		</div>
		{!! Form::label('nutricionistas', 'Nutricionistas', array('class' => 'col-lg-3 col-md-3 col-sm-3 control-label')) !!}
		<div class="col-lg-3 col-md-3 col-sm-3">
			{!! Form::text('nutricionistas', '', array('class' => 'form-control input-xs', 'id' => 'nutricionistas')) !!}
		</div>
	</div>
	<div class="form-group">
		{!! Form::label('tecnologos', 'Tecnólogos Médicos', array('class' => 'col-lg-3 col-md-3 col-sm-3 control-label')) !!}
		<div class="col-lg-3 col-md-3 col-sm-3">
			{!! Form::text('tecnologos', '', array('class' => 'form-control input-xs', 'id' => 'tecnologos')) !!}
		</div>
		{!! Form::label('obstetrices', 'Obtetrices', array('class' => 'col-lg-3 col-md-3 col-sm-3 control-label')) !!}
		<div class="col-lg-3 col-md-3 col-sm-3">
			{!! Form::text('obstetrices', '', array('class' => 'form-control input-xs', 'id' => 'obstetrices')) !!}
		</div>
	</div>
	<div class="form-group">
		{!! Form::label('farmaceuticos', 'Farmacéuticos', array('class' => 'col-lg-3 col-md-3 col-sm-3 control-label')) !!}
		<div class="col-lg-3 col-md-3 col-sm-3">
			{!! Form::text('farmaceuticos', '', array('class' => 'form-control input-xs', 'id' => 'farmaceuticos')) !!}
		</div>
		{!! Form::label('auxiliares', 'Auxiliares', array('class' => 'col-lg-3 col-md-3 col-sm-3 control-label')) !!}
		<div class="col-lg-3 col-md-3 col-sm-3">
			{!! Form::text('auxiliares', '', array('class' => 'form-control input-xs', 'id' => 'auxiliares')) !!}
		</div>
	</div>
	<div class="form-group">
		{!! Form::label('otros', 'Otros Profesionales', array('class' => 'col-lg-3 col-md-3 col-sm-3 control-label')) !!}
		<div class="col-lg-3 col-md-3 col-sm-3">
			{!! Form::text('otros', '', array('class' => 'form-control input-xs', 'id' => 'otros')) !!}
		</div>
		{!! Form::label('ambulancias', 'Ambulancias', array('class' => 'col-lg-3 col-md-3 col-sm-3 control-label')) !!}
		<div class="col-lg-3 col-md-3 col-sm-3">
			{!! Form::text('ambulancias', '', array('class' => 'form-control input-xs', 'id' => 'ambulancias')) !!}
		</div>
	</div>
	<div class="text-right">
		<button class="btn btn-success btn-sm" data-a="true" id="btnGuardar" onclick="enviar2();" type="button"><i class="fa fa-check fa-lg"></i> Registrar</button>
		<button class="btn btn-warning btn-sm" id="btnCancelarMovimiento" onclick="cerrarModal();" type="button"><i class="fa fa-exclamation fa-lg"></i> Cancelar</button>
	</div>
</form>
<script type="text/javascript">
	$(document).ready(function() {
		configurarAnchoModal('850');
		$('#codigo1').focus();
	}); 
	function enviar2() {
		var datos = $('#formMantenimientoT00a').serialize();
	}
</script>