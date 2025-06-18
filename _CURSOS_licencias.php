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

	$q = sprintf("SELECT * FROM %s", 'licencias_JSON');  //echo $q . "\n";
	$R1 = SQL_2_OBJ_V2($q, 1);
	$RESP['R1'] = $R1; //print_r($R1);
	$DB_DATA = $R1['DATA'];

		
	
	header('Content-type: html; charset=utf-8');
?>


<?php include './partials/layouts/layoutTop.php' ?>

        <div class="dashboard-main-body">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
                <h6 class="fw-semibold mb-0">Licencias</h6>
                <ul class="d-flex align-items-center gap-2">
                    <li class="fw-medium">
                        <a href="index.php" class="d-flex align-items-center gap-1 hover-text-primary">
                            <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                            Home
                        </a>
                    </li>
                    <li>-</li>
                    <li class="fw-medium">Licencias</li>

                </ul>
            </div>
								
								
								<?php 
									$T_PARAMS 					= [];
									$T_PARAMS['DB']['TABLE'] 	= 'licencias_JSON';
									$T_PARAMS['DB']['COLS'] 	= [ 'empresa', 'llave','nivel',  'consumo', 'inicia', 'expira',  'Action'];
									
									$T_PARAMS['TABLE']['ID'] 	= 'TBL_1';
									$T_PARAMS['TABLE']['COLS'] 	= [ 'Empresa', 'Llave','Nivel', 'Consumo', 'Inicia', 'Expira', 'Action'];
									$T_PARAMS['TABLE']['href'] 	= '_CURSOS_licencias.php';

									$T_PARAMS['TABLE']['SEARCH_BAR']	 	= true;
									$T_PARAMS['TABLE']['ADD_NEW']['TEXT'] 	= 'Add new';
									$T_PARAMS['TABLE']['ADD_NEW']['href'] 	= '';
									$T_PARAMS['TABLE']['ADD_NEW']['MISC_PROPS'] 	= 'data-bs-toggle="modal" data-bs-target="#Modal_1"';
									

									//echo DB_2_TABLE_V3($T_PARAMS );  

									include './_ELEMENTS/CURSOS/LIST/cursos_list.php';
									
								?>


			
											
        </div>


        <!-- Modal Start -->
        <div class="modal fade" id="Modal_1" tabindex="-1" aria-labelledby="Modal_1_LBL" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog modal-dialog-centered">
                <div class="modal-content radius-16 bg-base">
                    <div class="modal-header py-16 px-24 border border-top-0 border-start-0 border-end-0">
                        <h1 class="modal-title fs-5" id="Modal_1_LBL">Licencia</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-24">
                        <form id="FRM_1" action="#" FRM="FRM_1" DB="licencias_JSON" DB_ID="1" >
                            <div class="row">
                                <div class="col-12  mb-2 " style="justify-content: space-between;">
                                    <div class="form-switch switch-primary py-12 px-16 radius-8 position-relative mb-2 d-flex"  >
                                        <div class="d-flex align-items-center gap-2 justify-content-between mb-2 col-10">
                                            <input type="text" name="EDIT_ID" value="<?php echo ''; ?>" FRM="FRM_1" DB="licencias_JSON" readonly>
                                        </div>
                                        <div class="d-flex align-items-center justify-content-between mb-2 col-2">
                                            <label  class="position-relative w-100 h-100 start-0 top-0 mb-2">Activo</label>
                                            <input class="form-check-input" type="checkbox" role="switch" id="companzNew" name="STATUS"  >
                                        </div>
                                    </div>
                                    
                                </div>
                            </div>
                        
                            <div class="row">

                                <div class="col-12 mb-20">
                                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Empresa<span class="text-danger-600">*</span></label>
                                    <input type="text" class="form-control radius-8" placeholder="Enter Role  Name" name="empresa" required>
                                </div>
								
                                <div class="col-12 mb-20">
                                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Sucursal<span class="text-danger-600">*</span></label>
                                    <input type="text" class="form-control radius-8" placeholder="Enter Role  Name" name="sucursal" required>
                                </div>
								
                                <div class="col-12 mb-20">
                                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Region<span class="text-danger-600">*</span></label>
                                    <input type="text" class="form-control radius-8" placeholder="Enter Role  Name" name="region" required>
                                </div>

                                <div class="col-6 mb-20">
                                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Inicia<span class="text-danger-600">*</span></label>
                                    <input type="date" class="form-control radius-8" placeholder="Enter Role  Name" name="inicia"  required>
                                </div>
								
                                <div class="col-6 mb-20">
                                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Expira<span class="text-danger-600">*</span></label>
                                    <input type="date" class="form-control radius-8" placeholder="Enter Role  Name" name="expira" required>
                                </div>								
                                
								
                                <div class="col-6 mb-20">
                                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Cantidad de licencias<span class="text-danger-600">*</span></label>
                                    <input type="number" class="form-control radius-8" placeholder="" name="unidades" required>
                                </div>								
                                
								
                                <div class="col-6 mb-20">
                                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Llave<span class="text-danger-600">*</span></label>
                                    <input type="text" class="form-control radius-8" placeholder="" name="llave" required>
                                </div>								
                                



								

                                <div class="col-12 mb-20 d-flex gap-3">
                                


                                    <div class="col-4 mb-20">
                                        <div class="form-switch switch-primary py-12 px-16 border radius-8 position-relative mb-16">
                                          
                                            <label class="form-label fw-semibold text-primary-light text-sm border-label">Nivel de curso<span class="text-danger-600">*</span></label>

                                            
                                                <div class="d-flex align-items-center gap-3 justify-content-between mb-16">
                                                    <span class="form-check-label line-height-1 fw-medium text-secondary-light">Básico</span>
													<input class="form-check-input" type="radio" name="nivel" TBS="nivel" id="licenseTypeStandard" value="Básico" checked required>
                                                    
                                                    
                                                </div>
                                                <div class="d-flex align-items-center gap-3 justify-content-between mb-16">


                                                    <span class="form-check-label line-height-1 fw-medium text-secondary-light">Avanzado</span>
                                                    <input class="form-check-input" type="radio" name="nivel" TBS="nivel" id="licenseTypePremium" value="Avanzado" required>
                                                </div>

                                                
                                        </div>
                                    </div>
                                </div>
                                
                                
                                
                                <div class="d-flex align-items-center justify-content-center gap-3 mt-24">
                                    <button type="reset" class="border border-danger-600 bg-hover-danger-200 text-danger-600 text-md px-40 py-11 radius-8">
                                        Reset
                                    </button>
									<button type="button" class="border border-danger-600 bg-hover-danger-200 text-danger-600 text-md px-40 py-11 radius-8" data-bs-dismiss="modal">
										Close
									</button>
                                    <button type="submit" class="btn btn-primary border border-primary-600 text-md px-48 py-12 radius-8">
                                        Save
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal End -->

		
<?php include './partials/layouts/layoutBottom.php' ?>



