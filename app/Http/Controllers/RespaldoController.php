<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Validator;
use App\Http\Requests;
use App\Person;
use App\Respaldo;
use App\Librerias\Libreria;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class RespaldoController extends Controller
{

    protected $folderview      = 'app.respaldo';
    protected $tituloAdmin     = 'Respaldo';
    protected $tituloEliminar  = 'Eliminar respaldo';
    protected $rutas           = array('create' => 'respaldo.create', 
            'importarArchivo' => 'respaldo.importarArchivo',
            'delete' => 'respaldo.eliminar',
            'search' => 'respaldo.buscar',
            'index'  => 'respaldo.index',
        );

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function buscar(Request $request)
    {
        $pagina           = $request->input('page');
        $filas            = $request->input('filas');
        $entidad          = 'Respaldo';
        $nombre           = Libreria::getParam($request->input('nombre'));
        $fecha            = Libreria::getParam($request->input('fecha'));
        $resultado        = Respaldo::where('nombrearchivo', 'LIKE', '%'.strtoupper($nombre).'%')->where('fecha', 'LIKE', '%'.$fecha.'%')->orderBy('numero', 'ASC');
        $usertype         = Auth::user()->usertype_id;

        $filtro           = '';
        if($usertype == 1 || $usertype == 2) {
            $filtro       = $request->input('filtro');
        } 
        if($usertype == 28) {
            $filtro       = 'DESCARGADO';
        }
        if($usertype == 29) {
            $filtro       = 'SUBIDO';
        }

        $resultado        = $resultado->where('estado', 'LIKE', '%'.strtoupper($filtro).'%');

        $lista            = $resultado->get();
        $cabecera         = array();
        $cabecera[]       = array('valor' => 'Número', 'numero' => '1');
        $cabecera[]       = array('valor' => 'NombreArchivo', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Estado', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Fecha', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Responsable', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Operaciones', 'numero' => '2');
        
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
            return view($this->folderview.'.list')->with(compact('lista', 'paginacion', 'inicio', 'fin', 'entidad', 'cabecera', 'titulo_eliminar', 'ruta'));
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
        $entidad          = 'Respaldo';
        $title            = $this->tituloAdmin;
        $ruta             = $this->rutas;
        return view($this->folderview.'.respaldo')->with(compact('entidad', 'title', 'ruta'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $error = DB::transaction(function() use($request){
            $user = Auth::user();
            $respaldo       = new Respaldo();
            $respaldo->nombrearchivo = strtoupper($request->input('nombrearchivo'));
            $respaldo->numero = Respaldo::numeroSigue();
            $respaldo->estado = 'IMPORTADO';
            $respaldo->responsable_id = $user->person->id;
            $respaldo->fecha = date('Y-m-d');
            $respaldo->save();
        });
        return is_null($error) ? "OK" : $error;
    }

    public function destroy($id)
    {
        $existe = Libreria::verificarExistencia($id, 'respaldo');
        if ($existe !== true) {
            return $existe;
        }
        $error = DB::transaction(function() use($id){
            $area = Respaldo::find($id);
            $area->delete();
        });
        return is_null($error) ? "OK" : $error;
    }

    public function eliminar($id, $listarLuego)
    {
        $existe = Libreria::verificarExistencia($id, 'area');
        if ($existe !== true) {
            return $existe;
        }
        $listar = "NO";
        if (!is_null(Libreria::obtenerParametro($listarLuego))) {
            $listar = $listarLuego;
        }
        $modelo   = Area::find($id);
        $entidad  = 'Area';
        $formData = array('route' => array('area.destroy', $id), 'method' => 'DELETE', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Eliminar';
        return view('app.confirmarEliminar')->with(compact('modelo', 'formData', 'entidad', 'boton', 'listar'));
    }

    public function importarArchivo(Request $request)
    { 
        date_default_timezone_set('America/Lima');
        //obtenemos el campo file definido en el formulario
        $file = $request->file('nombrearchivo');
 
        //obtenemos el nombre del archivo
        $nombre = $file->getClientOriginalName();
 
        //indicamos que queremos guardar un nuevo archivo en el disco local
        \Storage::disk('local')->put($nombre,  \File::get($file));

        $respaldo = Respaldo::where('nombrearchivo', '=', $nombre)->where('estado', '=', 'SUBIDO')->first();

        if($respaldo == NULL) {

            $person = Auth::user()->person;

            $respaldo = new Respaldo();
            $respaldo->numero = Respaldo::numeroSigue('SUBIDO');
            $respaldo->responsable_id = $person->id;
            $respaldo->nombrearchivo = $nombre;
            $respaldo->fecha = date('Y-m-d');
            $respaldo->estado = 'SUBIDO';
            $respaldo->save();

            echo "Archivo: $nombre, Guardado Correctamente.";

        } else {
            echo "Archivo: $nombre, ya se encuentra en la base de datos. El archivo fue actualizado.";
        }
 
       
    }
}
