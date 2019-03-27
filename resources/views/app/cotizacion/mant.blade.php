<?php 
if($cotizacion == null) {
    $fecha = date('Y-m-d');
    $tipo = 'A';
    $plan = '';
    $plan_id = '';
    $codigo = '';
    $total = '';
} else {
    $fecha = $cotizacion->fecha;
    $tipo = $cotizacion->tipo;
    $plan = $cotizacion->plan->nombre;
    $plan_id = $cotizacion->plan->id;
    $codigo = $cotizacion->codigo;
    $total = number_format($cotizacion->total, 2);
}
?>
<style>
.tr_hover{
    color:red;
}
.form-group{
    margin-bottom: 8px !important;
}
</style>
<div id="divMensajeError{!! $entidad !!}"></div>
{!! Form::model($cotizacion, $formData) !!}    
    {!! Form::hidden('listar', $listar, array('id' => 'listar')) !!}
    {!! Form::hidden('listServicio', null, array('id' => 'listServicio')) !!}
    <div class="row">
        {{--<div class="col-lg-6 col-md-6 col-sm-6">--}}
        <div class="col-lg-12 col-md-12 col-sm-12">
            <div class="form-group">
                {!! Form::label('fecharegistro', 'Fecha:', array('class' => 'col-lg-1 col-md-1 col-sm-1 control-label')) !!}
                <div class="col-lg-3 col-md-3 col-sm-3">
                    {!! Form::date('fecharegistro', $fecha, array('class' => 'form-control input-xs', 'id' => 'fecharegistro')) !!}
                </div>
                {!! Form::label('tiporegistro', 'Tipo:', array('class' => 'col-lg-1 col-md-1 col-sm-1 control-label')) !!}
                <div class="col-lg-3 col-md-3 col-sm-3">
                    <select name="tiporegistro" class='form-control input-xs' id='tiporegistro'>
                        <option value="A">AMBULATORIO</option>
                        <option value="H">HOSPITALARIO</option>
                    </select>
                </div>
                {!! Form::label('codigoregistro', 'Código:', array('class' => 'col-lg-1 col-md-1 col-sm-1 control-label')) !!}
                <div class="col-lg-3 col-md-3 col-sm-3">
                    {!! Form::text('codigoregistro', $codigo, array('class' => 'form-control input-xs', 'id' => 'codigoregistro')) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('plan', 'Plan:', array('class' => 'col-lg-1 col-md-1 col-sm-1 control-label')) !!}
                <div class="col-lg-4 col-md-4 col-sm-4">
                    {!! Form::text('plan', $plan, array('class' => 'form-control input-xs', 'id' => 'plan')) !!}
                    {!! Form::hidden('plan_id', $plan_id, array('id' => 'plan_id')) !!}
                </div>
                {!! Form::label('titulo', 'Referencia:', array('class' => 'col-lg-1 col-md-1 col-sm-1 control-label')) !!}
                <div class="col-lg-6 col-md-6 col-sm-6">
                    {!! Form::text('titulo', $codigo, array('class' => 'form-control input-xs', 'id' => 'titulo')) !!}
                </div>
            </div>
            <div class="form-group">
                <div class="col-lg-12 col-md-12 col-sm-12 text-right">
                    {!! Form::button('<i class="fa fa-check fa-lg"></i> '.$boton, array('class' => 'btn btn-success btn-sm', 'id' => 'btnGuardar', 'onclick' => '$(\'#listServicio\').val(carro);$(\'#movimiento_id\').val(carroDoc);guardarPago(\''.$entidad.'\', this);')) !!}
                    {!! Form::button('<i class="fa fa-exclamation fa-lg"></i> Cancelar', array('class' => 'btn btn-warning btn-sm', 'id' => 'btnCancelar'.$entidad, 'onclick' => 'cerrarModal();')) !!}
                </div>
            </div>
        </div>
    </div>
    <div class="box">
        <div class="box-header">
            <h2 class="box-title col-lg-4 col-md-4 col-sm-4">CABECERA <button type="button" class="btn btn-xs btn-info" title="Agregar Detalle" onclick="seleccionarServicioOtro();"><i class="fa fa-plus"></i></button></h2>
        </div>
        <div class="box-body" style="max-height: 300px;overflow: auto;">
            <table class="table table-condensed table-border" id="tbDetalle">
                <thead>
                    <th class="text-center" width="5%">#</th>
                    <th class="text-center" width="34%">Conceptos</th>
                    <th class="text-center" width="8%">Cantidad</th>
                    <th class="text-center" width="8%">%</th>
                    <th class="text-center" width="8%">S/.</th>
                    <th class="text-center" width="8%">Unidad</th>
                    <th class="text-center" width="8%">Factor</th>
                    <th class="text-center" width="8%">Monto Total</th>
                    <th class="text-center" width="8%">Por Facturar</th>
                    <th class="text-center" width="5%" colspan="2"></th>
                </thead>
                <tbody>
                @if($cotizacion !== NULL) 
                    @foreach($cotizacion->detalles as $detalle)
                        <tr id='tr{{ $detalle->id }}'>
                            <td>
                                <input type='hidden' id='txtIdTipoServicio{{ $detalle->id }}' name='txtIdTipoServicio{{ $detalle->id }}' value='0' />
                                <input type='text' class='form-control input-xs txtareaa' id='txtServicio{{ $detalle->id }}' name='txtServicio{{ $detalle->id }}' value="{{ $detalle->descripcion }}" />
                            </td>
                            <td>
                                <a href='#' onclick="quitarServicio('{{ $detalle->id }}')"><i class='fa fa-minus-circle' title='Quitar' width='20px' height='20px'></i>
                            </td>
                        </tr>
                    @endforeach
                @endif
                </tbody>
                <tfoot>
                    <th class="text-right"></th>
                    <th class="text-right"></th>
                    <th class="text-right"></th>
                    <th class="text-right"></th>
                    <th class="text-right"></th>
                    <th class="text-right"></th>
                    <th class="text-right">Total</th>
                    <th>{!! Form::text('total', $total, array('class' => 'form-control input-xs', 'id' => 'total', 'size' => 3, 'style' => 'width: 100%;')) !!}</th>
                    <th>{!! Form::text('total', $total, array('class' => 'form-control input-xs', 'id' => 'total', 'size' => 3, 'style' => 'width: 100%;')) !!}</th>
                    <th class="text-right"></th>
                    <th class="text-right"></th>
                </tfoot>
            </table>
        </div>
    </div>
{!! Form::close() !!}
<script type="text/javascript">
var valorbusqueda="";
$(document).ready(function() {
    configurarAnchoModal('1400');
    init(IDFORMMANTENIMIENTO+'{!! $entidad !!}', 'B', '{!! $entidad !!}');
    $(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="total"]').inputmask('decimal', { radixPoint: ".", autoGroup: true, groupSeparator: "", groupSize: 3, digits: 2 });
    $(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="totalboleta"]').inputmask('decimal', { radixPoint: ".", autoGroup: true, groupSeparator: "", groupSize: 3, digits: 2 });
    $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="ruc"]').inputmask("99999999999");
    $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="numeroventa"]').inputmask("99999999");
    $(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="deducible"]').inputmask('decimal', { radixPoint: ".", autoGroup: true, groupSeparator: ",", groupSize: 3, digits: 2 });
    $(IDFORMMANTENIMIENTO + '{{ $entidad }} :input[id="coa"]').inputmask('decimal', { radixPoint: ".", autoGroup: true, groupSeparator: ",", groupSize: 3, digits: 2 });
    $('#tiporegistro').val('{{ $tipo }}');
    var personas = new Bloodhound({
        datumTokenizer: function (d) {
            return Bloodhound.tokenizers.whitespace(d.value);
        },
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        limit:10,
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
                        dni:movie.dni,
                        plan_id:movie.plan_id,
                        plan:movie.plan,
                        coa:movie.coa,
                        deducible:movie.deducible,
                        ruc:movie.ruc,
                        direccion:movie.direccion,
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
        $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="historia_id"]').val(datum.id);
        $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="numero_historia"]').val(datum.historia);
        $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="paciente"]').val(datum.value);
        $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="dni"]').val(datum.dni);
        $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="person_id"]').val(datum.person_id);
        if(datum.plan_id>0){
            $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="plan"]').val(datum.plan);
            $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="coa"]').val(datum.coa);
            $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="deducible"]').val(datum.deducible);
            $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="plan_id"]').val(datum.plan_id);
            $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="ruc"]').val(datum.ruc);
            $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="direccion"]').val(datum.direccion);
        }
        agregarDetallePrefactura(datum.person_id);
    });

    var personas2 = new Bloodhound({
        datumTokenizer: function (d) {
            return Bloodhound.tokenizers.whitespace(d.value);
        },
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        limit:10,
        remote: {
            url: 'historia/historiaautocompletar/%QUERY',
            filter: function (personas) {
                return $.map(personas, function (movie) {
                    return {
                        value: movie.value,
                        id: movie.id,
                        historia: movie.numero,
                        person_id:movie.person_id,
                        tipopaciente:movie.tipopaciente,
                        dni:movie.dni,
                        plan_id:movie.plan_id,
                        plan:movie.plan,
                        coa:movie.coa,
                        deducible:movie.deducible,
                        ruc:movie.ruc,
                        direccion:movie.direccion,
                    };
                });
            }
        }
    });
    personas2.initialize();
    $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="numero_historia"]').typeahead(null,{
        displayKey: 'historia',
        source: personas2.ttAdapter()
    }).on('typeahead:selected', function (object, datum) {
        $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="historia_id"]').val(datum.id);
        $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="numero_historia"]').val(datum.historia);
        $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="paciente"]').val(datum.value);
        $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="dni"]').val(datum.dni);
        $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="person_id"]').val(datum.person_id);
        $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="paciente"]').val(datum.value);
        if(datum.plan_id>0){
            $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="plan"]').val(datum.plan);
            $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="coa"]').val(datum.coa);
            $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="deducible"]').val(datum.deducible);
            $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="plan_id"]').val(datum.plan_id);
            $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="ruc"]').val(datum.ruc);
            $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="direccion"]').val(datum.direccion);
        }
        agregarDetallePrefactura(datum.person_id);
    });    
    
    var planes = new Bloodhound({
        datumTokenizer: function (d) {
            return Bloodhound.tokenizers.whitespace(d.value);
        },
        limit: 10,
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        remote: {
            url: 'plan/planautocompletar/%QUERY',
            filter: function (planes) {
                return $.map(planes, function (movie) {
                    return {
                        value: movie.value,
                        id: movie.id,
                        coa: movie.coa,
                        deducible:movie.deducible,
                        ruc:movie.ruc,
                        direccion:movie.direccion,
                    };
                });
            }
        }
    });
    planes.initialize();
    $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="plan"]').typeahead(null,{
        displayKey: 'value',
        source: planes.ttAdapter()
    }).on('typeahead:selected', function (object, datum) {
        $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="plan"]').val(datum.value);
        $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="coa"]').val(datum.coa);
        $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="deducible"]').val(datum.deducible);
        $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="plan_id"]').val(datum.id);
        $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="ruc"]').val(datum.ruc);
        $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="direccion"]').val(datum.direccion);

    });

    $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="paciente"]').focus();

    $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="descripcion"]').on( 'keydown', function () {
        var e = window.event; 
        var keyc = e.keyCode || e.which;
        if(this.value.length>1 && keyc == 13 && this.value!=valorbusqueda){
            buscarServicio(this.value);
            valorbusqueda=this.value;
            this.focus();
            return false;
        }
        if(keyc == 38 || keyc == 40 || keyc == 13) {
            var tabladiv='tablaServicio';
            var child = document.getElementById(tabladiv).rows;
            var indice = -1;
            var i=0;
            $('#tablaServicio tr').each(function(index, elemento) {
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
            });      
            // return
            if(keyc == 13) {                        
                 if(indice != -1){
                    var seleccionado = '';           
                    if(child[indice].id) {
                       seleccionado = child[indice].id;
                    } else {
                       seleccionado = child[indice].id;
                    }               
                    seleccionarServicio(seleccionado);
                }
            } else {
                // abajo
                if(keyc == 40) {
                    if(indice == (child.length - 1)) {
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
            }
        }
    });
}); 

