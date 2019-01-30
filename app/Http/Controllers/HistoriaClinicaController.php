<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Validator;
use App\Http\Requests;
use App\HistoriaClinica;
use App\Historia;
use App\Cie;
use App\Person;
use App\Detallemovcaja;
use App\Movimiento;
use App\Librerias\Libreria;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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
        $historiaclinica = HistoriaClinica::where('ticket_id', $ticket_id)->first();
        
        $Ticket   = Movimiento::find($ticket_id);
        $detallemovcaja = Detallemovcaja::where('movimiento_id', $ticket_id)->first();

        $doctor = Person::find($detallemovcaja->persona_id);

        $fondo = "NO";
        if($Ticket->tiempo_fondo != null){
            $fondo = "SI";
        }
        if($historiaclinica != null){
            $cie10 = Cie::find($historiaclinica->cie_id);
            $jsondata = array(
                'historia_id' => $historia->id,
                'ticket_id' => $ticket_id,
                'fondo' => $fondo,
                'doctor_id' => $doctor->id,
                'doctor' => $doctor->apellidopaterno . ' ' . $doctor->apellidomaterno . ' ' . $doctor->nombres,
                'paciente' => $historia->persona->apellidopaterno . ' ' . $historia->persona->apellidomaterno . ' ' . $historia->persona->nombres,
                'numhistoria' => $historia->numero,
                'numero' => $historiaclinica->numero,
                'motivo' => $historiaclinica->motivo,
                'cie10' => $cie10->codigo,
                'sintomas' => $historiaclinica->sintomas,
                'citaproxima' => $historiaclinica->citaproxima,
                'tratamiento' => $historiaclinica->tratamiento,
                'diagnostico' => $historiaclinica->diagnostico,
                'exploracion_fisica' => $historiaclinica->exploracion_fisica,
                'examenes' => $historiaclinica->examenes,
            );
        }else{
            $jsondata = array(
                'historia_id' => $historia->id,
                'ticket_id' => $ticket_id,
                'fondo' => $fondo,
                'doctor_id' => $doctor->id,
                'doctor' => $doctor->apellidopaterno . ' ' . $doctor->apellidomaterno . ' ' . $doctor->nombres,
                'paciente' => $historia->persona->apellidopaterno . ' ' . $historia->persona->apellidomaterno . ' ' . $historia->persona->nombres,
                'numhistoria' => $historia->numero,
                'numero' => HistoriaClinica::numeroSigue($historia->id),
            );
        }
        return json_encode($jsondata);
    }

    public function registrarHistoriaClinica(Request $request)
    {
        $error = DB::transaction(function() use($request){

            $cie10 = Cie::where('codigo', $request->input('cie102'))->get();
            if(count($cie10) == 0) {
                return 'El Código CIE no existe';
            }

            $historiaclinica = HistoriaClinica::where('ticket_id', $request->input('ticket_id') )->first();

            if($historiaclinica == null){
                $historiaclinica                 = new HistoriaClinica();
            }
            $historiaclinica->numero         = (int) $request->input('numero');
            $historiaclinica->historia_id    = $request->input('historia_id');
            $historiaclinica->tratamiento    = strtoupper($request->input('tratamiento'));
            $historiaclinica->sintomas       = strtoupper($request->input('sintomas'));
            $historiaclinica->diagnostico    = strtoupper($request->input('diagnostico'));
            $historiaclinica->examenes             = strtoupper($request->input('examenes'));
            $historiaclinica->motivo               = strtoupper($request->input('motivo'));
            $historiaclinica->citaproxima               = strtoupper($request->input('citaproxima'));
            $historiaclinica->exploracion_fisica   = strtoupper($request->input('exploracion_fisica'));
            $historiaclinica->ticket_id =  $request->input('ticket_id');
            $historiaclinica->doctor_id =  $request->input('doctor_id');

            $now = new \DateTime();

            $historiaclinica->cie_id         = $cie10[0]->id;

            $historiaclinica->fecha_atencion = $now;
            $historiaclinica->save();

            $Ticket   = Movimiento::find($request->input('ticket_id'));
            $Ticket->situacion2 = 'L'; //Atendido Listo

            if( $request->input('fondo') == "SI"){
                $Ticket->tiempo_fondo  = $now;
                $Ticket->situacion2 = 'F'; // Cola por fondo
            }

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
            $tabla .= '<tr><td colspan="3"><center>No Hay Citas Anteriores</center></td></tr>';
        } else {
            foreach($resultado as $value){

                $tabla = $tabla . "<tr>
                <td><center>" . $value->numero . "</center></td>
                <td><center>" . date('d-m-Y',strtotime($value->fecha_atencion)) . "</center></td>
                <td><center><button class='btn btn-success btn-sm btnVerCita' id='btnVerCita' onclick='ver(".$value->id.")' data-toggle='modal' data-target='#exampleModal1' type='button'><i class='fa fa-eye fa-lg'></i> Ver Cita</button></center>
                </td></tr>";

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
        $cie10               = Cie::find($cita->cie_id);
        $doctor              = Person::find($cita->doctor_id);

        if($cie10 == null){

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
                    <td width='15%'>
                        <strong><font style='color:blue'>Doctor</font></strong>
                    </td>
                    <td width='85%'>"
                        . $doctor->apellidopaterno . ' ' . $doctor->apellidomaterno . ' ' . $doctor->nombres .
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
                        <strong><font style='color:blue'>Próxima cita</font></strong><br>
                    </td>
                    <td>"
                        . date('d-m-Y',strtotime( $cita->citaproxima )) .
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

        }else{

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
                    <td width='15%'>
                        <strong><font style='color:blue'>Doctor</font></strong>
                    </td>
                    <td width='85%'>"
                        . $doctor->apellidopaterno . ' ' . $doctor->apellidomaterno . ' ' . $doctor->nombres .
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
                        <strong><font style='color:blue'>Próxima cita</font></strong><br>
                    </td>
                    <td>"
                        . date('d-m-Y',strtotime( $cita->citaproxima )) .
                    "</td>
                </tr>
                <tr>
                    <td>
                        <strong><font style='color:blue'>Cie 10</font></strong><br>
                    </td>
                    <td>"
                    .$cie10->codigo.
                    " - "
                    .$cie10->descripcion.
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

        }

        return $texto;
    }

    public function tablaAtendidos(Request $request){

        $ruta             = $this->rutas;

        $resultado = HistoriaClinica::whereDate('fecha_atencion', '=' ,Carbon::now()->format('Y-m-d') )->orderBy('id', 'ASC')->get();

        $tabla = "<table class='table table-bordered table-striped table-condensed table-hover'>
                            <thead>
                                <tr>
                                    <th class='text-center'>Nro</th>
                                    <th class='text-center'>Hora</th>
                                    <th class='text-center'>Paciente</th>
                                    <th class='text-center'>Doctor</th>
                                    <th class='text-center' colspan='2'>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>";

        if(count($resultado) == '0') {
            $tabla .= '<tr><td colspan="5"><center>No Hay Atenciones</center></td></tr>';
        } else {
            $c = 1;
            foreach($resultado as $value){

                $historia            = Historia::find($value->historia_id);

                $doctor              = Person::find($value->doctor_id);

                $tabla = $tabla . "<tr>
                <td><center>" . $c . "</center></td>
                <td><center>" . date('h:i:s a',strtotime($value->fecha_atencion)) . "</center></td>
                <td><center>" . $historia->persona->apellidopaterno . ' ' . $historia->persona->apellidomaterno . ' ' . $historia->persona->nombres  . "</center></td>
                <td><center>" .  $doctor->apellidopaterno . ' ' . $doctor->apellidomaterno . ' ' . $doctor->nombres . "</center></td>
                <td><center><button class='btn btn-success btn-sm btnVerCita' id='btnVerCita' onclick='ver(".$value->id.")' data-toggle='modal' data-target='#exampleModal1' type='button'><i class='fa fa-eye fa-lg'></i> Ver Cita</button></center>
                <td><center><button class='btn btn-primary btn-sm btnEditarCita' id='btnEditarCita' onclick='editar(".$value->id.")' data-toggle='modal' data-target='#exampleModal2' type='button'><i class='fa fa-pencil fa-lg'></i> Editar</button></center>
                </td></tr>";
                $c++;
            }
        }           

        $tabla = $tabla . "</tbody></table>";

        return $tabla;

    }

    public function editarCita(Request $request){

        $historiaclinica = HistoriaClinica::find($request->input('cita_id'));

        $historia = Historia::find($historiaclinica->historia_id);

        $Ticket   = Movimiento::find($historiaclinica->ticket_id);

        $detallemovcaja = Detallemovcaja::where('movimiento_id', $historiaclinica->ticket_id)->first();

        $doctor = Person::find($detallemovcaja->persona_id);

        $fondo = "NO";
        if($Ticket->tiempo_fondo != null){
            $fondo = "SI";
        }

        $cie10 = Cie::find($historiaclinica->cie_id);

        $jsondata = array(
            'atencion_id' => $request->input('cita_id'),
            'fecha' => date('d-m-Y',strtotime($historiaclinica->fecha_atencion)) ,
            'fondo' => $fondo,
            'doctor' => $doctor->apellidopaterno . ' ' . $doctor->apellidomaterno . ' ' . $doctor->nombres,
            'paciente' => $historia->persona->apellidopaterno . ' ' . $historia->persona->apellidomaterno . ' ' . $historia->persona->nombres,
            'numhistoria' => $historia->numero,
            'numero' => $historiaclinica->numero,
            'motivo' => $historiaclinica->motivo,
            'citaproxima' => $historiaclinica->citaproxima,
            'cie10' => $cie10->codigo,
            'sintomas' => $historiaclinica->sintomas,
            'tratamiento' => $historiaclinica->tratamiento,
            'diagnostico' => $historiaclinica->diagnostico,
            'exploracion_fisica' => $historiaclinica->exploracion_fisica,
            'examenes' => $historiaclinica->examenes,
        );

        return json_encode($jsondata);

    }

    public function guardarEditado(Request $request){

        $error = DB::transaction(function() use($request){
            $historiaclinica   = HistoriaClinica::find($request->input('cita_id'));
            $historiaclinica->tratamiento    = strtoupper($request->input('tratamiento'));
            $historiaclinica->sintomas       = strtoupper($request->input('sintomas'));
            $historiaclinica->diagnostico    = strtoupper($request->input('diagnostico'));
            $historiaclinica->examenes             = strtoupper($request->input('examenes'));
            $historiaclinica->motivo               = strtoupper($request->input('motivo'));
            $historiaclinica->citaproxima               = strtoupper($request->input('citaproxima'));
            $historiaclinica->exploracion_fisica   = strtoupper($request->input('exploracion_fisica'));
            $historiaclinica->save();
        });

        return is_null($error) ? "OK" : $error;

    }

    public function infoPaciente(Request $request){
        
        $numhistoria = $request->input('historia');
        $historia = Historia::where('numero','=', $numhistoria)->first();
        $paciente = Person::find($historia->person_id);

        $texto = "<table class='table table-responsive table-hover'>
            <thead>
                <tr>
                    <td colspan='2'>
                        <center style='color:red'>
                            <h3>
                                Paciente: ". $historia->persona->apellidopaterno . ' ' . $historia->persona->apellidomaterno . ' ' . $historia->persona->nombres ."
                            </h3>
                        </center>
                    </td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td width='30%'>
                        <strong><font style='color:blue'>DNI:</font></strong>
                    </td>
                    <td width='70%'>"
                        . $paciente->dni .
                    "</td>
                </tr>
                <tr>
                    <td>
                        <strong><font style='color:blue'>Fecha de nacimiento:</font></strong><br>
                    </td>
                    <td>"
                        . date('d-m-Y',strtotime($paciente->fechanacimiento)).
                    "</td>
                </tr>
                <tr>
                    <td>
                        <strong><font style='color:blue'>Teléfono:</font></strong><br>
                    </td>
                    <td>"
                       .$paciente->telefono.
                    "</td>
                </tr>
                <tr>
                    <td>
                        <strong><font style='color:blue'>Dirección</font></strong><br>
                    </td>
                    <td>"
                        . $paciente->direccion .
                    "</td>
                </tr>
            </tbody>
        </table>";

        return $texto;

    }

}
