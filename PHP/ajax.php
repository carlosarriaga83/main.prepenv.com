<?php
include_once 'MYF1.php';
	
setlocale(LC_ALL, 'en_US.UTF-8');
header('Content-type: text/javascript; charset=utf-8');
	
	//error_reporting(E_ALL);
	//ini_set('display_errors', 1);

if(session_id() == '' || !isset($_SESSION) || session_status() === PHP_SESSION_NONE) {
	// session isn't started
	session_start( [ 'cookie_lifetime' => 604800 ]); 

}
 


//require '../vendor/autoload.php';
//require __DIR__ . '/vendor/autoload.php';

//use PhpOffice\PhpSpreadsheet\IOFactory;
//use Twilio\Rest\Client;
	
	
	$entityBody = file_get_contents('php://input');
	$BODY_OB = json_decode($entityBody, true);		//$BODY_OB = json_decode($BODY_EN, true);
	
	$DATA = $BODY_OB;   
	//$KEYS = array_keys(json_decode($DATA , true)); 
//echo 'ok';
	//print_r($KEYS);
	//print_r($DATA);  
	
	$DATA_STR = json_encode($DATA , true);
	//print_r($DATA_STR); 
	//print_r($DATOS['DATA']);
	
	
	
	//$db_login = mysqli_connect('localhost', 'u124132715_SYP' ,'Salypimienta1!', 'u124132715_SYP'); 
	
	//$_SESSION['ID'] = session_id(); 
	
	//print_r($DATA);
		
	
	
	// CREA SESSION CON DATOS DE BROKER
	if( $DATA['R'] == '0' ){
		
			$RESP['PROMPT'] = '';
			$RESP['DATOS'] = '';
			$RESP['ERR'] = ''; 
		
		if ( strlen($DATA['S_ID']) > 0) {
			//$_SESSION['BROKER'] = GET_BROKER_INFO($DATA['S_ID']) ;
			//$_SESSION['BROKER']['ID'] = $DATA['S_ID'];
			//$_SESSION['REFERAL_ID'] = $DATA['S_ID'];
			$_SESSION['HIS_BROKER'] = $DATA['S_ID'];
		}else{
			//$_SESSION['BROKER'] = GET_BROKER_INFO('1') ;
			//$_SESSION['BROKER']['ID'] = '1';
			//$_SESSION['REFERAL_ID'] = '1';
			$_SESSION['HIS_BROKER'] = '1';
		}
		
		$RESP['SESSION'] = $_SESSION;
		
		echo json_encode($RESP);
		
		$db_login -> close(); die;
	}
	

	
	if( $DATA['R'] == 1 ){
		
		$q = sprintf("INSERT INTO u124132715_DB.%s (Datos) VALUES ('%s')", $DATA['tabla'], $DATA['Datos']); 
		//echo $q . "\n";
		
		$result = mysqli_query($db_login, $q);$ERR = mysqli_error($db_login);
		
		if($ERR != ''){ $RESP['ERR'] = $ERR; echo json_encode($RESP); $db_login -> close(); die;	}
		
	
		
		while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
			
			$myArr[] = $row['Datos'];
		}
		
		$INSERTED_ID = mysqli_insert_id($db_login);
		
		if ($_SESSION['nivel'] == 1){}
		
		$RESP['INSERTED_ID'] = $INSERTED_ID;
		$RESP['DATOS'] = $myArr;
		
		$JSON_OBJ = json_decode($DATA['Datos'], true);
		$ORIGIN = $JSON_OBJ['TXT_PROP_MAIN_PIC'];
		$DESTINATION = '../REPO/PROPERTIES/' . $INSERTED_ID . '/' . basename($JSON_OBJ['TXT_PROP_MAIN_PIC']);
		 
		if ( MOVE_FILE( $ORIGIN, $DESTINATION) ){} else  { $RESP['ERR'] = 'Error saving file.' . "\n" . $ORIGIN . "\n" . $DESTINATION; echo json_encode($RESP); }  
		
		$q = sprintf("UPDATE u124132715_DB.%s SET Datos = JSON_SET(Datos, '%s', '%s') WHERE id = %s",$DATA['tabla'], '$."TXT_PROP_MAIN_PIC"',$DESTINATION, $INSERTED_ID) ;  
		//echo $q . "\n"; 
		$result = mysqli_query($db_login, $q);$ERR = mysqli_error($db_login); 
		
		echo json_encode($RESP);
		
		$db_login -> close(); die;
	}
	
	
	
	if( $DATA['R'] == 2 ){
	
		
		
		$q = sprintf("SELECT * FROM u124132715_DB.%s ", $DATA['tabla']);  
		//echo $q . "\n";
		
		$result = mysqli_query($db_login, $q);$ERR = mysqli_error($db_login);
		
		if($ERR != ''){ $RESP['ERR'] = $ERR; echo json_encode($RESP); $db_login -> close(); die;	}
		
		$HTML_OUT = '';
		
		while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
			
			$PROP_STR = $row['Datos'];
			
			//echo $PROP_STR . "\n";
			
			$PROP_OBJ = json_decode($PROP_STR, true);		
			$PROP_PRICE = NUMBER_2_MONEY($PROP_OBJ['TXT_PROP_PRICE']);
			$PROP_ID = $row['id']; 

			include('HTML_PROP_CARD.php');
			
			
			$HTML_OUT = $HTML_OUT . $TEMPLATE;
			
			//echo $HTML_OUT . "\n";
		}
		
		//$INSERTED_ID = mysqli_insert_id($db_login);
		
		//if ($_SESSION['nivel'] == 1){}
		
		//$RESP['INSERTED_ID'] = $INSERTED_ID;
		$RESP['DATOS'] = $HTML_OUT;
		
		
		
		echo json_encode($RESP);
		
		//echo $HTML_OUT;
		
		$db_login -> close(); die;
	}
	
	if( $DATA['R'] == 2.1 ){
		include('HTML_PROFILE.php');


		$HTML_OUT = $HTML_OUT . $TEMPLATE;

		$RESP['DATOS'] = $HTML_OUT;


		echo json_encode($RESP);
	}
	
	
	if( $DATA['R'] == 3 ){
	
		
		
		$q = sprintf("SELECT * FROM u124132715_DB.%s WHERE id = %s", $DATA['tabla'], $DATA['prop_id']); 
		//echo $q . "\n";
		
		$result = mysqli_query($db_login, $q);$ERR = mysqli_error($db_login);
		
		if($ERR != ''){ $RESP['ERR'] = $ERR; echo json_encode($RESP); $db_login -> close(); die;	}
		
		$HTML_OUT = '';
		$PROP_ID = '';
		$PROP_LAT = '';
		$PROP_LON = '';
		
		
		while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
			
			$PROP_STR = $row['Datos'];
			
			//echo $PROP_STR . "\n";
			
			$PROP_OBJ = json_decode($PROP_STR, true);
			$PROP_PRICE = NUMBER_2_MONEY($PROP_OBJ['TXT_PROP_PRICE']);
			$PROP_ID = $row['id']; 
			$GPS_ARR = str_getcsv($PROP_OBJ['TXT_PROP_GPS']);
			$PROP_LAT = trim($GPS_ARR[0]) ;
			$PROP_LON = trim($GPS_ARR[1]);


			include('HTML_DETAILS_V3.php');
			//echo $TEMPLATE;
			
			$HTML_OUT = $HTML_OUT . $TEMPLATE;
			
			//echo $HTML_OUT . "\n";
		}
		
		//$INSERTED_ID = mysqli_insert_id($db_login);
		
		//if ($_SESSION['nivel'] == 1){}
		
		//$RESP['INSERTED_ID'] = $INSERTED_ID;
		$RESP['DATOS'] = $HTML_OUT;
		$RESP['PROP_ID'] = $PROP_ID;
		$RESP['PROP_LAT'] = $PROP_LAT;
		$RESP['PROP_LON'] = $PROP_LON;
				
		echo json_encode($RESP);
		
		//echo $HTML_OUT;
		
		$db_login -> close(); die;
	}
	
	if( $DATA['R'] == 4 ){
	
		include('HTML_TOP_MENU.php');
		//echo $TEMPLATE;
		
		$HTML_OUT = $HTML_OUT . $TEMPLATE;
		
		$RESP['DATOS'] = $HTML_OUT;
		
		echo json_encode($RESP);
		
		$db_login -> close(); die;
	}

	if( $DATA['R'] == 5 ){
	
		include('HTML_MODALS.php');
		//echo $TEMPLATE;
		
		$HTML_OUT = $HTML_OUT . $TEMPLATE;
		
		$RESP['DATOS'] = $HTML_OUT;
		
		echo json_encode($RESP);
		
		$db_login -> close(); die; 
	}



