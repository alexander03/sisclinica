<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Validator;
use App\Http\Requests;
use App\Historia;
use App\HistoriaClinica;
use App\Movimiento;
use App\Seguimiento;
use App\Convenio;
use App\Cita;
use App\Departamento;
use App\Provincia;
use App\Distrito;
use App\Person;
use App\Plan;
use App\Detallehistoriacie;
use App\Examenhistoriaclinica;
use App\Rolpersona;
use App\Librerias\Libreria;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Elibyy\TCPDF\Facades\TCPDF;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

ini_set('memory_limit', '512M'); //Raise to 512 MB
ini_set('max_execution_time', '60000'); //Raise to 512 MB 

class HistoriaController extends Controller
{
    protected $folderview      = 'app.historia';
    protected $tituloAdmin     = 'Historia';
    protected $tituloRegistrar = 'Registrar historia';
    protected $tituloModificar = 'Modificar historia';
    protected $tituloEliminar  = 'Eliminar historia';
    protected $rutas           = array('create' => 'historia.create', 
            'edit'   => 'historia.edit', 
            'delete' => 'historia.eliminar',
            'search' => 'historia.buscar',
            'buscaProv' => 'historia.buscaProv',
            'buscaDist' => 'historia.buscaDist',
            'index'  => 'historia.index',
            'fallecido'  => 'historia.fallecido',
        );

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function buscar(Request $request)
    {
        $sucursal_id      = Session::get('sucursal_id');
        $pagina           = $request->input('page');
        $filas            = $request->input('filas');
        $entidad          = 'Historia';
        $nombre           = Libreria::getParam($request->input('nombre'),'');
        $dni              = Libreria::getParam($request->input('dni'));
        $numero           = Libreria::getParam($request->input('numero'));
        $numero2          = Libreria::getParam($request->input('numero2'));
        $tipopaciente             = Libreria::getParam($request->input('tipopaciente'));
        $resultado        = Historia::join('person', 'person.id', '=', 'historia.person_id')
                            ->leftjoin('convenio', 'convenio.id', '=', 'historia.convenio_id')
                            //->where('historia.sucursal_id', '=', $sucursal_id)
                            ->where(DB::raw('concat(apellidopaterno,\' \',apellidomaterno,\' \',nombres)'), 'LIKE', '%'.strtoupper($nombre).'%')
                            ->where('person.dni', 'LIKE', '%'.strtoupper($dni).'%');
        if($tipopaciente!=""){
            $resultado = $resultado->where('historia.tipopaciente', 'LIKE', ''.strtoupper($tipopaciente).'');
        }
        if($numero!=""){
            $resultado = $resultado->where('historia.numero', 'LIKE', '%'.strtoupper($numero).'%');   
        }
        if($numero2!=""){
            $resultado = $resultado->where('historia.numero2', 'LIKE', '%'.strtoupper($numero2).'%');   
        }
        $resultado        = $resultado->select('historia.*')->orderBy('historia.numero', 'ASC');
        $vistamedico           = $request->input('vistamedico');
        if($vistamedico != "SI"){ 
            $resultado = $resultado->limit(100);
        }
        $lista            = $resultado->get();
        $cabecera         = array();
        $cabecera[]       = array('valor' => '#', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Nro Historia', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Nro Historia2', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Paciente', 'numero' => '1');
        $cabecera[]       = array('valor' => 'DNI', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Tipo Paciente', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Telefono', 'numero' => '1');
        //$cabecera[]       = array('valor' => 'Fecha Nacimiento', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Direccion', 'numero' => '1');

        if($vistamedico != "SI"){
            $cabecera[]       = array('valor' => 'Operaciones', 'numero' => '7');
        }else{
            $cabecera[]       = array('valor' => 'Ver Citas', 'numero' => '1');
        }
        
        $titulo_modificar = $this->tituloModificar;
        $titulo_eliminar  = $this->tituloEliminar;
        $ruta             = $this->rutas;
        $user = Auth::user();
        if (count($lista) > 0) {
            $clsLibreria     = new Libreria();
            $paramPaginacion = $clsLibreria->generarPaginacion($lista, $pagina, $filas, $entidad);
            $paginacion      = $paramPaginacion['cadenapaginacion'];
            $inicio          = $paramPaginacion['inicio'];
            $fin             = $paramPaginacion['fin'];
            $paginaactual    = $paramPaginacion['nuevapagina'];
            $lista           = $resultado->paginate($filas);
            $request->replace(array('page' => $paginaactual));
            return view($this->folderview.'.list')->with(compact('lista', 'paginacion', 'inicio', 'vistamedico' ,'fin', 'entidad', 'cabecera', 'titulo_modificar', 'titulo_eliminar', 'ruta', 'user'));
        }
        return view($this->folderview.'.list')->with(compact('lista', 'entidad'));
    }

    public function index()
    {
        $entidad          = 'Historia';
        $title            = $this->tituloAdmin;
        $titulo_registrar = $this->tituloRegistrar;
        $ruta             = $this->rutas;
        $cboTipoPaciente  = array("" => "Todos","Particular" => "Particular", "Convenio" => "Convenio", "Hospital" => "Hospital");
        return view($this->folderview.'.admin')->with(compact('entidad', 'title', 'titulo_registrar', 'ruta', 'cboTipoPaciente'));
    }

    public function create(Request $request)
    {
        $listar              = Libreria::getParam($request->input('listar'), 'NO');
        $modo              = $request->input('modo','');
        $entidad             = 'Historia';
        $historia = null;
        $cboConvenio = array();
        $cboDepa = array('---- Elija uno ----');
        $convenios = Convenio::where(DB::raw('1'),'=','1')->orderBy('nombre','ASC')->get();
        foreach ($convenios as $key => $value) {
            $cboConvenio = $cboConvenio + array($value->id => $value->nombre);
        }
        $departamentos = Departamento::orderBy('nombre','ASC')->get();
        foreach ($departamentos as $key => $value) {
            $cboDepa = $cboDepa + array($value->id => $value->nombre);
        }
        $cboEstadoCivil = array("SOLTERO(A)"=>"SOLTERO(A)","CASADO(A)"=>"CASADO(A)","VIUDO(A)"=>"VIUDO(A)","DIVORCIADO(A)"=>"DIVORCIADO(A)","CONVIVIENTE"=>"CONVIVIENTE");
        $cboSexo = array("M"=>"M","F"=>"F");
        $cboCategoria = array("Normal"=>"Normal","Religioso"=>"Religioso","Doctor"=>"Doctor","Familiar Trabajador"=>"Familiar Trabajador","Aldeas Infantiles"=>"Aldeas Infantiles");
        $formData            = array('historia.store');
        $cboTipoPaciente     = array("Particular" => "Particular", "Convenio" => "Convenio", "Hospital" => "Hospital");
        $cboModo             = array("F" => "Fisico", "V" => "Registro Virtual");
        $sucursal_id         = Session::get('sucursal_id');
        $num = Historia::NumeroSigue($sucursal_id);
        $user = Auth::user();
        $formData            = array('route' => $formData, 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton               = 'Registrar'; 
        return view($this->folderview.'.mant')->with(compact('historia', 'formData', 'entidad', 'boton', 'listar', 'cboTipoPaciente', 'cboConvenio', 'cboEstadoCivil', 'modo', 'cboSexo', 'cboDepa', 'cboModo', 'num', 'user', 'cboCategoria'));
    }

    public function buscaProv($departamento)
    {
        $provincias = Provincia::where('departamento_id','=',$departamento)->orderBy('nombre','ASC')->get();
        $cboProv = '<option value="0">---- Elija uno ----</option>';
        foreach ($provincias as $key => $value) {
            $cboProv = $cboProv.'<option value="'.$value->id.'">'.$value->nombre.'</option>';
        }
        echo $cboProv;
    }

    public function buscaDist($provincia)
    {
        $distritos = Distrito::where('provincia_id','=',$provincia)->orderBy('nombre','ASC')->get();
        $cboDist = '<option value="0">---- Elija uno ----</option>';
        foreach ($distritos as $key => $value) {
            $cboDist = $cboDist.'<option value="'.$value->id.'">'.$value->nombre.'</option>';
        }
        echo $cboDist;
    }

    public function store(Request $request)
    {
        $listar     = Libreria::getParam($request->input('listar'), 'NO');
        $modo     = $request->input('modo','');
        $reglas     = array(
                'nombres'                  => 'required',
                'apellidopaterno'          => 'required',
                'apellidomaterno'          => 'required',
                'telefono'          => 'required',
                );
        $mensajes = array(
            'apellidopaterno.required'         => 'Debe ingresar un apellido paterno',
            'apellidomaterno.required'         => 'Debe ingresar un apellido materno',
            'nombres.required'         => 'Debe ingresar un nombre',
            );
        $validacion = Validator::make($request->all(), $reglas, $mensajes);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        }

        $dni = $request->input('dni');
        $mdlPerson = new Person();
        $resultado = Person::where('dni','LIKE',$dni);
        $value     = $resultado->first();
        $sucursal_id      = Session::get('sucursal_id');
        if(count($value)>0 && strlen(trim($dni))>0){
            $objHistoria = new Historia();
            //$list2       = Historia::where('person_id','=',$value->id)->where('historia.sucursal_id','=',$sucursal_id)->first();
            $list2       = Historia::where('person_id','=',$value->id)->first();
            if(count($list2)>0){//SI TIENE HISTORIA
                return $dat[0]=array("respuesta"=>"Ya tiene historia");
            }else{//NO TIENE HISTORIA PERO SI ESTA REGISTRADO LA PERSONA COMO PROVEEDOR O PERSONAL
                $idpersona=$value->id;
            }
        }else{
            $idpersona=0;
        }        
        $dat=array();
        $user = Auth::user();
        $error = DB::transaction(function() use($request,$idpersona,$user,&$dat){
            $sucursal_id      = Session::get('sucursal_id');
            $Historia       = new Historia();
            if($idpersona==0){
                $person = new Person();
                $person->dni=$request->input('dni');
                $person->apellidopaterno=trim(strtoupper($request->input('apellidopaterno')));
                $person->apellidomaterno=trim(strtoupper($request->input('apellidomaterno')));
                $person->nombres=trim(strtoupper($request->input('nombres')));
                $person->telefono=$request->input('telefono');
                $person->direccion=trim($request->input('direccion'));
                $person->telefono2=$request->input('telefono2');
                $person->sexo=$request->input('sexo');
                $person->email=$request->input('email');
                if($request->input('fechanacimiento')!=""){
                    $person->fechanacimiento=$request->input('fechanacimiento');
                }
                $person->save();
                $idpersona=$person->id;
            }else{
                $person = Person::find($idpersona);
            }
            $Historia->person_id = $idpersona;
            $Historia->tipopaciente=$request->input('tipopaciente');
            $Historia->fecha=date("Y-m-d");
            $Historia->enviadopor=$request->input('enviadopor');
            $Historia->familiar=$request->input('familiar');
            $Historia->modo=$request->input('modo');
            $Historia->estadocivil=$request->input('estadocivil');
            $Historia->ocupacion=$request->input('ocupacion');
            $Historia->departamento=$request->input('departamento');
            $Historia->provincia=$request->input('provincia');
            $Historia->distrito=$request->input('distrito');
            $Historia->categoria=$request->input('categoria');
            $Historia->detallecategoria=$request->input('detallecategoria');
            $Historia->usuario_id=$user->person_id;
            if($request->input('tipopaciente')=="Convenio"){
                $Historia->convenio_id=$request->input('convenio');
                $Historia->empresa=$request->input('empresa');
                $Historia->carnet=$request->input('carnet');
                $Historia->plan_susalud=$request->input('plan_susalud');
                $Historia->poliza=$request->input('poliza');
                $Historia->soat=$request->input('soat');
                $Historia->titular=$request->input('titular');
            }
            $Historia->numero = Historia::NumeroSigue($sucursal_id);
            //$Historia->sucursal_id = $sucursal_id;
            $Historia->save();
            $RolPersona = new RolPersona();
            $RolPersona->rol_id = 3;
            $RolPersona->person_id = $idpersona;
            $RolPersona->save();
            
            $dat[0]=array("respuesta"=>"OK","id"=>$Historia->id,"paciente"=>$person->apellidopaterno.' '.$person->apellidomaterno.' '.$person->nombres,"historia"=>$Historia->numero,"person_id"=>$Historia->person_id,"tipopaciente"=>$Historia->tipopaciente);            
        });
        if($modo=="popup"){
            return is_null($error) ? json_encode($dat) : $error;
        }else{
            return is_null($error) ? json_encode($dat) : $error;
        }
    }

    public function show($id)
    {
        //
    }

    public function edit($id, Request $request)
    {
        $existe = Libreria::verificarExistencia($id, 'Historia');
        if ($existe !== true) {
            return $existe;
        }
        $listar              = Libreria::getParam($request->input('listar'), 'NO');
        $modo              = $request->input('modo','');
        $historia = Historia::join('person','person.id','=','historia.person_id')->where('historia.id','=',$id)->select('historia.*')->select('person.*','historia.*')->first();
        $entidad             = 'Historia';
        $cboConvenio = array();
        $convenios = Convenio::where(DB::raw('1'),'=','1')->orderBy('nombre','ASC')->get();
        foreach ($convenios as $key => $value) {
            $cboConvenio = $cboConvenio + array($value->id => $value->nombre);
        }
        $cboEstadoCivil = array("SOLTERO(A)"=>"SOLTERO(A)","CASADO(A)"=>"CASADO(A)","VIUDO(A)"=>"VIUDO(A)","DIVORCIADO(A)"=>"DIVORCIADO(A)","CONVIVIENTE"=>"CONVIVIENTE");
        $cboCategoria = array("Normal"=>"Normal","Religioso"=>"Religioso","Doctor"=>"Doctor","Familiar Trabajador"=>"Familiar Trabajador","Aldeas Infantiles"=>"Aldeas Infantiles");
        $cboSexo = array("M"=>"M","F"=>"F");
        $cboModo             = array("F" => "Fisico", "V" => "Registro Virtual");
        $cboTipoPaciente     = array("Particular" => "Particular", "Convenio" => "Convenio", "Hospital" => "Hospital");
        $user = Auth::user();
        $formData            = array('historia.update', $id);
        $formData            = array('route' => $formData, 'method' => 'PUT', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton               = 'Modificar';

        $cboDepa               = array();
        $departamentos = Departamento::orderBy('nombre','ASC')->get();
        foreach ($departamentos as $key => $value) {
            $cboDepa = $cboDepa + array($value->id => $value->nombre);
        }
        return view($this->folderview.'.mant')->with(compact('historia', 'formData', 'entidad', 'boton', 'listar', 'cboConvenio', 'cboTipoPaciente', 'cboEstadoCivil', 'modo', 'cboSexo', 'cboDepa', 'cboModo', 'user', 'cboCategoria'));
    }

    public function update(Request $request, $id)
    {
        $sucursal_id = Session::get('sucursal_id');
        $existe = Libreria::verificarExistencia($id, 'Historia');
        if ($existe !== true) {
            return $existe;
        }
        $reglas     = array(
                'nombres'                  => 'required',
                'apellidopaterno'          => 'required',
                'apellidomaterno'          => 'required',
                'telefono'          => 'required',
                );
        $mensajes = array(
            'apellidopaterno.required'         => 'Debe ingresar un apellido paterno',
            'apellidomaterno.required'         => 'Debe ingresar un apellido materno',
            'nombres.required'         => 'Debe ingresar un nombre',
            );
        $validacion = Validator::make($request->all(), $reglas, $mensajes);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        }
        $dni = $request->input('dni');
        $mdlPerson = new Person();
        $resultado = Person::where('dni','LIKE',$dni);
        $value     = $resultado->first();
        if(count($value)>0 && strlen(trim($dni))>0){
            $objHistoria = new Historia();
            $list2       = Historia::where('person_id','=',$value->id)->where('historia.sucursal_id', $sucursal_id)->where('id','<>',$id)->first();
            if(count($list2)>0){//SI TIENE HISTORIA
                return "Ya tiene otra historia";
            }else{//NO TIENE HISTORIA PERO SI ESTA REGISTRADO LA PERSONA COMO PROVEEDOR O PERSONAL
                $idpersona=$value->id;
            }
        }else{
            $idpersona=0;
        }    
        $error = DB::transaction(function() use($request, $id, $idpersona){
            $sucursal_id      = Session::get('sucursal_id');
            $Historia = Historia::find($id);
            if($Historia->modo=="V" && $request->input('modo')=="F"){
                $Historia->fechamodo=date("Y-m-d");
            }
            if($idpersona==0){
                $person = new Person();
                $person->dni=$request->input('dni');
                $person->apellidopaterno=trim(strtoupper($request->input('apellidopaterno')));
                $person->apellidomaterno=trim(strtoupper($request->input('apellidomaterno')));
                $person->nombres=trim(strtoupper($request->input('nombres')));
                $person->telefono=$request->input('telefono');
                $person->direccion=trim($request->input('direccion'));
                $person->nombres=$request->input('nombres');
                $person->telefono2=$request->input('telefono2');
                $person->sexo=$request->input('sexo');
                $person->email=$request->input('email');
                if($request->input('fechanacimiento')!=""){
                    $person->fechanacimiento=$request->input('fechanacimiento');
                }
                $person->save();
                $idpersona=$person->id;
                $list = Movimiento::where('persona_id','=',$Historia->person_id)->where('tipomovimiento_id','=',1)->get();
                foreach ($list as $key => $value) {
                    $value->persona_id=$idpersona;
                    $value->save();
                }
            }else{
                $person = Person::find($idpersona);
                $person->dni=$request->input('dni');
                $person->apellidopaterno=trim($request->input('apellidopaterno'));
                $person->apellidomaterno=trim($request->input('apellidomaterno'));
                $person->nombres=trim($request->input('nombres'));
                $person->telefono=$request->input('telefono');
                $person->direccion=trim($request->input('direccion'));
                $person->telefono2=$request->input('telefono2');
                $person->sexo=$request->input('sexo');
                $person->email=$request->input('email');
                if($request->input('fechanacimiento')!=""){
                    $person->fechanacimiento=$request->input('fechanacimiento');
                }
                $person->save();
                $idpersona=$person->id;
            }
            $Historia->person_id = $idpersona;
            $Historia->numero = $request->input('numero');
            $Historia->tipopaciente=$request->input('tipopaciente');
            //$Historia->fecha=date("Y-m-d");
            $Historia->enviadopor=$request->input('enviadopor');
            $Historia->familiar=$request->input('familiar');
            $Historia->estadocivil=$request->input('estadocivil');
            $Historia->modo=$request->input('modo');
            $Historia->departamento=$request->input('departamento');
            $Historia->provincia=$request->input('provincia');
            $Historia->distrito=$request->input('distrito');
            $Historia->categoria=$request->input('categoria');
            $Historia->detallecategoria=$request->input('detallecategoria');
            if($request->input('tipopaciente')=="Convenio"){
                $Historia->convenio_id=$request->input('convenio');
                $Historia->empresa=$request->input('empresa');
                $Historia->carnet=$request->input('carnet');
                $Historia->plan_susalud=$request->input('plan_susalud');
                $Historia->poliza=$request->input('poliza');
                $Historia->soat=$request->input('soat');
                $Historia->titular=$request->input('titular');
            }
            //$Historia->sucursal_id = $sucursal_id;
            $Historia->save();
        });
        $dat=array();
        $dat[0]=array("respuesta"=>"OK");
        return is_null($error) ? json_encode($dat) : $error;
    }

    public function destroy($id)
    {
        $existe = Libreria::verificarExistencia($id, 'Historia');
        if ($existe !== true) {
            return $existe;
        }
        $error = DB::transaction(function() use($id){
            $Historia = Historia::find($id);
            $Historia->delete();
        });
        return is_null($error) ? "OK" : $error;
    }

    public function eliminar($id, $listarLuego)
    {
        $existe = Libreria::verificarExistencia($id, 'Historia');
        if ($existe !== true) {
            return $existe;
        }
        $listar = "NO";
        if (!is_null(Libreria::obtenerParametro($listarLuego))) {
            $listar = $listarLuego;
        }
        $modelo   = Historia::find($id);
        $entidad  = 'Historia';
        $formData = array('route' => array('historia.destroy', $id), 'method' => 'DELETE', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Eliminar';
        return view('app.confirmarEliminar')->with(compact('modelo', 'formData', 'entidad', 'boton', 'listar'));
    }
    
    public function validarDNI(Request $request)
    {
        $dni = $request->input("dni");
        $entidad    = 'Person';
        $mdlPerson = new Person();
        $resultado = Person::where('dni','LIKE',$dni);
        $value     = $resultado->first();
        $sucursal_id = Session::get('sucursal_id');
        if(count($value)>0){
            $objHistoria = new Historia();
            //$list2       = Historia::where('person_id','=',$value->id)->where('historia.sucursal_id','=',$sucursal_id)->first();
            $list2       = Historia::where('person_id','=',$value->id)->first();
            if(count($list2)>0){//SI TIENE HISTORIA
                $data[] = array(
                            'apellidopaterno' => $value->apellidopaterno,
                            'apellidomaterno' => $value->apellidomaterno,
                            'nombres' => $value->nombres,
                            'telefono' => $value->telefono,
                            'direccion' => $value->direccion,
                            'id'    => $value->id,
                            'msg' => 'N',
                        );
            }else{//NO TIENE HISTORIA PERO SI ESTA REGISTRADO LA PERSONA COMO PROVEEDOR O PERSONAL
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
    
    public function personautocompletar($searching)
    {
        $entidad    = 'Historia';    
        $sucursal_id      = Session::get('sucursal_id');    
        $resultado = Historia::join('person', 'person.id', '=', 'historia.person_id')
                            ->leftjoin('convenio', 'convenio.id', '=', 'historia.convenio_id')
                            ->where(DB::raw('concat(person.dni,\' \',apellidopaterno,\' \',apellidomaterno,\' \',nombres)'), 'LIKE', '%'.strtoupper($searching).'%')
                            //->where('historia.sucursal_id', '=', $sucursal_id)
                            ->select('historia.*','convenio.nombre as convenio2','convenio.plan_id');
        $list      = $resultado->get();
        $data = array();
        foreach ($list as $key => $value) {
            if($value->plan_id){
                $pl = Plan::find($value->plan_id);
                $plan=$pl->nombre;
                $coa=$pl->coaseguro;
                $deducible=$pl->deducible;
                $ruc=$pl->ruc;
                $direccion=$pl->direccion;
                $razonsocial=$pl->razonsocial;
                $tipo=$pl->tipo;
            }else{
                $pl = Plan::find(6);
                $plan=$pl->nombre;
                $coa=$pl->coaseguro;
                $deducible=$pl->deducible;
                $ruc=$pl->ruc;
                $direccion=$pl->direccion;
                $razonsocial=$pl->razonsocial;
                $tipo=$pl->tipo;
            }
            $data[] = array(
                            'label' => $value->persona->dni.' '.$value->persona->apellidopaterno.' '.$value->persona->apellidomaterno.' '.$value->persona->nombres,
                            'id'    => $value->id,
                            'value' => $value->persona->dni.' '.$value->persona->apellidopaterno.' '.$value->persona->apellidomaterno.' '.$value->persona->nombres,
                            'value2' => $value->persona->apellidopaterno.' '.$value->persona->apellidomaterno.' '.$value->persona->nombres,
                            'numero'=> $value->numero,
                            'person_id' => $value->persona->id,
                            'dni' => $value->persona->dni,
                            'tipopaciente' => $value->tipopaciente,
                            'telefono' => $value->persona->telefono,
                            'fallecido' => $value->fallecido,
                            'placa' => $value->poliza,
                            'convenio' => $value->convenio2,
                            'plan_id' => $pl->id,
                            'plan' => $plan,
                            'coa' => $coa,
                            'deducible' => $deducible,
                            'ruc' => $ruc,
                            'direccion' => $direccion,
                            'razonsocial' => $razonsocial,
                            'tipo' => $tipo,
                            'direccion2' => $value->persona->direccion,
                            'edad' => ($value->persona->fechanacimiento==""?'0':$value->persona->fechanacimiento),
                            'fecha' => date('Y-m-d'),
                        );
        }
        return json_encode($data);
    }
    
    public function historiaautocompletar($searching)
    {
        $entidad    = 'Historia';
        $sucursal_id      = Session::get('sucursal_id');     
        $resultado = Historia::join('person', 'person.id', '=', 'historia.person_id')
                            ->leftjoin('convenio', 'convenio.id', '=', 'historia.convenio_id')
                            ->where('historia.numero', 'LIKE', '%'.strtoupper($searching).'%')
                            ->whereNull('person.deleted_at')
                            //->where('historia.sucursal_id', '=', $sucursal_id)
                            ->select('historia.*','convenio.nombre as convenio2','convenio.plan_id');
        $list      = $resultado->get();
        $data = array();
        foreach ($list as $key => $value) {
            if($value->plan_id){
                $pl = Plan::find($value->plan_id);
                $plan=$pl->nombre;
                $coa=$pl->coaseguro;
                $deducible=$pl->deducible;
                $ruc=$pl->ruc;
                $direccion=$pl->direccion;
                $razonsocial=$pl->razonsocial;
                $tipo=$pl->tipo;
            }else{
                $plan='';
                $coa=0;
                $deducible=0;
                $ruc='';
                $direccion='';
                $razonsocial='';
                $tipo='';
            }
            $data[] = array(
                            'label' => $value->persona->dni.' '.$value->persona->apellidopaterno.' '.$value->persona->apellidomaterno.' '.$value->persona->nombres,
                            'id'    => $value->id,
                            'value' => $value->persona->dni.' '.$value->persona->apellidopaterno.' '.$value->persona->apellidomaterno.' '.$value->persona->nombres,
                            'value2' => $value->persona->apellidopaterno.' '.$value->persona->apellidomaterno.' '.$value->persona->nombres,
                            'numero'=> $value->numero,
                            'person_id' => $value->persona->id,
                            'dni' => $value->persona->dni,
                            'tipopaciente' => $value->tipopaciente,
                            'telefono' => $value->persona->telefono,
                            'fallecido' => $value->fallecido,
                            'placa' => $value->poliza,
                            'convenio' => $value->convenio2,
                            'plan_id' => $value->plan_id,
                            'plan' => $plan,
                            'coa' => $coa,
                            'deducible' => $deducible,
                            'ruc' => $ruc,
                            'direccion' => $direccion,
                            'razonsocial' => $razonsocial,
                            'tipo' => $tipo,
                            'direccion2' => $value->persona->direccion,
                            'edad' => ($value->persona->fechanacimiento==""?'0':$value->persona->fechanacimiento),
                            'fecha' => date('Y-m-d'),
                        );
        }
        return json_encode($data);
    }

   	public function pdfSeguimiento(Request $request){
        $resultado        = Seguimiento::where('historia_id','=',$request->id)->orderBy('fechaenvio', 'ASC');
        $lista            = $resultado->get();
        if (count($lista) > 0) {            
            $historia = Historia::find($request->id);
            $pdf = new TCPDF();
            $pdf::SetTitle('Seguimiento de Historia');
            $pdf::AddPage();
            $pdf::SetFont('helvetica','B',12);
            $pdf::Cell(0,10,utf8_decode("SEGUIMIENTO DE HISTORIA ".$historia->numero),0,0,'C');
            $pdf::Ln();
            $pdf::SetFont('helvetica','B',10);
            $pdf::Cell(20,9,utf8_decode("PACIENTE: "),0,0,'C');
            $pdf::SetFont('helvetica','',10);
            $pdf::Cell(0,9,utf8_decode($historia->persona->apellidopaterno." ".$historia->persona->apellidomaterno." ".$historia->persona->nombres),0,0,'L');
            $pdf::Ln();
            $pdf::SetFont('helvetica','B',9);
            $pdf::Cell(20,6,utf8_decode("TIPO"),1,0,'C');
            $pdf::Cell(30,6,utf8_decode("FECHA"),1,0,'C');
            $pdf::Cell(40,6,utf8_decode("AREA"),1,0,'C');
            $pdf::Cell(40,6,utf8_decode("USUARIO"),1,0,'C');
            $pdf::Cell(60,6,utf8_decode("COMENTARIO"),1,0,'C');
            $pdf::Ln();

            foreach ($lista as $key => $value){                
                $pdf::SetFont('helvetica','',8);
                $pdf::Cell(20,5,utf8_decode("ENVIADO"),1,0,'C');
                $pdf::Cell(30,5,utf8_decode($value->fechaenvio),1,0,'L');
                $pdf::Cell(40,5,utf8_decode($value->areaenvio->nombre),1,0,'C');
                $pdf::Cell(40,5,utf8_decode($value->personaenvio->apellidopaterno." ".$value->personaenvio->apellidomaterno." ".$value->personaenvio->nombres),1,0,'C');
                $pdf::Cell(60,5,utf8_decode($value->comentario),1,0,'C');
                $pdf::Ln();
                if($value->fecharecepcion!=""){
                    if($value->situacion=="A"){
                        $pdf::Cell(20,5,utf8_decode("RECIBIDO"),1,0,'C');
                    }else{
                        $pdf::Cell(20,5,utf8_decode("RECHAZADO"),1,0,'C');
                    }
                    $pdf::Cell(30,5,utf8_decode($value->fecharecepcion),1,0,'L');
                    $pdf::Cell(40,5,utf8_decode($value->areadestino->nombre),1,0,'C');
                    $pdf::Cell(40,5,utf8_decode($value->personarecepcion->apellidopaterno." ".$value->personarecepcion->apellidomaterno." ".$value->personarecepcion->nombres),1,0,'C');
                    $pdf::Cell(60,5,"",1,0,'C');
                    $pdf::Ln();
                }
            }
            $pdf::Output('ListaCita.pdf');
        }
    }

    public function pdfHistoria(Request $request){
       
        $historia = Historia::find($request->id);
        if ($historia->departamento != 0) {
            $departamento = Departamento::find($historia->departamento);
            $provincia = Provincia::find($historia->provincia);
            $distrito = Distrito::find($historia->distrito);
        } else {
            $departamento = (object) array('nombre'=>'-');
            $provincia = (object) array('nombre'=>'-');
            $distrito = (object) array('nombre'=>'-');
        }
        $pdf = new TCPDF();
        $pdf::SetTitle('Historia');
        $pdf::AddPage();
        $pdf::Image("http://localhost:81/clinica/dist/img/logo2-ojos.jpg", 20, 7, 50, 15);
        $pdf::SetFont('helvetica','B',15);
        $pdf::Cell(60,10,"",0,0,'C');
        $pdf::Cell(75,10,"",0,0,'C');
        $pdf::SetFont('helvetica','B',18);
        $pdf::Cell(40,10,utf8_decode($historia->numero),1,0,'C');
        $pdf::Ln();
        $pdf::SetFont('helvetica','B',15);
        $pdf::Cell(60,10,strtoupper(""),0,0,'C');
        $pdf::Cell(70,10,"",0,0,'C');
        $pdf::Ln();
        $pdf::Cell(60,6,"",0,0,'C');
        $pdf::SetFont('helvetica','',14);
        $pdf::Cell(70,4,"----------------------------------------------------------",0,0,'C');
        $pdf::Ln();
        $pdf::SetFont('helvetica','B',14);
        $pdf::Cell(48,10,"",0,0,'C');
        $pdf::Cell(95,10,utf8_decode("HISTORIA CLINICA"),'B',0,'C');
        $pdf::Ln();
        $pdf::Ln();
        $pdf::Cell(40,8,"",0,0,'C');
        $pdf::SetFont('helvetica','B',9);
        $pdf::Cell(20,8,utf8_decode("FECHA: "),0,0,'L');
        $pdf::SetFont('helvetica','',9);
        $pdf::Cell(20,8,date("d/m/Y",strtotime($historia->fecha)),0,0,'C');
        $pdf::Cell(10,8,"",0,0,'C');
        $pdf::SetFont('helvetica','B',9);
        $pdf::Cell(20,8,utf8_decode("HORA: "),0,0,'C');
        $pdf::SetFont('helvetica','',9);
        $pdf::Cell(20,8,date("H:i:s",strtotime($historia->created_at)),0,0,'C');
        $pdf::Ln();
        $pdf::Cell(40,8,"",0,0,'C');
        $pdf::SetFont('helvetica','B',9);
        $pdf::Cell(20,8,utf8_decode("PACIENTE: "),0,0,'L');
        $pdf::SetFont('helvetica','',9);
        $pdf::Cell(60,8,($historia->persona->apellidopaterno." ".$historia->persona->apellidomaterno." ".$historia->persona->nombres),0,0,'L');
        $pdf::Ln();
        $pdf::Cell(40,8,"",0,0,'C');
        $pdf::SetFont('helvetica','B',9);
        $pdf::Cell(28,8,utf8_decode("TIPO PACIENTE: "),0,0,'L');
        $pdf::SetFont('helvetica','',9);
        $pdf::Cell(20,8,strtoupper($historia->tipopaciente),0,0,'L');
        $pdf::Ln();
        $pdf::Cell(40,8,"",0,0,'C');
        $pdf::SetFont('helvetica','B',9);
        $pdf::Cell(20,8,utf8_decode("DNI: "),0,0,'L');
        $pdf::SetFont('helvetica','',9);
        $pdf::Cell(20,8,utf8_decode($historia->persona->dni),0,0,'L');
        $pdf::Cell(10,8,"",0,0,'C');
        $pdf::SetFont('helvetica','B',9);
        $pdf::Cell(20,8,utf8_decode("SEXO: "),0,0,'C');
        $pdf::SetFont('helvetica','',9);
        $pdf::Cell(20,8,utf8_decode($historia->persona->sexo),0,0,'L');
        $pdf::Ln();
        $pdf::Cell(40,8,"",0,0,'C');
        $pdf::SetFont('helvetica','B',9);
        $pdf::Cell(20,8,utf8_decode("FECHA NAC:"),0,0,'L');
        $pdf::SetFont('helvetica','',9);
        $pdf::Cell(20,8,date("d/m/Y",strtotime($historia->persona->fechanacimiento)),0,0,'L');
        $pdf::Cell(10,8,"",0,0,'C');
        $pdf::SetFont('helvetica','B',9);
        $pdf::Cell(20,8,utf8_decode("EDAD: "),0,0,'C');
        $pdf::SetFont('helvetica','',9);
        if($historia->persona->fechanacimiento!=''){
            $pdf::Cell(20,8,date("Y-m-d") - date("Y-m-d",strtotime($historia->persona->fechanacimiento)),0,0,'L');
        }else{
            $pdf::Cell(20,8,'-',0,0,'L');
        }
        $pdf::Ln();
        $pdf::Cell(40,8,"",0,0,'C');
        $pdf::SetFont('helvetica','B',9);
        $pdf::Cell(20,8,utf8_decode("DOMICILIO:"),0,0,'L');
        $pdf::SetFont('helvetica','',9);
        $pdf::Cell(20,8,utf8_encode($historia->persona->direccion),0,0,'L');
        $pdf::Ln();
        $pdf::Cell(40,8,"",0,0,'C');
        $pdf::SetFont('helvetica','B',9);
        $pdf::Cell(29,8,utf8_decode("DEPARTAMENTO:"),0,0,'L');
        $pdf::SetFont('helvetica','',9);
        $pdf::Cell(20,8,utf8_decode($departamento->nombre),0,0,'L');
        $pdf::Cell(5,8,"",0,0,'C');
        $pdf::SetFont('helvetica','B',9);
        $pdf::Cell(20,8,utf8_decode("PROVINCIA:"),0,0,'L');
        $pdf::SetFont('helvetica','',9);
        if(!is_null($provincia)){
            $pdf::Cell(20,8,utf8_decode($provincia->nombre),0,0,'L');
        }else{
            $pdf::Cell(20,8,utf8_decode(""),0,0,'L');
        }
        $pdf::Cell(5,8,"",0,0,'C');
        $pdf::SetFont('helvetica','B',9);
        $pdf::Cell(16,8,utf8_decode("DISTRITO:"),0,0,'L');
        $pdf::SetFont('helvetica','',9);
        if(!is_null($distrito)){
            $pdf::Cell(20,8,utf8_decode($distrito->nombre),0,0,'L');
        }else{
            $pdf::Cell(20,8,utf8_decode(""),0,0,'L');   
        }
        $pdf::Ln();
        $pdf::Cell(40,8,"",0,0,'C');
        $pdf::SetFont('helvetica','B',9);
        $pdf::Cell(20,8,utf8_decode("CATEGORIA: "),0,0,'L');
        $pdf::SetFont('helvetica','',9);
        $pdf::Cell(25,8,utf8_decode($historia->categoria),0,0,'L');
        $pdf::Cell(8,8,"",0,0,'C');
        $pdf::SetFont('helvetica','B',9);
        $pdf::Cell(20,8,utf8_decode("DETALLE: "),0,0,'C');
        $pdf::SetFont('helvetica','',9);
        $pdf::Cell(20,8,utf8_decode($historia->detallecategoria),0,0,'L');
        $pdf::Ln();
        $pdf::Cell(40,8,"",0,0,'C');
        $pdf::SetFont('helvetica','B',9);
        $pdf::Cell(20,8,utf8_decode("TELEFONO: "),0,0,'L');
        $pdf::SetFont('helvetica','',9);
        $pdf::Cell(20,8,utf8_decode($historia->persona->telefono .' - '.$historia->persona->telefono2),0,0,'L');
        $pdf::Ln();
        $pdf::Cell(40,8,"",0,0,'C');
        $pdf::SetFont('helvetica','B',9);
        $pdf::Cell(28,8,utf8_decode("ESTADO CIVIL: "),0,0,'L');
        $pdf::SetFont('helvetica','',9);
        $pdf::Cell(20,8,utf8_decode($historia->estadocivil),0,0,'L');
        $pdf::Ln();
        $pdf::Cell(40,8,"",0,0,'C');
        $pdf::SetFont('helvetica','B',9);
        $pdf::Cell(20,8,utf8_decode("FAM. RESP: "),0,0,'L');
        $pdf::SetFont('helvetica','',9);
        $pdf::Cell(20,8,utf8_decode($historia->familiar),0,0,'L');
        $pdf::Ln();
        $pdf::Cell(40,8,"",0,0,'C');
        $pdf::SetFont('helvetica','B',9);
        $pdf::Cell(20,8,utf8_decode("ENV. POR: "),0,0,'L');
        $pdf::SetFont('helvetica','',9);
        $pdf::Cell(20,8,utf8_decode($historia->enviadopor),0,0,'L');
        $pdf::Ln();
        $pdf::Cell(40,8,"",0,0,'C');
        $pdf::SetFont('helvetica','B',9);
        $pdf::Cell(65,8,utf8_decode("OTROS DATOS DE LA HISTORIA:"),'B',0,'L');
        $pdf::Ln();
        $pdf::Cell(40,8,"",0,0,'C');
        $pdf::SetFont('helvetica','B',8);
        $pdf::Cell(20,8,utf8_decode("SOAT "),0,0,'L');
        $pdf::SetFont('helvetica','',8);
        $pdf::Cell(20,8,utf8_decode($historia->soat),0,0,'L');
        $pdf::Ln();
        $pdf::Cell(40,8,"",0,0,'C');
        $pdf::SetFont('helvetica','B',8);
        $pdf::Cell(20,8,utf8_decode("TITULAR: "),0,0,'L');
        $pdf::SetFont('helvetica','',8);
        $pdf::Cell(20,8,utf8_decode($historia->titular),0,0,'L');
        $pdf::Ln();
        if($historia->tipopaciente=='Convenio'){
            $pdf::Cell(40,8,"",0,0,'C');
            $pdf::SetFont('helvetica','B',8);
            $pdf::Cell(20,8,utf8_decode("EMPRESA: "),0,0,'L');
            $pdf::SetFont('helvetica','',8);
            if ($historia->convenio_id == NULL) {
                $pdf::Cell(20,8,utf8_decode($historia->empresa),0,0,'L');   
            } else {
                $pdf::Cell(20,8,utf8_decode($historia->convenio->nombre),0,0,'L');
                //.' - '.$historia->empresa
            }
            $pdf::Ln();
        }else{
            $pdf::Cell(40,8,"",0,0,'C');
            $pdf::SetFont('helvetica','B',8);
            $pdf::Cell(20,8,utf8_decode("EMPRESA: "),0,0,'L');
            $pdf::SetFont('helvetica','',8);
            $pdf::Cell(20,8,utf8_decode($historia->empresa),0,0,'L');
            $pdf::Ln();
        }
        $pdf::Cell(40,8,"",0,0,'C');
        $pdf::SetFont('helvetica','B',8);
        $pdf::Cell(20,8,utf8_decode("POLIZA: "),0,0,'L');
        $pdf::SetFont('helvetica','',8);
        $pdf::Cell(20,8,utf8_decode($historia->poliza),0,0,'L');
        $pdf::Ln();
        $pdf::Cell(40,8,"",0,0,'C');
        $pdf::SetFont('helvetica','B',8);
        $pdf::Cell(20,8,utf8_decode("CARNET: "),0,0,'L');
        $pdf::SetFont('helvetica','',8);
        $pdf::Cell(20,8,utf8_decode($historia->carnet),0,0,'L');
        $pdf::Ln();

        $pdf::Ln();
        $pdf::SetFont('helvetica','',8);
        $pdf::Cell(40,8,"",0,0,'C');
        $pdf::Cell(50,8,"IDENTIDAD MEDICINA - HISTORIAS CLINICA",0,0,'C');
        $pdf::Cell(40,8,"",0,0,'C');
        $pdf::Cell(15,8,"USUARIO:",0,0,'C');
        $pdf::SetFont('helvetica','',8);
        if($historia->usuario_id>0){
            $pdf::Cell(50,8,$historia->usuario->nombres,0,0,'L');
        }else{
            $pdf::Cell(50,8,"",0,0,'C');
        }
        $pdf::Output('Historia.pdf');
    }

    public function guardarfallecido(Request $request)
    {
        $error = DB::transaction(function() use($request){
            $Historia = Historia::find($request->input('historia_id'));
            $Historia->fechafallecido = $request->input('fecha');
            $Historia->fallecido='S';
            $Historia->save();
        });
        return is_null($error) ? "OK" : $error;
    }

    public function fallecido($id, $listarLuego)
    {
        $existe = Libreria::verificarExistencia($id, 'Historia');
        if ($existe !== true) {
            return $existe;
        }
        $listar = "NO";
        if (!is_null(Libreria::obtenerParametro($listarLuego))) {
            $listar = $listarLuego;
        }
        $modelo   = Historia::find($id);
        $entidad  = 'Historia';
        $paciente = $modelo->persona->apellidopaterno." ".$modelo->persona->apellidomaterno." ".$modelo->persona->nombres;
        $numero = $modelo->numero;
        $formData = array('route' => array('historia.guardarfallecido', $id), 'method' => 'Acept', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Fallecido';
        return view($this->folderview.'.fallecido')->with(compact('modelo', 'formData', 'entidad', 'boton', 'listar', 'numero', 'paciente'));
    }

    public function pdfHistoria2(Request $request){
        $citas = HistoriaClinica::where('historia_id', $request->input('id'))->get();
        $historia = Historia::find($request->id);
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf::SetTitle('Historial de Citas');
        // remove default header/footer
        $pdf::setPrintHeader(false);
        $pdf::setPrintFooter(false);

        // set default monospaced font
        $pdf::SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        // set margins
        $pdf::SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);

        // set auto page breaks
        $pdf::SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

        // set image scale factor
        $pdf::setImageScale(PDF_IMAGE_SCALE_RATIO);

        $pdf::AddPage();
        $pdf::Image("http://localhost:81/clinica/dist/img/logo2-ojos.jpg", 20, 26, 50, 15);
        $pdf::SetFont('helvetica','B',15);
        $pdf::Cell(60,10,"",0,0,'C');
        $pdf::Cell(75,10,"",0,0,'C');
        $pdf::SetFont('helvetica','B',10);
        $pdf::Cell(40,10,utf8_decode('Historia '.$historia->numero),1,0,'C');
        $pdf::Ln();
        $pdf::SetFont('helvetica','B',15);
        $pdf::Cell(60,10,strtoupper(""),0,0,'C');
        $pdf::Cell(70,10,"",0,0,'C');
        $pdf::Cell(60,6,"",0,0,'C');

        $o = 0;
        foreach ($citas as $cita) {            

            //Solo los antecedentes anteriores del paciente :v

            if($o == 0) {

                $pdf::Ln(4);
                $pdf::SetFont('helvetica','B',14);
                $pdf::Cell(48,10,"",0,0,'C');
                $pdf::Cell(95,10,'ANTECEDENTES ANTERIORES','B',0,'C');
                $pdf::Ln(4);
                $pdf::Ln(4);
                $pdf::Ln(4);
                $pdf::Ln(4);
                $pdf::Cell(8,8,"",0,0,'C');
                $pdf::SetFont('helvetica','B',9);
                $pdf::Cell(35,8,utf8_decode("PACIENTE: "),0,0,'L');
                $pdf::SetFont('helvetica','',9);
                $pdf::Multicell(120,8,($historia->persona->apellidopaterno." ".$historia->persona->apellidomaterno." ".$historia->persona->nombres),0,'L');

                $pdf::Ln(2);
                $pdf::Ln(3);
                $pdf::Ln(3);
                $pdf::SetFont('helvetica','',9);
                $pdf::Cell(8,8,"",0,0,'C');
                $pdf::Multicell(165,8,($historia->antecedentes2),0,'L');

                $pdf::Ln(2);
                $pdf::Ln(3);
                $pdf::Ln(3);

            }

            //

            $pdf::Ln(4);
            $pdf::SetFont('helvetica','B',14);
            $pdf::Cell(48,10,"",0,0,'C');
            $pdf::Cell(95,10,'Cita N° ' . $cita->numero . ' / ' . date('d-m-Y',strtotime($cita->fecha_atencion)),'B',0,'C');
            $pdf::Ln(4);
            $pdf::Ln(4);
            $pdf::Ln(4);
            $pdf::Ln(4);
            $pdf::Cell(8,8,"",0,0,'C');
            $pdf::SetFont('helvetica','B',9);
            $pdf::Cell(35,8,utf8_decode("PACIENTE: "),0,0,'L');
            $pdf::SetFont('helvetica','',9);
            $pdf::Multicell(120,8,($historia->persona->apellidopaterno." ".$historia->persona->apellidomaterno." ".$historia->persona->nombres),0,'L');

            $pdf::Ln(2);
            $pdf::Ln(3);
            $pdf::Ln(3);

            $pdf::Cell(8,8,"",0,0,'C');
            $pdf::SetFont('helvetica','B',9);
            $pdf::Cell(35,8,utf8_decode("CIE 10: "),0,0,'L');
            $pdf::SetFont('helvetica','',9);

            $cadenacies = '';

            if($cita->cie_id != 0 || $cita->cie_id != '') {
                $cadenacies .= $cita->cie->codigo . ' ' . $cita->cie->descripcion . '<br>';
            }

            $cies = Detallehistoriacie::where('historiaclinica_id', $cita->id)->whereNull('deleted_at')->get();

            if(count($cies) != 0){
                foreach ($cies as $value) {
                    $cadenacies .= $value->cie->codigo . ' ' . $value->cie->descripcion .'<br>';
                }
            }
            $cies2 = explode('<BR>', strtoupper($cadenacies));
            if($cies2[0] == ''){
                $cies2[] = '-';
            }
            $i = 0;
            foreach($cies2 as $c) {
                if($c != '') {
                    if($i != 0) {
                        $pdf::Cell(8,8,"",0,0,'C');
                        $pdf::Cell(35,8,"",0,0,'L');
                    }
                    $pdf::Multicell(120,8,$c==''?'-':$c,0,'L');
                    $pdf::Ln(3);
                    $i++;
                }                    
            }

            $pdf::Ln(3);
            $pdf::Ln(3);

            $pdf::Cell(8,8,"",0,0,'C');
            $pdf::SetFont('helvetica','B',9);
            $pdf::Cell(35,8,utf8_decode("MOTIVO: "),0,0,'L');
            $pdf::SetFont('helvetica','',9);
            
            $mot = explode('<BR>', strtoupper($cita->motivo));
            if($mot[0] == ''){
                $mot[] = '-';
            }
            $i = 0;
            foreach($mot as $m) {
                if($m != '') {
                    if($i != 0) {
                        $pdf::Cell(8,8,"",0,0,'C');
                        $pdf::Cell(35,8,"",0,0,'L');
                    }
                    $pdf::Multicell(120,8,$m==''?'-':$m,0,'L');
                    $pdf::Ln(3);
                    $i++;
                }                    
            } 
            $pdf::Ln(3);
            $pdf::Ln(3);

            $pdf::Cell(8,8,"",0,0,'C');
            $pdf::SetFont('helvetica','B',9);
            $pdf::Cell(35,8,"DIAGNÓSTICO: ",0,0,'L');
            $pdf::SetFont('helvetica','',9);

            $diag = explode('<BR>', strtoupper($cita->diagnostico));
            if($diag[0] == ''){
                $diag[] = '-';
            }
            $i = 0;
            foreach($diag as $d) {
                if($d != '') {
                    if($i != 0) {
                        $pdf::Cell(8,8,"",0,0,'C');
                        $pdf::Cell(35,8,"",0,0,'L');
                    }
                    $pdf::Multicell(120,8,$d,0,'L');
                    $pdf::Ln(3);
                    $i++;
                }
            } 
            $pdf::Ln(3);
            $pdf::Ln(3);

            $pdf::Cell(8,8,"",0,0,'C');
            $pdf::SetFont('helvetica','B',9);
            $pdf::Cell(35,8,"TRATAMIENTO: ",0,0,'L');
            $pdf::SetFont('helvetica','',9);

            $trat = explode('<BR>', strtoupper($cita->tratamiento));
            if($trat[0] == ''){
                $trat[] = '-';
            }
            $i = 0;
            foreach($trat as $t) {
                if($t != '') {
                    if($i != 0) {
                        $pdf::Cell(8,8,"",0,0,'C');
                        $pdf::Cell(35,8,"",0,0,'L');
                    }
                    $pdf::Multicell(120,8,$t,0,'L');
                    $pdf::Ln(3);
                    $i++;
                }
            } 
            $pdf::Ln(3);
            $pdf::Ln(3);

            $pdf::Cell(8,8,"",0,0,'C');
            $pdf::SetFont('helvetica','B',9);
            $pdf::Cell(35,8,"EXÁMENES: ",0,0,'L');
            $pdf::SetFont('helvetica','',9);

            $cadenaexamenes = '';

            $examenes = Examenhistoriaclinica::where('historiaclinica_id', $cita->id)->whereNull('deleted_at')->get();

            if(count($examenes) != 0){
                foreach ($examenes as $value) {
                    $cadenaexamenes .= $value->servicio->nombre .'<br>';
                }
            }  

            $examenes2 = explode('<BR>', strtoupper($cadenaexamenes));

            if($examenes2[0] == ''){
                $examenes2[] = '-';
            }

            $i = 0;
            foreach($examenes2 as $e) {
                if($e != '') {
                    if($i != 0) {
                        $pdf::Cell(8,8,"",0,0,'C');
                        $pdf::Cell(35,8,"",0,0,'L');
                    }
                    $pdf::Multicell(120,8,$e,0,'L');
                    $pdf::Ln(3);
                    $i++;
                }
            } 
            $pdf::Ln(3);
            $pdf::Ln(3);

            $pdf::Cell(8,8,"",0,0,'C');
            $pdf::SetFont('helvetica','B',9);
            $pdf::Cell(35,8,"EXPLOR. FÍSICA: ",0,0,'L');
            $pdf::SetFont('helvetica','',9);

            $exf = explode('<BR>', strtoupper($cita->exploracion_fisica));

            if($exf[0] == ''){
                $exf[] = '-';
            }

            $i = 0;
            foreach($exf as $ef) {
                if($ef != '') {
                    if($i != 0) {
                        $pdf::Cell(8,8,"",0,0,'C');
                        $pdf::Cell(35,8,"",0,0,'L');
                    }
                    $pdf::Multicell(120,8,$ef,0,'L');
                    $pdf::Ln(3);
                    $i++;
                }
            } 
            $pdf::Ln(3);
            $pdf::Ln(3);
            $pdf::Cell(8,8,"",0,0,'C');
            $pdf::SetFont('helvetica','B',9);
            $pdf::Cell(35,8,"COMENTARIO: ",0,0,'L');
            $pdf::SetFont('helvetica','',9);
            $pdf::Multicell(120,8,$cita->comentario== null ?'-':$cita->comentario,0,'L');
            $o++;
        }
        $pdf::Output('HistorialCitas.pdf');
    }

    public function unirHistorias(Request $request) {
        /*$historias = Person::select('h1.id as i1', 'h2.id as i2', 'h1.numero as n1', 'h1.sucursal_id as s1', 'h2.numero as n2', 'h2.sucursal_id as s2', 'h1.person_id as p1', 'h2.person_id as p2', 'person.id')
                            ->leftjoin('historia as h1', 'h1.person_id', '=', 'person.id')
                            ->leftjoin('historia as h2', 'h2.person_id', '=', 'person.id')                            
                            ->where('h1.sucursal_id', '=', 1)
                            ->where('h2.sucursal_id', '=', 2)
                            ->whereRaw('h1.person_id = h2.person_id') 
                            ->orderBy('h1.numero')
                            ->orderBy('h2.numero')
                            ->get();*/

        /*select `h1`.`numero` as `n1`, `h1`.`sucursal_id` as `s1`, `h2`.`numero` as `n2`, `h2`.`sucursal_id` as `s2`, `h1`.`person_id` as `p1`, `h2`.`person_id` as `p2`, `person`.`id` 
        from `person` 
        inner join `historia` as `h1` on `h1`.`person_id` = `person`.`id` 
        inner join `historia` as `h2` on `h2`.`person_id` = `person`.`id` 
        where `person`.`deleted_at` is null 
        and `h1`.`person_id` = h2.person_id 
        and h1.sucursal_id = 1 
        and h2.sucursal_id = 2 
        order by `h1`.`numero` asc, `h2`.`numero` asc*/

        /*$mensaje = '';

        foreach ($historias as $value) {
            //Eliminar la Historia con sucursal_id = 1, rescatar su numero de historia

            //Pasar ese id a id_alternativo y buscar historiasclinicas, actualizar la referencia con el nuevo id
        }

        echo $mensaje;*/

        $personas = Person::select('id')->get();
        foreach ($personas as $value) {
            $historia1 = Historia::where('person_id', '=', $value->id)->where('sucursal_id', '=', 1)->first(); 
            $historia2 = Historia::where('person_id', '=', $value->id)->where('sucursal_id', '=', 2)->first(); 
            //Solo si hay dos historias (ojos y esp)
            if($historia1 !== NULL && $historia2 !== NULL) {
                //busco las historiasclinicas que tienen id de historia de ojos
                $historiasclinicas = HistoriaClinica::where('historia_id', '=', $historia1->id)->get();
                if(count($historiasclinicas) > 0) {
                    foreach ($historiasclinicas as $hc) {
                        //Actualizo la historia_id a la de la sucursal 2 (esp)
                        $hc->historia_id = $historia2->id;
                        $hc->save();
                    }
                }
                //busco las citas que tienen id de historia de ojos
                $citas = Cita::where('historia_id', '=', $historia1->id)->get();
                if(count($citas) > 0) {
                    foreach ($citas as $cita) {
                        //Actualizo la historia_id a la de la sucursal 2 (esp)
                        $cita->historia_id = $historia2->id;
                        $cita->save();
                    }
                }
                //Elimino sucursal en Historia2
                //$historia2->sucursal_id=null;
                //$historia2->save();
                //Elimino historia con sucursal 1
                $historia1->delete();
            }
        }
        //Reestructurar números de historia
        $historias = Historia::select('historia.id')->orderBy(DB::raw('CONCAT(apellidopaterno, " ", apellidomaterno, " ", nombres)'), 'ASC')
                    ->join('person as p', 'p.id', '=', 'historia.person_id')
                    ->get();
        $i = 1;
        foreach ($historias as $history) {
            $historia = Historia::find($history->id);
            $numero2 = $historia->numero;
            $numero1 = str_pad($i,8,'0',STR_PAD_LEFT);
            if($historia->sucursal_id == 1) {
                $historia->numero2 = NULL;
            } else {
                $historia->numero2 = $numero2;
            }
            $historia->sucursal_id = NULL;
            $historia->numero = $numero1;       
            $historia->save();
            echo $historia->sucursal_id;
            $i++;
        }
    }

    //Eliminar Historias Clinicas con número = 0

    public function unirHistorias2(Request $request) {
        $historiasclinicas = HistoriaClinica::where('numero', '=', '0')->get();
        foreach ($historiasclinicas as $hc) {
            $hc->delete();
        }
        echo count($historiasclinicas) . ' HISTORIAS CLÍNICAS ELIMINADAS';
    }
}
