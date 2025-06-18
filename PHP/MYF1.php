<?php
	
	//error_reporting(E_ALL);
	//ini_set('display_errors', 1);

setlocale(LC_ALL, 'en_US.UTF-8');
header('Content-type: text/javascript; charset=utf-8');




include_once ($_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php');



use PhpOffice\PhpSpreadsheet\IOFactory;
//use Twilio\Rest\Client; 
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


ob_implicit_flush();


/*///////////////////////////////////////////////////////////////////////////////////////////////////////////////

AGREGA UN LINK PERZONALIZADO AL HTACCESS Y REDIRIJE EN TRAFICO AL LUGAR DESEADO CON PARAMETROS
		
		
		Options +FollowSymLinks
		 RewriteEngine On
		 RewriteRule ^carlos.*$ https://sellingparadise.turniot.xyz/property-halfmap-grid.html?s_id=4167682436 [R=301,L]
		 
		 
		$A = 'karla';
		$B = 'property-halfmap-grid.html?s_id=6476481539';
		ADD_CUSTOM_LINK($A,$B);
		
*///////////////////////////////////////////////////////////////////////////////////////////////////////////////

function ADD_CUSTOM_LINK($custom_link_name,$after_slash){
	
	DELETE_RULE($custom_link_name);
	
	$rule = sprintf('RewriteRule ^%s.*$ https://%s/%s [R=301,L]', $custom_link_name,$_SERVER['SERVER_NAME'], $after_slash );
	//$htaccess = file_get_contents('../.htaccess');
	$htaccess = file_get_contents( $_SERVER['DOCUMENT_ROOT'] . '/.htaccess');
	$htaccess = str_replace('###CUSTOM RULES###', $rule."\n###CUSTOM RULES###", $htaccess);
	file_put_contents($_SERVER['DOCUMENT_ROOT']. '/.htaccess', $htaccess);
	
	$sharable_link = sprintf('https://%s/%s',$_SERVER['SERVER_NAME'], $custom_link_name );
	return $sharable_link;
} 




/*///////////////////////////////////////////////////////////////////////////////////////////////////////////////

CONVIERTE UN PDF EN IMAGENES INDIVIFUALES
			
			//composer require phpoffice/phpspreadsheet
			
			use PhpOffice\PhpSpreadsheet\IOFactory;
			
			$origen = '../REPO/PDFs/test.pdf';
			$destino = '../REPO/IMAGES';
			PDF_2_JPJ($origen, $destino);

*///////////////////////////////////////////////////////////////////////////////////////////////////////////////


 function PDF_2_JPG($pdfPath, $outputPath) {
 
	//echo sprintf('PDF TO JPG %s ORIGEN: %s %s DESTINO: %s ' , PHP_EOL,$pdfPath,PHP_EOL, $outputPath);
		
	 if (!extension_loaded('imagick')) {
        throw new Exception("Imagick extension is not loaded");
    }
	
	if ( !file_exists($pdfPath)) { echo sprintf('File does not exist %s',$pdfPath); return;}
	
	if (!is_dir($outputPath)) { echo sprintf('Desstination folder does not exist %s',$outputPath); return;}

    $imagick = new Imagick();	echo 'plugin OK' . "\n";
    $imagick->setResolution(300, 300); // Set the resolution (quality) of the output image
    $imagick->readImage($pdfPath);
	$imagick->flattenImages(); 
	

    
    foreach($imagick as $i=>$imagickPage){
	
		flush(); 
        // Set image format and compression
        $imagickPage->setImageFormat('jpeg');
        $imagickPage->setImageCompression(imagick::COMPRESSION_JPEG);
        $imagickPage->setImageCompressionQuality(90);

        // Write image to the output directory
        $outputFilename = $outputPath . "/page_" . $i . ".jpg";
		echo $outputFilename . "\n";
        if (file_exists($outputFilename)) {
            unlink($outputFilename);
        }
        $imagickPage->writeImage($outputFilename);
    }
    
    $imagick->clear();
    $imagick->destroy();
}

/*///////////////////////////////////////////////////////////////////////////////////////////////////////////////
	
CONVIERTE UN PDF EN IMAGENES INDIVIFUALES
		
		//composer2 require phpoffice/phpspreadsheet
		
		require '../vendor/autoload.php';

		use PhpOffice\PhpSpreadsheet\IOFactory;

		$filePath = '../PHP/TemplateProps.xlsx';
		$JSON_OBJ = excelToJson1($filePath);
		print_r($JSON_OBJ);
			
*///////////////////////////////////////////////////////////////////////////////////////////////////////////////




function EXCEL_2_JSON($filePath) {
    $spreadsheet = IOFactory::load($filePath);
    $worksheet = $spreadsheet->getActiveSheet();
    $rows = $worksheet->toArray();
    $keys = $rows[0];
    unset($rows[0]);
    
    $data = [];
    foreach ($rows as $row) {
	

        $data[] = array_combine($keys, $row);
    }
    
    return json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
}

function EXCEL_2_ARR($filePath) {
    $spreadsheet = IOFactory::load($filePath);
    $worksheet = $spreadsheet->getActiveSheet();
    $rows = $worksheet->toArray();
    $keys = $rows[0];
    unset($rows[0]);
    
    $data = [];
    foreach ($rows as $row) {
        $data[] = array_combine($keys, $row);
    }
    
    return $data;
	}

/*///////////////////////////////////////////////////////////////////////////////////////////////////////////////

ENVIA SMS
			//composer2 require twilio/sdk

			use Twilio\Rest\Client;
			
			SEND_SMS('14167682436', 'TEST');
			
*///////////////////////////////////////////////////////////////////////////////////////////////////////////////

//SEND_SMS('14167682436', 'TEST');

function SEND_SMS($to_number, $txt_sms){
	
	$to_number = str_replace('+', '', $to_number);
	//echo sprintf('Enviando a: %s TEXTO: %s', $to_number, $txt_sms);
	$twilio_account_sid = "ACb40e950e1830290bdf408b07ea076319";
	$twilio_auth_token = "854c464cc2d78c76a43a1f42ddb36ff8";
	$twilio_phone_number = "+16316417869";
	$to_number_new = sprintf('+%s',$to_number);

	$client = new Client($twilio_account_sid, $twilio_auth_token);
	
	$client->messages->create(
		// Where to send a text message (your cell phone?)
		$to_number_new ,
		array(
			'from' => $twilio_phone_number,
			'body' => $txt_sms
		)
	);
	
	return true;
	//echo 'ok';
}


/*///////////////////////////////////////////////////////////////////////////////////////////////////////////////

MOVE FILE
			
			$source = '/path/to/source/file.txt';
			$destination = '/path/to/destination/file.txt';
			
*///////////////////////////////////////////////////////////////////////////////////////////////////////////////

 

function MOVE_FILE($source, $destination) {
    // Check if source file exists
    if (!file_exists($source)) {
        return false;
    }

    // Get the destination directory
    $destinationDir = dirname($destination);

    // Check if destination directory exists, if not create it
    if (!is_dir($destinationDir)) {
        mkdir($destinationDir, 0777, true);
    }

    // Move the file
	if (!file_exists($source)) {
		//echo "File does not exist.";
		return false;
	}
	if (file_exists($destination)) {
		//echo "File with the new name already exists.";
		unlink($destination);
		//return false;
	} 
	
	if (rename($source, $destination)) {
		//echo "File renamed successfully.";
		return true;
	} else {
		return false;
	}

}

function MOVE_RENAME_FILE($source, $destination, $newName) {
    // Check if source file exists
    if (!file_exists($source)) {
        return false;
    }

    // Get the destination directory
    $destinationDir = dirname($destination);

    // Check if destination directory exists, if not create it
    if (!is_dir($destinationDir)) {
        mkdir($destinationDir, 0777, true);
    }
	

	// Construct new file path
	$newFilePath = $destination . '/' . $newName;
	$newFilePathDir = dirname($newFilePath);
	
	
    if (!is_dir($newFilePathDir)) {
        mkdir($newFilePathDir, 0777, true);
    }
	
	
	if (file_exists($newFilePath)) {
		//echo "File with the new name already exists.";
		unlink($newFilePath);
		//return false;
	} 
	
	$TMP['source'] = $source;
	$TMP['destination'] = $destination;
	$TMP['newFilePath'] = $newFilePath;
	
	//print_r($TMP);die;
	
	// Move the file
	if (rename($source, $newFilePath)) {
		//echo "File moved and renamed successfully.";
		return true;
	} else {
		//echo "Failed to move and rename file.";
		return false;
	}

}


function COPY_RENAME_FILE($source, $destination, $newName) {
    // Check if source file exists
	
	//echo $source . "\n";	echo $destination. "\n";	echo $newName . "\n";	die; 
	

	
	if ($source == $destination){
		$TMP['OK'] 			= true;
		$TMP['newFilePath'] = $destination;
		$TMP['PROMPT'] 		= 'Same origin and destination.';
		return $TMP;
		//return true;
		
		}
	
    if (!file_exists($source)) {
	
		$TMP['OK'] 			= false;
		$TMP['PROMPT'] 		= 'Origin file does not exist.';
        return $TMP;
		//return false;
    }

    // Get the destination directory
    $destinationDir = dirname($destination. '/');
    //$destinationDir = $destination;

    // Check if destination directory exists, if not create it
    if (!is_dir($destinationDir)) {
        mkdir($destinationDir, 0777, true);
    }
	

	// Construct new file path
	$newFilePath 	= $destinationDir . '/' . $newName;
	$newFilePathDir = dirname($newFilePath);
	
	
    if (!is_dir($newFilePathDir)) {
        mkdir($newFilePathDir, 0777, true);
    }
	
	
	if (file_exists($newFilePath)) {
		//echo "File with the new name already exists.";
		unlink($newFilePath);
		//return false;
	} 
	
	$TMP['source'] 		= $source;
	$TMP['destination'] = $destination;
	$TMP['destinationDir'] = $destinationDir;
	$TMP['newFilePath'] = $newFilePath;
	
	
	//print_r($TMP);die;
	
	// Move the file
	if (copy($source, $newFilePath)) {
		//echo "File moved and renamed successfully.";
		$TMP['OK'] 			= true;
		return $TMP;
		//return true;
	} else {
		//echo "Failed to move and rename file.";
		$TMP['OK'] 			= false;
		return $TMP;
		//return false;
	}

}


function RENAME_FILE($oldName, $newName){
	
	if (!file_exists($oldName)) {
		//echo "File does not exist.";
		return false;
	} else if (file_exists($newName)) {
		//echo "File with the new name already exists.";
		return false;
	} else if (rename($oldName, $newName)) {
		//echo "File renamed successfully.";
		return true;
	} else {
		//echo "File could not be renamed.";
		return false;
	}
}





/*///////////////////////////////////////////////////////////////////////////////////////////////////////////////

WHATSAPP
			
			//composer require guzzlehttp/guzzle

			$CHAT_ID debe de ser LADA + 10 digitos
			
			WA_SEND_TXT('14167682436', 'TEST');  
			WA_SEND_FILE('14167682436', 'https://test.prepenv.com/REPO/robot.png', 'Imagen de Robot');
			
			
			
*///////////////////////////////////////////////////////////////////////////////////////////////////////////////

function CLEAN_CHAT_ID($str) {
	
	if (strpos($str, '@g.us') !== false) { return $str;  }
	
	$str= preg_replace("/[^0-9]/", "", $str);
	
    // MEXICO
    if (strpos($str, '52') === 0) {
        // Check if the string does not start with 521
        if (strpos($str, '521') !== 0) {
            // Add '1' to the string after '52'
            $str = '52' . '1' . substr($str, 2);
        }
    }
	
	// ARGENTINA
	
    if (strpos($str, '54') === 0) {
        // Check if the string does not start with 521
        if (strpos($str, '549') !== 0) {
            // Add '1' to the string after '52'
            $str = '54' . '9' . substr($str, 2);
        }
    }
	
	
	
	
    return $str;
}


function WA_SEND_TXT($CHAT_ID, $TXT){ 
	//echo '1';
	
	$CHAT_ID = CLEAN_CHAT_ID($CHAT_ID);

	$JSON['TO'] 		= $CHAT_ID;
	$JSON['TEXT'] 		= $TXT;
	$JSON['INSTANCE'] 	= '0';
	$JSON['REGISTERED'] = '0';
	$JSON['SENT'] 		= '0';
	$JSON['MSG_ID'] 	= '0';
	
	if ( WA_INSTANCE_STATUS()		== false ) { return $JSON; } else { $JSON['INSTANCE'] 	= '1'; }
	if ( WA_CHECK_EXISTE($CHAT_ID) 	== false ) { return $JSON; } else { $JSON['REGISTERED'] = '1'; }
	

	$WA_BODY = sprintf('{"message":"%s", "chatId":"%s@c.us"}', $TXT, $CHAT_ID );	
	$JSON['WA_BODY'] 	= $WA_BODY;
	
	//echo 'MENSAJE'. "\n";
	//$TXT = $JSON['WA_TXT'];
	$client = new \GuzzleHttp\Client();
	//echo 'WA_CLIENT OK'; 
	$response = $client->request('POST', 'https://waapi.app/api/v1/instances/15863/client/action/send-message', [
	  'body' => $WA_BODY,
	  'headers' => [
		'accept' => 'application/json',
		'authorization' => 'Bearer zHXEDP8kJqxtlLPtjWPkIrrQL2sfCGZPqhKjmDG3961f2855',
		'content-type' => 'application/json',
	  ],
	]);
	


	
	$PAYLOAD = (string) $response->getBody();	//print_r($USER);

	$PAYLOAD = json_decode($PAYLOAD, true);
	
	$JSON['SEND_TXT_PAYLOAD'] = $PAYLOAD;
	
	$MSG_ID = $PAYLOAD['data']['data']['id']['_serialized'];
	
	$JSON['MSG_ID'] = $MSG_ID;
	
	$STATUS = $PAYLOAD['data']['status'];

	if ( $STATUS == 'success' ) { $JSON['SENT'] = '1'; }else{ $JSON['SENT'] = '0'; } 
	
	return $JSON;
	
	//$JSON['WA_MSG_ID'] = $MSG_ID;
	
	//echo WA_CHECK_VISTO($MSG_ID);
	//echo $MSG_ID;
	
	//echo $response->getBody();
	
}

function WA_SEND_TXT_TO_GROUP($CHAT_ID, $TXT){ 
	//echo '1';
	
	//$CHAT_ID = CLEAN_CHAT_ID($CHAT_ID);

	$JSON['TO'] 		= $CHAT_ID;
	$JSON['TEXT'] 		= $TXT;
	$JSON['INSTANCE'] 	= '0';
	//$JSON['REGISTERED'] = '0';
	$JSON['SENT'] 		= '0';
	$JSON['MSG_ID'] 	= '0';
	
	if ( WA_INSTANCE_STATUS()		== false ) { return $JSON; } else { $JSON['INSTANCE'] 	= '1'; }
		

	$WA_BODY = sprintf('{"message":"%s", "chatId":"%s"}', $TXT, $CHAT_ID );	
	$JSON['WA_BODY'] 	= $WA_BODY;
	
	//echo 'MENSAJE'. "\n";
	//$TXT = $JSON['WA_TXT'];
	$client = new \GuzzleHttp\Client();
	//echo 'WA_CLIENT OK'; 
	$response = $client->request('POST', 'https://waapi.app/api/v1/instances/15863/client/action/send-message', [
	  'body' => $WA_BODY,
	  'headers' => [
		'accept' => 'application/json',
		'authorization' => 'Bearer zHXEDP8kJqxtlLPtjWPkIrrQL2sfCGZPqhKjmDG3961f2855',
		'content-type' => 'application/json',
	  ],
	]);
	


	
	$PAYLOAD = (string) $response->getBody();	//print_r($USER);

	$PAYLOAD = json_decode($PAYLOAD, true);
	
	$JSON['SEND_TXT_TO_GROUP_PAYLOAD'] = $PAYLOAD;
	
	$MSG_ID = $PAYLOAD['data']['data']['id']['_serialized'];
	
	$JSON['MSG_ID'] = $MSG_ID;
	
	$STATUS = $PAYLOAD['data']['status'];

	if ( $STATUS == 'success' ) { $JSON['SENT'] = '1'; }else{ $JSON['SENT'] = '0'; } 
	
	return $JSON;
	
	//$JSON['WA_MSG_ID'] = $MSG_ID;
	
	//echo WA_CHECK_VISTO($MSG_ID);
	//echo $MSG_ID;
	
	//echo $response->getBody();
	
}


function WA_CREATE_GROUP($GROUP_NAME, $PARTICIPANTS_PHONES, $MCA){ 
	//echo '1';
	
	foreach($PARTICIPANTS_PHONES as $PARTICIPANT){
		
		$P = CLEAN_CHAT_ID($PARTICIPANT) . '@c.us';
		
		if ( WA_CHECK_EXISTE($PARTICIPANT) == true ){ 
			$TEMP_ARR[] = '"' . $P . '"';
			}else{
			$NOT_REG_ARR[] = '"' . $P . '"';
		}
	}
	
	$PARTICIPANTS_ARR = $TEMP_ARR;
	


	$JSON['GROUP_NAME'] 			= $GROUP_NAME;
	$JSON['PARTICIPANTS_ARR'] 		= $PARTICIPANTS_ARR;
	$JSON['NOT_REG_ARR'] 			= $NOT_REG_ARR;
	$JSON['INSTANCE'] 				= '0';
	$JSON['REGISTERED'] 			= '0';
	$JSON['SENT'] 					= '0';
	$JSON['MSG_ID'] 				= '0';
	
	if ( WA_INSTANCE_STATUS()		== false ) { return $JSON; } else { $JSON['INSTANCE'] 	= '1'; }
	
	
	$WA_BODY = sprintf('{"groupName":"%s", "groupParticipants":  [%s]}', $GROUP_NAME, implode(",", $PARTICIPANTS_ARR) );
	$JSON['WA_BODY'] 	= $WA_BODY;
	


	$client = new \GuzzleHttp\Client();
	//echo 'WA_CLIENT OK'; 
	$response = $client->request('POST', 'https://waapi.app/api/v1/instances/15863/client/action/create-group', [
	  'body' => $WA_BODY,
	  'headers' => [
		'accept' => 'application/json',
		'authorization' => 'Bearer zHXEDP8kJqxtlLPtjWPkIrrQL2sfCGZPqhKjmDG3961f2855',
		'content-type' => 'application/json',
	  ],
	]);
	
	$PAYLOAD = (string) $response->getBody();	//print_r($USER);

	$PAYLOAD = json_decode($PAYLOAD, true);
	
	$JSON['PAYLOAD'] = $PAYLOAD;
	
	$GRP_ID = $PAYLOAD['data']['data']['gid']['_serialized'];
	
	$JSON['GRP_ID'] = $GRP_ID;
	
	$STATUS = $PAYLOAD['data']['status'];

	if ( $STATUS == 'success' ) { $JSON['SENT'] = '1'; }else{ $JSON['SENT'] = '0'; return $JSON;} 
	
	// MAKE ADMINS
	
	foreach($PARTICIPANTS_ARR as $P){
	
		$WA_BODY = sprintf('{"chatId":"%s", "participant": %s}', $GRP_ID, $P );
		
		$response = $client->request('POST', 'https://waapi.app/api/v1/instances/15863/client/action/promote-group-participant', [
		  'body' => $WA_BODY,
		  'headers' => [
			'accept' => 'application/json',
			'authorization' => 'Bearer zHXEDP8kJqxtlLPtjWPkIrrQL2sfCGZPqhKjmDG3961f2855',
			'content-type' => 'application/json',
		  ],
		]);
		
	}
	
	
	$PAYLOAD = (string) $response->getBody();	//print_r($USER);

	$PAYLOAD = json_decode($PAYLOAD, true);
	
	$JSON['GRP_PAYLOAD'] = $PAYLOAD;
	
	
	
	// UPDATE GROUP INFO
	
	$q = sprintf("SELECT * FROM u124132715_paradise.DEVELOPMENTS WHERE id = '%s'", $MCA);  //echo $q . "\n";
	$R1 = SQL_2_OBJ_V2($q);
	$RESP['R1'] = $R1;

	//echo json_encode($RESP); 
	$DEV_OBJ = $R1['DATA'][0]; //print_r($DEV_OBJ);
	
	$PIC_RAW = $DEV_OBJ['DEVELOPMENT']['PICS'][0]['Path'];
	$PIC_URL = preg_replace('/\.\./', 'https://micasapp.ai/', $PIC_RAW, 1);
	$PIC_URL = 'https://micasapp.ai/images/logo/Logo_new.png';
	
	$DESCRIPTION = 'Hello! 👋🏻 Welcome to your private micasapp.mx group chat! \n\n This has been created to address any questions ❓ regarding any property or for more information directly with your ✅ certified representative.';
	
	$WA_BODY = sprintf('{"chatId":"%s","description":"%s","pictureUrl":"%s"}', $GRP_ID, $DESCRIPTION , $PIC_URL );
	
	$response = $client->request('POST', 'https://waapi.app/api/v1/instances/15863/client/action/update-group-info', [
	  'body' => $WA_BODY,
	  'headers' => [
		'accept' => 'application/json',
		'authorization' => 'Bearer zHXEDP8kJqxtlLPtjWPkIrrQL2sfCGZPqhKjmDG3961f2855',
		'content-type' => 'application/json',
	  ],
	]);
		
	
	$PAYLOAD = (string) $response->getBody();	//print_r($USER);

	$PAYLOAD = json_decode($PAYLOAD, true);
	
	$JSON['GRP_INFO_PAYLOAD'] = $PAYLOAD;
	
	
	
	
	return $JSON;
	

	
}
 

//if( $JSON['WA_request'] == 2 ){

function WA_SEND_FILE($CHAT_ID, $FILE_URL, $FILE_NAME){
	//echo $CHAT_ID . "\n";
	
	$CHAT_ID = CLEAN_CHAT_ID($CHAT_ID);

	$JSON['INSTANCE'] 	= '0';
	$JSON['REGISTERED'] = '0';
	$JSON['SENT'] 		= '0';
	$JSON['MSG_ID'] 	= '0';
	
	if ( WA_INSTANCE_STATUS()		== false ) { return $JSON; } else { $JSON['INSTANCE'] 	= '1'; }
	if ( WA_CHECK_EXISTE($CHAT_ID) 	== false ) { return $JSON; } else { $JSON['REGISTERED'] = '1'; }
	
	$client = new \GuzzleHttp\Client();
	$response = $client->request('POST', 'https://waapi.app/api/v1/instances/15863/client/action/send-media', [
	  'body' => sprintf('{"chatId":"%s@c.us","mediaUrl":"%s", "mediaName":"%s" }',$CHAT_ID, $FILE_URL, $FILE_NAME ),
	  //'body' => sprintf('{"chatId":"%s","mediaUrl":"%s", "mediaName":"%s" }',$JSON['CHAT_ID'], $JSON['WA_FILE_URL'], $JSON['WA_FILE_NAME'] ),
	  'headers' => [
		'accept' => 'application/json',
		'authorization' => 'Bearer zHXEDP8kJqxtlLPtjWPkIrrQL2sfCGZPqhKjmDG3961f2855',
		'content-type' => 'application/json',
		],
	]);
	
	$PAYLOAD = (string) $response->getBody();	//print_r($USER);

	$PAYLOAD = json_decode($PAYLOAD, true);
	
	$MSG_ID = $PAYLOAD['data']['data']['id']['_serialized'];
	
	$JSON['MSG_ID'] 			= $MSG_ID;
	$JSON['WA_SEND_FILE_PL'] 	= $PAYLOAD;
	
	$STATUS = $PAYLOAD['data']['status'];

	if ( $STATUS == 'success' ) { $JSON['SENT'] = '1'; }else{ $JSON['SENT'] = '0'; }
	//print_r($JSON);
	

	return $JSON;

}

function WA_CHECK_EXISTE($CHAT_ID){
	
	//echo '2' . "\n";
	//echo $CHAT_ID . "\n";
	$CHAT_ID = CLEAN_CHAT_ID($CHAT_ID);

	$client = new \GuzzleHttp\Client();
	
		// VERIFICA SI EXISTE EL USUARIO EN WHATSSAP
	$response = $client->request('POST', 'https://waapi.app/api/v1/instances/15863/client/action/is-registered-user', [
	  'body' => sprintf('{"contactId":"%s@c.us"}', $CHAT_ID ),
	  'headers' => [
		'accept' => 'application/json',
		'authorization' => 'Bearer zHXEDP8kJqxtlLPtjWPkIrrQL2sfCGZPqhKjmDG3961f2855',
		'content-type' => 'application/json',
	  ],
	]);
	
	//echo '3' . "\n";
	
	//'body' => sprintf('{"contactId":"%s"}', $CHAT_ID ),

	//echo $response->getBody();
	 
	$USER = (string) $response->getBody();
	//print_r($USER);
	$USER = json_decode($USER, true); 
	//echo $USER['data']['data']['isRegisteredUser'];
	//die;
	if ( $USER['data']['data']['isRegisteredUser'] == false ) {
		return false; //'Usuario no registrado.'; die;
	} else{
		
		return true;
	}

}


function WA_CHECK_VISTO($MSG_ID){
	
	$client = new \GuzzleHttp\Client();
	
	
	$response = $client->request('POST', 'https://waapi.app/api/v1/instances/15863/client/action/get-message-by-id', [
	  'body' => sprintf('{"messageId":"%s@c.us"}', $MSG_ID),
	  'headers' => [
		'accept' => 'application/json',
		'authorization' => 'Bearer zHXEDP8kJqxtlLPtjWPkIrrQL2sfCGZPqhKjmDG3961f2855',
		'content-type' => 'application/json',
	  ],
	]);

	//echo $response->getBody();
	
	$PAYLOAD = (string) $response->getBody();	//print_r($USER);

	$PAYLOAD = json_decode($PAYLOAD, true);
	
	$VISTO = $PAYLOAD['data']['data']['ack']; 
	
	
	return $VISTO;
	
}




function WA_INSTANCE_STATUS(){
	
/*
	
///////// BAD
{
  "me": {
    "status": "error",
    "message": "instance not ready",
    "instanceId": "15863",
    "explanation": "instance has to be in ready status to perform this request",
    "instanceStatus": "booting"
  },
  "links": {
    "self": "https://waapi.app/api/v1/instances/15863/client/me"
  },
  "status": "success"
}	

//////// GOOD

(
    [me] => Array
        (
            [status] => success
            [instanceId] => 15863
            [data] => Array
                (
                    [displayName] => ChatBot
                    [contactId] => 5215574606871@c.us
                    [formattedNumber] => +52 1 55 7460 6871
                    [profilePicUrl] => https://pps.whatsapp.net/v/t61.24694-24/436766530_3441157376019600_6255113040182554646_n.jpg?ccb=11-4&oh=01_Q5AaIGtO9VB6I67eDyGTUr6BL-9Ae0ec8S7hrouf1XRsnJNz&oe=670AC2E8&_nc_sid=5e03e0&_nc_cat=100
                )

        )

    [links] => Array
        (
            [self] => https://waapi.app/api/v1/instances/15863/client/me
        )

    [status] => success
)

*/
	
	$client = new \GuzzleHttp\Client();

	$response = $client->request('GET', 'https://waapi.app/api/v1/instances/15863/client/me', [
	  'headers' => [
		'accept' => 'application/json',
		'authorization' => 'Bearer zHXEDP8kJqxtlLPtjWPkIrrQL2sfCGZPqhKjmDG3961f2855',
	  ],
	]);

	$PAYLOAD = (string) $response->getBody();	//print_r($USER);

	$PAYLOAD = json_decode($PAYLOAD, true);
	
	if ( $PAYLOAD['me']['status'] == 'success' ) { $STATUS = true; } else { $STATUS = false;} 
	
	return $STATUS;
	
	
}


function WA_GET_PICTURE($CHAT_ID){
	
	$CHAT_ID = CLEAN_CHAT_ID($CHAT_ID);
	
	$client = new \GuzzleHttp\Client();

	$response = $client->request('POST', 'https://waapi.app/api/v1/instances/15863/client/action/get-profile-pic-url', [
	  'body' => sprintf('{"contactId":"%s@c.us"}', $CHAT_ID),
	  'headers' => [
		'accept' => 'application/json',
		'authorization' => 'Bearer zHXEDP8kJqxtlLPtjWPkIrrQL2sfCGZPqhKjmDG3961f2855',
		'content-type' => 'application/json',
	  ],
	]);
	
	$PAYLOAD = (string) $response->getBody();	//print_r($USER);

	$PAYLOAD = json_decode($PAYLOAD, true);
	
	return $PAYLOAD['data']['data']['profilePicUrl'];
	
}

function WA_GET_NAME($CHAT_ID){
	

	
	$client = new \GuzzleHttp\Client();


	$response = $client->request('POST', 'https://waapi.app/api/v1/instances/15863/client/action/get-chat-by-id', [
	  'body' => sprintf('{"chatId":"%s@c.us"}', $CHAT_ID),
	  'headers' => [
		'accept' => 'application/json',
		'authorization' => 'Bearer zHXEDP8kJqxtlLPtjWPkIrrQL2sfCGZPqhKjmDG3961f2855',
		'content-type' => 'application/json',
	  ],
	]);
	
	$PAYLOAD = (string) $response->getBody();	//print_r($USER);

	$PAYLOAD = json_decode($PAYLOAD, true);
	
	return $PAYLOAD['data']['data']['name'];
	
}


////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function SQL_2_OBJ($q){
	
		$RESP['PROMPT'] = '';
		$RESP['DATOS'] = '';
		$RESP['ERR'] = ''; 
		$RESP['INSERTED_ID'] = '';
		$RESP['ROWS'] = '';
		$RESP['Q'] = $q;
		$RESP['OK'] = false;
		
		include 'config.php';
		$db_login = mysqli_connect($DB_CREDENTIALS['HOST'], $DB_CREDENTIALS['USER'] ,$DB_CREDENTIALS['PWD'], $DB_CREDENTIALS['DB']); 
		$result = mysqli_query($db_login, $q);
		$ERR = mysqli_error($db_login); 
		
		$RESP['ROWS'] = mysqli_num_rows($result);
		
		if($ERR != ''){ 
			$RESP['ERR'] = $ERR; 
			$db_login -> close(); 
			return json_encode($RESP); 
			
		}
		
		$RESP['INSERTED_ID'] = mysqli_insert_id($db_login);
		
		
		while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
			
			$myArr[] = json_decode($row['Datos'],true); 
			$myArr['ID'] = $row['id'];
			$myArr['TS'] = $row['TS'];
		}
		
		$RESP['DATA'] = $myArr;//json_encode($myArr);
		
		$db_login -> close(); 
		
		$RESP['OK'] = true;
		
		//return json_encode($RESP); 
		//return json_decode($RESP, true); 
		return $RESP; 
		
		
}

