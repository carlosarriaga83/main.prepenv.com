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


function queryToJson($dsn, $username, $password, $sql, $params = []) {
    try {
        // Create a new PDO instance
        $pdo = new PDO($dsn, $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Prepare and execute the SQL statement
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        // Fetch all results as an associative array
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
		
		return $results;
        // Convert the results to JSON
        return json_encode($results, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        return json_encode($results, TRUE);
		
    } catch (PDOException $e) {
        // Handle error (you can log it or return a specific error message)
		return ['error' => 'Database error: ' . $e->getMessage()];
        return json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
}




function THIS_FUNCTION( $T_PARAMS ) {
	
	/*
	// Example usage:
	//$dsn = 'mysql:host=localhost;dbname=u124132715_semaforo;charset=utf8';
	$dsn = 'mysql:host=localhost;dbname=u124132715_curso;charset=utf8';
	$username = 'u124132715_sa';
	$password = 'Pluma123.';
	$sql = 'SELECT * FROM licencias WHERE id >= :id';
	$params = [':id' => 0 ];

	$jsonResult = queryToJson($dsn, $username, $password, $sql, $params);
	//echo $jsonResult;die;
	$DB_DATA = $jsonResult;
	//echo $DB_DATA.
	//print_r($DB_DATA);die;
	
	*/

	$q = sprintf("SELECT * FROM %s", $T_PARAMS['DB']['TABLE']);  //echo $q . "\n";
	$R1 = SQL_2_OBJ_V2($q, 2);
	$RESP['R1'] = $R1; //print_r($R1);
	$DB_DATA = $R1['DATA'];

	//print_r($DB_DATA);die;

	
	//include_once($_SERVER['DOCUMENT_ROOT'] . '/API/STRIPE/repo.php');
	
	//$CLIENTS_STRIPE = listClients();
	//$CLIENTS_STRIPE = json_decode($CLIENTS_STRIPE, true);
	
	//$DB_DATA = $CLIENTS_STRIPE['data'];
	
	
	/// HEADERS ////
	
	
		$headers = '';
		foreach ($T_PARAMS['TABLE']['COLS'] as $index => $col_name){
		
			$FIXED_HEAD = 'style=" position: sticky; top: 0; z-index: 1;"';
			
			switch($col_name){
				
				case 'id1':
					$headers .= <<<H
									<th scope="col" $FIXED_HEAD" class="">
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
					$headers .= "<th class='text-center col-1' scope='col' $FIXED_HEAD>" . $col_name . "</th>";
				break;
				
				case 'DATA':
					$headers .= "<th class='col-11' scope='col' $FIXED_HEAD>" . 'Datos' . "</th>";
				break;
				
				default:
					$headers .= "<th class='' scope='col' $FIXED_HEAD>" . $col_name . "</th>";
					
			}
			
		}

	
	/// BARRA DE BUSQUEDA ///

		if ( $T_PARAMS['TABLE']['SEARCH_BAR'] == false ) { $V_SEARCH_BAR = 'd-none'; } else { $V_SEARCH_BAR = '';}

		$htmlOutput .= <<<H
						<div class="card h-100 p-0 radius-12">
						<div class="card-header border-bottom bg-base py-4 px-24 d-flex align-items-center flex-wrap gap-3 justify-content-between" style="justify-content: flex-end !important;">
							<div class="d-flex align-items-center flex-wrap gap-3 {$V_SEARCH_BAR}">
								<span class="text-md fw-medium text-secondary-light mb-0 d-none">Show</span>
								<select class="form-select form-select-sm w-auto ps-12 py-6 radius-12 h-40-px d-none">
									<option>5</option>
									<option>10</option>
									<option>15</option>
									<option>50</option>

								</select>
								<form class="navbar-search">
									<input type="text" class="bg-base h-40-px w-auto" name="search" placeholder="Search" id="SCH_BAR_{$T_PARAMS['TABLE']['ID']}">
									<iconify-icon icon="ion:search-outline" class="icon"></iconify-icon>
								</form>
								<select class="form-select form-select-sm w-auto ps-12 py-6 radius-12 h-40-px">
									<option>All</option>
									<option>Active</option>
									<option>Desactivated</option>
									<option>Archived</option>
								</select>
							</div>
							<a href="{$T_PARAMS['TABLE']['ADD_NEW']['href']}" class="btn btn-primary text-sm btn-sm px-12 py-12 radius-8 d-flex align-items-center gap-2 add">
								<iconify-icon icon="ic:baseline-plus" class="icon text-xl line-height-1"></iconify-icon>
								{$T_PARAMS['TABLE']['ADD_NEW']['TEXT']}
							</a>
						</div>
						<div class="card-body p-24  ">
						<div class="table-responsive scroll-m max-h-612-px overflow-y-auto">
						<table class="table bordered-table xsm-table mb-0 " id="{$T_PARAMS['TABLE']['ID']}" style="min-width: auto !important;">
							<thead>
							<tr>
								{$headers}
								
							</tr>
							</thead>
							<tbody>
						H;
	
	
	
	
	/// DATOS ///
	
	
	
		foreach ($DB_DATA as $row) {
		
			$htmlOutput .= '<tr class="text-sm">';

			// Loop through each key in the $T_PARAMS['DB']['COLS']_arr
			foreach ($T_PARAMS['DB']['COLS'] as $col) {
				

			
				if (true ) {
					// Append each value wrapped in <td> tags to the current row
					
						switch ($col) {
							case 'ID1':
								$HTML_ID = htmlspecialchars($row['id']);
								$htmlOutput .= <<<NAME
														<td>
															<div class="d-flex align-items-center gap-10">
																<div class="form-check style-check d-flex align-items-center">
																	<input class="form-check-input radius-4 border border-neutral-400" type="checkbox" name="checkbox">
																</div>
																{$HTML_ID}
															</div>
														</td>
													NAME;
							
							break;
							
							case 'id1':
								$HTML_ID = htmlspecialchars($row['id']);
								$htmlOutput .= <<<NAME
														<td>
															<div class="d-flex align-items-center gap-10">
																<div class="form-check style-check d-flex align-items-center">
																	<input class="form-check-input radius-4 border border-neutral-400" type="checkbox" name="checkbox">
																</div>
																{$HTML_ID}
															</div>
														</td>
													NAME;
							
							break;

							case "empresa": 
								$htmlOutput .= <<<NAME
														<td class="   ">
															<ul class="  radius-8">
																<li class="list-group-item align-items-top justify-content-between  text-secondary-light p-2 bg-base">
																	
																	<div class="sin-wrap align-items-top text-lg fw-bold ">{$row['empresa']}</div>
																	
																	<div class="sin-wrap align-items-top ">{$row['sucursal']}</div>
																	<div class="sin-wrap align-items-top ">{$row['region']}</div>

																		
																																		
																</li>

															</ul>

														</td>
													NAME;
							break;
							
							

							case "consumo":
								$consumo =  ($row['usadas'] / $row['unidades'])*100;
								$bar_color = 'primary';
								$consumo >= 33 ? $bar_color = 'primary' : $bar_color = $bar_color;
								$consumo >= 66 ? $bar_color = 'primary' : $bar_color = $bar_color;
								$consumo >= 100 ? $bar_color = 'success' : $bar_color = $bar_color;
								
								$porcentaje_consumo = number_format((float)$consumo, 1, '.', '');
								$htmlOutput .= <<<NAME
														<td class="  ">
															<div class="  gap-2 w-100">
																<div class="w-100  ">
																	<div class="progress progress-sm rounded-pill" role="progressbar" aria-label="Success example" aria-valuenow="{$porcentaje_consumo}" aria-valuemin="0" aria-valuemax="100">
																	
																		<div class="progress-bar bg-{$bar_color}-600 rounded-pill" style="width: {$porcentaje_consumo}%;"></div>
																																				
																	</div>
																	<div class="d-flex align-items-center justify-content-between gap-2 w-100">
																	
																		<span class="w-66 text-secondary-light font-xs sin-wrap">{$row['usadas']} / {$row['unidades']} </span>
																		<span class=" text-secondary-light font-xs sin-wrap"> {$porcentaje_consumo}%</span>
																	</div>
																</div>
																
															</div>
														</td>
													NAME;
							
							break;
							
							case "inicia":
								$htmlOutput .= '<td>' . '<span class="sin-wrap">' . htmlspecialchars(date("Y-m-d",strtotime($row[$col]))) . '</span>' . '</td>' . "\n";
							
							break;
							
							case "expira":
								$htmlOutput .= '<td>' . '<span class="sin-wrap">' . date("Y-m-d",strtotime($row[$col])) . '</span>' . '</td>' . "\n";
							
							break;
							
							case "NAME":
							$htmlOutput .= '<td>' . '<span class="">' . htmlspecialchars($row[$col]) . '</span>' . '</td>' . "\n";
							
							/*
							$NAME 	= htmlspecialchars($row[$col]);
							$IMG 	= ''; 
							$htmlOutput .= <<<NAME
													<td>
													<div class="d-flex align-items-center">
												        <img src="{$row['AVATAR_PIC']}" alt="assets/images/user-list/user-list2.png" class="w-40-px h-40-px rounded-circle flex-shrink-0 me-12 overflow-hidden" onerror="this.onerror=null;this.src='assets/images/user-list/user-list2.png';">
												        <div class="flex-grow-1">
												            <span class="text-md mb-0 fw-normal text-secondary-light">{$NAME}</span>
												        </div>
												    </div>
												    </td>
												NAME;
							*/

							break;
						
							case "DATA":
								//$htmlOutput .= '<td>' . '<span class="text-titulo">' . htmlspecialchars($row[$col]) . '</span>' . '</td>' . "\n";

								
								$htmlOutput .= <<<NAME
														<td class="   ">
															<ul class="  radius-8">
																<li class="list-group-item d-flex align-items-top justify-content-between  text-secondary-light p-2 bg-base gap-3">
																	
																	<div class="-flex align-items-top gap-2 fw-semibold w-50">{$row['name']}</div>
																	
																	<div class="-flex align-items-top gap-2 w-50">{$row['address']['line1']}</div>
																	<div class="-flex align-items-top gap-2 w-50">{$row['id']}</div>
																	<div class="-flex align-items-top gap-2 w-50">{$row['phone']}</div>
																		
																																		
																</li>

															</ul>

														</td>
													NAME;

							break;
							
							case "Action":

								/// BOTONES   ///
								
								$htmlOutput .= <<<H
												<td class="text-center ">
													<div class="d-flex align-items-center gap-10 justify-content-center">
														<button type="button" class="bg-info-focus bg-hover-info-200 text-info-600 fw-medium w-40-px h-40-px d-flex justify-content-center align-items-center rounded-circle" data-bs-toggle="modal" data-bs-target="#Modal_1" onclick="LOAD_ID({$row['ID']});">
															<iconify-icon icon="majesticons:eye-line" class="icon text-xl"></iconify-icon>
														</button>

															<button type="button" class="bg-success-focus text-success-600 bg-hover-success-200 fw-medium w-40-px h-40-px d-flex justify-content-center align-items-center rounded-circle edit admin_edit" EDIT_ID="{$row['id']}" {$T_PARAMS['TABLE']['ADD_NEW']['MISC_PROPS']} onclick="LOAD_ID({$row['ID']});">
																<iconify-icon icon="lucide:edit" class="menu-icon"></iconify-icon>
															</button>

														
															<button type="button" class="bg-danger-focus text-danger-600 bg-hover-danger-200 fw-medium w-40-px h-40-px d-flex justify-content-center align-items-center rounded-circle remove-item-btn delete admin_delete" onclick="DELETE_ID('{$T_PARAMS['DB']['TABLE']}', {$row['id']}, this)">
																<iconify-icon icon="lucide:trash" class="menu-icon"></iconify-icon>
															</button>
													</div>
												</td>
												H;

							break;
							
							default:
								

								if (isset($row[$col])){
									$htmlOutput .= '<td>' . '<span class="sin-wrap">' . $row[$col] . '</span>' . '</td>' . "\n";
									
								}else{
									$htmlOutput .= '<td>' . '<span class="sin-wrap">' . htmlspecialchars('') . '</span>' . '</td>' . "\n";
								
								}
						}
					

				}
			} 

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
	
		
		/// JAVA SCRIPT
		
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
