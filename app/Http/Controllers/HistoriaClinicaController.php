<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Validator;
use App\Http\Requests;
use App\HistoriaClinica;
use App\Historia;
use App\Cie;
use App\User;
use App\Cita;
use App\Servicio;
use App\Detallehistoriacie;
use App\Examenhistoriaclinica;
use App\Person;
use App\Detallemovcaja;
use App\Movimiento;
use App\Librerias\Libreria;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
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

        $ultimahc = HistoriaClinica::where('historia_id', $historia->id)->orderBy('id', 'DESC')->limit(1)->first();

        $mensaje = 'No hay mensajes.';

        if($ultimahc !== NULL && count($ultimahc)>0) {
            if($ultimahc->comentario != null || $ultimahc->comentario != '') {
                $mensaje = $ultimahc->comentario;
            }            
        }

        if($historiaclinica != null){
            //$cie10 = Cie::find($historiaclinica->cie_id);

            $examenes = Examenhistoriaclinica::leftjoin('servicio as servicio', 'servicio.id', '=', 'examenhistoriaclinica.servicio_id')
                                        ->where('examenhistoriaclinica.historiaclinica_id', $historiaclinica->id )
                                            ->get();

                                             $cies = Detallehistoriacie::leftjoin('cie', 'cie.id', '=', 'detallehistoriacie.cie_id')
                                        ->where('detallehistoriacie.historiaclinica_id',  $historiaclinica->id )->get();


            $cita = Cita::find($historiaclinica->citaproxima);

            $jsondata = array(
                'cita_id' => $historiaclinica->id,
                'historia_id' => $historia->id,
                'antecedentes' => $historia->antecedentes,
                'ticket_id' => $ticket_id,
                'fondo' => $fondo,
                'doctor_id' => $doctor->id,
                'doctor' => $doctor->apellidopaterno . ' ' . $doctor->apellidomaterno . ' ' . $doctor->nombres,
                'paciente' => $historia->persona->apellidopaterno . ' ' . $historia->persona->apellidomaterno . ' ' . $historia->persona->nombres,
                'numhistoria' => $historia->numero,
                'numero' => $historiaclinica->numero,
                'motivo' => $historiaclinica->motivo,
                //'cie10' => (is_null($cie10)?'':$cie10->codigo . " - " . $cie10->descripcion),
               // 'cie10id' => (is_null($cie10)?0:$cie10->id),
                'sintomas' => $historiaclinica->sintomas,
                'tratamiento' => $historiaclinica->tratamiento,
                'diagnostico' => $historiaclinica->diagnostico,
                'exploracion_fisica' => $historiaclinica->exploracion_fisica,
                'examenes' => $examenes,
                'cies' => $cies,
                'cantcies' => count($cies),
                'mensaje' => $mensaje,
            );

            if($cita != null){

                $cantidad = Cita::where('fecha', '=', ''.$cita->fecha.'')->count('id');

                $jsondata = array(
                    'cita_id' => $historiaclinica->id,
                    'historia_id' => $historia->id,
                    'antecedentes' => $historia->antecedentes,
                    'ticket_id' => $ticket_id,
                    'fondo' => $fondo,
                    'doctor_id' => $doctor->id,
                    'doctor' => $doctor->apellidopaterno . ' ' . $doctor->apellidomaterno . ' ' . $doctor->nombres,
                    'paciente' => $historia->persona->apellidopaterno . ' ' . $historia->persona->apellidomaterno . ' ' . $historia->persona->nombres,
                    'numhistoria' => $historia->numero,
                    'numero' => $historiaclinica->numero,
                    'motivo' => $historiaclinica->motivo,
                   // 'cie10' => (is_null($cie10)?'':$cie10->codigo . " - " . $cie10->descripcion),
                //    'cie10id' => (is_null($cie10)?0:$cie10->id),
                    'sintomas' => $historiaclinica->sintomas,
                    'citaproxima' => date('Y-m-d',strtotime($cita->fecha)) ,
                    'cantcitas' => $cantidad,
                    'tratamiento' => $historiaclinica->tratamiento,
                    'diagnostico' => $historiaclinica->diagnostico,
                    'exploracion_fisica' => $historiaclinica->exploracion_fisica,
                    'examenes' => $examenes,
                    'cies' => $cies,
                    'cantcies' => count($cies),
                    'mensaje' => $mensaje,
                );
    
            }
        }else{
            $jsondata = array(
                'historia_id' => $historia->id,
                'antecedentes' => $historia->antecedentes,
                'ticket_id' => $ticket_id,
                'fondo' => $fondo,
                'doctor_id' => $doctor->id,
                'doctor' => $doctor->apellidopaterno . ' ' . $doctor->apellidomaterno . ' ' . $doctor->nombres,
                'paciente' => $historia->persona->apellidopaterno . ' ' . $historia->persona->apellidomaterno . ' ' . $historia->persona->nombres,
                'numhistoria' => $historia->numero,
                'numero' => HistoriaClinica::numeroSigue($historia->id),
                'mensaje' => $mensaje,
            );
        }
        return json_encode($jsondata);
    }


    public function cie10autocompletar($searching)
    {
        $resultado        = Cie::where(DB::raw('CONCAT(codigo," ",descripcion)'), 'LIKE', '%'.strtoupper($searching).'%')->whereNull('deleted_at')->orderBy('descripcion', 'ASC');
        $list      = $resultado->get();
        $data = array();
        foreach ($list as $key => $value) {
            $name = $value->codigo." - ".$value->descripcion;
            $data[] = array(
                            'label' => trim($name),
                            'id'    => $value->id,
                            'value' => trim($name),
                        );
        }
        return json_encode($data);
    }

    public function examenesAutocompletar($searching)
    {
        $resultado        = Servicio::where('nombre', 'LIKE', '%'.strtoupper($searching).'%')->where('tiposervicio_id','!=', 1)->whereNull('deleted_at')->orderBy('nombre', 'ASC');
        $list      = $resultado->get();
        $data = array();
        foreach ($list as $key => $value) {
            $name = $value->nombre;
            $data[] = array(
                            'label' => trim($name),
                            'id'    => $value->id,
                            'value' => trim($name),
                        );
        }
        return json_encode($data);
    }

    public function registrarHistoriaClinica(Request $request)
    {

        $error = DB::transaction(function() use($request){
            if($request->input('citaproxima') != null){

                if($request->input('cita_id') == null){
                    
                    $Cita       = new Cita();

                    $user = Auth::user();
                    
                    //sucursal_id
                    $sucursal_id = 1;

                    $Cita->sucursal_id = $sucursal_id;
                    $Cita->fecha = $request->input('citaproxima');

                    $historia = Historia::find($request->input('historia_id'));

                    $Cita->paciente_id = $historia->persona->id;


                    $Cita->paciente = $historia->persona->apellidopaterno . " " . $historia->persona->apellidomaterno . " " . $historia->persona->nombres;
                    $Cita->historia = $historia->numero;
                    $Cita->tipopaciente = $historia->tipopaciente;


                    $Cita->historia_id = $request->input('historia_id');
                    
                    $Cita->doctor_id = $request->input('doctor_id');
                    
                    $Cita->situacion='P';//Pendiente
        
                    $Cita->usuario_id = $user->person_id;
                    $Cita->save();

                }else{

                    $historiaclinica   = HistoriaClinica::find($request->input('cita_id'));
                    
                    $error = DB::transaction(function() use($request, $historiaclinica){
                        if($historiaclinica->citaproxima == null){
                            $Cita       = new Cita();
                            $user = Auth::user();
                            
                            //sucursal_id
                            $sucursal_id = 1;
        
                            $Cita->sucursal_id = $sucursal_id;
                            $Cita->fecha = $request->input('citaproxima');
        
                            $historia = Historia::find($request->input('historia_id'));
        
                            $Cita->paciente_id = $historia->persona->id;
        
        
                            $Cita->paciente = $historia->persona->apellidopaterno . " " . $historia->persona->apellidomaterno . " " . $historia->persona->nombres;
                            $Cita->historia = $historia->numero;
                            $Cita->tipopaciente = $historia->tipopaciente;
        
        
                            $Cita->historia_id = $request->input('historia_id');
                            
                            $Cita->doctor_id = $request->input('doctor_id');
                            
                            $Cita->situacion='P';//Pendiente
                
                            $Cita->usuario_id = $user->person_id;
                            $Cita->save();

                        }else{
                            $cita  = Cita::find($historiaclinica->citaproxima);
                            $cita->fecha  = $request->input('citaproxima');
                            $cita->save();
                        }
                    });

                }

            }
        });

        $error = DB::transaction(function() use($request){
/*
            $cie10 = Cie::where('codigo', $request->input('cie102'))->get();
            if(count($cie10) == 0) {
                return 'El Código CIE no existe';
            }
*/
            $historiaclinica = HistoriaClinica::where('ticket_id', $request->input('ticket_id') )->first();

            if($historiaclinica == null){
                $historiaclinica                 = new HistoriaClinica();
            }
            $historiaclinica->numero         = (int) $request->input('numero');
            $historiaclinica->historia_id    = $request->input('historia_id');
            $historiaclinica->tratamiento    = strtoupper(($request->input('tratamiento')));
            $historiaclinica->sintomas       = strtoupper($request->input('sintomas'));
            $historiaclinica->diagnostico    = strtoupper($request->input('diagnostico'));
            //$historiaclinica->examenes             = strtoupper($request->input('examenes'));
            $historiaclinica->motivo               = strtoupper($request->input('motivo'));
            
            if($request->input('citaproxima') != null){
                   $historia = Historia::find($request->input('historia_id'));
                    $cita_id = Cita::where('historia_id',$historia->id)->where('paciente_id', $historia->persona->id)->max('id');
                    $historiaclinica->citaproxima     = $cita_id;
            }else{
                if($historiaclinica->citaproxima != null){
                    $citaant = Cita::find($historiaclinica->citaproxima);
                    $citaant->delete();
                    $historiaclinica->citaproxima     =  null ;
                }
            }

            $historiaclinica->exploracion_fisica   = strtoupper(($request->input('exploracion_fisica')));
            $historiaclinica->ticket_id =  $request->input('ticket_id');
            $historiaclinica->doctor_id =  $request->input('doctor_id');
            $user = Auth::user();
            $historiaclinica->user_id   = $user->id;

            $now = new \DateTime();

            //$historiaclinica->cie_id         = $request->input('cie102_id');

            $historiaclinica->fecha_atencion = $now;
            $historiaclinica->save();

            $Ticket   = Movimiento::find($request->input('ticket_id'));
            $Ticket->situacion2 = 'L'; //Atendido Listo

            if( $request->input('fondo') == "SI"){
                $Ticket->tiempo_fondo  = $now;
                $Ticket->situacion2 = 'F'; // Cola por fondo
            }

            $Ticket->save();

            $historia = Historia::find($request->input('historia_id'));
            $historia->antecedentes = strtoupper($request->input('antecedentes'));
            $historia->save();

        });

        $historiaclinica = HistoriaClinica::where('ticket_id', $request->input('ticket_id') )->first();

         $ciesborrar = Detallehistoriacie::where('historiaclinica_id', $historiaclinica->id )->get();
        foreach ($ciesborrar as $value) {
            $error = DB::transaction(function() use($request, $value){
                $value->delete();
            });
            
        }
        $cies = json_decode($request->input('cies'));
        foreach ($cies->{"data"} as $cie) {
            $error = DB::transaction(function() use($request, $historiaclinica, $cie){
                $detallehistoriacie = new Detallehistoriacie();
                $detallehistoriacie->historiaclinica_id = $historiaclinica->id;
                $detallehistoriacie->cie_id = $cie->{"id"};
                $detallehistoriacie->save();
            });
        }


        $examenesborrar = Examenhistoriaclinica::where('historiaclinica_id', $historiaclinica->id )->get();

        foreach ($examenesborrar as $value) {

            $error = DB::transaction(function() use($request, $value){

                $value->delete();

            });
            
        }

        $examenes = json_decode($request->input('examenes'));

        foreach ($examenes->{"data"} as $examen) {
            $error = DB::transaction(function() use($request, $historiaclinica, $examen){

                $examenhistoriaclinica = new Examenhistoriaclinica();
                $examenhistoriaclinica->situacion = 'N';
                $examenhistoriaclinica->historiaclinica_id = $historiaclinica->id;
                $examenhistoriaclinica->servicio_id = $examen->{"id"};
                $examenhistoriaclinica->save();

            });
        }


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
        //$cie10               = Cie::find($cita->cie_id);
        $doctor              = Person::find($cita->doctor_id);
        $user                = User::find($cita->user_id);
        $user2 = Auth::user();


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
            <tbody>";

                if($historia != null){
                    $texto .= "<tr>
                        <td width='15%'>
                            <strong><font style='color:blue'>Paciente</font></strong>
                        </td>
                        <td width='85%'>"
                            . $historia->persona->apellidopaterno . ' ' . $historia->persona->apellidomaterno . ' ' . $historia->persona->nombres .
                        "</td>
                    </tr>";
                }else{
                    $texto .= "<tr>
                        <td width='15%'>
                            <strong><font style='color:blue'>Paciente</font></strong>
                        </td>
                        <td width='85%'> - </td>
                    </tr>";
                }

                if($doctor != null){

                    $texto .= "<tr>
                        <td width='15%'>
                            <strong><font style='color:blue'>Doctor</font></strong>
                        </td>
                        <td width='85%'>"
                            . $doctor->apellidopaterno . ' ' . $doctor->apellidomaterno . ' ' . $doctor->nombres .
                        "</td>
                    </tr>";

                }else{
                    $texto .= "<tr>
                        <td width='15%'>
                            <strong><font style='color:blue'>Doctor</font></strong>
                        </td>
                        <td width='85%'> - </td>
                    </tr>";
                }

                if($historia != null){
                    $texto .= "<tr>
                        <td>
                            <strong><font style='color:blue'>Historia</font></strong><br>
                        </td>
                        <td>"
                            . $historia->numero .
                        "</td>
                    </tr>";
                }else{
                    $texto .= "<tr>
                        <td width='15%'>
                            <strong><font style='color:blue'>Historia</font></strong>
                        </td>
                        <td width='85%'> - </td>
                    </tr>";
                }

                $citaproxima = Cita::find($cita->citaproxima);

                if($citaproxima != null){

                    $texto .= "<tr>
                        <td>
                            <strong><font style='color:blue'>Próxima cita</font></strong><br>
                        </td>
                        <td>"
                            . date('d-m-Y',strtotime( $citaproxima->fecha )) .
                        "</td>
                    </tr>";

                }else{
                    $texto .= "<tr>
                        <td width='15%'>
                            <strong><font style='color:blue'>Próxima cita</font></strong>
                        </td>
                        <td width='85%'> - </td>
                    </tr>";
                }

                 $cies = Detallehistoriacie::where('historiaclinica_id', $cita->id)->whereNull('deleted_at')->get();

                if(count($cies) != 0){
                    $cont = 1;
                    $cies2 = "";
                    foreach ($cies as $value) {
                        $cies2 .= $cont . ' - ' . $value->cie->descripcion .'<br>';
                        $cont++;
                    }
                    $texto .= "<tr>
                        <td>
                            <strong><font style='color:blue'>Cie 10</font></strong><br>
                        </td>
                        <td>"
                            . $cies2 .
                        "</td>
                    </tr>";
                }else{
                    $texto .= "<tr>
                        <td width='15%'>
                            <strong><font style='color:blue'>Cie 10</font></strong>
                        </td>
                        <td width='85%'> - </td>
                    </tr>";
                }

                if($cita->motivo != null){
                
                    $texto .= "<tr>
                        <td>
                            <strong><font style='color:blue'>Motivo</font></strong><br>
                        </td>
                        <td>"
                            . $cita->motivo .
                        "</td>
                    </tr>";

                }else{
                    $texto .= "<tr>
                        <td width='15%'>
                            <strong><font style='color:blue'>Motivo</font></strong>
                        </td>
                        <td width='85%'> - </td>
                    </tr>";
                }

                if($cita->diagnostico != null){

                    $texto .= "<tr>
                        <td>
                            <strong><font style='color:blue'>Diagnóstico</font></strong><br>
                        </td>
                        <td>"
                            . $cita->diagnostico .
                        "</td>
                    </tr>";

                }else{
                    $texto .= "<tr>
                        <td width='15%'>
                            <strong><font style='color:blue'>Diagnóstico</font></strong>
                        </td>
                        <td width='85%'> - </td>
                    </tr>";
                }

                if($cita->tratamiento != null){
                
                $texto .= "<tr>
                    <td>
                        <strong><font style='color:blue'>Tratamiento</font></strong><br>
                    </td>
                    <td>"
                        . $cita->tratamiento .
                    "</td>
                </tr>";

                }else{
                    $texto .= "<tr>
                        <td width='15%'>
                            <strong><font style='color:blue'>Tratamiento</font></strong>
                        </td>
                        <td width='85%'> - </td>
                    </tr>";
                }

                $examenes = Examenhistoriaclinica::where('historiaclinica_id', $cita->id)->whereNull('deleted_at')->get();

                if(count($examenes) != 0){

                    $cont = 1;
                    $examenes2 = "";
                    foreach ($examenes as $value) {
                        $examenes2 .= $cont . ' - ' . $value->servicio->nombre .'<br>';
                        $cont++;
                    }

                    $texto .= "<tr>
                        <td>
                            <strong><font style='color:blue'>Exámenes</font></strong><br>
                        </td>
                        <td>"
                            . $examenes2 .
                        "</td>
                    </tr>";
                }else{
                    $texto .= "<tr>
                        <td width='15%'>
                            <strong><font style='color:blue'>Exámenes</font></strong>
                        </td>
                        <td width='85%'> - </td>
                    </tr>";
                }

                if($cita->exploracion_fisica != null){
                    $texto .= "<tr>
                        <td>
                            <strong><font style='color:blue'>Exploración física</font></strong><br>
                        </td>
                        <td><div class='table-responsive' style='max-width:450px;'>"
                            . $cita->exploracion_fisica .
                        "</div></td>
                    </tr>";
                }else{
                    $texto .= "<tr>
                        <td width='15%'>
                            <strong><font style='color:blue'>Exploración física</font></strong>
                        </td>
                        <td width='85%'> - </td>
                    </tr>";
                }

                if($user != null){
                    $texto .= "<tr>
                        <td>
                            <strong><font style='color:blue'>Responsable</font></strong><br>
                        </td>
                        <td>"
                            . $user->person->apellidopaterno . ' ' . $user->person->apellidomaterno . ' ' . $user->person->nombres .
                        "</td>
                    </tr>";
                }else{
                    $texto .= "<tr>
                        <td width='15%'>
                            <strong><font style='color:blue'>Responsable</font></strong>
                        </td>
                        <td width='85%'> - </td>
                    </tr>";
                }

             $texto .= "<tr>
                        <td>
                            <strong><font style='color:blue'>Comentario</font></strong><br>
                        </td>
                        <td><textarea class='form-control' id='anadirComentario' rows='8'>" . $cita->comentario . "</textarea>";

            if($user2->usertype_id == 5 || $user2->usertype_id == 7 || $user2->usertype_id == 1) {

                $texto .= "<a class='btn btn-danger btn-xs' href='#' onclick='anadirComentario(" . $cita_id . ")'>Añadir</a>";
            }

            $texto .= "</td></tr></tbody></table>";

        return $texto;
    }

    public function tablaAtendidos(Request $request){

        $nombre = $request->input('nombre');
        $fecha = $request->input('fecha');
        $fecha = date('Y-m-d', strtotime($fecha));

        $ruta             = $this->rutas;

        $resultado = HistoriaClinica::whereDate('fecha_atencion', '=' ,$fecha)->orderBy('id', 'ASC')->get();

        if($nombre != null){

            $resultado = HistoriaClinica::leftjoin('historia as h', 'h.id', '=', 'historiaclinica.historia_id')
            ->join('person as paciente', 'paciente.id', '=', 'h.person_id')
            ->whereDate('fecha_atencion', '=' ,$fecha)
            ->where(DB::raw('concat(paciente.apellidopaterno,\' \',paciente.apellidomaterno,\' \',paciente.nombres)'), 'LIKE', '%'.$nombre.'%')
            ->orderBy('historiaclinica.id', 'ASC')
            ->select('historiaclinica.*')
            ->get();
        
        }

        $tabla = "<table class='table table-bordered table-striped table-condensed table-hover'>
                            <thead>
                                <tr>
                                    <th class='text-center'>Nro</th>
                                    <th class='text-center'>Hora</th>
                                    <th class='text-center'>Paciente</th>
                                    <th class='text-center'>Doctor</th>
                                    <th class='text-center' colspan='3'>Acciones</th>
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
                </td>
                <td><center><button class='btn btn-info btn-sm' data-toggle='modal' data-target='#exampleModal4' onclick='abrirModalAntecedentesPasados(\"".$historia->numero."\", \"" . $historia->persona->apellidopaterno . ' ' . $historia->persona->apellidomaterno . ' ' . $historia->persona->nombres . "\")' type='button'><i class='fa fa-eye fa-lg'></i> Antecedentes</button></center>
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

        $citaproxima = null;

        if($historiaclinica->citaproxima !=null){
            $cita = Cita::find($historiaclinica->citaproxima);
            $citaproxima = $cita->fecha;
        }

        $examenes = Examenhistoriaclinica::leftjoin('servicio as servicio', 'servicio.id', '=', 'examenhistoriaclinica.servicio_id')
                    ->where('examenhistoriaclinica.historiaclinica_id', $historiaclinica->id )
                    ->get();

        //$cie10 = Cie::find($historiaclinica->cie_id);

        $cies = Detallehistoriacie::leftjoin('cie', 'cie.id', '=', 'detallehistoriacie.cie_id')
        ->where('detallehistoriacie.historiaclinica_id',  $historiaclinica->id )->get();

        if($citaproxima != null){


            $jsondata = array(
                'atencion_id' => $request->input('cita_id'),
                'fecha' => date('d-m-Y',strtotime($historiaclinica->fecha_atencion)) ,
                'citaproxima' => date('Y-m-d',strtotime($citaproxima)) ,
                'fondo' => $fondo,
                'doctor' => $doctor->apellidopaterno . ' ' . $doctor->apellidomaterno . ' ' . $doctor->nombres,
                'paciente' => $historia->persona->apellidopaterno . ' ' . $historia->persona->apellidomaterno . ' ' . $historia->persona->nombres,
                'numhistoria' => $historia->numero,
                'antecedentes' => $historia->antecedentes,
                'numero' => $historiaclinica->numero,
                'motivo' => $historiaclinica->motivo,
                //'cie10' => (is_null($cie10)?'':$cie10->codigo),
                'sintomas' => $historiaclinica->sintomas,
                'tratamiento' => $historiaclinica->tratamiento,
                'diagnostico' => $historiaclinica->diagnostico,
                'exploracion_fisica' => $historiaclinica->exploracion_fisica,
                'examenes' => $examenes,
                'cies' => $cies,
                'cantcies' => count($cies),
            );

        }else{
            
            $jsondata = array(
                'atencion_id' => $request->input('cita_id'),
                'fecha' => date('d-m-Y',strtotime($historiaclinica->fecha_atencion)) ,
                'fondo' => $fondo,
                'doctor' => $doctor->apellidopaterno . ' ' . $doctor->apellidomaterno . ' ' . $doctor->nombres,
                'paciente' => $historia->persona->apellidopaterno . ' ' . $historia->persona->apellidomaterno . ' ' . $historia->persona->nombres,
                'numhistoria' => $historia->numero,
                'antecedentes' => $historia->antecedentes,
                'numero' => $historiaclinica->numero,
                'motivo' => $historiaclinica->motivo,
                //'cie10' => (is_null($cie10)?'':$cie10->codigo),
                'sintomas' => $historiaclinica->sintomas,
                'tratamiento' => $historiaclinica->tratamiento,
                'diagnostico' => $historiaclinica->diagnostico,
                'exploracion_fisica' => $historiaclinica->exploracion_fisica,
                'examenes' => $examenes,
                'cies' => $cies,
                'cantcies' => count($cies),
            );

        }

        return json_encode($jsondata);

    }

    public function guardarEditado(Request $request){


        
        if($request->input('citaproxima') != null){

            $historiaclinica   = HistoriaClinica::find($request->input('cita_id'));

            if( $historiaclinica->citaproxima != null){

                $error = DB::transaction(function() use($request, $historiaclinica){
                    $cita  = Cita::find($historiaclinica->citaproxima);
                    $cita->fecha  = $request->input('citaproxima');
                    $cita->save();
                });
                
            }else{
                            
                $error = DB::transaction(function() use($request, $historiaclinica){
                        
                    $Cita       = new Cita();

                    $user = Auth::user();
                    
                    //sucursal_id
                    $sucursal_id = 1;

                    $Cita->sucursal_id = $sucursal_id;
                    $Cita->fecha = $request->input('citaproxima');

                    $historia = Historia::find($historiaclinica->historia_id);

                    $Cita->paciente_id = $historia->persona->id;


                    $Cita->paciente = $historia->persona->apellidopaterno . " " . $historia->persona->apellidomaterno . " " . $historia->persona->nombres;
                    $Cita->historia = $historia->numero;
                    $Cita->tipopaciente = $historia->tipopaciente;


                    $Cita->historia_id = $historia->id;
                    
                    $Cita->doctor_id = $historiaclinica->doctor_id;
                    
                    $Cita->situacion='P';//Pendiente
        
                    $Cita->usuario_id = $user->person_id;
                    $Cita->save();

                });


            }

        }


        $error = DB::transaction(function() use($request){
            $historiaclinica   = HistoriaClinica::find($request->input('cita_id'));
            $historiaclinica->tratamiento    = strtoupper($request->input('tratamiento'));
            $historiaclinica->sintomas       = strtoupper($request->input('sintomas'));
            $historiaclinica->diagnostico    = strtoupper($request->input('diagnostico'));
            //$historiaclinica->examenes             = strtoupper($request->input('examenes'));
            $historiaclinica->motivo               = strtoupper($request->input('motivo'));
            $historiaclinica->exploracion_fisica   = strtoupper($request->input('exploracion_fisica'));          
            $user = Auth::user();
            $historiaclinica->user_id   = $user->id;

            if($request->input('citaproxima') != null){
                    $cita_id = Cita::where('historia_id',$historiaclinica->historia_id)->where('paciente_id', $historiaclinica->historia->persona->id)->max('id');
                    $historiaclinica->citaproxima     = $cita_id;
            }else{
                if($historiaclinica->citaproxima != null){
                    $citaant = Cita::find($historiaclinica->citaproxima);
                    $citaant->delete();
                    $historiaclinica->citaproxima     =  null ;
                }
            }

            $historiaclinica->save();

            $historia = Historia::find($historiaclinica->historia_id);
            $historia->antecedentes = strtoupper($request->input('antecedentes'));
            $historia->save();

            $Ticket   = Movimiento::find($historiaclinica->ticket_id);

            $now = new \DateTime();
/*
            if( $request->input('fondo') == "SI"){
                if($Ticket->situacion2 != 'F'){
                    $Ticket->tiempo_fondo  = $now;
                    $Ticket->situacion2 = 'F'; // Cola por fondo
                }
            }else{
                $Ticket->tiempo_fondo  = null;
                $Ticket->situacion2 = 'L'; // Cola por fondo
            }
*/
            $Ticket->save();

        });


        $historiaclinica = HistoriaClinica::find( $request->input('cita_id') );

        $ciesborrar = Detallehistoriacie::where('historiaclinica_id', $historiaclinica->id )->get();
        foreach ($ciesborrar as $value) {
            $error = DB::transaction(function() use($request, $value){
                $value->delete();
            });
            
        }
        $cies = json_decode($request->input('cies'));
        foreach ($cies->{"data"} as $cie) {
            $error = DB::transaction(function() use($request, $historiaclinica, $cie){
                $detallehistoriacie = new Detallehistoriacie();
                $detallehistoriacie->historiaclinica_id = $historiaclinica->id;
                $detallehistoriacie->cie_id = $cie->{"id"};
                $detallehistoriacie->save();
            });
        }


        $examenesborrar = Examenhistoriaclinica::where('historiaclinica_id', $historiaclinica->id )->get();

        foreach ($examenesborrar as $value) {

            $error = DB::transaction(function() use($request, $value){

                $value->delete();

            });
            
        }

        $examenes = json_decode($request->input('examenes'));

        foreach ($examenes->{"data"} as $examen) {
            $error = DB::transaction(function() use($request, $historiaclinica, $examen){

                $examenhistoriaclinica = new Examenhistoriaclinica();
                $examenhistoriaclinica->situacion = 'N';
                $examenhistoriaclinica->historiaclinica_id = $historiaclinica->id;
                $examenhistoriaclinica->servicio_id = $examen->{"id"};
                $examenhistoriaclinica->save();

            });
        }


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
                    <td width='70%'>";
                        if($paciente->dni !=null ){
                            $texto .= $paciente->dni;
                        }else{
                            $texto .= " - ";
                        }
                    $texto .= "</td>
                </tr>
                <tr>
                    <td>
                        <strong><font style='color:blue'>Fecha de nacimiento:</font></strong><br>
                    </td>
                    <td>";
                        if( $paciente->fechanacimiento != null){
                            $texto .= date('d-m-Y',strtotime($paciente->fechanacimiento));
                        }else{
                            $texto .= " - ";
                        }
                    $texto .= "</td>
                </tr>
                <tr>
                    <td>
                        <strong><font style='color:blue'>Edad:</font></strong><br>
                    </td>
                    <td>";
                        
                    $dia=date("d");
                    $mes=date("m");
                    $ano=date("Y");
                    $dianaz=date("d",strtotime($paciente->fechanacimiento));
                    $mesnaz=date("m",strtotime($paciente->fechanacimiento));
                    $anonaz=date("Y",strtotime($paciente->fechanacimiento));
                    //si el mes es el mismo pero el día inferior aun no ha cumplido años, le quitaremos un año al actual
                    if (($mesnaz == $mes) && ($dianaz > $dia)) {
                    $ano=($ano-1); }
                    //si el mes es superior al actual tampoco habrá cumplido años, por eso le quitamos un año al actual
                    if ($mesnaz > $mes) {
                    $ano=($ano-1);}
                    //ya no habría mas condiciones, ahora simplemente restamos los años y mostramos el resultado como su edad
                    $edad=($ano-$anonaz);
                        if( $paciente->fechanacimiento != null){
                            $texto .= $edad;
                        }else{
                            $texto .= " - ";
                        }
                    $texto .= "</td>
                </tr>
                <tr>
                    <td>
                        <strong><font style='color:blue'>Teléfono:</font></strong><br>
                    </td>
                    <td>";
                        if( $paciente->fechanacimiento != null){
                            $texto .= $paciente->telefono;
                        }else{
                            $texto .= " - ";
                        }
                    $texto .= "</td>
                </tr>
                <tr>
                    <td>
                        <strong><font style='color:blue'>Dirección</font></strong><br>
                    </td>
                    <td>";
                        if( $paciente->fechanacimiento != null){
                            $texto .= $paciente->direccion;
                        }else{
                            $texto .= " - ";
                        }
                    $texto .= "</td>
                </tr>
            </tbody>
        </table>";
        return $texto;

    }

    public function cantidadCitasFecha(Request $request){
        $fecha = $request->input('fecha');

        $cantidad = Cita::where('fecha', '=', ''.$fecha.'')->count('id');

        return $cantidad;
    }

    public function anadirComentario(Request $request)
    {
        $error = DB::transaction(function() use($request){

            $cita_id = $request->input('cita_id');
            $comentario = $request->input('comentario');

            $cita     = HistoriaClinica::find($cita_id);
            $cita->comentario = $comentario;
            $cita->save();

        });

        return $error == null ? '1' : $error;
    }

    public function infoAntecedentes(Request $request){       
        $numhistoria = $request->input('historia');
        $historia = Historia::where('numero','=', $numhistoria)->first();
        $texto = $historia->antecedentes2;
        return $texto;
    }

    public function actualizarAntecedentes(Request $request){       
        $numhistoria = $request->input('historia');
        $antecedentes = $request->input('antecedentes');
        $historia = Historia::where('numero','=', $numhistoria)->first();
        $historia->antecedentes2 = $antecedentes;
        $historia->save();
    }

}