// BTN_SEND_CODE GUESTS
	if( $DATA['R'] == 'BTN_SEND_CODE_GUESTS' ){

			$RESP['PROMPT'] = '';
			$RESP['DATOS'] = '';
			$RESP['ERR'] = ''; 
			
			
		//EXTRACT DATA INTO JSON 
			$JSON_DATA = json_decode($DATA['Datos'], true); //print_r($JSON_DATA); 
			
			$_SESSION['USER'] = $JSON_DATA['TXT_REG_USER'];

		// CHECK IF USER EXIST PREVIOUSLY
			$q = sprintf("SELECT * FROM u124132715_DB.%s WHERE JSON_EXTRACT(Datos, \"$.USER\") = '%s' ", $DATA['tabla'], $JSON_DATA['TXT_REG_USER']);  
			//echo $q . "\n";
			$RESP['Q0'] = $q;
			
			$result = mysqli_query($db_login, $q);$ERR = mysqli_error($db_login);
			
			if($ERR != ''){ $RESP['ERR'] = $ERR; echo json_encode($RESP); $db_login -> close(); die;	}
			 
			$rows = mysqli_num_rows($result); //echo $rows . "\n";
			
			//if ( $rows > 0 ) { $RESP['PROMPT'] = 'Existing user, please Log in instead'; echo  json_encode($RESP, true); $db_login -> close(); die; }
			

			$_SESSION['REG_STEP_1'] = false;
			$_SESSION['REG_STEP_2'] = false;


		// GENERATE CODE
		
			$CODE = rand(1000, 9999);
			$JSON_DATA['CODE'] = $CODE;  
			$_SESSION['SMS_CODE'] = $CODE;
			
			
		// SEND CODE
		
			$TO_NUMBER = sprintf('%s%s',$JSON_DATA['TXT_REG_AREA_CODE'],$JSON_DATA['TXT_REG_USER']);
			$TO_NUMBER_NEW = str_replace('+', '', $TO_NUMBER);
			
			//echo $TO_NUMBER_NEW;
			
			//$RESP['SMS_SENT'] = SEND_SMS($TO_NUMBER_NEW, $CODE);
			$RESP['WA_SENT'] = WA_SEND_TXT($TO_NUMBER_NEW, $CODE);
			
		// GET WA USER NAME
			
			$RESP['CHAT_ID'] = $TO_NUMBER_NEW;
			$RESP['WA_USER_NAME'] = WA_GET_NAME($TO_NUMBER_NEW);	
		
		
			
			
			$RESP['SESSION'] = $_SESSION;
		

			
			echo json_encode($RESP); 
			
			$db_login -> close(); die;
	}
	
	
	

