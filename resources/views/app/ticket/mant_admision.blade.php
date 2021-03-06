
<?php
if(!is_null($ticket)){
    $plan=$ticket->plan->nombre;
    $plan_id=$ticket->plan_id;
    $tipoplan=$ticket->plan->tipo;
    $fecha=$ticket->fecha;
    $person_id=$ticket->persona_id;
    $dni=$ticket->persona->dni;
    $paciente=$ticket->persona->apellidopaterno.' '.$ticket->persona->apellidomaterno.' '.$ticket->persona->nombres;
    $coa=$ticket->subtotal;
    $deducible=$ticket->igv;
    $historia_id="";
    $numero_historia="";
    $referido_id=$ticket->doctor_id;
    if($ticket->doctor_id>0)
        $referido=$ticket->doctor->apellidopaterno.' '.$ticket->doctor->apellidomaterno.' '.$ticket->doctor->nombres;
    else
        $referido="";
}else{
    $plan="";
    $plan_id="";
    $tipoplan="";
    $fecha=date("Y-m-d");
    $person_id="";
    $dni="";
    $paciente="";
    $coa="";
    $deducible="";
    $historia_id="";
    $numero_historia="";
    $referido_id=9229;
    $referido="SIN REFERIDO";
}
?>
<style>
.tr_hover{
	color:red;
}
.form-group{
    margin-bottom: 8px !important;
}
.modal-body {
    height: 500px;
    overflow-y: auto;
}
</style>
<div id="divMensajeError{!! $entidad !!}"></div>
{!! Form::model($ticket, $formData) !!}	
	{!! Form::hidden('listar', $listar, array('id' => 'listar')) !!}
    {!! Form::hidden('listServicio', null, array('id' => 'listServicio')) !!}
    <div class="row">
        <div class="col-lg-6 col-md-6 col-sm-6">
            <div class="form-group">
        		{!! Form::label('fecha', 'Fecha:', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
        		<div class="col-lg-3 col-md-3 col-sm-3">
        			{!! Form::date('fecha', $fecha, array('class' => 'form-control input-xs', 'id' => 'fecha')) !!}
        		</div>
                {!! Form::label('numero', 'Nro:', array('class' => 'col-lg-1 col-md-1 col-sm-1 control-label')) !!}
        		<div class="col-lg-2 col-md-2 col-sm-2">
        			{!! Form::text('numero', $numero, array('class' => 'form-control input-xs', 'id' => 'numero', 'readonly' => 'true')) !!}
        		</div>
                <div style ="display:none;">
                    {!! Form::label('manual', 'Manual:', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
                    <div class="col-lg-1 col-md-1 col-sm-1">
                        {!! Form::hidden('manual', 'N', array('id' => 'manual')) !!}
                        <input type="checkbox" onclick="Manual(this.checked)" />
                    </div>
                </div>
        	</div>
            <div class="form-group">
        		{!! Form::label('paciente', 'Paciente:', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
        		<div class="col-lg-7 col-md-7 col-sm-7">
                {!! Form::hidden('person_id', $person_id, array('id' => 'person_id')) !!}
                {!! Form::hidden('dni', $dni, array('id' => 'dni')) !!}
        		{!! Form::text('paciente', $paciente, array('class' => 'form-control input-xs', 'id' => 'paciente', 'placeholder' => 'Ingrese Paciente')) !!}
        		</div>
                <div class="col-lg-1 col-md-1 col-sm-1">
                    {!! Form::button('<i class="fa fa-file fa-lg"></i>', array('class' => 'btn btn-info btn-xs', 'onclick' => 'modal (\''.URL::route('historia.create', array('listar'=>'SI','modo'=>'popup')).'\', \'Nueva Historia\', this);', 'title' => 'Nueva Historia')) !!}
        		</div>
                <div class="col-lg-1 col-md-1 col-sm-1">
                    {!! Form::button('<i class="fa fa-edit fa-lg"></i>', array('class' => 'btn btn-danger btn-xs', 'onclick' => 'solicitarHistoria();', 'title' => 'Solicitar Historia')) !!}
                </div>
        	</div>
            <div class="form-group">
        		{!! Form::label('numero', 'Historia:', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
        		<div class="col-lg-3 col-md-3 col-sm-3">
                    {!! Form::hidden('historia_id', $historia_id, array('id' => 'historia_id')) !!}
        			{!! Form::text('numero_historia', $numero_historia, array('class' => 'form-control input-xs', 'id' => 'numero_historia', 'readonly' => 'true')) !!}
        		</div>
                {!! Form::label('tipopaciente', 'Tipo Paciente:', array('class' => 'col-lg-3 col-md-3 col-sm-3 control-label')) !!}
        		<div class="col-lg-3 col-md-3 col-sm-3">
        			{!! Form::select('tipopaciente', $cboTipoPaciente, null, array('class' => 'form-control input-xs', 'id' => 'tipopaciente')) !!}
        		</div>
        	</div>
            <div class="form-group">
                {!! Form::label('plan', 'Plan:', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
        		<div class="col-lg-9 col-md-9 col-sm-9">
                    {!! Form::hidden('tipoplan', $tipoplan, array('id' => 'tipoplan')) !!}
                    {!! Form::hidden('plan_id', $plan_id, array('id' => 'plan_id')) !!}
        			{!! Form::text('plan', $plan, array('class' => 'form-control input-xs', 'id' => 'plan')) !!}
        		</div>
                {!! Form::label('soat', 'Soat:', array('class' => 'col-lg-1 col-md-1 col-sm-1 control-label', 'style' => 'display:none')) !!}
                <div class="col-lg-1 col-md-1 col-sm-1">
                    {!! Form::hidden('soat', 'N', array('id' => 'soat')) !!}
                    <input type="checkbox" onclick="Soat(this.checked)" style="display: none;" />
                </div>
            </div>
            <div class="form-group">
        		{!! Form::label('deducible', 'Deducible:', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
        		<div class="col-lg-3 col-md-3 col-sm-3">
        			{!! Form::text('deducible', $deducible, array('class' => 'form-control input-xs', 'id' => 'deducible')) !!}
        		</div>
                {!! Form::label('coa', 'Coaseguro:', array('class' => 'col-lg-3 col-md-3 col-sm-3 control-label')) !!}
        		<div class="col-lg-3 col-md-3 col-sm-3">
        			{!! Form::text('coa', $coa, array('class' => 'form-control input-xs', 'id' => 'coa')) !!}
        		</div>
                {!! Form::label('sctr', 'Sctr:', array('class' => 'col-lg-1 col-md-1 col-sm-1 control-label', 'style' => 'display:none')) !!}
                <div class="col-lg-1 col-md-1 col-sm-1">
                    {!! Form::hidden('sctr', 'N', array('id' => 'sctr')) !!}
                    <input type="checkbox" onclick="Sctr(this.checked)" style="display: none;" />
                </div>
        	</div>
            <div class="form-group">
                <div class="col-lg-12 col-md-12 col-sm-12">
                    {!! Form::label('formapago', 'Forma Pago:', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label datocaja caja input-sm')) !!}
                    <label id="divcbx0" class="checkbox-inline" style="color:red" onclick="divFormaPago('0', '0')">
                        <input style="display: none;" type="checkbox" id="cbx0">Efectivo
                    </label>
                    <label id="divcbx1" class="checkbox-inline" onclick="divFormaPago('1', '1')">
                        <input style="display: none;" type="checkbox" id="cbx1">Visa
                    </label>
                    <label id="divcbx2" class="checkbox-inline" onclick="divFormaPago('2', '1')">
                        <input style="display: none;" type="checkbox" id="cbx2">Master
                    </label>
                </div>          
            </div>
            <div class="row">
                <div class="col-lg-4 col-md-4 col-sm-4">            
                    <div class="input-group form-control">
                        <span class="input-group-addon input-xs">EFECTIVO</span>
                        <input onkeypress="return filterFloat(event,this);" onkeyup="calcularTotalPago();" name="efectivo" id="efectivo" type="text" class="form-control input-xs">
                    </div>
                    <div class="input-group form-control">
                        <span class="input-group-addon input-xs">VISA.</span>
                        <input onkeypress="return filterFloat(event,this);" onkeyup="calcularTotalPago();" name="visa" id="visa" type="text" class="form-control input-xs" readonly="">
                        <span style="display:none;" class="input-group-addon input-xs">N°</span>
                        <input style="display:none;" onkeypress="return filterFloat(event,this);" name="numvisa" id="numvisa" type="text" class="form-control input-xs" readonly="">
                    </div>
                    <div class="input-group form-control">
                        <span class="input-group-addon input-xs">MAST.</span>
                        <input onkeypress="return filterFloat(event,this);" onkeyup="calcularTotalPago();" name="master" id="master" type="text" class="form-control input-xs" readonly="">
                        <span style="display:none;" class="input-group-addon input-xs">N°</span>
                        <input style="display:none;" onkeypress="return filterFloat(event,this);" name="nummaster" id="nummaster" type="text" class="form-control input-xs" readonly="">
                    </div>  
                </div>  
                <div class="col-lg-4 col-md-4 col-sm-4">            
                    <div class="input-group form-control">
                        <span class="input-group-addon input-xs">TOTAL</span>
                        <input name="total2" id="total2" type="text" class="form-control input-xs" readonly="" value="0.000">
                    </div>
                    <b id="mensajeMontos" style="color:red"></b>
                </div>
                <div class="col-lg-4 col-md-4 col-sm-4">
                    {!! Form::label('plan', 'Editar Reparticion:', array('class' => 'col-lg-6 col-md-6 col-sm-6 control-label')) !!}
                    <div class="col-lg-1 col-md-1 col-sm-1">
                        {!! Form::hidden('editarprecio', 'N', array('id' => 'editarprecio')) !!}
                        <input type="checkbox" onclick="editarPrecio(this.checked)" />
                    </div>
                </div>      
            </div>
            <br>
            <div class="form-group" style="display: none;">
                {!! Form::label('plan', 'Generar:', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label', 'style' => 'display:none;')) !!}
        		<div class="col-lg-2 col-md-2 col-sm-2">
        			{!! Form::hidden('comprobante', 'S', array('id' => 'comprobante')) !!}
                    <input type="checkbox" onchange="mostrarDatoCaja(0,this.checked)" checked id="boleta" class="col-lg-2 col-md-2 col-sm-2 control-label" style="display: none;" />
                    {!! Form::label('boleta', 'Comprobante', array('class' => 'col-lg-10 col-md-10 col-sm-10 control-label')) !!}
                    {!! Form::hidden('pagar', 'S', array('id' => 'pagar')) !!}    
        			<input type="checkbox" onchange="mostrarDatoCaja(this.checked,0)" checked id="pago" class="col-lg-2 col-md-2 col-sm-2 control-label datocaja" style="display: none;" />
                    {!! Form::label('pago', 'Pago', array('class' => 'col-lg-10 col-md-10 col-sm-10 control-label datocaja', 'style' => 'display:none')) !!}
        		</div>
                {!! Form::label('formapago', 'Forma Pago:', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label datocaja caja')) !!}
        		<div class="col-lg-2 col-md-2 col-sm-2">
        			{!! Form::select('formapago', $cboFormaPago, null, array('class' => 'form-control input-xs datocaja caja', 'id' => 'formapago', 'onchange'=>'validarFormaPago(this.value);')) !!}
        		</div>
                <div style ="display:none;">
                    {!! Form::label('caja_id', 'Caja:', array('class' => 'col-lg-1 col-md-1 col-sm-1 control-label datocaja caja')) !!}
                    <div class="col-lg-3 col-md-3 col-sm-3">
                        {!! Form::select('caja_id', $cboCaja, $idcaja, array('class' => 'form-control input-xs datocaja caja', 'id' => 'caja_id', 'readonly' => 'true')) !!}
                    </div>
                </div>
            </div>
            <div class="form-group datocaja" id="divTarjeta" style="display: none;">
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

        	<div class="form-group">
                {!! Form::label('plan', 'Boletear Todo:', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label', 'style'=>'display:none')) !!}
        		<div class="col-lg-1 col-md-1 col-sm-1" style="display: none;">
                    {!! Form::hidden('boletear', 'N', array('id' => 'boletear')) !!}
        			<input type="checkbox" onclick="boletearTodoCaja(this.checked)" />
        		</div>
                {!! Form::label('personal', 'Pendiente Personal / Medico:', array('class' => 'col-lg-3 col-md-3 col-sm-3 control-label descuento', 'style' => 'display:none' )) !!}
                <div class="col-lg-1 col-md-1 col-sm-1">
                    {!! Form::hidden('descuentopersonal', 'N', array('id' => 'descuentopersonal')) !!}
                    <input type="checkbox" class="descuento" style="display: none;" onclick="editarDescuentoPersonal(this.checked)" />
                </div>
        		<div class="col-lg-12 col-md-12 col-sm-12 text-right">        			
                    {!! Form::button('<i class="fa fa-save fa-lg"></i> Guardado Temporal', array('class' => 'btn btn-info btn-sm', 'onclick' => 'guardarTemporal();')) !!}
                    {!! Form::button('<i class="fa fa-eye fa-lg"></i> Mostrar Temporal', array('class' => 'btn btn-primary btn-sm', 'onclick' => 'mostrarTemporal();')) !!}
                    {!! Form::button('<i class="fa fa-check fa-lg"></i> '.$boton, array('class' => 'btn btn-success btn-sm', 'id' => 'btnGuardar', 'onclick' => '$(\'#listServicio\').val(carro);$(\'#movimiento_id\').val(carroDoc);guardarPago(\''.$entidad.'\', this);')) !!}
                    {!! Form::button('<i class="fa fa-exclamation fa-lg"></i> Cancelar', array('class' => 'btn btn-warning btn-sm', 'id' => 'btnCancelar'.$entidad, 'onclick' => 'cerrarModal();')) !!}   			
                    
        		</div>
        	</div>
            <div class="form-group descuentopersonal" style="display: none">
                {!! Form::label('personal', 'Personal:', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
                <div class="col-lg-7 col-md-7 col-sm-7">
                {!! Form::hidden('personal_id', null, array('id' => 'personal_id')) !!}
                {!! Form::text('personal', null, array('class' => 'form-control input-xs', 'id' => 'personal', 'placeholder' => 'Ingrese Personal')) !!}
                </div>
            </div>
         </div>
         <div class="col-lg-6 col-md-6 col-sm-6">
            <div class="form-group datocaja">
                {!! Form::label('tipodocumento', 'Tipo Doc.:', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
        		<div class="col-lg-3 col-md-3 col-sm-3">
        			{!! Form::select('tipodocumento', $cboTipoDocumento + array('Ticket' => 'Ticket'), 'Ticket', array('class' => 'xyz form-control input-xs', 'id' => 'tipodocumento', 'onchange' => 'generarNumero()')) !!}
        		</div>
                {!! Form::label('numeroventa', 'Nro.:', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
        		<div class="col-lg-2 col-md-2 col-sm-2">
        			{!! Form::text('serieventa', $serie, array('class' => 'form-control input-xs datocaja', 'id' => 'serieventa', 'readonly' => 'true')) !!}
        		</div>
                <div class="col-lg-3 col-md-3 col-sm-3">
        			{!! Form::text('numeroventa', $numeroventa, array('class' => 'form-control input-xs', 'id' => 'numeroventa', 'readonly' => 'true')) !!}
        		</div>
        	</div>
            <div class="form-group" id="opcEmpresa" style="display: none;">
                {!! Form::label('ccruc', 'Ruc:', array('class' => 'col-lg-1 col-md-1 col-sm-1 control-label input-sm')) !!}
                <div class="col-lg-3 col-md-3 col-sm-3">
                    {!! Form::text('ccruc','', array('class' => 'form-control input-xs datocaja', 'id' => 'ccruc', 'maxlength' => '11')) !!}
                </div> 
                {!! Form::label('ccrazon', 'Razón:', array('class' => 'col-lg-1 col-md-1 col-sm-1 control-label input-sm')) !!}
                <div class="col-lg-3 col-md-3 col-sm-3">
                    {!! Form::text('ccrazon','', array('class' => 'form-control input-xs datocaja', 'id' => 'ccrazon', 'readonly' => 'readonly')) !!}
                </div> 
                {!! Form::label('ccdireccion', 'Direcc:', array('class' => 'col-lg-1 col-md-1 col-sm-1 control-label input-sm')) !!}
                <div class="col-lg-3 col-md-3 col-sm-3">
                    {!! Form::text('ccdireccion','-', array('class' => 'form-control input-xs datocaja', 'id' => 'ccdireccion')) !!}
                </div>  
            </div>
            <div class="form-group datocaja datofactura" style="display: none;">
                {!! Form::label('ruc', 'RUC:', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label', 'style' => 'display:none;')) !!}
        		<div class="col-lg-2 col-md-2 col-sm-2">
        			{!! Form::text('ruc', null, array('class' => 'form-control input-xs', 'id' => 'ruc', 'style' => 'display:none;')) !!}
        		</div>
                {!! Form::label('razon', 'Razon:', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label', 'style' => 'display:none;')) !!}
        		<div class="col-lg-6 col-md-6 col-sm-6">
        			{!! Form::text('razon', null, array('class' => 'form-control input-xs datocaja', 'id' => 'razon', 'style' => 'display:none;')) !!}
        		</div>
            </div>
            {{--<div class="form-group datocaja datofactura" style="display: none;">
                {!! Form::label('direccion', 'Direccion:', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
        		<div class="col-lg-10 col-md-10 col-sm-10">
        			{!! Form::text('direccion', null, array('class' => 'form-control input-xs', 'id' => 'direccion')) !!}
        		</div>
        	</div>--}}
            <div class="form-group">
                {!! Form::label('referido', 'Referido:', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
                <div class="col-lg-10 col-md-10 col-sm-10">
                    {!! Form::hidden('referido_id', $referido_id, array('id' => 'referido_id')) !!}
                    {!! Form::text('referido', $referido, array('class' => 'form-control input-xs', 'id' => 'referido')) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('tiposervicio', 'Tipo:', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
        		<div class="col-lg-3 col-md-3 col-sm-3">
        			{!! Form::select('tiposervicio', $cboTipoServicio, null, array('class' => 'form-control input-xs', 'id' => 'tiposervicio')) !!}
        		</div>
                {!! Form::label('descripcion', 'Servicio:', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
        		<div class="col-lg-5 col-md-5 col-sm-5">
        			{!! Form::text('descripcion', null, array('class' => 'form-control input-xs', 'id' => 'descripcion', 'onkeypress' => '')) !!}
        		</div>
            </div>
            <div class="form-group col-lg-12 col-md-12 col-sm-12" id="divBusqueda">
            </div>
         </div>     
     </div>
     <div class="box">
        <div class="box-header">
            <h2 class="box-title col-lg-5 col-md-5 col-sm-5">Detalle <button type="button" class="btn btn-xs btn-info" title="Agregar Detalle" onclick="seleccionarServicioOtro();" ><i class="fa fa-plus"></i></button>&nbsp;&nbsp;&nbsp;&nbsp;<button type="button" class="btn btn-xs btn-danger" title="Agregar Hoja Costo" onclick="agregarHojaCosto($('#historia_id').val());">Hoja de Costo<i class="fa fa-file"></i></button></h2>

            <div class="text-right col-lg-7 col-md-7 col-sm-7">
                <div class="col-lg-1 col-md-1 col-sm-1" style="display: none;">
                    {!! Form::hidden('movimientoref', 'N', array('id' => 'movimientoref')) !!}
                    <input type="checkbox" onclick="movimientoRef(this.checked)" />
                </div>
                {!! Form::label('movimiento', 'Doc. Ref.:', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label', 'style' => 'display:none')) !!}
                <div class="col-lg-3 col-md-3 col-sm-3">
                    {!! Form::hidden('movimiento_id', 0, array('id' => 'movimiento_id')) !!}
                    {!! Form::text('movimiento', null, array('class' => 'form-control input-xs', 'id' => 'movimiento', 'style' => 'display:none')) !!}
                </div>
                <div class="col-lg-4 col-md-4 col-sm-4">
                    <table class="table table-condensed table-border" id="tbDoc" style="display: none;">
                        <thead>
                            <tr>
                                <th class="text-center">Doc.</th>
                                <th class="text-center">Monto</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th class="text-center">Total</th>
                                <th class="text-center" id='totalDoc'>0</th>
                            </tr>                            
                        </tfoot>
                    </table>    
                </div>
            </div>
        </div>
        <div class="box-body" id="tablaDetallesTemporal">
            <table class="table table-condensed table-border" id="tbDetalle">
                <thead>
                    <th class="text-center">Cant.</th>
                    <th class="text-center" colspan="2">Medico</th>
                    <th class="text-center">Rubro</th>
                    <th class="text-center">Descripcion</th>
                    <th class="text-center">Precio</th>
                    <th class="text-center">
                        <select id='cboDescuento' name='cboDescuento' class="xyz input-xs" style='width: 60px;'>
                            <option value='P'>%</option>
                            <option value='M'>Monto</option>
                        </select><br />&nbsp;Desc.
                    </th>
                    <th class="text-center">Hospital</th>
                    <th class="text-center">Medico</th>
                    <th class="text-center">Subtotal</th>
                </thead>
                <tbody id="cuerpoTabla">
                </tbody>
                <tfoot>
                    <th class="text-right" colspan="7">Comprobante</th>
                    <th>{!! Form::text('totalboleta', null, array('class' => 'xyz form-control input-xs', 'id' => 'totalboleta', 'size' => 3, 'readonly' => 'true', 'style' => 'width: 60px;')) !!}</th>
                    <th class="text-right">Pago</th>
                    <th>{!! Form::text('total', null, array('class' => 'xyz form-control input-xs', 'id' => 'total', 'size' => 3, 'readonly' => 'true', 'style' => 'width: 60px;')) !!}</th>
                </tfoot>
            </table>
        </div>
     </div>
    <!-- Modal -->
    <div id="modalOxigeno" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">PRECIO PARA OXÍGENOS</h4>
                </div>
                <div class="modal-body" style="height: 170px;">
                    <div class="bootbox-body">
                        <form class="form-horizontal" role="form">
                            <div class="form-group">
                                <label  class="col-lg-2 col-md-2 col-sm-2 control-label" for="txtHorasO2">Horas</label>
                                <div class="col-lg-4 col-md-4 col-sm-4">
                                    <input type="text" class="form-control" id="txtHorasO2" onkeyup="calcularPrecioO2();" placeholder="Ingresa Horas"/>
                                </div>
                                <label class="col-lg-2 col-md-2 col-sm-2 control-label" for="txtLitros2">Litros</label>
                                <div class="col-lg-4 col-md-4 col-sm-4">
                                    <input type="text" class="form-control" id="txtLitros2" onkeyup="calcularPrecioO2();" placeholder="Ingresa Litros"/>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-lg-6 col-md-6 col-sm-6"></div>
                                <label  class="col-lg-2 col-md-2 col-sm-2 control-label" for="txtPrecioO2">Precio</label>
                                <div class="col-lg-4 col-md-4 col-sm-4">
                                    <input type="text" class="form-control" readonly="readonly" id="txtPrecioO2"/>
                                    <input type="hidden" id="txtIdPrecioO2" value="">
                                </div>
                            </div>
                        </form>
                    </div>     
                    <p class="text-right"><button type="button" class="btn btn-success" onclick="aceptarPrecioO2();">Aceptar</button>&nbsp;&nbsp;&nbsp;<button type="button" class="btn btn-warning" onclick="$('#modalOxigeno').modal('hide');">Cancelar</button></p>
                </div>                
            </div>
      </div>
    </div>
{!! Form::close() !!}
<script type="text/javascript">
var valorbusqueda="";
function cambiartipodoc() {
    if ($('#tipodocumento').val() == 'Factura') {
        $('#opcEmpresa').css('display', '');
        $('#ccruc').focus();
    } else {
        $('#opcEmpresa').css('display', 'none');
    }
}
$(document).ready(function() {
    $(document).on('change', '#tipodocumento', function(e) {
        e.preventDefault();
        if ($(this).val() == 'Factura') {
            $('#opcEmpresa').css('display', '');
            $('#ccruc').focus();
        } else {
            $('#opcEmpresa').css('display', 'none');
        }
    });
    $(document).on('keyup', '#ccruc', function(e) {
        e.preventDefault();
        e.stopImmediatePropagation();
        if ($(this).val().length == 11) {
            buscarEmpresa();
        }
    });
    $('#efectivo').val('0.00');
	configurarAnchoModal('1300');
	init(IDFORMMANTENIMIENTO+'{!! $entidad !!}', 'B', '{!! $entidad !!}');
    $(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="total"]').inputmask('decimal', { radixPoint: ".", autoGroup: true, groupSeparator: "", groupSize: 3, digits: 2 });
    $(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="totalboleta"]').inputmask('decimal', { radixPoint: ".", autoGroup: true, groupSeparator: "", groupSize: 3, digits: 2 });
    $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="ruc"]').inputmask("99999999999");
    $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="serieventa"]').inputmask("999");
    $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="numeroventa"]').inputmask("99999999");
    $(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="deducible"]').inputmask('decimal', { radixPoint: ".", autoGroup: true, groupSeparator: ",", groupSize: 3, digits: 2 });
    $(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="coa"]').inputmask('decimal', { radixPoint: ".", autoGroup: true, groupSeparator: ",", groupSize: 3, digits: 2 });
    $(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="txtHorasO2"]').inputmask('decimal', { radixPoint: ".", autoGroup: true, groupSeparator: "", groupSize: 3, digits: 2 });
    $(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="txtLitros2"]').inputmask('decimal', { radixPoint: ".", autoGroup: true, groupSeparator: "", groupSize: 3, digits: 2 });
    $(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="txtPrecioO2"]').inputmask('decimal', { radixPoint: ".", autoGroup: true, groupSeparator: "", groupSize: 3, digits: 2 });
	var personas = new Bloodhound({
		datumTokenizer: function (d) {
			return Bloodhound.tokenizers.whitespace(d.value);
		},
		queryTokenizer: Bloodhound.tokenizers.whitespace,
        limit:10,
		remote: {
			url: 'historia/personautocompletar/%QUERY',
			filter: function (personas) {
				return $.map(personas, function (movie) {
					return {
						value: movie.value,
						id: movie.id,
                        historia: movie.numero,
                        person_id:movie.person_id,
                        tipopaciente:movie.tipopaciente,
                        dni:movie.dni,
                        fallecido:movie.fallecido,
                        plan:movie.plan,
                        plan_id:movie.plan_id,
                        coa:movie.coa,
                        deducible:movie.deducible
					};
				});
			}
		}
	});
	personas.initialize();
	$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="paciente"]').typeahead(null,{
		displayKey: 'value',
		source: personas.ttAdapter()
	}).on('typeahead:selected', function (object, datum) {
        if(datum.fallecido=='S'){
            alert('No puede elegir paciente fallecido');
            $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="paciente"]').val('');
        }else{
            $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="historia_id"]').val(datum.id);
            $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="numero_historia"]').val(datum.historia);
            $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="paciente"]').val(datum.value);
            $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="dni"]').val(datum.dni);
            $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="plan"]').val(datum.plan);
            $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="plan_id"]').val(datum.plan_id);
            $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="coa"]').val(datum.coa);
            $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="deducible"]').val(datum.deducible);
            if(datum.tipopaciente=="Hospital"){
                $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="tipopaciente"]').val("Particular");
            }else{
                $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="tipopaciente"]').val(datum.tipopaciente);
            }    
            
            $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="person_id"]').val(datum.person_id);
        }
	});
    
    var personas2 = new Bloodhound({
		datumTokenizer: function (d) {
			return Bloodhound.tokenizers.whitespace(d.value);
		},
		queryTokenizer: Bloodhound.tokenizers.whitespace,
		remote: {
			url: 'ticket/personrucautocompletar/%QUERY',
			filter: function (personas2) {
				return $.map(personas2, function (movie) {
					return {
						value: movie.value,
						id: movie.id,
                        ruc: movie.ruc,
                        razonsocial:movie.razonsocial,
                        direccion:movie.direccion,
                        label: movie.label,
					};
				});
			}
		}
	});
	personas2.initialize();
	$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="ruc"]').typeahead(null,{
		displayKey: 'label',
		source: personas2.ttAdapter()
	}).on('typeahead:selected', function (object, datum) {
		$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="ruc"]').val(datum.ruc);
        $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="razon"]').val(datum.razonsocial);
        $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="direccion"]').val(datum.direccion);
	});

    var personas4 = new Bloodhound({
        datumTokenizer: function (d) {
            return Bloodhound.tokenizers.whitespace(d.value);
        },
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        remote: {
            url: 'ticket/personrazonautocompletar/%QUERY',
            filter: function (personas4) {
                return $.map(personas4, function (movie) {
                    return {
                        value: movie.value,
                        id: movie.id,
                        ruc: movie.ruc,
                        razonsocial:movie.razonsocial,
                        direccion:movie.direccion,
                        label: movie.label,
                    };
                });
            }
        }
    });
    personas4.initialize();
    $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="razon"]').typeahead(null,{
        displayKey: 'label',
        source: personas4.ttAdapter()
    }).on('typeahead:selected', function (object, datum) {
        $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="ruc"]').val(datum.ruc);
        $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="razon"]').val(datum.razonsocial);
        $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="direccion"]').val(datum.direccion);
    });
    
    var personas3 = new Bloodhound({
        datumTokenizer: function (d) {
            return Bloodhound.tokenizers.whitespace(d.value);
        },
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        remote: {
            url: 'medico/medicoautocompletar/%QUERY',
            filter: function (personas) {
                return $.map(personas, function (movie) {
                    return {
                        value: movie.value,
                        person_id:movie.id,
                    };
                });
            }
        }
    });
    personas3.initialize();
    $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="referido"]').typeahead(null,{
        displayKey: 'value',
        source: personas3.ttAdapter()
    }).on('typeahead:selected', function (object, datum) {
        $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="referido"]').val(datum.value);
        $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="referido_id"]').val(datum.person_id);
    });

    var personal = new Bloodhound({
        datumTokenizer: function (d) {
            return Bloodhound.tokenizers.whitespace(d.value);
        },
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        limit:10,
        remote: {
            url: 'employee/mixtoautocompletar/%QUERY',
            filter: function (personas) {
                return $.map(personas, function (movie) {
                    return {
                        value: movie.value,
                        id: movie.id,
                    };
                });
            }
        }
    });
    personal.initialize();
    $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="personal"]').typeahead(null,{
        displayKey: 'value',
        source: personal.ttAdapter()
    }).on('typeahead:selected', function (object, datum) {
        $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="personal_id"]').val(datum.id);
        $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="personal"]').val(datum.value);
    });
    

   	var planes = new Bloodhound({
		datumTokenizer: function (d) {
			return Bloodhound.tokenizers.whitespace(d.value);
		},
        limit: 10,
		queryTokenizer: Bloodhound.tokenizers.whitespace,
		remote: {
			url: 'plan/planautocompletar/%QUERY',
			filter: function (planes) {
				return $.map(planes, function (movie) {
					return {
						value: movie.value,
						id: movie.id,
                        coa: movie.coa,
                        deducible:movie.deducible,
                        tipo:movie.tipo,
					};
				});
			}
		}
	});
	planes.initialize();
	$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="plan"]').typeahead(null,{
		displayKey: 'value',
		source: planes.ttAdapter()
	}).on('typeahead:selected', function (object, datum) {
		$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="plan"]').val(datum.value);
        $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="coa"]').val(datum.coa);
        $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="deducible"]').val(datum.deducible);
        $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="plan_id"]').val(datum.id);
        $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="tipoplan"]').val(datum.tipo);
	});
    $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="paciente"]').focus();

    $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="descripcion"]').on( 'keydown', function () {
        var e = window.event; 
        var keyc = e.keyCode || e.which;
        if(this.value.length>1 && keyc == 13){
            buscarServicio(this.value);
            valorbusqueda=this.value;
            this.focus();
            return false;
        }
        if(keyc == 38 || keyc == 40 || keyc == 13) {
            var tabladiv='tablaServicio';
			var child = document.getElementById(tabladiv).rows;
			var indice = -1;
			var i=0;
            $('#tablaServicio tr').each(function(index, elemento) {
                if($(elemento).hasClass("tr_hover")) {
    			    $(elemento).removeClass("par");
    				$(elemento).removeClass("impar");								
    				indice = i;
                }
                if(i % 2==0){
    			    $(elemento).removeClass("tr_hover");
    			    $(elemento).addClass("impar");
                }else{
    				$(elemento).removeClass("tr_hover");								
    				$(elemento).addClass('par');
    			}
    			i++;
    		});		 
			// return
			if(keyc == 13) {        				
			     if(indice != -1){
					var seleccionado = '';			 
					if(child[indice].id) {
					   seleccionado = child[indice].id;
					} else {
					   seleccionado = child[indice].id;
					}		 		
					seleccionarServicio(seleccionado);
				}
			} else {
				// abajo
				if(keyc == 40) {
					if(indice == (child.length - 1)) {
					   indice = 1;
					} else {
					   if(indice==-1) indice=0;
	                   indice=indice+1;
					} 
				// arriba
				} else if(keyc == 38) {
					indice = indice - 1;
					if(indice==0) indice=-1;
					if(indice < 0) {
						indice = (child.length - 1);
					}
				}	 
				child[indice].className = child[indice].className+' tr_hover';
			}
        }
    });
    generarNumero();
}); 