function SQL_2_OBJ_V2($q, $DB_INDEX = 0){
	
		$RESP['QRY']['PROMPT'] = '';
		$RESP['QRY']['DATOS'] = ''; 
		$RESP['QRY']['ERR'] = ''; 
		$RESP['QRY']['INSERTED_ID'] = '';
		$RESP['QRY']['ROWS'] = '';
		$RESP['QRY']['Q'] = $q;
		$RESP['QRY']['OK'] = false;
		
		$q = str_replace('\n', ' ', $q);
		$q = str_replace('\\n', ' ', $q);
		$q = str_replace('\r\n', ' ', $q);
		

		include 'config.php';
		
		$db_login = mysqli_connect($DB_CREDENTIALS[0]['HOST'], $DB_CREDENTIALS[0]['USER'] ,$DB_CREDENTIALS[0]['PWD'], $DB_CREDENTIALS[0]['DB']);
		
		if ($DB_INDEX != 0) {$db_login = mysqli_connect($DB_CREDENTIALS[$DB_INDEX]['HOST'],$DB_CREDENTIALS[$DB_INDEX]['USER'],$DB_CREDENTIALS[$DB_INDEX]['PWD'],$DB_CREDENTIALS[$DB_INDEX]['DB']);}
		
		//echo $DB_INDEX . "\n";
		//echo $q . "\n"; 

		$result = mysqli_query($db_login, $q);
		$ERR = mysqli_error($db_login); 
		
				
		
		if($ERR != ''){ 
			$RESP['QRY']['ERR'] = $ERR; 
			$db_login -> close(); 
			return json_encode($RESP, true); 
			
		}
		
		$RESP['QRY']['INSERTED_ID'] = mysqli_insert_id($db_login);
		

		if( str_contains($q,'SELECT')){
		
			$RESP['QRY']['ROWS'] = mysqli_num_rows($result);
			//print_r($RESP);
		
			while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
			//while ($row = mysqli_fetch_assoc($result, MYSQLI_ASSOC)) {
				$TEMP = [];
				$TEMP[0] = json_decode($row['Datos'],true);   
				$TEMP[0]['ID'] = $row['id'];
				$TEMP[0]['TS'] = $row['TS'];
				$myArr[] = $TEMP[0];
				//array_push($myArr, $TEMP[0]);
			}
			
			$RESP['DATA'] = $myArr;//json_encode($myArr);
			$RESP['PL'] = $myArr;//json_encode($myArr);
		
		}else{
			$RESP['QRY']['ROWS'] = 0;
		}
		
		$db_login -> close(); 
		
		$RESP['QRY']['OK'] = true;
		

		return $RESP; 
		
		
}

