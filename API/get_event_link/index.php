<?php
	//error_reporting(E_ALL);
	//ini_set('display_errors', 1);
	
	session_start();
	


	setlocale(LC_ALL, 'en_US.UTF-8');
	header('Content-type: text/javascript; charset=utf-8');
		
	include_once($_SERVER['DOCUMENT_ROOT']. '/PHP/MYF1.php');
	
	
	
	// VERIFICA SI LA LLAMADA CONTIENE ID, SI NO, CREA UN ID
		if ( $_GET['id'] == '' || !isset($_GET['id'])){
			$ID     = GET_AN_ID('Events');
			$IS_NEW = true;
		}else {
			$ID     = $_GET['id'];
			$IS_NEW = false;
		}
	

		
		$after_slash = sprintf('IO/index.php?e_id=%s', $ID );
		
		$EVENT_LINK = ADD_CUSTOM_LINK($ID,$after_slash);

		//$EVENT_LINK = $after_slash;
		$q = sprintf("UPDATE u124132715_DB.Events SET Datos = JSON_SET(Datos, \"$.EVENT_LINK\", '%s') WHERE id = '%s'",  $EVENT_LINK, $ID); 
		$R1 = SQL_2_OBJ_V2($q);
	
	
	// LEE DATOS NUEVAMENTE 
		$q = sprintf("SELECT * FROM u124132715_DB.Events WHERE id = '%s'", $ID    );  
		$R1 = SQL_2_OBJ_V2($q);
		//unset($R1['QRY']);
		//$RESP['DATOS'] = $R1;  
		//echo $q; 
		//return;
 
 
	
	foreach($R1['PL'] as $KEY=>$VALUE){
		
		//echo $VALUE['Datos'];
		//print_r( $RENGLON['Datos']);
		
		//$ARR_OUT[$VALUE['id']] =  $VALUE['Datos'];
		$ARR_OUT[] = $VALUE;
		
	}
	
	$JSON_OUT = json_encode($ARR_OUT, true || JSON_PRETTY_PRINT);
	echo $JSON_OUT;
	return; 
 
?>