<div id="divMensajeError{!! $entidad !!}"></div>
{!! Form::model($cotizacion, $formData) !!}	
	<div class="col-lg-5 col-md-5 col-sm-5">
		<div class="form-group">
			{!! Form::label('fecha', 'Fecha:', array('class' => 'col-lg-5 col-md-5 col-sm-5 control-label')) !!}
			<div class="col-lg-7 col-md-7 col-sm-7">
				{!! Form::text('fecha', $cotizacion->fecha, array('class' => 'form-control input-xs', 'id' => 'fecha', 'readonly' => 'readonly')) !!}
			</div>
		</div>
		<div class="form-group">
			{!! Form::label('numerodocumento', 'Nro:', array('class' => 'col-lg-5 col-md-5 col-sm-5 control-label')) !!}
			<div class="col-lg-7 col-md-7 col-sm-7">
				{!! Form::text('numerodocumento', $cotizacion->numero, array('class' => 'form-control input-xs', 'id' => 'numerodocumento', 'placeholder' => 'Ingrese numerodocumento', 'readonly' => 'readonly')) !!}
			</div>
		</div>
		<div class="form-group">
			{!! Form::label('codigo', 'Código:', array('class' => 'col-lg-5 col-md-5 col-sm-5 control-label')) !!}
			<div class="col-lg-7 col-md-7 col-sm-7">
				{!! Form::text('codigo', $cotizacion->codigo, array('class' => 'form-control input-xs', 'id' => 'codigo', 'readonly' => 'readonly')) !!}
			</div>
		</div>
	</div>
	<div class="col-lg-7 col-md-7 col-sm-7">
		<div class="form-group">
			{!! Form::label('tipo', 'Tipo:', array('class' => 'col-lg-5 col-md-5 col-sm-5 control-label')) !!}
			<div class="col-lg-7 col-md-7 col-sm-7">
				{!! Form::text('tipo', $tipo, array('class' => 'form-control input-xs', 'id' => 'tipo', 'readonly' => 'readonly')) !!}
			</div>
		</div>
		<div class="form-group">
			{!! Form::label('total', 'Monto:', array('class' => 'col-lg-5 col-md-5 col-sm-5 control-label')) !!}
			<div class="col-lg-7 col-md-7 col-sm-7">
				{!! Form::text('total', number_format($cotizacion->total, 2), array('class' => 'form-control input-xs', 'id' => 'total', 'readonly' => 'readonly')) !!}
			</div>
		</div>
		<div class="form-group">
			{!! Form::label('paciente', 'Paciente:', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
			<div class="col-lg-8 col-md-8 col-sm-8">
				{!! Form::text('paciente', $cotizacion->paciente == null ? '-' : ($cotizacion->paciente->apellidopaterno . ' ' . $cotizacion->paciente->apellidomaterno . ' ' . $cotizacion->paciente->nombres), array('class' => 'form-control input-xs', 'id' => 'paciente', 'readonly' => 'readonly')) !!}
			</div>
		</div>
		<div class="form-group">
			{!! Form::label('situacion', 'Situación:', array('class' => 'col-lg-5 col-md-5 col-sm-5 control-label')) !!}
			<div class="col-lg-7 col-md-7 col-sm-7">
				{!! Form::text('situacion', $situacion, array('class' => 'form-control input-xs', 'id' => 'situacion', 'readonly' => 'readonly')) !!}
			</div>
		</div>		
	</div>
	<div class="row">
		<div class="col-lg-12 col-md-12 col-sm-12">
			<div id="divDetail" class="table-responsive">
		        <table class="table table-condensed table-border" id="tbDetalle">
	                <thead>
	                    <th class="text-center" width="5%">#</th>
	                    <th class="text-center" width="34%">Conceptos</th>
	                    <th class="text-center" width="7%">Pago</th>
	                    <th class="text-center" width="7%">Cantidad</th>
	                    <th class="text-center" width="7%">%</th>
	                    <th class="text-center" width="7%">S/.</th>
	                    <th class="text-center" width="7%">Unidad</th>
	                    <th class="text-center" width="7%">Factor</th>
	                    <th class="text-center" width="9%">Monto Total</th>
	                    <th class="text-center" width="10%">Por Facturar</th>
	                </thead>                
                    @foreach($cabeceras as $cabeza)
                        <tbody>
                            <tr>
                                <th class="text-center">§</th>
                                <th class="text-center" colspan="8">{{ $cabeza->descripcion }}</th>
                                <th class="text-right">{{ number_format($cabeza->monto,2,".","") }}</th>
                            </tr>
                            @foreach($cabeza->detalles as $detalle)
                                <tr>
                                    <td class="text-right">-</td>
                                    <td>{{ $detalle->descripcion }}</td>
                                    <td class="text-right">{{ $detalle->pago == 0 ? '' : $detalle->pago }}</td>
                                    <td class="text-right">{{ $detalle->cantidad }}</td>
                                    <td class="text-right">{{ $detalle->porcentaje == 0 ? '' : $detalle->porcentaje }}</td>
                                    <td class="text-right">{{ number_format($detalle->monto,2,".","") }}</td>
                                    <td>{{ $detalle->unidad }}</td>
                                    <td>{{ $detalle->factor }}</td>
                                    <td class="text-right">{{ number_format($detalle->total,2,".","") }}</td>
                                    <td></td>
                                </tr>
                            @endforeach
                        </tbody>
                    @endforeach
	                <tfoot>
	                    <tr>
	                        <th colspan="7"></th>
	                        <th colspan="2">Sub - Total</th>
	                        <th class="text-right">{{ number_format($cotizacion->total/1.18,2,".","") }}</th>
	                    </tr>
	                    <tr>
	                        <th colspan="7"></th>
	                        <th colspan="2">IGV</th>
	                        <th class="text-right">{{ number_format($cotizacion->total/1.18*0.18,2,".","") }}</th>
	                    </tr>
	                    <tr>
	                        <th colspan="7"></th>
	                        <th colspan="2">Total</th>
	                        <th class="text-right">{{ number_format($cotizacion->total,2,".","") }}</th>
	                    </tr>
	                </tfoot>
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
<script>
$(document).ready(function() {
    configurarAnchoModal('1000');
    init(IDFORMMANTENIMIENTO+'{!! $entidad !!}', 'B', '{!! $entidad !!}');
});
</script>