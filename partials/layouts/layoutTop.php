<!-- meta tags and other links -->
<!DOCTYPE html>
<html lang="en" data-theme="light">

<?php 
	
	session_start();

	if ($_SESSION['LOGIN'] == 1){
		//header("Location: my-events.php");	
	}else{
		header("Location: sign-in.php");	
	}

	include_once($_SERVER['DOCUMENT_ROOT'] . '/PHP/MYF1.php');
	
	$q = sprintf("SELECT * FROM Users WHERE id = '%s'   ", $id );  // echo $q . "\n";
	$R1 = SQL_2_OBJ_V2($q);
	//print_r($R1);die;
	
	$USER 	= $R1['PL'][0];


	$COMPANY['ID'] = '1';
    
	$q = sprintf("SELECT * FROM COMPANY WHERE id = '%s'   ",$COMPANY['ID'] );  // echo $q . "\n";
	$R1 = SQL_2_OBJ_V2($q);
	//print_r($R1);die;
	
	$COMPANY 	= $R1['PL'][0];
	
	header('Content-type: html; charset=utf-8');
	
?>


<?php include './partials/head.php' ?>

<body>
	
	
	
		<?php include './partials/mysidebar.php' ?>

		<main class="dashboard-main">
		


			<div id="DIV_LOADING" class="modal" style="z-index: 1000;">
				<div class="modal-content">
					<span class="close" id="modalClose">&times;</span>
					<h2>Loading, please wait...</h2>
				</div>
			</div>
			
			<?php include './partials/navbar.php' ?>
