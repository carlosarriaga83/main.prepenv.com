<?php
	
error_reporting(E_ALL);
ini_set('display_errors', 1);
	
	
include_once($_SERVER['DOCUMENT_ROOT'] . '/PHP/MYF1.php');





	

function GENERAR_ELEMENTO($NAME, $CATALOGO) {

    $html = '';

			$SIZE = 12;
            $REQUIRED = ' required';
			$SUB_TITULO = <<<S
							
							<label for="{$NAME}" class="form-label fw-semibold text-primary-light text-sm mb-8">Por defecto <span class="text-danger-600">*</span> </label>
							S;
			
							
            $DROPDOWN = sprintf('<select  class="form-control radius-8 form-select" name="%s" %s >',$NAME, $REQUIRED );
			$DROPDOWN .= '<option value="" selected></option>';
			
	            foreach ($CATALOGO as $UNO) {
	                // If the current value matches the value in the array, mark it as selected
	                //$selected = ($UNO['ID'] == $value) ? ' selected' : '';
	                $DROPDOWN .= '<option value="' . $UNO['ID'] . '"' . '' . '>' . $UNO['NAME'] . '</option>';
	            }
				
			
            $DROPDOWN .= '</select><br>';
			
			$DROPDOWN_WRAP = <<<D
							<div class="col-sm-{$SIZE}  px-1">
								<div class="mb-20">
								{$SUB_TITULO}
								{$DROPDOWN}
								</div>
							</div>
							D;

            $html .= $DROPDOWN_WRAP;

            

            $html .= <<<B
						<div class="col-sm-1 d-flex justify-content-center">
							<div class="d-flex align-items-center gap-10 justify-content-center">
								<button type="button" class="bg-danger-focus text-danger-600 bg-hover-danger-200 fw-medium w-40-px h-40-px d-flex justify-content-center align-items-center rounded-circle remove-item-btn" onclick="">
									<iconify-icon icon="lucide:trash" class="menu-icon"></iconify-icon>
								</button>
							</div>
						</div>
						B;

    return $html;

}
	
    
	$q = sprintf("SELECT * FROM PRODUCTS" );  // echo $q . "\n";
	$R1 = SQL_2_OBJ_V2($q);
	//print_r($R1);die;
	
	$CATALOGO = $R1['PL']; //print_r($DB_DISH_CATALOGO);die;


	echo GENERAR_ELEMENTO( 'TEST_EL', $CATALOGO); 
	//return GENERAR_ELEMENTO($DATA['idx'], $CATALOGO); 
		
	//echo json_encode($RESP, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
		
	//return;


	
?>
