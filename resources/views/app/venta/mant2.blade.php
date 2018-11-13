<div id="divMensajeError{!! $entidad !!}"></div>
<form method="POST" action="http://localhost/juanpablo/venta" accept-charset="UTF-8" class="form-horizontal" id="formMantenimientoVenta" autocomplete="off">
	{{ csrf_field() }}
	<input id="listar" name="listar" type="hidden" value="SI">
	<input id="total" name="total" type="hidden" value="0" style="text-align: right;">
	<input id="detalle" name="detalle" type="hidden" value="false">
	<div class="col-lg-5 col-md-5 col-sm-5">
		<div class="form-group" style="height: 12px;">
			<label for="documento" class="col-lg-4 col-md-4 col-sm-4 control-label">Documento:</label>
			<div class="col-lg-7 col-md-7 col-sm-7">
				<select style="background-color: rgb(25,241,227);" class="form-control input-xs" id="documento" onchange="generarNumero(this.value);" name="documento">
					@foreach($tiposdocumento as $tipodoc)
					<option value="{{$tipodoc->id}}">{{$tipodoc->nombre}}</option>
					@endforeach
				</select>
			</div>
		</div>
		<div class="form-group" style="height: 12px;">
			<label for="tipoventa" class="col-lg-4 col-md-4 col-sm-4 control-label">Tipo Venta:</label>
			<div class="col-lg-7 col-md-7 col-sm-7">
				<select style="background-color: rgb(25,241,227);" class="form-control input-xs" id="tipoventa" onchange="cambiotipoventa();" name="tipoventa">
					<option value="N">Normal</option>
					<option value="C">Convenio</option>
				</select>
			</div>
		</div>
		<div class="form-group" style="height: 12px;">
			<label for="formapago" class="col-lg-4 col-md-4 col-sm-4 control-label">Form Pago:</label>
			<div class="col-lg-7 col-md-7 col-sm-7">
				<select class="form-control input-xs" id="formapago" onchange="validarFormaPago(this.value);" name="formapago">
					<option value="C">Contado</option>
					<option value="P">Pendiente</option>
					<option value="T">Tarjeta</option>
				</select>
			</div>
		</div>
		
		<div class="form-group" style="height: 12px;">
			<label for="numerodocumento" class="col-lg-4 col-md-4 col-sm-4 control-label">Nro Doc:</label>
			<div class="col-lg-7 col-md-7 col-sm-7">
				<input class="form-control input-xs" id="numerodocumento" placeholder="Ingrese numerodocumento" readonly="true" name="numerodocumento" type="text" value="">
			</div>

		</div>
		<div class="form-group" style="height: 12px;">
			<label for="nombreempresa" class="col-lg-4 col-md-4 col-sm-4 control-label">Empresa:</label>
			<input id="empresa_id" name="empresa_id" type="hidden">
			<div class="col-lg-7 col-md-7 col-sm-7">
				<input style="background-color: rgb(252,215,147);" class="form-control input-xs" id="nombreempresa" placeholder="Seleccione Empresa" name="nombreempresa" type="text">
				
			</div>
		</div>
		<div class="form-group" style="height: 12px;">
			<label for="nombrepersona" class="col-lg-4 col-md-4 col-sm-4 control-label">Cliente:</label>
			<input id="person_id" name="person_id" type="hidden">
			<div class="col-lg-7 col-md-7 col-sm-7">
				<input style="background-color: rgb(252,215,147);" class="form-control input-xs" id="nombrepersona" placeholder="Seleccione Cliente" name="nombrepersona" type="text">
			</div>
		</div>
		<div class="form-group" style="height: 12px;">
			<label for="nombredoctor" class="col-lg-4 col-md-4 col-sm-4 control-label">Medico:</label>
			<input id="doctor_id" name="doctor_id" type="hidden">
			<div class="col-lg-7 col-md-7 col-sm-7">
				<input style="background-color: rgb(252,215,147);" class="form-control input-xs" id="nombredoctor" placeholder="Seleccione Medico" name="nombredoctor" type="text">
			</div>
		</div>
		<div class="form-group" style="display: none">
    		<label for="paciente" class="col-lg-4 col-md-4 col-sm-4 control-label">Paciente:</label>
    		<div class="col-lg-7 col-md-7 col-sm-7">
            {!-- Form::hidden('person_id', null, array('id' =&gt; 'person_id')) --!}
    			<span class="twitter-typeahead" style="position: relative; display: inline-block; direction: ltr;"><input class="form-control input-xs tt-hint" type="text" disabled="" autocomplete="off" spellcheck="false" style="position: absolute; top: 0px; left: 0px; border-color: transparent; box-shadow: none; background: none 0% 0% / auto repeat scroll padding-box border-box rgb(255, 255, 255);"><input class="form-control input-xs tt-input" id="paciente" placeholder="Ingrese Paciente" name="paciente" type="text" autocomplete="off" spellcheck="false" dir="auto" style="position: relative; vertical-align: top; background-color: transparent;"><pre aria-hidden="true" style="position: absolute; visibility: hidden; white-space: pre; font-family: &quot;Source Sans Pro&quot;, &quot;Helvetica Neue&quot;, Helvetica, Arial, sans-serif; font-size: 11px; font-style: normal; font-variant: normal; font-weight: 400; word-spacing: 0px; letter-spacing: 0px; text-indent: 0px; text-rendering: auto; text-transform: none;"></pre><span class="tt-dropdown-menu" style="position: absolute; top: 100%; left: 0px; z-index: 100; display: none; right: auto;"><div class="tt-dataset-0"></div></span></span>
    		</div>
    	</div>
		<div class="form-group" id="divConvenio" style="height: 12px; display: none;">
			<label for="conveniofarmacia" class="col-lg-4 col-md-4 col-sm-4 control-label">Convenio:</label>
			<input id="conveniofarmacia_id" name="conveniofarmacia_id" type="hidden">
			<div class="col-lg-7 col-md-7 col-sm-7">
				<input class="form-control input-xs" id="conveniofarmacia" placeholder="Seleccione Convenio" name="conveniofarmacia" type="text">
				
			</div>
		</div>
		<div class="form-group" id="divDescuentokayros" style="height: 12px;">
			<label for="fecha" class="col-lg-2 col-md-2 col-sm-2 control-label">Fecha:</label>
			<div class="col-lg-3 col-md-3 col-sm-3">
				<div class="input-group input-group-xs" id="divfecha">
					<input class="form-control input-xs" id="fecha" placeholder="Ingrese fecha" name="fecha" type="text" value="{{date("d/m/Y")}}">
					
				</div>
			</div>
			
			<label for="descuentokayros" class="col-lg-4 col-md-4 col-sm-4 control-label">Dcto. Kayros:</label>
			<div class="col-lg-2 col-md-2 col-sm-2">
				<input class="form-control input-xs" id="descuentokayros" name="descuentokayros" type="text">
				
			</div>	
		</div>
		<div class="form-group" style="height: 12px;">
		<div class="col-lg-7 col-md-7 col-sm-7">
			<label for="copago" class="col-lg-7 col-md-7 col-sm-7 control-label">Dcto Planilla:</label>	
				<div class="col-lg-1 col-md-1 col-sm-1">
        			<input name="descuentoplanilla" id="descuentoplanilla" value="NO" type="checkbox" onclick="pendienteplanilla(this.checked)">
        		</div>
			</div>
			<label for="copago" class="col-lg-2 col-md-2 col-sm-2 control-label">Copago:</label>
			<div class="col-lg-2 col-md-2 col-sm-2">
				<input class="form-control input-xs" id="copago" name="copago" type="text">
				
			</div>
		</div>	
		<div class="form-group descuentopersonal" style="display: none;height: 12px;">
            <label for="personal" class="col-lg-2 col-md-2 col-sm-2 control-label">Personal:</label>
            <div class="col-lg-10 col-md-10 col-sm-10">
            <input id="personal_id" name="personal_id" type="hidden">
            <span class="twitter-typeahead" style="position: relative; display: inline-block; direction: ltr;"><input class="form-control input-xs tt-hint" type="text" disabled="" autocomplete="off" spellcheck="false" style="position: absolute; top: 0px; left: 0px; border-color: transparent; box-shadow: none; background: none 0% 0% / auto repeat scroll padding-box border-box rgb(255, 255, 255);"><input class="form-control input-xs tt-input" id="personal" placeholder="Ingrese Personal" name="personal" type="text" autocomplete="off" spellcheck="false" dir="auto" style="position: relative; vertical-align: top; background-color: transparent;"><pre aria-hidden="true" style="position: absolute; visibility: hidden; white-space: pre; font-family: &quot;Source Sans Pro&quot;, &quot;Helvetica Neue&quot;, Helvetica, Arial, sans-serif; font-size: 11px; font-style: normal; font-variant: normal; font-weight: 400; word-spacing: 0px; letter-spacing: 0px; text-indent: 0px; text-rendering: auto; text-transform: none;"></pre><span class="tt-dropdown-menu" style="position: absolute; top: 100%; left: 0px; z-index: 100; display: none; right: auto;"><div class="tt-dataset-1"></div></span></span>
            </div>
		</div>
		<div class="form-group" style="height: 12px;">
		<div class="col-lg-3 col-md-3 col-sm-3">
				
				
			</div>
		<label for="nombreconvenio" class="col-lg-2 col-md-2 col-sm-2 control-label">Convenio:</label>
			<div class="col-lg-6 col-md-6 col-sm-6">
				<input class="form-control input-xs" id="nombreconvenio" readonly="" name="nombreconvenio" type="text">
				
			</div>
		</div>
		<div class="form-group tarjeta" style="height: 12px; display: none;">
			<label for="fecha" class="col-lg-2 col-md-2 col-sm-2 control-label">Tarjeta:</label>
			<div class="col-lg-4 col-md-4 col-sm-4">
				<div class="input-group input-group-xs" id="divfecha">
					<select style="background-color: rgb(25,241,227);" class="form-control input-xs" id="tipotarjeta" name="tipotarjeta"><option value="VISA">VISA</option><option value="MASTER">MASTER</option></select>
					
				</div>
			</div>
			<label for="tipotarjeta2" class="col-lg-2 col-md-2 col-sm-2 control-label">Tipo:</label>
			<div class="col-lg-4 col-md-4 col-sm-4">
				<div class="input-group input-group-xs" id="divfecha">
					<select style="background-color: rgb(25,241,227);" class="form-control input-xs" id="tipotarjeta2" name="tipotarjeta2"><option value="CREDITO">CREDITO</option><option value="DEBITO">DEBITO</option></select>
					
				</div>
			</div>
			
		</div>
		<div class="form-group tarjeta" style="height: 12px; display: none;">
			<div class="col-lg-5 col-md-5 col-sm-5">
				
			</div>
			<label for="nroref" class="col-lg-3 col-md-3 col-sm-3 control-label">Nro. Op.:</label>
            <div class="col-lg-3 col-md-3 col-sm-3">
                <input class="form-control input-xs" id="nroref" name="nroref" type="text">
            </div>
		</div>
	</div>
	<div class="col-lg-7 col-md-7 col-sm-7">
		<div class="form-group" style="height: 12px;">
			<label for="nombreproducto" class="col-lg-3 col-md-3 col-sm-3 control-label">Producto:</label>
			<div class="col-lg-4 col-md-4 col-sm-4">
				<input class="form-control input-xs" id="nombreproducto" placeholder="Ingrese nombre" onkeypress="" name="nombreproducto" type="text">
			</div>
			<input type="hidden" name="idsesioncarrito" id="idsesioncarrito" value="20181031125201">
			<label for="cantidad" class="col-lg-3 col-md-3 col-sm-3 control-label">Cantidad:</label>
			<div class="col-lg-2 col-md-2 col-sm-2">
				<input class="form-control input-xs" id="cantidad" name="cantidad" type="text" style="text-align: right;">
			</div>
			<input id="producto_id" name="producto_id" type="hidden">
			<input id="preciokayros" name="preciokayros" type="hidden">

			<input id="precioventa" name="precioventa" type="hidden">
			<input id="stock" name="stock" type="hidden">
		</div>
		<div class="form-group" id="divProductos" style="overflow:auto; height:180px; padding-right:10px; border:1px outset">
			
		</div>

		<div class="form-group">
			<div class="col-lg-12 col-md-12 col-sm-12 text-right">
				<!--<div align="center" class="col-lg-3 ">
		       {-- Form::button('<i class="glyphicon glyphicon-plus"></i> Agregar', array('class' => 'btn btn-info btn-xs', 'id' => 'btnAgregar', 'onclick' => 'ventanaproductos();')) --}   
		    	
		    	</div>-->
				<button class="btn btn-success btn-sm" id="btnGuardar" onclick="guardarVenta('Venta', this)" type="button"><i class="fa fa-check fa-lg"></i> Registrar</button>
				<button class="btn btn-warning btn-sm" id="btnCancelarVenta" onclick="cerrarModal();" type="button"><i class="fa fa-exclamation fa-lg"></i> Cancelar</button>
			</div>
		</div>
		
	</div>
	<div class="form-group" style="display: none;">
		<div class="col-lg-12 col-md-12 col-sm-12">
			<label for="codigo" class="col-lg-4 col-md-4 col-sm-4 control-label">Comprobar Productos:</label>
			<div class="col-lg-5 col-md-5 col-sm-5">
				<input class="form-control input-xs" id="codigo" placeholder="Ingrese codigo" name="codigo" type="text">
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-lg-12 col-md-12 col-sm-12">
			<div id="divDetail" class="table-responsive" style="overflow:auto; height:220px; padding-right:10px; border:1px outset">
		        <table style="width: 100%;" class="table-condensed table-striped">
		            <thead>
		                <tr>
		                    <th bgcolor="#E0ECF8" class="text-center">Producto</th>
		                    <th bgcolor="#E0ECF8" class="text-center">Cantidad</th>
		                    <th bgcolor="#E0ECF8" class="text-center">Precio</th>
		                    <th bgcolor="#E0ECF8" class="text-center">Subtotal</th>
		                    <th bgcolor="#E0ECF8" class="text-center">Quitar</th>
		                </tr>
		            </thead>
		            <tbody>
		            	
		            </tbody>
		        </table>
		    </div>
		</div>
	 </div>
    <br>
