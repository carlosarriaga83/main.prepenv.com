<?php include './partials/layouts/layoutTop.php' ?>
<?php 	
    //error_reporting(E_ALL);
	//ini_set('display_errors', 1);
    
    
    //session_start(); 
    
    include_once './PHP/MYF1.php';
    
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        //echo "Name: " . htmlspecialchars($name); // Use htmlspecialchars to prevent XSS
    }else{
        //$id = $_SESSION['USER']['ID'];
    }

	
	//print_r($COMPANY);die;
    
    
?>
        <div class="dashboard-main-body">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
                <h6 class="fw-semibold mb-0">Company</h6>
                <ul class="d-flex align-items-center gap-2">
                    <li class="fw-medium">
                        <a href="index.php" class="d-flex align-items-center gap-1 hover-text-primary">
                            <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                            Dashboard
                        </a>
                    </li>
                    <li>-</li>
                    <li class="fw-medium">Settings - Company</li>
                </ul>
            </div>

            <div class="card h-100 p-0 radius-12 overflow-hidden">
                <div class="card-body p-40">
					<form id="FRM_1" action="#" FRM="FRM_1" DB="COMPANY">
						<input type="text" name="EDIT_ID" value="<?php echo $COMPANY['ID']; ?>" readonly>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="mb-20">
                                    <label for="name" class="form-label fw-semibold text-primary-light text-sm mb-8">Full Name <span class="text-danger-600">*</span></label>
                                    <input type="text" class="form-control radius-8" id="name" placeholder="Enter Full Name" name="NAME" value="<?php echo $COMPANY['NAME']; ?>" required>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="mb-20">
                                    <label for="email" class="form-label fw-semibold text-primary-light text-sm mb-8">Email <span class="text-danger-600">*</span></label>
                                    <input type="email" class="form-control radius-8" id="email" placeholder="Enter email address" name="EMAIL" value="<?php echo $COMPANY['EMAIL']; ?>">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="mb-20">
                                    <label for="phone" class="form-label fw-semibold text-primary-light text-sm mb-8">Phone Number</label>
                                    <input type="phone" class="form-control radius-8" id="phone" placeholder="Enter phone number"  name="PHONE" value="<?php echo $COMPANY['PHONE']; ?>">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="mb-20">
                                    <label for="Website" class="form-label fw-semibold text-primary-light text-sm mb-8"> Website</label>
                                    <input type="url" class="form-control radius-8" id="Website" placeholder="Website URL"  name="WEBSITE" value="<?php echo $COMPANY['WEBSITE']; ?>">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="mb-20">
                                    <label for="country" class="form-label fw-semibold text-primary-light text-sm mb-8">Country <span class="text-danger-600">*</span> </label>
                                    <select class="form-control radius-8 form-select" id="country"  name="COUNTRY" value="<?php echo $COMPANY['COUNTRY']; ?>">
                                        <option selected disabled>Select Country</option>
                                        <option>USA</option>
                                        <option>Canada</option>
                                        <option>México</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="mb-20">
                                    <label for="city" class="form-label fw-semibold text-primary-light text-sm mb-8">City <span class="text-danger-600">*</span> </label>
                                    <select class="form-control radius-8 form-select" id="city"  name="CITY" value="<?php echo $COMPANY['CITY']; ?>">
                                        <option selected disabled>Select City</option>
                                        <option>CDMX</option>

                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="mb-20">
                                    <label for="state" class="form-label fw-semibold text-primary-light text-sm mb-8">State <span class="text-danger-600">*</span> </label>
                                    <select class="form-control radius-8 form-select" id="state"  name="STATE" value="<?php echo $COMPANY['STATE']; ?>">
                                        <option selected disabled>Select State</option>
                                        <option>CDMX</option>

                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="mb-20">
                                    <label for="zip" class="form-label fw-semibold text-primary-light text-sm mb-8"> Zip Code <span class="text-danger-600">*</span></label>
                                    <input type="text" class="form-control radius-8" id="zip" placeholder="Zip Code"  name="ZIPCODE" value="<?php echo $COMPANY['ZIPCODE']; ?>">
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="mb-20">
                                    <label for="address" class="form-label fw-semibold text-primary-light text-sm mb-8"> Address* <span class="text-danger-600">*</span></label>
                                    <input type="text" class="form-control radius-8" id="address" placeholder="Enter Your Address"  name="ADDRESS" value="<?php echo $COMPANY['ADDRESS']; ?>">
                                </div>
                            </div>
                            <div class="d-flex align-items-center justify-content-center gap-3 mt-24">
                                <button type="reset" class="border border-danger-600 bg-hover-danger-200 text-danger-600 text-md px-40 py-11 radius-8">
                                    Reset
                                </button>
                                <button type="submit" class="btn btn-primary border border-primary-600 text-md px-24 py-12 radius-8">
                                    Save Change
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

<?php include './partials/layouts/layoutBottom.php' ?>

<script>	
	$(document).ready(function() {
    
        hideAlertAfterTime($('.alert'), 5000);
		
		//$("#imagePreview").css("background-image", "url( <?php echo $CLIENTE['CLIENTE_PIC']; ?> )");
        
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