// BTN_SEND_CODE
	if( $DATA['R'] == 'BTN_SEND_CODE' ){

			$RESP['PROMPT'] = '';
			$RESP['DATOS'] = '';
			$RESP['ERR'] = ''; 
			
			
		//EXTRACT DATA INTO JSON 
			$JSON_DATA = json_decode($DATA['Datos'], true); //print_r($JSON_DATA); 
			
			$_SESSION['USER'] = $JSON_DATA['TXT_REG_USER'];

		// CHECK IF USER EXIST PREVIOUSLY
			$q = sprintf("SELECT * FROM u124132715_DB.%s WHERE JSON_EXTRACT(Datos, \"$.USER\") = '%s' ", $DATA['tabla'], $JSON_DATA['TXT_REG_USER']);  
			//echo $q . "\n";
			$RESP['Q0'] = $q;
			
			$result = mysqli_query($db_login, $q);$ERR = mysqli_error($db_login);
			
			if($ERR != ''){ $RESP['ERR'] = $ERR; echo json_encode($RESP); $db_login -> close(); die;	}
			 
			$rows = mysqli_num_rows($result); //echo $rows . "\n";
			
			if ( $rows > 0 ) { $RESP['PROMPT'] = 'Existing user, please Log in instead'; echo  json_encode($RESP, true); $db_login -> close(); die; }
			

			$_SESSION['REG_STEP_1'] = false;
			$_SESSION['REG_STEP_2'] = false;


		// GENERATE CODE
		
			$CODE = rand(1000, 9999);
			$JSON_DATA['CODE'] = $CODE;  
			$_SESSION['SMS_CODE'] = $CODE;
			
			
		// SEND CODE
		
			$TO_NUMBER = sprintf('%s%s',$JSON_DATA['TXT_REG_AREA_CODE'],$JSON_DATA['TXT_REG_USER']);
			$TO_NUMBER_NEW = str_replace('+', '', $TO_NUMBER);
			
			//echo $TO_NUMBER_NEW;
			
			//$RESP['SMS_SENT'] = SEND_SMS($TO_NUMBER_NEW, $CODE);
			$RESP['WA_SENT'] = WA_SEND_TXT($TO_NUMBER_NEW, $CODE);
			
		// GET WA USER NAME
			
			$RESP['CHAT_ID'] = $TO_NUMBER_NEW;
			$RESP['WA_USER_NAME'] = WA_GET_NAME($TO_NUMBER_NEW);	
		
		
			
			
			$RESP['SESSION'] = $_SESSION;
		

			
			echo json_encode($RESP); 
			
			$db_login -> close(); die;
	}
	
	
	
//CONFIRM SMS CODE
	if( $DATA['R'] == 7 ){
			
			$RESP['PROMPT'] = '';
			$RESP['DATOS'] = '';
			$RESP['ERR'] = ''; 
			
		// CHECK IF USER EXIST PREVIOUSLY
			
			//EXTRACT DATA INTO JSON 
			$JSON_DATA = json_decode($DATA['Datos'], true); //print_r($JSON_DATA); 
			
			if ( $JSON_DATA['TXT_REG_CODE'] == $_SESSION['SMS_CODE'] ) {$_SESSION['REG_STEP_1'] = true;}else{$_SESSION['REG_STEP_1'] = false;}
			
			$RESP['JSON_DATA'] = $JSON_DATA;
			$RESP['SESSION'] = $_SESSION;
			//$RESP['DATOS'] = $myArr;
			echo json_encode($RESP); 
			
			
			$db_login -> close(); die;
	}
	
// INSERT NEW USER
	if( $DATA['R'] == 'BTN_REGISTER_GUESTS' ){
			

		//EXTRACT DATA INTO JSON 
			$JSON_DATA = json_decode($DATA['Datos'], true); //print_r($JSON_DATA);
			
			
		// CHECK IF USER EXIST PREVIOUSLY
			
			 
			//if ($JSON_DATA['TXT_REG_PWD1'] != $JSON_DATA['TXT_REG_PWD2'] ) {$RESP['PROMPT'] = 'Passwords does not match' ; echo  json_encode($RESP, true); $db_login -> close(); die; } 		 
			//if ($JSON_DATA['TXT_REG_PWD1'] == '' || $JSON_DATA['TXT_REG_PWD2'] == '' ) {$RESP['PROMPT'] = 'Please select a password' ; echo  json_encode($RESP, true); $db_login -> close(); die; } 		 
			
			 
			$RESP['$JSON_DATA'] = $JSON_DATA;
			
		// USER CREDENTIALS
			
			$USER_DATA['USER'] = $JSON_DATA['TXT_REG_USER']; // TELEFONO / USER LOGIN
			$USER_DATA['PWD'] = $JSON_DATA['TXT_REG_PWD1']; 
			$USER_DATA['NAME'] = $JSON_DATA['TXT_REG_NAME']; 
			$USER_DATA['IS_BROKER'] = $JSON_DATA['CHK_REG_BROKER']; 
			$USER_DATA['PHONE'] = $JSON_DATA['TXT_REG_AREA_CODE'] . $JSON_DATA['TXT_REG_USER']; 
			$USER_DATA['HIS_BROKER'] = $_SESSION['HIS_BROKER'];
			$USER_DATA['JOINED'] = GET_TS();
			
			
			$USER_DATA_STR = json_encode($USER_DATA);
		
			$q = sprintf("INSERT INTO u124132715_DB.%s (Datos) VALUES ('%s')", $DATA['tabla'], $USER_DATA_STR); //echo $q . "\n"; 	
			
			$R1 = SQL_2_OBJ($q);
			
			if ($R1['OK']){ $_SESSION['REG_STEP_2'] = true; }
			
			$USER_ID = $R1['INSERTED_ID'];
			
			$RESP['R1'] = $R1;
			
			
			$q = sprintf("UPDATE u124132715_DB.%s SET Datos = JSON_SET(Datos, \"$.%s\", '%s') WHERE id = '%s' ",'ID', $JSON_DATA['tabla'],  $USER_ID , $USER_ID ) ;
			SQL_2_OBJ($q);
		

			
			
			 
			
			$RESP['SESSION'] = $_SESSION;

			echo json_encode($RESP); 
			
			session_destroy();
			
			$db_login -> close(); die;
	}
	
	
