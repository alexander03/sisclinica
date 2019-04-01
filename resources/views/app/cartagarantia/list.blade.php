@if(count($lista) == 0)
<h3 class="text-warning">No se encontraron resultados.</h3>
@else

{!! $paginacion or '' !!}
<div class="table-responsive">

	<table id="example1" class="table table-bordered table-striped table-condensed table-hover">

		<thead>
			<tr>
				@foreach($cabecera as $key => $value)
					<th class="text-center" @if((int)$value['numero'] > 1) colspan="{{ $value['numero'] }}" @endif>{!! $value['valor'] !!} @if($value['valor']=='Marcar') <input type='checkbox' onclick='marcarTodos(this.checked);' title='Marcar Todos' /> @endif</th>
				@endforeach
			</tr>
		</thead>
		<tbody>
			<?php
			$contador = $inicio + 1;
			$dat="";
			?>
			@foreach ($lista as $key => $value)
			<tr id="td{{ $value->id }}">
				<td>{{ $contador }}</td>
	            <td>{{ date('d/m/Y',strtotime($value->fecha)) }}</td>
	            <td>{{ $value->cotizacion->codigo }}</td>
	            <td>{{ $value->codigo }}</td>
	            <td>{{ $value->cotizacion->paciente->nombres . ' ' . $value->cotizacion->paciente->apellidopaterno . ' ' . $value->cotizacion->paciente->apellidomaterno }}</td>
	            <td>{{ $value->cotizacion->plan->nombre }}</td>
	            @if($value->cotizacion->tipo == 'A')
	            <td>AMBULATORIA</td>
	            @else
	            <td>HOSPITALARIA</td>
	            @endif
	            <td>{{ $value->situacion == 'E' ? 'CONFIRMADA' : 'ANULADA' }}</td>
	            <td align="center">{{ number_format($value->monto,2,'.','') }}</td>
	            <td>{{ $value->comentario == '' ? '-' : $value->comentario }}</td>
	            <td>{{ $value->responsable->nombres }}</td>
	            <td><button onclick="modal ('http://localhost/clinica/cotizacion/ver/2', 'Editar Carta de Garantía', this);" class="btn btn-xs btn-warning" type="button"><div class="glyphicon glyphicon-pencil"></div></button>
	            </td>
	            <td><button onclick="modal ('http://localhost/clinica/cotizacion/ver/2', 'Liquidación', this);" class="btn btn-xs btn-info" type="button"><div class="glyphicon glyphicon-list"></div> Liquidación</button>
	            </td>
	            <td><button onclick="#" class="btn btn-xs btn-danger" type="button"><div class="glyphicon glyphicon-remove"></div></button>
	            </td>
			</tr>
			<?php
			$contador = $contador + 1;
			$dat.=$value->id.",";
			$totalfac+=$value->monto;
			?>
			@endforeach
		</tbody>
	</table>
</div>
<div style="position: absolute; right: 20px; top: 80px; color: red; font-weight: bold;">Total Facturado: {{ number_format($totalfac,2,'.','') }} </div>
<script>
validarCheck();
<?php 
echo "cargarTodos('".substr($dat,0,strlen($dat)-1)."');";
?>
</script>
@endif