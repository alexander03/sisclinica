<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6">
		<h4 style="color:blue;font-weight: bold;">Producto: {{ $producto->nombre }}</h4>
		<table class="table-condensed table-striped table-hover" border="1" style="width: 100%;">
			<thead>
				<tr>
					<th width="70%">Nombre</th>
					<th width="20%">F. de Venc.</th>
					<th width="20%">Stock</th>
				</tr>					
			</thead>
			<tbody>
				@if(count($lotes) > 0)
					@foreach($lotes as $lote)
						<tr class="escogerFila2" style="cursor: pointer;" data-fechavencimiento="{{ $lote->fechavencimiento }}" data-nombre="{{ $lote->nombre }}" data-fraccion="{{ $lote->fraccion }}" data-stock="{{ $lote->queda }}" data-id="{{ $lote->productoid }}" data-idlote = "{{ $lote->loteid }}">
							<td>
								{{ $lote->nombre }}
							</td>
							<td>
								{{ $lote->fechavencimiento }}
							</td>	
							<td>						
							@if($lote->fraccion != 1)
								<?php 
									$num1 = (int) ($lote->queda/$lote->fraccion);
									$num2 = $lote->queda - $num1*$lote->fraccion;
								?>
								{{$num1}}F{{$num2}}</td>
							@else {{$lote->queda}}</td>
							@endif							
						</tr>
					@endforeach
				@else 
					<tr><th class="text-center" colspan="3" style="color: red;">No hay Lotes para este producto</th></tr>
				@endif
			</tbody>
		</table>
	</div>
	<div class="col-lg-6 col-md-6 col-sm-6">
		<br>		
		<div class="form-inline">
			<div class="form-group">
			    {!! Form::label('cantidadlote', 'Cantidad', array('class' => 'control-label input-sm')) !!}
			    {!! Form::text('cantidadlote', null, array('class' => 'form-control input-xs', 'id' => 'cantidadlote', 'readonly' => 'readonly', 'onkeyup' => "javascript:this.value=this.value.toUpperCase();")) !!}			   
			</div>	
			<div class="form-group">
				{!! Form::button('<i class="fa fa-check fa-lg"></i> Aceptar', array('class' => 'btn btn-success btn-xs', 'onclick' => 'anadirFilaLote();')) !!}
			    {!! Form::button('<i class="fa fa-exclamation fa-lg"></i> Cerrar', array('class' => 'btn btn-warning btn-xs', 'onclick' => 'cerrarModal();')) !!}
			    <input type="hidden" id="stock_lote">
			    <input type="hidden" id="id_producto_lote">
			    <input type="hidden" id="id_lote_lote">
			    <input type="hidden" id="fraccion_lote">
			    <input type="hidden" id="fechavencimiento_lote">
			    <input type="hidden" id="nombre_lote">
			</div>
			<br><br>
			<font id="mensajeLote">&nbsp;</font>
		</div>	
		<hr>
		<h4 style="color:blue;font-weight: bold;">Lotes seleccionados</h4>
		<table class="table-condensed table-striped table-hover" border="1" style="width: 100%;">
			<thead>
				<tr>
					<th width="55%">Nombre</th>
					<th width="20%">F. de Venc.</th>
					<th width="15%">Stock</th>
					<th width="10%">Quitar</th>
				</tr>					
			</thead>
			<tbody id="lotesseleccionados">
				
			</tbody>
			<tr>
				<td colspan="2"></td>
				<td id="totalcantidad_lotes">0</td>
				<input type="hidden" value="0" id="totalcantidad_lotes2">
			</tr>
			<tr id="menscerofilas"></tr>
		</table>	
		<br>
	</div>	
