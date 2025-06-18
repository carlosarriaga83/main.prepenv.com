<?php
	
	
error_reporting(E_ALL);
ini_set('display_errors', 1);


include_once($_SERVER['DOCUMENT_ROOT'] . '/PHP/MYF1.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/PHP/config.php');

//////////////   PARAMETERS

	$entityBody = file_get_contents('php://input');
	$BODY_OB = json_decode($entityBody, true);		//$BODY_OB = json_decode($BODY_EN, true);
	
	$DATA = $BODY_OB; 
    
//////////////   COMPANY     
	$COMPANY['ID'] = '1';
    
	$q = sprintf("SELECT * FROM COMPANY WHERE id = '%s'   ",$COMPANY['ID'] );  // echo $q . "\n";
	$R1 = SQL_2_OBJ_V2($q);
	//print_r($R1);die;
	
	$COMPANY 	= $R1['PL'][0];

//////////////   API KEY 


    $stripe_api_key = $COMPANY['STRIPE_TEST_API_KEY'];
    $stripe = new \Stripe\StripeClient($stripe_api_key);


function jsonResponse($success, $data = null, $message = '') {
    return json_encode([
        'success' => $success,
        'data' => $data,
        'message' => $message,
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
}



function sendInvoice($invoiceId) {
    global $stripe; // Usar el cliente global de Stripe
    try {
        // Enviar la factura utilizando el ID proporcionado
        $invoice = $stripe->invoices->sendInvoice($invoiceId);
        return jsonResponse(true, $invoice, 'Factura enviada con éxito.');
    } catch (\Stripe\Exception\ApiErrorException $e) {
        return jsonResponse(false, null, $e->getMessage());
    }
}

print_r(sendInvoice($DATA['ID']));

?>