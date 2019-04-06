<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Validator;
use App\Http\Requests;
use App\Historia;
use App\Convenio;
use App\Movimiento;
use App\Cartagarantia;
use App\Detallemovcaja;
use App\Cotizacion;
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

class CartagarantiaController extends Controller
{
    protected $folderview      = 'app.cartagarantia';
    protected $tituloAdmin     = 'Cartas de Garantía';
    protected $tituloLista     = 'Lista de Cartas de Garantía';
    protected $tituloRegistrar = 'Registrar de Carta de Garantía';
    protected $tituloModificar = 'Modificar Carta de Garantía';
    protected $tituloEliminar  = 'Anular Carta de Garantía';
    protected $rutas           = array('create' => 'cartagarantia.create', 
            'edit'   => 'cartagarantia.edit', 
            'delete' => 'cartagarantia.eliminar',
            'search' => 'cartagarantia.buscar',
            'index'  => 'cartagarantia.index'
        );

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function buscar(Request $request)
    {
        $pagina    = $request->input('page');
        $filas     = $request->input('filas');
        $entidad   = 'CartaGarantia';
        $codigo    = Libreria::getParam($request->input('codigo'),'');
        $fecha     = Libreria::getParam($request->input('fechainicial'));
        $fecha2    = Libreria::getParam($request->input('fechafinal'));
        $tipo      = Libreria::getParam($request->input('tipo'));
        $situacion = Libreria::getParam($request->input('situacion'));
        $plan      = Libreria::getParam($request->input('plan'));
        $user      = Auth::user();
        $resultado = Cartagarantia::leftjoin('cotizacion','cotizacion.id','=','cartagarantia.cotizacion_id')
        			->leftjoin('plan','plan.id','=','cotizacion.plan_id')
                    ->where('plan.razonsocial','like','%'.$plan.'%')
                    ->where('cotizacion.codigo','like','%'.$codigo.'%');
        if($fecha!=""){
            $resultado = $resultado->where('cartagarantia.fecha', '>=', ''.$fecha.'');
        }
        if($fecha2!=""){
            $resultado = $resultado->where('cartagarantia.fecha', '<=', ''.$fecha2.'');
        }
        if($situacion!=""){
            $resultado = $resultado->where('cartagarantia.situacion', '=', ''.$situacion.'');
        }
        if($tipo!=""){
            $resultado = $resultado->where('cotizacion.tipo', '=', ''.$tipo.'');
        }
        $resultado        = $resultado->select('cartagarantia.*')->orderBy('cartagarantia.fecha', 'ASC');
        $lista            = $resultado->get();
        $cabecera         = array();
        $cabecera[]       = array('valor' => '#', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Fecha', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Código Cotiz.', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Paciente', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Plan', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Tipo', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Situación', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Total', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Comentario', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Responsable', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Operaciones', 'numero' => '1');
        
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

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $entidad          = 'CartaGarantia';
        $title            = $this->tituloAdmin;
        $titulo_registrar = $this->tituloRegistrar;
        $titulo_lista     = $this->tituloLista;
        $ruta             = $this->rutas;
        $user             = Auth::user();
        return view($this->folderview.'.admin')->with(compact('entidad', 'title', 'titulo_registrar', 'ruta', 'user', 'titulo_lista'));
    }

    public function create(Request $request)
    {
        $listar   = Libreria::getParam($request->input('listar'), 'NO');
        $entidad  = 'CartaGarantia2';
        $boton    = 'Registrar'; 
        $ruta     = $this->rutas;
        $carta    = null;
        $formData = array('cartagarantia.store');
        $formData = array('route' => $formData, 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        return view($this->folderview.'.mant')->with(compact('entidad', 'boton', 'listar','carta','formData'));
    }

    public function store(Request $request)
    {
        $listar     = Libreria::getParam($request->input('listar'), 'NO');
        $reglas     = array(
                'fechacarta'    => 'required',
                'cotizacion_id' => 'required',
                'paciente_id'   => 'required',
                );
        $mensajes = array(
            'fechacarta.required'     => 'Debe seleccionar una fecha',
            'paciente_id.required'    => 'Debe seleccionar un paciente',
            'cotizacion_id.required'  => 'Debe agregar una cotización',
            );
        $validacion = Validator::make($request->all(), $reglas, $mensajes);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        }       
        $user = Auth::user();
        $dat=array();
        $numerocarta = Cartagarantia::NumeroSigue();
        $error = DB::transaction(function() use($request,$user,$numerocarta,&$dat){
        	$cotizacion              = Cotizacion::find($request->input('cotizacion_id'));
        	$cotizacion->situacion   = 'A';//ACEPTADA
        	$cotizacion->paciente_id = $request->input('paciente_id');//ACEPTADA
        	$cotizacion->total       = $request->input('totalcarta');
        	$cotizacion->save();

            $carta                   = new Cartagarantia();
            $carta->fecha            = $request->input('fechacarta');
            $carta->cotizacion_id    = $cotizacion->id;
            $carta->numero           = $numerocarta;
            $carta->situacion        = 'E';//ENVIADA
            $carta->comentario       = $request->input('comentariocarta');
            $carta->monto            = $cotizacion->total;
            $carta->responsable_id   = $user->person_id; 
            $carta->save();           
            
            $dat['respuesta'] = 'OK';
        });
        return is_null($error) ? json_encode($dat) : $error;
    }

