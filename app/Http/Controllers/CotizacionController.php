<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Validator;
use App\Http\Requests;
use App\Cotizacion;

use App\Convenio;
use App\Movimiento;
use App\Detallemovcaja;
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
    protected $tituloRegistrar = 'Registrar Cotizacion';
    protected $tituloModificar = 'Modificar Cotizacion';
    protected $tituloEliminar  = 'Eliminar Cotizacion';
    protected $rutas           = array('create' => 'cotizacion.create', 
            'edit'   => 'cotizacion.edit', 
            'delete' => 'cotizacion.eliminar',
            'search' => 'cotizacion.buscar',
            'index'  => 'cotizacion.index'
        );

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function buscar(Request $request)
    {
        $pagina           = $request->input('page');
        $filas            = $request->input('filas');
        $entidad          = 'Facturacion';
        $paciente         = Libreria::getParam($request->input('paciente'),'');
        $codigo           = Libreria::getParam($request->input('codigo'),'');
        $fecha            = Libreria::getParam($request->input('fechainicial'));
        $fecha2           = Libreria::getParam($request->input('fechafinal'));
        $tipo             = Libreria::getParam($request->input('tipo'));
        $situacion        = Libreria::getParam($request->input('situacion'));
        $user = Auth::user();
        $resultado        = Cotizacion::leftjoin('person as paciente', 'paciente.id', '=', 'cotizacion.paciente_id')
                            ->leftjoin('person as responsable','responsable.id','=','cotizacion.responsable_id')
                            ->where(DB::raw('concat(paciente.apellidopaterno,\' \',paciente.apellidomaterno,\' \',paciente.nombres)'), 'LIKE', '%'.strtoupper($paciente).'%')
                            ->where('cotizacion.codigo','LIKE','%'.$codigo.'%');
        if($fecha!=""){
            $resultado = $resultado->where('cotizacion.fecha', '>=', ''.$fecha.'');
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
        $resultado        = $resultado->select('cotizacion.*',DB::raw('concat(paciente.apellidopaterno,\' \',paciente.apellidomaterno,\' \',paciente.nombres) as paciente'))->orderBy('cotizacion.fecha', 'ASC');
        $lista            = $resultado->get();
        $cabecera         = array();
        $cabecera[]       = array('valor' => '#', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Fecha', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Código', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Paciente', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Tipo', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Situación', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Responsable', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Operaciones', 'numero' => '2');
        
        $titulo_modificar = $this->tituloModificar;
        $titulo_eliminar  = $this->tituloEliminar;
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
            return view($this->folderview.'.list')->with(compact('lista', 'totalfac', 'paginacion', 'inicio', 'fin', 'entidad', 'cabecera', 'titulo_modificar', 'titulo_eliminar', 'ruta'));
        }
        return view($this->folderview.'.list')->with(compact('lista', 'entidad'));
    }

    public function index()
    {
        $entidad          = 'Facturacion';
        $title            = $this->tituloAdmin;
        $titulo_registrar = $this->tituloRegistrar;
        $ruta             = $this->rutas;
        $user = Auth::user();
        return view($this->folderview.'.admin')->with(compact('entidad', 'title', 'titulo_registrar', 'ruta', 'user'));
    }

    public function create(Request $request)
    {
        $listar              = Libreria::getParam($request->input('listar'), 'NO');
        $entidad             = 'Facturacion';
        $facturacion = null;
        $cboConvenio = array();
        $convenios = Convenio::where(DB::raw('1'),'=','1')->orderBy('nombre','ASC')->get();
        foreach ($convenios as $key => $value) {
            $cboConvenio = $cboConvenio + array($value->id => $value->nombre);
        }
        $cboTipoServicio = array(""=>"--Todos--");
        $tiposervicio = Tiposervicio::where(DB::raw('1'),'=','1')->orderBy('nombre','ASC')->get();
        foreach ($tiposervicio as $key => $value) {
            $cboTipoServicio = $cboTipoServicio + array($value->id => $value->nombre);
        }
        $formData            = array('facturacion.store');
        $cboSerie     = array("002" => "002", "008" => "008");
        $user = Auth::user();
        if($user->id==41){
            $numeroventa = Movimiento::NumeroSigue2(9,17,8,'N');    
        }else{
            $numeroventa = Movimiento::NumeroSigue(9,17,2,'N');
        }
        
        $formData            = array('route' => $formData, 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton               = 'Registrar'; 
        return view($this->folderview.'.mant')->with(compact('facturacion', 'formData', 'entidad', 'boton', 'listar', 'cboConvenio', 'cboSerie', 'numeroventa', 'cboTipoServicio', 'user'));
    }

    public function store(Request $request)
    {
        $listar     = Libreria::getParam($request->input('listar'), 'NO');
        $reglas     = array(
                'fecha'                  => 'required',
                'numeroventa'          => 'required',
                'paciente'          => 'required',
                'total'         => 'required',
                'plan'          => 'required',
                );
        $mensajes = array(
            'fecha.required'         => 'Debe seleccionar una fecha',
            'numeroventa.required'         => 'La factura debe tener un numero',
            'paciente.required'         => 'Debe seleccionar un paciente',
            'plan.required'         => 'Debe seleccionar un plan',
            'total.required'         => 'Debe agregar detalle a la factura',
            );
        $validacion = Validator::make($request->all(), $reglas, $mensajes);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        }       
        
        $user = Auth::user();
        $dat=array();
        $serie=($request->input('serieventa') + 0);
        if($serie==8){
            $numeroventa = Movimiento::NumeroSigue2(9,17,$serie,'N');
        }else{
            $numeroventa = Movimiento::NumeroSigue(9,17,$serie,'N');
        }
        $numero="F".str_pad($request->input('serieventa'),3,'0',STR_PAD_LEFT).'-'.$numeroventa;
        $error = DB::transaction(function() use($request,$user,$numeroventa,$numero,&$dat){
            $venta        = new Movimiento();
            $venta->fecha = $request->input('fecha');
            $venta->fechaingreso = $request->input('fechaingreso');
            $venta->fechaalta = $request->input('fechasalida');
            $venta->numero= $numeroventa;
            $venta->serie = $request->input('serieventa');
            $venta->responsable_id=$user->person_id;
            $venta->cie_id=$request->input('cie_id');
            $venta->comentario=$request->input('siniestro');
            $venta->soat = $request->input('soat');
            $venta->uci = $request->input('uci');
            $venta->plan_id = $request->input('plan_id');
            $venta->persona_id = $request->input('person_id');
            $paciente = Person::find($request->input('person_id'));
            $person=Person::where('ruc','LIKE',$request->input('ruc'))->limit(1)->first();
            if(count($person)==0){
                $person = new Person();
                $person->bussinesname = $request->input('plan');
                $person->ruc = $request->input('ruc');
                $person->direccion = $request->input('direccion');
                $person->save();
                $venta->empresa_id=$person->id;
            }else{
                $venta->empresa_id=$person->id;
            }
            if($request->input('igv')=="N"){
                $venta->subtotal=number_format($request->input('total'),2,'.','');
                $venta->igv=number_format(0,2,'.','');
                $venta->total=$request->input('total');     
            }else{
                $venta->subtotal=number_format($request->input('total'),2,'.','');
                $venta->igv=number_format($request->input('total')*0.18,2,'.','');
                $venta->total=number_format($venta->subtotal + $venta->igv,2,'.','');                    
            }
            $venta->tipomovimiento_id=9;
            $venta->tipodocumento_id=17;
            $venta->situacion='P';//Pendiente 
            $venta->ventafarmacia='N';
            $venta->manual='N';
            $venta->copago=$request->input('copago');
            $venta->montoinicial=$request->input('coaseguro');
            $venta->save();

            $pagohospital=0;
            $arr=explode(",",$request->input('listServicio'));
            for($c=0;$c<count($arr);$c++){
                $Detalle = new Detallemovcaja();
                $Detalle->movimiento_id=$venta->id;
                if($request->input('txtIdTipoServicio'.$arr[$c])!="0"){
                    //$Detalle->servicio_id=null;
                    $Detalle->servicio_id=$request->input('txtIdServicio'.$arr[$c]);
                    $Detalle->descripcion=trim($request->input('txtServicio'.$arr[$c]));
                }else{
                    $Detalle->servicio_id=null;
                    $Detalle->descripcion=trim($request->input('txtServicio'.$arr[$c]));
                }
                $Detalle->persona_id=$request->input('txtIdMedico'.$arr[$c]);
                $Detalle->cantidad=$request->input('txtCantidad'.$arr[$c]);
                if($request->input('igv')=="N"){
                    $Detalle->precio=round($request->input('txtPrecio'.$arr[$c]),2);
                }else{
                    $Detalle->precio=round($request->input('txtPrecio'.$arr[$c])*1.18,2);
                }
                $Detalle->pagodoctor=$request->input('txtPrecioMedico'.$arr[$c]);
                $Detalle->pagohospital=0;
                $Detalle->descuento=$request->input('txtDias'.$arr[$c]);
                $Detalle->save();

                if(!is_null($request->input('txtIdDetalle'.$arr[$c]))){
                    $Detalle2 = Detallemovcaja::find($request->input('txtIdDetalle'.$arr[$c]));
                    $Detalle2->movimientodescargo_id=$Detalle->id;
                    $Detalle2->save();
                }
            }
            
            //Genero F.E.
            $codigo="01";
            $abreviatura="F";
            
            //Array Insert facturacion
            $person = Person::find($venta->persona_id);
            $columna1=6;
            $columna2="20480082673";//RUC HOSPITAL
            $columna3="HOSPITAL PRIVADO JUAN PABLO II SOCIEDAD ANONIMA CERRADA";//Razon social Hospital                
            $columna4=$codigo;
            $columna5=$abreviatura.str_pad($venta->serie,3,'0',STR_PAD_LEFT).'-'.$venta->numero;
            $columna6=date('Y-m-d');
            $columna7="sistemas@hospitaljuanpablo.pe";
            $columna8=6;//Tipo Doc. Persona->Paciente DNI // DNI=1  RUC=6  Ninguno=0
            $columna9=$request->input('ruc');
            $columna10=trim($request->input('plan'));//Razon social
            $columna101=trim($request->input('direccion'));
            //if(trim($person->email)!="" && trim($person->email)!="."){
            //    $columna11=$person->email;
            //}else{
                $columna11="-";    
            //}
            $columna12="PEN";
            if($request->input('igv')=="S"){
                $columna13=number_format($venta->subtotal,2,'.','');
                $columna14='0.00';
                $columna15='0.00';
            }else{
                $columna13='0.00';
                $columna14=number_format($venta->subtotal,2,'.','');
                $columna15='0.00';
            }
            $columna16="";
            $columna17=number_format($venta->igv,2,'.','');
            $columna18='0.00';
            $columna19='0.00';
            $columna20=number_format($venta->total,2,'.','');
            $columna21=1000;
            $letras = new EnLetras();
            $columna22=trim($letras->ValorEnLetras($columna20, "SOLES" ));//letras
            $columna23='9670';
            $columna24=substr("CONVENIO: ".$request->input('plan'),0,100);
            $columna25='9199';
            $columna26=substr(trim($paciente->apellidopaterno." ".$paciente->apellidomaterno." ".$paciente->nombres),0,100);
            $columna27='9671';
            $columna28='HISTORIA CLINICA: '.$request->input('numero_historia');
            $columna29='9672';
            $columna30='DNI: '.$request->input('dni');
            $columna31='8161';
            $columna32=($venta->montoinicial==''?'0':$venta->montoinicial);
            $columna33='8163';
            $columna34=($venta->copago==''?'0':$venta->copago);
            DB::connection('sqlsrv')->insert('insert into SPE_EINVOICEHEADER (
                tipoDocumentoEmisor,
                numeroDocumentoEmisor,
                razonSocialEmisor,
                tipoDocumento,
                serieNumero,
                fechaEmision,
                correoEmisor,
                tipoDocumentoAdquiriente,
                numeroDocumentoAdquiriente,
                razonSocialAdquiriente,
                correoAdquiriente,
                tipoMoneda,
                totalValorVentaNetoOpGravadas,
                totalValorVentaNetoOpNoGravada,
                totalValorVentaNetoOpExonerada,
                totalIgv,
                totalVenta,
                codigoLeyenda_1,
                textoLeyenda_1,
                codigoAuxiliar100_1,
                textoAuxiliar100_1,
                codigoAuxiliar100_2,
                textoAuxiliar100_2,
                codigoAuxiliar100_3,
                textoAuxiliar100_3,
                codigoAuxiliar100_4,
                textoAuxiliar100_4,
                codigoAuxiliar100_5,
                textoAuxiliar100_5,
                codigoAuxiliar100_6,
                textoAuxiliar100_6
                ) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ? ,?, ? ,?, ? ,?, ?, ?, ?, ?)', 
                [$columna1, $columna2, $columna3, $columna4, $columna5, $columna6, $columna7, $columna8, $columna9, $columna10, $columna11, $columna12, $columna13, $columna14, $columna15, $columna17, $columna20, $columna21, $columna22, $columna23, $columna24, $columna25, $columna26, $columna27, $columna28, $columna29, $columna30, $columna31, $columna32, $columna33, $columna34]);

            if($abreviatura=="F"){
                DB::connection('sqlsrv')->insert('insert into SPE_EINVOICEHEADER_ADD(
                    tipoDocumentoEmisor,
                    numeroDocumentoEmisor,
                    serieNumero,
                    tipoDocumento,
                    clave,
                    valor) 
                    values (?, ?, ?, ?, ?, ?)',
                    [$columna1, $columna2, $columna5, $columna4, 'direccionAdquiriente', $columna101]);
            }else{
                DB::connection('sqlsrv')->insert('insert into SPE_EINVOICEHEADER_ADD(
                    tipoDocumentoEmisor,
                    numeroDocumentoEmisor,
                    serieNumero,
                    tipoDocumento,
                    clave,
                    valor) 
                    values (?, ?, ?, ?, ?, ?)',
                    [$columna1, $columna2, $columna5, $columna4, 'lugarDestino', $columna101]);
            }
            //---
            
            //Array Insert Detalle Facturacion
            for($c=0;$c<count($arr);$c++){
                $columnad1=$c+1;
                $servicio = Servicio::find($request->input('txtIdServicio'.$arr[$c]));
                if(!is_null($servicio) && $servicio->tipopago=="Convenio"){
                    $columnad2=$servicio->tarifario->codigo;
                    $columnad3=trim($request->input('txtServicio'.$arr[$c]));    
                }else{
                    $columnad2="-";
                    if($request->input('txtIdTipoServicio'.$arr[$c])!="0"){
                        $columnad3=$servicio->nombre;
                    }else{
                        $columnad3=trim($request->input('txtServicio'.$arr[$c]));
                    }
                }
                $columnad4=$request->input('txtCantidad'.$arr[$c]);
                $columnad5="ZZ";
                $columnad6=$request->input('txtPrecio'.$arr[$c]);
                if($request->input('igv')=='S'){
                    $columnad7=round($request->input('txtPrecio'.$arr[$c])*1.18,2);
                }else{
                    $columnad7=$request->input('txtPrecio'.$arr[$c]);
                }
                $columnad8="01";
                $columnad9=round($columnad4*$columnad6,2);
                if($request->input('igv')=="S"){
                    $columnad10="10";
                    $columnad11=round($columnad9*0.18,2);
                }else{
                    $columnad10="30";
                    $columnad11='0.00';
                }
                $columnad12='0.00';
                $columnad13='0.00';
                DB::connection('sqlsrv')->insert('insert into SPE_EINVOICEDETAIL(
                tipoDocumentoEmisor,
                numeroDocumentoEmisor,
                tipoDocumento,
                serieNumero,
                numeroOrdenItem,
                codigoProducto,
                descripcion,
                cantidad,
                unidadMedida,
                importeUnitarioSinImpuesto,
                importeUnitarioConImpuesto,
                codigoImporteUnitarioConImpues,
                importeTotalSinImpuesto,
                codigoRazonExoneracion,
                importeIgv
                )
                values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
                [$columna1, $columna2, $columna4, $columna5, $columnad1, $columnad2, $columnad3, $columnad4, $columnad5, $columnad6, $columnad7, $columnad8, $columnad9, $columnad10, $columnad11]);
            }
            DB::connection('sqlsrv')->update('update SPE_EINVOICEHEADER set bl_estadoRegistro = ? where serieNumero  = ?',
                ['A',$columna5]);
                
            //--
            
            $dat[0]=array("respuesta"=>"OK","id"=>$venta->id);
        });
        /*if (!is_null($error)) {
            DB::connection('sqlsrv')->delete('delete from SPE_EINVOICEHEADER where serieNumero="'.$numero.'"');
            DB::connection('sqlsrv')->delete('delete from SPE_EINVOICEDETAIL where serieNumero="'.$numero.'"');
            DB::connection('sqlsrv')->delete('delete from SPE_EINVOICEHEADER_ADD where serieNumero="'.$numero.'"');
        }*/
        return is_null($error) ? json_encode($dat) : $error;
    }

    public function edit($id, Request $request)
    {
        $existe = Libreria::verificarExistencia($id, 'movimiento');
        if ($existe !== true) {
            return $existe;
        }
        $listar              = Libreria::getParam($request->input('listar'), 'NO');
        $movimiento          = Movimiento::find($id);
        $cie10               = Cie::find($movimiento->cie_id);
        $entidad             = 'Facturacion';
        $formData            = array('facturacion.update', $id);
        $formData            = array('route' => $formData, 'method' => 'PUT', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton               = 'Modificar';
        return view($this->folderview.'.siniestro')->with(compact('movimiento', 'cie10', 'formData', 'entidad', 'boton', 'listar'));
    }

    public function update(Request $request, $id)
    {
        $existe = Libreria::verificarExistencia($id, 'movimiento');
        if ($existe !== true) {
            return $existe;
        }
        $error = DB::transaction(function() use($request, $id){
            $movimiento        = Movimiento::find($id);
            $movimiento->comentario = $request->input('siniestro');
            $movimiento->cie_id = $request->input('cie_id');
            $movimiento->save();
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
            $Ticket = Movimiento::find($id);
            $Ticket->delete();
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
        $modelo   = Movimiento::find($id);
        $entidad  = 'Ticket';
        $formData = array('route' => array('ticket.destroy', $id), 'method' => 'DELETE', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Eliminar';
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
}
