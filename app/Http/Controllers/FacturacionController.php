<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Validator;
use App\Http\Requests;
use App\Historia;
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

class FacturacionController extends Controller
{
    protected $folderview      = 'app.facturacion';
    protected $tituloAdmin     = 'Facturacion';
    protected $tituloRegistrar = 'Registrar Factura';
    protected $tituloModificar = 'Modificar Siniestro';
    protected $tituloEliminar  = 'Eliminar Factura';
    protected $rutas           = array('create' => 'facturacion.create', 
            'edit'   => 'facturacion.edit', 
            'delete' => 'facturacion.eliminar',
            'search' => 'facturacion.buscar',
            'index'  => 'facturacion.index',
            'pdfListar'  => 'facturacion.pdfListar',
            'excel'  => 'facturacion.excel'
        );

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function excel(Request $request){
        $paciente         = Libreria::getParam($request->input('paciente'),'');
        $numero           = Libreria::getParam($request->input('numero'),'');
        $fecha            = Libreria::getParam($request->input('fechainicial'));
        $fecha2           = Libreria::getParam($request->input('fechafinal'));
        $user = Auth::user();
        if($request->input('usuario')=="Todos"){
            $responsable_id=0;
        }else{
            $responsable_id=$user->person_id;
        }
        $resultado        = Movimiento::leftjoin('person as paciente', 'paciente.id', '=', 'movimiento.persona_id')
                            ->leftjoin('person as responsable','responsable.id','=','movimiento.responsable_id')
                            ->leftjoin('cie','movimiento.cie_id','=','cie.id')
                            ->join('plan','plan.id','=','movimiento.plan_id')
                            ->where(DB::raw('concat(paciente.apellidopaterno,\' \',paciente.apellidomaterno,\' \',paciente.nombres)'), 'LIKE', '%'.strtoupper($paciente).'%')
                            ->where(DB::raw('concat(movimiento.serie,\'-\',movimiento.numero)'),'LIKE','%'.$numero.'%')->where('movimiento.tipodocumento_id','=','17')
                            ->where('movimiento.manual','like','N');
        if($fecha!=""){
            $resultado = $resultado->where('movimiento.fecha', '>=', ''.$fecha.'');
        }
        if($fecha2!=""){
            $resultado = $resultado->where('movimiento.fecha', '<=', ''.$fecha2.'');
        }
        if($responsable_id>0){
            $resultado = $resultado->where('movimiento.responsable_id', '=', $responsable_id);   
        }
        $resultado        = $resultado->select('movimiento.*','cie.codigo as cie10',DB::raw('concat(paciente.apellidopaterno,\' \',paciente.apellidomaterno,\' \',paciente.nombres) as paciente'),'responsable.nombres as responsable',DB::raw('plan.razonsocial as empresa'))->orderBy('movimiento.fecha', 'ASC')->orderBy('movimiento.numero','ASC');
        $lista            = $resultado->get();

        Excel::create('ExcelFacturacion', function($excel) use($lista,$request) {
 
            $excel->sheet('Facturacion', function($sheet) use($lista,$request) {

                $array = array();
                $cabecera = array();

                $cabecera[] = "Fecha";
                $cabecera[] = "Fecha Atencion";
                $cabecera[] = "Nro";
                $cabecera[] = "Paciente";
                $cabecera[] = "Empresa";
                $cabecera[] = "CIE";
                $cabecera[] = "UCI";
                $cabecera[] = "Total";
                $cabecera[] = "Situacion";
                $cabecera[] = "Usuario";
                $cabecera[] = "Estado Bz";
                $cabecera[] = "Mensaje Sunat";
                $array[] = $cabecera;
                $c=3;$d=3;$band=true;

                foreach ($lista as $key => $value2){
                    $detalle = array();
                    $detalle[] = date('d/m/Y',strtotime($value2->fecha));
                    $detalle[] = date('d/m/Y',strtotime($value2->fechaingreso));
                    $detalle[] = $value2->serie.'-'.$value2->numero;
                    $detalle[] = $value2->paciente;
                    $detalle[] = $value2->empresa;
                    $detalle[] = $value2->cie10;
                    $detalle[] = $value2->uci;
                    $detalle[] = number_format($value2->total);
                    $detalle[] = $value2->situacion;
                    $detalle[] = $value2->responsable;
                    $detalle[] = $value2->situacionbz;
                    $detalle[] = $value2->mensajesunat;
                    $array[] = $detalle;          
                }

                $sheet->fromArray($array);
            });
        })->export('xls');

    }

