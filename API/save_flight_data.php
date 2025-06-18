<?php
//error_reporting(E_ALL);
//ini_set('display_errors', 1);

include_once($_SERVER['DOCUMENT_ROOT'] . '/PHP/MYF1.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/PHP/config.php'); // For DB_CREDENTIALS

header('Content-Type: application/json; charset=utf-8');
setlocale(LC_ALL, 'en_US.UTF-8');

$RESP = ['status' => 'error', 'message' => 'Invalid request.'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $entityBody = file_get_contents('php://input');
    $DATA = json_decode($entityBody, true);

    if (json_last_error() === JSON_ERROR_NONE && isset($DATA['TABLA'], $DATA['DB_ID'], $DATA['ID'], $DATA['Datos'])) {
        $TABLE_NAME = $DATA['TABLA'];
        $DATABASE_INDEX = (int)$DATA['DB_ID'];
        $ROW_ID = $DATA['ID']; // This is the PK of the row to update (from EDIT_ID)
        $FORM_DATA_ARRAY = $DATA['Datos'];

        if (empty($TABLE_NAME) || !is_numeric($DATABASE_INDEX) || empty($ROW_ID) || !is_array($FORM_DATA_ARRAY)) {
            $RESP['message'] = 'Missing or invalid parameters.';
            echo json_encode($RESP, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            exit;
        }

        // Prepare data for JSON storage in the 'Datos' column
        // The EDIT_ID from the form is $ROW_ID, so it's not stored within the Datos JSON itself.
        unset($FORM_DATA_ARRAY['EDIT_ID']);

        // Handle 'enabled' checkbox: convert "true"/"false" string from form to boolean
        if (isset($FORM_DATA_ARRAY['enabled'])) {
            if ($FORM_DATA_ARRAY['enabled'] === 'true' || $FORM_DATA_ARRAY['enabled'] === true) {
                $FORM_DATA_ARRAY['enabled'] = true;
            } else {
                $FORM_DATA_ARRAY['enabled'] = false;
            }
        } else {
            // If 'enabled' checkbox was unchecked, it might not be submitted. Default to false.
            $FORM_DATA_ARRAY['enabled'] = false;
        }

        // Filter empty schedule_times_of_day
        if (isset($FORM_DATA_ARRAY['schedule_times_of_day']) && is_array($FORM_DATA_ARRAY['schedule_times_of_day'])) {
            $FORM_DATA_ARRAY['schedule_times_of_day'] = array_filter($FORM_DATA_ARRAY['schedule_times_of_day'], function($time) {
                return !empty($time);
            });
            $FORM_DATA_ARRAY['schedule_times_of_day'] = array_values($FORM_DATA_ARRAY['schedule_times_of_day']); // Re-index
        } else {
            $FORM_DATA_ARRAY['schedule_times_of_day'] = []; // Ensure it's an array if not provided
        }

        ksort($FORM_DATA_ARRAY); // Optional: sort keys for consistent JSON structure
        $jsonToSave = json_encode($FORM_DATA_ARRAY, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        // Escape the JSON string and ROW_ID for safe SQL query construction.
        // This is a workaround as SQL_2_OBJ_V2 doesn't use prepared statements.
        $db_creds = $DB_CREDENTIALS[$DATABASE_INDEX];
        $temp_conn = mysqli_connect($db_creds['HOST'], $db_creds['USER'], $db_creds['PWD'], $db_creds['DB']);

        if (!$temp_conn) {
            $RESP['message'] = 'Database connection error for escaping.';
            error_log("Failed to connect to DB for escaping: " . mysqli_connect_error());
        } else {
            mysqli_set_charset($temp_conn, 'utf8mb4');
            $escapedJsonToSave = mysqli_real_escape_string($temp_conn, $jsonToSave);
            $escapedRowId = mysqli_real_escape_string($temp_conn, $ROW_ID);
            mysqli_close($temp_conn);

            $updateQuery = sprintf("UPDATE %s SET Datos = '%s' WHERE id = '%s'",
                $TABLE_NAME,
                $escapedJsonToSave,
                $escapedRowId
            );

            $R1 = SQL_2_OBJ_V2($updateQuery, $DATABASE_INDEX);

            if ($R1 && isset($R1['QRY']['OK']) && $R1['QRY']['OK']) {
                $RESP['status'] = 'success';
                $RESP['message'] = 'Flight data saved successfully.';
                $fetchQuery = sprintf("SELECT * FROM %s WHERE id = '%s'", $TABLE_NAME, $escapedRowId);
                $updatedRecordResult = SQL_2_OBJ_V2($fetchQuery, $DATABASE_INDEX);
                $RESP['data'] = ($updatedRecordResult && isset($updatedRecordResult['PL'][0])) ? $updatedRecordResult['PL'][0] : null;
            } else {
                $RESP['message'] = 'Failed to save flight data.';
                $RESP['error_detail'] = isset($R1['QRY']['ERR']) ? $R1['QRY']['ERR'] : 'Unknown error.';
                error_log("SQL Error in save_flight_data: " . $RESP['error_detail'] . " | Query: " . $updateQuery);
            }
        }
    } else {
        $RESP['message'] = (json_last_error() !== JSON_ERROR_NONE) ? 'Invalid JSON payload: ' . json_last_error_msg() : 'Missing required parameters.';
        error_log("Invalid request to save_flight_data: " . $entityBody . " | JSON Error: " . json_last_error_msg());
    }
} else {
    $RESP['message'] = 'Invalid request method. Only POST is accepted.';
}

echo json_encode($RESP, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
exit;
?>