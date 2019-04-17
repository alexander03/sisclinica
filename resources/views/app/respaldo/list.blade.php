@if(count($lista) == 0)
<h3 class="text-warning">No se encontraron resultados.</h3>
@else
{!! $paginacion or '' !!}
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
		$contador = $inicio + 1;
		?>
		@foreach ($lista as $key => $value)
		<tr>
			<td>{{ $value->numero }}</td>
			<td>{{ $value->nombrearchivo }}</td>
			<td>{{ $value->estado }}</td>
			<td>{{ $value->fecha }}</td>
			<td>{{ $value->responsable->nombres.' '.$value->responsable->apellidopaterno }}</td>
			<td>{!! Form::button('<div class="glyphicon glyphicon-cloud-upload"></div> Importar', array('onclick' => 'importarArchivoHosting("'.$value->nombrearchivo.'", "--' . $value->numero .'--");', 'class' => 'btn btn-xs btn-success', 'id' => '"--' . $value->numero .'--"')) !!}</td>
			{{--<td>{!! Form::button('<div class="glyphicon glyphicon-remove"></div> Eliminar', array('onclick' => 'modal (\''.URL::route($ruta["delete"], array($value->id, 'SI')).'\', \''.$titulo_eliminar.'\', this);', 'class' => 'btn btn-xs btn-danger')) !!}</td>--}}
			<td>-</td>
		</tr>
		<?php
		$contador = $contador + 1;
		?>
		@endforeach
	</tbody>
</table>
@endif