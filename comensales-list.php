<?php $script = <<<HG
	
					<script>
							

								
					</script>
				HG;

?>



<?php include './partials/layouts/layoutTop.php' ?>






<?php
	
	//error_reporting(E_ALL);
	//ini_set('display_errors', 1);
	
	session_start();
	
	//if ($_SESSION['LOGIN'] != 1){ header("Location: index.php");	}
	
	include_once './PHP/MYF1.php';
	
	

	$q = sprintf("SELECT * FROM ROLES " );  // echo $q . "\n";
	$R1 = SQL_2_OBJ_V2($q);
	//print_r($R1);die;
	
	$ROLES 	= $R1['PL'];
    
    foreach($ROLES as $ROL){
        $ROL_ID_NAME[$ROL['ID']] = $ROL['NAME'];
    }
	
?>    
    
    
        <div class="dashboard-main-body">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
                <h6 class="fw-semibold mb-0">Lista de Comensales</h6>
                <ul class="d-flex align-items-center gap-2">
                    <li class="fw-medium">
                        <a href="index.php" class="d-flex align-items-center gap-1 hover-text-primary">
                            <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                            Dashboard
                        </a>
                    </li>
                    <li>-</li>
                    <li class="fw-medium">Lista de Comensales</li>
                </ul>
            </div>

			<?php 
				$T_PARAMS                   = [];
				$T_PARAMS['DB']['TABLE']    = 'SELECTIONS';
				$T_PARAMS['DB']['COLS']     = ['ID', 'NAME', 'USER', 'E_ID','Action'];
				
				$T_PARAMS['TABLE']['ID']    = 'TBL_USERS';
				$T_PARAMS['TABLE']['COLS']  = ['id', 'Nombre', 'Email','Evento', 'Action'];
				$T_PARAMS['TABLE']['href']  = 'view-profile.php';

				$T_PARAMS['TABLE']['SEARCH_BAR']	 		= true;
				//$T_PARAMS['TABLE']['HEADERS']['PRIORITY']	= ['NAME', 'Action'];
				$T_PARAMS['TABLE']['ADD_NEW']['TEXT'] 		= 'Add new';
				$T_PARAMS['TABLE']['ADD_NEW']['href'] 		= 'view-profile.php?id=';
				
				
				include './_ELEMENTS/COMENSALES/LIST/index.php';

				
			?>
        </div>

<?php include './partials/layouts/layoutBottom.php' ?>