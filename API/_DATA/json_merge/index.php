<?php
	
	
//error_reporting(E_ALL);
//ini_set('display_errors', 1);


include_once($_SERVER['DOCUMENT_ROOT'] . '/PHP/MYF1.php');
	
setlocale(LC_ALL, 'en_US.UTF-8');
header('Content-type: text/javascript; charset=utf-8');
	


$entityBody = file_get_contents('php://input');
$BODY_OB = json_decode($entityBody, true);		//$BODY_OB = json_decode($BODY_EN, true);
$DATA = $BODY_OB;   
$DATA_STR = json_encode($DATA , true);




function reduceImageSize($base64Image, $maxSize = 100000) {
    // Extract the image type from the base64 string
    $imageParts = explode(";base64,", $base64Image);
    $imageType = str_replace('data:image/', '', $imageParts[0]);
    $imageType = str_replace(';', '', $imageType);
    
    // Decode the base64 string
    $imageData = base64_decode($imageParts[1]);
    
    // Create an image resource from the decoded data
    $image = imagecreatefromstring($imageData);
    
    if ($image === false) {
        return false; // Return false if image creation fails
    }

    $quality = 100; // Start with the highest quality
    $width = imagesx($image);
    $height = imagesy($image);
    
    // Loop to reduce the image size
    do {
        // Create a new true color image with reduced dimensions
        $newWidth = (int) ($width * 0.9); // Reduce width by 10%
        $newHeight = (int) ($height * 0.9); // Reduce height by 10%
        
        $newImage = imagecreatetruecolor($newWidth, $newHeight);
        
        // Resample the image
        imagecopyresampled($newImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
        
        // Save the image to a temporary variable
        ob_start();
        imagejpeg($newImage, null, $quality); // Save as JPEG with current quality
        $imageData = ob_get_contents();
        ob_end_clean();
        
        // Calculate the size of the image data
        $imageSize = strlen($imageData);
        
        // Update width and height for the next iteration
        $width = $newWidth;
        $height = $newHeight;

        // Reduce quality for the next iteration if necessary
        if ($imageSize > $maxSize) {
            $quality -= 5; // Decrease quality by 5
        }
        
        // Destroy the new image resource to free memory
        imagedestroy($newImage);
        
    } while ($imageSize > $maxSize && $quality > 0);

    // Free the original image resource
    imagedestroy($image);

    // Encode the final image data back to base64
    $finalBase64Image = 'data:image/' . $imageType . ';base64,' . base64_encode($imageData);
    
    return $finalBase64Image;
}

function string_2_imagefile($base64String, $folder, $filename) {
    // Check if the base64 string is valid
    if (preg_match('/^data:image\/(\w+);base64,/', $base64String, $type)) {
        // Get the file extension
        $type = strtolower($type[1]); // jpg, png, gif
        if (!in_array($type, ['jpg', 'jpeg', 'png', 'gif'])) {
            return ['status' => 'error', 'message' => 'Invalid image type.'];
        }

        // Remove the base64 part
        $data = substr($base64String, strpos($base64String, ',') + 1);
        $data = base64_decode($data);
        if ($data === false) {
            return ['status' => 'error', 'message' => 'Base64 decode failed.'];
        }

        // Create the directory if it does not exist
        if (!file_exists($folder)) {
            if (!mkdir($folder, 0777, true) && !is_dir($folder)) {
                return ['status' => 'error', 'message' => 'Failed to create directory.'];
            }
        }

        // Create the file path
        $filePath = rtrim($folder, '/') . '/' . $filename . '.' . $type;

        // Save the image file
        if (file_put_contents($filePath, $data) === false) {
            return ['status' => 'error', 'message' => 'Failed to save the image.'];
        }

        return ['status' => 'success', 'message' => 'Image saved successfully.', 'filePath' => $filePath];
    } else {
        return ['status' => 'error', 'message' => 'Invalid base64 string.'];
    }
}

		
function FUNCION($DATA){
	

	/// CURRENT DATA 
	
			$currentQuery = sprintf("SELECT * FROM %s WHERE id = '%s'", $DATA['TABLA'], $DATA['ID']);
			$currentData = SQL_2_OBJ_V2($currentQuery, $DATA['DB_ID']);
			$currentData = $currentData['PL'][0];
			
	/// NEW DATA 
				
			$newData = json_decode($DATA['Datos'], true); // Assuming $DATA['Datos'] is a valid JSON string
			
			//print_r($newData);die;
			// Update the current array with new values where keys match
			
			foreach ($newData as $key => $value) {
			
					$value_type = gettype($value);
					
					switch($value_type){
						
						case 'string':
						
							if (strpos($value, ';base64,') !== false) {
	
								$value = reduceImageSize($value, 80000);
								
								$imageParts = explode(";base64,", $value);
								$imageType = str_replace('data:image/', '', $imageParts[0]);
								$imageType = str_replace(';', '', $imageType);
								
								$currentData[$key] = $value; ///     <------------- esto se guarda en la DB tabla original 
								
								
								$folder = $_SERVER['DOCUMENT_ROOT'] . '/_REPO/UPLOADS/' . $DATA['TABLA'] ;
								$filename = $DATA['ID'] . '_image';
								string_2_imagefile($value, $folder, $filename);
								
								
								$METADATA = json_encode([ 
									"TABLE"		=>	$DATA['TABLE'],
									"USER_ID"		=>	$DATA['ID'],
									"ELEMENT_NAME"	=>	$key
									
								] , true );
								
								
								$q = sprintf("SELECT id, TS, Datos FROM %s WHERE JSON_EXTRACT(Datos, '$.USER_ID') = '%s' ", 'FILES', $DATA['ID']);
								$R1 = SQL_2_OBJ_V2($q, $DATA['DB_ID']); // Execute the select query
								$ENCONTRADOS = $R1['QRY']['ROWS'];

								if ($ENCONTRADOS == 0) {
									// Ensure $value is defined before using it
									$q = sprintf("INSERT INTO %s (Datos, file_blob) VALUES ('%s', '%s') ", 'FILES', $METADATA, $value);
								} else {
									// Corrected SQL query without the extra parenthesis
									$q = sprintf("UPDATE %s SET Datos = '%s', file_blob = '%s' WHERE JSON_EXTRACT(Datos, '$.USER_ID') = '%s' ", 'FILES', $METADATA, $value, $DATA['ID']);
								}

								// Execute the query and check for errors
								$R1 = SQL_2_OBJ_V2($q, $DATA['DB_ID']); // Execute the update
								
								if (!$R1) {
									// Handle error here, e.g., log it or throw an exception
									error_log("SQL Error: " . mysqli_error($connection)); // Assuming $connection is your DB connection
								}

								//$RESP['IMG'] = $R1;
							}else{
								$currentData[$key] = $value; 
							}
						
						break;
						
						
						case 'array':
							
							$currentData[$key] = $value; 
						
						break;
						
						default:
						
							$currentData[$key] = $value; 
							
					}
										
							
				
			}
			
			//print_r($currentData);die;
			
		
			
	/// UPDATED DATA 
	
			// Encode the updated array back to JSON
			ksort($currentData);
			$updatedJson = json_encode($currentData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
			



	/// UPDATE THE DATABASE 
	
	
			// Update the database with the new JSON string
			$updateQuery = sprintf("UPDATE %s SET Datos = '%s' WHERE id = '%s'", $DATA['TABLA'], $updatedJson, $DATA['ID']);
			$R1 = SQL_2_OBJ_V2($updateQuery, $DATA['DB_ID']); // Execute the update

			$RESP['R1'] = $R1; // Store the result of the update
			$RESP['DATOS'] = $R1; // Optionally return the updated data
	
	return $RESP;



}

//print_r($DATA);die;

include_once($_SERVER['DOCUMENT_ROOT'] . '/API/STRIPE/repo.php');

$STRIPE_MESSAGES = [];
//print_r($DATA);

switch($DATA['TABLA']){
	
	case 'CLIENTS':
	
		$STRIPE_RESPONSE = createOrUpdateCustomer($DATA['Datos']);
		$new_data_json = json_decode($DATA['Datos'], true); 
		$STRIPE_RESPONSE_ARR = json_decode($STRIPE_RESPONSE,true);
		$STRIPE_MESSAGES[]		= $STRIPE_RESPONSE_ARR['message'];
		$new_data_json['stripe_client_id'] =  $STRIPE_RESPONSE_ARR['data']['id'];
		
		$DATA['Datos'] = json_encode($new_data_json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
		
		break;
	
	case 'PRODUCTS':

		$new_data_json 			= json_decode($DATA['Datos'], true); 
		
		$STRIPE_RESPONSE 		= createOrUpdateProduct($DATA['Datos']);
		$STRIPE_RESPONSE_ARR 	= json_decode($STRIPE_RESPONSE,true);  //print_r($STRIPE_RESPONSE_ARR);die;
		$STRIPE_MESSAGES[]		= $STRIPE_RESPONSE_ARR['message'];
		$new_data_json['stripe_product_id'] =  $STRIPE_RESPONSE_ARR['data']['id'];
		
		//print_r($new_data_json); die;
		/*
		$stripe_prod_id	= $STRIPE_RESPONSE_ARR['data']['id'];
		$priceData 		= json_encode([
			'PRICE' => $new_data_json['price'], 
			'CURRENCY' =>  $new_data_json['currency'], 
			'NICKNAME' => $new_data_json['price_nickname']
			]);
		
		//print_r($priceData); die;
		
		$STRIPE_RESPONSE 		= createOrUpdateProductPrice( $priceData, $stripe_prod_id );
		$STRIPE_RESPONSE_ARR 	= json_decode($STRIPE_RESPONSE,true);  //print_r($STRIPE_RESPONSE_ARR);die;
		$STRIPE_MESSAGES[]		= $STRIPE_RESPONSE_ARR['message'];
		$new_data_json['stripe_id'] =  $STRIPE_RESPONSE_ARR['data']['id'];

		*/
		$DATA['Datos'] = json_encode($new_data_json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
		
		break;

	case 'FACTURAS':

		$new_data_json 			= json_decode($DATA['Datos'], true); 
		//$STRIPE_RESPONSE 		= deleteInvoiceLinesById('in_1RIA4qIsh8FptpEpj47KJYft');
		$STRIPE_RESPONSE 		= createOrUpdateInvoice($DATA['Datos']);
		$STRIPE_RESPONSE_ARR 	= json_decode($STRIPE_RESPONSE,true);  //print_r($STRIPE_RESPONSE_ARR);die;
		$STRIPE_MESSAGES[]		= $STRIPE_RESPONSE_ARR['message'];
		$new_data_json['stripe_invoice_id'] =  $STRIPE_RESPONSE_ARR['data']['id'];
		
		$DATA['Datos'] = json_encode($new_data_json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
		
		break;
		
	case 'PRICES':
		
		$data_in 			= json_decode($DATA['Datos'], true); 
		
		$priceData 		= json_encode([
			'PRICE' => $data_in['price'], 
			'CURRENCY' =>  $data_in['currency'], 
			'NICKNAME' => $data_in['price_nickname']
			]);
		
		//print_r($priceData); die;
		$stripe_prod_id 		= $data_in['EDIT_ID'];
		$STRIPE_RESPONSE 		= createOrUpdateProductPrice( $priceData, $stripe_prod_id );
		$STRIPE_RESPONSE_ARR 	= json_decode($STRIPE_RESPONSE,true);  //print_r($STRIPE_RESPONSE_ARR);die;
		$STRIPE_MESSAGES[]		= $STRIPE_RESPONSE_ARR['message'];
		//$new_data_json['stripe_price_id'] =  $STRIPE_RESPONSE_ARR['data']['id'];

		
		$DATA['Datos'] = json_encode($data_in, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

		
		break;

		
	default:
	
	break;
}

//print_r($DATA);die;


$RESP = FUNCION($DATA);

//print_r($RESP);die;
//$RESP['STRIPE_MESSAGES'] = 		json_encode($STRIPE_MESSAGES, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);


$RESP['STRIPE_MESSAGES'] 	= 		$STRIPE_MESSAGES;
$RESP['stripe'] 			= 		$STRIPE_RESPONSE_ARR;


echo json_encode($RESP, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); 
return;


?>