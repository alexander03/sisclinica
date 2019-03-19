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
				<td>{{ $contador }}</td>
	            <td>{{ date('d/m/Y',strtotime($value->fecha)) }}</td>
	            <td>{{ $value->codigo }}</td>
	            <td>{{ $value->paciente == null ? '-' : ($value->paciente->apellidopaterno . ' ' . $value->paciente->apellidomaterno . ' ' . $value->paciente->nombres) }}</td>
	            @if($value->tipo=='A')
	            <td>AMBULATORIO</td>
	            @elseif($value->tipo=='H')
	            <td>HOSPITALARIO</td>
	            @endif
	            @if($value->situacion=='E')
	            <td>ENVIADA</td>
	            @elseif($value->situacion=='A')
	            <td>ACEPTADA</td>
	            @elseif($value->situacion=='O')
	            <td>OBSERVADA</td>
	            @elseif($value->situacion=='R')
	            <td>RECHAZADA</td>
	            @endif
	  			<td>{{ $value->total }}</td>
	  			<td>{{ $value->responsable->nombres }}</td>
	            <td>{!! Form::button('<div class="glyphicon glyphicon-pencil"></div> Ver', array('onclick' => 'modal (\''.URL::route($ruta["ver"], $value->id).'\', \''.$titulo_ver.'\', this);', 'class' => 'btn btn-xs btn-info')) !!}</td>
	            <td>{!! Form::button('<div class="glyphicon glyphicon-eye-open"></div> Editar', array('onclick' => 'modal (\''.URL::route($ruta["edit"], array($value->id, 'listar'=>'SI')).'\', \''.$titulo_modificar.'\', this);', 'class' => 'btn btn-xs btn-warning')) !!}</td>
				<td>{!! Form::button('<div class="glyphicon glyphicon-remove"></div> Anular', array('onclick' => '#', 'class' => 'btn btn-xs btn-danger')) !!}</td>
			</tr>
			<?php
			$contador = $contador + 1;
			?>
			@endforeach
		</tbody>
	</table>
</div>
@endif