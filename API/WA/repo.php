

<?php 
	

setlocale(LC_ALL, 'en_US.UTF-8');
header('Content-type: text/javascript; charset=utf-8');
include_once ($_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php');



/*///////////////////////////////////////////////////////////////////////////////////////////////////////////////

WHATSAPP
			
			//composer require guzzlehttp/guzzle

			$CHAT_ID debe de ser LADA + 10 digitos
			
			WA_SEND_TXT('14167682436', 'TEST');  
			WA_SEND_FILE('14167682436', 'https://test.prepenv.com/REPO/robot.png', 'Imagen de Robot');
			
			
			
*///////////////////////////////////////////////////////////////////////////////////////////////////////////////




function jsonResponse($success, $data = null, $message = '') {
    return json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data,
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
}


function CLEAN_CHAT_ID($str) {
	
	if (strpos($str, '@g.us') !== false) { return $str;  }
	//if (strpos($str, '@c.us') !== false) { return $str;  }
	
	
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

    $CHAT_ID = CLEAN_CHAT_ID($CHAT_ID);

    if ( WA_INSTANCE_STATUS() == false ) { 
        return jsonResponse(false, 'Instance status failed.'); 
    }

    if ( WA_CHECK_EXISTE($CHAT_ID) == false ) { 
        return jsonResponse(false, 'Chat ID does not exist.'); 
    }
	
	$TXT = str_replace("\n", ' \n ', $TXT);
	
    $WA_BODY = sprintf('{"message":"%s", "chatId":"%s@c.us"}', $TXT, $CHAT_ID );  

    $client = new \GuzzleHttp\Client();
    $response = $client->request('POST', 'https://waapi.app/api/v1/instances/15863/client/action/send-message', [
      'body' => $WA_BODY,
      'headers' => [
        'accept' => 'application/json',
        'authorization' => 'Bearer zHXEDP8kJqxtlLPtjWPkIrrQL2sfCGZPqhKjmDG3961f2855',
        'content-type' => 'application/json',
      ],
    ]);

    $PAYLOAD = (string) $response->getBody();
    $PAYLOAD = json_decode($PAYLOAD, true);

    $STATUS = $PAYLOAD['data']['status'];

    if ( $STATUS == 'success' ) { 
        return jsonResponse(true, $PAYLOAD, 'Message sent successfully.'); 
    } else { 
        return jsonResponse(false, $PAYLOAD,'Message sending failed.'); 
    }
}

function WA_SEND_TXT_TO_GROUP($CHAT_ID, $TXT){ 

    //Check instance status
    if ( WA_INSTANCE_STATUS() == false ) { 
        return jsonResponse(false, "Instance status is false"); 
    } 

    //Prepare request body
    $WA_BODY = sprintf('{"message":"%s", "chatId":"%s"}', $TXT, $CHAT_ID );    

    //Create client and send request
    $client = new \GuzzleHttp\Client();
    $response = $client->request('POST', 'https://waapi.app/api/v1/instances/15863/client/action/send-message', [
      'body' => $WA_BODY,
      'headers' => [
        'accept' => 'application/json',
        'authorization' => 'Bearer zHXEDP8kJqxtlLPtjWPkIrrQL2sfCGZPqhKjmDG3961f2855',
        'content-type' => 'application/json',
      ],
    ]);

    //Get response body
    $PAYLOAD = (string) $response->getBody();
    $PAYLOAD = json_decode($PAYLOAD, true);

    //Check status
    $STATUS = $PAYLOAD['data']['status'];

    if ( $STATUS == 'success' ) { 
        return jsonResponse(true, "Message sent successfully");
    } else { 
        return jsonResponse(false, "Message sending failed");
    }
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
		return jsonResponse(true, $USER, 'User DONT exist.');
	} else{
		
		return jsonResponse(true, $USER, 'User exist.');
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
	
	return jsonResponse(true, $PAYLOAD, 'Image retrieved.');
	

	
}

error_reporting(E_ALL);
ini_set('display_errors', 'On');

$PHONE = '14167682436' . '@c.us';
//$PHONE = '41768285549' . '@c.us';

//echo 'ok';
/*
$RESP = WA_SEND_TXT('14167682436', 'TEST');
$RESP = json_decode($RESP);
$id = $RESP->data->data->data->to;
echo $id;
print_r(WA_GET_PICTURE($id));


$RESP = WA_GET_PICTURE('14167682436@c.us');
$RESP = json_decode($RESP);
echo $RESP->data->data->data->profilePicUrl;
//print_r($RESP);

$RESP = WA_CHECK_EXISTE($PHONE);
$RESP = json_decode($RESP);
echo $RESP->data->data->data->isRegisteredUser;
print_r($RESP);

*/
 


?>