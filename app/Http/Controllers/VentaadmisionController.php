<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Validator;
use App\Http\Requests;
use App\Historia;
use App\Plan;
use App\Kardex;
use App\Tipodocumento;
use App\Movimiento;
use App\Detallemovimiento;
use App\Person;
use App\Servicio;
use App\Caja;
use App\Venta;
use App\Librerias\Libreria;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Elibyy\TCPDF\Facades\TCPDF;
use App\Detallemovcaja;
use App\Librerias\EnLetras;
use Illuminate\Support\Facades\Auth;
use Excel;
use DateTime;

class VentaadmisionController extends Controller
{
    protected $folderview      = 'app.ventaadmision';
    protected $tituloAdmin     = 'Venta';
    protected $tituloRegistrar = 'Registrar Venta';
    protected $tituloModificar = 'Modificar Venta';
    protected $tituloCobrar = 'Cobrar Venta';
    protected $tituloAnular  = 'Anular Venta';
    protected $rutas           = array('create' => 'ventaadmision.create', 
            'edit'   => 'ventaadmision.edit', 
            'anular' => 'ventaadmision.anular',
            'search' => 'ventaadmision.buscar',
            'index'  => 'ventaadmision.index',
            'pdfListar'  => 'ventaadmision.pdfListar',
            'procesar'  => 'ventaadmision.procesar',
            'cobrar' => 'ventaadmision.cobrar',
            'pagar' => 'ventaadmision.pagar',
            'excel' => 'ventaadmision.excelConcar',
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
        $entidad          = 'Ventaadmision';
        $resultado        = Movimiento::leftjoin('person as paciente', 'paciente.id', '=', 'movimiento.persona_id')
                            ->join('person as responsable', 'responsable.id', '=', 'movimiento.responsable_id')
                            ->leftjoin('movimiento as m2','m2.id','=','movimiento.movimiento_id')
                            ->where('movimiento.tipomovimiento_id','=',4)
                            ->where('movimiento.ventafarmacia','=','N')
                            //->where('movimiento.situacion','<>','U')
                            ->where('m2.tipomovimiento_id','=',1);
        if($request->input('fechainicial')!=""){
            $resultado = $resultado->where('movimiento.fecha','>=',$request->input('fechainicial').' 00:00:00');
        }
        if($request->input('fechafinal')!=""){
            $resultado = $resultado->where('movimiento.fecha','<=',$request->input('fechafinal').' 23:59:59');
        }        
        if($request->input('tipodocumento')!=""){
            $resultado = $resultado->where('movimiento.tipodocumento_id','=',$request->input('tipodocumento'));
        }        
        if($request->input('numero')!=""){
            $resultado = $resultado->where(DB::raw('CONCAT(case when movimiento.tipodocumento_id=4 then "F" else "B" end,movimiento.serie,"-",movimiento.numero)'),'LIKE','%'.$request->input('numero').'%');
        }    
        if($request->input('paciente')!=""){
            $resultado = $resultado->where(DB::raw('case when movimiento.tipodocumento_id=5 then concat(paciente.apellidopaterno," ",paciente.apellidomaterno," ",paciente.nombres) else paciente.bussinesname end'),'LIKE','%'.strtoupper($request->input('paciente')).'%');
        }    
        if($request->input('situacion')!=""){
            $resultado = $resultado->where('movimiento.situacion','like',$request->input('situacion'));
        }

        $resultado        = $resultado->select('movimiento.*','m2.situacion as situacion2',DB::raw('CONCAT(case when movimiento.tipodocumento_id=4 then "F" when movimiento.tipodocumento_id=5 then "B" else "T" end,lpad(movimiento.serie,3,"0"),"-",lpad(movimiento.numero,8,"0")) as numero2'))->orderBy('movimiento.fecha', 'ASC')->orderBy('movimiento.serie', 'ASC')->orderBy('movimiento.numero', 'ASC');
        $lista            = $resultado->get();
        $cabecera         = array();
        $cabecera[]       = array('valor' => '#', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Fecha', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Nro', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Paciente', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Total', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Situacion', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Estado Sunat', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Msg. Sunat', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Operaciones', 'numero' => '3');
        
        $user = Auth::user();
        $titulo_modificar = $this->tituloModificar;
        $titulo_anular  = $this->tituloAnular;
        $titulo_cobrar    = $this->tituloCobrar;
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
            return view($this->folderview.'.list')->with(compact('lista', 'paginacion', 'inicio', 'fin', 'entidad', 'cabecera', 'titulo_modificar', 'titulo_anular', 'titulo_cobrar', 'ruta', 'user'));
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
        $entidad          = 'Ventaadmision';
        $title            = $this->tituloAdmin;
        $titulo_registrar = $this->tituloRegistrar;
        $ruta             = $this->rutas;
        $user = Auth::user();
        $cboTipoDoc = array(''=>'Todos...');
        $rs = Tipodocumento::where('tipomovimiento_id','=','4')->orderBy('nombre','ASC')->get();
        foreach ($rs as $key => $value) {
            $cboTipoDoc = $cboTipoDoc + array($value->id => $value->nombre);
        }
        $cboSituacion = array(''=>'Todos...','U'=>'Anulado');
        return view($this->folderview.'.admin')->with(compact('entidad', 'title', 'titulo_registrar', 'ruta','cboTipoDoc', 'user','cboSituacion'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $listar              = Libreria::getParam($request->input('listar'), 'NO');
        $entidad             = 'Ventaadmision';
        $Venta = null;
        $cboConvenio = array();
        $convenios = Convenio::where(DB::raw('1'),'=','1')->orderBy('nombre','ASC')->get();
        foreach ($convenios as $key => $value) {
            $cboConvenio = $cboConvenio + array($value->id => $value->nombre);
        }
        $formData            = array('Venta.store');
        $cboTipoPaciente     = array("Particular" => "Particular", "Convenio" => "Convenio", "Hospital" => "Hospital");
        $formData            = array('route' => $formData, 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton               = 'Registrar'; 
        return view($this->folderview.'.mant')->with(compact('Venta', 'formData', 'entidad', 'boton', 'listar', 'cboTipoPaciente', 'cboConvenio'));
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
                'doctor'                  => 'required',
                'especialidad'          => 'required',
                'paciente'          => 'required',
                'numero'          => 'required',
                );
        $mensajes = array(
            'doctor.required'         => 'Debe seleccionar un doctor',
            'especialidad.required'         => 'Debe seleccionar una especialidad',
            'paciente.required'         => 'Debe seleccionar un paciente',
            'numero.required'         => 'Debe seleccionar una historia',
            );
        $validacion = Validator::make($request->all(), $reglas, $mensajes);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        }       
        
        $error = DB::transaction(function() use($request){
            $Venta       = new Venta();
            $Venta->fecha = $request->input('fecha');
            $person_id = $request->input('person_id');
            if($person_id==""){
                $person_id = null;
            }
            $historia_id = $request->input('historia_id');
            if($historia_id==""){
                $historia_id = null;
            }
            $Venta->paciente_id = $person_id;
            $Venta->historia_id = $historia_id;
            $Venta->doctor_id = $request->input('doctor_id');
            $Venta->situacion='P';//Pendiente
            $Venta->horainicio = $request->input('horainicio');
            $Venta->horafin = $request->input('horafin');
            $Venta->comentario = $request->input('comentario');
            $Venta->telefono = $request->input('telefono');
            $Venta->paciente = $request->input('paciente');
            $Venta->historia = $request->input('numero');
            $Venta->tipopaciente = $request->input('tipopaciente');
            $Venta->save();
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
        $existe = Libreria::verificarExistencia($id, 'Venta');
        if ($existe !== true) {
            return $existe;
        }
        $listar              = Libreria::getParam($request->input('listar'), 'NO');
        $Venta = Venta::find($id);
        $entidad             = 'Venta';
        $formData            = array('Venta.update', $id);
        $cboTipoPaciente     = array("Particular" => "Particular", "Convenio" => "Convenio", "Hospital" => "Hospital");
        $formData            = array('route' => $formData, 'method' => 'PUT', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton               = 'Modificar';
        return view($this->folderview.'.mant')->with(compact('Venta', 'formData', 'entidad', 'boton', 'listar', 'cboTipoPaciente'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function anulacion(Request $request)
    {
        $id=$request->input('id');
        $existe = Libreria::verificarExistencia($id, 'movimiento');
        if ($existe !== true) {
            return $existe;
        }
        $user = Auth::user();
        $error = DB::transaction(function() use($id,$user,$request){
            $Venta = Movimiento::find($id);
            $Venta->situacion = 'U';
            $Venta->usuarioentrega_id=$user->person_id;
            $Venta->comentario = $request->input('motivo');
            $Venta->save();

            $Caja = Movimiento::where('movimiento_id','=',$id)->first();
            if(!is_null($Caja)){
                $Caja->situacion = 'A';
                $Caja->save();
            }

            $Ticket = Movimiento::where('id','=',$Venta->movimiento_id)->first();
            if(!is_null($Ticket)){
                $Ticket->situacion = 'U';//Anulada
                $Ticket->save();
            }
            $rs=Detallemovcaja::where("movimiento_id",'=',$Venta->movimiento_id)->get();
            foreach ($rs as $key => $value) {
                $caja = Movimiento::where('listapago','like','%'.$value->id.'%')->first();
                if(!is_null($caja)){
                    $caja->situacion = 'A';//Anulada
                    $caja->save();
                }
            }
        });
        return is_null($error) ? "OK" : $error;
    }

    public function anular($id, $listarLuego)
    {
        $existe = Libreria::verificarExistencia($id, 'movimiento');
        if ($existe !== true) {
            return $existe;
        }
        $listar = "NO";
        if (!is_null(Libreria::obtenerParametro($listarLuego))) {
            $listar = $listarLuego;
        }
        $modelo   = Movimiento::find($id);
        $entidad  = 'Ventaadmision';
        $formData = array('route' => array('ventaadmision.anulacion', $id), 'method' => 'Acept', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Anular';
        return view($this->folderview.'.anular')->with(compact('modelo', 'formData', 'entidad', 'boton', 'listar'));
    }

    public function pagar(Request $request)
    {
        $existe = Libreria::verificarExistencia($request->input('id'), 'movimiento');
        if ($existe !== true) {
            return $existe;
        }
        $user = Auth::user();
        $rst  = Movimiento::where('tipomovimiento_id','=',2)->where('caja_id','=',$request->input('caja_id'))->orderBy('movimiento.id','DESC')->limit(1)->first();
        if(count($rst)==0){
            $conceptopago_id=2;
        }else{
            $conceptopago_id=$rst->conceptopago_id;
        }
        if($conceptopago_id==2){
            $dat[0]=array("respuesta"=>"ERROR","msg"=>"Caja cerrada");
            return json_encode($dat);
        }
    
        $error = DB::transaction(function() use($request, $user){
            $Venta = Movimiento::find($request->input('id'));
            $Venta->situacion ='N';
            $Venta->save();

            $movimiento        = new Movimiento();
            $movimiento->fecha = date("Y-m-d");
            $movimiento->numero= Movimiento::NumeroSigue(2,2);
            $movimiento->responsable_id=$user->person_id;
            $movimiento->persona_id=$Venta->persona_id;
            $movimiento->subtotal=0;
            $movimiento->igv=0;
            $movimiento->total=$request->input('total',0); 
            $movimiento->tipomovimiento_id=2;
            $movimiento->tipodocumento_id=2;
            $movimiento->conceptopago_id=23;//COBRANZA
            $movimiento->comentario='Pago de Documento de Venta Credito: '.($Venta->tipodocumento_id==5?'Boleta':'Factura').' '.$Venta->serie.'-'.$Venta->numero;
            $movimiento->caja_id=$request->input('caja_id');
            if($request->input('formapago')=="Tarjeta"){
                $movimiento->tipotarjeta=$request->input('tipotarjeta');
                $movimiento->tarjeta=$request->input('tipotarjeta2');
                $movimiento->voucher=$request->input('nroref');
                $movimiento->totalpagado=0;
            }else{
                $movimiento->totalpagado=$request->input('total',0);
            }
            $movimiento->situacion='N';
            $movimiento->movimiento_id=$Venta->id;
            $movimiento->save();
        });
        return is_null($error) ? "OK" : $error;
    }

    public function cobrar($id, $listarLuego,Request $request)
    {
        $existe = Libreria::verificarExistencia($id, 'movimiento');
        if ($existe !== true) {
            return $existe;
        }
        $listar = "NO";
        if (!is_null(Libreria::obtenerParametro($listarLuego))) {
            $listar = $listarLuego;
        }
        $modelo   = Movimiento::find($id);
        $entidad  = 'Ventaadmision';
        $cboCaja          = array();
        $resultado        = Caja::where('id','<>',6)->where('id','<>',4)->orderBy('nombre','ASC')->get();
        $caja=0;
        foreach ($resultado as $key => $value) {
            $cboCaja = $cboCaja + array($value->id => $value->nombre);
            if($request->ip()==$value->ip){
                $caja=$value->id;
                $serie=$value->serie;
            }
        }
        $cboFormaPago     = array("Efectivo" => "Efectivo", "Tarjeta" => "Tarjeta");
        $cboTipoTarjeta    = array("VISA" => "VISA", "MASTER" => "MASTER");
        $cboTipoTarjeta2    = array("CREDITO" => "CREDITO", "DEBITO" => "DEBITO");        
        $user = Auth::user();
        if($caja==0){//ADMISION 1
            $serie=3;
            $idcaja=1;
        }
        $formData = array('route' => array('ventaadmision.pagar', $id), 'method' => 'Acept', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Cobrar';
        return view($this->folderview.'.cobrar')->with(compact('modelo', 'formData', 'entidad', 'boton', 'listar', 'cboCaja' , 'caja', 'cboFormaPago', 'cboTipoTarjeta2', 'cboTipoTarjeta'));
    }
    
    public function validarDNI(Request $request)
    {
        $dni = $request->input("dni");
        $entidad    = 'Person';
        $mdlPerson = new Person();
        $resultado = Person::where('dni','LIKE',$dni);
        $value     = $resultado->first();
        if(count($value)>0){
            $objVenta = new Venta();
            $list2       = Venta::where('person_id','=',$value->id)->first();
            if(count($list2)>0){//SI TIENE Venta
                $data[] = array(
                            'apellidopaterno' => $value->apellidopaterno,
                            'apellidomaterno' => $value->apellidomaterno,
                            'nombres' => $value->nombres,
                            'telefono' => $value->telefono,
                            'direccion' => $value->direccion,
                            'id'    => $value->id,
                            'msg' => 'N',
                        );
            }else{//NO TIENE Venta PERO SI ESTA REGISTRADO LA PERSONA COMO PROVEEDOR O PERSONAL
                $data[] = array(
                            'apellidopaterno' => $value->apellidopaterno,
                            'apellidomaterno' => $value->apellidomaterno,
                            'nombres' => $value->nombres,
                            'telefono' => $value->telefono,
                            'direccion' => $value->direccion,
                            'id'    => $value->id,
                            'msg' => 'S',
                            'modo'=> 'Registrado',
                        );                
            }
        }else{
            $data[] = array('msg'=>'S','modo'=>'Nada');
        }
        return json_encode($data);
    }

    public function pdfComprobante(Request $request){
        $entidad          = 'Ventaadmision';
        $id            = Libreria::getParam($request->input('id'),'');
        //$rst              = Movimiento::find($idref);
        //$id = $rst->movimiento_id;
        $resultado        = Movimiento::join('person as paciente', 'paciente.id', '=', 'movimiento.persona_id')
                            ->leftjoin('person as responsable','responsable.id','=','movimiento.responsable_id')
                            ->join('tipodocumento','tipodocumento.id','=','movimiento.tipodocumento_id')
                            ->where('movimiento.id', '=', $id);
        $resultado        = $resultado->select('movimiento.*','tipodocumento.nombre as tipodocumento');
        $lista            = $resultado->get();
        if (count($lista) > 0) {     
            foreach($lista as $key => $value){
                $pdf = new TCPDF();
                $pdf::SetTitle('Comprobante');
                $pdf::AddPage();
                $pdf::SetFont('helvetica','B',12);
                $pdf::Cell(130,7,"",0,0,'C');
                $pdf::Image("http://localhost/juanpablo/dist/img/logo.jpg", 15, 5, 115, 30);
                $pdf::Cell(60,7,("RUC N° 20480082673"),'RTL',0,'C');
                $pdf::Ln();
                $pdf::Cell(130,7,"",0,0,'C');
                $pdf::Cell(60,7,utf8_encode($value->tipodocumento_id=='4'?"FACTURA":"BOLETA"),'RL',0,'C');
                $pdf::Ln();
                $pdf::Cell(130,7,"",0,0,'C');
                $pdf::Cell(60,7,("ELECTRÓNICA"),'RL',0,'C');
                $pdf::Ln();
                $pdf::Cell(130,7,"",0,0,'C');
                if($value->tipodocumento_id=="4"){
                    $abreviatura="F";
                    $dni=$value->persona->ruc;
                    $subtotal=number_format($value->total/1.18,2,'.','');
                    $igv=number_format($value->total - $subtotal,2,'.','');
                }else{
                    $abreviatura="B";
                    $subtotal=number_format($value->subtotal,2,'.','');
                    $igv=number_format($value->igv,2,'.','');
                    if(strlen($value->persona->dni)<>8){
                        $dni='00000000';
                    }else{
                        $dni=$value->persona->dni;
                    }
                }
                $pdf::Cell(60,7,utf8_encode($abreviatura.str_pad($value->serie,3,'0',STR_PAD_LEFT).'-'.str_pad($value->numero,8,'0',STR_PAD_LEFT)),'RBL',0,'C');
                $pdf::Ln();
                $pdf::SetFont('helvetica','B',9);
                $pdf::Cell(0,7,utf8_decode("HOSPITAL PRIVADO JUAN PABLO II SOCIEDAD ANONIMA CERRADA"),0,0,'L');
                $pdf::Ln();
                $pdf::Ln();
                $pdf::SetFont('helvetica','B',9);
                $pdf::Cell(37,6,("Nombre / Razón Social: "),0,0,'L');
                $pdf::SetFont('helvetica','',9);
                $pdf::Cell(110,6,(trim($value->persona->bussinesname." ".$value->persona->apellidopaterno." ".$value->persona->apellidomaterno." ".$value->persona->nombres)),0,0,'L');
                $pdf::SetFont('helvetica','B',9);
                $pdf::Cell(30,6,utf8_encode($abreviatura=="F"?"RUC":"DNI".": "),0,0,'L');
                $pdf::SetFont('helvetica','',8);
                $pdf::Cell(37,6,utf8_encode($dni),0,0,'L');
                $pdf::Ln();
                if($value->tipodocumento_id=="4" && $value->id!=86410 && $value->id!=86407){
                    $pdf::SetFont('helvetica','B',9);
                    $pdf::Cell(37,6,utf8_encode("Paciente: "),0,0,'L');
                    $pdf::SetFont('helvetica','',9);
                    $ticket = Movimiento::find($value->movimiento_id);
                    $pdf::Cell(110,6,(trim($ticket->persona->apellidopaterno." ".$ticket->persona->apellidomaterno." ".$ticket->persona->nombres)),0,0,'L');
                    $pdf::Ln();
                }
                $pdf::SetFont('helvetica','B',9);
                $pdf::Cell(37,6,("Dirección: "),0,0,'L');
                $pdf::SetFont('helvetica','',9);
                $pdf::Cell(110,6,(trim($value->persona->direccion)),0,0,'L');
                $pdf::SetFont('helvetica','B',9);
                $pdf::Cell(30,6,("Fecha de emisión: "),0,0,'L');
                $pdf::SetFont('helvetica','',8);
                $pdf::Cell(37,6,date("d/m/Y",strtotime($value->fecha)),0,0,'L');
                $pdf::Ln();
                $pdf::SetFont('helvetica','B',9);
                $pdf::Cell(37,6,utf8_encode("Moneda: "),0,0,'L');
                $pdf::SetFont('helvetica','',9);
                $pdf::Cell(110,6,(trim('PEN - Sol')),0,0,'L');
                $pdf::SetFont('helvetica','B',9);
                $pdf::Cell(30,6,("Condicion: "),0,0,'L');
                $pdf::SetFont('helvetica','',9);
                $value2=Movimiento::find($value->movimiento_id);
                if($value2->tarjeta!=""){
                    $pdf::Cell(37,6,trim($value2->tarjeta." - ".$value2->tipotarjeta),0,0,'L');
                }elseif($value2->situacion=='B'){
                    $pdf::Cell(37,6,trim('PENDIENTE'),0,0,'L');
                }else{
                    $pdf::Cell(37,6,trim('CONTADO'),0,0,'L');
                }

                $pdf::Ln();
                if($value->id!=86410 && $value->id!=86407){
                    $pdf::SetFont('helvetica','B',9);
                    $pdf::Cell(37,6,utf8_encode("Convenio: "),0,0,'L');
                    $pdf::SetFont('helvetica','',9);
                    $pdf::Cell(110,6,(trim($value2->plan->nombre)),0,0,'L');
                    $pdf::SetFont('helvetica','B',9);
                    $pdf::Cell(30,6,("Historia: "),0,0,'L');
                    $mov=Movimiento::find($value->movimiento_id);
                    $historia = Historia::where('person_id','=',$mov->persona_id)->first();
                    if(count($historia)>0){
                        $pdf::SetFont('helvetica','',8);
                        $pdf::Cell(30,6,($historia->numero),0,0,'L');
                    }
                    $pdf::Ln();
                }
                $pdf::SetFont('helvetica','B',9);
                $pdf::Cell(10,7,("Item"),1,0,'C');
                $pdf::Cell(13,7,("Código"),1,0,'C');
                $pdf::Cell(68,7,("Descripción"),1,0,'C');
                $pdf::Cell(10,7,("Und."),1,0,'C');
                $pdf::Cell(15,7,("Cantidad"),1,0,'C');
                $pdf::Cell(20,7,("V. Unitario"),1,0,'C');
                $pdf::Cell(20,7,("P. Unitario"),1,0,'C');
                $pdf::Cell(20,7,("Descuento"),1,0,'C');
                $pdf::Cell(20,7,("Sub Total"),1,0,'C');
                $pdf::Ln();
                $resultado        = Detallemovcaja::leftjoin('servicio', 'servicio.id', '=', 'detallemovcaja.servicio_id')
                            ->where('detallemovcaja.movimiento_id', '=', $value->movimiento_id)
                            ->select('detallemovcaja.*');
                $lista2            = $resultado->get();
                $c=0;
                foreach($lista2 as $key2 => $v){$c=$c+1;
                    $pdf::SetFont('helvetica','',7.5);
                    $pdf::Cell(10,7,$c,1,0,'C');
                    if(isset($v->servicio)){
                        if($v->servicio_id>0){
                            if($v->servicio->tipopago=="Convenio"){

                                //// REVISAR TARIA}FARIO ENDOCRINOLOGIA
                                if (isset($v->servicio->tarifario)) {
                                    $codigo=$v->servicio->tarifario->codigo;
                                    $nombre=$v->servicio->tarifario->nombre;
                                }
                                    
                            }else{
                                $codigo="-";
                                if($v->servicio_id>0){
                                    $nombre=$v->servicio->nombre;
                                }else{
                                    $nombre=trim($v->descripcion);
                                }
                            }
                        }else{
                            $codigo="-";
                            $nombre=trim($v->descripcion);
                        }
                    }else{
                        $codigo="-";
                        $nombre=trim($v->descripcion);
                    }
                    $nombre.=" - ".substr($v->persona->nombres,0,1)." ".$v->persona->apellidopaterno;
                    $pdf::Cell(13,7,$codigo,1,0,'C');
                    if(strlen($nombre)<50){
                        $pdf::Cell(68,7,utf8_encode($nombre),1,0,'L');
                    }else{
                        $x=$pdf::GetX();
                        $y=$pdf::GetY();
                        $pdf::Multicell(68,3.5,utf8_encode($nombre),1,'L');
                        $pdf::SetXY($x,$y);
                        $pdf::Cell(68,7,"",1,0,'L');
                    }
                    $pdf::Cell(10,7,("ZZ."),1,0,'C');
                    $pdf::Cell(15,7,number_format($v->cantidad,2,'.',''),1,0,'R');
                    $pdf::Cell(20,7,number_format($v->pagohospital/1.18,2,'.',''),1,0,'R');
                    $pdf::Cell(20,7,number_format($v->pagohospital,2,'.',''),1,0,'R');
                    $pdf::Cell(20,7,("0.00"),1,0,'R');
                    $pdf::Cell(20,7,number_format($v->pagohospital*$v->cantidad,2,'.',''),1,0,'R');
                    $pdf::Ln();                    
                }
                $letras = new EnLetras();
                $pdf::SetFont('helvetica','B',8);
                $valor=$letras->ValorEnLetras($value->total, "SOLES" );//letras
                
                $pdf::Cell(116,5,utf8_decode($valor),0,0,'L');
                $pdf::SetFont('helvetica','B',8);
                $pdf::Cell(40,5,utf8_decode('Op. Gravada'),0,0,'L');
                $pdf::SetFont('helvetica','',8);
                $pdf::Cell(20,5,utf8_decode('PEN'),0,0,'C');
                $pdf::Cell(20,5,$subtotal,0,0,'R');
                $pdf::Ln();
                $pdf::Cell(116,5,'',0,0,'L');
                $pdf::SetFont('helvetica','B',8);
                $pdf::Cell(40,5,utf8_decode('I.G.V'),0,0,'L');
                $pdf::SetFont('helvetica','',8);
                $pdf::Cell(20,5,utf8_decode('PEN'),0,0,'C');
                $pdf::Cell(20,5,$igv,0,0,'R');
                $pdf::Ln();
                $pdf::Cell(116,5,'',0,0,'L');
                $pdf::SetFont('helvetica','B',8);
                $pdf::Cell(40,5,utf8_decode('Op. Inafecta'),0,0,'L');
                $pdf::SetFont('helvetica','',8);
                $pdf::Cell(20,5,utf8_decode('PEN'),0,0,'C');
                $pdf::Cell(20,5,'0.00',0,0,'R');
                $pdf::Ln();
                $pdf::Cell(116,5,'',0,0,'L');
                $pdf::SetFont('helvetica','B',8);
                $pdf::Cell(40,5,utf8_decode('Op. Exonerada'),0,0,'L');
                $pdf::SetFont('helvetica','',8);
                $pdf::Cell(20,5,utf8_decode('PEN'),0,0,'C');
                $pdf::Cell(20,5,'0.00',0,0,'R');
                $pdf::Ln();
                $pdf::Cell(116,5,'',0,0,'L');
                $pdf::Cell(40,5,'',0,0,'L');
                $pdf::Cell(20,5,'',0,0,'C');
                $pdf::Cell(20,5,'----------------------',0,0,'R');
                $pdf::Ln();
                $pdf::Cell(116,5,'',0,0,'L');
                $pdf::SetFont('helvetica','B',8);
                $pdf::Cell(40,5,utf8_decode('Importe Total'),0,0,'L');
                $pdf::SetFont('helvetica','',8);
                $pdf::Cell(20,5,utf8_decode('PEN'),0,0,'C');
                $pdf::Cell(20,5,number_format($value->total,2,'.',''),0,0,'R');
                $pdf::Ln();
                $pdf::SetFont('helvetica','B',8);
                $pdf::Cell(195,5,'Observaciones de SUNAT:','LRT',0,'L');
                $pdf::Ln();
                $pdf::SetFont('helvetica','',8);
                $pdf::Cell(195,5,'','LRB',0,'C');
                $pdf::Ln();
                $pdf::SetFont('helvetica','',8);
                $pdf::Cell(0,5,('Autorizado a ser emisor electrónico mediante R.I. SUNAT Nº 0340050004781'),0,0,'L');
                $pdf::Ln();
                $pdf::Cell(0,5,'Usuario: '.$value->responsable->nombres,0,0,'R');
                $pdf::Ln();
                $pdf::SetFont('helvetica','',8);
                $pdf::Cell(160,5,('Representación Impresa de la Factura Electrónica, consulte en https://sfe.bizlinks.com.pe'),0,0,'L');
                $pdf::Cell(0,5,$value->created_at,0,0,'R');
                $pdf::Ln();
                $pdf::Output('Comprobante.pdf');
            }
        }
    }
    
   	public function pdfListar(Request $request){
        $entidad          = 'Venta';
        $paciente         = Libreria::getParam($request->input('paciente'),'');
        $doctor           = Libreria::getParam($request->input('doctor'),'');
        $fecha            = Libreria::getParam($request->input('fecha'));
        $resultado        = Venta::leftjoin('person as paciente', 'paciente.id', '=', 'Venta.paciente_id')
                            ->join('person as doctor', 'doctor.id', '=', 'Venta.doctor_id')
                            ->join('especialidad','especialidad.id','=','doctor.especialidad_id')
                            ->leftjoin('historia','historia.id','=','Venta.historia_id')
                            ->where('Venta.paciente', 'LIKE', '%'.strtoupper($paciente).'%')
                            ->where(DB::raw('concat(doctor.apellidopaterno,\' \',doctor.apellidomaterno,\' \',doctor.nombres)'), 'LIKE', '%'.strtoupper($doctor).'%');
        if($fecha!=""){
            $resultado = $resultado->where('Venta.fecha', '=', ''.$fecha.'');
        }
        $resultado        = $resultado->select('Venta.*','historia.tipopaciente as tipopaciente2','especialidad.nombre as especialidad','historia.numero as historia2',DB::raw('concat(paciente.apellidopaterno,\' \',paciente.apellidomaterno,\' \',paciente.nombres) as paciente2'),DB::raw('concat(doctor.apellidopaterno,\' \',doctor.apellidomaterno,\' \',doctor.nombres) as doctor'))->orderBy('Venta.fecha', 'ASC')->orderBy(DB::raw('concat(doctor.apellidopaterno,\' \',doctor.apellidomaterno,\' \',doctor.nombres)'),'asc')->orderBy('Venta.horainicio','ASC');
        $lista            = $resultado->get();
        if (count($lista) > 0) {            
            $pdf = new TCPDF();
            $pdf::SetTitle('Lista de Pacientes');
            $pdf::AddPage();
            $pdf::SetFont('helvetica','B',12);
            $pdf::Cell(0,10,utf8_decode("LISTA DE VentaS"),0,0,'C');
            $pdf::Ln();
            $iddoctorant=0;
            foreach ($lista as $key => $value){
                if($iddoctorant!=$value->doctor_id){
                    if($iddoctorant>0){
                        $pdf::Ln();
                    }

                    $pdf::SetFont('helvetica','B',10);
                    $pdf::Cell(17,9,utf8_decode("FECHA:"),0,0,'L');
                    $pdf::SetFont('helvetica','',10);
                    $pdf::Cell(20,9,utf8_decode($value->fecha),0,0,'L');
                    $pdf::SetFont('helvetica','B',10);
                    $pdf::Cell(20,9,utf8_decode("DOCTOR:"),0,0,'L');
                    $pdf::SetFont('helvetica','',10);
                    $pdf::Cell(55,9,($value->doctor),0,0,'L');
                    $pdf::SetFont('helvetica','B',10);
                    $pdf::Cell(30,9,utf8_decode("ESPECIALIDAD:"),0,0,'L');
                    $pdf::SetFont('helvetica','',10);
                    $pdf::Cell(0,9,utf8_decode($value->especialidad),0,0,'L');
                    $pdf::Ln();
                    $pdf::SetFont('helvetica','B',9);
                    $pdf::Cell(60,6,utf8_decode("PACIENTE"),1,0,'C');
                    $pdf::Cell(20,6,utf8_decode("TIPO PAC."),1,0,'C');
                    $pdf::Cell(23,6,utf8_decode("TELEF."),1,0,'C');
                    $pdf::Cell(18,6,utf8_decode("HISTORIA"),1,0,'C');
                    $pdf::Cell(13,6,utf8_decode("INICIO"),1,0,'C');
                    $pdf::Cell(13,6,utf8_decode("FIN"),1,0,'C');
                    $pdf::Cell(50,6,utf8_decode("CONCEPTO"),1,0,'C');
                    $pdf::Ln();
                    $iddoctorant=$value->doctor_id;
                }
                $pdf::SetFont('helvetica','',8);
                $pdf::Cell(60,5,utf8_decode($value->paciente),1,0,'L');
                $pdf::Cell(20,5,utf8_decode($value->tipopaciente),1,0,'C');
                $pdf::Cell(23,5,utf8_decode($value->telefono),1,0,'C');
                $pdf::Cell(18,5,utf8_decode($value->historia),1,0,'C');
                $pdf::Cell(13,5,utf8_decode(substr($value->horainicio,0,5)),1,0,'C');
                $pdf::Cell(13,5,utf8_decode(substr($value->horafin,0,5)),1,0,'C');
                $pdf::Cell(50,5,utf8_decode($value->comentario),1,0,'L');
                $pdf::Ln();
            }
            $pdf::Output('ListaVenta.pdf');
        }
    }

    public function procesar(Request $request)
    {
        $error = DB::transaction(function() use($request){
            $resultado        = Movimiento::leftjoin('person as paciente', 'paciente.id', '=', 'movimiento.persona_id')
                            ->join('person as responsable', 'responsable.id', '=', 'movimiento.responsable_id')
                            ->leftjoin('movimiento as m2','m2.id','=','movimiento.movimiento_id')
                            ->where('movimiento.tipomovimiento_id','=',4)
                            ->where('movimiento.ventafarmacia','=','N')
                            ->where('m2.tipomovimiento_id','=',1);
            if($request->input('fechainicial')!=""){
                $resultado = $resultado->where('movimiento.fecha','>=',$request->input('fechainicial'));
            }
            if($request->input('fechafinal')!=""){
                $resultado = $resultado->where('movimiento.fecha','<=',$request->input('fechafinal'));
            }        
            if($request->input('tipodocumento')!=""){
                $resultado = $resultado->where('movimiento.tipodocumento_id','=',$request->input('tipodocumento'));
            }        
            if($request->input('numero')!=""){
                $resultado = $resultado->where('movimiento.numero','LIKE','%'.$request->input('numero').'%');
            }        

            $resultado        = $resultado->select('movimiento.*','m2.situacion as situacion2')->orderBy('movimiento.fecha', 'ASC');
            $lista            = $resultado->get();
            foreach ($lista as $key => $value) {
                $numero=($value->tipodocumento_id==4?"F":"B").str_pad($value->serie,3,'0',STR_PAD_LEFT).'-'.str_pad($value->numero,8,'0',STR_PAD_LEFT);
                //if($value->situacionsunat!="E"){
                    $rs=DB::connection('sqlsrv')->table('SPE_EINVOICEHEADER')->where('serieNumero','like',$numero)->first();
                    if(count($rs)>0){
                        $value->situacionbz=$rs->bl_estadoRegistro;
                        if($rs->bl_estadoRegistro=='E'){
                            $value->situacionsunat='E';    
                        }
                    }
                    $rs=DB::connection('sqlsrv')->table('SPE_EINVOICE_RESPONSE')->where('serieNumero','like',$numero)->first();
                    if(count($rs)>0){
                        $value->situacionsunat=$rs->bl_estadoRegistro;
                        $value->mensajesunat=$rs->bl_mensajeSunat;
                    }
                    $value->save();
                //}
            }
        });
        return is_null($error) ? "OK" : $error;
    }

    public function resumen(Request $request){
        $error = DB::transaction(function() use($request){
                $fechainicial=$request->input('fechainicial');
                $fechafinal=$request->input('fechafinal');
                while(strtotime($fechainicial)<=strtotime($fechafinal)){
                    //CABECERA
                    $columna1=6;
                    $columna2="20480082673";//RUC HOSPITAL
                    $columna3="RC-".str_replace('-', '', $fechainicial).'-0001';
                    $columna4=$fechainicial;
                    $columna5=$fechainicial;
                    $columna6="HOSPITAL PRIVADO JUAN PABLO II SOCIEDAD ANONIMA CERRADA";//Razon social Hospital                
                    $columna7="sistemas@hospitaljuanpablo.pe";
                    $columna8=1;
                    $columna9='N';
                    $columna10='RC';
                    DB::connection('sqlsrv')->insert('insert into SPE_SUMMARYHEADER (
                        tipoDocumentoEmisor,
                        numeroDocumentoEmisor,
                        resumenId,
                        fechaEmisionComprobante,
                        fechaGeneracionResumen,
                        razonSocialEmisor,
                        correoEmisor,
                        inHabilitado,
                        bl_estadoRegistro,
                        resumenTipo
                        ) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', 
                        [$columna1, $columna2, $columna3, $columna4, $columna5, $columna6, $columna7, $columna8, $columna9, $columna10]);

                    //DETALLES POR SERIE
                    $rs=Movimiento::where('movimiento.tipomovimiento_id','=',4)
                                    ->where('movimiento.ventafarmacia','=','N')
                                    ->where('movimiento.fecha','>=',$fechainicial)
                                    ->where('movimiento.fecha','<=',$fechainicial)
                                    ->orderBy('movimiento.tipodocumento_id','desc')
                                    ->orderBy('movimiento.serie','asc')
                                    ->orderBy('movimiento.numero','asc')->get();
                    $c=0;$serie='';$tipodocumento_id=0;$subtotal=0;$igv=0;$total=0;
                    foreach ($rs as $key => $value) {
                        if($serie!=$value->serie || $tipodocumento_id!=$value->tipodocumento_id){
                            if($serie!=''){
                                $c=$c+1;
                                $columna4=$c;
                                $columna5=$codigo;
                                $columna6='PEN';
                                $columna7=$abreviatura.str_pad($serie,3,'0',STR_PAD_LEFT);
                                $columna8=$inicio;
                                $columna9=$fin;
                                $columna10=$subtotal;
                                $columna11=0;
                                $columna12=0;
                                $columna13=0;
                                $columna14=0;
                                $columna15=$total;
                                $columna16=0;
                                $columna17=$igv;
                                $columna18=0;

                                DB::connection('sqlsrv')->insert('insert into SPE_SUMMARYDETAIL (
                                tipoDocumentoEmisor,
                                numeroDocumentoEmisor,
                                resumenId,
                                numeroFila,
                                tipoDocumento,
                                tipoMoneda,
                                serieGrupoDocumento,
                                numeroCorrelativoInicio,
                                numeroCorrelativoFin,
                                totalValorVentaOpGravadaConIgv,
                                totalValorVentaOpExoneradasIgv,
                                totalValorVentaOpInafectasIgv,
                                totalValorVentaOpGratuitas,
                                totalOtrosCargos,
                                totalVenta,
                                totalIsc,
                                totalIgv,
                                totalOtrosTributos
                                ) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', 
                                [$columna1, $columna2, $columna3, $columna4, $columna5, $columna6, $columna7, $columna8, $columna9, $columna10, $columna11, $columna12, $columna13, $columna14, $columna15, $columna16, $columna17, $columna18]);

                                $subtotal=0;
                                $igv=0;
                                $total=0;
                            }
                            $serie=$value->serie;
                            $tipodocumento_id=$value->tipodocumento_id;
                            if($value->tipodocumento_id==5){//BOLETA
                                $codigo='03';
                                $abreviatura='B';
                            }else{
                                $codigo='01';
                                $abreviatura='F';
                            }
                            $inicio=str_pad($value->numero,8,'0',STR_PAD_LEFT);
                        }
                        $fin=str_pad($value->numero,8,'0',STR_PAD_LEFT);
                        $subtotal=$subtotal + $value->subtotal;
                        $total=$total + $value->total;
                        $igv=$igv + $value->igv;
                        /*$columna4=$c;
                        if($value->tipodocumento_id==5){//BOLETA
                            $codigo='03';
                            $abreviatura='B';
                            if(strlen($value->persona->dni)<>8 || $value->total<700){
                                $columna8=0;//Tipo Doc. Persona->Paciente DNI // DNI=1  RUC=6  Ninguno=0
                                $columna9='-';
                            }else{
                                $columna8=1;//Tipo Doc. Persona->Paciente DNI // DNI=1  RUC=6  Ninguno=0
                                $columna9=$value->persona->dni;
                            }
                        }else{
                            $codigo='01';
                            $abreviatura='F';
                            $columna8=6;
                            $columna9=$value->persona->ruc;
                        }
                        $columna5=$codigo
                        $columna6='PEN';
                        $columna7=utf8_encode($abreviatura.str_pad($value->serie,3,'0',STR_PAD_LEFT).'-'.str_pad($value->numero,8,'0',STR_PAD_LEFT));


                        DB::connection('sqlsrv')->insert('insert into SPE_SUMMARYITEM” (
                        tipoDocumentoEmisor,
                        numeroDocumentoEmisor,
                        resumenId,
                        numeroFila,
                        tipoDocumento,
                        tipoMoneda,
                        numeroCorrelativo,
                        tipoDocumentoAdquiriente,
                        numeroDocumentoAdquiriente,
                        numeroCorrBoletaModificada,
                        tipoDocumentoModificado,
                        estadoItem,
                        totalValorVentaOpGravadaConIgv,
                        totalValorVentaOpExoneradasIgv,
                        totalValorVentaOpInafectasIgv,
                        totalValorVentaOpGratuitas,
                        totalOtrosCargos,
                        totalVenta,
                        totalIsc,
                        totalIgv,
                        totalOtrosTributos
                        ) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', 
                        [$columna1, $columna2, $columna3, $columna4, $columna5, $columna6, $columna7, $columna8, $columna9, $columna10, $columna11, $columna12, $columna13, $columna14, $columna15, $columna16, $columna17, $columna18, $columna19, $columna20, $columna21]);
                        */
                    }

                    $c=$c+1;
                    $columna4=$c;
                    $columna5=$codigo;
                    $columna6='PEN';
                    $columna7=$abreviatura.str_pad($serie,3,'0',STR_PAD_LEFT);
                    $columna8=$inicio;
                    $columna9=$fin;
                    $columna10=$subtotal;
                    $columna11=0;
                    $columna12=0;
                    $columna13=0;
                    $columna14=0;
                    $columna15=$total;
                    $columna16=0;
                    $columna17=$igv;
                    $columna18=0;

                    DB::connection('sqlsrv')->insert('insert into SPE_SUMMARYDETAIL (
                    tipoDocumentoEmisor,
                    numeroDocumentoEmisor,
                    resumenId,
                    numeroFila,
                    tipoDocumento,
                    tipoMoneda,
                    serieGrupoDocumento,
                    numeroCorrelativoInicio,
                    numeroCorrelativoFin,
                    totalValorVentaOpGravadaConIgv,
                    totalValorVentaOpExoneradasIgv,
                    totalValorVentaOpInafectasIgv,
                    totalValorVentaOpGratuitas,
                    totalOtrosCargos,
                    totalVenta,
                    totalIsc,
                    totalIgv,
                    totalOtrosTributos
                    ) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', 
                    [$columna1, $columna2, $columna3, $columna4, $columna5, $columna6, $columna7, $columna8, $columna9, $columna10, $columna11, $columna12, $columna13, $columna14, $columna15, $columna16, $columna17, $columna18]);

                    /*
                        PARA FARMACIA
                    */

                    //DETALLES POR SERIE

                    $rs=Movimiento::where('movimiento.tipomovimiento_id','=',4)
                                    ->where('movimiento.ventafarmacia','=','S')
                                    ->where('movimiento.tipodocumento_id','<>',15)
                                    ->where('movimiento.fecha','>=',$fechainicial)
                                    ->where('movimiento.fecha','<=',$fechainicial)
                                    ->orderBy('movimiento.tipodocumento_id','desc')
                                    ->orderBy('movimiento.serie','asc')
                                    ->orderBy('movimiento.numero','asc')->get();

                    $serie='';$tipodocumento_id=0;$subtotal=0;$igv=0;$total=0;$totalinafecta=0;$inicio='';

                    foreach ($rs as $key => $value) {
                        $ind = 0;
                        $listdetalles = Detallemovimiento::where('movimiento_id','=',$value->id)->get();
                        foreach ($listdetalles as $key3 => $value3) {
                            if ($value3->producto->afecto == 'NO') {
                                $ind = 1;
                            }
                        }
                        if($serie!=$value->serie || $tipodocumento_id!=$value->tipodocumento_id){
                            if($serie!=''){
                                $c=$c+1;
                                $columna4=$c;
                                $columna5=$codigo;
                                $columna6='PEN';
                                $columna7=$abreviatura.str_pad($serie,3,'0',STR_PAD_LEFT);
                                $columna8=$inicio;
                                $columna9=$fin;
                                $columna10=$subtotal;
                                $columna11=0;
                                $columna12=$totalinafecta;
                                $columna13=0;
                                $columna14=0;
                                $columna15=$total;
                                $columna16=0;
                                $columna17=$igv;
                                $columna18=0;
                                
                                


                                DB::connection('sqlsrv')->insert('insert into SPE_SUMMARYDETAIL (
                                tipoDocumentoEmisor,
                                numeroDocumentoEmisor,
                                resumenId,
                                numeroFila,
                                tipoDocumento,
                                tipoMoneda,
                                serieGrupoDocumento,
                                numeroCorrelativoInicio,
                                numeroCorrelativoFin,
                                totalValorVentaOpGravadaConIgv,
                                totalValorVentaOpExoneradasIgv,
                                totalValorVentaOpInafectasIgv,
                                totalValorVentaOpGratuitas,
                                totalOtrosCargos,
                                totalVenta,
                                totalIsc,
                                totalIgv,
                                totalOtrosTributos
                                ) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', 

                                [$columna1, $columna2, $columna3, $columna4, $columna5, $columna6, $columna7, $columna8, $columna9, $columna10, $columna11, $columna12, $columna13, $columna14, $columna15, $columna16, $columna17, $columna18]);

                                $subtotal=0;
                                $igv=0;
                                $total=0;
                                $totalinafecta = 0;

                            }

                            $serie=$value->serie;
                            $tipodocumento_id=$value->tipodocumento_id;
                            if($value->tipodocumento_id==5){//BOLETA
                                $codigo='03';
                                $abreviatura='B';
                            }else{
                                $codigo='01';
                                $abreviatura='F';
                            }
                            $inicio=str_pad($value->numero,8,'0',STR_PAD_LEFT);
                        }

                        $fin=str_pad($value->numero,8,'0',STR_PAD_LEFT);
                        if ($ind == 0) {
                            $subtotal=$subtotal + $value->subtotal;
                            $total=$total + $value->total;
                            $igv=$igv + $value->igv;
                        }else{
                            $totalinafecta=$totalinafecta + $value->total;
                            $total=$total + $value->total;
                        }
                        

                    }


                    $c=$c+1;
                    $columna4=$c;
                    $columna5=$codigo;
                    $columna6='PEN';
                    $columna7=$abreviatura.str_pad($serie,3,'0',STR_PAD_LEFT);
                    $columna8=$inicio;
                    $columna9=$fin;
                    $columna10=$subtotal;
                    $columna11=0;
                    $columna12=$totalinafecta;
                    $columna13=0;
                    $columna14=0;
                    $columna15=$total;
                    $columna16=0;
                    $columna17=$igv;
                    $columna18=0;


                    DB::connection('sqlsrv')->insert('insert into SPE_SUMMARYDETAIL (
                    tipoDocumentoEmisor,
                    numeroDocumentoEmisor,
                    resumenId,
                    numeroFila,
                    tipoDocumento,
                    tipoMoneda,
                    serieGrupoDocumento,
                    numeroCorrelativoInicio,
                    numeroCorrelativoFin,
                    totalValorVentaOpGravadaConIgv,
                    totalValorVentaOpExoneradasIgv,
                    totalValorVentaOpInafectasIgv,
                    totalValorVentaOpGratuitas,
                    totalOtrosCargos,
                    totalVenta,
                    totalIsc,
                    totalIgv,
                    totalOtrosTributos
                    ) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', 

                    [$columna1, $columna2, $columna3, $columna4, $columna5, $columna6, $columna7, $columna8, $columna9, $columna10, $columna11, $columna12, $columna13, $columna14, $columna15, $columna16, $columna17, $columna18]);

                    DB::connection('sqlsrv')->update('update SPE_SUMMARYHEADER set bl_estadoRegistro = ? where resumenId  = ?',
                            ['A',$columna3]);

                    $fechainicial=date("Y-m-d",strtotime('+1 day',strtotime($fechainicial)));
                }
            });
        return is_null($error) ? "OK" : $error;

    }

    public function resumen1(Request $request){
        $error = DB::transaction(function() use($request){
            /*$lista = Movimiento::where('tipodocumento_id','=',5)
                        ->where('tipomovimiento_id','=',4)
                        ->whereIn('fecha',['2018-01-10','2018-01-12','2018-01-17'])
                        ->where('serie','=','4')
                        ->select('*')
                        ->orderBy('fecha','asc')
                        ->orderBy('serie','asc')
                        ->orderBy('numero','asc')
                        ->get();
            foreach ($lista as $key => $value) {
                if($value->serie!="4"){
                    $venta        = new Movimiento();
                    $venta->fecha = $value->fecha;
                    $numeroventa = Movimiento::NumeroSigue(4,5,3,'N');
                    $venta->numero=$numeroventa;
                    $venta->serie = '3';
                    $venta->responsable_id=$value->responsable_id;
                    $venta->persona_id=$value->persona_id;
                    $venta->subtotal=$value->subtotal;
                    $venta->igv=$value->igv;
                    $venta->total=$value->total;     
                    $venta->tipomovimiento_id=4;
                    $venta->tipodocumento_id=5;
                    $venta->comentario='';
                    $venta->manual='N';
                    $venta->situacion=$value->situacion;        
                    $venta->descuentoplanilla=$value->descuentoplanilla;
                    $venta->personal_id=$value->personal_id;
                    $venta->movimiento_id=$value->movimiento_id;
                    $venta->ventafarmacia='N';
                    $venta->save();
                    
                    $caja=Movimiento::where('movimiento_id','=',$value->id)
                            ->where('tipomovimiento_id','=',2)
                            ->first();
                    if(count($caja)>0){
                        $caja->movimiento_id=$venta->id;
                        $caja->save();
                    }
                    $value->situacion='U';
                    $venta->save();
                }else{
                    $venta  = new Movimiento();
                    $venta->serie = 4;
                    $venta->tipodocumento_id = 5;
                    $venta->persona_id = $value->persona_id;
                    $venta->nombrepaciente = $value->nombrepaciente;
                    $venta->empresa_id = $value->empresa_id;
                    $venta->tipomovimiento_id = 4;
                    $venta->almacen_id = 1;
                    
                    $numeroventa = Movimiento::NumeroSigue(4,5,4,'N');
                    $venta->numero=$numeroventa;
                    $venta->fecha  = $value->fecha;
                    $venta->subtotal=$value->total;
                    $venta->igv=$value->igv;
                    $venta->total = $value->total;
                    $venta->credito = $value->credito;
                    $venta->tipoventa = $value->tipoventa;
                    $venta->formapago = $value->formapago;
                    $venta->tarjeta=$value->tipotarjeta;//VISA/MASTER
                    $venta->tipotarjeta=$value->tipotarjeta2;//DEBITO/CREDITO
                    $venta->conveniofarmacia_id = $value->conveniofarmacia_id;
                    $venta->descuentokayros = $value->descuentokayros;
                    $venta->copago = $value->copago;
                           
                    $venta->inicial = 'N';
                    $venta->estadopago = $value->estadopago;
                    $venta->ventafarmacia = 'S';
                    $venta->manual='N';
                    $venta->descuentoplanilla = $value->descuentoplanilla;
                    $venta->responsable_id = $value->responsable_id;
                    $venta->doctor_id = $value->doctor_id;
                    $venta->save();
                    
                    $resultado1 = Detallemovimiento::where('detallemovimiento.movimiento_id','=',$value->id)
                                ->select('Detallemovimiento.*');
                    $lista1      = $resultado1->get();
                    foreach ($lista1 as $k => $v) {
                        $detalleVenta = new Detallemovimiento();
                        $detalleVenta->cantidad = $v->cantidad;
                        $detalleVenta->precio = $v->precio;
                        $detalleVenta->subtotal = $v->subtotal;
                        $detalleVenta->movimiento_id = $venta->id;
                        $detalleVenta->producto_id = $v->producto_id;
                        $detalleVenta->save();

                        //$kardex = Kardex::where('detallemovimiento_id','=',$v->id)
                        //            ->select('*')
                        //            ->get();
                        //$kardex = Kardex::where('producto_id','=',$v->producto_id)
                        //            ->where('fecha','=',$value->fecha)
                        //           ->where('cantidad',$v->cantidad)
                        //            ->where('tipo','like','S')
                        //            ->select('*')
                        //            ->get();
                        //foreach ($kardex as $key1 => $value1) {
                        //    $value1->detallemovimiento_id=$detalleVenta->id;
                        //    $value1->save();
                        //}
                    }
                    //$caja=Movimiento::where('movimiento_id','=',$value->id)
                    //        ->where('tipomovimiento_id','=',2)
                    //        ->first();
                    if($value->persona_id>0)
                        $caja=Movimiento::where('caja_id','=',4)
                            ->where('tipomovimiento_id','=',2)
                            ->where('tipomovimiento_id','=',5)
                            ->where('fecha','=',$value->fecha)
                            ->where('total','=',$value->total)
                            ->where('persona_id','=',$value->persona_id)
                            ->first();                            
                    else
                        $caja=Movimiento::where('caja_id','=',4)
                            ->where('tipomovimiento_id','=',2)
                            ->where('tipomovimiento_id','=',5)
                            ->where('fecha','=',$value->fecha)
                            ->where('total','=',$value->total)
                            ->where('nombrepaciente','like',$value->nombrepaciente)
                            ->first();                            
                    if(count($caja)>0){
                        $caja->movimiento_id=$venta->id;
                        $caja->numero=$venta->numero;
                        $caja->save();
                    }
                    $value->situacion='U';
                    $value->save();
                }
            }*/
            /*$lista = Movimiento::where('tipodocumento_id','=',13)
                        ->where('tipomovimiento_id','=',6)
                        ->whereIn('fecha',['2018-01-10','2018-01-12','2018-01-17'])
                        ->where('fecha','>=','2018-01-01')
                        ->where('fecha','<=','2018-01-31')
                        ->whereIn('situacionsunat',['L','R'])
                        ->select('*')
                        ->orderBy('fecha','asc')
                        ->orderBy('serie','asc')
                        ->orderBy('numero','asc')
                        ->get();
            foreach ($lista as $key => $value) {
                $Movimiento = new Movimiento();
                $Movimiento->fecha = date("Y-m-d");
                $Movimiento->serie = 2;
                $numero = Movimiento::NumeroSigue(6,13,2,'N');
                $Movimiento->numero = $numero;
                $Movimiento->persona_id = $value->persona_id;
                $Movimiento->total = $value->total;
                $Movimiento->subtotal = $value->subtotal;
                $Movimiento->igv = $value->igv;
                $Movimiento->responsable_id=$value->responsable_id;
                $Movimiento->movimiento_id = $value->movimiento_id;
                $Movimiento->situacion='N';//Normal
                $Movimiento->tipomovimiento_id = 6;
                $Movimiento->tipodocumento_id = 13;
                $Movimiento->comentario = $value->comentario;
                $Movimiento->manual='N';
                $Movimiento->save();
                $resultado1 = Detallemovcaja::where('movimiento_id','=',$value->id)
                                ->select('*');
                    $lista1      = $resultado1->get();
                foreach ($lista1 as $k => $v) {
                    $Detalle = new Detallemovcaja();
                    $Detalle->movimiento_id=$Movimiento->id;
                    $Detalle->persona_id=$v->persona_id;
                    $Detalle->cantidad=$v->cantidad;
                    $Detalle->precio=$v->precio;
                    $Detalle->servicio_id=$v->servicio_id;
                    $Detalle->pagohospital=$v->pagohospital;
                    $Detalle->descripcion=$v->descripcion;
                    $Detalle->descuento=0;
                    $Detalle->save();
                }
                $caja=Movimiento::where('tipomovimiento_id','=',2)
                    ->where('tipomovimiento_id','=',5)
                    ->where('fecha','=',$value->fecha)
                    ->where('total','=',$value->total)
                    ->where('movimiento_id','=',$value->id)
                    ->first();                            
                if(count($caja)>0){
                    $caja->movimiento_id=$Movimiento->id;
                    $caja->save();
                }
                $value->situacion='U';
                $value->save();
            }*/
            for($c=617;$c<=865;$c++){
                $nota = Movimiento::join('movimiento as m2','m2.id','=','movimiento.movimiento_id')
                        ->where('movimiento.numero','=',$c)
                        ->where('movimiento.serie','=',2)
                        ->where('movimiento.tipodocumento_id','=',13)
                        ->whereIn('m2.tipodocumento_id',[4,17])
                        ->whereIn('m2.tipomovimiento_id',[4,9])
                        ->select('movimiento.fecha')
                        ->first();
                if(!is_null($nota)){
                    $fecha=$nota->fecha;
                }else{
                    $venta  = new Movimiento();
                    $venta->serie = 2;
                    $venta->tipodocumento_id = 13;
                    $venta->persona_id = 57371;
                    $venta->tipomovimiento_id = 6;
                    
                    $numeroventa = $c;
                    $venta->numero=$numeroventa;
                    $venta->fecha  = $fecha;
                    $venta->subtotal=0;
                    $venta->igv=0;
                    $venta->total = 0;
                    $venta->comentario = 'Anulacion de la operacion';
                    $venta->situacion = 'N';
                    $venta->movimiento_id = 148032;
                    $venta->formapago='MI';
                           
                    $venta->manual='N';
                    $venta->responsable_id = 1;
                    $venta->save();
                    
                    $Detalle = new Detallemovcaja();
                    $Detalle->movimiento_id=$venta->id;
                    $Detalle->persona_id=294;
                    $Detalle->cantidad=1;
                    $Detalle->precio=0;
                    $Detalle->servicio_id=12244;
                    $Detalle->pagohospital=0;
                    $Detalle->descuento=0;
                    $Detalle->save();
                }
            }

        });
        return is_null($error) ? "OK" : $error;
    }

    public function ventaautocompletar($searching)
    {
        $resultado        = Movimiento::where(DB::raw('CONCAT(case when tipodocumento_id=4 or tipodocumento_id=17 then "F" else "B" end,serie,"-",numero)'), 'LIKE', '%'.trim($searching).'%')
                            ->where('ventafarmacia','=','N')
                            ->whereNotIn('situacion',['A','U'])
                            ->orderBy('serie', 'ASC')
                            ->orderBy('numero', 'ASC');
        $list      = $resultado->get();
        $data = array();
        foreach ($list as $key => $value) {
            if($value->tipodocumento_id=="4"){
                $paciente=$value->persona->bussinesname;
                $paciente2="";
            }else{
                if($value->tipodocumento_id=="17"){
                    $paciente=$value->empresa->bussinesname;
                    $paciente2=$value->persona->apellidopaterno.' '.$value->persona->apellidomaterno.' '.$value->persona->nombres;
                }else{
                    $paciente=$value->persona->apellidopaterno.' '.$value->persona->apellidomaterno.' '.$value->persona->nombres;
                    $paciente2=$paciente;
                }
            }

            $data[] = array(
                            'label' => ($value->tipodocumento_id=='4'?'F':($value->tipodocumento_id=='17')?'F':'B').trim($value->serie.'-'.$value->numero),
                            'id'    => $value->id,
                            'value' => ($value->tipodocumento_id=='4'?'F':($value->tipodocumento_id=='17')?'F':'B').trim($value->serie.'-'.$value->numero),
                            'paciente'   => $paciente,
                            'paciente2' => $paciente2,
                            'person_id' => $value->tipodocumento_id=='17'?$value->empresa_id:$value->persona_id,
                            'value2' => ($value->tipodocumento_id=='4'?'F':($value->tipodocumento_id=='17')?'F':'B').trim($value->serie.'-'.$value->numero).' | '.$value->total,
                            'total' => $value->total,
                        );
        }
        return json_encode($data);
    }

    public function excelConcar(Request $request){
        setlocale(LC_TIME, 'spanish');
        $resultado        = Movimiento::leftjoin('person as paciente', 'paciente.id', '=', 'movimiento.persona_id')
                            ->join('person as responsable', 'responsable.id', '=', 'movimiento.responsable_id')
                            ->leftjoin('movimiento as m2','m2.id','=','movimiento.movimiento_id')
                            ->where('movimiento.tipomovimiento_id','=',4)
                            ->where('movimiento.ventafarmacia','=','N')
                            ->where('m2.tipomovimiento_id','=',1);
        if($request->input('fechainicial')!=""){
            $resultado = $resultado->where('movimiento.fecha','>=',$request->input('fechainicial').' 00:00:00');
        }
        if($request->input('fechafinal')!=""){
            $resultado = $resultado->where('movimiento.fecha','<=',$request->input('fechafinal').' 23:59:59');
        }        

        $resultado        = $resultado->select('movimiento.*','m2.situacion as situacion2','m2.tarjeta as tarjeta2',DB::raw('CONCAT(case when movimiento.tipodocumento_id=4 then "F" else "B" end,movimiento.serie,"-",movimiento.numero) as numero2'))->orderBy('movimiento.fecha', 'ASC')->get();

        Excel::create('ExcelVentaConcar', function($excel) use($resultado,$request) {
 
            $excel->sheet('Ventas', function($sheet) use($resultado,$request) {
 
                $array = array();
                $cabecera = array();
                $cabecera[] = "Sub Diario";
                $cabecera[] = "Nro. Comprobante";
                $cabecera[] = "Fecha Comprobante";
                $cabecera[] = "Codigo Moneda";
                $cabecera[] = "Glosa Principal";
                $cabecera[] = "Tipo de Cambio";
                $cabecera[] = "Tipo de Conversion";
                $cabecera[] = "Flag de Conversion";
                $cabecera[] = "Fecha de Tipo de Cambio";
                $cabecera[] = "Cuenta Contable";
                $cabecera[] = "Codigo Anexo";
                $cabecera[] = "Codig Centro Costo";
                $cabecera[] = "Debe / Haber";
                $cabecera[] = "Importe Original";
                $cabecera[] = "Importe Dolares";
                $cabecera[] = "Importe Soles";
                $cabecera[] = "Tipo Documento";
                $cabecera[] = "Nro Documento";
                $cabecera[] = "Fecha Documento";
                $cabecera[] = "Fecha Vencimiento";
                $cabecera[] = "Codig Area";
                $cabecera[] = "Glosa Detalle";
                $cabecera[] = "Codigo Anexo Auxiliar";
                $cabecera[] = "Medio Pago";
                $cabecera[] = "Tipo de Documento de Referencia";
                $cabecera[] = "Nro Documento de Referencia";
                $cabecera[] = "Fecha Documento de Referencia";
                $cabecera[] = "Nro Maq. Registradora";
                $cabecera[] = "Base Imponible Doc. Referencia";
                $cabecera[] = "IGV Documento Provision";
                $cabecera[] = "Tipo de Referencia en Estado";
                $cabecera[] = "Nro de Serie de Caja Registradora";
                $cabecera[] = "Fecha de Operacion";
                $cabecera[] = "Tipo de Tasa";
                $cabecera[] = "Tasa de Detraccion/Percepcion";
                $cabecera[] = "Importe Base Detraccion/Percepcion Dolares";
                $cabecera[] = "Importe Base Detraccion/Percepcion Soles";
                $cabecera[] = "Tipo de Cambio para F";
                $cabecera[] = "Importe de IGV sin derecho a Credito Fiscal";
                $array[] = $cabecera;
                $c=1;$d=3;
                foreach ($resultado as $key => $value){
                    $detalle = Detallemovcaja::where('movimiento_id','=',$value->movimiento_id)->first();
                    if($detalle->servicio_id>0){
                        $glosa=$detalle->servicio->tiposervicio->nombre;
                    }else{
                        $glosa='VARIOS';
                    }
                    $detalle = array();
                    $detalle[] = "05";
                    $detalle[] = date('m',strtotime($value->fecha)).str_pad($c,4,'0',STR_PAD_LEFT);
                    $detalle[] = date('d/m/Y',strtotime($value->fecha));
                    $detalle[] = "MN";
                    $detalle[] = "VENTAS ".strtoupper(strftime("%B",strtotime($value->fecha)))." DEL ".date("Y",strtotime($value->fecha));
                    $detalle[] = "0";
                    $detalle[] = "M";
                    $detalle[] = "S";
                    $detalle[] = "";
                    $detalle[] = "401111";
                    $person = Person::find($value->persona_id);
                    if ($person !== null) {
                        if(strlen($person->dni)<>8 || $value->total<700){
                            $detalle[] = "0000";
                        }else{
                            if($value->tipodocumento_id==5)//boleta
                                $detalle[] = $person->dni;
                            else
                                $detalle[] = $person->ruc;
                        }
                    }else{
                        $detalle[] = "0000";
                    }
                    
                    $detalle[] = "";
                    $detalle[] = "H";
                    $detalle[] = $value->igv;
                    $detalle[] = "=O".($d)."/F".($d);
                    $detalle[] = "";
                    $detalle[] = $value->tipodocumento_id==5?'BV':'FT';
                    $detalle[] = $value->serie.'-'.$value->numero;
                    $detalle[] = date('d/m/Y',strtotime($value->fecha));
                    $detalle[] = "";
                    $detalle[] = "";
                    $detalle[] = $glosa;
                    $person = Person::find($value->persona_id);
                    if ($person !== null) {
                        if(strlen($person->dni)<>8 || $value->total<700){
                            $detalle[] = "0000";
                        }else{
                            if($value->tipodocumento_id==5)//boleta
                                $detalle[] = $person->dni;
                            else
                                $detalle[] = $person->ruc;
                        }
                    }else{
                        $detalle[] = "0000";
                    }
                    $array[] = $detalle;
                    $d=$d+1;

                    //SUBTOTAL
                    $detalle = array();
                    $detalle[] = "05";
                    $detalle[] = date('m',strtotime($value->fecha)).str_pad($c,4,'0',STR_PAD_LEFT);
                    $detalle[] = date('d/m/Y',strtotime($value->fecha));
                    $detalle[] = "MN";
                    $detalle[] = "VENTAS ".strtoupper(strftime("%B",strtotime($value->fecha)))." DEL ".date("Y",strtotime($value->fecha));
                    $detalle[] = "0";
                    $detalle[] = "M";
                    $detalle[] = "S";
                    $detalle[] = "";
                    $detalle[] = "704105";
                    $person = Person::find($value->persona_id);
                    $person = Person::find($value->persona_id);
                    if ($person != null) {
                        if(strlen($person->dni)<>8 || $value->total<700){
                            $detalle[] = "0000";
                        }else{
                            if($value->tipodocumento_id==5)//boleta
                                $detalle[] = $person->dni;
                            else
                                $detalle[] = $person->ruc;
                        }
                    }else{
                        $detalle[] = "0000";
                    }
                    $detalle[] = "";
                    $detalle[] = "H";
                    $detalle[] = $value->subtotal;
                    $detalle[] = "=O".($d)."/F".($d);
                    $detalle[] = "";
                    $detalle[] = '';
                    $detalle[] = $value->serie.'-'.$value->numero;
                    $detalle[] = date('d/m/Y',strtotime($value->fecha));
                    $detalle[] = "";
                    $detalle[] = "";
                    $detalle[] = $glosa;
                    $person = Person::find($value->persona_id);
                    if ($person != null) {
                        if(strlen($person->dni)<>8 || $value->total<700){
                            $detalle[] = "0000";
                        }else{
                            if($value->tipodocumento_id==5)//boleta
                                $detalle[] = $person->dni;
                            else
                                $detalle[] = $person->ruc;
                        }
                    }else{
                        $detalle[] = "0000";
                    }
                    $array[] = $detalle;
                    $d=$d+1;

                    //TOTAL
                    $detalle = array();
                    $detalle[] = "05";
                    $detalle[] = date('m',strtotime($value->fecha)).str_pad($c,4,'0',STR_PAD_LEFT);
                    $detalle[] = date('d/m/Y',strtotime($value->fecha));
                    $detalle[] = "MN";
                    $detalle[] = "VENTAS ".strtoupper(strftime("%B",strtotime($value->fecha)))." DEL ".date("Y",strtotime($value->fecha));
                    $detalle[] = "0";
                    $detalle[] = "M";
                    $detalle[] = "S";
                    $detalle[] = "";
                    if($value->situacion=='P'){
                        $detalle[] = "121204";//VENTA CREDITO
                    }else{
                        if($value->tarjeta!=""){
                            $detalle[] = "121205";//VENTA TARJETA
                        }else{
                            $detalle[] = "121203";//VENTA CONTADO
                        }
                    }
                    $person = Person::find($value->persona_id);
                    if ($person != null) {
                        if(strlen($person->dni)<>8 || $value->total<700){
                            $detalle[] = "0000";
                        }else{
                            if($value->tipodocumento_id==5)//boleta
                                $detalle[] = $person->dni;
                            else
                                $detalle[] = $person->ruc;
                        }
                    }else{
                        $detalle[] = "0000";
                    }
                    
                    $detalle[] = "";
                    $detalle[] = "D";
                    $detalle[] = $value->total;
                    $detalle[] = "=O".($d)."/F".($d);
                    $detalle[] = "";
                    $detalle[] = $value->tipodocumento_id==5?'BV':'FT';
                    $detalle[] = $value->serie.'-'.$value->numero;
                    $detalle[] = date('d/m/Y',strtotime($value->fecha));
                    $detalle[] = date('d/m/Y',strtotime($value->fecha));
                    $detalle[] = "";
                    $detalle[] = $glosa;
                    $person = Person::find($value->persona_id);
                    if ($person != null) {
                        if(strlen($person->dni)<>8 || $value->total<700){
                            $detalle[] = "0000";
                        }else{
                            if($value->tipodocumento_id==5)//boleta
                                $detalle[] = $person->dni;
                            else
                                $detalle[] = $person->ruc;
                        }
                    }else{
                        $detalle[] = "0000";
                    }
                    
                    $array[] = $detalle;
                    $c=$c+1;
                    $d=$d+1;
                }

                $resultado2        = Movimiento::leftjoin('person as paciente', 'paciente.id', '=', 'movimiento.persona_id')
                            ->join('person as responsable', 'responsable.id', '=', 'movimiento.responsable_id')
                            ->leftjoin('movimiento as m2','m2.id','=','movimiento.movimiento_id')
                            ->where('movimiento.tipomovimiento_id','=',4)
                            ->where('movimiento.ventafarmacia','=','S')
                            ->where('m2.tipomovimiento_id','=',2);
                if($request->input('fechainicial')!=""){
                    $resultado2 = $resultado2->where('movimiento.fecha','>=',$request->input('fechainicial').' 00:00:00');
                }
                if($request->input('fechafinal')!=""){
                    $resultado2 = $resultado2->where('movimiento.fecha','<=',$request->input('fechafinal').' 23:59:59');
                }        

                $resultado2        = $resultado2->select('movimiento.*','m2.situacion as situacion2','m2.tarjeta as tarjeta2',DB::raw('CONCAT(case when movimiento.tipodocumento_id=4 then "F" else "B" end,movimiento.serie,"-",movimiento.numero) as numero2'))->orderBy('movimiento.fecha', 'ASC')->get();

                foreach ($resultado2 as $key => $value){
                    $detalle = Detallemovcaja::where('movimiento_id','=',$value->movimiento_id)->first();
                    if($detalle->servicio_id>0){
                        $glosa=$detalle->servicio->tiposervicio->nombre;
                    }else{
                        $glosa='VARIOS';
                    }
                    $detalle = array();
                    $detalle[] = "05";
                    $detalle[] = date('m',strtotime($value->fecha)).str_pad($c,4,'0',STR_PAD_LEFT);
                    $detalle[] = date('d/m/Y',strtotime($value->fecha));
                    $detalle[] = "MN";
                    $detalle[] = "VENTAS ".strtoupper(strftime("%B",strtotime($value->fecha)))." DEL ".date("Y",strtotime($value->fecha));
                    $detalle[] = "0";
                    $detalle[] = "M";
                    $detalle[] = "S";
                    $detalle[] = "";
                    $detalle[] = "401111";
                    $person = Person::find($value->persona_id);
                    if ($person !== null) {
                        if(strlen($person->dni)<>8 || $value->total<700){
                            $detalle[] = "0000";
                        }else{
                            if($value->tipodocumento_id==5)//boleta
                                $detalle[] = $person->dni;
                            else
                                $detalle[] = $person->ruc;
                        }
                    }else{
                        $detalle[] = "0000";
                    }
                    
                    $detalle[] = "";
                    $detalle[] = "H";
                    $detalle[] = $value->igv;
                    $detalle[] = "=O".($d)."/F".($d);
                    $detalle[] = "";
                    $detalle[] = $value->tipodocumento_id==5?'BV':'FT';
                    $detalle[] = $value->serie.'-'.$value->numero;
                    $detalle[] = date('d/m/Y',strtotime($value->fecha));
                    $detalle[] = "";
                    $detalle[] = "";
                    $detalle[] = $glosa;
                    $person = Person::find($value->persona_id);
                    if ($person !== null) {
                        if(strlen($person->dni)<>8 || $value->total<700){
                            $detalle[] = "0000";
                        }else{
                            if($value->tipodocumento_id==5)//boleta
                                $detalle[] = $person->dni;
                            else
                                $detalle[] = $person->ruc;
                        }
                    }else{
                        $detalle[] = "0000";
                    }
                    $array[] = $detalle;
                    $d=$d+1;

                    //SUBTOTAL
                    $detalle = array();
                    $detalle[] = "05";
                    $detalle[] = date('m',strtotime($value->fecha)).str_pad($c,4,'0',STR_PAD_LEFT);
                    $detalle[] = date('d/m/Y',strtotime($value->fecha));
                    $detalle[] = "MN";
                    $detalle[] = "VENTAS ".strtoupper(strftime("%B",strtotime($value->fecha)))." DEL ".date("Y",strtotime($value->fecha));
                    $detalle[] = "0";
                    $detalle[] = "M";
                    $detalle[] = "S";
                    $detalle[] = "";
                    $detalle[] = "704105";
                    $person = Person::find($value->persona_id);
                    $person = Person::find($value->persona_id);
                    if ($person != null) {
                        if(strlen($person->dni)<>8 || $value->total<700){
                            $detalle[] = "0000";
                        }else{
                            if($value->tipodocumento_id==5)//boleta
                                $detalle[] = $person->dni;
                            else
                                $detalle[] = $person->ruc;
                        }
                    }else{
                        $detalle[] = "0000";
                    }
                    $detalle[] = "";
                    $detalle[] = "H";
                    $detalle[] = $value->subtotal;
                    $detalle[] = "=O".($d)."/F".($d);
                    $detalle[] = "";
                    $detalle[] = '';
                    $detalle[] = $value->serie.'-'.$value->numero;
                    $detalle[] = date('d/m/Y',strtotime($value->fecha));
                    $detalle[] = "";
                    $detalle[] = "";
                    $detalle[] = $glosa;
                    $person = Person::find($value->persona_id);
                    if ($person != null) {
                        if(strlen($person->dni)<>8 || $value->total<700){
                            $detalle[] = "0000";
                        }else{
                            if($value->tipodocumento_id==5)//boleta
                                $detalle[] = $person->dni;
                            else
                                $detalle[] = $person->ruc;
                        }
                    }else{
                        $detalle[] = "0000";
                    }
                    $array[] = $detalle;
                    $d=$d+1;

                    //TOTAL
                    $detalle = array();
                    $detalle[] = "05";
                    $detalle[] = date('m',strtotime($value->fecha)).str_pad($c,4,'0',STR_PAD_LEFT);
                    $detalle[] = date('d/m/Y',strtotime($value->fecha));
                    $detalle[] = "MN";
                    $detalle[] = "VENTAS ".strtoupper(strftime("%B",strtotime($value->fecha)))." DEL ".date("Y",strtotime($value->fecha));
                    $detalle[] = "0";
                    $detalle[] = "M";
                    $detalle[] = "S";
                    $detalle[] = "";
                    if($value->situacion=='P'){
                        $detalle[] = "121204";//VENTA CREDITO
                    }else{
                        if($value->tarjeta!=""){
                            $detalle[] = "121205";//VENTA TARJETA
                        }else{
                            $detalle[] = "121203";//VENTA CONTADO
                        }
                    }
                    $person = Person::find($value->persona_id);
                    if ($person != null) {
                        if(strlen($person->dni)<>8 || $value->total<700){
                            $detalle[] = "0000";
                        }else{
                            if($value->tipodocumento_id==5)//boleta
                                $detalle[] = $person->dni;
                            else
                                $detalle[] = $person->ruc;
                        }
                    }else{
                        $detalle[] = "0000";
                    }
                    
                    $detalle[] = "";
                    $detalle[] = "D";
                    $detalle[] = $value->total;
                    $detalle[] = "=O".($d)."/F".($d);
                    $detalle[] = "";
                    $detalle[] = $value->tipodocumento_id==5?'BV':'FT';
                    $detalle[] = $value->serie.'-'.$value->numero;
                    $detalle[] = date('d/m/Y',strtotime($value->fecha));
                    $detalle[] = date('d/m/Y',strtotime($value->fecha));
                    $detalle[] = "";
                    $detalle[] = $glosa;
                    $person = Person::find($value->persona_id);
                    if ($person != null) {
                        if(strlen($person->dni)<>8 || $value->total<700){
                            $detalle[] = "0000";
                        }else{
                            if($value->tipodocumento_id==5)//boleta
                                $detalle[] = $person->dni;
                            else
                                $detalle[] = $person->ruc;
                        }
                    }else{
                        $detalle[] = "0000";
                    }
                    
                    $array[] = $detalle;
                    $c=$c+1;
                    $d=$d+1;
                }

                $sheet->fromArray($array);
 
            });
        })->export('xls');
    }

    public function excelSunatConvenio(Request $request){
        setlocale(LC_TIME, 'spanish');
        $resultado        = Movimiento::leftjoin('person as paciente', 'paciente.id', '=', 'movimiento.persona_id')
                            ->join('person as responsable', 'responsable.id', '=', 'movimiento.responsable_id')
                            ->where('movimiento.tipodocumento_id','=',14);
        if($request->input('fechainicial')!=""){
            $resultado = $resultado->where('movimiento.fecha','>=',$request->input('fechainicial').' 00:00:00');
        }
        if($request->input('fechafinal')!=""){
            $resultado = $resultado->where('movimiento.fecha','<=',$request->input('fechafinal').' 23:59:59');
        }        

        $resultado        = $resultado->select('movimiento.*',DB::raw('CONCAT("F",movimiento.serie,"-",movimiento.numero) as numero2'))->orderBy('movimiento.fecha', 'ASC')->orderBy('movimiento.serie', 'ASC')->orderBy('movimiento.numero', 'ASC')->get();

        Excel::create('ExcelVentaSunatCovenio', function($excel) use($resultado,$request) {
 
            $excel->sheet('VentasConvenio', function($sheet) use($resultado,$request) {
 
                $array = array();
                $cabecera = array();
                $cabecera[] = "";
                $cabecera[] = "Codigo";
                $cabecera[] = "Fecha Emision";
                $cabecera[] = "Tipo de Comprob";
                $cabecera[] = "Serie";
                $cabecera[] = "Numero";
                $cabecera[] = "Ticket Sin IGv";
                $cabecera[] = "Tipo de Documento";
                $cabecera[] = "Numero de Dcto Ident";
                $cabecera[] = "Apellidos,Nombres,RAzon Social";
                $cabecera[] = "Total valor venta operac gravadas";
                $cabecera[] = "Total valor venta operac exoneradas";
                $cabecera[] = "Total valor venta operac. inafectas";
                $cabecera[] = "ISC";
                $cabecera[] = "IGV";
                $cabecera[] = "OTROS TRIBUTOS";
                $cabecera[] = "IMPORTE TOTAL";
                $cabecera[] = "TIPO DCTO RELACIONADO";
                $cabecera[] = "SERIE DCTO RELACIONADO";
                $cabecera[] = "N° DCTO RELACIONADO ";
                $cabecera[] = "FORMULA";
                $array[] = $cabecera;
                $c=3;$d=3;$band=true;

                foreach ($resultado as $key => $value){
                    if($value->situacion!="U"){//NO ANULADAS
                        $detalle = array();
                        $detalle[] = "";
                        $detalle[] = "7";
                        $detalle[] = date('d/m/Y',strtotime($value->fecha));
                        $detalle[] = $value->tipodocumento_id==5?'03':'01';
                        $detalle[] = str_pad($value->serie,4,'0',STR_PAD_LEFT);
                        $detalle[] = str_pad($value->numero,4,'0',STR_PAD_LEFT);
                        $detalle[] = "";
                        $person = Person::find($value->persona_id);
                        if($value->tipodocumento_id==5){//boleta
                            if(strlen($person->dni)<>8 || $value->total<700){
                                $detalle[] = "0";
                                $detalle[] = "0";
                                $detalle[] = "CLIENTES VARIOS";
                            }else{
                                $detalle[] = "1";
                                $detalle[] = $person->dni;
                                $detalle[] = $person->apellidopaterno." ".$person->apellidomaterno." ".$person->nombres;
                            }
                        }else{
                            $detalle[] = "6";
                            $detalle[] = $person->ruc;
                            $detalle[] = $person->bussinesname;
                            
                        }
                        $detalle[] = number_format($value->subtotal,2,'.','');
                        $detalle[] = "0.00";
                        $detalle[] = "0.00";
                        $detalle[] = "0.00";
                        $detalle[] = number_format($value->igv,2,'.','');
                        $detalle[] = "0.00";
                        $detalle[] = number_format($value->total,2,'.','');
                        $detalle[] = "";
                        $detalle[] = '';
                        $detalle[] = "";
                        $detalle[] = '=CONCATENATE(B'.$c.',"|",C'.$c.',"|",D'.$c.',"|",E'.$c.',"|",F'.$c.',"|",G'.$c.',"|",H'.$c.',"|",I'.$c.',"|",J'.$c.',"|",ROUND(K'.$c.',2),"|",ROUND(L'.$c.',2),"|",ROUND(M'.$c.',2),"|",ROUND(N'.$c.',2),"|",ROUND(O'.$c.',2),"|",ROUND(P'.$c.',2),"|",ROUND(Q'.$c.',2),"|",R'.$c.',"|",S'.$c.',"|",T'.$c.',"|")';
                        $c=$c+1;
                        $array[] = $detalle;
                    }
                }

                $sheet->fromArray($array);
            });
        })->export('xls');
    }

    public function excelVentaConvenio(Request $request){
        setlocale(LC_TIME, 'spanish');
        $resultado        = Movimiento::leftjoin('person as paciente', 'paciente.id', '=', 'movimiento.persona_id')
                            ->join('person as responsable', 'responsable.id', '=', 'movimiento.responsable_id')
                            ->leftjoin('person as empresa','empresa.id','=','movimiento.empresa_id')
                            ->where('movimiento.tipodocumento_id','=',17);
        if($request->input('fechainicial')!=""){
            $resultado = $resultado->where('movimiento.fecha','>=',$request->input('fechainicial').' 00:00:00');
        }
        if($request->input('fechafinal')!=""){
            $resultado = $resultado->where('movimiento.fecha','<=',$request->input('fechafinal').' 23:59:59');
        }        

        $resultado        = $resultado->select('movimiento.*',DB::raw('CONCAT("F",movimiento.serie,"-",movimiento.numero) as numero2'),'empresa.bussinesname as empresa2','empresa.ruc as ruc2')->orderBy('movimiento.fecha', 'ASC')->orderBy('movimiento.serie', 'ASC')->orderBy('movimiento.numero', 'ASC')->get();

        Excel::create('ExcelVentaCovenio', function($excel) use($resultado,$request) {
 
            $excel->sheet('VentasConvenio', function($sheet) use($resultado,$request) {
 
                $array = array();
                $cabecera = array();
                $cabecera[] = "Fecha";
                $cabecera[] = "Paciente";
                $cabecera[] = "Tipo";
                $cabecera[] = "Numero";
                $cabecera[] = "Cliente";
                $cabecera[] = "RUC";
                $cabecera[] = "Situacion";
                $cabecera[] = "CTA";
                $cabecera[] = "CTA";
                $cabecera[] = "CTA";
                $cabecera[] = "Subtotal";
                $cabecera[] = "IGV";
                $cabecera[] = "Total";
                $cabecera[] = "Glosa";
                $cabecera[] = "SERVICIO";
                $cabecera[] = "USUARIO";
                $cabecera[] = "ESTADO";
                $sheet->row(1,$cabecera);
                $c=2;$d=3;$band=true;
                $subtotal=0;
                $igv=0;
                $total=0;

                foreach ($resultado as $key => $value){
                    $detalle = array();
                    $detalle[] = date('d/m/Y',strtotime($value->fecha));
                    $detalle[] = $value->persona->apellidopaterno.' '.$value->persona->apellidomaterno.' '.$value->persona->nombres;
                    $detalle[] = 'FT';
                    $detalle[] = 'F'.str_pad($value->serie,3,'0',STR_PAD_LEFT).'-'.$value->numero;
                    $detalle[] = $value->empresa2;
                    $detalle[] = $value->ruc2;
                    $detalle[] = "CREDITO";
                    $rs=Detallemovcaja::where('movimiento_id','=',$value->id)
                            ->where(function($q){
                                $q->where('descripcion','like','%FARMACIA%')
                                  ->orWhere('descripcion','like','MEDICINA%')
                                  ->orWhere('descripcion','like','%MEDICAMENTO%');

                            })->get();
                    $farmacia=0;
                    if(count($rs)>0){
                        foreach ($rs as $k => $v) {
                            $farmacia=$farmacia + round($v->cantidad*$v->precio/1.18,2);
                        }
                        if(round($value->total/1.18,2)>$farmacia){
                            $value->subtotal = $value->subtotal - $farmacia;
                            $servicio='SERVICIOS';
                            $cuenta='704105';
                        }else{
                            $farmacia=0;
                            $servicio='FARMACIA';
                            $cuenta='701112';
                        }
                    }else{
                        $servicio='SERVICIOS';
                        $cuenta='704105';
                    }
                    $detalle[] = $cuenta;
                    $detalle[] = "121206";
                    $detalle[] = "401111";
                    if($value->situacionsunat!='E'){
                        $detalle[] = number_format($value->subtotal,2,'.','');
                        $detalle[] = number_format($value->igv,2,'.','');
                        $detalle[] = number_format($value->total,2,'.','');
                        $subtotal=$subtotal+number_format($value->subtotal,2,'.','');
                        $igv=$igv+number_format($value->igv,2,'.','');
                        $total=$total+number_format($value->total,2,'.','');
                    }else{
                        $detalle[] = 0;
                        $detalle[] = 0;
                        $detalle[] = 0;
                    }
                    $detalle[] = "VENTAS CONVENIOS ".strtoupper(strftime("%B",strtotime($value->fecha)))." DEL ".date("Y",strtotime($value->fecha));
                    $detalle[] = $servicio;
                    $detalle[] = $value->responsable->nombres;
                    if($value->situacionsunat=='L')
                        $sunat = 'PENDIENTE RESPUESTA';
                    elseif($value->situacionsunat=='R')
                        $sunat = 'RECHAZADO';
                    elseif($value->situacionsunat=='E')
                        $sunat = 'ERROR';
                    elseif($value->situacionsunat=='P')
                        $sunat = 'ACEPTADO';
                    else
                        $sunat = 'PENDIENTE';
                    $detalle[] = $sunat;
                    $sheet->row($c,$detalle);
                    $c=$c+1;

                    if($farmacia>0){
                        $detalle = array();
                        $detalle[] = date('d/m/Y',strtotime($value->fecha));
                        $detalle[] = $value->persona->apellidopaterno.' '.$value->persona->apellidomaterno.' '.$value->persona->nombres;
                        $detalle[] = 'FT';
                        $detalle[] = 'F'.str_pad($value->serie,3,'0',STR_PAD_LEFT).'-'.$value->numero;
                        $detalle[] = $value->empresa2;
                        $detalle[] = $value->ruc2;
                        $detalle[] = "CREDITO";
                        $detalle[] = '701112';
                        $detalle[] = '';
                        $detalle[] = '';
                        if($value->situacionsunat!='E'){
                            $detalle[] = number_format($farmacia,2,'.','');
                            $subtotal=$subtotal+number_format($farmacia,2,'.','');
                            $detalle[] = '';
                            $detalle[] = '';
                        }else{
                            $detalle[] = 0;
                            $detalle[] = '';
                            $detalle[] = '';
                        }
                        $detalle[] = "VENTAS CONVENIOS ".strtoupper(strftime("%B",strtotime($value->fecha)))." DEL ".date("Y",strtotime($value->fecha));
                        $detalle[] = 'FARMACIA';
                        $sheet->row($c,$detalle);
                        $c=$c+1;
                    }
                }
                $cabecera = array();
                $cabecera[] = "";
                $cabecera[] = "";
                $cabecera[] = "";
                $cabecera[] = "";
                $cabecera[] = "";
                $cabecera[] = "";
                $cabecera[] = number_format($subtotal,2,'.','');
                $cabecera[] = number_format($igv,2,'.','');
                $cabecera[] = number_format($total,2,'.','');
                $sheet->row($c,$cabecera);
            });

            $excel->sheet('Cobranza', function($sheet) use($resultado,$request) {
 
                $array = array();
                $cabecera = array();
                $cabecera[] = "Fecha";
                $cabecera[] = "Tipo";
                $cabecera[] = "Numero";
                $cabecera[] = "Paciente";
                $cabecera[] = "Cliente";
                $cabecera[] = "RUC";
                $cabecera[] = "Subtotal";
                $cabecera[] = "IGV";
                $cabecera[] = "Total";
                $cabecera[] = "Situacion";
                $cabecera[] = "Retencion";
                $cabecera[] = "Detraccion";
                $cabecera[] = "Fecha Pago";
                $cabecera[] = "Mov.";
                $cabecera[] = "USUARIO";
                $cabecera[] = "ESTADO";
                $sheet->row(1,$cabecera);
                $c=2;$d=3;$band=true;
                $subtotal=0;$retencion=0;
                $igv=0;$detraccion=0;
                $total=0;

                foreach ($resultado as $key => $value){
                    $detalle = array();
                    $detalle[] = date('d/m/Y',strtotime($value->fecha));
                    $detalle[] = 'FT';
                    $detalle[] = 'F'.str_pad($value->serie,3,'0',STR_PAD_LEFT).'-'.$value->numero;
                    $detalle[] = $value->persona->apellidopaterno.' '.$value->persona->apellidomaterno.' '.$value->persona->nombres;
                    $detalle[] = $value->ruc2;
                    $detalle[] = $value->empresa2;
                    if($value->situacionsunat!='E'){
                        $detalle[] = number_format($value->subtotal,2,'.','');
                        $detalle[] = number_format($value->igv,2,'.','');
                        $detalle[] = number_format($value->total,2,'.','');
                        $subtotal=$subtotal+number_format($value->subtotal,2,'.','');
                        $igv=$igv+number_format($value->igv,2,'.','');
                        $total=$total+number_format($value->total,2,'.','');
                        if($value->situacion=='C'){
                            $detraccion=$detraccion+number_format($value->detraccion,2,'.','');
                            $retencion=$retencion+number_format($value->retencion,2,'.','');
                        }
                    }else{
                        $detalle[] = 0;
                        $detalle[] = 0;
                        $detalle[] = 0;
                    }
                    if($value->situacion=='C'){
                        $detalle[] = "CANCELADO";
                    }elseif($value->situacion=='A'){
                        $detalle[] = "NOTA CREDITO";
                    }else{
                        $detalle[] = "CREDITO";
                    }
                    if($value->situacion=='C'){
                        $detalle[] = number_format($value->retencion,2,'.','');
                        $detalle[] = number_format($value->detraccion,2,'.','');
                    }else{
                        $detalle[] = 0;
                        $detalle[] = 0;
                    }
                    $detalle[] = date("d/m/Y",strtotime($value->fechaentrega));
                    $detalle[] = $value->voucher;
                    $detalle[] = $value->responsable->nombres;
                    if($value->situacionsunat=='L')
                        $sunat = 'PENDIENTE RESPUESTA';
                    elseif($value->situacionsunat=='R')
                        $sunat = 'RECHAZADO';
                    elseif($value->situacionsunat=='E')
                        $sunat = 'ERROR';
                    elseif($value->situacionsunat=='P')
                        $sunat = 'ACEPTADO';
                    else
                        $sunat = 'PENDIENTE';
                    $detalle[] = $sunat;
                    $sheet->row($c,$detalle);
                    $c=$c+1;
                }
                $cabecera = array();
                $cabecera[] = "";
                $cabecera[] = "";
                $cabecera[] = "";
                $cabecera[] = "";
                $cabecera[] = "";
                $cabecera[] = "";
                $cabecera[] = number_format($subtotal,2,'.','');
                $cabecera[] = number_format($igv,2,'.','');
                $cabecera[] = number_format($total,2,'.','');
                $cabecera[] = "";
                $cabecera[] = number_format($retencion,2,'.','');
                $cabecera[] = number_format($detraccion,2,'.','');
                $sheet->row($c,$cabecera);
            });
        })->export('xls');
    }

    public function excelSunat(Request $request){
        setlocale(LC_TIME, 'spanish');
        $resultado        = Movimiento::leftjoin('person as paciente', 'paciente.id', '=', 'movimiento.persona_id')
                            ->join('person as responsable', 'responsable.id', '=', 'movimiento.responsable_id')
                            ->leftjoin('movimiento as m2','m2.id','=','movimiento.movimiento_id')
                            ->where('movimiento.tipomovimiento_id','=',4)
                            ->where('movimiento.ventafarmacia','=','N')
                            ->where('m2.tipomovimiento_id','=',1);
        if($request->input('fechainicial')!=""){
            $resultado = $resultado->where('movimiento.fecha','>=',$request->input('fechainicial').' 00:00:00');
        }
        if($request->input('fechafinal')!=""){
            $resultado = $resultado->where('movimiento.fecha','<=',$request->input('fechafinal').' 23:59:59');
        }        

        $resultado        = $resultado->select('movimiento.*','m2.situacion as situacion2','m2.tarjeta as tarjeta2',DB::raw('CONCAT(case when movimiento.tipodocumento_id=4 then "F" else "B" end,movimiento.serie,"-",movimiento.numero) as numero2'))->orderBy('movimiento.fecha', 'ASC')->orderBy('movimiento.serie', 'ASC')->orderBy('movimiento.numero', 'ASC')->get();

        Excel::create('ExcelVentaSunat', function($excel) use($resultado,$request) {
 
            $excel->sheet('Ventas', function($sheet) use($resultado,$request) {
 
                $array = array();
                $cabecera = array();
                $cabecera[] = "";
                $cabecera[] = "Codigo";
                $cabecera[] = "Fecha Emision";
                $cabecera[] = "Tipo de Comprob";
                $cabecera[] = "Serie";
                $cabecera[] = "Numero";
                $cabecera[] = "Ticket Sin IGv";
                $cabecera[] = "Tipo de Documento";
                $cabecera[] = "Numero de Dcto Ident";
                $cabecera[] = "Apellidos,Nombres,RAzon Social";
                $cabecera[] = "Total valor venta operac gravadas";
                $cabecera[] = "Total valor venta operac exoneradas";
                $cabecera[] = "Total valor venta operac. inafectas";
                $cabecera[] = "ISC";
                $cabecera[] = "IGV";
                $cabecera[] = "OTROS TRIBUTOS";
                $cabecera[] = "IMPORTE TOTAL";
                $cabecera[] = "TIPO DCTO RELACIONADO";
                $cabecera[] = "SERIE DCTO RELACIONADO";
                $cabecera[] = "N° DCTO RELACIONADO ";
                $cabecera[] = "FORMULA";
                $array[] = $cabecera;
                $c=3;$d=3;$band=true;

                //NOTA DE CREDITO
                $resultado2        = Movimiento::leftjoin('person as paciente', 'paciente.id', '=', 'movimiento.persona_id')
                            ->join('person as responsable', 'responsable.id', '=', 'movimiento.responsable_id')
                            ->leftjoin('movimiento as m2','m2.id','=','movimiento.movimiento_id')
                            ->where('movimiento.tipomovimiento_id','=',6);
                            //->where('movimiento.situacion','<>','U')
                            //->where('movimiento.situacion','<>','A');
                if($request->input('fechainicial')!=""){
                    $resultado2 = $resultado2->where('movimiento.fecha','>=',$request->input('fechainicial').' 00:00:00');
                }
                if($request->input('fechafinal')!=""){
                    $resultado2 = $resultado2->where('movimiento.fecha','<=',$request->input('fechafinal').' 23:59:59');
                }        

                $resultado2        = $resultado2->select('movimiento.*','m2.situacion as situacion2','m2.tarjeta as tarjeta2','m2.tipodocumento_id as tipodocumento_id2','m2.serie as serie2','m2.numero as numero2')->orderBy('movimiento.fecha', 'ASC')->orderBy('movimiento.serie', 'ASC')->orderBy('movimiento.numero', 'ASC')->get();


                foreach ($resultado2 as $key1 => $value1){
                    //if($value1->situacion!="U"){//NO ANULADAS
                        $detalle = array();
                        $detalle[] = "";
                        $detalle[] = "7";
                        $detalle[] = date('d/m/Y',strtotime($value1->fecha));
                        $detalle[] = '07';
                        $detalle[] = str_pad($value1->serie,4,'0',STR_PAD_LEFT);
                        $detalle[] = str_pad($value1->numero,4,'0',STR_PAD_LEFT);
                        $detalle[] = "";
                        $person = Person::find($value1->persona_id);
                        if($value1->tipodocumento_id2==5){
                            if ($person != null) {
                                if(strlen($person->dni)<>8 || $value1->total<700){
                                    $detalle[] = "0";
                                    $detalle[] = "0";
                                    $detalle[] = "CLIENTES VARIOS";
                                }else{
                                    $detalle[] = "1";
                                    $detalle[] = $person->dni;
                                    $detalle[] = $person->apellidopaterno." ".$person->apellidomaterno." ".$person->nombres;
                                }
                            }else{
                                $detalle[] = "0";
                                $detalle[] = "0";
                                $detalle[] = "CLIENTES VARIOS";
                            }
                        }else{
                            $detalle[] = "6";
                            $detalle[] = $person->ruc;
                            $detalle[] = $person->bussinesname;
                            
                        }
                        $detalle[] = number_format($value1->subtotal,2,'.','');
                        $detalle[] = "0.00";
                        $detalle[] = "0.00";
                        $detalle[] = "0.00";
                        $detalle[] = number_format($value1->igv,2,'.','');
                        $detalle[] = "0.00";
                        $detalle[] = number_format($value1->total,2,'.','');
                        if($value1->tipodocumento_id2==5){
                            $detalle[] = "01";
                        }else{
                            $detalle[] = "03";
                        }
                        $detalle[] = str_pad($value1->serie2,4,'0',STR_PAD_LEFT);
                        $detalle[] = str_pad($value1->numero2,4,'0',STR_PAD_LEFT);
                        $detalle[] = '=CONCATENATE(B'.$c.',"|",C'.$c.',"|",D'.$c.',"|",E'.$c.',"|",F'.$c.',"|",G'.$c.',"|",H'.$c.',"|",I'.$c.',"|",J'.$c.',"|",ROUND(K'.$c.',2),"|",ROUND(L'.$c.',2),"|",ROUND(M'.$c.',2),"|",ROUND(N'.$c.',2),"|",ROUND(O'.$c.',2),"|",ROUND(P'.$c.',2),"|",ROUND(Q'.$c.',2),"|",R'.$c.',"|",S'.$c.',"|",T'.$c.',"|")';
                        $c=$c+1;
                        $array[] = $detalle;
                    //}
                }

                foreach ($resultado as $key => $value){
                    if (($value->serie==7 || $value->serie==9) && $band) {
                        $resultado2        = Movimiento::leftjoin('person as paciente', 'paciente.id', '=', 'movimiento.persona_id')
                            ->join('person as responsable', 'responsable.id', '=', 'movimiento.responsable_id')
                            ->leftjoin('movimiento as m2','m2.id','=','movimiento.movimiento_id')
                            ->where('movimiento.tipomovimiento_id','=',4)
                            ->where('movimiento.tipodocumento_id','<>',15)
                            ->where('movimiento.ventafarmacia','=','S');
                            //->where('m2.tipomovimiento_id','=',2);
                        if($request->input('fechainicial')!=""){
                            $resultado2 = $resultado2->where('movimiento.fecha','>=',$request->input('fechainicial').' 00:00:00');
                        }
                        if($request->input('fechafinal')!=""){
                            $resultado2 = $resultado2->where('movimiento.fecha','<=',$request->input('fechafinal').' 23:59:59');
                        }        

                        $resultado2        = $resultado2->select('movimiento.*','m2.situacion as situacion2','m2.tarjeta as tarjeta2',DB::raw('CONCAT(case when movimiento.tipodocumento_id=4 then "F" else "B" end,movimiento.serie,"-",movimiento.numero) as numero2'))->orderBy('movimiento.fecha', 'ASC')->orderBy('movimiento.serie', 'ASC')->orderBy('movimiento.numero', 'ASC')->get();


                        foreach ($resultado2 as $key1 => $value1){
                            //if($value1->situacion!="U"){//NO ANULADAS
                                $detalle = array();
                                $detalle[] = "";
                                $detalle[] = "7";
                                $detalle[] = date('d/m/Y',strtotime($value1->fecha));
                                $detalle[] = $value1->tipodocumento_id==5?'03':'01';
                                $detalle[] = str_pad($value1->serie,4,'0',STR_PAD_LEFT);
                                $detalle[] = str_pad($value1->numero,4,'0',STR_PAD_LEFT);
                                $detalle[] = "";
                                $person = Person::find($value1->persona_id);
                                if($value1->tipodocumento_id==5){//boleta
                                    if ($person != null) {
                                        if(strlen($person->dni)<>8 || $value1->total<700){
                                            $detalle[] = "0";
                                            $detalle[] = "0";
                                            $detalle[] = "CLIENTES VARIOS";
                                        }else{
                                            $detalle[] = "1";
                                            $detalle[] = $person->dni;
                                            $detalle[] = $person->apellidopaterno." ".$person->apellidomaterno." ".$person->nombres;
                                        }
                                    }else{
                                        $detalle[] = "0";
                                        $detalle[] = "0";
                                        $detalle[] = "CLIENTES VARIOS";
                                    }
                                    
                                }else{
                                    $detalle[] = "6";
                                    $detalle[] = $value1->empresa->ruc;
                                    $detalle[] = $value1->empresa->bussinesname;
                                    
                                }
                                $detalle[] = number_format($value1->subtotal,2,'.','');
                                $detalle[] = "0.00";
                                $detalle[] = "0.00";
                                $detalle[] = "0.00";
                                $detalle[] = number_format($value1->igv,2,'.','');
                                $detalle[] = "0.00";
                                $detalle[] = number_format($value1->total,2,'.','');
                                $detalle[] = "";
                                $detalle[] = '';
                                $detalle[] = "";
                                $detalle[] = '=CONCATENATE(B'.$c.',"|",C'.$c.',"|",D'.$c.',"|",E'.$c.',"|",F'.$c.',"|",G'.$c.',"|",H'.$c.',"|",I'.$c.',"|",J'.$c.',"|",ROUND(K'.$c.',2),"|",ROUND(L'.$c.',2),"|",ROUND(M'.$c.',2),"|",ROUND(N'.$c.',2),"|",ROUND(O'.$c.',2),"|",ROUND(P'.$c.',2),"|",ROUND(Q'.$c.',2),"|",R'.$c.',"|",S'.$c.',"|",T'.$c.',"|")';
                                $c=$c+1;
                                $array[] = $detalle;
                            //}
                        }
                        $band=false;
                    }
                    //if($value->situacion!="U"){//NO ANULADAS
                        $detalle = array();
                        $detalle[] = "";
                        $detalle[] = "7";
                        $detalle[] = date('d/m/Y',strtotime($value->fecha));
                        $detalle[] = $value->tipodocumento_id==5?'03':'01';
                        $detalle[] = str_pad($value->serie,4,'0',STR_PAD_LEFT);
                        $detalle[] = str_pad($value->numero,4,'0',STR_PAD_LEFT);
                        $detalle[] = "";
                        $person = Person::find($value->persona_id);
                        if($value->tipodocumento_id==5){//boleta
                            if(strlen($person->dni)<>8 || $value->total<700){
                                $detalle[] = "0";
                                $detalle[] = "0";
                                $detalle[] = "CLIENTES VARIOS";
                            }else{
                                $detalle[] = "1";
                                $detalle[] = $person->dni;
                                $detalle[] = $person->apellidopaterno." ".$person->apellidomaterno." ".$person->nombres;
                            }
                        }else{
                            $detalle[] = "6";
                            $detalle[] = $person->ruc;
                            $detalle[] = $person->bussinesname;
                            
                        }
                        $detalle[] = number_format($value->subtotal,2,'.','');
                        $detalle[] = "0.00";
                        $detalle[] = "0.00";
                        $detalle[] = "0.00";
                        $detalle[] = number_format($value->igv,2,'.','');
                        $detalle[] = "0.00";
                        $detalle[] = number_format($value->total,2,'.','');
                        $detalle[] = "";
                        $detalle[] = '';
                        $detalle[] = "";
                        $detalle[] = '=CONCATENATE(B'.$c.',"|",C'.$c.',"|",D'.$c.',"|",E'.$c.',"|",F'.$c.',"|",G'.$c.',"|",H'.$c.',"|",I'.$c.',"|",J'.$c.',"|",ROUND(K'.$c.',2),"|",ROUND(L'.$c.',2),"|",ROUND(M'.$c.',2),"|",ROUND(N'.$c.',2),"|",ROUND(O'.$c.',2),"|",ROUND(P'.$c.',2),"|",ROUND(Q'.$c.',2),"|",R'.$c.',"|",S'.$c.',"|",T'.$c.',"|")';
                        $c=$c+1;
                        $array[] = $detalle;
                    //}
                }

                $sheet->fromArray($array);
            });
        })->export('xls');
    }

    public function excelVenta(Request $request){
        setlocale(LC_TIME, 'spanish');
        $resultado        = Movimiento::leftjoin('person as paciente', 'paciente.id', '=', 'movimiento.persona_id')
                            ->join('person as responsable', 'responsable.id', '=', 'movimiento.responsable_id')
                            ->leftjoin('movimiento as m2','m2.id','=','movimiento.movimiento_id')
                            ->where('movimiento.tipomovimiento_id','=',4)
                            ->where('movimiento.ventafarmacia','=','N')
                            ->where('m2.tipomovimiento_id','=',1);
        if($request->input('fechainicial')!=""){
            $resultado = $resultado->where('movimiento.fecha','>=',$request->input('fechainicial').' 00:00:00');
        }
        if($request->input('fechafinal')!=""){
            $resultado = $resultado->where('movimiento.fecha','<=',$request->input('fechafinal').' 23:59:59');
        }        

        $resultado        = $resultado->select('movimiento.*','m2.situacion as situacion2','m2.tarjeta as tarjeta2',DB::raw('CONCAT(case when movimiento.tipodocumento_id=4 then "F" else "B" end,movimiento.serie,"-",movimiento.numero) as numero2'),'responsable.nombres as responsable2')->orderBy('movimiento.serie', 'ASC')->orderBy('movimiento.numero', 'ASC')->orderBy('movimiento.fecha', 'ASC')->get();

        Excel::create('ExcelVentaFarmacia', function($excel) use($resultado,$request) {
            
            $excel->sheet('VentasFarmacia', function($sheet) use($resultado,$request) {
 
                $array = array();
                $cabecera = array();
                $cabecera[] = "Fecha";
                $cabecera[] = "Paciente";
                $cabecera[] = "Doc.";
                $cabecera[] = "Nro.";
                $cabecera[] = "Cliente";
                $cabecera[] = "RUC";
                $cabecera[] = "Condicion";
                $cabecera[] = "CTA";
                $cabecera[] = "CTA";
                $cabecera[] = "CTA";
                $cabecera[] = "Subtotal";
                $cabecera[] = "Igv";
                $cabecera[] = "Total";
                $cabecera[] = "Glosa 1";
                $cabecera[] = "Glosa 2";
                $cabecera[] = "Sunat";
                $sheet->row(1,$cabecera);
                $c=2;$d=3;$subtotal=0;$igv=0;$total=0;$band=true;
                foreach ($resultado as $key => $value){
                    //FARMACIA
                    if (($value->serie==7 || $value->serie==9) && $band) {
                        $band=false;
                        $resultado2        = Movimiento::leftjoin('person as paciente', 'paciente.id', '=', 'movimiento.persona_id')
                            ->join('person as responsable', 'responsable.id', '=', 'movimiento.responsable_id')
                            ->leftjoin('movimiento as m2','m2.id','=','movimiento.movimiento_id')
                            ->where('movimiento.tipomovimiento_id','=',4)
                            ->where('movimiento.ventafarmacia','=','S')
                            ->where('movimiento.tipodocumento_id','<>',15);
                        if($request->input('fechainicial')!=""){
                            $resultado2 = $resultado2->where('movimiento.fecha','>=',$request->input('fechainicial').' 00:00:00');
                        }
                        if($request->input('fechafinal')!=""){
                            $resultado2 = $resultado2->where('movimiento.fecha','<=',$request->input('fechafinal').' 23:59:59');
                        }        

                        $resultado2        = $resultado2->select('movimiento.*','m2.situacion as situacion2','m2.tarjeta as tarjeta2',DB::raw('CONCAT(case when movimiento.tipodocumento_id=4 then "F" else "B" end,movimiento.serie,"-",movimiento.numero) as numero2'))->orderBy('movimiento.serie', 'ASC')->orderBy('movimiento.numero', 'ASC')->get();

                        foreach ($resultado2 as $key1 => $value1){
                            $glosa='VARIOS';

                            $detalle = array();
                            $detalle[] = date('d/m/Y',strtotime($value1->fecha));
                            $person = Person::find($value1->persona_id);
                            if ($person !== null) {
                                $detalle[] = $person->apellidopaterno." ".$person->apellidomaterno." ".$person->nombres;
                            }else{
                                $detalle[] = $value1->nombrepaciente;
                            }
                            $detalle[] = $value1->tipodocumento_id==5?'BV':'FT';
                            if($value1->manual=='S')
                                $detalle[] = $value1->serie.'-'.$value1->numero;
                            else
                                $detalle[] = ($value1->tipodocumento_id==5?'B':'F').str_pad($value1->serie,3,'0',STR_PAD_LEFT).'-'.$value1->numero;
                            if($value1->tipodocumento_id==4){//Factura
                                if(!is_null($value1->empresa_id) && $value1->empresa_id>0){
                                    $detalle[] = $value1->empresa->bussinesname;
                                    $detalle[] = $value1->empresa->ruc;
                                }else{
                                    $detalle[] = "";
                                    $detalle[] = "";
                                }
                            }else{
                                $detalle[] = "";
                                $detalle[] = "0000";
                            }
                            if($value1->tarjeta2!="" && $value1->estadopago!="PP"){
                                $detalle[] = "TARJETA";
                                $detalle[] = "121205";
                                $detalle[] = "701112";
                                $detalle[] = "401111";
                            }elseif($value1->estadopago=="PP"){//PENDIENTE
                                $detalle[] = "CREDITO";
                                $detalle[] = "121204";
                                $detalle[] = "701112";
                                $detalle[] = "401111";
                            }else{
                                $detalle[] = "CONTADO";
                                $detalle[] = "121203";
                                $detalle[] = "701112";
                                $detalle[] = "401111";
                            }
                            if($value1->situacion=="U"){
                                $detalle[] = number_format(0,2,'.','');
                                $detalle[] = number_format(0,2,'.','');
                                $detalle[] = number_format(0,2,'.','');
                            }else{
                                $detalle[] = number_format($value1->subtotal,2,'.','');
                                $detalle[] = number_format($value1->igv,2,'.','');
                                $detalle[] = number_format($value1->total,2,'.','');
                                $subtotal = $subtotal + number_format($value1->subtotal,2,'.','');
                                $igv = $igv + number_format($value1->igv,2,'.','');
                                $total = $total + number_format($value1->total,2,'.','');
                            }
                            $detalle[] = "VENTAS ".strtoupper(strftime("%B",strtotime($value1->fecha)))." DEL ".date("Y",strtotime($value1->fecha));
                            $detalle[] = $glosa;
                            if($value1->situacionsunat=='L')
                                $sunat = 'PENDIENTE RESPUESTA';
                            elseif($value1->situacionsunat=='R')
                                $sunat = 'RECHAZADO';
                            elseif($value1->situacionsunat=='E')
                                $sunat = 'ERROR';
                            elseif($value1->situacionsunat=='P')
                                $sunat = 'ACEPTADO';
                            else
                                $sunat = 'PENDIENTE';
                            $detalle[] = $sunat;
                            $sheet->row($c,$detalle);
                            $c=$c+1;
                        }
                    }

                    $detalle = Detallemovcaja::where('movimiento_id','=',$value->movimiento_id)->first();
                    if(!is_null($detalle->servicio) && $detalle->servicio_id>0){
                        $glosa=$detalle->servicio->tiposervicio->nombre;
                    }else{
                        $glosa='VARIOS';
                    }
                    $detalle = array();
                    $detalle[] = date('d/m/Y',strtotime($value->fecha));
                    $ticket = Movimiento::find($value->movimiento_id);
                    $person = Person::find($ticket->persona_id);
                    $detalle[] = $person->apellidopaterno." ".$person->apellidomaterno." ".$person->nombres;
                    $detalle[] = $value->tipodocumento_id==5?'BV':'FT';
                    if($value->manual=='S')
                        $detalle[] = $value->serie.'-'.$value->numero;
                    else
                        $detalle[] = ($value->tipodocumento_id==5?'B':'F').str_pad($value->serie,3,'0',STR_PAD_LEFT).'-'.$value->numero;
                    if($value->tipodocumento_id==4){//Factura
                        $detalle[] = $value->persona->bussinesname;
                        $detalle[] = $value->persona->ruc;
                    }else{
                        $detalle[] = "";
                        $detalle[] = "0000";
                    }
                    if($ticket->tarjeta!=""){
                        $detalle[] = "TARJETA";
                        $detalle[] = "121205";
                        $detalle[] = "704105";
                        $detalle[] = "401111";
                    }elseif($ticket->situacion=="B"){//PENDIENTE
                        $detalle[] = "CREDITO";
                        $detalle[] = "121204";
                        $detalle[] = "704105";
                        $detalle[] = "401111";
                    }else{
                        $detalle[] = "CONTADO";
                        $detalle[] = "121203";
                        $detalle[] = "704105";
                        $detalle[] = "401111";
                    }
                    if($value->situacion=="U"){
                        $detalle[] = number_format(0,2,'.','');
                        $detalle[] = number_format(0,2,'.','');
                        $detalle[] = number_format(0,2,'.','');
                    }else{
                        $detalle[] = number_format($value->subtotal,2,'.','');
                        $detalle[] = number_format($value->igv,2,'.','');
                        $detalle[] = number_format($value->total,2,'.','');
                        $subtotal = $subtotal + number_format($value->subtotal,2,'.','');
                        $igv = $igv + number_format($value->igv,2,'.','');
                        $total = $total + number_format($value->total,2,'.','');
                    }
                    $detalle[] = "VENTAS ".strtoupper(strftime("%B",strtotime($value->fecha)))." DEL ".date("Y",strtotime($value->fecha));
                    $detalle[] = $glosa;
                    if($value->situacionsunat=='L')
                        $sunat = 'PENDIENTE RESPUESTA';
                    elseif($value->situacionsunat=='E')
                        $sunat = 'ERROR';
                    elseif($value->situacionsunat=='R')
                        $sunat = 'RECHAZADO';
                    elseif($value->situacionsunat=='P')
                        $sunat = 'ACEPTADO';
                    else
                        $sunat = 'PENDIENTE';
                    $detalle[] = $sunat;
                    $sheet->row($c,$detalle);
                    $c=$c+1;
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
                $detalle[] = number_format($subtotal,2,'.','');
                $detalle[] = number_format($igv,2,'.','');
                $detalle[] = number_format($total,2,'.','');
                $sheet->row($c,$detalle);
 
            });

            $resultado        = Movimiento::leftjoin('person as paciente', 'paciente.id', '=', 'movimiento.persona_id')
                            ->join('person as responsable', 'responsable.id', '=', 'movimiento.responsable_id')
                            ->leftjoin('movimiento as m2','m2.id','=','movimiento.movimiento_id')
                            ->join('movimiento as m3','m3.movimiento_id','=','movimiento.id')
                            ->where('movimiento.tipomovimiento_id','=',4)
                            ->where('movimiento.ventafarmacia','=','N')
                            ->where('m2.tipomovimiento_id','=',1)
                            ->where('m3.tipomovimiento_id','=',2);
            if($request->input('fechainicial')!=""){
                $resultado = $resultado->where('m3.fecha','>=',$request->input('fechainicial').' 00:00:00');
            }
            if($request->input('fechafinal')!=""){
                $resultado = $resultado->where('m3.fecha','<=',$request->input('fechafinal').' 23:59:59');
            }        

            $resultado        = $resultado->select('movimiento.*','m2.situacion as situacion2','m2.tarjeta as tarjeta2',DB::raw('CONCAT(case when movimiento.tipodocumento_id=4 then "F" else "B" end,movimiento.serie,"-",movimiento.numero) as numero2'),'m3.fecha as fechacaja','m3.tipotarjeta as tipotarjeta3','responsable.nombres as responsable2')->orderBy('movimiento.serie', 'ASC')->orderBy('movimiento.numero', 'ASC')->orderBy('movimiento.fecha', 'ASC')->get();

            $excel->sheet('Cobranza', function($sheet) use($resultado,$request) {
 
                $array = array();
                $cabecera = array();
                $cabecera[] = "Fecha";
                $cabecera[] = "Paciente";
                $cabecera[] = "Doc.";
                $cabecera[] = "Nro.";
                $cabecera[] = "Condicion";
                $cabecera[] = "CTA";
                $cabecera[] = "CTA";
                $cabecera[] = "Cliente";
                $cabecera[] = "RUC";
                $cabecera[] = "Subtotal";
                $cabecera[] = "Igv";
                $cabecera[] = "Total";
                $cabecera[] = "Glosa 2";
                $cabecera[] = "Sunat";
                $sheet->row(1,$cabecera);

                $c=2;$d=3;$subtotal=0;$igv=0;$total=0;$band=true;
                foreach ($resultado as $key => $value){
                    //FARMACIA
                    if (($value->serie==7 || $value->serie==9) && $band) {
                        $band=false;
                        $resultado2        = Movimiento::leftjoin('person as paciente', 'paciente.id', '=', 'movimiento.persona_id')
                            ->join('person as responsable', 'responsable.id', '=', 'movimiento.responsable_id')
                            ->leftjoin('movimiento as m2','m2.id','=','movimiento.movimiento_id')
                            ->leftjoin('movimiento as m3', function($join)
                            {
                                $join->on('m3.movimiento_id','=','movimiento.id')
                                     ->where('m3.tipomovimiento_id', '=', 2);
                            })
                            ->leftjoin('movimiento as m4', function($join)
                            {
                                $join->on('m4.id','=','movimiento.movimientodescarga_id')
                                     ->where('m4.tipomovimiento_id', '=', 2);
                            })
                            ->where('movimiento.tipomovimiento_id','=',4)
                            ->where('movimiento.ventafarmacia','=','S')
                            ->whereIn('movimiento.formapago',['T','C'])
                            ->where('movimiento.tipodocumento_id','<>',15);
       
                        if($request->input('fechainicial')!=""){
                            $resultado2 = $resultado2->where(function($query) use ($request){
                                        $query->where('m3.fecha','>=',$request->input('fechainicial'))
                                              ->orWhere('m4.fecha','>=',$request->input('fechainicial'));
                                        });
                        }
                        if($request->input('fechafinal')!=""){
                            $resultado2 = $resultado2->where(function($query) use ($request){
                                        $query->where('m3.fecha','<=',$request->input('fechafinal'))
                                              ->orWhere('m4.fecha','<=',$request->input('fechafinal'));
                                        });
                        }        

                        $resultado2        = $resultado2->select('movimiento.*','m3.situacion as situacion2','m2.tarjeta as tarjeta2','m3.fecha as fechacaja3','m4.fecha as fechacaja4',DB::raw('CONCAT(case when movimiento.tipodocumento_id=4 then "F" else "B" end,movimiento.serie,"-",movimiento.numero) as numero2'),'m3.tarjeta as tarjeta3')->orderBy('movimiento.serie', 'ASC')->orderBy('movimiento.numero', 'ASC')->get();

                        foreach ($resultado2 as $key1 => $value1){
                            if($value1->situacion<>'U'){
                                $glosa='VARIOS';
                                //die();
                                $detalle = array();
                                if($value1->estadopago=='PP'){
                                    //print_r($value1->movimientodescarga_id."-");
                                    //$caja = Movimiento::where('id','=',$value1->movimientodescarga_id)->where('tipomovimiento_id','=','2')->first();
                                    
                                    if (!isset($value1->fechacaja4)) {
                                        $detalle[] = date('d/m/Y',strtotime($value1->fechacaja3));
                                    } else {
                                        $detalle[] = date('d/m/Y',strtotime($value1->fechacaja4));
                                    }
                                }else{
                                    //$caja = Movimiento::where('movimiento_id','=',$value1->id)->where('tipomovimiento_id','=','2')->first();
                                    $detalle[] = date('d/m/Y',strtotime($value1->fechacaja3));
                                }
                                $person = Person::find($value1->persona_id);
                                if ($person !== null) {
                                    $detalle[] = $person->apellidopaterno." ".$person->apellidomaterno." ".$person->nombres;
                                }else{
                                    $detalle[] = $value1->nombrepaciente;
                                }
                                $detalle[] = $value1->tipodocumento_id==5?'BV':'FT';
                                if($value1->manual=='S')
                                    $detalle[] = $value1->serie.'-'.$value1->numero;
                                else
                                    $detalle[] = ($value1->tipodocumento_id==5?'B':'F').str_pad($value1->serie,3,'0',STR_PAD_LEFT).'-'.$value1->numero;
                                if($value1->tarjeta2!="" && $value1->formapago=='T'){
                                    $detalle[] = "TARJETA";
                                    $detalle[] = "121205";
                                    $detalle[] = "103112";
                                }elseif($value1->estadopago=="PP" && $value1->formapago=='C'){//PENDIENTE
                                    if($value1->tarjeta3!=""){
                                        $detalle[] = "COBRANZA";
                                        $detalle[] = "121204";
                                        $detalle[] = "103112";
                                    }else{
                                        $detalle[] = "COBRANZA";
                                        $detalle[] = "121204";
                                        $detalle[] = "101101";
                                    }
                                }else{
                                    $detalle[] = "CONTADO";
                                    $detalle[] = "121203";
                                    $detalle[] = "101101";
                                }
                                if($value1->tipodocumento_id==4){//Factura
                                    if($value1->empresa_id>0){
                                        $detalle[] = $value1->empresa->bussinesname;
                                        $detalle[] = $value1->empresa->ruc;
                                    }else{
                                        $detalle[] = "";
                                        $detalle[] = "";
                                    }
                                }else{
                                    $detalle[] = "";
                                    $detalle[] = "0000";
                                }
                                $detalle[] = number_format($value1->subtotal,2,'.','');
                                $detalle[] = number_format($value1->igv,2,'.','');
                                $detalle[] = number_format($value1->total,2,'.','');
                                $subtotal = $subtotal + number_format($value1->subtotal,2,'.','');
                                $igv = $igv + number_format($value1->igv,2,'.','');
                                $total = $total + number_format($value1->total,2,'.','');
                                $detalle[] = $glosa;
                                if($value1->situacionsunat=='L')
                                    $sunat = 'PENDIENTE RESPUESTA';
                                elseif($value1->situacionsunat=='E')
                                    $sunat = 'ERROR';
                                elseif($value1->situacionsunat=='R')
                                    $sunat = 'RECHAZADO';
                                elseif($value1->situacionsunat=='P')
                                    $sunat = 'ACEPTADO';
                                else
                                    $sunat = 'PENDIENTE';
                                $detalle[] = $sunat;
                                $sheet->row($c,$detalle);
                                $c=$c+1;
                            }
                        }
                    }
                    if($value->situacion=='N' || $value->situacion=='A'){//SOLO PAGADOS
                        $detalle = Detallemovcaja::where('movimiento_id','=',$value->movimiento_id)->first();
                        if(!is_null($detalle->servicio) && $detalle->servicio_id>0){
                            $glosa=$detalle->servicio->tiposervicio->nombre;
                        }else{
                            $glosa='VARIOS';
                        }
                        $detalle = array();
                        $detalle[] = date('d/m/Y',strtotime($value->fechacaja));
                        $ticket = Movimiento::find($value->movimiento_id);
                        $person = Person::find($ticket->persona_id);
                        //$caja = Movimiento::where('movimiento_id','=',$value->id)->first();
                        $detalle[] = $person->apellidopaterno." ".$person->apellidomaterno." ".$person->nombres;
                        $detalle[] = $value->tipodocumento_id==5?'BV':'FT';
                        if($value->manual=='S')
                            $detalle[] = $value->serie.'-'.$value->numero;
                        else
                            $detalle[] = ($value->tipodocumento_id==5?'B':'F').str_pad($value->serie,3,'0',STR_PAD_LEFT).'-'.$value->numero;
                        if($ticket->tarjeta!=""){
                            $detalle[] = "TARJETA";
                            $detalle[] = "121205";
                            $detalle[] = "103112";
                        }elseif($ticket->situacion=="B" && $value->tipotarjeta3==""){//PENDIENTE Y CONTADO
                            $detalle[] = "COBRANZA";
                            $detalle[] = "121204";
                            if($value->serie==8){
                                $detalle[] = "101101";    
                            }else{
                                $detalle[] = "101101";
                            }
                        }elseif($ticket->situacion=="B" && $value->tipotarjeta3!=""){//PENDIENTE Y TARJETA
                            $detalle[] = "COBRANZA";
                            $detalle[] = "121204";
                            $detalle[] = "103112";
                        }else{
                            $detalle[] = "CONTADO";
                            $detalle[] = "121203";
                            if($value->serie==8){
                                $detalle[] = "101101";    
                            }else{
                                $detalle[] = "101101";
                            }
                        }
                        if($value->tipodocumento_id==4){//Factura
                            $detalle[] = $value->persona->bussinesname;
                            $detalle[] = $value->persona->ruc;
                        }else{
                            $detalle[] = "";
                            $detalle[] = "0000";
                        }
                        if($value->situacion=="U"){
                            $detalle[] = number_format(0,2,'.','');
                            $detalle[] = number_format(0,2,'.','');
                            $detalle[] = number_format(0,2,'.','');
                        }else{
                            $detalle[] = number_format($value->subtotal,2,'.','');
                            $detalle[] = number_format($value->igv,2,'.','');
                            $detalle[] = number_format($value->total,2,'.','');
                            $subtotal = $subtotal + number_format($value->subtotal,2,'.','');
                            $igv = $igv + number_format($value->igv,2,'.','');
                            $total = $total + number_format($value->total,2,'.','');
                        }
                        $detalle[] = $glosa;
                        if($value->situacionsunat=='L')
                            $sunat = 'PENDIENTE RESPUESTA';
                        elseif($value->situacionsunat=='E')
                            $sunat = 'ERROR';
                        elseif($value->situacionsunat=='R')
                            $sunat = 'RECHAZADO';
                        elseif($value->situacionsunat=='P')
                            $sunat = 'ACEPTADO';
                        else
                            $sunat = 'PENDIENTE';
                        $detalle[] = $sunat;
                        $sheet->row($c,$detalle);
                        $c=$c+1;
                    }
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
                $detalle[] = number_format($subtotal,2,'.','');
                $detalle[] = number_format($igv,2,'.','');
                $detalle[] = number_format($total,2,'.','');
                $sheet->row($c,$detalle);

            });

            $excel->sheet('Tarjeta', function($sheet) use($resultado,$request) {
 
                $array = array();
                $cabecera = array();
                $cabecera[] = "Fecha";
                $cabecera[] = "Paciente";
                $cabecera[] = "Doc.";
                $cabecera[] = "Nro.";
                $cabecera[] = "Importe";
                $cabecera[] = "Operacion";
                $cabecera[] = "Nro. Operacion";
                $cabecera[] = "Tipo Tarjeta";
                $cabecera[] = "Usuario";
                $cabecera[] = "Sunat";
                $sheet->row(1,$cabecera);
                $c=2;$d=3;$total=0;$band=true;
                foreach ($resultado as $key => $value){
                    //FARMACIA
                    if (($value->serie==7 || $value->serie==9) && $band) {
                        $band=false;
                        $resultado2        = Movimiento::leftjoin('person as paciente', 'paciente.id', '=', 'movimiento.persona_id')
                            ->join('person as responsable', 'responsable.id', '=', 'movimiento.responsable_id')
                            ->leftjoin('movimiento as m2','m2.id','=','movimiento.movimiento_id')
                            ->where('movimiento.tipomovimiento_id','=',4)
                            ->where('movimiento.ventafarmacia','=','S')
                            ->where('movimiento.estadopago','<>','PP')
                            ->where('movimiento.tipodocumento_id','<>',15)
                            ->where('m2.tipomovimiento_id','=',2);
                        if($request->input('fechainicial')!=""){
                            $resultado2 = $resultado2->where('movimiento.fecha','>=',$request->input('fechainicial').' 00:00:00');
                        }
                        if($request->input('fechafinal')!=""){
                            $resultado2 = $resultado2->where('movimiento.fecha','<=',$request->input('fechafinal').' 23:59:59');
                        }        

                        $resultado2        = $resultado2->select('movimiento.*','m2.situacion as situacion2','m2.tipotarjeta as tarjeta2','m2.voucher as voucher2',DB::raw('CONCAT(case when movimiento.tipodocumento_id=4 then "F" else "B" end,movimiento.serie,"-",movimiento.numero) as numero2'),'responsable.nombres as responsable2')->orderBy('movimiento.serie', 'ASC')->orderBy('movimiento.numero', 'ASC')->get();

                        foreach ($resultado2 as $key1 => $value1){
                            if($value1->situacion<>'U' && $value1->tarjeta2!=""){
                                $detalle = Detallemovcaja::where('movimiento_id','=',$value1->movimiento_id)->first();
                                if(!is_null($detalle) &&  !is_null($detalle->servicio_id) && $detalle->servicio_id>0){
                                    $glosa=$detalle->servicio->tiposervicio->nombre;
                                }else{
                                    $glosa='VARIOS';
                                }

                                $detalle = array();
                                $detalle[] = date('d/m/Y',strtotime($value1->fecha));
                                $person = Person::find($value1->persona_id);
                                if ($person !== null) {
                                    $detalle[] = $person->apellidopaterno." ".$person->apellidomaterno." ".$person->nombres;
                                }else{
                                    $detalle[] = $value1->nombrepaciente;
                                }
                                $detalle[] = $value1->tipodocumento_id==5?'BV':'FT';
                                if($value1->manual=='S')
                                    $detalle[] = $value1->serie.'-'.$value1->numero;
                                else
                                    $detalle[] = ($value1->tipodocumento_id==5?'B':'F').str_pad($value1->serie,3,'0',STR_PAD_LEFT).'-'.$value1->numero;
                                
                                if($value1->situacion=="U"){
                                    $detalle[] = number_format(0,2,'.','');
                                    $detalle[] = "ANULADA";
                                }else{
                                    $detalle[] = number_format($value1->total,2,'.','');
                                    $total = $total + number_format($value1->total,2,'.','');
                                    $detalle[] = "MEDICAMENTO";
                                }
                                $detalle[] = $value1->voucher2;
                                $detalle[] = $value1->tarjeta2;
                                $detalle[] = $value1->responsable2;
                                if($value1->situacionsunat=='L')
                                    $sunat = 'PENDIENTE RESPUESTA';
                                elseif($value1->situacionsunat=='R')
                                    $sunat = 'RECHAZADO';
                                elseif($value1->situacionsunat=='E')
                                    $sunat = 'ERROR';
                                elseif($value1->situacionsunat=='P')
                                    $sunat = 'ACEPTADO';
                                else
                                    $sunat = 'PENDIENTE';
                                $detalle[] = $sunat;
                                $sheet->row($c,$detalle);
                                $c=$c+1;
                            }
                        }
                    }
                    if($value->situacion=='N' || $value->situacion=='A'){//SOLO PAGADOS
                        $res = Detallemovcaja::where('movimiento_id','=',$value->movimiento_id)->get();
                        $caja = Movimiento::where('movimiento_id','=',$value->id)->first();
                        if(!is_null($caja) && $caja->tipotarjeta!=""){
                            foreach($res as $k1 => $det){
                                $detalle = array();
                                $detalle[] = date('d/m/Y',strtotime($value->fecha));
                                $ticket = Movimiento::find($value->movimiento_id);
                                $person = Person::find($ticket->persona_id);
                                $detalle[] = $person->apellidopaterno." ".$person->apellidomaterno." ".$person->nombres;
                                $detalle[] = $value->tipodocumento_id==5?'BV':'FT';
                                if($value->manual=='S')
                                    $detalle[] = $value->serie.'-'.$value->numero;
                                else
                                    $detalle[] = ($value->tipodocumento_id==5?'B':'F').str_pad($value->serie,3,'0',STR_PAD_LEFT).'-'.$value->numero;
                                if($value->situacion=="U"){
                                    $detalle[] = number_format(0,2,'.','');
                                    $detalle[] = "ANULADA";
                                }else{
                                    $detalle[] = number_format($det->pagohospital*$det->cantidad,2,'.','');
                                    $total = $total + number_format($det->pagohospital*$det->cantidad,2,'.','');
                                    if(!is_null($det->servicio) && $det->servicio_id>0){
                                        $detalle[] = $det->servicio->nombre;
                                    }else{
                                        $detalle[] = $det->descripcion;
                                    }
                                }
                                $detalle[] = $caja->voucher;
                                $detalle[] = $caja->tipotarjeta;
                                $detalle[] = $value->responsable2;
                                if($value->situacionsunat=='L')
                                    $sunat = 'PENDIENTE RESPUESTA';
                                elseif($value->situacionsunat=='E')
                                    $sunat = 'ERROR';
                                elseif($value->situacionsunat=='R')
                                    $sunat = 'RECHAZADO';
                                elseif($value->situacionsunat=='P')
                                    $sunat = 'ACEPTADO';
                                else
                                    $sunat = 'PENDIENTE';
                                $detalle[] = $sunat;
                                $sheet->row($c,$detalle);
                                $c=$c+1;
                            }
                        }
                    }
                }
                $detalle = array();
                $detalle[] = "";
                $detalle[] = "";
                $detalle[] = "";
                $detalle[] = "";
                $detalle[] = number_format($total,2,'.','');
                $sheet->row($c,$detalle);
 
            });
            
            $excel->sheet('Egresos', function($sheet) use($request) {
 
                $array = array();
                $cabecera = array();
                $cabecera[] = "Fecha";
                $cabecera[] = "Tipo Doc.";
                $cabecera[] = "Serie";
                $cabecera[] = "Nro.";
                $cabecera[] = "Proveedor";
                $cabecera[] = "Ruc";
                $cabecera[] = "Rubro";
                $cabecera[] = "Importe";
                $cabecera[] = "Glosa";
                $cabecera[] = "Usuario";
                $cabecera[] = "CTA";
                $cabecera[] = "CTA";
                $sheet->row(1,$cabecera);

                $resultado        = Movimiento::leftjoin('person as paciente', 'paciente.id', '=', 'movimiento.persona_id')
                            ->leftjoin('movimiento as mv2','movimiento.id',"=",'mv2.movimiento_id')
                            ->join('person as responsable', 'responsable.id', '=', 'movimiento.responsable_id')
                            ->where('movimiento.tipomovimiento_id','=',2)
                            ->where('movimiento.conceptopago_id','<>','2')
                            ->where('movimiento.conceptopago_id','<>','8')
                            ->where('movimiento.situacion','<>','R')
                            ->where('movimiento.situacion','<>','P')
                            ->where('movimiento.situacion','<>','A')
                            ->whereNotIn('movimiento.caja_id',[6,7])
                            ->whereNotNull('movimiento.caja_id')
                            ->where('movimiento.situacion','<>','U')
                            ->where(function($query){
                                $query
                                    ->whereNotIn('movimiento.conceptopago_id',[31])
                                    ->orWhere('mv2.situacion','<>','R');
                            })
                            ;
                if($request->input('fechainicial')!=""){
                    $resultado = $resultado->where('movimiento.fecha','>=',$request->input('fechainicial').' 00:00:00');
                }
                if($request->input('fechafinal')!=""){
                    $resultado = $resultado->where('movimiento.fecha','<=',$request->input('fechafinal').' 23:59:59');
                }        

                $resultado        = $resultado->select('movimiento.*','responsable.nombres as responsable2')->orderBy('movimiento.fecha', 'ASC')->get();
                $c=2;$d=3;$total=0;$band=true;$band2=true;
                foreach ($resultado as $key => $value){
                    if($value->conceptopago_id==20 || $value->conceptopago_id==16){
                        $mov = Movimiento::where('movimiento_id','=',$value->id)->first();
                        if(!is_null($mov)){
                            if($mov->situacion=='R'){
                                $band2=false;
                            }else{
                                $band2=true;
                            }
                        }else{
                            $band2=false;
                        }
                    }else{
                        $band2=true;
                    }
                    if($value->conceptopago->tipo=='E' && $band2){
                        $detalle = array();
                        $detalle[] = date('d/m/Y',strtotime($value->fecha));
                        //$ticket = Movimiento::find($value->movimiento_id);
                        if(($value->conceptopago_id==24 || $value->conceptopago_id==25) && $value->listapago){
                            if($value->formapago!=""){
                                $detalle[] = $value->formapago;
                                $num = explode('-',$value->voucher);
                                if(count($num)>1){
                                    $detalle[] = $num[0];
                                    $detalle[] = $num[1];
                                }else{
                                    $detalle[] = "";
                                    $detalle[] = $value->voucher;
                                }
                            }else{
                                $detalle[] = 'RH';
                                $list=explode(",",$value->listapago);
                                for($x=0;$x<count($list);$x++){
                                    $detalle1 = Detallemovcaja::find($list[0]);
                                    if($detalle1->recibo!=""){
                                        $num = explode('-',$detalle1->recibo);
                                    }
                                }
                                if(count($num)>1){
                                    $detalle[] = $num[0];
                                    $detalle[] = $num[1];
                                }else{
                                    $detalle[] = "";
                                    $detalle[] = $detalle1->recibo;
                                }
                            }
                        }else{
                            if($value->caja_id==4){
                                if($value->formapago!=""){
                                    $detalle[] = $value->formapago;    
                                }elseif($value->tipodocumento_id==7){
                                    $detalle[] = 'BV';    
                                }elseif($value->tipodocumento_id==6){
                                    $detalle[] = 'FT';    
                                }else{
                                    $detalle[] = '';    
                                }
                            }else{
                                $detalle[] = $value->formapago;
                            }
                            if($value->voucher!="")
                                $num = explode('-',$value->voucher);
                            else
                                $num = explode('-','1-'.$value->numero);
                            if(count($num)>1){
                                $detalle[] = $num[0];
                                $detalle[] = $num[1];
                            }else{
                                $detalle[] = "";
                                $detalle[] = $num[0];
                            }
                        }
                        if(!is_null($value->persona)){
                            if($value->persona->bussinesname!=null){
                                $detalle[] = $value->persona->bussinesname;
                            }else{
                                $detalle[] = $value->persona->apellidopaterno." ".$value->persona->apellidomaterno." ".$value->persona->nombres;
                            }
                            $detalle[] = ($value->persona->ruc==""?"00000":$value->persona->ruc);
                        }else{
                            $detalle[] = "";
                            $detalle[] = "00000";
                        }
                        if(strtoupper($value->conceptopago->nombre)=="TRANSFERENCIA FARMACIA" && strtoupper($value->comentario)=="TRANSFERENCIA A FARMACIA JESSENIA" && floatval($value->total) == 16 && $value->id != 293223){
                            //dd($value);
                        }
                        $detalle[] = $value->conceptopago->nombre;
                        $detalle[] = number_format($value->total,2,'.','');
                        $detalle[] = $value->comentario;
                        $detalle[] = $value->responsable2;
                        $total = $total + number_format($value->total,2,'.','');
                        if($value->caja_id==3)
                            $detalle[] = "101101";
                        else
                            $detalle[] = "101101";
                        $detalle[] = $value->conceptopago->cuenta;
                        $sheet->row($c,$detalle);
                        $c=$c+1;
                    }
                }
                $detalle = array();
                $detalle[] = "";
                $detalle[] = "";
                $detalle[] = "";
                $detalle[] = "";
                $detalle[] = "";
                $detalle[] = "";
                $detalle[] = "";
                $detalle[] = number_format($total,2,'.','');
                $sheet->row($c,$detalle);
            });
            
            $excel->sheet('EgresosTesoreria', function($sheet) use($request) {
 
                $array = array();
                $cabecera = array();
                $cabecera[] = "Fecha";
                $cabecera[] = "Tipo Doc.";
                $cabecera[] = "Serie";
                $cabecera[] = "Nro.";
                $cabecera[] = "Proveedor";
                $cabecera[] = "Ruc";
                $cabecera[] = "Rubro";
                $cabecera[] = "Importe";
                $cabecera[] = "Glosa";
                $cabecera[] = "Usuario";
                $cabecera[] = "CTA";
                $cabecera[] = "CTA";
                $sheet->row(1,$cabecera);

                $resultado        = Movimiento::leftjoin('person as paciente', 'paciente.id', '=', 'movimiento.persona_id')
                            ->join('person as responsable', 'responsable.id', '=', 'movimiento.responsable_id')
                            ->where('movimiento.tipomovimiento_id','=',2)
                            ->where('movimiento.conceptopago_id','<>','2')
                            ->where('movimiento.conceptopago_id','<>','8')
                            ->where('movimiento.caja_id','=','6')
                            ->where('movimiento.situacion','<>','R')
                            ->where('movimiento.situacion','<>','P')
                            ->where('movimiento.situacion','<>','A')
                            ->whereNotNull('movimiento.caja_id');
                if($request->input('fechainicial')!=""){
                    $resultado = $resultado->where('movimiento.fecha','>=',$request->input('fechainicial').' 00:00:00');
                }
                if($request->input('fechafinal')!=""){
                    $resultado = $resultado->where('movimiento.fecha','<=',$request->input('fechafinal').' 23:59:59');
                }        

                $resultado        = $resultado->select('movimiento.*','responsable.nombres as responsable2')->orderBy('movimiento.fecha', 'ASC')->get();
                $c=2;$d=3;$total=0;$band=true;
                foreach ($resultado as $key => $value){
                    if($value->conceptopago->tipo=='E'){
                        $detalle = array();
                        $detalle[] = date('d/m/Y',strtotime($value->fecha));
                        //$ticket = Movimiento::find($value->movimiento_id);
                       if(($value->conceptopago_id==24 || $value->conceptopago_id==25) && $value->listapago){
                            $detalle[] = 'RH';
                            if($value->voucher==""){
                                $list=explode(",",$value->listapago);
                                for($x=0;$x<count($list);$x++){
                                    $detalle1 = Detallemovcaja::find($list[0]);
                                    if($detalle1->recibo!=""){
                                        $num = explode('-',$detalle1->recibo);
                                    }
                                }
                            }else{
                                $num = explode('-',$value->voucher);
                            }
                            if(count($num)>1){
                                $detalle[] = $num[0];
                                $detalle[] = $num[1];
                            }else{
                                $detalle[] = "";
                                $detalle[] = $detalle1->recibo;
                            }
                        }else{
                            if($value->caja_id==4){
                                if($value->tipodocumento_id==7){
                                    $detalle[] = 'BV';    
                                }elseif($value->tipodocumento_id==6){
                                    $detalle[] = 'FT';    
                                }else{
                                    $detalle[] = $value->formapago;    
                                }
                            }else{
                                $detalle[] = $value->formapago;
                            }
                            if($value->voucher!="")
                                $num = explode('-',$value->voucher);
                            else
                                $num = explode('-','1-'.$value->numero);
                            if(count($num)>1){
                                $detalle[] = $num[0];
                                $detalle[] = $num[1];
                            }else{
                                $detalle[] = "";
                                $detalle[] = $num[0];
                            }
                        }
                        if(!is_null($value->persona)){
                            if($value->persona->bussinesname!=null){
                                $detalle[] = $value->persona->bussinesname;
                            }else{
                                $detalle[] = $value->persona->apellidopaterno." ".$value->persona->apellidomaterno." ".$value->persona->nombres;
                            }
                            $detalle[] = ($value->persona->ruc==""?"00000":$value->persona->ruc);
                        }else{
                            $detalle[] = "";
                            $detalle[] = "00000";
                        }
                        $detalle[] = $value->conceptopago->nombre;
                        $detalle[] = number_format($value->total,2,'.','');
                        $detalle[] = $value->comentario;
                        $detalle[] = $value->responsable2;
                        $total = $total + number_format($value->total,2,'.','');
                        $detalle[] = "101103";
                        $detalle[] = $value->conceptopago->cuenta;
                        $sheet->row($c,$detalle);
                        $c=$c+1;
                    }
                }
                $detalle = array();
                $detalle[] = "";
                $detalle[] = "";
                $detalle[] = "";
                $detalle[] = "";
                $detalle[] = "";
                $detalle[] = "";
                $detalle[] = "";
                $detalle[] = number_format($total,2,'.','');
                $sheet->row($c,$detalle);
 
            });
            
            $excel->sheet('NotaCredito', function($sheet) use($request) {
 
                $array = array();
                $cabecera = array();
                $cabecera[] = "Fecha";
                $cabecera[] = "Tipo";
                $cabecera[] = "Numero";
                $cabecera[] = "Usuario";
                $cabecera[] = "RUC";
                $cabecera[] = "Razon Social";
                $cabecera[] = "Descripcion";
                $cabecera[] = "Subtotal";
                $cabecera[] = "CTA";
                $cabecera[] = "Igv";
                $cabecera[] = "CTA";
                $cabecera[] = "Total";
                $cabecera[] = "CTA";
                $cabecera[] = "Fecha Ref";
                $cabecera[] = "Doc.";
                $cabecera[] = "Nro. Ref";
                $cabecera[] = "Sub Total Ref";
                $cabecera[] = "Igv Ref";
                $cabecera[] = "SUNAT";
                $sheet->row(1,$cabecera);

                $resultado2        = Movimiento::leftjoin('person as paciente', 'paciente.id', '=', 'movimiento.persona_id')
                            ->join('person as responsable', 'responsable.id', '=', 'movimiento.responsable_id')
                            ->leftjoin('movimiento as m2','m2.id','=','movimiento.movimiento_id')
                            ->where('movimiento.tipomovimiento_id','=',6);
                if($request->input('fechainicial')!=""){
                    $resultado2 = $resultado2->where('movimiento.fecha','>=',$request->input('fechainicial').' 00:00:00');
                }
                if($request->input('fechafinal')!=""){
                    $resultado2 = $resultado2->where('movimiento.fecha','<=',$request->input('fechafinal').' 23:59:59');
                }        

                $resultado2        = $resultado2->select('movimiento.*','m2.fecha as fecha2','m2.situacion as situacion2','m2.tarjeta as tarjeta2','m2.tipodocumento_id as tipodocumento_id2','m2.serie as serie2','m2.numero as numero2','m2.movimiento_id as movimiento_id2','m2.subtotal as subtotal2','m2.igv as igv2','m2.estadopago as estadopago2','responsable.nombres as responsable2','m2.nombrepaciente as nombrepaciente2','m2.manual as manual2')->orderBy('movimiento.fecha', 'ASC')->orderBy('movimiento.serie', 'ASC')->orderBy('movimiento.numero', 'ASC')->get();

                $c=2;$d=3;$subtotal=0;$igv=0;$total=0;$band=true;
                foreach ($resultado2 as $key => $value){
                    $detalle = Detallemovcaja::where('movimiento_id','=',$value->movimiento_id)->first();
                    if(!is_null($detalle) && $detalle->servicio_id>0 && !is_null($detalle->servicio)){
                        $glosa=$detalle->servicio->tiposervicio->nombre;
                    }else{
                        $glosa='SERVICIOS';
                    }
                    $rs=Detallemovcaja::where('movimiento_id','=',$value->id)
                            ->where(function($q){
                                $q->where('descripcion','like','%FARMACIA%')
                                  ->orWhere('descripcion','like','MEDICINA%')
                                  ->orWhere('descripcion','like','%MEDICAMENTO%');

                            })->get();
                    $farmacia=0;
                    if(count($rs)>0){
                        foreach ($rs as $k => $v) {
                            $farmacia=$farmacia + round($v->cantidad*$v->precio/1.18,2);
                        }
                        if(round($value->total/1.18,2)>$farmacia){
                            $value->subtotal = $value->subtotal - $farmacia;
                            $servicio='SERVICIOS';
                            $cuenta='704105';
                        }else{
                            $farmacia=0;
                            $servicio='FARMACIA';
                            $cuenta='701112';
                        }
                    }else{
                        if($value->serie2==4){
                            $servicio='FARMACIA';
                            $glosa='FARMACIA';
                            $cuenta='701112';
                        }else{
                            $servicio='SERVICIOS';
                            $cuenta='704105';
                        }
                    }

                    $detalle = array();
                    $detalle[] = date('d/m/Y',strtotime($value->fecha));
                    $detalle[] = 'NA';
                    if($value->manual=='S')
                        $detalle[] = $value->serie.'-'.$value->numero;
                    else
                        $detalle[] = ($value->tipodocumento_id2==5?'BC':'FC').str_pad($value->serie,2,'0',STR_PAD_LEFT).'-'.$value->numero;
                    $detalle[] = $value->responsable2;
                    if(!is_null($value->persona)){
                        if($value->tipodocumento_id2=='5'){
                            $detalle[] = "0000";
                            $detalle[] = $value->persona->apellidopaterno." ".$value->persona->apellidomaterno." ".$value->persona->nombres;
                        }else{
                            $detalle[] = $value->persona->ruc;
                            $detalle[] = $value->persona->bussinesname;
                        }
                    }else{
                        if($value->nombrepaciente2!=""){
                            $detalle[] = "0000";
                            $detalle[] = $value->nombrepaciente2;
                        }else{
                            $detalle[] = "0000";
                            $detalle[] = "";
                        }
                    }
                    $detalle[] = $glosa;
                    if($value->situacion!="U"){
                        $detalle[] = number_format($value->subtotal,2,'.','');
                        $subtotal = $subtotal + number_format($value->subtotal,2,'.','');
                    }else{
                        $detalle[] = number_format(0,2,'.','');
                        $subtotal = $subtotal + number_format(0,2,'.','');
                    }
                    if($value->serie2=="4"){
                        $detalle[] = "701112";
                    }else{
                        if($value->igv>0)
                            $detalle[] = "704105";
                        else
                            $detalle[] = "701112";
                    }
                    if($value->situacion!="U"){
                        $detalle[] = number_format($value->igv,2,'.','');
                        $igv = $igv + number_format($value->igv,2,'.','');
                    }else{
                        $detalle[] = number_format(0,2,'.','');
                        $igv = $igv + number_format(0,2,'.','');
                    }
                    $detalle[] = "401111";
                    if($value->situacion!="U"){
                        $detalle[] = number_format($value->total,2,'.','');
                        $total = $total + number_format($value->total,2,'.','');
                    }else{
                        $detalle[] = number_format(0,2,'.','');
                        $total = $total + number_format(0,2,'.','');
                    }
                    $caja = Movimiento::where("movimiento_id",'=',$value->movimiento_id)->where('tipomovimiento_id','=',2)->first();
                    $caja1 = Movimiento::where("movimiento_id",'=',$value->id)->where('tipomovimiento_id','=',2)->first();
                    $ticket=Movimiento::where('id','=',$value->movimiento_id2)->first();
                    if($value->tipodocumento_id2==17){
                        $detalle[] = "121206";
                    }else{
                        if((!is_null($ticket) && $ticket->situacion=="B") || $value->estadopago2=="PP"){
                            $detalle[] = "121204";
                        }elseif(!is_null($caja) && $caja->tipotarjeta!=""){
                            if(is_null($caja1)){
                                $detalle[] = "121205";//SIN MOVIMIETO DE CAJA
                            }else{
                                $detalle[] = "101101";//MOVIMIENTO DE CAJA DE LA NOTA DE CREDITO
                            }
                        }else{
                            if(!is_null($caja1) && $caja1->caja_id!="3"){//DIF DE CONVENIO
                                $detalle[] = "101101";
                            }else{
                                $detalle[] = "101101";
                            }
                        }
                    }
                    $detalle[] = date('d/m/Y',strtotime($value->fecha2));
                    $detalle[] = $value->tipodocumento_id2=="5"?"BV":"FT";
                    if($value->manual2=='S')
                        $detalle[] = $value->serie2.'-'.$value->numero2;
                    else
                        $detalle[] = ($value->tipodocumento_id2==5?'B':'F').str_pad($value->serie2,3,'0',STR_PAD_LEFT).'-'.$value->numero2;
                    $detalle[] = number_format($value->subtotal+$farmacia,2,'.','');
                    $detalle[] = number_format($value->igv,2,'.','');
                    if($value->situacionsunat=='L')
                        $sunat = 'PENDIENTE RESPUESTA';
                    elseif($value->situacionsunat=='E')
                        $sunat = 'ERROR';
                    elseif($value->situacionsunat=='R')
                        $sunat = 'RECHAZADO';
                    elseif($value->situacionsunat=='P')
                        $sunat = 'ACEPTADO';
                    else
                        $sunat = 'PENDIENTE';
                    $detalle[] = $sunat;
                    $sheet->row($c,$detalle);
                    $c=$c+1;
                    if($farmacia>0){
                        $detalle = array();
                        $detalle[] = date('d/m/Y',strtotime($value->fecha));
                        $detalle[] = 'NA';
                        if($value->manual=='S')
                            $detalle[] = $value->serie.'-'.$value->numero;
                        else
                            $detalle[] = ($value->tipodocumento_id2==5?'BC':'FC').str_pad($value->serie,2,'0',STR_PAD_LEFT).'-'.$value->numero;
                        $detalle[] = $value->responsable2;
                        if(!is_null($value->persona)){
                            if($value->tipodocumento_id2=='5'){
                                $detalle[] = "0000";
                                $detalle[] = $value->persona->apellidopaterno." ".$value->persona->apellidomaterno." ".$value->persona->nombres;
                            }else{
                                $detalle[] = $value->persona->ruc;
                                $detalle[] = $value->persona->bussinesname;
                            }
                        }else{
                            if($value->nombrepaciente2!=""){
                                $detalle[] = "0000";
                                $detalle[] = $value->nombrepaciente2;
                            }else{
                                $detalle[] = "0000";
                                $detalle[] = "";
                            }
                        }
                        $detalle[] = 'FARMACIA';
                        if($value->situacion!="U"){
                            $detalle[] = number_format($farmacia,2,'.','');
                            $subtotal = $subtotal + number_format($farmacia,2,'.','');
                        }else{
                            $detalle[] = number_format(0,2,'.','');
                            $subtotal = $subtotal + number_format(0,2,'.','');
                        }
                        $detalle[] = "701112";
                        $detalle[] = '';
                        $detalle[] = "";
                        $detalle[] = "";
                        $detalle[] = "";
                        $detalle[] = '';
                        $detalle[] = "";
                        $detalle[] = "";
                        $detalle[] = "";
                        $detalle[] = "";
                        if($value->situacionsunat=='L')
                            $sunat = 'PENDIENTE RESPUESTA';
                        elseif($value->situacionsunat=='E')
                            $sunat = 'ERROR';
                        elseif($value->situacionsunat=='R')
                            $sunat = 'RECHAZADO';
                        elseif($value->situacionsunat=='P')
                            $sunat = 'ACEPTADO';
                        else
                            $sunat = 'PENDIENTE';
                        $detalle[] = $sunat;
                        $sheet->row($c,$detalle);
                        $c=$c+1;
                    }

                }
                $detalle = array();
                $detalle[] = "";
                $detalle[] = "";
                $detalle[] = "";
                $detalle[] = "";
                $detalle[] = "";
                $detalle[] = "";
                $detalle[] = "";
                $detalle[] = number_format($subtotal,2,'.','');
                $detalle[] = "";
                $detalle[] = number_format($igv,2,'.','');
                $detalle[] = "";
                $detalle[] = number_format($total,2,'.','');
                $sheet->row($c,$detalle);
 
            });

            $excel->sheet('SaldoCaja', function($sheet) use($request) {
 
                $array = array();
                $cabecera = array();
                $cabecera[] = "Fecha";
                $cabecera[] = "Caja";
                $cabecera[] = "Ingresos";
                $cabecera[] = "Egresos";
                $cabecera[] = "Saldo";
                $cabecera[] = "Responsable";
                $cabecera[] = "Ingr. BV-FT";
                $cabecera[] = "Cobranza Efectivo";
                $cabecera[] = "Cobranza Tarjeta";
                $cabecera[] = "Ingr. x Trasnf.";
                $cabecera[] = "Trasnf.";
                $cabecera[] = "Garant.-Sobr.-Otros Ing";
                $sheet->row(1,$cabecera);

                $resultado        = Movimiento::join('person as responsable', 'responsable.id', '=', 'movimiento.responsable_id')
                            ->where('movimiento.tipomovimiento_id','=',2)
                            ->where('movimiento.conceptopago_id','=','2')
                            ->whereNotIn('movimiento.caja_id',[6,7]);
                if($request->input('fechainicial')!=""){
                    $resultado = $resultado->where('movimiento.fecha','>=',$request->input('fechainicial').' 00:00:00');
                }
                if($request->input('fechafinal')!=""){
                    $resultado = $resultado->where('movimiento.fecha','<=',$request->input('fechafinal').' 23:59:59');
                }        

                $resultado        = $resultado->select('movimiento.*','responsable.nombres as responsable2')->orderBy('movimiento.fecha', 'ASC')->get();
                $c=2;$d=3;$total=0;$band=true;

                $listConcepto     = array();
                $listConcepto[]   = 6;//TRANSF CAJA INGRESO
                $listConcepto[]   = 15;//TRANSF TARJETA INGRESO
                $listConcepto[]   = 17;//TRANSF SOCIO INGRESO
                $listConcepto[]   = 21;//TRANSF BOLETEO INGRESO
                foreach ($resultado as $key => $value1){
                    $apertura= Movimiento::where('conceptopago_id','=',1)
                            ->where('id','<',$value1->id)
                            ->where('caja_id','=',$value1->caja_id)
                            ->orderBy('id','desc')
                            ->first();

                    $resultado        = Movimiento::leftjoin('person as paciente', 'paciente.id', '=', 'movimiento.persona_id')
                                ->join('person as responsable', 'responsable.id', '=', 'movimiento.responsable_id')
                                ->join('conceptopago','conceptopago.id','=','movimiento.conceptopago_id')
                                ->leftjoin('movimiento as m2','m2.movimiento_id','=','movimiento.id')
                                ->where('movimiento.caja_id', '=', $value1->caja_id)
                                ->where(function ($query) use($value1,$apertura) {
                                    $query->where(function($q) use($value1,$apertura){
                                            $q->where('movimiento.id', '>', $apertura->id)
                                            ->where('movimiento.id', '<', $value1->id)
                                            ->whereNull('movimiento.cajaapertura_id');
                                    })
                                          ->orwhere(function ($query1) use($value1,$apertura){
                                            $query1->where('movimiento.cajaapertura_id','=',$apertura->id);
                                            });//normal
                                })
                                //->where('movimiento.id', '>', $aperturas[$valor])
                                //->where('movimiento.id', '<', $cierres[$valor])
                                ->where('movimiento.situacion', '<>', 'A')->where('movimiento.situacion', '<>', 'R')
                                ->where(function($query){
                                    $query
                                        ->whereNotIn('movimiento.conceptopago_id',[31])
                                        ->orWhere('m2.situacion','<>','R');
                                });
                    $resultado        = $resultado->select('movimiento.*','m2.situacion as situacion2','responsable.nombres as responsable2')->orderBy('conceptopago.tipo', 'asc')->orderBy('conceptopago.orden', 'asc')->orderBy('conceptopago.id', 'asc')->orderBy('movimiento.tipotarjeta', 'asc')->orderBy('movimiento.numero', 'asc');
                    $lista = $resultado->get();
                    $ingreso=0;$egreso=0;$visa=0;$master=0;$garantia=0;$efectivo=0;$transferenciai=0;$cliente=0;$cobrae=0;$cobrat=0;$itransferencia=0;
                    foreach ($lista as $key => $value){
                        if($value->conceptopago_id==33  && $value->situacion<>'A'){
                            $transferenciai = $transferenciai + $value->total;
                        }
                        if($value->conceptopago_id==3  && $value->tipotarjeta==''  && $value->situacion<>'A'){
                            if ($value->tipodocumento_id != 15) {
                                $Venta = Movimiento::find($value->movimiento_id);
                                $cliente = $cliente + $Venta->total;
                            }
                        }
                        if($value->conceptopago_id==23 || $value->conceptopago_id==32){
                            if($value->tipotarjeta==''){
                                $cobrae = $cobrae + $value->total;
                            }else{
                                $cobrat = $cobrat + $value->total;
                            }
                        }
                        if(in_array($value->conceptopago_id, $listConcepto) && $value->situacion<>'A'){
                            $itransferencia = $itransferencia + $value->total;
                        }
                        if($value->conceptopago_id<>2 && $value->situacion<>'A'){
                            if($value->conceptopago->tipo=="I"){
                                if($value->conceptopago_id<>10){//GARANTIA
                                    if($value->conceptopago_id<>15 && $value->conceptopago_id<>17 && $value->conceptopago_id<>19 && $value->conceptopago_id<>21){
                                        if ($value->tipodocumento_id != 15) {
                                            //echo $value->total."@";
                                            $ingreso = $ingreso + $value->total;
                                        }
                                            
                                    }elseif(($value->conceptopago_id==15 || $value->conceptopago_id==17 || $value->conceptopago_id==19 || $value->conceptopago_id==21) && $value->situacion=='C'){
                                        $ingreso = $ingreso + $value->total;    
                                    }
                                }else{
                                    $garantia = $garantia + $value->total;
                                }
                                if($value->conceptopago_id<>10){//GARANTIA
                                    if($value->tipotarjeta=='VISA'){
                                            $visa = $visa + $value->total;
                                    }elseif($value->tipotarjeta==''){
                                        if ($value->tipodocumento_id != 15) {
                                            $efectivo = $efectivo + $value->total;
                                        }
                                    }else{
                                        $master = $master + $value->total;
                                    }
                                }
                            }else{
                                if($value->conceptopago_id<>14 && $value->conceptopago_id<>16 && $value->conceptopago_id<>18 && $value->conceptopago_id<>20){
                                    if($value->conceptopago_id==8){//HONORARIOS MEDICOS
                                        $ingreso  = $ingreso - $value->total;
                                        $efectivo = $efectivo - $value->total;
                                    }else{
                                        $egreso  = $egreso + $value->total;
                                    }
                                }elseif(($value->conceptopago_id==14 || $value->conceptopago_id==16 || $value->conceptopago_id==18 || $value->conceptopago_id==20) && $value->situacion2=='C'){
                                    $egreso  = $egreso + $value->total;
                                }
                            }
                        }
                    }   
                    $detalle = array();
                    $detalle[] = date('d/m/Y',strtotime($value1->fecha));
                    
                    if(isset($value)){
                        $detalle[] = $value->caja->nombre;
                        if(isset($value->caja_id)){
                            if($value->caja_id==3) $ingreso = $ingreso + $apertura->total;                        
                        } else {
                            $detalle[] = "ERROR CAJA_ID";
                        }
                    } else {
                        $detalle[] = "ERROR CAJA_CIERRE";
                    }
                    $ingreso = $ingreso - $visa - $master;
                    $detalle[] = number_format($ingreso,2,'.','');
                    $detalle[] = number_format($egreso,2,'.','');
                    $detalle[] = number_format($ingreso - $egreso,2,'.','');
                    $detalle[] = $value1->responsable->nombres;
                    $detalle[] = number_format($cliente,2,'.','');
                    $detalle[] = number_format($cobrae,2,'.','');
                    $detalle[] = number_format($cobrat,2,'.','');
                    $detalle[] = number_format($transferenciai,2,'.','');
                    $detalle[] = number_format($itransferencia,2,'.','');
                    $detalle[] = number_format($ingreso - $cliente - $cobrae - $transferenciai - $itransferencia,2,'.','');
                    $sheet->row($c,$detalle);
                    $c=$c+1;
                    
                }
 
            });
            
        })->export('xls');
    }

    public function excelVentaBizlink(Request $request){
        setlocale(LC_TIME, 'spanish');
        $resultado        = Movimiento::leftjoin('person as paciente', 'paciente.id', '=', 'movimiento.persona_id')
                            ->join('person as responsable', 'responsable.id', '=', 'movimiento.responsable_id')
                            ->leftjoin('movimiento as m2','m2.id','=','movimiento.movimiento_id')
                            ->where('movimiento.tipomovimiento_id','=',4)
                            ->where('movimiento.ventafarmacia','=','N')
                            ->where('m2.tipomovimiento_id','=',1)
                            ->whereNotIn('movimiento.situacionsunat',['P']);
        if($request->input('fechainicial')!=""){
            $resultado = $resultado->where('movimiento.fecha','>=',$request->input('fechainicial').' 00:00:00');
        }
        if($request->input('fechafinal')!=""){
            $resultado = $resultado->where('movimiento.fecha','<=',$request->input('fechafinal').' 23:59:59');
        }        

        $resultado        = $resultado->select('movimiento.*','m2.situacion as situacion2','m2.tarjeta as tarjeta2',DB::raw('CONCAT(case when movimiento.tipodocumento_id=4 then "F" else "B" end,movimiento.serie,"-",movimiento.numero) as numero2'),'responsable.nombres as responsable2')->orderBy('movimiento.serie', 'ASC')->orderBy('movimiento.numero', 'ASC')->orderBy('movimiento.fecha', 'ASC')->get();

        Excel::create('ExcelVentaBizlink', function($excel) use($resultado,$request) {
            
            $excel->sheet('VentasBizlink', function($sheet) use($resultado,$request) {
 
                $array = array();
                $cabecera = array();
                $cabecera[] = "Fecha";
                $cabecera[] = "Paciente";
                $cabecera[] = "Doc.";
                $cabecera[] = "Nro.";
                $cabecera[] = "Cliente";
                $cabecera[] = "RUC";
                $cabecera[] = "Subtotal";
                $cabecera[] = "Igv";
                $cabecera[] = "Total";
                $cabecera[] = "Sunat";
                $sheet->row(1,$cabecera);
                $c=2;$d=3;$subtotal=0;$igv=0;$total=0;$band=true;
                foreach ($resultado as $key => $value){
                    //FARMACIA
                    if (($value->serie==7 || $value->serie==9) && $band) {
                        $band=false;
                        $resultado2        = Movimiento::leftjoin('person as paciente', 'paciente.id', '=', 'movimiento.persona_id')
                            ->join('person as responsable', 'responsable.id', '=', 'movimiento.responsable_id')
                            ->leftjoin('movimiento as m2','m2.id','=','movimiento.movimiento_id')
                            ->where('movimiento.tipomovimiento_id','=',4)
                            ->where('movimiento.ventafarmacia','=','S')
                            ->where('movimiento.tipodocumento_id','<>',15)
                            ->whereNotIn('movimiento.situacionsunat',['P']);
                        if($request->input('fechainicial')!=""){
                            $resultado2 = $resultado2->where('movimiento.fecha','>=',$request->input('fechainicial').' 00:00:00');
                        }
                        if($request->input('fechafinal')!=""){
                            $resultado2 = $resultado2->where('movimiento.fecha','<=',$request->input('fechafinal').' 23:59:59');
                        }        

                        $resultado2        = $resultado2->select('movimiento.*','m2.situacion as situacion2','m2.tarjeta as tarjeta2',DB::raw('CONCAT(case when movimiento.tipodocumento_id=4 then "F" else "B" end,movimiento.serie,"-",movimiento.numero) as numero2'))->orderBy('movimiento.serie', 'ASC')->orderBy('movimiento.numero', 'ASC')->get();

                        foreach ($resultado2 as $key1 => $value1){
                            $detalle = array();
                            $detalle[] = date('d/m/Y',strtotime($value1->fecha));
                            $person = Person::find($value1->persona_id);
                            if ($person !== null) {
                                $detalle[] = $person->apellidopaterno." ".$person->apellidomaterno." ".$person->nombres;
                            }else{
                                $detalle[] = $value1->nombrepaciente;
                            }
                            $detalle[] = $value1->tipodocumento_id==5?'BV':'FT';
                            if($value1->manual=='S')
                                $detalle[] = $value1->serie.'-'.$value1->numero;
                            else
                                $detalle[] = ($value1->tipodocumento_id==5?'B':'F').str_pad($value1->serie,3,'0',STR_PAD_LEFT).'-'.$value1->numero;
                            if($value1->tipodocumento_id==4){//Factura
                                if(!is_null($value1->empresa_id) && $value1->empresa_id>0){
                                    $detalle[] = $value1->empresa->bussinesname;
                                    $detalle[] = $value1->empresa->ruc;
                                }else{
                                    $detalle[] = "";
                                    $detalle[] = "";
                                }
                            }else{
                                $detalle[] = "";
                                $detalle[] = "0000";
                            }
                            if($value1->situacion=="U"){
                                $detalle[] = number_format(0,2,'.','');
                                $detalle[] = number_format(0,2,'.','');
                                $detalle[] = number_format(0,2,'.','');
                            }else{
                                $detalle[] = number_format($value1->subtotal,2,'.','');
                                $detalle[] = number_format($value1->igv,2,'.','');
                                $detalle[] = number_format($value1->total,2,'.','');
                                $subtotal = $subtotal + number_format($value1->subtotal,2,'.','');
                                $igv = $igv + number_format($value1->igv,2,'.','');
                                $total = $total + number_format($value1->total,2,'.','');
                            }
                            if($value1->situacionsunat=='L')
                                $sunat = 'PENDIENTE RESPUESTA';
                            elseif($value1->situacionsunat=='R')
                                $sunat = 'RECHAZADO';
                            elseif($value1->situacionsunat=='E')
                                $sunat = 'ERROR';
                            elseif($value1->situacionsunat=='P')
                                $sunat = 'ACEPTADO';
                            else
                                $sunat = 'PENDIENTE';
                            $detalle[] = $sunat;
                            $sheet->row($c,$detalle);
                            $c=$c+1;
                        }
                    }
                    $detalle = array();
                    $detalle[] = date('d/m/Y',strtotime($value->fecha));
                    $ticket = Movimiento::find($value->movimiento_id);
                    $person = Person::find($ticket->persona_id);
                    $detalle[] = $person->apellidopaterno." ".$person->apellidomaterno." ".$person->nombres;
                    $detalle[] = $value->tipodocumento_id==5?'BV':'FT';
                    if($value->manual=='S')
                        $detalle[] = $value->serie.'-'.$value->numero;
                    else
                        $detalle[] = ($value->tipodocumento_id==5?'B':'F').str_pad($value->serie,3,'0',STR_PAD_LEFT).'-'.$value->numero;
                    if($value->tipodocumento_id==4){//Factura
                        $detalle[] = $value->persona->bussinesname;
                        $detalle[] = $value->persona->ruc;
                    }else{
                        $detalle[] = "";
                        $detalle[] = "0000";
                    }
                    if($value->situacion=="U"){
                        $detalle[] = number_format(0,2,'.','');
                        $detalle[] = number_format(0,2,'.','');
                        $detalle[] = number_format(0,2,'.','');
                    }else{
                        $detalle[] = number_format($value->subtotal,2,'.','');
                        $detalle[] = number_format($value->igv,2,'.','');
                        $detalle[] = number_format($value->total,2,'.','');
                        $subtotal = $subtotal + number_format($value->subtotal,2,'.','');
                        $igv = $igv + number_format($value->igv,2,'.','');
                        $total = $total + number_format($value->total,2,'.','');
                    }
                    if($value->situacionsunat=='L')
                        $sunat = 'PENDIENTE RESPUESTA';
                    elseif($value->situacionsunat=='E')
                        $sunat = 'ERROR';
                    elseif($value->situacionsunat=='R')
                        $sunat = 'RECHAZADO';
                    elseif($value->situacionsunat=='P')
                        $sunat = 'ACEPTADO';
                    else
                        $sunat = 'PENDIENTE';
                    $detalle[] = $sunat;
                    $sheet->row($c,$detalle);
                    $c=$c+1;
                }
                $detalle = array();
                $detalle[] = "";
                $detalle[] = "";
                $detalle[] = "";
                $detalle[] = "";
                $detalle[] = "";
                $detalle[] = "";
                $detalle[] = number_format($subtotal,2,'.','');
                $detalle[] = number_format($igv,2,'.','');
                $detalle[] = number_format($total,2,'.','');
                $sheet->row($c,$detalle);
 
            });

            
        })->export('xls');
    }

    public function excelFarmacia(Request $request){
        setlocale(LC_TIME, 'spanish');
        $fechainicio             = Libreria::getParam($request->input('fechainicio'));
        $fechafin             = Libreria::getParam($request->input('fechafin'));
        $numero             = Libreria::getParam($request->input('numero'));
        $paciente             = Libreria::getParam($request->input('paciente'));
        $resultado        = Venta::leftjoin('person','person.id','=','movimiento.persona_id')
                                ->where('tipomovimiento_id', '=', '4')
                                ->where('ventafarmacia','=','S')//where('serie','=','4')->
                                ->where(function($query) use ($numero){   
                                if (!is_null($numero) && $numero !== '') {
                                   
                                    $query->where('numero', '=', $numero);
                                }
                            })->where(function($query) use ($fechainicio,$fechafin){   
                                if (!is_null($fechainicio) && $fechainicio !== '') {
                                    $query->where('fecha', '>=', $fechainicio);
                                }
                                if (!is_null($fechafin) && $fechafin !== '') {
                                    $query->where('fecha', '<=', $fechafin);
                                }
                            });
        if($paciente!=""){
            $resultado = $resultado->where(DB::raw('concat(person.apellidopaterno,\' \',person.apellidomaterno,\' \',person.nombres)'), 'LIKE', '%'.strtoupper($paciente).'%');
        }
        if($request->input('tipodocumento')!=""){
            $resultado = $resultado->where('movimiento.tipodocumento_id','=',$request->input('tipodocumento'));
        }
        if($request->input('situacion')!=""){
            $resultado = $resultado->where('movimiento.situacion','like',$request->input('situacion'));
        }
        $resultado = $resultado->orderBy('movimiento.id','DESC')->select('movimiento.*');
        $lista            = $resultado->get();

        Excel::create('ExcelFarmacia', function($excel) use($lista,$request) {
 
            $excel->sheet('Farmacia', function($sheet) use($lista,$request) {
                $cabecera[] = "Fecha";
                $cabecera[] = "Numero";
                $cabecera[] = "Paciente";
                $cabecera[] = "Total";
                $cabecera[] = "Descargado";
                $cabecera[] = "Observacion";
                $sheet->row(1,$cabecera);
                $c=2;$d=3;$band=true;

                foreach ($lista as $key => $value){
                    $detalle = array();
                    $nombrepaciente = '';
                    if ($value->persona_id !== NULL) {
                        $nombrepaciente = trim($value->person->bussinesname." ".$value->person->apellidopaterno." ".$value->person->apellidomaterno." ".$value->person->nombres);

                    }else{
                        $nombrepaciente = trim($value->nombrepaciente);
                    }
                    if($value->tipodocumento_id=="4"){
                        $abreviatura="F";
                    }elseif($value->tipodocumento_id=="5"){
                        $abreviatura="B";    
                    }else{
                        $abreviatura="G"; 
                    }
                    $detalle[] = date('d/m/Y',strtotime($value->fecha));
                    $detalle[] = utf8_encode($abreviatura.str_pad($value->serie,3,'0',STR_PAD_LEFT).'-'.str_pad($value->numero,8,'0',STR_PAD_LEFT));
                    $detalle[] = $nombrepaciente;
                    $detalle[] = $value->total;
                    $detalle[] = $value->tipo;
                    $detalle[] = $value->listapago;
                    $sheet->row($c,$detalle);
                    $c=$c+1;
                }
            });
        })->export('xls');
    }

    public function excelFarmacia1(Request $request){
        setlocale(LC_TIME, 'spanish');
        $id               = Libreria::getParam($request->input('venta_id'),'');
        $guia = $request->input('guia');
        $resultado        = Movimiento::leftjoin('person as paciente', 'paciente.id', '=', 'movimiento.persona_id')
                            ->leftjoin('person as responsable','responsable.id','=','movimiento.responsable_id')
                            ->join('tipodocumento','tipodocumento.id','=','movimiento.tipodocumento_id')
                            ->where('movimiento.id', '=', $id);
        $resultado        = $resultado->select('movimiento.*','tipodocumento.nombre as tipodocumento');
        $lista            = $resultado->get();
        //print_r(count($lista));
        if (count($lista) > 0) {     
            foreach($lista as $key => $value){
                if ( ($value->tipodocumento_id == 5) || ($value->tipodocumento_id == 4)) {
                    if ($guia == 'SI') {
                        Excel::create('ExcelGuia', function($excel) use($value,$request,$id) {
                 
                            $excel->sheet('Guia', function($sheet) use($value,$request,$id) {
                                $cabecera[] = "GUIA INTERNA DE SALIDA DE MEDICAMENTOS";
                                $sheet->row(1,$cabecera);
                                $sheet->mergeCells('A1:G1');
                                $detalle = array();
                                $abreviatura="B";
                                $detalle[]=utf8_encode($abreviatura.str_pad($value->serie,3,'0',STR_PAD_LEFT).'-'.str_pad($value->numero,8,'0',STR_PAD_LEFT));
                                $detalle[]="";
                                $detalle[]='Usuario: '.$value->responsable->nombres;
                                $detalle[]="";
                                $detalle[]='Convenio: '.$value->conveniofarmacia->nombre;
                                $detalle[]="";
                                $detalle[]="";
                                $sheet->row(2,$detalle);
                                $sheet->mergeCells('A2:B2');
                                $sheet->mergeCells('C2:D2');
                                $sheet->mergeCells('E2:G2');
                                $detalle = array();
                                if ($value->persona_id !== NULL) {
                                    $detalle[]=(trim("Paciente: ".$value->persona->bussinesname." ".$value->persona->apellidopaterno." ".$value->persona->apellidomaterno." ".$value->persona->nombres));
                                    $detalle[]="";
                                    $detalle[]="";
                                    $detalle[]=($value->fecha)." ".(substr($value->created_at, 11));
                                    $detalle[]="";
                                }else{
                                    $detalle[]=(trim("Paciente: ".$value->nombrepaciente));
                                    $detalle[]="";
                                    $detalle[]="";
                                    $detalle[]=($value->fecha)." ".(substr($value->created_at, 11));
                                    $detalle[]="";
                                }
                                $sheet->row(3,$detalle);
                                $sheet->mergeCells('A3:C3');
                                $sheet->mergeCells('D3:E3');
                                $detalle = array();
                                $detalle[]="Cantidad";
                                $detalle[]="Producto";
                                $detalle[]="Prec. Unit.";
                                $detalle[]="Dscto";
                                $detalle[]="Copago";
                                $detalle[]="Total";
                                $detalle[]="Sin IGV";
                                $sheet->row(4,$detalle);
                                $detalle = array();
                                $resultado = Detallemovimiento::where('movimiento_id','=',$id);
                                $lista2            = $resultado->get();
                                $totalpago=0;
                                $totaldescuento=0;
                                $totaligv=0;$c=5;
                                foreach($lista2 as $key2 => $v){
                                    $valaux = round(($v->precio*$v->cantidad), 2);
                                    $precioaux = $v->precio - ($v->precio*($value->descuentokayros/100));
                                    $dscto = round(($precioaux*$v->cantidad),2);
                                    $totalpago = $totalpago+$dscto;
                                    $subtotal = round(($dscto*($value->copago/100)),2);
                                    $subigv = round(($subtotal/1.18),2);
                                    $totaldescuento = $totaldescuento+$subtotal;
                                    $totaligv = $totaligv+$subigv;
                                    $detalle = array();
                                    $detalle[]=number_format($v->cantidad,2,'.','');
                                    $detalle[]=utf8_encode($v->producto->nombre);
                                    $detalle[]=number_format($v->precio,2,'.','');
                                    $detalle[]=number_format($dscto,2,'.','');
                                    $detalle[]=number_format($value->copago,2,'.','');
                                    $detalle[]=number_format($subtotal,2,'.','');
                                    $detalle[]=number_format($subigv,2,'.','');
                                    $sheet->row($c,$detalle);
                                    $c=$c+1;
                                }
                                $detalle = array();
                                $detalle[]="";
                                $detalle[]="";
                                $detalle[]="";
                                $detalle[]=number_format($totalpago,2,'.','');
                                $detalle[]="";
                                $detalle[]=number_format($totaldescuento,2,'.','');
                                $detalle[]=number_format($totaligv,2,'.','');
                                $sheet->row($c,$detalle);
                                
                            });
                        })->export('xls');
                    }
                }elseif ($value->tipodocumento_id == 15) {
                    Excel::create('ExcelGuia', function($excel) use($value,$request,$id) {
                 
                        $excel->sheet('Guia', function($sheet) use($value,$request,$id) {
                            $cabecera[] = "GUIA INTERNA";
                            $sheet->row(1,$cabecera);
                            $sheet->mergeCells('A1:G1');
                            $detalle = array();
                            $abreviatura="G";
                            $detalle[]=utf8_encode($abreviatura.str_pad($value->serie,3,'0',STR_PAD_LEFT).'-'.str_pad($value->numero,8,'0',STR_PAD_LEFT));
                            $detalle[]="";
                            $detalle[]='Usuario: '.$value->responsable->nombres;
                            $detalle[]="";
                            $detalle[]='Convenio: '.$value->conveniofarmacia->nombre;
                            $detalle[]="";
                            $detalle[]="";
                            $sheet->row(2,$detalle);
                            $sheet->mergeCells('A2:B2');
                            $sheet->mergeCells('C2:D2');
                            $sheet->mergeCells('E2:G2');
                            $detalle = array();
                            if ($value->persona_id !== NULL) {
                                $detalle[]=(trim("Paciente: ".$value->persona->bussinesname." ".$value->persona->apellidopaterno." ".$value->persona->apellidomaterno." ".$value->persona->nombres));
                                $detalle[]="";
                                $detalle[]="";
                                $detalle[]=($value->fecha)." ".(substr($value->created_at, 11));
                                $detalle[]="";
                            }else{
                                $detalle[]=(trim("Paciente: ".$value->nombrepaciente));
                                $detalle[]="";
                                $detalle[]="";
                                $detalle[]=($value->fecha)." ".(substr($value->created_at, 11));
                                $detalle[]="";
                            }
                            $sheet->row(3,$detalle);
                            $sheet->mergeCells('A3:C3');
                            $sheet->mergeCells('D3:E3');
                            $detalle = array();
                            $detalle[]="Cantidad";
                            $detalle[]="Producto";
                            $detalle[]="Prec. Unit.";
                            $detalle[]="Dscto";
                            $detalle[]="Copago";
                            $detalle[]="Total";
                            $detalle[]="Sin IGV";
                            $sheet->row(4,$detalle);
                            $resultado = Detallemovimiento::where('movimiento_id','=',$id);
                            $lista2            = $resultado->get();
                            $totalpago=0;
                            $totaldescuento=0;
                            $totaligv=0;$c=5;
                            foreach($lista2 as $key2 => $v){
                                $valaux = round(($v->precio*$v->cantidad), 2);
                                $precioaux = $v->precio - ($v->precio*($value->descuentokayros/100));
                                $dscto = round(($precioaux*$v->cantidad),2);
                                $totalpago = $totalpago+$dscto;
                                $subtotal = round(($dscto*($value->copago/100)),2);
                                $subigv = round(($subtotal/1.18),2);
                                $totaldescuento = $totaldescuento+$subtotal;
                                $totaligv = $totaligv+$subigv;
                                $detalle = array();
                                $detalle[]=number_format($v->cantidad,2,'.','');
                                $detalle[]=utf8_encode($v->producto->nombre);
                                $detalle[]=number_format($v->precio,2,'.','');
                                $detalle[]=number_format($dscto,2,'.','');
                                $detalle[]=number_format($value->copago,2,'.','');
                                $detalle[]=number_format($subtotal,2,'.','');
                                $detalle[]=number_format($subigv,2,'.','');
                                $sheet->row($c,$detalle);
                                $c=$c+1;
                            }
                            $detalle = array();
                            $detalle[]="";
                            $detalle[]="";
                            $detalle[]="";
                            $detalle[]=number_format($totalpago,2,'.','');
                            $detalle[]="";
                            $detalle[]=number_format($totaldescuento,2,'.','');
                            $detalle[]=number_format($totaligv,2,'.','');
                            $sheet->row($c,$detalle);
                        });
                     })->export('xls');
                }
                    
            }
        }
    }

    //A -> LLAMANDO
    //B -> ATENDIENDO
    //C -> COLA
    //F -> FONDO
    //N -> NO ESTA
    //L -> LISTO

    public function cola(Request $request){
        date_default_timezone_set('America/Lima');

        $ticket_id = null;

        if($request->input('ticket_id') != null){
            $ticket_id = $request->input('ticket_id');
            $error = DB::transaction(function() use($request,$ticket_id){
                $Ticket = Movimiento::find($ticket_id);
                $Ticket->situacion2 = 'A'; // Llamando
                $Ticket->save();
            });
        }

        $consultas = Movimiento::where('fecha', date('Y-m-d') )->where('tiempo_fondo', null)->where('clasificacionconsulta','like','C')->orderBy('turno','ASC')->orderBy('tiempo_cola','ASC')->orderBy('situacion2','ASC')
        ->where(function($q) {            
            $q->where('situacion2', 'like', 'C')->orWhere('situacion2', 'like', 'A')->orWhere('situacion2', 'like', 'B')->orWhere('situacion2', 'like', 'N');
        })
        ->where(function($q) {            
            $q->where('situacion', 'like', 'C')->orWhere('situacion', 'like', 'R');
        });
        $lista = $consultas->limit(20)->get();

        $sconsutas = '';
        $semergencias = '';
        $sojos = '';
        $slectura = '';

        $sconsultas="
                    <h3 class='text-center' style='font-weight:bold;color:blue'>CONSULTAS</h3>
                    <table style='width:100%; font-weight: 700;' border='1'>
                        <thead>
                            <tr>
                                <th class='text-center' width='10%'>Nro</th>
                                <th class='text-center' width='70%'>Cliente</th>
                                <th class='text-center' style='display:none;' width='20%'>Tiempo</th>
                            </tr>
                        </thead>
                        <tbody>";
        $c=1;

        if(count($lista) == 0) {
            $sconsultas.= '<tr class="text-center"><td colspan="2">No Hay consultas.</td></tr>';
        }

        foreach ($lista as $key => $value) {
            if( $value->situacion2 == 'A'){
                $sconsultas.= "<tr class='llamando' style ='color: white; background-color:green;' id = '" . $value->id . "' >";
            }else if( $value->situacion2 == 'N'){
                $sconsultas.= "<tr class='tarde' style ='color: white; background-color:#f96a27;' id = '" . $value->id . "' >";
            }else if( $value->situacion2 == 'B'){
                $sconsultas.= "<tr class='atendiendo' style ='color: white; background-color:green;' id = '" . $value->id . "' >";
            }else{
                $sconsultas.= "<tr id = '" . $value->id . "' >";
            }

            //$sconsultas.= "<tr id = '" . $value->id . "' >";
            $sconsultas.= "<td class='text-center'>".$c."</td>";
            if(!is_null($value->persona)){
                $sconsultas.= "<td>".$value->persona->apellidopaterno." ".$value->persona->apellidomaterno." ".$value->persona->nombres."</td>";
            }
            
            $date1 = new \DateTime(date("H:i:s",strtotime('now')));
            $date2 = new \DateTime(date("H:i:s",strtotime($value->tiempo_cola)));

            $diff = $date2->diff($date1);

            $h = $diff->h;
            $m = $diff->i;
            $s = $diff->s;

            $tiempo ="";

            if($h<10){
                $tiempo = "0" . $h . ":";
            }else{
                $tiempo = $h . ":";
            }
            if($m<10){
                $tiempo = $tiempo . "0" . $m . ":";
            }else{
                $tiempo = $tiempo . $m . ":";
            }
            if($s<10){
                $tiempo = $tiempo . "0" . $s ;
            }else{
                $tiempo = $tiempo . $s;   
            }


           // $consultas.= "<td>". $diff->format('%H:%i:%s') ."</td>";
            $sconsultas.= "<td class='text-center' style='display:none;'>". $tiempo ."</td>";
            $sconsultas.= "</tr>";
            $c=$c+1;
        }

        $sconsultas .= '</tbody></table>';

        $emergencias = Movimiento::where('fecha', date('Y-m-d') )->where('tiempo_fondo', null)->where('clasificacionconsulta','like','E')->orderBy('tiempo_cola','ASC')->orderBy('situacion2','ASC')
        ->where(function($q) {            
            $q->where('situacion2', 'like', 'C')->orWhere('situacion2', 'like', 'A')->orWhere('situacion2', 'like', 'B')->orWhere('situacion2', 'like', 'N');
        })
        ->where(function($q) {            
            $q->where('situacion', 'like', 'C')->orWhere('situacion', 'like', 'R');
        });
        $lista2 = $emergencias->limit(15)->get();

        $fondos = Movimiento::where('fecha', date('Y-m-d') )->whereNotNull('tiempo_fondo')->orderBy('tiempo_fondo','ASC')->orderBy('situacion2','ASC')
        ->where(function($q) {            
            $q->where('situacion2', 'like', 'C')->orWhere('situacion2', 'like', 'A')->orWhere('situacion2', 'like', 'B')->orWhere('situacion2', 'like', 'F')->orWhere('situacion2', 'like', 'N');
        })
        ->where(function($q) {            
            $q->where('situacion', 'like', 'C')->orWhere('situacion', 'like', 'R');
        });
        $lista3 = $fondos->limit(15)->get();

        $lectura = Movimiento::where('fecha', date('Y-m-d') )->where('tiempo_fondo', null)->where('clasificacionconsulta','like','L')->orderBy('tiempo_cola','ASC')->orderBy('situacion2','ASC')
        ->where(function($q) {            
            $q->where('situacion2', 'like', 'C')->orWhere('situacion2', 'like', 'A')->orWhere('situacion2', 'like', 'B')->orWhere('situacion2', 'like', 'N');
        })
        ->where(function($q) {            
            $q->where('situacion', 'like', 'C')->orWhere('situacion', 'like', 'R');
        });
        $lista4 = $lectura->limit(15)->get();

        $semergencias.="<h3 class='text-center' style='font-weight:bold;color:red'>EMERGENCIAS</h3>
                        <table style='width:100%; font-weight: 700;' border='1'>
                            <thead>
                                <tr>
                                    <th class='text-center' width='10%'>Nro</th>
                                    <th class='text-center' width='70%'>Cliente</th>
                                    <th class='text-center' style='display:none;' width='20%'>Tiempo</th>
                                </tr>
                            </thead>
                            <tbody>";

        if(count($lista2) == 0) {
            $semergencias.= '<tr class="text-center"><td colspan="2">No Hay emergencias.</td></tr>';
        }
        $c=1;
        foreach ($lista2 as $key => $value) {
            if( $value->situacion2 == 'A'){
                $semergencias.= "<tr class='llamando' style ='color: white; background-color:green;' id = '" . $value->id . "' >";
            }else if( $value->situacion2 == 'N'){
                $semergencias.= "<tr class='tarde' style ='color: white; background-color:#f96a27;' id = '" . $value->id . "' >";
            }else if( $value->situacion2 == 'B'){
                $semergencias.= "<tr class='atendiendo' style ='color: white; background-color:green;' id = '" . $value->id . "' >";
            }else{
                $semergencias.= "<tr id = '" . $value->id . "' >";
            }
            $semergencias.= "<td class='text-center'>".$c."</td>";
            if(!is_null($value->persona)){
                $semergencias.= "<td>".$value->persona->apellidopaterno." ".$value->persona->apellidomaterno." ".$value->persona->nombres."</td>";
            }
            
            $date1 = new \DateTime(date("H:i:s",strtotime('now')));
            $date2 = new \DateTime(date("H:i:s",strtotime($value->tiempo_cola)));

            $diff = $date2->diff($date1);

            $h = $diff->h;
            $m = $diff->i;
            $s = $diff->s;

            $tiempo ="";

            if($h<10){
                $tiempo = "0" . $h . ":";
            }else{
                $tiempo = $h . ":";
            }
            if($m<10){
                $tiempo = $tiempo . "0" . $m . ":";
            }else{
                $tiempo = $tiempo . $m . ":";
            }
            if($s<10){
                $tiempo = $tiempo . "0" . $s ;
            }else{
                $tiempo = $tiempo . $s;   
            }


           // $semergencias.= "<td>". $diff->format('%H:%i:%s') ."</td>";
            $semergencias.= "<td class='text-center' style='display:none;'>". $tiempo ."</td>";
            $semergencias.= "</tr>";
            $c=$c+1;
        }

        $semergencias .= '</tbody></table>';
                            
        $sojos.="<h3 class='text-center' style='font-weight:bold;color:#3498DB'>FONDO DE OJOS</h3>
                    <table style='width:100%; font-weight: 700;' border='1'>
                            <thead>
                                <tr>
                                    <th class='text-center' width='10%'>Nro</th>
                                    <th class='text-center' width='70%'>Cliente</th>
                                    <th class='text-center' style='display:none;' width='20%'>Tiempo</th>
                                </tr>
                            </thead>
                            <tbody>";
        $c=1;

        if(count($lista3) == 0) {
            $sojos.= '<tr class="text-center"><td colspan="2">No Hay fondo de ojos.</td></tr>';
        }

        //fondos 

        foreach ($lista3 as $key => $value) {
            if( $value->situacion2 == 'A'){
                $sojos.= "<tr class='llamando' style ='color: white; background-color:green;' id = '" . $value->id . "' >";
            }else if( $value->situacion2 == 'N'){
                $sojos.= "<tr class='tarde' style ='color: white; background-color:#f96a27;' id = '" . $value->id . "' >";
            }else if( $value->situacion2 == 'B'){
                $sojos.= "<tr class='atendiendo' style ='color: white; background-color:green;' id = '" . $value->id . "' >";
            }else{
                $sojos.= "<tr id = '" . $value->id . "' >";
            }
            $sojos.= "<td class='text-center'>".$c."</td>";
            if(!is_null($value->persona)){
                $sojos.= "<td>".$value->persona->apellidopaterno." ".$value->persona->apellidomaterno." ".$value->persona->nombres."</td>";
            }

            $date1 = new \DateTime(date("H:i:s",strtotime('now')));
            $date2 = new \DateTime(date("H:i:s",strtotime($value->tiempo_fondo)));

            $diff = $date2->diff($date1);

            $h = $diff->h;
            $m = $diff->i;
            $s = $diff->s;

            $tiempo ="";

            if($h<10){
                $tiempo = "0" . $h . ":";
            }else{
                $tiempo = $h . ":";
            }
            if($m<10){
                $tiempo = $tiempo . "0" . $m . ":";
            }else{
                $tiempo = $tiempo . $m . ":";
            }
            if($s<10){
                $tiempo = $tiempo . "0" . $s ;
            }else{
                $tiempo = $tiempo . $s;   
            }


           // $registro.= "<td>". $diff->format('%H:%i:%s') ."</td>";
            $sojos.= "<td class='text-center' style='display:none;'>". $tiempo ."</td>";
            $sojos.= "</tr>";
            $c=$c+1;
        }

        $sojos.="</tbody></table>";

        $slectura.="<h3 class='text-center' style='font-weight:bold;color:green'>LECT. DE RESULTADOS</h3>
                    <table style='width:100%; font-weight: 700;' border='1'>
                            <thead>
                                <tr>
                                    <th class='text-center' width='10%'>Nro</th>
                                    <th class='text-center' width='70%'>Cliente</th>
                                    <th class='text-center' style='display:none;' width='20%'>Tiempo</th>
                                </tr>
                            </thead>
                            <tbody>";
        $c=1;

        if(count($lista4) == 0) {
            $slectura.= '<tr class="text-center"><td colspan="2">No Hay lectura de resultados.</td></tr>';
        }

        //lectura 

        foreach ($lista4 as $key => $value) {
            if( $value->situacion2 == 'A'){
                $slectura.= "<tr class='llamando' style ='color: white; background-color:green;' id = '" . $value->id . "' >";
            }else if( $value->situacion2 == 'N'){
                $slectura.= "<tr class='tarde' style ='color: white; background-color:#f96a27;' id = '" . $value->id . "' >";
            }else if( $value->situacion2 == 'B'){
                $slectura.= "<tr class='atendiendo' style ='color: white; background-color:green;' id = '" . $value->id . "' >";
            }else{
                $slectura.= "<tr id = '" . $value->id . "' >";
            }
            $slectura.= "<td class='text-center'>".$c."</td>";
            if(!is_null($value->persona)){
                $slectura.= "<td>".$value->persona->apellidopaterno." ".$value->persona->apellidomaterno." ".$value->persona->nombres."</td>";
            }

            $date1 = new \DateTime(date("H:i:s",strtotime('now')));
            $date2 = new \DateTime(date("H:i:s",strtotime($value->tiempo_cola)));

            $diff = $date2->diff($date1);

            $h = $diff->h;
            $m = $diff->i;
            $s = $diff->s;

            $tiempo ="";

            if($h<10){
                $tiempo = "0" . $h . ":";
            }else{
                $tiempo = $h . ":";
            }
            if($m<10){
                $tiempo = $tiempo . "0" . $m . ":";
            }else{
                $tiempo = $tiempo . $m . ":";
            }
            if($s<10){
                $tiempo = $tiempo . "0" . $s ;
            }else{
                $tiempo = $tiempo . $s;   
            }


           // $registro.= "<td>". $diff->format('%H:%i:%s') ."</td>";
            $slectura.= "<td class='text-center' style='display:none;'>". $tiempo ."</td>";
            $slectura.= "</tr>";
            $c=$c+1;
        }

        $slectura.="</tbody></table>";

        $jsondata = array(
            'emergencias' => $semergencias,
            'consultas' => $sconsultas,
            'ojos' => $sojos,
            'lectura' => $slectura,
        );
        return json_encode($jsondata);
    }

    public function colamedico(Request $request){
        date_default_timezone_set('America/Lima');

        $ticket_id = null;

        if($request->input('ticket_id') != null){
            $ticket_id = $request->input('ticket_id');
            $error = DB::transaction(function() use($request,$ticket_id){
                $Ticket = Movimiento::find($ticket_id);
                $Ticket->situacion2 = 'A'; // Llamando
                $Ticket->save();
            });
        }
        $consultasm = Movimiento::where('fecha', date('Y-m-d') )->where('turno','like','M')->where('tiempo_fondo', null)->where('clasificacionconsulta','like','C')->orderBy('tiempo_cola','ASC')->orderBy('situacion2','ASC')
        ->where(function($q) {            
            $q->where('situacion2', 'like', 'C')->orWhere('situacion2', 'like', 'A')->orWhere('situacion2', 'like', 'N')->orWhere('situacion2', 'like', 'B');
        })
        ->where(function($q) {            
            $q->where('situacion', 'like', 'C')->orWhere('situacion', 'like', 'R');
        });

        $consultast = Movimiento::where('fecha', date('Y-m-d') )->where('turno','like','T')->where('tiempo_fondo', null)->where('clasificacionconsulta','like','C')->orderBy('tiempo_cola','ASC')->orderBy('situacion2','ASC')
        ->where(function($q) {            
            $q->where('situacion2', 'like', 'C')->orWhere('situacion2', 'like', 'A')->orWhere('situacion2', 'like', 'N')->orWhere('situacion2', 'like', 'B');
        })
        ->where(function($q) {            
            $q->where('situacion', 'like', 'C')->orWhere('situacion', 'like', 'R');
        });
        $listam = $consultasm->get();
        $listat = $consultast->get();

        $sconsutas = '';
        $semergencias = '';
        $sojos = '';
        $slectura = '';

        $sconsultas="
                    <table style='width:100%' border='1'>
                        <thead>
                            <tr>
                                <th class='text-center' width='10%'>Nro</th>
                                <th class='text-center' width='70%'>Cliente</th>
                                <th class='text-center' style='display:none;' width='20%'>Tiempo</th>
                            </tr>
                        </thead>
                        <tbody>";
        $c=1;

        if(count($listam) == 0 && count($listat) == 0) {
            $sconsultas.= '<tr class="text-center"><td colspan="2">No Hay consultas.</td></tr>';
        }

        if(count($listam) != 0) {
            $sconsultas.= '<tr class="text-center"><td colspan="2">Turno Mañana</td></tr>';
        }

        foreach ($listam as $key => $value) {
            if( $value->situacion2 == 'A'){
                $sconsultas.= "<tr class='llamando' style ='color: white; background-color:green;' id = '" . $value->id . "' >";
            }else if( $value->situacion2 == 'N'){
                $sconsultas.= "<tr class='tarde' style ='color: white; background-color:#f96a27;' id = '" . $value->id . "' >";
            }else if( $value->situacion2 == 'B'){
                $sconsultas.= "<tr class='atendiendo' style ='color: white; background-color:green;' id = '" . $value->id . "' >";
            }else{
                $sconsultas.= "<tr id = '" . $value->id . "' >";
            }

            //$sconsultas.= "<tr id = '" . $value->id . "' >";
            $sconsultas.= "<td class='text-center'>".$c."</td>";
            if(!is_null($value->persona)){
                $sconsultas.= "<td>".$value->persona->apellidopaterno." ".$value->persona->apellidomaterno." ".$value->persona->nombres."</td>";
            }
            
            $date1 = new \DateTime(date("H:i:s",strtotime('now')));
            $date2 = new \DateTime(date("H:i:s",strtotime($value->tiempo_cola)));

            $diff = $date2->diff($date1);

            $h = $diff->h;
            $m = $diff->i;
            $s = $diff->s;

            $tiempo ="";

            if($h<10){
                $tiempo = "0" . $h . ":";
            }else{
                $tiempo = $h . ":";
            }
            if($m<10){
                $tiempo = $tiempo . "0" . $m . ":";
            }else{
                $tiempo = $tiempo . $m . ":";
            }
            if($s<10){
                $tiempo = $tiempo . "0" . $s ;
            }else{
                $tiempo = $tiempo . $s;   
            }


           // $consultas.= "<td>". $diff->format('%H:%i:%s') ."</td>";
            $sconsultas.= "<td class='text-center' style='display:none;'>". $tiempo ."</td>";
            $sconsultas.= "</tr>";
            $c=$c+1;
        }

        if(count($listat) != 0) {
            $sconsultas.= '<tr class="text-center"><td colspan="2">Turno Tarde</td></tr>';
        }

        $c=1;

        foreach ($listat as $key => $value) {
            if( $value->situacion2 == 'A'){
                $sconsultas.= "<tr class='llamando' style ='color: white; background-color:green;' id = '" . $value->id . "' >";
            }else if( $value->situacion2 == 'N'){
                $sconsultas.= "<tr class='tarde' style ='color: white; background-color:#f96a27;' id = '" . $value->id . "' >";
            }else if( $value->situacion2 == 'B'){
                $sconsultas.= "<tr class='atendiendo' style ='color: white; background-color:green;' id = '" . $value->id . "' >";
            }else{
                $sconsultas.= "<tr id = '" . $value->id . "' >";
            }

            //$sconsultas.= "<tr id = '" . $value->id . "' >";
            $sconsultas.= "<td class='text-center'>".$c."</td>";
            if(!is_null($value->persona)){
                $sconsultas.= "<td>".$value->persona->apellidopaterno." ".$value->persona->apellidomaterno." ".$value->persona->nombres."</td>";
            }
            
            $date1 = new \DateTime(date("H:i:s",strtotime('now')));
            $date2 = new \DateTime(date("H:i:s",strtotime($value->tiempo_cola)));

            $diff = $date2->diff($date1);

            $h = $diff->h;
            $m = $diff->i;
            $s = $diff->s;

            $tiempo ="";

            if($h<10){
                $tiempo = "0" . $h . ":";
            }else{
                $tiempo = $h . ":";
            }
            if($m<10){
                $tiempo = $tiempo . "0" . $m . ":";
            }else{
                $tiempo = $tiempo . $m . ":";
            }
            if($s<10){
                $tiempo = $tiempo . "0" . $s ;
            }else{
                $tiempo = $tiempo . $s;   
            }


           // $consultas.= "<td>". $diff->format('%H:%i:%s') ."</td>";
            $sconsultas.= "<td class='text-center' style='display:none;'>". $tiempo ."</td>";
            $sconsultas.= "</tr>";
            $c=$c+1;
        }

        $sconsultas .= '</tbody></table>';

        $emergenciasm = Movimiento::where('fecha', date('Y-m-d') )->where('turno','like','M')->where('tiempo_fondo', null)->where('clasificacionconsulta','like','E')->orderBy('tiempo_cola','ASC')->orderBy('situacion2','ASC')
        ->where(function($q) {            
            $q->where('situacion2', 'like', 'C')->orWhere('situacion2', 'like', 'A')->orWhere('situacion2', 'like', 'N')->orWhere('situacion2', 'like', 'B');
        })
        ->where(function($q) {            
            $q->where('situacion', 'like', 'C')->orWhere('situacion', 'like', 'R');
        });
        $emergenciast = Movimiento::where('fecha', date('Y-m-d') )->where('turno','like','T')->where('tiempo_fondo', null)->where('clasificacionconsulta','like','E')->orderBy('tiempo_cola','ASC')->orderBy('situacion2','ASC')
        ->where(function($q) {            
            $q->where('situacion2', 'like', 'C')->orWhere('situacion2', 'like', 'A')->orWhere('situacion2', 'like', 'N')->orWhere('situacion2', 'like', 'B');
        })
        ->where(function($q) {            
            $q->where('situacion', 'like', 'C')->orWhere('situacion', 'like', 'R');
        });
        $lista2m = $emergenciasm->get();
        $lista2t = $emergenciast->get();

        $fondosm = Movimiento::where('fecha', date('Y-m-d') )->where('turno','like','M')->whereNotNull('tiempo_fondo')->orderBy('tiempo_fondo','ASC')->orderBy('situacion2','ASC')
        ->where(function($q) {            
            $q->where('situacion2', 'like', 'C')->orWhere('situacion2', 'like', 'A')->orWhere('situacion2', 'like', 'N')->orWhere('situacion2', 'like', 'B')->orWhere('situacion2', 'like', 'F');
        })
        ->where(function($q) {            
            $q->where('situacion', 'like', 'C')->orWhere('situacion', 'like', 'R');
        });
        $fondost = Movimiento::where('fecha', date('Y-m-d') )->where('turno','like','T')->whereNotNull('tiempo_fondo')->orderBy('tiempo_fondo','ASC')->orderBy('situacion2','ASC')
        ->where(function($q) {            
            $q->where('situacion2', 'like', 'C')->orWhere('situacion2', 'like', 'A')->orWhere('situacion2', 'like', 'N')->orWhere('situacion2', 'like', 'B')->orWhere('situacion2', 'like', 'F');
        })
        ->where(function($q) {            
            $q->where('situacion', 'like', 'C')->orWhere('situacion', 'like', 'R');
        });
        $lista3m = $fondosm->get();
        $lista3t = $fondost->get();

        $lecturam = Movimiento::where('fecha', date('Y-m-d') )->where('turno','like','M')->where('tiempo_fondo', null)->where('clasificacionconsulta','like','L')->orderBy('tiempo_cola','ASC')->orderBy('situacion2','ASC')
        ->where(function($q) {            
            $q->where('situacion2', 'like', 'C')->orWhere('situacion2', 'like', 'A')->orWhere('situacion2', 'like', 'N')->orWhere('situacion2', 'like', 'B');
        })
        ->where(function($q) {            
            $q->where('situacion', 'like', 'C')->orWhere('situacion', 'like', 'R');
        });
        $lecturat = Movimiento::where('fecha', date('Y-m-d') )->where('turno','like','T')->where('tiempo_fondo', null)->where('clasificacionconsulta','like','L')->orderBy('tiempo_cola','ASC')->orderBy('situacion2','ASC')
        ->where(function($q) {            
            $q->where('situacion2', 'like', 'C')->orWhere('situacion2', 'like', 'A')->orWhere('situacion2', 'like', 'N')->orWhere('situacion2', 'like', 'B');
        })
        ->where(function($q) {            
            $q->where('situacion', 'like', 'C')->orWhere('situacion', 'like', 'R');
        });
        $lista4m = $lecturam->get();
        $lista4t = $lecturat->get();

        $semergencias.="
                        <table style='width:100%' border='1'>
                            <thead>
                                <tr>
                                    <th class='text-center' width='10%'>Nro</th>
                                    <th class='text-center' width='70%'>Cliente</th>
                                    <th class='text-center' style='display:none;' width='20%'>Tiempo</th>
                                </tr>
                            </thead>
                            <tbody>";

        if(count($lista2m) == 0 && count($lista2t) == 0) {
            $semergencias.= '<tr class="text-center"><td colspan="2">No Hay emergencias.</td></tr>';
        }

        if(count($lista2m) != 0) {
            $semergencias.= '<tr class="text-center"><td colspan="2">Turno Mañana</td></tr>';
        }
        $c=1;
        foreach ($lista2m as $key => $value) {
            if( $value->situacion2 == 'A'){
                $semergencias.= "<tr class='llamando' style ='color: white; background-color:green;' id = '" . $value->id . "' >";
            }else if( $value->situacion2 == 'N'){
                $semergencias.= "<tr class='tarde' style ='color: white; background-color:#f96a27;' id = '" . $value->id . "' >";
            }else if( $value->situacion2 == 'B'){
                $semergencias.= "<tr class='atendiendo' style ='color: white; background-color:green;' id = '" . $value->id . "' >";
            }else{
                $semergencias.= "<tr id = '" . $value->id . "' >";
            }
            $semergencias.= "<td class='text-center'>".$c."</td>";
            if(!is_null($value->persona)){
                $semergencias.= "<td>".$value->persona->apellidopaterno." ".$value->persona->apellidomaterno." ".$value->persona->nombres."</td>";
            }
            
            $date1 = new \DateTime(date("H:i:s",strtotime('now')));
            $date2 = new \DateTime(date("H:i:s",strtotime($value->tiempo_cola)));

            $diff = $date2->diff($date1);

            $h = $diff->h;
            $m = $diff->i;
            $s = $diff->s;

            $tiempo ="";

            if($h<10){
                $tiempo = "0" . $h . ":";
            }else{
                $tiempo = $h . ":";
            }
            if($m<10){
                $tiempo = $tiempo . "0" . $m . ":";
            }else{
                $tiempo = $tiempo . $m . ":";
            }
            if($s<10){
                $tiempo = $tiempo . "0" . $s ;
            }else{
                $tiempo = $tiempo . $s;   
            }


           // $semergencias.= "<td>". $diff->format('%H:%i:%s') ."</td>";
            $semergencias.= "<td class='text-center' style='display:none;'>". $tiempo ."</td>";
            $semergencias.= "</tr>";
            $c=$c+1;
        }

        if(count($lista2t) != 0) {
            $semergencias.= '<tr class="text-center"><td colspan="2">Turno Tarde</td></tr>';
        }
        $c=1;
        foreach ($lista2t as $key => $value) {
            if( $value->situacion2 == 'A'){
                $semergencias.= "<tr class='llamando' style ='color: white; background-color:green;' id = '" . $value->id . "' >";
            }else if( $value->situacion2 == 'N'){
                $semergencias.= "<tr class='tarde' style ='color: white; background-color:#f96a27;' id = '" . $value->id . "' >";
            }else if( $value->situacion2 == 'B'){
                $semergencias.= "<tr class='atendiendo' style ='color: white; background-color:green;' id = '" . $value->id . "' >";
            }else{
                $semergencias.= "<tr id = '" . $value->id . "' >";
            }
            $semergencias.= "<td class='text-center'>".$c."</td>";
            if(!is_null($value->persona)){
                $semergencias.= "<td>".$value->persona->apellidopaterno." ".$value->persona->apellidomaterno." ".$value->persona->nombres."</td>";
            }
            
            $date1 = new \DateTime(date("H:i:s",strtotime('now')));
            $date2 = new \DateTime(date("H:i:s",strtotime($value->tiempo_cola)));

            $diff = $date2->diff($date1);

            $h = $diff->h;
            $m = $diff->i;
            $s = $diff->s;

            $tiempo ="";

            if($h<10){
                $tiempo = "0" . $h . ":";
            }else{
                $tiempo = $h . ":";
            }
            if($m<10){
                $tiempo = $tiempo . "0" . $m . ":";
            }else{
                $tiempo = $tiempo . $m . ":";
            }
            if($s<10){
                $tiempo = $tiempo . "0" . $s ;
            }else{
                $tiempo = $tiempo . $s;   
            }


           // $semergencias.= "<td>". $diff->format('%H:%i:%s') ."</td>";
            $semergencias.= "<td class='text-center' style='display:none;'>". $tiempo ."</td>";
            $semergencias.= "</tr>";
            $c=$c+1;
        }

        $semergencias .= '</tbody></table>';
                            
        $sojos.="
                    <table style='width:100%' border='1'>
                            <thead>
                                <tr>
                                    <th class='text-center' width='10%'>Nro</th>
                                    <th class='text-center' width='70%'>Cliente</th>
                                    <th class='text-center' style='display:none;' width='20%'>Tiempo</th>
                                </tr>
                            </thead>
                            <tbody>";

        if(count($lista3m) == 0 && count($lista3t) == 0) {
            $sojos.= '<tr class="text-center"><td colspan="2">No Hay fondo de ojos.</td></tr>';
        }

        if(count($lista3m) != 0) {
            $sojos.= '<tr class="text-center"><td colspan="2">Turno Mañana</td></tr>';
        }

        //fondos 
        $c=1;

        foreach ($lista3m as $key => $value) {
            if( $value->situacion2 == 'A'){
                $sojos.= "<tr class='llamando' style ='color: white; background-color:green;' id = '" . $value->id . "' >";
            }else if( $value->situacion2 == 'N'){
                $sojos.= "<tr class='tarde' style ='color: white; background-color:#f96a27;' id = '" . $value->id . "' >";
            }else if( $value->situacion2 == 'B'){
                $sojos.= "<tr class='atendiendo' style ='color: white; background-color:green;' id = '" . $value->id . "' >";
            }else{
                $sojos.= "<tr id = '" . $value->id . "' >";
            }
            $sojos.= "<td class='text-center'>".$c."</td>";
            if(!is_null($value->persona)){
                $sojos.= "<td>".$value->persona->apellidopaterno." ".$value->persona->apellidomaterno." ".$value->persona->nombres."</td>";
            }

            $date1 = new \DateTime(date("H:i:s",strtotime('now')));
            $date2 = new \DateTime(date("H:i:s",strtotime($value->tiempo_fondo)));

            $diff = $date2->diff($date1);

            $h = $diff->h;
            $m = $diff->i;
            $s = $diff->s;

            $tiempo ="";

            if($h<10){
                $tiempo = "0" . $h . ":";
            }else{
                $tiempo = $h . ":";
            }
            if($m<10){
                $tiempo = $tiempo . "0" . $m . ":";
            }else{
                $tiempo = $tiempo . $m . ":";
            }
            if($s<10){
                $tiempo = $tiempo . "0" . $s ;
            }else{
                $tiempo = $tiempo . $s;   
            }


           // $registro.= "<td>". $diff->format('%H:%i:%s') ."</td>";
            $sojos.= "<td class='text-center' style='display:none;'>". $tiempo ."</td>";
            $sojos.= "</tr>";
            $c=$c+1;
        }

        if(count($lista3t) != 0) {
            $sojos.= '<tr class="text-center"><td colspan="2">Turno Tarde</td></tr>';
        }

        //fondos 
        $c=1;

        foreach ($lista3t as $key => $value) {
            if( $value->situacion2 == 'A'){
                $sojos.= "<tr class='llamando' style ='color: white; background-color:green;' id = '" . $value->id . "' >";
            }else if( $value->situacion2 == 'N'){
                $sojos.= "<tr class='tarde' style ='color: white; background-color:#f96a27;' id = '" . $value->id . "' >";
            }else if( $value->situacion2 == 'B'){
                $sojos.= "<tr class='atendiendo' style ='color: white; background-color:green;' id = '" . $value->id . "' >";
            }else{
                $sojos.= "<tr id = '" . $value->id . "' >";
            }
            $sojos.= "<td class='text-center'>".$c."</td>";
            if(!is_null($value->persona)){
                $sojos.= "<td>".$value->persona->apellidopaterno." ".$value->persona->apellidomaterno." ".$value->persona->nombres."</td>";
            }

            $date1 = new \DateTime(date("H:i:s",strtotime('now')));
            $date2 = new \DateTime(date("H:i:s",strtotime($value->tiempo_fondo)));

            $diff = $date2->diff($date1);

            $h = $diff->h;
            $m = $diff->i;
            $s = $diff->s;

            $tiempo ="";

            if($h<10){
                $tiempo = "0" . $h . ":";
            }else{
                $tiempo = $h . ":";
            }
            if($m<10){
                $tiempo = $tiempo . "0" . $m . ":";
            }else{
                $tiempo = $tiempo . $m . ":";
            }
            if($s<10){
                $tiempo = $tiempo . "0" . $s ;
            }else{
                $tiempo = $tiempo . $s;   
            }


           // $registro.= "<td>". $diff->format('%H:%i:%s') ."</td>";
            $sojos.= "<td class='text-center' style='display:none;'>". $tiempo ."</td>";
            $sojos.= "</tr>";
            $c=$c+1;
        }

        $sojos.="</tbody></table>";

        $slectura.="
                    <table style='width:100%' border='1'>
                            <thead>
                                <tr>
                                    <th class='text-center' width='10%'>Nro</th>
                                    <th class='text-center' width='70%'>Cliente</th>
                                    <th class='text-center' style='display:none;' width='20%'>Tiempo</th>
                                </tr>
                            </thead>
                            <tbody>";

        if(count($lista4m) == 0 && count($lista4t) == 0) {
            $slectura.= '<tr class="text-center"><td colspan="2">No Hay lectura de resultados.</td></tr>';
        }

        if(count($lista4m) != 0) {
            $slectura.= '<tr class="text-center"><td colspan="2">Turno Mañana</td></tr>';
        }

        //lectura 
        $c=1;

        foreach ($lista4m as $key => $value) {
            if( $value->situacion2 == 'A'){
                $slectura.= "<tr class='llamando' style ='color: white; background-color:green;' id = '" . $value->id . "' >";
            }else if( $value->situacion2 == 'N'){
                $slectura.= "<tr class='tarde' style ='color: white; background-color:#f96a27;' id = '" . $value->id . "' >";
            }else if( $value->situacion2 == 'B'){
                $slectura.= "<tr class='atendiendo' style ='color: white; background-color:green;' id = '" . $value->id . "' >";
            }else{
                $slectura.= "<tr id = '" . $value->id . "' >";
            }
            $slectura.= "<td class='text-center'>".$c."</td>";
            if(!is_null($value->persona)){
                $slectura.= "<td>".$value->persona->apellidopaterno." ".$value->persona->apellidomaterno." ".$value->persona->nombres."</td>";
            }

            $date1 = new \DateTime(date("H:i:s",strtotime('now')));
            $date2 = new \DateTime(date("H:i:s",strtotime($value->tiempo_cola)));

            $diff = $date2->diff($date1);

            $h = $diff->h;
            $m = $diff->i;
            $s = $diff->s;

            $tiempo ="";

            if($h<10){
                $tiempo = "0" . $h . ":";
            }else{
                $tiempo = $h . ":";
            }
            if($m<10){
                $tiempo = $tiempo . "0" . $m . ":";
            }else{
                $tiempo = $tiempo . $m . ":";
            }
            if($s<10){
                $tiempo = $tiempo . "0" . $s ;
            }else{
                $tiempo = $tiempo . $s;   
            }


           // $registro.= "<td>". $diff->format('%H:%i:%s') ."</td>";
            $slectura.= "<td class='text-center' style='display:none;'>". $tiempo ."</td>";
            $slectura.= "</tr>";
            $c=$c+1;
        }

        if(count($lista4t) != 0) {
            $slectura.= '<tr class="text-center"><td colspan="2">Turno Tarde</td></tr>';
        }

        //lectura 
        $c=1;

        foreach ($lista4t as $key => $value) {
            if( $value->situacion2 == 'A'){
                $slectura.= "<tr class='llamando' style ='color: white; background-color:green;' id = '" . $value->id . "' >";
            }else if( $value->situacion2 == 'N'){
                $slectura.= "<tr class='tarde' style ='color: white; background-color:#f96a27;' id = '" . $value->id . "' >";
            }else if( $value->situacion2 == 'B'){
                $slectura.= "<tr class='atendiendo' style ='color: white; background-color:green;' id = '" . $value->id . "' >";
            }else{
                $slectura.= "<tr id = '" . $value->id . "' >";
            }
            $slectura.= "<td class='text-center'>".$c."</td>";
            if(!is_null($value->persona)){
                $slectura.= "<td>".$value->persona->apellidopaterno." ".$value->persona->apellidomaterno." ".$value->persona->nombres."</td>";
            }

            $date1 = new \DateTime(date("H:i:s",strtotime('now')));
            $date2 = new \DateTime(date("H:i:s",strtotime($value->tiempo_cola)));

            $diff = $date2->diff($date1);

            $h = $diff->h;
            $m = $diff->i;
            $s = $diff->s;

            $tiempo ="";

            if($h<10){
                $tiempo = "0" . $h . ":";
            }else{
                $tiempo = $h . ":";
            }
            if($m<10){
                $tiempo = $tiempo . "0" . $m . ":";
            }else{
                $tiempo = $tiempo . $m . ":";
            }
            if($s<10){
                $tiempo = $tiempo . "0" . $s ;
            }else{
                $tiempo = $tiempo . $s;   
            }


           // $registro.= "<td>". $diff->format('%H:%i:%s') ."</td>";
            $slectura.= "<td class='text-center' style='display:none;'>". $tiempo ."</td>";
            $slectura.= "</tr>";
            $c=$c+1;
        }

        $slectura.="</tbody></table>";

        $jsondata = array(
            'emergencias' => $semergencias,
            'consultas' => $sconsultas,
            'ojos' => $sojos,
            'lectura' => $slectura,
        );
        return json_encode($jsondata);
    }

    public function pacienteEstado(Request $request){
        $estado = $request->input('estado');
        $ticket_id = $request->input('ticket_id');
        if($estado == "SI"){
            $error = DB::transaction(function() use($request,$ticket_id){
                $Ticket = Movimiento::find($ticket_id);
                $Ticket->situacion2 = 'B'; // Atendiendo
                $Ticket->save();
            });
        }else{
            $error = DB::transaction(function() use($request,$ticket_id){
                $Ticket = Movimiento::find($ticket_id);
                $Ticket->situacion2 = 'N'; // No está
                $Ticket->save();
            });
        }
    }

    public function llamarAtender(Request $request){

        $tabla="<table class='table table-bordered table-striped table-condensed table-hover' style='width:auto; vertical-align:middle;'>
                        <tbody>";

        $consultas = Movimiento::where('fecha', date('Y-m-d') )->where('tiempo_fondo', null)->where('clasificacionconsulta','like','C')->where('situacion2', 'like', 'C')->orderBy('turno','ASC')->orderBy('tiempo_cola','ASC')
        ->where(function($q) {            
            $q->where('situacion', 'like', 'C')->orWhere('situacion', 'like', 'R');
        });
        $consulta = $consultas->first();

        $consultas_no = Movimiento::where('fecha', date('Y-m-d') )->where('tiempo_fondo', null)->where('clasificacionconsulta','like','C')->where('situacion2', 'like', 'N')->orderBy('turno','ASC')->orderBy('tiempo_cola','ASC')
        ->where(function($q) {            
            $q->where('situacion', 'like', 'C')->orWhere('situacion', 'like', 'R');
        })->get();

        $tabla = $tabla . " <tr>
                                <th class='text-center' bgcolor='#E0ECF8' colspan='2'>CONSULTAS</td>
                            </tr>";

        if(count($consultas_no) == 0 && $consulta == null) {
            $tabla.= '<tr class="text-center"><td colspan="3">No hay consultas.</td></tr>';
        }

        if($consulta != null){
            $tabla = $tabla . "<tr>
                                <td>".$consulta->persona->apellidopaterno." ".$consulta->persona->apellidomaterno." ".$consulta->persona->nombres."</td>
                                <td align='right'><button data-paciente_id = '" . $consulta->persona->id . "' data-ticket_id = '" . $consulta->id . "' data-pantalla = 'SI' class='btn btn-success btn-sm btnLlamarPaciente' id='btnLlamarConsulta' onclick='' type='button'><i class='fa fa-check fa-lg'></i> Llamar Paciente</button></td>
                            </tr>";
        }

        foreach($consultas_no as $value){
            $tabla = $tabla . " <tr>
                                    <td>".$value->persona->apellidopaterno." ".$value->persona->apellidomaterno." ".$value->persona->nombres."</td>
                                    <td align='right'><button data-paciente_id = '" . $value->persona->id . "' data-ticket_id = '" . $value->id . "' data-pantalla = 'SI' class='btn btn-warning btn-sm btnLlamarPaciente' id='btnLlamarConsulta' onclick='' type='button'><i class='fa fa-check fa-lg'></i> Llamar Paciente</button></td>
                                </tr>";
        }

        $emergencias = Movimiento::where('fecha', date('Y-m-d') )->where('tiempo_fondo', null)->where('clasificacionconsulta','like','E')->where('situacion2', 'like', 'C')->orderBy('turno','ASC')->orderBy('tiempo_cola','ASC')
        ->where(function($q) {            
            $q->where('situacion', 'like', 'C')->orWhere('situacion', 'like', 'R');
        });
        $emergencia = $emergencias->first();

        $emergencias_no = Movimiento::where('fecha', date('Y-m-d') )->where('tiempo_fondo', null)->where('clasificacionconsulta','like','E')->where('situacion2', 'like', 'N')->orderBy('turno','ASC')->orderBy('tiempo_cola','ASC')
        ->where(function($q) {            
            $q->where('situacion', 'like', 'C')->orWhere('situacion', 'like', 'R');
        })->get();

        $tabla = $tabla . " <tr>
                                <th class='text-center' bgcolor='#FC350A' colspan='2'>EMERGENCIAS</th>
                            </tr>";

        if(count($emergencias_no) == 0 && $emergencia == null) {
            $tabla.= '<tr class="text-center"><td colspan="3">No hay emergencias.</td></tr>';
        }

        if($emergencia != null){
            $tabla = $tabla . "<tr>
                                <td>".$emergencia->persona->apellidopaterno." ".$emergencia->persona->apellidomaterno." ".$emergencia->persona->nombres."</td>
                                <td align='right'><button data-paciente_id = '" . $emergencia->persona->id . "' data-ticket_id = '" . $emergencia->id . "' data-pantalla = 'SI' class='btn btn-success btn-sm btnLlamarPaciente' id='btnLlamarConsulta' onclick='' type='button'><i class='fa fa-check fa-lg'></i> Llamar Paciente</button></td>
                            </tr>";
        }

        foreach($emergencias_no as $value){
            $tabla = $tabla . " <tr>
                                    <td>".$value->persona->apellidopaterno." ".$value->persona->apellidomaterno." ".$value->persona->nombres."</td>
                                    <td align='right'><button data-paciente_id = '" . $value->persona->id . "' data-ticket_id = '" . $value->id . "' data-pantalla = 'SI' class='btn btn-warning btn-sm btnLlamarPaciente' id='btnLlamarConsulta' onclick='' type='button'><i class='fa fa-check fa-lg'></i> Llamar Paciente</button></td>
                                </tr>";
        }

        //lectura de resultados
        
        $lecturas = Movimiento::where('fecha', date('Y-m-d') )->where('tiempo_fondo', null)->where('clasificacionconsulta','like','L')->where('situacion2', 'like', 'C')->orderBy('turno','ASC')->orderBy('tiempo_cola','ASC')
        ->where(function($q) {            
            $q->where('situacion', 'like', 'C')->orWhere('situacion', 'like', 'R');
        });
        $lectura = $lecturas->first();

        $lecturas_no = Movimiento::where('fecha', date('Y-m-d') )->where('tiempo_fondo', null)->where('clasificacionconsulta','like','L')->where('situacion2', 'like', 'N')->orderBy('turno','ASC')->orderBy('tiempo_cola','ASC')
        ->where(function($q) {            
            $q->where('situacion', 'like', 'C')->orWhere('situacion', 'like', 'R');
        })->get();

        $tabla = $tabla . " <tr>
                                <th class='text-center' bgcolor='#44e044' colspan='2'>LECTURA DE RESULTADOS</th>
                            </tr>";
        
        
        if(count($lecturas_no) == 0 && $lectura == null) {
            $tabla.= '<tr class="text-center"><td colspan="3">No hay lectura de resultados.</td></tr>';
        }

        if($lectura != null){
            $tabla = $tabla . "<tr>
                                <td>".$lectura->persona->apellidopaterno." ".$lectura->persona->apellidomaterno." ".$lectura->persona->nombres."</td>
                                <td align='right'><button data-paciente_id = '" . $lectura->persona->id . "' data-ticket_id = '" . $lectura->id . "' data-pantalla = 'SI' class='btn btn-success btn-sm btnLlamarPaciente' id='btnLlamarConsulta' onclick='' type='button'><i class='fa fa-check fa-lg'></i> Llamar Paciente</button></td>
                            </tr>";
        }

        foreach($lecturas_no as $value){
            $tabla = $tabla . " <tr>
                                    <td>".$value->persona->apellidopaterno." ".$value->persona->apellidomaterno." ".$value->persona->nombres."</td>
                                    <td align='right'><button data-paciente_id = '" . $value->persona->id . "' data-ticket_id = '" . $value->id . "' data-pantalla = 'SI' class='btn btn-warning btn-sm btnLlamarPaciente' id='btnLlamarConsulta' onclick='' type='button'><i class='fa fa-check fa-lg'></i> Llamar Paciente</button></td>
                                </tr>";
        }

        //fin 

        $fondos = Movimiento::where('fecha', date('Y-m-d') )->whereNotNull('tiempo_fondo')->where('situacion2', 'like', 'F')->orderBy('turno','ASC')->orderBy('tiempo_fondo','ASC')
        ->where(function($q) {            
            $q->where('situacion', 'like', 'C')->orWhere('situacion', 'like', 'R');
        });
        $fondo = $fondos->first();

        $fondos_no = Movimiento::where('fecha', date('Y-m-d') )->whereNotNull('tiempo_fondo')->where('situacion2', 'like', 'N')->orderBy('turno','ASC')->orderBy('tiempo_fondo','ASC')
        ->where(function($q) {            
            $q->where('situacion', 'like', 'C')->orWhere('situacion', 'like', 'R');
        })->get();

        $tabla = $tabla . " <tr>
                                <th class='text-center' bgcolor='#3498db' colspan='2'>FONDO DE OJO</th>
                            </tr>";

        if(count($fondos_no) == 0 && $fondo == null) {
            $tabla.= '<tr class="text-center"><td colspan="3">No hay fondo de ojos.</td></tr>';
        }
        
        if($fondo != null){
            $date1 = new \DateTime(date("H:i:s",strtotime('now')));
            $date2 = new \DateTime(date("H:i:s",strtotime($fondo->tiempo_fondo)));
            $diff = abs($date2->getTimestamp() - $date1->getTimestamp())/60;
            if($diff > 30){
                $tabla = $tabla . "<tr>
                                <td>".$fondo->persona->apellidopaterno." ".$fondo->persona->apellidomaterno." ".$fondo->persona->nombres."</td>
                                <td align='right'><button data-paciente_id = '" . $fondo->persona->id . "' data-ticket_id = '" . $fondo->id . "' data-pantalla = 'SI' class='btn btn-success btn-sm btnLlamarPaciente' id='btnLlamarConsulta' onclick='' type='button'><i class='fa fa-check fa-lg'></i> Llamar Paciente</button></td>
                            </tr>";

            }else{
                $tabla.= '<tr class="text-center"><td colspan="3">Fondo de ojos en espera.</td></tr>';
            }
        }

        foreach($fondos_no as $value){
            $tabla = $tabla . " <tr>
                                    <td>".$value->persona->apellidopaterno." ".$value->persona->apellidomaterno." ".$value->persona->nombres."</td>
                                    <td align='right'><button data-paciente_id = '" . $value->persona->id . "' data-ticket_id = '" . $value->id . "' data-pantalla = 'SI' class='btn btn-warning btn-sm btnLlamarPaciente' id='btnLlamarConsulta' onclick='' type='button'><i class='fa fa-check fa-lg'></i> Llamar Paciente</button></td>
                                </tr>";
        }

        $tabla = $tabla . "</tbody>
                        </table>";

        return $tabla;
    }

    public function llamarPacienteNombre(Request $request){
            
        //A -> LLAMANDO
        //B -> ATENDIENDO
        //C -> COLA
        //F -> FONDO
        //N -> NO ESTA
        //L -> LISTO

        $paciente = $request->input('paciente');
        
        $resultado        = Movimiento::leftjoin('person as paciente', 'paciente.id', '=', 'movimiento.persona_id')
        ->where('movimiento.tipodocumento_id','=','1')
        ->where('movimiento.fecha', date('Y-m-d') )
        //->where('movimiento.tiempo_fondo', null)
        ->where(function($q) {            
            $q->where('situacion2', 'like', 'C')->orWhere('situacion2', 'like', 'N')->orWhere('situacion2', 'like', 'F');
        })
        ->where(function($q) {            
            $q->where('clasificacionconsulta','like','C')->orWhere('clasificacionconsulta','like','E')->orWhere('clasificacionconsulta','like','L');
        })
        ->where(function($q) {            
            $q->where('situacion', 'like', 'C')->orWhere('situacion', 'like', 'R');
        });
        if($paciente!="0"){
            $resultado = $resultado->where(DB::raw('concat(paciente.apellidopaterno,\' \',paciente.apellidomaterno,\' \',paciente.nombres)'), 'LIKE', '%'.$paciente.'%');
        }
        $resultado        = $resultado->select('movimiento.*',DB::raw('concat(paciente.apellidopaterno,\' \',paciente.apellidomaterno,\' \',paciente.nombres) as paciente'))->orderBy('movimiento.tiempo_cola','DESC')->orderBy('movimiento.situacion','DESC');
        $lista            = $resultado->get();


        $tabla="<table class='table table-bordered table-striped table-condensed table-hover' style='width:100%; vertical-align:middle;'>
                        <tbody>";

        $tabla = $tabla . " <tr>
                <th class='text-center' bgcolor='#ffa500' colspan='2'>RESULTADO DE BÚSQUEDA</th>
            </tr>";

        if(count($lista) == 0 ){
            $tabla.= '<tr class="text-center"><td colspan="3">No se encontraron resultados</td></tr>';
        }    

        foreach ($lista as $value) {
            
            $tabla = $tabla . " <tr>
                                    <td>".$value->paciente."</td>
                                    <td align='right'><button data-paciente_id = '" . $value->persona->id . "' data-ticket_id = '" . $value->id . "' data-pantalla = 'NO' class='btn btn-primary btn-sm btnLlamarPaciente' id='btnLlamarConsulta' onclick='' type='button'><i class='fa fa-check fa-lg'></i> Llamar Paciente</button></td>
                                </tr>";

        }

        $tabla = $tabla . "</tbody>
        </table>";

        return $tabla;

    }
}