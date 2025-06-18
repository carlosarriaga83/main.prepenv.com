<?php $script ='<script>
    // ======================== Upload Image Start =====================
	function readURL(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            var img = new Image();
            img.onload = function() {
                var canvas = document.createElement("canvas");
                var ctx = canvas.getContext("2d");
                ctx.drawImage(img, 0, 0);

                var MAX_WIDTH = 800;
                var MAX_HEIGHT = 600;
                var width = img.width;
                var height = img.height;

                if (width > height) {
                    if (width > MAX_WIDTH) {
                        height *= MAX_WIDTH / width;
                        width = MAX_WIDTH;
                    }
                } else {
                    if (height > MAX_HEIGHT) {
                        width *= MAX_HEIGHT / height;
                        height = MAX_HEIGHT;
                    }
                }
                canvas.width = width;
                canvas.height = height;
                var ctx = canvas.getContext("2d");
                ctx.drawImage(img, 0, 0, width, height);

                var dataurl = canvas.toDataURL("image/png");

                $("#imagePreview").css("background-image", "url(" + dataurl + ")");
                $("#imagePreview").hide();
                $("#imagePreview").fadeIn(650);
                $("#cliente_pic").prop("src",dataurl); // Assuming CLIENTE_PIC here is an ID, not a TBS value or array key. If it should be lowercased based on other rules, adjust accordingly.
            }
            img.src = e.target.result;
        }
        reader.readAsDataURL(input.files[0]);
    }
}
    $("#imageUpload").change(function() {
        readURL(this);
    });
    // ======================== Upload Image End =====================

    // ================== Password Show Hide Js Start ==========
    function initializePasswordToggle(toggleSelector) {
        $(toggleSelector).on("click", function() {
            $(this).toggleClass("ri-eye-off-line");
            var input = $($(this).attr("data-toggle"));
            if (input.attr("type") === "password") {
                input.attr("type", "text");
            } else {
                input.attr("type", "password");
            }
        });
    }
    // Call the function
    initializePasswordToggle(".toggle-password");
    // ========================= Password Show Hide Js End ===========================
    </script>';?>


<?php include './partials/layouts/layoutTop.php' ?>
<?php
    //error_reporting(E_ALL);
	//ini_set('display_errors', 1);


    //session_start();

    include_once './PHP/MYF1.php';

    if (isset($_GET['id'])) {
        $id = $_GET['id'];

    }else{
		$id='';
    }

    
	$q = sprintf("SELECT * FROM CLIENTS WHERE JSON_EXTRACT(Datos, '$.stripe_client_id') = '%s'   ", $id );  // echo $q . "\n";
	$R1 = SQL_2_OBJ_V2($q);
	//print_r($R1);die;

	$CLIENTE_DB 	= $R1['PL'][0]; // Lowercased 'PL'
    

		$clientId = $id;

		include_once($_SERVER['DOCUMENT_ROOT'] . '/API/STRIPE/repo.php');

		if ($clientId != ''){

			$CLIENT_STRIPE = getClientById($clientId);
			$CLIENT_STRIPE = json_decode($CLIENT_STRIPE, true);
			//print_r($CLIENT_STRIPE);//die;
			$CLIENT_STRIPE_ID = $CLIENT_STRIPE['data']['id']; // Already lowercase



			$CLIENTE = $CLIENT_STRIPE['data']; //print_r($PRODUCT);//die; // Already lowercase

			//$PRICES = productPrices($CLIENT_STRIPE_ID);
			//$PRICES = json_decode($PRICES, true);
			//print_r($PRICES);die;

			//$PRICES_TABLE_ROWS =  PRICES_TABLE($PRICES);

		}

