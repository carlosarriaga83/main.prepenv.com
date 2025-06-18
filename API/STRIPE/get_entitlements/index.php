<?php

//error_reporting(E_ALL);
//ini_set('display_errors', 1);
    
//echo $_SERVER['DOCUMENT_ROOT'] . '/ADMIN/PHP/MYF1.php';
include_once($_SERVER['DOCUMENT_ROOT'] . '/PHP/MYF1.php');

	$COMPANY['ID'] = '1';
    
	$q = sprintf("SELECT * FROM COMPANY WHERE id = '%s'   ",$COMPANY['ID'] );  // echo $q . "\n";
	$R1 = SQL_2_OBJ_V2($q);
	//print_r($R1);die;
	
	$COMPANY 	= $R1['PL'][0];
    
    $stripe_api_key = $COMPANY['STRIPE_TEST_API_KEY'];
    $stripe = new \Stripe\StripeClient($stripe_api_key);



//echo $stripe_api_key; die;

//include_once($_SERVER['DOCUMENT_ROOT'] . '/ADMIN/PHP/MYF1.php');


//setlocale(LC_ALL, 'en_US.UTF-8');
//header('Content-type: text/javascript; charset=utf-8');
	

	
//$entityBody = file_get_contents('php://input');
//$BODY_OB = json_decode($entityBody, true);		//$BODY_OB = json_decode($BODY_EN, true);

//$DATA = $BODY_OB;   



    
/////////////////////////////////////////////////////////////////////////////////////////////////////

function getStripeSubscriptions($email) {

    global $stripe_api_key;
    \Stripe\Stripe::setApiKey($stripe_api_key);
    global $stripe;




$subscriptionsData = [];

    $products = [];
			
    $customers = \Stripe\Customer::all(['email' => $email]);

    if (count($customers->data) == 0) {
        $subscriptionsData[] = [
            'success' => 1,
            'customer_email' => $email,
            'customer_name' => '',
            'customer_id' => '',
            'subscription_status' => 'inactive',
            'subscription_id' => '',
            'subscription_creation_date' => '',
            'last_payment_date' => '',
            'next_payment_date' => '',
            'subscription_price' => '',
            'products' => [],
        ];
        return $subscriptionsData;
    }

    foreach ($customers->data as $customer) {
        // Fetch subscriptions by customer
        $subscriptions = \Stripe\Subscription::all(['customer' => $customer->id]);

        foreach ($subscriptions->data as $subscription) {
            $lastPaymentDate = null;
            $nextPaymentDate = null;

            $invoices = \Stripe\Invoice::all([
                'customer' => $customer->id,
                'subscription' => $subscription->id,
                'limit' => 1
            ]);

            if (count($invoices->data) > 0) {
                $lastPaymentDate = date('Y-m-d', $invoices->data[0]->created);
                $nextPaymentDate = date('Y-m-d', $invoices->data[0]->next_payment_attempt);
            }


			
            foreach ($subscription->items->data as $item) {
                $price = $stripe->prices->retrieve($item->price->id, []);
                $product = $stripe->products->retrieve($price->product, []);

                $products[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'product_description' => $product->description,
                ];
            }

            $subscriptionsData[] = [
                'success' => 1,
                'customer_email' => $email,
                'customer_name' => $customer->name,
                'customer_id' => $customer->id,
                'subscription_id' => $subscription->id,
                'subscription_status' => $subscription->status,
                'subscription_creation_date' => date('Y-m-d', $subscription->created),
                'last_payment_date' => $lastPaymentDate,
                'next_payment_date' => $nextPaymentDate,
                'subscription_price' => $subscription->plan->amount / 100,
                'products' => $products,
            ];
			

        }
    }
	$PERTENENCIAS = [];
    $PERTENENCIAS['SUBSCRIPTIONS']		= $subscriptionsData;
    $PERTENENCIAS['PRODUCTS']			= $products;
    return $PERTENENCIAS;
}

////////////////////////////////////////////////////////////////////////
	
	//$PERTENENCIAS 	= getStripeSubscriptions('cear83@gmail.com');

    
    //print_r($PERTENENCIAS);die;
    
    //print_r($_SESSION);die;
	
	$PERTENENCIAS 	= getStripeSubscriptions($_SESSION['USER']['EMAIL']);
    //print_r($PERTENENCIAS);die;
	
    //$_SESSION['USER']['SUBSCRIPTIONS'] 	= $PERTENENCIAS['SUBSCRIPTIONS'];
    //$_SESSION['USER']['PRODUCTS'] 		= $PERTENENCIAS['PRODUCTS'];
    $_SESSION['USER']['PERTENENCIAS'] 	= $PERTENENCIAS;
	
    return $PERTENENCIAS;
	
	//print_r($ENTITLEMENTS);
	
    //$_SESSION['USER']['ENTITLEMENT'] = $ENTITLEMENTS[0];
	
    //return $ENTITLEMENTS[0];
	
    
 
    
?>