function guardarPago (entidad, idboton) {
    var band=true;
    var msg="";
    //var total2=0;var cant=0;var pv=0;var total=0;
    /*for(c=0; c < carro.length; c++){
        cant=parseFloat($("#txtCantidad"+carro[c]).val());
        pv=parseFloat($("#txtPrecio"+carro[c]).val());
        total=Math.round((pv*cant) * 100) / 100;
        $("#txtTotal"+carro[c]).val(total);   
        total2=Math.round((total2+total) * 100) / 100;        
    }*/
    if(carro.length==0){
        band = false;
        msg += " *Debes escribir al menos un detalle \n";    
    }

    if($('#plan_id').val()==""){
        band = false;
        msg += " *Debes Seleccionar un Plan \n";    
    }

    //$("#total").val(total2);
    if($(".txtareaa").val()==""){
        band = false;
        msg += " *Se debe agregar una descripcion \n"; 
        $(this).focus();   
    }

    if($("#person_id").val()==""){
        band = false;
        msg += " *No se selecciono un paciente \n";    
    }
    for(c=0; c < carro.length; c++){
        if($("#txtIdMedico"+carro[c]).val()==0){
            band = false;
            msg += " *Debe seleccionar medico \n";                        
        }
    }
    if(band){
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
                if(dat[0]!==undefined){
                    resp=dat[0].respuesta;    
                }else{
                    resp='VALIDACION';
                }
                
                if (resp === 'OK') {
                    cerrarModal();
                    buscarCompaginado('', 'Accion realizada correctamente', entidad, 'OK');
                } else if(resp === 'ERROR') {
                    alert(dat[0].msg);
                } else {
                    mostrarErrores(respuesta, idformulario, entidad);
                }
            }
        });
    }else{
        alert("Corregir los sgtes errores: \n"+msg);
    }
}

