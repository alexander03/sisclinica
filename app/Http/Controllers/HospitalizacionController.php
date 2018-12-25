<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Validator;
use App\Http\Requests;
use App\Hospitalizacion;
use App\Habitacion;
use App\Hojacosto;
use App\Tipohabitacion;
use App\Piso;
use App\Movimiento;
use App\Convenio;
use App\Salaoperacion;
use App\Librerias\Libreria;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Elibyy\TCPDF\Facades\TCPDF;
use Excel;

class HospitalizacionController extends Controller
{

    protected $folderview      = 'app.hospitalizacion';
    protected $tituloAdmin     = 'Hospitalizacion';
    protected $tituloRegistrar = 'Registrar Hospitalizacion';
    protected $tituloModificar = 'Modificar Hospitalizacion';
    protected $tituloEliminar  = 'Eliminar Hospitalizacion';
    protected $rutas           = array('create' => 'hospitalizacion.create', 
            'edit'   => 'hospitalizacion.edit', 
            'delete' => 'hospitalizacion.eliminar',
            'search' => 'hospitalizacion.buscar',
            'index'  => 'hospitalizacion.index',
            'alta'   => 'hospitalizacion.alta',
        );


     /**
     * Create a new controller instance.
     *
     * @return void
     */
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
        $entidad          = 'Hospitalizacion';
        $fechainicio      = Libreria::getParam($request->input('fechainicio'));
        $fechafin         = Libreria::getParam($request->input('fechafin'));
        $fechainicio2      = Libreria::getParam($request->input('fechaingresoinicio'));
        $fechafin2         = Libreria::getParam($request->input('fechaingresofin'));
        $paciente         = Libreria::getParam($request->input('paciente'));
        $tipoHospitalizacion   = Libreria::getParam($request->input('tipoHospitalizacion'));
        $resultado        = Hospitalizacion::join('habitacion as h','h.id','=','hospitalizacion.habitacion_id')
                            ->join('historia','historia.id','=','hospitalizacion.historia_id')
                            ->join('person as paciente','paciente.id','=','historia.person_id')
                            ->where(DB::raw('concat(paciente.apellidopaterno,\' \',paciente.apellidomaterno,\' \',paciente.nombres)'), 'LIKE', '%'.strtoupper($paciente).'%');
        if($request->input('tipopaciente')!=''){
            $resultado = $resultado->where('historia.tipopaciente','like',$request->input('tipopaciente'));
        }
        if($request->input('solo')!=""){
            $resultado = $resultado->where('hospitalizacion.situacion','=',$request->input('solo'));
        }

        if($request->input('solo')=='A' || $request->input('solo')==''){
            if($fechainicio!=""){
                $resultado= $resultado->where('hospitalizacion.fechaalta','>=',''.$fechainicio.'');
            }
            if($fechafin!=""){
                $resultado= $resultado->where('hospitalizacion.fechaalta','<=',''.$fechafin.'');
            }
            if($fechainicio2!=""){
                $resultado= $resultado->where('hospitalizacion.fecha','>=',''.$fechainicio2.'');
            }
            if($fechafin2!=""){
                $resultado= $resultado->where('hospitalizacion.fecha','<=',''.$fechafin2.'');
            }
            $resultado= $resultado->orderBy('hospitalizacion.fecha','asc');
        }else{
            $resultado= $resultado->orderBy('h.nombre','asc');
        }        
        $lista            = $resultado->select('hospitalizacion.*','historia.numero as numerohistoria',DB::raw('concat(paciente.apellidopaterno,\' \',paciente.apellidomaterno,\' \',paciente.nombres) as paciente2'),'historia.tipopaciente')->get();
        $cabecera         = array();
        $cabecera[]       = array('valor' => 'Cama', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Paciente', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Historia', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Fecha de Ingreso', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Hora', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Tipo Paciente', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Medico', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Paquete', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Tipo Tratamiento', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Situacion', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Fecha Alta', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Usuario Alta', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Operaciones', 'numero' => '3');
        