function aceptarPrecioO2() {
    var precio = $('#txtPrecioO2').val();
    var idprecio = $('#txtIdPrecioO2').val();
    $('#txtPrecio'+idprecio).val((parseFloat(precio).toFixed(2)).toString());
    calcularTotalItem2(idprecio);
    $('#modalOxigeno').modal('hide');
}

function calcularPrecioO2() {
    var horas = 0;
    var litros = 0;
    if($('#txtHorasO2').val()!=='') {
        var horas = parseFloat($('#txtHorasO2').val());
    } if($('#txtLitros2').val()!=='') {
        var litros = parseFloat($('#txtLitros2').val());
    }    
    var precio = horas*litros*60*0.03;
    $('#txtPrecioO2').val((parseFloat(precio).toFixed(2)).toString());
}

function modalO2(id) {
    $('#txtHorasO2').val('');
    $('#txtLitros2').val('');
    $('#txtIdPrecioO2').val(id);
    $('#txtPrecioO2').val((parseFloat('0.00').toFixed(2)).toString());
    $('#modalOxigeno').modal("show");
}

function guardarHistoria (entidad, idboton) {
	var idformulario = IDFORMMANTENIMIENTO + entidad;
	var data         = submitForm(idformulario);
	var respuesta    = '';
	var btn = $(idboton);
	btn.button('loading');
	data.done(function(msg) {
		respuesta = msg;
	}).fail(function(xhr, textStatus, errorThrown) {
		respuesta = 'ERROR';
	}).always(function() {
		btn.button('reset');
		if(respuesta === 'ERROR'){
		}else{
		  //alert(respuesta);
            var dat = JSON.parse(respuesta);
			if (dat[0]!==undefined && (dat[0].respuesta=== 'OK')) {
				cerrarModal();
                $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="historia_id"]').val(dat[0].id);
                $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="numero_historia"]').val(dat[0].historia);
                $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="person_id"]').val(dat[0].person_id);
                $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="tipopaciente"]').val(dat[0].tipopaciente);
                alert('Historia Generada');
                window.open("historia/pdfhistoria?id="+dat[0].id,"_blank");
			} else {
				mostrarErrores(respuesta, idformulario, entidad);
			}
		}
	});
}