</form>

<style type="text/css">
tr.resaltar {
    background-color: #A9F5F2;
    cursor: pointer;
}
</style>
<script type="text/javascript">
var valorbusqueda="";
var indice = -1;
var anterior = -1;
$(document).ready(function() {
	configurarAnchoModal('1300');
	init(IDFORMMANTENIMIENTO+'{!! $entidad !!}', 'B', '{!! $entidad !!}');

	console.log(IDFORMMANTENIMIENTO);

		$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="total"]').inputmask('decimal', { radixPoint: ".", autoGroup: true, groupSeparator: ",", groupSize: 3, digits: 2 });
		$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="cantidad"]').inputmask('decimal', { radixPoint: ".", autoGroup: true, groupSeparator: ",", groupSize: 3, digits: 2 });

	var clientes = new Bloodhound({
		datumTokenizer: function (d) {
			return Bloodhound.tokenizers.whitespace(d.value);
		},
        limit: 10,
		queryTokenizer: Bloodhound.tokenizers.whitespace,
		remote: {
			url: 'venta/clienteautocompletar/%QUERY',
			filter: function (clientes) {
				return $.map(clientes, function (movie) {
					return {
						value: movie.persona + " (" + movie.convenio + ")",
						//value: movie.value,
						id: movie.id,
					};
				});
			}
		}
	});
	clientes.initialize();
	$("#nombrepersona").typeahead(null,{
		displayKey: 'value',
		source: clientes.ttAdapter()
	}).on('typeahead:selected', function (object, datum) {
        $("#nombrepersona").val(datum.value);
        $("#person_id").val(datum.id);
        $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="busqueda"]').val(datum.value);
	});
		
	var empresa = new Bloodhound({
		datumTokenizer: function (d) {
			return Bloodhound.tokenizers.whitespace(d.value);
		},
        limit: 10,
		queryTokenizer: Bloodhound.tokenizers.whitespace,
		remote: {
			url: 'venta/empresaautocompletar/%QUERY',
			filter: function (empresa) {
				return $.map(empresa, function (movie) {
					return {
						value: movie.bussinesname + " (" + movie.ruc + ")",
						id: movie.id,
					};
				});
			}
		}
	});
	empresa.initialize();
	$("#nombreempresa").typeahead(null,{
		displayKey: 'value',
		source: empresa.ttAdapter()
	}).on('typeahead:selected', function (object, datum) {
        $("#nombreempresa").val(datum.value);
        $("#empresa_id ").val(datum.id);
        $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="busqueda"]').val(datum.value);
	});

	$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="fecha"]').inputmask("dd/mm/yyyy");
		$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="fecha"]').datetimepicker({
			pickTime: false,
			language: 'es'
		});

	$(IDFORMMANTENIMIENTO+'{!! $entidad !!}' + ' :input[id="codigo"]').keydown( function(e) {
			var key = e.charCode ? e.charCode : e.keyCode ? e.keyCode : 0;
			if(key == 13) {
				comprobarproducto ();
			}
		});

	/*$(IDFORMMANTENIMIENTO+'{!! $entidad !!}' + ' :input[id="nombrepersona"]').keydown( function(e) {
			var key = e.charCode ? e.charCode : e.keyCode ? e.keyCode : 0;
			if(key == 13) {
				/*var documento = $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="documento"]').val();
				if (documento == 4) {
					modal('{{URL::route('venta.busquedaempresa')}}', '');
				}else{
					modal('{{URL::route('venta.busquedacliente')}}', '');
				}*/
					/*modal('{{URL::route('venta.busquedacliente')}}', '');
			}
		});*/

	$(IDFORMMANTENIMIENTO+'{!! $entidad !!}' + ' :input[id="nombredoctor"]').keydown( function(e) {
			var key = e.charCode ? e.charCode : e.keyCode ? e.keyCode : 0;
			if(key == 27) {
				$('#nombreproducto').focus();
			}
		});
	$(IDFORMMANTENIMIENTO+'{!! $entidad !!}' + ' :input[id="nombreempresa"]').keydown( function(e) {
			var key = e.charCode ? e.charCode : e.keyCode ? e.keyCode : 0;
			if(key == 13) {
				/*var documento = $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="documento"]').val();
				if (documento == 4) {
					modal('{{URL::route('venta.busquedaempresa')}}', '');
				}else{
					modal('{{URL::route('venta.busquedacliente')}}', '');
				}*/
					modal('{{URL::route('venta.busquedaempresa')}}', '');
			}
		});

	/*$(IDFORMMANTENIMIENTO+'{!! $entidad !!}' + ' :input[id="nombrepersona"]').click(function(){
			/*var documento = $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="documento"]').val();
			if (documento == 4) {
				modal('{{URL::route('venta.busquedaempresa')}}', '');
			}else{
				modal('{{URL::route('venta.busquedacliente')}}', '');
			}*/
			/*modal('{{URL::route('venta.busquedacliente')}}', '');
			
		});*/
	/*
	$(IDFORMMANTENIMIENTO+'{!! $entidad !!}' + ' :input[id="nombreempresa"]').click(function(){
			/*var documento = $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="documento"]').val();
			if (documento == 4) {
				modal('{{URL::route('venta.busquedaempresa')}}', '');
			}else{
				modal('{{URL::route('venta.busquedacliente')}}', '');
			}*/
			/*modal('{{URL::route('venta.busquedaempresa')}}', '');
			
		});
	*/

	$(IDFORMMANTENIMIENTO+'{!! $entidad !!}' + ' :input[id="conveniofarmacia"]').focus(function(){
			abrirconvenios();
		});

	$(IDFORMMANTENIMIENTO+'{!! $entidad !!}' + ' :input[id="descuentokayros"]').keydown( function(e) {
			var key = e.charCode ? e.charCode : e.keyCode ? e.keyCode : 0;
			if(key == 13) {
				e.preventDefault();
				var inputs = $(this).closest('form').find(':input:visible:not([disabled]):not([readonly])');
				inputs.eq( inputs.index(this)+ 1 ).focus();
			}
		});
	$(IDFORMMANTENIMIENTO+'{!! $entidad !!}' + ' :input[id="copago"]').keydown( function(e) {
			var key = e.charCode ? e.charCode : e.keyCode ? e.keyCode : 0;
			if(key == 13) {
				e.preventDefault();
				var inputs = $(this).closest('form').find(':input:visible:not([disabled]):not([readonly])');
				inputs.eq( inputs.index(this)+ 1 ).focus();
			}
		});
	$(IDFORMMANTENIMIENTO+'{!! $entidad !!}' + ' :input[id="preciokayros"]').keydown( function(e) {
			var key = e.charCode ? e.charCode : e.keyCode ? e.keyCode : 0;
			if(key == 13) {
				e.preventDefault();
				var inputs = $(this).closest('form').find(':input:visible:not([disabled]):not([readonly])');
				inputs.eq( inputs.index(this)+ 1 ).focus();
			}
		});
	$(IDFORMMANTENIMIENTO+'{!! $entidad !!}' + ' :input[id="cantidad"]').keydown( function(e) {
			var key = e.charCode ? e.charCode : e.keyCode ? e.keyCode : 0;
			if(key == 13) {
				/*e.preventDefault();
				var inputs = $(this).closest('form').find(':input:visible:not([disabled]):not([readonly])');
				inputs.eq( inputs.index(this)+ 1 ).focus();*/
				addpurchasecart();
				$(IDFORMMANTENIMIENTO+'{!! $entidad !!}' + ' :input[id="nombreproducto"]').val('');
				$(IDFORMMANTENIMIENTO+'{!! $entidad !!}' + ' :input[id="cantidad"]').val('');
				indice = -1;
				$(IDFORMMANTENIMIENTO+'{!! $entidad !!}' + ' :input[id="nombreproducto"]').focus();
			}
		});


	var personas = new Bloodhound({
		datumTokenizer: function (d) {
			return Bloodhound.tokenizers.whitespace(d.value);
		},
		queryTokenizer: Bloodhound.tokenizers.whitespace,
		remote: {
			url: 'historia/personautocompletar/%QUERY',
			filter: function (personas) {
				return $.map(personas, function (movie) {
					return {
						value: movie.value,
						id: movie.id,
                        historia: movie.numero,
                        person_id:movie.person_id,
                        tipopaciente:movie.tipopaciente,
					};
				});
			}
		}
	});
	personas.initialize();
	$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="paciente"]').typeahead(null,{
		displayKey: 'value',
		source: personas.ttAdapter()
	}).on('typeahead:selected', function (object, datum) {  
        
        $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="person_id"]').val(datum.person_id);
	});

	var personal = new Bloodhound({
        datumTokenizer: function (d) {
            return Bloodhound.tokenizers.whitespace(d.value);
        },
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        limit:10,
        remote: {
            url: 'employee/mixtoautocompletar/%QUERY',
            filter: function (personas) {
                return $.map(personas, function (movie) {
                    return {
                        value: movie.value,
                        id: movie.id,
                    };
                });
            }
        }
    });
    personal.initialize();
    $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="personal"]').typeahead(null,{
        displayKey: 'value',
        source: personal.ttAdapter()
    }).on('typeahead:selected', function (object, datum) {
        $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="personal_id"]').val(datum.id);
        $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="personal"]').val(datum.value);
    });

	var doctores = new Bloodhound({
			datumTokenizer: function (d) {
				return Bloodhound.tokenizers.whitespace(d.value);
			},
			queryTokenizer: Bloodhound.tokenizers.whitespace,
			remote: {
				url: 'person/doctorautocompleting/%QUERY',
				filter: function (doctores) {
					return $.map(doctores, function (movie) {
						return {
							value: movie.value,
							id: movie.id
						};
					});
				}
			}
		});
		doctores.initialize();
		$('#nombredoctor').typeahead(null,{
			displayKey: 'value',
			source: doctores.ttAdapter()
		}).on('typeahead:selected', function (object, datum) {
			$('#doctor_id').val(datum.id);
		});

	$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="nombreproducto"]').on( 'keydown', function () {
        var e = window.event; 
        var keyc = e.keyCode || e.which;
        console.log(this.value);
        console.log(valorbusqueda);
        if(this.value.length>2 && keyc == 13 && valorbusqueda!=this.value){
            buscarProducto(this.value);
            valorbusqueda=this.value;
            this.focus();
            return false;
        }
        if(keyc == 38 || keyc == 40 || keyc == 27) {
            var tabladiv='tablaProducto';
			var child = document.getElementById(tabladiv).rows;
			//var indice = -1;
			var i=0;
            /*$('#tablaProducto tr').each(function(index, elemento) {
                if($(elemento).hasClass("tr_hover")) {
    			    $(elemento).removeClass("par");
    				$(elemento).removeClass("impar");								
    				indice = i;
                }
                if(i % 2==0){
    			    $(elemento).removeClass("tr_hover");
    			    $(elemento).addClass("impar");
                }else{
    				$(elemento).removeClass("tr_hover");								
    				$(elemento).addClass('par');
    			}
    			i++;
    		});*/		 
			// return
			//if(keyc == 13) { // enter
			if(keyc == 27) { // esc       				
			     if(indice != -1){
					var seleccionado = '';			 
					if(child[indice].id) {
					   seleccionado = child[indice].id;
					} else {
					   seleccionado = child[indice].id;
					}		 
					seleccionarProducto(seleccionado,$('#tdPrecioVenta'+seleccionado).text(),$('#tdPrecioKayros'+seleccionado).text(),$('#tdStock'+seleccionado).text());
					//seleccionarProducto(seleccionado);
				}
			} else {
				// abajo
				if(keyc == 40) {
					if(indice == (child.length - 1) ) {
					   indice = 1;
					} else {
					   if(indice==-1) indice=0;
	                   indice=indice+1;
					} 
				// arriba
				} else if(keyc == 38) {
					indice = indice - 1;
					if(indice==0) indice=-1;
					if(indice < 0) {
						indice = (child.length - 1);
					}
				}	
				
				child[indice].className = child[indice].className+' tr_hover';

				if (indice != -1) {
					var element = '#'+child[indice].id;
					$(element).addClass("resaltar");
					if (anterior  != -1) {
						element = '#'+anterior;
						$(element).removeClass("resaltar");
					}
					anterior = child[indice].id;
				}
			}
        }
    });


	cambiotipoventa();
	$(IDFORMMANTENIMIENTO+'{!! $entidad !!}' + ' :input[id="nombrepersona"]').focus();

}); 

