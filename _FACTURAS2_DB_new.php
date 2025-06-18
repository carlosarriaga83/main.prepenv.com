


<?php
    
	/*
	
	$data = [
    "EDIT_ID" => "1",
    "invoice_date" => "2025-04-20",
    "invoice_due_date" => "2025-05-10",
    "client_id" => "cus_S8QwM7HTgvskpg",
    "product_id" => "prod_SBDiJ14Pf2SWxl",
    //"price_id" => "price_1RGrWqIjMUz53iRBDwUL9L2E", // The price ID for the product
    "amount" => "350000" // The price ID for the product
    "quantity" => "1" // The price ID for the product
];
*/


	//error_reporting(E_ALL);
	//ini_set('display_errors', 1);

    include_once './PHP/MYF1.php';
    
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        //echo "Name: " . htmlspecialchars($name); // Use htmlspecialchars to prevent XSS
    }else{
		$id = '';
        //$id = $_SESSION['USER']['ID'];
    }
	
	
	

		
	////////////////////////////     DATABASE 	/////////////////////////////
    
	// LOAD COMPANY 
	
		$q = sprintf("SELECT * FROM COMPANY WHERE id = '1'   " );  // echo $q . "\n";
		$R1 = SQL_2_OBJ_V2($q);
		//print_r($R1);die;
		
		$COMPANY 	= $R1['PL'][0];	
	
	// LOAD FACTURAS 
	
		//$q = sprintf("SELECT * FROM FACTURAS WHERE id = '%s'   ", $id );  // echo $q . "\n";
		$q = sprintf("SELECT * FROM FACTURAS WHERE JSON_EXTRACT(Datos, '$.stripe_invoice_id') = '%s'   ", $id );  // echo $q . "\n";die;
		$R1 = SQL_2_OBJ_V2($q);
		//print_r($R1);die;
		
		$FACTURA 	= $R1['PL'][0];
		
		//print_r($FACTURA);die;
	
    /// OPCIONES DE DROPDOWN
    
        $q = sprintf("SELECT * FROM CLIENTS " );  // echo $q . "\n";
        $R1 = SQL_2_OBJ_V2($q);
        //print_r($R1);die;
        
        $CLIENTES 			= $R1['PL'];
		$CLIENTES_NAME_ARR 	= [];
		
		foreach($CLIENTES as $CLIENT){
			$CLIENTES_NAME_ARR[] 	= $CLIENT['name'];
        }
		
        foreach($CLIENTES as $R){
            $CLIENTE_ID_2_NAME[$R['ID']] = $R['name'];
        }
		
		
		$HTML_OUT_1 = '<option value="0"> </option>';
		foreach( $CLIENTES as $CLIENTE){
			$CLIENTE['ID'] ==  $FACTURAS['CLIENTE_ID'] ? $SELECTED = 'selected' : $SELECTED = '';
			//$ROL['ID'] == '777' ? $SA_ONLY = 'sa_only' : $SA_ONLY = '';
			$HTML_OUT_1 .= sprintf( '<option class="%s" value="%s" %s> %s</option>',$SA_ONLY, $CLIENTE['ID'], $SELECTED,  $CLIENTE['name']);
			
		}
		
    
        $q = sprintf("SELECT * FROM PRODUCTS " );  // echo $q . "\n";
        $R1 = SQL_2_OBJ_V2($q);
        //print_r($R1);die;
        
        $PRODUCTOS 	= $R1['PL'];
        
        foreach($PRODUCTOS as $R){
            $PRODUCTO_ID_2_NAME[$R['ID']] = $R['name'];
        }
		
	////////////////////////////   STRIPE    /////////////////////////////
	
		include_once($_SERVER['DOCUMENT_ROOT'] . '/API/STRIPE/repo.php');
		
		$CLIENTS_STRIPE = listClients();
		$CLIENTS_STRIPE = json_decode($CLIENTS_STRIPE, true);
		
		$PRODUCTS_STRIPE = listProducts();
		$PRODUCTS_STRIPE = json_decode($PRODUCTS_STRIPE, true);
		
		//$INVOICE_STRIPE = invoiceById($id);
		//$INVOICE_STRIPE = json_decode($INVOICE_STRIPE, true);
		//print_r($INVOICE_STRIPE);die;
		
		//$FACTURA = $INVOICE_STRIPE['data'];
		
		
		// DROPDOWN DE PRODUCTOS 
		
		
			function GENERA_SELECT($NAME, $CATALOGO, $SELECCIONADO){

					$OPTIONS = '<option value="0"> </option>';
					
					foreach( $CATALOGO as $PRODUCTO){
						$PRODUCTO['ID'] ==  $SELECCIONADO ? $SELECTED = 'selected' : $SELECTED = '';
						//$ROL['ID'] == '777' ? $SA_ONLY = 'sa_only' : $SA_ONLY = '';
						$OPTIONS .= sprintf( '<option class="%s" value="%s" %s> %s</option>',$SA_ONLY, $PRODUCTO['ID'], $SELECTED,  $PRODUCTO['name']);
						
					}
					
					$DEL_BTN .= <<<B
								<div class="col-sm-1 d-flex justify-content-center">
									<div class="d-flex align-items-center gap-10 justify-content-center">
										<button type="button" class="bg-danger-focus text-danger-600 bg-hover-danger-200 fw-medium w-40-px h-40-px d-flex justify-content-center align-items-center rounded-circle remove-item-btn" onclick="">
											<iconify-icon icon="lucide:trash" class="menu-icon"></iconify-icon>
										</button>
									</div>
								</div>
								B;
								
					$SELECT = <<<SEL
					
									<div class="d-flex flex-column gap-24">
										<div class="d-flex align-items-center justify-content-between gap-3" id="Producto">
											<div class="d-flex align-items-center">
												
												<div class="flex-grow-1">
													<h6 class="text-md mb-0">Producto</h6>
													<select class="form-control radius-8 form-select min-w-450-px " id="Producto"  name="{$NAME}" required >
													{$OPTIONS}
													</select>
												</div>
											</div>
											{$DEL_BTN}
										</div>
									</div>
					

								SEL;
								
								
					return $SELECT;

			}
			
			$HTML_OPTIONS = '';
			
			if (!isset($FACTURAS['PRODUCT_ID_1'])){
				$HTML_OPTIONS .= GENERA_SELECT('PRODUCT_ID_1', $PRODUCTOS, $FACTURAS['PRODUCT_ID_1'] );
			}else{
				foreach($FACTURAS as $key => $value){
					if (strpos($key, 'PRODUCT_ID_') !== false){
						$HTML_OPTIONS .= GENERA_SELECT($key, $PRODUCTOS, $FACTURAS[$key] );
					} 
				}
			}
			
	
	// TABLA DE PRODUCTOS 
	
	
	
	
		function PRODUCTS_TABLE($json) {
			// Decode the JSON string into an associative array
			//$data = json_decode($json, true);
			$data = $json;

		// Initialize an empty string for the table body
			$tableRow = '';

			// Loop through each product in the invoice_table array
			foreach ($data['invoice_table'] as $product) {
				// Create a table row for each product


				/*
								  <td><input type="text" class="invoive-form-control product-autofill stripe_product_id" SUB_TBS="stripe_product_id" value="{$product['stripe_product_id']}"></td>
				*/
				$tableRow .= <<<ROW
							  <tr>
								  <td><span SUB_TBS="item" >{$product['cell_0']}</span></td>
								  <td><input type="text" class="invoive-form-control product-autofill" SUB_TBS="item_name" value="{$product['item_name']}"></td>
								  <td class="d-none"><input type="text" class="invoive-form-control product-autofill stripe_product_id" SUB_TBS="stripe_product_id" value="{$product['stripe_product_id']}"></td>
								  <td><input type="number" class="invoive-form-control item_qty" SUB_TBS="item_qty" value="{$product['item_qty']}"></td>
								  <td><input type="text" class="invoive-form-control" SUB_TBS="item_units" value="{$product['item_units']}"></td>
								  <td><input type="number" class="invoive-form-control item_price" SUB_TBS="item_price" value="{$product['item_price']}" step="0.01"></td>  
								  <td><input type="number" class="invoive-form-control item_total" SUB_TBS="item_total" value="{$product['item_total']}" step="0.01"></td>  
								  <td class="text-center">
									  <button type="button" class="remove-row"><iconify-icon icon="ic:twotone-close" class="text-danger-main text-xl"></iconify-icon></button>
								  </td>
							  </tr>
							ROW;
							
			}

			// Return the table body
			return $tableRow;
		}
	
		//echo PRODUCTS_TABLE($FACTURA);die;
	
		////////    EXTRAS   /////////////
		
		
		$BTN_DISABLED = ($FACTURA['stripe_invoice_id'] == '') ? 'disabled' : '';
	
	
	
