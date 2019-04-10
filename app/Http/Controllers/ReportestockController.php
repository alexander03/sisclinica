<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Validator;
use App\Http\Requests;
use App\Conceptopago;
use App\Movimiento;
use App\Almacen;
use App\Producto;
use App\Librerias\Libreria;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Elibyy\TCPDF\Facades\TCPDF;
use Illuminate\Support\Facades\Session;
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
class ReportestockController extends Controller
{
    protected $folderview      = 'app.reportestock';
    protected $tituloAdmin     = 'Reporte de Stock';
    protected $tituloRegistrar = 'Registrar Concepto de Pago';
    protected $tituloModificar = 'Modificar Concepto de Pago';
    protected $tituloEliminar  = 'Eliminar Concepto de Pago';
    protected $rutas           = array('create' => 'reportestock.create', 
            'edit'   => 'conceptopago.edit', 
            'delete' => 'conceptopago.eliminar',
            'search' => 'reportestock.buscar',
            'index'  => 'reportestock.index',
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
        $entidad          = 'Reportestock';
        if($request->input('tipo')=="F"){
            $resultado     = Producto::join('lote as l','l.producto_id','=','producto.id')
                             ->leftjoin('laboratorio as l2','l2.id','=','producto.laboratorio_id')
                             ->leftjoin('presentacion as p2','p2.id','=','producto.presentacion_id')
                             ->leftjoin('anaquel as a2','a2.id','=','producto.anaquel_id')
                             ->join('almacen as a','a.id','=','l.almacen_id')
                             ->where('producto.tipo','like','F')
                             ->where('l.queda','>',0)
                             ->where('l.almacen_id','=',$request->input('almacen'));
        }else{
            $resultado     = Producto::join('stock as s','s.producto_id','=','producto.id')
                             ->leftjoin('laboratorio as l2','l2.id','=','producto.laboratorio_id')
                             ->leftjoin('presentacion as p2','p2.id','=','producto.presentacion_id')
                             ->leftjoin('anaquel as a2','a2.id','=','producto.anaquel_id')
                             ->join('almacen as a','a.id','=','s.almacen_id')
                             ->where('producto.tipo','like','O')
                             ->where('s.almacen_id','=',$request->input('almacen'));
        }
        if($request->input('producto')!=""){
            $resultado = $resultado->where('producto.nombre','like','%'.$request->input('producto').'%');
        }
        if($request->input('tipo')=="F"){
            $resultado        = $resultado->orderBy(DB::raw('producto.nombre'), 'asc')                    ->select('producto.nombre as producto','a.nombre as almacen','l.fechavencimiento','l.nombre as lote','l.queda as stock','l2.nombre as laboratorio','p2.nombre as presentacion','a2.descripcion as anaquel');
        }else{
            $resultado        = $resultado->orderBy(DB::raw('producto.nombre'), 'asc')                    ->select('producto.nombre as producto','a.nombre as almacen',DB::raw('" " as fechavencimiento'),DB::raw('" " as lote'),'s.cantidad as stock','l2.nombre as laboratorio','p2.nombre as presentacion','a2.descripcion as anaquel');
        }
        $lista            = $resultado->get();
        $cabecera         = array();
        $cabecera[]       = array('valor' => 'Almacen', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Producto', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Laboratorio', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Presentacion', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Anaquel', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Lote', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Fecha Venc.', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Stock', 'numero' => '1');
        
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
        $entidad          = 'Reportestock';
        $title            = $this->tituloAdmin;
        $titulo_registrar = $this->tituloRegistrar;
        $ruta             = $this->rutas;
        $sucursal_id = Session::get('sucursal_id');
        $almacen = Almacen::where('sucursal_id','=',$sucursal_id)->get();
        $cboAlmacen = array();
        foreach ($almacen as $key => $value){
            $cboAlmacen = $cboAlmacen + array($value->id => $value->nombre);
        }
        $cboTipo = array('F' => 'Farmacia', 'O' => 'Otros');
        return view($this->folderview.'.admin')->with(compact('entidad', 'title', 'titulo_registrar', 'ruta', 'cboAlmacen', 'cboTipo'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $listar              = Libreria::getParam($request->input('listar'), 'NO');
        $entidad             = 'Reportestock';
        $conceptopago = null;
        $formData            = array('reportestock.store');
        $cboTipo          = array("Ingreso" => "Ingreso", "Egreso" => "Egreso");
        $formData            = array('route' => $formData, 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton               = 'Registrar'; 
        return view($this->folderview.'.mant')->with(compact('reportefacturacion', 'formData', 'entidad', 'boton', 'listar', 'cboTipo'));
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

    public function excel(Request $request){
        setlocale(LC_TIME, 'spanish');
        if($request->input('tipo')=="F"){
            $resultado     = Producto::join('lote as l','l.producto_id','=','producto.id')
                             ->leftjoin('laboratorio as l2','l2.id','=','producto.laboratorio_id')
                             ->leftjoin('presentacion as p2','p2.id','=','producto.presentacion_id')
                             ->leftjoin('anaquel as a2','a2.id','=','producto.anaquel_id')
                             ->join('almacen as a','a.id','=','l.almacen_id')
                             ->where('producto.tipo','like','F')
                             ->where('l.queda','>',0)
                             ->where('l.almacen_id','=',$request->input('almacen'));
        }else{
            $resultado     = Producto::join('stock as s','s.producto_id','=','producto.id')
                             ->leftjoin('laboratorio as l2','l2.id','=','producto.laboratorio_id')
                             ->leftjoin('presentacion as p2','p2.id','=','producto.presentacion_id')
                             ->leftjoin('anaquel as a2','a2.id','=','producto.anaquel_id')
                             ->join('almacen as a','a.id','=','s.almacen_id')
                             ->where('producto.tipo','like','O')
                             ->where('s.almacen_id','=',$request->input('almacen'));
        }
        if($request->input('producto')!=""){
            $resultado = $resultado->where('producto.nombre','like','%'.$request->input('producto').'%');
        }
        if($request->input('tipo')=="F"){
            $resultado        = $resultado->orderBy(DB::raw('producto.nombre'), 'asc')                    ->select('producto.nombre as producto','a.nombre as almacen','l.fechavencimiento','l.nombre as lote','l.queda as stock','l2.nombre as laboratorio','p2.nombre as presentacion','a2.descripcion as anaquel');
        }else{
            $resultado        = $resultado->orderBy(DB::raw('producto.nombre'), 'asc')                    ->select('producto.nombre as producto','a.nombre as almacen',DB::raw('" " as fechavencimiento'),DB::raw('" " as lote'),'s.cantidad as stock','l2.nombre as laboratorio','p2.nombre as presentacion','a2.descripcion as anaquel');
        }
        $lista            = $resultado->get();

        Excel::create('ExcelReporteStock', function($excel) use($lista,$request) {
 

            $total=0;$tarjeta=0;$contado=0;$pendiente=0;
            $excel->sheet('ReporteStock', function($sheet) use($lista,$request) {
                $cabecera[] = "Almacen";
                $cabecera[] = "Producto";
                $cabecera[] = "Laboratorio";
                $cabecera[] = "Presentacion";
                $cabecera[] = "Anaquel";
                $cabecera[] = "Lote";
                $cabecera[] = "Fecha Venc.";
                $cabecera[] = "Stock";
                $sheet->row(1,$cabecera);
                $c=2;$d=3;$band=true;

                foreach ($lista as $key => $value){
                    $detalle = array();
                    $detalle[] = $value->almacen;
                    $detalle[] = $value->producto;
                    $detalle[] = $value->laboratorio;
                    $detalle[] = $value->presentacion;
                    $detalle[] = $value->anaquel;
                    $detalle[] = $value->lote;                    
                    $detalle[] = ($value->fechavencimiento!=''?date("d/m/Y",strtotime($value->fechavencimiento)):'');
                    $detalle[] = round($value->stock,0);
                    $sheet->row($c,$detalle);
                    $c=$c+1;
                }
            });
        })->export('xls');
    }

}
