<?php
use App\Kardex;
?>
@if(count($lista) == 0)
<h3 class="text-warning">No se encontraron resultados.</h3>
@else
{!! $paginacion or '' !!}
<table id="example1" class="table table-bordered table-striped table-condensed table-hover">

	<thead>
		<tr>
			@foreach($cabecera as $key => $value)
				<th class="text-center" @if((int)$value['numero'] > 1) colspan="{{ $value['numero'] }}" @endif>{!! $value['valor'] !!}</th>
			@endforeach
		</tr>
	</thead>
	<tbody>
		<?php
		$contador = $inicio + 1;
		?>
		@foreach ($lista as $key => $value)
		<tr>
			<?php   
				$currentstock = Kardex::join('detallemovimiento', 'kardex.detallemovimiento_id', '=', 'detallemovimiento.id')->join('movimiento', 'detallemovimiento.movimiento_id', '=', 'movimiento.id')->where('producto_id', '=', $value->id)->where('movimiento.almacen_id', '=',1)->orderBy('kardex.id', 'DESC')->first();
				$stock = 0;
				if ($currentstock !== null) {
					$stock=$currentstock->stockactual;
				}
				$tipo = '';
				if ($value->tipo == 'P') {
					$tipo = 'Producto';
				}elseif ($value->tipo == 'I') {
					$tipo = 'Insumo';
				}if ($value->tipo == 'O') {
					$tipo = 'Otros';
				}

				$nombrepresentacion = '';
				if ($value->presentacion != null) {
					$nombrepresentacion=$value->presentacion->nombre;
				}

				$nombreforma = '';
				if ($value->forma != null) {
					$nombreforma=$value->forma->nombre;
				}
			?>
			<td>{!! Form::hidden('product_id'.$contador, $value->id, array('id' => 'product_id'.$contador)) !!}{{ $contador }}</td>
			<td>{{ $value->codigosunasa }}</td>
			<td>{{ $value->nombre }}</td>
			<td>{{ $value->concentracion }}</td>
			<td>{{ $nombreforma }}</td>
			<td>{{ $nombrepresentacion }}</td>
			<td>{{ $value->rsanitario }}</td>
			<td>{{ $stock }}</td>
			<td>{!! Form::text('txtQuantity'.$contador, null, array('class' => '', 'size' => '7', 'id' => 'txtQuantity'.$contador, 'placeholder' => '', 'data-inputmask' => '\'alias\': \'decimal\', \'groupSeparator\': \',\', \'autoGroup\': false')) !!}</td>
			@if($tipo2 == 'I')
			<td>{!! Form::text('txtLote'.$contador, null, array('class' => '', 'size' => '7', 'id' => 'txtLote'.$contador, 'placeholder' => '')) !!}
			</td>
			<td>{!! Form::text('txtFecha'.$contador, null, array('class' => '', 'size' => '7', 'id' => 'txtFecha'.$contador, 'placeholder' => '')) !!}
			</td>
			@endif
			<td>{!! Form::hidden('txtNombre'.$contador, $value->nombre, array('id' => 'txtNombre'.$contador)) !!}
							{!! Form::button('<i class="fa fa-plus"></i> Agregar', array('onclick' => 'addpurchasecart(\''.$contador.'\')', 'class' => 'btn btn-xs btn-danger')) !!}</td>

			<?php
			$cadena = '<script>$(document).ready(function() {';
			$cadena .= '$(\'#txtQuantity'.$contador.'\').inputmask("decimal", { radixPoint: ".", autoGroup: true, groupSeparator: ",", groupSize: 3, digits: 2 });';
			$cadena .= '$(\'#txtFecha'.$contador.'\').inputmask("dd/mm/yyyy");';
            $cadena .= '$(\'#txtFecha'.$contador.'\').datetimepicker({ pickTime: false, language: "es"});';
			$cadena .= '});</script>';
			echo $cadena;
			?>
			
		</tr>
		<?php
		$contador = $contador + 1;
		?>
		@endforeach
	</tbody>
	<tfoot>
		<tr>
			@foreach($cabecera as $key => $value)
				<th @if((int)$value['numero'] > 1) colspan="{{ $value['numero'] }}" @endif>{!! $value['valor'] !!}</th>
			@endforeach
		</tr>
	</tfoot>
</table>
{!! $paginacion or '' !!}
@endif