?>



        <div class="dashboard-main-body">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
                <h6 class="fw-semibold mb-0">View Cliente</h6>
                <ul class="d-flex align-items-center gap-2">
                    <li class="fw-medium">
                        <a href="index.php" class="d-flex align-items-center gap-1 hover-text-primary">
                            <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                            Dashboard
                        </a>
                    </li>
                    <li>-</li>
                    <li class="fw-medium">View Cliente</li>
                </ul>
            </div>

            <div class="row gy-4">
                <div class="col-lg-4">
                    <div class="user-grid-card position-relative border radius-16 overflow-hidden bg-base h-100">
                        <img src="assets/images/user-grid/user-grid-bg1.png" alt="" class="w-100 object-fit-cover">
                        <div class="pb-24 ms-16 mb-24 me-16  mt--100">
                            <div class="text-center border border-top-0 border-start-0 border-end-0">
                                <img src="<?php echo $CLIENTE_DB['cliente_pic']; ?>" alt="#" class="border br-white border-width-2-px w-200-px h-200-px rounded-circle object-fit-cover">
                                <h6 class="mb-0 mt-16"><?php echo $CLIENTE['name']; ?></h6>

                            </div>
                            <div class="mt-24">
                                <h6 class="text-xl mb-16">Cliente Info</h6>
                                <ul>
                                    <li class="d-flex align-items-center gap-1 mb-12">
                                        <span class="w-30 text-md fw-semibold text-primary-light">Name</span>
                                        <span class="w-70 text-secondary-light fw-medium">: <?php echo $CLIENTE['name']; ?></span>
                                    </li>
									<li class="d-flex align-items-center gap-1 mb-12">
                                        <span class="w-30 text-md fw-semibold text-primary-light">Dirección</span>
                                        <span class="w-70 text-secondary-light fw-medium">: <?php echo $CLIENTE['address']['line1']; ?></span>
                                    </li>
									<li class="d-flex align-items-center gap-1 mb-12">
                                        <span class="w-30 text-md fw-semibold text-primary-light">Teléfono</span>
                                        <span class="w-70 text-secondary-light fw-medium">: <?php echo $CLIENTE['phone']; ?></span>
                                    </li>
									<li class="d-flex align-items-center gap-1 mb-12">
                                        <span class="w-30 text-md fw-semibold text-primary-light">Email</span>
                                        <span class="w-70 text-secondary-light fw-medium">: <?php echo $CLIENTE['email']; ?></span>
                                    </li>
									<li class="d-flex align-items-center gap-1 mb-12">
                                        <span class="w-30 text-md fw-semibold text-primary-light">Stripe id</span>
                                        <span class="w-70 text-secondary-light fw-medium">: <?php echo $CLIENTE['id']; ?></span>
                                    </li>
									<li class="d-flex align-items-center gap-1 mb-12">
                                        <span class="w-30 text-md fw-semibold text-primary-light">DB id</span>
                                        <span class="w-70 text-secondary-light fw-medium">: <?php echo $CLIENTE_DB['EDIT_ID']; ?></span>
                                    </li>


                                    <li class="d-flex align-items-center gap-1">
                                        <span class="w-30 text-md fw-semibold text-primary-light"> Descripcion</span>
                                        <span class="w-70 text-secondary-light fw-medium">: <?php echo $CLIENTE['description']; ?></span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-8">
                    <div class="card h-100">
                        <div class="card-body p-24">
                            <ul class="nav border-gradient-tab nav-pills mb-20 d-inline-flex" id="pills-tab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link d-flex align-items-center px-24 active" id="pills-edit-Cliente-tab" data-bs-toggle="pill" data-bs-target="#pills-edit-Cliente" type="button" role="tab" aria-controls="pills-edit-Cliente" aria-selected="true">
                                        Edit Cliente
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link d-flex align-items-center px-24" id="pills-change-passwork-tab" data-bs-toggle="pill" data-bs-target="#pills-change-passwork" type="button" role="tab" aria-controls="pills-change-passwork" aria-selected="false" tabindex="-1">
                                        Change Password
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link d-flex align-items-center px-24" id="pills-notification-tab" data-bs-toggle="pill" data-bs-target="#pills-notification" type="button" role="tab" aria-controls="pills-notification" aria-selected="false" tabindex="-1">
                                        Notification Settings
                                    </button>
                                </li>
                            </ul>

                            <div class="tab-content" id="pills-tabContent">
                                <div class="tab-pane fade show active" id="pills-edit-Cliente" role="tabpanel" aria-labelledby="pills-edit-Cliente-tab" tabindex="0">
                                    <h6 class="text-md text-primary-light mb-16">Cliente Image</h6>
                                    <!-- Upload Image Start -->
                                    <div class="mb-24 mt-16">
                                        <div class="avatar-upload">
                                            <div class="avatar-edit position-absolute bottom-0 end-0 me-24 mt-16 z-1 cursor-pointer">
                                                <input type='file' id="imageUpload" accept=".png, .jpg, .jpeg" hidden>
                                                <label for="imageUpload" class="w-32-px h-32-px d-flex justify-content-center align-items-center bg-primary-50 text-primary-600 border border-primary-600 bg-hover-primary-100 text-lg rounded-circle">
                                                    <iconify-icon icon="solar:camera-outline" class="icon"></iconify-icon>
                                                </label>
                                            </div>
                                            <div class="avatar-preview">
                                                <div id="imagePreview">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Upload Image End -->
                                    <form id="FRM_1" action="#" FRM="FRM_1" DB="CLIENTS">
										<input type="text" TBS="EDIT_ID" value="<?php echo $CLIENTE_DB['EDIT_ID']; ?>" readonly>
										<input type="text" TBS="id" value="<?php echo $CLIENTE['id']; ?>" readonly>
										<img id="cliente_pic" TBS="cliente_pic" src="<?php echo $CLIENTE['cliente_pic']; ?>" class="mb-24 mt-16" hidden></img>
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <div class="mb-20">
                                                    <label for="name" class="form-label fw-semibold text-primary-light text-sm mb-8">Name <span class="text-danger-600">*</span></label>
                                                    <input type="text" class="form-control radius-8" id="name" placeholder="Enter Cliente Name"  TBS="name"  value="<?php echo $CLIENTE['name']; ?>" required='required'>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="mb-20">
                                                    <label for="email" class="form-label fw-semibold text-primary-light text-sm mb-8">Correo <span class="text-danger-600">*</span></label>
                                                    <input type="email" class="form-control radius-8" id="email" placeholder="Email"  TBS="email"  value="<?php echo $CLIENTE['email']; ?>" required='required'>
                                                </div>
                                            </div>

                                            <div class="col-sm-6">
                                                <div class="mb-20">
                                                    <label for="phone" class="form-label fw-semibold text-primary-light text-sm mb-8">Teléfono<span class="text-danger-600"></span></label>
                                                    <input type="phone" class="form-control radius-8" id="phone" placeholder="10 dígitos"  TBS="phone"  value="<?php echo $CLIENTE['phone']; ?>" >
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="mb-20">
                                                    <label for="RFC" class="form-label fw-semibold text-primary-light text-sm mb-8">RFC<span class="text-danger-600"></span></label>
                                                    <input type="text" class="form-control radius-8" id="RFC" placeholder="ABC010203AB9"  TBS="rfc"  value="<?php echo $CLIENTE['metadata']['rfc']; ?>" >
                                                </div>
                                            </div>
                                            <div class="col-sm-12">
                                                <div class="mb-20">
                                                    <label for="address" class="form-label fw-semibold text-primary-light text-sm mb-8">Dirección</label>
                                                    <textarea class="form-control radius-8" id="address" placeholder="Dirección" TBS="address" ><?php echo $CLIENTE['address']['line1']; ?></textarea>
                                                </div>
                                            </div>
                                            <div class="col-sm-12">
                                                <div class="mb-20">
                                                    <label for="desc" class="form-label fw-semibold text-primary-light text-sm mb-8">Descripción</label>
                                                    <textarea class="form-control radius-8" id="desc" placeholder="Write description..." TBS="description" ><?php echo $CLIENTE['description']; ?></textarea>
                                                </div>
                                            </div>


                                            <div class="col-sm-6 d-none">
                                                <div class="form-switch switch-primary py-12 px-16 border radius-8 position-relative mb-16">
                                                    <label for="companzNew" class="position-absolute w-100 h-100 start-0 top-0"></label>
                                                    <div class="d-flex align-items-center gap-3 justify-content-between">
                                                        <span class="form-check-label line-height-1 fw-medium text-secondary-light">Vegetariano</span>

                                                        <?php $CLIENTE['opt_vegetariano'] == 'on' ? $CHECKED = 'checked' : $CHECKED = ''; // Lowercased 'OPT_VEGETARIANO' ?>

                                                        <input class="form-check-input" type="checkbox" role="switch" id="companzNew" TBS="OPT_VEGETARIANO" <?php echo $CHECKED; ?> >
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-6 d-none">
                                                <div class="form-switch switch-primary py-12 px-16 border radius-8 position-relative mb-16">
                                                    <label for="companzNew" class="position-absolute w-100 h-100 start-0 top-0"></label>
                                                    <div class="d-flex align-items-center gap-3 justify-content-between">
                                                        <span class="form-check-label line-height-1 fw-medium text-secondary-light">Vegano</span>

                                                        <?php $CLIENTE['opt_vegano'] == 'on' ? $CHECKED = 'checked' : $CHECKED = ''; // Lowercased 'OPT_VEGANO' ?>

                                                        <input class="form-check-input" type="checkbox" role="switch" id="companzNew" TBS="OPT_VEGANO" <?php echo $CHECKED; ?> >
                                                    </div>
                                                </div>
                                            </div>




                                        </div>

                                        <div class="d-flex align-items-center justify-content-center gap-3 mt-48">
                                            <a href="./_CLIENTS_Ver.php">
                                                <button type="button"  class="border border-danger-600 bg-hover-danger-200 text-danger-600 text-md px-56 py-11 radius-8">
                                                    Regresar
                                                </button>
                                            </a>
                                            <button type="submit" class="btn btn-primary border border-primary-600 text-md px-56 py-12 radius-8" onclick="" >
                                                Save
                                            </button>
                                        </div>
                                    </form>
                                </div>

                                <div class="tab-pane fade" id="pills-change-passwork" role="tabpanel" aria-labelledby="pills-change-passwork-tab" tabindex="0">
                                    <form id="FRM_1_PWD" action="#" FRM="FRM_1_PWD" DB="CLIENTS">
                                    <input type="text" TBS="id" value="<?php echo $CLIENTE['id']; ?>">

                                        <div class="mb-20">
                                            <label for="your-password" class="form-label fw-semibold text-primary-light text-sm mb-8">New Password <span class="text-danger-600">*</span></label>
                                            <div class="position-relative">
                                                <input type="password" class="form-control radius-8" id="your-password" placeholder="Enter New Password*" TBS="PWD" required>
                                                <span class="toggle-password ri-eye-line cursor-pointer position-absolute end-0 top-50 translate-middle-y me-16 text-secondary-light" data-toggle="#your-password"></span>
                                            </div>
                                        </div>
                                        <div class="mb-20">
                                            <label for="confirm-password" class="form-label fw-semibold text-primary-light text-sm mb-8">Confirmed Password <span class="text-danger-600">*</span></label>
                                            <div class="position-relative">
                                                <input type="password" class="form-control radius-8" id="confirm-password" placeholder="Confirm Password*" TBS="PWD2" required>
                                                <span class="toggle-password ri-eye-line cursor-pointer position-absolute end-0 top-50 translate-middle-y me-16 text-secondary-light" data-toggle="#confirm-password"></span>
                                            </div>
                                        </div>

                                        <div class="d-flex align-items-center justify-content-center gap-3">

                                            <button type="submit" class="btn btn-primary border border-primary-600 text-md px-56 py-12 radius-8" onclick="" >
                                                Save
                                            </button>
                                        </div>
                                    </form>
                                </div>

                                <div class="tab-pane fade" id="pills-notification" role="tabpanel" aria-labelledby="pills-notification-tab" tabindex="0">
                                    <div class="form-switch switch-primary py-12 px-16 border radius-8 position-relative mb-16">
                                        <label for="companzNew" class="position-absolute w-100 h-100 start-0 top-0"></label>
                                        <div class="d-flex align-items-center gap-3 justify-content-between">
                                            <span class="form-check-label line-height-1 fw-medium text-secondary-light">Company News</span>
                                            <input class="form-check-input" type="checkbox" role="switch" id="companzNew">
                                        </div>
                                    </div>
                                    <div class="form-switch switch-primary py-12 px-16 border radius-8 position-relative mb-16">
                                        <label for="pushNotifcation" class="position-absolute w-100 h-100 start-0 top-0"></label>
                                        <div class="d-flex align-items-center gap-3 justify-content-between">
                                            <span class="form-check-label line-height-1 fw-medium text-secondary-light">Push Notification</span>
                                            <input class="form-check-input" type="checkbox" role="switch" id="pushNotifcation" checked>
                                        </div>
                                    </div>
                                    <div class="form-switch switch-primary py-12 px-16 border radius-8 position-relative mb-16">
                                        <label for="weeklyLetters" class="position-absolute w-100 h-100 start-0 top-0"></label>
                                        <div class="d-flex align-items-center gap-3 justify-content-between">
                                            <span class="form-check-label line-height-1 fw-medium text-secondary-light">Weekly News Letters</span>
                                            <input class="form-check-input" type="checkbox" role="switch" id="weeklyLetters" checked>
                                        </div>
                                    </div>
                                    <div class="form-switch switch-primary py-12 px-16 border radius-8 position-relative mb-16">
                                        <label for="meetUp" class="position-absolute w-100 h-100 start-0 top-0"></label>
                                        <div class="d-flex align-items-center gap-3 justify-content-between">
                                            <span class="form-check-label line-height-1 fw-medium text-secondary-light">Meetups Near you</span>
                                            <input class="form-check-input" type="checkbox" role="switch" id="meetUp">
                                        </div>
                                    </div>
                                    <div class="form-switch switch-primary py-12 px-16 border radius-8 position-relative mb-16">
                                        <label for="orderNotification" class="position-absolute w-100 h-100 start-0 top-0"></label>
                                        <div class="d-flex align-items-center gap-3 justify-content-between">
                                            <span class="form-check-label line-height-1 fw-medium text-secondary-light">Orders Notifications</span>
                                            <input class="form-check-input" type="checkbox" role="switch" id="orderNotification" checked>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