var contador=0;
function guardarPago (entidad, idboton) {    
    //alert($('#listServicio').val());
    //return false;
    if ($('#tipodocumento').val() == 'Factura') {
        if($('#ccruc').val().length != 11 || $('#ccrazon').val() == '' || $('#ccdireccion').val() == '') {
            alert('No olvide digitar un RUC válido.');
            return false;
        }
    }
    if(!camposNoVacios()) {
        return false;
    }
    if(!coincidenciasMontos()) {
        $('#efectivo').focus();
        alert('Los montos no coinciden.');
        return false;
    }
    if($('#cuerpoTabla tr').length == 0) {
        alert('Debes seleccionar al menos un servicio.');
        $('#descripcion').focus();
        return false;
    }
    var band=true;
    var msg="";
    if($(".txtareaa").val()==""){
        band = false;
        msg += " *Se debe ingresar una descripcion \n";    
    }
     if($("#person_id").val()==""){
        band = false;
        msg += " *No se selecciono un paciente \n";    
    }
    if($("#plan_id").val()==""){
        band = false;
        msg += " *No se selecciono un plan \n";    
    }
    if($("#tipopaciente").val()!="Convenio"){
        for(c=0; c < carro.length; c++){
            if($("#txtIdTipoServicio"+carro[c]).val()!="1" && $("#txtIdTipoServicio"+carro[c]).val()!="6" && $("#txtIdTipoServicio"+carro[c]).val()!="7" && $("#txtIdTipoServicio"+carro[c]).val()!="8" && $("#txtIdTipoServicio"+carro[c]).val()!="12"){
                if($("#referido_id").val()=="0"){
                    band = false;
                    msg += " *Debe indicar referido \n";
                }
            }
        }  
    }
    for(c=0; c < carro.length; c++){
        if($("#txtDescuento"+carro[c]).val()==""){
            band = false;
            msg += " *Descuento no puede ser vacio \n";            
        }
        if($("#txtIdMedico"+carro[c]).val()==0){
            band = false;
            msg += " *Debe seleccionar medico \n";                        
        }
        var hospital = parseFloat($("#txtPrecioHospital"+carro[c]).val());
        var doctor = parseFloat($("#txtPrecioMedico"+carro[c]).val());
        var precio = parseFloat($("#txtPrecio"+carro[c]).val());
        var desc = parseFloat($("#txtDescuento"+carro[c]).val());
        if($("#cboDescuento").val()=="P"){
            precio = Math.round(100*(precio*(100-desc)/100))/100;
        }else{
            precio = precio - desc;
        }
        if((hospital + doctor) != precio){
            band = false;
            msg += " *Suma de pago hospital + doctor no coincide con el precio \n";
        }      
    } 
    if(parseFloat($("#total").val())>700 && $("#tipodocumento").val()=="Boleta"){
        if($("#dni").val().trim().length!=8){
            band = false;
            msg += " *El paciente debe tener DNI correcto \n";
        }
    }   
    if($("#tipodocumento").val()=="Factura"){
        var ruc = $("#ccruc").val();
        //ruc = ruc.replace("_"," ");
        console.log("ruc = " + ruc);
        if(ruc.trim().length != 11){
            band = false;
            msg += " *Debe registrar un correcto RUC \n";   
        }
    }
    if(band && contador==0){
        contador=1;
    	var idformulario = IDFORMMANTENIMIENTO + entidad;
    	var data         = submitForm(idformulario);
    	var respuesta    = '';
    	var btn = $(idboton);
    	btn.button('loading');
    	data.done(function(msg) {
    		respuesta = msg;
    	}).fail(function(xhr, textStatus, errorThrown) {
    		respuesta = 'ERROR';
            contador=0;
    	}).always(function() {
    		btn.button('reset');
            contador=0;
    		if(respuesta === 'ERROR'){
    		}else{
    		  //alert(respuesta);
                var dat = JSON.parse(respuesta);
                if(dat[0]!==undefined){
                    resp=dat[0].respuesta;    
                }else{
                    resp='VALIDACION';
                }
                
    			if (resp === 'OK') {
    				cerrarModal();
                    buscarCompaginado('', 'Accion realizada correctamente', entidad, 'OK');
                    if(dat[0].tipodocumento_id=="12"){
                        imprimirTicket(dat[0].venta_id);
                    }else{
                        tipo = $('#tipodocumento').val();
                        if(tipo == 'Boleta') {
                            tipo2 = 'B';
                        } else {
                            tipo2 = 'F';
                        }
                        serie = $('#serieventa').val();
                        numero = $('#numeroventa').val();                        
                        declarar1(dat[0].venta_id,dat[0].tipodocumento_id,dat[0].numero);
                        imprimirVenta(tipo2 + serie + '-' + numero);
                    }
                    /*if(dat[0].pagohospital!="0"){
                        window.open('/juanpablo/ticket/pdfComprobante3?ticket_id='+dat[0].ticket_id,'_blank')
                    }else{
                        window.open('/juanpablo/ticket/pdfPrefactura?ticket_id='+dat[0].ticket_id,'_blank')
                    }
                    if(dat[0].notacredito_id!="0"){
                        window.open('/juanpablo/notacredito/pdfComprobante3?id='+dat[0].notacredito_id,'_blank');
                    }*/
    			} else if(resp === 'ERROR') {
    				alert(dat[0].msg);
    			} else {
    				mostrarErrores(respuesta, idformulario, entidad);
    			}
    		}
    	});
    }else{
        alert("Corregir los sgtes errores: \n"+msg);
    }
}

