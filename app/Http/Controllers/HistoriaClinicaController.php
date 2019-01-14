<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Validator;
use App\Http\Requests;
use App\HistoriaClinica;
use App\Historia;
use App\Cie;
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
        $jsondata = array(
            'historia_id' => $historia->id,
            'ticket_id' => $ticket_id,
            'paciente' => $historia->persona->apellidopaterno . ' ' . $historia->persona->apellidomaterno . ' ' . $historia->persona->nombres,
            'numhistoria' => $historia->numero,
            'numero' => HistoriaClinica::NumeroSigue($historia->id),
        );
        return json_encode($jsondata);
    }

    public function registrarHistoriaClinica(Request $request)
    {
        $error = DB::transaction(function() use($request){

            $cie10 = Cie::where('codigo', $request->input('cie102'))->get();
            if(count($cie10) == 0) {
                return 'El Código CIE no existe';
            }
            $historiaclinica                 = new HistoriaClinica();
            $historiaclinica->numero         = strtoupper($request->input('numero'));
            $historiaclinica->historia_id    = strtoupper($request->input('historia_id'));
            $historiaclinica->tratamiento    = strtoupper($request->input('tratamiento'));
            $historiaclinica->sintomas       = strtoupper($request->input('sintomas'));
            $historiaclinica->diagnostico   = strtoupper($request->input('diasgnostico'));

            $historiaclinica->cie_id         = $cie10[0]->id;

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