<?php include './partials/layouts/layoutBottom.php' ?>

<script>
	$(document).ready(function() {



		$("#imagePreview").css("background-image", "url( <?php echo $CLIENTE_DB['cliente_pic']; ?> )"); // Lowercased 'CLIENTE_PIC'

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

        $(document).on('submit','#FRM_1_PWD', function VALIDAR(e) {
        //$('#formulario').on('submit', function (e) {
          if (e.isDefaultPrevented()) {
            // handle the invalid form...
          } else {
            // everything looks good!
            e.preventDefault(); //prevent submit
            var PWD = $('input[TBS="PWD"]').val();
            var PWD2 = $('input[TBS="PWD2"]').val();
            //$('input[TBS="PWD"]').val(PWD1);

            debugger;
            if ( PWD == PWD2){
                SAVE_CHANGES(this);
            }
          }
        });


    });



</script>

<script>



    // Usage example:
$('input[TBS="EMAIL"]').on('blur keyup', function() { // Assuming EMAIL here refers to the input name, not an array key
        debugger;

        OBJ = {};


        OBJ['TABLA']         = 'Users'; // Lowercased 'TABLA'
        OBJ['SEARCH_FOR']    = $(this).val(); // Lowercased 'SEARCH_FOR'

        debugger;
        var resp = POST_API(OBJ, 'API/does_exist/', true);
        debugger;

        if (resp.DATOS == 0){ // Assuming DATOS is a key from the API response, case sensitivity depends on the API
            var VALIDATED_DIV = $('input[TBS="EMAIL"]').closest( "div" );
            //VALIDATED_DIV.addClass('was-validated'); //.css( "background-color", "green" );
            $('input[TBS="EMAIL"]').addClass( 'bg-success-focus' ).removeClass( 'bg-danger-focus' );
            //debugger;

        }else{
            var VALIDATED_DIV = $('input[TBS="EMAIL"]').closest( "div" );
            //VALIDATED_DIV.removeClass('was-validated'); //
            $('input[TBS="EMAIL"]').addClass( 'bg-danger-focus' ).removeClass( 'bg-success-focus' );
            //debugger;

        }

        if ( $('input[TBS="EMAIL"]').val() == '' ) { $('input[TBS="EMAIL"]').removeClass( 'bg-success-focus' ).removeClass( 'bg-danger-focus' ); }


    });


</script>