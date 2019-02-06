<?php
	//error_reporting(E_ALL);
	class Sunat{
		var $cc;  //Class cUrl
		var $path;
		function __construct()
		{
			$this->path = dirname(__FILE__);
			
			$this->cc = new cURL(true,'http://e-consultaruc.sunat.gob.pe/cl-ti-itmrconsruc/jcrS00Alias',$this->path.'/cookies.txt');
			//$this->cc = new cURL(true,'http://www.sunat.gob.pe/cl-ti-itmrconsruc/jcrS00Alias',$this->path.'/cookies.txt');
		}
		function ProcesaNumRand()
		{
			$data = array(
				"accion"=>"random"
			);
			     
			$url="http://e-consultaruc.sunat.gob.pe/cl-ti-itmrconsruc/captcha";
			//$url="http://www.sunat.gob.pe/cl-ti-itmrconsruc/captcha";
			
			$numRand = $this->cc->post($url,$data);
			return $numRand;
		}
		
		function BuscaDatosSunat($ruc)
		{
			$captcha = $this->ProcesaNumRand();
			$rtn = array();
			//print_r(array($ruc,$captcha));exit();
			if($ruc != "" && $captcha!=false)
			{
				$data = array(
					//"accion" => "consPorTipdoc",
					//"tipdoc" =>1, //DNI
					"nroRuc" => $ruc,
					"accion" => "consPorRuc",
					"numRnd" => $captcha
				);
				$url = "http://e-consultaruc.sunat.gob.pe/cl-ti-itmrconsruc/jcrS00Alias";
				
				//$url = "http://www.sunat.gob.pe/cl-ti-itmrconsruc/jcrS00Alias";
				$Page = $this->cc->post($url,$data);
				$Page = utf8_encode ( $Page );
				$patron='/<input type="hidden" name="desRuc" value="(.*)">/';
				$output = preg_match_all($patron, $Page, $matches, PREG_SET_ORDER);
				if(isset($matches[0]))
				{
					$RS = str_replace('"','', ($matches[0][1]));
					$rtn = array("RUC"=>$ruc,"RazonSocial"=>trim($RS));
				}
				$busca=array(
					"Direccion" 			=> "Direcci&oacute;n del Domicilio Fiscal",
				);
				//echo "TRUE";
				//print_r($Page);exit();
				//echo "\n";
				foreach($busca as $i=>$v)
				{
					$patron='/<td class="bgn" colspan=1>'.$v.':[ ]*<\/td>\r\n[ ]+<td class="bg" colspan=[1|3]+>(.*)<\/td>/';
					$output = preg_match_all($patron, $Page, $matches, PREG_SET_ORDER);
					//print_r($Page);
					//echo " - $i \n";
					if(isset($matches[0]))
					{
						$rtn[$i] = trim(preg_replace( "[\s+]"," ", ($matches[0][1]) ) );
					}
				}
			}
			return $rtn;
		}
	}
?>
