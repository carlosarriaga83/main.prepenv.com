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
	$("#product_pic").prop("src",dataurl); // Note: IDs/Names in JS usually remain case-sensitive as defined in HTML
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
		//echo "Name: " . htmlspecialchars($name); // Use htmlspecialchars to prevent XSS
	}else{
		//$id = $_SESSION['user']['id']; // Lowercased 'USER' and 'ID'
		$id='';
	}

	
	$q = sprintf("SELECT * FROM PRODUCTS WHERE id = '%s'   ", $id );  // echo $q . "\n";
	$q = sprintf("SELECT * FROM PRODUCTS WHERE JSON_EXTRACT(Datos, '$.stripe_product_id') = '%s'   ", $id );  // echo $q . "\n";
	$R1 = SQL_2_OBJ_V2($q);
	//print_r($R1);die;

	$PRODUCT_DB 	= $R1['PL'][0]; // Lowercased 'PL'
	

	$DB_CURRENCIES = ['MXN', 'USD','CAD'];



	// TABLA DE PRECIOS


		function PRICES_TABLE($json) {
			// Decode the JSON string into an associative array
			//$data = json_decode($json, true);
			$data = $json;

		// Initialize an empty string for the table body
			$tableRow = '';

			// Loop through each product in the invoice_table array
			foreach ($data['data']['data'] as $P) {
				// Create a table row for each product

				$precio_money = '$ '. NUMBER_2_MONEY($P['unit_amount_decimal'] / 100); // Assuming 'unit_amount_decimal' is correct case from Stripe

				$tableRow .= <<<ROW
									<tr>
										<td>{$P['nickname']}</td>
										<td>$precio_money</td>
										<td>{$P['id']}</td>
										<td>{$P['active']}</td>
										<td class="text-center">
											<button type="button" class="" onclick="DELETE_STRIPE('PRICES', '{$P['id']}', this)">
												<iconify-icon icon="ic:twotone-close" class="text-danger-main text-xl"></iconify-icon>
											</button>
										</td>
									</tr>
								ROW;

			}

			// Return the table body
			return $tableRow;
		}

		include_once($_SERVER['DOCUMENT_ROOT'] . '/API/STRIPE/repo.php');



		$productId = $id;

		if ($productId != ''){

			$PRODUCT_STRIPE = getProductById($productId);
			$PRODUCT_STRIPE = json_decode($PRODUCT_STRIPE, true);
			//print_r($PRODUCT_STRIPE);die;
			$PRODUCT_STRIPE_ID = $PRODUCT_STRIPE['data']['id']; // Assuming 'data' and 'id' are correct case from Stripe



			$PRODUCT = $PRODUCT_STRIPE['data']; //print_r($PRODUCT);//die; // Assuming 'data' is correct case from Stripe

			$PRICES = productPrices($PRODUCT_STRIPE_ID);
			$PRICES = json_decode($PRICES, true);
			//print_r($PRICES);//die;

			$PRICES_TABLE_ROWS =  PRICES_TABLE($PRICES);

			$READ_ONLY = 'readonly';

		}

?>