var valorinicial="";
function buscarProducto(valor){
    if(valorinicial!=valor){valorinicial=valor;
        $.ajax({
            type: "POST",
            url: "venta/buscandoproducto",
            data: "nombre="+$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="nombreproducto"]').val()+"&_token="+$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[name="_token"]').val(),
            success: function(a) {
                datos=JSON.parse(a);
                //$("#divProductos").html("<table class='table table-bordered table-condensed table-hover' border='1' id='tablaProducto'><thead><tr><th class='text-center'>P. Activo</th><th class='text-center'>Nombre</th><th class='text-center'>Presentacion</th><th class='text-center'>Stock</th><th class='text-center'>P.Kayros</th><th class='text-center'>P.Venta</th></tr></thead></table>");
                $("#divProductos").html("<table class='table-condensed table-hover' border='1' id='tablaProducto'><thead><tr><th class='text-center' style='width:220px;'><span style='display: block; font-size:.9em'>P. Activo</span></th><th class='text-center' style='width:220px;'><span style='display: block; font-size:.9em'>Nombre</span></th><th class='text-center' style='width:70px;'><span style='display: block; font-size:.9em'>Presentacion</span></th><th class='text-center' style='width:20px;'><span style='display: block; font-size:.9em'>Stock</span></th><th class='text-center' style='width:20px;'><span style='display: block; font-size:.9em'>P.Kayros</span></th><th class='text-center' style='width:20px;'><span style='display: block; font-size:.9em'>P.Venta</span></th></tr></thead></table>");
                var pag=parseInt($("#pag").val());
                var d=0;
                for(c=0; c < datos.length; c++){
                    var a="<tr id='"+datos[c].idproducto+"' onclick=\"seleccionarProducto('"+datos[c].idproducto+"','"+datos[c].precioventa+"','"+datos[c].preciokayros+"','"+datos[c].stock+"')\"><td align='center'><span style='display: block; font-size:.7em'>"+datos[c].principio+"</span></td><td><span style='display: block; font-size:.7em'>"+datos[c].nombre+"</span></td><td align='right'><span style='display: block; font-size:.7em'>"+datos[c].presentacion+"</span></td><td align='right'><span style='display: block; font-size:.7em' id='tdStock"+datos[c].idproducto+"'>"+datos[c].stock+"</span></td><td align='right'><span style='display: block; font-size:.7em' id='tdPrecioKayros"+datos[c].idproducto+"'>"+datos[c].preciokayros+"</span></td><td align='right'><span style='display: block; font-size:.7em' id='tdPrecioVenta"+datos[c].idproducto+"'>"+datos[c].precioventa+"</span></td></tr>";
                    $("#tablaProducto").append(a);           
                }
                $('#tablaProducto').DataTable({
                    "scrollY":        "250px",
                    "scrollCollapse": true,
                    "paging":         false,
                    "ordering"        :false
                });
                $('#tablaProducto_filter').css('display','none');
                $("#tablaProducto_info").css("display","none");
    	    }
        });
    }
}