function declarar1(idventa,idtipodocumento,numero){
    if(idtipodocumento==5){
        var funcion="enviarBoleta";
    }else{
        var funcion="enviarFactura";
    }
    $.ajax({
        type: "GET",
        url: "../clifacturacion/controlador/contComprobante.php?funcion="+funcion,
        data: "idventa="+idventa+"&empresa=1&_token="+$(IDFORMBUSQUEDA + '{!! $entidad !!} :input[name="_token"]').val(),
        success: function(a) {
            console.log(a);
            imprimirVenta(numero);
        }
    }); 
}

function imprimirTicket(id){
    $.ajax({
        type: "POST",
        url: "http://localhost/clifacturacion/controlador/contImprimir.php?funcion=ImprimirTicket",
        data: "id="+id+"&_token="+$(IDFORMBUSQUEDA + '{!! $entidad !!} :input[name="_token"]').val(),
        success: function(a) {
            console.log(a);
        }
    });
}

function imprimirVenta(numero){
    $.ajax({
        type: "POST",
        url: "http://localhost/clifacturacion/controlador/contImprimir.php?funcion=ImprimirVenta",
        data: "numero="+numero+"&_token="+$(IDFORMBUSQUEDA + '{!! $entidad !!} :input[name="_token"]').val(),
        success: function(a) {
            console.log(a);
        }
    });
}

function validarFormaPago(forma){
    if(forma=="Tarjeta"){
        $(IDFORMMANTENIMIENTO + '{!! $entidad !!} div[id="divTarjeta"]').css("display","");
    }else{
        $(IDFORMMANTENIMIENTO + '{!! $entidad !!} div[id="divTarjeta"]').css("display","none");
    }
}

var valorinicial="";
function buscarServicio(valor){
    //if(valorinicial!=valor){valorinicial=valor;
        $.ajax({
            type: "POST",
            url: "ticket/buscarservicio",
            data: "idtiposervicio="+$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="tiposervicio"]').val()+"&descripcion="+$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="descripcion"]').val()+"&tipopaciente="+$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="tipopaciente"]').val()+"&plan_id="+$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="plan_id"]').val()+"&_token="+$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[name="_token"]').val(),
            success: function(a) {
                datos=JSON.parse(a);
                $("#divBusqueda").html("<table class='table table-bordered table-condensed table-hover' border='1' id='tablaServicio'><thead><tr><th class='text-center'>TIPO</th><th class='text-center'>SERVICIO</th><th class='text-center'>P. UNIT.</tr></thead></table>");
                var pag=parseInt($("#pag").val());
                var d=0;
                for(c=0; c < datos.length; c++){
                    var a="<tr id='"+datos[c].idservicio+"' onclick=\"seleccionarServicio('"+datos[c].idservicio+"')\"><td align='center'>"+datos[c].tiposervicio+"</td><td>"+datos[c].servicio+"</td><td align='right'>"+datos[c].precio+"</td></tr>";
                    $("#tablaServicio").append(a);           
                }
                $('#tablaServicio').DataTable({
                    "scrollY":        "250px",
                    "scrollCollapse": true,
                    "paging":         false
                });
                $('#tablaServicio_filter').css('display','none');
                $("#tablaServicio_info").css("display","none");
    	    }
        });
    //}
}

var carro = new Array();
var carroDoc = new Array();
var copia = new Array();
function seleccionarServicio(idservicio){
    var band=true;
    for(c=0; c < carro.length; c++){
        if(carro[c]==idservicio){
            band=false;
        }      
    }
    if(band){
        $.ajax({
            type: "POST",
            url: "ticket/seleccionarservicio",
            data: "idservicio="+idservicio+"&tipopaciente="+$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="tipopaciente"]').val()+"&formapago="+$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="formapago"]').val()+"&tarjeta="+$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="tipotarjeta2"]').val()+"&plan_id="+$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="plan_id"]').val()+"&_token="+$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[name="_token"]').val(),
            success: function(a) {
                datos=JSON.parse(a);
                var c=0;
                var inpu = datos[c].servicio;
                if(datos[c].idservicio == 30938){
                    inpu = "<textarea class='xyz form-control input-xs txtareaa' id='txtServicio"+datos[c].idservicio+"' name='txtServicio"+datos[c].idservicio+"' >"+datos[c].servicio+"</textarea>";
                }
                $("#tbDetalle").append("<tr id='tr"+datos[c].idservicio+"'><td><input type='hidden' id='txtIdTipoServicio"+datos[c].idservicio+"' name='txtIdTipoServicio"+datos[c].idservicio+"' value='"+datos[c].idtiposervicio+"' /><input type='hidden' id='txtIdServicio"+datos[c].idservicio+"' name='txtIdServicio"+datos[c].idservicio+"' value='"+datos[c].idservicio+"' /><input type='text' data='numero' style='width: 40px;' class='xyz form-control input-xs' id='txtCantidad"+datos[c].idservicio+"' name='txtCantidad"+datos[c].idservicio+"' value='1' size='3' onkeydown=\"if(event.keyCode==13){calcularTotal()}\" onblur=\"calcularTotalItem("+datos[c].idservicio+")\" /></td>"+
                    "<td><input type='checkbox' id='chkCopiar"+datos[c].idservicio+"' onclick='checkMedico(this.checked,"+datos[c].idservicio+")' /></td>"+
                    "<td><input type='text' class='xyz form-control input-xs' id='txtMedico"+datos[c].idservicio+"' name='txtMedico"+datos[c].idservicio+"' /><input type='hidden' id='txtIdMedico"+datos[c].idservicio+"' name='txtIdMedico"+datos[c].idservicio+"' value='0' /></td>"+
                    "<td align='left'>"+datos[c].tiposervicio+"</td><td>"+inpu+"</td>"+
                    "<td><input type='hidden' id='txtPrecio2"+datos[c].idservicio+"' name='txtPrecio2"+datos[c].idservicio+"' value='"+datos[c].precio+"' /><input type='text' size='5' class='xyz form-control input-xs' data='numero' id='txtPrecio"+datos[c].idservicio+"' style='width: 60px;' name='txtPrecio"+datos[c].idservicio+"' value='"+datos[c].precio+"' onkeydown=\"if(event.keyCode==13){calcularTotalItem("+datos[c].idservicio+")}\" onblur=\"calcularTotalItem("+datos[c].idservicio+")\" /><button type='button' style='width: 60px;' title='Fórmula Oxígeno' class='btn btn-success btn-xs' onclick=\"modalO2('"+datos[c].idservicio+"')\"><i class='fa fa-pencil'></i></button></td>"+
                    "<td><input type='text' size='5' class='xyz form-control input-xs' data='numero' id='txtDescuento"+datos[c].idservicio+"' style='width: 60px;' name='txtDescuento"+datos[c].idservicio+"' value='0' onkeydown=\"if(event.keyCode==13){calcularTotalItem("+datos[c].idservicio+")}\" onblur=\"calcularTotalItem("+datos[c].idservicio+")\" style='width:50%' /></td>"+
                    "<td><input type='hidden' id='txtPrecioHospital2"+datos[c].idservicio+"' name='txtPrecioHospital2"+datos[c].idservicio+"' value='"+datos[c].preciohospital+"' /><input type='text' readonly='' size='5' class='xyz form-control input-xs' style='width: 60px;' data='numero'  id='txtPrecioHospital"+datos[c].idservicio+"' name='txtPrecioHospital"+datos[c].idservicio+"' value='"+datos[c].preciohospital+"' onblur=\"calcularTotalItem("+datos[c].idservicio+")\" /></td>"+
                    "<td><input type='hidden' id='txtPrecioMedico2"+datos[c].idservicio+"' name='txtPrecioMedico2"+datos[c].idservicio+"' value='"+datos[c].preciomedico+"' /><input type='text' readonly='' size='5' class='xyz form-control input-xs' data='numero' style='width: 60px;' id='txtPrecioMedico"+datos[c].idservicio+"' name='txtPrecioMedico"+datos[c].idservicio+"' value='"+datos[c].preciomedico+"' onblur=\"calcularTotalItem("+datos[c].idservicio+")\" /></td>"+
                    "<td><input type='text' readonly='' data='numero' class='xyz form-control input-xs' size='5' name='txtTotal"+datos[c].idservicio+"' style='width: 60px;' id='txtTotal"+datos[c].idservicio+"' value='"+datos[c].precio+"' /></td>"+
                    "<td><a href='#' onclick=\"quitarServicio('"+datos[c].idservicio+"')\"><i class='fa fa-minus-circle' title='Quitar' width='20px' height='20px'></i></td></tr>");
                carro.push(idservicio);
                $(':input[data="numero"]').inputmask('decimal', { radixPoint: ".", autoGroup: true, groupSeparator: "", groupSize: 3, digits: 2 });
                calcularCoaseguro();
                eval("var planes"+datos[c].idservicio+" = new Bloodhound({"+
            		"datumTokenizer: function (d) {"+
            			"return Bloodhound.tokenizers.whitespace(d.value);"+
            		"},"+
                    "limit: 10,"+
            		"queryTokenizer: Bloodhound.tokenizers.whitespace,"+
            		"remote: {"+
            			"url: 'medico/medicoautocompletar/%QUERY',"+
            			"filter: function (planes"+datos[c].idservicio+") {"+
                            "return $.map(planes"+datos[c].idservicio+", function (movie) {"+
            					"return {"+
            						"value: movie.value,"+
            						"id: movie.id,"+
            					"};"+
            				"});"+
            			"}"+
            		"}"+
            	"});"+
            	"planes"+datos[c].idservicio+".initialize();"+
            	"$('#txtMedico"+datos[c].idservicio+"').typeahead(null,{"+
            		"displayKey: 'value',"+
            		"source: planes"+datos[c].idservicio+".ttAdapter()"+
            	"}).on('typeahead:selected', function (object, datum) {"+
            		"$('#txtMedico"+datos[c].idservicio+"').val(datum.value);"+
                    "$('#txtIdMedico"+datos[c].idservicio+"').val(datum.id);"+
                    "copiarMedico("+datos[c].idservicio+");"+
            	"});");
                $("#txtMedico"+datos[c].idservicio).focus();  
                if($(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="editarprecio"]').val()=='S'){
                    editarPrecio(true);
                }  
                $('#efectivo').val(parseFloat($('#total').val()).toFixed(2));  
                calcularTotalPago(); 
            }
        });
    }else{
        $('#txtMedico'+idservicio).focus();
    }
}

