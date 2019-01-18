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
            'numero' => HistoriaClinica::numeroSigue($historia->id),
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
            $historiaclinica->numero         = (int) $request->input('numero');
            $historiaclinica->historia_id    = $request->input('historia_id');
            $historiaclinica->tratamiento    = strtoupper($request->input('tratamiento'));
            $historiaclinica->sintomas       = strtoupper($request->input('sintomas'));
            $historiaclinica->diagnostico    = strtoupper($request->input('diagnostico'));
            $historiaclinica->examenes             = strtoupper($request->input('examenes'));
            $historiaclinica->motivo               = strtoupper($request->input('motivo'));
            $historiaclinica->exploracion_fisica   = strtoupper($request->input('exploracion_fisica'));

            $historiaclinica->cie_id         = $cie10[0]->id;

            $now = new \DateTime();

            $historiaclinica->fecha_atencion = $now;
            $historiaclinica->save();

            $Ticket   = Movimiento::find($request->input('ticket_id'));
            $Ticket->situacion2 = 'A'; //Atendido
            $Ticket->save();

        });
        return is_null($error) ? "OK" : $error;
    }

    public function tablaCita(Request $request){

        $ruta             = $this->rutas;

        $historia_id = $request->input('historia_id');

        $resultado = HistoriaClinica::where('historia_id', '=', $historia_id)->orderBy('numero', 'ASC')->get();

        $tabla = "<table class='table table-bordered table-striped table-condensed table-hover'>
                            <thead>
                                <tr>
                                    <th class='text-center'>Nro</th>
                                    <th class='text-center'>Fecha</th>
                                    <th class='text-center'>Ver</th>
                                </tr>
                            </thead>
                            <tbody>";

        if(count($resultado) == '0') {
            $tabla .= '<tr><td colspan="3"><center>No Hay Historias Antiguas</center></td></tr>';
        } else {
            foreach($resultado as $value){

                $tabla = $tabla . "<tr><td>" . $value->numero . "</td><td>" . date('d-m-Y',strtotime($value->fecha_atencion)) . "</td><td><button class='btn btn-success btn-sm btnVerCita' id='btnVerCita' onclick='ver(".$value->id.")' data-toggle='modal' data-target='#exampleModal' type='button'><i class='fa fa-eye fa-lg'></i> Ver Cita</button></td></tr>";

            }
        }           

        $tabla = $tabla . "</tbody></table>";

        return $tabla;

    }

    public function ver(Request $request)
    {
        $cita_id             = $request->input('cita_id');
        $cita                = HistoriaClinica::find($cita_id);
        $historia            = Historia::find($cita->historia_id);
        $cie10 = Cie::find($cita->cie_id);

        $texto = "<table class='table table-responsive table-hover'>
            <thead>
                <tr>
                    <td colspan='2'>
                        <center style='color:red'>
                            <h3>
                                Tratamiento N°". $cita->numero ." / ". date('d-m-Y',strtotime($cita->fecha_atencion)) ."
                            </h3>
                        </center>
                    </td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td width='15%'>
                        <strong><font style='color:blue'>Paciente</font></strong>
                    </td>
                    <td width='85%'>"
                        . $historia->persona->apellidopaterno . ' ' . $historia->persona->apellidomaterno . ' ' . $historia->persona->nombres .
                    "</td>
                </tr>
                <tr>
                    <td>
                        <strong><font style='color:blue'>Historia</font></strong><br>
                    </td>
                    <td>"
                        . $historia->numero .
                    "</td>
                </tr>
                <tr>
                    <td>
                        <strong><font style='color:blue'>Cie 10</font></strong><br>
                    </td>
                    <td>"
                        . $cie10->codigo . " - " . $cie10->descripcion .
                    "</td>
                </tr>
                <tr>
                    <td>
                        <strong><font style='color:blue'>Motivo</font></strong><br>
                    </td>
                    <td>"
                        . $cita->motivo .
                    "</td>
                </tr>
                <tr>
                    <td>
                        <strong><font style='color:blue'>Síntomas</font></strong><br>
                    </td>
                    <td>"
                        . $cita->sintomas .
                    "</td>
                </tr>
                <tr>
                    <td>
                        <strong><font style='color:blue'>Diagnóstico</font></strong><br>
                    </td>
                    <td>"
                        . $cita->diagnostico .
                    "</td>
                </tr>
                <tr>
                    <td>
                        <strong><font style='color:blue'>Tratamiento</font></strong><br>
                    </td>
                    <td>"
                        . $cita->tratamiento .
                    "</td>
                </tr>
                <tr>
                    <td>
                        <strong><font style='color:blue'>Exámenes</font></strong><br>
                    </td>
                    <td>"
                        . $cita->examenes .
                    "</td>
                </tr>
                <tr>
                    <td>
                        <strong><font style='color:blue'>Exploración física</font></strong><br>
                    </td>
                    <td>"
                        . $cita->exploracion_fisica .
                    "</td>
                </tr>
            </tbody>
        </table>";

        return $texto;
    }
}
