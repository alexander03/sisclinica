<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header">
                    <div class="row">
                        <div class="col-xs-12">
                            {!! Form::open(['method' => 'GET' ,'onsubmit' => 'return false;', 'class' => 'form-inline', 'role' => 'form', 'autocomplete' => 'off']) !!}
                            <div class="form-group">
                                {!! Form::label('fecha', 'Fecha:') !!}
                                <input class="form-control input-xs" id="fecha" name="fecha" type="month" value="{{date('Y-m')}}">
                            </div>
                            {!! Form::button('<i class="glyphicon glyphicon-download-alt"></i> Descargar Comprimido', array('class' => 'btn btn-success btn-xs', 'id' => 'btnDescargar', 'onclick' => 'descargarTramas();', 'disabled' => 'disabled')) !!}
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
                                    <td class="text-center"><input class="checkParcial" value="0" type="checkbox"></td>
                                    <td class="text-center">TAA0</td>
                                    <td>Tabla Agregada A - Reporte de Recursos de Salud</td>
                                    <td class="text-center"><button class="btn btn-primary btn-xs" onclick='modal("tramas2", "Establecer Datos de la Tabla TAA0");'><i class="fa fa-edit"></i> Establecer</button></td>
                                </tr>
                                <tr>
                                    <td class="text-center">2</td>
                                    <td class="text-center"><input class="checkParcial" value="1" type="checkbox"></td>
                                    <td class="text-center">TAB1</td>
                                    <td>Tabla Agregada B1 - Reporte Consolidado de Producción Asistencial en Consulta Ambulatoria</td>
                                    <td class="text-center">-</td>
                                </tr>
                                <tr>
                                    <td class="text-center">3</td>
                                    <td class="text-center"><input class="checkParcial" value="2" type="checkbox"></td>
                                    <td class="text-center">TAB2</td>
                                    <td>Tabla Agregada B2 - Reporte Consolidado de Morbilidad en Consulta Ambulatoria</td>
                                    <td class="text-center">-</td>
                                </tr>
                                <tr>
                                    <td class="text-center">4</td>
                                    <td class="text-center"><input class="checkParcial" value="3" type="checkbox"></td>
                                    <td class="text-center">TAC1</td>
                                    <td>Tabla Agregada C1 - Reporte Consolidado de Producción Asistencial en Emergencia</td>
                                    <td class="text-center">-</td>
                                </tr>
                                <tr>
                                    <td class="text-center">5</td>
                                    <td class="text-center"><input class="checkParcial" value="4" type="checkbox"></td>
                                    <td class="text-center">TAC2</td>
                                    <td>Tabla Agregada C2 - Reporte Consolidado de Morbilidad en Emergencia</td>
                                    <td class="text-center">-</td>
                                </tr>
                                <tr>
                                    <td class="text-center">6</td>
                                    <td class="text-center"><input class="checkParcial" value="5" type="checkbox"></td>
                                    <td class="text-center">TAD1</td>
                                    <td>Tabla Agregada D1 - Reporte Consolidado de Producción Asistencial en Hospitalización</td>
                                    <td class="text-center">-</td>
                                </tr>
                                <tr>
                                    <td class="text-center">7</td>
                                    <td class="text-center"><input class="checkParcial" value="6" type="checkbox"></td>
                                    <td class="text-center">TAD2</td>
                                    <td>Tabla Agregada D2 - Reporte Consolidado de Morbilidad en Hospitalización</td>
                                    <td class="text-center">-</td>
                                </tr>
                                <tr>
                                    <td class="text-center">8</td>
                                    <td class="text-center"><input class="checkParcial" value="7" type="checkbox"></td>
                                    <td class="text-center">TAE0</td>
                                    <td>Tabla Agregada E - Reporte Consolidado de Partos</td>
                                    <td class="text-center">-</td>
                                </tr>
                                <tr>
                                    <td class="text-center">9</td>
                                    <td class="text-center"><input class="checkParcial" value="8" type="checkbox"></td>
                                    <td class="text-center">TAF0</td>
                                    <td>Tabla Agregada F - Reporte Consolidado de Eventos bajo Vigilancia Institucional</td>
                                    <td class="text-center">-</td>
                                </tr>
                                <tr>
                                    <td class="text-center">10</td>
                                    <td class="text-center"><input class="checkParcial" value="9" type="checkbox"></td>
                                    <td class="text-center">TAG0</td>
                                    <td>Tabla Agregada G - Reporte Consolidado de Producción Asistencial de Procedimientos</td>
                                    <td class="text-center">-</td>
                                </tr>
                                <tr>
                                    <td class="text-center">11</td>
                                    <td class="text-center"><input class="checkParcial" value="9" type="checkbox"></td>
                                    <td class="text-center">TAH0</td>
                                    <td>Tabla Agregada H - Reporte Consolidado de Producción Asistencial de Intervenciones Quirúrgicas</td>
                                    <td class="text-center">-</td>
                                </tr>
                                <tr>
                                    <td class="text-center">12</td>
                                    <td class="text-center"><input class="checkParcial" value="10" type="checkbox"></td>
                                    <td class="text-center">TAI0</td>
                                    <td>Tabla I Referencias</td>
                                    <td class="text-center">-</td>
                                </tr>
                                <tr>
                                    <td class="text-center">13</td>
                                    <td class="text-center"><input class="checkParcial" value="11" type="checkbox"></td>
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

    function descargarTramas() {
        var descargar = false;
        $('.checkParcial').each(function() {
            if($(this).prop('checked')) {
                descargar = true;
            }
        });
        if(descargar) {
            alert('yaaaaaaaaaaa');
        } else {
            alert('nooooooooooo');
        }
    }
</script>