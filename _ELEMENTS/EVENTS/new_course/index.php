<?php
	
error_reporting(E_ALL);
ini_set('display_errors', 1);
	
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



	

function GENERAR_BLANK_EVENT_COURSE($INDEX, $DB_DISHES) {

    $html = '';

    $T = $INDEX;


    for ($i = 0; $i <= 4; $i++){

    	$key = sprintf('T%s_O%s', $T, $i);

        // Check if the key starts with 'T' and contains '_O'
        if (strpos($key, 'T') === 0 && strpos($key, '_O') !== false) {
            // Split the key into two parts: 'T' and 'O'
            list($t, $o) = explode('_O', $key);

            // If 'O' is '0', add a label to the html
            if ($o == '0') {

				$SIZE = 3;
				$REQUIRED = ' required';
				
				$html .= sprintf('</div><div id="COURSE" x="%s" class="row">', $t );

				$TITULO = <<<T
							
								<div class="col-sm-12">
									<label for="{$key}" class="form-label fw-semibold text-primary-light text-sm mb-8">Tiempo {$t}<span class="text-danger-600"></span> </label>
								</div>
							T;
							
                $html .= $TITULO;
                
				
				$SUB_TITULO = <<<S
								
								<label for="{$key}" class="form-label fw-semibold text-primary-light text-sm mb-8">Por defecto <span class="text-danger-600">*</span> </label>
								S;
            }else{
				$SIZE = 2;
				$REQUIRED = '';
				$SUB_TITULO = <<<S
								<label for="{$key}" class="form-label fw-medium text-primary-light text-sm mb-8">Opción {$o} <span class="text-danger-600"></span> </label>
								S;			
			}
			


            
            $DROPDOWN = sprintf('<select  class="form-control radius-8 form-select" name="%s" %s >',$key, $REQUIRED );
			$DROPDOWN .= '<option value="" selected></option>';
			
	            foreach ($DB_DISHES as $DISH) {
	                // If the current value matches the value in the array, mark it as selected
	                //$selected = ($DISH['ID'] == $value) ? ' selected' : '';
	                $DROPDOWN .= '<option value="' . $DISH['ID'] . '"' . '' . '>' . $DISH['NAME'] . '</option>';
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

            
        }
    }
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
	
    
	$q = sprintf("SELECT * FROM Dishes " );  // echo $q . "\n";
	$R1 = SQL_2_OBJ_V2($q);
	//print_r($R1);die;
	
	$DB_DISH_CATALOGO = $R1['PL']; //print_r($DB_DISH_CATALOGO);die;




		$RESP['SUCCESS'] = 1; 
		//$RESP['PROMPT'] = 'Selección eliminada.'; 
		$RESP['DATOS'] = GENERAR_BLANK_EVENT_COURSE($DATA['idx'], $DB_DISH_CATALOGO); 
		
		echo json_encode($RESP, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
		
		return;


	
?>