var valorinicial="";
function buscarServicio(valor){
    $.ajax({
        type: "POST",
        url: "cotizacion/buscarservicio",
        data: "idtiposervicio="+$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="tiposervicio"]').val()+"&descripcion="+$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="descripcion"]').val()+"&plan_id="+$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="plan_id"]').val()+"&_token="+$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[name="_token"]').val(),
        success: function(a) {
            datos=JSON.parse(a);
            $("#divBusqueda").html("<table class='table table-bordered table-condensed table-hover' border='1' id='tablaServicio'><thead><tr><th class='text-center'>TIPO</th><th class='text-center'>CODIGO</th><th class='text-center'>SERVICIO</th><th class='text-center'>P. UNIT.</tr></thead></table>");
            var pag=parseInt($("#pag").val());
            var d=0;
            for(c=0; c < datos.length; c++){
                var a="<tr id='"+datos[c].idservicio+"' onclick=\"seleccionarServicio('"+datos[c].idservicio+"')\"><td align='center' style='font-size:12px'>"+datos[c].tiposervicio+"</td><td style='font-size:12px'>"+datos[c].codigo+"</td><td style='font-size:12px'>"+datos[c].servicio+"</td><td align='right' style='font-size:12px'>"+datos[c].precio+"</td></tr>";
                $("#tablaServicio").append(a);           
            }
            $('#tablaServicio').DataTable({
                "scrollY":        "250px",
                "scrollCollapse": true,
                "paging":         false,
                "columnDefs": [
                    { "width": "80%", "targets": 2 }
                  ]
            });
            $('#tablaServicio_filter').css('display','none');
            $("#tablaServicio_info").css("display","none");
        }
    });
}