function seleccionarServicioOtro(){
    var idservicio = "0"+Math.round(Math.random()*100);
    $("#tbDetalle").append("<tr id='tr"+idservicio+"'><td><input type='hidden' id='txtIdTipoServicio"+idservicio+"' name='txtIdTipoServicio"+idservicio+"' value='0' /><input type='text' data='numero' class='xyz form-control input-xs' id='txtCantidad"+idservicio+"' name='txtCantidad"+idservicio+"' style='width: 40px;' value='1' size='3' onkeydown=\"if(event.keyCode==13){calcularTotal()}\" onblur=\"calcularTotalItem2('"+idservicio+"')\" /></td>"+
        "<td><input type='checkbox' id='chkCopiar"+idservicio+"' onclick=\"checkMedico(this.checked,'"+idservicio+"')\" /></td>"+
        "<td><input type='text' class='xyz form-control input-xs' id='txtMedico"+idservicio+"' name='txtMedico"+idservicio+"' /><input type='hidden' id='txtIdMedico"+idservicio+"' name='txtIdMedico"+idservicio+"' value='0' /></td>"+
        "<td align='left'><select class='xyz form-control input-xs' id='cboTipoServicio"+idservicio+"' name='cboTipoServicio"+idservicio+"'><option value='0' selected=''>OTROS</option>"+$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="tiposervicio"]').html()+"</select></td><td><textarea class='xyz form-control input-xs txtareaa' id='txtServicio"+idservicio+"' name='txtServicio"+idservicio+"' /></td>"+
        "<td><input type='hidden' id='txtPrecio2"+idservicio+"' name='txtPrecio2"+idservicio+"' value='0' /><input type='text' size='5' class='xyz form-control input-xs' style='width: 60px;' data='numero' id='txtPrecio"+idservicio+"' name='txtPrecio"+idservicio+"' value='0' onkeydown=\"if(event.keyCode==13){calcularTotalItem2('"+idservicio+"')}\" onblur=\"calcularTotalItem2('"+idservicio+"')\" />   <button type='button' style='width: 60px;' title='Fórmula Oxígeno' class='btn btn-success btn-xs' onclick=\"modalO2('"+idservicio+"')\"><i class='fa fa-pencil'></i></button>    </td>"+
        "<td><input type='text' size='5' style='width: 60px;' class='xyz form-control input-xs' data='numero' id='txtDescuento"+idservicio+"' name='txtDescuento"+idservicio+"' value='0' onkeydown=\"if(event.keyCode==13){calcularTotalItem2('"+idservicio+"')}\" onblur=\"calcularTotalItem2('"+idservicio+"')\" style='width:50%' /></td>"+
        "<td><input type='hidden' id='txtPrecioHospital2"+idservicio+"' name='txtPrecioHospital2"+idservicio+"' value='0' /><input type='text' size='5' style='width: 60px;' class='xyz form-control input-xs' data='numero'  id='txtPrecioHospital"+idservicio+"' name='txtPrecioHospital"+idservicio+"' value='0' onblur=\"calcularTotalItem2("+idservicio+")\" /></td>"+
        "<td><input type='hidden' id='txtPrecioMedico2"+idservicio+"' name='txtPrecioMedico2"+idservicio+"' value='0' /><input type='text' size='5' class='xyz form-control input-xs' data='numero'  id='txtPrecioMedico"+idservicio+"' name='txtPrecioMedico"+idservicio+"' value='0' style='width: 60px;' /></td>"+
        "<td><input type='text' style='width: 60px;' readonly='' data='numero' class='xyz form-control input-xs' size='5' name='txtTotal"+idservicio+"' id='txtTotal"+idservicio+"' value=0' /></td>"+
        "<td><a href='#' onclick=\"quitarServicio('"+idservicio+"')\"><i class='fa fa-minus-circle' title='Quitar' width='20px' height='20px'></i></td></tr>");
    carro.push(idservicio);
    $('#cboTipoServicio'+idservicio+' option[value=""]').remove();
    $(':input[data="numero"]').inputmask('decimal', { radixPoint: ".", autoGroup: true, groupSeparator: "", groupSize: 3, digits: 2 });
    eval("var planes"+idservicio+" = new Bloodhound({"+
		"datumTokenizer: function (d) {"+
			"return Bloodhound.tokenizers.whitespace(d.value);"+
		"},"+
        "limit: 10,"+
		"queryTokenizer: Bloodhound.tokenizers.whitespace,"+
		"remote: {"+
			"url: 'medico/medicoautocompletar/%QUERY',"+
			"filter: function (planes"+idservicio+") {"+
                "return $.map(planes"+idservicio+", function (movie) {"+
					"return {"+
						"value: movie.value,"+
						"id: movie.id,"+
					"};"+
				"});"+
			"}"+
		"}"+
	"});"+
	"planes"+idservicio+".initialize();"+
	"$('#txtMedico"+idservicio+"').typeahead(null,{"+
		"displayKey: 'value',"+
		"source: planes"+idservicio+".ttAdapter()"+
	"}).on('typeahead:selected', function (object, datum) {"+
		"$('#txtMedico"+idservicio+"').val(datum.value);"+
        "$('#txtIdMedico"+idservicio+"').val(datum.id);"+
        "copiarMedico('"+idservicio+"');"+
	"});");
    $("#txtMedico"+idservicio).focus();
}

function calcularTotal(){
    var total2=0;
    for(c=0; c < carro.length; c++){
        var tot=Math.round(parseFloat($("#txtTotal"+carro[c]).val())*100)/100;
        total2=Math.round((total2+tot) * 100) / 100;
    }
    $("#total").val(total2);
    var total2=0;
    for(c=0; c < carro.length; c++){
        var tot=Math.round(100*(parseFloat($("#txtPrecioHospital"+carro[c]).val())*parseFloat($("#txtCantidad"+carro[c]).val())))/100;
        total2=Math.round((total2+tot) * 100) / 100;        
    }
    $("#totalboleta").val(total2);
}

function calcularTotalPago() {
    var efectivo = $('#efectivo').val();
    var visa = $('#visa').val();
    var master = $('#master').val();
    var total = 0.00;
    if(efectivo == '') {
        efectivo = 0.00;
    } 
    if(visa == '') {
        visa = 0.00;
    }
    if(master == '') {
        master = 0.00;
    }
    total = parseFloat(efectivo) + parseFloat(visa) + parseFloat(master);
    $('#total2').val(total.toFixed(2));

    coincidenciasMontos();      
}

function coincidenciasMontos() {
    if(parseFloat($('#total').val()) == parseFloat($('#total2').val())) {
        $('#mensajeMontos').html('Los montos coindicen.').css('color', 'green');
        $('#genComp').css('display', '');
        return true;
    } else if(parseFloat($('#total').val()) > parseFloat($('#total2').val())) {
        $('#mensajeMontos').html('Es un monto menor.').css('color', 'orange');  
        $('#genComp').css('display', 'none');       
        return true;
    } else if(parseFloat($('#total').val()) < parseFloat($('#total2').val())) {
        $('#mensajeMontos').html('Es un monto mayor.').css('color', 'red'); 
        $('#genComp').css('display', 'none');       
        return false;
    }
}

function calcularCoaseguro(){
    if($(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="coa"]').val()!="" && parseFloat($(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="coa"]').val())>0){
        var ded=parseFloat($(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="coa"]').val());
        if(ded==100){
            ded=0;
        }
    }else{
        var ded=100;
    }
    for(c=0; c < carro.length; c++){
        if($("#txtIdTipoServicio"+carro[c]).val()!="1" && $("#txtIdTipoServicio"+carro[c]).val()!="0"){//Para todo lo q no es consulta
            var cant=parseFloat($("#txtCantidad"+carro[c]).val());
            var pv=parseFloat($("#txtPrecio2"+carro[c]).val());
            var tot=parseFloat($("#txtTotal"+carro[c]).val());
            var precio = Math.round((pv*ded/100)*100)/100;
            var hospital = Math.round((parseFloat($("#txtPrecioHospital2"+carro[c]).val())*ded/100)*100)/100;
            var medico = Math.round((parseFloat($("#txtPrecioMedico2"+carro[c]).val())*ded/100)*100)/100;
            $("#txtPrecio"+carro[c]).val(precio);  
            var desc=parseFloat($("#txtDescuento"+carro[c]).val());
            if($("#cboDescuento").val()=="P"){
                pv = Math.round(100*(pv * (100 - desc)/100))/100;
                hospital = Math.round(100*(hospital * (100 - desc)/100))/100;
                medico = Math.round(100*(medico * (100 - desc)/100))/100;
            }else{
                pv = pv - desc;
                medico = medico - desc;
            }
            var total=Math.round((pv*cant*ded/100) * 100) / 100;
            $("#txtTotal"+carro[c]).val(total);  
            $("#txtPrecioHospital"+carro[c]).val(hospital);
            $("#txtPrecioMedico"+carro[c]).val(medico);
        }else{
            if($(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="plan_id"]').val()!="6" && $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="tipopaciente"]').val()=="Convenio"){
                var cant=parseFloat($("#txtCantidad"+carro[c]).val());
                var pv=parseFloat($(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="deducible"]').val());
                var tot=parseFloat($("#txtTotal"+carro[c]).val());
                var total=Math.round((pv*cant) * 100) / 100;
                $("#txtTotal"+carro[c]).val(total);  
                $("#txtPrecio"+carro[c]).val(pv);  
                $("#txtPrecioHospital"+carro[c]).val(pv);
                var medico = parseFloat($("#txtPrecioMedico2"+carro[c]).val());
                $("#txtPrecioMedico"+carro[c]).val(medico);
            }
        }
    }
    calcularTotal();
}