function seleccionarProducto(idproducto,precioventa,preciokayros,stock){
	//alert(idproducto);
	var _token =$('input[name=_token]').val();
	/*$.post('{{ URL::route("venta.consultaproducto")}}', {idproducto: idproducto,_token: _token} , function(data){
		//$('#divDetail').html(data);
		//calculatetotal();
		var datos = data.split('@');
		$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="producto_id"]').val(datos[0]);
		$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="preciokayros"]').val(datos[1]);
		$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="precioventa"]').val(datos[2]);
		$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="stock"]').val(datos[3]);		
	});*/
	$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="producto_id"]').val(idproducto);
	$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="preciokayros"]').val(preciokayros);
	$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="precioventa"]').val(precioventa);
	$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="stock"]').val(stock);		
	$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="cantidad"]').focus();
}

function ventanaproductos() {
	var tipoventa = $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="tipoventa"]').val();
	var descuentokayros = $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="descuentokayros"]').val();
	var copago = $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="copago"]').val();
	modal('{{URL::route('venta.buscarproducto')}}'+'?tipoventa='+tipoventa+'&descuentokayros='+descuentokayros+'&copago='+copago, '');
}


function abrirconvenios() {
	modal('{{URL::route('venta.buscarconvenio')}}', '');
}

function pendienteplanilla(check){
    if(check){
        $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="descuentoplanilla"]').val('SI');
        $(".descuentopersonal").css('display','');
    }else{
        $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="descuentoplanilla"]').val('NO');
        $(".descuentopersonal").css('display','none');
    }
}

