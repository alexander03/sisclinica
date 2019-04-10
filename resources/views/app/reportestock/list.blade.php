@if(count($lista) == 0)
<h3 class="text-warning">No se encontraron resultados.</h3>
@else
{!! $paginacion or '' !!}
<div class="table-responsive">
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
            <td>{{ $value->almacen }}</td>
            <td>{{ $value->producto }}</td>
            <td>{{ $value->laboratorio }}</td>
            <td>{{ $value->presentacion }}</td>
            <td>{{ $value->anaquel }}</td>
            <td>{{ ($value->fechavencimiento!=''?date("d/m/Y",strtotime($value->fechavencimiento)):'') }}</td>
            <td>{{ $value->lote }}</td>
            <td align='right'>{{ round($value->stock,0) }}</td>
		</tr>
		<?php
		$contador = $contador + 1;
		?>
		@endforeach
	</tbody>
</table>
</div>
@endif