function calcularTotalItem(id){
    var cant=parseFloat($("#txtCantidad"+id).val());
    var pv=parseFloat($("#txtPrecio"+id).val());
    var pv2=parseFloat($("#txtPrecio2"+id).val());
    var desc=parseFloat($("#txtDescuento"+id).val());
    var hosp=parseFloat($("#txtPrecioHospital"+id).val());
    if($("#cboDescuento").val()=="P"){
        pv = Math.round(100*(pv * (100 - desc)/100))/100;
    }else{
        pv = pv - desc;
    }
    if($("#txtIdTipoServicio"+id).val()!="1" && $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="plan_id"]').val()!="6"){
        if($(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="coa"]').val()!=""){
            var ded=parseFloat($(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="coa"]').val());
            if(ded==100){
               ded=0;
            }else if(ded==0){
                ded=100;
            }
        }else{
            var ded=100;
        }
    }else{
        var ded = 100;
    }
    if(ded>0 && ded<100){
        pv = pv2;
    }
    pv=Math.round((pv*ded/100) * 100) / 100;
    var total=Math.round((pv*cant) * 100) / 100;
    var med = Math.round((parseFloat($("#txtPrecioMedico2"+id).val())*ded/100)*100)/100;

    if($("#txtIdTipoServicio"+id).val()!="1"){
        $("#txtTotal"+id).val(total);   
        if(med==0){
            var hos=pv - med;
            $("#txtPrecioHospital"+id).val(hos);    
        }
        $("#txtPrecioMedico"+id).val(med);
    }else if($("#txtIdTipoServicio"+id).val()=="1" && $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="plan_id"]').val()=="6"){
        $("#txtTotal"+id).val(total);
        med = pv - hosp;
        $("#txtPrecioMedico"+id).val(med);
    }else if($("#txtIdTipoServicio"+id).val()=="1" && $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="tipoplan"]').val()!="Institucion"){
        $("#txtTotal"+id).val(total);
        med = pv - hosp;
        $("#txtPrecioMedico"+id).val(med);
    }
    calcularTotal();
    $('#efectivo').val(parseFloat($('#total').val()).toFixed(2));  
    calcularTotalPago();
}

function calcularTotalItem2(id){
    var cant=parseFloat($("#txtCantidad"+id).val());
    var pv=parseFloat($("#txtPrecio"+id).val());
    var desc=parseFloat($("#txtDescuento"+id).val());
    var hosp=parseFloat($("#txtPrecioHospital"+id).val());
    if($("#cboDescuento").val()=="P"){
        pv = Math.round(100*(pv * (100 - desc)/100))/100;
    }else{
        pv = pv - desc;
    }
    /*if($("#txtIdTipoServicio"+id).val()!="1" && $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="plan_id"]').val()!="6"){
        if($(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="coa"]').val()!=""){
            var ded=parseFloat($(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="coa"]').val());
            if(ded==100){
               ded=0;
            }else if(ded==0){
                ded=100;
            }
        }else{
            var ded=100;
        }
    }else{*/
        var ded = 100;
    //}
    var total=Math.round((pv*cant*ded/100) * 100) / 100;
    var med = pv - hosp;
    if($("#txtIdTipoServicio"+id).val()!="1"){
        $("#txtTotal"+id).val(total);   
        $("#txtPrecioMedico"+id).val(med);
    }else if($("#txtIdTipoServicio"+id).val()=="1" && $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="plan_id"]').val()=="6"){
        $("#txtTotal"+id).val(total);
        $("#txtPrecioMedico"+id).val(med);
    }
    calcularTotal();
    $('#efectivo').val(parseFloat($('#total').val()).toFixed(2));  
    calcularTotalPago();
}

function quitarServicio(id){
    $("#tr"+id).remove();
    for(c=0; c < carro.length; c++){
        if(carro[c] == id) {
            carro.splice(c,1);
        }
    }
    calcularTotal();
    $('#efectivo').val(parseFloat($('#total').val()).toFixed(2));
    calcularTotalPago();
}

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

function boletearTodoCaja(check){
    if(check){
        $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="boletear"]').val('S');
    }else{
        $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="boletear"]').val('N');
    }
}

function editarPrecio(check){
    if(check){
        $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="editarprecio"]').val('S');
    }else{
        $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="editarprecio"]').val('N');
    }
    for(c=0; c < carro.length; c++){
        if(check) {
            //$("#txtPrecio"+carro[c]).removeAttr("readonly");
            $("#txtPrecioHospital"+carro[c]).removeAttr("readonly");
            $("#txtPrecioMedico"+carro[c]).removeAttr("readonly");
        }else{
            //$("#txtPrecio"+carro[c]).attr("readonly","true");
            $("#txtPrecioHospital"+carro[c]).attr("readonly","true");
            $("#txtPrecioMedico"+carro[c]).attr("readonly","true");
        }
    }
}

/*function generarNumero(){
    $.ajax({
        type: "POST",
        url: "ticket/generarNumero",
        data: "tipodocumento="+$(IDFORMMANTENIMIENTO + '{! $entidad !!} :input[name="tipodocumento"]').val()+"&serie="+$(IDFORMMANTENIMIENTO + '{! $entidad !!} :input[name="serieventa"]').val()+"&_token="+$(IDFORMMANTENIMIENTO + '{! $entidad !!} :input[name="_token"]').val(),
        success: function(a) {
            $(IDFORMMANTENIMIENTO + '{! $entidad !!} :input[name="numeroventa"]').val(a);
            if($(IDFORMMANTENIMIENTO + '{! $entidad !!} :input[name="tipodocumento"]').val()=="Factura"){
                $(".datofactura").css("display","");
            }else{
                $(".datofactura").css("display","none");
            }
        }
    });
}*/

function generarNumero(){
    $.ajax({
        type: "POST",
        url: "ticket/generarNumero",
        data: "tipodocumento="+$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[name="tipodocumento"]').val()+"&serie="+$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[name="serieventa"]').val()+"&_token="+$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[name="_token"]').val() + '&caja_id=2',
        success: function(a) {
            $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[name="numeroventa"]').val(a);
            if($(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[name="tipodocumento"]').val()=="Factura"){
                $(".datofactura").css("display","");
            }else{
                $(".datofactura").css("display","none");
            }
        }
    });
}

function Soat(check){
    if(check){
        $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="soat"]').val('S');
    }else{
        $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="soat"]').val('N');
    }
}

function Manual(check){
    if(check){
        $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="manual"]').val('S');
        $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[name="numeroventa"]').removeAttr("readonly");
    }else{
        $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="manual"]').val('N');
        $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[name="numeroventa"]').attr("readonly","true");
    }
}

function Sctr(check){
    if(check){
        $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="sctr"]').val('S');
    }else{
        $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="sctr"]').val('N');
    }
}

function checkMedico(check,idservicio){
    if(check){
        copia.push(idservicio);
    }else{
        for(c=0; c < copia.length; c++){
            if(copia[c]==idservicio){
                copia.splice(c,1);
            }
        }
        $("#txtIdMedico"+idservicio).val(0);
        $("#txtMedico"+idservicio).val("");
        $("#txtMedico"+idservicio).focus();
    }
}

function copiarMedico(idservicio){
    if($("#chkCopiar"+idservicio).is(":checked")){
        for(c=0; c < copia.length; c++){
            $("#txtIdMedico"+copia[c]).val($("#txtIdMedico"+idservicio).val());
            $("#txtMedico"+copia[c]).val($("#txtMedico"+idservicio).val());
        }
    }
}

function editarDescuentoPersonal(check){
    if(check){
        $(".descuentopersonal").css('display','');
        $("#descuentopersonal").val('S');
    }else{
        $(".descuentopersonal").css('display','none');
        $("#descuentopersonal").val('N');
    }
}

function movimientoRef(check){
    if(check){
        $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="movimientoref"]').val('S');
        $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="movimiento"]').css("display","");
        $('#tbDoc').css("display","");
        $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="movimiento_id"]').val(0);
    }else{
        $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="movimientoref"]').val('N');
        $('#tbDoc').css("display","none");
        $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="movimiento"]').css("display","none");
    }
}

    var numeroref = new Bloodhound({
        datumTokenizer: function (d) {
            return Bloodhound.tokenizers.whitespace(d.value);
        },
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        limit:10,
        remote: {
            url: 'ventaadmision/ventaautocompletar/%QUERY',
            filter: function (docs) {
                return $.map(docs, function (movie) {
                    return {
                        value: movie.value2,
                        id: movie.id,
                        paciente: movie.paciente,
                        person_id:movie.person_id,
                        num:movie.value,
                        total:movie.total,
                    };
                });
            }
        }
    });
    numeroref.initialize();
    $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="movimiento"]').typeahead(null,{
        displayKey: 'value',
        source: numeroref.ttAdapter()
    }).on('typeahead:selected', function (object, datum) {
        //$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="movimiento_id"]').val(datum.id);
        //$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="movimiento"]').val(datum.value);
        $("#tbDoc").append("<tr id='trDoc"+datum.id+"'><td align='left'>"+datum.num+"</td><td id='tdTotalDoc"+datum.id+"' align='center'>"+datum.total+"<td><td><a href='#' onclick=\"quitarDoc('"+datum.id+"')\"><i class='fa fa-minus-circle' title='Quitar' width='20px' height='20px'></i></td></tr>");
        carroDoc.push(datum.id);
        calcularTotalDoc();
        $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="movimiento"]').val('');
    });

function quitarDoc(id){
    $("#trDoc"+id).remove();
    for(c=0; c < carroDoc.length; c++){
        if(carroDoc[c] == id) {
            carroDoc.splice(c,1);
        }
    }
    calcularTotalDoc();
}

function calcularTotalDoc(){
    var total2=0;
    for(c=0; c < carroDoc.length; c++){
        var tot=parseFloat($("#tdTotalDoc"+carroDoc[c]).html());
        total2=Math.round((total2+tot) * 100) / 100;        
    }
    $("#totalDoc").html(total2);
}