?>

<?php $script = '<script src="assets/js/invoice.js' . '?r=' . GET_RND(1,1000) . '"></script>';?>

<?php include './partials/layouts/layoutTop.php' ?>

        <div class="dashboard-main-body">

				<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
					<h6 class="fw-semibold mb-0">Invoice</h6>
					<ul class="d-flex align-items-center gap-2">
						<li class="fw-medium">
							<a href="index.php" class="d-flex align-items-center gap-1 hover-text-primary">
								<iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
								Dashboard
							</a>
						</li>
						<li>-</li>
						<li class="fw-medium">Invoice</li>
					</ul>
				</div>

				<div class="card">
					<div class="card-header">
					
					<div class="d-flex flex-wrap align-items-center justify-content-end gap-2">
							<button type="submit" form="FRM_1" class="btn btn-sm btn-success-600 radius-8 d-inline-flex align-items-center gap-1">
								<iconify-icon icon="simple-line-icons:check" class="text-xl"></iconify-icon>
								Save
							</button>

                        <button type="button" class="btn btn-sm btn-primary radius-8 d-inline-flex align-items-center gap-1" onclick="sendInvoice('<?php echo $id; ?>')" <?php echo $BTN_DISABLED; ?> >
                            <iconify-icon icon="pepicons-pencil:paper-plane" class="text-xl"></iconify-icon>
                            Send Invoice
                        </button>
						
                        <button type="button" class="btn btn-sm btn-warning radius-8 d-inline-flex align-items-center gap-1" onclick="downloadInvoice('<?php echo $id; ?>')">
                            <iconify-icon icon="solar:download-linear" class="text-xl"></iconify-icon>
                            Download
                        </button>
						


                        <button type="button" class="btn btn-sm btn-danger radius-8 d-inline-flex align-items-center gap-1" onclick="printInvoice()">
                            <iconify-icon icon="basil:printer-outline" class="text-xl"></iconify-icon>
                            Print
                        </button>
                    </div>
					
 

					</div>
					<div class="card-body py-40">
					<form id="FRM_1" action="#" FRM="FRM_1" DB="FACTURAS">
						<input type="text" class="d-none" TBS="EDIT_ID" value="<?php echo $FACTURA['ID']; ?>" FRM="FRM_1" DB="FACTURAS" readonly>
						<div class="row justify-content-center" id="invoice">
							<div class="col-lg-8">
								<div class="shadow-4 border radius-8">
									<div class="p-20 border-bottom">
										<div class="row justify-content-between g-3">
											<div class="col-sm-4">
												<h3 class="text-xl">Invoice # <?php echo $FACTURA['ID']; ?></h3>
												<input type="text" class="d-none" TBS="stripe_invoice_id" value="<?php echo $id; ?>" FRM="FRM_1" DB="FACTURAS" readonly>

												
												<p class="mb-0 text-sm">Date Due: <span class="editable text-decoration-underline" TBS="invoice_date"><?php echo GET_MEX_DATE(); ?></span> <span class="text-success-main edit-icon">
														<iconify-icon icon="mage:edit"></iconify-icon>
													</span></p>
													
												<p class="mb-0 text-sm">Date Due: <span class="editable text-decoration-underline" TBS="invoice_due_date"><?php echo GET_MEX_DATE(' + 15 days'); ?></span> <span class="text-success-main edit-icon">
														<iconify-icon icon="mage:edit"></iconify-icon>
													</span></p>
											</div>
											<div class="col-sm-4">
												<img src="assets/images/logo.png" alt="image" class="mb-8">
												<p class="mb-1 text-sm"><?php echo $COMPANY['ADDRESS'] ?></p>
												<p class="mb-0 text-sm"><?php echo sprintf('%s',$COMPANY['EMAIL'] ); ?></p>
												<p class="mb-0 text-sm"><?php echo sprintf('%s',$COMPANY['PHONE']); ?></p>
											</div>
										</div>
									</div>

									<div class="py-28 px-20">
										<div class="d-flex">
											<div class="col-12">
												<h6 class="text-md">Issus For:</h6>
												
												
												<ul>
													<li class="d-flex align-items-center gap-1 mb-1 d-none">
														<span class="w-20 text-sm ">Stripe id</span>
														<span class="w-80 text-sm">
															<input type="text" class="invoive-form-control border-bottom-0 stripe_client_id w-100" tbs="stripe_client_id" value="<?php echo $FACTURA['stripe_client_id']; ?>" required>
														</span>
													</li>												
													<li class="d-flex align-items-center gap-1 mb-1 ">
														<span class="w-20 text-sm ">Name</span>
														<span class="w-80 text-sm"><input type="text" class="invoive-form-control  client_name client-autofill w-100" tbs="client_name" value="<?php echo $FACTURA['client_name']; ?>"  required></span>
													</li>
												
													<li class="d-flex align-items-center gap-1 mb-1 ">
														<span class="w-20 text-sm ">Address</span>
														<span class="w-80 text-sm "><input type="text" class="invoive-form-control border-bottom-0 client_address w-100" tbs="client_address" value="<?php echo $FACTURA['client_address']; ?>" readonly></span>
													</li>
												
													<li class="d-flex align-items-center gap-1 mb-1 ">
														
															
														<span class="w-20 text-sm ">Phone</span>
														<span class="w-auto text-sm">
															<input type="text" class="invoive-form-control border-bottom-0 client_phone w-100" tbs="client_phone" value="<?php echo $FACTURA['client_phone']; ?>" readonly>
														</span>
											
													
														<span class="w-20 text-sm ">Email</span>
														<span class="w-auto text-sm">
															<input type="text" class="invoive-form-control border-bottom-0 client_email w-100" tbs="client_email" value="<?php echo $FACTURA['client_email']; ?>" readonly>
														</span>
											
													

												

														

													</li>


												</ul>
												
						
											</div>

										</div>

										<div class="mt-24">
											<div class="table-responsive scroll-m">
												<table class="table bordered-table-sm text-sm" id="invoice-table" TBS="invoice_table">
													<thead>
														<tr>
															<th scope="col" class="text-sm">SL.</th>
															<th scope="col" class="text-sm">Items</th>
															<!-- <th scope="col" class="text-sm">Stripe id</th> -->
															<th scope="col" class="text-sm  d-none">Stripe id</th>
															<th scope="col" class="text-sm">Qty</th>
															<th scope="col" class="text-sm">Units</th>
															<th scope="col" class="text-sm">Unit Price</th>
															<th scope="col" class="text-sm">Price</th>
															<th scope="col" class="text-center text-sm">Action</th>
														</tr>
													</thead>
													<tbody>
													<?php echo PRODUCTS_TABLE($FACTURA); ?>
													</tbody>
												</table>
											</div>
											

											<div>
												<button type="button" id="addRow" class="btn btn-sm btn-primary-600 radius-8 d-inline-flex align-items-center gap-1">
													<iconify-icon icon="simple-line-icons:plus" class="text-xl"></iconify-icon>
													Add New
												</button>
											</div>

													<div id="" class="col-12 mt-2" style="">   
														<div id="" class="d-flex flex-wrap align-items-end justify-content-end pt-3 pe-4" style="">   
															<div class="form-switch switch-primary d-flex align-items-center gap-3 ">
																<input class="form-check-input d-none" type="checkbox" role="switch" id="TAX_IN_PRICES" name="TAX_IN_PRICES" <?php echo ($FACTURA['TAX_IN_PRICES'] == 'on') ? 'checked' : ''; ?>>
																<label class="form-check-label line-height-1 fw-medium text-secondary-light d-none" for="TAX_IN_PRICES">Tax included in the prices</label>
															</div>
														</div>
														<div id="" class="d-flex flex-wrap align-items-end justify-content-end pt-3 pe-4 me-3" style="">   
															<div class="form-switch switch-primary d-flex align-items-center gap-3 ">
																<input class="form-check-input" type="checkbox" role="switch" id="TAX_ENABLED" name="TAX_ENABLED" <?php echo ($FACTURA['TAX_ENABLED'] == 'on') ? 'checked' : ''; ?>>
																<label class="form-check-label line-height-1 fw-medium text-secondary-light" for="TAX_ENABLED">Tax</label>
																<input type="number" class="invoive-form-control invoice_tax_rate"  TBS="invoice_tax_rate" value="<?php echo $FACTURA['invoice_tax_rate']; ?>" step="0.1">
																
																<span class="">%</span>
																<span class="text-success-main edit-icon"><iconify-icon icon="mage:edit"></iconify-icon> </span>
															</div>
														</div>
													</div>
											<div class="d-flex flex-wrap justify-content-between gap-3 mt-24">
												<div>
													<p class="text-sm mb-0"><span class="text-primary-light fw-semibold">Sales By:</span> <?php echo $USER['name']; ?></p>
													<p class="text-sm mb-0">Thanks for your business</p>
												</div>
												
												
												<div>
													<table class="text-sm">
														<tbody>
															<tr>
																<td class="pe-64">Subtotal 1:</td>
																<td class="pe-16">
																	<span class="text-primary-light fw-semibold invoice_subtotal_1"  TBS="invoice_subtotal_1"> <?php echo $FACTURA['invoice_subtotal_1']; ?></span>
																</td>
															</tr>
															<tr>
																<td class="pe-64">Discount:</td>
																<td class="pe-16">
																	<span class="text-primary-light fw-semibold  invoice_discount"  TBS="invoice_discount"> <?php echo $FACTURA['invoice_discount']; ?></span>
																</td>
															</tr>
															<tr>
																<td class="pe-64">Subtotal 2:</td>
																<td class="pe-16">
																	<span class="text-primary-light fw-semibold invoice_subtotal_2"  TBS="invoice_subtotal_2"> <?php echo $FACTURA['invoice_subtotal_2']; ?></span>
																</td>
															</tr>
															
															<tr>
																<td class="pe-64 border-bottom pb-4">Tax <span class="text-primary-light fw-semibold  invoice_tax_rate_label"><?php echo $FACTURA['invoice_tax_rate']; ?></span> % :</td>
																<td class="pe-16 border-bottom pb-4">
																	<span class="text-primary-light fw-semibold  invoice_tax"  TBS="invoice_tax"><?php echo $FACTURA['invoice_tax']; ?></span>
																</td>
															</tr>
															<tr>
																<td class="pe-64 pt-4">
																	<span class="text-primary-light fw-semibold">Total:</span>
																</td>
																<td class="pe-16 pt-4">
																	<span class="text-primary-light fw-semibold  invoice_total" TBS="invoice_total"> <?php echo $FACTURA['invoice_total']; ?></span>
																</td>
															</tr>
														</tbody>
													</table>
												</div>
											</div>
										</div>

										<div class="mt-64">
											<p class="text-center text-secondary-light text-sm fw-semibold">Thank you for your purchase!</p>
										</div>

										<div class="d-flex flex-wrap justify-content-between align-items-end mt-64">
											<div class="text-sm border-top d-inline-block px-12">Signature of Customer</div>
											<div class="text-sm border-top d-inline-block px-12">Signature of Authorized</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						</div>
					</form>
					</div>
        </div>

