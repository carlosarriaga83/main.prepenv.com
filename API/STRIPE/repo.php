<?php
	
//error_reporting(E_ALL);
//ini_set('display_errors', 1);


include_once($_SERVER['DOCUMENT_ROOT'] . '/PHP/MYF1.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/PHP/config.php');

	$COMPANY['ID'] = '1';
    
	$q = sprintf("SELECT * FROM COMPANY WHERE id = '%s'   ",$COMPANY['ID'] );  // echo $q . "\n";
	$R1 = SQL_2_OBJ_V2($q);
	//print_r($R1);die;
	
	$COMPANY 	= $R1['PL'][0];
    
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

// Product Functions

function createProduct($name, $description) {
    global $stripe; // Use the global Stripe client
    try {
        $product = $stripe->products->create([
            'name' => $name,
            'description' => $description,
        ]);
        return jsonResponse(true, $product);
    } catch (\Stripe\Exception\ApiErrorException $e) {
        return jsonResponse(false, null, $e->getMessage());
    }
}

function editProduct($productId, $name, $description) {
    global $stripe;
    try {
        $product = $stripe->products->update($productId, [
            'name' => $name,
            'description' => $description,
        ]);
        return jsonResponse(true, $product);
    } catch (\Stripe\Exception\ApiErrorException $e) {
        return jsonResponse(false, null, $e->getMessage());
    }
}



function deleteProduct($productId) {
    global $stripe;
    try {
        $product = $stripe->products->retrieve($productId);
        $product->delete();
        return jsonResponse(true, null, 'Product deleted successfully.');
    } catch (\Stripe\Exception\ApiErrorException $e) {
        return jsonResponse(false, null, $e->getMessage());
    }
}

// Client Functions



function editCustomer($email, $name) {
    global $stripe;
    try {
        $customers = $stripe->customers->all(['email' => $email]);
        if (count($customers->data) === 0) {
            return jsonResponse(false, null, 'Customer not found.');
        }
        $customerId = $customers->data[0]->id;
        $customer = $stripe->customers->update($customerId, [
            'name' => $name,
        ]);
        return jsonResponse(true, $customer);
    } catch (\Stripe\Exception\ApiErrorException $e) {
        return jsonResponse(false, null, $e->getMessage());
    }
}

function listClients() {
    global $stripe;
    try {
        $customers = $stripe->customers->all();
        return jsonResponse(true, $customers->data);
    } catch (\Stripe\Exception\ApiErrorException $e) {
        return jsonResponse(false, null, $e->getMessage());
    }
}

function deleteClient($email) {
    global $stripe;
    try {
        $customers = $stripe->customers->all(['email' => $email]);
        if (count($customers->data) === 0) {
            return jsonResponse(false, null, 'Customer not found.');
        }
        $customerId = $customers->data[0]->id;
        $stripe->customers->delete($customerId);
        return jsonResponse(true, null, 'Customer deleted successfully.');
    } catch (\Stripe\Exception\ApiErrorException $e) {
        return jsonResponse(false, null, $e->getMessage());
    }
}

function getClientData($email) {
    global $stripe;
    try {
        $customers = $stripe->customers->all(['email' => $email]);
        if (count($customers->data) === 0) {
            return jsonResponse(false, null, 'Customer not found.');
        }
        return jsonResponse(true, $customers->data[0]);
    } catch (\Stripe\Exception\ApiErrorException $e) {
        return jsonResponse(false, null, $e->getMessage());
    }
}

function getClientById($clientId) {
    global $stripe;
    try {
        // Retrieve the product by ID
        $client = $stripe->customers->retrieve($clientId);

        // Return the product details
        return jsonResponse(true, $client, 'Client retrieved successfully');
        
    } catch (\Stripe\Exception\ApiErrorException $e) {
        return jsonResponse(false, null, $e->getMessage());
    }
}


// Quote Functions

function createQuote($customerId, $lineItems) {
    global $stripe;
    try {
        $quote = $stripe->quotes->create([
            'customer' => $customerId,
            'line_items' => $lineItems,
        ]);
        return jsonResponse(true, $quote);
    } catch (\Stripe\Exception\ApiErrorException $e) {
        return jsonResponse(false, null, $e->getMessage());
    }
}