var carro = new Array();
var carrodetalles = new Array();
var carroDoc = new Array();
var copia = new Array();
function seleccionarServicio(idservicio){
    var band=true;
    if(band){
        $.ajax({
            type: "POST",
            url: "cotizacion/seleccionarservicio",
            //data: "idservicio="+idservicio+"&plan_id="+$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="plan_id"]').val()+"&_token="+$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[name="_token"]').val(),
            data: "idservicio="+idservicio+"&plan_id=5&_token="+$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[name="_token"]').val(),
            success: function(a) {
                datos=JSON.parse(a);
                var c=0;
                $("#tbDetalle").append("<tr id='tr"+datos[c].idservicio+"'><td><input type='hidden' id='txtIdTipoServicio"+datos[c].idservicio+"' name='txtIdTipoServicio"+datos[c].idservicio+"' value='"+datos[c].idtiposervicio+"' /><input type='hidden' id='txtIdServicio"+datos[c].idservicio+"' name='txtIdServicio"+datos[c].idservicio+"' value='"+datos[c].id+"' /><input type='text' data='numero' style='width: 40px;' class='form-control input-xs' id='txtCantidad"+datos[c].idservicio+"' name='txtCantidad"+datos[c].idservicio+"' value='1' size='3' onkeydown=\"if(event.keyCode==13){calcularTotal()}\" onblur=\"calcularTotalItem('"+datos[c].idservicio+"')\" /></td>"+
                    "<td><input type='checkbox' id='chkCopiar"+datos[c].idservicio+"' onclick=\"checkMedico(this.checked,'"+datos[c].idservicio+"')\" /></td>"+
                    "<td><input type='text' class='form-control input-xs' id='txtMedico"+datos[c].idservicio+"' name='txtMedico"+datos[c].idservicio+"' /><input type='hidden' id='txtIdMedico"+datos[c].idservicio+"' name='txtIdMedico"+datos[c].idservicio+"' value='0' /></td>"+
                    "<td align='left'>"+datos[c].tiposervicio+"</td><td>"+datos[c].codigo+"</td><td><textarea style='resize: none;' class='form-control input-xs txtareaa' id='txtServicio"+datos[c].idservicio+"' name='txtServicio"+datos[c].idservicio+"'>"+datos[c].servicio+"</textarea></td>"+
                    "<td><input type='hidden' id='txtPrecio2"+datos[c].idservicio+"' name='txtPrecio2"+datos[c].idservicio+"' value='"+datos[c].precio+"' /><input type='text' size='5' class='form-control input-xs' data='numero' id='txtPrecio"+datos[c].idservicio+"' style='width: 60px;' name='txtPrecio"+datos[c].idservicio+"' value='"+datos[c].precio+"' onkeydown=\"if(event.keyCode==13){calcularTotalItem('"+datos[c].idservicio+"')}\" onblur=\"calcularTotalItem('"+datos[c].idservicio+"')\" /></td>"+
                    "<td><input type='text' size='5' class='form-control input-xs' data='numero' id='txtDias"+datos[c].idservicio+"' style='width: 60px;' name='txtDias"+datos[c].idservicio+"' value='0' onkeydown=\"if(event.keyCode==13){calcularTotalItem('"+datos[c].idservicio+"')}\" onblur=\"calcularTotalItem('"+datos[c].idservicio+"')\" style='width:50%' /></td>"+
                    "<td><input type='text' size='5' class='form-control input-xs' data='numero' style='width: 60px;' id='txtPorcentajeMedico"+datos[c].idservicio+"' name='txtPorcentajeMedico"+datos[c].idservicio+"' value='"+datos[c].preciomedico+"' onkeyup=\"calcularPorcentajeMedico('"+datos[c].idservicio+"')\" /></td>"+
                    "<td><input type='text' size='5' class='form-control input-xs' data='numero' style='width: 60px;' id='txtPrecioMedico"+datos[c].idservicio+"' name='txtPrecioMedico"+datos[c].idservicio+"' value='"+datos[c].preciomedico+"' onblur=\"calcularTotalItem('"+datos[c].idservicio+"');$('#descripcion').focus();\" /></td>"+
                    "<td><input type='text' readonly='' data='numero' class='form-control input-xs' size='5' name='txtTotal"+datos[c].idservicio+"' style='width: 60px;' id='txtTotal"+datos[c].idservicio+"' value='"+datos[c].precio+"' /></td>"+
                    "<td><a href='#' onclick=\"quitarServicio('"+datos[c].idservicio+"')\"><i class='fa fa-minus-circle' title='Quitar' width='20px' height='20px'></i></td></tr>");
                carro.push(datos[c].idservicio);
                $(':input[data="numero"]').inputmask('decimal', { radixPoint: ".", autoGroup: true, groupSeparator: "", groupSize: 3, digits: 2 });
                eval("var planes"+datos[c].idservicio+" = new Bloodhound({"+
                    "datumTokenizer: function (d) {"+
                        "return Bloodhound.tokenizers.whitespace(d.value);"+
                    "},"+
                    "limit: 10,"+
                    "queryTokenizer: Bloodhound.tokenizers.whitespace,"+
                    "remote: {"+
                        "url: 'medico/medicoautocompletar/%QUERY',"+
                        "filter: function (planes"+datos[c].idservicio+") {"+
                            "return $.map(planes"+datos[c].idservicio+", function (movie) {"+
                                "return {"+
                                    "value: movie.value,"+
                                    "id: movie.id,"+
                                "};"+
                            "});"+
                        "}"+
                    "}"+
                "});"+
                "planes"+datos[c].idservicio+".initialize();"+
                "$('#txtMedico"+datos[c].idservicio+"').typeahead(null,{"+
                    "displayKey: 'value',"+
                    "source: planes"+datos[c].idservicio+".ttAdapter()"+
                "}).on('typeahead:selected', function (object, datum) {"+
                    "$('#txtMedico"+datos[c].idservicio+"').val(datum.value);"+
                    "$('#txtIdMedico"+datos[c].idservicio+"').val(datum.id);"+
                    "copiarMedico("+datos[c].idservicio+");"+
                "});");
                $("#txtMedico"+datos[c].idservicio).focus();  
                if($(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="editarprecio"]').val()=='S'){
                    editarPrecio(true);
                }             
            }
        });
    }else{
        $('#txtMedico'+idservicio).focus();
    }
}

