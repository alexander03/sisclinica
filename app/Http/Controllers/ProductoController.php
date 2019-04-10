<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Validator;
use App\Http\Requests;
use App\Producto;
use App\Kardex;
use App\Cie;
use App\Categoria;
use App\Especialidadfarmacia;
use App\Productoprincipio;
use App\Principioactivo;
use App\Presentacion;
use App\Origen;
use App\Librerias\Libreria;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Jenssegers\Date\Date;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

ini_set('memory_limit', '512M'); //Raise to 512 MB
ini_set('max_execution_time', '60000'); //Raise to 512 MB 

class ProductoController extends Controller
{
    protected $folderview      = 'app.producto';
    protected $tituloAdmin     = 'Productos';
    protected $tituloRegistrar = 'Registrar producto';
    protected $tituloModificar = 'Modificar producto';
    protected $tituloEliminar  = 'Eliminar producto';
    protected $rutas           = array('create' => 'producto.create', 
            'edit'   => 'producto.edit', 
            'delete' => 'producto.eliminar',
            'search' => 'producto.buscar',
            'index'  => 'producto.index',
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
     * Mostrar el resultado de búsquedas
     * 
     * @return Response 
     */
    public function buscar(Request $request)
    {
        $pagina           = $request->input('page');
        $filas            = $request->input('filas');
        $entidad          = 'Producto';
        $nombre             = Libreria::getParam($request->input('nombre'));
        $principioactivo             = Libreria::getParam($request->input('principioactivo'));
        $resultado        = Producto::where('nombre', 'LIKE', '%'.strtoupper($nombre).'%')->where(function ($query) use($request){
                        if ($request->input('tipo') !== null && $request->input('tipo') !== '') {
                            $query->where('tipo', '=', $request->input('tipo'));
                        }
                        if ($request->input('categoria_id2') !== null && $request->input('categoria_id2') !== '') {
                            $query->where('categoria_id', '=', $request->input('categoria_id2'));
                        }
                        if ($request->input('especialidadfarmacia_id2') !== null && $request->input('especialidadfarmacia_id2') !== '') {
                            $query->where('especialidadfarmacia_id', '=', $request->input('especialidadfarmacia_id2'));
                        }
                        if ($request->input('presentacion_id2') !== null && $request->input('presentacion_id2') !== '') {
                            $query->where('presentacion_id', '=', $request->input('presentacion_id2'));
                        }
                        if ($request->input('origen_id2') !== null && $request->input('origen_id2') !== '') {
                            $query->where('origen_id', '=', $request->input('origen_id2'));
                        }
                        if($request->input('tipo') !== null && $request->input('tipo') !== ''){
                            $query->where('tipo','like',$request->input('tipo'));
                        }
                    })->orderBy('nombre', 'ASC');
        $lista            = $resultado->get();
        $cabecera         = array();
        $cabecera[]       = array('valor' => '#', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Nombre', 'numero' => '1');
        if($request->input('tipo')=="F"){
            $cabecera[]       = array('valor' => 'Principio Activo', 'numero' => '1');
        }
        $cabecera[]       = array('valor' => 'Clasificacion', 'numero' => '1');
        if($request->input('tipo')=="F"){
            $cabecera[]       = array('valor' => 'Laboratorio', 'numero' => '1');
        }
        $cabecera[]       = array('valor' => 'Presentacion', 'numero' => '1');
        if($request->input('tipo')=="F"){
            $cabecera[]       = array('valor' => 'Especialidad', 'numero' => '1');
        }
        $cabecera[]       = array('valor' => 'Proveedor', 'numero' => '1');
        if($request->input('tipo')=="F"){
            $cabecera[]       = array('valor' => 'Origen', 'numero' => '1');
        }
        $cabecera[]       = array('valor' => 'Anaquel', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Precio venta', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Precio Compra', 'numero' => '1');
        if($request->input('tipo')=="F"){
            $cabecera[]       = array('valor' => 'Precio Kayros', 'numero' => '1');
        }
        $cabecera[]       = array('valor' => 'Afecto', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Operaciones', 'numero' => '2');
        
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
            return view($this->folderview.'.list')->with(compact('lista', 'paginacion', 'inicio', 'fin', 'entidad', 'cabecera', 'titulo_modificar', 'titulo_eliminar', 'ruta','principioactivo', 'user'));
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
        $entidad          = 'Producto';
        $title            = $this->tituloAdmin;
        $titulo_registrar = $this->tituloRegistrar;
        $ruta             = $this->rutas;
        $cboCategoria = array('' => 'Todos') + Categoria::lists('nombre', 'id')->all(); 
        $cboEspecialidad = array('' => 'Todos') + Especialidadfarmacia::lists('nombre', 'id')->all();
        $cboPresentacion = array('' => 'Todos') + Presentacion::lists('nombre', 'id')->all(); 
        $cboOrigen = array('' => 'Todos') + Origen::lists('nombre', 'id')->all(); 
        $cboTipo = array('F' => 'Farmacia', 'O' => 'Otros');
        return view($this->folderview.'.admin')->with(compact('entidad', 'title', 'titulo_registrar', 'ruta', 'cboCategoria','cboEspecialidad','cboPresentacion', 'cboOrigen', 'cboTipo'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexbuscarproducto()
    {
        $entidad          = 'Producto';
        $title            = $this->tituloAdmin;
        $titulo_registrar = $this->tituloRegistrar;
        $ruta             = $this->rutas;
        $cboCategoria = array('' => 'Todos') + Categoria::lists('nombre', 'id')->all(); 
        $cboEspecialidad = array('' => 'Todos') + Especialidadfarmacia::lists('nombre', 'id')->all();
        $cboPresentacion = array('' => 'Todos') + Presentacion::lists('nombre', 'id')->all(); 
        $cboOrigen = array('' => 'Todos') + Origen::lists('nombre', 'id')->all(); 
        return view($this->folderview.'.adminProducto')->with(compact('entidad', 'title', 'titulo_registrar', 'ruta', 'cboCategoria','cboEspecialidad','cboPresentacion', 'cboOrigen'));
    }


    /**
     * Mostrar el resultado de búsquedas
     * 
     * @return Response 
     */
    public function buscarproducto(Request $request)
    {
        $pagina           = $request->input('page');
        $filas            = $request->input('filas');
        $entidad          = 'Producto';
        $nombre             = Libreria::getParam($request->input('nombre'));
        $principioactivo             = Libreria::getParam($request->input('principioactivo'));
        $resultado        = Producto::where('nombre', 'LIKE', '%'.strtoupper($nombre).'%')->where(function ($query) use($request){
                        if ($request->input('tipo') !== null && $request->input('tipo') !== '') {
                            $query->where('tipo', '=', $request->input('tipo'));
                        }
                        if ($request->input('categoria_id2') !== null && $request->input('categoria_id2') !== '') {
                            $query->where('categoria_id', '=', $request->input('categoria_id2'));
                        }
                        if ($request->input('especialidadfarmacia_id2') !== null && $request->input('especialidadfarmacia_id2') !== '') {
                            $query->where('especialidadfarmacia_id', '=', $request->input('especialidadfarmacia_id2'));
                        }
                        if ($request->input('presentacion_id2') !== null && $request->input('presentacion_id2') !== '') {
                            $query->where('presentacion_id', '=', $request->input('presentacion_id2'));
                        }
                        if ($request->input('origen_id2') !== null && $request->input('origen_id2') !== '') {
                            $query->where('origen_id', '=', $request->input('origen_id2'));
                        }
                    })->orderBy('nombre', 'ASC');
        $lista            = $resultado->get();
        $cabecera         = array();
        $cabecera[]       = array('valor' => '#', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Nombre', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Principio Activo', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Clasificacion', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Laboratorio', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Presentacion', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Especialidad', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Proveedor', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Origen', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Precio venta', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Afecto', 'numero' => '1');
        
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
            return view($this->folderview.'.listProducto')->with(compact('lista', 'paginacion', 'inicio', 'fin', 'entidad', 'cabecera', 'titulo_modificar', 'titulo_eliminar', 'ruta','principioactivo'));
        }
        return view($this->folderview.'.listProducto')->with(compact('lista', 'entidad'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
     public function create(Request $request)
    {
        $listar   = Libreria::getParam($request->input('listar'), 'NO');
        $entidad  = 'Producto';
        $producto = null;
        $cboAfecto          = array("SI" => "SI", "NO" => "NO");
        $cboLote          = array("SI" => "SI", "NO" => "NO");
        $formData = array('producto.store');
        $formData = array('route' => $formData, 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Registrar'; 
        $cboTipo = array('F' => 'Farmacia', 'O' => 'Otros');
        $request->session()->forget('carritoprincipio');
        return view($this->folderview.'.mant')->with(compact('producto', 'formData', 'entidad', 'boton', 'listar','cboAfecto','cboTipo','cboLote'));
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
                'nombre'               => 'required|max:100',
                'preciocompra'         => 'required',
                'precioventa'          => 'required',
                'fraccion'             => 'required',
                'presentacion_id'      => 'required'
                );
        $mensajes = array(
            'nombre.required'          => 'Debe ingresar un nombre',
            'preciocompra.required'    => 'Debe ingresar precio de compra',
            'precioventa.required'     => 'Debe ingresar precio de venta',
            'fraccion.required'        => 'Debe ingresar una fracción',
            'presentacion_id.required' => 'Debe ingresar una presentación'
            );
        $validacion = Validator::make($request->all(), $reglas, $mensajes);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        }
        $error = DB::transaction(function() use($request){
            $producto       = new Producto();
            $producto->tipo = $request->input('tipo');
            $producto->lote = $request->input('lote');
            $producto->nombre = strtoupper($request->input('nombre'));
            $producto->codigobarra       = $request->input('codigobarra');
            $producto->afecto       = $request->input('afecto');
            $producto->codigo_producto    = $request->input('codigo_producto');
            $producto->registro_sanitario = $request->input('registro_sanitario');
            $producto->precioxcaja    = str_replace(',', '', $request->input('precioxcaja'));
            $producto->preciocompra   = str_replace(',', '', $request->input('preciocompra'));
            $producto->precioventa    = str_replace(',', '', $request->input('precioventa'));
            $producto->preciokayros   = Libreria::obtenerParametro(str_replace(',', '', $request->input('preciokayros'))); 
            $producto->stockseguridad   = Libreria::obtenerParametro(str_replace(',', '', $request->input('stockseguridad'))); 
            $producto->categoria_id = Libreria::obtenerParametro($request->input('categoria_id'));
            $producto->laboratorio_id = Libreria::obtenerParametro($request->input('laboratorio_id'));
            $producto->presentacion_id = Libreria::obtenerParametro($request->input('presentacion_id'));
            $producto->fraccion = Libreria::obtenerParametro(str_replace(',', '', $request->input('fraccion'))); 
            $producto->especialidadfarmacia_id = Libreria::obtenerParametro($request->input('especialidadfarmacia_id'));
            $producto->proveedor_id = Libreria::obtenerParametro($request->input('proveedor_id'));
            $producto->origen_id = Libreria::obtenerParametro($request->input('origen_id'));
            $producto->anaquel_id = Libreria::obtenerParametro($request->input('anaquel_id'));
            $producto->save();

            $lista = $request->session()->get('carritoprincipio');
            for ($i=0; $i < count($lista); $i++) { 
                $productoprincipio = new Productoprincipio();
                $productoprincipio->producto_id = $producto->id;
                $productoprincipio->principioactivo_id = $lista[$i]['principioactivo_id'];
                $productoprincipio->save();
            }

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
        $existe = Libreria::verificarExistencia($id, 'producto');
        if ($existe !== true) {
            return $existe;
        }
        $listar              = Libreria::getParam($request->input('listar'), 'NO');
        $producto = Producto::find($id);
        $entidad             = 'Producto';
        $cboAfecto          = array("SI" => "SI", "NO" => "NO");
        $cboLote          = array("SI" => "SI", "NO" => "NO");
        $cboTipo = array('F' => 'Farmacia', 'O' => 'Otros');
        $formData            = array('producto.update', $id);
        $formData            = array('route' => $formData, 'method' => 'PUT', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton               = 'Modificar';
        $request->session()->forget('carritoprincipio');
        $lista = array();
        $listado = Productoprincipio::where('producto_id','=',$producto->id)->get();
        foreach ($listado as $key2 => $value2) {
            $lista[]  = array('principioactivo_id' => $value2->principioactivo_id, 'nombre' => $value2->principioactivo->nombre);
        }
        $request->session()->put('carritoprincipio', $lista);
        return view($this->folderview.'.mant')->with(compact('producto', 'formData', 'entidad', 'boton', 'listar','cboAfecto','cboTipo','cboLote'));
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
        $existe = Libreria::verificarExistencia($id, 'producto');
        if ($existe !== true) {
            return $existe;
        }
        $reglas     = array(
                'nombre'                  => 'required|max:100',
                'preciocompra'                 => 'required',
                'precioventa'                 => 'required'
                );
        $mensajes = array(
            'nombre.required'         => 'Debe ingresar un nombre',
            'preciocompra.required'         => 'Debe ingresar precio de compra',
            'precioventa.required'         => 'Debe ingresar precio de venta'
            );
        $validacion = Validator::make($request->all(), $reglas, $mensajes);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        }
        $error = DB::transaction(function() use($request, $id){
            $producto                        = Producto::find($id);
            $producto->tipo = $request->input('tipo');
            $producto->lote = $request->input('lote');
            $producto->nombre = strtoupper($request->input('nombre'));
            $producto->codigobarra       = $request->input('codigobarra');
            $producto->afecto       = $request->input('afecto');
            $producto->codigo_producto    = $request->input('codigo_producto');
            $producto->registro_sanitario = $request->input('registro_sanitario');
            $producto->precioxcaja    = str_replace(',', '', $request->input('precioxcaja'));
            $producto->preciocompra   = str_replace(',', '', $request->input('preciocompra'));
            $producto->precioventa    = str_replace(',', '', $request->input('precioventa'));
            $producto->preciokayros   = Libreria::obtenerParametro(str_replace(',', '', $request->input('preciokayros'))); 
            $producto->stockseguridad   = Libreria::obtenerParametro(str_replace(',', '', $request->input('stockseguridad'))); 
            $producto->categoria_id = Libreria::obtenerParametro($request->input('categoria_id'));
            $producto->laboratorio_id = Libreria::obtenerParametro($request->input('laboratorio_id'));
            $producto->presentacion_id = Libreria::obtenerParametro($request->input('presentacion_id'));
            $producto->especialidadfarmacia_id = Libreria::obtenerParametro($request->input('especialidadfarmacia_id'));
            $producto->proveedor_id = Libreria::obtenerParametro($request->input('proveedor_id'));
            $producto->origen_id = Libreria::obtenerParametro($request->input('origen_id'));
            $producto->anaquel_id = Libreria::obtenerParametro($request->input('anaquel_id'));
            $producto->save();

            $lista = $request->session()->get('carritoprincipio');
            $listado = Productoprincipio::where('producto_id','=',$producto->id)->get();
            foreach ($listado as $key2 => $value2) {
                $band=false;
                for ($i=0; $i < count($lista); $i++) {
                    if($lista[$i]['principioactivo_id']==$value2->principioactivo_id){
                        $bnd=true;
                    }
                }    
                if(!$band){
                    $value2->delete();
                }
            }
            
            for ($i=0; $i < count($lista); $i++) {
                $principiocomprobacion = Productoprincipio::where('principioactivo_id','=',$lista[$i]['principioactivo_id'])->where('producto_id','=',$producto->id)->first();
                if ($principiocomprobacion === null) {
                    $productoprincipio = new Productoprincipio();
                    $productoprincipio->producto_id = $producto->id;
                    $productoprincipio->principioactivo_id = $lista[$i]['principioactivo_id'];
                    $productoprincipio->save();
                } 
                
            }
        });
        return is_null($error) ? "OK" : $error;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $existe = Libreria::verificarExistencia($id, 'producto');
        if ($existe !== true) {
            return $existe;
        }
        $error = DB::transaction(function() use($id){
            $producto = Producto::find($id);
            $producto->delete();
        });
        return is_null($error) ? "OK" : $error;
    }

    public function eliminar($id, $listarLuego)
    {
        $existe = Libreria::verificarExistencia($id, 'producto');
        if ($existe !== true) {
            return $existe;
        }
        $listar = "NO";
        if (!is_null(Libreria::obtenerParametro($listarLuego))) {
            $listar = $listarLuego;
        }
        $modelo   = Producto::find($id);
        $entidad  = 'Producto';
        $formData = array('route' => array('producto.destroy', $id), 'method' => 'DELETE', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Eliminar';
        return view('app.confirmarEliminar')->with(compact('modelo', 'formData', 'entidad', 'boton', 'listar'));
    }


    public function cambiarOrigen(Request $request)
    {
        $error = DB::transaction(function() use($request){
            $producto = Producto::find($request->input('id'));
            $producto->origen_id = Libreria::obtenerParametro($request->input('origen_id'));
            $producto->save();
        });
        return is_null($error) ? "OK" : $error;
    }

    public function cambiarAnaquel(Request $request)
    {
        $error = DB::transaction(function() use($request){
            $producto = Producto::find($request->input('id'));
            $producto->anaquel_id = Libreria::obtenerParametro($request->input('anaquel_id'));
            $producto->save();
        });
        return is_null($error) ? "OK" : $error;
    }

    public function vistamedico(Request $request)
    {
        $nombre = $request->input('producto');
        $resultado        = Producto::where('nombre', 'LIKE', '%'.strtoupper($nombre).'%')
                            ->orWhere(function($query) use($nombre){
                                $query->WhereIn('id',function($q) use($nombre){
                                    $q->select('producto_id')
                                      ->from('productoprincipio')
                                      ->join('principioactivo','principioactivo.id','=','Productoprincipio.principioactivo_id')
                                      ->where('principioactivo.nombre','like','%'.strtoupper($nombre).'%');
                                });
                            })
                            ->orderBy('nombre', 'ASC');
        $lista            = $resultado->limit(20)->get();
        $registro = '';
        if(count($lista) > 0) {
            $registro.="<table class='table table-bordered table-striped table-condensed table-hover'>
                    <thead>
                        <tr>
                            <th class='text-center'>#</th>
                            <th class='text-center'>Producto</th>
                            <th class='text-center'>Presentacion</th>
                            <th class='text-center'>Origen</th>
                            <th class='text-center'>Stock</th>
                            <th class='text-center'>P. Activo</th>
                            <th class='text-center'>Laboratorio</th>
                            <th class='text-center'>P. Venta</th>
                        </tr>
                    </thead>
                    <tbody>";
            $q = 1;
            foreach ($lista as $key => $value) {
                $registro.="<tr>";
                $registro.="<td>".$q."</td>";
                $registro.="<td>".$value->nombre."</td>";
                if($value->presentacion_id>0 && !is_null($value->presentacion)){
                    $registro.="<td align='center'>".$value->presentacion->nombre."</td>";
                }else{
                    $registro.="<td align='center'> - </td>";
                }
                if($value->origen_id>0){
                    $registro.="<td align='center'>".$value->origen->nombre."</td>";
                }else{
                    $registro.="<td align='center'> - </td>";
                }
                $currentstock = Kardex::join('detallemovimiento', 'kardex.detallemovimiento_id', '=', 'detallemovimiento.id')->join('movimiento', 'detallemovimiento.movimiento_id', '=', 'movimiento.id')->where('producto_id', '=', $value->id)->where('movimiento.almacen_id', '=',1)->orderBy('kardex.id', 'DESC')->first();
                $stock = 0;
                if ($currentstock !== null) {
                    $stock=$currentstock->stockactual;
                }
                $registro.="<td align='center'>".number_format($stock,0,'.','')."</td>";
                $listado = Productoprincipio::where('producto_id','=',$value->id)->get();
                $i = 0;
                $principio = '';
                foreach ($listado as $key2 => $value2) {
                    if ($i == 0) {
                       if ($value2->principioactivo !== null) {
                            $principio = $principio.$value2->principioactivo->nombre;
                        }
                    }else{
                        if ($value2->principioactivo !== null) {
                            $principio = $principio.'+'.$value2->principioactivo->nombre;
                        }
                    }
                    $i++;
                }
                $registro.="<td align='center'>".$principio."</td>";
                if($value->laboratorio_id>0 && !is_null($value->laboratorio)){
                    $registro.="<td align='center'>".$value->laboratorio->nombre."</td>";
                }else{
                    $registro.="<td align='center'> - </td>";
                }
                $registro.="<td align='center'>".$value->precioventa."</td>";
                $registro.="</tr>";
                $q++;
            }
            $registro.="</tbody></table>";
        
        } else {
            $registro ='<h3 class="text-warning">No se encontraron resultados.</h3>';
        }
        return $registro;
    }

    public function cie10(Request $request)
    {
        $nombre = $request->input('cie');
        $resultado        = Cie::where(DB::raw('concat(codigo,\' \',descripcion)'), 'LIKE', '%'.strtoupper($nombre).'%')
                            ->orderBy('descripcion', 'ASC');
        $lista            = $resultado->limit(20)->get();
        if(count($lista)>0) {
            $registro="<table class='table table-bordered table-striped table-condensed table-hover'>
                        <thead>
                            <tr>
                                <th class='text-center'>#</th>
                                <th class='text-center'>Codigo</th>
                                <th class='text-center'>Nombre</th>
                            </tr>
                        </thead>
                        <tbody>";
            $i = 1;
            foreach ($lista as $key => $value) {
                $registro.="<tr>";
                $registro.="<td>".$i."</td>";
                $registro.="<td>".$value->codigo."</td>";
                $registro.="<td>".$value->descripcion."</td>";
                $registro.="</tr>";
                $i++;
            }
            $registro.="</tbody></table>";            
        } else {
            $registro ='<h3 class="text-warning">No se encontraron resultados.</h3>';
        }
        return $registro;
    }

    public function actualizarKayrosMensual(Request $request) {
        $mes = date('m-Y');
        $producto = Producto::where('tipo', 'Z')->first();

        if($producto === NULL) {
            $product = new Producto();
            $product->tipo = 'Z';
            $product->nombre = 'CAMBIO KAYROS';
            $product->codigobarra = $mes;
            $product->save();
            $productos = Producto::where('tipo', '!=', 'Z')->get();
            foreach ($productos as $prod) {
                $prod->preciokayros = 0;
                $prod->save();
            }
            echo '';
        } else {
            if($producto->codigobarra != $mes) {
                $productos = Producto::where('tipo', '!=', 'Z')->get();
                foreach ($productos as $prod) {
                    $prod->preciokayros = 0;
                    $prod->save();
                }
                $producto->codigobarra = $mes;
                $producto->save();
                echo 'P';
            } else {
                echo '';
            }            
        }        
    }
}
