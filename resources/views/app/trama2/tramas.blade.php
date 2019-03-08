<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header">
                    <div class="row">
                        <div class="col-xs-12">
                            {!! Form::open(['method' => 'GET' ,'onsubmit' => 'return false;', 'class' => 'form-inline', 'role' => 'form', 'autocomplete' => 'off']) !!}
                            <div class="form-group">
                                {!! Form::label('fecha_trama', 'Fecha:') !!}
                                <input class="form-control input-xs" id="fecha_trama" name="fecha_trama" type="month" value="{{date('Y-m')}}">
                            </div>
                            {!! Form::button('<i class="glyphicon glyphicon-download-alt"></i> Descargar', array('class' => 'btn btn-success btn-xs', 'id' => 'btnDescargar', 'onclick' => 'descargarZipTramas();', 'disabled' => 'disabled')) !!}
                            {!! Form::close() !!}
                        </div>
                    </div>                       
                </div>
                <!-- /.box-header -->
                <div class="box-body" id="listadoCuentasporpagar">
                    <div class="table-responsive">
                        <table id="example1" class="table table-bordered table-striped table-condensed table-hover">
                            <thead>
                                <tr>
                                    <th class="text-center" width="5%">N°</th>
                                    <th class="text-center" width="5%"><input id="checkTodo" value="0" onclick="checkTodo();" type="checkbox"></th>
                                    <th class="text-center" width="8%">Código</th>
                                    <th class="text-center" width="67%">Nombre Formato</th>
                                    <th class="text-center" width="15%">Operaciones</th>
                                </tr>
                            </thead>
                                <tr>
                                    <td class="text-center">1</td>
                                    <td class="text-center"><input class="checkParcial 01" value="TAA0" type="checkbox"></td>
                                    <td class="text-center">TAA0</td>
                                    <td>Tabla Agregada A - Reporte de Recursos de Salud</td>
                                    <td class="text-center"><button class="btn btn-primary btn-xs" onclick='modal("tramas2", "Establecer Datos de la Tabla TAA0");'><i class="fa fa-edit"></i> Establecer</button></td>
                                </tr>
                                <tr>
                                    <td class="text-center">2</td>
                                    <td class="text-center"><input class="checkParcial 02" value="TAB1" type="checkbox"></td>
                                    <td class="text-center">TAB1</td>
                                    <td>Tabla Agregada B1 - Reporte Consolidado de Producción Asistencial en Consulta Ambulatoria</td>
                                    <td class="text-center">-</td>
                                </tr>
                                <tr>
                                    <td class="text-center">3</td>
                                    <td class="text-center"><input class="checkParcial 03" value="TAB2" type="checkbox"></td>
                                    <td class="text-center">TAB2</td>
                                    <td>Tabla Agregada B2 - Reporte Consolidado de Morbilidad en Consulta Ambulatoria</td>
                                    <td class="text-center">-</td>
                                </tr>
                                <tr>
                                    <td class="text-center">4</td>
                                    <td class="text-center"><input class="checkParcial 04" value="TAC1" type="checkbox"></td>
                                    <td class="text-center">TAC1</td>
                                    <td>Tabla Agregada C1 - Reporte Consolidado de Producción Asistencial en Emergencia</td>
                                    <td class="text-center">-</td>
                                </tr>
                                <tr>
                                    <td class="text-center">5</td>
                                    <td class="text-center"><input class="checkParcial 05" value="TAC2" type="checkbox"></td>
                                    <td class="text-center">TAC2</td>
                                    <td>Tabla Agregada C2 - Reporte Consolidado de Morbilidad en Emergencia</td>
                                    <td class="text-center">-</td>
                                </tr>
                                <tr>
                                    <td class="text-center">6</td>
                                    <td class="text-center"><input class="checkParcial 06" value="TAD1" type="checkbox"></td>
                                    <td class="text-center">TAD1</td>
                                    <td>Tabla Agregada D1 - Reporte Consolidado de Producción Asistencial en Hospitalización</td>
                                    <td class="text-center">-</td>
                                </tr>
                                <tr>
                                    <td class="text-center">7</td>
                                    <td class="text-center"><input class="checkParcial 07" value="TAD2" type="checkbox"></td>
                                    <td class="text-center">TAD2</td>
                                    <td>Tabla Agregada D2 - Reporte Consolidado de Morbilidad en Hospitalización</td>
                                    <td class="text-center">-</td>
                                </tr>
                                <tr>
                                    <td class="text-center">8</td>
                                    <td class="text-center"><input class="checkParcial 08" value="TAE0" type="checkbox"></td>
                                    <td class="text-center">TAE0</td>
                                    <td>Tabla Agregada E - Reporte Consolidado de Partos</td>
                                    <td class="text-center">-</td>
                                </tr>
                                <tr>
                                    <td class="text-center">9</td>
                                    <td class="text-center"><input class="checkParcial 09" value="TAF0" type="checkbox"></td>
                                    <td class="text-center">TAF0</td>
                                    <td>Tabla Agregada F - Reporte Consolidado de Eventos bajo Vigilancia Institucional</td>
                                    <td class="text-center">-</td>
                                </tr>
                                <tr>
                                    <td class="text-center">10</td>
                                    <td class="text-center"><input class="checkParcial 10" value="TAG0" type="checkbox"></td>
                                    <td class="text-center">TAG0</td>
                                    <td>Tabla Agregada G - Reporte Consolidado de Producción Asistencial de Procedimientos</td>
                                    <td class="text-center">-</td>
                                </tr>
                                <tr>
                                    <td class="text-center">11</td>
                                    <td class="text-center"><input class="checkParcial 11" value="TAH0" type="checkbox"></td>
                                    <td class="text-center">TAH0</td>
                                    <td>Tabla Agregada H - Reporte Consolidado de Producción Asistencial de Intervenciones Quirúrgicas</td>
                                    <td class="text-center">-</td>
                                </tr>
                                <tr>
                                    <td class="text-center">12</td>
                                    <td class="text-center"><input class="checkParcial 12" value="TAI0" type="checkbox"></td>
                                    <td class="text-center">TAI0</td>
                                    <td>Tabla I Referencias</td>
                                    <td class="text-center">-</td>
                                </tr>
                                <tr>
                                    <td class="text-center">13</td>
                                    <td class="text-center"><input class="checkParcial 13" value="TAJ0" type="checkbox"></td>
                                    <td class="text-center">TAJ0</td>
                                    <td>Tabla Agregada J - Reporte Consilidado de Programación Asistencial</td>
                                    <td class="text-center">-</td>
                                </tr>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script type="text/javascript">
    function checkTodo() {
        var check = $('#checkTodo');
        if(check.val() == '0') {
            check.val('1');
            $('.checkParcial').prop('checked', true);            
        } else {
            check.val('0');
            $('.checkParcial').prop('checked', false);
        }   
        habilitarBoton();     
    }

    $(document).on('click', '.checkParcial', function() {
        habilitarBoton();
    });

    function habilitarBoton() {
        var descargar = false;
        var totalCkecks = 0;
        var check = $('#checkTodo');
        $('.checkParcial').each(function() {
            if($(this).prop('checked')) {
                descargar = true;
                totalCkecks++;
            }
        });
        if(descargar) {
            $('#btnDescargar').removeAttr('disabled');
        } else {
            $('#btnDescargar').attr('disabled', 'disabled');
        }
        if(totalCkecks == 13) {
            check.val('1');
            check.prop('checked', true);
        } else {
            check.val('0');
            check.prop('checked', false);
        }
        return descargar;
    }

    function descargarZipTramas() {
        var descargar = false;
        $('.checkParcial').each(function() {
            if($(this).prop('checked')) {
                descargar = true;
            }
        });
        if(descargar) {
            if($('.01').prop('checked')) {
                window.open('trama2/descargarZipTramas?fecha_trama=' + $('#fecha_trama').val() + '&trama=TAA0');
            } 
            if($('.02').prop('checked')) {
                window.open('trama2/descargarZipTramas?fecha_trama=' + $('#fecha_trama').val() + '&trama=TAB1');
            }
            if($('.03').prop('checked')) {
                window.open('trama2/descargarZipTramas?fecha_trama=' + $('#fecha_trama').val() + '&trama=TAB2');
            } 
            if($('.04').prop('checked')) {
                window.open('trama2/descargarZipTramas?fecha_trama=' + $('#fecha_trama').val() + '&trama=TAC1');
            }
            if($('.05').prop('checked')) {
                window.open('trama2/descargarZipTramas?fecha_trama=' + $('#fecha_trama').val() + '&trama=TAC2');
            } 
            if($('.06').prop('checked')) {
                window.open('trama2/descargarZipTramas?fecha_trama=' + $('#fecha_trama').val() + '&trama=TAD1');
            }
            if($('.07').prop('checked')) {
                window.open('trama2/descargarZipTramas?fecha_trama=' + $('#fecha_trama').val() + '&trama=TAD2');
            } 
            if($('.08').prop('checked')) {
                window.open('trama2/descargarZipTramas?fecha_trama=' + $('#fecha_trama').val() + '&trama=TAE0');
            }
            if($('.09').prop('checked')) {
                window.open('trama2/descargarZipTramas?fecha_trama=' + $('#fecha_trama').val() + '&trama=TAF0');
            } 
            if($('.10').prop('checked')) {
                window.open('trama2/descargarZipTramas?fecha_trama=' + $('#fecha_trama').val() + '&trama=TAG0');
            }
            if($('.11').prop('checked')) {
                window.open('trama2/descargarZipTramas?fecha_trama=' + $('#fecha_trama').val() + '&trama=TAH0');
            } 
            if($('.12').prop('checked')) {
                window.open('trama2/descargarZipTramas?fecha_trama=' + $('#fecha_trama').val() + '&trama=TAI0');
            }
            if($('.13').prop('checked')) {
                window.open('trama2/descargarZipTramas?fecha_trama=' + $('#fecha_trama').val() + '&trama=TAJ0');
            }                               
        } else {
            alert('Tienes que seleccionar al menos una Trama.');
        }
    }
</script>