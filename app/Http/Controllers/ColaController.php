<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Validator;
use App\Http\Requests;
use App\Historia;
use App\Plan;
use App\Kardex;
use App\Tipodocumento;
use App\Movimiento;
use App\Detallemovimiento;
use App\Person;
use App\Servicio;
use App\Caja;
use App\Venta;
use App\Librerias\Libreria;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Elibyy\TCPDF\Facades\TCPDF;
use App\Detallemovcaja;
use App\Librerias\EnLetras;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Excel;
use DateTime;

class ColaController extends Controller
{
    public function cola(Request $request){
        date_default_timezone_set('America/Lima');

        $ticket_id = null;

        if($request->input('ticket_id') != null){
            $ticket_id = $request->input('ticket_id');
            $error = DB::transaction(function() use($request,$ticket_id){
                $Ticket = Movimiento::find($ticket_id);
                $Ticket->situacion2 = 'A'; // Llamando
                $Ticket->save();
            });
        }

        $consultas = Movimiento::where('fecha', date('Y-m-d') )->where('tiempo_fondo', null)->where('clasificacionconsulta','like','C')->orderBy('turno','ASC')->orderBy('tiempo_cola','ASC')->orderBy('situacion2','ASC')
        ->where(function($q) {            
            $q->where('situacion2', 'like', 'C')->orWhere('situacion2', 'like', 'A')->orWhere('situacion2', 'like', 'B')->orWhere('situacion2', 'like', 'N');
        })
        ->where(function($q) {            
            $q->where('situacion', 'like', 'C')->orWhere('situacion', 'like', 'R');
        });
        $lista = $consultas->limit(20)->get();

        $sconsutas = '';
        $semergencias = '';
        $sojos = '';
        $slectura = '';

        $sconsultas="
                    <h3 class='text-center' style='font-weight:bold;color:blue'>CONSULTAS</h3>
                    <table style='width:100%; font-weight: 700;' border='1'>
                        <thead>
                            <tr>
                                <th class='text-center' width='10%'>Nro</th>
                                <th class='text-center' width='70%'>Cliente</th>
                                <th class='text-center' style='display:none;' width='20%'>Tiempo</th>
                            </tr>
                        </thead>
                        <tbody>";
        $c=1;

        if(count($lista) == 0) {
            $sconsultas.= '<tr class="text-center"><td colspan="2">No Hay consultas.</td></tr>';
        }

        foreach ($lista as $key => $value) {
            if( $value->situacion2 == 'A'){
                $sconsultas.= "<tr class='llamando' style ='color: white; background-color:green;' id = '" . $value->id . "' >";
            }else if( $value->situacion2 == 'N'){
                $sconsultas.= "<tr class='tarde' style ='color: white; background-color:#f96a27;' id = '" . $value->id . "' >";
            }else if( $value->situacion2 == 'B'){
                $sconsultas.= "<tr class='atendiendo' style ='color: white; background-color:green;' id = '" . $value->id . "' >";
            }else{
                $sconsultas.= "<tr id = '" . $value->id . "' >";
            }

            //$sconsultas.= "<tr id = '" . $value->id . "' >";
            $sconsultas.= "<td class='text-center'>".$c."</td>";
            if(!is_null($value->persona)){
                $sconsultas.= "<td>".$value->persona->apellidopaterno." ".$value->persona->apellidomaterno." ".$value->persona->nombres."</td>";
            }
            
            $date1 = new \DateTime(date("H:i:s",strtotime('now')));
            $date2 = new \DateTime(date("H:i:s",strtotime($value->tiempo_cola)));

            $diff = $date2->diff($date1);

            $h = $diff->h;
            $m = $diff->i;
            $s = $diff->s;

            $tiempo ="";

            if($h<10){
                $tiempo = "0" . $h . ":";
            }else{
                $tiempo = $h . ":";
            }
            if($m<10){
                $tiempo = $tiempo . "0" . $m . ":";
            }else{
                $tiempo = $tiempo . $m . ":";
            }
            if($s<10){
                $tiempo = $tiempo . "0" . $s ;
            }else{
                $tiempo = $tiempo . $s;   
            }


           // $consultas.= "<td>". $diff->format('%H:%i:%s') ."</td>";
            $sconsultas.= "<td class='text-center' style='display:none;'>". $tiempo ."</td>";
            $sconsultas.= "</tr>";
            $c=$c+1;
        }

        $sconsultas .= '</tbody></table>';

        $emergencias = Movimiento::where('fecha', date('Y-m-d') )->where('tiempo_fondo', null)->where('clasificacionconsulta','like','E')->orderBy('tiempo_cola','ASC')->orderBy('situacion2','ASC')
        ->where(function($q) {            
            $q->where('situacion2', 'like', 'C')->orWhere('situacion2', 'like', 'A')->orWhere('situacion2', 'like', 'B')->orWhere('situacion2', 'like', 'N');
        })
        ->where(function($q) {            
            $q->where('situacion', 'like', 'C')->orWhere('situacion', 'like', 'R');
        });
        $lista2 = $emergencias->limit(15)->get();

        $fondos = Movimiento::where('fecha', date('Y-m-d') )->whereNotNull('tiempo_fondo')->orderBy('tiempo_fondo','ASC')->orderBy('situacion2','ASC')
        ->where(function($q) {            
            $q->where('situacion2', 'like', 'C')->orWhere('situacion2', 'like', 'A')->orWhere('situacion2', 'like', 'B')->orWhere('situacion2', 'like', 'F')->orWhere('situacion2', 'like', 'N');
        })
        ->where(function($q) {            
            $q->where('situacion', 'like', 'C')->orWhere('situacion', 'like', 'R');
        });
        $lista3 = $fondos->limit(15)->get();

        $lectura = Movimiento::where('fecha', date('Y-m-d') )->where('tiempo_fondo', null)->where('clasificacionconsulta','like','L')->orderBy('tiempo_cola','ASC')->orderBy('situacion2','ASC')
        ->where(function($q) {            
            $q->where('situacion2', 'like', 'C')->orWhere('situacion2', 'like', 'A')->orWhere('situacion2', 'like', 'B')->orWhere('situacion2', 'like', 'N');
        })
        ->where(function($q) {            
            $q->where('situacion', 'like', 'C')->orWhere('situacion', 'like', 'R');
        });
        $lista4 = $lectura->limit(15)->get();

        $semergencias.="<h3 class='text-center' style='font-weight:bold;color:red'>EMERGENCIAS</h3>
                        <table style='width:100%; font-weight: 700;' border='1'>
                            <thead>
                                <tr>
                                    <th class='text-center' width='10%'>Nro</th>
                                    <th class='text-center' width='70%'>Cliente</th>
                                    <th class='text-center' style='display:none;' width='20%'>Tiempo</th>
                                </tr>
                            </thead>
                            <tbody>";

        if(count($lista2) == 0) {
            $semergencias.= '<tr class="text-center"><td colspan="2">No Hay emergencias.</td></tr>';
        }
        $c=1;
        foreach ($lista2 as $key => $value) {
            if( $value->situacion2 == 'A'){
                $semergencias.= "<tr class='llamando' style ='color: white; background-color:green;' id = '" . $value->id . "' >";
            }else if( $value->situacion2 == 'N'){
                $semergencias.= "<tr class='tarde' style ='color: white; background-color:#f96a27;' id = '" . $value->id . "' >";
            }else if( $value->situacion2 == 'B'){
                $semergencias.= "<tr class='atendiendo' style ='color: white; background-color:green;' id = '" . $value->id . "' >";
            }else{
                $semergencias.= "<tr id = '" . $value->id . "' >";
            }
            $semergencias.= "<td class='text-center'>".$c."</td>";
            if(!is_null($value->persona)){
                $semergencias.= "<td>".$value->persona->apellidopaterno." ".$value->persona->apellidomaterno." ".$value->persona->nombres."</td>";
            }
            
            $date1 = new \DateTime(date("H:i:s",strtotime('now')));
            $date2 = new \DateTime(date("H:i:s",strtotime($value->tiempo_cola)));

            $diff = $date2->diff($date1);

            $h = $diff->h;
            $m = $diff->i;
            $s = $diff->s;

            $tiempo ="";

            if($h<10){
                $tiempo = "0" . $h . ":";
            }else{
                $tiempo = $h . ":";
            }
            if($m<10){
                $tiempo = $tiempo . "0" . $m . ":";
            }else{
                $tiempo = $tiempo . $m . ":";
            }
            if($s<10){
                $tiempo = $tiempo . "0" . $s ;
            }else{
                $tiempo = $tiempo . $s;   
            }


           // $semergencias.= "<td>". $diff->format('%H:%i:%s') ."</td>";
            $semergencias.= "<td class='text-center' style='display:none;'>". $tiempo ."</td>";
            $semergencias.= "</tr>";
            $c=$c+1;
        }

        $semergencias .= '</tbody></table>';
                            
        $sojos.="<h3 class='text-center' style='font-weight:bold;color:#3498DB'>FONDO DE OJOS</h3>
                    <table style='width:100%; font-weight: 700;' border='1'>
                            <thead>
                                <tr>
                                    <th class='text-center' width='10%'>Nro</th>
                                    <th class='text-center' width='70%'>Cliente</th>
                                    <th class='text-center' style='display:none;' width='20%'>Tiempo</th>
                                </tr>
                            </thead>
                            <tbody>";
        $c=1;

        if(count($lista3) == 0) {
            $sojos.= '<tr class="text-center"><td colspan="2">No Hay fondo de ojos.</td></tr>';
        }

        //fondos 

        foreach ($lista3 as $key => $value) {
            if( $value->situacion2 == 'A'){
                $sojos.= "<tr class='llamando' style ='color: white; background-color:green;' id = '" . $value->id . "' >";
            }else if( $value->situacion2 == 'N'){
                $sojos.= "<tr class='tarde' style ='color: white; background-color:#f96a27;' id = '" . $value->id . "' >";
            }else if( $value->situacion2 == 'B'){
                $sojos.= "<tr class='atendiendo' style ='color: white; background-color:green;' id = '" . $value->id . "' >";
            }else{
                $sojos.= "<tr id = '" . $value->id . "' >";
            }
            $sojos.= "<td class='text-center'>".$c."</td>";
            if(!is_null($value->persona)){
                $sojos.= "<td>".$value->persona->apellidopaterno." ".$value->persona->apellidomaterno." ".$value->persona->nombres."</td>";
            }

            $date1 = new \DateTime(date("H:i:s",strtotime('now')));
            $date2 = new \DateTime(date("H:i:s",strtotime($value->tiempo_fondo)));

            $diff = $date2->diff($date1);

            $h = $diff->h;
            $m = $diff->i;
            $s = $diff->s;

            $tiempo ="";

            if($h<10){
                $tiempo = "0" . $h . ":";
            }else{
                $tiempo = $h . ":";
            }
            if($m<10){
                $tiempo = $tiempo . "0" . $m . ":";
            }else{
                $tiempo = $tiempo . $m . ":";
            }
            if($s<10){
                $tiempo = $tiempo . "0" . $s ;
            }else{
                $tiempo = $tiempo . $s;   
            }


           // $registro.= "<td>". $diff->format('%H:%i:%s') ."</td>";
            $sojos.= "<td class='text-center' style='display:none;'>". $tiempo ."</td>";
            $sojos.= "</tr>";
            $c=$c+1;
        }

        $sojos.="</tbody></table>";

        $slectura.="<h3 class='text-center' style='font-weight:bold;color:green'>LECT. DE RESULTADOS</h3>
                    <table style='width:100%; font-weight: 700;' border='1'>
                            <thead>
                                <tr>
                                    <th class='text-center' width='10%'>Nro</th>
                                    <th class='text-center' width='70%'>Cliente</th>
                                    <th class='text-center' style='display:none;' width='20%'>Tiempo</th>
                                </tr>
                            </thead>
                            <tbody>";
        $c=1;

        if(count($lista4) == 0) {
            $slectura.= '<tr class="text-center"><td colspan="2">No Hay lectura de resultados.</td></tr>';
        }

        //lectura 

        foreach ($lista4 as $key => $value) {
            if( $value->situacion2 == 'A'){
                $slectura.= "<tr class='llamando' style ='color: white; background-color:green;' id = '" . $value->id . "' >";
            }else if( $value->situacion2 == 'N'){
                $slectura.= "<tr class='tarde' style ='color: white; background-color:#f96a27;' id = '" . $value->id . "' >";
            }else if( $value->situacion2 == 'B'){
                $slectura.= "<tr class='atendiendo' style ='color: white; background-color:green;' id = '" . $value->id . "' >";
            }else{
                $slectura.= "<tr id = '" . $value->id . "' >";
            }
            $slectura.= "<td class='text-center'>".$c."</td>";
            if(!is_null($value->persona)){
                $slectura.= "<td>".$value->persona->apellidopaterno." ".$value->persona->apellidomaterno." ".$value->persona->nombres."</td>";
            }

            $date1 = new \DateTime(date("H:i:s",strtotime('now')));
            $date2 = new \DateTime(date("H:i:s",strtotime($value->tiempo_cola)));

            $diff = $date2->diff($date1);

            $h = $diff->h;
            $m = $diff->i;
            $s = $diff->s;

            $tiempo ="";

            if($h<10){
                $tiempo = "0" . $h . ":";
            }else{
                $tiempo = $h . ":";
            }
            if($m<10){
                $tiempo = $tiempo . "0" . $m . ":";
            }else{
                $tiempo = $tiempo . $m . ":";
            }
            if($s<10){
                $tiempo = $tiempo . "0" . $s ;
            }else{
                $tiempo = $tiempo . $s;   
            }


           // $registro.= "<td>". $diff->format('%H:%i:%s') ."</td>";
            $slectura.= "<td class='text-center' style='display:none;'>". $tiempo ."</td>";
            $slectura.= "</tr>";
            $c=$c+1;
        }

        $slectura.="</tbody></table>";

        $jsondata = array(
            'emergencias' => $semergencias,
            'consultas' => $sconsultas,
            'ojos' => $sojos,
            'lectura' => $slectura,
        );
        return json_encode($jsondata);
    }
}