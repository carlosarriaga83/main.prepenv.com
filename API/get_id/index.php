<?php
	//error_reporting(E_ALL);
	//ini_set('display_errors', 1);
	
	session_start();
	

	setlocale(LC_ALL, 'en_US.UTF-8');
	header('Content-type: text/javascript; charset=utf-8');
		
	include_once($_SERVER['DOCUMENT_ROOT'] . '/PHP/MYF1.php');
	//include_once('../ADMIN/PHP/MYF1.php');
	//include_once '../PHP/MYF1.php';
	
	
	$TABLE 	= $_GET['table'];
	$DB_ID 	= $_GET['DB_ID'];

	$ID     = GET_AN_ID($TABLE, $DB_ID);

	
	
	//echo $_GET['table']; echo GET_TS();die;

	$ARR_OUT['ID'] = $ID;
		

	
	$JSON_OUT = json_encode($ARR_OUT, true || JSON_PRETTY_PRINT);
	echo $JSON_OUT;
	return; 

 
?>