function getQuoteStatus($quoteId) {
    global $stripe;
    try {
        $quote = $stripe->quotes->retrieve($quoteId);
        return jsonResponse(true, $quote->status);
    } catch (\Stripe\Exception\ApiErrorException $e) {
        return jsonResponse(false, null, $e->getMessage());
    }
}

function editQuote($quoteId, $lineItems) {
    global $stripe;
    try {
        $quote = $stripe->quotes->update($quoteId, [
            'line_items' => $lineItems,
        ]);
        return jsonResponse(true, $quote);
    } catch (\Stripe\Exception\ApiErrorException $e) {
        return jsonResponse(false, null, $e->getMessage());
    }
}

function cancelQuote($quoteId) {
    global $stripe;
    try {
        $quote = $stripe->quotes->cancel($quoteId);
        return jsonResponse(true, $quote);
    } catch (\Stripe\Exception\ApiErrorException $e) {
        return jsonResponse(false, null, $e->getMessage());
    }
}

function listAllQuotes() {
    global $stripe;
    try {
        $quotes = $stripe->quotes->all();
        return jsonResponse(true, $quotes->data);
    } catch (\Stripe\Exception\ApiErrorException $e) {
        return jsonResponse(false, null, $e->getMessage());
    }
}

function acceptQuote($quoteId) {
    global $stripe;
    try {
        $quote = $stripe->quotes->retrieve($quoteId);
        $invoice = $quote->accept();
        return jsonResponse(true, $invoice);
    } catch (\Stripe\Exception\ApiErrorException $e) {
        return jsonResponse(false, null, $e->getMessage());
    }
}

function downloadQuotePDF($quoteId) {
    // Stripe does not provide direct PDF download links.
    return jsonResponse(false, null, 'PDF download is not implemented.');
}

function getQuoteLink($quoteId) {
    global $stripe;
    try {
        $quote = $stripe->quotes->retrieve($quoteId);
        return jsonResponse(true, $quote->hosted_invoice_url);
    } catch (\Stripe\Exception\ApiErrorException $e) {
        return jsonResponse(false, null, $e->getMessage());
    }
}

// Invoice Functions




function invoiceById($invoiceId) {
    global $stripe;
    try {
        $invoice = $stripe->invoices->retrieve($invoiceId);
        return jsonResponse(true, $invoice, 'Invoice retrieved OK');
    } catch (\Stripe\Exception\ApiErrorException $e) {
        return jsonResponse(false, null, $e->getMessage());
    }
}

function editInvoice($invoiceId, $lineItems) {
    global $stripe;
    try {
        $invoice = $stripe->invoices->update($invoiceId, [
            'line_items' => $lineItems,
        ]);
        return jsonResponse(true, $invoice);
    } catch (\Stripe\Exception\ApiErrorException $e) {
        return jsonResponse(false, null, $e->getMessage());
    }
}

function cancelInvoice($invoiceId) {
    global $stripe;
    try {
        $invoice = $stripe->invoices->retrieve($invoiceId);
        $invoice->voidInvoice(); // Voids the invoice
        return jsonResponse(true, null, 'Invoice canceled successfully.');
    } catch (\Stripe\Exception\ApiErrorException $e) {
        return jsonResponse(false, null, $e->getMessage());
    }
}

function listAllInvoices() {
    global $stripe;
    try {
        $invoices = $stripe->invoices->all();
        return jsonResponse(true, $invoices->data);
    } catch (\Stripe\Exception\ApiErrorException $e) {
        return jsonResponse(false, null, $e->getMessage());
    }
}

function downloadInvoicePDF($invoiceId) {
    // Stripe does not provide direct PDF download links.
    return jsonResponse(false, null, 'PDF download is not implemented.');
}

function getInvoiceLink($invoiceId) {
    global $stripe;
    try {
        $invoice = $stripe->invoices->retrieve($invoiceId);
        return jsonResponse(true, $invoice->hosted_invoice_url);
    } catch (\Stripe\Exception\ApiErrorException $e) {
        return jsonResponse(false, null, $e->getMessage());
    }
}