function seleccionarServicioOtro(){
    var idservicio = "10"+Math.round(Math.random()*10000);
    $("#tbDetalle").append("<tbody id='tbDetalle"+idservicio+"'><tr id='trDetalle"+idservicio+"'><td>§</td><td colspan='7'><input style='font-weight:bold;text-align: center;font-size:15px;' type='text' class='form-control input-xs txtareaa' id='txtServicio"+idservicio+"' name='txtServicio"+idservicio+"' /></td>" +
        "<td><input class='form-control input-xs' type='text' id='txtFacturar" + idservicio + "' name='txtFacturar" + idservicio + "' /></td>"  + 
        "<td><a href='#' class='btn btn-danger btn-xs' onclick=\"quitarServicio2('"+idservicio+"')\"><i class='fa fa-minus-circle' title='Quitar Cabecera'></i></td><td><a class='btn btn-success btn-xs' href='#' onclick=\"seleccionarServicioOtro2('"+idservicio+"')\"><i class='fa fa-plus-circle' title='Añadir Detalle'></i></td></tr></tbody>");
    carro.push(idservicio);
    $("#txtServicio"+idservicio).focus();
}

function seleccionarServicioOtro2(idservicio){
    var idservicio2 = "10"+Math.round(Math.random()*10000);
    $("#tbDetalle" + idservicio).append("<tr id='" + idservicio + "tr"+idservicio2+"'><td>-</td><td><input type='text' class='form-control input-xs' id='" + idservicio + "txtServicio"+idservicio2+"' name='" + idservicio + "'txtServicio"+idservicio2+"' /></td>" + 
        "<td><input class='form-control input-xs' type='text' id='" + idservicio + "txtCantidad" + idservicio2 + "' name='" + idservicio + "txtCantidad" + idservicio2 + "' /></td>"  + 
        "<td><input class='form-control input-xs' type='text' id='" + idservicio + "txtPorcentaje" + idservicio2 + "'  name='" + idservicio + "txtPorcentaje" + idservicio2 + "' /></td>"  + 
        "<td><input class='form-control input-xs' type='text' id='" + idservicio + "txtSoles" + idservicio2 + "'  name='" + idservicio + "txtSoles" + idservicio2 + "' /></td>"  + 
        "<td><input class='form-control input-xs' type='text' id='" + idservicio + "txtUnidad" + idservicio2 + "' name='" + idservicio + "txtUnidad" + idservicio2 + "' /></td>"  + 
        "<td><input class='form-control input-xs' type='text' id='" + idservicio + "txtFactor" + idservicio2 + "' name='" + idservicio + "txtFactor" + idservicio2 + "' /></td>"  + 
        "<td><input class='form-control input-xs' type='text' id='" + idservicio + "txtTotal" + idservicio2 + "' name='" + idservicio + "txtTotal" + idservicio2 + "' /></td>"  + 
        "<td><input class='form-control input-xs' readonly='readonly' type='text' id='" + idservicio + "txtFacturar" + idservicio2 + "' name=" + idservicio + "txtFacturar" + idservicio2 + "' /></td>"  + 
        "<td><a href='#' class='btn btn-warning btn-xs' onclick=\"quitarServicio('" + idservicio + "tr"+idservicio2+"')\"><i class='fa fa-minus-circle' title='Quitar Detalle'></i></td><td></td></tr>");
    carrodetalles.push(idservicio2);
    $("#" + idservicio + "txtServicio"+idservicio2).focus();             
}

