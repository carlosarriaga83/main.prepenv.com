<?php

//error_reporting(E_ALL);
//ini_set('display_errors', 1);
    
include_once($_SERVER['DOCUMENT_ROOT'] . '/PHP/MYF1.php');


//setlocale(LC_ALL, 'en_US.UTF-8');
//header('Content-type: text/javascript; charset=utf-8');
	
$entityBody = file_get_contents('php://input');
$BODY_OB = json_decode($entityBody, true);		//$BODY_OB = json_decode($BODY_EN, true);
$DATA = $BODY_OB;   
$DATA_STR = json_encode($DATA , true);





    
/////////////////////////////////////////////////////////////////////////////////////////////////////

function FUNCION($email, $name, $description = null) {
    // Set your Stripe secret key
    include_once($_SERVER['DOCUMENT_ROOT'] . '/PHP/config.php');

    \Stripe\Stripe::setApiKey($stripe_api_key);
    $stripe = new \Stripe\StripeClient($stripe_api_key);

try {
        // Check if a customer with the given email already exists
        $customers = \Stripe\Customer::all(['email' => $email]);

        if (count($customers->data) > 0) {
            // Customer already exists, return the existing customer
            return $customers->data[0]; // Return the first matching customer
        }

        // Create a new customer if none exists
        $customer = \Stripe\Customer::create([ // Ensure you're referencing the Stripe namespace correctly
            'email' => $email,
            'name' => $name,
            'description' => $description,
        ]);

        return $customer; // Return the newly created customer

    } catch (\Stripe\Exception\ApiErrorException $e) {
        // Handle error
        error_log('Error creating or retrieving customer: ' . $e->getMessage()); // Log the error instead of echoing
        return null; // Handle it as per your requirement
    }
}
////////////////////////////////////////////////////////////////////////
    
   
	
	
	//$RESPUESTA = FUNCION('andre-x98@hotmail.com', 'A', 'SYP');
	
	$RESPUESTA = FUNCION($_SESSION['USER']['EMAIL'], $_SESSION['USER']['NAME'], 'SYP');
	
	//print_r($RESPUESTA); die;
	
    //$_SESSION['USER']['ENTITLEMENT'] = $ENTITLEMENTS[0];
	
    return $RESPUESTA;
    
 
    
?>

