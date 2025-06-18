<?php
session_start();
require_once 'sql_config.php';

header('Content-Type: application/json');
$response = ['success' => false, 'progress' => []];

if (!isset($_SESSION['user_id'])) {
    $response['message'] = 'Usuario no autenticado.';
    echo json_encode($response);
    exit;
}

$user_id = $_SESSION['user_id'];

$stmt = $db_connection->prepare("SELECT video_internal_id, status, last_watched_seconds FROM user_video_progress WHERE user_id = ?");
if ($stmt) {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $response['progress'][$row['video_internal_id']] = $row;
    }
    $response['success'] = true;
    $stmt->close();
} else {
    $response['message'] = 'Error al preparar la consulta de progreso: ' . $db_connection->error;
}

echo json_encode($response);
mysqli_close($db_connection);
?>