// Sessions 


function createSession($jsonInput) {
    global $stripe;

    // Decode the JSON input
    $data = json_decode($jsonInput, true);

    // Prepare line items from the invoice_table
    $lineItems = [];
    foreach ($data['invoice_table'] as $item) {
        $lineItems[] = [
            'price_data' => [
                'currency' => 'mxn', // Change currency if needed
                'product_data' => [
                    'name' => $item['item_name'],
                ],
                'unit_amount' => intval($item['item_total'] * 100), // Amount in cents
            ],
            'quantity' => intval($item['item_qty']),
        ];
    }

    try {
        // Create a checkout session
        $session = $stripe->checkout->sessions->create([
            'payment_method_types' => ['card'],
            'line_items' => $lineItems,
            'mode' => 'payment',
            'success_url' => 'https://your-success-url.com', // Replace with your success URL
            'cancel_url' => 'https://your-cancel-url.com', // Replace with your cancel URL
        ]);

        // Return the session ID and the payment link
        return jsonResponse(true,$session);
    } catch (\Stripe\Exception\ApiErrorException $e) {
        return jsonResponse(false, null, $e->getMessage());
    }
}


function retrieveAllSessions() {
    global $stripe;

    try {
        // Retrieve all checkout sessions
        $sessions = $stripe->checkout->sessions->all([
            'limit' => 100, // You can specify the number of sessions to retrieve (up to 100)
        ]);

        // Return the sessions
        return jsonResponse(true, $sessions->data);
    } catch (\Stripe\Exception\ApiErrorException $e) {
        return jsonResponse(false, null, $e->getMessage());
    }
}


function retrieveSessionById($sessionId) {
    global $stripe;

    try {
        // Retrieve the checkout session by ID
        $session = $stripe->checkout->sessions->retrieve($sessionId);

        // Return the session details
        return jsonResponse(true, $session);
    } catch (\Stripe\Exception\ApiErrorException $e) {
        return jsonResponse(false, null, $e->getMessage());
    }
}

function sec_to_ts($seconds) {
    // Convert seconds to a DateTime object
    $dateTime = new DateTime("@$seconds");

    // Format the DateTime object and return
    return $dateTime->format('Y-m-d H:i:s');
}


function createOrUpdateProduct($productData) {
    global $stripe;
    try {
        // Convert json string to array
        $productData = json_decode($productData, true);

        // Check for required fields
        if (!isset($productData['name']) ) {
            return jsonResponse(false, null, 'Missing required product data.');
        }

        // Get product name, price, and description
        $productName = $productData['name'];
        //$productPrice = $productData['PRICE'] * 100; // Convert to cents
        $productDescription = $productData['description'];



        // Check if product already exists
        $products = $stripe->products->all();
        $existingProduct = null;
        foreach ($products->autoPagingIterator() as $product) {
            if ($product->name == $productName) {
                $existingProduct = $product;
                break;
            }
        }

        if ($existingProduct) {
            // Update product
            $updatedProduct = $stripe->products->update($existingProduct->id, [
                'name' => $productName,
                'description' => $productDescription, // Include description in update
            ]);

            return jsonResponse(true, $updatedProduct, 'Product updated successfully');
        } else {
            // Create product
            $newProduct = $stripe->products->create([
                'name' => $productName,
                'description' => $productDescription, // Include description in creation
            ]);

            return jsonResponse(true, $newProduct, 'Product created successfully');
        }
    } catch (\Stripe\Exception\ApiErrorException $e) {
        return jsonResponse(false, null, $e->getMessage());
    }
}

