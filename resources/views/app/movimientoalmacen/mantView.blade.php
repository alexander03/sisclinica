<?php
$url = URL::route("movimientoalmacen.buscarproducto");
?>		
<div id="divMensajeError{!! $entidad !!}"></div>
{!! Form::model($movimientoalmacen, $formData) !!}	
	{!! Form::hidden('listar', $listar, array('id' => 'listar')) !!}
	<div class="col-lg-6 col-md-6 col-sm-6">
		<div class="form-group">
			{!! Form::label('documento', 'Documento:', array('class' => 'col-lg-5 col-md-5 col-sm-5 control-label')) !!}
			<div class="col-lg-7 col-md-7 col-sm-7">
				{!! Form::text('documento', $movimientoalmacen->tipodocumento->nombre, array('class' => 'form-control input-xs', 'id' => 'documento', 'readonly' => 'readonly')) !!}
			</div>
		</div>
		<div class="form-group">
			{!! Form::label('numerodocumento', 'Nro Doc:', array('class' => 'col-lg-5 col-md-5 col-sm-5 control-label')) !!}
			<div class="col-lg-7 col-md-7 col-sm-7">
				{!! Form::text('numerodocumento', $movimientoalmacen->numero, array('class' => 'form-control input-xs', 'id' => 'numerodocumento', 'placeholder' => 'Ingrese numerodocumento', 'readonly' => 'readonly')) !!}
			</div>
		</div>
		<div class="form-group">
			{!! Form::label('comentario', 'Comentario:', array('class' => 'col-lg-5 col-md-5 col-sm-5 control-label')) !!}
			<div class="col-lg-7 col-md-7 col-sm-7">
				{!! Form::textarea('comentario', $movimientoalmacen->comentario == '' ? '-' : $movimientoalmacen->comentario, array('style' => 'resize: none;', 'rows' => '3','class' => 'form-control input-xs', 'id' => 'comentario', 'placeholder' => 'Ingrese comentario', 'readonly' => 'readonly')) !!}
			</div>
		</div>


	</div>
	<div class="col-lg-6 col-md-6 col-sm-6">
		{{--<div class="form-group">
			{!! Form::label('tipo', 'Tipo:', array('class' => 'col-lg-5 col-md-5 col-sm-5 control-label')) !!}
			<div class="col-lg-7 col-md-7 col-sm-7">
				{!! Form::select('tipo', $cboTipo, null, array('class' => 'form-control input-xs', 'id' => 'tipo', 'onchange' => 'cambiar();')) !!}
			</div>
		</div>--}}		
		<div class="form-group">
			{!! Form::label('fecha', 'Fecha:', array('class' => 'col-lg-5 col-md-5 col-sm-5 control-label')) !!}
			<div class="col-lg-7 col-md-7 col-sm-7">
				{!! Form::text('fecha', date('d/m/Y'), array('class' => 'form-control input-xs', 'id' => 'fecha', 'placeholder' => 'Ingrese fecha', 'readonly' => 'readonly')) !!}
			</div>
		</div>
		<div class="form-group">
			{!! Form::label('total', 'Total:', array('class' => 'col-lg-5 col-md-5 col-sm-5 control-label')) !!}
			<div class="col-lg-7 col-md-7 col-sm-7">
				{!! Form::text('total', $movimientoalmacen->totalpagado, array('class' => 'form-control input-xs', 'id' => 'total', 'onchange' => 'cambiar();', 'readonly' => 'readonly')) !!}
			</div>
		</div>
		
	</div>
	<div class="row">
		<div class="col-lg-12 col-md-12 col-sm-12">
			<div id="divDetail" class="table-responsive" style="overflow:auto; height:180px; padding-right:10px; border:1px outset">
		        <table style="width: 100%;" class="table-condensed table-striped">
		            <thead>
		                <tr>
		                    <th bgcolor="#E0ECF8" class='text-center'>Producto</th>
		                    <th bgcolor="#E0ECF8" class='text-center'>Cantidad (Unidades)</th>
		                    <th bgcolor="#E0ECF8" class="text-center">Precio</th>
		                    <th bgcolor="#E0ECF8" class="text-center">Subtotal</th>                          
		                </tr>
		            </thead>
		            <tbody>
		            @foreach($detalles as $key => $value)
					<tr>
						<td class="text-center">{!! $value->producto->nombre !!}</td>
						<td class="text-center">{!! $value->cantidad !!}</td>
						<td class="text-center">{!! $value->precio !!}</td>
						<td class="text-center">{!! $value->subtotal !!}</td>
					</tr>
					@endforeach
		            </tbody>
		           
		        </table>
		    </div>
		</div>
	 </div>
    <br>
	
	<div class="form-group">
		<div class="col-lg-12 col-md-12 col-sm-12 text-right">
			
			{!! Form::button('<i class="fa fa-exclamation fa-lg"></i> Cancelar', array('class' => 'btn btn-warning btn-sm', 'id' => 'btnCancelar'.$entidad, 'onclick' => 'cerrarModal();')) !!}
		</div>
	</div>
{!! Form::close() !!}
<script type="text/javascript">
$(document).ready(function() {
	configurarAnchoModal('880');
	init(IDFORMMANTENIMIENTO+'{!! $entidad !!}', 'M', '{!! $entidad !!}');

		$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="total"]').inputmask('decimal', { radixPoint: ".", autoGroup: true, groupSeparator: ",", groupSize: 3, digits: 2 });

		
	$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="fecha"]').inputmask("dd/mm/yyyy");
		$('#divfecha').datetimepicker({
			pickTime: false,
			language: 'es'
		});

	

}); 

function agregar() {
	var url = '{!! $url !!}'+'?tipo='+$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="tipo"]').val();
	modal(url,'');
}



function setValorFormapago (id, valor) {
	$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="' + id + '"]').val(valor);
}

function getValorFormapago (id) {
	var valor = $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="' + id + '"]').val();
	return valor;
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

function quitar (valor) {
	var _token =$('input[name=_token]').val();
	$.post('{{ URL::route("movimientoalmacen.quitarcarritomovimientoalmacen")}}', {valor: valor,_token: _token} , function(data){
		$('#divDetail').html(data);
		//calculatetotal();
		//generarSaldototal ();
		// var totalpedido = $('#totalpedido').val();
		// $('#total').val(totalpedido);
	});
}




</script>