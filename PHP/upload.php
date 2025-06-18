
<?php
session_start();

setlocale(LC_ALL, 'en_US.UTF-8');

//if ($_SESSION["nivel"] != 0){echo 'Usuario no permitido para subir archivos.'; die;}

ini_set("allow_url_fopen", true);


	
	$RESP['DATA'] = $_POST['SENDER'];
	
	$SENDER_EL 	=  $_POST['SENDER'];
	$INTO_ID 	=  $_POST['ID'];

	$RESP['ERR'] = '';

// FOLDER DE SUBIDA

	$upload_folder = "../images" . "/tmp" . "/" . session_id();
		
		
// QUIEN LO ENVIA
	if ( $SENDER_EL == 'AVT_PIC_FILE_EVENT' ){ $upload_folder = sprintf('../images/EVENTS' );  $ARR_FILE_TYPES = ['jpg','png', 'JPG', 'PNG', 'jpeg', 'JPEG']; }
	if ( $SENDER_EL == 'AVT_PIC_FILE_DISH'  ){ $upload_folder = sprintf('../images/DISHES' );  $ARR_FILE_TYPES = ['jpg','png', 'JPG', 'PNG', 'jpeg', 'JPEG']; }
	if ( $SENDER_EL == 'imageUpload' 		){ $upload_folder = sprintf('../CLIENTS/users' );  $ARR_FILE_TYPES = ['jpg','png', 'JPG', 'PNG', 'jpeg', 'JPEG']; }



// CREA FOLDER DE SUBIDA

	if(!file_exists($upload_folder)) {
		//echo 'Creando: ' . $upload_folder . "\r\n"; 
		mkdir($upload_folder,0777,true);
	}



// NOMBRE DEL ARCHIVO

	$ORIGINAL_FILE_NAME = basename($_FILES["file"]["name"]);

	$imageFileType = strtolower(pathinfo($ORIGINAL_FILE_NAME,PATHINFO_EXTENSION));

	$target_dir = $upload_folder;
	
	//$target_file = $target_dir . '/' . basename($_FILES["file"]["name"]);  									// SAVE WITH ORIGINAL NAME
	//$target_file = $target_dir . '/' . $_SESSION['USER']['ID'] . '_Avatar.' . $imageFileType;   				// SAVE WITH CUSTOM NAME
	//$target_file = sprintf('%s/%s_Avatar.%s', $target_dir , $_SESSION['USER']['ID'] , $imageFileType);   		// SAVE WITH CUSTOM NAME
	//$target_file = sprintf('%s/%s', $target_dir , $ORIGINAL_FILE_NAME);   										// SAVE WITH ORIGINAL
	
	if ( $SENDER_EL == 'imageUpload' 		){
		$target_file = sprintf('%s/%s_Avatar.%s', $target_dir , $INTO_ID , $imageFileType); 
	}

	$uploadOk = 1;


	
// BORRA ARCHIVO SI EXISTE PREVIAMENTE
	
	if (file_exists($target_file)) {
		unlink($target_file); // delete file
	  //$RESP['ERR'] = "Sorry, file already exists" . "\r\n";
	  //$uploadOk = 0;
	}
	

// BORRA TODOS LOS ARCHIVOS ANTERIORES DENTRO DEL FOLDER
	/*
	$files = glob($target_dir . '/' . '*'); // get all file names
	foreach($files as $file){ // iterate files
	  if(is_file($file))
		unlink($file); // delete file
	}
	*/

// Check file size
	if ($_FILES["file"]["size"] > 50000000) {
	  $RESP['ERR'] = "Sorry, your file is too large" . "\r\n";
	  $uploadOk = 0;
	}

// Allow certain file formats
	//if($imageFileType != "jpg" ) {
	if ( !in_array($imageFileType, $ARR_FILE_TYPES, true ) ) {
	  $RESP['ERR'] = "Sorry, only image files are allowed, not: " . "\r\n" . $imageFileType; 
	  $uploadOk = 0;
	}

// Check if $uploadOk is set to 0 by an error
	if ($uploadOk == 0) {
	  //$RESP['ERR'] = "Sorry, your file was not uploaded.";
	// if everything is ok, try to upload file
	} else {
		
		
		  if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
			  
			//echo "The file ". htmlspecialchars( basename( $_FILES["file"]["name"])). " has been uploaded." . " " . $target_file ;
			

			
			$JSON['path'] 	= $target_file;
			$JSON['name'] 	= basename($target_file);
			//$JSON['id'] 	= $_REQUEST['id']; 
			//$JSON['id_E'] 	= $_REQUEST['id_E']; 
			//echo 'id ' . $JSON['id'];
			
			//$db_login = mysqli_connect('localhost', 'u124132715_paradise' ,'Paradise1!.', 'u124132715_paradise'); 
			//$q = sprintf("UPDATE u124132715_semaforo.Documentos SET path='%s' WHERE id = '%s' ", $JSON['path'], ltrim($JSON['id'], 'D') );
			//echo $q;
					   
					  
			//$result = mysqli_query($db_login, $q);
			
			//$db_login -> close(); die;
			
			
			
		  } else {
			//echo "Sorry, there was an error uploading your file.";
		  }
	}
	
	
	$RESP['OK'] = $uploadOk;
	//$RESP['ERR'] = '';
	//$RESP['PATH'] = $target_file;
	$RESP['PATH'] =  str_replace('../','../ADMIN/', $target_file) . '?a='GET_RND();
	$RESP['FILES'] = $_FILES;
	$RESP['FILE_NAME'] = basename($target_file);
	$RESP['DATA'] = $_POST['SENDER'];
	
	//renameFile('oldfile.txt', 'newfile.txt');

	echo json_encode($RESP);
		
		
function renameFile($oldName, $newName) {
    if (file_exists($oldName)) {
        if(rename($oldName, $newName)) {
            //echo "File renamed successfully";
			return true;
        } else {
            //echo "Error renaming file";
			return false;
        }
    } else {
        //echo "The file $oldName does not exist";
		return false;
    }
}

die;

?>