    public function buscar(Request $request)
    {
        $pagina           = $request->input('page');
        $filas            = $request->input('filas');
        $entidad          = 'Facturacion';
        $paciente         = Libreria::getParam($request->input('paciente'),'');
        $numero           = Libreria::getParam($request->input('numero'),'');
        $fecha            = Libreria::getParam($request->input('fechainicial'));
        $fecha2            = Libreria::getParam($request->input('fechafinal'));
        $user = Auth::user();
        if($request->input('usuario')=="Todos"){
            $responsable_id=0;
        }else{
            $responsable_id=$user->person_id;
        }
        $resultado        = Movimiento::leftjoin('person as paciente', 'paciente.id', '=', 'movimiento.persona_id')
                            ->leftjoin('person as responsable','responsable.id','=','movimiento.responsable_id')
                            ->leftjoin('cie','movimiento.cie_id','=','cie.id')
                            ->join('plan','plan.id','=','movimiento.plan_id')
                            ->where(DB::raw('concat(paciente.apellidopaterno,\' \',paciente.apellidomaterno,\' \',paciente.nombres)'), 'LIKE', '%'.strtoupper($paciente).'%')
                            ->where(DB::raw('concat(movimiento.serie,\'-\',movimiento.numero)'),'LIKE','%'.$numero.'%')->where('movimiento.tipodocumento_id','=','17')
                            ->where('movimiento.manual','like','N');
        if($fecha!=""){
            $resultado = $resultado->where('movimiento.fecha', '>=', ''.$fecha.'');
        }
        if($fecha2!=""){
            $resultado = $resultado->where('movimiento.fecha', '<=', ''.$fecha2.'');
        }
        if($responsable_id>0){
            $resultado = $resultado->where('movimiento.responsable_id', '=', $responsable_id);   
        }
        $resultado        = $resultado->select('movimiento.*','cie.codigo as cie10',DB::raw('concat(paciente.apellidopaterno,\' \',paciente.apellidomaterno,\' \',paciente.nombres) as paciente'),'responsable.nombres as responsable',DB::raw('plan.razonsocial as empresa'))->orderBy('movimiento.fecha', 'ASC')->orderBy('movimiento.numero','ASC');
        $lista            = $resultado->get();
        $cabecera         = array();
        $cabecera[]       = array('valor' => '#', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Fecha', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Fecha Atencion', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Nro', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Paciente', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Empresa', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Siniestro', 'numero' => '1');
        $cabecera[]       = array('valor' => 'CIE', 'numero' => '1');
        $cabecera[]       = array('valor' => 'UCI', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Total', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Situacion', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Usuario', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Estado Bz', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Estado Sunat', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Mensaje Sunat', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Operaciones', 'numero' => '4');
        
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
            return view($this->folderview.'.list')->with(compact('lista', 'totalfac', 'paginacion', 'inicio', 'fin', 'entidad', 'cabecera', 'titulo_modificar', 'titulo_eliminar', 'ruta', 'conf'));
        }
        return view($this->folderview.'.list')->with(compact('lista', 'entidad','conf'));
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

    public function show($id)
    {
        //
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
    
    public function buscarservicio(Request $request)
    {
        $descripcion = $request->input("descripcion");
        $idtiposervicio = trim($request->input("idtiposervicio"));
        $tipopago = 'Convenio';
        $resultado = Servicio::leftjoin('tarifario','tarifario.id','=','servicio.tarifario_id');
        $resultado = $resultado->where(DB::raw('trim(concat(tarifario.codigo,\' \',servicio.nombre,\' \',tarifario.nombre))'),'LIKE','%'.$descripcion.'%')->where('servicio.plan_id','=',$request->input('plan_id'));
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
            /*if($tipopago=='Convenio' && ($idtiposervicio=='' || $idtiposervicio==1)){//buscar consultas con precio de convenio
                $resultado = Servicio::where(DB::raw('trim(servicio.nombre)'),'LIKE','%'.$descripcion.'%')
                            ->where('tipopago','LIKE','Particular')
                            ->where('tiposervicio_id','=','1')->get();
                if(count($resultado)>0){
                    foreach ($resultado as $key => $value){
                        //COSTO DE CONSULTA
                        $plan = Plan::find($request->input('plan_id'));
                        $data[$c] = array(
                                    'servicio' => ($value->tipopago=='Convenio')?$value->tarifario:$value->nombre,
                                    'codigo' => ($value->tipopago=='Convenio')?$value->codigo:'-',
                                    'tiposervicio' => $value->tiposervicio->nombre,
                                    'precio' => $plan->consulta,
                                    'idservicio' => $value->id,
                                );
                                $c++;                
                    }            
                }
            }*/
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
                        $plan = Plan::find($request->input('plan_id'));
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
                    'codigo' => $resultado->tarifario->codigo,
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

    public function agregarDetallePrefactura(Request $request){
        $resultado        = Movimiento::leftjoin('person as paciente', 'paciente.id', '=', 'movimiento.persona_id')
                            ->join('detallemovcaja as dmc','dmc.movimiento_id','=','movimiento.id')
                            ->join('plan','plan.id','=','movimiento.plan_id')
                            ->join('person as medico','medico.id','=','dmc.persona_id')
                            ->leftjoin('servicio as s','s.id','=','dmc.servicio_id')
                            ->leftjoin('person as responsable','responsable.id','=','movimiento.responsable_id')
                            ->join('historia','historia.person_id','=','paciente.id')
                            ->where('paciente.id', '=', $request->input('persona_id'))
                            ->whereIn('movimiento.plan_id',function($query){
                                $query->select('id')->from('plan')->where('tipopago','LIKE','Convenio');
                                })
                            ->where('movimiento.tipodocumento_id','=','1')
                            ->whereNotIn('movimiento.situacion',['U','A'])
                            ->where('dmc.descargado', 'like', 'S')
                            ->whereNull('dmc.deleted_at')
                            ->where(function($q){
                                $q->whereNull('dmc.movimientodescargo_id')
                                   ->orWhere('dmc.movimientodescargo_id','=',0);
                            });
        $resultado        = $resultado->select('dmc.id',DB::raw('dmc.observacion as listapago'),'movimiento.fecha','movimiento.numero','dmc.cantidad','dmc.servicio_id','dmc.descripcion as servicio2','s.nombre as servicio',DB::raw('dmc.cantidad*dmc.precio as total'),'plan.nombre as plan2',DB::raw('concat(medico.apellidopaterno,\' \',medico.apellidomaterno,\' \',medico.nombres) as medico'),'dmc.persona_id as medico_id',DB::raw('case when dmc.servicio_id>0 then s.tiposervicio_id else dmc.tiposervicio_id end as tiposervicio_id'),'movimiento.plan_id')->orderBy('movimiento.fecha', 'ASC')->orderBy('movimiento.numero','ASC');
        $lista = $resultado->get();
        $c=0;$data = array();
        foreach ($lista as $key => $value) {
            if($value->servicio_id>0){
                $resultado = Servicio::find($value->servicio_id);
                if($resultado->tiposervicio_id==1){//CONSULTA
                    $plan = Plan::find($value->plan_id);
                    $precio=round($plan->consulta,2);
                }else{
                    $precio=round($resultado->precio/1.18,2);
                }

                if(strpos($resultado->nombre, 'CONS ') !== false){
                    $otro = '390101';
                } else {
                    $otro = '-';
                }

                $data[$c]=array(
                        'servicio' => ($resultado->tipopago=='Convenio')?$resultado->tarifario->nombre:$resultado->nombre,
                        'tiposervicio' => $resultado->tiposervicio->nombre,
                        'codigo' => ($resultado->tipopago=='Convenio')?$resultado->tarifario->codigo:$otro,
                        'precio' => $precio,
                        'id' => $resultado->id,
                        'idservicio' => "20".rand(0,1000).$resultado->id,
                        'preciohospital' => $precio,
                        'preciomedico' => 0,
                        'modo' => $resultado->modo,
                        'idtiposervicio' => $resultado->tiposervicio_id,
                        'medico' => trim($value->medico),
                        'medico_id' => $value->medico_id,
                        'cantidad' => $value->cantidad,
                        'iddetalle' => $value->id,
                        );
            }else{
                $tiposervicio = Tiposervicio::find($value->tiposervicio_id);
                if($resultado->tiposervicio_id==1){//CONSULTA
                    $plan = Plan::find($value->plan_id);
                    $precio=round($plan->consulta,2);
                }else{
                    $precio=round($resultado->precio/1.18,2);
                }
                $data[$c]=array(
                        'servicio' => $value->servicio2,
                        'tiposervicio' => $tiposervicio->nombre,
                        'codigo' => '-',
                        'precio' => $precio,
                        'id' => 0,
                        'idservicio' => "20".rand(0,1000).'0',
                        'preciohospital' => $precio,
                        'preciomedico' => 0,
                        'modo' => 'M',
                        'idtiposervicio' => $value->tiposervicio_id,
                        'medico' => trim($value->medico),
                        'medico_id' => $value->medico_id,
                        'cantidad' => $value->cantidad,
                        'iddetalle' => $value->id,
                        );
            }
            $c=$c+1;
        }
        return json_encode($data);
    }
    
    public function pdfComprobante(Request $request){
        $entidad          = 'Facturacion';
        $id               = Libreria::getParam($request->input('id'),'');
        $resultado        = Movimiento::join('person as paciente', 'paciente.id', '=', 'movimiento.persona_id')
                            ->leftjoin('person as responsable','responsable.id','=','movimiento.responsable_id')
                            ->join('tipodocumento','tipodocumento.id','=','movimiento.tipodocumento_id')
                            ->where('movimiento.id', '=', $id);
        $resultado        = $resultado->select('movimiento.*','tipodocumento.nombre as tipodocumento');
        //dd($resultado->toSql());
        //error_log($resultado->toSql());
        $lista            = $resultado->get();
        //dd($lista);
        if (count($lista) > 0) {     
            foreach($lista as $key => $value){
                $pdf = new TCPDF();
                $pdf::SetTitle('Comprobante');
                $pdf::AddPage();
                $pdf::SetFont('helvetica','B',12);
                $pdf::Cell(130,7,"",0,0,'C');
                $pdf::Image("http://localhost/juanpablo/dist/img/logo.jpg", 15, 5, 115, 30);
                $pdf::Cell(60,7,utf8_encode("RUC N° 20480082673"),'RTL',0,'C');
                $pdf::Ln();
                $pdf::Cell(130,7,"",0,0,'C');
                $pdf::Cell(60,7,utf8_encode("FACTURA"),'RL',0,'C');
                $pdf::Ln();
                $pdf::Cell(130,7,"",0,0,'C');
                $pdf::Cell(60,7,utf8_encode("ELECTRÓNICA"),'RL',0,'C');
                $pdf::Ln();
                $pdf::Cell(130,7,"",0,0,'C');
                $abreviatura="F";
                $dni=$value->empresa->ruc;
                $subtotal=number_format($value->subtotal,2,'.','');
                $igv=number_format($value->total - $subtotal,2,'.','');
                $pdf::Cell(60,7,utf8_encode($abreviatura.str_pad($value->serie,3,'0',STR_PAD_LEFT).'-'.str_pad($value->numero,8,'0',STR_PAD_LEFT)),'RBL',0,'C');
                $pdf::Ln();
                $pdf::SetFont('helvetica','B',9);
                $pdf::Cell(0,7,utf8_decode("HOSPITAL PRIVADO JUAN PABLO II SOCIEDAD ANONIMA CERRADA"),0,0,'L');
                $pdf::Ln();
                $pdf::Ln();
                $pdf::SetFont('helvetica','B',9);
                $pdf::Cell(37,6,utf8_encode("Paciente: "),0,0,'L');
                $pdf::SetFont('helvetica','',9);
                //$ticket = Movimiento::find($value->movimiento_id);
                $pdf::Cell(110,6,(trim($value->persona->apellidopaterno." ".$value->persona->apellidomaterno." ".$value->persona->nombres)),0,0,'L');
                $pdf::SetFont('helvetica','B',9);
                $pdf::Cell(30,6,utf8_encode("DNI: "),0,0,'L');
                $pdf::SetFont('helvetica','',8);
                $pdf::Cell(37,6,(trim($value->persona->dni)),0,0,'L');
                $pdf::Ln();
                $pdf::SetFont('helvetica','B',9);
                $pdf::Cell(37,6,utf8_encode("Nombre / Razón Social: "),0,0,'L');
                $pdf::SetFont('helvetica','',9);
                $pdf::Cell(110,6,(trim($value->empresa->bussinesname)),0,0,'L');
                $pdf::SetFont('helvetica','B',9);
                $pdf::Cell(30,6,utf8_encode($abreviatura=="F"?"RUC :":"DNI".": "),0,0,'L');
                $pdf::SetFont('helvetica','',8);
                $pdf::Cell(37,6,utf8_encode($dni),0,0,'L');
                $pdf::Ln();
                $pdf::SetFont('helvetica','B',9);
                $pdf::Cell(37,6,utf8_encode("Dirección: "),0,0,'L');
                $pdf::SetFont('helvetica','',9);
                $pdf::Cell(110,6,(trim($value->empresa->direccion)),0,0,'L');
                $pdf::SetFont('helvetica','B',9);
                $pdf::Cell(30,6,utf8_encode("Fecha de emisión: "),0,0,'L');
                $pdf::SetFont('helvetica','',8);
                $pdf::Cell(37,6,utf8_encode($value->fecha),0,0,'L');
                $pdf::Ln();
                $pdf::SetFont('helvetica','B',9);
                $pdf::Cell(37,6,utf8_encode("Moneda: "),0,0,'L');
                $pdf::SetFont('helvetica','',9);
                $pdf::Cell(70,6,(trim('PEN - Sol')),0,0,'L');
                $pdf::Cell(40,6,(trim('PENDIENTE')),0,0,'L');
                $pdf::SetFont('helvetica','B',9);
                //dd($value->persona_id);
                $historia = Historia::where('person_id','=',$value->persona_id)->first();
                $pdf::Cell(30,6,utf8_encode("Historia: "),0,0,'L');
                $pdf::SetFont('helvetica','',8);
                $pdf::Cell(37,6,utf8_encode($historia->numero),0,0,'L');
                $pdf::Ln();
                
                $pdf::SetFont('helvetica','B',9);
                $pdf::Cell(10,5,("Item"),1,0,'C');
                $pdf::Cell(13,5,utf8_encode("Código"),1,0,'C');
                $pdf::Cell(107,5,utf8_encode("Descripción"),1,0,'C');
                $pdf::Cell(10,5,("Und."),1,0,'C');
                $pdf::Cell(15,5,("Cantidad"),1,0,'C');
                $pdf::Cell(20,5,("V. Unitario"),1,0,'C');
                //$pdf::Cell(20,7,("P. Unitario"),1,0,'C');
                //$pdf::Cell(20,7,("Descuento"),1,0,'C');
                $pdf::Cell(20,5,("Sub Total"),1,0,'C');
                $pdf::Ln();
                $resultado        = Detallemovcaja::leftjoin('servicio', 'servicio.id', '=', 'detallemovcaja.servicio_id')
                            ->where('detallemovcaja.movimiento_id', '=', $id)
                            ->select('detallemovcaja.*');
                //error_log($resultado->toSql());
                $lista2            = $resultado->get();
                $c=0;
                foreach($lista2 as $key2 => $v){$c=$c+1;
                    $pdf::SetFont('helvetica','',7.5);
                    $pdf::Cell(10,5,$c,1,0,'C');

                    //AQUI CODIGOS CONS
                    if(strpos($v->descripcion, 'CONS ') !== false){
                        $otro = '390101';
                    } else {
                        $otro = '390101';
                    }

                    if($v->servicio_id>0 && $v->servicio_id!=""){
                        if(!is_null($v->servicio) && $v->servicio->tipopago=="Convenio"){
                            $codigo=$v->servicio->tarifario->codigo;
                            $nombre=trim($v->descripcion);    
                        }else{
                            $codigo=$otro;
                            if($v->servicio_id>"0"){
                                //$nombre=$v->servicio->nombre;
                                $nombre=trim($v->descripcion);
                            }else{
                                $nombre=trim($v->descripcion);
                            }
                        }
                    }else{
                        $codigo="-";
                        $nombre=trim($v->descripcion);
                    }
                    $pdf::Cell(13,5,$codigo,1,0,'C');
                    if(strlen($nombre)<60){
                        $pdf::Cell(107,5,($nombre),1,0,'L');
                    }else{
                        $x=$pdf::GetX();
                        $y=$pdf::GetY();
                        $pdf::Multicell(107,2,($nombre),0,'L');
                        $pdf::SetXY($x,$y);
                        $pdf::Cell(107,5,"",1,0,'L');
                    }
                    $pdf::Cell(10,5,("ZZ."),1,0,'C');
                    $pdf::Cell(15,5,number_format($v->cantidad,2,'.',''),1,0,'R');
                    if($value->igv>0){
                        $pdf::Cell(20,5,number_format($v->precio/1.18,2,'.',''),1,0,'R');
                        $pdf::Cell(20,5,number_format($v->precio*$v->cantidad/1.18,2,'.',''),1,0,'R');
                    }else{
                        $pdf::Cell(20,5,number_format($v->precio,2,'.',''),1,0,'R');
                        $pdf::Cell(20,5,number_format($v->precio*$v->cantidad,2,'.',''),1,0,'R');
                    }
                    //$pdf::Cell(20,7,number_format($v->precio,2,'.',''),1,0,'R');
                    //$pdf::Cell(20,7,("0.00"),1,0,'R');
                    //$pdf::Cell(20,7,number_format($v->precio*$v->cantidad,2,'.',''),1,0,'R');
                    $pdf::Ln();                    
                }
                $pdf::Cell(70,5,"",0,0,'L');
                $pdf::SetFont('helvetica','B',8);
                $pdf::Cell(20,5,"COPAGO:",0,0,'L');
                $pdf::SetFont('helvetica','',8);
                $pdf::Cell(20,5,number_format($value->copago,2,'.',''),0,0,'L');
                $pdf::SetFont('helvetica','B',8);
                $pdf::Cell(20,5,"COASEGURO:",0,0,'L');
                $pdf::SetFont('helvetica','',8);
                $pdf::Cell(20,5,number_format($value->montoinicial,2,'.','').'%',0,0,'L');
                $pdf::Ln();                    
                $letras = new EnLetras();
                $pdf::SetFont('helvetica','B',8);
                $valor=$letras->ValorEnLetras($value->total, "SOLES" );//letras
                
                $pdf::Cell(116,5,utf8_decode($valor),0,0,'L');
                $pdf::SetFont('helvetica','B',8);
                $pdf::Cell(40,5,utf8_decode('Op. Gravada'),0,0,'L');
                $pdf::SetFont('helvetica','',8);
                $pdf::Cell(20,5,utf8_decode('PEN'),0,0,'C');
                if($igv>0)
                    $pdf::Cell(20,5,$subtotal,0,0,'R');
                else
                    $pdf::Cell(20,5,'0.00',0,0,'R');
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
                if($igv>0)
                    $pdf::Cell(20,5,'0.00',0,0,'R');
                else
                    $pdf::Cell(20,5,$subtotal,0,0,'R');
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
                $pdf::Cell(0,5,utf8_encode('Autorizado a ser emisor electrónico mediante R.I. SUNAT Nº 0340050004781'),0,0,'L');
                $pdf::Ln();
                $pdf::Cell(0,5,'Usuario: '.$value->responsable->nombres,0,0,'R');
                $pdf::Ln();
                $pdf::SetFont('helvetica','',8);
                $pdf::Cell(0,5,utf8_encode('Representación Impresa de la Factura Electrónica, consulte en https://sfe.bizlinks.com.pe'),0,0,'L');
                $pdf::Ln();
                $pdf::Output('Comprobante.pdf');
            }
        }
    }

    public function pdfComprobante2(Request $request){
        $entidad          = 'Ticket';
        $id               = Libreria::getParam($request->input('id'),'');
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
                $pdf::AddPage('P');
                $pdf::SetFont('helvetica','B',8);
                $pdf::Cell(100,6,"",0,0,'C');
                $pdf::Ln();
                $pdf::Ln();
                $pdf::Ln();
                $pdf::Cell(100,6,"",0,0,'C');
                $abreviatura="F";
                $dni=$value->empresa->ruc;
                $subtotal=number_format($value->total/1.18,2,'.','');
                $igv=number_format($value->total - $subtotal,2,'.','');
                $pdf::Cell(60,4,utf8_encode(str_pad($value->serie,3,'0',STR_PAD_LEFT).'-'.str_pad($value->numero,8,'0',STR_PAD_LEFT)),0,0,'C');
                $pdf::Ln();
                $pdf::SetFont('helvetica','B',8);
                $pdf::Cell(35,6,utf8_encode("PACIENTE: "),0,0,'L');
                $pdf::Cell(100,6,(trim($value->persona->apellidopaterno." ".$value->persona->apellidomaterno." ".$value->persona->nombres)),0,0,'L');
                $pdf::Cell(25,4,"DNI: ",0,0,'L');
                $pdf::Cell(30,4,utf8_encode($value->persona->dni),0,0,'L');
                $pdf::Ln();
                $pdf::Cell(35,4,"RAZON SOCIAL: ",0,0,'L');
                $pdf::Cell(100,4,(trim($value->empresa->bussinesname)),0,0,'L');
                $pdf::Cell(25,4,"RUC: ",0,0,'L');
                $pdf::Cell(30,4,$dni,0,0,'L');
                $pdf::Ln();
                $pdf::SetFont('helvetica','B',8);
                $pdf::Cell(35,4,"DIRECCION: ",0,0,'L');
                $pdf::Cell(100,4,(trim($value->empresa->direccion)),0,0,'L');
                $pdf::Cell(25,4,"FECHA: ",0,0,'L');
                $pdf::Cell(30,4,date("d/m/Y",strtotime($value->fecha)),0,0,'L');
                //$ticket = Movimiento::find($value->movimiento_id);57616
                $pdf::Ln();
                $value2=Movimiento::find($id);
                //dd($value2->persona_id);
                $historia = Historia::where('person_id','=',$value2->persona_id)->first();
                //$pdf::Cell(35,4,"CONVENIO: ",0,0,'L');
                //$pdf::Cell(120,4,trim($value2->plan->nombre),0,0,'L');
                $pdf::Cell(75,4,"",0,0,'L');
                $pdf::Cell(60,4,utf8_encode($value->situacion=='P'?'PENDIENTE':'CONTADO'),0,0,'C');
                $pdf::Cell(25,4,"HISTORIA: ",0,0,'L');
                $pdf::Cell(30,4,utf8_encode($historia->numero),0,0,'L');
                $pdf::Ln();
                if($value2->tarjeta!="")
                    $pdf::Cell(50,6,trim($value2->tarjeta." - ".$value2->tipotarjeta),0,0,'L');
                $pdf::Cell(0,4,$value->responsable->nombres,0,0,'R');
                $pdf::Ln();
                $pdf::SetFont('helvetica','B',8);
                $pdf::Cell(5,7,"",0,0,'C');
                $pdf::Cell(15,7,("Cant."),0,0,'C');
                $pdf::Cell(100,7,utf8_encode("Descripción"),0,0,'C');
                $pdf::Cell(30,7,("P. Unitario"),0,0,'C');
                $pdf::Cell(30,7,("Sub Total"),0,0,'C');
                $pdf::Ln();
                $resultado        = Detallemovcaja::leftjoin('servicio', 'servicio.id', '=', 'detallemovcaja.servicio_id')
                            ->where('detallemovcaja.movimiento_id', '=', $id)
                            ->select('detallemovcaja.*');
                $lista2            = $resultado->get();
                $c=0;$y=$pdf::GetY();
                foreach($lista2 as $key2 => $v){$c=$c+1;
                    $pdf::SetFont('helvetica','',8);
                    $pdf::Cell(5,3,"",0,0,'C');
                    if($v->servicio_id>"0"){
                        if($v->servicio->tipopago=="Convenio"){
                            $codigo=$v->servicio->tarifario->codigo;
                            $nombre=trim($v->descripcion);     
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
                    $pdf::Cell(15,3,number_format($v->cantidad,0,'.',''),0,0,'C');
                    if($v->persona_id!="56595")
                        $nombre.=" - ".substr($v->persona->nombres,0,1)." ".$v->persona->apellidopaterno;

                    if(strlen($nombre)<80){
                        $pdf::Cell(100,3,utf8_decode($nombre),0,0,'L');
                    }else{
                        $x=$pdf::GetX();
                        $y=$pdf::GetY();
                        $pdf::Multicell(100,1.5,utf8_decode($nombre),0,'L');
                        $pdf::SetXY($x,$y);
                        $pdf::Cell(100,3,"",0,0,'L');
                    }
                    $pdf::Cell(30,3,number_format($v->precio/1.18,2,'.',''),0,0,'R');
                    $pdf::Cell(30,3,number_format($v->precio*$v->cantidad/1.18,2,'.',''),0,0,'R');
                    $y=$y+3;
                    $pdf::SetXY(10,$y);
                    //$pdf::Ln('3');                    
                }
                $pdf::SetXY(10,$y+1);
                $letras = new EnLetras();
                $pdf::SetFont('helvetica','B',8);
                $valor=$letras->ValorEnLetras($value->total, " SOLES" );//letras
                $pdf::Cell(15,3,"",0,0,'C');
                $pdf::Cell(115,3,utf8_decode($valor),0,0,'L');
                $pdf::SetFont('helvetica','B',8);
                $pdf::Cell(20,3,"SUBTOTAL: ",0,0,'L');
                $pdf::Cell(30,3,"S/. ".number_format($subtotal,2,'.',''),0,0,'R');
                $pdf::Ln();
                $pdf::Cell(130,3,'',0,0,'L');
                $pdf::Cell(20,3,"IGV: ",0,0,'L');
                $pdf::Cell(30,3,"S/. ".$igv,0,0,'R');
                $pdf::Ln();
                $pdf::Cell(130,3,'',0,0,'L');
                $pdf::Cell(20,3,"TOTAL: ",0,0,'L');
                $pdf::Cell(30,3,"S/. ".number_format($value->total,2,'.',''),0,0,'R');
                $pdf::Ln();
                $pdf::Output('Comprobante.pdf');
            }
        }
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
    
    public function personrucautocompletar($searching)
    {
        $resultado        = Person::where(DB::raw('CONCAT(ruc," ",bussinesname)'), 'LIKE', ''.strtoupper(str_replace("_","",$searching)).'%')->orderBy('ruc', 'ASC');
        $list      = $resultado->get();
        $data = array();
        foreach ($list as $key => $value) {
            $data[] = array(
                            'label' => trim($value->ruc.' '.$value->bussinesname),
                            'id'    => $value->id,
                            'value' => trim($value->bussinesname),
                            'ruc'   => $value->ruc,
                            'razonsocial' => $value->bussinesname,
                            'direccion' => $value->direccion,
                        );
        }
        return json_encode($data);
    }

    public function cieautocompletar($searching)
    {
        $resultado        = Cie::where(DB::raw('CONCAT(codigo," ",descripcion)'), 'LIKE', '%'.strtoupper(str_replace("_","",$searching)).'%')->orderBy('codigo', 'ASC');
        $list      = $resultado->get();
        $data = array();
        foreach ($list as $key => $value) {
            $data[] = array(
                            'label' => trim($value->codigo.' '.$value->descripcion),
                            'id'    => $value->id,
                            'value' => trim($value->codigo.' '.$value->descripcion),
                        );
        }
        return json_encode($data);
    }

    public function procesar(Request $request)
    {
        $error = DB::transaction(function() use($request){
            $resultado        = Movimiento::leftjoin('person as paciente', 'paciente.id', '=', 'movimiento.persona_id')
                            ->join('person as responsable', 'responsable.id', '=', 'movimiento.responsable_id')
                            ->leftjoin('movimiento as m2','m2.id','=','movimiento.movimiento_id')
                            ->where('movimiento.tipomovimiento_id','=',9);
            if($request->input('fechainicial')!=""){
                $resultado = $resultado->where('movimiento.fecha','>=',$request->input('fechainicial'));
            }
            if($request->input('fechafinal')!=""){
                $resultado = $resultado->where('movimiento.fecha','<=',$request->input('fechafinal'));
            }        
            if($request->input('numero')!=""){
                $resultado = $resultado->where('movimiento.numero','LIKE','%'.$request->input('numero').'%');
            }        

            $resultado        = $resultado->select('movimiento.*','m2.tipodocumento_id as tipodocumento_id2')->orderBy('movimiento.fecha', 'ASC');
            $lista            = $resultado->get();
            foreach ($lista as $key => $value) {
                $numero=($value->tipodocumento_id2==5?"B":"F").str_pad($value->serie,3,'0',STR_PAD_LEFT).'-'.str_pad($value->numero,8,'0',STR_PAD_LEFT);
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

    public function pdfLiquidacion(Request $request){
        $entidad          = 'Facturacion';
        $id               = Libreria::getParam($request->input('id'),'');
        $resultado        = Movimiento::join('person as paciente', 'paciente.id', '=', 'movimiento.persona_id')
                            ->leftjoin('person as responsable','responsable.id','=','movimiento.responsable_id')
                            ->join('tipodocumento','tipodocumento.id','=','movimiento.tipodocumento_id')
                            ->where('movimiento.id', '=', $id);
        $resultado        = $resultado->select('movimiento.*','tipodocumento.nombre as tipodocumento');
        $lista            = $resultado->get();
        $inafecta=0;
        if (count($lista) > 0) {     
            foreach($lista as $key => $value){
                $pdf = new TCPDF();
                $pdf::SetTitle('Comprobante');
                $pdf::AddPage();
                $pdf::SetFont('helvetica','B',12);
                $pdf::Cell(130,7,"",0,0,'C');
                $pdf::Image("http://localhost/juanpablo/dist/img/logo.jpg", 15, 5, 115, 30);
                $pdf::Cell(60,7,utf8_encode("RUC N° 20480082673"),'RTL',0,'C');
                $pdf::Ln();
                $pdf::Cell(130,7,"",0,0,'C');
                $pdf::Cell(60,7,utf8_encode("LIQUIDACION"),'RL',0,'C');
                $pdf::Ln();
                $pdf::Cell(130,7,"",0,0,'C');
                $dni=$value->empresa->ruc;
                $subtotal=number_format($value->subtotal,2,'.','');
                $igv=number_format($value->total - $subtotal,2,'.','');
                $pdf::Cell(60,7,utf8_encode(str_pad($value->serie,3,'0',STR_PAD_LEFT).'-'.str_pad($value->numero,8,'0',STR_PAD_LEFT)),'RBL',0,'C');
                $pdf::Ln();
                $pdf::Ln();
                $pdf::SetFont('helvetica','B',9);
                $pdf::Cell(0,7,utf8_decode("HOSPITAL PRIVADO JUAN PABLO II SOCIEDAD ANONIMA CERRADA"),0,0,'L');
                $pdf::Ln();
                $pdf::Ln();
                $pdf::SetFont('helvetica','B',9);
                $pdf::Cell(33,6,utf8_encode("Paciente: "),0,0,'L');
                $pdf::SetFont('helvetica','',9);
                //$ticket = Movimiento::find($value->movimiento_id);
                $pdf::Cell(110,6,(trim($value->persona->apellidopaterno." ".$value->persona->apellidomaterno." ".$value->persona->nombres)),0,0,'L');
                $pdf::SetFont('helvetica','B',9);
                $pdf::Cell(30,6,utf8_encode("DNI: "),0,0,'L');
                $pdf::SetFont('helvetica','',8);
                $pdf::Cell(37,6,(trim($value->persona->dni)),0,0,'L');
                $pdf::Ln();
                $pdf::SetFont('helvetica','B',9);
                $pdf::Cell(33,6,utf8_encode("Razón Social: "),0,0,'L');
                $pdf::SetFont('helvetica','',9);
                $pdf::Cell(110,6,(trim($value->empresa->bussinesname)),0,0,'L');
                $pdf::SetFont('helvetica','B',9);
                $pdf::Cell(30,6,"RUC :",0,0,'L');
                $pdf::SetFont('helvetica','',8);
                $pdf::Cell(37,6,utf8_encode($dni),0,0,'L');
                $pdf::Ln();
                $pdf::SetFont('helvetica','B',9);
                $pdf::Cell(33,6,utf8_encode("Dirección: "),0,0,'L');
                $pdf::SetFont('helvetica','',9);
                $pdf::Cell(110,6,(trim($value->empresa->direccion)),0,0,'L');
                $pdf::SetFont('helvetica','B',9);
                $pdf::Cell(30,6,utf8_encode("Fecha de ingreso: "),0,0,'L');
                $pdf::SetFont('helvetica','',8);
                $pdf::Cell(37,6,date("d/m/Y",strtotime($value->fechaingreso)),0,0,'L');
                $pdf::Ln();
                
                $pdf::SetFont('helvetica','B',9);
                $pdf::Cell(10,7,("Item"),1,0,'C');
                $pdf::Cell(130,7,utf8_encode("Descripción"),1,0,'C');
                $pdf::Cell(15,7,("Cantidad"),1,0,'C');
                $pdf::Cell(20,7,("V. Unitario"),1,0,'C');
                $pdf::Cell(20,7,("Sub Total"),1,0,'C');
                $pdf::Ln();
                $resultado        = Detallemovcaja::leftjoin('servicio', 'servicio.id', '=', 'detallemovcaja.servicio_id')
                            ->where('detallemovcaja.movimiento_id', '=', $id)
                            ->select('detallemovcaja.*');
                $lista2            = $resultado->get();
                $c=0;$total=0;$total1=0;
                foreach($lista2 as $key2 => $v){$c=$c+1;
                    $pdf::SetFont('helvetica','',7.5);
                    $pdf::Cell(10,7,$c,1,0,'C');
                    if($v->servicio_id>"0"){
                        if(!is_null($v->servicio) && $v->servicio->tipopago=="Convenio"){
                            $codigo=$v->servicio->tarifario->codigo;
                            $nombre=trim($v->descripcion);    
                            $tiposervicio_id=$v->servicio->tiposervicio_id;
                        }else{
                            $codigo="-";
                            if(!is_null($v->servicio) && $v->servicio_id>"0"){
                                $nombre=$v->servicio->nombre;
                                $tiposervicio_id=$v->servicio->tiposervicio_id;
                            }else{
                                $nombre=trim($v->descripcion);
                                $tiposervicio_id=0;
                            }
                        }
                    }else{
                        $codigo="-";
                        $nombre=trim($v->descripcion);
                        $tiposervicio_id=0;
                    }

                    if(strlen($nombre)<65){
                        $pdf::Cell(130,7,($nombre),1,0,'L');
                    }else{
                        $x=$pdf::GetX();
                        $y=$pdf::GetY();
                        $pdf::Multicell(130,3.5,($nombre),1,'L');
                        $pdf::SetXY($x,$y);
                        $pdf::Cell(130,7,"",1,0,'L');
                    }
                    $pdf::Cell(15,7,number_format($v->cantidad,2,'.',''),1,0,'R');

                    strpos($nombre,'FARMACIA INA') !== false ? $inafecta = 1 : null;
                    
                    if(strpos($nombre,'CONSULTA') === false && strpos($nombre,'CONS ') === false && $tiposervicio_id!="1") {
                        $v->precio=number_format($v->precio*100/(100-$value->montoinicial),2,'.','');
                    }
                    if($value->igv>0){
                        if(strpos($nombre,'CONSULTA') === false && strpos($nombre,'CONS ') === false && $tiposervicio_id!="1") {
                            $pr=number_format($v->precio/1.18,2,'.','');
                            $pc=number_format($v->precio*$v->cantidad/1.18,2,'.','');
                            $pdf::Cell(20,7,$pr,1,0,'R');
                            $pdf::Cell(20,7,$pc,1,0,'R');
                            $total=$total+$pc;
                            $total1=$total1+$pc;
                        }else{
                            $cop=number_format($value->copago+round($v->precio/1.18,2),2,'.','');
                            $cop1=number_format($value->copago+round($v->precio*$v->cantidad/1.18,2),2,'.','');
                            $pdf::Cell(20,7,$cop,1,0,'R');
                            $pdf::Cell(20,7,$cop1,1,0,'R');
                            $total=$total+$cop1;
                            //+number_format($v->precio*$v->cantidad/1.18,2,'.','')
                        }
                    }else{
                        $pdf::Cell(20,7,number_format($v->precio,2,'.',''),1,0,'R');
                        $pdf::Cell(20,7,number_format($v->precio*$v->cantidad,2,'.',''),1,0,'R');
                        $total=$total+number_format($v->precio*$v->cantidad,2,'.','');
                        $total1=$total1+number_format($v->precio*$v->cantidad,2,'.','');
                    }
                    $pdf::Ln();                    
                }
                $pdf::SetFont('helvetica','',8);
                $pdf::Cell(175,5,"",0,0,'L');
                $pdf::Cell(20,5,number_format($total,2,'.',''),0,0,'R');
                $pdf::Ln();
                $pdf::Cell(100,5,"",0,0,'L');
                $pdf::SetFont('helvetica','B',8);
                $pdf::Cell(75,5,"COPAGO:",0,0,'R');
                $pdf::SetFont('helvetica','',8);
                $pdf::Cell(20,5,number_format($value->copago,2,'.',''),0,0,'R');
                $pdf::Ln();
                $pdf::Cell(100,5,"",0,0,'L');
                $pdf::SetFont('helvetica','B',8);
                $pdf::Cell(75,5,"COASEGURO ".number_format($value->montoinicial,2,'.','').'%'.":",0,0,'R');
                $pdf::SetFont('helvetica','',8);
                $pdf::Cell(20,5,number_format($total1*$value->montoinicial/100,2,'.',''),'B',0,'R');
                $pdf::Ln();                    
                $pdf::SetFont('helvetica','B',8);
                $pdf::Cell(100,5,"",0,0,'L');
                $pdf::Cell(75,5,utf8_decode('SUBTOTAL:'),0,0,'R');
                $pdf::SetFont('helvetica','',8);
                $pdf::Cell(20,5,number_format($subtotal,2,'.',''),0,0,'R');
                $pdf::Ln();
                $pdf::SetFont('helvetica','B',8);
                $pdf::Cell(100,5,"",0,0,'L');
                $pdf::Cell(75,5,utf8_decode('IGV 18%:'),0,0,'R');
                $pdf::SetFont('helvetica','',8);
                if ($inafecta != 0) {
                    $pdf::Cell(20,5,number_format(0,2,'.',''),0,0,'R');
                } else {
                    $pdf::Cell(20,5,number_format($subtotal*0.18,2,'.',''),0,0,'R');
                }
                $pdf::Ln();
                $pdf::Cell(100,5,'',0,0,'L');
                $pdf::SetFont('helvetica','B',8);
                $pdf::Cell(75,5,utf8_decode('TOTAL:'),0,0,'R');
                $pdf::SetFont('helvetica','',8);
                $pdf::Cell(20,5,number_format($value->total,2,'.',''),'T',0,'R');
                $pdf::Ln();
                $pdf::Output('Liquidacion.pdf');
            }
        }
    }

    public function cartaGarantia(Request $request){
        $phpWord = new Word();

        /* Note: any element you append to a document must reside inside of a Section. */

        // Adding an empty Section to the document...
        $section = $phpWord->addSection();
        // Adding Text element to the Section having font styled by default...
        $section->addText(
            '"Learn from yesterday, live for today, hope for tomorrow. '
                . 'The important thing is not to stop questioning." '
                . '(Albert Einstein)'
        );

        /*
         * Note: it's possible to customize font style of the Text element you add in three ways:
         * - inline;
         * - using named font style (new font style object will be implicitly created);
         * - using explicitly created font style object.
         */

        // Adding Text element with font customized inline...
        $section->addText(
            '"Great achievement is usually born of great sacrifice, '
                . 'and is never the result of selfishness." '
                . '(Napoleon Hill)',
            array('name' => 'Tahoma', 'size' => 10)
        );

        // Adding Text element with font customized using named font style...
        $fontStyleName = 'oneUserDefinedStyle';
        $phpWord->addFontStyle(
            $fontStyleName,
            array('name' => 'Tahoma', 'size' => 10, 'color' => '1B2232', 'bold' => true)
        );
        $section->addText(
            '"The greatest accomplishment is not in never falling, '
                . 'but in rising again after you fall." '
                . '(Vince Lombardi)',
            $fontStyleName
        );

        // Adding Text element with font customized using explicitly created font style object...
        $fontStyle = new \PhpOffice\PhpWord\Style\Font();
        $fontStyle->setBold(true);
        $fontStyle->setName('Tahoma');
        $fontStyle->setSize(13);
        $myTextElement = $section->addText('"Believe you can and you\'re halfway there." (Theodor Roosevelt)');
        $myTextElement->setFontStyle($fontStyle);

        // Saving the document as OOXML file...
        $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
        $file = 'HelloWorld.docx';
        header("Content-Description: File Transfer");
        header('Content-Disposition: attachment; filename="' . $file . '"');
        header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
        header('Content-Transfer-Encoding: binary');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Expires: 0');
        $objWriter->save("php://output");
    }
}
