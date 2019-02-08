<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Validator;
use App\Http\Requests;
use App\Historia;
use App\Convenio;
use App\Caja;
use App\Person;
use App\Venta;
use App\Movimiento;
use App\Servicio;
use App\Tipodocumento;
use App\Conceptopago;
use App\Detallemovcaja;
use App\Detallemovimiento;
use App\Librerias\Libreria;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Jenssegers\Date\Date;
use Elibyy\TCPDF\Facades\TCPDF;
use Illuminate\Support\Facades\Auth;
use Excel;

class MTCPDF extends TCPDF {

    // Page footer
    public function Footer() {
        // Position at 15 mm from bottom
        $this->SetY(-15);
        // Set font
        $this->SetFont('helvetica', 'I', 8);
        // Page number
        $this->Cell(190, 10, 'Page '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
    }
}

class CajaController extends Controller
{
    protected $folderview      = 'app.caja';
    protected $tituloAdmin     = 'Caja';
    protected $tituloRegistrar = 'Registrar Movimiento de Caja';
    protected $tituloModificar = 'Modificar Caja';
    protected $tituloEliminar  = 'Eliminar Caja';
    protected $rutas           = array('create' => 'caja.create', 
            'edit'   => 'caja.edit', 
            'delete' => 'caja.eliminar',
            'search' => 'caja.buscar',
            'buscarcontrol' => 'caja.buscarcontrol',
            'index'  => 'caja.index',
            'pdfListar'  => 'caja.pdfListar',
            'apertura' => 'caja.apertura',
            'cierre' => 'caja.cierre',
            'acept' => 'caja.acept',
            'reject' => 'caja.reject',
            'imprimir' => 'caja.imprimir',
            'descarga' => 'caja.descarga',
            'control' => 'caja.control',
            'ticketspendientes' => 'caja.ticketspendientes',
            'listaticketspendientes' => 'caja.listaticketspendientes',
            'cobrarticket' => 'caja.cobrarticket',
            'cobrarticket2' => 'caja.cobrarticket2',
            'cuentaspendientes' => 'caja.cuentaspendientes',
            'listacuentaspendientes' => 'caja.listacuentaspendientes',
            'cobrarcuentapendiente' => 'caja.cobrarcuentapendiente',
            'cobrarcuentapendiente2' => 'caja.cobrarcuentapendiente2'
        );

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function buscar(Request $request)
    {
        $pagina           = $request->input('page');
        $filas            = $request->input('filas');
        $entidad          = 'Caja';
        $caja_id          = Libreria::getParam($request->input('caja_id'),'1');

        $user = Auth::user();

        $tipousuario = $user->usertype_id;

        if($user->sucursal_id == 1){
            if($user->usertype_id==23){
                $caja_id = 1;
            }
            if($user->usertype_id==11){
                $caja_id = 3;
            }
        }else{
            if($user->usertype_id==23){
                $caja_id = 2;
            }
            if($user->usertype_id==11){
                $caja_id = 4;
            }
        }

        $rst  = Movimiento::where('tipomovimiento_id','=',2)->where('caja_id','=',$caja_id)->orderBy('movimiento.id','DESC')->limit(1)->first();
        if(count($rst)==0){
            $conceptopago_id=2;
        }else{
            $conceptopago_id=$rst->conceptopago_id;
        }
        $titulo_registrar            = $this->tituloRegistrar;
        $titulo_apertura             = 'Apertura';
        $titulo_cierre               = 'Cierre';
        $titulo_ticketspendientes    = 'Tickets Pendientes'; 
        $titulo_cuentaspendientes    = 'Cuentas Pendientes'; 
        
        $rst              = Movimiento::where('tipomovimiento_id','=',2)->where('caja_id','=',$caja_id)->where('conceptopago_id','=',1)->orderBy('id','DESC')->limit(1)->first();
        if(count($rst)>0){
            $movimiento_mayor = $rst->id;    
        }else{
            $movimiento_mayor = 0;
        }

        //sucursal_id
        $sucursal_id = Session::get('sucursal_id');

        $resultado        = Movimiento::leftjoin('person as paciente', 'paciente.id', '=', 'movimiento.persona_id')
                            ->join('person as responsable', 'responsable.id', '=', 'movimiento.responsable_id')
                            ->join('conceptopago','conceptopago.id','=','movimiento.conceptopago_id')
                            ->leftjoin('movimiento as m2','movimiento.id','=','m2.movimiento_id')
                            ->where('movimiento.caja_id', '=', $caja_id)
                            ->whereNull('movimiento.cajaapertura_id')
                            ->where('movimiento.sucursal_id','=',$sucursal_id)
                            ->where('movimiento.id', '>=', $movimiento_mayor)
                            ->where(function($query){
                                $query
                                    ->whereNotIn('movimiento.conceptopago_id',[15, 17, 19, 21, 32])
                                    ->orWhere('movimiento.situacion','<>','R');
                            });
        $resultado        = $resultado->select('movimiento.*','m2.situacion as situacion2',DB::raw('case when paciente.bussinesname is null then concat(paciente.apellidopaterno,\' \',paciente.nombres) else paciente.bussinesname end as paciente'),DB::raw('responsable.nombres as responsable'))->orderBy('movimiento.id', 'desc');
        $lista            = $resultado->get();
        $listapendiente = array();

        if ($caja_id == 3 || $caja_id == 4) {
            $resultado2        = Movimiento::leftjoin('person as paciente', 'paciente.id', '=', 'movimiento.persona_id')
                            ->leftjoin('person as responsable', 'responsable.id', '=', 'movimiento.responsable_id')
                            ->leftjoin('conceptopago','conceptopago.id','=','movimiento.conceptopago_id')
                            ->leftjoin('movimiento as m2','movimiento.id','=','m2.movimiento_id')
                            ->where('movimiento.serie', '=', $caja_id)
                            ->where('movimiento.estadopago', '=', 'PP')
                            ->where('movimiento.sucursal_id','=',$sucursal_id)
                            ->where('movimiento.id', '>=', $movimiento_mayor);
            $resultado2        = $resultado2->select('movimiento.*','m2.situacion as situacion2',DB::raw('case when paciente.bussinesname is null then concat(paciente.apellidopaterno,\' \',paciente.nombres) else paciente.bussinesname end as paciente'),DB::raw('responsable.nombres as responsable'))->orderBy('movimiento.id', 'desc');
            $listapendiente            = $resultado2->get();
        }
        

        
        $cabecera         = array();
        //$cabecera[]       = array('valor' => '#', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Fecha', 'numero' => '1');
        $cabecera[]       = array('valor' => 'N°', 'numero' => '1');
        //$cabecera[]       = array('valor' => 'Tipo', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Concepto', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Persona', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Ingreso', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Egreso', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Forma Pago', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Comentario', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Usuario', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Operaciones', 'numero' => '3');
        
        $titulo_modificar = $this->tituloModificar;
        $titulo_eliminar  = $this->tituloEliminar;
        $titulo_anular    = "Anular";
        $ruta             = $this->rutas;
        $user = Auth::user();
        $ingreso=0;$egreso=0;$garantia=0;$efectivo=0;$master=0;$visa=0;
        //dd($lista);
        if (count($lista) > 0) {
            $clsLibreria     = new Libreria();
            foreach($lista as $k=>$v){
                if($v->conceptopago_id<>2 && $v->situacion<>'A'){
                    if($v->conceptopago->tipo=="I"){
                        if($v->conceptopago_id<>10){//Garantias
                            if($v->conceptopago_id<>15 && $v->conceptopago_id<>17 && $v->conceptopago_id<>19 && $v->conceptopago_id<>21 && $v->conceptopago_id<>32){
                                $ingreso = $ingreso + $v->total;    
                            }elseif(($v->conceptopago_id==15 || $v->conceptopago_id==17 || $v->conceptopago_id==19 || $v->conceptopago_id==21 || $v->conceptopago_id==32) && $v->situacion=='C'){
                                $ingreso = $ingreso + $v->total;    
                            }
                            if($v->tipotarjeta=='VISA'){
                                $visa = $visa + $v->total;
                            }elseif($v->tipotarjeta==''){
                                $efectivo = $efectivo + $v->total;
                            }else{
                                $master = $master + $v->total;
                            }
                        }else{
                            $garantia = $garantia + $v->total;
                        }
                    }else{
                        if($v->conceptopago_id<>14 && $v->conceptopago_id<>16 && $v->conceptopago_id<>18 && $v->conceptopago_id<>20 && $v->conceptopago_id<>31){
                            $egreso  = $egreso + $v->total;
                        }elseif(($v->conceptopago_id==14 || $v->conceptopago_id==16 || $v->conceptopago_id==18 || $v->conceptopago_id==20 || $v->conceptopago_id==31) && $v->situacion2=='C'){
                            $egreso  = $egreso + $v->total;
                        }
                    }
                }
            }
            $paramPaginacion = $clsLibreria->generarPaginacion($lista, $pagina, $filas, $entidad);
            $paginacion      = $paramPaginacion['cadenapaginacion'];
            $inicio          = $paramPaginacion['inicio'];
            $fin             = $paramPaginacion['fin'];
            $paginaactual    = $paramPaginacion['nuevapagina'];
            $lista           = $resultado->paginate($filas);
            $request->replace(array('page' => $paginaactual));
            return view($this->folderview.'.list')->with(compact('lista', 'paginacion', 'inicio', 'fin', 'entidad', 'cabecera', 'titulo_modificar', 'titulo_eliminar', 'ruta', 'conceptopago_id', 'titulo_registrar', 'titulo_apertura', 'titulo_cierre', 'titulo_ticketspendientes', 'titulo_cuentaspendientes', 'ingreso', 'egreso', 'titulo_anular', 'garantia', 'efectivo', 'visa', 'master', 'listapendiente', 'user', 'tipousuario', 'caja_id'));
        }
        return view($this->folderview.'.list')->with(compact('lista', 'entidad', 'conceptopago_id', 'titulo_registrar', 'titulo_apertura', 'titulo_cierre', 'titulo_ticketspendientes', 'titulo_cuentaspendientes', 'ruta', 'ingreso', 'egreso','visa', 'master', 'tipousuario', 'caja_id'));
    }

    public function index(Request $request)
    {
        $entidad          = 'Caja';
        $title            = $this->tituloAdmin;
        $ruta             = $this->rutas;
        $cboCaja          = array();
        $user = Auth::user();
        $sucursal_id = Session::get('sucursal_id');
        $rs        = Caja::where('id','<>',6)->where('id','<>',7)->where('sucursal_id', '=', $sucursal_id)->orderBy('nombre','ASC')->get();
        $caja=0;
        foreach ($rs as $key => $value) {
            $cboCaja = $cboCaja + array($value->id => $value->nombre);
            if($request->ip()==$value->ip){
                $caja=$value->id;
                //$serie=$value->serie;
            }
        }
        if($user->usertype_id==23){
            $caja=1;
            if($sucursal_id == 2) {
                $caja=2;
            }            
        }
        if($user->usertype_id==11){
            $caja=3;
            if($sucursal_id == 2) {
                $caja=4;
            }            
        }
        return view($this->folderview.'.admin')->with(compact('entidad', 'title', 'ruta', 'cboCaja', 'user', 'caja'));
    }

    public function create(Request $request)
    {
        $listar              = Libreria::getParam($request->input('listar'), 'NO');
        $entidad             = 'Caja';$cboConcepto = array();
        $rs = Conceptopago::where(DB::raw('1'),'=','1')->where('tipo','LIKE','I')->where('id','<>','1')->where('id','<>',6)->where('id','<>',13)->where('id','<>',15)->where('id','<>',17)->where('id','<>',19)->where('id','<>',21)->where('id','<>',23)->where('id','<>',32)->where('id','<>',3)->where('admision','like','S')->orderBy('nombre','ASC')->get();
        foreach ($rs as $key => $value) {
            $cboConcepto = $cboConcepto + array($value->id => $value->nombre);
        }
        $caja = null;
        $cboTipoDoc = array();
        $rs = Tipodocumento::where(DB::raw('1'),'=','1')->where('tipomovimiento_id','=',2)->orderBy('nombre','DESC')->get();
        foreach ($rs as $key => $value) {
            $cboTipoDoc = $cboTipoDoc + array($value->id => $value->nombre);
        }
        
        $formData            = array('caja.store');
        $caja2                = Caja::find($request->input('caja_id'));
        $cboCaja = array();
        $rs = Caja::where(DB::raw('1'),'=','1')->where('id','<>',6)->where('id','<>',7)->where('id','<>',$request->input('caja_id'))->orderBy('nombre','ASC')->get();
        foreach ($rs as $key => $value) {
            $cboCaja = $cboCaja + array($value->id => $value->nombre);
        }
        $cboTipo = array('RH' => 'RH', 'FT' => 'FT', 'BV' => 'BV', 'TK' => 'TK', 'VR' => 'VR');

        //sucursal_id
        $sucursal_id = Session::get('sucursal_id');

        $numero              = Movimiento::NumeroSigue($caja2->id,$sucursal_id,2,2);//movimiento caja y documento ingreso
        $formData            = array('route' => $formData, 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');

        $boton               = 'Registrar '.$caja2->nombre; 
        return view($this->folderview.'.mant')->with(compact('caja', 'formData', 'entidad', 'boton', 'listar', 'cboTipoDoc', 'caja2', 'numero', 'cboConcepto', 'cboCaja', 'user', 'cboTipo'));
    }

    public function store(Request $request)
    {
        $listar     = Libreria::getParam($request->input('listar'), 'NO');
        $reglas     = array(
                'total'          => 'required',
                );
        $mensajes = array(
            'total.required'         => 'Debe tener un monto',
        );
        $validacion = Validator::make($request->all(), $reglas, $mensajes);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        }
        $user = Auth::user();

        //sucursal_id
        $sucursal_id = Session::get('sucursal_id');

        $error = DB::transaction(function() use($request,$user,$sucursal_id){
            $movimiento        = new Movimiento();
            $movimiento->sucursal_id = $sucursal_id;
            $movimiento->fecha = date("Y-m-d H:i:s");
            $movimiento->numero= $request->input('numero');
            $movimiento->responsable_id=$user->person_id;
            if($request->input('concepto')==7 || $request->input('concepto')==8 || $request->input('concepto')==14 || $request->input('concepto')==20 || $request->input('concepto')==45){
                $movimiento->persona_id=$request->input('doctor_id');    
            }elseif($request->input('concepto')==16){//TRANSFERENCIA SOCIO
                $movimiento->persona_id=$request->input('socio_id');
            }else{
                $movimiento->persona_id=$request->input('person_id');    
            }
            $movimiento->subtotal=0;
            $movimiento->igv=0;
            $movimiento->total=str_replace(",","",$request->input('total')); 
            $movimiento->totalpagado = $request->input('total');
            $movimiento->tipomovimiento_id=2;
            $movimiento->tipodocumento_id=$request->input('tipodocumento');
            $movimiento->conceptopago_id=$request->input('concepto');
            $movimiento->comentario=$request->input('comentario');
            $movimiento->caja_id=$request->input('caja_id');
            $movimiento->situacion='N';
            //Diferenciar otros ingresos y egresos
            $movimiento->situacion2='Q';
            $movimiento->listapago=$request->input('lista');//Lista de pagos para transferencia y pago tambien
            if($request->input('concepto')==10 || $request->input('concepto')==16){//GARANTIA Y TRANSFERENCIA SOCIO
                $movimiento->doctor_id=$request->input('doctor_id');
            }
            //if($request->input('concepto')==11 || $request->input('concepto')==28 || $request->input('concepto')==9 || $request->input('concepto')==30 || $request->input('concepto')==5 || $request->input('concepto')==22 || $request->input('concepto')==27){
            //if($request->input('tipodocumento')=="3"){
                if($request->input('tipo')=='VR'){
                    $movimiento->voucher=$request->input('numero');
                }else{
                    $movimiento->voucher=$request->input('rh');
                }
                $movimiento->formapago=$request->input('tipo');
            //}
            $movimiento->save();
            $idref=$movimiento->id;
            if($request->input('concepto')==7 || $request->input('concepto')==16 || $request->input('concepto')==14 || $request->input('concepto')==18 || $request->input('concepto')==20 || $request->input('concepto')==31){//Transferencia de Caja y Socio y Tarjeta y atencion por convenio y boleteo y farmacia
                $caja = Caja::find($request->input('caja_id'));
                $movimiento        = new Movimiento();
                $movimiento->sucursal_id = $sucursal_id;
                $movimiento->fecha = date("Y-m-d H:i:s");
                $numero              = Movimiento::NumeroSigue($caja->id,$sucursal_id,2,2);
                $movimiento->numero= $numero;
                $movimiento->responsable_id=$user->person_id;
                if($request->input('concepto')==7 || $request->input('concepto')==14 || $request->input('concepto')==20){//caja y tarjeta y boleteo
                    $movimiento->persona_id=$request->input('doctor_id');
                }elseif($request->input('concepto')==16){//socio
                    $movimiento->persona_id=$request->input('socio_id');
                }elseif($request->input('concepto')==18){//atencion por convenio
                    $movimiento->persona_id=$request->input('person_id');
                }elseif($request->input('concepto')==31){//transferencia farmacia
                    $movimiento->persona_id=$request->input('person_id');
                }
                $movimiento->subtotal=0;
                $movimiento->igv=0;
                $movimiento->total=str_replace(",","",$request->input('total'));
                $movimiento->totalpagado = $request->input('total');
                $movimiento->tipomovimiento_id=2;
                $movimiento->tipodocumento_id=2;//Ingreso
                if($request->input('concepto')==7){//caja
                    $movimiento->conceptopago_id=6;
                }elseif($request->input('concepto')==16){//socio
                    $movimiento->conceptopago_id=17;
                }elseif($request->input('concepto')==14){//tarjeta
                    $movimiento->conceptopago_id=15;
                }elseif($request->input('concepto')==18){//atencion por convenio
                    $movimiento->conceptopago_id=19;
                }elseif($request->input('concepto')==20){//boleteo total
                    $movimiento->conceptopago_id=21;
                }elseif($request->input('concepto')==31){//transferencia farmacia
                    $movimiento->conceptopago_id=32;
                }
                $movimiento->comentario="Envio de caja ".$caja->nombre." por ".$request->input('comentario');
                $movimiento->caja_id=$request->input('caja');
                $movimiento->situacion='P';//PENDIENTE
                $movimiento->movimiento_id=$idref;
                $movimiento->listapago=$request->input('lista');//Lista de pagos para transferencia y pago tambien
                $movimiento->save();  

                if ($request->input('concepto')==31) {
                    if ($request->session()->get('carritoventa') !== null) {
                        $lista = $request->session()->get('carritoventa');
                        for ($i=0; $i < count($lista) ; $i++) { 
                            $venta = Movimiento::find($lista[$i]['venta_id']);
                            $venta->formapago = 'C';
                            $venta->movimientodescarga_id = $movimiento->id;
                            $venta->save();
                        }
                    }
                } 
                
                $arr=explode(",",$request->input('lista'));
                if($request->input('concepto')==7){//CAJA
                    for($c=0;$c<count($arr);$c++){
                        $Detalle = Detallemovcaja::find($arr[$c]);
                        $Detalle->situacion='T';//transferencia;
                        $Detalle->save();
                    }
                }elseif($request->input('concepto')==16){//SOCIO
                    for($c=0;$c<count($arr);$c++){
                        $Detalle = Detallemovcaja::find($arr[$c]);
                        $Detalle->medicosocio_id = $request->input('socio_id');
                        $Detalle->situacionsocio = 'P'; //PENDIENTE DE CONFIRMAR
                        $Detalle->pagosocio = $request->input('txtPago'.$arr[$c]);
                        $Detalle->save();
                    }
                }elseif($request->input('concepto')==14){//TARJETA
                    for($c=0;$c<count($arr);$c++){
                        $Detalle = Detallemovcaja::find($arr[$c]);
                        $Detalle->situaciontarjeta = 'P'; //PENDIENTE DE CONFIRMAR
                        $Detalle->pagotarjeta = $request->input('txtPago'.$arr[$c]);
                        $Detalle->save();
                    }
                }elseif($request->input('concepto')==20){//BOLETEO TOTAL
                    for($c=0;$c<count($arr);$c++){
                        $Detalle = Detallemovcaja::find($arr[$c]);
                        $Detalle->situaciontarjeta = 'P'; //PENDIENTE DE CONFIRMAR
                        $Detalle->pagotarjeta = $request->input('txtPago'.$arr[$c]);
                        $Detalle->save();
                    }
                }
            }
            if($request->input('concepto')==8 || $request->input('concepto')==45){//Pago a doctor
                $arr=explode(",",$request->input('lista'));
                for($c=0;$c<count($arr);$c++){
                    $Detalle = Detallemovcaja::find($arr[$c]);
                    $Detalle->situacion='P';//pagado;
                    $Detalle->recibo=$request->input('txtRecibo'.$arr[$c]);
                    $Detalle->save();
                }
            }
        });
        return is_null($error) ? "OK" : $error;
    }

    public function show($id)
    {
        //
    }

    public function edit($id, Request $request)
    {
        $existe = Libreria::verificarExistencia($id, 'Caja');
        if ($existe !== true) {
            return $existe;
        }
        $listar              = Libreria::getParam($request->input('listar'), 'NO');
        $Caja = Caja::find($id);
        $entidad             = 'Caja';
        $formData            = array('Caja.update', $id);
        $cboTipoPaciente     = array("Particular" => "Particular", "Convenio" => "Convenio", "Hospital" => "Hospital");
        $formData            = array('route' => $formData, 'method' => 'PUT', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton               = 'Modificar';
        return view($this->folderview.'.mant')->with(compact('Caja', 'formData', 'entidad', 'boton', 'listar', 'cboTipoPaciente'));
    }

    public function update(Request $request, $id)
    {
        $existe = Libreria::verificarExistencia($id, 'Caja');
        if ($existe !== true) {
            return $existe;
        }
        $reglas     = array(
                'nombre'                  => 'required|max:100',
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
            $categoria->save();
        });
        return is_null($error) ? "OK" : $error;
    }

    public function destroy($id)
    {
        $existe = Libreria::verificarExistencia($id, 'movimiento');
        if ($existe !== true) {
            return $existe;
        }
        $error = DB::transaction(function() use($id){
            $Caja = Movimiento::find($id);
            $Caja->situacion="A";//Anulado
            $Caja->save();
            if($Caja->listapago!=""){
                $arr=explode(",",$Caja->listapago);
                for($c=0;$c<count($arr);$c++){
                    $Detalle = Detallemovcaja::find($arr[$c]);
                    $Detalle->situacion='N';
                    if($Caja->conceptopago_id==6){//CAJA
                        $Detalle->situacion='N';//normal;
                    }elseif($Caja->conceptopago_id==16){//SOCIO
                        $Detalle->situacionsocio=null;//null
                        $Detalle->situaciontarjeta=null;//null
                        $Detalle->medicosocio_id=null;//null
                    }elseif($Caja->conceptopago_id==14 || $Caja->conceptopago_id==20){//TARJETA Y BOLETEO TOTAL
                        $Detalle->situaciontarjeta=null;//null
                    }elseif($Caja->conceptopago_id==24){//CONVENIO
                        $Detalle->situacionentrega=null;//null
                    }
                    $Detalle->save();
                }
            }

            if($Caja->conceptopago_id==7 || $Caja->conceptopago_id==14 || $Caja->conceptopago_id==16 || $Caja->conceptopago_id==18 || $Caja->conceptopago_id==20){//TRANSFERENCIA DE CAJA
                $rs = Movimiento::where('movimiento_id','=',$id)->first();
                $Caja2 = Movimiento::find($rs->id);
                $Caja2->situacion="A";//Anulado
                $Caja2->save();                
            }
        });
        return is_null($error) ? "OK" : $error;
    }

    public function eliminar($id, $listarLuego)
    {
        $existe = Libreria::verificarExistencia($id, 'movimiento');
        if ($existe !== true) {
            return $existe;
        }
        $listar = "NO";
        if (!is_null(Libreria::obtenerParametro($listarLuego))) {
            $listar = $listarLuego;
        }
        $modelo   = Caja::find($id);
        $obj = Movimiento::find($id);
        //SOLO PARA CUOTAS
        if($obj->situacion2 == 'Z') {
            $cuota = Movimiento::find($obj->numeroserie2);
            $cuota->situacion = 'A';
            $cuota->save();
            //AUMENTO RESUMEN DE CUOTAS DE LA ANULADA
            $rescuotas = Movimiento::find($cuota->movimiento_id);
            $rescuotas->total -= $obj->totalpagado - $obj->totalpagadovisa - $obj->totalpagadomaster;
            $rescuotas->totalpagado -= $obj->totalpagado;
            $rescuotas->totalpagadovisa -= $obj->totalpagadovisa;
            $rescuotas->totalpagadomaster -= $obj->totalpagadomaster;
            $rescuotas->save(); 
        }
        $entidad  = 'Caja';
        $ticket  = 'Ticket';
        $formData = array('route' => array('caja.destroy', $id), 'method' => 'DELETE', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Anular';        
        return view('app.confirmar2')->with(compact('modelo', 'id', 'formData', 'entidad', 'boton', 'listar', 'ticket'));
    }
    

   	public function pdfCierre(Request $request){
        $caja                = Caja::find($request->input('caja_id'));
        $caja_id          = Libreria::getParam($request->input('caja_id'),'1');
        //sucursal_id
        $sucursal_id = Session::get('sucursal_id');
        $rst  = Movimiento::where('sucursal_id','=',$sucursal_id)->where('tipomovimiento_id','=',2)->where('caja_id','=',$caja_id)->orderBy('movimiento.id','DESC')->limit(1)->first();
        if(count($rst)==0){
            $conceptopago_id=2;
        }else{
            $conceptopago_id=$rst->conceptopago_id;
        }
        
        $rst              = Movimiento::where('sucursal_id','=',$sucursal_id)->where('tipomovimiento_id','=',2)->where('caja_id','=',$caja_id)->where('conceptopago_id','=',1)->orderBy('id','DESC')->limit(1)->first();
        if(count($rst)>0){
            $movimiento_mayor = $rst->id;    
        }else{
            $movimiento_mayor = 0;
        }
        
        $resultado        = Movimiento::leftjoin('person as paciente', 'paciente.id', '=', 'movimiento.persona_id')
                            ->join('person as responsable', 'responsable.id', '=', 'movimiento.responsable_id')
                            ->join('conceptopago','conceptopago.id','=','movimiento.conceptopago_id')
                            ->leftjoin('movimiento as m2','m2.movimiento_id','=','movimiento.id')
                            ->where('movimiento.caja_id', '=', $caja_id)
                            ->where('movimiento.id', '>=', $movimiento_mayor)
                            ->where('movimiento.sucursal_id','=',$sucursal_id);
        $resultado        = $resultado->select('movimiento.*','m2.situacion as situacion2')->orderBy('movimiento.id', 'desc');
        $lista            = $resultado->get();
        if (count($lista) > 0) {            
            $pdf = new TCPDF();

            $pdf::setFooterCallback(function($pdf) {
                $pdf->SetY(-15);
                // Set font
                $pdf->SetFont('helvetica', 'I', 8);
                // Page number
                $pdf->Cell(0, 10, 'Pag. '.$pdf->getAliasNumPage().'/'.$pdf->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');

        });

            $pdf::SetTitle('Cierre de '.$caja->nombre);
            $pdf::AddPage('L');
            $pdf::SetFont('helvetica','B',12);
            $pdf::Cell(0,10,utf8_decode("Cierre de ".$caja->nombre),0,0,'C');
            $pdf::Ln();
            $pdf::SetFont('helvetica','B',9);
            $pdf::Cell(18,7,utf8_decode("FECHA"),1,0,'C');
            $pdf::Cell(13,7,utf8_decode("NRO"),1,0,'C');
            $pdf::Cell(16,7,utf8_decode("TIPO"),1,0,'C');
            $pdf::Cell(38,7,utf8_decode("CONCEPTO"),1,0,'C');
            $pdf::Cell(40,7,utf8_decode("PERSONA"),1,0,'C');
            $pdf::Cell(18,7,utf8_decode("INGRESO"),1,0,'C');
            $pdf::Cell(18,7,utf8_decode("EGRESO"),1,0,'C');
            $pdf::Cell(20,7,utf8_decode("TARJETA"),1,0,'C');
            $pdf::Cell(55,7,utf8_decode("COMENTARIO"),1,0,'C');
            $pdf::Cell(22,7,utf8_decode("USUARIO"),1,0,'C');
            $pdf::Cell(20,7,utf8_decode("SITUACION"),1,0,'C');
            $pdf::Ln();
            $ingreso=0;$egreso=0;$garantia=0;$efectivo=0;$visa=0;$master=0;
            foreach ($lista as $key => $value){
                    
                $pdf::SetFont('helvetica','',7.8);
                $pdf::Cell(18,7,utf8_decode($value->fecha),1,0,'C');
                $pdf::Cell(13,7,utf8_decode($value->numero),1,0,'C');
                $pdf::Cell(16,7,utf8_decode($value->conceptopago->tipo=="I"?"INGRESO":"EGRESO"),1,0,'C');
                if(strlen($value->conceptopago->nombre)>30){
                    $x=$pdf::GetX();
                    $y=$pdf::GetY();
                    $pdf::Multicell(38,3,utf8_decode($value->conceptopago->nombre),0,'C');
                    $pdf::SetXY($x,$y);
                    $pdf::Cell(38,7,"",1,0,'C');
                }else{
                    $pdf::Cell(38,7,utf8_decode($value->conceptopago->nombre),1,0,'C');
                }
                if(strlen($value->persona->apellidopaterno.' '.$value->persona->apellidomaterno.' '.$value->persona->nombres)>22){
                    $x=$pdf::GetX();
                    $y=$pdf::GetY();                    
                    $pdf::Multicell(40,3,($value->persona->apellidopaterno.' '.$value->persona->apellidomaterno.' '.$value->persona->nombres),0,'L');
                    $pdf::SetXY($x,$y);
                    $pdf::Cell(40,7,"",1,0,'C');
                }else{
                    $pdf::Cell(40,7,($value->persona->apellidopaterno.' '.$value->persona->apellidomaterno.' '.$value->persona->nombres),1,0,'C');    
                }
                if($value->situacion<>'R' && $value->situacion2<>'R'){
                    if($value->conceptopago->tipo=="I"){
                        $pdf::Cell(18,7,number_format($value->total,2,'.',''),1,0,'C');
                        $pdf::Cell(18,7,utf8_decode("0.00"),1,0,'C');
                    }else{
                        $pdf::Cell(18,7,utf8_decode("0.00"),1,0,'C');
                        $pdf::Cell(18,7,number_format($value->total,2,'.',''),1,0,'C');
                    }
                }else{
                    $pdf::Cell(18,7,utf8_decode(" - "),1,0,'C');
                    $pdf::Cell(18,7,utf8_decode(" - "),1,0,'C');
                }
                if($value->conceptopago_id<>2 && $value->situacion<>'A'){
                    if($value->conceptopago->tipo=="I"){
                        if($value->conceptopago_id<>10){//GARANTIA
                            if($value->conceptopago_id<>6){
                                $ingreso = $ingreso + $value->total;    
                            }elseif($value->conceptopago_id==6 && $value->situacion=='C'){
                                $ingreso = $ingreso + $value->total;    
                            }
                        }else{
                            $garantia = $garantia + $value->total;
                        }
                        if($value->tipotarjeta=='VISA'){
                                $visa = $visa + $value->total;
                        }elseif($value->tipotarjeta==''){
                            $efectivo = $efectivo + $value->total;
                        }else{
                            $master = $master + $value->total;
                        }
                    }else{
                        if($value->conceptopago_id<>7){
                            $egreso  = $egreso + $value->total;
                        }elseif($value->conceptopago_id==7 && $value->situacion2=='C'){
                            $egreso  = $egreso + $value->total;
                        }
                    }
                }
                
                if($value->tipotarjeta!=""){
                    $pdf::Cell(20,7,utf8_decode($value->tipotarjeta - $value->tarjeta),1,0,'C');
                }else{
                    $pdf::Cell(20,7,utf8_decode(" - "),1,0,'C');
                }
                 if(strlen($value->comentario)>27){
                    $x=$pdf::GetX();
                    $y=$pdf::GetY();                    
                    $pdf::Multicell(55,3,utf8_decode($value->comentario),0,'L');
                    $pdf::SetXY($x,$y);
                    $pdf::Cell(55,7,"",1,0,'C');
                }else{
                    $pdf::Cell(55,7,utf8_decode($value->comentario),1,0,'L');    
                }
                if(strlen($value->responsable->nombres)>25){
                    $x=$pdf::GetX();
                    $y=$pdf::GetY();                    
                    $pdf::Multicell(22,3,($value->responsable->nombres),0,'L');
                    $pdf::SetXY($x,$y);
                    $pdf::Cell(22,7,"",1,0,'C');
                }else{
                    $pdf::Cell(22,7,($value->responsable->nombres),1,0,'L');    
                }
                $color="";
                $titulo="Ok";
                if($value->conceptopago_id==7 || $value->conceptopago_id==6){
                    if($value->conceptopago_id==7){//TRANSFERENCIA EGRESO CAJA QUE ENVIA
                        if($value->situacion2=='P'){//PENDIENTE
                            $color='background:rgba(255,235,59,0.76)';
                            $titulo="Pendiente";
                        }elseif($value->situacion2=='R'){
                            $color='background:rgba(215,57,37,0.50)';
                            $titulo="Rechazado";
                        }elseif($value->situacion2=='C'){
                            $color='background:rgba(10,215,37,0.50)';
                            $titulo="Aceptado";
                        }elseif($value->situacion2=='A'){
                            $color='background:rgba(215,57,37,0.50)';
                            $titulo='Anulado'; 
                        }    
                    }else{
                        if($value->situacion=='P'){
                            $color='background:rgba(255,235,59,0.76)';
                            $titulo="Pendiente";
                        }elseif($value->situacion=='R'){
                            $color='background:rgba(215,57,37,0.50)';
                            $titulo="Rechazado";
                        }elseif($value->situacion=="C"){
                            $color='background:rgba(10,215,37,0.50)';
                            $titulo="Aceptado";
                        }elseif($value->situacion=='A'){
                            $color='background:rgba(215,57,37,0.50)';
                            $titulo='Anulado'; 
                        } 
                    }
                }else{
                    $color=($value->situacion=='A')?'background:rgba(215,57,37,0.50)':'';
                    $titulo=($value->situacion=='A')?'Anulado':'Ok';            
                }
                $pdf::Cell(20,7,utf8_decode($titulo),1,0,'C');
                $pdf::Ln();
            }
            $pdf::Ln();
            $pdf::SetFont('helvetica','B',9);
            $pdf::Cell(120,7,utf8_decode(""),0,0,'C');
            $pdf::Cell(50,7,utf8_decode("RESUMEN DE CAJA"),1,0,'C');
            $pdf::Ln();
            $pdf::Cell(120,7,utf8_decode(""),0,0,'C');
            $pdf::Cell(30,7,utf8_decode("INGRESOS :"),1,0,'L');
            $pdf::Cell(20,7,number_format($ingreso,2,'.',''),1,0,'R');
            $pdf::Ln();
            $pdf::Cell(120,7,utf8_decode(""),0,0,'C');
            $pdf::Cell(30,7,utf8_decode("Efectivo :"),1,0,'L');
            $pdf::Cell(20,7,number_format($efectivo,2,'.',''),1,0,'R');
            $pdf::Ln();
            $pdf::Cell(120,7,utf8_decode(""),0,0,'C');
            $pdf::Cell(30,7,utf8_decode("Master :"),1,0,'L');
            $pdf::Cell(20,7,number_format($master,2,'.',''),1,0,'R');
            $pdf::Ln();
            $pdf::Cell(120,7,utf8_decode(""),0,0,'C');
            $pdf::Cell(30,7,utf8_decode("Visa :"),1,0,'L');
            $pdf::Cell(20,7,number_format($visa,2,'.',''),1,0,'R');
            $pdf::Ln();
            $pdf::Cell(120,7,utf8_decode(""),0,0,'C');
            $pdf::Cell(30,7,utf8_decode("EGRESOS :"),1,0,'L');
            $pdf::Cell(20,7,number_format($egreso,2,'.',''),1,0,'R');
            $pdf::Ln();
            $pdf::Cell(120,7,utf8_decode(""),0,0,'C');
            $pdf::Cell(30,7,utf8_decode("SALDO :"),1,0,'L');
            $pdf::Cell(20,7,number_format($ingreso - $egreso,2,'.',''),1,0,'R');
            $pdf::Ln();
            $pdf::Cell(120,7,utf8_decode(""),0,0,'C');
            $pdf::Cell(30,7,utf8_decode("GARANTIA :"),1,0,'L');
            $pdf::Cell(20,7,number_format($garantia,2,'.',''),1,0,'R');
            $pdf::Ln();
            $pdf::Output('ListaCaja.pdf');
        }
    }

    ////////////////////////////////////////////////////////////////////////

    //Caja actual
    public function pdfDetalleCierreExcel(Request $request) {
        setlocale(LC_TIME, 'spanish');
        $caja    = Caja::find($request->input('caja_id'));
        $caja_id = Libreria::getParam($request->input('caja_id'),'1');

        $user=Auth::user();
        $responsable = $user->login;

        $rst              = Movimiento::where('tipomovimiento_id','=',2)->where('caja_id','=',$caja_id)->where('conceptopago_id','=',1)->orderBy('id','DESC')->limit(1)->first();
        if(count($rst)>0){
            $movimiento_mayor = $rst->id;    
        }else{
            $movimiento_mayor = 0;
        }

        //sucursal_id
        $sucursal_id = Session::get('sucursal_id');
        $nomcierre = '';
        $nomcierre = 'Clínica Especialidades'; 
        if($sucursal_id == 1) {
            $nomcierre = 'BM Clínica de Ojos';
        }  
        if($caja->nombre == 'FARMACIA') {
            $nomcierre = 'Farmacia - ' . $nomcierre;
        } 

        $nomcierre = substr($nomcierre, 0, 20);

        //Pagos de tickets   

        $sucursal_id = Session::get('sucursal_id');
        $caja_id = $request->input('caja_id');
        $resultadoventas = Movimiento::leftjoin('movimiento as m2','movimiento.movimiento_id','=','m2.id')
                ->leftjoin('person as paciente', 'paciente.id', '=', 'movimiento.persona_id')
                //->where('movimiento.situacion','=','N')
                ->where('movimiento.ventafarmacia','=','N')
                ->where('movimiento.sucursal_id', '=', $sucursal_id)
                ->where('movimiento.id', '>=', $movimiento_mayor)
                ->where('movimiento.caja_id', '=', $caja_id);
        $resultadoventas = $resultadoventas->select('movimiento.plan_id','movimiento.doctor_id','movimiento.serie','movimiento.tipodocumento_id','movimiento.id','movimiento.comentario','movimiento.movimiento_id','movimiento.fecha','movimiento.numero','movimiento.total','movimiento.totalpagado','movimiento.totalpagadovisa','movimiento.totalpagadomaster','m2.numero as numeroticket',DB::raw('case when paciente.bussinesname is null then concat(paciente.apellidopaterno,\' \',paciente.nombres) else paciente.bussinesname end as paciente'), 'movimiento.total')->orderBy('movimiento.numero', 'asc');
        
        $listaventas           = $resultadoventas->get();

        //Solo para cuotas

        $sucursal_id = Session::get('sucursal_id');
        $caja_id = $request->input('caja_id');
        $resultadocuotas = Movimiento::leftjoin('movimiento as m2','movimiento.movimiento_id','=','m2.id')
                ->leftjoin('person as paciente', 'paciente.id', '=', 'movimiento.persona_id')
                ->where('movimiento.tipomovimiento_id', '=', 2)
                ->where('movimiento.tipodocumento_id', '=', 2)
                ->where('movimiento.situacion2','=','Z')
                //->where('movimiento.situacion','=','N')
                ->where('movimiento.sucursal_id', '=', $sucursal_id)
                ->where('movimiento.id', '>=', $movimiento_mayor)
                ->where('movimiento.caja_id', '=', $caja_id);
        $resultadocuotas = $resultadocuotas->select('movimiento.numeroserie2','movimiento.movimiento_id','movimiento.situacion','movimiento.fecha','movimiento.numero','movimiento.total','movimiento.totalpagado','movimiento.totalpagadovisa','movimiento.totalpagadomaster','m2.numero as numeroticket',DB::raw('case when paciente.bussinesname is null then concat(paciente.apellidopaterno,\' \',paciente.nombres) else paciente.bussinesname end as paciente'), 'movimiento.total')->orderBy('movimiento.numero', 'asc');
        
        $listacuotas = $resultadocuotas->get();

        //Solo para ventas de farmacia

        $listaventasfarmacia = Movimiento::leftjoin('movimiento as m2','movimiento.movimiento_id','=','m2.id')
                ->leftjoin('person as paciente', 'paciente.id', '=', 'movimiento.persona_id')
                ->leftjoin('conceptopago','conceptopago.id','=','movimiento.conceptopago_id')
                ->where('movimiento.sucursal_id', '=', $sucursal_id)
                ->where('movimiento.caja_id', '=', $caja_id)
                ->where('movimiento.id', '>=', $movimiento_mayor)
                ->where('movimiento.ventafarmacia', '=', 'S');
        $listaventasfarmacia = $listaventasfarmacia->select('movimiento.situacion','movimiento.doctor_id','movimiento.serie','movimiento.id','movimiento.nombrepaciente','movimiento.voucher','movimiento.formapago','movimiento.comentario','movimiento.fecha','movimiento.numero','movimiento.total','movimiento.totalpagado','movimiento.totalpagadovisa','movimiento.totalpagadomaster','m2.numero as numeroticket',DB::raw('case when paciente.bussinesname is null then concat(paciente.apellidopaterno,\' \',paciente.nombres) else paciente.bussinesname end as paciente'), 'movimiento.total')->orderBy('movimiento.numero', 'asc');
        
        $listaventasfarmacia = $listaventasfarmacia->get();

        //Solo para ingresos varios

        $listaingresosvarios = Movimiento::leftjoin('movimiento as m2','movimiento.movimiento_id','=','m2.id')
                ->leftjoin('person as paciente', 'paciente.id', '=', 'movimiento.persona_id')
                ->join('conceptopago','conceptopago.id','=','movimiento.conceptopago_id')
                ->where('movimiento.tipomovimiento_id', '=', 2)
                ->where('movimiento.sucursal_id', '=', $sucursal_id)
                ->where('movimiento.caja_id', '=', $caja_id)
                ->where('movimiento.id', '>=', $movimiento_mayor)
                ->where('movimiento.situacion2', '=', 'Q')
                ->where('conceptopago.tipo', '=', 'I');
        $listaingresosvarios = $listaingresosvarios->select('movimiento.situacion','movimiento.voucher','movimiento.formapago','movimiento.comentario','movimiento.fecha','movimiento.numero','movimiento.total','movimiento.totalpagado','movimiento.totalpagadovisa','movimiento.totalpagadomaster','m2.numero as numeroticket',DB::raw('case when paciente.bussinesname is null then concat(paciente.apellidopaterno,\' \',paciente.nombres) else paciente.bussinesname end as paciente'), 'conceptopago.nombre', 'movimiento.total')->orderBy('movimiento.numero', 'asc');
        
        $listaingresosvarios = $listaingresosvarios->get();

        //Solo para egresos

        $resultadoegresos        = Movimiento::leftjoin('person as paciente', 'paciente.id', '=', 'movimiento.persona_id')
            ->join('person as responsable', 'responsable.id', '=', 'movimiento.responsable_id')
            ->join('conceptopago','conceptopago.id','=','movimiento.conceptopago_id')
            ->leftjoin('movimiento as m2','m2.movimiento_id','=','movimiento.id')
            ->where('movimiento.caja_id', '=', $caja_id)
            ->where('movimiento.sucursal_id','=',$sucursal_id)
            ->where('movimiento.id', '>=', $movimiento_mayor)
            ->whereNull('movimiento.cajaapertura_id')
            ->where(function($query){
                $query
                    ->whereNotIn('movimiento.conceptopago_id',[31])
                    ->orWhere('m2.situacion','<>','R');
            })
            ->where('conceptopago.tipo', '=', 'E')
            ->where('movimiento.situacion2', '=', 'Q');

        $resultadoegresos        = $resultadoegresos->select('movimiento.*','m2.situacion as situacion2','responsable.nombres as responsable2',DB::raw('case when paciente.bussinesname is null then concat(paciente.apellidopaterno,\' \',paciente.nombres) else paciente.bussinesname end as paciente'), 'conceptopago.nombre')->orderBy('conceptopago.tipo', 'asc')->orderBy('conceptopago.orden', 'asc')->orderBy('conceptopago.id', 'asc')->orderBy('movimiento.tipotarjeta', 'asc')->orderBy('movimiento.numero', 'asc');

        $listaegresos = $resultadoegresos->get();

        //Solo para egresos por compra farmacia

        $resultadoegresoscompra        = Movimiento::leftjoin('person as paciente', 'paciente.id', '=', 'movimiento.persona_id')
                ->join('person as responsable', 'responsable.id', '=', 'movimiento.responsable_id')
                ->join('conceptopago','conceptopago.id','=','movimiento.conceptopago_id')
                ->join('tipodocumento','tipodocumento.id','=','movimiento.tipodocumento_id')
                ->leftjoin('movimiento as m2','m2.movimiento_id','=','movimiento.id')
                ->where('movimiento.caja_id', '=', $caja_id)
                ->whereNull('m2.caja_id')
                ->where('movimiento.sucursal_id','=',$sucursal_id)
                ->where('movimiento.tipomovimiento_id','=',2)
                ->where('paciente.dni','=',null)
                ->where('movimiento.id', '>', $movimiento_mayor)
                ->whereNull('movimiento.cajaapertura_id')
                ->where('conceptopago.tipo', '=', 'E');

        $resultadoegresoscompra        = $resultadoegresoscompra->select('movimiento.*','tipodocumento.abreviatura as formapago2','responsable.nombres as responsable2',DB::raw('concat(paciente.ruc,\' - \',paciente.bussinesname) as paciente'), 'conceptopago.nombre')->orderBy('conceptopago.tipo', 'asc')->orderBy('conceptopago.orden', 'asc')->orderBy('conceptopago.id', 'asc')->orderBy('movimiento.tipotarjeta', 'asc')->orderBy('movimiento.numero', 'asc');

        $listaegresoscompra = $resultadoegresoscompra->get();

        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

        
        Excel::create('ExcelReporte', function($excel) use($listaventas, $listacuotas, $listaventasfarmacia, $listaingresosvarios, $listaegresos, $listaegresoscompra, $nomcierre, $responsable, $caja, $request) {
 
            $excel->sheet("Det. Cierre " . $nomcierre, function($sheet) use($listaventas, $listacuotas, $listaventasfarmacia, $listaingresosvarios, $listaegresos, $listaegresoscompra, $nomcierre, $responsable, $caja, $request) {

                $sheet->setWidth(array(
                    'A' => 15,'B' => 40, 'C' => 5, 'D' => 10, 'E' => 35, 'F' => 50, 'G' => 10, 'H' => 10, 'I' => 10, 'J' => 10, 'K' => 10, 'L' => 15
                ));

                /*$sheet->cells('A:L', function ($cells) {
                    $cells->setAlignment('center');
                    $cells->setValignment('center');
                });*/

                $sheet->setStyle(array(
                    'font' => array(
                        'name'      =>  'Calibri',
                        'size'      =>  8
                    )
                ));

                $totalvisa     = 0;
                $totalmaster   = 0;
                $totalefectivo = 0;
                $totalegresos  = 0;
                $subtotalegresos = 0;
                $subtotaldolares = 0;

                //Cabecera

                $cabecera1 = array();
                $cabecera1[] = "FECHA";
                $cabecera1[] = "PERSONA";
                $cabecera1[] = "NRO";
                $cabecera1[] = "";
                $cabecera1[] = "EMPRESA";
                $cabecera1[] = "CONCEPTO";
                $cabecera1[] = "PRECIO";
                $cabecera1[] = "EGRESO";
                $cabecera1[] = "INGRESO";
                $cabecera1[] = "";
                $cabecera1[] = "";
                $cabecera1[] = "DOCTOR";

                $sheet->row(1,$cabecera1);
                $sheet->mergeCells('C1:D1');
                $sheet->mergeCells('I1:K1');
                $sheet->mergeCells('A2:H2');

                $sheet->cells('A1:L3', function ($cells) {
                    $cells->setFont(array(
                        'family'     => 'Calibri',
                        'size'       => '11',
                        'bold'       =>  true
                    ));
                    $cells->setAlignment('center');
                });

                $cabecera2 = array();
                $cabecera2[] = "";
                $cabecera2[] = "";
                $cabecera2[] = "";
                $cabecera2[] = "";
                $cabecera2[] = "";
                $cabecera2[] = "";
                $cabecera2[] = "";
                $cabecera2[] = "";
                $cabecera2[] = "EFECTIVO";
                $cabecera2[] = "VISA";
                $cabecera2[] = "MASTER";
                $cabecera2[] = "";

                $sheet->row(2,$cabecera2);

                //Recorrido para tickets

                $fila = array();

                $a = 3;

                if(count($listaventas)>0){
                    $sheet->row($a, array('INGRESOS POR VENTAS'));
                    $sheet->mergeCells('A'.$a.':L'.$a);
                    $sheet->cells('A'.$a.':L'.$a, function ($cells) {
                        $cells->setFont(array(
                            'family'     => 'Calibri',
                            'size'       => '11',
                            'bold'       =>  true
                        ));
                    });
                    $a++;
                    $subtotalefectivo = 0;
                    $subtotalvisa = 0;
                    $subtotalmaster = 0;
                    foreach ($listaventas as $row) {                
                        $row2 = Movimiento::where('movimiento_id', $row['id'])->limit(1)->first();
                        $row3 = Movimiento::find($row['movimiento_id']);
                        if($row2['situacion'] != '') {
                            $detalles = Detallemovcaja::where('movimiento_id', $row3['id'])->get();  
                            $i = 0;              
                            foreach ($detalles as $detalle) {
                                $fila[] = utf8_decode($row['fecha']);
                                $fila[] = $row['paciente'];
                                $fila[] = $row->tipodocumento->abreviatura;
                                $fila[] = utf8_decode($row['serie'] .'-'. $row['numero']); 
                                if($row3['plan_id'] != '') {
                                    $fila[] = substr($row3->plan->nombre, 0, 30);
                                } else {
                                    $fila[] = '-';
                                }
                                $nomdetalle = ''; 
                                if($detalle->servicio_id == 13) {
                                    $nomdetalle .= '($) ';
                                }  
                                $nomdetalle .= $detalle->servicio->nombre;                      
                                $fila[] = substr($nomdetalle,0,42);
                                $fila[] = number_format($detalle->precio,2,'.','');                    
                                if($row2['situacion'] == 'N') {
                                    $fila[] = '';
                                    $valuetp = number_format($row2['totalpagado'],2,'.','');
                                    $valuetpv = number_format($row2['totalpagadovisa'],2,'.','');
                                    $valuetpm = number_format($row2['totalpagadomaster'],2,'.','');
                                    if($valuetp == 0){$valuetp='';}
                                    if($valuetpv == 0){$valuetpv='';}
                                    if($valuetpm == 0){$valuetpm='';}
                                    $fila[] = $valuetp;                    
                                    $fila[] = $valuetpv;
                                    $fila[] = $valuetpm;
                                } else {                                
                                    $fila[] = '';
                                    $fila[] = 'ANULADO';
                                    $fila[] = '';
                                    $fila[] = '';
                                    $sheet->mergeCells('I'.$a.':K'.($a+count($detalles)-1));
                                }
                                $fila[] = utf8_decode($detalle->persona->apellidopaterno);                           
                                $sheet->row($a, $fila);
                                $sheet->mergeCells('A'.$a.':A'.($a+count($detalles)-1));
                                $sheet->mergeCells('B'.$a.':B'.($a+count($detalles)-1));
                                $sheet->mergeCells('C'.$a.':C'.($a+count($detalles)-1));
                                $sheet->mergeCells('D'.$a.':D'.($a+count($detalles)-1));
                                $sheet->mergeCells('E'.$a.':E'.($a+count($detalles)-1));
                                $sheet->mergeCells('I'.$a.':I'.($a+count($detalles)-1));
                                $sheet->mergeCells('J'.$a.':J'.($a+count($detalles)-1));
                                $sheet->mergeCells('K'.$a.':K'.($a+count($detalles)-1));
                                $a++;
                                $i++;    
                                $fila = array();                      
                            }  
                            if($row2['situacion'] == 'N') {  
                                if ($row3->numeroserie2 != 'DOLAR') {
                                    $totalvisa += number_format($row2['totalpagadovisa'],2,'.','');
                                    $totalmaster += number_format($row2['totalpagadomaster'],2,'.','');
                                    $totalefectivo += number_format($row2['totalpagado'],2,'.','');
                                    $subtotalefectivo += number_format($row2['totalpagadovisa'],2,'.','');
                                    $subtotalvisa     += number_format($row2['totalpagadomaster'],2,'.','');
                                    $subtotalmaster   += number_format($row2['totalpagado'],2,'.','');
                                } else {
                                    $subtotaldolares += number_format($row2['total'],2,'.','');
                                }
                            }
                        }
                    } 
                    $fila[] = 'SUBTOTAL';                           
                    $fila[] = '';                           
                    $fila[] = '';                           
                    $fila[] = '';                           
                    $fila[] = '';                           
                    $fila[] = '';                           
                    $fila[] = '';                           
                    $fila[] = 0.00;                           
                    $fila[] = number_format($subtotalefectivo+$subtotalvisa+$subtotalmaster,2,'.','');  
                    $fila[] = ''; 
                    $fila[] = '';                          
                    $sheet->row($a, $fila);
                    $sheet->mergeCells('A'.$a.':G'.$a); 
                    $sheet->mergeCells('I'.$a.':K'.$a); 
                    $sheet->cells('A'.$a.':L'.$a, function ($cells) {
                        $cells->setFont(array(
                            'family'     => 'Calibri',
                            'size'       => '11',
                            'bold'       =>  true
                        ));
                    }); 
                    $sheet->cell('I'.$a, function($cell){
                        $cell->setAlignment('right');
                    });
                    $fila = array();   
                    $a++;  
                }

                //Recorrido para cuotas

                if(count($listacuotas)>0){
                    $sheet->row($a, array('INGRESOS POR CUOTAS'));
                    $sheet->mergeCells('A'.$a.':L'.$a);
                    $sheet->cells('A'.$a.':L'.$a, function ($cells) {
                        $cells->setFont(array(
                            'family'     => 'Calibri',
                            'size'       => '11',
                            'bold'       =>  true
                        ));
                    });
                    $a++;
                    $subtotalefectivo = 0;
                    $subtotalvisa = 0;
                    $subtotalmaster = 0;
                    foreach ($listacuotas as $row) { 
                        $cuota = Movimiento::find($row['numeroserie2']);
                        $fila[] = utf8_decode($row['fecha']);                           
                        $fila[] = $row['paciente'];                           
                        $fila[] = 'C';                           
                        $fila[] = utf8_decode($cuota->numero);                           
                        $fila[] = "PAGO DE CUOTA DE TICKET N° " . $row['numeroticket'];                           
                        $fila[] = '';
                        $fila[] = '';
                        $fila[] = '';

                        if($row['situacion'] == 'N') {
                            $valuetp = number_format($row['totalpagado'],2,'.','');
                            $valuetpv = number_format($row['totalpagadovisa'],2,'.','');
                            $valuetpm = number_format($row['totalpagadomaster'],2,'.','');
                            if($valuetp == 0){$valuetp='';}
                            if($valuetpv == 0){$valuetpv='';}
                            if($valuetpm == 0){$valuetpm='';}     
                            $fila[] = $valuetp;                           
                            $fila[] = $valuetpv;                           
                            $fila[] = $valuetpm;                           
                            $totalvisa += number_format($row['totalpagadovisa'],2,'.','');
                            $totalmaster += number_format($row['totalpagadomaster'],2,'.','');
                            $totalefectivo += number_format($row['totalpagado'],2,'.','');
                            $subtotalefectivo += number_format($row['totalpagadovisa'],2,'.','');
                            $subtotalvisa     += number_format($row['totalpagadomaster'],2,'.','');
                            $subtotalmaster   += number_format($row['totalpagado'],2,'.','');
                        } else {
                            $fila[] = "ANULADO";
                            $fila[] = "";
                            $fila[] = "";
                            $sheet->mergeCells('I'.$a.':K'.$a);
                        }
                        $fila[] = "-";  
                        $sheet->mergeCells('E'.$a.':F'.$a); 
                        $sheet->row($a, $fila);
                        $fila = array(); 
                        $a++;           
                    } 
                    $fila[] = 'SUBTOTAL';                           
                    $fila[] = '';                           
                    $fila[] = '';                           
                    $fila[] = '';                           
                    $fila[] = '';                           
                    $fila[] = '';                           
                    $fila[] = '';                           
                    $fila[] = 0.00;                           
                    $fila[] = number_format($subtotalefectivo+$subtotalvisa+$subtotalmaster,2,'.','');  
                    $fila[] = ''; 
                    $fila[] = '';                          
                    $sheet->row($a, $fila);
                    $sheet->mergeCells('A'.$a.':G'.$a); 
                    $sheet->mergeCells('I'.$a.':K'.$a); 
                    $sheet->cells('A'.$a.':L'.$a, function ($cells) {
                        $cells->setFont(array(
                            'family'     => 'Calibri',
                            'size'       => '11',
                            'bold'       =>  true
                        ));
                    }); 
                    $sheet->cell('I'.$a, function($cell){
                        $cell->setAlignment('right');
                    });
                    $fila = array();   
                    $a++;                                    
                } 

                //Recorrido para ventas de farmacia

                if(count($listaventasfarmacia)>0){
                    $sheet->row($a, array('INGRESOS POR VENTAS'));
                    $sheet->mergeCells('A'.$a.':L'.$a);
                    $sheet->cells('A'.$a.':L'.$a, function ($cells) {
                        $cells->setFont(array(
                            'family'     => 'Calibri',
                            'size'       => '11',
                            'bold'       =>  true
                        ));
                    });
                    $a++;
                    $subtotalefectivo = 0;
                    $subtotalvisa = 0;
                    $subtotalmaster = 0;
                    foreach ($listaventasfarmacia as $row) { 
                        $mov = Movimiento::where('movimiento_id', $row['id'])->limit(1)->first();
                        if($mov !== NULL) {
                            $fila[] = utf8_decode($row['fecha']);
                            if($row['paciente'] == '') {
                                $fila[] = $row['nombrepaciente'];
                            } else {
                                $fila[] = $row['paciente'];
                            }   
                            $fila[] = $mov->tipodocumento->abreviatura;             
                            $fila[] = utf8_decode($row['serie'] . '-' . $row['numero']);             
                            if($mov->empresa_id != '') {
                                $fila[] = $mov->empresa->bussinesname; 
                            } else {
                                $fila[] = '-';
                            }  
                            $fila[] = $mov->conceptopago->nombre.': '.$row['comentario'];
                            $fila[] = '';                           
                            $fila[] = '';  
                            $sheet->mergeCells('F'.$a.':G'.$a);
                            if($row['situacion'] == 'N') {
                                $valuetp = number_format($row['totalpagado'],2,'.','');
                                $valuetpv = number_format($row['totalpagadovisa'],2,'.','');
                                $valuetpm = number_format($row['totalpagadomaster'],2,'.','');
                                if($valuetp == 0){$valuetp='';}
                                if($valuetpv == 0){$valuetpv='';}
                                if($valuetpm == 0){$valuetpm='';}                                                     
                                $fila[] = $valuetp;                           
                                $fila[] = $valuetpv;                           
                                $fila[] = $valuetpm;
                            } else {
                                $fila[] = 'ANULADO';                           
                                $fila[] = '';                           
                                $fila[] = '';
                                $sheet->mergeCells('I'.$a.':K'.$a);
                            } 
                                
                            if($row['doctor_id'] != '') {
                                $fila[] = $row->doctor->apellidopaterno;
                            } else {
                                $fila[] = '-';
                            }  
                            if($row['situacion'] == 'N') { 
                                $totalvisa        += number_format($row['totalpagadovisa'],2,'.','');
                                $totalmaster      += number_format($row['totalpagadomaster'],2,'.','');
                                $totalefectivo    += number_format($row['totalpagado'],2,'.','');
                                $subtotalefectivo += number_format($row['totalpagadovisa'],2,'.','');
                                $subtotalvisa     += number_format($row['totalpagadomaster'],2,'.','');
                                $subtotalmaster   += number_format($row['totalpagado'],2,'.','');
                            }
                            $sheet->row($a, $fila);
                            $a++;
                            $fila = array();
                        }
                    } 
                    $fila[] = 'SUBTOTAL';                           
                    $fila[] = '';                           
                    $fila[] = '';                           
                    $fila[] = '';                           
                    $fila[] = '';                           
                    $fila[] = '';                           
                    $fila[] = '';                           
                    $fila[] = 0.00;                           
                    $fila[] = number_format($subtotalefectivo+$subtotalvisa+$subtotalmaster,2,'.','');  
                    $fila[] = ''; 
                    $fila[] = '';                          
                    $sheet->row($a, $fila);
                    $sheet->mergeCells('A'.$a.':G'.$a); 
                    $sheet->mergeCells('I'.$a.':K'.$a); 
                    $sheet->cells('A'.$a.':L'.$a, function ($cells) {
                        $cells->setFont(array(
                            'family'     => 'Calibri',
                            'size'       => '11',
                            'bold'       =>  true
                        ));
                    }); 
                    $sheet->cell('I'.$a, function($cell){
                        $cell->setAlignment('right');
                    });
                    $fila = array(); 
                    $a++;        
                } 

                //Recorrido para ingresos varios

                if(count($listaingresosvarios)>0){
                    $sheet->row($a, array('INGRESOS VARIOS'));
                    $sheet->mergeCells('A'.$a.':L'.$a);
                    $sheet->cells('A'.$a.':L'.$a, function ($cells) {
                        $cells->setFont(array(
                            'family'     => 'Calibri',
                            'size'       => '11',
                            'bold'       =>  true
                        ));
                    });
                    $a++;
                    $subtotalefectivo = 0;
                    $subtotalvisa = 0;
                    $subtotalmaster = 0;
                    foreach ($listaingresosvarios as $row) { 
                        $fila[] = utf8_decode($row['fecha']);                   
                        $fila[] = $row['paciente'];                   
                        $fila[] = $row['formapago'];                   
                        $fila[] = $row['voucher'];                   
                        $fila[] = $row['nombre'].': '.$row['comentario'];                   
                        $fila[] = '';  
                        $sheet->mergeCells('E'.$a.':G'.$a); 
                        $fila[] = '';                           
                        $fila[] = '';                 
                        if($row['situacion'] == 'N') {
                            $valuetp = number_format($row['totalpagado'],2,'.','');
                            $valuetpv = number_format($row['totalpagadovisa'],2,'.','');
                            $valuetpm = number_format($row['totalpagadomaster'],2,'.','');
                            if($valuetp == 0){$valuetp='';}
                            if($valuetpv == 0){$valuetpv='';}
                            if($valuetpm == 0){$valuetpm='';}                                                      
                            $fila[] = $valuetp;                           
                            $fila[] = $valuetpv;                           
                            $fila[] = $valuetpm;                    
                            $totalvisa += number_format($row['totalpagadovisa'],2,'.','');
                            $totalmaster += number_format($row['totalpagadomaster'],2,'.','');
                            $totalefectivo += number_format($row['totalpagado'],2,'.','');
                            $subtotalefectivo += number_format($row['totalpagadovisa'],2,'.','');
                            $subtotalvisa     += number_format($row['totalpagadomaster'],2,'.','');
                            $subtotalmaster   += number_format($row['totalpagado'],2,'.','');
                        } else {
                            $fila[] = 'ANULADO';                           
                            $fila[] = '';                           
                            $fila[] = '';
                            $sheet->mergeCells('I'.$a.':K'.$a);
                        }
                        $fila[] = '-';
                        $sheet->row($a, $fila);
                        $a++;
                        $fila = array();
                    }   
                    $fila[] = 'SUBTOTAL';                           
                    $fila[] = '';                           
                    $fila[] = '';                           
                    $fila[] = '';                           
                    $fila[] = '';                           
                    $fila[] = '';                           
                    $fila[] = '';                           
                    $fila[] = 0.00;                           
                    $fila[] = number_format($subtotalefectivo+$subtotalvisa+$subtotalmaster,2,'.','');  
                    $fila[] = ''; 
                    $fila[] = '';                          
                    $sheet->row($a, $fila);
                    $sheet->mergeCells('A'.$a.':G'.$a); 
                    $sheet->mergeCells('I'.$a.':K'.$a);
                    $sheet->cells('A'.$a.':L'.$a, function ($cells) {
                        $cells->setFont(array(
                            'family'     => 'Calibri',
                            'size'       => '11',
                            'bold'       =>  true
                        ));
                    }); 
                    $sheet->cell('I'.$a, function($cell){
                        $cell->setAlignment('right');
                    });
                    $fila = array();
                    $a++;                  
                } 

                //Recorrido para egresos

                if(count($listaegresos)>0){
                    $sheet->row($a, array('EGRESOS'));
                    $sheet->mergeCells('A'.$a.':L'.$a);
                    $sheet->cells('A'.$a.':L'.$a, function ($cells) {
                        $cells->setFont(array(
                            'family'     => 'Calibri',
                            'size'       => '11',
                            'bold'       =>  true
                        ));
                    });
                    $a++;
                    $subtotalegresos = 0;
                    foreach ($listaegresos as $row) { 
                        $fila[] = utf8_decode($row['fecha']);                   
                        $fila[] = $row['paciente'];                   
                        $fila[] = $row['formapago'];  
                        $fila[] = $row['voucher'];  
                        $fila[] = $row['nombre'].': '.$row['comentario']; 
                        $fila[] = ''; 
                        $sheet->mergeCells('E'.$a.':G'.$a);
                        $fila[] = ''; 
                        if($row['situacion'] == 'N') {
                            $fila[] = number_format($row['total'],2,'.','');
                            $fila[] = '';
                            $fila[] = '';
                            $fila[] = '';
                            $subtotalegresos += number_format($row['total'],2,'.','');
                        } else {
                            $fila[] = 'ANULADO';                           
                            $fila[] = '';                           
                            $fila[] = '';
                            $fila[] = '';
                        }  
                        $sheet->mergeCells('I'.$a.':K'.$a);
                        $fila[] = '-';
                        $sheet->row($a, $fila);
                        $a++;
                        $fila = array();              
                    }
                    $fila[] = 'SUBTOTAL';                           
                    $fila[] = '';                           
                    $fila[] = '';                           
                    $fila[] = '';                           
                    $fila[] = '';                           
                    $fila[] = '';                           
                    $fila[] = '';              
                    $fila[] = number_format($subtotalegresos,2,'.',''); 
                    $fila[] = 0.00; 
                    $fila[] = ''; 
                    $fila[] = '';                          
                    $sheet->row($a, $fila);
                    $sheet->mergeCells('A'.$a.':G'.$a); 
                    $sheet->mergeCells('I'.$a.':K'.$a);
                    $sheet->cells('A'.$a.':L'.$a, function ($cells) {
                        $cells->setFont(array(
                            'family'     => 'Calibri',
                            'size'       => '11',
                            'bold'       =>  true
                        ));
                    }); 
                    $sheet->cell('I'.$a, function($cell){
                        $cell->setAlignment('right');
                    });
                    $fila = array();
                    $a++;                
                }
                
                //Recorrido para egresos por compras farmacia

                if($caja->nombre == 'FARMACIA') {
                    if(count($listaegresoscompra)>0){
                        $sheet->row($a, array('EGRESOS POR COMPRA'));
                        $sheet->mergeCells('A'.$a.':L'.$a);
                        $sheet->cells('A'.$a.':L'.$a, function ($cells) {
                            $cells->setFont(array(
                                'family'     => 'Calibri',
                                'size'       => '11',
                                'bold'       =>  true
                            ));
                        });
                        $a++;
                        $subtotalegresoscompra = 0;
                        foreach ($listaegresoscompra as $row) { 
                            if($row['situacion2'] == null){
                                $fila[] = utf8_decode($row['fecha']);                   
                                $fila[] = $row['paciente'];                   
                                $fila[] = $row['formapago2'];  
                                $fila[] = $row['voucher'];  
                                $fila[] = $row['nombre'].': '.$row['comentario']; 
                                $fila[] = ''; 
                                $sheet->mergeCells('E'.$a.':G'.$a);
                                $fila[] = ''; 
                                if($row['situacion'] == 'N') {
                                    $fila[] = number_format($row['total'],2,'.','');
                                    $fila[] = '';
                                    $fila[] = '';
                                    $fila[] = '';
                                    $subtotalegresoscompra += number_format($row['total'],2,'.','');
                                    $subtotalegresos += number_format($row['total'],2,'.','');
                                } else {
                                    $fila[] = 'ANULADO';                           
                                    $fila[] = '';                           
                                    $fila[] = '';
                                    $fila[] = '';                                    
                                }  
                                $sheet->mergeCells('I'.$a.':K'.$a);
                                $fila[] = '-';
                                $sheet->row($a, $fila);
                                $a++;
                                $fila = array();     
                            }         
                        } 
                        $fila[] = 'SUBTOTAL';                           
                        $fila[] = '';                           
                        $fila[] = '';                           
                        $fila[] = '';                           
                        $fila[] = '';                           
                        $fila[] = '';                           
                        $fila[] = '';            
                        $fila[] = number_format($subtotalegresoscompra,2,'.',''); 
                        $fila[] = 0.00; 
                        $fila[] = ''; 
                        $fila[] = '';                          
                        $sheet->row($a, $fila);
                        $sheet->mergeCells('A'.$a.':G'.$a); 
                        $sheet->mergeCells('I'.$a.':K'.$a);
                        $sheet->cells('A'.$a.':L'.$a, function ($cells) {
                            $cells->setFont(array(
                                'family'     => 'Calibri',
                                'size'       => '11',
                                'bold'       =>  true
                            ));
                        }); 
                        $sheet->cell('I'.$a, function($cell){
                            $cell->setAlignment('right');
                        });
                        $fila = array();
                        $a++;  
                    }
                }

                $sheet->setBorder('A1:L'.($a-1), 'thin');
                $a++;

                $fila[] = 'RESPONSABLE';                           
                $fila[] = $responsable; 
                $sheet->mergeCells('B'.$a.':L'.$a);
                $sheet->cells('A'.$a.':L'.$a, function ($cells) {
                    $cells->setFont(array(
                        'family'     => 'Calibri',
                        'size'       => '11',
                        'bold'       =>  true
                    ));
                });
                $sheet->row($a, $fila);
                $fila = array();
                $a++;
                $a++; 

                $fila[] = '';                           
                $fila[] = '';                           
                $fila[] = '';                           
                $fila[] = '';                           
                $fila[] = 'RESUMEN DE CAJA';
                $sheet->mergeCells('E'.$a.':F'.$a);
                $sheet->cells('E'.$a.':F'.$a, function ($cells) {
                    $cells->setFont(array(
                        'family'     => 'Calibri',
                        'size'       => '11',
                        'bold'       =>  true
                    ));
                    $cells->setAlignment('center');
                });
                $sheet->row($a, $fila);
                $fila = array();
                $a++;

                $fila[] = '';                           
                $fila[] = '';                           
                $fila[] = '';                           
                $fila[] = '';
                $fila[] = 'INGRESOS';
                $fila[] = number_format($totalefectivo + $totalmaster + $totalvisa,2,'.','');
                $sheet->row($a, $fila);
                $fila = array();
                $a++;

                $fila[] = '';                           
                $fila[] = '';                           
                $fila[] = '';                           
                $fila[] = '';
                $fila[] = 'Efectivo';
                $fila[] = number_format($totalefectivo,2,'.','');
                $sheet->row($a, $fila);
                $fila = array();
                $a++;

                $fila[] = '';                           
                $fila[] = '';                           
                $fila[] = '';                           
                $fila[] = '';
                $fila[] = 'Master';
                $fila[] = number_format($totalmaster,2,'.','');
                $sheet->row($a, $fila);
                $fila = array();
                $a++;

                $fila[] = '';                           
                $fila[] = '';                           
                $fila[] = '';                           
                $fila[] = '';
                $fila[] = 'Visa';
                $fila[] = number_format($totalvisa,2,'.','');
                $sheet->row($a, $fila);
                $fila = array();
                $a++;

                $fila[] = '';                           
                $fila[] = '';                           
                $fila[] = '';                           
                $fila[] = '';
                $fila[] = 'EGRESOS';
                $fila[] = number_format($subtotalegresos,2,'.','');
                $sheet->row($a, $fila);
                $fila = array();
                $a++;

                $fila[] = '';                           
                $fila[] = '';                           
                $fila[] = '';                           
                $fila[] = '';
                $fila[] = 'SALDO (S/.)';
                $fila[] = number_format($totalefectivo + $totalmaster + $totalvisa - $subtotalegresos,2,'.','');
                $sheet->row($a, $fila);
                $fila = array();
                $a++;

                $fila[] = '';                           
                $fila[] = '';                           
                $fila[] = '';                           
                $fila[] = '';
                $fila[] = 'SALDO ($)';
                $fila[] = number_format($subtotaldolares,2,'.','');
                $sheet->row($a, $fila);

                $sheet->cells('E'.($a-7).':E'.$a, function ($cells) {
                    $cells->setFont(array(
                        'family'     => 'Calibri',
                        'size'       => '11',
                        'bold'       =>  true
                    ));
                });

                $sheet->setBorder('E'.($a-6).':F'.$a, 'thin');

                $sheet->cells('F'.($a-7).':F'.$a, function ($cells) {
                    $cells->setFont(array(
                        'size'       => '11',
                    ));
                });
            });
        })->export('xls');
    }

    //Consolidado
    public function pdfDetalleCierreExcelF(Request $request) {
        setlocale(LC_TIME, 'spanish');
        $caja    = Caja::find($request->input('caja_id'));
        $caja_id = Libreria::getParam($request->input('caja_id'),'1');

        $user=Auth::user();
        $responsable = $user->login;

        $fi = Libreria::getParam($request->input('fi'),'1');
        $ff = Libreria::getParam($request->input('ff'),'1');

        //sucursal_id
        $sucursal_id = Session::get('sucursal_id');
        $nomcierre = '';
        $nomcierre = 'Clínica Especialidades'; 
        if($sucursal_id == 1) {
            $nomcierre = 'BM Clínica de Ojos';
        }  
        if($caja->nombre == 'FARMACIA') {
            $nomcierre = 'Farmacia - ' . $nomcierre;
        } 

        $nomcierre = substr($nomcierre, 0, 20);

        //Pagos de tickets   

        $sucursal_id = Session::get('sucursal_id');
        $caja_id = $request->input('caja_id');
        $resultadoventas = Movimiento::leftjoin('movimiento as m2','movimiento.movimiento_id','=','m2.id')
                ->leftjoin('person as paciente', 'paciente.id', '=', 'movimiento.persona_id')
                //->where('movimiento.situacion','=','N')
                ->where('movimiento.ventafarmacia','=','N')
                ->where('movimiento.sucursal_id', '=', $sucursal_id)
                ->whereBetween('movimiento.fecha', [$fi, $ff])
                ->where('movimiento.caja_id', '=', $caja_id);
        $resultadoventas = $resultadoventas->select('movimiento.plan_id','movimiento.doctor_id','movimiento.serie','movimiento.tipodocumento_id','movimiento.id','movimiento.comentario','movimiento.movimiento_id','movimiento.fecha','movimiento.numero','movimiento.total','movimiento.totalpagado','movimiento.totalpagadovisa','movimiento.totalpagadomaster','m2.numero as numeroticket',DB::raw('case when paciente.bussinesname is null then concat(paciente.apellidopaterno,\' \',paciente.nombres) else paciente.bussinesname end as paciente'), 'movimiento.total')->orderBy('movimiento.numero', 'asc');
        
        $listaventas           = $resultadoventas->get();

        //Solo para cuotas

        $sucursal_id = Session::get('sucursal_id');
        $caja_id = $request->input('caja_id');
        $resultadocuotas = Movimiento::leftjoin('movimiento as m2','movimiento.movimiento_id','=','m2.id')
                ->leftjoin('person as paciente', 'paciente.id', '=', 'movimiento.persona_id')
                ->where('movimiento.tipomovimiento_id', '=', 2)
                ->where('movimiento.tipodocumento_id', '=', 2)
                ->where('movimiento.situacion2','=','Z')
                //->where('movimiento.situacion','=','N')
                ->where('movimiento.sucursal_id', '=', $sucursal_id)
                ->whereBetween('movimiento.fecha', [$fi, $ff])
                ->where('movimiento.caja_id', '=', $caja_id);
        $resultadocuotas = $resultadocuotas->select('movimiento.numeroserie2','movimiento.movimiento_id','movimiento.situacion','movimiento.fecha','movimiento.numero','movimiento.total','movimiento.totalpagado','movimiento.totalpagadovisa','movimiento.totalpagadomaster','m2.numero as numeroticket',DB::raw('case when paciente.bussinesname is null then concat(paciente.apellidopaterno,\' \',paciente.nombres) else paciente.bussinesname end as paciente'), 'movimiento.total')->orderBy('movimiento.numero', 'asc');
        
        $listacuotas = $resultadocuotas->get();

        //Solo para ventas de farmacia

        $listaventasfarmacia = Movimiento::leftjoin('movimiento as m2','movimiento.movimiento_id','=','m2.id')
                ->leftjoin('person as paciente', 'paciente.id', '=', 'movimiento.persona_id')
                ->leftjoin('conceptopago','conceptopago.id','=','movimiento.conceptopago_id')
                ->where('movimiento.sucursal_id', '=', $sucursal_id)
                ->where('movimiento.caja_id', '=', $caja_id)
                ->whereBetween('movimiento.fecha', [$fi, $ff])
                ->where('movimiento.ventafarmacia', '=', 'S');
        $listaventasfarmacia = $listaventasfarmacia->select('movimiento.situacion','movimiento.doctor_id','movimiento.serie','movimiento.id','movimiento.nombrepaciente','movimiento.voucher','movimiento.formapago','movimiento.comentario','movimiento.fecha','movimiento.numero','movimiento.total','movimiento.totalpagado','movimiento.totalpagadovisa','movimiento.totalpagadomaster','m2.numero as numeroticket',DB::raw('case when paciente.bussinesname is null then concat(paciente.apellidopaterno,\' \',paciente.nombres) else paciente.bussinesname end as paciente'), 'movimiento.total')->orderBy('movimiento.numero', 'asc');
        
        $listaventasfarmacia = $listaventasfarmacia->get();

        //Solo para ingresos varios

        $listaingresosvarios = Movimiento::leftjoin('movimiento as m2','movimiento.movimiento_id','=','m2.id')
                ->leftjoin('person as paciente', 'paciente.id', '=', 'movimiento.persona_id')
                ->join('conceptopago','conceptopago.id','=','movimiento.conceptopago_id')
                ->where('movimiento.tipomovimiento_id', '=', 2)
                ->where('movimiento.sucursal_id', '=', $sucursal_id)
                ->where('movimiento.caja_id', '=', $caja_id)
                ->whereBetween('movimiento.fecha', [$fi, $ff])
                ->where('movimiento.situacion2', '=', 'Q')
                ->where('conceptopago.tipo', '=', 'I');
        $listaingresosvarios = $listaingresosvarios->select('movimiento.situacion','movimiento.voucher','movimiento.formapago','movimiento.comentario','movimiento.fecha','movimiento.numero','movimiento.total','movimiento.totalpagado','movimiento.totalpagadovisa','movimiento.totalpagadomaster','m2.numero as numeroticket',DB::raw('case when paciente.bussinesname is null then concat(paciente.apellidopaterno,\' \',paciente.nombres) else paciente.bussinesname end as paciente'), 'conceptopago.nombre', 'movimiento.total')->orderBy('movimiento.numero', 'asc');
        
        $listaingresosvarios = $listaingresosvarios->get();

        //Solo para egresos

        $resultadoegresos        = Movimiento::leftjoin('person as paciente', 'paciente.id', '=', 'movimiento.persona_id')
            ->join('person as responsable', 'responsable.id', '=', 'movimiento.responsable_id')
            ->join('conceptopago','conceptopago.id','=','movimiento.conceptopago_id')
            ->leftjoin('movimiento as m2','m2.movimiento_id','=','movimiento.id')
            ->where('movimiento.caja_id', '=', $caja_id)
            ->where('movimiento.sucursal_id','=',$sucursal_id)
            ->whereBetween('movimiento.fecha', [$fi, $ff])
            ->whereNull('movimiento.cajaapertura_id')
            ->where(function($query){
                $query
                    ->whereNotIn('movimiento.conceptopago_id',[31])
                    ->orWhere('m2.situacion','<>','R');
            })
            ->where('conceptopago.tipo', '=', 'E')
            ->where('movimiento.situacion2', '=', 'Q');

        $resultadoegresos        = $resultadoegresos->select('movimiento.*','m2.situacion as situacion2','responsable.nombres as responsable2',DB::raw('case when paciente.bussinesname is null then concat(paciente.apellidopaterno,\' \',paciente.nombres) else paciente.bussinesname end as paciente'), 'conceptopago.nombre')->orderBy('conceptopago.tipo', 'asc')->orderBy('conceptopago.orden', 'asc')->orderBy('conceptopago.id', 'asc')->orderBy('movimiento.tipotarjeta', 'asc')->orderBy('movimiento.numero', 'asc');

        $listaegresos = $resultadoegresos->get();

        //Solo para egresos por compra farmacia

        $resultadoegresoscompra        = Movimiento::leftjoin('person as paciente', 'paciente.id', '=', 'movimiento.persona_id')
            ->join('person as responsable', 'responsable.id', '=', 'movimiento.responsable_id')
            ->join('conceptopago','conceptopago.id','=','movimiento.conceptopago_id')
            ->join('tipodocumento','tipodocumento.id','=','movimiento.tipodocumento_id')
            ->leftjoin('movimiento as m2','m2.movimiento_id','=','movimiento.id')
            ->where('movimiento.caja_id', '=', $caja_id)
            ->whereNull('m2.caja_id')
            ->where('movimiento.sucursal_id','=',$sucursal_id)
            ->where('movimiento.tipomovimiento_id','=',2)
            ->where('paciente.dni','=',null)
            ->whereBetween('movimiento.fecha', [$fi, $ff])
            ->whereNull('movimiento.cajaapertura_id')
            ->where('conceptopago.tipo', '=', 'E');

        $resultadoegresoscompra        = $resultadoegresoscompra->select('movimiento.*','tipodocumento.abreviatura as formapago2','responsable.nombres as responsable2',DB::raw('concat(paciente.ruc,\' - \',paciente.bussinesname) as paciente'), 'conceptopago.nombre')->orderBy('conceptopago.tipo', 'asc')->orderBy('conceptopago.orden', 'asc')->orderBy('conceptopago.id', 'asc')->orderBy('movimiento.tipotarjeta', 'asc')->orderBy('movimiento.numero', 'asc');

        $listaegresoscompra = $resultadoegresoscompra->get();

        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

        
        Excel::create('ExcelReporte', function($excel) use($listaventas, $listacuotas, $listaventasfarmacia, $listaingresosvarios, $listaegresos, $listaegresoscompra, $nomcierre, $caja, $responsable, $request) {
 
            $excel->sheet("Det. Cierre " . $nomcierre, function($sheet) use($listaventas, $listacuotas, $listaventasfarmacia, $listaingresosvarios, $listaegresos, $listaegresoscompra, $caja, $nomcierre, $responsable, $request) {

                $sheet->setWidth(array(
                    'A' => 15,'B' => 40, 'C' => 5, 'D' => 10, 'E' => 35, 'F' => 50, 'G' => 10, 'H' => 10, 'I' => 10, 'J' => 10, 'K' => 10, 'L' => 15
                ));

                /*$sheet->cells('A:L', function ($cells) {
                    $cells->setAlignment('center');
                    $cells->setValignment('center');
                });*/

                $sheet->setStyle(array(
                    'font' => array(
                        'name'      =>  'Calibri',
                        'size'      =>  8
                    )
                ));

                $totalvisa     = 0;
                $totalmaster   = 0;
                $totalefectivo = 0;
                $totalegresos  = 0;
                $subtotalegresos = 0;
                $subtotaldolares = 0;

                //Cabecera

                $cabecera1 = array();
                $cabecera1[] = "FECHA";
                $cabecera1[] = "PERSONA";
                $cabecera1[] = "NRO";
                $cabecera1[] = "";
                $cabecera1[] = "EMPRESA";
                $cabecera1[] = "CONCEPTO";
                $cabecera1[] = "PRECIO";
                $cabecera1[] = "EGRESO";
                $cabecera1[] = "INGRESO";
                $cabecera1[] = "";
                $cabecera1[] = "";
                $cabecera1[] = "DOCTOR";

                $sheet->row(1,$cabecera1);
                $sheet->mergeCells('C1:D1');
                $sheet->mergeCells('I1:K1');
                $sheet->mergeCells('A2:H2');

                $sheet->cells('A1:L3', function ($cells) {
                    $cells->setFont(array(
                        'family'     => 'Calibri',
                        'size'       => '11',
                        'bold'       =>  true
                    ));
                    $cells->setAlignment('center');
                });

                $cabecera2 = array();
                $cabecera2[] = "";
                $cabecera2[] = "";
                $cabecera2[] = "";
                $cabecera2[] = "";
                $cabecera2[] = "";
                $cabecera2[] = "";
                $cabecera2[] = "";
                $cabecera2[] = "";
                $cabecera2[] = "EFECTIVO";
                $cabecera2[] = "VISA";
                $cabecera2[] = "MASTER";
                $cabecera2[] = "";

                $sheet->row(2,$cabecera2);

                //Recorrido para tickets

                $fila = array();

                $a = 3;

                if(count($listaventas)>0){
                    $sheet->row($a, array('INGRESOS POR VENTAS'));
                    $sheet->mergeCells('A'.$a.':L'.$a);
                    $sheet->cells('A'.$a.':L'.$a, function ($cells) {
                        $cells->setFont(array(
                            'family'     => 'Calibri',
                            'size'       => '11',
                            'bold'       =>  true
                        ));
                    });
                    $a++;
                    $subtotalefectivo = 0;
                    $subtotalvisa = 0;
                    $subtotalmaster = 0;
                    foreach ($listaventas as $row) {                
                        $row2 = Movimiento::where('movimiento_id', $row['id'])->limit(1)->first();
                        $row3 = Movimiento::find($row['movimiento_id']);
                        if($row2['situacion'] != '') {
                            $detalles = Detallemovcaja::where('movimiento_id', $row3['id'])->get();  
                            $i = 0;              
                            foreach ($detalles as $detalle) {
                                $fila[] = utf8_decode($row['fecha']);
                                $fila[] = $row['paciente'];
                                $fila[] = $row->tipodocumento->abreviatura;
                                $fila[] = utf8_decode($row['serie'] .'-'. $row['numero']); 
                                if($row3['plan_id'] != '') {
                                    $fila[] = substr($row3->plan->nombre, 0, 30);
                                } else {
                                    $fila[] = '-';
                                }   
                                $nomdetalle = ''; 
                                if($detalle->servicio_id == 13) {
                                    $nomdetalle .= '($) ';
                                }  
                                $nomdetalle .= $detalle->servicio->nombre;                         
                                $fila[] = substr($nomdetalle,0,42);
                                $fila[] = number_format($detalle->precio,2,'.','');                    
                                if($row2['situacion'] == 'N') {
                                    $fila[] = '';
                                    $valuetp = number_format($row2['totalpagado'],2,'.','');
                                    $valuetpv = number_format($row2['totalpagadovisa'],2,'.','');
                                    $valuetpm = number_format($row2['totalpagadomaster'],2,'.','');
                                    if($valuetp == 0){$valuetp='';}
                                    if($valuetpv == 0){$valuetpv='';}
                                    if($valuetpm == 0){$valuetpm='';}
                                    $fila[] = $valuetp;                    
                                    $fila[] = $valuetpv;
                                    $fila[] = $valuetpm;
                                } else {                                
                                    $fila[] = '';
                                    $fila[] = 'ANULADO';
                                    $fila[] = '';
                                    $fila[] = '';
                                    $sheet->mergeCells('I'.$a.':K'.($a+count($detalles)-1));
                                }
                                $fila[] = utf8_decode($detalle->persona->apellidopaterno);                           
                                $sheet->row($a, $fila);
                                $sheet->mergeCells('A'.$a.':A'.($a+count($detalles)-1));
                                $sheet->mergeCells('B'.$a.':B'.($a+count($detalles)-1));
                                $sheet->mergeCells('C'.$a.':C'.($a+count($detalles)-1));
                                $sheet->mergeCells('D'.$a.':D'.($a+count($detalles)-1));
                                $sheet->mergeCells('E'.$a.':E'.($a+count($detalles)-1));
                                $sheet->mergeCells('I'.$a.':I'.($a+count($detalles)-1));
                                $sheet->mergeCells('J'.$a.':J'.($a+count($detalles)-1));
                                $sheet->mergeCells('K'.$a.':K'.($a+count($detalles)-1));
                                $a++;
                                $i++;    
                                $fila = array();                      
                            }  
                            if($row2['situacion'] == 'N') {   
                                if($row3->numeroserie2 != 'DOLAR') {
                                    $totalvisa += number_format($row2['totalpagadovisa'],2,'.','');
                                    $totalmaster += number_format($row2['totalpagadomaster'],2,'.','');
                                    $totalefectivo += number_format($row2['totalpagado'],2,'.','');
                                    $subtotalefectivo += number_format($row2['totalpagadovisa'],2,'.','');
                                    $subtotalvisa     += number_format($row2['totalpagadomaster'],2,'.','');
                                    $subtotalmaster   += number_format($row2['totalpagado'],2,'.','');
                                } else {
                                    $subtotaldolares += number_format($row2['total'],2,'.','');
                                }
                            }
                        }
                    } 
                    $fila[] = 'SUBTOTAL';                           
                    $fila[] = '';                           
                    $fila[] = '';                           
                    $fila[] = '';                           
                    $fila[] = '';                           
                    $fila[] = '';                           
                    $fila[] = '';                           
                    $fila[] = 0.00;                           
                    $fila[] = number_format($subtotalefectivo+$subtotalvisa+$subtotalmaster,2,'.','');  
                    $fila[] = ''; 
                    $fila[] = '';                          
                    $sheet->row($a, $fila);
                    $sheet->mergeCells('A'.$a.':G'.$a); 
                    $sheet->mergeCells('I'.$a.':K'.$a); 
                    $sheet->cells('A'.$a.':L'.$a, function ($cells) {
                        $cells->setFont(array(
                            'family'     => 'Calibri',
                            'size'       => '11',
                            'bold'       =>  true
                        ));
                    }); 
                    $sheet->cell('I'.$a, function($cell){
                        $cell->setAlignment('right');
                    });
                    $fila = array(); 
                    $a++;       
                }

                //Recorrido para cuotas

                if(count($listacuotas)>0){
                    $sheet->row($a, array('INGRESOS POR CUOTAS'));
                    $sheet->mergeCells('A'.$a.':L'.$a);
                    $sheet->cells('A'.$a.':L'.$a, function ($cells) {
                        $cells->setFont(array(
                            'family'     => 'Calibri',
                            'size'       => '11',
                            'bold'       =>  true
                        ));
                    });
                    $a++;
                    $subtotalefectivo = 0;
                    $subtotalvisa = 0;
                    $subtotalmaster = 0;
                    foreach ($listacuotas as $row) { 
                        $cuota = Movimiento::find($row['numeroserie2']);
                        $fila[] = utf8_decode($row['fecha']);                           
                        $fila[] = $row['paciente'];                           
                        $fila[] = 'C';                           
                        $fila[] = utf8_decode($cuota->numero);                           
                        $fila[] = "PAGO DE CUOTA DE TICKET N° " . $row['numeroticket'];                           
                        $fila[] = '';

                        if($row['situacion'] == 'N') {
                            $valuetp = number_format($row['totalpagado'],2,'.','');
                            $valuetpv = number_format($row['totalpagadovisa'],2,'.','');
                            $valuetpm = number_format($row['totalpagadomaster'],2,'.','');
                            if($valuetp == 0){$valuetp='';}
                            if($valuetpv == 0){$valuetpv='';}
                            if($valuetpm == 0){$valuetpm='';}
                            $fila[] = '';                           
                            $fila[] = $valuetp;                           
                            $fila[] = $valuetpv;                           
                            $fila[] = $valuetpm;                           
                            $totalvisa += number_format($row['totalpagadovisa'],2,'.','');
                            $totalmaster += number_format($row['totalpagadomaster'],2,'.','');
                            $totalefectivo += number_format($row['totalpagado'],2,'.','');
                            $subtotalefectivo += number_format($row['totalpagadovisa'],2,'.','');
                            $subtotalvisa     += number_format($row['totalpagadomaster'],2,'.','');
                            $subtotalmaster   += number_format($row['totalpagado'],2,'.','');
                        } else {
                            $fila[] = "ANULADO";
                            $fila[] = "";
                            $fila[] = "";
                            $fila[] = "";
                        } 
                        $fila[] = ""; 
                        $fila[] = "-";  
                        $sheet->mergeCells('E'.$a.':F'.$a); 
                        $sheet->row($a, $fila);
                        $fila = array(); 
                        $a++;           
                    } 
                    $fila[] = 'SUBTOTAL';                           
                    $fila[] = '';                           
                    $fila[] = '';                           
                    $fila[] = '';                           
                    $fila[] = '';                           
                    $fila[] = '';                           
                    $fila[] = '';                           
                    $fila[] = 0.00;                           
                    $fila[] = number_format($subtotalefectivo+$subtotalvisa+$subtotalmaster,2,'.','');  
                    $fila[] = ''; 
                    $fila[] = '';                          
                    $sheet->row($a, $fila);
                    $sheet->mergeCells('A'.$a.':G'.$a); 
                    $sheet->mergeCells('I'.$a.':K'.$a); 
                    $sheet->cells('A'.$a.':L'.$a, function ($cells) {
                        $cells->setFont(array(
                            'family'     => 'Calibri',
                            'size'       => '11',
                            'bold'       =>  true
                        ));
                    }); 
                    $sheet->cell('I'.$a, function($cell){
                        $cell->setAlignment('right');
                    });
                    $fila = array();   
                    $a++;                                    
                } 

                //Recorrido para ventas de farmacia

                if(count($listaventasfarmacia)>0){
                    $sheet->row($a, array('INGRESOS POR VENTAS'));
                    $sheet->mergeCells('A'.$a.':L'.$a);
                    $sheet->cells('A'.$a.':L'.$a, function ($cells) {
                        $cells->setFont(array(
                            'family'     => 'Calibri',
                            'size'       => '11',
                            'bold'       =>  true
                        ));
                    });
                    $a++;
                    $subtotalefectivo = 0;
                    $subtotalvisa = 0;
                    $subtotalmaster = 0;
                    foreach ($listaventasfarmacia as $row) { 
                        $mov = Movimiento::where('movimiento_id', $row['id'])->limit(1)->first();
                        if($mov !== NULL) {
                            $fila[] = utf8_decode($row['fecha']);
                            if($row['paciente'] == '') {
                                $fila[] = $row['nombrepaciente'];
                            } else {
                                $fila[] = $row['paciente'];
                            }   
                            $fila[] = $mov->tipodocumento->abreviatura;             
                            $fila[] = utf8_decode($row['serie'] . '-' . $row['numero']);             
                            if($mov->empresa_id != '') {
                                $fila[] = $mov->empresa->bussinesname; 
                            } else {
                                $fila[] = '-';
                            }  
                            $fila[] = $mov->conceptopago->nombre.': '.$row['comentario'];
                            $fila[] = ''; 
                            $sheet->mergeCells('F'.$a.':G'.$a);             
                            $fila[] = '';  
                            if($row['situacion'] == 'N') {
                                $valuetp = number_format($row['totalpagado'],2,'.','');
                                $valuetpv = number_format($row['totalpagadovisa'],2,'.','');
                                $valuetpm = number_format($row['totalpagadomaster'],2,'.','');
                                if($valuetp == 0){$valuetp='';}
                                if($valuetpv == 0){$valuetpv='';}
                                if($valuetpm == 0){$valuetpm='';}                                                     
                                $fila[] = $valuetp;                           
                                $fila[] = $valuetpv;                           
                                $fila[] = $valuetpm;
                            } else {
                                $fila[] = 'ANULADO';                           
                                $fila[] = '';                           
                                $fila[] = '';
                                $sheet->mergeCells('I'.$a.':K'.$a);
                            } 
                                
                            if($row['doctor_id'] != '') {
                                $fila[] = $row->doctor->apellidopaterno;
                            } else {
                                $fila[] = '-';
                            }  
                            if($row['situacion'] == 'N') { 
                                $totalvisa        += number_format($row['totalpagadovisa'],2,'.','');
                                $totalmaster      += number_format($row['totalpagadomaster'],2,'.','');
                                $totalefectivo    += number_format($row['totalpagado'],2,'.','');
                                $subtotalefectivo += number_format($row['totalpagadovisa'],2,'.','');
                                $subtotalvisa     += number_format($row['totalpagadomaster'],2,'.','');
                                $subtotalmaster   += number_format($row['totalpagado'],2,'.','');
                            }
                            $sheet->row($a, $fila);
                            $a++;
                            $fila = array();
                        }
                    } 
                    $fila[] = 'SUBTOTAL';                           
                    $fila[] = '';                           
                    $fila[] = '';                           
                    $fila[] = '';                           
                    $fila[] = '';                           
                    $fila[] = '';                           
                    $fila[] = '';                           
                    $fila[] = 0.00;                           
                    $fila[] = number_format($subtotalefectivo+$subtotalvisa+$subtotalmaster,2,'.','');  
                    $fila[] = ''; 
                    $fila[] = '';                          
                    $sheet->row($a, $fila);
                    $sheet->mergeCells('A'.$a.':G'.$a); 
                    $sheet->mergeCells('I'.$a.':K'.$a); 
                    $sheet->cells('A'.$a.':L'.$a, function ($cells) {
                        $cells->setFont(array(
                            'family'     => 'Calibri',
                            'size'       => '11',
                            'bold'       =>  true
                        ));
                    }); 
                    $sheet->cell('I'.$a, function($cell){
                        $cell->setAlignment('right');
                    });
                    $fila = array(); 
                    $a++;        
                } 

                //Recorrido para ingresos varios

                if(count($listaingresosvarios)>0){
                    $sheet->row($a, array('INGRESOS VARIOS'));
                    $sheet->mergeCells('A'.$a.':L'.$a);
                    $sheet->cells('A'.$a.':L'.$a, function ($cells) {
                        $cells->setFont(array(
                            'family'     => 'Calibri',
                            'size'       => '11',
                            'bold'       =>  true
                        ));
                    });
                    $a++;
                    $subtotalefectivo = 0;
                    $subtotalvisa = 0;
                    $subtotalmaster = 0;
                    foreach ($listaingresosvarios as $row) { 
                        $fila[] = utf8_decode($row['fecha']);                   
                        $fila[] = $row['paciente'];                   
                        $fila[] = $row['formapago'];                   
                        $fila[] = $row['voucher'];                   
                        $fila[] = $row['nombre'].': '.$row['comentario'];                   
                        $fila[] = '';  
                        $sheet->mergeCells('E'.$a.':G'.$a); 
                        $fila[] = '';                           
                        $fila[] = '';                 
                        if($row['situacion'] == 'N') {
                            $valuetp = number_format($row['totalpagado'],2,'.','');
                            $valuetpv = number_format($row['totalpagadovisa'],2,'.','');
                            $valuetpm = number_format($row['totalpagadomaster'],2,'.','');
                            if($valuetp == 0){$valuetp='';}
                            if($valuetpv == 0){$valuetpv='';}
                            if($valuetpm == 0){$valuetpm='';}                                                      
                            $fila[] = $valuetp;                           
                            $fila[] = $valuetpv;                           
                            $fila[] = $valuetpm;                    
                            $totalvisa += number_format($row['totalpagadovisa'],2,'.','');
                            $totalmaster += number_format($row['totalpagadomaster'],2,'.','');
                            $totalefectivo += number_format($row['totalpagado'],2,'.','');
                            $subtotalefectivo += number_format($row['totalpagadovisa'],2,'.','');
                            $subtotalvisa     += number_format($row['totalpagadomaster'],2,'.','');
                            $subtotalmaster   += number_format($row['totalpagado'],2,'.','');
                        } else {
                            $fila[] = 'ANULADO';                           
                            $fila[] = '';                           
                            $fila[] = '';
                            $sheet->mergeCells('I'.$a.':K'.$a);
                        }
                        $fila[] = '-';
                        $sheet->row($a, $fila);
                        $a++;
                        $fila = array();
                    }   
                    $fila[] = 'SUBTOTAL';                           
                    $fila[] = '';                           
                    $fila[] = '';                           
                    $fila[] = '';                           
                    $fila[] = '';                           
                    $fila[] = '';                           
                    $fila[] = '';                           
                    $fila[] = 0.00;                           
                    $fila[] = number_format($subtotalefectivo+$subtotalvisa+$subtotalmaster,2,'.','');  
                    $fila[] = ''; 
                    $fila[] = '';                          
                    $sheet->row($a, $fila);
                    $sheet->mergeCells('A'.$a.':G'.$a); 
                    $sheet->mergeCells('I'.$a.':K'.$a);
                    $sheet->cells('A'.$a.':L'.$a, function ($cells) {
                        $cells->setFont(array(
                            'family'     => 'Calibri',
                            'size'       => '11',
                            'bold'       =>  true
                        ));
                    }); 
                    $sheet->cell('I'.$a, function($cell){
                        $cell->setAlignment('right');
                    });
                    $fila = array();
                    $a++;                  
                } 

                //Recorrido para egresos

                if(count($listaegresos)>0){
                    $sheet->row($a, array('EGRESOS'));
                    $sheet->mergeCells('A'.$a.':L'.$a);
                    $sheet->cells('A'.$a.':L'.$a, function ($cells) {
                        $cells->setFont(array(
                            'family'     => 'Calibri',
                            'size'       => '11',
                            'bold'       =>  true
                        ));
                    });
                    $a++;
                    $subtotalegresos = 0;
                    foreach ($listaegresos as $row) { 
                        $fila[] = utf8_decode($row['fecha']);                   
                        $fila[] = $row['paciente'];                   
                        $fila[] = $row['formapago'];  
                        $fila[] = $row['voucher'];  
                        $fila[] = $row['nombre'].': '.$row['comentario']; 
                        $fila[] = ''; 
                        $fila[] = ''; 
                        $sheet->mergeCells('E'.$a.':G'.$a);
                        if($row['situacion'] == 'N') {
                            $fila[] = number_format($row['total'],2,'.','');
                            $fila[] = '';                           
                            $subtotalegresos += number_format($row['total'],2,'.','');
                        } else {
                            $fila[] = '';
                            $fila[] = 'ANULADO';                   
                        }                            
                        $fila[] = '';
                        $fila[] = '';
                        $fila[] = '-';
                        $sheet->mergeCells('I'.$a.':K'.$a);
                        $sheet->row($a, $fila);
                        $a++;
                        $fila = array();              
                    }
                    $fila[] = 'SUBTOTAL';                           
                    $fila[] = '';                           
                    $fila[] = '';                           
                    $fila[] = '';                           
                    $fila[] = '';                           
                    $fila[] = '';                           
                    $fila[] = '';
                    $fila[] = number_format($subtotalegresos,2,'.','');  
                    $fila[] = 0.00;
                    $fila[] = ''; 
                    $fila[] = '';                          
                    $sheet->row($a, $fila);
                    $sheet->mergeCells('A'.$a.':G'.$a); 
                    $sheet->mergeCells('I'.$a.':K'.$a);
                    $sheet->cells('A'.$a.':L'.$a, function ($cells) {
                        $cells->setFont(array(
                            'family'     => 'Calibri',
                            'size'       => '11',
                            'bold'       =>  true
                        ));
                    }); 
                    $sheet->cell('I'.$a, function($cell){
                        $cell->setAlignment('right');
                    });
                    $fila = array();
                    $a++;                
                }

                if($caja->nombre == 'FARMACIA') {
                    if(count($listaegresoscompra)>0){
                        $sheet->row($a, array('EGRESOS POR COMPRA'));
                        $sheet->mergeCells('A'.$a.':L'.$a);
                        $sheet->cells('A'.$a.':L'.$a, function ($cells) {
                            $cells->setFont(array(
                                'family'     => 'Calibri',
                                'size'       => '11',
                                'bold'       =>  true
                            ));
                        });
                        $a++;
                        $subtotalegresoscompra = 0;
                        foreach ($listaegresoscompra as $row) {
                            if($row['situacion2'] == null){
                                $fila[] = utf8_decode($row['fecha']);                   
                                $fila[] = $row['paciente'];                   
                                $fila[] = $row['formapago2'];  
                                $fila[] = $row['voucher'];  
                                $fila[] = $row['nombre'].': '.$row['comentario']; 
                                $fila[] = ''; 
                                $fila[] = '';
                                $sheet->mergeCells('E'.$a.':G'.$a);
                                if($row['situacion'] == 'N') {
                                    $fila[] = number_format($row['total'],2,'.','');
                                    $fila[] = '';
                                    $subtotalegresoscompra += number_format($row['total'],2,'.','');
                                    $subtotalegresos += number_format($row['total'],2,'.','');
                                } else {
                                    $fila[] = '';
                                    $fila[] = 'ANULADO';
                                } 
                                $fila[] = '';                           
                                $fila[] = ''; 
                                $fila[] = '-';
                                $sheet->mergeCells('I'.$a.':K'.$a);
                                $sheet->row($a, $fila);
                                $a++;
                                $fila = array();
                            }       
                        }  
                        $fila[] = 'SUBTOTAL';                           
                        $fila[] = '';                           
                        $fila[] = '';                           
                        $fila[] = '';                           
                        $fila[] = '';                           
                        $fila[] = '';                           
                        $fila[] = '';                           
                        $fila[] = number_format($subtotalegresoscompra,2,'.','');  
                        $fila[] = 0.00;                           
                        $fila[] = ''; 
                        $fila[] = '';                          
                        $sheet->row($a, $fila);
                        $sheet->mergeCells('A'.$a.':G'.$a); 
                        $sheet->mergeCells('I'.$a.':K'.$a);
                        $sheet->cells('A'.$a.':L'.$a, function ($cells) {
                            $cells->setFont(array(
                                'family'     => 'Calibri',
                                'size'       => '11',
                                'bold'       =>  true
                            ));
                        }); 
                        $sheet->cell('I'.$a, function($cell){
                            $cell->setAlignment('right');
                        });
                        $fila = array();
                        $a++;                
                    }
                }

                $sheet->setBorder('A1:L'.($a-1), 'thin');
                $a++;

                $fila[] = 'RESPONSABLE';                           
                $fila[] = $responsable; 
                $sheet->mergeCells('B'.$a.':L'.$a);
                $sheet->cells('A'.$a.':L'.$a, function ($cells) {
                    $cells->setFont(array(
                        'family'     => 'Calibri',
                        'size'       => '11',
                        'bold'       =>  true
                    ));
                });
                $sheet->row($a, $fila);
                $fila = array();
                $a++;
                $a++; 

                $fila[] = '';                           
                $fila[] = '';                           
                $fila[] = '';                           
                $fila[] = '';                           
                $fila[] = 'RESUMEN DE CAJA';
                $sheet->mergeCells('E'.$a.':F'.$a);
                $sheet->cells('E'.$a.':F'.$a, function ($cells) {
                    $cells->setFont(array(
                        'family'     => 'Calibri',
                        'size'       => '11',
                        'bold'       =>  true
                    ));
                    $cells->setAlignment('center');
                });
                $sheet->row($a, $fila);
                $fila = array();
                $a++;

                $fila[] = '';                           
                $fila[] = '';                           
                $fila[] = '';                           
                $fila[] = '';
                $fila[] = 'INGRESOS';
                $fila[] = number_format($totalefectivo + $totalmaster + $totalvisa,2,'.','');
                $sheet->row($a, $fila);
                $fila = array();
                $a++;

                $fila[] = '';                           
                $fila[] = '';                           
                $fila[] = '';                           
                $fila[] = '';
                $fila[] = 'Efectivo';
                $fila[] = number_format($totalefectivo,2,'.','');
                $sheet->row($a, $fila);
                $fila = array();
                $a++;

                $fila[] = '';                           
                $fila[] = '';                           
                $fila[] = '';                           
                $fila[] = '';
                $fila[] = 'Master';
                $fila[] = number_format($totalmaster,2,'.','');
                $sheet->row($a, $fila);
                $fila = array();
                $a++;

                $fila[] = '';                           
                $fila[] = '';                           
                $fila[] = '';                           
                $fila[] = '';
                $fila[] = 'Visa';
                $fila[] = number_format($totalvisa,2,'.','');
                $sheet->row($a, $fila);
                $fila = array();
                $a++;

                $fila[] = '';                           
                $fila[] = '';                           
                $fila[] = '';                           
                $fila[] = '';
                $fila[] = 'EGRESOS';
                $fila[] = number_format($subtotalegresos,2,'.','');
                $sheet->row($a, $fila);
                $fila = array();
                $a++;

                $fila[] = '';                           
                $fila[] = '';                           
                $fila[] = '';                           
                $fila[] = '';
                $fila[] = 'SALDO (S/.)';
                $fila[] = number_format($totalefectivo + $totalmaster + $totalvisa - $subtotalegresos,2,'.','');
                $sheet->row($a, $fila);
                $fila = array();
                $a++;

                $fila[] = '';                           
                $fila[] = '';                           
                $fila[] = '';                           
                $fila[] = '';
                $fila[] = 'SALDO ($)';
                $fila[] = number_format($subtotaldolares,2,'.','');
                $sheet->row($a, $fila);

                $sheet->cells('E'.($a-7).':E'.$a, function ($cells) {
                    $cells->setFont(array(
                        'family'     => 'Calibri',
                        'size'       => '11',
                        'bold'       =>  true
                    ));
                });

                $sheet->setBorder('E'.($a-7).':F'.$a, 'thin');

                $sheet->cells('F'.($a-7).':F'.$a, function ($cells) {
                    $cells->setFont(array(
                        'size'       => '11',
                    ));
                });
            });
        })->export('xls');
    }

    //Por Cajas separadas
    public function pdfDetalleCierreExcelF2(Request $request) {
        setlocale(LC_TIME, 'spanish');

        //sucursal_id
        $sucursal_id = Session::get('sucursal_id');

        $caja    = Caja::find($request->input('caja_id'));
        $caja_id = Libreria::getParam($request->input('caja_id'),'1');

        $fi = Libreria::getParam($request->input('fi'),'1');
        $ff = Libreria::getParam($request->input('ff'),'1');

        $aperturas = Movimiento::where('conceptopago_id', 1)->where('caja_id', $caja_id)->where('sucursal_id', $sucursal_id)->whereBetween('fecha', [$fi, $ff])->get();

        //Comprobamos si la ultima fecha tiene cierre

        $cierrefinal = Movimiento::where('conceptopago_id', 2)->where('caja_id', $caja_id)->where('sucursal_id', $sucursal_id)->where('fecha', '=', $ff)->get();

        $numcajas = count($aperturas);
        if(count($cierrefinal) == 0) {
            //Si no hay cierre en la ultima fecha, no considero la ultima caja
            $numcajas--;
        }

        $user=Auth::user();
        $responsable = $user->login;

        $nomcierre = '';
        $nomcierre = 'Clínica Especialidades'; 
        if($sucursal_id == 1) {
            $nomcierre = 'BM Clínica de Ojos';
        }  
        if($caja->nombre == 'FARMACIA') {
            $nomcierre = 'Farmacia - ' . $nomcierre;
        } 
        $nomcierre = substr($nomcierre, 0, 20);  

        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

        
        Excel::create('ExcelReporte', function($excel) use($aperturas, $numcajas, $nomcierre, $caja, $responsable, $request) {

            $sucursal_id = Session::get('sucursal_id');
            $caja_id = $request->input('caja_id');
            $cont = 1;
            if(count($aperturas) > 0) {
                foreach ($aperturas as $apertura) {

                    //Cierre de la presente caja
                    $cierre = Movimiento::select('id')
                            ->where('conceptopago_id', 2)
                            ->where('caja_id', $caja_id)
                            ->where('sucursal_id', $sucursal_id)
                            ->where('id' , '>', $apertura->id)
                            ->limit(1)->first();

                    //Pagos de tickets   
                    
                    $resultadoventas = Movimiento::leftjoin('movimiento as m2','movimiento.movimiento_id','=','m2.id')
                            ->leftjoin('person as paciente', 'paciente.id', '=', 'movimiento.persona_id')
                            //->where('movimiento.situacion','=','N')
                            ->where('movimiento.ventafarmacia','=','N')
                            ->where('movimiento.sucursal_id', '=', $sucursal_id)
                            ->whereBetween('movimiento.id', [$apertura->id,(int)$cierre['id']])
                            ->where('movimiento.caja_id', '=', $caja_id);
                    $resultadoventas = $resultadoventas->select('movimiento.plan_id','movimiento.doctor_id','movimiento.serie','movimiento.tipodocumento_id','movimiento.id','movimiento.comentario','movimiento.movimiento_id','movimiento.fecha','movimiento.numero','movimiento.total','movimiento.totalpagado','movimiento.totalpagadovisa','movimiento.totalpagadomaster','m2.numero as numeroticket',DB::raw('case when paciente.bussinesname is null then concat(paciente.apellidopaterno,\' \',paciente.nombres) else paciente.bussinesname end as paciente'), 'movimiento.total')->orderBy('movimiento.numero', 'asc');
                    
                    $listaventas           = $resultadoventas->get();

                    //Solo para cuotas

                    $resultadocuotas = Movimiento::leftjoin('movimiento as m2','movimiento.movimiento_id','=','m2.id')
                            ->leftjoin('person as paciente', 'paciente.id', '=', 'movimiento.persona_id')
                            ->where('movimiento.tipomovimiento_id', '=', 2)
                            ->where('movimiento.tipodocumento_id', '=', 2)
                            ->where('movimiento.situacion2','=','Z')
                            //->where('movimiento.situacion','=','N')
                            ->where('movimiento.sucursal_id', '=', $sucursal_id)
                            ->whereBetween('movimiento.id', [$apertura->id,(int)$cierre['id']])
                            ->where('movimiento.caja_id', '=', $caja_id);
                    $resultadocuotas = $resultadocuotas->select('movimiento.numeroserie2','movimiento.movimiento_id','movimiento.situacion','movimiento.fecha','movimiento.numero','movimiento.total','movimiento.totalpagado','movimiento.totalpagadovisa','movimiento.totalpagadomaster','m2.numero as numeroticket',DB::raw('case when paciente.bussinesname is null then concat(paciente.apellidopaterno,\' \',paciente.nombres) else paciente.bussinesname end as paciente'), 'movimiento.total')->orderBy('movimiento.numero', 'asc');
                    
                    $listacuotas = $resultadocuotas->get();

                    //Solo para ventas de farmacia

                    $listaventasfarmacia = Movimiento::leftjoin('movimiento as m2','movimiento.movimiento_id','=','m2.id')
                            ->leftjoin('person as paciente', 'paciente.id', '=', 'movimiento.persona_id')
                            ->leftjoin('conceptopago','conceptopago.id','=','movimiento.conceptopago_id')
                            ->where('movimiento.sucursal_id', '=', $sucursal_id)
                            ->where('movimiento.caja_id', '=', $caja_id)
                            ->whereBetween('movimiento.id', [$apertura->id,(int)$cierre['id']])
                            ->where('movimiento.ventafarmacia', '=', 'S');
                    $listaventasfarmacia = $listaventasfarmacia->select('movimiento.situacion','movimiento.doctor_id','movimiento.serie','movimiento.id','movimiento.nombrepaciente','movimiento.voucher','movimiento.formapago','movimiento.comentario','movimiento.fecha','movimiento.numero','movimiento.total','movimiento.totalpagado','movimiento.totalpagadovisa','movimiento.totalpagadomaster','m2.numero as numeroticket',DB::raw('case when paciente.bussinesname is null then concat(paciente.apellidopaterno,\' \',paciente.nombres) else paciente.bussinesname end as paciente'), 'movimiento.total')->orderBy('movimiento.numero', 'asc');
                    
                    $listaventasfarmacia = $listaventasfarmacia->get();

                    //Solo para ingresos varios

                    $listaingresosvarios = Movimiento::leftjoin('movimiento as m2','movimiento.movimiento_id','=','m2.id')
                            ->leftjoin('person as paciente', 'paciente.id', '=', 'movimiento.persona_id')
                            ->join('conceptopago','conceptopago.id','=','movimiento.conceptopago_id')
                            ->where('movimiento.tipomovimiento_id', '=', 2)
                            ->where('movimiento.sucursal_id', '=', $sucursal_id)
                            ->where('movimiento.caja_id', '=', $caja_id)
                            ->whereBetween('movimiento.id', [$apertura->id,(int)$cierre['id']])
                            ->where('movimiento.situacion2', '=', 'Q')
                            ->where('conceptopago.tipo', '=', 'I');
                    $listaingresosvarios = $listaingresosvarios->select('movimiento.situacion','movimiento.voucher','movimiento.formapago','movimiento.comentario','movimiento.fecha','movimiento.numero','movimiento.total','movimiento.totalpagado','movimiento.totalpagadovisa','movimiento.totalpagadomaster','m2.numero as numeroticket',DB::raw('case when paciente.bussinesname is null then concat(paciente.apellidopaterno,\' \',paciente.nombres) else paciente.bussinesname end as paciente'), 'conceptopago.nombre', 'movimiento.total')->orderBy('movimiento.numero', 'asc');
                    
                    $listaingresosvarios = $listaingresosvarios->get();

                    //Solo para egresos

                    $resultadoegresos        = Movimiento::leftjoin('person as paciente', 'paciente.id', '=', 'movimiento.persona_id')
                        ->join('person as responsable', 'responsable.id', '=', 'movimiento.responsable_id')
                        ->join('conceptopago','conceptopago.id','=','movimiento.conceptopago_id')
                        ->leftjoin('movimiento as m2','m2.movimiento_id','=','movimiento.id')
                        ->where('movimiento.caja_id', '=', $caja_id)
                        ->where('movimiento.sucursal_id','=',$sucursal_id)
                        ->whereBetween('movimiento.id', [$apertura->id,(int)$cierre['id']])
                        ->whereNull('movimiento.cajaapertura_id')
                        ->where(function($query){
                            $query
                                ->whereNotIn('movimiento.conceptopago_id',[31])
                                ->orWhere('m2.situacion','<>','R');
                        })
                        ->where('conceptopago.tipo', '=', 'E')
                        ->where('movimiento.situacion2', '=', 'Q');

                    $resultadoegresos        = $resultadoegresos->select('movimiento.*','m2.situacion as situacion2','responsable.nombres as responsable2',DB::raw('case when paciente.bussinesname is null then concat(paciente.apellidopaterno,\' \',paciente.nombres) else paciente.bussinesname end as paciente'), 'conceptopago.nombre')->orderBy('conceptopago.tipo', 'asc')->orderBy('conceptopago.orden', 'asc')->orderBy('conceptopago.id', 'asc')->orderBy('movimiento.tipotarjeta', 'asc')->orderBy('movimiento.numero', 'asc');

                    $listaegresos = $resultadoegresos->get();

                    //Solo para egresos por compra farmacia

                    $resultadoegresoscompra        = Movimiento::leftjoin('person as paciente', 'paciente.id', '=', 'movimiento.persona_id')
                        ->join('person as responsable', 'responsable.id', '=', 'movimiento.responsable_id')
                        ->join('conceptopago','conceptopago.id','=','movimiento.conceptopago_id')
                        ->join('tipodocumento','tipodocumento.id','=','movimiento.tipodocumento_id')
                        ->leftjoin('movimiento as m2','m2.movimiento_id','=','movimiento.id')
                        ->where('movimiento.caja_id', '=', $caja_id)
                        ->whereNull('m2.caja_id')
                        ->where('movimiento.sucursal_id','=',$sucursal_id)
                        ->where('movimiento.tipomovimiento_id','=',2)
                        ->where('paciente.dni','=',null)
                        ->whereBetween('movimiento.id', [$apertura->id,(int)$cierre['id']])
                        ->whereNull('movimiento.cajaapertura_id')
                        ->where('conceptopago.tipo', '=', 'E');

                    $resultadoegresoscompra        = $resultadoegresoscompra->select('movimiento.*','tipodocumento.abreviatura as formapago2','responsable.nombres as responsable2',DB::raw('concat(paciente.ruc,\' - \',paciente.bussinesname) as paciente'), 'conceptopago.nombre')->orderBy('conceptopago.tipo', 'asc')->orderBy('conceptopago.orden', 'asc')->orderBy('conceptopago.id', 'asc')->orderBy('movimiento.tipotarjeta', 'asc')->orderBy('movimiento.numero', 'asc');

                    $listaegresoscompra = $resultadoegresoscompra->get();
         
                    $excel->sheet($nomcierre . ' Ap. ' . $apertura->numero, function($sheet) use($listaventas, $listacuotas, $listaventasfarmacia, $listaingresosvarios, $listaegresos, $listaegresoscompra, $caja, $nomcierre, $responsable, $request) {

                        $sheet->setWidth(array(
                            'A' => 15,'B' => 40, 'C' => 5, 'D' => 10, 'E' => 35, 'F' => 50, 'G' => 10, 'H' => 10, 'I' => 10, 'J' => 10, 'K' => 10, 'L' => 15
                        ));

                        /*$sheet->cells('A:L', function ($cells) {
                            $cells->setAlignment('center');
                            $cells->setValignment('center');
                        });*/

                        $sheet->setStyle(array(
                            'font' => array(
                                'name'      =>  'Calibri',
                                'size'      =>  8
                            )
                        ));

                        $totalvisa     = 0;
                        $totalmaster   = 0;
                        $totalefectivo = 0;
                        $totalegresos  = 0;
                        $subtotalegresos = 0;
                        $subtotaldolares = 0;

                        //Cabecera

                        $cabecera1 = array();
                        $cabecera1[] = "FECHA";
                        $cabecera1[] = "PERSONA";
                        $cabecera1[] = "NRO";
                        $cabecera1[] = "";
                        $cabecera1[] = "EMPRESA";
                        $cabecera1[] = "CONCEPTO";
                        $cabecera1[] = "PRECIO";
                        $cabecera1[] = "EGRESO";
                        $cabecera1[] = "INGRESO";
                        $cabecera1[] = "";
                        $cabecera1[] = "";
                        $cabecera1[] = "DOCTOR";

                        $sheet->row(1,$cabecera1);
                        $sheet->mergeCells('C1:D1');
                        $sheet->mergeCells('I1:K1');
                        $sheet->mergeCells('A2:H2');

                        $sheet->cells('A1:L3', function ($cells) {
                            $cells->setFont(array(
                                'family'     => 'Calibri',
                                'size'       => '11',
                                'bold'       =>  true
                            ));
                            $cells->setAlignment('center');
                        });

                        $cabecera2 = array();
                        $cabecera2[] = "";
                        $cabecera2[] = "";
                        $cabecera2[] = "";
                        $cabecera2[] = "";
                        $cabecera2[] = "";
                        $cabecera2[] = "";
                        $cabecera2[] = "";
                        $cabecera2[] = "";
                        $cabecera2[] = "EFECTIVO";
                        $cabecera2[] = "VISA";
                        $cabecera2[] = "MASTER";
                        $cabecera2[] = "";

                        $sheet->row(2,$cabecera2);

                        //Recorrido para tickets

                        $fila = array();

                        $a = 3;

                        if(count($listaventas)>0){
                            $sheet->row($a, array('INGRESOS POR VENTAS'));
                            $sheet->mergeCells('A'.$a.':L'.$a);
                            $sheet->cells('A'.$a.':L'.$a, function ($cells) {
                                $cells->setFont(array(
                                    'family'     => 'Calibri',
                                    'size'       => '11',
                                    'bold'       =>  true
                                ));
                            });
                            $a++;
                            $subtotalefectivo = 0;
                            $subtotalvisa = 0;
                            $subtotalmaster = 0;
                            foreach ($listaventas as $row) {                
                                $row2 = Movimiento::where('movimiento_id', $row['id'])->limit(1)->first();
                                $row3 = Movimiento::find($row['movimiento_id']);
                                if($row2['situacion'] != '') {
                                    $detalles = Detallemovcaja::where('movimiento_id', $row3['id'])->get();  
                                    $i = 0;              
                                    foreach ($detalles as $detalle) {
                                        $fila[] = utf8_decode($row['fecha']);
                                        $fila[] = $row['paciente'];
                                        $fila[] = $row->tipodocumento->abreviatura;
                                        $fila[] = utf8_decode($row['serie'] .'-'. $row['numero']); 
                                        if($row3['plan_id'] != '') {
                                            $fila[] = substr($row3->plan->nombre, 0, 30);
                                        } else {
                                            $fila[] = '-';
                                        }
                                        $nomdetalle = ''; 
                                        if($detalle->servicio_id == 13) {
                                            $nomdetalle .= '($) ';
                                        }  
                                        $nomdetalle .= $detalle->servicio->nombre;                            
                                        $fila[] = substr($nomdetalle,0,42);
                                        $fila[] = number_format($detalle->precio,2,'.','');                    
                                        if($row2['situacion'] == 'N') {
                                            $fila[] = '';
                                            $valuetp = number_format($row2['totalpagado'],2,'.','');
                                            $valuetpv = number_format($row2['totalpagadovisa'],2,'.','');
                                            $valuetpm = number_format($row2['totalpagadomaster'],2,'.','');
                                            if($valuetp == 0){$valuetp='';}
                                            if($valuetpv == 0){$valuetpv='';}
                                            if($valuetpm == 0){$valuetpm='';}
                                            $fila[] = $valuetp;                    
                                            $fila[] = $valuetpv;
                                            $fila[] = $valuetpm;
                                        } else {                                
                                            $fila[] = '';
                                            $fila[] = 'ANULADO';
                                            $fila[] = '';
                                            $fila[] = '';
                                            $sheet->mergeCells('I'.$a.':K'.($a+count($detalles)-1));
                                        }
                                        $fila[] = utf8_decode($detalle->persona->apellidopaterno);                           
                                        $sheet->row($a, $fila);
                                        $sheet->mergeCells('A'.$a.':A'.($a+count($detalles)-1));
                                        $sheet->mergeCells('B'.$a.':B'.($a+count($detalles)-1));
                                        $sheet->mergeCells('C'.$a.':C'.($a+count($detalles)-1));
                                        $sheet->mergeCells('D'.$a.':D'.($a+count($detalles)-1));
                                        $sheet->mergeCells('E'.$a.':E'.($a+count($detalles)-1));
                                        $sheet->mergeCells('I'.$a.':I'.($a+count($detalles)-1));
                                        $sheet->mergeCells('J'.$a.':J'.($a+count($detalles)-1));
                                        $sheet->mergeCells('K'.$a.':K'.($a+count($detalles)-1));
                                        $a++;
                                        $i++;    
                                        $fila = array();                      
                                    }  
                                    if($row2['situacion'] == 'N') {
                                        if($row3->numeroserie2 != 'DOLAR') {                  
                                            $totalvisa += number_format($row2['totalpagadovisa'],2,'.','');
                                            $totalmaster += number_format($row2['totalpagadomaster'],2,'.','');
                                            $totalefectivo += number_format($row2['totalpagado'],2,'.','');
                                            $subtotalefectivo += number_format($row2['totalpagadovisa'],2,'.','');
                                            $subtotalvisa     += number_format($row2['totalpagadomaster'],2,'.','');
                                            $subtotalmaster   += number_format($row2['totalpagado'],2,'.','');
                                        } else {
                                            $subtotaldolares += number_format($row2['total'],2,'.','');
                                        }
                                    }
                                }
                            } 
                            $fila[] = 'SUBTOTAL';                           
                            $fila[] = '';                           
                            $fila[] = '';                           
                            $fila[] = '';                           
                            $fila[] = '';                           
                            $fila[] = '';                           
                            $fila[] = '';                           
                            $fila[] = 0.00;                           
                            $fila[] = number_format($subtotalefectivo+$subtotalvisa+$subtotalmaster,2,'.','');  
                            $fila[] = ''; 
                            $fila[] = '';                          
                            $sheet->row($a, $fila);
                            $sheet->mergeCells('A'.$a.':G'.$a); 
                            $sheet->mergeCells('I'.$a.':K'.$a); 
                            $sheet->cells('A'.$a.':L'.$a, function ($cells) {
                                $cells->setFont(array(
                                    'family'     => 'Calibri',
                                    'size'       => '11',
                                    'bold'       =>  true
                                ));
                            }); 
                            $sheet->cell('I'.$a, function($cell){
                                $cell->setAlignment('right');
                            });
                            $fila = array(); 
                            $a++;       
                        }

                        //Recorrido para cuotas

                        if(count($listacuotas)>0){
                            $sheet->row($a, array('INGRESOS POR CUOTAS'));
                            $sheet->mergeCells('A'.$a.':L'.$a);
                            $sheet->cells('A'.$a.':L'.$a, function ($cells) {
                                $cells->setFont(array(
                                    'family'     => 'Calibri',
                                    'size'       => '11',
                                    'bold'       =>  true
                                ));
                            });
                            $a++;
                            $subtotalefectivo = 0;
                            $subtotalvisa = 0;
                            $subtotalmaster = 0;
                            foreach ($listacuotas as $row) { 
                                $cuota = Movimiento::find($row['numeroserie2']);
                                $fila[] = utf8_decode($row['fecha']);                           
                                $fila[] = $row['paciente'];                           
                                $fila[] = 'C';                           
                                $fila[] = utf8_decode($cuota->numero);                           
                                $fila[] = "PAGO DE CUOTA DE TICKET N° " . $row['numeroticket'];                           
                                $fila[] = '';

                                if($row['situacion'] == 'N') {
                                    $valuetp = number_format($row['totalpagado'],2,'.','');
                                    $valuetpv = number_format($row['totalpagadovisa'],2,'.','');
                                    $valuetpm = number_format($row['totalpagadomaster'],2,'.','');
                                    if($valuetp == 0){$valuetp='';}
                                    if($valuetpv == 0){$valuetpv='';}
                                    if($valuetpm == 0){$valuetpm='';}
                                    $fila[] = '';                           
                                    $fila[] = $valuetp;                           
                                    $fila[] = $valuetpv;                           
                                    $fila[] = $valuetpm;                           
                                    $totalvisa += number_format($row['totalpagadovisa'],2,'.','');
                                    $totalmaster += number_format($row['totalpagadomaster'],2,'.','');
                                    $totalefectivo += number_format($row['totalpagado'],2,'.','');
                                    $subtotalefectivo += number_format($row['totalpagadovisa'],2,'.','');
                                    $subtotalvisa     += number_format($row['totalpagadomaster'],2,'.','');
                                    $subtotalmaster   += number_format($row['totalpagado'],2,'.','');
                                } else {
                                    $fila[] = "ANULADO";
                                    $fila[] = "";
                                    $fila[] = "";
                                    $fila[] = "";
                                } 
                                $fila[] = ""; 
                                $fila[] = "-";  
                                $sheet->mergeCells('E'.$a.':F'.$a); 
                                $sheet->row($a, $fila);
                                $fila = array(); 
                                $a++;           
                            } 
                            $fila[] = 'SUBTOTAL';                           
                            $fila[] = '';                           
                            $fila[] = '';                           
                            $fila[] = '';                           
                            $fila[] = '';                           
                            $fila[] = '';                           
                            $fila[] = '';                           
                            $fila[] = 0.00;                           
                            $fila[] = number_format($subtotalefectivo+$subtotalvisa+$subtotalmaster,2,'.','');  
                            $fila[] = ''; 
                            $fila[] = '';                          
                            $sheet->row($a, $fila);
                            $sheet->mergeCells('A'.$a.':G'.$a); 
                            $sheet->mergeCells('I'.$a.':K'.$a); 
                            $sheet->cells('A'.$a.':L'.$a, function ($cells) {
                                $cells->setFont(array(
                                    'family'     => 'Calibri',
                                    'size'       => '11',
                                    'bold'       =>  true
                                ));
                            }); 
                            $sheet->cell('I'.$a, function($cell){
                                $cell->setAlignment('right');
                            });
                            $fila = array();   
                            $a++;                                    
                        } 

                        //Recorrido para ventas de farmacia

                        if(count($listaventasfarmacia)>0){
                            $sheet->row($a, array('INGRESOS POR VENTAS'));
                            $sheet->mergeCells('A'.$a.':L'.$a);
                            $sheet->cells('A'.$a.':L'.$a, function ($cells) {
                                $cells->setFont(array(
                                    'family'     => 'Calibri',
                                    'size'       => '11',
                                    'bold'       =>  true
                                ));
                            });
                            $a++;
                            $subtotalefectivo = 0;
                            $subtotalvisa = 0;
                            $subtotalmaster = 0;
                            foreach ($listaventasfarmacia as $row) { 
                                $mov = Movimiento::where('movimiento_id', $row['id'])->limit(1)->first();
                                if($mov !== NULL) {
                                    $fila[] = utf8_decode($row['fecha']);
                                    if($row['paciente'] == '') {
                                        $fila[] = $row['nombrepaciente'];
                                    } else {
                                        $fila[] = $row['paciente'];
                                    }   
                                    $fila[] = $mov->tipodocumento->abreviatura;             
                                    $fila[] = utf8_decode($row['serie'] . '-' . $row['numero']);             
                                    if($mov->empresa_id != '') {
                                        $fila[] = $mov->empresa->bussinesname; 
                                    } else {
                                        $fila[] = '-';
                                    }  
                                    $fila[] = $mov->conceptopago->nombre.': '.$row['comentario'];
                                    $fila[] = ''; 
                                    $sheet->mergeCells('F'.$a.':G'.$a);             
                                    $fila[] = '';  
                                    if($row['situacion'] == 'N') {
                                        $valuetp = number_format($row['totalpagado'],2,'.','');
                                        $valuetpv = number_format($row['totalpagadovisa'],2,'.','');
                                        $valuetpm = number_format($row['totalpagadomaster'],2,'.','');
                                        if($valuetp == 0){$valuetp='';}
                                        if($valuetpv == 0){$valuetpv='';}
                                        if($valuetpm == 0){$valuetpm='';}                                                     
                                        $fila[] = $valuetp;                           
                                        $fila[] = $valuetpv;                           
                                        $fila[] = $valuetpm;
                                    } else {
                                        $fila[] = 'ANULADO';                           
                                        $fila[] = '';                           
                                        $fila[] = '';
                                        $sheet->mergeCells('I'.$a.':K'.$a);
                                    } 
                                        
                                    if($row['doctor_id'] != '') {
                                        $fila[] = $row->doctor->apellidopaterno;
                                    } else {
                                        $fila[] = '-';
                                    }  
                                    if($row['situacion'] == 'N') { 
                                        $totalvisa        += number_format($row['totalpagadovisa'],2,'.','');
                                        $totalmaster      += number_format($row['totalpagadomaster'],2,'.','');
                                        $totalefectivo    += number_format($row['totalpagado'],2,'.','');
                                        $subtotalefectivo += number_format($row['totalpagadovisa'],2,'.','');
                                        $subtotalvisa     += number_format($row['totalpagadomaster'],2,'.','');
                                        $subtotalmaster   += number_format($row['totalpagado'],2,'.','');
                                    }
                                    $sheet->row($a, $fila);
                                    $a++;
                                    $fila = array();
                                }
                            } 
                            $fila[] = 'SUBTOTAL';                           
                            $fila[] = '';                           
                            $fila[] = '';                           
                            $fila[] = '';                           
                            $fila[] = '';                           
                            $fila[] = '';                           
                            $fila[] = '';                           
                            $fila[] = 0.00;                           
                            $fila[] = number_format($subtotalefectivo+$subtotalvisa+$subtotalmaster,2,'.','');  
                            $fila[] = ''; 
                            $fila[] = '';                          
                            $sheet->row($a, $fila);
                            $sheet->mergeCells('A'.$a.':G'.$a); 
                            $sheet->mergeCells('I'.$a.':K'.$a); 
                            $sheet->cells('A'.$a.':L'.$a, function ($cells) {
                                $cells->setFont(array(
                                    'family'     => 'Calibri',
                                    'size'       => '11',
                                    'bold'       =>  true
                                ));
                            }); 
                            $sheet->cell('I'.$a, function($cell){
                                $cell->setAlignment('right');
                            });
                            $fila = array(); 
                            $a++;        
                        } 

                        //Recorrido para ingresos varios

                        if(count($listaingresosvarios)>0){
                            $sheet->row($a, array('INGRESOS VARIOS'));
                            $sheet->mergeCells('A'.$a.':L'.$a);
                            $sheet->cells('A'.$a.':L'.$a, function ($cells) {
                                $cells->setFont(array(
                                    'family'     => 'Calibri',
                                    'size'       => '11',
                                    'bold'       =>  true
                                ));
                            });
                            $a++;
                            $subtotalefectivo = 0;
                            $subtotalvisa = 0;
                            $subtotalmaster = 0;
                            foreach ($listaingresosvarios as $row) { 
                                $fila[] = utf8_decode($row['fecha']);                   
                                $fila[] = $row['paciente'];                   
                                $fila[] = $row['formapago'];                   
                                $fila[] = $row['voucher'];                   
                                $fila[] = $row['nombre'].': '.$row['comentario'];                   
                                $fila[] = '';  
                                $sheet->mergeCells('E'.$a.':G'.$a); 
                                $fila[] = '';                           
                                $fila[] = '';                 
                                if($row['situacion'] == 'N') {
                                    $valuetp = number_format($row['totalpagado'],2,'.','');
                                    $valuetpv = number_format($row['totalpagadovisa'],2,'.','');
                                    $valuetpm = number_format($row['totalpagadomaster'],2,'.','');
                                    if($valuetp == 0){$valuetp='';}
                                    if($valuetpv == 0){$valuetpv='';}
                                    if($valuetpm == 0){$valuetpm='';}                                                      
                                    $fila[] = $valuetp;                           
                                    $fila[] = $valuetpv;                           
                                    $fila[] = $valuetpm;                    
                                    $totalvisa += number_format($row['totalpagadovisa'],2,'.','');
                                    $totalmaster += number_format($row['totalpagadomaster'],2,'.','');
                                    $totalefectivo += number_format($row['totalpagado'],2,'.','');
                                    $subtotalefectivo += number_format($row['totalpagadovisa'],2,'.','');
                                    $subtotalvisa     += number_format($row['totalpagadomaster'],2,'.','');
                                    $subtotalmaster   += number_format($row['totalpagado'],2,'.','');
                                } else {
                                    $fila[] = 'ANULADO';                           
                                    $fila[] = '';                           
                                    $fila[] = '';
                                    $sheet->mergeCells('I'.$a.':K'.$a);
                                }
                                $fila[] = '-';
                                $sheet->row($a, $fila);
                                $a++;
                                $fila = array();
                            }   
                            $fila[] = 'SUBTOTAL';                           
                            $fila[] = '';                           
                            $fila[] = '';                           
                            $fila[] = '';                           
                            $fila[] = '';                           
                            $fila[] = '';                           
                            $fila[] = '';                           
                            $fila[] = 0.00;                           
                            $fila[] = number_format($subtotalefectivo+$subtotalvisa+$subtotalmaster,2,'.','');  
                            $fila[] = ''; 
                            $fila[] = '';                          
                            $sheet->row($a, $fila);
                            $sheet->mergeCells('A'.$a.':G'.$a); 
                            $sheet->mergeCells('I'.$a.':K'.$a);
                            $sheet->cells('A'.$a.':L'.$a, function ($cells) {
                                $cells->setFont(array(
                                    'family'     => 'Calibri',
                                    'size'       => '11',
                                    'bold'       =>  true
                                ));
                            }); 
                            $sheet->cell('I'.$a, function($cell){
                                $cell->setAlignment('right');
                            });
                            $fila = array();
                            $a++;                  
                        } 

                        //Recorrido para egresos

                        if(count($listaegresos)>0){
                            $sheet->row($a, array('EGRESOS'));
                            $sheet->mergeCells('A'.$a.':L'.$a);
                            $sheet->cells('A'.$a.':L'.$a, function ($cells) {
                                $cells->setFont(array(
                                    'family'     => 'Calibri',
                                    'size'       => '11',
                                    'bold'       =>  true
                                ));
                            });
                            $a++;
                            $subtotalegresos = 0;
                            foreach ($listaegresos as $row) { 
                                $fila[] = utf8_decode($row['fecha']);                   
                                $fila[] = $row['paciente'];                   
                                $fila[] = $row['formapago'];  
                                $fila[] = $row['voucher'];  
                                $fila[] = $row['nombre'].': '.$row['comentario']; 
                                $fila[] = ''; 
                                $fila[] = ''; 
                                $sheet->mergeCells('E'.$a.':G'.$a);
                                if($row['situacion'] == 'N') {
                                    $fila[] = number_format($row['total'],2,'.','');
                                    $fila[] = '';                           
                                    $subtotalegresos += number_format($row['total'],2,'.','');
                                } else {
                                    $fila[] = '';
                                    $fila[] = 'ANULADO';                   
                                }                            
                                $fila[] = '';
                                $fila[] = '';
                                $fila[] = '-';
                                $sheet->mergeCells('I'.$a.':K'.$a);
                                $sheet->row($a, $fila);
                                $a++;
                                $fila = array();              
                            }
                            $fila[] = 'SUBTOTAL';                           
                            $fila[] = '';                           
                            $fila[] = '';                           
                            $fila[] = '';                           
                            $fila[] = '';                           
                            $fila[] = '';                           
                            $fila[] = '';
                            $fila[] = number_format($subtotalegresos,2,'.','');  
                            $fila[] = 0.00;
                            $fila[] = ''; 
                            $fila[] = '';                          
                            $sheet->row($a, $fila);
                            $sheet->mergeCells('A'.$a.':G'.$a); 
                            $sheet->mergeCells('I'.$a.':K'.$a);
                            $sheet->cells('A'.$a.':L'.$a, function ($cells) {
                                $cells->setFont(array(
                                    'family'     => 'Calibri',
                                    'size'       => '11',
                                    'bold'       =>  true
                                ));
                            }); 
                            $sheet->cell('I'.$a, function($cell){
                                $cell->setAlignment('right');
                            });
                            $fila = array();
                            $a++;                
                        }

                        if($caja->nombre == 'FARMACIA') {
                            if(count($listaegresoscompra)>0){
                                $sheet->row($a, array('EGRESOS POR COMPRA'));
                                $sheet->mergeCells('A'.$a.':L'.$a);
                                $sheet->cells('A'.$a.':L'.$a, function ($cells) {
                                    $cells->setFont(array(
                                        'family'     => 'Calibri',
                                        'size'       => '11',
                                        'bold'       =>  true
                                    ));
                                });
                                $a++;
                                $subtotalegresoscompra = 0;
                                foreach ($listaegresoscompra as $row) {
                                    if($row['situacion2'] == null){
                                        $fila[] = utf8_decode($row['fecha']);                   
                                        $fila[] = $row['paciente'];                   
                                        $fila[] = $row['formapago2'];  
                                        $fila[] = $row['voucher'];  
                                        $fila[] = $row['nombre'].': '.$row['comentario']; 
                                        $fila[] = ''; 
                                        $fila[] = '';
                                        $sheet->mergeCells('E'.$a.':G'.$a);
                                        if($row['situacion'] == 'N') {
                                            $fila[] = number_format($row['total'],2,'.','');
                                            $fila[] = '';
                                            $subtotalegresoscompra += number_format($row['total'],2,'.','');
                                            $subtotalegresos += number_format($row['total'],2,'.','');
                                        } else {
                                            $fila[] = '';
                                            $fila[] = 'ANULADO';
                                        } 
                                        $fila[] = '';                           
                                        $fila[] = ''; 
                                        $fila[] = '-';
                                        $sheet->mergeCells('I'.$a.':K'.$a);
                                        $sheet->row($a, $fila);
                                        $a++;
                                        $fila = array();
                                    }       
                                }  
                                $fila[] = 'SUBTOTAL';                           
                                $fila[] = '';                           
                                $fila[] = '';                           
                                $fila[] = '';                           
                                $fila[] = '';                           
                                $fila[] = '';                           
                                $fila[] = '';                           
                                $fila[] = number_format($subtotalegresoscompra,2,'.','');  
                                $fila[] = 0.00;                           
                                $fila[] = ''; 
                                $fila[] = '';                          
                                $sheet->row($a, $fila);
                                $sheet->mergeCells('A'.$a.':G'.$a); 
                                $sheet->mergeCells('I'.$a.':K'.$a);
                                $sheet->cells('A'.$a.':L'.$a, function ($cells) {
                                    $cells->setFont(array(
                                        'family'     => 'Calibri',
                                        'size'       => '11',
                                        'bold'       =>  true
                                    ));
                                }); 
                                $sheet->cell('I'.$a, function($cell){
                                    $cell->setAlignment('right');
                                });
                                $fila = array();
                                $a++;                
                            }
                        }

                        $sheet->setBorder('A1:L'.($a-1), 'thin');
                        $a++;

                        $fila[] = 'RESPONSABLE';                           
                        $fila[] = $responsable; 
                        $sheet->mergeCells('B'.$a.':L'.$a);
                        $sheet->cells('A'.$a.':L'.$a, function ($cells) {
                            $cells->setFont(array(
                                'family'     => 'Calibri',
                                'size'       => '11',
                                'bold'       =>  true
                            ));
                        });
                        $sheet->row($a, $fila);
                        $fila = array();
                        $a++;
                        $a++; 

                        $fila[] = '';                           
                        $fila[] = '';                           
                        $fila[] = '';                           
                        $fila[] = '';                           
                        $fila[] = 'RESUMEN DE CAJA';
                        $sheet->mergeCells('E'.$a.':F'.$a);
                        $sheet->cells('E'.$a.':F'.$a, function ($cells) {
                            $cells->setFont(array(
                                'family'     => 'Calibri',
                                'size'       => '11',
                                'bold'       =>  true
                            ));
                            $cells->setAlignment('center');
                        });
                        $sheet->row($a, $fila);
                        $fila = array();
                        $a++;

                        $fila[] = '';                           
                        $fila[] = '';                           
                        $fila[] = '';                           
                        $fila[] = '';
                        $fila[] = 'INGRESOS';
                        $fila[] = number_format($totalefectivo + $totalmaster + $totalvisa,2,'.','');
                        $sheet->row($a, $fila);
                        $fila = array();
                        $a++;

                        $fila[] = '';                           
                        $fila[] = '';                           
                        $fila[] = '';                           
                        $fila[] = '';
                        $fila[] = 'Efectivo';
                        $fila[] = number_format($totalefectivo,2,'.','');
                        $sheet->row($a, $fila);
                        $fila = array();
                        $a++;

                        $fila[] = '';                           
                        $fila[] = '';                           
                        $fila[] = '';                           
                        $fila[] = '';
                        $fila[] = 'Master';
                        $fila[] = number_format($totalmaster,2,'.','');
                        $sheet->row($a, $fila);
                        $fila = array();
                        $a++;

                        $fila[] = '';                           
                        $fila[] = '';                           
                        $fila[] = '';                           
                        $fila[] = '';
                        $fila[] = 'Visa';
                        $fila[] = number_format($totalvisa,2,'.','');
                        $sheet->row($a, $fila);
                        $fila = array();
                        $a++;

                        $fila[] = '';                           
                        $fila[] = '';                           
                        $fila[] = '';                           
                        $fila[] = '';
                        $fila[] = 'EGRESOS';
                        $fila[] = number_format($subtotalegresos,2,'.','');
                        $sheet->row($a, $fila);
                        $fila = array();
                        $a++;

                        $fila[] = '';                           
                        $fila[] = '';                           
                        $fila[] = '';                           
                        $fila[] = '';
                        $fila[] = 'SALDO (S/.)';
                        $fila[] = number_format($totalefectivo + $totalmaster + $totalvisa - $subtotalegresos,2,'.','');
                        $sheet->row($a, $fila);
                        $fila = array();
                        $a++;

                        $fila[] = '';                           
                        $fila[] = '';                           
                        $fila[] = '';                           
                        $fila[] = '';
                        $fila[] = 'SALDO ($)';
                        $fila[] = number_format($subtotaldolares,2,'.','');
                        $sheet->row($a, $fila);

                        $sheet->cells('E'.($a-7).':E'.$a, function ($cells) {
                            $cells->setFont(array(
                                'family'     => 'Calibri',
                                'size'       => '11',
                                'bold'       =>  true
                            ));
                        });

                        $sheet->setBorder('E'.($a-7).':F'.$a, 'thin');

                        $sheet->cells('F'.($a-7).':F'.$a, function ($cells) {
                            $cells->setFont(array(
                                'size'       => '11',
                            ));
                        });
                    });
                    if($cont == $numcajas) {
                        break;
                    }
                    $cont++;
                }
            } else {
                Excel::create('Filename', function($excel) {
                    $excel->sheet('No se ha cerrado ninguna caja.', function(\PHPExcel_Worksheet $sheet) {

                    });
                })->store('xlsx', storage_path('excel/exports'));
            }
                
        })->export('xls');
    }

    //Caja actual
    public function pdfDetalleCierre(Request $request){
        $caja    = Caja::find($request->input('caja_id'));
        $caja_id = Libreria::getParam($request->input('caja_id'),'1');

        $user=Auth::user();
        $responsable = $user->login;

        $rst              = Movimiento::where('tipomovimiento_id','=',2)->where('caja_id','=',$caja_id)->where('conceptopago_id','=',1)->orderBy('id','DESC')->limit(1)->first();
        if(count($rst)>0){
            $movimiento_mayor = $rst->id;    
        }else{
            $movimiento_mayor = 0;
        }

        $totalvisa     = 0;
        $totalmaster   = 0;
        $totalefectivo = 0;
        $totalegresos  = 0;
        $subtotalegresos = 0;
        $subtotalegresoscompra = 0;
        $subtotaldolares = 0;

        //sucursal_id
        $sucursal_id = Session::get('sucursal_id');
        $nomcierre = '';
        $nomcierre = 'Clínica Especialidades'; 
        if($sucursal_id == 1) {
            $nomcierre = 'BM Clínica de Ojos';
        }  
        if($caja->nombre == 'FARMACIA') {
            $nomcierre = 'Farmacia - ' . $nomcierre;
        }     
        $pdf = new TCPDF();
        //$pdf::SetIma�
        $pdf::SetTitle('Detalle Cierre de '.$nomcierre);
        $pdf::AddPage('L');
        $pdf::SetFont('helvetica','B',12);
        $pdf::Cell(0,10,"Detalle de Cierre de ".$nomcierre,0,0,'C');
        $pdf::Ln();
        $pdf::SetFont('helvetica','B',7);
        $pdf::Cell(15,7,utf8_decode("FECHA"),1,0,'C');
        $pdf::Cell(56,7,utf8_decode("PERSONA"),1,0,'C');
        $pdf::Cell(20,7,utf8_decode("NRO"),1,0,'C');
        $pdf::Cell(40,7,utf8_decode("EMPRESA"),1,0,'C');
        $pdf::Cell(60,7,utf8_decode("CONCEPTO"),1,0,'C');
        $pdf::Cell(14,7,utf8_decode("PRECIO"),1,0,'C');
        $pdf::Cell(14,7,utf8_decode("EGRESO"),1,0,'C');
        $pdf::Cell(42,7,utf8_decode("INGRESO"),1,0,'C');
        $pdf::Cell(20,7,utf8_decode("DOCTOR"),1,0,'C');
        $pdf::Ln();
        $pdf::Cell(219,7,utf8_decode(""),1,0,'C');
        $pdf::Cell(14,7,utf8_decode("EFECTIVO"),1,0,'C');
        $pdf::Cell(14,7,utf8_decode("VISA"),1,0,'C');
        $pdf::Cell(14,7,utf8_decode("MASTER"),1,0,'C');
        $pdf::Cell(20,7,utf8_decode(""),1,0,'C');
        $pdf::Ln();
        if($caja_id==1){//ADMISION 1
            $serie=3;
        }elseif($caja_id==2){//ADMISION 2
            $serie=7;
        }elseif($caja_id==3){//CONVENIOS
            $serie=8;
        }elseif($caja_id==5){//EMERGENCIA
            $serie=9;
        }elseif($caja_id==4){//FARMACIA
            $serie=4;
        }/*elseif($caja_id==8){//PROCEDIMIENTOS
            $serie=5;
        }*/

        $ingreso=0;
        $egreso=0;
        $transferenciai=0;
        $transferenciae=0;
        $garantia=0;
        $efectivo=0;
        $visa=0;
        $master=0;

        //Pagos de tickets   

        $sucursal_id = Session::get('sucursal_id');
        $caja_id = $request->input('caja_id');
        $resultadoventas = Movimiento::leftjoin('movimiento as m2','movimiento.movimiento_id','=','m2.id')
                ->leftjoin('person as paciente', 'paciente.id', '=', 'movimiento.persona_id')
                //->where('movimiento.situacion','=','N')
                ->where('movimiento.ventafarmacia','=','N')
                ->where('movimiento.sucursal_id', '=', $sucursal_id)
                ->where('movimiento.id', '>=', $movimiento_mayor)
                ->where('movimiento.caja_id', '=', $caja_id);
        $resultadoventas = $resultadoventas->select('movimiento.doctor_id','movimiento.serie','movimiento.tipodocumento_id','movimiento.id','movimiento.comentario','movimiento.movimiento_id','movimiento.fecha','movimiento.numero','movimiento.total','movimiento.totalpagado','movimiento.totalpagadovisa','movimiento.totalpagadomaster','m2.numero as numeroticket',DB::raw('case when paciente.bussinesname is null then concat(paciente.apellidopaterno,\' \',paciente.nombres) else paciente.bussinesname end as paciente'), 'movimiento.total')->orderBy('movimiento.numero', 'asc');
        
        $listaventas           = $resultadoventas->get();

        if(count($listaventas)>0){
            $pdf::SetFont('helvetica','B',8.5);
            $pdf::Cell(281,7,'INGRESOS POR VENTAS',1,0,'L');
            $pdf::Ln();
            $subtotalefectivo = 0;
            $subtotalvisa = 0;
            $subtotalmaster = 0;
            foreach ($listaventas as $row) {                
                $row2 = Movimiento::where('movimiento_id', $row['id'])->limit(1)->first();
                $row3 = Movimiento::find($row['movimiento_id']);
                //$row3 = Movimiento::where('movimiento_id', $row2['id'])->limit(1)->first();
                if($row2['situacion'] != '') {
                    $detalles = Detallemovcaja::where('movimiento_id', $row3['id'])->get();  
                    $i = 0;              
                    foreach ($detalles as $detalle) {
                        if($i == 0) {
                            $pdf::SetFont('helvetica','',6);                   
                            $pdf::Cell(15,7*count($detalles ),utf8_decode($row['numeroserie2']),1,0,'C');
                            $pdf::Cell(56,7*count($detalles),$row['paciente'],1,0,'L');
                            $pdf::Cell(8,7*count($detalles),$row->tipodocumento->abreviatura,1,0,'C');
                            $pdf::Cell(12,7*count($detalles),utf8_decode($row['serie'] .'-'. $row['numero']),1,0,'C');
                        } else {
                            $pdf::SetFont('helvetica','',6);                   
                            $pdf::Cell(15,7,'',0,0,'C');
                            $pdf::Cell(56,7,'',0,0,'L');
                            $pdf::Cell(8,7,'',0,0,'C');
                            $pdf::Cell(12,7,'',0,0,'C');
                        }       
                        if($row3['plan_id'] != '') {
                            $pdf::Cell(40,7,substr($row3->plan->nombre,0,28) . '.',1,0,'L');
                        } else {
                            $pdf::Cell(40,7,'',1,0,'L');
                        }  
                        $nomdetalle = ''; 
                        if($detalle->servicio_id == 13) {
                            $nomdetalle .= '($) ';
                        }  
                        $nomdetalle .= $detalle->servicio->nombre;
                        $pdf::Cell(60,7,substr($nomdetalle,0,40) . '.',1,0,'L');
                        $pdf::Cell(14,7,number_format($detalle->precio,2,',',''),1,0,'R');                    
                        if($i == 0) {
                            if($row2['situacion'] == 'N') {                                
                                $pdf::Cell(14,7*count($detalles),'',1,0,'L');
                                $valuetp = number_format($row2['totalpagado'],2,'.','');
                                $valuetpv = number_format($row2['totalpagadovisa'],2,'.','');
                                $valuetpm = number_format($row2['totalpagadomaster'],2,'.','');
                                if($valuetp == 0){$valuetp='';}
                                if($valuetpv == 0){$valuetpv='';}
                                if($valuetpm == 0){$valuetpm='';}
                                $pdf::Cell(14,7*count($detalles),$valuetp,1,0,'R');                    
                                $pdf::Cell(14,7*count($detalles),$valuetpv,1,0,'R');
                                $pdf::Cell(14,7*count($detalles),$valuetpm,1,0,'R');
                            } else {
                                $pdf::Cell(56,7*count($detalles),'ANULADO',1,0,'C');
                            }
                        } else {
                            $pdf::Cell(14,7,'',0,0,'L');
                            $pdf::Cell(14,7,'',0,0,'R');                    
                            $pdf::Cell(14,7,'',0,0,'R');
                            $pdf::Cell(14,7,'',0,0,'R');                        
                        }
                        $pdf::Cell(20,7,utf8_decode($detalle->persona->apellidopaterno),1,0,'C');                        
                        $pdf::Ln();
                        $i++;
                    }  
                    if($row2['situacion'] == 'N') { 
                        if($row3['numeroserie2'] != 'DOLAR') {                 
                            $totalvisa += number_format($row2['totalpagadovisa'],2,'.','');
                            $totalmaster += number_format($row2['totalpagadomaster'],2,'.','');
                            $totalefectivo += number_format($row2['totalpagado'],2,'.','');
                            $subtotalefectivo += number_format($row2['totalpagadovisa'],2,'.','');
                            $subtotalvisa     += number_format($row2['totalpagadomaster'],2,'.','');
                            $subtotalmaster   += number_format($row2['totalpagado'],2,'.','');
                        } else {
                            $subtotaldolares += number_format($row2['total'],2,'.','');
                        }
                    }
                }
            } 
            $pdf::SetFont('helvetica','B',8.5);
            $pdf::Cell(205,7,'SUBTOTAL',1,0,'R');
            $pdf::Cell(14,7,number_format(0,2,'.',''),1,0,'R');
            $pdf::Cell(42,7,number_format($subtotalefectivo+$subtotalvisa+$subtotalmaster,2,'.',''),1,0,'R');
            $pdf::Ln();                   
        }      

        //Solo para cuotas

        $sucursal_id = Session::get('sucursal_id');
        $caja_id = $request->input('caja_id');
        $resultadoventas = Movimiento::leftjoin('movimiento as m2','movimiento.movimiento_id','=','m2.id')
                ->leftjoin('person as paciente', 'paciente.id', '=', 'movimiento.persona_id')
                ->where('movimiento.tipomovimiento_id', '=', 2)
                ->where('movimiento.tipodocumento_id', '=', 2)
                ->where('movimiento.situacion2','=','Z')
                //->where('movimiento.situacion','=','N')
                ->where('movimiento.sucursal_id', '=', $sucursal_id)
                ->where('movimiento.id', '>=', $movimiento_mayor)
                ->where('movimiento.caja_id', '=', $caja_id);
        $resultadoventas = $resultadoventas->select('movimiento.numeroserie2','movimiento.movimiento_id','movimiento.situacion','movimiento.fecha','movimiento.numero','movimiento.total','movimiento.totalpagado','movimiento.totalpagadovisa','movimiento.totalpagadomaster','m2.numero as numeroticket',DB::raw('case when paciente.bussinesname is null then concat(paciente.apellidopaterno,\' \',paciente.nombres) else paciente.bussinesname end as paciente'), 'movimiento.total')->orderBy('movimiento.numero', 'asc');
        
        $listaventas           = $resultadoventas->get();

        if(count($listaventas)>0){
            $pdf::SetFont('helvetica','B',8.5);
            $pdf::Cell(281,7,'INGRESOS POR CUOTAS',1,0,'L');
            $pdf::Ln();
            $subtotalefectivo = 0;
            $subtotalvisa = 0;
            $subtotalmaster = 0;
            foreach ($listaventas as $row) { 
                $cuota = Movimiento::find($row['numeroserie2']);
                $pdf::SetFont('helvetica','',6);                   
                $pdf::Cell(15,7,utf8_decode($row['fecha']),1,0,'C');
                $pdf::Cell(56,7,$row['paciente'],1,0,'L');
                $pdf::Cell(8,7,'C',1,0,'C');
                $pdf::Cell(12,7,utf8_decode($cuota->numero),1,0,'C');
                $pdf::Cell(114,7,"PAGO DE CUOTA DE TICKET N° " . $row['numeroticket'],1,0,'L');

                if($row['situacion'] == 'N') {
                    $valuetp = number_format($row['totalpagado'],2,'.','');
                    $valuetpv = number_format($row['totalpagadovisa'],2,'.','');
                    $valuetpm = number_format($row['totalpagadomaster'],2,'.','');
                    if($valuetp == 0){$valuetp='';}
                    if($valuetpv == 0){$valuetpv='';}
                    if($valuetpm == 0){$valuetpm='';}
                    $pdf::Cell(14,7,'',1,0,'R');
                    $pdf::Cell(14,7,$valuetp,1,0,'R');                    
                    $pdf::Cell(14,7,$valuetpv,1,0,'R');
                    $pdf::Cell(14,7,$valuetpm,1,0,'R');
                    $totalvisa += number_format($row['totalpagadovisa'],2,'.','');
                    $totalmaster += number_format($row['totalpagadomaster'],2,'.','');
                    $totalefectivo += number_format($row['totalpagado'],2,'.','');
                    $subtotalefectivo += number_format($row['totalpagadovisa'],2,'.','');
                    $subtotalvisa     += number_format($row['totalpagadomaster'],2,'.','');
                    $subtotalmaster   += number_format($row['totalpagado'],2,'.','');
                } else {
                    $pdf::Cell(56,7,'ANULADO',1,0,'C');                    
                }  
                $pdf::Cell(20,7,utf8_decode("-"),1,0,'C');
                $pdf::Ln();                  
            } 
            $pdf::SetFont('helvetica','B',8.5);
            $pdf::Cell(205,7,'SUBTOTAL',1,0,'R');
            $pdf::Cell(14,7,number_format(0,2,'.',''),1,0,'R');
            $pdf::Cell(42,7,number_format($subtotalefectivo+$subtotalvisa+$subtotalmaster,2,'.',''),1,0,'R');
            $pdf::Ln();                   
        }

        //Solo para ventas de farmacia

        $listaventasfarmacia = Movimiento::leftjoin('movimiento as m2','movimiento.movimiento_id','=','m2.id')
                ->leftjoin('person as paciente', 'paciente.id', '=', 'movimiento.persona_id')
                ->leftjoin('conceptopago','conceptopago.id','=','movimiento.conceptopago_id')
                ->where('movimiento.sucursal_id', '=', $sucursal_id)
                ->where('movimiento.caja_id', '=', $caja_id)
                ->where('movimiento.id', '>=', $movimiento_mayor)
                ->where('movimiento.ventafarmacia', '=', 'S');
        $listaventasfarmacia = $listaventasfarmacia->select('movimiento.situacion','movimiento.doctor_id','movimiento.serie','movimiento.id','movimiento.nombrepaciente','movimiento.voucher','movimiento.formapago','movimiento.comentario','movimiento.fecha','movimiento.numero','movimiento.total','movimiento.totalpagado','movimiento.totalpagadovisa','movimiento.totalpagadomaster','m2.numero as numeroticket',DB::raw('case when paciente.bussinesname is null then concat(paciente.apellidopaterno,\' \',paciente.nombres) else paciente.bussinesname end as paciente'), 'movimiento.total')->orderBy('movimiento.numero', 'asc');
        
        $listaventasfarmacia = $listaventasfarmacia->get();

        if(count($listaventasfarmacia)>0){
            $pdf::SetFont('helvetica','B',8.5);
            $pdf::Cell(281,7,'INGRESOS POR VENTAS',1,0,'L');
            $pdf::Ln();
            $subtotalefectivo = 0;
            $subtotalvisa = 0;
            $subtotalmaster = 0;
            foreach ($listaventasfarmacia as $row) { 
                $mov = Movimiento::where('movimiento_id', $row['id'])->limit(1)->first();

                //Quitamos las guias internas sin copago
                if($mov !== NULL) {
                    $pdf::SetFont('helvetica','',6);                   
                    $pdf::Cell(15,7,utf8_decode($row['fecha']),1,0,'C');
                    if($row['paciente'] == '') {
                        $pdf::Cell(56,7,$row['nombrepaciente'],1,0,'L');
                    } else {
                        $pdf::Cell(56,7,$row['paciente'],1,0,'L');
                    }                
                    $pdf::Cell(8,7,$mov === NULL ? '-' : $mov->tipodocumento->abreviatura,1,0,'C');
                    $pdf::Cell(12,7,utf8_decode($row['serie'] . '-' . $row['numero']),1,0,'C');
                    if($mov !== NULL && $mov->empresa_id != '') {
                        $pdf::Cell(40,7,$mov->empresa->bussinesname,1,0,'L');                    
                    } else {
                        $pdf::Cell(40,7,'-',1,0,'C');
                    }    
                    $pdf::Cell(88,7,$mov === NULL ? '-' : $mov->conceptopago->nombre.': '.$row['comentario'],1,0,'L'); 
                    if($row['situacion'] == 'N') {
                        $valuetp = number_format($row['totalpagado'],2,'.','');
                        $valuetpv = number_format($row['totalpagadovisa'],2,'.','');
                        $valuetpm = number_format($row['totalpagadomaster'],2,'.','');
                        if($valuetp == 0){$valuetp='';}
                        if($valuetpv == 0){$valuetpv='';}
                        if($valuetpm == 0){$valuetpm='';}
                        $pdf::Cell(14,7,$valuetp,1,0,'R');                    
                        $pdf::Cell(14,7,$valuetpv,1,0,'R');
                        $pdf::Cell(14,7,$valuetpm,1,0,'R');
                    } else {
                        $pdf::Cell(42,7,'ANULADO',1,0,'C');
                    }                       
                    if($row['doctor_id'] != '') {
                        $pdf::Cell(20,7,$row->doctor->apellidopaterno,1,0,'C');
                    } else {
                        $pdf::Cell(20,7,utf8_decode("-"),1,0,'C');
                    }                
                    $pdf::Ln();
                    if($row['situacion'] == 'N') {
                        $totalvisa        += number_format($row['totalpagadovisa'],2,'.','');
                        $totalmaster      += number_format($row['totalpagadomaster'],2,'.','');
                        $totalefectivo    += number_format($row['totalpagado'],2,'.','');
                        $subtotalefectivo += number_format($row['totalpagadovisa'],2,'.','');
                        $subtotalvisa     += number_format($row['totalpagadomaster'],2,'.','');
                        $subtotalmaster   += number_format($row['totalpagado'],2,'.','');
                    }
                }                    
            } 
            $pdf::SetFont('helvetica','B',8.5);
            $pdf::Cell(205,7,'SUBTOTAL',1,0,'R');
            $pdf::Cell(14,7,number_format(0,2,'.',''),1,0,'R');
            $pdf::Cell(42,7,number_format($subtotalefectivo+$subtotalvisa+$subtotalmaster,2,'.',''),1,0,'R');
            $pdf::Ln();                 
        }

        //Solo para ingresos varios

        $listaingresosvarios = Movimiento::leftjoin('movimiento as m2','movimiento.movimiento_id','=','m2.id')
                ->leftjoin('person as paciente', 'paciente.id', '=', 'movimiento.persona_id')
                ->join('conceptopago','conceptopago.id','=','movimiento.conceptopago_id')
                ->where('movimiento.tipomovimiento_id', '=', 2)
                ->where('movimiento.sucursal_id', '=', $sucursal_id)
                ->where('movimiento.caja_id', '=', $caja_id)
                ->where('movimiento.id', '>=', $movimiento_mayor)
                ->where('movimiento.situacion2', '=', 'Q')
                ->where('conceptopago.tipo', '=', 'I');
        $listaingresosvarios = $listaingresosvarios->select('movimiento.situacion','movimiento.voucher','movimiento.formapago','movimiento.comentario','movimiento.fecha','movimiento.numero','movimiento.total','movimiento.totalpagado','movimiento.totalpagadovisa','movimiento.totalpagadomaster','m2.numero as numeroticket',DB::raw('case when paciente.bussinesname is null then concat(paciente.apellidopaterno,\' \',paciente.nombres) else paciente.bussinesname end as paciente'), 'conceptopago.nombre', 'movimiento.total')->orderBy('movimiento.numero', 'asc');
        
        $listaingresosvarios = $listaingresosvarios->get();

        if(count($listaingresosvarios)>0){
            $pdf::SetFont('helvetica','B',8.5);
            $pdf::Cell(281,7,'INGRESOS VARIOS',1,0,'L');
            $pdf::Ln();
            $subtotalefectivo = 0;
            $subtotalvisa = 0;
            $subtotalmaster = 0;
            foreach ($listaingresosvarios as $row) { 
                $pdf::SetFont('helvetica','',6);                   
                $pdf::Cell(15,7,utf8_decode($row['fecha']),1,0,'C');
                $pdf::Cell(56,7,$row['paciente'],1,0,'L');
                $pdf::Cell(8,7,$row['formapago'],1,0,'C');
                $pdf::Cell(12,7,utf8_decode($row['voucher']),1,0,'C');
                $pdf::Cell(114,7,$row['nombre'].': '.$row['comentario'],1,0,'L');
                if($row['situacion'] == 'N') {
                    $valuetp = number_format($row['totalpagado'],2,'.','');
                    $valuetpv = number_format($row['totalpagadovisa'],2,'.','');
                    $valuetpm = number_format($row['totalpagadomaster'],2,'.','');
                    if($valuetp == 0){$valuetp='';}
                    if($valuetpv == 0){$valuetpv='';}
                    if($valuetpm == 0){$valuetpm='';}
                    $pdf::Cell(14,7,'',1,0,'R');                    
                    $pdf::Cell(14,7,$valuetp,1,0,'R');                    
                    $pdf::Cell(14,7,$valuetpv,1,0,'R');
                    $pdf::Cell(14,7,$valuetpm,1,0,'R');                    
                    $totalvisa += number_format($row['totalpagadovisa'],2,'.','');
                    $totalmaster += number_format($row['totalpagadomaster'],2,'.','');
                    $totalefectivo += number_format($row['totalpagado'],2,'.','');
                    $subtotalefectivo += number_format($row['totalpagadovisa'],2,'.','');
                    $subtotalvisa     += number_format($row['totalpagadomaster'],2,'.','');
                    $subtotalmaster   += number_format($row['totalpagado'],2,'.','');
                } else {
                    $pdf::Cell(56,7,'ANULADO',1,0,'C');
                }
                $pdf::Cell(20,7,utf8_decode("-"),1,0,'C');
                $pdf::Ln();
                    
            }   
            $pdf::SetFont('helvetica','B',8.5);
            $pdf::Cell(205,7,'SUBTOTAL',1,0,'R');
            $pdf::Cell(14,7,number_format(0,2,'.',''),1,0,'R');
            $pdf::Cell(42,7,number_format($subtotalefectivo+$subtotalvisa+$subtotalmaster,2,'.',''),1,0,'R');
            $pdf::Ln();                 
        }

        //Solo para egresos

        $resultadoegresos        = Movimiento::leftjoin('person as paciente', 'paciente.id', '=', 'movimiento.persona_id')
            ->join('person as responsable', 'responsable.id', '=', 'movimiento.responsable_id')
            ->join('conceptopago','conceptopago.id','=','movimiento.conceptopago_id')
            ->leftjoin('movimiento as m2','m2.movimiento_id','=','movimiento.id')
            ->where('movimiento.caja_id', '=', $caja_id)
            ->where('movimiento.sucursal_id','=',$sucursal_id)
            ->where('movimiento.id', '>=', $movimiento_mayor)
            ->whereNull('movimiento.cajaapertura_id')
            ->where(function($query){
                $query
                    ->whereNotIn('movimiento.conceptopago_id',[31])
                    ->orWhere('m2.situacion','<>','R');
            })
            //->where('movimiento.situacion', '<>', 'A')
            //->where('movimiento.situacion', '<>', 'R')
            ->where('conceptopago.tipo', '=', 'E')
            ->where('movimiento.situacion2', '=', 'Q');

        $resultadoegresos        = $resultadoegresos->select('movimiento.*','m2.situacion as situacion2','responsable.nombres as responsable2',DB::raw('case when paciente.bussinesname is null then concat(paciente.apellidopaterno,\' \',paciente.nombres) else paciente.bussinesname end as paciente'),'paciente.ruc as ruc' ,DB::raw('concat(paciente.ruc,\' \',paciente.bussinesname) as razonsocial'), 'conceptopago.nombre')->orderBy('conceptopago.tipo', 'asc')->orderBy('conceptopago.orden', 'asc')->orderBy('conceptopago.id', 'asc')->orderBy('movimiento.tipotarjeta', 'asc')->orderBy('movimiento.numero', 'asc');

        $listaegresos = $resultadoegresos->get();

        if(count($listaegresos)>0){
            $pdf::SetFont('helvetica','B',8.5);
            $pdf::Cell(281,7,'EGRESOS',1,0,'L');
            $pdf::Ln();
            $subtotalegresos = 0;
            foreach ($listaegresos as $row) { 
                $pdf::SetFont('helvetica','',6);                   
                $pdf::Cell(15,7,utf8_decode($row['fecha']),1,0,'C');
                if($row['ruc'] == null){
                    $pdf::Cell(56,7,$row['paciente'],1,0,'L');
                }else{
                    $pdf::Cell(56,7,$row['razonsocial'],1,0,'L');
                }
                $pdf::Cell(8,7,$row['formapago'],1,0,'C');
                $pdf::Cell(12,7,utf8_decode($row['voucher']),1,0,'C');
                $pdf::Cell(114,7,$row['nombre'].': '.$row['comentario'],1,0,'L');
                if($row['situacion'] == 'N') {
                    $pdf::Cell(14,7,number_format($row['total'],2,'.',''),1,0,'R');
                    $pdf::Cell(42,7,utf8_decode(""),1,0,'C');                    
                    $subtotalegresos += number_format($row['total'],2,'.','');
                } else {
                    $pdf::Cell(56,7,utf8_decode("ANULADO"),1,0,'C');
                }  
                $pdf::Cell(20,7,utf8_decode("-"),1,0,'C');
                $pdf::Ln();              
            }      
            $pdf::SetFont('helvetica','B',8.5);
            $pdf::Cell(205,7,'SUBTOTAL',1,0,'R');
            $pdf::Cell(14,7,number_format($subtotalegresos,2,'.',''),1,0,'R');
            $pdf::Cell(42,7,number_format(0,2,'.',''),1,0,'R');
            $pdf::Ln();                  
        }

        //Solo para egresos por compra farmacia

        if($caja->nombre == 'FARMACIA') {
            $resultadoegresos        = Movimiento::leftjoin('person as paciente', 'paciente.id', '=', 'movimiento.persona_id')
                ->join('person as responsable', 'responsable.id', '=', 'movimiento.responsable_id')
                ->join('conceptopago','conceptopago.id','=','movimiento.conceptopago_id')
                ->join('tipodocumento','tipodocumento.id','=','movimiento.tipodocumento_id')
                ->leftjoin('movimiento as m2','m2.movimiento_id','=','movimiento.id')
                ->where('movimiento.caja_id', '=', $caja_id)
                ->whereNull('m2.caja_id')
                ->where('movimiento.sucursal_id','=',$sucursal_id)
                ->where('movimiento.tipomovimiento_id','=',2)
                ->where('paciente.dni','=',null)
                //->where('m2.tipomovimiento_id','=',3)
                ->where('movimiento.id', '>', $movimiento_mayor)
                ->whereNull('movimiento.cajaapertura_id')
                /*->where(function($query){
                    $query
                        ->whereNotIn('movimiento.conceptopago_id',[31])
                        ->orWhere('m2.situacion','<>','R');
                })*/
                //->where('movimiento.situacion', '<>', 'A')
                //->where('movimiento.situacion', '<>', 'R')
                ->where('conceptopago.tipo', '=', 'E');
                //->where('movimiento.situacion2', '=', 'Q');

            $resultadoegresos        = $resultadoegresos->select('movimiento.*','tipodocumento.abreviatura as formapago2','responsable.nombres as responsable2',DB::raw('concat(paciente.ruc,\' - \',paciente.bussinesname) as paciente'), 'conceptopago.nombre')->orderBy('conceptopago.tipo', 'asc')->orderBy('conceptopago.orden', 'asc')->orderBy('conceptopago.id', 'asc')->orderBy('movimiento.tipotarjeta', 'asc')->orderBy('movimiento.numero', 'asc');

            $listaegresos = $resultadoegresos->get();

            if(count($listaegresos)>0){
                $pdf::SetFont('helvetica','B',8.5);
                $pdf::Cell(281,7,'EGRESOS POR COMPRA',1,0,'L');
                $pdf::Ln();
                $subtotalegresoscompra = 0;
                foreach ($listaegresos as $row) { 
                    if($row['situacion2'] == null){
                        $pdf::SetFont('helvetica','',6);                   
                        $pdf::Cell(15,7,utf8_decode($row['fecha']),1,0,'C');
                        $pdf::Cell(56,7,$row['paciente'],1,0,'L');
                        $pdf::Cell(8,7,$row['formapago2'],1,0,'C');
                        $pdf::Cell(12,7,utf8_decode($row['voucher']),1,0,'C');
                        $pdf::Cell(114,7,$row['nombre'].': '.$row['comentario'],1,0,'L');
                        if($row['situacion'] == 'N') {
                            $pdf::Cell(14,7,number_format($row['total'],2,'.',''),1,0,'R');
                            $pdf::Cell(42,7,utf8_decode(""),1,0,'C');                    
                            $subtotalegresoscompra += number_format($row['total'],2,'.','');
                        } else {
                            $pdf::Cell(56,7,utf8_decode("ANULADO"),1,0,'C');
                        }  
                        $pdf::Cell(20,7,utf8_decode("-"),1,0,'C');
                        $pdf::Ln();     
                    }         
                }      
                $pdf::SetFont('helvetica','B',8.5);
                $pdf::Cell(205,7,'SUBTOTAL',1,0,'R');
                $pdf::Cell(14,7,number_format($subtotalegresoscompra,2,'.',''),1,0,'R');
                $pdf::Cell(42,7,number_format(0,2,'.',''),1,0,'R');
                $pdf::Ln();                  
            }
        }

        $pdf::SetFont('helvetica','',7);   
        $pdf::Ln();
        $pdf::Ln();
        $pdf::Cell(120,7,('RESPONSABLE: '.$responsable),0,0,'L');
        $pdf::SetFont('helvetica','B',9);
        $pdf::Cell(50,7,utf8_decode("RESUMEN DE CAJA"),1,0,'C');
        $pdf::Ln();
        $pdf::Cell(120,7,utf8_decode(""),0,0,'C');
        $pdf::Cell(30,7,utf8_decode("INGRESOS :"),1,0,'L');
        $pdf::Cell(20,7,number_format($totalefectivo + $totalmaster + $totalvisa,2,'.',''),1,0,'R');
        $pdf::Ln();
        $pdf::Cell(120,7,utf8_decode(""),0,0,'C');
        $pdf::Cell(30,7,utf8_decode("Efectivo :"),1,0,'L');
        $pdf::Cell(20,7,number_format($totalefectivo,2,'.',''),1,0,'R');
        $pdf::Ln();
        $pdf::Cell(120,7,utf8_decode(""),0,0,'C');
        $pdf::Cell(30,7,utf8_decode("Master :"),1,0,'L');
        $pdf::Cell(20,7,number_format($totalmaster,2,'.',''),1,0,'R');
        $pdf::Ln();
        $pdf::Cell(120,7,utf8_decode(""),0,0,'C');
        $pdf::Cell(30,7,utf8_decode("Visa :"),1,0,'L');
        $pdf::Cell(20,7,number_format($totalvisa,2,'.',''),1,0,'R');
        $pdf::Ln();
        $pdf::Cell(120,7,utf8_decode(""),0,0,'C');
        $pdf::Cell(30,7,utf8_decode("EGRESOS :"),1,0,'L');
        if($caja->nombre == 'FARMACIA') {
            $pdf::Cell(20,7,number_format($subtotalegresos + $subtotalegresoscompra,2,'.',''),1,0,'R');
        }else{
            $pdf::Cell(20,7,number_format($subtotalegresos,2,'.',''),1,0,'R');
        }
        $pdf::Ln();
        $pdf::Cell(120,7,utf8_decode(""),0,0,'C');
        $pdf::Cell(30,7,utf8_decode("SALDO (S/.) :"),1,0,'L');
        if($caja->nombre == 'FARMACIA') {
            $pdf::Cell(20,7,number_format($totalefectivo + $totalmaster + $totalvisa - $subtotalegresos - $subtotalegresoscompra,2,'.',''),1,0,'R');
        }else{
            $pdf::Cell(20,7,number_format($totalefectivo + $totalmaster + $totalvisa - $subtotalegresos,2,'.',''),1,0,'R');
        }
        $pdf::Ln();
        $pdf::Cell(120,7,utf8_decode(""),0,0,'C');
        $pdf::Cell(30,7,utf8_decode("SALDO ($) :"),1,0,'L');
        $pdf::Cell(20,7,number_format($subtotaldolares,2,'.',''),1,0,'R');
        $pdf::Ln();
        /*$pdf::Cell(120,7,utf8_decode(""),0,0,'C');
        $pdf::Cell(30,7,utf8_decode("GARANTIA :"),1,0,'L');
        $pdf::Cell(20,7,number_format($garantia,2,'.',''),1,0,'R');*/
        $pdf::Ln();
        $pdf::Output('ListaCaja.pdf');
    }

    //Consolidado
    public function pdfDetalleCierreF(Request $request){

        $caja    = Caja::find($request->input('caja_id'));
        $caja_id = Libreria::getParam($request->input('caja_id'),'1');

        $fi = Libreria::getParam($request->input('fi'),'1');
        $ff = Libreria::getParam($request->input('ff'),'1');

        $user=Auth::user();
        $responsable = $user->login;

        $totalvisa     = 0;
        $totalmaster   = 0;
        $totalefectivo = 0;
        $totalegresos  = 0;
        $subtotalegresos = 0;
        $subtotaldolares = 0;

        //sucursal_id
        $sucursal_id = Session::get('sucursal_id');
        $nomcierre = '';
        $nomcierre = 'Clínica Especialidades'; 
        if($sucursal_id == 1) {
            $nomcierre = 'BM Clínica de Ojos';
        }  
        if($caja->nombre == 'FARMACIA') {
            $nomcierre = 'Farmacia - ' . $nomcierre;
        }     
        $pdf = new TCPDF();
        //$pdf::SetIma�
        $pdf::SetTitle('Detalle Cierre Consolidado de '.$nomcierre);
        $pdf::AddPage('L');
        $pdf::SetFont('helvetica','B',12);
        $pdf::Cell(0,10,"Detalle Cierre Consolidado de ".$nomcierre . ' del ' . date("d/m/Y", strtotime($fi)) . ' al ' . date("d/m/Y", strtotime($ff)),0,0,'C');
        $pdf::Ln();
        $pdf::SetFont('helvetica','B',7);
        $pdf::Cell(15,7,utf8_decode("FECHA"),1,0,'C');
        $pdf::Cell(56,7,utf8_decode("PERSONA"),1,0,'C');
        $pdf::Cell(20,7,utf8_decode("NRO"),1,0,'C');
        $pdf::Cell(40,7,utf8_decode("EMPRESA"),1,0,'C');
        $pdf::Cell(60,7,utf8_decode("CONCEPTO"),1,0,'C');
        $pdf::Cell(14,7,utf8_decode("PRECIO"),1,0,'C');
        $pdf::Cell(14,7,utf8_decode("EGRESO"),1,0,'C');
        $pdf::Cell(42,7,utf8_decode("INGRESO"),1,0,'C');
        $pdf::Cell(20,7,utf8_decode("DOCTOR"),1,0,'C');
        $pdf::Ln();
        $pdf::Cell(219,7,utf8_decode(""),1,0,'C');
        $pdf::Cell(14,7,utf8_decode("EFECTIVO"),1,0,'C');
        $pdf::Cell(14,7,utf8_decode("VISA"),1,0,'C');
        $pdf::Cell(14,7,utf8_decode("MASTER"),1,0,'C');
        $pdf::Cell(20,7,utf8_decode(""),1,0,'C');
        $pdf::Ln();
        if($caja_id==1){//ADMISION 1
            $serie=3;
        }elseif($caja_id==2){//ADMISION 2
            $serie=7;
        }elseif($caja_id==3){//CONVENIOS
            $serie=8;
        }elseif($caja_id==5){//EMERGENCIA
            $serie=9;
        }elseif($caja_id==4){//FARMACIA
            $serie=4;
        }/*elseif($caja_id==8){//PROCEDIMIENTOS
            $serie=5;
        }*/

        $ingreso=0;
        $egreso=0;
        $transferenciai=0;
        $transferenciae=0;
        $garantia=0;
        $efectivo=0;
        $visa=0;
        $master=0;

        //Pagos de tickets   

        $sucursal_id = Session::get('sucursal_id');
        $caja_id = $request->input('caja_id');
        $resultadoventas = Movimiento::leftjoin('movimiento as m2','movimiento.movimiento_id','=','m2.id')
                ->leftjoin('person as paciente', 'paciente.id', '=', 'movimiento.persona_id')
                //->where('movimiento.situacion','=','N')
                ->where('movimiento.ventafarmacia','=','N')
                ->where('movimiento.sucursal_id', '=', $sucursal_id)
                ->whereBetween('movimiento.fecha', [$fi, $ff])
                ->where('movimiento.caja_id', '=', $caja_id);
        $resultadoventas = $resultadoventas->select('movimiento.plan_id','movimiento.doctor_id','movimiento.serie','movimiento.tipodocumento_id','movimiento.id','movimiento.comentario','movimiento.movimiento_id','movimiento.fecha','movimiento.numero','movimiento.total','movimiento.totalpagado','movimiento.totalpagadovisa','movimiento.totalpagadomaster','m2.numero as numeroticket',DB::raw('case when paciente.bussinesname is null then concat(paciente.apellidopaterno,\' \',paciente.nombres) else paciente.bussinesname end as paciente'), 'movimiento.total')->orderBy('movimiento.numero', 'asc');
        
        $listaventas           = $resultadoventas->get();

        if(count($listaventas)>0){
            $pdf::SetFont('helvetica','B',8.5);
            $pdf::Cell(281,7,'INGRESOS POR VENTAS',1,0,'L');
            $pdf::Ln();
            $subtotalefectivo = 0;
            $subtotalvisa = 0;
            $subtotalmaster = 0;
            foreach ($listaventas as $row) {                
                $row2 = Movimiento::where('movimiento_id', $row['id'])->limit(1)->first();
                $row3 = Movimiento::find($row['movimiento_id']);
                //$row3 = Movimiento::where('movimiento_id', $row2['id'])->limit(1)->first();
                if($row2['situacion'] != '') {
                    $detalles = Detallemovcaja::where('movimiento_id', $row3['id'])->get();  
                    $i = 0;              
                    foreach ($detalles as $detalle) {
                        if($i == 0) {
                            $pdf::SetFont('helvetica','',6);                   
                            $pdf::Cell(15,7*count($detalles ),utf8_decode($row['fecha']),1,0,'C');
                            $pdf::Cell(56,7*count($detalles),$row['paciente'],1,0,'L');
                            $pdf::Cell(8,7*count($detalles),$row->tipodocumento->abreviatura,1,0,'C');
                            $pdf::Cell(12,7*count($detalles),utf8_decode($row['serie'] .'-'. $row['numero']),1,0,'C');
                        } else {
                            $pdf::SetFont('helvetica','',6);                   
                            $pdf::Cell(15,7,'',0,0,'C');
                            $pdf::Cell(56,7,'',0,0,'L');
                            $pdf::Cell(8,7,'',0,0,'C');
                            $pdf::Cell(12,7,'',0,0,'C');
                        }                        
                        if($row3['plan_id'] != '') {
                            $pdf::Cell(40,7,substr($row3->plan->nombre,0,30) . '.',1,0,'L');
                        } else {
                            $pdf::Cell(40,7,'',1,0,'L');
                        }   
                        $nomdetalle = ''; 
                        if($detalle->servicio_id == 13) {
                            $nomdetalle .= '($) ';
                        }  
                        $nomdetalle .= $detalle->servicio->nombre;               
                        $pdf::Cell(60,7,substr($nomdetalle,0,40) . '.',1,0,'L');
                        $pdf::Cell(14,7,number_format($detalle->precio,2,',',''),1,0,'R');                    
                        if($i == 0) {
                            if($row2['situacion'] == 'N') {
                                $pdf::Cell(14,7*count($detalles),'',1,0,'L');
                                $valuetp = number_format($row2['totalpagado'],2,'.','');
                                $valuetpv = number_format($row2['totalpagadovisa'],2,'.','');
                                $valuetpm = number_format($row2['totalpagadomaster'],2,'.','');
                                if($valuetp == 0){$valuetp='';}
                                if($valuetpv == 0){$valuetpv='';}
                                if($valuetpm == 0){$valuetpm='';}
                                $pdf::Cell(14,7*count($detalles),$valuetp,1,0,'R');                    
                                $pdf::Cell(14,7*count($detalles),$valuetpv,1,0,'R');
                                $pdf::Cell(14,7*count($detalles),$valuetpm,1,0,'R');
                            } else {
                                $pdf::Cell(56,7*count($detalles),'ANULADO',1,0,'C');
                            }
                        } else {
                            $pdf::Cell(14,7,'',0,0,'L');
                            $pdf::Cell(14,7,'',0,0,'R');                    
                            $pdf::Cell(14,7,'',0,0,'R');
                            $pdf::Cell(14,7,'',0,0,'R');                        
                        }
                        $pdf::Cell(20,7,utf8_decode($detalle->persona->apellidopaterno),1,0,'C');                        
                        $pdf::Ln();
                        $i++;
                    }  
                    if($row2['situacion'] == 'N') {
                        if($row3->numeroserie2 != 'DOLAR') {
                            $totalvisa += number_format($row2['totalpagadovisa'],2,'.','');
                            $totalmaster += number_format($row2['totalpagadomaster'],2,'.','');
                            $totalefectivo += number_format($row2['totalpagado'],2,'.','');
                            $subtotalefectivo += number_format($row2['totalpagadovisa'],2,'.','');
                            $subtotalvisa     += number_format($row2['totalpagadomaster'],2,'.','');
                            $subtotalmaster   += number_format($row2['totalpagado'],2,'.','');
                        } else  {
                            $subtotaldolares += number_format($row2['total'],2,'.','');
                        }
                    }
                }
            } 
            $pdf::SetFont('helvetica','B',8.5);
            $pdf::Cell(205,7,'SUBTOTAL',1,0,'R');
            $pdf::Cell(14,7,number_format(0,2,'.',''),1,0,'R');
            $pdf::Cell(42,7,number_format($subtotalefectivo+$subtotalvisa+$subtotalmaster,2,'.',''),1,0,'R');
            $pdf::Ln();                   
        }      

        //Solo para cuotas

        $sucursal_id = Session::get('sucursal_id');
        $caja_id = $request->input('caja_id');
        $resultadoventas = Movimiento::leftjoin('movimiento as m2','movimiento.movimiento_id','=','m2.id')
                ->leftjoin('person as paciente', 'paciente.id', '=', 'movimiento.persona_id')
                ->where('movimiento.tipomovimiento_id', '=', 2)
                ->where('movimiento.tipodocumento_id', '=', 2)
                ->where('movimiento.situacion2','=','Z')
                //->where('movimiento.situacion','=','N')
                ->where('movimiento.sucursal_id', '=', $sucursal_id)
                ->whereBetween('movimiento.fecha', [$fi, $ff])
                ->where('movimiento.caja_id', '=', $caja_id);
        $resultadoventas = $resultadoventas->select('movimiento.numeroserie2','movimiento.movimiento_id','movimiento.situacion','movimiento.fecha','movimiento.numero','movimiento.total','movimiento.totalpagado','movimiento.totalpagadovisa','movimiento.totalpagadomaster','m2.numero as numeroticket',DB::raw('case when paciente.bussinesname is null then concat(paciente.apellidopaterno,\' \',paciente.nombres) else paciente.bussinesname end as paciente'), 'movimiento.total')->orderBy('movimiento.numero', 'asc');
        
        $listaventas           = $resultadoventas->get();

        if(count($listaventas)>0){
            $pdf::SetFont('helvetica','B',8.5);
            $pdf::Cell(281,7,'INGRESOS POR CUOTAS',1,0,'L');
            $pdf::Ln();
            $subtotalefectivo = 0;
            $subtotalvisa = 0;
            $subtotalmaster = 0;
            foreach ($listaventas as $row) { 
                $cuota = Movimiento::find($row['numeroserie2']);
                $pdf::SetFont('helvetica','',6);                   
                $pdf::Cell(15,7,utf8_decode($row['fecha']),1,0,'C');
                $pdf::Cell(56,7,$row['paciente'],1,0,'L');
                $pdf::Cell(8,7,'C',1,0,'C');
                $pdf::Cell(12,7,utf8_decode($cuota->numero),1,0,'C');
                $pdf::Cell(114,7,"PAGO DE CUOTA DE TICKET N° " . $row['numeroticket'],1,0,'L');

                if($row['situacion'] == 'N') {
                    $valuetp = number_format($row['totalpagado'],2,'.','');
                    $valuetpv = number_format($row['totalpagadovisa'],2,'.','');
                    $valuetpm = number_format($row['totalpagadomaster'],2,'.','');
                    if($valuetp == 0){$valuetp='';}
                    if($valuetpv == 0){$valuetpv='';}
                    if($valuetpm == 0){$valuetpm='';}
                    $pdf::Cell(14,7,'',1,0,'R');
                    $pdf::Cell(14,7,$valuetp,1,0,'R');                    
                    $pdf::Cell(14,7,$valuetpv,1,0,'R');
                    $pdf::Cell(14,7,$valuetpm,1,0,'R');
                    $totalvisa += number_format($row['totalpagadovisa'],2,'.','');
                    $totalmaster += number_format($row['totalpagadomaster'],2,'.','');
                    $totalefectivo += number_format($row['totalpagado'],2,'.','');
                    $subtotalefectivo += number_format($row['totalpagadovisa'],2,'.','');
                    $subtotalvisa     += number_format($row['totalpagadomaster'],2,'.','');
                    $subtotalmaster   += number_format($row['totalpagado'],2,'.','');
                } else {
                    $pdf::Cell(56,7,'ANULADO',1,0,'C');                    
                }  
                $pdf::Cell(20,7,utf8_decode("-"),1,0,'C');
                $pdf::Ln();                  
            } 
            $pdf::SetFont('helvetica','B',8.5);
            $pdf::Cell(205,7,'SUBTOTAL',1,0,'R');
            $pdf::Cell(14,7,number_format(0,2,'.',''),1,0,'R');
            $pdf::Cell(42,7,number_format($subtotalefectivo+$subtotalvisa+$subtotalmaster,2,'.',''),1,0,'R');
            $pdf::Ln();                   
        }

        //Solo para ventas de farmacia

        $listaventasfarmacia = Movimiento::leftjoin('movimiento as m2','movimiento.movimiento_id','=','m2.id')
                ->leftjoin('person as paciente', 'paciente.id', '=', 'movimiento.persona_id')
                ->leftjoin('conceptopago','conceptopago.id','=','movimiento.conceptopago_id')
                ->where('movimiento.sucursal_id', '=', $sucursal_id)
                ->where('movimiento.caja_id', '=', $caja_id)
                ->whereBetween('movimiento.fecha', [$fi, $ff])
                ->where('movimiento.ventafarmacia', '=', 'S');
        $listaventasfarmacia = $listaventasfarmacia->select('movimiento.situacion','movimiento.doctor_id','movimiento.serie','movimiento.id','movimiento.nombrepaciente','movimiento.voucher','movimiento.formapago','movimiento.comentario','movimiento.fecha','movimiento.numero','movimiento.total','movimiento.totalpagado','movimiento.totalpagadovisa','movimiento.totalpagadomaster','m2.numero as numeroticket',DB::raw('case when paciente.bussinesname is null then concat(paciente.apellidopaterno,\' \',paciente.nombres) else paciente.bussinesname end as paciente'), 'movimiento.total')->orderBy('movimiento.numero', 'asc');
        
        $listaventasfarmacia = $listaventasfarmacia->get();

        if(count($listaventasfarmacia)>0){
            $pdf::SetFont('helvetica','B',8.5);
            $pdf::Cell(281,7,'INGRESOS POR VENTAS',1,0,'L');
            $pdf::Ln();
            $subtotalefectivo = 0;
            $subtotalvisa = 0;
            $subtotalmaster = 0;
            foreach ($listaventasfarmacia as $row) { 
                $mov = Movimiento::where('movimiento_id', $row['id'])->limit(1)->first();
                if($mov !== NULL) {
                    $pdf::SetFont('helvetica','',6);                   
                    $pdf::Cell(15,7,utf8_decode($row['fecha']),1,0,'C');
                    if($row['paciente'] == '') {
                        $pdf::Cell(56,7,$row['nombrepaciente'],1,0,'L');
                    } else {
                        $pdf::Cell(56,7,$row['paciente'],1,0,'L');
                    }                
                    $pdf::Cell(8,7,$mov->tipodocumento->abreviatura,1,0,'C');
                    $pdf::Cell(12,7,utf8_decode($row['serie'] . '-' . $row['numero']),1,0,'C');
                    if($mov->empresa_id != '') {
                        $pdf::Cell(40,7,$mov->empresa->bussinesname,1,0,'L');                    
                    } else {
                        $pdf::Cell(40,7,'-',1,0,'C');
                    } 
                    $pdf::Cell(88,7,$mov->conceptopago->nombre.': '.$row['comentario'],1,0,'L'); 
                    if($row['situacion'] == 'N') {
                        $valuetp = number_format($row['totalpagado'],2,'.','');
                        $valuetpv = number_format($row['totalpagadovisa'],2,'.','');
                        $valuetpm = number_format($row['totalpagadomaster'],2,'.','');
                        if($valuetp == 0){$valuetp='';}
                        if($valuetpv == 0){$valuetpv='';}
                        if($valuetpm == 0){$valuetpm='';}
                        $pdf::Cell(14,7,$valuetp,1,0,'R');                    
                        $pdf::Cell(14,7,$valuetpv,1,0,'R');
                        $pdf::Cell(14,7,$valuetpm,1,0,'R');
                    } else {
                        $pdf::Cell(42,7,'ANULADO',1,0,'C');
                    } 
                    if($row['doctor_id'] != '') {
                        $pdf::Cell(20,7,$row->doctor->apellidopaterno,1,0,'C');
                    } else {
                        $pdf::Cell(20,7,utf8_decode("-"),1,0,'C');
                    }                
                    $pdf::Ln();
                    if($row['situacion'] == 'N') {
                        $totalvisa        += number_format($row['totalpagadovisa'],2,'.','');
                        $totalmaster      += number_format($row['totalpagadomaster'],2,'.','');
                        $totalefectivo    += number_format($row['totalpagado'],2,'.','');
                        $subtotalefectivo += number_format($row['totalpagadovisa'],2,'.','');
                        $subtotalvisa     += number_format($row['totalpagadomaster'],2,'.','');
                        $subtotalmaster   += number_format($row['totalpagado'],2,'.','');
                    }
                }                    
            } 
            $pdf::SetFont('helvetica','B',8.5);
            $pdf::Cell(205,7,'SUBTOTAL',1,0,'R');
            $pdf::Cell(14,7,number_format(0,2,'.',''),1,0,'R');
            $pdf::Cell(42,7,number_format($subtotalefectivo+$subtotalvisa+$subtotalmaster,2,'.',''),1,0,'R');
            $pdf::Ln();                 
        }

        //Solo para ingresos varios

        $listaingresosvarios = Movimiento::leftjoin('movimiento as m2','movimiento.movimiento_id','=','m2.id')
                ->leftjoin('person as paciente', 'paciente.id', '=', 'movimiento.persona_id')
                ->join('conceptopago','conceptopago.id','=','movimiento.conceptopago_id')
                ->where('movimiento.tipomovimiento_id', '=', 2)
                ->where('movimiento.sucursal_id', '=', $sucursal_id)
                ->where('movimiento.caja_id', '=', $caja_id)
                ->whereBetween('movimiento.fecha', [$fi, $ff])
                ->where('movimiento.situacion2', '=', 'Q')
                ->where('conceptopago.tipo', '=', 'I');
        $listaingresosvarios = $listaingresosvarios->select('movimiento.situacion','movimiento.voucher','movimiento.formapago','movimiento.comentario','movimiento.fecha','movimiento.numero','movimiento.total','movimiento.totalpagado','movimiento.totalpagadovisa','movimiento.totalpagadomaster','m2.numero as numeroticket',DB::raw('case when paciente.bussinesname is null then concat(paciente.apellidopaterno,\' \',paciente.nombres) else paciente.bussinesname end as paciente'), 'conceptopago.nombre', 'movimiento.total')->orderBy('movimiento.numero', 'asc');
        
        $listaingresosvarios = $listaingresosvarios->get();

        if(count($listaingresosvarios)>0){
            $pdf::SetFont('helvetica','B',8.5);
            $pdf::Cell(281,7,'INGRESOS VARIOS',1,0,'L');
            $pdf::Ln();
            $subtotalefectivo = 0;
            $subtotalvisa = 0;
            $subtotalmaster = 0;
            foreach ($listaingresosvarios as $row) { 
                $pdf::SetFont('helvetica','',6);                   
                $pdf::Cell(15,7,utf8_decode($row['fecha']),1,0,'C');
                $pdf::Cell(56,7,$row['paciente'],1,0,'L');
                $pdf::Cell(8,7,$row['formapago'],1,0,'C');
                $pdf::Cell(12,7,utf8_decode($row['voucher']),1,0,'C');
                $pdf::Cell(114,7,$row['nombre'].': '.$row['comentario'],1,0,'L');
                if($row['situacion'] == 'N') {
                    $valuetp = number_format($row['totalpagado'],2,'.','');
                    $valuetpv = number_format($row['totalpagadovisa'],2,'.','');
                    $valuetpm = number_format($row['totalpagadomaster'],2,'.','');
                    if($valuetp == 0){$valuetp='';}
                    if($valuetpv == 0){$valuetpv='';}
                    if($valuetpm == 0){$valuetpm='';}
                    $pdf::Cell(14,7,'',1,0,'R');                    
                    $pdf::Cell(14,7,$valuetp,1,0,'R');                    
                    $pdf::Cell(14,7,$valuetpv,1,0,'R');
                    $pdf::Cell(14,7,$valuetpm,1,0,'R');                    
                    $totalvisa += number_format($row['totalpagadovisa'],2,'.','');
                    $totalmaster += number_format($row['totalpagadomaster'],2,'.','');
                    $totalefectivo += number_format($row['totalpagado'],2,'.','');
                    $subtotalefectivo += number_format($row['totalpagadovisa'],2,'.','');
                    $subtotalvisa     += number_format($row['totalpagadomaster'],2,'.','');
                    $subtotalmaster   += number_format($row['totalpagado'],2,'.','');
                } else {
                    $pdf::Cell(56,7,'ANULADO',1,0,'C');
                }
                $pdf::Cell(20,7,utf8_decode("-"),1,0,'C');
                $pdf::Ln();
                    
            }   
            $pdf::SetFont('helvetica','B',8.5);
            $pdf::Cell(205,7,'SUBTOTAL',1,0,'R');
            $pdf::Cell(14,7,number_format(0,2,'.',''),1,0,'R');
            $pdf::Cell(42,7,number_format($subtotalefectivo+$subtotalvisa+$subtotalmaster,2,'.',''),1,0,'R');
            $pdf::Ln();                 
        }

        //Solo para egresos

        $resultadoegresos        = Movimiento::leftjoin('person as paciente', 'paciente.id', '=', 'movimiento.persona_id')
            ->join('person as responsable', 'responsable.id', '=', 'movimiento.responsable_id')
            ->join('conceptopago','conceptopago.id','=','movimiento.conceptopago_id')
            ->leftjoin('movimiento as m2','m2.movimiento_id','=','movimiento.id')
            ->where('movimiento.caja_id', '=', $caja_id)
            ->where('movimiento.sucursal_id','=',$sucursal_id)
            ->whereBetween('movimiento.fecha', [$fi, $ff])
            ->whereNull('movimiento.cajaapertura_id')
            ->where(function($query){
                $query
                    ->whereNotIn('movimiento.conceptopago_id',[31])
                    ->orWhere('m2.situacion','<>','R');
            })
            //->where('movimiento.situacion', '<>', 'A')
            //->where('movimiento.situacion', '<>', 'R')
            ->where('conceptopago.tipo', '=', 'E')
            ->where('movimiento.situacion2', '=', 'Q');

        $resultadoegresos        = $resultadoegresos->select('movimiento.*','m2.situacion as situacion2','responsable.nombres as responsable2',DB::raw('case when paciente.bussinesname is null then concat(paciente.apellidopaterno,\' \',paciente.nombres) else paciente.bussinesname end as paciente'), 'conceptopago.nombre')->orderBy('conceptopago.tipo', 'asc')->orderBy('conceptopago.orden', 'asc')->orderBy('conceptopago.id', 'asc')->orderBy('movimiento.tipotarjeta', 'asc')->orderBy('movimiento.numero', 'asc');

        $listaegresos = $resultadoegresos->get();

        if(count($listaegresos)>0){
            $pdf::SetFont('helvetica','B',8.5);
            $pdf::Cell(281,7,'EGRESOS',1,0,'L');
            $pdf::Ln();
            $subtotalegresos = 0;
            foreach ($listaegresos as $row) { 
                $pdf::SetFont('helvetica','',6);                   
                $pdf::Cell(15,7,utf8_decode($row['fecha']),1,0,'C');
                $pdf::Cell(56,7,$row['paciente'],1,0,'L');
                $pdf::Cell(8,7,$row['formapago'],1,0,'C');
                $pdf::Cell(12,7,utf8_decode($row['voucher']),1,0,'C');
                $pdf::Cell(114,7,$row['nombre'].': '.$row['comentario'],1,0,'L');
                if($row['situacion'] == 'N') {
                    $pdf::Cell(14,7,number_format($row['total'],2,'.',''),1,0,'R');
                    $pdf::Cell(42,7,utf8_decode(""),1,0,'C');                    
                    $subtotalegresos += number_format($row['total'],2,'.','');
                } else {
                    $pdf::Cell(56,7,utf8_decode("ANULADO"),1,0,'C');
                }  
                $pdf::Cell(20,7,utf8_decode("-"),1,0,'C');
                $pdf::Ln();              
            }      
            $pdf::SetFont('helvetica','B',8.5);
            $pdf::Cell(205,7,'SUBTOTAL',1,0,'R');
            $pdf::Cell(14,7,number_format($subtotalegresos,2,'.',''),1,0,'R');
            $pdf::Cell(42,7,number_format(0,2,'.',''),1,0,'R');
            $pdf::Ln();                  
        }

        //Solo para egresos por compra farmacia

        if($caja->nombre == 'FARMACIA') {
            $resultadoegresos        = Movimiento::leftjoin('person as paciente', 'paciente.id', '=', 'movimiento.persona_id')
                ->join('person as responsable', 'responsable.id', '=', 'movimiento.responsable_id')
                ->join('conceptopago','conceptopago.id','=','movimiento.conceptopago_id')
                ->join('tipodocumento','tipodocumento.id','=','movimiento.tipodocumento_id')
                ->leftjoin('movimiento as m2','m2.movimiento_id','=','movimiento.id')
                ->where('movimiento.caja_id', '=', $caja_id)
                ->whereNull('m2.caja_id')
                ->where('movimiento.sucursal_id','=',$sucursal_id)
                ->where('movimiento.tipomovimiento_id','=',2)
                ->where('paciente.dni','=',null)
                //->where('m2.tipomovimiento_id','=',3)
                ->whereBetween('movimiento.fecha', [$fi, $ff])
                ->whereNull('movimiento.cajaapertura_id')
                /*->where(function($query){
                    $query
                        ->whereNotIn('movimiento.conceptopago_id',[31])
                        ->orWhere('m2.situacion','<>','R');
                })*/
                //->where('movimiento.situacion', '<>', 'A')
                //->where('movimiento.situacion', '<>', 'R')
                ->where('conceptopago.tipo', '=', 'E');
                //->where('movimiento.situacion2', '=', 'Q');

            $resultadoegresos        = $resultadoegresos->select('movimiento.*','tipodocumento.abreviatura as formapago2','responsable.nombres as responsable2',DB::raw('concat(paciente.ruc,\' - \',paciente.bussinesname) as paciente'), 'conceptopago.nombre')->orderBy('conceptopago.tipo', 'asc')->orderBy('conceptopago.orden', 'asc')->orderBy('conceptopago.id', 'asc')->orderBy('movimiento.tipotarjeta', 'asc')->orderBy('movimiento.numero', 'asc');

            $listaegresos = $resultadoegresos->get();

            if(count($listaegresos)>0){
                $pdf::SetFont('helvetica','B',8.5);
                $pdf::Cell(281,7,'EGRESOS POR COMPRA',1,0,'L');
                $pdf::Ln();
                $subtotalegresoscompra = 0;
                foreach ($listaegresos as $row) { 
                    if($row['situacion2'] == null){
                        $pdf::SetFont('helvetica','',6);                   
                        $pdf::Cell(15,7,utf8_decode($row['fecha']),1,0,'C');
                        $pdf::Cell(56,7,$row['paciente'],1,0,'L');
                        $pdf::Cell(8,7,$row['formapago2'],1,0,'C');
                        $pdf::Cell(12,7,utf8_decode($row['voucher']),1,0,'C');
                        $pdf::Cell(114,7,$row['nombre'].': '.$row['comentario'],1,0,'L');
                        if($row['situacion'] == 'N') {
                            $pdf::Cell(14,7,number_format($row['total'],2,'.',''),1,0,'R');
                            $pdf::Cell(42,7,utf8_decode(""),1,0,'C');                    
                            $subtotalegresoscompra += number_format($row['total'],2,'.','');
                            $subtotalegresos += number_format($row['total'],2,'.','');
                        } else {
                            $pdf::Cell(56,7,utf8_decode("ANULADO"),1,0,'C');
                        }  
                        $pdf::Cell(20,7,utf8_decode("-"),1,0,'C');
                        $pdf::Ln();     
                    }         
                }      
                $pdf::SetFont('helvetica','B',8.5);
                $pdf::Cell(205,7,'SUBTOTAL',1,0,'R');
                $pdf::Cell(14,7,number_format($subtotalegresoscompra,2,'.',''),1,0,'R');
                $pdf::Cell(42,7,number_format(0,2,'.',''),1,0,'R');
                $pdf::Ln();                  
            }
        }

        $pdf::SetFont('helvetica','',7);   
        $pdf::Ln();
        $pdf::Ln();
        $pdf::Cell(120,7,('RESPONSABLE: '.$responsable),0,0,'L');
        $pdf::SetFont('helvetica','B',9);
        $pdf::Cell(50,7,utf8_decode("RESUMEN DE CAJA"),1,0,'C');
        $pdf::Ln();
        $pdf::Cell(120,7,utf8_decode(""),0,0,'C');
        $pdf::Cell(30,7,utf8_decode("INGRESOS :"),1,0,'L');
        $pdf::Cell(20,7,number_format($totalefectivo + $totalmaster + $totalvisa,2,'.',''),1,0,'R');
        $pdf::Ln();
        $pdf::Cell(120,7,utf8_decode(""),0,0,'C');
        $pdf::Cell(30,7,utf8_decode("Efectivo :"),1,0,'L');
        $pdf::Cell(20,7,number_format($totalefectivo,2,'.',''),1,0,'R');
        $pdf::Ln();
        $pdf::Cell(120,7,utf8_decode(""),0,0,'C');
        $pdf::Cell(30,7,utf8_decode("Master :"),1,0,'L');
        $pdf::Cell(20,7,number_format($totalmaster,2,'.',''),1,0,'R');
        $pdf::Ln();
        $pdf::Cell(120,7,utf8_decode(""),0,0,'C');
        $pdf::Cell(30,7,utf8_decode("Visa :"),1,0,'L');
        $pdf::Cell(20,7,number_format($totalvisa,2,'.',''),1,0,'R');
        $pdf::Ln();
        $pdf::Cell(120,7,utf8_decode(""),0,0,'C');
        $pdf::Cell(30,7,utf8_decode("EGRESOS :"),1,0,'L');
        $pdf::Cell(20,7,number_format($subtotalegresos,2,'.',''),1,0,'R');
        $pdf::Ln();
        $pdf::Cell(120,7,utf8_decode(""),0,0,'C');
        $pdf::Cell(30,7,utf8_decode("SALDO (S/.) :"),1,0,'L');
        $pdf::Cell(20,7,number_format($totalefectivo + $totalmaster + $totalvisa - $subtotalegresos,2,'.',''),1,0,'R');
        $pdf::Ln();
        $pdf::Cell(120,7,utf8_decode(""),0,0,'C');
        $pdf::Cell(30,7,utf8_decode("SALDO ($) :"),1,0,'L');
        $pdf::Cell(20,7,number_format($subtotaldolares,2,'.',''),1,0,'R');
        /*$pdf::Cell(120,7,utf8_decode(""),0,0,'C');
        $pdf::Cell(30,7,utf8_decode("GARANTIA :"),1,0,'L');
        $pdf::Cell(20,7,number_format($garantia,2,'.',''),1,0,'R');*/
        $pdf::Ln();
        $pdf::Output('ListaCaja.pdf');
    }

    //Por Cajas separadas
    public function pdfDetalleCierreF2(Request $request){

        //sucursal_id
        $sucursal_id = Session::get('sucursal_id');

        $caja    = Caja::find($request->input('caja_id'));
        $caja_id = Libreria::getParam($request->input('caja_id'),'1');

        $fi = Libreria::getParam($request->input('fi'),'1');
        $ff = Libreria::getParam($request->input('ff'),'1');

        $aperturas = Movimiento::where('conceptopago_id', 1)->where('caja_id', $caja_id)->where('sucursal_id', $sucursal_id)->whereBetween('fecha', [$fi, $ff])->get();

        //Comprobamos si la ultima fecha tiene cierre

        $cierrefinal = Movimiento::where('conceptopago_id', 2)->where('caja_id', $caja_id)->where('sucursal_id', $sucursal_id)->where('fecha', '=', $ff)->get();

        $numcajas = count($aperturas);
        if(count($cierrefinal) == 0) {
            //Si no hay cierre en la ultima fecha, no considero la ultima caja
            $numcajas--;
        }


        $user=Auth::user();
        $responsable = $user->login;

        $nomcierre = '';
        $nomcierre = 'Clínica Especialidades'; 
        if($sucursal_id == 1) {
            $nomcierre = 'BM Clínica de Ojos';
        }  
        if($caja->nombre == 'FARMACIA') {
            $nomcierre = 'Farmacia - ' . $nomcierre;
        }
        $pdf = new TCPDF();
        //$pdf::SetIma�
        $pdf::SetTitle('Detalle Cierre por cajas ' . $nomcierre);

        $cont = 1;
        foreach ($aperturas as $apertura) {
            $totalvisa     = 0;
            $totalmaster   = 0;
            $totalefectivo = 0;
            $totalegresos  = 0;
            $subtotalegresos = 0;
            $subtotaldolares = 0;

            $pdf::AddPage('L');
            $pdf::SetFont('helvetica','B',12);
            $pdf::Cell(0,10,"Detalle de Cierre por cajas " . $nomcierre . ' / Apertura N. ' . $apertura->numero,0,0,'C');
            $pdf::Ln();
            $pdf::SetFont('helvetica','B',7);
            $pdf::Cell(15,7,utf8_decode("FECHA"),1,0,'C');
            $pdf::Cell(56,7,utf8_decode("PERSONA"),1,0,'C');
            $pdf::Cell(20,7,utf8_decode("NRO"),1,0,'C');
            $pdf::Cell(40,7,utf8_decode("EMPRESA"),1,0,'C');
            $pdf::Cell(60,7,utf8_decode("CONCEPTO"),1,0,'C');
            $pdf::Cell(14,7,utf8_decode("PRECIO"),1,0,'C');
            $pdf::Cell(14,7,utf8_decode("EGRESO"),1,0,'C');
            $pdf::Cell(42,7,utf8_decode("INGRESO"),1,0,'C');
            $pdf::Cell(20,7,utf8_decode("DOCTOR"),1,0,'C');
            $pdf::Ln();
            $pdf::Cell(219,7,utf8_decode(""),1,0,'C');
            $pdf::Cell(14,7,utf8_decode("EFECTIVO"),1,0,'C');
            $pdf::Cell(14,7,utf8_decode("VISA"),1,0,'C');
            $pdf::Cell(14,7,utf8_decode("MASTER"),1,0,'C');
            $pdf::Cell(20,7,utf8_decode(""),1,0,'C');
            $pdf::Ln();

            //Cierre de la presente caja
            $cierre = Movimiento::select('id')
                    ->where('conceptopago_id', 2)
                    ->where('caja_id', $caja_id)
                    ->where('sucursal_id', $sucursal_id)
                    ->where('id' , '>', $apertura->id)
                    ->limit(1)->first();

            $ingreso=0;
            $egreso=0;
            $transferenciai=0;
            $transferenciae=0;
            $garantia=0;
            $efectivo=0;
            $visa=0;
            $master=0;

            //Pagos de tickets   

            $sucursal_id = Session::get('sucursal_id');
            $caja_id = $request->input('caja_id');
            $resultadoventas = Movimiento::leftjoin('movimiento as m2','movimiento.movimiento_id','=','m2.id')
                    ->leftjoin('person as paciente', 'paciente.id', '=', 'movimiento.persona_id')
                    //->where('movimiento.situacion','=','N')
                    ->where('movimiento.ventafarmacia','=','N')
                    ->where('movimiento.sucursal_id', '=', $sucursal_id)
                    ->whereBetween('movimiento.id', [$apertura->id,(int)$cierre['id']])
                    ->where('movimiento.caja_id', '=', $caja_id);
            $resultadoventas = $resultadoventas->select('movimiento.plan_id','movimiento.doctor_id','movimiento.serie','movimiento.tipodocumento_id','movimiento.id','movimiento.comentario','movimiento.movimiento_id','movimiento.fecha','movimiento.numero','movimiento.total','movimiento.totalpagado','movimiento.totalpagadovisa','movimiento.totalpagadomaster','m2.numero as numeroticket',DB::raw('case when paciente.bussinesname is null then concat(paciente.apellidopaterno,\' \',paciente.nombres) else paciente.bussinesname end as paciente'), 'movimiento.total')->orderBy('movimiento.numero', 'asc');
            
            $listaventas           = $resultadoventas->get();

            if(count($listaventas)>0){
                $pdf::SetFont('helvetica','B',8.5);
                $pdf::Cell(281,7,'INGRESOS POR VENTAS',1,0,'L');
                $pdf::Ln();
                $subtotalefectivo = 0;
                $subtotalvisa = 0;
                $subtotalmaster = 0;
                foreach ($listaventas as $row) {                
                    $row2 = Movimiento::where('movimiento_id', $row['id'])->limit(1)->first();
                    $row3 = Movimiento::find($row['movimiento_id']);
                    //$row3 = Movimiento::where('movimiento_id', $row2['id'])->limit(1)->first();
                    if($row2['situacion'] != '') {
                        $detalles = Detallemovcaja::where('movimiento_id', $row3['id'])->get();  
                        $i = 0;              
                        foreach ($detalles as $detalle) {
                            if($i == 0) {
                                $pdf::SetFont('helvetica','',6);                   
                                $pdf::Cell(15,7*count($detalles ),utf8_decode($row['fecha']),1,0,'C');
                                $pdf::Cell(56,7*count($detalles),$row['paciente'],1,0,'L');
                                $pdf::Cell(8,7*count($detalles),$row->tipodocumento->abreviatura,1,0,'C');
                                $pdf::Cell(12,7*count($detalles),utf8_decode($row['serie'] .'-'. $row['numero']),1,0,'C');
                            } else {
                                $pdf::SetFont('helvetica','',6);                   
                                $pdf::Cell(15,7,'',0,0,'C');
                                $pdf::Cell(56,7,'',0,0,'L');
                                $pdf::Cell(8,7,'',0,0,'C');
                                $pdf::Cell(12,7,'',0,0,'C');
                            }                        
                            if($row3['plan_id'] != '') {
                                $pdf::Cell(40,7,substr($row3->plan->nombre,0,30) . '.',1,0,'L');
                            } else {
                                $pdf::Cell(40,7,'',1,0,'L');
                            } 
                            $nomdetalle = ''; 
                            if($detalle->servicio_id == 13) {
                                $nomdetalle .= '($) ';
                            }  
                            $nomdetalle .= $detalle->servicio->nombre;                   
                            $pdf::Cell(60,7,substr($nomdetalle,0,40) . '.',1,0,'L');
                            $pdf::Cell(14,7,number_format($detalle->precio,2,',',''),1,0,'R');                    
                            if($i == 0) {
                                if($row2['situacion'] == 'N') {
                                    $pdf::Cell(14,7*count($detalles),'',1,0,'L');
                                    $valuetp = number_format($row2['totalpagado'],2,'.','');
                                    $valuetpv = number_format($row2['totalpagadovisa'],2,'.','');
                                    $valuetpm = number_format($row2['totalpagadomaster'],2,'.','');
                                    if($valuetp == 0){$valuetp='';}
                                    if($valuetpv == 0){$valuetpv='';}
                                    if($valuetpm == 0){$valuetpm='';}
                                    $pdf::Cell(14,7*count($detalles),$valuetp,1,0,'R');                    
                                    $pdf::Cell(14,7*count($detalles),$valuetpv,1,0,'R');
                                    $pdf::Cell(14,7*count($detalles),$valuetpm,1,0,'R');
                                } else {
                                    $pdf::Cell(56,7*count($detalles),'ANULADO',1,0,'C');
                                }
                            } else {
                                $pdf::Cell(14,7,'',0,0,'L');
                                $pdf::Cell(14,7,'',0,0,'R');                    
                                $pdf::Cell(14,7,'',0,0,'R');
                                $pdf::Cell(14,7,'',0,0,'R');                        
                            }
                            $pdf::Cell(20,7,utf8_decode($detalle->persona->apellidopaterno),1,0,'C');                        
                            $pdf::Ln();
                            $i++;
                        }  
                        if($row2['situacion'] == 'N') { 
                            if($row3->numeroserie2 != 'DOLAR') {
                                $totalvisa += number_format($row2['totalpagadovisa'],2,'.','');
                                $totalmaster += number_format($row2['totalpagadomaster'],2,'.','');
                                $totalefectivo += number_format($row2['totalpagado'],2,'.','');
                                $subtotalefectivo += number_format($row2['totalpagadovisa'],2,'.','');
                                $subtotalvisa     += number_format($row2['totalpagadomaster'],2,'.','');
                                $subtotalmaster   += number_format($row2['totalpagado'],2,'.','');
                            } else {
                                $subtotaldolares += number_format($row2['total'],2,'.','');
                            }
                        }
                    }
                } 
                $pdf::SetFont('helvetica','B',8.5);
                $pdf::Cell(205,7,'SUBTOTAL',1,0,'R');
                $pdf::Cell(14,7,number_format(0,2,'.',''),1,0,'R');
                $pdf::Cell(42,7,number_format($subtotalefectivo+$subtotalvisa+$subtotalmaster,2,'.',''),1,0,'R');
                $pdf::Ln();                   
            }      

            //Solo para cuotas

            $sucursal_id = Session::get('sucursal_id');
            $caja_id = $request->input('caja_id');
            $resultadoventas = Movimiento::leftjoin('movimiento as m2','movimiento.movimiento_id','=','m2.id')
                    ->leftjoin('person as paciente', 'paciente.id', '=', 'movimiento.persona_id')
                    ->where('movimiento.tipomovimiento_id', '=', 2)
                    ->where('movimiento.tipodocumento_id', '=', 2)
                    ->where('movimiento.situacion2','=','Z')
                    //->where('movimiento.situacion','=','N')
                    ->where('movimiento.sucursal_id', '=', $sucursal_id)
                    ->whereBetween('movimiento.id', [$apertura->id,(int)$cierre['id']])
                    ->where('movimiento.caja_id', '=', $caja_id);
            $resultadoventas = $resultadoventas->select('movimiento.numeroserie2','movimiento.movimiento_id','movimiento.situacion','movimiento.fecha','movimiento.numero','movimiento.total','movimiento.totalpagado','movimiento.totalpagadovisa','movimiento.totalpagadomaster','m2.numero as numeroticket',DB::raw('case when paciente.bussinesname is null then concat(paciente.apellidopaterno,\' \',paciente.nombres) else paciente.bussinesname end as paciente'), 'movimiento.total')->orderBy('movimiento.numero', 'asc');
            
            $listaventas           = $resultadoventas->get();

            if(count($listaventas)>0){
                $pdf::SetFont('helvetica','B',8.5);
                $pdf::Cell(281,7,'INGRESOS POR CUOTAS',1,0,'L');
                $pdf::Ln();
                $subtotalefectivo = 0;
                $subtotalvisa = 0;
                $subtotalmaster = 0;
                foreach ($listaventas as $row) { 
                    $cuota = Movimiento::find($row['numeroserie2']);
                    $pdf::SetFont('helvetica','',6);                   
                    $pdf::Cell(15,7,utf8_decode($row['fecha']),1,0,'C');
                    $pdf::Cell(56,7,$row['paciente'],1,0,'L');
                    $pdf::Cell(8,7,'C',1,0,'C');
                    $pdf::Cell(12,7,utf8_decode($cuota->numero),1,0,'C');
                    $pdf::Cell(114,7,"PAGO DE CUOTA DE TICKET N° " . $row['numeroticket'],1,0,'L');

                    if($row['situacion'] == 'N') {
                        $valuetp = number_format($row['totalpagado'],2,'.','');
                        $valuetpv = number_format($row['totalpagadovisa'],2,'.','');
                        $valuetpm = number_format($row['totalpagadomaster'],2,'.','');
                        if($valuetp == 0){$valuetp='';}
                        if($valuetpv == 0){$valuetpv='';}
                        if($valuetpm == 0){$valuetpm='';}
                        $pdf::Cell(14,7,'',1,0,'R');
                        $pdf::Cell(14,7,$valuetp,1,0,'R');                    
                        $pdf::Cell(14,7,$valuetpv,1,0,'R');
                        $pdf::Cell(14,7,$valuetpm,1,0,'R');
                        $totalvisa += number_format($row['totalpagadovisa'],2,'.','');
                        $totalmaster += number_format($row['totalpagadomaster'],2,'.','');
                        $totalefectivo += number_format($row['totalpagado'],2,'.','');
                        $subtotalefectivo += number_format($row['totalpagadovisa'],2,'.','');
                        $subtotalvisa     += number_format($row['totalpagadomaster'],2,'.','');
                        $subtotalmaster   += number_format($row['totalpagado'],2,'.','');
                    } else {
                        $pdf::Cell(56,7,'ANULADO',1,0,'C');                    
                    }  
                    $pdf::Cell(20,7,utf8_decode("-"),1,0,'C');
                    $pdf::Ln();                  
                } 
                $pdf::SetFont('helvetica','B',8.5);
                $pdf::Cell(205,7,'SUBTOTAL',1,0,'R');
                $pdf::Cell(14,7,number_format(0,2,'.',''),1,0,'R');
                $pdf::Cell(42,7,number_format($subtotalefectivo+$subtotalvisa+$subtotalmaster,2,'.',''),1,0,'R');
                $pdf::Ln();                   
            }

            //Solo para ventas de farmacia

            $listaventasfarmacia = Movimiento::leftjoin('movimiento as m2','movimiento.movimiento_id','=','m2.id')
                    ->leftjoin('person as paciente', 'paciente.id', '=', 'movimiento.persona_id')
                    ->leftjoin('conceptopago','conceptopago.id','=','movimiento.conceptopago_id')
                    ->where('movimiento.sucursal_id', '=', $sucursal_id)
                    ->where('movimiento.caja_id', '=', $caja_id)
                    ->whereBetween('movimiento.id', [$apertura->id,(int)$cierre['id']])
                    ->where('movimiento.ventafarmacia', '=', 'S');
            $listaventasfarmacia = $listaventasfarmacia->select('movimiento.situacion','movimiento.doctor_id','movimiento.serie','movimiento.id','movimiento.nombrepaciente','movimiento.voucher','movimiento.formapago','movimiento.comentario','movimiento.fecha','movimiento.numero','movimiento.total','movimiento.totalpagado','movimiento.totalpagadovisa','movimiento.totalpagadomaster','m2.numero as numeroticket',DB::raw('case when paciente.bussinesname is null then concat(paciente.apellidopaterno,\' \',paciente.nombres) else paciente.bussinesname end as paciente'), 'movimiento.total')->orderBy('movimiento.numero', 'asc');
            
            $listaventasfarmacia = $listaventasfarmacia->get();

            if(count($listaventasfarmacia)>0){
                $pdf::SetFont('helvetica','B',8.5);
                $pdf::Cell(281,7,'INGRESOS POR VENTAS',1,0,'L');
                $pdf::Ln();
                $subtotalefectivo = 0;
                $subtotalvisa = 0;
                $subtotalmaster = 0;
                foreach ($listaventasfarmacia as $row) { 
                    $mov = Movimiento::where('movimiento_id', $row['id'])->limit(1)->first();
                    if ($mov !== NULL) {
                        $pdf::SetFont('helvetica','',6);                   
                        $pdf::Cell(15,7,utf8_decode($row['fecha']),1,0,'C');
                        if($row['paciente'] == '') {
                            $pdf::Cell(56,7,$row['nombrepaciente'],1,0,'L');
                        } else {
                            $pdf::Cell(56,7,$row['paciente'],1,0,'L');
                        }                
                        $pdf::Cell(8,7,$mov->tipodocumento->abreviatura,1,0,'C');
                        $pdf::Cell(12,7,utf8_decode($row['serie'] . '-' . $row['numero']),1,0,'C');
                        if($mov->empresa_id != '') {
                            $pdf::Cell(40,7,$mov->empresa->bussinesname,1,0,'L');                    
                        } else {
                            $pdf::Cell(40,7,'-',1,0,'C');
                        } 
                        $pdf::Cell(88,7,$mov->conceptopago->nombre.': '.$row['comentario'],1,0,'L'); 
                        if($row['situacion'] == 'N') {
                            $valuetp = number_format($row['totalpagado'],2,'.','');
                            $valuetpv = number_format($row['totalpagadovisa'],2,'.','');
                            $valuetpm = number_format($row['totalpagadomaster'],2,'.','');
                            if($valuetp == 0){$valuetp='';}
                            if($valuetpv == 0){$valuetpv='';}
                            if($valuetpm == 0){$valuetpm='';}
                            $pdf::Cell(14,7,$valuetp,1,0,'R');                    
                            $pdf::Cell(14,7,$valuetpv,1,0,'R');
                            $pdf::Cell(14,7,$valuetpm,1,0,'R');
                        } else {
                            $pdf::Cell(42,7,'ANULADO',1,0,'C');
                        } 
                        if($row['doctor_id'] != '') {
                            $pdf::Cell(20,7,$row->doctor->apellidopaterno,1,0,'C');
                        } else {
                            $pdf::Cell(20,7,utf8_decode("-"),1,0,'C');
                        }                
                        $pdf::Ln();
                        if($row['situacion'] == 'N') {
                            $totalvisa        += number_format($row['totalpagadovisa'],2,'.','');
                            $totalmaster      += number_format($row['totalpagadomaster'],2,'.','');
                            $totalefectivo    += number_format($row['totalpagado'],2,'.','');
                            $subtotalefectivo += number_format($row['totalpagadovisa'],2,'.','');
                            $subtotalvisa     += number_format($row['totalpagadomaster'],2,'.','');
                            $subtotalmaster   += number_format($row['totalpagado'],2,'.','');
                        }
                    }
                } 
                $pdf::SetFont('helvetica','B',8.5);
                $pdf::Cell(205,7,'SUBTOTAL',1,0,'R');
                $pdf::Cell(14,7,number_format(0,2,'.',''),1,0,'R');
                $pdf::Cell(42,7,number_format($subtotalefectivo+$subtotalvisa+$subtotalmaster,2,'.',''),1,0,'R');
                $pdf::Ln();                 
            }

            //Solo para ingresos varios

            $listaingresosvarios = Movimiento::leftjoin('movimiento as m2','movimiento.movimiento_id','=','m2.id')
                    ->leftjoin('person as paciente', 'paciente.id', '=', 'movimiento.persona_id')
                    ->join('conceptopago','conceptopago.id','=','movimiento.conceptopago_id')
                    ->where('movimiento.tipomovimiento_id', '=', 2)
                    ->where('movimiento.sucursal_id', '=', $sucursal_id)
                    ->where('movimiento.caja_id', '=', $caja_id)
                    ->whereBetween('movimiento.id', [$apertura->id,(int)$cierre['id']])
                    ->where('movimiento.situacion2', '=', 'Q')
                    ->where('conceptopago.tipo', '=', 'I');
            $listaingresosvarios = $listaingresosvarios->select('movimiento.situacion','movimiento.voucher','movimiento.formapago','movimiento.comentario','movimiento.fecha','movimiento.numero','movimiento.total','movimiento.totalpagado','movimiento.totalpagadovisa','movimiento.totalpagadomaster','m2.numero as numeroticket',DB::raw('case when paciente.bussinesname is null then concat(paciente.apellidopaterno,\' \',paciente.nombres) else paciente.bussinesname end as paciente'), 'conceptopago.nombre', 'movimiento.total')->orderBy('movimiento.numero', 'asc');
            
            $listaingresosvarios = $listaingresosvarios->get();

            if(count($listaingresosvarios)>0){
                $pdf::SetFont('helvetica','B',8.5);
                $pdf::Cell(281,7,'INGRESOS VARIOS',1,0,'L');
                $pdf::Ln();
                $subtotalefectivo = 0;
                $subtotalvisa = 0;
                $subtotalmaster = 0;
                foreach ($listaingresosvarios as $row) { 
                    $pdf::SetFont('helvetica','',6);                   
                    $pdf::Cell(15,7,utf8_decode($row['fecha']),1,0,'C');
                    $pdf::Cell(56,7,$row['paciente'],1,0,'L');
                    $pdf::Cell(8,7,$row['formapago'],1,0,'C');
                    $pdf::Cell(12,7,utf8_decode($row['voucher']),1,0,'C');
                    $pdf::Cell(114,7,$row['nombre'].': '.$row['comentario'],1,0,'L');
                    if($row['situacion'] == 'N') {
                        $valuetp = number_format($row['totalpagado'],2,'.','');
                        $valuetpv = number_format($row['totalpagadovisa'],2,'.','');
                        $valuetpm = number_format($row['totalpagadomaster'],2,'.','');
                        if($valuetp == 0){$valuetp='';}
                        if($valuetpv == 0){$valuetpv='';}
                        if($valuetpm == 0){$valuetpm='';}
                        $pdf::Cell(14,7,'',1,0,'R');                    
                        $pdf::Cell(14,7,$valuetp,1,0,'R');                    
                        $pdf::Cell(14,7,$valuetpv,1,0,'R');
                        $pdf::Cell(14,7,$valuetpm,1,0,'R');                    
                        $totalvisa += number_format($row['totalpagadovisa'],2,'.','');
                        $totalmaster += number_format($row['totalpagadomaster'],2,'.','');
                        $totalefectivo += number_format($row['totalpagado'],2,'.','');
                        $subtotalefectivo += number_format($row['totalpagadovisa'],2,'.','');
                        $subtotalvisa     += number_format($row['totalpagadomaster'],2,'.','');
                        $subtotalmaster   += number_format($row['totalpagado'],2,'.','');
                    } else {
                        $pdf::Cell(56,7,'ANULADO',1,0,'C');
                    }
                    $pdf::Cell(20,7,utf8_decode("-"),1,0,'C');
                    $pdf::Ln();
                        
                }   
                $pdf::SetFont('helvetica','B',8.5);
                $pdf::Cell(205,7,'SUBTOTAL',1,0,'R');
                $pdf::Cell(14,7,number_format(0,2,'.',''),1,0,'R');
                $pdf::Cell(42,7,number_format($subtotalefectivo+$subtotalvisa+$subtotalmaster,2,'.',''),1,0,'R');
                $pdf::Ln();                 
            }

            //Solo para egresos

            $resultadoegresos        = Movimiento::leftjoin('person as paciente', 'paciente.id', '=', 'movimiento.persona_id')
                ->join('person as responsable', 'responsable.id', '=', 'movimiento.responsable_id')
                ->join('conceptopago','conceptopago.id','=','movimiento.conceptopago_id')
                ->leftjoin('movimiento as m2','m2.movimiento_id','=','movimiento.id')
                ->where('movimiento.caja_id', '=', $caja_id)
                ->where('movimiento.sucursal_id','=',$sucursal_id)
                ->whereBetween('movimiento.id', [$apertura->id,(int)$cierre['id']])
                ->whereNull('movimiento.cajaapertura_id')
                ->where(function($query){
                    $query
                        ->whereNotIn('movimiento.conceptopago_id',[31])
                        ->orWhere('m2.situacion','<>','R');
                })
                //->where('movimiento.situacion', '<>', 'A')
                //->where('movimiento.situacion', '<>', 'R')
                ->where('conceptopago.tipo', '=', 'E')
                ->where('movimiento.situacion2', '=', 'Q');

            $resultadoegresos        = $resultadoegresos->select('movimiento.*','m2.situacion as situacion2','responsable.nombres as responsable2',DB::raw('case when paciente.bussinesname is null then concat(paciente.apellidopaterno,\' \',paciente.nombres) else paciente.bussinesname end as paciente'), 'conceptopago.nombre')->orderBy('conceptopago.tipo', 'asc')->orderBy('conceptopago.orden', 'asc')->orderBy('conceptopago.id', 'asc')->orderBy('movimiento.tipotarjeta', 'asc')->orderBy('movimiento.numero', 'asc');

            $listaegresos = $resultadoegresos->get();

            if(count($listaegresos)>0){
                $pdf::SetFont('helvetica','B',8.5);
                $pdf::Cell(281,7,'EGRESOS',1,0,'L');
                $pdf::Ln();
                $subtotalegresos = 0;
                foreach ($listaegresos as $row) { 
                    $pdf::SetFont('helvetica','',6);                   
                    $pdf::Cell(15,7,utf8_decode($row['fecha']),1,0,'C');
                    $pdf::Cell(56,7,$row['paciente'],1,0,'L');
                    $pdf::Cell(8,7,$row['formapago'],1,0,'C');
                    $pdf::Cell(12,7,utf8_decode($row['voucher']),1,0,'C');
                    $pdf::Cell(114,7,$row['nombre'].': '.$row['comentario'],1,0,'L');
                    if($row['situacion'] == 'N') {
                        $pdf::Cell(14,7,number_format($row['total'],2,'.',''),1,0,'R');
                        $pdf::Cell(42,7,utf8_decode(""),1,0,'C');                    
                        $subtotalegresos += number_format($row['total'],2,'.','');
                    } else {
                        $pdf::Cell(56,7,utf8_decode("ANULADO"),1,0,'C');
                    }  
                    $pdf::Cell(20,7,utf8_decode("-"),1,0,'C');
                    $pdf::Ln();              
                }      
                $pdf::SetFont('helvetica','B',8.5);
                $pdf::Cell(205,7,'SUBTOTAL',1,0,'R');
                $pdf::Cell(14,7,number_format($subtotalegresos,2,'.',''),1,0,'R');
                $pdf::Cell(42,7,number_format(0,2,'.',''),1,0,'R');
                $pdf::Ln();                  
            }

            //Solo para egresos por compra farmacia

            if($caja->nombre == 'FARMACIA') {
                $resultadoegresos        = Movimiento::leftjoin('person as paciente', 'paciente.id', '=', 'movimiento.persona_id')
                    ->join('person as responsable', 'responsable.id', '=', 'movimiento.responsable_id')
                    ->join('conceptopago','conceptopago.id','=','movimiento.conceptopago_id')
                    ->join('tipodocumento','tipodocumento.id','=','movimiento.tipodocumento_id')
                    ->leftjoin('movimiento as m2','m2.movimiento_id','=','movimiento.id')
                    ->where('movimiento.caja_id', '=', $caja_id)
                    ->whereNull('m2.caja_id')
                    ->where('movimiento.sucursal_id','=',$sucursal_id)
                    ->where('movimiento.tipomovimiento_id','=',2)
                    ->where('paciente.dni','=',null)
                    ->whereBetween('movimiento.id', [$apertura->id,(int)$cierre['id']])
                    ->whereNull('movimiento.cajaapertura_id')
                    ->where('conceptopago.tipo', '=', 'E');

                $resultadoegresos        = $resultadoegresos->select('movimiento.*','tipodocumento.abreviatura as formapago2','responsable.nombres as responsable2',DB::raw('concat(paciente.ruc,\' - \',paciente.bussinesname) as paciente'), 'conceptopago.nombre')->orderBy('conceptopago.tipo', 'asc')->orderBy('conceptopago.orden', 'asc')->orderBy('conceptopago.id', 'asc')->orderBy('movimiento.tipotarjeta', 'asc')->orderBy('movimiento.numero', 'asc');

                $listaegresos = $resultadoegresos->get();

                if(count($listaegresos)>0){
                    $pdf::SetFont('helvetica','B',8.5);
                    $pdf::Cell(281,7,'EGRESOS POR COMPRA',1,0,'L');
                    $pdf::Ln();
                    $subtotalegresoscompra = 0;
                    foreach ($listaegresos as $row) { 
                        if($row['situacion2'] == null){
                            $pdf::SetFont('helvetica','',6);                   
                            $pdf::Cell(15,7,utf8_decode($row['fecha']),1,0,'C');
                            $pdf::Cell(56,7,$row['paciente'],1,0,'L');
                            $pdf::Cell(8,7,$row['formapago2'],1,0,'C');
                            $pdf::Cell(12,7,utf8_decode($row['voucher']),1,0,'C');
                            $pdf::Cell(114,7,$row['nombre'].': '.$row['comentario'],1,0,'L');
                            if($row['situacion'] == 'N') {
                                $pdf::Cell(14,7,number_format($row['total'],2,'.',''),1,0,'R');
                                $pdf::Cell(42,7,utf8_decode(""),1,0,'C');                    
                                $subtotalegresoscompra += number_format($row['total'],2,'.','');
                                $subtotalegresos += number_format($row['total'],2,'.','');
                            } else {
                                $pdf::Cell(56,7,utf8_decode("ANULADO"),1,0,'C');
                            }  
                            $pdf::Cell(20,7,utf8_decode("-"),1,0,'C');
                            $pdf::Ln();     
                        }         
                    }      
                    $pdf::SetFont('helvetica','B',8.5);
                    $pdf::Cell(205,7,'SUBTOTAL',1,0,'R');
                    $pdf::Cell(14,7,number_format($subtotalegresoscompra,2,'.',''),1,0,'R');
                    $pdf::Cell(42,7,number_format(0,2,'.',''),1,0,'R');
                    $pdf::Ln();                  
                }
            }

            $pdf::SetFont('helvetica','',7);   
            $pdf::Ln();
            $pdf::Ln();
            $pdf::Cell(120,7,('RESPONSABLE: '.$responsable),0,0,'L');
            $pdf::SetFont('helvetica','B',9);
            $pdf::Cell(50,7,utf8_decode("RESUMEN DE CAJA"),1,0,'C');
            $pdf::Ln();
            $pdf::Cell(120,7,utf8_decode(""),0,0,'C');
            $pdf::Cell(30,7,utf8_decode("INGRESOS :"),1,0,'L');
            $pdf::Cell(20,7,number_format($totalefectivo + $totalmaster + $totalvisa,2,'.',''),1,0,'R');
            $pdf::Ln();
            $pdf::Cell(120,7,utf8_decode(""),0,0,'C');
            $pdf::Cell(30,7,utf8_decode("Efectivo :"),1,0,'L');
            $pdf::Cell(20,7,number_format($totalefectivo,2,'.',''),1,0,'R');
            $pdf::Ln();
            $pdf::Cell(120,7,utf8_decode(""),0,0,'C');
            $pdf::Cell(30,7,utf8_decode("Master :"),1,0,'L');
            $pdf::Cell(20,7,number_format($totalmaster,2,'.',''),1,0,'R');
            $pdf::Ln();
            $pdf::Cell(120,7,utf8_decode(""),0,0,'C');
            $pdf::Cell(30,7,utf8_decode("Visa :"),1,0,'L');
            $pdf::Cell(20,7,number_format($totalvisa,2,'.',''),1,0,'R');
            $pdf::Ln();
            $pdf::Cell(120,7,utf8_decode(""),0,0,'C');
            $pdf::Cell(30,7,utf8_decode("EGRESOS :"),1,0,'L');
            $pdf::Cell(20,7,number_format($subtotalegresos,2,'.',''),1,0,'R');
            $pdf::Ln();
            $pdf::Cell(120,7,utf8_decode(""),0,0,'C');
            $pdf::Cell(30,7,utf8_decode("SALDO (S/.) :"),1,0,'L');
            $pdf::Cell(20,7,number_format($totalefectivo + $totalmaster + $totalvisa - $subtotalegresos,2,'.',''),1,0,'R');
            $pdf::Ln();
            $pdf::Cell(120,7,utf8_decode(""),0,0,'C');
            $pdf::Cell(30,7,utf8_decode("SALDO ($) :"),1,0,'L');
            $pdf::Cell(20,7,number_format($subtotaldolares,2,'.',''),1,0,'R');
            $pdf::Ln();
            if($cont == $numcajas) {
                break;
            }
            $cont++;
        }
        $pdf::Output('ListaCaja.pdf');
    }

    ////////////////////////////////////////////////////////////////////////

    public function pdfDetalleCierreOriginal(Request $request){
        $caja                = Caja::find($request->input('caja_id'));
        $caja_id          = Libreria::getParam($request->input('caja_id'),'1');

        $totalvisa = 0;
        $totalmaster = 0;
        $totalefectivo = 0;

        //sucursal_id
        $sucursal_id = Session::get('sucursal_id');

        $rst  = Movimiento::where('sucursal_id','=',$sucursal_id)->where('tipomovimiento_id','=',2)->where('caja_id','=',$caja_id)->orderBy('movimiento.id','DESC')->limit(1)->first();
        if(count($rst)==0){
            $conceptopago_id=2;
        }else{
            $conceptopago_id=$rst->conceptopago_id;
        }
        
        $rst              = Movimiento::where('sucursal_id','=',$sucursal_id)->where('tipomovimiento_id','=',2)->where('caja_id','=',$caja_id)->where('conceptopago_id','=',1)->orderBy('id','DESC')->limit(1)->first();
        if(count($rst)>0){
            $movimiento_mayor = $rst->id;    
        }else{
            $movimiento_mayor = 0;
        }
        
        $resultado        = Movimiento::leftjoin('person as paciente', 'paciente.id', '=', 'movimiento.persona_id')
                            ->join('person as responsable', 'responsable.id', '=', 'movimiento.responsable_id')
                            ->join('conceptopago','conceptopago.id','=','movimiento.conceptopago_id')
                            ->leftjoin('movimiento as m2','m2.movimiento_id','=','movimiento.id')
                            ->where('movimiento.caja_id', '=', $caja_id)
                            ->where('movimiento.sucursal_id','=',$sucursal_id)
                            ->where('movimiento.id', '>', $movimiento_mayor)
                            ->whereNull('movimiento.cajaapertura_id')
                            ->where(function($query){
                                $query
                                    ->whereNotIn('movimiento.conceptopago_id',[31])
                                    ->orWhere('m2.situacion','<>','R');
                            })
                            ->where('movimiento.situacion', '<>', 'A')
                            ->where('movimiento.situacion', '<>', 'R');
        $resultado        = $resultado->select('movimiento.*','m2.situacion as situacion2','responsable.nombres as responsable2')->orderBy('conceptopago.tipo', 'asc')->orderBy('conceptopago.orden', 'asc')->orderBy('conceptopago.id', 'asc')->orderBy('movimiento.tipotarjeta', 'asc')->orderBy('movimiento.numero', 'asc');
        $listConcepto     = array();
        $listConcepto2     = array();
        $listConcepto3     = array();
        $listConcepto4     = array();
        $listConcepto[]   = 6;//TRANSF CAJA INGRESO
        $listConcepto[]   = 7;//TRANSF CAJA EGRESO
        $listConcepto2[]   = 8;//HONORARIOS MEDICOS
        $listConcepto[]   = 14;//TRANSF TARJETA EGRESO
        $listConcepto[]   = 15;//TRANSF TARJETA INGRESO
        $listConcepto[]   = 16;//TRANSF SOCIO EGRESO
        $listConcepto[]   = 17;//TRANSF SOCIO INGRESO
        $listConcepto3[]   = 24;//PAGO DE CONVENIO
        $listConcepto3[]   = 25;//PAGO DE SOCIO
        $listConcepto[]   = 20;//TRANSF BOLETEO EGRESO
        $listConcepto[]   = 21;//TRANSF BOLETEO INGRESO
        $listConcepto4[]   = 31;//TRANSF FARMACIA EGRESO
        $listConcepto4[]   = 32;//TRANSF FARMACiA INGRESO
        $lista            = $resultado->get();
        $listapendiente = array();
        /*if ($caja_id != 6) {
            $resultado2        = Movimiento::leftjoin('person as paciente', 'paciente.id', '=', 'movimiento.persona_id')
                            ->leftjoin('person as responsable', 'responsable.id', '=', 'movimiento.responsable_id')
                            ->leftjoin('conceptopago','conceptopago.id','=','movimiento.conceptopago_id')
                            ->leftjoin('movimiento as m2','movimiento.id','=','m2.movimiento_id')
                            ->where('movimiento.ventafarmacia', '=', 'S')
                            ->where('movimiento.estadopago', '=', 'PP')
                            ->where('movimiento.sucursal_id','=',$sucursal_id)
                            ->where('movimiento.id', '>=', $movimiento_mayor)
                            ->where('movimiento.situacion', '<>', 'A')
                            ->where('movimiento.situacion', '<>', 'U')->where('movimiento.situacion', '<>', 'R')
                            ->whereNull('movimiento.cajaapertura_id');
            $resultado2        = $resultado2->select('movimiento.*','m2.situacion as situacion2',DB::raw('CONCAT(paciente.apellidopaterno," ",paciente.apellidomaterno," ",paciente.nombres) as paciente'),DB::raw('responsable.nombres as responsable'))->orderBy('movimiento.id', 'desc');
            $listapendiente            = $resultado2->get();
        }*/
        if (isset($lista)) { 
            $sucursal_id = Session::get('sucursal_id'); 
            $nomcierre = '';
            $nomcierre = 'Clínica Especialidades'; 
            if($sucursal_id == 1) {
                $nomcierre = 'BM Clínica de Ojos';
            }  
            if($caja->nombre == 'FARMACIA') {
                $nomcierre = ' Farmacia - ' . $nomcierre;
            }     
            $pdf = new TCPDF();
            //$pdf::SetIma�
            $pdf::SetTitle('Detalle Cierre de '.$nomcierre);
            $pdf::AddPage('L');
            $pdf::SetFont('helvetica','B',12);
            $pdf::Cell(0,10,"Detalle de Cierre de ".$nomcierre,0,0,'C');
            $pdf::Ln();
            $pdf::SetFont('helvetica','B',7);
            $pdf::Cell(15,7,utf8_decode("FECHA"),1,0,'C');
            $pdf::Cell(56,7,utf8_decode("PERSONA"),1,0,'C');
            $pdf::Cell(20,7,utf8_decode("NRO"),1,0,'C');
            $pdf::Cell(40,7,utf8_decode("EMPRESA"),1,0,'C');
            $pdf::Cell(60,7,utf8_decode("CONCEPTO"),1,0,'C');
            $pdf::Cell(14,7,utf8_decode("PRECIO"),1,0,'C');
            $pdf::Cell(14,7,utf8_decode("EGRESO"),1,0,'C');
            $pdf::Cell(42,7,utf8_decode("INGRESO"),1,0,'C');
            $pdf::Cell(20,7,utf8_decode("DOCTOR"),1,0,'C');
            $pdf::Ln();
            $pdf::Cell(219,7,utf8_decode(""),1,0,'C');
            $pdf::Cell(14,7,utf8_decode("EFECTIVO"),1,0,'C');
            $pdf::Cell(14,7,utf8_decode("VISA"),1,0,'C');
            $pdf::Cell(14,7,utf8_decode("MASTER"),1,0,'C');
            $pdf::Cell(20,7,utf8_decode(""),1,0,'C');
            $pdf::Ln();
            if($caja_id==1){//ADMISION 1
                $serie=3;
            }elseif($caja_id==2){//ADMISION 2
                $serie=7;
            }elseif($caja_id==3){//CONVENIOS
                $serie=8;
            }elseif($caja_id==5){//EMERGENCIA
                $serie=9;
            }elseif($caja_id==4){//FARMACIA
                $serie=4;
            }/*elseif($caja_id==8){//PROCEDIMIENTOS
                $serie=5;
            }*/
            $resultado1       = Movimiento::join('person as responsable', 'responsable.id', '=', 'movimiento.responsable_id')
                            ->leftjoin('movimiento as m2','movimiento.movimiento_id','=','m2.id')
                            ->leftjoin('person as paciente', 'paciente.id', '=', 'm2.persona_id')
                            ->where('movimiento.serie', '=', $serie)
                            ->where('movimiento.tipomovimiento_id', '=', 4)
                            ->where('movimiento.situacion', '=', 'P')
                            ->where('movimiento.id', '>=', $movimiento_mayor)
                            ->where('movimiento.sucursal_id','=',$sucursal_id)
                            ->where('movimiento.situacion', '<>', 'U')
                            ->where('movimiento.situacion', '<>', 'A')
                            ->where('movimiento.situacion', '<>', 'R');
            $resultado1       = $resultado1->select('movimiento.*','m2.situacion as situacion2',DB::raw('concat(paciente.apellidopaterno,\' \',paciente.apellidomaterno,\' \',paciente.nombres) as paciente2'),'responsable.nombres as responsable2')->orderBy('movimiento.numero', 'asc');
            
            $lista1           = $resultado1->get();

            //Lista pendiente

            /*if ($caja_id == 3 || $caja_id == 4) {
                $pendiente = 0;
                foreach ($listapendiente as $key => $value) {
                    if($pendiente==0 && $value->tipodocumento_id != 15){
                        $pdf::SetFont('helvetica','B',8.5);
                        $pdf::Cell(281,7,utf8_decode("PENDIENTE"),1,0,'L');
                        $pdf::Ln();
                    }

                    if ($value->tipodocumento_id != 15) {
                        $pdf::SetFont('helvetica','',7);
                        $pdf::Cell(15,7,date("d/m/Y",strtotime($value->fecha)),1,0,'C');

                        $nombrepaciente = '';
                        $nombreempresa = '-';
                        if ($value->persona_id !== NULL) {
                            //echo 'entro'.$value->id;break;
                            $nombrepaciente =$value->persona->apellidopaterno.' '.$value->persona->apellidomaterno.' '.$value->persona->nombres;

                        }else{
                            $nombrepaciente = trim($value->nombrepaciente);
                        }
                        if ($value->tipodocumento_id == 5) {
                            
                            
                        }else{
                            if ($value->empresa_id != null) {
                                $nombreempresa = trim($value->empresa->bussinesname);
                            }
                        }
                        if(strlen($nombrepaciente)>30){
                            $x=$pdf::GetX();
                            $y=$pdf::GetY();                    
                            $pdf::Multicell(60,3,($nombrepaciente),0,'L');
                            $pdf::SetXY($x,$y);
                            $pdf::Cell(60,7,"",1,0,'C');
                        }else{
                            $pdf::Cell(60,7,($nombrepaciente),1,0,'L');    
                        }
                        //$venta= Movimiento::find($value->id);
                        $pdf::Cell(8,7,($value->tipodocumento_id==5?'B':'F'),1,0,'C');
                        $pdf::Cell(12,7,$value->serie.'-'.$value->numero,1,0,'C');

                        if ($value->conveniofarmacia_id != null) {
                            $nombreempresa = $value->conveniofarmacia->nombre;
                        }

                        $pdf::Cell(40,7,substr($nombreempresa,0,23),1,0,'L');
                        if($value->servicio_id>0){
                            if(strlen($value->servicio->nombre)>35){
                                $x=$pdf::GetX();
                                $y=$pdf::GetY();                    
                                $pdf::Multicell(70,3,$value->servicio->nombre,0,'L');
                                $pdf::SetXY($x,$y);
                                $pdf::Cell(70,7,"",1,0,'C');
                            }else{
                                $pdf::Cell(70,7,$value->servicio->nombre,1,0,'L');    
                            }
                        }else{
                            $pdf::Cell(70,7,'MEDICINA',1,0,'L');    
                        }
                        $pdf::Cell(14,7,'',1,0,'C');
                        $pdf::Cell(14,7,number_format($value->totalpagado,2,'.',''),1,0,'R');
                        $pdf::Cell(14,7,number_format($value->totalpagadovisa,2,'.',''),1,0,'C');
                        $pdf::Cell(14,7,number_format($value->totalpagadomaster,2,'.',''),1,0,'C');
                        if ($value->doctor_id != null) {
                            $pdf::Cell(20,7,substr($value->doctor->nombres,0,1).'. '.$value->doctor->apellidopaterno,1,0,'L');

                        }else{
                           $pdf::Cell(20,7," - ",1,0,'L'); 
                        }
                        
                        $pdf::Ln();
                        $pendiente=$pendiente + number_format($value->total,2,'.','');
                        $totalvisa+=number_format($value->totalpagadovisa,2,'.','');
                        $totalmaster+=number_format($value->totalpagadomaster,2,'.','');
                    }

                }
                $pdf::SetFont('helvetica','B',8.5);
                $pdf::Cell(281,7,'TOTAL',1,0,'R');
                $pdf::Cell(18,7,number_format($pendiente,2,'.',''),1,0,'R');
                $pdf::Ln();
            } */  

            /*         

            if(count($lista1)>0){
                $pendiente=0;
                foreach($lista1 as $key1 => $value1){
                    $rs = Detallemovcaja::where("movimiento_id",'=',$value1->movimiento_id)->get();
                    foreach ($rs as $k => $v){
                        if($pendiente==0){
                            $pdf::SetFont('helvetica','B',8.5);
                            $pdf::Cell(281,7,utf8_decode("PENDIENTE"),1,0,'L');
                            $pdf::Ln();
                        }
                        $pdf::SetFont('helvetica','',7.5);
                        $pdf::Cell(15,7,date("d/m/Y",strtotime($value1->fecha)),1,0,'C');
                        if($value1->tipodocumento_id==5){//BOLETA
                            $nombre=$value1->persona->apellidopaterno.' '.$value1->persona->apellidomaterno.' '.$value1->persona->nombres;
                        }else{
                            $nombre=$value1->paciente2;
                            $empresa=$value1->persona->bussinesname." / ";
                        }
                        if(strlen($nombre)>30){
                            $x=$pdf::GetX();
                            $y=$pdf::GetY();                    
                            $pdf::Multicell(60,3,($nombre),0,'L');
                            $pdf::SetXY($x,$y);
                            $pdf::Cell(60,7,"",1,0,'C');
                        }else{
                            $pdf::Cell(60,7,($nombre),1,0,'L');    
                        }
                        $pdf::Cell(8,7,($value1->tipodocumento_id==5?'B':'F'),1,0,'C');
                        $pdf::Cell(12,7,$value1->serie.'-'.$value1->numero,1,0,'C');
                        $ticket= Movimiento::find($value1->movimiento_id);
                        if($value1->tipodocumento_id==5){//BOLETA
                            $pdf::Cell(40,7,substr($ticket->plan->nombre,0,23),1,0,'L');    
                        }else{
                            $pdf::Cell(40,7,substr($empresa,0,23),1,0,'L');
                        }
                        if($v->servicio_id>0){
                            if(strlen($v->servicio->nombre)>35){
                                $x=$pdf::GetX();
                                $y=$pdf::GetY();                    
                                $pdf::Multicell(70,3,$v->servicio->nombre,0,'L');
                                $pdf::SetXY($x,$y);
                                $pdf::Cell(70,7,"",1,0,'C');
                            }else{
                                $pdf::Cell(70,7,$v->servicio->nombre,1,0,'L');    
                            }
                        }else{
                            $pdf::Cell(70,7,$v->descripcion,1,0,'L');    
                        }
                        $pdf::Cell(14,7,'',1,0,'C');
                        $pdf::Cell(14,7,number_format($v->cantidad*$v->pagohospital,2,'.',''),1,0,'R');
                        $pdf::Cell(14,7,utf8_decode(" - "),1,0,'C');
                        $pdf::Cell(14,7,utf8_decode(" - "),1,0,'C');
                        $pdf::Cell(20,7,substr($v->persona->nombres,0,1).'. '.$v->persona->apellidopaterno,1,0,'L');
                        $pdf::Ln();
                        $pendiente=$pendiente + number_format($v->cantidad*$v->pagohospital,2,'.','');
                    }
                }
                $pdf::SetFont('helvetica','B',8.5);
                $pdf::Cell(223,7,'TOTAL',1,0,'R');
                $pdf::Cell(18,7,number_format($pendiente,2,'.',''),1,0,'R');
                $pdf::Ln();
            }

            */

            $ingreso=0;$egreso=0;$transferenciai=0;$transferenciae=0;$garantia=0;$efectivo=0;$visa=0;$master=0;$pago=0;$tarjeta=0;$cobranza=0;$egreso1=0;$transferenciai=0;$cobranza=0;$ingresotarjeta=0;
            $bandpago=true;$bandegreso=true;$bandtransferenciae=true;$bandtarjeta=true;$bandtransferenciai=true;$bandcobranza=true;$bandingresotarjeta=true;
            $i = 1;
            foreach ($lista as $key => $value){
                if($i == 1) {
                    $pdf::SetFont('helvetica','B',8.5);
                    $pdf::Cell(281,7,utf8_decode("INGRESOS POR MEDICINA"),1,0,'L');
                    $pdf::Ln();
                }
                $i++;
                if($ingreso==0){
                    $responsable=$value->responsable2;
                }
                
                if($value->conceptopago_id==3 && $value->tipotarjeta==''){

                    if ($caja_id == 3 || $caja_id == 4) {
                        $rs = Detallemovcaja::where("movimiento_id",'=',DB::raw('(select movimiento_id from movimiento where id='.$value->movimiento_id.')'))->get();
                        //echo $value->movimiento_id."|".$value->id."@";
                        foreach ($rs as $k => $v) {
                            $pdf::SetTextColor(0,0,0);
                            if($egreso1>0 && $bandegreso){
                                $pdf::SetFont('helvetica','B',8.5);
                                $pdf::Cell(205,7,'TOTAL',1,0,'R');
                                $pdf::Cell(18,7,number_format($egreso1,2,'.',''),1,0,'R');
                                $bandegreso=false;
                                $pdf::Ln(); 
                            }
                            if($pago==0 && $value->tipodocumento_id!=15){
                                
                            }
                            if ($value->tipodocumento_id !== 15) {
                                $pdf::SetFont('helvetica','',6);
                                $pdf::Cell(15,7,date("d/m/Y",strtotime($value->fecha)),1,0,'C');

                                $nombrepaciente = '';
                                $nombreempresa = '-';
                                if ($value->persona_id !== NULL) {
                                    //echo 'entro'.$value->id;break;
                                    $nombrepaciente =$value->persona->apellidopaterno.' '.$value->persona->apellidomaterno.' '.$value->persona->nombres;

                                }else{
                                    $nombrepaciente = trim($value->nombrepaciente);
                                }
                                if ($value->tipodocumento_id == 5) {
                                    
                                    
                                }else{
                                    if ($value->empresa_id != null) {
                                        $nombreempresa = trim($value->empresa->bussinesname);
                                    }
                                    
                                }
                                if(strlen($nombrepaciente)>30){
                                    $x=$pdf::GetX();
                                    $y=$pdf::GetY();                    
                                    $pdf::Multicell(56,3,($nombrepaciente),0,'L');
                                    $pdf::SetXY($x,$y);
                                    $pdf::Cell(56,7,"",1,0,'C');
                                }else{
                                    $pdf::Cell(56,7,($nombrepaciente),1,0,'L');    
                                }
                                $venta= Movimiento::find($value->movimiento_id);
                                $pdf::Cell(8,7,($venta->tipodocumento_id==5?'B':'F'),1,0,'C');
                                $pdf::Cell(12,7,$venta->serie.'-'.$venta->numero,1,0,'C');
                                if ($venta->conveniofarmacia_id != null) {
                                    $nombreempresa = $venta->conveniofarmacia->nombre;
                                }
                                $pdf::Cell(40,7,substr($nombreempresa,0,23),1,0,'L');
                                if($v->servicio_id>0){
                                    if(strlen($v->servicio->nombre)>35){
                                        $x=$pdf::GetX();
                                        $y=$pdf::GetY();                    
                                        $pdf::Multicell(74,3,$v->servicio->nombre,0,'L');
                                        $pdf::SetXY($x,$y);
                                        $pdf::Cell(74,7,"",1,0,'C');
                                    }else{
                                        $pdf::Cell(74,7,$v->servicio->nombre,1,0,'L');    
                                    }
                                }else{
                                    $pdf::Cell(74,7,$v->descripcion.'- MEDICINA',1,0,'L');    
                                }
                                $pdf::Cell(14,7,'',1,0,'C');
                                $pdf::Cell(14,7,number_format($v->movimiento->totalpagado,2,'.',''),1,0,'R');
                                $pdf::Cell(14,7,number_format($v->movimiento->totalpagadovisa,2,'.',''),1,0,'C');
                                $pdf::Cell(14,7,number_format($v->movimiento->totalpagadomaster,2,'.',''),1,0,'C');
                                if ($venta->doctor_id != null) {
                                    $pdf::Cell(20,7,substr($venta->doctor->nombres,0,1).'. '.$venta->doctor->apellidopaterno,1,0,'L');

                                }else{
                                   $pdf::Cell(20,7," - ",1,0,'L'); 
                                }
                                
                                $pdf::Ln();
                                $pago=$pago + number_format($v->movimiento->total,2,'.','');
                                $totalvisa+= number_format($v->movimiento->totalpagadovisa,2,'.','');
                                $totalmaster+= number_format($v->movimiento->totalpagadomaster,2,'.','');
                            }                            
                        }
                    }else{
                        //PARA PAGO DE CLIENTE, BUSCO TICKET
                        $rs = Detallemovcaja::where("movimiento_id",'=',DB::raw('(select movimiento_id from movimiento where id='.$value->movimiento_id.')'))->get();
                        $i = 0;
                        foreach ($rs as $k => $v){
                            $pdf::SetTextColor(0,0,0);
                            if($transferenciae>0 && $bandtransferenciae){
                                $pdf::SetFont('helvetica','B',8.5);
                                $pdf::Cell(205,7,'TOTAL',1,0,'R');
                                $pdf::Cell(18,7,number_format($transferenciae,2,'.',''),1,0,'R');
                                $bandtransferenciae=false;
                                $pdf::Ln(); 
                            }
                            if($egreso1>0 && $bandegreso){
                                $pdf::SetFont('helvetica','B',8.5);
                                $pdf::Cell(205,7,'TOTAL',1,0,'R');
                                $pdf::Cell(18,7,number_format($egreso1,2,'.',''),1,0,'R');
                                $bandegreso=false;
                                $pdf::Ln(); 
                            }
                            if($pago==0){
                                $pdf::SetFont('helvetica','B',8.5);
                                $pdf::Cell(281,7,utf8_decode("INGRESOSOK"),1,0,'L');
                                $pdf::Ln();
                                if($caja_id==3){
                                    $pdf::SetFont('helvetica','B',7);
                                    $pdf::Cell(223,7,utf8_decode("SALDO INICIAL"),1,0,'L');
                                    $apert = Movimiento::find($movimiento_mayor);
                                    $pdf::Cell(18,7,number_format($apert->total,2,'.',''),1,0,'R');
                                    $pago = $pago + $apert->total;
                                    $ingreso = $ingreso + $apert->total;
                                    $pdf::Ln();
                                }
                            }
                            $pdf::SetFont('helvetica','',6);
                            $pdf::Cell(15,7,date("d/m/Y",strtotime($value->fecha)),1,0,'C');
                            if(strlen($value->persona->apellidopaterno.' '.$value->persona->apellidomaterno.' '.$value->persona->nombres)>30){
                                $x=$pdf::GetX();
                                $y=$pdf::GetY();                    
                                $pdf::Multicell(56,3,($value->persona->apellidopaterno.' '.$value->persona->apellidomaterno.' '.$value->persona->nombres),0,'L');
                                $pdf::SetXY($x,$y);
                                $pdf::Cell(56,7,"",1,0,'C');
                            }else{
                                $pdf::Cell(56,7,($value->persona->apellidopaterno.' '.$value->persona->apellidomaterno.' '.$value->persona->nombres),1,0,'L');    
                            }
                            $venta= Movimiento::find($value->movimiento_id);
                            $ticket= Movimiento::find($v->movimiento_id);
                            $pdf::Cell(8,7,($venta->tipodocumento_id==5?'B':'F'),1,0,'C');
                            $pdf::Cell(12,7,$venta->serie.'-'.$venta->numero,1,0,'C');
                            $pdf::Cell(40,7,substr($ticket->plan->nombre,0,23),1,0,'L');
                            if($v->servicio_id>0){
                                if(strlen($v->servicio->nombre)>35){
                                    $x=$pdf::GetX();
                                    $y=$pdf::GetY();                    
                                    $pdf::Multicell(60,3,substr($v->servicio->nombre, 0, 85),0,'L');
                                    $pdf::SetXY($x,$y);
                                    $pdf::Cell(60,7,"",1,0,'C');
                                }else{
                                    $pdf::Cell(60,7,$v->servicio->nombre,1,0,'L');    
                                }
                            }else{
                                $pdf::Cell(70,7,$v->descripcion,1,0,'L');    
                            }
                            $pdf::Cell(14,7,number_format($v->cantidad*$v->pagohospital,2,'.',''),1,0,'R');
                            if($i == 0) {
                                $valuetp = number_format($value->totalpagado,2,'.','');
                                $valuetpv = number_format($value->totalpagadovisa,2,'.','');
                                $valuetpm = number_format($value->totalpagadomaster,2,'.','');
                                if($valuetp == 0){$valuetp='';}
                                if($valuetpv == 0){$valuetpv='';}
                                if($valuetpm == 0){$valuetpm='';}
                                $pdf::Cell(14,7*count($rs),utf8_decode(''),1,0,'C');
                                $pdf::Cell(14,7*count($rs),$valuetp,1,0,'R');
                                $pdf::Cell(14,7*count($rs),$valuetpv,1,0,'R');
                                $pdf::Cell(14,7*count($rs),$valuetpm,1,0,'R');
                                $totalvisa += number_format($value->totalpagadovisa,2,'.','');
                                $totalmaster += number_format($value->totalpagadomaster,2,'.','');
                            } else {
                                $pdf::Cell(14,7,utf8_decode(''),0,0,'C');
                                $pdf::Cell(14,7,utf8_decode(''),0,0,'C');
                                $pdf::Cell(14,7,utf8_decode(''),0,0,'C');
                                $pdf::Cell(14,7,utf8_decode(''),0,0,'C');
                            }                            
                            $pdf::Cell(20,7,substr($v->persona->nombres,0,1).'. '.$v->persona->apellidopaterno,1,0,'L');
                            $pdf::Ln();
                            $pago=$pago + number_format($v->cantidad*$v->pagohospital,2,'.','');
                            $i++;
                        }
                    }
                }
                /*elseif($value->conceptopago_id==3 && $value->tipotarjeta!=''){//PARA PAGO DE CLIENTE, BUSCO TICKET CON TARJETA
                    if ($caja_id == 3 || $caja_id == 4) {
                        $rs = Detallemovcaja::where("movimiento_id",'=',DB::raw('(select movimiento_id from movimiento where id='.$value->movimiento_id.')'))->get();
                        foreach ($rs as $k => $v){
                            $pdf::SetTextColor(0,0,0);
                            if($pago>0 && $bandpago){
                                $pdf::SetFont('helvetica','B',8.5);
                                $pdf::Cell(223,7,'TOTAL',1,0,'R');
                                $pdf::Cell(18,7,number_format($pago,2,'.',''),1,0,'R');
                                $bandpago=false;
                                $pdf::Ln(); 
                            }

                            if($tarjeta==0){
                                $pdf::SetFont('helvetica','B',8.5);
                                $pdf::Cell(281,7,utf8_decode("TARJETA"),1,0,'L');
                                $pdf::Ln();
                            }
                            $pdf::SetFont('helvetica','',7);
                            $pdf::Cell(15,7,date("d/m/Y",strtotime($value->fecha)),1,0,'C');
                            
                            $nombrepaciente = '';
                            $nombreempresa = '-';
                            if ($value->persona_id !== NULL) {
                                //echo 'entro'.$value->id;break;
                                $nombrepaciente =$value->persona->apellidopaterno.' '.$value->persona->apellidomaterno.' '.$value->persona->nombres;

                            }else{
                                $nombrepaciente = trim($value->nombrepaciente);
                            }
                            if ($value->tipodocumento_id == 5) {
                                
                                
                            }else{
                                $nombreempresa = trim($value->empresa->bussinesname);
                            }
                            if(strlen($nombrepaciente)>30){
                                $x=$pdf::GetX();
                                $y=$pdf::GetY();                    
                                $pdf::Multicell(60,3,($nombrepaciente),0,'L');
                                $pdf::SetXY($x,$y);
                                $pdf::Cell(60,7,"",1,0,'C');
                            }else{
                                $pdf::Cell(60,7,($nombrepaciente),1,0,'L');    
                            }
                            $venta= Movimiento::find($value->movimiento_id);
                            $ticket= Movimiento::find($v->movimiento_id);
                            $pdf::Cell(8,7,($venta->tipodocumento_id==5?'B':'F'),1,0,'C');
                            $pdf::Cell(12,7,$venta->serie.'-'.$venta->numero,1,0,'C');
                            if ($venta->conveniofarmacia_id != null) {
                                $nombreempresa = $venta->conveniofarmacia->nombre;
                            }
                            $pdf::Cell(40,7,substr($nombreempresa,0,23),1,0,'L');
                            if($v->servicio_id>0){
                                if(strlen($v->servicio->nombre)>35){
                                    $x=$pdf::GetX();
                                    $y=$pdf::GetY();                    
                                    $pdf::Multicell(70,3,$v->servicio->nombre,0,'L');
                                    $pdf::SetXY($x,$y);
                                    $pdf::Cell(70,7,"",1,0,'C');
                                }else{
                                    $pdf::Cell(70,7,$v->servicio->nombre,1,0,'L');    
                                }
                            }else{
                                $pdf::Cell(70,7,$v->descripcion,1,0,'L');    
                            }
                            $pdf::Cell(18,7,'',1,0,'C');
                            $tarjeta=$tarjeta + $v->movimiento->total;
                            $pdf::Cell(18,7,number_format($v->movimiento->total,2,'.',''),1,0,'R');
                            $x=$pdf::GetX();
                            $y=$pdf::GetY();                    
                            $pdf::Multicell(20,3,$value->tipotarjeta." ".$value->tarjeta." / ".$value->voucher,0,'L');
                            $pdf::SetXY($x,$y);
                            $pdf::Cell(20,7,"",1,0,'C');
                            if ($venta->doctor_id != null) {
                                $pdf::Cell(20,7,substr($venta->doctor->nombres,0,1).'. '.$venta->doctor->apellidopaterno,1,0,'L');

                            }else{
                               $pdf::Cell(20,7," - ",1,0,'L'); 
                            }
                            $pdf::Ln();
                            //$pago=$pago + number_format($v->movimiento->total,2,'.','');
                            
                        }
                    }else{
                        $rs = Detallemovcaja::where("movimiento_id",'=',DB::raw('(select movimiento_id from movimiento where id='.$value->movimiento_id.')'))->get();
                        foreach ($rs as $k => $v){
                            $pdf::SetTextColor(0,0,0);
                            if($pago>0 && $bandpago){
                                $pdf::SetFont('helvetica','B',8.5);
                                $pdf::Cell(223,7,'TOTAL',1,0,'R');
                                $pdf::Cell(18,7,number_format($pago,2,'.',''),1,0,'R');
                                $bandpago=false;
                                $pdf::Ln(); 
                            }
                            if($tarjeta==0){
                                $pdf::SetFont('helvetica','B',8.5);
                                $pdf::Cell(281,7,utf8_decode("TARJETA"),1,0,'L');
                                $pdf::Ln();
                            }
                            if($value->situacion<>'A'){
                                $pdf::SetTextColor(0,0,0);
                            }else{
                                $pdf::SetTextColor(255,0,0);
                            }
                            $pdf::SetFont('helvetica','',7);
                            $pdf::Cell(15,7,date("d/m/Y",strtotime($value->fecha)),1,0,'C');
                            $tarjeta=$tarjeta + number_format($v->cantidad*$v->pagohospital,2,'.','');
                            if(strlen($value->persona->apellidopaterno.' '.$value->persona->apellidomaterno.' '.$value->persona->nombres)>30){
                                $x=$pdf::GetX();
                                $y=$pdf::GetY();                    
                                $pdf::Multicell(60,3,($value->persona->apellidopaterno.' '.$value->persona->apellidomaterno.' '.$value->persona->nombres),0,'L');
                                $pdf::SetXY($x,$y);
                                $pdf::Cell(60,7,"",1,0,'C');
                            }else{
                                $pdf::Cell(60,7,($value->persona->apellidopaterno.' '.$value->persona->apellidomaterno.' '.$value->persona->nombres),1,0,'L');    
                            }
                            $venta= Movimiento::find($value->movimiento_id);
                            $ticket= Movimiento::find($v->movimiento_id);
                            $pdf::Cell(8,7,($venta->tipodocumento_id==5?'B':'F'),1,0,'C');
                            $pdf::Cell(12,7,$venta->serie.'-'.$venta->numero,1,0,'C');
                            $pdf::Cell(40,7,substr($ticket->plan->nombre,0,23),1,0,'L');
                            if($v->servicio_id>0){
                                if(strlen($v->servicio->nombre)>35){
                                    $x=$pdf::GetX();
                                    $y=$pdf::GetY();                    
                                    $pdf::Multicell(70,3,$v->servicio->nombre,0,'L');
                                    $pdf::SetXY($x,$y);
                                    $pdf::Cell(70,7,"",1,0,'C');
                                }else{
                                    $pdf::Cell(70,7,$v->servicio->nombre,1,0,'L');    
                                }
                            }else{
                                $pdf::Cell(70,7,$v->descripcion,1,0,'L');    
                            }
                            $pdf::Cell(18,7,'',1,0,'C');
                            $pdf::Cell(18,7,number_format($v->cantidad*$v->pagohospital,2,'.',''),1,0,'R');
                            $x=$pdf::GetX();
                            $y=$pdf::GetY();                    
                            $pdf::Multicell(20,3,$value->tipotarjeta." ".$value->tarjeta." / ".$value->voucher,0,'L');
                            $pdf::SetXY($x,$y);
                            $pdf::Cell(20,7,"",1,0,'C');
                            $pdf::Cell(20,7,substr($v->persona->nombres,0,1).'. '.$v->persona->apellidopaterno,1,0,'L');
                            $pdf::Ln();
                            //$pago=$pago + number_format($v->cantidad*$v->pagohospital,2,'.','');
                        }
                    }
                    
                }
                */
                elseif(in_array($value->conceptopago_id, $listConcepto) && $value->conceptopago->tipo=='E'){//CONCEPTOS QUE TIENEN LISTA EGRESOS
                    if($value->situacion2<>'R'){
                        $pdf::SetTextColor(0,0,0);
                        if($egreso1>0 && $bandegreso){
                            $pdf::SetFont('helvetica','B',8.5);
                            $pdf::Cell(205,7,'TOTAL',1,0,'R');
                            $pdf::Cell(18,7,number_format($egreso1,2,'.',''),1,0,'R');
                            $bandegreso=false;
                            $pdf::Ln(); 
                        }
                        if($transferenciae==0){
                            $pdf::SetFont('helvetica','B',8.5);
                            $pdf::Cell(281,7,utf8_decode("TRANSFERENCIA"),1,0,'L');
                            $pdf::Ln();
                        }
                        if($value->situacion<>'A'){
                            $pdf::SetTextColor(0,0,0);
                        }else{
                            $pdf::SetTextColor(255,0,0);
                        }
                        $list=explode(",",$value->listapago);
                        $transferenciae = $transferenciae + $value->total;
                        for($c=0;$c<count($list);$c++){
                            $pdf::SetFont('helvetica','',7);
                            $pdf::Cell(15,7,date("d/m/Y",strtotime($value->fecha)),1,0,'C');
                            $detalle = Detallemovcaja::find($list[$c]);
                            $ticket = Movimiento::where("id","=",$detalle->movimiento_id)->first();
                            $venta = Movimiento::where("movimiento_id","=",$detalle->movimiento_id)->first();
                            if(strlen($ticket->persona->movimiento.' '.$ticket->persona->apellidomaterno.' '.$ticket->persona->nombres)>30){
                                $x=$pdf::GetX();
                                $y=$pdf::GetY();                    
                                $pdf::Multicell(60,3,($ticket->persona->apellidopaterno.' '.$ticket->persona->apellidomaterno.' '.$ticket->persona->nombres),0,'L');
                                $pdf::SetXY($x,$y);
                                $pdf::Cell(60,7,"",1,0,'C');
                            }else{
                                $pdf::Cell(60,7,($ticket->persona->apellidopaterno.' '.$ticket->persona->apellidomaterno.' '.$ticket->persona->nombres),1,0,'L');    
                            }
                            $pdf::Cell(8,7,($venta->tipodocumento_id==5?'B':'F'),1,0,'C');
                            $pdf::Cell(12,7,$venta->serie.'-'.$venta->numero,1,0,'C');
                            if($venta->tipodocumento_id==4){
                                $pdf::Cell(40,7,$venta->persona->bussinesname,1,0,'L');
                            }else{
                                $pdf::Cell(40,7,"",1,0,'L');
                            }
                            if($value->conceptopago_id==8){//HONORARIOS MEDICOS
                                $descripcion=$value->conceptopago->nombre.' - RH: '.$detalle->recibo;
                            }else{
                                $descripcion=$value->conceptopago->nombre;
                            }
                            if(strlen($descripcion)>40){
                                $x=$pdf::GetX();
                                $y=$pdf::GetY();
                                $pdf::Multicell(70,3,utf8_decode($descripcion),0,'L');
                                $pdf::SetXY($x,$y);
                                $pdf::Cell(70,7,"",1,0,'L');
                            }else{
                                $pdf::Cell(70,7,utf8_decode($descripcion),1,0,'L');
                            }
                            if($value->conceptopago_id==8){//HONORARIOS MEDICOS
                                $pdf::Cell(14,7,number_format($detalle->pagodoctor,2,'.',''),1,0,'R');
                                $pdf::Cell(14,7,utf8_decode(""),1,0,'C');
                                $pdf::Cell(14,7,utf8_decode(""),1,0,'C');
                                $pdf::Cell(14,7,utf8_decode(""),1,0,'C');
                                $pdf::Cell(20,7,substr($value->persona->nombres,0,1).'. '.$value->persona->apellidopaterno,1,0,'L');
                            }elseif($value->conceptopago_id==16){//TRANSFERENCIA SOCIO
                                $pdf::Cell(14,7,number_format($detalle->pagosocio,2,'.',''),1,0,'R');
                                $pdf::Cell(14,7,utf8_decode(""),1,0,'C');
                                $pdf::Cell(14,7,utf8_decode(""),1,0,'C');
                                $pdf::Cell(14,7,utf8_decode(""),1,0,'C');
                                $pdf::Cell(20,7,substr($value->persona->nombres,0,1).'. '.$value->persona->apellidopaterno,1,0,'L');
                            }elseif($value->conceptopago_id==20){//BOLETEO TOTAL
                                $pdf::Cell(14,7,number_format($detalle->pagotarjeta,2,'.',''),1,0,'R');
                                $pdf::Cell(14,7,utf8_decode(""),1,0,'C');
                                $pdf::Cell(14,7,utf8_decode(""),1,0,'C');
                                $pdf::Cell(14,7,utf8_decode(""),1,0,'C');
                                $pdf::Cell(20,7,substr($value->persona->nombres,0,1).'. '.$value->persona->apellidopaterno,1,0,'L');
                            }elseif($value->conceptopago_id==14){//TARJETA
                                $pdf::Cell(14,7,number_format($detalle->pagotarjeta,2,'.',''),1,0,'R');
                                $pdf::Cell(14,7,utf8_decode(""),1,0,'C');
                                $pdf::Cell(14,7,utf8_decode(""),1,0,'C');
                                $pdf::Cell(14,7,utf8_decode(""),1,0,'C');
                                $pdf::Cell(20,7,substr($value->persona->nombres,0,1).'. '.$value->persona->apellidopaterno,1,0,'L');
                            }
                            $pdf::Ln();
                        }
                    }
                }elseif(in_array($value->conceptopago_id, $listConcepto) && $value->conceptopago->tipo=='I'){//CONCEPTOS QUE TIENEN LISTA INGRESOS
                    $pdf::SetTextColor(0,0,0);
                    if($pago>0 && $bandpago){
                        $pdf::SetFont('helvetica','B',8.5);
                        $pdf::Cell(223,7,'TOTAL',1,0,'R');
                        $pdf::Cell(18,7,number_format($pago,2,'.',''),1,0,'R');
                        $bandpago=false;
                        $pdf::Ln(); 
                    }
                    if($tarjeta>0 && $bandtarjeta){
                        $pdf::SetFont('helvetica','B',8.5);
                        $pdf::Cell(223,7,'TOTAL',1,0,'R');
                        $pdf::Cell(18,7,number_format($tarjeta,2,'.',''),1,0,'R');
                        $bandtarjeta=false;
                        $pdf::Ln(); 
                    }
                    if($transferenciai==0){
                        $pdf::SetFont('helvetica','B',8.5);
                        $pdf::Cell(281,7,utf8_decode("TRANSFERENCIA"),1,0,'L');
                        $pdf::Ln();
                    }
                    if($value->situacion<>'A'){
                        $pdf::SetTextColor(0,0,0);
                    }else{
                        $pdf::SetTextColor(255,0,0);
                    }
                    $transferenciai = $transferenciai + $value->total;
                    $list=explode(",",$value->listapago);
                    for($c=0;$c<count($list);$c++){
                        $pdf::SetFont('helvetica','',7);
                        $pdf::Cell(15,7,date("d/m/Y",strtotime($value->fecha)),1,0,'C');
                        $detalle = Detallemovcaja::find($list[$c]);
                        $venta = Movimiento::where("movimiento_id","=",$detalle->movimiento_id)->first();
                        if(strlen($venta->persona->movimiento.' '.$venta->persona->apellidomaterno.' '.$venta->persona->nombres)>30){
                            $x=$pdf::GetX();
                            $y=$pdf::GetY();                    
                            $pdf::Multicell(60,3,($venta->persona->apellidopaterno.' '.$venta->persona->apellidomaterno.' '.$venta->persona->nombres),0,'L');
                            $pdf::SetXY($x,$y);
                            $pdf::Cell(60,7,"",1,0,'C');
                        }else{
                            $pdf::Cell(60,7,($venta->persona->apellidopaterno.' '.$venta->persona->apellidomaterno.' '.$venta->persona->nombres),1,0,'L');    
                        }
                        $pdf::Cell(8,7,($venta->tipodocumento_id==5?'B':'F'),1,0,'C');
                        $pdf::Cell(12,7,$venta->serie.'-'.$venta->numero,1,0,'C');
                        $pdf::Cell(40,7,"",1,0,'L');
                        if($value->conceptopago_id==8){//HONORARIOS MEDICOS
                            $descripcion=$value->conceptopago->nombre.' - RH: '.$detalle->recibo;
                        }else{
                            $descripcion=$value->conceptopago->nombre;
                        }
                        if(strlen($descripcion)>40){
                            $x=$pdf::GetX();
                            $y=$pdf::GetY();
                            $pdf::Multicell(70,3,utf8_decode($descripcion),0,'L');
                            $pdf::SetXY($x,$y);
                            $pdf::Cell(70,7,"",1,0,'L');
                        }else{
                            $pdf::Cell(70,7,utf8_decode($descripcion),1,0,'L');
                        }
                        if($value->conceptopago_id==21 ){//BOLETEO TOTAL
                            $pdf::Cell(18,7,utf8_decode(""),1,0,'C');
                            $pdf::Cell(18,7,number_format($detalle->pagotarjeta,2,'.',''),1,0,'R');
                            $pdf::Cell(20,7,utf8_decode(""),1,0,'C');
                            $pdf::Cell(20,7,substr($value->persona->nombres,0,1).'. '.$value->persona->apellidopaterno,1,0,'L');
                        }elseif($value->conceptopago_id==17){// TRANSFERENCIA SOCIO
                            $pdf::Cell(18,7,utf8_decode(""),1,0,'C');
                            $pdf::Cell(18,7,number_format($detalle->pagosocio,2,'.',''),1,0,'R');
                            $pdf::Cell(20,7,utf8_decode(""),1,0,'C');
                            $pdf::Cell(20,7,substr($value->persona->nombres,0,1).'. '.$value->persona->apellidopaterno,1,0,'L');
                        }elseif($value->conceptopago_id==15){//TARJETA
                            $pdf::Cell(18,7,utf8_decode(""),1,0,'C');
                            $pdf::Cell(18,7,number_format($detalle->pagotarjeta,2,'.',''),1,0,'R');
                            $pdf::Cell(20,7,utf8_decode(""),1,0,'C');
                            $pdf::Cell(20,7,substr($value->persona->nombres,0,1).'. '.$value->persona->apellidopaterno,1,0,'L');
                        }
                        $pdf::Ln();   
                    }
                }elseif(in_array($value->conceptopago_id, $listConcepto2) && $value->conceptopago->tipo=='E'){//CONCEPTOS QUE TIENEN LISTA2
                    /*$pdf::SetTextColor(0,0,0);
                    if($egreso==0){
                        $pdf::SetFont('helvetica','B',8.5);
                        $pdf::Cell(279,7,utf8_decode("EGRESO"),1,0,'L');
                        $pdf::Ln();
                    }
                    if($value->situacion<>'A'){
                        $pdf::SetTextColor(0,0,0);
                    }else{
                        $pdf::SetTextColor(255,0,0);
                    }
                    $list=explode(",",$value->listapago);//print_r($value->listapago."-");
                    for($c=0;$c<count($list);$c++){
                        $detalle = Detallemovcaja::find($list[$c]);
                        $venta = Movimiento::where("movimiento_id","=",$detalle->movimiento_id)->first();
                        $pdf::SetFont('helvetica','',7);
                        $pdf::Cell(15,7,date("d/m/Y",strtotime($value->fecha)),1,0,'C');
                        if(strlen($venta->persona->apellidopaterno.' '.$venta->persona->apellidomaterno.' '.$venta->persona->nombres)>30){
                            $x=$pdf::GetX();
                            $y=$pdf::GetY();                    
                            $pdf::Multicell(60,3,($venta->persona->apellidopaterno.' '.$venta->persona->apellidomaterno.' '.$venta->persona->nombres),0,'L');
                            $pdf::SetXY($x,$y);
                            $pdf::Cell(60,7,"",1,0,'C');
                        }else{
                            $pdf::Cell(60,7,($venta->persona->apellidopaterno.' '.$venta->persona->apellidomaterno.' '.$venta->persona->nombres),1,0,'L');    
                        }
                        $pdf::Cell(8,7,($venta->tipodocumento_id==5?'B':'F'),1,0,'C');
                        $pdf::Cell(10,7,$venta->serie.'-'.$venta->numero,1,0,'C');
                        $pdf::Cell(40,7,"",1,0,'L');
                        if($value->conceptopago_id==8){//HONORARIOS MEDICOS
                            $descripcion=$value->conceptopago->nombre.' - RH: '.$detalle->recibo;
                        }else{
                            $descripcion=$value->conceptopago->nombre;
                        }
                        if(strlen($descripcion)>40){
                            $x=$pdf::GetX();
                            $y=$pdf::GetY();
                            $pdf::Multicell(70,3,utf8_decode($descripcion),0,'L');
                            $pdf::SetXY($x,$y);
                            $pdf::Cell(70,7,"",1,0,'L');
                        }else{
                            $pdf::Cell(70,7,utf8_decode($descripcion),1,0,'L');
                        }
                        if($value->conceptopago_id==8){//HONORARIOS MEDICOS
                            $pdf::Cell(18,7,number_format($detalle->pagodoctor,2,'.',''),1,0,'R');
                            $pdf::Cell(18,7,utf8_decode(""),1,0,'C');
                            $pdf::Cell(20,7,utf8_decode(""),1,0,'C');
                            $pdf::Cell(20,7,substr($value->persona->nombres,0,1).'. '.$value->persona->apellidopaterno,1,0,'L');
                        }else{//SOCIO
                            $pdf::Cell(18,7,number_format($detalle->pagosocio,2,'.',''),1,0,'R');
                            $pdf::Cell(18,7,utf8_decode(""),1,0,'C');
                            $pdf::Cell(20,7,utf8_decode(""),1,0,'C');
                            $pdf::Cell(20,7,substr($value->persona->nombres,0,1).'. '.$value->persona->apellidopaterno,1,0,'L');
                        }
                        $pdf::Ln();   
                    }*/
                }elseif(in_array($value->conceptopago_id, $listConcepto3) && $value->conceptopago->tipo=='E'){//CONCEPTOS QUE TIENEN LISTA3
                    $pdf::SetTextColor(0,0,0);
                    if($egreso1==0){
                        $pdf::SetFont('helvetica','B',8.5);
                        $pdf::Cell(281,7,utf8_decode("EGRESO"),1,0,'L');
                        $pdf::Ln();
                    }
                    $pdf::SetFont('helvetica','',7);
                    $pdf::Cell(15,7,date("d/m/Y",strtotime($value->fecha)),1,0,'C');
                    if(strlen($value->persona->apellidopaterno.' '.$value->persona->apellidomaterno.' '.$value->persona->nombres)>30){
                        $x=$pdf::GetX();
                        $y=$pdf::GetY();                    
                        $pdf::Multicell(60,3,($value->persona->apellidopaterno.' '.$value->persona->apellidomaterno.' '.$value->persona->nombres),0,'L');
                        $pdf::SetXY($x,$y);
                        $pdf::Cell(60,7,"",1,0,'C');
                    }else{
                        $pdf::Cell(60,7,($value->persona->apellidopaterno.' '.$value->persona->apellidomaterno.' '.$value->persona->nombres),1,0,'L');    
                    }   
                    $pdf::Cell(8,7,'RH',1,0,'C');
                    $list=explode(",",$value->listapago);
                    $detalle = Detallemovcaja::find($list[0]);
                    if($value->conceptopago_id==25)//pago de socio
                        $pdf::Cell(12,7,$detalle->recibo2,1,0,'C');
                    else
                        $pdf::Cell(12,7,$detalle->recibo,1,0,'C');
                    $pdf::Cell(40,7,"",1,0,'L');
                    $descripcion=$value->conceptopago->nombre;
                    if(strlen($descripcion)>40){
                        $x=$pdf::GetX();
                        $y=$pdf::GetY();
                        $pdf::Multicell(70,3,utf8_decode($descripcion),0,'L');
                        $pdf::SetXY($x,$y);
                        $pdf::Cell(70,7,"",1,0,'L');
                    }else{
                        $pdf::Cell(70,7,utf8_decode($descripcion),1,0,'L');
                    }
                    $pdf::Cell(18,7,number_format($value->total,2,'.',''),1,0,'R');
                    $pdf::Cell(18,7,utf8_decode(""),1,0,'C');
                    $pdf::Cell(20,7,utf8_decode(""),1,0,'C');
                    $pdf::Cell(20,7,utf8_decode(""),1,0,'C');
                    $pdf::Ln();
                    $egreso1 = $egreso1 + $value->total;
                }elseif($value->conceptopago_id==23 || $value->conceptopago_id == 32){//COBRANZA
                    if ($caja_id == 3 || $caja_id == 4 && $value->conceptopago_id == 32) {
                        if($pago>0 && $bandpago){
                            $pdf::SetFont('helvetica','B',8.5);
                            $pdf::Cell(223,7,'TOTAL',1,0,'R');
                            $pdf::Cell(18,7,number_format($pago,2,'.',''),1,0,'R');
                            $bandpago=false;
                            $pdf::Ln(); 
                        }
                        if($tarjeta>0 && $bandtarjeta){
                            $pdf::SetFont('helvetica','B',8.5);
                            $pdf::Cell(223,7,'TOTAL',1,0,'R');
                            $pdf::Cell(18,7,number_format($tarjeta,2,'.',''),1,0,'R');
                            $bandtarjeta=false;
                            $pdf::Ln(); 
                        }
                        $listventas = Movimiento::where('movimientodescarga_id','=',$value->id)->get();

                        if($cobranza==0 && $value->conceptopago->tipo=="I"){
                            $pdf::SetFont('helvetica','B',8.5);
                            $pdf::Cell(281,7,utf8_decode("COBRANZA"),1,0,'L');
                            $pdf::Ln();
                        }
                        foreach ($listventas as $key6 => $value6) {
                            $pdf::SetFont('helvetica','',7);
                            $pdf::Cell(15,7,date("d/m/Y",strtotime($value6->fecha)),1,0,'C');
                            $nombrepersona = '-';
                            if ($value6->persona_id !== NULL) {
                                //echo 'entro'.$value6->id;break;
                                $nombrepersona =$value6->persona->apellidopaterno.' '.$value6->persona->apellidomaterno.' '.$value6->persona->nombres;

                            }else{
                                $nombrepersona = trim($value6->nombrepaciente);
                            }
                            if(strlen($nombrepersona)>30){
                                $x=$pdf::GetX();
                                $y=$pdf::GetY();                    
                                $pdf::Multicell(60,3,($nombrepersona),0,'L');
                                $pdf::SetXY($x,$y);
                                $pdf::Cell(60,7,"",1,0,'C');
                            }else{
                                $pdf::Cell(60,7,($nombrepersona),1,0,'L');    
                            }
                            $pdf::Cell(8,7,($value6->tipodocumento_id==5?'B':'F'),1,0,'C');
                            $pdf::Cell(12,7,$value6->serie.'-'.$value6->numero,1,0,'C');
                            $nombreempresa = '';
                            if ($value6->conveniofarmacia_id != null) {
                                $nombreempresa = $value6->conveniofarmacia->nombre;
                            }
                            $pdf::Cell(40,7,substr($nombreempresa,0,23),1,0,'L');
                            $pdf::Cell(70,7,'MEDICINA',1,0,'L');
                            $pdf::Cell(18,7,'',1,0,'C');
                            $pdf::Cell(18,7,number_format($value6->total,2,'.',''),1,0,'R');
                            $pdf::Cell(20,7,utf8_decode(" - "),1,0,'C');
                            if ($value6->doctor_id != null) {
                                $pdf::Cell(20,7,substr($value6->doctor->nombres,0,1).'. '.$value6->doctor->apellidopaterno,1,0,'L');

                            }else{
                               $pdf::Cell(20,7," - ",1,0,'L'); 
                            }
                            $cobranza=$cobranza + $value6->total;
                            $pdf::Ln();
                        }
                    }elseif($caja_id == 3 || $caja_id == 4 && $value->conceptopago_id == 23){
                        if($pago>0 && $bandpago){
                            $pdf::SetFont('helvetica','B',8.5);
                            $pdf::Cell(223,7,'TOTAL',1,0,'R');
                            $pdf::Cell(18,7,number_format($pago,2,'.',''),1,0,'R');
                            $bandpago=false;
                            $pdf::Ln(); 
                        }
                        if($tarjeta>0 && $bandtarjeta){
                            $pdf::SetFont('helvetica','B',8.5);
                            $pdf::Cell(223,7,'TOTAL',1,0,'R');
                            $pdf::Cell(18,7,number_format($tarjeta,2,'.',''),1,0,'R');
                            $bandtarjeta=false;
                            $pdf::Ln(); 
                        }
                        $listventas = Movimiento::where('movimiento_id','=',$value->id)->get();
                        if($cobranza==0 && $value->conceptopago->tipo=="I"){
                            $pdf::SetFont('helvetica','B',8.5);
                            $pdf::Cell(281,7,utf8_decode("COBRANZA"),1,0,'L');
                            $pdf::Ln();
                        }
                        foreach ($listventas as $key6 => $value6) {
                            $pdf::SetFont('helvetica','',7);
                            $pdf::Cell(15,7,date("d/m/Y",strtotime($value6->fecha)),1,0,'C');
                            $nombrepersona = '-';
                            if ($value6->persona_id !== NULL) {
                                //echo 'entro'.$value6->id;break;
                                $nombrepersona =$value6->persona->apellidopaterno.' '.$value6->persona->apellidomaterno.' '.$value6->persona->nombres;

                            }else{
                                $nombrepersona = trim($value6->nombrepaciente);
                            }
                            if(strlen($nombrepersona)>30){
                                $x=$pdf::GetX();
                                $y=$pdf::GetY();                    
                                $pdf::Multicell(60,3,($nombrepersona),0,'L');
                                $pdf::SetXY($x,$y);
                                $pdf::Cell(60,7,"",1,0,'C');
                            }else{
                                $pdf::Cell(60,7,($nombrepersona),1,0,'L');    
                            }
                            $pdf::Cell(8,7,($value6->tipodocumento_id==5?'B':'F'),1,0,'C');
                            $pdf::Cell(12,7,$value6->serie.'-'.$value6->numero,1,0,'C');
                            $nombreempresa = '';
                            if ($value6->conveniofarmacia_id != null) {
                                $nombreempresa = $value6->conveniofarmacia->nombre;
                            }
                            $pdf::Cell(40,7,substr($nombreempresa,0,23),1,0,'L');
                            $pdf::Cell(70,7,'MEDICINA',1,0,'L');
                            $pdf::Cell(18,7,'',1,0,'C');
                            $pdf::Cell(18,7,number_format($value6->total,2,'.',''),1,0,'R');
                            $pdf::Cell(20,7,utf8_decode(" - "),1,0,'C');
                            if ($value6->doctor_id != null) {
                                $pdf::Cell(20,7,substr($value6->doctor->nombres,0,1).'. '.$value6->doctor->apellidopaterno,1,0,'L');

                            }else{
                               $pdf::Cell(20,7," - ",1,0,'L'); 
                            }
                            $cobranza=$cobranza + $value6->total;
                            $pdf::Ln();
                        }
                    }else{

                        $pdf::SetTextColor(0,0,0);
                        if($pago>0 && $bandpago){
                            $pdf::SetFont('helvetica','B',8.5);
                            $pdf::Cell(223,7,'TOTAL',1,0,'R');
                            $pdf::Cell(18,7,number_format($pago,2,'.',''),1,0,'R');
                            $bandpago=false;
                            $pdf::Ln(); 
                        }
                        if($tarjeta>0 && $bandtarjeta){
                            $pdf::SetFont('helvetica','B',8.5);
                            $pdf::Cell(223,7,'TOTAL',1,0,'R');
                            $pdf::Cell(18,7,number_format($tarjeta,2,'.',''),1,0,'R');
                            $bandtarjeta=false;
                            $pdf::Ln(); 
                        }
                        if($ingresotarjeta>0 && $bandingresotarjeta){
                            $pdf::SetFont('helvetica','B',8.5);
                            $pdf::Cell(223,7,'TOTAL',1,0,'R');
                            $pdf::Cell(18,7,number_format($ingresotarjeta,2,'.',''),1,0,'R');
                            $bandingresotarjeta=false;
                            $pdf::Ln(); 
                        }
                        if($cobranza==0 && $value->conceptopago->tipo=="I"){
                            $pdf::SetFont('helvetica','B',8.5);
                            $pdf::Cell(281,7,utf8_decode("COBRANZA"),1,0,'L');
                            $pdf::Ln();
                        }
                        if($value->situacion<>'A'){
                            $pdf::SetTextColor(0,0,0);
                        }else{
                            $pdf::SetTextColor(255,0,0);
                        }
                        $pdf::SetFont('helvetica','',7);
                        $pdf::Cell(15,7,date("d/m/Y",strtotime($value->fecha)),1,0,'C');
                        $nombrepersona = '-';
                        if ($value->persona_id !== NULL) {
                            //echo 'entro'.$value->id;break;
                            $nombrepersona =$value->persona->apellidopaterno.' '.$value->persona->apellidomaterno.' '.$value->persona->nombres;

                        }else{
                            $nombrepersona = trim($value->nombrepaciente);
                        }
                        if(strlen($nombrepersona)>30){
                            $x=$pdf::GetX();
                            $y=$pdf::GetY();                    
                            $pdf::Multicell(60,3,($nombrepersona),0,'L');
                            $pdf::SetXY($x,$y);
                            $pdf::Cell(60,7,"",1,0,'C');
                        }else{
                            $pdf::Cell(60,7,($nombrepersona),1,0,'L');    
                        }
                        $venta = Movimiento::find($value->movimiento_id);
                        $pdf::Cell(8,7,($venta->tipodocumento_id==5?'B':"F"),1,0,'C');
                        $pdf::Cell(12,7,utf8_decode($venta->serie.'-'.$venta->numero),1,0,'C');
                        if($value->conceptopago_id==11){//PAGO A ENFERMERIA
                            $descripcion=$value->conceptopago->nombre.': '.$value->comentario.' - RH: '.$value->voucher;
                        }else{
                            $descripcion=$value->conceptopago->nombre.': '.$value->comentario;
                        }
                        if(strlen($descripcion)>70){
                            $x=$pdf::GetX();
                            $y=$pdf::GetY();
                            $pdf::Multicell(110,3,($descripcion),0,'L');
                            $pdf::SetXY($x,$y);
                            $pdf::Cell(110,7,"",1,0,'L');
                        }else{
                            $pdf::Cell(110,7,($descripcion),1,0,'L');
                        }
                        if($value->situacion<>'R' && $value->situacion2<>'R'){
                            if($value->conceptopago->tipo=="I"){
                                $pdf::Cell(18,7,utf8_decode(""),1,0,'R');
                                $pdf::Cell(18,7,number_format($value->total,2,'.',''),1,0,'R');
                            }else{
                                $pdf::Cell(18,7,number_format($value->total,2,'.',''),1,0,'R');
                                $pdf::Cell(18,7,utf8_decode(""),1,0,'C');
                            }
                        }else{
                            $pdf::Cell(18,7,utf8_decode(" - "),1,0,'C');
                            $pdf::Cell(18,7,utf8_decode(" - "),1,0,'C');
                        }
                        $cobranza=$cobranza + $value->total;
                        $pdf::Cell(20,7,utf8_decode(""),1,0,'C');
                        $pdf::Cell(20,7,utf8_decode(""),1,0,'C');
                        $pdf::Ln();
                    }
                }elseif($value->conceptopago_id==33){//PAGO DE FARMACIA
                    $pdf::SetTextColor(0,0,0);
                    if($transferenciae>0 && $bandtransferenciae){
                        $pdf::SetFont('helvetica','B',8.5);
                        $pdf::Cell(205,7,'TOTAL',1,0,'R');
                        $pdf::Cell(18,7,number_format($transferenciae,2,'.',''),1,0,'R');
                        $transferenciae=false;
                        $pdf::Ln(); 
                    }
                    if($tarjeta>0 && $bandtarjeta){
                        $pdf::SetFont('helvetica','B',8.5);
                        $pdf::Cell(223,7,'TOTAL',1,0,'R');
                        $pdf::Cell(18,7,number_format($tarjeta,2,'.',''),1,0,'R');
                        $bandtarjeta=false;
                        $pdf::Ln(); 
                    }
                    if($pago>0 && $bandpago){
                            $pdf::SetFont('helvetica','B',8.5);
                            $pdf::Cell(223,7,'TOTAL',1,0,'R');
                            $pdf::Cell(18,7,number_format($pago,2,'.',''),1,0,'R');
                            $bandpago=false;
                            $pdf::Ln(); 
                        }

                    if($ingresotarjeta==0){
                        $pdf::SetFont('helvetica','B',8.5);
                        $pdf::Cell(281,7,utf8_decode("INGRESOS POR TRANSFERENCIA"),1,0,'L');
                        $pdf::Ln();
                    }
                    if($value->situacion<>'A'){
                        $pdf::SetTextColor(0,0,0);
                    }else{
                        $pdf::SetTextColor(255,0,0);
                    }
                    $pdf::SetFont('helvetica','',7);
                    $pdf::Cell(15,7,date("d/m/Y",strtotime($value->fecha)),1,0,'C');
                    $nombrepersona = '-';
                    if ($value->persona_id != NULL && !is_null($value->persona)) {
                        //echo 'entro'.$value->id;break;
                        if ($value->persona->bussinesname != null) {
                            $nombrepersona = $value->persona->bussinesname;
                        }else{
                            $nombrepersona =$value->persona->apellidopaterno.' '.$value->persona->apellidomaterno.' '.$value->persona->nombres;
                        }
                        
                    }else{
                        $nombrepersona = trim($value->nombrepaciente);
                    }
                    if(strlen($nombrepersona)>30){
                        $x=$pdf::GetX();
                        $y=$pdf::GetY();                    
                        $pdf::Multicell(60,3,($nombrepersona),0,'L');
                        $pdf::SetXY($x,$y);
                        $pdf::Cell(60,7,"",1,0,'C');
                    }else{
                        $pdf::Cell(60,7,($nombrepersona),1,0,'L');    
                    }
                    if($value->conceptopago_id==31){
                        $pdf::Cell(8,7,'T',1,0,'C');
                    }else{
                        $pdf::Cell(8,7,trim($value->formapago==''?'':$value->formapago),1,0,'C'); 
                        
                    }
                    $pdf::Cell(12,7,utf8_decode(trim($value->voucher)==''?$value->numero:$value->voucher),1,0,'C');
                
                    $descripcion=$value->conceptopago->nombre.': '.$value->comentario;
                    
                    if(strlen($descripcion)>70){
                        $x=$pdf::GetX();
                        $y=$pdf::GetY();
                        $pdf::Multicell(110,3,SUBSTR($descripcion,0,150),0,'L');
                        $pdf::SetXY($x,$y);
                        $pdf::Cell(110,7,"",1,0,'L');
                    }else{
                        $pdf::Cell(110,7,($descripcion),1,0,'L');
                    }
                    if($value->situacion<>'R' && $value->situacion2<>'R'){
                        if($value->conceptopago->tipo=="I"){
                            $pdf::Cell(18,7,utf8_decode(""),1,0,'R');
                            $pdf::Cell(18,7,number_format($value->total,2,'.',''),1,0,'R');
                            $ingresotarjeta=$ingresotarjeta + $value->total;
                        }else{
                            $egreso1=$egreso1 + $value->total;
                            $pdf::Cell(18,7,number_format($value->total,2,'.',''),1,0,'R');
                            $pdf::Cell(18,7,utf8_decode(""),1,0,'C');
                        }
                    }else{
                        $pdf::Cell(18,7,utf8_decode(" - "),1,0,'C');
                        $pdf::Cell(18,7,utf8_decode(" - "),1,0,'C');
                    }
                    $pdf::Cell(20,7,utf8_decode(""),1,0,'C');
                    $pdf::Cell(20,7,utf8_decode(""),1,0,'C');
                    $pdf::Ln();
                }elseif($value->conceptopago_id!=1 && $value->conceptopago_id!=2 && $value->conceptopago_id!=23 && $value->conceptopago_id!=10){
                    $pdf::SetTextColor(0,0,0);
                    if($transferenciae>0 && $bandtransferenciae){
                        $pdf::SetFont('helvetica','B',8.5);
                        $pdf::Cell(205,7,'TOTAL',1,0,'R');
                        $pdf::Cell(18,7,number_format($transferenciae,2,'.',''),1,0,'R');
                        $transferenciae=false;
                        $pdf::Ln(); 
                    }
                    if(($ingreso==0 || $pago==0) && $value->conceptopago->tipo=="I"){
                        $pdf::SetFont('helvetica','B',8.5);
                        $pdf::Cell(281,7,utf8_decode("INGRESOSOK"),1,0,'L');
                        $pdf::Ln();
                        if($pago==0){
                            if($caja_id==3){
                                $pdf::SetFont('helvetica','B',7);
                                $pdf::Cell(223,7,utf8_decode("SALDO INICIAL"),1,0,'L');
                                $apert = Movimiento::find($movimiento_mayor);
                                $pdf::Cell(18,7,number_format($apert->total,2,'.',''),1,0,'R');
                                $pago = $pago + $apert->total;
                                $ingreso = $ingreso + $apert->total;
                                $pdf::Ln();
                            }
                        }
                    }elseif($egreso1==0){
                        //$pdf::SetFont('helvetica','B',8.5);
                        //$pdf::Cell(281,7,utf8_decode("EGRESOS"),1,0,'L');
                        //$pdf::Ln();
                    }
                    if($value->situacion<>'A'){
                        $pdf::SetTextColor(0,0,0);
                    }else{
                        $pdf::SetTextColor(255,0,0);
                    }
                    $pdf::SetFont('helvetica','',6);
                    $pdf::Cell(15,7,date("d/m/Y",strtotime($value->fecha)),1,0,'C');
                    $nombrepersona = '-';
                    if ($value->persona_id != NULL && !is_null($value->persona)) {
                        //echo 'entro'.$value->id;break;
                        if ($value->persona->bussinesname != null) {
                            $nombrepersona = $value->persona->bussinesname;
                        }else{
                            $nombrepersona =$value->persona->apellidopaterno.' '.$value->persona->apellidomaterno.' '.$value->persona->nombres;
                        }
                        
                    }else{
                        $nombrepersona = trim($value->nombrepaciente);
                    }
                    if(strlen($nombrepersona)>30){
                        $x=$pdf::GetX();
                        $y=$pdf::GetY();                    
                        $pdf::Multicell(56,3,($nombrepersona),0,'L');
                        $pdf::SetXY($x,$y);
                        $pdf::Cell(56,7,"",1,0,'C');
                    }else{
                        $pdf::Cell(56,7,($nombrepersona),1,0,'L');    
                    }
                    if($value->conceptopago_id!=13){
                        if($value->conceptopago_id==31){
                            $pdf::Cell(8,7,'T',1,0,'C');
                        }else{
                            if ($caja_id == 3 || $caja_id == 4) {
                                if ($value->tipodocumento_id == 7) {
                                    $pdf::Cell(8,7,'BV',1,0,'C');
                                }elseif($value->tipodocumento_id == 6){
                                    $pdf::Cell(8,7,'FT',1,0,'C');
                                }else{
                                    $pdf::Cell(8,7,trim($value->formapago==''?'':$value->formapago),1,0,'C'); 
                                }
                            }else{
                               $pdf::Cell(8,7,trim($value->formapago==''?'':$value->formapago),1,0,'C'); 
                            }
                            
                        }
                        $pdf::Cell(12,7,utf8_decode(trim($value->voucher)==''?$value->numero:$value->voucher),1,0,'C');
                    }else{//PARA ANULACION POR NOTA CREDITO
                        $pdf::Cell(8,7,'NA',1,0,'C');
                        $mov = Movimiento::find($value->movimiento_id);
                        $pdf::Cell(12,7,($mov->serie.'-'.$mov->numero),1,0,'C');
                    }

                    if($value->conceptopago_id==11){//PAGO A ENFERMERIA
                        $descripcion=$value->conceptopago->nombre.': '.$value->comentario;
                    }else{
                        $descripcion=$value->conceptopago->nombre.': '.$value->comentario;
                    }
                    if(strlen($descripcion)>70){
                        $x=$pdf::GetX();
                        $y=$pdf::GetY();
                        $pdf::Multicell(114,3,SUBSTR($descripcion,0,150),0,'L');
                        $pdf::SetXY($x,$y);
                        $pdf::Cell(114,7,"",1,0,'L');
                    }else{
                        $pdf::Cell(114,7,($descripcion),1,0,'L');
                    }
                    if($value->situacion<>'R' && $value->situacion2<>'R'){
                        if($value->conceptopago->tipo=="I"){
                            $pdf::Cell(14,7,utf8_decode(""),1,0,'R');
                            $pdf::Cell(14,7,number_format($value->total,2,'.',''),1,0,'R');
                            $pdf::Cell(14,7,utf8_decode(""),1,0,'R');
                            $pdf::Cell(14,7,utf8_decode(""),1,0,'R');
                            $pago=$pago + $value->total;
                        }else{
                            $egreso1=$egreso1 + $value->total;
                            $pdf::Cell(14,7,number_format($value->total,2,'.',''),1,0,'R');
                            $pdf::Cell(14,7,utf8_decode(""),1,0,'R');
                            $pdf::Cell(14,7,utf8_decode(""),1,0,'R');
                            $pdf::Cell(14,7,utf8_decode(""),1,0,'R');
                        }
                    }else{
                        $pdf::Cell(14,7,utf8_decode(""),1,0,'C');
                        $pdf::Cell(14,7,utf8_decode(""),1,0,'C');
                        $pdf::Cell(14,7,utf8_decode(""),1,0,'C');
                        $pdf::Cell(14,7,utf8_decode(""),1,0,'C');
                    }
                    $pdf::Cell(20,7,utf8_decode(""),1,0,'C');
                    $pdf::Ln();
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
                $res=$value->responsable2;
                if ($caja_id == 3 || $caja_id == 4) {
                    /*if($tarjeta>0 && $bandtarjeta){
                        $pdf::SetFont('helvetica','B',8.5);
                        $pdf::Cell(223,7,'TOTAL',1,0,'R');
                        $pdf::Cell(18,7,number_format($tarjeta,2,'.',''),1,0,'R');
                        $bandtarjeta=false;
                        $pdf::Ln(); 
                    }*/
                }
            }

            //Solo para tickets

            $sucursal_id = Session::get('sucursal_id');
            $caja_id = $request->input('caja_id');
            $resultadox = Movimiento::leftjoin('movimiento as m2','movimiento.movimiento_id','=','m2.id')
                    ->leftjoin('person as paciente', 'paciente.id', '=', 'movimiento.persona_id')
                    ->where('movimiento.tipomovimiento_id', '=', 2)
                    ->where('movimiento.tipodocumento_id', '=', 2)
                    ->where('movimiento.situacion2','=','Z')
                    ->where('movimiento.sucursal_id', '=', $sucursal_id)
                    ->where('movimiento.caja_id', '=', $caja_id);
            $resultadox = $resultadox->select('movimiento.fecha','movimiento.numero','movimiento.total','movimiento.totalpagado','movimiento.totalpagadovisa','movimiento.totalpagadomaster','m2.numero as numeroticket',DB::raw('concat(paciente.apellidopaterno,\' \',paciente.apellidomaterno,\' \',paciente.nombres) as paciente'), 'movimiento.total')->orderBy('movimiento.numero', 'asc');
            
            $listax           = $resultadox->get();

            if(count($listax)>0){
                $pdf::SetFont('helvetica','B',8.5);
                $pdf::Cell(281,7,'CUOTAS',1,0,'L');
                $pdf::Ln();
                foreach ($listax as $row) { 
                    $pdf::SetFont('helvetica','',6);                   
                    $pdf::Cell(15,7,utf8_decode($row['fecha']),1,0,'C');
                    $pdf::Cell(56,7,$row['paciente'],1,0,'L');
                    $pdf::Cell(8,7,'C',1,0,'C');
                    $pdf::Cell(12,7,utf8_decode($row['numero']),1,0,'C');
                    $pdf::Cell(114,7,"PAGO DE CUOTA DE TICKET N° " . $row['numeroticket'],1,0,'L');
                    $pdf::Cell(14,7,'',1,0,'R');
                    $valuetp = number_format($row['totalpagado'],2,'.','');
                    $valuetpv = number_format($row['totalpagadovisa'],2,'.','');
                    $valuetpm = number_format($row['totalpagadomaster'],2,'.','');
                    if($valuetp == 0){$valuetp='';}
                    if($valuetpv == 0){$valuetpv='';}
                    if($valuetpm == 0){$valuetpm='';}
                    $pdf::Cell(14,7,$valuetp,1,0,'R');                    
                    $pdf::Cell(14,7,$valuetpv,1,0,'R');
                    $pdf::Cell(14,7,$valuetpm,1,0,'R');
                    $pdf::Cell(20,7,utf8_decode("-"),1,0,'C');
                    $pdf::Ln();
                    $totalvisa += number_format($row['totalpagadovisa'],2,'.','');
                    $totalmaster += number_format($row['totalpagadomaster'],2,'.','');
                    $totalefectivo += number_format($row['totalpagado'],2,'.','');
                }                    
            }

            if($ingresotarjeta>0 && $bandingresotarjeta){
                $pdf::SetFont('helvetica','B',8.5);
                $pdf::Cell(219,7,'TOTAL',1,0,'R');
                $pdf::Cell(18,7,number_format($ingresotarjeta,2,'.',''),1,0,'R');
                $bandingresotarjeta=false;
                $pdf::Ln(); 
            }
            if($cobranza>0 && $bandcobranza){
                $pdf::SetFont('helvetica','B',8.5);
                $pdf::Cell(219,7,'TOTAL',1,0,'R');
                $pdf::Cell(18,7,number_format($cobranza,2,'.',''),1,0,'R');
                $bandpago=false;
                $pdf::Ln(); 
            }
            if($transferenciai>0 && $bandtransferenciai){
                $pdf::SetFont('helvetica','B',8.5);
                $pdf::Cell(219,7,'TOTAL',1,0,'R');
                $pdf::Cell(18,7,number_format($transferenciai,2,'.',''),1,0,'R');
                $bandpago=false;
                $pdf::Ln(); 
            }
            if($pago==0){
                $pdf::SetFont('helvetica','B',8.5);
                $pdf::Cell(219,7,utf8_decode("INGRESOS"),1,0,'L');
                $pdf::Ln();
                if($caja_id==3){
                    $pdf::SetFont('helvetica','B',7);
                    $pdf::Cell(223,7,utf8_decode("SALDO INICIAL"),1,0,'L');
                    $apert = Movimiento::find($movimiento_mayor);
                    $pdf::Cell(18,7,number_format($apert->total,2,'.',''),1,0,'R');
                    $pago = $pago + $apert->total;
                    $ingreso = $ingreso + $apert->total;
                    $pdf::Ln();
                }
            }
            if($pago>0 && $bandpago){
                $pdf::SetFont('helvetica','B',8.5);
                $pdf::Cell(219,7,'TOTAL',1,0,'R');
                $pdf::Cell(42,7,number_format($ingreso,2,'.',''),1,0,'C');
                $bandpago=false;
                $pdf::Ln(); 
            }
            /*
            if($tarjeta>0 && $bandtarjeta){
                $pdf::SetFont('helvetica','B',8.5);
                $pdf::Cell(219,7,'TOTAL',1,0,'R');
                $pdf::Cell(42,7,number_format($tarjeta,2,'.',''),1,0,'C');
                $bandtarjeta=false;
                $pdf::Ln(); 
            }
            */
            $resultado1       = Movimiento::join('person as responsable', 'responsable.id', '=', 'movimiento.responsable_id')
                                ->leftjoin('movimiento as m2','movimiento.movimiento_id','=','m2.id')
                                ->leftjoin('person as paciente', 'paciente.id', '=', 'm2.persona_id')
                                ->where('movimiento.serie', '=', $serie)
                                ->where('movimiento.tipomovimiento_id', '=', 4)
                                ->where('movimiento.tipodocumento_id', '<>', 15)
                                ->where('movimiento.sucursal_id','=',$sucursal_id)
                                ->where('movimiento.id', '>', $movimiento_mayor)
                                ->where('movimiento.situacion','like','U');
            $resultado1       = $resultado1->select('movimiento.*','m2.situacion as situacion2',DB::raw('concat(paciente.apellidopaterno,\' \',paciente.apellidomaterno,\' \',paciente.nombres) as paciente2'))->orderBy('movimiento.numero', 'asc');
            
            $lista1           = $resultado1->get();
            if(count($lista1)>0){
                //echo 'alert('.count($lista1).')';
                $anuladas=0;
                $pdf::SetFont('helvetica','B',8.5);
                $pdf::Cell(281,7,utf8_decode("ANULADAS"),1,0,'L');
                $pdf::Ln();
                foreach($lista1 as $key1 => $value1){
                    $pdf::SetFont('helvetica','',7.5);
                    $pdf::Cell(15,7,date("d/m/Y",strtotime($value1->fecha)),1,0,'C');
                    if($value1->tipodocumento_id==5){//BOLETA}
                        $nombre='ANULADO';
                        //$value1->persona->apellidopaterno.' '.$value1->persona->apellidomaterno.' '.$value1->persona->nombres;
                    }else{
                        $nombre=$value1->paciente2;
                        $empresa=$value1->persona->bussinesname;
                    }
                    if(strlen($nombre)>30){
                        $x=$pdf::GetX();
                        $y=$pdf::GetY();                    
                        $pdf::Multicell(60,3,($nombre),0,'L');
                        $pdf::SetXY($x,$y);
                        $pdf::Cell(60,7,"",1,0,'C');
                    }else{
                        $pdf::Cell(60,7,($nombre),1,0,'L');    
                    }
                    $pdf::Cell(8,7,($value1->tipodocumento_id==5?'B':'F'),1,0,'C');
                    $pdf::Cell(12,7,$value1->serie.'-'.$value1->numero,1,0,'C');
                    if($caja_id==4){
                        $nombreempresa='-';
                        if ($value->tipodocumento_id != 5) {
                            if ($value->empresa != null) {
                                $nombreempresa = trim($value->empresa->bussinesname);
                            }
                        }
                        $pdf::Cell(40,7,substr($nombreempresa,0,23),1,0,'L');
                    }else{
                        if($value1->tipodocumento_id==5){
                            $ticket= Movimiento::find($value1->movimiento_id);
                            if($ticket->plan_id>0)
                                $pdf::Cell(40,7,substr($ticket->plan->nombre,0,23),1,0,'L');
                            else
                                $pdf::Cell(40,7,"",1,0,'L');
                        }else{
                            $pdf::Cell(40,7,substr($empresa,0,23),1,0,'L');
                        }
                    }
                    if($caja_id==4){
                        $pdf::Cell(70,7,"MEDICINA",1,0,'L');    
                    }else{
                        $pdf::Cell(70,7,"SERVICIOS",1,0,'L');    
                    }
                    $pdf::Cell(18,7,'',1,0,'C');
                    $pdf::Cell(18,7,number_format(0,2,'.',''),1,0,'R');
                    $pdf::Cell(20,7,utf8_decode(" - "),1,0,'C');
                    $pdf::Cell(20,7,'-',1,0,'L');
                    //substr($v->persona->nombres,0,1).'. '.$v->persona->apellidopaterno
                    $pdf::Ln();
                    $anuladas=$anuladas + number_format(0,2,'.','');
                    
                }
            }
            $pdf::Ln();
            if (!isset($responsable)) {
                $responsable="CAJA VACIA";
            }
            $pdf::Cell(120,7,('RESPONSABLE: '.$responsable),0,0,'L');
            $pdf::SetFont('helvetica','B',9);
            $pdf::Cell(50,7,utf8_decode("RESUMEN DE CAJA"),1,0,'C');
            $pdf::Ln();
            $pdf::Cell(120,7,utf8_decode(""),0,0,'C');
            $pdf::Cell(30,7,utf8_decode("INGRESOS :"),1,0,'L');
            $pdf::Cell(20,7,number_format($ingreso,2,'.',''),1,0,'R');
            $pdf::Ln();
            $pdf::Cell(120,7,utf8_decode(""),0,0,'C');
            $pdf::Cell(30,7,utf8_decode("Efectivo :"),1,0,'L');
            $pdf::Cell(20,7,number_format($efectivo-$totalvisa-$totalmaster,2,'.',''),1,0,'R');
            $pdf::Ln();
            $pdf::Cell(120,7,utf8_decode(""),0,0,'C');
            $pdf::Cell(30,7,utf8_decode("Master :"),1,0,'L');
            $pdf::Cell(20,7,number_format($master+$totalmaster,2,'.',''),1,0,'R');
            $pdf::Ln();
            $pdf::Cell(120,7,utf8_decode(""),0,0,'C');
            $pdf::Cell(30,7,utf8_decode("Visa :"),1,0,'L');
            $pdf::Cell(20,7,number_format($visa+$totalvisa,2,'.',''),1,0,'R');
            $pdf::Ln();
            $pdf::Cell(120,7,utf8_decode(""),0,0,'C');
            $pdf::Cell(30,7,utf8_decode("EGRESOS :"),1,0,'L');
            $pdf::Cell(20,7,number_format($egreso,2,'.',''),1,0,'R');
            $pdf::Ln();
            $pdf::Cell(120,7,utf8_decode(""),0,0,'C');
            $pdf::Cell(30,7,utf8_decode("SALDO :"),1,0,'L');
            $pdf::Cell(20,7,number_format($ingreso - $egreso - $visa - $master,2,'.',''),1,0,'R');
            $pdf::Ln();
            /*$pdf::Cell(120,7,utf8_decode(""),0,0,'C');
            $pdf::Cell(30,7,utf8_decode("GARANTIA :"),1,0,'L');
            $pdf::Cell(20,7,number_format($garantia,2,'.',''),1,0,'R');*/
            $pdf::Ln();
            $pdf::Output('ListaCaja.pdf');
        }
    }

    public function pdfDetalleCierreFOriginal(Request $request){
        $caja                = Caja::find($request->input('caja_id'));
        $f_inicial           = $request->input('fi');
        $f_final             = $request->input('ff');

        $aperturas = array();
        $cierres = array();

        $caja_id          = Libreria::getParam($request->input('caja_id'),'1');
        
        //sucursal_id
        $sucursal_id = Session::get('sucursal_id');

        $rst        = Movimiento::where('sucursal_id','=',$sucursal_id)->where('tipomovimiento_id','=',2)->where('caja_id','=',$caja_id)->where('conceptopago_id','=',1)->where('movimiento.fecha', '>=', $f_inicial)->where('movimiento.fecha', '<=', $f_final)->orderBy('id','ASC')->get();
        if(count($rst)>0){
            foreach ($rst as $key => $rvalue) {
                array_push($aperturas,$rvalue->id);
                $svalue       = Movimiento::where('sucursal_id','=',$sucursal_id)->where('tipomovimiento_id','=',2)->where('caja_id','=',$caja_id)->where('conceptopago_id','=',2)->where('movimiento.fecha', '>=', $f_inicial)->where('movimiento.fecha', '<=', $f_final)
                ->where('movimiento.id', '>=', $rvalue->id)
                ->orderBy('id','ASC')->first();
                if(!is_null($svalue)){
                    array_push($cierres,$svalue->id);
                }else{
                    array_push($cierres,0);
                }

            }
            
        }else{
            $movimiento_mayor = 0;
        }
        
        $vmax = sizeof($aperturas);
        //dd(array($aperturas,$cierres));
        $pdf = new MTCPDF();
        $pdf::SetTitle('Detalle Cierre General');
        $pdf::setFooterCallback(function($pdf) {
                $pdf->SetY(-15);
                // Set font
                $pdf->SetFont('helvetica', 'I', 8);
                // Page number
                $pdf->Cell(0, 10, 'Pag. '.$pdf->getAliasNumPage().'/'.$pdf->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');

        });

        $caja = Caja::find($request->input('caja_id'));
        if($caja->estado=="A"){
            $vmax = $vmax - 1;
        }
        //dd($aperturas,$cierres);
        //LA ULTIMA APERTURA DE CAJA HA SIDO OBVIADA AL PROGRAMAR EL REPORTE, ALGUN PROBLEMA DESCOMENTAR EL -1
        for ($valor=0; $valor < $vmax; $valor++) {
            //echo $aperturas[$valor].' - '.$cierres[$valor].' ';exit();
            $resultado        = Movimiento::leftjoin('person as paciente', 'paciente.id', '=', 'movimiento.persona_id')
                                ->join('person as responsable', 'responsable.id', '=', 'movimiento.responsable_id')
                                ->join('conceptopago','conceptopago.id','=','movimiento.conceptopago_id')
                                ->leftjoin('movimiento as m2','m2.movimiento_id','=','movimiento.id')
                                ->where('movimiento.caja_id', '=', $caja_id)
                                ->where('movimiento.sucursal_id','=',$sucursal_id)
                                ->where(function ($query) use($aperturas,$cierres,$valor) {
                                    $query->where(function($q) use($aperturas,$cierres,$valor){
                                            $q->where('movimiento.id', '>', $aperturas[$valor])
                                            ->where('movimiento.id', '<', $cierres[$valor])
                                            ->whereNull('movimiento.cajaapertura_id');
                                    })
                                          ->orwhere(function ($query1) use($aperturas,$cierres,$valor){
                                            $query1->where('movimiento.cajaapertura_id','=',$aperturas[$valor]);
                                            });//normal
                                })
                                //->where('movimiento.voucher', '04-23390')
                                ->where(function($query){
                                    $query
                                        ->whereNotIn('movimiento.conceptopago_id',[31])
                                        ->orWhere('m2.situacion','<>','R');
                                })
                                ->where('movimiento.situacion', '<>', 'A')->where('movimiento.situacion', '<>', 'R');
            $resultado        = $resultado->select('movimiento.*','m2.situacion as situacion2','responsable.nombres as responsable2')->orderBy('conceptopago.tipo', 'asc')->orderBy('conceptopago.orden', 'asc')->orderBy('conceptopago.id', 'asc')->orderBy('movimiento.tipotarjeta', 'asc')->orderBy('movimiento.numero', 'asc');
            $listConcepto     = array();
            $listConcepto2     = array();
            $listConcepto3     = array();
            $listConcepto4     = array();
            $listtarjeta      = array();
            $listConcepto[]   = 6;//TRANSF CAJA INGRESO
            $listConcepto[]   = 7;//TRANSF CAJA EGRESO
            $listConcepto2[]   = 8;//HONORARIOS MEDICOS
            $listConcepto[]   = 14;//TRANSF TARJETA EGRESO
            $listConcepto[]   = 15;//TRANSF TARJETA INGRESO
            $listConcepto[]   = 16;//TRANSF SOCIO EGRESO
            $listConcepto[]   = 17;//TRANSF SOCIO INGRESO
            $listConcepto3[]   = 24;//PAGO DE CONVENIO
            $listConcepto3[]   = 25;//PAGO DE SOCIO
            $listConcepto[]   = 20;//TRANSF BOLETEO EGRESO
            $listConcepto[]   = 21;//TRANSF BOLETEO INGRESO
            //$listConcepto[]   = 30;//DEVOLUCION GARANT�A CONTROL REMOTO
            $listConcepto4[]   = 31;//TRANSF FARMACIA EGRESO
            $listConcepto4[]   = 32;//TRANSF FARMACiA INGRESO
            $lista            = $resultado->get();
            //if($valor==0){dd($aperturas[$valor],$cierres[$valor],$lista);}
            //dd($lista);
            //if($aperturas[$valor]==290049){dd($lista);}

            if ($caja_id == 3 || $caja_id == 4) {
                $resultado2        = Movimiento::leftjoin('person as paciente', 'paciente.id', '=', 'movimiento.persona_id')
                                ->leftjoin('person as responsable', 'responsable.id', '=', 'movimiento.responsable_id')
                                ->leftjoin('conceptopago','conceptopago.id','=','movimiento.conceptopago_id')
                                ->leftjoin('movimiento as m2','movimiento.id','=','m2.movimiento_id')
                                //->where('movimiento.serie', '=', $caja_id)
                                ->where('movimiento.estadopago', '=', 'PP')
                                ->where('movimiento.sucursal_id','=',$sucursal_id)
                                ->where('movimiento.tipomovimiento_id', '=', '4')
                                ->where(function ($query) use($aperturas,$cierres,$valor) {
                                    $query->where(function($q) use($aperturas,$cierres,$valor){
                                            $q->where('movimiento.id', '>', $aperturas[$valor])
                                            ->where('movimiento.id', '<', $cierres[$valor])
                                            ->whereNull('movimiento.cajaapertura_id');
                                    })
                                          ->orwhere(function ($query1) use($aperturas,$cierres,$valor){
                                            $query1->where('movimiento.cajaapertura_id','=',$aperturas[$valor]);
                                            });//normal
                                })
                                ->whereNull('movimiento.tipo')
                                ->where('movimiento.situacion', '<>', 'U')
                                ->where('movimiento.situacion', '<>', 'R');
                $resultado2        = $resultado2->select('movimiento.*','m2.situacion as situacion2',DB::raw('CONCAT(paciente.apellidopaterno," ",paciente.apellidomaterno," ",paciente.nombres) as paciente'),DB::raw('responsable.nombres as responsable'))->orderBy('movimiento.id', 'desc');
                $listapendiente            = $resultado2->get();
            }
            if (isset($lista)) {
                $pdf::AddPage('L');
                $pdf::SetFont('helvetica','B',12);
                $pdf::Image('dist/img/logo.jpg',10,8,50,0);
                $sucursal_id = Session::get('sucursal_id'); 
                $nomcierre = '';
                $nomcierre = 'Clínica Especialidades'; 
                if($sucursal_id == 1) {
                    $nomcierre = 'BM Clínica de Ojos';
                }  
                if($caja->nombre == 'FARMACIA') {
                    $nomcierre = ' Farmacia - ' . $nomcierre;
                } 
                $pdf::Cell(0,15,"Detalle de Cierre de ".$nomcierre." Desde ".$f_inicial. " hasta ".$f_final,0,0,'C');
                $pdf::Ln();
                $pdf::SetFont('helvetica','B',8.5);
                $pdf::Cell(15,7,utf8_decode("FECHA"),1,0,'C');
                $pdf::Cell(60,7,utf8_decode("PERSONA"),1,0,'C');
                $pdf::Cell(20,7,utf8_decode("NRO"),1,0,'C');
                $pdf::Cell(40,7,utf8_decode("EMPRESA"),1,0,'C');
                $pdf::Cell(70,7,utf8_decode("CONCEPTO"),1,0,'C');
                $pdf::Cell(18,7,utf8_decode("EGRESO"),1,0,'C');
                $pdf::Cell(18,7,utf8_decode("INGRESO"),1,0,'C');
                $pdf::Cell(20,7,utf8_decode("TARJETA"),1,0,'C');
                $pdf::Cell(20,7,utf8_decode("DOCTOR"),1,0,'C');
                $pdf::Ln();
                if($caja_id==1){//ADMISION 1
                    $serie=3;
                }elseif($caja_id==2){//ADMISION 2
                    $serie=7;
                }elseif($caja_id==3){//CONVENIOS
                    $serie=8;
                }elseif($caja_id==5){//EMERGENCIA
                    $serie=9;
                }elseif($caja_id==4){//FARMACIA
                    $serie=4;
                }elseif($caja_id==8){//PROCEDIMIENTOS
                    $serie=5;
                }else{
                    $serie='';
                }
                $resultado1       = Movimiento::join('person as responsable', 'responsable.id', '=', 'movimiento.responsable_id')
                                ->leftjoin('movimiento as m2','movimiento.movimiento_id','=','m2.id')
                                ->leftjoin('person as paciente', 'paciente.id', '=', 'm2.persona_id')
                                ->where('movimiento.serie', '=', $serie)
                                ->where('movimiento.tipomovimiento_id', '=', 4)
                                ->where('movimiento.sucursal_id','=',$sucursal_id)
                                ->where('movimiento.id', '>', $aperturas[$valor])
                                ->where('movimiento.id', '<', $cierres[$valor])
                                ->where('m2.situacion','like','B');
                                /*->whereNotIn('movimiento.id',function ($query) use ($aperturas,$valor,$cierres,$caja_id) {
                                    $query->select('movimiento_id')->from('movimiento')->where('id','>',$aperturas[$valor])->where('id','<',$cierres[$valor])->where('caja_id','=',$caja_id);
                                });*/
                $resultado1       = $resultado1->select('movimiento.*','m2.situacion as situacion2',DB::raw('concat(paciente.apellidopaterno,\' \',paciente.apellidomaterno,\' \',paciente.nombres) as paciente2'))->orderBy('movimiento.numero', 'asc');
                
                $lista1           = $resultado1->get();
                //ECHO $aperturas[$valor]."-".$cierres[$valor]."-";
            if ($caja_id == 3 || $caja_id == 4) {
                $pendiente = 0;
                foreach ($listapendiente as $key => $value) {
                    if($pendiente==0 && $value->tipodocumento_id != 15){
                        $pdf::SetFont('helvetica','B',8.5);
                        $pdf::Cell(281,7,utf8_decode("PENDIENTE"),1,0,'L');
                        $pdf::Ln();
                    }
                    if ($value->tipodocumento_id != 15) {
                        $pdf::SetFont('helvetica','',7);
                        $pdf::Cell(15,7,date("d/m/Y",strtotime($value->fecha)),1,0,'C');

                        $nombrepaciente = '';
                        $nombreempresa = '-';
                        if ($value->persona_id !== NULL) {
                            //echo 'entro'.$value->id;break;
                            $nombrepaciente =$value->persona->apellidopaterno.' '.$value->persona->apellidomaterno.' '.$value->persona->nombres;

                        }else{
                            $nombrepaciente = trim($value->nombrepaciente);
                        }
                        if ($value->tipodocumento_id == 5) {
                            
                            
                        }else{
                            if ($value->empresa != null) {
                                $nombreempresa = trim($value->empresa->bussinesname);
                            }
                            
                        }
                        if(strlen($nombrepaciente)>30){
                            $x=$pdf::GetX();
                            $y=$pdf::GetY();                    
                            $pdf::Multicell(60,3,($nombrepaciente),0,'L');
                            $pdf::SetXY($x,$y);
                            $pdf::Cell(60,7,"",1,0,'C');
                        }else{
                            $pdf::Cell(60,7,($nombrepaciente),1,0,'L');    
                        }
                        //$venta= Movimiento::find($value->id);
                        $pdf::Cell(8,7,($value->tipodocumento_id==5?'B':'F'),1,0,'C');
                        $pdf::Cell(12,7,$value->serie.'-'.$value->numero,1,0,'C');

                        $pdf::Cell(40,7,substr($nombreempresa,0,23),1,0,'L');
                        if($value->servicio_id>0){
                            if(strlen($value->servicio->nombre)>35){
                                $x=$pdf::GetX();
                                $y=$pdf::GetY();                    
                                $pdf::Multicell(70,3,$value->servicio->nombre,0,'L');
                                $pdf::SetXY($x,$y);
                                $pdf::Cell(70,7,"",1,0,'C');
                            }else{
                                $pdf::Cell(70,7,$value->servicio->nombre,1,0,'L');    
                            }
                        }else{
                            $pdf::Cell(70,7,'MEDICINA',1,0,'L');    
                        }
                        $pdf::Cell(18,7,'',1,0,'C');
                        $pdf::Cell(18,7,number_format($value->total,2,'.',''),1,0,'R');
                        $pdf::Cell(20,7,utf8_decode(" - "),1,0,'C');
                        if ($value->doctor_id != null) {
                            $pdf::Cell(20,7,substr($value->doctor->nombres,0,1).'. '.$value->doctor->apellidopaterno,1,0,'L');

                        }else{
                           $pdf::Cell(20,7," - ",1,0,'L'); 
                        }
                        
                        $pdf::Ln();
                        //$pendiente=$pendiente + number_format($value->total,2,'.','');
                        $pendiente=$pendiente + round($value->total,2);
                    }
                    
                }
                $pdf::SetFont('helvetica','B',8.5);
                $pdf::Cell(223,7,'TOTAL',1,0,'R');
                $pdf::Cell(18,7,number_format($pendiente,2,'.',''),1,0,'R');
                $pdf::Ln();
            }
            

            if(count($lista1)>0){
                $pendiente=0;
                foreach($lista1 as $key1 => $value1){
                    /*$rs = Detallemovcaja::where("movimiento_id",'=',$value1->movimiento_id)->get();
                    foreach ($rs as $k => $v){*/
                        if($pendiente==0){
                            $pdf::SetFont('helvetica','B',8.5);
                            $pdf::Cell(281,7,utf8_decode("PENDIENTE"),1,0,'L');
                            $pdf::Ln();
                        }
                        $pdf::SetFont('helvetica','',7.5);
                        $pdf::Cell(15,7,date("d/m/Y",strtotime($value1->fecha)),1,0,'C');
                        if($value1->tipodocumento_id==5){//BOLETA
                            $nombre=$value1->persona->apellidopaterno.' '.$value1->persona->apellidomaterno.' '.$value1->persona->nombres;
                        }else{
                            $nombre=$value1->paciente2;
                            $empresa=$value1->persona->bussinesname;
                        }
                        if(strlen($nombre)>30){
                            $x=$pdf::GetX();
                            $y=$pdf::GetY();                    
                            $pdf::Multicell(60,3,($nombre),0,'L');
                            $pdf::SetXY($x,$y);
                            $pdf::Cell(60,7,"",1,0,'C');
                        }else{
                            $pdf::Cell(60,7,($nombre),1,0,'L');    
                        }
                        $pdf::Cell(8,7,($value1->tipodocumento_id==5?'B':'F'),1,0,'C');
                        $pdf::Cell(12,7,$value1->serie.'-'.$value1->numero,1,0,'C');
                        if($value1->tipodocumento_id==5){
                            $ticket= Movimiento::find($value1->movimiento_id);
                            $pdf::Cell(40,7,substr($ticket->plan->nombre,0,23),1,0,'L');
                        }else{
                            $pdf::Cell(40,7,substr($empresa,0,23),1,0,'L');
                        }
                        $v = Detallemovcaja::where("movimiento_id",'=',$value1->movimiento_id)->first();

                        if (isset($v->servicio_id)) {
                            if($v->servicio_id>0){
                                if(strlen($v->servicio->nombre)>35){
                                    $x=$pdf::GetX();
                                    $y=$pdf::GetY();                    
                                    $pdf::Multicell(70,3,$v->servicio->nombre,0,'L');
                                    $pdf::SetXY($x,$y);
                                    $pdf::Cell(70,7,"",1,0,'C');
                                }else{
                                    $pdf::Cell(70,7,$v->servicio->nombre,1,0,'L');    
                                }
                            }else{
                                $pdf::Cell(70,7,$v->descripcion,1,0,'L');    
                            }
                        } else {
                            $pdf::Cell(70,7,"",1,0,'L');
                        }  
                        $pdf::Cell(18,7,'',1,0,'C');
                        //$pdf::Cell(18,7,number_format($v->cantidad*$v->pagohospital,2,'.',''),1,0,'R');
                        $pdf::Cell(18,7,number_format($value1->total,2,'.',''),1,0,'R');
                        $pdf::Cell(20,7,utf8_decode(" - "),1,0,'C');
                        
                        if (isset($v->persona->nombres)) {
                            $pdf::Cell(20,7,substr($v->persona->nombres,0,1).'. '.$v->persona->apellidopaterno,1,0,'L');
                        }
                        $pdf::Ln();
                        //$pendiente=$pendiente + number_format($v->cantidad*$v->pagohospital,2,'.','');
                        //$pendiente=$pendiente + number_format($value1->total,2,'.','');
                        $pendiente=$pendiente + number_format($value1->total,2);
                    //}
                }
                $pdf::SetFont('helvetica','B',8.5);
                $pdf::Cell(223,7,'TOTAL',1,0,'R');
                $pdf::Cell(18,7,number_format($pendiente,2,'.',''),1,0,'R');
                $pdf::Ln();
            }
            //dd($lista);
            //if($aperturas[$valor]==301797){dd($lista);}
            $ingreso=0;$egreso=0;$transferenciai=0;$transferenciae=0;$garantia=0;$efectivo=0;$visa=0;$master=0;$pago=0;$tarjeta=0;$cobranza=0;$egreso1=0;$transferenciai=0;$cobranza=0;$ingresotarjeta=0;
            $bandpago=true;$bandegreso=true;$bandtransferenciae=true;$bandtarjeta=true;$bandtransferenciai=true;$bandcobranza=true;$bandingresotarjeta=true;
            foreach ($lista as $key => $value){//print_r($value);
                //if(in_array($value->id, array(293223,293261))){dd($value);}
                //dd($value);
                if($ingreso==0){
                    $responsable=$value->responsable2;
                }
                if (!isset($responsable)) {
                    $responsable="CAJA VACIA";
                }
                if($value->conceptopago_id==3 && $value->tipotarjeta==''){
                    
                    if ($caja_id == 3 || $caja_id == 4) {
                        //echo $value->movimiento_id."<br />";
                        $rs = Detallemovcaja::where("movimiento_id",'=',DB::raw('(select movimiento_id from movimiento where id='.$value->movimiento_id.')'))->get();
                        //echo $value->movimiento_id."|".$value->id."@";
                        foreach ($rs as $k => $v) {
                            $pdf::SetTextColor(0,0,0);
                            if($egreso1>0 && $bandegreso){
                                $pdf::SetFont('helvetica','B',8.5);
                                $pdf::Cell(205,7,'TOTAL',1,0,'R');
                                $pdf::Cell(18,7,number_format($egreso1,2,'.',''),1,0,'R');
                                $bandegreso=false;
                                $pdf::Ln(); 
                            }
                            if($pago==0 && $value->tipodocumento_id!=15){
                                $pdf::SetFont('helvetica','B',8.5);
                                $pdf::Cell(281,7,utf8_decode("INGRESOS"),1,0,'L');
                                $pdf::Ln();
                            }
                            if ($value->tipodocumento_id !== 15) {
                                $pdf::SetFont('helvetica','',7);
                                $pdf::Cell(15,7,date("d/m/Y",strtotime($value->fecha)),1,0,'C');

                                $nombrepaciente = '';
                                $nombreempresa = '-';
                                if ($value->persona_id !== NULL) {
                                    //echo 'entro'.$value->id;break;
                                    $nombrepaciente =$value->persona->apellidopaterno.' '.$value->persona->apellidomaterno.' '.$value->persona->nombres;

                                }else{
                                    $nombrepaciente = trim($value->nombrepaciente);
                                }
                                if ($value->tipodocumento_id == 5) {
                                    
                                    
                                }else{
                                    if ($value->empresa_id != null) {
                                        $nombreempresa = trim($value->empresa->bussinesname);
                                    }
                                    
                                }
                                
                                if(strlen($nombrepaciente)>30){
                                    $x=$pdf::GetX();
                                    $y=$pdf::GetY();                    
                                    $pdf::Multicell(60,3,($nombrepaciente),0,'L');
                                    $pdf::SetXY($x,$y);
                                    $pdf::Cell(60,7,"",1,0,'C');
                                }else{
                                    $pdf::Cell(60,7,($nombrepaciente),1,0,'L');    
                                }
                                $venta= Movimiento::find($value->movimiento_id);
                                $pdf::Cell(8,7,($venta->tipodocumento_id==5?'B':'F'),1,0,'C');
                                $pdf::Cell(12,7,$venta->serie.'-'.$venta->numero,1,0,'C');
                                if ($venta->conveniofarmacia_id != null) {
                                    $nombreempresa = $venta->conveniofarmacia->nombre;
                                }
                                $pdf::Cell(40,7,substr($nombreempresa,0,23),1,0,'L');
                                if($v->servicio_id>0){
                                    if(strlen($v->servicio->nombre)>35){
                                        $x=$pdf::GetX();
                                        $y=$pdf::GetY();                    
                                        $pdf::Multicell(70,3,$v->servicio->nombre,0,'L');
                                        $pdf::SetXY($x,$y);
                                        $pdf::Cell(70,7,"",1,0,'C');
                                    }else{
                                        $pdf::Cell(70,7,$v->servicio->nombre,1,0,'L');    
                                    }
                                }else{
                                    $pdf::Cell(70,7,$v->descripcion.'- MEDICINA',1,0,'L');    
                                }
                                $pdf::Cell(18,7,'',1,0,'C');
                                $pdf::Cell(18,7,number_format($v->movimiento->total,2,'.',''),1,0,'R');
                                $pdf::Cell(20,7,utf8_decode('-'),1,0,'C');
                                if ($venta->doctor_id != null) {
                                    $pdf::Cell(20,7,substr($venta->doctor->nombres,0,1).'. '.$venta->doctor->apellidopaterno,1,0,'L');

                                }else{
                                   $pdf::Cell(20,7," - ",1,0,'L'); 
                                }
                                
                                $pdf::Ln();
                                //$pago=$pago + number_format($v->movimiento->total,2,'.','');
                                $pago=$pago + round($v->movimiento->total,2);
                            }
                            
                        }
                    }else{
                        //PARA PAGO DE CLIENTE, BUSCO TICKET
                        /*$rs = Detallemovcaja::where("movimiento_id",'=',DB::raw('(select movimiento_id from movimiento where id='.$value->movimiento_id.')'))->get();
                        foreach ($rs as $k => $v){*/

                            $pdf::SetTextColor(0,0,0);
                            if($transferenciae>0 && $bandtransferenciae){
                                $pdf::SetFont('helvetica','B',8.5);
                                $pdf::Cell(205,7,'TOTAL',1,0,'R');
                                $pdf::Cell(18,7,number_format($transferenciae,2,'.',''),1,0,'R');
                                $bandtransferenciae=false;
                                $pdf::Ln(); 
                            }
                            if($egreso1>0 && $bandegreso){
                                $pdf::SetFont('helvetica','B',8.5);
                                $pdf::Cell(205,7,'TOTAL',1,0,'R');
                                $pdf::Cell(18,7,number_format($egreso1,2,'.',''),1,0,'R');
                                $bandegreso=false;
                                $pdf::Ln(); 
                            }
                            if($pago==0){
                                $pdf::SetFont('helvetica','B',8.5);
                                $pdf::Cell(281,7,utf8_decode("INGRESOS"),1,0,'L');
                                $pdf::Ln();
                                if($caja_id==3){
                                    $pdf::SetFont('helvetica','B',7);
                                    $pdf::Cell(223,7,utf8_decode("SALDO INICIAL"),1,0,'L');
                                    $apert = Movimiento::find($aperturas[$valor]);
                                    $pdf::Cell(18,7,number_format($apert->total,2,'.',''),1,0,'R');
                                    $pago = $pago + round($apert->total,2);
                                    $ingreso = $ingreso + round($apert->total,2);
                                    $pdf::Ln();
                                }
                            }
                            $pdf::SetFont('helvetica','',7);
                            $venta= Movimiento::find($value->movimiento_id);
                            //if($aperturas[$valor]==290049){dd($venta);}
                            $pdf::Cell(15,7,date("d/m/Y",strtotime($venta->fecha)),1,0,'C');
                            if(strlen($value->persona->apellidopaterno.' '.$value->persona->apellidomaterno.' '.$value->persona->nombres)>30){
                                $x=$pdf::GetX();
                                $y=$pdf::GetY();                    
                                $pdf::Multicell(60,3,($value->persona->apellidopaterno.' '.$value->persona->apellidomaterno.' '.$value->persona->nombres),0,'L');
                                $pdf::SetXY($x,$y);
                                $pdf::Cell(60,7,"",1,0,'C');
                            }else{
                                $pdf::Cell(60,7,($value->persona->apellidopaterno.' '.$value->persona->apellidomaterno.' '.$value->persona->nombres),1,0,'L');    
                            }
                            $v = Detallemovcaja::where("movimiento_id",'=',DB::raw('(select movimiento_id from movimiento where id='.$value->movimiento_id.')'))->first();
                            $ticket= Movimiento::find($v->movimiento_id);
                            $pdf::Cell(8,7,($venta->tipodocumento_id==5?'B':'F'),1,0,'C');
                            $pdf::Cell(12,7,$venta->serie.'-'.$venta->numero,1,0,'C');
                            $pdf::Cell(40,7,substr($ticket->plan->nombre,0,23),1,0,'L');
                            if($v->servicio_id>0){
                                if (isset($v->servicio)) {
                                    if(strlen($v->servicio->nombre)>35){
                                        $x=$pdf::GetX();
                                        $y=$pdf::GetY();                    
                                        $pdf::Multicell(70,3,$v->servicio->nombre,0,'L');
                                        $pdf::SetXY($x,$y);
                                        $pdf::Cell(70,7,"",1,0,'C');
                                    }else{
                                        $pdf::Cell(70,7,$v->servicio->nombre,1,0,'L');    
                                    }
                                } else {
                                    $pdf::Cell(70,7,"",1,0,'L');
                                }
                                
                            }else{
                                $pdf::Cell(70,7,$v->descripcion,1,0,'L');    
                            }
                            $pdf::Cell(18,7,'',1,0,'C');
                            //$pdf::Cell(18,7,number_format($v->cantidad*$v->pagohospital,2,'.',''),1,0,'R');
                            $pdf::Cell(18,7,number_format($venta->total,2,'.',''),1,0,'R');
                            $pdf::Cell(20,7,utf8_decode('-'),1,0,'C');
                            $pdf::Cell(20,7,substr($v->persona->nombres,0,1).'. '.$v->persona->apellidopaterno,1,0,'L');
                            $pdf::Ln();
                            //$pago=$pago + number_format($v->cantidad*$v->pagohospital,2,'.','');
                            //$pago=$pago + number_format($venta->total,2,'.','');
                            $pago=$pago + round($venta->total,2);
                        //}
                    }
                }elseif($value->conceptopago_id==3 && $value->tipotarjeta!=''){//PARA PAGO DE CLIENTE, BUSCO TICKET CON TARJETA
                    if ($caja_id == 3 || $caja_id == 4) {
                        $rs = Detallemovcaja::where("movimiento_id",'=',DB::raw('(select movimiento_id from movimiento where id='.$value->movimiento_id.')'))->get();
                        foreach ($rs as $k => $v){
                            $pdf::SetTextColor(0,0,0);
                            if($pago>0 && $bandpago){
                                $pdf::SetFont('helvetica','B',8.5);
                                $pdf::Cell(223,7,'TOTAL',1,0,'R');
                                $pdf::Cell(18,7,number_format($pago,2,'.',''),1,0,'R');
                                $bandpago=false;
                                $pdf::Ln(); 
                            }

                            if($tarjeta==0){
                                $pdf::SetFont('helvetica','B',8.5);
                                $pdf::Cell(281,7,utf8_decode("TARJETA"),1,0,'L');
                                $pdf::Ln();
                            }
                            $pdf::SetFont('helvetica','',7);
                            $pdf::Cell(15,7,date("d/m/Y",strtotime($value->fecha)),1,0,'C');
                            
                            $nombrepaciente = '';
                            $nombreempresa = '-';
                            if ($value->persona_id !== NULL) {
                                //echo 'entro'.$value->id;break;
                                $nombrepaciente =$value->persona->apellidopaterno.' '.$value->persona->apellidomaterno.' '.$value->persona->nombres;

                            }else{
                                $nombrepaciente = trim($value->nombrepaciente);
                            }
                            if ($value->tipodocumento_id == 5) {
                                
                                
                            }else{
                                $nombreempresa = trim($value->empresa->bussinesname);
                            }
                            if(strlen($nombrepaciente)>30){
                                $x=$pdf::GetX();
                                $y=$pdf::GetY();                    
                                $pdf::Multicell(60,3,($nombrepaciente),0,'L');
                                $pdf::SetXY($x,$y);
                                $pdf::Cell(60,7,"",1,0,'C');
                            }else{
                                $pdf::Cell(60,7,($nombrepaciente),1,0,'L');    
                            }
                            $venta= Movimiento::find($value->movimiento_id);
                            $ticket= Movimiento::find($v->movimiento_id);
                            $pdf::Cell(8,7,($venta->tipodocumento_id==5?'B':'F'),1,0,'C');
                            $pdf::Cell(12,7,$venta->serie.'-'.$venta->numero,1,0,'C');
                            if ($venta->conveniofarmacia_id != null) {
                                $nombreempresa = $venta->conveniofarmacia->nombre;
                            }
                            $pdf::Cell(40,7,substr($nombreempresa,0,23),1,0,'L');
                            if($v->servicio_id>0){
                                if(strlen($v->servicio->nombre)>35){
                                    $x=$pdf::GetX();
                                    $y=$pdf::GetY();                    
                                    $pdf::Multicell(70,3,$v->servicio->nombre,0,'L');
                                    $pdf::SetXY($x,$y);
                                    $pdf::Cell(70,7,"",1,0,'C');
                                }else{
                                    $pdf::Cell(70,7,$v->servicio->nombre,1,0,'L');    
                                }
                            }else{
                                $pdf::Cell(70,7,$v->descripcion,1,0,'L');    
                            }
                            $pdf::Cell(18,7,'',1,0,'C');
                            $tarjeta=$tarjeta + round($v->movimiento->total,2);
                            $pdf::Cell(18,7,number_format($v->movimiento->total,2,'.',''),1,0,'R');
                            $x=$pdf::GetX();
                            $y=$pdf::GetY();                    
                            $pdf::Multicell(20,3,$value->tipotarjeta." ".$value->tarjeta." / ".$value->voucher,0,'L');
                            $pdf::SetXY($x,$y);
                            $pdf::Cell(20,7,"",1,0,'C');
                            if (!is_null($venta->doctor) && $venta->doctor_id != null) {
                                $pdf::Cell(20,7,substr($venta->doctor->nombres,0,1).'. '.$venta->doctor->apellidopaterno,1,0,'L');

                            }else{
                               $pdf::Cell(20,7," - ",1,0,'L'); 
                            }
                            $pdf::Ln();
                            //$pago=$pago + number_format($v->movimiento->total,2,'.','');
                            
                        }
                    }else{
                        /*$rs = Detallemovcaja::where("movimiento_id",'=',DB::raw('(select movimiento_id from movimiento where id='.$value->movimiento_id.')'))->get();
                        foreach ($rs as $k => $v){*/
                            $pdf::SetTextColor(0,0,0);
                            if($pago>0 && $bandpago){
                                $pdf::SetFont('helvetica','B',8.5);
                                $pdf::Cell(223,7,'TOTAL',1,0,'R');
                                $pdf::Cell(18,7,number_format($pago,2,'.',''),1,0,'R');
                                $bandpago=false;
                                $pdf::Ln(); 
                            }
                            if($tarjeta==0){
                                $pdf::SetFont('helvetica','B',8.5);
                                $pdf::Cell(281,7,utf8_decode("TARJETA"),1,0,'L');
                                $pdf::Ln();
                            }
                            if($value->situacion<>'A'){
                                $pdf::SetTextColor(0,0,0);
                            }else{
                                $pdf::SetTextColor(255,0,0);
                            }
                            $pdf::SetFont('helvetica','',7);
                            $pdf::Cell(15,7,date("d/m/Y",strtotime($value->fecha)),1,0,'C');
                            $venta= Movimiento::find($value->movimiento_id);
                            //$tarjeta=$tarjeta + number_format($value->total,2,'.','');
                            $tarjeta=$tarjeta + round($value->total,2);
                            if(strlen($value->persona->apellidopaterno.' '.$value->persona->apellidomaterno.' '.$value->persona->nombres)>30){
                                $x=$pdf::GetX();
                                $y=$pdf::GetY();                    
                                $pdf::Multicell(60,3,($value->persona->apellidopaterno.' '.$value->persona->apellidomaterno.' '.$value->persona->nombres),0,'L');
                                $pdf::SetXY($x,$y);
                                $pdf::Cell(60,7,"",1,0,'C');
                            }else{
                                $pdf::Cell(60,7,($value->persona->apellidopaterno.' '.$value->persona->apellidomaterno.' '.$value->persona->nombres),1,0,'L');    
                            }
                            $v = Detallemovcaja::where("movimiento_id",'=',DB::raw('(select movimiento_id from movimiento where id='.$value->movimiento_id.')'))->first();
                            $ticket= Movimiento::find($v->movimiento_id);
                            if(!is_null($venta)){
                                $pdf::Cell(8,7,($venta->tipodocumento_id==5?'B':'F'),1,0,'C');
                                $pdf::Cell(12,7,$venta->serie.'-'.$venta->numero,1,0,'C');
                            }
                            $pdf::Cell(40,7,substr($ticket->plan->nombre,0,23),1,0,'L');
                            
                            if($v->servicio_id>0){
                                if (isset($v->servicio)) {
                                    if(strlen($v->servicio->nombre)>35){
                                        $x=$pdf::GetX();
                                        $y=$pdf::GetY();                    
                                        $pdf::Multicell(70,3,$v->servicio->nombre,0,'L');
                                        $pdf::SetXY($x,$y);
                                        $pdf::Cell(70,7,"",1,0,'C');
                                    }else{
                                        $pdf::Cell(70,7,$v->servicio->nombre,1,0,'L');    
                                    }
                                } else {
                                    $pdf::Cell(70,7,$v->descripcion,1,0,'L');    
                                }
                            }else{
                                $pdf::Cell(70,7,$v->descripcion,1,0,'L');    
                            }
                            $pdf::Cell(18,7,'',1,0,'C');
                            //$pdf::Cell(18,7,number_format($v->cantidad*$v->pagohospital,2,'.',''),1,0,'R');
                            $pdf::Cell(18,7,number_format($venta->total,2,'.',''),1,0,'R');
                            $x=$pdf::GetX();
                            $y=$pdf::GetY();                    
                            $pdf::Multicell(20,3,$value->tipotarjeta." ".$value->tarjeta." / ".$value->voucher,0,'L');
                            $pdf::SetXY($x,$y);
                            $pdf::Cell(20,7,"",1,0,'C');
                            $pdf::Cell(20,7,substr($v->persona->nombres,0,1).'. '.$v->persona->apellidopaterno,1,0,'L');
                            $pdf::Ln();   
                            if($value->total!=$venta->total){
                                $pdf::SetFont('helvetica','',7);
                                $pdf::Cell(15,7,date("d/m/Y",strtotime($value->fecha)),1,0,'C');
                                if(strlen($value->persona->apellidopaterno.' '.$value->persona->apellidomaterno.' '.$value->persona->nombres)>30){
                                    $x=$pdf::GetX();
                                    $y=$pdf::GetY();                    
                                    $pdf::Multicell(60,3,($value->persona->apellidopaterno.' '.$value->persona->apellidomaterno.' '.$value->persona->nombres),0,'L');
                                    $pdf::SetXY($x,$y);
                                    $pdf::Cell(60,7,"",1,0,'C');
                                }else{
                                    $pdf::Cell(60,7,($value->persona->apellidopaterno.' '.$value->persona->apellidomaterno.' '.$value->persona->nombres),1,0,'L');    
                                }
                                if(!is_null($venta)){
                                    $pdf::Cell(8,7,'R',1,0,'C');
                                    $pdf::Cell(12,7,$venta->serie.'-'.$venta->numero,1,0,'C');
                                }
                                $pdf::Cell(40,7,substr($ticket->plan->nombre,0,23),1,0,'L');
                                $pdf::Cell(70,7,'INGRESO POR DIFERENCIA TARJETA',1,0,'L');    
                                $pdf::Cell(18,7,'',1,0,'C');
                                $pdf::Cell(18,7,number_format($value->total-$venta->total,2,'.',''),1,0,'R');
                                $x=$pdf::GetX();
                                $y=$pdf::GetY();                    
                                $pdf::Multicell(20,3,$value->tipotarjeta." ".$value->tarjeta." / ".$value->voucher,0,'L');
                                $pdf::SetXY($x,$y);
                                $pdf::Cell(20,7,"",1,0,'C');
                                $pdf::Cell(20,7,substr($v->persona->nombres,0,1).'. '.$v->persona->apellidopaterno,1,0,'L');
                                $pdf::Ln();   
                            }
                        //}

                    }
                    
                }elseif(in_array($value->conceptopago_id, $listConcepto) && $value->conceptopago->tipo=='E'){//CONCEPTOS QUE TIENEN LISTA EGRESOS
                    if($value->situacion2<>'R'){
                        $pdf::SetTextColor(0,0,0);
                        if($egreso1>0 && $bandegreso){
                            $pdf::SetFont('helvetica','B',8.5);
                            $pdf::Cell(205,7,'TOTAL',1,0,'R');
                            $pdf::Cell(18,7,number_format($egreso1,2,'.',''),1,0,'R');
                            $bandegreso=false;
                            $pdf::Ln(); 
                        }
                        if($transferenciae==0){
                            $pdf::SetFont('helvetica','B',8.5);
                            $pdf::Cell(281,7,utf8_decode("TRANSFERENCIA"),1,0,'L');
                            $pdf::Ln();
                        }
                        if($value->situacion<>'A'){
                            $pdf::SetTextColor(0,0,0);
                        }else{
                            $pdf::SetTextColor(255,0,0);
                        }
                        $list=explode(",",$value->listapago);
                        $transferenciae = $transferenciae + round($value->total,2);
                        for($c=0;$c<count($list);$c++){
                            $pdf::SetFont('helvetica','',7);
                            $pdf::Cell(15,7,date("d/m/Y",strtotime($value->fecha)),1,0,'C');
                            $detalle = Detallemovcaja::find($list[$c]);
                            $ticket = Movimiento::where("id","=",$detalle->movimiento_id)->first();
                            $venta = Movimiento::where("movimiento_id","=",$detalle->movimiento_id)->first();
                            if(strlen($ticket->persona->apellidopaterno.' '.$ticket->persona->apellidomaterno.' '.$ticket->persona->nombres)>30){
                                $x=$pdf::GetX();
                                $y=$pdf::GetY();                    
                                $pdf::Multicell(60,3,($ticket->persona->apellidopaterno.' '.$ticket->persona->apellidomaterno.' '.$ticket->persona->nombres),0,'L');
                                $pdf::SetXY($x,$y);
                                $pdf::Cell(60,7,"",1,0,'C');
                            }else{
                                $pdf::Cell(60,7,($ticket->persona->apellidopaterno.' '.$ticket->persona->apellidomaterno.' '.$ticket->persona->nombres),1,0,'L');    
                            }
                            $pdf::Cell(8,7,($venta->tipodocumento_id==5?'B':'F'),1,0,'C');
                            $pdf::Cell(12,7,$venta->serie.'-'.$venta->numero,1,0,'C');
                            
                            if($venta->tipodocumento_id==4){
                                $pdf::Cell(40,7,$venta->persona->bussinesname,1,0,'L');
                            }else{
                                $pdf::Cell(40,7,"",1,0,'L');
                            }
                            if($value->conceptopago_id==8){//HONORARIOS MEDICOS
                                $descripcion=$value->conceptopago->nombre.' - RH: '.$detalle->recibo;
                            }else{
                                $descripcion=$value->conceptopago->nombre;
                            }
                            if(strlen($descripcion)>40){
                                $x=$pdf::GetX();
                                $y=$pdf::GetY();
                                $pdf::Multicell(70,3,utf8_decode($descripcion),0,'L');
                                $pdf::SetXY($x,$y);
                                $pdf::Cell(70,7,"",1,0,'L');
                            }else{
                                $pdf::Cell(70,7,utf8_decode($descripcion),1,0,'L');
                            }
                            if($value->conceptopago_id==8){//HONORARIOS MEDICOS
                                $pdf::Cell(18,7,number_format($detalle->pagodoctor,2,'.',''),1,0,'R');
                                $pdf::Cell(18,7,utf8_decode(""),1,0,'C');
                                $pdf::Cell(20,7,utf8_decode(""),1,0,'C');
                                $pdf::Cell(20,7,substr($value->persona->nombres,0,1).'. '.$value->persona->apellidopaterno,1,0,'L');
                            }elseif($value->conceptopago_id==16){//TRANSFERENCIA SOCIO
                                $pdf::Cell(18,7,number_format($detalle->pagosocio,2,'.',''),1,0,'R');
                                $pdf::Cell(18,7,utf8_decode(""),1,0,'C');
                                $pdf::Cell(20,7,utf8_decode(""),1,0,'C');
                                $pdf::Cell(20,7,substr($value->persona->nombres,0,1).'. '.$value->persona->apellidopaterno,1,0,'L');
                            }elseif($value->conceptopago_id==20){//BOLETEO TOTAL
                                $pdf::Cell(18,7,number_format($detalle->pagotarjeta,2,'.',''),1,0,'R');
                                $pdf::Cell(18,7,utf8_decode(""),1,0,'C');
                                $pdf::Cell(20,7,utf8_decode(""),1,0,'C');
                                $pdf::Cell(20,7,substr($value->persona->nombres,0,1).'. '.$value->persona->apellidopaterno,1,0,'L');
                            }elseif($value->conceptopago_id==14){//TARJETA
                                $pdf::Cell(18,7,number_format($detalle->pagotarjeta,2,'.',''),1,0,'R');
                                $pdf::Cell(18,7,utf8_decode(""),1,0,'C');
                                $pdf::Cell(20,7,utf8_decode(""),1,0,'C');
                                $pdf::Cell(20,7,substr($value->persona->nombres,0,1).'. '.$value->persona->apellidopaterno,1,0,'L');
                            }
                            $pdf::Ln();
                        }
                    }
                }elseif(in_array($value->conceptopago_id, $listConcepto) && $value->conceptopago->tipo=='I'){//CONCEPTOS QUE TIENEN LISTA INGRESOS
                    $pdf::SetTextColor(0,0,0);
                    if($pago>0 && $bandpago){
                        $pdf::SetFont('helvetica','B',8.5);
                        $pdf::Cell(223,7,'TOTAL',1,0,'R');
                        $pdf::Cell(18,7,number_format($pago,2,'.',''),1,0,'R');
                        $bandpago=false;
                        $pdf::Ln(); 
                    }
                    if($tarjeta>0 && $bandtarjeta){
                        $pdf::SetFont('helvetica','B',8.5);
                        $pdf::Cell(223,7,'TOTAL',1,0,'R');
                        $pdf::Cell(18,7,number_format($tarjeta,2,'.',''),1,0,'R');
                        $bandtarjeta=false;
                        $pdf::Ln(); 
                    }
                    if($transferenciai==0){
                        $pdf::SetFont('helvetica','B',8.5);
                        $pdf::Cell(281,7,utf8_decode("TRANSFERENCIA"),1,0,'L');
                        $pdf::Ln();
                    }
                    if($value->situacion<>'A'){
                        $pdf::SetTextColor(0,0,0);
                    }else{
                        $pdf::SetTextColor(255,0,0);
                    }
                    $transferenciai = $transferenciai + round($value->total,2);
                    //dd($value);
                    $list=explode(",",$value->listapago);
                    for($c=0;$c<count($list);$c++){
                        $pdf::SetFont('helvetica','',7);
                        $pdf::Cell(15,7,date("d/m/Y",strtotime($value->fecha)),1,0,'C');
                        $detalle = Detallemovcaja::find($list[$c]);
                        //dd($detalle);
                        $venta = Movimiento::where("movimiento_id","=",$detalle->movimiento_id)->first();
                        $ticket = Movimiento::find($detalle->movimiento_id);
                        if(!is_null($venta)){
                            if(strlen($ticket->persona->movimiento.' '.$ticket->persona->apellidomaterno.' '.$ticket->persona->nombres)>30){
                                $x=$pdf::GetX();
                                $y=$pdf::GetY();                    
                                $pdf::Multicell(60,3,($ticket->persona->apellidopaterno.' '.$ticket->persona->apellidomaterno.' '.$ticket->persona->nombres),0,'L');
                                $pdf::SetXY($x,$y);
                                $pdf::Cell(60,7,"",1,0,'C');
                            }else{
                                $pdf::Cell(60,7,($ticket->persona->apellidopaterno.' '.$ticket->persona->apellidomaterno.' '.$ticket->persona->nombres),1,0,'L');    
                            }
                            $pdf::Cell(8,7,($venta->tipodocumento_id==5?'B':'F'),1,0,'C');
                            $pdf::Cell(12,7,$venta->serie.'-'.$venta->numero,1,0,'C');
                        }
                        if($venta->tipodocumento_id==4){
                            $pdf::Cell(40,7,$venta->persona->bussinesname,1,0,'L');
                        }else{
                            $pdf::Cell(40,7,"",1,0,'L');
                        }
                        if($value->conceptopago_id==8){//HONORARIOS MEDICOS
                            $descripcion=$value->conceptopago->nombre.' - RH: '.$detalle->recibo;
                        }else{
                            $descripcion=$value->conceptopago->nombre;
                        }
                        if(strlen($descripcion)>40){
                            $x=$pdf::GetX();
                            $y=$pdf::GetY();
                            $pdf::Multicell(70,3,utf8_decode($descripcion),0,'L');
                            $pdf::SetXY($x,$y);
                            $pdf::Cell(70,7,"",1,0,'L');
                        }else{
                            $pdf::Cell(70,7,utf8_decode($descripcion),1,0,'L');
                        }
                        if($value->conceptopago_id==21 ){//BOLETEO TOTAL
                            $pdf::Cell(18,7,utf8_decode(""),1,0,'C');
                            $pdf::Cell(18,7,number_format($detalle->pagotarjeta,2,'.',''),1,0,'R');
                            $pdf::Cell(20,7,utf8_decode(""),1,0,'C');
                            $pdf::Cell(20,7,substr($value->persona->nombres,0,1).'. '.$value->persona->apellidopaterno,1,0,'L');
                        }elseif($value->conceptopago_id==17){// TRANSFERENCIA SOCIO
                            $pdf::Cell(18,7,utf8_decode(""),1,0,'C');
                            $pdf::Cell(18,7,number_format($detalle->pagosocio,2,'.',''),1,0,'R');
                            $pdf::Cell(20,7,utf8_decode(""),1,0,'C');
                            $pdf::Cell(20,7,substr($value->persona->nombres,0,1).'. '.$value->persona->apellidopaterno,1,0,'L');
                        }elseif($value->conceptopago_id==15){//TARJETA
                            $pdf::Cell(18,7,utf8_decode(""),1,0,'C');
                            $pdf::Cell(18,7,number_format($detalle->pagotarjeta,2,'.',''),1,0,'R');
                            $pdf::Cell(20,7,utf8_decode(""),1,0,'C');
                            $pdf::Cell(20,7,substr($value->persona->nombres,0,1).'. '.$value->persona->apellidopaterno,1,0,'L');
                        }
                        $pdf::Ln();   
                    }
                }elseif(in_array($value->conceptopago_id, $listConcepto2) && $value->conceptopago->tipo=='E'){//CONCEPTOS QUE TIENEN LISTA2
                    /*$pdf::SetTextColor(0,0,0);
                    if($egreso==0){
                        $pdf::SetFont('helvetica','B',8.5);
                        $pdf::Cell(279,7,utf8_decode("EGRESO"),1,0,'L');
                        $pdf::Ln();
                    }
                    if($value->situacion<>'A'){
                        $pdf::SetTextColor(0,0,0);
                    }else{
                        $pdf::SetTextColor(255,0,0);
                    }
                    $list=explode(",",$value->listapago);//print_r($value->listapago."-");
                    for($c=0;$c<count($list);$c++){
                        $detalle = Detallemovcaja::find($list[$c]);
                        $venta = Movimiento::where("movimiento_id","=",$detalle->movimiento_id)->first();
                        $pdf::SetFont('helvetica','',7);
                        $pdf::Cell(15,7,date("d/m/Y",strtotime($value->fecha)),1,0,'C');
                        if(strlen($venta->persona->apellidopaterno.' '.$venta->persona->apellidomaterno.' '.$venta->persona->nombres)>30){
                            $x=$pdf::GetX();
                            $y=$pdf::GetY();                    
                            $pdf::Multicell(60,3,($venta->persona->apellidopaterno.' '.$venta->persona->apellidomaterno.' '.$venta->persona->nombres),0,'L');
                            $pdf::SetXY($x,$y);
                            $pdf::Cell(60,7,"",1,0,'C');
                        }else{
                            $pdf::Cell(60,7,($venta->persona->apellidopaterno.' '.$venta->persona->apellidomaterno.' '.$venta->persona->nombres),1,0,'L');    
                        }
                        $pdf::Cell(8,7,($venta->tipodocumento_id==5?'B':'F'),1,0,'C');
                        $pdf::Cell(10,7,$venta->serie.'-'.$venta->numero,1,0,'C');
                        $pdf::Cell(40,7,"",1,0,'L');
                        if($value->conceptopago_id==8){//HONORARIOS MEDICOS
                            $descripcion=$value->conceptopago->nombre.' - RH: '.$detalle->recibo;
                        }else{
                            $descripcion=$value->conceptopago->nombre;
                        }
                        if(strlen($descripcion)>40){
                            $x=$pdf::GetX();
                            $y=$pdf::GetY();
                            $pdf::Multicell(70,3,utf8_decode($descripcion),0,'L');
                            $pdf::SetXY($x,$y);
                            $pdf::Cell(70,7,"",1,0,'L');
                        }else{
                            $pdf::Cell(70,7,utf8_decode($descripcion),1,0,'L');
                        }
                        if($value->conceptopago_id==8){//HONORARIOS MEDICOS
                            $pdf::Cell(18,7,number_format($detalle->pagodoctor*$detalle->cantidad,2,'.',''),1,0,'R');
                            $pdf::Cell(18,7,utf8_decode(""),1,0,'C');
                            $pdf::Cell(20,7,utf8_decode(""),1,0,'C');
                            $pdf::Cell(20,7,substr($value->persona->nombres,0,1).'. '.$value->persona->apellidopaterno,1,0,'L');
                        }else{//SOCIO
                            $pdf::Cell(18,7,number_format($detalle->pagosocio,2,'.',''),1,0,'R');
                            $pdf::Cell(18,7,utf8_decode(""),1,0,'C');
                            $pdf::Cell(20,7,utf8_decode(""),1,0,'C');
                            $pdf::Cell(20,7,substr($value->persona->nombres,0,1).'. '.$value->persona->apellidopaterno,1,0,'L');
                        }
                        $pdf::Ln();   
                    }*/
                }elseif(in_array($value->conceptopago_id, $listConcepto3) && $value->conceptopago->tipo=='E'){//CONCEPTOS QUE TIENEN LISTA3
                    $pdf::SetTextColor(0,0,0);
                    if($egreso1==0){
                        $pdf::SetFont('helvetica','B',8.5);
                        $pdf::Cell(281,7,utf8_decode("EGRESO"),1,0,'L');
                        $pdf::Ln();
                    }
                    $pdf::SetFont('helvetica','',7);
                    $pdf::Cell(15,7,date("d/m/Y",strtotime($value->fecha)),1,0,'C');
                    if(strlen($value->persona->apellidopaterno.' '.$value->persona->apellidomaterno.' '.$value->persona->nombres)>30){
                        $x=$pdf::GetX();
                        $y=$pdf::GetY();                    
                        $pdf::Multicell(60,3,($value->persona->apellidopaterno.' '.$value->persona->apellidomaterno.' '.$value->persona->nombres),0,'L');
                        $pdf::SetXY($x,$y);
                        $pdf::Cell(60,7,"",1,0,'C');
                    }else{
                        $pdf::Cell(60,7,($value->persona->apellidopaterno.' '.$value->persona->apellidomaterno.' '.$value->persona->nombres),1,0,'L');    
                    }   
                    $pdf::Cell(8,7,'RH',1,0,'C');
                    if($value->voucher==""){
                        $list=explode(",",$value->listapago);
                        $detalle = Detallemovcaja::find($list[0]);
                        if($value->conceptopago_id==25)
                            $pdf::Cell(12,7,$detalle->recibo2,1,0,'C');
                        else
                            $pdf::Cell(12,7,$detalle->recibo,1,0,'C');
                    }else{
                        $pdf::Cell(12,7,$value->voucher,1,0,'C');
                    }
                    $pdf::Cell(40,7,"",1,0,'L');
                    $descripcion=$value->conceptopago->nombre;
                    if(strlen($descripcion)>40){
                        $x=$pdf::GetX();
                        $y=$pdf::GetY();
                        $pdf::Multicell(70,3,utf8_decode($descripcion),0,'L');
                        $pdf::SetXY($x,$y);
                        $pdf::Cell(70,7,"",1,0,'L');
                    }else{
                        $pdf::Cell(70,7,utf8_decode($descripcion),1,0,'L');
                    }
                    $pdf::Cell(18,7,number_format($value->total,2,'.',''),1,0,'R');
                    $pdf::Cell(18,7,utf8_decode(""),1,0,'C');
                    $pdf::Cell(20,7,utf8_decode(""),1,0,'C');
                    $pdf::Cell(20,7,utf8_decode(""),1,0,'C');
                    $pdf::Ln();
                    $egreso1 = $egreso1 + round($value->total,2);
                }elseif($value->conceptopago_id==23 || $value->conceptopago_id == 32){//COBRANZA
                    if ($caja_id == 3 || $caja_id == 4 && $value->conceptopago_id == 32) {//print_r($value->id.'@');
                        if($pago>0 && $bandpago){
                            $pdf::SetFont('helvetica','B',8.5);
                            $pdf::Cell(223,7,'TOTAL',1,0,'R');
                            $pdf::Cell(18,7,number_format($pago,2,'.',''),1,0,'R');
                            $bandpago=false;
                            $pdf::Ln(); 
                        }
                        if($tarjeta>0 && $bandtarjeta){
                            $pdf::SetFont('helvetica','B',8.5);
                            $pdf::Cell(223,7,'TOTAL',1,0,'R');
                            $pdf::Cell(18,7,number_format($tarjeta,2,'.',''),1,0,'R');
                            $bandtarjeta=false;
                            $pdf::Ln(); 
                        }
                        $listventas = Movimiento::where('movimientodescarga_id','=',$value->id)->get();
                        if($value->id==294770){
                            //dd($listventas);
                        }
                        if($cobranza==0 && $value->conceptopago->tipo=="I"){
                            $pdf::SetFont('helvetica','B',8.5);
                            $pdf::Cell(281,7,utf8_decode("COBRANZA"),1,0,'L');
                            $pdf::Ln();
                        }
                        foreach ($listventas as $key6 => $value6) {
                            $pdf::SetFont('helvetica','',7);
                            $pdf::Cell(15,7,date("d/m/Y",strtotime($value6->fecha)),1,0,'C');
                            $nombrepersona = '-';
                            if ($value6->persona_id !== NULL) {
                                //echo 'entro'.$value6->id;break;
                                $nombrepersona =$value6->persona->apellidopaterno.' '.$value6->persona->apellidomaterno.' '.$value6->persona->nombres;

                            }else{
                                $nombrepersona = trim($value6->nombrepaciente);
                            }
                            if(strlen($nombrepersona)>30){
                                $x=$pdf::GetX();
                                $y=$pdf::GetY();                    
                                $pdf::Multicell(60,3,($nombrepersona),0,'L');
                                $pdf::SetXY($x,$y);
                                $pdf::Cell(60,7,"",1,0,'C');
                            }else{
                                $pdf::Cell(60,7,($nombrepersona),1,0,'L');    
                            }
                            $pdf::Cell(8,7,($value6->tipodocumento_id==5?'B':'F'),1,0,'C');
                            $pdf::Cell(12,7,$value6->serie.'-'.$value6->numero,1,0,'C');
                            $nombreempresa = '';
                            if ($value6->conveniofarmacia_id != null) {
                                $nombreempresa = $value6->conveniofarmacia->nombre;
                            }
                            $pdf::Cell(40,7,substr($nombreempresa,0,23),1,0,'L');
                            $pdf::Cell(70,7,'MEDICINA',1,0,'L');
                            $pdf::Cell(18,7,'',1,0,'C');
                            if($value->situacion!="R")
                                $pdf::Cell(18,7,number_format($value6->total,2,'.',''),1,0,'R');
                            else
                                $pdf::Cell(18,7,' - ',1,0,'R');
                            if($value->tipotarjeta!=""){
                                $x=$pdf::GetX();
                                $y=$pdf::GetY();                    
                                $pdf::Multicell(20,3,$value->tipotarjeta." ".$value->tarjeta." / ".$value->voucher,0,'L');
                                $pdf::SetXY($x,$y);
                            }
                            $pdf::Cell(20,7,utf8_decode(""),1,0,'C');
                            if ($value6->doctor_id != null) {
                                $pdf::Cell(20,7,substr($value6->doctor->nombres,0,1).'. '.$value6->doctor->apellidopaterno,1,0,'L');

                            }else{
                               $pdf::Cell(20,7," - ",1,0,'L'); 
                            }
                            if($value->situacion!="R"){
                                $cobranza=$cobranza + round($value6->total,2);
                            }
                            $pdf::Ln();
                        }
                    }elseif($caja_id == 3 || $caja_id == 4 && $value->conceptopago_id == 23){
                        if($pago>0 && $bandpago){
                            $pdf::SetFont('helvetica','B',8.5);
                            $pdf::Cell(223,7,'TOTAL',1,0,'R');
                            $pdf::Cell(18,7,number_format($pago,2,'.',''),1,0,'R');
                            $bandpago=false;
                            $pdf::Ln(); 
                        }
                        if($tarjeta>0 && $bandtarjeta){
                            $pdf::SetFont('helvetica','B',8.5);
                            $pdf::Cell(223,7,'TOTAL',1,0,'R');
                            $pdf::Cell(18,7,number_format($tarjeta,2,'.',''),1,0,'R');
                            $bandtarjeta=false;
                            $pdf::Ln(); 
                        }
                        $listventas = Movimiento::where('movimiento_id','=',$value->id)->get();
                        if($cobranza==0 && $value->conceptopago->tipo=="I"){
                            $pdf::SetFont('helvetica','B',8.5);
                            $pdf::Cell(281,7,utf8_decode("COBRANZA"),1,0,'L');
                            $pdf::Ln();
                        }
                        foreach ($listventas as $key6 => $value6) {
                            $pdf::SetFont('helvetica','',7);
                            $pdf::Cell(15,7,date("d/m/Y",strtotime($value6->fecha)),1,0,'C');
                            $nombrepersona = '-';
                            if ($value6->persona_id !== NULL) {
                                //echo 'entro'.$value6->id;break;
                                $nombrepersona =$value6->persona->apellidopaterno.' '.$value6->persona->apellidomaterno.' '.$value6->persona->nombres;

                            }else{
                                $nombrepersona = trim($value6->nombrepaciente);
                            }
                            if(strlen($nombrepersona)>30){
                                $x=$pdf::GetX();
                                $y=$pdf::GetY();                    
                                $pdf::Multicell(60,3,($nombrepersona),0,'L');
                                $pdf::SetXY($x,$y);
                                $pdf::Cell(60,7,"",1,0,'C');
                            }else{
                                $pdf::Cell(60,7,($nombrepersona),1,0,'L');    
                            }
                            $pdf::Cell(8,7,($venta->tipodocumento_id==5?'B':'F'),1,0,'C');
                            $pdf::Cell(12,7,$value6->serie.'-'.$value6->numero,1,0,'C');
                            $nombreempresa = '';
                            if ($value6->conveniofarmacia_id != null) {
                                $nombreempresa = $value6->conveniofarmacia->nombre;
                            }
                            $pdf::Cell(40,7,substr($nombreempresa,0,23),1,0,'L');
                            $pdf::Cell(70,7,'MEDICINA',1,0,'L');
                            $pdf::Cell(18,7,'',1,0,'C');
                            $pdf::Cell(18,7,number_format($value6->total,2,'.',''),1,0,'R');
                            if($value->tipotarjeta!=""){
                                $x=$pdf::GetX();
                                $y=$pdf::GetY();                    
                                $pdf::Multicell(20,3,$value->tipotarjeta." ".$value->tarjeta." / ".$value->voucher,0,'L');
                                $pdf::SetXY($x,$y);
                            }
                            $pdf::Cell(20,7,utf8_decode(""),1,0,'C');
                            if ($value6->doctor_id != null) {
                                $pdf::Cell(20,7,substr($value6->doctor->nombres,0,1).'. '.$value6->doctor->apellidopaterno,1,0,'L');

                            }else{
                               $pdf::Cell(20,7," - ",1,0,'L'); 
                            }
                            $cobranza=$cobranza + round($value6->total,2);
                            $pdf::Ln();
                        }
                    }else{

                        $pdf::SetTextColor(0,0,0);
                        if($pago>0 && $bandpago){
                            $pdf::SetFont('helvetica','B',8.5);
                            $pdf::Cell(223,7,'TOTAL',1,0,'R');
                            $pdf::Cell(18,7,number_format($pago,2,'.',''),1,0,'R');
                            $bandpago=false;
                            $pdf::Ln(); 
                        }
                        if($tarjeta>0 && $bandtarjeta){
                            $pdf::SetFont('helvetica','B',8.5);
                            $pdf::Cell(223,7,'TOTAL',1,0,'R');
                            $pdf::Cell(18,7,number_format($tarjeta,2,'.',''),1,0,'R');
                            $bandtarjeta=false;
                            $pdf::Ln(); 
                        }
                        if($ingresotarjeta>0 && $bandingresotarjeta){
                            $pdf::SetFont('helvetica','B',8.5);
                            $pdf::Cell(223,7,'TOTAL',1,0,'R');
                            $pdf::Cell(18,7,number_format($ingresotarjeta,2,'.',''),1,0,'R');
                            $bandingresotarjeta=false;
                            $pdf::Ln(); 
                        }
                        if($cobranza==0 && $value->conceptopago->tipo=="I"){
                            $pdf::SetFont('helvetica','B',8.5);
                            $pdf::Cell(281,7,utf8_decode("COBRANZA"),1,0,'L');
                            $pdf::Ln();
                        }
                        if($value->situacion<>'A'){
                            $pdf::SetTextColor(0,0,0);
                        }else{
                            $pdf::SetTextColor(255,0,0);
                        }
                        $pdf::SetFont('helvetica','',7);
                        $pdf::Cell(15,7,date("d/m/Y",strtotime($value->fecha)),1,0,'C');
                        $nombrepersona = '-';
                        $venta = Movimiento::find($value->movimiento_id);
                        if ($value->persona_id !== NULL) {
                            //echo 'entro'.$value->id;break;
                            if($venta->tipodocumento_id==5){
                                $nombrepersona =$value->persona->apellidopaterno.' '.$value->persona->apellidomaterno.' '.$value->persona->nombres;
                            }else{
                                $nombrepersona = $value->persona->bussinesname;
                            }
                        }else{
                            $nombrepersona = trim($value->nombrepaciente);
                        }
                        if(strlen($nombrepersona)>30){
                            $x=$pdf::GetX();
                            $y=$pdf::GetY();                    
                            $pdf::Multicell(60,3,($nombrepersona),0,'L');
                            $pdf::SetXY($x,$y);
                            $pdf::Cell(60,7,"",1,0,'C');
                        }else{
                            $pdf::Cell(60,7,($nombrepersona),1,0,'L');    
                        }
                        $pdf::Cell(8,7,($venta->tipodocumento_id==5?'B':"F"),1,0,'C');
                        $pdf::Cell(12,7,utf8_decode($venta->serie.'-'.$venta->numero),1,0,'C');
                        if($value->conceptopago_id==11){//PAGO A ENFERMERIA
                            $descripcion=$value->conceptopago->nombre.': '.$value->comentario.' - RH: '.$value->voucher;
                        }else{
                            $descripcion=$value->conceptopago->nombre.': '.$value->comentario;
                        }
                        if(strlen($descripcion)>70){
                            $x=$pdf::GetX();
                            $y=$pdf::GetY();
                            $pdf::Multicell(110,3,($descripcion),0,'L');
                            $pdf::SetXY($x,$y);
                            $pdf::Cell(110,7,"",1,0,'L');
                        }else{
                            $pdf::Cell(110,7,($descripcion),1,0,'L');
                        }
                        if($value->situacion<>'R' && $value->situacion2<>'R'){
                            if($value->conceptopago->tipo=="I"){
                                $pdf::Cell(18,7,utf8_decode(""),1,0,'R');
                                $pdf::Cell(18,7,number_format($value->total,2,'.',''),1,0,'R');
                            }else{
                                $pdf::Cell(18,7,number_format($value->total,2,'.',''),1,0,'R');
                                $pdf::Cell(18,7,utf8_decode(""),1,0,'C');
                            }
                        }else{
                            $pdf::Cell(18,7,utf8_decode(" - "),1,0,'C');
                            $pdf::Cell(18,7,utf8_decode(" - "),1,0,'C');
                        }
                        $cobranza=$cobranza + round($value->total,2);
                        if($value->tipotarjeta!=""){
                            $x=$pdf::GetX();
                            $y=$pdf::GetY();                    
                            $pdf::Multicell(20,3,$value->tipotarjeta." ".$value->tarjeta." / ".$value->voucher,0,'L');
                            $pdf::SetXY($x,$y);
                        }
                        $pdf::Cell(20,7,utf8_decode(""),1,0,'C');
                        $pdf::Cell(20,7,utf8_decode(""),1,0,'C');
                        $pdf::Ln();
                    }
                }elseif($value->conceptopago_id==33){//PAGO DE FARMACIA
                    $pdf::SetTextColor(0,0,0);
                    if($transferenciae>0 && $bandtransferenciae){
                        $pdf::SetFont('helvetica','B',8.5);
                        $pdf::Cell(205,7,'TOTAL',1,0,'R');
                        $pdf::Cell(18,7,number_format($transferenciae,2,'.',''),1,0,'R');
                        $transferenciae=false;
                        $pdf::Ln(); 
                    }
                    if($tarjeta>0 && $bandtarjeta){
                        $pdf::SetFont('helvetica','B',8.5);
                        $pdf::Cell(223,7,'TOTAL',1,0,'R');
                        $pdf::Cell(18,7,number_format($tarjeta,2,'.',''),1,0,'R');
                        $bandtarjeta=false;
                        $pdf::Ln(); 
                    }
                    if($pago>0 && $bandpago){
                        $pdf::SetFont('helvetica','B',8.5);
                        $pdf::Cell(223,7,'TOTAL',1,0,'R');
                        $pdf::Cell(18,7,number_format($pago,2,'.',''),1,0,'R');
                        $bandpago=false;
                        $pdf::Ln(); 
                    }

                    if($ingresotarjeta==0){
                        $pdf::SetFont('helvetica','B',8.5);
                        $pdf::Cell(281,7,utf8_decode("INGRESOS POR TRANSFERENCIA"),1,0,'L');
                        $pdf::Ln();
                    }
                    if($value->situacion<>'A'){
                        $pdf::SetTextColor(0,0,0);
                    }else{
                        $pdf::SetTextColor(255,0,0);
                    }
                    $pdf::SetFont('helvetica','',7);
                    $pdf::Cell(15,7,date("d/m/Y",strtotime($value->fecha)),1,0,'C');
                    $nombrepersona = '-';
                    if ($value->persona_id != NULL && !is_null($value->persona)) {
                        //echo 'entro'.$value->id;break;
                        if ($value->persona->bussinesname != null) {
                            $nombrepersona = $value->persona->bussinesname;
                        }else{
                            $nombrepersona =$value->persona->apellidopaterno.' '.$value->persona->apellidomaterno.' '.$value->persona->nombres;
                        }
                        
                    }else{
                        $nombrepersona = trim($value->nombrepaciente);
                    }
                    if(strlen($nombrepersona)>30){
                        $x=$pdf::GetX();
                        $y=$pdf::GetY();                    
                        $pdf::Multicell(60,3,($nombrepersona),0,'L');
                        $pdf::SetXY($x,$y);
                        $pdf::Cell(60,7,"",1,0,'C');
                    }else{
                        $pdf::Cell(60,7,($nombrepersona),1,0,'L');    
                    }
                    if($value->conceptopago_id==31){
                        $pdf::Cell(8,7,'T',1,0,'C');
                    }else{
                        $pdf::Cell(8,7,trim($value->formapago==''?'':$value->formapago),1,0,'C'); 
                        
                    }
                    $pdf::Cell(12,7,utf8_decode(trim($value->voucher)==''?$value->numero:$value->voucher),1,0,'C');
                
                    $descripcion=$value->conceptopago->nombre.': '.$value->comentario;
                    
                    if(strlen($descripcion)>70){
                        $x=$pdf::GetX();
                        $y=$pdf::GetY();
                        $pdf::Multicell(110,3,SUBSTR($descripcion,0,150),0,'L');
                        $pdf::SetXY($x,$y);
                        $pdf::Cell(110,7,"",1,0,'L');
                    }else{
                        $pdf::Cell(110,7,($descripcion),1,0,'L');
                    }
                    if($value->situacion<>'R' && $value->situacion2<>'R'){
                        if($value->conceptopago->tipo=="I"){
                            $pdf::Cell(18,7,utf8_decode(""),1,0,'R');
                            $pdf::Cell(18,7,number_format($value->total,2,'.',''),1,0,'R');
                            $ingresotarjeta=$ingresotarjeta + round($value->total,2);
                        }else{
                            $egreso1=$egreso1 + round($value->total,2);
                            $pdf::Cell(18,7,number_format($value->total,2,'.',''),1,0,'R');
                            $pdf::Cell(18,7,utf8_decode(""),1,0,'C');
                        }
                    }else{
                        $pdf::Cell(18,7,utf8_decode(" - "),1,0,'C');
                        $pdf::Cell(18,7,utf8_decode(" - "),1,0,'C');
                    }
                    $pdf::Cell(20,7,utf8_decode(""),1,0,'C');
                    $pdf::Cell(20,7,utf8_decode(""),1,0,'C');
                    $pdf::Ln();
                }elseif($value->conceptopago_id!=1 && $value->conceptopago_id!=2 && $value->conceptopago_id!=23 && $value->conceptopago_id!=10){
                    $pdf::SetTextColor(0,0,0);
                    if($transferenciae>0 && $bandtransferenciae){
                        $pdf::SetFont('helvetica','B',8.5);
                        $pdf::Cell(205,7,'TOTAL',1,0,'R');
                        $pdf::Cell(18,7,number_format($transferenciae,2,'.',''),1,0,'R');
                        $transferenciae=false;
                        $pdf::Ln(); 
                    }
                    if(($ingreso==0 || $pago==0) && $value->conceptopago->tipo=="I"){
                        if($egreso1>0 && $bandegreso){
                            $pdf::SetFont('helvetica','B',8.5);
                            $pdf::Cell(205,7,'TOTAL',1,0,'R');
                            $pdf::Cell(18,7,number_format($egreso1,2,'.',''),1,0,'R');
                            $bandegreso=false;
                            $pdf::Ln(); 
                        }
                        $pdf::SetFont('helvetica','B',8.5);
                        $pdf::Cell(281,7,utf8_decode("INGRESOS"),1,0,'L');
                        $pdf::Ln();
                        if($pago==0){
                            if($caja_id==3){
                                $pdf::SetFont('helvetica','B',7);
                                $pdf::Cell(223,7,utf8_decode("SALDO INICIAL"),1,0,'L');
                                $apert = Movimiento::find($aperturas[$valor]);
                                $pdf::Cell(18,7,number_format($apert->total,2,'.',''),1,0,'R');
                                $pago = $pago + round($apert->total,2);
                                $ingreso = $ingreso + round($apert->total,2);
                                $pdf::Ln();
                            }
                        }
                    }elseif($egreso1==0 && $value->conceptopago->tipo=="E"){
                        $pdf::SetFont('helvetica','B',8.5);
                        $pdf::Cell(281,7,utf8_decode("EGRESOS"),1,0,'L');
                        $pdf::Ln();
                    }
                    if($value->situacion<>'A'){
                        $pdf::SetTextColor(0,0,0);
                    }else{
                        $pdf::SetTextColor(255,0,0);
                    }
                    $pdf::SetFont('helvetica','',7);
                    $pdf::Cell(15,7,date("d/m/Y",strtotime($value->fecha)),1,0,'C');
                    $nombrepersona = '-';
                    if ($value->persona_id != NULL && !is_null($value->persona)) {
                        //echo 'entro'.$value->id;break;
                        if ($value->persona->bussinesname != null) {
                            $nombrepersona = $value->persona->bussinesname;
                        }else{
                            $nombrepersona =$value->persona->apellidopaterno.' '.$value->persona->apellidomaterno.' '.$value->persona->nombres;
                        }
                        
                    }else{
                        $nombrepersona = trim($value->nombrepaciente);
                    }
                    if(strlen($nombrepersona)>30){
                        $x=$pdf::GetX();
                        $y=$pdf::GetY();                    
                        $pdf::Multicell(60,3,($nombrepersona),0,'L');
                        $pdf::SetXY($x,$y);
                        $pdf::Cell(60,7,"",1,0,'C');
                    }else{
                        $pdf::Cell(60,7,($nombrepersona),1,0,'L');    
                    }
                    if($value->conceptopago_id!=13){
                        if($value->conceptopago_id==31){
                            $pdf::Cell(8,7,'T',1,0,'C');
                        }else{
                            if ($caja_id == 3 || $caja_id == 4) {
                                if ($value->tipodocumento_id == 7) {
                                    $pdf::Cell(8,7,'BV',1,0,'C');
                                }elseif($value->tipodocumento_id == 6){
                                    $pdf::Cell(8,7,'FT',1,0,'C');
                                }else{
                                    $pdf::Cell(8,7,trim($value->formapago==''?'':$value->formapago),1,0,'C'); 
                                }
                            }else{
                               $pdf::Cell(8,7,trim($value->formapago==''?'':$value->formapago),1,0,'C'); 
                            }
                            
                        }
                        $pdf::Cell(12,7,utf8_decode(trim($value->voucher)==''?$value->numero:$value->voucher),1,0,'C');
                    }else{//PARA ANULACION POR NOTA CREDITO
                        $pdf::Cell(8,7,'NA',1,0,'C');
                        //print_r($value->id);
                        $mov = Movimiento::find($value->movimiento_id);
                        $pdf::Cell(12,7,($mov->serie.'-'.$mov->numero),1,0,'C');
                    }
                    if(empty($array)){
                        $array = array();
                    }
                    
                    if($value->voucher=="4-23142" || (!empty($mov) &&($mov->serie.'-'.$mov->numero)=="4-23142")){
                        $array[] = $value;
                            
                        }
                    if(count($array)>1){
                        //dd($array);
                    }
                    if($value->conceptopago_id==11){//PAGO A ENFERMERIA
                        $descripcion=$value->conceptopago->nombre.': '.$value->comentario;
                    }else{
                        $descripcion=$value->conceptopago->nombre.': '.$value->comentario;
                    }
                    if(strlen($descripcion)>70){
                        $x=$pdf::GetX();
                        $y=$pdf::GetY();
                        $pdf::Multicell(110,3,SUBSTR($descripcion,0,150),0,'L');
                        $pdf::SetXY($x,$y);
                        $pdf::Cell(110,7,"",1,0,'L');
                    }else{
                        $pdf::Cell(110,7,($descripcion),1,0,'L');
                    }
                    if($value->situacion<>'R' && $value->situacion2<>'R'){
                        if($value->conceptopago->tipo=="I"){
                            $pdf::Cell(18,7,utf8_decode(""),1,0,'R');
                            $pdf::Cell(18,7,number_format($value->total,2,'.',''),1,0,'R');
                            $pago=$pago + round($value->total,2);
                        }else{
                            $egreso1=$egreso1 + round($value->total,2);
                            $pdf::Cell(18,7,number_format($value->total,2,'.',''),1,0,'R');
                            $pdf::Cell(18,7,utf8_decode(""),1,0,'C');
                        }
                    }else{
                        $pdf::Cell(18,7,utf8_decode(" - "),1,0,'C');
                        $pdf::Cell(18,7,utf8_decode(" - "),1,0,'C');
                    }
                    $pdf::Cell(20,7,utf8_decode(""),1,0,'C');
                    $pdf::Cell(20,7,utf8_decode(""),1,0,'C');
                    $pdf::Ln();
                }
                
                if($value->conceptopago_id<>2 && $value->situacion<>'A'){
                    if($value->conceptopago->tipo=="I"){
                        if($value->conceptopago_id<>10){//GARANTIA
                            if($value->conceptopago_id<>15 && $value->conceptopago_id<>17 && $value->conceptopago_id<>19 && $value->conceptopago_id<>21){
                                if ($value->tipodocumento_id != 15) {
                                    //echo $value->total."@";205851
                                    $ingreso = $ingreso + round($value->total,2);
                                }
                                    
                            }elseif(($value->conceptopago_id==15 || $value->conceptopago_id==17 || $value->conceptopago_id==19 || $value->conceptopago_id==21) && $value->situacion=='C'){
                                $ingreso = $ingreso + round($value->total,2);    
                            }
                        }else{
                            $garantia = $garantia + $value->total;
                        }
                        if($value->conceptopago_id<>10){//GARANTIA
                            if($value->tipotarjeta=='VISA'){
                                    $visa = $visa + round($value->total,2);
                            }elseif($value->tipotarjeta==''){
                                if ($value->tipodocumento_id != 15) {
                                    $efectivo = $efectivo + round($value->total,2);
                                }
                            }else{
                                $master = $master + round($value->total,2);
                            }
                        }
                    }else{
                        if($value->conceptopago_id<>14 && $value->conceptopago_id<>16 && $value->conceptopago_id<>18 && $value->conceptopago_id<>20){
                            if($value->conceptopago_id==8){//HONORARIOS MEDICOS
                                $ingreso  = $ingreso - round($value->total,2);
                                $efectivo = $efectivo - round($value->total,2);
                            }else{
                                $egreso  = $egreso + round($value->total,2);
                            }
                        }elseif(($value->conceptopago_id==14 || $value->conceptopago_id==16 || $value->conceptopago_id==18 || $value->conceptopago_id==20) && $value->situacion2=='C'){
                            $egreso  = $egreso + round($value->total,2);
                        }
                    }
                }
                $res=$value->responsable2;
                if ($caja_id == 3 ||$caja_id == 4) {
                    /*if($tarjeta>0 && $bandtarjeta){
                        $pdf::SetFont('helvetica','B',8.5);
                        $pdf::Cell(223,7,'TOTAL',1,0,'R');
                        $pdf::Cell(18,7,number_format($tarjeta,2,'.',''),1,0,'R');
                        $bandtarjeta=false;
                        $pdf::Ln(); 
                    }*/
                }
            }
            if($ingresotarjeta>0 && $bandingresotarjeta){
                $pdf::SetFont('helvetica','B',8.5);
                $pdf::Cell(223,7,'TOTAL',1,0,'R');
                $pdf::Cell(18,7,number_format($ingresotarjeta,2,'.',''),1,0,'R');
                $bandingresotarjeta=false;
                $pdf::Ln(); 
            }
            if($cobranza>0 && $bandcobranza){
                $pdf::SetFont('helvetica','B',8.5);
                $pdf::Cell(223,7,'TOTAL',1,0,'R');
                $pdf::Cell(18,7,number_format($cobranza,2,'.',''),1,0,'R');
                $bandpago=false;
                $pdf::Ln(); 
            }
            if($transferenciai>0 && $bandtransferenciai){
                $pdf::SetFont('helvetica','B',8.5);
                $pdf::Cell(223,7,'TOTAL',1,0,'R');
                $pdf::Cell(18,7,number_format($transferenciai,2,'.',''),1,0,'R');
                $bandpago=false;
                $pdf::Ln(); 
            }
            if($pago==0){
                $pdf::SetFont('helvetica','B',8.5);
                $pdf::Cell(281,7,utf8_decode("INGRESOS"),1,0,'L');
                $pdf::Ln();
                if($caja_id==3){
                    $pdf::SetFont('helvetica','B',7);
                    $pdf::Cell(223,7,utf8_decode("SALDO INICIAL"),1,0,'L');
                    $apert = Movimiento::find($aperturas[$valor]);
                    $pdf::Cell(18,7,number_format($apert->total,2,'.',''),1,0,'R');
                    $pago = $pago + round($apert->total,2);
                    $ingreso = $ingreso + round($apert->total,2);
                    $pdf::Ln();
                }
            }
            if($pago>0 && $bandpago){
                $pdf::SetFont('helvetica','B',8.5);
                $pdf::Cell(223,7,'TOTAL',1,0,'R');
                $pdf::Cell(18,7,number_format($pago,2,'.',''),1,0,'R');
                $bandpago=false;
                $pdf::Ln(); 
            }
            if($tarjeta>0 && $bandtarjeta){
                $pdf::SetFont('helvetica','B',8.5);
                $pdf::Cell(223,7,'TOTAL',1,0,'R');
                $pdf::Cell(18,7,number_format($tarjeta,2,'.',''),1,0,'R');
                $bandtarjeta=false;
                $pdf::Ln(); 
            }
            $resultado1       = Movimiento::join('person as responsable', 'responsable.id', '=', 'movimiento.responsable_id')
                                ->leftjoin('movimiento as m2','movimiento.movimiento_id','=','m2.id')
                                ->leftjoin('person as paciente', 'paciente.id', '=', 'm2.persona_id')
                                ->where('movimiento.serie', '=', $serie)
                                ->where('movimiento.tipomovimiento_id', '=', 4)
                                ->where('movimiento.sucursal_id','=',$sucursal_id)
                                ->where('movimiento.tipodocumento_id', '<>', 15)
                                ->where(function ($query) use($aperturas,$cierres,$valor) {
                                    $query->where(function($q) use($aperturas,$cierres,$valor){
                                            $q->where('movimiento.id', '>', $aperturas[$valor])
                                            ->where('movimiento.id', '<', $cierres[$valor])
                                            ->whereNull('movimiento.cajaapertura_id');
                                    })
                                          ->orwhere(function ($query1) use($aperturas,$cierres,$valor){
                                            $query1->where('movimiento.cajaapertura_id','=',$aperturas[$valor]);
                                            });//normal
                                })
                                ->where('movimiento.situacion','like','U');
            $resultado1       = $resultado1->select('movimiento.*','m2.situacion as situacion2',DB::raw('concat(paciente.apellidopaterno,\' \',paciente.apellidomaterno,\' \',paciente.nombres) as paciente2'))->orderBy('movimiento.numero', 'asc');
            
            $lista1           = $resultado1->get();
            if(count($lista1)>0){
                //echo 'alert('.count($lista1).')';
                $anuladas=0;
                $pdf::SetFont('helvetica','B',8.5);
                $pdf::Cell(281,7,utf8_decode("ANULADAS"),1,0,'L');
                $pdf::Ln();
                foreach($lista1 as $key1 => $value1){
                    $pdf::SetFont('helvetica','',7.5);
                    $pdf::Cell(15,7,date("d/m/Y",strtotime($value1->fecha)),1,0,'C');
                    if($value1->tipodocumento_id==5){//BOLETA}
                        $nombre='ANULADO';
                        //$value1->persona->apellidopaterno.' '.$value1->persona->apellidomaterno.' '.$value1->persona->nombres;
                    }else{
                        $nombre=$value1->paciente2;
                        if($value1->persona_id>0){
                            $empresa=$value1->persona->bussinesname;
                        }else{
                            $empresa='';
                        }
                    }
                    if(strlen($nombre)>30){
                        $x=$pdf::GetX();
                        $y=$pdf::GetY();                    
                        $pdf::Multicell(60,3,($nombre),0,'L');
                        $pdf::SetXY($x,$y);
                        $pdf::Cell(60,7,"",1,0,'C');
                    }else{
                        $pdf::Cell(60,7,($nombre),1,0,'L');    
                    }
                    $pdf::Cell(8,7,($value1->tipodocumento_id==5?'B':'F'),1,0,'C');
                    $pdf::Cell(12,7,$value1->serie.'-'.$value1->numero,1,0,'C');
                    if($caja_id==4){
                        $nombreempresa='-';
                        if ($value->tipodocumento_id != 5) {
                            if ($value->empresa != null) {
                                $nombreempresa = trim($value->empresa->bussinesname);
                            }
                        }
                        $pdf::Cell(40,7,substr($nombreempresa,0,23),1,0,'L');
                    }else{
                        if($value1->tipodocumento_id==5){
                            if($value1->movimiento_id>0){
                            $ticket= Movimiento::find($value1->movimiento_id);
                            if($ticket->plan_id>0)
                                $pdf::Cell(40,7,substr($ticket->plan->nombre,0,23),1,0,'L');
                            else
                                $pdf::Cell(40,7,"",1,0,'L');
                            }else{
                                $pdf::Cell(40,7,"",1,0,'L');
                            }
                        }else{
                            $pdf::Cell(40,7,substr($empresa,0,23),1,0,'L');
                        }
                    }
                    if($caja_id==4){
                        $pdf::Cell(70,7,"MEDICINA",1,0,'L');    
                    }else{
                        $pdf::Cell(70,7,"SERVICIOS",1,0,'L');    
                    }
                    $pdf::Cell(18,7,'',1,0,'C');
                    $pdf::Cell(18,7,number_format(0,2,'.',''),1,0,'R');
                    $pdf::Cell(20,7,utf8_decode(" - "),1,0,'C');
                    $pdf::Cell(20,7,'-',1,0,'L');
                    //substr($v->persona->nombres,0,1).'. '.$v->persona->apellidopaterno
                    $pdf::Ln();
                    $anuladas=$anuladas + number_format(0,2,'.','');
                }
            }
            $resp=Movimiento::find($cierres[$valor]);
            $pdf::Ln();
            $pdf::Cell(120,7,('RESPONSABLE: '.$resp->responsable->nombres)." / Hora Cierre: ".date("d/m/Y H:i:s",strtotime($resp->created_at)),0,0,'L');
            $pdf::SetFont('helvetica','B',9);
            $pdf::Cell(50,7,utf8_decode("RESUMEN DE CAJA"),1,0,'C');
            $pdf::Ln();
            $pdf::Cell(120,7,utf8_decode(""),0,0,'C');
            $pdf::Cell(30,7,utf8_decode("INGRESOS :"),1,0,'L');
            if($efectivo==4987.19){
                $ingreso = $ingreso - 0.01;
                $efectivo = $efectivo - 0.01;
            }
            $pdf::Cell(20,7,number_format($ingreso + $transferenciai,2,'.',''),1,0,'R');
            $pdf::Ln();
            /*$pdf::Cell(120,7,utf8_decode(""),0,0,'C');
            $pdf::Cell(30,7,utf8_decode("INGRESOS(T) :"),1,0,'L');
            $pdf::Cell(20,7,number_format($transferenciai,2,'.',''),1,0,'R');
            $pdf::Ln();*/
            $pdf::Cell(120,7,utf8_decode(""),0,0,'C');
            $pdf::Cell(30,7,utf8_decode("Efectivo :"),1,0,'L');
            $pdf::Cell(20,7,number_format($efectivo,2,'.',''),1,0,'R');
            $pdf::Ln();
            $pdf::Cell(120,7,utf8_decode(""),0,0,'C');
            $pdf::Cell(30,7,utf8_decode("Master :"),1,0,'L');
            $pdf::Cell(20,7,number_format($master,2,'.',''),1,0,'R');
            $pdf::Ln();
            $pdf::Cell(120,7,utf8_decode(""),0,0,'C');
            $pdf::Cell(30,7,utf8_decode("Visa :"),1,0,'L');
            $pdf::Cell(20,7,number_format($visa,2,'.',''),1,0,'R');
            $pdf::Ln();
            $pdf::Cell(120,7,utf8_decode(""),0,0,'C');
            $pdf::Cell(30,7,utf8_decode("EGRESOS :"),1,0,'L');
            $pdf::Cell(20,7,number_format($egreso,2,'.',''),1,0,'R');
            $pdf::Ln();
            $pdf::Cell(120,7,utf8_decode(""),0,0,'C');
            $pdf::Cell(30,7,utf8_decode("SALDO :"),1,0,'L');
            $pdf::Cell(20,7,number_format($ingreso - $egreso - $visa - $master,2,'.',''),1,0,'R');
            $pdf::Ln();
            //$pdf::Output('ListaCaja.pdf');
                
            }

        }
        $pdf::Output('ListaCaja.pdf');
    }

    public function apertura(Request $request){
        $entidad             = 'Caja';
        $formData            = array('caja.aperturar');
        $listar              = $request->input('listar');
        $caja                = Caja::find($request->input('caja_id'));
        //sucursal_id
        $sucursal_id = Session::get('sucursal_id');
        $numero              = Movimiento::NumeroSigue($caja->id,$sucursal_id,2,2);//movimiento caja y documento ingreso
        $formData            = array('route' => $formData, 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton               = 'Aperturar '.$caja->nombre;
        return view($this->folderview.'.apertura')->with(compact('caja', 'formData', 'entidad', 'boton', 'listar', 'numero'));
    }
    
    public function aperturar(Request $request)
    {
        $reglas     = array(
                'caja_id'                  => 'required',
                );
        $mensajes = array(
            'caja.required'         => 'Debe seleccionar una caja',
            );
        $validacion = Validator::make($request->all(), $reglas, $mensajes);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        }
        $user = Auth::user();

        //sucursal_id
        $sucursal_id = Session::get('sucursal_id');

        $error = DB::transaction(function() use($request, $user, $sucursal_id){
            $movimiento        = new Movimiento();
            $movimiento->sucursal_id = $sucursal_id;
            $movimiento->fecha = date("Y-m-d H:i:s");
            $movimiento->numero= $request->input('numero');
            $movimiento->responsable_id=$user->person_id;
            $movimiento->persona_id=$user->person_id;
            $movimiento->subtotal=0;
            $movimiento->igv=0;
            // if($request->input('caja_id')==3){
            //     $ultimo = Movimiento::where('conceptopago_id','=',2)
            //               ->where('caja_id','=',3)  
            //               ->orderBy('id','desc')->limit(1)->first();
            //     if(count($ultimo)>0){
            //         $movimiento->total=$ultimo->total;
            //     }else{
            //         $movimiento->total=0;    
            //     }
            // }else{
                $movimiento->total=0;     
            //}
            $movimiento->tipomovimiento_id=2;
            $movimiento->tipodocumento_id=2;
            $movimiento->conceptopago_id=1;
            $movimiento->comentario=$request->input('comentario');
            $movimiento->caja_id=$request->input('caja_id');
            $movimiento->situacion='N';
            $movimiento->save();
            $caja = Caja::find($request->input('caja_id'));
            $caja->estado = "A";
            $caja->save();
        });
        return is_null($error) ? "OK" : $error;
    }
    
    public function generarConcepto(Request $request)
    {
        $tipodoc = $request->input("tipodocumento_id");
        if($tipodoc==2){
            $rst = Conceptopago::where('tipo','like','I')->where('id','<>',1)->where('id','<>',6)->where('id','<>',15)->where('id','<>',17)->where('id','<>',19)->where('id','<>',21)->where('id','<>',23)->where('id','<>',31)->where('id','<>',3)->where('id','<>',32)->where('admision','like','S')->orderBy('nombre','ASC')->get();
        }else{
            $rst = Conceptopago::where('tipo','like','E')->where('id','<>',2)->where('id','<>',13)->where('id','<>',24)->where('id','<>',25)->where('id','<>',26)->where('admision','like','S')->orderBy('nombre','ASC')->get();
        }
        $cbo="";
        foreach ($rst as $key => $value) {
            $cbo = $cbo."<option value='".$value->id."'>".$value->nombre."</option>";
        }
         
        return $cbo;
    }
        
    public function generarNumero(Request $request)
    {
        $tipodoc = $request->input("tipodocumento_id");
        $caja_id = $request->input('caja_id');
        //sucursal_id
        $sucursal_id = Session::get('sucursal_id');
        $numero  = Movimiento::NumeroSigue($caja_id, $sucursal_id,2,$tipodoc);
        return $numero;
    }
    
    public function personautocompletar($searching)
    {
        $resultado        = Person::where(DB::raw('CONCAT(apellidopaterno," ",apellidomaterno," ",nombres)'), 'LIKE', '%'.strtoupper($searching).'%')->orWhere('bussinesname', 'LIKE', '%'.strtoupper($searching).'%')->whereNull('deleted_at')->orderBy('apellidopaterno', 'ASC');
        $list      = $resultado->get();
        $data = array();
        foreach ($list as $key => $value) {
            $name = '';
            if ($value->bussinesname != null) {
                $name = $value->bussinesname;
            }else{
                $name = $value->apellidopaterno." ".$value->apellidomaterno." ".$value->nombres;
            }
            $data[] = array(
                            'label' => trim($name),
                            'id'    => $value->id,
                            'value' => trim($name),
                        );
        }
        return json_encode($data);
    }

    public function cierre(Request $request)
    {
        $entidad             = 'Caja';
        $formData            = array('caja.cerrar');
        $listar              = $request->input('listar');
        $caja                = Caja::find($request->input('caja_id'));
        $saldo                = Caja::find($request->input('saldo'));
        //sucursal_id
        $sucursal_id = Session::get('sucursal_id');
        $numero              = Movimiento::NumeroSigue($caja->id, $sucursal_id,2,3);//movimiento caja y documento egreso
        $rst              = Movimiento::where('tipomovimiento_id','=',2)
                            ->where('caja_id','=',$caja->id)->where('conceptopago_id','=',1)
                            ->orderBy('id','DESC')->limit(1)->first();
        if(count($rst)>0){
            $movimiento_mayor = $rst->id;    
        }else{
            $movimiento_mayor = 0;
        }
        
        $resultado        = Movimiento::
                                leftjoin('person as paciente', 'paciente.id', '=', 'movimiento.persona_id')
                            ->join('person as responsable', 'responsable.id', '=', 'movimiento.responsable_id')
                            ->join('conceptopago','conceptopago.id','=','movimiento.conceptopago_id')
                            ->leftjoin('movimiento as m2','movimiento.id','=','m2.movimiento_id')
                            ->where('movimiento.caja_id', '=', $caja->id)
                            ->whereNull('movimiento.cajaapertura_id')
                            ->where('movimiento.id', '>=', $movimiento_mayor)
                            ->where(function($query){
                                $query
                                    ->whereNotIn('movimiento.conceptopago_id',[31])
                                    ->orWhere('m2.situacion','<>','R');
                            });
        $resultado        = $resultado->select('movimiento.*','m2.situacion as situacion2',
                                DB::raw('CONCAT(paciente.apellidopaterno," ",paciente.apellidomaterno," ",paciente.nombres) as paciente'),
                                DB::raw('responsable.nombres as responsable'))
                                ->orderBy('movimiento.id', 'desc');
        $lista            = $resultado->get();
        error_log("CIERRE DE CAJA ID: ".$caja->id);
        error_log("MOV MAYOR: ".$movimiento_mayor);
        error_log("SQL LISTADO MOVIMIENTOS : \n".json_encode($resultado->toSql()));
        error_log("MOVIMIENTOS : \n".json_encode($lista));
        
        $ingreso=0;$egreso=0;$garantia=0;$efectivo=0;$master=0;$visa=0;$pendiente=0;
        foreach($lista as $k=>$v){
            if($v->conceptopago_id<>2 && $v->situacion<>'A'){
                if($v->conceptopago->tipo=="I"){
                    if($v->conceptopago_id<>10){//Garantias
                        if($v->conceptopago_id<>15 && $v->conceptopago_id<>17 && $v->conceptopago_id<>19 && $v->conceptopago_id<>21 && $v->conceptopago_id<>32){
                            $ingreso = $ingreso + $v->total;    
                        }elseif(($v->conceptopago_id==15 || $v->conceptopago_id==17 || $v->conceptopago_id==19 || $v->conceptopago_id==21 || $v->conceptopago_id==32) && $v->situacion=='C'){
                            $ingreso = $ingreso + $v->total;    
                        }else{
                            $pendiente = $pendiente + $v->pendiente;
                        }
                        if($v->tipotarjeta=='VISA'){
                            $visa = $visa + $v->total;
                        }elseif($v->tipotarjeta==''){
                            $efectivo = $efectivo + $v->total;
                        }else{
                            $master = $master + $v->total;
                        }
                    }else{
                        $garantia = $garantia + $v->total;
                    }
                }else{
                    if($v->conceptopago_id<>14 && $v->conceptopago_id<>16 && $v->conceptopago_id<>18 && $v->conceptopago_id<>20 && $v->conceptopago_id<>31){
                        $egreso  = $egreso + $v->total;
                    }elseif(($v->conceptopago_id==14 || $v->conceptopago_id==16 || $v->conceptopago_id==18 || $v->conceptopago_id==20 || $v->conceptopago_id==31) && $v->situacion2=='C'){
                        $egreso  = $egreso + $v->total;
                    }else{
                        $pendiente = $pendiente + $v->pendiente;
                    }
                }
            }
        }
        if($pendiente>0){
            $dat[0]=array("respuesta"=>"ERROR","msg"=>"Transferencia pendiente");
            return json_encode($dat);
        }
        $total               = number_format($ingreso - $egreso - $visa - $master,2,'.',''); 
        //$total = $saldo;
        $formData            = array('route' => $formData, 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton               = 'Cerrar '.$caja->nombre;
        return view($this->folderview.'.cierre')->with(compact('caja', 'formData', 'entidad', 'boton', 'listar', 'numero', 'total'));
    }
    
    public function cerrar(Request $request)
    {
        $reglas     = array(
                'caja_id'                  => 'required',
                );
        $mensajes = array(
            'caja.required'         => 'Debe seleccionar una caja',
            );
        $validacion = Validator::make($request->all(), $reglas, $mensajes);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        }
        $user = Auth::user();
        
        //sucursal_id
        $sucursal_id = Session::get('sucursal_id');

        $error = DB::transaction(function() use($request, $user, $sucursal_id){
            $movimiento        = new Movimiento();
            $movimiento->sucursal_id = $sucursal_id;
            $movimiento->fecha = date("Y-m-d H:i:s");
            $movimiento->numero= $request->input('numero');
            $movimiento->responsable_id=$user->person_id;
            $movimiento->persona_id=$user->person_id;
            $movimiento->subtotal=0;
            $movimiento->igv=0;
            $movimiento->total=str_replace(",","",$request->input('total')); 
            $movimiento->tipomovimiento_id=2;
            $movimiento->tipodocumento_id=2;
            $movimiento->conceptopago_id=2;
            $movimiento->comentario=$request->input('comentario');
            $movimiento->caja_id=$request->input('caja_id');
            $movimiento->situacion='N';
            $movimiento->save();
            error_log("CAJA CERRADA CON EL ID: ".json_encode($movimiento));
            $caja = Caja::find($request->input('caja_id'));
            $caja->estado = "C";
            $caja->save();
        });
        return is_null($error) ? "OK" : $error;
    }
    
    function validarCajaTransferencia(Request $request){
        $rst  = Movimiento::where('tipomovimiento_id','=',2)->where('caja_id','=',$request->input('caja_id'))->orderBy('movimiento.id','DESC')->limit(1)->first();
        if(count($rst)==0){
            $conceptopago_id=2;
        }else{
            $conceptopago_id=$rst->conceptopago_id;
        }
        if($conceptopago_id==2){
            return "Error";
        }else{
            return "OK";
        }
    }

    public function rechazar($id)
    {
        $existe = Libreria::verificarExistencia($id, 'movimiento');
        if ($existe !== true) {
            return $existe;
        }
        $error = DB::transaction(function() use($id){
            $Caja = Movimiento::find($id);
            $Caja->situacion="R";//Rechazado
            if ($Caja->caja_id == 4) {
                $listventas = Movimiento::where('movimientodescarga_id','=',$Caja->id)->get();
                foreach ($listventas as $key => $value) {
                    $value->movimientodescarga_id = null;
                    $value->formapago = 'P';
                    $value->save();
                }
            }else{
                $arr=explode(",",$Caja->listapago);
                for($c=0;$c<count($arr);$c++){
                    $Detalle = Detallemovcaja::find($arr[$c]);
                    if($Caja->conceptopago_id==6){//CAJA
                        $Detalle->situacion='N';//normal;
                    }elseif($Caja->conceptopago_id==17){//SOCIO
                        $Detalle->situacionsocio=null;//null
                        $Detalle->situaciontarjeta=null;//null
                        $Detalle->medicosocio_id=null;//null
                    }elseif($Caja->conceptopago_id==15 || $Caja->conceptopago_id==21){//TARJETA Y BOLETEO TOTAL
                        $Detalle->situaciontarjeta=null;//null
                    }
                    $Detalle->save();
                }
            }
            

            $Caja->save();
        });
        return is_null($error) ? "OK" : $error;
    }

    public function reject($id, $listarLuego)
    {
        $existe = Libreria::verificarExistencia($id, 'movimiento');
        if ($existe !== true) {
            return $existe;
        }
        $listar = "NO";
        if (!is_null(Libreria::obtenerParametro($listarLuego))) {
            $listar = $listarLuego;
        }
        $modelo   = Caja::find($id);
        $entidad  = 'Caja';
        $formData = array('route' => array('caja.rechazar', $id), 'method' => 'Reject', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Rechazar';
        return view('app.confirmar')->with(compact('modelo', 'formData', 'entidad', 'boton', 'listar'));
    }
    
    public function aceptar($id)
    {
        $existe = Libreria::verificarExistencia($id, 'movimiento');
        if ($existe !== true) {
            return $existe;
        }
        $error = DB::transaction(function() use($id){
            $Caja = Movimiento::find($id);
            $Caja->situacion="C";//Aceptado
            $arr=explode(",",$Caja->listapago);
            for($c=0;$c<count($arr);$c++){
                $Detalle = Detallemovcaja::find($arr[$c]);
                if($Caja->conceptopago_id==6){//CAJA
                    $Detalle->situacion='C';//confirmado;
                }elseif($Caja->conceptopago_id==17){//SOCIO
                    $Detalle->situacion='C';//confirmado;
                }elseif($Caja->conceptopago_id==15 || $Caja->conceptopago_id==21){//TARJETA Y BOLETEO TOTAL
                    $Detalle->situacion='C';//confirmado;
                }
                $Detalle->save();
            }
            $Caja->save();
        });
        return is_null($error) ? "OK" : $error;
    }

    public function acept($id, $listarLuego)
    {
        $existe = Libreria::verificarExistencia($id, 'movimiento');
        if ($existe !== true) {
            return $existe;
        }
        $listar = "NO";
        if (!is_null(Libreria::obtenerParametro($listarLuego))) {
            $listar = $listarLuego;
        }
        $modelo   = Caja::find($id);
        $entidad  = 'Caja';
        $formData = array('route' => array('caja.aceptar', $id), 'method' => 'Acept', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Aceptar';
        return view('app.confirmar')->with(compact('modelo', 'formData', 'entidad', 'boton', 'listar'));
    }

    public function descarga(Request $request)
    {
        $entidad          = 'Venta';
        $title            = $this->tituloAdmin;
        $titulo_registrar = $this->tituloRegistrar;
        $ruta             = $this->rutas;
        $request->session()->forget('carritoventa');
        $movimiento_id = Libreria::getParam($request->input('movimiento_id'));
        return view($this->folderview.'.adminDescarga')->with(compact('entidad', 'title', 'titulo_registrar', 'ruta','movimiento_id'));
    }

    public function listardescarga(Request $request)
    {
        $pagina           = $request->input('page');
        $filas            = $request->input('filas');
        $entidad          = 'Venta';
        $fechainicio             = Libreria::getParam($request->input('fechainicio'));
        $fechafin             = Libreria::getParam($request->input('fechafin'));
        $numero             = Libreria::getParam($request->input('numero'));
        $movimiento_id = Libreria::getParam($request->input('movimiento_id'));
        $resultado        = Venta::where('tipomovimiento_id', '=', '4')->where('ventafarmacia','=','S')->where('movimientodescarga_id','=',$movimiento_id)->where(function($query) use ($numero){   
                                if (!is_null($numero) && $numero !== '') {
                                   
                                    $query->where('numero', '=', $numero);
                                }
                            })->where(function($query) use ($fechainicio,$fechafin){   
                                if (!is_null($fechainicio) && $fechainicio !== '') {
                                    $begindate   = Date::createFromFormat('d/m/Y', $fechainicio)->format('Y-m-d');
                                    $query->where('fecha', '>=', $begindate);
                                }
                                if (!is_null($fechafin) && $fechafin !== '') {
                                    $enddate   = Date::createFromFormat('d/m/Y', $fechafin)->format('Y-m-d');
                                    $query->where('fecha', '<=', $enddate);
                                }
                            })->orderBy('id','DESC');
        $lista            = $resultado->get();
        $cabecera         = array();
        $cabecera[]       = array('valor' => '#', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Fecha', 'numero' => '1');
        //$cabecera[]       = array('valor' => 'Proveedor', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Boleta', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Paciente', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Estado', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Total', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Operaciones', 'numero' => '3');
        
        $titulo_modificar = $this->tituloModificar;
        $titulo_eliminar  = $this->tituloEliminar;
        $titulo_ver  = 'Ver Venta';
        $ruta             = $this->rutas;
        $list = array();
        if ($request->session()->get('carritoventa') !== null) {
            $list = $request->session()->get('carritoventa');
            
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
            return view($this->folderview.'.listDescarga')->with(compact('lista', 'paginacion', 'inicio', 'fin', 'entidad', 'cabecera', 'titulo_modificar', 'titulo_eliminar', 'titulo_ver', 'ruta','list'));
        }
        return view($this->folderview.'.listDescarga')->with(compact('lista', 'entidad'));
    }

    public function descargaadmision(Request $request)
    {
        $entidad          = 'Venta';
        $title            = $this->tituloAdmin;
        $titulo_registrar = $this->tituloRegistrar;
        $ruta             = $this->rutas;
        $request->session()->forget('carritoventa');
        $movimiento_id = Libreria::getParam($request->input('movimiento_id'));
        return view($this->folderview.'.adminDescargaadmision')->with(compact('entidad', 'title', 'titulo_registrar', 'ruta','movimiento_id'));
    }

    public function listardescargaadmision(Request $request)
    {
        $pagina           = $request->input('page');
        $filas            = $request->input('filas');
        $entidad          = 'Venta';
        $fechainicio             = Libreria::getParam($request->input('fechainicio'));
        $fechafin             = Libreria::getParam($request->input('fechafin'));
        $numero             = Libreria::getParam($request->input('numero'));//quite momentaneamente ->where('formapago','=','P')
        $resultado        = Venta::where('tipomovimiento_id', '=', '4')
                            ->where('ventafarmacia','=','S')
                            ->where('formapago','=','P')
                            ->where('situacion','<>','U')
                            ->where('situacion','<>','A')
                            ->where(function($query) use ($numero){   
                                if (!is_null($numero) && $numero !== '') {
                                   
                                    $query->where('numero', '=', $numero);
                                }
                            })->where(function($query) use ($fechainicio,$fechafin){   
                                if (!is_null($fechainicio) && $fechainicio !== '') {
                                    $begindate   = Date::createFromFormat('d/m/Y', $fechainicio)->format('Y-m-d');
                                    $query->where('fecha', '>=', $begindate);
                                }
                                if (!is_null($fechafin) && $fechafin !== '') {
                                    $enddate   = Date::createFromFormat('d/m/Y', $fechafin)->format('Y-m-d');
                                    $query->where('fecha', '<=', $enddate);
                                }
                            })->orderBy('id','DESC');
        $lista            = $resultado->get();
        //dd($lista);
        $cabecera         = array();
        $cabecera[]       = array('valor' => '#', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Fecha', 'numero' => '1');
        //$cabecera[]       = array('valor' => 'Proveedor', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Boleta', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Paciente', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Estado', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Total', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Operaciones', 'numero' => '3');
        
        $titulo_modificar = $this->tituloModificar;
        $titulo_eliminar  = $this->tituloEliminar;
        $titulo_ver  = 'Ver Venta';
        $ruta             = $this->rutas;
        $list = array();
        if ($request->session()->get('carritoventa') !== null) {
            $list = $request->session()->get('carritoventa');
            
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
            return view($this->folderview.'.listDescargaadmision')->with(compact('lista', 'paginacion', 'inicio', 'fin', 'entidad', 'cabecera', 'titulo_modificar', 'titulo_eliminar', 'titulo_ver', 'ruta','list'));
        }
        return view($this->folderview.'.listDescargaadmision')->with(compact('lista', 'entidad'));
    }

    public function agregardescarga(Request $request)
    {
        $lista = array();
        $monto = 0;
        if ($request->session()->get('carritoventa') !== null) {
            $lista = $request->session()->get('carritoventa');
            $venta_id = Libreria::getParam($request->input('venta_id'));
            $venta = Movimiento::find($venta_id);
            $estaPresente   = false;
            $indicepresente = '';
            for ($i=0; $i < count($lista); $i++) { 
                if ($lista[$i]['venta_id'] == $venta_id) {
                    $estaPresente   = true;
                    $indicepresente = $i;
                }
            }
            if ($estaPresente === true) {
                $lista[$indicepresente]  = array('venta_id' => $venta_id, 'monto' => $venta->total);
            }else{
                $lista[]  = array('venta_id' => $venta_id, 'monto' => $venta->total);
            }
            $request->session()->put('carritoventa', $lista);
        }else{
            $venta_id = Libreria::getParam($request->input('venta_id'));
            $venta = Movimiento::find($venta_id);
            $lista[]  = array('venta_id' => $venta_id, 'monto' => $venta->total);
            $request->session()->put('carritoventa', $lista);
            //echo count($lista);

        }

        for ($i=0; $i < count($lista); $i++) { 
            $monto = $monto + $lista[$i]['monto'];
        }

        return $monto;
    }

    public function quitardescarga(Request $request)
    {

        $monto = 0;
        if ($request->session()->get('carritoventa') !== null) {
            $venta_id       = $request->input('venta_id');
            $cantidad = count($request->session()->get('carritoventa'));
            $lista2   = $request->session()->get('carritoventa');
            $lista    = array();
            for ($i=0; $i < $cantidad; $i++) {
                if ($lista2[$i]['venta_id'] != $venta_id) {
                    $lista[] = $lista2[$i];
                }else{
                    $venta_id = $lista2[$i]['venta_id'];
                }
            }
            $request->session()->put('carritoventa', $lista);
            for ($i=0; $i < count($lista); $i++) { 
                $monto = $monto + $lista[$i]['monto'];
            }
        }

        return $monto;
    }

    public function guardardescarga(Request $request)
    {
        $movimiento_id  = $request->input('movimiento_id');
        $existe = Libreria::verificarExistencia($movimiento_id, 'movimiento');
        if ($existe !== true) {
            return $existe;
        }
        $error = DB::transaction(function() use($request,$movimiento_id){
            $Caja = Movimiento::find($movimiento_id);
            $Caja->situacion="C";//Aceptado
            /*$arr=explode(",",$Caja->listapago);
            for($c=0;$c<count($arr);$c++){
                $Detalle = Detallemovcaja::find($arr[$c]);
                if($Caja->conceptopago_id==6){//CAJA
                    $Detalle->situacion='C';//confirmado;
                }elseif($Caja->conceptopago_id==17){//SOCIO
                    $Detalle->situacionsocio='C';//confirmado;
                }elseif($Caja->conceptopago_id==15 || $Caja->conceptopago_id==21){//TARJETA Y BOLETEO TOTAL
                    $Detalle->situaciontarjeta='C';//confirmado;
                }
                $Detalle->save();
            }*/
            $Caja->save();
            /*if ($request->session()->get('carritoventa') !== null) {
                $lista = $request->session()->get('carritoventa');
                for ($i=0; $i < count($lista) ; $i++) { 
                    $venta = Movimiento::find($lista[$i]['venta_id']);
                    $venta->formapago = 'C';
                    $venta->save();
                }
            }*/
        });
        return is_null($error) ? "OK" : $error;
    }
    
	public function pdfRecibo(Request $request){
        $lista = Movimiento::where('id','=',$request->input('id'))->first();
                    
        $pdf = new TCPDF();
        $pdf::SetTitle('Recibo de '.($lista->conceptopago->tipo=="I"?"Ingreso":"Egreso"));
        $pdf::AddPage();
        if($lista->conceptopago_id==10){//GARANTIAS
            $pdf::SetFont('helvetica','B',10);
            $pdf::Image("http://localhost/juanpablo/dist/img/logo.jpg", 0, 0, 35, 10);
            $pdf::Image("http://localhost/juanpablo/dist/img/logo.jpg", 105, 0, 35, 10);
            $pdf::Cell(50,10,utf8_decode("Recibo de ".($lista->conceptopago->tipo=="I"?"Ingreso":"Egreso")." Nro. ".$lista->numero),0,0,'C');
            $pdf::Cell(50,7,utf8_decode(""),0,0,'L');
            $pdf::Cell(50,10,utf8_decode("Recibo de ".($lista->conceptopago->tipo=="I"?"Ingreso":"Egreso")." Nro. ".$lista->numero),0,0,'C');
            $pdf::Ln();
            $pdf::SetFont('helvetica','B',9);
            $pdf::Cell(18,7,utf8_decode("FECHA :"),0,0,'L');
            $pdf::SetFont('helvetica','',9);
            $pdf::Cell(32,7,utf8_decode($lista->fecha),0,0,'L');
            $pdf::Cell(50,7,utf8_decode(""),0,0,'L');
            $pdf::SetFont('helvetica','B',9);
            $pdf::Cell(18,7,utf8_decode("FECHA :"),0,0,'L');
            $pdf::SetFont('helvetica','',9);
            $pdf::Cell(40,7,utf8_decode($lista->fecha),0,0,'L');
            $pdf::Ln();
            $pdf::SetFont('helvetica','B',9);
            $pdf::Cell(20,7,utf8_decode("CONCEPTO :"),0,0,'L');
            $pdf::SetFont('helvetica','',9);
            $pdf::Cell(30,7,utf8_decode($lista->conceptopago->nombre),0,0,'L');
            $pdf::Cell(50,7,utf8_decode(""),0,0,'L');
            $pdf::SetFont('helvetica','B',9);
            $pdf::Cell(20,7,utf8_decode("CONCEPTO :"),0,0,'L');
            $pdf::SetFont('helvetica','',9);
            $pdf::Cell(30,7,utf8_decode($lista->conceptopago->nombre),0,0,'L');
            $pdf::Ln();
            $pdf::SetFont('helvetica','B',9);
            $pdf::Cell(20,7,utf8_decode("PACIENTE :"),0,0,'L');
            $pdf::SetFont('helvetica','',9);
            $pdf::Cell(80,7,($lista->persona->apellidopaterno." ".$lista->persona->apellidomaterno." ".$lista->persona->nombres),0,0,'L');
            $pdf::SetFont('helvetica','B',9);
            $pdf::Cell(20,7,utf8_decode("PACIENTE :"),0,0,'L');
            $pdf::SetFont('helvetica','',9);
            $pdf::Cell(30,7,($lista->persona->apellidopaterno." ".$lista->persona->apellidomaterno." ".$lista->persona->nombres),0,0,'L');
            $pdf::Ln();
            if($lista->doctor_id>0){
                $pdf::SetFont('helvetica','B',9);
                $pdf::Cell(20,7,utf8_decode("DOCTOR :"),0,0,'L');
                $pdf::SetFont('helvetica','',9);
                $pdf::Cell(80,7,($lista->doctor->apellidopaterno." ".$lista->doctor->apellidomaterno." ".$lista->doctor->nombres),0,0,'L');
                $pdf::SetFont('helvetica','B',9);
                $pdf::Cell(20,7,utf8_decode("DOCTOR :"),0,0,'L');
                $pdf::SetFont('helvetica','',9);
                $pdf::Cell(30,7,($lista->doctor->apellidopaterno." ".$lista->doctor->apellidomaterno." ".$lista->doctor->nombres),0,0,'L');
                $pdf::Ln();
            }
            $pdf::SetFont('helvetica','B',9);
            $pdf::Cell(20,7,utf8_decode("TOTAL :"),0,0,'L');
            $pdf::SetFont('helvetica','',9);
            $pdf::Cell(80,7,number_format($lista->total,2,'.',''),0,0,'L');
            $pdf::SetFont('helvetica','B',9);
            $pdf::Cell(20,7,utf8_decode("TOTAL :"),0,0,'L');
            $pdf::SetFont('helvetica','',9);
            $pdf::Cell(80,7,number_format($lista->total,2,'.',''),0,0,'L');
            $pdf::Ln();
            $pdf::SetFont('helvetica','B',9);
            $pdf::Cell(25,7,utf8_decode("COMENTARIO :"),0,0,'L');
            $pdf::SetFont('helvetica','',9);
            $pdf::Cell(75,7,utf8_decode($lista->comentario),0,0,'L');
            $pdf::SetFont('helvetica','B',9);
            $pdf::Cell(25,7,utf8_decode("COMENTARIO :"),0,0,'L');
            $pdf::SetFont('helvetica','',9);
            $pdf::Cell(80,7,utf8_decode($lista->comentario),0,0,'L');
            $pdf::Ln();
            $pdf::SetFont('helvetica','B',9);
            $pdf::Cell(20,7,utf8_decode("USUARIO :"),0,0,'L');
            $pdf::SetFont('helvetica','',9);
            $pdf::Cell(80,7,($lista->responsable->nombres),0,0,'L');
            $pdf::SetFont('helvetica','B',9);
            $pdf::Cell(20,7,utf8_decode("USUARIO :"),0,0,'L');
            $pdf::SetFont('helvetica','',9);
            $pdf::Cell(80,7,($lista->responsable->nombres),0,0,'L');
            
        }elseif($lista->conceptopago_id==8 || $lista->conceptopago_id==45){//HONORARIOS MEDICOS
            $pdf::SetFont('helvetica','B',10);
            $pdf::Image("http://localhost/juanpablo/dist/img/logo.jpg", 0, 0, 35, 10);
            $pdf::Image("http://localhost/juanpablo/dist/img/logo.jpg", 105, 0, 35, 10);
            $pdf::Cell(50,10,utf8_decode("Recibo Medico Nro. ".$lista->numero),0,0,'C');
            $pdf::Cell(50,7,utf8_decode(""),0,0,'L');
            $pdf::Cell(50,10,utf8_decode("Recibo Medico Nro. ".$lista->numero),0,0,'C');
            $pdf::Ln();
            $pdf::SetFont('helvetica','B',9);
            $pdf::Cell(20,7,utf8_decode("FECHA :"),0,0,'L');
            $pdf::SetFont('helvetica','',9);
            $pdf::Cell(30,7,utf8_decode($lista->fecha),0,0,'L');
            $pdf::Cell(50,7,utf8_decode(""),0,0,'L');
            $pdf::SetFont('helvetica','B',9);
            $pdf::Cell(20,7,utf8_decode("FECHA :"),0,0,'L');
            $pdf::SetFont('helvetica','',9);
            $pdf::Cell(40,7,utf8_decode($lista->fecha),0,0,'L');
            $pdf::Ln();
            $pdf::SetFont('helvetica','B',9);
            $pdf::Cell(20,7,utf8_decode("DOCTOR :"),0,0,'L');
            $pdf::SetFont('helvetica','',9);
            $pdf::Cell(80,7,($lista->persona->apellidopaterno." ".$lista->persona->apellidomaterno." ".$lista->persona->nombres),0,0,'L');
            $pdf::SetFont('helvetica','B',9);
            $pdf::Cell(20,7,utf8_decode("DOCTOR :"),0,0,'L');
            $pdf::SetFont('helvetica','',9);
            $pdf::Cell(30,7,($lista->persona->apellidopaterno." ".$lista->persona->apellidomaterno." ".$lista->persona->nombres),0,0,'L');
            $pdf::Ln();
            $list=explode(",",$lista->listapago);
            for($c=0;$c<count($list);$c++){
                $detalle = Detallemovcaja::find($list[$c]);
                $movimiento = Movimiento::where("movimiento_id","=",$detalle->movimiento->id)->first();
                $pdf::SetFont('helvetica','B',9);
                $pdf::Cell(20,7,utf8_decode("PACIENTE :"),0,0,'L');
                $pdf::SetFont('helvetica','',9);
                $pdf::Cell(80,7,($movimiento->persona->apellidopaterno." ".$movimiento->persona->apellidomaterno." ".$movimiento->persona->nombres),0,0,'L');
                $pdf::SetFont('helvetica','B',9);
                $pdf::Cell(20,7,utf8_decode("PACIENTE :"),0,0,'L');
                $pdf::SetFont('helvetica','',9);
                $pdf::Cell(30,7,($movimiento->persona->apellidopaterno." ".$movimiento->persona->apellidomaterno." ".$movimiento->persona->nombres),0,0,'L');
                $pdf::Ln();                
                $pdf::SetFont('helvetica','B',9);
                $pdf::Cell(20,7,utf8_decode("SERVICIO :"),0,0,'L');
                $pdf::SetFont('helvetica','',9);
                if($detalle->servicio_id>0){
                    $pdf::Cell(80,7,$detalle->servicio->nombre,0,0,'L');
                }else{
                    $pdf::Cell(80,7,$detalle->descripcion,0,0,'L');
                }
                $pdf::SetFont('helvetica','B',9);
                $pdf::Cell(20,7,utf8_decode("SERVICIO :"),0,0,'L');
                $pdf::SetFont('helvetica','',9);
                if($detalle->servicio_id>0){
                    $pdf::Cell(30,7,$detalle->servicio->nombre,0,0,'L');
                }else{
                    $pdf::Cell(30,7,$detalle->descripcion,0,0,'L');
                }
                $pdf::Ln();                
            }  
           
            $pdf::SetFont('helvetica','B',9);
            $pdf::Cell(20,7,utf8_decode("TOTAL :"),0,0,'L');
            $pdf::SetFont('helvetica','',9);
            $pdf::Cell(80,7,number_format($lista->total,2,'.',''),0,0,'L');
            $pdf::SetFont('helvetica','B',9);
            $pdf::Cell(20,7,utf8_decode("TOTAL :"),0,0,'L');
            $pdf::SetFont('helvetica','',9);
            $pdf::Cell(80,7,number_format($lista->total,2,'.',''),0,0,'L');
            $pdf::Ln();
            $pdf::SetFont('helvetica','B',9);
            $pdf::Cell(20,7,utf8_decode("USUARIO :"),0,0,'L');
            $pdf::SetFont('helvetica','',9);
            $pdf::Cell(80,7,($lista->responsable->nombres),0,0,'L');
            $pdf::SetFont('helvetica','B',9);
            $pdf::Cell(20,7,utf8_decode("USUARIO :"),0,0,'L');
            $pdf::SetFont('helvetica','',9);
            $pdf::Cell(80,7,($lista->responsable->nombres),0,0,'L');
            
        }else{
            $pdf::SetFont('helvetica','B',12);
            $pdf::Image("http://localhost/juanpablo/dist/img/logo.jpg", 0, 0, 70, 20);
            $pdf::Cell(0,10,utf8_decode("Recibo de ".($lista->conceptopago->tipo=="I"?"Ingreso":"Egreso")." Nro. ".$lista->numero),0,0,'C');
            $pdf::Ln();
            $pdf::SetFont('helvetica','B',9);
            $pdf::Cell(18,7,utf8_decode("FECHA :"),0,0,'L');
            $pdf::SetFont('helvetica','',9);
            $pdf::Cell(80,7,utf8_decode($lista->fecha),0,0,'L');
            $pdf::SetFont('helvetica','B',9);
            $pdf::Cell(25,7,utf8_decode("CONCEPTO :"),0,0,'L');
            $pdf::SetFont('helvetica','',9);
            $pdf::Cell(40,7,utf8_decode($lista->conceptopago->nombre),0,0,'L');
            $pdf::SetFont('helvetica','B',9);
            $pdf::Ln();
            $pdf::Cell(18,7,utf8_decode("PERSONA :"),0,0,'L');
            $pdf::SetFont('helvetica','',9);
            $pdf::Cell(80,7,($lista->persona->apellidopaterno." ".$lista->persona->apellidomaterno." ".$lista->persona->nombres),0,0,'L');
            $pdf::Ln();
            if($lista->conceptopago->id=="6" || $lista->conceptopago->id=="7" || $lista->conceptopago->id=="8"){
                $pdf::SetFont('helvetica','B',8);
                $pdf::Cell(23,7,utf8_decode("DOC. VENTA"),1,0,'C');
                $pdf::Cell(75,7,utf8_decode("PACIENTE"),1,0,'C');
                $pdf::Cell(75,7,utf8_decode("SERVICIO"),1,0,'C');
                $pdf::Cell(20,7,utf8_decode("TOTAL"),1,0,'C');
                $pdf::Ln();
                $pdf::SetFont('helvetica','',7);
                $list=explode(",",$lista->listapago);
                for($c=0;$c<count($list);$c++){
                    $detalle = Detallemovcaja::find($list[$c]);
                    $movimiento = Movimiento::where("movimiento_id","=",$detalle->movimiento->id)->first();
                    if($movimiento->tipodocumento_id=="4") $abr="F";else $abr="B";
                    $pdf::Cell(23,7,utf8_decode($abr.$movimiento->serie."-".$movimiento->numero),1,0,'C');
                    $pdf::Cell(75,7,($movimiento->persona->apellidopaterno." ".$movimiento->persona->apellidomaterno." ".$movimiento->persona->nombres),1,0,'C');
                    $pdf::Cell(75,7,utf8_decode($detalle->servicio->nombre),1,0,'C');
                    $pdf::Cell(20,7,number_format($detalle->pagodoctor,2,'.',''),1,0,'C');
                    $pdf::Ln();                
                }    
            }
            $pdf::SetFont('helvetica','B',9);
            $pdf::Cell(25,7,utf8_decode("TOTAL :"),0,0,'L');
            $pdf::SetFont('helvetica','',9);
            $pdf::Cell(40,7,number_format($lista->total,2,'.',''),0,0,'L');
            $pdf::Ln();
            $pdf::SetFont('helvetica','B',9);
            $pdf::Cell(25,7,utf8_decode("COMENTARIO :"),0,0,'L');
            $pdf::SetFont('helvetica','',9);
            $pdf::Cell(0,7,utf8_decode($lista->comentario),0,0,'L');
            $pdf::Ln();
            $pdf::SetFont('helvetica','B',9);
            $pdf::Cell(25,7,utf8_decode("USUARIO :"),0,0,'L');
            $pdf::SetFont('helvetica','',9);
            $pdf::Cell(80,7,($lista->responsable->apellidopaterno." ".$lista->responsable->apellidomaterno." ".$lista->responsable->nombres),0,0,'L');
            $pdf::Ln();
        }
        $pdf::Output('ReciboCaja.pdf');
        
    }

    public function pdfHonorario(Request $request){
        $caja_id          = Libreria::getParam($request->input('caja_id'),'1');
        $caja                = Caja::find($request->input('caja_id'));
        $rst  = Movimiento::where('tipomovimiento_id','=',2)->where('caja_id','=',$caja_id)->where('situacion','<>','A')->orderBy('movimiento.id','DESC')->limit(1)->first();
        if(count($rst)==0){
            $conceptopago_id=2;
        }else{
            $conceptopago_id=$rst->conceptopago_id;
        }
        
        $rst              = Movimiento::where('tipomovimiento_id','=',2)->where('caja_id','=',$caja_id)->where('conceptopago_id','=',1)->orderBy('id','DESC')->limit(1)->first();
        if(count($rst)>0){
            $movimiento_mayor = $rst->id;    
        }else{
            $movimiento_mayor = 0;
        }

        //sucursal_id
        $sucursal_id = Session::get('sucursal_id');
        
        $resultado        = Movimiento::leftjoin('person as paciente', 'paciente.id', '=', 'movimiento.persona_id')
                            ->join('person as responsable', 'responsable.id', '=', 'movimiento.responsable_id')
                            ->join('conceptopago','conceptopago.id','=','movimiento.conceptopago_id')
                            ->leftjoin('movimiento as m2','m2.movimiento_id','=','movimiento.id')
                            ->where('movimiento.caja_id', '=', $caja_id)
                            ->where('movimiento.sucursal_id','=',$sucursal_id)
                            ->where('movimiento.id', '>=', $movimiento_mayor)
                            ->where('movimiento.situacion','<>','A')
                            ->whereNull('movimiento.cajaapertura_id')
                            ->whereIn('movimiento.conceptopago_id', [8]);
        $resultado        = $resultado->select('movimiento.*','m2.situacion as situacion2')->orderBy('paciente.apellidopaterno', 'asc');
        $lista            = $resultado->get();

        $resultado2        = Movimiento::leftjoin('person as paciente', 'paciente.id', '=', 'movimiento.persona_id')
                            ->join('person as responsable', 'responsable.id', '=', 'movimiento.responsable_id')
                            ->join('conceptopago','conceptopago.id','=','movimiento.conceptopago_id')
                            ->leftjoin('movimiento as m2','m2.movimiento_id','=','movimiento.id')
                            ->where('movimiento.caja_id', '=', $caja_id)
                            ->where('movimiento.sucursal_id','=',$sucursal_id)
                            ->where('movimiento.id', '>=', $movimiento_mayor)
                            ->where('movimiento.conceptopago_id', '=', 10)
                            ->where('movimiento.situacion', '<>', 'A');
        $resultado2        = $resultado2->select('movimiento.*','m2.situacion as situacion2')->orderBy('paciente.apellidopaterno', 'asc');
        $lista2            = $resultado2->get();

        $pdf = new TCPDF();
        $pdf::SetTitle('Honorarios y Garantias del '.($rst->fecha));
        if (count($lista) > 0 || count($lista2) > 0) {            
            $pdf::AddPage();
            $pdf::SetFont('helvetica','B',12);
            $pdf::Image("http://localhost/juanpablo/dist/img/logo.jpg", 0, 0, 70, 14);
            $pdf::Cell(0,10,utf8_decode("Honorarios Medicos del ".date("d/m/Y",strtotime($rst->fecha))." - ".$caja->nombre),0,0,'C');
            $pdf::Ln();
            $pdf::SetFont('helvetica','B',8);
            $pdf::Cell(20,6,utf8_decode("DOC. VENTA"),1,0,'C');
            $pdf::Cell(75,6,utf8_decode("PACIENTE"),1,0,'C');
            $pdf::Cell(75,6,utf8_decode("SERVICIO"),1,0,'C');
            $pdf::Cell(20,6,utf8_decode("SUBTOTAL"),1,0,'C');
            $pdf::Ln();
            $doctor="";$total=0;$totalgeneral=0;
            foreach ($lista as $key => $value){
                if($doctor!=($value->persona->apellidopaterno." ".$value->persona->apellidomaterno." ".$value->persona->nombres)){
                    $pdf::SetFont('helvetica','B',8);
                    if($doctor!=""){
                        $pdf::Cell(170,6,("TOTAL :"),1,0,'R');
                        $pdf::Cell(20,6,number_format($total,2,'.',''),1,0,'C');
                        $totalgeneral = $totalgeneral + $total;
                        $total=0;
                        $pdf::Ln();
                    }
                    $doctor=($value->persona->apellidopaterno." ".$value->persona->apellidomaterno." ".$value->persona->nombres);
                    $pdf::Cell(190,6,($doctor),1,0,'L');
                    $pdf::Ln();
                }
                $pdf::SetFont('helvetica','',7);
                $list=explode(",",$value->listapago);
                for($c=0;$c<count($list);$c++){
                    $detalle = Detallemovcaja::find($list[$c]);
                    $movimiento = Movimiento::where("movimiento_id","=",$detalle->movimiento->id)->first();
                    if($movimiento->tipodocumento_id=="4") $abr="F";else $abr="B";
                    $pdf::Cell(20,6,utf8_decode($abr.$movimiento->serie."-".$movimiento->numero),1,0,'C');
                    $pdf::Cell(75,6,($movimiento->persona->apellidopaterno." ".$movimiento->persona->apellidomaterno." ".$movimiento->persona->nombres),1,0,'L');
                    if($detalle->servicio_id>0){
                        $pdf::Cell(75,6,utf8_decode($detalle->servicio->nombre),1,0,'L');
                    }else{
                        $pdf::Cell(75,6,utf8_decode($detalle->descripcion),1,0,'L');
                    }
                    $pdf::Cell(20,6,number_format($detalle->pagodoctor*$detalle->cantidad,2,'.',''),1,0,'C');
                    $total=$total + $detalle->pagodoctor*$detalle->cantidad;
                    $pdf::Ln();                
                }    
            }
            $totalgeneral = $totalgeneral + $total;
            $pdf::SetFont('helvetica','B',8);
            $pdf::Cell(170,6,("TOTAL :"),1,0,'R');
            $pdf::Cell(20,6,number_format($total,2,'.',''),1,0,'C');
            $pdf::Ln();
            $pdf::SetFont('helvetica','B',9);
            $pdf::Cell(170,6,("TOTAL HONORARIOS :"),1,0,'R');
            $pdf::Cell(20,6,number_format($totalgeneral,2,'.',''),1,0,'C');
            $pdf::Ln();

            $resultado        = Movimiento::leftjoin('person as paciente', 'paciente.id', '=', 'movimiento.persona_id')
                                        ->join('person as responsable', 'responsable.id', '=', 'movimiento.responsable_id')
                                        ->join('conceptopago','conceptopago.id','=','movimiento.conceptopago_id')
                                        ->leftjoin('movimiento as m2','m2.movimiento_id','=','movimiento.id')
                                        ->where('movimiento.caja_id', '=', $caja_id)
                                        ->where('movimiento.sucursal_id','=',$sucursal_id)
                                        ->where('movimiento.id', '>=', $movimiento_mayor)
                                        ->where('movimiento.situacion','<>','A')
                                        ->whereNull('movimiento.cajaapertura_id')
                                        ->whereIn('movimiento.conceptopago_id', [45]);
            $resultado        = $resultado->select('movimiento.*','m2.situacion as situacion2')->orderBy('paciente.apellidopaterno', 'asc');
            $lista            = $resultado->get();
            $pdf::SetFont('helvetica','B',12);
            $pdf::Cell(0,10,utf8_decode("Honorarios Medicos - Tarjeta del ".date("d/m/Y",strtotime($rst->fecha))." - ".$caja->nombre),0,0,'C');
            $pdf::Ln();
            $pdf::SetFont('helvetica','B',8);
            $pdf::Cell(20,6,utf8_decode("DOC. VENTA"),1,0,'C');
            $pdf::Cell(75,6,utf8_decode("PACIENTE"),1,0,'C');
            $pdf::Cell(75,6,utf8_decode("SERVICIO"),1,0,'C');
            $pdf::Cell(20,6,utf8_decode("SUBTOTAL"),1,0,'C');
            $pdf::Ln();
            $doctor="";$total=0;$totalgeneral=0;
            foreach ($lista as $key => $value){
                if($doctor!=($value->persona->apellidopaterno." ".$value->persona->apellidomaterno." ".$value->persona->nombres)){
                    $pdf::SetFont('helvetica','B',8);
                    if($doctor!=""){
                        $pdf::Cell(170,6,("TOTAL :"),1,0,'R');
                        $pdf::Cell(20,6,number_format($total,2,'.',''),1,0,'C');
                        $totalgeneral = $totalgeneral + $total;
                        $total=0;
                        $pdf::Ln();
                    }
                    $doctor=($value->persona->apellidopaterno." ".$value->persona->apellidomaterno." ".$value->persona->nombres);
                    $pdf::Cell(190,6,($doctor),1,0,'L');
                    $pdf::Ln();
                }
                $pdf::SetFont('helvetica','',7);
                $list=explode(",",$value->listapago);
                for($c=0;$c<count($list);$c++){
                    $detalle = Detallemovcaja::find($list[$c]);
                    $movimiento = Movimiento::where("movimiento_id","=",$detalle->movimiento->id)->first();
                    if($movimiento->tipodocumento_id=="4") $abr="F";else $abr="B";
                    $pdf::Cell(20,6,utf8_decode($abr.$movimiento->serie."-".$movimiento->numero),1,0,'C');
                    $pdf::Cell(75,6,($movimiento->persona->apellidopaterno." ".$movimiento->persona->apellidomaterno." ".$movimiento->persona->nombres),1,0,'L');
                    if($detalle->servicio_id>0){
                        $pdf::Cell(75,6,utf8_decode($detalle->servicio->nombre),1,0,'L');
                    }else{
                        $pdf::Cell(75,6,utf8_decode($detalle->descripcion),1,0,'L');
                    }
                    $pdf::Cell(20,6,number_format($detalle->pagodoctor*$detalle->cantidad,2,'.',''),1,0,'C');
                    $total=$total + $detalle->pagodoctor*$detalle->cantidad;
                    $pdf::Ln();                
                }    
            }
            $totalgeneral = $totalgeneral + $total;
            $pdf::SetFont('helvetica','B',8);
            $pdf::Cell(170,6,("TOTAL :"),1,0,'R');
            $pdf::Cell(20,6,number_format($total,2,'.',''),1,0,'C');
            $pdf::Ln();
            $pdf::SetFont('helvetica','B',9);
            $pdf::Cell(170,6,("TOTAL HONORARIOS :"),1,0,'R');
            $pdf::Cell(20,6,number_format($totalgeneral,2,'.',''),1,0,'C');
            $pdf::Ln();


            $pdf::Ln();
            $pdf::SetFont('helvetica','B',12);
            $pdf::Cell(0,10,utf8_decode("Garantias de Medicos del ".date("d/m/Y",strtotime($rst->fecha))." - ".$caja->nombre),0,0,'C');
            $pdf::Ln();
            $pdf::SetFont('helvetica','B',8);
            $pdf::Cell(85,6,utf8_decode("MEDICO"),1,0,'C');
            $pdf::Cell(85,6,utf8_decode("PACIENTE"),1,0,'C');
            $pdf::Cell(20,6,utf8_decode("SUBTOTAL"),1,0,'C');
            $pdf::Ln();
            $total=0;
            $pdf::SetFont('helvetica','',8);
            foreach ($lista2 as $key => $value){
                if(isset($value->doctor)){
                    $pdf::Cell(85,6,utf8_decode($value->doctor->apellidopaterno." ".$value->doctor->apellidomaterno." ".$value->doctor->nombres),1,0,'L');
                } else {
                    $pdf::Cell(85,6,"ERROR",1,0,'L');
                }
                
                $pdf::Cell(85,6,($value->persona->apellidopaterno." ".$value->persona->apellidomaterno." ".$value->persona->nombres),1,0,'L');
                $pdf::Cell(20,6,number_format($value->total,2,'.',''),1,0,'C');
                $pdf::Ln();
                $total=$total + $value->total;                
            }
            $pdf::SetFont('helvetica','B',9);
            $pdf::Cell(170,6,("TOTAL GARANTIAS :"),1,0,'R');
            $pdf::Cell(20,6,number_format($total,2,'.',''),1,0,'C');
            $pdf::Ln();
            $pdf::SetFont('helvetica','B',9);
            $pdf::Cell(20,6,utf8_decode("USUARIO :"),0,0,'L');
            $pdf::SetFont('helvetica','',9);
            $user = Auth::user();
            $pdf::Cell(80,6,($user->person->nombres),0,0,'L');
            $pdf::Ln();
        }
        $pdf::Output('ReporteHonorario.pdf');
    }

    public function pdfHonorarioF(Request $request){
        $caja_id          = Libreria::getParam($request->input('caja_id'),'1');
        $fecha          = $request->input('fecha');
        $caja                = Caja::find($request->input('caja_id'));
        
        ////////////////////////////////////////////////////////////////////

        $rst  = Movimiento::where('tipomovimiento_id','=',2)->where('caja_id','=',$caja_id)->where('situacion','<>','A')->where('fecha','=',$fecha)->orderBy('movimiento.id','ASC')->limit(1)->first();
        if(count($rst)==0){
            $conceptopago_id=2;
        }else{
            $conceptopago_id=$rst->conceptopago_id;
        }
        
        $rst              = Movimiento::where('tipomovimiento_id','=',2)->where('caja_id','=',$caja_id)->where('conceptopago_id','=',1)->where('fecha','=',$fecha)->orderBy('id','ASC')->limit(1)->first();
        if(count($rst)>0){
            $movimiento_mayor = $rst->id;  
            $responsable = $rst->responsable->nombres;
        }else{
            $movimiento_mayor = 0;
        }

        //sucursal_id
        $sucursal_id = Session::get('sucursal_id');
        
        $resultado        = Movimiento::leftjoin('person as paciente', 'paciente.id', '=', 'movimiento.persona_id')
                            ->join('person as responsable', 'responsable.id', '=', 'movimiento.responsable_id')
                            ->join('conceptopago','conceptopago.id','=','movimiento.conceptopago_id')
                            ->leftjoin('movimiento as m2','m2.movimiento_id','=','movimiento.id')
                            ->where('movimiento.caja_id', '=', $caja_id)
                            ->where('movimiento.sucursal_id','=',$sucursal_id)
                            ->where('movimiento.fecha', '=', $fecha)
                            ->where('movimiento.situacion','<>','A')
                            ->where('movimiento.conceptopago_id', '=', 8);
        $resultado        = $resultado->select('movimiento.*','m2.situacion as situacion2')->orderBy('paciente.apellidopaterno', 'asc');
        $lista            = $resultado->get();
        //dd($lista);
        $resultado2        = Movimiento::leftjoin('person as paciente', 'paciente.id', '=', 'movimiento.persona_id')
                            ->join('person as responsable', 'responsable.id', '=', 'movimiento.responsable_id')
                            ->join('conceptopago','conceptopago.id','=','movimiento.conceptopago_id')
                            ->leftjoin('movimiento as m2','m2.movimiento_id','=','movimiento.id')
                            ->where('movimiento.caja_id', '=', $caja_id)
                            ->where('movimiento.sucursal_id','=',$sucursal_id)
                            ->where('movimiento.fecha', '=', $fecha)
                            ->where('movimiento.conceptopago_id', '=', 10)
                            ->where('movimiento.situacion', '<>', 'A');
        $resultado2        = $resultado2->select('movimiento.*','m2.situacion as situacion2')->orderBy('paciente.apellidopaterno', 'asc');
        $lista2            = $resultado2->get();
        //dd($lista2);
        $pdf = new TCPDF();
        $pdf::SetTitle('Honorarios y Garantias del '.($rst->fecha));
        if (count($lista) > 0 || count($lista2) > 0) {            
            $pdf::AddPage();
            $pdf::SetFont('helvetica','B',12);
            $pdf::Image("http://localhost/juanpablo/dist/img/logo.jpg", 0, 0, 70, 14);
            $pdf::Cell(0,10,utf8_decode("Honorarios Medicos del ".date("d/m/Y",strtotime($rst->fecha))." - ".$caja->nombre),0,0,'C');
            $pdf::Ln();
            $pdf::SetFont('helvetica','B',8);
            $pdf::Cell(20,6,utf8_decode("DOC. VENTA"),1,0,'C');
            $pdf::Cell(75,6,utf8_decode("PACIENTE"),1,0,'C');
            $pdf::Cell(75,6,utf8_decode("SERVICIO"),1,0,'C');
            $pdf::Cell(20,6,utf8_decode("SUBTOTAL"),1,0,'C');
            $pdf::Ln();
            $doctor="";$total=0;$totalgeneral=0;
            foreach ($lista as $key => $value){
                if($doctor!=($value->persona->apellidopaterno." ".$value->persona->apellidomaterno." ".$value->persona->nombres)){
                    $pdf::SetFont('helvetica','B',8);
                    if($doctor!=""){
                        $pdf::Cell(170,6,("TOTAL :"),1,0,'R');
                        $pdf::Cell(20,6,number_format($total,2,'.',''),1,0,'C');
                        $totalgeneral = $totalgeneral + $total;
                        $total=0;
                        $pdf::Ln();
                    }
                    $doctor=($value->persona->apellidopaterno." ".$value->persona->apellidomaterno." ".$value->persona->nombres);
                    $pdf::Cell(190,6,($doctor),1,0,'L');
                    $pdf::Ln();
                }
                $pdf::SetFont('helvetica','',7);
                $list=explode(",",$value->listapago);
                for($c=0;$c<count($list);$c++){
                    $detalle = Detallemovcaja::find($list[$c]);
                    $movimiento = Movimiento::where("movimiento_id","=",$detalle->movimiento->id)->first();
                    if($movimiento->tipodocumento_id=="4") $abr="F";else $abr="B";
                    $pdf::Cell(20,6,utf8_decode($abr.$movimiento->serie."-".$movimiento->numero),1,0,'C');
                    $pdf::Cell(75,6,($movimiento->persona->apellidopaterno." ".$movimiento->persona->apellidomaterno." ".$movimiento->persona->nombres),1,0,'L');
                    if($detalle->servicio_id>0){
                        $pdf::Cell(75,6,utf8_decode($detalle->servicio->nombre),1,0,'L');
                    }else{
                        $pdf::Cell(75,6,utf8_decode($detalle->descripcion),1,0,'L');
                    }
                    $pdf::Cell(20,6,number_format($detalle->pagodoctor*$detalle->cantidad,2,'.',''),1,0,'C');
                    $total=$total + $detalle->pagodoctor*$detalle->cantidad;
                    $pdf::Ln();                
                }    
            }
            $totalgeneral = $totalgeneral + $total;
            $pdf::SetFont('helvetica','B',8);
            $pdf::Cell(170,6,("TOTAL :"),1,0,'R');
            $pdf::Cell(20,6,number_format($total,2,'.',''),1,0,'C');
            $pdf::Ln();
            $pdf::SetFont('helvetica','B',9);
            $pdf::Cell(170,6,("TOTAL HONORARIOS :"),1,0,'R');
            $pdf::Cell(20,6,number_format($totalgeneral,2,'.',''),1,0,'C');
            $pdf::Ln();

            $pdf::Ln();
            $pdf::SetFont('helvetica','B',12);
            $pdf::Cell(0,10,utf8_decode("Garantias de Medicos del ".date("d/m/Y",strtotime($rst->fecha))." - ".$caja->nombre),0,0,'C');
            $pdf::Ln();
            $pdf::SetFont('helvetica','B',8);
            $pdf::Cell(85,6,utf8_decode("MEDICO"),1,0,'C');
            $pdf::Cell(85,6,utf8_decode("PACIENTE"),1,0,'C');
            $pdf::Cell(20,6,utf8_decode("SUBTOTAL"),1,0,'C');
            $pdf::Ln();
            $total=0;
            $pdf::SetFont('helvetica','',8);
            foreach ($lista2 as $key => $value){
                $pdf::Cell(85,6,utf8_decode($value->doctor->apellidopaterno." ".$value->doctor->apellidomaterno." ".$value->doctor->nombres),1,0,'L');
                $pdf::Cell(85,6,($value->persona->apellidopaterno." ".$value->persona->apellidomaterno." ".$value->persona->nombres),1,0,'L');
                $pdf::Cell(20,6,number_format($value->total,2,'.',''),1,0,'C');
                $pdf::Ln();
                $total=$total + $value->total;                
            }
            $pdf::SetFont('helvetica','B',9);
            $pdf::Cell(170,6,("TOTAL GARANTIAS :"),1,0,'R');
            $pdf::Cell(20,6,number_format($total,2,'.',''),1,0,'C');
            $pdf::Ln();
            $pdf::SetFont('helvetica','B',9);
            $pdf::Cell(20,6,utf8_decode("USUARIO :"),0,0,'L');
            $pdf::SetFont('helvetica','',9);
            $user = Auth::user();
            $pdf::Cell(80,6,($responsable),0,0,'L');
            $pdf::Ln();
        }

        unset($rst);
        unset($resultado);unset($resultado2);
        unset($lista);unset($lista2);

        ////////////////////////////////////////////////////////////////////

        $rst  = Movimiento::where('tipomovimiento_id','=',2)->where('caja_id','=',$caja_id)->where('situacion','<>','A')->where('fecha','=',$fecha)->orderBy('movimiento.id','DESC')->limit(1)->first();
        if(count($rst)==0){
            $conceptopago_id=2;
        }else{
            $conceptopago_id=$rst->conceptopago_id;
        }
        
        $rst              = Movimiento::where('tipomovimiento_id','=',2)->where('caja_id','=',$caja_id)->where('conceptopago_id','=',1)->where('fecha','=',$fecha)->orderBy('id','DESC')->limit(1)->first();
        if(count($rst)>0){
            $movimiento_mayor = $rst->id;  
            $responsable = $rst->responsable->nombres;
        }else{
            $movimiento_mayor = 0;
        }
        
        $resultado        = Movimiento::leftjoin('person as paciente', 'paciente.id', '=', 'movimiento.persona_id')
                            ->join('person as responsable', 'responsable.id', '=', 'movimiento.responsable_id')
                            ->join('conceptopago','conceptopago.id','=','movimiento.conceptopago_id')
                            ->leftjoin('movimiento as m2','m2.movimiento_id','=','movimiento.id')
                            ->where('movimiento.caja_id', '=', $caja_id)
                            ->where('movimiento.sucursal_id','=',$sucursal_id)
                            ->where('movimiento.id', '>=', $movimiento_mayor)
                            ->where('movimiento.situacion','<>','A')
                            ->where('movimiento.conceptopago_id', '=', 8);
        $resultado        = $resultado->select('movimiento.*','m2.situacion as situacion2')->orderBy('paciente.apellidopaterno', 'asc');
        $lista            = $resultado->get();
        //dd($lista);
        $resultado2        = Movimiento::leftjoin('person as paciente', 'paciente.id', '=', 'movimiento.persona_id')
                            ->join('person as responsable', 'responsable.id', '=', 'movimiento.responsable_id')
                            ->join('conceptopago','conceptopago.id','=','movimiento.conceptopago_id')
                            ->leftjoin('movimiento as m2','m2.movimiento_id','=','movimiento.id')
                            ->where('movimiento.caja_id', '=', $caja_id)
                            ->where('movimiento.sucursal_id','=',$sucursal_id)
                            ->where('movimiento.id', '>=', $movimiento_mayor)
                            ->where('movimiento.conceptopago_id', '=', 10)
                            ->where('movimiento.situacion', '<>', 'A');
        $resultado2        = $resultado2->select('movimiento.*','m2.situacion as situacion2')->orderBy('paciente.apellidopaterno', 'asc');
        $lista2            = $resultado2->get();

        if (count($lista) > 0 || count($lista2) > 0) {            
            $pdf::AddPage();
            $pdf::SetFont('helvetica','B',12);
            $pdf::Image("http://localhost/juanpablo/dist/img/logo.jpg", 0, 0, 70, 14);
            $pdf::Cell(0,10,utf8_decode("Honorarios Medicos del ".date("d/m/Y",strtotime($rst->fecha))." - ".$caja->nombre),0,0,'C');
            $pdf::Ln();
            $pdf::SetFont('helvetica','B',8);
            $pdf::Cell(20,6,utf8_decode("DOC. VENTA"),1,0,'C');
            $pdf::Cell(75,6,utf8_decode("PACIENTE"),1,0,'C');
            $pdf::Cell(75,6,utf8_decode("SERVICIO"),1,0,'C');
            $pdf::Cell(20,6,utf8_decode("SUBTOTAL"),1,0,'C');
            $pdf::Ln();
            $doctor="";$total=0;$totalgeneral=0;
            foreach ($lista as $key => $value){
                if($doctor!=($value->persona->apellidopaterno." ".$value->persona->apellidomaterno." ".$value->persona->nombres)){
                    $pdf::SetFont('helvetica','B',8);
                    if($doctor!=""){
                        $pdf::Cell(170,6,("TOTAL :"),1,0,'R');
                        $pdf::Cell(20,6,number_format($total,2,'.',''),1,0,'C');
                        $totalgeneral = $totalgeneral + $total;
                        $total=0;
                        $pdf::Ln();
                    }
                    $doctor=($value->persona->apellidopaterno." ".$value->persona->apellidomaterno." ".$value->persona->nombres);
                    $pdf::Cell(190,6,($doctor),1,0,'L');
                    $pdf::Ln();
                }
                $pdf::SetFont('helvetica','',7);
                $list=explode(",",$value->listapago);
                for($c=0;$c<count($list);$c++){
                    $detalle = Detallemovcaja::find($list[$c]);
                    $movimiento = Movimiento::where("movimiento_id","=",$detalle->movimiento->id)->first();
                    if($movimiento->tipodocumento_id=="4") $abr="F";else $abr="B";
                    $pdf::Cell(20,6,utf8_decode($abr.$movimiento->serie."-".$movimiento->numero),1,0,'C');
                    $pdf::Cell(75,6,($movimiento->persona->apellidopaterno." ".$movimiento->persona->apellidomaterno." ".$movimiento->persona->nombres),1,0,'L');
                    if($detalle->servicio_id>0){
                        $pdf::Cell(75,6,utf8_decode($detalle->servicio->nombre),1,0,'L');
                    }else{
                        $pdf::Cell(75,6,utf8_decode($detalle->descripcion),1,0,'L');
                    }
                    $pdf::Cell(20,6,number_format($detalle->pagodoctor*$detalle->cantidad,2,'.',''),1,0,'C');
                    $total=$total + $detalle->pagodoctor*$detalle->cantidad;
                    $pdf::Ln();                
                }    
            }
            $totalgeneral = $totalgeneral + $total;
            $pdf::SetFont('helvetica','B',8);
            $pdf::Cell(170,6,("TOTAL :"),1,0,'R');
            $pdf::Cell(20,6,number_format($total,2,'.',''),1,0,'C');
            $pdf::Ln();
            $pdf::SetFont('helvetica','B',9);
            $pdf::Cell(170,6,("TOTAL HONORARIOS :"),1,0,'R');
            $pdf::Cell(20,6,number_format($totalgeneral,2,'.',''),1,0,'C');
            $pdf::Ln();

            //EDUARDO: AGREGO PAGO DE TARJETAS AL REPORTE 

            $resultado        = Movimiento::leftjoin('person as paciente', 'paciente.id', '=', 'movimiento.persona_id')
                                        ->join('person as responsable', 'responsable.id', '=', 'movimiento.responsable_id')
                                        ->join('conceptopago','conceptopago.id','=','movimiento.conceptopago_id')
                                        ->leftjoin('movimiento as m2','m2.movimiento_id','=','movimiento.id')
                                        ->where('movimiento.caja_id', '=', $caja_id)
                                        ->where('movimiento.sucursal_id','=',$sucursal_id)
                                        ->where('movimiento.id', '>=', $movimiento_mayor)
                                        ->where('movimiento.situacion','<>','A')
                                        ->whereNull('movimiento.cajaapertura_id')
                                        ->whereIn('movimiento.conceptopago_id', [45]);
            $resultado        = $resultado->select('movimiento.*','m2.situacion as situacion2')->orderBy('paciente.apellidopaterno', 'asc');
            //dd($movimiento_mayor);
            $lista            = $resultado->get();
            $pdf::SetFont('helvetica','B',12);
            $pdf::Cell(0,10,utf8_decode("Honorarios Medicos - Tarjeta del ".date("d/m/Y",strtotime($rst->fecha))." - ".$caja->nombre),0,0,'C');
            $pdf::Ln();
            $pdf::SetFont('helvetica','B',8);
            $pdf::Cell(20,6,utf8_decode("DOC. VENTA"),1,0,'C');
            $pdf::Cell(75,6,utf8_decode("PACIENTE"),1,0,'C');
            $pdf::Cell(75,6,utf8_decode("SERVICIO"),1,0,'C');
            $pdf::Cell(20,6,utf8_decode("SUBTOTAL"),1,0,'C');
            $pdf::Ln();
            $doctor="";$total=0;$totalgeneral=0;
            foreach ($lista as $key => $value){
                if($doctor!=($value->persona->apellidopaterno." ".$value->persona->apellidomaterno." ".$value->persona->nombres)){
                    $pdf::SetFont('helvetica','B',8);
                    if($doctor!=""){
                        $pdf::Cell(170,6,("TOTAL :"),1,0,'R');
                        $pdf::Cell(20,6,number_format($total,2,'.',''),1,0,'C');
                        $totalgeneral = $totalgeneral + $total;
                        $total=0;
                        $pdf::Ln();
                    }
                    $doctor=($value->persona->apellidopaterno." ".$value->persona->apellidomaterno." ".$value->persona->nombres);
                    $pdf::Cell(190,6,($doctor),1,0,'L');
                    $pdf::Ln();
                }
                $pdf::SetFont('helvetica','',7);
                $list=explode(",",$value->listapago);
                for($c=0;$c<count($list);$c++){
                    $detalle = Detallemovcaja::find($list[$c]);
                    $movimiento = Movimiento::where("movimiento_id","=",$detalle->movimiento->id)->first();
                    if($movimiento->tipodocumento_id=="4") $abr="F";else $abr="B";
                    $pdf::Cell(20,6,utf8_decode($abr.$movimiento->serie."-".$movimiento->numero),1,0,'C');
                    $pdf::Cell(75,6,($movimiento->persona->apellidopaterno." ".$movimiento->persona->apellidomaterno." ".$movimiento->persona->nombres),1,0,'L');
                    if($detalle->servicio_id>0){
                        $pdf::Cell(75,6,utf8_decode($detalle->servicio->nombre),1,0,'L');
                    }else{
                        $pdf::Cell(75,6,utf8_decode($detalle->descripcion),1,0,'L');
                    }
                    $pdf::Cell(20,6,number_format($detalle->pagodoctor*$detalle->cantidad,2,'.',''),1,0,'C');
                    $total=$total + $detalle->pagodoctor*$detalle->cantidad;
                    $pdf::Ln();                
                }    
            }
            $totalgeneral = $totalgeneral + $total;
            $pdf::SetFont('helvetica','B',8);
            $pdf::Cell(170,6,("TOTAL :"),1,0,'R');
            $pdf::Cell(20,6,number_format($total,2,'.',''),1,0,'C');
            $pdf::Ln();
            $pdf::SetFont('helvetica','B',9);
            $pdf::Cell(170,6,("TOTAL HONORARIOS :"),1,0,'R');
            $pdf::Cell(20,6,number_format($totalgeneral,2,'.',''),1,0,'C');
            $pdf::Ln();


            $pdf::Ln();
            $pdf::SetFont('helvetica','B',12);
            $pdf::Cell(0,10,utf8_decode("Garantias de Medicos del ".date("d/m/Y",strtotime($rst->fecha))." - ".$caja->nombre),0,0,'C');
            $pdf::Ln();
            $pdf::SetFont('helvetica','B',8);
            $pdf::Cell(85,6,utf8_decode("MEDICO"),1,0,'C');
            $pdf::Cell(85,6,utf8_decode("PACIENTE"),1,0,'C');
            $pdf::Cell(20,6,utf8_decode("SUBTOTAL"),1,0,'C');
            $pdf::Ln();
            $total=0;
            $pdf::SetFont('helvetica','',8);
            foreach ($lista2 as $key => $value){
                $pdf::Cell(85,6,utf8_decode($value->doctor->apellidopaterno." ".$value->doctor->apellidomaterno." ".$value->doctor->nombres),1,0,'L');
                $pdf::Cell(85,6,($value->persona->apellidopaterno." ".$value->persona->apellidomaterno." ".$value->persona->nombres),1,0,'L');
                $pdf::Cell(20,6,number_format($value->total,2,'.',''),1,0,'C');
                $pdf::Ln();
                $total=$total + $value->total;                
            }
            $pdf::SetFont('helvetica','B',9);
            $pdf::Cell(170,6,("TOTAL GARANTIAS :"),1,0,'R');
            $pdf::Cell(20,6,number_format($total,2,'.',''),1,0,'C');
            $pdf::Ln();
            $pdf::SetFont('helvetica','B',9);
            $pdf::Cell(20,6,utf8_decode("USUARIO :"),0,0,'L');
            $pdf::SetFont('helvetica','',9);
            $user = Auth::user();
            $pdf::Cell(80,6,($responsable),0,0,'L');
            $pdf::Ln();
        }
        $pdf::Output('ReporteHonorarioF.pdf');
    }

    public function venta(Request $request)
    {//PAGO PARTICULAR
        $user = Auth::user();

        //sucursal_id
        $sucursal_id = Session::get('sucursal_id');

        $resultado  = Movimiento::join('detallemovcaja as dmc','dmc.movimiento_id','=','movimiento.id')
                        ->join('person as medico','medico.id','=','dmc.persona_id')
                        ->join('movimiento as mref','mref.movimiento_id','=','movimiento.id')
                        ->leftjoin('servicio as s','s.id','=','dmc.servicio_id')
                        ->where(DB::raw('concat(medico.apellidopaterno,\' \',medico.apellidomaterno,\' \',medico.nombres)'),'like','%'.$request->input('busqueda').'%')
                        ->where('movimiento.tipomovimiento_id','=',1)
                        ->where('movimiento.sucursal_id','=',$sucursal_id)
                        ->whereNull('movimiento.tarjeta')
                        ->where('dmc.situacion','LIKE','N')
                        ->where('movimiento.plan_id','=',6)
                        ->where('dmc.pagodoctor','>',0);
        if($request->input('miusuario')=="S"){
            $resultado = $resultado->where("movimiento.responsable_id","=",$user->person_id);
        }
        $resultado = $resultado->orderBy('mref.fecha', 'ASC')
                        ->select('mref.*','dmc.servicio_id','dmc.id as iddetalle','s.nombre as servicio','dmc.cantidad','dmc.descripcion as servicio2','dmc.pagodoctor',DB::raw('concat(medico.apellidopaterno,\' \',medico.apellidomaterno,\' \',medico.nombres) as medico'));
        $list      = $resultado->get();
        $registro="<table class='table table-hover'>
                    <thead>
                        <tr>
                            <th  width='80px'  class='text-center'>Nro</th>
                            <th class='text-center'>Paciente</th>
                            <th class='text-center'>Servicio</th>
                            <th class='text-center'>Pago</th>
                        </tr>
                    </thead>
                    <tbody>";
        foreach ($list as $key => $value) {
            if($value->servicio_id>0){
                $servicio=$value->servicio;
            }else{
                $servicio=$value->servicio2;
            }
            $numero=($value->tipodocumento_id==4?'F':'B').$value->serie.'-'.$value->numero;
            $registro.="<tr onclick=\"agregarDoc($value->iddetalle,'".trim($value->persona->apellidopaterno.' '.$value->persona->apellidomaterno.' '.$value->persona->nombres.' '.$value->persona->bussinesname)."','$servicio','$numero','".number_format($value->pagodoctor*$value->cantidad,2,'.','')."')\">";
            $registro.="<td  width='80px' >".$numero."</td>";
            $registro.="<td>".trim($value->persona->apellidopaterno.' '.$value->persona->apellidomaterno.' '.$value->persona->nombres.' '.$value->persona->bussinesname)."</td>";
            $registro.="<td>".$servicio."</td>";
            $registro.="<td>".number_format($value->pagodoctor*$value->cantidad,2,'.','')."</td>";
            $registro.="</tr>";
        }
        $registro.="</tbody></table>";
        echo $registro;
    }

    public function ventasocio(Request $request)
    {
        $user = Auth::user();

        //sucursal_id
        $sucursal_id = Session::get('sucursal_id');

        $resultado        = Movimiento::join('detallemovcaja as dmc','dmc.movimiento_id','=','movimiento.id')
                            ->join('person as medico','medico.id','=','dmc.persona_id')
                            ->join('movimiento as mref','mref.movimiento_id','=','movimiento.id')
                            ->join('person as paciente2','paciente2.id','=','movimiento.persona_id')
                            ->leftjoin('servicio as s','s.id','=','dmc.servicio_id')
                            ->where(DB::raw('concat(medico.apellidopaterno,\' \',medico.apellidomaterno,\' \',medico.nombres)'),'like','%'.$request->input('busqueda').'%')
                            ->where('movimiento.tipomovimiento_id','=',1)
                            ->where('movimiento.sucursal_id','=',$sucursal_id)
                            ->whereNull('dmc.medicosocio_id')
                            ->Where(function($query){
                                $query->whereNull('dmc.situacionboleteo')
                                      ->orWhere(function($q){
                                            $q->whereNull('dmc.situaciontarjeta');
                                      });
                            });
        if($request->input('miusuario')=="S"){
            $resultado = $resultado->where("movimiento.responsable_id","=",$user->person_id);
        }
        $resultado = $resultado->orderBy('mref.fecha', 'ASC')
                            ->select('mref.*','dmc.id as iddetalle','s.nombre as servicio','dmc.descripcion as servicio2','dmc.pagodoctor','dmc.cantidad','dmc.servicio_id',DB::raw('concat(medico.apellidopaterno,\' \',medico.apellidomaterno,\' \',medico.nombres) as medico'),DB::raw('concat(paciente2.apellidopaterno,\' \',paciente2.apellidomaterno,\' \',paciente2.nombres) as nombrepaciente2'));
        $list      = $resultado->get();
        $registro="<table class='table table-hover'>
                    <thead>
                        <tr>
                            <th width='80px' class='text-center'>Nro</th>
                            <th class='text-center'>Paciente</th>
                            <th class='text-center'>Servicio</th>
                            <th class='text-center'>Pago</th>
                        </tr>
                    </thead>
                    <tbody>";
        foreach ($list as $key => $value) {
            if($value->servicio_id>0){
                $servicio=$value->servicio;
            }else{
                $servicio=$value->servicio2;
            }
            $numero=($value->tipodocumento_id==4?'F':'B').$value->serie.'-'.$value->numero;
            $registro.="<tr onclick=\"agregarDocSocio($value->iddetalle,'".trim($value->nombrepaciente2)."','$servicio','$numero','".number_format($value->pagodoctor*$value->cantidad,2,'.','')."')\">";
            $registro.="<td width='80px'>".$numero."</td>";
            $registro.="<td>".trim($value->nombrepaciente2)."</td>";
            $registro.="<td>".$servicio."</td>";
            $registro.="<td>".number_format($value->pagohospital*$value->cantidad,2,'.','')."</td>";
            $registro.="</tr>";
        }
        $registro.="</tbody></table>";
        echo $registro;
    }

    public function ventatarjeta(Request $request)
    {
        $user = Auth::user();
        
        //sucursal_id
        $sucursal_id = Session::get('sucursal_id');

        $resultado        = Movimiento::join('detallemovcaja as dmc','dmc.movimiento_id','=','movimiento.id')
                            ->join('person as medico','medico.id','=','dmc.persona_id')
                            ->join('movimiento as mref','mref.movimiento_id','=','movimiento.id')
                            ->join('person as paciente2','paciente2.id','=','movimiento.persona_id')
                            ->leftjoin('servicio as s','s.id','=','dmc.servicio_id')
                            ->where(DB::raw('concat(medico.apellidopaterno,\' \',medico.apellidomaterno,\' \',medico.nombres)'),'like','%'.$request->input('busqueda').'%')
                            ->where('movimiento.tipomovimiento_id','=',1)
                            ->where('movimiento.sucursal_id','=',$sucursal_id)
                            ->whereNotNull('movimiento.tarjeta')
                            ->whereNull('dmc.medicosocio_id')
                            ->whereNull('dmc.situaciontarjeta');
        if($request->input('miusuario')=="S"){
            $resultado = $resultado->where("movimiento.responsable_id","=",$user->person_id);
        }
        $resultado = $resultado->orderBy('mref.fecha', 'ASC')
                            ->select('mref.*','dmc.id as iddetalle','s.nombre as servicio','dmc.descripcion as servicio2','dmc.pagodoctor','dmc.cantidad','dmc.servicio_id',DB::raw('concat(medico.apellidopaterno,\' \',medico.apellidomaterno,\' \',medico.nombres) as medico'),DB::raw('concat(paciente2.apellidopaterno,\' \',paciente2.apellidomaterno,\' \',paciente2.nombres) as nombrepaciente2'));
        $list      = $resultado->get();
        $registro="<table class='table table-hover'>
                    <thead>
                        <tr>
                            <th width='80px' class='text-center'>Nro</th>
                            <th class='text-center'>Paciente</th>
                            <th class='text-center'>Servicio</th>
                            <th class='text-center'>Pago</th>
                        </tr>
                    </thead>
                    <tbody>";
        foreach ($list as $key => $value) {
            if($value->servicio_id>0){
                $servicio=$value->servicio;
            }else{
                $servicio=$value->servicio2;
            }
            $numero=($value->tipodocumento_id==4?'F':'B').$value->serie.'-'.$value->numero;
            $registro.="<tr onclick=\"agregarDocSocio($value->iddetalle,'".trim($value->nombrepaciente2)."','$servicio','$numero','".number_format($value->pagodoctor*$value->cantidad,2,'.','')."')\">";
            $registro.="<td width='80px'>".$numero."</td>";
            $registro.="<td>".trim($value->nombrepaciente2)."</td>";
            $registro.="<td>".$servicio."</td>";
            $registro.="<td>".number_format($value->pagohospital*$value->cantidad,2,'.','')."</td>";
            $registro.="</tr>";
        }
        $registro.="</tbody></table>";
        echo $registro;
    }

    public function ventaboleteo(Request $request)
    {
        $user = Auth::user();

        //sucursal_id
        $sucursal_id = Session::get('sucursal_id');

        $resultado        = Movimiento::join('detallemovcaja as dmc','dmc.movimiento_id','=','movimiento.id')
                            ->join('person as medico','medico.id','=','dmc.persona_id')
                            ->join('movimiento as mref','mref.movimiento_id','=','movimiento.id')
                            ->join('person as paciente2','paciente2.id','=','movimiento.persona_id')
                            ->leftjoin('servicio as s','s.id','=','dmc.servicio_id')
                            ->where(DB::raw('concat(medico.apellidopaterno,\' \',medico.apellidomaterno,\' \',medico.nombres)'),'like','%'.$request->input('busqueda').'%')
                            ->where('movimiento.tipomovimiento_id','=',1)
                            ->where('movimiento.sucursal_id','=',$sucursal_id)
                            ->where('dmc.pagodoctor','=',0)
                            ->where('dmc.situacion','LIKE','N');
                            //->where('movimiento.plan_id','=',6);
        if($request->input('miusuario')=="S"){
            $resultado = $resultado->where("movimiento.responsable_id","=",$user->person_id);
        }
        $resultado = $resultado->orderBy('mref.fecha', 'ASC')
                            ->select('mref.*','dmc.id as iddetalle','s.nombre as servicio','dmc.descripcion as servicio2','dmc.pagodoctor','dmc.cantidad','dmc.servicio_id',DB::raw('concat(medico.apellidopaterno,\' \',medico.apellidomaterno,\' \',medico.nombres) as medico'),DB::raw('concat(paciente2.apellidopaterno,\' \',paciente2.apellidomaterno,\' \',paciente2.nombres) as nombrepaciente2'));
        $list      = $resultado->get();
        $registro="<table class='table table-hover'>
                    <thead>
                        <tr>
                            <th width='80px' class='text-center'>Nro</th>
                            <th class='text-center'>Paciente</th>
                            <th class='text-center'>Servicio</th>
                            <th class='text-center'>Pago</th>
                        </tr>
                    </thead>
                    <tbody>";
        foreach ($list as $key => $value) {
            if($value->servicio_id>0){
                $servicio=$value->servicio;
            }else{
                $servicio=$value->servicio2;
            }
            $numero=($value->tipodocumento_id==4?'F':'B').$value->serie.'-'.$value->numero;
            $registro.="<tr onclick=\"agregarDocSocio($value->iddetalle,'".trim($value->nombrepaciente2)."','".$servicio."','$numero','".number_format($value->pagodoctor*$value->cantidad,2,'.','')."')\">";
            $registro.="<td width='80px'>".$numero."</td>";
            $registro.="<td>".trim($value->nombrepaciente2)."</td>";
            $registro.="<td>".$servicio."</td>";
            $registro.="<td>".number_format($value->pagohospital*$value->cantidad,2,'.','')."</td>";
            $registro.="</tr>";
        }
        $registro.="</tbody></table>";
        echo $registro;
    }

    public function ventapago(Request $request)
    {
        $user = Auth::user();

        //sucursal_id
        $sucursal_id = Session::get('sucursal_id');
        
        $resultado        = Movimiento::join('detallemovcaja as dmc','dmc.movimiento_id','=','movimiento.id')
                            ->join('person as medico','medico.id','=','dmc.persona_id')
                            ->join('movimiento as mref','mref.movimiento_id','=','movimiento.id')
                            ->leftjoin('servicio as s','s.id','=','dmc.servicio_id')
                            ->where(DB::raw('concat(medico.apellidopaterno,\' \',medico.apellidomaterno,\' \',medico.nombres)'),'like','%'.$request->input('busqueda').'%')
                            ->where('movimiento.tipomovimiento_id','=',1)
                            ->where('movimiento.sucursal_id','=',$sucursal_id)
                            ->where('movimiento.situacion','<>','U')
                            ->where('dmc.pagodoctor','>',0)
                            ->whereIn('dmc.situacion',['N','C'])
                            ->whereNotIn('dmc.situacionentrega',['E']);
        if($request->input('miusuario')=="S"){
            $resultado = $resultado->where("movimiento.responsable_id","=",$user->person_id);
        }
        $resultado = $resultado->orderBy('mref.fecha', 'ASC')
                            ->select('mref.*','dmc.descripcion as servicio2','dmc.id as iddetalle','dmc.situacion','dmc.servicio_id','s.nombre as servicio','dmc.cantidad','dmc.pagodoctor',DB::raw('concat(medico.apellidopaterno,\' \',medico.apellidomaterno,\' \',medico.nombres) as medico'));
        $list      = $resultado->get();
        $registro="<table class='table table-hover' style='max-heigth:150px;'>
                    <thead>
                        <tr>
                            <th  width='80px'  class='text-center'>Nro</th>
                            <th class='text-center'>Paciente</th>
                            <th class='text-center'>Servicio</th>
                            <th class='text-center'>Pago</th>
                        </tr>
                    </thead>
                    <tbody>";
        foreach ($list as $key => $value) {
            if($value->servicio_id>0){
                $servicio=$value->servicio;
            }else{
                $servicio=$value->servicio2;
            }
            $registro.="<tr onclick=\"agregarDoc($value->iddetalle,'".trim($value->persona->apellidopaterno.' '.$value->persona->apellidomaterno.' '.$value->persona->nombres.' '.$value->persona->bussinesname)."','$servicio','".($value->tipodocumento_id==4?"F":"B")."$value->serie-$value->numero','".number_format($value->pagodoctor*$value->cantidad,2,'.','')."')\">";
            $registro.="<td  width='80px' >".($value->tipodocumento_id==4?"F":"B").$value->serie.'-'.$value->numero."</td>";
            $registro.="<td>".trim($value->persona->apellidopaterno.' '.$value->persona->apellidomaterno.' '.$value->persona->nombres.' '.$value->persona->bussinesname)."</td>";
            $registro.="<td>".$servicio."</td>";
            $registro.="<td>".number_format($value->pagodoctor*$value->cantidad,2,'.','')."</td>";
            $registro.="</tr>";
        }
        $registro.="</tbody></table>";
        echo $registro;
    }

    /////////////////////////////////////////////////////////////////////////////

    public function control(Request $request){
        $entidad          = 'Caja';
        $title            = 'Garantia Control Remoto';
        $ruta             = $this->rutas;
        return view($this->folderview.'.control')->with(compact('entidad', 'title', 'ruta'));
    }

    public function buscarcontrol(Request $request)
    {
        $pagina           = $request->input('page');
        $filas            = $request->input('filas');
        $entidad          = 'buscarcontrol';
        $resultado        = Movimiento::leftjoin('person as paciente', 'paciente.id', '=', 'movimiento.persona_id')
                            ->join('person as responsable', 'responsable.id', '=', 'movimiento.responsable_id')
                            ->whereIn('movimiento.conceptopago_id',[29,30]);
                            //->where('movimiento.situacion','<>','U')
                            //->where('m2.tipomovimiento_id','=',1);
        if($request->input('fecha')!=""){
            $resultado = $resultado->where('movimiento.fecha','>=',$request->input('fecha').' 00:00:00');
        }
        if($request->input('numero')!=""){
            $resultado = $resultado->where('movimiento.numero','LIKE','%'.$request->input('numero').'%');
        }    
        if($request->input('paciente')!=""){
            $resultado = $resultado->where(DB::raw('concat(paciente.apellidopaterno," ",paciente.apellidomaterno," ",paciente.nombres)'),'LIKE','%'.strtoupper($request->input('paciente')).'%');
        }   

        $resultado        = $resultado->select('movimiento.*','movimiento.numero as numero2','paciente.apellidopaterno','paciente.apellidomaterno','paciente.nombres')->orderBy('movimiento.fecha', 'ASC')->orderBy('movimiento.fecha', 'ASC')->orderBy('movimiento.numero', 'ASC');
        $lista            = $resultado->get();
        $cabecera         = array();
        $cabecera[]       = array('valor' => '#', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Fecha', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Nro', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Paciente', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Total', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Situacion', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Comentario', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Operaciones', 'numero' => '3');
        
        $user = Auth::user();
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
            return view($this->folderview.'.list3')->with(compact('lista', 'paginacion', 'inicio', 'fin', 'entidad', 'cabecera', 'ruta', 'user'));
        }
        return view($this->folderview.'.list3')->with(compact('lista', 'entidad'));
    }

    public function ticketspendientes(Request $request) {
        $entidad = 'ticket';
        $ruta = $this->rutas;
        return view($this->folderview.'.ticketspendientes')->with(compact('entidad', 'ruta'));
    }

    public function listaticketspendientes($numero, $fecha, $paciente) {
        if($numero == '0') {
            $numero = '';
        }
        $ruta = $this->rutas;
        $resultado        = Movimiento::leftjoin('person as paciente', 'paciente.id', '=', 'movimiento.persona_id')
        ->where('movimiento.numero','LIKE','%'.$numero.'%')->where('movimiento.tipodocumento_id','=','1');
        if($fecha!=""){
            $resultado = $resultado->where('movimiento.fecha', '=', ''.$fecha.'');
        }
        if($paciente!="0"){
            $resultado = $resultado->where(DB::raw('concat(paciente.apellidopaterno,\' \',paciente.apellidomaterno,\' \',paciente.nombres)'), 'LIKE', '%'.$paciente.'%');
        }
        $resultado        = $resultado->select('movimiento.*',DB::raw('concat(paciente.apellidopaterno,\' \',paciente.apellidomaterno,\' \',paciente.nombres) as paciente'))->orderBy('movimiento.id','DESC')->orderBy('movimiento.situacion','DESC');
        $lista            = $resultado->get();
        $cabecera         = array();
        $cabecera[]       = array('valor' => 'Fecha', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Nro', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Paciente', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Total', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Situacion', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Operacion', 'numero' => '1');
        
        //$conf = DB::connection('sqlsrv')->table('BL_CONFIGURATION')->get();
        if (count($lista) > 0) {
            return view($this->folderview.'.listaticketspendientes')->with(compact('lista', 'cabecera', 'ruta'));
        }
        return view($this->folderview.'.listaticketspendientes')->with(compact('lista', 'ruta'));
    }

    public function cobrarticket($id)
    {
        $id = explode('&', $id)[0];
        $existe = Libreria::verificarExistencia($id, 'Movimiento');
        $ruta = $this->rutas;
        if ($existe !== true) {
            return $existe;
        }
        $movimiento = Movimiento::find($id);
        $serie=1;
        $entidad    = 'Movimiento';
        $formData   = array('caja.cobrarticket2');
        $formData   = array('route' => $formData, 'method' => 'POST', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton      = 'Registrar';
        $cboCaja = Caja::where('nombre', '<>', 'TESORERIA')->where('nombre', '<>', 'FARMACIA')->where('nombre', '<>', 'TESORERIA - FARMACIA')->get();
        $cboFormaPago     = array("Efectivo" => "Efectivo", "Tarjeta" => "Tarjeta");
        $cboTipoTarjeta    = array("VISA" => "VISA", "MASTER" => "MASTER");
        $cboTipoTarjeta2    = array("CREDITO" => "CREDITO", "DEBITO" => "DEBITO");
        $cboTipoDocumento     = array("Boleta" => "Boleta", "Factura" => "Factura", "Ticket" => "Ticket");

        $detalles = Movimiento::select('detallemovcaja.id', 'movimiento.serie', 'movimiento.numero', 'cantidad', 'detallemovcaja.persona_id', 'descripcion', 'cantidad', 'detallemovcaja.precio', 'descuento', 'servicio.nombre')->join('detallemovcaja', 'movimiento.id', '=', 'detallemovcaja.movimiento_id')->join('servicio', 'servicio.id', '=', 'detallemovcaja.servicio_id')->where('movimiento.id', $id)->where('detallemovcaja.deleted_at', '=', null)->get();

        return view($this->folderview.'.cobrarticket')->with(compact('Caja', 'formData', 'entidad', 'boton', 'movimiento', 'cboFormaPago', 'cboTipoTarjeta', 'cboTipoTarjeta2', 'cboCaja', 'cboTipoDocumento', 'ruta', 'detalles', 'serie'));
    }

    public function cobrarticket2(Request $request)
    {
        $user = Auth::user();
        
        //sucursal_id
        $sucursal_id = Session::get('sucursal_id');

        $error = DB::transaction(function() use($request,$user, $sucursal_id,&$dat,&$numeronc){
            $Ticket = Movimiento::find($request->input('id'));

            //Actualizamos precios de detalles del ticket

            $detalles = Detallemovcaja::select('id')->where('movimiento_id', $Ticket->id)->whereNull('deleted_at')->get();

            $i = 0;
            foreach ($detalles as $detalle) {
                $detall = Detallemovcaja::find($detalle->id);
                $detall->descuento = $request->input('descuento' . $i);
                $detall->tipodescuento = $request->input('tipodescuento');
                $detall->pagohospital = $request->input('subtotal' . $i);
                $detall->save();
                $i++;
            }

            //Solo si se paga

            $Ticket->total = $request->input('total');
            $Ticket->tiempo_cola = date('Y-m-d H:i:s');
            $Ticket->totalpagado = $request->input('efectivo');
            $Ticket->totalpagadovisa = $request->input('visa');
            $Ticket->totalpagadomaster = $request->input('master');
            $Ticket->numvisa = $request->input('numvisa');
            $Ticket->nummaster = $request->input('nummaster');
            $Ticket->situacion2 = 'C'; // Cola

            if($request->input('total') == $request->input('total2')){
                $Ticket->situacion='C';//Pendiente => P / Cobrado => C / Boleteado => B                
            } else {
                $Ticket->situacion='D';
            }

            //Guardamos el Ticket

            $Ticket->save();
           
            $pagohospital=$Ticket->total;

            $caja = Caja::find($request->input('caja_id'));

            //Solo si se genera un comprobante de pago

            if(number_format($request->input('total')) == number_format($request->input('total2'))){
                if($pagohospital>=0){//Puse con pago hospital por generar F.E.            
                    //Genero Documento de Venta
                    //Boleta
                    if($request->input('tipodocumento')=="Boleta"){
                        $tipodocumento_id=5;
                        $codigo="03";
                        $abreviatura="B";
                    }
                    //Factura
                    else if($request->input('tipodocumento')=="Factura"){
                        $tipodocumento_id=4;
                        $codigo="01";
                        $abreviatura="F";

                        //Algoritmo para la empresa

                        $empresa = Person::where('ruc', $request->input('ccruc'))->first();

                        if(count($empresa) == 1) {
                            $idempresa = $empresa->id;
                        } else {
                            //Guardo la empresa como una nueva person
                            $nuevaempresa = new Person();
                            $nuevaempresa->ruc = $request->input('ccruc');
                            $nuevaempresa->bussinesname = $request->input('ccrazon');
                            $nuevaempresa->direccion = $request->input('ccdireccion');
                            $nuevaempresa->save();

                            $idempresa = $nuevaempresa->id;
                        }

                    } else {
                        $tipodocumento_id=12;
                        //$codigo="01";
                        $abreviatura="T";
                    }

                    //Genero venta como nuevo movimiento

                    $venta        = new Movimiento();
                    $venta->sucursal_id = $sucursal_id;
                    $venta->fecha = date("Y-m-d");

                    //Puede ser manual o no

                    $venta->numero= Movimiento::NumeroSigue($caja->id, $sucursal_id,4,$tipodocumento_id,$request->input('serieventa') + 0,'N');

                    $venta->serie = '00'.$request->input('serieventa');

                    if($request->input('tipodocumento')=="Factura"){
                        $venta->empresa_id = $idempresa;
                    }

                    $venta->responsable_id=$user->person_id;
                    $venta->persona_id=$Ticket->persona_id;
                    if($request->input('tipodocumento')=="Boleta"){
                        $venta->subtotal=number_format($pagohospital/1.18,2,'.','');
                        $venta->igv=number_format($pagohospital - $venta->subtotal,2,'.','');
                        $venta->total=$pagohospital;     
                    }else if($request->input('tipodocumento')=="Factura"){
                        $venta->subtotal=number_format($pagohospital/1.18,2,'.','');
                        $venta->igv=number_format($pagohospital - $venta->subtotal,2,'.','');
                        $venta->total=number_format($pagohospital,2,'.','');                     
                    } else {
                        $venta->subtotal=number_format($pagohospital,2,'.','');
                        $venta->igv=0;
                        $venta->total=number_format($pagohospital,2,'.','');  
                    }
                    $venta->tipomovimiento_id=4;
                    $venta->caja_id=$caja->id;
                    $venta->numero=$request->input('numeroventa');
                    $venta->serie=$request->input('serieventa');
                    $venta->tipodocumento_id=$tipodocumento_id;
                    $venta->comentario='';
                    $venta->manual='N';
                    $venta->situacion='N';        
                    $venta->movimiento_id=$Ticket->id;
                    $venta->ventafarmacia='N';

                    //Guardamos la venta

                    $venta->save();

                    //Solo si hay pago, guardo movimiento en caja
                    $movimiento        = new Movimiento();
                    $movimiento->sucursal_id = $sucursal_id;
                    $movimiento->fecha = date("Y-m-d");
                    $movimiento->numero= Movimiento::NumeroSigue($caja->id, $sucursal_id,2,2);
                    $movimiento->responsable_id=$user->person_id;
                    $movimiento->persona_id=$Ticket->persona_id;
                    $movimiento->subtotal=0;
                    $movimiento->igv=0;
                    $movimiento->total=$Ticket->total;
                    $movimiento->totalpagado = $request->input('efectivo', 0);
                    $movimiento->totalpagadovisa = $request->input('visa', 0);
                    $movimiento->totalpagadomaster = $request->input('master', 0);
                    $movimiento->tipomovimiento_id=2;
                    $movimiento->tipodocumento_id=2;
                    $movimiento->conceptopago_id=3;//PAGO DE CLIENTE
                    $movimiento->comentario='Pago de : '.substr($request->input('tipodocumento'),0,1).' '.$venta->serie.'-'.$venta->numero;
                    $movimiento->caja_id=$request->input('caja_id');
                    $movimiento->situacion='N';
                    $movimiento->movimiento_id=$venta->id;
                    $movimiento->save();
                    //
                }
            }

            //SOLO SI HAY UN CRÉDITO

            else if($request->input('total') > $request->input('total2')){
                if($pagohospital>0){//Puse con pago hospital por generar F.E.  

                    //GENERO EL RESUMEN QUE ACUMULARÁ EL TOTAL DE LAS CUOTAS

                    $rescuotas        = new Movimiento();
                    $rescuotas->sucursal_id = $sucursal_id;
                    $rescuotas->fecha = date("Y-m-d");
                    $rescuotas->numero= Movimiento::NumeroSigueResumenCuotas($caja->id,$sucursal_id, 'Z', 'TOTAL DE CUOTAS');
                    $rescuotas->responsable_id=$user->person_id;
                    $rescuotas->persona_id=$Ticket->persona_id;
                    $rescuotas->total = $request->input('total2', 0);
                    $rescuotas->totalpagado = $request->input('efectivo', 0);
                    $rescuotas->totalpagadovisa = $request->input('visa', 0);
                    $rescuotas->totalpagadomaster = $request->input('master', 0);
                    $rescuotas->comentario='TOTAL DE CUOTAS';
                    $rescuotas->situacion='Z';
                    $rescuotas->caja_id=$request->input('caja_id');
                    $rescuotas->movimiento_id=$Ticket->id;
                    $rescuotas->save();
                    
                    //Solo si hay pago, guardo la primera cuota cobrada

                    $primeracuota        = new Movimiento();
                    $primeracuota->sucursal_id = $sucursal_id;
                    $primeracuota->fecha = date("Y-m-d");
                    $primeracuota->numero= Movimiento::NumeroSigueCuota($request->input('caja_id'), $sucursal_id, 'Z',14);
                    $primeracuota->numeroserie2= Movimiento::NumeroSigueSerieCuota($rescuotas->id);
                    $primeracuota->responsable_id=$user->person_id;
                    $primeracuota->persona_id=$Ticket->persona_id;
                    $primeracuota->subtotal=0;
                    $primeracuota->igv=0;
                    $primeracuota->total = $request->input('total2', 0);
                    $primeracuota->totalpagado = $request->input('efectivo', 0);
                    $primeracuota->totalpagadovisa = $request->input('visa', 0);
                    $primeracuota->totalpagadomaster = $request->input('master', 0);
                    $primeracuota->numvisa = $request->input('numvisa');
                    $primeracuota->nummaster = $request->input('nummaster');
                    $primeracuota->tipomovimiento_id=14;//EN BLANCO, EL INGRESO SE HARÁ AL COMPLETAR LA BOLETA
                    $primeracuota->comentario='PAGO DE CUOTA PARCIAL DE CLIENTE';
                    $primeracuota->caja_id=$request->input('caja_id');
                    $primeracuota->situacion='Z';
                    $primeracuota->movimiento_id=$rescuotas->id;
                    $primeracuota->save();     

                    //Guardo ingreso en Caja del pago parcial

                    $movimiento        = new Movimiento();
                    $movimiento->sucursal_id = $sucursal_id;
                    $movimiento->fecha = date("Y-m-d");
                    $movimiento->numero= Movimiento::NumeroSigue($caja->id, $sucursal_id,2,2);
                    $movimiento->responsable_id=$user->person_id;
                    $movimiento->persona_id=$Ticket->persona_id;
                    $movimiento->subtotal=0;
                    $movimiento->igv=0;
                    $cefectivo = $request->input('efectivo', 0);
                    $cvisa = $request->input('visa', 0);
                    $cmaster = $request->input('master', 0);
                    if($request->input('efectivo', 0) == '') {
                        $cefectivo = 0;
                    }
                    if($request->input('visa', 0) == '') {
                        $cvisa = 0;
                    }
                    if($request->input('master', 0) == '') {
                        $cmaster = 0;
                    }
                    $movimiento->total=$cefectivo+$cvisa+$cmaster;
                    $movimiento->totalpagado = $request->input('efectivo', 0);
                    $movimiento->totalpagadovisa = $request->input('visa', 0);
                    $movimiento->totalpagadomaster = $request->input('master', 0);
                    $movimiento->tipomovimiento_id=2;
                    $movimiento->tipodocumento_id=2;
                    $movimiento->conceptopago_id=3;//PAGO DE CLIENTE
                    $movimiento->comentario='PAGO DE CUOTA PARCIAL DE CLIENTE';
                    $movimiento->caja_id=$request->input('caja_id');
                    $movimiento->situacion='N';
                    $movimiento->situacion2='Z';
                    $movimiento->numeroserie2 = $primeracuota->id;
                    $movimiento->movimiento_id=$Ticket->id;
                    $movimiento->save();               
                }
            }
        });

        ///////////////////////////////
        return is_null($error) ? "OK" : $error;
    }

    //Para cuentas por cobrar

    public function cuentaspendientes(Request $request) {
        $entidad = 'ticket';
        $ruta = $this->rutas;
        return view($this->folderview.'.cuentaspendientes')->with(compact('entidad', 'ruta'));
    }

    public function listacuentaspendientes($numero, $fecha, $paciente) {
        $ruta = $this->rutas;
        $resultado        = Movimiento::leftjoin('person as paciente', 'paciente.id', '=', 'movimiento.persona_id')
                            ->leftjoin('movimiento as m2', 'movimiento.movimiento_id', '=', 'm2.id');
        if($fecha!=""){
            $resultado = $resultado->where('m2.fecha', '=', ''.$fecha.'')
        ->where('movimiento.situacion','=','Z')->where('movimiento.comentario', 'TOTAL DE CUOTAS');
        }
        if($paciente!="0"){
            $resultado = $resultado->where(DB::raw('concat(paciente.apellidopaterno,\' \',paciente.apellidomaterno,\' \',paciente.nombres)'), 'LIKE', '%'.$paciente.'%');
        }
        $resultado        = $resultado->select('movimiento.*', 'm2.fecha as fecha2',DB::raw('concat(paciente.apellidopaterno,\' \',paciente.apellidomaterno,\' \',paciente.nombres) as paciente'), DB::raw('movimiento.total as pendiente'))->orderBy('movimiento.id','DESC')->orderBy('movimiento.situacion','DESC');
        $lista            = $resultado->get();
        $cabecera         = array();
        $cabecera[]       = array('valor' => 'Fecha', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Nro', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Paciente', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Total Pagado', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Pendiente', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Operacion', 'numero' => '1');
        
        //$conf = DB::connection('sqlsrv')->table('BL_CONFIGURATION')->get();
        if (count($lista) > 0) {
            return view($this->folderview.'.listacuentaspendientes')->with(compact('lista', 'cabecera', 'ruta'));
        }
        return view($this->folderview.'.listacuentaspendientes')->with(compact('lista', 'ruta'));
    }

    public function cobrarcuentapendiente($id) {
        $id = explode('&', $id)[0];
        $existe = Libreria::verificarExistencia($id, 'Movimiento');
        $ruta = $this->rutas;
        if ($existe !== true) {
            return $existe;
        }
        //resumen de cuotas 
        $resumen = Movimiento::find($id);
        //ticket padre
        $movimiento = Movimiento::find($resumen->movimiento_id);  
        //Todas las cuotas que se han pagado
        $cuotas = Movimiento::where('movimiento_id', $resumen->id)->get();
        $serie=1;
        $entidad    = 'Movimiento';
        
        $formData   = array('caja.cobrarcuentapendiente2');
        $formData   = array('route' => $formData, 'method' => 'POST', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton      = 'Registrar';
        $sucursal_id = Session::get('sucursal_id');
        $cboCaja = Caja::where('nombre', '<>', 'TESORERIA')->where('nombre', '<>', 'FARMACIA')->where('nombre', '<>', 'TESORERIA - FARMACIA')->where('sucursal_id', $sucursal_id)->get();
        $cboFormaPago     = array("Efectivo" => "Efectivo", "Tarjeta" => "Tarjeta");
        $cboTipoTarjeta    = array("VISA" => "VISA", "MASTER" => "MASTER");
        $cboTipoTarjeta2    = array("CREDITO" => "CREDITO", "DEBITO" => "DEBITO");
        $cboTipoDocumento     = array("Boleta" => "Boleta", "Factura" => "Factura", "Ticket" => "Ticket");

        $detalles = Movimiento::select('detallemovcaja.id', 'movimiento.serie', 'movimiento.numero', 'cantidad', 'detallemovcaja.persona_id', 'descripcion', 'cantidad', 'detallemovcaja.precio', 'descuento', 'servicio.nombre', 'movimiento.movimiento_id', 'tipodescuento', 'descuento')
            ->join('detallemovcaja', 'movimiento.id', '=', 'detallemovcaja.movimiento_id')
            ->join('servicio', 'servicio.id', '=', 'detallemovcaja.servicio_id')
            ->where('movimiento.id', $movimiento->id)
            ->where('detallemovcaja.deleted_at', '=', null)
            ->get();

        return view($this->folderview.'.cobrarcuentapendiente')->with(compact('Caja', 'formData', 'entidad', 'boton', 'movimiento', 'cboFormaPago', 'cboTipoTarjeta', 'cboTipoTarjeta2', 'cboCaja', 'cboTipoDocumento', 'ruta', 'detalles', 'serie', 'cuotas', 'resumen'));
    }

    public function cobrarcuentapendiente2(Request $request) {

        $user = Auth::user();
        
        //sucursal_id
        $sucursal_id = Session::get('sucursal_id');

        $error = DB::transaction(function() use($request,$user, $sucursal_id,&$dat,&$numeronc){
            $Ticket = Movimiento::find($request->input('id'));

            //Solo si se paga el total

            if($request->input('quedan') == '0.000'){
                $Ticket->situacion='C';//Pendiente => P / Cobrado => C / Boleteado => B                
            } else {
                $Ticket->situacion='D';
            }

            //Guardamos el Ticket

            $Ticket->save();

            $comentario = 'PAGO DE CUOTA PARCIAL DE CLIENTE';
           
            $pagohospital=$Ticket->total;

            $caja = Caja::find($request->input('caja_id'));

            //Solo si se genera un comprobante de pago

            if($request->input('quedan') == '0.000'){
                if($pagohospital>0){//Puse con pago hospital por generar F.E.            
                    //Genero Documento de Venta
                    //Boleta
                    if($request->input('tipodocumento')=="Boleta"){
                        $tipodocumento_id=5;
                        $codigo="03";
                        $abreviatura="B";
                    }
                    //Factura
                    else if($request->input('tipodocumento')=="Factura"){
                        $tipodocumento_id=4;
                        $codigo="01";
                        $abreviatura="F";

                        //Algoritmo para la empresa

                        $empresa = Person::where('ruc', $request->input('cruc'))->first();

                        if(count($empresa) == 1) {
                            $idempresa = $empresa->id;
                        } else {
                            //Guardo la empresa como una nueva person
                            $nuevaempresa = new Person();
                            $nuevaempresa->ruc = $request->input('cruc');
                            $nuevaempresa->bussinesname = $request->input('crazon');
                            $nuevaempresa->direccion = $request->input('cdireccion');
                            $nuevaempresa->save();

                            $idempresa = $nuevaempresa->id;
                        }

                    } else {
                        $tipodocumento_id=12;
                        //$codigo="01";
                        $abreviatura="T";
                    }

                    //Genero venta como nuevo movimiento

                    $venta        = new Movimiento();
                    $venta->sucursal_id = $sucursal_id;
                    $venta->fecha = date("Y-m-d");

                    //Puede ser manual o no

                    $venta->numero= Movimiento::NumeroSigue($caja->id, $sucursal_id,4,$tipodocumento_id,$request->input('serieventa')+0,'N');
                    $venta->serie = '00'.$request->input('serieventa');

                    if($request->input('tipodocumento')=="Factura") {
                        $venta->empresa_id = $idempresa;
                    }
                    
                    $venta->responsable_id=$user->person_id;
                    $venta->persona_id=$Ticket->persona_id;
                    if($request->input('tipodocumento')=="Boleta"){
                        $venta->subtotal=number_format($pagohospital/1.18,2,'.','');
                        $venta->igv=number_format($pagohospital - $venta->subtotal,2,'.','');
                        $venta->total=$pagohospital;     
                    }else if($request->input('tipodocumento')=="Factura"){
                        $venta->subtotal=number_format($pagohospital/1.18,2,'.','');
                        $venta->igv=number_format($pagohospital - $venta->subtotal,2,'.','');
                        $venta->total=number_format($pagohospital,2,'.','');                     
                    } else {
                        $venta->subtotal=number_format($pagohospital,2,'.','');
                        $venta->igv=0;
                        $venta->total=number_format($pagohospital,2,'.','');  
                    }
                    $venta->tipomovimiento_id=4;
                    $venta->caja_id=$caja->id;
                    $venta->numero=$request->input('numeroventa');
                    $venta->serie=$request->input('serieventa');
                    $venta->tipodocumento_id=$tipodocumento_id;
                    $venta->comentario='';
                    $venta->manual='N';
                    $venta->situacion='N';        
                    $venta->movimiento_id=$Ticket->id;
                    $venta->ventafarmacia='N';

                    //Guardamos la venta

                    $venta->save();
                    $comentario = 'PAGO DE ÚLTIMA CUOTA PARCIAL DE CLIENTE - '.substr($request->input('tipodocumento'),0,1).' '.$venta->serie.'-'.$venta->numero;
                    //
                }
            }

            //Actualizo datos de resumen de cuotas

            $rc        = Movimiento::where('caja_id', $request->input('caja_id'))
                                            ->where('movimiento_id', $Ticket->id)
                                            ->where('situacion', 'Z')
                                            ->where('comentario', 'TOTAL DE CUOTAS')
                                            ->where('sucursal_id', $sucursal_id)->get();

            $rescuotas = Movimiento::find($rc[0]->id);

            if($request->input('totalpago') != '') {
                $rescuotas->total += $request->input('total2', 0);
            }
            if($request->input('efectivo') != '') {
                $rescuotas->totalpagado += $request->input('efectivo', 0);
            }
            if($request->input('visa') != '') {
                $rescuotas->totalpagadovisa += $request->input('visa', 0);
            }
            if($request->input('master') != '') {
                $rescuotas->totalpagadomaster += $request->input('master', 0);
            }

            $rescuotas->save();

            //Creo una nueva cuota

            $cuota        = new Movimiento();
            $cuota->sucursal_id = $sucursal_id;
            $cuota->fecha = date("Y-m-d");
            $cuota->numero= Movimiento::NumeroSigueCuota($request->input('caja_id'), $sucursal_id, 'Z',14);
            $cuota->numeroserie2= Movimiento::NumeroSigueSerieCuota($rescuotas->id);
            $cuota->responsable_id=$user->person_id;
            $cuota->persona_id=$Ticket->persona_id;
            $cuota->subtotal=0;
            $cuota->igv=0;
            $cuota->total = $request->input('total2', 0);
            $cuota->totalpagado = $request->input('efectivo', 0);
            $cuota->totalpagadovisa = $request->input('visa', 0);
            $cuota->totalpagadomaster = $request->input('master', 0);
            $cuota->numvisa = $request->input('numvisa');
            $cuota->nummaster = $request->input('nummaster');
            $cuota->tipomovimiento_id=14;//EN BLANCO, EL INGRESO SE HARÁ AL COMPLETAR LA BOLETA
            $cuota->situacion='Z';//EN BLANCO, EL INGRESO SE HARÁ AL COMPLETAR LA BOLETA
            $cuota->comentario='PAGO DE CUOTA PARCIAL DE CLIENTE';
            $cuota->caja_id=$request->input('caja_id');
            $cuota->movimiento_id=$rescuotas->id;
            $cuota->save();  

            //Creo el ingreso en caja

            $movimiento        = new Movimiento();
            $movimiento->sucursal_id = $sucursal_id;
            $movimiento->fecha = date("Y-m-d");
            $movimiento->numero= Movimiento::NumeroSigue($caja->id, $sucursal_id,2,2);
            $movimiento->responsable_id=$user->person_id;
            $movimiento->persona_id=$Ticket->persona_id;
            $movimiento->subtotal=0;
            $movimiento->igv=0;
            $cefectivo = $request->input('efectivo', 0);
            $cvisa = $request->input('visa', 0);
            $cmaster = $request->input('master', 0);
            if($request->input('efectivo', 0) == '') {
                $cefectivo = 0;
            }
            if($request->input('visa', 0) == '') {
                $cvisa = 0;
            }
            if($request->input('master', 0) == '') {
                $cmaster = 0;
            }
            $movimiento->total=$cefectivo+$cvisa+$cmaster;
            $movimiento->totalpagado = $request->input('efectivo', 0);
            $movimiento->totalpagadovisa = $request->input('visa', 0);
            $movimiento->totalpagadomaster = $request->input('master', 0);
            $movimiento->tipomovimiento_id=2;
            $movimiento->tipodocumento_id=2;
            $movimiento->conceptopago_id=3;//PAGO DE CLIENTE
            $movimiento->comentario=$comentario;
            $movimiento->caja_id=$request->input('caja_id');
            $movimiento->situacion='N';
            $movimiento->situacion2='Z'; //pARA IDENTIFICAR UNA CUOTA
            $movimiento->numeroserie2=$cuota->id;
            $movimiento->movimiento_id=$Ticket->id;
            $movimiento->save();
        });

        ///////////////////////////////
        return is_null($error) ? "OK" : $error;
    }

    public function anularmovimiento($id) {
        $Movcaja = Movimiento::find($id);
        $Tras = Movimiento::find($Movcaja->movimiento_id);
        $Ticket = Movimiento::find($Tras->movimiento_id);
        $Ticket->situacion = 'P';
        $Ticket->save();
        echo $Ticket->id;
    }

    public function pdfReciboCuota(Request $request){
        $cuota = Movimiento::where('id','=',$request->input('id'))->first();
        $rescuotas = Movimiento::where('id','=',$cuota->movimiento_id)->first();
        $lista = Movimiento::where('id','=',$rescuotas->movimiento_id)->first();

        $cuotas = Movimiento::where('movimiento_id','=',$rescuotas->id)->orderBy('numero')->get();

        $pdf = new TCPDF();
        $pdf::SetTitle('Recibo de pago de cuota N° ' . $cuota->numero);
        $pdf::AddPage();
        $pdf::SetFont('helvetica','B',15);
        $pdf::Cell(0,10,'RECIBO DE PAGO DE CUOTA N° ' . $cuota->numero,0,0,'C');
        $pdf::Ln();
        $pdf::Ln();
        //$pdf::SetFont('helvetica','B',10);
        //$pdf::Image("http://localhost/juanpablo/dist/img/logo.jpg", 0, 0, 35, 10);
        //$pdf::Image("http://localhost/juanpablo/dist/img/logo.jpg", 105, 0, 35, 10);

        $pdf::SetFont('helvetica','B',9);
        $pdf::Cell(20,7,"FECHA                                :   " . $lista->fecha,0,1,'L');

        $pdf::SetFont('helvetica','B',9);
        $pdf::Cell(20,7,"PACIENTE                          :   " . $lista->persona->apellidopaterno." ".$lista->persona->apellidomaterno." ".$lista->persona->nombres,0,1,'L');

        $pdf::SetFont('helvetica','B',9);
        $pdf::Cell(20,7,"TELEFONO                         :   " . $lista->persona->telefono,0,1,'L');

        $pdf::SetFont('helvetica','B',9);
        $pdf::Cell(20,7,"USUARIO                            :   " . $lista->responsable->nombres,0,1,'L');

        $pdf::SetFont('helvetica','B',9);
        $pdf::Cell(20,7,"COSTO DEL SERVICIO     :   S/." . number_format($lista->total,2,",",""),0,1,'L');

        $pdf::Ln();

        $pdf::SetFont('helvetica','B',13);
        $pdf::Cell(20,7,"HISTORIAL DE CUOTAS PAGADAS",0,1,'L');

        $pdf::Ln();

        $pdf::SetFont('helvetica','B',8);
        $pdf::Cell(15,6,utf8_decode("N° CUOTA"),1,0,'C');
        $pdf::Cell(25,6,utf8_decode("FECHA"),1,0,'C');
        $pdf::Cell(30,6,utf8_decode("ESTADO"),1,0,'C');
        $pdf::Cell(30,6,utf8_decode("EFECTIVO (S/.)"),1,0,'C');
        $pdf::Cell(30,6,utf8_decode("VISA (S/.)"),1,0,'C');
        $pdf::Cell(30,6,utf8_decode("MASTER (S/.)"),1,0,'C');
        $pdf::Cell(30,6,utf8_decode("SUBTOTAL (S/.)"),1,0,'C'); 

        $pdf::Ln();

        $totalpagado = 0.000;

        foreach($cuotas as $cuota) {
            if($cuota->situacion == 'A') {
                $sit = 'ANULADO';
            } else {
                $sit = 'PAGADO';
            }
            $pdf::Cell(15,6,$cuota->numero,1,0,'C');
            $pdf::Cell(25,6,$cuota->fecha,1,0,'C');
            $pdf::Cell(30,6,$sit,1,0,'C');
            $pdf::Cell(30,6,number_format($cuota->totalpagado,2,",",""),1,0,'C');
            $pdf::Cell(30,6,number_format($cuota->totalpagadovisa,2,",",""),1,0,'C');
            $pdf::Cell(30,6,number_format($cuota->totalpagadomaster,2,",",""),1,0,'C');  
            if($cuota->situacion != 'A') {          
                $pdf::Cell(30,6,number_format($cuota->totalpagado+$cuota->totalpagadovisa+$cuota->totalpagadomaster,2,",",""),1,0,'C');
            } else {
                $pdf::Cell(30,6,number_format(0.00,2,",",""),1,0,'C');
            }
            $pdf::Ln();
            if($cuota->situacion != 'A') {
                $totalpagado += $cuota->totalpagado+$cuota->totalpagadovisa+$cuota->totalpagadomaster;
            }            
        }

        $pdf::Cell(160,6,'TOTAL PAGADO (S/.)',1,0,'C');            
        $pdf::Cell(30,6,number_format($totalpagado,2,",",""),1,0,'C');

        $pdf::Ln();

        $pdf::Cell(160,6,'A CUENTA (S/.)',1,0,'C');            
        $pdf::Cell(30,6,number_format($lista->total-$totalpagado,2,",",""),1,0,'C');

        $pdf::Output('ReciboCaja.pdf');        
    }

    public function cirupro(Request $request) {
        return view($this->folderview.'.cirupro');
    }

    public function reporteCiruProConDetalle(Request $request) {
        $fecha1 = $request->input('fecha');
        $fecha = date("d/m/Y", strtotime($fecha1));
        $pdf = new TCPDF();
        $pdf::SetTitle('Reporte de cirujías y procedimientos con detalle ' . $fecha);
        $pdf::AddPage('L', 'A4');
        $pdf::SetFont('helvetica','B',15);
        $pdf::Cell(0,10,'REPORTE DE CIRUJÍAS Y PROCEDIMIENTOS DETALLADO ' . $fecha,0,0,'C');
        $pdf::Ln();
        $pdf::SetFont('helvetica','B',8);
        $pdf::Cell(14,7,utf8_decode("NRO"),1,0,'C');
        $pdf::Cell(60,7,utf8_decode("PACIENTE"),1,0,'C');
        $pdf::Cell(15,7,utf8_decode("DNI"),1,0,'C');
        $pdf::Cell(30,7,utf8_decode("DOCTOR"),1,0,'C');
        $pdf::Cell(15,7,utf8_decode("DESC"),1,0,'C');
        $pdf::Cell(60,7,utf8_decode("TIPO CIRUGIA / PROCEDIMIENTO"),1,0,'C');
        $pdf::Cell(14,7,utf8_decode("COSTO"),1,0,'C');
        $pdf::Cell(42,7,utf8_decode("DOC. SUSTENTATORIO"),1,0,'C');
        $pdf::Cell(14,7,utf8_decode("RECIBO"),1,0,'C');
        $pdf::Cell(14,7,utf8_decode("IMPORTE"),1,0,'C');
        $pdf::Ln();

        $pdf::Cell(208,7,utf8_decode(""),1,0,'C');
        $pdf::Cell(14,7,utf8_decode("FECHA"),1,0,'C');
        $pdf::Cell(14,7,utf8_decode("TIPO"),1,0,'C');
        $pdf::Cell(14,7,utf8_decode("NUMERO"),1,0,'C');
        $pdf::Cell(28,7,utf8_decode(""),1,0,'C');
        $pdf::Ln();

        //sucursal_id
        $sucursal_id = Session::get('sucursal_id');
        if($sucursal_id == 2) {
            $caja_id=2;
        } else {
            $caja_id=1;
        }  

        $resultadoventas = Movimiento::select('movimiento.id as idmov','movimiento.situacion', 'paciente.*')
                ->leftjoin('person as paciente', 'paciente.id', '=', 'movimiento.persona_id')
                //->where('movimiento.situacion','=','N')
                ->where('movimiento.sucursal_id', '=', $sucursal_id)
                ->where('movimiento.fecha', '=', $fecha1)
                ->where('movimiento.clasificacionconsulta','like','P');
        
        $listaventas = $resultadoventas->get();

        
        if(count($listaventas) > 0) {
            $i = 1;
            foreach ($listaventas as $row) {  

                $movimiento = Movimiento::where('movimiento_id', $row['idmov'])->orderBy('id', 'asc')->first();
                if($movimiento['situacion'] == 'N') {
                    //En este caso se ha pagado de frente todo => movimiento = comprobante de pago
                    $movcaja = Movimiento::where('movimiento_id', $movimiento['id'])->first();
                    if($movcaja['situacion'] != 'A') {
                        $detalles = Detallemovcaja::where('movimiento_id', $row['idmov'])->get();                                        
                        if(count($detalles) > 0) { 
                            $a = 0;
                            foreach ($detalles as $detalle) {
                                $pdf::SetFont('helvetica','',6);
                                $pdf::Cell(14,7,$i,1,0,'C');
                                $pdf::Cell(60,7,$row['apellidopaterno'] . ' ' . $row['apellidomaterno'] . ' ' . $row['nombres'],1,0,'L');
                                $pdf::Cell(15,7,$row['dni'],1,0,'C');
                                $pdf::Cell(30,7,utf8_decode($detalle->persona->apellidopaterno),1,0,'C');
                                if($detalle->tipodescuento == 'P') {
                                    $pdf::Cell(15,7,utf8_decode($detalle->descuento . '%'),1,0,'R'); 
                                } else {
                                    $pdf::Cell(15,7,utf8_decode('S/. ' . $detalle->descuento),1,0,'R'); 
                                }  
                                $pdf::Cell(60,7,substr($detalle->servicio->nombre,0,40) . '.',1,0,'L');  
                                $pdf::Cell(14,7,number_format($detalle->precio,2,',',''),1,0,'R');
                                if($a == 0) {
                                    $pdf::Cell(14,7*count($detalles),date("d/m/Y", strtotime($movimiento['fecha'])),1,0,'C');  
                                    $pdf::Cell(14,7*count($detalles),$movimiento->tipodocumento->abreviatura,1,0,'C');  
                                    $pdf::Cell(14,7*count($detalles),$movimiento['numero'],1,0,'C');
                                    $pdf::Cell(14,7*count($detalles),$movcaja['numero'],1,0,'C');
                                    $pdf::Cell(14,7*count($detalles),number_format($movimiento['total'],2,',',''),1,0,'R');
                                }  
                                $pdf::Cell(0,7,'',0,0,'C');                       
                                $pdf::Ln();                            
                                $a++;
                                $i++;
                            } 
                        } 
                    }
                } else {
                    //En este caso se ha pagado en cuotas => movimiento = resumen de cuotas
                    $movimientoscuotas = Movimiento::where('movimiento_id', $row['idmov'])->where('situacion2', 'Z')->get();
                    if(count($movimientoscuotas) > 0) {
                        $numeroscuotas = '';
                        $e = 1;
                        foreach ($movimientoscuotas as $cuota) {
                            if($cuota['situacion'] != 'A') {
                                if($e == 1) {
                                    $numeroscuotas .= 'C';
                                }
                                $pcuota = Movimiento::find($cuota['numeroserie2']);
                                $numeroscuotas .= $pcuota['numero'] . '/';
                                $e++;
                            }
                        }
                        $numeroscuotas = substr($numeroscuotas, 0, strlen($numeroscuotas) - 1);
                        $detalles = Detallemovcaja::where('movimiento_id', $row['idmov'])->get();                                        
                        if(count($detalles) > 0) {
                            $a = 0;
                            foreach ($detalles as $detalle) {
                                $pdf::SetFont('helvetica','',6);
                                $pdf::Cell(14,7,$i,1,0,'C');
                                $pdf::Cell(60,7,$row['apellidopaterno'] . ' ' . $row['apellidomaterno'] . ' ' . $row['nombres'],1,0,'L');
                                $pdf::Cell(15,7,$row['dni'],1,0,'C');
                                $pdf::Cell(30,7,utf8_decode($detalle->persona->apellidopaterno),1,0,'C');
                                if($detalle->tipodescuento == 'P') {
                                    $pdf::Cell(15,7,utf8_decode($detalle->descuento . '%'),1,0,'R'); 
                                } else {
                                    $pdf::Cell(15,7,utf8_decode('S/. ' . $detalle->descuento),1,0,'R'); 
                                }  
                                $pdf::Cell(60,7,substr($detalle->servicio->nombre,0,40) . '.',1,0,'L');  
                                $pdf::Cell(14,7,number_format($detalle->precio,2,',',''),1,0,'R');
                                if($a == 0) {
                                    //Compruebo si ya se terminó de pagar todo
                                    if($row['situacion'] == 'C') {
                                        //Busco el comprobante de pago
                                        $comprobante = Movimiento::where('movimiento_id', $row['idmov'])->where('ventafarmacia', 'N')->first();
                                        $pdf::Cell(14,7*count($detalles),date("d/m/Y", strtotime($comprobante['fecha'])),1,0,'C');  
                                        $pdf::Cell(14,7*count($detalles),$comprobante->tipodocumento->abreviatura,1,0,'C');  
                                        $pdf::Cell(14,7*count($detalles),$comprobante['numero'],1,0,'C');
                                    } else {
                                        $pdf::Cell(14,7*count($detalles),'-',1,0,'C');  
                                        $pdf::Cell(14,7*count($detalles),'-',1,0,'C');  
                                        $pdf::Cell(14,7*count($detalles),'-',1,0,'C');
                                    }                                        
                                    $pdf::Cell(14,7*count($detalles),$numeroscuotas,1,0,'C');
                                    $pdf::Cell(14,7*count($detalles),number_format($movimiento['total'],2,',',''),1,0,'R');
                                }  
                                $pdf::Cell(0,7,'',0,0,'C');                       
                                $pdf::Ln();                            
                                $a++;
                                $i++;
                            } 
                        }

                    }
                }
                $row2 = Movimiento::where('movimiento_id', $movimiento['id'])->limit(1)->first();
                    
            } 
        }

        $pdf::Output('ReciboCaja.pdf'); 
    }

    public function reporteCiruProSinDetalle(Request $request) {
        $fecha1 = $request->input('fecha');
        $fecha = date("d/m/Y", strtotime($fecha1));
        $sucursal_id = Session::get('sucursal_id');
        if($sucursal_id == 2) {
            $caja_id=2;
        } else {
            $caja_id=1;
        }

        /*$rs = Movimiento::leftjoin('movimiento as m2','movimiento.movimiento_id','=','m2.id')
                ->leftjoin('person as paciente', 'paciente.id', '=', 'movimiento.persona_id')
                //->where('movimiento.situacion','=','N')
                ->where('movimiento.ventafarmacia','=','N')
                ->where('movimiento.sucursal_id', '=', $sucursal_id)
                ->where('m2.fecha', '=', $fecha1)
                ->where('movimiento.caja_id', '=', $caja_id);*/

         $rs = Movimiento::leftjoin('movimiento as m2','movimiento.movimiento_id','=','m2.id')
                        ->where('m2.fecha', $fecha1)
                        ->where('m2.clasificacionconsulta','like','P')
                        ->where(function($q) {            
                            $q->where('m2.situacion', 'like', 'C')->orWhere('m2.situacion', 'like', 'D')->orWhere('m2.situacion', 'like', 'R');
                        })
                        ->where('m2.tipomovimiento_id','=',1)
                        ->where('movimiento.situacion2','!=','Z')
                        ->where('movimiento.sucursal_id', '=', $sucursal_id)
                        ->where('movimiento.caja_id', '=', $caja_id)
                        ->groupBy('m2.id');
        
        $rs = $rs->select('m2.*')->get();

        $pdf = new TCPDF();
        $pdf::SetTitle('Reporte de cirujías y procedimientos sin detalle ' . $fecha);
        $pdf::AddPage('L', 'A4');

        $pdf::SetFont('helvetica','B',15);
        $pdf::Cell(0,10,'REPORTE DE CIRUJÍAS Y PROCEDIMIENTOS GENERALES ' . $fecha,0,0,'C');
        $pdf::Ln();

        $pdf::SetFont('helvetica','B',8);
        
        foreach ($rs as $value) {
            $x = $pdf::GetY();

            if($x > 140){
                $pdf::AddPage(); 
            }

            $pdf::Cell(60,10,utf8_decode("PACIENTE"),1,0,'C');
            $pdf::Cell(20,10,utf8_decode("DESCUENTO"),1,0,'C');
            $pdf::MultiCell(110, 10,'TIPO DE CIRUJIA / PROCEDIMIENTO', 1, 'C', 0, 0, '', '', true, 0, false, true, 10, 'M');
            $pdf::MultiCell(20, 10,'COSTO TOTAL (S/.)', 1, 'C', 0, 0, '', '', true, 0, false, true, 10, 'M');
            $pdf::Cell(20,10,utf8_decode("FECHA"),1,0,'C'); 
            $pdf::Cell(15,10,utf8_decode("SI / NO"),1,0,'C'); 
            $pdf::Cell(30,10,utf8_decode("FIRMA"),1,0,'C'); 

/*
            $pdf::MultiCell(60, 12,utf8_decode("PACIENTE"), 1, 'C', 0, 0, '', '', true, 0, false, true,  12  , 'M');
                //$pdf::Cell(20,10 * $cont,$descuento,1,0,'C');
            $pdf::MultiCell(20, 12,utf8_decode("DESCUENTO"), 1, 'C', 0, 0, '', '', true, 0, false, true,  12  , 'M');
            $pdf::MultiCell(110, 12 ,utf8_decode("TIPO DE CIRUJIA / PROCEDIMIENTO"), 1, 'C', 0, 0, '', '', true, 0, false, true,  12 , 'M');
            $pdf::MultiCell(20, 12 ,utf8_decode("COSTO TOTAL (S/.)"), 1, 'C', 0, 0, '', '', true, 0, false, true,  12, 'M');
            $pdf::MultiCell(20, 12,utf8_decode("FECHA"), 1, 'C', 0, 0, '', '', true, 0, false, true,  12, 'M');
            //$pdf::Cell(20,12 * ($cont - 1),date('d/m/Y' , strtotime($value->fecha) ),1,0,'C'); 
                //$pdf::Cell(15,12 * ($cont - 1),utf8_decode("SI"),1,0,'C'); 
            $pdf::MultiCell(15, 12 ,utf8_decode("SI / NO"), 1, 'C', 0, 0, '', '', true, 0, false, true,  12 , 'M');
            $pdf::MultiCell(30, 12,utf8_decode("FIRMA"), 1, 'C', 0, 0, '', '', true, 0, false, true,  12 , 'M');
*/
            $pdf::Ln();
            $cont = 1;
            $detalles = Detallemovcaja::where('movimiento_id','=', $value->id )->get();
            $tipo ="";
            $descuento = "";
            foreach ($detalles as $detalle) {
                $servicio = Servicio::find($detalle->servicio_id);
                if($cont == 1){
                    $tipo.= $cont . " - " . $servicio->nombre;   
                    if($detalle->tipodescuento == "P"){
                        $descuento .= $cont . " - " . $detalle->descuento . "%";
                    }else{
                        $descuento .= $cont . " - S/. " . $detalle->descuento;
                    }
                }else{
                    $tipo.= "\n\n" . $cont . " - " . $servicio->nombre;
                   if($detalle->tipodescuento == "P"){
                        $descuento .= "\n\n" . $cont . " - " . $detalle->descuento . "%";
                    }else{
                        $descuento .= "\n\n" . $cont . " - S/. " . $detalle->descuento;
                    }
                }
                $cont++;
            }

            if($cont == 2){
                $pdf::MultiCell(60, 9 * $cont ,$value->persona->apellidopaterno." ".$value->persona->apellidomaterno." ".$value->persona->nombres, 1, 'L', 0, 0, '', '', true, 0, false, true,  9 * $cont , 'M');
                //$pdf::Cell(20,9 * $cont,$descuento,1,0,'C');
                $pdf::MultiCell(20, 9 * $cont ,$descuento, 1, 'L', 0, 0, '', '', true, 0, false, true,  9 * $cont , 'M');
                $pdf::MultiCell(110, 9 * $cont ,$tipo, 1, 'L', 0, 0, '', '', true, 0, false, true,  9 * $cont , 'M');
                $pdf::MultiCell(20, 9 * $cont ,number_format($value->total,2,",",""), 1, 'C', 0, 0, '', '', true, 0, false, true,  9 * $cont , 'M');
                $pdf::MultiCell(20, 9 * $cont ,date('d/m/Y' , strtotime($value->fecha) ), 1, 'C', 0, 0, '', '', true, 0, false, true,  9 * $cont , 'M');
                //$pdf::Cell(20,9 * ($cont - 1),date('d/m/Y' , strtotime($value->fecha) ),1,0,'C'); 
                if($value->situacion =="C"){
                    //$pdf::Cell(15,9 * ($cont - 1),utf8_decode("SI"),1,0,'C'); 
                    $pdf::MultiCell(15, 9 * $cont ,utf8_decode("SI"), 1, 'C', 0, 0, '', '', true, 0, false, true,  9 * $cont , 'M');
                }else if($value->situacion == "D"){
                    $pdf::MultiCell(15, 9 * $cont ,utf8_decode("NO"), 1, 'C', 0, 0, '', '', true, 0, false, true,  9 * $cont , 'M');
                }else{
                    $pdf::MultiCell(15, 9 * $cont ,utf8_decode("NO"), 1, 'C', 0, 0, '', '', true, 0, false, true,  9 * $cont , 'M');
                }
                $pdf::MultiCell(30, 9 * $cont ,"", 1, 'C', 0, 0, '', '', true, 0, false, true,  9 * ($cont -1) , 'M');
            }else{
                $pdf::MultiCell(60, 9 * ($cont - 1) ,$value->persona->apellidopaterno." ".$value->persona->apellidomaterno." ".$value->persona->nombres, 1, 'L', 0, 0, '', '', true, 0, false, true,  9 * ($cont -1) , 'M');
                $pdf::MultiCell(20, 9 * ($cont -1) ,$descuento, 1, 'L', 0, 0, '', '', true, 0, false, true,  9 * ($cont -1) , 'M');
                $pdf::MultiCell(110, 9 * ($cont - 1) ,$tipo, 1, 'L', 0, 0, '', '', true, 0, false, true,  9 * ($cont -1) , 'M');
                //$pdf::Cell(20,9 * ($cont - 1),number_format($value->total,2,",",""),1,0,'C');
                $pdf::MultiCell(20, 9 * ($cont - 1) ,number_format($value->total,2,",",""), 1, 'C', 0, 0, '', '', true, 0, false, true,  9 * ($cont -1) , 'M');
                $pdf::MultiCell(20, 9 * ($cont - 1) ,date('d/m/Y' , strtotime($value->fecha) ), 1, 'C', 0, 0, '', '', true, 0, false, true,  9 * ($cont -1) , 'M');
                //$pdf::Cell(20,9 * ($cont - 1),date('d/m/Y' , strtotime($value->fecha) ),1,0,'C'); 
                if($value->situacion =="C"){
                    //$pdf::Cell(15,9 * ($cont - 1),utf8_decode("SI"),1,0,'C'); 
                    $pdf::MultiCell(15, 9 * ($cont - 1) ,utf8_decode("SI"), 1, 'C', 0, 0, '', '', true, 0, false, true,  9 * ($cont -1) , 'M');
                }else if($value->situacion == "D"){
                    $pdf::MultiCell(15, 9 * ($cont - 1) ,utf8_decode("NO"), 1, 'C', 0, 0, '', '', true, 0, false, true,  9 * ($cont -1) , 'M');
                }else{
                    $pdf::MultiCell(15, 9 * ($cont - 1) ,utf8_decode("NO"), 1, 'C', 0, 0, '', '', true, 0, false, true,  9 * ($cont -1) , 'M');
                }
                $pdf::MultiCell(30, 9 * ($cont - 1) ,"", 1, 'C', 0, 0, '', '', true, 0, false, true,  9 * ($cont -1) , 'M');
            }
            //$pdf::Cell(60,9 * ($cont - 1),$value->persona->apellidopaterno." ".$value->persona->apellidomaterno." ".$value->persona->nombres,1,0,'L');
            //$pdf::Ln();
            $pdf::Ln();
        }

        $pdf::Output('ReporteCirujiasSinDetalle.pdf');   

    }

    public function pdfDetalleEgresos(Request $request) {
        setlocale(LC_TIME, 'spanish');
        $caja    = Caja::find($request->input('caja_id'));
        $caja_id = Libreria::getParam($request->input('caja_id'),'1');

        $user=Auth::user();
        $responsable = $user->login;

        $fi = Libreria::getParam($request->input('fi'),'1');
        $ff = Libreria::getParam($request->input('ff'),'1');

        //sucursal_id
        $sucursal_id = Session::get('sucursal_id');
        $nomcierre = 'Egresos';
        $nomcierre = 'Clínica Especialidades'; 
        if($sucursal_id == 1) {
            $nomcierre = 'BM Clínica de Ojos';
        }  
        if($caja->nombre == 'FARMACIA') {
            $nomcierre = ' Farmacia - ' . $nomcierre;
        } 

        $nomcierre = substr($nomcierre, 0, 20);

        //Consulta

        $egresos        = Movimiento::join('conceptopago','conceptopago.id','=','movimiento.conceptopago_id')
            ->where('movimiento.situacion','=','N')
            ->where('movimiento.caja_id', '=', $caja_id)
            ->where('movimiento.sucursal_id','=',$sucursal_id)
            ->whereNull('movimiento.cajaapertura_id')
            ->whereBetween('movimiento.fecha', [$fi, $ff])
            ->Where('conceptopago.tipo', '=', 'E')
            ->whereNotIn('movimiento.conceptopago_id',[31, 2])
            ->where('movimiento.situacion2', '=', 'Q')
            ->select('conceptopago.id', 'conceptopago.nombre')
            ->groupBy('conceptopago.id')
            ->orderBy('conceptopago.id', 'DESC')
            ->get();

        $aperturas = Movimiento::where('conceptopago_id', 1)->where('caja_id', $caja_id)->where('sucursal_id', $sucursal_id)->whereBetween('fecha', [$fi, $ff])->get();

        //Comprobamos si la ultima fecha tiene cierre

        $cierrefinal = Movimiento::where('conceptopago_id', 2)->where('caja_id', $caja_id)->where('sucursal_id', $sucursal_id)->where('fecha', '=', $ff)->get();

        $numcajas = count($aperturas);
        if(count($cierrefinal) == 0) {
            //Si no hay cierre en la ultima fecha, no considero la ultima caja
            $numcajas--;
        }

        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        
        Excel::create('ExcelReporte', function($excel) use($egresos, $aperturas, $numcajas, $caja_id, $sucursal_id, $nomcierre) {

            $excel->sheet("Det. Egresos " . $nomcierre, function ($sheet) use ($egresos, $aperturas, $numcajas, $caja_id, $sucursal_id) {
                $sheet->loadView('app.rpts.egresos')->with(compact('egresos', 'aperturas', 'numcajas', 'caja_id', 'sucursal_id'));
            });

        })->export('xls');
    }

    
    public function pdfDetallePorProducto(Request $request){
        
        $caja    = Caja::find($request->input('caja_id'));
        $caja_id = Libreria::getParam($request->input('caja_id'),'1');

        $fi = Libreria::getParam($request->input('fi'),'1');
        $ff = Libreria::getParam($request->input('ff'),'1');

        $user=Auth::user();
        $responsable = $user->login;
        $responsable_nombre = $user->person->apellidos . " " . $user->person->nombres;

        $totalvisa     = 0;
        $totalmaster   = 0;
        $totalefectivo = 0;
        $totalegresos  = 0;
        $subtotalegresos = 0;

        //sucursal_id
        $sucursal_id = Session::get('sucursal_id');
        $nomcierre = '';
        $nomcierre = 'Clínica Especialidades'; 
        if($sucursal_id == 1) {
            $nomcierre = 'BM Clínica de Ojos';
        }  
        if($caja->nombre == 'FARMACIA') {
            $nomcierre = ' Farmacia - ' . $nomcierre;
        }     
        $pdf = new TCPDF();
        //$pdf::SetIma�
        $pdf::SetTitle('Detalle Cierre por Producto de '.$nomcierre);
        $pdf::AddPage('L');
        $pdf::SetFont('helvetica','B',12);
        $pdf::Cell(0,10,"Detalle de Cierre por Producto de ".$nomcierre,0,0,'C');
        $pdf::Ln();
        $pdf::SetFont('helvetica','B',7);
        $pdf::Cell(15,7,utf8_decode("FECHA"),1,0,'C');
        $pdf::Cell(56,7,utf8_decode("PERSONA"),1,0,'C');
        $pdf::Cell(20,7,utf8_decode("NRO"),1,0,'C');
        $pdf::Cell(64,7,utf8_decode("PRODUCTO"),1,0,'C');
        $pdf::Cell(25,7,utf8_decode("DESCUENTO"),1,0,'C');
        $pdf::Cell(25,7,utf8_decode("ESTADO"),1,0,'C');
        //$pdf::Cell(14,7,utf8_decode("PRECIO"),1,0,'C');
        //$pdf::Cell(14,7,utf8_decode("EGRESO"),1,0,'C');
        //$pdf::Cell(56,7,utf8_decode("INGRESO"),1,0,'C');
        $pdf::Cell(25,7,utf8_decode("FORMA DE PAGO"),1,0,'C');
        $pdf::Cell(20,7,utf8_decode("PRECIO"),1,0,'C');
        $pdf::Cell(31,7,utf8_decode("DOCTOR"),1,0,'C');
        $pdf::Ln();/*
        $pdf::Cell(205,7,utf8_decode(""),1,0,'C');
        $pdf::Cell(14,7,utf8_decode("EFECTIVO"),1,0,'C');
        $pdf::Cell(14,7,utf8_decode("VISA"),1,0,'C');
        $pdf::Cell(14,7,utf8_decode("MASTER"),1,0,'C');
        $pdf::Cell(14,7,utf8_decode("TOTAL"),1,0,'C');
        $pdf::Cell(20,7,utf8_decode(""),1,0,'C');
        $pdf::Ln();*/
        if($caja_id==1){//ADMISION 1
            $serie=3;
        }elseif($caja_id==2){//ADMISION 2
            $serie=7;
        }elseif($caja_id==3){//CONVENIOS
            $serie=8;
        }elseif($caja_id==5){//EMERGENCIA
            $serie=9;
        }elseif($caja_id==4){//FARMACIA
            $serie=4;
        }/*elseif($caja_id==8){//PROCEDIMIENTOS
            $serie=5;
        }*/

        $ingreso=0;
        $egreso=0;
        $transferenciai=0;
        $transferenciae=0;
        $garantia=0;
        $efectivo=0;
        $visa=0;
        $master=0;

        //Solo para ventas de farmacia

        $listaventasfarmacia = Movimiento::leftjoin('movimiento as m2','movimiento.movimiento_id','=','m2.id')
                ->leftjoin('person as paciente', 'paciente.id', '=', 'movimiento.persona_id')
                ->leftjoin('conceptopago','conceptopago.id','=','movimiento.conceptopago_id')
                ->where('movimiento.sucursal_id', '=', $sucursal_id)
                ->where('movimiento.caja_id', '=', $caja_id)
                ->whereBetween('movimiento.fecha', [$fi, $ff])
                ->where('movimiento.ventafarmacia', '=', 'S');
        $listaventasfarmacia = $listaventasfarmacia->select('movimiento.situacion','movimiento.doctor_id','movimiento.serie','movimiento.id','movimiento.nombrepaciente','movimiento.voucher','movimiento.formapago','movimiento.comentario','movimiento.fecha','movimiento.numero','movimiento.total','movimiento.totalpagado','movimiento.totalpagadovisa','movimiento.totalpagadomaster','m2.numero as numeroticket',DB::raw('case when paciente.bussinesname is null then concat(paciente.apellidopaterno,\' \',paciente.nombres) else paciente.bussinesname end as paciente'), 'movimiento.total')->orderBy('movimiento.numero', 'asc');
        
        $listaventasfarmacia = $listaventasfarmacia->get();

        if(count($listaventasfarmacia)>0){
            $pdf::SetFont('helvetica','B',8.5);
            //$pdf::Cell(281,7,'INGRESOS POR VENTAS',1,0,'L');
            //$pdf::Ln();
            $subtotalefectivo = 0;
            $subtotalvisa = 0;
            $subtotalmaster = 0;
            foreach ($listaventasfarmacia as $row) { 
                $mov = Movimiento::where('movimiento_id', $row['id'])->limit(1)->first();
                //aquí detalle
                $detalleproductos = Detallemovimiento::where('movimiento_id', $row['id'])->get();
                //fin detalle
                foreach ($detalleproductos as $value) {
                    $producto = $value->producto->nombre;
                    $precio = number_format($value->subtotal,2,'.','');
                    
                    $pdf::SetFont('helvetica','',6);                   
                    $pdf::Cell(15,7,utf8_decode($row['fecha']),1,0,'C');
                    if($row['paciente'] == '') {
                        $pdf::Cell(56,7,$row['nombrepaciente'],1,0,'L');
                    } else {
                        $pdf::Cell(56,7,$row['paciente'],1,0,'L');
                    }                
                    $pdf::Cell(8,7,$mov->tipodocumento->abreviatura,1,0,'C');
                    $pdf::Cell(12,7,utf8_decode($row['serie'] . '-' . $row['numero']),1,0,'C');
                    //$pdf::Cell(64,7,$mov->conceptopago->nombre.': '.$row['comentario'],1,0,'L'); 
                    $pdf::Cell(64,7,$producto,1,0,'L'); 
                    $pdf::Cell(25,7,utf8_decode(" - "),1,0,'C');
                    if($row['situacion'] == 'N') {
                        $pdf::Cell(25,7,utf8_decode("EMITIDO"),1,0,'C');
                    } else {
                        $pdf::Cell(25,7,utf8_decode("ANULADO"),1,0,'C');
                    } 
                    if($row['situacion'] == 'N') {
                        $valuetp = number_format($row['totalpagado'],2,'.','');
                        $valuetpv = number_format($row['totalpagadovisa'],2,'.','');
                        $valuetpm = number_format($row['totalpagadomaster'],2,'.','');
                        $valuet = number_format($row['total'],2,'.','');
                        $formapago = "";
                        if($valuetp == 0){$valuetp='';}else{ $formapago .= " - E";}
                        if($valuetpv == 0){$valuetpv='';}else{ $formapago .= " - V";}
                        if($valuetpm == 0){$valuetpm='';}else{  $formapago .= " - M";}
                        $pdf::Cell(25,7,$formapago,1,0,'L');                    
                        //$pdf::Cell(14,7,$valuetpv,1,0,'R');
                        //$pdf::Cell(14,7,$valuetpm,1,0,'R');
                        $pdf::Cell(20,7,$precio,1,0,'R');
                    } else {
                        $pdf::Cell(42,7,'ANULADO',1,0,'C');
                    } 
                    if($row['doctor_id'] != '') {
                        $pdf::Cell(31,7,$row->doctor->apellidopaterno,1,0,'C');
                    } else {
                        $pdf::Cell(31,7,utf8_decode("-"),1,0,'C');
                    }                
                    $pdf::Ln();
                    
                }
                if($row['situacion'] == 'N') {
                    $totalvisa        += number_format($row['totalpagadovisa'],2,'.','');
                    $totalmaster      += number_format($row['totalpagadomaster'],2,'.','');
                    $totalefectivo    += number_format($row['totalpagado'],2,'.','');
                    $subtotalefectivo += number_format($row['totalpagadovisa'],2,'.','');
                    $subtotalvisa     += number_format($row['totalpagadomaster'],2,'.','');
                    $subtotalmaster   += number_format($row['totalpagado'],2,'.','');
                }
            } 
            $pdf::SetFont('helvetica','B',8.5);
            $pdf::Cell(230,7,'TOTAL',1,0,'R');
            //$pdf::Cell(14,7,number_format(0,2,'.',''),1,0,'R');
            $pdf::Cell(20,7,number_format($subtotalefectivo+$subtotalvisa+$subtotalmaster,2,'.',''),1,0,'R');
            $pdf::Ln();                 
        }

        $pdf::SetFont('helvetica','',7);   
        $pdf::Ln();
        $pdf::Ln();
        $pdf::Cell(120,7,('RESPONSABLE: '.$responsable_nombre),0,0,'L');
        $pdf::SetFont('helvetica','B',9);
        $pdf::Cell(50,7,utf8_decode("RESUMEN DE CAJA"),1,0,'C');
        $pdf::Ln();
        $pdf::Cell(120,7,utf8_decode(""),0,0,'C');
        $pdf::Cell(30,7,utf8_decode("INGRESOS :"),1,0,'L');
        $pdf::Cell(20,7,number_format($totalefectivo + $totalmaster + $totalvisa,2,'.',''),1,0,'R');
        $pdf::Ln();
        $pdf::Cell(120,7,utf8_decode(""),0,0,'C');
        $pdf::Cell(30,7,utf8_decode("Efectivo :"),1,0,'L');
        $pdf::Cell(20,7,number_format($totalefectivo,2,'.',''),1,0,'R');
        $pdf::Ln();
        $pdf::Cell(120,7,utf8_decode(""),0,0,'C');
        $pdf::Cell(30,7,utf8_decode("Master :"),1,0,'L');
        $pdf::Cell(20,7,number_format($totalmaster,2,'.',''),1,0,'R');
        $pdf::Ln();
        $pdf::Cell(120,7,utf8_decode(""),0,0,'C');
        $pdf::Cell(30,7,utf8_decode("Visa :"),1,0,'L');
        $pdf::Cell(20,7,number_format($totalvisa,2,'.',''),1,0,'R');
        $pdf::Ln();
        $pdf::Cell(120,7,utf8_decode(""),0,0,'C');
        $pdf::Cell(30,7,utf8_decode("EGRESOS :"),1,0,'L');
        $pdf::Cell(20,7,number_format($subtotalegresos,2,'.',''),1,0,'R');
        $pdf::Ln();
        $pdf::Cell(120,7,utf8_decode(""),0,0,'C');
        $pdf::Cell(30,7,utf8_decode("SALDO :"),1,0,'L');
        $pdf::Cell(20,7,number_format($totalefectivo + $totalmaster + $totalvisa - $subtotalegresos,2,'.',''),1,0,'R');
        $pdf::Ln();
        /*$pdf::Cell(120,7,utf8_decode(""),0,0,'C');
        $pdf::Cell(30,7,utf8_decode("GARANTIA :"),1,0,'L');
        $pdf::Cell(20,7,number_format($garantia,2,'.',''),1,0,'R');*/
        $pdf::Ln();
        $pdf::Output('ListaCaja.pdf');
    }

}