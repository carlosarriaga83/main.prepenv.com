

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

    //\Stripe\Stripe::setApiKey($stripe_api_key); // Set the API key


function jsonResponse($success, $data = null, $message = '') {
    return json_encode([
        'success' => $success,
        'data' => $data,
        'message' => $message,
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
}

function createInvoice($data) {
    global $stripe;
    try {
        // Convert date strings to epoch timestamps
        $invoiceDateEpoch = strtotime($data['invoice_date']);
        $dueDateEpoch = strtotime($data['invoice_due_date']);

        // Prepare the invoice
        $invoice = $stripe->invoices->create([
            'customer' => $data['client_id'],
            'currency' => 'mxn',
            'auto_advance' => false, 
            'collection_method' => 'send_invoice',
            'due_date' => $dueDateEpoch, 
            'metadata' => [
                'EDIT_ID' => $data['EDIT_ID'],
                'invoice_date' => $invoiceDateEpoch,
            ],
        ]);

        // Prepare line items from invoice_table
        $lineItems = [];
        foreach ($data['invoice_table'] as $item) {
            $lineItems[] = [
                'price_data' => [
                    'currency' => 'mxn',
                    'product' => $item['product_id'], // Use the product_id from the invoice_table
                    'unit_amount_decimal' => (int)($item['item_price'] * 100), // Convert to cents
                    'tax_behavior' => 'inclusive', // Assuming tax is included in price
                ],
                'quantity' => (int)$item['item_qty'], // Use item_qty from the invoice_table
            ];
        }

        // Add line items to the invoice
        $invoice = $stripe->invoices->addLines(
            $invoice['id'],
            [
                'lines' => $lineItems,
            ]
        );

        // Send the invoice
        $invoice = $stripe->invoices->sendInvoice($invoice['id'], []);

        return jsonResponse(true, $invoice, 'Invoice created successfully');
    } catch (\Stripe\Exception\ApiErrorException $e) {
        return jsonResponse(false, null, $e->getMessage());
    }
}


// Sample data to create an invoice
$data = [
    "EDIT_ID" => "1",
    "invoice_date" => "2025-04-20",
    "invoice_due_date" => "2025-05-10",
    "client_id" => "cus_SBtEYujKzAyVyz",
    "product_id" => "prod_SBDiJ14Pf2SWxl",
    //"price_id" => "price_1RGrWqIjMUz53iRBDwUL9L2E", // The price ID for the product
    "amount" => "350000" // The price ID for the product
];


$data = '{"EDIT_ID":"","invoice_date":"2025-04-25","invoice_due_date":"2025-05-10","client_id":"cus_SBtEYujKzAyVyz","client_name":"Carlos Arriaga","client_address":"323 Milestone Cr Aurora  Canada","client_phone":"4167682436","client_email":"cear83@gmail.com","invoice_table":[{"cell_0":"01","item_name":"Tacos","product_id":"prod_SC0HN90f7XrcAz","item_qty":"1","item_units":"Pza","item_price":"100","item_total":"100","cell_7":""},{"cell_0":"02","item_name":"Pescado","product_id":"prod_SBtEV8xsOOJumC","item_qty":"1","item_units":"Pza","item_price":"50","item_total":"50","cell_7":""}],"TAX_IN_PRICES":"on","TAX_ENABLED":"on","invoice_tax_rate":"16","invoice_subtotal_1":"126.00","invoice_discount":"","invoice_subtotal_2":"126.00","invoice_tax":"24.00","invoice_total":"150.00"}';

$data = json_decode($data, true);
//print_r($data);die;

// Call the function to create an invoice and get the invoice link
$response = createInvoice($data);

// Print the response
//echo json_encode($response);

// Output the response
print_r($response);



?>