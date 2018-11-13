<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Validator;
use App\Http\Requests;
use App\Conceptopago;
use App\Movimiento;
use App\Person;
use App\Caja;
use App\Detallemovcaja;
use App\Librerias\Libreria;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
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
class GarantiaController extends Controller
{
    protected $folderview      = 'app.garantia';
    protected $tituloAdmin     = 'Garantias Medicas';
    protected $tituloRegistrar = 'Registrar Concepto de Pago';
    protected $tituloModificar = 'Pago Particular';
    protected $tituloEliminar  = 'Eliminar el Pago Particular';
    protected $rutas           = array('create' => 'garantia.create', 
            'pagar'   => 'pagoparticular.pago', 
            'regularizar' => 'garantia.regularizar',
            'delete' => 'garantia.eliminar',
            'search' => 'garantia.buscar',
            'index'  => 'garantia.index',
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
        $entidad          = 'Garantia';
        $nombre           = Libreria::getParam($request->input('nombre'));
        $paciente           = Libreria::getParam($request->input('paciente'));
        $fechainicial     = Libreria::getParam($request->input('fechainicial'));
        $fechafinal       = Libreria::getParam($request->input('fechafinal'));
        $situacion        = Libreria::getParam($request->input('situacion'));
        $resultado        = Movimiento::leftjoin('person as paciente', 'paciente.id', '=', 'movimiento.persona_id')
                ->join('person as medico','medico.id','=','movimiento.doctor_id')
                ->leftjoin('person as usuarioentrega','usuarioentrega.id','=','movimiento.usuarioentrega_id')
                ->join('person as responsable','responsable.id','=','movimiento.responsable_id')
                ->where('movimiento.situacion', 'not like', 'A')
                ->whereIn('movimiento.conceptopago_id',['10'])
                ->where(DB::raw('concat(medico.apellidopaterno,\' \',medico.apellidomaterno,\' \',medico.nombres)'),'like','%'.$nombre.'%')
                ->where(DB::raw('concat(paciente.apellidopaterno,\' \',paciente.apellidomaterno,\' \',paciente.nombres)'),'like','%'.$paciente.'%');

        if($fechainicial!=""){
            $resultado = $resultado->where('movimiento.fecha','>=',$fechainicial);
        }
        if($fechafinal!=""){
            $resultado = $resultado->where('movimiento.fecha','<=',$fechafinal);
        }
        if($situacion!=""){
            $resultado = $resultado->where('movimiento.situacion','LIKE',$situacion);
        }
        $resultado        = $resultado->orderBy('movimiento.fecha', 'ASC')
                            ->select('movimiento.fecha','movimiento.id','movimiento.comentario as servicio2','movimiento.numero as recibo','movimiento.situacion as situacionentrega','movimiento.comentario as servicio','movimiento.fechaentrega','movimiento.id as servicio_id',DB::raw('1 as cantidad'),'movimiento.total as pagodoctor',DB::raw('concat(medico.apellidopaterno,\' \',medico.apellidomaterno,\' \',medico.nombres) as medico'),DB::raw('concat(paciente.apellidopaterno,\' \',paciente.apellidomaterno,\' \',paciente.nombres) as paciente'),'paciente.bussinesname as paciente2',DB::raw('concat(usuarioentrega.apellidopaterno,\' \',usuarioentrega.apellidomaterno,\' \',usuarioentrega.nombres) as usuarioentrega'),'movimiento.tipomovimiento_id',DB::raw('responsable.nombres as responsable'))->orderBy('movimiento.fecha', 'ASC')->orderBy('movimiento.numero','ASC');
        $lista            = $resultado->get();
        //dd($lista);
        $cabecera         = array();
        $cabecera[]       = array('valor' => '#', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Fecha', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Doctor', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Paciente', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Servicio', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Recibo', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Pago', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Usuario', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Situacion', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Fecha Pago', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Usuario Pago', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Operaciones', 'numero' => '2');
        
        $titulo_regularizar = 'Regularizar';
        $ruta             = $this->rutas;
        $user = Auth::user();
        $rs        = Caja::orderBy('nombre','ASC')->get();
        $band=false;
        foreach ($rs as $key => $value) {
            if($request->ip()==$value->ip && $value->id==3){
                $band=true;
            }
        }

        if (count($lista) > 0) {
            $clsLibreria     = new Libreria();
            $paramPaginacion = $clsLibreria->generarPaginacion($lista, $pagina, $filas, "Garantia");
            $paginacion      = $paramPaginacion['cadenapaginacion'];
            $inicio          = $paramPaginacion['inicio'];
            $fin             = $paramPaginacion['fin'];
            $paginaactual    = $paramPaginacion['nuevapagina'];
            $lista           = $resultado->paginate($filas);
            $request->replace(array('page' => $paginaactual));
            return view($this->folderview.'.list')->with(compact('lista', 'paginacion', 'inicio', 'fin', 'entidad', 'cabecera', 'titulo_regularizar', 'ruta', 'band', 'user'));
        }
        return view($this->folderview.'.list')->with(compact('lista', 'entidad', 'band'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $entidad          = 'Garantia';
        $title            = $this->tituloAdmin;
        $titulo_registrar = $this->tituloRegistrar;
        $ruta             = $this->rutas;
        $cboSituacion          = array("" => "Todos", "E" => "Pagado", "N" => "Pendiente");
        return view($this->folderview.'.admin')->with(compact('entidad', 'title', 'titulo_registrar', 'ruta', 'cboSituacion'));
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

    public function regulariza($id)
    {
        $existe = Libreria::verificarExistencia($id, 'movimiento');
        if ($existe !== true) {
            return $existe;
        }
        $user = Auth::user();
        $error = DB::transaction(function() use($id,$user){
            $Venta = Movimiento::find($id);
            $Venta->situacion='E';
            $Venta->fechaentrega = date("Y-m-d");
            $Venta->usuarioentrega_id = $user->person_id;
            $Venta->save();
        });
        return is_null($error) ? "OK" : $error;
    }

    public function regularizar($id, $listarLuego)
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
        $entidad  = 'Garantia';
        $formData = array('route' => array('garantia.regulariza', $id), 'method' => 'Regulariza', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Regularizar';
        return view('app.confirmar')->with(compact('modelo', 'formData', 'entidad', 'boton', 'listar'));
    }


    public function destroy(Request $request)
    {
        $id = $request->input("id");
        $comentarioa = $request->input("comentarioa");

        $existe = Libreria::verificarExistencia($id, 'movimiento');
        if ($existe !== true) {
            return $existe;
        }
        $user = Auth::user();
        $error = DB::transaction(function() use($id, $user, $comentarioa){
            $movimiento = Movimiento::find($id);
            $movimiento->fechaentrega = date("Y-m-d");
            $movimiento->usuarioentrega_id = $user->person_id;
            $movimiento->situacion = 'A';
            $movimiento->motivo_anul = $comentarioa;
            $movimiento->save();
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
        $entidad  = 'Garantia';
        $formData = array('route' => array('garantia.destroy', $id), 'method' => 'DELETE', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Eliminar';
        return view('app.confirmarEliminar2')->with(compact('modelo', 'formData', 'entidad', 'boton', 'listar','id'));
    }

    public function pdfReporte(Request $request){
        $nombre           = Libreria::getParam($request->input('doctor'));
        $paciente           = Libreria::getParam($request->input('paciente'));
        $fechainicial     = Libreria::getParam($request->input('fechainicial'));
        $fechafinal       = Libreria::getParam($request->input('fechafinal'));
        $situacion        = Libreria::getParam($request->input('situacion'));

        $resultado        = Movimiento::leftjoin('person as paciente', 'paciente.id', '=', 'movimiento.persona_id')
                ->join('person as medico','medico.id','=','movimiento.doctor_id')
                ->leftjoin('person as usuarioentrega','usuarioentrega.id','=','movimiento.usuarioentrega_id')
                ->join('person as responsable','responsable.id','=','movimiento.responsable_id')
                ->whereIn('movimiento.conceptopago_id',['10'])
                ->where(DB::raw('concat(medico.apellidopaterno,\' \',medico.apellidomaterno,\' \',medico.nombres)'),'like','%'.$nombre.'%')
                ->where(DB::raw('concat(paciente.apellidopaterno,\' \',paciente.apellidomaterno,\' \',paciente.nombres)'),'like','%'.$paciente.'%');

        if($fechainicial!=""){
            $resultado = $resultado->where('movimiento.fecha','>=',$fechainicial);
        }
        if($fechafinal!=""){
            $resultado = $resultado->where('movimiento.fecha','<=',$fechafinal);
        }
        if($situacion!=""){
            $resultado = $resultado->where('movimiento.situacion','LIKE',$situacion);
        }
        $resultado        = $resultado->orderBy('movimiento.fecha', 'ASC')
                            ->select('movimiento.fecha','movimiento.id','movimiento.comentario as servicio2','movimiento.numero as recibo','movimiento.situacion as situacionentrega','movimiento.comentario as servicio','movimiento.fechaentrega','movimiento.id as servicio_id',DB::raw('1 as cantidad'),'movimiento.total as pagodoctor',DB::raw('concat(medico.apellidopaterno,\' \',medico.apellidomaterno,\' \',medico.nombres) as medico'),DB::raw('concat(paciente.apellidopaterno,\' \',paciente.apellidomaterno,\' \',paciente.nombres) as paciente'),'paciente.bussinesname as paciente2',DB::raw('concat(usuarioentrega.apellidopaterno,\' \',usuarioentrega.apellidomaterno,\' \',usuarioentrega.nombres) as usuarioentrega'),'movimiento.tipomovimiento_id',DB::raw('responsable.nombres as responsable'))->orderBy('movimiento.fecha', 'ASC')->orderBy('movimiento.numero','ASC');
        $lista = $resultado->get();
        $pdf = new TCPDF();
        $pdf::SetTitle('Reporte de Dinero en Garantias por Medico al '.($fechafinal));
        if (count($lista) > 0) {            
            $pdf::AddPage();
            $pdf::SetFont('helvetica','B',12);
            $pdf::Image("http://localhost/juanpablo/dist/img/logo.jpg", 0, 0, 70, 14);
            $pdf::Cell(0,10,utf8_decode("Reporte de Garantias por Medico al ".date("d/m/Y",strtotime($fechafinal))),0,0,'C');
            $pdf::Ln();
            $pdf::SetFont('helvetica','B',7.5);
            $pdf::Cell(15,6,utf8_decode("FECHA"),1,0,'C');
            $pdf::Cell(55,6,utf8_decode("PACIENTE"),1,0,'C');
            $pdf::Cell(55,6,utf8_decode("CONCEPTO"),1,0,'C');
            $pdf::Cell(15,6,utf8_decode("RECIBO"),1,0,'C');
            $pdf::Cell(15,6,utf8_decode("TOTAL"),1,0,'C');
            $pdf::Cell(25,6,utf8_decode("USUARIO"),1,0,'C');
            $pdf::Ln();
            $doctor="";$total=0;$totalgeneral=0;$idmedico=0;$array=array();
            foreach ($lista as $key => $value){
                if($doctor!=($value->medico)){
                    $pdf::SetFont('helvetica','B',7);
                    if($doctor!=""){
                        $pdf::SetFont('helvetica','B',8);
                        $pdf::Cell(125,6,("EFECTIVO :"),1,0,'R');
                        $pdf::Cell(15,6,'',1,0,'L');
                        $pdf::Cell(15,6,number_format($total,2,'.',''),1,0,'C');
                        $totalgeneral = $totalgeneral + $total;
                        $total=0;
                        $pdf::Ln();
                    }
                    $doctor=($value->medico);
                    $idmedico=$value->medico_id;
                    $array[]=$idmedico;
                    $pdf::SetFont('helvetica','B',8);
                    $pdf::Cell(180,6,($doctor),1,0,'L');
                    $pdf::Ln();
                }
                $pdf::SetFont('helvetica','',7);
                $pdf::Cell(15,6,date("d/m/Y",strtotime($value->fecha)),1,0,'C');
                if($value->paciente!=""){
                    $pdf::Cell(55,6,($value->paciente),1,0,'L');
                }else{
                    $pdf::Cell(55,6,($value->paciente2),1,0,'L');
                }
                $pdf::Cell(55,6,($value->servicio2),1,0,'L');
                $pdf::Cell(15,6,$value->recibo,1,0,'L');
                $pdf::Cell(15,6,number_format($value->pagodoctor*$value->cantidad,2,'.',''),1,0,'C');
                $pdf::Cell(25,6,($value->responsable),1,0,'L');
                $total=$total + $value->pagodoctor*$value->cantidad;
                $pdf::Ln();                
            }
            
            $pdf::SetFont('helvetica','B',8);
            $pdf::Cell(125,6,("EFECTIVO :"),1,0,'R');
            $pdf::Cell(15,6,(""),1,0,'R');
            $pdf::Cell(15,6,number_format($total,2,'.',''),1,0,'C');
            $totalgeneral = $totalgeneral + $total;
            $total=0;
            $pdf::Ln();
            $pdf::SetFont('helvetica','B',9);
            $pdf::Cell(140,6,("TOTAL GENERAL:"),1,0,'R');
            $pdf::Cell(15,6,number_format($totalgeneral,2,'.',''),1,0,'C');
            $pdf::Ln();
        }
        $pdf::Output('ReporteGarantia.pdf');
    }

    public function ExcelReporte(Request $request){
        $nombre           = Libreria::getParam($request->input('doctor'));
        $paciente         = Libreria::getParam($request->input('paciente'));
        $fechainicial     = Libreria::getParam($request->input('fechainicial'));
        $fechafinal       = Libreria::getParam($request->input('fechafinal'));
        $situacion        = Libreria::getParam($request->input('situacion'));

        $resultado        = Movimiento::leftjoin('person as paciente', 'paciente.id', '=', 'movimiento.persona_id')
                ->join('person as medico','medico.id','=','movimiento.doctor_id')
                ->leftjoin('person as usuarioentrega','usuarioentrega.id','=','movimiento.usuarioentrega_id')
                ->join('person as responsable','responsable.id','=','movimiento.responsable_id')
                ->whereIn('movimiento.conceptopago_id',['10'])
                ->where(DB::raw('concat(medico.apellidopaterno,\' \',medico.apellidomaterno,\' \',medico.nombres)'),'like','%'.$nombre.'%')
                ->where(DB::raw('concat(paciente.apellidopaterno,\' \',paciente.apellidomaterno,\' \',paciente.nombres)'),'like','%'.$paciente.'%');

        if($fechainicial!=""){
            $resultado = $resultado->where('movimiento.fecha','>=',$fechainicial);
        }
        if($fechafinal!=""){
            $resultado = $resultado->where('movimiento.fecha','<=',$fechafinal);
        }
        if($situacion!=""){
            $resultado = $resultado->where('movimiento.situacion','LIKE',$situacion);
        }
        $resultado        = $resultado->orderBy('movimiento.fecha', 'ASC')
                            ->select('movimiento.fecha','movimiento.id','movimiento.comentario as servicio2','movimiento.numero as recibo','movimiento.situacion as situacionentrega','movimiento.comentario as servicio','movimiento.fechaentrega','movimiento.id as servicio_id',DB::raw('1 as cantidad'),'movimiento.total as pagodoctor',DB::raw('concat(medico.apellidopaterno,\' \',medico.apellidomaterno,\' \',medico.nombres) as medico'),DB::raw('concat(paciente.apellidopaterno,\' \',paciente.apellidomaterno,\' \',paciente.nombres) as paciente'),'paciente.bussinesname as paciente2',DB::raw('concat(usuarioentrega.apellidopaterno,\' \',usuarioentrega.apellidomaterno,\' \',usuarioentrega.nombres) as usuarioentrega'),'movimiento.tipomovimiento_id',DB::raw('responsable.nombres as responsable'))->orderBy('movimiento.fecha', 'ASC')->orderBy('movimiento.numero','ASC');
        $lista = $resultado->get();
        
        Excel::create('ExcelPagoDoctor', function($excel) use($lista,$request) {
 
            $excel->sheet('PagoDoctor', function($sheet) use($lista,$request) {
                $cabecera[] = "Fecha";
                $cabecera[] = "Paciente";
                $cabecera[] = "Concepto";
                $cabecera[] = "Nro Doc.";
                $cabecera[] = "Total";
                $cabecera[] = "Usuario";
                $sheet->row(1,$cabecera);
                $c=2;$d=3;$band=true;$total=0;$doctor="";$totalg=0;$idmedico=0;$array=array();
                foreach ($lista as $key => $value){
                    if($doctor!=$value->medico){
                        if($doctor!=""){
                            $detalle = array();
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
                    if($value->paciente!=""){
                        $detalle[] = $value->paciente;
                    }else{
                        $detalle[] = $value->paciente2;
                    }
                    $detalle[] = $value->servicio2;
                    $detalle[] = $value->recibo;
                    $detalle[] = number_format($value->pagodoctor*$value->cantidad,2,'.','');
                    $detalle[] = $value->responsable;
                    $sheet->row($c,$detalle);
                    $c=$c+1;
                    $total=$total + $value->pagodoctor*$value->cantidad;
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
                $total=0;
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
}