function calcularTotal(){
    /*var total2=0;
    for(c=0; c < carro.length; c++){
        var tot=parseFloat($("#txtTotal"+carro[c]).val());
        total2=Math.round((total2+tot) * 100) / 100;        
    }
    $("#total").val(total2);*/
}

function calcularTotalItem(id){
    /*var cant=parseFloat($("#txtCantidad"+id).val());
    var pv=parseFloat($("#txtPrecio"+id).val());
    var total=Math.round((pv*cant) * 100) / 100;

    $("#txtTotal"+id).val(total);   
    calcularTotal();*/
}

function calcularPorcentajeMedico(id){
    /*var e = window.event; 
    var keyc = e.keyCode || e.which;
    if(keyc==13){
        var pago = Math.round((parseFloat($("#txtCantidad"+id).val())*parseFloat($("#txtPorcentajeMedico"+id).val())*parseFloat($("#txtPrecio"+id).val())/100)*100)/100;
        $("#txtPrecioMedico"+id).val(pago);
    }*/
}

function calcularTotalItem2(id){
    /*var cant=parseFloat($("#txtCantidad"+id).val());
    var pv=parseFloat($("#txtPrecio"+id).val());
    var total=Math.round((pv*cant) * 100) / 100;
    $("#txtTotal"+id).val(total);   
    calcularTotal();*/
}

function quitarServicio(id){
    $("#"+id).remove();
    /*for(c=0; c < carro.length; c++){
        if(carro[c] == id) {
            carro.splice(c,1);
        }
    }
    calcularTotal();*/
}