function SQL_2_OBJ_V3($q, $DB_INDEX = 0){
	
		$RESP['QRY']['PROMPT'] = '';
		$RESP['QRY']['DATOS'] = ''; 
		$RESP['QRY']['ERR'] = ''; 
		$RESP['QRY']['INSERTED_ID'] = '';
		$RESP['QRY']['ROWS'] = '';
		$RESP['QRY']['Q'] = $q;
		$RESP['QRY']['OK'] = false;
		
		include 'config.php';
		
		$db_login = mysqli_connect($DB_CREDENTIALS[0]['HOST'], $DB_CREDENTIALS[0]['USER'] ,$DB_CREDENTIALS[0]['PWD'], $DB_CREDENTIALS[0]['DB']);
		
		if ($DB_INDEX == 1) {$db_login = mysqli_connect(	$DB_CREDENTIALS[1]['HOST'],$DB_CREDENTIALS[1]['USER'],$DB_CREDENTIALS[1]['PWD'],$DB_CREDENTIALS[1]['DB']);}
		
		$result = mysqli_query($db_login, $q);
		$ERR = mysqli_error($db_login); 
		
				
		
		if($ERR != ''){ 
			$RESP['QRY']['ERR'] = $ERR; 
			$db_login -> close(); 
			return json_encode($RESP, true); 
			
		}
		
		$RESP['QRY']['INSERTED_ID'] = mysqli_insert_id($db_login);
		
		if( str_contains($q,'SELECT')){

			$RESP['QRY']['ROWS'] = mysqli_num_rows($result);
			
			if (mysqli_num_rows($result) > 0) {
				$myArr2 = array();
				while($r = mysqli_fetch_assoc($result)) {
					$myArr2[] = $r;
					//$myArr2[] = json_decode($r['Datos'],true); 
				}
				//print json_encode($rows);
			} else {
				//echo "0 results";
			}	
			
			//$RESP['OBJ'] = json_encode($myArr2,true);  
			$RESP['OBJ'] = $myArr2;  
			$RESP['PL'] = $myArr2;  
  
		}
		$db_login -> close(); 
		
		$RESP['QRY']['OK'] = true;
		

		return $RESP; 
		
		
}