</div>
<script>
	$(document).ready(function() {
		configurarAnchoModal('1300');
		cerofilas();
	});
	$(document).on('click', '.escogerFila2', function(e){
		e.preventDefault();
		$('.escogerFila2').css('background-color', 'white');
		$(this).css('background-color', 'yellow');
		$('#cantidadlote').attr('readonly', false).focus();
		$('#stock_lote').val($(this).data('stock'));
		$('#id_producto_lote').val($(this).data('id'));
		$('#id_lote_lote').val($(this).data('idlote'));
		$('#fraccion_lote').val($(this).data('fraccion'));
		$('#fechavencimiento_lote').val($(this).data('fechavencimiento'));
		$('#nombre_lote').val($(this).data('nombre'));
		$('#mensajeLote').html('&nbsp;')
	});
	$(document).on('keyup', '#cantidadlote', function(e){
		e.preventDefault();
		e.stopImmediatePropagation();
		var key = e.charCode ? e.charCode : e.keyCode ? e.keyCode : 0;
		if(key == 13) {
			if($(this).val() == '' || $(this).val() == 0) {				
				$(this).val('').focus();
			} else {
				addcarritolote();
			}				
		}
	});
	function addcarritolote() {
		var cantidad = $('#cantidadlote').val(); //Pido
		var stocklote = $('#stock_lote').val(); //Hay
		var lote_id = $('#id_lote_lote').val();
		var producto_id = $('#id_producto_lote').val();
		var fraccion = $('#fraccion_lote').val();
		var fechavencimiento = $('#fechavencimiento_lote').val();
		var nombre = $('#nombre_lote').val();
		$.ajax({
			url: "movimientoalmacen/addcarritolote/" + cantidad + "/" + stocklote + "/" + producto_id + "/" + fraccion,
			type: 'GET',
			success: function(e) {
				if(e === '0-0') {
					$('#mensajeLote').css('color', 'red').html('El formato de cantidad no coincide para este producto.');
				} else if(e === '0-1') {
					$('#mensajeLote').css('color', 'red').html('Ingresa una cantidad correcta');
				} else if(e === '0-2') {
					$('#mensajeLote').css('color', 'red').html('No puedes sacar más productos de los que tienes.');
				} else {
					$('#mensajeLote').css('color', 'green').html('Producto añadido a la tabla.');					
					$('#stock_lote').val('');
					$('#id_producto_lote').val('');
					$('#cantidadlote').attr('readonly', true);
					//armo fila
					var fila = '<td>' + nombre + '</td><td>' + fechavencimiento + '</td><td>' + e + '</td><td><a href="#" class="btn btn-danger btn-xs quitarFila"><i class="glyphicon glyphicon-minus"></i></a></td><input type="hidden" class="cantidad_lote_" id="cantidad_lote_' + lote_id + '" value="' + e + '"><input type="hidden" class="id_lote_" id="id_lote_' + lote_id + '" value="' + lote_id + '">';
					if($('#filaseleccionada'+lote_id)[0]) {
						$('#filaseleccionada'+lote_id).html(fila);
					} else {
						$('#lotesseleccionados').append('<tr  id="filaseleccionada' + lote_id + '">' + fila + '</tr>');
					}
					$("#filaseleccionada" + lote_id).css('display', 'none').fadeIn(1000);
				}
				$('#cantidadlote').val('');
				cerofilas();
				sumaTotal();
			}
		});
	}
	function cerofilas() {
		if($('#lotesseleccionados tr td').length == 0) {
			$('#menscerofilas').html('<th colspan="4" style="color: red;" class="text-center">No seleccionaste ningún lote</th>');
		} else {
			$('#menscerofilas').html('');
		}
	}
	$(document).on('click', '.quitarFila', function(event) {
		event.preventDefault();
		$(this).parent('td').parent('tr').remove();
		$('#mensajeLote').html('&nbsp;');
		cerofilas();
		sumaTotal();
	});
	function sumaTotal() {
		var sumaTotal = 0;
		var cantidadUnidades = 0;
		var total = '';
		var fraccion = $('#fraccion_lote').val();
		$('.cantidad_lote_').each(function() {
			if(parseInt(fraccion) !== 1) {
				var cantidadx = $(this).val();
				cantidadx = cantidadx.split('F');
				cantidadUnidades = parseFloat(cantidadx[0])*fraccion + parseFloat(cantidadx[1]);
				sumaTotal += cantidadUnidades;
			} else {
				sumaTotal += parseFloat($(this).val());
			}			
		});
		if(parseInt(fraccion) !== 1) {
    		var pres1 = 1;
    		pres1 = parseInt(parseFloat(sumaTotal)/parseFloat(fraccion));
    		pres2 = parseFloat(sumaTotal) - pres1*parseFloat(fraccion);
    		total = pres1.toString() + 'F' + pres2.toString();
		} else {
			total = sumaTotal;
		}
		$('#totalcantidad_lotes2').val(total);
		$('#totalcantidad_lotes').html(total);
	}

	function anadirFilaLote() {
		var cantidad = obtenerDatosLotes();
		if(cantidad === '') {
			$('#mensajeLote').css('color', 'red').html('Debes seleccionar al menos un lote.');
		} else {
			//Añadimos fila y cerramos modal
			addpurchasecart(cantidad);
			cerrarModal();
		}		
	}

	function obtenerDatosLotes() {
		var retorno = '';
		$('.cantidad_lote_').each(function() {
			retorno += $(this).val() + ';';			
		});
		$('.id_lote_').each(function() {
			retorno += $(this).val() + ';';			
		});
		return retorno;
	}
</script>