<div class="dashboard-main-body">
	<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
		<h6 class="fw-semibold mb-0">View Product</h6>
		<ul class="d-flex align-items-center gap-2">
			<li class="fw-medium">
				<a href="index.php" class="d-flex align-items-center gap-1 hover-text-primary">
					<iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
					Dashboard
				</a>
			</li>
			<li>-</li>
			<li class="fw-medium">View Product</li>
		</ul>
	</div>


	<div class="row gy-4">
		<div class="col-lg-4">
			<div class="user-grid-card position-relative border radius-16 overflow-hidden bg-base h-100">
				<img src="assets/images/user-grid/user-grid-bg1.png" alt="" class="w-100 object-fit-cover">
				<div class="pb-24 ms-16 mb-24 me-16  mt--100">
					<div class="text-center border border-top-0 border-start-0 border-end-0">
						<img src="<?php echo $PRODUCT_DB['product_pic']; ?>" alt="" class="border br-white border-width-2-px w-200-px h-200-px rounded-circle object-fit-cover">
						<h6 class="mb-0 mt-16"><?php echo $PRODUCT['name']; ?></h6>

					</div>
					<div class="mt-24">
						<h6 class="text-xl mb-16">Product Info</h6>
						<ul>
							<li class="d-flex align-items-center gap-1 mb-12">
								<span class="w-30 text-md fw-semibold text-primary-light">Name</span>
								<span class="w-70 text-secondary-light fw-medium">: <?php echo $PRODUCT['name']; ?></span>
							</li>
							<li class="d-flex align-items-center gap-1 mb-12">
								<span class="w-30 text-md fw-semibold text-primary-light">Precio</span>
								<span class="w-70 text-secondary-light fw-medium">: <?php echo $PRODUCT['price']; ?></span>
							</li>
							<li class="d-flex align-items-center gap-1 mb-12">
								<span class="w-30 text-md fw-semibold text-primary-light">Stripe id</span>
								<span class="w-70 text-secondary-light fw-medium">: <?php echo $PRODUCT['id']; ?></span>
							</li>
							<li class="d-flex align-items-center gap-1 mb-12">
								<span class="w-30 text-md fw-semibold text-primary-light">DB id</span>
								<span class="w-70 text-secondary-light fw-medium">: <?php echo $PRODUCT_DB['EDIT_ID']; ?></span>
							</li>

							<li class="d-flex align-items-center gap-1">
								<span class="w-30 text-md fw-semibold text-primary-light"> Descripcion</span>
								<span class="w-70 text-secondary-light fw-medium">: <?php echo $PRODUCT['description']; ?></span>
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
							<button class="nav-link d-flex align-items-center px-24 active" id="pills-edit-Product-tab" data-bs-toggle="pill" data-bs-target="#pills-edit-Product" type="button" role="tab" aria-controls="pills-edit-Product" aria-selected="true">
								Edit Product
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
						<div class="tab-pane fade show active" id="pills-edit-Product" role="tabpanel" aria-labelledby="pills-edit-Product-tab" tabindex="0">
							<form id="FRM_1" action="#" FRM="FRM_1" DB="PRODUCTS">
								<h6 class="text-md text-primary-light mb-16">Product Image</h6>
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
								<input type="text" TBS="EDIT_ID" value="<?php echo $PRODUCT_DB['EDIT_ID']; ?>" readonly> <!-- Lowercased name attribute -->
								<input type="text" TBS="id" value="<?php echo $PRODUCT['id']; ?>" readonly> <!-- Lowercased name attribute -->
								<img id="product_pic" TBS="product_pic" src="<?php echo $PRODUCT_DB['product_pic']; ?>" alt="#" class="mb-24 mt-16" hidden></img> <!-- Lowercased name attribute -->
								<div class="row">
									<div class="col-sm-12">
										<div class="mb-20">
											<label for="name" class="form-label fw-semibold text-primary-light text-sm mb-8">Name <span class="text-danger-600">*</span></label>
											<input type="text" class="form-control radius-8" id="name" placeholder="Enter Product Name"  TBS="name"  value="<?php echo $PRODUCT['name']; ?>" <?php echo $READ_ONLY; ?> required> <!-- Lowercased name attribute -->
										</div>
									</div>
									<div class="col-sm-12">
										<div class="mb-20">
											<label for="desc" class="form-label fw-semibold text-primary-light text-sm mb-8">Descripción<span class="text-danger-600">*</span></label>
											<textarea class="form-control radius-8" id="desc" placeholder="Write description..." TBS="description" required ><?php echo $PRODUCT['description']; ?></textarea> <!-- Lowercased name attribute -->
										</div>
									</div>
									<div class="col-sm-12">
										<div class="d-flex align-items-center justify-content-center gap-3 mt-8 mb-24">
											<a href="./_PRODUCTOS_Ver.php">
												<button type="button"  class="border border-danger-600 bg-hover-danger-200 text-danger-600 text-md px-56 py-11 radius-8">
													Regresar
													</button>
											</a>
											<button type="submit" form="FRM_1" class="btn btn-primary border border-primary-600 text-md px-56 py-12 radius-8" onclick="" >
												Save
											</button>
										</div>
									</div>
								</div>
							</form>

							<form id="FRM_2" action="#" FRM="FRM_2" DB="PRICES">
							<input type="text" TBS="EDIT_ID" value="<?php echo $PRODUCT['EDIT_ID']; ?>" readonly> <!-- Lowercased name attribute -->
							<input type="text" TBS="id" value="<?php echo $PRODUCT['stripe_product_id']; ?>" readonly> <!-- Lowercased name attribute -->
								<div class="row">
									<div class="col-sm-4">
										<div class="mb-20">
											<label for="nick" class="form-label fw-semibold text-primary-light text-sm mb-8">Nickname<span class="text-danger-600">*</span></label>
											<input type="text" class="form-control radius-8" id="nick" placeholder="Nombre del precio"  TBS="price_nickname"  value="<?php echo $PRODUCT['price_nickname']; ?>" required> <!-- Lowercased name attribute -->
										</div>
									</div>

									<div class="col-sm-4">
										<div class="mb-20">
											<label for="price" class="form-label fw-semibold text-primary-light text-sm mb-8">Precio sin impuesto<span class="text-danger-600">*</span></label>
											<input type="number" class="form-control radius-8" id="price" placeholder="Precio unitario"  TBS="price"  value="<?php echo $PRODUCT['price']; ?>" required> <!-- Lowercased name attribute -->
										</div>
									</div>


									<div class="col-sm-4">
										<div class="mb-20">
											<label for="price" class="form-label fw-semibold text-primary-light text-sm mb-8">Currency<span class="text-danger-600">*</span></label>
											<select class="form-control radius-8 form-select" id="CURRENCY"  TBS="currency" value="<?php echo $PRODUCT['currency']; ?>" required> <!-- Lowercased name attribute -->

												<?php
													$HTML_OUT = '<option value="0" selected disabled> Select </option>';
													foreach( $DB_CURRENCIES as $CURRENCY){
														$PRODUCT['currency'] ==  $CURRENCY ? $SELECTED = 'selected' : $SELECTED = ''; // Lowercased 'currency'
														$ROL['id'] == '777' ? $SA_ONLY = 'sa_only' : $SA_ONLY = ''; // Lowercased 'id'
														$HTML_OUT .= sprintf( '<option class="%s" value="%s" %s> %s</option>',$SA_ONLY, $CURRENCY, $SELECTED,  $CURRENCY);

													}

													echo $HTML_OUT;


												?>

											</select>
										</div>
									</div>



									<div class="col-sm-12">
										<div class="table-responsive scroll-m">
											<table class="table bordered-table-sm text-sm" id="invoice-table" TBS="invoice_table"> <!-- 'invoice_table' was already lowercase -->
												<thead>
													<tr>

														<th scope="col" class="text-sm">Price</th>
														<th scope="col" class="text-sm">Id</th>
														<th scope="col" class="text-sm">Descripción</th>
														<th scope="col" class="text-sm">Activo</th>
														<th scope="col" class="text-center text-sm">Action</th>
													</tr>
												</thead>
												<tbody>
													<?php echo $PRICES_TABLE_ROWS; ?>
												</tbody>
											</table>
										</div>

									</div>



									<div class="col-sm-6 d-none">
										<div class="form-switch switch-primary py-12 px-16 border radius-8 position-relative mb-16">
											<label for="companzNewVeg" class="position-absolute w-100 h-100 start-0 top-0"></label>
											<div class="d-flex align-items-center gap-3 justify-content-between">
												<span class="form-check-label line-height-1 fw-medium text-secondary-light">Vegetariano</span>

												<?php $PRODUCT['opt_vegetariano'] == 'on' ? $CHECKED = 'checked' : $CHECKED = ''; ?> <!-- Lowercased key -->

												<input class="form-check-input" type="checkbox" role="switch" id="companzNewVeg" TBS="opt_vegetariano" <?php echo $CHECKED; ?> > <!-- Lowercased name attribute -->
											</div>
										</div>
									</div>
									<div class="col-sm-6 d-none">
										<div class="form-switch switch-primary py-12 px-16 border radius-8 position-relative mb-16">
											<label for="companzNewVegan" class="position-absolute w-100 h-100 start-0 top-0"></label>
											<div class="d-flex align-items-center gap-3 justify-content-between">
												<span class="form-check-label line-height-1 fw-medium text-secondary-light">Vegano</span>

												<?php $PRODUCT['opt_vegano'] == 'on' ? $CHECKED = 'checked' : $CHECKED = ''; ?> <!-- Lowercased key -->

												<input class="form-check-input" type="checkbox" role="switch" id="companzNewVegan" TBS="opt_vegano" <?php echo $CHECKED; ?> > <!-- Lowercased name attribute -->
											</div>
										</div>
									</div>




								</div>



							</form>
							<div class="d-flex align-items-center justify-content-center gap-3 mt-48">
								<a href="./_PRODUCTOS_Ver.php">
									<button type="button"  class="border border-danger-600 bg-hover-danger-200 text-danger-600 text-md px-56 py-11 radius-8">
										Regresar
									</button>
								</a>
								<button type="submit" form="FRM_2" class="btn btn-primary border border-primary-600 text-md px-56 py-12 radius-8" onclick="" >
									Save
								</button>
							</div>
						</div>

					<div class="tab-pane fade" id="pills-change-passwork" role="tabpanel" aria-labelledby="pills-change-passwork-tab" tabindex="0">
						<form id="FRM_USER_Product_PWD" action="#" FRM="FRM_USER_Product_PWD" DB="PRODUCTS">



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

		$("#imagePreview").css("background-image", "url( <?php echo $PRODUCT_DB['product_pic']; ?> )"); // Lowercased 'CLIENTE_PIC'

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

		$(document).on('submit','#FRM_2', function VALIDAR(e) {
            //$('#formulario').on('submit', function (e) {
			if (e.isDefaultPrevented()) {
				// handle the invalid form...
				} else {


				// everything looks good!
				e.preventDefault(); //prevent submit
				SAVE_CHANGES(this);

			}
		});

		$(document).on('submit','#FRM_USER_Product_PWD', function VALIDAR(e) {
			//$('#formulario').on('submit', function (e) {
			if (e.isDefaultPrevented()) {
				// handle the invalid form...
				} else {
				// everything looks good!
				e.preventDefault(); //prevent submit
				var PWD = $('input[TBS="pwd"]').val(); // Assuming TBS="pwd" exists somewhere for this form
				var PWD2 = $('input[TBS="pwd2"]').val();// Assuming TBS="pwd2" exists somewhere for this form
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
	$('input[TBS="email"]').on('blur keyup', function() { // Assuming an input with TBS="email" exists
		debugger;

		OBJ = {};


		OBJ['tabla']		 = 'Users'; // Lowercased key
		OBJ['search_for']	= $(this).val(); // Lowercased key

		debugger;
		var resp = POST_API(OBJ, 'API/does_exist/', true);
		debugger;

		if (resp.DATOS == 0){ // Assuming DATOS is correct case from API
			var VALIDATED_DIV = $('input[TBS="email"]').closest( "div" ); // Lowercased name attribute
			//VALIDATED_DIV.addClass('was-validated'); //.css( "background-color", "green" );
			$('input[TBS="email"]').addClass( 'bg-success-focus' ).removeClass( 'bg-danger-focus' ); // Lowercased name attribute
			//debugger;

			}else{
			var VALIDATED_DIV = $('input[TBS="email"]').closest( "div" ); // Lowercased name attribute
			//VALIDATED_DIV.removeClass('was-validated'); //
			$('input[TBS="email"]').addClass( 'bg-danger-focus' ).removeClass( 'bg-success-focus' ); // Lowercased name attribute
			//debugger;

		}

		if ( $('input[TBS="email"]').val() == '' ) { $('input[TBS="email"]').removeClass( 'bg-success-focus' ).removeClass( 'bg-danger-focus' ); } // Lowercased name attribute


	});

    $(document).on('click', '.remove-row', function() {
        //$(this).closest('tr').remove();


	});


</script>