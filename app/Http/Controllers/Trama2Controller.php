<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Validator;
use App\Http\Requests;
use App\HistoriaClinica;
use App\Historia;
use App\Trama2;
use App\Movimiento;
use App\Librerias\Libreria;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use ZipArchive;

class Trama2Controller extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function registrardatos(Request $request)
    {
        $Trama = Trama2::find(1);

        if($Trama == null) {
            $Trama = new Trama2();
        }
       
        $Trama->codigo1 = $request->input('codigo1');
        $Trama->codigo2 = $request->input('codigo2');
        $Trama->consultoriosfi = $request->input('consultoriosfi');
        $Trama->consultoriosfu = $request->input('consultoriosfu');
        $Trama->camas = $request->input('camas');
        $Trama->medicost = $request->input('medicost');
        $Trama->medicoss = $request->input('medicoss');
        $Trama->medicosr = $request->input('medicosr');
        $Trama->enfermeras = $request->input('enfermeras');
        $Trama->odontologos = $request->input('odontologos');
        $Trama->psicologos = $request->input('psicologos');
        $Trama->nutricionistas = $request->input('nutricionistas');
        $Trama->tecnologos = $request->input('tecnologos');
        $Trama->obstetrices = $request->input('obstetrices');
        $Trama->farmaceuticos = $request->input('farmaceuticos');
        $Trama->auxiliares = $request->input('auxiliares');
        $Trama->otros = $request->input('otros');
        $Trama->ambulancias = $request->input('ambulancias');

        $Trama->save();
    }

    public function descargarZipTramas(Request $request) {

        $anno   = substr($request->input('fecha_trama'), 0, 4);
        $mes    = substr($request->input('fecha_trama'), 5, 7);
        $fechai = $request->input('fecha_trama') . '-01';
        $fechaf = $request->input('fecha_trama') . '-31';
        $fechatrama = str_replace('-', '', $request->input('fecha_trama'));

        $trama = $request->input('trama');

        $datos = Trama2::find(1);

        if($trama == 'TAA0') {
            $elementos = Trama2::get();
        }
        $cons =
        '(CASE 
            WHEN YEAR(CURDATE())-YEAR(person.fechanacimiento) < 1 THEN 1 
            WHEN YEAR(CURDATE())-YEAR(person.fechanacimiento) >=1 AND YEAR(CURDATE())-YEAR(person.fechanacimiento) <= 4 THEN 2
            WHEN YEAR(CURDATE())-YEAR(person.fechanacimiento) >=5 AND YEAR(CURDATE())-YEAR(person.fechanacimiento) <= 9 THEN 3
            WHEN YEAR(CURDATE())-YEAR(person.fechanacimiento) >=10 AND YEAR(CURDATE())-YEAR(person.fechanacimiento) <= 14 THEN 4
            WHEN YEAR(CURDATE())-YEAR(person.fechanacimiento) >=15 AND YEAR(CURDATE())-YEAR(person.fechanacimiento) <= 19 THEN 5
            WHEN YEAR(CURDATE())-YEAR(person.fechanacimiento) >=20 AND YEAR(CURDATE())-YEAR(person.fechanacimiento) <= 24 THEN 6
            WHEN YEAR(CURDATE())-YEAR(person.fechanacimiento) >=25 AND YEAR(CURDATE())-YEAR(person.fechanacimiento) <= 29 THEN 7
            WHEN YEAR(CURDATE())-YEAR(person.fechanacimiento) >=30 AND YEAR(CURDATE())-YEAR(person.fechanacimiento) <= 34 THEN 8
            WHEN YEAR(CURDATE())-YEAR(person.fechanacimiento) >=35 AND YEAR(CURDATE())-YEAR(person.fechanacimiento) <= 39 THEN 9
            WHEN YEAR(CURDATE())-YEAR(person.fechanacimiento) >=40 AND YEAR(CURDATE())-YEAR(person.fechanacimiento) <= 44 THEN 10
            WHEN YEAR(CURDATE())-YEAR(person.fechanacimiento) >=45 AND YEAR(CURDATE())-YEAR(person.fechanacimiento) <= 49 THEN 11
            WHEN YEAR(CURDATE())-YEAR(person.fechanacimiento) >=50 AND YEAR(CURDATE())-YEAR(person.fechanacimiento) <= 54 THEN 12
            WHEN YEAR(CURDATE())-YEAR(person.fechanacimiento) >=55 AND YEAR(CURDATE())-YEAR(person.fechanacimiento) <= 59 THEN 13
            WHEN YEAR(CURDATE())-YEAR(person.fechanacimiento) >=60 AND YEAR(CURDATE())-YEAR(person.fechanacimiento) <= 64 THEN 14
            WHEN YEAR(CURDATE())-YEAR(person.fechanacimiento) > 64 THEN 15        
        END)';        
        if($trama == 'TAB1') {
            $elementos = Movimiento::where('clasificacionconsulta', 'C')
                        ->where('situacion', 'C')
                        ->where('situacion2', 'L')
                        ->where('sucursal_id', 1)
                        ->where('fechanacimiento', '!=', 'NULL')
                        ->where('sexo', '!=', 'NULL')
                        ->whereBetween('fecha_atencion', [$fechai, $fechaf])
                        ->join('historiaclinica', 'ticket_id', '=', 'movimiento.id')
                        ->join('person', 'movimiento.persona_id', '=', 'person.id')
                        ->orderBy('person.sexo', 'DESC')
                        ->orderBy(DB::raw($cons), 'ASC')
                        ->groupBy('person.sexo')
                        ->groupBy(DB::raw($cons))
                        ->select(DB::raw($cons.' AS edad'), DB::raw('(CASE WHEN person.sexo = "M" THEN 1 WHEN person.sexo = "F" THEN 2 END) AS sexo'), DB::raw('COUNT(movimiento.id) AS totalatenciones'))
                        ->get();
        }
        if($trama == 'TAB2') {
            $elementos = Movimiento::where('clasificacionconsulta', 'C')
                        ->where('situacion', 'C')
                        ->where('situacion2', 'L')
                        ->where('sucursal_id', 1)
                        ->where('fechanacimiento', '!=', 'NULL')
                        ->where('sexo', '!=', 'NULL')
                        ->whereBetween('fecha_atencion', [$fechai, $fechaf])
                        ->join('historiaclinica', 'ticket_id', '=', 'movimiento.id')
                        ->join('person', 'movimiento.persona_id', '=', 'person.id')
                        ->join('detallehistoriacie', 'detallehistoriacie.historiaclinica_id', '=', 'historiaclinica.id')
                        ->join('cie', 'cie.id', '=', 'detallehistoriacie.cie_id')
                        ->orderBy('person.sexo', 'DESC')
                        ->orderBy(DB::raw($cons), 'ASC')
                        ->orderBy('codigo', 'ASC')
                        ->groupBy('person.sexo')
                        ->groupBy(DB::raw($cons))
                        ->groupBy('cie.codigo')
                        ->select(DB::raw($cons.' AS edad'), DB::raw('(CASE WHEN person.sexo = "M" THEN 1 WHEN person.sexo = "F" THEN 2 END) AS sexo'), DB::raw('COUNT(movimiento.id) AS totalatenciones'), 'cie.codigo')
                        ->get();

        }
        if($trama == 'TAC1') {
            $elementos = Movimiento::where('clasificacionconsulta', 'E')
                        ->where('situacion', 'C')
                        ->where('situacion2', 'L')
                        ->where('sucursal_id', 1)
                        ->where('fechanacimiento', '!=', 'NULL')
                        ->where('sexo', '!=', 'NULL')
                        ->whereBetween('fecha_atencion', [$fechai, $fechaf])
                        ->join('historiaclinica', 'ticket_id', '=', 'movimiento.id')
                        ->join('person', 'movimiento.persona_id', '=', 'person.id')
                        ->orderBy('person.sexo', 'DESC')
                        ->orderBy(DB::raw($cons), 'ASC')
                        ->groupBy('person.sexo')
                        ->groupBy(DB::raw($cons))
                        ->select(DB::raw($cons.' AS edad'), DB::raw('(CASE WHEN person.sexo = "M" THEN 1 WHEN person.sexo = "F" THEN 2 END) AS sexo'), DB::raw('COUNT(movimiento.id) AS totalatenciones'))
                        ->get();
        }
        if($trama == 'TAC2') {
            $elementos = Movimiento::where('clasificacionconsulta', 'E')
                        ->where('situacion', 'C')
                        ->where('situacion2', 'L')
                        ->where('sucursal_id', 1)
                        ->where('fechanacimiento', '!=', 'NULL')
                        ->where('sexo', '!=', 'NULL')
                        ->whereBetween('fecha_atencion', [$fechai, $fechaf])
                        ->join('historiaclinica', 'ticket_id', '=', 'movimiento.id')
                        ->join('person', 'movimiento.persona_id', '=', 'person.id')
                        ->join('detallehistoriacie', 'detallehistoriacie.historiaclinica_id', '=', 'historiaclinica.id')
                        ->join('cie', 'cie.id', '=', 'detallehistoriacie.cie_id')
                        ->orderBy('person.sexo', 'DESC')
                        ->orderBy(DB::raw($cons), 'ASC')
                        ->orderBy('codigo', 'ASC')
                        ->groupBy('person.sexo')
                        ->groupBy(DB::raw($cons))                        
                        ->groupBy('cie.codigo')
                        ->select(DB::raw($cons.'AS edad'), DB::raw('(CASE WHEN person.sexo = "M" THEN 1 WHEN person.sexo = "F" THEN 2 END) AS sexo'), DB::raw('COUNT(movimiento.id) AS totalatenciones'), 'cie.codigo')
                        ->get();
        }
        if($trama == 'TAD1') {
            $elementos = '';
        }
        if($trama == 'TAD2') {
            $elementos = '';
        }
        if($trama == 'TAE0') {
            $elementos = '';
        }
        if($trama == 'TAF0') {
            $elementos = '';
        }
        if($trama == 'TAG0') {
            $elementos = '';
        }
        if($trama == 'TAH0') {

        }
        if($trama == 'TAI0') {
            $elementos = '';
        }
        if($trama == 'TAJ0') {
            $elementos = '';
        }

        $content = \View::make('app.trama2.reporte')->with('trama', $trama)->with('elementos', $elementos)->with('fechatrama', $fechatrama)->with('datos', $datos);

        // Set the name of the text file
        $filename = $datos->codigo1 . '_' . $anno . '_' . $mes . '_' . $trama . '.txt';

        // Set headers necessary to initiate a download of the textfile, with the specified name
        $headers = array(
            'Content-Type' => 'plain/txt',
            'Content-Disposition' => sprintf('attachment; filename="%s"', $filename),
        );

        return \Response::make($content, 200, $headers);
    }
}
 