function createOrUpdateProductPrice($priceData, $productId, $set_default = false) {
    global $stripe;
    try {
        $priceData = json_decode($priceData, true);

        if (!isset($priceData['PRICE']) || !isset($priceData['CURRENCY']) || !isset($priceData['NICKNAME'])) {
            return jsonResponse(false, null, 'Missing required price data.');
        }

        $priceAmount = $priceData['PRICE'] * 100; // Convert to cents
        $currency = strtoupper($priceData['CURRENCY']); // Ensure currency is uppercase
        $nickname = $priceData['NICKNAME']; // Get the nickname

        if (!is_numeric($priceAmount) || $priceAmount < 0) {
            return jsonResponse(false, null, 'Invalid price amount.');
        }

        // Retrieve all prices for the specified product
        $prices = $stripe->prices->all(['product' => $productId]);
        $existingPrice = null;

        // Check if a price with the same nickname already exists
        foreach ($prices->data as $price) {
            if ($price->nickname === $nickname) {
                $existingPrice = $price;
                return jsonResponse(false, $price, 'Price with this nickname already exists.');
            }
        }

        // If no existing price with the same nickname, create a new price
        $newPrice = $stripe->prices->create([
            'unit_amount' => $priceAmount,
            'currency' => $currency,
            'product' => $productId,
            'nickname' => $nickname, // Include nickname when creating the price
        ]);

        // Set the new price as default if specified
        if ($set_default) {
            $updatedProduct = $stripe->products->update($productId, [
                'default_price' => $newPrice->id,
            ]);
            return jsonResponse(true, $updatedProduct, 'Price created and set as default successfully');
        }

        return jsonResponse(true, $newPrice, 'Price created successfully');
    } catch (\Stripe\Exception\ApiErrorException $e) {
        return jsonResponse(false, null, $e->getMessage());
    }
}

function createOrUpdateCustomer($jsonInput) {
    global $stripe;
    try {
        // Decode the JSON input
        $data = json_decode($jsonInput, true);

        // Check if customer ID is provided
        if (!empty($data['id'])) {
            //return jsonResponse(false, null, "Customer ID is required.");

            // Retrieve the customer by ID
            try {
                $existingCustomer = $stripe->customers->retrieve($data['id']);
            } catch (\Stripe\Exception\InvalidRequestException $e) {
                // If customer does not exist, we can create a new one
                $existingCustomer = null;
            }

            if ($existingCustomer) {
                // Update existing customer data
                $customer = $stripe->customers->update($existingCustomer->id, [
                    'name' => ucwords(strtolower($data['name'])),
                    'phone' => $data['phone'],
                    'description' => $data['description'] ?? '', // Include description if provided
                    'address' => [
                        'line1' => $data['address'],
                        'country' => 'MX',
                    ],
                    'metadata' => [
                        'currency' => 'mxn',
                        'rfc' => $data['rfc'],
                        'language' => 'es',
                    ],
                ]);
                return jsonResponse(true, $customer, "Customer updated successfully");
            }

        }
        
        // If customer doesn't exist, create a new one
        $customer = $stripe->customers->create([
            'email' => $data['email'],
            'name' => ucwords(strtolower($data['name'])),
            'phone' => $data['phone'],
            'description' => $data['description'] ?? '', // Include description if provided
            'address' => [
                'line1' => $data['address'],
                'country' => 'MX',
            ],
            'metadata' => [
                'currency' => 'mxn',
                'rfc' => $data['rfc'],
                'language' => 'es',
            ],
        ]);
        return jsonResponse(true, $customer, "Customer created successfully");
    } catch (\Stripe\Exception\ApiErrorException $e) {
        return jsonResponse(false, null, $e->getMessage());
    }
}

function getProductById($productId) {
    global $stripe;
    try {
        // Retrieve the product by ID
        $product = $stripe->products->retrieve($productId);

        // Return the product details
        return jsonResponse(true, $product, 'Product retrieved successfully');
        
    } catch (\Stripe\Exception\ApiErrorException $e) {
        return jsonResponse(false, null, $e->getMessage());
    }
}

function getPriceById($priceId) {
    global $stripe;
    try {
        // Retrieve the product by ID
        $product = $stripe->prices->retrieve($priceId);

        // Return the product details
        return jsonResponse(true, $product, 'Price retrieved successfully');
        
    } catch (\Stripe\Exception\ApiErrorException $e) {
        return jsonResponse(false, null, $e->getMessage());
    }
}


