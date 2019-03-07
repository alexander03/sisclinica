<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Validator;
use App\Http\Requests;
use App\HistoriaClinica;
use App\Historia;
use App\Trama2;
use App\Librerias\Libreria;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

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

    public function descargarZip(Request $request) {
        $za = new ZipArchive();

        $za->open('test_with_comment.zip');
        print_r($za);
        var_dump($za);
        echo "numFicheros: " . $za->numFiles . "\n";
        echo "estado: " . $za->status  . "\n";
        echo "estadosSis: " . $za->statusSys . "\n";
        echo "nombreFichero: " . $za->filename . "\n";
        echo "comentario: " . $za->comment . "\n";

        for ($i=0; $i<$za->numFiles;$i++) {
            echo "index: $i\n";
            print_r($za->statIndex($i));
        }
        echo "numFichero:" . $za->numFiles . "\n";
    }
}