function agregarDetalle(id){
    $.ajax({
        type: "POST",
        url: "ticket/agregardetalle",
        data: "id="+id+"&_token="+$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[name="_token"]').val(),
        success: function(a) {
            datos=JSON.parse(a);
            for(d=0;d < datos.length; d++){
                if(datos[d].idservicio>0){
                    datos[d].id=datos[d].idservicio;
                }else{
                    datos[d].id="00"+Math.round(Math.random()*100);
                }
                //console.log(datos[d].idservicio);
                datos[d].idservicio="01"+Math.round(Math.random()*100)+datos[d].idservicio;
                $("#tbDetalle").append("<tr id='tr"+datos[d].idservicio+"'><td><input type='hidden' id='txtIdTipoServicio"+datos[d].idservicio+"' name='txtIdTipoServicio"+datos[d].idservicio+"' value='"+datos[d].idtiposervicio+"' /><input type='hidden' id='txtIdServicio"+datos[d].idservicio+"' name='txtIdServicio"+datos[d].idservicio+"' value='"+datos[d].id+"' /><input type='text' data='numero' style='width: 40px;' class='xyz form-control input-xs' id='txtCantidad"+datos[d].idservicio+"' name='txtCantidad"+datos[d].idservicio+"' value='"+datos[d].cantidad+"' size='3' onkeydown=\"if(event.keyCode==13){calcularTotal()}\" onblur=\"calcularTotalItem('"+datos[d].idservicio+"')\" /></td>"+
                    "<td><input type='checkbox' id='chkCopiar"+datos[d].idservicio+"' onclick=\"checkMedico(this.checked,'"+datos[d].idservicio+"')\" /></td>"+
                    "<td><input type='text' class='xyz form-control input-xs' id='txtMedico"+datos[d].idservicio+"' name='txtMedico"+datos[d].idservicio+"' value='"+datos[d].medico+"' /><input type='hidden' id='txtIdMedico"+datos[d].idservicio+"' name='txtIdMedico"+datos[d].idservicio+"' value='"+datos[d].idmedico+"' /></td>"+
                    "<td align='left'>"+datos[d].tiposervicio+"</td><td>"+datos[d].servicio+"</td>"+
                    "<td><input type='hidden' id='txtPrecio2"+datos[d].idservicio+"' name='txtPrecio2"+datos[d].idservicio+"' value='0' /><input type='text' size='5' class='xyz form-control input-xs' style='width: 60px;' data='numero' id='txtPrecio"+datos[d].idservicio+"' name='txtPrecio"+datos[d].idservicio+"' value='0' onkeydown=\"if(event.keyCode==13){calcularTotalItem2('"+datos[d].idservicio+"')}\" onblur=\"calcularTotalItem2('"+datos[d].idservicio+"')\" /></td>"+
                    "<td><input type='text' size='5' style='width: 60px;' class='xyz form-control input-xs' data='numero' id='txtDescuento"+datos[d].idservicio+"' name='txtDescuento"+datos[d].idservicio+"' value='0' onkeydown=\"if(event.keyCode==13){calcularTotalItem2('"+datos[d].idservicio+"')}\" onblur=\"calcularTotalItem2('"+datos[d].idservicio+"')\" style='width:50%' /></td>"+
                    "<td><input type='hidden' id='txtPrecioHospital2"+datos[d].idservicio+"' name='txtPrecioHospital2"+datos[d].idservicio+"' value='0' /><input type='text' size='5' style='width: 60px;' class='xyz form-control input-xs' data='numero'  id='txtPrecioHospital"+datos[d].idservicio+"' name='txtPrecioHospital"+datos[d].idservicio+"' value='0' onblur=\"calcularTotalItem2("+datos[d].idservicio+")\" /></td>"+
                    "<td><input type='hidden' id='txtPrecioMedico2"+datos[d].idservicio+"' name='txtPrecioMedico2"+datos[d].idservicio+"' value='0' /><input type='text' size='5' class='xyz form-control input-xs' data='numero'  id='txtPrecioMedico"+datos[d].idservicio+"' name='txtPrecioMedico"+datos[d].idservicio+"' value='0' style='width: 60px;' /></td>"+
                    "<td><input type='text' style='width: 60px;' readonly='' data='numero' class='xyz form-control input-xs' size='5' name='txtTotal"+datos[d].idservicio+"' id='txtTotal"+datos[d].idservicio+"' value=0' /></td>"+
                    "<td><a href='#' id='Quitar"+datos[d].idservicio+"' onclick=\"quitarServicio('"+datos[d].idservicio+"')\"><i class='fa fa-minus-circle' title='Quitar' width='20px' height='20px'></i></td></tr>");
                if(datos[d].situacionentrega!="A"){
                    carro.push(datos[d].idservicio);
                }else{
                    $("#Quitar"+datos[d].idservicio).css('display','none');
                }
                calcularTotalItem(datos[d].idservicio);
                $(':input[data="numero"]').inputmask('decimal', { radixPoint: ".", autoGroup: true, groupSeparator: "", groupSize: 3, digits: 2 });
                eval("var planes"+datos[d].idservicio+" = new Bloodhound({"+
                    "datumTokenizer: function (d) {"+
                        "return Bloodhound.tokenizers.whitespace(d.value);"+
                    "},"+
                    "limit: 10,"+
                    "queryTokenizer: Bloodhound.tokenizers.whitespace,"+
                    "remote: {"+
                        "url: 'medico/medicoautocompletar/%QUERY',"+
                        "filter: function (planes"+datos[d].idservicio+") {"+
                            "return $.map(planes"+datos[d].idservicio+", function (movie) {"+
                                "return {"+
                                    "value: movie.value,"+
                                    "id: movie.id,"+
                                "};"+
                            "});"+
                        "}"+
                    "}"+
                "});"+
                "planes"+datos[d].idservicio+".initialize();"+
                "$('#txtMedico"+datos[d].idservicio+"').typeahead(null,{"+
                    "displayKey: 'value',"+
                    "source: planes"+datos[d].idservicio+".ttAdapter()"+
                "}).on('typeahead:selected', function (object, datum) {"+
                    "$('#txtMedico"+datos[d].idservicio+"').val(datum.value);"+
                    "$('#txtIdMedico"+datos[d].idservicio+"').val(datum.id);"+
                    "copiarMedico('"+datos[d].idservicio+"');"+
                "});");
                $("#txtMedico"+datos[d].idservicio).focus(); 

            } 
            $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[name="coa"]').attr("readonly","true");
            $(".datofactura").css("display","none");
        }
    });
}

function agregarHojaCosto(id){
    $.ajax({
        type: "POST",
        url: "ticket/agregarhojacosto",
        data: "id="+id+"&_token="+$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[name="_token"]').val(),
        success: function(a) {
            datos=JSON.parse(a);
            for(d=0;d < datos.length; d++){
                if(datos[d].idservicio>0){
                    datos[d].id=datos[d].idservicio;
                    datos[d].idservicio="01"+Math.round(Math.random()*100)+datos[d].idservicio;
                    $("#tbDetalle").append("<tr id='tr"+datos[d].idservicio+"'><td><input type='hidden' id='txtIdTipoServicio"+datos[d].idservicio+"' name='txtIdTipoServicio"+datos[d].idservicio+"' value='"+datos[d].idtiposervicio+"' /><input type='hidden' id='txtIdServicio"+datos[d].idservicio+"' name='txtIdServicio"+datos[d].idservicio+"' value='"+datos[d].id+"' /><input type='text' data='numero' style='width: 40px;' class='form-control input-xs' id='txtCantidad"+datos[d].idservicio+"' name='txtCantidad"+datos[d].idservicio+"' value='"+datos[d].cantidad+"' size='3' onkeydown=\"if(event.keyCode==13){calcularTotal()}\" onblur=\"calcularTotalItem('"+datos[d].idservicio+"')\" /></td>"+
                    "<td><input type='checkbox' id='chkCopiar"+datos[d].idservicio+"' onclick=\"checkMedico(this.checked,'"+datos[d].idservicio+"')\" /></td>"+
                    "<td><input type='text' class='form-control input-xs' id='txtMedico"+datos[d].idservicio+"' name='txtMedico"+datos[d].idservicio+"' value='"+datos[d].medico+"' /><input type='hidden' id='txtIdMedico"+datos[d].idservicio+"' name='txtIdMedico"+datos[d].idservicio+"' value='"+datos[d].idmedico+"' /></td>"+
                    "<td align='left'>"+datos[d].tiposervicio+"</td><td>"+datos[d].servicio+"</td>"+
                    "<td><input type='hidden' id='txtPrecio2"+datos[d].idservicio+"' name='txtPrecio2"+datos[d].idservicio+"' value='0' /><input type='text' size='5' class='form-control input-xs' style='width: 60px;' data='numero' id='txtPrecio"+datos[d].idservicio+"' name='txtPrecio"+datos[d].idservicio+"' value='"+datos[d].precio+"' onkeydown=\"if(event.keyCode==13){calcularTotalItem2('"+datos[d].idservicio+"')}\" onblur=\"calcularTotalItem2('"+datos[d].idservicio+"')\" /></td>"+
                    "<td><input type='text' size='5' style='width: 60px;' class='form-control input-xs' data='numero' id='txtDescuento"+datos[d].idservicio+"' name='txtDescuento"+datos[d].idservicio+"' value='0' onkeydown=\"if(event.keyCode==13){calcularTotalItem2('"+datos[d].idservicio+"')}\" onblur=\"calcularTotalItem2('"+datos[d].idservicio+"')\" style='width:50%' /></td>"+
                    "<td><input type='hidden' id='txtPrecioHospital2"+datos[d].idservicio+"' name='txtPrecioHospital2"+datos[d].idservicio+"' value='"+datos[d].precio+"' /><input type='text' size='5' style='width: 60px;' class='form-control input-xs' data='numero'  id='txtPrecioHospital"+datos[d].idservicio+"' name='txtPrecioHospital"+datos[d].idservicio+"' value='"+datos[d].precio+"' onblur=\"calcularTotalItem2("+datos[d].idservicio+")\" /></td>"+
                    "<td><input type='hidden' id='txtPrecioMedico2"+datos[d].idservicio+"' name='txtPrecioMedico2"+datos[d].idservicio+"' value='0' /><input type='text' size='5' class='form-control input-xs' data='numero'  id='txtPrecioMedico"+datos[d].idservicio+"' name='txtPrecioMedico"+datos[d].idservicio+"' value='0' style='width: 60px;' /></td>"+
                    "<td><input type='text' style='width: 60px;' readonly='' data='numero' class='form-control input-xs' size='5' name='txtTotal"+datos[d].idservicio+"' id='txtTotal"+datos[d].idservicio+"' value='"+datos[d].total+"' /></td>"+
                    "<td><a href='#' id='Quitar"+datos[d].idservicio+"' onclick=\"quitarServicio('"+datos[d].idservicio+"')\"><i class='fa fa-minus-circle' title='Quitar' width='20px' height='20px'></i></td></tr>");
                }else{
                    datos[d].id="00"+Math.round(Math.random()*100);
                    datos[d].idservicio="01"+Math.round(Math.random()*100)+datos[d].idservicio;
                    $("#tbDetalle").append("<tr id='tr"+datos[d].idservicio+"'><td><input type='hidden' id='txtIdTipoServicio"+datos[d].idservicio+"' name='txtIdTipoServicio"+datos[d].idservicio+"' value='"+datos[d].idtiposervicio+"' /><input type='hidden' id='txtIdServicio"+datos[d].idservicio+"' name='txtIdServicio"+datos[d].idservicio+"' value='"+datos[d].id+"' /><input type='text' data='numero' style='width: 40px;' class='form-control input-xs' id='txtCantidad"+datos[d].idservicio+"' name='txtCantidad"+datos[d].idservicio+"' value='"+datos[d].cantidad+"' size='3' onkeydown=\"if(event.keyCode==13){calcularTotal()}\" onblur=\"calcularTotalItem('"+datos[d].idservicio+"')\" /></td>"+
                    "<td><input type='checkbox' id='chkCopiar"+datos[d].idservicio+"' onclick=\"checkMedico(this.checked,'"+datos[d].idservicio+"')\" /></td>"+
                    "<td><input type='text' class='form-control input-xs' id='txtMedico"+datos[d].idservicio+"' name='txtMedico"+datos[d].idservicio+"' value='"+datos[d].medico+"' /><input type='hidden' id='txtIdMedico"+datos[d].idservicio+"' name='txtIdMedico"+datos[d].idservicio+"' value='"+datos[d].idmedico+"' /></td>"+
                    "<td align='left'>"+datos[d].tiposervicio+"</td><td><textarea class='form-control input-xs txtareaa' id='txtServicio"+datos[d].idservicio+"' name='txtServicio"+datos[d].idservicio+"' >"+datos[d].servicio+"</textarea></td>"+
                    "<td><input type='hidden' id='txtPrecio2"+datos[d].idservicio+"' name='txtPrecio2"+datos[d].idservicio+"' value='0' /><input type='text' size='5' class='form-control input-xs' style='width: 60px;' data='numero' id='txtPrecio"+datos[d].idservicio+"' name='txtPrecio"+datos[d].idservicio+"' value='"+datos[d].precio+"' onkeydown=\"if(event.keyCode==13){calcularTotalItem2('"+datos[d].idservicio+"')}\" onblur=\"calcularTotalItem2('"+datos[d].idservicio+"')\" /></td>"+
                    "<td><input type='text' size='5' style='width: 60px;' class='form-control input-xs' data='numero' id='txtDescuento"+datos[d].idservicio+"' name='txtDescuento"+datos[d].idservicio+"' value='0' onkeydown=\"if(event.keyCode==13){calcularTotalItem2('"+datos[d].idservicio+"')}\" onblur=\"calcularTotalItem2('"+datos[d].idservicio+"')\" style='width:50%' /></td>"+
                    "<td><input type='hidden' id='txtPrecioHospital2"+datos[d].idservicio+"' name='txtPrecioHospital2"+datos[d].idservicio+"' value='"+datos[d].precio+"' /><input type='text' size='5' style='width: 60px;' class='form-control input-xs' data='numero'  id='txtPrecioHospital"+datos[d].idservicio+"' name='txtPrecioHospital"+datos[d].idservicio+"' value='"+datos[d].precio+"' onblur=\"calcularTotalItem2("+datos[d].idservicio+")\" /></td>"+
                    "<td><input type='hidden' id='txtPrecioMedico2"+datos[d].idservicio+"' name='txtPrecioMedico2"+datos[d].idservicio+"' value='0' /><input type='text' size='5' class='form-control input-xs' data='numero'  id='txtPrecioMedico"+datos[d].idservicio+"' name='txtPrecioMedico"+datos[d].idservicio+"' value='0' style='width: 60px;' /></td>"+
                    "<td><input type='text' style='width: 60px;' readonly='' data='numero' class='form-control input-xs' size='5' name='txtTotal"+datos[d].idservicio+"' id='txtTotal"+datos[d].idservicio+"' value='"+datos[d].total+"' /></td>"+
                    "<td><a href='#' id='Quitar"+datos[d].idservicio+"' onclick=\"quitarServicio('"+datos[d].idservicio+"')\"><i class='fa fa-minus-circle' title='Quitar' width='20px' height='20px'></i></td></tr>");
                }
                //console.log(datos[d].idservicio);
                
                if(datos[d].situacionentrega!="A"){
                    carro.push(datos[d].idservicio);
                }else{
                    $("#Quitar"+datos[d].idservicio).css('display','none');
                }
                calcularTotalItem(datos[d].idservicio);
                $(':input[data="numero"]').inputmask('decimal', { radixPoint: ".", autoGroup: true, groupSeparator: "", groupSize: 3, digits: 2 });
                eval("var planes"+datos[d].idservicio+" = new Bloodhound({"+
                    "datumTokenizer: function (d) {"+
                        "return Bloodhound.tokenizers.whitespace(d.value);"+
                    "},"+
                    "limit: 10,"+
                    "queryTokenizer: Bloodhound.tokenizers.whitespace,"+
                    "remote: {"+
                        "url: 'medico/medicoautocompletar/%QUERY',"+
                        "filter: function (planes"+datos[d].idservicio+") {"+
                            "return $.map(planes"+datos[d].idservicio+", function (movie) {"+
                                "return {"+
                                    "value: movie.value,"+
                                    "id: movie.id,"+
                                "};"+
                            "});"+
                        "}"+
                    "}"+
                "});"+
                "planes"+datos[d].idservicio+".initialize();"+
                "$('#txtMedico"+datos[d].idservicio+"').typeahead(null,{"+
                    "displayKey: 'value',"+
                    "source: planes"+datos[d].idservicio+".ttAdapter()"+
                "}).on('typeahead:selected', function (object, datum) {"+
                    "$('#txtMedico"+datos[d].idservicio+"').val(datum.value);"+
                    "$('#txtIdMedico"+datos[d].idservicio+"').val(datum.id);"+
                    "copiarMedico('"+datos[d].idservicio+"');"+
                "});");
                $("#txtMedico"+datos[d].idservicio).focus(); 

            } 
        }
    });
}

