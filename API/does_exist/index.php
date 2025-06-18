<?php
	
//error_reporting(E_ALL);
//ini_set('display_errors', 1);
	

	
//echo $_SERVER['DOCUMENT_ROOT'] . '/ADMIN/PHP/MYF1.php';
include_once($_SERVER['DOCUMENT_ROOT'] . '/PHP/MYF1.php');



setlocale(LC_ALL, 'en_US.UTF-8');
header('Content-type: text/javascript; charset=utf-8');
	

if(session_id() == '' || !isset($_SESSION) || session_status() === PHP_SESSION_NONE) {
	// session isn't started
	session_start( [ 'cookie_lifetime' => 604800 ]); 

}
 

	
	$entityBody = file_get_contents('php://input');
	$BODY_OB = json_decode($entityBody, true);		//$BODY_OB = json_decode($BODY_EN, true);
	
	$DATA = $BODY_OB;   
	//echo 'ok';
	//print_r($DATA);die;

function THIS_FUNCTION( $F_PARAMS ) {


	$q = sprintf("SELECT * FROM %s WHERE JSON_EXTRACT(Datos, '$.EMAIL') = '%s' ", $F_PARAMS['TABLA'], $F_PARAMS['SEARCH_FOR']);  //echo $q . "\n";
	$R1 = SQL_2_OBJ_V2($q);
	$RESP['R1'] = $R1; //print_r($R1);
	$DB_DATA = $R1['DATA'];

	return $R1;

}


		//$PARAMS['TABLE'] 			= 'Users';
		//$PARAMS['SEARCH_FOR'] 	= 'cear83@gmail.com';


		$RESP['SUCCESS'] 	= 1; 
		//$RESP['IN'] 		= $DATA; 

		$F_RESULT = THIS_FUNCTION( $DATA ); 

		if ($F_RESULT['QRY']['ROWS'] != 0 ) {

			$RESP['PROMPT'] 	= 'Usuario existente.'; 
		} else {
			$RESP['PROMPT'] 	= 'Usuario disponible'; 
		}

		$RESP['DATOS'] 		= $F_RESULT['QRY']['ROWS'];
		

		//print_r (THIS_FUNCTION( $PARAMS )); 
		echo json_encode($RESP, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
		
		//echo 'ok'; //die;

		return;


	
?>
