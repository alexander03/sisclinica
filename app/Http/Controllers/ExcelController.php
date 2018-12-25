<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Input;
use Excel;
use App\Historia;
use App\Person;
use App\Servicio;
use App\Tiposervicio;
use App\Tarifario;
use App\Cie;

use Illuminate\Support\Facades\DB;

class ExcelController extends Controller
{

	public function importHistoria()
    {
		return view('importHistoria');
	}

	public function downloadExcel($type)
	{
		$data = Item::get()->toArray();
		return Excel::create('itsolutionstuff_example', function($excel) use ($data) {
			$excel->sheet('mySheet', function($sheet) use ($data)
	        {
				$sheet->fromArray($data);

	        });
		})->download($type);
	}

	public function importHistoriaExcel()
	{
        ini_set('memory_limit', -1);
        ini_set('max_execution_time', 0);
		if(Input::hasFile('import_file')){
			$path = Input::file('import_file')->getRealPath();
			$data = Excel::load($path, function($reader) {

			})->get();
			if(!empty($data) && $data->count()){
			    $dat=array();
				foreach ($data as $key => $value) {
                    $dni = trim($value->dni);
                    $band=true;
                    if($dni!="00000000" && strlen($dni)==8){
                        $mdlPerson = new Person();
                        $resultado = Person::where('dni','LIKE',$dni);
                        $value2     = $resultado->first();
                        if(count($value2)>0 && strlen(trim($dni))>0){
                            $objHistoria = new Historia();
                            $list2       = Historia::where('person_id','=',$value2->id)->first();
                            if(count($list2)>0){//SI TIENE HISTORIA
                                echo "Ya tiene historia ".$value->historia." -> ".$dni;
                                $idpersona=0;
                                $dni="";
                                $band=false;
                            }else{//NO TIENE HISTORIA PERO SI ESTA REGISTRADO LA PERSONA COMO PROVEEDOR O PERSONAL
                                $idpersona=$value2->id;
                            }
                        }else{
                            $resultado = Person::where(DB::raw('concat(apellidopaterno,\' \',apellidomaterno,\' \',nombres)'), 'LIKE', '%'.strtoupper(trim($value->apellidopaterno)." ".trim($value->apellidomaterno)." ".trim($value->nombres)).'%');
                            $value2     = $resultado->first();
                            if(count($value2)>0 && strlen(trim($dni))>0){
                                $objHistoria = new Historia();
                                $list2       = Historia::where('person_id','=',$value2->id)->first();
                                if(count($list2)>0){//SI TIENE HISTORIA
                                    echo "Ya tiene historia ".$value->historia." -> ".$dni;
                                    $idpersona=0;
                                    $dni="";
                                    $band=false;
                                }else{//NO TIENE HISTORIA PERO SI ESTA REGISTRADO LA PERSONA COMO PROVEEDOR O PERSONAL
                                    $idpersona=$value2->id;
                                }
                            }else
                                $idpersona=0;
                        }        
                    }else{
                        $resultado = Person::where(DB::raw('concat(apellidopaterno,\' \',apellidomaterno,\' \',nombres)'), 'LIKE', '%'.strtoupper(trim($value->apellidopaterno)." ".trim($value->apellidomaterno)." ".trim($value->nombres)).'%');
                        $value2     = $resultado->first();
                        if(count($value2)>0){
                            $objHistoria = new Historia();
                            $list2       = Historia::where('person_id','=',$value2->id)->first();
                            if(count($list2)>0){//SI TIENE HISTORIA
                                echo "Ya tiene historia ".$value->historia." -> ".$dni;
                                $idpersona=0;
                                $dni="";
                                $band=false;
                            }else{//NO TIENE HISTORIA PERO SI ESTA REGISTRADO LA PERSONA COMO PROVEEDOR O PERSONAL
                                $idpersona=$value2->id;
                            }
                        }else
                            $idpersona=0;
                        $dni='';
                    }
                    $error = DB::transaction(function() use($dni,$idpersona,$value,&$dat,$band){
                        if($band){
                            $Historia       = new Historia();
                            if($idpersona==0){
                                $person = new Person();
                                $person->dni=$dni;
                                $person->apellidopaterno=strtoupper(trim($value->apellidopaterno));
                                $person->apellidomaterno=strtoupper(trim($value->apellidomaterno));
                                $person->nombres=trim(strtoupper($value->nombres));
                                $person->telefono=$value->telefono;
                                $person->direccion=trim($value->direccion).' - '.trim($value->distrito);
                                $person->sexo=substr($value->sexo,0,1);
                                $person->telefono=$value->telefono;
                                if($value->fechanacimiento!="")    $person->fechanacimiento=$value->fechanacimiento->format("Y-m-d");
                                $person->save();
                                $idpersona=$person->id;
                            }else{
                                $person = Person::find($idpersona);
                                $person->dni=$dni;
                                $person->apellidopaterno=strtoupper(trim($value->apellidopaterno));
                                $person->apellidomaterno=strtoupper(trim($value->apellidomaterno));
                                $person->nombres=trim(strtoupper($value->nombres));
                                $person->direccion=trim($value->direccion).' - '.trim($value->distrito);
                                $person->sexo=substr($value->sexo,0,1);
                                $person->telefono=$value->telefono;
                                if($value->fechanacimiento!="")    $person->fechanacimiento=$value->fechanacimiento->format("Y-m-d");
                                $person->save();
                                $idpersona=$person->id;
                            }
                            $Historia->numero = Historia::NumeroSigue();
                            $Historia->person_id = $idpersona;
                            /*if(trim($value->tipo_paciente)=="HOSPITAL"){
                                $tipopaciente="Hospital";
                            }elseif(trim($value->tipo_paciente)=="PARTICULAR"){*/
                                $tipopaciente="Particular";
                            /*}else{
                                $tipopaciente="Convenio";
                            }*/
                            $Historia->tipopaciente=$tipopaciente;
                            $Historia->fecha=date("Y-m-d");
                            $Historia->modo="F";
                            $Historia->estadocivil='';
                            if($tipopaciente=="Convenio"){
                                $Historia->empresa=$value->empresa;
                                $Historia->carnet=$value->carnet;
                                $Historia->poliza=$value->poliza;
                                $Historia->soat=$value->soat;
                                $Historia->titular=$value->titular;
                            }
                            $Historia->save();
                            $dat[]=array("respuesta"=>"OK","id"=>$Historia->id,"paciente"=>$person->apellidopaterno.' '.$person->apellidomaterno.' '.$person->nombres,"historia"=>$Historia->numero,"person_id"=>$Historia->person_id);
                        }            
                    });
                    if(!is_null($error)){
                        print_r($error);die();
                    }
				}
                print_r($dat);
			}
		}
		return view('importHistoria');;

	}

