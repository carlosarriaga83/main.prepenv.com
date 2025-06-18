<?php
	
error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once($_SERVER['DOCUMENT_ROOT'] . '/PHP/MYF1.php');


setlocale(LC_ALL, 'en_US.UTF-8');
header('Content-type: text/javascript; charset=utf-8');
	



	$entityBody = file_get_contents('php://input');
	$BODY_OB = json_decode($entityBody, true);		//$BODY_OB = json_decode($BODY_EN, true);
	
	$DATA = $BODY_OB;   

	//print_r($DATA);die;
	


	$COMPANY['ID'] = '1';
    
	$q = sprintf("SELECT * FROM COMPANY WHERE id = '%s'   ",$COMPANY['ID'] );  // echo $q . "\n";
	$R1 = SQL_2_OBJ_V2($q);
	//print_r($R1);die;
	
	$COMPANY 	= $R1['PL'][0];
    
    $stripe_api_key = $COMPANY['STRIPE_TEST_API_KEY'];
    $stripe = new \Stripe\StripeClient($stripe_api_key);




function jsonResponse($success, $data = null, $message = '') {
    return json_encode([
        'success' => $success,
        'data' => $data,
        'message' => $message,
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
}

// Product Functions

function deleteStripeId($Id) {
    global $stripe; // Use the global Stripe client
    try {
        $prefix = substr($Id, 0, 4); // Get the prefix of the ID
        switch ($prefix) {
            case 'cus_':
                $item = $stripe->customers->delete($Id, []);
                $message = 'Customer deleted successfully.';
                break;
            case 'prod':
                $item = $stripe->products->update($Id, [
                    'active' => false,
                ]);
                $message = 'Product deleted successfully.';
                break;
            case 'in_':
                // Retrieve the invoice to check its status
                $invoice = $stripe->invoices->retrieve($Id);
                if ($invoice->status === 'draft') {
                    // Delete the invoice if it is in draft status
                    $item = $stripe->invoices->delete($Id, []);
                    $message = 'Invoice deleted successfully.';
                } elseif ($invoice->status === 'open') {
                    // Void the invoice if it is open
                    $item = $stripe->invoices->voidInvoice($Id,[]);
                    $message = 'Invoice voided successfully.';
                } else {
                    return jsonResponse(false, null, 'Invoice cannot be deleted or voided. Current status: ' . $invoice->status);
                }
                break;
            case 'pric':
                $item = $stripe->prices->update($Id, [
                    'active' => false,
                ]);
                $message = 'Price deactivated successfully.';
                break;
            case 'qt_':
                $item = $stripe->quotes->delete($Id, []);
                $message = 'Quote deleted successfully.';
                break;
            // Add more cases as needed...
            default:
                return jsonResponse(false, null, 'Invalid ID prefix: ' . $Id);
        }
        return jsonResponse(true, $item, $message);
    } catch (\Stripe\Exception\ApiErrorException $e) {
        return jsonResponse(false, null, $e->getMessage());
    }
}
	


	$item =  deleteStripeId($DATA['id']);
	echo $item;
	//return  $item;
	
	//die;
?>