function SQL_2_OBJ_V4($q){
	
		$RESP['QRY']['PROMPT'] = '';
		$RESP['QRY']['DATOS'] = ''; 
		$RESP['QRY']['ERR'] = ''; 
		$RESP['QRY']['INSERTED_ID'] = '';
		$RESP['QRY']['ROWS'] = '';
		$RESP['QRY']['Q'] = $q;
		$RESP['QRY']['OK'] = false;
		
		//$db_login = mysqli_connect('localhost', 'u124132715_paradise' ,'Paradise1!.', 'u124132715_paradise'); 
		
		
		
		
		// CONEXION A DATABASE 
		
			//$servername 	= "localhost";
			$username 		= "u124132715_paradise";
			$password 		= "Paradise1!.";
			$dbname 		= "u124132715_paradise";

			try {
			  $pdo = new PDO("mysql:host=localhost;dbname=$dbname", $username, $password);
			  // set the PDO error mode to exception
			  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			  if ($pdo) {
					//echo "Connected to the database successfully!";
					$RESP['DB']['CON'] = true;
					$RESP['DB']['ERR'] = '';
				}
			} catch(PDOException $e) {
				//echo "Connection failed: " . $e->getMessage();
				//echo 'Connection failed!<br>';
				$RESP['DB']['CON'] = false;
				$RESP['DB']['ERR'] = $e->getMessage();
			}
		
			//print_r($RESP);
		
		// EXECUTE QRY
		
			$stmt = $pdo->query($q);
			$result = $stmt->fetch();
			
					
			
			if($ERR != ''){ 
				$RESP['QRY']['ERR'] = $ERR; 
				$db_login -> close(); 
				return json_encode($RESP); 
				
			}
		
			$RESP['QRY']['ROWS'] = $result->fetchColumn();
			
			//$RESP['QRY']['INSERTED_ID'] = mysqli_insert_id($db_login);
		
		// PLAY WITH RESULT DATA
		
			//while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
			 while ($row = $stmt->fetch()){	
				$TEMP[0] = json_decode($row['Datos'],true); 
				$TEMP[0]['ID'] = $row['id'];
				$TEMP[0]['TS'] = $row['TS'];
				$myArr[] = $TEMP[0];
			}
			
			$RESP['DATA'] = $myArr;//json_encode($myArr);
		
		//$db_login -> close(); 
		
		$RESP['QRY']['OK'] = true;
		
		return json_encode($RESP); 

		return $RESP; 
		
		
}

function GENERATE_CODE() {
    return rand(1000, 9999);
}

function GET_RND() {
    return rand(1000, 9999);
}



function NUMBER_2_MONEY($str) {
    // Check if string is a number
    if(is_numeric($str)) {
        // Convert string to number format
        $formattedNum = number_format($str);
        return $formattedNum;
    } else {
        return "Invalid input. Please enter a numeric value.";
    }
}

function TEST() {
    return '1';
}




/*
$json = json_decode('{"key1":"value1","key2":{"key3":"value3","key4":"value4"}}', true);
$value = "value3";
$path = searchValueInJson($json, $value);
echo $path; // Outputs: key2.key3
*/

function FIND_IN_JSON($json, $value, $path = '') {
    foreach ($json as $key => $item) {
        if (is_array($item)) {
            $subPath = $path . '.' . $key;
            $result = searchValueInJson($item, $value, $subPath);
            if ($result) return $result;
        } else {
            if ($item === $value) {
                return $path ? $path . '.' . $key : $key;
            }
        }
    }
    return null;
}


// FECHA
function GET_TS(){
	
	
	/*
	date_default_timezone_set('America/Mexico_City');
	$current_date = date('Y-m-d H:i:s');
	$current_ts = date('Y-m-d H:i:s', strtotime($current_date . ' -1 hour')); 
	*/
	
	//date_default_timezone_set('America/Mexico_City');
	$current_date = date('Y-m-d H:i:s');
	$current_ts = date('Y-m-d H:i:s', strtotime($current_date . '+0 hour')); 
	
	
	return $current_ts;
	
}

// FECHA
function GET_MEX_TS(){
	
	
	/*
	date_default_timezone_set('America/Mexico_City');
	$current_date = date('Y-m-d H:i:s');
	$current_ts = date('Y-m-d H:i:s', strtotime($current_date . ' -1 hour')); 
	*/
	
	date_default_timezone_set('America/Mexico_City');
	$current_date = date('Y-m-d H:i:s');
	$current_ts = date('Y-m-d H:i:s', strtotime($current_date . '+0 hour')); 
	
	
	return $current_ts;
	
}
// FECHA
function GET_MEX_DATE($offset = ''){
	
	
	/*
	date_default_timezone_set('America/Mexico_City');
	$current_date = date('Y-m-d H:i:s');
	$current_ts = date('Y-m-d H:i:s', strtotime($current_date . ' -1 hour')); 
	*/
	
	date_default_timezone_set('America/Mexico_City');
	$current_date = date('Y-m-d H:i:s');
	$current_ts = date('Y-m-d', strtotime($current_date . $offset)); 
	
	
	return $current_ts;
	
}

// TIMESTAMP CLEAN
function GET_TS_CLEAN(){
	
	
	/*
	date_default_timezone_set('America/Mexico_City');
	$current_date = date('Y-m-d H:i:s');
	$current_ts = date('Y-m-d H:i:s', strtotime($current_date . ' -1 hour')); 
	*/
	
	//date_default_timezone_set('America/Mexico_City');
	$current_date = date('Y-m-d H:i:s');
	$current_ts = date('Ymd_His', strtotime($current_date . '+0 hour')); 
	
	
	return $current_ts;
	
}

// FECHA
function GET_LOCAL_TS(){
	
	
	if(isset($_COOKIE['timezone'])) {
		$timezone = $_COOKIE['timezone'];
	} else {
		$timezone = "UTC";
	}
	//date_default_timezone_set($timezone);
	
	//date_default_timezone_set('America/Mexico_City');
	$current_date = date('Y-m-d H:i:s');
	$current_ts = date('Y-m-d H:i:s', strtotime($current_date . '+0 hour')); 
	
	
	return $current_ts;
	
}




function DELETE_RULE($LINE_TO_DELETE){
	
	// The file path
	$filePath = $_SERVER['DOCUMENT_ROOT'] . '/.htaccess';

	// The text to search
	$searchText = $LINE_TO_DELETE;

	// Temp array to hold the lines
	$tempLines = array();

	// Open the file
	$file = fopen($filePath, 'r');

	if ($file) {
		while (($line = fgets($file)) !== false) {
			// If the line does not contain the search text, add it to the array
			if (strpos($line, $searchText) === false) {
				$tempLines[] = $line;
			}
		}

		fclose($file);

		// Write the lines back to the file
		file_put_contents($filePath, implode("", $tempLines));
	} else {
		// Error opening the file
		//echo "Error opening the file.";
	}
		
}



function RESIZE_IMAGE($sourceImage, $targetWidth, $targetHeight) {
    // Get the dimensions of the source image
    $sourceWidth = imagesx($sourceImage);
    $sourceHeight = imagesy($sourceImage);

    // Calculate the scale ratio
    $ratio = min($targetWidth / $sourceWidth, $targetHeight / $sourceHeight);

    // Calculate the new image dimensions
    $newWidth = (int)($sourceWidth * $ratio);
    $newHeight = (int)($sourceHeight * $ratio);

    // Create a new empty image
    $targetImage = imagecreatetruecolor($newWidth, $newHeight);

    // Resample the old image into the new image
    imagecopyresampled($targetImage, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $sourceWidth, $sourceHeight);

    return $targetImage;
}

function getLocation($coordinates) {
    $apiKey = 'AIzaSyCvXrP7IAc8GF3jd89m_9bB8_XimVNbEmI'; // Replace with your API key
    //$url = "https://maps.googleapis.com/maps/api/geocode/json?latlng={$coordinates}&key={$apiKey}";
    //$url = "https://maps.googleapis.com/maps/api/geocode/json?latlng=20.21495,-87.429353233&key=AIzaSyCvXrP7IAc8GF3jd89m_9bB8_XimVNbEmI";
    $url = sprintf('https://maps.googleapis.com/maps/api/geocode/json?latlng=%s&key=%s', $coordinates, $apiKey);
	echo $url;

    $json = file_get_contents($url);
    $data = json_decode($json, TRUE); print_r($data);

    if(isset($data['status']) && $data['status'] == 'OK') {
        $location = array();

        foreach($data['results'][0]['address_components'] as $element) {
            if(in_array('country', $element['types'])) {
                $location['country'] = $element['long_name'];
            }

            if(in_array('administrative_area_level_1', $element['types'])) {
                $location['province'] = $element['long_name'];
            }

            if(in_array('locality', $element['types']) || in_array('postal_town', $element['types'])) {
                $location['town'] = $element['long_name'];
            }
        }

        return $location;
    }
	
	//print_r($data);
    return NULL;
}

function getFilePaths($dir) {
    $filePaths = array();

    // Open the directory
    $handle = opendir($dir);

    if (!$handle) {
        return $filePaths;
    }

    // Read all files in the directory
    while (false !== ($entry = readdir($handle))) {
        // Skip current and parent directory entries
        if ($entry == '.' || $entry == '..') {
            continue;
        }

        // Get the full path of the file
        $filePath = $dir . '/' . $entry;

        // Check if it's a file and not a directory
        if (is_file($filePath)) {
            $filePaths[] = $filePath;
        }
    }

    // Close the directory
    closedir($handle);

    return $filePaths;
}

function getFiles($dir) {
    $files = array();
    if ($handle = opendir($dir)) {
        while (false !== ($file = readdir($handle))) {
            if ($file != "." && $file != "..") {
                $filePath = $dir . '/' . $file;
                $fileSize = filesize($filePath);
                $files[] = array(
                    'path' => $filePath,
                    'name' => $file,
                    'size' => $fileSize
                );
            }
        }
        closedir($handle);
    }
    return $files;
}

function resizeAndChangeResolution($sourceFile, $destinationFile, $newWidth, $newHeight, $newResolution) {
    // Get image details
    list($srcWidth, $srcHeight, $type) = getimagesize($sourceFile);

    // Create a new true color image
    $dstImage = imagecreatetruecolor($newWidth, $newHeight);

    // Load the source image
    switch ($type) {
        case IMAGETYPE_JPEG:
            $srcImage = imagecreatefromjpeg($sourceFile);
            break;
        case IMAGETYPE_PNG:
            $srcImage = imagecreatefrompng($sourceFile);
            break;
        case IMAGETYPE_GIF:
            $srcImage = imagecreatefromgif($sourceFile);
            break;
        default:
            throw new Exception('Unsupported image type');
    }

    // Resample the image
    imagecopyresampled($dstImage, $srcImage, 0, 0, 0, 0, $newWidth, $newHeight, $srcWidth, $srcHeight);

    // Save the resized image
    switch ($type) {
        case IMAGETYPE_JPEG:
            imagejpeg($dstImage, $destinationFile, $newResolution);
            break;
        case IMAGETYPE_PNG:
            imagepng($dstImage, $destinationFile, $newResolution / 10);
            break;
        case IMAGETYPE_GIF:
		imagegif($dstImage, $destinationFile);
            break;
    }

    // Free memory
    imagedestroy($srcImage);
    imagedestroy($dstImage);
}