    public function importApellidoExcel()
    {
        ini_set('memory_limit', -1);
        ini_set('max_execution_time', 0);
        if(Input::hasFile('import_file')){
            $path = Input::file('import_file')->getRealPath();
            $data = Excel::load($path, function($reader) {

            })->get();
            if(!empty($data) && $data->count()){
                $dat=array();
                foreach ($data as $key => $value) {
                    $error = DB::transaction(function() use($value,&$dat){
                        $Historia       = Historia::where('numero','like',$value->historia)->first();
                        if(count($Historia)>0){//SI TIENE HISTORIA
                            $nom=explode(" ",$value->paciente);
                            $nombres="";
                            if(isset($nom[1])){
                                $person = Person::find($Historia->person_id);
                                $person->apellidomaterno=trim(strtoupper($nom[1]));
                                $person->save();
                                $idpersona=$person->id;
                                $dat[]=array("respuesta"=>"OK","id"=>$Historia->id,"paciente"=>$person->apellidopaterno.' '.$person->apellidomaterno.' '.$person->nombres,"historia"=>$Historia->numero,"person_id"=>$Historia->person_id);
                            }else{
                                echo "No tiene apellido Nro:".$value->historia."|";
                            }
                        }else{
                            echo "No existe historia migrada Nro:".$value->historia."|";
                        }
                    });
                    if(!is_null($error)){
                        print_r($error);die();
                    }
                }
                print_r($dat);
            }
        }
        return view('importHistoria');;

    }