// INSERT NEW USER
	if( $DATA['R'] == 'BTN_REGISTER' ){
			

		//EXTRACT DATA INTO JSON 
			$JSON_DATA = json_decode($DATA['Datos'], true); //print_r($JSON_DATA);
			
			
		// CHECK IF USER EXIST PREVIOUSLY
			
			 
			//if ($JSON_DATA['TXT_REG_PWD1'] != $JSON_DATA['TXT_REG_PWD2'] ) {$RESP['PROMPT'] = 'Passwords does not match' ; echo  json_encode($RESP, true); $db_login -> close(); die; } 		 
			//if ($JSON_DATA['TXT_REG_PWD1'] == '' || $JSON_DATA['TXT_REG_PWD2'] == '' ) {$RESP['PROMPT'] = 'Please select a password' ; echo  json_encode($RESP, true); $db_login -> close(); die; } 		 
			
			 
			$RESP['$JSON_DATA'] = $JSON_DATA;
			
		// USER CREDENTIALS
			
			$USER_DATA['USER'] = $JSON_DATA['TXT_REG_USER']; // TELEFONO / USER LOGIN
			$USER_DATA['PWD'] = $JSON_DATA['TXT_REG_PWD1']; 
			$USER_DATA['NAME'] = $JSON_DATA['TXT_REG_NAME']; 
			$USER_DATA['IS_BROKER'] = $JSON_DATA['CHK_REG_BROKER']; 
			$USER_DATA['PHONE'] = $JSON_DATA['TXT_REG_AREA_CODE'] . $JSON_DATA['TXT_REG_USER']; 
			$USER_DATA['HIS_BROKER'] = $_SESSION['HIS_BROKER'];
			$USER_DATA['JOINED'] = GET_TS();
			
			
			$USER_DATA_STR = json_encode($USER_DATA);
		
			$q = sprintf("INSERT INTO u124132715_DB.%s (Datos) VALUES ('%s')", $DATA['tabla'], $USER_DATA_STR); //echo $q . "\n"; 	
			
			$R1 = SQL_2_OBJ($q);
			
			if ($R1['OK']){ $_SESSION['REG_STEP_2'] = true; }
			
			$USER_ID = $R1['INSERTED_ID'];
			
			$RESP['R1'] = $R1;
			
			
			$q = sprintf("UPDATE u124132715_DB.%s SET Datos = JSON_SET(Datos, \"$.%s\", '%s') WHERE id = '%s' ",'ID', $JSON_DATA['tabla'],  $USER_ID , $USER_ID ) ;
			SQL_2_OBJ($q);
		
		// IF IS BROKER CREATE CUSTOM LINK AND ADD TO BROKERS TABLE
		
		
			if ($JSON_DATA['CHK_REG_BROKER'] == true){
				/*
				$BROKER_DATA['USER'] =  $USER_DATA['USER'];
				$BROKER_DATA['NAME'] =  $USER_DATA['NAME'];
				$BROKER_DATA['PHONE'] =  $USER_DATA['PHONE'];				
				
								
				$BROKER_DATA_STR = json_encode($BROKER_DATA);
				
				$q = sprintf("INSERT INTO u124132715_DB.Brokers (Datos) VALUES ('%s')",  $BROKER_DATA_STR); 
					
				$R2 = SQL_2_OBJ($q);
				$RESP['R2'] = $R2;

				$BROKER_ID_NEW = $R2['INSERTED_ID'];
				*/
				
				$custom_link_name = $USER_DATA['PHONE'];
				$after_slash = sprintf('index.php?s_id=%s', $USER_ID );
				
				$CUSTOM_LINK = ADD_CUSTOM_LINK($custom_link_name,$after_slash);	
				
								
				$q = sprintf("UPDATE u124132715_DB.%s SET Datos = JSON_SET(Datos, \"$.%s\", '%s') WHERE id = '%s' ",'CUSTOM_LINK', $DATA['tabla'], $CUSTOM_LINK, $USER_ID ) ;
				$R3 = SQL_2_OBJ($q);
				$RESP['R3'] = $R3;
				
				/*
				$q = sprintf("UPDATE u124132715_DB.Users SET Datos = JSON_SET(Datos, \"$.%s\", '%s') WHERE id = '%s' ",'BROKER_PROFILE_ID', $BROKER_ID_NEW  ,$USER_ID) ;
				$R4 = SQL_2_OBJ($q);
				$RESP['R4'] = $R4;
				*/
			}
		
		
		// REFERED TREATMNET
			
			$JSON_DATA['HIS_BROKER'] = $_SESSION['HIS_BROKER'];
			
			
			$PYRAMID_DATA['USER_ID'] = $USER_ID;
			$PYRAMID_DATA['HIS_BROKER'] = $_SESSION['HIS_BROKER'];
			
			$PYRAMID_DATA_STR = json_encode($PYRAMID_DATA);
			
			$q = sprintf("INSERT INTO u124132715_DB.Pyramid (Datos) VALUES ('%s')",  $PYRAMID_DATA_STR); //echo $q . "\n"; 	
			
			//$R5 = SQL_2_OBJ($q);
			$RESP['R5'] = $R5;
			
			
			 
			
			$RESP['SESSION'] = $_SESSION;

			echo json_encode($RESP); 
			
			session_destroy();
			
			$db_login -> close(); die;
	}
	
// LOGIN
	if( $DATA['R'] == 9 ){
	

			

		// CHECK IF USER EXIST PREVIOUSLY
			
			//EXTRACT DATA INTO JSON 
			$JSON_DATA = json_decode($DATA['Datos'], true); //print_r($JSON_DATA); 

			
			$q = sprintf("SELECT * FROM u124132715_DB.%s WHERE JSON_EXTRACT(Datos, \"$.USER\") = '%s' ", $DATA['tabla'], $JSON_DATA['TXT_LOGIN_USER']);  //echo $q . "\n";
			
			$R1 = SQL_2_OBJ_V2($q);
			$RESP['R1'] = $R1;
			
			//print_r($R1);
			
			if ( $R1['QRY']['ROWS'] == 0 ){ $RESP['PROMPT'] = 'User not found.'; }

			if ( $R1['DATA'][0]['PWD'] == $JSON_DATA['TXT_LOGIN_PWD']) { 
				/*
				$_SESSION['LOGIN'] = true;
				$_SESSION['USER']['ID'] = $R1['DATA']['ID'];
				$_SESSION['USER']['USER'] = $JSON_DATA['TXT_LOGIN_USER'];
				$_SESSION['USER']['NAME'] = $R1['DATA'][0]['NAME'];
				$_SESSION['USER']['IS_BROKER'] = $R1['DATA'][0]['IS_BROKER'];
				$_SESSION['USER']['HIS_BROKER'] = $R1['DATA'][0]['HIS_BROKER'];
				$_SESSION['USER']['BROKER_PROFILE_ID'] = $R1['DATA'][0]['BROKER_PROFILE_ID'];
				*/
				
				
				
				$_SESSION['USER'] 			= $R1['DATA'][0];

				$_SESSION['LOGIN'] 			= true;
				
				$TS = GET_TS();
				
				$q = sprintf("UPDATE u124132715_DB.%s SET Datos = JSON_SET(Datos, \"$.%s\", '%s') WHERE id = '%s' ",'LAST_LOGIN_TS', $DATA['tabla'], $TS, $R1['DATA'][0]['ID']) ;
				SQL_2_OBJ($q);

				
				//header("Location: property-halfmap-grid.php");	
				//return;
			
			} else {
				$_SESSION['LOGIN'] = false; 
				$RESP['PROMPT'] = 'Incorrect user / password.';
			}
			
			//$RESP['SESSION'] = $_SESSION;
			
			echo json_encode($RESP); 
			
			return;
			
	}	
	