function DELETE_EXCLUDING_FILENAMES($dir, $excludeFiles = array()) {
    if (!is_dir($dir)) {
        //return "Error: The directory does not exist.";
		return false;
    }

    $files = array_diff(scandir($dir), array('.', '..')); // get all files

    foreach($files as $file) {
        if (is_file("$dir/$file") && !in_array($file, $excludeFiles)) {
            unlink("$dir/$file"); // delete the file
        }
    }
	
	return true;
}

function DELETE_EXCLUDING_FILEPATHS($dir, $excludeFiles) {
    // Open the directory
    if ($handle = opendir($dir)) {
        // Loop through the directory
        while (false !== ($file = readdir($handle))) {
            // If not a directory
            if (!is_dir($dir . '/' . $file)) {
                // If the file is not in the excludeFiles array
                if (!in_array($dir . '/' . $file, $excludeFiles)) {
                    // Delete the file
                    unlink($dir . '/' . $file);
                }
            }
        }
        // Close the directory
        closedir($handle);
    }
}

function COPY_DIR($src,$dst) { 
    $dir = opendir($src); 
    @mkdir($dst); 
    while(false !== ( $file = readdir($dir)) ) { 
        if (( $file != '.' ) && ( $file != '..' )) { 
            if ( is_dir($src . '/' . $file) ) { 
                recurse_copy($src . '/' . $file,$dst . '/' . $file); 
            } 
            else { 
                copy($src . '/' . $file,$dst . '/' . $file); 
            } 
        } 
    } 
    closedir($dir); 
} 

function GET_USER_AND_BROKER($U_ID){
	
	
	
		$q = sprintf("SELECT * FROM u124132715_paradise.Users WHERE id = '%s'  ", $U_ID);  //echo $q . "\n";
		$R1 = SQL_2_OBJ_V2($q);
		$RESP['R1'] = $R1; //print_r($R1);

		$USER = $R1['DATA'][0];
		//print_r($_SESSION);
		//print_r($USER);

		$q = sprintf("SELECT * FROM u124132715_paradise.Users WHERE id = '%s'  ", $USER['HIS_BROKER']);  //echo $q . "\n";
		$R2 = SQL_2_OBJ_V2($q);
		$RESP['R2'] = $R2; //print_r($R2);

		$BROKER = $R2['DATA'][0];
		
		
		return array('USER' => $USER, 'BROKER' => $BROKER);
	
	
	
}

function GET_USER($U_ID){
	
	
	
		$q = sprintf("SELECT * FROM u124132715_paradise.Users WHERE id = '%s'  ", $U_ID);  //echo $q . "\n";
		$R1 = SQL_2_OBJ_V2($q);
		$RESP['R1'] = $R1; //print_r($R1);

		$USER = $R1['DATA'][0];
		//print_r($_SESSION);
		//print_r($USER);
		
		return $USER;
	
	
	
}

function REMOVE_FROM_ARRAY($array, $element) {
    $key = array_search($element, $array);
    if ($key !== false) {
        unset($array[$key]);
    }
    return $array; // Return updated array
}

function GET_CRC($inputString) {
    return hash('crc32b', $inputString);
}


function GET_QR_CODE($text, $path, $file_name){
	
    include('phpqrcode/qrlib.php'); 

    // Text to be encoded into the QR code
    //$text = 'Hello, World!';

    // Path where the QR code PNG image will be saved
    //$path = 'images/';

    // Name of the PNG file (you can use a custom name)
    //$file_name = 'myQRCode.png';

    // Full path to the PNG file
    $pngFile = $path.$file_name;

	// CREA FOLDER DE SUBIDA

	if(!file_exists($path)) {
		mkdir($path,0777,true);
	}

	$pngFile = $pngFile . '.png';
	
	// BORRA ARCHIVO SI EXISTE PREVIAMENTE
	
	if (file_exists($pngFile)) {
		unlink($pngFile); // delete file
	}
	
    // Generating the QR code
    // 'L' means the lowest error correction level
    // '3' is the size of the QR code
    QRcode::png($text, $pngFile, 'H', 5); 
	
	
	$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
	$domainName = $_SERVER['HTTP_HOST'];
	$path = $_SERVER['REQUEST_URI'];

	$absolutePath = $protocol . $domainName . $path;

	$TEMP['PATH_DOMAIN'] 		= $protocol . $domainName;
	$TEMP['PATH_ABS'] 			= $absolutePath;
	$TEMP['PATH_FILE_ABS'] 		= $protocol . $domainName . str_replace('..', '', $pngFile);
	$TEMP['PATH_FILE_LOCAL'] 	= $pngFile;
	
	//addTextToImage($TEMP['PATH_FILE_ABS'], 'Shoppers Link');
	
    //echo 'QR code generated and saved at: '.$pngFile;
	return $TEMP;
} 

function addTextToImage($imagePath, $text) {
	
    // Load the image
    $image = imagecreatefrompng($imagePath);

    // Set up the text color (in this case, black)
    $textColor = imagecolorallocate($image, 0, 0, 0);

    // Get the image dimensions
    $imageWidth = imagesx($image);
    $imageHeight = imagesy($image);

    // Set up the text size and position
    $textSize = 10;
    $textX = 10; // 10 pixels from the left
    $textY = $imageHeight - 10; // 10 pixels from the bottom

    // Add the text to the image
    imagestring($image, $textSize, $textX, $textY, $text, $textColor);

    // Save the image
    imagepng($image, $imagePath);

    // Clean up
    imagedestroy($image);
	
	/*
    // Load the image
    $image = imagecreatefrompng($imagePath);

    // Set up the text properties
    $textColor = imagecolorallocate($image, 0, 0, 0); // Black text
    $fontPath = '../fonts/icomoon.ttf'; // Path to your font file
    $fontSize = 20; // Size of the font

    // Calculate the position of the text
    $imageWidth = imagesx($image);
    $imageHeight = imagesy($image);
    $textBox = imagettfbbox($fontSize, 0, $fontPath, $text);
    $textWidth = $textBox[2] - $textBox[0];
    $textHeight = $textBox[7] - $textBox[1];
    $x = ($imageWidth - $textWidth) / 2;
    $y = $imageHeight - $textHeight;

    // Add the text to the image
    imagettftext($image, $fontSize, 0, $x, $y, $textColor, $fontPath, $text);

    // Save the image
    imagepng($image, $imagePath);

    // Free up memory
    imagedestroy($image);
	*/
}



