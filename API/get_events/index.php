<?php

	session_start();
	

	
	include($_SERVER['DOCUMENT_ROOT']. '/PHP/MYF1.php');
	
	$id = isset($_GET['id']) ? intval($_GET['id']) : 0;


	$q = sprintf("SELECT * FROM u124132715_DB.Events WHERE id = '%s' ", $id);  //echo $q . "\n";
	$R1 = SQL_2_OBJ_V2($q);
	unset($R1['QRY']);
	//print_r(json_encode($R1['OBJ'], JSON_PRETTY_PRINT));
	
	//$json_obj = {};
	
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