<?php include './partials/layouts/layoutBottom.php' ?>


<script>	
	$(document).ready(function() {
    
        //hideAlertAfterTime($('.alert'), 5000);
		
		//$("#imagePreview").css("background-image", "url( <?php echo $FACTURAS['FACTURA_PIC']; ?> )");
        
        $(document).on('submit','#FRM_1', function VALIDAR(e) { 
        //$('#formulario').on('submit', function (e) {
          if (e.isDefaultPrevented()) {
            // handle the invalid form...
          } else {
          
            
            // everything looks good!
            e.preventDefault(); //prevent submit
			
            SAVE_CHANGES(this).then(response => {
			
			
				debugger;
				if (typeof(response.stripe.data.id) != 'undefined'){
					$('input[TBS=stripe_invoice_id]').val(response.stripe.data.id);
					window.location.href = window.location.origin + window.location.pathname  + '?id=' + response.stripe.data.id ;
				}
			});
			
          }
        });
        

		$('.ui-datepicker').datepicker({
			dateFormat: 'yy-mm-dd',
			//startDate: '-3d'
		})
		
		
		$.datepicker.setDefaults({ dateFormat: 'yy-mm-dd' });
    });



$(document).on('focus focusout keyup', '.client-autofill', function (index, value) {
    //debugger;

    // check if the parent element has a given class

        // extract the NAME and ADDRESS values from the JSON data
		var json = <?php echo json_encode($CLIENTES);?>;
		var names = json.map(function(item) {
			return {
				value: item.name,
				id: item.ID,
				address: item.address ,  // Use an empty string if ADDRESS is not defined
				phone: item.phone,   // Use an empty string if PHONE is not defined
				email: item.email,   // Use an empty string if PHONE is not defined
				stripe_client_id: item.stripe_client_id   // Use an empty string if PHONE is not defined
			};
		});

		$('.client-autofill').autocomplete({
			source: names,
			minLength: 1,
			select: function(event, ui) {
				// Change the text of the span to the selected item's address
				$('.client_address').val(ui.item.address);
				$('.client_phone').val(ui.item.phone);
				$('.client_email').val(ui.item.email);
				$('.stripe_client_id').val(ui.item.stripe_client_id);
			}
		}).on('focus', function() {
			$(this).autocomplete("search", "");
		});
    
});

