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
use App\Detallecotizacion;
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

class LiquidacionController extends Controller
{
    protected $folderview      = 'app.cartagarantia';
    protected $rutas           = array(
            'edit'   => 'liquidacion.edit'
        );

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function edit(Request $request)
    {
    	$id = $request->input('id');
        $existe = Libreria::verificarExistencia($id, 'cartagarantia');
        if ($existe !== true) {
            return $existe;
        }
        $listar   = Libreria::getParam($request->input('listar'), 'NO');
        $entidad  = 'CartaGarantia3';
        $boton    = 'Editar'; 
        $ruta     = $this->rutas;
        $liquidacion    = Cotizacion::where('cartagarantia_id', '=', $id)->first();
        $cabeceras    = Detallecotizacion::where('cotizacion_id', '=', $liquidacion->id)->where('detallecotizacion_id', '=', NULL)->get();
        $formData = array('liquidacion.update', $id);
        $formData = array('route' => $formData, 'method' => 'PUT', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Modificar'; 
        return view($this->folderview.'.mantLiquidacion')->with(compact('ruta', 'entidad', 'boton', 'listar','liquidacion','formData', 'cabeceras'));
    }

    public function update(Request $request, $id)
    {
        $existe = Libreria::verificarExistencia($id, 'cartagarantia');
        if ($existe !== true) {
            return $existe;
        }
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
        $error = DB::transaction(function() use($request,$user,$numerocarta,$id,&$dat){
            $cotizacion              = Cotizacion::find($request->input('cotizacion_id'));
            $cotizacion->situacion   = 'A';//ACEPTADA
            $cotizacion->paciente_id = $request->input('paciente_id');//ACEPTADA
            $cotizacion->total       = $request->input('totalcarta');
            $cotizacion->save();

            $carta                   = Cartagarantia::find($id);
            $carta->fecha            = $request->input('fechacarta');
            $carta->cotizacion_id    = $cotizacion->id;
            $carta->codigo           = $request->input('codigocarta');
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
}
