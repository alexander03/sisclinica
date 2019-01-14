<?php

namespace App\Http\Controllers;

use Validator;
use App\Http\Requests;
use App\HistoriaClinica;
use App\Historia;
use App\Movimiento;
use App\Librerias\Libreria;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class HistoriaClinicaController extends Controller
{
    protected $folderview      = 'app.producto';
    protected $rutas           = array('create' => 'historiaclinica.create', 
            'buscar' => 'historiaclinica.buscar',
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
    public function nuevaHistoriaClinica($paciente_id, $ticket_id)
    {
        $historia = Historia::where('person_id', $paciente_id)->first();
        return view($this->folderview.'.vistamedico')->with(compact('historia', 'ticket_id'));
    }

    public function registrarHistoriaClinica(Request $request)
    {
        $reglas     = array(
            'tratamiento'  => 'required|max:1000',
            'sintomas'     => 'required|max:1000',
            'diagnostico'  => 'required|max:1000',
            'cie102'        => 'required',
        );
        $mensajes = array(
            'tratamiento.required'   => 'Debe ingresar un tratamiento',
            'sintomas.required'      => 'Debe ingresar un sintomas',
            'diasgnostico.required'  => 'Debe ingresar un diasgnostico',
            'cie102.required'         => 'Debe ingresar un cie102',
        );
        $validacion = Validator::make($request->all(), $reglas, $mensajes);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        }
        $error = DB::transaction(function() use($request){
            $historiaclinica                 = new HistoriaClinica();
            $historiaclinica->numero         = strtoupper($request->input('numero'));
            $historiaclinica->historia_id    = strtoupper($request->input('historia_id'));
            $historiaclinica->tratamiento    = strtoupper($request->input('tratamiento'));
            $historiaclinica->sintomas       = strtoupper($request->input('sintomas'));
            $historiaclinica->diasgnostico   = strtoupper($request->input('diasgnostico'));

            $cie10 = Cie::where('codigo', $request->input('cie10'))->first();

            $historiaclinica->cie_id         = $cie10->id;

            $now = new \DateTime();

            $historiaclinica->fecha_atencion = $now;
            $historiaclinica->save();

            $Ticket   = Movimiento::find($request->input('ticket_id'));
            $Ticket->situacion2 = 'L';
            $Ticket->save();

        });
        return is_null($error) ? "OK" : $error;
    }
}
