<?php
	
	//error_reporting(E_ALL);
	//ini_set('display_errors', 1);
	
include_once($_SERVER['DOCUMENT_ROOT'] . '/PHP/MYF1.php');
	
setlocale(LC_ALL, 'en_US.UTF-8');
header('Content-type: text/javascript; charset=utf-8');
	


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
	
	$DATA['NAME'] 		= trim($_POST["username"]);
	$DATA['PWD'] 		= trim($_POST["password"]);
	$DATA['EMAIL'] 		= trim($_POST["email"]);
	$DATA['ROLE_ID'] 	= '0';
	
	
}else {

	header("Location: ../sign-up.php");		

}


//$db_login = mysqli_connect('localhost', 'u124132715_SYP' ,'Salypimienta1!', 'u124132715_SYP'); 

$DB_AND_TABLE = 'u124132715_DB.Users';


// CHECK IF USER EXIST PREVIOUSLY
	
	//EXTRACT DATA INTO JSON 
	//$JSON_DATA = json_decode($DATA['Datos'], true); //print_r($JSON_DATA); 

	$q = sprintf("SELECT * FROM %s WHERE JSON_EXTRACT(Datos, \"$.EMAIL\") = '%s'   ", $DB_AND_TABLE, $DATA['EMAIL'] );  //echo $q . "\n";
	$R1 = SQL_2_OBJ_V2($q);
	$RESP['R1'] = $R1; //print_r($R1);//die;

	
	
	// NO EXISTE USUARIO ->  REGISTRA USUARIO
	
	if ( $R1['QRY']['ROWS'] == 0 ){ 
	
	
	
			$_SESSION['LOGIN'] 		= false; 
			
			$DATA['JOINED'] = GET_MEX_TS();
			
			$q = sprintf("INSERT INTO %s (Datos) VALUES ('%s')", $DB_AND_TABLE, json_encode($DATA, true)); //echo $q . "\n"; die;
			$R1 = SQL_2_OBJ_V2($q); //print_r($R1);die;
			
			$PROMPT = <<<P
			<p>
			<div class="alert alert-success bg-success-100 text-success-600 border-success-100 px-24 py-11 mb-0 fw-semibold text-lg radius-8" role="alert">
				<div class="d-flex align-items-center justify-content-between text-lg">
					Listo!
					<button class="remove-button text-success-600 text-xxl line-height-1">
						<iconify-icon icon="iconamoon:sign-times-light" class="icon"></iconify-icon>
					</button>
				</div>
				<p class="fw-medium text-success-600 text-sm mt-8">Usuario registrado exitosamente.</p>
			</div>
			</p>
			P;
	
			$_SESSION['SIGN_IN']['PROMPT'] = $PROMPT ;
			
			header("Location: ../sign-in.php");	die;
			
	}else{ // USUARIO REGISTRADO PREVIAMENTE
	
			$PROMPT = <<<P
			<p>
			<div class="alert alert-danger bg-danger-100 text-danger-600 border-danger-100 px-24 py-11 mb-0 fw-semibold text-lg radius-8" role="alert">
				<div class="d-flex align-items-center justify-content-between text-lg">
					Error!
					<button class="remove-button text-danger-600 text-xxl line-height-1">
						<iconify-icon icon="iconamoon:sign-times-light" class="icon"></iconify-icon>
					</button>
				</div>
				<p class="fw-medium text-danger-600 text-sm mt-8">Usuario existente.</p>
			</div>
			</p>
			P;
	
	
		$_SESSION['SIGN_UP']['PROMPT'] 	= $PROMPT ;
		$_SESSION['SIGN_UP']['NAME'] 	= $DATA['NAME'] ;
		$_SESSION['SIGN_UP']['EMAIL'] 	= $DATA['EMAIL'] ;
		$_SESSION['SIGN_UP']['PWD'] 	= $DATA['PWD'] ;
		
		header("Location: ../sign-up.php");	die;
	
	}
			
	
	/////////////////////////////// CREDENCIALES VALIDAS   /////////////////////
		
	// ACTUALIZA TIMESTAMP

	$TS = GET_TS();
	
	$q = sprintf("UPDATE %s SET Datos = JSON_SET(Datos, \"$.%s\", '%s') WHERE id = '%s' ",$DB_AND_TABLE,'LAST_LOGIN_TS', $TS, $R1['PL'][0]['ID']);//echo $q;
	//$R1 = SQL_2_OBJ_V2($q); //print_r($R1);die;
	//$RESP['R1'] = $R1; //print_r($R1);die;
	
	
	/////////////////////////////   VARIABLES GLOBALES    ///////////////////////	

	//$_SESSION['USER'] 	= $R1['PL'][0];
		
	//$_SESSION['LOGIN'] 		= true; 
	
	//$RESP['TXT_LOGIN_USER'] = $DATA['TXT_LOGIN_USER'];
	$RESP['SESSION'] 		= $_SESSION;
	

	header("Location: ../sign-un.php");	die;	
	
	echo json_encode($RESP); 
	
	//return;
	

	
	
?>