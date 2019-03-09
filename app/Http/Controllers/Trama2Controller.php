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
        if($trama == 'TAB1') {
            $elementos = Movimiento::where('clasificacionconsulta', 'C')
                        ->where('situacion', 'C')
                        ->where('situacion2', 'L')
                        ->where('sucursal_id', 1)
                        ->whereBetween('fecha_atencion', [$fechai, $fechaf])
                        ->join('historiaclinica', 'ticket_id', '=', 'movimiento.id')
                        ->join('person', 'movimiento.persona_id', '=', 'person.id')
                        ->orderBy('person.sexo', 'DESC')
                        ->orderBy('edad', 'ASC')
                        ->groupBy('edad')
                        ->select(DB::raw('YEAR(CURDATE())-YEAR(person.fechanacimiento) AS edad'), 'person.sexo', DB::raw('COUNT(movimiento.id) AS totalatenciones'))
                        ->get();
        }
        if($trama == 'TAB2') {
            $elementos = Movimiento::where('clasificacionconsulta', 'C')
                        ->where('situacion', 'C')
                        ->where('situacion2', 'L')
                        ->where('sucursal_id', 1)
                        ->whereBetween('fecha_atencion', [$fechai, $fechaf])
                        ->join('historiaclinica', 'ticket_id', '=', 'movimiento.id')
                        ->join('person', 'movimiento.persona_id', '=', 'person.id')
                        ->join('detallehistoriacie', 'detallehistoriacie.historiaclinica_id', '=', 'historiaclinica.id')
                        ->join('cie', 'cie.id', '=', 'detallehistoriacie.cie_id')
                        ->orderBy('person.sexo', 'DESC')
                        ->orderBy('edad', 'ASC')
                        ->groupBy('edad')
                        ->groupBy('cie.codigo')
                        ->select(DB::raw('YEAR(CURDATE())-YEAR(person.fechanacimiento) AS edad'), 'person.sexo', DB::raw('COUNT(movimiento.id) AS totalatenciones'), 'cie.codigo')
                        ->get();

        }
        if($trama == 'TAC1') {
            $elementos = Movimiento::where('clasificacionconsulta', 'E')
                        ->where('situacion', 'C')
                        ->where('situacion2', 'L')
                        ->where('sucursal_id', 1)
                        ->whereBetween('fecha_atencion', [$fechai, $fechaf])
                        ->join('historiaclinica', 'ticket_id', '=', 'movimiento.id')
                        ->join('person', 'movimiento.persona_id', '=', 'person.id')
                        ->orderBy('person.sexo', 'DESC')
                        ->orderBy(DB::raw('YEAR(CURDATE())-YEAR(person.fechanacimiento)'), 'ASC')
                        ->groupBy(DB::raw('YEAR(CURDATE())-YEAR(person.fechanacimiento)'))
                        ->select(DB::raw('YEAR(CURDATE())-YEAR(person.fechanacimiento) AS edad'), 'person.sexo', DB::raw('COUNT(movimiento.id) AS totalatenciones'))
                        ->get();
        }
        if($trama == 'TAC2') {
            $elementos = Movimiento::where('clasificacionconsult', 'E')
                        ->where('situacion', 'C')
                        ->where('situacion2', 'L')
                        ->where('sucursal_id', 1)
                        ->whereBetween('fecha_atencion', [$fechai, $fechaf])
                        ->join('historiaclinica', 'ticket_id', '=', 'movimiento.id')
                        ->join('person', 'movimiento.persona_id', '=', 'person.id')
                        ->join('detallehistoriacie', 'detallehistoriacie.historiaclinica_id', '=', 'historiaclinica.id')
                        ->join('cie', 'cie.id', '=', 'detallehistoriacie.cie_id')
                        ->orderBy('person.sexo', 'DESC')
                        ->orderBy(DB::raw('YEAR(CURDATE())-YEAR(person.fechanacimiento)'), 'ASC')
                        ->groupBy(DB::raw('YEAR(CURDATE())-YEAR(person.fechanacimiento)'))
                        ->groupBy('cie.codigo')
                        ->select(DB::raw('YEAR(CURDATE())-YEAR(person.fechanacimiento) AS edad'), 'person.sexo', DB::raw('COUNT(movimiento.id) AS totalatenciones'), 'cie.codigo')
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
