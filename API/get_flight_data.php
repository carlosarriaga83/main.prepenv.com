<?php
//error_reporting(E_ALL);
//ini_set('display_errors', 1);

include_once($_SERVER['DOCUMENT_ROOT'] . '/PHP/MYF1.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/PHP/config.php'); // For DB_CREDENTIALS

header('Content-Type: application/json; charset=utf-8');
setlocale(LC_ALL, 'en_US.UTF-8');

$RESP = ['status' => 'error', 'message' => 'Invalid request.'];

if (isset($_GET['id']) && !empty($_GET['id']) && isset($_GET['db_id']) && is_numeric($_GET['db_id'])) {
    $flightId = $_GET['id'];
    $dbIndex = (int)$_GET['db_id'];
    $tableName = 'GFLIGHTS'; // Table name for flight configurations

    // It's good practice to ensure the ID is safe, though SQL_2_OBJ_V2 handles query construction.
    // For more robust security, SQL_2_OBJ_V2 should ideally use prepared statements.
    $db_creds = $DB_CREDENTIALS[$dbIndex];
    $temp_conn = mysqli_connect($db_creds['HOST'], $db_creds['USER'], $db_creds['PWD'], $db_creds['DB']);
    if (!$temp_conn) {
        $RESP['message'] = 'Database connection error for ID validation.';
    } else {
        mysqli_set_charset($temp_conn, 'utf8mb4');
        $escapedFlightId = mysqli_real_escape_string($temp_conn, $flightId);
        mysqli_close($temp_conn);

        $query = sprintf("SELECT * FROM %s WHERE id = '%s'", $tableName, $escapedFlightId);
        $result = SQL_2_OBJ_V2($query, $dbIndex);

        if ($result && isset($result['QRY']['OK']) && $result['QRY']['OK'] && isset($result['PL'][0])) {
            $RESP['status'] = 'success';
            $RESP['message'] = 'Flight data retrieved successfully.';
            $RESP['data'] = $result['PL'][0]; // SQL_2_OBJ_V2 returns the decoded 'Datos' JSON merged with ID and TS
        } elseif ($result && isset($result['QRY']['OK']) && $result['QRY']['OK'] && empty($result['PL'])) {
            $RESP['status'] = 'error';
            $RESP['message'] = 'Flight not found.';
        } else {
            $RESP['message'] = 'Failed to retrieve flight data.';
            $RESP['error_detail'] = isset($result['QRY']['ERR']) ? $result['QRY']['ERR'] : 'Unknown SQL error.';
            error_log("SQL Error in get_flight_data.php: " . $RESP['error_detail'] . " | Query: " . $query);
        }
    }
} else {
    $RESP['message'] = 'Flight ID and Database ID are required.';
}

echo json_encode($RESP, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
exit;
?>