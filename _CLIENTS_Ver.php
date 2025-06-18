<?php $script ='<script>
                        $(".remove-item-btn").on("click", function() {
						//$(this).closest("tr").addClass("d-none")
                        });
						
						//let table = new DataTable("#TBL_DISHES");
						
            </script>';?>



<?php
	
	//error_reporting(E_ALL);
	//ini_set('display_errors', 1);
	
	session_start();
	
	//if ($_SESSION['LOGIN'] != 1){ header("Location: index.php");	}
	
	include_once './PHP/MYF1.php';
	
	//echo GET_TS();die;
	//require_once '/vendor/autoload.php';
	
	
	//echo arrayToHtmlTable($EVENTOS, ['ID','NAME','COMENSALES','FECHA','EVENT_LINK']);
	
	//print_r($EVENTOS);die;
	include_once($_SERVER['DOCUMENT_ROOT'] . '/API/STRIPE/repo.php');
	
	$CLIENTS_STRIPE = listClients();
	$CLIENTS_STRIPE = json_decode($CLIENTS_STRIPE, true);
	//print_r($PRODUCTS_STRIPE);die;
	
	
	// TABLA DE Clientes
	
	
		function TABLE_ROWS_1($json) {
			// Decode the JSON string into an associative array
			//$data = json_decode($json, true);
			$data = $json;

		// Initialize an empty string for the table body
			$tableRow = '';

			// Loop through each product in the invoice_table array
			foreach ($data['data'] as $P) {
				// Create a table row for each product
				
				//$precio_money = '$ '. NUMBER_2_MONEY($P['unit_amount_decimal'] / 100);
				
				$tableRow .= <<<ROW
									<tr>
										<td>{$P['name']}</td>
										<td>{$P['email']}</td>
										<td>{$P['id']}</td>
										<td>{$P['phone']}</td>
										<td>{$P['address']['line1']}</td>
										<td class="text-center">
											<button type="button" class="remove-row" onclick="DELETE_STRIPE('{$P['id']}');">
												<iconify-icon icon="ic:twotone-close" class="text-danger-main text-xl"></iconify-icon>
											</button>
										</td>
									</tr>
								ROW;
							
			}

			// Return the table body
			return $tableRow;
		}
		
		
	
	header('Content-type: html; charset=utf-8');
?>


<?php include './partials/layouts/layoutTop.php' ?>

        <div class="dashboard-main-body">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
                <h6 class="fw-semibold mb-0">Clientes</h6>
                <ul class="d-flex align-items-center gap-2">
                    <li class="fw-medium">
                        <a href="index.php" class="d-flex align-items-center gap-1 hover-text-primary">
                            <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                            Home
                        </a>
                    </li>
                    <li>-</li>
                    <li class="fw-medium">Clientes</li>

                </ul>
            </div>
								
								
								<?php 
									$T_PARAMS 					= [];
									$T_PARAMS['DB']['TABLE'] 	= 'PRODUCTS';
									$T_PARAMS['DB']['COLS'] 	= [ 'name','email','phone','description',  'Action'];
									
									$T_PARAMS['TABLE']['ID'] 	= 'TBL_1';
									$T_PARAMS['TABLE']['COLS'] 	= [ 'Nombre','Email','Teléfono','Descripción', 'Action'];
									$T_PARAMS['TABLE']['href'] 	= '_CLIENTS_edit.php';

									$T_PARAMS['TABLE']['SEARCH_BAR']	 	= true;
									$T_PARAMS['TABLE']['ADD_NEW']['TEXT'] 	= 'Add new';
									$T_PARAMS['TABLE']['ADD_NEW']['href'] 	= '_CLIENTS_edit.php';
									$T_PARAMS['TABLE']['ADD_NEW']['MISC_PROPS'] 	= 'data-bs-toggle="modal" data-bs-target="#Modal_1"';
									

									//echo DB_2_TABLE_V3($T_PARAMS ); 

									include './_ELEMENTS/CLIENTS/FROM_STRIPE/index.php';
									
								?>

			<div class="col-sm-12 d-none">
				<div class="table-responsive scroll-m">
					<table class="table bordered-table-sm text-sm" id="TABLE_STRIPE_1" >
						<thead>
							<tr>

								<th scope="col" class="text-sm">Producto</th>
								<th scope="col" class="text-sm">Descripción</th>
								<th scope="col" class="text-center text-sm">Action</th>
							</tr>
						</thead>
						<tbody>
						<?php echo TABLE_ROWS_1($CLIENTS_STRIPE); ?>
						</tbody>
					</table>
				</div>
			
			</div>
			
											
        </div>



		
<?php include './partials/layouts/layoutBottom.php' ?>