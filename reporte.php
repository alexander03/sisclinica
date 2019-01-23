<?php 
	
	define('HOST','localhost');
	define('DBNAME','clinica');
	define('USER','root');
	define('PASS','');

	require('fpdf/fpdf.php');
	require('excel/PHPExcel.php');

	class PDF extends FPDF{
		function Header()
		{
			// 1
			$this->Image('dist/img/logo2.jpg',7,7,20,15);
			// Arial bold 15
			$this->SetFont('Arial','B',12);
			// Movernos a la derecha
			//$this->Cell(65);
			// Título
			$this->SetFont('Arial','B',15);
			$this->SetTextColor(7,126,128);
			$or = $this->CurOrientation;
			if ($or == 'P') {
				$this->Cell(60,3,'',0,0,'C');
			} else {
				$this->Cell(60,3,'',0,0,'C');
			}
			// Salto de línea
			$this->Ln(7);
		}

		// Pie de página
		function Footer()
		{
			// Posición: a 1,5 cm del final
			$this->SetY(-15);
			// Arial italic 8
			$this->SetFont('Arial','I',8);
			// Número de página
			$this->Cell(0,10,utf8_decode('Página '.$this->PageNo().'/{nb}'),0,0,'C');
		}
	}

	class Conexion extends mysqli{
	    public function __construct(){
	        parent::__construct(HOST,USER,PASS,DBNAME);
	        $this->query("SET NAMES utf8;");
	        $this->connect_errno ? die('ERROR: Datos incorrectos en la Conexion') : null;
	    }

	    public function rows($x){
	        return mysqli_num_rows($x);
	    }

	    public function recorrer($x){
	        return mysqli_fetch_array($x);
	    }

	    public function liberar($x){
	        return mysqli_free_result($x);
	    }
	    public function recorrerobj($x){
		       return mysqli_fetch_object($x);
	    }
	}

	if (isset($_GET['rep'])) {
		$fi = $_GET['fi'];
		$ff = $_GET['ff'];
		$rep = $_GET['rep'];
		switch ($rep) {
			case 1:
				rMensajes($fi,$ff);
				break;
			
			case 2:
				$med = $_GET['med'];
				$sala = $_GET['sala'];
				rSalaOpe($fi,$ff,$med,$sala);
				break;
			case 221:
				$med = $_GET['med'];
				$sala = $_GET['sala'];
				rSalaOpeE($fi,$ff,$med,$sala);
				break;

			case 3:
				$med = $_GET['med'];
				$tipo = $_GET['tipo'];
				rHospM($fi,$ff,$med,$tipo);
				break;
			
			case 4:
				$med = $_GET['med'];
				$nmed = $_GET['nmed'];
				$mmed = $_GET['mmed'];
				$nombres = $_GET['nombres'];
				$tipo = $_GET['tipo'];
				rHospP($fi,$ff,$med,$nmed,$mmed,$nombres,$tipo);
				break;

			case 5:
				rHospG($fi,$ff);
				break;
			
			case 6:
				$med = $_GET['med'];
				rCaja($fi,$ff,$med);
				break;
			case 61:
				rfCaja($fi,$ff);
				break;
			case 7:
				$med = $_GET['med'];
				rPagosE($fi,$ff,$med);
				break;

			case 8:
				$med = $_GET['med'];
				rPagosC($fi,$ff,$med);
				break;

			case 9:
				$med = $_GET['med'];
				rPagosC($fi,$ff,$med);
				break;

			case 10:
				$med = $_GET['med'];
				rPagosME($fi,$ff,$med);
				break;

			case 11:
				$med = $_GET['med'];
				rPagosMC($fi,$ff,$med);
				break;

			case 12:
				$med = $_GET['med'];
				rConvenios($fi,$ff,$med);
				break;
			case 13:
				$tipo = $_GET['tipo'];
				isset($_GET['med'])?$pro = $_GET['med']:$pro = '';
				rCompras($fi,$ff,$tipo,$pro);
				break;
			case 131:
				$med = $_GET['tipo'];
				isset($_GET['med'])?$pro = $_GET['med']:$pro = '';
				rComprasE($fi,$ff,$med,$pro);
			case 14:
				$med = $_GET['tipo'];
				isset($_GET['med'])?$pro = $_GET['med']:$pro = '';
				rVentas($fi,$ff,$med,$pro);
				break;
			case 141:
				$med = $_GET['tipo'];
				isset($_GET['med'])?$pro = $_GET['med']:$pro = '';
				rVentasE($fi,$ff,$med,$pro);
				break;
			case 142:
				$med = $_GET['tipo'];
				isset($_GET['med'])?$pro = $_GET['med']:$pro = '';
				rMovAlma($fi,$ff,$med,$pro);
				break;
			case 15:
				rNotaCredito($fi,$ff);
				break;
			case 16:
				$min = $_GET['min'];
				$origen = $_GET['origen'];
				rStock($min,$origen);
				break;
			case 17:
				rProveedores();
				break;
			case 18:
				rMedicaConv($fi,$ff);
				break;
			case 19:
				$p_id = $_GET['p_id'];
				rKardex($fi,$ff,$p_id);
				break;
			case 20:
				rPedido($fi,$ff);
				break;
			case 21:
				$med = $_GET['med'];
				$tipo = $_GET['tipo'];
				$origen = $_GET['origen'];
				$presentacion = $_GET['presentacion'];
				rProductos($med,$tipo,$origen,$presentacion);
				break;
			case 211:
				$med = $_GET['med'];
				$tipo = $_GET['tipo'];
				$origen = $_GET['origen'];
				$presentacion = $_GET['presentacion'];
				rProductosE($med,$tipo,$origen,$presentacion);
				break;
			case 22:
				$tipo = $_GET['tipo'];
				$filtro = $_GET['filtro'];
				$limite = $_GET['limite'];
				$palabra = $_GET['palabra'];
				$orden = $_GET['orden'];
				$origen = $_GET['origen'];
				rMasVen($fi,$ff,$tipo,$filtro,$limite,$palabra,$orden,$origen);
				break;
			case 23:
				$tipo = $_GET['tipo'];
				isset($_GET['indicio'])?$indicio = $_GET['indicio']:$indicio='';
				rGuiasconvenio($fi,$ff,$tipo,$indicio);
				break;
			case 24:
				rKardexEE();
				break;
			case 25:
				$lab = $_GET['lab'];
				$presentacion = $_GET['presentacion'];
				$origen = $_GET['origen'];
				rInventarioV($ff, $lab, $origen, $presentacion);
				break;
			case 251:
				rInventarioVE();
				break;

			case 26:
				$tipo = $_GET['tipo'];
				rFacCon($fi,$ff,$tipo);
				break;

			case 261:
				$tipo = $_GET['tipo'];
				rFacConP($fi,$ff,$tipo);
				break;

			case 27:
				$lab = $_GET['lab'];
				$presentacion = $_GET['presentacion'];
				$origen = $_GET['origen'];
				rInicial($lab, $origen, $presentacion);
				break;

			case 28:
				rFallecidos($fi,$ff);
				break;

			case 281:
				rFallecidosE($fi,$ff);
				break;

			case 29:
				$med = $_GET['med'];
				$rubro = $_GET['rubro'];
				rConsultaPagos($fi,$ff,$med,$rubro);
				break;

			case 291:
				$med = $_GET['med'];
				$rubro = $_GET['rubro'];
				rConsultaPagosE($fi,$ff,$med,$rubro);
				break;

			case 30:
				$med = $_GET['med'];
				$tipo = $_GET['tipo'];
				rHospME($fi,$ff,$med,$tipo);
				break;

			case 31:
				$filtro = $_GET['filtro'];
				$extra = $_GET['servicio'];
				$paciente = $_GET['paciente'];
				rMovimientosE($fi,$ff,$filtro,$extra,$paciente);
				break;
			
			case 32:
				rHistoriasE($fi,$ff);
				break;

			case 33:
				rCajaUsuariosE($fi,$ff);
				break;

			case 34:
				rMovimientosMed($fi,$ff);
				break;

			case 35:
				rHosU($fi,$ff);
				break;

			case 36:
				rAnuladas($fi,$ff);
				break;

			case 37:
				rCreditoPersonal($fi,$ff);
				break;

			case 38:
				$p_id = $_GET['p_id'];
				rKardexCo($fi,$ff,$p_id);
				break;
		}
	}

	function rKardexCo($fi,$ff,$p_id){
        $response = '';
        $db = new Conexion();

        $pdf = new PDF('L','mm','A4');
        $pdf->AliasNbPages();
        $pdf->AddPage();

        $pdf->SetFillColor(255,255,255);
        $pdf->SetFont('Arial','B',15);

        $query=$db->query("SELECT p.nombre, r.nombre proveedor FROM producto p join laboratorio r on p.laboratorio_id = r.id where p.id = '$p_id' ");

		while($rows=$db->recorrerobj($query)){
			$producto = $rows->nombre;
			$provee = $rows->proveedor;
		}
		if (!isset($provee)) {
			$provee = 'NO REGISTRADO';
			 $query=$db->query("SELECT p.nombre FROM producto p where p.id = '$p_id' ");

			while($rows=$db->recorrerobj($query)){
				$producto = $rows->nombre;
			}
		}

        $pdf->Cell(0,6,utf8_decode('REPORTE DE KARDEX DE PRODUCTOS'),0,0,'C',0);
        $pdf->Ln();
        $pdf->SetFont('Arial','B',10);
        $pdf->Cell(30,5,utf8_decode('CODIGO: '),0,0,'L',1);
        $pdf->SetFont('Arial','',10);
        $pdf->Cell(50,5,utf8_decode($producto),0,0,'L',1);
        $pdf->Ln();
        $pdf->SetFont('Arial','B',10);
        $pdf->Cell(30,5,utf8_decode('DESCRIPCION: '),0,0,'L',1);
        $pdf->SetFont('Arial','',10);
        $pdf->Cell(50,5,utf8_decode($producto),0,0,'L',1);
        $pdf->Ln();
        $pdf->SetFont('Arial','B',10);
        $pdf->Cell(30,5,utf8_decode('LABORATORIO: '),0,0,'L',1);
        $pdf->SetFont('Arial','',10);
        $pdf->Cell(50,5,utf8_decode($provee),0,0,'L',1);
        $pdf->Ln();

        //$pdf->SetFillColor(68,164,168);
        $pdf->SetFont('Arial','B',10);
        //$pdf->Cell(8,6,'#',1,0,'C',1);

		//multi                
        $pdf->Cell(85,5,'DOCUMENTO DE TRASLADO, COMPROBANTE','LTR',0,'C',0);
        $pdf->Cell(25,5,'TIPO DE','LTR',0,'C',0);
        $pdf->Cell(57,5,'','LTR',0,'C',0);
        $pdf->Cell(57,5,'','LTR',0,'C',0);
        $pdf->Cell(57,5,'','LTR',0,'C',0);
        $pdf->Ln();
        //
        $pdf->Cell(85,5,'DE PAGO, DOCUMENTO INTERNO O SIMILAR','LBR',0,'C',0);
        $pdf->cell(25,5,'DE','LR',0,'C',0);
        $pdf->Cell(57,5,'ENTRADAS','LBR',0,'C',0);
        $pdf->Cell(57,5,'SALIDAS','LBR',0,'C',0);
        $pdf->Cell(57,5,'SALDO FINAL','LBR',0,'C',0);
        $pdf->Ln();
        //$pdf->SetY($pdf->GetY() - 4);
        $pdf->Cell(20,5,'FECHA',1,0,'C',1);
        $pdf->Cell(18,5,'HORA',1,0,'C',1);
        $pdf->Cell(12,5,'TIPO',1,0,'C',1);
        $pdf->Cell(15,5,'SERIE',1,0,'C',1);
        $pdf->Cell(20,5,'NUMERO',1,0,'C',1);
        //
        $pdf->Cell(25,5,'OPERACION','LBR',0,'C',0);
        $pdf->Cell(19,5,'CANT.',1,0,'C',0);
        $pdf->Cell(19,5,'CO. UNIT.',1,0,'C',0);
        $pdf->Cell(19,5,'TOTAL',1,0,'C',0);
        $pdf->Cell(14,5,'CANT.',1,0,'C',0);
        $pdf->Cell(14,5,'C. UNIT',1,0,'C',0);
        $pdf->Cell(14,5,'P. UNIT',1,0,'C',0);
        $pdf->Cell(15,5,'TOTAL',1,0,'C',0);
        $pdf->Cell(19,5,'CANT.',1,0,'C',0);
        $pdf->Cell(19,5,'CO. UNIT.',1,0,'C',0);
        $pdf->Cell(19,5,'TOTAL',1,0,'C',0);

        $pdf->SetFont('Arial','',9);
        //$pdf->SetFillColor(250,250,250);
        $pdf->Ln();
        $entradas = 0;
        $salidas = 0;
        $contador = 0;
		$sumtar = 0;
		$objetivo = array();
		$precioob = array();

        //SELECT * FROM detallemovimiento d join kardex k on d.id = k.detallemovimiento_id WHERE k.lote_id = '$lote'
        $num = 1;
        $query=$db->query("SELECT k.fecha, TIME(m.created_at) hora, m.situacion, k.cantidad, k.tipo, k.stockactual, p.nombre producto, u.nombres usuario, m.tipomovimiento_id, m.serie, m.numero, m.tipodocumento_id, dm.precio preciop, dm.subtotal ctotal FROM kardex k join detallemovimiento dm on k.detallemovimiento_id = dm.id join movimiento m on dm.movimiento_id = m.id join person u on u.id = m.responsable_id JOIN producto p on dm.producto_id = p.id where m.situacion = 'N' and m.deleted_at is null and p.id = '".$p_id."' AND k.fecha BETWEEN '".$fi."' AND '".$ff."' order by k.fecha, hora asc");
		while($rows=$db->recorrerobj($query)){
			//26 PRIMER CONTEO
			//32 SEGUNDO CONTEO
		    $tipodo = $rows->tipomovimiento_id;
		    $tipodo == 4 ? $tipodo = '03' : $tipodo = '05';
		    $tipo = $rows->tipomovimiento_id;
		    $tipokardex = $rows->tipo;
       		$stof=$rows->stockactual;
       		$situacion=$rows->situacion;
       		$preciop=$rows->preciop;
       		$ctotal=$rows->ctotal;

		    $fecha = date_format(date_create($rows->fecha),"d/m/Y");
		    $hora = $rows->hora;
		    $tipooper = '01';
		    $cantidad = $rows->cantidad;
		    
		    $serie = str_pad($rows->serie, 4, "0", STR_PAD_LEFT);
		    $numero = str_pad($rows->numero, 7, "0", STR_PAD_LEFT);;
		    $usuario = $rows->usuario;
		    $tdocumento = $rows->tipodocumento_id;
		    $tipom = $tipo;
		    if($tipo != 4){
		    	if ($tipo == 5) {
		    		if ($tipokardex == 'I') {
		    			$tipo = 'INGRESO';
			    		$entradas += $cantidad;
			    		$egreso = '-';
			    		$ingreso = number_format($cantidad);
			    	} else {
			    		$tipo = 'SALIDA';
			    		$ingreso = '-';
			    		$salidas += $cantidad;
			    		$egreso = number_format($cantidad);
			    	}
		    	} else if ($tipo == 3){
		    		$tipo = 'INGRESO';
		    		$entradas+=$cantidad;
		    		$egreso = '-';
		    		$ingreso = number_format($cantidad);
		    	} else if ($tipo == 6){
		    		if ($tipokardex == 'I') {
		    			$tipo = 'INGRESO';
			    		$entradas += $cantidad;
			    		$egreso = '-';
			    		$ingreso = number_format($cantidad);
			    	} else {
			    		$tipo = 'SALIDA';
			    		$ingreso = '-';
			    		$salidas += $cantidad;
			    		$egreso = number_format($cantidad);
			    	}
		    	}
		    } else {
		    	$tipooper = '01';
		    	$tipo = 'SALIDA'; $salidas+=$cantidad;
		    	$egreso = number_format($cantidad);
		    	$ingreso = '-';
		    }

		    if ($tipodo == "05") {
		    	array_push($objetivo,$cantidad);
		    	array_push($precioob,$preciop);
		    	$pdf->Cell(85,5,"SALDO INICIAL",1,0,'C',1);
		    	$tipooper = '16';
		    	$pdf->Cell(25,5,$tipooper,1,0,'C',1);
		        $pdf->Cell(19,5,number_format($cantidad),1,0,'C',1);
		        $pdf->Cell(19,5,number_format($preciop,2,'.',''),1,0,'C',1);
		        $pdf->Cell(19,5,$ctotal,1,0,'C',1);
		        $pdf->Cell(14,5,"",1,0,'C',1);
		        $pdf->Cell(14,5,"",1,0,'C',1);
		        $pdf->Cell(14,5,"",1,0,'C',1);
		        $pdf->Cell(15,5,"",1,0,'C',1);
		    } else {
		    	$sumtar += $cantidad;
		    	$pdf->Cell(20,5,$fecha,1,0,'C',1);
			    $pdf->Cell(18,5,$hora,1,0,'C',1);
			    $pdf->Cell(12,5,$tipodo,1,0,'C',1);
			    $pdf->Cell(15,5,$serie,1,0,'C',1);
			    $pdf->Cell(20,5,$numero,1,0,'C',1);
			    $pdf->Cell(25,5,$tipooper,1,0,'C',1);
		        $pdf->Cell(19,5,"",1,0,'C',1);
		        $pdf->Cell(19,5,"",1,0,'C',1);
		        $pdf->Cell(19,5,"",1,0,'C',1);
		        $pdf->Cell(14,5,number_format($cantidad),1,0,'C',1);
		        if ($sumtar >= $objetivo[0]) {
		        	if (isset($objetivo[1])) {
		        		array_splice($objetivo,0,1);
		    			array_splice($precioob,0,1);
		        	}
		    	}
		    	$pdf->Cell(14,5,number_format($precioob[0],2,'.',''),1,0,'C',1);	
		        $pdf->Cell(14,5,number_format($preciop,2,'.',''),1,0,'C',1);
		        $pdf->Cell(15,5,$ctotal,1,0,'C',1);
		    }
		    $pdf->Cell(19,5,number_format($stof),1,0,'C',1);
	        $pdf->Cell(19,5,number_format($preciop,2,'.',''),1,0,'C',1);
	        $pdf->Cell(19,5,number_format($stof*$preciop,2,'.',''),1,0,'C',1);
	        $contador++;

			$pdf->Ln();
			$num++;
		}

        $pdf->Ln();
     	//$pdf->Cell(50);
	    //$pdf->Cell(30,6,'Ingresos:',1,0,'C',1);
        //$pdf->Cell(35,6,$entradas,1,0,'C',1);
        //$pdf->Ln();
        //$pdf->Cell(50);
        //$pdf->Cell(30,6,'Egresos:',1,0,'C',1);
        //$pdf->Cell(35,6,$salidas,1,0,'C',1);
        //$pdf->Ln();
        //$pdf->Cell(50);
        //$pdf->SetFillColor(150,150,150);
        //$pdf->Cell(30,6,'STOCK:',1,0,'C',1);
        //$pdf->Cell(35,6,$entra-$sale,1,0,'C',1);
			  
        $pdf->Output();
    }

	function rKardexCoE(){
		$db = new Conexion();
		error_reporting(E_ALL);
		ini_set('display_errors', TRUE);
		ini_set('display_startup_errors', TRUE);
		if (PHP_SAPI == 'cli')
			die('This example should only be run from a Web Browser');
		/** Include PHPExcel */
		// Create new PHPExcel object
		$objPHPExcel = new PHPExcel();
		// Set document properties
		$objPHPExcel->getProperties()->setCreator("Garzatec")
									 ->setLastModifiedBy("Garzatec")
									 ->setTitle("Reporte de Kardex XLS")
									 ->setSubject("Reporte de Kardex")
									 ->setDescription("Reporte generado para Office 2007 XLSX.")
									 ->setKeywords("office 2007 openxml php")
									 ->setCategory("Test report file");
		
		$objPHPExcel->setActiveSheetIndex(0)
					->mergeCells('I2:K2')
		            ->setCellValue('I2', 'REPORTE DE KARDEX DE PRODUCTOS')
		            ->setCellValue('A3', 'FECHA')
		            ->setCellValue('B3', 'HORA')
		            ->setCellValue('C3', 'CANTIDAD')
		            ->setCellValue('D3', 'TIPO')
		            ->setCellValue('E3', 'STOCK ACTUAL')
		            ->setCellValue('F3', 'detallemovimiento_id')
		            ->setCellValue('G3', 'movimiento_numero');

		$query=$db->query("SELECT k.fecha, TIME(k.created_at) hora, k.cantidad, k.tipo, k.lote_id, k.stockactual tdocument, k.detallemovimiento_id, movimiento.numero movimiento FROM kardex k join detallemovimiento dm on dm.id = k.detallemovimiento_id join movimiento on movimiento.id = dm.movimiento_id where k.FECHA BETWEEN '2017-12-24' and '2017-12-26' order by k.fecha, hora asc");

		$fila = 4;
		while($rows=$db->recorrerobj($query)){
		    $fecha = $rows->fecha;
		    $hora = $rows->hora;
		    $cantidad = $rows->cantidad;
		    $tipo = $rows->tipo;
		    $total = $rows->tdocument;
		    $detallemovimiento_id = $rows->detallemovimiento_id;
		    $numero = $rows->movimiento;

		    $objPHPExcel->setActiveSheetIndex(0)
		            ->setCellValue('A'.$fila, $fecha)
		            ->setCellValue('B'.$fila, $hora)
		            ->setCellValue('C'.$fila, $cantidad)
		            ->setCellValue('D'.$fila, $tipo)
		            ->setCellValue('E'.$fila, $total)
		            ->setCellValue('F'.$fila, $detallemovimiento_id)
		            ->setCellValue('G'.$fila, $numero);
		    $fila++;
		}

		
		// Rename worksheet
		$objPHPExcel->getActiveSheet()->setTitle('Kardex');
		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		$objPHPExcel->setActiveSheetIndex(0);
		// Redirect output to a client’s web browser (Excel5)
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="Kardex.xls"');
		header('Cache-Control: max-age=0');
		// If you're serving to IE 9, then the following may be needed
		header('Cache-Control: max-age=1');
		// If you're serving to IE over SSL, then the following may be needed
		header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
		header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
		header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
		header ('Pragma: public'); // HTTP/1.0
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
		exit;
	}

	function rCreditoPersonal($fi,$ff){
		$db = new Conexion();
		error_reporting(E_ALL);
		ini_set('display_errors', TRUE);
		ini_set('display_startup_errors', TRUE);
		if (PHP_SAPI == 'cli')
			die('This example should only be run from a Web Browser');
		/** Include PHPExcel */
		// Create new PHPExcel object
		$objPHPExcel = new PHPExcel();
		// Set document properties
		$objPHPExcel->getProperties()->setCreator("Garzatec")
									 ->setLastModifiedBy("Garzatec")
									 ->setTitle("Reporte de Credito Personal XLS")
									 ->setSubject("Reporte de Credito Personal")
									 ->setDescription("Reporte generado para Office 2007 XLSX.")
									 ->setKeywords("office 2007 openxml php")
									 ->setCategory("Test report file");
		
		$objPHPExcel->setActiveSheetIndex(0)
		            ->setCellValue('A1', 'FECHA')
		            ->setCellValue('B1', 'TIPO DOC')
		            ->setCellValue('C1', 'NUM DOC')
		            ->setCellValue('D1', 'PACIENTE')
		            ->setCellValue('E1', 'PERSONAL')
		            ->setCellValue('F1', 'TOTAL')
		            ->setCellValue('G1', 'SITUACION');

		$fila = 2;
		//CON ESTA CONSULTA FIGURA DATA DE LOS DE CONVENIO PERO NO PARTICULARES, PARA PARTICULARES SE NECESITARÌA OTRO SQL
		$query=$db->query("SELECT m.fecha, m.estadopago, m.situacion, paciente.nombres, paciente.apellidopaterno, paciente.apellidomaterno, td.nombre tipodocumento, m.serie serie, m.numero numero, m.subtotal, m.igv, m.total, CONCAT(personal.apellidopaterno, ' ', personal.apellidomaterno, ' ',personal.nombres) as personaln FROM movimiento m LEFT JOIN person paciente on paciente.id = m.persona_id LEFT JOIN person personal on personal.id = m.personal_id LEFT JOIN tipodocumento td on td.id = m.tipodocumento_id WHERE m.situacion != 'U' AND m.serie IS NOT NULL AND m.personal_id IS NOT NULL AND m.fecha BETWEEN '$fi' and '$ff' order by m.id asc");

		while($rows=$db->recorrerobj($query)){
		    $fecha = date('d/m/Y',strtotime($rows->fecha));
		    $paciente = $rows->apellidopaterno.' '.$rows->apellidomaterno.' '.$rows->nombres;
		    $tdoc = $rows->tipodocumento;
		    $tdoc == 'BOLETA DE VENTA' ? $doc = "B" : $doc = "F";
		    $numero = $rows->serie.'-'.$rows->numero;
		    $personal = $rows->personaln;
		    $subtotal = $rows->subtotal;
		    $situacion = $rows->situacion;
		    $situacion == 'P' ? $sitres = 'PENDIENTE' : $sitres = 'NORMAL';

		  	$igv = $rows->igv;
		    $total = $rows->total;

		    $objPHPExcel->setActiveSheetIndex(0)
		            ->setCellValue('A'.$fila, $fecha)
		            ->setCellValue('B'.$fila, $tdoc)
		            ->setCellValue('C'.$fila, $doc."".$numero)
		            ->setCellValue('D'.$fila, $paciente)
		            ->setCellValue('E'.$fila, $personal)
		            ->setCellValue('F'.$fila, $total)
		            ->setCellValue('G'.$fila, $sitres);
		    $fila++;
		}

		$query=$db->query("SELECT m.fecha, m.estadopago, m.situacion, paciente.nombres, paciente.apellidopaterno, paciente.apellidomaterno, td.nombre tipodocumento, m.serie serie, m.numero numero, m.subtotal, m.igv, m.total, CONCAT(personal.apellidopaterno, ' ', personal.apellidomaterno, ' ',personal.nombres) as personaln FROM movimiento m LEFT JOIN person paciente on paciente.id = m.persona_id LEFT JOIN person personal on personal.id = m.personal_id LEFT JOIN tipodocumento td on td.id = m.tipodocumento_id WHERE m.situacion != 'U' AND m.serie IS NOT NULL AND paciente.workertype_id IS NOT NULL AND m.fecha BETWEEN '$fi' and '$ff' order by m.id asc");

		while($rows=$db->recorrerobj($query)){
		    $fecha = date('d/m/Y',strtotime($rows->fecha));
		    $paciente = $rows->apellidopaterno.' '.$rows->apellidomaterno.' '.$rows->nombres;
		    $tdoc = $rows->tipodocumento;
		    $tdoc == 'BOLETA DE VENTA' ? $doc = "B" : $doc = "F";
		    $numero = $rows->serie.'-'.$rows->numero;
		    $personal = $rows->personaln;
		    $subtotal = $rows->subtotal;
		    $situacion = $rows->situacion;
		    $situacion == 'P' ? $sitres = 'PENDIENTE' : $situacion == 'A' ? $sitres = 'NOTA CREDITO' : $sitres = 'PAGADO';

		  	$igv = $rows->igv;
		    $total = $rows->total;

		    $objPHPExcel->setActiveSheetIndex(0)
		            ->setCellValue('A'.$fila, $fecha)
		            ->setCellValue('B'.$fila, $tdoc)
		            ->setCellValue('C'.$fila, $doc."".$numero)
		            ->setCellValue('D'.$fila, $paciente)
		            ->setCellValue('E'.$fila, $personal)
		            ->setCellValue('F'.$fila, $total)
		            ->setCellValue('G'.$fila, $sitres);
		    $fila++;
		}

		// Rename worksheet
		$objPHPExcel->getActiveSheet()->setTitle('CreditoPersonal');
		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		$objPHPExcel->setActiveSheetIndex(0);
		// Redirect output to a client’s web browser (Excel5)
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="CreditoPersonal.xls"');
		header('Cache-Control: max-age=0');
		// If you're serving to IE 9, then the following may be needed
		header('Cache-Control: max-age=1');
		// If you're serving to IE over SSL, then the following may be needed
		header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
		header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
		header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
		header ('Pragma: public'); // HTTP/1.0
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
		exit;
	}

	function rAnuladas($fi,$ff){
		$db = new Conexion();
		error_reporting(E_ALL);
		ini_set('display_errors', TRUE);
		ini_set('display_startup_errors', TRUE);
		if (PHP_SAPI == 'cli')
			die('This example should only be run from a Web Browser');
		/** Include PHPExcel */
		// Create new PHPExcel object
		$objPHPExcel = new PHPExcel();
		// Set document properties
		$objPHPExcel->getProperties()->setCreator("Garzatec")
									 ->setLastModifiedBy("Garzatec")
									 ->setTitle("Reporte de Anulados XLS")
									 ->setSubject("Reporte de Anulados")
									 ->setDescription("Reporte generado para Office 2007 XLSX.")
									 ->setKeywords("office 2007 openxml php")
									 ->setCategory("Test report file");
		
		$objPHPExcel->setActiveSheetIndex(0)
		            ->setCellValue('A1', 'FECHA')
		            ->setCellValue('B1', 'PACIENTE')
		            ->setCellValue('C1', 'DOC')
		            ->setCellValue('D1', 'NUMERO')
		            ->setCellValue('E1', 'CLIENTE')
		            ->setCellValue('F1', 'RUC')
		            ->setCellValue('G1', 'CONDICION')
		            ->setCellValue('H1', 'SUBTOTAL')
		            ->setCellValue('I1', 'IGV')
		            ->setCellValue('J1', 'TOTAL');

		$fila = 2;
		//CON ESTA CONSULTA FIGURA DATA DE LOS DE CONVENIO PERO NO PARTICULARES, PARA PARTICULARES SE NECESITARÌA OTRO SQL
		$query=$db->query("SELECT m.fecha, paciente.nombres, paciente.apellidopaterno, paciente.apellidomaterno, td.nombre tipodocumento, m.serie serie, m.numero numero, e.nombre aseguradora, e.ruc, m.tarjeta, m.subtotal, m.igv, m.total FROM movimiento m LEFT JOIN person paciente on paciente.id = m.persona_id LEFT JOIN plan as e on m.plan_id = e.id LEFT JOIN tipodocumento td on td.id = m.tipodocumento_id WHERE m.situacion = 'U' AND m.serie IS NOT NULL AND m.fecha BETWEEN '$fi' and '$ff' order by m.id asc");

		while($rows=$db->recorrerobj($query)){
		    $fecha = $rows->fecha;
		    $paciente = $rows->apellidopaterno.' '.$rows->apellidomaterno.' '.$rows->nombres;
		    $doc = $rows->tipodocumento;
		    $numero = $rows->serie.'-'.$rows->numero;
		    $bussinesname = $rows->aseguradora;
		    if ($bussinesname == 'TARIFA-PARTICULAR 1') {
		    	$ruc = "-";
		    } else {
		    	$ruc = $rows->ruc;
		    }
		    $tarjeta = $rows->tarjeta;
		    if ($tarjeta == NULL) {
		    	$tarjeta = 'CONTADO';
		    } else {
		    	$tarjeta = 'TARJETA';
		    }
		    $subtotal = $rows->subtotal;

		    if ($subtotal != 0) {
				$igv = $rows->igv;
			    $total = $rows->total;

			    $objPHPExcel->setActiveSheetIndex(0)
			            ->setCellValue('A'.$fila, $fecha)
			            ->setCellValue('B'.$fila, $paciente)
			            ->setCellValue('C'.$fila, $doc)
			            ->setCellValue('D'.$fila, $numero)
			            ->setCellValue('E'.$fila, $bussinesname)
			            ->setCellValue('F'.$fila, $ruc)
			            ->setCellValue('G'.$fila, $tarjeta)
			            ->setCellValue('H'.$fila, $subtotal)
			            ->setCellValue('I'.$fila, $igv)
			            ->setCellValue('J'.$fila, number_format($total,2,'.',''));
			    $fila++;
			}

		}

		
		// Rename worksheet
		$objPHPExcel->getActiveSheet()->setTitle('Anulados');
		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		$objPHPExcel->setActiveSheetIndex(0);
		// Redirect output to a client’s web browser (Excel5)
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="Anulados.xls"');
		header('Cache-Control: max-age=0');
		// If you're serving to IE 9, then the following may be needed
		header('Cache-Control: max-age=1');
		// If you're serving to IE over SSL, then the following may be needed
		header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
		header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
		header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
		header ('Pragma: public'); // HTTP/1.0
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
		exit;
	}

	function rHosU($fi,$ff){
		$db = new Conexion();
		error_reporting(E_ALL);
		ini_set('display_errors', TRUE);
		ini_set('display_startup_errors', TRUE);
		if (PHP_SAPI == 'cli')
			die('This example should only be run from a Web Browser');
		/** Include PHPExcel */
		// Create new PHPExcel object
		$objPHPExcel = new PHPExcel();
		// Set document properties
		$objPHPExcel->getProperties()->setCreator("Garzatec")
									 ->setLastModifiedBy("Garzatec")
									 ->setTitle("Reporte de Movimientos XLS")
									 ->setSubject("Reporte de Movimientos")
									 ->setDescription("Reporte generado para Office 2007 XLSX.")
									 ->setKeywords("office 2007 openxml php")
									 ->setCategory("Test report file");
		
		$objPHPExcel->setActiveSheetIndex(0)
		            ->setCellValue('A1', 'FECHA')
		            ->setCellValue('B1', 'PACIENTE')
		            ->setCellValue('C1', 'DOC')
		            ->setCellValue('D1', 'NUMERO')
		            ->setCellValue('E1', 'CLIENTE')
		            ->setCellValue('F1', 'RUC')
		            ->setCellValue('G1', 'HABITACION')
		            ->setCellValue('H1', 'SUBTOTAL')
		            ->setCellValue('I1', 'IGV')
		            ->setCellValue('J1', 'TOTAL')
		            ->setCellValue('K1', 'TIPO')
		            ->setCellValue('L1', 'MEDICO');

		$fila = 2;
		//CON ESTA CONSULTA FIGURA DATA DE LOS DE CONVENIO PERO NO PARTICULARES, PARA PARTICULARES SE NECESITARÌA OTRO SQL
		$query=$db->query("SELECT h.fecha, ha.nombre habitacion, paciente.nombres, paciente.apellidopaterno, paciente.apellidomaterno, td.nombre tipodocumento, m.numero numero, e.nombre aseguradora, e.ruc, m.subtotal, m.igv, m.total, es.nombre area, med.nombres mednom, med.apellidopaterno medapep, med.apellidomaterno medapem FROM hospitalizacion h left join habitacion ha on h.habitacion_id = ha.id left join movimiento m on m.id = h.movimientoinicial_id left JOIN person med on med.id = h.medico_id left join especialidad es on es.id = med.especialidad_id LEFT JOIN person paciente on paciente.id = m.persona_id LEFT JOIN plan as e on m.plan_id = e.id left join tipodocumento td on td.id = m.tipodocumento_id WHERE h.situacion = 'A' and h.habitacion_id IN (23,24,25,32,33,34,35,36) AND m.situacion != 'U' AND m.situacion != 'A' AND m.deleted_at IS NULL AND m.fecha BETWEEN '$fi' and '$ff' order by h.fecha asc");

		while($rows=$db->recorrerobj($query)){
		    $fecha = $rows->fecha;
		    $paciente = $rows->apellidopaterno.' '.$rows->apellidomaterno.' '.$rows->nombres;
		    $medico = $rows->medapep.' '.$rows->medapem.' '.$rows->mednom;
		    $doc = $rows->tipodocumento;
		    $numero = $rows->numero;
		    $habitacion = $rows->habitacion;
		    $bussinesname = $rows->aseguradora;
		    if ($bussinesname == 'TARIFA-PARTICULAR 1') {
		    	$ruc = "-";
		    } else {
		    	$ruc = $rows->ruc;
		    }
		    $subtotal = $rows->subtotal;
		    $igv = $rows->igv;
		    $total = $rows->total;
		    $area = $rows->area;

		    $objPHPExcel->setActiveSheetIndex(0)
		            ->setCellValue('A'.$fila, $fecha)
		            ->setCellValue('B'.$fila, $paciente)
		            ->setCellValue('C'.$fila, $doc)
		            ->setCellValue('D'.$fila, $numero)
		            ->setCellValue('E'.$fila, $bussinesname)
		            ->setCellValue('F'.$fila, $ruc)
		            ->setCellValue('G'.$fila, $habitacion)
		            ->setCellValue('H'.$fila, $subtotal)
		            ->setCellValue('I'.$fila, $igv)
		            ->setCellValue('J'.$fila, number_format($total,2,'.',''))
		            ->setCellValue('K'.$fila, $area)
		            ->setCellValue('L'.$fila, $medico);
		    $fila++;
		}
		
		// Rename worksheet
		$objPHPExcel->getActiveSheet()->setTitle('MovimientosConta');
		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		$objPHPExcel->setActiveSheetIndex(0);
		// Redirect output to a client’s web browser (Excel5)
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="MovimientosConta.xls"');
		header('Cache-Control: max-age=0');
		// If you're serving to IE 9, then the following may be needed
		header('Cache-Control: max-age=1');
		// If you're serving to IE over SSL, then the following may be needed
		header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
		header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
		header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
		header ('Pragma: public'); // HTTP/1.0
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
		exit;
	}

	function rCajaUsuariosE($fi,$ff){
		$db = new Conexion();
		error_reporting(E_ALL);
		ini_set('display_errors', TRUE);
		ini_set('display_startup_errors', TRUE);
		if (PHP_SAPI == 'cli')
			die('This example should only be run from a Web Browser');
		/** Include PHPExcel */
		// Create new PHPExcel object
		$objPHPExcel = new PHPExcel();
		// Set document properties
		$objPHPExcel->getProperties()->setCreator("Garzatec")
									 ->setLastModifiedBy("Garzatec")
									 ->setTitle("Reporte de Compras XLS")
									 ->setSubject("Reporte de Ventas solicitado")
									 ->setDescription("Reporte generado para Office 2007 XLSX.")
									 ->setKeywords("office 2007 openxml php")
									 ->setCategory("Test report file");
		// Add some data

		$objPHPExcel->setActiveSheetIndex(0)
		            ->setCellValue('A3', 'FECHA')
		            ->setCellValue('B3', 'USUARIO')
		            ->setCellValue('C3', 'EFECTIVO')
		            ->setCellValue('D3', 'TARJETA')
		            ->setCellValue('E3', 'EGRESOS')
		            ->setCellValue('F3', 'APERTURA')
		            ->setCellValue('G3', 'CIERRE');

		$tp=0;$pp=0;$yp=0;

		$query=$db->query("SELECT movimiento.fecha, TIME(movimiento.created_at) hora, movimiento.numero, producto.nombre as producto, p.apellidopaterno, p.apellidomaterno, p.nombres, kardex.cantidad, producto.precioventa, movimiento.total, u.nombres as usuario, movimiento.estadopago FROM detallemovimiento inner JOIN movimiento on detallemovimiento.movimiento_id = movimiento.id inner join person AS p on movimiento.persona_id = p.id inner join person as u on movimiento.responsable_id = u.id inner JOIN producto on detallemovimiento.producto_id = producto.id JOIN kardex on detallemovimiento.id = kardex.detallemovimiento_id where movimiento.tipomovimiento_id = 4 AND movimiento.ventafarmacia = 'S' ".$q." AND movimiento.fecha BETWEEN '$fi' AND '$ff' order by movimiento.fecha, hora asc");
		$fila = 4;
		while($rows=$db->recorrerobj($query)){
		    $fecha = $rows->fecha;
		    $hora = $rows->hora;
		    $numero = $rows->numero;
		    $producto = $rows->producto;
		    $apellidopaterno = $rows->apellidopaterno;
		    $apellidomaterno = $rows->apellidomaterno;
		    $nombres = $rows->nombres;
		    $cantidad = $rows->cantidad;
		    $precioventa = $rows->precioventa;
		    $total = $rows->total;
		    $estadopago = $rows->estadopago;		    
		    
		    $estadopago == 'P' ? $yp+=$total : $pp+=$total;
		    $estadopago == 'P' ? $estadopago = 'PAGADO' : $estadopago = 'PENDIENTE';

            $pac = $apellidopaterno.' '.$apellidomaterno.' '.$nombres;

			$tp += $total;

		    $objPHPExcel->setActiveSheetIndex(0)
		            ->setCellValue('A'.$fila, $fecha)
		            ->setCellValue('B'.$fila, $hora)
		            ->setCellValue('C'.$fila, $numero)
		            ->setCellValue('D'.$fila, $producto)
		            ->setCellValue('E'.$fila, $pac)
		            ->setCellValue('F'.$fila, $cantidad)
		            ->setCellValue('G'.$fila, $precioventa);
		    $fila++;
		}

		$objPHPExcel->setActiveSheetIndex(0)
		            ->setCellValue('A'.$fila, 'PAGADO')
		            ->setCellValue('B'.$fila, $yp);
	    $fila++;
	    $objPHPExcel->setActiveSheetIndex(0)
		            ->setCellValue('C'.$fila, 'PENDIENTE')
		            ->setCellValue('D'.$fila, $pp);
        $fila++;
	    $objPHPExcel->setActiveSheetIndex(0)
		            ->setCellValue('E'.$fila, 'TOTAL')
		            ->setCellValue('F'.$fila, $tp);
		
		// Rename worksheet
		$objPHPExcel->getActiveSheet()->setTitle('Usuarios');
		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		$objPHPExcel->setActiveSheetIndex(0);
		// Redirect output to a client’s web browser (Excel5)
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="CajaUsuarios.xls"');
		header('Cache-Control: max-age=0');
		// If you're serving to IE 9, then the following may be needed
		header('Cache-Control: max-age=1');
		// If you're serving to IE over SSL, then the following may be needed
		header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
		header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
		header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
		header ('Pragma: public'); // HTTP/1.0
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
		exit;
	}

	function rMovimientosE($fi,$ff,$filtro,$extra,$paciente){
		$db = new Conexion();
		error_reporting(E_ALL);
		ini_set('display_errors', TRUE);
		ini_set('display_startup_errors', TRUE);
		if (PHP_SAPI == 'cli')
			die('This example should only be run from a Web Browser');
		/** Include PHPExcel */
		// Create new PHPExcel object
		$objPHPExcel = new PHPExcel();
		// Set document properties
		$objPHPExcel->getProperties()->setCreator("Garzatec")
									 ->setLastModifiedBy("Garzatec")
									 ->setTitle("Reporte de Movimientos XLS")
									 ->setSubject("Reporte de Movimientos")
									 ->setDescription("Reporte generado para Office 2007 XLSX.")
									 ->setKeywords("office 2007 openxml php")
									 ->setCategory("Test report file");
		
		$objPHPExcel->setActiveSheetIndex(0)
		            ->setCellValue('A1', 'FECHA')
		            ->setCellValue('B1', 'PACIENTE')
		            ->setCellValue('C1', 'DOC')
		            ->setCellValue('D1', 'NUMERO')
		            ->setCellValue('E1', 'CLIENTE')
		            ->setCellValue('F1', 'RUC')
		            ->setCellValue('G1', 'CONDICION')
		            ->setCellValue('H1', 'SUBTOTAL')
		            ->setCellValue('I1', 'IGV')
		            ->setCellValue('J1', 'TOTAL')
		            ->setCellValue('K1', 'SERVICIO')
		            ->setCellValue('L1', 'TIPO')
		            ->setCellValue('M1', 'MEDICO');

		if ($filtro == 1) {
			$condicion = "ts.id =".$extra." AND";
		} else if ($filtro == 2) {
			$condicion = "es.id =".$extra." AND";
		} else if ($filtro == 3) {
			$condicion = "servicio.id =".$extra." AND";
		}

		if ($paciente == 1) {
			$condicion = $condicion." m.plan_id = 6 ";
		} else if ($paciente == 2) {
			$condicion = $condicion." m.plan_id != 6 ";
		} else if ($paciente == 0) {
			$condicion = $condicion." m.plan_id IS NOT NULL ";
		}

		if ($extra == 0) {
			$condicion = '';
		}

		$fila = 2;
		//CON ESTA CONSULTA FIGURA DATA DE LOS DE CONVENIO PERO NO PARTICULARES, PARA PARTICULARES SE NECESITARÌA OTRO SQL
		$query=$db->query("SELECT m2.fecha, paciente.nombres, paciente.apellidopaterno, paciente.apellidomaterno, td.nombre tipodocumento, m2.numero numero, e.nombre aseguradora, e.ruc, m2.tarjeta, m2.subtotal, m2.igv, m2.total, servicio.nombre servicio, es.nombre area, med.nombres mednom, med.apellidopaterno medapep, med.apellidomaterno medapem FROM movimiento m join movimiento m2 on m.id = m2.movimiento_id JOIN detallemovcaja dmc on m.id = dmc.movimiento_id JOIN person med on med.id = dmc.persona_id join especialidad es on es.id = med.especialidad_id LEFT JOIN person paciente on paciente.id = m.persona_id LEFT JOIN plan as e on m.plan_id = e.id join servicio on servicio.id = dmc.servicio_id join tiposervicio ts on ts.id = servicio.tiposervicio_id join tipodocumento td on td.id = m2.tipodocumento_id WHERE m.tipodocumento_id = 1 AND m.situacion != 'U' AND m.situacion != 'A' AND m.deleted_at IS NULL and ".$condicion." AND m.fecha BETWEEN '$fi' and '$ff' order by m2.id asc");

		while($rows=$db->recorrerobj($query)){
		    $fecha = $rows->fecha;
		    $paciente = $rows->apellidopaterno.' '.$rows->apellidomaterno.' '.$rows->nombres;
		    $medico = $rows->medapep.' '.$rows->medapem.' '.$rows->mednom;
		    $doc = $rows->tipodocumento;
		    $numero = $rows->numero;
		    $bussinesname = $rows->aseguradora;
		    if ($bussinesname == 'TARIFA-PARTICULAR 1') {
		    	$ruc = "-";
		    } else {
		    	$ruc = $rows->ruc;
		    }
		    $tarjeta = $rows->tarjeta;
		    if ($tarjeta == NULL) {
		    	$tarjeta = 'CONTADO';
		    } else {
		    	$tarjeta = 'TARJETA';
		    }
		    $subtotal = $rows->subtotal;
		    $igv = $rows->igv;
		    $total = $rows->total;
		    $servicio = $rows->servicio;
		    $area = $rows->area;

		    $objPHPExcel->setActiveSheetIndex(0)
		            ->setCellValue('A'.$fila, $fecha)
		            ->setCellValue('B'.$fila, $paciente)
		            ->setCellValue('C'.$fila, $doc)
		            ->setCellValue('D'.$fila, $numero)
		            ->setCellValue('E'.$fila, $bussinesname)
		            ->setCellValue('F'.$fila, $ruc)
		            ->setCellValue('G'.$fila, $tarjeta)
		            ->setCellValue('H'.$fila, $subtotal)
		            ->setCellValue('I'.$fila, $igv)
		            ->setCellValue('J'.$fila, number_format($total,2,'.',''))
		            ->setCellValue('K'.$fila, $servicio)
		            ->setCellValue('L'.$fila, $area)
		            ->setCellValue('M'.$fila, $medico);
		    $fila++;
		}

		$query=$db->query("SELECT m.fecha, paciente.nombres, paciente.apellidopaterno, paciente.apellidomaterno, td.nombre tipodocumento, m.numero numero, e.nombre aseguradora, e.ruc, m.tarjeta, m.subtotal, m.igv, m.total, servicio.nombre servicio, es.nombre area, med.nombres mednom, med.apellidopaterno medapep, med.apellidomaterno medapem FROM movimiento m JOIN detallemovcaja dmc on m.id = dmc.movimiento_id JOIN person med on med.id = dmc.persona_id join especialidad es on es.id = med.especialidad_id LEFT JOIN person paciente on paciente.id = m.persona_id LEFT JOIN plan as e on m.plan_id = e.id join servicio on servicio.id = dmc.servicio_id join tiposervicio ts on ts.id = servicio.tiposervicio_id join tipodocumento td on td.id = m.tipodocumento_id WHERE m.tipomovimiento_id = 9 AND m.situacion != 'U' AND m.situacion != 'A' AND m.deleted_at IS NULL and ".$condicion." AND m.fecha BETWEEN '$fi' and '$ff' order by m.id asc");

		while($rows=$db->recorrerobj($query)){
		    $fecha = $rows->fecha;
		    $paciente = $rows->apellidopaterno.' '.$rows->apellidomaterno.' '.$rows->nombres;
		    $medico = $rows->medapep.' '.$rows->medapem.' '.$rows->mednom;
		    $doc = $rows->tipodocumento;
		    $numero = $rows->numero;
		    $bussinesname = $rows->aseguradora;
		    if ($bussinesname == 'TARIFA-PARTICULAR 1') {
		    	$ruc = "-";
		    } else {
		    	$ruc = $rows->ruc;
		    }
		    $tarjeta = $rows->tarjeta;
		    if ($tarjeta == NULL) {
		    	$tarjeta = 'CONTADO';
		    } else {
		    	$tarjeta = 'TARJETA';
		    }
		    $subtotal = $rows->subtotal;
		    $igv = $rows->igv;
		    $total = $rows->total;
		    $servicio = $rows->servicio;
		    $area = $rows->area;

		    $objPHPExcel->setActiveSheetIndex(0)
		            ->setCellValue('A'.$fila, $fecha)
		            ->setCellValue('B'.$fila, $paciente)
		            ->setCellValue('C'.$fila, $doc)
		            ->setCellValue('D'.$fila, $numero)
		            ->setCellValue('E'.$fila, $bussinesname)
		            ->setCellValue('F'.$fila, $ruc)
		            ->setCellValue('G'.$fila, $tarjeta)
		            ->setCellValue('H'.$fila, $subtotal)
		            ->setCellValue('I'.$fila, $igv)
		            ->setCellValue('J'.$fila, number_format($total,2,'.',''))
		            ->setCellValue('K'.$fila, $servicio)
		            ->setCellValue('L'.$fila, $area)
		            ->setCellValue('M'.$fila, $medico);
		    $fila++;
		}

		
		// Rename worksheet
		$objPHPExcel->getActiveSheet()->setTitle('MovimientosConta');
		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		$objPHPExcel->setActiveSheetIndex(0);
		// Redirect output to a client’s web browser (Excel5)
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="MovimientosConta.xls"');
		header('Cache-Control: max-age=0');
		// If you're serving to IE 9, then the following may be needed
		header('Cache-Control: max-age=1');
		// If you're serving to IE over SSL, then the following may be needed
		header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
		header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
		header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
		header ('Pragma: public'); // HTTP/1.0
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
		exit;
	}

	function rMovimientosMed($fi,$ff){
		$db = new Conexion();

		$pdf = new PDF('L','mm','A4');
        $pdf->AliasNbPages();
        $pdf->AddPage();

        $pdf->SetFillColor(255,255,255);
        $pdf->SetFont('Arial','B',14);

        $pdf->Cell(0,10,utf8_decode('ATENCIONES POR MÉDICO'),0,0,'C',0);
        $pdf->Ln();

		$pdf->SetFillColor(68,164,168);
        $pdf->SetFont('Arial','B',11);
        $pdf->Cell(70,6,'FECHA',1,0,'C',1);
        $pdf->Cell(30,6,utf8_decode('PACIENTE'),1,0,'C',1);
        $pdf->Cell(15,6,utf8_decode('DOC'),1,0,'C',1);
        $pdf->Cell(50,6,'NUMERO',1,0,'C',1);
        $pdf->Cell(16,6,utf8_decode('CLIENTE.'),1,0,'C',1);
        $pdf->Cell(16,6,utf8_decode('RUC.'),1,0,'C',1);
        $pdf->Cell(16,6,utf8_decode('TOTAL'),1,0,'C',1);
        $pdf->Cell(22,6,utf8_decode('SERVICIO'),1,0,'C',1);
        $pdf->Cell(20,6,utf8_decode('TIPO'),1,0,'C',1);
        $pdf->Cell(20,6,utf8_decode('MEDICO'),1,0,'C',1);

		$medico_id = '';
		//CON ESTA CONSULTA FIGURA DATA DE LOS DE CONVENIO PERO NO PARTICULARES, PARA PARTICULARES SE NECESITARÌA OTRO SQL
		$query=$db->query("SELECT m2.fecha, paciente.nombres, paciente.apellidopaterno, paciente.apellidomaterno, td.nombre tipodocumento, m2.numero numero, e.nombre aseguradora, e.ruc, m2.tarjeta, m2.subtotal, m2.igv, m2.total, servicio.nombre servicio, es.nombre area, med.nombres mednom, med.apellidopaterno medapep, med.apellidomaterno medapem FROM movimiento m join movimiento m2 on m.id = m2.movimiento_id JOIN detallemovcaja dmc on m.id = dmc.movimiento_id JOIN person med on med.id = dmc.persona_id join especialidad es on es.id = med.especialidad_id LEFT JOIN person paciente on paciente.id = m.persona_id LEFT JOIN plan as e on m.plan_id = e.id join servicio on servicio.id = dmc.servicio_id join tiposervicio ts on ts.id = servicio.tiposervicio_id join tipodocumento td on td.id = m2.tipodocumento_id WHERE m.tipodocumento_id = 1 AND m.situacion != 'U' AND m.situacion != 'A' AND m.deleted_at IS NULL AND m.fecha BETWEEN '$fi' and '$ff' order by m2.id asc");

		while($rows=$db->recorrerobj($query)){
		    $fecha = $rows->fecha;
		    $paciente = $rows->apellidopaterno.' '.$rows->apellidomaterno.' '.$rows->nombres;
		    $medico = $rows->medapep.' '.$rows->medapem.' '.$rows->mednom;
		    $doc = $rows->tipodocumento;
		    $numero = $rows->numero;
		    $bussinesname = $rows->aseguradora;
		    if ($bussinesname == 'TARIFA-PARTICULAR 1') {
		    	$ruc = "-";
		    } else {
		    	$ruc = $rows->ruc;
		    }
		    $tarjeta = $rows->tarjeta;
		    if ($tarjeta == NULL) {
		    	$tarjeta = 'CONTADO';
		    } else {
		    	$tarjeta = 'TARJETA';
		    }
		    $subtotal = $rows->subtotal;
		    $igv = $rows->igv;
		    $total = $rows->total;
		    $servicio = $rows->servicio;
		    $area = $rows->area;

		    if ($medico != $medico_id) {
		    	$medico_id = $medico;

		    }

		    $pdf->Cell(30,6.5,$fecha,1,0,'C',1);
		    if(strlen($paciente)>40){
				$pdf->SetFont('Arial','',8);
                $x=$pdf->GetX();
                $y=$pdf->GetY();                    
                $pdf->Multicell(50,3,utf8_decode($paciente),0,'L');
                $pdf->SetXY($x,$y);
                $pdf->Cell(70,6.5,"",1,0,'C');
            }else{
            	$pdf->SetFont('Arial','',8);
                $pdf->Cell(70,6.5,utf8_decode($paciente),1,0,'C',1);  
            }

	        $pdf->Cell(30,6.5,$doc,1,0,'C',1);
	        $pdf->Cell(15,6.5,$numero,1,0,'C',1);
	        if(strlen($bussinesname)>30){
				$pdf->SetFont('Arial','',8);
                $x=$pdf->GetX();
                $y=$pdf->GetY();                    
                $pdf->Multicell(50,3,utf8_decode($bussinesname),0,'L');
                $pdf->SetXY($x,$y);
                $pdf->Cell(50,6.5,"",1,0,'C');
            }else{
            	$pdf->SetFont('Arial','',8);
                $pdf->Cell(50,6.5,utf8_decode($bussinesname),1,0,'C',1);  
            }

	        $pdf->Cell(16,6.5,$ruc,1,0,'C',1);
	        $pdf->Cell(16,6.5,number_format($total),1,0,'C',1);
	        $pdf->Cell(22,6.5,utf8_decode($servicio),1,0,'C',1);
	        $pdf->Cell(20,6.5,utf8_decode($area),1,0,'C',1);
        	$pdf->Cell(20,6.5,$medico,1,0,'C',1);
		}

		$query=$db->query("SELECT m.fecha, paciente.nombres, paciente.apellidopaterno, paciente.apellidomaterno, td.nombre tipodocumento, m.numero numero, e.nombre aseguradora, e.ruc, m.tarjeta, m.subtotal, m.igv, m.total, servicio.nombre servicio, es.nombre area, med.nombres mednom, med.apellidopaterno medapep, med.apellidomaterno medapem FROM movimiento m JOIN detallemovcaja dmc on m.id = dmc.movimiento_id JOIN person med on med.id = dmc.persona_id join especialidad es on es.id = med.especialidad_id LEFT JOIN person paciente on paciente.id = m.persona_id LEFT JOIN plan as e on m.plan_id = e.id join servicio on servicio.id = dmc.servicio_id join tiposervicio ts on ts.id = servicio.tiposervicio_id join tipodocumento td on td.id = m.tipodocumento_id WHERE m.tipomovimiento_id = 9 AND m.situacion != 'U' AND m.situacion != 'A' AND m.deleted_at IS NULL AND m.fecha BETWEEN '$fi' and '$ff' order by m.id asc");

		while($rows=$db->recorrerobj($query)){
		    $fecha = $rows->fecha;
		    $paciente = $rows->apellidopaterno.' '.$rows->apellidomaterno.' '.$rows->nombres;
		    $medico = $rows->medapep.' '.$rows->medapem.' '.$rows->mednom;
		    $doc = $rows->tipodocumento;
		    $numero = $rows->numero;
		    $bussinesname = $rows->aseguradora;
		    if ($bussinesname == 'TARIFA-PARTICULAR 1') {
		    	$ruc = "-";
		    } else {
		    	$ruc = $rows->ruc;
		    }
		    $tarjeta = $rows->tarjeta;
		    if ($tarjeta == NULL) {
		    	$tarjeta = 'CONTADO';
		    } else {
		    	$tarjeta = 'TARJETA';
		    }
		    $subtotal = $rows->subtotal;
		    $igv = $rows->igv;
		    $total = $rows->total;
		    $servicio = $rows->servicio;
		    $area = $rows->area;

		    $pdf->Cell(30,6.5,$fecha,1,0,'C',1);
		    if(strlen($paciente)>40){
				$pdf->SetFont('Arial','',8);
                $x=$pdf->GetX();
                $y=$pdf->GetY();                    
                $pdf->Multicell(50,3,utf8_decode($paciente),0,'L');
                $pdf->SetXY($x,$y);
                $pdf->Cell(70,6.5,"",1,0,'C');
            }else{
            	$pdf->SetFont('Arial','',8);
                $pdf->Cell(70,6.5,utf8_decode($paciente),1,0,'C',1);  
            }

	        $pdf->Cell(30,6.5,$doc,1,0,'C',1);
	        $pdf->Cell(15,6.5,$numero,1,0,'C',1);
	        if(strlen($bussinesname)>30){
				$pdf->SetFont('Arial','',8);
                $x=$pdf->GetX();
                $y=$pdf->GetY();                    
                $pdf->Multicell(50,3,utf8_decode($bussinesname),0,'L');
                $pdf->SetXY($x,$y);
                $pdf->Cell(50,6.5,"",1,0,'C');
            }else{
            	$pdf->SetFont('Arial','',8);
                $pdf->Cell(50,6.5,utf8_decode($bussinesname),1,0,'C',1);  
            }

	        $pdf->Cell(16,6.5,$ruc,1,0,'C',1);
	        $pdf->Cell(16,6.5,number_format($total),1,0,'C',1);
	        $pdf->Cell(22,6.5,utf8_decode($servicio),1,0,'C',1);
	        $pdf->Cell(20,6.5,utf8_decode($area),1,0,'C',1);
        	$pdf->Cell(20,6.5,$medico,1,0,'C',1);
	        
			
			$pdf->Ln();
		}

    	$pdf->Output();
	}

	function rInicial($lab, $origen, $presentacion){
        $response = '';
        $db = new Conexion();

        $pdf = new PDF('L','mm','A4');
        $pdf->AliasNbPages();
        $pdf->AddPage();

        $pdf->SetFillColor(255,255,255);
        $pdf->SetFont('Arial','B',14);

        $pdf->Cell(290,10,utf8_decode('Inventario Inicial 2018 Valorizado'),0,0,'C',0);
        $pdf->Ln();

    	if ($origen != 0) {
    		$query2 = $db->query("SELECT nombre from origen where id = '$origen'");

			while ($rows2=$db->recorrerobj($query2)) {
				$pdf->Cell(0,8,utf8_decode('Origen: '.$rows2->nombre),0,0,'C',1);
	        	$pdf->Ln();
			}
		}

		if ($presentacion != 0) {
    		$query2 = $db->query("SELECT nombre from presentacion where id = '$presentacion'");

			while ($rows2=$db->recorrerobj($query2)) {
				$pdf->Cell(0,8,utf8_decode('Presentación: '.$rows2->nombre),0,0,'C',1);
	        	$pdf->Ln();
			}
		}

        $pdf->SetFillColor(68,164,168);
        $pdf->SetFont('Arial','B',11);
        $pdf->Cell(70,6,'Producto',1,0,'C',1);
        $pdf->Cell(30,6,utf8_decode('Presentación'),1,0,'C',1);
        $pdf->Cell(15,6,utf8_decode('Origen'),1,0,'C',1);
        $pdf->Cell(50,6,'Laboratorio',1,0,'C',1);
        $pdf->Cell(16,6,utf8_decode('Inv.'),1,0,'C',1);
        $pdf->Cell(16,6,utf8_decode('Vent.'),1,0,'C',1);
        $pdf->Cell(16,6,utf8_decode('Comp.'),1,0,'C',1);
        $pdf->Cell(22,6,utf8_decode('Exist. al 31'),1,0,'C',1);
        $pdf->Cell(20,6,utf8_decode('P.Comp.'),1,0,'C',1);
        $pdf->Cell(20,6,utf8_decode('Valor'),1,0,'C',1);

    	$extra = '';
		if ($origen != 0) {
			$extra = 'WHERE p.origen_id = '.$origen;
			if ($presentacion != 0) {
    			$extra = $extra.' AND p.presentacion_id='.$presentacion;
    		}
		} else {
			if ($presentacion != 0) {
    			$extra = ' WHERE p.presentacion_id='.$presentacion;
    		}
		}

        $pdf->SetFont('Arial','',10);
        $pdf->SetFillColor(250,250,250);
        $pdf->Ln();

        $totals = 0;

        $query2 = $db->query("SELECT p.id, p.nombre producto, p.preciocompra, p.precioventa, p.preciokayros, p.origen_id, u.bussinesname proveedor, pr.nombre presentacion, l.nombre laboratorio, pp.principioactivo_id principioactivop FROM producto p join person u on p.proveedor_id = u.id left JOIN presentacion AS pr ON p.presentacion_id = pr.id left JOIN laboratorio AS l ON p.laboratorio_id = l.id join productoprincipio pp on pp.producto_id = p.id ".$extra." where p.deleted_at is null group by p.id ORDER BY p.nombre ASC");

		while ($rows2=$db->recorrerobj($query2)) {
			$pro_id = $rows2->id;
			$cantidad = 0;
			$query=$db->query("SELECT detallemovimiento.cantidad FROM detallemovimiento JOIN movimiento on detallemovimiento.movimiento_id = movimiento.id WHERE movimiento.fecha between '2018-01-02' and '2018-01-03' and movimiento.tipomovimiento_id = 5 and detallemovimiento.producto_id = ".$rows2->id." and movimiento.deleted_at is null order by detallemovimiento.id DESC");
			while($rows=$db->recorrerobj($query)){
			   $cantidad += $rows->cantidad;
			}
			$origen_id = $rows2->origen_id;
			$origen = '';
			$query=$db->query("SELECT nombre from origen where id = '$origen_id'");
			while($rows=$db->recorrerobj($query)){
			   $origen = $rows->nombre;
			}
			$ventas=0;
			$query=$db->query("SELECT detallemovimiento.cantidad FROM detallemovimiento JOIN movimiento on detallemovimiento.movimiento_id = movimiento.id WHERE movimiento.fecha between '2018-01-01' and '2018-01-02' and movimiento.tipomovimiento_id = 4 and detallemovimiento.producto_id = ".$rows2->id." and movimiento.created_at<'2018-01-02 20:23:00' and movimiento.situacion not in ('A','U') and movimiento.deleted_at is null order by detallemovimiento.id DESC");
			while($rows=$db->recorrerobj($query)){
				$ventas=$ventas+$rows->cantidad;
			}
			$compras=0;
			$query=$db->query("SELECT detallemovimiento.cantidad FROM detallemovimiento JOIN movimiento on detallemovimiento.movimiento_id = movimiento.id WHERE movimiento.fecha between '2018-01-01' and '2018-01-02' and movimiento.tipomovimiento_id = 3 and detallemovimiento.producto_id = ".$rows2->id." and movimiento.created_at<'2018-01-02 20:23:00' and movimiento.situacion not in ('A','U') and movimiento.deleted_at is null order by detallemovimiento.id DESC");
			while($rows=$db->recorrerobj($query)){
				$compras=$compras+$rows->cantidad;
			}
			$producto = $rows2->producto;
		    $presentacion = $rows2->presentacion;
		    $laboratorio = $rows2->laboratorio;
		    $compra = $rows2->preciocompra;
		    $venta = $rows2->precioventa;
		    $kayros = $rows2->preciokayros;
		    $totals += ($cantidad + $ventas - $compras)*$compra;
		    
		    if(strlen($producto)>40){
				$pdf->SetFont('Arial','',8);
                $x=$pdf->GetX();
                $y=$pdf->GetY();                    
                $pdf->Multicell(50,3,utf8_decode($producto),0,'L');
                $pdf->SetXY($x,$y);
                $pdf->Cell(70,6.5,"",1,0,'C');
            }else{
            	$pdf->SetFont('Arial','',8);
                $pdf->Cell(70,6.5,utf8_decode($producto),1,0,'C',1);  
            }

	        $pdf->Cell(30,6.5,$presentacion,1,0,'C',1);
	        $pdf->Cell(15,6.5,$origen,1,0,'C',1);
	        if(strlen($laboratorio)>30){
				$pdf->SetFont('Arial','',8);
                $x=$pdf->GetX();
                $y=$pdf->GetY();                    
                $pdf->Multicell(50,3,utf8_decode($laboratorio),0,'L');
                $pdf->SetXY($x,$y);
                $pdf->Cell(50,6.5,"",1,0,'C');
            }else{
            	$pdf->SetFont('Arial','',8);
                $pdf->Cell(50,6.5,utf8_decode($laboratorio),1,0,'C',1);  
            }

	        $pdf->Cell(16,6.5,number_format($cantidad),1,0,'C',1);
	        $pdf->Cell(16,6.5,number_format($ventas),1,0,'C',1);
	        $pdf->Cell(16,6.5,number_format($compras),1,0,'C',1);
	        $pdf->Cell(22,6.5,number_format($cantidad + $ventas - $compras),1,0,'C',1);
	        $pdf->Cell(20,6.5,utf8_decode($compra),1,0,'C',1);
        	$pdf->Cell(20,6.5,'S/ '.number_format(($cantidad + $ventas - $compras)*$compra,2,'.',''),1,0,'C',1);
	        
			
			$pdf->Ln();

		}

		$pdf->Cell(255);
		$pdf->Cell(20,6.5,'S/ '.number_format($totals,2,'.',''),1,0,'C',1);
            
        $pdf->Output();
	}

	function rFacConP($fi,$ff,$tipo){
		$db = new Conexion();

		$response = '<table class="table table-bordered table-striped table-condensed table-hover"><tr><th>Fecha</th><th>Serie</th><th>Numero</th><th>Paciente</th><th>Cliente</th><th>RUC</th><th>Siniestro</th><th>IGV</th><th>Total</th><th>Usuario</th></tr>';

		$dato = '';
		if ($tipo == 1) {
			$dato="AND movimiento.situacion != 'A'";
		}
		$query=$db->query("SELECT movimiento.fecha, movimiento.comentario, movimiento.serie, movimiento.numero, p.apellidopaterno, p.apellidomaterno, p.nombres, e.bussinesname, e.ruc, movimiento.subtotal, movimiento.igv, movimiento.total, res.nombres responsable FROM movimiento join person AS p on movimiento.persona_id = p.id join person as e on movimiento.empresa_id = e.id join person res on res.id = movimiento.responsable_id where movimiento.tipomovimiento_id = 9 ".$dato." AND movimiento.tipodocumento_id = 17 AND movimiento.fecha BETWEEN '$fi' AND '$ff' order by movimiento.fecha asc");
		$totalf = 0;
		while($rows=$db->recorrerobj($query)){
			$fecha = $rows->fecha;
		    $serie = $rows->serie;
		    $numero = $rows->numero;
		    $siniestro = $rows->comentario;
		    $apellidopaterno = $rows->apellidopaterno;
		    $apellidomaterno = $rows->apellidomaterno;
		    $nombres = $rows->nombres;
		    $bussinesname = $rows->bussinesname;
		    $ruc = $rows->ruc;
		    $subtotal = $rows->subtotal;
		    $igv = $rows->igv;
		    $total = $rows->total;
		    $responsable = $rows->responsable;

		    $fechai = date_create($fecha);
		    $fechai = date_format($fechai,"d/m/y");
            $pac = $apellidopaterno.' '.$apellidomaterno.' '.$nombres;

            $response = $response.'<tr><td style="font-size:12px">'.$fechai.'</td><td style="font-size:12px">'.$serie.'</td><td style="font-size:12px">'.$numero.'</td><td style="font-size:12px">'.$pac.'</td><td style="font-size:12px">'.$bussinesname.'</td><td style="font-size:12px">'.$ruc.'</td><td style="font-size:12px">'.$siniestro.'</td><td style="font-size:12px">'.$igv.'</td><td style="font-size:12px">'.$total.'</td><td style="font-size:12px">'.$responsable.'</td></tr>';

            $totalf += $total;
		}

        $response = $response.'<tr><td colspan="8" style="font-weight: bold; text-align: right;">TOTAL:</td><td style="font-weight: bold;">'.$totalf.'</td></table>';
		echo $response;
	}

	function rFacCon($fi,$ff,$tipo){
		$db = new Conexion();
		error_reporting(E_ALL);
		ini_set('display_errors', TRUE);
		ini_set('display_startup_errors', TRUE);
		if (PHP_SAPI == 'cli')
			die('This example should only be run from a Web Browser');
		/** Include PHPExcel */
		// Create new PHPExcel object
		$objPHPExcel = new PHPExcel();
		// Set document properties
		$objPHPExcel->getProperties()->setCreator("Garzatec")
									 ->setLastModifiedBy("Garzatec")
									 ->setTitle("Reporte de Facturas por Convenios XLS")
									 ->setSubject("Reporte de Facturas por Convenios")
									 ->setDescription("Reporte generado para Office 2007 XLSX.")
									 ->setKeywords("office 2007 openxml php")
									 ->setCategory("Test report file");
		// Add some data
		$dato = '';
		if ($tipo == 1) {
			$dato="AND movimiento.situacion != 'A'";
		}
		$objPHPExcel->setActiveSheetIndex(0)
		            ->setCellValue('A1', 'FECHA')
		            ->setCellValue('B1', 'SERIE')
		            ->setCellValue('C1', 'NUMERO')
		            ->setCellValue('D1', 'PACIENTE')
		            ->setCellValue('E1', 'CLIENTE')
		            ->setCellValue('F1', 'RUC')
		            ->setCellValue('G1', 'SINIESTRO')
		            ->setCellValue('H1', 'STOTAL')
		            ->setCellValue('I1', 'IGV')
		            ->setCellValue('J1', 'TOTAL')
		            ->setCellValue('K1', 'RESPONSABLE');

		$query=$db->query("SELECT movimiento.fecha, movimiento.comentario, movimiento.serie, movimiento.numero, p.apellidopaterno, p.apellidomaterno, p.nombres, e.bussinesname, e.ruc, movimiento.subtotal, movimiento.igv, movimiento.total, res.nombres responsable FROM movimiento join person AS p on movimiento.persona_id = p.id join person as e on movimiento.empresa_id = e.id join person res on res.id = movimiento.responsable_id where movimiento.tipomovimiento_id = 9 ".$dato." AND movimiento.tipodocumento_id = 17 AND movimiento.fecha BETWEEN '$fi' AND '$ff' order by movimiento.fecha asc");
		$fila = 2; $totalf = 0;
		while($rows=$db->recorrerobj($query)){
		    $fecha = $rows->fecha;
		    $serie = $rows->serie;
		    $numero = $rows->numero;
		    $siniestro = $rows->comentario;
		    $apellidopaterno = $rows->apellidopaterno;
		    $apellidomaterno = $rows->apellidomaterno;
		    $nombres = $rows->nombres;
		    $bussinesname = $rows->bussinesname;
		    $ruc = $rows->ruc;
		    $subtotal = $rows->subtotal;
		    $igv = $rows->igv;
		    $total = $rows->total;
		    $responsable = $rows->responsable;

		    $fechai = date_create($fecha);

            $pac = $apellidopaterno.' '.$apellidomaterno.' '.$nombres;

		    $objPHPExcel->setActiveSheetIndex(0)
		            ->setCellValue('A'.$fila, date_format($fechai,'d-m-Y'))
		            ->setCellValue('B'.$fila, $serie)
		            ->setCellValue('C'.$fila, $numero)
		            ->setCellValue('D'.$fila, $pac)
		            ->setCellValue('E'.$fila, $bussinesname)
		            ->setCellValue('F'.$fila, $ruc)
		            ->setCellValue('G'.$fila, $siniestro)
		            ->setCellValue('H'.$fila, $subtotal)
		            ->setCellValue('I'.$fila, $igv)
		            ->setCellValue('J'.$fila, $total)
		            ->setCellValue('K'.$fila, $responsable);
		    $fila++; $totalf += $total;
		}

        $fila++;
	    $objPHPExcel->setActiveSheetIndex(0)
		            ->setCellValue('I'.$fila, 'TOTAL')
		            ->setCellValue('J'.$fila, $totalf);
		
		// Rename worksheet
		$objPHPExcel->getActiveSheet()->setTitle('FacturasConvenio');
		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		$objPHPExcel->setActiveSheetIndex(0);
		// Redirect output to a client’s web browser (Excel5)
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="FacturasConvenio'.$fi.'-'.$ff.'.xls"');
		header('Cache-Control: max-age=0');
		// If you're serving to IE 9, then the following may be needed
		header('Cache-Control: max-age=1');
		// If you're serving to IE over SSL, then the following may be needed
		header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
		header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
		header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
		header ('Pragma: public'); // HTTP/1.0
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
		exit;
	}

	function rInventarioV($ff, $lab, $origen, $presentacion){
        $response = '';
        $db = new Conexion();

        $pdf = new PDF('L','mm','A4');
        $pdf->AliasNbPages();
        $pdf->AddPage();

        $pdf->SetFillColor(255,255,255);
        $pdf->SetFont('Arial','B',14);

        $pdf->Cell(290,10,utf8_decode('Inventario Valorizado'),0,0,'C',0);
        $pdf->Ln();

    	if ($origen != 0) {
    		$query2 = $db->query("SELECT nombre from origen where id = '$origen'");

			while ($rows2=$db->recorrerobj($query2)) {
				$pdf->Cell(0,8,utf8_decode('Origen: '.$rows2->nombre),0,0,'C',1);
	        	$pdf->Ln();
			}
		}

		if ($presentacion != 0) {
    		$query2 = $db->query("SELECT nombre from presentacion where id = '$presentacion'");

			while ($rows2=$db->recorrerobj($query2)) {
				$pdf->Cell(0,8,utf8_decode('Presentación: '.$rows2->nombre),0,0,'C',1);
	        	$pdf->Ln();
			}
		}

        $pdf->SetFillColor(68,164,168);
        $pdf->SetFont('Arial','B',12);
        
        $pdf->Cell(15);
        $pdf->Cell(70,6,'Producto',1,0,'C',1);
        $pdf->Cell(35,6,utf8_decode('Presentación'),1,0,'C',1);
        $pdf->Cell(25,6,utf8_decode('Origen'),1,0,'C',1);
        $pdf->Cell(50,6,'Laboratorio',1,0,'C',1);
        $pdf->Cell(25,6,utf8_decode('Existencias'),1,0,'C',1);
        $pdf->Cell(25,6,utf8_decode('P.Compra'),1,0,'C',1);
        $pdf->Cell(20,6,utf8_decode('Valor'),1,0,'C',1);

    	$extra = '';
		if ($origen != 0) {
			$extra = 'WHERE p.origen_id = '.$origen;
			if ($presentacion != 0) {
    			$extra = $extra.' AND p.presentacion_id='.$presentacion;
    		}
		} else {
			if ($presentacion != 0) {
    			$extra = ' WHERE p.presentacion_id='.$presentacion;
    		}
		}

        $pdf->SetFont('Arial','',10);
        $pdf->SetFillColor(250,250,250);
        $pdf->Ln();

        $totals = 0;

        $query2 = $db->query("SELECT p.id, p.nombre producto, p.preciocompra, p.precioventa, p.preciokayros, p.origen_id, u.bussinesname proveedor, pr.nombre presentacion, l.nombre laboratorio, pp.principioactivo_id princicioa FROM producto p join person u on p.proveedor_id = u.id LEFT JOIN presentacion AS pr ON p.presentacion_id = pr.id LEFT JOIN laboratorio AS l ON p.laboratorio_id = l.id join productoprincipio pp on pp.producto_id = p.id ".$extra." ORDER BY p.nombre ASC");

		while ($rows2=$db->recorrerobj($query2)) {
			$pro_id = $rows2->id;
			$cantidad = 0;
			$query=$db->query("SELECT  stockactual as cantidad FROM kardex INNER JOIN detallemovimiento on kardex.detallemovimiento_id = detallemovimiento.id INNER JOIN movimiento on detallemovimiento.movimiento_id = movimiento.id WHERE movimiento.almacen_id = 1 and kardex.fecha <= '$ff' and detallemovimiento.producto_id = ".$rows2->id." order by kardex.id DESC limit 1");
			while($rows=$db->recorrerobj($query)){
			   $cantidad = $rows->cantidad;
			}
			$origen_id = $rows2->origen_id;
			$query=$db->query("SELECT nombre from origen where id = '$origen_id'");
			while($rows=$db->recorrerobj($query)){
			   $origen = $rows->nombre;
			}

			$principioactivop = $rows2->princicioa;
			$producto = $rows2->producto;
		    $presentacion = $rows2->presentacion;
		    $laboratorio = $rows2->laboratorio;
		    $compra = $rows2->preciocompra;
		    $venta = $rows2->precioventa;
		    $kayros = $rows2->preciokayros;
		    $totals += $compra*$cantidad;
		    
		    $pdf->Cell(15);
		    if(strlen($producto)>40){
				$pdf->SetFont('Arial','',8);
                $x=$pdf->GetX();
                $y=$pdf->GetY();                    
                $pdf->Multicell(50,3,utf8_decode($producto),0,'L');
                $pdf->SetXY($x,$y);
                $pdf->Cell(70,6.5,"",1,0,'C');
            }else{
            	$pdf->SetFont('Arial','',8);
                $pdf->Cell(70,6.5,utf8_decode($producto),1,0,'C',1);  
            }

	        $pdf->Cell(35,6.5,$presentacion,1,0,'C',1);
	        $pdf->Cell(25,6.5,$origen,1,0,'C',1);
	        if(strlen($laboratorio)>30){
				$pdf->SetFont('Arial','',8);
                $x=$pdf->GetX();
                $y=$pdf->GetY();                    
                $pdf->Multicell(50,3,utf8_decode($laboratorio),0,'L');
                $pdf->SetXY($x,$y);
                $pdf->Cell(50,6.5,"",1,0,'C');
            }else{
            	$pdf->SetFont('Arial','',8);
                $pdf->Cell(50,6.5,utf8_decode($laboratorio),1,0,'C',1);  
            }

	        $pdf->Cell(25,6.5,number_format($cantidad),1,0,'C',1);
	        $pdf->Cell(25,6.5,utf8_decode($compra),1,0,'C',1);
        	$pdf->Cell(20,6.5,'S/'.number_format($cantidad*$compra,2,'.',''),1,0,'C',1);
	        
			
			$pdf->Ln();

		}

		$pdf->Cell(245);
		$pdf->Cell(20,6.5,'S/'.$totals,1,0,'C',1);
            
        $pdf->Output();
    }

    function rInventarioVE($ff){
		$db = new Conexion();
		error_reporting(E_ALL);
		ini_set('display_errors', TRUE);
		ini_set('display_startup_errors', TRUE);
		if (PHP_SAPI == 'cli')
			die('This example should only be run from a Web Browser');
		/** Include PHPExcel */
		// Create new PHPExcel object
		$objPHPExcel = new PHPExcel();
		// Set document properties
		$objPHPExcel->getProperties()->setCreator("Garzatec")
									 ->setLastModifiedBy("Garzatec")
									 ->setTitle("Inventario Valorizado XLS")
									 ->setSubject("Inventario Valorizado")
									 ->setDescription("Reporte generado para Office 2007 XLSX.")
									 ->setKeywords("office 2007 openxml php")
									 ->setCategory("Test report file");
		
		$objPHPExcel->setActiveSheetIndex(0)
					->mergeCells('B2:C2')
					->setCellValue('B2', 'INVENTARIO VALORIZADO')
		            ->setCellValue('A3', 'PRODUCTO')
		            ->setCellValue('B3', 'PRESENTACIÓN')
		            ->setCellValue('C3', 'ORIGEN')
		            ->setCellValue('D3', 'LABORATORIO')
		            ->setCellValue('E3', 'P.COMPRA')
		            ->setCellValue('F3', 'EXISTENCIAS')
		            ->setCellValue('G3', 'VALOR');

		$query = $db->query("SELECT p.id, p.origen_id, p.nombre producto, p.preciocompra, p.precioventa, p.preciokayros, u.bussinesname proveedor, pr.nombre presentacion, l.nombre laboratorio, pp.principioactivo_id princicioa, an.descripcion as anaquel FROM producto p join person u on p.proveedor_id = u.id LEFT JOIN presentacion AS pr ON p.presentacion_id = pr.id LEFT JOIN laboratorio AS l ON p.laboratorio_id = l.id left join anaquel an on an.id=p.anaquel_id join productoprincipio pp on pp.producto_id = p.id ORDER BY p.nombre ASC");

		$fila = 4;
		$totals = 0;
		while($rows=$db->recorrerobj($query)){
			$pro_id = $rows->id;
			$origen = $rows->origen_id;
			$anaquel = $rows->anaquel;
			if ($origen == 1) {
				$origen = 'M';
			} else if ($origen == 4) {
				$origen = 'I';
			} else if ($origen == 6) {
				$origen = 'G';
			} else if ($origen == 8) {
				$origen = 'S';
			} else if ($origen == 9) {
				$origen = 'SO';
			}
			
			$cantidad = 0;
			$query2=$db->query("SELECT  stockactual as cantidad FROM kardex INNER JOIN detallemovimiento on kardex.detallemovimiento_id = detallemovimiento.id INNER JOIN movimiento on detallemovimiento.movimiento_id = movimiento.id WHERE movimiento.almacen_id = 1 and kardex.fecha < '$ff' and detallemovimiento.producto_id = ".$rows->id." order by kardex.id DESC limit 1");
			while($rows2=$db->recorrerobj($query2)){
			   $cantidad = $rows2->cantidad;
			}

			$producto = $rows->producto;
		    $presentacion = $rows->presentacion;
		    $laboratorio = $rows->laboratorio;
		    $proveedor = $rows->proveedor;
		    $preciocompra = $rows->preciocompra;
		    
		    $totals += $preciocompra*$cantidad;
		    $objPHPExcel->setActiveSheetIndex(0)
		            ->setCellValue('A'.$fila, $producto)
		            ->setCellValue('B'.$fila, $presentacion)
		            ->setCellValue('C'.$fila, $origen)
		            ->setCellValue('D'.$fila, $laboratorio)
		            ->setCellValue('E'.$fila, $preciocompra)
		            ->setCellValue('F'.$fila, $cantidad)
		            ->setCellValue('G'.$fila, 'S/'.$preciocompra*$cantidad);
		    $fila++;
		}

		$objPHPExcel->setActiveSheetIndex(0)
		            ->setCellValue('G'.$fila, 'S/'.$totals);

		
		// Rename worksheet
		$objPHPExcel->getActiveSheet()->setTitle('InventarioValorizado');
		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		$objPHPExcel->setActiveSheetIndex(0);
		// Redirect output to a client’s web browser (Excel5)
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="invval.xls"');
		header('Cache-Control: max-age=0');
		// If you're serving to IE 9, then the following may be needed
		header('Cache-Control: max-age=1');
		// If you're serving to IE over SSL, then the following may be needed
		header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
		header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
		header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
		header ('Pragma: public'); // HTTP/1.0
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
		exit;
    }

	function rKardexEE(){
		$db = new Conexion();
		error_reporting(E_ALL);
		ini_set('display_errors', TRUE);
		ini_set('display_startup_errors', TRUE);
		if (PHP_SAPI == 'cli')
			die('This example should only be run from a Web Browser');
		/** Include PHPExcel */
		// Create new PHPExcel object
		$objPHPExcel = new PHPExcel();
		// Set document properties
		$objPHPExcel->getProperties()->setCreator("Garzatec")
									 ->setLastModifiedBy("Garzatec")
									 ->setTitle("Reporte de Kardex XLS")
									 ->setSubject("Reporte de Kardex")
									 ->setDescription("Reporte generado para Office 2007 XLSX.")
									 ->setKeywords("office 2007 openxml php")
									 ->setCategory("Test report file");
		
		$objPHPExcel->setActiveSheetIndex(0)
					->mergeCells('I2:K2')
		            ->setCellValue('I2', 'MOVIMIENTOS')
		            ->setCellValue('A3', 'FECHA')
		            ->setCellValue('B3', 'HORA')
		            ->setCellValue('C3', 'CANTIDAD')
		            ->setCellValue('D3', 'TIPO')
		            ->setCellValue('E3', 'STOCK ACTUAL')
		            ->setCellValue('F3', 'detallemovimiento_id')
		            ->setCellValue('G3', 'movimiento_numero');

		$query=$db->query("SELECT k.fecha, TIME(k.created_at) hora, k.cantidad, k.tipo, k.lote_id, k.stockactual tdocument, k.detallemovimiento_id, movimiento.numero movimiento FROM kardex k join detallemovimiento dm on dm.id = k.detallemovimiento_id join movimiento on movimiento.id = dm.movimiento_id where k.FECHA BETWEEN '2017-12-24' and '2017-12-26' order by k.fecha, hora asc");

		$fila = 4;
		while($rows=$db->recorrerobj($query)){
		    $fecha = $rows->fecha;
		    $hora = $rows->hora;
		    $cantidad = $rows->cantidad;
		    $tipo = $rows->tipo;
		    $total = $rows->tdocument;
		    $detallemovimiento_id = $rows->detallemovimiento_id;
		    $numero = $rows->movimiento;

		    $objPHPExcel->setActiveSheetIndex(0)
		            ->setCellValue('A'.$fila, $fecha)
		            ->setCellValue('B'.$fila, $hora)
		            ->setCellValue('C'.$fila, $cantidad)
		            ->setCellValue('D'.$fila, $tipo)
		            ->setCellValue('E'.$fila, $total)
		            ->setCellValue('F'.$fila, $detallemovimiento_id)
		            ->setCellValue('G'.$fila, $numero);
		    $fila++;
		}

		
		// Rename worksheet
		$objPHPExcel->getActiveSheet()->setTitle('Kardex');
		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		$objPHPExcel->setActiveSheetIndex(0);
		// Redirect output to a client’s web browser (Excel5)
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="Kardex.xls"');
		header('Cache-Control: max-age=0');
		// If you're serving to IE 9, then the following may be needed
		header('Cache-Control: max-age=1');
		// If you're serving to IE over SSL, then the following may be needed
		header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
		header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
		header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
		header ('Pragma: public'); // HTTP/1.0
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
		exit;
	}

	function rProductosE($id, $tipo, $origen_id, $presentacion){
		$db = new Conexion();
		error_reporting(E_ALL);
		ini_set('display_errors', TRUE);
		ini_set('display_startup_errors', TRUE);
		if (PHP_SAPI == 'cli')
			die('This example should only be run from a Web Browser');
		/** Include PHPExcel */
		// Create new PHPExcel object
		$objPHPExcel = new PHPExcel();
		// Set document properties
		$objPHPExcel->getProperties()->setCreator("Garzatec")
									 ->setLastModifiedBy("Garzatec")
									 ->setTitle("Reporte de Productos XLS")
									 ->setSubject("Reporte de Productos")
									 ->setDescription("Reporte generado para Office 2007 XLSX.")
									 ->setKeywords("office 2007 openxml php")
									 ->setCategory("Test report file");
		
		$objPHPExcel->setActiveSheetIndex(0)
					->mergeCells('A2:I2')
					->setCellValue('A2', 'REPORTE DE INVENTARIO')
					->setCellValue('A3', 'ORIGEN')
		            ->setCellValue('B3', 'NOMBRE')
		            ->setCellValue('C3', 'PRESENTACIÓN')
		            ->setCellValue('D3', 'P. COMPRA')
		            ->setCellValue('E3', 'EXISTENCIAS')
		            ->setCellValue('F3', 'CONTEO')
		            ->setCellValue('G3', 'EGRESO')
		            ->setCellValue('H3', 'INGRESO')
		            ->setCellValue('I3', 'TOTAL')
		            ->setCellValue('J3', 'F.V')
		            ->setCellValue('K3', 'F.V NUEVO')
		            ->setCellValue('P3', 'IDPRODUCTO');


		$otroin = '';
        if ($id != 0) {
        	if ($tipo == 2) {
        		$otroin = ' left join especialidadfarmacia e on p.especialidadfarmacia_id = e.id ';
        		$extra = 'AND e.id = '.$id;
        	} else if($tipo == 1) {
        		$extra = 'AND p.id = '.$id;
        	} else if($tipo == 3) {
        		$extra = 'AND pp.principioactivo_id = '.$id;
        	}
        } else {
        	$extra = '';
    		if ($origen_id != 0) {
    			if($origen_id > 0){
    				$extra = 'AND p.origen_id = '.$origen_id;
    			}else{
					$extra = 'AND p.origen_id IS NULL ';
    			}
    			if ($presentacion != 0) {
	    			$extra = $extra.' AND p.presentacion_id='.$presentacion;
	    		}
    		} else {
    			if ($presentacion != 0) {
	    			$extra = ' AND p.presentacion_id='.$presentacion;
	    		}
    		}
        }

        $query2 = $db->query("SELECT p.id, p.nombre producto, p.anaquel_id anaquel, p.origen_id origen, p.preciocompra, p.precioventa, p.preciokayros, u.bussinesname proveedor, pr.nombre presentacion, l.nombre laboratorio, pp.principioactivo_id princicioa FROM producto p left join person u on p.proveedor_id = u.id LEFT JOIN presentacion AS pr ON p.presentacion_id = pr.id LEFT JOIN laboratorio AS l ON p.laboratorio_id = l.id left join productoprincipio pp on pp.producto_id = p.id ".$otroin." WHERE p.deleted_at is null ".$extra." group by p.id ORDER BY p.origen_id,p.nombre ASC");

		$fila = 4;
		while($rows2=$db->recorrerobj($query2)){
			$pro_id = $rows2->id;
			$origen = $rows2->origen;

			$cantidad = 0;

			$anaquel = $rows2->anaquel;
			if ($origen == 1) {
				$origen = 'M';
			} else if ($origen == 4) {
				$origen = 'I';
			} else if ($origen == 6) {
				$origen = 'G';
			} else if ($origen == 8) {
				$origen = 'S';
			} else if ($origen == 9) {
				$origen = 'SO';
			}
			$cantidad = 0;$fechavencimiento='';$preciocompra=0;
			$query=$db->query("SELECT kardex.cantidad,lote.fechavencimiento,kardex.preciocompra,kardex.stockactual,kardex.tipo FROM kardex LEFT JOIN detallemovimiento on detallemovimiento.id = kardex.detallemovimiento_id left JOIN movimiento on detallemovimiento.movimiento_id = movimiento.id left join lote on lote.id=kardex.lote_id WHERE kardex.deleted_at is NULL and movimiento.almacen_id = 1 and detallemovimiento.producto_id = ".$rows2->id." order by kardex.id desc limit 1");
			$tipo = "I";
			while($rows=$db->recorrerobj($query)){
			    $cantidad = $rows->stockactual;
			    $fechavencimiento = $rows->fechavencimiento;
			    $preciocompra = round($rows->preciocompra/1.18,2);
			    $tipo = $rows->tipo;
			}
			if($tipo=="S"){
				$query=$db->query("SELECT kardex.preciocompra FROM kardex LEFT JOIN detallemovimiento on detallemovimiento.id = kardex.detallemovimiento_id left JOIN movimiento on detallemovimiento.movimiento_id = movimiento.id left join lote on lote.id=kardex.lote_id WHERE kardex.deleted_at is NULL and movimiento.almacen_id = 1 and detallemovimiento.producto_id = ".$rows2->id." and kardex.tipo = 'I' AND kardex.preciocompra IS NOT NULL order by kardex.id desc limit 1");
				while($rows=$db->recorrerobj($query)){
				    $preciocompra = round($rows->preciocompra/1.18,2);
				}
			}

			$producto = $rows2->producto;
		    $presentacion = $rows2->presentacion;
		    $laboratorio = $rows2->laboratorio;
		    $proveedor = $rows2->proveedor;

		    $objPHPExcel->setActiveSheetIndex(0)
		    		->setCellValue('A'.$fila, $origen)
		            ->setCellValue('B'.$fila, $producto)
		            ->setCellValue('C'.$fila, $presentacion)
		            ->setCellValue('D'.$fila, $preciocompra)
		            ->setCellValue('E'.$fila, $cantidad)
		            ->setCellValue('F'.$fila, '')
		            ->setCellValue('G'.$fila, '')
		            ->setCellValue('H'.$fila, '')
		            ->setCellValue('I'.$fila, '')
		            ->setCellValue('J'.$fila, $fechavencimiento)
		            ->setCellValue('K'.$fila, '')
		            ->setCellValue('P'.$fila, $pro_id);
		    $fila++;
		}

		
		// Rename worksheet
		$objPHPExcel->getActiveSheet()->setTitle('Productos');
		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		$objPHPExcel->setActiveSheetIndex(0);
		// Redirect output to a client’s web browser (Excel5)
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="productos.xls"');
		header('Cache-Control: max-age=0');
		// If you're serving to IE 9, then the following may be needed
		header('Cache-Control: max-age=1');
		// If you're serving to IE over SSL, then the following may be needed
		header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
		header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
		header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
		header ('Pragma: public'); // HTTP/1.0
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
		exit;
    }

	function rComprasE($fi,$ff,$med,$pro){
		$db = new Conexion();
		error_reporting(E_ALL);
		ini_set('display_errors', TRUE);
		ini_set('display_startup_errors', TRUE);
		if (PHP_SAPI == 'cli')
			die('This example should only be run from a Web Browser');
		/** Include PHPExcel */
		// Create new PHPExcel object
		$objPHPExcel = new PHPExcel();
		// Set document properties
		$objPHPExcel->getProperties()->setCreator("Garzatec")
									 ->setLastModifiedBy("Garzatec")
									 ->setTitle("Reporte de Compras XLS")
									 ->setSubject("Reporte de Compras solicitado")
									 ->setDescription("Reporte generado para Office 2007 XLSX.")
									 ->setKeywords("office 2007 openxml php")
									 ->setCategory("Test report file");
		// Add some data
		if ($med == '0') {
			$objPHPExcel->setActiveSheetIndex(0)
		            ->setCellValue('A1', 'Reporte de Compras - TODO');
			$pro = '';
		} else if  ($med == '1'){
			$med = 'P';
			$objPHPExcel->setActiveSheetIndex(0)
		            ->setCellValue('A1', 'Reporte de Compras - PAGADO');
		} else if  ($med == '2'){
			$med = 'PP';
			$objPHPExcel->setActiveSheetIndex(0)
		            ->setCellValue('A1', 'Reporte de Compras - PENDIENTE');
		} else if  ($med == '3'){
			$med = '0';
			$query=$db->query("SELECT nombre FROM producto where id = '$pro'");
			while($rows=$db->recorrerobj($query)){
				$producto = $rows->nombre;
				$objPHPExcel->setActiveSheetIndex(0)
		            ->setCellValue('A1', 'Reporte de Compras - '.$producto);
			}
			$pro = "AND producto.id = '$pro' ";
		} else if  ($med == '4'){
			$med = '0';
			$objPHPExcel->setActiveSheetIndex(0)
		            ->setCellValue('A1', 'Reporte de Compras - ANULADOS');
			$pro = "AND situacion = 'A' ";
		}
		
		$objPHPExcel->setActiveSheetIndex(0)
					->mergeCells('I2:K2')
		            ->setCellValue('I2', 'cta. Contables')
		            ->setCellValue('A3', 'FECHA')
		            ->setCellValue('B3', 'PROVEEDOR')
		            ->setCellValue('C3', 'TIPO DOCUMENTO')
		            ->setCellValue('D3', 'DOCUMENTO')
		            ->setCellValue('E3', 'RUC')
		            ->setCellValue('F3', 'VALOR')
		            ->setCellValue('G3', 'IGV')
		            ->setCellValue('H3', 'TOTAL')
		            ->setCellValue('I3', '40')
		            ->setCellValue('J3', '42')
		            ->setCellValue('K3', '60')
		            ->setCellValue('L3', 'PERCEP.')
		            ->setCellValue('M3', 'RESPONSABLE');

		$tp=0;$pp=0;$yp=0;
		$med == '0' ? $q = $pro : $q = "AND movimiento.estadopago = '".$med."'";

		$query=$db->query("SELECT movimiento.fecha, movimiento.serie, movimiento.numero,  movimiento.igv, movimiento.total, movimiento.subtotal, movimiento.tipodocumento_id tipodocu, producto.nombre, kardex.cantidad, producto.preciocompra, producto.precioventa, producto.preciokayros, producto.afecto, p.bussinesname, p.ruc, u.nombres as usuario FROM detallemovimiento left JOIN movimiento on detallemovimiento.movimiento_id = movimiento.id left join person AS p on movimiento.persona_id = p.id left join person as u on movimiento.responsable_id = u.id left JOIN producto on detallemovimiento.producto_id = producto.id left JOIN kardex on detallemovimiento.id = kardex.detallemovimiento_id where movimiento.tipomovimiento_id = 3 ".$q." AND movimiento.fecha BETWEEN '$fi' AND '$ff' and movimiento.deleted_at is null group by movimiento.numero order by movimiento.id ASC");

		$fila = 4;
		$totaltotal = 0.00;
		while($rows=$db->recorrerobj($query)){
		    $fecha = $rows->fecha;

		    $datef = date_create($fecha);
 			$fecha = date_format($datef, 'd/m/Y');

		    $serie = $rows->serie;
		    $numero = $rows->numero;
		    $producto = $rows->nombre;
		    $cantidad = $rows->cantidad;
		    $preciocompra = $rows->preciocompra;
		    $precioventa = $rows->precioventa;
		    $preciokayros = $rows->preciokayros;
		    $proveedor = $rows->bussinesname;		    
		    $ruc = $rows->ruc;
		    $total = $rows->total;
		    $subtotal = $rows->subtotal;
		    $afecto = $rows->afecto;
		    $igv = $rows->igv;
		    $tipodoc = $rows->tipodocu;
		    $responsable = $rows->usuario;

		    if ($tipodoc == 7) {
		    	$tipodoc = 'BV';
		    } else if ($tipodoc == 6) {
		    	$tipodoc = 'FT';
		    } else if ($tipodoc == 12) {
		    	$tipodoc = 'TK';
		    }
		    
		    $ganancia = (100*($precioventa - $preciocompra))/$preciocompra;
		    $ganancia = round($ganancia, 2, PHP_ROUND_HALF_UP);
		    
		    if ($tipodoc == 'BV') {
		    	$valor = $total;
		    	$igv = 0;
		    } else {
		    	if ($afecto == 'SI') {
			    	$valor = $subtotal;
			    	$igv = $igv;
			    } else {
			    	$valor = $total;
			    	$igv = 0;
			    }
		    }

		    $objPHPExcel->setActiveSheetIndex(0)
		            ->setCellValue('A'.$fila, $fecha)
		            ->setCellValue('B'.$fila, $proveedor)
		            ->setCellValue('C'.$fila, $tipodoc)
		            ->setCellValue('D'.$fila, $serie.'-'.$numero)
		            ->setCellValue('E'.$fila, $ruc)
		            ->setCellValue('F'.$fila, number_format($valor,2,'.',''))
		            ->setCellValue('G'.$fila, number_format($igv,2,'.',''))
		            ->setCellValue('H'.$fila, number_format($total,2,'.',''))
		            ->setCellValue('I'.$fila, '401111')
		            ->setCellValue('J'.$fila, '421201')
		            ->setCellValue('K'.$fila, '601101')
		            ->setCellValue('L'.$fila, 0.00)
		            ->setCellValue('M'.$fila, $responsable);

			$totaltotal = $totaltotal + $total;
		    $fila++;
		}

		    $objPHPExcel->setActiveSheetIndex(0)
		            ->setCellValue('H'.$fila, number_format($totaltotal,2,'.',''));

		
		// Rename worksheet
		$objPHPExcel->getActiveSheet()->setTitle('Compras');
		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		$objPHPExcel->setActiveSheetIndex(0);
		// Redirect output to a client’s web browser (Excel5)
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="reporteCompras.xls"');
		header('Cache-Control: max-age=0');
		// If you're serving to IE 9, then the following may be needed
		header('Cache-Control: max-age=1');
		// If you're serving to IE over SSL, then the following may be needed
		header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
		header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
		header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
		header ('Pragma: public'); // HTTP/1.0
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
		exit;
	}

	function rVentasE($fi,$ff,$med,$pro){
		$db = new Conexion();
		error_reporting(E_ALL);
		ini_set('display_errors', TRUE);
		ini_set('display_startup_errors', TRUE);
		if (PHP_SAPI == 'cli')
			die('This example should only be run from a Web Browser');
		/** Include PHPExcel */
		// Create new PHPExcel object
		$objPHPExcel = new PHPExcel();
		// Set document properties
		$objPHPExcel->getProperties()->setCreator("Garzatec")
									 ->setLastModifiedBy("Garzatec")
									 ->setTitle("Reporte de Compras XLS")
									 ->setSubject("Reporte de Ventas solicitado")
									 ->setDescription("Reporte generado para Office 2007 XLSX.")
									 ->setKeywords("office 2007 openxml php")
									 ->setCategory("Test report file");
		// Add some data

		if ($med == '0') {
			$objPHPExcel->setActiveSheetIndex(0)
		            ->setCellValue('A1', 'Reporte de Ventas - TODO');
			$pro = '';
		} else if  ($med == '1'){
			$med = 'P';
			$objPHPExcel->setActiveSheetIndex(0)
		            ->setCellValue('A1', 'Reporte de Ventas - PAGADO');
		} else if  ($med == '2'){
			$med = 'PP';
			$objPHPExcel->setActiveSheetIndex(0)
		            ->setCellValue('A1', 'Reporte de Ventas - PENDIENTE');
		} else if  ($med == '3'){
			$med = '0';
			$query=$db->query("SELECT nombre FROM producto where id = '$pro'");
			while($rows=$db->recorrerobj($query)){
				$producto = $rows->nombre;
				$objPHPExcel->setActiveSheetIndex(0)
		            ->setCellValue('A1', 'Reporte de Ventas - '.$producto);
			}
			$pro = "AND producto.id = '$pro' ";
		} else if  ($med == '4'){
			$med = '0';
			$objPHPExcel->setActiveSheetIndex(0)
		            ->setCellValue('A1', 'Reporte de Ventas - ANULADOS');
			$pro = "AND situacion = 'A' ";
		} else if  ($med == '5'){
			$med = '0';
			$query=$db->query("SELECT nombres, apellidopaterno, apellidomaterno FROM person where dni = '$pro'");
			while($rows=$db->recorrerobj($query)){
				$producto = $rows->apellidopaterno.' '.$rows->apellidomaterno.' '.$rows->nombres;
				$objPHPExcel->setActiveSheetIndex(0)
		            ->setCellValue('A1', 'Reporte de Ventas - '.$producto);
			}
			$pro = "AND p.dni = '$pro' ";
		}
		
		$objPHPExcel->setActiveSheetIndex(0)
		            ->setCellValue('A3', 'FECHA')
		            ->setCellValue('B3', 'HORA')
		            ->setCellValue('C3', 'DOCUM')
		            ->setCellValue('D3', 'PRODUCTO')
		            ->setCellValue('E3', 'CLIENTE')
		            ->setCellValue('F3', 'CANTIDAD')
		            ->setCellValue('G3', 'PRECIO')
		            ->setCellValue('H3', 'TOTAL')
		            ->setCellValue('I3', 'ESTADO');

		$tp=0;$pp=0;$yp=0;
		$med == '0' ? $q = $pro : $q = "AND movimiento.estadopago = '".$med."'";

		$query=$db->query("SELECT DISTINCT movimiento.fecha, TIME(movimiento.created_at) hora, movimiento.numero, producto.nombre as producto, p.apellidopaterno, p.apellidomaterno, p.nombres, kardex.cantidad, producto.precioventa, movimiento.total, u.nombres as usuario, movimiento.estadopago FROM detallemovimiento inner JOIN movimiento on detallemovimiento.movimiento_id = movimiento.id inner join person AS p on movimiento.persona_id = p.id inner join person as u on movimiento.responsable_id = u.id inner JOIN producto on detallemovimiento.producto_id = producto.id JOIN kardex on detallemovimiento.id = kardex.detallemovimiento_id where movimiento.situacion <> 'U' AND movimiento.tipomovimiento_id = 4 AND movimiento.ventafarmacia = 'S' ".$q." AND movimiento.fecha BETWEEN '$fi' AND '$ff' order by movimiento.fecha, hora asc");
		$fila = 4;
		while($rows=$db->recorrerobj($query)){
		    $fecha = $rows->fecha;
		    $hora = $rows->hora;
		    $numero = $rows->numero;
		    $producto = $rows->producto;
		    $apellidopaterno = $rows->apellidopaterno;
		    $apellidomaterno = $rows->apellidomaterno;
		    $nombres = $rows->nombres;
		    $cantidad = $rows->cantidad;
		    $precioventa = $rows->precioventa;
		    //$total = $rows->total;
		    $total = $rows->precioventa*$rows->cantidad;
		    $estadopago = $rows->estadopago;		    
		    
		    $estadopago == 'P' ? $yp+=$total : $pp+=$total;
		    $estadopago == 'P' ? $estadopago = 'PAGADO' : $estadopago = 'PENDIENTE';

            $pac = $apellidopaterno.' '.$apellidomaterno.' '.$nombres;

			$tp += $total;

		    $objPHPExcel->setActiveSheetIndex(0)
		            ->setCellValue('A'.$fila, $fecha)
		            ->setCellValue('B'.$fila, $hora)
		            ->setCellValue('C'.$fila, $numero)
		            ->setCellValue('D'.$fila, $producto)
		            ->setCellValue('E'.$fila, $pac)
		            ->setCellValue('F'.$fila, $cantidad)
		            ->setCellValue('G'.$fila, $precioventa)
		            ->setCellValue('H'.$fila, $total)
		            ->setCellValue('I'.$fila, $estadopago);
		    $fila++;
		}

		$objPHPExcel->setActiveSheetIndex(0)
		            ->setCellValue('A'.$fila, 'PAGADO')
		            ->setCellValue('B'.$fila, $yp);
	    $fila++;
	    $objPHPExcel->setActiveSheetIndex(0)
		            ->setCellValue('C'.$fila, 'PENDIENTE')
		            ->setCellValue('D'.$fila, $pp);
        $fila++;
	    $objPHPExcel->setActiveSheetIndex(0)
		            ->setCellValue('E'.$fila, 'TOTAL')
		            ->setCellValue('F'.$fila, $tp);
		
		// Rename worksheet
		$objPHPExcel->getActiveSheet()->setTitle('Compras');
		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		$objPHPExcel->setActiveSheetIndex(0);
		// Redirect output to a client’s web browser (Excel5)
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="reporteVentas.xls"');
		header('Cache-Control: max-age=0');
		// If you're serving to IE 9, then the following may be needed
		header('Cache-Control: max-age=1');
		// If you're serving to IE over SSL, then the following may be needed
		header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
		header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
		header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
		header ('Pragma: public'); // HTTP/1.0
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
		exit;
	}

	function rGuiasconvenio($fi,$ff,$tipo,$indicio){
        $response = '';
        $db = new Conexion();

        $pdf = new PDF('P','mm','A4');
        $pdf->AliasNbPages();
        $pdf->AddPage();

        $pdf->SetFillColor(255,255,255);
        $pdf->SetFont('Arial','B',14);

        $pdf->Cell(190,10,utf8_decode('Reporte de Farmacia - Guías de Convenio'),0,0,'C',0);
        $pdf->Ln();
        $pdf->Cell(190,10,utf8_decode('Del '.$fi.' al '.$ff),0,0,'C',1);
        $pdf->Ln();

        $pdf->SetFillColor(68,164,168);
        $pdf->SetFont('Arial','B',12);
        
        $pdf->Cell(8);
        $pdf->Cell(25,6,'Fecha',1,0,'C',1);
        $pdf->Cell(22,6,'Tipo Doc.',1,0,'C',1);
        $pdf->Cell(20,6,'Nro',1,0,'C',1);
        $pdf->Cell(88,6,utf8_decode('Paciente'),1,0,'C',1);
        $pdf->Cell(25,6,utf8_decode('Total'),1,0,'C',1);

        $pdf->SetFont('Arial','',10);
        $pdf->SetFillColor(250,250,250);
        $pdf->Ln();

        $tp=0;

        $query=$db->query("SELECT movimiento.total, movimiento.fecha, movimiento.numero, paciente.nombres, paciente.apellidopaterno, paciente.apellidomaterno, m2.situacion as situacion2, m2.tipodocumento_id, m2.serie, m2.numero FROM movimiento left join person as paciente on movimiento.persona_id = paciente.id join person as responsable on responsable.id = movimiento.responsable_id left join movimiento as m2 on m2.id = movimiento.movimiento_id where movimiento.tipodocumento_id = 10 OR movimiento.tipodocumento_id = 15 AND movimiento.conveniofarmacia_id IS NOT null AND movimiento.fecha BETWEEN '$fi' AND '$ff' order by movimiento.fecha DESC");

        while($rows=$db->recorrerobj($query)){
            $fecha = $rows->fecha;
            $numero = $rows->numero;
            $situacion2 = $rows->situacion2;
            $total = $rows->total;
            $tipodocumento_id = $rows->tipodocumento_id;
            $apellidopaterno = $rows->apellidopaterno;
            $apellidomaterno = $rows->apellidomaterno;
            $nombres = $rows->nombres;
            $paciente = $apellidopaterno.' '.$apellidomaterno.' '.$nombres;

            $tipodocumento_id == 4 ? $tipodocumento_id = 'F' : $tipodocumento_id = 'G';
            
            $pdf->Cell(8);
            $pdf->Cell(25,6,$fecha,1,0,'C',1);
            $pdf->Cell(22,6,utf8_decode($tipodocumento_id),1,0,'C',1);
            $pdf->Cell(20,6,utf8_decode($numero),1,0,'C',1);
            //$pdf->Cell(80,6,utf8_decode($situacion2),1,0,'C',1);
            $pdf->Cell(88,6,utf8_decode($paciente),1,0,'C',1);
            $pdf->Cell(25,6,number_format($total,2,'.',''),1,0,'C',1);

            $pdf->Ln();

            $tp += $total;
        }

        $pdf->Ln();
        $pdf->Cell(107);
        $pdf->SetFont('Arial','B',11);
        $pdf->Cell(30,6,'TOTAL',1,0,'C',1);
        $pdf->Cell(30,6,$tp,1,0,'C',1);
            
        $pdf->Output();
    }

	function rMasVen($fi,$ff,$tipo,$filtro,$limite,$palabra,$orden,$origen){
        $response = '';
        $db = new Conexion();

        $pdf = new PDF('P','mm','A4');
        $pdf->AliasNbPages();
        $pdf->AddPage();

        $pdf->SetFillColor(255,255,255);
        $pdf->SetFont('Arial','B',14);

        if ($tipo == 1) {
        	$tcon = 'MÁS';
        	if ($orden != 0) {
        		$condi = 'stof desc';
        	} else {
        		$condi = 'cto DESC, precioventa desc';
        	}

        	if ($origen == 1) {
        		$orif = 'origen.id = 1';
        		$especificaorigen = 'M';
        	} else if ($origen == 2) {
        		$orif = 'origen.id = 6';
        		$especificaorigen = 'G';
        	} else if ($origen == 3) {
        		$orif = 'origen.id = 4';
        		$especificaorigen = 'I';
        	} else if ($origen == 4) {
        		$orif = 'origen.id = 8';
        		$especificaorigen = 'S';
        	} else if ($origen == 5) {
        		$orif = 'origen.id = 9';
        		$especificaorigen = 'SO';
        	}
        } else {
        	$tcon = 'MENOS';
        	if ($orden != 0) {
        		$condi = 'stof asc';
        	} else {
        		$condi = 'cto ASC, precioventa desc';
        	}

        	if ($origen == 1) {
        		$orif = 'origen.id = 1';
        		$especificaorigen = 'M';
        	} else if ($origen == 2) {
        		$orif = 'origen.id = 6';
        		$especificaorigen = 'G';
        	} else if ($origen == 3) {
        		$orif = 'origen.id = 4';
        		$especificaorigen = 'I';
        	} else if ($origen == 4) {
        		$orif = 'origen.id = 8';
        		$especificaorigen = 'S';
        	} else if ($origen == 5) {
        		$orif = 'origen.id = 9';
        		$especificaorigen = 'SO';
        	}
        }

        if ($limite != 0) {
        	$condi = $condi.' LIMIT '.$limite;
        	$pdf->Cell(0,10,utf8_decode('Medicamentos '.$tcon.' VENDIDOS - TOP '.$limite),0,0,'C',0);
        	$pdf->Ln();
        	$pdf->Cell(0,8,'Origen: '.$especificaorigen,0,0,'C',1);
        	$pdf->Ln();
        } else {
        	$pdf->Cell(0,10,utf8_decode('Medicamentos '.$tcon.' VENDIDOS'),0,0,'C',0);
        	$pdf->Ln();
        	$pdf->Cell(0,8,'Origen: '.$especificaorigen,0,0,'C',1);
        	$pdf->Ln();
        }

		$filtrop='';
        if ($filtro == 2) {
        	$filtrop = 'AND ef.id = '.$palabra;
        	//$pdf->Cell(190,8,utf8_decode('Especialidad: '.$tcon,0,0,'C',1);
        } 

        $pdf->Cell(190,10,utf8_decode($fi.' al '.$ff),0,0,'C',1);
        $pdf->Ln();

        $pdf->SetFillColor(68,164,168);
        $pdf->SetFont('Arial','B',9);
        
        $pdf->Cell(55,6,'Producto',1,0,'C',1);
        $pdf->Cell(40,6,'Especialidad',1,0,'C',1);
        $pdf->Cell(42,6,'Principio Activo',1,0,'C',1);
        $pdf->Cell(18,6,'P. Venta',1,0,'C',1);
        $pdf->Cell(20,6,'Cantidad',1,0,'C',1);
        $pdf->Cell(20,6,'Total Soles',1,0,'C',1);

        $pdf->SetFont('Arial','',8);
        $pdf->SetFillColor(250,250,250);
        $pdf->Ln();

        $query=$db->query("SELECT SUM(k.cantidad) cto, k.lote_id, k.stockactual, p.id p_id, p.nombre producto, p.precioventa, k.cantidad*p.precioventa as stof, ef.nombre esfa, lo.fechavencimiento FROM kardex k left join detallemovimiento dm on k.detallemovimiento_id = dm.id join lote lo on k.lote_id = lo.id join movimiento m on dm.movimiento_id = m.id join person u on u.id = m.responsable_id LEFT JOIN producto p on dm.producto_id = p.id join especialidadfarmacia ef on p.especialidadfarmacia_id = ef.id join origen on origen.id = p.origen_id where k.tipo = 'S' ".$filtrop." AND ".$orif." AND k.fecha BETWEEN '".$fi."' AND '".$ff."' group by p_id order by ".$condi);

		while($rows=$db->recorrerobj($query)){
			$p_id = $rows->p_id;
			$query2=$db->query('SELECT pa.id proaid, pa.nombre nombrep FROM productoprincipio pp join principioactivo pa on pa.id = pp.principioactivo_id where pp.producto_id = '.$p_id);
			while($rows2=$db->recorrerobj($query2)){
				$proaid = $rows2->proaid;
				$princiio = $rows2->nombrep;
			}
		    $cantidad = $rows->cto;
		    $producto = $rows->producto;
		    $especialidad = $rows->esfa;
		    $precioventa = $rows->precioventa;
		    $vence = $rows->fechavencimiento;
		    $stof = number_format(round($rows->stof,2),2,'.','');

		    if ($filtro == 3) {
	        	if ($proaid == $palabra) {
			    	if(strlen($producto)>34){
				$pdf->SetFont('Arial','',8);
                $x=$pdf->GetX();
                $y=$pdf->GetY();                    
                $pdf->Multicell(55,3,utf8_decode($producto),0,'L');
                $pdf->SetXY($x,$y);
                $pdf->Cell(55,6,"",1,0,'C');
                $pdf->SetFont('Arial','',8);
            }else{
            	
            	$pdf->Cell(55,6,utf8_decode($producto),1,0,'C',1);
            }
	        $pdf->Cell(40,6,$especialidad,1,0,'C',1);
	        if(strlen($princiio)>23){
				$pdf->SetFont('Arial','',8);
                $x=$pdf->GetX();
                $y=$pdf->GetY();                    
                $pdf->Multicell(42,3,utf8_decode($princiio),0,'C');
                $pdf->SetXY($x,$y);
                $pdf->Cell(42,6,"",1,0,'C');
                $pdf->SetFont('Arial','',8);
            }else{
            	
            	$pdf->Cell(42,6,utf8_decode($princiio),1,0,'C',1);
            }
	        $pdf->Cell(18,6,$precioventa,1,0,'C',1);
	        $pdf->Cell(20,6,intval($cantidad),1,0,'C',1);
	        $pdf->Cell(20,6,$stof,1,0,'R',1);
			
			$pdf->Ln();
			    }
        	} else {
			if(strlen($producto)>34){
				$pdf->SetFont('Arial','',8);
                $x=$pdf->GetX();
                $y=$pdf->GetY();                    
                $pdf->Multicell(55,3,utf8_decode($producto),0,'L');
                $pdf->SetXY($x,$y);
                $pdf->Cell(55,6,"",1,0,'C');
                $pdf->SetFont('Arial','',8);
            }else{
            	
            	$pdf->Cell(55,6,utf8_decode($producto),1,0,'C',1);
            }
	        $pdf->Cell(40,6,$especialidad,1,0,'C',1);
	        if(strlen($princiio)>23){
				$pdf->SetFont('Arial','',8);
                $x=$pdf->GetX();
                $y=$pdf->GetY();                    
                $pdf->Multicell(42,3,utf8_decode($princiio),0,'C');
                $pdf->SetXY($x,$y);
                $pdf->Cell(42,6,"",1,0,'C');
                $pdf->SetFont('Arial','',8);
            }else{
            	
            	$pdf->Cell(42,6,utf8_decode($princiio),1,0,'C',1);
            }
	        $pdf->Cell(18,6,$precioventa,1,0,'C',1);
	        $pdf->Cell(20,6,intval($cantidad),1,0,'C',1);
	        $pdf->Cell(20,6,$stof,1,0,'R',1);
			
			$pdf->Ln();
        	}
        
		}
			  
        $pdf->Output();
    }

	function rStock($min,$origen){
		$response = '';
		$db = new Conexion();

		$pdf = new PDF('L','mm','A4');
		$pdf->AliasNbPages();
		$pdf->AddPage();

		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','B',14);

		$orif='';
		if ($origen == 1) {
    		$orif = 'where origen. id = 1';
    		$especificaorigen = 'M';
    	} else if ($origen == 2) {
    		$orif = 'where origen. id = 6';
    		$especificaorigen = 'G';
    	} else if ($origen == 3) {
    		$orif = 'where origen. id = 4';
    		$especificaorigen = 'I';
    	} else if ($origen == 4) {
    		$orif = 'where origen. id = 8';
    		$especificaorigen = 'S';
    	} else if ($origen == 5) {
    		$orif = 'where origen. id = 9';
    		$especificaorigen = 'SO';
    	}

		if ($origen != 0) {
			$pdf->Cell(0,10,utf8_decode('Reporte de productos con stock menor a '.$min.' - Origen: '.$especificaorigen),0,0,'C',0);
		} else {
			$pdf->Cell(0,10,utf8_decode('Reporte de productos con stock menor a '.$min),0,0,'C',0);
		}
		$pdf->Ln();

		$pdf->SetFillColor(200,200,200);
		$pdf->SetFont('Arial','B',8);
		
		$pdf->Cell(6);
        $pdf->Cell(55,6,'Producto',1,0,'C',1);
        $pdf->Cell(30,6,utf8_decode('Presentación'),1,0,'C',1);
        $pdf->Cell(50,6,'Laboratorio',1,0,'C',1);
        $pdf->Cell(50,6,utf8_decode('Proovedor'),1,0,'C',1);
        $pdf->Cell(20,6,utf8_decode('Compra'),1,0,'C',1);
        $pdf->Cell(20,6,utf8_decode('Venta'),1,0,'C',1);
        $pdf->Cell(20,6,utf8_decode('Kayros'),1,0,'C',1);
        $pdf->Cell(25,6,utf8_decode('Existencias'),1,0,'C',1);

        $pdf->SetFont('Arial','',10);
        $pdf->SetFillColor(250,250,250);
        $pdf->Ln();
        $hoy = date("Y-m-d");

        $query2 = $db->query("SELECT p.id, p.nombre producto, p.preciocompra, p.precioventa, p.preciokayros, u.bussinesname proveedor, pr.nombre presentacion, l.nombre laboratorio FROM producto p join person u on p.proveedor_id = u.id LEFT JOIN presentacion AS pr ON p.presentacion_id = pr.id LEFT JOIN laboratorio AS l ON p.laboratorio_id = l.id join origen on origen.id = p.origen_id ".$orif." ORDER BY p.nombre ASC");

		while ($rows2=$db->recorrerobj($query2)) {
			$pid = $rows2->id;
			$query=$db->query("SELECT stockactual cantidad FROM kardex INNER JOIN detallemovimiento on kardex.detallemovimiento_id = detallemovimiento.id INNER JOIN movimiento on detallemovimiento.movimiento_id = movimiento.id WHERE movimiento.almacen_id = 1 and detallemovimiento.producto_id = ".$pid." order by kardex.id DESC LIMIT 1");
			
			while($rows=$db->recorrerobj($query)){
			    $prcantidad = $rows->cantidad;
			    if($prcantidad <= $min){
			    	$cantidad=$prcantidad;
			    }
			}

			if (isset($cantidad)) {
				$producto = $rows2->producto;
			    $presentacion = $rows2->presentacion;
			    $laboratorio = $rows2->laboratorio;
			    $proveedor = $rows2->proveedor;
			    $compra = $rows2->preciocompra;
			    $venta = $rows2->precioventa;
			    $kayros = $rows2->preciokayros;
			    
			    
			    $pdf->Cell(6);
			    if(strlen($producto)>34){
					$pdf->SetFont('Arial','',8);
	                $x=$pdf->GetX();
	                $y=$pdf->GetY();                    
	                $pdf->Multicell(55,3,utf8_decode($producto),0,'L');
	                $pdf->SetXY($x,$y);
	                $pdf->Cell(55,6.5,"",1,0,'C');
	            }else{
	            	$pdf->SetFont('Arial','',8);
	                $pdf->Cell(55,6.5,utf8_decode($producto),1,0,'C',1);  
	            }

		        $pdf->Cell(30,6.5,$presentacion,1,0,'C',1);
		        $pdf->Cell(50,6.5,$laboratorio,1,0,'C',1);
		        if(strlen($proveedor)>30){
					$pdf->SetFont('Arial','',8);
	                $x=$pdf->GetX();
	                $y=$pdf->GetY();                    
	                $pdf->Multicell(50,3,utf8_decode($proveedor),0,'L');
	                $pdf->SetXY($x,$y);
	                $pdf->Cell(50,6.5,"",1,0,'C');
	            }else{
	            	$pdf->SetFont('Arial','',8);
	                $pdf->Cell(50,6.5,utf8_decode($proveedor),1,0,'C',1);  
	            }

		        $pdf->Cell(20,6.5,utf8_decode($compra),1,0,'C',1);
	        	$pdf->Cell(20,6.5,utf8_decode($venta),1,0,'C',1);
	        	$pdf->Cell(20,6.5,utf8_decode($kayros),1,0,'C',1);
		        $pdf->Cell(25,6.5,number_format($cantidad),1,0,'C',1);
				
				$pdf->Ln();
				unset($cantidad);
			}
		}
			
		$pdf->Output();
	}

	function rMensajes($fi,$ff){
		$response = '';
		$db = new Conexion();
		$query=$db->query("SELECT m.fecha, m.hora, m.mensaje, u.login FROM mensaje as m inner join user as u on m.usuario_id = u.id WHERE fecha BETWEEN '$fi' AND '$ff' ");

		$pdf = new PDF();
		$pdf->AliasNbPages();
		$pdf->AddPage();

		$pdf->SetFont('Arial','B',10);
		$pdf->SetFillColor(150,194,200);
		$pdf->Cell(190,6,utf8_decode('Informe de Mensajes Enviados(Del '.$fi.' hasta '.$ff.')'),1,0,'C',0);
		$pdf->Ln();

		$pdf->SetFillColor(150,194,200);
		$pdf->SetFont('Arial','B',10);
		$pdf->Cell(20,6,'Fecha',1,0,'C',1);
		$pdf->Cell(20,6,'Hora',1,0,'C',1);
		$pdf->Cell(40,6,'Usuario',1,0,'C',1);
		$pdf->Cell(110,6,'Mensaje',1,0,'C',1);

		$pdf->SetFont('Arial','',10);
		$pdf->SetFillColor(250,250,250);
		$pdf->Ln();

		while($rows=$db->recorrerobj($query)){
			$fecha = $rows->fecha;
		    $hora = $rows->hora;
		    $mensaje = $rows->mensaje;
		    $usuario = $rows->login;
		    
		    $pdf->Cell(20,6,$fecha,1,0,'C',1);
		    $pdf->Cell(20,6,$hora,1,0,'C',1);
			$pdf->Cell(40,6,$usuario,1,0,'C',1);
			$pdf->Cell(110,6,utf8_decode($mensaje),1,0,'C',1);

			$pdf->Ln();
		}
		$pdf->Output();
	}

	function rFallecidos($fi,$ff){
		$response = '';
		$db = new Conexion();
		
		$meds = '';
		$pdf = new PDF('P','mm','A4');
		$pdf->AliasNbPages();
		$pdf->AddPage();

		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','B',12);
		$pdf->Cell(0,10,utf8_decode('PACIENTES FALLECIDOS ENTRE '.$fi.' Y '.$ff),0,0,'C',0);
		$pdf->Ln();

		$pdf->SetFillColor(68,164,168);
		$pdf->SetFont('Arial','B',10);
		$pdf->Cell(20,6,utf8_decode('Nº Historia'),1,0,'C',1);
		$pdf->Cell(15,6,'DNI',1,0,'C',1);
		$pdf->Cell(20,6,'Tipo Pac.',1,0,'C',1);
		$pdf->Cell(50,6,'Paciente',1,0,'C',1);
		$pdf->Cell(20,6,utf8_decode('Teléfono'),1,0,'C',1);
		$pdf->Cell(45,6,utf8_decode('Dirección'),1,0,'C',1);
		$pdf->Cell(22,6,'Fecha Fallec.',1,0,'C',1);

		$pdf->SetFont('Arial','',8);
		$pdf->SetFillColor(250,250,250);
		$pdf->Ln();

		$query=$db->query("SELECT h.numero, p.dni, h.tipopaciente, p.telefono, p.direccion, h.fechafallecido, p.apellidopaterno as 'pM', p.apellidomaterno as 'pP', p.nombres as 'pN' FROM historia h left join person as p on p.id = h.person_id WHERE h.fallecido = 'S' AND h.fechafallecido BETWEEN '$fi' AND '$ff' ORDER BY h.fechafallecido ASC");

		$med = '';
		while($rows=$db->recorrerobj($query)){
		    $numero = $rows->numero;
		    $dni = $rows->dni;
		    $telefono = $rows->telefono;
		    $direccion = $rows->direccion;
		    $fechafallecido = $rows->fechafallecido;
		    $tipopaciente = $rows->tipopaciente;
		    $paciente = ''.($rows->pM).' '.$rows->pP.' '.$rows->pN;
		    
		    $pdf->Cell(20,6.5,$numero,1,0,'C',1);
		    $pdf->Cell(15,6.5,$dni,1,0,'C',1);
			
			$pdf->Cell(20,6.5,$tipopaciente,1,0,'C',1);

			if(strlen($paciente)>24){
				$pdf->SetFont('Arial','',8);
                $x=$pdf->GetX();
                $y=$pdf->GetY();                    
                $pdf->Multicell(50,3,utf8_decode($paciente),0,'L');
                $pdf->SetXY($x,$y);
                $pdf->Cell(50,6.5,"",1,0,'C');
            }else{
            	$pdf->SetFont('Arial','',8);
                $pdf->Cell(50,6.5,utf8_decode($paciente),1,0,'C',1);
            }

			$pdf->Cell(20,6.5,$telefono,1,0,'C',1);
			
			if(strlen($direccion)>25){
				$pdf->SetFont('Arial','',8);
                $x=$pdf->GetX();
                $y=$pdf->GetY();                    
                $pdf->Multicell(45,3,utf8_decode($direccion),0,'L');
                $pdf->SetXY($x,$y);
                $pdf->Cell(45,6.5,"",1,0,'C');
            }else{
            	$pdf->SetFont('Arial','',8);
                $pdf->Cell(45,6.5,utf8_decode($direccion),1,0,'C',1);
            }

			$pdf->Cell(22,6.5,$fechafallecido,1,0,'C',1);

			$pdf->Ln();
		}
		$pdf->Output();
	}

	function rFallecidosE($fi,$ff){
		$db = new Conexion();
		error_reporting(E_ALL);
		ini_set('display_errors', TRUE);
		ini_set('display_startup_errors', TRUE);

		if (PHP_SAPI == 'cli')
			die('This example should only be run from a Web Browser');
		/** Include PHPExcel */
		// Create new PHPExcel object
		$objPHPExcel = new PHPExcel();
		// Set document properties
		$objPHPExcel->getProperties()->setCreator("Garzatec")
									 ->setLastModifiedBy("Garzatec")
									 ->setTitle("Reporte de Sala de Operaciones XLS")
									 ->setSubject("Reporte de Sala de Operaciones")
									 ->setDescription("Reporte generado para Office 2007 XLSX.")
									 ->setKeywords("office 2007 openxml php")
									 ->setCategory("Test report file");
		
		$objPHPExcel->setActiveSheetIndex(0)
				    ->setCellValue('A1', 'Nº Historia')
		            ->setCellValue('B1', 'DNI')
		            ->setCellValue('C1', 'TIPO PAC')
		            ->setCellValue('D1', 'PACIENTE')
		            ->setCellValue('E1', 'TELEFONO')
		            ->setCellValue('F1', 'DIRECCION')
		            ->setCellValue('G1', 'FECHA FALLECIMIENTO');

		$query=$db->query("SELECT h.numero, p.dni, h.tipopaciente, p.telefono, p.direccion, h.fechafallecido, p.apellidopaterno as 'pM', p.apellidomaterno as 'pP', p.nombres as 'pN' FROM historia h left join person as p on p.id = h.person_id WHERE h.fallecido = 'S' AND h.fechafallecido BETWEEN '$fi' AND '$ff' ORDER BY h.fechafallecido ASC");

		$fila = 2;
		while($rows=$db->recorrerobj($query)){
		    $numero = $rows->numero;
		    $dni = $rows->dni;
		    $telefono = $rows->telefono;
		    $direccion = $rows->direccion;
		    $fechafallecido = $rows->fechafallecido;
		    $tipopaciente = $rows->tipopaciente;
		    $paciente = ''.($rows->pM).' '.$rows->pP.' '.$rows->pN;
		    $objPHPExcel->setActiveSheetIndex(0)
		            ->setCellValue('A'.$fila, $numero)
		            ->setCellValue('B'.$fila, $dni)
		            ->setCellValue('C'.$fila, $tipopaciente)
		            ->setCellValue('D'.$fila, $paciente)
		            ->setCellValue('E'.$fila, $telefono)
		            ->setCellValue('F'.$fila, $direccion)
		            ->setCellValue('G'.$fila, $fechafallecido);
		    $fila++;

		}
		// Rename worksheet
		$objPHPExcel->getActiveSheet()->setTitle('Fallecidos');
		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		$objPHPExcel->setActiveSheetIndex(0);
		// Redirect output to a client’s web browser (Excel5)
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="fallecidos.xls"');
		header('Cache-Control: max-age=0');
		// If you're serving to IE 9, then the following may be needed
		header('Cache-Control: max-age=1');
		// If you're serving to IE over SSL, then the following may be needed
		header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
		header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
		header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
		header ('Pragma: public'); // HTTP/1.0
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
		exit;
	}

	function rHistoriasE($fi,$ff){
		$db = new Conexion();
		error_reporting(E_ALL);
		ini_set('display_errors', TRUE);
		ini_set('display_startup_errors', TRUE);

		if (PHP_SAPI == 'cli')
			die('This example should only be run from a Web Browser');
		/** Include PHPExcel */
		// Create new PHPExcel object
		$objPHPExcel = new PHPExcel();
		// Set document properties
		$objPHPExcel->getProperties()->setCreator("Garzatec")
									 ->setLastModifiedBy("Garzatec")
									 ->setTitle("Reporte de Sala de Operaciones XLS")
									 ->setSubject("Reporte de Sala de Operaciones")
									 ->setDescription("Reporte generado para Office 2007 XLSX.")
									 ->setKeywords("office 2007 openxml php")
									 ->setCategory("Test report file");
		
		$objPHPExcel->setActiveSheetIndex(0)
				    ->setCellValue('A1', 'FECHA REGISTRO')
		            ->setCellValue('B1', 'Nº Historia')
		            ->setCellValue('C1', 'DNI')
		            ->setCellValue('D1', 'PACIENTE')
		            ->setCellValue('E1', 'TIPO PAC')
		            ->setCellValue('F1', 'CONVENIO')
		            ->setCellValue('G1', 'TELEFONO')
		            ->setCellValue('H1', 'DIRECCION')
		            ->setCellValue('I1', 'SEXO')
		            ->setCellValue('J1', 'DEPARTAMENTO')
		            ->setCellValue('K1', 'PROVINCIA')
		            ->setCellValue('L1', 'DISTRITO')
		            ->setCellValue('M1', 'FECHA FALLECIMIENTO');

		$query=$db->query("SELECT h.numero, p.dni, h.tipopaciente, h.convenio_id, p.telefono, p.direccion, h.fecha, h.fallecido, h.fechafallecido, p.apellidopaterno as 'pM', p.apellidomaterno as 'pP', p.nombres as 'pN', d.nombre departamento, pro.nombre provincia, di.nombre distrito, p.sexo FROM historia h left join person as p on p.id = h.person_id join departamento d on d.id = h.departamento join provincia pro on pro.id = h.provincia join distrito di on di.id = h.distrito WHERE h.deleted_at is NULL and h.created_at BETWEEN '$fi' AND '$ff' ORDER BY h.fecha ASC");

		$fila = 2;
		while($rows=$db->recorrerobj($query)){

			// $numero = '';
		 //    $dni = '';
		 //    $telefono = '';
		 //    $direccion = '';
		 //    $fecha = '';
		 //    $fallecido = '';
	  //   	$fechafallecido = '';
		 //    $paciente = '';
		 //    $tipopaciente = '';
	  //   	$convenio = '';
		 //    $sexo = '';
	  //   	$departamento = '';
	  //   	$provincia = '';
	  //   	$distrito = '';

		    $numero = $rows->numero;
		    $dni = $rows->dni;
		    $telefono = $rows->telefono;
		    $direccion = $rows->direccion;
		    $fecha = $rows->fecha;
		    $fallecido = $rows->fallecido;
		    if ($fallecido != '') {
		    	$fechafallecido = $rows->fechafallecido;
		    } else {
		    	$fechafallecido = '';
	    	}
		    $paciente = ''.($rows->pM).' '.$rows->pP.' '.$rows->pN;
		    $tipopaciente = $rows->tipopaciente;
		    if ($tipopaciente == 'Convenio') {
		    	$query2=$db->query("SELECT nombre FROM convenio WHERE id = ".$rows->convenio_id);
		    	while($rows2=$db->recorrerobj($query2)){
		    		$convenio = $rows2->nombre;
		    	}
		    } else {
		    	$convenio = '';
	    	}
		    
	    	$sexo = $rows->sexo;
	    	$departamento = $rows->departamento;
	    	$provincia = $rows->provincia;
	    	$distrito = $rows->distrito;

		    $objPHPExcel->setActiveSheetIndex(0)
		            ->setCellValue('A'.$fila, $fecha)
		            ->setCellValue('B'.$fila, $numero)
		            ->setCellValue('C'.$fila, $dni)
		            ->setCellValue('D'.$fila, $paciente)
		            ->setCellValue('E'.$fila, $tipopaciente)
		            ->setCellValue('F'.$fila, $convenio)
		            ->setCellValue('G'.$fila, $telefono)
		            ->setCellValue('H'.$fila, $direccion)
		            ->setCellValue('I'.$fila, $sexo)
		            ->setCellValue('J'.$fila, $departamento)
		            ->setCellValue('K'.$fila, $provincia)
		            ->setCellValue('L'.$fila, $distrito)
		            ->setCellValue('M'.$fila, $fechafallecido);
		    $fila++;

		}
		// Rename worksheet
		$objPHPExcel->getActiveSheet()->setTitle('Pacientes');
		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		$objPHPExcel->setActiveSheetIndex(0);
		// Redirect output to a client’s web browser (Excel5)
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="historias.xls"');
		header('Cache-Control: max-age=0');
		// If you're serving to IE 9, then the following may be needed
		header('Cache-Control: max-age=1');
		// If you're serving to IE over SSL, then the following may be needed
		header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
		header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
		header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
		header ('Pragma: public'); // HTTP/1.0
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
		exit;
	}

	function rConsultaPagos($fi,$ff,$med,$rubro){
		$response = '';
		$db = new Conexion();
		
		$meds = '';
		$rubros = '';
		$pdf = new PDF('L','mm','A4');
		$pdf->AliasNbPages();
		$pdf->AddPage();

		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','B',12);
		$pdf->Cell(0,10,utf8_decode('CONSULTA DE PAGOS ENTRE '.$fi.' Y '.$ff),0,0,'C',0);
		$pdf->Ln();

		$pdf->SetFillColor(68,164,168);
		$pdf->SetFont('Arial','B',10);
		$pdf->Cell(20,6,utf8_decode('Fecha'),1,0,'C',1);
		$pdf->Cell(15,6,'Tipo Pac',1,0,'C',1);
		$pdf->Cell(20,6,'Paciente',1,0,'C',1);
		$pdf->Cell(50,6,'Plan',1,0,'C',1);
		$pdf->Cell(20,6,utf8_decode('Médico'),1,0,'C',1);
		$pdf->Cell(45,6,'Cantidad',1,0,'C',1);
		$pdf->Cell(22,6,'Servicio',1,0,'C',1);
		$pdf->Cell(15,6,'Pago Doc',1,0,'C',1);
		$pdf->Cell(15,6,'Pago Hosp',1,0,'C',1);
		$pdf->Cell(22,6,utf8_decode('Situación'),1,0,'C',1);
		$pdf->Cell(22,6,'Usuario',1,0,'C',1);

		$pdf->SetFont('Arial','',8);
		$pdf->SetFillColor(250,250,250);
		$pdf->Ln();

		if ($med != '') {
			$query=$db->query("SELECT apellidopaterno, apellidomaterno, nombres from person where id= '$med' ");
			while($rows=$db->recorrerobj($query)){
				$nmedico = ''.($rows->apellidopaterno).' '.($rows->apellidomaterno).' '.($rows->nombres);
				$meds = " s.medico_id = '$med' AND";
			}
		}

		$query=$db->query("SELECT r.nombres usuario, h.tipopaciente, p.apellidopaterno as 'pM', p.apellidomaterno as 'pP', p.nombres as 'pN', co.nombre plan, u.apellidopaterno, u.apellidomaterno, u.nombres FROM historia h left join convenio co on h.convenio_id = co.id person as u on s.medico_id = u.id left join person as p on p.id = h.person_id left join person r on s.usuario_id = r.id WHERE ".$rubros." ".$meds." s.fecha BETWEEN '$fi' AND '$ff' ORDER BY s.medico_id ASC");

		$med = '';
		while($rows=$db->recorrerobj($query)){
		    //$numero = $rows->numero;
		    $tipopaciente = $rows->tipopaciente;
		    $paciente = ''.($rows->pM).' '.$rows->pP.' '.$rows->pN;
		    $plan = $rows->plan;
		    $medico = ''.($rows->apellidopaterno).' '.$rows->apellidomaterno.' '.$rows->nombres;
		    $cantidad = 1;
		    //$rows->telefono;
		    $servicio = $rows->direccion;
		    $fechafallecido = $rows->fechafallecido;
		    
		    $pdf->Cell(20,6.5,$numero,1,0,'C',1);
		    $pdf->Cell(15,6.5,$dni,1,0,'C',1);
			
			$pdf->Cell(20,6.5,$tipopaciente,1,0,'C',1);


			if(strlen($paciente)>24){
				$pdf->SetFont('Arial','',8);
                $x=$pdf->GetX();
                $y=$pdf->GetY();                    
                $pdf->Multicell(50,3,utf8_decode($paciente),0,'L');
                $pdf->SetXY($x,$y);
                $pdf->Cell(50,6.5,"",1,0,'C');
            }else{
            	$pdf->SetFont('Arial','',8);
                $pdf->Cell(50,6.5,utf8_decode($paciente),1,0,'C',1);
            }

			$pdf->Cell(20,6.5,$telefono,1,0,'C',1);
			
			if(strlen($direccion)>25){
				$pdf->SetFont('Arial','',8);
                $x=$pdf->GetX();
                $y=$pdf->GetY();                    
                $pdf->Multicell(45,3,utf8_decode($direccion),0,'L');
                $pdf->SetXY($x,$y);
                $pdf->Cell(45,6.5,"",1,0,'C');
            }else{
            	$pdf->SetFont('Arial','',8);
                $pdf->Cell(45,6.5,utf8_decode($direccion),1,0,'C',1);
            }

			$pdf->Cell(22,6.5,$fechafallecido,1,0,'C',1);

			$pdf->Ln();
		}
		$pdf->Output();
	}

	function rConsultaPagosE($fi,$ff,$med,$rubro){
		$response = '';
		$db = new Conexion();
		
		$meds = '';
		$pdf = new PDF('L','mm','A4');
		$pdf->AliasNbPages();
		$pdf->AddPage();

		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','B',12);
		$pdf->Cell(0,10,utf8_decode('CONSULTA DE PAGOS ENTRE '.$fi.' Y '.$ff),0,0,'C',0);
		$pdf->Ln();

		$pdf->SetFillColor(68,164,168);
		$pdf->SetFont('Arial','B',10);
		$pdf->Cell(20,6,utf8_decode('Fecha'),1,0,'C',1);
		$pdf->Cell(15,6,'Tipo Pac',1,0,'C',1);
		$pdf->Cell(20,6,'Paciente',1,0,'C',1);
		$pdf->Cell(50,6,'Plan',1,0,'C',1);
		$pdf->Cell(20,6,utf8_decode('Médico'),1,0,'C',1);
		$pdf->Cell(45,6,'Cantidad',1,0,'C',1);
		$pdf->Cell(22,6,'Servicio',1,0,'C',1);
		$pdf->Cell(15,6,'Pago Doc',1,0,'C',1);
		$pdf->Cell(15,6,'Pago Hosp',1,0,'C',1);
		$pdf->Cell(22,6,utf8_decode('Situación'),1,0,'C',1);
		$pdf->Cell(22,6,'Usuario',1,0,'C',1);

		$pdf->SetFont('Arial','',8);
		$pdf->SetFillColor(250,250,250);
		$pdf->Ln();

		$query=$db->query("SELECT h.numero, p.dni, h.tipopaciente, p.telefono, p.direccion, h.fechafallecido, p.apellidopaterno as 'pM', p.apellidomaterno as 'pP', p.nombres as 'pN' FROM historia h left join person as p on p.id = h.person_id WHERE h.fallecido = 'S' AND h.fechafallecido BETWEEN '$fi' AND '$ff' ORDER BY h.fechafallecido ASC");

		$med = '';
		while($rows=$db->recorrerobj($query)){
		    $numero = $rows->numero;
		    $dni = $rows->dni;
		    $telefono = $rows->telefono;
		    $direccion = $rows->direccion;
		    $fechafallecido = $rows->fechafallecido;
		    $tipopaciente = $rows->tipopaciente;
		    $paciente = ''.($rows->pM).' '.$rows->pP.' '.$rows->pN;
		    
		    $pdf->Cell(20,6.5,$numero,1,0,'C',1);
		    $pdf->Cell(15,6.5,$dni,1,0,'C',1);
			
			$pdf->Cell(20,6.5,$tipopaciente,1,0,'C',1);

			if(strlen($paciente)>24){
				$pdf->SetFont('Arial','',8);
                $x=$pdf->GetX();
                $y=$pdf->GetY();                    
                $pdf->Multicell(50,3,utf8_decode($paciente),0,'L');
                $pdf->SetXY($x,$y);
                $pdf->Cell(50,6.5,"",1,0,'C');
            }else{
            	$pdf->SetFont('Arial','',8);
                $pdf->Cell(50,6.5,utf8_decode($paciente),1,0,'C',1);
            }

			$pdf->Cell(20,6.5,$telefono,1,0,'C',1);
			
			if(strlen($direccion)>25){
				$pdf->SetFont('Arial','',8);
                $x=$pdf->GetX();
                $y=$pdf->GetY();                    
                $pdf->Multicell(45,3,utf8_decode($direccion),0,'L');
                $pdf->SetXY($x,$y);
                $pdf->Cell(45,6.5,"",1,0,'C');
            }else{
            	$pdf->SetFont('Arial','',8);
                $pdf->Cell(45,6.5,utf8_decode($direccion),1,0,'C',1);
            }

			$pdf->Cell(22,6.5,$fechafallecido,1,0,'C',1);

			$pdf->Ln();
		}
		$pdf->Output();
	}

	function rSalaOpe($fi,$ff,$med,$sala){
		$response = '';
		$db = new Conexion();
		
		$meds = '';
		$pdf = new PDF('L','mm','A4');
		$pdf->AliasNbPages();
		$pdf->AddPage();

		switch ($sala) {
			case '0':
				$sala = "TODAS";
				$salas = "";
				break;
			
			case '1':
				$salas = "s.sala_id = '$sala' AND";
				$sala = "SALA I";
				break;

			case '2':
				$salas = "s.sala_id = '$sala' AND";
				$sala = "SALA II";
				break;

			case '3':
				$salas = "s.sala_id = '$sala' AND";
				$sala = "SALA PROCEDIMIENTOS";
				break;
		}

		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','B',13);
		$pdf->Cell(280,10,utf8_decode('SALA DE OPERACIONES ('.$fi.' HASTA '.$ff.')'),0,0,'C',0);
		$pdf->Ln();
		$query=$db->query("SELECT apellidopaterno, apellidomaterno, nombres from person where id= '$med' ");
		while($rows=$db->recorrerobj($query)){
			$nmedico = ''.($rows->apellidopaterno).' '.($rows->apellidomaterno).' '.($rows->nombres);
			$pdf->Cell(280,10,utf8_decode($sala.' - MÉDICO '.$nmedico),0,0,'C',1);
			$pdf->Ln();
			$meds = " s.medico_id = '$med' AND";
		}

		$pdf->SetFillColor(68,164,168);
		$pdf->SetFont('Arial','B',10);
		$pdf->Cell(20,6,'Fecha',1,0,'C',1);
		$pdf->Cell(15,6,'Inicio',1,0,'C',1);
		$pdf->Cell(15,6,'Fin',1,0,'C',1);
		$pdf->Cell(50,6,utf8_decode('Operación'),1,0,'C',1);
		$pdf->Cell(16,6,'Tipo Pac.',1,0,'C',1);
		$pdf->Cell(40,6,'Paciente',1,0,'C',1);
		$pdf->Cell(10,6,'Histo.',1,0,'C',1);
		$pdf->Cell(30,6,utf8_decode('Anestesiólogo'),1,0,'C',1);
		$pdf->Cell(30,6,'Instrumentista',1,0,'C',1);
		$pdf->Cell(15,6,'Paquete',1,0,'C',1);
		$pdf->Cell(14,6,'Sala',1,0,'C',1);
		$pdf->Cell(25,6,'Usuario',1,0,'C',1);

		$pdf->SetFont('Arial','',10);
		$pdf->SetFillColor(250,250,250);
		$pdf->Ln();

		$query=$db->query("SELECT r.nombres usuario, s.medico_id, s.fecha, s.horainicio, s.horafin, s.anestesiologo, s.instrumentista, s.paquete, s.ayudante2, s.operacion, h.tipopaciente, u.apellidopaterno, u.apellidomaterno, u.nombres, p.apellidopaterno as 'pM', p.apellidomaterno as 'pP', p.nombres as 'pN', sa.nombre salao, h.numero as nrohistoria FROM salaoperacion as s left join historia as h on h.id = s.historia_id left join person as u on s.medico_id = u.id left join person as p on p.id = h.person_id left join person r on s.usuario_id = r.id left join sala sa on sa.id = s.sala_id WHERE ".$salas." ".$meds." s.fecha BETWEEN '$fi' AND '$ff' ORDER BY s.medico_id ASC");

		$med = '';
		while($rows=$db->recorrerobj($query)){
		    $fecha = $rows->fecha;
		    $horainicio = $rows->horainicio;
		    $horafin = $rows->horafin;
		    $operacion = $rows->operacion;
		    $medico = ''.($rows->apellidopaterno).' '.$rows->apellidomaterno.' '.$rows->nombres;
		    $tipopaciente = $rows->tipopaciente;
		    $paciente = ''.($rows->pM).' '.$rows->pP.' '.$rows->pN;
		    $aneste = strtoupper($rows->anestesiologo);
		    $instrumen = $rows->instrumentista;
		    $paquete = $rows->paquete;
		    $salao = $rows->salao;
		    $usuario = $rows->usuario;
		    $nrohistoria = ltrim($rows->nrohistoria,"0");

		    if ($med != $medico) {
		    	$pdf->SetFillColor(150,194,200);
		    	$pdf->SetFont('Arial','B',10);
		    	$pdf->Cell(280,6,utf8_decode('MÉDICO: '.$medico),1,0,'L',1);
		    	$pdf->SetFillColor(250,250,250);
		    	$pdf->SetFont('Arial','',9);
		    	$pdf->Ln();
				$med = $medico;
		    }
		    
		    $pdf->Cell(20,6.5,$fecha,1,0,'C',1);
			$pdf->Cell(15,6.5,$horainicio,1,0,'C',1);
			$pdf->Cell(15,6.5,utf8_decode($horafin),1,0,'C',1);
			
			if(strlen($operacion)>25){
				$pdf->SetFont('Arial','',8);
                $x=$pdf->GetX();
                $y=$pdf->GetY();                    
                $pdf->Multicell(50,3,utf8_decode($operacion),0,'L');
                $pdf->SetXY($x,$y);
                $pdf->Cell(50,6.5,"",1,0,'C');
            }else{
            	$pdf->SetFont('Arial','',9);
                $pdf->Cell(50,6.5,utf8_decode($operacion),1,0,'C',1);    
            }
			
			$pdf->Cell(16,6.5,$tipopaciente,1,0,'C',1);

			if(strlen($paciente)>19){
				$pdf->SetFont('Arial','',8);
                $x=$pdf->GetX();
                $y=$pdf->GetY();                    
                $pdf->Multicell(40,3,utf8_decode($paciente),0,'L');
                $pdf->SetXY($x,$y);
                $pdf->Cell(40,6.5,"",1,0,'C');
            }else{
            	$pdf->SetFont('Arial','',9);
                $pdf->Cell(40,6.5,utf8_decode($paciente),1,0,'C',1);
            }

            $pdf->SetFont('Arial','',8);
            $pdf->Cell(10,6.5,$nrohistoria,1,0,'C',1);

            if(strlen($aneste)>13){
				$pdf->SetFont('Arial','',8);
                $x=$pdf->GetX();
                $y=$pdf->GetY();                    
                $pdf->Multicell(30,3,utf8_decode($aneste),0,'L');
                $pdf->SetXY($x,$y);
                $pdf->Cell(30,6.5,"",1,0,'C');
            }else{
            	$pdf->SetFont('Arial','',9);
                $pdf->Cell(30,6.5,$aneste,1,0,'C',1);
            }

			$pdf->Cell(30,6.5,$instrumen,1,0,'C',1);
			$pdf->Cell(15,6.5,$paquete,1,0,'C',1);
			$pdf->Cell(14,6.5,$salao,1,0,'C',1);

			if(strlen($usuario)>12){
				$pdf->SetFont('Arial','',8);
                $x=$pdf->GetX();
                $y=$pdf->GetY();                    
                $pdf->Multicell(25,3.25,utf8_decode($usuario),0,'L');
                $pdf->SetXY($x,$y);
                $pdf->Cell(25,6.5,"",1,0,'C');
            }else{
            	$pdf->SetFont('Arial','',9);
                $pdf->Cell(25,6.5,utf8_decode($usuario),1,0,'C',1);
            }
			$pdf->SetFont('Arial','',9);

			$pdf->Ln();
		}
		$pdf->Output();
	}

	function rSalaOpeE($fi,$ff,$med,$sala){
		$db = new Conexion();
		error_reporting(E_ALL);
		ini_set('display_errors', TRUE);
		ini_set('display_startup_errors', TRUE);

		$meds = '';

		switch ($sala) {
			case '0':
				$sala = "TODAS";
				$salas = "";
				break;
			
			case '1':
				$salas = "s.sala_id = '$sala' AND";
				$sala = "SALA I";
				break;

			case '2':
				$salas = "s.sala_id = '$sala' AND";
				$sala = "SALA II";
				break;

			case '3':
				$salas = "s.sala_id = '$sala' AND";
				$sala = "SALA PROCEDIMIENTOS";
				break;
		}

		$query=$db->query("SELECT apellidopaterno, apellidomaterno, nombres from person where id= '$med' ");
		while($rows=$db->recorrerobj($query)){
			$meds = " s.medico_id = '$med' AND";
		}

		if (PHP_SAPI == 'cli')
			die('This example should only be run from a Web Browser');
		/** Include PHPExcel */
		// Create new PHPExcel object
		$objPHPExcel = new PHPExcel();
		// Set document properties
		$objPHPExcel->getProperties()->setCreator("Garzatec")
									 ->setLastModifiedBy("Garzatec")
									 ->setTitle("Reporte de Sala de Operaciones XLS")
									 ->setSubject("Reporte de Sala de Operaciones")
									 ->setDescription("Reporte generado para Office 2007 XLSX.")
									 ->setKeywords("office 2007 openxml php")
									 ->setCategory("Test report file");
		
		$objPHPExcel->setActiveSheetIndex(0)
				    ->setCellValue('A1', 'FECHA')
		            ->setCellValue('B1', 'INICIO')
		            ->setCellValue('C1', 'FIN')
		            ->setCellValue('D1', 'MEDICO')
		            ->setCellValue('E1', 'OPERACION')
		            ->setCellValue('F1', 'TIPO PAC')
		            ->setCellValue('G1', 'PLAN')
		            ->setCellValue('H1', 'PACIENTE')
		            ->setCellValue('I1', 'HISTORIA')
		            ->setCellValue('J1', 'ANESTESIOLOGO')
		            ->setCellValue('K1', 'INSTRUMENTISTA')
		            ->setCellValue('L1', 'PAQUETE')
		            ->setCellValue('M1', 'SALA')
		            ->setCellValue('N1', 'USUARIO')
		            ->setCellValue('O1', 'SITUACION')
		            ->setCellValue('P1', 'OBSERVACION');

		$query=$db->query("SELECT r.nombres usuario, s.medico_id, s.fecha, s.horainicio, s.horafin, s.anestesiologo, s.instrumentista, s.paquete, s.ayudante2, s.operacion, h.tipopaciente, u.apellidopaterno, u.apellidomaterno, u.nombres, p.apellidopaterno as 'pM', p.apellidomaterno as 'pP', p.nombres as 'pN', sa.nombre salao, plan.nombre plan, h.numero as nrohistoria, s.situacion, s.comentario FROM salaoperacion as s left join historia as h on h.id = s.historia_id left join convenio on convenio.id = h.convenio_id left join plan on plan.id = convenio.plan_id left join person as u on s.medico_id = u.id left join person as p on p.id = h.person_id left join person r on s.usuario_id = r.id left join sala sa on sa.id = s.sala_id WHERE ".$salas." ".$meds." s.fecha BETWEEN '$fi' AND '$ff' ORDER BY s.medico_id ASC");

		$fila = 2;
		while($rows=$db->recorrerobj($query)){
		    $fecha = $rows->fecha;
		    //$nfecha = $fecha;
		    $nfecha = date("d/m/Y", strtotime($fecha));
		    $horainicio = $rows->horainicio;
		    $horafin = $rows->horafin;
		    $operacion = $rows->operacion;
		    $medico = ''.($rows->apellidopaterno).' '.$rows->apellidomaterno.' '.$rows->nombres;
		    $tipopaciente = $rows->tipopaciente;
		    $paciente = ''.($rows->pM).' '.$rows->pP.' '.$rows->pN;
		    $aneste = strtoupper($rows->anestesiologo);
		    $instrumen = $rows->instrumentista;
		    $paquete = $rows->paquete;
		    $salao = $rows->salao;
		    $usuario = $rows->usuario;
		    $plan = $rows->plan;
		    $situacion = $rows->situacion;
		    if($situacion=="A"){
		    	$situacion = "RECHAZADA";
		    }elseif($situacion=="C"){
		    	$situacion = "EJECUTADA";
		    }elseif($situacion=="P"){
		    	$situacion = "PENDIENTE";
		    }
		    $comentario = $rows->comentario;
		    $nrohistoria = ltrim($rows->nrohistoria,"0");
		    $objPHPExcel->setActiveSheetIndex(0)
		            ->setCellValue('A'.$fila, PHPExcel_Shared_Date::PHPToExcel($fecha))
		            ->setCellValue('B'.$fila, $horainicio)
		            ->setCellValue('C'.$fila, $horafin)
		            ->setCellValue('D'.$fila, rtrim(ltrim($medico)))
		            ->setCellValue('E'.$fila, $operacion)
		            ->setCellValue('F'.$fila, $tipopaciente)
		            ->setCellValue('G'.$fila, $plan)
		            ->setCellValue('H'.$fila, $paciente)
		            ->setCellValue('I'.$fila, $nrohistoria)
		            ->setCellValue('J'.$fila, $aneste)
		            ->setCellValue('K'.$fila, $instrumen)
		            ->setCellValue('L'.$fila, $paquete)
		            ->setCellValue('M'.$fila, $salao)
		            ->setCellValue('N'.$fila, $usuario)
		            ->setCellValue('O'.$fila, $situacion)
		            ->setCellValue('P'.$fila, $comentario);
        	$objPHPExcel->getActiveSheet()->getStyle('A'.$fila)->getNumberFormat()->setFormatCode("dd/mm/yyyy");
		    $fila++;
		}

		
		// Rename worksheet
		$objPHPExcel->getActiveSheet()->setTitle('Sala de Operaciones');
		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		$objPHPExcel->setActiveSheetIndex(0);
		// Redirect output to a client’s web browser (Excel5)
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="salaoperaciones.xls"');
		header('Cache-Control: max-age=0');
		// If you're serving to IE 9, then the following may be needed
		header('Cache-Control: max-age=1');
		// If you're serving to IE over SSL, then the following may be needed
		header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
		header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
		header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
		header ('Pragma: public'); // HTTP/1.0
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
		exit;
    }

    function dias_transcurridos($fecha_i,$fecha_f){
		$dias	= (strtotime($fecha_i)-strtotime($fecha_f))/86400;
		$dias 	= abs($dias); $dias = floor($dias);		
		return $dias;
	}

	function rHospM($fi,$ff,$med,$tipo){
		$response = '';
		$db = new Conexion();
		switch ($tipo) {
			case '0':
				$tipo = '';
				$titulo = 'HOSPITALIZACION - TODOS';
				break;
			
			case '1':
				$tipo = "hospitalizacion.modo = 'Tratamiento Medico' AND ";
				$titulo = 'HOSPITALIZACION - AMBULATORIO';
				break;

			case '2':
				$tipo = "hospitalizacion.modo = 'Tratamiento Quirurgico' AND ";
				$titulo = 'HOSPITALIZACION - CIRUGÌA';
				break;
		}

		$pdf = new PDF('L','mm','A4');
		$pdf->AliasNbPages();
		$pdf->AddPage();

		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','B',13);

		if ($med == '0') {
			$pdf->Cell(290,10,utf8_decode($titulo),0,0,'C',0);
			$pdf->Ln();

			$meds = '';
		} else {
			$query=$db->query("SELECT apellidopaterno, apellidomaterno, nombres from person where id= '$med' ");
			while($rows=$db->recorrerobj($query)){
				$nmedico = ''.($rows->apellidopaterno).' '.($rows->apellidomaterno).' '.($rows->nombres);
				$pdf->Cell(260,10,utf8_decode('HOSPITALIZACIONES A CARGO DEL MÉDICO '.$nmedico),0,0,'C',0);
				$pdf->Ln();
			}

			$meds = 'hospitalizacion.medico_id = '.$med.' AND';
		}	

		$pdf->SetFillColor(68,164,168);
		$pdf->SetFont('Arial','B',12);
	
		$pdf->Cell(10,6,utf8_decode('Nº'),1,0,'C',1);
		$pdf->Cell(20,6,'Fecha',1,0,'C',1);
		$pdf->Cell(15,6,'Hora',1,0,'C',1);
		$pdf->Cell(40,6,'Modo',1,0,'C',1);
		$pdf->Cell(40,6,'Medico',1,0,'C',1);
		$pdf->Cell(20,6,utf8_decode('Tipo Pac'),1,0,'C',1);
		$pdf->Cell(50,6,'Paciente',1,0,'C',1);
		$pdf->Cell(15,6,'Histo',1,0,'C',1);
		$pdf->Cell(15,6,utf8_decode('Hab'),1,0,'C',1);
		$pdf->Cell(20,6,'Alta',1,0,'C',1);
		$pdf->Cell(10,6,'Dias',1,0,'C',1);
		$pdf->Cell(25,6,'Usu. Alta',1,0,'C',1);

		$pdf->SetFont('Arial','',9);
		$pdf->SetFillColor(250,250,250);
		$pdf->Ln();


		$cont = 1;
		$query=$db->query("SELECT hospitalizacion.fecha, hospitalizacion.hora, hospitalizacion.medico_id, hospitalizacion.modo, hospitalizacion.fechaalta, habitacion.nombre as habita, person.apellidopaterno, person.apellidomaterno, person.nombres, historia.numero hnum, historia.tipopaciente, r.nombres usuario FROM hospitalizacion inner join habitacion on hospitalizacion.habitacion_id = habitacion.id inner join historia on hospitalizacion.historia_id = historia.id left join person on person.id = historia.person_id left join person r on hospitalizacion.usuarioalta_id = r.id WHERE ".$meds." ".$tipo."hospitalizacion.fecha BETWEEN '$fi' AND '$ff' ");
		while($rows=$db->recorrerobj($query)){

			$medico_id = $rows->medico_id;
			$query2=$db->query("SELECT apellidopaterno, apellidomaterno, nombres from person where id= '$medico_id' ");
			while($rows2=$db->recorrerobj($query2)){
				$nmedico = ''.($rows2->apellidopaterno).' '.($rows2->apellidomaterno).' '.($rows2->nombres);
			}

		    $fecha = $rows->fecha;
		    $hnum = $rows->hnum;
		    $hora = $rows->hora;
		    $modo = $rows->modo;
		    $tipopaciente = $rows->tipopaciente;
		    $paciente = ''.($rows->apellidopaterno).' '.$rows->apellidomaterno.' '.$rows->nombres;
		    $alta = $rows->fechaalta;
		    if ($alta == '0000-00-00') {
		    	$alta = 'NO';
		    }
		    $habitacion = $rows->habita;
		    $usuario = $rows->usuario;
		    
		    $pdf->Cell(10,6,$cont,1,0,'C',1);
		    $pdf->Cell(20,6,$fecha,1,0,'C',1);
			$pdf->Cell(15,6,$hora,1,0,'C',1);
			$pdf->Cell(40,6,utf8_decode($modo),1,0,'C',1);

			if(strlen($nmedico)>20){
				$pdf->SetFont('Arial','',8);
                $x=$pdf->GetX();
                $y=$pdf->GetY();                    
                $pdf->Multicell(40,3,utf8_decode($nmedico),0,'L');
                $pdf->SetXY($x,$y);
                $pdf->Cell(40,6,"",1,0,'C');
                $pdf->SetFont('Arial','',9);
            }else{
            	$pdf->SetFont('Arial','',9);
                $pdf->Cell(40,6,utf8_decode($nmedico),1,0,'C',1);
            }

			$pdf->Cell(20,6,utf8_decode($tipopaciente),1,0,'C',1);
			
			if(strlen($paciente)>24){
				$pdf->SetFont('Arial','',8);
                $x=$pdf->GetX();
                $y=$pdf->GetY();                    
                $pdf->Multicell(50,3,utf8_decode($paciente),0,'L');
                $pdf->SetXY($x,$y);
                $pdf->Cell(50,6,"",1,0,'C');
                $pdf->SetFont('Arial','',9);
            }else{
            	$pdf->SetFont('Arial','',9);
                $pdf->Cell(50,6,utf8_decode($paciente),1,0,'C',1);
            }
			$pdf->Cell(15,6,utf8_decode($hnum),1,0,'C',1);
			$pdf->Cell(15,6,utf8_decode($habitacion),1,0,'C',1);
			$pdf->Cell(20,6,utf8_decode($alta),1,0,'C',1);
			if ($alta != 'NO') {
				$dias = dias_transcurridos($fecha,$alta);
			} else {
				$hoy = date("Y-m-d");
				$dias = dias_transcurridos($fecha,$hoy);
			}
			$pdf->Cell(10,6,$dias,1,0,'C',1);
			$pdf->SetFont('Arial','',7.5);
			$pdf->Cell(25,6,utf8_decode($usuario),1,0,'C',1);
			$pdf->SetFont('Arial','',9);

			$cont++;
			$pdf->Ln();
		}

		$pdf->Output();
	}

	function rHospME($fi,$ff,$med,$tipo){
		$db = new Conexion();
		error_reporting(E_ALL);
		ini_set('display_errors', TRUE);
		ini_set('display_startup_errors', TRUE);
		switch ($tipo) {
			case '0':
				$tipo = '';
				$titulo = 'HOSPITALIZACION - TODOS';
				break;
			
			case '1':
				$tipo = "hospitalizacion.modo = 'Tratamiento Medico' AND ";
				$titulo = 'HOSPITALIZACION - AMBULATORIO';
				break;

			case '2':
				$tipo = "hospitalizacion.modo = 'Tratamiento Quirurgico' AND ";
				$titulo = 'HOSPITALIZACION - CIRUGÌA';
				break;
		}

		if ($med == '0') {
			$meds = '';
		} else {
			$meds = 'hospitalizacion.medico_id = '.$med.' AND';
		}	

		if (PHP_SAPI == 'cli')
			die('This example should only be run from a Web Browser');
		/** Include PHPExcel */
		// Create new PHPExcel object
		$objPHPExcel = new PHPExcel();
		// Set document properties
		$objPHPExcel->getProperties()->setCreator("Garzatec")
									 ->setLastModifiedBy("Garzatec")
									 ->setTitle("Reporte de Sala de Operaciones XLS")
									 ->setSubject("Reporte de Sala de Operaciones")
									 ->setDescription("Reporte generado para Office 2007 XLSX.")
									 ->setKeywords("office 2007 openxml php")
									 ->setCategory("Test report file");
		
		$objPHPExcel->setActiveSheetIndex(0)
				    ->setCellValue('A1', 'FECHA')
		            ->setCellValue('B1', 'HORA')
		            ->setCellValue('C1', 'MODO')
		            ->setCellValue('D1', 'MEDICO')
		            ->setCellValue('E1', 'TIPO PAC')
		            ->setCellValue('F1', 'PLAN')
		            ->setCellValue('G1', 'PACIENTE')
		            ->setCellValue('H1', 'HISTORIA')
		            ->setCellValue('I1', 'HABITACION')
		            ->setCellValue('J1', 'ALTA')
		            ->setCellValue('K1', 'DIAS')
		            ->setCellValue('L1', 'USUARIO ALTA');

		$query=$db->query("SELECT hospitalizacion.fecha, hospitalizacion.hora, hospitalizacion.medico_id, hospitalizacion.modo, hospitalizacion.fechaalta, habitacion.nombre as habita, person.apellidopaterno, person.apellidomaterno, person.nombres, historia.numero as hnum,historia.tipopaciente, r.nombres usuario, plan.nombre plan FROM hospitalizacion inner join habitacion on hospitalizacion.habitacion_id = habitacion.id inner join historia on hospitalizacion.historia_id = historia.id left join person on person.id = historia.person_id left join convenio on convenio.id = historia.convenio_id left join plan on plan.id = convenio.plan_id left join person r on hospitalizacion.usuarioalta_id = r.id WHERE ".$meds." ".$tipo."hospitalizacion.fecha BETWEEN '$fi' AND '$ff' ");

		$fila = 2;
		while($rows=$db->recorrerobj($query)){

			$medico_id = $rows->medico_id;
			$query2=$db->query("SELECT apellidopaterno, apellidomaterno, nombres from person where id= '$medico_id' ");
			while($rows2=$db->recorrerobj($query2)){
				$nmedico = ''.($rows2->apellidopaterno).' '.($rows2->apellidomaterno).' '.($rows2->nombres);
			}

		    $fecha = $rows->fecha;
		    $nfecha = date("d/m/Y", strtotime($fecha));
		    $hora = $rows->hora;
		    $hnum = intval($rows->hnum);
		    $modo = $rows->modo;
		    $plan = $rows->plan;
		    $tipopaciente = $rows->tipopaciente;
		    $paciente = ''.($rows->apellidopaterno).' '.$rows->apellidomaterno.' '.$rows->nombres;
		    $alta = $rows->fechaalta;
		    if ($alta == '0000-00-00') {
		    	$alta = 'NO';
		    } else {
		    	$alta = date("d/m/Y", strtotime($alta));
		    }

		    if ($alta != 'NO') {
				//$dias = dias_transcurridos($fecha,$alta);

				$datetime1 = new DateTime($rows->fecha);
				$datetime2 = new DateTime($rows->fechaalta);
				$interval = $datetime1->diff($datetime2);
				$dias = $interval->format('%R%a');
			} else {
				$hoy = date("Y-m-d");
				//$dias = dias_transcurridos($fecha,$hoy);
				$datetime1 = new DateTime($rows->fecha);
				$datetime2 = new DateTime($hoy);
				$interval = $datetime1->diff($datetime2);
				$dias = $interval->format('%R%a');
			}

		    $habitacion = intval($rows->habita);
		    $usuario = $rows->usuario;
		    $objPHPExcel->setActiveSheetIndex(0)
		            ->setCellValue('A'.$fila, $nfecha)
		            ->setCellValue('B'.$fila, $hora)
		            ->setCellValue('C'.$fila, $modo)
		            ->setCellValue('D'.$fila, $nmedico)
		            ->setCellValue('E'.$fila, $tipopaciente)
		            ->setCellValue('F'.$fila, $plan)
		            ->setCellValue('G'.$fila, $paciente)
		            ->setCellValue('H'.$fila, $hnum)
		            ->setCellValue('I'.$fila, $habitacion)
		            ->setCellValue('J'.$fila, $alta)
		            ->setCellValue('K'.$fila, $dias)
		            ->setCellValue('L'.$fila, $usuario);
		    $fila++;
		}

		
		// Rename worksheet
		$objPHPExcel->getActiveSheet()->setTitle('Sala de Operaciones');
		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		$objPHPExcel->setActiveSheetIndex(0);
		// Redirect output to a client’s web browser (Excel5)
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="salaoperaciones.xls"');
		header('Cache-Control: max-age=0');
		// If you're serving to IE 9, then the following may be needed
		header('Cache-Control: max-age=1');
		// If you're serving to IE over SSL, then the following may be needed
		header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
		header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
		header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
		header ('Pragma: public'); // HTTP/1.0
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
		exit;
    }

	function rHospP($fi,$ff,$med,$nmed,$mmed,$nombres,$tipo){
		$response = '';
		$pid = 0;

		switch ($tipo) {
			case '0':
				$tipo = '';
				$titulo = '';
				break;
			
			case '1':
				$tipo = "hospitalizacion.modo = 'Tratamiento Medico' AND ";
				$titulo = ' - AMBULATORIO';
				break;

			case '2':
				$tipo = "hospitalizacion.modo = 'Tratamiento Quirurgico' AND ";
				$titulo = ' - CIRUGÌA';
				break;
		}

		$db = new Conexion();

		$pdf = new PDF('L','mm','A4');
		$pdf->AliasNbPages();
		$pdf->AddPage();

		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','B',13);

		if ($med == '' && $nmed != '') {
			
			$nombres != '' ? $optn = "AND nombres ='".$nombres."'" : $optn = '';
			$mmed != '' ? $optm = "AND apellidomaterno ='".$mmed."' ".$optn : $optm = '';
			$omeds = "apellidopaterno = '".$nmed."' ".$optm;

			$query=$db->query("SELECT id, apellidopaterno, apellidomaterno, nombres from person where ".$omeds);
			while($rows=$db->recorrerobj($query)){
				$pid = $rows->id;
				$npaciente = ''.($rows->apellidopaterno).' '.($rows->apellidomaterno).' '.($rows->nombres);
				$pdf->Cell(290,10,utf8_decode('HOSPITALIZACIONES DEL PACIENTE '.$npaciente.''.$titulo),0,0,'C',0);
				$pdf->Ln();
				$meds = 'historia.person_id = '.$pid.' AND';
			}

		} else {
			if ($med != '') {
				$query=$db->query("SELECT id, apellidopaterno, apellidomaterno, nombres from person where dni= '$med' ");
				while($rows=$db->recorrerobj($query)){
					$pid = $rows->id;
					$npaciente = ''.($rows->apellidopaterno).' '.($rows->apellidomaterno).' '.($rows->nombres);
					$pdf->Cell(290,10,utf8_decode('HOSPITALIZACIONES DEL PACIENTE '.$npaciente),0,0,'C',0);
					$pdf->Ln();
				}

				$meds = 'historia.person_id = '.$pid.' AND';
			}
			else {
				$pdf->Cell(290,10,utf8_decode('HOSPITALIZACIONES GENERAL'),0,0,'C',0);
				$pdf->Ln();
				$meds = '';
			}
			
		}	

		$pdf->SetFillColor(68,164,168);
		$pdf->SetFont('Arial','B',12);

		$pdf->Cell(25);
		$pdf->Cell(20,6,'Fecha',1,0,'C',1);
		$pdf->Cell(20,6,'Hora',1,0,'C',1);
		$pdf->Cell(45,6,'Modo',1,0,'C',1);
		$pdf->Cell(25,6,'Alta',1,0,'C',1);
		$pdf->Cell(30,6,utf8_decode('Habitación'),1,0,'C',1);
		$pdf->Cell(90,6,utf8_decode('Médico'),1,0,'C',1);

		$pdf->SetFont('Arial','',10);
		$pdf->SetFillColor(250,250,250);
		$pdf->Ln();

		$query=$db->query("SELECT hospitalizacion.fecha, hospitalizacion.hora, hospitalizacion.modo, hospitalizacion.fechaalta, habitacion.nombre as habita, p.apellidopaterno, p.apellidomaterno, p.nombres FROM hospitalizacion inner join habitacion on hospitalizacion.habitacion_id = habitacion.id inner join historia on hospitalizacion.historia_id = historia.id left join person as p on p.id = hospitalizacion.medico_id WHERE ".$meds." ".$tipo." hospitalizacion.fecha BETWEEN '$fi' AND '$ff' ");
		while($rows=$db->recorrerobj($query)){
		    $fecha = $rows->fecha;
		    $hora = $rows->hora;
		    $modo = $rows->modo;
		    $alta = $rows->fechaalta;
		    if ($alta == '0000-00-00') {
		    	$alta = 'NO';
		    }
		    $habitacion = $rows->habita;
		    $medico = ''.($rows->apellidopaterno).' '.$rows->apellidomaterno.' '.$rows->nombres;
		    
		    $pdf->Cell(25);
		    $pdf->Cell(20,6,$fecha,1,0,'C',1);
			$pdf->Cell(20,6,$hora,1,0,'C',1);
			$pdf->Cell(45,6,utf8_decode($modo),1,0,'C',1);
			$pdf->Cell(25,6,utf8_decode($alta),1,0,'C',1);
			$pdf->Cell(30,6,utf8_decode($habitacion),1,0,'C',1);
			$pdf->Cell(90,6,utf8_decode($medico),1,0,'C',1);

			$pdf->Ln();
		}

		$pdf->Output();
	}

	function rHospG($fi,$ff){
		$response = '';
		$db = new Conexion();

		$pdf = new PDF();
		$pdf->AliasNbPages();
		$pdf->AddPage();

		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','B',14);
		$pdf->Cell(190,10,utf8_decode('HOSPITALIZACIONES ('.$fi.' HASTA '.$ff.')'),0,0,'C',0);
		$pdf->Ln();

		$pdf->SetFillColor(68,164,168);
		$pdf->SetFont('Arial','B',12);
		$pdf->Cell(20,6,'Fecha',1,0,'C',1);
		$pdf->Cell(15,6,'Hora',1,0,'C',1);
		$pdf->Cell(40,6,'Modo',1,0,'C',1);
		$pdf->Cell(20,6,'Alta',1,0,'C',1);
		$pdf->Cell(23,6,utf8_decode('Habitación'),1,0,'C',1);
		$pdf->Cell(70,6,'Paciente',1,0,'C',1);

		$pdf->SetFont('Arial','',9);
		$pdf->SetFillColor(250,250,250);
		$pdf->Ln();

		$query=$db->query("SELECT hospitalizacion.fecha, hospitalizacion.hora, hospitalizacion.modo, hospitalizacion.paquete, hospitalizacion.fechaalta, habitacion.nombre as habita, historia.numero as histo, person.apellidopaterno, person.apellidomaterno, person.nombres FROM hospitalizacion inner join habitacion on hospitalizacion.habitacion_id = habitacion.id inner join historia on hospitalizacion.historia_id = historia.id left join person on person.id = historia.person_id WHERE hospitalizacion.fecha BETWEEN '$fi' AND '$ff' ");
		while($rows=$db->recorrerobj($query)){
		    $fecha = $rows->fecha;
		    $hora = $rows->hora;
		    $modo = $rows->modo;
		    $paquete = $rows->paquete;
		    $alta = $rows->fechaalta;
		    if ($alta == '0000-00-00') {
		    	$alta = 'NO';
		    }
		    $habitacion = $rows->habita;
		    $historia = ''.($rows->apellidopaterno).' '.$rows->apellidomaterno.' '.$rows->nombres;
		    
		    $pdf->Cell(20,6,$fecha,1,0,'C',1);
			$pdf->Cell(15,6,$hora,1,0,'C',1);
			$pdf->Cell(40,6,utf8_decode($modo),1,0,'C',1);
			$pdf->Cell(20,6,utf8_decode($alta),1,0,'C',1);
			$pdf->Cell(23,6,utf8_decode($habitacion),1,0,'C',1);
			$pdf->Cell(70,6,utf8_decode($historia),1,0,'C',1);

			$pdf->Ln();
		}

		$pdf->Output();
	}

	function rCaja($fi,$ff,$med){
		$response = '';
		$db = new Conexion();

		$pdf = new PDF('L','mm','A4');
		$pdf->AliasNbPages();
		$pdf->AddPage();

		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','B',12);
		$query=$db->query("SELECT id, nombre FROM caja WHERE id = '$med' ");
		while($rows=$db->recorrerobj($query)){
			$nombc = $rows->nombre;
			$pdf->Cell(45);
			$pdf->Cell(190,10,utf8_decode('Detalle de Caja - '.$nombc.' ('.$fi.' hasta '.$ff.')'),0,0,'C',0);
			$pdf->Ln();
		}

		$pdf->SetFillColor(68,164,168);
		$pdf->SetFont('Arial','B',9);
		$pdf->Cell(65,6,'PERSONA',1,0,'C',1);
		$pdf->Cell(15,6,'NRO',1,0,'C',1);
		$pdf->Cell(45,6,'EMPRESA',1,0,'C',1);
		$pdf->Cell(70,6,'CONCEPTO',1,0,'C',1);
		$pdf->Cell(20,6,'EGRESO',1,0,'C',1);
		$pdf->Cell(20,6,'INGRESO',1,0,'C',1);
		$pdf->Cell(20,6,'TARJETA',1,0,'C',1);
		$pdf->Cell(25,6,'DOCTOR',1,0,'C',1);

		$pdf->SetFont('Arial','',10);
		$pdf->SetFillColor(250,250,250);
		$pdf->Ln();

		if($med==1){//ADMISION 1
                $serie=3;
        }elseif($med==2){//ADMISION 2
            $serie=7;
        }elseif($med==3){//CONVENIOS
            $serie=8;
        }elseif($med==5){//EMERGENCIA
            $serie=9;
        }elseif($med==4){//FARMACIA
            $serie=4;
        }

		$query=$db->query("SELECT m.numero, m.serie, m.situacion, m2.situacion as situacion2, m.fecha, m.tipotarjeta, m.total, m.comentario, p.apellidopaterno, p.apellidomaterno, p.nombres, responsable.nombres as respn,cp.id as conceptopago_id, cp.nombre as conce, cp.tipo as tic FROM movimiento as m left join person as p on p.id = m.persona_id join person as responsable on responsable.id = m.responsable_id join conceptopago as cp on m.conceptopago_id = cp.id left join movimiento as m2 on m2.id = m.movimiento_id WHERE m.caja_id = '$med' AND m.fecha BETWEEN '$fi' AND '$ff' order by m.id desc");

		$fec = '';
		$pv = 0;
		$ingreso=0;$egreso=0;$garantia=0;$efectivo=0;$master=0;$visa=0;$saldo=0;$ingresos='';$egresos='';
		$sig = 2;
		$responsable = '';
		while($rows=$db->recorrerobj($query)){
			$fecha = $rows->fecha;
			$total = $rows->total;
			$tarjeta = $rows->tipotarjeta;
		    $mov = $rows->tic;
		    $responsable = $rows->respn;

			if ($sig == 1) {
				$sig = 0;
			}

			if ($pv == 0) {
				$pdf->SetFillColor(150,194,200);
		    	$pdf->SetFont('Arial','B',10);
		    	$pdf->Cell(280,6,utf8_decode('DÍA: '.$fecha.' RESPONSABLE: '.$responsable),1,0,'L',1);
		    	$pdf->SetFillColor(250,250,250);
		    	$pdf->SetFont('Arial','',9);
		    	$pdf->Ln();
		    	$fec = $fecha;			
		    	$pv++;
		    }

		    if ($med != 5) {
				if ($rows->conceptopago_id==2) {
			    	if ($pv != 0) {
			    		$pdf->Ln();
				    	$pdf->Cell(119);
				    	$pdf->SetFont('Arial','B',9);
				    	$pdf->Cell(40,6,utf8_decode('RESÚMEN DEL DÍA'),1,0,'C',1);
				    	$pdf->Ln();
				    	$pdf->Cell(119);
						$pdf->Cell(20,6,'Ingresos',1,0,'C',1);
						$pdf->Cell(20,6,number_format($ingreso,2,'.',''),1,0,'C',1);
						$pdf->Ln();
						$pdf->Cell(119);
						$pdf->SetFont('Arial','',9);
						$pdf->Cell(20,6,'Efectivo',1,0,'C',1);
						$pdf->Cell(20,6,number_format($efectivo,2,'.',''),1,0,'C',1);
						$pdf->Ln();
						$pdf->Cell(119);
						$pdf->Cell(20,6,'VISA',1,0,'C',1);
						$pdf->Cell(20,6,number_format($visa,2,'.',''),1,0,'C',1);
						$pdf->Ln();
						$pdf->Cell(119);
						$pdf->Cell(20,6,'MASTER',1,0,'C',1);
						$pdf->Cell(20,6,number_format($master,2,'.',''),1,0,'C',1);
						$pdf->Ln();
						$pdf->Cell(119);
						$pdf->SetFont('Arial','B',9);
						$pdf->Cell(20,6,'Egresos',1,0,'C',1);
						$pdf->Cell(20,6,number_format($egreso,2,'.',''),1,0,'C',1);
						$pdf->Ln();
						$pdf->Cell(119);
						$pdf->Cell(20,6,'Saldo',1,0,'C',1);
						$pdf->Cell(20,6,number_format($saldo,2,'.',''),1,0,'C',1);
						$pdf->Ln();
						$pdf->Cell(119);
						$pdf->Cell(20,6,'Garantia',1,0,'C',1);
						$pdf->Cell(20,6,number_format($garantia,2,'.',''),1,0,'C',1);
						$pdf->Ln();
						$pdf->Ln();
						$ingreso=0;$egreso=0;$garantia=0;$efectivo=0;$master=0;$visa=0;
			    	}

			    	$pdf->SetFillColor(150,194,200);
			    	$pdf->SetFont('Arial','B',10);
			    	$pdf->Cell(280,6,utf8_decode('DÍA: '.$fecha),1,0,'L',1);
			    	$pdf->SetFillColor(250,250,250);
			    	$pdf->SetFont('Arial','',9);
			    	$pdf->Ln();
			    	$fec = $fecha;
			    	$pv++;
			    }

			    if ($mov == 'I') {
					if($rows->conceptopago_id<>10){//Garantias
						$ingreso += $total;
						if ($tarjeta == 'VISA') {
							$visa += $total;
						} else if($tarjeta == 'MASTER'){
							$master += $total;
						} else {
							$tarjeta = '-';
							$ingresos = $total;
							$egresos = '';
							$efectivo += $total;
						}
					}else{
						$garantia += $total;
					}
				} else {
					$egresos = $total;
					$ingresos = '';
					$egreso += $total;
				}
				
				$saldo = $ingreso - $egreso;

			    $conce = $rows->conce;
			    $person = ''.($rows->apellidopaterno).' '.($rows->apellidomaterno).' '.($rows->nombres);
			    $person = trim($person);

			    $situacion = $rows->situacion;
			    if ($situacion == 'N') {
			    	$situacion = 'OK';
			    } else if ($situacion == 'A'){
			    	$situacion = 'Anulado';
			    }

			    $comen = $rows->comentario;		   

	            if(strlen($person)>35){
					$pdf->SetFont('Arial','',8);
	                $x=$pdf->GetX();
	                $y=$pdf->GetY();                    
	                $pdf->Multicell(65,3,utf8_decode($person),0,'L');
	                $pdf->SetXY($x,$y);
	                $pdf->Cell(65,6.5,"",1,0,'C');
	            }else{
	            	$pdf->SetFont('Arial','',8);
	                $pdf->Cell(65,6.5,utf8_decode($person),1,0,'C',1);    
	            }

	            $numero = $rows->numero;
	            if ($conce != 'a') {
	            	$aserie = '';
	            	$aserie = 'B'.$serie.'-'.$numero;
	            }
				$pdf->Cell(15,6.5,$aserie,1,0,'C',1);
				$pdf->Cell(45,6.5,utf8_decode($conce),1,0,'C',1);

				
	            $pdf->SetFont('Arial','',9);

				if(strlen($comen)>50){
					$pdf->SetFont('Arial','',8);
	                $x=$pdf->GetX();
	                $y=$pdf->GetY();                    
	                $pdf->Multicell(70,3,utf8_decode($comen),0,'L');
	                $pdf->SetXY($x,$y);
	                $pdf->Cell(70,6.5,"",1,0,'C');
	            }else{
	            	$pdf->Cell(70,6.5,utf8_decode($comen),1,0,'C',1);    
	            }

	            $pdf->Cell(20,6.5,utf8_decode($egresos),1,0,'C',1);
	            $pdf->Cell(20,6.5,utf8_decode($ingresos),1,0,'C',1);
	            $pdf->Cell(20,6.5,utf8_decode($tarjeta),1,0,'C',1);

	            $pdf->SetFont('Arial','',9);
				//$pdf->Cell(80,6,utf8_decode($comen),1,0,'C',1);

				$pdf->Cell(25,6.5,$situacion,1,0,'C',1);

				$pdf->Ln();
			} else {
				if ($rows->conceptopago_id==1) {
					$sig = 1;
				}

				if ($sig == 0) {
					
					$sig = 2;	
					if ($pv != 0) {
			    		$pdf->Ln();
				    	$pdf->Cell(119);
				    	$pdf->SetFont('Arial','B',9);
				    	$pdf->Cell(40,6,utf8_decode('RESÚMEN DEL DÍA'),1,0,'C',1);
				    	$pdf->Ln();
				    	$pdf->Cell(119);
						$pdf->Cell(20,6,'Ingresos',1,0,'C',1);
						$pdf->Cell(20,6,number_format($ingreso,2,'.',''),1,0,'C',1);
						$pdf->Ln();
						$pdf->Cell(119);
						$pdf->SetFont('Arial','',9);
						$pdf->Cell(20,6,'Efectivo',1,0,'C',1);
						$pdf->Cell(20,6,number_format($efectivo,2,'.',''),1,0,'C',1);
						$pdf->Ln();
						$pdf->Cell(119);
						$pdf->Cell(20,6,'VISA',1,0,'C',1);
						$pdf->Cell(20,6,number_format($visa,2,'.',''),1,0,'C',1);
						$pdf->Ln();
						$pdf->Cell(119);
						$pdf->Cell(20,6,'MASTER',1,0,'C',1);
						$pdf->Cell(20,6,number_format($master,2,'.',''),1,0,'C',1);
						$pdf->Ln();
						$pdf->Cell(119);
						$pdf->SetFont('Arial','B',9);
						$pdf->Cell(20,6,'Egresos',1,0,'C',1);
						$pdf->Cell(20,6,number_format($egreso,2,'.',''),1,0,'C',1);
						$pdf->Ln();
						$pdf->Cell(119);
						$pdf->Cell(20,6,'Saldo',1,0,'C',1);
						$pdf->Cell(20,6,number_format($saldo,2,'.',''),1,0,'C',1);
						$pdf->Ln();
						$pdf->Cell(119);
						$pdf->Cell(20,6,'Garantia',1,0,'C',1);
						$pdf->Cell(20,6,number_format($garantia,2,'.',''),1,0,'C',1);
						$pdf->Ln();
						$pdf->Ln();
						$ingreso=0;$egreso=0;$garantia=0;$efectivo=0;$master=0;$visa=0;
			    	}
			    	$pdf->SetFillColor(150,194,200);
		    	$pdf->SetFont('Arial','B',10);
		    	$pdf->Cell(280,6,utf8_decode('DÍA: '.$fecha),1,0,'L',1);
		    	$pdf->SetFillColor(250,250,250);
		    	$pdf->SetFont('Arial','',9);
		    	$pdf->Ln();
		    	$pdf->Cell(20,6.5,utf8_decode(substr($responsable,0,11)),1,0,'C',1);
		    	$pdf->Ln();
		    	$fec = $fecha;			
		    	$pv++;
			    	
				} else {
					if ($mov == 'I') {
						if($rows->conceptopago_id<>10){//Garantias
							$ingreso += $total;
							if ($tarjeta == 'VISA') {
								$visa += $total;
							} else if($tarjeta == 'MASTER'){
								$master += $total;
							} else {
								$tarjeta = '-';
								$efectivo += $total;
							}
						}else{
							$garantia += $total;
						}
					} else {
						$egreso += $total;
					}
					
					$saldo = $ingreso - $egreso;
					$mov == 'I' ? $mov = 'INGRESO' : $mov = 'EGRESO';
				    $conce = $rows->conce;
				    $person = ''.($rows->apellidopaterno).' '.($rows->apellidomaterno).' '.($rows->nombres);
				    $person = trim($person);

				    $situacion = $rows->situacion;
				    if ($situacion == 'N') {
				    	$situacion = 'OK';
				    } else if ($situacion == 'A'){
				    	$situacion = 'Anulado';
				    }

				    $responsable = $rows->respn;
				    $comen = $rows->comentario;		   

				    $pdf->SetFont('Arial','',8);
		            $pdf->Cell(20,6.5,utf8_decode(substr($responsable,0,11)),1,0,'C',1);    

					$pdf->Cell(20,6.5,$mov,1,0,'C',1);
					$pdf->Cell(55,6.5,utf8_decode($conce),1,0,'C',1);

					if(strlen($person)>30){
						$pdf->SetFont('Arial','',8);
		                $x=$pdf->GetX();
		                $y=$pdf->GetY();                    
		                $pdf->Multicell(55,3,utf8_decode($person),0,'L');
		                $pdf->SetXY($x,$y);
		                $pdf->Cell(55,6.5,"",1,0,'C');
		            }else{
		            	$pdf->SetFont('Arial','',8);
		                $pdf->Cell(55,6.5,utf8_decode($person),1,0,'C',1);    
		            }
		            $pdf->SetFont('Arial','',9);

					if(strlen($comen)>50){
						$pdf->SetFont('Arial','',8);
		                $x=$pdf->GetX();
		                $y=$pdf->GetY();                    
		                $pdf->Multicell(80,3,utf8_decode($comen),0,'L');
		                $pdf->SetXY($x,$y);
		                $pdf->Cell(80,6.5,"",1,0,'C');
		            }else{
		            	$pdf->Cell(80,6.5,utf8_decode($comen),1,0,'C',1);    
		            }

		            $pdf->Cell(15,6.5,utf8_decode($tarjeta),1,0,'C',1);

		            $pdf->SetFont('Arial','',9);
					//$pdf->Cell(80,6,utf8_decode($comen),1,0,'C',1);
					$pdf->Cell(15,6.5,$total,1,0,'C',1);

					$pdf->Cell(20,6.5,$situacion,1,0,'C',1);

					$pdf->Ln();
				}
			}
		}

		if ($med != 5) {
			if ($pv != 0) {
	    		$pdf->Ln();
		    	$pdf->Cell(119);
		    	$pdf->SetFont('Arial','B',9);
		    	$pdf->Cell(40,6,utf8_decode('RESÚMEN DEL DÍA'),1,0,'C',1);
		    	$pdf->Ln();
		    	$pdf->Cell(119);
				$pdf->Cell(20,6,'Ingresos',1,0,'C',1);
				$pdf->Cell(20,6,number_format($ingreso,2,'.',''),1,0,'C',1);
				$pdf->Ln();
				$pdf->Cell(119);
				$pdf->SetFont('Arial','',9);
				$pdf->Cell(20,6,'Efectivo',1,0,'C',1);
				$pdf->Cell(20,6,number_format($efectivo,2,'.',''),1,0,'C',1);
				$pdf->Ln();
				$pdf->Cell(119);
				$pdf->Cell(20,6,'VISA',1,0,'C',1);
				$pdf->Cell(20,6,number_format($visa,2,'.',''),1,0,'C',1);
				$pdf->Ln();
				$pdf->Cell(119);
				$pdf->Cell(20,6,'MASTER',1,0,'C',1);
				$pdf->Cell(20,6,number_format($master,2,'.',''),1,0,'C',1);
				$pdf->Ln();
				$pdf->Cell(119);
				$pdf->SetFont('Arial','B',9);
				$pdf->Cell(20,6,'Egresos',1,0,'C',1);
				$pdf->Cell(20,6,number_format($egreso,2,'.',''),1,0,'C',1);
				$pdf->Ln();
				$pdf->Cell(119);
				$pdf->Cell(20,6,'Saldo',1,0,'C',1);
				$pdf->Cell(20,6,number_format($saldo,2,'.',''),1,0,'C',1);
				$pdf->Ln();
				$pdf->Cell(119);
				$pdf->Cell(20,6,'Garantia',1,0,'C',1);
				$pdf->Cell(20,6,number_format($garantia,2,'.',''),1,0,'C',1);
				$pdf->Ln();
				$pdf->Ln();
				$ingreso=0;$egreso=0;$garantia=0;$efectivo=0;$master=0;$visa=0;
	    	}
		}
		

		$pdf->Output();
	}

	function rPagosE($fi,$ff,$med){
		$response = '';
		$db = new Conexion();

		$pdf = new PDF('L','mm','A4');
		$pdf->AliasNbPages();
		$pdf->AddPage();

		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','B',14);

		if ($med == '0') {
			$pdf->Cell(290,10,utf8_decode('PAGOS DE PACIENTES EXTERNOS - TODOS'),0,0,'C',0);
			$pdf->Ln();
		} else {
			$query=$db->query("SELECT apellidopaterno, apellidomaterno, nombres from person where id= '$med' ");
			while($rows=$db->recorrerobj($query)){
				$nmedico = ''.($rows->apellidopaterno).' '.($rows->apellidomaterno).' '.($rows->nombres);
				$pdf->Cell(290,10,utf8_decode('PAGOS DE PACIENTES EXTERNOS - '.$nmedico),0,0,'C',0);
				$pdf->Ln();
			}
		}

		$pdf->SetFillColor(68,164,168);
		$pdf->SetFont('Arial','B',12);
		
		$pdf->Cell(8);
		$pdf->Cell(25,6,'Fecha',1,0,'C',1);
		$pdf->Cell(100,6,utf8_decode('Paciente'),1,0,'C',1);
		$pdf->Cell(80,6,'Servicio',1,0,'C',1);
		$pdf->Cell(25,6,'Pago',1,0,'C',1);
		$pdf->Cell(30,6,utf8_decode('Situación'),1,0,'C',1);

		$pdf->SetFont('Arial','',10);
		$pdf->SetFillColor(250,250,250);
		$pdf->Ln();

		$tp=0;$pp=0;$yp=0;
		$med == '0' ? $q = '' : $q = 'AND det.persona_id = '.$med.'';

		$query=$db->query("SELECT m.fecha, det.situacion, p.apellidopaterno, p.apellidomaterno, p.nombres, det.pagodoctor, s.nombre as serv FROM movimiento as m inner join detallemovcaja as det on det.movimiento_id = m.id inner join person as p on p.id = m.persona_id left join servicio as s on s.id = det.servicio_id WHERE m.plan_id = 6 and m.tipodocumento_id = 1 ".$q." AND m.fecha BETWEEN '$fi' AND '$ff' order by m.numero desc");

		while($rows=$db->recorrerobj($query)){
		    $fecha = $rows->fecha;
		    $paciente = ''.($rows->apellidopaterno).' '.($rows->apellidomaterno).' '.($rows->nombres);
		    $total = $rows->pagodoctor;
		    $servicio = $rows->serv;

		    if (strpos($servicio, 'ECOCARDIOGRAFIA DOPPLER') !== false) {
			    $servicio = 'ECODOPPLER, ECOCARDIOGRAFIA DOPPLER';
			}

			if (strpos($servicio, 'ELECTROCARDIOGRAMA DE HOLTER') !== false) {
			    $servicio = 'ELECTROCARDIOGRAMA DE HOLTER 24Hrs';
			}

		    $situacion = $rows->situacion;
		    $situacion == 'C' ? $yp+=$total : $pp+=$total;
		    $situacion == 'C' ? $situacion = 'PAGADO' : $situacion = 'PENDIENTE';

		    $paciente = ($rows->apellidopaterno).' '.($rows->apellidomaterno).' '.($rows->nombres);
		    
		    $pdf->Cell(8);
		    $pdf->Cell(25,6,$fecha,1,0,'C',1);
			$pdf->Cell(100,6,utf8_decode($paciente),1,0,'C',1);
			$pdf->Cell(80,6,utf8_decode($servicio),1,0,'C',1);
			$pdf->Cell(25,6,utf8_decode($total),1,0,'C',1);
			$pdf->Cell(30,6,utf8_decode($situacion),1,0,'C',1);

			$pdf->Ln();

			$tp += $total;
		}

		$pdf->Ln();
		$pdf->Cell(107);
		$pdf->Cell(30,6,'PAGADO',1,0,'C',1);
		$pdf->Cell(30,6,$yp,1,0,'C',1);
		$pdf->Ln();
		$pdf->Cell(107);
		$pdf->Cell(30,6,'PENDIENTE',1,0,'C',1);
		$pdf->Cell(30,6,$pp,1,0,'C',1);
		$pdf->Ln();
		$pdf->Cell(107);
		$pdf->SetFont('Arial','B',11);
		$pdf->Cell(30,6,'TOTAL',1,0,'C',1);
		$pdf->Cell(30,6,$tp,1,0,'C',1);
			
		$pdf->Output();
	}

	function rPagosC($fi,$ff,$med){
		$response = '';
		$db = new Conexion();

		$pdf = new PDF('L','mm','A4');
		$pdf->AliasNbPages();
		$pdf->AddPage();

		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','B',14);

		if ($med == '0') {
			$pdf->Cell(290,10,utf8_decode('PAGOS DE PACIENTES POR CONVENIO - TODOS'),0,0,'C',0);
			$pdf->Ln();
		} else {
			$query=$db->query("SELECT apellidopaterno, apellidomaterno, nombres from person where id= '$med' ");
			while($rows=$db->recorrerobj($query)){
				$nmedico = ''.($rows->apellidopaterno).' '.($rows->apellidomaterno).' '.($rows->nombres);
				$pdf->Cell(290,10,utf8_decode('PAGOS DE PACIENTES POR CONVENIO - '.$nmedico),0,0,'C',0);
				$pdf->Ln();
			}
		}

		$pdf->SetFillColor(68,164,168);
		$pdf->SetFont('Arial','B',12);
		
		$pdf->Cell(8);
		$pdf->Cell(25,6,'Fecha',1,0,'C',1);
		$pdf->Cell(100,6,utf8_decode('Paciente'),1,0,'C',1);
		$pdf->Cell(80,6,'Servicio',1,0,'C',1);
		$pdf->Cell(25,6,'Pago',1,0,'C',1);
		$pdf->Cell(30,6,utf8_decode('Situación'),1,0,'C',1);

		$pdf->SetFont('Arial','',10);
		$pdf->SetFillColor(250,250,250);
		$pdf->Ln();

		$tp=0;$pp=0;$yp=0;
		$med == '0' ? $q = '' : $q = 'AND det.persona_id = '.$med.'';

		$query=$db->query("SELECT m.fecha, det.situacion, p.apellidopaterno, p.apellidomaterno, p.nombres, det.pagodoctor, s.nombre as serv FROM movimiento as m inner join detallemovcaja as det on det.movimiento_id = m.id inner join person as p on p.id = m.persona_id left join servicio as s on s.id = det.servicio_id WHERE m.plan_id != 6 and m.tipodocumento_id = 1 ".$q." AND m.fecha BETWEEN '$fi' AND '$ff' order by m.numero desc");

		while($rows=$db->recorrerobj($query)){
		    $fecha = $rows->fecha;
		    $paciente = ''.($rows->apellidopaterno).' '.($rows->apellidomaterno).' '.($rows->nombres);
		    $total = $rows->pagodoctor;
		    $servicio = $rows->serv;

		    if (strpos($servicio, 'ECOCARDIOGRAFIA DOPPLER') !== false) {
			    $servicio = 'ECODOPPLER, ECOCARDIOGRAFIA DOPPLER';
			}

			if (strpos($servicio, 'ELECTROCARDIOGRAMA DE HOLTER') !== false) {
			    $servicio = 'ELECTROCARDIOGRAMA DE HOLTER 24Hrs';
			}

		    $situacion = $rows->situacion;
		    $situacion == 'C' ? $yp+=$total : $pp+=$total;
		    $situacion == 'C' ? $situacion = 'PAGADO' : $situacion = 'PENDIENTE';

		    $paciente = ($rows->apellidopaterno).' '.($rows->apellidomaterno).' '.($rows->nombres);
		    
		    $pdf->Cell(8);
		    $pdf->Cell(25,6,$fecha,1,0,'C',1);
			$pdf->Cell(100,6,utf8_decode($paciente),1,0,'C',1);
			$pdf->Cell(80,6,utf8_decode($servicio),1,0,'C',1);
			$pdf->Cell(25,6,utf8_decode($total),1,0,'C',1);
			$pdf->Cell(30,6,utf8_decode($situacion),1,0,'C',1);

			$pdf->Ln();

			$tp += $total;
		}

		$pdf->Ln();
		$pdf->Cell(107);
		$pdf->Cell(30,6,'PAGADO',1,0,'C',1);
		$pdf->Cell(30,6,$yp,1,0,'C',1);
		$pdf->Ln();
		$pdf->Cell(107);
		$pdf->Cell(30,6,'PENDIENTE',1,0,'C',1);
		$pdf->Cell(30,6,$pp,1,0,'C',1);
		$pdf->Ln();
		$pdf->Cell(107);
		$pdf->SetFont('Arial','B',11);
		$pdf->Cell(30,6,'TOTAL',1,0,'C',1);
		$pdf->Cell(30,6,$tp,1,0,'C',1);
			
		$pdf->Output();
	}

	function rPagosME($fi,$ff,$med){
		$response = '';
		$db = new Conexion();

		$pdf = new PDF('L','mm','A4');
		$pdf->AliasNbPages();
		$pdf->AddPage();

		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','B',14);

		if ($med == '0') {
			$pdf->Cell(190,10,utf8_decode('PAGOS DE PACIENTES EXTERNOS - TODOS'),0,0,'C',0);
			$pdf->Ln();
		} else {
			$query=$db->query("SELECT apellidopaterno, apellidomaterno, nombres from person where id= '$med' ");
			while($rows=$db->recorrerobj($query)){
				$nmedico = ''.($rows->apellidopaterno).' '.($rows->apellidomaterno).' '.($rows->nombres);
				$pdf->Cell(290,10,utf8_decode('PAGOS DE PACIENTES EXTERNOS - '.$nmedico),0,0,'C',0);
				$pdf->Ln();
			}
		}

		$pdf->SetFillColor(68,164,168);
		$pdf->SetFont('Arial','B',12);
		
		$pdf->Cell(8);
		$pdf->Cell(25,6,'Fecha',1,0,'C',1);
		$pdf->Cell(90,6,utf8_decode('Paciente'),1,0,'C',1);
		$pdf->Cell(90,6,'Servicio',1,0,'C',1);
		$pdf->Cell(25,6,'Pago',1,0,'C',1);
		$pdf->Cell(30,6,utf8_decode('Situación'),1,0,'C',1);

		$pdf->SetFont('Arial','',10);
		$pdf->SetFillColor(250,250,250);
		$pdf->Ln();

		$med == '0' ? $q = '' : $q = 'AND det.persona_id = '.$med.'';

		$query=$db->query("SELECT m.fecha, det.situacion, p.apellidopaterno, p.apellidomaterno, p.nombres, det.pagodoctor, s.nombre as serv FROM movimiento as m inner join detallemovcaja as det on det.movimiento_id = m.id inner join person as p on p.id = m.persona_id left join servicio as s on s.id = det.servicio_id WHERE m.tipodocumento_id = 1 ".$q." AND m.fecha BETWEEN '$fi' AND '$ff' order by m.numero desc");

		while($rows=$db->recorrerobj($query)){
		    $fecha = $rows->fecha;
		    $paciente = ''.($rows->apellidopaterno).' '.($rows->apellidomaterno).' '.($rows->nombres);
		    $total = $rows->pagodoctor;
		    $servicio = $rows->serv;

		    if (strpos($servicio, 'ECOCARDIOGRAFIA DOPPLER') !== false) {
			    $servicio = 'ECODOPPLER, ECOCARDIOGRAFIA DOPPLER';
			}

			if (strpos($servicio, 'ELECTROCARDIOGRAMA DE HOLTER') !== false) {
			    $servicio = 'ELECTROCARDIOGRAMA DE HOLTER 24Hrs';
			}

		    $situacion = $rows->situacion;
		    $situacion == 'C' ? $situacion = 'PAGADO' : $situacion = 'PENDIENTE';

		    $paciente = ($rows->apellidopaterno).' '.($rows->apellidomaterno).' '.($rows->nombres);
		    
		    $pdf->Cell(8);
		    $pdf->Cell(25,6,$fecha,1,0,'C',1);
			$pdf->Cell(90,6,utf8_decode($paciente),1,0,'C',1);
			$pdf->Cell(90,6,utf8_decode($servicio),1,0,'C',1);
			$pdf->Cell(25,6,utf8_decode($total),1,0,'C',1);
			$pdf->Cell(30,6,utf8_decode($situacion),1,0,'C',1);
			//$pdf->Cell(30,6,utf8_decode($historia),1,0,'C',1);
			
			$pdf->Ln();
		}

		$pdf->Output();
	}

	function rPagosMC($fi,$ff,$med){
		$response = '';
		$db = new Conexion();

		$pdf = new PDF('L','mm','A4');
		$pdf->AliasNbPages();
		$pdf->AddPage();

		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','B',14);

		if ($med == '0') {
			$pdf->Cell(290,10,utf8_decode('PAGOS DE PACIENTES POR CONVENIO - TODOS'),0,0,'C',0);
			$pdf->Ln();
		} else {
			$query=$db->query("SELECT apellidopaterno, apellidomaterno, nombres from person where id= '$med' ");
			while($rows=$db->recorrerobj($query)){
				$nmedico = ''.($rows->apellidopaterno).' '.($rows->apellidomaterno).' '.($rows->nombres);
				$pdf->Cell(290,10,utf8_decode('PAGOS DE PACIENTES POR CONVENIO - '.$nmedico),0,0,'C',0);
				$pdf->Ln();
			}
		}

		$pdf->SetFillColor(68,164,168);
		$pdf->SetFont('Arial','B',12);
		
		$pdf->Cell(8);
		$pdf->Cell(25,6,'Fecha',1,0,'C',1);
		$pdf->Cell(100,6,utf8_decode('Paciente'),1,0,'C',1);
		$pdf->Cell(80,6,'Servicio',1,0,'C',1);
		$pdf->Cell(25,6,'Pago',1,0,'C',1);
		$pdf->Cell(30,6,utf8_decode('Situación'),1,0,'C',1);

		$pdf->SetFont('Arial','',10);
		$pdf->SetFillColor(250,250,250);
		$pdf->Ln();

		$tp=0;$pp=0;$yp=0;
		$med == '0' ? $q = '' : $q = 'AND det.persona_id = '.$med.'';

		$query=$db->query("SELECT m.fecha, det.situacion, p.apellidopaterno, p.apellidomaterno, p.nombres, det.pagodoctor, s.nombre as serv FROM movimiento as m inner join detallemovcaja as det on det.movimiento_id = m.id inner join person as p on p.id = m.persona_id left join servicio as s on s.id = det.servicio_id WHERE m.plan_id != 6 and m.tipodocumento_id = 1 ".$q." AND m.fecha BETWEEN '$fi' AND '$ff' order by m.numero desc");

		while($rows=$db->recorrerobj($query)){
		    $fecha = $rows->fecha;
		    $paciente = ''.($rows->apellidopaterno).' '.($rows->apellidomaterno).' '.($rows->nombres);
		    $total = $rows->pagodoctor;
		    $servicio = $rows->serv;

		    if (strpos($servicio, 'ECOCARDIOGRAFIA DOPPLER') !== false) {
			    $servicio = 'ECODOPPLER, ECOCARDIOGRAFIA DOPPLER';
			}

			if (strpos($servicio, 'ELECTROCARDIOGRAMA DE HOLTER') !== false) {
			    $servicio = 'ELECTROCARDIOGRAMA DE HOLTER 24Hrs';
			}

		    $situacion = $rows->situacion;
		    $situacion == 'C' ? $yp+=$total : $pp+=$total;
		    $situacion == 'C' ? $situacion = 'PAGADO' : $situacion = 'PENDIENTE';

		    $paciente = ($rows->apellidopaterno).' '.($rows->apellidomaterno).' '.($rows->nombres);
		    
		    $pdf->Cell(8);
		    $pdf->Cell(25,6,$fecha,1,0,'C',1);
			$pdf->Cell(100,6,utf8_decode($paciente),1,0,'C',1);
			$pdf->Cell(80,6,utf8_decode($servicio),1,0,'C',1);
			$pdf->Cell(25,6,utf8_decode($total),1,0,'C',1);
			$pdf->Cell(30,6,utf8_decode($situacion),1,0,'C',1);

			$pdf->Ln();

			$tp += $total;
		}

		$pdf->Ln();
		$pdf->Cell(107);
		$pdf->Cell(30,6,'PAGADO',1,0,'C',1);
		$pdf->Cell(30,6,$yp,1,0,'C',1);
		$pdf->Ln();
		$pdf->Cell(107);
		$pdf->Cell(30,6,'PENDIENTE',1,0,'C',1);
		$pdf->Cell(30,6,$pp,1,0,'C',1);
		$pdf->Ln();
		$pdf->Cell(107);
		$pdf->SetFont('Arial','B',11);
		$pdf->Cell(30,6,'TOTAL',1,0,'C',1);
		$pdf->Cell(30,6,$tp,1,0,'C',1);
			
		$pdf->Output();
	}

	function rConvenios($fi,$ff,$med){
		$response = '';
		$db = new Conexion();

		$pdf = new PDF();
		$pdf->AliasNbPages();
		$pdf->AddPage();

		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','B',14);

		if ($med == '0') {
			$pdf->Cell(190,10,utf8_decode('PACIENTES ATENDIDOS - TODOS LOS CONVENIOS'),0,0,'C',0);
			$pdf->Ln();
		} else {
			$query=$db->query("SELECT nombre from plan where id= '$med' ");
			while($rows=$db->recorrerobj($query)){
				$nmedico = $rows->nombre;
				$pdf->Cell(190,10,utf8_decode('PACIENTES ATENDIDOS - '.$nmedico),0,0,'C',0);
				$pdf->Ln();
			}
		}

		$pdf->SetFillColor(68,164,168);
		$pdf->SetFont('Arial','B',12);
		
		$pdf->Cell(25,6,'Fecha',1,0,'C',1);
		$pdf->Cell(20,6,utf8_decode('Número'),1,0,'C',1);
		$pdf->Cell(90,6,'Paciente',1,0,'C',1);
		$pdf->Cell(25,6,'Total',1,0,'C',1);
		$pdf->Cell(30,6,utf8_decode('Situación'),1,0,'C',1);

		$pdf->SetFont('Arial','',10);
		$pdf->SetFillColor(250,250,250);
		$pdf->Ln();

		$med == '0' ? $q = '' : $q = 'AND m.plan_id = '.$med.'';

		$query=$db->query("SELECT m.fecha, m.total, m.numero, m.situacion, p.apellidopaterno, p.apellidomaterno, p.nombres FROM movimiento as m left join person as p on p.id = m.persona_id WHERE m.tipodocumento_id = 1 ".$q." AND m.fecha BETWEEN '$fi' AND '$ff' order by m.numero desc");

		while($rows=$db->recorrerobj($query)){
		    $fecha = $rows->fecha;
		    $num = $rows->numero;
		    $total = $rows->total;
		    $situacion = $rows->situacion;
		    $situacion == 'P' ? $situacion = 'PENDIENTE' : $situacion = 'COBRADO';

		    $paciente = ($rows->apellidopaterno).' '.($rows->apellidomaterno).' '.($rows->nombres);
		    
		    $pdf->Cell(25,6,$fecha,1,0,'C',1);
			$pdf->Cell(20,6,$num,1,0,'C',1);
			$pdf->Cell(90,6,utf8_decode($paciente),1,0,'C',1);
			$pdf->Cell(25,6,utf8_decode($total),1,0,'C',1);
			$pdf->Cell(30,6,utf8_decode($situacion),1,0,'C',1);
			//$pdf->Cell(30,6,utf8_decode($historia),1,0,'C',1);
			
			$pdf->Ln();
		}

		$pdf->Output();
	}

	function rCompras($fi,$ff,$med,$pro){
		$response = '';
		$db = new Conexion();

		$pdf = new PDF('L','mm','A4');
		$pdf->AliasNbPages();
		$pdf->AddPage();

		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','B',14);

		if ($med == '0') {
			$pdf->Cell(0,10,utf8_decode('Reporte de Compras - TODOS'),0,0,'C',0);
			$pdf->Ln();
			$pro = '';
		} else if  ($med == '1'){
			$med = 'P';
			$pdf->Cell(0,10,utf8_decode('Reporte de Compras - PAGADO'),0,0,'C',0);
			$pdf->Ln();
		} else if  ($med == '2'){
			$med = 'PP';
			$pdf->Cell(0,10,utf8_decode('Reporte de Compras - PENDIENTE'),0,0,'C',0);
			$pdf->Ln();
		} else if  ($med == '3'){
			$med = '0';
			$query=$db->query("SELECT nombre FROM producto where id = '$pro'");
			while($rows=$db->recorrerobj($query)){
				$producto = $rows->nombre;
				$pdf->Cell(0,10,utf8_decode('Reporte de Compras - '.$producto),0,0,'C',0);
				$pdf->Ln();
			}
			$pro = "AND producto.id = '$pro' ";
		} else if  ($med == '4'){
			$med = '0';
			$pdf->Cell(0,10,utf8_decode('Reporte de Compras - ANULADOS'),0,0,'C',0);
			$pdf->Ln();
			$pro = "AND situacion = 'A' ";
		} else if  ($med == '5'){
			$med = '0';
			$query=$db->query("SELECT bussinesname FROM person where id = '$pro'");
			while($rows=$db->recorrerobj($query)){
				$proveedor = $rows->bussinesname;
				$pdf->Cell(0,10,utf8_decode('Reporte de Compras - '.$proveedor),0,0,'C',0);
				$pdf->Ln();
			}
			$pro = "AND p.id = '$pro' ";
		}

		$pdf->SetFillColor(200,200,200);
		$pdf->SetFont('Arial','B',8);
		
		$pdf->Cell(5);
		$pdf->Cell(20,6,'FECHA',1,0,'C',1);
		$pdf->Cell(20,6,'DOCUM',1,0,'C',1);
		$pdf->Cell(52,6,'PRODUCTO',1,0,'C',1);
		$pdf->Cell(20,6,'UNIDAD',1,0,'C',1);
		$pdf->Cell(20,6,'COMPRA',1,0,'C',1);
		$pdf->Cell(20,6,'PUBLICO',1,0,'C',1);
		$pdf->Cell(20,6,'KAYROS',1,0,'C',1);
		$pdf->Cell(20,6,'% GANANC.',1,0,'C',1);
		$pdf->Cell(46,6,utf8_decode('PROOVEDOR'),1,0,'C',1);
		$pdf->Cell(30,6,utf8_decode('USUARIO'),1,0,'C',1);

		$pdf->SetFont('Arial','',8);
		$pdf->SetFillColor(250,250,250);
		$pdf->Ln();

		$tp=0;$pp=0;$yp=0;
		$med == '0' ? $q = $pro : $q = "AND movimiento.estadopago = '".$med."'";

		//echo("SELECT movimiento.fecha, movimiento.numero, producto.nombre, kardex.cantidad, producto.preciocompra, producto.precioventa, producto.preciokayros, p.bussinesname, u.nombres as usuario FROM detallemovimiento inner JOIN movimiento on detallemovimiento.movimiento_id = movimiento.id inner join person AS p on movimiento.persona_id = p.id inner join person as u on movimiento.responsable_id = u.id inner JOIN producto on detallemovimiento.producto_id = producto.id JOIN kardex on detallemovimiento.id = kardex.detallemovimiento_id where movimiento.tipomovimiento_id = 3 ".$q." AND movimiento.fecha BETWEEN '$fi' AND '$ff' order by movimiento.fecha ASC");exit();

		$query=$db->query("SELECT movimiento.fecha, movimiento.numero, producto.nombre, kardex.cantidad, producto.preciocompra, producto.precioventa, producto.preciokayros, p.bussinesname, u.nombres as usuario FROM detallemovimiento left JOIN movimiento on detallemovimiento.movimiento_id = movimiento.id left join person AS p on movimiento.persona_id = p.id left join person as u on movimiento.responsable_id = u.id left JOIN producto on detallemovimiento.producto_id = producto.id left JOIN kardex on detallemovimiento.id = kardex.detallemovimiento_id where movimiento.tipomovimiento_id = 3 ".$q." AND movimiento.fecha BETWEEN '$fi' AND '$ff' order by movimiento.fecha ASC");

		while($rows=$db->recorrerobj($query)){
		    $fecha = $rows->fecha;
		    $numero = $rows->numero;
		    $producto = $rows->nombre;
		    $cantidad = $rows->cantidad;
		    $preciocompra = $rows->preciocompra;
		    $precioventa = $rows->precioventa;
		    $preciokayros = $rows->preciokayros;
		    $proveedor = $rows->bussinesname;		    
		    $total = $rows->usuario;
		    
		    $ganancia = (100*($precioventa - $preciocompra))/$preciocompra;
		    $ganancia = round($ganancia, 2, PHP_ROUND_HALF_UP);

		    $pdf->Cell(5);
		    $pdf->Cell(20,6,$fecha,1,0,'C',1);
		    $pdf->Cell(20,6,utf8_decode($numero),1,0,'C',1);
			if(strlen($producto)>30){
				$pdf->SetFont('Arial','',8);
                $x=$pdf->GetX();
                $y=$pdf->GetY();                    
                $pdf->Multicell(52,3,utf8_decode($producto),0,'L');
                $pdf->SetXY($x,$y);
                $pdf->Cell(52,6,"",1,0,'C');
            }else{
            	$pdf->Cell(52,6,utf8_decode($producto),1,0,'C',1);
            }

			$pdf->Cell(20,6,$cantidad,1,0,'C',1);
			$pdf->Cell(20,6,$preciocompra,1,0,'C',1);
			$pdf->Cell(20,6,$precioventa,1,0,'C',1);
			$pdf->Cell(20,6,$preciokayros,1,0,'C',1);
			$pdf->Cell(20,6,$ganancia,1,0,'C',1);
			if(strlen($proveedor)>30){
				$pdf->SetFont('Arial','',8);
                $x=$pdf->GetX();
                $y=$pdf->GetY();                    
                $pdf->Multicell(46,3,utf8_decode($proveedor),0,'L');
                $pdf->SetXY($x,$y);
                $pdf->Cell(46,6,"",1,0,'C');
            }else{
            	$pdf->Cell(46,6,utf8_decode($proveedor),1,0,'C',1);
            }
			$pdf->Cell(30,6,utf8_decode($total),1,0,'C',1);

			$pdf->Ln();

			$tp += $total;
		}

		$pdf->Ln();
		// $pdf->Cell(107);
		// $pdf->Cell(30,6,'PAGADO',1,0,'C',1);
		// $pdf->Cell(30,6,$yp,1,0,'C',1);
		// $pdf->Ln();
		// $pdf->Cell(107);
		// $pdf->Cell(30,6,'PENDIENTE',1,0,'C',1);
		// $pdf->Cell(30,6,$pp,1,0,'C',1);
		// $pdf->Ln();
		// $pdf->Cell(107);
		// $pdf->SetFont('Arial','B',11);
		// $pdf->Cell(30,6,'TOTAL',1,0,'C',1);
		// $pdf->Cell(30,6,$tp,1,0,'C',1);
			
		$pdf->Output();
	}

	function rVentas($fi,$ff,$med,$pro){
		$response = '';
		$db = new Conexion();

		$pdf = new PDF('L','mm','A4');
		$pdf->AliasNbPages();
		$pdf->AddPage();

		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','B',14);

		if ($med == '0') {
			$pdf->Cell(290,10,utf8_decode('Reporte de Ventas - TODOS'),0,0,'C',0);
			$pdf->Ln();
			$pro = '';
		} else if  ($med == '1'){
			$med = 'P';
			$pdf->Cell(290,10,utf8_decode('Reporte de Ventas - PAGADO'),0,0,'C',0);
			$pdf->Ln();
		} else if  ($med == '2'){
			$med = 'PP';
			$pdf->Cell(290,10,utf8_decode('Reporte de Ventas - PENDIENTE'),0,0,'C',0);
			$pdf->Ln();
		} else if  ($med == '3'){
			$med = '0';
			$query=$db->query("SELECT nombre FROM producto where id = '$pro'");
			while($rows=$db->recorrerobj($query)){
				$producto = $rows->nombre;
				$pdf->Cell(290,10,utf8_decode('Reporte de Ventas - '.$producto),0,0,'C',0);
				$pdf->Ln();
			}
			$pro = "AND producto.id = '$pro' ";
		} else if  ($med == '4'){
			$med = '0';
			$pdf->Cell(290,10,utf8_decode('Reporte de Ventas - ANULADOS'),0,0,'C',0);
			$pdf->Ln();
			$pro = "AND situacion = 'A' ";
		} else if  ($med == '5'){
			$med = '0';
			$query=$db->query("SELECT nombres, apellidopaterno, apellidomaterno FROM person where id = '$pro'");
			while($rows=$db->recorrerobj($query)){
				$producto = $rows->apellidopaterno.' '.$rows->apellidomaterno.' '.$rows->nombres;
				$pdf->Cell(290,10,utf8_decode('Reporte de Ventas - '.$producto),0,0,'C',0);
				$pdf->Ln();
			}
			$pro = "AND p.id = '$pro' ";
		}

		$pdf->SetFillColor(200,200,200);
		$pdf->SetFont('Arial','B',8);
		
		$pdf->Cell(8);
		$pdf->Cell(20,6,'FECHA',1,0,'C',1);
		$pdf->Cell(20,6,'HORA',1,0,'C',1);
		$pdf->Cell(20,6,'DOCUM',1,0,'C',1);
		$pdf->Cell(67,6,'PRODUCTO',1,0,'C',1);
		$pdf->Cell(50,6,'CLIENTE',1,0,'C',1);
		$pdf->Cell(20,6,'CANTIDAD',1,0,'C',1);
		$pdf->Cell(20,6,'PRECIO',1,0,'C',1);
		$pdf->Cell(20,6,'TOTAL',1,0,'C',1);
		$pdf->Cell(25,6,'ESTADO.',1,0,'C',1);

		$pdf->SetFont('Arial','',8);
		$pdf->SetFillColor(250,250,250);
		$pdf->Ln();

		$tp=0;$pp=0;$yp=0;
		$med == '0' ? $q = $pro : $q = "AND movimiento.estadopago = '".$med."'";

		$query=$db->query("SELECT movimiento.fecha, TIME(movimiento.created_at) hora, movimiento.numero, movimiento.tipodocumento_id, movimiento.situacion, producto.nombre as producto, p.apellidopaterno, p.apellidomaterno, p.nombres, kardex.cantidad, producto.precioventa, movimiento.total, u.nombres as usuario, movimiento.estadopago, detallemovimiento.subtotal dmsub FROM detallemovimiento left JOIN movimiento on detallemovimiento.movimiento_id = movimiento.id left join person AS p on movimiento.persona_id = p.id left join person as u on movimiento.responsable_id = u.id left JOIN producto on detallemovimiento.producto_id = producto.id JOIN kardex on detallemovimiento.id = kardex.detallemovimiento_id where movimiento.situacion <> 'U' AND movimiento.tipomovimiento_id = 4 AND movimiento.ventafarmacia = 'S' ".$q." AND movimiento.fecha BETWEEN '$fi' AND '$ff' order by movimiento.fecha, hora asc");

		while($rows=$db->recorrerobj($query)){
		    $fecha = $rows->fecha;
		    $hora = $rows->hora;
		    $numero = $rows->numero;
		    $situacion = $rows->situacion;
		    $producto = $rows->producto;
		    $apellidopaterno = $rows->apellidopaterno;
		    $apellidomaterno = $rows->apellidomaterno;
		    $nombres = $rows->nombres;
		    $cantidad = $rows->cantidad;
		    $total = $rows->dmsub;
		    $precioventa = $total/($cantidad==0?1:$cantidad);
		    $estadopago = $rows->estadopago;
		    $tipodocumento_id = $rows->tipodocumento_id;
		    if ($tipodocumento_id == 4) {
		    	$tipodocumento_id = 'F';
		    } else if ($tipodocumento_id == 5){
				$tipodocumento_id = 'B';
		    } else if ($tipodocumento_id == 15){
		    	$tipodocumento_id = 'G';
		    }
		    
		    if ($situacion == 'N') {
		    	if ($estadopago == 'P') {
			    	$yp+=$total;
			    	$estadopago = 'PAGADO';
			    } else if ($estadopago == 'PP'){
			    	$estadopago = 'PENDIENTE';
			    	$pp+=$total;
			    }
		    } else if ($situacion == 'A'){
		    	$estadopago = "ANULADO CON NOTA CRED";
		    } else {
		    	$estadopago = "ANULADO MANUAL";
		    }

		    $pdf->Cell(8);
		    $pdf->Cell(20,6,$fecha,1,0,'C',1);
		    $pdf->Cell(20,6,$hora,1,0,'C',1);
		    $pdf->Cell(20,6,utf8_decode($tipodocumento_id.'-'.$numero),1,0,'C',1);
			if(strlen($producto)>39){
				$pdf->SetFont('Arial','',8);
                $x=$pdf->GetX();
                $y=$pdf->GetY();                    
                $pdf->Multicell(67,3,utf8_decode($producto),0,'L');
                $pdf->SetXY($x,$y);
                $pdf->Cell(67,6,"",1,0,'C');
            }else{
            	$pdf->Cell(67,6,utf8_decode($producto),1,0,'C',1);
            }

            $pac = $apellidopaterno.' '.$apellidomaterno.' '.$nombres;
            if(strlen($pac)>26){
				$pdf->SetFont('Arial','',8);
                $x=$pdf->GetX();
                $y=$pdf->GetY();                    
                $pdf->Multicell(50,3,utf8_decode($pac),0,'L');
                $pdf->SetXY($x,$y);
                $pdf->Cell(50,6,"",1,0,'C');
            }else{
            	$pdf->Cell(50,6,utf8_decode($pac),1,0,'C',1);
            }
			
			$pdf->Cell(20,6,number_format($cantidad),1,0,'C',1);
			$pdf->Cell(20,6,number_format($precioventa,2,'.',''),1,0,'C',1);
			$pdf->Cell(20,6,number_format($precioventa*$cantidad,2,'.',''),1,0,'C',1);
			if(strlen($estadopago)>8){
				$pdf->SetFont('Arial','',8);
                $x=$pdf->GetX();
                $y=$pdf->GetY();                    
                $pdf->Multicell(25,3,utf8_decode($estadopago),0,'L');
                $pdf->SetXY($x,$y);
                $pdf->Cell(25,6,"",1,0,'C');
            }else{
            	$pdf->Cell(25,6,utf8_decode($estadopago),1,0,'C',1);
            }


			$pdf->Ln();

			$tp += $total;
		}

		$pdf->Ln();
		$pdf->Cell(107);
		$pdf->Cell(30,6,'PAGADO',1,0,'C',1);
		$pdf->Cell(30,6,$yp,1,0,'C',1);
		$pdf->Ln();
		$pdf->Cell(107);
		$pdf->Cell(30,6,'PENDIENTE',1,0,'C',1);
		$pdf->Cell(30,6,$pp,1,0,'C',1);
		$pdf->Ln();
		$pdf->Cell(107);
		$pdf->SetFont('Arial','B',11);
		$pdf->Cell(30,6,'TOTAL',1,0,'C',1);
		$pdf->Cell(30,6,$tp,1,0,'C',1);
			
		$pdf->Output();
	}

	function rMovAlma($fi,$ff,$med,$pro){
		$response = '';
		$db = new Conexion();

		$pdf = new PDF('P','mm','A4');
		$pdf->AliasNbPages();
		$pdf->AddPage();

		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','B',14);

		if ($med == '0') {
			$pdf->Cell(0,10,utf8_decode('Movimientos de Almacén - TODOS'),0,0,'C',0);
			$pdf->Ln();
			$pro = '';
		} else if  ($med == '1'){
			$med = '0';
			$query=$db->query("SELECT nombre FROM producto where id = '$pro'");
			while($rows=$db->recorrerobj($query)){
				$producto = $rows->nombre;
				$pdf->Cell(0,10,utf8_decode('Movimientos de Almacén - '.$producto),0,0,'C',0);
				$pdf->Ln();
			}
			$pro = "AND p.id = '$pro' ";
		} 

		$pdf->SetFillColor(200,200,200);
		$pdf->SetFont('Arial','B',8);
		
		//$pdf->Cell(8);
		$pdf->Cell(20,6,'Fecha',1,0,'C',1);
        $pdf->Cell(15,6,'Hora',1,0,'C',1);
        $pdf->Cell(20,6,'Responsable',1,0,'C',1);
        $pdf->Cell(35,6,'Tipo Doc',1,0,'C',1);
        $pdf->Cell(20,6,'Nro Doc',1,0,'C',1);
        $pdf->Cell(25,6,'Vencimiento',1,0,'C',1);
        $pdf->Cell(20,6,'Ingreso',1,0,'C',1);
        $pdf->Cell(20,6,'Egreso',1,0,'C',1);
        $pdf->Cell(20,6,'Saldo Final',1,0,'C',1);

		$pdf->SetFont('Arial','',8);
		$pdf->SetFillColor(250,250,250);
		$pdf->Ln();

		$tp=0;$pp=0;$yp=0; $entradas = 0; $salidas = 0;

		$query=$db->query("SELECT k.fecha, TIME(m.created_at) hora, k.cantidad, k.tipo, k.stockactual, p.nombre producto, u.nombres usuario, m.tipomovimiento_id, m.numero, m.tipodocumento_id FROM kardex k join detallemovimiento dm on k.detallemovimiento_id = dm.id join movimiento m on dm.movimiento_id = m.id join person u on u.id = m.responsable_id JOIN producto p on dm.producto_id = p.id where m.deleted_at is null and m.tipomovimiento_id = 5 ".$pro." AND k.fecha BETWEEN '".$fi."' AND '".$ff."' order by k.fecha, hora asc");
		while($rows=$db->recorrerobj($query)){
		    $tipo = $rows->tipomovimiento_id;
		    $tipokardex = $rows->tipo;
       		$stof=$rows->stockactual;

		    $fecha = $rows->fecha;
		    $hora = $rows->hora;
		    //$tipo = $rows->tipomovimiento_id;
		    $cantidad = $rows->cantidad;
		    
		    $numero = $rows->numero;
		    $usuario = $rows->usuario;
		    $tdocumento = $rows->tipodocumento_id;
		    $tipom = $tipo;
		    if($tipo != 4){
		    	if ($tipo == 5) {
		    		if ($tipokardex == 'I') {
		    			$tipo = 'INGRESO';
			    		$entradas += $cantidad;
			    		$egreso = '-';
			    		$ingreso = number_format($cantidad);
			    	} else {
			    		$tipo = 'SALIDA';
			    		$ingreso = '-';
			    		$salidas += $cantidad;
			    		$egreso = number_format($cantidad);
			    	}
		    	} else if ($tipo == 3){
		    		$tipo = 'INGRESO';
		    		$entradas+=$cantidad;
		    		$egreso = '-';
		    		$ingreso = number_format($cantidad);
		    	} else if ($tipo == 6){
		    		if ($tipokardex == 'I') {
		    			$tipo = 'INGRESO';
			    		$entradas += $cantidad;
			    		$egreso = '-';
			    		$ingreso = number_format($cantidad);
			    	} else {
			    		$tipo = 'SALIDA';
			    		$ingreso = '-';
			    		$salidas += $cantidad;
			    		$egreso = number_format($cantidad);
			    	}
		    	}
		    } else {
		    	$tipo = 'SALIDA'; $salidas+=$cantidad;
		    	$egreso = number_format($cantidad);
		    	$ingreso = '-';
		    }
		    //$vence = $rows->fechavencimiento;
		    $vence = '-';

		    if($tdocumento != NULL){
				$query2=$db->query("SELECT nombre FROM tipodocumento where id = '$tdocumento' ");
				while($rows2=$db->recorrerobj($query2)){
					$tdocumento = $rows2->nombre;
				}
		    } else {
		    	$tdocumento = '-';
		    }

		    $pdf->Cell(20,6,$fecha,1,0,'C',1);
		    $pdf->Cell(15,6,$hora,1,0,'C',1);

	        if(strlen($usuario)>10){
				$pdf->SetFont('Arial','',8);
                $x=$pdf->GetX();
                $y=$pdf->GetY();                    
                $pdf->Multicell(20,3,utf8_decode($usuario),0,'L');
                $pdf->SetXY($x,$y);
                $pdf->Cell(20,6,"",1,0,'C');
                $pdf->SetFont('Arial','',9);
            }else{
            	
            	$pdf->Cell(20,6,utf8_decode($usuario),1,0,'C',1);
            }
	        $pdf->Cell(35,6,$tdocumento,1,0,'C',1);
	        $pdf->Cell(20,6,$numero,1,0,'C',1);
	        $pdf->Cell(25,6,$vence,1,0,'C',1);
	        $pdf->Cell(20,6,$ingreso,1,0,'C',1);
	        $pdf->Cell(20,6,$egreso,1,0,'C',1);
	        $pdf->Cell(20,6,number_format($stof),1,0,'C',1);
			
			$pdf->Ln();
		}

        $pdf->Ln();
     	$pdf->Cell(50);
	    $pdf->Cell(30,6,'Ingresos:',1,0,'C',1);
        $pdf->Cell(35,6,$entradas,1,0,'C',1);
        $pdf->Ln();
        $pdf->Cell(50);
        $pdf->Cell(30,6,'Egresos:',1,0,'C',1);
        $pdf->Cell(35,6,$salidas,1,0,'C',1);
			
		$pdf->Output();
	}

	function rfCaja($fi,$ff){
		$response = '';
		$db = new Conexion();

		$pdf = new PDF('L','mm','A4');
		$pdf->AliasNbPages();
		$pdf->AddPage();

		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','B',14);
		
		$pdf->Cell(45);
		$pdf->Cell(190,10,utf8_decode('Informe de Caja - Farmacia ('.$fi.' hasta '.$ff.')'),0,0,'C',0);
		$pdf->Ln();

		$pdf->SetFillColor(68,164,168);
		$pdf->SetFont('Arial','B',11.5);
		$pdf->Cell(20,6,'Usuario',1,0,'C',1);
		$pdf->Cell(20,6,'Tipo',1,0,'C',1);
		$pdf->Cell(55,6,'Concepto',1,0,'C',1);
		$pdf->Cell(55,6,'Persona',1,0,'C',1);
		$pdf->Cell(80,6,'Comentario',1,0,'C',1);
		$pdf->Cell(15,6,'Tarjeta',1,0,'C',1);
		$pdf->Cell(15,6,'Total',1,0,'C',1);
		$pdf->Cell(20,6,utf8_decode('Situación'),1,0,'C',1);

		$pdf->SetFont('Arial','',10);
		$pdf->SetFillColor(250,250,250);
		$pdf->Ln();

		$query=$db->query("SELECT m.nombrepaciente, m.persona_id, m.tipodocumento_id, m.situacion, m2.situacion as situacion2, m.fecha, m.tipotarjeta, m.total, m.comentario, p.bussinesname, p.apellidopaterno, p.apellidomaterno, p.nombres, responsable.nombres as respn,cp.id as conceptopago_id, cp.nombre as conce, cp.tipo as tic FROM movimiento as m left join person as p on p.id = m.persona_id join person as responsable on responsable.id = m.responsable_id join conceptopago as cp on m.conceptopago_id = cp.id left join movimiento as m2 on m2.id = m.movimiento_id WHERE m.caja_id = '4' AND m.fecha BETWEEN '$fi' AND '$ff' order by m.id desc");

		$fec = '';
		$pv = 0;
		$ingreso=0;$egreso=0;$garantia=0;$efectivo=0;$master=0;$visa=0;$saldo=0;
		$sig = 2;

		while($rows=$db->recorrerobj($query)){
			$fecha = $rows->fecha;
			$total = $rows->total;
			$tarjeta = $rows->tipotarjeta;
		    $mov = $rows->tic;

			if ($sig == 1) {
				$sig = 0;
			}

			if ($pv == 0) {
				$pdf->SetFillColor(150,194,200);
		    	$pdf->SetFont('Arial','B',10);
		    	$pdf->Cell(280,6,utf8_decode('DÍA: '.$fecha),1,0,'L',1);
		    	$pdf->SetFillColor(250,250,250);
		    	$pdf->SetFont('Arial','',9);
		    	$pdf->Ln();
		    	$fec = $fecha;			
		    	$pv++;
		    }

			if ($rows->conceptopago_id==1) {
				$sig = 1;
			}

			if ($sig == 0) {
				
				$sig = 2;	
				if ($pv != 0) {
		    		$pdf->Ln();
			    	$pdf->Cell(119);
			    	$pdf->SetFont('Arial','B',9);
			    	$pdf->Cell(40,6,utf8_decode('RESÚMEN DEL DÍA'),1,0,'C',1);
			    	$pdf->Ln();
			    	$pdf->Cell(119);
					$pdf->Cell(20,6,'Ingresos',1,0,'C',1);
					$pdf->Cell(20,6,number_format($ingreso,2,'.',''),1,0,'C',1);
					$pdf->Ln();
					$pdf->Cell(119);
					$pdf->SetFont('Arial','',9);
					$pdf->Cell(20,6,'Efectivo',1,0,'C',1);
					$pdf->Cell(20,6,number_format($efectivo,2,'.',''),1,0,'C',1);
					$pdf->Ln();
					$pdf->Cell(119);
					$pdf->Cell(20,6,'VISA',1,0,'C',1);
					$pdf->Cell(20,6,number_format($visa,2,'.',''),1,0,'C',1);
					$pdf->Ln();
					$pdf->Cell(119);
					$pdf->Cell(20,6,'MASTER',1,0,'C',1);
					$pdf->Cell(20,6,number_format($master,2,'.',''),1,0,'C',1);
					$pdf->Ln();
					$pdf->Cell(119);
					$pdf->SetFont('Arial','B',9);
					$pdf->Cell(20,6,'Egresos',1,0,'C',1);
					$pdf->Cell(20,6,number_format($egreso,2,'.',''),1,0,'C',1);
					$pdf->Ln();
					$pdf->Cell(119);
					$pdf->Cell(20,6,'Saldo',1,0,'C',1);
					$pdf->Cell(20,6,number_format($saldo,2,'.',''),1,0,'C',1);
					$pdf->Ln();
					$pdf->Cell(119);
					$pdf->Cell(20,6,'Garantia',1,0,'C',1);
					$pdf->Cell(20,6,number_format($garantia,2,'.',''),1,0,'C',1);
					$pdf->Ln();
					$pdf->Ln();
					$ingreso=0;$egreso=0;$garantia=0;$efectivo=0;$master=0;$visa=0;
		    	}
		    	$pdf->SetFillColor(150,194,200);
		    	$pdf->SetFont('Arial','B',10);
		    	$pdf->Cell(280,6,utf8_decode('DÍA: '.$fecha),1,0,'L',1);
		    	$pdf->SetFillColor(250,250,250);
		    	$pdf->SetFont('Arial','',9);
		    	$pdf->Ln();
		    	$fec = $fecha;			
		    	$pv++;
			    	
				} else {
					if ($mov == 'I') {
						if($rows->conceptopago_id<>10){//Garantias
							$ingreso += $total;
							if ($tarjeta == 'VISA') {
								$visa += $total;
							} else if($tarjeta == 'MASTER'){
								$master += $total;
							} else {
								$tarjeta = '-';
								$efectivo += $total;
							}
						}else{
							$garantia += $total;
						}
					} else {
						$egreso += $total;
					}
					
					$saldo = $ingreso - $egreso;
					$mov == 'I' ? $mov = 'INGRESO' : $mov = 'EGRESO';
				    $conce = $rows->conce;
				    $nombrepaciente = '';
	                //if ($rows->tipodocumento_id == 5) {
	                    if ($rows->persona_id !== NULL) {
	                    	//echo 'entro'.$value->id;break;
	                        $nombrepaciente = trim($rows->bussinesname." ".($rows->apellidopaterno).' '.($rows->apellidomaterno).' '.($rows->nombres));

	                    }else{
	                        $nombrepaciente = trim($rows->nombrepaciente);
	                    }
	                    
	                //}else{
	                    //$nombrepaciente = trim($rows->nombreEmpresa);
	                //}
				    
				    $nombrepaciente = trim($nombrepaciente);

				    $situacion = $rows->situacion;
				    if ($situacion == 'N') {
				    	$situacion = 'OK';
				    } else if ($situacion == 'A'){
				    	$situacion = 'Anulado';
				    }

				    $responsable = $rows->respn;
				    $comen = $rows->comentario;		   

				    $pdf->SetFont('Arial','',8);
		            $pdf->Cell(20,6.5,utf8_decode(substr($responsable,0,11)),1,0,'C',1);    

					$pdf->Cell(20,6.5,$mov,1,0,'C',1);
					$pdf->Cell(55,6.5,utf8_decode($conce),1,0,'C',1);

					if(strlen($nombrepaciente)>30){
						$pdf->SetFont('Arial','',8);
		                $x=$pdf->GetX();
		                $y=$pdf->GetY();                    
		                $pdf->Multicell(55,3,utf8_decode($nombrepaciente),0,'L');
		                $pdf->SetXY($x,$y);
		                $pdf->Cell(55,6.5,"",1,0,'C');
		            }else{
		            	$pdf->SetFont('Arial','',8);
		                $pdf->Cell(55,6.5,utf8_decode($nombrepaciente),1,0,'C',1);    
		            }
		            $pdf->SetFont('Arial','',9);

					if(strlen($comen)>50){
						$pdf->SetFont('Arial','',8);
		                $x=$pdf->GetX();
		                $y=$pdf->GetY();                    
		                $pdf->Multicell(80,3,utf8_decode($comen),0,'L');
		                $pdf->SetXY($x,$y);
		                $pdf->Cell(80,6.5,"",1,0,'C');
		            }else{
		            	$pdf->Cell(80,6.5,utf8_decode($comen),1,0,'C',1);    
		            }

		            $pdf->Cell(15,6.5,utf8_decode($tarjeta),1,0,'C',1);

		            $pdf->SetFont('Arial','',9);
					//$pdf->Cell(80,6,utf8_decode($comen),1,0,'C',1);
					$pdf->Cell(15,6.5,$total,1,0,'C',1);

					$pdf->Cell(20,6.5,$situacion,1,0,'C',1);

					$pdf->Ln();
				}
		}

		if ($pv != 0) {
    		$pdf->Ln();
	    	$pdf->Cell(119);
	    	$pdf->SetFont('Arial','B',9);
	    	$pdf->Cell(40,6,utf8_decode('RESÚMEN DEL DÍA'),1,0,'C',1);
	    	$pdf->Ln();
	    	$pdf->Cell(119);
			$pdf->Cell(20,6,'Ingresos',1,0,'C',1);
			$pdf->Cell(20,6,number_format($ingreso,2,'.',''),1,0,'C',1);
			$pdf->Ln();
			$pdf->Cell(119);
			$pdf->SetFont('Arial','',9);
			$pdf->Cell(20,6,'Efectivo',1,0,'C',1);
			$pdf->Cell(20,6,number_format($efectivo,2,'.',''),1,0,'C',1);
			$pdf->Ln();
			$pdf->Cell(119);
			$pdf->Cell(20,6,'VISA',1,0,'C',1);
			$pdf->Cell(20,6,number_format($visa,2,'.',''),1,0,'C',1);
			$pdf->Ln();
			$pdf->Cell(119);
			$pdf->Cell(20,6,'MASTER',1,0,'C',1);
			$pdf->Cell(20,6,number_format($master,2,'.',''),1,0,'C',1);
			$pdf->Ln();
			$pdf->Cell(119);
			$pdf->SetFont('Arial','B',9);
			$pdf->Cell(20,6,'Egresos',1,0,'C',1);
			$pdf->Cell(20,6,number_format($egreso,2,'.',''),1,0,'C',1);
			$pdf->Ln();
			$pdf->Cell(119);
			$pdf->Cell(20,6,'Saldo',1,0,'C',1);
			$pdf->Cell(20,6,number_format($saldo,2,'.',''),1,0,'C',1);
			$pdf->Ln();
			$pdf->Cell(119);
			$pdf->Cell(20,6,'Garantia',1,0,'C',1);
			$pdf->Cell(20,6,number_format($garantia,2,'.',''),1,0,'C',1);
			$pdf->Ln();
			$pdf->Ln();
			$ingreso=0;$egreso=0;$garantia=0;$efectivo=0;$master=0;$visa=0;
    	}
		$pdf->Output();
	}

	function rNotaCredito($fi,$ff){
        $response = '';
        $db = new Conexion();

        $pdf = new PDF('P','mm','A4');
        $pdf->AliasNbPages();
        $pdf->AddPage();

        $pdf->SetFillColor(255,255,255);
        $pdf->SetFont('Arial','B',14);

        $pdf->Cell(190,10,utf8_decode('Reporte de Farmacia - Notas de Crédito'),0,0,'C',0);
        $pdf->Ln();

        $pdf->SetFillColor(68,164,168);
        $pdf->SetFont('Arial','B',12);
        
        $pdf->Cell(8);
        $pdf->Cell(25,6,'Fecha',1,0,'C',1);
        $pdf->Cell(22,6,'Tipo Doc.',1,0,'C',1);
        $pdf->Cell(20,6,'Nro',1,0,'C',1);
        $pdf->Cell(88,6,utf8_decode('Paciente'),1,0,'C',1);
        $pdf->Cell(25,6,utf8_decode('Total'),1,0,'C',1);

        $pdf->SetFont('Arial','',10);
        $pdf->SetFillColor(250,250,250);
        $pdf->Ln();

        $tp=0;

        $query=$db->query("SELECT movimiento.total, movimiento.fecha, movimiento.numero, paciente.nombres, paciente.apellidopaterno, paciente.apellidomaterno, m2.situacion as situacion2, m2.tipodocumento_id, m2.serie, m2.numero FROM movimiento left join person as paciente on movimiento.persona_id = paciente.id join person as responsable on responsable.id = movimiento.responsable_id left join movimiento as m2 on m2.id = movimiento.movimiento_id where movimiento.tipomovimiento_id = 6 AND movimiento.fecha BETWEEN '$fi' AND '$ff' order by movimiento.fecha DESC");

        while($rows=$db->recorrerobj($query)){
            $fecha = $rows->fecha;
            $numero = $rows->numero;
            $situacion2 = $rows->situacion2;
            $total = $rows->total;
            $tipodocumento_id = $rows->tipodocumento_id;
            $apellidopaterno = $rows->apellidopaterno;
            $apellidomaterno = $rows->apellidomaterno;
            $nombres = $rows->nombres;
            $paciente = $apellidopaterno.' '.$apellidomaterno.' '.$nombres;

            $tipodocumento_id == 4 ? $tipodocumento_id = 'F' : $tipodocumento_id = 'B';
            
            $pdf->Cell(8);
            $pdf->Cell(25,6,$fecha,1,0,'C',1);
            $pdf->Cell(22,6,utf8_decode($tipodocumento_id),1,0,'C',1);
            $pdf->Cell(20,6,utf8_decode($numero),1,0,'C',1);
            //$pdf->Cell(80,6,utf8_decode($situacion2),1,0,'C',1);
            $pdf->Cell(88,6,utf8_decode($paciente),1,0,'C',1);
            $pdf->Cell(25,6,number_format($total,2,'.',''),1,0,'C',1);

            $pdf->Ln();

            $tp += $total;
        }

        $pdf->Ln();
        $pdf->Cell(107);
        $pdf->SetFont('Arial','B',11);
        $pdf->Cell(30,6,'TOTAL',1,0,'C',1);
        $pdf->Cell(30,6,$tp,1,0,'C',1);
            
        $pdf->Output();
    }

    function rProductos($id, $tipo, $origen, $presentacion){
        $response = '';
        $db = new Conexion();

        $pdf = new PDF('L','mm','A4');
        $pdf->AliasNbPages();
        $pdf->AddPage();

        $pdf->SetFillColor(255,255,255);
        $pdf->SetFont('Arial','B',14);

        $pdf->Cell(290,10,utf8_decode('Reporte de Farmacia - Saldo de Productos'),0,0,'C',0);
        $pdf->Ln();

        if ($id == 0) {
        	if ($origen != 0) {
        		$query2 = $db->query("SELECT nombre from origen where id = '$origen'");

				while ($rows2=$db->recorrerobj($query2)) {
					$pdf->Cell(0,8,utf8_decode('Origen: '.$rows2->nombre),0,0,'C',1);
		        	$pdf->Ln();
				}
    		}

    		if ($presentacion != 0) {
        		$query2 = $db->query("SELECT nombre from presentacion where id = '$presentacion'");

				while ($rows2=$db->recorrerobj($query2)) {
					$pdf->Cell(0,8,utf8_decode('Presentación: '.$rows2->nombre),0,0,'C',1);
		        	$pdf->Ln();
				}
    		}
        }

        if ($tipo == 2) {
        	$query2 = $db->query("SELECT nombre from especialidadfarmacia where id = '$id'");

			while ($rows2=$db->recorrerobj($query2)) {
				$pdf->Cell(290,10,utf8_decode('Especialidad: '.$rows2->nombre),0,0,'C',1);
	        $pdf->Ln();
			}
        } else if ($tipo == 3) {
        	$query2 = $db->query("SELECT nombre from principioactivo where id = '$id'");

			while ($rows2=$db->recorrerobj($query2)) {
				$pdf->Cell(290,10,utf8_decode('Principio Activo: '.$rows2->nombre),0,0,'C',1);
	        $pdf->Ln();
			}
        }

        $pdf->SetFillColor(68,164,168);
        $pdf->SetFont('Arial','B',12);
        
        $pdf->Cell(6);
        $pdf->Cell(50,6,'Producto',1,0,'C',1);
        $pdf->Cell(35,6,utf8_decode('Presentación'),1,0,'C',1);
        $pdf->Cell(50,6,'Laboratorio',1,0,'C',1);
        $pdf->Cell(50,6,utf8_decode('Proveedor'),1,0,'C',1);
        $pdf->Cell(20,6,utf8_decode('Compra'),1,0,'C',1);
        $pdf->Cell(20,6,utf8_decode('Venta'),1,0,'C',1);
        $pdf->Cell(20,6,utf8_decode('Kayros'),1,0,'C',1);
        $pdf->Cell(25,6,utf8_decode('Existencias'),1,0,'C',1);

        $otroin = '';
        if ($id != 0) {
        	if ($tipo == 2) {
        		$otroin = 'join especialidadfarmacia e on p.especialidadfarmacia_id = e.id ';
        		$extra = 'AND e.id = '.$id;
        	} else if($tipo == 1) {
        		$extra = 'AND p.id = '.$id;
        	} else if($tipo == 3) {
        		$extra = 'AND pp.principioactivo_id = '.$id;
        	}
        } else {
        	$extra = '';
    		if ($origen != 0) {
    			$extra = 'AND p.origen_id = '.$origen;
    			if ($presentacion != 0) {
	    			$extra = $extra.' AND p.presentacion_id='.$presentacion;
	    		}
    		} else {
    			if ($presentacion != 0) {
	    			$extra = ' AND p.presentacion_id='.$presentacion;
	    		}
    		}
        }

        $pdf->SetFont('Arial','',10);
        $pdf->SetFillColor(250,250,250);
        $pdf->Ln();

        $query2 = $db->query("SELECT p.id, p.nombre producto, p.preciocompra, p.precioventa, p.preciokayros, u.bussinesname proveedor, pr.nombre presentacion, l.nombre laboratorio, pp.principioactivo_id princicioa FROM producto p join person u on p.proveedor_id = u.id LEFT JOIN presentacion AS pr ON p.presentacion_id = pr.id LEFT JOIN laboratorio AS l ON p.laboratorio_id = l.id join productoprincipio pp on pp.producto_id = p.id ".$otroin." WHERE p.deleted_at is null ".$extra." group by p.id ORDER BY p.nombre ASC");

		while ($rows2=$db->recorrerobj($query2)) {
			$pro_id = $rows2->id;
			$cantidad = 0;

			$query=$db->query("SELECT * FROM kardex LEFT JOIN detallemovimiento on detallemovimiento.id = kardex.detallemovimiento_id left JOIN movimiento on detallemovimiento.movimiento_id = movimiento.id WHERE kardex.deleted_at is NULL and movimiento.almacen_id = 1 and producto_id = ".$rows2->id." order by kardex.id desc limit 1");
			while($rows=$db->recorrerobj($query)){
			    $cantidad = $rows->stockactual;
			}

			$principioactivop = $rows2->princicioa;
			$producto = $rows2->producto;
		    $presentacion = $rows2->presentacion;
		    $laboratorio = $rows2->laboratorio;
		    $proveedor = $rows2->proveedor;
		    $compra = $rows2->preciocompra;
		    $venta = $rows2->precioventa;
		    $kayros = $rows2->preciokayros;
		    
		    
		    $pdf->Cell(6);
		    if(strlen($producto)>30){
				$pdf->SetFont('Arial','',8);
                $x=$pdf->GetX();
                $y=$pdf->GetY();                    
                $pdf->Multicell(50,3,utf8_decode($producto),0,'L');
                $pdf->SetXY($x,$y);
                $pdf->Cell(50,6.5,"",1,0,'C');
            }else{
            	$pdf->SetFont('Arial','',8);
                $pdf->Cell(50,6.5,utf8_decode($producto),1,0,'C',1);  
            }

	        $pdf->Cell(35,6.5,$presentacion,1,0,'C',1);
	        $pdf->Cell(50,6.5,$laboratorio,1,0,'C',1);
	        if(strlen($proveedor)>30){
				$pdf->SetFont('Arial','',8);
                $x=$pdf->GetX();
                $y=$pdf->GetY();                    
                $pdf->Multicell(50,3,utf8_decode($proveedor),0,'L');
                $pdf->SetXY($x,$y);
                $pdf->Cell(50,6.5,"",1,0,'C');
            }else{
            	$pdf->SetFont('Arial','',8);
                $pdf->Cell(50,6.5,utf8_decode($proveedor),1,0,'C',1);  
            }

	        $pdf->Cell(20,6.5,utf8_decode($compra),1,0,'C',1);
        	$pdf->Cell(20,6.5,utf8_decode($venta),1,0,'C',1);
        	$pdf->Cell(20,6.5,utf8_decode($kayros),1,0,'C',1);
	        $pdf->Cell(25,6.5,number_format($cantidad),1,0,'C',1);
			
			$pdf->Ln();

		}

		if ($tipo == 1) {
			$pdf->Ln();
			$pdf->SetFont('Arial','B',11);
			$pdf->Cell(0,6.5,'Productos Similares:',0,0,'L',0);
			$pdf->SetFont('Arial','',10);
			$pdf->Ln();

			$query3 = $db->query("SELECT p.id, p.nombre producto, p.preciocompra, p.precioventa, p.preciokayros, u.bussinesname proveedor, pr.nombre presentacion, l.nombre laboratorio FROM producto p join person u on p.proveedor_id = u.id LEFT JOIN presentacion AS pr ON p.presentacion_id = pr.id LEFT JOIN laboratorio AS l ON p.laboratorio_id = l.id join productoprincipio pp on pp.producto_id = p.id where pp.principioactivo_id = '$principioactivop' AND p.id != '$pro_id' ORDER BY p.nombre ASC");

			while ($rows3=$db->recorrerobj($query3)) {
				$query=$db->query("SELECT  stockactual as cantidad FROM kardex INNER JOIN detallemovimiento on kardex.detallemovimiento_id = detallemovimiento.id INNER JOIN movimiento on detallemovimiento.movimiento_id = movimiento.id WHERE movimiento.almacen_id = 1 and detallemovimiento.producto_id = ".$rows3->id." order by kardex.id DESC limit 1");
				$cantidad = 0;
				while($rows=$db->recorrerobj($query)){
				    $cantidad = $rows->cantidad;
				}

				$producto = $rows3->producto;
			    $presentacion = $rows3->presentacion;
			    $laboratorio = $rows3->laboratorio;
			    $proveedor = $rows3->proveedor;
			    $compra = $rows3->preciocompra;
			    $venta = $rows3->precioventa;
			    $kayros = $rows3->preciokayros;
			    
			    
			    $pdf->Cell(6);
			    if(strlen($producto)>30){
					$pdf->SetFont('Arial','',8);
	                $x=$pdf->GetX();
	                $y=$pdf->GetY();                    
	                $pdf->Multicell(50,3,utf8_decode($producto),0,'L');
	                $pdf->SetXY($x,$y);
	                $pdf->Cell(50,6.5,"",1,0,'C');
	            }else{
	            	$pdf->SetFont('Arial','',8);
	                $pdf->Cell(50,6.5,utf8_decode($producto),1,0,'C',1);  
	            }

		        $pdf->Cell(35,6.5,$presentacion,1,0,'C',1);
		        $pdf->Cell(50,6.5,$laboratorio,1,0,'C',1);
		        if(strlen($proveedor)>30){
					$pdf->SetFont('Arial','',8);
	                $x=$pdf->GetX();
	                $y=$pdf->GetY();                    
	                $pdf->Multicell(50,3,utf8_decode($proveedor),0,'L');
	                $pdf->SetXY($x,$y);
	                $pdf->Cell(50,6.5,"",1,0,'C');
	            }else{
	            	$pdf->SetFont('Arial','',8);
	                $pdf->Cell(50,6.5,utf8_decode($proveedor),1,0,'C',1);  
	            }

		        $pdf->Cell(20,6.5,utf8_decode($compra),1,0,'C',1);
	        	$pdf->Cell(20,6.5,utf8_decode($venta),1,0,'C',1);
	        	$pdf->Cell(20,6.5,utf8_decode($kayros),1,0,'C',1);
		        $pdf->Cell(25,6.5,number_format($cantidad),1,0,'C',1);
				
				$pdf->Ln();

			}
		}
            
        $pdf->Output();
    }

    function rKardex($fi,$ff,$p_id){
        $response = '';
        $db = new Conexion();

        $pdf = new PDF('P','mm','A4');
        $pdf->AliasNbPages();
        $pdf->AddPage();

        $pdf->SetFillColor(255,255,255);
        $pdf->SetFont('Arial','B',14);

        $query=$db->query("SELECT p.nombre, r.nombre proveedor FROM producto p join laboratorio r on p.laboratorio_id = r.id where p.id = '$p_id' ");

		while($rows=$db->recorrerobj($query)){
			$producto = $rows->nombre;
			$provee = $rows->proveedor;
		}
		if (!isset($provee)) {
			$provee = 'NO REGISTRADO';
			 $query=$db->query("SELECT p.nombre FROM producto p where p.id = '$p_id' ");

			while($rows=$db->recorrerobj($query)){
				$producto = $rows->nombre;
			}
		}

        $pdf->Cell(190,10,utf8_decode('Movimientos - '.$producto),0,0,'C',0);
        $pdf->Ln();
        $pdf->Cell(190,10,utf8_decode($fi.' al '.$ff),0,0,'C',1);
        $pdf->Ln();
        $pdf->SetFont('Arial','B',7);
        $pdf->Cell(50,10,utf8_decode('Laboratorio: '.$provee),0,0,'L',1);
        $pdf->Ln();

        $pdf->SetFillColor(68,164,168);
        $pdf->SetFont('Arial','B',9);
        
        //$pdf->Cell(8,6,'#',1,0,'C',1);
        $pdf->Cell(20,6,'Fecha',1,0,'C',1);
        $pdf->Cell(15,6,'Hora',1,0,'C',1);
        //$pdf->Cell(40,6,'Producto',1,0,'C',1);
        //$pdf->Cell(35,6,'Laboratorio',1,0,'C',1);
        $pdf->Cell(20,6,'Responsable',1,0,'C',1);
        $pdf->Cell(35,6,'Tipo Doc',1,0,'C',1);
        $pdf->Cell(20,6,'Nro Doc',1,0,'C',1);
        $pdf->Cell(25,6,'Vencimiento',1,0,'C',1);
        $pdf->Cell(20,6,'Ingreso',1,0,'C',1);
        $pdf->Cell(20,6,'Egreso',1,0,'C',1);
        $pdf->Cell(20,6,'Saldo Final',1,0,'C',1);

        $pdf->SetFont('Arial','',9);
        $pdf->SetFillColor(250,250,250);
        $pdf->Ln();
        $entradas = 0;
        $salidas = 0;

        //SELECT * FROM detallemovimiento d join kardex k on d.id = k.detallemovimiento_id WHERE k.lote_id = '$lote'
        $num = 1;
        $query=$db->query("SELECT k.fecha, TIME(k.created_at) hora, m.situacion, k.cantidad, k.tipo, k.stockactual, p.nombre producto, u.nombres usuario, m.tipomovimiento_id, m.numero, m.tipodocumento_id FROM kardex k join detallemovimiento dm on k.detallemovimiento_id = dm.id join movimiento m on dm.movimiento_id = m.id join person u on u.id = m.responsable_id JOIN producto p on dm.producto_id = p.id where m.deleted_at is null and p.id = '".$p_id."' AND k.fecha BETWEEN '".$fi."' AND '".$ff."' order by k.fecha asc, TIME(k.created_at) asc");
		while($rows=$db->recorrerobj($query)){
			//$detalleid = $rows->detalleid;
		    //$cantidadmov = $rows->cantidad;
		    $tipo = $rows->tipomovimiento_id;
		    $tipokardex = $rows->tipo;
       		$stof=$rows->stockactual;
       		$situacion=$rows->situacion;

		    $fecha = $rows->fecha;
		    $hora = $rows->hora;
		    //$tipo = $rows->tipomovimiento_id;
		    $cantidad = $rows->cantidad;
		    
		    $numero = $rows->numero;
		    $usuario = $rows->usuario;
		    $tdocumento = $rows->tipodocumento_id;
		    $tipom = $tipo;
		    if($tipo != 4){
		    	if ($tipo == 5) {
		    		if ($tipokardex == 'I') {
		    			$tipo = 'INGRESO';
			    		$entradas += $cantidad;
			    		$egreso = '-';
			    		$ingreso = number_format($cantidad);
			    	} else {
			    		$tipo = 'SALIDA';
			    		$ingreso = '-';
			    		$salidas += $cantidad;
			    		$egreso = number_format($cantidad);
			    	}
		    	} else if ($tipo == 3){
		    		$tipo = 'INGRESO';
		    		$entradas+=$cantidad;
		    		$egreso = '-';
		    		$ingreso = number_format($cantidad);
		    	} else if ($tipo == 6){
		    		if ($tipokardex == 'I') {
		    			$tipo = 'INGRESO';
			    		$entradas += $cantidad;
			    		$egreso = '-';
			    		$ingreso = number_format($cantidad);
			    	} else {
			    		$tipo = 'SALIDA';
			    		$ingreso = '-';
			    		$salidas += $cantidad;
			    		$egreso = number_format($cantidad);
			    	}
		    	}
		    } else {
		    	if ($tipokardex == 'I') {
	    			$tipo = 'INGRESO';
		    		$entradas += $cantidad;
		    		$egreso = '-';
		    		$ingreso = number_format($cantidad);
		    	} else {
		    		$tipo = 'SALIDA'; 
		    		$salidas+=$cantidad;
		    		$egreso = number_format($cantidad);
		    		$ingreso = '-';
		    	}
		    }
		    //$vence = $rows->fechavencimiento;
		    $vence = '-';

		    $estadopago = '';
		    if ($situacion != 'N' && $situacion!="A"){
		    	$estadopago = " ANULADO";
		    }

		    if($tdocumento != NULL){
				$query2=$db->query("SELECT nombre FROM tipodocumento where id = '$tdocumento' ");
				while($rows2=$db->recorrerobj($query2)){
					$tdocumento = $rows2->nombre;
				}
				$tdocumento = $tdocumento.$estadopago;
		    } else {
		    	$tdocumento = '-';
		    }

		    $pdf->Cell(20,6,$fecha,1,0,'C',1);
		    $pdf->Cell(15,6,$hora,1,0,'C',1);

	        if(strlen($usuario)>10){
				$pdf->SetFont('Arial','',8);
                $x=$pdf->GetX();
                $y=$pdf->GetY();                    
                $pdf->Multicell(20,3,utf8_decode($usuario),0,'L');
                $pdf->SetXY($x,$y);
                $pdf->Cell(20,6,"",1,0,'C');
                $pdf->SetFont('Arial','',9);
            }else{
            	
            	$pdf->Cell(20,6,utf8_decode($usuario),1,0,'C',1);
            }
            if(strlen($tdocumento)>18){
				$pdf->SetFont('Arial','',8);
                $x=$pdf->GetX();
                $y=$pdf->GetY();                    
                $pdf->Multicell(35,3,utf8_decode($tdocumento),0,'L');
                $pdf->SetXY($x,$y);
                $pdf->Cell(35,6,"",1,0,'C');
                $pdf->SetFont('Arial','',9);
            }else{
            	
            	$pdf->Cell(35,6,$tdocumento,1,0,'C',1);
            }
	        $pdf->Cell(20,6,$numero,1,0,'C',1);
	        $pdf->Cell(25,6,$vence,1,0,'C',1);
	        $pdf->Cell(20,6,$ingreso,1,0,'C',1);
	        $pdf->Cell(20,6,$egreso,1,0,'C',1);
	        $pdf->Cell(20,6,number_format($stof),1,0,'C',1);
			
			$pdf->Ln();
			$num++;
		}

        $pdf->Ln();
     	$pdf->Cell(50);
	    $pdf->Cell(30,6,'Ingresos:',1,0,'C',1);
        $pdf->Cell(35,6,$entradas,1,0,'C',1);
        $pdf->Ln();
        $pdf->Cell(50);
        $pdf->Cell(30,6,'Egresos:',1,0,'C',1);
        $pdf->Cell(35,6,$salidas,1,0,'C',1);
        //$pdf->Ln();
        //$pdf->Cell(50);
        //$pdf->SetFillColor(150,150,150);
        //$pdf->Cell(30,6,'STOCK:',1,0,'C',1);
        //$pdf->Cell(35,6,$entra-$sale,1,0,'C',1);
			  
        $pdf->Output();
    }

    function bProductos($nomb){
    	$db = new Conexion();
    	$response = '';
    	$query=$db->query("SELECT id, nombre from producto where nombre like '%$nomb%' order by nombre asc");

		while($rows=$db->recorrerobj($query)){
			$id = $rows->$id;
			$response = $response.'<div style="width:100%;" onclick="autocompleta('.$id.')">'.$nombre.'</div>';
		}

		echo $response;
    }

    function rPedido($fi,$ff){
        $response = '';
        $db = new Conexion();

        $pdf = new PDF('P','mm','A4');
        $pdf->AliasNbPages();
        $pdf->AddPage();

        $pdf->SetFillColor(255,255,255);
        $pdf->SetFont('Arial','B',14);

        $pdf->Cell(190,10,utf8_decode('Reporte de Farmacia - Notas de Crédito'),0,0,'C',0);
        $pdf->Ln();

        $pdf->SetFillColor(68,164,168);
        $pdf->SetFont('Arial','B',12);
        
        $pdf->Cell(8);
        $pdf->Cell(25,6,'Fecha',1,0,'C',1);
        $pdf->Cell(22,6,'Tipo Doc.',1,0,'C',1);
        $pdf->Cell(20,6,'Nro',1,0,'C',1);
        $pdf->Cell(88,6,utf8_decode('Paciente'),1,0,'C',1);
        $pdf->Cell(25,6,utf8_decode('Total'),1,0,'C',1);

        $pdf->SetFont('Arial','',10);
        $pdf->SetFillColor(250,250,250);
        $pdf->Ln();

        $tp=0;

        $query=$db->query("SELECT movimiento.total, movimiento.fecha, movimiento.numero, paciente.nombres, paciente.apellidopaterno, paciente.apellidomaterno, m2.situacion as situacion2, m2.tipodocumento_id, m2.serie, m2.numero FROM movimiento left join person as paciente on movimiento.persona_id = paciente.id join person as responsable on responsable.id = movimiento.responsable_id left join movimiento as m2 on m2.id = movimiento.movimiento_id where movimiento.tipomovimiento_id = 6 AND movimiento.fecha BETWEEN '$fi' AND '$ff' order by movimiento.fecha DESC");

        while($rows=$db->recorrerobj($query)){
            $fecha = $rows->fecha;
            $numero = $rows->numero;
            $situacion2 = $rows->situacion2;
            $total = $rows->total;
            $tipodocumento_id = $rows->tipodocumento_id;
            $apellidopaterno = $rows->apellidopaterno;
            $apellidomaterno = $rows->apellidomaterno;
            $nombres = $rows->nombres;
            $paciente = $apellidopaterno.' '.$apellidomaterno.' '.$nombres;

            $tipodocumento_id == 4 ? $tipodocumento_id = 'F' : $tipodocumento_id = 'B';
            
            $pdf->Cell(8);
            $pdf->Cell(25,6,$fecha,1,0,'C',1);
            $pdf->Cell(22,6,utf8_decode($tipodocumento_id),1,0,'C',1);
            $pdf->Cell(20,6,utf8_decode($numero),1,0,'C',1);
            //$pdf->Cell(80,6,utf8_decode($situacion2),1,0,'C',1);
            $pdf->Cell(88,6,utf8_decode($paciente),1,0,'C',1);
            $pdf->Cell(25,6,utf8_decode($total),1,0,'C',1);

            $pdf->Ln();

            $tp += $total;
        }

        $pdf->Ln();
        $pdf->Cell(107);
        $pdf->SetFont('Arial','B',11);
        $pdf->Cell(30,6,'TOTAL',1,0,'C',1);
        $pdf->Cell(30,6,$tp,1,0,'C',1);
            
        $pdf->Output();
    }

    function rProveedores(){
		$response = '';
		$db = new Conexion();

		$pdf = new PDF('P','mm','A4');
		$pdf->AliasNbPages();
		$pdf->AddPage();

		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','B',14);

		$pdf->Cell(190,10,utf8_decode('PROVEEDORES'),0,0,'C',0);
		$pdf->Ln();

		$pdf->SetFillColor(68,164,168);
		$pdf->SetFont('Arial','B',12);
		
		$pdf->Cell(60,6,utf8_decode('Razón Social'),1,0,'C',1);
		$pdf->Cell(26,6,'RUC',1,0,'C',1);
		$pdf->Cell(77,6,utf8_decode('Dirección'),1,0,'C',1);
		$pdf->Cell(27,6,'Telefono',1,0,'C',1);

		$pdf->SetFont('Arial','',8.5);
		$pdf->SetFillColor(250,250,250);
		$pdf->Ln();

		$query=$db->query("SELECT p.bussinesname, p.ruc, p.direccion, p.telefono FROM rolpersona as r join person as p on r.person_id = p.id WHERE rol_id = '2' order by bussinesname asc");

		while($rows=$db->recorrerobj($query)){
		    $nombre = $rows->bussinesname;
		    $ruc = $rows->ruc;
		    $direccion = $rows->direccion;
		    $telefono = $rows->telefono;
		    
		    //$pdf->Cell(68,6,utf8_decode($nombre),1,0,'C',1);
		    if(strlen($nombre)>33){
				$pdf->SetFont('Arial','',7.5);
                $x=$pdf->GetX();
                $y=$pdf->GetY();                    
                $pdf->Multicell(60,3,utf8_decode($nombre),0,'C');
                $pdf->SetXY($x,$y);
                $pdf->Cell(60,6,"",1,0,'C');
            }else{
            	$pdf->SetFont('Arial','',8.5);
            	$pdf->Cell(60,6,utf8_decode($nombre),1,0,'C',1);    
            }
			$pdf->Cell(26,6,$ruc,1,0,'C',1);
			
			if(strlen($direccion)>43){
				$pdf->SetFont('Arial','',7.5);
                $x=$pdf->GetX();
                $y=$pdf->GetY();                    
                $pdf->Multicell(77,3,utf8_decode($direccion),0,'L');
                $pdf->SetXY($x,$y);
                $pdf->Cell(77,6,"",1,0,'C');
            }else{
            	$pdf->SetFont('Arial','',8.5);
            	$pdf->Cell(77,6,utf8_decode($direccion),1,0,'C',1);
            }
			
			if(strlen($telefono)>15){
				$pdf->SetFont('Arial','',7.5);
                $x=$pdf->GetX();
                $y=$pdf->GetY();                    
                $pdf->Multicell(27,3,utf8_decode($telefono),0,'L');
                $pdf->SetXY($x,$y);
                $pdf->Cell(27,6,"",1,0,'C');
            }else{
            	$pdf->SetFont('Arial','',8.5);
            	$pdf->Cell(27,6,$telefono,1,0,'C',1);
            }

			$pdf->Ln();
		}
			
		$pdf->Output();
	}
 ?>