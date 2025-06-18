<?php 
	

setlocale(LC_ALL, 'en_US.UTF-8');
header('Content-type: text/javascript; charset=utf-8');
//include_once ($_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php');
include_once ($_SERVER['DOCUMENT_ROOT'] . '/API/WA/repo.php');



// Get the raw POST data
$entityBody = file_get_contents('php://input');

// Check if the entity body is not empty
if ($entityBody) {
    // Decode the JSON input
    $BODY_OB = json_decode($entityBody, true);

    // Check if JSON decoding was successful
    if (json_last_error() === JSON_ERROR_NONE) {
        $DATA = $BODY_OB;   
        $KEYS = array_keys($DATA); // No need to decode $DATA again

        // Encode the data back to JSON
        $DATA_STR = json_encode($DATA);
        
        // Check if JSON encoding was successful
        if ($DATA_STR === false) {
            // Handle JSON encoding error (optional)
            error_log('JSON encoding error: ' . json_last_error_msg());
        }

        // Continue processing with $DATA, $KEYS, and $DATA_STR as needed
    } else {
        // Handle JSON decoding error (optional)
        error_log('JSON decoding error: ' . json_last_error_msg());
    }
} else {
    // No data received, continue executing
}


$RESP = WA_SEND_TXT($DATA['TO'], $DATA['TXT']);
$RESP = json_decode($RESP);
print_r($RESP);
#$id = $RESP->data->data->data->to;
#echo $id;
//print_r(WA_GET_PICTURE($id));

?>
