<?php

//error_reporting(E_ALL);
//ini_set('display_errors', 1);
    
//echo $_SERVER['DOCUMENT_ROOT'] . '/ADMIN/PHP/MYF1.php';
include_once($_SERVER['DOCUMENT_ROOT'] . '/PHP/MYF1.php');

//include_once($_SERVER['DOCUMENT_ROOT'] . '/ADMIN/PHP/MYF1.php');


//setlocale(LC_ALL, 'en_US.UTF-8');
//header('Content-type: text/javascript; charset=utf-8');
	

	
//$entityBody = file_get_contents('php://input');
//$BODY_OB = json_decode($entityBody, true);		//$BODY_OB = json_decode($BODY_EN, true);

//$DATA = $BODY_OB;   



    
/////////////////////////////////////////////////////////////////////////////////////////////////////


function getStripeProduct($productId) {
	
    \Stripe\Stripe::setApiKey('sk_test_ZrX77nHXoVge9uHFlssXjQZ8');
    $stripe = new \Stripe\StripeClient('sk_test_ZrX77nHXoVge9uHFlssXjQZ8');
	
    try {
        $product = \Stripe\Product::retrieve($productId);
        $prices = \Stripe\Price::all(["product" => $productId]);
		
		$product = \Stripe\Product::retrieve($productId);

        $priceDetails = array();

        foreach($prices->data as $price) {
            $priceDetail = array();
            $priceDetail['PRICE'] = $price->unit_amount / 100; // Stripe price is in cents
            $priceDetail['TYPE'] = $price->type;
            $priceDetail['CURRENCY'] = $price->currency;
            $priceDetail['DESCRIPTION'] = $product->description;
            $priceDetail['METADATA'] = $product->metadata;
			
            if($price->recurring) {
                $priceDetail['INTERVAL'] = $price->recurring->interval;
            }
            $priceDetails[] = $priceDetail;
        }

        return $priceDetails;
		
    } catch(\Stripe\Exception\ApiErrorException $e) {
	    return [
            'STATUS' => 'error',
            'MESSAGE' => $e->getMessage()
        ];
    }
	

    //return json_encode($subscriptionsData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);


}
////////////////////////////////////////////////////////////////////////
    


    
    //print_r( getStripeSubscriptions('cear83@gmail.com'));
    
    return getStripeProduct($PRODUCT_ID);
    
 
    
?>