function generarNumero(valor){
    $.ajax({
        type: "POST",
        url: "venta/generarNumero",
        data: "tipodocumento_id="+valor+"&_token={{ csrf_token() }}",
        success: function(a) {
            $("#numerodocumento").val(a);
        }
    });
}

generarNumero($("#documento").val());


function setValorFormapago (id, valor) {
	$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="' + id + '"]').val(valor);
}

function getValorFormapago (id) {
	var valor = $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="' + id + '"]').val();
	return valor;
}

function cambiotipoventa() {
	var tipoventa = $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="tipoventa"]').val();
	if (tipoventa == 'C') {
		//$('#divConvenio').show();
		//$('#divDescuentokayros').show();
		//$('#divCopago').show();
		modal('{{URL::route('venta.busquedacliente')}}', '');

	}else if (tipoventa == 'N') {
		$('#divConvenio').hide();
		//$('#divDescuentokayros').hide();
		//$('#divCopago').hide();
	}
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
	$.post('{{ URL::route("venta.quitarcarritoventa")}}', {valor: valor,_token: _token} , function(data){
		$('#divDetail').html(data);
		calculatetotal();
		//generarSaldototal ();
		// var totalpedido = $('#totalpedido').val();
		// $('#total').val(totalpedido);
	});
}

function calculatetotal () {
	var _token =$('input[name=_token]').val();
	var valor =0;
	$.post('{{ URL::route("venta.calculartotal")}}', {valor: valor,_token: _token} , function(data){
		valor = retornarFloat(data);
		$("#total").val(valor);
		//generarSaldototal();
		// var totalpedido = $('#totalpedido').val();
		// $('#total').val(totalpedido);
	});
}