function quitarServicio2(id){
    $("#tbDetalle"+id).remove();
    /*for(c=0; c < carro.length; c++){
        if(carro[c] == id) {
            carro.splice(c,1);
        }
    }
    calcularTotal();*/
}

function generarNumero(){
    $.ajax({
        type: "POST",
        url: "facturacion/generarNumero",
        data: "&serie="+$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[name="serieventa"]').val()+"&_token="+$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[name="_token"]').val(),
        success: function(a) {
            $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[name="numeroventa"]').val(a);
        }
    });
}

function Soat(check){
    if(check){
        $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="soat"]').val('S');
    }else{
        $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="soat"]').val('N');
    }
}

function Uci(check){
    if(check){
        $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="uci"]').val('S');
    }else{
        $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="uci"]').val('N');
    }
}


function Igv(check){
    if(check){
        $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="igv"]').val('S');
    }else{
        $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="igv"]').val('N');
    }
}

function checkMedico(check,idservicio){
    if(check){
        copia.push(idservicio);
    }else{
        for(c=0; c < copia.length; c++){
            if(copia[c]==idservicio){
                copia.splice(c,1);
            }
        }
        $("#txtIdMedico"+idservicio).val(0);
        $("#txtMedico"+idservicio).val("");
        $("#txtMedico"+idservicio).focus();
    }
}

function copiarMedico(idservicio){
    if($("#chkCopiar"+idservicio).is(":checked")){
        for(c=0; c < copia.length; c++){
            $("#txtIdMedico"+copia[c]).val($("#txtIdMedico"+idservicio).val());
            $("#txtMedico"+copia[c]).val($("#txtMedico"+idservicio).val());
        }
    }
}

function calcularCoaseguro(value){
    /*var e = window.event; 
    var keyc = e.keyCode || e.which;
    if(keyc==13){
        if($("#coaseguro").val()!="0" && $("#coaseguro").val()!=""){
            for(x=0; x < carro.length; x++){
                var descr = $("#txtServicio"+carro[x]).val();
                console.log(descr.search('CONSULTA'));
                if(descr.search('CONSULTA')=="-1" && descr.search('CONS')=="-1" && $("#txtIdServicio"+carro[x]).val()!="1"){
                    var precio = Math.round(parseFloat($("#txtPrecio"+carro[x]).val())*(100 - parseFloat($("#coaseguro").val())))/100;
                    $("#txtPrecio"+carro[x]).val(precio);
                    calcularTotalItem(carro[x]);
                }
            }
        }
    }*/
}