function CLEAN_UNITS_IN_ID($ID){
	
	
	// BORRAR UNIDADES ANTERIORES
		$q = sprintf("SELECT * FROM u124132715_paradise.DEVELOPMENTS WHERE id = '%s' ", $ID ) ;
		//echo $q . ';' . "\n";
		$R31 = SQL_2_OBJ_V2($q);

		$OLD_JSON = $R31['PL'][0];
				
		$OLD_JSON['DEVELOPMENT']['UNITS'] = [];

				
		$NEW_JSON = json_encode($OLD_JSON, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
		
		//print_r($NEW_JSON);

		$q = sprintf("UPDATE u124132715_paradise.DEVELOPMENTS SET Datos = '%s' WHERE id = '%s' ",$NEW_JSON, $ID ) ;
		//echo $q . ';' . "\n";
		$R31 = SQL_2_OBJ_V2($q);
	
	
}


function EXCEL_2_UNITS($filePath){
	
	
		//$filePath =  'Template_units.xlsx';

		$NEW_DATA = EXCEL_2_ARR($filePath);




	// BORRAR UNIDADES ANTERIORES

		foreach($NEW_DATA as $NEW_UNIT){
			echo 'BORRAR UNIDADES ANTERIORES ' . $NEW_UNIT['id'] . "\n";
			//echo '.' ;
			CLEAN_UNITS_IN_ID($NEW_UNIT['id']);
			//echo $NEW_UNIT['id'] . "\n";
		
		}
	
		echo 'OK' . "\n";

	// CARGAR NUEVAS UNIDADES
	
		foreach($NEW_DATA as $NEW_UNIT){
		
			echo 'CARGAR NUEVAS UNIDADES ' . $NEW_UNIT['id'];
			
			//$NEW_UNIT['id'] = '760';
			
			$q = sprintf("SELECT * FROM u124132715_paradise.DEVELOPMENTS WHERE id = '%s' ", $NEW_UNIT['id'] ) ;
			//echo $q . ';' . "\n";
			$R31 = SQL_2_OBJ_V2($q);

			$OLD_JSON = $R31['PL'][0];
			
			$TMP_ARR = [];

			
			foreach( $OLD_JSON['DEVELOPMENT']['UNITS'] as $U ){
				$TMP_ARR[] = $U;
				echo '.'; 
			}
			
			//$TMP_ARR[] = $OLD_JSON['DEVELOPMENT']['UNITS'] ;
			$TMP_ARR[] = (object)$NEW_UNIT;
			//$TMP_ARR[] = $NEW_UNIT[0];

			

			$OLD_JSON['DEVELOPMENT']['UNITS'] = $TMP_ARR;

			
			$NEW_JSON = json_encode($OLD_JSON, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
			
			//print_r($NEW_JSON);

			$q = sprintf("UPDATE u124132715_paradise.DEVELOPMENTS SET Datos = '%s' WHERE id = '%s' ",$NEW_JSON, $NEW_UNIT['id'] ) ;
			//echo $q . ';' . "\n";
			$R31 = SQL_2_OBJ_V2($q);
			
			echo ' OK ' . "\n";
		}
	
	
	
}



function removeSpecialCharsFromArray($array) {
    return array_map(function($item) {
        $item = str_replace(['/', '\\', "\t"], '', $item);
        return $item;
		}, $array);
}

function cleanObject($obj) {
    foreach ($obj as $key => $value) {
        if (is_object($value) || is_array($value)) {
            // If the value is an object or array, recursively clean it
            $obj->$key = cleanObject($value);
        } else {
            // If the value is a string, remove the specified characters
            // Replace '/' with a placeholder, then remove unwanted characters, then put back '/'
            $value = str_replace('//', '<slash>', $value);
            $value = str_replace(array('"', "'", "\t", "\r"), '', $value);
            $obj->$key = str_replace('<slash>', '//', $value);
        }
    }

    return $obj;
}

function GET_AN_ID($TABLE, $DB_ID = 0){
	
		$q = sprintf("INSERT INTO %s (Datos) VALUES ('{}')",  $TABLE); //echo $q . "\n"; 
		$R1 = SQL_2_OBJ_V2($q, $DB_ID);
		$Rs[] = $R1;
		
		
		$RESP['DATOS'] = $Rs;
		
		$NEW_ID = $R1['QRY']['INSERTED_ID'];
		
		return $NEW_ID;
	
}


function DISH_ID_2_NAME($ID){
		
		$ID = str_replace('"', '', $ID);

		$q = sprintf("SELECT JSON_EXTRACT(Datos, '$.NAME') AS DISH_NAME FROM u124132715_DB.Dishes WHERE id = '%s' " , $ID );  //echo $q . "\n";
		$R2 = SQL_2_OBJ_V3($q); //print_r($R2['OBJ'][0]['DISH_NAME']); die;
		
		//return $q;
		
		$TEMP = $R2['OBJ'][0]['DISH_NAME'];
		$TEMP = json_decode($TEMP, true);
		$TEMP = $TEMP;
		
		$TEMP = str_replace('"', '', $TEMP);
		
		return $TEMP;
	
}

function USER_2_NAME($USER){
		
		$USER = str_replace('\"', '', $USER);
		$USER = str_replace('"', '', $USER);

		$q = sprintf("SELECT JSON_EXTRACT(Datos, '$.NAME') AS NAME FROM u124132715_DB.GUESTS WHERE  JSON_EXTRACT(Datos, '$.USER')= '%s' " , $USER );  //echo $q . "\n";
		$R2 = SQL_2_OBJ_V3($q); //print_r($R2['OBJ'][0]['DISH_NAME']); die;
		
		$TEMP = $R2['OBJ'][0]['NAME'];
		
		$TEMP = str_replace('"', '', $TEMP);
		
		return $TEMP;
	
}

function USER_2_PHONE($USER){
		
		$USER = str_replace('\"', '', $USER);
		$USER = str_replace('"', '', $USER);

		$q = sprintf("SELECT JSON_EXTRACT(Datos, '$.PHONE') AS PHONE FROM u124132715_DB.GUESTS WHERE  JSON_EXTRACT(Datos, '$.USER') = '%s' " , $USER );  //echo $q . "\n";
		$R2 = SQL_2_OBJ_V3($q); //print_r($R2['OBJ'][0]['DISH_NAME']); die;
		
		$TEMP = $R2['OBJ'][0]['PHONE'];
		
		$TEMP = str_replace('"', '', $TEMP);
		
		return $TEMP;
	
}

function DISH_ID_2_JSON($ID){
		
		$ID = str_replace('"', '', $ID);

		$q = sprintf("SELECT * FROM u124132715_DB.Dishes WHERE id = '%s' " , $ID );  //echo $q . "\n";
		$R2 = SQL_2_OBJ_V2($q); //print_r($R2['OBJ'][0]['DISH_NAME']); die;
		
		$TEMP  = $R2['PL'][0];
		
		//$TEMP = str_replace('"', '', $TEMP);
		
		return $TEMP;
	
}



function JSON_2_EXCEL($jsonData, $save_to_path, $filename = 'export.xlsx') {
    // Decode the JSON data into an associative array
    $dataArray = json_decode($jsonData, true);

    // Create a new Spreadsheet object
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Check if the JSON data is an array
    if (is_array($dataArray) && !empty($dataArray)) {
        // Get the header row from the first element
        $headers = array_keys($dataArray[0]);
        
        // Set the header row in the Excel sheet
        $sheet->fromArray($headers, NULL, 'A1');

        // Populate the sheet with data
        $sheet->fromArray($dataArray, NULL, 'A2');
        
        // Set the format for the phone number column (assuming it's in column A)
        $phoneColumn = 'A'; // Change this if the phone numbers are in a different column
        $rowCount = count($dataArray) + 1; // +1 for the header row
        $sheet->getStyle("A2:A$rowCount")->getNumberFormat()->setFormatCode('000-000-0000'); // Change format as needed
                
    } else {
        // If the JSON data is empty or not an array, handle it as needed
        $sheet->setCellValue('A1', 'No data available');
        return 'ERROR';
    }

    // Create a writer and save the file
    $writer = new Xlsx($spreadsheet);
    $full_path = $save_to_path . $filename; // Ensure there's no double slash
    $writer->save($full_path); // This will overwrite the file if it exists

    // Optional: Return the file path for further processing
    return $full_path;
}





function GENERAR_HTML_DISH_CARDS($EVENTO_ID, $INDEX){
	
		
	$E_ID = $EVENTO_ID;
		
	$q = sprintf("SELECT * FROM u124132715_DB.Events WHERE id = '%s'", $E_ID);  //echo $q . "\n";
	$R1 = SQL_2_OBJ_V2($q);
	$EVENTO = $R1['PL'][0]; 
	


	
	$CARDS = [];
	$T_VISIBLE = [];
	
	for ($T = 1; $T <= 4; $T++) {
		
		for ($x = 0; $x <= 4; $x++) {
			
			$TIEMPO = sprintf('T%s_O%s', $T, $x);
			
					
			//if ( $EVENTO[$TIEMPO] == '' ) {continue;}
			if ( $EVENTO[$TIEMPO] == '' & $x == 0 ) { $T_VISIBLE[$T] = 'hide'; }

			
			if ($x == 0 ){$ACTIVE = 'active';}else{$ACTIVE = '';}
			if ($x == 0 ){$SELECTED = ' checked="checked"';}else{$SELECTED = ' ';}
			
			$q = sprintf("SELECT * FROM u124132715_DB.Dishes WHERE id = '%s'", $EVENTO[$TIEMPO]);  //echo $q . "\n";
			$R1 = SQL_2_OBJ_V2($q);
			$DISH = $R1['PL'][0]; 
			
			//print_r($DISH);
			
			$OP = '';
			
					foreach($DISH as $KEY=>$VALUE){
						
						if (strpos($KEY, 'OPT_') === 0 & $VALUE === 'on') {
							$OP .= $KEY . ' '; // Append to OP if it starts with 'OP_'
						}
						
					}
			
			
			
			
			$CARD = <<<CARD
			
					<div class="carousel-item {$ACTIVE}" restricciones="{$OP}" style="" persona="{$INDEX}" tiempo="{$T}">
						
						
						<div class="card" style="">

							<div class="card-body CARD_ARRIBA">
								<h5 class="card-title">{$DISH['NAME']}</h5>
								<input type='radio' style="display: none;" class="radio_m_{$OP} {$OP}" persona="{$INDEX}" padre="SELECCION" name='T{$T}' id='RADIO_P{$INDEX}_T{$T}_{$x}' D_ID="{$DISH['ID']}"  tiempo="{$T}" {$SELECTED}> 
								
								<i class="fa-solid fa-circle-info icon-info" onclick="POPUP_INFO('{$DISH['DESCRIPCION']}',  'OK')"  style="display: none;" ></i>
								
								
							</div>
						  
							<div class="FOTO card-img-bottom" style=""> 
								<img src=" {$DISH['AVATAR_PIC']}" class="d-block w-100 picture" alt="...">
							</div>
							
							<div class="card-body DESCRIPCION_PLATILLO">
								<span class="SPAN_DESCRIPCION"> {$DISH['DESCRIPCION']} </span>
							</div>
													
							<div class="card-body CARD_ABAJO">
								<button type="button" class=" BTN_SOLID SEL_DISH {$OP}" for="RADIO_P{$INDEX}_T{$T}_{$x}" persona="{$INDEX}" tiempo="{$T}" restricciones="{$OP}"> Seleccionar</button>
							</div>
						
						</div>




						<div  class="" >
							

						</div>
					</div>
			
			CARD;
			
			
			if (isset($DISH['NAME'])){
				$CARDS[$T][$x] = $CARDS[$T][$x] . $CARD;
				}else{
				//$CARDS[$T][$x] = '';
			}
		}
		
	}
	
	return $CARDS;
	
	
}


function GENERAR_HTML_EXISTING_DISH_CARDS($EVENTO_ID, $USER){
	
		
	$E_ID = $EVENTO_ID;
	
		
	$q = sprintf("SELECT * FROM u124132715_DB.Events WHERE id = '%s'", $E_ID);  //echo $q . "\n";
	$R1 = SQL_2_OBJ_V2($q);
	$EVENTO = $R1['PL'][0]; 
	
	$q = sprintf("SELECT * FROM u124132715_DB.SELECTIONS WHERE JSON_EXTRACT(Datos, '$.E_ID') = '%s' AND JSON_EXTRACT(Datos, '$.USER') = '%s' ", $E_ID, $USER);  //echo $q . "\n";
	$R1 = SQL_2_OBJ_V2($q);
	$SELECTIONS = $R1['PL']; 
		
	$q = sprintf("SELECT * FROM u124132715_DB.RESTRICTIONS");  //echo $q . "\n";
	$R1 = SQL_2_OBJ_V2($q);
	$RESTRICTIONS_DB = $R1['DATA']; 
	

	
	
	//print_r($EVENTO);
	//print_r($SELECTIONS);
	//print_r($RESTRICTIONS_DB);
	
	
	
	if (count($SELECTIONS) > 0){
	

        $EXISTENTES_HTML = '';
			
		
		foreach($SELECTIONS as $INDEX=>$SELECTION){
        
            $INDEX = $INDEX +1;
		
			//$TXT_SELECTED_T = ['Seleccionar ⚠️','Seleccionar ⚠️','Seleccionar ⚠️','Seleccionar ⚠️','Seleccionar ⚠️'];
			
            $CARDS = [];
			$T_VISIBLE = [];
		
			for ($T = 1; $T <= 4; $T++) { //RECORRE CADA TIEMPO
				
				for ($x = 0; $x <= 4; $x++) { // RECORRE CADA OPCION/PLATILLO DE ESE TIEMPO
				
					$TIEMPO = sprintf('T%s_O%s', $T, $x);
					
					//if ( $EVENTO[$TIEMPO] == '' ) {continue; }
					if ( $EVENTO[$TIEMPO] == '' & $x == 0 ) { $T_VISIBLE[$T] = 'hide'; }
					

					// GENERA CHK DE RESTRICCIONES
					
					$HTML_CHK_RESTRICTIONS = '';
					
					$QTY_RESTRICTIONS = 0;
					
					foreach($RESTRICTIONS_DB as $RESTRICTION_DB){
					
						if ( in_array($RESTRICTION_DB,$SELECTION['SELECTED']['RESTRICTIONS'] ) ) { $CHK_SELECTED = 'checked="checked"'; $QTY_RESTRICTIONS++;} else { $CHK_SELECTED = ''; }
						
						$ONE_CHK = <<<TEMP
						<li class="list-group-item">
							<input class="form-check-input CHK_OPTION" type="checkbox" value="{$RESTRICTION_DB['VALUE']}" aria-label="..." persona="{$INDEX}" {$CHK_SELECTED}>{$RESTRICTION_DB['TEXT']}
						</li>
						TEMP;
						
						$HTML_CHK_RESTRICTIONS = $HTML_CHK_RESTRICTIONS . $ONE_CHK;
							
					}
							
					
					
					$q = sprintf("SELECT * FROM u124132715_DB.Dishes WHERE id = '%s'", $EVENTO[$TIEMPO]);  //echo $q . "\n";
					$R1 = SQL_2_OBJ_V2($q);
					$DISH = $R1['PL'][0]; 
					
					//print_r($DISH);
					
					$OP = '';
					
					foreach($DISH as $KEY=>$VALUE){
						
						if (strpos($KEY, 'OPT_') === 0 & $VALUE === 'on') {
							$OP .= $KEY . ' '; // Append to OP if it starts with 'OP_'
						}
						
					}
					
					
					if ( $SELECTION['SELECTED']['T' . $T] == $EVENTO[$TIEMPO] ) {
						
						$ACTIVE = 'active';
						$SELECTED = ' checked="checked"';
						
					}else{
						
						$ACTIVE = '';
						$SELECTED = '';
						
					}
									
					
					$CARD = <<<CARD
					
					<div class="carousel-item {$ACTIVE}" restricciones="{$OP}" style="" persona="{$INDEX}" tiempo="{$T}">
						
						
						<div class="card" style="">

							<div class="card-body CARD_ARRIBA">
								<h5 class="card-title">{$DISH['NAME']}</h5>
								<input type='radio' style="display: none;" class="radio_m_{$OP} {$OP}" persona="{$INDEX}" padre="SELECCION" name='T{$T}' id='RADIO_P{$INDEX}_T{$T}_{$x}' D_ID="{$DISH['ID']}"  tiempo="{$T}" {$SELECTED}> 
								
								<i class="fa-solid fa-circle-info icon-info" onclick="POPUP_INFO('{$DISH['DESCRIPCION']}',  'OK')"  style="display: none;" ></i>
								
								
							</div>
						  
							<div class="FOTO card-img-bottom" style=""> 
								<img src=" {$DISH['AVATAR_PIC']}" class="d-block w-100 picture" alt="...">
							</div>
							
							<div class="card-body DESCRIPCION_PLATILLO">
								<span class="SPAN_DESCRIPCION"> {$DISH['DESCRIPCION']} </span>
							</div>
													
							<div class="card-body CARD_ABAJO">
								<button type="button" class=" BTN_SOLID SEL_DISH {$OP}" for="RADIO_P{$INDEX}_T{$T}_{$x}" persona="{$INDEX}" tiempo="{$T}" restricciones="{$OP}"> Seleccionar</button>
							</div>
						
						</div>




						<div  class="" >
							

						</div>
					</div>
					
					CARD;
					
					
					if (isset($DISH['NAME'])){
						$CARDS[$T][$x] = $CARDS[$T][$x] . $CARD;
						}else{
						//$CARDS[$T][$x] = '';
					}
				}
				
			}
			
			$NOMBRE 		= $SELECTION['NAME'];
			$P_ID 			= $SELECTION['ID'];
			$SAVED 			= 'true';
			$ALERGIAS_TXT 	=  $SELECTION['SELECTED']['ALERGIAS']; 
			//print_r($T_VISIBLE);
			
			include 'GENERAR_PERSONA.php';
			
			
			$EXISTENTES_HTML = $EXISTENTES_HTML . $PERSONA_CARD;
			
		}
		
		
	}
	
	return $EXISTENTES_HTML;
	
}


function SUMMARY($inputArray) {


    $summary = []; // Initialize an empty array to hold the summary counts

    // Iterate through each element of the input array
    foreach ($inputArray as $element) {
        // Check each key in the element
        foreach ($element as $key => $value) {
            // Only consider keys that start with 'T'
            //if (strpos($key, 'T') === 0) {
                // Initialize the count for this T value if it doesn't exist
                if (!isset($summary[$key][$value])) {
                    $summary[$key][$value] = 0;
                }
                // Increment the count for this T value
                $summary[$key][$value]++;
            //}
        }
    }
    // Return the summary as a JSON string
    //return json_encode($summary, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
	
	return $summary;
}



function GET_DEFAULT_OPTIONS($inputArray) {
    $result = []; // Initialize an empty array to store the results
    $result1 = []; // Initialize an empty array to store the results

    // Iterate through the input array
    foreach ($inputArray as $key => $value) {
        // Check if the key ends with 'O0'
        if (substr($key, -2) === 'O0' & $value != '') {
            $result[] = $value; // Add the value to the result array
            $result1[substr($key, 0,2)] = $value; // Add the value to the result array
        }
    }
	

    return $result1; // Return the result array
}

function KITCHEN_LIST($SUMMARY_LIST, $COMENSALES, $DEFAULT_MENU_ARR) {

	//$DEFAULT_MENU_ARR = [$DEFAULT_MENU_ARR[0]=>$DEFAULT_MENU_ARR[0]]

    $summary = []; // Initialize an empty array to hold the summary counts
	$KITCHEN_ARR = [];
	$KITCHEN_LIST = [];

	
	//echo $DEFAULT_MENU_ARR['T3'];
	
	//return $KITCHEN_ARR;
	//if (  count($SUMMARY_LIST) == 0){  }
	
		foreach ($DEFAULT_MENU_ARR as $key => $value){
			$KITCHEN_ARR[$key][$value] = $COMENSALES - sumArrayExceptKey($SUMMARY_LIST[$key], $DEFAULT_MENU_ARR[$key]);
			//$KITCHEN_ARR[$key]['restar'] = sumArrayExceptKey($SUMMARY_LIST[$key], $DEFAULT_MENU_ARR[$key]);
			
		}
		
		//print_r($SUMMARY_LIST);
		

		// Iterate through each element of the input array
		foreach ($SUMMARY_LIST as $T_key => $T_value) {   // TIEMPO LEVEL
			// Check each key in the element
			foreach ($T_value as $key => $value) {   // DISH LEVEL key = Tx, value = dish id
				//echo "$T_key  - $key $value \n";
				// Only consider keys that start with 'T'
					 //echo '-' . $SUMMARY_LIST[$key][$value] . "\n";
				if (strpos($T_key, 'T') === 0 & $key != '' ) {
					
					
					if ( !isset( $KITCHEN_ARR[$T_key][$key] ) ){
						$KITCHEN_ARR[$T_key][$key] = $value;
					}
					
					
					// Initialize the count for this T value if it doesn't exist
					if (!isset($summary[$key][$value])) {
						$summary[$key][$value] = 0;
						//$summary[$value] = 0;
					}
					// Increment the count for this T value
					//$summary[$key][$value]++;
					//$summary[$value]++;
				}
			}
		}
	
	
	foreach ($KITCHEN_ARR as $T => $COURSES){
		//echo $T . "\n";
		foreach ($COURSES as $DISH_ID => $QTY){
			//echo $DISH_ID . "\n";
			$DISH_NAME = DISH_ID_2_NAME($DISH_ID);
			
			$KITCHEN_LIST[$T][$DISH_NAME] = $QTY;
		}
	}
	
	//print_r($KITCHEN_LIST); die;
	
	//$KITCHEN_LIST = createHtmlList($KITCHEN_LIST);
	//$KITCHEN_LIST = array_2_table($KITCHEN_LIST);
	
    // Return the summary as a JSON string
    //return json_encode($summary, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
	return $KITCHEN_LIST;
}


function sumArrayExceptKey($inputArray, $excludeKey) {
    $sum = 0; // Initialize the sum

    // Iterate through each key-value pair in the array
    foreach ($inputArray as $key => $value) {
        // Check if the current key is not the one to be excluded
        if ($key != $excludeKey) {
            // Check if the value is numeric before adding
            if (is_numeric($value)) {
				//echo $key . ' - ' . $value . ' - ' . $excludeKey . "\n";
                $sum += $value; // Add the value to the sum
            }
        }
    }

    return $sum; // Return the total sum
}

function createHtmlList($array, $level = 0) {
    $html = '<ul class="row pt-10">';

    foreach ($array as $title => $subtitles) {
        $html .= "<div class='col-3'><li><h6>$title</h6><ul>";

        foreach ($subtitles as $subtitle => $value) {
            $html .= "<li><h10>$subtitle: $value</h4></li>";
        }

        $html .= '</ul></li></div>';
    }

    $html .= '</ul>';
	


    return $html;
}

function calculateTotals($inputs) {
    $totals = [];

    foreach($inputs as $items) {
        foreach($items as $key => $value) {
            if(array_key_exists($key, $totals)) {
                $totals[$key] += $value;
            } else {
                $totals[$key] = $value;
            }
        }
    }

    return $totals;
}						

function createHtmlListFromJson($json) {
    // Decode the JSON string to an associative array
    $array = json_decode($json, true);

    // Start the HTML list
    $html = '<ul>';

    // Loop through the array
    foreach ($array as $key => $value) {
        // Add each key-value pair to the list
        $html .= '<li class="d-flex align-items-center gap-1 mb-12">';;
        $html .= '<span class="w-50 text-md fw-semibold text-primary-light align-items-left">' . $key . '</span>';
        $html .= '<span class="w-50 text-secondary-light fw-medium">' . $value . '</span>';
        $html .= '</li>';
    }

    // End the HTML list
    $html .= '</ul>';

    // Return the HTML list
    return $html;
}


function arrayToHtmlTable($array, $cols_arr, $table) {
	
	$headers = '';
	foreach ($cols_arr as $index => $col_name){
		
		if ($index != 0){
			$headers .= '<th scope="col">'. $col_name . '</th>';
		}
	}
$htmlOutput = <<<H
				<table class="table bordered-table sm-table mb-0">
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

    foreach ($array as $row) {
        $htmlOutput .= '<tr>';
        $htmlOutput .= '<td>
                        <div class="d-flex align-items-center gap-10">
                            <div class="form-check style-check d-flex align-items-center">
                                <input class="form-check-input radius-4 border border-neutral-400" type="checkbox" name="checkbox">
                            </div>
                            '.htmlspecialchars($row['ID']).'
                        </div>
                    </td>'; 

        // Loop through each key in the $cols_arr
        foreach ($cols_arr as $col) {
            if (isset($row[$col]) & $col != 'ID') {
                // Append each value wrapped in <td> tags to the current row
                $htmlOutput .= '<td>' . '<span class="">' . htmlspecialchars($row[$col]) . '</span>' . '</td>' . "\n";
            }
			if (!isset($row[$col])){
				$htmlOutput .= '<td>' . '<span class="">' . htmlspecialchars($row[$col]) . '</span>' . '</td>' . "\n";
			}
        }

$htmlOutput .= <<<H
				<td class="text-center">
					<div class="d-flex align-items-center gap-10 justify-content-center">
						<button type="button" class="bg-info-focus bg-hover-info-200 text-info-600 fw-medium w-40-px h-40-px d-flex justify-content-center align-items-center rounded-circle">
							<iconify-icon icon="majesticons:eye-line" class="icon text-xl"></iconify-icon>
						</button>
						<button type="button" class="bg-success-focus text-success-600 bg-hover-success-200 fw-medium w-40-px h-40-px d-flex justify-content-center align-items-center rounded-circle">
							<iconify-icon icon="lucide:edit" class="menu-icon"></iconify-icon>
						</button>
						<button type="button" class="bg-danger-focus text-danger-600 bg-hover-danger-200 fw-medium w-40-px h-40-px d-flex justify-content-center align-items-center rounded-circle" >
							<iconify-icon icon="lucide:trash" class="menu-icon"></iconify-icon>
						</button>
					</div>
				</td>
				H;
        $htmlOutput .= '</tr>';
    }
	
	$htmlOutput .= '</tbody> </table>';
	
	//bg-success-focus text-success-600 border border-success-main px-24 py-4 radius-4 fw-medium text-sm
	//bg-neutral-200 text-neutral-600 border border-neutral-400 px-24 py-4 radius-4 fw-medium text-sm

    return $htmlOutput;
}


function DB_2_TABLE( $cols_arr, $table) {

	$q = sprintf("SELECT * FROM %s", $table);  //echo $q . "\n";
	$R1 = SQL_2_OBJ_V2($q);
	$RESP['R1'] = $R1; //print_r($R1);
	$DB_DATA = $R1['DATA'];


	$headers = '';
	foreach ($cols_arr as $index => $col_name){
		
		if ($index != 0){
			$headers .= '<th scope="col">'. $col_name . '</th>';
		}
	}
$htmlOutput = <<<H
				<table class="table bordered-table sm-table mb-0">
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

        // Loop through each key in the $cols_arr
        foreach ($cols_arr as $col) {
			
			
		
            if (isset($row[$col]) & $col != 'ID') {
                // Append each value wrapped in <td> tags to the current row
                $htmlOutput .= '<td>' . '<span class="">' . htmlspecialchars($row[$col]) . '</span>' . '</td>' . "\n";
            }
			if (!isset($row[$col])){
				$htmlOutput .= '<td>' . '<span class="">' . htmlspecialchars($row[$col]) . '</span>' . '</td>' . "\n";
			}
        }

		$htmlOutput .= <<<H
				<td class="text-center">
					<div class="d-flex align-items-center gap-10 justify-content-center">
						<button type="button" class="bg-info-focus bg-hover-info-200 text-info-600 fw-medium w-40-px h-40-px d-flex justify-content-center align-items-center rounded-circle">
							<iconify-icon icon="majesticons:eye-line" class="icon text-xl"></iconify-icon>
						</button>
						<a  href="view-profile.php?id={$row['ID']}">
						<button type="button" class="bg-success-focus text-success-600 bg-hover-success-200 fw-medium w-40-px h-40-px d-flex justify-content-center align-items-center rounded-circle">
							<iconify-icon icon="lucide:edit" class="menu-icon"></iconify-icon>
						</button>
						</a>
						<button type="button" class="bg-danger-focus text-danger-600 bg-hover-danger-200 fw-medium w-40-px h-40-px d-flex justify-content-center align-items-center rounded-circle" onclick="DELETE_ID('{$table}', {$row['ID']})">
							<iconify-icon icon="lucide:trash" class="menu-icon"></iconify-icon>
						</button>
					</div>
				</td>
				H;
        $htmlOutput .= '</tr>';
    }
	
	$htmlOutput .= '</tbody> </table>';
	
	//bg-success-focus text-success-600 border border-success-main px-24 py-4 radius-4 fw-medium text-sm
	//bg-neutral-200 text-neutral-600 border border-neutral-400 px-24 py-4 radius-4 fw-medium text-sm

    return $htmlOutput;
}


function DB_2_TABLE_V2( $cols_arr, $table) {

	$q = sprintf("SELECT * FROM %s", $table);  //echo $q . "\n";
	$R1 = SQL_2_OBJ_V2($q);
	$RESP['R1'] = $R1; //print_r($R1);
	$DB_DATA = $R1['DATA'];


	$headers = '';
	foreach ($cols_arr as $index => $col_name){
		
		if ($index != 0){
			$headers .= '<th scope="col">'. $col_name . '</th>';
		}
	}
$htmlOutput = <<<H
				<div class="card h-100 p-0 radius-12">
				<div class="card-header border-bottom bg-base py-16 px-24 d-flex align-items-center flex-wrap gap-3 justify-content-between">
					<div class="d-flex align-items-center flex-wrap gap-3">
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
							<input type="text" class="bg-base h-40-px w-auto" name="search" placeholder="Search">
							<iconify-icon icon="ion:search-outline" class="icon"></iconify-icon>
						</form>
						<select class="form-select form-select-sm w-auto ps-12 py-6 radius-12 h-40-px">
							<option>Status</option>
							<option>Active</option>
							<option>Inactive</option>
						</select>
					</div>
					<a href="add-user.php" class="btn btn-primary text-sm btn-sm px-12 py-12 radius-8 d-flex align-items-center gap-2">
						<iconify-icon icon="ic:baseline-plus" class="icon text-xl line-height-1"></iconify-icon>
						Add New User
					</a>
				</div>
				<div class="card-body p-24">
					<div class="table-responsive scroll-sm">
				<table class="table bordered-table sm-table mb-0">
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

        // Loop through each key in the $cols_arr
        foreach ($cols_arr as $col) {
			
			
		
            if (isset($row[$col]) & $col != 'ID') {
                // Append each value wrapped in <td> tags to the current row
                $htmlOutput .= '<td>' . '<span class="">' . htmlspecialchars($row[$col]) . '</span>' . '</td>' . "\n";
            }
			if (!isset($row[$col])){
				$htmlOutput .= '<td>' . '<span class="">' . htmlspecialchars($row[$col]) . '</span>' . '</td>' . "\n";
			}
        }

		$htmlOutput .= <<<H
						<td class="text-center">
							<div class="d-flex align-items-center gap-10 justify-content-center">
								<button type="button" class="bg-info-focus bg-hover-info-200 text-info-600 fw-medium w-40-px h-40-px d-flex justify-content-center align-items-center rounded-circle">
									<iconify-icon icon="majesticons:eye-line" class="icon text-xl"></iconify-icon>
								</button>
								<a  href="view-profile.php?id={$row['ID']}">
								<button type="button" class="bg-success-focus text-success-600 bg-hover-success-200 fw-medium w-40-px h-40-px d-flex justify-content-center align-items-center rounded-circle">
									<iconify-icon icon="lucide:edit" class="menu-icon"></iconify-icon>
								</button>
								</a>
								<button type="button" class="bg-danger-focus text-danger-600 bg-hover-danger-200 fw-medium w-40-px h-40-px d-flex justify-content-center align-items-center rounded-circle" onclick="DELETE_ID('{$table}', {$row['ID']})">
									<iconify-icon icon="lucide:trash" class="menu-icon"></iconify-icon>
								</button>
							</div>
						</td>
						H;
        $htmlOutput .= '</tr>';
    }
	
	$htmlOutput .= <<<H
							</tbody> 
						</table>
							</div>
								<div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mt-24">
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
	
	//bg-success-focus text-success-600 border border-success-main px-24 py-4 radius-4 fw-medium text-sm
	//bg-neutral-200 text-neutral-600 border border-neutral-400 px-24 py-4 radius-4 fw-medium text-sm

    return $htmlOutput;
}

