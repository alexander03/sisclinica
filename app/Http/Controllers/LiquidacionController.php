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
        $formData = array('liquidacion.update', 'id'=>$liquidacion->id, 'listar'=>'SI');
        $formData = array('route' => $formData, 'method' => 'PUT', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Modificar'; 
        return view($this->folderview.'.mantLiquidacion')->with(compact('ruta', 'entidad', 'boton', 'listar','liquidacion','formData', 'cabeceras'));
    }

    public function update(Request $request)
    {
    	$id = $request->input('id');
        $existe = Libreria::verificarExistencia($id, 'cotizacion');
        if ($existe !== true) {
            return $existe;
        }
        $listar = Libreria::getParam($request->input('listar'), 'SI');
        $user = Auth::user();
        $dat=array();
        $error = DB::transaction(function() use($request,$user,$id,&$dat){
            $liquidacion        = Cotizacion::find($id);
            $carta        = $liquidacion->cartagarantia;
            $carta->monto = $request->input('total');
            $carta->save();
            foreach ($liquidacion->detalles as $key => $value) {
                $value->delete();
            }

            $arr=explode(",",$request->input('listServicio'));
            $arr_detalle=explode(";",$request->input('listDetallesServicio'));
            for($c=0;$c<count($arr);$c++){
                $Detalle = new Detallecotizacion();
                $Detalle->cotizacion_id=$liquidacion->id;
                $Detalle->descripcion = trim($request->input('txtServicio'.$arr[$c]));
                $Detalle->monto = $request->input('txtFacturar'.$arr[$c]);
                $Detalle->save();

                $detallitos = explode(",",$arr_detalle[$c]);
                foreach ($detallitos as $value) {
                    $detallito = new Detallecotizacion();
                    $detallito->cotizacion_id=$liquidacion->id;
                    $detallito->detallecotizacion_id=$Detalle->id;

                    $detallito->descripcion = trim($request->input($arr[$c].'txtServicio'.$value));
                    $detallito->cantidad = $request->input($arr[$c].'txtCantidad'.$value);
                    $detallito->pago = $request->input($arr[$c].'txtPago'.$value);
                    $detallito->porcentaje = $request->input($arr[$c].'txtPorcentaje'.$value);
                    $detallito->monto = $request->input($arr[$c].'txtSoles'.$value);
                    //$detallito->unidad = $request->input($arr[$c].'txtUnidad'.$value);
                    //$detallito->factor = $request->input($arr[$c].'txtFactor'.$value);
                    $detallito->total = $request->input($arr[$c].'txtTotal'.$value);

                    $detallito->save();
                }
            }            
            $dat[0]=array("respuesta"=>"OK","id"=>$liquidacion->id);
        });
        return is_null($error) ? json_encode($dat) : $error;
    }
}