$(document).on('focus focusout keyup', '.product-autofill', function (index, value) {
    //debugger;

    // extract the NAME and ADDRESS values from the JSON data
    var json = <?php echo json_encode($PRODUCTOS);?>;
    var names = json.map(function(item) {
        return {
            value: item.name,
            id: item.ID,
            price: item.PRICE,
        };
    });

    var names = json.map(function(item) {
        return {
            label: item.name,
            value: item.name,
            id: item.ID,
            price: item.PRICE,
            stripe_product_id: item.stripe_product_id,
        };
    });

    $(this).autocomplete({
        source: names,
		minLength: 1,
        select: function(event, ui) {
            var $row = $(this).closest('tr');
            $row.find('.item_price').val(ui.item.price);
            $row.find('.stripe_product_id').val(ui.item.stripe_product_id);
            calculateRowTotal($row);
        }
		}).focus(function() {
			$(this).autocomplete("search", "");
		});
});


$(document).on('focus focusout keyup change', '.item_qty, .item_price, .item_total ', function (index, value) {

            var $row = $(this).closest('tr');
			//debugger;
			
            var new_item_total = $row.find('.item_price').val() * $(this).val();
			$row.find('.item_total').val(new_item_total);
			
            calculateRowTotal($row);
			calculateInvoiceTotal();
});



$(document).on('focus focusout keyup change blur', 'input[name=TAX_ENABLED], input[name=TAX_IN_PRICES], span .invoice_tax_rate, .invoice_tax_rate', function (index, value) {

	calculateInvoiceTotal();
});



$(document).on('click', '.edit-icon' , function() {
        var editableElement = $(this).siblings('.editable');
        if(editableElement.text().trim() === '') {
            editableElement.text('__________');
        }
});




function printInvoice() {
	var printContents = document.getElementById("invoice").innerHTML;
	var originalContents = document.body.innerHTML;

	document.body.innerHTML = printContents;

	window.print();

	document.body.innerHTML = originalContents;
}

function sendInvoice(ID){
	
	var OBJ = {};
	

	//OBJ['Datos'] = JSON.stringify(OBJ);
	

	OBJ['ID'] = ID;
	
	//console.log(OBJ);
	
	debugger;
		
	POST_API_v2(OBJ, 'API/STRIPE/invoice/send/', true)
	.then(response => {
			// Handle the successful response here
			console.log("Response from server:", response);
			window.location.href = response.PATH ;
			//return response;
		})
		.catch(error => {
			// Handle the error here
			console.error("Error during API call:", error);
		});
	
}

</script>