function DB_2_TABLE_V3( $T_PARAMS ) {

	$q = sprintf("SELECT * FROM %s", $T_PARAMS['DB']['TABLE']);  //echo $q . "\n";
	$R1 = SQL_2_OBJ_V2($q);
	$RESP['R1'] = $R1; //print_r($R1);
	$DB_DATA = $R1['DATA'];

	
	/// HEADERS ////
		$headers = '';
		foreach ($T_PARAMS['TABLE']['COLS'] as $index => $col_name){
			
			
				$headers .= '<th scope="col">'. $col_name . '</th>';
			
		}
	

	
	/// BARRA DE BUSQUEDA ///



		$htmlOutput .= <<<H
						<div class="card h-100 p-0 radius-12">
						<div class="card-header border-bottom bg-base py-16 px-24 d-flex align-items-center flex-wrap gap-3 justify-content-between">
							<div class="d-flex align-items-center flex-wrap gap-3">
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
								<select class="form-select form-select-sm w-auto ps-12 py-6 radius-12 h-40-px">
									<option>Status</option>
									<option>Active</option>
									<option>Inactive</option>
								</select>
							</div>
							<a href="add-user.php" class="btn btn-primary text-sm btn-sm px-12 py-12 radius-8 d-flex align-items-center gap-2">
								<iconify-icon icon="ic:baseline-plus" class="icon text-xl line-height-1"></iconify-icon>
								Add New User
							</a>
						</div>
						<div class="card-body p-24">
							<div class="table-responsive scroll-sm">
						<table class="table bordered-table sm-table mb-0" id="{$T_PARAMS['TABLE']['ID']}">
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
							case "NAME":
							//code block
							$htmlOutput .= '<td>' . '<span class="">' . htmlspecialchars($row[$col]) . '</span>' . '</td>' . "\n";
							break;

						  default:
							//code block
							$htmlOutput .= '<td>' . '<span class="">' . htmlspecialchars($row[$col]) . '</span>' . '</td>' . "\n";
						}
					
				}
				if (!isset($row[$col])){
					$htmlOutput .= '<td>' . '<span class="">' . htmlspecialchars($row[$col]) . '</span>' . '</td>' . "\n";
				}
			}

			$htmlOutput .= <<<H
							<td class="text-center">
								<div class="d-flex align-items-center gap-10 justify-content-center">
									<button type="button" class="bg-info-focus bg-hover-info-200 text-info-600 fw-medium w-40-px h-40-px d-flex justify-content-center align-items-center rounded-circle">
										<iconify-icon icon="majesticons:eye-line" class="icon text-xl"></iconify-icon>
									</button>
									<a  href="{$T_PARAMS['TABLE']['href']}?id={$row['ID']}">
									<button type="button" class="bg-success-focus text-success-600 bg-hover-success-200 fw-medium w-40-px h-40-px d-flex justify-content-center align-items-center rounded-circle">
										<iconify-icon icon="lucide:edit" class="menu-icon"></iconify-icon>
									</button>
									</a>
									<button type="button" class="bg-danger-focus text-danger-600 bg-hover-danger-200 fw-medium w-40-px h-40-px d-flex justify-content-center align-items-center rounded-circle remove-item-btn" onclick="DELETE_ID('{$T_PARAMS['DB']['TABLE']}', {$row['ID']})">
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
									<div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mt-24">
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
	

		$htmlOutput .= <<<FN

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


		 				</script>
		 				

		FN;

    return $htmlOutput;
}


