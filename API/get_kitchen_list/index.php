<?php
	
//error_reporting(E_ALL);
//ini_set('display_errors', 1);
	
//echo $_SERVER['DOCUMENT_ROOT'] . '/ADMIN/PHP/MYF1.php';
include_once($_SERVER['DOCUMENT_ROOT'] . '/PHP/MYF1.php');



//setlocale(LC_ALL, 'en_US.UTF-8');
//header('Content-type: text/javascript; charset=utf-8');
	
	$entityBody = file_get_contents('php://input');
	$BODY_OB = json_decode($entityBody, true);		//$BODY_OB = json_decode($BODY_EN, true);
	
	$DATA = $BODY_OB;   

		$EVENTO['ID'] = $DATA['ID'];

		$q = sprintf("SELECT * FROM u124132715_DB.Events WHERE id = '%s'", $EVENTO['ID']);  //echo $q . "\n";
		$R1 = SQL_2_OBJ_V2($q);
		$RESP['R1'] = $R1; //print_r($R1); die;
		$EVENTO = $R1['PL'][0];
		
		//print_r($EVENTO);die;

		
		
		// CONFIRMADOS 
		
		$q = sprintf("SELECT count(id) AS CONFIRMED_GUESTS FROM u124132715_DB.SELECTIONS WHERE JSON_EXTRACT(Datos, '$.E_ID') = '%s' " ,$EVENTO['ID'] );  //echo $q . "\n";
		$R2 = SQL_2_OBJ_V3($q);
		$CONFIRMED_GUESTS = $R2['OBJ'][0]['CONFIRMED_GUESTS']; //print_r($R2['OBJ'][0]['CONFIRMED_GUESTS']); echo $CONFIRMED_GUESTS . "\n";
		
		// FECHA LIMITE
		
		$date = new DateTime($EVENTO['FECHA']);
		$date->sub(new DateInterval('P14D')); // subtract 14 days
		$FECHA_LIMITE = $date->format('Y-m-d'); // print the date
		
		
		
		// SELECCIONES 
		

			
			$q = sprintf("SELECT 
							JSON_UNQUOTE(JSON_EXTRACT(Datos, '$.SELECTED.T1')) AS T1,
							JSON_UNQUOTE(JSON_EXTRACT(Datos, '$.SELECTED.T2')) AS T2,
							JSON_UNQUOTE(JSON_EXTRACT(Datos, '$.SELECTED.T3')) AS T3,
							JSON_UNQUOTE(JSON_EXTRACT(Datos, '$.SELECTED.T4')) AS T4
						FROM 
							SELECTIONS
						WHERE 
							JSON_UNQUOTE(JSON_EXTRACT(Datos, '$.E_ID')) = '%s' ", $EVENTO['ID']);
							
			$R1 = SQL_2_OBJ_V3($q);
			$SELECTIONS_LIST = $R1['OBJ']; 
			//echo $q . "\n";

			//print_r($SELECTIONS_LIST);//die;

			$SUMMARY = SUMMARY($SELECTIONS_LIST);
			//print_r($SUMMARY);die;
			
			$DEFAULT_MENU_ARR = GET_DEFAULT_OPTIONS($EVENTO);
			//print_r($DEFAULT_MENU_ARR); //die;
			
			$KITCHEN_LIST_ARR = KITCHEN_LIST($SUMMARY, $EVENTO['COMENSALES'], $DEFAULT_MENU_ARR);
			//print_r($KITCHEN_LIST_ARR); die;
			
			
			$KITCHEN_LIST_HTML = createHtmlList($KITCHEN_LIST_ARR);
			
			$TOTALS_JSON = json_encode(calculateTotals($KITCHEN_LIST_ARR));
			$TOTALS_LIST_HTML = createHtmlListFromJson($TOTALS_JSON);
			
			$html = <<<H
						<div class="col">
							<div class="card shadow-none border bg-gradient-start-3">
								<div class="card-body p-20">
									<div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
										<div>
											<p class="fw-medium text-primary-light mb-1">Fecha</p>
											<h6 class="mb-0">{$EVENTO['FECHA']}</h6>
										</div>
										
										<div>
											<p class="fw-medium text-primary-light mb-1">Comensales</p>
											<h6 class="mb-0">{$EVENTO['COMENSALES']}</h6>
										</div>
										<div>
											<p class="fw-medium text-primary-light mb-1">Selecciones</p>
											<h6 class="mb-0">{$CONFIRMED_GUESTS}</h6>
										</div>
										
										<div class="w-50-px h-50-px bg-info rounded-circle d-flex justify-content-center align-items-center">
											<iconify-icon icon="mingcute:dish-cover-line" class="text-base text-2xl mb-0"></iconify-icon>
											
										</div>
									</div>
									<p class="fw-medium text-sm text-primary-light mt-12 mb-0 pt-10 border border-bottom-0 border-start-0 border-end-0">
										{$KITCHEN_LIST_HTML}
									</p>
									<p class="fw-medium text-sm text-primary-light mt-12 mb-0 pt-10 border border-bottom-0 border-start-0 border-end-0">
										{$TOTALS_LIST_HTML}
									</p>
									
								</div>
							</div><!-- card end -->
						</div>
						H;
			
			
			
			
			
			$RESP['DATOS'] = $html;
			
			//print_r($KITCHEN_LIST); die;
			
		
		echo json_encode($RESP, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
		
		//return $KITCHEN_LIST;
	
	
	
	
	
?>