function productPrices($productId) {
    global $stripe;
    try {

        $prices = $stripe->prices->all(['product' => $productId]);
        $existingPrice = null;


        return jsonResponse(true, $prices, 'Prices listed');
        
    } catch (\Stripe\Exception\ApiErrorException $e) {
        return jsonResponse(false, null, $e->getMessage());
    }
}

function listProducts() {
    global $stripe;
    try {
        $products = $stripe->products->all();
        return jsonResponse(true, $products->data);
    } catch (\Stripe\Exception\ApiErrorException $e) {
        return jsonResponse(false, null, $e->getMessage());
    }
}

function listInvoices() {
    global $stripe;
    try {
        
        $invoices = $stripe->invoices->all(); // Fetch all invoices
        return jsonResponse(true, $invoices->data, 'Invoices read OK'); // Return success response with invoice data
    } catch (\Stripe\Exception\ApiErrorException $e) {
        return jsonResponse(false, null, $e->getMessage()); // Return error response
    }
}

function deletePriceById($priceId) {
    global $stripe;

    try {
        // Delete the price by ID
        $deletedPrice = $stripe->prices->delete($priceId);

        // Return a success response with the deleted price information
        return jsonResponse(true, $deletedPrice, 'Price deleted successfully');
    } catch (\Stripe\Exception\ApiErrorException $e) {
        // Handle any API errors
        return jsonResponse(false, null, $e->getMessage());
    }
}



function deleteInvoiceLinesById($invoiceId) {
    global $stripe;

    try {
        // Fetch the invoice by ID
        $invoice = $stripe->invoices->retrieve($invoiceId);

        // Loop through each line item and delete it
        foreach ($invoice->lines->data as $line) {
            $stripe->invoices->deleteLineItem($invoiceId, $line->id);
        }

        // Return a success response
        return jsonResponse(true, $invoiceId, 'Invoice lines deleted successfully');
    } catch (\Stripe\Exception\ApiErrorException $e) {
        // Handle any API errors
        return jsonResponse(false, null, $e->getMessage());
    }
}


function invoiceItemsFromId($invoiceId) {
    global $stripe;

    try {

        $invoiceItems = $stripe->invoiceItems->all(['invoice' => $invoiceId]);

        return jsonResponse(true, $invoiceItems, 'Items listed successfully');
    } catch (\Stripe\Exception\ApiErrorException $e) {
        // Handle any API errors
        return jsonResponse(false, null, $e->getMessage());
    }
}