function GENERAR_EVENT_COURSES($data, $DB_DISHES) {

    $html = sprintf('');
    // Loop through each key in the array
    foreach ($data as $key => $value) {

        // Check if the key starts with 'T' and contains '_O'
        if (strpos($key, 'T') === 0 && strpos($key, '_O') !== false) {
            // Split the key into two parts: 'T' and 'O'
            list($t, $o) = explode('_O', $key);




            // If 'O' is '0', add a label to the html
            if ($o == '0') {



				$SIZE = 3;
				$REQUIRED = ' required';

				
				$html .= sprintf('<div id="COURSE" idx="%s" class="row">', $t );

				$TITULO = <<<T
							
								<div class="col-sm-12">
									<label for="{$key}" class="form-label fw-semibold text-primary-light text-sm mb-8">Tiempo {$t}<span class="text-danger-600">*</span> </label>
								</div>
							T;
							
                $html .= $TITULO;
                
				
				$SUB_TITULO = <<<S
								
								<label for="{$key}" class="form-label fw-semibold text-primary-light text-sm mb-8">Por defecto <span class="text-danger-600"></span> </label>
								S;
            }else{
				$SIZE = 2;
				$REQUIRED = '';
				$SUB_TITULO = <<<S
								<label for="{$key}" class="form-label fw-medium text-primary-light text-sm mb-8">Opción {$o} <span class="text-danger-600"></span> </label>
								S;			
			}
			


            
            $DROPDOWN = sprintf('<select  class="form-control radius-8 form-select" name="%s" %s >',$key, $REQUIRED );
			$DROPDOWN .= '<option value=""></option>';
			
	            foreach ($DB_DISHES as $DISH) {
	                // If the current value matches the value in the array, mark it as selected
	                $selected = ($DISH['ID'] == $value) ? ' selected' : '';
	                $DROPDOWN .= '<option value="' . $DISH['ID'] . '"' . $selected . '>' . $DISH['NAME'] . '</option>';
	            }
				
			
            $DROPDOWN .= '</select><br>';
			
			$DROPDOWN_WRAP = <<<D
							<div class="col-sm-{$SIZE}">
								<div class="mb-20">
								{$SUB_TITULO}
								{$DROPDOWN}
								</div>
							</div>
							D;

            $html .= $DROPDOWN_WRAP;

            
            
        }

        if ($o == 4) { $html .= '</div>'; }
    }



    return $html;

}


function array_2_table($data) {
    // start table
    $html = '<table>';

    // header row
    $html .= '<tr>';
    foreach($data as $key => $value){
        $html .= '<th style="text-align:center;">' . htmlspecialchars($key) . '</th>';
    }
    $html .= '</tr>';

    // data rows
    $maxRows = max(array_map('count', $data));
    for ($i = 0; $i < $maxRows; $i++) {
        $html .= '<tr>';
        foreach($data as $value){
            $keys = array_keys($value);
            $vals = array_values($value);
            if(isset($keys[$i]) && isset($vals[$i])){
                $html .= '<td><iconify-icon icon="mingcute:dish-cover-line" class="icon"></iconify-icon> ' . htmlspecialchars($keys[$i]) . ' ' . htmlspecialchars($vals[$i]) . '</td>';
            } else {
                $html .= '<td></td>';
            }
        }
        $html .= '</tr>';
    }

    // finish table and return it

    $html .= '</table>';
    return $html;
}

function convertEpochToString($epoch) {
    // check if epoch time is numeric
    if(is_numeric($epoch)) {
        // convert epoch to date time format
        $date = new DateTime("@$epoch");
        // format the date
        //return $date->format('Y-m-d H:i:s');
        return $date->format('Y-m-d');
    } else {
        // if epoch time is not numeric, return an error message
        return "---";
    }
}


?>


