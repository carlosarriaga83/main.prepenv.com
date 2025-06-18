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

    if (json_last_error() === JSON_ERROR_NONE && isset($DATA['TABLA'], $DATA['DB_ID'], $DATA['ID'])) {
        $TABLE_NAME = $DATA['TABLA'];
        $DATABASE_INDEX = (int)$DATA['DB_ID'];
        $ROW_ID = $DATA['ID'];

        if (empty($TABLE_NAME) || !is_numeric($DATABASE_INDEX) || empty($ROW_ID)) {
            $RESP['message'] = 'Missing or invalid parameters (Table, DB_ID, or Row ID).';
            echo json_encode($RESP, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            exit;
        }

        // For GFLIGHTS, table name is fixed, but good to have it for potential future use
        if ($TABLE_NAME !== 'GFLIGHTS') {
            $RESP['message'] = 'Invalid table specified for this operation.';
            echo json_encode($RESP, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            exit;
        }

        // Escape ROW_ID for safe SQL query construction.
        $db_creds = $DB_CREDENTIALS[$DATABASE_INDEX];
        $temp_conn = mysqli_connect($db_creds['HOST'], $db_creds['USER'], $db_creds['PWD'], $db_creds['DB']);

        if (!$temp_conn) {
            $RESP['message'] = 'Database connection error for escaping.';
            error_log("Failed to connect to DB for escaping in delete_flight_data: " . mysqli_connect_error());
        } else {
            mysqli_set_charset($temp_conn, 'utf8mb4');
            $escapedRowId = mysqli_real_escape_string($temp_conn, $ROW_ID);
            mysqli_close($temp_conn);

            $deleteQuery = sprintf("DELETE FROM %s WHERE id = '%s'", $TABLE_NAME, $escapedRowId);
            $R1 = SQL_2_OBJ_V2($deleteQuery, $DATABASE_INDEX);

            if ($R1 && isset($R1['QRY']['OK']) && $R1['QRY']['OK']) {
                // Optionally, you can check $R1['QRY']['AFFECTED_ROWS'] if SQL_2_OBJ_V2 populates it
                $RESP['status'] = 'success';
                $RESP['message'] = 'Flight configuration (ID: ' . htmlspecialchars($ROW_ID) . ') deleted successfully.';
            } else {
                $RESP['message'] = 'Failed to delete flight configuration.';
                $RESP['error_detail'] = isset($R1['QRY']['ERR']) ? $R1['QRY']['ERR'] : 'Unknown SQL error.';
                error_log("SQL Error in delete_flight_data: " . $RESP['error_detail'] . " | Query: " . $deleteQuery);
            }
        }
    } else {
        $RESP['message'] = (json_last_error() !== JSON_ERROR_NONE) ? 'Invalid JSON payload: ' . json_last_error_msg() : 'Missing required parameters in JSON payload.';
        error_log("Invalid request to delete_flight_data: " . $entityBody . " | JSON Error: " . json_last_error_msg());
    }
} else {
    $RESP['message'] = 'Invalid request method. Only POST is accepted.';
}

echo json_encode($RESP, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
exit;
?>