<script>




        function LOAD_ID(id) { 
        
            clearForm('FRM_1');
            
            $('input[name="EDIT_ID"]').val(id);
            

            $('input[name="EDIT_ID"]').trigger( "change" );
        }
        
        
        $(document).on('change','input[name="EDIT_ID"]', function LOAD(e) { 
            
            debugger;
            var TABLA   = $(this).attr('DB');
            var FORM    = $(this).attr('FRM');
            var ID      = $(this).val();
			var id = $('#' + FORM + ' input[name="EDIT_ID"]').val();
            
            OBJ = {};
            
            OBJ['ID'] 		= id;
            OBJ['EDIT_ID'] 	= id;
            OBJ['ID'] 		= id;
            OBJ['TABLA'] 	= TABLA;
            OBJ['DB_ID'] 	= 1 ;
            var resp = POST_API(OBJ, 'API/get_data/custom1.php');

            if (resp && resp.PL) {
                jsonToForm_v2('' + FORM, resp.PL);
            } else {
                //console.error("Error: Response or response payload is undefined.", resp);
            }
        });
		
        $(document).on('submit','#FRM_1', function VALIDAR(e) { 
        //$('#formulario').on('submit', function (e) {
          if (e.isDefaultPrevented()) {
            // handle the invalid form...
          } else {
            // everything looks good!
            e.preventDefault(); //prevent submit
            SAVE_CHANGES(this);
            
          }
        });
	
</script>