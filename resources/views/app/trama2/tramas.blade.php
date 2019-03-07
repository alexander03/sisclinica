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
                            {!! Form::button('<i class="glyphicon glyphicon-download-alt"></i> Descargar Comprimido', array('class' => 'btn btn-success btn-xs', 'id' => 'btnDescargar', 'onclick' => '#', 'disabled')) !!}
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
                                    <th class="text-center" width="5%"><input type="checkbox"></th>
                                    <th class="text-center" width="8%">Código</th>
                                    <th class="text-center" width="67%">Nombre Formato</th>
                                    <th class="text-center" width="15%">Operaciones</th>
                                </tr>
                            </thead>
                                <tr>
                                    <td class="text-center">1</td>
                                    <td class="text-center"><input type="checkbox"></td>
                                    <td class="text-center">TAA0</td>
                                    <td>Tabla Agregada A - Reporte de Recursos de Salud</td>
                                    <td class="text-center"><button class="btn btn-warning btn-xs" onclick='modal("caja/cirupro", "Establecer Datos de la Tabla TAA0");'><i class="fa fa-edit"></i> Establecer</button></td>
                                </tr>
                                <tr>
                                    <td class="text-center">2</td>
                                    <td class="text-center"><input type="checkbox"></td>
                                    <td class="text-center">TAB1</td>
                                    <td>Tabla Agregada B1 - Reporte Consolidado de Producción Asistencial en Consulta Ambulatoria</td>
                                    <td class="text-center">-</td>
                                </tr>
                                <tr>
                                    <td class="text-center">3</td>
                                    <td class="text-center"><input type="checkbox"></td>
                                    <td class="text-center">TAB2</td>
                                    <td>Tabla Agregada B2 - Reporte Consolidado de Morbilidad en Consulta Ambulatoria</td>
                                    <td class="text-center">-</td>
                                </tr>
                                <tr>
                                    <td class="text-center">4</td>
                                    <td class="text-center"><input type="checkbox"></td>
                                    <td class="text-center">TAC1</td>
                                    <td>Tabla Agregada C1 - Reporte Consolidado de Producción Asistencial en Emergencia</td>
                                    <td class="text-center">-</td>
                                </tr>
                                <tr>
                                    <td class="text-center">4</td>
                                    <td class="text-center"><input type="checkbox"></td>
                                    <td class="text-center">TAC2</td>
                                    <td>Tabla Agregada C2 - Reporte Consolidado de Morbilidad en Emergencia</td>
                                    <td class="text-center">-</td>
                                </tr>
                                <tr>
                                    <td class="text-center">5</td>
                                    <td class="text-center"><input type="checkbox"></td>
                                    <td class="text-center">TAD1</td>
                                    <td>Tabla Agregada D1 - Reporte Consolidado de Producción Asistencial en Hospitalización</td>
                                    <td class="text-center">-</td>
                                </tr>
                                <tr>
                                    <td class="text-center">6</td>
                                    <td class="text-center"><input type="checkbox"></td>
                                    <td class="text-center">TAD2</td>
                                    <td>Tabla Agregada D2 - Reporte Consolidado de Morbilidad en Hospitalización</td>
                                    <td class="text-center">-</td>
                                </tr>
                                <tr>
                                    <td class="text-center">7</td>
                                    <td class="text-center"><input type="checkbox"></td>
                                    <td class="text-center">TAE0</td>
                                    <td>Tabla Agregada E - Reporte Consolidado de Partos</td>
                                    <td class="text-center">-</td>
                                </tr>
                                <tr>
                                    <td class="text-center">8</td>
                                    <td class="text-center"><input type="checkbox"></td>
                                    <td class="text-center">TAF0</td>
                                    <td>Tabla Agregada F - Reporte Consolidado de Eventos bajo Vigilancia Institucional</td>
                                    <td class="text-center">-</td>
                                </tr>
                                <tr>
                                    <td class="text-center">9</td>
                                    <td class="text-center"><input type="checkbox"></td>
                                    <td class="text-center">TAG0</td>
                                    <td>Tabla Agregada G - Reporte Consolidado de Producción Asistencial de Procedimientos</td>
                                    <td class="text-center">-</td>
                                </tr>
                                <tr>
                                    <td class="text-center">10</td>
                                    <td class="text-center"><input type="checkbox"></td>
                                    <td class="text-center">TAH0</td>
                                    <td>Tabla Agregada H - Reporte Consolidado de Producción Asistencial de Intervenciones Quirúrgicas</td>
                                    <td class="text-center">-</td>
                                </tr>
                                <tr>
                                    <td class="text-center">11</td>
                                    <td class="text-center"><input type="checkbox"></td>
                                    <td class="text-center">TAI0</td>
                                    <td>Tabla I Referencias</td>
                                    <td class="text-center">-</td>
                                </tr>
                                <tr>
                                    <td class="text-center">12</td>
                                    <td class="text-center"><input type="checkbox"></td>
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
<script type="text/javascript"></script>