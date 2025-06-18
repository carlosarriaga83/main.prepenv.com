<?php
	
	

	
setlocale(LC_ALL, 'en_US.UTF-8');
header('Content-type: text/javascript; charset=utf-8');
	
//error_reporting(E_ALL);
//ini_set('display_errors', 1);
 
 
 

include_once($_SERVER['DOCUMENT_ROOT'] . '/PHP/MYF1.php');


$entityBody = file_get_contents('php://input');
$BODY_OB = json_decode($entityBody, true);		//$BODY_OB = json_decode($BODY_EN, true);
$DATA = $BODY_OB;   
$DATA_STR = json_encode($DATA , true);



		
function FUNCION($DATA){
	



		//$DATA['EVENT_ID'] = 220;
		
		$q = sprintf("SELECT * FROM SELECTIONS WHERE JSON_EXTRACT(Datos,'$.E_ID') = '%s' ", $DATA['EVENT_ID'] ) ;
		$R1 = SQL_2_OBJ_V2($q);
		//$RESP['R1'] = $R1; //print_r($R1);
			
		$ARR = [];
		
		foreach( $R1['PL'] as $X ){
			
			$T1 = DISH_ID_2_NAME($X['SELECTED']['T1']);
			$T2 = DISH_ID_2_NAME($X['SELECTED']['T2']);
			$T3 = DISH_ID_2_NAME($X['SELECTED']['T3']);
			$T4 = DISH_ID_2_NAME($X['SELECTED']['T4']);
			
			$RESTRICCIONES = '';
			
			//foreach($X['SELECTED']['RESTRICTIONS'] as $RESTRICCION ){ $RESTRICCIONES .= $RESTRICCION; }
			
			$ARR[] = [
			'Usuario'=>$X['USER'],
			'Nombre'=>$X['NAME'],
			'1 Tiempo'=>$T1,
			'2 Tiempo'=>$T2,
			'3 Tiempo'=>$T3,
			'4 Tiempo'=>$T4,
			'Alergias'=>$X['SELECTED']['ALERGIAS'],
			'Restricciones'=> implode(', ', $X['SELECTED']['RESTRICTIONS'])
			];
			
		}
		
		//print_r($ARR);die;
		
		$FILE_PATH = JSON_2_EXCEL( json_encode($ARR, true), $_SERVER['DOCUMENT_ROOT'] . '/_REPO/LISTAS/' , 'Lista_'. $DATA['EVENT_ID'] . '_' . GET_MEX_TS() . '.xlsx');
		
			
		//if ($FILE_PATH == 'ERROR' ) {$RESP['PROMPT'] = 'ERROR'; return;}
		
		$RESP['PATH'] =  str_replace($_SERVER['DOCUMENT_ROOT'], '',  $FILE_PATH);
		
		//print_r($RESP);die;
		
	
	return $RESP;



}



$RESP = FUNCION($DATA);

//print_r($RESP);die;

//return $RESP;

echo json_encode($RESP, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); 

return;


?>