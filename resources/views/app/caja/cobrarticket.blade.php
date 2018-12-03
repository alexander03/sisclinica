{!! Form::model($movimiento, $formData) !!}	
	<!-- DATOS DEL TICKET -->
	<div id="divMensajeError{!! $entidad !!}"></div>
	<div class="form-group">
	    {!! Form::label('numero', 'Nro. Doc.:', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
	    {!! Form::label('numero', $movimiento->serie.'-'.$movimiento->numero, array('class' => 'col-lg-8 col-md-8 col-sm-8 control-label', 'style' => 'font-weight:normal;text-align:left;text-align:left')) !!}
	</div>
	<div class="form-group">
		{!! Form::label('siniestro', 'Siniestro:', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
		{!! Form::label('siniestro', $movimiento->comentario, array('class' => 'col-lg-8 col-md-8 col-sm-8 control-label', 'style' => 'font-weight:normal;text-align:left')) !!}
	</div>
	<div class="form-group">
		{!! Form::label('fecha', 'Fecha:', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
		{!! Form::label('siniestro', $movimiento->fecha, array('class' => 'col-lg-8 col-md-8 col-sm-8 control-label', 'style' => 'font-weight:normal;text-align:left')) !!}
	</div>
	<div class="form-group">
		{!! Form::label('paciente', 'Paciente:', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
		{!! Form::label('siniestro', $movimiento->persona->nombres . ' ' . $movimiento->persona->apellidopaterno . ' ' . $movimiento->persona->apellidomaterno, array('class' => 'col-lg-8 col-md-8 col-sm-8 control-label', 'style' => 'font-weight:normal;text-align:left')) !!}
	</div>
	<div class="form-group">
		{!! Form::label('tipomovimiento', 'Tipo de movimiento:', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
		{!! Form::label('siniestro', $movimiento->tipomovimiento->nombre, array('class' => 'col-lg-8 col-md-8 col-sm-8 control-label', 'style' => 'font-weight:normal;text-align:left')) !!}
	</div>
	<div class="form-group">
		{!! Form::label('total', 'Total:', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
		{!! Form::label('siniestro', $movimiento->total, array('class' => 'col-lg-8 col-md-8 col-sm-8 control-label', 'style' => 'font-weight:normal;text-align:left')) !!}
	</div>
	<div class="form-group">
		{!! Form::label('doctor', 'Doctor:', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
		{!! Form::label('siniestro', $movimiento->doctor->nombres . ' ' . $movimiento->doctor->apellidopaterno . ' ' . $movimiento->doctor->apellidomaterno, array('class' => 'col-lg-8 col-md-8 col-sm-8 control-label', 'style' => 'font-weight:normal;text-align:left')) !!}
	</div>
	<div class="form-group">
		{!! Form::label('plan', 'Plan:', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
		{!! Form::label('siniestro', $movimiento->plan->nombre, array('class' => 'col-lg-8 col-md-8 col-sm-8 control-label', 'style' => 'font-weight:normal;text-align:left')) !!}
	</div>
	<!-- OPCIONES -->
	<div class="form-group">
        {!! Form::label('plan', 'Generar:', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
		<div class="col-lg-2 col-md-2 col-sm-2">
			{!! Form::hidden('comprobante', 'S', array('id' => 'comprobante')) !!}
            <input readonly="readonly" disabled="disabled" checked="checked" type="checkbox" onchange="mostrarDatoCaja(0,this.checked)" id="boleta" class="col-lg-2 col-md-2 col-sm-2 control-label" />
            {!! Form::label('boleta', 'Comprobante', array('class' => 'col-lg-10 col-md-10 col-sm-10 control-label')) !!}
            {!! Form::hidden('pagar', 'S', array('id' => 'pagar')) !!}    
			<input readonly="readonly" disabled="disabled" checked="checked" type="checkbox" onchange="mostrarDatoCaja(this.checked,0)" id="pago" class="col-lg-2 col-md-2 col-sm-2 control-label datocaja" />
            {!! Form::label('pago', 'Pago', array('class' => 'col-lg-10 col-md-10 col-sm-10 control-label datocaja')) !!}
		</div>
        {!! Form::label('formapago', 'Forma Pago:', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label datocaja caja')) !!}
		<div class="col-lg-4 col-md-4 col-sm-4">
			{!! Form::select('formapago', $cboFormaPago, null, array('class' => 'form-control input-xs datocaja caja', 'id' => 'formapago', 'onchange'=>'validarFormaPago(this.value);')) !!}
		</div>
		<br><br><br>
        {!! Form::label('caja_id', 'Caja:', array('class' => 'col-lg-1 col-md-1 col-sm-1 control-label datocaja caja')) !!}
		<div class="col-lg-3 col-md-3 col-sm-3">
			<select name="caja_id" id="caja_id" class="form-control input-xs datocaja caja">
				@foreach($cboCaja as $caja)
				<option value="{{ $caja->id }}">{{ $caja->nombre }}</option>
				@endforeach
			</select>
		</div>	
		{!! Form::label('tipodocumento', 'Tipo de documento:', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label caja')) !!}
		<div class="col-lg-4 col-md-4 col-sm-4">
			<select name="tipodocumento" id="tipodocumento" class="col-lg-8 col-md-8 col-sm-8 control-label input-xs form-control caja">
				<option value="Boleta">Boleta</option>
				<option value="Factura">Factura</option>
			</select>
		</div>	
    </div>
	<div class="form-group datocaja" id="divTarjeta" style="display: none">
        {!! Form::label('tipotarjeta', 'Tarjeta:', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
		<div class="col-lg-2 col-md-2 col-sm-2">
			{!! Form::select('tipotarjeta', $cboTipoTarjeta, null, array('class' => 'form-control input-xs', 'id' => 'tipotarjeta')) !!}
		</div>
        {!! Form::label('tipotarjeta2', 'Tipo Tarjeta:', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
		<div class="col-lg-2 col-md-2 col-sm-2">
			{!! Form::select('tipotarjeta2', $cboTipoTarjeta2, null, array('class' => 'form-control input-xs', 'id' => 'tipotarjeta2')) !!}
		</div>
        {!! Form::label('nroref', 'Nro. Op.:', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
        <div class="col-lg-2 col-md-2 col-sm-2">
            {!! Form::text('nroref', null, array('class' => 'form-control input-xs', 'id' => 'nroref')) !!}
        </div>
	</div>
	{!! Form::hidden('id', $movimiento->id) !!}
	{!! Form::button('<i class="fa fa-check fa-lg"></i> '.$boton, array('class' => 'btn btn-success btn-sm', 'id' => 'btnGuardar', 'onclick' => 'guardar(\''.$entidad.'\', this)')) !!}
	{!! Form::button('<i class="fa fa-exclamation fa-lg"></i> Cancelar', array('class' => 'btn btn-warning btn-sm', 'id' => 'btnCancelar'.$entidad, 'onclick' => 'cerrarModal();')) !!}
{!! Form::close() !!}
<script type="text/javascript">
$(document).ready(function() {
	configurarAnchoModal('600');
	init(IDFORMMANTENIMIENTO+'{!! $entidad !!}', 'B', '{!! $entidad !!}');
}); 

function mostrarDatoCaja(check,check2){
    if(check==0){
        check = $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="pago"]').is(":checked");
    }
    if(check2==0){
        check2 = $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="boleta"]').is(":checked");
    }
    if(check2){//CON BOLETA
        $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="comprobante"]').val('S');
        $(".datocaja").css("display","");
        if($(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[name="tipodocumento"]').val()=="Factura"){
            $(".datofactura").css("display","");
        }else{
            $(".datofactura").css("display","none");
        }
        if(check){
            $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="pagar"]').val('S');
            $(".caja").css("display","");
            $(".descuento").css("display","none");
            $(".descuentopersonal").css('display','none');
            $("#descuentopersonal").val('N');
        }else{
            $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="pagar"]').val('N');
            $(".caja").css("display","none");
            $(".descuento").css("display","");
        }
        validarFormaPago($(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="formapago"]').val());
    }else{
        $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="comprobante"]').val('N');
        $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="pagar"]').val('N');
        $(".datocaja").css("display","none");
        $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="pago"]').attr("checked",true);
        validarFormaPago($(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="formapago"]').val());
    }
}

function validarFormaPago(forma){
    if(forma=="Tarjeta"){
        $(IDFORMMANTENIMIENTO + '{!! $entidad !!} div[id="divTarjeta"]').css("display","");
    }else{
        $(IDFORMMANTENIMIENTO + '{!! $entidad !!} div[id="divTarjeta"]').css("display","none");
    }
}

</script>