function comprobarproducto () {
	var _token =$('input[name=_token]').val();
	var valor =$('input[name=codigo]').val();
	$.post('{{ URL::route("venta.comprobarproducto")}}', {valor: valor,_token: _token} , function(data){
		
		if (data.trim() == 'NO') {
			$('input[name=codigo]').val('');
			bootbox.alert("Este Producto no esta en lista de venta");
            setTimeout(function () {
                $('#codigo').focus();
            },2000) 
		}else{
			$('input[name=codigo]').val('');
			$('#codigo').focus();
		}
	});
}

function seleccionarCliente(id) {
	var _token =$('input[name=_token]').val();
	$.post('{{ URL::route("venta.clienteid")}}', {id: id,_token: _token} , function(data){
		var datos = data.split('-'); 
		$('#person_id').val(datos[0]);
		$('#nombrepersona').val(datos[1]);
		
		cerrarModal();
		var tipoventa = $('#tipoventa').val();
		if (tipoventa == 'N') {
			//$('#nombreproducto').focus();
			$('#nombredoctor').focus();
		}else{
			//$('#conveniofarmacia').focus();
			abrirconvenios();
		}
	});
	
}

function seleccionarParticular(value) {
	$('#nombrepersona').val(value);
	cerrarModal();
	//$('#nombreproducto').focus();
	$('#nombredoctor').focus();
}