// COMENSAL LOGIN
	if( $DATA['R'] == 'BTN_LOGIN_GUESTS' ){
	

		// CHECK IF USER EXIST PREVIOUSLY
			
			//EXTRACT DATA INTO JSON 
			$JSON_DATA = json_decode($DATA['Datos'], true); //print_r($JSON_DATA); 

			
			$q = sprintf("SELECT * FROM u124132715_DB.GUESTS WHERE JSON_EXTRACT(Datos, \"$.USER\") = '%s' ",  $JSON_DATA['TXT_LOGIN_USER']);  //echo $q . "\n";
			
			$R1 = SQL_2_OBJ_V2($q);
			$RESP['R1'] = $R1;
			
			//print_r($R1);
			
			if ( $R1['QRY']['ROWS'] == 0 ){ $RESP['PROMPT'] = 'User not found.';  echo  json_encode($RESP, true); $db_login -> close(); die; }

			//if ( $R1['DATA'][0]['PWD'] == $JSON_DATA['TXT_LOGIN_PWD']) { 
			if ( true ) { 
				/*
				$_SESSION['LOGIN'] = true;
				$_SESSION['USER']['ID'] = $R1['DATA']['ID'];
				$_SESSION['USER']['USER'] = $JSON_DATA['TXT_LOGIN_USER'];
				$_SESSION['USER']['NAME'] = $R1['DATA'][0]['NAME'];
				$_SESSION['USER']['IS_BROKER'] = $R1['DATA'][0]['IS_BROKER'];
				$_SESSION['USER']['HIS_BROKER'] = $R1['DATA'][0]['HIS_BROKER'];
				$_SESSION['USER']['BROKER_PROFILE_ID'] = $R1['DATA'][0]['BROKER_PROFILE_ID'];
				*/
				
				
				
				$_SESSION['USER'] 			= $R1['DATA'][0];

				$_SESSION['LOGIN'] 			= true;
				
				$TS = GET_TS();
				
				$q = sprintf("UPDATE u124132715_DB.Users SET Datos = JSON_SET(Datos, \"$.%s\", '%s') WHERE id = '%s' ",'LAST_LOGIN_TS', $TS, $R1['DATA'][0]['ID']) ;
				SQL_2_OBJ($q);

				
				//header("Location: property-halfmap-grid.php");	
				//return;
			
			} else {
				$_SESSION['LOGIN'] = false; 
				$RESP['PROMPT'] = 'Incorrect user / password.';
			}
			
			$_SESSION['LOGIN'] = true; 
			$RESP['SESSION'] = $_SESSION;
			
			echo json_encode($RESP); 
			
			return;
			
	}
	
	if( $DATA['R'] == 'BTN_LOGIN_GUESTS_V2' ){
	
		// CHECK IF USER EXIST PREVIOUSLY
			
			//EXTRACT DATA INTO JSON 
			//$JSON_DATA = json_decode($DATA['Datos'], true); //print_r($JSON_DATA); 

			
			$q = sprintf("SELECT * FROM u124132715_DB.GUESTS WHERE JSON_EXTRACT(Datos, \"$.USER\") = '%s' AND JSON_EXTRACT(Datos, \"$.E_ID\") = '%s'  ",  $DATA['email'],$_SESSION['E_ID'] );  //echo $q . "\n";
			$R1 = SQL_2_OBJ_V2($q);
			$RESP['R1'] = $R1;
			
			//print_r($R1);
			
			
			
			if ( $R1['QRY']['ROWS'] == 0 ){ 
			
				// NO EXISTE USUARIO
					$NUEVO_USUARIO_OBJ['USER']	= $DATA['email'];
					$NUEVO_USUARIO_OBJ['E_ID']	= $_SESSION['E_ID'] ;
					$NUEVO_USUARIO_OBJ['PWD']	= '123';
					
					$NUEVO_USUARIO_JSON = json_encode($NUEVO_USUARIO_OBJ);
					
					$q = sprintf("INSERT INTO u124132715_DB.GUESTS (Datos) VALUES ('%s') ",  $NUEVO_USUARIO_JSON);  //echo $q . "\n";
					$R1 = SQL_2_OBJ_V2($q);
					$RESP['R1'] 			= $R1;
					$RESP['USUARIO_NUEVO'] 	= true; 
					
				
			}else{

				// EXISTE USUARIO
				
					
				
					// ACTUALIZA TIMESTAMP
				
						$_SESSION['USER'] 	= $R1['DATA'][0];
						
						$TS = GET_TS();
						
						$q = sprintf("UPDATE u124132715_DB.GUESTS SET Datos = JSON_SET(Datos, \"$.%s\", '%s') WHERE id = '%s' ",'LAST_LOGIN_TS', $TS, $R1['DATA'][0]['ID']) ;
						SQL_2_OBJ($q);
						
						$RESP['USUARIO_NUEVO'] = false; 

					
			}	
			
			$_SESSION['LOGIN'] 		= true; 
			$_SESSION['USER'] 		= $DATA['email'];
			
			$RESP['TXT_LOGIN_USER'] = $DATA['email'];
			$RESP['SESSION'] 		= $_SESSION;
			
			echo json_encode($RESP); 
			
			return;
			
	}
	
	if( $DATA['R'] == 'DELETE_PREVIOUS' ){
	
	
		$TO_SAVE['USER_ID'] 	= $_SESSION['USER']['ID'];
		//$TO_SAVE['USER'] 		= $_SESSION['USER']['USER'];
		//$TO_SAVE['PHONE'] 		= $_SESSION['USER']['PHONE'];
		$TO_SAVE['E_ID'] 		= $_SESSION['E_ID'];
	
		$q = sprintf("DELETE FROM u124132715_DB.SELECTIONS WHERE JSON_EXTRACT(Datos, '$.USER') = '%s' AND JSON_EXTRACT(Datos, '$.E_ID') = '%s';", $_SESSION['USER'], $_SESSION['E_ID'], $TO_SAVE_JSON); 
		$R1 = SQL_2_OBJ_V2($q);
		$Rs[] = $R1;
	
		$RESP['DATOS'] 		= $Rs;
		$RESP['PROMPT'] 	= '';
		$RESP['SUCCESS'] 	= true;
		
		echo json_encode($RESP); 
		
		return;
	
	}
	
	if( $DATA['R'] == 'BTN_SEND_SELECTION' ){
	
		$JSON_IN = json_decode($DATA['Datos'], true); //print_r($JSON_DATA); 
		
		$RESP['JSON_DATA_IN'] = $JSON_IN;
		
		unset($JSON_IN['OPTIONS']);
		
		//echo json_encode($RESP); 
		//$TO_SAVE['USER_ID'] 	= $_SESSION['USER']['ID'];
		$TO_SAVE['USER'] 		= $_SESSION['USER'];
		//$TO_SAVE['PHONE'] 		= $_SESSION['USER']['PHONE'];
		$TO_SAVE['SELECTED'] 	= $JSON_IN;
		$TO_SAVE['E_ID'] 		= $_SESSION['E_ID'];
		$TO_SAVE['NAME'] 		= $DATA['NOMBRE'];
		
		$TO_SAVE_JSON = json_encode($TO_SAVE, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);



		$q = sprintf("INSERT INTO u124132715_DB.SELECTIONS (Datos) VALUES ('%s')",  $TO_SAVE_JSON); 
		$R1 = SQL_2_OBJ_V2($q);
		$Rs[] = $R1;
		

		$RESP['DATOS'] 		= $Rs;
		$RESP['PROMPT'] 	= 'Seleccion guardada.';
		$RESP['SUCCESS'] 	= true;
		
		echo json_encode($RESP, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); 
		
		//$_SESSION['LOGIN'] = false;
		
		return;
		
	}
		
	if( $DATA['R'] == 'BTN_SAVE' ){
	
		$JSON_IN = json_decode($DATA['Datos'], true); //print_r($JSON_DATA); 
		
		$RESP['JSON_DATA_IN'] = $JSON_IN;
		
		//echo json_encode($RESP); 

		$q = sprintf("INSERT INTO u124132715_DB.%s (Datos) VALUES ('%s')",$DATA['tabla'],  $DATA['Datos']); 
		$R1 = SQL_2_OBJ_V2($q);
		$Rs[] = $R1;
		

		$RESP['DATOS'] = $Rs;
		
		echo json_encode($RESP); 
		return;
		
	}
	
		
	if( $DATA['R'] == 'JSON_SAVE' ){
	
		//$DATA['Datos'] = htmlspecialchars($DATA['Datos'], ENT_QUOTES | ENT_HTML5, 'UTF-8');
	
		$JSON_IN = json_decode($DATA['Datos'], true); //print_r($JSON_DATA); 
		
		$RESP['JSON_DATA_IN'] = $JSON_IN;
		
		//echo json_encode($RESP); 

		$q = sprintf("INSERT INTO u124132715_DB.%s (Datos) VALUES ('%s')",$DATA['tabla'],  $DATA['Datos']); 
		$R1 = SQL_2_OBJ_V2($q);
		$Rs[] = $R1;
		

		//$RESP['DATOS'] = $Rs;
		$RESP['DATOS'] = $R1;
		
		echo json_encode($RESP); 
		return;
		
	}
			
			
			
			
	if( $DATA['R'] == 'JSON_LOAD' ){
	
		$q = sprintf("SELECT * FROM u124132715_DB.%s WHERE id = '%s' ", $DATA['tabla'], $DATA['id'] ) ;
			$R1 = SQL_2_OBJ_V2($q);
			$RESP['R1'] = $R1; //print_r($R1);
		
		$RESP['DATOS'] = $R1['DATA'];
		
		
		echo json_encode($RESP, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
		return;
	}
				


	if( $DATA['R'] == 'JSON_UPDATE' ){
	
		//$DATA['Datos'] = htmlspecialchars($DATA['Datos'], ENT_QUOTES | ENT_HTML5, 'UTF-8');
	
		$q = sprintf("UPDATE u124132715_DB.%s SET Datos = '%s' WHERE id = '%s' ", $DATA['tabla'], $DATA['Datos'],  $DATA['id'] ) ; //echo $q;
			$R1 = SQL_2_OBJ_V2($q);
			$RESP['R1'] = $R1; //print_r($R1);
		
		$RESP['DATOS'] = $R1;
		
		
		echo json_encode($RESP, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
		return;
		
	}
			
if ($DATA['R'] == 'JSON_UPDATE_GAP') {
	

	
    // Fetch the current Datos for the given ID
    $currentQuery = sprintf("SELECT * FROM u124132715_DB.%s WHERE id = '%s'", $DATA['tabla'], $DATA['id']);
    $currentData = SQL_2_OBJ_V2($currentQuery);
	$currentData = $currentData['PL'][0];
	
	//echo $currentQuery . "\n";
	//print_r($currentData);//die;


        // Assuming the Datos is in the first row and is a valid JSON string
        //$currentJson = $currentData->Datos; // Adjust according to the structure returned by SQL_2_OBJ_V2
        //$currentArray = json_decode($currentData, true); // Decode the current JSON
		
		//print_r($currentData);die;

        // Decode the new data to be updated
        $newData = json_decode($DATA['Datos'], true); // Assuming $DATA['Datos'] is a valid JSON string

        // Update the current array with new values where keys match
        foreach ($newData as $key => $value) {
            if (array_key_exists($key, $currentData)) {
                $currentData[$key] = $value; // Update only if the key exists
            }else{
				$currentData[$key] = $value;
			}
        }
		
	//print_r($currentData);die;

        // Encode the updated array back to JSON
        $updatedJson = json_encode($currentData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
		

		
        // Update the database with the new JSON string
        $updateQuery = sprintf("UPDATE u124132715_DB.%s SET Datos = '%s' WHERE id = '%s'", $DATA['tabla'], $updatedJson, $DATA['id']);
        $R1 = SQL_2_OBJ_V2($updateQuery); // Execute the update

        $RESP['R1'] = $R1; // Store the result of the update
        $RESP['DATOS'] = $R1; // Optionally return the updated data
        
        echo json_encode($RESP, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); 
        return;

}

	
	/*
	if( $DATA['R'] == 'BTN_NUEVO_EVENTO' ){
	
		$JSON_IN = json_decode($DATA['Datos'], true); //print_r($JSON_DATA); 
		
		$RESP['JSON_DATA_IN'] = $JSON_IN;
		
		//echo json_encode($RESP); 

		$q = sprintf("INSERT INTO u124132715_DB.%s (Datos) VALUES ('{}')",$DATA['tabla']); 
		$R1 = SQL_2_OBJ_V2($q);
		$Rs[] = $R1;
		

		$RESP['DATOS'] = $Rs;
		
		echo json_encode($RESP); 
		return;
		
	}
	*/
	
	if( $DATA['R'] == 'BTN_SAVE_TO_ID' ){
		
		
		if ( $DATA['id'] == '' || !isset($DATA['id'])){
			$NEW_ID = GET_AN_ID('Events');
			$IS_NEW = true;
		}else {
			$NEW_ID = $DATA['id'];
			$IS_NEW = false;
		}
	
		$JSON_IN = json_decode($DATA['Datos'], true); //print_r($JSON_DATA); 
		
		
		$RESP['JSON_DATA_IN'] = $JSON_IN;
		
		$JSON_OUT = $JSON_IN;
		
		//echo json_encode($RESP); return;
	
	// SELECCIONA SI HAY ALGO ANTETIORMENTE CON ESE ID
		if (!$IS_NEW){
			$q = sprintf("SELECT * FROM u124132715_DB.Events WHERE id = '%s'", $DATA['id']);
			$R1 = SQL_2_OBJ_V2($q);
			$EVENTO_ORIGINIAL = $R1['PL'][0];
		}
			
	// ACTUALIZA INFO DEL EVENTO
	
		//$q = sprintf("INSERT INTO u124132715_DB.Events (Datos) VALUES ('%s')",  $DATA['Datos']); 
		$q = sprintf("UPDATE u124132715_DB.Events SET Datos = '%s' WHERE id = '%s'", $DATA['Datos'], $NEW_ID);
		$R1 = SQL_2_OBJ_V2($q);
		$Rs[] = $R1;
		
		//$NEW_ID = $R1['QRY']['INSERTED_ID'];
		
	// ACTUALIZA CON LINK
	
		if ($IS_NEW){
			$after_slash = sprintf('IO/index.php?e_id=%s', $NEW_ID );
			
			$EVENT_LINK = ADD_CUSTOM_LINK($NEW_ID,$after_slash);
			
			$JSON_OUT['EVENT_LINK'] = $EVENT_LINK;
			
			$q = sprintf("UPDATE u124132715_DB.Events SET Datos = JSON_SET(Datos, \"$.EVENT_LINK\", '%s') WHERE id = '%s'",  $EVENT_LINK, $NEW_ID); 
			$R1 = SQL_2_OBJ_V2($q);
		}else{
		
			$EVENT_LINK = $EVENTO_ORIGINIAL['EVENT_LINK'];
			$q = sprintf("UPDATE u124132715_DB.Events SET Datos = JSON_SET(Datos, \"$.EVENT_LINK\", '%s') WHERE id = '%s'",  $EVENT_LINK, $NEW_ID); 
			$R1 = SQL_2_OBJ_V2($q);
		
		}
	
	// LEE DATOS NUEVAMENTE 
		$q = sprintf("SELECT * FROM u124132715_DB.Events WHERE id = '%s'", $NEW_ID); 
		$R1 = SQL_2_OBJ_V2($q);
	
		$RESP['DATOS'] = $R1;
		//echo $q;
		//return;
		echo json_encode($RESP); 
		return;
		
	}
	
	if( $DATA['R'] == 11 ){
	
		$JSON_IN = json_decode($DATA['Datos'], true); //print_r($JSON_DATA); 
		
		$RESP['JSON_DATA_IN'] = $JSON_IN;
		
		//echo json_encode($RESP); 

		$q = sprintf("DELETE FROM u124132715_DB.%s WHERE id = '%s'",$DATA['tabla'],$DATA['ID'] ); 
		$R1 = SQL_2_OBJ_V2($q);
		$Rs[] = $R1;
		

		$RESP['DATOS'] = $Rs;
		$RESP['PROMPT'] = '1';
		
		echo json_encode($RESP); 
		
	}
	/////////////////////////////////////////////////////////////////////
	
	
	if( $DATA['R'] == 'FORGOT_PWD' ){
	
	
	
		$JSON_IN = json_decode($DATA['Datos'], true); //print_r($JSON_DATA); 
		
		$RESP['JSON_DATA_IN'] = $JSON_IN;
		
		//echo json_encode($RESP); 

		
		
		$q = sprintf("SELECT * FROM u124132715_DB.Users WHERE JSON_EXTRACT(Datos, \"$.USER\") = '%s' ",  $JSON_IN['TXT_LOGIN_USER']);  //echo $q . "\n";
		$R1 = SQL_2_OBJ_V2($q);
		$Rs[] = $R1;
		
		$RESP['DATOS'] = $Rs;
		
		
		$RESP['WA_SENT'] 	= WA_SEND_TXT($R1['DATA'][0]['PHONE'], 'Your password is: ' . $R1['DATA'][0]['PWD']);
		//$RESP['SMS_SENT'] 	= SEND_SMS($R1['DATA'][0]['PHONE'], 'Your password is: ' . $R1['DATA'][0]['PWD']);
		
		
			
		$RESP['PROMPT'] = 'Password has been sent to your mobile.'; 
		
		
		echo json_encode($RESP); 
		
	}

	//print_r($DATA);


	if( $DATA['R'] == 'DEV_ID_2_JSON' ){ 
	
		$q = sprintf("SELECT * FROM u124132715_DB.%s WHERE id = '%s' ", $DATA['tabla'], $DATA['id'] ) ;
			$R1 = SQL_2_OBJ_V2($q);
			$RESP['R1'] = $R1; //print_r($R1);
		
		$RESP['DATOS'] = $R1['DATA'];
		
		
		echo json_encode($RESP); 
	
	} 



	if( $DATA['R'] == 'GENERA_LISTA' ){ 
	

		//$DATA['EVENT_ID'] = 220;
		
		$q = sprintf("SELECT * FROM u124132715_DB.SELECTIONS WHERE JSON_EXTRACT(Datos,'$.E_ID') = '%s' ", $DATA['EVENT_ID'] ) ;
		$R1 = SQL_2_OBJ_V2($q);
		$RESP['R1'] = $R1; //print_r($R1);
			
		$ARR = [];
		
		foreach( $R1['PL'] as $X ){
			
			$T1 = DISH_ID_2_NAME($X['SELECTED']['T1']);
			$T2 = DISH_ID_2_NAME($X['SELECTED']['T2']);
			$T3 = DISH_ID_2_NAME($X['SELECTED']['T3']);
			$T4 = DISH_ID_2_NAME($X['SELECTED']['T4']);
			
			$ARR[] = ['Usuario'=>$X['USER'], 'Nombre'=>$X['NAME'],'1. Tiempo'=>$T1,'2 Tiempo'=>$T2,'3 Tiempo'=>$T3,'4 Tiempo'=>$T4, 'Alergias'=>$X['SELECTED']['Alergias'] ];
			
		}
		
		
		$FILE_PATH = JSON_2_EXCEL( json_encode($ARR, true), 'Lista_'. $DATA['EVENT_ID'] . '.xlsx');
			
		if ($FILE_PATH == 'ERROR' ) {$RESP['PROMPT'] = 'ERROR'; return;}
		
		$RESP['PATH'] = '../PHP/' . $FILE_PATH;
		
		echo json_encode($RESP, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
		
		return;
	} 


	if( $DATA['R'] == 'BTN_AGREGAR_PERSONA' ){ 
		
		$E_ID 			= $_SESSION['E_ID'];
		$RESP['E_ID'] 	= $_SESSION['E_ID'];
		
		$NOMBRE 		= $DATA['NOMBRE'];
		$INDEX  		= $DATA['INDEX'];
		
		
		$q = sprintf("SELECT * FROM u124132715_DB.SELECTIONS WHERE UPPER(JSON_EXTRACT(Datos,'$.NAME')) REGEXP UPPER('%s') AND JSON_EXTRACT(Datos,'$.E_ID') = '%s'", $NOMBRE, $E_ID);  //echo $q . "\n";
		$R1 = SQL_2_OBJ_V2($q);
		$DUPLICATED_NAMES = $R1['DATA']; 
		
		$PERSONAL_SELECTIONS = $R1['PL']; 
	
		$ALERGIAS_TXT 	= $R1['PL'][0]['SELECTED']['ALERGIAS'];
		
		
		if ( count($DUPLICATED_NAMES) != 0 ) {
			
			
			$RESP['PROMPT'] = 'Nombre ya registrado:' . "<br><br>";
			
			foreach($DUPLICATED_NAMES as $DUPLICADO_NOMBRE){
				$RESP['PROMPT'] = $RESP['PROMPT'] . $DUPLICADO_NOMBRE['NAME']  . "<br>";
			}
			
			echo json_encode($RESP, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
			return;
		}
		
	
		$q = sprintf("SELECT * FROM u124132715_DB.RESTRICTIONS");  //echo $q . "\n";
		$R1 = SQL_2_OBJ_V2($q);
		$RESTRICTIONS_DB = $R1['DATA']; 
	
		
		// GENERA TARJETAS DE OPCIONES DE DISHES
			$CARDS = GENERAR_HTML_DISH_CARDS($E_ID, $INDEX);
		
		
		// GENERA CHK DE RESTRICCIONES
			
			$HTML_CHK_RESTRICTIONS = '';
			
			$QTY_RESTRICTIONS = 0;
			
			foreach($RESTRICTIONS_DB as $RESTRICTION_DB){
			
				//if ( in_array($RESTRICTION_DB['VALUE'],$SELECTION['SELECTED']['RESTRICTIONS'] ) ) { $CHK_SELECTED = 'checked="checked"'; } else { $CHK_SELECTED = ''; }
				
				$ONE_CHK = <<<TEMP
				<li class="list-group-item">
					
					<input class="form-check-input CHK_OPTION" type="checkbox" value="{$RESTRICTION_DB['VALUE']}" aria-label="..." persona="{$INDEX}" {$CHK_SELECTED}>{$RESTRICTION_DB['TEXT']}
				</li>
				TEMP;
				
				$HTML_CHK_RESTRICTIONS = $HTML_CHK_RESTRICTIONS . $ONE_CHK;
					
			}
			
		// MANAGE PREDEFINED OPTIONS	
			
				
			$q = sprintf("SELECT * FROM u124132715_DB.Events WHERE id = '%s'", $E_ID);  //echo $q . "\n";
			$R1 = SQL_2_OBJ_V2($q);
			$EVENTO = $R1['PL'][0]; 
			
			$TXT_SELECTED_T[1] =  DISH_ID_2_NAME($EVENTO['T1_O0']);
			$TXT_SELECTED_T[2] =  DISH_ID_2_NAME($EVENTO['T2_O0']);
			$TXT_SELECTED_T[3] =  DISH_ID_2_NAME($EVENTO['T3_O0']);
			$TXT_SELECTED_T[4] =  DISH_ID_2_NAME($EVENTO['T4_O0']);
			
			
			if ( $EVENTO['T1_O0'] == '' ) { $T_VISIBLE[1] = 'hide'; }
			if ( $EVENTO['T2_O0'] == '' ) { $T_VISIBLE[2] = 'hide'; }
			if ( $EVENTO['T3_O0'] == '' ) { $T_VISIBLE[3] = 'hide'; }
			if ( $EVENTO['T4_O0'] == '' ) { $T_VISIBLE[4] = 'hide'; }
		
		// GENERA TARJETA DE PERSONA 
			include 'GENERAR_PERSONA.php';
			
		
		$RESP['HTML'] = $PERSONA_CARD;
		
		echo json_encode($RESP, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
		
		return;
	
	
	}
	
	
	if( $DATA['R'] == 'BTN_SALIR' ){ 
	
		//echo json_encode(['success' => true]); // Return a JSON response
		
		$_SESSION["LOGIN"] = false;
		session_start();
		session_destroy(); // Destroy the session
		
		$RESP['SALIR'] 	= true;
		$RESP['E_ID'] 	= $_SESSION['E_ID'];
		
		echo json_encode($RESP, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
		
		return;
	}
	
		
	if( $DATA['R'] == 'DELETE_PERSONA' ){ 
	
		$q = sprintf("DELETE FROM u124132715_DB.SELECTIONS WHERE id = '%s'", $DATA['P_ID']);  //echo $q . "\n";
		$R1 = SQL_2_OBJ_V2($q);
		$RESTRICTIONS_DB = $R1['DATA']; 
	
		$RESP['PROMPT'] = 'Selección eliminada.'; 
		
		echo json_encode($RESP, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
		
		return;
	}
	
	
	
	
?>