function createOrUpdateInvoice($jsonInput) {
    global $stripe;
    
    try {
        // Decode the JSON input
        $data = json_decode($jsonInput, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Invalid JSON input');
        }

        // Validate required fields
        if (empty($data['invoice_date']) || empty($data['invoice_due_date']) || empty($data['stripe_client_id']) || empty($data['invoice_table'])) {
            throw new Exception('Missing required fields in input data');
        }

        // Convert date strings to epoch timestamps
        $invoiceDateEpoch = strtotime($data['invoice_date']);
        $dueDateEpoch = strtotime($data['invoice_due_date']);

        // Check if tax is enabled and get tax rate
        $taxEnabled = isset($data['TAX_ENABLED']) && $data['TAX_ENABLED'] === 'on';
        $taxRate = $taxEnabled ? ($data['invoice_tax_rate'] ?? 16) : 0;
        
		$data['TAX_IN_PRICES'] == 'on' ?  $taxInclusive = true :    $taxInclusive = false ;
					
        // Get or create the tax rate in Stripe
        $taxRateId = null;

        if ($taxEnabled && $taxRate > 0) {
            $taxRates = $stripe->taxRates->all(['limit' => 1, 'active' => true, 'inclusive' => $taxInclusive]);
            if (count($taxRates->data) > 0) {
                $taxRateId = $taxRates->data[0]->id;
            } else {
                $newTaxRate = $stripe->taxRates->create([
                    'display_name' => 'IVA ',
                    'description' => 'Impuesto al Valor Agregado',
                    'jurisdiction' => 'MX',
                    'percentage' => $taxRate,
                    'inclusive' => $taxInclusive,
                ]);
                $taxRateId = $newTaxRate->id;
            }
        }

        // Check if we're updating an existing invoice
        $isUpdate = !empty($data['stripe_invoice_id']);
        
        if ($isUpdate) {
            // Retrieve existing invoice
            $invoice = $stripe->invoices->retrieve($data['stripe_invoice_id'], ['expand' => ['lines']]);
            
            // Only update invoice if it's in a draft state
            if ($invoice->status !== 'draft') {
                return jsonResponse(false, null, 'Cannot update invoice - it is no longer in draft status (current status: '.$invoice->status.')');
            }
            
            // Update invoice metadata and due date
            $invoice = $stripe->invoices->update($data['stripe_invoice_id'], [
                'due_date' => $dueDateEpoch,
                'metadata' => [
                    'EDIT_ID' => $data['EDIT_ID'] ?? '',
                    'invoice_date' => $invoiceDateEpoch,
                    'client_name' => $data['client_name'] ?? '',
                    'tax_rate' => $taxRate,
                ],
            ]);
			
			$invoiceItems = $stripe->invoiceItems->all(['invoice' => $invoice->id]);
            
            // First remove all existing line items
            foreach ($invoiceItems->data as $line) {
                // For invoice lines, we need to delete them through the InvoiceItem service
                //if ($line->type === 'invoiceitem') {
                    //$stripe->invoiceItems->delete($line->id);
					$deleted = $stripe->invoiceItems->delete($line->id,[]);
                //}
            }
        } else {
            // Create new invoice
            $invoice = $stripe->invoices->create([
                'customer' => $data['stripe_client_id'],
                'currency' => 'mxn',
                'auto_advance' => false, 
                'collection_method' => 'send_invoice',
                'due_date' => $dueDateEpoch, 
                'metadata' => [
                    'EDIT_ID' => $data['EDIT_ID'] ?? '',
                    'invoice_date' => $invoiceDateEpoch,
                    'client_name' => $data['client_name'] ?? '',
                    'tax_rate' => $taxRate,
                ],
            ]);
        }

        // Prepare line items from invoice_table
        $lineItems = [];
        foreach ($data['invoice_table'] as $item) {
            if (empty($item['stripe_product_id']) || empty($item['item_price']) || empty($item['item_qty'])) {
                continue; // Skip invalid items
            }
            
			//$data['TAX_IN_PRICES'] == 'on' ?  $taxBehavior = 'inclusive' :    $taxBehavior = 'exclusive' ;
			
            $lineItem = [
                'price_data' => [
                    'currency' => 'mxn',
                    'product' => $item['stripe_product_id'],
                    'unit_amount_decimal' => (int)($item['item_price'] * 100),
                    //'tax_behavior' => $taxBehavior,
                ],
                'quantity' => (int)$item['item_qty'],
                'description' => $item['item_name'] ?? '',
            ];
            
            // Add tax rate if tax is enabled and not already included in price
            //if ($taxRateId && ( $data['TAX_IN_PRICES'] == 'on')) {
            if ($taxRateId ) {
                $lineItem['tax_rates'] = [$taxRateId];
            }else{
			
			}
            
            $lineItems[] = $lineItem;
        }

        if (empty($lineItems)) {
            throw new Exception('No valid line items to add to invoice');
        }

        // Add line items to the invoice
        $invoice = $stripe->invoices->addLines(
            $invoice->id,
            ['lines' => $lineItems]
        );

        return jsonResponse(true, $invoice, $isUpdate ? 'Invoice updated successfully' : 'Invoice created successfully');

    } catch (\Stripe\Exception\ApiErrorException $e) {
        $errorDetails = [
            'message' => $e->getMessage(),
            'stripe_error' => $e->getStripeCode(),
            'http_status' => $e->getHttpStatus(),
            'edit_id' => $data['EDIT_ID'] ?? null,
        ];
        return jsonResponse(false, $errorDetails, $e->getMessage());
    } catch (Exception $e) {
        $errorDetails = [
            'message' => $e->getMessage(),
            'edit_id' => $data['EDIT_ID'] ?? null,
        ];
        return jsonResponse(false, $errorDetails, $e->getMessage());
    }
}

