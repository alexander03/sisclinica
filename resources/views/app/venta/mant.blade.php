<div id="divMensajeError{!! $entidad !!}"></div>
{!! Form::model($venta, $formData) !!}	
	{!! Form::hidden('listar', $listar, array('id' => 'listar')) !!}
	{!! Form::hidden('total', '0', array( 'id' => 'total')) !!}
	{!! Form::hidden('detalle', 'false', array( 'id' => 'detalle')) !!}
	{!! Form::hidden('caja_id', $caja_id, array( 'id' => 'caja_id')) !!}
	<input type="hidden" name="cantproductos" id="cantproductos">
	<div class="col-lg-4 col-md-4 col-sm-4" style="padding:0;margin: 0">
		<div class="form-group" style="height: 12px;">
			{!! Form::label('documento', 'Documento:', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
			<div class="col-lg-7 col-md-7 col-sm-7">
				{!! Form::select('documento', $cboDocumento, null, array('style' => 'background-color: #D4F0FF;' ,'class' => 'form-control input-xs', 'id' => 'documento', 'onchange' => 'generarNumero(this.value);')) !!}
			</div>
		</div>
		<div class="form-group" style="height: 12px;">
			{!! Form::label('tipoventa', 'Tipo Venta:', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
			<div class="col-lg-7 col-md-7 col-sm-7">
				{!! Form::select('tipoventa', $cboTipoventa, null, array('style' => 'background-color: #D4F0FF;' ,'class' => 'form-control input-xs', 'id' => 'tipoventa', 'onchange' => 'cambiotipoventa();')) !!}
			</div>
		</div>
		<!--<div class="form-group" style="height: 12px;">
			{! Form::label('formapago', 'Form Pago:', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
			<div class="col-lg-7 col-md-7 col-sm-7">
				{! Form::select('formapago', $cboFormapago, null, array('class' => 'form-control input-xs', 'id' => 'formapago', 'onchange' => 'validarFormaPago(this.value)')) !!}
			</div>
		</div>-->
		
		<div class="form-group" style="height: 12px;">
			{!! Form::label('numerodocumento', 'Nro Doc:', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
			<div class="col-lg-7 col-md-7 col-sm-7">
				{!! Form::text('numerodocumento', '', array('class' => 'form-control input-xs', 'id' => 'numerodocumento', 'placeholder' => 'Ingrese numerodocumento' ,'readonly' => 'true')) !!}
			</div>

		</div>
		<div id="opcEmpresa" style="display: none;">
			<div class="form-group" style="height: 12px;">
				{!! Form::label('ruc2', 'RUC:', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
				{!! Form::hidden('empresa_id', null, array('id' => 'empresa_id')) !!}
				<div class="col-lg-7 col-md-7 col-sm-7">
					{!! Form::text('nombreempresa', null, array('style' => 'background-color: #FFEEC5;' ,'class' => 'form-control input-xs', 'id' => 'nombreempresa', 'placeholder' => 'Seleccione Empresa', 'style'=>'display:none')) !!}
					{!! Form::text('ruc2', null, array('style' => 'background-color: #FFEEC5;' ,'class' => 'form-control input-xs', 'id' => 'ruc2', 'placeholder' => 'Digite RUC')) !!}
				</div>			
			</div>
			<div class="form-group" style="height: 12px;">
				{!! Form::label('nombreempresa2', 'Empresa:', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
				<div class="col-lg-7 col-md-7 col-sm-7">
					{!! Form::text('nombreempresa2', null, array('style' => 'background-color: #FFEEC5;' ,'class' => 'form-control input-xs', 'id' => 'nombreempresa2', 'readonly' => 'readonly')) !!}
				</div>
			</div>
			<div class="form-group" style="height: 12px;">
				{!! Form::label('direccion2', 'Direccion:', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
				<div class="col-lg-7 col-md-7 col-sm-7">
					{!! Form::text('direccion2', null, array('style' => 'background-color: #FFEEC5;' ,'class' => 'form-control input-xs', 'id' => 'direccion2', 'placeholder' => 'Digite Direccion')) !!}				
				</div>
			</div>
		</div>
		<div class="form-group" style="height: 12px;">
			{!! Form::label('nombrepersona', 'Cliente:', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
			{!! Form::hidden('person_id', null, array('id' => 'person_id')) !!}
			<div class="col-lg-7 col-md-7 col-sm-7">
				<div class="input-group">
					{!! Form::text('nombrepersona', null, array('style' => 'background-color: #FFEEC5;' ,'class' => 'form-control input-xs', 'id' => 'nombrepersona', 'placeholder' => 'Seleccione Cliente')) !!}				
					<span style="cursor: pointer;" id="btnnombrepersona" class="btn-xs input-group-addon input-xs"><i class="glyphicon glyphicon-plus"></i></span>
				</div>
			</div>				
		</div>
		<div class="form-group" style="height: 12px;">
		
		{!! Form::label('nombredoctor', 'Medico:', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
			{!! Form::hidden('doctor_id', null, array('id' => 'doctor_id')) !!}
			<div class="col-lg-7 col-md-7 col-sm-7">
				{!! Form::text('nombredoctor', null, array('style' => 'background-color: #FFEEC5;','class' => 'form-control input-xs', 'id' => 'nombredoctor', 'placeholder' => 'Seleccione Medico')) !!}
				
			</div>
		</div>
		<div class="form-group" style="display: none">
    		{!! Form::label('paciente', 'Paciente:', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
    		<div class="col-lg-7 col-md-7 col-sm-7">
            {{-- Form::hidden('person_id', null, array('id' => 'person_id')) --}}
    			{!! Form::text('paciente', null, array('class' => 'form-control input-xs', 'id' => 'paciente', 'placeholder' => 'Ingrese Paciente')) !!}
    		</div>
    	</div>
		<div class="form-group" id="divConvenio" style="height: 12px;">
			{!! Form::label('conveniofarmacia', 'Convenio:', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
			{!! Form::hidden('conveniofarmacia_id', null, array('id' => 'conveniofarmacia_id')) !!}
			<div class="col-lg-7 col-md-7 col-sm-7">
				{!! Form::text('conveniofarmacia', null, array('class' => 'form-control input-xs', 'id' => 'conveniofarmacia', 'placeholder' => 'Seleccione Convenio')) !!}
				
			</div>
		</div>
		<div class="form-group" id="divDescuentokayros" style="height: 12px;">
			{!! Form::label('fecha', 'Fecha:', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
			<div class="col-lg-3 col-md-3 col-sm-3">
				<div class='input-group input-group-xs' id='divfecha'>
					{!! Form::text('fecha', date('d/m/Y'), array('class' => 'form-control input-xs', 'id' => 'fecha', 'placeholder' => 'Ingrese fecha')) !!}
					
				</div>
			</div>
			
			{!! Form::label('descuentokayros', 'Dcto. Kayros:', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
			<div class="col-lg-2 col-md-2 col-sm-2">
				{!! Form::text('descuentokayros', null, array('class' => 'form-control input-xs', 'id' => 'descuentokayros')) !!}
				
			</div>	
		</div>
		<div class="form-group" style="height: 12px;">
		<div class="col-lg-7 col-md-7 col-sm-7">
			{!! Form::label('copago', 'Dcto Planilla:', array('class' => 'col-lg-7 col-md-7 col-sm-7 control-label')) !!}	
				<div class="col-lg-1 col-md-1 col-sm-1" >
        			<input name="descuentoplanilla" id="descuentoplanilla" value="NO" type="checkbox" onclick="pendienteplanilla(this.checked)" />
        		</div>
			</div>
			{!! Form::label('copago', 'Copago:', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
			<div class="col-lg-2 col-md-2 col-sm-2">
				{!! Form::text('copago', null, array('class' => 'form-control input-xs', 'id' => 'copago')) !!}
				
			</div>
		</div>	
		<div class="form-group descuentopersonal" style="display: none;height: 12px;">
            {!! Form::label('personal', 'Personal:', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
            <div class="col-lg-9 col-md-9 col-sm-9">
            {!! Form::hidden('personal_id', null, array('id' => 'personal_id')) !!}
            {!! Form::text('personal', null, array('class' => 'form-control input-xs', 'id' => 'personal', 'placeholder' => 'Ingrese Personal')) !!}
            </div>
		</div>
		<div class="form-group" style="height: 12px;display: none;">
		<div class="col-lg-3 col-md-3 col-sm-3">
				
				
			</div>
		{!! Form::label('nombreconvenio', 'Convenio:', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
			<div class="col-lg-6 col-md-6 col-sm-6">
				{!! Form::text('nombreconvenio', null, array('class' => 'form-control input-xs', 'id' => 'nombreconvenio', 'readonly' => '')) !!}
				
			</div>
		</div>
		<div class="form-group tarjeta" style="height: 12px;">
			{!! Form::label('fecha', 'Tarjeta:', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
			<div class="col-lg-4 col-md-4 col-sm-4">
				<div class='input-group input-group-xs' id='divfecha'>
					{!! Form::select('tipotarjeta', $cboTipoTarjeta, null, array('style' => 'background-color: rgb(25,241,227);' ,'class' => 'form-control input-xs', 'id' => 'tipotarjeta')) !!}
					
				</div>
			</div>
			{!! Form::label('tipotarjeta2', 'Tipo:', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
			<div class="col-lg-4 col-md-4 col-sm-4">
				<div class='input-group input-group-xs' id='divfecha'>
					{!! Form::select('tipotarjeta2', $cboTipoTarjeta2, null, array('style' => 'background-color: rgb(25,241,227);' ,'class' => 'form-control input-xs', 'id' => 'tipotarjeta2')) !!}
					
				</div>
			</div>
			
		</div>
		<div class="form-group tarjeta" style="height: 12px;">
			<div class="col-lg-5 col-md-5 col-sm-5">
				
			</div>
			{!! Form::label('nroref', 'Nro. Op.:', array('class' => 'col-lg-3 col-md-3 col-sm-3 control-label')) !!}
            <div class="col-lg-3 col-md-3 col-sm-3">
                {!! Form::text('nroref', null, array('class' => 'form-control input-xs', 'id' => 'nroref')) !!}
            </div>
		</div>
	</div>	
	<div class="col-lg-5 col-md-5 col-sm-5" style="padding-right:10px;margin:0">
		<div class="form-group" style="height: 12px;">
			{!! Form::label('nombreproducto', 'Producto:', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
			<div class="col-lg-6 col-md-6 col-sm-6" style="margin: 0;padding: 5px;">
				{!! Form::text('nombreproducto', null, array('class' => 'form-control input-xs', 'id' => 'nombreproducto', 'placeholder' => 'Ingrese nombre','onkeyup' => 'buscarProducto($(this).val());')) !!}
			</div>
			<input type="hidden" name="idsesioncarrito" id="idsesioncarrito" value="<?php echo date("YmdHis");?>">
			{!! Form::label('cantidad', 'Cantidad:', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label')) !!}
			<div class="col-lg-2 col-md-2 col-sm-2" style="margin: 0; padding: 5px;">
				{!! Form::text('cantidad', null, array('class' => 'form-control input-xs', 'id' => 'cantidad', 'onkeyup' => "javascript:this.value=this.value.toUpperCase();")) !!}
			</div>
			{!! Form::hidden('producto_id', null, array( 'id' => 'producto_id')) !!}
			{!! Form::hidden('preciokayros', null, array( 'id' => 'preciokayros')) !!}

			{!! Form::hidden('precioventa', null, array('id' => 'precioventa')) !!}
			{!! Form::hidden('stock', null, array('id' => 'stock')) !!}
		</div>
		<div class="form-group table-responsive" id="divProductos" style="overflow:auto; height:230px; border:1px outset">
			<table class='table-condensed table-hover' border='1'>
				<thead>
					<tr>
						<th class='text-center' style='width:220px;'><span style='display: block; font-size:.7em'>P. Activo</span></th>
						<th class='text-center' style='width:220px;'><span style='display: block; font-size:.7em'>Nombre</span></th>
						<th class='text-center' style='width:20px;'><span style='display: block; font-size:.7em'>Present.</span></th>
						<th class='text-center' style='width:20px;'><span style='display: block; font-size:.7em'>Fracción</span></th>
						<th class='text-center' style='width:20px;'><span style='display: block; font-size:.7em'>Stock</span></th>
						<th class='text-center' style='width:20px;'><span style='display: block; font-size:.7em'>P.Kayros</span></th>
						<th class='text-center' style='width:20px;'><span style='display: block; font-size:.7em'>P.Venta</span></th>
					</tr>
				</thead>
				<tbody id='tablaProducto'>
					<tr><td align='center' colspan='7'>Digite más de 3 caracteres.</td></tr>
				</tbody>
			</table>
		</div>
	</div>
	<div class="col-lg-3 col-md-3 col-sm-3">
		<div class="row">
			<div class="col-lg-12 col-md-12 col-sm-12">
				<label for="divcbx0" class="col-lg-4 col-md-4 col-sm-4 control-label datocaja caja input-sm">Forma Pago:</label>
				<label id="divcbx0" class="checkbox-inline" style="color:red" onclick="divFormaPago('0', '0')">
			      	<input style="display: none;" type="checkbox" id="cbx0">EF
			    </label>
			    <label id="divcbx1" class="checkbox-inline" onclick="divFormaPago('1', '1')">
			      	<input style="display: none;" type="checkbox" id="cbx1">VI
			    </label>
			    <label id="divcbx2" class="checkbox-inline" onclick="divFormaPago('2', '1')">
			      	<input style="display: none;" type="checkbox" id="cbx2">MA
			    </label>
			</div>        	
	    </div>
	    <br>
	    <div class="row">
		    <div class="col-lg-12 col-md-12 col-sm-12">	    	
			    <div class="input-group">
					<span class="input-group-addon input-xs">EFECTIVO</span>
					<input onkeypress="return filterFloat(event,this);" onkeyup="calcularTotalPago();" name="efectivo" id="efectivo" type="text" class="form-control input-xs">
				</div>
				<div class="input-group">
					<span class="input-group-addon input-xs">VISA.</span>
					<input onkeypress="return filterFloat(event,this);" onkeyup="calcularTotalPago();" name="visa" id="visa" type="text" class="form-control input-xs" readonly="">
					<span style="display:none;" class="input-group-addon input-xs">N°</span>
					<input style="display:none;" onkeypress="return filterFloat(event,this);" name="numvisa" id="numvisa" type="text" class="form-control input-xs" readonly="">
				</div>
				<div class="input-group">
					<span class="input-group-addon input-xs">MAST.</span>
					<input onkeypress="return filterFloat(event,this);" onkeyup="calcularTotalPago();" name="master" id="master" type="text" class="form-control input-xs" readonly="">
					<span style="display:none;" class="input-group-addon input-xs">N°</span>
					<input style="display:none;" onkeypress="return filterFloat(event,this);" name="nummaster" id="nummaster" type="text" class="form-control input-xs" readonly="">
				</div>	
			</div>						
		</div>	
		<hr>
		<div class="row">
			<div class="col-lg-12 col-md-12 col-sm-12">	    	
			    <div class="input-group">
					<span class="input-group-addon input-xs">TOTAL</span>
					<input name="total2" id="total2" type="text" class="form-control input-xs" readonly="" value="0.00">
				</div>
			</div>
			<div class="col-lg-12 col-md-12 col-sm-12">	    	
			    <b id="mensajeMontos" style="color: green;">Los montos coindicen.</b>
			</div>
		</div>	
		<br>
		<div class="form-group">
			<div class="col-lg-12 col-md-12 col-sm-12 text-right">
				<!--<div align="center" class="col-lg-3 ">
		       {-- Form::button('<i class="glyphicon glyphicon-plus"></i> Agregar', array('class' => 'btn btn-info btn-xs', 'id' => 'btnAgregar', 'onclick' => 'ventanaproductos();')) --}   
		    	
		    	</div>-->
				{!! Form::button('<i class="fa fa-check fa-lg"></i> '.$boton, array('class' => 'btn btn-success btn-sm', 'id' => 'btnGuardar', 'onclick' => 'enviar();')) !!}
				{!! Form::button('<i class="fa fa-exclamation fa-lg"></i> Cancelar', array('class' => 'btn btn-warning btn-sm', 'id' => 'btnCancelar'.$entidad, 'onclick' => 'cerrarModal();')) !!}
			</div>
		</div>
	</div>
	<div class="form-group" style="display: none;">
		<div class="col-lg-12 col-md-12 col-sm-12" >
			{!! Form::label('codigo', 'Comprobar Productos:', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
			<div class="col-lg-5 col-md-5 col-sm-5">
				{!! Form::text('codigo', null, array('class' => 'form-control input-xs', 'id' => 'codigo', 'placeholder' => 'Ingrese codigo')) !!}
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-lg-12 col-md-12 col-sm-12">
			<div id="divDetail" class="table-responsive" style="overflow:auto; height:220px; padding-right:10px; border:1px outset">
		        <table style="width: 100%;" class="table-condensed table-striped" border="1">
		            <thead>
		                <tr>
		                    <th bgcolor="#E0ECF8" class='text-center'>N°</th>
		                    <th bgcolor="#E0ECF8" class='text-center' style="width:550px;">Producto</th>
		                    <th bgcolor="#E0ECF8" class='text-center' style="width:300px;">Cantidad</th>
		                    <th bgcolor="#E0ECF8" class="text-center" style="width:100px;">Precio Unit</th>
		                    <th bgcolor="#E0ECF8" class="text-center" style="width:90px;">Dscto</th>
		                    <th bgcolor="#E0ECF8" class="text-center" style="width:90px;">Subtotal</th>
		                    <th bgcolor="#E0ECF8" class='text-center'>Quitar</th>
		                </tr>
		            </thead>
		            <tbody id="detallesVenta">		            	
		            </tbody>
		            <tbody border="1">
		            	<tr>
		            		<th colspan="5" style="text-align: right;">TOTAL</th>
		            		<td class="text-center">
		            			<center id="totalventa2">0.00</center><input type="hidden" id="totalventa" readonly="" name="totalventa" value="0.00">
		            		</td>
		            	</tr>
		            </tbody>
		        </table>
		    </div>
		</div>
	 </div>
    <br>
{!! Form::close() !!}
<style type="text/css">
tr.resaltar {
    background-color: #A9F5F2;
    cursor: pointer;
}
</style>
<script type="text/javascript">
var valorbusqueda="";
var indice = -1;
var anterior = -1;

$(document).on('change', '#documento', function(e) {
	e.preventDefault();
	e.stopImmediatePropagation();
	if($(this).val() == '4') {
		$('#opcEmpresa').css('display', 'block');
	} else {
		$('#opcEmpresa').css('display', 'none');
	}
});

$(document).ready(function() {
	$('#detallesVenta').html('');
	$('#cantproductos').val('0');
	configurarAnchoModal('1300');
	cargarEfectivo();
	init(IDFORMMANTENIMIENTO+'{!! $entidad !!}', 'B', '{!! $entidad !!}');

		$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="total"]').inputmask('decimal', { radixPoint: ".", autoGroup: true, groupSeparator: ",", groupSize: 3, digits: 2 });

		//$(IDFORMMANTENIMIENTO + '{! $entidad !!} :input[id="cantidad"]').inputmask('decimal', { radixPoint: ".", autoGroup: true, groupSeparator: ",", groupSize: 3, digits: 2 });

		
	$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="fecha"]').inputmask("dd/mm/yyyy");
		$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="fecha"]').datetimepicker({
			pickTime: false,
			language: 'es'
		});

	$(IDFORMMANTENIMIENTO+'{!! $entidad !!}' + ' :input[id="codigo"]').keydown( function(e) {
			var key = e.charCode ? e.charCode : e.keyCode ? e.keyCode : 0;
			if(key == 13) {
				comprobarproducto ();
			}
		});

	$(IDFORMMANTENIMIENTO+'{!! $entidad !!}' + ' :input[id="nombrepersona"]').keydown( function(e) {
		$('#person_id').val('');
	});

	$(document).on("click", "#btnnombrepersona", function(e) {
		e.preventDefault();
		e.stopImmediatePropagation();
		var key = e.charCode ? e.charCode : e.keyCode ? e.keyCode : 0;
		//if(key == 13) {
			/*var documento = $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="documento"]').val();
			if (documento == 4) {
				modal('{{URL::route('venta.busquedaempresa')}}', '');
			}else{
				modal('{{URL::route('venta.busquedacliente')}}', '');
			}*/
				modal('{{URL::route('venta.busquedacliente')}}', '');
		//}
	});

	$(IDFORMMANTENIMIENTO+'{!! $entidad !!}' + ' :input[id="nombredoctor"]').keydown( function(e) {
			var key = e.charCode ? e.charCode : e.keyCode ? e.keyCode : 0;
			if(key == 27) {
				$('#nombreproducto').focus();
			}
		});
	$(IDFORMMANTENIMIENTO+'{!! $entidad !!}' + ' :input[id="nombreempresa"]').keydown( function(e) {
			var key = e.charCode ? e.charCode : e.keyCode ? e.keyCode : 0;
			if(key == 13) {
				/*var documento = $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="documento"]').val();
				if (documento == 4) {
					modal('{{URL::route('venta.busquedaempresa')}}', '');
				}else{
					modal('{{URL::route('venta.busquedacliente')}}', '');
				}*/
					modal('{{URL::route('venta.busquedaempresa')}}', '');
			}
		});
	$(IDFORMMANTENIMIENTO+'{!! $entidad !!}' + ' :input[id="nombreempresa"]').click(function(){
			/*var documento = $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="documento"]').val();
			if (documento == 4) {
				modal('{{URL::route('venta.busquedaempresa')}}', '');
			}else{
				modal('{{URL::route('venta.busquedacliente')}}', '');
			}*/
			modal('{{URL::route('venta.busquedaempresa')}}', '');
			
		});
	$(IDFORMMANTENIMIENTO+'{!! $entidad !!}' + ' :input[id="conveniofarmacia"]').focus(function(){
			abrirconvenios();
		});

	$(IDFORMMANTENIMIENTO+'{!! $entidad !!}' + ' :input[id="descuentokayros"]').keydown( function(e) {
			var key = e.charCode ? e.charCode : e.keyCode ? e.keyCode : 0;
			if(key == 13) {
				e.preventDefault();
				var inputs = $(this).closest('form').find(':input:visible:not([disabled]):not([readonly])');
				inputs.eq( inputs.index(this)+ 1 ).focus();
			}
		});
	$(IDFORMMANTENIMIENTO+'{!! $entidad !!}' + ' :input[id="copago"]').keydown( function(e) {
			var key = e.charCode ? e.charCode : e.keyCode ? e.keyCode : 0;
			if(key == 13) {
				e.preventDefault();
				var inputs = $(this).closest('form').find(':input:visible:not([disabled]):not([readonly])');
				inputs.eq( inputs.index(this)+ 1 ).focus();
			}
		});
	$(IDFORMMANTENIMIENTO+'{!! $entidad !!}' + ' :input[id="preciokayros"]').keydown( function(e) {
			var key = e.charCode ? e.charCode : e.keyCode ? e.keyCode : 0;
			if(key == 13) {
				e.preventDefault();
				var inputs = $(this).closest('form').find(':input:visible:not([disabled]):not([readonly])');
				inputs.eq( inputs.index(this)+ 1 ).focus();
			}
		});
	$(IDFORMMANTENIMIENTO+'{!! $entidad !!}' + ' :input[id="cantidad"]').keydown( function(e) {
			var key = e.charCode ? e.charCode : e.keyCode ? e.keyCode : 0;
			if(key == 13) {
				/*e.preventDefault();
				var inputs = $(this).closest('form').find(':input:visible:not([disabled]):not([readonly])');
				inputs.eq( inputs.index(this)+ 1 ).focus();*/
				addpurchasecart();
				$(IDFORMMANTENIMIENTO+'{!! $entidad !!}' + ' :input[id="nombreproducto"]').val('');
				$(IDFORMMANTENIMIENTO+'{!! $entidad !!}' + ' :input[id="cantidad"]').val('');
				indice = -1;
				$(IDFORMMANTENIMIENTO+'{!! $entidad !!}' + ' :input[id="nombreproducto"]').focus();
			}
		});


	var personas = new Bloodhound({
		datumTokenizer: function (d) {
			return Bloodhound.tokenizers.whitespace(d.value);
		},
		queryTokenizer: Bloodhound.tokenizers.whitespace,
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
        
        $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="person_id"]').val(datum.person_id);
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

	var doctores = new Bloodhound({
			datumTokenizer: function (d) {
				return Bloodhound.tokenizers.whitespace(d.value);
			},
			queryTokenizer: Bloodhound.tokenizers.whitespace,
			remote: {
				url: 'person/doctorautocompleting/%QUERY',
				filter: function (doctores) {
					return $.map(doctores, function (movie) {
						return {
							value: movie.value,
							id: movie.id
						};
					});
				}
			}
		});
		doctores.initialize();
		$('#nombredoctor').typeahead(null,{
			displayKey: 'value',
			source: doctores.ttAdapter()
		}).on('typeahead:selected', function (object, datum) {
			$('#doctor_id').val(datum.id);
		});

	$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="nombreproducto"]').on( 'keydown', function () {
        var e = window.event; 
        var keyc = e.keyCode || e.which;
        console.log(this.value);
        console.log(valorbusqueda);
        if(this.value.length>2 && keyc == 13 && valorbusqueda!=this.value){
            buscarProducto(this.value);
            valorbusqueda=this.value;
            this.focus();
            return false;
        }
        if(keyc == 38 || keyc == 40 || keyc == 27) {
            var tabladiv='tablaProducto';
			var child = document.getElementById(tabladiv).rows;
			//var indice = -1;
			var i=0;
            /*$('#tablaProducto tr').each(function(index, elemento) {
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
    		});*/		 
			// return
			//if(keyc == 13) { // enter
			if(keyc == 27) { // esc       				
			     if(indice != -1){
					var seleccionado = '';			 
					if(child[indice].id) {
					   seleccionado = child[indice].id;
					} else {
					   seleccionado = child[indice].id;
					}		 
					seleccionarProducto(seleccionado,$('#tdPrecioVenta'+seleccionado).text(),$('#tdPrecioKayros'+seleccionado).text(),$('#tdStock'+seleccionado).text());
					//seleccionarProducto(seleccionado);
				}
			} else {
				// abajo
				if(keyc == 40) {
					if(indice == (child.length - 1) ) {
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

				if (indice != -1) {
					var element = '#'+child[indice].id;
					$(element).addClass("resaltar");
					if (anterior  != -1) {
						element = '#'+anterior;
						$(element).removeClass("resaltar");
					}
					anterior = child[indice].id;
				}
			}
        }
    });


	cambiotipoventa();
	$(IDFORMMANTENIMIENTO+'{!! $entidad !!}' + ' :input[id="nombrepersona"]').focus();

	generarNumero('5');

}); 

function buscarProducto(valor){
    if(valor.length >= 3){
        $.ajax({
            type: "POST",
            url: "venta/buscandoproducto",
            data: "nombre="+$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="nombreproducto"]').val()+"&par=S&_token="+$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[name="_token"]').val(),
            success: function(a) {
                datos=JSON.parse(a);
                //$("#divProductos").html("<table class='table table-bordered table-condensed table-hover' border='1' id='tablaProducto'><thead><tr><th class='text-center'>P. Activo</th><th class='text-center'>Nombre</th><th class='text-center'>Presentacion</th><th class='text-center'>Stock</th><th class='text-center'>P.Kayros</th><th class='text-center'>P.Venta</th></tr></thead></table>");
                $("#divProductos").css("overflow-x",'hidden');
                var pag=parseInt($("#pag").val());
                var d=0;
                var a='';
                if(datos.length > 0) {
                	for(c=0; c < datos.length; c++){
	                	//Algoritmo para stock
	                	var stock = datos[c].stock;
	                	if(datos[c].presentacion !== 'UNIDAD') {
	                		var pres1 = 1;
	                		pres1 = Math.trunc(parseFloat(datos[c].stock)/parseFloat(datos[c].fraccion));
	                		entero = parseFloat(pres1);
	                		pres2 = parseFloat(datos[c].stock) - entero*parseFloat(datos[c].fraccion);
	                		stock = pres1.toString() + 'F' + pres2.toString();
	                	}
	                    a+="<tr style='cursor:pointer' class='escogerFila' id='"+datos[c].idproducto+"' onclick=\"seleccionarProducto('"+datos[c].idproducto+"','"+datos[c].precioventa+"','"+datos[c].preciokayros+"','"+datos[c].stock+"')\"><td align='center'><span style='display: block; font-size:.7em'>"+datos[c].principio+"</span></td><td><span style='display: block; font-size:.7em'>"+datos[c].nombre+"</span></td><td align='right'><span style='display: block; font-size:.7em'>"+datos[c].presentacion+"</span></td><td align='right'><span style='display: block; font-size:.7em'>"+datos[c].fraccion+"</span></td><td align='right' style='display: none;'><span style='display: none; font-size:.7em' id='tdStock"+datos[c].idproducto+"'>"+datos[c].stock+"</span></td><td align='right'><span style='display: block; font-size:.7em'>"+stock+"</span></td><td align='right'><span style='display: block; font-size:.7em' id='tdPrecioKayros"+datos[c].idproducto+"'>"+datos[c].preciokayros+"</span></td><td align='right'><span style='display: block; font-size:.7em' id='tdPrecioVenta"+datos[c].idproducto+"'>"+datos[c].precioventa+"</span></td></tr>";                              
	                }
                } else {
                	a+="<tr><td align='center' colspan='7'>Productos no encontrados.</td></tr>";   
                }
	                
                $("#tablaProducto").html(a); 
                $('#tablaProducto').DataTable({
                    "paging":         false,
                    "ordering"        :false                    
                });
                $('#tablaProducto_filter').css('display','none');
                $("#tablaProducto_info").css("display","none");
                calcularTotalPago();
    	    }
        });
    } else {
     	$("#tablaProducto").html("<tr><td align='center' colspan='7'>Digite más de 3 caracteres.</td></tr>");
    }
}

$(document).on('dblclick', '.escogerFila', function(e) {
	e.preventDefault();
	e.stopImmediatePropagation();
	if($('#tipoventa').val() == 'C') {
		var idproducto = $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="producto_id"]').val();
		modal('venta/editarKayros/' + idproducto, 'Editar Precio Kayros');
	}	
});

function seleccionarProducto(idproducto,precioventa,preciokayros,stock){
	//alert(idproducto);
	var _token =$('input[name=_token]').val();
	/*$.post('{ URL::route("venta.consultaproducto")}}', {idproducto: idproducto,_token: _token} , function(data){
		//$('#divDetail').html(data);
		//calculatetotal();
		var datos = data.split('@');
		$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="producto_id"]').val(datos[0]);
		$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="preciokayros"]').val(datos[1]);
		$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="precioventa"]').val(datos[2]);
		$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="stock"]').val(datos[3]);		
	});*/
	if(parseInt(preciokayros) == 0 && $('#tipoventa').val() == 'C') {
		modal('venta/editarKayros/' + idproducto, 'Editar Precio Kayros');
	}
	$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="producto_id"]').val(idproducto);
	$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="preciokayros"]').val(preciokayros);
	$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="precioventa"]').val(precioventa);
	$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="stock"]').val(stock);		
	$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="cantidad"]').focus();
}

function ventanaproductos() {
	var tipoventa = $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="tipoventa"]').val();
	var descuentokayros = $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="descuentokayros"]').val();
	var copago = $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="copago"]').val();
	modal('{{URL::route('venta.buscarproducto')}}'+'?tipoventa='+tipoventa+'&descuentokayros='+descuentokayros+'&copago='+copago, '');
}


function abrirconvenios() {
	modal('{{URL::route('venta.buscarconvenio')}}', '');
}

function pendienteplanilla(check){
    if(check){
        $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="descuentoplanilla"]').val('SI');
        $(".descuentopersonal").css('display','');
    }else{
        $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="descuentoplanilla"]').val('NO');
        $(".descuentopersonal").css('display','none');
    }
}

function generarNumero(valor){
    $.ajax({
        type: "POST",
        url: "venta/generarNumero",
        data: "caja_id="+$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[name="caja_id"]').val()+"&tipodocumento_id="+valor+"&_token="+$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[name="_token"]').val(),
        success: function(a) {
            $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[name="numerodocumento"]').val(a);
        }
    });
    /*if (valor == 4) {
		modal('{{URL::route('venta.busquedaempresa')}}', '');
	}else{
		modal('{{URL::route('venta.busquedacliente')}}', '');
	}   */ 
}


function setValorFormapago (id, valor) {
	$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="' + id + '"]').val(valor);
}

function getValorFormapago (id) {
	var valor = $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="' + id + '"]').val();
	return valor;
}

function cambiotipoventa() {
	var tipoventa = $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="tipoventa"]').val();
	if (tipoventa == 'C') {
		//$('#divConvenio').show();
		//$('#divDescuentokayros').show();
		//$('#divCopago').show();
		modal('{{URL::route('venta.busquedacliente')}}', '');
		$('#divConvenio').css('display', 'block');

	}else if (tipoventa == 'N') {
		$('#divConvenio').css('display', 'none');
		$('#conveniofarmacia_id').val('');
		//$('#divDescuentokayros').hide();
		//$('#divCopago').hide();
	}
}

function generarSaldototal () {
	var total = retornarFloat(getValorFormapago('total'));
	var inicial = retornarFloat(getValorFormapago('inicial'));
	var saldototal = (total - inicial).toFixed(2);
	if (saldototal < 0.00) {
		setValorFormapago('inicial', total);
		setValorFormapago('saldo', '0.00');
	}else{
		setValorFormapago('saldo', saldototal);
	}
}

function retornarFloat (value) {
	var retorno = 0.00;
	value       = value.replace(',','');
	if(value.trim() === ''){
		retorno = 0.00; 
	}else{
		retorno = parseFloat(value)
	}
	return retorno;
}

$(document).on('click', '.quitarFila', function(event) {
	event.preventDefault();
	$(this).parent('span').parent('td').parent('tr').remove();
	calculatetotal();
	$('#efectivo').val($('#totalventa').val());
	calcularTotalPago();
});

function quitar(btn) {
	
	/*var _token =$('input[name=_token]').val();
	$.post('{ URL::route("venta.quitarcarritoventa")}}', {valor: valor,_token: _token} , function(data){
		$('#divDetail').html(data);
		calculatetotal();
		//generarSaldototal ();
		// var totalpedido = $('#totalpedido').val();
		// $('#total').val(totalpedido);
	});*/
}

function calculatetotal () {	
	/*var _token =$('input[name=_token]').val();
	var valor =0;
	$.post('{ URL::route("venta.calculartotal")}}', {valor: valor,_token: _token} , function(data){
		valor = retornarFloat(data);
		$("#total").val(valor);
		//generarSaldototal();
		// var totalpedido = $('#totalpedido').val();
		// $('#total').val(totalpedido);
	});*/
	//Reorganizamos los nombres y números de las filas de la tabla
	var i = 1;
	var total = 0;
	$('#detallesVenta tr .numeration2').each(function() {
		$(this).html(i);
		i++;
	});
	i = 1;
	$('#detallesVenta tr .infoProducto').each(function() {
		$(this).find('.producto_id').attr('name', '').attr('name', 'producto_id' + i);
		$(this).find('.codigobarra').attr('name', '').attr('name', 'codigobarra' + i);
		$(this).find('.tipoventa').attr('name', '').attr('name', 'tipoventa' + i);
		$(this).find('.descuentokayros').attr('name', '').attr('name', 'descuentokayros' + i);
		$(this).find('.copago').attr('name', '').attr('name', 'copago' + i);
		$(this).find('.cantidad').attr('name', '').attr('name', 'cantidad' + i);
		$(this).find('.precio').attr('name', '').attr('name', 'precio' + i);
		$(this).find('.dscto').attr('name', '').attr('name', 'dscto' + i);
		$(this).find('.subtotal').attr('name', '').attr('name', 'subtotal' + i);
		total += parseFloat($(this).find('.subtotal').val());
		i++;
	});
	$('#cantproductos').val(i-1);
	$('#totalventa2').html(parseFloat(total).toFixed(2));
	$('#totalventa').val(parseFloat(total).toFixed(2));
}

function comprobarproducto () {
	var _token =$('input[name=_token]').val();
	var valor =$('input[name=codigo]').val();
	$.post('{{ URL::route("venta.comprobarproducto")}}', {valor: valor,_token: _token} , function(data){
		
		if (data.trim() == 'NO') {
			$('input[name=codigo]').val('');
			bootbox.alert("Este Producto no esta en lista de venta");
            setTimeout(function () {
                $('#codigo').focus();
            },2000) 
		}else{
			$('input[name=codigo]').val('');
			$('#codigo').focus();
		}
	});
}

function seleccionarCliente(id) {
	var _token =$('input[name=_token]').val();
	$.post('{{ URL::route("venta.clienteid")}}', {id: id,_token: _token} , function(data){
		var datos = data.split('-'); 
		$('#person_id').val(datos[0]);
		$('#nombrepersona').val(datos[1]);
		
		cerrarModal();
		var tipoventa = $('#tipoventa').val();
		if (tipoventa == 'N') {
			//$('#nombreproducto').focus();
			$('#nombredoctor').focus();
		}else{
			//$('#conveniofarmacia').focus();
			abrirconvenios();
		}
	});
	
}

function seleccionarParticular(value) {
	$('#nombrepersona').val(value);
	cerrarModal();
	//$('#nombreproducto').focus();
	$('#nombredoctor').focus();
}

function agregarconvenio(id){

	var kayros = $('#txtKayros').val();
	var copago = $('#txtCopago').val();
	var convenio_id = id;

	var _token =$('input[name=_token]').val();
	if(kayros.trim() == '' ){
		bootbox.alert("Ingrese precio kayros");
            setTimeout(function () {
                $('#txtKayros').focus();
            },2000) 
	}else if(copago.trim() == '' ){
		bootbox.alert("Ingrese copago");
            setTimeout(function () {
                $('#txtCopago').focus();
            },2000) 
	}else{
		$.post('{{ URL::route("venta.agregarconvenio")}}', {kayros: kayros,copago: copago, convenio_id: convenio_id,_token: _token} , function(data){
			dat = data.split('|');
			$('#copago').val(copago);
			$('#descuentokayros').val(kayros);
			$('#conveniofarmacia').val(dat[0]);
			$('#nombreconvenio').val(dat[0]);
			$('#conveniofarmacia_id').val(dat[1]);

			cerrarModal();
			$('#descuentokayros').focus();
			/*$('#divDetail').html(data);
			calculatetotal();
			bootbox.alert("Producto Agregado");
            setTimeout(function () {
                $('#txtPrecio' + elemento).focus();
            },2000) */
			
		});
	}
	}

function agregarempresa(id){

	var ruc = $('#ruc').val();
	var direccion = $('#direccion').val();
	var telefono = $('#telefono').val();
	var empresa_id = id;

	var _token =$('input[name=_token]').val();
	/*if(kayros.trim() == '' ){
		bootbox.alert("Ingrese precio kayros");
            setTimeout(function () {
                $('#txtKayros').focus();
            },2000) 
	}else if(copago.trim() == '' ){
		bootbox.alert("Ingrese copago");
            setTimeout(function () {
                $('#txtCopago').focus();
            },2000) 
	}else{*/
	$.post('{{ URL::route("venta.agregarempresa")}}', {ruc: ruc,direccion: direccion,telefono: telefono, empresa_id: empresa_id,_token: _token} , function(data){
		dat = data.split('*');
		$('#nombreempresa').val(dat[0]);
		$('#empresa_id').val(dat[1]);

		cerrarModal();
		$('#nombreproducto').focus();
		/*$('#divDetail').html(data);
		calculatetotal();
		bootbox.alert("Producto Agregado");
        setTimeout(function () {
            $('#txtPrecio' + elemento).focus();
        },2000) */
		
	});
	//}
}

function addpurchasecart(elemento){
	var venta = 1;
	var cantidad = $('#cantidad').val();	
	cantidad = cantidad.replace(",", "");
	var price = $('#precioventa').val();
	price = price.replace(",", "");
	var preciokayros = $('#preciokayros').val();
	preciokayros = preciokayros.replace(",", "");
	var product_id = $('#producto_id').val();
	var stock = $('#stock').val();
	var tipoventa = $(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="tipoventa"]').val();
	var descuentokayros = $(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="descuentokayros"]').val();
	var copago = $(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="copago"]').val();
	var conveniofarmacia_id = $(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="conveniofarmacia_id"]').val();
	var _token =$('input[name=_token]').val();
	if(cantidad.trim() == '' ){
		bootbox.alert("Ingrese Cantidad");
            setTimeout(function () {
                $('#cantidad').focus();
            },2000) 
	}else if(cantidad.trim() == 0){
		bootbox.alert("la cantidad debe ser mayor a 0");
            setTimeout(function () {
                $('#cantidad').focus();
            },2000) 
	}else if(price.trim() == '' ){
		bootbox.alert("Ingrese Precio");
            setTimeout(function () {
                $('#precioventa').focus();
            },2000) 
	}/*else if(price.trim() == 0){
		bootbox.alert("el precio debe ser mayor a 0");
            setTimeout(function () {
                $('#precioventa').focus();
            },2000) 
	}*/else{
		var idsesioncarrito = $("#idsesioncarrito").val();
		var detalle = $('#detalle').val();
		$('#detalle').val(true);
		$.post('{{ URL::route("venta.agregarcarritoventa")}}', {cantidad: cantidad,precio: price, producto_id: product_id, tipoventa: tipoventa, descuentokayros: descuentokayros, copago: copago, preciokayros: preciokayros, conveniofarmacia_id: conveniofarmacia_id, detalle: detalle,idsesioncarrito:idsesioncarrito,_token: _token,stock:stock,venta:venta} , function(data){
			if(data === '0-0') {
				bootbox.alert('No es un formato válido de cantidad.');
				$('#cantidad').val('').focus();
			} else if(data === '0-1') {
				bootbox.alert('No puede vender una cantidad mayor al stock actual');
				$('#cantidad').val('').focus();
			} else {
				var producto_id = $('#producto_id').val();
				if ($("#Product" + producto_id)[0]) {
					$("#Product" + producto_id).html(data);
				} else {
					$('#detallesVenta').append('<tr id="Product' + producto_id + '">' + data + '</tr>');
				}	
				$("#Product" + producto_id).css('display', 'none').fadeIn(1000);				
				calculatetotal();	
				$('#efectivo').val($('#totalventa').val()).focus();	
				calcularTotalPago();			
				/*bootbox.alert("Producto Agregado");
	            setTimeout(function () {
	                $(IDFORMBUSQUEDA + '{ $entidad }} :input[id="nombre"]').focus();
	            },2000) */
				//var totalpedido = $('#totalpedido').val();
				//$('#total').val(totalpedido);
			}				
		});
	}
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
                //$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="historia_id"]').val(dat[0].id);
                //$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="numero_historia"]').val(dat[0].historia);
                //$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="person_id"]').val(dat[0].person_id);
                //$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="nombrepersona"]').val(dat[0].paciente);
                $('#person_id').val(dat[0].person_id);
				$('#nombrepersona').val(dat[0].paciente);
				cerrarModal();
                var tipoventa = $('#tipoventa').val();
				if (tipoventa == 'N') {
					$('#nombreproducto').focus();
				}else{
					//$('#conveniofarmacia').focus();
					abrirconvenios();
				}
			} else {
				mostrarErrores(respuesta, idformulario, entidad);
			}
		}
	});
}

function guardarEmpresa (entidad, idboton) {
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
                $('#empresa_id').val(dat[0].empresa_id);
				$('#nombrepersona').val(dat[0].nombre);
				cerrarModal();
                var tipoventa = $('#tipoventa').val();
				if (tipoventa == 'N') {
					$('#nombreproducto').focus();
				}else{
					//$('#conveniofarmacia').focus();
					abrirconvenios();
				}
			} else {
				mostrarErrores(respuesta, idformulario, entidad);
			}
		}
	});
}

var contador=0;
function guardarVenta (entidad, idboton) {
	var total = $(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="totalventa"]').val();
	var mensaje = '<h3 align = "center">Total = '+total+'</h3>';
	/*if (typeof mensajepersonalizado != 'undefined' && mensajepersonalizado !== '') {
		mensaje = mensajepersonalizado;
	}*/
	if($(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="nombrepersona"]').val()==""){
		bootbox.alert("Debe agregar el nombre del cliente");
		modal('{{URL::route('venta.busquedacliente')}}', '');
		return false;
	}
	/*if($(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="documento"]').val()=="4" && $(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="empresa_id"]').val()==""){
		bootbox.alert("Debe seleccionar una empresa para la factura");
		modal('{URL::route('venta.busquedaempresa')}}', '');
		return false;
	}*/
	if($(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="formapago"]').val()=="T" && $(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="nroref"]').val()==""){
		bootbox.alert("Debe agregar el nro de operacion de la tarjeta");
		return false;
	}
	
	bootbox.confirm({
		message : mensaje,
		buttons: {
			'cancel': {
				label: 'Cancelar',
				className: 'btn btn-default btn-sm'
			},
			'confirm':{
				label: 'Aceptar',
				className: 'btn btn-success btn-sm'
			}
		}, 
		callback: function(result) {
			if (result && contador==0) {
				contador=1;
				var idformulario = IDFORMMANTENIMIENTO + entidad;
				var data         = submitForm(idformulario);
				var respuesta    = '';
				var listar       = 'NO';
				
				var btn = $(idboton);
				btn.button('loading');
				data.done(function(msg) {
					respuesta = msg;
				}).fail(function(xhr, textStatus, errorThrown) {
					respuesta = 'ERROR';
					contador=0;
				}).always(function() {
					contador=0;
					btn.button('reset');
					if(respuesta === 'ERROR'){
					}else{
						var dat = JSON.parse(respuesta);
			            if(dat[0]!==undefined){
			                resp=dat[0].respuesta;    
			            }else{
			                resp='VALIDACION';
			            }
			            
						if (resp === 'OK') {
							cerrarModal();
			                buscarCompaginado('', 'Accion realizada correctamente', entidad, 'OK');
			                /*if(dat[0].pagohospital!="0"){
			                    window.open('/juanpablo/ticket/pdfComprobante?ticket_id='+dat[0].ticket_id,'_blank')
			                }else{
			                    window.open('/juanpablo/ticket/pdfPrefactura?ticket_id='+dat[0].ticket_id,'_blank')
			                }*/
			                //alert('hola');
			                if (dat[0].ind == 1) {
			                	//window.open('/juanpablo/venta/pdfComprobante?venta_id='+dat[0].venta_id+'&guia='+dat[0].guia,'_blank');
			                	//window.open('/juanpablo/venta/pdfComprobante?venta_id='+dat[0].second_id+'&guia='+dat[0].guia,'_blank');
			                }else{
			                	if(dat[0].tipodocumento_id!="15"){
			                		declarar1(dat[0].venta_id,dat[0].tipodocumento_id,dat[0].numero);
			                	}else{
			                		//window.open('venta/pdfComprobante?venta_id='+dat[0].venta_id+'&guia='+dat[0].guia,'_blank');
			                	}
			                }
			                
						} else if(resp === 'ERROR') {
							bootbox.alert(dat[0].msg);
						} else {
							mostrarErrores(respuesta, idformulario, entidad);
						}
					}
				});
			};
		}            
	}).find("div.modal-content").addClass("bootboxConfirmWidth");
	setTimeout(function () {
		if (contadorModal !== 0) {
			$('.modal' + (contadorModal-1)).css('pointer-events','auto');
			$('body').addClass('modal-open');
		}
	},2000);
	
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
        data: "idventa="+idventa+"&empresa={{ ($sucursal_id==1?'2':'1') }}&_token="+$(IDFORMBUSQUEDA + '{!! $entidad !!} :input[name="_token"]').val(),
        success: function(a) {
            console.log(a);
            imprimirVenta(numero);
	    }
    });	
}

function imprimirVenta(numero){
	$.ajax({
        type: "POST",
        url: "http://localhost:81/clifacturacion/controlador/contImprimir.php?funcion=ImprimirVenta",
        data: "numero="+numero+"&_token="+$(IDFORMBUSQUEDA + '{!! $entidad !!} :input[name="_token"]').val(),
        success: function(a) {
            console.log(a);
	    }
    });
}

function validarFormaPago(formapago){
	if(formapago=="T"){
		$(".tarjeta").css("display","");
	}else{
		$(".tarjeta").css("display","none");
	}
}

function guardarito() {
	var i = $('.numeration2').length;
	if(i == 0) {
		bootbox.alert('Debes seleccionar al menos un producto.');
	} else {	
		guardarVenta('Venta', $('#btnGuardar').value);
	}
}

validarFormaPago($("#formapago").val());

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

function coincidenciasMontos() {
	if(parseFloat($('#totalventa').val()) == parseFloat($('#total2').val())) {
		$('#mensajeMontos').html('Los montos coindicen.').css('color', 'green');
		$('#genComp').css('display', '');
		return true;
	} else if(parseFloat($('#totalventa').val()) > parseFloat($('#total2').val())) {
		$('#mensajeMontos').html('Es un monto menor.').css('color', 'orange');	
		$('#genComp').css('display', 'none');		
		return true;
	} else if(parseFloat($('#totalventa').val()) < parseFloat($('#total2').val())) {
		$('#mensajeMontos').html('Es un monto mayor.').css('color', 'red');	
		$('#genComp').css('display', 'none');		
		return false;
	}
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

function cargarEfectivo() {
	$('#efectivo').val($('#totalventa').val()).focus();
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

function camposNoVacios() {	
	if(!$('#efectivo').attr('readonly')) {
		if($('#efectivo').val().length == 0) {
			$('#efectivo').focus();
			bootbox.alert('Ingresa un monto para efectivo.');
			return false;
		}
	} 
	if(!$('#visa').attr('readonly')) {
		if($('#visa').val().length == 0) {
			$('#visa').focus();
			bootbox.alert('Ingresa un monto para visa.');
			return false;
		}
	} 
	if(!$('#master').attr('readonly')) {
		if($('#master').val().length == 0) {
			$('#master').focus();
			bootbox.alert('Ingresa un monto para master.');
			return false;
		}
	}
	return true;
}	

function enviar() {
	if ($('#documento').val() == '4') {
		if($('#ruc2').val().length != 11 || $('#nombreempresa2').val() == '' || $('#direccion2').val() == '') {
			alert('No olvide digitar un RUC válido.');
			return false;
		}
	}
	form = $('#formMantenimientoVenta');
	if(!camposNoVacios()) {
		return false;
	} else {
		if(!coincidenciasMontos()) {
			$('#efectivo').focus();
			bootbox.alert('Los montos no coinciden.');
			return false;
		} else {
			guardarito();
		}
	}
}

$(document).on('click', '.escogerFila', function(){
	$('.escogerFila').css('background-color', 'white');
	$(this).css('background-color', 'yellow');
});

$(document).on('keyup', '#ruc2', function(e) {
	e.preventDefault();
	e.stopImmediatePropagation();
	if ($(this).val().length == 11) {
		buscarEmpresa();
	}
});

function buscarEmpresa() {
	ruc = $("#ruc2").val();     
    $.ajax({
        type: 'GET',
        url: "ticket/buscarEmpresa",
        data: "ruc="+ruc,
        beforeSend(){
            $("#ruc2").val('Comprobando...');
        },
        success: function (a) {
            if(a == '')  {
        		buscarEmpresa2(ruc);
        	} else {
        		var e = a.split(';;');
        		$("#ruc2").val(ruc);
        		$('#nombreempresa2').val(e[0]);
        		$('#direccion2').val(e[1]);
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
            $("#ruc2").val('Comprobando...');
        },
        success: function (data, textStatus, jqXHR) {
            if(data.RazonSocial == null) {
                alert('El RUC ingresado no existe... Digite uno válido.');
        		$("#ruc2").val('').focus();
                $("#nombreempresa2").val('');
                $("#direccion2").val('');
            } else {
                $("#ruc2").val(ruc);
                $("#nombreempresa2").val(data.RazonSocial);
                $("#direccion2").val('-');
            }
        }
    });
}

</script>