function agregarDetallePrefactura(idpersona){
    $.ajax({
        type: "POST",
        url: "facturacion/agregarDetallePrefactura",
        data: "&persona_id="+idpersona+"&_token="+$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[name="_token"]').val(),
        success: function(a) {
            datos=JSON.parse(a);
            for(c=0; c < datos.length; c++){
                $("#tbDetalle").append("<tr id='tr"+datos[c].idservicio+"'><td><input type='hidden' id='txtIdDetalle"+datos[c].idservicio+"' name='txtIdDetalle"+datos[c].idservicio+"' value='"+datos[c].iddetalle+"' /><input type='hidden' id='txtIdTipoServicio"+datos[c].idservicio+"' name='txtIdTipoServicio"+datos[c].idservicio+"' value='"+datos[c].idtiposervicio+"' /><input type='hidden' id='txtIdServicio"+datos[c].idservicio+"' name='txtIdServicio"+datos[c].idservicio+"' value='"+datos[c].id+"' /><input type='text' data='numero' style='width: 40px;' class='form-control input-xs' id='txtCantidad"+datos[c].idservicio+"' name='txtCantidad"+datos[c].idservicio+"' value='"+datos[c].cantidad+"' size='3' onkeydown=\"if(event.keyCode==13){calcularTotal()}\" onblur=\"calcularTotalItem('"+datos[c].idservicio+"')\" /></td>"+
                    "<td><input type='checkbox' id='chkCopiar"+datos[c].idservicio+"' onclick=\"checkMedico(this.checked,'"+datos[c].idservicio+"')\" /></td>"+
                    "<td><input type='text' class='form-control input-xs' id='txtMedico"+datos[c].idservicio+"' name='txtMedico"+datos[c].idservicio+"' value='"+datos[c].medico+"' /><input type='hidden' id='txtIdMedico"+datos[c].idservicio+"' name='txtIdMedico"+datos[c].idservicio+"' value='"+datos[c].medico_id+"' /></td>"+
                    "<td align='left'>"+datos[c].tiposervicio+"</td><td>"+datos[c].codigo+"</td><td><textarea style='resize: none;' class='form-control input-xs txtareaa' id='txtServicio"+datos[c].idservicio+"' name='txtServicio"+datos[c].idservicio+"'>"+datos[c].servicio+"</textarea></td>"+
                    "<td><input type='hidden' id='txtPrecio2"+datos[c].idservicio+"' name='txtPrecio2"+datos[c].idservicio+"' value='"+datos[c].precio+"' /><input type='text' size='5' class='form-control input-xs' data='numero' id='txtPrecio"+datos[c].idservicio+"' style='width: 60px;' name='txtPrecio"+datos[c].idservicio+"' value='"+datos[c].precio+"' onkeydown=\"if(event.keyCode==13){calcularTotalItem('"+datos[c].idservicio+"')}\" onblur=\"calcularTotalItem('"+datos[c].idservicio+"')\" /></td>"+
                    "<td><input type='text' size='5' class='form-control input-xs' data='numero' id='txtDias"+datos[c].idservicio+"' style='width: 60px;' name='txtDias"+datos[c].idservicio+"' value='0' onkeydown=\"if(event.keyCode==13){calcularTotalItem('"+datos[c].idservicio+")}\" onblur=\"calcularTotalItem('"+datos[c].idservicio+"')\" style='width:50%' /></td>"+
                    "<td><input type='text' size='5' class='form-control input-xs' data='numero' style='width: 60px;' id='txtPorcentajeMedico"+datos[c].idservicio+"' name='txtPorcentajeMedico"+datos[c].idservicio+"' value='"+datos[c].preciomedico+"' onkeyup=\"calcularPorcentajeMedico('"+datos[c].idservicio+"')\" /></td>"+
                    "<td><input type='text' size='5' class='form-control input-xs' data='numero' style='width: 60px;' id='txtPrecioMedico"+datos[c].idservicio+"' name='txtPrecioMedico"+datos[c].idservicio+"' value='"+datos[c].preciomedico+"' onblur=\"calcularTotalItem('"+datos[c].idservicio+"');$('#descripcion').focus();\" /></td>"+
                    "<td><input type='text' readonly='' data='numero' class='form-control input-xs' size='5' name='txtTotal"+datos[c].idservicio+"' style='width: 60px;' id='txtTotal"+datos[c].idservicio+"' value='"+datos[c].precio+"' /></td>"+
                    "<td><a href='#' onclick=\"quitarServicio('"+datos[c].idservicio+"')\"><i class='fa fa-minus-circle' title='Quitar' width='20px' height='20px'></i></td></tr>");
                carro.push(datos[c].idservicio);
                $(':input[data="numero"]').inputmask('decimal', { radixPoint: ".", autoGroup: true, groupSeparator: "", groupSize: 3, digits: 2 });
                eval("var planes"+datos[c].idservicio+" = new Bloodhound({"+
                    "datumTokenizer: function (d) {"+
                        "return Bloodhound.tokenizers.whitespace(d.value);"+
                    "},"+
                    "limit: 10,"+
                    "queryTokenizer: Bloodhound.tokenizers.whitespace,"+
                    "remote: {"+
                        "url: 'medico/medicoautocompletar/%QUERY',"+
                        "filter: function (planes"+datos[c].idservicio+") {"+
                            "return $.map(planes"+datos[c].idservicio+", function (movie) {"+
                                "return {"+
                                    "value: movie.value,"+
                                    "id: movie.id,"+
                                "};"+
                            "});"+
                        "}"+
                    "}"+
                "});"+
                "planes"+datos[c].idservicio+".initialize();"+
                "$('#txtMedico"+datos[c].idservicio+"').typeahead(null,{"+
                    "displayKey: 'value',"+
                    "source: planes"+datos[c].idservicio+".ttAdapter()"+
                "}).on('typeahead:selected', function (object, datum) {"+
                    "$('#txtMedico"+datos[c].idservicio+"').val(datum.value);"+
                    "$('#txtIdMedico"+datos[c].idservicio+"').val(datum.id);"+
                    "copiarMedico("+datos[c].idservicio+");"+
                "});");
                $("#txtMedico"+datos[c].idservicio).focus(); 
            } 
            calcularTotal();
        }
    });
}

@if($cotizacion !== NULL) 
function cargarCarro() {
    @foreach($cotizacion->detalles as $detalle)
        carro.push({{ $detalle->id }});
    @endforeach
}

cargarCarro();
@endif
</script>