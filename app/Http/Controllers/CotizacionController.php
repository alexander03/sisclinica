<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Validator;
use App\Http\Requests;
use App\Cotizacion;

use App\Convenio;
use App\Movimiento;
use App\Detallecotizacion;
use App\Person;
use App\Cie;
use App\Tiposervicio;
use App\Servicio;
use App\Plan;
use App\Detalleplan;

use App\Librerias\Libreria;
use App\Librerias\EnLetras;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Elibyy\TCPDF\Facades\TCPDF;
use Illuminate\Support\Facades\Auth;
use Word;
use PhpOffice\PhpWord\Style\Font;
use PhpOffice\PhpWord\Style\Paragraph;
use PhpOffice\PhpWord\Settings;
use Excel;

class CotizacionController extends Controller
{
    protected $folderview      = 'app.cotizacion';
    protected $tituloAdmin     = 'Cotizacion';
    protected $tituloRegistrar = 'Registrar Cotización';
    protected $tituloModificar = 'Modificar Cotización';
    protected $tituloVer       = 'Ver Detalles de Cotización';
    protected $tituloAnular    = 'Anular Cotización';
    protected $rutas           = array('create' => 'cotizacion.create', 
            'edit'   => 'cotizacion.edit', 
            'delete' => 'cotizacion.eliminar',
            'search' => 'cotizacion.buscar',
            'index'  => 'cotizacion.index',
            'ver'    => 'cotizacion.ver'
        );

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function buscar(Request $request)
    {
        $pagina           = $request->input('page');
        $filas            = $request->input('filas');
        $entidad          = 'Cotizacion';
        $paciente         = Libreria::getParam($request->input('paciente'),'');
        $codigo           = Libreria::getParam($request->input('codigo'),'');
        $fecha            = Libreria::getParam($request->input('fechainicial'));
        $fecha2           = Libreria::getParam($request->input('fechafinal'));
        $tipo             = Libreria::getParam($request->input('tipo'));
        $situacion        = Libreria::getParam($request->input('situacion'));
        $plan             = Libreria::getParam($request->input('plan'));
        $user = Auth::user();
        $resultado        = Cotizacion::/*leftjoin('person as paciente', 'paciente.id', '=', 'cotizacion.paciente_id')
                            ->leftjoin('person as responsable','responsable.id','=','cotizacion.responsable_id')
                            ->where(DB::raw('concat(paciente.apellidopaterno,\' \',paciente.apellidomaterno,\' \',paciente.nombres)'), 'LIKE', '%'.strtoupper($paciente).'%')
                            ->*/where('cotizacion.codigo','LIKE','%'.$codigo.'%')
                            ->join('plan','plan.id','=','cotizacion.plan_id');
        if($fecha!=""){
            $resultado = $resultado->where('cotizacion.fecha', '>=', ''.$fecha.'');
        }
        if($plan!="") {
            $resultado = $resultado->where('plan.razonsocial','like','%'.$plan.'%');
        }
        if($fecha2!=""){
            $resultado = $resultado->where('cotizacion.fecha', '<=', ''.$fecha2.'');
        }
        if($tipo!=""){
            $resultado = $resultado->where('cotizacion.tipo', 'LIKE', '%'.$tipo.'%');
        }
        if($situacion!=""){
            $resultado = $resultado->where('cotizacion.situacion', 'LIKE', '%'.$situacion.'%');
        }
        $resultado        = $resultado->select('cotizacion.*'/*,DB::raw('concat(paciente.apellidopaterno,\' \',paciente.apellidomaterno,\' \',paciente.nombres) as paciente')*/)->orderBy('cotizacion.fecha', 'ASC');
        $lista            = $resultado->get();
        $cabecera         = array();
        $cabecera[]       = array('valor' => '#', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Fecha', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Código', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Plan', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Paciente', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Tipo', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Situación', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Total', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Respon.', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Operaciones', 'numero' => '3');
        
        $titulo_modificar = $this->tituloModificar;
        $titulo_anular  = $this->tituloAnular;
        $titulo_ver       = $this->tituloVer;
        $ruta             = $this->rutas;
        $totalfac = 0;

        foreach ($lista as $key => $value3) {
            $totalfac+=$value3->total;
        }

        if (count($lista) > 0) {
            $clsLibreria     = new Libreria();
            $paramPaginacion = $clsLibreria->generarPaginacion($lista, $pagina, $filas, $entidad);
            $paginacion      = $paramPaginacion['cadenapaginacion'];
            $inicio          = $paramPaginacion['inicio'];
            $fin             = $paramPaginacion['fin'];
            $paginaactual    = $paramPaginacion['nuevapagina'];
            $lista           = $resultado->paginate($filas);
            $request->replace(array('page' => $paginaactual));
            return view($this->folderview.'.list')->with(compact('lista', 'totalfac', 'paginacion', 'inicio', 'fin', 'entidad', 'cabecera', 'titulo_modificar', 'titulo_anular', 'ruta', 'titulo_ver'));
        }
        return view($this->folderview.'.list')->with(compact('lista', 'entidad'));
    }

    public function index()
    {
        $entidad          = 'Cotizacion';
        $title            = $this->tituloAdmin;
        $titulo_registrar = $this->tituloRegistrar;
        $ruta             = $this->rutas;
        $user = Auth::user();
        $cotizacion       = null;
        return view($this->folderview.'.admin')->with(compact('entidad', 'title', 'titulo_registrar', 'ruta', 'user', 'cotizacion'));
    }

    public function create(Request $request)
    {
        $listar              = Libreria::getParam($request->input('listar'), 'NO');
        $entidad             = 'Cotizacion';
        $cotizacion = null;
        $cboTipoServicio = array(""=>"--Todos--");
        $tiposervicio = Tiposervicio::where(DB::raw('1'),'=','1')->orderBy('nombre','ASC')->get();
        foreach ($tiposervicio as $key => $value) {
            $cboTipoServicio = $cboTipoServicio + array($value->id => $value->nombre);
        }
        $formData            = array('cotizacion.store');
        $user = Auth::user();
        
        $formData            = array('route' => $formData, 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton               = 'Registrar'; 
        return view($this->folderview.'.mant')->with(compact('cotizacion', 'formData', 'entidad', 'boton', 'listar', 'cboTipoServicio', 'user'));
    }

    public function store(Request $request)
    {
        $listar     = Libreria::getParam($request->input('listar'), 'NO');
        $reglas     = array(
                'fecharegistro'  => 'required',
                //'paciente'     => 'required',
                'total'          => 'required',
                'codigoregistro' => 'required',
                'plan_id'        => 'required',
                );
        $mensajes = array(
            'fecharegistro.required' => 'Debe seleccionar una fecha',
            //'paciente.required'      => 'Debe seleccionar un paciente',
            'total.required'         => 'Debe agregar un monto a la cotización',
            'codigoregistro.required'        => 'Debe agregar un código',
            'plan_id.required'        => 'Debe seleccionar un plan',
            );
        $validacion = Validator::make($request->all(), $reglas, $mensajes);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        }       
        $user = Auth::user();
        $dat=array();
        $numerocotizacion = Cotizacion::NumeroSigue();
        $error = DB::transaction(function() use($request,$user,$numerocotizacion,&$dat){
            $cotizacion        = new Cotizacion();
            $cotizacion->fecha = $request->input('fecharegistro');
            $cotizacion->numero= $numerocotizacion;
            $cotizacion->situacion='E';//ENVIADA
            $cotizacion->responsable_id=$user->person_id;
            $cotizacion->plan_id = $request->input('plan_id');
            //$cotizacion->paciente_id = $request->input('person_id');
            $cotizacion->total=$request->input('total');  
            $cotizacion->tipo=$request->input('tiporegistro');  
            $cotizacion->codigo=$request->input('codigoregistro');  
            $cotizacion->save();

            $pagohospital=0;
            $arr=explode(",",$request->input('listServicio'));
            for($c=0;$c<count($arr);$c++){
                $Detalle = new Detallecotizacion();
                $Detalle->cotizacion_id=$cotizacion->id;
                if($request->input('txtIdTipoServicio'.$arr[$c])!="0"){
                    $Detalle->servicio_id=$request->input('txtIdServicio'.$arr[$c]);
                    $Detalle->descripcion=trim($request->input('txtServicio'.$arr[$c]));
                }else{
                    $Detalle->servicio_id=null;
                    $Detalle->descripcion=trim($request->input('txtServicio'.$arr[$c]));
                }
                //$Detalle->doctor_id=$request->input('txtIdMedico'.$arr[$c]);
                //$Detalle->cantidad=$request->input('txtCantidad'.$arr[$c]);
                //$Detalle->precio=round($request->input('txtPrecio'.$arr[$c]),2);
                $Detalle->save();
            }
            
            $dat[0]=array("respuesta"=>"OK","id"=>$cotizacion->id);
        });
        return is_null($error) ? json_encode($dat) : $error;
    }

    public function edit($id, Request $request)
    {
        $existe = Libreria::verificarExistencia($id, 'cotizacion');
        if ($existe !== true) {
            return $existe;
        }
        $listar           = Libreria::getParam($request->input('listar'), 'NO');
        $entidad          = 'Cotizacion';
        $cotizacion       = Cotizacion::find($id);
        $cboTipoServicio  = array(""=>"--Todos--");
        $tiposervicio     = Tiposervicio::where(DB::raw('1'),'=','1')->orderBy('nombre','ASC')->get();
        foreach ($tiposervicio as $key => $value) {
            $cboTipoServicio = $cboTipoServicio + array($value->id => $value->nombre);
        }
        $user = Auth::user();
        $formData            = array('cotizacion.update', $id);
        $formData            = array('route' => $formData, 'method' => 'PUT', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton               = 'Modificar'; 
        return view($this->folderview.'.mant')->with(compact('cotizacion', 'formData', 'entidad', 'boton', 'listar', 'cboTipoServicio', 'user'));        
    }

    public function update(Request $request, $id)
    {
        $existe = Libreria::verificarExistencia($id, 'cotizacion');
        if ($existe !== true) {
            return $existe;
        }
        $listar = Libreria::getParam($request->input('listar'), 'NO');
        $reglas = array(
                'fecharegistro' => 'required',
                //'paciente'      => 'required',
                'total'         => 'required',
                'codigoregistro'        => 'required',
                'plan_id'        => 'required',
                );
        $mensajes = array(
            'fecharegistro.required' => 'Debe seleccionar una fecha',
            //'paciente.required'      => 'Debe seleccionar un paciente',
            'total.required'         => 'Debe agregar un monto a la cotización',
            'codigoregistro.required'        => 'Debe agregar un código',
            'plan_id.required'        => 'Debe agregar un Plan',
            );
        $validacion = Validator::make($request->all(), $reglas, $mensajes);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        }       
        $user = Auth::user();
        $dat=array();
        $error = DB::transaction(function() use($request,$user,$id,&$dat){
            $cotizacion        = Cotizacion::find($id);
            $cotizacion->fecha = $request->input('fecharegistro');
            $cotizacion->situacion='E';//ENVIADA
            $cotizacion->responsable_id=$user->person_id;
            $cotizacion->plan_id = $request->input('plan_id');
            //$cotizacion->paciente_id = $request->input('person_id');
            $cotizacion->total=$request->input('total');  
            $cotizacion->tipo=$request->input('tiporegistro');  
            $cotizacion->codigo=$request->input('codigoregistro');  
            $cotizacion->save();

            $pagohospital=0;
            foreach ($cotizacion->detalles as $key => $value) {
                $value->delete();
            }
            $arr=explode(",",$request->input('listServicio'));
            for($c=0;$c<count($arr);$c++){                
                $Detalle = new Detallecotizacion();
                $Detalle->cotizacion_id=$cotizacion->id;
                $Detalle->descripcion=trim($request->input('txtServicio'.$arr[$c]));
                //$Detalle->doctor_id=$request->input('txtIdMedico'.$arr[$c]);
                //$Detalle->cantidad=$request->input('txtCantidad'.$arr[$c]);
                //$Detalle->precio=round($request->input('txtPrecio'.$arr[$c]),2);
                $Detalle->save();
            }
            
            $dat[0]=array("respuesta"=>"OK","id"=>$cotizacion->id);
        });
        return is_null($error) ? json_encode($dat) : $error;
    }

    public function destroy($id)
    {
        $existe = Libreria::verificarExistencia($id, 'cotizacion');
        if ($existe !== true) {
            return $existe;
        }
        $error = DB::transaction(function() use($id){
            $cotizacion = Cotizacion::find($id);
            $cotizacion->situacion = 'U';
            $cotizacion->save();
        });
        return is_null($error) ? "OK" : $error;
    }

    public function eliminar($id, $listarLuego)
    {
        $existe = Libreria::verificarExistencia($id, 'cotizacion');
        if ($existe !== true) {
            return $existe;
        }
        $listar = "NO";
        if (!is_null(Libreria::obtenerParametro($listarLuego))) {
            $listar = $listarLuego;
        }
        $modelo   = Cotizacion::find($id);
        $entidad  = 'Cotizacion';
        $formData = array('route' => array('cotizacion.destroy', $id), 'method' => 'DELETE', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Anular';
        return view('app.confirmarEliminar')->with(compact('modelo', 'formData', 'entidad', 'boton', 'listar'));
    }

    public function generarNumero(Request $request){
        $serie = $request->input('serie') + 0;
        if($serie==8){
            $numeroventa = Movimiento::NumeroSigue2(9,17,$serie,'N');
        }else{
            $numeroventa = Movimiento::NumeroSigue(9,17,$serie,'N');
        }
        echo $numeroventa;
    }

    public function buscarservicio(Request $request)
    {
        $descripcion = $request->input("descripcion");
        $idtiposervicio = trim($request->input("idtiposervicio"));
        $tipopago = 'Convenio';
        $resultado = Servicio::leftjoin('tarifario','tarifario.id','=','servicio.tarifario_id');
        $resultado = $resultado->where(DB::raw('trim(concat(tarifario.codigo,\' \',servicio.nombre,\' \',tarifario.nombre))'),'LIKE','%'.$descripcion.'%');
        if(trim($idtiposervicio)!=""){
            $resultado = $resultado->where('tiposervicio_id','=',$idtiposervicio);
        }
        $resultado    = $resultado->where('tipopago','LIKE',''.strtoupper($tipopago).'')->select('servicio.*','tarifario.nombre as tarifario','tarifario.codigo')->get();
        if(count($resultado)>0){
            $c=0;
            foreach ($resultado as $key => $value){
                if(strpos($value->nombre, 'CONS ') !== false){
                    $otro = '390101';
                } else {
                    $otro = '-';
                }
                $data[$c] = array(
                            'servicio' => ($value->tipopago=='Convenio')?$value->tarifario:$value->nombre,
                            'codigo' => ($value->tipopago=='Convenio')?$value->codigo:$otro,
                            'tiposervicio' => $value->tiposervicio->nombre,
                            'precio' => $value->precio,
                            'idservicio' => $value->id,
                        );
                        $c++;                
            } 
        }else{
            if($tipopago=='Convenio' && ($idtiposervicio=='' || $idtiposervicio==1)){//buscar consultas con precio de convenio
                $resultado = Servicio::where(DB::raw('trim(servicio.nombre)'),'LIKE','%'.$descripcion.'%')
                            ->where('tipopago','LIKE','Particular')
                            ->where('tiposervicio_id','=','1')->get();
                if(count($resultado)>0){
                    $c=0;
                    foreach ($resultado as $key => $value){
                        if(strpos($value->nombre, 'CONS ') !== false){
                            $otro = '390101';
                        } else {
                            $otro = '-';
                        }
                        //COSTO DE CONSULTA
                        //SALUDPOL 5
                        //$plan = Plan::find($request->input('plan_id'));
                        $plan = Plan::find(5);
                        $data[$c] = array(
                                    'servicio' => ($value->tipopago=='Convenio')?$value->tarifario:$value->nombre,
                                    'codigo' => ($value->tipopago=='Convenio')?$value->codigo:$otro,
                                    'tiposervicio' => $value->tiposervicio->nombre,
                                    'precio' => $plan->consulta,
                                    'idservicio' => $value->id,
                                );
                                $c++;                
                    }            
                }else{
                    $data = array();    
                }
            }else{
                $data = array();
            }
        }
        return json_encode($data);
    }

    public function ver($id) {
        $entidad = "Cotizacion";
        $existe = Libreria::verificarExistencia($id, 'cotizacion');
        if ($existe !== true) {
            return $existe;
        }        
        $cotizacion = Cotizacion::find($id);
        if($cotizacion->tipo=='A') {$tipo = 'AMBULATORIO';}elseif($cotizacion->tipo=='H') {$tipo = 'HOSPITALARIO';}
        if($cotizacion->situacion=='E') {$situacion = 'ENVIADA';}elseif($cotizacion->situacion=='A') {$situacion = 'ACEPTADA';}elseif($cotizacion->situacion=='O') {$situacion = 'OBSERVADA';}elseif($cotizacion->situacion=='R') {$situacion = 'RECHAZADA';} 
        $formData  = array('class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        return view($this->folderview.'.ver')->with(compact('entidad', 'cotizacion', 'formData', 'tipo', 'situacion'));
    }

    public function seleccionarservicio(Request $request)
    {
        $resultado = Servicio::find($request->input('idservicio'));
        if($resultado->modo=="Monto"){
            $pagohospital=$resultado->pagohospital;
            $pagomedico=$resultado->pagodoctor;
        }else{
            $pagohospital=number_format($resultado->pagohospital*$resultado->precio/100,2,'.','');
            $pagomedico=number_format($resultado->pagodoctor*$resultado->precio/100,2,'.','');
        }
        if($request->input('plan_id')>0){
            $plan = Plan::find($request->input('plan_id'));
            if($resultado->tiposervicio_id==1){//CONSULTA
                $data[0] = array(
                    'servicio' => ($resultado->tipopago=='Convenio')?$resultado->tarifario->nombre:$resultado->nombre,
                    'codigo' => '',
                    'tiposervicio' => $resultado->tiposervicio->nombre,
                    'precio' => $plan->consulta,
                    'id' => $resultado->id,
                    'idservicio' => "20".rand(0,1000).$resultado->id,
                    'preciohospital' => $plan->consulta,
                    'preciomedico' => 0,
                    'modo' => $resultado->modo,
                    'idtiposervicio' => $resultado->tiposervicio_id,
                );
            }else{
                $data[0] = array(
                    'servicio' => ($resultado->tipopago=='Convenio')?$resultado->tarifario->nombre:$resultado->nombre,
                    'tiposervicio' => $resultado->tiposervicio->nombre,
                    'codigo' => $resultado->tarifario->codigo,
                    'precio' => round($resultado->precio/1.18,2),
                    'id' => $resultado->id,
                    'idservicio' => "20".rand(0,1000).$resultado->id,
                    'preciohospital' => round($resultado->precio/1.18,2),
                    'preciomedico' => 0,
                    'modo' => $resultado->modo,
                    'idtiposervicio' => $resultado->tiposervicio_id,
                );
            }
        }
        return json_encode($data);
    }
}
