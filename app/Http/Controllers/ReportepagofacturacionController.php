<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Validator;
use App\Http\Requests;
use App\Conceptopago;
use App\Movimiento;
use App\Librerias\Libreria;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Elibyy\TCPDF\Facades\TCPDF;
use Excel;


/**
 * PagodoctorController
 * 
 * @package 
 * @author DaYeR
 * @copyright 2017
 * @version $Id$
 * @access public
 */
class ReportepagofacturacionController extends Controller
{
    protected $folderview      = 'app.reportepagofacturacion';
    protected $tituloAdmin     = 'Reporte de Pago a Doctores';
    protected $tituloRegistrar = 'Registrar Concepto de Pago';
    protected $tituloModificar = 'Modificar Concepto de Pago';
    protected $tituloEliminar  = 'Eliminar Concepto de Pago';
    protected $rutas           = array('create' => 'reportepagofacturacion.create', 
            'edit'   => 'reporteconsulta.edit', 
            'delete' => 'reporteconsulta.eliminar',
            'search' => 'reportepagofacturacion.buscar',
            'index'  => 'reportepagofacturacion.index',
        );

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Mostrar el resultado de búsquedas
     * 
     * @return Response 
     */
    public function buscar(Request $request)
    {
        $pagina           = $request->input('page');
        $filas            = $request->input('filas');
        $entidad          = 'Reportepagofacturacion';
        $paciente         = Libreria::getParam($request->input('paciente'));
        $doctor           = Libreria::getParam($request->input('doctor'));
        $tipopaciente     = Libreria::getParam($request->input('tipopaciente'));
        $plan           = Libreria::getParam($request->input('plan'));
        $fechainicial     = Libreria::getParam($request->input('fechainicial'));
        $fechafinal       = Libreria::getParam($request->input('fechafinal'));
        $resultado        = Movimiento::join('detallemovcaja as dmc','dmc.movimiento_id','=','movimiento.id')
                            ->join('person as medico','medico.id','=','dmc.persona_id')
                            ->join('person as paciente','paciente.id','=','movimiento.persona_id')
                            ->join('person as responsable','responsable.id','=','movimiento.responsable_id')
                            ->join('plan','plan.id','=','movimiento.plan_id')
                            ->leftjoin('servicio as s','s.id','=','dmc.servicio_id')
                            ->leftjoin('historia','historia.person_id','=','movimiento.persona_id')
                            ->leftjoin('person as referido','referido.id','=','movimiento.doctor_id')
                            ->where('plan.nombre','like','%'.$plan.'%')
                            ->where(DB::raw('concat(paciente.apellidopaterno,\' \',paciente.apellidomaterno,\' \',paciente.nombres)'),'like','%'.$paciente.'%')
                            ->where(DB::raw('concat(medico.apellidopaterno,\' \',medico.apellidomaterno,\' \',medico.nombres)'),'like','%'.$doctor.'%')
                            ->where('movimiento.tipomovimiento_id','=',9)
                            ->where('dmc.pagodoctor','>',0)
                            ->whereIn('movimiento.situacion',['C'])
                            ->whereNotIn('movimiento.situacion',['U','A']);
        if($fechainicial!=""){
            $resultado = $resultado->where('movimiento.fechaentrega','>=',$fechainicial);
        }
        if($fechafinal!=""){
            $resultado = $resultado->where('movimiento.fechaentrega','<=',$fechafinal);
        }
        $resultado        = $resultado->orderBy(DB::raw('movimiento.fecha'), 'desc')->orderBy('movimiento.serie', 'ASC')->orderBy('movimiento.numero', 'ASC')
                            ->select('movimiento.total',DB::raw('plan.nombre as plan'),'movimiento.tipomovimiento_id','movimiento.serie','movimiento.numero',DB::raw('movimiento.fecha'),'movimiento.doctor_id as referido_id','historia.tipopaciente',DB::raw('historia.numero as historia'),'dmc.servicio_id','dmc.descripcion as servicio2','movimiento.tarjeta','movimiento.tipotarjeta','movimiento.situacion as situacion2','historia.numero as historia','dmc.recibo','dmc.id as iddetalle','s.nombre as servicio','dmc.precioconvenio','dmc.precio','dmc.pagohospital','s.precio as precio2','dmc.cantidad','dmc.pagodoctor',DB::raw('concat(medico.apellidopaterno,\' \',medico.apellidomaterno,\' \',medico.nombres) as medico'),DB::raw('concat(referido.apellidopaterno,\' \',referido.apellidomaterno,\' \',referido.nombres) as referido'),DB::raw('concat(paciente.apellidopaterno,\' \',paciente.apellidomaterno,\' \',paciente.nombres) as paciente2'),DB::raw('responsable.nombres as responsable'),DB::raw('movimiento.numero as numero2'),'movimiento.fechaingreso','movimiento.fechaentrega','movimiento.voucher');
        $lista            = $resultado->get();
        //dd($lista);
        $cabecera         = array();
        $cabecera[]       = array('valor' => 'Doctor', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Paciente', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Fecha Atencion', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Fecha Doc.', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Nro Doc.', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Pago Doctor', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Fecha Pago', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Nro Ope.', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Plan', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Cant.', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Servicio', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Usuario', 'numero' => '1');
        
        $titulo_modificar = $this->tituloModificar;
        $titulo_eliminar  = $this->tituloEliminar;
        $ruta             = $this->rutas;
        if (count($lista) > 0) {
            $clsLibreria     = new Libreria();
            $paramPaginacion = $clsLibreria->generarPaginacion($lista, $pagina, $filas, $entidad);
            $paginacion      = $paramPaginacion['cadenapaginacion'];
            $inicio          = $paramPaginacion['inicio'];
            $fin             = $paramPaginacion['fin'];
            $paginaactual    = $paramPaginacion['nuevapagina'];
            $lista           = $resultado->paginate($filas);
            $request->replace(array('page' => $paginaactual));
            return view($this->folderview.'.list')->with(compact('lista', 'paginacion', 'inicio', 'fin', 'entidad', 'cabecera', 'titulo_modificar', 'titulo_eliminar', 'ruta'));
        }
        return view($this->folderview.'.list')->with(compact('lista', 'entidad'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $entidad          = 'Reportepagofacturacion';
        $title            = $this->tituloAdmin;
        $titulo_registrar = $this->tituloRegistrar;
        $ruta             = $this->rutas;
        $cboTipoPaciente          = array("" => "Todos", "P" => "Particular", "C" => "Convenio");
        return view($this->folderview.'.admin')->with(compact('entidad', 'title', 'titulo_registrar', 'ruta', 'cboTipoPaciente'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $listar              = Libreria::getParam($request->input('listar'), 'NO');
        $entidad             = 'Reportepagofacturacion';
        $conceptopago = null;
        $formData            = array('reportepagofacturacion.store');
        $cboTipo          = array("Ingreso" => "Ingreso", "Egreso" => "Egreso");
        $formData            = array('route' => $formData, 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton               = 'Registrar'; 
        return view($this->folderview.'.mant')->with(compact('reportepagofacturacion', 'formData', 'entidad', 'boton', 'listar', 'cboTipo'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $listar     = Libreria::getParam($request->input('listar'), 'NO');
        $reglas     = array(
                'nombre'                  => 'required|max:200',
                );
        $mensajes = array(
            'nombre.required'         => 'Debe ingresar un nombre',
            );
        $validacion = Validator::make($request->all(), $reglas, $mensajes);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        }
        $error = DB::transaction(function() use($request){
            $Conceptopago       = new Conceptopago();
            $Conceptopago->nombre = strtoupper($request->input('nombre'));
            $Conceptopago->tipo = $request->input('tipo');
            $Conceptopago->save();
        });
        return is_null($error) ? "OK" : $error;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id, Request $request)
    {
        $existe = Libreria::verificarExistencia($id, 'conceptopago');
        if ($existe !== true) {
            return $existe;
        }
        $listar              = Libreria::getParam($request->input('listar'), 'NO');
        $conceptopago = Conceptopago::find($id);
        $entidad             = 'conceptopago';
        $formData            = array('Conceptopago.update', $id);
        $formData            = array('route' => $formData, 'method' => 'PUT', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton               = 'Modificar';
        return view($this->folderview.'.mant')->with(compact('conceptopago', 'formData', 'entidad', 'boton', 'listar'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $existe = Libreria::verificarExistencia($id, 'conceptopago');
        if ($existe !== true) {
            return $existe;
        }
        $reglas     = array(
                'nombre'                  => 'required|max:200',
                );
        $mensajes = array(
            'nombre.required'         => 'Debe ingresar un nombre',
            );
        $validacion = Validator::make($request->all(), $reglas, $mensajes);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        }
        $error = DB::transaction(function() use($request, $id){
            $categoria                        = Categoria::find($id);
            $categoria->nombre = strtoupper($request->input('nombre'));
            $Conceptopago->tipo = $request->input('tipo');
            $categoria->save();
        });
        return is_null($error) ? "OK" : $error;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $existe = Libreria::verificarExistencia($id, 'conceptopago');
        if ($existe !== true) {
            return $existe;
        }
        $error = DB::transaction(function() use($id){
            $Conceptopago = Conceptopago::find($id);
            $Conceptopago->delete();
        });
        return is_null($error) ? "OK" : $error;
    }

    public function eliminar($id, $listarLuego)
    {
        $existe = Libreria::verificarExistencia($id, 'conceptopago');
        if ($existe !== true) {
            return $existe;
        }
        $listar = "NO";
        if (!is_null(Libreria::obtenerParametro($listarLuego))) {
            $listar = $listarLuego;
        }
        $modelo   = Conceptopago::find($id);
        $entidad  = 'conceptopago';
        $formData = array('route' => array('conceptopago.destroy', $id), 'method' => 'DELETE', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Eliminar';
        return view('app.confirmarEliminar')->with(compact('modelo', 'formData', 'entidad', 'boton', 'listar'));
    }

    public function excel(Request $request){
        setlocale(LC_TIME, 'spanish');
        $paciente         = Libreria::getParam($request->input('paciente'));
        $doctor           = Libreria::getParam($request->input('doctor'));
        $tipopaciente     = Libreria::getParam($request->input('tipopaciente'));
        $tiposervicio_id  = Libreria::getParam($request->input('tiposervicio_id'));
        $plan           = Libreria::getParam($request->input('plan'));
        $fechainicial     = Libreria::getParam($request->input('fechainicial'));
        $fechafinal       = Libreria::getParam($request->input('fechafinal'));
        $resultado        = Movimiento::join('detallemovcaja as dmc','dmc.movimiento_id','=','movimiento.id')
                            ->join('person as medico','medico.id','=','dmc.persona_id')
                            ->join('person as paciente','paciente.id','=','movimiento.persona_id')
                            ->join('person as responsable','responsable.id','=','movimiento.responsable_id')
                            ->join('plan','plan.id','=','movimiento.plan_id')
                            ->leftjoin('servicio as s','s.id','=','dmc.servicio_id')
                            ->leftjoin('historia','historia.person_id','=','movimiento.persona_id')
                            ->leftjoin('person as referido','referido.id','=','movimiento.doctor_id')
                            ->where('plan.nombre','like','%'.$plan.'%')
                            ->where(DB::raw('concat(paciente.apellidopaterno,\' \',paciente.apellidomaterno,\' \',paciente.nombres)'),'like','%'.$paciente.'%')
                            ->where(DB::raw('concat(medico.apellidopaterno,\' \',medico.apellidomaterno,\' \',medico.nombres)'),'like','%'.$doctor.'%')
                            ->where('movimiento.tipomovimiento_id','=',9)
                            ->where('dmc.pagodoctor','>',0)
                            ->whereIn('movimiento.situacion',['C'])
                            ->whereNotIn('movimiento.situacion',['U','A']);
        if($fechainicial!=""){
            $resultado = $resultado->where('movimiento.fechaentrega','>=',$fechainicial);
        }
        if($fechafinal!=""){
            $resultado = $resultado->where('movimiento.fechaentrega','<=',$fechafinal);
        }
        $resultado        = $resultado->orderBy(DB::raw('concat(medico.apellidopaterno,\' \',medico.apellidomaterno,\' \',medico.nombres)'), 'desc')->orderBy(DB::raw('movimiento.fecha'), 'desc')->orderBy('movimiento.serie', 'ASC')->orderBy('movimiento.numero', 'ASC')
                            ->select('movimiento.total',DB::raw('plan.nombre as plan'),'movimiento.tipomovimiento_id','movimiento.serie','movimiento.numero',DB::raw('movimiento.fecha'),'movimiento.doctor_id as referido_id','historia.tipopaciente',DB::raw('historia.numero as historia'),'dmc.servicio_id','dmc.descripcion as servicio2','movimiento.tarjeta','movimiento.tipotarjeta','movimiento.situacion as situacion2','historia.numero as historia','dmc.recibo','dmc.id as iddetalle','s.nombre as servicio','dmc.precioconvenio','dmc.precio','dmc.pagohospital','s.precio as precio2','s.tiposervicio_id','dmc.cantidad','dmc.pagodoctor',DB::raw('concat(medico.apellidopaterno,\' \',medico.apellidomaterno,\' \',medico.nombres) as medico'),DB::raw('concat(referido.apellidopaterno,\' \',referido.apellidomaterno,\' \',referido.nombres) as referido'),DB::raw('concat(paciente.apellidopaterno,\' \',paciente.apellidomaterno,\' \',paciente.nombres) as paciente2'),DB::raw('responsable.nombres as responsable'),DB::raw('movimiento.numero as numero2'),'movimiento.fechaingreso','movimiento.fechaentrega','movimiento.voucher','movimiento.montoinicial','movimiento.igv','movimiento.copago');
        $lista            = $resultado->get();

        Excel::create('ExcelPagoDoctor', function($excel) use($lista,$request) {
 
            $excel->sheet('PagoDoctor', function($sheet) use($lista,$request) {
                $cabecera[] = "Paciente";
                $cabecera[] = "Fecha Atencion";
                $cabecera[] = "Fecha Doc.";
                $cabecera[] = "Nro Doc.";
                $cabecera[] = "Pago Doctor";
                $cabecera[] = "Precio";
                $cabecera[] = "Fecha Pago";
                $cabecera[] = "Nro Ope.";
                $cabecera[] = "Plan";
                $cabecera[] = "Servicio";
                $cabecera[] = "Usuario";
                $sheet->row(1,$cabecera);
                $c=2;$d=3;$band=true;$total=0;$doctor="";$totalg=0;

                foreach ($lista as $key => $value){
                    if($doctor!=$value->medico){
                        if($doctor!=""){
                            $detalle = array();
                            $detalle[] = "";
                            $detalle[] = "";
                            $detalle[] = "";
                            $detalle[] = "TOTAL";
                            $detalle[] = number_format($total,2,'.','');
                            $sheet->row($c,$detalle);
                            $totalg=$totalg+$total;
                            $c=$c+1;        
                        }
                        $detalle = array();
                        $detalle[] = $value->medico;
                        $sheet->row($c,$detalle);
                        $doctor=$value->medico;
                        $c=$c+1;    
                        $total=0;
                    }
                    if($value->servicio_id>0){
                        $tiposervicio_id=$value->tiposervicio_id;
                    }else{
                        $tiposervicio_id=0;
                    }
                    $nombre=$value->servicio2;
                    $detalle = array();
                    $detalle[] = $value->paciente2;
                    $detalle[] = date('d/m/Y',strtotime($value->fechaingreso));
                    $detalle[] = date('d/m/Y',strtotime($value->fecha));
                    $detalle[] = "F".$value->serie.'-'.$value->numero;
                    $detalle[] = number_format(($value->pagodoctor),2,'.','');
                    //$detalle[] = number_format($value->precio,2,'.','');
                    if(strpos($nombre,'CONSULTA') === false && strpos($nombre,'CONS ') === false && $tiposervicio_id!="1") {
                        $value->precio=number_format($value->precio*100/(100-$value->montoinicial),2,'.','');
                    }
                    if($value->igv>0){
                        if(strpos($nombre,'CONSULTA') === false && strpos($nombre,'CONS ') === false && $tiposervicio_id!="1") {
                            $detalle[] = number_format($value->precio/1.18,2,'.','');
                        }else{
                            $detalle[] = number_format($value->copago+round($value->precio/1.18,2),2,'.','');
                        }
                    }else{
                        $detalle[] = number_format($value->precio,2,'.','');
                    }
                    $detalle[] = date('d/m/Y',strtotime($value->fechaentrega));
                    $detalle[] = $value->voucher;
                    $detalle[] = $value->plan;
                    $detalle[] = $value->servicio2;
                    $detalle[] = $value->responsable;
                    $sheet->row($c,$detalle);
                    $c=$c+1;
                    $total=$total+$value->pagodoctor;
                }
                $detalle = array();
                $detalle[] = "";
                $detalle[] = "";
                $detalle[] = "";
                $detalle[] = "TOTAL";
                $detalle[] = number_format($total,2,'.','');
                $sheet->row($c,$detalle);
                $c=$c+1;        
                $totalg=$totalg+$total;
                $detalle = array();
                $detalle[] = "";
                $detalle[] = "";
                $detalle[] = "";
                $detalle[] = "TOTAL GENERAL";
                $detalle[] = number_format($totalg,2,'.','');
                $sheet->row($c,$detalle);
                $c=$c+1;        
            });
        })->export('xls');
    }

    public function excelGeneral(Request $request){
        setlocale(LC_TIME, 'spanish');
        $paciente         = Libreria::getParam($request->input('paciente'));
        $doctor           = Libreria::getParam($request->input('doctor'));
        $plan           = Libreria::getParam($request->input('plan'));
        $fechainicial     = Libreria::getParam($request->input('fechainicial'));
        $fechafinal       = Libreria::getParam($request->input('fechafinal'));
        $resultado        = Movimiento::join('detallemovcaja as dmc','dmc.movimiento_id','=','movimiento.id')
                            ->join('person as medico','medico.id','=','dmc.persona_id')
                            ->join('especialidad','especialidad.id','=','medico.especialidad_id')
                            ->join('person as paciente','paciente.id','=','movimiento.persona_id')
                            ->join('person as responsable','responsable.id','=','movimiento.responsable_id')
                            ->join('plan','plan.id','=','movimiento.plan_id')
                            ->leftjoin('servicio as s','s.id','=','dmc.servicio_id')
                            ->leftjoin('historia','historia.person_id','=','movimiento.persona_id')
                            ->leftjoin('person as referido','referido.id','=','movimiento.doctor_id')
                            ->where('plan.nombre','like','%'.$plan.'%')
                            ->where(DB::raw('concat(paciente.apellidopaterno,\' \',paciente.apellidomaterno,\' \',paciente.nombres)'),'like','%'.$paciente.'%')
                            ->where(DB::raw('concat(medico.apellidopaterno,\' \',medico.apellidomaterno,\' \',medico.nombres)'),'like','%'.$doctor.'%')
                            ->where('movimiento.tipomovimiento_id','=',9)
                            ->where('dmc.pagodoctor','>',0)
                            ->whereIn('movimiento.situacion',['C'])
                            ->whereNotIn('movimiento.situacion',['U','A']);
        if($fechainicial!=""){
            $resultado = $resultado->where('movimiento.fechaentrega','>=',$fechainicial);
        }
        if($fechafinal!=""){
            $resultado = $resultado->where('movimiento.fechaentrega','<=',$fechafinal);
        }
        $resultado        = $resultado->orderBy(DB::raw('concat(medico.apellidopaterno,\' \',medico.apellidomaterno,\' \',medico.nombres)'), 'desc')->orderBy(DB::raw('movimiento.fecha'), 'desc')->orderBy('movimiento.serie', 'ASC')->orderBy('movimiento.numero', 'ASC')
                            ->select('movimiento.total',DB::raw('plan.nombre as plan'),'movimiento.tipomovimiento_id','movimiento.serie','movimiento.numero',DB::raw('movimiento.fecha'),'movimiento.doctor_id as referido_id','historia.tipopaciente',DB::raw('historia.numero as historia'),'dmc.servicio_id','plan.ruc as ruc2','dmc.descripcion as servicio2','movimiento.tarjeta','movimiento.tipotarjeta','movimiento.situacion as situacion2','historia.numero as historia','dmc.recibo','dmc.id as iddetalle','s.nombre as servicio','dmc.precioconvenio','dmc.precio','dmc.pagohospital','dmc.cantidad','s.precio as precio2','dmc.cantidad','dmc.pagodoctor',DB::raw('concat(medico.apellidopaterno,\' \',medico.apellidomaterno,\' \',medico.nombres) as medico'),DB::raw('concat(referido.apellidopaterno,\' \',referido.apellidomaterno,\' \',referido.nombres) as referido'),DB::raw('concat(paciente.apellidopaterno,\' \',paciente.apellidomaterno,\' \',paciente.nombres) as paciente2'),'paciente.dni as dni2',DB::raw('responsable.nombres as responsable'),DB::raw('movimiento.numero as numero2'),'movimiento.fechaingreso','movimiento.fechaentrega','movimiento.voucher','especialidad.nombre as especialidad2');
        $lista            = $resultado->get();

        Excel::create('ExcelPagoGeneral', function($excel) use($lista,$request) {
 
            $excel->sheet('PagoGeneral', function($sheet) use($lista,$request) {
                $cabecera[] = "Fecha";
                $cabecera[] = "Nro Doc.";
                $cabecera[] = "Paciente";
                $cabecera[] = "DNI";
                $cabecera[] = "Cliente";
                $cabecera[] = "RUC";
                $cabecera[] = "Situacion";
                $cabecera[] = "Rubro";
                $cabecera[] = "Total";
                $cabecera[] = "Fecha Atencion";
                $cabecera[] = "Medico";
                $cabecera[] = "Especialidad";
                $cabecera[] = "Fecha Pago";
                $cabecera[] = "Pago Medico";
                $sheet->row(1,$cabecera);
                $c=2;$d=3;$band=true;$total=0;$doctor="";$totalg=0;

                foreach ($lista as $key => $value){
                    $detalle = array();
                    $detalle[] = date('d/m/Y',strtotime($value->fecha));
                    $detalle[] = "F".$value->serie.'-'.$value->numero;
                    $detalle[] = $value->paciente2;
                    $detalle[] = $value->dni2;
                    $detalle[] = $value->plan;
                    $detalle[] = $value->ruc2;
                    $detalle[] = 'PAGADO';
                    $detalle[] = $value->servicio2;
                    $detalle[] = number_format(($value->precio*$value->cantidad)/1.18,2,'.','');
                    $detalle[] = date('d/m/Y',strtotime($value->fechaingreso));
                    $detalle[] = $value->medico;
                    $detalle[] = $value->especialidad2;
                    $detalle[] = date('d/m/Y',strtotime($value->fechaentrega));
                    $detalle[] = number_format(($value->pagodoctor),2,'.','');
                    $sheet->row($c,$detalle);
                    $c=$c+1;
                    $total=$total+$value->pagodoctor;
                }
                $detalle = array();
                $detalle[] = "";
                $detalle[] = "";
                $detalle[] = "";
                $detalle[] = "TOTAL GENERAL";
                $detalle[] = number_format($total,2,'.','');
                $sheet->row($c,$detalle);
                $c=$c+1;        
            });
        })->export('xls');
    }

    public function excelDoctor(Request $request){
        setlocale(LC_TIME, 'spanish');
        $paciente         = Libreria::getParam($request->input('paciente'));
        $doctor           = Libreria::getParam($request->input('doctor'));
        $plan           = Libreria::getParam($request->input('plan'));
        $fechainicial     = Libreria::getParam($request->input('fechainicial'));
        $fechafinal       = Libreria::getParam($request->input('fechafinal'));
        $resultado        = Movimiento::join('detallemovcaja as dmc','dmc.movimiento_id','=','movimiento.id')
                            ->join('person as medico','medico.id','=','dmc.persona_id')
                            ->join('especialidad','especialidad.id','=','medico.especialidad_id')
                            ->join('person as paciente','paciente.id','=','movimiento.persona_id')
                            ->join('person as responsable','responsable.id','=','movimiento.responsable_id')
                            ->join('plan','plan.id','=','movimiento.plan_id')
                            ->leftjoin('servicio as s','s.id','=','dmc.servicio_id')
                            ->leftjoin('historia','historia.person_id','=','movimiento.persona_id')
                            ->leftjoin('person as referido','referido.id','=','movimiento.doctor_id')
                            ->where('plan.nombre','like','%'.$plan.'%')
                            ->where(DB::raw('concat(paciente.apellidopaterno,\' \',paciente.apellidomaterno,\' \',paciente.nombres)'),'like','%'.$paciente.'%')
                            ->where(DB::raw('concat(medico.apellidopaterno,\' \',medico.apellidomaterno,\' \',medico.nombres)'),'like','%'.$doctor.'%')
                            ->where('movimiento.tipomovimiento_id','=',9)
                            ->where('dmc.pagodoctor','>',0)
                            ->whereIn('movimiento.situacion',['C'])
                            ->whereNotIn('movimiento.situacion',['U','A']);
        if($fechainicial!=""){
            $resultado = $resultado->where('movimiento.fechaentrega','>=',$fechainicial);
        }
        if($fechafinal!=""){
            $resultado = $resultado->where('movimiento.fechaentrega','<=',$fechafinal);
        }
        $resultado        = $resultado->orderBy(DB::raw('concat(medico.apellidopaterno,\' \',medico.apellidomaterno,\' \',medico.nombres)'), 'desc')->orderBy(DB::raw('movimiento.fecha'), 'desc')->orderBy('movimiento.serie', 'ASC')->orderBy('movimiento.numero', 'ASC')
                            ->select('movimiento.total',DB::raw('plan.nombre as plan'),'movimiento.tipomovimiento_id','movimiento.serie','movimiento.numero',DB::raw('movimiento.fecha'),'movimiento.doctor_id as referido_id','historia.tipopaciente',DB::raw('historia.numero as historia'),'dmc.servicio_id','plan.ruc as ruc2','dmc.descripcion as servicio2','movimiento.tarjeta','movimiento.tipotarjeta','movimiento.situacion as situacion2','historia.numero as historia','dmc.recibo','dmc.id as iddetalle','s.nombre as servicio','dmc.precioconvenio','dmc.precio','dmc.pagohospital','dmc.cantidad','s.precio as precio2','dmc.cantidad','dmc.pagodoctor',DB::raw('concat(medico.apellidopaterno,\' \',medico.apellidomaterno,\' \',medico.nombres) as medico'),DB::raw('concat(referido.apellidopaterno,\' \',referido.apellidomaterno,\' \',referido.nombres) as referido'),DB::raw('concat(paciente.apellidopaterno,\' \',paciente.apellidomaterno,\' \',paciente.nombres) as paciente2'),'paciente.dni as dni2',DB::raw('responsable.nombres as responsable'),DB::raw('movimiento.numero as numero2'),'movimiento.fechaingreso','movimiento.fechaentrega','movimiento.voucher','especialidad.nombre as especialidad2');
        $lista            = $resultado->get();

        Excel::create('ExcelPagoDoctor', function($excel) use($lista,$request) {
 
            $excel->sheet('PagoDoctor', function($sheet) use($lista,$request) {
                $cabecera[] = "Fecha";
                $cabecera[] = "Nro Doc.";
                $cabecera[] = "Paciente";
                $cabecera[] = "DNI";
                $cabecera[] = "Cliente";
                $cabecera[] = "RUC";
                $cabecera[] = "Situacion";
                $cabecera[] = "Rubro";
                $cabecera[] = "Total";
                $cabecera[] = "Fecha Atencion";
                $cabecera[] = "Fecha Pago";
                $cabecera[] = "Pago Medico";
                $sheet->row(1,$cabecera);
                $c=2;$d=3;$band=true;$total=0;$doctor="";$totalg=0;

                foreach ($lista as $key => $value){
                    if($doctor!=$value->medico){
                        if($doctor!=""){
                            $detalle = array();
                            $detalle[] = "";
                            $detalle[] = "";
                            $detalle[] = "";
                            $detalle[] = "";
                            $detalle[] = "";
                            $detalle[] = "";
                            $detalle[] = "";
                            $detalle[] = "";
                            $detalle[] = "";
                            $detalle[] = "";
                            $detalle[] = "TOTAL";
                            $detalle[] = number_format($total,2,'.','');
                            $sheet->row($c,$detalle);
                            $totalg=$totalg+$total;
                            $c=$c+1;        
                        }
                        $detalle = array();
                        $detalle[] = $value->medico;
                        $sheet->row($c,$detalle);
                        $doctor=$value->medico;
                        $c=$c+1;    
                        $total=0;
                    }
                    $detalle = array();
                    $detalle[] = date('d/m/Y',strtotime($value->fecha));
                    $detalle[] = "F".$value->serie.'-'.$value->numero;
                    $detalle[] = $value->paciente2;
                    $detalle[] = $value->dni2;
                    $detalle[] = $value->plan;
                    $detalle[] = $value->ruc2;
                    $detalle[] = 'PAGADO';
                    $detalle[] = $value->servicio2;
                    $detalle[] = number_format(($value->precio*$value->cantidad)/1.18,2,'.','');
                    $detalle[] = date('d/m/Y',strtotime($value->fechaingreso));
                    $detalle[] = date('d/m/Y',strtotime($value->fechaentrega));
                    $detalle[] = number_format(($value->pagodoctor),2,'.','');
                    $sheet->row($c,$detalle);
                    $c=$c+1;
                    $total=$total+$value->pagodoctor;
                }
                $detalle = array();
                $detalle[] = "";
                $detalle[] = "";
                $detalle[] = "";
                $detalle[] = "";
                $detalle[] = "";
                $detalle[] = "";
                $detalle[] = "";
                $detalle[] = "";
                $detalle[] = "";
                $detalle[] = "";
                $detalle[] = "TOTAL";
                $detalle[] = number_format($total,2,'.','');
                $sheet->row($c,$detalle);
                $totalg=$totalg+$total;
                $c=$c+1;
                $detalle = array();
                $detalle[] = "";
                $detalle[] = "";
                $detalle[] = "";
                $detalle[] = "TOTAL GENERAL";
                $detalle[] = number_format($total,2,'.','');
                $sheet->row($c,$detalle);
                $c=$c+1;        
            });
        })->export('xls');
    }
}
