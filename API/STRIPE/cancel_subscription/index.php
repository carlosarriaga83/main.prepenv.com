<?php

//error_reporting(E_ALL);
//ini_set('display_errors', 1);
    
//echo $_SERVER['DOCUMENT_ROOT'] . '/ADMIN/PHP/MYF1.php';
include_once($_SERVER['DOCUMENT_ROOT'] . '/PHP/MYF1.php');

//include_once($_SERVER['DOCUMENT_ROOT'] . '/ADMIN/PHP/MYF1.php');


//setlocale(LC_ALL, 'en_US.UTF-8');
//header('Content-type: text/javascript; charset=utf-8');
	

	
$entityBody = file_get_contents('php://input');
$BODY_OB = json_decode($entityBody, true);		//$BODY_OB = json_decode($BODY_EN, true);

$DATA = $BODY_OB;   



    
/////////////////////////////////////////////////////////////////////////////////////////////////////

function cancelSubscription($subscriptionId, $atPeriodEnd = false) {
	
    include_once($_SERVER['DOCUMENT_ROOT'] . '/PHP/config.php');

    \Stripe\Stripe::setApiKey($stripe_api_key);
    $stripe = new \Stripe\StripeClient($stripe_api_key);

    try {
	
		//$stripe->subscriptions->cancel( $subscriptionId,  ['cancel_at_period_end' => $atPeriodEnd ]	);
        // Cancel the subscription
        $subscription = \Stripe\Subscription::retrieve($subscriptionId);
        
        // Cancel the subscription
        $subscription->cancel(['at_period_end' => $atPeriodEnd]);

        // Return success message
        return [
            'STATUS' => 'success',
            'MESSAGE' => 'Subscription canceled successfully.'
        ];
    } catch (\Stripe\Exception\ApiErrorException $e) {
        // Handle error from Stripe API
        return [
            'STATUS' => 'error',
            'MESSAGE' => $e->getMessage()
        ];
    } 

}
////////////////////////////////////////////////////////////////////////
    

    
    //print_r( cancelSubscription('cear83@gmail.com'));
    
	$RESP['DATOS'] = cancelSubscription($DATA['SUBSCRIPTION_ID']);
    
	echo json_encode($RESP, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
	
	return;

    
?>