function agregarconvenio(id){

	var kayros = $('#txtKayros').val();
	var copago = $('#txtCopago').val();
	var convenio_id = id;

	var _token =$('input[name=_token]').val();
	if(kayros.trim() == '' ){
		bootbox.alert("Ingrese precio kayros");
            setTimeout(function () {
                $('#txtKayros').focus();
            },2000) 
	}else if(copago.trim() == '' ){
		bootbox.alert("Ingrese copago");
            setTimeout(function () {
                $('#txtCopago').focus();
            },2000) 
	}else{
		$.post('{{ URL::route("venta.agregarconvenio")}}', {kayros: kayros,copago: copago, convenio_id: convenio_id,_token: _token} , function(data){
			dat = data.split('|');
			$('#copago').val(copago);
			$('#descuentokayros').val(kayros);
			$('#conveniofarmacia').val(dat[0]);
			$('#nombreconvenio').val(dat[0]);
			$('#conveniofarmacia_id').val(dat[1]);

			cerrarModal();
			$('#descuentokayros').focus();
			/*$('#divDetail').html(data);
			calculatetotal();
			bootbox.alert("Producto Agregado");
            setTimeout(function () {
                $('#txtPrecio' + elemento).focus();
            },2000) */
			
		});
	}
	}

function agregarempresa(id){

	var ruc = $('#ruc').val();
	var direccion = $('#direccion').val();
	var telefono = $('#telefono').val();
	var empresa_id = id;

	var _token =$('input[name=_token]').val();
	/*if(kayros.trim() == '' ){
		bootbox.alert("Ingrese precio kayros");
            setTimeout(function () {
                $('#txtKayros').focus();
            },2000) 
	}else if(copago.trim() == '' ){
		bootbox.alert("Ingrese copago");
            setTimeout(function () {
                $('#txtCopago').focus();
            },2000) 
	}else{*/
	$.post('{{ URL::route("venta.agregarempresa")}}', {ruc: ruc,direccion: direccion,telefono: telefono, empresa_id: empresa_id,_token: _token} , function(data){
		dat = data.split('*');
		$('#nombreempresa').val(dat[0]);
		$('#empresa_id').val(dat[1]);

		cerrarModal();
		$('#nombreproducto').focus();
		/*$('#divDetail').html(data);
		calculatetotal();
		bootbox.alert("Producto Agregado");
        setTimeout(function () {
            $('#txtPrecio' + elemento).focus();
        },2000) */
		
	});
	//}
}

function addpurchasecart(elemento){
	var cantidad = $('#cantidad').val();
	var price = $('#precioventa').val();
	var preciokayros = $('#preciokayros').val();
	var product_id = $('#producto_id').val();
	var stock = $('#stock').val();
	var tipoventa = $(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="tipoventa"]').val();
	var descuentokayros = $(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="descuentokayros"]').val();
	var copago = $(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="copago"]').val();
	var conveniofarmacia_id = $(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="conveniofarmacia_id"]').val();
	var _token =$('input[name=_token]').val();
	if(cantidad.trim() == '' ){
		bootbox.alert("Ingrese Cantidad");
            setTimeout(function () {
                $('#cantidad').focus();
            },2000) 
	}else if(cantidad.trim() == 0){
		bootbox.alert("la cantidad debe ser mayor a 0");
            setTimeout(function () {
                $('#cantidad').focus();
            },2000) 
	}else if(price.trim() == '' ){
		bootbox.alert("Ingrese Precio");
            setTimeout(function () {
                $('#precioventa').focus();
            },2000) 
	}else if(price.trim() == 0){
		bootbox.alert("el precio debe ser mayor a 0");
            setTimeout(function () {
                $('#precioventa').focus();
            },2000) 
	}else if(parseFloat(cantidad.trim()) > parseFloat(stock)){
		bootbox.alert("No puede vender una cantidad mayor al stock actual");
            setTimeout(function () {
                $('#cantidad').focus();
            },2000) 
	}else{
		var idsesioncarrito = $("#idsesioncarrito").val();
		var detalle = $('#detalle').val();
		$('#detalle').val(true);
		$.post('{{ URL::route("venta.agregarcarritoventa")}}', {cantidad: cantidad,precio: price, producto_id: product_id, tipoventa: tipoventa, descuentokayros: descuentokayros, copago: copago, preciokayros: preciokayros, conveniofarmacia_id: conveniofarmacia_id, detalle: detalle,idsesioncarrito:idsesioncarrito,_token: _token} , function(data){
			$('#detalle').val(true);
			$('#divDetail').html(data);
			calculatetotal();
			/*bootbox.alert("Producto Agregado");
            setTimeout(function () {
                $(IDFORMBUSQUEDA + '{{ $entidad }} :input[id="nombre"]').focus();
            },2000) */
			//var totalpedido = $('#totalpedido').val();
			//$('#total').val(totalpedido);
		});
	}
}