    public function destroy($id)
    {
        $error = DB::transaction(function() use($id){
            $plan = explode("@", $id);
            $listventas = Movimiento::where('plan_id','=',$plan[0])
                            ->where('numerodias','=',$plan[1])
                            ->get();
            foreach ($listventas as $key => $value) {
                $value->tipoventa = 'A';
                $value->save();
            }
        });
        return is_null($error) ? "OK" : $error;
    }

    public function eliminar($plan_id, $numero, $listarLuego)
    {
        $listar = "NO";
        if (!is_null(Libreria::obtenerParametro($listarLuego))) {
            $listar = $listarLuego;
        }
        $modelo   = null;
        $entidad  = 'CartasGarantia2';
        $formData = array('route' => array('cartasgarantia.destroy', $plan_id.'@'.$numero), 'method' => 'DELETE', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Anular';
        return view('app.confirmar')->with(compact('modelo', 'formData', 'entidad', 'boton', 'listar'));
    }

    public function buscarcarta(Request $request)
    {
        $pagina           = $request->input('page');
        $filas            = $request->input('filas');
        $entidad          = 'CartasGarantia2';
        $plan           = Libreria::getParam($request->input('plan2'),'');
        $resultado        = Movimiento::leftjoin('person as responsable','responsable.id','=','movimiento.usuariocarta_id')
                            ->join('plan','plan.id','=','movimiento.plan_id')
                            ->where('plan.razonsocial','like','%'.$plan.'%')
                            ->where('movimiento.tipomovimiento_id','=',9)
                            ->where('movimiento.manual','like','N')
                            ->whereNotNull('movimiento.numerodias')
                            ->whereNotIn('movimiento.situacion',['U','A'])
                            ->groupBy('movimiento.plan_id')
                            ->groupBy('plan.razonsocial')
                            ->groupBy('movimiento.fechacarta')
                            ->groupBy('movimiento.tipoventa')
                            ->groupBy('movimiento.numerodias')
                            ->groupBy('responsable.nombres');
        $resultado        = $resultado->select(DB::raw('plan.razonsocial as empresa'),DB::raw('sum(movimiento.total) as total'),DB::raw('count(*) as documentos'),'movimiento.numerodias','movimiento.plan_id','movimiento.fechacarta','movimiento.tipoventa','responsable.nombres')->orderBy('plan.razonsocial', 'asc')->orderBy('movimiento.numerodias','desc');
        $lista            = $resultado->get();
        $cabecera         = array();
        $cabecera[]       = array('valor' => 'Nro', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Empresa', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Documentos', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Fecha', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Usuario', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Total', 'numero' => '1');
        
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
            return view($this->folderview.'.listCarta')->with(compact('lista', 'totalfac', 'paginacion', 'inicio', 'fin', 'entidad', 'cabecera', 'titulo_modificar', 'titulo_eliminar', 'ruta'));
        }
        return view($this->folderview.'.listCarta')->with(compact('lista', 'entidad','conf'));
    }

    public function personaautocompletar($searching)
    {      
        $resultado = Person::where(DB::raw('CONCAT(person.nombres, " ", person.apellidopaterno, " ", person.apellidomaterno)'), 'LIKE', '%'.strtoupper($searching).'%');
        $list      = $resultado->get();
        $data = array();
        foreach ($list as $key => $value) {
            $data[] = array(
            	'label' => $value->dni . ' - ' . $value->nombres . ' ' . $value->apellidopaterno . ' ' . $value->apellidomaterno,
                'value' => $value->dni . ' - ' . $value->nombres . ' ' . $value->apellidopaterno . ' ' . $value->apellidomaterno,
                'id'=> $value->id,
            );
        }
        return json_encode($data);
    }

    public function buscarcotizacion($searching)
    {      
        $resultado = Cotizacion::where('codigo', '=', ''.strtoupper($searching).'')
        						->where('situacion', '=', 'E')
        						->first();
        $data = array();
        if($resultado !== NULL) {  
        	$tipo = 'AMBULATORIO';
        	if($resultado->tipo == 'H') {
        		$tipo = 'HOSPITALARIO';
        	}  	
            $data['id'] = $resultado->id;
            $data['codigo'] = $resultado->codigo;
            $data['plan'] = $resultado->plan->nombre;
            $data['fecha'] = $resultado->fecha;
            $data['tipo'] = $tipo;
            $data['total'] = $resultado->total;
        } else {
        	$data['codigo'] = '';
        }
	        
        return json_encode($data);
    }
}