    public function importTarifario()
    {
        ini_set('memory_limit', -1);
        ini_set('max_execution_time', 0);
        if(Input::hasFile('import_file')){
            $path = Input::file('import_file')->getRealPath();
            $data = Excel::load($path, function($reader) {

            })->get();
            if(!empty($data) && $data->count()){
                $dat=array();
                foreach ($data as $key => $value) {
                    $error = DB::transaction(function() use($value,&$dat){
                        $plan_id=12;
                        $servicio       = Servicio::join('tarifario','tarifario.id','=','servicio.tarifario_id')
                                            ->where('servicio.plan_id','=',$plan_id)
                                            ->where('servicio.tipopago','like','Convenio')
                                            ->where('tarifario.codigo','like',str_pad($value->codigo,6,'0',STR_PAD_LEFT))
                                            ->select('servicio.*')
                                            ->first();
                        if(count($servicio)>0){
                            $servicio = Servicio::find($servicio->id);
                            $servicio->precio=round($value->plan*1.18,2);
                            $servicio->factor=4.3;
                            $servicio->save();
                            $dat[]=array("respuesta"=>"ACTUALIZADO","id"=>$servicio->id,"descripcion"=>$servicio->nombre);
                        }else{
                            $tarifario = Tarifario::where('codigo','like',str_pad($value->codigo,6,'0',STR_PAD_LEFT))->first();
                            if(count($tarifario)>0){
                                $servicio = new Servicio();
                                $servicio->precio=round($value->plan*1.18,2);
                                $servicio->plan_id=$plan_id;
                                $servicio->tipopago='Convenio';
                                $servicio->pagohospital=round($value->plan*1.18,2);
                                $servicio->pagodoctor=0;
                                $servicio->modo='Monto';
                                $servicio->tarifario_id=$tarifario->id;
                                $servicio->nombre = $tarifario->nombre;
                                $servicio->factor=4.3;
                                $tipo = Servicio::join('tarifario','tarifario.id','=','servicio.tarifario_id')
                                                ->where('servicio.tipopago','like','Convenio')
                                                ->where('tarifario.codigo','like',str_pad($value->codigo,6,'0',STR_PAD_LEFT))
                                                ->first();
                                $servicio->tiposervicio_id=$tipo->tiposervicio_id;
                                $servicio->save();
                                $dat[]=array("respuesta"=>"NUEVO","id"=>$servicio->id,"descripcion"=>$servicio->nombre);
                            }else{
                                $dat[]=array("respuesta"=>"NO EXISTE","id"=>0,"descripcion"=>$value->codigo);
                            }
                        }
                    });
                    if(!is_null($error)){
                        print_r($error);die();
                    }
                }
                print_r($dat);
            }else{
                print_r("No tiene datos");
            }
        }
        return view('importHistoria');;

    }

    public function importCie()
    {
        ini_set('memory_limit', -1);
        ini_set('max_execution_time', 0);
        if(Input::hasFile('import_file')){
            $path = Input::file('import_file')->getRealPath();
            $data = Excel::load($path, function($reader) {

            })->get();
            if(!empty($data) && $data->count()){
                $dat=array();
                foreach ($data as $key => $value) {
                    $error = DB::transaction(function() use($value,&$dat){
                        $Cie       = Cie::where('codigo','like',str_replace('.', '', $value->codigo))->first();
                        if(count($Cie)>0){//SI TIENE HISTORIA
                            $Cie->codigo=$value->codigo;
                            $Cie->descripcion=$value->diagnostico;
                            $Cie->save();
                            $dat[]=array("respuesta"=>"ACTUALIZADO","descripcion"=>$value->codigo);
                        }else{
                            $Cie = new Cie();
                            $Cie->codigo=$value->codigo;
                            $Cie->descripcion=$value->diagnostico;
                            $Cie->save();
                            $dat[]=array("respuesta"=>"NUEVO","descripcion"=>$value->codigo);
                        }
                    });
                    if(!is_null($error)){
                        print_r($error);die();
                    }
                }
                print_r($dat);
            }
        }
        return view('importHistoria');;

    }

    public function importServicio()
    {
        ini_set('memory_limit', -1);
        ini_set('max_execution_time', 0);
        if(Input::hasFile('import_file')){
            $path = Input::file('import_file')->getRealPath();
            $data = Excel::load($path, function($reader) {

            })->get();
            if(!empty($data) && $data->count()){
                $dat=array();
                foreach ($data as $key => $value) {
                    $error = DB::transaction(function() use($value,&$dat){
                        $servicio = new Servicio();
                        $servicio->nombre = strtoupper($value->descripcion);
                        $tipo = Tiposervicio::where('nombre','like',trim($value->tiposervicio))->first();
                        if(!is_null($tipo)){
                            $servicio->tiposervicio_id = $tipo->id;
                        }else{
                            $servicio->tiposervicio_id = 1;
                        }
                        $servicio->tipopago = 'Particular';
                        $servicio->precio = str_replace(",","",$value->precio);
                        $servicio->modo = 'Monto';
                        $servicio->pagohospital = str_replace(",","",$value->precio);
                        $servicio->pagodoctor = 0;
                        $servicio->save();
                        $dat[]=array("respuesta"=>"NUEVO","descripcion"=>$value->descripcion);
                    });
                    if(!is_null($error)){
                        print_r($error);die();
                    }
                }
                print_r($dat);
            }
        }
        return view('importHistoria');;

    }
}