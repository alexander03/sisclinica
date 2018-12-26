<div class="row">
	{!! Form::model($movimiento, $formData) !!}
	{!! Form::hidden('cantdetalles', count($detalles)) !!}
	<div class="col-lg-6 col-md-6 col-sm-6">
		<table class="table table-bordered table-responsive table-condensed table-hover dataTable no-footer" border="1" role="grid" style="width: 100%;">
			<thead>
				<tr>
					<td colspan="4"></td>
					<td>
						<select class="form-control input-xs" name="tipodescuento" id="tipodescuento" onchange="inicializarPrecios()">
							<option value="0">%</option>
							<option value="1">S/.</option>
						</select>
					</td>
					<td></td>
				</tr>
				<tr>
					<th>Cant.</th>
					<th>Medico</th>
					<th>Descrip.</th>
					<th>Precio</th>
					<th>Desc.</th>
					<th>Subtot.</th>
				</tr>					
			</thead>
			<tbody>
				<?php $i = 0; ?>				
				@foreach($detalles as $detalle)
					<tr>
						<td>{{ (integer) $detalle->cantidad }}</td>
						<td style="font-size: 10px">
							{{ $detalle->persona->nombres }} {{ $detalle->persona->apellidopaterno }}
						</td>
						<td style="font-size: 10px">{{ $detalle->nombre }}</td>
						<td>
							<input name="precio{{ $i }}" id="precio{{ $i }}" class="form-control input-xs precio" type="text" value="{{ $detalle->precio }}" onkeyup="inicializarPrecios();">
						</td>
						<td>
							<input name="descuento{{ $i }}" id="descuento{{ $i }}" class="form-control input-xs" type="text" value="0" onkeyup="inicializarPrecios();">
						</td>
						<td>
							<input name="subtotal{{ $i }}" id="subtotal{{ $i }}" class="form-control input-xs subtotal" type="text" readonly="">
						</td>
					</tr>
					<?php $i++; ?>
				@endforeach
				<tr>
					<th colspan="5" class="text-right">Pago</th>
					<td><input name="total" id="total" class="form-control input-xs" type="text" readonly=""></td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="col-lg-6 col-md-6 col-sm-6">			
		<!-- DATOS DEL TICKET -->
		<div id="divMensajeError{!! $entidad !!}"></div>
		<div class="form-group">
		    {!! Form::label('numero', 'Nro. Doc.', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label input-sm')) !!}
		    {!! Form::label('numero', $movimiento->serie.'-'.$movimiento->numero, array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label input-sm', 'style' => 'font-weight:normal;text-align:left;text-align:left')) !!}
		    {!! Form::label('siniestro', 'Siniestro', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label input-sm')) !!}
			{!! Form::label('siniestro', $movimiento->comentario . ' | ' .$movimiento->fecha, array('class' => 'col-lg-6 col-md-6 col-sm-6 control-label input-sm', 'style' => 'font-weight:normal;text-align:left')) !!}
		</div>
		<div class="form-group">
			{!! Form::label('paciente', 'Paciente', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label input-sm')) !!}
			{!! Form::label('siniestro', $movimiento->persona->nombres . ' ' . $movimiento->persona->apellidopaterno . ' ' . $movimiento->persona->apellidomaterno, array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label input-sm', 'style' => 'font-weight:normal;text-align:left')) !!}
			{!! Form::label('doctor', 'Referido', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label input-sm')) !!}
			@if(!is_null($movimiento->doctor))
				{!! Form::label('siniestro', ($movimiento->doctor->nombres . ' ' . $movimiento->doctor->apellidopaterno . ' ' . $movimiento->doctor->apellidomaterno), array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label input-sm', 'style' => 'font-weight:normal;text-align:left')) !!}
			@endif
		</div>
		<div class="form-group">
			{!! Form::label('plan', 'Plan', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label input-sm')) !!}
			{!! Form::label('siniestro', $movimiento->plan->nombre, array('class' => 'col-lg-8 col-md-8 col-sm-8 control-label input-sm', 'style' => 'font-weight:normal;text-align:left')) !!}
		</div>
		<hr>
		<!-- OPCIONES -->
		<div class="form-group">
	        <!--{!! Form::label('plan', 'Generar:', array('class' => 'col-lg-2 col-md-2 col-sm-2 control-label input-sm')) !!}
			<div class="col-lg-2 col-md-2 col-sm-2">
				{!! Form::hidden('comprobante', 'S', array('id' => 'comprobante')) !!}
	            <input readonly="readonly" disabled="disabled" checked="checked" type="checkbox" onchange="mostrarDatoCaja(0,this.checked)" id="boleta" class="col-lg-2 col-md-2 col-sm-2 control-label input-sm" />
	            {!! Form::label('boleta', 'Comprobante', array('class' => 'col-lg-10 col-md-10 col-sm-10 control-label input-sm')) !!}
	            {!! Form::hidden('pagar', 'S', array('id' => 'pagar')) !!}    
				<input readonly="readonly" disabled="disabled" checked="checked" type="checkbox" onchange="mostrarDatoCaja(this.checked,0)" id="pago" class="col-lg-2 col-md-2 col-sm-2 control-label input-sm datocaja" />
	            {!! Form::label('pago', 'Pago', array('class' => 'col-lg-10 col-md-10 col-sm-10 control-label input-sm datocaja')) !!}
			</div>-->
			{!! Form::label('caja_id', 'Caja:', array('class' => 'col-lg-1 col-md-1 col-sm-1 control-label input-sm datocaja caja')) !!}
			<div class="col-lg-3 col-md-3 col-sm-3">
				<select name="caja_id" id="caja_id" class="form-control input-xs datocaja caja">
					@foreach($cboCaja as $caja)
					<option value="{{ $caja->id }}">{{ $caja->nombre }}</option>
					@endforeach
				</select>
			</div>	
			{!! Form::label('tipodocumento', 'Tipo de documento:', array('class' => 'col-lg-3 col-md-3 col-sm-3 control-label input-sm caja')) !!}
			<div class="col-lg-2 col-md-2 col-sm-2">
				<select name="tipodocumento" id="tipodocumento" class="form-control input-xs form-control caja">
					<option value="Boleta">Boleta</option>
					<option value="Factura">Factura</option>
					<option value="Ticket">Ticket</option>
				</select>
			</div>
			{!! Form::label('numcomprobante', 'N°:', array('class' => 'col-lg-1 col-md-1 col-sm-1 control-label input-sm')) !!}
			<div class="col-lg-2 col-md-2 col-sm-2">
				<input type="text" name="numcomprobante" id="numcomprobante" class="form-control input-xs form-control">
			</div>
			<hr>
	        {!! Form::label('formapago', 'Forma Pago:', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label datocaja caja input-sm')) !!}
			<div class="col-lg-8 col-md-8 col-sm-8">
				<label id="divcbx0" class="checkbox-inline" onclick="divFormaPago('0', '1')">
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
		    <div class="col-lg-6 col-md-6 col-sm-6">	    	
			    <div class="input-group form-control">
					<span class="input-group-addon input-xs">EFECTIVO</span>
					<input onkeyup="calcularTotalPago();" id="efectivo" type="text" class="form-control input-xs" readonly="">
				</div>
				<div class="input-group form-control">
					<span class="input-group-addon input-xs">VISA</span>
					<input onkeyup="calcularTotalPago();" id="visa" type="text" class="form-control input-xs" readonly="">
				</div>
				<div class="input-group form-control">
					<span class="input-group-addon input-xs">MASTER</span>
					<input onkeyup="calcularTotalPago();" id="master" type="text" class="form-control input-xs" readonly="">
				</div>	
			</div>	
			<div class="col-lg-6 col-md-6 col-sm-6">	    	
			    <div class="input-group form-control">
					<span class="input-group-addon input-xs">TOTAL</span>
					<input id="total2" type="text" class="form-control input-xs" readonly="" value="0.000">
				</div>
			</div>
			<div class="col-lg-6 col-md-6 col-sm-6">	    	
			    <b id="mensajeMontos" style="color:red">Los montos no coinciden.</b>
			</div>		
		</div>
		{!! Form::hidden('id', $movimiento->id) !!}
		<div class="text-right">
			{!! Form::button('<i class="fa fa-check fa-lg"></i> '.$boton, array('class' => 'btn btn-success btn-sm', 'data-a' => 'true', 'id' => 'btnGuardar', 'onclick' => 'enviar();')) !!}
			{!! Form::button('<i class="fa fa-exclamation fa-lg"></i> Cancelar', array('class' => 'btn btn-warning btn-sm', 'id' => 'btnCancelar'.$entidad, 'onclick' => 'cerrarModal();')) !!}
		</div>		
	</div>	
	{!! Form::close() !!}
</div>
<script type="text/javascript">
	$(document).ready(function() {
		configurarAnchoModal('1200');
		init(IDFORMMANTENIMIENTO+'{!! $entidad !!}', 'B', '{!! $entidad !!}');
		inicializarPrecios();
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

	function inicializarPrecios() {
		var total = 0;
		var cont = 0;
		var subtotal = 0;
		var descuento = 0;
		$(".subtotal").each(function(){
			if($('#precio' + cont).val() == '') {
				subtotal = 0;
				subtotal = subtotal.toFixed(3);
			} else {
				descuento = $('#descuento' + cont).val();
				if($('#tipodescuento').val() == 0) {
					descuento = $('#descuento' + cont).val() * $('#precio' + cont).val() / 100;
				}
				subtotal = parseFloat($('#precio' + cont).val() - descuento).toFixed(3);
			}			
			$(this).val(subtotal);	
			cont++;
		});
		$(".subtotal").each(function(){
			total += parseFloat($(this).val());
		});
		$('#total').val(total.toFixed(3));
		calcularTotalPago();
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
			} else {
				$('#master').attr('readonly', true).val('');
			}
		} else {
			m = '0';
			$('#cbx' + num).attr('checked', true);
			$('#divcbx' + num).css('color', 'red');
			if(num == '0') {
				$('#efectivo').attr('readonly', false).focus();
			} else if(num == '1') {
				$('#visa').attr('readonly', false).focus();
			} else {
				$('#master').attr('readonly', false).focus();
			}
		}
		$('#divcbx' + num).attr("onclick", "divFormaPago('" + num + "', '" + m + "');");
		calcularTotalPago();
	}

	function solodecimal(numero) {
	    var RE = /^\d*\.?\d*$/;
	    if (RE.test(numero)) {
	        return true;
	    } else {
	        return false;
	    }
	}

	function calcularTotalPago() {
		var efectivo = $('#efectivo').val();
		var visa = $('#visa').val();
		var master = $('#master').val();
		var total = 0.000;
		if(!solodecimal(efectivo) || efectivo == '') {
			efectivo = 0.000;
		} 
		if(!solodecimal(visa) || visa == '') {
			visa = 0.000;
		}
		if(!solodecimal(master) || master == '') {
			master = 0.000;
		}
		total = parseFloat(efectivo) + parseFloat(visa) + parseFloat(master);
		$('#total2').val(total.toFixed(3));

		coincidenciasMontos();		
	}

	function coincidenciasMontos() {
		if($('#total').val() == $('#total2').val()) {
			$('#mensajeMontos').html('Los montos coindicen.').css('color', 'green');
			return true;
		} else {
			$('#mensajeMontos').html('Los montos no coindicen.').css('color', 'red');
			return false;
		}
	}

	function enviar() {
		form = $('#formMantenimientoMovimiento');
		if(!coincidenciasMontos()) {
			return false;
		}
		$.ajax({
			url: form.attr('action'),
			type: form.attr('method'),
			beforeSend: function() {
				$('#btnGuardar').html('Cargando...').attr('disabled', true);
			},
			success: function() {
				//ok
			},
		});
	}
</script>