        $user = Auth::user();
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
            return view($this->folderview.'.list')->with(compact('lista', 'paginacion', 'inicio', 'fin', 'entidad', 'cabecera', 'titulo_modificar', 'titulo_eliminar', 'ruta', 'user'));
        }
        return view($this->folderview.'.list')->with(compact('lista', 'entidad', 'user'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $entidad          = 'Hospitalizacion';
        $title            = $this->tituloAdmin;
        $titulo_registrar = $this->tituloRegistrar;
        $ruta             = $this->rutas;
        $cboSolo          = array('H' => 'Hospitalizados','A' => 'Alta', '' => 'Todos');
        $cboTipoPaciente  = array('' => 'Todos...', 'Convenio' => 'Convenio', 'Particular' => 'Particular');
        $resultado        = Habitacion::leftjoin('hospitalizacion','hospitalizacion.id','=','habitacion.hospitalizacion_id')
                            ->join('piso','piso.id','=','habitacion.piso_id')
                            ->select('habitacion.*')->orderBy('piso.nombre','asc')->orderBy('habitacion.nombre','asc');
        $lista           = $resultado->get();
        return view($this->folderview.'.admin')->with(compact('entidad', 'title', 'titulo_registrar', 'ruta', 'lista', 'cboSolo', 'cboTipoPaciente'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $listar   = Libreria::getParam($request->input('listar'), 'NO');
        $entidad  = 'Hospitalizacion';
        $hospitalizacion = null;
        $formData = array('hospitalizacion.store');
        $habitacion = Habitacion::find($request->input('habitacion_id'));
        $cboModo  = array('Tratamiento Quirurgico' => 'Tratamiento Quirurgico','Tratamiento Medico' => 'Tratamiento Medico');
        $cboPaquete = array('Ninguno'=>'Ninguno','Medio'=>'Medio','Completo'=>'Completo');
        $formData = array('route' => $formData, 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Registrar'; 
        return view($this->folderview.'.mant')->with(compact('hospitalizacion', 'formData', 'entidad', 'boton', 'listar','habitacion', 'cboModo', 'cboPaquete'));
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
        $reglas     = array('paciente' => 'required|max:100');
        $mensajes = array(
            'paciente.required'         => 'Debe ingresar un paciente'
            );
        $validacion = Validator::make($request->all(), $reglas, $mensajes);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        }

        $user = Auth::user();
        $error = DB::transaction(function() use($request,$user){
            $Hospitalizacion       = new Hospitalizacion();
            $Hospitalizacion->historia_id = strtoupper($request->input('historia_id'));
            $Hospitalizacion->fecha = $request->input('fecha');
            $Hospitalizacion->habitacion_id = $request->input('habitacion_id');
            $Hospitalizacion->hora = $request->input('hora');
            $Hospitalizacion->modo = $request->input('modo');
            $Hospitalizacion->paquete = $request->input('paquete');
            $Hospitalizacion->medico_id = $request->input('medico_id');
            $Hospitalizacion->usuario_id = $user->person_id;
            $Hospitalizacion->situacion='H';
            $Hospitalizacion->movimientoinicial_id = Movimiento::max('id');
            $Hospitalizacion->save();
            $Habitacion = Habitacion::find($request->input('habitacion_id'));
            $Habitacion->hospitalizacion_id=$Hospitalizacion->id;
            $Habitacion->situacion='O';
            $Habitacion->save();
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
        $existe = Libreria::verificarExistencia($id, 'hospitalizacion');
        if ($existe !== true) {
            return $existe;
        }
        $listar   = Libreria::getParam($request->input('listar'), 'NO');
        $hospitalizacion = Hospitalizacion::find($id);
        $entidad  = 'Hospitalizacion';
        $habitacion = Habitacion::find($hospitalizacion->habitacion_id);
        $habitacion2 = Habitacion::where('situacion','like','D')->orderBy('nombre','asc')->get();
        $cboHabitacion = array($habitacion->id => $habitacion->nombre);
        foreach ($habitacion2 as $key => $value) {
            $cboHabitacion = $cboHabitacion + array($value->id => $value->nombre);
        }
        $cboModo  = array('Tratamiento Quirurgico' => 'Tratamiento Quirurgico','Tratamiento Medico' => 'Tratamiento Medico');
        $cboPaquete = array('Ninguno'=>'Ninguno','Medio'=>'Medio','Completo'=>'Completo');
        $formData = array('hospitalizacion.update', $id);
        $formData = array('route' => $formData, 'method' => 'PUT', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Modificar';
        return view($this->folderview.'.mant')->with(compact('hospitalizacion', 'formData', 'entidad', 'boton', 'listar', 'habitacion', 'cboModo', 'cboPaquete', 'cboHabitacion'));
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
        $existe = Libreria::verificarExistencia($id, 'Hospitalizacion');
        if ($existe !== true) {
            return $existe;
        }
        $reglas     = array('paciente' => 'required|max:100');
        $mensajes = array(
            'paciente.required'         => 'Debe ingresar un paciente'
            );
        $validacion = Validator::make($request->all(), $reglas, $mensajes);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        } 
        $error = DB::transaction(function() use($request, $id){
            $Hospitalizacion       = Hospitalizacion::find($id);

            $Habitacion = Habitacion::find($Hospitalizacion->habitacion_id);
            $Habitacion->situacion='D';
            $Habitacion->save();
            
            $Hospitalizacion->historia_id = strtoupper($request->input('historia_id'));
            $Hospitalizacion->fecha = $request->input('fecha');
            $Hospitalizacion->habitacion_id = $request->input('habitacion_id');
            $Hospitalizacion->hora = $request->input('hora');
            $Hospitalizacion->modo = $request->input('modo');
            $Hospitalizacion->paquete = $request->input('paquete');
            $Hospitalizacion->medico_id = $request->input('medico_id');
            $Hospitalizacion->save();

            $Habitacion = Habitacion::find($request->input('habitacion_id'));
            $Habitacion->hospitalizacion_id=$Hospitalizacion->id;
            $Habitacion->situacion='O';
            $Habitacion->save();
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
        $existe = Libreria::verificarExistencia($id, 'hospitalizacion');
        if ($existe !== true) {
            return $existe;
        }
        $error = DB::transaction(function() use($id){
            $Hospitalizacion = Hospitalizacion::find($id);
            $Habitacion = Habitacion::find($Hospitalizacion->habitacion_id);
            $Habitacion->situacion='D';
            $Habitacion->save();
            $Hospitalizacion->delete();
        });
        return is_null($error) ? "OK" : $error;
    }

    public function eliminar($id, $listarLuego)
    {
        $existe = Libreria::verificarExistencia($id, 'hospitalizacion');
        if ($existe !== true) {
            return $existe;
        }
        $listar = "NO";
        if (!is_null(Libreria::obtenerParametro($listarLuego))) {
            $listar = $listarLuego;
        }
        $modelo   = Hospitalizacion::find($id);
        $entidad  = 'Hospitalizacion';
        $formData = array('route' => array('hospitalizacion.destroy', $id), 'method' => 'DELETE', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Eliminar';
        return view('app.confirmarEliminar')->with(compact('modelo', 'formData', 'entidad', 'boton', 'listar', 'id'));
    }

    public function aceptalta(Request $request)
    {
        $existe = Libreria::verificarExistencia($request->input('id'), 'hospitalizacion');
        if ($existe !== true) {
            return $existe;
        }
        $user = Auth::user();
        $error = DB::transaction(function() use($request,$user){
            $Hospitalizacion = Hospitalizacion::find($request->input('id'));
            $Hospitalizacion->fechaalta=$request->input('fechaalta');
            $Hospitalizacion->usuarioalta_id=$user->person_id;
            $Hospitalizacion->movimientofinal_id = Movimiento::max('id');
            $Hospitalizacion->situacion='A';
            $Hospitalizacion->save();
            $Hoja = Hojacosto::where('hospitalizacion_id','=',$request->input('id'))->first();
            if(!is_null($Hoja)){
                $Hoja->situacion = 'F';
                $Hoja->movimientofinal_id = $Hospitalizacion->movimientofinal_id;
                $Hoja->save();
            }
            $Habitacion = Habitacion::find($Hospitalizacion->habitacion_id);
            $Habitacion->situacion='D';
            $Habitacion->save();
        });
        return is_null($error) ? "OK" : $error;
    }

    public function alta($id, $listarLuego)
    {
        $existe = Libreria::verificarExistencia($id, 'hospitalizacion');
        if ($existe !== true) {
            return $existe;
        }
        $listar = "NO";
        if (!is_null(Libreria::obtenerParametro($listarLuego))) {
            $listar = $listarLuego;
        }
        $entidad = 'Hospitalizacion';
        $hospitalizacion   = Hospitalizacion::find($id);
        $habitacion = Habitacion::find($hospitalizacion->habitacion_id);
        $formData = array('route' => array('hospitalizacion.aceptalta', $id), 'method' => 'Acept', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Aceptar';
        return view($this->folderview.'.alta')->with(compact('hospitalizacion', 'formData', 'entidad', 'boton', 'listar', 'habitacion', 'entidad'));
    }

    public function pdfHospitalizados($tipo, $alta, $fi, $ff){
        $signo = '=';
        if ($tipo == 'Todos') {
            $signo = '!=';
        }
        $resultado        = Hospitalizacion::join('habitacion as h','h.id','=','hospitalizacion.habitacion_id')
                            ->join('historia','historia.id','=','hospitalizacion.historia_id')
                            ->join('person as paciente','paciente.id','=','historia.person_id')
                            
                            ->where('hospitalizacion.situacion','=',$alta)
                            ->where('historia.tipopaciente',$signo,$tipo)
                            ->where('hospitalizacion.fecha','<=',$ff);
                            
        $lista            = $resultado->select('hospitalizacion.*','historia.numero as numerohistoria',DB::raw('concat(paciente.apellidopaterno,\' \',paciente.apellidomaterno,\' \',paciente.nombres) as paciente2'),'historia.tipopaciente')->orderBy('h.nombre','asc')->get();            
        $pdf = new TCPDF();
        $pdf::SetTitle('Hospitalizacion');
        $pdf::AddPage('L');
        $pdf::Image("http://localhost:81/clinica/dist/img/logo2-ojos.jpg", 10, 8, 50);
        $pdf::Cell(65);;
        $pdf::Ln();
        $pdf::SetFont('helvetica','B',12);
        $pdf::Cell(0,10,utf8_decode("Reporte de Hospitalizados al ".date("d/m/Y")),0,0,'C');
        $pdf::Ln();
        $pdf::SetFillColor(68,164,168);
        $pdf::SetFont('helvetica','B',9);
        $pdf::Cell(15,7,utf8_decode("CAMA"),1,0,'C',1);
        $pdf::Cell(70,7,utf8_decode("PACIENTE"),1,0,'C',1);
        $pdf::Cell(17,7,utf8_decode("HISTORIA"),1,0,'C',1);
        $pdf::Cell(19,7,utf8_decode("FECHA ING."),1,0,'C',1);
        $pdf::Cell(13,7,utf8_decode("HORA"),1,0,'C',1);
        $pdf::Cell(20,7,utf8_decode("TIPO PAC."),1,0,'C',1);
        $pdf::Cell(60,7,utf8_decode("MEDICO"),1,0,'C',1);
        $pdf::Cell(24,7,utf8_decode("PAQUETE"),1,0,'C',1);
        $pdf::Cell(30,7,utf8_decode("TIPO TRATAT."),1,0,'C',1);
        $pdf::Ln();
        $pdf::SetFont('helvetica','',8);
        foreach ($lista as $key => $value){
            $pdf::Cell(15,7,utf8_decode($value->habitacion->nombre),1,0,'C');
            $pdf::Cell(70,7,($value->paciente2),1,0,'L');
            $pdf::Cell(17,7,utf8_decode($value->numerohistoria),1,0,'C');
            $pdf::Cell(19,7,date("d/m/Y",strtotime($value->fecha)),1,0,'C');
            $pdf::Cell(13,7,substr($value->hora,0,5),1,0,'C');
            $pdf::Cell(20,7,utf8_decode($value->tipopaciente),1,0,'C');
            if($value->medico_id>0 && !is_null($value->medico)){
                if(strlen($value->medico->apellidopaterno.' '.$value->medico->apellidomaterno.' '.$value->medico->nombres)>27){
                    $x=$pdf::GetX();
                    $y=$pdf::GetY();                    
                    $pdf::Multicell(60,3,($value->medico->apellidopaterno.' '.$value->medico->apellidomaterno.' '.$value->medico->nombres),0,'L');
                    $pdf::SetXY($x,$y);
                    $pdf::Cell(60,7,"",1,0,'C');
                }else{
                    $pdf::Cell(60,7,($value->medico->apellidopaterno.' '.$value->medico->apellidomaterno.' '.$value->medico->nombres),1,0,'L');    
                }
            }else{
                $pdf::Cell(60,7,'-',1,0,'C');
            }
            $pdf::Cell(24,7,$value->paquete ,1,0,'C');
            $pdf::Cell(30,7,$value->modo  ,1,0,'C');
            $pdf::Ln();
        }
    
        $pdf::Output('ReciboCaja.pdf');
        
    }

    public function excel(Request $request){
        setlocale(LC_TIME, 'spanish');
        $fechainicio      = Libreria::getParam($request->input('fechainicio'));
        $fechafin         = Libreria::getParam($request->input('fechafin'));
        $fechainicio2      = Libreria::getParam($request->input('fechaingresoinicio'));
        $fechafin2         = Libreria::getParam($request->input('fechaingresofin'));
        $paciente         = Libreria::getParam($request->input('paciente'));
        $tipoHospitalizacion   = Libreria::getParam($request->input('tipoHospitalizacion'));
        $resultado        = Hospitalizacion::join('habitacion as h','h.id','=','hospitalizacion.habitacion_id')
                            ->join('historia','historia.id','=','hospitalizacion.historia_id')
                            ->join('person as paciente','paciente.id','=','historia.person_id')
                            ->where(DB::raw('concat(paciente.apellidopaterno,\' \',paciente.apellidomaterno,\' \',paciente.nombres)'), 'LIKE', '%'.strtoupper($paciente).'%');
        if($request->input('tipopaciente')!=''){
            $resultado = $resultado->where('historia.tipopaciente','like',$request->input('tipopaciente'));
        }
        if($request->input('solo')!=""){
            $resultado = $resultado->where('hospitalizacion.situacion','=',$request->input('solo'));
        }
        if($request->input('solo')=='A' || $request->input('solo')==''){
            if($fechainicio!=""){
                $resultado= $resultado->where('hospitalizacion.fechaalta','>=',''.$fechainicio.'');
            }
            if($fechafin!=""){
                $resultado= $resultado->where('hospitalizacion.fechaalta','<=',''.$fechafin.'');
            }
            if($fechainicio2!=""){
                $resultado= $resultado->where('hospitalizacion.fecha','>=',''.$fechainicio2.'');
            }
            if($fechafin2!=""){
                $resultado= $resultado->where('hospitalizacion.fecha','<=',''.$fechafin2.'');
            }
            $resultado= $resultado->orderBy('hospitalizacion.fecha','asc');
        }else{
            $resultado= $resultado->orderBy('h.nombre','asc');
        }        
        $resultado            = $resultado->select('hospitalizacion.*','historia.numero as numerohistoria',DB::raw('concat(paciente.apellidopaterno,\' \',paciente.apellidomaterno,\' \',paciente.nombres) as paciente2'),'historia.tipopaciente','historia.convenio_id')->get();


        Excel::create('ExcelHospitalizacion', function($excel) use($resultado,$request) {
 
            $excel->sheet('Hospitalizacion', function($sheet) use($resultado,$request) {
 
                $c = 1;
                $cabecera = array();
                $cabecera[] = "Cama";
                $cabecera[] = "Paciente";
                $cabecera[] = "Historia";
                $cabecera[] = "Fecha Ingreso";
                $cabecera[] = "Hora";
                $cabecera[] = "Tipo Pac.";
                $cabecera[] = "Plan";
                $cabecera[] = "Medico";
                $cabecera[] = "Paquete";
                $cabecera[] = "Tipo Tratamiento";
                $cabecera[] = "Situacion";
                $cabecera[] = "Fecha Alta";
                $cabecera[] = "Usuario Alta";
                $sheet->row($c,$cabecera);
                $c=$c+1;$d=3;$band=true;


                foreach ($resultado as $key => $value){
                    $detalle = array();
                    $detalle[] = $value->habitacion->nombre;
                    $detalle[] = $value->paciente2;
                    $detalle[] = $value->numerohistoria;
                    $detalle[] = date('d/m/Y',strtotime($value->fecha));
                    $detalle[] = substr($value->hora,0,5);
                    $detalle[] = $value->tipopaciente;
                    if($value->convenio_id>0){
                        $convenio = Convenio::find($value->convenio_id);
                        $detalle[] = isset($convenio->plan->nombre) ? $convenio->plan->nombre : '';
                    }else{
                        $detalle[] = 'PARTICULAR';
                    }
                    if($value->medico_id>0)
                        $detalle[] = $value->medico->apellidopaterno.' '.$value->medico->apellidomaterno.' '.$value->medico->nombres;
                    else
                        $detalle[] = '-';
                    $detalle[] = $value->paquete;
                    $detalle[] = $value->modo;
                    $detalle[] = $value->situacion=='H'?'HOSPITALIZADO':'ALTA';
                    if($value->usuarioalta_id>0){
                        $detalle[] = date('d/m/Y',strtotime($value->fechaalta));
                        $detalle[] = $value->usuarioalta->nombres;
                    }else{
                        $detalle[] = '-';
                        $detalle[] = '-';
                    }
                    $sheet->row($c,$detalle);
                    $c=$c+1;
                }

                $res        = Salaoperacion::leftjoin('historia','historia.id','=','salaoperacion.historia_id')
                            ->join('person as doctor', 'doctor.id', '=', 'salaoperacion.medico_id')
                            ->leftjoin('person as usuario', 'usuario.id', '=', 'salaoperacion.usuario_id')
                            ->join('especialidad','especialidad.id','=','doctor.especialidad_id')
                            ->leftjoin('person as paciente', 'paciente.id', '=', 'historia.person_id')
                            ->where('historia.tipopaciente','like',$request->input('tipopaciente'))
                            ->where('salaoperacion.fecha','>=',''.$request->input('fechaingresoinicio').'')
                            ->where('salaoperacion.fecha','<=',''.$request->input('fechaingresofin').'')
                            ->where('salaoperacion.sala_id','=',3)
                            ->select('salaoperacion.*','historia.numero as numerohistoria',DB::raw('concat(paciente.apellidopaterno,\' \',paciente.apellidomaterno,\' \',paciente.nombres) as paciente2'),'historia.tipopaciente','historia.convenio_id','usuario.nombres as usuario2')->get();
                foreach ($res as $key => $value){
                    $detalle = array();
                    $detalle[] = 'SALA PROC.';
                    $detalle[] = $value->paciente2;
                    $detalle[] = $value->numerohistoria;
                    $detalle[] = date('d/m/Y',strtotime($value->fecha));
                    $detalle[] = substr($value->horainicio,0,5);
                    $detalle[] = $value->tipopaciente;
                    if($value->convenio_id>0){
                        $convenio = Convenio::find($value->convenio_id);
                        $detalle[] = $convenio->plan->nombre;
                    }else{
                        $detalle[] = 'PARTICULAR';
                    }
                    if($value->medico_id>0)
                        $detalle[] = $value->medico->apellidopaterno.' '.$value->medico->apellidomaterno.' '.$value->medico->nombres;
                    else
                        $detalle[] = '-';
                    $detalle[] = '';
                    $detalle[] = '';
                    $detalle[] = 'ALTA';
                    $detalle[] = '-';
                    $detalle[] = $value->usuario2;
                    $sheet->row($c,$detalle);
                    $c=$c+1;
                }

            });
        })->export('xls');
    }

    public function cargado(Request $request)
    {
        $user = Auth::user();
        $error = DB::transaction(function() use($request,$user){
            $hospitalizacion = Hospitalizacion::find($request->input('id'));
            $hospitalizacion->descargado = ($request->input('check')=="true"?'S':'N');
            if($request->input('check')=="true"){
                $hospitalizacion->fechadescargo = date("Y-m-d");
                $hospitalizacion->usuariodescargo_id = $user->person_id;
            }
            $hospitalizacion->save();
        });
        return is_null($error) ? "OK" : $error;
    }
}
