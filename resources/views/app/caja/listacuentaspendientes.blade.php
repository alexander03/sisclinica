@if(count($lista) == 0)
<h3 class="text-warning">No se encontraron resultados.</h3>
@else
<table id="example1" class="table table-bordered table-striped table-condensed table-hover">
	<thead>
		<tr>
			@foreach($cabecera as $key => $value)
				<th @if((int)$value['numero'] > 1) colspan="{{ $value['numero'] }}" @endif>{!! $value['valor'] !!}</th>
			@endforeach
		</tr>
	</thead>
	<tbody>
		<?php
		?>
		@foreach ($lista as $key => $value)
		<tr>
			<td>{{ date('d/m/Y',strtotime($value->fecha)) }}</td>
            <td>{{ $value->numero }}</td>
            <td>{{ $value->paciente }}</td>
            <td align="center">{{ number_format($value->total,2,'.','') }}</td>
            <td style="color:blue;font-weight: bold;">
            	<font color="red">{{ number_format($value->movimiento->movimiento->total - $value->total,2,'.','') }}</font>
            </td>
            <td style="color:blue;font-weight: bold;"></td>
		</tr>
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
<script type="text/javascript">
$(document).ready(function() {
	configurarAnchoModal('900');
}); 
</script>
@endif