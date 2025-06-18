<?php $script = '<script>
                    $(".remove-item-btn").on("click", function() {
                        //$(this).closest("tr").addClass("d-none")
                    });
                    
                    //let table = new DataTable("#TBL_1");
                    
                </script>';
                
                
                
                ?>


    
<?php include './partials/layouts/layoutTop.php' ?>




        <div class="dashboard-main-body">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
                <h6 class="fw-semibold mb-0">Role & Access</h6>
                <ul class="d-flex align-items-center gap-2">
                    <li class="fw-medium">
                        <a href="index.php" class="d-flex align-items-center gap-1 hover-text-primary">
                            <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                            Dashboard
                        </a>
                    </li>
                    <li>-</li>
                    <li class="fw-medium">Role & Access</li>
                </ul>
            </div>
            


                    
								<?php 
									$T_PARAMS 					= [];
									$T_PARAMS['DB']['TABLE'] 	= 'ROLES';
									$T_PARAMS['DB']['COLS'] 	= [ 'NAME', 'DESCRIPTION', 'STATUS'];
									
									$T_PARAMS['TABLE']['ID'] 	= 'TBL_1';
									$T_PARAMS['TABLE']['COLS'] 	= [ 'Name', 'Description', 'Status'];
									//$T_PARAMS['TABLE']['href'] 	= 'view-event.php';
									
									$T_PARAMS['TABLE']['SEARCH_BAR']	 	= true;
									$T_PARAMS['TABLE']['ADD_NEW']['TEXT'] 	= 'Nuevo Rol';
									//$T_PARAMS['TABLE']['ADD_NEW']['href'] 	= 'view-event.php';
									$T_PARAMS['TABLE']['ADD_NEW']['MISC_PROPS'] 	= 'data-bs-toggle="modal" data-bs-target="#Modal_1"';
									

									//echo DB_2_TABLE_V3($T_PARAMS ); 

									include './_ELEMENTS/ROLES/LIST/index.php';
									
								?>
                    
                    
        </div>

        <!-- Modal Start -->
        <div class="modal fade" id="Modal_1" tabindex="-1" aria-labelledby="Modal_1_LBL" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog modal-dialog-centered">
                <div class="modal-content radius-16 bg-base">
                    <div class="modal-header py-16 px-24 border border-top-0 border-start-0 border-end-0">
                        <h1 class="modal-title fs-5" id="Modal_1_LBL">Add New Role</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-24">
                        <form id="FRM_1" action="#" FRM="FRM_1" DB="ROLES">
                            <div class="row">
                                <div class="col-12  mb-2 " style="justify-content: space-between;">
                                    <div class="form-switch switch-primary py-12 px-16 radius-8 position-relative mb-2 d-flex"  >
                                        <div class="d-flex align-items-center gap-2 justify-content-between mb-2 col-10">
                                            <input type="text" name="EDIT_ID" value="<?php echo ''; ?>" FRM="FRM_1" DB="ROLES" readonly>
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
                                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Role Name<span class="text-danger-600">*</span></label>
                                    <input type="text" class="form-control radius-8" placeholder="Enter Role  Name" name="NAME" required>
                                </div>
                                <div class="col-12 mb-20">
                                    <label for="desc" class="form-label fw-semibold text-primary-light text-sm mb-8">Description</label>
                                    <textarea class="form-control" id="desc" rows="4" cols="50" placeholder="Write some text" name="DESCRIPTION" ></textarea>
                                </div>

                                <div class="col-12 mb-20 d-flex gap-3">
                                
                                    <div class="col-4 mb-20">
                                        <div class="form-switch switch-primary py-12 px-16 border radius-8 position-relative mb-16">
                                          
                                            <label  class="position-relative w-100 h-100 start-0 top-0 mb-24">General</label>
                                            
                                                <div class="d-flex align-items-center gap-3 justify-content-between mb-16">
                                                    <span class="form-check-label line-height-1 fw-medium text-secondary-light">Ver</span>

                                                    

                                                    <input class="form-check-input" type="checkbox" role="switch" id="companzNew" name="CAN_VIEW"  >
                                                    
                                                </div>
                                                <div class="d-flex align-items-center gap-3 justify-content-between mb-16">
                                                    <span class="form-check-label line-height-1 fw-medium text-secondary-light">Agregar</span>

                                                    

                                                    <input class="form-check-input" type="checkbox" role="switch" id="companzNew" name="CAN_ADD"  >
                                                </div>
                                                <div class="d-flex align-items-center gap-3 justify-content-between mb-16">
                                                    <span class="form-check-label line-height-1 fw-medium text-secondary-light">Editar</span>

                                                    

                                                    <input class="form-check-input" type="checkbox" role="switch" id="companzNew" name="CAN_EDIT"  >
                                                </div>
                                                <div class="d-flex align-items-center gap-3 justify-content-between mb-16">
                                                    <span class="form-check-label line-height-1 fw-medium text-secondary-light">Eliminar</span>

                                                    

                                                    <input class="form-check-input" type="checkbox" role="switch" id="companzNew" name="CAN_DELETE"  >
                                                </div>
                                                
                                        </div>
                                    </div>

                                    <div class="col-4 mb-20">
                                        <div class="form-switch switch-primary py-12 px-16 border radius-8 position-relative mb-16">
                                          
                                            <label  class="position-relative w-100 h-100 start-0 top-0 mb-24">Admin</label>
                                            
                                                <div class="d-flex align-items-center gap-3 justify-content-between mb-16">
                                                    <span class="form-check-label line-height-1 fw-medium text-secondary-light">Ver</span>

                                                    

                                                    <input class="form-check-input" type="checkbox" role="switch" id="companzNew" name="ADMIN_VIEW"  >
                                                    
                                                </div>
                                                <div class="d-flex align-items-center gap-3 justify-content-between mb-16">
                                                    <span class="form-check-label line-height-1 fw-medium text-secondary-light">Agregar</span>

                                                    

                                                    <input class="form-check-input" type="checkbox" role="switch" id="companzNew" name="ADMIN_ADD"  >
                                                </div>
                                                <div class="d-flex align-items-center gap-3 justify-content-between mb-16">
                                                    <span class="form-check-label line-height-1 fw-medium text-secondary-light">Editar</span>

                                                    

                                                    <input class="form-check-input" type="checkbox" role="switch" id="companzNew" name="ADMIN_EDIT"  >
                                                </div>
                                                <div class="d-flex align-items-center gap-3 justify-content-between mb-16">
                                                    <span class="form-check-label line-height-1 fw-medium text-secondary-light">Eliminar</span>

                                                    

                                                    <input class="form-check-input" type="checkbox" role="switch" id="companzNew" name="ADMIN_DELETE"  >
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
	$(document).ready(function() {
    
        hideAlertAfterTime($('.alert'), 5000);
		

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
        

            
    });

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
            
            OBJ = {};
            
            OBJ['ID'] 		= ID;
            OBJ['TABLA'] 	= TABLA;
            var resp = POST_API(OBJ, '/API/get_data/');
    
            jsonToForm_v2('' + FORM, resp.DATOS.PL[0].Datos)
        });
	
</script>