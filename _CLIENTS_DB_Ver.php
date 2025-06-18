<?php $script ='<script>

						
						
            </script>';?>



<?php
	
	//error_reporting(E_ALL);
	//ini_set('display_errors', 1);
	
	session_start();
	

	
	include_once($_SERVER['DOCUMENT_ROOT'] . '/PHP/MYF1.php');
	


	
	$q = sprintf("SELECT * FROM u124132715_DB.CLIENTS");  //echo $q . "\n";
	$R1 = SQL_2_OBJ_V2($q);
	$RESP['R1'] = $R1; //print_r($R1);
	$EVENTOS = $R1['DATA'];


	
	header('Content-type: html; charset=utf-8');
?>


<?php include './partials/layouts/layoutTop.php' ?>

        <div class="dashboard-main-body">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
                <h6 class="fw-semibold mb-0">Cliente</h6>
                <ul class="d-flex align-items-center gap-2">
                    <li class="fw-medium">
                        <a href="index.php" class="d-flex align-items-center gap-1 hover-text-primary">
                            <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                            Home
                        </a>
                    </li>
                    <li>-</li>
                    <li class="fw-medium">Cliente</li>

                </ul>
            </div>
								
								
<?php 
	$T_PARAMS 					= [];
	$T_PARAMS['DB']['TABLE'] 	= 'CLIENTS';
	$T_PARAMS['DB']['COLS'] 	= [ 'DATA',  'Action'];
	
	$T_PARAMS['TABLE']['ID'] 	= 'TBL_1';
	$T_PARAMS['TABLE']['COLS'] 	= [ 'DATA',  'Action'];
	$T_PARAMS['TABLE']['href'] 	= '_CLIENTS_edit.php';
	
	$T_PARAMS['TABLE']['SEARCH_BAR']	 	= true;
	$T_PARAMS['TABLE']['ADD_NEW']['TEXT'] 	= 'Agregar cliente';
	$T_PARAMS['TABLE']['ADD_NEW']['href'] 	= '_CLIENTS_edit.php';
	$T_PARAMS['TABLE']['ADD_NEW']['MISC_PROPS'] 	= 'data-bs-toggle="modal" data-bs-target="#Modal_1"';
	
	//$T_PARAMS['TABLE']['HEADERS']['PRIORITY']	= ['NAME', 'Action'];



	include './_ELEMENTS/CLIENTS/LIST/index.php';
	
?>


        </div>


        <!-- Modal Start -->
        <div class="modal fade" id="Modal_1" tabindex="-1" aria-labelledby="Modal_1_LBL" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog modal-dialog-centered">
                <div class="modal-content radius-16 bg-base">
                    <div class="modal-header py-16 px-24 border border-top-0 border-start-0 border-end-0 d-none">
                        <h1 class="modal-title fs-5 " id="Modal_1_LBL">Cliente</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-0">
						<img src="IMAGES/EVENT_RIBON_2.jpg" alt="" class="h-50 w-100 object-fit-cover" style="max-height: 15%;">
                        <form id="FRM_1" action="#" FRM="FRM_1" DB="Events">

							
                            <div class="row">


									<div class="col-lg-12">
										<div class="user-grid-card position-relative border radius-16 overflow-hidden bg-base h-100">
											<div class="pb-24 ms-16 mb-24 me-16  mt-100">
												<div class="text-center border border-top-0 border-start-0 border-end-0">
													
													<h6 class="mb-0 mt-16" ><span name="NAME"></span></h6>
													<span class="text-secondary-light mb-16" name="FECHA"></span>
												</div>
												<div class="mt-24">
													
													<ul>

														
														<div class="text-center border border-bottom-0 border-start-0 border-end-0">
															
															<h6 class="mb-0 mt-16" ><span> Link</span></h6>
															<a class="w-100" href="" name="LINK" >
																<span class="w-70 text-secondary-light fw-medium" name="LINK" >: </span>
															</a>
															
														</div>
														

														

														
														<div class="text-center border border-bottom-0 border-start-0 border-end-0">
															
															<h6 class="mb-0 mt-16" ><span> Selecciones</span></h6>
															<span class="text-secondary-light mb-16" ></span>
															<div id="SELECCIONES">
															</div>
														</div>
														
														<input type="text" class="d-none" name="EDIT_ID" value="<?php echo ''; ?>" FRM="FRM_1" DB="Events" readonly>
													</ul>
												</div>
											</div>
											
											
											<div class="d-flex align-items-center justify-content-center gap-3 mt-24">

												<button type="button" class="btn btn-danger border w-50 border-danger-600 bg-hover-danger-200  text-md px-40 py-11 radius-8 mb-20" data-bs-dismiss="modal">
													Close
												</button>

											</div>
											
										</div>
									</div>
                                
                                
                            </div>


							
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal End -->
		
<?php include  './partials/layouts/layoutBottom.php' ?>


<script>

		$(document).on('change','input[name="EDIT_ID"]', function LOAD(e) { 
			
			//debugger;
			var TABLA   = $(this).attr('DB');
			var FORM    = $(this).attr('FRM');
			var ID      = $(this).val();
			
			OBJ = {};
			
			OBJ['ID'] 		= ID;
			OBJ['TABLA'] 	= TABLA;
			
			var resp = POST_API(OBJ, '/API/get_data/');

			jsonToForm_v2('' + FORM, resp.DATOS.PL[0])
			
			var KITCHEN_LIST = POST_API(OBJ, '/API/get_kitchen_list/');
			
			$('#SELECCIONES').html(KITCHEN_LIST.DATOS);
			
			
		});

		function LOAD_ID(id) { 
		
			clearForm('FRM_1');
			
			$('input[name="EDIT_ID"]').val(id);
			
			$('input[name="EDIT_ID"]').trigger( "change" );
					
		}
							
		//oTable = $('#TBL_EVENTOS').DataTable();   
		
		$('#SCH_BAR_TBL_EVENTOS').keyup(function(){
			  //oTable.search($(this).val()).draw() ;
		})
	
</script>