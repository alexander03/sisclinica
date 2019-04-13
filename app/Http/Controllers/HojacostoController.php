<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Validator;
use App\Http\Requests;
use App\Historia;
use App\Convenio;
use App\Hojacosto;
use App\Movimiento;
use App\Detallehojacosto;
use App\Person;
use App\Caja;
use App\Tiposervicio;
use App\Servicio;
use App\Plan;
use App\Detalleplan;
use App\Hospitalizacion;
use App\Librerias\Libreria;
use App\Librerias\EnLetras;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Elibyy\TCPDF\Facades\TCPDF;
use Illuminate\Support\Facades\Auth;

class HojacostoController extends Controller
{
    protected $folderview      = 'app.hojacosto';
    protected $tituloAdmin     = 'Hoja de Costo';
    protected $tituloRegistrar = 'Registrar Hoja de Costo';
    protected $tituloModificar = 'Modificar Hoja de Costo';
    protected $tituloEliminar  = 'Eliminar Hoja de Costo';
    protected $rutas           = array('create' => 'hojacosto.create', 
            'edit'   => 'hojacosto.edit', 
            'delete' => 'hojacosto.eliminar',
            'search' => 'hojacosto.buscar',
            'index'  => 'hojacosto.index',
            'pdfListar'  => 'hojacosto.pdfListar',
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
        $entidad          = 'Hojacosto';
        $paciente         = Libreria::getParam($request->input('paciente'),'');
        $fechainicio      = Libreria::getParam($request->input('fechainicio'));
        $fechafin         = Libreria::getParam($request->input('fechafin'));
        $user = Auth::user();

        $resultado        = Hojacosto::join('hospitalizacion','hospitalizacion.id','=','hojacosto.hospitalizacion_id')
                            ->join('historia', 'historia.id', '=', 'hospitalizacion.historia_id')
                            ->join('person as paciente', 'paciente.id', '=', 'historia.person_id')
                            ->leftjoin('person as responsable','responsable.id','=','hojacosto.usuario_id')
                            ->where(DB::raw('concat(paciente.apellidopaterno,\' \',paciente.apellidomaterno,\' \',paciente.nombres)'), 'LIKE', '%'.strtoupper($paciente).'%')
                            ->where('hojacosto.situacion','=',$request->input('solo'));
        if($request->input('solo')=='A'){
            $resultado= $resultado->where('hospitalizacion.fecha','>=',''.$fechainicio.'')
                            ->where('hospitalizacion.fecha','<=',''.$fechafin.'');
        }
        $resultado        = $resultado->select('hojacosto.*',DB::raw('concat(paciente.apellidopaterno,\' \',paciente.apellidomaterno,\' \',paciente.nombres) as paciente'),'responsable.nombres as responsable','hospitalizacion.fecha',DB::raw('historia.numero as historia'))->orderBy('hospitalizacion.fecha', 'DESC');
        $lista            = $resultado->get();
        $cabecera         = array();
        $cabecera[]       = array('valor' => '#', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Fecha', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Historia', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Paciente', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Situacion', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Usuario', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Operaciones', 'numero' => '3');
        
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
            return view($this->folderview.'.list')->with(compact('lista', 'paginacion', 'inicio', 'fin', 'entidad', 'cabecera', 'titulo_modificar', 'titulo_eliminar', 'ruta', 'conf'));
        }
        return view($this->folderview.'.list')->with(compact('lista', 'entidad','conf'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $entidad          = 'Hojacosto';
        $title            = $this->tituloAdmin;
        $titulo_registrar = $this->tituloRegistrar;
        $ruta             = $this->rutas;
        $cboSolo          = array('P' => 'Pendiente','F' => 'Finalizado');
        return view($this->folderview.'.admin')->with(compact('entidad', 'title', 'titulo_registrar', 'ruta', 'cboSolo'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $listar              = Libreria::getParam($request->input('listar'), 'NO');
        $entidad             = 'Hojacosto';
        $hojacosto = null;
        $cboTipoServicio = array(""=>"--Todos--");
        $tiposervicio = Tiposervicio::where(DB::raw('1'),'=','1')->orderBy('nombre','ASC')->get();
        foreach ($tiposervicio as $key => $value) {
            $cboTipoServicio = $cboTipoServicio + array($value->id => $value->nombre);
        }
        $formData            = array('hojacosto.store');
        $cboTipoPaciente     = array("Convenio" => "Convenio", "Particular" => "Particular", "Hospital" => "Hospital");

        $formData            = array('route' => $formData, 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton               = 'Registrar'; 
        return view($this->folderview.'.mant')->with(compact('hojacosto', 'formData', 'entidad', 'boton', 'listar', 'cboTipoPaciente', 'cboConvenio', 'cboTipoServicio'));
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
                'paciente'          => 'required',
                );
        $mensajes = array(
            'paciente.required'         => 'Debe seleccionar un paciente',
            );
        $validacion = Validator::make($request->all(), $reglas, $mensajes);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        }       
        
        $user = Auth::user();
        $dat=array();
        
        $error = DB::transaction(function() use($request,$user,&$dat,&$numeronc){
            $Hoja       = new Hojacosto();
            $Hospitalizacion = Hospitalizacion::find($request->input('hospitalizacion_id'));
            $Hoja->hospitalizacion_id = $request->input('hospitalizacion_id');
            $Hoja->situacion = 'P';
            $Hoja->movimientoinicial_id = $Hospitalizacion->movimientoinicial_id;
            $Hoja->usuario_id=$user->person_id;
            $Hoja->save();

            $arr=explode(",",$request->input('listServicio'));
            for($c=0;$c<count($arr);$c++){
                $Detalle = new Detallehojacosto();
                $Detalle->hojacosto_id=$Hoja->id;
                if($request->input('txtIdTipoServicio'.$arr[$c])!="0"){
                    $Detalle->servicio_id=$request->input('txtIdServicio'.$arr[$c]);
                    $Detalle->descripcion="";
                }else{
                    $Detalle->servicio_id=null;
                    $Detalle->descripcion=trim($request->input('txtServicio'.$arr[$c]));
                }
                $Detalle->persona_id=$request->input('txtIdMedico'.$arr[$c]);
                $Detalle->cantidad=$request->input('txtCantidad'.$arr[$c]);
                $Detalle->precio=$request->input('txtPrecio'.$arr[$c]);
                $Detalle->save();
            }

            $dat[0]=array("respuesta"=>"OK");
        });
        return is_null($error) ? json_encode($dat) : $error;
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
        $existe = Libreria::verificarExistencia($id, 'hojacosto');
        if ($existe !== true) {
            return $existe;
        }
        $listar              = Libreria::getParam($request->input('listar'), 'NO');
        $cboTipoServicio = array(""=>"--Todos--");
        $tiposervicio = Tiposervicio::where(DB::raw('1'),'=','1')->orderBy('nombre','ASC')->get();
        foreach ($tiposervicio as $key => $value) {
            $cboTipoServicio = $cboTipoServicio + array($value->id => $value->nombre);
        }
        $hojacosto = Hojacosto::find($id);
        $entidad             = 'Hojacosto';
        $formData            = array('hojacosto.update', $id);
        $cboTipoPaciente     = array("Particular" => "Particular", "Convenio" => "Convenio");
        $formData            = array('route' => $formData, 'method' => 'PUT', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton               = 'Modificar';
        return view($this->folderview.'.mant')->with(compact('hojacosto', 'formData', 'entidad', 'boton', 'listar', 'cboTipoPaciente', 'cboTipoServicio'));
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
        $existe = Libreria::verificarExistencia($id, 'hojacosto');
        if ($existe !== true) {
            return $existe;
        }
        $reglas     = array(
                'paciente'                  => 'required',
                );
        $mensajes = array(
            'paciente.required'         => 'Debe ingresar un paciente',
            );
        $validacion = Validator::make($request->all(), $reglas, $mensajes);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        }
        $dat= array();
        $error = DB::transaction(function() use($request, $id, &$dat){
            Detallehojacosto::where('hojacosto_id','=',$id)->delete();
            $arr=explode(",",$request->input('listServicio'));
            for($c=0;$c<count($arr);$c++){
                $Detalle = new Detallehojacosto();
                $Detalle->hojacosto_id=$id;
                if($request->input('txtIdTipoServicio'.$arr[$c])!="0"){
                    $Detalle->servicio_id=$request->input('txtIdServicio'.$arr[$c]);
                    $Detalle->descripcion="";
                }else{
                    $Detalle->servicio_id=null;
                    $Detalle->descripcion=trim($request->input('txtServicio'.$arr[$c]));
                }
                $Detalle->persona_id=$request->input('txtIdMedico'.$arr[$c]);
                $Detalle->cantidad=$request->input('txtCantidad'.$arr[$c]);
                $Detalle->precio=$request->input('txtPrecio'.$arr[$c]);
                $Detalle->save();
            }

            $dat[0]=array("respuesta"=>"OK");
            
        });
        return is_null($error) ? json_encode($dat) : $error;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $existe = Libreria::verificarExistencia($id, 'hojacosto');
        if ($existe !== true) {
            return $existe;
        }
        $error = DB::transaction(function() use($id){
            $Hojacosto = Hojacosto::find($id);
            $Hojacosto->delete();
        });
        return is_null($error) ? "OK" : $error;
    }

    public function eliminar($id, $listarLuego)
    {
        $existe = Libreria::verificarExistencia($id, 'hojacosto');
        if ($existe !== true) {
            return $existe;
        }
        $listar = "NO";
        if (!is_null(Libreria::obtenerParametro($listarLuego))) {
            $listar = $listarLuego;
        }
        $modelo   = Movimiento::find($id);
        $entidad  = 'Hojacosto';
        $formData = array('route' => array('hojacosto.destroy', $id), 'method' => 'DELETE', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Eliminar';
        return view('app.confirmarEliminar')->with(compact('modelo', 'formData', 'entidad', 'boton', 'listar'));
    }
    
    public function buscarservicio(Request $request)
    {
        $descripcion = $request->input("descripcion");
        $idtiposervicio = trim($request->input("idtiposervicio"));
        $tipopago = $request->input('tipopaciente');
        $resultado = Servicio::leftjoin('tarifario','tarifario.id','=','servicio.tarifario_id');
        if($tipopago=='Convenio'){
            $resultado = $resultado->where(DB::raw('trim(concat(servicio.nombre,\' \',tarifario.nombre))'),'LIKE','%'.$descripcion.'%')->where('servicio.plan_id','=',$request->input('plan_id'));
        }else{
            $resultado = $resultado->where(DB::raw('trim(servicio.nombre)'),'LIKE','%'.$descripcion.'%');
        }
        if(trim($idtiposervicio)!=""){
            $resultado = $resultado->where('tiposervicio_id','=',$idtiposervicio);
        }
        $resultado    = $resultado->where('tipopago','LIKE',''.strtoupper($tipopago).'')->select('servicio.*','tarifario.nombre as tarifario')->get();
        if(count($resultado)>0){
            $c=0;
            foreach ($resultado as $key => $value){
                $data[$c] = array(
                            'servicio' => ($value->tipopago=='Convenio')?$value->tarifario:$value->nombre,
                            'tiposervicio' => $value->tiposervicio->nombre,
                            'precio' => $value->precio,
                            'idservicio' => $value->id,
                        );
                        $c++;                
            }            
            if($tipopago=='Convenio' && ($idtiposervicio=='' || $idtiposervicio==1)){//buscar consultas con precio de convenio
                $resultado = Servicio::where(DB::raw('trim(servicio.nombre)'),'LIKE','%'.$descripcion.'%')
                            ->where('tipopago','LIKE','Particular')
                            ->where('tiposervicio_id','=','1')->get();
                if(count($resultado)>0){
                    foreach ($resultado as $key => $value){
                        //COSTO DE CONSULTA
                        $plan = Plan::find($request->input('plan_id'));
                        $data[$c] = array(
                                    'servicio' => ($value->tipopago=='Convenio')?$value->tarifario:$value->nombre,
                                    'tiposervicio' => $value->tiposervicio->nombre,
                                    'precio' => $plan->consulta,
                                    'idservicio' => $value->id,
                                );
                                $c++;                
                    }            
                }
            }
        }else{
            if($tipopago=='Convenio' && ($idtiposervicio=='' || $idtiposervicio==1)){//buscar consultas con precio de convenio
                $resultado = Servicio::where(DB::raw('trim(servicio.nombre)'),'LIKE','%'.$descripcion.'%')
                            ->where('tipopago','LIKE','Particular')
                            ->where('tiposervicio_id','=','1')->get();
                if(count($resultado)>0){
                    $c=0;
                    foreach ($resultado as $key => $value){
                        //COSTO DE CONSULTA
                        $plan = Plan::find($request->input('plan_id'));
                        $data[$c] = array(
                                    'servicio' => ($value->tipopago=='Convenio')?$value->tarifario:$value->nombre,
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
        if($request->input('plan_id')>0 && $request->input('tipopaciente')=="Convenio"){
            $plan = Plan::find($request->input('plan_id'));
            if($resultado->tiposervicio_id==1){//CONSULTA
                $data[0] = array(
                    'servicio' => ($resultado->tipopago=='Convenio')?$resultado->tarifario->nombre:$resultado->nombre,
                    'tiposervicio' => $resultado->tiposervicio->nombre,
                    'precio' => $plan->consulta,
                    'idservicio' => $resultado->id,
                    'preciohospital' => $plan->consulta,
                    'preciomedico' => 0,
                    'modo' => $resultado->modo,
                    'idtiposervicio' => $resultado->tiposervicio_id,
                );
            }else{
                $data[0] = array(
                    'servicio' => ($resultado->tipopago=='Convenio')?$resultado->tarifario->nombre:$resultado->nombre,
                    'tiposervicio' => $resultado->tiposervicio->nombre,
                    'precio' => $resultado->precio,
                    'idservicio' => $resultado->id,
                    'preciohospital' => $resultado->precio,
                    'preciomedico' => 0,
                    'modo' => $resultado->modo,
                    'idtiposervicio' => $resultado->tiposervicio_id,
                );
            }
        }elseif($request->input('plan_id')>0 && $request->input('tipopaciente')=="Particular") {
            $plan = Plan::find($request->input('plan_id'));
            if($plan->tipo=='Institucion'){//DESCUENTO PARA LOS CONVENIOS INSTITUCIONALES
                $rs = Detalleplan::where('plan_id','=',$request->input('plan_id'))
                                    ->where('tiposervicio_id','=',$resultado->tiposervicio_id)->get();
                if(count($rs)>0){
                    foreach ($rs as $key => $value) {
                        $precio = number_format($resultado->precio*(100-$value->descuento)/100,2,'.','');
                        $pagohospital = $precio;
                        $pagomedico = 0;   
                    }
                }else{
                    $precio = $resultado->precio;
                }
                /*if($request->input('formapago')=='Tarjeta'){
                    if($request->input('tarjeta')=="CREDITO"){
                        $precio=number_format($precio*1.04,2,'.','');
                    }else{
                        $precio=number_format($precio*1.03,2,'.','');
                    }
                    $pagohospital=$precio - $pagomedico;
                }*/
                $data[0] = array(
                        'servicio' => ($resultado->tipopago=='Convenio')?$resultado->tarifario->nombre:$resultado->nombre,
                        'tiposervicio' => $resultado->tiposervicio->nombre,
                        'precio' => $precio,
                        'idservicio' => $resultado->id,
                        'preciohospital' => $pagohospital,
                        'preciomedico' => $pagomedico,
                        'modo' => $resultado->modo,
                        'idtiposervicio' => $resultado->tiposervicio_id,
                    );
            }else{
                /*if($request->input('formapago')=='Tarjeta'){
                    if($request->input('tarjeta')=="CREDITO"){
                        $precio=number_format($resultado->precio*1.04,2,'.','');
                    }else{
                        $precio=number_format($resultado->precio*1.03,2,'.','');
                    }
                    $pagohospital=$precio - $pagomedico;
                }else{*/
                    $precio=$resultado->precio;
                //}
                $data[0] = array(
                        'servicio' => ($resultado->tipopago=='Convenio')?$resultado->tarifario->nombre:$resultado->nombre,
                        'tiposervicio' => $resultado->tiposervicio->nombre,
                        'precio' => $precio,
                        'idservicio' => $resultado->id,
                        'preciohospital' => $pagohospital,
                        'preciomedico' => $pagomedico,
                        'modo' => $resultado->modo,
                        'idtiposervicio' => $resultado->tiposervicio_id,
                    );
            }
        }else{
            /*if($request->input('formapago')=='Tarjeta'){
                if($request->input('tarjeta')=="CREDITO"){
                    $precio=number_format($resultado->precio*1.04,2,'.','');
                }else{
                    $precio=number_format($resultado->precio*1.03,2,'.','');
                }
                $pagohospital=$precio - $pagomedico;
            }else{*/
                $precio=$resultado->precio;
            //}
            $data[0] = array(
                    'servicio' => ($resultado->tipopago=='Convenio')?$resultado->tarifario->nombre:$resultado->nombre,
                    'tiposervicio' => $resultado->tiposervicio->nombre,
                    'precio' => $precio,
                    'idservicio' => $resultado->id,
                    'preciohospital' => $pagohospital,
                    'preciomedico' => $pagomedico,
                    'modo' => $resultado->modo,
                    'idtiposervicio' => $resultado->tiposervicio_id,
                );
        }
        return json_encode($data);
    }
    
    public function pdfHojacosto(Request $request){
        $entidad          = 'Ticket';
        $id               = Libreria::getParam($request->input('id'),'');
        $resultado        = Hojacosto::join('hospitalizacion','hospitalizacion.id','=','hojacosto.hospitalizacion_id')
                            ->join('historia', 'historia.id', '=', 'hospitalizacion.historia_id')
                            ->join('person as paciente', 'paciente.id', '=', 'historia.person_id')
                            ->leftjoin('person as responsable','responsable.id','=','hojacosto.usuario_id')
                            ->where('hojacosto.id','=',$id);
        $resultado        = $resultado->select('hojacosto.*',DB::raw('concat(paciente.apellidopaterno,\' \',paciente.apellidomaterno,\' \',paciente.nombres) as paciente'),'responsable.nombres as responsable','hospitalizacion.fecha',DB::raw('historia.numero as historia'),'hospitalizacion.fechaalta',DB::raw('paciente.id as paciente_id'),'hospitalizacion.hora',DB::raw('hospitalizacion.situacion as situacion2'))->orderBy('hospitalizacion.fecha', 'ASC');
        $lista            = $resultado->get();
        if (count($lista) > 0) {     
            foreach($lista as $key => $value){
                $pdf = new TCPDF();
                $pdf::SetTitle('Hoja de Costo');
                $pdf::AddPage();
                $pdf::SetFont('helvetica','B',12);
                $pdf::Cell(130,7,"",0,0,'C');
                //$pdf::Image("http://localhost/juanpablo/dist/img/logo.jpg", 15, 5, 115, 20);
                $pdf::Ln();
                $pdf::Ln();
                $pdf::Cell(0,7,"HOJA DE COSTO",0,0,'C');
                $pdf::Ln();
                $pdf::Ln();
                $pdf::SetFont('helvetica','B',9);
                $pdf::Cell(25,6,utf8_encode("Paciente: "),0,0,'L');
                $pdf::SetFont('helvetica','',9);
                $pdf::Cell(100,6,(trim($value->paciente)),0,0,'L');
                $pdf::SetFont('helvetica','B',9);
                $pdf::Cell(20,6,utf8_encode("Historia: "),0,0,'L');
                $pdf::SetFont('helvetica','',9);
                $pdf::Cell(0,6,(trim($value->historia)),0,0,'L');
                $pdf::Ln();
                $pdf::SetFont('helvetica','B',9);
                $pdf::Cell(25,6,utf8_encode("Fecha Ingreso: "),0,0,'L');
                $pdf::SetFont('helvetica','',9);
                $pdf::Cell(100,6,date("d/m/Y",strtotime($value->fecha))." ".substr($value->hora,0,5),0,0,'L');
                $pdf::SetFont('helvetica','B',9);
                $pdf::Cell(20,6,utf8_encode("Fecha Alta: "),0,0,'L');
                $pdf::SetFont('helvetica','',9);
                if($value->situacion2=='A'){
                    $pdf::Cell(0,6,date("d/m/Y",strtotime($value->fechaalta)),0,0,'L');
                }
                $pdf::Ln();
                $pdf::SetFont('helvetica','B',9);
                $pdf::Cell(8,8.2,("Item"),1,0,'C');
                $pdf::Cell(25,8.2,utf8_encode("Tipo Serv."),1,0,'C');
                $pdf::Cell(70,8.2,utf8_encode("Servicio"),1,0,'C');
                $pdf::Cell(15,8,'Doc.',1,0,'C');
                $pdf::Cell(40,3.5,utf8_encode("Deuda"),1,0,'C');
                $pdf::Cell(40,3.5,utf8_encode("Pagado"),1,0,'C');
                $pdf::Ln();
                $pdf::Cell(118,8,'',0,0,'C');
                $pdf::Cell(10,3.5,("Cant."),1,0,'C');
                $pdf::Cell(15,3.5,("Precio"),1,0,'C');
                $pdf::Cell(15,3.5,("Sub Tot."),1,0,'C');
                $pdf::Cell(10,3.5,("Cant."),1,0,'C');
                $pdf::Cell(15,3.5,("Precio"),1,0,'C');
                $pdf::Cell(15,3.5,("Sub Tot."),1,0,'C');
                $pdf::Ln();
                $resultado        = Detallehojacosto::leftjoin('servicio', 'servicio.id', '=', 'detallehojacosto.servicio_id')
                            ->where('detallehojacosto.hojacosto_id', '=', $id)
                            ->select('detallehojacosto.*');
                $lista2            = $resultado->get();
                $c=0;$deuda=0;
                foreach($lista2 as $key2 => $v){$c=$c+1;
                    $pdf::SetFont('helvetica','',7.5);
                    $pdf::Cell(8,6,$c,1,0,'C');
                    if($v->servicio_id>"0"){
                        if($v->servicio->tipopago=="Convenio"){
                            $codigo=$v->servicio->tarifario->codigo;
                            $nombre=$v->servicio->tarifario->nombre;    
                        }else{
                            $codigo="-";
                            if($v->servicio_id>"0"){
                                $nombre=$v->servicio->nombre;
                            }else{
                                $nombre=trim($v->descripcion);
                            }
                        }
                    }else{
                        $codigo="-";
                        $nombre=trim($v->descripcion);
                    }
                    if (isset($v->servicio->tiposervicio->nombre)) {
                        $pdf::Cell(25,6,utf8_encode($v->servicio->tiposervicio->nombre),1,0,'L');
                    } else {
                        $pdf::Cell(25,6,'-',1,0,'L');
                    }
                    $nombre.=" - ".substr($v->persona->nombres,0,1)." ".$v->persona->apellidopaterno;
                    if(strlen($nombre)<43){
                        $pdf::Cell(70,6,($nombre),1,0,'L');
                    }else{
                        $x=$pdf::GetX();
                        $y=$pdf::GetY();
                        $pdf::Multicell(70,3,($nombre),0,'L');
                        $pdf::SetXY($x,$y);
                        $pdf::Cell(70,6,"",1,0,'L');
                    }
                    $pdf::Cell(15,6,'Hoja Costo',1,0,'R');
                    $pdf::Cell(10,6,number_format($v->cantidad,2,'.',''),1,0,'R');
                    $pdf::Cell(15,6,number_format($v->precio,2,'.',''),1,0,'R');
                    $pdf::Cell(15,6,number_format($v->precio*$v->cantidad,2,'.',''),1,0,'R');
                    $pdf::Cell(10,6,'-',1,0,'R');
                    $pdf::Cell(15,6,'-',1,0,'R');
                    $pdf::Cell(15,6,'-',1,0,'R');
                    $pdf::Ln();
                    $deuda = $deuda + number_format($v->precio*$v->cantidad,2,'.','');         
                }
                
                $resultado1        = Movimiento::join('detallemovcaja as dmc','dmc.movimiento_id','=','movimiento.id')
                            ->join('person as medico','medico.id','=','dmc.persona_id')
                            ->join('person as paciente','paciente.id','=','movimiento.persona_id')
                            ->join('movimiento as mref','mref.movimiento_id','=','movimiento.id')
                            ->leftjoin('servicio as s','s.id','=','dmc.servicio_id')
                            ->join('historia','historia.person_id','=','movimiento.persona_id')
                            ->leftjoin('person as referido','referido.id','=','movimiento.doctor_id')
                            ->where('movimiento.tipomovimiento_id','=',1)
                            ->where('mref.situacion','<>','A')
                            ->where('movimiento.persona_id','=',$value->paciente_id)
                            ->where('movimiento.id','>=',$value->movimientoinicial_id);
                if($value->movimientofinal_id>0){
                    $resultado1 = $resultado1->where('movimiento.id','<=',$value->movimientofinal_id);
                }
                $resultado1        = $resultado1->orderBy('mref.fecha', 'ASC')->orderBy('mref.serie', 'ASC')->orderBy('mref.numero', 'ASC')
                                    ->select('mref.*','movimiento.doctor_id as referido_id','historia.tipopaciente','dmc.servicio_id','dmc.descripcion as servicio2','movimiento.tarjeta','movimiento.tipotarjeta','movimiento.situacion as situacion2','historia.numero as historia','dmc.recibo','dmc.cantidad','dmc.precio','dmc.id as iddetalle','s.nombre as servicio','dmc.pagohospital','dmc.pagodoctor',DB::raw('concat(medico.apellidopaterno,\' \',medico.apellidomaterno,\' \',medico.nombres) as medico'),DB::raw('concat(referido.apellidopaterno,\' \',referido.apellidomaterno,\' \',referido.nombres) as referido'),DB::raw('concat(paciente.apellidopaterno,\' \',paciente.apellidomaterno,\' \',paciente.nombres) as paciente2'));
                $lista2            = $resultado1->get();
                $pago=0;
                if (count($lista2) > 0) {     
                    foreach($lista2 as $key2 => $v){$c=$c+1;
                        $pdf::SetFont('helvetica','',7.5);
                        $pdf::Cell(8,6,$c,1,0,'C');
                        if($v->servicio_id>"0"){
                            $servicio = Servicio::find($v->servicio_id);
                            if($servicio->tipopago=="Convenio"){
                                $codigo=$servicio->tarifario->codigo;
                                $nombre=$servicio->tarifario->nombre;    
                            }else{
                                $codigo="-";
                                if($v->servicio_id>"0"){
                                    $nombre=$servicio->nombre;
                                }else{
                                    $nombre=trim($servicio->descripcion);
                                }
                            }
                            $tiposervicio=$servicio->tiposervicio->nombre;
                        }else{
                            $codigo="-";
                            $nombre=trim($v->servicio2);
                            $tiposervicio='-';
                        }
                        $pdf::Cell(25,6,utf8_encode($tiposervicio),1,0,'L');
                        //$nombre.=" - ".substr($v->persona->nombres,0,1)." ".$v->persona->apellidopaterno;
                        if(strlen($nombre)<43){
                            $pdf::Cell(70,6,utf8_encode($nombre),1,0,'L');
                        }else{
                            $x=$pdf::GetX();
                            $y=$pdf::GetY();
                            $pdf::Multicell(70,3,utf8_encode($nombre),0,'L');
                            $pdf::SetXY($x,$y);
                            $pdf::Cell(70,6,"",1,0,'L');
                        }
                        $venta = Movimiento::where("movimiento_id","=",$v->movimiento_id)->first();
                        $pdf::Cell(15,6,(($venta->tipodocumento_id==5?'B':'F').$venta->serie.'-'.$venta->numero),1,0,'L');
                        if($venta->situacion=='P'){
                            $pdf::Cell(10,6,number_format($v->cantidad,2,'.',''),1,0,'R');
                            $pdf::Cell(15,6,number_format($v->precio,2,'.',''),1,0,'R');
                            $pdf::Cell(15,6,number_format($v->precio*$v->cantidad,2,'.',''),1,0,'R');
                            $pdf::Cell(10,6,'-',1,0,'R');
                            $pdf::Cell(15,6,'-',1,0,'R');
                            $pdf::Cell(15,6,'-',1,0,'R');

                            $pdf::Ln();  
                            $deuda = $deuda + number_format($v->precio*$v->cantidad,2,'.','');
                        }else{
                            $pdf::Cell(10,6,'-',1,0,'R');
                            $pdf::Cell(15,6,'-',1,0,'R');
                            $pdf::Cell(15,6,'-',1,0,'R');
                            $pdf::Cell(10,6,number_format($v->cantidad,2,'.',''),1,0,'R');
                            $pdf::Cell(15,6,number_format($v->precio,2,'.',''),1,0,'R');
                            $pdf::Cell(15,6,number_format($v->precio*$v->cantidad,2,'.',''),1,0,'R');
                            $pdf::Ln();  
                            $pago = $pago + number_format($v->precio*$v->cantidad,2,'.','');
                        }
                    }
                }
                $pdf::SetFont('helvetica','B',10);
                $pdf::Cell(118,5,utf8_decode('TOTAL:'),0,0,'R');
                $pdf::Cell(40,5,number_format($deuda,2,'.',''),0,0,'R');
                $pdf::Cell(40,5,number_format($pago,2,'.',''),0,0,'R');
                $pdf::Ln();
                $pdf::Ln();

                $pdf::SetFont('helvetica','B',12);
                $pdf::Cell(118,5,utf8_decode('POR REGULARIZAR:'),0,0,'R');
                if($deuda>$pago){
                    $pdf::Cell(0,5,number_format($deuda - $pago,2,'.',''),0,0,'C');
                }else{
                    $pdf::Cell(0,5,number_format(0,2,'.',''),0,0,'C');
                }
                $pdf::Ln();

                $pdf::SetFont('helvetica','',9);
                $pdf::Cell(0,5,'Usuario: '.$value->usuario->nombres,0,0,'L');
                $pdf::Ln();
               $pdf::Output('HojaCosto.pdf');
            }
        }
    }

    public function hospitalizadoautocompletar($searching)
    {
        $resultado        = Hospitalizacion::join('habitacion as h','h.id','=','hospitalizacion.habitacion_id')
                            ->join('historia','historia.id','=','hospitalizacion.historia_id')
                            ->join('person as paciente','paciente.id','=','historia.person_id')
                            ->where('hospitalizacion.situacion','=','H')
                            ->where(DB::raw('concat(paciente.apellidopaterno,\' \',paciente.apellidomaterno,\' \',paciente.nombres)'), 'LIKE', '%'.strtoupper($searching).'%')
                            ->select('hospitalizacion.*');
        $list      = $resultado->get();
        $data = array();
        foreach ($list as $key => $value) {
            $data[] = array(
                            'label' => trim($value->historia->persona->apellidopaterno.' '.$value->historia->persona->apellidomaterno.' '.$value->historia->persona->nombres),
                            'id'    => $value->id,
                            'historia' => $value->historia->numero,
                            'tipopaciente' => $value->historia->tipopaciente,
                            'value' => trim($value->historia->persona->apellidopaterno.' '.$value->historia->persona->apellidomaterno.' '.$value->historia->persona->nombres),
                        );
        }
        return json_encode($data);
    }

    public function agregardetalle(Request $request){
        $resultado        = Detallehojacosto::leftjoin('servicio', 'servicio.id', '=', 'detallehojacosto.servicio_id')
                            ->where('detallehojacosto.hojacosto_id', '=', $request->input('id'))
                            ->select('detallehojacosto.*');
        $lista            = $resultado->get();
        $data = array();
        foreach($lista as $k => $v){
            $data[] = array("idservicio"=> $v->servicio_id,
                            "servicio" => ($v->servicio_id>0?$v->servicio->nombre:''),
                            "precio" => $v->precio,
                            "cantidad" => $v->cantidad,
                            "servicio2" => $v->descripcion,
                            "tiposervicio" => ($v->servicio_id>0?$v->servicio->tiposervicio->nombre:'VARIOS'),
                            "idtiposervicio" => ($v->servicio_id>0?$v->servicio->tiposervicio_id:0),
                            "idmedico" => $v->persona_id,
                            "medico" => $v->persona->apellidopaterno.' '.$v->persona->apellidomaterno.' '.$v->persona->nombres);
        }
        return json_encode($data);
    }

}
