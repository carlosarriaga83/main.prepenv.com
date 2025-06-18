<?php
	
    error_reporting(E_ALL);
	ini_set('display_errors', 1);
	
setlocale(LC_ALL, 'en_US.UTF-8');
header('Content-type: text/javascript; charset=utf-8');
//print_r($_SERVER);die;


include_once($_SERVER['DOCUMENT_ROOT'] . '/PHP/MYF1.php');
	
	


if(session_id() == '' || !isset($_SESSION) || session_status() === PHP_SESSION_NONE) {
	// session isn't started
	session_start( [ 'cookie_lifetime' => 604800 ]); 

}
 


//require '../vendor/autoload.php';
//require __DIR__ . '/vendor/autoload.php';

//use PhpOffice\PhpSpreadsheet\IOFactory;
//use Twilio\Rest\Client;


/*

$entityBody = file_get_contents('php://input');
$BODY_OB = json_decode($entityBody, true);		//$BODY_OB = json_decode($BODY_EN, true);

$DATA = $BODY_OB;   
$KEYS = array_keys(json_decode($DATA , true)); 
//print_r($KEYS);
//print_r($DATA);  

$DATA_STR = json_encode($DATA , true);
//print_r($DATA_STR); 
//print_r($DATOS['DATA']);


*/

//print_r($_SERVER);

if($_SERVER["REQUEST_METHOD"] == "POST"){
	
	//echo 'POSTED' . "\n";//die;
	//echo trim($_POST["username"]);
	
	$DATA['EMAIL'] 	= trim($_POST["email"]);
	$DATA['PWD'] 	= trim($_POST["password"]);
	
	
}else {

	header("Location: ../sign-in.php");		

}



//$db_login = mysqli_connect('localhost', 'u124132715_SYP' ,'Salypimienta1!', 'u124132715_SYP'); 

$DB_AND_TABLE = 'u124132715_DB.Users';


// CHECK IF USER EXIST PREVIOUSLY
	
	//EXTRACT DATA INTO JSON 
	//$JSON_DATA = json_decode($DATA['Datos'], true); //print_r($JSON_DATA); 

	$q = sprintf("SELECT * FROM %s WHERE JSON_EXTRACT(Datos, \"$.EMAIL\") = '%s'   ", $DB_AND_TABLE, $DATA['EMAIL'] );  //echo $q . "\n";
	$R1 = SQL_2_OBJ_V2($q);
	$RESP['R1'] = $R1; //print_r($R1);die;

$PROMPT = <<<PROM
			<p>
			<div class="alert alert-danger bg-danger-100 text-danger-600 border-danger-100 px-24 py-11 mb-0 fw-semibold text-lg radius-8" role="alert">
				<div class="d-flex align-items-center justify-content-between text-lg">
					Error!
					<button class="remove-button text-danger-600 text-xxl line-height-1">
						<iconify-icon icon="iconamoon:sign-times-light" class="icon"></iconify-icon>
					</button>
				</div>
				<p class="fw-medium text-danger-600 text-sm mt-8">Usuario y/o contrasena invalidos.</p>
			</div>
			</p>
			PROM;

	//print_r($_SESSION);die;
	
	// NO EXISTE USUARIO
	
	if ( $R1['QRY']['ROWS'] == 0 ){ 
	
			$_SESSION['LOGIN'] 		= false; 
			$RESP['MSG'] 			= 'Usuario y/o contrasena invalidos.';
			$RESP['SUCCESS'] 		= false;
			$_SESSION['SIGN_IN']['PROMPT'] = $PROMPT ;
			$_SESSION['SIGN_IN']['EMAIL'] 	= $DATA['EMAIL'] ;
			$_SESSION['SIGN_IN']['PWD'] 	= $DATA['PWD'] ;
		header("Location: ../sign-in.php");	die;
			echo json_encode($RESP); 
			//return;
	}
	
	// NO EXISTE USUARIO
	
	if ( $R1['PL'][0]['PWD'] !=  $DATA['PWD']){ 
	
			$_SESSION['LOGIN'] 		= false; 
			$RESP['MSG'] 			= 'Usuario y/o contrasena invalidos.';
			$RESP['SUCCESS'] 		= false;
			$_SESSION['SIGN_IN']['PROMPT'] 	= $PROMPT;
			$_SESSION['SIGN_IN']['EMAIL'] 	= $DATA['EMAIL'] ;
			$_SESSION['SIGN_IN']['PWD'] 	= $DATA['PWD'] ;
			//print_r($_SESSION);die;
		header("Location: ../sign-in.php");	die;
			echo json_encode($RESP); 
			//return;
	}
			

	
	/////////////////////////////// CREDENCIALES VALIDAS   /////////////////////
		
	// ACTUALIZA TIMESTAMP

	$TS = GET_MEX_TS();
	
	$q = sprintf("UPDATE %s SET Datos = JSON_SET(Datos, \"$.%s\", '%s') WHERE id = '%s' ",$DB_AND_TABLE,'LAST_LOGIN_TS', $TS, $R1['PL'][0]['ID']);//echo $q;
	$RX = SQL_2_OBJ_V2($q); //print_r($R1);die;
	//$RESP['R1'] = $R1; //print_r($R1);die;
	
	
	/////////////////////////////   VARIABLES GLOBALES    ///////////////////////	
	
	$q = sprintf("SELECT * FROM %s WHERE JSON_EXTRACT(Datos, \"$.EMAIL\") = '%s'   ", $DB_AND_TABLE, $DATA['EMAIL'] );  //echo $q . "\n";
	$R1 = SQL_2_OBJ_V2($q);
	//$RESP['R1'] = $R1; //print_r($R1);//die;
	
	$_SESSION['USER'] 	= $R1['PL'][0];
		
	$_SESSION['LOGIN'] 		= true; 
	
	//$RESP['TXT_LOGIN_USER'] = $DATA['TXT_LOGIN_USER'];
	$RESP['SESSION'] 		= $_SESSION;
	
//echo GET_TS();die;
	//print_r($RESP);die;
	
	$_SESSION['SIGN_IN']['PROMPT'] 	= '';
	
	
	/////////////////////////////////// MEMBERSHIP ////////////////////////////
    
    
			$filePath = $_SERVER['DOCUMENT_ROOT'] . '/API/STRIPE/get_entitlements/index.php';
			

			require_once $filePath;
			
			
					
			//$SUBSCRIPTION_ID 	= $_SESSION['USER']['PERTENENCIAS']['SUBSCRIPTIONS'][0]['subscription_id'];

	//////////////////////////////    LOGIN 
	
	
	header("Location: ../sign-in.php");	die;

	
	//return;
	

	
	
?>