function solicitarHistoria(){
    if($(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[name="historia_id"]').val()!="" && $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[name="historia_id"]').val()!="0"){
        $.ajax({
            type: "POST",
            url: "seguimiento/solicitar",
            data: "historia_id="+$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[name="historia_id"]').val()+"&_token="+$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[name="_token"]').val(),
            success: function(a) {
                alert('Solicitud Enviada');
            }
        });
    }else{
        alert('No ha seleccionado historia');
    }
}

function divFormaPago(num, mostrar) {
    var m;
    if(mostrar == '0') {
        m = '1';
        $('#cbx' + num).attr('checked', false);
        $('#divcbx' + num).css('color', 'black');
        if(num == '0') {
            $('#efectivo').attr('readonly', true).val('');
        } else if(num == '1') {
            $('#visa').attr('readonly', true).val('');
            $('#numvisa').attr('readonly', true).val('');
        } else {
            $('#master').attr('readonly', true).val('');
            $('#nummaster').attr('readonly', true).val('');
        }
    } else {
        m = '0';
        $('#cbx' + num).attr('checked', true);
        $('#divcbx' + num).css('color', 'red');
        if(num == '0') {
            $('#efectivo').attr('readonly', false).focus();
        } else if(num == '1') {
            $('#visa').attr('readonly', false).focus();
            $('#numvisa').attr('readonly', false);
        } else {
            $('#master').attr('readonly', false).focus();
            $('#nummaster').attr('readonly', false);
        }
    }
    $('#divcbx' + num).attr("onclick", "divFormaPago('" + num + "', '" + m + "');");
    calcularTotalPago();
}

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
    var preg = /^([0-9]+\.?[0-9]{0,3})$/; 
    if(preg.test(__val__) === true){
        return true;
    }else{
       return false;
    }       
}

function buscarEmpresa() {
    ruc = $("#ccruc").val();     
    $.ajax({
        type: 'GET',
        url: "ticket/buscarEmpresa",
        data: "ruc="+ruc,
        beforeSend(){
            $("#ccruc").val('Comprobando...');
        },
        success: function (a) {
            if(a == '')  {
                buscarEmpresa2(ruc);
            } else {
                var e = a.split(';;');
                $("#ccruc").val(ruc);
                $('#ccrazon').val(e[0]);
                $('#ccdireccion').val(e[1]);
            }
        }
    });
}

function buscarEmpresa2(ruc){
    $.ajax({
        type: 'GET',
        url: "SunatPHP/demo.php",
        data: "ruc="+ruc,
        beforeSend(){
            $("#ccruc").val('Comprobando...');
        },
        success: function (data, textStatus, jqXHR) {
            if(data.RazonSocial == null) {
                alert('El RUC ingresado no existe... Digite uno válido.');
                $("#ccruc").val('').focus();
                $("#ccrazon").val('');
                $("#ccdireccion").val('');
            } else {
                $("#ccruc").val(ruc);
                $("#ccrazon").val(data.RazonSocial);
                $("#ccdireccion").val('-');
            }
        }
    });
}

function camposNoVacios() {     
    if(!$('#numeroventa').val() || !$('#serieventa').val()) {
        $('#serieventa').focus();
        alert('Ingresa un numero de comprobante.');
        return false;
    } else {
        if(!$('#visa').attr('readonly')) {
            if($('#visa').val().length == 0) {
                $('#visa').focus();
                alert('Ingresa un monto para visa.');
                return false;
            }
            /*if($('#numvisa').val().length == 0) {
                $('#numvisa').focus();
                alert('Ingresa un numero para visa.');
                return false;
            }*/
        } 
        if(!$('#master').attr('readonly')) {
            if($('#master').val().length == 0) {
                $('#master').focus();
                alert('Ingresa un monto para master.');
                return false;
            }
            /*if($('#nummaster').val().length == 0) {
                $('#nummaster').focus();
                alert('Ingresa un numero para master.');
                return false;
            }*/
        }
        return true;
    }       
}

function guardarTemporal() { 
    $('.xyz').each(function() {
        var input = $(this).val();
        $(this).attr('value', input);
        $(this).find("option[value='" + input + "']").attr("selected", true);
    });

    var tabladetallestemporal = $("#tablaDetallesTemporal").html();
    var person_id = $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="person_id"]').val();
    var tipodocumento = $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="tipodocumento"]').val();
    var plan_id = $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="plan_id"]').val();
    var deducible = $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="deducible"]').val();
    var coa = $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="coa"]').val();
    var tipopaciente = $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="tipopaciente"]').val();
    var ruc = $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="ccruc"]').val();
    var referido_id = $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="referido_id"]').val();
    $.ajax({
        url: 'ticket/guardarTemporal',
        type: 'POST',
        data: {
            "tabladetallestemporal": tabladetallestemporal,
            "person_id": person_id,
            "tipodocumento": tipodocumento,
            "plan_id": plan_id,
            "deducible": deducible,
            "coa": coa,
            "tipopaciente": tipopaciente,
            "ruc": ruc,
            "referido_id": referido_id,
            "_token": "{{ csrf_token() }}",
        },
    }).done(function(e) {
        if(e === "OK") {
            alert('Plantilla Guardada Correctamente');
        } else {
            alert('No se pudo Guardar');
        }        
    }).fail(function(){
        alert('No se pudo Guardar');
    });
}

function mostrarTemporal() {
    $.ajax({
        url: 'ticket/mostrarTemporal',
        type: 'POST',
        data: {
            "_token": "{{ csrf_token() }}",
        },
        dataType: 'JSON',
        success: function(e){
            $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="person_id"]').val(e['person_id']);
            $("#tablaDetallesTemporal").html(e['tabla']);
            $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="tipodocumento"]').val(e['tipodocumento']);            
            $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="historia_id"]').val(e['historia_id']);
            $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="numero_historia"]').val(e['numero_historia']);
            $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="paciente"]').val(e['paciente']);
            $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="dni"]').val(e['dni']);
            $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="plan"]').val(e['plan']);
            $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="plan_id"]').val(e['plan_id']);
            $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="coa"]').val(parseFloat(e['coa']).toFixed(2));
            $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="deducible"]').val(parseFloat(e['deducible']).toFixed(2));            
            $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="tipopaciente"]').val(e['tipopaciente']);
            $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="referido_id"]').val(e['referido_id']);
            $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="referido"]').val(e['nombre_referido']);
            $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="ccruc"]').val(e['ruc']);

            var tt = 0;
            if($('#total').val() !== '') {
                tt = $('#total').val();
            }

            $('#efectivo').val(parseFloat(tt).toFixed(2));
            $('#visa').val('');
            $('#master').val('');

            calcularTotalPago();
            generarNumero();  
            cambiartipodoc();
            
            $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="person_id"]').val(e['person_id']);

            $('.txtareaa').each(function() {
                var value = $(this).attr('value');
                //alert(value);
                $(this).html(value);
                
            });

            if(e['ruc'].length == 11) {
                buscarEmpresa();
            }

            //vacio carro y seteo el carro nuevamente con los datos nuevos obviamente :v
            carro.length = 0;
            var listilla = '';
            $('#cuerpoTabla tr').each(function(index, el) {
                if($(this).attr('id')) {
                    var id = $(this).attr('id');
                    listilla+=','+id.substring(2,id.lenght);
                    carro.push(id.substring(2,id.lenght));
                    ////

                    $(':input[data="numero"]').inputmask('decimal', { radixPoint: ".", autoGroup: true, groupSeparator: "", groupSize: 3, digits: 2 });

                    eval("var planes"+id.substring(2,id.lenght)+" = new Bloodhound({"+
                        "datumTokenizer: function (d) {"+
                            "return Bloodhound.tokenizers.whitespace(d.value);"+
                        "},"+
                        "limit: 10,"+
                        "queryTokenizer: Bloodhound.tokenizers.whitespace,"+
                        "remote: {"+
                            "url: 'medico/medicoautocompletar/%QUERY',"+
                            "filter: function (planes"+id.substring(2,id.lenght)+") {"+
                                "return $.map(planes"+id.substring(2,id.lenght)+", function (movie) {"+
                                    "return {"+
                                        "value: movie.value,"+
                                        "id: movie.id,"+
                                    "};"+
                                "});"+
                            "}"+
                        "}"+
                    "});"+
                    "planes"+id.substring(2,id.lenght)+".initialize();"+
                    "$('#txtMedico"+id.substring(2,id.lenght)+"').typeahead(null,{"+
                        "displayKey: 'value',"+
                        "source: planes"+id.substring(2,id.lenght)+".ttAdapter()"+
                    "}).on('typeahead:selected', function (object, datum) {"+
                        "$('#txtMedico"+id.substring(2,id.lenght)+"').val(datum.value);"+
                        "$('#txtIdMedico"+id.substring(2,id.lenght)+"').val(datum.id);"+
                        "copiarMedico("+id.substring(2,id.lenght)+");"+
                    "});");

                    ////
                }
            });
            listilla=listilla.substring(1, listilla.lenght);
            $('#listServicio').val(listilla);
            //seleccionarServicioOtro();
        },
    }).fail(function(){
        alert('Ocurrió un error');
    });
}

<?php
if(!is_null($ticket)){
    echo "agregarDetalle(".$ticket->id.");";
}
?>

</script>