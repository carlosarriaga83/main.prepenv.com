<?php $script ='<script>
                        $(".remove-item-btn").on("click", function() {
						//$(this).closest("tr").addClass("d-none")
                        });
						
						//let table = new DataTable("#TBL_DISHES");
						
            </script>';?>



<?php
	
	session_start();
	
	//if ($_SESSION['LOGIN'] != 1){ header("Location: index.php");	}
	
	include_once './PHP/MYF1.php';
	
	//echo GET_TS();die;
	//require_once '/vendor/autoload.php';
	
	
	//echo arrayToHtmlTable($EVENTOS, ['ID','NAME','COMENSALES','FECHA','EVENT_LINK']);
	
	//print_r($EVENTOS);die;
	include_once($_SERVER['DOCUMENT_ROOT'] . '/API/STRIPE/repo.php');


	
	header('Content-type: html; charset=utf-8');
?>

<?php include './partials/layouts/layoutTop.php' ?>


        <div class="dashboard-main-body">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
                <h6 class="fw-semibold mb-0">Facturas</h6>
                <ul class="d-flex align-items-center gap-2">
                    <li class="fw-medium">
                        <a href="index.php" class="d-flex align-items-center gap-1 hover-text-primary">
                            <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                            Home
                        </a>
                    </li>
                    <li>-</li>
                    <li class="fw-medium">Facturas</li>

                </ul>
            </div>
								
								
								<?php 
									$T_PARAMS 					= [];
									$T_PARAMS['DB']['TABLE'] 	= 'FACTURAS';
									//$T_PARAMS['DB']['COLS'] 	= [ 'DATA', 'Action'];
									$T_PARAMS['DB']['COLS'] 	= [ 'customer_name', 'status','total','due_date','hosted_invoice_url' ,'invoice_pdf', 'Action'];
									
									$T_PARAMS['TABLE']['ID'] 	= 'TBL_1';
									//$T_PARAMS['TABLE']['COLS'] 	= [ 'DATA', 'Action'];
									$T_PARAMS['TABLE']['COLS'] 	= [ 'Nombre','Status','Total','Vence', 'Link' , 'Archivo', 'Action'];
									$T_PARAMS['TABLE']['href'] 	= '_FACTURAS2_DB_new.php';

									$T_PARAMS['TABLE']['SEARCH_BAR']	 	= true;
									$T_PARAMS['TABLE']['ADD_NEW']['TEXT'] 	= 'Add new';
									$T_PARAMS['TABLE']['ADD_NEW']['href'] 	= '_FACTURAS2_DB_new.php';
									$T_PARAMS['TABLE']['ADD_NEW']['MISC_PROPS'] 	= 'data-bs-toggle="modal" data-bs-target="#Modal_1"';
									

									//echo DB_2_TABLE_V3($T_PARAMS ); 

									include './_ELEMENTS/FACTURAS/FROM_STRIPE/index.php';
									
								?>


        </div>



		
<?php include './partials/layouts/layoutBottom.php' ?>