/*
// Retrieve sessions
$response = retrieveAllSessions();
$response = json_decode($response);
//print_r($response);die;
// Check if the response is successful
if ($response->success) {
    $sessions = $response->data; // Assuming jsonResponse returns an array with a 'data' key
    foreach ($sessions as $session) {
		$expires_at_ts = sec_to_ts($session->expires_at);
        echo "{$session->id} - {$session->amount_total} - {$session->payment_status} - {$session->status} - {$expires_at_ts} - \n";
    }
} else {
    // Handle the error case
    echo "Error retrieving sessions: " . $response['message'] . "\n";
}


 

$CLIENTES = listClients();
$CLIENTES = json_decode($CLIENTES);

//print_r($CLIENTES);
foreach($CLIENTES->data as $CLIENT){
    //echo $CLIENTES->data[0]->id . ' - ' . $CLIENTES->data[0]->name  . "\n";
    echo $CLIENT->id . ' - ' . $CLIENT->name  . "\n";
}



$PRODUCTS = listProducts();

$PRODUCTS = json_decode($PRODUCTS);

echo $PRODUCTS->data[0]->id . ' - ' . $PRODUCTS->data[0]->name  . "\n";
echo $PRODUCTS->data[1]->id . ' - ' . $PRODUCTS->data[1]->name  . "\n";

//print_r( $PRODUCTS->data);


$jsonInput = '{
    "EDIT_ID": "1",
    "ID": "1",
    "TAX_ENABLED": "on",
    "TAX_IN_PRICES": "on",
    "TS": "2025-04-21 15:00:39",
    "client_address": "Por ahi",
    "client_name": "cus_S8QwM7HTgvskpg",
    "client_phone": "27384632874",
    "invoice_date": "2025-04-21",
    "invoice_discount": "0.00",
    "invoice_due_date": "2025-05-06",
    "invoice_subtotal_1": "2520.00",
    "invoice_subtotal_2": "2520.00",
    "invoice_table": [
        {
            "cell_0": "01",
            "item_name": "Producto 3",
            "item_qty": "1",
            "item_units": "Pza",
            "item_price": "1000",
            "item_total": "1000",
            "cell_6": ""
        },
        {
            "cell_0": "02",
            "item_name": "Producto 3",
            "item_qty": "1",
            "item_units": "Pza",
            "item_price": "1000",
            "item_total": "1000",
            "cell_6": ""
        },
        {
            "cell_0": "03",
            "item_name": "Producto 3",
            "item_qty": "1",
            "item_units": "Pza",
            "item_price": "1000",
            "item_total": "1000",
            "cell_6": ""
        }
    ],
    "invoice_tax": "480.00",
    "invoice_tax_rate": "16",
    "invoice_total": "3000.00"
}';

//$result = createSession($jsonInput);
//print_r($result);


die;

*/


/*

### Explanation of the Code

1. **Configuration**: The `config.php` file contains the Stripe API key, which is loaded at the beginning of the `stripe_functions.php` file.

2. **Standard Response**: The `standardResponse` function formats the responses to ensure consistency across all functions.

3. **Product Functions**: 
   - `createProduct`, `editProduct`, `listProducts`, and `deleteProduct` handle product management.

4. **Client Functions**: 
   - `createCustomer`, `editCustomer`, `listClients`, `deleteClient`, and `getClientData` manage customer interactions.

5. **Quote Functions**: 
   - Functions for creating, editing, canceling, and accepting quotes, as well as listing quotes and getting quote links.

6. **Invoice Functions**: 
   - Functions for creating, editing, canceling, and listing invoices, as well as getting invoice links.

7. **Error Handling**: Each function catches exceptions from the Stripe API and returns a standardized error message.

### Important Notes
- Ensure that the Stripe PHP library is installed and that you are using the correct version of PHP.
- Replace `sk_test_YOUR_SECRET_KEY` with your actual Stripe secret key in the `config.php` file.
- The `downloadQuotePDF` and `downloadInvoicePDF` functions are placeholders, as Stripe does not provide direct PDF download capabilities. You may need to implement PDF generation using libraries like TCPDF or FPDF.
- Test the functions in a development environment before using them in production.
*/


?>