function guardarHistoria (entidad, idboton) {
	var idformulario = IDFORMMANTENIMIENTO + entidad;
	var data         = submitForm(idformulario);
	var respuesta    = '';
	var btn = $(idboton);
	btn.button('loading');
	data.done(function(msg) {
		respuesta = msg;
	}).fail(function(xhr, textStatus, errorThrown) {
		respuesta = 'ERROR';
	}).always(function() {
		btn.button('reset');
		if(respuesta === 'ERROR'){
		}else{
		  //alert(respuesta);
            var dat = JSON.parse(respuesta);
			if (dat[0]!==undefined && (dat[0].respuesta=== 'OK')) {
				cerrarModal();
                //$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="historia_id"]').val(dat[0].id);
                //$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="numero_historia"]').val(dat[0].historia);
                //$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="person_id"]').val(dat[0].person_id);
                //$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="nombrepersona"]').val(dat[0].paciente);
                $('#person_id').val(dat[0].person_id);
				$('#nombrepersona').val(dat[0].paciente);
				cerrarModal();
                var tipoventa = $('#tipoventa').val();
				if (tipoventa == 'N') {
					$('#nombreproducto').focus();
				}else{
					//$('#conveniofarmacia').focus();
					abrirconvenios();
				}
			} else {
				mostrarErrores(respuesta, idformulario, entidad);
			}
		}
	});
}

function guardarEmpresa (entidad, idboton) {
	var idformulario = IDFORMMANTENIMIENTO + entidad;
	var data         = submitForm(idformulario);
	var respuesta    = '';
	var btn = $(idboton);
	btn.button('loading');
	data.done(function(msg) {
		respuesta = msg;
	}).fail(function(xhr, textStatus, errorThrown) {
		respuesta = 'ERROR';
	}).always(function() {
		btn.button('reset');
		if(respuesta === 'ERROR'){
		}else{
		  //alert(respuesta);
            var dat = JSON.parse(respuesta);
			if (dat[0]!==undefined && (dat[0].respuesta=== 'OK')) {
				cerrarModal();
                $('#empresa_id').val(dat[0].empresa_id);
				$('#nombrepersona').val(dat[0].nombre);
				cerrarModal();
                var tipoventa = $('#tipoventa').val();
				if (tipoventa == 'N') {
					$('#nombreproducto').focus();
				}else{
					//$('#conveniofarmacia').focus();
					abrirconvenios();
				}
			} else {
				mostrarErrores(respuesta, idformulario, entidad);
			}
		}
	});
}

var contador=0;
function guardarVenta (entidad, idboton, entidad2) {
	var total = $(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="totalventa"]').val();
	var mensaje = '<h3 align = "center">Total = '+total+'</h3>';
	/*if (typeof mensajepersonalizado != 'undefined' && mensajepersonalizado !== '') {
		mensaje = mensajepersonalizado;
	}*/
	if($(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="nombrepersona"]').val()==""){
		alert("Debe agregar el nombre del cliente");
		return false;
	}
	if($(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="documento"]').val()=="4" && $(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="empresa_id"]').val()==""){
		alert("Debe seleccionar una empresa para la factura");
		return false;
	}
	if($(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="formapago"]').val()=="T" && $(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="nroref"]').val()==""){
		alert("Debe agregar el nro de operacion de la tarjeta");
		return false;
	}
	
	bootbox.confirm({
		message : mensaje,
		buttons: {
			'cancel': {
				label: 'Cancelar',
				className: 'btn btn-default btn-sm'
			},
			'confirm':{
				label: 'Aceptar',
				className: 'btn btn-success btn-sm'
			}
		}, 
		callback: function(result) {
			if (result && contador==0) {
				contador=1;
				var idformulario = IDFORMMANTENIMIENTO + entidad;
				var data         = submitForm(idformulario);
				var respuesta    = '';
				var listar       = 'NO';
				
				var btn = $(idboton);
				btn.button('loading');
				data.done(function(msg) {
					respuesta = msg;
				}).fail(function(xhr, textStatus, errorThrown) {
					respuesta = 'ERROR';
					contador=0;
				}).always(function() {
					contador=0;
					btn.button('reset');
					if(respuesta === 'ERROR'){
					}else{
						var dat = JSON.parse(respuesta);
			            if(dat[0]!==undefined){
			                resp=dat[0].respuesta;    
			            }else{
			                resp='VALIDACION';
			            }
			            
						if (resp === 'OK') {
							cerrarModal();
			                buscarCompaginado('', 'Accion realizada correctamente', entidad, 'OK');
			                /*if(dat[0].pagohospital!="0"){
			                    window.open('/juanpablo/ticket/pdfComprobante?ticket_id='+dat[0].ticket_id,'_blank')
			                }else{
			                    window.open('/juanpablo/ticket/pdfPrefactura?ticket_id='+dat[0].ticket_id,'_blank')
			                }*/
			                //alert('hola');
			                if (dat[0].ind == 1) {
			                	window.open('/juanpablo/venta/pdfComprobante?venta_id='+dat[0].venta_id+'&guia='+dat[0].guia,'_blank');
			                	window.open('/juanpablo/venta/pdfComprobante?venta_id='+dat[0].second_id+'&guia='+dat[0].guia,'_blank');
			                }else{
			                	window.open('/juanpablo/venta/pdfComprobante?venta_id='+dat[0].venta_id+'&guia='+dat[0].guia,'_blank');
			                }
			                
						} else if(resp === 'ERROR') {
							alert(dat[0].msg);
						} else {
							mostrarErrores(respuesta, idformulario, entidad);
						}
					}
				});
			};
		}            
	}).find("div.modal-content").addClass("bootboxConfirmWidth");
	setTimeout(function () {
		if (contadorModal !== 0) {
			$('.modal' + (contadorModal-1)).css('pointer-events','auto');
			$('body').addClass('modal-open');
		}
	},2000);


	
}

function validarFormaPago(formapago){
	if(formapago=="T"){
		$(".tarjeta").css("display","");
	}else{
		$(".tarjeta").css("display","none");
	}
}

validarFormaPago($("#formapago").val());
</script>