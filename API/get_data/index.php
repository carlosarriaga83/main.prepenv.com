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
	//$KEYS = array_keys(json_decode($DATA , true)); 
	//echo 'ok';
	//print_r($KEYS);
	//print_r($DATA);  
	
	//$DATA_STR = json_encode($DATA , true);

	
		if (isset( $DATA['DB_ID'] )) { $db_id = $DATA['DB_ID']; } else { $db_id = 0;}
	
		$q = sprintf("SELECT * FROM %s WHERE id = '%s'", $DATA['TABLA'], $DATA['ID']);  //echo $q . "\n";
		$R1 = SQL_2_OBJ_V3($q, $db_id);

	
		$RESP['SUCCESS'] 	= 1; 
		$RESP['DATOS'] 		= $R1;  
		$RESP['PROMPT'] 	= 'Data leida OK.' . $db_id; 
		
		echo json_encode($RESP, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
		
		return;
	
	
	
	
	
?>
