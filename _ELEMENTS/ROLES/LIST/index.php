<?php
	
//error_reporting(E_ALL);
//ini_set('display_errors', 1);
	
//echo $_SERVER['DOCUMENT_ROOT'] . '/ADMIN/PHP/MYF1.php';
include_once($_SERVER['DOCUMENT_ROOT'] . '/ADMIN/PHP/MYF1.php');


setlocale(LC_ALL, 'en_US.UTF-8');
header('Content-type: text/javascript; charset=utf-8');
	

if(session_id() == '' || !isset($_SESSION) || session_status() === PHP_SESSION_NONE) {
	// session isn't started
	session_start( [ 'cookie_lifetime' => 604800 ]); 

}
 

	
$entityBody = file_get_contents('php://input');
$BODY_OB = json_decode($entityBody, true);		//$BODY_OB = json_decode($BODY_EN, true);

$DATA = $BODY_OB;   

//echo 'ok'; //die;


function THIS_FUNCTION( $T_PARAMS ) {


	$q = sprintf("SELECT * FROM %s", $T_PARAMS['DB']['TABLE']);  //echo $q . "\n";
	$R1 = SQL_2_OBJ_V2($q);
	$RESP['R1'] = $R1; //print_r($R1);
	$DB_DATA = $R1['DATA'];

	
	/// HEADERS ////
		$headers = '';
		foreach ($T_PARAMS['TABLE']['COLS'] as $index => $col_name){
			
			switch($col_name){
				case 'id':
					$headers .= <<<H
									<th scope="col">
										<div class="form-check style-check d-flex align-items-center">
											<input class="form-check-input" type="checkbox">
											<label class="form-check-label">
											{$col_name}
											</label>
										</div>
									</th>
								H;
				break;
				case 'Action':
					$headers .= '<th class="text-center" scope="col">'. $col_name . '</th>';
				break;
				
				default:
					$headers .= '<th scope="col">'. $col_name . '</th>';
					
			}
			
		}
	

	
	/// BARRA DE BUSQUEDA ///

		if ( $T_PARAMS['TABLE']['SEARCH_BAR'] == false ) { $V_SEARCH_BAR = 'd-none'; } else { $V_SEARCH_BAR = '';}


		$htmlOutput .= <<<H
						<div class="card h-100 p-0 radius-12">
						<div class="card-header border-bottom bg-base py-16 px-24 d-flex align-items-center flex-wrap gap-3 justify-content-between" style="justify-content: flex-end !important;">
							<div class="d-flex align-items-center flex-wrap gap-3 {$V_SEARCH_BAR}">
								<span class="text-md fw-medium text-secondary-light mb-0">Show</span>
								<select class="form-select form-select-sm w-auto ps-12 py-6 radius-12 h-40-px">
									<option>1</option>
									<option>2</option>
									<option>3</option>
									<option>4</option>
									<option>5</option>
									<option>6</option>
									<option>7</option>
									<option>8</option>
									<option>9</option>
									<option>10</option>
								</select>
								<form class="navbar-search">
									<input type="text" class="bg-base h-40-px w-auto" name="search" placeholder="Search" id="SCH_BAR_{$T_PARAMS['TABLE']['ID']}">
									<iconify-icon icon="ion:search-outline" class="icon"></iconify-icon>
								</form>
								<select class="form-select form-select-sm w-auto ps-12 py-6 radius-12 h-40-px ">
									<option>Status</option>
									<option>Active</option>
									<option>Inactive</option>
								</select>
							</div>
							<a href="" class="btn btn-primary text-sm btn-sm px-12 py-12 radius-8 d-flex align-items-center gap-2 add" {$T_PARAMS['TABLE']['ADD_NEW']['MISC_PROPS']} onclick="clearForm('FRM_1');">
								<iconify-icon icon="ic:baseline-plus" class="icon text-xl line-height-1"></iconify-icon>
								{$T_PARAMS['TABLE']['ADD_NEW']['TEXT']}
							</a>
						</div>
						<div class="card-body p-24">
							<div class="table-responsive scroll-sm">
						<table class="table bordered-table xsm-table mb-0" id="{$T_PARAMS['TABLE']['ID']}">
							<thead>
							<tr>
								<th scope="col">
								<div class="d-flex align-items-center gap-10">
								<div class="form-check style-check d-flex align-items-center">
								<input class="form-check-input radius-4 border input-form-dark" type="checkbox" name="checkbox" id="selectAll">
								</div>
								ID
								</div>
								</th>
								{$headers}
								<th scope="col" class="text-center">Action</th>
							</tr>
							</thead>
							<tbody>
						H;
	/// DATOS ///
		foreach ($DB_DATA as $row) {
		
			$htmlOutput .= '<tr>';
			$htmlOutput .= '<td>
							<div class="d-flex align-items-center gap-10">
								<div class="form-check style-check d-flex align-items-center">
									<input class="form-check-input radius-4 border border-neutral-400" type="checkbox" name="checkbox">
								</div>
								'.htmlspecialchars($row['ID']).'
							</div>
						</td>'; 

			// Loop through each key in the $T_PARAMS['DB']['COLS']_arr
			foreach ($T_PARAMS['DB']['COLS'] as $col) {
				

			 
				if (isset($row[$col]) & $col != 'ID') {
					// Append each value wrapped in <td> tags to the current row
					
						switch ($col) {
							case "AVATAR_PIC":
								//$htmlOutput .= '<td>' . '<span class="">' . htmlspecialchars($row[$col]) . '</span>' . '</td>' . "\n";
								//$NAME 	= htmlspecialchars($row[$col]);
								$IMG 	= ''; 
								$htmlOutput .= <<<NAME
														<td>
														<div class="d-flex align-items-center">
													        <img src="{$row['AVATAR_PIC']}" alt="assets/images/user-list/user-list2.png" class="w-60-px h-60-px rounded-circle flex-shrink-0 me-12 overflow-hidden" onerror="this.onerror=null;this.src='assets/images/user-list/user-list2.png';">
													        <div class="flex-grow-1">
													            <span class="text-md mb-0 fw-normal text-secondary-light"></span>
													        </div>
													    </div>
													    </td>
													NAME;

								break;

							case "STATUS":
									
									$row[$col] == 'on' ? $ELEMENTO = '<span class="bg-success-focus text-success-600 border border-success-main px-24 py-4 radius-4 fw-medium text-sm">Active</span>' : $ELEMENTO = '<span class="bg-danger-focus text-danger-600 border border-danger-main px-24 py-4 radius-4 fw-medium text-sm">Inactive</span>';
									
									
									
									$htmlOutput .= '<td class="text-center">' . '' . $ELEMENTO . '' . '</td>' . "\n";
								break;



						  default:
							//code block
							$htmlOutput .= '<td>' . '<span class="" style="">' . htmlspecialchars($row[$col]) . '</span>' . '</td>' . "\n";
						}
					
				}
				if (!isset($row[$col])){
					$htmlOutput .= '<td>' . '<span class="">' . htmlspecialchars($row[$col]) . '</span>' . '</td>' . "\n";
				}
			}

			$htmlOutput .= <<<H
							<td class="text-center">
								<div class="d-flex align-items-center gap-10 justify-content-center">

									
									<button type="button" class="bg-success-focus text-success-600 bg-hover-success-200 fw-medium w-40-px h-40-px d-flex justify-content-center align-items-center rounded-circle edit admin_edit" EDIT_ID="{$row['ID']}" {$T_PARAMS['TABLE']['ADD_NEW']['MISC_PROPS']} onclick="LOAD_ID({$row['ID']});">
										<iconify-icon icon="lucide:edit" class="menu-icon"></iconify-icon>
									</button>
									
									<button type="button" class="bg-danger-focus text-danger-600 bg-hover-danger-200 fw-medium w-40-px h-40-px d-flex justify-content-center align-items-center rounded-circle remove-item-btn delete admin_delete" onclick="DELETE_ID('{$T_PARAMS['DB']['TABLE']}', {$row['ID']}, this)">
										<iconify-icon icon="lucide:trash" class="menu-icon"></iconify-icon>
									</button>
								</div>
							</td>
							H;
			$htmlOutput .= '</tr>';
		}
	
	
	/// PAGINATION   ///
	
		$htmlOutput .= <<<H
								</tbody> 
							</table>
								</div>
									<div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mt-24 {$V_SEARCH_BAR}">
										<span>Showing 1 to 10 of 12 entries</span>
										<ul class="pagination d-flex flex-wrap align-items-center gap-2 justify-content-center">
											<li class="page-item">
												<a class="page-link bg-neutral-200 text-secondary-light fw-semibold radius-8 border-0 d-flex align-items-center justify-content-center h-32-px w-32-px text-md" href="javascript:void(0)">
													<iconify-icon icon="ep:d-arrow-left" class=""></iconify-icon>
												</a>
											</li>
											<li class="page-item">
												<a class="page-link text-secondary-light fw-semibold radius-8 border-0 d-flex align-items-center justify-content-center h-32-px w-32-px text-md bg-primary-600 text-white" href="javascript:void(0)">1</a>
											</li>
											<li class="page-item">
												<a class="page-link bg-neutral-200 text-secondary-light fw-semibold radius-8 border-0 d-flex align-items-center justify-content-center h-32-px w-32-px" href="javascript:void(0)">2</a>
											</li>
											<li class="page-item">
												<a class="page-link bg-neutral-200 text-secondary-light fw-semibold radius-8 border-0 d-flex align-items-center justify-content-center h-32-px w-32-px text-md" href="javascript:void(0)">3</a>
											</li>
											<li class="page-item">
												<a class="page-link bg-neutral-200 text-secondary-light fw-semibold radius-8 border-0 d-flex align-items-center justify-content-center h-32-px w-32-px text-md" href="javascript:void(0)">4</a>
											</li>
											<li class="page-item">
												<a class="page-link bg-neutral-200 text-secondary-light fw-semibold radius-8 border-0 d-flex align-items-center justify-content-center h-32-px w-32-px text-md" href="javascript:void(0)">5</a>
											</li>
											<li class="page-item">
												<a class="page-link bg-neutral-200 text-secondary-light fw-semibold radius-8 border-0 d-flex align-items-center justify-content-center h-32-px w-32-px text-md" href="javascript:void(0)">
													<iconify-icon icon="ep:d-arrow-right" class=""></iconify-icon>
												</a>
											</li>
										</ul>
									</div>
								</div>
							</div>
							H;
	

		$JS_SCRIPT = <<<FN

		 				<script>

							function hideRowsBasedOnInput(tableId, inputId) {
								// Get the input value and convert it to lower case for case-insensitive search
								var inputVal = $('#' + inputId).val().toLowerCase();

								// Loop through the table rows and hide/show based on the search input
								$('#' + tableId + ' tbody tr').each(function() {
									// Get the text of the row
									var rowText = $(this).text().toLowerCase();
									
									// Check if the row contains the input value
									if (rowText.includes(inputVal)) {
										$(this).show(); // Show the row if it matches
									} else {
										$(this).hide(); // Hide the row if it doesn't match
									}
								});
							}

							// Usage example:
							$('#SCH_BAR_{$T_PARAMS['TABLE']['ID']}').on('keyup', function() {
								debugger;
							    hideRowsBasedOnInput('{$T_PARAMS['TABLE']['ID']}', 'SCH_BAR_{$T_PARAMS['TABLE']['ID']}');
							});

							  $("INPUT.form-check-input").on("click", function() {
								const isChecked = $(this).is(":checked");
								$('#{$T_PARAMS['TABLE']['ID']} input[type="checkbox"]').prop("checked", isChecked);
							});
		 				</script>
		 				

		FN;

    return $htmlOutput . $JS_SCRIPT;
}


	



		$RESP['SUCCESS'] 	= 1; 
		//$RESP['PROMPT'] 	= 'Selección eliminada.'; 
		//$RESP['DATOS'] 		= THIS_FUNCTION( $DATA['T_PARAMS'] ); 
		
		//echo json_encode($RESP, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
		
		echo THIS_FUNCTION( $T_PARAMS ); 

		return;


	
?>
