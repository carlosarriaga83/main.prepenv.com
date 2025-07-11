<?php include './partials/layouts/layoutTop.php' ?>
<?php 
	
    //error_reporting(E_ALL);
	//ini_set('display_errors', 1);
	
	include_once($_SERVER['DOCUMENT_ROOT'] . '/PHP/MYF1.php');
	

	
?>

        <div class="dashboard-main-body">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
                <h6 class="fw-semibold mb-0">Languages</h6>
                <ul class="d-flex align-items-center gap-2">
                    <li class="fw-medium">
                        <a href="index.php" class="d-flex align-items-center gap-1 hover-text-primary">
                            <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                            Dashboard
                        </a>
                    </li>
                    <li>-</li>
                    <li class="fw-medium">Settings - Languages</li>
                </ul>
            </div>

            <div class="card h-100 p-0 radius-12">
                <div class="card-body p-24">
					<form id="FRM_1" action="#" FRM="FRM_1" DB="COMPANY">
					<input type="text" TBS="EDIT_ID" value="<?php echo $COMPANY['ID']; ?>" readonly>
                    <div class="row gy-4">
                        <div class="col-xxl-6">
                            <div class="card radius-12 shadow-none border overflow-hidden">
                                <div class="card-header bg-neutral-100 border-bottom py-16 px-24 d-flex align-items-center flex-wrap gap-3 justify-content-between">
                                    <div class="d-flex align-items-center gap-10">
                                        <span class="w-36-px h-36-px bg-base rounded-circle d-flex justify-content-center align-items-center">
                                            <img src="assets/images/payment/payment-gateway1.png" alt="" class="">
                                        </span>
                                        <span class="text-lg fw-semibold text-primary-light">Stripe</span>
                                    </div>
                                    <div class="form-switch switch-primary d-flex align-items-center justify-content-center">
                                        <input class="form-check-input" type="checkbox" role="switch" TBS="STRIPE_ENABLED" <?php echo ($COMPANY['STRIPE_ENABLED'] == 'on') ? 'checked' : ''; ?>>
                                    </div>
                                </div>
                                <div class="card-body p-24">
                                    <div class="row gy-3">
                                        <div class="col-sm-6">
                                            <span class="form-label fw-semibold text-primary-light text-md mb-8">Environment <span class="text-danger-600">*</span></span>
											

                                            <div class="d-flex align-items-center gap-3 d-none">
												<div class="d-flex align-items-center flex-wrap gap-28">
													<div class="form-check checked-primary d-flex align-items-center gap-2">
														<input class="form-check-input" type="radio" name="ENVIROMENT" value="TEST" id="horizontal1" <?php echo ($COMPANY['ENVIROMENT'] == 'TEST') ? 'checked' : ''; ?>>
														<label class="form-check-label line-height-1 fw-medium text-secondary-light" for="horizontal1"  > Test </label>
													</div>
													<div class="form-check checked-secondary d-flex align-items-center gap-2">
														<input class="form-check-input" type="radio" name="ENVIROMENT" value="PRODUCTION" id="horizontal2" <?php echo ($COMPANY['ENVIROMENT'] == 'PRODUCTION') ? 'checked' : ''; ?>>
														<label class="form-check-label line-height-1 fw-medium text-secondary-light" for="horizontal2"  > Production </label>
													</div>

												</div>											
			
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <label for="currency" class="form-label fw-semibold text-primary-light text-md mb-8">Currency <span class="text-danger-600">*</span></label>
                                            <select class="form-control radius-8 form-select" id="currency">
                                                <option selected disabled>USD</option>
                                                <option>MXN</option>
                                                <option>CAD</option>
                                            </select>
                                        </div>
                                        <div class="col-sm-6">
                                            <label for="secretKey" class="form-label fw-semibold text-primary-light text-md mb-8">Production API Key <span class="text-danger-600">*</span></label>
                                            <input type="text" class="form-control radius-8" id="secretKey" placeholder="Secret Key" value="<?php echo $COMPANY['STRIPE_PRODUCTION_API_KEY']; ?>" TBS="STRIPE_PRODUCTION_API_KEY">
                                        </div>
                                        <div class="col-sm-6">
                                            <label for="publicKey" class="form-label fw-semibold text-primary-light text-md mb-8">Test API Key<span class="text-danger-600">*</span></label>
                                            <input type="text" class="form-control radius-8" id="publicKey" placeholder="Publics Key" value="<?php echo $COMPANY['STRIPE_TEST_API_KEY']; ?>" TBS="STRIPE_TEST_API_KEY">
                                        </div>

                                        <div class="col-sm-6">
                                            <label for="logo" class="form-label fw-semibold text-primary-light text-md mb-8">Logo <span class="text-danger-600">*</span></label>
                                            <input type="file" class="form-control radius-8" id="logo">
                                        </div>
                                        <div class="col-sm-6">
                                            <label for="logo" class="form-label fw-semibold text-primary-light text-md mb-8"><span class="visibility-hidden">Save</span></label>
                                            <button type="submit" class="btn btn-primary border border-primary-600 text-md px-56 py-12 radius-8" onclick="" >
                                                Save
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xxl-6 d-none">
                            <div class="card radius-12 shadow-none border overflow-hidden">
                                <div class="card-header bg-neutral-100 border-bottom py-16 px-24 d-flex align-items-center flex-wrap gap-3 justify-content-between">
                                    <div class="d-flex align-items-center gap-10">
                                        <span class="w-36-px h-36-px bg-base rounded-circle d-flex justify-content-center align-items-center">
                                            <img src="assets/images/payment/payment-gateway2.png" alt="" class="">
                                        </span>
                                        <span class="text-lg fw-semibold text-primary-light">RazorPay</span>
                                    </div>
                                    <div class="form-switch switch-primary d-flex align-items-center justify-content-center">
                                        <input class="form-check-input" type="checkbox" role="switch" checked>
                                    </div>
                                </div>
                                <div class="card-body p-24">
                                    <div class="row gy-3">
                                        <div class="col-sm-6">
                                            <span class="form-label fw-semibold text-primary-light text-md mb-8">Environment <span class="text-danger-600">*</span></span>
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="d-flex align-items-center gap-10 fw-medium text-lg">
                                                    <div class="form-check style-check d-flex align-items-center">
                                                        <input class="form-check-input radius-4 border border-neutral-500" type="checkbox" name="checkbox" id="sandbox2" checked>
                                                    </div>
                                                    <label for="sandbox2" class="form-label fw-medium text-lg text-primary-light mb-0">Sandbox</label>
                                                </div>
                                                <div class="d-flex align-items-center gap-10 fw-medium text-lg">
                                                    <div class="form-check style-check d-flex align-items-center">
                                                        <input class="form-check-input radius-4 border border-neutral-500" type="checkbox" name="checkbox" id="Production2">
                                                    </div>
                                                    <label for="Production2" class="form-label fw-medium text-lg text-primary-light mb-0">Production</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <label for="currencyTwo" class="form-label fw-semibold text-primary-light text-md mb-8">Currency <span class="text-danger-600">*</span></label>
                                            <select class="form-control radius-8 form-select" id="currencyTwo">
                                                <option selected disabled>USD</option>
                                                <option>TK</option>
                                                <option>Rupee</option>
                                            </select>
                                        </div>
                                        <div class="col-sm-6">
                                            <label for="secretKeyTwo" class="form-label fw-semibold text-primary-light text-md mb-8">Secret Key <span class="text-danger-600">*</span></label>
                                            <input type="text" class="form-control radius-8" id="secretKeyTwo" placeholder="Secret Key" value="EGtgNkjt3I5lkhEEzicdot8gVH_PcFiKxx6ZBiXpVrp4QLDYcVQQMLX6MMG_fkS9_H0bwmZzBovb4jLP">
                                        </div>
                                        <div class="col-sm-6">
                                            <label for="publicKeyTwo" class="form-label fw-semibold text-primary-light text-md mb-8">Publics Key<span class="text-danger-600">*</span></label>
                                            <input type="text" class="form-control radius-8" id="publicKeyTwo" placeholder="Publics Key" value="AcRx7vvy79nbNxBemacGKmnnRe_CtxkItyspBS_eeMIPREwfCEIfPg1uX-bdqPrS_ZFGocxEH_SJRrIJ">
                                        </div>

                                        <div class="col-sm-6">
                                            <label for="logoTwo" class="form-label fw-semibold text-primary-light text-md mb-8">Logo <span class="text-danger-600">*</span></label>
                                            <input type="file" class="form-control radius-8" id="logoTwo">
                                        </div>
                                        <div class="col-sm-6">
                                            <label class="form-label fw-semibold text-primary-light text-md mb-8"><span class="visibility-hidden">Save</span></label>
                                            <button type="submit" class="btn btn-primary border border-primary-600 text-md px-24 py-8 radius-8 w-100 text-center">
                                                Save Change
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    </form>
                </div>
            </div>
        </div>

<?php include './partials/layouts/layoutBottom.php' ?>


<script>	
	$(document).ready(function() {
    

		

        
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


	
</script>