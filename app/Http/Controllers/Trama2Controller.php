<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Validator;
use App\Http\Requests;
use App\HistoriaClinica;
use App\Historia;
use App\Librerias\Libreria;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class Trama2Controller extends Controller
{

    protected $folderview      = 'app.trama2';
    protected $tituloAdmin     = 'Reportes SETIPRESS';
    protected $rutas           = array(
            'index'  => 'area.index',
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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //return view($this->folderview.'.tramas')->with(compact('entidad', 'title', 'titulo_registrar', 'ruta'));
        return view($this->folderview.'.tramas');
    }
}
